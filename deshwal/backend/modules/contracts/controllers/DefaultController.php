<?php

namespace backend\modules\contracts\controllers;

use common\controllers\ModuleController;
use Yii;

/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'single';
    public $ModuleName = 'contracts';
    public $FieldId = 'contract_id';
    public $TableName = 'contracts';
    public $TabLabel = 'Contracts';


    public $TabId = '12';
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

    public function actionGetaccountdetail()
    {
        $data = $_POST;
        $record_id = Yii::$app->request->post('account_name');


        $connection = Yii::$app->db;
        $command = $connection
            ->createCommand("SELECT account_category_value AS account_category, cust_code, 
                        account_number as bankaccount_number,bank_names as bank_name,bank_ifsc_code as ifsc_code,payment_terms,bank_swift_code as swift_code 
                        FROM vendor_account 
                        JOIN account_category 
                        ON FIND_IN_SET(account_category.account_categoryid, vendor_account.account_category) 
                        WHERE vendoraccid = :vendoraccid")
            ->bindValue(":vendoraccid", $record_id);

        $rows = $command->queryAll(); // Fetch multiple rows

        if (!empty($rows)) {
            // Extract all account categories into an array
            $accountCategories = array_column($rows, 'account_category');

            return $this->asJson([
                'status' => 'success',
                'data' => [
                    'account_category' => implode(", ", $accountCategories), // Convert to a comma-separated string
                    'account_code' => $rows[0]['cust_code'] ?? "", // Assuming the cust_code is same for all
                    'bankaccount_number' => $rows[0]['bankaccount_number'] ?? "",
                    'bank_name' => $rows[0]['bank_name'] ?? "",
                    'ifsc_code' => $rows[0]['ifsc_code'] ?? "",
                    'payment_terms' => $rows[0]['payment_terms'] ?? "",
                    'swift_code' => $rows[0]['swift_code'] ?? "",

                ]
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No detail found.',
                'data' => ''
            ]);
        }
    }

    public function actionGetcontactdetail()
    {
        $data = $_POST;
        $record_id = Yii::$app->request->post('contact_person_name');


        $connection = Yii::$app->db;
        $command = $connection
            ->createCommand("SELECT email as contact_email,cdesignation_value as designation,mobile FROM contacts
        left join cdesignation on  cdesignation.cdesignationid = contacts.designation
        WHERE contacts_id  = :contacts_id ")
            ->bindValues([":contacts_id" => $record_id]);
        $columns = $command->queryOne();
        if (!empty($columns)) {


            return $this->asJson([
                'status' => 'success',
                'data' => [

                    'contact_email' => $columns['contact_email'] ?? "",
                    'contact_designation' => $columns['designation'] ?? "",
                    'contact_phone_number' => $columns['mobile'] ?? "",

                ]
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No detail found.',
                'data' => ''
            ]);
        }
    }
    public function actionGetaddressdetail()
    {
        $data = $_POST;
        $record_id = Yii::$app->request->post('hqcorporate_address');


        $connection = Yii::$app->db;
        $command = $connection
            ->createCommand("SELECT state_value as state,city_name as city,gstin_no_uin,pan_no FROM vendor_locations
        left join city on  city.cityid = vendor_locations.city
        left join state on  state.state_id = vendor_locations.state
        WHERE vendorloc_id  = :vendorloc_id ")
            ->bindValues([":vendorloc_id" => $record_id]);
        $columns = $command->queryOne();
        if (!empty($columns)) {


            return $this->asJson([
                'status' => 'success',
                'data' => [

                    'state' => $columns['state'] ?? "",
                    'city' => $columns['city'] ?? "",
                    'gst' => $columns['gstin_no_uin'] ?? "",
                    'pan' => $columns['pan_no'] ?? "",

                ]
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No detail found.',
                'data' => ''
            ]);
        }
    }

    public function actionGetuserdetail()
    {
        $data = $_POST;
        $record_id = Yii::$app->request->post('userid');


        $connection = Yii::$app->db;
        $command = $connection
            ->createCommand("SELECT user_designation_value as designation FROM user
        left join user_designation on  user_designation.user_designation_id = user.designation
        WHERE id  = :vendorloc_id ")
            ->bindValues([":vendorloc_id" => $record_id]);
        $columns = $command->queryOne();
        if (!empty($columns)) {


            return $this->asJson([
                'status' => 'success',
                'data' => [

                    'designation' => $columns['designation'] ?? "",


                ]
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No detail found.',
                'data' => ''
            ]);
        }
    }

    public function actionGetcustdetail()
    {
        $data = $_POST;
        $record_id = Yii::$app->request->post('userid');



        $connection = Yii::$app->db;
        $command = $connection
            ->createCommand("SELECT cdesignation_value as designation FROM contacts
        left join cdesignation on  cdesignation.cdesignationid = contacts.designation
        WHERE contacts_id  = :contacts_id ")
            ->bindValues([":contacts_id" => $record_id]);
        $columns = $command->queryOne();
        if (!empty($columns)) {


            return $this->asJson([
                'status' => 'success',
                'data' => [

                    'designation' => $columns['designation'] ?? "",


                ]
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No detail found.',
                'data' => ''
            ]);
        }
    }
}
