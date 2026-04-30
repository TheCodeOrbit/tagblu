<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_dit".
 *
 * @property int $productdit_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $productdit_no
 * @property string|null $product_name
 * @property string|null $product_description
 * @property string|null $hsn_code
 * @property string|null $oem_part_number
 * @property int|null $unit_in_hand
 * @property float|null $opening_stock
 * @property int|null $master_category
 * @property int|null $product_nuture
 * @property int|null $groupid
 * @property int|null $category
 * @property int|null $sub_category
 * @property int|null $oem
 * @property float|null $cost_price
 * @property float|null $gst_percentage
 * @property int|null $seq_no
 * @property int $deleted
 * @property float|null $product_dimensions_cm
 * @property float|null $product_weight_kgs
 * @property string|null $uom
 */
class ProductDit extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_dit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['productdit_no', 'product_name', 'product_description', 'hsn_code', 'oem_part_number', 'unit_in_hand', 'opening_stock', 'master_category', 'product_nuture', 'groupid', 'category', 'sub_category', 'oem', 'cost_price', 'gst_percentage', 'seq_no', 'product_dimensions_cm', 'product_weight_kgs', 'uom'], 'default', 'value' => null],
            [['deleted'], 'default', 'value' => 0],
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'unit_in_hand', 'master_category', 'product_nuture', 'groupid', 'category', 'sub_category', 'oem', 'seq_no', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime'], 'safe'],
            [['product_description'], 'string'],
            [['opening_stock', 'cost_price', 'gst_percentage', 'product_dimensions_cm', 'product_weight_kgs'], 'number'],
            [['productdit_no', 'product_name', 'hsn_code', 'oem_part_number', 'uom'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'productdit_id' => 'Productdit ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'productdit_no' => 'Productdit No',
            'product_name' => 'Product Name',
            'product_description' => 'Product Description',
            'hsn_code' => 'Hsn Code',
            'oem_part_number' => 'Oem Part Number',
            'unit_in_hand' => 'Unit In Hand',
            'opening_stock' => 'Opening Stock',
            'master_category' => 'Master Category',
            'product_nuture' => 'Product Nuture',
            'groupid' => 'Groupid',
            'category' => 'Category',
            'sub_category' => 'Sub Category',
            'oem' => 'Oem',
            'cost_price' => 'Cost Price',
            'gst_percentage' => 'Gst Percentage',
            'seq_no' => 'Seq No',
            'deleted' => 'Deleted',
            'product_dimensions_cm' => 'Product Dimensions Cm',
            'product_weight_kgs' => 'Product Weight Kgs',
            'uom' => 'Uom',
        ];
    }

}
