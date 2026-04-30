<?php
/**
 * common code for all
 * 
 * */
$rootDir = dirname(__DIR__);

require_once("comman.inc.php");
require_once("params.php");
$connection = db_connect();
// Yesterday's date
$yesterday = date("Y-m-d", strtotime("-2 day"));


try {

    // Connect
     $mycon = db_connect();

    // Only pick records modified yesterday
    $sql = "
       INSERT INTO rep_sourcingdeal (
            sourcingdeal_id,
            sourcingdeal_no,
            ownerid,
            deal_name,
            vendor_account_name,
            acc_id,
            business_type,
            contact_name,
            contact_email,
            contact_mobile,
            stage,
            remarks,
            payment_type,
            forecast_category,
            category,
            currency,
            is_contract,
            type_of_contract,
            lead_source,
            oem,
            oem_manager,
            oem_manager_name,
            opportunity_score,
            pricing_type,
            inspection_required,
            special_pricing,
            costing_done,
            ceo_approval,
            total_sourcing_deal_amount,
            total_sourcing_deal_cost,
            total_sourcing_deal_sale,
            service_sale,
            service_cost,
            product_cost,
            product_sale,
            margin,
            margin_percentage,
            createdtime
        )
        SELECT
            s.sourcingdeal_id,
            s.sourcingdeal_no,
            s.ownerid,
            s.deal_name,

            va.acc_name,                        -- vendor_account_name VALUE
            va.cust_code,                       -- acc_id VALUE
            bt.business_type_value,             -- business_type VALUE

            c.first_name,                       -- contact_name VALUE

            s.contact_email,
            s.contact_mobile,
            st.stage_value,                     -- stage VALUE
            s.remarks,
            ptp.sourcing_payment_type_value,    -- payment_type VALUE
            fc.forecast_category_value,         -- forecast_category VALUE
            lc.lead_category_value,             -- category VALUE
            cur.currency_value,                 -- currency VALUE
            ic.iscontract_value,                -- is_contract VALUE
            toc.type_of_contract_value,         -- type_of_contract VALUE
            ls.leadsource_value,                -- lead_source VALUE
            ro.rolename,                        -- oem VALUE
            rm.rolename,                        -- oem_manager VALUE
            CONCAT(uom.first_name,' ',uom.last_name), -- oem_manager_name VALUE
            os.opportunity_score_value,         -- opportunity_score VALUE
            ptyp.pricing_type_value,            -- pricing_type VALUE
            insr.inspection_required_value,     -- inspection_required VALUE

            s.special_pricing,
            s.costing_done,
            s.ceo_approval,

            s.total_sourcing_deal_amount,
            s.total_sourcing_deal_cost,
            s.total_sourcing_deal_sale,
            s.service_sale,
            s.service_cost,
            s.product_cost,
            s.product_sale,
            s.margin,
            s.margin_percentage,
            s.createdtime

        FROM sourcingdeal s
        LEFT JOIN user u ON s.ownerid = u.id
        LEFT JOIN vendor_account va ON s.vendor_account_name = va.vendoraccid
        LEFT JOIN oppr_business_type bt ON s.business_type = bt.business_type_id
        LEFT JOIN contacts c ON s.contact_name = c.contacts_id
        LEFT JOIN sourcingdeal_stage st ON s.stage = st.stage_id
        LEFT JOIN sourcing_payment_type ptp ON s.payment_type = ptp.sourcing_payment_typeid
        LEFT JOIN forecast_category fc ON s.forecast_category = fc.forecast_categoryid
        LEFT JOIN lead_category lc ON s.category = lc.lead_category_id
        LEFT JOIN currency cur ON s.currency = cur.currencyid
        LEFT JOIN sd_iscontract ic ON s.is_contract = ic.iscontract_id
        LEFT JOIN type_of_contract toc ON s.type_of_contract = toc.type_of_contractid
        LEFT JOIN lead_source ls ON s.lead_source = ls.leadsourceid
        LEFT JOIN role ro ON s.oem = ro.roleid
        LEFT JOIN role rm ON s.oem_manager = rm.roleid
        LEFT JOIN user uom ON s.oem_manager_name = uom.id
        LEFT JOIN opportunity_score os ON s.opportunity_score = os.opportunity_scoreid
        LEFT JOIN pricing_type ptyp ON s.pricing_type = ptyp.pricing_type_id
        LEFT JOIN oppr_inspection_required insr ON s.inspection_required = insr.inspection_required_id
        WHERE s.deleted = 0
        AND s.is_temp = 0
        AND DATE(s.modifiedtime) = :yesterday

        ON DUPLICATE KEY UPDATE
            sourcingdeal_no = VALUES(sourcingdeal_no),
            deal_name = VALUES(deal_name),
            ownerid = VALUES(ownerid),
            vendor_account_name = VALUES(vendor_account_name),
            acc_id = VALUES(acc_id),
            business_type = VALUES(business_type),
            contact_name = VALUES(contact_name),
            contact_email = VALUES(contact_email),
            contact_mobile = VALUES(contact_mobile),
            stage = VALUES(stage),
            remarks = VALUES(remarks),
            payment_type = VALUES(payment_type),
            forecast_category = VALUES(forecast_category),
            category = VALUES(category),
            currency = VALUES(currency),
            is_contract = VALUES(is_contract),
            type_of_contract = VALUES(type_of_contract),
            lead_source = VALUES(lead_source),
            oem = VALUES(oem),
            oem_manager = VALUES(oem_manager),
            oem_manager_name = VALUES(oem_manager_name),
            opportunity_score = VALUES(opportunity_score),
            pricing_type = VALUES(pricing_type),
            inspection_required = VALUES(inspection_required),
            special_pricing = VALUES(special_pricing),
            costing_done = VALUES(costing_done),
            ceo_approval = VALUES(ceo_approval),
            total_sourcing_deal_amount = VALUES(total_sourcing_deal_amount),
            total_sourcing_deal_cost = VALUES(total_sourcing_deal_cost),
            total_sourcing_deal_sale = VALUES(total_sourcing_deal_sale),
            service_sale = VALUES(service_sale),
            service_cost = VALUES(service_cost),
            product_cost = VALUES(product_cost),
            product_sale = VALUES(product_sale),
            margin = VALUES(margin),
            margin_percentage = VALUES(margin_percentage),
            createdtime = VALUES(createdtime);

    ";

    $stmt = $mycon->prepare($sql);
    $stmt->execute([':yesterday' => $yesterday]);

    echo "Cron executed successfully. Records inserted/updated for date: $yesterday\n Module : Sourcingdeal ";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

