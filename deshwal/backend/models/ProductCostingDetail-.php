<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_costing_detail".
 *
 * @property int $product_costing_detail_id
 * @property int $productid
 * @property int $product_costing_id
 * @property string|null $product_description
 * @property int|null $category
 * @property int|null $subcategory
 * @property string $hsn_code
 * @property float|null $cp
 * @property int|null $quantity_required
 * @property int|null $uom
 * @property float|null $cgst
 * @property float|null $sgst
 * @property float|null $igst
 * @property float|null $gst_amount
 * @property float|null $sgst_amount
 * @property float|null $igst_amount
 * @property float|null $total_cp
 * @property float|null $total_price
 * @property int $deleted
 *
 * @property ProductCosting $productCosting
 */
class ProductCostingDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_costing_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['productid', 'product_costing_id', 'hsn_code'], 'required'],
            [['productid', 'product_costing_id', 'category', 'subcategory', 'quantity_required', 'uom', 'deleted'], 'integer'],
            [['cp', 'cgst', 'sgst', 'igst', 'gst_amount', 'sgst_amount', 'igst_amount', 'total_cp', 'total_price'], 'number'],
            [['product_description'], 'string', 'max' => 200],
            [['hsn_code'], 'string', 'max' => 100],
            [['product_costing_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductCosting::class, 'targetAttribute' => ['product_costing_id' => 'product_costing_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'product_costing_detail_id' => 'Product Costing Detail ID',
            'productid' => 'Productid',
            'product_costing_id' => 'Product Costing ID',
            'product_description' => 'Product Description',
            'category' => 'Category',
            'subcategory' => 'Subcategory',
            'hsn_code' => 'Hsn Code',
            'cp' => 'Cp',
            'quantity_required' => 'Quantity Required',
            'uom' => 'Uom',
            'cgst' => 'Cgst',
            'sgst' => 'Sgst',
            'igst' => 'Igst',
            'gst_amount' => 'Gst Amount',
            'sgst_amount' => 'Sgst Amount',
            'igst_amount' => 'Igst Amount',
            'total_cp' => 'Total Cp',
            'total_price' => 'Total Price',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * Gets query for [[ProductCosting]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductCosting()
    {
        return $this->hasOne(ProductCosting::class, ['product_costing_id' => 'product_costing_id']);
    }
}
