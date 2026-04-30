<?php

namespace backend\modules\iqcdesktop\controllers;

use common\controllers\ModuleController;
use Yii;
/**
 * Default controller for the `Iqcdesktop` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout='multiple';
    public $ModuleName='iqcdesktop';
    public $FieldId='iqcdesktop_id';
    public $TableName='iqc_desktop';
    public $TabLabel='IQC Desktop';

   
    public $TabId='35';
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
        if(empty($make)){
            return $this->asJson(['status' => 'success','data' => []]);
        }
        $connection = Yii::$app->db;
        $command = $connection
        ->createCommand("SELECT modelid as value,model_value as text FROM iqcd_model WHERE makeid = :make and is_active=1 order by seq_no")
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
    public function actionGethddcapacity()
    {   
        $data = $_POST;
        $category = Yii::$app->request->post('category');
        $connection = Yii::$app->db;

        $command = $connection
        ->createCommand("SELECT ssd_capacityid as value,ssd_capacity_value as text FROM iqc_ssd_capacity WHERE ssdid = :category and is_active=1 order by seq_no")
        ->bindValue(":category", $category);
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
    public function actionGetramcapacity()
    {   
        $data = $_POST;
        $ram_type = Yii::$app->request->post('ram_type');
        $connection = Yii::$app->db;

        $command = $connection
        ->createCommand("SELECT ram_capacityid as value,ram_capacity_value as text FROM iqc_ram_capacity WHERE ramtypeid = :ramtypeid and is_active=1 order by seq_no")
        ->bindValue(":ramtypeid", $ram_type);
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
