<?php

namespace backend\modules\focdit\controllers;

use common\controllers\ModuleController;
use Yii;

/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'single';
    public $ModuleName = 'focdit';
    public $FieldId = 'focdit_id';
    public $TableName = 'foc_dit';
    public $TabLabel = 'Foc DevIT';
    public $TabId = '91';
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

    public function actionGetcustomerdetail()
    {
        $contacts_id = Yii::$app->request->get('customer_name');
        $connection = Yii::$app->db;
        $command = $connection->createCommand("
                            SELECT mobile from contacts 
                          WHERE contacts_id = :contacts_id
                    ")->bindValue(":contacts_id", $contacts_id);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Customer info found.',
                'data' => ''
            ]);
        }
    }

    public function actionGetproductdetail()
    {
        $Recordid = Yii::$app->request->get('product_name');
        $connection = Yii::$app->db;
        $command = $connection->createCommand("
                SELECT hsn_code,product_description,gst_percentage FROM product_dit 
                    WHERE productdit_id = :Recordid
            ")->bindValue(":Recordid", $Recordid);

        $columns = $command->queryOne();

        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Contact found.',
                'data'=>''
            ]);
        }
    }

}
