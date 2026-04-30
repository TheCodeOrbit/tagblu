<?php

namespace backend\modules\user\controllers;

use app\models\ModtrackerBasic;
use common\controllers\ModuleController;
use common\models\User;
use yii\validators\StringValidator;
use Yii;

/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'single';
    public $ModuleName = 'user';
    public $FieldId = 'id';
    public $TableName = 'user';
    public $TabLabel = 'User';


    public $TabId = '41';
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

    public function actionGetroleusers()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try {
            if (isset($_POST['role'])) {
                $role = strval($_POST['role']); // Sanitize input
                $db = Yii::$app->db;

                // Fetch categories based on product_group_id
                $query = "SELECT parentrole FROM `role`  where roleid=:roleid";
                $command = $db->createCommand($query);
                $command->bindValue(':roleid', $role);
                $roles = $command->queryOne();
                $parentrole = $roles['parentrole'];
                $parentrolearr = explode("::" . $role, $parentrole);
                $parent = explode("::", $parentrolearr[0]);
                // Add inverted commas to each element
                $quotedArray = array_map(function ($value) {
                    return '"' . $value . '"';  // Add quotes around each element
                }, $parent);

                // Now implode with a separator (e.g., a comma)
                $imploded = implode(", ", $quotedArray);


                $sql = "select id,concat(first_name,' ',last_name) as fullname FROM user join user2role on user2role.userid = user.id where user2role.roleid in ($imploded);";
                // echo $sql;
                $users = Yii::$app->db->createCommand($sql)->queryAll();

                // Return categories in JSON format
                return ['status' => 'success', 'users' => $users];
            } else {
                return ['status' => 'error', 'message' => 'Product Group ID is required.'];
            }
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()];
        }
    }
    public function actionGetfyear()
    {
        $connection = Yii::$app->db;
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {

            $db = Yii::$app->db;
            $command = $connection->createCommand("
                    SELECT * 
                    FROM fyear
                    WHERE is_active = 1
                ");
            $columns = $command->queryAll();

            return ['status' => 'success', 'fyears' => $columns];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()];
        }
    }

       public function actionUpdatecontactpassword()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $password = Yii::$app->request->post('password');
        $recordid = Yii::$app->request->post('record_id');

        if (empty($password) || empty($recordid)) {
            return ['success' => false, 'message' => 'Invalid request']; 
        }

        // echo "from user defaul controller";die;
        // Update query
        $hash_pass = Yii::$app->security->generatePasswordHash($password);
        $oldAttributes = Yii::$app->db->createCommand("select * from `$this->TableName` where $this->FieldId=:id")
                    ->bindValue(":id", $recordid)
                    ->queryOne();
        $updated = Yii::$app->db->createCommand()
            ->update(
                $this->TableName, 
                ['password_hash' => $hash_pass], 
                [$this->FieldId => $recordid]
            )
            ->execute();
            $modlog = new ModtrackerBasic();
        
            $newattributes= array("password_hash" => $hash_pass);
            $modlog->auditlog($oldAttributes, $newattributes, $this->ModuleName, $recordid, 8, Yii::$app->user->id);
        
        if ($updated) {
            return ['success' => true, 'message' => 'Password updated successfully'];
        } else {
            return ['success' => false, 'message' => 'Record not found or password not updated'];
        }
    }

        
    public function actionDeactivateuser()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $recordid = Yii::$app->request->post('record_id');

        if (empty($recordid)) {
            return ['success' => false, 'message' => 'Invalid request'];
        }

        $old_status = User::findOne($recordid);

        // if ($old_status !== null) {
        //     $old_status = $old_status->status;
        // }
        $text ='';
        if($old_status->status == 9){
            $new_status = 10;
            $text = 'Activated';
        }
        else
        {
            $new_status = 9; 
            $text = 'Inactivated';
        }

        // echo "from user defaul controller";die;
        $oldAttributes = $old_status;
        // Update query
        $updated = Yii::$app->db->createCommand()
            ->update(
                $this->TableName, 
                ['status' => $new_status], //9 - inactive 10-active
                [$this->FieldId => $recordid]
            )
            ->execute();
            $modlog = new ModtrackerBasic();
        
            $newattributes= array("status" => $new_status);
            $modlog->auditlog($oldAttributes, $newattributes, $this->ModuleName, $recordid,2 , Yii::$app->user->id);
                        
        if ($updated) {
            return ['success' => true, 'message' => 'User '.$text.' successfully'];
        } else {
            return ['success' => false, 'message' => 'Record not found or user not '.$text];
        }
    }
}
