<?php

namespace app\models;

use Yii;

class PurchaseOrderItemsDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'purchase_order_itemsdetail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['purchase_order_id'], 'required'],
            // [['productid', 'product_costing_id', 'category', 'subcategory', 'quantity_required', 'uom', 'deleted'], 'integer'],
            [['hsn_code','category', 'quantity', 'cost_price', 'cgst', 'sgst', 'deleted','igst','base_cp','total','asset_condition'], 'safe'],
            [['product_name','product_description',], 'string', 'max' => 200],
            
            [['purchase_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Purchaseorder::class, 'targetAttribute' => ['purchase_order_id' => 'purchase_order_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'purchase_order_id' => 'Purchase Order Id',
            'product_name' => 'Item Name',
            'product_description' => 'Sub-Category',
            'hsn_code' => 'Lot No.',
            'category' => 'Customer Name',
            'quantity' => 'Purchase Order Quantity',
            'cost_price' => 'Quantity in Invoice',
            'cgst' => 'Physical Quantity',
            'deleted' => 'Deleted',
            'sgst' => 'Tag Numbers',
            'igst' => 'Make',
            'base_cp' => 'Model',
            'total'=> 'Unit Price (Without Tax)',
            'asset_condition' => 'Unit Price (With Tax - 18%)'     
        ];
    }

    /**
     * Gets query for [[Grn]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPurchaseorder()
    {
        return $this->hasOne(Grn::class, ['purchase_order_id' => 'purchase_order_id']);
    }
    public function savePurchaseOderItems($entityId)
    {
        if(empty($_REQUEST['purchase_order_itemsdetail'])){
            return false;
        }
        $po_items=$_REQUEST['purchase_order_itemsdetail'];
		if(count($po_items)>0)
		{
			foreach($po_items as $product_detail)
			{
			$product_detail['purchase_order_id']=$entityId;
			$product_detail_obj=new PurchaseOrderItemsDetail;	
			$product_detail_obj->attributes=$product_detail;
            // print_r($product_detail_obj->attributes);die;
			$product_detail_obj->validate();
			$product_detail_obj->save(false);
			}
		}
    }
}
