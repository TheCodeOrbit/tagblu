<?php 
try{
    require_once("comman.inc.php");
    $connection = db_connect();
    $now = date("Y-m-d H:i:s");
    $today = date("d-m-Y");
    function get_user_details($connection,$user_id){
        $query = "SELECT * FROM user WHERE id = :user_id";
        $stmt = $connection->prepare($query);
        $stmt->execute([':user_id' => $user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return empty($result)?[]:$result;

    }

    function validateAndFormatDate($inputDate){
        // Validate the input format
        if(empty($inputDate)) return "";
        $dateTimeObject = DateTime::createFromFormat('Y-m-d', $inputDate);
        if ($dateTimeObject && $dateTimeObject->format('Y-m-d') === $inputDate) {
            $formattedDateTime = $dateTimeObject->format('d/m/Y');
            return $formattedDateTime;
        } else {
            return $inputDate;
        }
    }
    //start of datawiping notification
        echo "\n Starting Data wiping notifications  \n";
        // Query to fetch the relevant records
        $sql = "SELECT datawiping_id,data_wiping_no,fe_name,activity_schedule_date,wiping_status.wiping_status_value as wiping_status 
                from data_wiping 
                inner join wiping_status on data_wiping.wiping_status = wiping_status.wiping_statusid 
                where activity_schedule_date is not null 
                and fe_name is not null 
                and deleted=:deleted 
                and DATE(activity_schedule_date) = CURDATE() + INTERVAL 1 DAY";
        $stmt = $connection->prepare($sql);
        $stmt->execute([':deleted' => 0]);

        $insert_sql = "INSERT INTO notification(userid,source_link,read_status,display_status,message,createdtime,modifiedtime) 
            VALUES(:userid,:source_link,:read_status,:display_status,:message,:createdtime,:modifiedtime)";
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $datawiping_id = $row["datawiping_id"];
            $data_wiping_no = $row["data_wiping_no"];
            $fe_name = $row['fe_name'];
            // verify if user exists
            $user_details = get_user_details($connection, $fe_name);
            if(empty($user_details)){
                echo "\n User record is not found for $fe_name, Data Wiping No : $data_wiping_no \n";
                continue;
            }
            
            //Message formating
            $activity_schedule_date = validateAndFormatDate($row['activity_schedule_date']);
            $data_wiping_status = $row["wiping_status"];
            $notification_message = "For Data-Wiping $data_wiping_no, activity is scheduled for $activity_schedule_date. Current status is $data_wiping_status.";
            $source_link = "/deshwal/admin/datawiping/detail?Record=$datawiping_id";
            //Notification table entry
            
            $insert_stmt = $connection->prepare($insert_sql);
            $params = [
                ':userid' => "$fe_name", 
                ':source_link' => "$source_link",
                ':read_status' => 0,
                ':display_status' => 0,
                ':message' => "$notification_message",
                ':createdtime' => $now,
                ':modifiedtime' => $now
            ];
            $res = $insert_stmt->execute($params);
            if($res){
                echo "\n Notification created for $fe_name, Data Wiping No : $data_wiping_no\n";
            }else{
                echo "\n Error in creating notification for $fe_name, Data Wiping No : $data_wiping_no \n";
            }
        }
        echo "\n Data wiping notifications completed for $today\n";
    //end of datawiping notification
}catch(Exception $e){
    echo $e->getMessage();
}catch(Eroor $e){
    echo $e->getMessage();
}
