<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "quotesdit_ship_detail".
 *
 * @property int $quotesdit_shipdetail_id
 * @property int $quotes_dit_id
 * @property string|null $ship_to_location
 * @property string|null $ship_address
 * @property string|null $ship_state
 * @property string|null $ship_state_code
 * @property string|null $ship_gst
 * @property string|null $ship_legal_name
 * @property int $deleted
 *
 * @property QuotesDit $quotesDit
 */
class QuotesditShipDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quotesdit_ship_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['quotes_dit_id'], 'required'],
            [['quotes_dit_id', 'deleted'], 'integer'],
            [['ship_to_location', 'ship_state', 'ship_legal_name'], 'string', 'max' => 200],
            [['ship_address'], 'string', 'max' => 3000],
            [['ship_state_code', 'ship_gst'], 'string', 'max' => 100],
            [['quotes_dit_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuotesDit::class, 'targetAttribute' => ['quotes_dit_id' => 'quotes_dit_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'quotesdit_shipdetail_id' => 'Quotesdit Shipdetail ID',
            'quotes_dit_id' => 'Quotes Dit ID',
            'ship_to_location' => 'Ship To Location',
            'ship_address' => 'Ship Address',
            'ship_state' => 'Ship State',
            'ship_state_code' => 'Ship State Code',
            'ship_gst' => 'Ship Gst',
            'ship_legal_name' => 'Ship Legal Name',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * Gets query for [[QuotesDit]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuotesDit()
    {
        return $this->hasOne(QuotesDit::class, ['quotes_dit_id' => 'quotes_dit_id']);
    }

    public function saveQuotesditShipDetail($entityId)
    {
        if(empty($_REQUEST['quotesdit_ship_detail'])){
            return false;
        }
         else{
             //delete old record from child table            
             $sql = "Delete from quotesdit_ship_detail where quotes_dit_id = :quotes_dit_id";
             Yii::$app->db->createCommand($sql)->bindValue(":quotes_dit_id", $entityId)->execute();
        }
        $po_items=$_REQUEST['quotesdit_ship_detail'];
		if(count($po_items)>0)
		{
			foreach($po_items as $product_detail)
			{
			$product_detail['quotes_dit_id']=$entityId;
			$product_detail_obj=new QuotesditShipDetail;	
			$product_detail_obj->attributes=$product_detail;
            // print_r($product_detail_obj->attributes);die;
			$product_detail_obj->validate();
			$product_detail_obj->save(false);
			}
		}
    }
}
