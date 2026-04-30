<?php

namespace backend\modules\tagging\controllers;
use yii\web\UploadedFile;

use app\models\Inventory;
use app\models\InventoryLogDetails;
use app\models\ModtrackerBasic;
use common\controllers\ModuleController;
use Yii;
use yii\base\Exception;

/**
 * Default controller for the `grn` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'multiple';
    public $ModuleName = 'tagging';
    public $FieldId = 'tagging_id';
    public $TableName = 'tagging';
    public $TabLabel = 'Tagging';
    public $TabId = '68';

    public function actionExample()
    {
        return $this->render('index');
    }

    public function actionGetdatafrominventory()
    {
        $Recordid = Yii::$app->request->post('Recordid');
        $Productid = Yii::$app->request->post('Productid');
        $Subcategory = Yii::$app->request->post('Subcategory');
        $connection = Yii::$app->db;
        $columns = (new \yii\db\Query())
            ->select([
                'inventory.*',
                'vendor_account.acc_name',
                // 'vendor_locations.vendor_loc_name',
                'warehouse.warehouse_name',
                'prod_sub_catagory.sub_catagory_value',
                'prod_model.prod_model_value',
                'prod_make.prod_make_value',
                'prod_category.prod_category_value',
                'products.product_name',
            ])
            ->from('inventory')
            ->leftJoin('prod_sub_catagory', 'prod_sub_catagory.sub_catagory_id = inventory.subcategory')
            ->leftJoin('prod_model', 'prod_model.prod_model_id = inventory.model')
            ->leftJoin('prod_make', 'prod_make.prod_make_id = inventory.make')
            ->leftJoin('prod_category', 'prod_category.prod_category_id = inventory.category')
            ->innerJoin('vendor_account', 'inventory.account_name = vendor_account.vendoraccid')
            //   ->innerJoin('vendor_locations', 'inventory.location = vendor_locations.vendorloc_id')
            ->leftJoin('warehouse', 'inventory.location = warehouse.warehouse_id')
            ->innerJoin('products', 'products.products_id = inventory.product_name')
            ->where(['inventory.grn_id' => $Recordid])
            ->andWhere(['inventory.product_name' => $Productid])
            ->andWhere(['inventory.subcategory' => $Subcategory])
            ->andWhere(['inventory.status' => 2])
            //add as per v11 -240 new changes
            // ->andWhere(['inventory.tag_number' ,"!=",''])
            ->all();
        // $command = $connection->createCommand("
        //             SELECT 
        //                 segregation_detail.qty,
        //                 segregation_detail.product_name as product_id,
        //                 segregation_detail.sub_category,
        //                 segregation_detail.segregation_detail_id,
        //                 segregation.*,
        //                 products.product_name,
        //                 prod_sub_catagory.sub_catagory_value
        //             FROM segregation_detail
        //             INNER JOIN segregation ON segregation.segregation_id = segregation_detail.segregation_id
        //             INNER JOIN products ON products.products_id = segregation_detail.product_name
        //             INNER JOIN prod_sub_catagory ON products.subcategory = prod_sub_catagory.sub_catagory_id
        //             WHERE segregation_detail.segregation_detail_id = :Recordid

        //         ")->bindValue(":Recordid", $Recordid);
        // $columns = $command->queryOne();
        // echo "<pre>";print_r($columns);die;
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Contact found.',
                'data' => ''
            ]);
        }
    }

    public function actionGettaggingdetail()
    {
        $Recordid = Yii::$app->request->post('Recordid');
        $Productid = Yii::$app->request->post('Productid');
        $Subcategory = Yii::$app->request->post('Subcategory');
        $connection = Yii::$app->db;
        $columns = (new \yii\db\Query())
            ->select([
                'inventory.*',
                'vendor_account.acc_name',
                // 'vendor_locations.vendor_loc_name',
                'warehouse.warehouse_name',
                'prod_sub_catagory.sub_catagory_value',
                'prod_model.prod_model_value',
                'prod_make.prod_make_value',
                'prod_category.prod_category_value',
                'products.product_name',
            ])
            ->from('inventory')
            ->leftJoin('prod_sub_catagory', 'prod_sub_catagory.sub_catagory_id = inventory.subcategory')
            ->leftJoin('prod_model', 'prod_model.prod_model_id = inventory.model')
            ->leftJoin('prod_make', 'prod_make.prod_make_id = inventory.make')
            ->leftJoin('prod_category', 'prod_category.prod_category_id = inventory.category')
            ->innerJoin('vendor_account', 'inventory.account_name = vendor_account.vendoraccid')
            //   ->innerJoin('vendor_locations', 'inventory.location = vendor_locations.vendorloc_id')
            ->leftJoin('warehouse', 'inventory.location = warehouse.warehouse_id')
            ->innerJoin('products', 'products.products_id = inventory.product_name')
            ->where(['inventory.grn_id' => $Recordid])
            ->andWhere(['inventory.product_name' => $Productid])
            ->andWhere(['inventory.subcategory' => $Subcategory])
            ->andWhere(['inventory.status' => 2])
            ->one();

        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Contact found.',
                'data' => ''
            ]);
        }
    }

    public function actionDownloadinventorycsv()
    {
        $Recordid = Yii::$app->request->post('Recordid');
        $Productid = Yii::$app->request->post('Productid');
        $Subcategory = Yii::$app->request->post('Subcategory');
        $statusrows = (new \yii\db\Query())
            ->select(['status_value'])
            ->from('inv_status')
            ->where(['not in', 'status_id', [1, 2]])
            ->all();

        $statusarr = [];

        foreach ($statusrows as $row) {
            $statusarr[] = $row['status_value'];
        }

        // Convert array → single string with " || "
        $statusString = implode(" || ", $statusarr);
        /*if ($Subcategory != "39") //show bin number only for laptop
        {
            $rows = (new \yii\db\Query())
                ->select([
                    "TO_BASE64(inventory.inventory_id) as `ID`",
                    'products.product_name as `Product Name`',
                    'prod_sub_catagory.sub_catagory_value as `Sub Category`',
                    'inventory.serial_number as `Serial Number`',
                    'inventory.tag_number as `Tag Number`',
                    //'inv_status.status_value as `Status`',
                    "'' as `Status`",
                    // 'inventory.*',
                    // 'vendor_account.acc_name',
                    // // 'vendor_locations.vendor_loc_name',
                    // 'warehouse.warehouse_name',
                    // 'prod_sub_catagory.sub_catagory_value',
                    // 'prod_model.prod_model_value',
                    // 'prod_make.prod_make_value',
                    // 'prod_category.prod_category_value',
                    // 'products.product_name',
                ])
                ->from('inventory')
                ->leftJoin('prod_sub_catagory', 'prod_sub_catagory.sub_catagory_id = inventory.subcategory')
                ->leftJoin('prod_model', 'prod_model.prod_model_id = inventory.model')
                ->leftJoin('prod_make', 'prod_make.prod_make_id = inventory.make')
                ->leftJoin('prod_category', 'prod_category.prod_category_id = inventory.category')
                ->innerJoin('vendor_account', 'inventory.account_name = vendor_account.vendoraccid')
                //   ->innerJoin('vendor_locations', 'inventory.location = vendor_locations.vendorloc_id')
                ->leftJoin('warehouse', 'inventory.location = warehouse.warehouse_id')
                ->innerJoin('products', 'products.products_id = inventory.product_name')
                ->innerJoin('inv_status', 'inv_status.status_id = inventory.status')
                ->where(['inventory.grn_id' => $Recordid])
                ->andWhere(['inventory.product_name' => $Productid])
                ->andWhere(['inventory.subcategory' => $Subcategory])
                ->andWhere(['inventory.status' => 2])
                ->all();

        } else {
            */
            $rows = (new \yii\db\Query())
                ->select([
                    "TO_BASE64(inventory.inventory_id) as `ID`",
                    'products.product_name as `Product Name`',
                    'prod_sub_catagory.sub_catagory_value as `Sub Category`',
                    'inventory.serial_number as `Serial Number`',
                    'inventory.tag_number as `Tag Number`',
                    'inventory.bin_number as `Bin Number`',
                    //'inv_status.status_value as `Status`',
                    "'' as `Status`",
                    // 'inventory.*',
                    // 'vendor_account.acc_name',
                    // // 'vendor_locations.vendor_loc_name',
                    // 'warehouse.warehouse_name',
                    // 'prod_sub_catagory.sub_catagory_value',
                    // 'prod_model.prod_model_value',
                    // 'prod_make.prod_make_value',
                    // 'prod_category.prod_category_value',
                    // 'products.product_name',
                ])
                ->from('inventory')
                ->leftJoin('prod_sub_catagory', 'prod_sub_catagory.sub_catagory_id = inventory.subcategory')
                ->leftJoin('prod_model', 'prod_model.prod_model_id = inventory.model')
                ->leftJoin('prod_make', 'prod_make.prod_make_id = inventory.make')
                ->leftJoin('prod_category', 'prod_category.prod_category_id = inventory.category')
                ->innerJoin('vendor_account', 'inventory.account_name = vendor_account.vendoraccid')
                //   ->innerJoin('vendor_locations', 'inventory.location = vendor_locations.vendorloc_id')
                ->leftJoin('warehouse', 'inventory.location = warehouse.warehouse_id')
                ->innerJoin('products', 'products.products_id = inventory.product_name')
                ->innerJoin('inv_status', 'inv_status.status_id = inventory.status')
                ->where(['inventory.grn_id' => $Recordid])
                ->andWhere(['inventory.product_name' => $Productid])
                ->andWhere(['inventory.subcategory' => $Subcategory])
                ->andWhere(['inventory.status' => 2])
                ->all();
        // }
        if (!$rows) {
            throw new \yii\web\NotFoundHttpException("No records found");
        }

        // CSV
        $csv = fopen('php://temp', 'w');
        fputcsv($csv, array_keys($rows[0]));
        $i = 0;
        foreach ($rows as $row) {
            // Insert StatusString into Status column
            if (isset($row['Status']) && ($i == 0)) {
                $row['Status'] = $statusString;
                $i++;
            }
            fputcsv($csv, $row);
        }

        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);

        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'text/csv');
        Yii::$app->response->headers->add('Content-Disposition', 'attachment; filename="inventory_export.csv"');

        return $content;
    }
