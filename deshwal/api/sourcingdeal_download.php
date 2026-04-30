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


     function checkMailStatus($date_cond)
     {
          // Assuming db_connect() returns a PDO connection
          $mycon = db_connect();
          $result_count = 1;

          // Correct query without quotes around :date_cond
          $query_mailstatus = "SELECT mail_run_date FROM mail_status WHERE mail_type = 1 AND mail_run_date = :date_cond";

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
              VALUES (:date_cond, 1, :mailStatus, NOW(), NOW())";

          // Step 2: Prepare the statement
          $stmt = $mycon->prepare($query);

          // Step 3: Bind the parameters
          $stmt->bindParam(':date_cond', $date_cond, PDO::PARAM_STR);
          $stmt->bindParam(':mailStatus', $mailStatus, PDO::PARAM_STR);

          // Step 4: Execute the query
          $stmt->execute();
     }

     $result_count = checkMailStatus($today);
     echo "<br>Mail send count=$result_count";
     if ($result_count == 0) {//startmail
          // Your query with headers in SELECT
          $sql = "
        SELECT sourcingdeal.sourcingdeal_no as 'Sourcing Deal No',sourcingdeal.deal_name as 'Sourcing Deal Name',
          va.acc_name as 'Account Name',
          product_costing.product_costing_no as 'Product Costing No',
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
          product_costing_detail.total_quoted_price_exclusive_gst as 'Total Quoted price (Exclusive GST)',
          product_costing_detail.logistics_cost as 'Logistics Cost',
          product_costing_detail.total_logistics_cost as 'Total Logistics Cost'
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
     AND DATE(product_costing.createdtime) < :today
   ORDER BY product_costing.product_costing_id DESC
   ";

          $stmt = $connection->prepare($sql);
          $stmt->execute(['today' => $today]);

          // Get directory path dynamically
          $directory = __DIR__ . '/exports';
          if (!is_dir($directory)) {
               mkdir($directory, 0777, true);
          }

          $filePath = $directory . "/sourcingdeal_productdetail_$today.csv";
          $fp = fopen($filePath, 'w');
          if (!$fp) {
               throw new Exception("Unable to create or write to the CSV file.");
          }

          // Column headers
          $columnCount = $stmt->columnCount();
          $headers = [];
          for ($i = 0; $i < $columnCount; $i++) {
               $meta = $stmt->getColumnMeta($i);
               $headers[] = $meta['name'];
          }
          fputcsv($fp, $headers);

          // Data rows
          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
               fputcsv($fp, $row);
          }

          fclose($fp);
          echo "CSV file saved to: $filePath";

          ///save sourcing deal
          $sql2 = "select sourcingdeal.sourcingdeal_no as 'Sourcing Deal No',concat(userownerid.first_name,' ',userownerid.last_name) as `Sourcing Deal Owner`,sourcingdeal.deal_name as `Sourcing Deal Name`,DATE_FORMAT(`sourcingdeal`.`closing_date`,'%d-%m-%Y') as `Closure Date`,monthsclosure_month.`months_value` as `Closure Month`,closure_weekclosure_week.`closure_week_value` as `Closure Week`,vendor_accountvendor_account_name.acc_name as `Account Name`,oppr_business_typebusiness_type.`business_type_value` as `Business Type`,contactscontact_name.first_name as `Contact Name`,sourcingdeal.contact_email as `Contact Email`,sourcingdeal.role as `Role`,sourcingdeal.designation as `Designation`,sourcingdeal.department as `Department`,sourcingdeal.contact_mobile as `Contact Mobile`,sourcingdeal.opportunity_tentative_value as `Sourcing Deal Tentative Value`,sourcingdeal_stagestage.`stage_value` as `Stage`,loss_reasonloss_reason.`loss_reason_value` as `Lost Reason`,sourcingdeal.remarks as Remarks,sourcing_payment_typepayment_type.`sourcing_payment_type_value` as `Payment Type`,forecast_categoryforecast_category.`forecast_category_value` as `Forecast Category`,lead_categorycategory.`lead_category_value` as `Category`,sourcingdeal.pickup_request_id as `Pickup Request Id`,currencycurrency.`currency_value` as `Currency`,sourcingdeal.exchange_rate as `Exchange Rate`,sourcingdeal.terms_conditions as `Terms and Conditions`,sd_iscontractis_contract.`iscontract_value` as `IsContract`,type_of_contracttype_of_contract.`type_of_contract_value` as `Type of Contract`,lead_sourcelead_source.`leadsource_value` as `Lead Source`,roleoem.`rolename` as `OEM`,roleoem_manager.`rolename` as `OEM Manager`,concat(useroem_manager_name.first_name,' ',useroem_manager_name.last_name) as `OEM Manager Name`,sourcingdeal.oem_manager_email as `OEM Manager Email`,opportunity_scoreopportunity_score.`opportunity_score_value` as `Opportunity Score`,campaigncampaign_source.campaign_subject as `Campaign Source`,sourcingdeal.probability as Probability,pricing_typepricing_type.`pricing_type_value` as `Pricing Type`,oppr_inspection_requiredinspection_required.`inspection_required_value` as `Inspection Required`,if(`sourcingdeal` . `special_pricing` is not null,if(`sourcingdeal` . `special_pricing`=0,'No','Yes'),'') as `Submit Special Pricing`, if(`sourcingdeal` . `submit_for_pricing` is not null,if(`sourcingdeal` . `submit_for_pricing`=0,'No','Yes'),'') as `Submit For Pricing`, if(`sourcingdeal` . `costing_done` is not null,if(`sourcingdeal` . `costing_done`=0,'No','Yes'),'') as `Costing Done`, if(`sourcingdeal` . `ceo_approval` is not null,if(`sourcingdeal` . `ceo_approval`=0,'No','Yes'),'') as `CEO Approval`, usercreatorid.username as `Created BY`,usermodifiedby.username as  `Last Modified By`,sourcingdeal.createdtime as `Created Time`,sourcingdeal.modifiedtime as `Modified Time`,sourcingdeal.total_sourcing_deal_amount as `Total Sourcng Deal Amount`,sourcingdeal.total_sourcing_deal_cost as `Total Sourcing Deal Cost`,sourcingdeal.total_sourcing_deal_sale as `Total Sourcing Deal Sale`,sourcingdeal.service_sale as `Service Sale`,sourcingdeal.service_cost as `Service Cost`,sourcingdeal.product_cost as `Product Cost`,sourcingdeal.product_sale as `Product Sale`,sourcingdeal.margin as Margin,sourcingdeal.margin_percentage as `Margin%` 
          ,userdeshwal_isr.first_name as 'Deshwal ISR',
          useraccount_manager.first_name as 'Account Manager'
          from `sourcingdeal` left join `user` as userownerid on (`sourcingdeal`.ownerid=userownerid.id) left join months as monthsclosure_month on (`sourcingdeal`.`closure_month`=monthsclosure_month.months_id) left join closure_week as closure_weekclosure_week on (`sourcingdeal`.`closure_week`=closure_weekclosure_week.closure_weekid) LEFT OUTER JOIN vendor_account as vendor_accountvendor_account_name on (`sourcingdeal`.vendor_account_name=vendor_accountvendor_account_name.vendoraccid) left join oppr_business_type as oppr_business_typebusiness_type on (`sourcingdeal`.`business_type`=oppr_business_typebusiness_type.business_type_id) LEFT OUTER JOIN contacts as contactscontact_name on (`sourcingdeal`.contact_name=contactscontact_name.contacts_id) left join sourcingdeal_stage as sourcingdeal_stagestage on (`sourcingdeal`.`stage`=sourcingdeal_stagestage.stage_id) left join loss_reason as loss_reasonloss_reason on (`sourcingdeal`.`loss_reason`=loss_reasonloss_reason.loss_reasonid) left join sourcing_payment_type as sourcing_payment_typepayment_type on (`sourcingdeal`.`payment_type`=sourcing_payment_typepayment_type.sourcing_payment_typeid) left join forecast_category as forecast_categoryforecast_category on (`sourcingdeal`.`forecast_category`=forecast_categoryforecast_category.forecast_categoryid) left join lead_category as lead_categorycategory on (`sourcingdeal`.`category`=lead_categorycategory.lead_category_id) left join currency as currencycurrency on (`sourcingdeal`.`currency`=currencycurrency.currencyid) left join sd_iscontract as sd_iscontractis_contract on (`sourcingdeal`.`is_contract`=sd_iscontractis_contract.iscontract_id) left join type_of_contract as type_of_contracttype_of_contract on (`sourcingdeal`.`type_of_contract`=type_of_contracttype_of_contract.type_of_contractid) left join lead_source as lead_sourcelead_source on (`sourcingdeal`.`lead_source`=lead_sourcelead_source.leadsourceid) left join role as roleoem on (`sourcingdeal`.`oem`=roleoem.roleid) left join role as roleoem_manager on (`sourcingdeal`.`oem_manager`=roleoem_manager.roleid) left join `user` as useroem_manager_name on (`sourcingdeal`.oem_manager_name=useroem_manager_name.id) left join opportunity_score as opportunity_scoreopportunity_score on (`sourcingdeal`.`opportunity_score`=opportunity_scoreopportunity_score.opportunity_scoreid) LEFT OUTER JOIN campaign as campaigncampaign_source on (`sourcingdeal`.campaign_source=campaigncampaign_source.campaign_id) left join pricing_type as pricing_typepricing_type on (`sourcingdeal`.`pricing_type`=pricing_typepricing_type.pricing_type_id) left join oppr_inspection_required as oppr_inspection_requiredinspection_required on (`sourcingdeal`.`inspection_required`=oppr_inspection_requiredinspection_required.inspection_required_id) left join `user` as usercreatorid on (`sourcingdeal`.creatorid=usercreatorid.id) left join `user` as usermodifiedby on (`sourcingdeal`.modifiedby=usermodifiedby.id) inner join user as owner on (owner.id=`sourcingdeal`.ownerid)           
          LEFT OUTER JOIN user as userdeshwal_isr on (`sourcingdeal`.deshwal_isr=userdeshwal_isr.id) 
          LEFT OUTER JOIN user as useraccount_manager on (`sourcingdeal`.account_manager=useraccount_manager.id)
          where `sourcingdeal`.deleted=0 and `sourcingdeal`.is_temp = 0 and  DATE(sourcingdeal.createdtime) < :today order by `sourcingdeal`.sourcingdeal_id DESC";
          $stmt = $connection->prepare($sql2);
          $stmt->execute(['today' => $today]);
          $filePath = $directory . "/sourcingdeal_$today.csv";
          $fp = fopen($filePath, 'w');
          if (!$fp) {
               throw new Exception("Unable to create or write to the CSV file.");
          }

          // Column headers
          $columnCount = $stmt->columnCount();
          $headers = [];
          for ($i = 0; $i < $columnCount; $i++) {
               $meta = $stmt->getColumnMeta($i);
               $headers[] = $meta['name'];
          }
          fputcsv($fp, $headers);

          // Data rows
          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
               fputcsv($fp, $row);
          }

          fclose($fp);
          echo "\nCSV file saved to: $filePath";
          /**code for call Module CSV file creation and store into export folder */


          // --- 1. Get metadata (tab + autonumber field column) ---
          /*  $call_meta_sql = "SELECT t.tabid, t.tablename, t.tablekeyid, f.columnname, t.tablabel
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
            $call_filePath = $directory . "/call_details_$today.csv";
            $call_fp = fopen($call_filePath, 'w');
            if (!$call_fp) {
                 throw new Exception("Unable to create or write to the CSV file.");
            }


            $call_headers = ["Call Owner","Related Module","Related Record","Subject","Comment","Outgoing Call Status","Call Start Time",
            "Call End Time","Call Duration","Call Type","Call Purpose","Call Agenda","Call Result"];
            fputcsv($call_fp, $call_headers);

            // Data rows
           foreach ($calls as $row) {
            fputcsv($call_fp, [
                 $row['Call Owner'],
                 $row['Related Module'],
                 $row['Related Record'],
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
            ]);
            }

            fclose($call_fp);
            echo "\nCSV file saved to: $call_filePath";die;
            /**code end for call Module CSV file creation and store into export folder*/

          /***meeting module CSV file creation and store into export folder */
          /*$meeting_sql = "select 
               concat(userownerid.first_name,' ',userownerid.last_name) as 'Meeting Owner',
               meeting_information.title as 'Title',
               vendor_locationslocation.vendor_loc_name as 'Location',
               if(meeting_information . all_day is not null,if(meeting_information . all_day=0,'No','Yes'),'') as 'All Day',
               DATE_FORMAT(meeting_information.from,'%d-%m-%Y %H:%i:%s') as 'From',
               DATE_FORMAT(meeting_information.to,'%d-%m-%Y %H:%i:%s') as 'To',
               concat(userhost.first_name,' ',userhost.last_name) as 'Host',
               concat(usersolution_architect.first_name,' ',usersolution_architect.last_name) as 'Solution Architect',
               GROUP_CONCAT(concat(contacts_alias.first_name,' ',contacts_alias.last_name) ORDER BY contacts_alias.contacts_id) AS 'External Participants',
               GROUP_CONCAT(concat(user_alias.first_name,' ',if(user_alias.last_name is null,'',user_alias.last_name)) ORDER BY user_alias.id) AS 'Internal Participants',
               tab.tablabel as 'Related To',
               CASE 
                    WHEN meeting_information.related_to = (select tabid from tab where tab.name = 'leads') 
                         THEN leadinformation.leadname 
                    WHEN meeting_information.related_to = (select tabid from tab where tab.name = 'opportunities') 
                         THEN opportunity.opportunity_no 
                    WHEN meeting_information.related_to = (select tabid from tab where tab.name = 'vendoraccount') 
                         THEN vendor_account.acc_name 
                    WHEN meeting_information.related_to = (select tabid from tab where tab.name = 'quotes') 
                         THEN quotes.quotes_no 
                    WHEN meeting_information.related_to = (select tabid from tab where tab.name = 'contacts') 
                         THEN contacts.contact_no 
                    WHEN meeting_information.related_to = (select tabid from tab where tab.name = 'pickup') 
                         THEN pickup.pickup_no 
                    WHEN meeting_information.related_to = (select tabid from tab where tab.name = 'iqcdesktop') 
                         THEN iqc_desktop.iqc_desktop_no 
                    WHEN meeting_information.related_to = (select tabid from tab where tab.name = 'iqclaptop') 
                         THEN iqc_laptop.iqc_laptop_no 
                    WHEN meeting_information.related_to = (select tabid from tab where tab.name = 'iqctft') 
                         THEN iqc_tft.iqc_tft_no 
                    WHEN meeting_information.related_to = (select tabid from tab where tab.name = 'grn') 
                         THEN grn.grn_no 
                    WHEN meeting_information.related_to = (select tabid from tab where tab.name = 'purchaseorder') 
                         THEN purchase_order.purchase_order_no 
                    WHEN meeting_information.related_to = (select tabid from tab where tab.name = 'vendorlocations') 
                         THEN vendor_locations.vendor_loc_no 
                    WHEN meeting_information.related_to = (select tabid from tab where tab.name = 'drilling') 
                         THEN drilling.drilling_no 
                    WHEN meeting_information.related_to = (select tabid from tab where tab.name = 'degaussing') 
                         THEN degaussing.degaussing_no 
                    WHEN meeting_information.related_to = (select tabid from tab where tab.name = 'shredding') 
                         THEN shredding.shredding_no 
                    WHEN meeting_information.related_to = (select tabid from tab where tab.name = 'datawiping') 
                         THEN data_wiping.data_wiping_no 
                    WHEN meeting_information.related_to = (select tabid from tab where tab.name = 'inspection') 
                         THEN inspection.inspection_no 
                    WHEN meeting_information.related_to = (select tabid from tab where tab.name = 'sourcingdeal') 
                         THEN sourcingdeal.sourcingdeal_no 
                    WHEN meeting_information.related_to = (select tabid from tab where tab.name = 'payments') 
                         THEN payments.payment_no 
                    WHEN meeting_information.related_to = (select tabid from tab where tab.name = 'salesorderdit') 
                         THEN salesorder_dit.salesorder_dit_no 
                    WHEN meeting_information.related_to = (select tabid from tab where tab.name = 'purchaseorderdit') 
                         THEN purchase_order_dit.purchaseorder_dit_no 
                    WHEN meeting_information.related_to = (select tabid from tab where tab.name = 'grndit') 
                         THEN grn_dit.grndit_no 
                    WHEN meeting_information.related_to = (select tabid from tab where tab.name = 'invoicedit') 
                         THEN invoicedit.invoicedit_no 
                    WHEN meeting_information.related_to = (select tabid from tab where tab.name = 'paymentdit') 
                         THEN paymentdit.paymentdit_no 
                    WHEN meeting_information.related_to = (select tabid from tab where tab.name = 'packinglistdit') 
                         THEN packing_list_dit.packinglist_no 
                    WHEN meeting_information.related_to = (select tabid from tab where tab.name = 'focdit') 
                         THEN foc_dit.focdit_no 
                    WHEN meeting_information.related_to = (select tabid from tab where tab.name = 'quotesdit') 
                         THEN quotes_dit.quotes_dit_no 
                    WHEN meeting_information.related_to = (select tabid from tab where tab.name = 'deliverychallandit') 
                         THEN delivery_challandit.deliverychallan_no 
                    ELSE 
                         NULL 
                    END AS 'Related Module No',
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
                    usercreatorid.username as 'Created By',
                    usermodifiedby.username as 'Modified By',
                    meeting_information.createdtime as 'Created Time',
                    meeting_information.modifiedtime as 'Modified Time'
                    from 
                    meeting_information 
                    left join user as userownerid on (meeting_information.ownerid=userownerid.id) 
                    LEFT OUTER JOIN vendor_locations as vendor_locationslocation on (meeting_information.location=vendor_locationslocation.vendorloc_id) 
                    left join user as userhost on (meeting_information.host=userhost.id) 
                    left join user as usersolution_architect on (meeting_information.solution_architect=usersolution_architect.id) 
                    LEFT JOIN contacts AS contacts_alias ON FIND_IN_SET(contacts_alias.contacts_id, meeting_information.external_participants) 
                    LEFT JOIN user AS user_alias ON FIND_IN_SET(user_alias.id, meeting_information.internal_participants) 
                    LEFT OUTER JOIN tab on (meeting_information.related_to= tab.tabid) 
                    LEFT OUTER JOIN leadinformation on (meeting_information.related_to_id=leadinformation.leadid) 
                    LEFT OUTER JOIN opportunity on (meeting_information.related_to_id=opportunity.opportunity_id) 
                    LEFT OUTER JOIN vendor_account on (meeting_information.related_to_id=vendor_account.vendoraccid) 
                    LEFT OUTER JOIN quotes on (meeting_information.related_to_id=quotes.quotes_id) 
                    LEFT OUTER JOIN contacts on (meeting_information.related_to_id=contacts.contacts_id) 
                    LEFT OUTER JOIN pickup on (meeting_information.related_to_id=pickup.pickup_id) 
                    LEFT OUTER JOIN iqc_desktop on (meeting_information.related_to_id=iqc_desktop.iqcdesktop_id) 
                    LEFT OUTER JOIN iqc_laptop on (meeting_information.related_to_id=iqc_laptop.iqclaptop_id) 
                    LEFT OUTER JOIN iqc_tft on (meeting_information.related_to_id=iqc_tft.iqctft_id) 
                    LEFT OUTER JOIN grn on (meeting_information.related_to_id=grn.grn_id) 
                    LEFT OUTER JOIN purchase_order on (meeting_information.related_to_id=purchase_order.purchase_order_id) 
                    LEFT OUTER JOIN vendor_locations on (meeting_information.related_to_id=vendor_locations.vendorloc_id) 
                    LEFT OUTER JOIN drilling on (meeting_information.related_to_id=drilling.drilling_id) 
                    LEFT OUTER JOIN degaussing on (meeting_information.related_to_id=degaussing.degaussinginfo_id) 
                    LEFT OUTER JOIN shredding on (meeting_information.related_to_id=shredding.shredding_id) 
                    LEFT OUTER JOIN data_wiping on (meeting_information.related_to_id=data_wiping.datawiping_id) 
                    LEFT OUTER JOIN inspection on (meeting_information.related_to_id=inspection.inspection_id) 
                    LEFT OUTER JOIN sourcingdeal on (meeting_information.related_to_id=sourcingdeal.sourcingdeal_id) 
                    LEFT OUTER JOIN payments on (meeting_information.related_to_id=payments.payments_id) 
                    LEFT OUTER JOIN salesorder_dit on (meeting_information.related_to_id=salesorder_dit.salesorder_dit_id) 
                    LEFT OUTER JOIN purchase_order_dit on (meeting_information.related_to_id=purchase_order_dit.purchaseorder_dit_id) 
                    LEFT OUTER JOIN grn_dit on (meeting_information.related_to_id=grn_dit.grndit_id) 
                    LEFT OUTER JOIN invoicedit on (meeting_information.related_to_id=invoicedit.invoicedit_id) 
                    LEFT OUTER JOIN paymentdit on (meeting_information.related_to_id=paymentdit.paymentdit_id) 
                    LEFT OUTER JOIN packing_list_dit on (meeting_information.related_to_id=packing_list_dit.packinglist_id) 
                    LEFT OUTER JOIN foc_dit on (meeting_information.related_to_id=foc_dit.focdit_id) 
                    LEFT OUTER JOIN quotes_dit on (meeting_information.related_to_id=quotes_dit.quotes_dit_id) 
                    LEFT OUTER JOIN delivery_challandit on (meeting_information.related_to_id=delivery_challandit.deliverychallan_id) 
                    left join task_repeattype as task_repeattyperepeat_type on (meeting_information.repeat_type=task_repeattyperepeat_type.repeattype_id) 
                    left join mparticipants_reminder as mparticipants_reminderparticipants_reminder on (meeting_information.participants_reminder=mparticipants_reminderparticipants_reminder.mparticipants_reminderid) 
                    left join mreminder as mreminderremainder on (meeting_information.remainder=mreminderremainder.mreminderid) 
                    LEFT OUTER JOIN vendor_account as vendor_accountaccount_name on (meeting_information.account_name=vendor_accountaccount_name.vendoraccid) 
                    left join mconveyance_required as mconveyance_requiredconveyance_required on (meeting_information.conveyance_required=mconveyance_requiredconveyance_required.mconveyance_requiredid) 
                    left join meeting_expence_category as meeting_expence_categoryexpence_category on (meeting_information.expence_category=meeting_expence_categoryexpence_category.expence_category_id) 
                    left join meeting_tax_type as meeting_tax_typetax_type on (meeting_information.tax_type=meeting_tax_typetax_type.tax_type_id) 
                    left join user as usercreatorid on (meeting_information.creatorid=usercreatorid.id) 
                    left join user as usermodifiedby on (meeting_information.modifiedby=usermodifiedby.id) 
                    inner join user as owner on (owner.id=meeting_information.ownerid) 
                    where 
                    meeting_information.deleted=0 and 1=1 AND DATE(meeting_information.createdtime) < :today
                    GROUP BY meeting_information.meetinginfo_id order by 
                    meeting_information.meetinginfo_id DESC";*/

          // --- 1. Get metadata (tab + autonumber field column) ---
          /*$meet_meta_sql = "SELECT t.tabid, t.tablename, t.tablekeyid, f.columnname, t.tablabel
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
          $meet_sql = "SELECT meeting_information.*, tab.tablabel,
                    concat(userownerid.first_name,' ',userownerid.last_name) as 'Meeting Owner',
                    meeting_information.title as 'Title'
                    vendor_locationslocation.vendor_loc_name as 'Location',
                    if(meeting_information . all_day is not null,if(meeting_information . all_day=0,'No','Yes'),'') as 'All Day',
                    DATE_FORMAT(meeting_information.from,'%d-%m-%Y %H:%i:%s') as 'From',
                    DATE_FORMAT(meeting_information.to,'%d-%m-%Y %H:%i:%s') as 'To',
                    concat(userhost.first_name,' ',userhost.last_name) as 'Host',
                    concat(usersolution_architect.first_name,' ',usersolution_architect.last_name) as 'Solution Architect',
                    GROUP_CONCAT(concat(contacts_alias.first_name,' ',contacts_alias.last_name) ORDER BY contacts_alias.contacts_id) AS 'External Participants',
                    GROUP_CONCAT(concat(user_alias.first_name,' ',if(user_alias.last_name is null,'',user_alias.last_name)) ORDER BY user_alias.id) AS 'Internal Participants',
                    tab.tablabel as 'Related To',
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
                    if(meeting_information . submit_approval=0,'No','Yes'),'') as 'Submit Approval'
               FROM meeting_information 
                left join user as userownerid on (meeting_information.ownerid=userownerid.id) 
                    LEFT OUTER JOIN vendor_locations as vendor_locationslocation on (meeting_information.location=vendor_locationslocation.vendorloc_id) 
                    left join user as userhost on (meeting_information.host=userhost.id) 
                    left join user as usersolution_architect on (meeting_information.solution_architect=usersolution_architect.id) 
                    LEFT JOIN contacts AS contacts_alias ON FIND_IN_SET(contacts_alias.contacts_id, meeting_information.external_participants) 
                    LEFT JOIN user AS user_alias ON FIND_IN_SET(user_alias.id, meeting_information.internal_participants) 
                    LEFT OUTER JOIN tab on (meeting_information.related_to= tab.tabid) 
                    left join task_repeattype as task_repeattyperepeat_type on (meeting_information.repeat_type=task_repeattyperepeat_type.repeattype_id) 
                    left join mparticipants_reminder as mparticipants_reminderparticipants_reminder on (meeting_information.participants_reminder=mparticipants_reminderparticipants_reminder.mparticipants_reminderid) 
                    left join mreminder as mreminderremainder on (meeting_information.remainder=mreminderremainder.mreminderid) 
                    LEFT OUTER JOIN vendor_account as vendor_accountaccount_name on (meeting_information.account_name=vendor_accountaccount_name.vendoraccid) 
                    left join mconveyance_required as mconveyance_requiredconveyance_required on (meeting_information.conveyance_required=mconveyance_requiredconveyance_required.mconveyance_requiredid) 
                    left join meeting_expence_category as meeting_expence_categoryexpence_category on (meeting_information.expence_category=meeting_expence_categoryexpence_category.expence_category_id) 
                    left join meeting_tax_type as meeting_tax_typetax_type on (meeting_information.tax_type=meeting_tax_typetax_type.tax_type_id) 
                    left join user as usercreatorid on (meeting_information.creatorid=usercreatorid.id) 
                    left join user as usermodifiedby on (meeting_information.modifiedby=usermodifiedby.id) 
                    inner join user as owner on (owner.id=meeting_information.ownerid) 
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
          $meet_filePath = $directory . "/meet_details_$today.csv";
          $meet_fp = fopen($meet_filePath, 'w');
          if (!$meet_fp) {
               throw new Exception("Unable to create or write to the CSV file.");
          }


     //      $meet_headers = ["Title","Location","All Day","From","To","Host","Solution Architect","External Participants","
     //      Internal Participants","Related Module","Related Record","Repeat","Repeat Type","Participants","Reminder","Internal Comments	External", Comments	Remainder	Account Name	From Location	To Location	Confirms	Distance	MOM Shared	Conveyance Required	Description	Expence Category	Expence Type	Tax Type	Expence Date	Submit Approval	Created By	Modified By	Created Time	Modified Time
     // ];
          fputcsv($meet_fp, $meet_headers);

          // Data rows
         foreach ($meets as $row) {
          fputcsv($meet_fp, [
               $row['Call Owner'],
               $row['Related Module'],
               $row['Related Record'],
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
          ]);
          }

          fclose($meet_fp);
          echo "\n CSV file saved to: $meet_filePath";*/
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

          //         $mail->isSMTP();                                       // Set mailer to use SMTP
