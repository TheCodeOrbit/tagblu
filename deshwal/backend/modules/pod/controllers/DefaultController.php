<?php

namespace backend\modules\pod\controllers;

use common\controllers\ModuleController;
use Yii;
use yii\db\Query;

/**
 * Default controller for the `pod` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'single';
    public $ModuleName = 'pod';
    public $FieldId = 'pod_id';
    public $TableName = 'pod';
    public $TabLabel = 'POD';
    public $TabId = '100';


    public function actionGetvendorname()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $soId = Yii::$app->request->post('so_number');
        $data = null;

        $salesOrder = \app\models\SalesOrder::findOne($soId);

        if ($salesOrder) {
            $vendorName = $salesOrder->vendor_name;

            $accName = null;
            if ($salesOrder->vendorAccount) {
                $accName = $salesOrder->vendorAccount->acc_name;
            }

            return [
                'status' => 'success',
                'vendor_id' => $vendorName,
                'vendor_name' => $accName
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Sales Order not found'
            ];
        }
    }


  
}
