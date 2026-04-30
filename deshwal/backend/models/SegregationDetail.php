<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "segregation_detail".
 *
 * @property int $segregation_detail_id
 * @property int $segregation_id
 * @property string|null $product_name
 * @property int|null $sub_category
 * @property string|null $model_no
 * @property string|null $make
 * @property int|null $qty
 * @property int|null $uom
 * @property string|null $location_floor
 * @property string|null $location_code
 * @property int|null $status
 * @property int $deleted
 */
class SegregationDetail extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'segregation_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_name', 'category','sub_category', 'model_no', 'make', 'qty', 'prod_weight' ,'uom','hsn', 'location_floor', 'location_code', 'status'], 'default', 'value' => null],
            [['deleted'], 'default', 'value' => 0],
            [['segregation_id'], 'required'],
            [['segregation_id', 'status', 'deleted'], 'integer'],
            [['product_name', 'location_code','category','uom','sub_category'], 'string', 'max' => 100],
            [['model_no', 'make', 'location_floor'], 'string', 'max' => 200],
            [[ 'qty','prod_weight'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'segregation_detail_id' => 'Segregation Detail ID',
            'segregation_id' => 'Segregation ID',
            'product_name' => 'Product Name',
            'sub_category' => 'Sub Category',
            'model_no' => 'Model No',
            'make' => 'Make',
            'qty' => 'Qty',
            'uom' => 'Uom',
            'location_floor' => 'Location Floor',
            'location_code' => 'Location Code',
            'status' => 'Status',
            'deleted' => 'Deleted',
            'prod_weight' => 'Weight',
        ];
    }

    public function saveSegregationDetail($entityId)
    {
        // echo "<pre>";print_r($_POST['segregation_detail']);die;
        $items=$_POST['segregation_detail']??[];
        if(count($items)>0)
		{
            $i=1;
			foreach($items as $rec)
			{
                $rec['segregation_id']=$entityId;
                $rec_obj=new SegregationDetail();	
                $rec_obj->attributes=$rec;
                $rec_obj->validate();
                $rec_obj->save(false);
                // if($rec_obj->status == 1)//inventory
                // {
                //     $inventory = new Inventory();
                //     $inventory->saveInventory($_POST['segregation'],$_POST['segregation_detail'][$i]);
                // }

                $i++;
			}
		}
    }
}
