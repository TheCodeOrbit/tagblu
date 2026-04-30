<?php

namespace app\models;

use Exception;
use Yii;

/**
 * This is the model class for table "purchase_order".
 *
 * @property int $purchase_order_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string $purchase_order_no
 * @property string|null $subject
 * @property string|null $stage
 * @property string|null $account_name
 * @property string|null $opportunity_name
 * @property string|null $contact_name
 * @property string|null $currency
 * @property string|null $submit_approval
 * @property string|null $purchase_order_number
 * @property string|null $vendor_name
 * @property string|null $po_expiry_date
 * @property string|null $exchange_rate
 * @property string|null $inspection_vendor
 * @property string|null $drilling_vendor
 * @property string|null $type
 * @property string|null $material_receiver_name
 * @property string|null $material_receiver_contact
 * @property string|null $material_receiver_email
 * @property string|null $nature_po
 * @property string|null $po_type
 * @property string|null $bill_address
 * @property string|null $billing_city
 * @property string|null $billing_state
 * @property string|null $billing_code
 * @property string|null $bill_gstin_no
 * @property string|null $bill_pan_no
 * @property string|null $pincode
 * @property string|null $ship_warehouse_name
 * @property string|null $warehouse_address
 * @property string|null $warehouse_state
 * @property string|null $warehouse_state_code
 * @property string|null $warehouse_pincode
 * @property string|null $warehouse_gstin_no
 * @property string|null $attach_file
 * @property float|null $total_amount
 * @property string|null $terms_conditions
 * @property string|null $payment_terms
 * @property int $deleted
 * @property string|null $comment
 * @property string|null $bill_to_name
 * @property string|null $bill_legal_name
 * @property string|null $billing_state_code
 * @property string|null $bill_to_pincode
 * @property string|null $warehouse_business_entity
 * @property string|null $warehouse_city
 * @property string|null $quote
 * @property float|null $basic_cp
 * @property float|null $total_cgst_amount
 * @property float|null $total_sgst_amount
 * @property float|null $total_igst_amount
 * @property float|null $total_amount1
 * @property string|null $po_bill_location_name
 * @property string|null $po_bill_address
 * @property string|null $po_bill_state
 * @property string|null $po_bill_pin_code
 * @property string|null $po_bill_warehouse_name
 * @property string|null $po_bill_city
 * @property string|null $po_bill_state_code
 * @property string|null $po_bill_gstin_no
 *
 * @property PurchaseOrderItemsdetail[] $purchaseOrderItemsdetails
 */
