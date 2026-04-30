<?php

namespace backend\modules\productdit\controllers;

use common\controllers\ModuleController;
use Yii;
/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'single';
    public $ModuleName = 'productdit';
    public $FieldId = 'productdit_id';
    public $TableName = 'product_dit';
    public $TabLabel = 'Product Master DevIT';


    public $TabId = '73';
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

    public function actionMastersubcategories()
    {
        $Recordid = Yii::$app->request->post('master_category_id');
        $connection = Yii::$app->db;

         Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {

            $db = Yii::$app->db;

            // Fetch subcategories where prod_catagory_id matches the selected category
            $command = $connection->createCommand("
                    SELECT * 
                    FROM proddit_sub_category
                    WHERE master_category_id = :Recordid AND is_active = 1
                ")->bindValue(":Recordid", $Recordid);
        $columns = $command->queryAll();

            return ['status' => 'success', 'subcategories' => $columns];

        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()];
        }
    }

    //code added by ptpatel on date 08-01-2025
     public function actionIsproductduplicate()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $field = Yii::$app->request->post('field');
        $value = Yii::$app->request->post('value');
        $recordid = Yii::$app->request->post('recordid');

        if (!in_array($field, ['product_name'])) {
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
     //code added by ptpatel on date 08-01-2025
}