<?php

namespace backend\modules\servicemaster\controllers;

use common\controllers\ModuleController;
use Yii;
/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{ 
    public $layout = 'single';
    public $ModuleName = 'servicemaster';
    public $FieldId = 'servicemaster_id';
    public $TableName = 'servicemaster';
    public $TabLabel = 'Service Master';


    public $TabId = '52';
    /**
     * Renders the index view for the module
     * @return string
     */
    //  public function beforeAction($action)
// {
//     $this->layout = '@app/views/layouts/main';  // Set the main layout before each action
//     return parent::beforeAction($action);
// }

public function actionSubcategories()
{
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    try {
        if (isset($_POST['category_id'])) {
            $categoryId = intval($_POST['category_id']); // Sanitize input
            $db = Yii::$app->db;

            // Fetch subcategories where prod_catagory_id matches the selected category
            $query = "
                SELECT service_sub_categoryid AS id, service_sub_category_value AS name 
                FROM service_sub_category 
                WHERE FIND_IN_SET(:category_id, category_id) AND is_active = 1";
            $command = $db->createCommand($query);
            $command->bindValue(':category_id', $categoryId);
            $subcategories = $command->queryAll();

            return ['status' => 'success', 'subcategories' => $subcategories];
        } else {
            return ['status' => 'error', 'message' => 'Category ID is required.'];
        }
    } catch (\Exception $e) {
        return ['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()];
    }
}



    public function actionExample()
    {
        return $this->render('index');
    }

    //code added by ptpatel on date 08-01-2025
     public function actionIsservicenameuplicate()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $field = Yii::$app->request->post('field');
        $value = Yii::$app->request->post('value');
        $recordid = Yii::$app->request->post('recordid');

        if (!in_array($field, ['service_name'])) {
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