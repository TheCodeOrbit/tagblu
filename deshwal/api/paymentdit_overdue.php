<?php 
try{
    require_once("comman.inc.php");
    $connection = db_connect();
    $now = date("Y-m-d H:i:s");
    $today = date("Y-m-d");
    
   
        // get all the invoice with status =  Invoice Submit - Payment Pending, Partial Payment Received 
        $sql = "SELECT * FROM `invoicedit` where invoice_status in (5,6) and payment_due_date < :today";
        $stmt = $connection->prepare($sql);
        $stmt->execute([':today' => $today]);

        $insert_sql = "Update `invoicedit` set invoice_status = 8 where invoicedit_id=:invoicedit_id ";
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $invoicedit_id = $row["invoicedit_id"];
            $invoicedit_no = $row["invoicedit_no"];
           
            
            $insert_stmt = $connection->prepare($insert_sql);
            $params = [
                ':invoicedit_id' => "$invoicedit_id", 
                
            ];
            $res = $insert_stmt->execute($params);
            if($res){
                echo "\n Invoice No $invoicedit_no, updated to Invoice Overdue\n";
            }else{
                echo "\n Error in updating Invoice No $invoicedit_no \n";
            }
        }
        echo "\n Invoices updation completed for $today\n";
    //end of datawiping notification
}catch(Exception $e){
    echo $e->getMessage();
}catch(Eroor $e){
    echo $e->getMessage();
}
