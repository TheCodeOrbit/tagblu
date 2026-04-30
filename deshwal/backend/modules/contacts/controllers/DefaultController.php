<?php

namespace backend\modules\contacts\controllers;

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
    public $ModuleName='contacts';
    public $FieldId='contacts_id';
    public $TableName='contacts';
    public $TabLabel='Contacts';

   
    public $TabId='19';
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

    public function actionGethierarchy()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
            if (isset($_POST['industry'])) {
                $industry = intval($_POST['industry']); // Sanitize input
                $db = Yii::$app->db;

                // Fetch categories based on industry
                $query = "SELECT hierarchy_id AS id, hierarchy_level_value AS name FROM hierarchy_level WHERE FIND_IN_SET(:industry, vendorindustryid) AND is_active = 1 ORDER BY seq_no ASC";
                $command = $db->createCommand($query);
                $command->bindValue(':industry', $industry);
                $hierarchies = $command->queryAll();

                // Return categories in JSON format
                return ['status' => 'success', 'hierarchies' => $hierarchies];
            } else {
                return ['status' => 'error', 'message' => 'Industry is required.'];
            }
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()];
        }
    }

    // get getdesignation
    public function actionGetdesignation()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
            if (isset($_POST['hierarchy'])) {
                $hierarchy = intval($_POST['hierarchy']); // Sanitize input
                $db = Yii::$app->db;

                // Fetch categories based on hierarchy
                $query = "SELECT cdesignationid  AS id, cdesignation_value AS name FROM cdesignation WHERE FIND_IN_SET(:hierarchy, hierarchy_id) AND is_active = 1 ORDER BY seq_no ASC";
                $command = $db->createCommand($query);
                $command->bindValue(':hierarchy', $hierarchy);
                $categories = $command->queryAll();


                // Return categories in JSON format
                return ['status' => 'success', 'categories' => $categories];
            } else {
                return ['status' => 'error', 'message' => 'Hierarchy is required.'];
            }
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()];
        }
    }

    public function actionGetindustry()
    {
         Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
            if (isset($_POST['accountid'])) {
                $accountid = intval($_POST['accountid']); // Sanitize input
                $db = Yii::$app->db;

                // Fetch categories based on hierarchy
                $query = "SELECT industry FROM vendor_account WHERE vendoraccid = :vendoraccid";
                $command = $db->createCommand($query);
                $command->bindValue(':vendoraccid', $accountid);
                $industry = $command->queryOne();


                // Return categories in JSON format
                return ['status' => 'success', 'industry' => $industry];
            } else {
                return ['status' => 'error', 'message' => 'vendor account is required.'];
            }
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()];
        }
    }

    public function actionCheckexistemailormobile()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $field = Yii::$app->request->post('field');
        $value = Yii::$app->request->post('value');

        if (!in_array($field, ['email', 'mobile'])) {
            return ['exists' => false];
        }

        $exists = (new \yii\db\Query())
            ->from($this->TableName)   // <-- your table 
            ->where([$field => $value])
            ->exists();

        return ['exists' => $exists];
    }

   public function actionUpdatecontactpassword()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $password = Yii::$app->request->post('password');
        $recordid = Yii::$app->request->post('record_id');

        if (empty($password) || empty($recordid)) {
            return ['success' => false, 'message' => 'Invalid request'];
        }

        $hash_pass = Yii::$app->security->generatePasswordHash($password);
        $oldAttributes = Yii::$app->db->createCommand("select * from `$this->TableName` where $this->FieldId=:id")
                    ->bindValue(":id", $recordid)
                    ->queryOne();
        // Update query
        $updated = Yii::$app->db->createCommand()
            ->update(
                $this->TableName, 
                ['password' => $hash_pass], 
                [$this->FieldId => $recordid]
            )
            ->execute();
            $modlog = new ModtrackerBasic();
        
            $newattributes= array("password" => $hash_pass);
            $modlog->auditlog($oldAttributes, $newattributes, $this->ModuleName, $recordid, 7, Yii::$app->user->id);
        if ($updated) {
            return ['success' => true, 'message' => 'Password updated successfully'];
        } else {
            return ['success' => false, 'message' => 'Record not found or password not updated'];
        }
    }
}