//     public function actionUploadinventorycsv()
// {
//     $file = UploadedFile::getInstanceByName('csv_file');
//     if (!$file) {
//         return $this->asJson([
//             "status" => "error",
//             "message" => "CSV file not received."
//         ]);
//     }

//     $csvData = array_map("str_getcsv", file($file->tempName));
//     if (count($csvData) < 2) {
//         return $this->asJson([
//             "status" => "error",
//             "message" => "CSV file has no data."
//         ]);
//     }

//     // ---- A. VALIDATE REQUIRED COLUMNS ----
//     $headers = array_map('trim', $csvData[0]);
//     $required = ['inventory_id', 'product_name', 'subcategory'];

//     $missing = array_diff($required, $headers);
//     if ($missing) {
//         return $this->asJson([
//             "status" => "error",
//             "message" => "Missing columns: " . implode(", ", $missing)
//         ]);
//     }

//     $headerIndex = array_flip($headers);
//     unset($csvData[0]);

//     $db = Yii::$app->db;
//     $transaction = $db->beginTransaction();

//     try {
//         foreach ($csvData as $row) {
//             if (count($row) != count($headers)) continue; // skip bad row

//             $rowData = array_combine($headers, $row);
//             $inventoryId = $rowData['inventory_id'];

//             // Check existing
//             $exists = (new \yii\db\Query())
//                 ->from("inventory")
//                 ->where(["inventory_id" => $inventoryId])
//                 ->exists();

