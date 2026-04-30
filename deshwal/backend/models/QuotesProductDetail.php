<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "quotes_product_detail".
 *
 * @property int $quotes_product_id
 * @property int $quotes_id
 * @property string|null $p_name
 * @property float $p_qty
 * @property string|null $p_lngdes
 * @property string|null $p_longdes
 *
 * @property Quotes $quotes
 */
class QuotesProductDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quotes_product_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['quotes_id', 'p_qty'], 'required'],
            [['quotes_id'], 'integer'],
            [['p_qty'], 'number'],
            [['p_name', 'p_lngdes', 'p_longdes'], 'string', 'max' => 200],
            [['quotes_id'], 'exist', 'skipOnError' => true, 'targetClass' => Quotes::class, 'targetAttribute' => ['quotes_id' => 'quotes_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'quotes_product_id' => 'Quotes Product ID',
            'quotes_id' => 'Quotes ID',
            'p_name' => 'P Name',
            'p_qty' => 'P Qty',
            'p_lngdes' => 'P Lngdes',
            'p_longdes' => 'P Longdes',
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
    
    public function saveQuotesProductDetail($entityId)
    {
       
        $product_costing_detail=$_REQUEST['quotes_product_detail'];
		//print_r($loss_production_hours);
		
		if(count($product_costing_detail)>0)
		{
			foreach($product_costing_detail as $product_detail)
			{
                if(!empty($product_detail['p_name']))
                {
                    $product_detail['quotes_id']=$entityId;
                    $product_detail_obj=new QuotesProductDetail;	
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
