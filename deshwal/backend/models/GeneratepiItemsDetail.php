<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "generatepi_items_detail".
 *
 * @property int $generateitemdetail_id
 * @property int $generatepi_id
 * @property int|null $product_name
 * @property string|null $hsn_code
 * @property float|null $qty
 * @property float|null $base_price_gst_exclude
 * @property float|null $cgst_percentage
 * @property float|null $sgst_percentage
 * @property float|null $igst_percentage
 * @property float|null $cgst_amount
 * @property float|null $sgst_amount
 * @property float|null $igst_amount
 * @property float|null $total_amount
 *
 * @property GeneratePi $generatepi
 */
class GeneratepiItemsDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'generatepi_items_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['generatepi_id'], 'required'],
            [['generatepi_id', 'product_name'], 'integer'],
            [['qty', 'base_price_gst_exclude', 'cgst_percentage', 'sgst_percentage', 'igst_percentage', 'cgst_amount', 'sgst_amount', 'igst_amount', 'total_amount'], 'number'],
            [['hsn_code'], 'string', 'max' => 200],
            [['generatepi_id'], 'exist', 'skipOnError' => true, 'targetClass' => GeneratePi::class, 'targetAttribute' => ['generatepi_id' => 'generatepi_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'generateitemdetail_id' => 'Generateitemdetail ID',
            'generatepi_id' => 'Generatepi ID',
            'product_name' => 'Product Name',
            'hsn_code' => 'Hsn Code',
            'qty' => 'Qty',
            'base_price_gst_exclude' => 'Base Price Gst Exclude',
            'cgst_percentage' => 'Cgst Percentage',
            'sgst_percentage' => 'Sgst Percentage',
            'igst_percentage' => 'Igst Percentage',
            'cgst_amount' => 'Cgst Amount',
            'sgst_amount' => 'Sgst Amount',
            'igst_amount' => 'Igst Amount',
            'total_amount' => 'Total Amount',
        ];
    }

    /**
     * Gets query for [[Generatepi]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGeneratepi()
    {
        return $this->hasOne(GeneratePi::class, ['generatepi_id' => 'generatepi_id']);
    }

    public function saveGeneratepiProductDetail($generatepiId)
    {
        $details = $_POST['generatepi_items_detail'] ?? [];
        if (!empty($details)) {
            foreach ($details as $item) {
                if (is_array($item)) {
                    $item['generatepi_id'] = intval($generatepiId);
                    $detailObj = new GeneratepiItemsDetail();
                    $detailObj->attributes = $item;
                    $detailObj->validate();
                    $detailObj->save(false);
                }
            }
        }
    }

}
