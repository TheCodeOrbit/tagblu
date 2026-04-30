<?php

namespace backend\modules\meeting\controllers;

use app\models\Leadinformation;
use common\controllers\ModuleController;
use Yii;

/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout='single';
    public $ModuleName='meeting';
    public $FieldId='meetinginfo_id';
    public $TableName='meeting_information';
    public $TabLabel='Meeting';

   
    public $TabId='21';
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

    public function actionGetexternalusers()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $accountName = \Yii::$app->request->get('account_name');

        if (!$accountName) {
            return ['status' => 'error', 'message' => 'Account name is required.'];
        }

        // Fetch users where account_name matches
       $contacts = (new \yii\db\Query())
            ->select([
                'contacts_id',
                new \yii\db\Expression("CONCAT(first_name, ' ', last_name) AS name")
            ])
            ->from('contacts')
            ->where(['vendor_account_name' => $accountName])
            ->all();

        if (empty($contacts)) {
            return [];
        }

        // Return as JSON
        return $contacts;
    }
    public function actionGetleaddetails()
{
    $leadNo = Yii::$app->request->get('lead_no');

    $lead = (new \yii\db\Query())
        ->from('leadinformation li')
        ->leftJoin('vendor_account va', 'va.vendoraccid = li.vendor')
        ->select([
            'li.customer_type',
            'li.account_name',                
            'li.vendor',                       
            'va.vendoraccid',
            'va.acc_name AS vendor_acc_name',  
        ])
        ->where(['li.lead_no' => $leadNo])
        ->one();

    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    return $lead ?: [];
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
