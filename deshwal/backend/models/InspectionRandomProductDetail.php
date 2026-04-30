<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "inspection_random_product_detail".
 *
 * @property int $inspection_product_detail_id
 * @property int $inspection_id
 * @property int|null $product_name
 * @property int|null $product_subcategory
 * @property int|null $qty
 * @property int|null $uom
 * @property int|null $conditions
 * @property int|null $critical
 * @property string|null $remarks
 */
class InspectionRandomProductDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inspection_random_product_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inspection_id'], 'required'],
            [['inspection_id', 'product_name', 'product_subcategory', 'uom', 'conditions', 'critical'], 'integer'],
            [['remarks'], 'string', 'max' => 200],
            [[ 'qty'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'inspection_product_detail_id' => 'Inspection Product Detail ID',
            'inspection_id' => 'Inspection ID',
            'product_name' => 'Product Name',
            'product_subcategory' => 'Product Subcategory',
            'qty' => 'Qty',
            'uom' => 'Uom',
            'conditions' => 'Conditions',
            'critical' => 'Critical',
            'remarks' => 'Remarks',
        ];
    }
    public function saveInspectionRandomProductDetail($entityId)
    {
        if (empty($_POST['inspection_random_product_detail']) || !is_array($_POST['inspection_random_product_detail'])) {
            return false;
        }
        else{
             //delete old record from child table
            
             $sql = "Delete from inspection_random_product_detail where inspection_id = :inspection_id";
             Yii::$app->db->createCommand($sql)->bindValue(":inspection_id", $entityId)->execute();
        }
        $grn_items=$_REQUEST['inspection_random_product_detail'];
		if(count($grn_items)>0)
		{
			foreach($grn_items as $product_detail)
			{
			$product_detail['inspection_id']=$entityId;
			$product_detail_obj=new InspectionRandomProductDetail;	
			$product_detail_obj->attributes=$product_detail;
            // print_r($product_detail_obj->attributes);die;
			$product_detail_obj->validate();
			$product_detail_obj->save(false);
			}
		}
        
    }
    
}
