<?php

namespace app\models;

use Yii;
use yii\web\BadRequestHttpException;

/**
 * This is the model class for table "opportunity".
 *
 * @property int $opportunity_id
 * @property string $opportunity_no
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $deal_owner
 * @property string|null $expected_closure_date
 * @property string|null $deal_name
 * @property int|null $opportunity_type
 * @property string|null $commit
 * @property int|null $related_to
 * @property int|null $related_to_id
 * @property string|null $pan_number
 * @property int|null $warehouse_loc_business_entity
 * @property int|null $ship_to_location
 * @property int|null $ship_to_legal_name
 * @property int|null $ship_pan_number
 * @property int|null $product_category
 * @property int|null $requester_customer_name
 * @property string|null $requester_email_customer_email
 * @property string|null $requester_mobile
 * @property int|null $decision_maker_name
 * @property string|null $decision_maker_email
 * @property string|null $decision_maker_mobile
 * @property int|null $zone_region
 * @property int|null $team_name
 * @property string|null $team_responsible
 * @property int|null $sa_assigned
 * @property int|null $sf_assigned
 * @property int|null $procurement_team_member
 * @property string|null $comments
 * @property int|null $lead_source
 * @property int $deleted
 * @property string|null $vendor_account_name
 * @property int|null $commit_month
 * @property string|null $commit_price
 * @property string|null $business_type
 * @property string|null $target_date
 * @property string|null $contact_name
 * @property string|null $contact_email
 * @property string|null $opportunity_close
 * @property string|null $contact_mobile
 * @property string|null $closing_date
 * @property int|null $commit_year
 * @property int|null $closure_week
 * @property string|null $forcast_date
 * @property int|null $commit_week
 * @property string|null $opportunity_tentative_value
 * @property int|null $closure_month
 * @property string|null $vertical_manager
 * @property string|null $closure_year
 * @property string|null $business_manager
 * @property int|null $account_manager
 * @property int|null $account_director_rsm
 * @property int|null $opportunity_stage
 * @property int $submit_for_screening
 * @property int $submit_for_pricing
 * @property int $pricing_done
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
 * @property string|null $bill_from_location
 * @property string|null $bill_from_address
 * @property string|null $bill_from_state
 * @property string|null $bill_from_state_code
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
 * @property int|null $sub_category
 * @property string|null $hsn_code
 * @property string|null $cp(₹)
 * @property string|null $quantity_required
 * @property string|null $uom_desktop
 * @property string|null $cgst
 * @property string|null $sgst
 * @property string|null $igst
 * @property float|null $cgst_amount
 * @property float|null $sgst_amount
 * @property float|null $igst_amount
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
 * @property float|null $unloading_cost
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
 * @property string|null $customer_po_num
 * @property int|null $customer_payment_terms
 * @property string|null $customer_po_date
 * @property int|null $devit_isr
 * @property int|null $devit_vertical_manager
 * @property float|null $total_oppr_cost_tax_exclude
 * @property float|null $total_oppr_sale_tax_exclude
 * @property float|null $total_opportunity_cgst
 * @property float|null $total_opportunity_sgst
 * @property float|null $total_opportunity_igst
 * @property float|null $total_oppr_amount_tax_include
 * @property float|null $opportunity_margin
 * @property float|null $opportunity_margin_percentage
 * @property string|null $po_received_date
 * @property OpportunityProductDetail[] $opportunityProductDetails
 * @property OpportunityShipDetail[] $opportunityShipDetails
 */
