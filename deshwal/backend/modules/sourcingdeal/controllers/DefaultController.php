<?php

namespace backend\modules\sourcingdeal\controllers;

use common\controllers\ModuleController;
use Yii;

/**
 * Default controller for the `vendoraccount` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'single';
    public $ModuleName = 'sourcingdeal';
    public $FieldId = 'sourcingdeal_id';
    public $TableName = 'sourcingdeal';
    public $TabLabel = 'Sourcing Deal';

    public $TabId = '51';
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
    public function actionGetcontacts()
    {
        $data = $_POST;
        $contact_name = Yii::$app->request->post('contact_name');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                        SELECT contacts_id,first_name,mobile,email,contactrole_value as contact_role,cdesignation_value as designation,department_value as department FROM contacts
                        left join contact_role on contact_role.contactroleid = contacts.contact_role
                        left join cdesignation on cdesignation.cdesignationid = contacts.designation
                        left join cdepartments on cdepartments.departmentsid = contacts.department
                        WHERE contacts_id = :contacts_id
                    ")->bindValue(":contacts_id", $contact_name);
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
                'data' => ''
            ]);
        }
    }
    public function actionGetwarehouse()
    {
        $data = $_POST;
        $business_entity = Yii::$app->request->post('business_entity');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                        SELECT address,state,statecode,pincode,gstn FROM warehouse WHERE warehouse_id = :business_entity
                    ")->bindValue(":business_entity", $business_entity);
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
                'data' => ''
            ]);
        }
    }
    public function actionGetvendorlocation()
    {
        $data = $_POST;
        $bill_location = Yii::$app->request->post('bill_location');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                        SELECT legal_entity_name,address,state,state_code,gstin_no_uin,pincode FROM vendor_locations WHERE vendorloc_id = :bill_location
                    ")->bindValue(":bill_location", $bill_location);
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
                'data' => ''
            ]);
        }
    }

    public function actionCheckopportunity()
    {
        $data = $_POST;
        $vendor_account_name = Yii::$app->request->post('vendor_account_name');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                        SELECT count(*) as cnt FROM opportunity WHERE stage = 16 and vendor_account_name = :vendor_account_name and closing_date >= DATE_SUB(CURDATE(), INTERVAL 2 YEAR) 
                    ")->bindValue(":vendor_account_name", $vendor_account_name);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            $cnt = $columns['cnt'];
            if ($cnt > 0)
                $data = $cnt;
            else
                $data = 0;
            return $this->asJson([
                'status' => 'success',
                'data' => $data,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Contact found.',
                'data' => ''
            ]);
        }
    }

    public function actionGetoem()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {

            $db = Yii::$app->db;

            // Fetch subcategories where prod_catagory_id matches the selected category
            $query = "
                SELECT roleid AS id, rolename AS name 
                FROM role where depth =1 and rolename like 'OEM%'";
            $command = $db->createCommand($query);
            $subcategories = $command->queryAll();

            return ['status' => 'success', 'subcategories' => $subcategories];

        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()];
        }
    }
    public function actionGetoemanager()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        try {

            if (isset($_POST['role'])) {
                $role = strval($_POST['role']); // Sanitize input
                $db = Yii::$app->db;
    
                // Fetch OEMs where sub_catagory_id matches the selected subcategory
                // echo "
                //     SELECT user.id as id, concat(first_name,' ',last_name) AS name 
                //     FROM  user join user2role on user2role.userid = user.id where user2role.roleid='$role' and deleted =0 and status=10 group by userid";die;
                $query = "
                    SELECT roleid AS id, rolename AS name 
                FROM role where rolename like '%manager%' and parentrole like '%$role%' ";
                $command = $db->createCommand($query);
                // $command->bindValue(':roleid', $role);
                $oems = $command->queryAll();
    
                return ['status' => 'success', 'oems' => $oems];
            } else {
                return ['status' => 'error', 'message' => 'OEM is required.'];
            }

        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()];
        }
    }

    public function actionGetoemanagername()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        try {

            if (isset($_POST['role'])) {
                $role = strval($_POST['role']); // Sanitize input
                $db = Yii::$app->db;
    
                // Fetch OEMs where sub_catagory_id matches the selected subcategory
                // echo "
                //     SELECT user.id as id, concat(first_name,' ',last_name) AS name 
                //     FROM  user join user2role on user2role.userid = user.id where user2role.roleid='$role' and deleted =0 and status=10 group by userid";die;
                $query = "
                    SELECT user.id as id, concat(first_name,' ',last_name) AS name 
                    FROM  user join user2role on user2role.userid = user.id where user2role.roleid=:roleid and deleted =0 and status=10 group by userid";
                $command = $db->createCommand($query);
                $command->bindValue(':roleid', $role);
                $oems = $command->queryAll();
    
                return ['status' => 'success', 'oems' => $oems];
            } else {
                return ['status' => 'error', 'message' => 'OEM is required.'];
            }

        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()];
        }
    }
    public function actionGetoememail() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        try {

            if (isset($_POST['oem_manager_name'])) {
                $oem_manager_name = strval($_POST['oem_manager_name']); // Sanitize input
                $db = Yii::$app->db;
                $query = "select email from user where id=:id";
                $command = $db->createCommand($query);
                $command->bindValue(':id', $oem_manager_name);
                $oems = $command->queryOne();
                // print_r($oems);die;
                if(isset($oems['email']))
                $email = $oems['email'];
                else $email = '';
               
    
                return ['status' => 'success', 'oememail' => $email];
            } else {
                return ['status' => 'error', 'message' => 'OEM Manager Name is required.'];
            }

        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()];
        }
        
    }
    public function actionCheckproducts()
    {
        $data = $_POST;
        $record = Yii::$app->request->post('record');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                        Select count(*) as cnt from product_costing where related_to = 51 and related_to_id = :record
                    ")->bindValue(":record", $record);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            $cnt = $columns['cnt'];
            if($cnt > 0)
            {
                return $this->asJson([
                    'status' => 'success',
                    'data' => 1,
                ]);
            }
            else{
                return $this->asJson([
                    'status' => 'success',
                    'data' => 0,
                ]);

            }
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No record found.',
                'data' => ''
            ]);
        }
    }

    public function actionGetaccmgrandisr()
   {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        try {

            if (isset($_POST['acc_id'])) {
                $acc_id = strval($_POST['acc_id']); // Sanitize input
                $db = Yii::$app->db;
                // H25 account manager
                // H50 deshwal ISR
                // $query = "
                //     SELECT 
                //         u1.first_name AS account_manager_name,
                //         u1.id As account_manager_id,
                //         u2.first_name AS deshwal_isr_name,
                //         u2.id AS deshwal_isr_id
                //     FROM vendor_account_orgaisation_section va
                //      LEFT JOIN user u1 ON va.userid = u1.id AND va.roleid = 'H25' AND u1.deleted = 0 AND u1.status = 10
                //    LEFT JOIN user u2 ON va.userid = u2.id AND va.roleid = 'H50' AND u2.deleted = 0 AND u2.status = 10
                //     WHERE va.vendoraccid = :acc_id ";
                //above query return two record
                $query = "SELECT 
                        va.vendoraccid,
                        MAX(CASE WHEN va.roleid = 'H25' THEN CONCAT_WS(' ', u.first_name, u.last_name) END) AS account_manager_name,
                        MAX(CASE WHEN va.roleid = 'H25' THEN u.id END) AS account_manager_id,
                        MAX(CASE WHEN va.roleid = 'H50' THEN CONCAT_WS(' ', u.first_name, u.last_name) END) AS deshwal_isr_name,
                        MAX(CASE WHEN va.roleid = 'H50' THEN u.id END) AS deshwal_isr_id
                    FROM vendor_account_orgaisation_section va
                    JOIN user u 
                    ON va.userid = u.id 
                    AND u.deleted = 0 
                    AND u.status = 10
                    WHERE va.vendoraccid = :acc_id
                    GROUP BY va.vendoraccid";
                $command = $db->createCommand($query);
                $command->bindValue(':acc_id', $acc_id);
                $data = $command->queryOne();
    
                return ['status' => 'success', 'data' => $data];
            } else {
                return ['status' => 'error', 'message' => 'Something went wrong while getting Account manager and Deshwal ISR.'];
            }

        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()];
        }
    }
 
}
