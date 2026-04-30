<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "vehicle_loading".
 *
 * @property int $vehicleloading_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property int $deleted
 * @property string $vehicleloading_no
 * @property string|null $so_number
 * @property string|null $po_number
 * @property string|null $po_date
 * @property string|null $front_vehicle_photo
 * @property string|null $empty_vehicle_photo
 * @property string|null $gate_pass_number
 * @property string|null $gate_pass_photo
 * @property float|null $empty_vehicle_weight
 * @property string|null $empty_vehicle_weight_slip
 * @property float|null $loaded_vehicle_weight
 * @property string|null $loaded_vehicle_weight_slip
 * @property string|null $invoice_number
 * @property string|null $invoice_date
 * @property float|null $invoice_amount
 * @property string|null $invoice_image
 * @property int|null $vehicle_expence_owned_by
 * @property string|null $vehicle_number
 * @property int|null $vendor_name
 * @property string|null $vendor_vehicle_number
 * @property float|null $amount
 * @property string|null $payment_terms
 *
 * @property VehicleLoadingProductItems[] $vehicleLoadingProductItems
 */
class VehicleLoading extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vehicle_loading';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime', 'vehicleloading_no'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'deleted', 'vehicle_expence_owned_by', 'vendor_name','account_name','vehicle_loading_done','status'], 'integer'],
            [['createdtime', 'modifiedtime', 'po_date', 'invoice_date'], 'safe'],
            [['empty_vehicle_weight', 'loaded_vehicle_weight', 'invoice_amount', 'amount'], 'number'],
            [['vehicleloading_no'], 'string', 'max' => 100],
            [['so_number', 'po_number', 'front_vehicle_photo', 'empty_vehicle_photo', 'gate_pass_number', 'gate_pass_photo', 'empty_vehicle_weight_slip', 'loaded_vehicle_weight_slip', 'invoice_number', 'invoice_image', 'vehicle_number', 'vendor_vehicle_number', 'payment_terms'], 'string', 'max' => 200],
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
            'vehicleloading_id' => 'Vehicleloading ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'deleted' => 'Deleted',
            'vehicleloading_no' => 'Vehicleloading No',
            'so_number' => 'So Number',
            'po_number' => 'Po Number',
            'po_date' => 'Po Date',
            'front_vehicle_photo' => 'Front Vehicle Photo',
            'empty_vehicle_photo' => 'Empty Vehicle Photo',
            'gate_pass_number' => 'Gate Pass Number',
            'gate_pass_photo' => 'Gate Pass Photo',
            'empty_vehicle_weight' => 'Empty Vehicle Weight',
            'empty_vehicle_weight_slip' => 'Empty Vehicle Weight Slip',
            'loaded_vehicle_weight' => 'Loaded Vehicle Weight',
            'loaded_vehicle_weight_slip' => 'Loaded Vehicle Weight Slip',
            'invoice_number' => 'Invoice Number',
            'invoice_date' => 'Invoice Date',
            'invoice_amount' => 'Invoice Amount',
            'invoice_image' => 'Invoice Image',
            'vehicle_expence_owned_by' => 'Vehicle Expence Owned By',
            'vehicle_number' => 'Vehicle Number',
            'vendor_name' => 'Vendor Name',
            'vendor_vehicle_number' => 'Vendor Vehicle Number',
            'amount' => 'Amount',
            'payment_terms' => 'Payment Terms',
        ];
    }

    /**
     * Gets query for [[VehicleLoadingProductItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVehicleLoadingProductItems()
    {
        return $this->hasMany(VehicleLoadingProductItems::class, ['vehicleloading_id' => 'vehicleloading_id']);
    }
}