class Opportunity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'opportunity';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['opportunity_no', 'ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime','vendor_account_name'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'opportunity_type', 'related_to', 'related_to_id', 'warehouse_loc_business_entity', 'ship_to_location', 'ship_to_legal_name', 'ship_pan_number', 'product_category', 'requester_customer_name', 'decision_maker_name', 'zone_region', 'team_name',  'lead_source', 'deleted', 'commit_month', 'commit_year', 'closure_week', 'commit_week', 'closure_month', 'account_manager', 'account_director_rsm', 'opportunity_stage', 'submit_for_screening', 'submit_for_pricing', 'pricing_done', 'stage', 'oem_referred', 'inspection', 'drilling', 'degaussing', 'shredding', 'data_wiping', 'pickup', 'weighing', 'sub_category', 'expected_revenue', 'margin_approval', 'currency', 'kyc_done', 'leadid', 'is_temp', 'customer_payment_terms', 'devit_isr', 'devit_vertical_manager','oem_tagging'], 'integer'],
            [['createdtime', 'modifiedtime', 'target_date', 'closing_date', 'forcast_date', 'submit_pricing_date', 'customer_po_date','po_received_date','prodpricing_done_date'], 'safe'],
            [['comments', 'bill_from_address', 'bill_address', 'description'], 'string'],
            [['cgst_amount', 'sgst_amount', 'igst_amount', 'cost_price', 'total_opportunity_price', 'submit_costing', 'total_cost_price', 'total_base_cost_price', 'total_price', 'packing_cost', 'labour_cost', 'engineer_cost', 'halting_cost', 'vehicle_cost', 'local_union_charge', 'unloading_cost', 'exchange_rate', 'total_oppr_cost_tax_exclude', 'total_oppr_sale_tax_exclude', 'total_opportunity_cgst', 'total_opportunity_sgst', 'total_opportunity_igst', 'total_oppr_amount_tax_include', 'opportunity_margin', 'opportunity_margin_percentage'], 'number'],
            [['opportunity_no', 'requester_email_customer_email', 'decision_maker_email', 'bill_from_state', 'bill_from_state_code', 'so_number', 'terms_conditions'], 'string', 'max' => 100],
            [['deal_owner', 'expected_closure_date', 'deal_name', 'commit', 'team_responsible', 'commit_price', 'business_type', 'contact_name', 'contact_email', 'opportunity_close', 'opportunity_tentative_value', 'vertical_manager', 'business_manager', 'commit_date', 'vertical_manager_deshwal', 'pipeline', 'business_manager_deshwal', 'remarks', 'payment_terms', 'inspection_required', 'asset_list_received', 'send_opp_logistics', 'manager', 'forecast_category', 'no_degaussing_location', 'no_shredding_location', 'no_wiping_location', 'no_pickup_locations', 'no_weighing_locations', 'inspection_billable', 'drilling_billable', 'degaussing_billable', 'shredding_billable', 'data_wiping_billable', 'weighing_billable', 'inspection_billing_type', 'drilling_billing_type', 'degaussing_billing_type', 'shredding_billing_type', 'data_wiping_billing_type', 'data_weighing_billing_type', 'bill_from_location', 'bill_location', 'bill_legal_entity', 'bill_state', 'bill_state_code', 'bill_gstin_no', 'bill_pincode', 'costing_done', 'business_entity', 'warehouse_address', 'warehouse_state', 'warehouse_state_code', 'warehouse_gstin_no', 'warehouse_pincode', 'tcs_applicable', 'tcs%', 'product_name', 'product_description', 'category', 'hsn_code', 'cp(₹)', 'quantity_required', 'uom_desktop', 'cgst', 'sgst', 'igst', 'delivery_terms', 'invoice_receiver_name', 'customer_po_num','po_received_date','sa_assigned', 'sf_assigned', 'procurement_team_member'], 'string', 'max' => 200],//'sa_assigned', 'sf_assigned', 'procurement_team_member', for CR point of multiselect
            [['pan_number', 'requester_mobile', 'decision_maker_mobile', 'contact_mobile'], 'string', 'max' => 15],
            [['closure_year'], 'string', 'max' => 5],
            // added for handling blank values saving in opportuntiy on 21 jan 2026 by deepika
            [['vendor_account_name'], 'trim'],
            [['vendor_account_name'], 'required', 'message' => 'Vendor Account Name cannot be blank.'],
            [['vendor_account_name'], 'integer', 'message' => 'Vendor Account Name must be a number.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'opportunity_id' => 'Opportunity ID',
            'opportunity_no' => 'Opportunity No',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'deal_owner' => 'Deal Owner',
            'expected_closure_date' => 'Expected Closure Date',
            'deal_name' => 'Deal Name',
            'opportunity_type' => 'Opportunity Type',
            'commit' => 'Commit',
            'related_to' => 'Related To',
            'related_to_id' => 'Related To ID',
            'pan_number' => 'Pan Number',
            'warehouse_loc_business_entity' => 'Warehouse Loc Business Entity',
            'ship_to_location' => 'Ship To Location',
            'ship_to_legal_name' => 'Ship To Legal Name',
            'ship_pan_number' => 'Ship Pan Number',
            'product_category' => 'Product Category',
            'requester_customer_name' => 'Requester Customer Name',
            'requester_email_customer_email' => 'Requester Email Customer Email',
            'requester_mobile' => 'Requester Mobile',
            'decision_maker_name' => 'Decision Maker Name',
            'decision_maker_email' => 'Decision Maker Email',
            'decision_maker_mobile' => 'Decision Maker Mobile',
            'zone_region' => 'Zone Region',
            'team_name' => 'Team Name',
            'team_responsible' => 'Team Responsible',
            'sa_assigned' => 'Sa Assigned',
            'sf_assigned' => 'Sf Assigned',
            'procurement_team_member' => 'Procurement Team Member',
            'comments' => 'Comments',
            'lead_source' => 'Lead Source',
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
            'commit_year' => 'Commit Year',
            'closure_week' => 'Closure Week',
            'forcast_date' => 'Forcast Date',
            'commit_week' => 'Commit Week',
            'opportunity_tentative_value' => 'Opportunity Tentative Value',
            'closure_month' => 'Closure Month',
            'vertical_manager' => 'Vertical Manager',
            'closure_year' => 'Closure Year',
            'business_manager' => 'Business Manager',
            'account_manager' => 'Account Manager',
            'account_director_rsm' => 'Account Director Rsm',
            'opportunity_stage' => 'Opportunity Stage',
            'submit_for_screening' => 'Submit For Screening',
            'submit_for_pricing' => 'Submit For Pricing',
            'pricing_done' => 'Pricing Done',
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
            'bill_from_location' => 'Bill From Location',
            'bill_from_address' => 'Bill From Address',
            'bill_from_state' => 'Bill From State',
            'bill_from_state_code' => 'Bill From State Code',
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
            'unloading_cost' => 'Unloading Cost',
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
            'customer_po_num' => 'Customer Po Num',
            'customer_payment_terms' => 'Customer Payment Terms',
            'customer_po_date' => 'Customer Po Date',
            'devit_isr' => 'Devit Isr',
            'devit_vertical_manager' => 'Devit Vertical Manager',
            'total_oppr_cost_tax_exclude' => 'Total Oppr Cost Tax Exclude',
            'total_oppr_sale_tax_exclude' => 'Total Oppr Sale Tax Exclude',
            'total_opportunity_cgst' => 'Total Opportunity Cgst',
            'total_opportunity_sgst' => 'Total Opportunity Sgst',
            'total_opportunity_igst' => 'Total Opportunity Igst',
            'total_oppr_amount_tax_include' => 'Total Oppr Amount Tax Include',
            'opportunity_margin' => 'Opportunity Margin',
            'opportunity_margin_percentage' => 'Opportunity Margin Percentage',
            'po_received_date' => 'Po Received Date',
            'oem_tagging' =>'Oem Tagging',
        ];
    }

    /**
     * Gets query for [[OpportunityProductDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOpportunityProductDetails()
    {
        return $this->hasMany(OpportunityProductDetail::class, ['opportunity_id' => 'opportunity_id']);
    }

    /**
     * Gets query for [[OpportunityShipDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOpportunityShipDetails()
    {
        return $this->hasMany(OpportunityShipDetail::class, ['opportunity_id' => 'opportunity_id']);
    }
    public function checkProducts($opportunity_id)
    {
        try {
            $cond = false; // Set initial condition as false

            // Check if any record exists in OpportunityProductDetail for the given opportunity_id
            $exists1 = OpportunityProductDetail::find()
                ->where(['opportunity_id' => $opportunity_id])
                ->exists();  // Returns true if any record exists, false otherwise




            if ($exists1) {
                $cond = true;
            }
            // $cond = false;


            return $cond;



            // Normal logic continues here if records exist

        } catch (BadRequestHttpException $e) {
            // Log the error message for debugging purposes
            Yii::error($e->getMessage(), __METHOD__);

            // Re-throw the BadRequestHttpException to be handled by Yii's exception handler
            throw $e;
        }
    }

    public function checkProductPricing($opportunity_id)
    {
        try {
            $cond = false; // Set initial condition as false
            $sql = "Select * from `opportunity_product_detail` where opportunity_id=:opportunity_id";
            $records = Yii::$app->db->createCommand($sql)->bindValue(':opportunity_id', $opportunity_id)->queryAll();
            // print_r($records);die; 
            foreach ($records as $key => $value) {
                // print_r($value);
                $add_price_validity = $value['add_price_validity'];
                $add_product_delivery_timeline = $value['add_product_delivery_timeline'];
                $add_product_warranty = $value['add_product_warranty'];
                $reject = $value['reject'];
                $cost_price = $value['cost_price'];
                if ((empty($add_price_validity) && empty($add_product_delivery_timeline) && empty($add_product_warranty)) && (empty($reject) || $reject == 0) && empty($cost_price)) {
                    $cond = true;
                    break;
                }
            }
            // echo $cond;
            // die;
            // If $cond is true, throw an exception
            if ($cond) {

                // Set a flash message (if necessary)
                Yii::$app->session->setFlash('error', 'Invalid request.Error: Invalid request. Fill Product wise validity or Reject with Remarks');

                // Throw an exception with a custom message
                throw new BadRequestHttpException('Invalid request.Error: Invalid request. Fill Product wise Cost Price ,Price Validity and Product Delivery Time Line or Reject with Remarks');
            }

            // Continue with normal processing if condition is not met
            // Your normal logic continues here if $cond is false

        } catch (BadRequestHttpException $e) {
            // Log the error message for debugging purposes
            Yii::error($e->getMessage(), __METHOD__);

            // Re-throw the exception so Yii can handle it (display error page, etc.)
            throw $e;
        }
    }

     public function showEditBtnOpportunity($record = [],$user = [])
    {   
        if (isset($user) && $user->is_super_admin && isset($record['opportunity_id'])) { 
                $quotesDit = QuotesDit::find()->where(['opportunity_name' => $record['opportunity_id']])
                ->andWhere(['=', 'deleted', 0])->all();
                $allQuotesCancelled = true;
                if (!empty($quotesDit)) {
                    foreach ($quotesDit as $quote) { 
                        if ($quote->quote_stage != 7) {
                            $allQuotesCancelled = false;
                            break;
                        }
                    }
                }
                if ($allQuotesCancelled) {
                    return false;
                }
        }
        return true;
    }
}
