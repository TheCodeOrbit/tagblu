<?php 
try{
    date_default_timezone_set('Asia/Kolkata');
    require_once("comman.inc.php");
    require_once("add_cron_log.php");
    $connection = db_connect();
    $now = date("Y-m-d H:i:s");
    $today = date("Y-m-d");

    // Assuming you already have $connection = Yii::$app->db (or PDO connection)

    $log_message = '';

    //  Fetch all delivered orders that are not yet closed or payment pending
    //stage 8 = delivered
    $sql = "SELECT * FROM sales_order 
            WHERE stage = 8 
            AND TRIM(LOWER(payment_terms)) IN ('credit', 'advance')";
    $stmt = $connection->prepare($sql);
    $stmt->execute();

    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($orders as $order) {
        $paymentTerm   = strtolower(trim($order['payment_terms']));
        $deliveredDate = !empty($order['delivery_date']) ? trim($order['delivery_date']) : '';
        $salesOrderId  = $order['salesorder_id'];

        // Ensure delivery date exists
        if (empty($deliveredDate)) {
            continue;
        }

        //  Calculate how many days since delivery
        $daysSinceDelivery = (strtotime($today) - strtotime($deliveredDate)) / (60 * 60 * 24);

        //  Handle both cases
        if ($paymentTerm === 'credit') {
            // If payment term is Credit, change to Payment Pending after 2 days
            //stage 9 = payment pending
            if ($daysSinceDelivery >= 2) {
                $updateSql = "UPDATE sales_order 
                            SET stage = 9, modifiedtime = :modifiedtime 
                            WHERE salesorder_id = :id";
                $updateStmt = $connection->prepare($updateSql);
                if ($updateStmt->execute([
                ':modifiedtime' => date('Y-m-d H:i:s'),
                ':id' => $salesOrderId
                    ])) {
                        $log_message .= "<br/>Updated sales_order_id {$salesOrderId} to Payment Pending.";
                    }

            }
        } elseif ($paymentTerm === 'advance') {
            // If payment term is Advance, close immediately after delivery
            //12 = 'SO Closed'
            $updateSql = "UPDATE sales_order 
                        SET stage = 12, modifiedtime = :modifiedtime 
                        WHERE salesorder_id = :id";
            $updateStmt = $connection->prepare($updateSql);
            if ($updateStmt->execute([
                ':modifiedtime' => date('Y-m-d H:i:s'),
                ':id' => $salesOrderId
            ])) {
                $log_message .= "<br/>Updated sales_order_id {$salesOrderId} to SO Closed.";
            }
            
        }
    }
}catch (Exception $e) {
    $log_message .= "<br/>Exception: " . $e->getMessage();
    echo $e->getMessage();

} catch (Error $e) {
    $log_message .= "<br/>Error: " . $e->getMessage();
    echo $e->getMessage();

} finally {
    $cronFile = basename(__FILE__);
    try {
        addCronLog('salesorder', $log_message, $cronFile);
    }
    catch (Exception $ex) {
        // Fallback if logging fails
        error_log("Cron log insert failed: " . $ex->getMessage());
    }

    echo "<br/><b>CRON Execution Completed:</b> " . date("Y-m-d H:i:s");
}
?>
