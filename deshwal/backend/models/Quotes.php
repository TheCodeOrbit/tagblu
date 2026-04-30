<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "quotes".
 *
 * @property int $quotes_id
 * @property string $quotes_no
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $subject
 * @property string $quote_creation_date
 * @property string|null $quote_stage
 * @property string|null $payment_terms
 * @property string|null $exchange_rate
 * @property string|null $approval
 * @property string|null $gross_profit
 * @property string|null $margin_percent
 * @property string|null $quotation_number
 * @property string|null $account_name
 * @property string|null $deal_name
 * @property string|null $expiry_date
 * @property string|null $delivery_terms
 * @property string|null $contact_name
 * @property string|null $currency
 * @property string|null $team
 * @property int $update_team
 * @property int $update_long_description
 * @property string|null $po_type
 * @property string|null $vendor_name
 * @property string|null $kyc_status
 * @property string|null $bill_name
 * @property string|null $bill_legal_name
 * @property string|null $bill_address
 * @property string|null $bill_state
 * @property string|null $bill_city
 * @property string|null $bill_state_code
 * @property string|null $bill_gstin_no_uin
 * @property string|null $bill_pincode
 * @property string|null $bill_pan_no
 * @property string|null $business_entity
 * @property string|null $warehouse_name
 * @property string|null $warehouse_address
 * @property string|null $warehouse_city
 * @property string|null $warehouse_state
 * @property string|null $warehouse_state_code
 * @property string|null $warehouse_pincode
 * @property string|null $warehouse_gstin_no
 * @property string|null $product_name
 * @property string|null $category
 * @property string|null $hsn_code
 * @property string|null $quantity
 * @property string|null $uom
 * @property string|null $list_price
 * @property string|null $cost_price
 * @property string|null $cgst_percent
 * @property string|null $sgst_percent
 * @property string|null $igst_percent
 * @property string|null $cgst_amount
 * @property string|null $sgst_amount
 * @property string|null $igst_amount
 * @property string|null $basic_price
 * @property string|null $basic_cp
 * @property string|null $total_amount
 * @property string|null $p_name
 * @property string|null $p_qty
 * @property string|null $p_lngdes
 * @property string|null $p_longdes
 * @property string|null $amount_word
 * @property string|null $total_sgst_amount
 * @property string|null $quote_created
 * @property string|null $email
 * @property string|null $total_cgst_amount
 * @property string|null $total_igst_amount
 * @property string|null $mobile
 * @property float|null $sub_total
 * @property float|null $final_amount
 * @property string|null $terms_and_conditions
 * @property string|null $opportunity_stage
 * @property int|null $related_to
 * @property int|null $related_to_id
 * @property string|null $qu_bill_location_name
 * @property string|null $qu_bill_address
 * @property string|null $qu_bill_state
 * @property string|null $qu_bill_pin_code
 * @property string|null $qu_bill_warehouse_name
 * @property string|null $qu_bill_city
 * @property string|null $qu_bill_state_code
 * @property string|null $qu_bill_gstin_no
 * @property float|null $tcs_percentage
 * @property float|null $tcs_amount
 * @property float|null $grand_total
 * @property int $deleted
 *
 * @property QuotedItemsDetail[] $quotedItemsDetails
 */
