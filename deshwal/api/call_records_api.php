<?php
// --- Database Connection ---
$dsn = "mysql:host=139.84.169.156:3306;dbname=deshwal;charset=utf8";
$user = "deshwal_erp";
$pass = "Qe2/G@OrK/ndH5t4";
    $today = date("Y-m-d");
try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}

// --- 1. Get metadata (tab + autonumber field column) ---
$sql = "SELECT t.tabid, t.tablename, t.tablekeyid, f.columnname, t.tablabel
        FROM tab t
        JOIN field f ON f.tabid = t.tabid
        WHERE f.uitype = 11";
$stmt = $pdo->query($sql);
$metaRows = $stmt->fetchAll();

$moduleMeta = [];
foreach ($metaRows as $row) {
    $moduleMeta[$row['tabid']] = $row;
}

// --- 2. Get call_information records with tablabel ---
// $sql = "SELECT ci.*, t.tablabel
//         FROM call_information ci
//         JOIN tab t ON ci.related_to = t.tabid";
$sql = "
            SELECT call_information.*, tab.tablabel,
            concat(userownerid.first_name,' ',userownerid.last_name) as 'Call Owner',
            tab.tablabel as 'Related Module',
            call_information.subject as Subject,
            call_information.comments as Comment,
            outgoing_call_statusoutgoing_call_status.outgoingcall_status_value as 'Outgoing Call Status',
            DATE_FORMAT(call_information.call_start_time,'%d-%m-%Y %H:%i:%s') as 'Call Start Time',
            DATE_FORMAT(call_information.call_end_time,'%d-%m-%Y %H:%i:%s') as 'Call End Time',
            call_information.call_duration as 'Call Duration',
            call_typecall_type.calltype_value as 'Call Type',
            call_purposecall_purpose.callpurpose_value as 'Call Purpose',
            call_information.call_agenda as 'Call Agenda',
            call_resultcall_result.callresult_value as 'Call Result'
        FROM call_information 
        left join user as userownerid on (call_information.ownerid=userownerid.id) 
        JOIN tab ON call_information.related_to = tab.tabid
                    left join outgoing_call_status as outgoing_call_statusoutgoing_call_status on (call_information.outgoing_call_status=outgoing_call_statusoutgoing_call_status.outgoingcall_status_id) 
                    left join call_type as call_typecall_type on (call_information.call_type=call_typecall_type.calltypeid) 
                    left join call_purpose as call_purposecall_purpose on (call_information.call_purpose=call_purposecall_purpose.callpurposeid) 
                    left join call_result as call_resultcall_result on (call_information.call_result=call_resultcall_result.callresultid) 
                    left join user as usercreatorid on (call_information.creatorid=usercreatorid.id) 
                    left join user as usermodifiedby on (call_information.modifiedby=usermodifiedby.id) 
                    inner join user as owner on (owner.id=call_information.ownerid) 
                    where call_information.deleted=0 and 1=1 
                    AND DATE(call_information.createdtime) < :today
                    order by call_information.callinfo_id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(["today"=>$today]);  // pass parameter here
$calls = $stmt->fetchAll();
// echo "<pre>";print_r($calls);die;
// --- 3. Resolve autonumber for each record ---
foreach ($calls as &$call) {
    $tabid = $call['related_to'];
    $relId = $call['related_to_id'];

    if (isset($moduleMeta[$tabid])) {
        $meta = $moduleMeta[$tabid];
        $table = $meta['tablename'];
        $pk    = $meta['tablekeyid'];
        $col   = $meta['columnname'];

        // fetch the autonumber value
        $sql = "SELECT $col FROM $table WHERE $pk = :id LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $relId]);
        $autoNum = $stmt->fetchColumn();

        $call['autonumber'] = $autoNum;
    } else {
        $call['autonumber'] = null;
    }
}

echo "<pre>";print_r($calls);die;
// --- 4. Export (CSV Example) ---
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=call_export.csv");

$output = fopen("php://output", "w");

// CSV Header
fputcsv($output, ["Call ID", "Related Module", "Related ID", "Auto Number"]);

// Rows
foreach ($calls as $row) {
    fputcsv($output, [
        $row['callid'],
        $row['tablabel'],
        $row['related_to_id'],
        $row['autonumber']
    ]);
}

fclose($output);
exit;
