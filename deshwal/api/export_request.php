<?php

use app\models\Exportrequest as ModelsExportrequest;
use backend\controllers\ExportrequestController;
// use common\components\ExportRequestHelper;
use backend\models\Exportrequest;


error_reporting(E_ALL ^ E_NOTICE);

include('comman.inc.php');
$conn = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
if (!$conn) {
    die('Could not connect: ');
}
// else
// echo "connected";die;
echo "check export request";
$ModuleName =  $TableName = $FieldId = $from_date = $to_date = $TabId = $uid = $export_all = '';
$sql = "SELECT * FROM `exportrequest` WHERE status = 1";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo "processing request No - ".$row['export_request_no'];
    $tabsql = "SELECT * FROM `tab` WHERE tabid = ?";
    $tabstmt = $conn->prepare($tabsql);
    $tabstmt->bind_param("s", $row['module_name']); // "s" means string type
    $tabstmt->execute();
    $tabresult = $tabstmt->get_result();
    $tabrecord = $tabresult->fetch_assoc();

    // Get result set
    // $result = $stmt->get_result();
    $status = $row['status'];
    $TabId = $tabrecord['tabid'];
    $FieldId = $tabrecord['tablekeyid'];
    $ModuleName = $tabrecord['name'];
    $TableName = $tabrecord['tablename'];
    $from_date = $row['from_date'];
    $to_date = $row['to_date'];
    $uid= $row['ownerid'];
    $export_all = $row['export_all'];
        /*if(exportAllDataAndSave($conn, $TabId, $FieldId, $ModuleName, $TableName, $from_date, $to_date,$row['export_request_no'])){
            $sql = "UPDATE  `exportrequest` set `status` = 2 WHERE export_request_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $row['export_request_id']); // "i" means integer type
            $stmt->execute();
            echo "\nUpdated status of export request id = " . $row['export_request_id'] . ", Export Request No: ".$row['export_request_no']." to Complete";
        }
        else
        {
            echo "\nError in export request id = " . $row['export_request_id'] . " Export Request No.".$row['export_request_no'];
        }*/
        $recordresult = exportAllDataAndSave($conn, $TabId, $FieldId, $ModuleName, $TableName, $from_date, $to_date,$row['export_request_no'],$export_all);

        if ($recordresult['status'] && $recordresult['reason'] === 'success') {
            $sql = "UPDATE  `exportrequest` set `status` = 2 WHERE export_request_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $row['export_request_id']); // "i" means integer type
            $stmt->execute();
            echo "\nUpdated status of export request id = " . $row['export_request_id'] . ", Export Request No: ".$row['export_request_no']." to Complete";
        
        } elseif ($recordresult['reason'] === 'no_records') {
            $sql = "UPDATE  `exportrequest` set `status` = 3 WHERE export_request_id = ?"; //3-no records
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $row['export_request_id']); // "i" means integer type
            $stmt->execute();
            echo "\nNo records found for export request id = " . $row['export_request_id'];
        } else {
            echo "\nError in export request id = " . $row['export_request_id'];
        }
}



