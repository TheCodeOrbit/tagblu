<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "grn_asset_detail".
 *
 * @property int $grn_asset_detail_id
 * @property int $grn_id
 * @property string|null $porduct_name
 * @property string|null $category
 * @property string|null $sub_category
 * @property string|null $model_no
 * @property string|null $make
 * @property string|null $all_accessories
 * @property string|null $hsn_code
 * @property string|null $quoted_price_gst_include
 * @property string|null $quoted_price_gst_exclude
 * @property string|null $quantity_quoted
 * @property string $total_quantity
 * @property string|null $uom
 * @property string|null $cgst
 * @property string|null $sgst
 * @property string|null $igst
 * @property string|null $cgst_amount
 * @property string|null $sgst_amount
 * @property string|null $igst_amount
 * @property string|null $total_quoted_price_gst_include
 * @property string|null $total_quoted_price_gst_exclude
 * @property int|null $pickup_qty
 * @property int|null $picked_qty
 * @property int|null $difference
 * @property string|null $received_qty
 * @property string|null $received_variance
 * @property string|null $remarks
 * @property int|null $status
 * @property int $deleted
 */
class GrnAssetDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'grn_asset_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['grn_id'], 'required'],
            [['grn_id', 'difference', 'status', 'deleted'], 'integer'],
            [['porduct_name', 'hsn_code', 'uom', 'remarks'], 'string', 'max' => 200],
            [['category', 'sub_category', 'model_no', 'make', 'all_accessories', 'quoted_price_gst_include', 
            'quoted_price_gst_exclude', 'quantity_quoted', 'total_quantity','cgst', 'sgst', 'igst', 'cgst_amount', 
            'sgst_amount', 'igst_amount', 'total_quoted_price_gst_include', 'total_quoted_price_gst_exclude',
            'received_qty', 'received_variance'], 'safe'],
            [['picked_qty','pickup_qty'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'grn_asset_detail_id' => 'Grn Asset Detail ID',
            'grn_id' => 'Grn ID',
            'porduct_name' => 'Porduct Name',
            'category' => 'Category',
            'sub_category' => 'Sub Category',
            'model_no' => 'Model No',
            'make' => 'Make',
            'all_accessories' => 'All Accessories',
            'hsn_code' => 'Hsn Code',
            'quoted_price_gst_include' => 'Quoted Price Gst Include',
            'quoted_price_gst_exclude' => 'Quoted Price Gst Exclude',
            'quantity_quoted' => 'Quantity Quoted',
            'total_quantity' => 'Total Quantity',
            'uom' => 'Uom',
            'cgst' => 'Cgst',
            'sgst' => 'Sgst',
            'igst' => 'Igst',
            'cgst_amount' => 'Cgst Amount',
            'sgst_amount' => 'Sgst Amount',
            'igst_amount' => 'Igst Amount',
            'total_quoted_price_gst_include' => 'Total Quoted Price Gst Include',
            'total_quoted_price_gst_exclude' => 'Total Quoted Price Gst Exclude',
            'pickup_qty' => 'Pickup Qty',
            'picked_qty' => 'Picked Qty',
            'difference' => 'Difference',
            'received_qty' => 'Received Qty',
            'received_variance' => 'Received Variance',
            'remarks' => 'Remarks',
            'status' => 'Status',
            'deleted' => 'Deleted',
        ];
    }

    public function saveGrnAssets($entityId)
    {
        $items=$_POST['grn_asset_detail']??[];
		if(count($items)>0)
		{
			foreach($items as $rec)
			{
                $rec['grn_id']=$entityId;
                $rec_obj=new GrnAssetDetail;	
                $rec_obj->attributes=$rec;
                $rec_obj->validate();
                $rec_obj->save(false);
			}
		}
    }

    public function updategrnstaus($id){
        
        
        if(empty($id)) return false;
        $rows = Yii::$app->db->createCommand("UPDATE grn_asset_detail set grn_status = 0 where grn_asset_detail_id=:id")
        ->bindValue(":id", $id)
        ->execute();
        // if ($rows > 0) {
        //     echo "Status updated.";
        // } else {
        //     echo "No record found or already updated.";
        // }
        // die;
    }
}
