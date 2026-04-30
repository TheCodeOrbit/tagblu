<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sourcingdeal".
 *
 * @property int $sourcingdeal_id
 * @property string $sourcingdeal_no
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $expected_closure_date
 * @property string|null $deal_name
 * @property int|null $opportunity_type
 * @property string|null $commit
 * @property int|null $related_to
 * @property int|null $related_to_id
 * @property int $deleted
 * @property string|null $vendor_account_name
 * @property string|null $commit_month
 * @property string|null $commit_price
 * @property string|null $business_type
 * @property string|null $target_date
 * @property string|null $contact_name
 * @property string|null $contact_email
 * @property string|null $opportunity_close
 * @property string|null $contact_mobile
 * @property string|null $closing_date
 * @property string|null $opportunity_tentative_value
 * @property int|null $closure_month
 * @property string|null $vertical_manager
 * @property string|null $closure_year
 * @property string|null $business_manager
 * @property string|null $commit_date
 * @property string|null $vertical_manager_deshwal
 * @property string|null $pipeline
 * @property string|null $business_manager_deshwal
 * @property int|null $stage
 * @property string|null $remarks
 * @property string|null $payment_terms
 * @property string|null $inspection_required
 * @property string|null $asset_list_received
 * @property string|null $send_opp_logistics
 * @property string|null $manager
 * @property int $oem_referred
 * @property string|null $submit_pricing_date
 * @property string|null $forecast_category
 * @property int|null $inspection
 * @property int|null $drilling
 * @property int|null $degaussing
 * @property int|null $shredding
 * @property int|null $data_wiping
 * @property int|null $pickup
 * @property int|null $weighing
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
 * @property string|null $tcs
 * @property string|null $product_name
 * @property string|null $product_description
 * @property string|null $category
 * @property int|null $sub_category
 * @property string|null $hsn_code
 * @property string|null $cp
 * @property string|null $quantity_required
 * @property string|null $uom_desktop
 * @property float|null $cost_price
 * @property float|null $total_opportunity_price
 * @property float|null $submit_costing
 * @property float|null $total_cost_price
 * @property float|null $total_base_cost_price
 * @property float|null $total_price
 * @property float|null $packing_cost
 * @property float|null $labour_cost
 * @property float|null $engineer_cost
 * @property float|null $halting_cost
 * @property float|null $vehicle_cost
 * @property float|null $local_union_charge
 * @property string|null $misc
 * @property int|null $unloading_cost
 * @property string|null $ceo_approval
 * @property float|null $total_sourcing_deal_amount
 * @property float|null $total_sourcing_deal_cost
 * @property float|null $total_sourcing_deal_sale
 * @property float|null $service_sale
 * @property float|null $service_cost
 * @property float|null $product_cost
 * @property float|null $product_sale
 * @property float|null $margin
 * @property float|null $margin_percentage
 * @property int|null $expected_revenue
 * @property int|null $margin_approval
 * @property int|null $currency
 * @property string|null $description
 * @property string|null $so_number
 * @property float|null $exchange_rate
 * @property string|null $terms_conditions
 * @property int $kyc_done
 * @property string|null $delivery_terms
 * @property string|null $invoice_receiver_name
 * @property int|null $leadid
 * @property int $is_temp
 * @property string|null $opportunity_name
 * @property string|null $account_name
 * @property string|null $role
 * @property string|null $closing_date1
 * @property string|null $designation
 * @property string|null $department
 * @property string|null $closure_week
 * @property string|null $payment_type
 * @property string|null $is_contract
 * @property string|null $type_of_contract
 * @property string|null $lead_source
 * @property string|null $oem
 * @property int|null $oem_manager
 * @property string|null $oem_manager_name
 * @property string|null $oem_manager_email
 * @property string|null $loss_reason
 * @property string|null $opportunity_score
 * @property string|null $campaign_source
 * @property string|null $probability
 * @property string|null $special_pricing
 * @property string|null $submit_for_pricing
 * @property float|null $total_logistics_cost
 * @property float|null $cost_price_gst_exclude
 * @property float|null $cost_price_gst_include
 * @property float|null $sales_price_gst_exclude
 * @property float|null $sales_price_gst_include
 * @property float|null $logistics_cost
 * @property float|null $repairing_cost
 * @property float|null $exp_cost
 * @property float|null $total_purchase_cost
 * @property float|null $additional_cost
 * @property float|null $actual_profit
 * @property float|null $actual_profit_percentage
 * @property int $submit_for_logistics
 */