class PurchaseOrder extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'purchase_order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subject', 'stage', 'account_name', 'opportunity_name', 'contact_name', 'currency', 'submit_approval', 'purchase_order_number', 'vendor_name', 'po_expiry_date', 'exchange_rate', 'inspection_vendor', 'drilling_vendor', 'type', 'material_receiver_name', 'material_receiver_contact', 'material_receiver_email', 'nature_po', 'po_type', 'bill_address', 'billing_city', 'billing_state', 'billing_code', 'bill_gstin_no', 'bill_pan_no', 'pincode', 'ship_warehouse_name', 'warehouse_address', 'warehouse_state', 'warehouse_state_code', 'warehouse_pincode', 'warehouse_gstin_no', 'attach_file', 'total_amount', 'terms_conditions', 'payment_terms', 'comment', 'bill_to_name', 'bill_legal_name', 'billing_state_code', 'bill_to_pincode', 'warehouse_business_entity', 'warehouse_city', 'quote', 'basic_cp', 'total_cgst_amount', 'total_sgst_amount', 'total_igst_amount', 'total_amount1', 'po_bill_location_name', 'po_bill_address', 'po_bill_state', 'po_bill_pin_code', 'po_bill_warehouse_name', 'po_bill_city', 'po_bill_state_code', 'po_bill_gstin_no'], 'default', 'value' => null],
            [['deleted'], 'default', 'value' => 0],
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime', 'purchase_order_no'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime'], 'safe'],
            [['total_amount', 'basic_cp', 'total_cgst_amount', 'total_sgst_amount', 'total_igst_amount', 'total_amount1'], 'number'],
            [['terms_conditions', 'comment', 'po_bill_address'], 'string'],
            [['purchase_order_no', 'subject', 'stage', 'account_name', 'opportunity_name', 'contact_name', 'currency', 'submit_approval', 'purchase_order_number', 'vendor_name', 'po_expiry_date', 'exchange_rate', 'inspection_vendor', 'drilling_vendor', 'type', 'material_receiver_name', 'material_receiver_contact', 'material_receiver_email', 'nature_po', 'po_type', 'billing_city', 'billing_state', 'billing_code', 'bill_gstin_no', 'bill_pan_no', 'pincode', 'ship_warehouse_name', 'warehouse_state', 'warehouse_state_code', 'warehouse_pincode', 'warehouse_gstin_no', 'attach_file', 'payment_terms', 'bill_to_name', 'bill_legal_name', 'billing_state_code', 'bill_to_pincode', 'warehouse_business_entity', 'warehouse_city', 'po_bill_location_name', 'po_bill_warehouse_name'], 'string', 'max' => 200],
            [['bill_address', 'warehouse_address'], 'string', 'max' => 3000],
            [['quote', 'po_bill_state', 'po_bill_gstin_no'], 'string', 'max' => 100],
            [['po_bill_pin_code', 'po_bill_city', 'po_bill_state_code'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'purchase_order_id' => 'Purchase Order ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'purchase_order_no' => 'Purchase Order No',
            'subject' => 'Subject',
            'stage' => 'Stage',
            'account_name' => 'Account Name',
            'opportunity_name' => 'Opportunity Name',
            'contact_name' => 'Contact Name',
            'currency' => 'Currency',
            'submit_approval' => 'Submit Approval',
            'purchase_order_number' => 'Purchase Order Number',
            'vendor_name' => 'Vendor Name',
            'po_expiry_date' => 'Po Expiry Date',
            'exchange_rate' => 'Exchange Rate',
            'inspection_vendor' => 'Inspection Vendor',
            'drilling_vendor' => 'Drilling Vendor',
            'type' => 'Type',
            'material_receiver_name' => 'Material Receiver Name',
            'material_receiver_contact' => 'Material Receiver Contact',
            'material_receiver_email' => 'Material Receiver Email',
            'nature_po' => 'Nature Po',
            'po_type' => 'Po Type',
            'bill_address' => 'Bill Address',
            'billing_city' => 'Billing City',
            'billing_state' => 'Billing State',
            'billing_code' => 'Billing Code',
            'bill_gstin_no' => 'Bill Gstin No',
            'bill_pan_no' => 'Bill Pan No',
            'pincode' => 'Pincode',
            'ship_warehouse_name' => 'Ship Warehouse Name',
            'warehouse_address' => 'Warehouse Address',
            'warehouse_state' => 'Warehouse State',
            'warehouse_state_code' => 'Warehouse State Code',
            'warehouse_pincode' => 'Warehouse Pincode',
            'warehouse_gstin_no' => 'Warehouse Gstin No',
            'attach_file' => 'Attach File',
            'total_amount' => 'Total Amount',
            'terms_conditions' => 'Terms Conditions',
            'payment_terms' => 'Payment Terms',
            'deleted' => 'Deleted',
            'comment' => 'Comment',
            'bill_to_name' => 'Bill To Name',
            'bill_legal_name' => 'Bill Legal Name',
            'billing_state_code' => 'Billing State Code',
            'bill_to_pincode' => 'Bill To Pincode',
            'warehouse_business_entity' => 'Warehouse Business Entity',
            'warehouse_city' => 'Warehouse City',
            'quote' => 'Quote',
            'basic_cp' => 'Basic Cp',
            'total_cgst_amount' => 'Total Cgst Amount',
            'total_sgst_amount' => 'Total Sgst Amount',
            'total_igst_amount' => 'Total Igst Amount',
            'total_amount1' => 'Total Amount1',
            'po_bill_location_name' => 'Po Bill Location Name',
            'po_bill_address' => 'Po Bill Address',
            'po_bill_state' => 'Po Bill State',
            'po_bill_pin_code' => 'Po Bill Pin Code',
            'po_bill_warehouse_name' => 'Po Bill Warehouse Name',
            'po_bill_city' => 'Po Bill City',
            'po_bill_state_code' => 'Po Bill State Code',
            'po_bill_gstin_no' => 'Po Bill Gstin No',
        ];
    }

    /**
     * Gets query for [[PurchaseOrderItemsdetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPurchaseOrderItemsdetails()
    {
        return $this->hasMany(PurchaseOrderItemsdetail::class, ['purchase_order_id' => 'purchase_order_id']);
    }

    public function validateDataIntegrityForPoApproval($stage)
    {
        
            //there has to be atleast one purchase item
            $purchase_order_itemsdetail = $_POST['purchase_order_itemsdetail']??[];
            if(count($purchase_order_itemsdetail) == 0){
                throw new Exception("Atleast one Purchase Item is required");
            }
            $basic_cp = $_POST["purchase_order"]["basic_cp"];
            $total_amount = $_POST["purchase_order"]["total_amount"];
            if(empty($total_amount)){
                throw new Exception("Total Amount of a Purchase Order cannot be zero");
            }
            $total_amount_of_items = 0;
            foreach ($purchase_order_itemsdetail as $item) {
                if(empty($item['quantity'])){
                    throw new Exception("Quantity of a Purchase Item can not be empty");
                }
                if(empty($item['cost_price'])){
                    throw new Exception("Cost price of a Purchase Item can not be empty");
                }
                if(empty($item['total'])){
                    throw new Exception("Total Amount of a Purchase Item can not be empty");
                }
                $total_amount_of_items += $item['total'];
            }
            // echo $total_amount_of_items." ".$basic_cp;die;
            if((int)$total_amount_of_items != (int)$basic_cp){
                throw new Exception("Total Amount of Purchase Order $total_amount_of_items do not match with the Basic CP $basic_cp of all Items. Please check the submitted data");
            }

        
        return true;
    }

    function saveToVpReports($RecordId)
    {
        $Record = (int)$RecordId;
        $this->saveVpPurchaseOder($RecordId);
    }
    
    function saveVpPurchaseOder($RecordId)
    {
        $sql = "SELECT purchase_order_no FROM purchase_order where purchase_order_id=:RecordId";
        $result = Yii::$app->db->createCommand($sql)->bindValue(":RecordId",$RecordId)->queryOne();
        $purchase_order_no= $result['purchase_order_no'];
        //delete old record
        $sql_del = "Delete from `rep_vp_purchase_order` where req_reference_no=:req_reference_no";
        Yii::$app->db->createCommand($sql_del)
        ->bindValue(":req_reference_no",$purchase_order_no)
        ->execute();

        $sql = "SELECT sourcingdeal.sourcingdeal_no,sourcingdeal.sourcingdeal_id, sourcingdeal.vendor_account_name as account_id,
            purchase_order.purchase_order_no,purchase_order.purchase_order_id,
            purchase_order.po_expiry_date,purchase_order.total_amount,
            purchase_order.bill_to_name,purchase_order.bill_legal_name,purchase_order.bill_address,purchase_order.billing_city,
            purchase_order.billing_state,purchase_order.bill_to_pincode,
            purchase_order.warehouse_business_entity,purchase_order.ship_warehouse_name,purchase_order.warehouse_address,
            purchase_order.warehouse_city,purchase_order.warehouse_state,purchase_order.warehouse_pincode, 
            purchase_order.stage as status,purchase_order_stage.stage_name as status_name 
            from purchase_order 
            left join purchase_order_stage on purchase_order_stage.po_stage_id = purchase_order.stage
            left join sourcingdeal on sourcingdeal.sourcingdeal_id = purchase_order.opportunity_name 
            where purchase_order.purchase_order_id = :RecordId";
        $result = Yii::$app->db->createCommand($sql)->bindValue(":RecordId",$RecordId)->queryAll();
        //echo "at b ";exit;
        foreach($result as $value)
        {
            $account_id = $value['account_id']?$value['account_id']:null;
            $sourcingdeal_no = $value['sourcingdeal_no']?$value['sourcingdeal_no']:null;
            $sourcingdeal_id = $value['sourcingdeal_id']?$value['sourcingdeal_id']:null;
            $purchase_order_no = $value['purchase_order_no'] ? $value['purchase_order_no']:null;
            $purchase_order_id = $value['purchase_order_id']?$value['purchase_order_id']:null;

            $po_expiry_date = $value['po_expiry_date']?$value['po_expiry_date'] : null;
            $total_amount = $value['total_amount']?$value['total_amount'] : null;
            $bill_to_name = $value['bill_to_name']?$value['bill_to_name'] : null;
            $status = $value['status'] ? $value['status'] : null;
            $status_name = $value['status_name'] ? $value['status_name'] : null;
            $bill_to_legal_name = $value['bill_legal_name']?$value['bill_legal_name'] : null;
            $bill_to_address = $value['bill_address']?$value['bill_address'] : null;
            $bill_to_city = $value['billing_city']?$value['billing_city'] : null;
            $bill_to_state = $value['billing_state']?$value['billing_state'] : null;
            $bill_to_pin = $value['bill_to_pincode']?$value['bill_to_pincode'] : null;
            $warehouse = $value['warehouse_business_entity']?$value['warehouse_business_entity'] : null;
            $warehouse_name = $value['ship_warehouse_name']?$value['ship_warehouse_name'] : null;
            $warehouse_address = $value['warehouse_address']?$value['warehouse_address'] : null;
            $warehouse_city = $value['warehouse_city']?$value['warehouse_city'] : null;
            $warehouse_state = $value['warehouse_state']?$value['warehouse_state'] : null;
            $warehouse_pin = $value['warehouse_pincode']?$value['warehouse_pincode'] : null;
            
            $sql_ins = "INSERT INTO `rep_vp_purchase_order` 
                SET account_id = :account_id,
                    req_reference_no = :req_reference_no,
                    sourcingdeal_no = :sourcingdeal_no,
                    sourcingdeal_id = :sourcingdeal_id,
                    purchase_order_id = :purchase_order_id,
                    total_amount = :total_amount,
                    po_expiry_date = :po_expiry_date,
                    bill_to_name = :bill_to_name,
                    bill_to_legal_name = :bill_to_legal_name,
                    bill_to_address = :bill_to_address,
                    bill_to_city = :bill_to_city,
                    bill_to_state = :bill_to_state,
                    bill_to_pin = :bill_to_pin,
                    warehouse = :warehouse,
                    warehouse_name = :warehouse_name,
                    warehouse_address = :warehouse_address,
                    warehouse_city = :warehouse_city,
                    warehouse_state = :warehouse_state,
                    warehouse_pin = :warehouse_pin,
                    status = :status,
                    status_name = :status_name,
                    created_on = NOW()";

            Yii::$app->db->createCommand($sql_ins)
                ->bindValue(":account_id", $account_id)
                ->bindValue(":req_reference_no", $purchase_order_no)
                ->bindValue(":sourcingdeal_no", $sourcingdeal_no)
                ->bindValue(":sourcingdeal_id", $sourcingdeal_id)
                ->bindValue(":purchase_order_id", $purchase_order_id)
                ->bindValue(":total_amount", $total_amount)
                ->bindValue(":po_expiry_date", $po_expiry_date)
                ->bindValue(":bill_to_name", $bill_to_name)
                ->bindValue(":bill_to_legal_name", $bill_to_legal_name)
                ->bindValue(":bill_to_address", $bill_to_address)
                ->bindValue(":bill_to_city", $bill_to_city)
                ->bindValue(":bill_to_state", $bill_to_state)
                ->bindValue(":bill_to_pin", $bill_to_pin)
                ->bindValue(":warehouse", $warehouse)
                ->bindValue(":warehouse_name", $warehouse_name)
                ->bindValue(":warehouse_address", $warehouse_address)
                ->bindValue(":warehouse_city", $warehouse_city)
                ->bindValue(":warehouse_state", $warehouse_state)
                ->bindValue(":warehouse_pin", $warehouse_pin)
                ->bindValue(":status", $status)
                ->bindValue(":status_name", $status_name)
                ->execute();   
        }
    }
}
