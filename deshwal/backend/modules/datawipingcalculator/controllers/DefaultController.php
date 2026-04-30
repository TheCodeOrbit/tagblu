<?php

namespace backend\modules\datawipingcalculator\controllers;

use app\models\DatawipingCalculatorParents;
use common\controllers\ModuleController;
use Yii;

/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'multiple';
    public $ModuleName = 'datawipingcalculator';
    public $FieldId = 'datawiping_cal_parent_id';
    public $TableName = 'datawiping_calculator_parents';
    public $TabLabel = 'Datawiping Cost Calculator';
    public $ChildTableName = 'datawiping_calculator';
    public $ChildFieldId = 'datawiping_calculator_id';


    public $TabId = '60';
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


    public function actionDatawipingcalcheck()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $exists = DatawipingCalculatorParents::find()->exists();

        return ['exists' => $exists];
    }
}
