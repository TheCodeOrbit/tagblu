<?php

namespace backend\modules\vendoraccount\controllers;

use common\controllers\ModuleController;
use Yii;
/**
 * Default controller for the `vendoraccount` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'single';
    public $ModuleName = 'vendoraccount';
    public $FieldId = 'vendoraccid';
    public $TableName = 'vendor_account';
    public $TabLabel = 'Accounts';

    public $TabId = '18';
    /**
     * Renders the index view for the module
     * @return string
     */
    //  public function beforeAction($action)
// {
//     $this->layout = '@app/views/layouts/main';  // Set the main layout before each action
//     return parent::beforeAction($action);
// }

    public function actionGetsubindustry()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
            if (isset($_POST['sub_industry_type'])) {
                $sub_industry_type = intval($_POST['sub_industry_type']); // Sanitize input
                $db = Yii::$app->db;

                // Fetch categories based on state
                $query = "SELECT sub_industry_id  AS id, sub_industry_value AS name FROM sub_industry WHERE FIND_IN_SET(:sub_industry_type, subindustry_type_id) AND is_active = 1 ORDER BY seq_no ASC";
                $command = $db->createCommand($query);
                $command->bindValue(':sub_industry_type', $sub_industry_type);
                $categories = $command->queryAll();

                // Return categories in JSON format
                return ['status' => 'success', 'categories' => $categories];
            } else {
                return ['status' => 'error', 'message' => 'Sub Industry is required.'];
            }
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()];
        }
    }
    public function actionGetsubindustrytype()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
            if (isset($_POST['industry'])) {
                $industry = intval($_POST['industry']); // Sanitize input
                $db = Yii::$app->db;

                // Fetch categories based on state
                $query = "SELECT subindustry_type_id   AS id, subindustry_type_value AS name FROM subindustry_type WHERE FIND_IN_SET(:industryid, industryid) AND is_active = 1 ORDER BY seq_no ASC";
                $command = $db->createCommand($query);
                $command->bindValue(':industryid', $industry);
                $categories = $command->queryAll();

                // Return categories in JSON format
                return ['status' => 'success', 'categories' => $categories];
            } else {
                return ['status' => 'error', 'message' => 'Sub Industry is required.'];
            }
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()];
        }
    }

    
    public function actionIsaccountduplicate()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $field = Yii::$app->request->post('field');
        $value = Yii::$app->request->post('value');
        $recordid = Yii::$app->request->post('recordid');

        if (!in_array($field, ['acc_name'])) {
            return ['exists' => false];
        }

        $query = (new \yii\db\Query())
        ->from($this->TableName)
        ->where(['LOWER(REPLACE(' . $field . ', " ", ""))' => strtolower(str_replace(' ', '', $value))]);

        if (!empty($recordid)) {
            $query->andWhere(['!=', $this->FieldId, $recordid]);
        }

        $exists = $query->exists();

        return ['exists' => $exists];
    }
}
