//Opportunity
SELECT userownerid.username AS `Opportunity Owner`, 
usercreatorid.username AS `Created BY`,
opportunity_no AS `Opportunity No`,
 usermodifiedby.username  AS `Last Modified By`,
  deal_name AS `Deal Name`, 
  DATE_FORMAT(`opportunity`.`createdtime`,'%d-%m-%Y %H:%i:%s') AS `Created Time`,
   DATE_FORMAT(`opportunity`.`modifiedtime`,'%d-%m-%Y %H:%i:%s') AS `Modified Time`,
   vendor_accountaccount_name.acc_name  AS `Account Name`,
    concat(requester_customer_name.first_name,' ',requester_customer_name.last_name) AS `Requester/Customer Name`,
     requester_email_customer_email AS `Requester Email/Customer Email`,
      requester_mobile AS `Requester Mobile`, 
      concat(decision_maker_name.first_name,' ',decision_maker_name.last_name) AS `Decision Maker Name`, 
      decision_maker_email AS `Decision Maker Email`, 
      decision_maker_mobile AS `Decision Maker Mobile`, 
      zn.zone_value AS `Zone/Region`,
       tm.team_name_value AS `Team Name`, 
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
              account_manager.username AS `DevIT Account Manager`,
               account_director_rsm.username AS `Account Director/ RSM`,
                opportunity_stage.stage_value AS `Opportunity Stage`, 
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
                   sa_assigned.username AS `Solution Architect Assigned`,
                    sf_assigned.username AS `Solution Factory Assigned`,
                     procurement_team_member.username AS `Procurement Team Member`, 
                     lead_source.leadsource_value AS `Lead Source`,
                      customer_po_num AS `Customer PO Number`, 
                      customer_payment_terms.payment_terms_value AS `Customer Payment Terms`, 
                      DATE_FORMAT(`opportunity`.`customer_po_date`,'%d-%m-%Y')  AS `Customer PO Date`
                       FROM opportunity
inner join user as userownerid on (`opportunity`.ownerid=`opportunity`.ownerid) 
left join `user` as usercreatorid on (`opportunity`.creatorid=usercreatorid.id) 
left join `user` as usermodifiedby on (`opportunity`.modifiedby=usermodifiedby.id)
left join `user` as business_manager on (`opportunity`.business_manager=business_manager.id)
left join `user` as account_manager on (`opportunity`.account_manager=account_manager.id)
left join `user` as account_director_rsm on (`opportunity`.account_director_rsm=account_manager.id)
left join `user` as sa_assigned on (`opportunity`.sa_assigned=sa_assigned.id)
left join `user` as sf_assigned on (`opportunity`.sf_assigned=sf_assigned.id)
left join `user` as procurement_team_member on (`opportunity`.procurement_team_member=procurement_team_member.id)
left join `oppr_zone` as zn on (`opportunity`.zone_region=zn.zone_id)
left join `oppr_team_name` as tm on (`opportunity`.team_name=tm.team_name_id)
left join `months` as closure_months on (`opportunity`.closure_month=closure_months.months_id)
left join `months` as commit_month on (`opportunity`.commit_month=closure_months.months_id)
left join `oppr_commit` as oc on (`opportunity`.`commit`=oc.commit_id)
left join `closure_week` as closure_week on (`opportunity`.closure_week=closure_week.closure_weekid) 
left join `closure_week` as commit_week on (`opportunity`.closure_week=commit_week.closure_weekid) 
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
 where `opportunity`.deleted=0 AND  `opportunity`.is_temp =0 group by `opportunity`.opportunity_id order by `opportunity`.opportunity_id DESC

---shipping detail----
 SELECT opportunity_no AS `Opportunity No`,
  deal_name AS `Deal Name`, ship_to_location.vendor_loc_name AS `Ship To Location`, opportunity_ship_detail.ship_to_address AS `Ship to Address`, opportunity_ship_detail.ship_to_state AS `Ship to State`, opportunity_ship_detail.ship_legal_name AS `Ship to Lagal Name`, opportunity_ship_detail.gstin_no_uin AS `Ship to GSTIN No`, opportunity_ship_detail.ship_state_code AS `Ship to State Code` FROM opportunity_ship_detail 
  join opportunity on opportunity.opportunity_id=opportunity_ship_detail.opportunity_id 
left join `vendor_locations` as ship_to_location on (`opportunity_ship_detail`.ship_to_location=ship_to_location.vendorloc_id) 
where  `opportunity`.deleted=0 AND  `opportunity`.is_temp =0;

