<?php

// /quotes file creation

         /* $quotesql = "SELECT quotes.*, tab.tablabel,
                    concat(userownerid.first_name,' ',userownerid.last_name) as 'Quote Owner',
                    quotes.quotes_no as 'Quote No.',
                    tab.tablabel as 'Related Module',
                    quotes.quote_creation_date as 'Quote Creation Date',
                    quotes.quote_stage as 'Quote Stage',
                    quotes.payment_terms as 'Payment Terms',
                    quotes.exchange_rate as 'Exchange Rate',
                    quotes.gross_profit as 'Gross Profit',
                    quotes.margin_percent as 'Margin Percent',
                    quotes.deal_name as 'Deal Name',
                    CONCAT(u2.first_name,' ',u2.last_name) as 'Created By',
                    CONCAT(u3.first_name,' ',u3.last_name) as 'Modified By',
                    call_information.createdtime as 'Created Time',
                    call_information.modifiedtime as 'Modified Time',                    
                    vendor_accountaccount_name.acc_name as 'Account Name'
               FROM quotes 
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
          */
                              
            $quotesql ="select 
                concat(userownerid.first_name,' ',userownerid.last_name) as `Owner`,
                quotes.quotes_no,DATE_FORMAT(`quotes`.`quote_creation_date`,'%d-%m-%Y') as `Quote Creation Date`,quote_stagequote_stage.`quote_stage_value` as `Quote Stage`,
                qu_payment_termspayment_terms.`payment_terms_value` as `Payment Terms`,
                currencycurrency.`currency_value` as `Currency`,
                quotes.exchange_rate as 'Exchange Rate',
                quotes.gross_profit as 'Gross Profit',
                quotes.margin_percent as 'Margin Percent',
                tab.tablabel as 'Related To',
                vendor_accountaccount_name.acc_name as 'Account Name',
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
                GROUP BY `quotes`.quotes_id order by `quotes`.quotes_id DESC";
                
        /*  $quotestmt = $connection->prepare($quotesql);
          $quotestmt->execute(['today' => $today]);
          $calls = $quotestmt->fetchAll(PDO::FETCH_ASSOC);
          
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
          $quotefilePath = $directory . "/quotes_detail_$today.csv";
          $quotefp = fopen($quotefilePath, 'w');
          if (!$quotefp) {
               throw new Exception("Unable to create or write to the CSV file.");
          }

          
          $quoteheaders = ["Quote Owner","Quote No.","Related Module","Related Record","Account Name","Subject","Comment","Outgoing Quote Status","Quote Start Time",
          "Quote End Time","Quote Duration","Quote Type","Quote Purpose","Quote Agenda","Quote Result","Created By","Last Modified By","Created Time","Modified Time"];
          fputcsv($quotefp, $quoteheaders);

          // Data rows
         foreach ($calls as $row) {
          fputcsv($quotefp, [
               $row['Quote Owner'],
               $row['Quote No.'],
               $row['Related Module'],
               $row['Related Record'],
               $row['Account Name'],
               $row['Subject'],
               $row['Comment'],
               $row['Outgoing Quote Status'],
               $row['Quote Start Time'],
               $row['Quote End Time'],
               $row['Quote Duration'],
               $row['Quote Type'],
               $row['Quote Purpose'],
               $row['Quote Agenda'],
               $row['Quote Result'],
               $row['Created By'],
               $row['Modified By'],
               $row['Created Time'],
               $row['Modified Time'],
          ]);
          }

          fclose($quotefp);
          echo "\nCSV file saved to: $quotefilePath";
            */

        // =======================================================================
        // payments
        //   =====================================================================
        $paymentql = "select 
        usercreatorid.username as 'Created By',
        usermodifiedby.username as 'Modified By',
        payments.createdtime as 'Created Time',
        payments.modifiedtime  as 'Modified Time',
        concat(userownerid.first_name,' ',userownerid.last_name) as `Owner`,
        payment_typepayment_type.`payment_type_value` as `Payment Type`,
        sourcingdealsourcing_deal.sourcingdeal_no as 'Sourcing Deal',
        payments.sourcing_deal_stage as 'Sourcing Deal Stage',
        payments.account_name as 'Account Name',
        purchase_orderpo.purchase_order_no as 'Purchase Order No',
        payments.first_comment as 'First Comment',
        payments.second_comment as 'Second Comment',
        payments.payment_no as 'Payment No',
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
          LEFT OUTER JOIN purchase_order as purchase_orderpo on (`payments`.po=purchase_orderpo.purchase_order_id) left join payment_stage as payment_stagestage on (`payments`.`stage`=payment_stagestage.payment_stage_id) inner join user as owner on (owner.id=`payments`.ownerid) 
          where `payments`.deleted=0 and 1=1 order by `payments`.payments_id DESC";


        //   ===========================================================================
        //   Inspection
        //   ===========================================================================
        $inspectionsql = "select 
        usercreatorid.username as 'Created By',
        inspection.createdtime as 'Created Time',
        usermodifiedby.username as 'Modified By',
        inspection.modifiedtime as 'Modified Time',
        concat(userownerid.first_name,' ',userownerid.last_name) as `Owner`,
        sourcingdealsourcing_deal.sourcingdeal_no as 'Sourcing Deal',
        inspection.inspection_no as 'Inspection No',vendor_accountaccount_name.acc_name as account_name,contactsspoc_name.first_name as spoc_name,inspection.spoc_number,inspection.spoc_email,DATE_FORMAT(`inspection`.`inspection_preferred_date`,'%d-%m-%Y') as `inspection_preferred_date`,inspection.inspection_preferred_time,inspection_stagesstages.`stages_value` as `stages`,if(`inspection` . `submit_for_logistics` is not null,if(`inspection` . `submit_for_logistics`=0,'No','Yes'),'') as `submit_for_logistics`, if(`inspection` . `schedule_inspection` is not null,if(`inspection` . `schedule_inspection`=0,'No','Yes'),'') as `schedule_inspection`, if(`inspection` . `inspection_started` is not null,if(`inspection` . `inspection_started`=0,'No','Yes'),'') as `inspection_started`, if(`inspection` . `inspection_completed` is not null,if(`inspection` . `inspection_completed`=0,'No','Yes'),'') as `inspection_completed`, inspection.pav_hold_by_client_reason,inspection.pav_hold_by_dwmpl_reason,inspection.pav_cancelled_reason,DATE_FORMAT(`inspection`.`resume_date`,'%d-%m-%Y') as `resume_date`,vendor_locationsinspection_location.vendor_loc_name as inspection_location,inspection.location_address,inspection.location_state,inspection.location_city,inspection.location_pincode,inspection_doneinspection_done_by.`inspection_done_value` as `inspection_done_by`,vendor_accountvendor_name.acc_name as vendor_name,inspection_typeinsection_type.`inspectiontype_value` as `insection_type`,DATE_FORMAT(`inspection`.`inspection_start_date`,'%d-%m-%Y') as `inspection_start_date`,DATE_FORMAT(`inspection`.`inpection_completed_date`,'%d-%m-%Y') as `inpection_completed_date`,inspection.vendor_spoc_number,DATE_FORMAT(`inspection`.`inspection_schedule_date`,'%d-%m-%Y') as `inspection_schedule_date`,material_typematerial_type.`material_type_value` as `material_type`,contactsvendor_spoc_name_done_by_vendor.first_name as vendor_spoc_name_done_by_vendor,userlogistics_fe_name_done_by_dwmpl.first_name as logistics_fe_name_done_by_dwmpl,userlogistics_spoc.first_name as logistics_spoc,inspection.logistics_fe_number,ins_entry_personnelentry_personnel.`value` as `entry_personnel`,ins_working_timingworking_timings.`value` as `working_timings`,ins_inspect_itemslot_get_inspect_item.`value` as `slot_get_inspect_item`,ins_multi_locationsingle_location_multi_location.`value` as `single_location_multi_location`,inspection.how_many_locations_floor,GROUP_CONCAT(ins_protocoal_parameter_alias.value ORDER BY ins_protocoal_parameter_alias.id) AS security_protocoal_parameter,ins_allowed_faciltiyallowed_at_the_faciltiy.`value` as `allowed_at_the_faciltiy`,ins_allowed_faciltiyitems_which_need_inspect.`value` as `items_which_need_inspect`,ins_laptop_entry_premiseslaptop_entry_at_the_premises.`value` as `laptop_entry_at_the_premises`,ins_allowed_faciltiyphysical_verification_of_asset.`value` as `physical_verification_of_asset`,inspection.perform_at_which_floor_area,ins_allowed_faciltiydesignated_inspection_area.`value` as `designated_inspection_area`,ins_allowed_faciltiysufficient_power_supply.`value` as `sufficient_power_supply`,ins_allowed_faciltiysupply_to_laptop_desktop.`value` as `supply_to_laptop_desktop`,ins_allowed_faciltiypower_on_the_machines.`value` as `power_on_the_machines`,ins_allowed_faciltiytools_allowed_inside_premises.`value` as `tools_allowed_inside_premises`,ins_allowed_faciltiyvehicle_allowed_parking.`value` as `vehicle_allowed_parking`,ins_formailites_vehicle_entryformailites_vehicle_entry.`value` as `formailites_vehicle_entry`, IF ((`inspection`.ownerid = 1 OR 1 = 1) , '1' , '0') as isEdit from `inspection` left join `user` as usercreatorid on (`inspection`.creatorid=usercreatorid.id) left join `user` as usermodifiedby on (`inspection`.modifiedby=usermodifiedby.id) left join `user` as userownerid on (`inspection`.ownerid=userownerid.id) LEFT OUTER JOIN sourcingdeal as sourcingdealsourcing_deal on (`inspection`.sourcing_deal=sourcingdealsourcing_deal.sourcingdeal_id) LEFT OUTER JOIN vendor_account as vendor_accountaccount_name on (`inspection`.account_name=vendor_accountaccount_name.vendoraccid) LEFT OUTER JOIN contacts as contactsspoc_name on (`inspection`.spoc_name=contactsspoc_name.contacts_id) left join inspection_stages as inspection_stagesstages on (`inspection`.`stages`=inspection_stagesstages.stages_id) LEFT OUTER JOIN vendor_locations as vendor_locationsinspection_location on (`inspection`.inspection_location=vendor_locationsinspection_location.vendorloc_id) left join inspection_done as inspection_doneinspection_done_by on (`inspection`.`inspection_done_by`=inspection_doneinspection_done_by.inspection_doneid) LEFT OUTER JOIN vendor_account as vendor_accountvendor_name on (`inspection`.vendor_name=vendor_accountvendor_name.vendoraccid) left join inspection_type as inspection_typeinsection_type on (`inspection`.`insection_type`=inspection_typeinsection_type.inspectiontypeid) left join material_type as material_typematerial_type on (`inspection`.`material_type`=material_typematerial_type.material_type_id) LEFT OUTER JOIN contacts as contactsvendor_spoc_name_done_by_vendor on (`inspection`.vendor_spoc_name_done_by_vendor=contactsvendor_spoc_name_done_by_vendor.contacts_id) LEFT OUTER JOIN user as userlogistics_fe_name_done_by_dwmpl on (`inspection`.logistics_fe_name_done_by_dwmpl=userlogistics_fe_name_done_by_dwmpl.id) LEFT OUTER JOIN user as userlogistics_spoc on (`inspection`.logistics_spoc=userlogistics_spoc.id) left join ins_entry_personnel as ins_entry_personnelentry_personnel on (`inspection`.`entry_personnel`=ins_entry_personnelentry_personnel.id) left join ins_working_timing as ins_working_timingworking_timings on (`inspection`.`working_timings`=ins_working_timingworking_timings.id) left join ins_inspect_item as ins_inspect_itemslot_get_inspect_item on (`inspection`.`slot_get_inspect_item`=ins_inspect_itemslot_get_inspect_item.id) left join ins_multi_location as ins_multi_locationsingle_location_multi_location on (`inspection`.`single_location_multi_location`=ins_multi_locationsingle_location_multi_location.id) LEFT JOIN ins_protocoal_parameter AS ins_protocoal_parameter_alias ON FIND_IN_SET(ins_protocoal_parameter_alias.id, `inspection`.security_protocoal_parameter) left join ins_allowed_faciltiy as ins_allowed_faciltiyallowed_at_the_faciltiy on (`inspection`.`allowed_at_the_faciltiy`=ins_allowed_faciltiyallowed_at_the_faciltiy.id) left join ins_allowed_faciltiy as ins_allowed_faciltiyitems_which_need_inspect on (`inspection`.`items_which_need_inspect`=ins_allowed_faciltiyitems_which_need_inspect.id) left join ins_laptop_entry_premises as ins_laptop_entry_premiseslaptop_entry_at_the_premises on (`inspection`.`laptop_entry_at_the_premises`=ins_laptop_entry_premiseslaptop_entry_at_the_premises.id) left join ins_allowed_faciltiy as ins_allowed_faciltiyphysical_verification_of_asset on (`inspection`.`physical_verification_of_asset`=ins_allowed_faciltiyphysical_verification_of_asset.id) left join ins_allowed_faciltiy as ins_allowed_faciltiydesignated_inspection_area on (`inspection`.`designated_inspection_area`=ins_allowed_faciltiydesignated_inspection_area.id) left join ins_allowed_faciltiy as ins_allowed_faciltiysufficient_power_supply on (`inspection`.`sufficient_power_supply`=ins_allowed_faciltiysufficient_power_supply.id) left join ins_allowed_faciltiy as ins_allowed_faciltiysupply_to_laptop_desktop on (`inspection`.`supply_to_laptop_desktop`=ins_allowed_faciltiysupply_to_laptop_desktop.id) left join ins_allowed_faciltiy as ins_allowed_faciltiypower_on_the_machines on (`inspection`.`power_on_the_machines`=ins_allowed_faciltiypower_on_the_machines.id) left join ins_allowed_faciltiy as ins_allowed_faciltiytools_allowed_inside_premises on (`inspection`.`tools_allowed_inside_premises`=ins_allowed_faciltiytools_allowed_inside_premises.id) left join ins_allowed_faciltiy as ins_allowed_faciltiyvehicle_allowed_parking on (`inspection`.`vehicle_allowed_parking`=ins_allowed_faciltiyvehicle_allowed_parking.id) left join ins_formailites_vehicle_entry as ins_formailites_vehicle_entryformailites_vehicle_entry on (`inspection`.`formailites_vehicle_entry`=ins_formailites_vehicle_entryformailites_vehicle_entry.id) inner join user as owner on (owner.id=`inspection`.ownerid) where `inspection`.deleted=0 and 1=1 GROUP BY `inspection`.inspection_id order by `inspection`.inspection_id DESC";
          ?>