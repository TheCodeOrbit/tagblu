<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "payment_details".
 *
 * @property int $payment_details_id
 * @property int $payments_id
 * @property string $mode
 * @property string $utr_cheque
 * @property string $payment_date
 * @property float $payment_amount
 * @property float $tds_amount
 * @property int $deleted
 */
class PaymentDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['payments_id', 'mode', 'utr_cheque', 'payment_date', 'payment_amount', 'tds_amount', 'deleted'], 'required'],
            [['payments_id', 'deleted'], 'integer'],
            [['payment_date'], 'safe'],
            [['payment_amount', 'tds_amount'], 'number'],
            [['mode', 'utr_cheque'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'payment_details_id' => 'Payment Details ID',
            'payments_id' => 'Payments ID',
            'mode' => 'Mode',
            'utr_cheque' => 'Utr Cheque',
            'payment_date' => 'Payment Date',
            'payment_amount' => 'Payment Amount',
            'tds_amount' => 'Tds Amount',
            'deleted' => 'Deleted',
        ];
    }
    public function savePaymentDetails($entityId)
    {
       //if condition added by ptpatel on date 28-03-25 it throw error in  edit
       if(isset($_POST['payment_details'])){
            $product_costing_detail=$_POST['payment_details'];
            //print_r($loss_production_hours);
            
            if(count($product_costing_detail)>0)
            {
                foreach($product_costing_detail as $product_detail)
                {
                $product_detail['payments_id']=$entityId;
                $product_detail_obj=new PaymentDetails();	
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

