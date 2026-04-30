<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "payments_invoice_details".
 *
 * @property int $payments_invoice_id
 * @property int $payments_id
 * @property string $invoice_date
 * @property int $invoice_number
 * @property float $amount
 * @property float $cgst
 * @property float $sgst
 * @property float $igst
 * @property float $tcs
 * @property float $tcs_amount
 * @property float $total_amount
 * @property int $deleted
 */
class PaymentsInvoiceDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payments_invoice_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['payments_id', 'invoice_date', 'invoice_number', 'amount', 'total_amount'], 'required'],
            [['payments_id', 'deleted'], 'integer'],
            [['invoice_date'], 'safe'],
            [['amount', 'cgst', 'sgst', 'igst', 'tcs', 'tcs_amount', 'total_amount'], 'number'],
            [['invoice_number'], 'string', 'max' => 100],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'payments_invoice_id' => 'Payments Invoice ID',
            'payments_id' => 'Payments ID',
            'invoice_date' => 'Invoice Date',
            'invoice_number' => 'Invoice Number',
            'amount' => 'Amount',
            'cgst' => 'Cgst',
            'sgst' => 'Sgst',
            'igst' => 'Igst',
            'tcs' => 'Tcs',
            'tcs_amount' => 'Tcs Amount',
            'total_amount' => 'Total Amount',
            'deleted' => 'Deleted',
        ];
    }
    public function savePaymentsInvoiceDetails($entityId)
    {
    //    print_r($_POST['payments_invoice_details']);die;
       if(isset($_POST['payments_invoice_details'])){
            $product_costing_detail=$_POST['payments_invoice_details'];
            if(count($product_costing_detail)>0)
            {
                foreach($product_costing_detail as $product_detail)
                {
                $product_detail['payments_id']=$entityId;
                $product_detail_obj=new PaymentsInvoiceDetails();	
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
