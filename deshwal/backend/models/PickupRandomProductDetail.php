<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pickup_random_product_detail".
 *
 * @property int $pickup_product_detail_id
 * @property int $pickup_id
 * @property int|null $product_name
 * @property int|null $product_subcategory
 * @property int|null $qty
 * @property int|null $uom
 * @property int|null $conditions
 * @property int|null $critical
 * @property string|null $remarks
 * @property int|null $pickup_done
 * @property int|null $condition
 * @property string|null $pickup_remarks
 *
 * @property Pickup $pickup
 */
class PickupRandomProductDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pickup_random_product_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pickup_id'], 'required'],
            [['pickup_id'], 'integer'],
            [['product_name', 'product_subcategory', 'qty', 'uom', 'conditions', 'critical', 'pickup_done', 'condition'], 'safe'],
            [['remarks', 'pickup_remarks'], 'safe'],
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
            'product_name' => 'Product Name',
            'product_subcategory' => 'Product Subcategory',
            'qty' => 'Qty',
            'uom' => 'Uom',
            'conditions' => 'Conditions',
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
            $pickupDetail = new PickupRandomProductDetail();
            $pickupDetail->pickup_id = $pickup_id;

            // Map attributes from PickupRandomProductDetail to PickupRandomProductDetail
            $pickupDetail->product_name = $detail->product_name;
            $pickupDetail->product_subcategory = $detail->product_subcategory;
            $pickupDetail->qty = $detail->qty;
            $pickupDetail->uom = $detail->uom;
            $pickupDetail->conditions = $detail->conditions;
            $pickupDetail->critical = $detail->critical;
            $pickupDetail->remarks = $detail->remarks;  
            if (!$pickupDetail->save(false)) {
                \Yii::error("Failed to save pickup detail random products: " . json_encode($pickupDetail->errors));
            }
        }
        return true;
    }

    public function savePickupRandomProduct($entityId)
    {
        $savePostData = $_POST['pickup_random_product_detail']??[];
        if (!empty($savePostData)) {
            if (count($savePostData) > 0) {
                foreach ($savePostData as $product_detail) {
                    if(is_array($product_detail))
                    {
                        $product_detail['pickup_id'] = intval($entityId);
                        $product_detail_obj = new PickupRandomProductDetail();
                        $product_detail_obj->attributes = $product_detail;
                        $product_detail_obj->validate();
                        $product_detail_obj->save(false);
                    }
                }
            }
        }
    }
}
