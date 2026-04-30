<?php

namespace app\models;

use Yii;

/**
 * EditModel class.
 * EditModel is the data structure for keeping
 * EditModel form data. It is used by the 'Module' action of 'Controller'.
 */
//session_start(); use Yii;
class StockCalculation extends \yii\db\ActiveRecord
{


    /**
     * Get today's opening stock for a given product.
     * Priority:
     * 1. openingstock_prod_dit for today
     * 2. rep_prodstock_dit for today
     * 3. rep_prodstock_dit before today (latest)
     */
    public function getOpeningStockForToday($productId,$location)
    {
        $today = date('Y-m-d');

        // 1. Check openingstock_prod_dit for today's stock
        $openingStockToday = (new \yii\db\Query())
            ->select(['quantity'])
            ->from('openingstock_prod_dit')
            ->where([
                'productid' => $productId,
                'stock_date' => $today,
                'location' => $location,
            ])
            ->scalar();

        if ($openingStockToday !== false && $openingStockToday !== null) {
            return [
                'source' => 'openingstock_prod_dit',
                'date' => $today,
                'quantity' => $openingStockToday,
                'location' => $location
            ];
        }

        // 2. Check rep_prodstock_dit for today's stock
        $repStockToday = (new \yii\db\Query())
            ->select(['stock_quantity'])
            ->from('rep_prodstock_dit')
            ->where([
                'productid' => $productId,
                'stockdate' => $today,
                'location' => $location,
            ])
            ->scalar();

        if ($repStockToday !== false && $repStockToday !== null) {
            return [
                'source' => 'rep_prodstock_dit (today)',
                'date' => $today,
                'quantity' => $repStockToday,
                'location' => $location
            ];
        }

        // 3. Check rep_prodstock_dit for latest available stock before today
        $previousStock = (new \yii\db\Query())
            ->select(['stock_quantity', 'stockdate'])
            ->from('rep_prodstock_dit')
            ->where(['productid' => $productId])
            ->andWhere(['location' => $location])
            ->andWhere(['<', 'stockdate', $today])
            ->orderBy(['stockdate' => SORT_DESC])
            ->limit(1)
            ->one();

        if ($previousStock) {
            return [
                'source' => 'rep_prodstock_dit (previous)',
                'date' => $previousStock['stockdate'],
                'quantity' => $previousStock['stock_quantity'],
                'location' => $location,
            ];
        }

        // 4. No stock found anywhere
        return [
            'source' => 'No stock found for current date in both the tables',
            'date' => null,
            'quantity' => 0,
            'location' => $location
        ];
    }


