<?php 
require_once("comman.inc.php");
function addCronLog($moduleName, $logMessage, $cronFilename)
{
    $connection = db_connect();
    try {
        $sql = "INSERT INTO cron_history (modulename, log_message, cron_filename, createddatetime)
                VALUES (:modulename, :log_message, :cron_filename, :createddatetime)";

        $stmt = $connection->prepare($sql);
        $stmt->execute([
            ':modulename' => $moduleName,
            ':log_message' => $logMessage,
            ':cron_filename' => $cronFilename,
            ':createddatetime' => date('Y-m-d H:i:s')
        ]);

        return true; // success
    } catch (Exception $e) {
        error_log("Cron log insert failed: " . $e->getMessage());
        return false;
    }
}
?>
