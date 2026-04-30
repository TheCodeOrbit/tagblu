<?php

namespace app\models;

use Yii;

class GrnItemDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'grn_item_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['grn_id'], 'required'],
            // [['productid', 'product_costing_id', 'category', 'subcategory', 'quantity_required', 'uom', 'deleted'], 'integer'],
            [['physical_quantity','unit_price_without_tax', 'unit_price_with_tax', 'item_value', 'location', 'item_description', 'deleted'], 'safe'],
            [['item_name','category','sub_category','lot_no','customer_name','purchase_order_quantity'], 'string', 'max' => 200],
            [['quantity_invoice','tag_numbers','make','model'], 'string', 'max' => 200],
            [['grn_id'], 'exist', 'skipOnError' => true, 'targetClass' => Grn::class, 'targetAttribute' => ['grn_id' => 'grn_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'grn_item_detail_id' => 'GRN Item Detail ID',
            'grn_id' => 'GRNId',
            'item_name' => 'Item Name',
            'category' => 'Category',
            'sub_category' => 'Sub-Category',
            'lot_no' => 'Lot No.',
            'customer_name' => 'Customer Name',
            'purchase_order_quantity' => 'Purchase Order Quantity',
            'quantity_invoice' => 'Quantity in Invoice',
            'physical_quantity' => 'Physical Quantity',
            'deleted' => 'Deleted',
            'tag_numbers' => 'Tag Numbers',
            'make' => 'Make',
            'model' => 'Model',
            'unit_price_without_tax'=> 'Unit Price (Without Tax)',
            'unit_price_with_tax' => 'Unit Price (With Tax - 18%)',
            'item_value' => 'Item Value',
            'location' => 'Location',
            'item_description' => 'Item Description'      
        ];
    }

    /**
     * Gets query for [[Grn]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGrn()
    {
        return $this->hasOne(Grn::class, ['grn_id' => 'grn_id']);
    }
    public function saveGrnItems($entityId)
    {
        if(empty($_REQUEST['grn_item_detail'])){
            return false;
        }
        $grn_items=$_REQUEST['grn_item_detail'];
		if(count($grn_items)>0)
		{
			foreach($grn_items as $product_detail)
			{
			$product_detail['grn_id']=$entityId;
			$product_detail_obj=new GrnItemDetail;	
			$product_detail_obj->attributes=$product_detail;
            // print_r($product_detail_obj->attributes);die;
			$product_detail_obj->validate();
			$product_detail_obj->save(false);
			}
		}
    }
}
