<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sales_order".
 *
 * @property int $salesorder_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property int $deleted
 * @property string $salesorder_no
 * @property int|null $vendor_name
 * @property string|null $payment_terms
 * @property int|null $bill_vendor_location
 * @property string|null $bill_address
 * @property string|null $bill_city
 * @property string|null $bill_state
 * @property string|null $bill_pincode
 * @property string|null $bill_statecode
 * @property string|null $bill_gst_number
 * @property string|null $bill_pan_number
 * @property int|null $ship_vendor_location
 * @property string|null $ship_address
 * @property string|null $ship_city
 * @property string|null $ship_state
 * @property string|null $ship_pincode
 * @property string|null $ship_statecode
 * @property string|null $ship_gst_number
 * @property string|null $ship_pan_number
 * @property int|null $bill_wh_location
 * @property string|null $bill_wh_address
 * @property string|null $bill_wh_city
 * @property string|null $bill_wh_state
 * @property string|null $bill_wh_pincode
 * @property string|null $bill_wh_statecode
 * @property string|null $bill_wh_gst_number
 * @property string|null $bill_wh_pan_number
 */
class SalesOrder extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sales_order';
    }

    public static function primaryKey()
    {
        return ['salesorder_id']; 
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['delivery_date', 'proof_of_delivery','payment_receive_amount','payment_date','balance_amount'], 'default', 'value' => null],
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime', 'salesorder_no','bill_vendor_location','ship_vendor_location','bill_wh_location','ship_wh_location'], 'required'],
            [['ownerid', 'creatorid','stage','send_for_approval', 'modifiedby', 'deleted', 'vendor_name','bill_vendor_location','ship_vendor_location','bill_wh_location','ship_wh_location'], 'integer'],
            [['createdtime', 'modifiedtime'], 'safe'],
            [['bill_address', 'ship_address', 'bill_wh_address', 'ship_wh_address'], 'string'],
            [['bill_address', 'ship_address','salesorder_no', 'payment_terms','proof_of_delivery',], 'string', 'max' => 200],
            [['bill_city', 'bill_state', 'bill_wh_city', 'bill_wh_state', 'ship_city', 'ship_state', 'ship_wh_city', 'ship_wh_state'], 'string', 'max' => 100],
            [['bill_statecode', 'ship_statecode', 'bill_wh_statecode', 'ship_wh_statecode'], 'string', 'max' => 50],
            [['bill_pincode', 'ship_pincode', 'bill_wh_pincode', 'ship_wh_pincode'], 'string', 'max' => 10],
            [['payment_receive_amount', 'balance_amount'], 'number'],
            [['bill_gst_number', 'ship_gst_number', 'bill_wh_gst_number', 'ship_wh_gst_number'], 'string', 'max' => 100],
            [['bill_pan_number', 'ship_pan_number', 'bill_wh_pan_number', 'ship_wh_pan_number'], 'string', 'max' => 10],
            [['deleted'], 'default', 'value' => 0],
             // added for handling blank values saving in by ptpatel on date 24-01-2026
            [['vendor_name'], 'trim'],
            [['vendor_name'], 'required', 'message' => 'Vendor Name cannot be blank.'],
            [['vendor_name'], 'integer', 'message' => 'Vandor Name must be a number.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'salesorder_id' => 'Salesorder ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'deleted' => 'Deleted',
            'salesorder_no' => 'Salesorder No',
            'vendor_name' => 'Vendor Name',
            'payment_terms' => 'Payment Terms',
            'bill_vendor_location' => 'Bill Vendor Location',
            'bill_address' => 'Bill Address',
            'bill_city' => 'Bill City',
            'bill_state' => 'Bill State',
            'bill_pincode' => 'Bill Pincode',
            'bill_statecode' => 'Bill Statecode',
            'bill_gst_number' => 'Bill Gst Number',
            'bill_pan_number' => 'Bill Pan Number',
            'ship_vendor_location' => 'Ship Vendor Location',
            'ship_address' => 'Ship Address',
            'ship_city' => 'Ship City',
            'ship_state' => 'Ship State',
            'ship_pincode' => 'Ship Pincode',
            'ship_statecode' => 'Ship Statecode',
            'ship_gst_number' => 'Ship Gst Number',
            'ship_pan_number' => 'Ship Pan Number',
            'bill_wh_location' => 'Bill Wh Location',
            'bill_wh_address' => 'Bill Wh Address',
            'bill_wh_city' => 'Bill Wh City',
            'bill_wh_state' => 'Bill Wh State',
            'bill_wh_pincode' => 'Bill Wh Pincode',
            'bill_wh_statecode' => 'Bill Wh Statecode',
            'bill_wh_gst_number' => 'Bill Wh Gst Number',
            'bill_wh_pan_number' => 'Bill Wh Pan Number',
        ];
    }

   public function beforeValidate()
    {
        if ($this->isNewRecord) {
            if (empty($this->salesorder_no)) 
                $this->salesorder_no = 'SO-' . time();   // fallback order number
            if (empty($this->createdtime)) 
                $this->createdtime = date('Y-m-d H:i:s');
            if (empty($this->modifiedtime)) 
                $this->modifiedtime = date('Y-m-d H:i:s');
            if (empty($this->ownerid)) 
                $this->ownerid = Yii::$app->user->id;
            if (empty($this->creatorid)) 
                $this->creatorid = Yii::$app->user->id ?? 1;
            if (empty($this->modifiedby)) 
                $this->modifiedby = Yii::$app->user->id ?? 1;
            if (!isset($this->deleted)) 
                $this->deleted = 0;
        }
        return parent::beforeValidate();
    }

    public function getBillVendor() {
    return $this->hasOne(VendorLocations::class, ['vendorloc_id' => 'bill_vendor_location']);
    }
    public function getShipVendor() {
        return $this->hasOne(VendorLocations::class, ['vendorloc_id' => 'ship_vendor_location']);
    }
    public function getBillWarehouse() {
        return $this->hasOne(Warehouse::class, ['warehouse_id' => 'bill_wh_location']);
    }
    public function getShipWarehouse() {
        return $this->hasOne(Warehouse::class, ['warehouse_id' => 'ship_wh_location']);
    }
    public function getVendorAccount() {
        return $this->hasOne(VendorAccount::class, ['vendoraccid' => 'vendor_name']);
    }


    //code added by ptpatel on date 05-11-2025
    function updateStatus($so_number, $so_status)
    {
        if (!empty($so_number) && !empty($so_status)) {
            $salesOrder = SalesOrder::find()
                ->where(['salesorder_id' => $so_number])
                ->one();

            if ($salesOrder) {
                $salesOrder->stage = $so_status;

                if ($salesOrder->validate()) {
                    $salesOrder->save(false);
                } else {
                    Yii::error('SalesOrder validation failed: ' . json_encode($salesOrder->getErrors()), __METHOD__);
                }

            } else {
                Yii::error("SalesOrder not found for SO number: $so_number", __METHOD__);
            }
        }
    }

    //end code added by ptpatel on date 05-11-2025

    


    function save_stock($RecordId)
    {
        
        $sqlSo = "SELECT stage FROM sales_order where salesorder_id = :salesorder_id";
        $resultSo = Yii::$app->db->createCommand($sqlSo)->bindValue(":salesorder_id", $RecordId)->queryOne();
        if(isset($resultSo['stage']) && $resultSo['stage'] != 7){
            return;
        }
        $sql = "SELECT * FROM salesorder_items_detail where salesorder_id = :salesorder_id GROUP BY product_name" ;
        $result = Yii::$app->db->createCommand($sql)->bindValue(":salesorder_id", $RecordId)->queryAll();
        $location_sql = "SELECT ship_wh_location FROM sales_order where salesorder_id = :salesorder_id";
        $location = Yii::$app->db->createCommand($location_sql)->bindValue(":salesorder_id", $RecordId)->queryOne();
        // echo "<pre>";print_r($location);die;
       
        foreach ($result as $result) {
            $StockCalculation = new StockCalculation();
            $StockCalculation->getTodayStockSingleProductdeshwal($result['product_name'], $location['ship_wh_location']);
        }
    }
}
