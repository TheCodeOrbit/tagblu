<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\db\Query;
use yii\db\Expression;
use app\models\StockCalculation;
class SalesOrderStockController extends Controller
{
    /**
     * Cron job - 
     * First run: ALL stage=7 sales orders
     * Daily run: TODAY's stage=7 sales orders only
     */
    public function actionCalcDailyStockAll() 
    {
        $today = date('Y-m-d');
        $startOfDay = $today . ' 00:00:00';
        $endOfDay = $today . ' 23:59:59';

        $isFirstRun = !(new Query())
            ->from('rep_prodstock')
            ->where(['stockdate' => $today])
            ->exists();

        echo "Stock calculation for $today. First run: " . ($isFirstRun ? 'YES' : 'NO') . "\n";

        $salesOrderQuery = (new Query())
            ->select(['salesorder_id'])
            ->from('sales_order')
            ->where(['stage' => 7]);

        if (!$isFirstRun) {
            $salesOrderQuery->andWhere(['between', 'modifiedtime', $startOfDay, $endOfDay]);
        }

        $salesOrders = $salesOrderQuery->column();
        echo "Found " . count($salesOrders) . " stage=7 sales order(s)\n";

        $processed = 0;
        foreach ($salesOrders as $salesorder_id) {
            try {
                $this->save_stock($salesorder_id); 
                $processed++;
                echo "Processed salesorder_id: $salesorder_id\n";
            } catch (\Exception $e) {
                echo "Error processing salesorder_id: $salesorder_id - " . $e->getMessage() . "\n";
            }
        }

        echo "Completed! Processed $processed sales orders for $today\n";
    }

    public function save_stock($RecordId)
    {
        $sqlSo = "SELECT stage FROM sales_order where salesorder_id = :salesorder_id";
        $resultSo = Yii::$app->db->createCommand($sqlSo)->bindValue(":salesorder_id", $RecordId)->queryOne();
        if(isset($resultSo['stage']) && $resultSo['stage'] != 7){
            return;
        }
        
        $sql = "SELECT * FROM salesorder_items_detail where salesorder_id = :salesorder_id GROUP BY product_name";
        $result = Yii::$app->db->createCommand($sql)->bindValue(":salesorder_id", $RecordId)->queryAll();
        
        $location_sql = "SELECT ship_wh_location FROM sales_order where salesorder_id = :salesorder_id";
        $location = Yii::$app->db->createCommand($location_sql)->bindValue(":salesorder_id", $RecordId)->queryOne();
        
        foreach ($result as $result_row) {
            $this->getTodayStockSingleProductdeshwal($result_row['product_name'], $location['ship_wh_location']);
        }
    }

    public function getOpeningStockForTodayDeshwal($productId,$location)
    {
        $today = date('Y-m-d');

        $openingStockToday = (new Query())
            ->select(['quantity'])
            ->from('openingstock_prod')
            ->where([
                'productid' => $productId,
                'stock_date' => $today,
                'location' => $location,
            ])
            ->scalar();

        if ($openingStockToday !== false && $openingStockToday !== null) {
            return [
                'source' => 'openingstock_prod',
                'date' => $today,
                'quantity' => $openingStockToday,
                'location' => $location
            ];
        }

        $previousStock = (new Query())
            ->select(['stock_quantity', 'stockdate'])
            ->from('rep_prodstock')
            ->where(['productid' => $productId])
            ->andWhere(['location' => $location])
            ->andWhere(['<', 'stockdate', $today])
            ->orderBy(['stockdate' => SORT_DESC])
            ->limit(1)
            ->one();

        if ($previousStock) {
            return [
                'source' => 'rep_prodstock (previous)',
                'date' => $previousStock['stockdate'],
                'quantity' => $previousStock['stock_quantity'],
                'location' => $location,
            ];
        }

        return [
            'source' => 'No stock found for current date in both the tables',
            'date' => null,
            'quantity' => 0,
            'location' => $location
        ];
    }

    
    public function getTodayStockSingleProductdeshwal($productId,$location)
    {
        $today = date('Y-m-d');

        $openingData = $this->getOpeningStockForTodayDeshwal($productId,$location);
        $openingQty = $openingData['quantity'] ?? 0;

        $query = (new Query())
            ->select(['SUM(qty) AS sum_qty'])
            ->from('inventory')
            ->where(['product_name' => $productId])
            ->andWhere(['location' => $location])
            ->andWhere(['status' => 1])
            ->andWhere(['between', 'modifiedtime', date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')]);

        $inwardQty = $query->scalar();
        $inwardQty = $inwardQty === null ? 0 : (float) $inwardQty;

        $startOfDay = $today . ' 00:00:00';
        $endOfDay = $today . ' 23:59:59';

        $outwardQty = (new Query())
            ->select([
                new Expression("SUM(sid.qty) AS qty")
            ])
            ->from(['sid' => 'salesorder_items_detail'])
            ->innerJoin(['so' => 'sales_order'], 'so.salesorder_id = sid.salesorder_id')
            ->where(['sid.product_name' => $productId])
            ->andWhere(['between', 'so.modifiedtime', $startOfDay, $endOfDay])
            ->andWhere(['so.ship_wh_location' => $location])
            ->scalar();

        $outwardQty = $outwardQty ?: 0;

        $closingStock = $openingQty + $inwardQty - $outwardQty;

        if($outwardQty < 0) {
            $inwardQty = abs($outwardQty);
        }

        Yii::$app->db->createCommand()
            ->delete('rep_prodstock', ['productid' => $productId,'location'=>$location, 'stockdate' => $today])
            ->execute();

        Yii::$app->db->createCommand()->insert('rep_prodstock', [
            'productid' => $productId,
            'stock_quantity' => $closingStock,
            'total_in' => $inwardQty,
            'total_out' => $outwardQty,
            'location' => $location,
            'stockdate' => $today,
            'created_at' => date('Y-m-d H:i:s')
        ])->execute();
    }
}
