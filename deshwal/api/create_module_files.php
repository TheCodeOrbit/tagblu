<?php

date_default_timezone_set('Asia/Kolkata');
// /quotes file creation
/**
 * common code for all
 * 
 * */
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
$todayDatetime = date("Y-m-d H:i:s");
$directory = __DIR__ . '/exports';
$mailStatus = 1;
if (!is_dir($directory)) {
     mkdir($directory, 0777, true);
}

$meta_sql = "SELECT t.tabid, t.tablename, t.tablekeyid, f.columnname, t.tablabel
               FROM tab t
               JOIN field f ON f.tabid = t.tabid
               WHERE f.uitype = 11";
$meta_stmt = $connection->query($meta_sql);
$metaRows = $meta_stmt->fetchAll(PDO::FETCH_ASSOC);

$moduleMeta = [];
foreach ($metaRows as $row) {
     $moduleMeta[$row['tabid']] = $row;
}

$sourceingdealStatus = 1;//1 - sourceing deal
$callStatus = 2;//2 - call
$meetingStatus = 3; //3 - meeting
$quotesStatus = 4; //4 -  quotes
$paymentsStatus = 5; //5 -  payments
$inspectionStatus = 6;//6 - Inspection
$drillingStatus = 7; //7 - Drilling
$degaussingStatus = 8;//8 - Degaussing
$shreddingStatus = 9;//9 - Shredding
$datawipingStatus = 10;//10 - Data Wiping
$pickupStatus = 11; //11 - Pickup
$opporStatus = 12; //12 - opportuntiy
$opporshipStatus = 13;//13 - opportunity shipping detial
$opporproductStatus = 14;//14 - opportunity product detial
$leadStatus = 15;//15 - LEad dEtail
$quotesditStatus = 16; //16 - Quotes DevIT Detail
$soditStatus = 17; //17 - SO DevIT Detail
$poditStatus=18;//18 - PO DevIt Detail
$purchaseRequestStatus = 19; //19 - Purchase Request (Vishwas)

function upFileGenerateMailStatus($slot_code,  $date_cond, $mailStatus, $modulestatus)
{
     // Assuming db_connect() returns a PDO connection
     $mycon = db_connect();

     $query = "INSERT INTO `report_files_status` (`file_created_date`, `file_type`, `slot_code`,`status`, `created_time`, `modified_time`)
              VALUES (:date_cond, :modulestatus, :slot_code , :mailStatus, NOW(), NOW())";

     // Step 2: Prepare the statement
     $stmt = $mycon->prepare($query);

     // Step 3: Bind the parameters
     $stmt->bindParam(':date_cond', $date_cond, PDO::PARAM_STR);
     $stmt->bindParam(':mailStatus', $mailStatus, PDO::PARAM_STR);
     $stmt->bindParam(':slot_code', $slot_code, PDO::PARAM_STR);
     $stmt->bindParam(':modulestatus', $modulestatus, PDO::PARAM_STR);

     // Step 4: Execute the query
     $stmt->execute();
}
function checkMailStatus($slot_code,$date_cond, $modulestatus)
{
     $mycon = db_connect();
     $result_count = 1;
     $query_mailstatus = "SELECT file_created_date FROM report_files_status WHERE file_type = :modulestatus AND file_created_date = :date_cond AND slot_code = :slot_code";
     $stmt = $mycon->prepare($query_mailstatus);
     $stmt->bindValue(':date_cond', $date_cond, PDO::PARAM_STR);
     $stmt->bindValue(':modulestatus', $modulestatus, PDO::PARAM_STR);
     $stmt->bindParam(':slot_code', $slot_code, PDO::PARAM_STR);
     $stmt->execute();
     $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
     $result_count = count($result);
     return $result_count;
}
// echo $todayDatetime;die;

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

$filepathDatetime = $today."_".$hour;
// echo $slot_code;  die;
/**
 * end common code for all
 * */

checkMailStatus($slot_code, $today, $callStatus);
checkMailStatus($slot_code, $today, $meetingStatus);
checkMailStatus($slot_code, $today, $quotesStatus);
checkMailStatus($slot_code, $today, $paymentsStatus);
checkMailStatus($slot_code, $today, $inspectionStatus);
checkMailStatus($slot_code, $today, $drillingStatus);
checkMailStatus($slot_code, $today, $degaussingStatus);
checkMailStatus($slot_code, $today, $shreddingStatus);
checkMailStatus($slot_code, $today, $datawipingStatus);
checkMailStatus($slot_code, $today, $pickupStatus);
checkMailStatus($slot_code, $today, $opporStatus);
checkMailStatus($slot_code, $today, $opporshipStatus);
checkMailStatus($slot_code, $today, $opporproductStatus);
checkMailStatus($slot_code, $today, $leadStatus);
checkMailStatus($slot_code, $today, $quotesditStatus);
checkMailStatus($slot_code, $today, $soditStatus);
checkMailStatus($slot_code, $today, $poditStatus);
checkMailStatus($slot_code, $today, $purchaseRequestStatus);


//=============================================================
//product costing
//=============================================================
$sd_result_count = checkMailStatus($slot_code, $today, $sourceingdealStatus);
echo "\nFile Created count for Sourcing deal=$sd_result_count";
if ($sd_result_count == 0) {
     $pc_sql = "
                    SELECT sourcingdeal.sourcingdeal_no as 'Sourcing Deal No',sourcingdeal.deal_name as 'Sourcing Deal Name',
                         va.acc_name as 'Account Name',
                          va.cust_code as 'Account Code',
                         product_costing.product_costing_no as 'Product Costing No',
                         product_costing.direct_expenses_service_expens as 'Spare Cost',
                         product_costing.marketing_expenses as 'Repair Cost',
                         product_costing.total_quoted_amt_inclusive_gst as 'Total Quoted Amount (Inclusive GST)',
                         product_costing.total_quoted_amt_exclusive_gst as 'Total Quoted Amount (Exclusive GST)',
                         product_costing.total_sp_amount_inclusive_gst as 'Total SP Amount (Inclusive GST)',
                         product_costing.total_sp_amount_exclusive_gst as 'Total SP Amount (exclusive GST)',
                         product_costing_detail.total_logistics_cost as 'Total Logistics Cost',
                         product_costing.total_expence_cost as 'Total Expense Cost',
                         product_costing.margin as 'Total Margin',
                         product_costing.margin_percentage as 'Total Margin %',
                         product_costing.round_off as 'Round Off',
                         product_costing.tcs_percentage as 'TCS%',
                         product_costing.tcs_amount as 'TCS Amount',
                         product_costing.final_quoted_amount_incl_gst as 'Final Quoted Amount (Inclusive GST)',
                         CONCAT(u1.first_name,' ',u1.last_name) as Owner,
                         CONCAT(u2.first_name,' ',u2.last_name) as 'Created By',
                         CONCAT(u3.first_name,' ',u3.last_name) as 'Modified By',
                         product_costing.createdtime as 'Created Datetime',
                         product_costing.modifiedtime as 'Modified Datetime',
                         products.product_name as 'Product',
                         product_costing_detail.category as 'Category',
                         product_costing_detail.subcategory as 'Sub Category',
                         product_costing_detail.vendor1 as Vendor1,
                         product_costing_detail.vendor1_pricing as 'Vendor1 Pricing',
                         product_costing_detail.vendor2 as Vendor2,
                         product_costing_detail.vendor2_pricing as 'Vendor2 Pricing',
                         product_costing_detail.make as Make,
                         product_costing_detail.model_no as Model,
                         vl3.vendor_loc_name as `Pickup location`,
                         vl1.vendor_loc_name as `Billing From Location`,
                         vl2.vendor_loc_name as `Shipping From Location`,
                         w1.warehouse_name as `Bill To Warehouse`,
                         w1.warehouse_name as `Ship To Warehouse`,
                         assetcondition_value as `Asset Condition`,
                         all_accessories_value as `All Accessories`,
                         product_costing_detail.hsn_code as 'HSN Code',
                         product_costing_detail.calculated_sp as 'Suggested SP',
                         product_costing_detail.sp_inclusive_gst as 'SP (Inclusive GST)',
                         product_costing_detail.sp_exclusive_gst as 'SP (Exclusive GST)',
                         product_costing_detail.quoted_price_inclusive_gst as 'Quoted price (Inclusive of GST)',
                         product_costing_detail.quoted_price_gst_exclude as 'Quoted price (GST exclude)',
                         product_costing_detail.margin as 'Margin',
                         product_costing_detail.margin_percentage as 'Margin %',
                         product_costing_detail.quantity_required as 'Quantity Required',
                         product_costing_detail.uom as 'UOM',
                         if(`product_costing_detail` . `no_gst` is not null,if(`product_costing_detail` . `no_gst`=0,'No','Yes'),'') as `No GST`,
                         product_costing_detail.cgst as CGST,
                         product_costing_detail.sgst as SCGST,
                         product_costing_detail.igst as IGST,
                         product_costing_detail.cgst_amount as 'CGST Amount',
                         product_costing_detail.sgst_amount as 'SGST Amount',
                         product_costing_detail.igst_amount as 'IGST Amount',
                         product_costing_detail.total_sp_inclusive_gst as 'Total SP (Inclusive GST)',
                         product_costing_detail.total_sp_exclusive_gst as 'Total SP (Exclusive GST)',
                         product_costing_detail.total_quoted_price_inclusive_gst as 'Total Quoted price (Inclusive GST)',
                         product_costing_detail.total_quoted_price_exclusive_gst as 'Total Quoted price (Exclusive GST)'
               FROM product_costing
               JOIN product_costing_detail 
                    ON product_costing_detail.product_costing_id = product_costing.product_costing_id
               JOIN sourcingdeal 
                    ON sourcingdeal.sourcingdeal_id = product_costing.related_to_id
               JOIN products 
                    ON products.products_id = product_costing_detail.productid
               LEFT JOIN warehouse w1 
                    ON w1.warehouse_id = product_costing_detail.bill_to_warehouse
               LEFT JOIN warehouse w2 
                    ON w2.warehouse_id = product_costing_detail.ship_to_warehouse
               LEFT JOIN vendor_locations vl1 
                    ON vl1.vendorloc_id = product_costing_detail.billing_from_location
               LEFT JOIN vendor_locations vl2 
                    ON vl2.vendorloc_id = product_costing_detail.shipping_from_location
               LEFT JOIN vendor_locations vl3 
                    ON vl3.vendorloc_id = product_costing_detail.pickup_location
               LEFT JOIN po_asset_condition ac 
                    ON ac.assetconditionid = product_costing_detail.asset_condition
               LEFT JOIN prod_detail_all_accessories pdac 
                    ON pdac.all_accessories_id = product_costing_detail.all_accessories
               LEFT JOIN vendor_account va 
                    ON va.vendoraccid = product_costing.vendor_account_name
               LEFT JOIN user u1 
                    ON u1.id = product_costing.ownerid
               LEFT JOIN user u2 
                    ON u2.id = product_costing.creatorid
               LEFT JOIN user u3 
                    ON u3.id = product_costing.modifiedby
               WHERE product_costing.deleted = 0
               --     AND DATE(product_costing.createdtime) < :today
               ORDER BY product_costing.product_costing_id DESC
               ";

     $pc_stmt = $connection->prepare($pc_sql);
     $pc_stmt->execute();

     $pc_filePath = $directory . "/sourcingdeal_productdetail_$filepathDatetime.csv";
     $pc_fp = fopen($pc_filePath, 'w');
     if (!$pc_fp) {
          throw new Exception("Unable to create or write product costing to the CSV file.");
     }

     // Column headers
     $pc_columnCount = $pc_stmt->columnCount();
     $pc_headers = [];
     for ($i = 0; $i < $pc_columnCount; $i++) {
          $meta = $pc_stmt->getColumnMeta($i);
          $pc_headers[] = $meta['name'];
     }
     fputcsv($pc_fp, $pc_headers);

     // Data rows
     while ($row = $pc_stmt->fetch(PDO::FETCH_ASSOC)) {
          fputcsv($pc_fp, $row);
     }

     fclose($pc_fp);
     // echo "\nproduct costing CSV file saved to: $pc_filePath";
      // VERIFY FILE CREATED SUCCESSFULLY
     if (file_exists($pc_filePath) && filesize($pc_filePath) > 0) {
          // upFileGenerateMailStatus($slot_code, $todayDatetime, $mailStatus, $sourceingdealStatus);

          echo "\nProduct costing CSV file saved to: $pc_filePath";
     } else {
          echo "\nError:product costing CSV file not generated or empty. Status not updated for : $pc_filePath";
     }

     //===============================================================
//sourcingdeal 
//==============================================================


     $sd_sql = "select sourcingdeal.sourcingdeal_no as 'Sourcing Deal No',concat(userownerid.first_name,' ',userownerid.last_name) as `Sourcing Deal Owner`,sourcingdeal.deal_name as `Sourcing Deal Name`,DATE_FORMAT(`sourcingdeal`.`closing_date`,'%d-%m-%Y') as `Closure Date`,monthsclosure_month.`months_value` as `Closure Month`,closure_weekclosure_week.`closure_week_value` as `Closure Week`,
          DATE_FORMAT(`sourcingdeal`.`commit_date`,'%d-%m-%Y') as `Commit Date`,monthscommit_month.`months_value` as `Commit Month`,commit_weekclosure_week.`closure_week_value` as `Commit Week`,
          vendor_accountvendor_account_name.acc_name as `Account Name`,
          vendor_accountvendor_account_name.cust_code  as `Account Code`,
          oppr_business_typebusiness_type.`business_type_value` as `Business Type`,contactscontact_name.first_name as `Contact Name`,sourcingdeal.contact_email as `Contact Email`,sourcingdeal.role as `Role`,sourcingdeal.designation as `Designation`,sourcingdeal.department as `Department`,sourcingdeal.contact_mobile as `Contact Mobile`,sourcingdeal.opportunity_tentative_value as `Sourcing Deal Tentative Value`,sourcingdeal_stagestage.`stage_value` as `Stage`,loss_reasonloss_reason.`loss_reason_value` as `Lost Reason`,sourcingdeal.remarks as Remarks,sourcing_payment_typepayment_type.`sourcing_payment_type_value` as `Payment Type`,forecast_categoryforecast_category.`forecast_category_value` as `Forecast Category`,lead_categorycategory.`lead_category_value` as `Category`,sourcingdeal.pickup_request_id as `Pickup Request Id`,currencycurrency.`currency_value` as `Currency`,sourcingdeal.exchange_rate as `Exchange Rate`,sourcingdeal.terms_conditions as `Terms and Conditions`,sd_iscontractis_contract.`iscontract_value` as `IsContract`,type_of_contracttype_of_contract.`type_of_contract_value` as `Type of Contract`,lead_sourcelead_source.`leadsource_value` as `Lead Source`,roleoem.`rolename` as `OEM`,roleoem_manager.`rolename` as `OEM Manager`,concat(useroem_manager_name.first_name,' ',useroem_manager_name.last_name) as `OEM Manager Name`,sourcingdeal.oem_manager_email as `OEM Manager Email`,opportunity_scoreopportunity_score.`opportunity_score_value` as `Opportunity Score`,campaigncampaign_source.campaign_subject as `Campaign Source`,sourcingdeal.probability as Probability,pricing_typepricing_type.`pricing_type_value` as `Pricing Type`,oppr_inspection_requiredinspection_required.`inspection_required_value` as `Inspection Required`,if(`sourcingdeal` . `special_pricing` is not null,if(`sourcingdeal` . `special_pricing`=0,'No','Yes'),'') as `Submit Special Pricing`, if(`sourcingdeal` . `submit_for_pricing` is not null,if(`sourcingdeal` . `submit_for_pricing`=0,'No','Yes'),'') as `Submit For Pricing`, if(`sourcingdeal` . `costing_done` is not null,if(`sourcingdeal` . `costing_done`=0,'No','Yes'),'') as `Costing Done`, if(`sourcingdeal` . `ceo_approval` is not null,if(`sourcingdeal` . `ceo_approval`=0,'No','Yes'),'') as `CEO Approval`, usercreatorid.username as `Created BY`,usermodifiedby.username as  `Last Modified By`,sourcingdeal.createdtime as `Created Time`,sourcingdeal.modifiedtime as `Modified Time`,sourcingdeal.total_sourcing_deal_amount as `Total Sourcng Deal Amount`,sourcingdeal.total_sourcing_deal_cost as `Total Sourcing Deal Cost`,sourcingdeal.total_sourcing_deal_sale as `Total Sourcing Deal Sale`,sourcingdeal.service_sale as `Service Sale`,sourcingdeal.service_cost as `Service Cost`,sourcingdeal.product_cost as `Product Cost`,sourcingdeal.product_sale as `Product Sale`,sourcingdeal.margin as Margin,sourcingdeal.margin_percentage as `Margin%`,
          concat(userdeshwal_isr.first_name,' ',userdeshwal_isr.last_name) as 'Deshwal ISR',
          concat(useraccount_manager.first_name,' ',useraccount_manager.last_name) as 'Account Manager'
          from `sourcingdeal` left join `user` as userownerid on (`sourcingdeal`.ownerid=userownerid.id) 
          left join months as monthsclosure_month on (`sourcingdeal`.`closure_month`=monthsclosure_month.months_id) left join closure_week as closure_weekclosure_week on (`sourcingdeal`.`closure_week`=closure_weekclosure_week.closure_weekid) 
          left join months as monthscommit_month on (`sourcingdeal`.`commit_month`=monthscommit_month.months_id) left join closure_week as commit_weekclosure_week on (`sourcingdeal`.`commit_week`=commit_weekclosure_week.closure_weekid) 
          LEFT OUTER JOIN vendor_account as vendor_accountvendor_account_name on (`sourcingdeal`.vendor_account_name=vendor_accountvendor_account_name.vendoraccid) left join oppr_business_type as oppr_business_typebusiness_type on (`sourcingdeal`.`business_type`=oppr_business_typebusiness_type.business_type_id) LEFT OUTER JOIN contacts as contactscontact_name on (`sourcingdeal`.contact_name=contactscontact_name.contacts_id) left join sourcingdeal_stage as sourcingdeal_stagestage on (`sourcingdeal`.`stage`=sourcingdeal_stagestage.stage_id) left join loss_reason as loss_reasonloss_reason on (`sourcingdeal`.`loss_reason`=loss_reasonloss_reason.loss_reasonid) left join sourcing_payment_type as sourcing_payment_typepayment_type on (`sourcingdeal`.`payment_type`=sourcing_payment_typepayment_type.sourcing_payment_typeid) left join forecast_category as forecast_categoryforecast_category on (`sourcingdeal`.`forecast_category`=forecast_categoryforecast_category.forecast_categoryid) left join lead_category as lead_categorycategory on (`sourcingdeal`.`category`=lead_categorycategory.lead_category_id) left join currency as currencycurrency on (`sourcingdeal`.`currency`=currencycurrency.currencyid) left join sd_iscontract as sd_iscontractis_contract on (`sourcingdeal`.`is_contract`=sd_iscontractis_contract.iscontract_id) left join type_of_contract as type_of_contracttype_of_contract on (`sourcingdeal`.`type_of_contract`=type_of_contracttype_of_contract.type_of_contractid) left join lead_source as lead_sourcelead_source on (`sourcingdeal`.`lead_source`=lead_sourcelead_source.leadsourceid) left join role as roleoem on (`sourcingdeal`.`oem`=roleoem.roleid) left join role as roleoem_manager on (`sourcingdeal`.`oem_manager`=roleoem_manager.roleid) left join `user` as useroem_manager_name on (`sourcingdeal`.oem_manager_name=useroem_manager_name.id) left join opportunity_score as opportunity_scoreopportunity_score on (`sourcingdeal`.`opportunity_score`=opportunity_scoreopportunity_score.opportunity_scoreid) LEFT OUTER JOIN campaign as campaigncampaign_source on (`sourcingdeal`.campaign_source=campaigncampaign_source.campaign_id) left join pricing_type as pricing_typepricing_type on (`sourcingdeal`.`pricing_type`=pricing_typepricing_type.pricing_type_id) left join oppr_inspection_required as oppr_inspection_requiredinspection_required on (`sourcingdeal`.`inspection_required`=oppr_inspection_requiredinspection_required.inspection_required_id) left join `user` as usercreatorid on (`sourcingdeal`.creatorid=usercreatorid.id) left join `user` as usermodifiedby on (`sourcingdeal`.modifiedby=usermodifiedby.id) inner join user as owner on (owner.id=`sourcingdeal`.ownerid)           
          LEFT OUTER JOIN user as userdeshwal_isr on (`sourcingdeal`.deshwal_isr=userdeshwal_isr.id) 
          LEFT OUTER JOIN user as useraccount_manager on (`sourcingdeal`.account_manager=useraccount_manager.id)
          where `sourcingdeal`.deleted=0 and `sourcingdeal`.is_temp = 0 
          -- DATE(sourcingdeal.createdtime) < :today 
          order by `sourcingdeal`.sourcingdeal_id DESC";
     $sd_stmt = $connection->prepare($sd_sql);
     $sd_stmt->execute();
     $sd_filePath = $directory . "/sourcingdeal_$filepathDatetime.csv";
     $sd_fp = fopen($sd_filePath, 'w');
     if (!$sd_fp) {
          throw new Exception("Unable to create or write sourcing deal to the CSV file.");
     }

     // Column headers
     $sd_columnCount = $sd_stmt->columnCount();
     $sd_headers = [];
     for ($i = 0; $i < $sd_columnCount; $i++) {
          $meta = $sd_stmt->getColumnMeta($i);
          $sd_headers[] = $meta['name'];
     }
     fputcsv($sd_fp, $sd_headers);

     // Data rows
     while ($row = $sd_stmt->fetch(PDO::FETCH_ASSOC)) {
          fputcsv($sd_fp, $row);
     }

     fclose($sd_fp);
     // echo "\nsourcing deal CSV file saved to: $sd_filePath";
     // upFileGenerateMailStatus($slot_code, $todayDatetime, $mailStatus, $sourceingdealStatus);//1 - sourcing deal

     // VERIFY FILE CREATED SUCCESSFULLY
     if (file_exists($sd_filePath) && filesize($sd_filePath) > 0) {
          upFileGenerateMailStatus($slot_code, $today, $mailStatus, $sourceingdealStatus);

          echo "\nSourcing deal CSV file saved to: $sd_filePath";
     } else {
          echo "\nError:Sourcing deal CSV file not generated or empty. Status not updated for : $sd_filePath";
     }
}

// ==============================================================
// Call Module code start
//==============================================================