    /**
     * Calculate today's stock for one product and insert into rep_prodstock_dit
     */
    public function getTodayStockSingleProduct($productId,$location)
    {
        $today = date('Y-m-d');

        // Step 1: Get opening quantity with logic
        $openingData = $this->getOpeningStockForToday($productId,$location);
        $openingQty = $openingData['quantity'] ?? 0;

        // Step 2: Get inward quantity till today
        // $inwardQty = (new \yii\db\Query())
        //     ->select(['SUM(qty)'])
        //     ->from('inventory_dit')
        //     ->where(['product_name' => $productId])
        //     ->andWhere(['<=', 'createdtime', date('Y-m-d 23:59:59')])
        //     ->scalar();
        // $inwardQty = $inwardQty ?: 0;
        $query = (new \yii\db\Query())
            ->select(['IFNULL(SUM(qty), 0) AS sum_qty'])
            ->from('inventory_dit')
            ->where(['product_name' => $productId])
            ->andWhere(['location' => $location])
            ->andWhere(['between', 'createdtime', date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')]);

            $query = (new \yii\db\Query())
            ->select(['IFNULL(SUM(qty), 0) AS sum_qty'])
            ->from('inventory_dit')
            ->where(['product_name' => $productId])
            ->andWhere(['location' => $location])
            ->andWhere(['between', 'createdtime', date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')]);

        // Output raw SQL
        $rawSql = $query->createCommand()->getRawSql();
        // echo $rawSql;die;

        $sql = $query->createCommand()->getRawSql();  // for debug
        

        $inwardQty = $query->scalar();
        $inwardQty = $inwardQty === null ? 0 : (float) $inwardQty;




        // Step 3: Get outward quantity till today
        $startOfDay = $today . ' 00:00:00';
        $endOfDay = $today . ' 23:59:59';

        // $outwardQty = (new \yii\db\Query())
        //     ->select(['SUM(dpd.product_qty)'])
        //     ->from(['dpd' => 'deliverychallandit_product_details'])
        //     ->innerJoin(['dc' => 'delivery_challandit'], 'dc.deliverychallan_id = dpd.deliverychallan_id')
        //     ->where(['dpd.poduct_description' => $productId])
        //     ->andWhere(['between', 'dc.createdtime', $startOfDay, $endOfDay])
        //     ->scalar();


        $outwardQty = (new \yii\db\Query())
            ->select([
                new \yii\db\Expression("
                    SUM(
                        CASE  
                            WHEN dc.status = 7 THEN dpd.product_qty 
                            ELSE 0 
                        END
                    ) AS qty
                ")
            ])
            ->from(['dpd' => 'deliverychallandit_product_details'])
            ->innerJoin(['dc' => 'delivery_challandit'], 'dc.deliverychallan_id = dpd.deliverychallan_id')
            ->where(['dpd.poduct_description' => $productId])
            ->andWhere(['between', 'dc.createdtime', $startOfDay, $endOfDay])
            ->andWhere(['dc.delivery_challan_location' => $location])
            ->scalar();

            $returninwardQty = (new \yii\db\Query())
            ->select([
                new \yii\db\Expression("
                    SUM(
                        CASE 
                            WHEN dc.status = 8 THEN dpd.product_qty
                            ELSE 0 
                        END
                    ) AS qty
                ")
            ])
            ->from(['dpd' => 'deliverychallandit_product_details'])
            ->innerJoin(['dc' => 'delivery_challandit'], 'dc.deliverychallan_id = dpd.deliverychallan_id')
            ->where(['dpd.poduct_description' => $productId])
            ->andWhere(['between', 'dc.createdtime', $startOfDay, $endOfDay])
            ->andWhere(['dc.delivery_challan_location' => $location])
            ->scalar();

        // echo $outwardQty->createCommand()->getRawSql();die;
        // $outwardQty = $outwardQty ?: 0;
        
        $outwardQty = $outwardQty ?: 0;
        $inwardQty += $returninwardQty;
        // echo $openingQty . "----" . $inwardQty . "--->" . $outwardQty;die;
        // Step 4: Calculate final closing stock
        $closingStock = $openingQty + $inwardQty - $outwardQty;
        // echo "closingStock" . $closingStock;
        // die;
        // below if statment is used when DC is return and want total in entery 
        if($outwardQty < 0)
        {
            $inwardQty = abs($outwardQty);//it convert -5 to 5
        }
        // Step 5: Delete existing stock entry for today (optional: to avoid duplicates)
        \Yii::$app->db->createCommand()
            ->delete('rep_prodstock_dit', ['productid' => $productId,'location'=>$location, 'stockdate' => $today])
            ->execute();

        // Step 6: Insert today's stock into rep_prodstock_dit
        \Yii::$app->db->createCommand()->insert('rep_prodstock_dit', [
            'productid' => $productId,
            'stock_quantity' => $closingStock,
            'total_in' => $inwardQty,
            'total_out' => $outwardQty,
            'location' => $location,
            'stockdate' => $today,
            'created_at' => date('Y-m-d H:i:s')
        ])->execute();

        // return "Stock inserted for product $productId: $closingStock units. Opening source: " . $openingData['source'];
    }
    //calcualte stock for deshwal
    public function getOpeningStockForTodayDeshwal($productId,$location)
    {
        $today = date('Y-m-d');

        // 1. Check openingstock_prod for today's stock
        $openingStockToday = (new \yii\db\Query())
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

        // 2. Check rep_prodstock_dit for today's stock
        // $repStockToday = (new \yii\db\Query())
        //     ->select(['stock_quantity'])
        //     ->from('rep_prodstock')
        //     ->where([
        //         'productid' => $productId,
        //         'stockdate' => $today,
        //         'location' => $location,
        //     ])
        //     ->scalar();

        // if ($repStockToday !== false && $repStockToday !== null) {
        //     return [
        //         'source' => 'rep_prodstock (today)',
        //         'date' => $today,
        //         'quantity' => $repStockToday,
        //         'location' => $location
        //     ];
        // }

        // 3. Check rep_prodstock_dit for latest available stock before today
        $previousStock = (new \yii\db\Query())
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

        // 4. No stock found anywhere
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

        // Step 1: Get opening quantity with logic
        $openingData = $this->getOpeningStockForTodayDeshwal($productId,$location);
        $openingQty = $openingData['quantity'] ?? 0;

        // Step 2: Get inward quantity till today

        $query = (new \yii\db\Query())
            ->select(['SUM(qty) AS sum_qty'])
            ->from('inventory')
            ->where(['product_name' => $productId])
            ->andWhere(['location' => $location])
            ->andWhere(['status' => 1])
            ->andWhere(['between', 'modifiedtime', date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')]);

        
    // Output raw SQL
    $rawSql = $query->createCommand()->getRawSql();
    // echo $rawSql;die;

        $sql = $query->createCommand()->getRawSql();  // for debu
        

        $inwardQty = $query->scalar();
        $inwardQty = $inwardQty === null ? 0 : (float) $inwardQty;




        // Step 3: Get outward quantity till today
        $startOfDay = $today . ' 00:00:00';
        $endOfDay = $today . ' 23:59:59';


        $outwardQty = (new \yii\db\Query())
            ->select([
                new \yii\db\Expression("
                    SUM(sid.qty) AS qty
                ")
            ])
            ->from(['sid' => 'salesorder_items_detail'])
            ->innerJoin(['so' => 'sales_order'], 'so.salesorder_id = sid.salesorder_id')
            ->where(['sid.product_name' => $productId])
            ->andWhere(['between', 'so.modifiedtime', $startOfDay, $endOfDay])
            ->andWhere(['so.ship_wh_location' => $location])
            ->scalar();

            

        // echo $outwardQty->createCommand()->getRawSql();die;
        // $outwardQty = $outwardQty ?: 0;
        
        $outwardQty = $outwardQty ?: 0;
        // echo $openingQty . "----" . $inwardQty . "--->" . $outwardQty;die;
        // Step 4: Calculate final closing stock
        $closingStock = $openingQty + $inwardQty - $outwardQty;
        // echo "closingStock" . $closingStock;
        // die;
        // below if statment is used when DC is return and want total in entery 
        if($outwardQty < 0)
        {
            $inwardQty = abs($outwardQty);//it convert -5 to 5
        }
        // Step 5: Delete existing stock entry for today (optional: to avoid duplicates)
        \Yii::$app->db->createCommand()
            ->delete('rep_prodstock', ['productid' => $productId,'location'=>$location, 'stockdate' => $today])
            ->execute();

        // Step 6: Insert today's stock into rep_prodstock_dit
        \Yii::$app->db->createCommand()->insert('rep_prodstock', [
            'productid' => $productId,
            'stock_quantity' => $closingStock,
            'total_in' => $inwardQty,
            'total_out' => $outwardQty,
            'location' => $location,
            'stockdate' => $today,
            'created_at' => date('Y-m-d H:i:s')
        ])->execute();

        // return "Stock inserted for product $productId: $closingStock units. Opening source: " . $openingData['source'];
    }

    /**
     * insert opening stock in openingstock_prod
     */
    public function addOpeningStock(array $rows, string $stockDate)
    {
        if (empty($rows)) {
            return;
        }

        $db = Yii::$app->db;
        $userId = Yii::$app->user->id ?? 0;
        $now = date('Y-m-d H:i:s');

        foreach ($rows as $row) {
            
            if (
                empty($row['productid']) ||
                empty($row['location']) ||
                !isset($row['quantity'])
            ) {
                continue;
            }

            $db->createCommand("
                INSERT INTO openingstock_prod
                    (productid, location, stock_date, quantity,
                    creatorid, ownerid, modifiedby, createdtime, modifiedtime, deleted)
                VALUES
                    (:productid, :location, :stock_date, :quantity,
                    :creatorid, :ownerid, :modifiedby, :createdtime, :modifiedtime, 0)
                ON DUPLICATE KEY UPDATE
                    quantity = quantity + VALUES(quantity),
                    modifiedby = VALUES(modifiedby),
                    modifiedtime = VALUES(modifiedtime)
            ")
            ->bindValues([
                ':productid'    => $row['productid'],
                ':location'     => $row['location'],
                ':stock_date'   => $stockDate,
                ':quantity'     => $row['quantity'],
                ':creatorid'    => $userId,
                ':ownerid'      => $userId,
                ':modifiedby'   => $userId,
                ':createdtime'  => $now,
                ':modifiedtime' => $now,
            ])
            ->execute();
            echo "added";
        }
    }



}
