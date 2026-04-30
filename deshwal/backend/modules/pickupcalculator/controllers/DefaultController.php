<?php

namespace backend\modules\pickupcalculator\controllers;

use app\models\PickupCalculatorParent;
use common\controllers\ModuleController;
use Yii;
use yii\web\Response;

/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'multiple';
    public $ModuleName = 'pickupcalculator';
    public $FieldId = 'pickup_calculator_parentid';
    public $TableName = 'pickup_calculator_parent';
    public $TabLabel = 'Pickup Cost Calculator';
    public $ChildTableName = 'pickup_calculator';
    public $ChildFieldId = 'pickup_calculator_id';


    public $TabId = '66';
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

    public function actionGetproductinfo()
    {
        $data = $_POST;
        $productid = Yii::$app->request->post('productid');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                        SELECT  products_id,product_name  FROM `products` 
                          WHERE products_id = :products_id
                    ")->bindValue(":products_id", $productid);
        $columns = $command->queryOne();
        if (!empty($columns)) {


            return $this->asJson([
                'status' => 'success',
                'data' => $columns,

            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Product info found.',
                'data' => ''
            ]);
        }
    }

    public function actionCheckpickupduplicate()
{
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    $productId = Yii::$app->request->post('productid');
    $exists = PickupCalculatorParent::find()->where(['productid' => $productId])->exists();

    return ['exists' => $exists];
}
}
