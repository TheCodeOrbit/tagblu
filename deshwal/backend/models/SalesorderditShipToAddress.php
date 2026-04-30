<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "salesorderdit_ship_to_address".
 *
 * @property int $salesorderdit_ship_to_address_id
 * @property int $salesorder_dit_id
 * @property string|null $ship_delivery_location
 * @property string|null $ship_address
 * @property string|null $ship_city
 * @property string|null $ship_state
 * @property string|null $ship_pin_code
 * @property string|null $ship_state_code
 * @property string|null $ship_gst
 * @property string|null $ship_pan
 * @property int $deleted
 *
 * @property SalesorderDit $salesorderDit
 */
class SalesorderditShipToAddress extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'salesorderdit_ship_to_address';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['salesorder_dit_id'], 'required'],
            [['salesorder_dit_id', 'deleted'], 'integer'],
            [['ship_delivery_location', 'ship_city', 'ship_state', 'ship_pin_code', 'ship_state_code', 'ship_gst'], 'string', 'max' => 200],
            [['ship_address'], 'string', 'max' => 3000],
            [['ship_pan'], 'string', 'max' => 100],
            [['salesorder_dit_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalesorderDit::class, 'targetAttribute' => ['salesorder_dit_id' => 'salesorder_dit_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'salesorderdit_ship_to_address_id' => 'Salesorderdit Ship To Address ID',
            'salesorder_dit_id' => 'Salesorder Dit ID',
            'ship_delivery_location' => 'Ship Delivery Location',
            'ship_address' => 'Ship Address',
            'ship_city' => 'Ship City',
            'ship_state' => 'Ship State',
            'ship_pin_code' => 'Ship Pin Code',
            'ship_state_code' => 'Ship State Code',
            'ship_gst' => 'Ship Gst',
            'ship_pan' => 'Ship Pan',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * Gets query for [[SalesorderDit]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSalesorderDit()
    {
        return $this->hasOne(SalesorderDit::class, ['salesorder_dit_id' => 'salesorder_dit_id']);
    }
    public function saveSalesorderditShipToAddress($entityId)
    {
        if(empty($_REQUEST['salesorderdit_ship_to_address'])){
            return false;
        }
         else{
             //delete old record from child table            
             $sql = "Delete from salesorderdit_ship_to_address where salesorder_dit_id = :salesorder_dit_id";
             Yii::$app->db->createCommand($sql)->bindValue(":salesorder_dit_id", $entityId)->execute();
        }
        $po_items=$_REQUEST['salesorderdit_ship_to_address'];
		if(count($po_items)>0)
		{
			foreach($po_items as $product_detail)
			{
			$product_detail['salesorder_dit_id']="$entityId";
			$product_detail_obj=new SalesorderditShipToAddress;	
			$product_detail_obj->attributes=$product_detail;
            // print_r($product_detail_obj->attributes);die;
			$product_detail_obj->validate();
			$product_detail_obj->save(false);
			}
		}
    }
}
