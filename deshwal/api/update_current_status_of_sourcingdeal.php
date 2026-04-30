<?php
require_once("comman.inc.php");
require_once("add_cron_log.php");
require_once("params.php");
date_default_timezone_set('Asia/Kolkata');
$mycon = db_connect();
// $fiveMinAgo = date("Y-m-d H:i:s", strtotime("-5 minutes"));
// $fiveMinAgo = date("Y-m-d H:i:s", strtotime("-2 day"));echo $fiveMinAgo;die;
$current_stage_moduleName = 'current_status_of_sourcingdeal';
$fileName = basename(__FILE__);

$sqlcronhistory = "SELECT cron_filename, createddatetime, modulename, id
                    FROM cron_history
                    WHERE cron_filename = :cronFilename
                        AND modulename = :moduleName
                    ORDER BY id DESC
                    LIMIT 1";

$stmt = $mycon->prepare($sqlcronhistory);
$stmt->bindParam(':cronFilename', $fileName, PDO::PARAM_STR);
$stmt->bindParam(':moduleName', $current_stage_moduleName, PDO::PARAM_STR);
$stmt->execute();

$lastupdatedtimeresult = $stmt->fetch(PDO::FETCH_ASSOC);
$FirstTime = false;
if ($lastupdatedtimeresult === false) {
    // First time run — no history in cron history table then user current date time 
    $lastupdatedtime = date("Y-m-d H:i:s");
    $FirstTime = true;
} else {
    $lastupdatedtime = $lastupdatedtimeresult['createddatetime'];
}

// echo "<pre>"; print_r($lastupdatedtime); die;