function exportAllDataAndSave($conn, $TabId, $FieldId, $ModuleName, $TableName, $from_date, $to_date,$export_request_no,$export_all)
{
    $ColumnList = [];
    $RecordList = [];

    echo "\n Export All Fuction working..";
    // Case 1: Inventory Ageing
    if ($TabId == 77) {
        if (!empty($subcategory_id)) {
            $ColumnList = [
                'grn_date' => 'GRN Date',
                'lot_no' => 'Lot No.',
                'account_name' => 'Account Name',
                'product_name' => 'Product',
                'qty' => 'Quantity',
                'sub_catagory_value' => 'Sub Category',
                'day_0_15' => '0-15 Days',
                'day_16_30' => '16-30 Days',
                'day_31_60' => '31-60 Days',
                'day_61_90' => '61-90 Days',
                'day_91_180' => '91-180 Days',
                'day_180_plus' => '>180 Days',
                'total_value' => 'Total Value'
            ];

            $sql = "
                SELECT 
                    DATE_FORMAT(rep_inventory_ageing.grn_date, '%d-%m-%Y') AS grn_date,
                    rep_inventory_ageing.lot_no,
                    vendor_account.account_name,
                    products.product_name,
                    rep_inventory_ageing.qty,
                    prod_sub_catagory.sub_catagory_value,
                    CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 0 AND 15 THEN rep_inventory_ageing.amount ELSE 0 END AS day_0_15,
                    CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 16 AND 30 THEN rep_inventory_ageing.amount ELSE 0 END AS day_16_30,
                    CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 31 AND 60 THEN rep_inventory_ageing.amount ELSE 0 END AS day_31_60,
                    CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 61 AND 90 THEN rep_inventory_ageing.amount ELSE 0 END AS day_61_90,
                    CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 91 AND 180 THEN rep_inventory_ageing.amount ELSE 0 END AS day_91_180,
                    CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) > 180 THEN rep_inventory_ageing.amount ELSE 0 END AS day_180_plus,
                    rep_inventory_ageing.amount AS total_value
                FROM rep_inventory_ageing
                LEFT JOIN prod_sub_catagory ON prod_sub_catagory.sub_catagory_id = rep_inventory_ageing.subcategory
                LEFT JOIN products ON products.products_id = rep_inventory_ageing.product_name
                INNER JOIN vendor_account ON rep_inventory_ageing.account_name = vendor_account.vendoraccid
                WHERE rep_inventory_ageing.subcategory = ?
            ";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $subcategory_id);
            $stmt->execute();
            $RecordList = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        } else {
            $ColumnList = [
                'sub_catagory_value' => 'Sub Category',
                'qty' => 'Quantity',
                'uom_value' => 'UOM',
                'amt_0_15' => '0-15 Days',
                'amt_16_30' => '16-30 Days',
                'amt_31_60' => '31-60 Days',
                'amt_61_90' => '61-90 Days',
                'amt_91_180' => '91-180 Days',
                'amt_180_plus' => '>180 Days',
                'total_value' => 'Total Value'
            ];

            $sql = "
                SELECT 
                    rep_inventory_ageing.subcategory,
                    prod_sub_catagory.sub_catagory_value,
                    SUM(rep_inventory_ageing.qty) AS qty,
                    SUM(rep_inventory_ageing.amount) AS total_value,
                    prod_uom.uom_value,
                    SUM(CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 0 AND 15 THEN rep_inventory_ageing.amount ELSE 0 END) AS amt_0_15,
                    SUM(CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 16 AND 30 THEN rep_inventory_ageing.amount ELSE 0 END) AS amt_16_30,
                    SUM(CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 31 AND 60 THEN rep_inventory_ageing.amount ELSE 0 END) AS amt_31_60,
                    SUM(CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 61 AND 90 THEN rep_inventory_ageing.amount ELSE 0 END) AS amt_61_90,
                    SUM(CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 91 AND 180 THEN rep_inventory_ageing.amount ELSE 0 END) AS amt_91_180,
                    SUM(CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) > 180 THEN rep_inventory_ageing.amount ELSE 0 END) AS amt_180_plus
                FROM rep_inventory_ageing
                LEFT JOIN prod_sub_catagory ON prod_sub_catagory.sub_catagory_id = rep_inventory_ageing.subcategory
                LEFT JOIN prod_uom ON prod_uom.uom_id = rep_inventory_ageing.uom
                INNER JOIN vendor_account ON rep_inventory_ageing.account_name = vendor_account.vendoraccid
                GROUP BY rep_inventory_ageing.subcategory
            ";

            $result = $conn->query($sql);
            $RecordList = $result->fetch_all(MYSQLI_ASSOC);
        }
    }

    // Case 2: Clubbed Inventory
    else if ($TabId == 80) {
        $ColumnList = [
            'prod_category_value' => 'Category',
            'sub_catagory_value' => 'Sub Category',
            'qty' => 'Quantity',
            'uom_value' => 'UOM',
            'purchase_value' => 'Purchase Value',
            'location_code_value' => 'Location Code',
            'location_floor_value' => 'Location Floor'
        ];

        $sql = "
            SELECT t.*,
                   prod_category.prod_category_value,
                   prod_sub_catagory.sub_catagory_value,
                   prod_uom.uom_value,
                   seg_location_code.location_code_value,
                   seg_location_floor.location_floor_value
            FROM $TableName t
            LEFT JOIN prod_sub_catagory ON prod_sub_catagory.sub_catagory_id = t.subcategory
            LEFT JOIN prod_category ON prod_category.prod_category_id = t.category
            LEFT JOIN prod_uom ON prod_uom.uom_id = t.uom
            INNER JOIN seg_location_code ON t.location_code = seg_location_code.location_code_id
            INNER JOIN seg_location_floor ON t.location_floor = seg_location_floor.location_floor_id
        ";

        $result = $conn->query($sql);
        $RecordList = $result->fetch_all(MYSQLI_ASSOC);
    }

    // Case 3: Default (Generic)
    else {
        // echo "in export all else";die;
        // here you will need to write a custom query to fetch $ColumnList and $RecordList 
        // since Yii2 ListModel->getExportAllRecord() is framework specific.
        // $ColumnList = ['id' => 'ID']; 
        // $RecordList = [];
        // Create model

        // Same call as Yii2
        list($ColumnList, $RecordList, $totalitemcount) = getExportAllRecord(
            $ActionList['OrderBy'] ?? '',
            $ActionList['SortOrder'] ?? ''
        );
        // echo "in end of exportalldata and save";
        // echo "<pre>";print_r($RecordList);die;
    }

    if ($TabId == 18) {
            $allRoles = [];

            // Step 1: Collect all unique role names
            foreach ($RecordList as $record) {
                foreach (['oem_role_user_names', 'org_role_user_names'] as $field) {
                    if (!empty($record[$field])) {
                        $pairs = explode(',', $record[$field]);
                        foreach ($pairs as $pair) {
                            $parts = explode('-', trim($pair), 2);
                            if (count($parts) == 2) {
                                $role = trim($parts[0]);
                                $allRoles[$role] = true;
                            }
                        }
                    }
                }
            }

            $uniqueRoles = array_keys($allRoles); // new dynamic columns
            $finalDataMap = [];

            // Step 2: Build a map of role-based data by RecordId
            foreach ($RecordList as $record) {
                $row = ['record_id' => $record['RecordId']];

                foreach ($uniqueRoles as $role) {
                    $row[$role] = '';
                }

                foreach (['oem_role_user_names', 'org_role_user_names'] as $field) {
                    if (!empty($record[$field])) {
                        $pairs = explode(',', $record[$field]);
                        foreach ($pairs as $pair) {
                            $parts = explode('-', trim($pair), 2);
                            if (count($parts) == 2) {
                                $role = trim($parts[0]);
                                $user = trim($parts[1]);
                                $row[$role] = $user; // or concatenate if needed
                            }
                        }
                    }
                }

                $finalDataMap[$record['RecordId']] = $row;
            }

            // Step 3: Merge finalDataMap into RecordList by RecordId and remove unwanted fields
            foreach ($RecordList as &$record) {
                $id = $record['RecordId'];
                unset($record['oem_role_user_names'], $record['org_role_user_names']); // remove fields

                if (isset($finalDataMap[$id])) {
                    $record = array_merge($record, $finalDataMap[$id]);
                    unset($record['record_id']);
                }                
                unset($record['isEdit']);
                unset($record['RecordId']);
            }
            unset($record); // clean reference
        }

    // If no records
    if (empty($RecordList)) {
        // echo "No records found.";
        // return false;
        return ['status' => false, 'reason' => 'no_records'];
    }

    // Prepare Excel HTML
    // $headers = array_values($ColumnList);
    // $rows = array_map(function ($record) use ($ColumnList) {
    //     return array_map(function ($key) use ($record) {
    //         return $record[$key] ?? "";
    //     }, array_keys($ColumnList));
    // }, $RecordList);


    // Extract headers dynamically
        $headers = array_values($ColumnList);
        if(!empty($uniqueRoles) && $TabId == 18){
            $headers = array_values(array_unique(array_merge($headers, $uniqueRoles)));
        }
        // echo "<pre>";print_r($headers);die;

        // Map filtered records to dynamic headers
        if ($TabId == 18) {
            // Step 1: Start with known field labels
            $headers = $ColumnList; // already key => label

            // Step 2: Detect extra keys from data (like dynamic role fields)
            $allRecordKeys = [];
            foreach ($RecordList as $record) {
                $allRecordKeys = array_merge($allRecordKeys, array_keys($record));
            }
            $allRecordKeys = array_unique($allRecordKeys);

            foreach ($allRecordKeys as $key) {
                if (!isset($headers[$key])) {
                    $headers[$key] = $key; // fallback: use key as label
                }
            }

            // Step 4: Get ordered keys and headers
            $finalKeys = array_keys($headers);
            $headers = array_values($headers); // for export/csv/etc.
            // unset($finalKeys
            // Step 5: Build rows for each record
            $rows = array_map(function ($record) use ($finalKeys) {
                return array_map(function ($key) use ($record) {
                    return $record[$key] ?? '';
                }, $finalKeys);
            }, $RecordList);

        }

        else
        {
            $rows = array_map(function ($record) use ($ColumnList) {
                return array_map(function ($key) use ($record) {
                    return $record[$key] ?? "";
                }, array_keys($ColumnList));
            }, $RecordList);
        }

        
    $excelData = "<html><head><meta charset='UTF-8'></head><body><table border='1'><tr>";
    foreach ($headers as $h) {
        $excelData .= "<th>{$h}</th>";
    }
    $excelData .= "</tr>";

    foreach ($rows as $row) {
        $excelData .= "<tr>";
        foreach ($row as $cell) {
            $excelData .= "<td>" . htmlspecialchars($cell ?? '') . "</td>";
        }
        $excelData .= "</tr>";
    }

    $excelData .= "</table></body></html>";

    if($export_all == 0){
    // Save file
        $filename = "Export_Request_".$export_request_no ."_". ucfirst($ModuleName) . "_" . $from_date . "_TO_" . $to_date . ".xls";
    } else if($export_all == 1)
    {
        $filename = "Export_Request_".$export_request_no ."_". ucfirst($ModuleName) . "_Export_All.xls";
    }
    // $exportPath = __DIR__ . "/exports/Requested_Exported_Files/";
    $exportPath = __DIR__ . "/exports/";
    if (!is_dir($exportPath)) {
        mkdir($exportPath, 0777, true);
    }
    file_put_contents($exportPath . $filename, $excelData);

    echo "\nExported file saved as " . $filename;
    // return true;
    return ['status' => true, 'reason' => 'success'];
}


function getExportAllRecord()
{
    global $ModuleName, $TableName, $conn;
    // 1. Get column definitions
    $ColumnList = getExportAllList();
    // echo "<pre>";print_r($ColumnList);die;
    list($Column, $ListQuery, $totalitemcount) = getQueryforExportAll($ColumnList, '', '', '', '');
    // echo $ListQuery;die;

    // $RecordList = Yii::$app->db->createCommand($ListQuery)
    //     ->queryAll();
    $result = $conn->query($ListQuery);

    $RecordList = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $RecordList[] = $row;
        }
    }
    return array($Column, $RecordList, $totalitemcount);
}

