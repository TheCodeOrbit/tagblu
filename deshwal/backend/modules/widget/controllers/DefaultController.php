<?php

namespace backend\modules\widget\controllers;

use app\models\Widget;
use common\controllers\ModuleController;
use Yii;
use yii\base\Exception;

/**
 * Default controller for the `grn` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'single';
    public $ModuleName = 'widget';
    public $FieldId = 'id';
    public $TableName = 'widget';
    public $TabLabel = 'Widget';
    public $TabId = '76';

    public function actionExample()
    {
        return $this->render('index');
    }

    public function actionGetprofiles()
    {
         $connection = Yii::$app->db;

        $command = $connection->createCommand("SELECT profileid,profilename from profile");
        $columns = $command->queryAll();
        if (!empty($columns)) {
              return $this->asJson([
                    'status' => 'success',
                    'data' => $columns,
                ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Info found.',
                'data'=>''
            ]);
        }
    }
}