//             if ($exists) {
//                 // ---- B. UPDATE ----
//                 $db->createCommand()->update("inventory", [
//                     "product_name" => $rowData["product_name"],
//                     "subcategory"  => $rowData["subcategory"],
//                     // add more mapping if needed
//                 ], ["inventory_id" => $inventoryId])->execute();
//             } else {
//                 // ---- C. INSERT ----
//                 $db->createCommand()->insert("inventory", [
//                     "inventory_id" => $inventoryId,
//                     "product_name" => $rowData["product_name"],
//                     "subcategory"  => $rowData["subcategory"],
//                     "status" => 2
//                 ])->execute();
//             }
//         }

//         $transaction->commit();

//         return $this->asJson([
//             "status" => "success",
//             "message" => "CSV uploaded & processed successfully."
//         ]);

//     } catch (\Throwable $e) {
//         $transaction->rollBack();
//         return $this->asJson([
//             "status" => "error",
//             "message" => $e->getMessage()
//         ]);
//     }
// }

//this function work till serail number validation for mobile
public function actionUploadinventorycsv($Recordid = null, $Productid = null, $Subcategory = null)
{
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    
    $file = UploadedFile::getInstanceByName('csv_file');
    
    if (!$file) {
        return ["status" => "error", "message" => "No file uploaded"];
    }

    $csvData = array_map('str_getcsv', file($file->tempName));

    // if(!$csvData)
    //     {
    //          return [
    //                 "status" => "error",
    //                 "message" => "Invalid file encoding detected at Row  Please save the file as CSV UTF-8 and upload again.",
    //                 // "errors" => $errors,
    //                 "rows" => $csvData,     
    //             ];
    //     }
    // ---- UTF-8 ENCODING CHECK (IMPORTANT) ----
    foreach ($csvData as $rowIndex => $row) {
        foreach ($row as $colIndex => $value) {
            if (!mb_check_encoding($value, 'UTF-8')) {
                // return [
                //     "status"  => "error",
                //     "message" => "Invalid file encoding detected at Row " . ($rowIndex + 1) .
                //                 ". Please save the file as CSV UTF-8 and upload again."
                // ];
                 return [
                    "status" => "error",
                    "message" => "Invalid file encoding detected at Row " . ($rowIndex + 1) .
                                ". Please save the file as CSV UTF-8 and upload again.",
                    // "errors" => $errors,
                    "rows" => $csvData,     
                ];
            }
        }
    }


    // Remove header row
    $headers = array_shift($csvData);

    $requiredHeaders = [
        "ID", "Product Name", "Sub Category",
        "Serial Number", "Tag Number", "Bin Number", "Status"
    ];

    if ($headers !== $requiredHeaders) {
        return ["status" => "error", "message" => "Invalid CSV format or wrong headers"];
    }

    $errors = [];
    $validRows = [];

    // 1. COLLECT SERIAL & TAG FROM CSV (lowercase)
    $csvSerials = [];
    $csvTags = [];

    foreach ($csvData as $r) {
        $csvSerials[] = strtolower($r[3] ?? "");   // Serial Number
        $csvTags[]    = strtolower($r[4] ?? "");   // Tag Number
    }

    // 2. GET DB SERIALS/TAGS (lowercase to match)
    $existing = (new \yii\db\Query())
        ->select([
            'LOWER(serial_number) AS serial_number', 
            'LOWER(tag_number) AS tag_number'
        ])
        ->from('inventory')
        ->where(['LOWER(serial_number)' => $csvSerials])
        ->orWhere(['LOWER(tag_number)' => $csvTags])
        ->all();

    $dbSerials = array_column($existing, 'serial_number');
    $dbTags    = array_column($existing, 'tag_number');

    // FETCH ALL VALID BINS FROM BIN TABLE (LOWERCASE)
    $validBins = (new \yii\db\Query())
        ->select("LOWER(bin_number_value)")   // change column name if needed
        ->from("tag_bin_number")          // change table name if needed
        ->column();

    // Convert to associative array for faster lookup
    $validBins = array_flip($validBins);
            // echo "<pre>";print_r($validBins);die;
    // For internal CSV duplicate detection
    $seenSerial = [];
    $seenTag = [];


    // 3. VALIDATE ROWS
    foreach ($csvData as $index => $row) {

        $rowNum = $index + 1;

        list(
            $encodedID,
            $productName,
            $subCat,
            $serial,
            $tag,
            $binNumber,
            $status
        ) = $row;

        // ORIGINAL VALUES
        $origSerial = $serial;
        $origTag    = $tag;

        // LOWERCASE FOR CHECKING
        $serialLower = strtolower($serial);
        $tagLower    = strtolower($tag);

        // ---- ID decode ----
        $inventory_id = base64_decode($encodedID);
        if (!$inventory_id || !is_numeric($inventory_id)) {
            $errors[$index][] = "Invalid ID (base64 decode failed) at Row $rowNum.";
        }

        // ---- Required fields ----
        // mobile subcategory not required serial number //as per new change of tagging v11 -240
        if ($serialLower === "" && (strtolower($subCat) != "mobile")) $errors[$index][] = "Serial Number is required at Row $rowNum.";
        // if ($serialLower === "") $errors[$index][] = "Serial Number is required at Row $rowNum.";
        if ($tagLower === "")    $errors[$index][] = "Tag Number is required at Row $rowNum.";
        if ($status === "")    $errors[$index][] = "Status is required at Row $rowNum.";

        // ---- Laptop Bin Number rule ----
        if (strtolower($subCat) == "laptop") {
            if (trim($binNumber) === "") {
                $errors[$index][] = "Bin Number required for laptops at Row $rowNum.";
            }
        } else {
            if (trim($binNumber) !== "") {
                $errors[$index][] = "Bin Number must be empty for non-laptops at Row $rowNum.";
            }
        }
    
        // ---- CHECK IF BIN EXISTS IN BIN MASTER TABLE ----
        $binLower = strtolower(trim($binNumber));

        if ($binLower !== "" && !isset($validBins[$binLower])) {
            $errors[$index][] = "Invalid Bin Number '$binNumber' at Row $rowNum.";
        }
        // ---- Status not allowed ----
        $disallowedStatus = ["inventory", "tagging pending"];

        if (in_array(strtolower(trim($status)), $disallowedStatus, true)) {
            $errors[$index][] = "Status '$status' is not allowed at Row $rowNum.";
        }

        // CHECK DUPLICATES (CSV LEVEL)

        //as per new change of tagging v11 -240
        if (isset($seenSerial[$serialLower]) && (strtolower($subCat) != "mobile")) {
            $errors[$index][] = "Duplicate Serial '$origSerial' inside CSV at Row $rowNum.";
        } else {
            $seenSerial[$serialLower] = true;
        }

        if (isset($seenTag[$tagLower])) {
            $errors[$index][] = "Duplicate Tag '$origTag' inside CSV at Row $rowNum.";
        } else {
            $seenTag[$tagLower] = true;
        }

        // CHECK DUPLICATES (DB LEVEL)
        if (in_array($serialLower, $dbSerials)) {
            $errors[$index][] = "Serial '$origSerial' already exists in database at Row $rowNum.";
        }

        if (in_array($tagLower, $dbTags)) {
            $errors[$index][] = "Tag '$origTag' already exists in database at Row $rowNum.";
        }

        if (!empty($errors[$index])) continue;

        // valid row
        $validRows[] = [
            'srno'=>$rowNum,
            "id" => intval($inventory_id),
            "productname" => $productName ?? '',
            "subcategory" => $subCat ?? '',
            "serial" => $origSerial ?? '',
            "tag" => $origTag ?? '',
            "bin" => $binNumber ?? '',
            "status" => $status ?? ''
        ];
    }

    if (!empty($errors)) {
        return [
            "status" => "error",
            "message" => "Validation failed",
            "errors" => $errors,
            "rows" => $csvData,     
        ];
    }

    return [
        "status" => "success",
        "validRows" => $validRows
    ];
}


