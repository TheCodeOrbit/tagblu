<?php

namespace backend\modules\payments\controllers;

use common\controllers\ModuleController;
use Yii;


/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout='multiple';
    public $ModuleName='payments';
    public $FieldId='payments_id';
    public $TableName='payments';
    public $TabLabel='Payments';
 
   
    public $TabId='65';
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

    public function actionGetsourcingdetail(){
        $data = $_POST;
        $sourcingdeal_id = Yii::$app->request->post('sourcingdeal');
        $connection = Yii::$app->db;
        // sd.deal_name- added by ptpatel on date 17-11-2025 for point 101 of v11 sheet
        $command = $connection->createCommand("
                         SELECT  sd.deal_name,acc_name,stage_value,vendoraccid,va.bank_names	,va.account_name,va.account_number,va.bank_ifsc_code,va.bank_swift_code  from sourcingdeal sd
                         join sourcingdeal_stage on sourcingdeal_stage.stage_id = sd.stage
                         join vendor_account va on va.vendoraccid = sd.vendor_account_name
                         WHERE sourcingdeal_id = :sourcingdeal_id
                    ")->bindValue(":sourcingdeal_id", $sourcingdeal_id);
        $columns = $command->queryOne();
        if (!empty($columns)) {
              return $this->asJson([
                    'status' => 'success',
                    'data' => $columns,
                ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Info found.',
                'data'=>''
            ]);
        }
    }

    public function actionGetpodetail(){
        $data = $_POST;
        $po_id = Yii::$app->request->post('po');
        $connection = Yii::$app->db;

        // $command = $connection->createCommand("
        //                  SELECT  total_amount from purchase_order
        //                  WHERE purchase_order_id = :purchase_order_id
        //             ")->bindValue(":purchase_order_id", $po_id);
        $command = $connection->createCommand("
                         SELECT  grand_total as total_amount from purchase_order
                         WHERE purchase_order_id = :purchase_order_id
                    ")->bindValue(":purchase_order_id", $po_id);
        $columns = $command->queryOne();
        if (!empty($columns)) {
              return $this->asJson([
                    'status' => 'success',
                    'data' => $columns,
                ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Info found.',
                'data'=>''
            ]);
        }
    }

   
}
