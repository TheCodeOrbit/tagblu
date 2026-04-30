<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "opportunity_information".
 *
 * @property int $opportunity_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $deal_owner
 * @property string|null $expected_closure_date
 * @property string|null $deal_name
 * @property string|null $commit
 * @property int $related_to
 * @property int $related_to_id
 * @property int $deleted
 * @property string|null $vendor_account_name
 * @property string|null $commit_month
 * @property string|null $commit_price
 * @property string|null $type
 * @property int|null $target_date
 * @property string|null $contact_name
 * @property string|null $contact_email
 * @property string|null $opportunity_close
 * @property int|null $contact_mobile
 * @property int|null $closing_date
 * @property string|null $opportunity_tentative_value
 * @property string|null $closure_month
 * @property string|null $vertical_manager
 * @property int|null $closure_year
 * @property string|null $business_manager
 * @property string|null $commit_date
 * @property string|null $vertical_manager_deshwal
 * @property string|null $pipeline
 * @property string|null $business_manager_deshwal
 * @property string|null $stage
 * @property string|null $remarks
 * @property string|null $payment_terms
 * @property string|null $inspection_required
 * @property string|null $asset_list_received
 * @property string|null $send_opp_logistics
 * @property string|null $manager
 * @property string|null $forecast_category
 * @property string|null $inspection
 * @property string|null $drilling
 * @property string|null $degaussing
 * @property string|null $shredding
 * @property string|null $data_wiping
 * @property string|null $pickup
 * @property string|null $weighing
 * @property string|null $no_inspection_locations
 * @property string|null $no_drilling_locations
 * @property string|null $no_degaussing_location
 * @property string|null $no_shredding_location
 * @property string|null $no_wiping_location
 * @property string|null $no_pickup_locations
 * @property string|null $no_weighing_locations
 * @property string|null $inspection_billable
 * @property string|null $drilling_billable
 * @property string|null $degaussing_billable
 * @property string|null $shredding_billable
 * @property string|null $data_wiping_billable
 * @property string|null $weighing_billable
 * @property string|null $inspection_billing_type
 * @property string|null $drilling_billing_type
 * @property string|null $degaussing_billing_type
 * @property string|null $shredding_billing_type
 * @property string|null $data_wiping_billing_type
 * @property string|null $data_weighing_billing_type
 * @property string|null $bill_location
 * @property string|null $bill_legal_entity
 * @property string|null $bill_address
 * @property string|null $bill_state
 * @property string|null $bill_state_code
 * @property string|null $bill_gstin_no
 * @property string|null $bill_pincode
 * @property string|null $costing_done
 * @property string|null $business_entity
 * @property string|null $warehouse_address
 * @property string|null $warehouse_state
 * @property string|null $warehouse_state_code
 * @property string|null $warehouse_gstin_no
 * @property string|null $warehouse_pincode
 * @property string|null $tcs_applicable
 * @property string|null $tcs%
 * @property string|null $product_name
 * @property string|null $product_description
 * @property string|null $category
 * @property string|null $sub_category
 * @property string|null $hsn_code
 * @property string|null $cp(₹)
 * @property string|null $quantity_required
 * @property string|null $uom_desktop
 * @property string|null $cgst
 * @property string|null $sgst
 * @property int|null $igst
 * @property int|null $cgst_amount
 * @property int|null $sgst_amount
 * @property int|null $igst_amount
 * @property int|null $cost_price
 * @property int|null $total_opportunity_price
 * @property int|null $submit_costing
 * @property int|null $total_cost_price
 * @property int|null $total_base_cost_price
 * @property int|null $total_price
 * @property int|null $packing_cost
 * @property int|null $labour_cost
 * @property int|null $engineer_cost
 * @property int|null $halting_cost
 * @property int|null $vehicle_cost
 * @property int|null $local_union_charge
 * @property string|null $misc
 * @property int|null $unloading_cost
 * @property string|null $ceo_approval
 * @property int|null $expected_revenue
 */
