<?php

namespace backend\modules\degaussingcalculator\controllers;

use app\models\DegaussingCalculatorParents;
use common\controllers\ModuleController;
use Yii;

/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout='multiple';
    public $ModuleName='degaussingcalculator';
    public $FieldId='degaussing_cal_parent_id';
    public $TableName='degaussing_calculator_parents';
    public $TabLabel='Degaussing Cost Calculator';
    public $ChildTableName = 'degaussing_calculator';
    public $ChildFieldId = 'degaussing_calculator_id';

   
    public $TabId='62';
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

    public function actionDegaussingcalcheck()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $exists = DegaussingCalculatorParents::find()->exists();

        return ['exists' => $exists];
    }
}
