<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pickup_vehicle_details".
 *
 * @property int $pickup_vehicle_detail_id
 * @property string|null $date
 * @property int $pickup_id
 * @property string|null $vendor_name
 * @property string|null $docket_number
 * @property string|null $vehicle_no
 * @property int|null $vehicle_size
 * @property int|null $mode
 * @property float|null $empty_weight
 * @property float|null $loaded_weight
 * @property string|null $lock_by
 * @property string|null $seal_number
 * @property string|null $tentative_delivery_date
 * @property string|null $delivered_date
 * @property int|null $status
 * @property int|null $ageing
 * @property int|null $age
 * @property int|null $shipping_ageing
 * @property int|null $shipping_age
 * @property int|null $attach
 * @property int $deleted
 *
 * @property Pickup $pickup
 */
class PickupVehicleDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pickup_vehicle_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date', 'tentative_delivery_date', 'delivered_date'], 'safe'],
            [['pickup_id'], 'required'],
            [['pickup_id', 'vehicle_size', 'mode', 'status', 'ageing', 'age', 'shipping_ageing', 'shipping_age', 'attach', 'deleted'], 'integer'],
            [['empty_weight', 'loaded_weight'], 'number'],
            [['vendor_name', 'lock_by'], 'string', 'max' => 200],
            [['docket_number', 'seal_number'], 'string', 'max' => 100],
            [['vehicle_no'], 'string', 'max' => 10],
            [['pickup_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pickup::class, 'targetAttribute' => ['pickup_id' => 'pickup_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pickup_vehicle_detail_id' => 'Pickup Vehicle Detail ID',
            'date' => 'Date',
            'pickup_id' => 'Pickup ID',
            'vendor_name' => 'Vendor Name',
            'docket_number' => 'Docket Number',
            'vehicle_no' => 'Vehicle No',
            'vehicle_size' => 'Vehicle Size',
            'mode' => 'Mode',
            'empty_weight' => 'Empty Weight',
            'loaded_weight' => 'Loaded Weight',
            'lock_by' => 'Lock By',
            'seal_number' => 'Seal Number',
            'tentative_delivery_date' => 'Tentative Delivery Date',
            'delivered_date' => 'Delivered Date',
            'status' => 'Status',
            'ageing' => 'Ageing',
            'age' => 'Age',
            'shipping_ageing' => 'Shipping Ageing',
            'shipping_age' => 'Shipping Age',
            'attach' => 'Attach',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * Gets query for [[Pickup]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPickup()
    {
        return $this->hasOne(Pickup::class, ['pickup_id' => 'pickup_id']);
    }
    public function savePickupVehicleDetails($entityId)
    {

        $savePickupVehicleDetails = $_POST['pickup_vehicle_details']??[];
        // echo "<br>pickup vehicle<pre>";
        // print_r($_POST['pickup_vehicle_details']);die;
        if (!empty($savePickupVehicleDetails)) {
            if (count($savePickupVehicleDetails) > 0) {
                foreach ($savePickupVehicleDetails as $product_detail) {
                    // echo $entityId;die;
                    if(is_array($product_detail))
                    {
                    $product_detail['pickup_id'] = intval($entityId);
                    $product_detail_obj = new PickupVehicleDetails();
                    $product_detail_obj->attributes = $product_detail;
                    // print_r($product_detail_obj->attributes);die;
                    $product_detail_obj->validate();
                    $product_detail_obj->save(false);
                    // $modlog = new ModtrackerBasic();
                    // $modlog->auditlog($oldAttributes = '', $product_detail_obj, 'productdetail', $product_detail_obj->$product_costing_detail_id, 0, Yii::$app->user->id);
                    }
                }
            }
        }
    }
}
