<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "purchaseorderdit_product_details".
 *
 * @property int $product_details_id
 * @property int $purchaseorder_dit_id
 * @property int|null $product_name
 * @property string|null $product_description
 * @property string|null $hsn_code
 * @property float|null $qty
 * @property float|null $so_qty
 * @property float|null $remaining_qty
 * @property string|null $oem_part_number
 * @property float|null $basic_cost_price
 * @property float|null $gst
 * @property float|null $cgst
 * @property float|null $sgst
 * @property float|null $igst
 * @property float|null $product_total
 * @property string|null $reference_no
 * @property int $deleted
 *
 * @property PurchaseOrderDit $purchaseorderDit
 */
class PurchaseorderditProductDetails extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'purchaseorderdit_product_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_name', 'product_description', 'hsn_code', 'qty', 'so_qty', 'remaining_qty', 'oem_part_number', 'basic_cost_price', 'gst', 'cgst', 'sgst', 'igst', 'product_total', 'reference_no'], 'default', 'value' => null],
            [['deleted'], 'default', 'value' => 0],
            [['purchaseorder_dit_id'], 'required'],
            [['purchaseorder_dit_id', 'product_name', 'deleted'], 'integer'],
            [['product_description'], 'string'],
            [['qty', 'so_qty', 'remaining_qty', 'basic_cost_price', 'gst', 'cgst', 'sgst', 'igst', 'product_total'], 'number'],
            [['hsn_code', 'reference_no'], 'string', 'max' => 200],
            [['oem_part_number'], 'string', 'max' => 500],
            [['purchaseorder_dit_id'], 'exist', 'skipOnError' => true, 'targetClass' => PurchaseOrderDit::class, 'targetAttribute' => ['purchaseorder_dit_id' => 'purchaseorder_dit_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'product_details_id' => 'Product Details ID',
            'purchaseorder_dit_id' => 'Purchaseorder Dit ID',
            'product_name' => 'Product Name',
            'product_description' => 'Product Description',
            'hsn_code' => 'Hsn Code',
            'qty' => 'Qty',
            'so_qty' => 'So Qty',
            'remaining_qty' => 'Remaining Qty',
            'oem_part_number' => 'Oem Part Number',
            'basic_cost_price' => 'Basic Cost Price',
            'gst' => 'Gst',
            'cgst' => 'Cgst',
            'sgst' => 'Sgst',
            'igst' => 'Igst',
            'product_total' => 'Product Total',
            'reference_no' => 'Reference No',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * Gets query for [[PurchaseorderDit]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPurchaseorderDit()
    {
        return $this->hasOne(PurchaseOrderDit::class, ['purchaseorder_dit_id' => 'purchaseorder_dit_id']);
    }

}
