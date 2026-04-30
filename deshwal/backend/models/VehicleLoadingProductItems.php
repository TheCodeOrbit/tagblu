<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "vehicle_loading_product_items".
 *
 * @property int $vehicleloading_product_items_id
 * @property int $vehicleloading_id
 * @property string|null $product_name
 * @property string|null $category
 * @property string|null $sub_category
 * @property float|null $qty_in_stock
 * @property float|null $qty
 * @property float|null $out_qty
 * @property float|null $difference
 *
 * @property VehicleLoading $vehicleloading
 */
class VehicleLoadingProductItems extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vehicle_loading_product_items';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['vehicleloading_id'], 'required'],
            [['vehicleloading_id'], 'integer'],
            [['qty_in_stock', 'qty', 'out_qty', 'difference'], 'number'],
            [['product_name', 'category', 'sub_category'], 'string', 'max' => 200],
            [['vehicleloading_id'], 'exist', 'skipOnError' => true, 'targetClass' => VehicleLoading::class, 'targetAttribute' => ['vehicleloading_id' => 'vehicleloading_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'vehicleloading_product_items_id' => 'Vehicleloading Product Items ID',
            'vehicleloading_id' => 'Vehicleloading ID',
            'product_name' => 'Product Name',
            'category' => 'Category',
            'sub_category' => 'Sub Category',
            'qty_in_stock' => 'Qty In Stock',
            'qty' => 'Qty',
            'out_qty' => 'Out Qty',
            'difference' => 'Difference',
        ];
    }

    /**
     * Gets query for [[Vehicleloading]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVehicleloading()
    {
        return $this->hasOne(VehicleLoading::class, ['vehicleloading_id' => 'vehicleloading_id']);
    }

    public function saveVehicleLoadingProductDetail($vehicleLoadingId)
    {
        $details = $_POST['vehicle_loading_product_items'] ?? [];
        if (!empty($details)) {
            foreach ($details as $product) {
                if (is_array($product)) {
                    $product['vehicleloading_id'] = intval($vehicleLoadingId);
                    $detailObj = new VehicleLoadingProductItems();
                    $detailObj->attributes = $product;
                    $detailObj->validate();
                    $detailObj->save(false);
                }
            }
        }
    }


}
