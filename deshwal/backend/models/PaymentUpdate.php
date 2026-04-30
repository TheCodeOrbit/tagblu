<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "payment_update".
 *
 * @property int $payment_update_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property int $deleted
 * @property string $payment_update_no
 * @property string|null $so_number
 * @property int|null $vendor_name
 * @property string|null $invoice_number
 * @property string|null $invoice_date
 * @property float|null $invoice_amount
 * @property float|null $payment_received_amount
 * @property string|null $pay_payment_date
 * @property float|null $balance_amount
 * @property int|null $status
 */
class PaymentUpdate extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment_update';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['so_number', 'vendor_name', 'invoice_number', 'invoice_date', 'invoice_amount', 'payment_received_amount', 'pay_payment_date', 'balance_amount', 'status'], 'default', 'value' => null],
            [['deleted'], 'default', 'value' => 0],
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime', 'payment_update_no'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'deleted', 'vendor_name', 'status'], 'integer'],
            [['createdtime', 'modifiedtime', 'invoice_date', 'pay_payment_date'], 'safe'],
            [['invoice_amount', 'payment_received_amount', 'balance_amount'], 'number'],
            [['payment_update_no'], 'string', 'max' => 100],
            [['so_number', 'invoice_number'], 'string', 'max' => 200],
              // added for handling blank values saving in by ptpatel on date 24-01-2026
            [['vendor_name'], 'trim'],
            [['vendor_name'], 'required', 'message' => 'Vendor Name cannot be blank.'],
            [['vendor_name'], 'integer', 'message' => 'Vandor Name must be a number.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'payment_update_id' => 'Payment Update ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'deleted' => 'Deleted',
            'payment_update_no' => 'Payment Update No',
            'so_number' => 'So Number',
            'vendor_name' => 'Vendor Name',
            'invoice_number' => 'Invoice Number',
            'invoice_date' => 'Invoice Date',
            'invoice_amount' => 'Invoice Amount',
            'payment_received_amount' => 'Payment Received Amount',
            'pay_payment_date' => 'Payment Date',
            'balance_amount' => 'Balance Amount',
            'status' => 'Status',
        ];
    }

}
