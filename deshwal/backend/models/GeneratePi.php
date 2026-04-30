<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "generate_pi".
 *
 * @property int $generatepi_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property int $deleted
 * @property string $generatepi_no
 * @property int|null $vendor_name
 * @property string|null $payment_terms
 * @property string|null $so_number
 * @property string|null $pi_date
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
 * @property int|null $ship_wh_location
 * @property string|null $ship_wh_address
 * @property string|null $ship_wh_city
 * @property string|null $ship_wh_state
 * @property string|null $ship_wh_pincode
 * @property string|null $ship_wh_statecode
 * @property string|null $ship_wh_gst_number
 * @property string|null $ship_wh_pan_number
 *
 * @property GeneratepiItemsDetail[] $generatepiItemsDetails
 */
class GeneratePi extends \yii\db\ActiveRecord
{

    public $tab = 'generatepi';
    /**
     * {@inheritdoc}
     */ 
    public static function tableName()
    {
        return 'generate_pi';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['po_number', 'match', 'pattern' => '/^[a-zA-Z0-9]+$/', 'message' => 'PO Number must be alphanumeric only.'],
            // 'vendor_name', removed from here to prevent blank account by ptpatel on date 24-01-2026
            [[ 'payment_terms', 'so_number', 'pi_date', 'bill_vendor_location', 'bill_address', 'bill_city', 'bill_state', 'bill_pincode', 'bill_statecode', 'bill_gst_number', 'bill_pan_number', 'ship_vendor_location', 'ship_address', 'ship_city', 'ship_state', 'ship_pincode', 'ship_statecode', 'ship_gst_number', 'ship_pan_number', 'bill_wh_location', 'bill_wh_address', 'bill_wh_city', 'bill_wh_state', 'bill_wh_pincode', 'bill_wh_statecode', 'bill_wh_gst_number', 'bill_wh_pan_number', 'ship_wh_location', 'ship_wh_address', 'ship_wh_city', 'ship_wh_state', 'ship_wh_pincode', 'ship_wh_statecode', 'ship_wh_gst_number', 'ship_wh_pan_number','po_number','po_amount','po_date'], 'default', 'value' => null],
            [['deleted'], 'default', 'value' => 0],
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime', 'generatepi_no'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'deleted', 'vendor_name', 'bill_vendor_location', 'ship_vendor_location', 'bill_wh_location', 'ship_wh_location'], 'integer'],
            [['createdtime', 'modifiedtime', 'pi_date','po_date'], 'safe'],
            [['bill_address', 'ship_address', 'bill_wh_address', 'ship_wh_address'], 'string'],
            [['generatepi_no', 'payment_terms'], 'string', 'max' => 200],
            [['so_number', 'bill_city', 'bill_state', 'bill_gst_number', 'ship_city', 'ship_state', 'ship_gst_number', 'bill_wh_city', 'bill_wh_state', 'bill_wh_gst_number', 'ship_wh_city', 'ship_wh_state', 'ship_wh_gst_number'], 'string', 'max' => 100],
            [['bill_pincode', 'bill_pan_number', 'ship_pincode', 'ship_pan_number', 'bill_wh_pincode', 'bill_wh_pan_number', 'ship_wh_pincode', 'ship_wh_pan_number'], 'string', 'max' => 10],
            [['bill_statecode', 'ship_statecode', 'bill_wh_statecode', 'ship_wh_statecode'], 'string', 'max' => 50],
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
            'generatepi_id' => 'Generatepi ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'deleted' => 'Deleted',
            'generatepi_no' => 'Generatepi No',
            'vendor_name' => 'Vendor Name',
            'payment_terms' => 'Payment Terms',
            'so_number' => 'So Number',
            'pi_date' => 'Pi Date',
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
            'ship_wh_location' => 'Ship Wh Location',
            'ship_wh_address' => 'Ship Wh Address',
            'ship_wh_city' => 'Ship Wh City',
            'ship_wh_state' => 'Ship Wh State',
            'ship_wh_pincode' => 'Ship Wh Pincode',
            'ship_wh_statecode' => 'Ship Wh Statecode',
            'ship_wh_gst_number' => 'Ship Wh Gst Number',
            'ship_wh_pan_number' => 'Ship Wh Pan Number',
        ];
    }

    /**
     * Gets query for [[GeneratepiItemsDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGeneratepiItemsDetails()
    {
        return $this->hasMany(GeneratepiItemsDetail::class, ['generatepi_id' => 'generatepi_id']);
    }

    public function createPIFromSalesOrder($salesOrderId)
    {
        try {
            $salesOrder = Yii::$app->db->createCommand("SELECT * FROM sales_order WHERE salesorder_id = :id")
                ->bindValue(":id", $salesOrderId)
                ->queryOne();
            if (!$salesOrder) {
                return false;
            }

            $piModel = new GeneratePi();
            if ($autoField = $piModel->checkAutoNo()) {
                $piModel->{$autoField} = $piModel->getAutoNo($this->tab); 
            }
            $piModel->ownerid = $salesOrder['creatorid'] ?? null;
            $piModel->creatorid = $salesOrder['ownerid'] ?? null;
            $piModel->modifiedby = $salesOrder['modifiedby'] ?? null;
            $piModel->createdtime = $salesOrder['createdtime'] ?? date('Y-m-d H:i:s');
            $piModel->modifiedtime = $salesOrder['modifiedtime'] ?? date('Y-m-d H:i:s');
            $piModel->deleted = $salesOrder['deleted'] ?? 0;
            $piModel->so_number = $salesOrder['salesorder_id'] ?? null;
            $piModel->vendor_name = $salesOrder['vendor_name'] ?? null;
            $piModel->payment_terms = $salesOrder['payment_terms'] ?? null;
            $piModel->pi_date = date('Y-m-d');
            $piModel->bill_vendor_location = $salesOrder['bill_vendor_location'] ?? null;
            $piModel->bill_address = $salesOrder['bill_address'] ?? null;
            $piModel->bill_city = $salesOrder['bill_city'] ?? null;
            $piModel->bill_state = $salesOrder['bill_state'] ?? null;
            $piModel->bill_pincode = $salesOrder['bill_pincode'] ?? null;
            $piModel->bill_statecode = $salesOrder['bill_statecode'] ?? null;
            $piModel->bill_gst_number = $salesOrder['bill_gst_number'] ?? null;
            $piModel->bill_pan_number = $salesOrder['bill_pan_number'] ?? null;
            $piModel->ship_vendor_location = $salesOrder['ship_vendor_location'] ?? null;
            $piModel->ship_address = $salesOrder['ship_address'] ?? null;
            $piModel->ship_city = $salesOrder['ship_city'] ?? null;
            $piModel->ship_state = $salesOrder['ship_state'] ?? null;
            $piModel->ship_pincode = $salesOrder['ship_pincode'] ?? null;
            $piModel->ship_statecode = $salesOrder['ship_statecode'] ?? null;
            $piModel->ship_gst_number = $salesOrder['ship_gst_number'] ?? null;
            $piModel->ship_pan_number = $salesOrder['ship_pan_number'] ?? null;
            $piModel->bill_wh_location = $salesOrder['bill_wh_location'] ?? null;
            $piModel->bill_wh_address = $salesOrder['bill_wh_address'] ?? null;
            $piModel->bill_wh_city = $salesOrder['bill_wh_city'] ?? null;
            $piModel->bill_wh_state = $salesOrder['bill_wh_state'] ?? null;
            $piModel->bill_wh_pincode = $salesOrder['bill_wh_pincode'] ?? null;
            $piModel->bill_wh_statecode = $salesOrder['bill_wh_statecode'] ?? null;
            $piModel->bill_wh_gst_number = $salesOrder['bill_wh_gst_number'] ?? null;
            $piModel->bill_wh_pan_number = $salesOrder['bill_wh_pan_number'] ?? null;
            $piModel->ship_wh_location = $salesOrder['ship_wh_location'] ?? null;
            $piModel->ship_wh_address = $salesOrder['ship_wh_address'] ?? null;
            $piModel->ship_wh_city = $salesOrder['ship_wh_city'] ?? null;
            $piModel->ship_wh_state = $salesOrder['ship_wh_state'] ?? null;
            $piModel->ship_wh_pincode = $salesOrder['ship_wh_pincode'] ?? null;
            $piModel->ship_wh_statecode = $salesOrder['ship_wh_statecode'] ?? null;
            $piModel->ship_wh_gst_number = $salesOrder['ship_wh_gst_number'] ?? null;
            $piModel->ship_wh_pan_number = $salesOrder['ship_wh_pan_number'] ?? null;

            if (!$piModel->save(false)) {
                return false;
            }
            if ($autoField = $piModel->checkAutoNo()){
                $piModel->setAutoNo($this->tab);
            }
            $generatepiid = $piModel->generatepi_id;
            $modlog = new ModtrackerBasic();
            $modlog->auditlog([], $piModel->attributes, 'generatepi', $generatepiid, 1, Yii::$app->user->id);
            $salesOrderItems = SalesorderItemsDetail::find()->where(['salesorder_id' => $salesOrderId])->all();

            foreach ($salesOrderItems as $orderItem) {
                $piItem = new GeneratepiItemsDetail();
                $piItem->generatepi_id = $generatepiid;
                $piItem->product_name = $orderItem->product_name;
                $piItem->hsn_code = $orderItem->hsn_code;
                $piItem->qty = $orderItem->qty ?? 0;
                $piItem->base_price_gst_exclude = $orderItem->base_price_gst_exclude ?? 0;
                $piItem->cgst_percentage = $orderItem->cgst_percentage ?? 0;
                $piItem->cgst_percentage = $orderItem->sgst_percentage ?? 0;
                $piItem->igst_percentage = $orderItem->igst_percentage ?? 0;
                $piItem->cgst_amount = $orderItem->cgst_amount ?? 0;
                $piItem->sgst_amount = $orderItem->sgst_amount ?? 0;
                $piItem->igst_amount = $orderItem->igst_amount ?? 0;
                $piItem->total_amount = $orderItem->total_amount ?? 0;

                $piItem->save(false);
            }

            return true;
        }catch (\Exception $th) {
            Yii::error("Failed to save GeneratePi: " . $th->getMessage());
            // If the error is from validation, get model errors
            if (isset($piModel) && $piModel->hasErrors()) {
                $errors = $piModel->getErrors();
                $errorMessages = [];
                foreach ($errors as $field => $msgs) {
                    $errorMessages[] = implode(', ', $msgs);
                }
                throw new \Exception('Validation failed: ' . implode(' | ', $errorMessages));
            }
            throw $th; // Or return false, or handle as you want
        }

    }

    public function checkAutoNo()
    {

        $table_name = $this->tableName();
        $autoField = Yii::$app->db->createCommand("SELECT columnname
            FROM field 
            WHERE tablename = :tablename AND uitype = :uitype")
            ->bindValue(':tablename', $table_name)
            ->bindValue(':uitype', 11)
            ->queryOne();
        if (empty($autoField))
            return false; // if does not exist;
        if (count($autoField) < 1)
            return false;
        else
            return $autoField['columnname'];
    }

    public function setAutoNo($tabs)
    {
        $table_name = $this->tableName();
        $model = new AutoNo();
        $upAutoNo = $model->setAutomoduleno($tabs, $table_name);
        return $upAutoNo;
    }
    public function getAutoNo($tabs)
    {
        $table_name = $this->tableName();
        $model = new AutoNo();
        $orderno = $model->getautomoduleno($tabs, $table_name);
        return $orderno;
    }
}
