<?php

namespace backend\modules\iqclaptopgrade\controllers;

use common\controllers\ModuleController;
use Yii;
/**
 * Default controller for the `Iqclaptopgrade` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout='multiple';
    public $ModuleName='iqclaptopgrade';
    public $FieldId='laptop_grade_id';
    public $TableName='iqc_laptop_grade';
    public $TabLabel='Laptop Grade';
    public $TabId='39';
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

    public function actionGetmodels()
    {   
        $data = $_POST;
        $make = Yii::$app->request->post('make');
        $connection = Yii::$app->db;

        $command = $connection
        ->createCommand("SELECT modelid as value,model_value as text FROM iqc_model WHERE makeid = :make and is_active=1 order by seq_no")
        ->bindValue(":make", $make);
        $columns = $command->queryAll();
        if (!empty($columns)) {
              return $this->asJson([
                    'status' => 'success',
                    'data' => $columns,
                ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Data found.',
                'data'=>[]
            ]);
        }
    }
}