function getExportAllList()
{
    global $ModuleName, $TableName, $conn;
    // Step 1: Get tabid for module
    $sql = "SELECT tabid FROM tab WHERE name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $ModuleName);
    $stmt->execute();
    $tabRes = $stmt->get_result();
    $tabRecord = $tabRes->fetch_assoc();
    $stmt->close();

    if (!$tabRecord) {
        return [];
    }
    $tabId = $tabRecord['tabid'];

    // Step 2: Get block IDs
    $sql = "SELECT blockid FROM blocks WHERE tabid = ? AND display_status = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $tabId);
    $stmt->execute();
    $blockRes = $stmt->get_result();
    $blockIds = [];
    while ($row = $blockRes->fetch_assoc()) {
        $blockIds[] = $row['blockid'];
    }
    $stmt->close();

    if (empty($blockIds)) {
        return [];
    }

    // Step 3: Get fields for those blocks
    $sql = "
        SELECT field.fieldid, 
               field.columnname AS fieldname, 
               field.fieldlabel, 
               field.uitype, 
               field.tablename, 
               field.sequence, 
               field.block
        FROM field
        WHERE field.tabid = ? 
          AND detail_view = 1 
          AND tablename = ? 
          AND block IN (" . implode(',', $blockIds) . ")
        ORDER BY field.block, field.sequence
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $tabId, $TableName);
    $stmt->execute();
    $res = $stmt->get_result();
    $ColumnList = $res->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $ColumnList;
}

