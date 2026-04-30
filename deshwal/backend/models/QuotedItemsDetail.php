<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "quoted_items_detail".
 *
 * @property int $quoted_items_id
 * @property string $product_name
 * @property int $quotes_id
 * @property string|null $category
 * @property string|null $hsn_code
 * @property string|null $quantity
 * @property string|null $uom
 * @property string|null $list_price
 * @property string|null $cost_price
 * @property string|null $cgst_percent
 * @property string|null $sgst_percent
 * @property string|null $igst_percent
 * @property string|null $cgst_amount
 * @property string|null $sgst_amount
 * @property string|null $igst_amount
 * @property string|null $basic_price
 * @property string|null $basic_cp
 * @property string|null $total_amount
 *
 * @property Quotes $quotes
 */
class QuotedItemsDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quoted_items_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_name', 'quotes_id'], 'required'],
            [['quotes_id','working_condition'], 'integer'],
            [['product_name', 'category', 'hsn_code', 'quantity', 'uom', 'list_price', 'cost_price', 'cgst_percent', 'sgst_percent', 'igst_percent', 'cgst_amount', 'sgst_amount', 'igst_amount', 'basic_price', 'basic_cp', 'total_amount'], 'string', 'max' => 200],
            [['description'], 'string', 'max' => 500],
            [['quotes_id'], 'exist', 'skipOnError' => true, 'targetClass' => Quotes::class, 'targetAttribute' => ['quotes_id' => 'quotes_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'quoted_items_id' => 'Quoted Items ID',
            'product_name' => 'Product Name',
            'quotes_id' => 'Quotes ID',
            'category' => 'Category',
            'hsn_code' => 'Hsn Code',
            'quantity' => 'Quantity',
            'uom' => 'Uom',
            'list_price' => 'List Price',
            'cost_price' => 'Cost Price',
            'cgst_percent' => 'Cgst Percent',
            'sgst_percent' => 'Sgst Percent',
            'igst_percent' => 'Igst Percent',
            'cgst_amount' => 'Cgst Amount',
            'sgst_amount' => 'Sgst Amount',
            'igst_amount' => 'Igst Amount',
            'basic_price' => 'Basic Price',
            'basic_cp' => 'Basic Cp',
            'total_amount' => 'Total Amount',
            'description' => 'Description',
            'working_condition'=>'working condition'
        ];
    }

    /**
     * Gets query for [[Quotes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuotes()
    {
        return $this->hasOne(Quotes::class, ['quotes_id' => 'quotes_id']);
    }
    
    public function saveQuotedItemsDetail($entityId)
    {
       //if condition added by ptpatel on date 28-03-25 it throw error in  edit
       if(isset($_REQUEST['quoted_items_detail'])){
            $product_costing_detail=$_REQUEST['quoted_items_detail'];
            //print_r($loss_production_hours);
            
            if(count($product_costing_detail)>0)
            {
                foreach($product_costing_detail as $product_detail)
                {
                $product_detail['quotes_id']=$entityId;
                $product_detail_obj=new QuotedItemsDetail;	
                $product_detail_obj->attributes=$product_detail;
                // print_r($product_detail_obj->attributes);die;
                $product_detail_obj->validate();
                $product_detail_obj->save(false);
                // $modlog = new ModtrackerBasic();
                // $modlog->auditlog($oldAttributes = '', $product_detail_obj, 'productdetail', $product_detail_obj->$product_costing_detail_id, 0, Yii::$app->user->id);
                }
            }
        }
    }
}