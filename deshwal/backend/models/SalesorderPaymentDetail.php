<?php

namespace app\models;

use DateTime;
use Yii;

/**
 * This is the model class for table "salesorder_payment_detail".
 *
 * @property int $salesorderpaymentdetail_id
 * @property int|null $salesorder_id
 * @property string|null $invoice_no
 * @property float|null $payment_receive_amount
 * @property string|null $payment_date
 * @property float|null $balance_amount
 */
class SalesorderPaymentDetail extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'salesorder_payment_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['salesorder_id', 'invoice_no', 'payment_receive_amount', 'payment_date', 'balance_amount','payment_update_id'], 'default', 'value' => null],
            [['salesorder_id','payment_update_id'], 'integer'],
            [['payment_receive_amount', 'balance_amount'], 'number'],
            [['payment_date'], 'safe'],
            [['invoice_no'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'salesorderpaymentdetail_id' => 'Salesorderpaymentdetail ID',
            'salesorder_id' => 'Salesorder ID',
            'invoice_no' => 'Invoice No',
            'payment_receive_amount' => 'Payment Receive Amount',
            'payment_date' => 'Payment Date',
            'balance_amount' => 'Balance Amount',
        ];
    }

   public function saveorderpaymentdetails($entityId, $from)
    {
        if (isset($_REQUEST['payment_update'])) {
            $paymentupdate = $_REQUEST['payment_update'];

            if (count($paymentupdate) > 0) {

                // Try to find existing record only if editing
                $product_detail_obj = null;
                if ($from == 'edit') {
                    $product_detail_obj = SalesorderPaymentDetail::find()
                        ->where(['payment_update_id' => $entityId])
                        ->one();
                }

                // If no record found, create new (covers both add & missing edit)
                if (!$product_detail_obj) {
                    $product_detail_obj = new SalesorderPaymentDetail();
                    $paymentupdate['payment_update_id'] = $entityId;
                }

                // Common field assignments
                $product_detail_obj->attributes = $paymentupdate;
                $product_detail_obj->salesorder_id = $paymentupdate['so_number'];
                $product_detail_obj->payment_receive_amount = (float)$paymentupdate['payment_received_amount'];
                $product_detail_obj->invoice_no = $paymentupdate['invoice_number'];

                // Handle payment date conversion
                if (!empty($paymentupdate['pay_payment_date'])) {
                    $date = DateTime::createFromFormat('d-m-Y', $paymentupdate['pay_payment_date']);
                    if ($date !== false) {
                        $product_detail_obj->payment_date = $date->format('Y-m-d');
                    }
                }

                // echo "<pre>";print_r($paymentupdate);print_r($product_detail_obj->attributes);die;
                // Validate & save
                $product_detail_obj->validate();
                $product_detail_obj->save(false);
            }
        }
    }


}