/*public function actionUpdateinventorycsv()
{
     Yii::$app->log->targets = [];
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

     ini_set('memory_limit', '2024M');
         set_time_limit(0);
    $taggingdetails = explode('_',Yii::$app->request->post('taggingdetails'));
    
    $grn_id = $taggingdetails[0];
    $product_id= $taggingdetails[1];
    $subcategory_id = $taggingdetails[2];

    $modifiedtime = date("Y-m-d h:i:s");
    $modifiedBy = Yii::$app->user->id;

    $rows = json_decode(Yii::$app->request->post('rows'), true);

    if (!$rows) {
        return ["status" => "error", "message" => "No rows received"];
    }

    // 1. Fetch all BINs once
    $bins = (new \yii\db\Query())
        ->select(['bin_number_id', 'bin_number_value'])
        ->from('tag_bin_number')
        ->all();

    // Map bin_number (lowercase, trimmed) => bin_id
    $binMap = [];
    foreach ($bins as $b) {
        $binMap[strtolower(trim($b['bin_number_value']))] = $b['bin_number_id'];
    }

    // 2. Fetch all statuses once
    $statuses = (new \yii\db\Query())
        ->select(['status_id', 'status_value'])
        ->from('inv_status')
        ->all();

    // Map status_value (lowercase, trimmed) => status_id
    $statusMap = [];
    foreach ($statuses as $s) {
        $statusMap[strtolower(trim($s['status_value']))] = $s['status_id'];
    }
    
    $from = "tagging";
    // Extract subcategory from URL

    $inventory_model = New Inventory();
    $i=0;
     // Start transaction
    $transaction = Yii::$app->db->beginTransaction();

    try{
        foreach ($rows as $row) {
            // echo "in foreach:".$row['id'];
                if($i == 0)
                {
                    // echo "<br/>--1";
                    //check uploaded csv file is correct against grn and product and subcategory
                    $checkgrn = (new \yii\db\Query())
                        ->select(['*'])
                        ->from('inventory')
                        ->where(['grn_id'=>$grn_id])
                        ->andWhere(['product_name' =>$product_id])
                        ->andWhere(['subcategory'=>$subcategory_id])
                        ->andWhere(['inventory_id' => $row['id']])
                        ->exists();
                        // echo $checkgrn;die;
                        if (!$checkgrn) {
                            
                            $transaction->rollBack();
                            return ["status" => "error",'message' => 'GRN No.,Product and Sub category are not match with uploaded CSV file.',
                            'redirect' => Yii::$app->urlManager->createUrl(['tagging/dashboard'])];
                        }
                }
                // echo "--2";die;
                $i++;
                if(isset($row['bin']) && !empty($row['bin']))
                    $binNumber = strtolower(trim($row['bin']));
                $statusVal = strtolower(trim($row['status']));

                // Get corresponding IDs
                if(isset($row['bin']) && !empty($row['bin']))
                    $binId    = $binMap[$binNumber] ?? null;
                $statusId = $statusMap[$statusVal] ?? null;

                // Optional: skip if bin or status not found
                if(isset($row['bin']) && !empty($row['bin'])){
                    if ($binNumber && !$binId) {
                    // handle missing bin if needed
                    // continue;
                }}

                if ($statusVal && !$statusId) {
                    // handle missing status if needed
                    // continue;
                }

                // echo "<pre>";print_r($row);die;
                $item = [
                    'serial_number' => $row['serial'],
                    'tag_number'    => $row['tag'],
                    'bin_number'   => $binId ?? '',
                    'status'     => $statusId,
                    'inventory_id' => $row['id']
                ];
                // $inventory_model->updateInventoryStatus($item,$from);
                if(isset($row['bin']) && !empty($row['bin'])){
                    Yii::$app->db->createCommand()->update('inventory', [
                        'serial_number' => $row['serial'],
                        'tag_number'    => $row['tag'],
                        'bin_number'        => $binId,
                        'status'     => $statusId,
                        'modifiedtime' => $modifiedtime,
                        'modifiedby' => $modifiedBy,
                    ], ['inventory_id' => $row['id']])->execute();
                }
                else{
                    Yii::$app->db->createCommand()->update('inventory', [
                        'serial_number' => $row['serial'],
                        'tag_number'    => $row['tag'],
                        'bin_number'        => $binId,
                        'status'     => $statusId,
                        'modifiedtime' => $modifiedtime,
                        'modifiedby' => $modifiedBy,
                    ], ['inventory_id' => $row['id']])->execute();
                }
        }
        // No errors → commit
        $transaction->commit();
        return ["status" => "success",'message' => 'Tagging data updated successfully',
        'redirect' => Yii::$app->urlManager->createUrl(['tagging/dashboard'])];
    }
    catch (\Exception $e) {
            // In case of exception → rollback
            $transaction->rollBack();

            return ["status" => "error",'message' => 'Tagging data not updated successfully',
            'redirect' => Yii::$app->urlManager->createUrl(['tagging/dashboard'])];
        }
    
}*/

    public function actionUpdateinventorycsv()
    {
        Yii::$app->log->targets = [];
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        ini_set('memory_limit', '2048M');
        set_time_limit(0);

        $taggingdetails = explode('_', Yii::$app->request->post('taggingdetails'));

        $grn_id        = $taggingdetails[0] ?? null;
        $product_id    = $taggingdetails[1] ?? null;
        $subcategory_id= $taggingdetails[2] ?? null;

        $modifiedtime = date("Y-m-d H:i:s");
        $modifiedBy   = Yii::$app->user->id;

        $rows = json_decode(Yii::$app->request->post('rows'), true);

        if (empty($rows)) {
            return ["status" => "error", "message" => "No rows received"];
        }

        /* ---------------- BIN MAP ---------------- */
        $bins = (new \yii\db\Query())
            ->select(['bin_number_id', 'bin_number_value'])
            ->from('tag_bin_number')
            ->all();

        $binMap = [];
        foreach ($bins as $b) {
            $binMap[strtolower(trim($b['bin_number_value']))] = $b['bin_number_id'];
        }

        /* ---------------- STATUS MAP ---------------- */
        $statuses = (new \yii\db\Query())
            ->select(['status_id', 'status_value'])
            ->from('inv_status')
            ->all();

        $statusMap = [];
        foreach ($statuses as $s) {
            $statusMap[strtolower(trim($s['status_value']))] = $s['status_id'];
        }

        /* ---------------- CHUNK CONFIG ---------------- */
        $chunkSize = 200;
        $batchData = [];
        $counter   = 0;
        $updated   = 0;

        try {

            foreach ($rows as $row) {

                /* -------- FIRST ROW VALIDATION -------- */
                if ($counter === 0) {
                    $checkgrn = (new \yii\db\Query())
                        ->from('inventory')
                        ->where([
                            'grn_id'       => $grn_id,
                            'product_name' => $product_id,
                            'subcategory'  => $subcategory_id,
                            'inventory_id' => $row['id'],
                        ])
                        ->exists();

                    if (!$checkgrn) {
                        return [
                            "status" => "error",
                            "message" => "GRN No., Product or Subcategory mismatch with uploaded CSV",
                            "redirect" => Yii::$app->urlManager->createUrl(['tagging/dashboard'])
                        ];
                    }
                }

                /* -------- NORMALIZE VALUES -------- */
                $binId = null;
                if (!empty($row['bin'])) {
                    $binKey = strtolower(trim($row['bin']));
                    $binId  = $binMap[$binKey] ?? null;
                }

                $statusKey = strtolower(trim($row['status']));
                $statusId  = $statusMap[$statusKey] ?? null;

                $batchData[] = [
                    'inventory_id' => $row['id'],
                    'serial'       => $row['serial'],
                    'tag'          => $row['tag'],
                    'bin_id'       => $binId,
                    'status_id'    => $statusId,
                ];

                $counter++;

                /* -------- PROCESS CHUNK -------- */
                if ($counter % $chunkSize === 0) {
                    $this->processInventoryChunk($batchData, $modifiedtime, $modifiedBy);
                    $updated += count($batchData);
                    $batchData = [];
                }
            }

            /* -------- PROCESS REMAINING -------- */
            if (!empty($batchData)) {
                $this->processInventoryChunk($batchData, $modifiedtime, $modifiedBy);
                $updated += count($batchData);
            }

            return [
                "status"   => "success",
                "message"  => "Tagging data updated successfully",
                "updated"  => $updated,
                "redirect" => Yii::$app->urlManager->createUrl(['tagging/dashboard'])
            ];

        } catch (\Exception $e) {

            return [
                "status" => "error",
                "message" => "Tagging data not updated successfully",
                "error" => $e->getMessage(),
                "redirect" => Yii::$app->urlManager->createUrl(['tagging/dashboard'])
            ];
        }
    }
    private function processInventoryChunk(array $batchData, $modifiedtime, $modifiedBy)
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            foreach ($batchData as $item) {
                $inventory = \app\models\Inventory::findOne($item['inventory_id']);
                if (!$inventory) continue;

                $oldAttributes = $inventory->getOldAttributes();

                Yii::$app->db->createCommand()->update(
                    'inventory',
                    [
                        'serial_number' => $item['serial'],
                        'tag_number'    => $item['tag'],
                        'bin_number'    => $item['bin_id'],
                        'status'        => $item['status_id'],
                        'modifiedtime'  => $modifiedtime,
                        'modifiedby'    => $modifiedBy,
                    ],
                    ['inventory_id' => $item['inventory_id']]
                )->execute();

                // Inventory log
                $log = new InventoryLogDetails();
                $log->inventory_id = $inventory->inventory_id;
                $log->tagging_updatedby = $modifiedBy;
                $log->tagging_updated_at = $modifiedtime;
                $log->save(false);
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }




    public function actionGetproductdetails()
    {
        $Recordid = Yii::$app->request->post('Recordid');
        $connection = Yii::$app->db;


        // AND pc.product_category_id = p.category
        $command = $connection->createCommand("
                SELECT 
                        p.*, 
                        ps.* ,
                        pm.*, 
                        m.*,
                        pc.*
                    FROM products p
                    LEFT JOIN prod_sub_catagory ps ON p.subcategory = ps.sub_catagory_id
                    LEFT JOIN prod_model pm ON p.model = pm.prod_model_id 
                    LEFT JOIN prod_make m ON p.make = m.prod_make_id
                    LEFT JOIN prod_category pc ON p.category = pc.prod_category_id
                    WHERE p.products_id = :Recordid;

            ")->bindValue(":Recordid", $Recordid);

        // if (isset($_POST['product_group_id'])) {
        //     $productGroupId = intval($_POST['product_group_id']); // Sanitize input
        //     $db = Yii::$app->db;

        //     // Fetch categories based on product_group_id
        //     $query = "SELECT prod_catagory_id AS id, prod_catagory_value AS name FROM prod_catagory WHERE FIND_IN_SET(:product_group_id, prod_group_id) AND is_active = 1 ORDER BY seq_no ASC";
        //     $command = $db->createCommand($query);
        //     $command->bindValue(':product_group_id', $productGroupId);
        //     $categories = $command->queryAll();

        //     // Return categories in JSON format
        //     return ['status' => 'success', 'categories' => $categories];
        // } else {
        //     return ['status' => 'error', 'message' => 'Product Group ID is required.'];
        // }
        $columns = $command->queryOne();

        // $query = "
        //         SELECT sub_catagory_id AS id, sub_catagory_value AS name 
        //         FROM prod_sub_catagory 
        //         WHERE FIND_IN_SET(:category_id, prod_catagory_id) AND is_active = 1";
        //     $command = $connection->createCommand($query);
        //     $command->bindValue(':category_id', $columns['category']);
        //     $subcategories = $command->queryAll();
        //     $columns['subcategories'] = $subcategories;
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Contact found.',
                'data' => ''
            ]);
        }
    }

    public function actionGetlocationcde()
    {
        $Recordid = Yii::$app->request->post('Recordid');
        $connection = Yii::$app->db;
        $command = $connection->createCommand("
                    SELECT * 
                    FROM seg_location_code
                    WHERE locationfloor_id = :Recordid AND is_active = 1
                ")->bindValue(":Recordid", $Recordid);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Contact found.',
                'data' => ''
            ]);
        }
    }

    public function actionCheckserialduplicates()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $serialNumbers = Yii::$app->request->post('serialNumbers', []);
        // echo "<pre>";print_r($serialNumbers);die;
        $duplicates = [];

        if (!empty($serialNumbers)) {
            // Query your table to find existing serial numbers
            $found = Inventory::find()
                ->select('serial_number')
                ->where(['serial_number' => $serialNumbers])
                ->asArray()
                ->all();

            // Extract duplicates from the result
            $duplicates = array_column($found, 'serial_number');
        }

        return ['duplicates' => $duplicates];
    }

    public function actionChecktagduplicates()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $tagNumbers = Yii::$app->request->post('tagNumbers', []);
        // echo "<pre>";print_r($tagNumbers);die;
        $duplicates = [];

        if (!empty($tagNumbers)) {
            // Query your table to find existing serial numbers
            $found = Inventory::find()
                ->select('tag_number')
                ->where(['tag_number' => $tagNumbers])
                ->asArray()
                ->all();

            // Extract duplicates from the result
            $duplicates = array_column($found, 'tag_number');
        }

        return ['duplicates' => $duplicates];
    }

}