$logMessage = '';
try {

    // $mycon->beginTransaction();
    // 1) Get all sourcing deals
    $sqlDeals = "SELECT sourcingdeal_id, stage 
                 FROM sourcingdeal 
                 WHERE deleted = 0 AND is_temp = 0 ";
                //  order by sourcingdeal_id desc limit 1
    $stmtDeals = $mycon->prepare($sqlDeals);
    $stmtDeals->execute();
    $sourcingDeals = $stmtDeals->fetchAll(PDO::FETCH_ASSOC);

    // 2) Get all modules from mapping table
    $sqlMap1 = "SELECT * FROM sourcingdeal_currentstage_mapping WHERE current_stage = 1 ORDER BY id ASC";
    $stmtMap1 = $mycon->prepare($sqlMap1);
    $stmtMap1->execute();
    $modules1 = $stmtMap1->fetchAll(PDO::FETCH_ASSOC);

    $sqlMap2 = "SELECT * FROM sourcingdeal_currentstage_mapping WHERE current_stage = 2 ORDER BY id ASC";
    $stmtMap2 = $mycon->prepare($sqlMap2);
    $stmtMap2->execute();
    $modules2 = $stmtMap2->fetchAll(PDO::FETCH_ASSOC);

    $sqlMap3 = "SELECT * FROM sourcingdeal_currentstage_mapping WHERE current_stage = 3 ORDER BY id ASC";
    $stmtMap3 = $mycon->prepare($sqlMap3);
    $stmtMap3->execute();
    $modules3 = $stmtMap3->fetchAll(PDO::FETCH_ASSOC);

    // 3) Loop through each sourcing deal
    foreach ($sourcingDeals as $deal) {
        $current_stage_1 =  $current_stage_2 =  $current_stage_3 = '';
        $sdid = $deal['sourcingdeal_id'];
        $moduleUpdated1 = $moduleUpdated2 = $moduleUpdated3 = false;

        // echo "\n ======================= \n SOURCING DEAL: $sdid\n";
        /**
         * 
         * this is working for current_stage_1
         * 
         **/
        
           
           // reset flags used only for modules1 logic
            $isPaymentfound = false;
            $isPOFound = false;

            foreach ($modules1 as $m) {

                $tabid      = $m['tabid'];
                $tablename  = $m['tablename'];
                $status_column_name = $m['status_column_name'];
                $sourcingdeal_column_name = $m['sourcingdeal_column_name'];
                $moduleName = $m['modulename'];

                $module_status_table    = $m['module_status_table'];
                $module_status_id = $m['module_status_id'];
                $module_status_value = $m['module_status_value'];

                // build query (FirstTime logic preserved)
                if (!$FirstTime) {
                    $sqlCheck = "SELECT * FROM $tablename
                                WHERE modifiedtime BETWEEN '$lastupdatedtime' AND '" . date("Y-m-d H:i:s") . "'
                                AND $sourcingdeal_column_name = :sdid";
                } else {
                    $sqlCheck = "SELECT * FROM $tablename WHERE $sourcingdeal_column_name = :sdid";
                }

                if ($tablename == "payments") {
                    $sqlCheck .= " AND PO IS NOT NULL AND TRIM(PO) <> '' ";
                }

                $sqlCheck .= " ORDER BY modifiedtime DESC LIMIT 1";

                $stmtCheck = $mycon->prepare($sqlCheck);
                $stmtCheck->bindParam(':sdid', $sdid);
                $stmtCheck->execute();
                $moduleData = $stmtCheck->fetchAll(PDO::FETCH_ASSOC);

                if (!$moduleData) {
                    continue;
                }

                // If this is payments and has a record -> use it and STOP modules1 processing only
                if ($tablename === 'payments') {
                    // mark payment found for modules1 behavior
                    $isPaymentfound = true;

                    // use latest record (LIMIT 1 so usually one row)
                    $mdata = $moduleData[0];
                    $statusId = $mdata[$status_column_name];
                    $stage_value = getStatusLabel($mycon, $module_status_table, $module_status_id, $module_status_value, $statusId);

                    // append payment info to current_stage_1
                    $current_stage_1 .= ucfirst($moduleName) . " ( " . $mdata['payment_no'] . " : " . $stage_value . " )";
                    $moduleUpdated1 = true;

                    // IMPORTANT: stop checking any further modules inside modules1 (do NOT touch modules2/modules3)
                    break;
                }

                // If payment not found (so far) and this is purchase_order, use it and stop quotes in modules1
                if ($tablename === 'purchase_order' && !$isPaymentfound) {
                    $mdata = $moduleData[0];
                    $statusId = $mdata[$status_column_name];
                    $stage_value = getStatusLabel($mycon, $module_status_table, $module_status_id, $module_status_value, $statusId);

                    $current_stage_1 .= ucfirst($moduleName) . " ( " . $mdata['purchase_order_no'] . " : " . $stage_value . " )";
                    $moduleUpdated1 = true;

                    // mark PO found so we don't add quotes in modules1
                    $isPOFound = true;

                    // stop processing further modules inside modules1 (if you prefer to stop entirely for modules1)
                    break;
                }

                // If neither payment nor PO found yet, check quotes (only if PO wasn't already found)
                if ($tablename === 'quotes' && !$isPaymentfound && !$isPOFound) {
                    $mdata = $moduleData[0];
                    $statusId = $mdata[$status_column_name];
                    $stage_value = getStatusLabel($mycon, $module_status_table, $module_status_id, $module_status_value, $statusId);

                    $current_stage_1 .= ucfirst($moduleName) . " ( " . $mdata['quotes_no'] . " : " . $stage_value . " )";
                    $moduleUpdated1 = true;

                    // you can break here if you want to stop modules1 after finding a quote
                    break;
                }

                // If it's some other module (not payments/po/quotes), keep existing behavior:
                // $mdata = $moduleData[0];
                // $statusId = $mdata[$status_column_name];
                // $stage_value = getStatusLabel($mycon, $module_status_table, $module_status_id, $module_status_value, $statusId);
                // $current_stage_1 .= ucfirst($moduleName) . " ( " . ($mdata[$module_status_value] ?? '') . ":" . $stage_value . " )";
                // $moduleUpdated1 = true;

                // continue to next module in modules1
            }

        // die;
        //end code for current_stage_1

        /**
         * 
         * this is working for current_stage_2
         * 
         **/
        foreach ($modules2 as $m) {

            $tabid      = $m['tabid'];
            $tablename  = $m['tablename'];
            $status_column_name = $m['status_column_name'];
            $sourcingdeal_column_name = $m['sourcingdeal_column_name'];
            $moduleName = $m['modulename'];

            $module_status_table    = $m['module_status_table'];
            $module_status_id = $m['module_status_id'];
            $module_status_value = $m['module_status_value'];
            // echo " → module: $moduleName\n";

            //"SELECT * from purchase_order where modifiedtime >= $fiveMinAgo AND opportunity_name = 965
            // $sqlCheck = "SELECT * from $tablename where modifiedtime  BETWEEN '$lastupdatedtime' AND '".date("Y-m-d H:i:s")."' AND $sourcingdeal_column_name = :sdid AND (PO IS NULL OR TRIM(PO)='') ";
            if(!$FirstTime)
                $sqlCheck = "SELECT * from $tablename where modifiedtime  BETWEEN '$lastupdatedtime' AND '".date("Y-m-d H:i:s")."' AND $sourcingdeal_column_name = :sdid AND (PO IS NULL OR TRIM(PO)='') ";
            else
                $sqlCheck = "SELECT * from $tablename where  $sourcingdeal_column_name = :sdid AND (PO IS NULL OR TRIM(PO)='') ";
            // echo $sqlCheck;die;     
            $stmtCheck = $mycon->prepare($sqlCheck);
            $stmtCheck->bindParam(':sdid', $sdid);
            $stmtCheck->execute();
            // $moduleData = $stmtCheck->fetch(PDO::FETCH_ASSOC); //fetch one records
            $moduleData = $stmtCheck->fetchAll(PDO::FETCH_ASSOC);

            // No record found
            if ($moduleData) {
                //if more than one record found logic need to be change
                // echo "<pre>";print_r($moduleData);die;
                $i2 = 0;
                foreach ($moduleData as $mdata) {
                    $statusId = $mdata[$status_column_name];
                    $stage_value = getStatusLabel($mycon, $module_status_table, $module_status_id, $module_status_value, $statusId);
                    //
                    if($i2 == 0 )
                    {
                        $current_stage_2 .= ucfirst($moduleName);
                        $current_stage_2 .= " ( ".$mdata['payment_no'] . " : " . $stage_value ;
                    }
                    else
                        $current_stage_2 .= " | " .$mdata['payment_no'] . " : " . $stage_value ;
                    $moduleUpdated2 = true;
                    $i2++;
                }
                $current_stage_2 .= " ) ";
            }
        }
        //end code for current_stage_2

        /**
         * 
         * this is working for current_stage_3
         * 
         * **/
        // 4) Loop through each module in mapping table 
        foreach ($modules3 as $m) {

            $tabid      = $m['tabid'];
            $tablename  = $m['tablename'];
            $status_column_name = $m['status_column_name'];
            $sourcingdeal_column_name = $m['sourcingdeal_column_name'];
            $moduleName = $m['modulename'];

            $module_status_table    = $m['module_status_table'];
            $module_status_id = $m['module_status_id'];
            $module_status_value = $m['module_status_value'];
            // echo " → module: $moduleName\n";

            //"SELECT * from purchase_order where modifiedtime >= $fiveMinAgo AND opportunity_name = 965
            // $sqlCheck = "SELECT * from $tablename where modifiedtime  BETWEEN '$lastupdatedtime' AND '".date("Y-m-d H:i:s")."' AND $sourcingdeal_column_name = :sdid";
            
            if(!$FirstTime)
                $sqlCheck = "SELECT * from $tablename where modifiedtime  BETWEEN '$lastupdatedtime' AND '".date("Y-m-d H:i:s")."' AND $sourcingdeal_column_name = :sdid";
            else
                $sqlCheck = "SELECT * from $tablename where  $sourcingdeal_column_name = :sdid ";
            // echo $sqlCheck;die;     
            $stmtCheck = $mycon->prepare($sqlCheck);
            $stmtCheck->bindParam(':sdid', $sdid);
            $stmtCheck->execute();
            // $moduleData = $stmtCheck->fetch(PDO::FETCH_ASSOC); //fetch one records
            $moduleData = $stmtCheck->fetchAll(PDO::FETCH_ASSOC);

            // No record found
            if ($moduleData) {
                $i3=0;
                //if more than one record found logic need to be change
                // echo "<pre>";print_r($moduleData);die;
                foreach ($moduleData as $mdata) {
                    if($moduleName == "drilling")
                        $columnno = 'drilling_no';
                    else if($moduleName == "degaussing")
                        $columnno = 'degaussing_no';
                    else if($moduleName == "inspection")
                        $columnno = 'inspection_no';
                    else if($moduleName == "shredding")
                        $columnno = 'shredding_no';
                    else if($moduleName == "pickup")
                        $columnno = 'pickup_no';
                    else if($moduleName == "datawiping")
                        $columnno = 'data_wiping_no';
                    $statusId = $mdata[$status_column_name];
                    $stage_value = getStatusLabel($mycon, $module_status_table, $module_status_id, $module_status_value, $statusId);
                    if($i3 == 0 )
                    {
                        $current_stage_3 .= ucfirst($moduleName);
                        $current_stage_3 .= " ( ". $mdata[$columnno]. " : " . $stage_value;
                    }
                    else
                        $current_stage_3 .= " | " .$mdata[$columnno] . " : " . $stage_value;
                    $moduleUpdated3 = true;
                    $i3++;
                }
                
                $current_stage_3 .= " ) ";
            } else {
                // echo "   → No record in $moduleName<br>";
                continue;
            }
        }
        //end code for current_stage_3

        //below if line commented because if in first cron run status has something else and in next it will remove and get blank than blank also should be update
        if ($moduleUpdated1 || $moduleUpdated2 || $moduleUpdated3) {
            // echo "\n\n\moduleUpdated1";print_r($moduleUpdated1);
            // echo "\n\moduleUpdated2";print_r($moduleUpdated2);
            // echo "\n\moduleUpdated3";print_r($moduleUpdated3);
            $sqlUpdate = "UPDATE sourcingdeal SET ";
            $setParts = $setlogmsg = [];

            if ($moduleUpdated1) {
                $setParts[] = "current_stage = :current_stage_1";
            }
            if ($moduleUpdated2) {
                $setParts[] = "current_stage_2 = :current_stage_2";
            }
            if ($moduleUpdated3) {
                $setParts[] = "current_stage_3 = :current_stage_3";
            }

            // Join all parts with comma
            $sqlUpdate = "UPDATE sourcingdeal SET " . implode(", ", $setParts) . " WHERE sourcingdeal_id = :sdid";

            // Debug
            // echo $sqlUpdate;
             $stmtUpdate = $mycon->prepare($sqlUpdate);

            if ($moduleUpdated1){
             $current_stage_1  = !empty($current_stage_1) ? $current_stage_1 : '-';
                $stmtUpdate->bindParam(':current_stage_1', $current_stage_1);
                 if (trim($current_stage_1) !== "") {
                        $setlogmsg[] = trim($current_stage_1);
                    }
            }
            if ($moduleUpdated2){
            $current_stage_2  = !empty($current_stage_2) ? $current_stage_2 : '-';
                $stmtUpdate->bindParam(':current_stage_2', $current_stage_2);
                 if (trim($current_stage_2) !== "") {
                    $setlogmsg[] = trim($current_stage_2);
                }
            }
            if ($moduleUpdated3){
            $current_stage_3  = !empty($current_stage_3) ? $current_stage_3 : '-';
                $stmtUpdate->bindParam(':current_stage_3', $current_stage_3);
                if (trim($current_stage_3) !== "") {
                    $setlogmsg[] = trim($current_stage_3);
                }
            }

            $stmtUpdate->bindParam(':sdid', $sdid);
            $stmtUpdate->execute();

            // echo "\n----------------------------- current stage updated for $sdid\n";
            // // // // echo "<pre>";
            // echo "\n\n\current_stage_1";print_r($current_stage_1);
            // echo "\n\ncurrent_stage_2";print_r($current_stage_2);
            // echo "\n\ncurrent_stage_3";print_r($current_stage_3);
            $logMessage .= "current stage for ($sdid-".implode(" || ",$setlogmsg).") ";
            
            // echo "\n".$logMessage;
          
        }
    }
    
        //   exit;
    // $mycon->commit();
} catch (Exception $e) {
    // if ($mycon->inTransaction()) {
    //     $mycon->rollBack();
    // }
    
    $logMessage .= "Error: " . $e->getMessage();
    echo $logMessage;
} finally {
    $cronFile = basename(__FILE__);
    try {
        addCronLog('current_status_of_sourcingdeal', $logMessage, $cronFile);
    } catch (Exception $ex) {
        // Fallback if logging fails
        error_log("Cron log insert failed: " . $ex->getMessage());
    }

    echo "<br/><b>CRON Execution Completed:</b> " . date("Y-m-d H:i:s");
}

// helper: get status label from lookup table
function getStatusLabel($mycon, $table, $idField, $valueField, $statusId) {
    $sql = "SELECT $valueField AS val FROM $table WHERE $idField = :sid AND is_active = 1";
    $stm = $mycon->prepare($sql);
    $stm->bindParam(':sid', $statusId);
    $stm->execute();
    $r = $stm->fetch(PDO::FETCH_ASSOC);
    return ($r) ? $r['val'] : null;
}