class OpportunityInformation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'opportunity_information';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime', 'related_to', 'related_to_id'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'related_to', 'related_to_id', 'deleted', 'target_date', 'contact_mobile', 'closing_date', 'closure_year', 'igst', 'cgst_amount', 'sgst_amount', 'igst_amount', 'cost_price', 'total_opportunity_price', 'submit_costing', 'total_cost_price', 'total_base_cost_price', 'total_price', 'packing_cost', 'labour_cost', 'engineer_cost', 'halting_cost', 'vehicle_cost', 'local_union_charge', 'unloading_cost', 'expected_revenue'], 'integer'],
            [['createdtime', 'modifiedtime'], 'safe'],
            [['deal_owner', 'expected_closure_date', 'deal_name', 'commit', 'vendor_account_name', 'commit_month', 'commit_price', 'type', 'contact_name', 'contact_email', 'opportunity_close', 'opportunity_tentative_value', 'closure_month', 'vertical_manager', 'business_manager', 'commit_date', 'vertical_manager_deshwal', 'pipeline', 'business_manager_deshwal', 'stage', 'remarks', 'payment_terms', 'inspection_required', 'asset_list_received', 'send_opp_logistics', 'manager', 'forecast_category', 'inspection', 'drilling', 'degaussing', 'shredding', 'data_wiping', 'pickup', 'weighing', 'no_inspection_locations', 'no_drilling_locations', 'no_degaussing_location', 'no_shredding_location', 'no_wiping_location', 'no_pickup_locations', 'no_weighing_locations', 'inspection_billable', 'drilling_billable', 'degaussing_billable', 'shredding_billable', 'data_wiping_billable', 'weighing_billable', 'inspection_billing_type', 'drilling_billing_type', 'degaussing_billing_type', 'shredding_billing_type', 'data_wiping_billing_type', 'data_weighing_billing_type', 'bill_location', 'bill_legal_entity', 'bill_address', 'bill_state', 'bill_state_code', 'bill_gstin_no', 'bill_pincode', 'costing_done', 'business_entity', 'warehouse_address', 'warehouse_state', 'warehouse_state_code', 'warehouse_gstin_no', 'warehouse_pincode', 'tcs_applicable', 'tcs%', 'product_name', 'product_description', 'category', 'sub_category', 'hsn_code', 'cp(₹)', 'quantity_required', 'uom_desktop', 'cgst', 'sgst', 'misc', 'ceo_approval'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'opportunity_id' => 'Opportunityinfo ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'deal_owner' => 'Deal Owner',
            'expected_closure_date' => 'Expected Closure Date',
            'deal_name' => 'Deal Name',
            'commit' => 'Commit',
            'related_to' => 'Related To',
            'related_to_id' => 'Related To ID',
            'deleted' => 'Deleted',
            'vendor_account_name' => 'Vendor Account Name',
            'commit_month' => 'Commit Month',
            'commit_price' => 'Commit Price',
            'type' => 'Type',
            'target_date' => 'Target Date',
            'contact_name' => 'Contact Name',
            'contact_email' => 'Contact Email',
            'opportunity_close' => 'Opportunity Close',
            'contact_mobile' => 'Contact Mobile',
            'closing_date' => 'Closing Date',
            'opportunity_tentative_value' => 'Opportunity Tentative Value',
            'closure_month' => 'Closure Month',
            'vertical_manager' => 'Vertical Manager',
            'closure_year' => 'Closure Year',
            'business_manager' => 'Business Manager',
            'commit_date' => 'Commit Date',
            'vertical_manager_deshwal' => 'Vertical Manager Deshwal',
            'pipeline' => 'Pipeline',
            'business_manager_deshwal' => 'Business Manager Deshwal',
            'stage' => 'Stage',
            'remarks' => 'Remarks',
            'payment_terms' => 'Payment Terms',
            'inspection_required' => 'Inspection Required',
            'asset_list_received' => 'Asset List Received',
            'send_opp_logistics' => 'Send Opp Logistics',
            'manager' => 'Manager',
            'forecast_category' => 'Forecast Category',
            'inspection' => 'Inspection',
            'drilling' => 'Drilling',
            'degaussing' => 'Degaussing',
            'shredding' => 'Shredding',
            'data_wiping' => 'Data Wiping',
            'pickup' => 'Pickup',
            'weighing' => 'Weighing',
            'no_inspection_locations' => 'No Inspection Locations',
            'no_drilling_locations' => 'No Drilling Locations',
            'no_degaussing_location' => 'No Degaussing Location',
            'no_shredding_location' => 'No Shredding Location',
            'no_wiping_location' => 'No Wiping Location',
            'no_pickup_locations' => 'No Pickup Locations',
            'no_weighing_locations' => 'No Weighing Locations',
            'inspection_billable' => 'Inspection Billable',
            'drilling_billable' => 'Drilling Billable',
            'degaussing_billable' => 'Degaussing Billable',
            'shredding_billable' => 'Shredding Billable',
            'data_wiping_billable' => 'Data Wiping Billable',
            'weighing_billable' => 'Weighing Billable',
            'inspection_billing_type' => 'Inspection Billing Type',
            'drilling_billing_type' => 'Drilling Billing Type',
            'degaussing_billing_type' => 'Degaussing Billing Type',
            'shredding_billing_type' => 'Shredding Billing Type',
            'data_wiping_billing_type' => 'Data Wiping Billing Type',
            'data_weighing_billing_type' => 'Data Weighing Billing Type',
            'bill_location' => 'Bill Location',
            'bill_legal_entity' => 'Bill Legal Entity',
            'bill_address' => 'Bill Address',
            'bill_state' => 'Bill State',
            'bill_state_code' => 'Bill State Code',
            'bill_gstin_no' => 'Bill Gstin No',
            'bill_pincode' => 'Bill Pincode',
            'costing_done' => 'Costing Done',
            'business_entity' => 'Business Entity',
            'warehouse_address' => 'Warehouse Address',
            'warehouse_state' => 'Warehouse State',
            'warehouse_state_code' => 'Warehouse State Code',
            'warehouse_gstin_no' => 'Warehouse Gstin No',
            'warehouse_pincode' => 'Warehouse Pincode',
            'tcs_applicable' => 'Tcs Applicable',
            'tcs%' => 'Tcs%',
            'product_name' => 'Product Name',
            'product_description' => 'Product Description',
            'category' => 'Category',
            'sub_category' => 'Sub Category',
            'hsn_code' => 'Hsn Code',
            'cp(₹)' => 'Cp(₹)',
            'quantity_required' => 'Quantity Required',
            'uom_desktop' => 'Uom Desktop',
            'cgst' => 'Cgst',
            'sgst' => 'Sgst',
            'igst' => 'Igst',
            'cgst_amount' => 'Cgst Amount',
            'sgst_amount' => 'Sgst Amount',
            'igst_amount' => 'Igst Amount',
            'cost_price' => 'Cost Price',
            'total_opportunity_price' => 'Total Opportunity Price',
            'submit_costing' => 'Submit Costing',
            'total_cost_price' => 'Total Cost Price',
            'total_base_cost_price' => 'Total Base Cost Price',
            'total_price' => 'Total Price',
            'packing_cost' => 'Packing Cost',
            'labour_cost' => 'Labour Cost',
            'engineer_cost' => 'Engineer Cost',
            'halting_cost' => 'Halting Cost',
            'vehicle_cost' => 'Vehicle Cost',
            'local_union_charge' => 'Local Union Charge',
            'misc' => 'Misc',
            'unloading_cost' => 'Unloading Cost',
            'ceo_approval' => 'Ceo Approval',
            'expected_revenue' => 'Expected Revenue',
        ];
    }
}
