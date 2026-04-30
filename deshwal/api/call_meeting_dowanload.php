<?php
try {
     $rootDir = dirname(__DIR__);

     require_once($rootDir . '/PHPMailer/src/Exception.php');
     require_once($rootDir . '/PHPMailer/src/PHPMailer.php');
     require_once($rootDir . '/PHPMailer/src/SMTP.php');

     // use PHPMailer\PHPMailer\PHPMailer;
     // use PHPMailer\PHPMailer\Exception;

     require_once("comman.inc.php");
     require_once("params.php");
     $connection = db_connect();
     $now = date("Y-m-d H:i:s");
     $today = date("Y-m-d");
     $directory = __DIR__ . '/exports';

     function checkMailStatus($date_cond)
     {
          // Assuming db_connect() returns a PDO connection
          $mycon = db_connect();
          $result_count = 1;

          // Correct query without quotes around :date_cond
          $query_mailstatus = "SELECT mail_run_date FROM mail_status WHERE mail_type = 2 AND mail_run_date = :date_cond";

          // Step 2: Prepare the statement
          $stmt = $mycon->prepare($query_mailstatus);

          // Step 3: Bind the parameter
          $stmt->bindValue(':date_cond', $date_cond, PDO::PARAM_STR); // Bind as string

          // Step 4: Execute the query
          $stmt->execute();

          // Step 5: Get the result
          $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch results as associative array

          // Step 6: Count the number of rows
          $result_count = count($result);

          // Return the result count
          return $result_count;
     }

     function upMailStatus($date_cond, $mailStatus)
     {
          // Assuming db_connect() returns a PDO connection
          $mycon = db_connect();

          // Step 1: Prepare the query using PDO's prepare() method
          $query = "INSERT INTO `mail_status` (`mail_run_date`, `mail_type`, `status`, `created_time`, `modified_time`)
              VALUES (:date_cond, 2, :mailStatus, NOW(), NOW())";

          // Step 2: Prepare the statement
          $stmt = $mycon->prepare($query);

          // Step 3: Bind the parameters
          $stmt->bindParam(':date_cond', $date_cond, PDO::PARAM_STR);
          $stmt->bindParam(':mailStatus', $mailStatus, PDO::PARAM_STR);

          // Step 4: Execute the query
          $stmt->execute();
     }
     function deleteOlderFiles($folder){
          // Number of days
          $days = 30;             // delete files older than 7 days
          $now    = time();

          // allowed prefixes
          $prefixes = [
          "meetings_detail_",
          "calls_detail_",
          "sourcingdeal_",
          "sourcingdeal_productdetail_"
          ];

          // Loop through files in the folder
          foreach (glob($folder . "/*") as $file) {
               if (is_file($file)) {
                    $filename = basename($file);   // only file name, not path
                    $fileTime = filemtime($file);  // last modified time

                    // If file is older than X days
                    if ($now - $fileTime >= ($days * 24 * 60 * 60)) {
                         // Check if file starts with any prefix
                         foreach ($prefixes as $prefix) {
                              if (strpos($filename, $prefix) === 0) {
                                   if (unlink($file)) {
                                   echo "Deleted: $filename\n";
                                   } else {
                                   echo "Failed to delete: $filename\n";
                                   }
                                   break; // stop checking other prefixes
                              }
                         }
                    }
               }
          }

     }
     deleteOlderFiles($directory);
     $result_count = checkMailStatus($today);
     echo "<br>Mail send count=$result_count";
     if ($result_count == 0) {//startmail
          // Your query with headers in SELECT
         
          // --- 1. Get metadata (tab + autonumber field column) ---
          $call_meta_sql = "SELECT t.tabid, t.tablename, t.tablekeyid, f.columnname, t.tablabel
               FROM tab t
               JOIN field f ON f.tabid = t.tabid
               WHERE f.uitype = 11";
          $call_meta_stmt = $connection->query($call_meta_sql);
          $metaRows = $call_meta_stmt->fetchAll(PDO::FETCH_ASSOC);

          $moduleMeta = [];
          foreach ($metaRows as $row) {
          $moduleMeta[$row['tabid']] = $row;
          }
          // echo "<pre>";print_r($moduleMeta);die;
          $call_sql = "SELECT call_information.*, tab.tablabel,
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
                    call_resultcall_result.callresult_value as 'Call Result',
                    CONCAT(u2.first_name,' ',u2.last_name) as 'Created By',
                    CONCAT(u3.first_name,' ',u3.last_name) as 'Modified By',
                    call_information.createdtime as 'Created Time',
                    call_information.modifiedtime as 'Modified Time',                    
                    vendor_accountaccount_name.acc_name as 'Account Name'
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
                              LEFT JOIN user u2 ON u2.id = call_information.creatorid
                              LEFT JOIN user u3 ON u3.id = call_information.modifiedby
                              LEFT OUTER JOIN vendor_account as vendor_accountaccount_name on (call_information.account_name=vendor_accountaccount_name.vendoraccid) 
                              where call_information.deleted=0 and 1=1 
                              AND DATE(call_information.createdtime) < :today
                              order by call_information.callinfo_id DESC";
          $call_stmt = $connection->prepare($call_sql);
          $call_stmt->execute(['today' => $today]);
          $calls = $call_stmt->fetchAll(PDO::FETCH_ASSOC);
          
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
                    // $stmt = $pdo->prepare($sql);
                    $stmt = $connection->prepare($sql);
                    $stmt->execute([':id' => $relId]);
                    $autoNum = $stmt->fetchColumn();

                    $call['Related Record'] = $autoNum;
               } else {
                    $call['Related Record'] = null;
               }
          }
          // echo "<pre>";print_r($calls);die;
          $call_filePath = $directory . "/calls_detail_$today.csv";
          $call_fp = fopen($call_filePath, 'w');
          if (!$call_fp) {
               throw new Exception("Unable to create or write to the CSV file.");
          }

          
          $call_headers = ["Call Owner","Related Module","Related Record","Account Name","Subject","Comment","Outgoing Call Status","Call Start Time",
          "Call End Time","Call Duration","Call Type","Call Purpose","Call Agenda","Call Result","Created By","Last Modified By","Created Time","Modified Time"];
          fputcsv($call_fp, $call_headers);

          // Data rows
         foreach ($calls as $row) {
          fputcsv($call_fp, [
               $row['Call Owner'],
               $row['Related Module'],
               $row['Related Record'],
               $row['Account Name'],
               $row['Subject'],
               $row['Comment'],
               $row['Outgoing Call Status'],
               $row['Call Start Time'],
               $row['Call End Time'],
               $row['Call Duration'],
               $row['Call Type'],
               $row['Call Purpose'],
               $row['Call Agenda'],
               $row['Call Result'],
               $row['Created By'],
               $row['Modified By'],
               $row['Created Time'],
               $row['Modified Time'],
          ]);
          }

          fclose($call_fp);
          echo "\nCSV file saved to: $call_filePath";
          /**code end for call Module CSV file creation and store into export folder*/

          /***meeting module CSV file creation and store into export folder */
          // --- 1. Get metadata (tab + autonumber field column) ---
          $meet_meta_sql = "SELECT t.tabid, t.tablename, t.tablekeyid, f.columnname, t.tablabel
               FROM tab t
               JOIN field f ON f.tabid = t.tabid
               WHERE f.uitype = 11";
          $meet_meta_stmt = $connection->query($meet_meta_sql);
          $metaRows = $meet_meta_stmt->fetchAll(PDO::FETCH_ASSOC);

          $moduleMeta = [];
          foreach ($metaRows as $row) {
          $moduleMeta[$row['tabid']] = $row;
          }
          // echo "<pre>";print_r($moduleMeta);die;
          
                    // vendor_locationslocation.vendor_loc_name as 'Location',
                    // LEFT OUTER JOIN vendor_locations on (meeting_information.location=vendor_locations.vendorloc_id) 
                    // GROUP_CONCAT(concat(contacts_alias.first_name,' ',contacts_alias.last_name) ORDER BY contacts_alias.contacts_id) AS 'External Participants',
                    
                    
                    // LEFT JOIN contacts AS contacts_alias ON FIND_IN_SET(contacts_alias.contacts_id, meeting_information.external_participants) 
                    // LEFT JOIN user AS user_alias ON FIND_IN_SET(user_alias.id, meeting_information.internal_participants) 
          $meet_sql = "SELECT meeting_information.*, tab.tablabel,
                    concat(userownerid.first_name,' ',userownerid.last_name) as 'Meeting Owner',
                    meeting_information.title as 'Title',
                    vendor_locations.vendor_loc_name as 'Location',
                    if(meeting_information.all_day is not null,
                    if(meeting_information.all_day=0,'No','Yes'),'') as 'All Day',
                    DATE_FORMAT(meeting_information.from,'%d-%m-%Y %H:%i:%s') as 'From',
                    DATE_FORMAT(meeting_information.to,'%d-%m-%Y %H:%i:%s') as 'To',
                    concat(userhost.first_name,' ',userhost.last_name) as 'Host',
                    concat(usersolution_architect.first_name,' ',usersolution_architect.last_name) as 'Solution Architect',
                    ( SELECT GROUP_CONCAT(DISTINCT CONCAT(u.first_name,' ',u.last_name) ORDER BY u.id SEPARATOR ', ') 
                        FROM user u 
                            WHERE FIND_IN_SET(u.id, REPLACE(meeting_information.internal_participants,' ', '')) ) AS `Internal Participants`,
                    ( SELECT GROUP_CONCAT(DISTINCT CONCAT(c.first_name,' ',c.last_name) ORDER BY c.contacts_id SEPARATOR ', ') 
                        FROM contacts c 
                        WHERE FIND_IN_SET(c.contacts_id, REPLACE(meeting_information.external_participants,' ', '')) ) AS `External Participants`,
                    tab.tablabel as 'Related Module',
                    if(meeting_information . `repeat` is not null,
                    if(meeting_information . `repeat`=0,'No','Yes'),'') as 'Repeat', 
                    task_repeattyperepeat_type.repeattype_value as 'Repeat Type',
                    mparticipants_reminderparticipants_reminder.mparticipants_reminder_value as 'Participants Reminder',
                    meeting_information.internal_comments as 'Internal Comments',
                    meeting_information.external_comments as 'External Comments',
                    mreminderremainder.mreminder_value as 'Remainder',
                    vendor_accountaccount_name.acc_name as 'Account Name',
                    meeting_information.from_location As 'From Location',
                    meeting_information.to_location as 'To Location',
                    if(meeting_information . confirms is not null,
                    if(meeting_information . confirms=0,'No','Yes'),'') as 'Confirms',
                    if(meeting_information . distance1 is not null,
                    if(meeting_information . distance1=0,'No','Yes'),'') as 'Distance',
                    if(meeting_information . MOM_shared is not null,
                    if(meeting_information . MOM_shared=0,'No','Yes'),'') as 'MOM Shared',
                    mconveyance_requiredconveyance_required.mconveyance_required_value as 'Conveyance Required',
                    meeting_information.description as 'Description',
                    meeting_expence_categoryexpence_category.expence_category_value as 'Expence Category',
                    meeting_information.expence_type as 'Expence Type',
                    meeting_tax_typetax_type.tax_type_value as 'Tax Type',
                    DATE_FORMAT(meeting_information.expence_date,'%d-%m-%Y') as 'Expence Date',
                    if(meeting_information . submit_approval is not null,
                    if(meeting_information . submit_approval=0,'No','Yes'),'') as 'Submit Approval',
                    CONCAT(u2.first_name,' ',u2.last_name) as 'Created By',
                    CONCAT(u3.first_name,' ',u3.last_name) as 'Modified By',
                    meeting_information.createdtime as 'Created Time',
                    meeting_information.modifiedtime as 'Modified Time'
               FROM meeting_information     
                    left join user as userownerid on (meeting_information.ownerid=userownerid.id) 
                    LEFT OUTER JOIN vendor_locations on (meeting_information.location=vendor_locations.vendorloc_id) 
                    left join user as userhost on (meeting_information.host=userhost.id) 
                    left join user as usersolution_architect on (meeting_information.solution_architect=usersolution_architect.id) 
                    LEFT OUTER JOIN tab on (meeting_information.related_to= tab.tabid) 
                    left join task_repeattype as task_repeattyperepeat_type on (meeting_information.repeat_type=task_repeattyperepeat_type.repeattype_id) 
                    left join mparticipants_reminder as mparticipants_reminderparticipants_reminder on (meeting_information.participants_reminder=mparticipants_reminderparticipants_reminder.mparticipants_reminderid) 
                    left join mreminder as mreminderremainder on (meeting_information.remainder=mreminderremainder.mreminderid) 
                    LEFT OUTER JOIN vendor_account as vendor_accountaccount_name on (meeting_information.account_name=vendor_accountaccount_name.vendoraccid) 
                    left join mconveyance_required as mconveyance_requiredconveyance_required on (meeting_information.conveyance_required=mconveyance_requiredconveyance_required.mconveyance_requiredid) 
                    left join meeting_expence_category as meeting_expence_categoryexpence_category on (meeting_information.expence_category=meeting_expence_categoryexpence_category.expence_category_id) 
                    left join meeting_tax_type as meeting_tax_typetax_type on (meeting_information.tax_type=meeting_tax_typetax_type.tax_type_id) 
                    LEFT JOIN user u2 ON u2.id = meeting_information.creatorid
                    LEFT JOIN user u3 ON u3.id = meeting_information.modifiedby
               where 
                    meeting_information.deleted=0 and 1=1 AND DATE(meeting_information.createdtime) < :today
                    GROUP BY meeting_information.meetinginfo_id order by 
                    meeting_information.meetinginfo_id DESC";
          $meet_stmt = $connection->prepare($meet_sql);
          $meet_stmt->execute(['today' => $today]);
          $meets = $meet_stmt->fetchAll(PDO::FETCH_ASSOC);
          
          // echo "<pre>";print_r($meets);die;
          // --- 3. Resolve autonumber for each record ---
          foreach ($meets as &$call) {
               $tabid = $call['related_to'];
               $relId = $call['related_to_id'];

               if (isset($moduleMeta[$tabid])) {
                    $meta = $moduleMeta[$tabid];
                    $table = $meta['tablename'];
                    $pk    = $meta['tablekeyid'];
                    $col   = $meta['columnname'];

                    // fetch the autonumber value
                    $sql = "SELECT $col FROM $table WHERE $pk = :id LIMIT 1";
                    // $stmt = $pdo->prepare($sql);
                    $stmt = $connection->prepare($sql);
                    $stmt->execute([':id' => $relId]);
                    $autoNum = $stmt->fetchColumn();

                    $call['Related Record'] = $autoNum;
               } else {
                    $call['Related Record'] = null;
               }
          }
          // echo "<pre>";print_r($calls);die;
          $meet_filePath = $directory . "/meetings_detail_$today.csv";
          $meet_fp = fopen($meet_filePath, 'w');
          if (!$meet_fp) {
               throw new Exception("Unable to create or write to the CSV file.");
          }

          
          $meet_headers = ["Meeting Owner","Title","Location","All Day","From","To","Host","Solution Architect","External Participants",
          "Internal Participants","Related Module","Related Record","Repeat","Repeat Type","Participants Reminder",
          "Remainder","Internal Comments", "External Comments","Account Name","From Location",
          "To Location","Confirms","Distance","MOM Shared","Conveyance Required","Description","Expence Category",
          "Expence Type","Tax Type","Expence Date","Submit Approval","Created By","Modified By","Created Time","Last Modified Time"];
          fputcsv($meet_fp, $meet_headers);

        //   echo "<pre>";print_r($meets);die;
          // Data rows
         foreach ($meets as $row) {
            // echo "<pre>";print_r($row);die;
          fputcsv($meet_fp, [
               $row['Meeting Owner'],
               $row['Title'],
               $row['Location'],
               $row['All Day'],
               $row['From'],
               $row['To'],
               $row['Host'],
               $row['Solution Architect'],
               $row['External Participants'],
               $row['Internal Participants'],
               $row['Related Module'],
               $row['Related Record'],
               $row['Repeat'],
               $row['Repeat Type'],
               $row['Participants Reminder'],               
               $row['Remainder'],
               $row['Internal Comments'],
               $row['External Comments'],
               $row['Account Name'],
               $row['From Location'],
               $row['To Location'],
               $row['Confirms'],
               $row['Distance'],
               $row['MOM Shared'],
               $row['Conveyance Required'],
               $row['Description'],
               $row['Expence Category'],
               $row['Expence Type'],
               $row['Tax Type'],
               $row['Expence Date'],
               $row['Submit Approval'],
               $row['Created By'],
               $row['Modified By'],
               $row['Created Time'],
               $row['Modified Time'],
          ]);
          }

          fclose($meet_fp);
          echo "\n CSV file saved to: $meet_filePath";
          /** end meeting module CSV file creation and store into export folder*/
          ///send email


          $mail = new \PHPMailer\PHPMailer\PHPMailer();
          $mail->IsSMTP();
          $mail->Host = SMTP_HOST;
          $mail->Port = SMTP_PORT;
          $mail->SMTPAuth = true;
          $mail->Username = SMTP_USER;
          $mail->Password = SMTP_PASS;
          $to_mail_id = 'arvinder.singh@dwmpl.com';
          // $to_mail_id = 'durgesh.tetra@gmail.com';
          $mail->SMTPSecure = 'tls';     // Enable TLS encryption


          $mail->AddAddress($to_mail_id);


         $mail->AddCC('deepika.tetra@gmail.com');
          // Automatically detect HTTP or HTTPS
          // Detect CLI (cron) vs Browser
         if (php_sapi_name() === 'cli' || defined('STDIN')) {
               // Default values when running via CLI
               $protocol = 'https'; // force https for cron
               $host = 'erp.ditserv.com'; // fallback domain for cron
          } else {
               // Automatically detect HTTP or HTTPS
               $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
               $host = $_SERVER['HTTP_HOST'] ?? 'erp.ditserv.com';
          }

          // Check the domain and set base URL accordingly
          // if ($host === 'erp.ditserv.com' || $host === 'stagerp.ditserv.com') {
               $baseUrl = $protocol . '://' . $host . '/api/exports/';
          // } else {
          //      $baseUrl = $protocol . '://' . $host . '/deshwal/api/exports/';
          // }
          //$baseUrl = $protocol . '://' . $_SERVER['HTTP_HOST'] . '/deshwal/api/exports/';
          // $baseUrl = $protocol . '://' . $_SERVER['HTTP_HOST'] . '/api/exports/';



          // Function to base64 encode the URL
          function encode_url($url)
          {
               return base64_encode($url);
          }

          // File URL (this should be the actual file path)
          $directory = __DIR__ . '/exports';
          $callfilePathprod = $directory . "/calls_detail_$today.csv";  // Dynamic file path
          $meetingfilePathprod = $directory . "/meetings_detail_$today.csv";

          // Base64 encode the file URL
          $callencodedFileUrlprod = encode_url($callfilePathprod);
          $meetingencodedFileUrlprod = encode_url($meetingfilePathprod);
          // if ($host === 'erp.ditserv.com' || $host === 'stagerp.ditserv.com')
               $concaturl = "";
          // else
               // $concaturl = "/deshwal";
          // for dev
          //this code is added to pass arg to link and from cli
          //pass arg in CLI like php script.php h=localhost/deshwal
          if (php_sapi_name() === 'cli') {
               global $argv;
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
          $calldownloadlinkprod = $protocol . '://' . $host . $concaturl . '/downloadsrc?url=' . urlencode($callencodedFileUrlprod);
          $meetingdownloadlinkprod = $protocol . '://' . $host . $concaturl . '/downloadsrc?url=' . urlencode($meetingencodedFileUrlprod);
          // $downloadlinkprod = $protocol . '://' . $_SERVER['HTTP_HOST'] . '/downloadsrc?url=' . urlencode($encodedFileUrlprod);

          $mail->MsgHTML("Hello Team,<br><br>The CSV report for Calls and Meetings has been generated successfully. You can download the files using the following link: <br><a href='$calldownloadlinkprod'>Download Call Details</a><br><a href='$meetingdownloadlinkprod'>Download Meeting Details </a>");


          $mail->SetFrom('erp@Dwmpl.com');
          $mail->isHTML(true);
          $today_dt = date("d/m/Y", strtotime($today));
          // $mail->Subject = "Sourcing deal, Call and Meeting Report - $today_dt";
          $mail->Subject = "Call and Meeting Report - $today_dt";
          // $mail->MsgHTML("Dear user,<br><br>The CSV report for sourcing deals has been generated successfully. You can download the file using the following link:<a href='" . $encodedFileUrl  . "'>Download CSV</a><br>Best regards,<br>Your Team");
          // echo "<br>Final Mail Object=<pre>";
          // print_r($mail);
          if (!$mail->Send()) {
               echo "Mailer Error: " . $mail->ErrorInfo;

               return 0;
          } else
               echo "<br>Mail sent successfully";
          $mailStatus = 1;
          upMailStatus($today, $mailStatus);

     }//end mail


} catch (Exception $e) {
     echo $e->getMessage();
} catch (Error $e) {
     echo $e->getMessage();
}