-- opporttunity product detial --
SELECT opportunity_no AS `Opportunity No`,
  deal_name AS `Deal Name`,opportunity_product_detail.purchase_request_number AS `Purchase Request Number`, product_dit.product_name AS `Product Name`, opportunity_product_detail.product_description AS `Product Description`, proddit_master_category.master_category_value AS `Master Category`, proddit_sub_category.sub_category_value AS `Sub Category`, opportunity_product_detail.hsn_code AS `HSN Code`, opportunity_product_detail.quantity AS `Quantity`, opportunity_product_detail.cost_price AS `Cost Price`, opportunity_product_detail.margin_percentage AS `Margin (%)`, opportunity_product_detail.sales_price AS `Sales Price`, opportunity_product_detail.cgst AS `CGST`, opportunity_product_detail.sgst AS `SGST`, opportunity_product_detail.igst AS `IGST`, opportunity_product_detail.total_amount AS `Total Line Item Amount`, opportunity_product_detail.gross_profit AS `Gross Profit`, opportunity_product_detail.add_price_validity AS `Add Price Validity`, opportunity_product_detail.add_product_delivery_timeline AS `Add Product Delivery Time Line`, opportunity_product_detail.add_product_warranty AS `Add Product Warranty`,opportunity_product_detail.reject AS `Reject`, opportunity_product_detail.remarks AS `Remarks` 
FROM opportunity_product_detail
  left join opportunity on opportunity.opportunity_id=opportunity_product_detail.opportunity_id 
  left join product_dit on opportunity_product_detail.product_name=product_dit.productdit_id  
  left join proddit_master_category on opportunity_product_detail.master_category=proddit_master_category.master_category_id  
  left join proddit_sub_category on opportunity_product_detail.sub_category=proddit_sub_category.sub_category_id  
  where  `opportunity`.deleted=0 AND  `opportunity`.is_temp =0;

  -- quotes dit -
  
  SELECT userownerid.username AS `Quotes DIT Owner`,   quotes_dit.quotes_dit_no AS `Quotation Number`,
  usercreatorid.username AS `Created BY`, warehouse_loc_business_entity.warehouse_name AS `Warehouse Location/ Business entity`,usermodifiedby.modifiedby AS `Last Modified By`, opportunity_name.opportunity_no AS `Opportunity Name`,
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
  where quotes_dit.deleted = 0;




  -- sales order dit --

  SELECT  
  userownerid.username AS `Sales DIT Owner`, 
  deal_name.opportunity_no AS `Deal Name`,
  account_name.acc_name AS `Account Name`, 
  account_name.cust_code AS `Account Code`, 
  salesorder_dit.salesorder_dit_no AS `Sales Order No`,
  so_stage.stage_value AS `SO Stage`, 
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
  DATE_FORMAT(`salesorder_dit`.`customer_po_date`,'%d-%m-%Y %H:%i:%s') AS `Customer PO Date`,
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
  where salesorder_dit.deleted = 0 order by salesorder_dit.salesorder_dit_id desc;
   
   
   

  -- po dit --

  SELECT 
  userownerid.username AS `Purchase Order DIT Owner`,
  purchase_order_dit.purchaseorder_dit_no AS `Purchase Order No`,
  po_Issued_entity_name.warehouse_name AS `PO Issued Entity Name`, 
  reference_number.salesorder_dit_no AS `Reference Number`, 
  po_type.purchaseorder_potype_value AS `PO Type`,
  stage.purchaseorder_value AS `Stage`, 
  purchase_order_dit.delivery_instruction AS `Delivery Instruction`, 
  purchase_order_dit.terms_condition AS `Terms & Condition`, 
  credit_terms.purchaseorder_cerdit_terms_value AS `Credit Terms`, 
  DATE_FORMAT(`purchase_order_dit`.`po_expiry_date`,'%d-%m-%Y')  AS `PO Expiry Date`, 
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
  left join `salesorder_dit` as reference_number on (`purchase_order_dit`.reference_number=reference_number.salesorder_dit_id) 
  left join `vendor_locations` as location on (`purchase_order_dit`.location=location.vendorloc_id) 

  where purchase_order_dit.deleted = 0 order by purchase_order_dit.purchaseorder_dit_id desc;

  -- lead --

  SELECT 
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
  usercreatorid.creatorid AS `Created BY`, 
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
  where leadinformation.deleted = 0 order by leadinformation.leadid desc;