// $mail->Host       = 'smtp.office365.com';               // Office 365 SMTP server
// $mail->SMTPAuth   = true;                               // Enable SMTP authentication
// $mail->Username   = 'erps@wmpl.com';                    // SMTP username (full email)
// $mail->Password   = 'your-email-password';              // SMTP password (use App password if MFA is enabled)
// $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;     // Enable TLS encryption
// $mail->Port       = 587;                                // TCP port to connect to (587 for TLS)

          $mail->AddCC('deepika.tetra@gmail.com');
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
          $fileUrl = $baseUrl . "sourcingdeal_productdetail_$today.csv";



          // Function to base64 encode the URL
          function encode_url($url)
          {
               return base64_encode($url);
          }

          // File URL (this should be the actual file path)
          $directory = __DIR__ . '/exports';
          $filePathprod = $directory . "/sourcingdeal_productdetail_$today.csv";  // Dynamic file path
          $filePathsrc = $directory . "/sourcingdeal_$today.csv";  // Dynamic file path

          //meeting and call file dynamic
          // $callfilePath= $directory . "/call_details_$today.csv";  // Dynamic file path
          // $meetingfilePath = $directory . "/meeting_details_$today.csv";  // Dynamic file path

          // Base64 encode the file URL
          $encodedFileUrlprod = encode_url($filePathprod);
          // if ($host === 'erp.ditserv.com' || $host === 'stagerp.ditserv.com')
               $concaturl = "";
          // else
               // $concaturl = "/deshwal";
          // for dev
          $downloadlinkprod = $protocol . '://' . $host . $concaturl . '/downloadsrc?url=' . urlencode($encodedFileUrlprod);
          // $downloadlinkprod = $protocol . '://' . $_SERVER['HTTP_HOST'] . '/downloadsrc?url=' . urlencode($encodedFileUrlprod);

          $encodedFileUrlsrc = encode_url($filePathsrc);
          // for dev
          $downloadlink = $protocol . '://' . $host . $concaturl . '/downloadsrc?url=' . urlencode($encodedFileUrlsrc);
          // $downloadlink = $protocol . '://' . $_SERVER['HTTP_HOST'] . '/downloadsrc?url=' . urlencode($encodedFileUrlsrc);
          //call ane meeting code
          // $encodedCallFileUrlsrc = encode_url($callfilePath);
          // $calldownloadlink = $protocol . '://' . $_SERVER['HTTP_HOST']  . $concaturl. '/downloadsrc?url=' . urlencode($encodedCallFileUrlsrc);

          // $encodedMeetingFileUrlsrc = encode_url($meetingfilePath);
          // $meetingdownloadlink = $protocol . '://' . $_SERVER['HTTP_HOST']  . $concaturl. '/downloadsrc?url=' . urlencode($encodedMeetingFileUrlsrc);

          // Send the email
          // $mail->MsgHTML("Hello Team,<br><br>The CSV report for Sourcing deals, Calls and Meetings has been generated successfully. You can download the files using the following link: <br><a href='$downloadlink'>Download Sourcing Deal</a><br><a href='$downloadlinkprod'>Download Product Detail </a><br><a href='$calldownloadlink'>Download Call Detail </a><br><a href='$meetingdownloadlink'>Download Meeting Detail </a>");

          $mail->MsgHTML("Hello Team,<br><br>The CSV report for Sourcing deals has been generated successfully. You can download the files using the following link: <br><a href='$downloadlink'>Download Sourcing Deal</a><br><a href='$downloadlinkprod'>Download Product Detail </a>");


          $mail->SetFrom('erp@Dwmpl.com');
          $mail->isHTML(true);
          $today_dt = date("d/m/Y", strtotime($today));
          // $mail->Subject = "Sourcing deal, Call and Meeting Report - $today_dt";
          $mail->Subject = "Sourcing deal Report - $today_dt";
          // $mail->MsgHTML("Dear user,<br><br>The CSV report for sourcing deals has been generated successfully. You can download the file using the following link:<a href='" . $encodedFileUrl  . "'>Download CSV</a><br>Best regards,<br>Your Team");
          // echo "<br>Final Mail Object=<pre>";
          // print_r($mail);
          // if (!$mail->Send()) {
          //      echo "Mailer Error: " . $mail->ErrorInfo;

          //      return 0;
          // } else
          //      echo "<br>Mail sent successfully";
          // $mailStatus = 1;
          // upMailStatus($today, $mailStatus);

     }//end mail


} catch (Exception $e) {
     echo $e->getMessage();
} catch (Error $e) {
     echo $e->getMessage();
}
