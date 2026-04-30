<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "salesorder_items_detail".
 *
 * @property int $salesorderitemdetail_id
 * @property int $salesorder_id
 * @property int|null $product_name
 * @property string|null $category
 * @property string|null $sub_category
 * @property float|null $qty_in_stock
 * @property float|null $qty
 * @property float|null $purchase_price
 * @property float|null $selling_price
 * @property float|null $selling_price_gst_exclude
 * @property float|null $base_price_gst_exclude
 * @property float|null $cgst_percentage
 * @property float|null $sgst_percentage
 * @property float|null $igst_percentage
 * @property float|null $cgst_amount
 * @property float|null $sgst_amount
 * @property float|null $igst_amount
 * @property float|null $total_amount
 * @property string|null $tag_number
 * @property string|null $hsn_code
 * @property SalesOrder $salesorder
 * @property string|null $gst_percentage
 */
class SalesorderItemsDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'salesorder_items_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['salesorder_id','inventory_id'], 'required'],
            [['salesorder_id', 'product_name'], 'integer'],
            [['qty_in_stock', 'qty', 'purchase_price','gst_percentage','selling_price' ,'selling_price_gst_exclude', 'base_price_gst_exclude', 'cgst_percentage', 'sgst_percentage', 'igst_percentage', 'cgst_amount', 'sgst_amount', 'igst_amount', 'total_amount'], 'number'],
            [['category', 'sub_category','tag_number','hsn_code'], 'string', 'max' => 100],
            [['salesorder_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalesOrder::class, 'targetAttribute' => ['salesorder_id' => 'salesorder_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'salesorderitemdetail_id' => 'Salesorderitemdetail ID',
            'salesorder_id' => 'Salesorder ID',
            'product_name' => 'Product Name',
            'category' => 'Category',
            'sub_category' => 'Sub Category',
            'qty_in_stock' => 'Qty In Stock',
            'qty' => 'Qty',
            'purchase_price' => 'Purchase Price',
            'selling_price_gst_exclude' => 'Selling Price Gst Exclude',
            'base_price_gst_exclude' => 'Base Price Gst Exclude',
            'cgst_percentage' => 'Cgst Percentage',
            'sgst_percentage' => 'Sgst Percentage',
            'igst_percentage' => 'Igst Percentage',
            'cgst_amount' => 'Cgst Amount',
            'tag_number' => 'Tag Number',
            'sgst_amount' => 'Sgst Amount',
            'igst_amount' => 'Igst Amount',
            'total_amount' => 'Total Amount',
            'gst_percentage' => 'Gst Percentage',
        ];
    }

    /**
     * Gets query for [[Salesorder]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSalesorder()
    {
        return $this->hasOne(SalesOrder::class, ['salesorder_id' => 'salesorder_id']);
    }
  public function saveSalesOrderProductDetail($salesOrderId)
    {
        $details = $_POST['salesorder_items_detail'] ?? [];
        if (!empty($details)) {
            foreach ($details as $product) {
                if (is_array($product)) {
                    $product['salesorder_id'] = intval($salesOrderId);
                    $detailObj = new SalesorderItemsDetail();
                    $detailObj->attributes = $product;
                    $detailObj->validate();
                    $detailObj->save(false);
                }
            }
        }
    }

    public function getProduct()
    {
        return $this->hasOne(Products::class, ['products_id' => 'product_name']);
    }

}
