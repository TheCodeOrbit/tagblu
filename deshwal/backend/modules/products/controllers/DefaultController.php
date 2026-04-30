<?php

namespace backend\modules\products\controllers;

use common\controllers\ModuleController;
use Yii;
/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'single';
    public $ModuleName = 'products';
    public $FieldId = 'products_id';
    public $TableName = 'products';
    public $TabLabel = 'Products';


    public $TabId = '9';
    /**
     * Renders the index view for the module
     * @return string
     */
    //  public function beforeAction($action)
// {
//     $this->layout = '@app/views/layouts/main';  // Set the main layout before each action
//     return parent::beforeAction($action);
// }

public function actionCategories()
{
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    try {
        if (isset($_POST['product_group_id'])) {
            $productGroupId = intval($_POST['product_group_id']); // Sanitize input
            $db = Yii::$app->db;

            // Fetch categories based on product_group_id
            $query = "SELECT prod_catagory_id AS id, prod_catagory_value AS name FROM prod_catagory WHERE FIND_IN_SET(:product_group_id, prod_group_id) AND is_active = 1 ORDER BY seq_no ASC";
            $command = $db->createCommand($query);
            $command->bindValue(':product_group_id', $productGroupId);
            $categories = $command->queryAll();

            // Return categories in JSON format
            return ['status' => 'success', 'categories' => $categories];
        } else {
            return ['status' => 'error', 'message' => 'Product Group ID is required.'];
        }
    } catch (\Exception $e) {
        return ['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()];
    }
}

public function actionSubcategories()
{
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    try {
        if (isset($_POST['category_id'])) {
            $categoryId = intval($_POST['category_id']); // Sanitize input
            $db = Yii::$app->db;

            // Fetch subcategories where prod_catagory_id matches the selected category
            $query = "
                SELECT sub_catagory_id AS id, sub_catagory_value AS name 
                FROM prod_sub_catagory 
                WHERE FIND_IN_SET(:category_id, prod_catagory_id) AND is_active = 1";
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

public function actionOems()
{
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    try {
        if (isset($_POST['sub_category_id'])) {
            $subCategoryId = intval($_POST['sub_category_id']); // Sanitize input
            $db = Yii::$app->db;

            // Fetch OEMs where sub_catagory_id matches the selected subcategory
            $query = "
                SELECT prod_oem_id AS id, prod_oem_value AS name 
                FROM prod_oem 
                WHERE FIND_IN_SET(:sub_category_id, sub_catagory_id) AND is_active = 1";
            $command = $db->createCommand($query);
            $command->bindValue(':sub_category_id', $subCategoryId);
            $oems = $command->queryAll();

            return ['status' => 'success', 'oems' => $oems];
        } else {
            return ['status' => 'error', 'message' => 'Sub Category ID is required.'];
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
     public function actionIsproductduplicate()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $field = Yii::$app->request->post('field');
        $value = Yii::$app->request->post('value');
        $recordid = Yii::$app->request->post('recordid');

        if (!in_array($field, ['product_description'])) {
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