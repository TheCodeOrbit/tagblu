<?php
require_once("/app/api/comman.inc.php");
$connection = db_connect();
$tables = ['customer_pickup_request', 'customer_pickup_assets', 'sourcingdeal', 'call_information', 'meeting_information'];
foreach ($tables as $table) {
    try {
        $stmt = $connection->query("SELECT COUNT(*) FROM $table");
        $count = $stmt->fetchColumn();
        echo "Table $table has $count records.\n";
    } catch (Exception $e) {
        echo "Error querying table $table: " . $e->getMessage() . "\n";
    }
}
