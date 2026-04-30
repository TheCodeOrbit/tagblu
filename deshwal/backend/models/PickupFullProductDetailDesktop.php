<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pickup_full_product_detail_desktop".
 *
 * @property int $pickup_product_detail_id
 * @property int $pickup_id
 * @property int|null $prod_category
 * @property string|null $serial_number
 * @property int|null $make
 * @property int|null $model
 * @property int|null $generation
 * @property int|null $ram
 * @property int|null $storage_capacity
 * @property int|null $storage_type
 * @property int|null $physical_dent
 * @property int|null $quantity
 * @property string|null $image_open
 * @property string|null $image_front
 * @property string|null $image_back
 * @property string|null $image_motherboard
 * @property int|null $critical
 * @property string|null $remarks
 * @property int|null $pickup_done
 * @property int|null $condition
 * @property string|null $pickup_remarks
 *
 * @property Pickup $pickup
 */
class PickupFullProductDetailDesktop extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pickup_full_product_detail_desktop';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pickup_id'], 'required'],
            [['pickup_id'], 'integer'],
            [['prod_category', 'make', 'model', 'generation', 'ram', 'storage_capacity', 'storage_type', 'physical_dent', 'quantity', 'critical', 'pickup_done', 'condition'], 'safe'],
            [['serial_number', 'image_open', 'image_front', 'image_back', 'image_motherboard', 'remarks', 'pickup_remarks'], 'safe'],
            [['pickup_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pickup::class, 'targetAttribute' => ['pickup_id' => 'pickup_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pickup_product_detail_id' => 'Pickup Product Detail ID',
            'pickup_id' => 'Pickup ID',
            'prod_category' => 'Prod Category',
            'serial_number' => 'Serial Number',
            'make' => 'Make',
            'model' => 'Model',
            'generation' => 'Generation',
            'ram' => 'Ram',
            'storage_capacity' => 'Storage Capacity',
            'storage_type' => 'Storage Type',
            'physical_dent' => 'Physical Dent',
            'quantity' => 'Quantity',
            'image_open' => 'Image Open',
            'image_front' => 'Image Front',
            'image_back' => 'Image Back',
            'image_motherboard' => 'Image Motherboard',
            'critical' => 'Critical',
            'remarks' => 'Remarks',
            'pickup_done' => 'Pickup Done',
            'condition' => 'Condition',
            'pickup_remarks' => 'Pickup Remarks',
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

    public function saveFromInspectionData($pickup_id, $inspectionDetails)
    {
        foreach ($inspectionDetails as $detail) {
            $pickupDetail = new PickupFullProductDetailDesktop();
            $pickupDetail->pickup_id = $pickup_id;

            // Map attributes from InspectionFullProductDetailDesktop to PickupFullProductDetailDesktop
            $pickupDetail->prod_category = $detail->prod_category;
            $pickupDetail->serial_number = $detail->serial_number;
            $pickupDetail->make = $detail->make;
            
            $pickupDetail->model = $detail->model;
            $pickupDetail->generation = $detail->generation;
            $pickupDetail->ram = $detail->ram;
            $pickupDetail->storage_capacity = $detail->storage_capacity;
            $pickupDetail->storage_type = $detail->storage_type;
            $pickupDetail->physical_dent = $detail->physical_dent;
            $pickupDetail->quantity = $detail->quantity;
            $pickupDetail->image_open = $detail->image_open;
            $pickupDetail->image_front = $detail->image_front;
            $pickupDetail->image_back = $detail->image_back;
            $pickupDetail->image_motherboard = $detail->image_motherboard;
            $pickupDetail->critical = $detail->critical;
            $pickupDetail->remarks = $detail->remarks;   
            if (!$pickupDetail->save(false)) {
                \Yii::error("Failed to save pickup detail desktop: " . json_encode($pickupDetail->errors));
            }
        }
        return true;
    }

    public function savePickupDesktopProduct($entityId)
    {
        $savePostData = $_POST['pickup_full_product_detail_desktop']??[];
        if (!empty($savePostData)) {
            if (count($savePostData) > 0) {
                foreach ($savePostData as $product_detail) {
                    if(is_array($product_detail))
                    {
                        $product_detail['pickup_id'] = intval($entityId);
                        $product_detail_obj = new PickupFullProductDetailDesktop();
                        $product_detail_obj->attributes = $product_detail;
                        $product_detail_obj->validate();
                        $product_detail_obj->save(false);
                    }
                }
            }
        }
    }

}
