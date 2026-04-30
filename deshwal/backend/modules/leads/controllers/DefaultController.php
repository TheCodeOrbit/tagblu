<?php

namespace backend\modules\leads\controllers;

use common\controllers\ModuleController;
use yii;
/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout='single';
    public $ModuleName='leads';
    public $FieldId='leadid';
    public $TableName='leadinformation';
    public $TabLabel='Lead';

   
    public $TabId='7';
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

    
    public function actionGetvendordetail()
    {   
        $data = $_POST;
        $account_name = Yii::$app->request->post('account_name');
        $connection = Yii::$app->db;
        // echo "
        //                 SELECT emp_size,empsize_value,industry,annual_revenue FROM vendor_account
        //                 join employee_size em on em.empsizeid = vendor_account.emp_size
        //                 WHERE vendoraccid = $account_name
        //             ";die;
        $command = $connection->createCommand("
                        SELECT industry FROM vendor_account                        
                        WHERE vendoraccid = :account_name
                    ")->bindValue(":account_name", $account_name);
        $columns = $command->queryOne();
        if (!empty($columns)) {
              return $this->asJson([
                    'status' => 'success',
                    'data' => $columns,
                ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Contact found.',
                'data'=>''
            ]);
        }
    }
    public function actionCheckcontact()
    {
        $data = $_POST;
        $contact = Yii::$app->request->post('contact');
        $vendor_account_name =  Yii::$app->request->post('vendor_account_name');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                        SELECT count(*) as cnt FROM contacts WHERE vendor_account_name = :vendor_account_name and contacts_id = :contact and is_temp=0
                    ")->bindValue(":contact", $contact)
                    ->bindValue(":vendor_account_name", $vendor_account_name);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            if($columns['cnt']>0)
            $data ="matched";
        else $data ='';
              return $this->asJson([
                    'status' => 'success',
                    'data' => $data,
                ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Contact found.',
                'data'=>''
            ]);
        }
    }

    public function actionGetdatamining()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    try {
       
            
            $db = Yii::$app->db;

            // Fetch OEMs where sub_catagory_id matches the selected subcategory
            $query = "
               SELECT id,concat(first_name,' ',last_name) as fullname 
                                    FROM user 
                                    JOIN user2role ON user2role.userid = user.id 
                                    WHERE user.deleted = 0 
                                    AND status = 10 
                                    AND user2role.roleid = 'H74' 
                                    ORDER BY id desc";
            $command = $db->createCommand($query);
            $oems = $command->queryAll();

            return ['status' => 'success', 'users' => $oems];
        
    } catch (\Exception $e) {
        return ['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()];
    }


    }

   public function actionIsaccountduplicate()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $field = Yii::$app->request->post('field');
        $value = Yii::$app->request->post('value');

        // Allow duplicate check only for account_name
        if (!in_array($field, ['account_name', 'deal_name'])) {
            return ['exists' => false];
        }

        $normalizedValue = strtolower(str_replace(' ', '', $value));

        $exists = (new \yii\db\Query())
            ->from('vendor_account')
            ->where(
                new \yii\db\Expression(
                    'LOWER(REPLACE(acc_name, " ", "")) = :val',
                    [':val' => $normalizedValue]
                )
            )
            ->exists();

        return ['exists' => $exists];

    }

}
