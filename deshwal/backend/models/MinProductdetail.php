<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "min_productdetail".
 *
 * @property int $min_productdetail_id
 * @property int|null $mindit_id
 * @property string|null $product_description
 * @property int|null $product_name
 * @property float|null $product_qty
 * @property string|null $product_hsn
 * @property int $deleted
 */
class MinProductdetail extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'min_productdetail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mindit_id','product_name', 'product_description', 'product_qty', 'product_hsn'], 'default', 'value' => null],
            [['deleted'], 'default', 'value' => 0],
            [['mindit_id', 'deleted'], 'integer'],
            [['product_qty'], 'number'],
            [['product_description'], 'string', 'max' => 1000],
            [['product_hsn'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'min_productdetail_id' => 'Min Productdetail ID',
            'mindit_id' => 'Min ID',
            'product_description' => 'Product Description',
            'product_qty' => 'Product Qty',
            'product_hsn' => 'Product Hsn',
            'deleted' => 'Deleted',
        ];
    }

     public function saveGenerateProductDetail($materialId)
    {
        $details = $_POST['min_productdetail'] ?? [];
        if (!empty($details)) {
            foreach ($details as $item) {
                if (is_array($item)) {
                    $item['mindit_id'] = intval($materialId);
                    $detailObj = new MinProductdetail();
                    $detailObj->attributes = $item;
                    $detailObj->validate();
                    $detailObj->save(false);
                }
            }
        }
    }

}
