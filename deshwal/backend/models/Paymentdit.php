<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "paymentdit".
 *
 * @property int $paymentdit_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $paymentdit_no
 * @property int|null $invoice_number_lookup
 * @property string|null $invoice_number
 * @property string|null $invoice_due_date
 * @property float|null $invoice_amount
 * @property string|null $payment_received_date
 * @property float|null $amount_received
 * @property string|null $utr_number
 * @property int|null $deleted
 */
class Paymentdit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'paymentdit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'invoice_number_lookup', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime', 'invoice_due_date', 'payment_received_date'], 'safe'],
            [['invoice_amount', 'amount_received', 'balance_amount'], 'number'],
            [['paymentdit_no', 'invoice_number', 'utr_number'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'paymentdit_id' => 'Paymentdit ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'paymentdit_no' => 'Paymentdit No',
            'invoice_number_lookup' => 'Invoice Number Lookup',
            'invoice_number' => 'Invoice Number',
            'invoice_due_date' => 'Invoice Due Date',
            'invoice_amount' => 'Invoice Amount',
            'balance_amount' => 'Balance Amount',
            'payment_received_date' => 'Payment Received Date',
            'amount_received' => 'Amount Received',
            'utr_number' => 'Utr Number',
            'deleted' => 'Deleted',
        ];
    }

    public function savetoreports($paymentdit_id)
    {
        $connection = Yii::$app->db;

        //check if full amount receive and update invoice
        $command = $connection->createCommand("
                        SELECT total_invoice_amount,invoice_number_lookup,invoice_amount from paymentdit join invoicedit on invoicedit.invoicedit_id = paymentdit.invoice_number_lookup where paymentdit_id = :paymentdit_id
                    ")->bindValue(":paymentdit_id", $paymentdit_id);
        $columnsbal = $command->queryOne();
        $invoice_number_lookup = '';
        $total_invoice_amount = 0;
        if (!empty($columnsbal['invoice_number_lookup'])) {
            $invoice_number_lookup = $columnsbal['invoice_number_lookup'];
            $total_invoice_amount = $columnsbal['total_invoice_amount'];
        }

        $command = $connection->createCommand("
                        SELECT sum(amount_received) as totalreceived from paymentdit where invoice_number_lookup = :invoice_number_lookup
                    ")->bindValue(":invoice_number_lookup", $invoice_number_lookup);
        $columns = $command->queryOne();
        $totalreceived = round($columns['totalreceived'],2);
        $diff = $total_invoice_amount - $totalreceived;
        echo $total_invoice_amount;
        echo "<br>";
        echo $totalreceived;
        echo "<br>";

        // if ($diff > 1) {
        //     $invoice_status = 6; // Partial Payment Received
        // } else if ($diff > 0) {
        //     $invoice_status = 7; // Payment Received - Closed
        // } else {
        //     // Handle fully paid invoices (optional)
        //     $invoice_status = 7; // Payment Received - Closed
        // }
        // echo $diff;die;
        if($diff >= 0)
             $invoice_status = 7; // Payment Received - Closed
            else
             $invoice_status = 6; // Partial Payment Received   

        // now update invoice status
        $command = $connection->createCommand("
                      Update invoicedit set invoice_status = :invoice_status where invoicedit_id = :invoicedit_id
                    ")->bindValue(":invoice_status", $invoice_status)
            ->bindValue(":invoicedit_id", $invoice_number_lookup)
            ->execute();



    }
}
