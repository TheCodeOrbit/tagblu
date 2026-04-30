<?php

namespace backend\modules\warehouse\controllers;

use common\controllers\ModuleController;
use Yii;

/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout='single';
    public $ModuleName='warehouse';
    public $FieldId='warehouse_id';
    public $TableName='warehouse';
    public $TabLabel='Warehouse';

   
    public $TabId='30';
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

    public function actionGetcontact()
    {
        $warehouse_manager = \Yii::$app->request->get('warehouse_manager');
        $connection = \Yii::$app->db;
        $command = $connection->createCommand("
                        SELECT mobile from user WHERE id = :warehouse_manager
                    ")->bindValue(":warehouse_manager", $warehouse_manager);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No user mobile info found.',
                'data' => ''
            ]);
        }
    }

     //code added by ptpatel on date 08-01-2025
     public function actionIswarehouseduplicate()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $field = Yii::$app->request->post('field');
        $value = Yii::$app->request->post('value');
        $recordid = Yii::$app->request->post('recordid');

        if (!in_array($field, ['warehouse_name'])) {
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
