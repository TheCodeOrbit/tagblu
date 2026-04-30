<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pickup_full_product_detail_laptop".
 *
 * @property int $pickup_product_detail_id
 * @property int $pickup_id
 * @property int|null $prod_category
 * @property string|null $serial_number
 * @property int|null $make
 * @property int|null $model
 * @property int|null $generation
 * @property int|null $processor
 * @property int|null $screen_size
 * @property int|null $ram
 * @property int|null $storage_capacity
 * @property int|null $storage_type
 * @property int|null $screen_broken
 * @property int|null $physical_dent
 * @property int|null $battery_health
 * @property int|null $quantity
 * @property string|null $image_top
 * @property string|null $image_bottom
 * @property string|null $image_open
 * @property string|null $image_screen
 * @property string|null $image_bios
 * @property int|null $critical
 * @property string|null $remarks
 * @property int|null $pickup_done
 * @property int|null $condition
 * @property string|null $pickup_remarks
 *
 * @property Pickup $pickup
 */
class PickupFullProductDetailLaptop extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pickup_full_product_detail_laptop';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pickup_id'], 'required'],
            [['pickup_id'], 'integer'],
            [['prod_category', 'make', 'model', 'generation', 'processor', 'screen_size', 'ram', 'storage_capacity', 'storage_type', 'screen_broken', 'physical_dent', 'battery_health', 'quantity', 'critical', 'pickup_done', 'condition'], 'safe'],
            [['serial_number', 'image_top', 'image_bottom', 'image_open', 'image_screen', 'image_bios', 'remarks', 'pickup_remarks'], 'safe'],
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
            'processor' => 'Processor',
            'screen_size' => 'Screen Size',
            'ram' => 'Ram',
            'storage_capacity' => 'Storage Capacity',
            'storage_type' => 'Storage Type',
            'screen_broken' => 'Screen Broken',
            'physical_dent' => 'Physical Dent',
            'battery_health' => 'Battery Health',
            'quantity' => 'Quantity',
            'image_top' => 'Image Top',
            'image_bottom' => 'Image Bottom',
            'image_open' => 'Image Open',
            'image_screen' => 'Image Screen',
            'image_bios' => 'Image Bios',
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
            $pickupDetail = new PickupFullProductDetailLaptop();
            $pickupDetail->pickup_id = $pickup_id;

            // Map attributes from InspectionFullProductDetailLaptop to PickupFullProductDetailLaptop
            $pickupDetail->prod_category = $detail->prod_category;
            $pickupDetail->serial_number = $detail->serial_number;
            $pickupDetail->make = $detail->make;
            $pickupDetail->model = $detail->model;
            $pickupDetail->generation = $detail->generation;
            $pickupDetail->processor = $detail->processor;
            $pickupDetail->screen_size = $detail->screen_size;
            $pickupDetail->ram = $detail->ram;
            $pickupDetail->storage_capacity = $detail->storage_capacity;
            $pickupDetail->storage_type = $detail->storage_type;
            $pickupDetail->screen_broken = $detail->screen_broken;
            $pickupDetail->physical_dent = $detail->physical_dent;
            $pickupDetail->battery_health = $detail->battery_health;
            $pickupDetail->quantity = $detail->quantity;
            $pickupDetail->image_top = $detail->image_top;
            $pickupDetail->image_bottom = $detail->image_bottom;
            $pickupDetail->image_open = $detail->image_open;
            $pickupDetail->image_screen = $detail->image_screen;
            $pickupDetail->image_bios = $detail->image_bios;
            $pickupDetail->critical = $detail->critical;
            $pickupDetail->remarks = $detail->remarks;   
            if (!$pickupDetail->save(false)) {
                \Yii::error("Failed to save pickup detail for inspection laptop: " . json_encode($pickupDetail->errors));
            }
        }
        return true;
    }

    public function savePickupLaptopProduct($entityId)
    {
        $savePostData = $_POST['pickup_full_product_detail_laptop']??[];
        if (!empty($savePostData)) {
            if (count($savePostData) > 0) {
                foreach ($savePostData as $product_detail) {
                    if(is_array($product_detail))
                    {
                        $product_detail['pickup_id'] = intval($entityId);
                        $product_detail_obj = new PickupFullProductDetailLaptop();
                        $product_detail_obj->attributes = $product_detail;
                        $product_detail_obj->validate();
                        $product_detail_obj->save(false);
                    }
                }
            }
        }
    }
}
