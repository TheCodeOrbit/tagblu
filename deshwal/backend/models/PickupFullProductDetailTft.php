<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pickup_full_product_detail_tft".
 *
 * @property int $pickup_product_detail_id
 * @property int $pickup_id
 * @property int|null $prod_category
 * @property string|null $serial_number
 * @property int|null $make
 * @property int|null $model
 * @property int|null $screen_size
 * @property int|null $screen_broken
 * @property int|null $physical_dent
 * @property int|null $quantity
 * @property string|null $image_screen
 * @property string|null $image_front
 * @property string|null $image_back
 * @property int|null $critical
 * @property string|null $remarks
 * @property int|null $pickup_done
 * @property int|null $condition
 * @property string|null $pickup_remarks
 *
 * @property Pickup $pickup
 */
class PickupFullProductDetailTft extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pickup_full_product_detail_tft';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pickup_id'], 'required'],
            [['pickup_id'], 'integer'],
            [['prod_category', 'make', 'model', 'screen_size', 'screen_broken', 'physical_dent', 'quantity', 'critical', 'pickup_done', 'condition'], 'safe'],
            [['serial_number', 'image_screen', 'image_front', 'image_back', 'remarks', 'pickup_remarks'], 'safe'],
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
            'screen_size' => 'Screen Size',
            'screen_broken' => 'Screen Broken',
            'physical_dent' => 'Physical Dent',
            'quantity' => 'Quantity',
            'image_screen' => 'Image Screen',
            'image_front' => 'Image Front',
            'image_back' => 'Image Back',
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
            $pickupDetail = new PickupFullProductDetailTft();
            $pickupDetail->pickup_id = $pickup_id;

            // Map attributes from InspectionFullProductDetailTft to PickupFullProductDetailTft
            $pickupDetail->prod_category = $detail->prod_category;
            $pickupDetail->serial_number = $detail->serial_number;
            $pickupDetail->make = $detail->make;
            $pickupDetail->model = $detail->model;
            $pickupDetail->screen_size = $detail->screen_size;
            $pickupDetail->screen_broken = $detail->screen_broken;
            $pickupDetail->physical_dent = $detail->physical_dent;
            $pickupDetail->quantity = $detail->quantity;
            $pickupDetail->image_screen = $detail->image_screen;

            $pickupDetail->image_front = $detail->image_front;
            $pickupDetail->image_back = $detail->image_back;
            
            $pickupDetail->critical = $detail->critical;
            $pickupDetail->remarks = $detail->remarks;   
            if (!$pickupDetail->save(false)) {
                \Yii::error("Failed to save pickup detail tft products: " . json_encode($pickupDetail->errors));
            }
        }
        return true;
    }

    public function savePickupTftProduct($entityId)
    {
        $savePostData = $_POST['pickup_full_product_detail_tft']??[];
        if (!empty($savePostData)) {
            if (count($savePostData) > 0) {
                foreach ($savePostData as $product_detail) {
                    if(is_array($product_detail))
                    {
                        $product_detail['pickup_id'] = intval($entityId);
                        $product_detail_obj = new PickupFullProductDetailTft();
                        $product_detail_obj->attributes = $product_detail;
                        $product_detail_obj->validate();
                        $product_detail_obj->save(false);
                    }
                }
            }
        }
    }
}
