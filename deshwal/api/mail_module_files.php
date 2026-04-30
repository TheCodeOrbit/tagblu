<?php
try {
     date_default_timezone_set('Asia/Kolkata');
     $rootDir = dirname(__DIR__);

     require_once($rootDir . '/PHPMailer/src/Exception.php');
     require_once($rootDir . '/PHPMailer/src/PHPMailer.php');
     require_once($rootDir . '/PHPMailer/src/SMTP.php');

     // use PHPMailer\PHPMailer\PHPMailer;
     // use PHPMailer\PHPMailer\Exception;

     require_once("comman.inc.php");
     require_once("params.php");
     require_once("create_module_files.php");
     $connection = db_connect();
     $now = date("Y-m-d H:i:s");
     $today = date("Y-m-d");
     $todayDatetime = date("Y-m-d H:i:s");
     $directory = __DIR__ . '/exports';


     $hour = (int) date('H');  // e.g. 7, 10, 12, 15, 18

     $slotsql = "SELECT * FROM dailyreport_timeslot_codes WHERE report_time = :hr LIMIT 1";
     $slotstmt = $connection->prepare($slotsql);
     $slotstmt->bindValue(':hr', $hour, PDO::PARAM_INT);
     $slotstmt->execute();

     $slotData = $slotstmt->fetch(PDO::FETCH_ASSOC);

     if (!$slotData) {
     die("No valid slot");
     }

     $slot_code = $slotData['timeslot_code'];

     // echo $slot_code;die;
     function isMailSent($date_cond,$slot_code)
     {
          // Assuming db_connect() returns a PDO connection
          $mycon = db_connect();
          $result_count = 1;

          // Correct query without quotes around :date_cond
          $query_mailstatus = "SELECT mail_run_date FROM mail_status WHERE mail_type = 1 AND mail_run_date = :date_cond AND slot_code = :slot_code";

          // Step 2: Prepare the statement
          $stmt = $mycon->prepare($query_mailstatus);

          // Step 3: Bind the parameter
          $stmt->bindValue(':date_cond', $date_cond, PDO::PARAM_STR); // Bind as string
          $stmt->bindValue(':slot_code', $slot_code, PDO::PARAM_STR); // Bind as string

          // Step 4: Execute the query
          $stmt->execute();

          // Step 5: Get the result
          $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch results as associative array

          // Step 6: Count the number of rows
          $result_count = count($result);

          // Return the result count
          return $result_count;
     }

     function checkAllMailStatus($date_cond,$slot_code)
     {
          $mycon = db_connect();

          // Count distinct mail types for the given date
          $query_mailstatus = "
               SELECT COUNT(DISTINCT file_type) AS type_count
               FROM report_files_status
               WHERE file_type IN (1,2,3,4,5,6,7,8,9,10,11)
                    AND file_created_date = :date_cond AND slot_code = :slot_code
          ";

          $stmt = $mycon->prepare($query_mailstatus);
          $stmt->bindValue(':date_cond', $date_cond, PDO::PARAM_STR);
          $stmt->bindValue(':slot_code', $slot_code, PDO::PARAM_STR);
          $stmt->execute();
          $row = $stmt->fetch(PDO::FETCH_ASSOC);

          // Check if we got exactly 11 distinct mail types
          return ($row && $row['type_count'] == 11);
     }

     function upMailStatus($date_cond, $mailStatus,$slot_code)
     {
          // Assuming db_connect() returns a PDO connection
          $mycon = db_connect();

          // Step 1: Prepare the query using PDO's prepare() method
          $query = "INSERT INTO `mail_status` (`mail_run_date`, `mail_type`, `status`, `created_time`, `modified_time`,`slot_code`)
              VALUES (:date_cond, 1, :mailStatus, NOW(), NOW(), :slot_code)";

          // Step 2: Prepare the statement
          $stmt = $mycon->prepare($query);

          // Step 3: Bind the parameters
          $stmt->bindParam(':date_cond', $date_cond, PDO::PARAM_STR);
          $stmt->bindParam(':mailStatus', $mailStatus, PDO::PARAM_STR);
          $stmt->bindParam(':slot_code', $slot_code, PDO::PARAM_STR);

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

     // $todayDatetime =  getCurrentSlotDatetime($time_slots,$todayDatetime);
     deleteOlderFiles($directory);
     
     $result_count = isMailSent($today,$slot_code);
     if ($result_count == 0) 
     {
          if (checkAllMailStatus($today,$slot_code)) 
               {//startmail
                    // Your query with headers in SELECT
               ///send email

                    $mail = new \PHPMailer\PHPMailer\PHPMailer();
                    $mail->IsSMTP();
                    $mail->Host = SMTP_HOST;
                    $mail->Port = SMTP_PORT;
                    $mail->SMTPAuth = true;
                    $mail->Username = SMTP_USER;
                    $mail->Password = SMTP_PASS;
                    $to_mail_id = 'arvinder.singh@dwmpl.com';
                    //$to_mail_id = 'deepika.tetra@gmail.com';
                   
                    // $to_mail_id = 'arvinder.singh@dwmpl.com';
                    //$to_mail_id = 'deepika.tetra@gmail.com';
                    // $to_mail_id = 'durgesh.tetra@gmail.com';
                    $mail->SMTPSecure = 'tls';     // Enable TLS encryption


                     $mail->AddAddress("BI@ditserv.com");
                     $mail->AddAddress("suresh.goel@ditserv.com");
                     $mail->AddAddress("priyanshu.mishra@ditserv.com");
                     $mail->AddAddress($to_mail_id);


                     $mail->AddCC('deepa@tetrain.com');
                     $mail->AddCC('rakeshdubey@tetrain.com');
                     $mail->AddBCC('deepika.tetra@gmail.com');



                    //$mail->AddAddress($to_mail_id);


                    //     $mail->AddCC('deepika.tetra@gmail.com');
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
                    $filepathDatetime = $today."_".$hour;
                    $directory = __DIR__ . '/exports';
                    $sourceingdeal_filepath = $directory . "/sourcingdeal_$filepathDatetime.csv";
                    $product_details_filepath = $directory . "/sourcingdeal_productdetail_$filepathDatetime.csv";
                    $call_filePathprod = $directory . "/calls_detail_$filepathDatetime.csv";  // Dynamic file path
                    $meeting_filePathprod = $directory . "/meetings_detail_$filepathDatetime.csv";
                    $quotes_filePathprod = $directory . "/quotes_detail_$filepathDatetime.csv";  // Dynamic file path
                    $payments_filePathprod = $directory . "/payments_detail_$filepathDatetime.csv";
                    $inspection_filePathprod = $directory . "/inspection_detail_$filepathDatetime.csv";  // Dynamic file path
                    $drilling_filePathprod = $directory . "/drilling_detail_$filepathDatetime.csv";
                    $degaussing_filePathprod = $directory . "/degaussing_detail_$filepathDatetime.csv";  // Dynamic file path
                    $shredding_filePathprod = $directory . "/shredding_detail_$filepathDatetime.csv";
                    $datawiping_filePathprod = $directory . "/datawiping_detail_$filepathDatetime.csv";  // Dynamic file path
                    $pickup_filePathprod = $directory . "/pickup_detail_$filepathDatetime.csv";
                    $oppor_product_filePathprod = $directory . "/opportunity_product_detail_$filepathDatetime.csv";
                    $oppor_shipdetail_filePathprod = $directory . "/opportunity_shiptoaddress_detail_$filepathDatetime.csv";
                    $opporfilePathprod = $directory . "/opportunity_$filepathDatetime.csv";
                    $quotesditfilePathprod = $directory . "/quotesdit_detail_$filepathDatetime.csv";
                    $soditfilePathprod = $directory . "/saleordersdit_detail_$filepathDatetime.csv";
                    $poditfilePathprod = $directory . "/purchaseordersdit_detail_$filepathDatetime.csv";
                    $leadfilePathprod = $directory . "/lead_detail_$filepathDatetime.csv";


                    // Base64 encode the file URL
                    $en_sourceingdeal_filepathprod = encode_url($sourceingdeal_filepath);
                    $en_product_details_filepathprod = encode_url($product_details_filepath);
                    $en_call_filePathprod = encode_url($call_filePathprod);
                    $en_meeting_filePathprod = encode_url($meeting_filePathprod);
                    $en_quotes_filePathprod = encode_url($quotes_filePathprod);
                    $en_payments_filePathprod = encode_url($payments_filePathprod);
                    $en_inspection_filePathprod = encode_url($inspection_filePathprod);
                    $en_drilling_filePathprod = encode_url($drilling_filePathprod);
                    $en_degaussing_filePathprod = encode_url($degaussing_filePathprod);
                    $en_shredding_filePathprod = encode_url($shredding_filePathprod);
                    $en_datawiping_filePathprod = encode_url($datawiping_filePathprod);
                    $en_pickup_filePathprod = encode_url($pickup_filePathprod);
                    $en_oppor_product_filePathprod = encode_url($oppor_product_filePathprod);
                    $en_oppor_shipdetail_filePathprod = encode_url($oppor_shipdetail_filePathprod);
                    $en_opporfilePathprod = encode_url($opporfilePathprod);
                    $en_quotesditfilePathprod = encode_url($quotesditfilePathprod);
                    $en_soditfilePathprod = encode_url($soditfilePathprod);
                    $en_poditfilePathprod = encode_url($poditfilePathprod);
                    $en_leadfilePathprod = encode_url($leadfilePathprod);

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
                    $sourcingdeal_dw_link = $protocol . '://' . $host . $concaturl . '/downloadsrc?url=' . urlencode($en_sourceingdeal_filepathprod);
                    $product_details_dw_link = $protocol . '://' . $host . $concaturl . '/downloadsrc?url=' . urlencode($en_product_details_filepathprod);
                    $call_dw_link = $protocol . '://' . $host . $concaturl . '/downloadsrc?url=' . urlencode($en_call_filePathprod);
                    $meeting_dw_link = $protocol . '://' . $host . $concaturl . '/downloadsrc?url=' . urlencode($en_meeting_filePathprod);
                    $quotes_dw_link = $protocol . '://' . $host . $concaturl . '/downloadsrc?url=' . urlencode($en_quotes_filePathprod);
                    $payments_dw_link = $protocol . '://' . $host . $concaturl . '/downloadsrc?url=' . urlencode($en_payments_filePathprod);
                    $inspection_dw_link = $protocol . '://' . $host . $concaturl . '/downloadsrc?url=' . urlencode($en_inspection_filePathprod);
                    $drilling_dw_link = $protocol . '://' . $host . $concaturl . '/downloadsrc?url=' . urlencode($en_drilling_filePathprod);
                    $degaussing_dw_link = $protocol . '://' . $host . $concaturl . '/downloadsrc?url=' . urlencode($en_degaussing_filePathprod);
                    $shredding_dw_link = $protocol . '://' . $host . $concaturl . '/downloadsrc?url=' . urlencode($en_shredding_filePathprod);
                    $datawiping_dw_link = $protocol . '://' . $host . $concaturl . '/downloadsrc?url=' . urlencode($en_datawiping_filePathprod);
                    $pickup_dw_link = $protocol . '://' . $host . $concaturl . '/downloadsrc?url=' . urlencode($en_pickup_filePathprod);
                    $oppor_dw_link = $protocol . '://' . $host . $concaturl . '/downloadsrc?url=' . urlencode($en_opporfilePathprod);
                    $oppor_shipdetail_dw_link = $protocol . '://' . $host . $concaturl . '/downloadsrc?url=' . urlencode($en_oppor_shipdetail_filePathprod);
                    $oppor_product_dw_link = $protocol . '://' . $host . $concaturl . '/downloadsrc?url=' . urlencode($en_oppor_product_filePathprod);
                    $quotesdit_dw_link = $protocol . '://' . $host . $concaturl . '/downloadsrc?url=' . urlencode($en_quotesditfilePathprod);
                    $sodit_dw_link = $protocol . '://' . $host . $concaturl . '/downloadsrc?url=' . urlencode($en_soditfilePathprod);
                    $podit_dw_link = $protocol . '://' . $host . $concaturl . '/downloadsrc?url=' . urlencode($en_poditfilePathprod);
                    $lead_dw_link = $protocol . '://' . $host . $concaturl . '/downloadsrc?url=' . urlencode($en_leadfilePathprod);

                    // echo "Hello Team,<br><br>The CSV report for Calls and Meetings has been generated successfully. You can download the files using the following link: 
                    //      <br><a href='$sourcingdeal_dw_link'>Download Sourcing Deal Details</a>
                    //      <br><a href='$product_details_dw_link'>Download Sourcing Deal Product Details</a>
                    //      <br><a href='$call_dw_link'>Download Call Details</a>
                    //      <br><a href='$meeting_dw_link'>Download Meeting Details </a>
                    //      <br><a href='$quotes_dw_link'>Download Quotes Details </a>
                    //      <br><a href='$payments_dw_link'>Download Payments Details </a>
                    //      <br><a href='$inspection_dw_link'>Download Inspection Details </a>
                    //      <br><a href='$drilling_dw_link'>Download Drilling Details </a>
                    //      <br><a href='$degaussing_dw_link'>Download Degaussing Details </a>
                    //      <br><a href='$shredding_dw_link'>Download Shredding Details </a>
                    //      <br><a href='$datawiping_dw_link'>Download Data Wiping Details </a>
                    //      <br><a href='$pickup_dw_link'>Download Pickup Details </a>";die;
                    $mail->MsgHTML("Hello Team,<br><br>Daily Report has been generated successfully. You can download the files using the following link:<br> 
                         <br><a href='$sourcingdeal_dw_link'>Download Sourcing Deal Details</a>
                         <br><a href='$product_details_dw_link'>Download Sourcing Deal Product Details</a>
                         <br><a href='$call_dw_link'>Download Call Details</a>
                         <br><a href='$meeting_dw_link'>Download Meeting Details </a>
                         <br><a href='$quotes_dw_link'>Download Quotes Details </a>
                         <br><a href='$payments_dw_link'>Download Payments Details </a>
                         <br><a href='$inspection_dw_link'>Download Inspection Details </a>
                         <br><a href='$drilling_dw_link'>Download Drilling Details </a>
                         <br><a href='$degaussing_dw_link'>Download Degaussing Details </a>
                         <br><a href='$shredding_dw_link'>Download Shredding Details </a>
                         <br><a href='$datawiping_dw_link'>Download Data Wiping Details </a>
                         <br><a href='$pickup_dw_link'>Download Pickup Details </a>
                         <br><a href='$oppor_dw_link'>Download Opportunity Details </a>
                         <br><a href='$oppor_shipdetail_dw_link'>Download Opportunity Ship To Address Details </a>
                         <br><a href='$oppor_product_dw_link'>Download Opportunity Product Details </a>
                         <br><a href='$quotesdit_dw_link'>Download DevIT Quotes Details </a>
                         <br><a href='$sodit_dw_link'>Download DevIT Sales Order Details </a>
                         <br><a href='$podit_dw_link'>Download DevIT Purchase Order Details </a>
                         <br><a href='$lead_dw_link'>Download Lead Details </a>
                         ");


                    $mail->SetFrom('erp@Dwmpl.com');
                    $mail->isHTML(true);
                    $today_dt = date("d/m/Y", strtotime($todayDatetime));
                    // $mail->Subject = "Sourcing deal, Call and Meeting Report - $today_dt";
                    // $mail->Subject = "Test Sourcing Deal, Call, Meeting, Quotes, Payments, Inspection, Drilling, Degaussing, Shredding, Data Wiping and Pickup Report - $today_dt";
                    $mail->Subject = "Deshwal ERP Daily Report (Slot ".date("h A", strtotime($slotData['report_time'] . ":00")).") - ".$today_dt;
                    // $mail->MsgHTML("Dear user,<br><br>The CSV report for sourcing deals has been generated successfully. You can download the file using the following link:<a href='" . $encodedFileUrl  . "'>Download CSV</a><br>Best regards,<br>Your Team");
                    // echo "<br>Final Mail Object=<pre>";
                    // print_r($mail);
                    // $mail->AddAddress('durgesh.tetra@gmail.com');
                    if (!$mail->Send()) {
                         echo "\nMailer Error: " . $mail->ErrorInfo;

                         return 0;
                    } else
                         echo "\nMail sent successfully for today's $slot_code.";
                    $mailStatus = 1;
                    upMailStatus($todayDatetime, $mailStatus,$slot_code);

               }//end mail
          else
          {
               echo "\nSome files are not Generated or Something went wrong.";
          }
     }
     else
     {
          echo "\nMail already sent for today's $slot_code.";
     }


} catch (Exception $e) {
     echo $e->getMessage();
} catch (Error $e) {
     echo $e->getMessage();
}
