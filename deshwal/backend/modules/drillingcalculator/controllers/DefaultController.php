<?php

namespace backend\modules\drillingcalculator\controllers;

use app\models\DrillingCalculatorParents;
use common\controllers\ModuleController;
use Yii;

/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'multiple';
    public $ModuleName = 'drillingcalculator';
    public $FieldId = 'drilling_cal_parent_id';
    public $TableName = 'drilling_calculator_parents';
    public $TabLabel='Drilling Cost Calculator';
    public $ChildTableName = 'drilling_calculator';
    public $ChildFieldId = 'drilling_calculator_id';

   
    public $TabId='59';
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

    public function actionGetdrillingcalcheck()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $exists = DrillingCalculatorParents::find()->exists();

        return ['exists' => $exists];
    }
}