function getQueryforExportAll($ColumnList, $OrderBy = '', $SortOrder = '', $rolebasedrecord = '', $modulepermission = '')
{
    global $ModuleName, $TableName, $conn, $FieldId,$from_date,$to_date,$TabId,$uid,$export_all;

    $FieldId = $FieldId;
    $TableName = "`" . $TableName . "`";
    // $RecordId = $this->_members[$FieldId];
    $RecordId = '';//$FieldId;
    $ColumnKey = "";
    $roleid = $rolebasedrecord;
    $Query = '';
    $groupby = '';
    // echo "<br>role id=";
    // echo gettype($rolebasedrecord['userid']); 
    // print_r($roleid);
    // die;
    $join = "from $TableName";
    //$join="from Entity inner join $TableName on(Entity.entityid=$TableName.$FieldId)";
    $Column = array();
    foreach ($ColumnList as $arrColumn) {  //echo "<pre>"; print_r($arrColumn); die;
        $Column[$arrColumn['fieldname']] = $arrColumn['fieldlabel'];
        if ($arrColumn['uitype'] == 8 || $arrColumn['uitype'] == 10) {
            /*$PickList=new PickList;   
                    $PickList->fieldid=$Field->fieldid;
                    $BlockDetail->Fields[$FieldKey]->fieldoptions=$PickList->getPickListValue();*/

            $PickListDetail = getPickListDetail($arrColumn['fieldid']);
            if ($PickListDetail) {
                $targettable = $PickListDetail['targettable'];
                $targetfield = $PickListDetail['targetfield'];
                $dispfield = $PickListDetail['dispfield'];
                if ($arrColumn['fieldname'] == "ownerid" || $PickListDetail['targettable'] == 'user') {

                    $ColumnKey .= "concat(user" . $arrColumn['fieldname'] . '.first_name," ",user' . $arrColumn['fieldname'] . ".last_name) as `" . $arrColumn['fieldname'] . "`,";
                    $join .= " left join `user` as user" . $arrColumn['fieldname'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=user" . $arrColumn['fieldname'] . ".id)";
                } else if ($PickListDetail['targettable'] == 'tab') {


                    $ColumnKey .= 'UPPER(' . $PickListDetail['targettable'] . '.' . $PickListDetail['dispfield'] . ') as `' . $arrColumn['fieldname'] . "`,";
                    $join .= " left join " . $PickListDetail['targettable'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=" . $PickListDetail['targettable'] . "." . $PickListDetail["targetfield"] . ")";
                } else {


                    $ColumnKey .= $PickListDetail['targettable'] . $arrColumn['fieldname'] . '.`' . $PickListDetail['dispfield'] . '` as `' . $arrColumn['fieldname'] . "`,";
                    $join .= " left join " . $PickListDetail['targettable'] . " as " . $PickListDetail['targettable'] . $arrColumn['fieldname'] . " on (" . $TableName . ".`" . $arrColumn['fieldname'] . "`=" . $PickListDetail['targettable'] . $arrColumn['fieldname'] . "." . $PickListDetail["targetfield"] . ")";
                }
            }
        } else if ($arrColumn['uitype'] == 53) {
            /*$PickList=new PickList;   
                    $PickList->fieldid=$Field->fieldid;
                    $BlockDetail->Fields[$FieldKey]->fieldoptions=$PickList->getPickListValue();*/


            $ColumnKey .= "user" . $arrColumn['fieldname'] . '.username as ' . $arrColumn['fieldname'] . ",";
            $join .= " left join `user` as user" . $arrColumn['fieldname'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=user" . $arrColumn['fieldname'] . ".id)";
        } else if ($arrColumn['uitype'] == 22 || $arrColumn['uitype'] == 9) {
            /*$PickListDetail = $this->getPickListDetail($arrColumn['fieldid']);
                $targettable = $PickListDetail['targettable'];
                $targetfield = $PickListDetail['targetfield'];
                $dispfield = $PickListDetail['dispfield'];
                if ($PickListDetail['targettable'] != 'user') {
                    // below if condition reuire when concat function is there than it throgh error special in meeting module
                    if (stripos($PickListDetail['dispfield'], 'concat') !== false) {
                        $ColumnKey .= "GROUP_CONCAT(". $PickListDetail['dispfield'] . " order by " . $PickListDetail['targettable'] . "." . $PickListDetail['targetfield'] . " ) as " . $arrColumn['fieldname'] . ",";
                    }
                    else{
                        $ColumnKey .= "GROUP_CONCAT(" . $PickListDetail['targettable'] . "." . $PickListDetail['dispfield'] . " order by " . $PickListDetail['targettable'] . "." . $PickListDetail['targetfield'] . " ) as " . $arrColumn['fieldname'] . ",";
                    }
                } else {
                    $ColumnKey .= "GROUP_CONCAT(" . $PickListDetail['dispfield'] . " order by " . $PickListDetail['targettable'] . "." . $PickListDetail['targetfield'] . " ) as " . $arrColumn['fieldname'] . ",";
                }
                $alias = $PickListDetail['targettable'] . '_alias';
                // $join .= " left join " . $PickListDetail['targettable'] . " on FIND_IN_SET(" . $PickListDetail['targettable'] . "." . $PickListDetail['targetfield'] . "," . $TableName . "." . $arrColumn['fieldname'] . ")";

                $join .= " left join " . $PickListDetail['targettable'] . " AS " . $alias . " on FIND_IN_SET(" . $alias . ".". $PickListDetail['targettable'] . "." . $PickListDetail['targetfield'] . "," . $TableName . "." . $arrColumn['fieldname'] . ")";



                $groupby = "Group By $TableName.$FieldId";*/
            $PickListDetail = getPickListDetail($arrColumn['fieldid']);
            $targettable = $PickListDetail['targettable'];
            $targetfield = $PickListDetail['targetfield'];
            $dispfield = $PickListDetail['dispfield'];
            $alias = $targettable . '_alias';

            if ($targettable != 'user') {
                if (stripos($dispfield, 'concat') !== false) {
                    // Replace targettable with alias inside dispfield (e.g. user. → user_alias.)
                    // $modifiedDispField = str_ireplace($targettable . ".", $alias . ".", $dispfield);
                    $modifiedDispField = preg_replace_callback(
                        '/\b([a-zA-Z_][a-zA-Z0-9_]*)\b/',
                        function ($matches) use ($alias) {
                            $keywords = ['CONCAT', 'IF', 'IS', 'NULL']; // SQL functions & keywords to skip
                            $word = strtoupper($matches[1]);
                            return in_array($word, $keywords) ? $matches[1] : $alias . '.' . $matches[1];
                        },
                        $dispfield
                    );
                    // Optional debug output
                    // echo $modifiedDispField; die;

                    $ColumnKey .= "GROUP_CONCAT(" . $modifiedDispField . " ORDER BY " . $alias . "." . $targetfield . ") AS " . $arrColumn['fieldname'] . ",";
                } else {
                    $ColumnKey .= "GROUP_CONCAT(" . $alias . "." . $dispfield . " ORDER BY " . $alias . "." . $targetfield . ") AS " . $arrColumn['fieldname'] . ",";
                }
            } else {
                if (stripos($dispfield, 'concat') !== false) {

                    $dispfield = preg_replace('/\b' . preg_quote($targettable, '/') . '\./i', '', $dispfield);

                    // Step 2: Prefix alias only to unqualified fields (skip SQL keywords)
                    $keywords = ['CONCAT', 'IF', 'IS', 'NULL'];

                    $modifiedDispField = preg_replace_callback(
                        '/(?<![\.\w])(\b[a-zA-Z_][a-zA-Z0-9_]*\b)/',
                        function ($matches) use ($alias, $keywords) {
                            $word = $matches[1];
                            return in_array(strtoupper($word), $keywords) ? $word : $alias . '.' . $word;
                        },
                        $dispfield
                    );

                    // echo $modifiedDispField; die;

                    $ColumnKey .= "GROUP_CONCAT(" . $modifiedDispField . " ORDER BY " . $alias . "." . $targetfield . ") AS " . $arrColumn['fieldname'] . ",";
                } else {
                    // Non-CONCAT fields
                    $ColumnKey .= "GROUP_CONCAT(" . $alias . "." . $dispfield . " ORDER BY " . $alias . "." . $targetfield . ") AS " . $arrColumn['fieldname'] . ",";
                }
            }

            // Proper alias used in join
            $join .= " LEFT JOIN " . $targettable . " AS " . $alias . " ON FIND_IN_SET(" . $alias . "." . $targetfield . ", " . $TableName . "." . $arrColumn['fieldname'] . ")";

            $groupby = "GROUP BY $TableName.$FieldId";
        } else if ($arrColumn['uitype'] == 12 || $arrColumn['uitype'] == 27 || $arrColumn['uitype'] == 28) {
            $getEntityNameDetail = getReferenceEntityNameDetail($arrColumn['fieldid']);
            if ($getEntityNameDetail) {
                $targettable = $getEntityNameDetail['targettable'];
                $targetfield = $getEntityNameDetail['entityidfield'];
                $dispfield = $getEntityNameDetail['fieldname'];
                $ColumnKey .= $getEntityNameDetail['targettable'] . $arrColumn['fieldname'] . "." . $dispfield . " as " . $arrColumn['fieldname'] . ",";

                $join .= " LEFT OUTER JOIN " . $getEntityNameDetail['targettable'] . " as " . $getEntityNameDetail['targettable'] . $arrColumn['fieldname'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=" . $getEntityNameDetail['targettable'] . $arrColumn['fieldname'] . "." . $getEntityNameDetail['entityidfield'] . ")";
            }
        } else if ($arrColumn['uitype'] == 26) {
            $ColumnKey .=
                "CASE ";
            $getEntityNameDetailval = getReferenceEntityNameDetailMultiple($arrColumn['fieldid']);

            foreach ($getEntityNameDetailval as $getEntityNameDetail) {
                $modulename = $getEntityNameDetail['modulename'];
                $targettable = $getEntityNameDetail['targettable'];
                $targetfield = $getEntityNameDetail['entityidfield'];
                $dispfield = $getEntityNameDetail['fieldname'];

                if ($modulename == 'opportunities') {
                    $ColumnKey .=
                        "
        WHEN $TableName.related_to = (select tabid from tab where tab.name = '$modulename') THEN opportunity.$dispfield
        ";
                } else {
                    $ColumnKey .=
                        "
        WHEN $TableName.related_to = (select tabid from tab where tab.name = '$modulename') THEN $targettable.$dispfield
        ";
                }



                // $ColumnKey .= $getEntityNameDetail['targettable'] . "." . $dispfield . " as " . $arrColumn['fieldname'] . ",";


                $join .= " LEFT OUTER JOIN " . $getEntityNameDetail['targettable'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=" . $getEntityNameDetail['targettable'] . "." . $getEntityNameDetail['entityidfield'] . ")";
            }
            $ColumnKey .= "ELSE NULL
    END AS " . $arrColumn['fieldname'] . ",";
            // echo $ColumnKey;die;
        } else if ($arrColumn['uitype'] == 25) {

            // $ColumnKey .= 'mrelated_to.mrelatedto_value ' . " as " . $arrColumn['fieldname'] . ",";
            // $join .= " LEFT OUTER JOIN `mrelated_to` "  . " on (" . $TableName . "." . $arrColumn['fieldname'] . "= mrelated_to.mrelatedtoid)";
            $ColumnKey .= 'tab.tablabel ' . " as " . $arrColumn['fieldname'] . ",";
            $join .= " LEFT OUTER JOIN `tab` " . " on (" . $TableName . "." . $arrColumn['fieldname'] . "= tab.tabid)";
        } else if ($arrColumn['uitype'] == 5) {
            $unique_alias = "attachments" . $arrColumn['fieldname'];
            $ColumnKey .= "$unique_alias.name " . " as " . $arrColumn['fieldname'] . ",";

            // `" . $arrColumn['fieldname'] . "` tick added by ptpatel on date 01-08-25 to resolve error for .3years_financial_statement column in account
            $join .= " LEFT OUTER JOIN `attachments` as $unique_alias " . " on (" . $TableName . ".`" . $arrColumn['fieldname'] . "`= $unique_alias.attachmentsid)";
        } elseif ($arrColumn['uitype'] == 6) {
            if ($arrColumn['fieldname'] == 'is_admin' && $arrColumn['tablename'] == "user")
                $ColumnKey .= "if(user.is_admin is not null,if(user.is_admin=0,'No','Yes'),'') as is_admin,";
            else
                $ColumnKey .= str_replace("{$arrColumn['fieldname']}", "if({$TableName} . `{$arrColumn['fieldname']}` is not null,if({$TableName} . `{$arrColumn['fieldname']}`=0,'No','Yes'),'') as `$arrColumn[fieldname]`, ", $arrColumn['fieldname']);
            // $ColumnKey .= str_replace("$arrColumn[fieldname]", "if($arrColumn[fieldname] is not null,if($arrColumn[fieldname]=0,'No','Yes'),'') as $arrColumn[fieldname], ", $arrColumn['fieldname']);
        } elseif ($arrColumn['uitype'] == 13) {
            $ColumnKey .= 'DATE_FORMAT(' . $TableName . '.`' . $arrColumn['fieldname'] . '`,' . "'%d-%m-%Y %H:%i:%s'" . ') as `' . $arrColumn['fieldname'] . '`,';
        } elseif ($arrColumn['uitype'] == 15) {
            $ColumnKey .= 'DATE_FORMAT(' . $TableName . '.`' . $arrColumn['fieldname'] . '`,' . "'%m/%Y'" . ') as `' . $arrColumn['fieldname'] . '`,';
        } elseif ($arrColumn['uitype'] == 17) {
            $ColumnKey .= 'DATE_FORMAT(' . $TableName . '.`' . $arrColumn['fieldname'] . '`,' . "'%d-%m-%Y'" . ') as `' . $arrColumn['fieldname'] . '`,';
            // $ColumnKey .= 'DATE_FORMAT(`' . $arrColumn['fieldname'] . '`,' . "'%d-%m-%Y'" . ') as `' . $arrColumn['fieldname'] . '`,';
        } elseif ($arrColumn['uitype'] == 19) {
            $ColumnKey .= 'DATE_FORMAT(' . $TableName . '.`' . $arrColumn['fieldname'] . '`,' . "'%d/%m/%Y'" . ') as `' . $arrColumn['fieldname'] . '`,';
        }
        //code added by ptpatel on date 01-11-2025 for refrence number with , seperated value
            else if ($arrColumn['uitype'] == 31) {

                    $getEntityNameDetail = getReferenceEntityNameDetail($arrColumn['fieldid']);
                    if ($getEntityNameDetail) {
                        $targettable = $getEntityNameDetail['targettable'];        // e.g. salesorder_dit
                        $targetfield = $getEntityNameDetail['entityidfield'];      // e.g. salesorder_dit_id
                        $dispfield   = $getEntityNameDetail['fieldname'];          // e.g. salesorder_dit_no
                        $alias       = $targettable . $arrColumn['fieldname'];     // e.g. salesorder_ditreference_number

                        // SELECT COLUMN with GROUP_CONCAT (for SO numbers)
                        $ColumnKey .= "GROUP_CONCAT(DISTINCT {$alias}.{$dispfield} 
                                            ORDER BY {$alias}.{$dispfield} 
                                            SEPARATOR ', ') AS {$arrColumn['fieldname']},";

                        // JOIN CONDITION using FIND_IN_SET for multi-IDs
                        $join .= " LEFT JOIN {$targettable} AS {$alias}
                                    ON FIND_IN_SET({$alias}.{$targetfield}, {$TableName}.{$arrColumn['fieldname']})";
                    }

            } 
            //end code added by ptpatel on date 01-11-2025 
        else {
            $ColumnKey .= $arrColumn['tablename'] . "." . $arrColumn['fieldname'] . ",";
        }
        if ($OrderBy == $arrColumn['fieldname'])
            $OrderBy = $arrColumn['tablename'] . "." . $OrderBy;
    }
    $ColumnKey = substr(trim($ColumnKey), 0, -1);

    if ($TableName)

        $ColumnKey = "DISTINCT(" . $TableName . ".$FieldId) as RecordId," . $ColumnKey;
    if ($TableName == "`users`" and $OrderBy == '') {
        //echo $TableName;die;
        $OrderBy = "$TableName.$FieldId";
        $SortOrder = "DESC";
    } else if ($OrderBy == '' and $TableName != "`production`") {
        $OrderBy = "$TableName.$FieldId";
        $SortOrder = "DESC";
    } else if ($TableName == "`production`" and $OrderBy == '') {
        $OrderBy = "$TableName.productionid";
        $SortOrder = "DESC";
    }
    // echo $ColumnKey;die;

    // $SourceModule = Yii::$app->request->get('sourcemodule');
    // $SourceRecordId = Yii::$app->request->get('sourceid');
    $ModuleName = $ModuleName;
    $where = '1=1';
    if($export_all == 0){
        $from_date = date('Y-m-d 00:00:00', strtotime($from_date));
        $to_date   = date('Y-m-d 23:59:59', strtotime($to_date));

        $where .= " AND $TableName.`createdtime` BETWEEN '{$from_date}' AND '{$to_date}' ";
    }
   
    // echo $Query;die;
    //end widget filter code
    if (!empty($RecordId)) {
        $join .= " inner join user on (user.id=$TableName.ownerid)";
        $Query = "select $ColumnKey $join where $TableName.deleted=0 and 
            $FieldId=$RecordId";
        $Query = str_replace(",$TableName.ownerid", ",concat(first_name,' ',last_name) as ownerid", $Query);
    } else {
        // added on 14 jan 2025 to open reference to all users   
        // $isreference = 0;
        // $recordlisting = new ListHire();
        //code added by ptpatel start from here on date 22-03-25
        // $model = new AccessCheck();
        // $id = Yii::$app->user->id;
        // $tabs = $model->tabs($id, $ModuleName);
        // $profile = $model->profile($id, $tabs, $ModuleName);
        // $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        // $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        // $hasadminpower = $model->hasadminpower($profile);

        //this code is for alloed single edit in listview table cell
        //0 not allowed 1= allowed
        // 4,5,6,9 this is leadstatus which is not allowed to edit in listview
        // echo $tablename;die;
        // echo $TabId;die;
        if ($TabId == 18) {
            // echo "in 18";die;
            $ColumnKey .= ', oem_users.oem_role_user_names ';
            $join .= " LEFT JOIN (
                                SELECT 
                                    oem_mgr.vendoraccid,
                                    GROUP_CONCAT(DISTINCT CONCAT(role.rolename, '-', user.first_name, ' ', user.last_name) SEPARATOR ', ') AS oem_role_user_names
                                FROM vendor_account_oem_manager_detail AS oem_mgr
                                INNER JOIN user 
                                    ON user.id = oem_mgr.userid 
                                AND user.role = oem_mgr.roleid
                                INNER JOIN role 
                                    ON role.roleid = oem_mgr.roleid
                                GROUP BY oem_mgr.vendoraccid
                            ) AS oem_users ON oem_users.vendoraccid = vendor_account.vendoraccid ";

            $ColumnKey .= ', org_users.org_role_user_names ';
            $join .= " LEFT JOIN (
                                SELECT 
                                    org_mgr.vendoraccid,
                                    GROUP_CONCAT(DISTINCT CONCAT(role.rolename, '-', user.first_name, ' ', user.last_name) SEPARATOR ', ') AS org_role_user_names
                                FROM vendor_account_orgaisation_section AS org_mgr
                                INNER JOIN user 
                                    ON user.id = org_mgr.userid 
                                AND user.role = org_mgr.roleid
                                INNER JOIN role 
                                    ON role.roleid = org_mgr.roleid
                                GROUP BY org_mgr.vendoraccid
                            ) AS org_users ON org_users.vendoraccid = vendor_account.vendoraccid ";
        }
        //code added by ptpatel end here on date 22-03-25
        $Query = listing($roleid, $modulepermission, $Query, $ColumnKey, $join, $OrderBy, $SortOrder, $TableName, $groupby, 0, $ModuleName, $where,
        $arrColumn['uitype'],$arrColumn['fieldname'],$arrColumn['fieldid'],$uid);
        if($TableName == "`purchase_order_dit`")
            {
                $groupBy = " GROUP BY {$TableName}.{$FieldId} "; // example: purchase_order_dit.purchaseorder_dit_id

                // Find position of ORDER BY (case-insensitive)
                $pos = strpos($Query, "order by");
                // echo $pos;die;
                if ($pos !== false) {
                    // Insert GROUP BY before ORDER BY
                    $Query = substr_replace($Query, $groupBy, $pos, 0);
                } 
            }
        // echo "<br>Query=$Query";
        // die;

        // echo $Query;
        // die;
        // $Query = "$query_res";
        //$recordlisting=new ListHire();
        //$Query=$recordlisting->listing($roleid,$modulepermission,$Query,$ColumnKey,$join,$OrderBy,$SortOrder,$TableName);
    }
    // echo "<br>Query=$Query";
    // die;

    return array($Column, $Query, '');
}


