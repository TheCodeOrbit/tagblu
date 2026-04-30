<?php

namespace backend\modules\paymentdit\controllers;

use common\controllers\ModuleController;
use common\components\TcpdfHelper;
use DateTime;
use backend\models\AccessCheck;
use app\models\Paymentdit;
use app\models\EditModel;
use yii\base\Exception;

use Yii;
/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'multiple';
    public $ModuleName = 'paymentdit';
    public $FieldId = 'paymentdit_id';
    public $TableName = 'paymentdit';
    public $TabLabel = 'DevIT Payment';


    public $TabId = '89';
    /**
     * Renders the index view for the module
     * @return string
     */
    //  public function beforeAction($action)
// {
//     $this->layout = '@app/views/layouts/main';  // Set the main layout before each action
//     return parent::beforeAction($action);
// }

    public function actionExample()
    {
        return $this->render('index');
    }

    public function actionGetinvoicedetail()
    {
        $dcid = Yii::$app->request->get('dcid');
        $connection = Yii::$app->db;
        // SELECT * from delivery_challandit 
        //                   WHERE deliverychallan_id = :dcid
        $command = $connection->createCommand("
                        SELECT invoicedit_no,payment_due_date  as invoice_due_date,total_invoice_amount from invoicedit where invoicedit_id = :dcid
                    ")->bindValue(":dcid", $dcid);
        $columns = $command->queryOne();

        //get balance amount

        $command = $connection->createCommand("
                        SELECT sum(amount_received) as totalreceived from paymentdit where invoice_number_lookup = :dcid
                    ")->bindValue(":dcid", $dcid);
        $columnsbal = $command->queryOne();
        if(!empty($columnsbal))
        {
            $columns['balance_amount'] =$columns['total_invoice_amount'] - $columnsbal['totalreceived'];
        }
        else{
             $columns['balance_amount'] = $columns['total_invoice_amount'];
        }
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
    }





}
