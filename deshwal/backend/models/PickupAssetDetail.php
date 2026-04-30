<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pickup_asset_detail".
 *
 * @property int $pickup_asset_detail_id
 * @property int $pickup_id
 * @property string|null $porduct_name
 * @property string|null $hsn_code
 * @property float $total_quantity
 * @property string|null $uom
 * @property int|null $picked_qty
 * @property int|null $difference
 * @property int|null $status
 * @property int $deleted
 *
 * @property Pickup $pickup
 */
class PickupAssetDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pickup_asset_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pickup_id'], 'required'],
            [['pickup_id', 'difference', 'status', 'deleted'], 'integer'],
            [['category','sub_category','model_no','make','all_accessories','quoted_price_gst_include','quoted_price_gst_exclude',
                'quantity_quoted','cgst','sgst','igst','cgst_amount','sgst_amount','igst_amount','total_quoted_price_gst_include',
                'total_quoted_price_gst_exclude'], 'safe'],
            [['total_quantity'], 'string', 'max' => 100],
            [['porduct_name', 'hsn_code', 'uom'], 'string', 'max' => 200],
            [['pickup_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pickup::class, 'targetAttribute' => ['pickup_id' => 'pickup_id']],
            [['picked_qty'], 'number'],
        ]; 
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pickup_asset_detail_id' => 'Pickup Asset Detail ID',
            'pickup_id' => 'Pickup ID',
            'porduct_name' => 'Porduct Name',
            'category' => 'Category',
            'sub_category' => 'Sub Category',
            'model_no' => 'Model No',
            'make' => 'Make',
            'all_accessories' => 'All Accessories',
            'hsn_code' => 'Hsn Code',
            'quoted_price_gst_include' => 'Quoted Price (inclusive of GST)',
            'quoted_price_gst_exclude' => 'Quoted Price (GST exclude)',
            'quantity_quoted' => 'Total Quantity',
            'uom' => 'Uom',
            'cgst' => 'CGST',
            'sgst' => 'SGST',
            'igst' => 'IGST',
            'cgst_amount' => 'CGST Amount',
            'sgst_amount' => 'SGST Amount',
            'igst_amount' => 'IGST Amount',
            'total_quoted_price_gst_include' => 'Total Quoted Price (inclusive of GST)',
            'total_quoted_price_gst_exclude' => 'Total Quoted Price (GST exclude)',
            'picked_qty' => 'Picked Qty',
            'difference' => 'Difference',
            'status' => 'Status',
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

    public function savePickupAssetDetail($entityId)
    {
        $savePickupAssetDetail = $_POST['pickup_asset_detail']??[];
        if (!empty($savePickupAssetDetail)) {
            if (count($savePickupAssetDetail) > 0) {
                foreach ($savePickupAssetDetail as $product_detail) {
                    $product_detail['pickup_id'] = $entityId;
                    $product_detail_obj = new PickupAssetDetail();
                    $product_detail_obj->attributes = $product_detail;
                    $product_detail_obj->validate();
                    $product_detail_obj->save(false);
                }
            }
        }
    }
}
