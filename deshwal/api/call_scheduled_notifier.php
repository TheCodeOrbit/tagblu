<?php
require_once("comman.inc.php");
require_once("params.php");

$mycon = db_connect(); // assuming this returns a PDO connection
date_default_timezone_set('Asia/Kolkata');
$now = date("Y-m-d H:i:s");
echo $now."\n";
// Step 1: Prepare the query
//all call start time is less than 5 min from now 
$sql = "
    SELECT callinfo_id,ownerid,call_start_time
    FROM call_information
   WHERE call_start_time > :now
    AND TIMESTAMPDIFF(SECOND, :now, call_start_time) <= 300
    AND is_notified = 0
";
$stmt = $mycon->prepare($sql);

// Step 2: Bind parameters
$stmt->bindValue(':now', $now);

// Step 3: Execute
$stmt->execute();

// Step 4: Fetch results
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";print_r($result);

 if (php_sapi_name() === 'cli' || defined('STDIN')) {
        // Default values when running via CLI
        $protocol = 'https'; // force https for cron
        $host = 'erp.ditserv.com'; // fallback domain for cron
} else {
        // Automatically detect HTTP or HTTPS
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'erp.ditserv.com';
}
 if (php_sapi_name() === 'cli') {
    global $argv; //this is used when we pass aarg from command line like below line 
    //php filename.php h='localhost/deshwal'

    // Remove script name and parse the rest
    parse_str(implode('&', array_slice($argv, 1)), $_GET);
    }
if (!empty($_GET['h']) && $_GET['h'] == 'localhost/deshwal') {
    $host = 'localhost/deshwal';
}
else if (!empty($_GET['h']) && $_GET['h'] == '139.84.169.156/deshwal') {
    $host = '139.84.169.156/deshwal';
}
else if (!empty($_GET['h']) && $_GET['h'] == 'stagerp') {
    $host = 'stagerp.ditserv.com';
}
else if (!empty($_GET['h']) && $_GET['h'] == 'erp') {
    $host = 'erp.ditserv.com';
}

// Step 5: Process each row
foreach ($result as $row) {
    // echo "in foreach";
    $callId     = $row['callinfo_id'];
    $creatorId  = $row['ownerid'];
    $callStart  = $row['call_start_time'];
    $formattedCallStart = date("d-m-Y h:i A", strtotime($callStart));


    $source_link = $protocol . '://' . $host .'/admin/call/detail?Record='.$callId;
    //  Notification message
    $message = "You have a Call scheduled at $formattedCallStart. Please be ready.";

    $insert = "
            INSERT INTO notification (userid, source_link, read_status, display_status, message, createdtime)
            VALUES (:user_id, :source_link, :read_status, :display_status, :message, :createdtime)
        ";
        $insertStmt = $mycon->prepare($insert);
        $insertStmt->bindValue(':user_id', $creatorId, PDO::PARAM_INT);
        $insertStmt->bindValue(':source_link', $source_link, PDO::PARAM_STR);
        $insertStmt->bindValue(':read_status', 0, PDO::PARAM_INT);
        $insertStmt->bindValue(':display_status', 0, PDO::PARAM_INT);
        $insertStmt->bindValue(':message', $message, PDO::PARAM_STR);
        $insertStmt->bindValue(':createdtime', $now);
        $insertStmt->execute();

    // echo "inserted";
    // Update call_info to mark as notified
    $update = "UPDATE call_information SET is_notified = 1 WHERE callinfo_id = :callinfo_id";
    $updateStmt = $mycon->prepare($update);
    $updateStmt->bindValue(':callinfo_id', $callId, PDO::PARAM_INT);
    $updateStmt->execute();

    echo "Notification added for call ID $callId (Start: $callStart)\n";
}

echo "Script completed.\n";
?>
