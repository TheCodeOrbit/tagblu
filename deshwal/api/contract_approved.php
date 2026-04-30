<?php
error_reporting(E_ALL ^ E_NOTICE);

// include('/var/www/html/deshwal/api/comman.inc.php');
include('comman.inc.php');
$mycon = $conn = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
if (!$mycon) {
    die('Could not connect: ');
} else
    // echo "connected";



// Query with placeholder
$sql = "SELECT account_name FROM `contracts` WHERE contract_end_date = ?";

// Prepare statement
$stmt = $conn->prepare($sql);

// Bind parameter
$todaydate = date('Y-m-d'); // Set your date
$stmt->bind_param("s", $todaydate); // "s" means string type

// Execute query
$stmt->execute();

// Get result set
$result = $stmt->get_result();

// Fetch all results

while ($row = $result->fetch_assoc()) {
    $accounts = $row['account_name'];
    //update accounts billing type to non-rc
    $sql = "UPDATE  `vendor_account` set billing_type = 2 WHERE vendoraccid = ?";

    // Prepare statement
    $stmt = $conn->prepare($sql);
    // Bind parameter  
    $stmt->bind_param("i", $accounts); // "i" means integer type
    // Execute query
    $stmt->execute();
    echo "Updated account id = $accounts<br>";
}

/**code added by ptpatel on date 25-06-25 to expire contracts if contract_end_date is higer thant today */

$ec_todaydate = date('Y-m-d'); // Set your date
$update_sql = "UPDATE contracts SET contract_status = '4' WHERE contract_end_date < ?";
$ec_stmt = $conn->prepare($update_sql);

if ($ec_stmt) {
    $ec_stmt->bind_param("s", $ec_todaydate);
    $ec_stmt->execute();
    echo "Contracts updated successfully.";
} else {
    echo "Error: " . $conn->error;
}

/**code ended by ptpatel on date 25-05-25 */
// Close connection
$stmt->close();
$conn->close();

