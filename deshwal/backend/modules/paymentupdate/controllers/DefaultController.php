<?php

namespace backend\modules\paymentupdate\controllers;

use app\models\ModtrackerBasic;
use common\controllers\ModuleController;
use Yii;


/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout='single';
    public $ModuleName='paymentupdate';
    public $FieldId='payment_update_id';
    public $TableName='payment_update';
    public $TabLabel='Payment Update';

   
    public $TabId='101';
    /**
     * Renders the index view for the module
     * @return string
     */

    public function actionExample()
    {
        return $this->render('index');
    }

    /*public function actionGetinvoicenonvendorname()
    {
        $soid = Yii::$app->request->post('recordId');
        $connection = Yii::$app->db;
        // SELECT warehouse_name,address,gstn,contact_number,pan_number from warehouse 
        $command = $connection->createCommand("
                        SELECT vl.invoice_amount,vl.invoice_number,vl.vendor_name,va.acc_name
                        from vehicle_loading vl
                        LEFT JOIN vendor_account va ON va.vendoraccid = vl.vendor_name
                        WHERE so_number = :so_number
                    ")->bindValue(":so_number", $soid);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Product info found.',
                'data' => ''
            ]);
        }
    }*/

        public function actionGetinvoicenonvendorname()
        {
            $soid = Yii::$app->request->post('recordId');
            $connection = Yii::$app->db;

            $invoiceQuery = "
                SELECT 
                    vl.invoice_amount,
                    vl.invoice_number,
                    vl.vendor_name,
                    va.acc_name
                FROM vehicle_loading vl
                LEFT JOIN vendor_account va ON va.vendoraccid = vl.vendor_name
                WHERE vl.so_number = :so_number
            ";

            $invoiceCmd = $connection->createCommand($invoiceQuery)
                ->bindValue(":so_number", $soid);
            $invoiceData = $invoiceCmd->queryOne();

            //  Get total payment received for that SO
            $paymentQuery = "
                SELECT 
                    IFNULL(SUM(payment_receive_amount), 0) AS total_received
                FROM salesorder_payment_detail
                WHERE salesorder_id = :so_number
            ";

            $paymentCmd = $connection->createCommand($paymentQuery)
                ->bindValue(":so_number", $soid);
            $paymentData = $paymentCmd->queryOne();

            //  Calculate balance amount safely
            $invoiceAmount = isset($invoiceData['invoice_amount']) ? (float)$invoiceData['invoice_amount'] : 0;
            $paymentReceived = isset($paymentData['total_received']) ? (float)$paymentData['total_received'] : 0;

            $balanceAmount = $invoiceAmount - $paymentReceived;

            //  Merge the data for response
            $responseData = [
                'invoice_number' => $invoiceData['invoice_number'] ?? '',
                'invoice_amount' => $invoiceData['invoice_amount'] ?? '',
                'vendor_name'    => $invoiceData['vendor_name'] ?? '',
                'acc_name'       => $invoiceData['acc_name'] ?? '',
                'invoice_amount' => $invoiceAmount,
                'balance_amount' => $balanceAmount,
            ];

            //  Return JSON response
            if (!empty($invoiceData)) {
                return $this->asJson([
                    'status' => 'success',
                    'data' => $responseData,
                ]);
            } else {
                return $this->asJson([
                    'status' => 'error',
                    'message' => 'No invoice found for this SO.',
                    'data' => []
                ]);
            }
        }


}