class Quotes extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quotes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // 'account_name', //remove by ptpatel on date 24-01-2026
            [['subject', 'quote_stage', 'payment_terms', 'exchange_rate', 'approval', 'gross_profit', 'margin_percent', 'quotation_number',  'deal_name', 'expiry_date', 'delivery_terms', 'contact_name', 'currency', 'team', 'po_type', 'vendor_name', 'kyc_status', 'bill_name', 'bill_legal_name', 'bill_address', 'bill_state', 'bill_city', 'bill_state_code', 'bill_gstin_no_uin', 'bill_pincode', 'bill_pan_no', 'business_entity', 'warehouse_name', 'warehouse_address', 'warehouse_city', 'warehouse_state', 'warehouse_state_code', 'warehouse_pincode', 'warehouse_gstin_no', 'product_name', 'category', 'hsn_code', 'quantity', 'uom', 'list_price', 'cost_price', 'cgst_percent', 'sgst_percent', 'igst_percent', 'cgst_amount', 'sgst_amount', 'igst_amount', 'basic_price', 'basic_cp', 'total_amount', 'p_name', 'p_qty', 'p_lngdes', 'p_longdes', 'amount_word', 'total_sgst_amount', 'quote_created', 'email', 'total_cgst_amount', 'total_igst_amount', 'mobile', 'sub_total', 'final_amount', 'terms_and_conditions', 'opportunity_stage', 'related_to', 'related_to_id', 'qu_bill_location_name', 'qu_bill_address', 'qu_bill_state', 'qu_bill_pin_code', 'qu_bill_warehouse_name', 'qu_bill_city', 'qu_bill_state_code', 'qu_bill_gstin_no', 'tcs_percentage', 'tcs_amount', 'grand_total'], 'default', 'value' => null],
            [['deleted'], 'default', 'value' => 0],
            [['quotes_no', 'ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime', 'quote_creation_date'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'update_team', 'update_long_description', 'related_to', 'related_to_id', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime', 'quote_creation_date', 'expiry_date'], 'safe'],
            [['warehouse_address', 'terms_and_conditions', 'qu_bill_address'], 'string'],
            [['sub_total', 'final_amount', 'tcs_percentage', 'tcs_amount', 'grand_total'], 'number'],
            [['quotes_no', 'subject', 'quote_stage', 'payment_terms', 'exchange_rate', 'approval', 'gross_profit', 'margin_percent', 'quotation_number', 'account_name', 'deal_name', 'delivery_terms', 'contact_name', 'currency', 'team', 'po_type', 'vendor_name', 'kyc_status', 'bill_name', 'bill_legal_name', 'bill_state', 'bill_city', 'bill_state_code', 'bill_gstin_no_uin', 'bill_pincode', 'bill_pan_no', 'business_entity', 'warehouse_name', 'warehouse_city', 'warehouse_state', 'warehouse_state_code', 'warehouse_pincode', 'warehouse_gstin_no', 'product_name', 'category', 'hsn_code', 'quantity', 'uom', 'list_price', 'cost_price', 'cgst_percent', 'sgst_percent', 'igst_percent', 'cgst_amount', 'sgst_amount', 'igst_amount', 'basic_price', 'basic_cp', 'total_amount', 'p_name', 'p_qty', 'p_lngdes', 'p_longdes', 'amount_word', 'total_sgst_amount', 'quote_created', 'email', 'total_cgst_amount', 'total_igst_amount', 'opportunity_stage', 'qu_bill_location_name', 'qu_bill_warehouse_name'], 'string', 'max' => 200],
            [['bill_address'], 'string', 'max' => 3000],
            [['mobile', 'qu_bill_pin_code', 'qu_bill_state_code','round_off'], 'string', 'max' => 20],
            [['qu_bill_state', 'qu_bill_city'], 'string', 'max' => 50],
            [['qu_bill_gstin_no'], 'string', 'max' => 100],
            // added for handling blank values saving in by ptpatel on date 24-01-2026
            [['account_name'], 'trim'],
            [['account_name'], 'required', 'message' => 'Account Name cannot be blank.'],
            [['account_name'], 'integer', 'message' => 'Account Name must be a number.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'quotes_id' => 'Quotes ID',
            'quotes_no' => 'Quotes No',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'subject' => 'Subject',
            'quote_creation_date' => 'Quote Creation Date',
            'quote_stage' => 'Quote Stage',
            'payment_terms' => 'Payment Terms',
            'exchange_rate' => 'Exchange Rate',
            'approval' => 'Approval',
            'gross_profit' => 'Gross Profit',
            'margin_percent' => 'Margin Percent',
            'quotation_number' => 'Quotation Number',
            'account_name' => 'Account Name',
            'deal_name' => 'Deal Name',
            'expiry_date' => 'Expiry Date',
            'delivery_terms' => 'Delivery Terms',
            'contact_name' => 'Contact Name',
            'currency' => 'Currency',
            'team' => 'Team',
            'update_team' => 'Update Team',
            'update_long_description' => 'Update Long Description',
            'po_type' => 'Po Type',
            'vendor_name' => 'Vendor Name',
            'kyc_status' => 'Kyc Status',
            'bill_name' => 'Bill Name',
            'bill_legal_name' => 'Bill Legal Name',
            'bill_address' => 'Bill Address',
            'bill_state' => 'Bill State',
            'bill_city' => 'Bill City',
            'bill_state_code' => 'Bill State Code',
            'bill_gstin_no_uin' => 'Bill Gstin No Uin',
            'bill_pincode' => 'Bill Pincode',
            'bill_pan_no' => 'Bill Pan No',
            'business_entity' => 'Business Entity',
            'warehouse_name' => 'Warehouse Name',
            'warehouse_address' => 'Warehouse Address',
            'warehouse_city' => 'Warehouse City',
            'warehouse_state' => 'Warehouse State',
            'warehouse_state_code' => 'Warehouse State Code',
            'warehouse_pincode' => 'Warehouse Pincode',
            'warehouse_gstin_no' => 'Warehouse Gstin No',
            'product_name' => 'Product Name',
            'category' => 'Category',
            'hsn_code' => 'Hsn Code',
            'quantity' => 'Quantity',
            'uom' => 'Uom',
            'list_price' => 'List Price',
            'cost_price' => 'Cost Price',
            'cgst_percent' => 'Cgst Percent',
            'sgst_percent' => 'Sgst Percent',
            'igst_percent' => 'Igst Percent',
            'cgst_amount' => 'Cgst Amount',
            'sgst_amount' => 'Sgst Amount',
            'igst_amount' => 'Igst Amount',
            'basic_price' => 'Basic Price',
            'basic_cp' => 'Basic Cp',
            'total_amount' => 'Total Amount',
            'p_name' => 'P Name',
            'p_qty' => 'P Qty',
            'p_lngdes' => 'P Lngdes',
            'p_longdes' => 'P Longdes',
            'amount_word' => 'Amount Word',
            'total_sgst_amount' => 'Total Sgst Amount',
            'quote_created' => 'Quote Created',
            'email' => 'Email',
            'total_cgst_amount' => 'Total Cgst Amount',
            'total_igst_amount' => 'Total Igst Amount',
            'mobile' => 'Mobile',
            'sub_total' => 'Sub Total',
            'final_amount' => 'Final Amount',
            'terms_and_conditions' => 'Terms And Conditions',
            'opportunity_stage' => 'Opportunity Stage',
            'related_to' => 'Related To',
            'related_to_id' => 'Related To ID',
            'qu_bill_location_name' => 'Qu Bill Location Name',
            'qu_bill_address' => 'Qu Bill Address',
            'qu_bill_state' => 'Qu Bill State',
            'qu_bill_pin_code' => 'Qu Bill Pin Code',
            'qu_bill_warehouse_name' => 'Qu Bill Warehouse Name',
            'qu_bill_city' => 'Qu Bill City',
            'qu_bill_state_code' => 'Qu Bill State Code',
            'qu_bill_gstin_no' => 'Qu Bill Gstin No',
            'tcs_percentage' => 'Tcs Percentage',
            'tcs_amount' => 'Tcs Amount',
            'grand_total' => 'Grand Total',
            'deleted' => 'Deleted',
            'round_off'=>'Round Off'
        ];
    }

    /**
     * Gets query for [[QuotedItemsDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuotedItemsDetails()
    {
        return $this->hasMany(QuotedItemsDetail::class, ['quotes_id' => 'quotes_id']);
    }

    function saveToVpReports($RecordId)
    {
        $Record = (int)$RecordId;
        $this->updatePickuprequest($RecordId);
    }
    function updatePickuprequest($RecordId)
    {
        $sql = "SELECT sd.pickup_request,sd.stage FROM quotes join sourcingdeal sd on sd.sourcingdeal_id = quotes.related_to_id and quotes.related_to =51 where quotes_id=:RecordId";
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
     * *Added functionalithy For stage = 1 (Approve) and Purchase
     *  Order against it so display the edit btn only to super admin 
     * @Date 13/11/2025
     * @param mixed $record
     * @param mixed $user
     * @return bool|null
     */
    public function showEditBtnQuotes($record = [],$user = [])
    {
        if (isset($user) && $user->is_super_admin) { 
                $purchaseOrders = PurchaseOrder::find()->where(['quote' => $record['quotes_id']])
                ->andWhere(['=', 'deleted', 0])->all();
                $allPOCancelled = true;
                if (!empty($purchaseOrders)) {
                    foreach ($purchaseOrders as $po) {
                        if ($po->stage != 5) {
                            $allPOCancelled = false;
                            break;
                        }
                    }
                }
                if ($allPOCancelled) {
                    return null;
                }
        }
        return true;
    }

}
