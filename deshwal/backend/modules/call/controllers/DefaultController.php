<?php

namespace backend\modules\call\controllers;

use common\controllers\ModuleController;
use yii;
/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'single';
    public $ModuleName = 'call';
    public $FieldId = 'callinfo_id';
    public $TableName = 'call_information';
    public $TabLabel = 'Calls';


    public $TabId = '20';
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
    public function actionGetaccountname()
    {
        $data = $_POST;
        $related_to = Yii::$app->request->post('related_to');
        $related_to_id = Yii::$app->request->post('related_to_id');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("SELECT  relation_with_account,tablename,tablekeyid from module_relation join tab on tab.tabid = source_module WHERE source_module = :related_to and related_table='call_information' ")
            ->bindValue(":related_to", $related_to);
        $columns = $command->queryOne();
        $columns2 = '';
        if (!empty($columns)) {
            $relation_with_account = $columns['relation_with_account'];
            $tablename = $columns['tablename'];
            $tablekeyid = $columns['tablekeyid'];
            if (!empty($relation_with_account)) {
                if ($tablename == "vendor_account") {
                    $command = $connection->createCommand("SELECT  vendoraccid as vendor,acc_name from vendor_account where vendoraccid  = $related_to_id ");
                    $columns2 = $command->queryOne();
                } else {
                   
                    $command = $connection->createCommand("SELECT  $tablename.`$relation_with_account` as vendor,va.acc_name from `$tablename` join vendor_account va on va.vendoraccid = $tablename.$relation_with_account where $tablename.`$tablekeyid` = $related_to_id ");
                    $columns2 = $command->queryOne();

                }
            }

        }
        if (!empty($columns2)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns2,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Info found.',
                'data' => ''
            ]);
        }

    }
}
