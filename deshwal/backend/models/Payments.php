<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "payments".
 *
 * @property int $payments_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $payment_type
 * @property string|null $sourcing_deal
 * @property string|null $sourcing_deal_stage
 * @property string|null $account_name
 * @property string|null $po
 * @property float|null $total_invoice_amount
 * @property float|null $total_payment_done
 * @property float|null $balance_amount
 * @property float|null $requested_amount
 * @property string|null $stage
 * @property string|null $bank_name
 * @property int|null $account_number
 * @property string|null $swift_code
 * @property string|null $bank_account_name
 * @property string|null $bank_idfc_code
 * @property string|null $payment_bank_name
 * @property int|null $payment_account_number
 * @property string|null $payment_swift_code
 * @property string|null $payment_account_name
 * @property string|null $idfc_code
 * @property string|null $submit_approval
 * @property int $deleted
 */
class Payments extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payments';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby',  'deleted','account_id'], 'integer'],
            [['createdtime', 'modifiedtime'], 'safe'],
            [['total_invoice_amount', 'total_payment_done', 'balance_amount', 'requested_amount'], 'number'],
            [['payment_type','document_name','sourcing_deal', 'sourcing_deal_stage', 'account_name', 'po', 'stage', 'bank_name', 'swift_code', 'bank_account_name', 'bank_idfc_code', 'payment_bank_name', 'payment_swift_code', 'payment_account_name', 'idfc_code', 'submit_approval','upload','account_number', 'payment_account_number',], 'string', 'max' => 100],
            [['sourcing_deal_name'], 'string', 'max' => 1000],
             // added for handling blank values saving in by ptpatel on date 24-01-2026
            [['account_name'], 'trim'],
            [['account_name'], 'required', 'message' => 'Account Name cannot be blank.'],
            // [['account_name'], 'integer', 'message' => 'Account Name must be a number.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'payments_id' => 'Payments ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'payment_type' => 'Payment Type',
            'sourcing_deal' => 'Sourcing Deal',
            'sourcing_deal_stage' => 'Sourcing Deal Stage',
            'account_name' => 'Account Name',
            'account_id'=>'Account ID',
            'po' => 'Po',
            'total_invoice_amount' => 'Total Invoice Amount',
            'total_payment_done' => 'Total Payment Done',
            'balance_amount' => 'Balance Amount',
            'requested_amount' => 'Requested Amount',
            'stage' => 'Stage',
            'bank_name' => 'Bank Name',
            'account_number' => 'Account Number',
            'swift_code' => 'Swift Code',
            'bank_account_name' => 'Bank Account Name',
            'bank_idfc_code' => 'Bank Idfc Code',
            'payment_bank_name' => 'Payment Bank Name',
            'payment_account_number' => 'Payment Account Number',
            'payment_swift_code' => 'Payment Swift Code',
            'payment_account_name' => 'Payment Account Name',
            'idfc_code' => 'Idfc Code',
            'submit_approval' => 'Submit Approval',
            'document_name'=>'Document Name',
            'upload'=>'Upload',
            'deleted' => 'Deleted',
        ];
    }

    function savetoreports($RecordId)
    {
        $Record = (int)$RecordId;
        $this->save_vp_payments($RecordId);
    }
    function save_vp_payments($RecordId)
    {
          // var_dump($RecordId);die;
        $sql = "SELECT payment_no FROM  payments 
        where payments.payments_id=:RecordId";
        //die;
        $result = Yii::$app->db->createCommand($sql)->bindValue(":RecordId",$RecordId)->queryOne();
        $payment_no= $result['payment_no'];
         //delete old record
            $sql_del = "Delete from `rep_vp_payments` where req_reference_no=:req_reference_no";
            Yii::$app->db->createCommand($sql_del)
            ->bindValue(":req_reference_no",$payment_no)
            ->execute();

        // var_dump($RecordId);die;
        $sql = "SELECT sd.vendor_account_name,purchase_order_no,payment_no,invoice_number,po.bill_address,amount,payments.`stage`,payment_stage_value FROM  payments   join `payments_invoice_details` pid  on pid.payments_id=payments.payments_id 
        join purchase_order po on po.purchase_order_id=payments.po 
        join sourcingdeal sd on sd.sourcingdeal_id=payments.sourcing_deal
        join payment_stage on payment_stage.payment_stage_id = payments.stage
        where payments.payments_id=:RecordId";
        //die;
        $result = Yii::$app->db->createCommand($sql)->bindValue(":RecordId",$RecordId)->queryAll();
        // print_r($result);die;
        foreach($result as $value)
        {
            $account_id=$value['vendor_account_name'];
            $purchase_order_no=$value['purchase_order_no'];
            $payment_no= $value['payment_no'];
            $invoice_number = $value['invoice_number'];
            $vendor_loc_name = $value['bill_address'];
            $amount = $value['amount'];
            $status = $value['stage'];
            $status_name = $value['payment_stage_value'];
            
           
           
            $sql_ins = "Insert into `rep_vp_payments` set account_id=:account_id,req_reference_no=:req_reference_no,po_number=:po_number,invoice_number=:invoice_number,location=:location,amount=:amount,status=:status,status_name=:status_name,createdat=now()" ;
            Yii::$app->db->createCommand($sql_ins)
            ->bindValue(":account_id",$account_id)
            ->bindValue(":req_reference_no",$payment_no)
            ->bindValue(":po_number",$purchase_order_no)
            ->bindValue(":invoice_number",$invoice_number)
            ->bindValue(":location",$vendor_loc_name)
            ->bindValue(":amount",$amount)
            ->bindValue(":status",$status)
            ->bindValue(":status_name",$status_name)
            ->execute();
            
        }
        //die;
    }
}