class Sourcingdeal extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sourcingdeal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sourcingdeal_no', 'ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'opportunity_type', 'related_to', 'related_to_id', 'deleted', 'closure_month', 'stage', 'oem_referred', 'inspection', 'drilling', 'degaussing', 'shredding', 'data_wiping', 'pickup', 'weighing', 'sub_category',  'expected_revenue', 'margin_approval', 'currency', 'kyc_done', 'leadid', 'is_temp', 'oem_manager', 'submit_for_logistics','pricing_type','pickup_request','type_of_rc','type_of_contracts'], 'integer'],
            [['createdtime', 'modifiedtime', 'target_date', 'closing_date', 'submit_pricing_date', 'closing_date1'], 'safe'],
            [['cost_price', 'total_opportunity_price', 'submit_costing', 'total_cost_price', 'total_base_cost_price', 'total_price', 'packing_cost', 'labour_cost', 'engineer_cost', 'halting_cost', 'vehicle_cost', 'local_union_charge', 'total_sourcing_deal_amount', 'total_sourcing_deal_cost', 'total_sourcing_deal_sale', 'service_sale', 'service_cost', 'product_cost', 'product_sale', 'margin', 'margin_percentage', 'exchange_rate', 'total_logistics_cost', 'cost_price_gst_exclude', 'cost_price_gst_include', 'sales_price_gst_exclude', 'sales_price_gst_include', 'logistics_cost', 'repairing_cost', 'exp_cost', 'total_purchase_cost', 'additional_cost', 'actual_profit', 'actual_profit_percentage'], 'number'],
            [['description'], 'string'],
            [['sourcingdeal_no', 'so_number', 'terms_conditions', 'opportunity_name', 'account_name', 'role', 'designation', 'department', 'closure_week', 'payment_type', 'is_contract', 'type_of_contract', 'lead_source', 'oem', 'oem_manager_name', 'oem_manager_email', 'loss_reason', 'opportunity_score', 'campaign_source', 'probability', 'special_pricing', 'submit_for_pricing','pickup_request_id'], 'string', 'max' => 100],
            [['expected_closure_date', 'deal_name', 'commit', 'vendor_account_name', 'commit_month', 'commit_price', 'business_type', 'contact_name', 'contact_email', 'opportunity_close', 'opportunity_tentative_value', 'vertical_manager', 'business_manager', 'commit_date', 'vertical_manager_deshwal', 'pipeline', 'business_manager_deshwal', 'remarks', 'payment_terms', 'inspection_required', 'asset_list_received', 'send_opp_logistics', 'manager', 'forecast_category', 'no_inspection_locations', 'no_drilling_locations', 'no_degaussing_location', 'no_shredding_location', 'no_wiping_location', 'no_pickup_locations', 'no_weighing_locations', 'inspection_billable', 'drilling_billable', 'degaussing_billable', 'shredding_billable', 'data_wiping_billable', 'weighing_billable', 'inspection_billing_type', 'drilling_billing_type', 'degaussing_billing_type', 'shredding_billing_type', 'data_wiping_billing_type', 'data_weighing_billing_type', 'bill_location', 'bill_legal_entity', 'bill_address', 'bill_state', 'bill_state_code', 'bill_gstin_no', 'bill_pincode', 'costing_done', 'business_entity', 'warehouse_address', 'warehouse_state', 'warehouse_state_code', 'warehouse_gstin_no', 'warehouse_pincode', 'tcs_applicable', 'tcs', 'product_name', 'product_description', 'category', 'hsn_code', 'cp', 'quantity_required', 'uom_desktop', 'misc', 'ceo_approval', 'delivery_terms', 'invoice_receiver_name'], 'string', 'max' => 200],
            [['contact_mobile'], 'string', 'max' => 15],
            [['closure_year'], 'string', 'max' => 5],
            //CR point changes
            [['commit_date', 'commit_week','account_manager', 'deshwal_isr'], 'default', 'value' => null],
            [['unloading_cost'],'number'],
            // added for handling blank values saving in by ptpatel on date 24-01-2026
            [['vendor_account_name'], 'trim'],
            [['vendor_account_name'], 'required', 'message' => 'Account Name cannot be blank.'],
            [['vendor_account_name'], 'integer', 'message' => 'Account Name must be a number.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'sourcingdeal_id' => 'Sourcingdeal ID',
            'sourcingdeal_no' => 'Sourcingdeal No',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'expected_closure_date' => 'Expected Closure Date',
            'deal_name' => 'Deal Name',
            'opportunity_type' => 'Opportunity Type',
            'commit' => 'Commit',
            'related_to' => 'Related To',
            'related_to_id' => 'Related To ID',
            'deleted' => 'Deleted',
            'vendor_account_name' => 'Vendor Account Name',
            'commit_month' => 'Commit Month',
            'commit_price' => 'Commit Price',
            'business_type' => 'Business Type',
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
            'oem_referred' => 'Oem Referred',
            'submit_pricing_date' => 'Submit Pricing Date',
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
            'tcs' => 'Tcs',
            'product_name' => 'Product Name',
            'product_description' => 'Product Description',
            'category' => 'Category',
            'sub_category' => 'Sub Category',
            'hsn_code' => 'Hsn Code',
            'cp' => 'Cp',
            'quantity_required' => 'Quantity Required',
            'uom_desktop' => 'Uom Desktop',
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
            'total_sourcing_deal_amount' => 'Total Sourcing Deal Amount',
            'total_sourcing_deal_cost' => 'Total Sourcing Deal Cost',
            'total_sourcing_deal_sale' => 'Total Sourcing Deal Sale',
            'service_sale' => 'Service Sale',
            'service_cost' => 'Service Cost',
            'product_cost' => 'Product Cost',
            'product_sale' => 'Product Sale',
            'margin' => 'Margin',
            'margin_percentage' => 'Margin Percentage',
            'expected_revenue' => 'Expected Revenue',
            'margin_approval' => 'Margin Approval',
            'currency' => 'Currency',
            'description' => 'Description',
            'so_number' => 'So Number',
            'exchange_rate' => 'Exchange Rate',
            'terms_conditions' => 'Terms Conditions',
            'kyc_done' => 'Kyc Done',
            'delivery_terms' => 'Delivery Terms',
            'invoice_receiver_name' => 'Invoice Receiver Name',
            'leadid' => 'Leadid',
            'is_temp' => 'Is Temp',
            'opportunity_name' => 'Opportunity Name',
            'account_name' => 'Account Name',
            'role' => 'Role',
            'closing_date1' => 'Closing Date1',
            'designation' => 'Designation',
            'department' => 'Department',
            'closure_week' => 'Closure Week',
            'payment_type' => 'Payment Type',
            'is_contract' => 'Is Contract',
            'type_of_contract' => 'Type Of Contract',
            'lead_source' => 'Lead Source',
            'oem' => 'Oem',
            'oem_manager' => 'Oem Manager',
            'oem_manager_name' => 'Oem Manager Name',
            'oem_manager_email' => 'Oem Manager Email',
            'loss_reason' => 'Loss Reason',
            'opportunity_score' => 'Opportunity Score',
            'campaign_source' => 'Campaign Source',
            'probability' => 'Probability',
            'special_pricing' => 'Special Pricing',
            'submit_for_pricing' => 'Submit For Pricing',
            'total_logistics_cost' => 'Total Logistics Cost',
            'cost_price_gst_exclude' => 'Cost Price Gst Exclude',
            'cost_price_gst_include' => 'Cost Price Gst Include',
            'sales_price_gst_exclude' => 'Sales Price Gst Exclude',
            'sales_price_gst_include' => 'Sales Price Gst Include',
            'logistics_cost' => 'Logistics Cost',
            'repairing_cost' => 'Repairing Cost',
            'exp_cost' => 'Exp Cost',
            'total_purchase_cost' => 'Total Purchase Cost',
            'additional_cost' => 'Additional Cost',
            'actual_profit' => 'Actual Profit',
            'actual_profit_percentage' => 'Actual Profit Percentage',
            'submit_for_logistics' => 'Submit For Logistics',
            'pricing_type'=>'Pricing Type',
            //adde for CR point
            'commit_date' => 'Commit Date',
            'commit_week' => 'Commit Week',
            'account_manager' => 'Account Manager',
            'deshwal_isr' => 'Deshwal Isr',
        ];
    }
    public function getprobability($stage)
    {
        $sql = "select probability from sourcingdeal_stage where stage_id = :stageid";
        $res = Yii::$app->db->createCommand($sql)->bindValue(":stageid",$stage)->queryOne();
        if(isset($res['probability']))
        return $res['probability'];
        else return '';
    }

    function saveToVpReports($RecordId)
    {
        $Record = (int)$RecordId;
        $this->updatePickuprequest($RecordId);
    }
    function updatePickuprequest($RecordId)
    {
        $sql = "SELECT pickup_request,stage FROM sourcingdeal where sourcingdeal_id=:RecordId";
        $result = Yii::$app->db->createCommand($sql)->bindValue(":RecordId",$RecordId)->queryOne();
        $pickup_request= $result['pickup_request'];
        if(!empty($pickup_request))
        {
        $sourcingdeal_stage= $result['stage'];

            $sql = "update customer_pickup_request set sourcingdeal_stage=:sourcingdeal_stage where pickup_request_id=:pickup_request";
            $result = Yii::$app->db->createCommand($sql)
            ->bindValue(":sourcingdeal_stage",$sourcingdeal_stage)
            ->bindValue(":pickup_request",$pickup_request)->execute();
        }
    }

    /**
     * functionalithy For stage = 14 (Won) and No payments,
     * No Quotes and no services taken  against it so display the edit btn only to super admin
     * @Date 13/11/2025
     * @param mixed $sourcingDeal
     * @param mixed $user
     * @return bool|null
     */
    public function showEditBtnSD($sourcingDeal,$user= [])
    {
        if (isset($user) && $user->is_super_admin ) {

            $quotes = Quotes::find()->where(['related_to_id' => $sourcingDeal['sourcingdeal_id']])
            ->andWhere(['=', 'deleted', 0])->all();
            $allQuotesCancelledOrNone = true;
            if (!empty($quotes)) {
                foreach ($quotes as $quote) {
                    if ($quote->quote_stage != 4) {
                        $allQuotesCancelledOrNone = false;
                        break;
                    }
                }
            } 
            $payments = Payments::find()->where(['sourcing_deal' => $sourcingDeal['sourcingdeal_id']])
            ->andWhere(['=', 'deleted', 0])->all();
            $allPaymentsRejectedOrNone = true;
            if (!empty($payments)) {
                foreach ($payments as $payment) {
                    if ($payment->stage != 4) { 
                        $allPaymentsRejectedOrNone = false;
                        break;
                    }
                }
            }

            $sd_id = $sourcingDeal['sourcingdeal_id'];

            $serviceExists = (
                Degaussing::find()->where(['opportunity_name' => $sd_id])->andWhere(['=', 'deleted', 0])->exists() ||
                Pickup::find()->where(['opportuity_name' => $sd_id])->andWhere(['=', 'deleted', 0])->exists() ||
                DataWiping::find()->where(['opportunity_name' => $sd_id])->andWhere(['=', 'deleted', 0])->exists() ||
                Drilling::find()->where(['opportunity_name' => $sd_id])->andWhere(['=', 'deleted', 0])->exists() ||
                Inspection::find()->where(['sourcing_deal' => $sd_id])->andWhere(['=', 'deleted', 0])->exists() ||
                Shredding::find()->where(['opportunity_name' => $sd_id])->andWhere(['=', 'deleted', 0])->exists() ||
                Weighing::find()->where(['opportunity_name' => $sd_id])->andWhere(['=', 'deleted', 0])->exists()
            );


            if ($allQuotesCancelledOrNone && $allPaymentsRejectedOrNone  && !$serviceExists) {
                return null;
            }
            return true;
        }
        
        return true;
    }

}