$call_result_count = checkMailStatus($slot_code, $today, $callStatus);
echo "\nFile Created count for call=$call_result_count";
if ($call_result_count == 0) {
     $call_sql = "SELECT call_information.*, tab.tablabel,
                              concat(userownerid.first_name,' ',userownerid.last_name) as 'Call Owner',
                              tab.tablabel as 'Related Module',
                              call_information.call_no as `Call No`,
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
                              vendor_accountaccount_name.acc_name as 'Account Name',
                              vendor_accountaccount_name.cust_code as 'Account Code',
                              call_type_of_engagementtype_of_engagement.typeofengagement_value as `Type of Engagement`
                         FROM call_information 
                         left join user as userownerid on (call_information.ownerid=userownerid.id) 
                         JOIN tab ON call_information.related_to = tab.tabid
                                        left join outgoing_call_status as outgoing_call_statusoutgoing_call_status on (call_information.outgoing_call_status=outgoing_call_statusoutgoing_call_status.outgoingcall_status_id) 
                                        left join call_type as call_typecall_type on (call_information.call_type=call_typecall_type.calltypeid) 
                                        left join call_purpose as call_purposecall_purpose on (call_information.call_purpose=call_purposecall_purpose.callpurposeid) 
                                        left join call_result as call_resultcall_result on (call_information.call_result=call_resultcall_result.callresultid) 
                                        left join user as usercreatorid on (call_information.creatorid=usercreatorid.id) 
                                        left join user as usermodifiedby on (call_information.modifiedby=usermodifiedby.id)
                                        left join call_type_of_engagement as call_type_of_engagementtype_of_engagement on (`call_information`.type_of_engagement=call_type_of_engagementtype_of_engagement.typeofengagement_id) 
                                        inner join user as owner on (owner.id=call_information.ownerid) 
                                        LEFT JOIN user u2 ON u2.id = call_information.creatorid
                                        LEFT JOIN user u3 ON u3.id = call_information.modifiedby
                                        LEFT OUTER JOIN vendor_account as vendor_accountaccount_name on (call_information.account_name=vendor_accountaccount_name.vendoraccid) 
                                        where call_information.deleted=0 and 1=1 
                                        -- DATE(call_information.createdtime) < :today
                                        order by call_information.callinfo_id DESC";
     $call_stmt = $connection->prepare($call_sql);
     $call_stmt->execute();
     $calls = $call_stmt->fetchAll(PDO::FETCH_ASSOC);

     // echo "<pre>";print_r($calls);die;
     // --- 3. Resolve autonumber for each record ---
     foreach ($calls as &$call) {
          $tabid = $call['related_to'];
          $relId = $call['related_to_id'];

          if (isset($moduleMeta[$tabid])) {
               $meta = $moduleMeta[$tabid];
               $table = $meta['tablename'];
               $pk = $meta['tablekeyid'];
               $col = $meta['columnname'];

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
     $call_filePath = $directory . "/calls_detail_$filepathDatetime.csv";
     $call_fp = fopen($call_filePath, 'w');
     if (!$call_fp) {
          throw new Exception("Unable to create or write calls to the CSV file.");
     }


     $call_headers = [
          "Call Owner",
          "Call No",
          "Related Module",
          "Related Record",
          "Account Name",
          "Account Code",
          "Subject",
          "Comment",
          "Outgoing Call Status",
          "Call Start Time",
          "Call End Time",
          "Call Duration",
          "Call Type",
          "Call Purpose",
          "Call Agenda",
          "Call Result",
          "Type of Engagement",
          "Created By",
          "Last Modified By",
          "Created Time",
          "Modified Time"
     ];
     fputcsv($call_fp, $call_headers);

     // Data rows
     foreach ($calls as $row) {
          fputcsv($call_fp, [
               $row['Call Owner'],
               $row['Call No'],
               $row['Related Module'],
               $row['Related Record'],
               $row['Account Name'],
               $row['Account Code'],
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
               $row['Type of Engagement'],
               $row['Created By'],
               $row['Modified By'],
               $row['Created Time'],
               $row['Modified Time'],
          ]);
     }

     fclose($call_fp);
     // echo "\nCall Module CSV file saved to: $call_filePath";
     // upFileGenerateMailStatus($slot_code, $todayDatetime, $mailStatus, $callStatus);//2 - call
     // VERIFY FILE CREATED SUCCESSFULLY
     if (file_exists($call_filePath) && filesize($call_filePath) > 0) {
          upFileGenerateMailStatus($slot_code, $today, $mailStatus, $callStatus);//2 - call

          echo "\nCall Module CSV file saved to: $call_filePath";
     } else {
          echo "\nError: Call Module CSV file not generated or empty. Status not updated for : $call_filePath";
     }
     /**code end for call Module CSV file creation and store into export folder*/
}

//====================================================================
// Meeting Module start from here 
//====================================================================

$meet_result_count = checkMailStatus($slot_code, $today, $meetingStatus);
echo "\nFile Created count for meeting =$meet_result_count";
if ($meet_result_count == 0) {
     $meet_sql = "SELECT meeting_information.*, tab.tablabel,
                              concat(userownerid.first_name,' ',userownerid.last_name) as 'Meeting Owner',
                              meeting_information.title as 'Title',
                              meeting_information.meeting_no as 'Meeting No',
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
                              meeting_information.account_name_manual as 'Account Name Manual',
                              meeting_information.external_participants_manual as 'External Participants Manual',
                              vendor_accountaccount_name.cust_code as 'Account Code',
                              meeting_information.from_location As 'Start Time',
                              meeting_information.to_location as 'End Time',
                              if(meeting_information . confirms is not null,
                              if(meeting_information . confirms=0,'No','Yes'),'') as 'Confirms',
                              if(meeting_information . distance1 is not null,
                              if(meeting_information . distance1=0,'No','Yes'),'') as 'Distance',
                              if(meeting_information . MOM_shared is not null,
                              if(meeting_information . MOM_shared=0,'No','Yes'),'') as 'MOM Shared',
                              mconveyance_requiredconveyance_required.mconveyance_required_value as 'Conveyance Required',
                              meeting_information.description as 'Description',
                              if(meeting_information . submit_approval is not null,
                              if(meeting_information . submit_approval=0,'No','Yes'),'') as 'Submit Approval',
                              CONCAT(u2.first_name,' ',u2.last_name) as 'Created By',
                              CONCAT(u3.first_name,' ',u3.last_name) as 'Modified By',
                              meeting_information.createdtime as 'Created Time',
                              meeting_information.modifiedtime as 'Modified Time',
                              DATE_FORMAT(`mom_date`, '%d-%m-%Y') AS `MOM Date`,
                              DATE_FORMAT(`mom_time`, '%H:%i:%s') AS `MOM Time`,
                              ( SELECT GROUP_CONCAT(DISTINCT CONCAT(ua.first_name,' ',ua.last_name) ORDER BY ua.id SEPARATOR ', ') 
                              FROM user ua 
                              WHERE FIND_IN_SET(ua.id, REPLACE(meeting_information.attendees,' ', '')) ) AS `Attendees`,

                              meeting_information.discussion_points AS `Discussion points`,
                              meeting_information.next_action AS `Next action`,
                              meeting_typetype_of_meeting.meeting_type_value as `Type of Meeting`,
                              meeting_engagement_typetype_of_engagement.engagement_type_value as `Type of Engagement`,
                              meeting_information.expence_type AS 'Expence Type',
                              GROUP_CONCAT(DISTINCT meeting_expence_category.expence_category_value order by meeting_expence_category.expence_category_id ) as 'Expence Category',
                              DATE_FORMAT(`meeting_information`.expence_date,'%d-%m-%Y') as `Expence Date`                             
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
                              left join meeting_type as meeting_typetype_of_meeting on (`meeting_information`.type_of_meeting=meeting_typetype_of_meeting.meeting_type_id)
						left join meeting_engagement_type as meeting_engagement_typetype_of_engagement on (`meeting_information`.type_of_engagement=meeting_engagement_typetype_of_engagement.engagement_type_id) 
                              left join meeting_expence_category on FIND_IN_SET(meeting_expence_category.expence_category_id,REPLACE(`meeting_information`.expence_category, ' ', ''))
                         where 
                              meeting_information.deleted=0 and 1=1 
                              -- DATE(meeting_information.createdtime) < :today
                              GROUP BY meeting_information.meetinginfo_id order by 
                              meeting_information.meetinginfo_id DESC";
     $meet_stmt = $connection->prepare($meet_sql);
     $meet_stmt->execute();
     $meets = $meet_stmt->fetchAll(PDO::FETCH_ASSOC);

     // echo "<pre>";print_r($meets);die;
     // --- 3. Resolve autonumber for each record ---
     foreach ($meets as &$call) {
          $tabid = $call['related_to'];
          $relId = $call['related_to_id'];

          if (isset($moduleMeta[$tabid])) {
               $meta = $moduleMeta[$tabid];
               $table = $meta['tablename'];
               $pk = $meta['tablekeyid'];
               $col = $meta['columnname'];

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
     $meet_filePath = $directory . "/meetings_detail_$filepathDatetime.csv";
     $meet_fp = fopen($meet_filePath, 'w');
     if (!$meet_fp) {
          throw new Exception("Unable to create or write to the Meeting CSV file.");
     }


     $meet_headers = [
          "Meeting No",
          "Meeting Owner",
          "Title",
          "Location",
          "All Day",
          "From",
          "To",
          "Host",
          "Solution Architect",
          "External Participants",
          "External Participants Manual",
          "Internal Participants",
          "Related Module",
          "Related Record",
          "Repeat",
          "Repeat Type",
          "Participants Reminder",
          "Remainder",
          "Internal Comments",
          "External Comments",
          "Account Name",
          "Account Name Manual",
          "Account Code",
          "Start Time",
          "End Time",
          "Confirms",
          "Distance",
          "MOM Shared",
          "Conveyance Required",
          "Description",
          "Expence Category",
          "Expence Type",
          "Tax Type",
          "Expence Date",
          "Submit Approval",
          "MOM Date",
          "MOM Time",
          "Attendees",
          "Discussion points",
          "Next action",
          "Type of Meeting",
          "Type of Engagement",
          "Created By",
          "Modified By",
          "Created Time",
          "Last Modified Time"
     ];
     fputcsv($meet_fp, $meet_headers);

     //   echo "<pre>";print_r($meets);die;
     // Data rows
     foreach ($meets as $row) {
          // echo "<pre>";print_r($row);die;
          fputcsv($meet_fp, [
               $row['Meeting No'],
               $row['Meeting Owner'],
               $row['Title'],
               $row['Location'],
               $row['All Day'],
               $row['From'],
               $row['To'],
               $row['Host'],
               $row['Solution Architect'],
               $row['External Participants'],
               $row['External Participants Manual'],
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
               $row['Account Name Manual'],
               $row['Account Code'],
               $row['Start Time'],
               $row['End Time'],
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
               $row["MOM Date"],
               $row["MOM Time"],
               $row["Attendees"],
               $row["Discussion points"],
               $row["Next action"],
               $row["Type of Meeting"],
               $row["Type of Engagement"],
               $row['Created By'],
               $row['Modified By'],
               $row['Created Time'],
               $row['Modified Time'],
          ]);
     }

     fclose($meet_fp);
     // echo "\nMeeting CSV file saved to: $meet_filePath";
     // upFileGenerateMailStatus($slot_code, $todayDatetime, $mailStatus, $meetingStatus);//3 - meeting
      // VERIFY FILE CREATED SUCCESSFULLY
     if (file_exists($meet_filePath) && filesize($meet_filePath) > 0) {
          upFileGenerateMailStatus($slot_code, $today, $mailStatus, $meetingStatus);//3 - meeting

          echo "\nMeeting CSV file saved to: $meet_filePath";
     } else {
          echo "\nError: Meeting CSV file not generated or empty. Status not updated for : $meet_filePath";
     }
     /** end meeting module CSV file creation and store into export folder*/
}

//=========================================================================
// quotes code start from here
//========================================================================

$quotes_result_count = checkMailStatus($slot_code, $today, $quotesStatus);
echo "\nFile Created count for quotes =$quotes_result_count";
if ($quotes_result_count == 0) {
     $quotesql = "select 
                         concat(userownerid.first_name,' ',userownerid.last_name) as `Owner`,
                         quotes.quotes_no,DATE_FORMAT(`quotes`.`quote_creation_date`,'%d-%m-%Y') as `Quote Creation Date`,quote_stagequote_stage.`quote_stage_value` as `Quote Stage`,
                         qu_payment_termspayment_terms.`payment_terms_value` as `Payment Terms`,
                         currencycurrency.`currency_value` as `Currency`,
                         quotes.exchange_rate as 'Exchange Rate',
                         quotes.gross_profit as 'Gross Profit',
                         quotes.margin_percent as 'Margin Percent',
                         tab.tablabel as 'Related To',
                         quotes.related_to_id as 'related_to_id',
                         quotes.related_to,
                         vendor_accountaccount_name.acc_name as 'Account Name',
                         vendor_accountaccount_name.cust_code as 'Account Code',
                         DATE_FORMAT(`quotes`.`expiry_date`,'%d-%m-%Y') as `Expiry Date`,
                         contactscontact_name.first_name as 'Contact Name',
                         GROUP_CONCAT(po_type_alias.type_value ORDER BY po_type_alias.typeid) AS 'PO Type',
                         quotes.kyc_status as 'KYC Status',
                         contactsbill_legal_name.first_name as 'Legal Name',
                         vendor_locationsbill_name.vendor_loc_name as 'Bill Name',
                         quotes.bill_address as 'Bill Address',
                         quotes.bill_city as 'Bill City',
                         quotes.bill_pincode as 'Bill Pincode',
                         quotes.bill_pan_no as 'Bill PAN No',
                         quotes.bill_state as 'Bill State',
                         quotes.bill_state_code as 'Bill State Code',
                         quotes.bill_gstin_no_uin as 'Bill GSTIN/UIN No',
                         warehousebusiness_entity.warehouse_name as 'Business Entity',
                         quotes.warehouse_address as 'Warehouse Address',
                         quotes.warehouse_state as 'Warehouse State',
                         quotes.warehouse_pincode  as 'Warehouse Pincode',
                         quotes.warehouse_name  as 'Warehouse Name',
                         quotes.warehouse_city as 'Warehouse City',
                         quotes.warehouse_state_code as 'Warehouse State Code',
                         quotes.warehouse_gstin_no  as 'Warehouse GSTIN/UIN No',
                         warehousequ_bill_location_name.warehouse_name as 'Quote Bill Location Name',
                         quotes.qu_bill_address as 'Quote Bill Address',
                         quotes.qu_bill_state as 'Quote Bill State',
                         quotes.qu_bill_pin_code as 'Quote Bill Pin code',
                         quotes.qu_bill_warehouse_name as 'Quote Warehouse Name',
                         quotes.qu_bill_city as 'Quote Bill City',
                         quotes.qu_bill_state_code as 'Quote Bill State Code',
                         quotes.qu_bill_gstin_no as 'Quote Bill GSTIN/UIN No.',
                         quotes.terms_and_conditions as 'Terms and Condition',
                         usercreatorid.username as 'Creatored By',
                         usermodifiedby.username as 'Modified By',
                         quotes.createdtime as 'Created Time',
                         quotes.modifiedtime as 'Modified Time',
                         quotes.basic_cp As 'Basic CP',
                         quotes.total_cgst_amount as 'Total CGST Amount',
                         quotes.total_sgst_amount as 'Total SGST Amount',
                         quotes.total_igst_amount as 'Total IGST Amount',
                         quotes.total_amount as 'Total Amount'
                         from 
                         `quotes` 
                         left join `user` as userownerid on (`quotes`.ownerid=userownerid.id)
                         left join quote_stage as quote_stagequote_stage on (`quotes`.`quote_stage`=quote_stagequote_stage.quote_stageid) left join qu_payment_terms as qu_payment_termspayment_terms on (`quotes`.`payment_terms`=qu_payment_termspayment_terms.payment_termsid) 
                         left join currency as currencycurrency on (`quotes`.`currency`=currencycurrency.currencyid) 
                         LEFT OUTER JOIN `tab` on (`quotes`.related_to= tab.tabid) 
                         LEFT OUTER JOIN vendor_account as vendor_accountaccount_name on (`quotes`.account_name=vendor_accountaccount_name.vendoraccid) 
                         LEFT OUTER JOIN contacts as contactscontact_name on (`quotes`.contact_name=contactscontact_name.contacts_id)
                         LEFT JOIN po_type AS po_type_alias ON FIND_IN_SET(po_type_alias.typeid, `quotes`.po_type) 
                         LEFT OUTER JOIN vendor_locations as vendor_locationsbill_name on (`quotes`.bill_name=vendor_locationsbill_name.vendorloc_id) 
                         LEFT OUTER JOIN warehouse as warehousebusiness_entity on (`quotes`.business_entity=warehousebusiness_entity.warehouse_id) 
                         LEFT OUTER JOIN warehouse as warehousequ_bill_location_name on (`quotes`.qu_bill_location_name=warehousequ_bill_location_name.warehouse_id) 
                         left join `user` as usercreatorid on (`quotes`.creatorid=usercreatorid.id) 
                         left join `user` as usermodifiedby on (`quotes`.modifiedby=usermodifiedby.id) 
                         inner join user as owner on (owner.id=`quotes`.ownerid)
                         LEFT OUTER JOIN contacts as contactsbill_legal_name on (`quotes`.bill_legal_name=contactsbill_legal_name.contacts_id)
                         where `quotes`.deleted=0 and 1=1 
                         -- DATE(quotes.createdtime) < :today
                         GROUP BY `quotes`.quotes_id order by `quotes`.quotes_id DESC";

     $quotestmt = $connection->prepare($quotesql);
     $quotestmt->execute();
     $quotes = $quotestmt->fetchAll(PDO::FETCH_ASSOC);

     // echo "<pre>";print_r($moduleMeta);die;
     // --- 3. Resolve autonumber for each record ---
     foreach ($quotes as &$quote) {
          $tabid = $quote['related_to'];
          $relId = $quote['related_to_id'];
          // echo "1-->".$tabid;die;
          if (isset($moduleMeta[$tabid])) {
               // echo "2";
               $meta = $moduleMeta[$tabid];
               $table = $meta['tablename'];
               $pk = $meta['tablekeyid'];
               $col = $meta['columnname'];

               // fetch the autonumber value
               $sql = "SELECT $col FROM $table WHERE $pk = :id LIMIT 1";
               // echo $sql;die;
               // $stmt = $pdo->prepare($sql);
               $stmt = $connection->prepare($sql);
               $stmt->execute([':id' => $relId]);
               $autoNum = $stmt->fetchColumn();

               $quote['Related Record'] = $autoNum;
          } else {
               $quote['Related Record'] = null;
          }
          // die;
     }
     // echo "<pre>";print_r($quotes);die;
     $quotefilePath = $directory . "/quotes_detail_$filepathDatetime.csv";
     $quotefp = fopen($quotefilePath, 'w');
     if (!$quotefp) {
          throw new Exception("Unable to create or write quotes to the CSV file.");
     }


     $quoteheaders = [
          "Quote Owner",
          "Quote No.",
          "Quote Creation Date",
          "Related Module",
          "Related Record",
          "Account Name",
          "Account Code",
          "Quote Stage",
          "Payment Terms",
          "Currency",
          "Exchange Rate",
          "Gross Profit",
          "Margin Percent",
          "Expiry Date",
          "Contact Name",
          "PO Type",
          "KYC Status",
          "Legal Name",
          "Bill Name",
          "Bill Address",
          "Bill City",
          "Bill Pincode",
          "Bill PAN No",
          "Bill State",
          "Bill State Code",
          "Bill GSTIN/UIN No",
          "Business Entity",
          "Warehouse Name",
          "Warehouse Address",
          "Warehouse State",
          "Warehouse Pincode",
          "Warehouse City",
          "Warehouse State Code",
          "Warehouse GSTIN/UIN No",
          "Quote Bill Location Name",
          "Quote Bill Address",
          "Quote Bill State",
          "Quote Bill Pin code",
          "Quote Warehouse Name",
          "Quote Bill City",
          "Quote Bill State Code",
          "Quote Bill GSTIN/UIN No.",
          "Terms and Condition",
          "Basic CP",
          "Total CGST Amount",
          "Total SGST Amount",
          "Total IGST Amount",
          "Total Amount",
          "Creatored By",
          "Modified By",
          "Created Time",
          "Modified Time"
     ];
     fputcsv($quotefp, $quoteheaders);

     // Data rows

     foreach ($quotes as $row) {
          // echo "<pre>";print_r($row);die;
          fputcsv($quotefp, [
               $row['Owner'],
               $row['quotes_no'],
               $row['Quote Creation Date'],
               $row['Related To'],
               $row['Related Record'],
               $row['Account Name'],
               $row['Account Code'],
               $row['Quote Stage'],
               $row['Payment Terms'],
               $row['Currency'],
               $row['Exchange Rate'],
               $row['Gross Profit'],
               $row['Margin Percent'],
               $row['Expiry Date'],
               $row['Contact Name'],
               $row['PO Type'],
               $row['KYC Status'],
               $row['Legal Name'],
               $row['Bill Name'],
               $row['Bill Address'],
               $row['Bill City'],
               $row['Bill Pincode'],
               $row['Bill PAN No'],
               $row['Bill State'],
               $row['Bill State Code'],
               $row['Bill GSTIN/UIN No'],
               $row['Business Entity'],
               $row['Warehouse Name'],
               $row['Warehouse Address'],
               $row['Warehouse State'],
               $row['Warehouse Pincode'],
               $row['Warehouse City'],
               $row['Warehouse State Code'],
               $row['Warehouse GSTIN/UIN No'],
               $row['Quote Bill Location Name'],
               $row['Quote Bill Address'],
               $row['Quote Bill State'],
               $row['Quote Bill Pin code'],
               $row['Quote Warehouse Name'],
               $row['Quote Bill City'],
               $row['Quote Bill State Code'],
               $row['Quote Bill GSTIN/UIN No.'],
               $row['Terms and Condition'],
               $row['Basic CP'],
               $row['Total CGST Amount'],
               $row['Total SGST Amount'],
               $row['Total IGST Amount'],
               $row['Total Amount'],
               $row['Creatored By'],
               $row['Modified By'],
               $row['Created Time'],
               $row['Modified Time'],
          ]);
     }

     fclose($quotefp);
     // echo "\nQuotes CSV file saved to: $quotefilePath";
     // upFileGenerateMailStatus($slot_code, $todayDatetime, $mailStatus, $quotesStatus);//4 - quotes
     // VERIFY FILE CREATED SUCCESSFULLY
     if (file_exists($quotefilePath) && filesize($quotefilePath) > 0) {
          upFileGenerateMailStatus($slot_code, $today, $mailStatus, $quotesStatus);//4 - quotes

          echo "\nQuotes CSV file saved to: $quotefilePath";
     } else {
          echo "\nError: nQuotes CSV file not generated or empty. Status not updated for : $quotefilePath";
     }
}
//end of quotes

// =======================================================================
// payments
//   =====================================================================
$payments_result_count = checkMailStatus($slot_code, $today, $paymentsStatus);
echo "\nFile Created count for payments =$payments_result_count";
if ($payments_result_count == 0) {
     $paymentql = "select 
               
                         payments.payment_no as 'Payment No',        
                         concat(userownerid.first_name,' ',userownerid.last_name) as `Owner`,
                         usercreatorid.username as 'Created By',
                         usermodifiedby.username as 'Modified By',
                         payments.createdtime as 'Created Time',
                         payments.modifiedtime  as 'Modified Time',
                         payment_typepayment_type.`payment_type_value` as `Payment Type`,
                         sourcingdealsourcing_deal.sourcingdeal_no as 'Sourcing Deal',
                         payments.sourcing_deal_stage as 'Sourcing Deal Stage',
                         payments.account_name as 'Account Name',     
                         va.cust_code as 'Account Code',                         
                         purchase_orderpo.purchase_order_no as 'Purchase Order No',
                         payments.first_comment as 'First Comment',
                         payments.second_comment as 'Second Comment',
                         if(`payments` . `submit_approval` is not null,if(`payments` . `submit_approval`=0,'No','Yes'),'') as `Submit Approval`,
                         payments.bank_name as 'Bank Name',
                         payments.account_number as 'Account Number',
                         payments.swift_code as 'Swift Code',
                         payments.bank_account_name as 'Bank Account Name',
                         payments.bank_idfc_code as 'Bank IFSC_code',
                         payments.payment_bank_name as 'Payment Bank Name',
                         payments.payment_account_number as 'Payment Account Number',
                         payments.payment_swift_code as 'Payment Swift Code',
                         payments.payment_account_name as 'Payment Account Name',
                         payments.idfc_code as 'Payment IFSC code',
                         payments.total_invoice_amount as 'Total Invoice Amount',
                         payments.total_payment_done as 'Total Payment Done',
                         payments.balance_amount as 'Balance Amount',
                         payments.requested_amount as 'Requested Amount',
                         payment_stagestage.`payment_stage_value` as `Stage`
                         from 
                         `payments`
                              left join `user` as usercreatorid on (`payments`.creatorid=usercreatorid.id)
                              left join `user` as usermodifiedby on (`payments`.modifiedby=usermodifiedby.id) 
                              left join `user` as userownerid on (`payments`.ownerid=userownerid.id) 
                              left join payment_type as payment_typepayment_type on (`payments`.`payment_type`=payment_typepayment_type.payment_type_id) 
                              LEFT OUTER JOIN sourcingdeal as sourcingdealsourcing_deal on (`payments`.sourcing_deal=sourcingdealsourcing_deal.sourcingdeal_id) 
                              LEFT OUTER JOIN vendor_account as va on (`va`.	vendoraccid=sourcingdealsourcing_deal.vendor_account_name) 
                              LEFT OUTER JOIN purchase_order as purchase_orderpo on (`payments`.po=purchase_orderpo.purchase_order_id) left join payment_stage as payment_stagestage on (`payments`.`stage`=payment_stagestage.payment_stage_id) inner join user as owner on (owner.id=`payments`.ownerid) 
                              where `payments`.deleted=0 
                              -- DATE(`payments`.createdtime) < :today 
                              order by `payments`.payments_id DESC";

     $paymentstmt = $connection->prepare($paymentql);
     $paymentstmt->execute();

     $paymentfilePath = $directory . "/payments_detail_$filepathDatetime.csv";
     $paymentfp = fopen($paymentfilePath, 'w');
     if (!$paymentfp) {
          throw new Exception("\n Unable to create or write to the payment CSV file.");
     }

     // Column headers
     $payment_columnCount = $paymentstmt->columnCount();
     $pay_headers = [];
     for ($p = 0; $p < $payment_columnCount; $p++) {
          $meta = $paymentstmt->getColumnMeta($p);
          $pay_headers[] = $meta['name'];
     }
     fputcsv($paymentfp, $pay_headers);

     // Data rows
     while ($row = $paymentstmt->fetch(PDO::FETCH_ASSOC)) {
          fputcsv($paymentfp, $row);
     }

     fclose($paymentfp);
     // echo "\n Payments Record CSV file saved to: $paymentfilePath";
     // upFileGenerateMailStatus($slot_code, $todayDatetime, $mailStatus, $paymentsStatus);//5 - Payment
     // VERIFY FILE CREATED SUCCESSFULLY
     if (file_exists($paymentfilePath) && filesize($paymentfilePath) > 0) {
          upFileGenerateMailStatus($slot_code, $today, $mailStatus, $paymentsStatus);//5 - Payment

          echo "\Payments CSV file saved to: $paymentfilePath";
     } else {
          echo "\nError: Payments CSV file not generated or empty. Status not updated for : $paymentfilePath";
     }
}

//   ===========================================================================
//   Inspection
//   ===========================================================================

$inspection_result_count = checkMailStatus($slot_code, $today, $inspectionStatus);
echo "\nFile Created count for Inspection =$inspection_result_count";
if ($inspection_result_count == 0) {
     $inspectionsql = "select 
               
                              inspection.inspection_no as 'Inspection No',                    
                              concat(userownerid.first_name,' ',userownerid.last_name) as `Owner`,
                              usercreatorid.username as 'Created By',
                              inspection.createdtime as 'Created Time',
                              usermodifiedby.username as 'Modified By',
                              inspection.modifiedtime as 'Modified Time',
                              sourcingdealsourcing_deal.sourcingdeal_no as 'Sourcing Deal',
                              vendor_accountaccount_name.acc_name as 'Account Name',
                              vendor_accountaccount_name.cust_code as 'Account Code',
                              contactsspoc_name.first_name as 'Spoc Name',
                              inspection.spoc_number as 'Spoc Number',
                              inspection.spoc_email as 'Spoc Email',
                              DATE_FORMAT(`inspection`.`inspection_preferred_date`,'%d-%m-%Y') as `Inspection Preferred Date`,
                              inspection.inspection_preferred_time as 'Inspection Preferred Time',
                              inspection_stagesstages.`stages_value` as `stages`,
                              if(`inspection` . `submit_for_logistics` is not null,
                              if(`inspection` . `submit_for_logistics`=0,'No','Yes'),'') as `Submit For Logistics`,
                              if(`inspection` . `schedule_inspection` is not null,if(`inspection` . `schedule_inspection`=0,'No','Yes'),'') as `Schedule Inspection`,
                              if(`inspection` . `inspection_started` is not null,
                              if(`inspection` . `inspection_started`=0,'No','Yes'),'') as `Inspection Started`,
                              if(`inspection` . `inspection_completed` is not null,
                              if(`inspection` . `inspection_completed`=0,'No','Yes'),'') as `Inspection Completed`,
                              inspection.pav_hold_by_client_reason as 'PAV Hold by Client Reason',
                              inspection.pav_hold_by_dwmpl_reason as 'PAV Hold by DWMPL Reason',
                              inspection.pav_cancelled_reason as 'PAV Cancelled Reason',
                              DATE_FORMAT(`inspection`.`resume_date`,'%d-%m-%Y') as `Resume Date`,
                              vendor_locationsinspection_location.vendor_loc_name as 'Inspection Location',
                              inspection.location_address as 'Location Address',
                              inspection.location_state as 'Location State',
                              inspection.location_city as 'Location City',
                              inspection.location_pincode as 'Location Pincode',
                              inspection_doneinspection_done_by.`inspection_done_value` as `Inspection Done By`,
                              vendor_accountvendor_name.acc_name as 'Vendor Name',
                              inspection_typeinsection_type.`inspectiontype_value` as `Insection Type`,
                              DATE_FORMAT(`inspection`.`inspection_start_date`,'%d-%m-%Y') as `Inspection Start Date`,
                              DATE_FORMAT(`inspection`.`inpection_completed_date`,'%d-%m-%Y') as `Inpection Completed Date`,
                              inspection.vendor_spoc_number as 'Vendor SPOC Number',
                              DATE_FORMAT(`inspection`.`inspection_schedule_date`,'%d-%m-%Y') as `Inspection Schedule Date`,material_typematerial_type.`material_type_value` as `Material Type`,
                              contactsvendor_spoc_name_done_by_vendor.first_name as 'Vendor SPOC Name Done By Vendor',userlogistics_fe_name_done_by_dwmpl.first_name as 'Logistics fe Name Done By DWMPL',
                              userlogistics_spoc.first_name as 'Logistics Spoc',
                              inspection.logistics_fe_number as 'Logistics fe Number',
                              ins_entry_personnelentry_personnel.`value` as `What are the formalities for entry personnel`,
                              ins_working_timingworking_timings.`value` as `What are the working timings`,
                              ins_inspect_itemslot_get_inspect_item.`value` as `What is the time slot we get to inspect the items`,
                              ins_multi_locationsingle_location_multi_location.`value` as `All items are stored at single location OR Multi location`,
                              inspection.how_many_locations_floor as 'Items are stored at how many locations/Floor',
                              GROUP_CONCAT(ins_protocoal_parameter_alias.value ORDER BY ins_protocoal_parameter_alias.id) AS 'Do we need to follow any security protocoal / parameter.',
                              ins_allowed_faciltiyallowed_at_the_faciltiy.`value` as `Laptop & Mobile phones are allowed at the faciltiy`,
                              ins_allowed_faciltiyitems_which_need_inspect.`value` as `Does facility allow DWMPL to take the images/photograph of the items which need to inspect`,
                              ins_laptop_entry_premiseslaptop_entry_at_the_premises.`value` as `What are the formalities for Laptop entry at the premises`,
                              ins_allowed_faciltiyphysical_verification_of_asset.`value` as `In case if machine is not working, does DWMPL allowed to open the hardware and do the physical verification`,
                              inspection.perform_at_which_floor_area as 'Activity will be perform at which floor/area',
                              ins_allowed_faciltiydesignated_inspection_area.`value` as `Identify and secure designated Inspection area`,
                              ins_allowed_faciltiysufficient_power_supply.`value` as `Does inspection work area having sufficient power supply`,
                              ins_allowed_faciltiysupply_to_laptop_desktop.`value` as `Do you want us to bring the power extention for power supply to Laptop/Desktop`,
                              ins_allowed_faciltiypower_on_the_machines.`value` as `Do we have sufficient Power Cable/Laptop Charges to power on the machines`,
                              ins_allowed_faciltiytools_allowed_inside_premises.`value` as `Does Tools are allowed inside the premises`,
                              ins_allowed_faciltiyvehicle_allowed_parking.`value` as `Vehicle are allowed inside the parking`,
                              ins_formailites_vehicle_entryformailites_vehicle_entry.`value` as `Formailites Vehicle Entry`
                              from 
                              `inspection` 
                              left join `user` as usercreatorid on (`inspection`.creatorid=usercreatorid.id) 
                              left join `user` as usermodifiedby on (`inspection`.modifiedby=usermodifiedby.id) 
                              left join `user` as userownerid on (`inspection`.ownerid=userownerid.id) 
                              LEFT OUTER JOIN sourcingdeal as sourcingdealsourcing_deal on (`inspection`.sourcing_deal=sourcingdealsourcing_deal.sourcingdeal_id) 
                              LEFT OUTER JOIN vendor_account as vendor_accountaccount_name on (`inspection`.account_name=vendor_accountaccount_name.vendoraccid) 
                              LEFT OUTER JOIN contacts as contactsspoc_name on (`inspection`.spoc_name=contactsspoc_name.contacts_id) left join inspection_stages as inspection_stagesstages on (`inspection`.`stages`=inspection_stagesstages.stages_id) 
                              LEFT OUTER JOIN vendor_locations as vendor_locationsinspection_location on (`inspection`.inspection_location=vendor_locationsinspection_location.vendorloc_id) 
                              left join inspection_done as inspection_doneinspection_done_by on (`inspection`.`inspection_done_by`=inspection_doneinspection_done_by.inspection_doneid) 
                              LEFT OUTER JOIN vendor_account as vendor_accountvendor_name on (`inspection`.vendor_name=vendor_accountvendor_name.vendoraccid) 
                              left join inspection_type as inspection_typeinsection_type on (`inspection`.`insection_type`=inspection_typeinsection_type.inspectiontypeid) 
                              left join material_type as material_typematerial_type on (`inspection`.`material_type`=material_typematerial_type.material_type_id) 
                              LEFT OUTER JOIN contacts as contactsvendor_spoc_name_done_by_vendor on (`inspection`.vendor_spoc_name_done_by_vendor=contactsvendor_spoc_name_done_by_vendor.contacts_id) 
                              LEFT OUTER JOIN user as userlogistics_fe_name_done_by_dwmpl on (`inspection`.logistics_fe_name_done_by_dwmpl=userlogistics_fe_name_done_by_dwmpl.id) 
                              LEFT OUTER JOIN user as userlogistics_spoc on (`inspection`.logistics_spoc=userlogistics_spoc.id) 
                              left join ins_entry_personnel as ins_entry_personnelentry_personnel on (`inspection`.`entry_personnel`=ins_entry_personnelentry_personnel.id) 
                              left join ins_working_timing as ins_working_timingworking_timings on (`inspection`.`working_timings`=ins_working_timingworking_timings.id) 
                              left join ins_inspect_item as ins_inspect_itemslot_get_inspect_item on (`inspection`.`slot_get_inspect_item`=ins_inspect_itemslot_get_inspect_item.id) 
                              left join ins_multi_location as ins_multi_locationsingle_location_multi_location on (`inspection`.`single_location_multi_location`=ins_multi_locationsingle_location_multi_location.id) 
                              LEFT JOIN ins_protocoal_parameter AS ins_protocoal_parameter_alias ON FIND_IN_SET(ins_protocoal_parameter_alias.id, `inspection`.security_protocoal_parameter) 
                              left join ins_allowed_faciltiy as ins_allowed_faciltiyallowed_at_the_faciltiy on (`inspection`.`allowed_at_the_faciltiy`=ins_allowed_faciltiyallowed_at_the_faciltiy.id) 
                              left join ins_allowed_faciltiy as ins_allowed_faciltiyitems_which_need_inspect on (`inspection`.`items_which_need_inspect`=ins_allowed_faciltiyitems_which_need_inspect.id) 
                              left join ins_laptop_entry_premises as ins_laptop_entry_premiseslaptop_entry_at_the_premises on (`inspection`.`laptop_entry_at_the_premises`=ins_laptop_entry_premiseslaptop_entry_at_the_premises.id)
                              left join ins_allowed_faciltiy as ins_allowed_faciltiyphysical_verification_of_asset on (`inspection`.`physical_verification_of_asset`=ins_allowed_faciltiyphysical_verification_of_asset.id) 
                              left join ins_allowed_faciltiy as ins_allowed_faciltiydesignated_inspection_area on (`inspection`.`designated_inspection_area`=ins_allowed_faciltiydesignated_inspection_area.id) 
                              left join ins_allowed_faciltiy as ins_allowed_faciltiysufficient_power_supply on (`inspection`.`sufficient_power_supply`=ins_allowed_faciltiysufficient_power_supply.id) 
                              left join ins_allowed_faciltiy as ins_allowed_faciltiysupply_to_laptop_desktop on (`inspection`.`supply_to_laptop_desktop`=ins_allowed_faciltiysupply_to_laptop_desktop.id) 
                              left join ins_allowed_faciltiy as ins_allowed_faciltiypower_on_the_machines on (`inspection`.`power_on_the_machines`=ins_allowed_faciltiypower_on_the_machines.id) 
                              left join ins_allowed_faciltiy as ins_allowed_faciltiytools_allowed_inside_premises on (`inspection`.`tools_allowed_inside_premises`=ins_allowed_faciltiytools_allowed_inside_premises.id) 
                              left join ins_allowed_faciltiy as ins_allowed_faciltiyvehicle_allowed_parking on (`inspection`.`vehicle_allowed_parking`=ins_allowed_faciltiyvehicle_allowed_parking.id) 
                              left join ins_formailites_vehicle_entry as ins_formailites_vehicle_entryformailites_vehicle_entry on (`inspection`.`formailites_vehicle_entry`=ins_formailites_vehicle_entryformailites_vehicle_entry.id) 
                              inner join user as owner on (owner.id=`inspection`.ownerid) 
                              where `inspection`.deleted=0 
                              -- DATE(`inspection`.createdtime) < :today 
                                   GROUP BY `inspection`.inspection_id order by `inspection`.inspection_id DESC";

     $ins_stmt = $connection->prepare($inspectionsql);
     $ins_stmt->execute();

     $ins_filePath = $directory . "/inspection_detail_$filepathDatetime.csv";
     $ins_fp = fopen($ins_filePath, 'w');
     if (!$ins_fp) {
          throw new Exception("\n Unable to create or write to the Inspection CSV file.");
     }

     // Column headers
     $ins_columnCount = $ins_stmt->columnCount();
     $ins_headers = [];
     for ($p = 0; $p < $ins_columnCount; $p++) {
          $meta = $ins_stmt->getColumnMeta($p);
          $ins_headers[] = $meta['name'];
     }
     fputcsv($ins_fp, $ins_headers);

     // Data rows
     while ($row = $ins_stmt->fetch(PDO::FETCH_ASSOC)) {
          fputcsv($ins_fp, $row);
     }

     fclose($ins_fp);
     // echo "\n Inspection Record CSV file saved to: $ins_filePath";
     // upFileGenerateMailStatus($slot_code, $todayDatetime, $mailStatus, $inspectionStatus);//6 - inspection
      // VERIFY FILE CREATED SUCCESSFULLY
     if (file_exists($ins_filePath) && filesize($ins_filePath) > 0) {
          upFileGenerateMailStatus($slot_code, $today, $mailStatus, $inspectionStatus);//6 - inspection

          echo "\Inspection  CSV file saved to: $ins_filePath";
     } else {
          echo "\nError: Inspection  CSV file not generated or empty. Status not updated for : $ins_filePath";
     }
}

//============================================================================
//   Drilling
// =============================================================================
$drilling_result_count = checkMailStatus($slot_code, $today, $drillingStatus);
echo "\nFile Created count for drilling =$drilling_result_count";
if ($drilling_result_count == 0) {
     $drillingsql = "select 
                                        drilling.drilling_no AS 'Drilling No',                              
                                        CONCAT(userownerid.first_name,' ',userownerid.last_name) AS 'Owner',
                                        usercreatorid.username AS 'Created By',
                                        usermodifiedby.username AS 'Modified By',
                                        drilling.createdtime AS 'Created Time',
                                        drilling.modifiedtime AS 'Modified Time',
                                        sourcingdealopportunity_name.sourcingdeal_no AS 'Sourcing Deal Name',
                                        vendor_accountaccount_name.acc_name AS 'Account Name',
                                        vendor_accountaccount_name.cust_code AS 'Account Code',
                                        contactsspoc_name.first_name AS 'SPOC Name',
                                        drilling.spoc_mobile_number AS 'SPOC Mobile Number',
                                        drilling.hdd_count AS 'HDD Count',
                                        drilling.hdd_completed AS 'HDD Completed',
                                        billable_typebillable.billable_type_value AS 'Billable Type',
                                        drilling_statusdrilling_status.drilling_status_value AS 'Drilling Status',
                                        attachmentsimage.name AS 'Image',
                                        pickup_entry_person_formalitiesentry_formalities_person.value AS 'What are the formalities for entry personnel',
                                        drilling.ssd_hdd_stored AS 'At which floor all the SSD/HDD are stored',
                                        drilling.activity_area AS 'Activity will be perform at which floor/area',
                                        pickup_power_supply_area3phase_power_supply.value AS 'Does location area have the 3 phase power supply to run the machine',
                                        drilling.power_socket_machine_location AS 'Distance between the power socket to Machine location',
                                        pickup_power_supply_areamachine_movement.value AS 'Do we have service lift for machine movement',
                                        drilling.lift_timings AS 'What are the lift timings',
                                        pickup_power_supply_areastairs_sufficient_space.value AS 'Does stairs has sufficient space from where we can move the machine/equipment',
                                        --- drilling.movement_activity_floor AS 'How we can do the machine movement to activity floor',
                                        pickup_equipment_working_timingsworking_timings.value AS 'What are the working timings',
                                        pickup_timing_ext_provisionextend_time_provision.value AS 'Do we have any provision to extend the timings',
                                        pickup_procedure_for_extextension_provision.value AS 'What is the procedure to inform/update regarding extension',
                                        pickup_power_supply_arearemoved_devices.value AS 'Does HDD/SSD are removed from the devices',
                                        drill_removed_hdd_ssdremoved_hdd_ssd.value AS 'Who will remove the HDD/SSD from Laptop/Desktop',
                                        pickup_power_supply_arearemoval_hdd.value AS 'Do we have space availbale for removal of HDD',
                                        vendor_locationsactivity_location.vendor_loc_name AS 'Activity Location',
                                        drilling.activity_address AS 'Activity Address',
                                        drilling.activity_city AS 'Activity City',
                                        drilling.activity_state AS 'Activity State',
                                        drilling.activity_pincode AS 'Activity Pincode',
                                        contactsactivtiy_spoc.first_name AS 'Activity SPOC',
                                        drilling.activtiy_spoc_mobile AS 'Activity SPOC Mobile',
                                        drilling.activtiy_spoc_email AS 'Activity SPOC Email',
                                        vendor_locationsbill_location.vendor_loc_name AS 'Bill Location',
                                        drilling.bill_address AS 'Bill Address',
                                        drilling.city AS 'Bill City',
                                        drilling.state AS 'Bill State',
                                        drilling.pincode AS 'Bill Pincode',
                                        drilling.gstin_no AS 'GSTIN No',
                                        contactsbill_spoc.first_name AS 'Bill SPOC',
                                        drilling.bill_spoc_number AS 'Bill SPOC Number',
                                        drilling.bill_spoc_email AS 'Bill SPOC Email',
                                        drilling.billing_amount AS 'Billing Amount',
                                        acc_billing_typebilling_type.billing_type_value AS 'Billing Type',
                                        userlogistic_spoc_name.first_name AS 'Logistic SPOC Name',
                                        drilling.logistic_spoc_number AS 'Logistic SPOC Number',
                                        DATE_FORMAT(drilling.activity_schedule_date,'%d-%m-%Y') AS 'Activity Schedule Date',
                                        DATE_FORMAT(drilling.completed_date,'%d-%m-%Y') AS 'Completed Date',
                                        userfe_name.first_name AS 'FE Name',
                                        drilling.fe_number AS 'FE Number',
                                        hsap_key_requirehsap_key_require.value AS 'HSAP Key Required',
                                        drilling.hsap_count AS 'HSAP Count',
                                        delivery_location_typepickup_location_type.value AS 'Pickup Location Type',
                                        warehousepickup_location.warehouse_name AS 'Pickup Location',
                                        drilling.pickup_location_engineer AS 'Pickup Location Engineer',
                                        vendor_locationspickup_location_client.vendor_loc_name AS 'Pickup Location Client',
                                        drilling.pickup_address AS 'Pickup Address',
                                        drilling.pickup_city AS 'Pickup City',
                                        drilling.pickup_state AS 'Pickup State',
                                        drilling.pickup_pin AS 'Pickup Pincode',
                                        userpickup_spoc.first_name AS 'Pickup SPOC',
                                        drilling.pickup_spoc_number AS 'Pickup SPOC Number',
                                        DATE_FORMAT(drilling.dongle_pickup_date,'%d-%m-%Y') AS 'Dongle Pickup Date',
                                        delivery_conditiondongle_pickup_condition.value AS 'Dongle Pickup Condition',
                                        drilling.hsap_key_serial_num AS 'HSAP Key Serial Number',
                                        attachmentshsap_key_image.name AS 'HSAP Key Image',
                                        courier_listcourrier_name.value AS 'Courier Name',
                                        drilling.docket_number AS 'Docket Number',
                                        DATE_FORMAT(drilling.shipped_date,'%d-%m-%Y') AS 'Shipped Date',
                                        attachmentsgate_pass.name AS 'Gate Pass',
                                        attachmentsdelivery_challan_invoice.name AS 'Delivery Challan Invoice',
                                        delivery_location_typedelivery_location_type.value AS 'Delivery Location Type',
                                        warehousedelivery_location_internal.warehouse_name AS 'Delivery Location Internal',
                                        vendor_locationsdelivery_location_client.vendor_loc_name AS 'Delivery Location Client',
                                        drilling.delivery_location_engineer AS 'Delivery Location Engineer',
                                        drilling.delivery_address AS 'Delivery Address',
                                        drilling.delivery_city AS 'Delivery City',
                                        drilling.delivery_state AS 'Delivery State',
                                        drilling.delivery_pin AS 'Delivery Pincode',
                                        userreceiver_spoc_name.first_name AS 'Receiver SPOC Name',
                                        drilling.receiver_spoc_number AS 'Receiver SPOC Number',
                                        DATE_FORMAT(drilling.delivery_date,'%d-%m-%Y') AS 'Delivery Date',
                                        delivery_conditiondelivery_condition.value AS 'Delivery Condition',
                                        attachmentshsap_key_receipient.name AS 'HSAP Key Recipient',
                                        currencycurrency.currency_value AS 'Currency',
                                        drilling.exchange_rate AS 'Exchange Rate'
                                   from `drilling` 
                                   LEFT OUTER JOIN sourcingdeal as sourcingdealopportunity_name on (`drilling`.opportunity_name=sourcingdealopportunity_name.sourcingdeal_id) 
                                   LEFT OUTER JOIN vendor_account as vendor_accountaccount_name on (`drilling`.account_name=vendor_accountaccount_name.vendoraccid) LEFT OUTER JOIN contacts as contactsspoc_name on (`drilling`.spoc_name=contactsspoc_name.contacts_id) left join billable_type as billable_typebillable on (`drilling`.`billable`=billable_typebillable.billable_type_id) left join drilling_status as drilling_statusdrilling_status on (`drilling`.`drilling_status`=drilling_statusdrilling_status.drilling_statusid) 
                                   LEFT OUTER JOIN `attachments` as attachmentsimage  on (`drilling`.`image`= attachmentsimage.attachmentsid) left join pickup_entry_person_formalities as pickup_entry_person_formalitiesentry_formalities_person on (`drilling`.`entry_formalities_person`=pickup_entry_person_formalitiesentry_formalities_person.id) left join pickup_power_supply_area as pickup_power_supply_area3phase_power_supply on (`drilling`.`3phase_power_supply`=pickup_power_supply_area3phase_power_supply.id) left join pickup_power_supply_area as pickup_power_supply_areamachine_movement on (`drilling`.`machine_movement`=pickup_power_supply_areamachine_movement.id) left join pickup_power_supply_area as pickup_power_supply_areastairs_sufficient_space on (`drilling`.`stairs_sufficient_space`=pickup_power_supply_areastairs_sufficient_space.id) left join pickup_equipment_working_timings as pickup_equipment_working_timingsworking_timings on (`drilling`.`working_timings`=pickup_equipment_working_timingsworking_timings.id) left join pickup_timing_ext_provision as pickup_timing_ext_provisionextend_time_provision on (`drilling`.`extend_time_provision`=pickup_timing_ext_provisionextend_time_provision.id) left join pickup_procedure_for_ext as pickup_procedure_for_extextension_provision on (`drilling`.`extension_provision`=pickup_procedure_for_extextension_provision.id) left join pickup_power_supply_area as pickup_power_supply_arearemoved_devices on (`drilling`.`removed_devices`=pickup_power_supply_arearemoved_devices.id) left join drill_removed_hdd_ssd as drill_removed_hdd_ssdremoved_hdd_ssd on (`drilling`.`removed_hdd_ssd`=drill_removed_hdd_ssdremoved_hdd_ssd.id) left join pickup_power_supply_area as pickup_power_supply_arearemoval_hdd on (`drilling`.`removal_hdd`=pickup_power_supply_arearemoval_hdd.id) LEFT OUTER JOIN vendor_locations as vendor_locationsactivity_location on (`drilling`.activity_location=vendor_locationsactivity_location.vendorloc_id) LEFT OUTER JOIN contacts as contactsactivtiy_spoc on (`drilling`.activtiy_spoc=contactsactivtiy_spoc.contacts_id) LEFT OUTER JOIN vendor_locations as vendor_locationsbill_location on (`drilling`.bill_location=vendor_locationsbill_location.vendorloc_id) LEFT OUTER JOIN contacts as contactsbill_spoc on (`drilling`.bill_spoc=contactsbill_spoc.contacts_id) left join acc_billing_type as acc_billing_typebilling_type on (`drilling`.`billing_type`=acc_billing_typebilling_type.billing_type_id) LEFT OUTER JOIN user as userlogistic_spoc_name on (`drilling`.logistic_spoc_name=userlogistic_spoc_name.id) LEFT OUTER JOIN user as userfe_name on (`drilling`.fe_name=userfe_name.id) left join hsap_key_require as hsap_key_requirehsap_key_require on (`drilling`.`hsap_key_require`=hsap_key_requirehsap_key_require.id) left join delivery_location_type as delivery_location_typepickup_location_type on (`drilling`.`pickup_location_type`=delivery_location_typepickup_location_type.id) LEFT OUTER JOIN warehouse as warehousepickup_location on (`drilling`.pickup_location=warehousepickup_location.warehouse_id) LEFT OUTER JOIN vendor_locations as vendor_locationspickup_location_client on (`drilling`.pickup_location_client=vendor_locationspickup_location_client.vendorloc_id) LEFT OUTER JOIN user as userpickup_spoc on (`drilling`.pickup_spoc=userpickup_spoc.id) left join delivery_condition as delivery_conditiondongle_pickup_condition on (`drilling`.`dongle_pickup_condition`=delivery_conditiondongle_pickup_condition.id) LEFT OUTER JOIN `attachments` as attachmentshsap_key_image  on (`drilling`.`hsap_key_image`= attachmentshsap_key_image.attachmentsid) left join courier_list as courier_listcourrier_name on (`drilling`.`courrier_name`=courier_listcourrier_name.id) LEFT OUTER JOIN `attachments` as attachmentsgate_pass  on (`drilling`.`gate_pass`= attachmentsgate_pass.attachmentsid) LEFT OUTER JOIN `attachments` as attachmentsdelivery_challan_invoice  on (`drilling`.`delivery_challan_invoice`= attachmentsdelivery_challan_invoice.attachmentsid) left join delivery_location_type as delivery_location_typedelivery_location_type on (`drilling`.`delivery_location_type`=delivery_location_typedelivery_location_type.id) LEFT OUTER JOIN warehouse as warehousedelivery_location_internal on (`drilling`.delivery_location_internal=warehousedelivery_location_internal.warehouse_id) LEFT OUTER JOIN vendor_locations as vendor_locationsdelivery_location_client on (`drilling`.delivery_location_client=vendor_locationsdelivery_location_client.vendorloc_id) LEFT OUTER JOIN user as userreceiver_spoc_name on (`drilling`.receiver_spoc_name=userreceiver_spoc_name.id) left join delivery_condition as delivery_conditiondelivery_condition on (`drilling`.`delivery_condition`=delivery_conditiondelivery_condition.id) LEFT OUTER JOIN `attachments` as attachmentshsap_key_receipient  on (`drilling`.`hsap_key_receipient`= attachmentshsap_key_receipient.attachmentsid) left join `user` as userownerid on (`drilling`.ownerid=userownerid.id) left join currency as currencycurrency on (`drilling`.`currency`=currencycurrency.currencyid) left join `user` as usercreatorid on (`drilling`.creatorid=usercreatorid.id) left join `user` as usermodifiedby on (`drilling`.modifiedby=usermodifiedby.id) inner join user as owner on (owner.id=`drilling`.ownerid) where `drilling`.deleted=0  
                                   -- DATE(`drilling`.createdtime) < :today
                                    order by `drilling`.drilling_id DESC";


     $drilling_stmt = $connection->prepare($drillingsql);
     $drilling_stmt->execute();

     $drilling_filePath = $directory . "/drilling_detail_$filepathDatetime.csv";
     $drilling_fp = fopen($drilling_filePath, 'w');
     if (!$drilling_fp) {
          throw new Exception("\n Unable to create or write to the Drilling CSV file.");
     }

     // Column headers
     $drilling_columnCount = $drilling_stmt->columnCount();
     $drilling_headers = [];
     for ($p = 0; $p < $drilling_columnCount; $p++) {
          $meta = $drilling_stmt->getColumnMeta($p);
          $drilling_headers[] = $meta['name'];
     }
     fputcsv($drilling_fp, $drilling_headers);

     // Data rows
     while ($row = $drilling_stmt->fetch(PDO::FETCH_ASSOC)) {
          fputcsv($drilling_fp, $row);
     }

     fclose($drilling_fp);
     // echo "\n Drilling Record CSV file saved to: $drilling_filePath";
     // upFileGenerateMailStatus($slot_code, $todayDatetime, $mailStatus, $drillingStatus);//7 - drilling
      // VERIFY FILE CREATED SUCCESSFULLY
     if (file_exists($drilling_filePath) && filesize($drilling_filePath) > 0) {
          upFileGenerateMailStatus($slot_code, $today, $mailStatus, $drillingStatus);//7 - drilling

          echo "\Drilling  CSV file saved to: $drilling_filePath";
     } else {
          echo "\nError: Drilling  CSV file not generated or empty. Status not updated for : $drilling_filePath";
     }
}
//============================================================================
//   Degaussing
// =============================================================================
$degaussing_result_count = checkMailStatus($slot_code, $today, $degaussingStatus);
echo "\nFile Created count for degaussing =$degaussing_result_count";
if ($degaussing_result_count == 0) {
     $degaussingsql = "select         
                                        degaussing.degaussing_no AS 'Degaussing No',
                                        CONCAT(userownerid.first_name,' ',userownerid.last_name) AS 'Owner',
                                        usercreatorid.username AS 'Created By',
                                        usermodifiedby.username AS 'Modified By',
                                        degaussing.createdtime AS 'Created Time',
                                        degaussing.modifiedtime AS 'Modified Time',
                                        currencycurrency.`currency_value` AS 'Currency',
                                        degaussing.exchange_rate AS 'Exchange Rate',
                                        sourcingdealopportunity_name.sourcingdeal_no AS 'Opportunity Name',
                                        vendor_accountaccount_name.acc_name AS 'Account Name',
                                        vendor_accountaccount_name.cust_code AS 'Account Code',
                                        contactsspoc_name.first_name AS 'SPOC Name',
                                        degaussing.spoc_mobile_number AS 'SPOC Mobile Number',
                                        degaussing.hdd_count AS 'HDD Count',
                                        degaussing.hdd_completed AS 'HDD Completed',
                                        dbillablebillable.`dbillable_value` AS 'Billable',
                                        degaussing_statusdegaussing_status.`degaussing_status_value` AS 'Degaussing Status',
                                        attachmentsimage.name AS 'Image',
                                        vendor_locationsactivity_location.vendor_loc_name AS 'Activity Location',
                                        degaussing.activity_address AS 'Activity Address',
                                        degaussing.activity_city AS 'Activity City',
                                        degaussing.activity_state AS 'Activity State',
                                        degaussing.activity_pincode AS 'Activity Pincode',
                                        contactsactivity_spoc.first_name AS 'Activity SPOC',
                                        degaussing.activity_spoc_mobile AS 'Activity SPOC Mobile',
                                        degaussing.activity_spoc_email AS 'Activity SPOC Email',
                                        vendor_locationsbill_location.vendor_loc_name AS 'Bill Location',
                                        degaussing.bill_address AS 'Bill Address',
                                        degaussing.city AS 'Bill City',
                                        degaussing.state AS 'Bill State',
                                        degaussing.pincode AS 'Bill Pincode',
                                        degaussing.gstin_no_uin AS 'GSTIN/UIN',
                                        contactsbill_spoc.first_name AS 'Bill SPOC',
                                        degaussing.bill_spoc_number AS 'Bill SPOC Number',
                                        degaussing.bill_spoc_email AS 'Bill SPOC Email',
                                        degaussing.billing_amount AS 'Billing Amount',
                                        acc_billing_typebilling_type.`billing_type_value` AS 'Billing Type',
                                        pickup_entry_person_formalitiesentry_formalities_person.`value` AS 'What are the formalities for entry personnel',
                                        degaussing.material_location_floor AS 'At which floor all the SSD/HDD are stored',
                                        degaussing.activity_area AS 'Activity will be perform at which floor/area',
                                        degaussing.secure_degaussing_area AS 'Identify and secure designated degaussing area',
                                        degaussing.proper_ventilation AS 'Ensure workspace is properly ventilated',
                                        degaussing.power_socket_to_machine_distance AS 'Distance between the power socket to Machine location',
                                        pickup_service_liftservice_lift.`value` AS 'Do we have service lift for machine movement',
                                        degaussing.lift_timings AS 'What are the lift timings',
                                        pickup_stairs_spacestairs_area.`value` AS 'Does stairs has sufficient space from where we can move the the machine/equipment',
                                        degaussing.how_to_do_machine_movement AS 'How we can do the machine movement to activity floor',
                                        pickup_equipment_working_timingsworking_timings.`value` AS 'What are the working timings',
                                        pickup_timing_ext_provisionextend_time_provision.`value` AS 'Do we have any provision to extend the timings',
                                        pickup_procedure_for_extextension_provision.`value` AS 'What is the procedure to inform/update regarding extension',
                                        hdd_sdd_removedhdd_sdd_removed_from_device.`value` AS 'Does HDD/SSD are removed from the devices',
                                        who_removes_hdd_sddwho_will_remove_hdd_sdd.`value` AS 'Who will remove the HDD/SSD from Laptop/Desktop',
                                        space_for_hdd_removalspace_available_hdd_removal.`value` AS 'Do we have space available for removal of HDD',
                                        userlogistic_spoc_name.first_name AS 'Logistic SPOC Name',
                                        degaussing.logistic_spoc_number AS 'Logistic SPOC Number',
                                        DATE_FORMAT(`degaussing`.`activity_schedule_date`,'%d-%m-%Y') AS 'Activity Schedule Date',
                                        DATE_FORMAT(`degaussing`.`completed_date`,'%d-%m-%Y') AS 'Completed Date',
                                        userfe_name.first_name AS 'FE Name',
                                        degaussing.fe_number AS 'FE Number',
                                        machine_movementmachine_movement.`machine_movement_value` AS 'Machine Movement',
                                        delivery_location_typepickup_location_type.`value` AS 'Pickup Location Type',
                                        warehousepickup_location.warehouse_name AS 'Pickup Location (Internal Warehouse)',
                                        vendor_locationspickup_location_client.vendor_loc_name AS 'Pickup Location (Client)',
                                        degaussing.pickup_location_engineer AS 'Pickup Location Engineer',
                                        degaussing.pickup_address AS 'Pickup Address',
                                        degaussing.pickup_city AS 'Pickup City',
                                        degaussing.pickup_state AS 'Pickup State',
                                        degaussing.pickup_pin AS 'Pickup Pincode',
                                        userpickup_spoc.first_name AS 'Pickup SPOC',
                                        degaussing.pickup_spoc_number AS 'Pickup SPOC Number',
                                        degaussing.machine_serial_num AS 'Machine Serial Number',
                                        degaussing.machine_model AS 'Machine Model',
                                        attachmentsmachine_image.name AS 'Machine Image',
                                        courier_listcourrier_name.`value` AS 'Courier Name',
                                        degaussing.docket_number AS 'Docket Number',
                                        DATE_FORMAT(`degaussing`.`shipped_date`,'%d-%m-%Y') AS 'Shipped Date',
                                        attachmentsgate_pass.name AS 'Gate Pass',
                                        attachmentsdelivery_challan_invoice.name AS 'Delivery Challan Invoice',
                                        delivery_location_typedelivery_location_type.`value` AS 'Delivery Location Type',
                                        warehousedelivery_location_internal.warehouse_name AS 'Delivery Location (Internal Warehouse)',
                                        vendor_locationsdelivery_location_client.vendor_loc_name AS 'Delivery Location (Client)',
                                        degaussing.delivery_location_engineer AS 'Delivery Location Engineer',
                                        degaussing.delivery_address AS 'Delivery Address',
                                        degaussing.delivery_city AS 'Delivery City',
                                        degaussing.delivery_state AS 'Delivery State',
                                        degaussing.delivery_pin AS 'Delivery Pincode',
                                        userreceiver_spoc_name.first_name AS 'Receiver SPOC Name',
                                        degaussing.receiver_spoc_number AS 'Receiver SPOC Number',
                                        DATE_FORMAT(`degaussing`.`delivery_date`,'%d-%m-%Y') AS 'Delivery Date',
                                        delivery_conditiondelivery_condition.`value` AS 'Delivery Condition',
                                        attachmentsmachine_image_receipient.name AS 'Machine Image Recipient'
                                   from 
                                   `degaussing` 
                                   
                                   left join `user` as usercreatorid on (`degaussing`.creatorid=usercreatorid.id) 
                                   left join `user` as usermodifiedby on (`degaussing`.modifiedby=usermodifiedby.id)                          
                                   left join `user` as userownerid on (`degaussing`.ownerid=userownerid.id) 
                                   left join currency as currencycurrency on (`degaussing`.`currency`=currencycurrency.currencyid) 
                                   LEFT OUTER JOIN sourcingdeal as sourcingdealopportunity_name on (`degaussing`.opportunity_name=sourcingdealopportunity_name.sourcingdeal_id) 
                                   LEFT OUTER JOIN vendor_account as vendor_accountaccount_name on (`degaussing`.account_name=vendor_accountaccount_name.vendoraccid) 
                                   LEFT OUTER JOIN contacts as contactsspoc_name on (`degaussing`.spoc_name=contactsspoc_name.contacts_id) 
                                   left join dbillable as dbillablebillable on (`degaussing`.`billable`=dbillablebillable.dbillableid) 
                                   left join degaussing_status as degaussing_statusdegaussing_status on (`degaussing`.`degaussing_status`=degaussing_statusdegaussing_status.degaussingstatusid) 
                                   LEFT OUTER JOIN `attachments` as attachmentsimage on (`degaussing`.`image`= attachmentsimage.attachmentsid) 
                                   LEFT OUTER JOIN vendor_locations as vendor_locationsactivity_location on (`degaussing`.activity_location=vendor_locationsactivity_location.vendorloc_id) 
                                   LEFT OUTER JOIN contacts as contactsactivity_spoc on (`degaussing`.activity_spoc=contactsactivity_spoc.contacts_id) LEFT OUTER JOIN vendor_locations as vendor_locationsbill_location on (`degaussing`.bill_location=vendor_locationsbill_location.vendorloc_id) 
                                   LEFT OUTER JOIN contacts as contactsbill_spoc on (`degaussing`.bill_spoc=contactsbill_spoc.contacts_id) 
                                   left join acc_billing_type as acc_billing_typebilling_type on (`degaussing`.`billing_type`=acc_billing_typebilling_type.billing_type_id) left join pickup_entry_person_formalities as pickup_entry_person_formalitiesentry_formalities_person on (`degaussing`.`entry_formalities_person`=pickup_entry_person_formalitiesentry_formalities_person.id) 
                                   left join pickup_service_lift as pickup_service_liftservice_lift on (`degaussing`.`service_lift`=pickup_service_liftservice_lift.id) left join pickup_stairs_space as pickup_stairs_spacestairs_area on (`degaussing`.`stairs_area`=pickup_stairs_spacestairs_area.id) 
                                   left join pickup_equipment_working_timings as pickup_equipment_working_timingsworking_timings on (`degaussing`.`working_timings`=pickup_equipment_working_timingsworking_timings.id) 
                                   left join pickup_timing_ext_provision as pickup_timing_ext_provisionextend_time_provision on (`degaussing`.`extend_time_provision`=pickup_timing_ext_provisionextend_time_provision.id)
                                   left join pickup_procedure_for_ext as pickup_procedure_for_extextension_provision on (`degaussing`.`extension_provision`=pickup_procedure_for_extextension_provision.id)
                                   left join hdd_sdd_removed as hdd_sdd_removedhdd_sdd_removed_from_device on (`degaussing`.`hdd_sdd_removed_from_device`=hdd_sdd_removedhdd_sdd_removed_from_device.id) 
                                   left join who_removes_hdd_sdd as who_removes_hdd_sddwho_will_remove_hdd_sdd on (`degaussing`.`who_will_remove_hdd_sdd`=who_removes_hdd_sddwho_will_remove_hdd_sdd.id) 
                                   left join space_for_hdd_removal as space_for_hdd_removalspace_available_hdd_removal on (`degaussing`.`space_available_hdd_removal`=space_for_hdd_removalspace_available_hdd_removal.id) 
                                   LEFT OUTER JOIN user as userlogistic_spoc_name on (`degaussing`.logistic_spoc_name=userlogistic_spoc_name.id)
                                   LEFT OUTER JOIN user as userfe_name on (`degaussing`.fe_name=userfe_name.id) 
                                   left join machine_movement as machine_movementmachine_movement on (`degaussing`.`machine_movement`=machine_movementmachine_movement.machine_movementid) 
                                   left join delivery_location_type as delivery_location_typepickup_location_type on (`degaussing`.`pickup_location_type`=delivery_location_typepickup_location_type.id)
                                   LEFT OUTER JOIN warehouse as warehousepickup_location on (`degaussing`.pickup_location=warehousepickup_location.warehouse_id) LEFT OUTER JOIN vendor_locations as vendor_locationspickup_location_client on (`degaussing`.pickup_location_client=vendor_locationspickup_location_client.vendorloc_id) 
                                   LEFT OUTER JOIN user as userpickup_spoc on (`degaussing`.pickup_spoc=userpickup_spoc.id)
                                        LEFT OUTER JOIN `attachments` as attachmentsmachine_image on (`degaussing`.`machine_image`= attachmentsmachine_image.attachmentsid) 
                                        left join courier_list as courier_listcourrier_name on (`degaussing`.`courrier_name`=courier_listcourrier_name.id) 
                                        LEFT OUTER JOIN `attachments` as attachmentsgate_pass on (`degaussing`.`gate_pass`= attachmentsgate_pass.attachmentsid) 
                                        LEFT OUTER JOIN `attachments` as attachmentsdelivery_challan_invoice on (`degaussing`.`delivery_challan_invoice`= attachmentsdelivery_challan_invoice.attachmentsid) 
                                        left join delivery_location_type as delivery_location_typedelivery_location_type on (`degaussing`.`delivery_location_type`=delivery_location_typedelivery_location_type.id) 
                                        LEFT OUTER JOIN warehouse as warehousedelivery_location_internal on (`degaussing`.delivery_location_internal=warehousedelivery_location_internal.warehouse_id) 
                                        LEFT OUTER JOIN vendor_locations as vendor_locationsdelivery_location_client on (`degaussing`.delivery_location_client=vendor_locationsdelivery_location_client.vendorloc_id) 
                                        LEFT OUTER JOIN user as userreceiver_spoc_name on (`degaussing`.receiver_spoc_name=userreceiver_spoc_name.id) left join delivery_condition as delivery_conditiondelivery_condition on (`degaussing`.`delivery_condition`=delivery_conditiondelivery_condition.id) 
                                        LEFT OUTER JOIN `attachments` as attachmentsmachine_image_receipient on (`degaussing`.`machine_image_receipient`= attachmentsmachine_image_receipient.attachmentsid) 
                                        inner join user as owner on (owner.id=`degaussing`.ownerid) 
                                        where `degaussing`.deleted=0 
                                        -- DATE(`degaussing`.createdtime) < :today
                                         order by `degaussing`.degaussinginfo_id DESC";

     $degaussing_stmt = $connection->prepare($degaussingsql);
     $degaussing_stmt->execute();

     $degaussing_filePath = $directory . "/degaussing_detail_$filepathDatetime.csv";
     $degaussing_fp = fopen($degaussing_filePath, 'w');
     if (!$degaussing_fp) {
          throw new Exception("\n Unable to create or write to the Degaussing CSV file.");
     }

     // Column headers
     $degaussing_columnCount = $degaussing_stmt->columnCount();
     $degaussing_headers = [];
     for ($p = 0; $p < $degaussing_columnCount; $p++) {
          $meta = $degaussing_stmt->getColumnMeta($p);
          $degaussing_headers[] = $meta['name'];
     }
     fputcsv($degaussing_fp, $degaussing_headers);

     // Data rows
     while ($row = $degaussing_stmt->fetch(PDO::FETCH_ASSOC)) {
          fputcsv($degaussing_fp, $row);
     }

     fclose($degaussing_fp);
     // echo "\n Degaussing Record CSV file saved to: $degaussing_filePath";
     // upFileGenerateMailStatus($slot_code, $todayDatetime, $mailStatus, $degaussingStatus);//8 - Degaussing
      // VERIFY FILE CREATED SUCCESSFULLY
     if (file_exists($degaussing_filePath) && filesize($degaussing_filePath) > 0) {
          upFileGenerateMailStatus($slot_code, $today, $mailStatus, $degaussingStatus);//8 - Degaussing

          echo "\Degaussing  CSV file saved to: $degaussing_filePath";
     } else {
          echo "\nError: Degaussing  CSV file not generated or empty. Status not updated for : $degaussing_filePath";
     }
}
//============================================================================
//   Shredding
// =============================================================================
$shredding_result_count = checkMailStatus($slot_code, $today, $shreddingStatus);
echo "\nFile Created count for shredding =$shredding_result_count";
if ($shredding_result_count == 0) {
     $Shreddingsql = "select 
                         shredding.shredding_no AS 'Shredding No',
                         CONCAT(userownerid.first_name,' ',userownerid.last_name) AS 'Owner',
                         usercreatorid.username AS 'Created By',
                         usermodifiedby.username AS 'Modified By',
                         shredding.createdtime AS 'Created Time',
                         shredding.modifiedtime AS 'Modified Time',

                         pickup_entry_person_formalitiesentry_formalities_person.value AS 'What are the formalities for entry personnel',
                         shredding.material_location_floor AS 'At which floor all the SSD/HDD are stored',
                         shredding.activity_area AS 'Activity will be perform at which floor/area',
                         shredding.secure_degaussing_area AS 'Identify and secure designated degaussing area',
                         shredding.proper_ventilation AS 'Ensure workspace is properly ventilated',
                         location_has_pwr_supplytrolley_for_movement.value AS 'Do facility have mobile trolley for machine movement',
                         location_has_pwr_supplypower_supply_area.value AS 'Does location area have the 3 phase power supply to run the machine',
                         shredding.power_socket_to_machine_distance AS 'Distance between the power socket to Machine location',
                         pickup_service_liftservice_lift.value AS 'Do we have service lift for machine movement',
                         shredding.lift_timings AS 'What are the lift timings',
                         pickup_stairs_spacestairs_area.value AS 'Does stairs has sufficient space from where we can move the the machine/equipment',
                         shredding.how_to_do_machine_movement AS 'How we can do the machine movement to activity floor',
                         pickup_equipment_working_timingsworking_timings.value AS 'What are the working timings',
                         pickup_timing_ext_provisionextend_time_provision.value AS 'Do we have any provision to extend the timings',
                         pickup_procedure_for_extextension_provision.value AS 'What is the procedure to inform/update regarding extension',
                         hdd_sdd_removedhdd_sdd_removed_from_device.value AS 'Does HDD/SSD are removed from the devices',
                         who_removes_hdd_sddwho_will_remove_hdd_sdd.value AS 'Who will remove the HDD/SSD from Laptop/Desktop',
                         space_for_hdd_removalspace_available_hdd_removal.value AS 'Do we have space available for removal of HDD',

                         sourcingdealopportunity_name.sourcingdeal_no AS 'Opportunity Name',
                         vendor_accountaccount_name.acc_name AS 'Account Name',
                         vendor_accountaccount_name.cust_code AS 'Account Code',
                         contactsspoc_name.first_name AS 'SPOC Name',
                         shredding.spoc_mobile_number AS 'SPOC Mobile Number',
                         shredding.hdd_count AS 'HDD Count',
                         shredding.hdd_completed AS 'HDD Completed',
                         billable_typebillable.billable_type_value AS 'Billable Type',
                         shredding_statusshredding_status.shredding_status_value AS 'Shredding Status',
                         attachmentsimage.name AS 'Image',

                         currencycurrency.currency_value AS 'Currency',
                         shredding.exchange_rate AS 'Exchange Rate',

                         vendor_locationsbill_location.vendor_loc_name AS 'Bill Location',
                         shredding.bill_address AS 'Bill Address',
                         shredding.city AS 'Bill City',
                         shredding.state AS 'Bill State',
                         shredding.pincode AS 'Bill Pincode',
                         shredding.gstin_no AS 'GSTIN No',
                         contactsbill_spoc.first_name AS 'Bill SPOC',
                         shredding.bill_spoc_number AS 'Bill SPOC Number',
                         shredding.bill_spoc_email AS 'Bill SPOC Email',
                         shredding.billing_amount AS 'Billing Amount',
                         acc_billing_typebilling_type.billing_type_value AS 'Billing Type',

                         vendor_locationsactivity_location.vendor_loc_name AS 'Activity Location',
                         shredding.activity_address AS 'Activity Address',
                         shredding.activity_city AS 'Activity City',
                         shredding.activity_state AS 'Activity State',
                         shredding.activity_pincode AS 'Activity Pincode',
                         contactsactivtiy_spoc.first_name AS 'Activity SPOC',
                         shredding.activtiy_spoc_mobile AS 'Activity SPOC Mobile',
                         shredding.activtiy_spoc_email AS 'Activity SPOC Email',

                         delivery_location_typepickup_location_type.value AS 'Pickup Location Type',
                         warehousepickup_location.warehouse_name AS 'Pickup Location',
                         shredding.pickup_location_engineer AS 'Pickup Location Engineer',
                         vendor_locationspickup_location_client.vendor_loc_name AS 'Pickup Location Client',
                         shredding.pickup_address AS 'Pickup Address',
                         shredding.pickup_city AS 'Pickup City',
                         shredding.pickup_state AS 'Pickup State',
                         shredding.pickup_pin AS 'Pickup Pincode',
                         userpickup_spoc.first_name AS 'Pickup SPOC',
                         shredding.pickup_spoc_number AS 'Pickup SPOC Number',

                         DATE_FORMAT(shredding.dongle_pickup_date,'%d-%m-%Y') AS 'Dongle Pickup Date',
                         delivery_conditiondongle_pickup_condition.value AS 'Dongle Pickup Condition',
                         shredding.hsap_key_serial_num AS 'HSAP Key Serial Number',
                         attachmentshsap_key_image.name AS 'HSAP Key Image',
                         courier_listcourrier_name.value AS 'Courier Name',
                         shredding.docket_number AS 'Docket Number',
                         DATE_FORMAT(shredding.shipped_date,'%d-%m-%Y') AS 'Shipped Date',
                         attachmentsgate_pass.name AS 'Gate Pass',
                         attachmentsdelivery_challan_invoice.name AS 'Delivery Challan Invoice',

                         delivery_location_typedelivery_location_type.value AS 'Delivery Location Type',
                         warehousedelivery_location_internal.warehouse_name AS 'Delivery Location Internal',
                         vendor_locationsdelivery_location_client.vendor_loc_name AS 'Delivery Location Client',
                         shredding.delivery_location_engineer AS 'Delivery Location Engineer',
                         shredding.delivery_address AS 'Delivery Address',
                         shredding.delivery_city AS 'Delivery City',
                         shredding.delivery_state AS 'Delivery State',
                         shredding.delivery_pin AS 'Delivery Pincode',
                         userreceiver_spoc_name.first_name AS 'Receiver SPOC Name',
                         shredding.receiver_spoc_number AS 'Receiver SPOC Number',
                         DATE_FORMAT(shredding.delivery_date,'%d-%m-%Y') AS 'Delivery Date',
                         delivery_conditiondelivery_condition.value AS 'Delivery Condition',
                         attachmentshsap_key_receipient.name AS 'HSAP Key Recipient',

                         userlogistic_spoc_name.first_name AS 'Logistic SPOC Name',
                         shredding.logistic_spoc_number AS 'Logistic SPOC Number',
                         DATE_FORMAT(shredding.activity_schedule_date,'%d-%m-%Y') AS 'Activity Schedule Date',
                         DATE_FORMAT(shredding.completed_date,'%d-%m-%Y') AS 'Completed Date',
                         userfe_name.first_name AS 'FE Name',
                         shredding.fe_number AS 'FE Number',
                         hsap_key_requirehsap_key_require.value AS 'HSAP Key Required',
                         shredding.hsap_count AS 'HSAP Count'
                    from 
                         `shredding` 
                         left join `user` as usercreatorid on (`shredding`.creatorid=usercreatorid.id) 
                         left join `user` as usermodifiedby on (`shredding`.modifiedby=usermodifiedby.id) 
                         left join pickup_entry_person_formalities as pickup_entry_person_formalitiesentry_formalities_person on (`shredding`.`entry_formalities_person`=pickup_entry_person_formalitiesentry_formalities_person.id) 
                         left join location_has_pwr_supply as location_has_pwr_supplytrolley_for_movement on (`shredding`.`trolley_for_movement`=location_has_pwr_supplytrolley_for_movement.id) 
                         left join location_has_pwr_supply as location_has_pwr_supplypower_supply_area on (`shredding`.`power_supply_area`=location_has_pwr_supplypower_supply_area.id) 
                         left join pickup_service_lift as pickup_service_liftservice_lift on (`shredding`.`service_lift`=pickup_service_liftservice_lift.id) 
                         left join pickup_stairs_space as pickup_stairs_spacestairs_area on (`shredding`.`stairs_area`=pickup_stairs_spacestairs_area.id) left join pickup_equipment_working_timings as pickup_equipment_working_timingsworking_timings on (`shredding`.`working_timings`=pickup_equipment_working_timingsworking_timings.id) 
                         left join pickup_timing_ext_provision as pickup_timing_ext_provisionextend_time_provision on (`shredding`.`extend_time_provision`=pickup_timing_ext_provisionextend_time_provision.id) 
                         left join pickup_procedure_for_ext as pickup_procedure_for_extextension_provision on (`shredding`.`extension_provision`=pickup_procedure_for_extextension_provision.id) 
                         left join hdd_sdd_removed as hdd_sdd_removedhdd_sdd_removed_from_device on (`shredding`.`hdd_sdd_removed_from_device`=hdd_sdd_removedhdd_sdd_removed_from_device.id) 
                         left join who_removes_hdd_sdd as who_removes_hdd_sddwho_will_remove_hdd_sdd on (`shredding`.`who_will_remove_hdd_sdd`=who_removes_hdd_sddwho_will_remove_hdd_sdd.id) 
                         left join space_for_hdd_removal as space_for_hdd_removalspace_available_hdd_removal on (`shredding`.`space_available_hdd_removal`=space_for_hdd_removalspace_available_hdd_removal.id) 
                         LEFT OUTER JOIN sourcingdeal as sourcingdealopportunity_name on (`shredding`.opportunity_name=sourcingdealopportunity_name.sourcingdeal_id) 
                         LEFT OUTER JOIN vendor_account as vendor_accountaccount_name on (`shredding`.account_name=vendor_accountaccount_name.vendoraccid) 
                         LEFT OUTER JOIN contacts as contactsspoc_name on (`shredding`.spoc_name=contactsspoc_name.contacts_id) 
                         left join billable_type as billable_typebillable on (`shredding`.`billable`=billable_typebillable.billable_type_id) 
                         left join shredding_status as shredding_statusshredding_status on (`shredding`.`shredding_status`=shredding_statusshredding_status.shredding_statusid) 
                         LEFT OUTER JOIN `attachments` as attachmentsimage on (`shredding`.`image`= attachmentsimage.attachmentsid) left join `user` as userownerid on (`shredding`.ownerid=userownerid.id) 
                         left join currency as currencycurrency on (`shredding`.`currency`=currencycurrency.currencyid) 
                         LEFT OUTER JOIN vendor_locations as vendor_locationsbill_location on (`shredding`.bill_location=vendor_locationsbill_location.vendorloc_id) 
                         LEFT OUTER JOIN contacts as contactsbill_spoc on (`shredding`.bill_spoc=contactsbill_spoc.contacts_id) 
                         left join acc_billing_type as acc_billing_typebilling_type on (`shredding`.`billing_type`=acc_billing_typebilling_type.billing_type_id) LEFT OUTER JOIN vendor_locations as vendor_locationsactivity_location on (`shredding`.activity_location=vendor_locationsactivity_location.vendorloc_id) 
                         LEFT OUTER JOIN contacts as contactsactivtiy_spoc on (`shredding`.activtiy_spoc=contactsactivtiy_spoc.contacts_id) 
                         left join delivery_location_type as delivery_location_typepickup_location_type on (`shredding`.`pickup_location_type`=delivery_location_typepickup_location_type.id) 
                         LEFT OUTER JOIN warehouse as warehousepickup_location on (`shredding`.pickup_location=warehousepickup_location.warehouse_id) LEFT OUTER JOIN vendor_locations as vendor_locationspickup_location_client on (`shredding`.pickup_location_client=vendor_locationspickup_location_client.vendorloc_id) 
                         LEFT OUTER JOIN user as userpickup_spoc on (`shredding`.pickup_spoc=userpickup_spoc.id) 
                         left join delivery_condition as delivery_conditiondongle_pickup_condition on (`shredding`.`dongle_pickup_condition`=delivery_conditiondongle_pickup_condition.id) 
                         LEFT OUTER JOIN `attachments` as attachmentshsap_key_image on (`shredding`.`hsap_key_image`= attachmentshsap_key_image.attachmentsid) 
                         left join courier_list as courier_listcourrier_name on (`shredding`.`courrier_name`=courier_listcourrier_name.id) 
                         LEFT OUTER JOIN `attachments` as attachmentsgate_pass on (`shredding`.`gate_pass`= attachmentsgate_pass.attachmentsid) 
                         LEFT OUTER JOIN `attachments` as attachmentsdelivery_challan_invoice on (`shredding`.`delivery_challan_invoice`= attachmentsdelivery_challan_invoice.attachmentsid) 
                         left join delivery_location_type as delivery_location_typedelivery_location_type on (`shredding`.`delivery_location_type`=delivery_location_typedelivery_location_type.id) 
                         LEFT OUTER JOIN warehouse as warehousedelivery_location_internal on (`shredding`.delivery_location_internal=warehousedelivery_location_internal.warehouse_id) 
                         LEFT OUTER JOIN vendor_locations as vendor_locationsdelivery_location_client on (`shredding`.delivery_location_client=vendor_locationsdelivery_location_client.vendorloc_id) 
                         LEFT OUTER JOIN user as userreceiver_spoc_name on (`shredding`.receiver_spoc_name=userreceiver_spoc_name.id) left join delivery_condition as delivery_conditiondelivery_condition on (`shredding`.`delivery_condition`=delivery_conditiondelivery_condition.id) 
                         LEFT OUTER JOIN `attachments` as attachmentshsap_key_receipient on (`shredding`.`hsap_key_receipient`= attachmentshsap_key_receipient.attachmentsid) 
                         LEFT OUTER JOIN user as userlogistic_spoc_name on (`shredding`.logistic_spoc_name=userlogistic_spoc_name.id) 
                         LEFT OUTER JOIN user as userfe_name on (`shredding`.fe_name=userfe_name.id) left join hsap_key_require as hsap_key_requirehsap_key_require on (`shredding`.`hsap_key_require`=hsap_key_requirehsap_key_require.id) 
                         inner join user as owner on (owner.id=`shredding`.ownerid) where `shredding`.deleted=0 
                         -- DATE(`shredding`.createdtime) < :today
                          order by `shredding`.shredding_id DESC";

     $shredding_stmt = $connection->prepare($Shreddingsql);
     $shredding_stmt->execute();

     $shredding_filePath = $directory . "/shredding_detail_$filepathDatetime.csv";
     $shredding_fp = fopen($shredding_filePath, 'w');
     if (!$shredding_fp) {
          throw new Exception("\n Unable to create or write to the Shredding CSV file.");
     }

     // Column headers
     $shredding_columnCount = $shredding_stmt->columnCount();
     $shredding_headers = [];
     for ($p = 0; $p < $shredding_columnCount; $p++) {
          $meta = $shredding_stmt->getColumnMeta($p);
          $shredding_headers[] = $meta['name'];
     }
     fputcsv($shredding_fp, $shredding_headers);

     // Data rows
     while ($row = $shredding_stmt->fetch(PDO::FETCH_ASSOC)) {
          fputcsv($shredding_fp, $row);
     }

     fclose($shredding_fp);
     // echo "\n Shredding Record CSV file saved to: $shredding_filePath";
     // upFileGenerateMailStatus($slot_code, $todayDatetime, $mailStatus, $shreddingStatus);//9 - Shredding
      // VERIFY FILE CREATED SUCCESSFULLY
     if (file_exists($shredding_filePath) && filesize($shredding_filePath) > 0) {
          upFileGenerateMailStatus($slot_code, $today, $mailStatus, $shreddingStatus);//9 - Shredding

          echo "\Shredding  CSV file saved to: $shredding_filePath";
     } else {
          echo "\nError: Shredding  CSV file not generated or empty. Status not updated for : $shredding_filePath";
     }
}
//============================================================================
//   Data Wiping
// =============================================================================
$datawiping_result_count = checkMailStatus($slot_code, $today, $datawipingStatus);
echo "\nFile Created count for datawiping =$datawiping_result_count";
if ($datawiping_result_count == 0) {
     $datawipingsql = "select 
                    
                         data_wiping.data_wiping_no  as 'Data Wiping No',
                         concat(userownerid.first_name,' ',userownerid.last_name) as `Owner`,
                         usercreatorid.username as 'Created By',
                         usermodifiedby.username as 'Modified By',
                         data_wiping.createdtime  as 'Created Time',
                         data_wiping.modifiedtime as 'Modified Time',
                         sourcingdealopportunity_name.sourcingdeal_no as 'Opportunity Name',
                         vendor_accountaccount_name.acc_name as 'Account Name', 
                         vendor_accountaccount_name.cust_code as 'Account Code', 
                         contactsspoc_name.first_name as 'SPOC Name',
                         data_wiping.spoc_mobile_number as 'SPOC Mobile Number',
                         data_wiping.hdd_count as 'HDD Count',
                         data_wiping.hdd_completed as 'HDD Completed',
                         billable_typebillable.`billable_type_value` as `Billable`,
                         wiping_statuswiping_status.`wiping_status_value` as `Wiping Status`
                         ,currencycurrency.`currency_value` as `Currency`,
                         data_wiping.exchange_rate as 'Exchange Rate',
                         vendor_locationsbill_location.vendor_loc_name as 'Bill Location',
                         data_wiping.bill_address as 'Bill Address',
                         data_wiping.city as 'City',
                         data_wiping.state as 'State',
                         data_wiping.pincode as 'Pincode',
                         data_wiping.gstin_no as 'Gstin No',
                         contactsbill_spoc.first_name as 'Bill SPOC',
                         data_wiping.bill_spoc_number as 'Bill SPOC Number',
                         data_wiping.bill_spoc_email as 'Bill SPOC Email',
                         data_wiping.billing_amount as 'Billing Amount',
                         acc_billing_typebilling_type.`billing_type_value` as `Billing Type`,
                         vendor_locationsactivity_location.vendor_loc_name as 'Activity Location',
                         data_wiping.activity_address as 'Activity Address',
                         data_wiping.activity_city as 'Activity City',
                         data_wiping.activity_state as 'Activity State',
                         data_wiping.activity_pincode AS 'Activity Pincode',
                              contactsactivtiy_spoc.first_name AS 'Activity SPOC',
                              data_wiping.activtiy_spoc_mobile AS 'Activity SPOC Mobile',
                              data_wiping.activtiy_spoc_email AS 'Activity SPOC Email',
                              pickup_entry_person_formalitiesentry_formalities_person.`value` AS 'What are the formalities for entry personnel',
                              data_wiping.material_location_floor AS 'Material stored at which location/floor',
                              data_wiping.activity_area AS 'Activity will be perform at which floor/area',
                              pickup_power_supply_areapower_supply_area.`value` AS 'Identify the secure designated area with enough power socket along with power supply',
                              pickup_wifi_servicewifi_service.`value` AS 'Do we have open/Guest WIFI service available',
                              data_wiping.machine_plug AS 'How many machine\'s can we plug in at single point of time',
                              data_wiping.num_of_days AS 'No of days to complete the activity',
                              pickup_equipment_working_timingsworking_timings.`value` AS 'What are the working timings',
                              pickup_timing_ext_provisionextend_time_provision.`value` AS 'Do we have any provision to extend the timings',
                              pickup_procedure_for_extextension_provision.`value` AS 'What is the procedure to inform/update regarding extension',
                              data_wiping.machines_num_working_condition AS 'How many machines are in working conditions',
                              data_wiping.hdd_count_loose_unorganized AS 'How many hard disk drives (HDDs) are currently stored or found in loose or unorganized conditions',
                              data_wiping.power_extension_provision AS 'Power extension provision in case power sockets are not available',
                              delivery_location_typepickup_location_type.`value` AS 'Pickup Location Type',
                              warehousepickup_location.warehouse_name AS 'Pickup Location (Internal Warehouse)',
                              vendor_locationspickup_location_client.vendor_loc_name AS 'Pickup Location (Client)',
                              data_wiping.pickup_location_engineer AS 'Pickup Location Engineer',
                              data_wiping.pickup_address AS 'Pickup Address',
                              data_wiping.pickup_city AS 'Pickup City',
                              data_wiping.pickup_state AS 'Pickup State',
                              data_wiping.pickup_pin AS 'Pickup Pincode',
                              userpickup_spoc.first_name AS 'Pickup SPOC',
                              data_wiping.pickup_spoc_number AS 'Pickup SPOC Number',
                              DATE_FORMAT(`data_wiping`.`dongle_pickup_date`,'%d-%m-%Y') AS 'Dongle Pickup Date',
                              delivery_conditiondongle_pickup_condition.`value` AS 'Dongle Pickup Condition',
                              data_wiping.hsap_key_serial_num AS 'HSAP Key Serial Number',
                              attachmentshsap_key_image.name AS 'HSAP Key Image',
                              courier_listcourrier_name.`value` AS 'Courier Name',
                              data_wiping.docket_number AS 'Docket Number',
                              DATE_FORMAT(`data_wiping`.`shipped_date`,'%d-%m-%Y') AS 'Shipped Date',
                              attachmentsgate_pass.name AS 'Gate Pass',
                              attachmentsdelivery_challan_invoice.name AS 'Delivery Challan Invoice',
                              delivery_location_typedelivery_location_type.`value` AS 'Delivery Location Type',
                              warehousedelivery_location_internal.warehouse_name AS 'Delivery Location (Internal Warehouse)',
                              vendor_locationsdelivery_location_client.vendor_loc_name AS 'Delivery Location (Client)',
                              data_wiping.delivery_location_engineer AS 'Delivery Location Engineer',
                              data_wiping.delivery_address AS 'Delivery Address',
                              data_wiping.delivery_city AS 'Delivery City',
                              data_wiping.delivery_state AS 'Delivery State',
                              data_wiping.delivery_pin AS 'Delivery Pincode',
                              userreceiver_spoc_name.first_name AS 'Receiver SPOC Name',
                              data_wiping.receiver_spoc_number AS 'Receiver SPOC Number',
                              DATE_FORMAT(`data_wiping`.`delivery_date`,'%d-%m-%Y') AS 'Delivery Date',
                              delivery_conditiondelivery_condition.`value` AS 'Delivery Condition',
                              attachmentshsap_key_receipient.name AS 'HSAP Key Recipient',
                              userlogistic_spoc_name.first_name AS 'Logistic SPOC Name',
                              data_wiping.logistic_spoc_number AS 'Logistic SPOC Number',
                              DATE_FORMAT(`data_wiping`.`activity_schedule_date`,'%d-%m-%Y') AS 'Activity Schedule Date',
                              DATE_FORMAT(`data_wiping`.`completed_date`,'%d-%m-%Y') AS 'Completed Date',
                              userfe_name.first_name AS 'FE Name',
                              data_wiping.fe_number AS 'FE Number',
                              hsap_key_requirehsap_key_require.`value` AS 'HSAP Key Required',
                              data_wiping.hsap_count AS 'HSAP Count'
                              from `data_wiping` 
                              left join `user` as usercreatorid on (`data_wiping`.creatorid=usercreatorid.id) 
                              left join `user` as usermodifiedby on (`data_wiping`.modifiedby=usermodifiedby.id) 
                              LEFT OUTER JOIN sourcingdeal as sourcingdealopportunity_name on (`data_wiping`.opportunity_name=sourcingdealopportunity_name.sourcingdeal_id) 
                              LEFT OUTER JOIN vendor_account as vendor_accountaccount_name on (`data_wiping`.account_name=vendor_accountaccount_name.vendoraccid) 
                              LEFT OUTER JOIN contacts as contactsspoc_name on (`data_wiping`.spoc_name=contactsspoc_name.contacts_id) 
                              left join billable_type as billable_typebillable on (`data_wiping`.`billable`=billable_typebillable.billable_type_id) 
                              left join wiping_status as wiping_statuswiping_status on (`data_wiping`.`wiping_status`=wiping_statuswiping_status.wiping_statusid) 
                              LEFT OUTER JOIN `attachments` as attachmentsimage on (`data_wiping`.`image`= attachmentsimage.attachmentsid) 
                              left join `user` as userownerid on (`data_wiping`.ownerid=userownerid.id) 
                              left join currency as currencycurrency on (`data_wiping`.`currency`=currencycurrency.currencyid) 
                              LEFT OUTER JOIN vendor_locations as vendor_locationsbill_location on (`data_wiping`.bill_location=vendor_locationsbill_location.vendorloc_id) 
                              LEFT OUTER JOIN contacts as contactsbill_spoc on (`data_wiping`.bill_spoc=contactsbill_spoc.contacts_id) 
                              left join acc_billing_type as acc_billing_typebilling_type on (`data_wiping`.`billing_type`=acc_billing_typebilling_type.billing_type_id) 
                              LEFT OUTER JOIN vendor_locations as vendor_locationsactivity_location on (`data_wiping`.activity_location=vendor_locationsactivity_location.vendorloc_id) 
                              LEFT OUTER JOIN contacts as contactsactivtiy_spoc on (`data_wiping`.activtiy_spoc=contactsactivtiy_spoc.contacts_id) 
                              left join pickup_entry_person_formalities as pickup_entry_person_formalitiesentry_formalities_person on (`data_wiping`.`entry_formalities_person`=pickup_entry_person_formalitiesentry_formalities_person.id) 
                              left join pickup_power_supply_area as pickup_power_supply_areapower_supply_area on (`data_wiping`.`power_supply_area`=pickup_power_supply_areapower_supply_area.id) 
                              left join pickup_wifi_service as pickup_wifi_servicewifi_service on (`data_wiping`.`wifi_service`=pickup_wifi_servicewifi_service.id) 
                              left join pickup_equipment_working_timings as pickup_equipment_working_timingsworking_timings on (`data_wiping`.`working_timings`=pickup_equipment_working_timingsworking_timings.id) 
                              left join pickup_timing_ext_provision as pickup_timing_ext_provisionextend_time_provision on (`data_wiping`.`extend_time_provision`=pickup_timing_ext_provisionextend_time_provision.id) 
                              left join pickup_procedure_for_ext as pickup_procedure_for_extextension_provision on (`data_wiping`.`extension_provision`=pickup_procedure_for_extextension_provision.id) 
                              left join delivery_location_type as delivery_location_typepickup_location_type on (`data_wiping`.`pickup_location_type`=delivery_location_typepickup_location_type.id) 
                              LEFT OUTER JOIN warehouse as warehousepickup_location on (`data_wiping`.pickup_location=warehousepickup_location.warehouse_id) LEFT OUTER JOIN vendor_locations as vendor_locationspickup_location_client on (`data_wiping`.pickup_location_client=vendor_locationspickup_location_client.vendorloc_id) 
                              LEFT OUTER JOIN user as userpickup_spoc on (`data_wiping`.pickup_spoc=userpickup_spoc.id) 
                              left join delivery_condition as delivery_conditiondongle_pickup_condition on (`data_wiping`.`dongle_pickup_condition`=delivery_conditiondongle_pickup_condition.id) 
                              LEFT OUTER JOIN `attachments` as attachmentshsap_key_image on (`data_wiping`.`hsap_key_image`= attachmentshsap_key_image.attachmentsid) 
                              left join courier_list as courier_listcourrier_name on (`data_wiping`.`courrier_name`=courier_listcourrier_name.id) 
                              LEFT OUTER JOIN `attachments` as attachmentsgate_pass on (`data_wiping`.`gate_pass`= attachmentsgate_pass.attachmentsid) 
                              LEFT OUTER JOIN `attachments` as attachmentsdelivery_challan_invoice on (`data_wiping`.`delivery_challan_invoice`= attachmentsdelivery_challan_invoice.attachmentsid) 
                              left join delivery_location_type as delivery_location_typedelivery_location_type on (`data_wiping`.`delivery_location_type`=delivery_location_typedelivery_location_type.id) 
                              LEFT OUTER JOIN warehouse as warehousedelivery_location_internal on (`data_wiping`.delivery_location_internal=warehousedelivery_location_internal.warehouse_id) 
                              LEFT OUTER JOIN vendor_locations as vendor_locationsdelivery_location_client on (`data_wiping`.delivery_location_client=vendor_locationsdelivery_location_client.vendorloc_id) 
                              LEFT OUTER JOIN user as userreceiver_spoc_name on (`data_wiping`.receiver_spoc_name=userreceiver_spoc_name.id) 
                              left join delivery_condition as delivery_conditiondelivery_condition on (`data_wiping`.`delivery_condition`=delivery_conditiondelivery_condition.id) 
                              LEFT OUTER JOIN `attachments` as attachmentshsap_key_receipient on (`data_wiping`.`hsap_key_receipient`= attachmentshsap_key_receipient.attachmentsid) 
                              LEFT OUTER JOIN user as userlogistic_spoc_name on (`data_wiping`.logistic_spoc_name=userlogistic_spoc_name.id) 
                              LEFT OUTER JOIN user as userfe_name on (`data_wiping`.fe_name=userfe_name.id) 
                              left join hsap_key_require as hsap_key_requirehsap_key_require on (`data_wiping`.`hsap_key_require`=hsap_key_requirehsap_key_require.id) 
                              inner join user as owner on (owner.id=`data_wiping`.ownerid) 
                              where `data_wiping`.deleted=0  
                              -- DATE(`data_wiping`.createdtime) < :today
                              order by `data_wiping`.datawiping_id DESC";

     $datawiping_stmt = $connection->prepare($datawipingsql);
     $datawiping_stmt->execute();

     $datawiping_filePath = $directory . "/datawiping_detail_$filepathDatetime.csv";
     $datawiping_fp = fopen($datawiping_filePath, 'w');
     if (!$datawiping_fp) {
          throw new Exception("\n Unable to create or write to the Data Wiping CSV file.");
     }

     // Column headers
     $datawiping_columnCount = $datawiping_stmt->columnCount();
     $datawiping_headers = [];
     for ($p = 0; $p < $datawiping_columnCount; $p++) {
          $meta = $datawiping_stmt->getColumnMeta($p);
          $datawiping_headers[] = $meta['name'];
     }
     fputcsv($datawiping_fp, $datawiping_headers);

     // Data rows
     while ($row = $datawiping_stmt->fetch(PDO::FETCH_ASSOC)) {
          fputcsv($datawiping_fp, $row);
     }

     fclose($datawiping_fp);
     // echo "\n Data Wiping Record CSV file saved to: $datawiping_filePath";
     // upFileGenerateMailStatus($slot_code, $todayDatetime, $mailStatus, $datawipingStatus);//10 - Data wiping
      // VERIFY FILE CREATED SUCCESSFULLY
     if (file_exists($datawiping_filePath) && filesize($datawiping_filePath) > 0) {
          upFileGenerateMailStatus($slot_code, $today, $mailStatus, $datawipingStatus);//10 - Data wiping

          echo "\Data Wiping  CSV file saved to: $datawiping_filePath";
     } else {
          echo "\nError: Data Wiping  CSV file not generated or empty. Status not updated for : $datawiping_filePath";
     }
}
//  =======================================================================
// Pickup
// ========================================================================
$pickup_result_count = checkMailStatus($slot_code, $today, $pickupStatus);
echo "\nFile Created count for pickup =$pickup_result_count";
if ($pickup_result_count == 0) {
     $pickupsql = "Select 
                                        pickup.pickup_no AS 'Pickup No',
                                        CONCAT(userownerid.first_name,' ',userownerid.last_name) AS 'Owner',
                                        currencycurrency.`currency_value` AS 'Currency',
                                        pickup.exchange_rate AS 'Exchange Rate',
                                        sourcingdealopportuity_name.sourcingdeal_no AS 'Opportunity Name',
                                        vendor_accountaccount_name.acc_name AS 'Account Name',
                                        vendor_accountaccount_name.cust_code AS 'Account Code',
                                        contactsspoc_name.first_name AS 'SPOC Name',
                                        pickup.spoc_number AS 'SPOC Number',
                                        pickup.spoc_email AS 'SPOC Email',
                                        DATE_FORMAT(`pickup`.`scheduled_pickup_date`,'%d-%m-%Y') AS 'Scheduled Pickup Date',
                                        DATE_FORMAT(`pickup`.`preferred_pickup_date`,'%d-%m-%Y') AS 'Preferred Pickup Date',
                                        DATE_FORMAT(`pickup`.`actual_pickup_date`,'%d-%m-%Y') AS 'Actual Pickup Date',
                                        pick_document_receiveddoc_received.`documentrec_value` AS 'Document Received',
                                        GROUP_CONCAT(pick_additional_info_alias.additionalinfo_value ORDER BY pick_additional_info_alias.additionalinfoid) AS 'Additional Info',
                                        userfe_name.first_name AS 'FE Name',
                                        pickup.fe_number AS 'FE Number',
                                        userlogistic_user.first_name AS 'Logistic User',
                                        pickup.logistic_user_number AS 'Logistic User Number',
                                        IF(`pickup`.`pickup_submitted_for_logistics` IS NOT NULL,
                                        IF(`pickup`.`pickup_submitted_for_logistics`=0,'No','Yes'),'') AS 'Pickup Submitted For Logistics',
                                        IF(`pickup`.`pickup_inspection_require` IS NOT NULL,
                                        IF(`pickup`.`pickup_inspection_require`=0,'No','Yes'),'') AS 'Pickup Inspection Required',
                                        IF(`pickup`.`packing_material_approval_requested` IS NOT NULL,
                                        IF(`pickup`.`packing_material_approval_requested`=0,'No','Yes'),'') AS 'Packing Material Approval Requested',
                                        IF(`pickup`.`vehicle_planning_approval_requested` IS NOT NULL,
                                        IF(`pickup`.`vehicle_planning_approval_requested`=0,'No','Yes'),'') AS 'Vehicle Planning Approval Requested',
                                        IF(`pickup`.`pickup_schedule` IS NOT NULL,
                                        IF(`pickup`.`pickup_schedule`=0,'No','Yes'),'') AS 'Pickup Scheduled',
                                        IF(`pickup`.`pickup_in_process` IS NOT NULL,
                                        IF(`pickup`.`pickup_in_process`=0,'No','Yes'),'') AS 'Pickup In Process',
                                        IF(`pickup`.`pickup_completed` IS NOT NULL,
                                        IF(`pickup`.`pickup_completed`=0,'No','Yes'),'') AS 'Pickup Completed',
                                        pickup.remarks AS 'Remarks',
                                        pickup.vehicle_planning_remarks AS 'Vehicle Planning Remarks',
                                        pick_pickup_statuspickup_status.`pickup_status_value` AS 'Pickup Status',
                                        usercreatorid.username AS 'Created By',
                                        usermodifiedby.username AS 'Modified By',
                                        pickup.createdtime AS 'Created Time',
                                        pickup.modifiedtime AS 'Modified Time',
                                        pickup_equipment_working_timingsworking_timings.`value` AS 'What are the working timings',
                                        pickup_timing_ext_provisionextend_time_provision.`value` AS 'Do we have any provision to extend the timings',
                                        pickup_procedure_for_extextension_provision.`value` AS 'What is the procedure to inform/update regarding extension',
                                        pickup_entry_person_formalitiesentry_formalities_person.`value` AS 'What are the formalities for entry personnel',
                                        pickup_material_locationmaterial_location_floor.`value` AS 'Material lying at which location/floor',
                                        pickup.material_floor AS 'At which floor all the material is stored',
                                        pickup.floor_num_material_count AS 'Floor-wise Material Count',
                                        pickup_service_liftservice_lift.`value` AS 'Do we have service lift available',
                                        pickup.lift_timing AS 'How many Service lifts are avaibale',
                                        pickup_stairs_spacestairs_space.`value` AS 'Does stairs has sufficient space from where we can move the the material out from the premises',
                                        pickup.material_move AS 'How we can move the material out',
                                        pickup_segregationsegregation.`value` AS 'All items are segregated or segregation require',
                                        pickup_space_for_segregationspace_for_segregation.`value` AS 'Do we have space available for this segregation',
                                        pickup_movement_from_premisesmovement_from_premises.`value` AS 'What is the material movement from premises',
                                        pickup.floor_num_for_take_out AS 'Please share the basement floor / number from where we need to take out the material',
                                        pickup_vehicle_entry_formalitiesvehicle_entry_formalities.`value` AS 'What are the formalities for vehicle entry',
                                        pickup_vehicle_inside_premisesvehicle_inside_premises.`value` AS 'Vehicle can parked inside the premises',
                                        vendor_locationspickup_location.vendor_loc_name AS 'Pickup Location',
                                        pickup.pickup_address AS 'Pickup Address',
                                        pickup.pickup_city AS 'Pickup City',
                                        pickup.pickup_state AS 'Pickup State',
                                        pickup.pickup_pin_code AS 'Pickup Pincode',
                                        warehousedelivery_location.warehouse_name AS 'Delivery Location',
                                        pickup.delivery_address AS 'Delivery Address',
                                        pickup.delivery_city AS 'Delivery City',
                                        pickup.delivery_state AS 'Delivery State',
                                        pickup.delivery_pin_code AS 'Delivery Pincode',
                                        GROUP_CONCAT(pick_vehicle_size_alias.vehiclesize_value ORDER BY pick_vehicle_size_alias.vehiclesizeid) AS 'Vehicle Size',
                                        pickup.num_of_vehicle_required AS 'Number Of Vehicles Required',
                                        pickup.distance_from_pickup AS 'Distance From Pickup',
                                        pickup_segregationvehicle_parking_available.`value` AS 'Vehicle Parking Available',
                                        pickup.distance_from_lift AS 'Distance From Lift',
                                        pickup_point_locationpickup_point_location.`value` AS 'Pickup Point Location',
                                        pickup_segregationhydra_require.`value` AS 'Hydra Required',
                                        pickup_segregationfolk_lift_require.`value` AS 'Forklift Required',
                                        pickup_segregationmobile_trolley.`value` AS 'Mobile Trolley',
                                        pickup.labour_count AS 'Labour Count',
                                        pickup.labour_rate AS 'Labour Rate',
                                        pickup.total_labour_count AS 'Total Labour Count',
                                        pickup_segregationlocal_union.`value` AS 'Local Union',
                                        pickup.local_union_charges AS 'Local Union Charges',
                                        pickup_local_vehicle_requirelocal_vehicle_require.`value` AS 'Local Vehicle Required',
                                        local_vehicle_sizelocal_vehicle_size.`value` AS 'Local Vehicle Size',
                                        pickup.local_vehicle_charges AS 'Local Vehicle Charges',
                                        pickup.num_local_vehicle AS 'Number Of Local Vehicles',
                                        pickup_segregationover_time.`value` AS 'Overtime',
                                        pickup.over_time_charges AS 'Overtime Charges',
                                        pickup.pre_pickup_remarks AS 'Pre-Pickup Remarks',
                                        attachmentsform6_unsigned_copy.name AS 'Form 6 Unsigned Copy',
                                        attachmentsform6_stamped_copy.name AS 'Form 6 Stamped Copy',
                                        attachmentsform10_unsigned_copy.name AS 'Form 10 Unsigned Copy',
                                        attachmentsform10_stamped_copy.name AS 'Form 10 Stamped Copy',
                                        attachmentsupload_unsigned_copy.name AS 'Upload Unsigned Copy',
                                        attachmentsupload_stamped_copy.name AS 'Upload Stamped Copy',
                                        attachmentsgreen_certificate.name AS 'Green Certificate'
                                   from `pickup` 
                                   left join `user` as userownerid on (`pickup`.ownerid=userownerid.id) 
                                   left join currency as currencycurrency on (`pickup`.`currency`=currencycurrency.currencyid) 
                                   LEFT OUTER JOIN sourcingdeal as sourcingdealopportuity_name on (`pickup`.opportuity_name=sourcingdealopportuity_name.sourcingdeal_id) 
                                   LEFT OUTER JOIN vendor_account as vendor_accountaccount_name on (`pickup`.account_name=vendor_accountaccount_name.vendoraccid) 
                                   LEFT OUTER JOIN contacts as contactsspoc_name on (`pickup`.spoc_name=contactsspoc_name.contacts_id) 
                                   left join pick_document_received as pick_document_receiveddoc_received on (`pickup`.`doc_received`=pick_document_receiveddoc_received.documentrecid) 
                                   LEFT JOIN pick_additional_info AS pick_additional_info_alias ON FIND_IN_SET(pick_additional_info_alias.additionalinfoid, `pickup`.additional_info) 
                                   LEFT OUTER JOIN user as userfe_name on (`pickup`.fe_name=userfe_name.id) 
                                   LEFT OUTER JOIN user as userlogistic_user on (`pickup`.logistic_user=userlogistic_user.id) 
                                   left join pick_pickup_status as pick_pickup_statuspickup_status on (`pickup`.`pickup_status`=pick_pickup_statuspickup_status.pickup_status_id) 
                                   left join `user` as usercreatorid on (`pickup`.creatorid=usercreatorid.id) 
                                   left join `user` as usermodifiedby on (`pickup`.modifiedby=usermodifiedby.id) 
                                   left join pickup_equipment_working_timings as pickup_equipment_working_timingsworking_timings on (`pickup`.`working_timings`=pickup_equipment_working_timingsworking_timings.id) 
                                   left join pickup_timing_ext_provision as pickup_timing_ext_provisionextend_time_provision on (`pickup`.`extend_time_provision`=pickup_timing_ext_provisionextend_time_provision.id) 
                                   left join pickup_procedure_for_ext as pickup_procedure_for_extextension_provision on (`pickup`.`extension_provision`=pickup_procedure_for_extextension_provision.id) 
                                   left join pickup_entry_person_formalities as pickup_entry_person_formalitiesentry_formalities_person on (`pickup`.`entry_formalities_person`=pickup_entry_person_formalitiesentry_formalities_person.id) 
                                   left join pickup_material_location as pickup_material_locationmaterial_location_floor on (`pickup`.`material_location_floor`=pickup_material_locationmaterial_location_floor.id) 
                                   left join pickup_service_lift as pickup_service_liftservice_lift on (`pickup`.`service_lift`=pickup_service_liftservice_lift.id) 
                                   left join pickup_stairs_space as pickup_stairs_spacestairs_space on (`pickup`.`stairs_space`=pickup_stairs_spacestairs_space.id) 
                                   left join pickup_segregation as pickup_segregationsegregation on (`pickup`.`segregation`=pickup_segregationsegregation.id) 
                                   left join pickup_space_for_segregation as pickup_space_for_segregationspace_for_segregation on (`pickup`.`space_for_segregation`=pickup_space_for_segregationspace_for_segregation.id) 
                                   left join pickup_movement_from_premises as pickup_movement_from_premisesmovement_from_premises on (`pickup`.`movement_from_premises`=pickup_movement_from_premisesmovement_from_premises.id) 
                                   left join pickup_vehicle_entry_formalities as pickup_vehicle_entry_formalitiesvehicle_entry_formalities on (`pickup`.`vehicle_entry_formalities`=pickup_vehicle_entry_formalitiesvehicle_entry_formalities.id) 
                                   left join pickup_vehicle_inside_premises as pickup_vehicle_inside_premisesvehicle_inside_premises on (`pickup`.`vehicle_inside_premises`=pickup_vehicle_inside_premisesvehicle_inside_premises.id) 
                                   LEFT OUTER JOIN vendor_locations as vendor_locationspickup_location on (`pickup`.pickup_location=vendor_locationspickup_location.vendorloc_id) 
                                   LEFT OUTER JOIN warehouse as warehousedelivery_location on (`pickup`.delivery_location=warehousedelivery_location.warehouse_id) 
                                   LEFT JOIN pick_vehicle_size AS pick_vehicle_size_alias ON FIND_IN_SET(pick_vehicle_size_alias.vehiclesizeid, `pickup`.vehicle_size1) 
                                   left join pickup_segregation as pickup_segregationvehicle_parking_available on (`pickup`.`vehicle_parking_available`=pickup_segregationvehicle_parking_available.id) 
                                   left join pickup_point_location as pickup_point_locationpickup_point_location on (`pickup`.`pickup_point_location`=pickup_point_locationpickup_point_location.id) 
                                   left join pickup_segregation as pickup_segregationhydra_require on (`pickup`.`hydra_require`=pickup_segregationhydra_require.id) 
                                   left join pickup_segregation as pickup_segregationfolk_lift_require on (`pickup`.`folk_lift_require`=pickup_segregationfolk_lift_require.id) 
                                   left join pickup_segregation as pickup_segregationmobile_trolley on (`pickup`.`mobile_trolley`=pickup_segregationmobile_trolley.id) 
                                   left join pickup_segregation as pickup_segregationlocal_union on (`pickup`.`local_union`=pickup_segregationlocal_union.id) 
                                   left join pickup_local_vehicle_require as pickup_local_vehicle_requirelocal_vehicle_require on (`pickup`.`local_vehicle_require`=pickup_local_vehicle_requirelocal_vehicle_require.id) 
                                   left join local_vehicle_size as local_vehicle_sizelocal_vehicle_size on (`pickup`.`local_vehicle_size`=local_vehicle_sizelocal_vehicle_size.id) 
                                   left join pickup_segregation as pickup_segregationover_time on (`pickup`.`over_time`=pickup_segregationover_time.id) LEFT OUTER JOIN `attachments` as attachmentsform6_unsigned_copy on (`pickup`.`form6_unsigned_copy`= attachmentsform6_unsigned_copy.attachmentsid) 
                                   LEFT OUTER JOIN `attachments` as attachmentsform6_stamped_copy on (`pickup`.`form6_stamped_copy`= attachmentsform6_stamped_copy.attachmentsid) 
                                   LEFT OUTER JOIN `attachments` as attachmentsform10_unsigned_copy on (`pickup`.`form10_unsigned_copy`= attachmentsform10_unsigned_copy.attachmentsid) 
                                   LEFT OUTER JOIN `attachments` as attachmentsform10_stamped_copy on (`pickup`.`form10_stamped_copy`= attachmentsform10_stamped_copy.attachmentsid) 
                                   LEFT OUTER JOIN `attachments` as attachmentsupload_unsigned_copy on (`pickup`.`upload_unsigned_copy`= attachmentsupload_unsigned_copy.attachmentsid) 
                                   LEFT OUTER JOIN `attachments` as attachmentsupload_stamped_copy on (`pickup`.`upload_stamped_copy`= attachmentsupload_stamped_copy.attachmentsid) 
                                   LEFT OUTER JOIN `attachments` as attachmentsgreen_certificate on (`pickup`.`green_certificate`= attachmentsgreen_certificate.attachmentsid) 
                                   inner join user as owner on (owner.id=`pickup`.ownerid) 
                                   where `pickup`.deleted=0  
                                   -- DATE(`pickup`.createdtime) < :today
                                   GROUP BY `pickup`.pickup_id order by `pickup`.pickup_id DESC";

     $pickup_stmt = $connection->prepare($pickupsql);
     $pickup_stmt->execute();

     $pickup_filePath = $directory . "/pickup_detail_$filepathDatetime.csv";
     $pickup_fp = fopen($pickup_filePath, 'w');
     if (!$pickup_fp) {
          throw new Exception("\n Unable to create or write to the Pickup CSV file.");
     }

     // Column headers
     $pickup_columnCount = $pickup_stmt->columnCount();
     $pickup_headers = [];
     for ($p = 0; $p < $pickup_columnCount; $p++) {
          $meta = $pickup_stmt->getColumnMeta($p);
          $pickup_headers[] = $meta['name'];
     }
     fputcsv($pickup_fp, $pickup_headers);

     // Data rows
     while ($row = $pickup_stmt->fetch(PDO::FETCH_ASSOC)) {
          fputcsv($pickup_fp, $row);
     }

     fclose($pickup_fp);
     // echo "\n Pickup Record CSV file saved to: $pickup_filePath";
     // upFileGenerateMailStatus($slot_code, $todayDatetime, $mailStatus, $pickupStatus);//11 - Pickup
     // VERIFY FILE CREATED SUCCESSFULLY
     if (file_exists($pickup_filePath) && filesize($pickup_filePath) > 0) {
          upFileGenerateMailStatus($slot_code, $today, $mailStatus, $pickupStatus);//11 - Pickup

          echo "\Pickup  CSV file saved to: $pickup_filePath";
     } else {
          echo "\nError: Pickup  CSV file not generated or empty. Status not updated for : $pickup_filePath";
     }
}
//  =======================================================================
// Opportunity
// ========================================================================
$oppor_result_count = checkMailStatus($slot_code, $today, $opporStatus);
echo "\nFile Created count for opportuntiy =$oppor_result_count";
if ($oppor_result_count == 0) {
     $pickupsql = "SELECT concat(userownerid.first_name,' ', userownerid.last_name) AS `Opportunity Owner`, 
          usercreatorid.username AS `Created BY`,
          opportunity_no AS `Opportunity No`,
          usermodifiedby.username  AS `Last Modified By`,
          deal_name AS `Deal Name`, 
          DATE_FORMAT(`opportunity`.`createdtime`,'%d-%m-%Y %H:%i:%s') AS `Created Time`,
          DATE_FORMAT(`opportunity`.`modifiedtime`,'%d-%m-%Y %H:%i:%s') AS `Modified Time`,
          vendor_accountaccount_name.acc_name  AS `Account Name`,
          vendor_accountaccount_name.cust_code  AS `Account Code`,
          concat(requester_customer_name.first_name,' ',requester_customer_name.last_name) AS `Requester/Customer Name`,
               requester_email_customer_email AS `Requester Email/Customer Email`,
               requester_mobile AS `Requester Mobile`, 
               concat(decision_maker_name.first_name,' ',decision_maker_name.last_name) AS `Decision Maker Name`, 
               decision_maker_email AS `Decision Maker Email`, 
               decision_maker_mobile AS `Decision Maker Mobile`, 
               zn.zone_value AS `Zone/Region`,
               tm.team_name_value AS `Team Name`,
               opportunity_tentative_value AS `Opportunity Tentative Value`, 
               DATE_FORMAT(`opportunity`.`closing_date`,'%d-%m-%Y')  AS `Closing Date`, 
               closure_months.months_value AS `Closure Month`,
               closure_year AS `Closure year`,
               closure_week.closure_week_value AS `Closure Week`,
               DATE_FORMAT(`opportunity`.`forcast_date`,'%d-%m-%Y')   AS `Forcast Date`, 
               oc.commit_value AS `Commit`, 
               DATE_FORMAT(`opportunity`.`commit_date`,'%d-%m-%Y')   AS `Commit Date`,
               commit_month.months_value  AS `Commit Month`,
               commit_year AS `Commit Year`, 
              commit_week.closure_week_value AS `Commit Week`, 
              business_manager.username AS `DevIT Business Manager`, 
          --     account_manager.username AS `DevIT Account Manager`,
               account_director_rsm.username AS `Account Director/ RSM`,
               devit_isr.username AS `DevIT ISR`,
               devit_vertical_manager.username AS `DevIT Vertical Manager`,
                opportunity_stage.stage_value AS `Opportunity Stage`, 
                DATE_FORMAT(`opportunity`.po_received_date,'%d-%m-%Y') as `PO Received Date`,
                oppr_oem_taggingoem_tagging.oem_tagging_value as `OEM Tagging`,
                if(`opportunity`.`submit_for_screening` is not null,if(`opportunity` . `submit_for_screening`=0,'No','Yes'),'')  AS `Submit For Screening`, 
                if(`opportunity`.`submit_for_pricing` is not null,if(`opportunity` . `submit_for_pricing`=0,'No','Yes'),'') AS `Submit For Pricing`, 
                if(`opportunity`.`pricing_done` is not null,if(`opportunity` . `pricing_done`=0,'No','Yes'),'') AS `Pricing Done`, 
                comments AS `Comments`, 
                warehouse_loc_business_entity.warehouse_name AS `Warehouse Location/Business Entity`, 
                bill_location.vendor_loc_name AS `Bill to Location`, 
                bill_from_location.vendor_loc_name AS `Bill From Location`, 
                bill_address AS `Bill to Address`, 
                bill_from_address AS `Bill From Address`, 
                bill_from_state AS `Bill From State`, 
                bill_state AS `Bill to State`,
                 bill_from_state_code AS `Bill From State Code`,
                  bill_state_code AS `Bill to State Code`,
                   opportunity.pan_number AS `Pan Number`, 
                   bill_gstin_no AS `Bill To GSTIN No/ UIN`, 
                   product_category.product_category_value AS `Product Category`, 
                   (
                    SELECT GROUP_CONCAT(r.team_responsible_value ORDER BY r.team_responsible_value SEPARATOR ', ')
                    FROM oppr_team_responsible r
                    WHERE FIND_IN_SET(r.team_responsible_id, opportunity.team_responsible)
               ) AS `Team Responsible`, 
                              -- sa_assigned.username AS `Solution Architect Assigned`,
                              -- sf_assigned.username AS `Solution Factory Assigned`,
                              -- procurement_team_member.username AS `Procurement Team Member`, 
                              ( SELECT GROUP_CONCAT(DISTINCT CONCAT(u.first_name,' ',u.last_name) ORDER BY u.id SEPARATOR ', ') 
                              FROM user u 
                                   WHERE FIND_IN_SET(u.id, REPLACE(opportunity.sa_assigned,' ', '')) ) AS `Solution Architect Assigned`,
                              ( SELECT GROUP_CONCAT(DISTINCT CONCAT(u.first_name,' ',u.last_name) ORDER BY u.id SEPARATOR ', ') 
                              FROM user u 
                                   WHERE FIND_IN_SET(u.id, REPLACE(opportunity.sf_assigned,' ', '')) ) AS  `Solution Factory Assigned`,
                                   ( SELECT GROUP_CONCAT(DISTINCT CONCAT(u.first_name,' ',u.last_name) ORDER BY u.id SEPARATOR ', ') 
                              FROM user u 
                                   WHERE FIND_IN_SET(u.id, REPLACE(opportunity.procurement_team_member,' ', '')) ) AS `Procurement Team Member`,
                                   lead_source.leadsource_value AS `Lead Source`,
                                   customer_po_num AS `Customer PO Number`, 
                                   customer_payment_terms.payment_terms_value AS `Customer Payment Terms`, 
                                   DATE_FORMAT(`opportunity`.`customer_po_date`,'%d-%m-%Y')  AS `Customer PO Date`,
                                   DATE_FORMAT(`opportunity`.`po_received_date`,'%d-%m-%Y') AS `PO Received Date`,
                                   opportunity.total_oppr_cost_tax_exclude as `Total Opportunity Cost Tax Exclude`,
                                   opportunity.total_oppr_sale_tax_exclude as `Total Opportunity Sale Tax Exclude`,
                                   opportunity.total_opportunity_cgst as `Total Opportunity CGST`,
                                   opportunity.total_opportunity_sgst as `Total Opportunity SGST`,
                                   opportunity.total_opportunity_igst as `Total Opportunity IGST`,
                                   opportunity.total_oppr_amount_tax_include as `Total Opportunity Amount Tax Include`,
                                   opportunity.opportunity_margin as `Opportunity Margin`,
                                   opportunity.opportunity_margin_percentage as `Opportunity Margin %`
                                   FROM opportunity
          inner join user as userownerid on (`opportunity`.ownerid=`userownerid`.id) 
          left join `user` as usercreatorid on (`opportunity`.creatorid=usercreatorid.id) 
          left join `user` as usermodifiedby on (`opportunity`.modifiedby=usermodifiedby.id)
          left join `user` as business_manager on (`opportunity`.business_manager=business_manager.id)
          -- left join `user` as account_manager on (`opportunity`.account_manager=account_manager.id)
          left join `user` as devit_isr on (`opportunity`.devit_isr=devit_isr.id)
          left join `user` as devit_vertical_manager on (`opportunity`.devit_vertical_manager=devit_vertical_manager.id)
          left join `user` as account_director_rsm on (`opportunity`.account_director_rsm=account_director_rsm.id)
          left join `user` as sa_assigned on (`opportunity`.sa_assigned=sa_assigned.id)
          left join `user` as sf_assigned on (`opportunity`.sf_assigned=sf_assigned.id)
          left join `user` as procurement_team_member on (`opportunity`.procurement_team_member=procurement_team_member.id)
          left join `oppr_zone` as zn on (`opportunity`.zone_region=zn.zone_id)
          left join `oppr_team_name` as tm on (`opportunity`.team_name=tm.team_name_id)
          left join `months` as closure_months on (`opportunity`.closure_month=closure_months.months_id)
          left join `months` as commit_month on (`opportunity`.commit_month=commit_month.months_id)
          left join `oppr_commit` as oc on (`opportunity`.`commit`=oc.commit_id)
          left join `closure_week` as closure_week on (`opportunity`.closure_week=closure_week.closure_weekid) 
          left join `closure_week` as commit_week on (`opportunity`.commit_week=commit_week.closure_weekid) 
          left join `oppr_stage` as opportunity_stage on (`opportunity`.opportunity_stage=opportunity_stage.stage_id) 
          left join `oppr_product_category` as product_category on (`opportunity`.product_category=product_category.product_category_id) 
          left join `lead_source` as lead_source on (`opportunity`.lead_source=lead_source.leadsourceid) 
          left join `opp_payment_terms` as customer_payment_terms on (`opportunity`.customer_payment_terms=customer_payment_terms.payment_terms_id) 
          left join `warehouse` as warehouse_loc_business_entity on (`opportunity`.warehouse_loc_business_entity=warehouse_loc_business_entity.warehouse_id) 
          left join `contacts` as requester_customer_name on (`opportunity`.requester_customer_name=requester_customer_name.contacts_id) 
          left join `contacts` as decision_maker_name on (`opportunity`.decision_maker_name=decision_maker_name.contacts_id) 
          left join `vendor_locations` as bill_location on (`opportunity`.bill_location=bill_location.vendorloc_id) 
          left join `vendor_locations` as bill_from_location on (`opportunity`.bill_from_location=bill_from_location.vendorloc_id) 
          LEFT OUTER JOIN vendor_account as vendor_accountaccount_name on (`opportunity`.vendor_account_name=vendor_accountaccount_name.vendoraccid)  
          left join oppr_oem_tagging as oppr_oem_taggingoem_tagging on (`opportunity`.oem_tagging=oppr_oem_taggingoem_tagging.oem_tagging_id) 
          where `opportunity`.deleted=0 AND  `opportunity`.is_temp =0   
          -- DATE(`opportunity`.createdtime) < :today 
           group by `opportunity`.opportunity_id order by `opportunity`.opportunity_id DESC";

     $pickup_stmt = $connection->prepare($pickupsql);
     $pickup_stmt->execute();

     $pickup_filePath = $directory . "/opportunity_$filepathDatetime.csv";
     $pickup_fp = fopen($pickup_filePath, 'w');
     if (!$pickup_fp) {
          throw new Exception("\n Unable to create or write to the opportunity CSV file.");
     }

     // Column headers
     $pickup_columnCount = $pickup_stmt->columnCount();
     $pickup_headers = [];
     for ($p = 0; $p < $pickup_columnCount; $p++) {
          $meta = $pickup_stmt->getColumnMeta($p);
          $pickup_headers[] = $meta['name'];
     }
     fputcsv($pickup_fp, $pickup_headers);

     // Data rows
     while ($row = $pickup_stmt->fetch(PDO::FETCH_ASSOC)) {
          fputcsv($pickup_fp, $row);
     }

     fclose($pickup_fp);
     // echo "\n opportunity Record CSV file saved to: $pickup_filePath";
     // upFileGenerateMailStatus($slot_code, $todayDatetime, $mailStatus, $opporStatus);
     // VERIFY FILE CREATED SUCCESSFULLY
     if (file_exists($pickup_filePath) && filesize($pickup_filePath) > 0) {
          upFileGenerateMailStatus($slot_code, $today, $mailStatus, $opporStatus);

          echo "\opportunity  CSV file saved to: $pickup_filePath";
     } else {
          echo "\nError: opportunity  CSV file not generated or empty. Status not updated for : $pickup_filePath";
     }

}

//  =======================================================================
// Opportunity Shipping to address Detail
// ========================================================================
$oppor_ship_result_count = checkMailStatus($slot_code, $today, $opporshipStatus);
echo "\nFile Created count for opportuntiy shipping detail =$oppor_ship_result_count";
if ($oppor_ship_result_count == 0) {
     $pickupsql = "SELECT opportunity_no AS `Opportunity No`,
     deal_name AS `Deal Name`, ship_to_location.vendor_loc_name AS `Ship To Location`, opportunity_ship_detail.ship_to_address AS `Ship to Address`, opportunity_ship_detail.ship_to_state AS `Ship to State`, opportunity_ship_detail.ship_legal_name AS `Ship to Lagal Name`, opportunity_ship_detail.gstin_no_uin AS `Ship to GSTIN No`, opportunity_ship_detail.ship_state_code AS `Ship to State Code` FROM opportunity_ship_detail 
     join opportunity on opportunity.opportunity_id=opportunity_ship_detail.opportunity_id 
     left join `vendor_locations` as ship_to_location on (`opportunity_ship_detail`.ship_to_location=ship_to_location.vendorloc_id) 
     where     `opportunity`.deleted=0 AND  `opportunity`.is_temp =0  
     -- DATE(`opportunity`.createdtime) < :today  
      order by `opportunity`.opportunity_id DESC";

     $pickup_stmt = $connection->prepare($pickupsql);
     $pickup_stmt->execute();

     $pickup_filePath = $directory . "/opportunity_shiptoaddress_detail_$filepathDatetime.csv";
     $pickup_fp = fopen($pickup_filePath, 'w');
     if (!$pickup_fp) {
          throw new Exception("\n Unable to create or write to the Opportunity shipping detail CSV file.");
     }

     // Column headers
     $pickup_columnCount = $pickup_stmt->columnCount();
     $pickup_headers = [];
     for ($p = 0; $p < $pickup_columnCount; $p++) {
          $meta = $pickup_stmt->getColumnMeta($p);
          $pickup_headers[] = $meta['name'];
     }
     fputcsv($pickup_fp, $pickup_headers);

     // Data rows
     while ($row = $pickup_stmt->fetch(PDO::FETCH_ASSOC)) {
          fputcsv($pickup_fp, $row);
     }

     fclose($pickup_fp);
     echo "\n Opportunity shipping detail Record CSV file saved to: $pickup_filePath";
     upFileGenerateMailStatus($slot_code, $todayDatetime, $mailStatus, $opporshipStatus);
}

//  =======================================================================
// Opportunity Product Detail
// ========================================================================
$oppor_product_result_count = checkMailStatus($slot_code, $today, $opporproductStatus);
echo "\nFile Created count for opportuntiy product detail =$oppor_product_result_count";
if ($oppor_product_result_count == 0) {
     $pickupsql = "SELECT opportunity_no AS `Opportunity No`,
  deal_name AS `Deal Name`,opportunity_product_detail.purchase_request_number AS `Purchase Request Number`, product_dit.product_name AS `Product Name`, opportunity_product_detail.product_description AS `Product Description`, proddit_master_category.master_category_value AS `Master Category`, proddit_sub_category.sub_category_value AS `Sub Category`, opportunity_product_detail.hsn_code AS `HSN Code`, opportunity_product_detail.quantity AS `Quantity`, opportunity_product_detail.cost_price AS `Cost Price`, opportunity_product_detail.margin_percentage AS `Margin (%)`, opportunity_product_detail.sales_price AS `Sales Price`, opportunity_product_detail.cgst AS `CGST`, opportunity_product_detail.sgst AS `SGST`, opportunity_product_detail.igst AS `IGST`, opportunity_product_detail.total_amount AS `Total Line Item Amount`, opportunity_product_detail.gross_profit AS `Gross Profit`, 
DATE_FORMAT(`opportunity_product_detail`.`add_price_validity`,'%d-%m-%Y')  AS `Add Price Validity`,
   add_product_delivery_timeline.days_value AS `Add Product Delivery Time Line`, opportunity_product_detail.add_product_warranty AS `Add Product Warranty`,
   if(opportunity_product_detail.reject is not null,if(opportunity_product_detail.reject=0,'No','Yes'),'') AS `Reject`,
    opportunity_product_detail.remarks AS `Remarks` ,
    opportunity_product_detail.cgst_amount AS `CGST Amount`, 
    opportunity_product_detail.sgst_amount AS `SGST Amount` ,
    opportunity_product_detail.igst_amount AS `IGST Amount` ,
    opportunity_product_detail.total_cost_tax_exclude AS `Total Cost Tax Exclude`, 
    opportunity_product_detail.total_sale_tax_exclude AS `Total Sale Tax Exclude`,
    opportunity_product_detail.total_amt_tax_include AS `Total Amount Tax Include`,
    prodoem.prod_oem_value AS `OEM Name`
FROM opportunity_product_detail
  left join opportunity on opportunity.opportunity_id=opportunity_product_detail.opportunity_id 
  left join product_dit on opportunity_product_detail.product_name=product_dit.productdit_id  
  left join proddit_oem prodoem ON prodoem.prod_oem_id = product_dit.oem
  left join proddit_master_category on opportunity_product_detail.master_category=proddit_master_category.master_category_id  
  left join proddit_sub_category on opportunity_product_detail.sub_category=proddit_sub_category.sub_category_id
  left join oppr_days as add_product_delivery_timeline on opportunity_product_detail.add_product_delivery_timeline=add_product_delivery_timeline.days_id
     where  `opportunity`.deleted=0 AND  `opportunity`.is_temp =0  
     -- DATE(`opportunity`.createdtime) < :today 
      order by `opportunity`.opportunity_id DESC";

     $pickup_stmt = $connection->prepare($pickupsql);
     $pickup_stmt->execute();

     $pickup_filePath = $directory . "/opportunity_product_detail_$filepathDatetime.csv";
     $pickup_fp = fopen($pickup_filePath, 'w');
     if (!$pickup_fp) {
          throw new Exception("\n Unable to create or write to the Opportuntiy Product detail CSV file.");
     }

     // Column headers
     $pickup_columnCount = $pickup_stmt->columnCount();
     $pickup_headers = [];
     for ($p = 0; $p < $pickup_columnCount; $p++) {
          $meta = $pickup_stmt->getColumnMeta($p);
          $pickup_headers[] = $meta['name'];
     }
     fputcsv($pickup_fp, $pickup_headers);

     // Data rows
     while ($row = $pickup_stmt->fetch(PDO::FETCH_ASSOC)) {
          fputcsv($pickup_fp, $row);
     }

     fclose($pickup_fp);
     // echo "\n Opportuntiy Product detail Record CSV file saved to: $pickup_filePath";
     // upFileGenerateMailStatus($slot_code, $todayDatetime, $mailStatus, $opporproductStatus);//11 - Pickup
     // VERIFY FILE CREATED SUCCESSFULLY
     if (file_exists($pickup_filePath) && filesize($pickup_filePath) > 0) {
          upFileGenerateMailStatus($slot_code, $today, $mailStatus, $opporproductStatus);

          echo "\Opportuntiy Product detail  CSV file saved to: $pickup_filePath";
     } else {
          echo "\nError: Opportuntiy Product detail  CSV file not generated or empty. Status not updated for : $pickup_filePath";
     }
}

// ========================================================================
// Purchase Request Detail
// ========================================================================
$purchase_req_result_count = checkMailStatus($slot_code, $today, $purchaseRequestStatus);
echo "\nFile Created count for Purchase Request detail = $purchase_req_result_count";
if ($purchase_req_result_count == 0) {

    $prSql = "SELECT
          CONCAT(u_owner.first_name, ' ', u_owner.last_name) AS `Purchase Request (PR) Number Requester`,
          opd.purchase_request_number AS `PR No.`,
          op.opportunity_no AS `Opportunity No`,
          va.acc_name AS `Account Name`,
          va.cust_code AS `Account Code`,
          CONCAT(u_isr.first_name, ' ', u_isr.last_name) AS `ISR`,
          CONCAT(u_bm.first_name, ' ', u_bm.last_name) AS `BM`,
          CONCAT(u_vm.first_name, ' ', u_vm.last_name) AS `VM`,
          CONCAT(u_rsm.first_name, ' ', u_rsm.last_name) AS `RSM`,
          DATE_FORMAT(op.submit_pricing_date, '%d/%m/%Y') AS `PR Received Date`,
          DATE_FORMAT(op.prodpricing_done_date, '%d/%m/%Y') AS `PR Closed Date`,
          GROUP_CONCAT(DISTINCT otr.team_responsible_value ORDER BY otr.team_responsible_id SEPARATOR ', ') AS `Pricing Team`, 
          CONCAT_WS(', ',
          NULLIF(CONCAT('SA Assigned - ', GROUP_CONCAT(DISTINCT CONCAT(u_sa.first_name, ' ', u_sa.last_name) ORDER BY u_sa.id SEPARATOR ', ')), 'SA Assigned - '),
          NULLIF(CONCAT('SF Assigned - ', GROUP_CONCAT(DISTINCT CONCAT(u_sf.first_name, ' ', u_sf.last_name) ORDER BY u_sf.id SEPARATOR ', ')), 'SF Assigned - '),
          NULLIF(CONCAT('Procurement Team - ', GROUP_CONCAT(DISTINCT CONCAT(u_ptm.first_name, ' ', u_ptm.last_name) ORDER BY u_ptm.id SEPARATOR ', ')), 'Procurement Team - ')
          ) AS `Pricing User`,
          DATE_FORMAT(opd.add_price_validity, '%d/%m/%Y')  AS `Quote Validity (Date)`,
          prodoem.prod_oem_value AS `OEM Name`,
          proddit_master_category.master_category_value AS `Category`,
          proddit_sub_category.sub_category_value AS `Sub Category`,
          product_dit.product_description AS `Product Description`,
          opd.quantity AS `Qty`,
          opd.total_amt_tax_include AS `Price Total`,
          opd.total_cost_tax_exclude AS `Cost Price`,
          opppt.payment_terms_value AS `Credit Terms`,
          opprday.days_value AS `Estimate Time Delivery`,
          opprstage.stage_value AS `Status`
          FROM opportunity_product_detail opd
          INNER JOIN opportunity op ON op.opportunity_id = opd.opportunity_id
          INNER JOIN vendor_account va ON va.vendoraccid = op.vendor_account_name
          LEFT JOIN user u_isr ON u_isr.id = op.devit_isr
          LEFT JOIN user u_bm ON u_bm.id = op.business_manager
          LEFT JOIN user u_vm ON u_vm.id = op.devit_vertical_manager
          LEFT JOIN user u_owner ON u_owner.id = op.creatorid
          LEFT JOIN user u_rsm ON u_rsm.id = op.account_director_rsm
          LEFT JOIN user u_sa ON FIND_IN_SET(u_sa.id, op.sa_assigned) > 0
          LEFT JOIN user u_sf ON FIND_IN_SET(u_sf.id, op.sf_assigned) > 0
          LEFT JOIN user u_ptm ON FIND_IN_SET(u_ptm.id, op.procurement_team_member) > 0
          LEFT JOIN product_dit ON opd.product_name = product_dit.productdit_id
          LEFT JOIN proddit_master_category ON opd.master_category = proddit_master_category.master_category_id
          LEFT JOIN proddit_sub_category ON opd.sub_category = proddit_sub_category.sub_category_id
          LEFT JOIN proddit_oem prodoem ON prodoem.prod_oem_id = product_dit.oem
          LEFT JOIN oppr_days opprday ON opprday.days_id = opd.add_product_delivery_timeline
          LEFT JOIN opp_payment_terms opppt ON opppt.payment_terms_id = op.customer_payment_terms
          LEFT JOIN oppr_stage opprstage ON opprstage.stage_id = op.opportunity_stage
          LEFT JOIN oppr_team_responsible otr ON FIND_IN_SET(otr.team_responsible_id, op.team_responsible) > 0
          WHERE opd.deleted = 0
          -- AND op.modifiedtime BETWEEN :start AND :end
          GROUP BY opd.purchase_request_number
          ORDER BY op.opportunity_id";

    $prStmt = $connection->prepare($prSql);
    $prStmt->execute();
//     $prStmt->execute([
//         ':start' => $start,
//         ':end'   => $end,
//     ]);

    $prRows = $prStmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($prRows)) {
        echo "\nNo Purchase Request records found for the given date range.";
    } else {
        $pickup_filePath = $directory . "/purchase_request_detail_$filepathDatetime.csv";
        $pickup_fp = fopen($pickup_filePath, 'w');
        if (!$pickup_fp) {
            throw new Exception("\nUnable to create or write to the Purchase Request CSV file.");
        }

        fputcsv($pickup_fp, array_keys($prRows[0]));

        foreach ($prRows as $row) {
            fputcsv($pickup_fp, $row);
        }

        fclose($pickup_fp);
        echo "\nPurchase Request Record CSV file saved to: $pickup_filePath";

        upFileGenerateMailStatus($slot_code, $todayDatetime, $mailStatus, $purchaseRequestStatus);
    }
}

//  =======================================================================
//LEad Detail
// ========================================================================
$lead_result_count = checkMailStatus($slot_code, $today, $leadStatus);
echo "\nFile Created count for Lead detail =$lead_result_count";
if ($lead_result_count == 0) {
     $pickupsql = "SELECT 
  leadinformation.lead_no AS `Lead No`, 
  lead_source.leadsource_value AS `Lead Source`, 
  leadinformation.mobile AS `Mobile / Alternate Mobile Number`, 
  userownerid.username AS `Lead Owner`, 
  customer_type.lead_customertype_value AS `Account Type`, 
  industry.vendorindustry_value AS `Industry`, 
  leadinformation.address AS `Address`, 
  vendor.acc_name AS `Select Account`, 
  vendor.cust_code AS `Account Code`, 
  category.lead_category_value AS `Category`,
  city.city_name AS `City`, 
  usermodifiedby.username AS `Last Modified By`, 
  leadinformation.account_name AS `Account Name`, 
  leadinformation.pincode AS `Pincode`, 
  salutation.salutationtype AS `Salutation`, 
  lead_org.lead_org_value AS `Lead Org`, 
  leadinformation.firstname AS `First Name`, 
  leadinformation.lastname AS `Last Name`, 
  usercreatorid.username AS `Created BY`, 
  leadinformation.email AS `Email`, 
  leadinformation.phone AS `Phone`, 
  leadinformation.leadname AS `Lead Name`, 
  departments.department_value AS `Department`, 
  designation.cdesignation_value AS `Designation`, 
  leadinformation.website AS `Website`, 
  leadinformation.description AS `Description`, 
  leadstatus.leadstatus_value AS `Lead Status`, 
  DATE_FORMAT(`leadinformation`.`contact_future_date`,'%d-%m-%Y') AS `Contact In Future Date`, 
  leadinformation.other_reject_reason AS `Other Reject Reason`, 
  leadinformation.reject_reason AS `Reject Reason`, 
  leadinformation.duplicate_lead_reference_id AS `Duplicate Lead Reference Id`, 
  not_contacted_reason.contacted_value AS `Not Contacted Reason`, 
  not_interested_reason.interested_value AS `Not Interested Reason`, 
  if(`leadinformation`.`dnd` is not null,if(`leadinformation` . `dnd`=0,'No','Yes'),'') AS `DND`,
  if(`leadinformation`.`data_validated` is not null,if(`leadinformation` . `data_validated`=0,'No','Yes'),'')  AS `Data Validated`, 
  if(`leadinformation`.`ready_to_pitch` is not null,if(`leadinformation` . `ready_to_pitch`=0,'No','Yes'),'') AS `Ready to Pitch`, 
   if(`leadinformation`.`email_opted_out` is not null,if(`leadinformation` . `email_opted_out`=0,'No','Yes'),'') AS `Email Opted Out`, 
  leadinformation.vm_comment AS `Manager Comment`, 
  disqualified_reason.disqualified_value AS `Disqualified Reason` ,
   DATE_FORMAT(`leadinformation`.`createdtime`,'%d-%m-%Y %H:%i:%s') AS `Created Time`, 
  DATE_FORMAT(`leadinformation`.`modifiedtime`,'%d-%m-%Y %H:%i:%s') AS `Modified Time`
  FROM leadinformation
   inner join user as userownerid on (`leadinformation`.ownerid=`userownerid`.id) 
  left join `user` as usercreatorid on (`leadinformation`.creatorid=usercreatorid.id) 
  left join `user` as usermodifiedby on (`leadinformation`.modifiedby=usermodifiedby.id) 
  left join `lead_source` as lead_source on (`leadinformation`.lead_source=lead_source.leadsourceid) 
  left join `lead_customer_type` as customer_type on (`leadinformation`.customer_type=customer_type.lead_customertype_id) 
  left join `vendor_industry` as industry on (`leadinformation`.industry=industry.vendorindustryid) 
  left join `lead_category` as category on (`leadinformation`.category=category.lead_category_id) 
  left join `city` as city on (`leadinformation`.city=city.cityid) 
  left join `salutationtype` as salutation on (`leadinformation`.salutation=salutation.salutationid) 
  left join `lead_org` as lead_org on (`leadinformation`.lead_org=lead_org.lead_org_id) 
  left join `cdepartments` as departments on (`leadinformation`.departments=departments.departmentsid) 
  left join `cdesignation` as designation on (`leadinformation`.designation=designation.cdesignationid) 
  left join `lead_status` as leadstatus on (`leadinformation`.leadstatus=leadstatus.leadstatusid) 
  left join `lead_not_contacted_reasons` as not_contacted_reason on (`leadinformation`.not_contacted_reason=not_contacted_reason.contactedid) 
  left join `lead_not_interested_reasons` as not_interested_reason on (`leadinformation`.not_interested_reason=not_interested_reason.interestedid) 
  left join `lead_disqualified_reasons` as disqualified_reason on (`leadinformation`.disqualified_reason=disqualified_reason.disqualifiedid) 
  left join `vendor_account` as vendor on (`leadinformation`.vendor=vendor.vendoraccid) 
  where leadinformation.deleted = 0  
  -- DATE(`leadinformation`.createdtime) < :today  
  order by leadinformation.leadid desc";

     $pickup_stmt = $connection->prepare($pickupsql);
     $pickup_stmt->execute();

     $pickup_filePath = $directory . "/lead_detail_$filepathDatetime.csv";
     $pickup_fp = fopen($pickup_filePath, 'w');
     if (!$pickup_fp) {
          throw new Exception("\n Unable to create or write to the Lead CSV file.");
     }

     // Column headers
     $pickup_columnCount = $pickup_stmt->columnCount();
     $pickup_headers = [];
     for ($p = 0; $p < $pickup_columnCount; $p++) {
          $meta = $pickup_stmt->getColumnMeta($p);
          $pickup_headers[] = $meta['name'];
     }
     fputcsv($pickup_fp, $pickup_headers);

     // Data rows
     while ($row = $pickup_stmt->fetch(PDO::FETCH_ASSOC)) {
          fputcsv($pickup_fp, $row);
     }

     fclose($pickup_fp);
     // echo "\n Lead Record CSV file saved to: $pickup_filePath";
     // upFileGenerateMailStatus($slot_code, $todayDatetime, $mailStatus, $leadStatus);//11 - Pickup
     // VERIFY FILE CREATED SUCCESSFULLY
     if (file_exists($pickup_filePath) && filesize($pickup_filePath) > 0) {
          upFileGenerateMailStatus($slot_code, $today, $mailStatus, $opporproductStatus);

          echo "\Lead Record  CSV file saved to: $pickup_filePath";
     } else {
          echo "\nError: Lead Record  CSV file not generated or empty. Status not updated for : $pickup_filePath";
     }
}

//  =======================================================================
//Quotes DevIT Detail
// ========================================================================
$quotesdit_result_count = checkMailStatus($slot_code, $today, $quotesditStatus);
echo "\nFile Created count for Quotes DevIT detail =$quotesdit_result_count";
if ($quotesdit_result_count == 0) {
     $pickupsql = "SELECT userownerid.username AS `Quotes DIT Owner`,   quotes_dit.quotes_dit_no AS `Quotation Number`,
  usercreatorid.username AS `Created BY`, warehouse_loc_business_entity.warehouse_name AS `Warehouse Location/ Business entity`,usermodifiedby.username AS `Last Modified By`, opportunity_name.opportunity_no AS `Opportunity ID`,quotes_dit.deal_name as `Deal Name`,
   bill_from_location.vendor_loc_name AS `Bill From Location`,
    DATE_FORMAT(`quotes_dit`.`createdtime`,'%d-%m-%Y %H:%i:%s') AS `Created Time`,
          DATE_FORMAT(`quotes_dit`.`modifiedtime`,'%d-%m-%Y %H:%i:%s') AS `Modified Time`, 
   account_name.acc_name AS `Account Name`,
   account_name.cust_code AS `Account Code`,
    quotes_dit.bill_from_address AS `Bill From Address`, 
     quotes_dit.bill_from_state AS `Bill From State`,
     DATE_FORMAT(`quotes_dit`.`quote_create_date`,'%d-%m-%Y')   AS `Quote Create Date`,
      quotes_dit.bill_from_state_code AS `Bill From State Code`, 
      quote_stage.quote_stage_value AS `Quote Stage`, 
      quotes_dit.first_comment AS `First Approval Comment`, 
      quotes_dit.second_comment AS `Second Approval Comment`, 
      quotes_dit.payment_terms AS `Payment Terms`, 
      category.product_category_value AS `Category`,
      quotes_dit.gross_profit AS `Gross Profit`,
   DATE_FORMAT(`quotes_dit`.`expiry_date`,'%d-%m-%Y')   AS `Expiry Date`,
    quotes_dit.delivery_terms AS `Delivery Terms`, 
    quotes_dit.margin AS `Margin %`,
    concat(requester_name.first_name,' ',requester_name.last_name) AS `Requester Name`,
     team_name.team_name_value AS `Team Name`,
      region.zone_value AS `Region`,     
     bill_to_location.vendor_loc_name AS `Bill To Location`,
      if(`quotes_dit`.`send_for_approval` is not null,if(`quotes_dit` . `send_for_approval`=0,'No','Yes'),'')   AS `Send For Approval`,
       quotes_dit.bill_to_address AS `Bill To Address`,
       quotes_dit.bill_to_state AS `Bill To State`, 
       quotes_dit.bill_to_state_code AS `Bill To State Code`, 
       quotes_dit.bill_to_gst AS `Bill To GSTIN No/ UIN`, 
       quotes_dit.bill_to_pan AS `Pan Number`, 
       quotes_dit.cgst_amount AS `CGST Amount`, 
       quotes_dit.sgst_amount AS `SGST Amount`, 
       quotes_dit.igst_amount AS `IGST Amount`, 
       quotes_dit.sub_total AS `Sub Total`, 
       quotes_dit.grand_total AS `Grand Total`, 
       quotes_dit.amount_in_words AS `Amount In Words`, 
       quotes_dit.terms_and_condition AS `Terms And Conditions` FROM quotes_dit
  inner join user as userownerid on (`quotes_dit`.ownerid=`userownerid`.id) 
  left join `user` as usercreatorid on (`quotes_dit`.creatorid=usercreatorid.id) 
  left join `user` as usermodifiedby on (`quotes_dit`.modifiedby=usermodifiedby.id)
  left join `quotesdit_stage` as quote_stage on (`quotes_dit`.quote_stage=quote_stage.quote_stage_id)
  left join `oppr_product_category` as category on (`quotes_dit`.category=category.product_category_id)
  left join `oppr_team_name` as team_name on (`quotes_dit`.team_name=team_name.team_name_id)
  left join `oppr_zone` as region on (`quotes_dit`.region=region.zone_id)
  left join `warehouse` as warehouse_loc_business_entity on (`quotes_dit`.warehouse_loc_business_entity=warehouse_loc_business_entity.warehouse_id)
  left join `opportunity` as opportunity_name on (`quotes_dit`.opportunity_name=opportunity_name.opportunity_id)
  left join `vendor_account` as account_name on (`quotes_dit`.account_name=account_name.vendoraccid)
  left join `vendor_locations` as bill_from_location on (`quotes_dit`.bill_from_location=bill_from_location.vendorloc_id)
  left join `vendor_locations` as bill_to_location on (`quotes_dit`.bill_to_location=bill_to_location.vendorloc_id)
  left join `contacts` as requester_name on (`quotes_dit`.requester_name=requester_name.contacts_id) 
  where quotes_dit.deleted = 0 
  --   DATE(`quotes_dit`.createdtime) < :today 
  order by quotes_dit.quotes_dit_id desc";

     $pickup_stmt = $connection->prepare($pickupsql);
     $pickup_stmt->execute();

     $pickup_filePath = $directory . "/quotesdit_detail_$filepathDatetime.csv";
     $pickup_fp = fopen($pickup_filePath, 'w');
     if (!$pickup_fp) {
          throw new Exception("\n Unable to create or write to the Quotes DevIT CSV file.");
     }

     // Column headers
     $pickup_columnCount = $pickup_stmt->columnCount();
     $pickup_headers = [];
     for ($p = 0; $p < $pickup_columnCount; $p++) {
          $meta = $pickup_stmt->getColumnMeta($p);
          $pickup_headers[] = $meta['name'];
     }
     fputcsv($pickup_fp, $pickup_headers);

     // Data rows
     while ($row = $pickup_stmt->fetch(PDO::FETCH_ASSOC)) {
          fputcsv($pickup_fp, $row);
     }

     fclose($pickup_fp);
     // echo "\n Quotes DevIT Record CSV file saved to: $pickup_filePath";
     // upFileGenerateMailStatus($slot_code, $todayDatetime, $mailStatus, $quotesditStatus);
     // VERIFY FILE CREATED SUCCESSFULLY
     if (file_exists($pickup_filePath) && filesize($pickup_filePath) > 0) {
          upFileGenerateMailStatus($slot_code, $today, $mailStatus, $quotesditStatus);

          echo "\Quotes DevIT  CSV file saved to: $pickup_filePath";
     } else {
          echo "\nError: Quotes DevIT  CSV file not generated or empty. Status not updated for : $pickup_filePath";
     }
}

//  =======================================================================
//SalesOrder DevIT Detail
// ========================================================================
$sodit_result_count = checkMailStatus($slot_code, $today, $soditStatus);
echo "\nFile Created count for Sales Order DevIT detail =$sodit_result_count";
if ($sodit_result_count == 0) {
     $pickupsql = "SELECT  
  userownerid.username AS `Sales DIT Owner`,
  deal_name.opportunity_no AS `Deal Name`,
  account_name.acc_name AS `Account Name`, 
  account_name.cust_code AS `Account Code`, 
  salesorder_dit.salesorder_dit_no AS `Sales Order No`,
  so_stage.stage_value AS `SO Stage`, 
  procurement_executive.username AS `Procurement Executive`,  
  salesorder_dit.margin_percentage AS `Margin %`,
  salesorder_dit.gross_profit AS `Gross Profit`,
  quote_name.quotes_dit_no AS `Quote Name`, 
  concat(requester_name_contact_name.first_name,' ',requester_name_contact_name.last_name) AS `Requester Name/Contact name`,
  so_type.so_type_value AS `SO Type`,
  team.team_name_value AS `Team`,
  if(`salesorder_dit`.`send_for_approval` is not null,if(`salesorder_dit` . `send_for_approval`=0,'No','Yes'),'') AS `Send For Approval`,
  salesorder_dit.first_approval_comment AS `First Approval Comment`,
  salesorder_dit.second_approval_comment AS `Second Approval Comment`,
   delivery_location.vendor_loc_name AS `Delivery Location`,
  salesorder_dit.bill_to_legal_name AS `Bill To Legal Name`,
  salesorder_dit.address AS `Address`,
  salesorder_dit.state AS `State`,
  salesorder_dit.city AS `City`,
  salesorder_dit.state_code AS `State Code`,
  salesorder_dit.pin_code AS `Pin Code`, 
  salesorder_dit.gst AS `GST`,
  salesorder_dit.pan AS `Pan`,
  salesorder_dit.cgst_amount AS `CGST Amount`,
  salesorder_dit.sgst_amount AS `SGST Amount`, 
  salesorder_dit.igst_amount AS `IGST Amount`,
  salesorder_dit.basic_amount AS `Basic Amount`,
  salesorder_dit.grand_total AS `Grand Total`,
  salesorder_dit.amount_in_words AS `Amount In Words`,
  salesorder_dit.customer_po_num AS `Customer PO Number`,
  customer_payment_terms.payment_terms_value AS `Customer Payment Terms`,
  DATE_FORMAT(`salesorder_dit`.`customer_po_date`,'%d-%m-%Y') AS `Customer PO Date`,
  DATE_FORMAT(`salesorder_dit`.`po_received_date`,'%d-%m-%Y') AS `PO Received Date`,
  salesorder_dit.terms_and_condition AS `Terms And Condition`,
  concat(first_level_name.first_name,' ',first_level_name.last_name) AS `ESCALATION MATRIX - FINANCE First Level Name`,
  concat(wh_first_level_name.first_name,' ',wh_first_level_name.last_name) AS `ESCALATION MATRIX - WAREHOUSE First Level Name`,
  concat(pro_first_level_name.first_name,' ',pro_first_level_name.last_name) AS `ESCALATION MATRIX - PRODUREMENT First Level Name`,
  salesorder_dit.first_level_number AS `ESCALATION MATRIX - FINANCE First Level Number`,
  salesorder_dit.wh_first_level_number AS `ESCALATION MATRIX - WAREHOUSE First Level Number`,  
  salesorder_dit.pro_first_level_number AS `ESCALATION MATRIX - PRODUREMENT First Level Number`,
  salesorder_dit.first_level_email AS `ESCALATION MATRIX - FINANCE First Level Email Address`,
  salesorder_dit.wh_first_level_email AS `ESCALATION MATRIX - WAREHOUSE First Level Email Address`,
  salesorder_dit.pro_first_level_email AS `ESCALATION MATRIX - PRODUREMENT First Level Email Address`,
  salesorder_dit.first_level_designation AS `ESCALATION MATRIX - FINANCE First Level designation`,  
  salesorder_dit.pro_first_level_designation AS `ESCALATION MATRIX - PRODUREMENT First Level designation`, 
  salesorder_dit.wh_first_level_designation AS `ESCALATION MATRIX - WAREHOUSE First Level designation`,  
  concat(second_level_name.first_name,' ',second_level_name.last_name) AS `ESCALATION MATRIX - FINANCE Second Level Name`,
  concat(pro_second_level_name.first_name,' ',pro_second_level_name.last_name) AS `ESCALATION MATRIX - PRODUREMENT Second Level Name`,
  concat(wh_second_level_name.first_name,' ',wh_second_level_name.last_name) AS `ESCALATION MATRIX - WAREHOUSE Second Level Name`,
  salesorder_dit.second_level_number AS `ESCALATION MATRIX - FINANCE Second Level Number`,
  salesorder_dit.pro_second_level_number AS `ESCALATION MATRIX - PRODUREMENT Second Level Number`,
  salesorder_dit.wh_second_level_number AS `ESCALATION MATRIX - WAREHOUSE Second Level Number`,
  salesorder_dit.second_level_email AS `ESCALATION MATRIX - FINANCE Second Level Email Address`,
  salesorder_dit.pro_second_level_email AS `ESCALATION MATRIX - PRODUREMENT Second Level Email Address`,
  salesorder_dit.wh_second_level_email AS `ESCALATION MATRIX - WAREHOUSE Second Level Email Address`,
  salesorder_dit.second_level_designation AS `ESCALATION MATRIX - FINANCE Second Level designation`,
  salesorder_dit.pro_second_level_designation AS `ESCALATION MATRIX - PRODUREMENT Second Level designation`,
  salesorder_dit.wh_second_level_designation AS `ESCALATION MATRIX - WAREHOUSE Second Level designation`,  
  timeline_commited.yesno_options_value AS `Specify,any different delivery timeline commited`,  
  DATE_FORMAT(`salesorder_dit`.`timeline_commited_date`,'%d-%m-%Y %H:%i:%s') AS `Please confirm the timeline commited date`,
  case_scattered_delivery.yesno_options_value AS `Is there a case of Scattered Delivery`,
  case_scattered_delivery_files.name AS `Upload Scattered Delivery file`, 
  additional_service_offered.yesno_options_value AS `Any Additional Service/Installation Offered`,   
  free_chargeable_offered_services.services_value AS `Free/Chargeable offered Services/Installation`, 
  scope_work_installation.yesno_options_value AS `Is there scope of work of installation`,
  scope_work_installation_doc.name AS `Upload Scope of work of Installation file`,
  DATE_FORMAT(`salesorder_dit`.`estimate_date_delivery`,'%d-%m-%Y %H:%i:%s') AS `Estimate Date of Delivery`,
  DATE_FORMAT(`salesorder_dit`.`actual_date_delivery`,'%d-%m-%Y %H:%i:%s')  AS `Actual Date of Delivery`,
  
  DATE_FORMAT(`salesorder_dit`.`createdtime`,'%d-%m-%Y %H:%i:%s') AS `Created Time`,
   
  DATE_FORMAT(`salesorder_dit`.`modifiedtime`,'%d-%m-%Y %H:%i:%s') AS `Modified Time`,
  usercreatorid.username AS `Created BY`, 
  usermodifiedby.username AS `Last Modified By`  
  FROM salesorder_dit
  LEFT JOIN attachments as scope_work_installation_doc on salesorder_dit.scope_work_installation_doc = scope_work_installation_doc.attachmentsid 
  LEFT JOIN attachments as case_scattered_delivery_files on salesorder_dit.case_scattered_delivery_files = case_scattered_delivery_files.attachmentsid
  inner join user as userownerid on (`salesorder_dit`.ownerid=`userownerid`.id) 
  left join `user` as usercreatorid on (`salesorder_dit`.creatorid=usercreatorid.id) 
  left join `user` as usermodifiedby on (`salesorder_dit`.modifiedby=usermodifiedby.id) 
  left join `user` as procurement_executive on (`salesorder_dit`.procurement_executive=procurement_executive.id) 
  left join `yesno_options` as timeline_commited on (`salesorder_dit`.timeline_commited=timeline_commited.yesno_options_id) 
  left join `yesno_options` as case_scattered_delivery on (`salesorder_dit`.case_scattered_delivery=case_scattered_delivery.yesno_options_id) 
  left join `yesno_options` as additional_service_offered on (`salesorder_dit`.additional_service_offered=additional_service_offered.yesno_options_id) 
  left join `so_stage` as so_stage on (`salesorder_dit`.so_stage=so_stage.stage_id) 
  left join `so_services` as free_chargeable_offered_services on (`salesorder_dit`.free_chargeable_offered_services=free_chargeable_offered_services.services_id) 
  left join `yesno_options` as scope_work_installation on (`salesorder_dit`.scope_work_installation=scope_work_installation.yesno_options_id) 
  left join `opp_payment_terms` as customer_payment_terms on (`salesorder_dit`.customer_payment_terms=customer_payment_terms.payment_terms_id) 
  left join `so_type` as so_type on (`salesorder_dit`.so_type=so_type.so_type_id) 
  left join `oppr_team_name` as team on (`salesorder_dit`.team=team.team_name_id) 
  left join `vendor_account` as account_name on (`salesorder_dit`.account_name=account_name.vendoraccid)
  left join `contacts` as first_level_name on (`salesorder_dit`.first_level_name=first_level_name.contacts_id) 
  left join `contacts` as wh_first_level_name on (`salesorder_dit`.wh_first_level_name=wh_first_level_name.contacts_id) 
  left join `contacts` as pro_first_level_name on (`salesorder_dit`.pro_first_level_name=pro_first_level_name.contacts_id) 
  left join `opportunity` as deal_name on (`salesorder_dit`.deal_name=deal_name.opportunity_id) 
  left join `contacts` as second_level_name on (`salesorder_dit`.second_level_name=second_level_name.contacts_id) 
  left join `contacts` as pro_second_level_name on (`salesorder_dit`.pro_second_level_name=pro_second_level_name.contacts_id) 
  left join `contacts` as wh_second_level_name on (`salesorder_dit`.wh_second_level_name=wh_second_level_name.contacts_id) 
  left join `quotes_dit` as quote_name on (`salesorder_dit`.quote_name=quote_name.quotes_dit_id) 
  left join `contacts` as requester_name_contact_name on (`salesorder_dit`.requester_name_contact_name=requester_name_contact_name.contacts_id) 
  left join `vendor_locations` as delivery_location on (`salesorder_dit`.delivery_location=delivery_location.vendorloc_id) 
  where salesorder_dit.deleted = 0 
 -- DATE(`salesorder_dit`.createdtime) < :today  
  order by salesorder_dit.salesorder_dit_id desc";

     $pickup_stmt = $connection->prepare($pickupsql);
     $pickup_stmt->execute();

     $pickup_filePath = $directory . "/saleordersdit_detail_$filepathDatetime.csv";
     $pickup_fp = fopen($pickup_filePath, 'w');
     if (!$pickup_fp) {
          throw new Exception("\n Unable to create or write to the SO DevIT CSV file.");
     }

     // Column headers
     $pickup_columnCount = $pickup_stmt->columnCount();
     $pickup_headers = [];
     for ($p = 0; $p < $pickup_columnCount; $p++) {
          $meta = $pickup_stmt->getColumnMeta($p);
          $pickup_headers[] = $meta['name'];
     }
     fputcsv($pickup_fp, $pickup_headers);

     // Data rows
     while ($row = $pickup_stmt->fetch(PDO::FETCH_ASSOC)) {
          fputcsv($pickup_fp, $row);
     }

     fclose($pickup_fp);
     // echo "\n SO DevIT Record CSV file saved to: $pickup_filePath";
     // upFileGenerateMailStatus($slot_code, $today, $mailStatus, $soditStatus);//11 - Pickup
     // VERIFY FILE CREATED SUCCESSFULLY
     if (file_exists($pickup_filePath) && filesize($pickup_filePath) > 0) {
          upFileGenerateMailStatus($slot_code, $today, $mailStatus, $quotesditStatus);

          echo "\  SO DevIT  CSV file saved to: $pickup_filePath";
     } else {
          echo "\nError:  SO DevIT  CSV file not generated or empty. Status not updated for : $pickup_filePath";
     }
}

//  =======================================================================
//Purchase Order DevIT Detail
// ========================================================================
$podit_result_count = checkMailStatus($slot_code, $today, $poditStatus);
echo "\nFile Created count for Purchase Order DevIT detail =$podit_result_count";
if ($podit_result_count == 0) {
     $pickupsql = "SELECT 
  userownerid.username AS `Purchase Order DIT Owner`,
  purchase_order_dit.purchaseorder_dit_no AS `Purchase Order No`,
  po_Issued_entity_name.warehouse_name AS `PO Issued Entity Name`, 
  -- reference_number.salesorder_dit_no AS `Reference Number`, 
  GROUP_CONCAT(DISTINCT reference_number.salesorder_dit_no 
             ORDER BY reference_number.salesorder_dit_no 
             SEPARATOR ', ') AS `Reference Number`,
  po_type.purchaseorder_potype_value AS `PO Type`,
  stage.purchaseorder_value AS `Stage`, 
  purchase_order_dit.delivery_instruction AS `Delivery Instruction`, 
  purchase_order_dit.terms_condition AS `Terms & Condition`, 
  credit_terms.purchaseorder_cerdit_terms_value AS `Credit Terms`, 
  DATE_FORMAT(`purchase_order_dit`.`po_expiry_date`,'%d-%m-%Y')  AS `PO Expiry Date`,
  DATE_FORMAT(`purchase_order_dit`.`estimate_time_delivery`,'%d-%m-%Y')  AS `Estimate Time Delivery`, 
  if(`purchase_order_dit`.`send_for_approval` is not null,if(`purchase_order_dit` . `send_for_approval`=0,'No','Yes'),'') AS `Send For Approval`, 
  vendor_name.acc_name AS `Vendor Name`, 
  vendor_name.cust_code AS `Vendor Code`, 
  location.vendor_loc_name AS `Vendor Location`,
   purchase_order_dit.address AS `Vendor Address`,  
   purchase_order_dit.gst_number AS `Vendor GST Number`, 
 purchase_order_dit.state_code AS `Vendor State Code`, 
  purchase_order_dit.source_of_supply AS `Source Of Supply`, 
  bill_entitiy_name.warehouse_name AS `BILL TO ADDRESS Entitiy Name`,
 purchase_order_dit.bill_location AS `BILL TO ADDRESS Location`, 
  purchase_order_dit.bill_address AS `BILL TO ADDRESS Address`, 
  purchase_order_dit.bill_state_code AS `BILL TO ADDRESS State Code`,  
  purchase_order_dit.bill_gst_number AS `BILL TO ADDRESS GST Number`, 
 purchase_order_dit.destination_of_supply AS `BILL TO ADDRESS Destination Of Supply`, 
  delivery_entitiy_name.warehouse_name AS `Delivery Entitiy Name`,
   purchase_order_dit.delivery_location AS `Delivery Location`, 
  purchase_order_dit.delivery_address AS `Delivery Address`, 
   purchase_order_dit.delivery_gst_number AS `Delivery GST Number`, 
 purchase_order_dit.delivery_state_code AS `Delivery State Code`, 
 purchase_order_dit.delivery_destination_of_supply AS `Delivery Destination Of Supply`, 
 DATE_FORMAT(`purchase_order_dit`.`purchase_order_date`,'%d-%m-%Y') AS `Purchase Order Date`, 
 purchase_order_dit.sub_total AS `Sub Total`,
  purchase_order_dit.cgst_amount AS `CGST Amount`, 
  purchase_order_dit.sgst_amount AS `SGST Amount`, 
  purchase_order_dit.igst_amount AS `IGST Amount`, 
 purchase_order_dit.total AS `Total`, 
   purchase_order_dit.first_approval_comment AS `First Approval Comment`, 
  purchase_order_dit.second_approval_comment AS `Second Approval Comment` ,
  usercreatorid.username AS `Created BY`, 
  usermodifiedby.username AS `Last Modified By`, 
  DATE_FORMAT(`purchase_order_dit`.`createdtime`,'%d-%m-%Y %H:%i:%s') AS `Created Time`, 
  DATE_FORMAT(`purchase_order_dit`.`modifiedtime`,'%d-%m-%Y %H:%i:%s') AS `Modified Time`
  FROM purchase_order_dit 
  inner join user as userownerid on (`purchase_order_dit`.ownerid=`userownerid`.id) 
  left join `user` as usercreatorid on (`purchase_order_dit`.creatorid=usercreatorid.id) 
  left join `user` as usermodifiedby on (`purchase_order_dit`.modifiedby=usermodifiedby.id) 
  left join `purchaseorder_potype` as po_type on (`purchase_order_dit`.po_type=po_type.purchaseorder_potype_id) 
  left join `purchaseorder_stage` as stage on (`purchase_order_dit`.stage=stage.purchaseorder_stage_id) 
  left join `purchaseorder_cerdit_terms` as credit_terms on (`purchase_order_dit`.credit_terms=credit_terms.purchaseorder_cerdit_terms_id) 
  left join `warehouse` as delivery_entitiy_name on (`purchase_order_dit`.delivery_entitiy_name=delivery_entitiy_name.warehouse_id) 
  left join `warehouse` as bill_entitiy_name on (`purchase_order_dit`.bill_entitiy_name=bill_entitiy_name.warehouse_id) 
  left join `vendor_account` as vendor_name on (`purchase_order_dit`.vendor_name=vendor_name.vendoraccid) 
  left join `warehouse` as po_Issued_entity_name on (`purchase_order_dit`.po_Issued_entity_name=po_Issued_entity_name.warehouse_id) 
  -- left join `salesorder_dit` as reference_number on (`purchase_order_dit`.reference_number=reference_number.salesorder_dit_id) 
  LEFT JOIN salesorder_dit AS reference_number ON FIND_IN_SET(reference_number.salesorder_dit_id, `purchase_order_dit`.reference_number)
  left join `vendor_locations` as location on (`purchase_order_dit`.location=location.vendorloc_id) 

  where purchase_order_dit.deleted = 0 
 -- DATE(`purchase_order_dit`.createdtime) < :today  
   -- added by ptpatel on date 01-11-2025
     GROUP BY `purchase_order_dit`.purchaseorder_dit_id
  order by purchase_order_dit.purchaseorder_dit_id desc;";

     $pickup_stmt = $connection->prepare($pickupsql);
     $pickup_stmt->execute();

     $pickup_filePath = $directory . "/purchaseordersdit_detail_$filepathDatetime.csv";
     $pickup_fp = fopen($pickup_filePath, 'w');
     if (!$pickup_fp) {
          throw new Exception("\n Unable to create or write to the PO DevIT CSV file.");
     }

     // Column headers
     $pickup_columnCount = $pickup_stmt->columnCount();
     $pickup_headers = [];
     for ($p = 0; $p < $pickup_columnCount; $p++) {
          $meta = $pickup_stmt->getColumnMeta($p);
          $pickup_headers[] = $meta['name'];
     }
     fputcsv($pickup_fp, $pickup_headers);

     // Data rows
     while ($row = $pickup_stmt->fetch(PDO::FETCH_ASSOC)) {
          fputcsv($pickup_fp, $row);
     }

     fclose($pickup_fp);
     // echo "\n PO DevIT Record CSV file saved to: $pickup_filePath";
     // upFileGenerateMailStatus($slot_code, $todayDatetime, $mailStatus, $poditStatus);
     // VERIFY FILE CREATED SUCCESSFULLY
     if (file_exists($pickup_filePath) && filesize($pickup_filePath) > 0) {
          upFileGenerateMailStatus($slot_code, $today, $mailStatus, $poditStatus);

          echo "\PO DevIT CSV file saved to: $pickup_filePath";
     } else {
          echo "\nError: PO DevIT CSV file not generated or empty. Status not updated for : $pickup_filePath";
     }
}
