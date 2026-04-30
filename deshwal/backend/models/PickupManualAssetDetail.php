<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pickup_manual_asset_detail".
 *
 * @property int $pickup_asset_detail_id
 * @property int $pickup_id
 * @property string|null $productname
 * @property string|null $category_name
 * @property string|null $sub_category_name
 * @property string|null $model_name
 * @property string|null $make_name
 * @property string $qty
 * @property int $deleted
 */
class PickupManualAssetDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pickup_manual_asset_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pickup_id', 'qty'], 'required'],
            [['pickup_id', 'deleted'], 'integer'],
            [['productname'], 'string', 'max' => 200],
            [['category_name', 'sub_category_name', 'model_name', 'make_name', 'qty'], 'string', 'max' => 100],
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
            'productname' => 'Productname',
            'category_name' => 'Category Name',
            'sub_category_name' => 'Sub Category Name',
            'model_name' => 'Model Name',
            'make_name' => 'Make Name',
            'qty' => 'Qty',
            'deleted' => 'Deleted',
        ];
    }
    public function savePickupManualAssetDetail($entityId)
    {

        $savePickupManualAssetDetail = $_POST['pickup_manual_asset_detail']??[];
        // print_r($savePickupManualAssetDetail);die;
        if (!empty($savePickupManualAssetDetail)) {
            if (count($savePickupManualAssetDetail) > 0) {
                foreach ($savePickupManualAssetDetail as $product_detail) {
                    $product_detail['pickup_id'] = $entityId;
                    $product_detail_obj = new PickupManualAssetDetail();
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
