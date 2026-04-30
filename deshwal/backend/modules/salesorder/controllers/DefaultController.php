<?php

namespace backend\modules\salesorder\controllers;

use app\models\SalesorderItemsDetail;
use common\controllers\ModuleController;
use Yii;
use yii\db\Query;
use yii\web\Response;
use yii\db\Transaction;
use yii\helpers\Json;
/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'single';
    public $ModuleName = 'salesorder';
    public $FieldId = 'salesorder_id';
    public $TableName = 'sales_order';
    public $TabLabel = 'Sales Order';
    public $TabId = '14';

    //  public function beforeAction($action)
    // {
    //     $this->layout = '@app/views/layouts/main';  // Set the main layout before each action
    //     return parent::beforeAction($action);
    // }

    public function actionGetpaymentterm()
    {
        $vendorId = Yii::$app->request->post('vendor_id');

        $paymentTerm = '';
        if ($vendorId) {
            $connection = Yii::$app->db;


            $sql = "SELECT payment_terms FROM vendor_account WHERE vendoraccid = :vendor_id LIMIT 1";
            $command = $connection->createCommand($sql);
            $command->bindValue(':vendor_id', $vendorId);
            $row = $command->queryOne();

            if ($row && isset($row['payment_terms'])) {
                $paymentTerm = $row['payment_terms'];
            }
        }

        return $this->asJson([
            'status' => 'success',
            'data' => [
                'payment_term' => $paymentTerm
            ]
        ]);
    }

    private function getLocationData($table, $idField, $id, $hasStateJoin = true)
    {
        if (!$id) {
            return [
                'address' => '',
                'city' => '',
                'state' => '',
                'pincode' => '',
                'statecode' => '',
                'gstn' => '',
                'pan_number' => '',
            ];
        }
        if ($table === 'warehouse') {
            $select = "$table.address, c.city_name AS city, $table.state AS state, $table.pincode, $table.statecode, $table.gstn, $table.pan_number";
            $joinCity = "LEFT JOIN city c ON $table.city = c.cityid";
            $sql = "SELECT $select FROM $table $joinCity WHERE $table.$idField = :id LIMIT 1";
        } else {
            $select = "$table.address, c.city_name AS city, s.state_value AS state, $table.pincode, $table.state_code AS statecode, $table.gstin_no_uin AS gstn, $table.pan_no AS pan_number";
            $joinCity  = "LEFT JOIN city c ON $table.city = c.cityid";
            $joinState = "LEFT JOIN state s ON $table.state = s.state_id";
            $sql = "SELECT $select FROM $table $joinCity $joinState WHERE $table.$idField = :id LIMIT 1";
        }
        $row = Yii::$app->db->createCommand($sql)->bindValue(':id', $id)->queryOne();
        if ($row) {
            return $row;
        }
        return [
            'address' => '',
            'city' => '',
            'state' => '',
            'pincode' => '',
            'statecode' => '',
            'gstn' => '',
            'pan_number' => '',
        ];
    }


   public function actionGetbillvendorlocation()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $locationId = Yii::$app->request->post('location_id');
        $data = $this->getLocationData('vendor_locations', 'vendorloc_id', $locationId);
        return [
            'status' => 'success',
            'data' => $data
        ];
    }

    public function actionGetshipvendorlocation()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $locationId = Yii::$app->request->post('location_id');
        $data = $this->getLocationData('vendor_locations', 'vendorloc_id', $locationId);
        return [
            'status' => 'success',
            'data' => $data
        ];
    }

    public function actionGetbillwhlocation()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $whId = Yii::$app->request->post('warehouse_id');
        $data = $this->getLocationData('warehouse', 'warehouse_id', $whId, false);
        return [
            'status' => 'success',
            'data' => $data
        ];
    }

    public function actionGetshipwhlocation()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $whId = Yii::$app->request->post('warehouse_id');
        $data = $this->getLocationData('warehouse', 'warehouse_id', $whId, false);
        return [
            'status' => 'success',
            'data' => $data
        ];
    }

    public function actionGetproductwithdetails(){
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $productWithDetails = Yii::$app->request->post();
        print_r($productWithDetails); exit;
        $query = new Query();
        $query->select('p.product_name, p.products_id, p.product_no, p.category, p.subcategory')
                ->from(['p' => 'products'])
                ->innerJoin(['spd' => 'salesorderdit_product_details'], 'p.products_id = spd.product_name')
                ->limit(10);

            
        $command = $query->createCommand();
        $result = $command->queryAll();
        return $result;
        
    }

    public function actionBulkproductbytag()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $tagNumber    = trim(Yii::$app->request->post('tagnumber', ''));
        $salesorderId = (int)Yii::$app->request->post('salesorder_id', 0);

        if ($tagNumber === '') {
            return [
                'status'  => 'error',
                'message' => 'Tag Number is required.',
            ];
        }

        $usedInventoryIds = (new \yii\db\Query())
            ->select('inventory_id')
            ->from('salesorder_items_detail')
            ->where(['salesorder_id' => $salesorderId])
            ->column();
        $usedInventoryIds = array_unique(array_filter($usedInventoryIds));

        $subQuery = (new \yii\db\Query())
            ->select([
                'i.inventory_id',
                '(i.qty - IFNULL(SUM(soid.qty), 0)) AS qty',
                'i.lot_no',
                'i.category',
                'i.subcategory',
                'i.tag_number',
                'i.product_name AS product_id',
                'pr.product_name',
                'c.prod_category_value',
                'sc.sub_catagory_value',
                'pr.gst_percentage',
                'pr.hsn_code',
                '(SELECT pcd.quoted_price_gst_exclude
                FROM pickup p
                INNER JOIN sourcingdeal sd ON sd.sourcingdeal_id = p.opportuity_name
                INNER JOIN product_costing pc ON pc.related_to_id = sd.sourcingdeal_id
                INNER JOIN product_costing_detail pcd ON pc.product_costing_id = pcd.product_costing_id
                WHERE p.pickup_no = i.pickup_id
                LIMIT 1
            ) AS quoted_price_gst_exclude',
                '(SELECT pcd.sp_exclusive_gst
                FROM pickup p
                INNER JOIN sourcingdeal sd ON sd.sourcingdeal_id = p.opportuity_name
                INNER JOIN product_costing pc ON pc.related_to_id = sd.sourcingdeal_id
                INNER JOIN product_costing_detail pcd ON pc.product_costing_id = pcd.product_costing_id
                WHERE p.pickup_no = i.pickup_id
                LIMIT 1
            ) AS sp_exclusive_gst',
            ])
            ->from(['i' => 'inventory'])
            ->leftJoin(
                ['soid' => 'salesorder_items_detail'],
                'soid.inventory_id = i.inventory_id
            AND soid.salesorder_id != :excludeSoId
            AND soid.salesorder_id IN (
                SELECT salesorder_id
                FROM sales_order
                WHERE deleted = 0
                AND stage != 3
            )',
                [':excludeSoId' => $salesorderId]
            )
            ->innerJoin(['pr' => 'products'], 'pr.products_id = i.product_name')
            ->innerJoin(['c' => 'prod_category'], 'c.prod_category_id = i.category')
            ->innerJoin(['sc' => 'prod_sub_catagory'], 'sc.sub_catagory_id = i.subcategory')
            ->where(['i.deleted' => 0])
            ->groupBy([
                'i.inventory_id',
                'i.lot_no',
                'i.category',
                'i.subcategory',
                'i.tag_number',
                'i.product_name',
                'pr.product_name',
                'c.prod_category_value',
                'sc.sub_catagory_value',
                'pr.gst_percentage',
                'pr.hsn_code',
            ]);

        if (!empty($usedInventoryIds)) {
            $subQuery->andWhere(['NOT IN', 'i.inventory_id', $usedInventoryIds]);
        }

        $query = (new \yii\db\Query())
            ->from(['sub' => $subQuery])
            ->where(['>', 'sub.qty', 0])
            ->andWhere(['IS NOT', 'sub.quoted_price_gst_exclude', null])
            ->andWhere(['IS NOT', 'sub.sp_exclusive_gst', null])
            ->andWhere(['sub.tag_number' => $tagNumber]);

        $product = $query->one();

        if (!$product) {
            return [
                'status'  => 'error',
                'message' => 'Tag not available, fully used, or already processed in this Sales Order.',
            ];
        }

        return [
            'status' => 'success',
            'data'   => $product,
        ];
    }
    /*** 
     * Download sample CSV file
     */
    public function actionDownloadsample()
    {
        $filePath = \Yii::getAlias('@webroot/thememain/samples/sales_order_bulk_sample.csv');
        $fileName = 'sales_order_bulk_sample.csv';

        if (!file_exists($filePath)) {
            throw new \yii\web\NotFoundHttpException("Sample file not found.");
        }

        return \Yii::$app->response->sendFile($filePath, $fileName, [
            'mimeType' => 'text/csv',
            'inline' => false,
        ]);
    }   

    public function actionBulkpreviewcsv()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isPost) {
            return ['success' => false, 'message' => 'Invalid request method'];
        }

        $salesorderId = (int)Yii::$app->request->post('salesorder_id', 0);
        $csvText      = Yii::$app->request->post('csvtext', '');

        if ($salesorderId <= 0) {
            return ['success' => false, 'message' => 'Invalid Sales Order id'];
        }
        if (trim($csvText) === '') {
            return ['success' => false, 'message' => 'CSV data is empty'];
        }

        $lines = preg_split('/\r\n|\r|\n/', trim($csvText));
        if (count($lines) < 2) {
            return ['success' => false, 'message' => 'CSV has no data rows'];
        }

        $rawHeaders = str_getcsv(array_shift($lines));
        $headers = [];
        foreach ($rawHeaders as $h) {
            $h = trim(mb_strtolower($h, 'UTF-8'));
            if ($h === 'tag number') {
                $headers[] = 'tagnumber';
            } elseif ($h === 'qty') {
                $headers[] = 'qty';
            } elseif ($h === 'selling price') {
                $headers[] = 'selling_price';
            } else {
                $headers[] = $h;
            }
        }

        $previewRows = [];
        $errors = [];
        $rowIndex = 0;
        $usedTagsInFile = [];

        foreach ($lines as $line) {
            $rowIndex++;
            $cols = str_getcsv($line);
            if (!array_filter($cols, static fn($v) => trim($v) !== '')) {
                continue;
            }

            $row = [];
            foreach ($headers as $idx => $key) {
                $row[$key] = isset($cols[$idx]) ? trim($cols[$idx]) : '';
            }

            $tagStr = $row['tagnumber'] ?? '';
            $qtyStr = $row['qty'] ?? '';
            $spStr  = $row['selling_price'] ?? '';

            $tag = trim($tagStr);
            $qty = (float)$qtyStr;
            $sp  = ($spStr === '') ? null : (float)$spStr;

            if ($tag === '') {
                $errors[] = "Row {$rowIndex}: Tag Number is required.";
                continue;
            }
            if (isset($usedTagsInFile[mb_strtolower($tag, 'UTF-8')])) {
                $errors[] = "Row {$rowIndex}: Duplicate Tag Number {$tag} in CSV.";
                continue;
            }
            $usedTagsInFile[mb_strtolower($tag, 'UTF-8')] = true;

            if ($qty <= 0 || !is_numeric($qtyStr)) {
                $errors[] = "Row {$rowIndex}: Qty must be a positive number.";
                continue;
            }
            if ($spStr !== '' && !is_numeric($spStr)) {
                $errors[] = "Row {$rowIndex}: Selling Price must be numeric.";
                continue;
            }

            $res = $this->fetchProductByTagForBulk($tag, $salesorderId);
            if (!$res['success']) {
                $errors[] = "Row {$rowIndex}: " . $res['message'];
                continue;
            }
            $data = $res['data'];

            $qtyInStock = (float)($data['qty'] ?? 0);
            if ($qty > $qtyInStock) {
                $errors[] = "Row {$rowIndex}: Qty ({$qty}) cannot be more than Qty in stock ({$qtyInStock}) for Tag {$tag}.";
                continue;
            }

            $spExcl = (float)($data['sp_exclusive_gst'] ?? 0);
            if ($sp !== null && $sp < $spExcl) {
                $errors[] = "Row {$rowIndex}: Selling Price ({$sp}) cannot be less than Suggested SP (GST Exclude) ({$spExcl}) for Tag {$tag}.";
                continue;
            }

            $previewRows[] = [
                'row_number'          => $rowIndex,
                'tag_number'          => $tag,
                'qty'                 => $qty,
                'selling_price'       => $sp,
                'qty_in_stock'        => $qtyInStock,
                'product_id'          => $data['product_id'] ?? '',
                'product_name'        => $data['product_name'] ?? '',
                'prod_category_value' => $data['prod_category_value'] ?? '',
                'sub_catagory_value'  => $data['sub_catagory_value'] ?? '',
                'gst_percentage'      => $data['gst_percentage'] ?? '',
                'hsn_code'            => $data['hsn_code'] ?? '',
                'quoted_price_gst_exclude' => $data['quoted_price_gst_exclude'] ?? '',
                'sp_exclusive_gst'    => $spExcl,
                'inventory_id'        => $data['inventory_id'] ?? '',
            ];
        }

        if (!empty($errors)) {
            return [
                'success' => false,
                'message' => 'Validation errors found in CSV.',
                'errors'  => $errors,
            ];
        }

        return [
            'success' => true,
            'rows'    => $previewRows,
        ];
    }

    protected function fetchProductByTagForBulk($tagNumber, $salesorderId)
    {
        if ($tagNumber === '') {
            return ['success' => false, 'message' => 'Tag Number is required.'];
        }

        $usedInventoryIds = (new Query())
            ->select('inventory_id')
            ->from('salesorder_items_detail')
            ->where(['salesorder_id' => $salesorderId])
            ->column();

        $usedInventoryIds = array_unique(array_filter($usedInventoryIds));

        $subQuery = (new Query())
            ->select([
                'i.inventory_id',
                '(i.qty - IFNULL(SUM(soid.qty), 0)) AS qty',
                'i.lot_no',
                'i.category',
                'i.subcategory',
                'i.tag_number',
                'i.product_name AS product_id',
                'pr.product_name',
                'c.prod_category_value',
                'sc.sub_catagory_value',
                'pr.gst_percentage',
                'pr.hsn_code',
                '(SELECT pcd.quoted_price_gst_exclude
                FROM pickup p
                INNER JOIN sourcingdeal sd ON sd.sourcingdeal_id = p.opportuity_name
                INNER JOIN product_costing pc ON pc.related_to_id = sd.sourcingdeal_id
                INNER JOIN product_costing_detail pcd ON pc.product_costing_id = pcd.product_costing_id
                WHERE p.pickup_no = i.pickup_id
                LIMIT 1
                ) AS quoted_price_gst_exclude',
                '(SELECT pcd.sp_exclusive_gst
                FROM pickup p
                INNER JOIN sourcingdeal sd ON sd.sourcingdeal_id = p.opportuity_name
                INNER JOIN product_costing pc ON pc.related_to_id = sd.sourcingdeal_id
                INNER JOIN product_costing_detail pcd ON pc.product_costing_id = pcd.product_costing_id
                WHERE p.pickup_no = i.pickup_id
                LIMIT 1
                ) AS sp_exclusive_gst',
            ])
            ->from(['i' => 'inventory'])
            ->leftJoin(
                ['soid' => 'salesorder_items_detail'],
                'soid.inventory_id = i.inventory_id
                AND soid.salesorder_id != :excludeSoId
                AND soid.salesorder_id IN (
                    SELECT salesorder_id
                    FROM sales_order
                    WHERE deleted = 0
                    AND stage != 3
                )',
                [':excludeSoId' => $salesorderId]
            )
            ->innerJoin(['pr' => 'products'], 'pr.products_id = i.product_name')
            ->innerJoin(['c' => 'prod_category'], 'c.prod_category_id = i.category')
            ->innerJoin(['sc' => 'prod_sub_catagory'], 'sc.sub_catagory_id = i.subcategory')
            ->where(['i.deleted' => 0])
            ->groupBy([
                'i.inventory_id',
                'i.lot_no',
                'i.category',
                'i.subcategory',
                'i.tag_number',
                'i.product_name',
                'pr.product_name',
                'c.prod_category_value',
                'sc.sub_catagory_value',
                'pr.gst_percentage',
                'pr.hsn_code',
            ]);

        if (!empty($usedInventoryIds)) {
            $subQuery->andWhere(['NOT IN', 'i.inventory_id', $usedInventoryIds]);
        }

        $query = (new Query())
            ->from(['sub' => $subQuery])
            ->where(['>', 'sub.qty', 0])
            ->andWhere(['IS NOT', 'sub.quoted_price_gst_exclude', null])
            ->andWhere(['IS NOT', 'sub.sp_exclusive_gst', null])
            ->andWhere(['sub.tag_number' => $tagNumber]);

        $product = $query->one();

        if (!$product) {
            return [
                'success' => false,
                'message' => 'Tag not available, fully used, or already processed in this Sales Order.',
            ];
        }

        return [
            'success' => true,
            'data'    => $product,
        ];
    }

    public function actionBulksavecsv_so()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isPost) {
            return ['success' => false, 'message' => 'Invalid request method'];
        }

        $salesorderId = (int)Yii::$app->request->post('salesorder_id', 0);
        $rowsJson     = Yii::$app->request->post('rows');
        $replaceAll   = (int)Yii::$app->request->post('replace_all', 0);
        if ($salesorderId <= 0) {
            return ['success' => false, 'message' => 'Invalid Sales Order id'];
        }
        if (empty($rowsJson)) {
            return ['success' => false, 'message' => 'No rows received'];
        }

        $rows = Json::decode($rowsJson, true);
        if (!is_array($rows) || empty($rows)) {
            return ['success' => false, 'message' => 'Invalid rows format'];
        }

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction(Transaction::SERIALIZABLE);
        $errors = [];
        $successCount = 0;

        try {
            $oldItems = (new \yii\db\Query())
            ->from('salesorder_items_detail')
            ->where(['salesorder_id' => $salesorderId])
            ->orderBy(['salesorderitemdetail_id' => SORT_ASC])
            ->all();
        $oldItemsJson = Json::encode($oldItems);
             if ($replaceAll === 1) {
                $db->createCommand()
                    ->delete('salesorder_items_detail', ['salesorder_id' => $salesorderId])
                    ->execute();
            }
            foreach ($rows as $idx => $row) {
                $rowNo = (int)($row['row_number'] ?? ($idx + 1));
                try {
                    $inventoryId = $row['inventory_id'] ?? null;
                    $qty = (float)($row['qty'] ?? 0);
                    if (!$inventoryId || $qty <= 0) {
                        $errors[] = "Row {$rowNo}: Missing inventory id or qty.";
                        continue;
                    }

                    $check = $this->fetchProductByTagForBulk($row['tag_number'], $salesorderId);
                    if (!$check['success']) {
                        $errors[] = "Row {$rowNo}: " . $check['message'];
                        continue;
                    }
                    $data = $check['data'];
                    $qtyInStock = (float)($data['qty'] ?? 0);
                    if ($qty > $qtyInStock) {
                        $errors[] = "Row {$rowNo}: Qty ({$qty}) now exceeds Qty in stock ({$qtyInStock}).";
                        continue;
                    }
                    
                    $spExcl   = (float)($data['sp_exclusive_gst'] ?? 0);
                    $lineBase = $spExcl * $qty; 
                    $gstPct   = (float)($data['gst_percentage'] ?? 0);

                    $igstPct = $gstPct;
                    $cgstPct = 0;
                    $sgstPct = 0;

                    $igstAmount = $lineBase * $igstPct / 100;
                    $cgstAmount = 0;
                    $sgstAmount = 0;

                    $totalAmount = $lineBase + $igstAmount + $cgstAmount + $sgstAmount;

                    $command = $db->createCommand()->insert('salesorder_items_detail', [
                        'salesorder_id'             => $salesorderId,
                        'inventory_id'              => $inventoryId,
                        'product_name'              => $data['product_id'] ?? null,

                        'category'                  => $data['category'] ?? null,
                        'sub_category'              => $data['sub_catagory_value'] ?? null, 
                        'tag_number'                => $row['tag_number'] ?? null,
                        'qty_in_stock'              => $qtyInStock,
                        'qty'                       => $qty,
                        'purchase_price'            => $data['quoted_price_gst_exclude'] ?? null,
                        'selling_price'             => $row['selling_price'] ?? null,
                        'selling_price_gst_exclude' => $data['sp_exclusive_gst'] ?? null,
                        'base_price_gst_exclude'    => $lineBase,
                        'cgst_percentage'           => $cgstPct,
                        'sgst_percentage'           => $sgstPct,
                        'igst_percentage'           => $igstPct,
                        'cgst_amount'               => $cgstAmount,
                        'sgst_amount'               => $sgstAmount,
                        'igst_amount'               => $igstAmount,
                        'total_amount'              => $totalAmount,
                        'gst_percentage'            => $gstPct,
                        'hsn_code'                  => $data['hsn_code'] ?? null,
                    ]);

                    $command->execute();
                    $successCount++;
                } catch (\Throwable $eRow) {
                    $errors[] = "Row {$rowNo}: " . $eRow->getMessage();
                }
            }

            if (!empty($errors)) {
                $transaction->rollBack();
                return [
                    'success'        => false,
                    'message'        => "Saved {$successCount}/" . count($rows) . " rows. Errors present.",
                    'errors'         => $errors,
                    'total_saved'    => $successCount,
                    'total_records'  => count($rows),
                ];
            }
            $newItems = (new \yii\db\Query())
                ->from('salesorder_items_detail')
                ->where(['salesorder_id' => $salesorderId])
                ->orderBy(['salesorderitemdetail_id' => SORT_ASC])
                ->all();
            $newItemsJson = Json::encode($newItems);

            $userId = Yii::$app->user->id ?? null;

            $db->createCommand()->insert('salesorder_items_history', [
                'salesorder_id'  => $salesorderId,
                'changed_by'     => $userId,
                'changed_at'     => date('Y-m-d H:i:s'),
                'action_type'    => 'BULK_IMPORT',
                'replace_all'    => $replaceAll,
                'old_items_json' => $oldItemsJson,
                'new_items_json' => $newItemsJson,
                'remarks'        => 'Bulk CSV import from detail page',
            ])->execute();
            $transaction->commit();
            return [
                'success'       => true,
                'message'       => "{$successCount} records saved successfully to salesorder_items_detail",
                'total_saved'   => $successCount,
                'total_records' => count($rows),
            ];
        } catch (\Throwable $e) {
            $transaction->rollBack();
            return [
                'success' => false,
                'message' => 'Database transaction failed: ' . $e->getMessage(),
            ];
        }
    }

    public function actionItemslist($salesorder_id = '', $page = 1)
    {
        $page = (int)$page;
        $pageSize = 50;

        $serial     = trim(Yii::$app->request->get('serial', ''));    
        $product    = trim(Yii::$app->request->get('product', ''));     
        $dateFrom   = trim(Yii::$app->request->get('date_from', ''));  
        $dateTo     = trim(Yii::$app->request->get('date_to', ''));
        $salesorder_id = Yii::$app->request->get('salesorder_id', $salesorder_id);

        if (!$salesorder_id) {
            return false;
        }

        $so = (new \yii\db\Query())
            ->from('sales_order')
            // ->select(['salesorder_no'])
            ->where(['salesorder_id' => $salesorder_id])
            ->one();
        $sales_order_no = $so['salesorder_no'] ?? '';
        $owner = $so['ownerid'] ?? '';
        $userId = Yii::$app->user->id ?? null;
        $isAdmin = Yii::$app->user->identity->is_super_admin ?? 0;
        $isSuperAdmin = Yii::$app->user->identity->is_admin ?? 0;
        $hasAccess = 0;
        if($isSuperAdmin || $isAdmin || $owner == $userId ){
        $hasAccess = 1;
        }

        $query = (new \yii\db\Query())
            ->from('salesorder_items_detail sid')
            ->select([
                'sid.salesorderitemdetail_id',
                'sid.salesorder_id',
                'sid.product_name',
                'sid.category',
                'sid.sub_category',
                'sid.tag_number',
                'sid.qty_in_stock',
                'sid.qty',
                'sid.purchase_price',
                'sid.selling_price',
                'sid.selling_price_gst_exclude',
                'sid.base_price_gst_exclude',
                'sid.cgst_percentage',
                'sid.sgst_percentage',
                'sid.igst_percentage',
                'sid.cgst_amount',
                'sid.sgst_amount',
                'sid.igst_amount',
                'sid.total_amount',
                'sid.gst_percentage',
                'sid.hsn_code',
                'sid.inventory_id',
                'p.product_name AS product_name_text',
            ])
            ->leftJoin('products p', 'p.products_id = sid.product_name')
            ->where(['sid.salesorder_id' => $salesorder_id]);

        if ($serial !== '') {
            $query->andWhere(['like', 'sid.tag_number', $serial]);
        }
        if ($product !== '') {
            $query->andWhere(['like', 'p.product_name', $product]);
        }

        $totalRecords = (int)$query->count();

        $rows = $query
            ->orderBy(['sid.salesorderitemdetail_id' => SORT_ASC])
            ->offset(($page - 1) * $pageSize)
            ->limit($pageSize)
            ->all();

        $this->layout = '@app/views/layouts/main-one'; // or whatever single layout you use

        return $this->render('itemsdetail', [
            'salesorder_id'  => $salesorder_id,
            'salesorder_no' => $sales_order_no,
            'rows'           => $rows,
            'totalRecords'   => $totalRecords,
            'page'           => $page,
            'pageSize'       => $pageSize,
            'hasAccess'      => $hasAccess,
            'filters'        => [
                'serial'   => $serial,
                'product'  => $product,
                'date_from'=> $dateFrom,
                'date_to'  => $dateTo,
            ],
        ]);
    }
    public function actionSoitemdetail($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $row = (new \yii\db\Query())
            ->from('salesorder_items_detail sid')
            ->select([
                'sid.salesorderitemdetail_id',
                'sid.salesorder_id',
                'sid.product_name',
                'sid.category',
                'sid.sub_category',
                'sid.tag_number',
                'sid.qty_in_stock',
                'sid.qty',
                'sid.purchase_price',
                'sid.selling_price',
                'sid.selling_price_gst_exclude',
                'sid.base_price_gst_exclude',
                'sid.cgst_percentage',
                'sid.sgst_percentage',
                'sid.igst_percentage',
                'sid.cgst_amount',
                'sid.sgst_amount',
                'sid.igst_amount',
                'sid.total_amount',
                'sid.gst_percentage',
                'sid.hsn_code',
                'sid.inventory_id',
                'p.product_name AS product_name_text',
            ])
            ->leftJoin('products p', 'p.products_id = sid.product_name')
            ->where(['sid.salesorderitemdetail_id' => (int)$id])
            ->one();

        if (!$row) {
            return ['success' => false, 'message' => 'Item not found'];
        }

        return [
            'success' => true,
            'data'    => $row,
        ];
    }

    
    protected function calculateGstSplit($gstPercentage, $billStateCode, $shipStateCode)
    {
        $gstPer = (float)$gstPercentage;

        $cgstPer = 0.0;
        $sgstPer = 0.0;
        $igstPer = 0.0;

        if ($billStateCode !== null && $shipStateCode !== null) {
            if ((string)$billStateCode === (string)$shipStateCode) {
                $igstPer = $gstPer;
                $cgstPer = 0.0;
                $sgstPer = 0.0;
            } else {
                $cgstPer = $gstPer / 2.0;
                $sgstPer = $gstPer / 2.0;
                $igstPer = 0.0;
            }
        }

        return [$cgstPer, $sgstPer, $igstPer];
    }

    public function actionSoitemupdate()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $id = (int)Yii::$app->request->post('id', 0);
        if ($id <= 0) {
            return ['success' => false, 'message' => 'Invalid item id'];
        }

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();

        try {
            // 1) Load existing row (OLD snapshot)
            $row = (new \yii\db\Query())
                ->from('salesorder_items_detail sid')
                ->select(['sid.*'])
                ->where(['sid.salesorderitemdetail_id' => $id])
                ->one();

            if (!$row) {
                $transaction->rollBack();
                return ['success' => false, 'message' => 'Item not found'];
            }

            $salesorderId = (int)$row['salesorder_id'];
            $oldItemsJson = \yii\helpers\Json::encode([$row]); 

            $qty       = (float)Yii::$app->request->post('qty', $row['qty']);
            $spExcl    = (float)Yii::$app->request->post('selling_price_gst_exclude', $row['selling_price_gst_exclude']);
            $sellingPrice = (float)Yii::$app->request->post('selling_price', $row['selling_price']);
            $gstPer    = (float)Yii::$app->request->post('gst_percentage', $row['gst_percentage']);

            $category    = Yii::$app->request->post('category', $row['category']);
            $subCategory = Yii::$app->request->post('sub_category', $row['sub_category']);
            $hsnCode     = Yii::$app->request->post('hsn_code', $row['hsn_code']);

            $so = (new \yii\db\Query())
                ->from('sales_order')
                ->select(['bill_statecode', 'ship_statecode'])
                ->where(['salesorder_id' => $salesorderId])
                ->one();

            $billState = $so['bill_statecode'] ?? null;
            $shipState = $so['ship_statecode'] ?? null;

            list($cgstPer, $sgstPer, $igstPer) = $this->calculateGstSplit($gstPer, $billState, $shipState);

            $basePrice = $spExcl * $qty;

            $cgstAmt = $basePrice * $cgstPer / 100.0;
            $sgstAmt = $basePrice * $sgstPer / 100.0;
            $igstAmt = $basePrice * $igstPer / 100.0;
            $total   = $basePrice + $cgstAmt + $sgstAmt + $igstAmt;

            $db->createCommand()->update('salesorder_items_detail', [
                'qty'                       => $qty,
                'qty_in_stock'              => $row['qty_in_stock'], 
                'category'                  => $category,
                'sub_category'              => $subCategory,
                'hsn_code'                  => $hsnCode,
                'gst_percentage'            => $gstPer,
                'selling_price_gst_exclude' => $spExcl,
                'selling_price'             => $sellingPrice,
                'base_price_gst_exclude'    => $basePrice,
                'cgst_percentage'           => $cgstPer,
                'sgst_percentage'           => $sgstPer,
                'igst_percentage'           => $igstPer,
                'cgst_amount'               => $cgstAmt,
                'sgst_amount'               => $sgstAmt,
                'igst_amount'               => $igstAmt,
                'total_amount'              => $total,
            ], [
                'salesorderitemdetail_id' => $id,
            ])->execute();

            $newRow = (new \yii\db\Query())
                ->from('salesorder_items_detail sid')
                ->select(['sid.*'])
                ->where(['sid.salesorderitemdetail_id' => $id])
                ->one();
            $newItemsJson = \yii\helpers\Json::encode([$newRow]);

            $userId = Yii::$app->user->id ?? null;

            $db->createCommand()->insert('salesorder_items_history', [
                'salesorder_id'  => $salesorderId,
                'itemdetail_id'  => $id,                
                'changed_by'     => $userId,
                'changed_at'     => date('Y-m-d H:i:s'),
                'action_type'    => 'ROW_EDIT',
                'source_action'  => 'SOITEMUPDATE',
                'replace_all'    => 0,
                'old_items_json' => $oldItemsJson,
                'new_items_json' => $newItemsJson,
                'remarks'        => 'Single row edit from items detail page',
            ])->execute();

            $transaction->commit();

            return [
                'success' => true,
                'message' => 'Item updated successfully',
                'data'    => [
                    'qty'                       => $qty,
                    'selling_price_gst_exclude' => $spExcl,
                    'selling_price'             => $sellingPrice,
                    'gst_percentage'            => $gstPer,
                    'base_price_gst_exclude'    => $basePrice,
                    'cgst_percentage'           => $cgstPer,
                    'sgst_percentage'           => $sgstPer,
                    'igst_percentage'           => $igstPer,
                    'cgst_amount'               => $cgstAmt,
                    'sgst_amount'               => $sgstAmt,
                    'igst_amount'               => $igstAmt,
                    'total_amount'              => $total,
                ],
            ];
        } catch (\Throwable $e) {
            $transaction->rollBack();
            return [
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage(),
            ];
        }
    }


      public function actionGetcount()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $record_id = Yii::$app->request->post('id');
        $count = SalesorderItemsDetail::find()
            ->where(['salesorder_id' => $record_id])
            ->count();

        return [
            'success' => true,
            'count'   => (int)$count,
        ];
    }

    public function actionSoitemhistory($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $id = (int)$id;

        if ($id <= 0) {
            return ['success' => false, 'message' => 'Invalid item id'];
        }

        $item = (new \yii\db\Query())
            ->from('salesorder_items_detail')
            ->select(['salesorder_id'])
            ->where(['salesorderitemdetail_id' => $id])
            ->one();

        if (!$item) {
            return ['success' => false, 'message' => 'Item not found'];
        }

        $salesorderId = (int)$item['salesorder_id'];

        $historyRows = (new \yii\db\Query())
            ->from('salesorder_items_history')
            ->where([
                'salesorder_id' => $salesorderId,
            ])
            ->andWhere([
                'or',
                ['itemdetail_id' => $id],             
                ['itemdetail_id' => null],            
            ])
            ->orderBy(['changed_at' => SORT_DESC, 'history_id' => SORT_DESC])
            ->all();

        $result = [];
        foreach ($historyRows as $h) {
            $oldItems = [];
            $newItems = [];

            if (!empty($h['old_items_json'])) {
                $decoded = \yii\helpers\Json::decode($h['old_items_json'], true);
                if (is_array($decoded)) {
                    foreach ($decoded as $r) {
                        if ((int)($r['salesorderitemdetail_id'] ?? 0) === $id) {
                            $oldItems[] = $r;
                        }
                    }
                }
            }

            if (!empty($h['new_items_json'])) {
                $decoded = \yii\helpers\Json::decode($h['new_items_json'], true);
                if (is_array($decoded)) {
                    foreach ($decoded as $r) {
                        if ((int)($r['salesorderitemdetail_id'] ?? 0) === $id) {
                            $newItems[] = $r;
                        }
                    }
                }
            }

            if (empty($oldItems) && empty($newItems)) {
                continue;
            }

            $result[] = [
                'history_id'    => (int)$h['history_id'],
                'changed_at'    => $h['changed_at'],
                'changed_by'    => $h['changed_by'],
                'action_type'   => $h['action_type'],
                'source_action' => $h['source_action'] ?? null,
                'replace_all'   => (int)$h['replace_all'],
                'remarks'       => $h['remarks'],
                'old_items'     => $oldItems,
                'new_items'     => $newItems,
            ];
        }

        if (!$result) {
            return ['success' => true, 'history' => []];
        }

        return ['success' => true, 'history' => $result];
    }
    public function actionBulkdeleteitems_so()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!Yii::$app->request->isPost) {
            return ['success' => false, 'message' => 'Invalid request method'];
        }

        $salesorderId = (int)Yii::$app->request->post('salesorder_id', 0);
        if ($salesorderId <= 0) {
            return ['success' => false, 'message' => 'Invalid Sales Order id'];
        }

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();

        try {
            // snapshot OLD items
            $oldItems = (new \yii\db\Query())
                ->from('salesorder_items_detail')
                ->where(['salesorder_id' => $salesorderId])
                ->orderBy(['salesorderitemdetail_id' => SORT_ASC])
                ->all();

            if (!$oldItems) {
                $transaction->rollBack();
                return ['success' => false, 'message' => 'No items to delete for this Sales Order.'];
            }

            $oldItemsJson = \yii\helpers\Json::encode($oldItems);

            // delete all items
            $db->createCommand()
                ->delete('salesorder_items_detail', ['salesorder_id' => $salesorderId])
                ->execute();

            // snapshot NEW state (empty)
            $newItems = [];
            $newItemsJson = \yii\helpers\Json::encode($newItems);

            // history row
            $userId = Yii::$app->user->id ?? null;
            $db->createCommand()->insert('salesorder_items_history', [
                'salesorder_id'  => $salesorderId,
                'itemdetail_id'  => null,
                'changed_by'     => $userId,
                'changed_at'     => date('Y-m-d H:i:s'),
                'action_type'    => 'BULK_DELETE',
                'source_action'  => 'BULKDELETE_SO',
                'replace_all'    => 1,
                'old_items_json' => $oldItemsJson,
                'new_items_json' => $newItemsJson,
                'remarks'        => 'Delete all existing items from detail page bulk import',
            ])->execute();

            $transaction->commit();

            return [
                'success' => true,
                'message' => 'Existing items deleted successfully.',
            ];
        } catch (\Throwable $e) {
            $transaction->rollBack();
            return [
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage(),
            ];
        }
    }

}