//this function not used because it is not needed
function rolebasedrecord($uid, $profile, $pdo)
{
    try {
        // Check if user is admin
        $stmt = $pdo->prepare("SELECT is_admin FROM `user` WHERE id = ?");
        $stmt->execute([$uid]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $isadmin = $user['is_admin'] ?? 0;

        if ($isadmin == 1) {
            return [
                'userid' => $uid,
                'isadmin' => $isadmin,
                'roleid' => '',
                'rolename' => ''
            ];
        }

        // Check profile global permissions
        $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM `profile2globalpermissions` 
                               WHERE globalactionid IN (1,2) AND globalactionpermission=0 AND profileid=?");
        $stmt->execute([$profile]);
        $perm = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($perm['cnt'] == 3) {
            $isadmin = 1;
            return [
                'userid' => $uid,
                'isadmin' => $isadmin,
                'roleid' => '',
                'rolename' => ''
            ];
        }

        // New logic: show only to reporting officers
        $stmt = $pdo->prepare("SELECT u.is_admin, u2r.roleid, u.id, r.rolename
                               FROM user2role u2r
                               JOIN user u ON u.id = u2r.userid
                               JOIN role2profile r2p ON r2p.roleid = u2r.roleid
                               JOIN role r ON r.roleid = r2p.roleid
                               WHERE u.reports_to = ?");
        $stmt->execute([$uid]);
        $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($roles)) $roles = [];

        $roleid = $roles[0]['roleid'] ?? "";
        $rolename = $roles[0]['rolename'] ?? "";
        $id = $roles[0]['id'] ?? "";
        $isadmin = $roles[0]['is_admin'] ?? 0;

        $userid = $id ? "'$id','$uid'" : $uid;

        // Get role info of current user
        $stmt = $pdo->prepare("SELECT u.is_admin, u2r.roleid, r.rolename
                               FROM user2role u2r
                               JOIN user u ON u.id = u2r.userid
                               JOIN role2profile r2p ON r2p.roleid = u2r.roleid
                               JOIN role r ON r.roleid = r2p.roleid
                               WHERE u.id = ?");
        $stmt->execute([$uid]);
        $role = $stmt->fetch(PDO::FETCH_ASSOC);

        $roleid = $role['roleid'] ?? $roleid;
        $rolename = $role['rolename'] ?? $rolename;

        return [
            'userid' => $userid,
            'isadmin' => $isadmin,
            'roleid' => $roleid,
            'rolename' => $rolename
        ];
    } catch (Exception $e) {
        echo "Some error has occurred: " . $e->getMessage();
        return null;
    }
}

function getPickListDetail($fieldid)
{
    global $conn;
    $sql = "SELECT targettable, targetfield, dispfield FROM picklist WHERE fieldid = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $fieldid); // assuming fieldid is integer
    $stmt->execute();

    $result = $stmt->get_result();
    $Columns = $result->fetch_assoc(); // fetch one row as associative array

    $stmt->close();
    return $Columns;
}

function getReferenceEntityNameDetail($fieldid)
{
    global $conn;
    $sql = "SELECT targettable, entityidfield, fieldname FROM `entityname` WHERE fieldid = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) die("Prepare failed: " . $conn->error);

    $stmt->bind_param("i", $fieldid); // "i" for integer, "s" for string
    $stmt->execute();

    $result = $stmt->get_result();
    $Columns = $result->fetch_assoc(); // fetch single row
    $stmt->close();

    return $Columns;
}

// Function 2: getReferenceEntityNameDetailMultiple
function getReferenceEntityNameDetailMultiple($fieldid)
{
    global $conn;
    $sql = "SELECT modulename, targettable, entityidfield, fieldname 
            FROM `entityname` 
            WHERE fieldid = ? 
            GROUP BY modulename";
    $stmt = $conn->prepare($sql);
    if (!$stmt) die("Prepare failed: " . $conn->error);

    $stmt->bind_param("i", $fieldid);
    $stmt->execute();

    $result = $stmt->get_result();
    $Columns = [];
    while ($row = $result->fetch_assoc()) {
        $Columns[] = $row;
    }
    $stmt->close();

    return $Columns;
}

// Function 3: getRelatedDetail
function getRelatedDetail($fieldid)
{
    global $conn;
    $sql = "SELECT mrelatedto_value FROM `mrelated_to` WHERE mrelatedtoid = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) die("Prepare failed: " . $conn->error);

    $stmt->bind_param("i", $fieldid);
    $stmt->execute();

    $result = $stmt->get_result();
    $Columns = $result->fetch_assoc();
    $stmt->close();

    return $Columns;
}

function listing($roleid, $modulepermission, $Query, $ColumnKey, $join, $OrderBy, $SortOrder, $TableName, $groupby, $isreference, $ModuleName = '', $where = '',
$uitype,$fieldname,$fieldId,$uid)
    {
        global $TableName,$conn;
        // echo $isreference;die;
        $filterFielduitype = $uitype;
        $filterFieldtablename = $TableName;
        $filterFieldName = $fieldname;
        $labelValue = null;//Yii::$app->request->post('labelValue');
        $inputValue = null;
        $fieldId = $fieldId;
        $filterOperator = null;
        $filterselectbox = null;
        $cond = '';
        //echo $where;die;


        // added on 14 jan 2025 for export only converted = 0
        // echo $TableName;die;
        if ($TableName == "`leadinformation`") {
            //removed hide converted leads on 04 Mar 2025
            // $cond .= " and if((`leadinformation`.leadstatus != 4 && `leadinformation`.leadstatus != 3),converted = 0,1=1) ";
        }
        if ($TableName == "`contacts`" || $TableName == "`vendor_account`" || $TableName == "`sourcingdeal`" || $TableName == "`opportunity`") {
            $cond .= " and $TableName.is_temp = 0 ";
        }

        if ($where != '') {
            $cond .= " and " . $where;
        }
        // if ($filterselectbox) {
        //     //get tabid
        //     $tbar = Yii::$app->db->createCommand("SELECT tabid FROM tab WHERE  name = :name")
        //         ->bindValue(':name', $ModuleName)
        //         ->queryOne();
        //     $Tabid = $tbar['tabid'];
        //     $default_filter = Yii::$app->db->createCommand("SELECT * FROM default_filter WHERE  id = :id and tabid=:tabid and (userid=1 or userid=:userid)")
        //         ->bindValue(':id', $filterselectbox)
        //         ->bindValue(':tabid', $Tabid)
        //         ->bindValue(':userid', Yii::$app->user->id)
        //         ->queryOne();

        //     $cond .= " " . $default_filter['default_condition'];
        //     //get user filter

        // }
        // echo $cond;die;
        if ($filterFielduitype == 8) {
            // if ($filterFieldName == 'ownerid') {
            //     $filterFieldtablename = 'userownerid';
            //     $filterFieldName = 'username';
            // } else {
            //     //get fieldname
            //     $PickListDetail = $this->getPickListDetail($fieldId);
            //     $targettable = $PickListDetail['targettable'];
            //     $targetfield = $PickListDetail['targetfield'];
            //     $dispfield = $PickListDetail['dispfield'];

            //     $filterFieldtablename = $targettable . $filterFieldName;
            //     $filterFieldName = $dispfield;
            // }
        }

        if ($filterFieldName == 'creatorid') {
            $filterFieldtablename = 'usercreatorid';
            $filterFieldName = 'username';
        }
        if ($filterFieldName == 'modifiedby') {
            $filterFieldtablename = 'usermodifiedby';
            $filterFieldName = 'username';
        }
        if ($filterFielduitype == 12 || $filterFielduitype == 27 || $filterFielduitype == 28) {
            //get fieldname
            $getEntityNameDetail = getReferenceEntityNameDetail($fieldId);
            $targettable = $getEntityNameDetail['targettable'];
            $targetfield = $getEntityNameDetail['entityidfield'];
            $dispfield = $getEntityNameDetail['fieldname'];

            // $filterFieldtablename = $targettable;
            $filterFieldtablename = $targettable . $filterFieldName;
            $filterFieldName = $dispfield;
        }
        if ($filterFielduitype == 25) {
            //get fieldname            
            $filterFieldtablename = 'tab';
            $filterFieldName = 'tablabel';
        }
        if ($filterFielduitype == 17) {
            $date = $inputValue; // original date in Y-m-d format

            // Convert to a timestamp
            $timestamp = strtotime($date);

            // Format the timestamp to d-m-Y
            $inputValue = date('Y-m-d', $timestamp);
        }

        if ($filterFielduitype == 13) {
            $date = $inputValue; // original date in Y-m-d format

            // Convert to a timestamp
            $timestamp = strtotime($date);

            // Format the timestamp to d-m-Y
            $inputValue = date('Y-m-d H:i:s', $timestamp);
        }
        //add by ptpatel on date 02-04-25
        if ($filterFielduitype == 22) {
            $inputValueArr = $inputValue;
            $inputValue = implode("','", $inputValue);

        }
        // echo $inputValue;die;

        // if($arr_cond = isset($_POST['searchres']) != ''){
        // $model 	  	= new ListSearch;	
        // $cond	  	= $model->SerachResult($TableName);
        // //echo $cond;die;
        // }
        // else $cond = '';	
        $uid = $uid;
        $past_assigned_records = null;
        //get profile of user
        
            $sql = "SELECT profileid 
        FROM role2profile rp 
        JOIN user2role ur ON rp.roleid = ur.roleid 
        WHERE ur.userid = $uid";

        $result = $conn->query($sql);
        $profilerr = $result->fetch_assoc();

        $profileid = isset($profilerr['profileid']) ? (int)$profilerr['profileid'] : null;

        // 2. Check for global action
        $sql2 = "SELECT COUNT(*) as cnt 
                FROM profile2globalpermissions 
                WHERE globalactionid IN (1,2) 
                AND globalactionpermission = 0 
                AND profileid = $profileid";

        $result2 = $conn->query($sql2);
        $hasadminpower = $result2->fetch_assoc();


        // echo $hasadminpower['cnt'];die;
        //print_r($hasadminpower);die;
        $isadmin = 0;
        $access = 0;

        if ($hasadminpower['cnt'] == 2) {
            $isadmin = 1;
            $access = 1;
        }
        $sql3 = "SELECT count(*) as cnt FROM `user` WHERE is_admin=1 AND id = ?";
        $stmt3 = $conn->prepare($sql3);
        $stmt3->bind_param("i", $uid); // assuming userid is integer

        $stmt3->execute();
        $result3 = $stmt3->get_result();

        $hasadminpower = $result3->fetch_assoc();
        // echo $hasadminpower['cnt'];die;
        //print_r($hasadminpower);die;
        if ($hasadminpower['cnt'] > 0) {
            $isadmin = 1;
            $access = 1;
        }


        if ($ModuleName == "pickup") {
            if (empty($isadmin) && !empty($ModuleName)) {
                $sql = "SELECT DISTINCT module_reference_id 
                        FROM owner_tracker 
                        WHERE module = ? 
                        AND ownerid = ? 
                        AND deleted = ?";

                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sii", $ModuleName, $ownerid, 0); 
                // "sii" → s = string (module), i = int (ownerid), i = int (deleted)

                $stmt->execute();
                $result = $stmt->get_result();

                $records_list = [];
                while ($row = $result->fetch_assoc()) {
                    $records_list[] = $row;
                }
                if ($records_list && is_array($records_list) && count($records_list) > 0) {
                    $past_assigned_records = array_map(function ($item) {
                        return $item['module_reference_id'];
                    }, $records_list);
                }
            }
            if (!empty($past_assigned_records) && is_array($past_assigned_records))
                $past_assigned_records = implode(",", $past_assigned_records);
        }
        if (empty($past_assigned_records))
            $past_assigned_records = 0;
        //echo $TableName;die;
        // if ($isadmin == '1') {
            //  if($TableName =='`user`'){
            // $Query="select DISTINCT(id) as RecordId,user.user_name,yearname as fyear,
            // 	user.is_admin ,user.first_name,user.last_name,title,minenamename as mine_name,
            // 	concat(manpower.first_name,' ',manpower.last_name) as manpower_name,
            // 	user.employee_code,contractormaster.company_name as company_name,
            // 	UserType.user_type as utypeid
            // 	from user inner join minename on minename.minenameid= user.mine_name
            // 	left join manpower on manpower.manpowerid= user.manpower_name 
            // 	inner join fyear on fyear.yearid= user.fyear
            // 	inner join UserType on UserType.utypeid = user.utypeid
            // 	INNER JOIN contractormaster on contractormaster.contractormaster_id = user.company_name
            // 	where $TableName.deleted=0 $cond $groupby order by $OrderBy $SortOrder";
            // }else
            // {
            if ($TableName != '`sourcingdeal_contact_role`' && $TableName != '`opportunity_contact_role`')
                $join .= " inner join user as owner on (owner.id=$TableName.ownerid)";
            $Query = "select $ColumnKey $join where $TableName.deleted=0 $cond $groupby order by $OrderBy $SortOrder";
            $Query = str_replace(",$TableName.ownerid", ",concat(first_name,' ',last_name) as ownerid", $Query);
            // }
        // } 
        //this else part is not required becuse we give export all show it will show all data who has permission to export all request module
       /* else {
            if ($modulepermission['shareid'] == '1' || $modulepermission['shareid'] == '2' || $modulepermission['shareid'] == '0') {
                //echo "w";
                // if ($TableName == '`user`') {
                //     $Query = "select DISTINCT(id) as RecordId,user.user_name,yearname as fyear,
                // 		user.is_admin ,user.first_name,user.last_name,title,minenamename as mine_name,
                // 		concat(manpower.first_name,' ',manpower.last_name) as manpower_name,
                // 		user.employee_code,contractormaster.company_name as company_name,
                // 		UserType.user_type as utypeid
                // 		from user inner join minename on minename.minenameid= user.mine_name
                // 		left join manpower on manpower.manpowerid= user.manpower_name 
                // 		inner join fyear on fyear.yearid= user.fyear
                // 		inner join UserType on UserType.utypeid = user.utypeid
                // 		INNER JOIN contractormaster on contractormaster.contractormaster_id = user.company_name
                // 		where $TableName.deleted=0 and $TableName.ownerid IN (" . $roleid['userid'] . ") 
                // $cond $groupby order by $OrderBy $SortOrder";
                // } else {


                $uid = $uid; // $_SESSION[Yii::$app->params['dirName'].'_id'];
                $join .= " inner join user as owner on (owner.id=$TableName.ownerid)";
                $Query = "select $ColumnKey $join where $TableName.deleted=0 and 
						   $TableName.ownerid='" . $uid . "' $cond $groupby order by $OrderBy $SortOrder";
                if ($ModuleName == "leads") {
                    if ($isreference == 1) {
                        $Query = "select $ColumnKey $join where $TableName.deleted=0 
                            $cond $groupby order by $OrderBy $SortOrder";
                    } else {
                        //    old condtion

                        //                         $Query = "select $ColumnKey $join where $TableName.deleted=0 AND (
                        //     leadinformation.vertical_manager IN  (" . Yii::$app->user->id . ")
                        //     OR
                        //     $TableName.ownerid IN (" . $roleid['userid'] . ")
                        // )            $cond $groupby order by $OrderBy $SortOrder";
                        $Query = "select $ColumnKey $join where $TableName.deleted=0 AND 
                ($TableName.ownerid IN (" . $roleid['userid'] . ") || $TableName.creatorid IN (" . $roleid['userid'] . "))
                $cond $groupby order by $OrderBy $SortOrder";
                    }
                } else {
                    if ($isreference == 1) {
                        $Query = "select $ColumnKey $join where $TableName.deleted=0 $cond $groupby order by $OrderBy $SortOrder";
                    } else {
                        if ($ModuleName == "pickup" && !empty($past_assigned_records)) {
                            $Query = "select $ColumnKey $join where $TableName.deleted=0 and ( $TableName.ownerid IN (" . $roleid['userid'] . ") ||  $TableName.creatorid IN (" . $roleid['userid'] . ") || $TableName.pickup_id IN(" . $past_assigned_records . ")) $cond $groupby order by $OrderBy $SortOrder";
                        } else if ($ModuleName == "opportunities") {
                            $Query = "select $ColumnKey $join where $TableName.deleted=0  
                             and 
                            
                              ( $TableName.ownerid IN (" . $roleid['userid'] . ") ||  $TableName.creatorid IN (" . $roleid['userid'] . ")
                            OR (
                            FIND_IN_SET('1',opportunity.team_responsible) AND opportunity.opportunity_stage='4' AND " . $uid . " = IFNULL(opportunity.sa_assigned, 0)
                            )
                            OR (
                            FIND_IN_SET('1',opportunity.team_responsible) AND opportunity.opportunity_stage='4' AND " . $uid . " = IFNULL(opportunity.sf_assigned, 0)
                            )
                            OR (
                            FIND_IN_SET('2',opportunity.team_responsible) AND opportunity.opportunity_stage='4' AND " . $uid . " = IFNULL(opportunity.procurement_team_member, 0)
                            )
                            )
                             
                              $cond $groupby order by $OrderBy $SortOrder";
                        } else if ($ModuleName == "vendoraccount") {
                            // Get the records for the organization section
                           
                                $sql_v = "SELECT vendoraccid FROM `vendor_account_orgaisation_section` WHERE userid = ?";
                                $stmt = $conn->prepare($sql_v);
                                $stmt->bind_param("i", $uid);  // assuming userid is integer

                                $stmt->execute();
                                $result = $stmt->get_result();

                                $org_records = [];
                                while ($row = $result->fetch_assoc()) {
                                    $org_records[] = $row;
                                }

                            // Initialize the base query components
                            $baseQuery = "SELECT $ColumnKey $join WHERE $TableName.deleted = 0 AND (
                     $TableName.ownerid IN (" . $roleid['userid'] . ") ||  $TableName.creatorid IN (" . $roleid['userid'] . ")";

                            // If organization records exist, include vendoraccid condition
                            if ($org_records) {
                                // Extract vendoraccid values from the result set
                                $vendoraccids = array_column($org_records, 'vendoraccid');

                                // Directly insert the vendoraccid values as a comma-separated string
                                $vendoraccids_string = implode(',', $vendoraccids);

                                // Append the condition for vendoraccid to the query
                                $baseQuery .= " OR `vendor_account`.vendoraccid IN ($vendoraccids_string)";
                            }

                            // Append the rest of the conditions
                            $baseQuery .= ") $cond $groupby ORDER BY $OrderBy $SortOrder";

                            // Prepare the query string (without execution)
                            $Query = $baseQuery;

                            // Optional: You can output or log the query to see the final result
                            // echo $query;
                            // die; // Make sure there's no extra code after this point if you are using die here
                        }
                        else if ($ModuleName == "vendorlocations" || $ModuleName == "contacts" || $ModuleName == "contracts") {
                            // Get the records for the organization section
                            $sql_v = "SELECT vendoraccid FROM `vendor_account_orgaisation_section` WHERE userid = ?";
                            $stmt = $conn->prepare($sql_v);
                            $stmt->bind_param("i", $uid); // assuming userid is an integer

                            $stmt->execute();
                            $result = $stmt->get_result();

                            $org_records = [];
                            while ($row = $result->fetch_assoc()) {
                                $org_records[] = $row;
                            }

                            // Initialize the base query components
                            $baseQuery = "SELECT $ColumnKey $join WHERE $TableName.deleted = 0 AND (
                     $TableName.ownerid IN (" . $roleid['userid'] . ") ||  $TableName.creatorid IN (" . $roleid['userid'] . ")";

                            // If organization records exist, include vendoraccid condition
                            if ($org_records) {
                                // Extract vendoraccid values from the result set
                                $vendoraccids = array_column($org_records, 'vendoraccid');

                                // Directly insert the vendoraccid values as a comma-separated string
                                $vendoraccids_string = implode(',', $vendoraccids);

                                // Append the condition for vendoraccid to the query
                                if ($ModuleName == "vendorlocations" )
                                $baseQuery .= " OR `vendor_locations`.vendor_account IN ($vendoraccids_string)";
                                else if ($ModuleName == "contacts" )
                                $baseQuery .= " OR `contacts`.vendor_account_name IN ($vendoraccids_string)";
                                else if ($ModuleName == "contracts" )
                                $baseQuery .= " OR `contracts`.account_name IN ($vendoraccids_string)";
                            }

                            // Append the rest of the conditions
                            $baseQuery .= ") $cond $groupby ORDER BY $OrderBy $SortOrder";

                            // Prepare the query string (without execution)
                            $Query = $baseQuery;

                            // Optional: You can output or log the query to see the final result
                            // echo $query;
                            // die; // Make sure there's no extra code after this point if you are using die here
                        } else {
                            $Query = "select $ColumnKey $join where $TableName.deleted=0 and ( $TableName.ownerid IN (" . $roleid['userid'] . ") ||  $TableName.creatorid IN (" . $roleid['userid'] . ")) $cond $groupby order by $OrderBy $SortOrder";
                        }

                    }
                }
                $Query = str_replace(",$TableName.ownerid", ",concat(first_name,' ',last_name) as ownerid", $Query);
                // }
                //echo $Query;exit;

            }/*else if($utypeid == '9' && $modulepermission['shareid'] == '0' ){
            if($TableName =='`Product`'){
                $join	.= " inner join Depot2Division on Depot2Division.
                         division_id = Product.ProductDivision_productdivisionid
                         INNER JOIN Depot ON Depot.depotid = Depot2Division.depotname";
                $Query	 = "select $ColumnKey $join where $TableName.deleted=0 and 
                       Depot.depotid='".$_SESSION['depot_code']."' $cond order by $OrderBy $SortOrder";
                $Query   = str_replace(",$TableName.ownerid",",concat(first_name,' ',last_name) as ownerid",$Query);
            }else if($TableName =='`PriceBook`'){
                $join	.= " inner join Depot on PriceBook.depotname = Depot.depotid";
                $Query	 = "select $ColumnKey $join where $TableName.deleted=0 and 
                       PriceBook.user_depot_code='".$_SESSION['depot_code']."' $cond order by $OrderBy $SortOrder";
                $Query   = str_replace(",$TableName.ownerid",",concat(first_name,' ',last_name) as ownerid",$Query);
            }else{
                $join	.= " inner join user on $TableName.user_depot_code=user.depot_code";
                $Query	 = "select $ColumnKey $join where $TableName.deleted=0 and 
                       $TableName.user_depot_code='".$_SESSION['depot_code']."' $cond order by $OrderBy $SortOrder";
                $Query   = str_replace(",$TableName.ownerid",",concat(first_name,' ',last_name) as ownerid",$Query);
            }
        }*//* else {
                if ($TableName != '`sourcingdeal_contact_role`' && $TableName != '`opportunity_contact_role`')
                    $join .= " inner join user as owner on (owner.id=$TableName.ownerid)";

                if ($isreference == 1 || ($TableName == '`sourcingdeal_contact_role`' || $TableName == '`opportunity_contact_role`'))
                    $Query = "select $ColumnKey $join where $TableName.deleted=0  $cond $groupby order by $OrderBy $SortOrder";
                else
                    $Query = "select $ColumnKey $join where $TableName.deleted=0 and ( $TableName.ownerid IN (" . $roleid['userid'] . ") || $TableName.creatorid IN (" . $roleid['userid'] . ") || $TableName.ownerid=1 ) $cond $groupby order by $OrderBy $SortOrder";


                $Query = str_replace(",$TableName.ownerid", ",concat(first_name,' ',last_name) as ownerid", $Query);
            }
            // echo $Query;die;
        }*/
        //die;
        // echo $isreference ;die;
        // echo $Query;die;
        return $Query;
    }

// Close connection
$stmt->close();
$conn->close();
