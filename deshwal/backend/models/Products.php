<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "products".
 *
 * @property int $products_id
 * @property int|null $ownerid
 * @property int|null $creatorid
 * @property int|null $modifiedby
 * @property string|null $createdtime
 * @property string|null $modifiedtime
 * @property string|null $product_no
 * @property string|null $product_name
 * @property int|null $product_type
 * @property string|null $product_description
 * @property string|null $hsn_code
 * @property string|null $unit_in_hand
 * @property string|null $vendor_name
 * @property int|null $tax_preference
 * @property int|null $active
 * @property int|null $product_nature
 * @property int|null $master_category
 * @property int|null $product_group
 * @property int|null $category
 * @property int|null $subcategory
 * @property int|null $make
 * @property int|null $model
 * @property int|null $oem
 * @property int|null $waste_catagory
 * @property string|null $eee_category
 * @property string|null $weight_kg
 * @property float|null $weight_with_packing_kg
 * @property int|null $uom
 * @property string|null $remarks
 * @property string|null $mop
 * @property string|null $cost_price
 * @property float|null $gst_percentage
 * @property int|null $mrp
 * @property float|null $minimum_margin_percentage
 * @property float|null $std_margin_percentage
 * @property float|null $max_margin_percentage
 * @property string|null $valid_from
 * @property float|null $packing_cost
 * @property int $push_to_books
 * @property string|null $qty_ordered
 * @property int $create_in_books
 * @property string|null $online_selling
 * @property string|null $length
 * @property string|null $width
 * @property string|null $height
 * @property float|null $standard_sale_price
 * @property int|null $processor
 * @property int|null $generation
 * @property int|null $storage_capacity
 * @property int|null $drive_type
 * @property int|null $ram
 * @property int $is_active
 * @property int $deleted
 */
class Products extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'products';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime', 'product_no', 'product_name', 'product_type', 'product_description', 'hsn_code', 'unit_in_hand', 'vendor_name', 'tax_preference', 'product_nature', 'master_category', 'product_group', 'category', 'subcategory', 'make', 'model', 'oem', 'waste_catagory', 'eee_category', 'weight_kg', 'weight_with_packing_kg', 'uom', 'remarks', 'mop', 'cost_price', 'gst_percentage', 'mrp', 'minimum_margin_percentage', 'std_margin_percentage', 'max_margin_percentage', 'valid_from', 'packing_cost', 'qty_ordered', 'online_selling', 'length', 'width', 'height', 'standard_sale_price', 'processor', 'generation', 'storage_capacity', 'drive_type', 'ram'], 'default', 'value' => null],
            [['deleted'], 'default', 'value' => 0],
            [['is_active'], 'default', 'value' => 1],
            [['ownerid', 'creatorid', 'modifiedby', 'product_type', 'tax_preference', 'active', 'product_nature', 'master_category', 'product_group', 'category', 'subcategory', 'make', 'model', 'oem', 'waste_catagory', 'uom',  'push_to_books', 'create_in_books', 'processor', 'generation', 'storage_capacity', 'drive_type', 'ram', 'is_active', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime', 'valid_from'], 'safe'],
            [['weight_with_packing_kg', 'gst_percentage', 'minimum_margin_percentage', 'std_margin_percentage', 'max_margin_percentage', 'packing_cost', 'standard_sale_price','mrp',], 'number'],
            [['product_no', 'product_description', 'remarks'], 'string', 'max' => 200],
            [['product_name', 'hsn_code', 'unit_in_hand', 'vendor_name', 'eee_category', 'weight_kg', 'mop', 'cost_price', 'qty_ordered', 'online_selling', 'length', 'width', 'height'], 'string', 'max' => 100],
            [['product_description'], 'validateDuplicateProductdesc'],
            ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'products_id' => 'Products ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'product_no' => 'Product No',
            'product_name' => 'Product Name',
            'product_type' => 'Product Type',
            'product_description' => 'Product Description',
            'hsn_code' => 'Hsn Code',
            'unit_in_hand' => 'Unit In Hand',
            'vendor_name' => 'Vendor Name',
            'tax_preference' => 'Tax Preference',
            'active' => 'Active',
            'product_nature' => 'Product Nature',
            'master_category' => 'Master Category',
            'product_group' => 'Product Group',
            'category' => 'Category',
            'subcategory' => 'Subcategory',
            'make' => 'Make',
            'model' => 'Model',
            'oem' => 'Oem',
            'waste_catagory' => 'Waste Catagory',
            'eee_category' => 'Eee Category',
            'weight_kg' => 'Weight Kg',
            'weight_with_packing_kg' => 'Weight With Packing Kg',
            'uom' => 'Uom',
            'remarks' => 'Remarks',
            'mop' => 'Mop',
            'cost_price' => 'Cost Price',
            'gst_percentage' => 'Gst Percentage',
            'mrp' => 'Mrp',
            'minimum_margin_percentage' => 'Minimum Margin Percentage',
            'std_margin_percentage' => 'Std Margin Percentage',
            'max_margin_percentage' => 'Max Margin Percentage',
            'valid_from' => 'Valid From',
            'packing_cost' => 'Packing Cost',
            'push_to_books' => 'Push To Books',
            'qty_ordered' => 'Qty Ordered',
            'create_in_books' => 'Create In Books',
            'online_selling' => 'Online Selling',
            'length' => 'Length',
            'width' => 'Width',
            'height' => 'Height',
            'standard_sale_price' => 'Standard Sale Price',
            'processor' => 'Processor',
            'generation' => 'Generation',
            'storage_capacity' => 'Storage Capacity',
            'drive_type' => 'Drive Type',
            'ram' => 'Ram',
            'is_active' => 'Is Active',
            'deleted' => 'Deleted',
        ];
    }

    public function validateDuplicateProductdesc($attribute, $params)
    {
        if ($this->hasErrors()) {
            return;
        }

        $query = self::find()
        ->where(['LOWER(REPLACE(product_description, " ", ""))' => strtolower(str_replace(' ', '', $this->$attribute))]);
            // ->where(['product_description' => $this->$attribute]);

        // IMPORTANT: ignore current record while updating
        if (!$this->isNewRecord) {
            $query->andWhere(['!=', 'products_id', $this->product_id]); // replace `id` with your PK
        }

        if ($query->exists()) {
            $this->addError($attribute, $this->$attribute . ' already exists!');
        }
    }
}
 