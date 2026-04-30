<?php

namespace backend\modules\generatepi\controllers;

use common\controllers\ModuleController;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\SalesOrder;
/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'single';
    public $ModuleName = 'generatepi';
    public $FieldId = 'generatepi_id';
    public $TableName = 'generate_pi';
    public $TabLabel = 'Generate PI';
    public $TabId = '98';
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

    public function actionGetsalesorderdetails()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $so_id = Yii::$app->request->post('so_number');

        $order = SalesOrder::find()
            ->where(['salesorder_id' => $so_id, 'deleted' => 0])
            ->with([
                'billVendor',        // relation for bill_vendor_location
                'shipVendor',        // relation for ship_vendor_location
                'billWarehouse',     // relation for bill_wh_location
                'shipWarehouse',     // relation for ship_wh_location
                'vendorAccount'      // relation for vendor_name
            ])
            ->one();

        if (!$order) {
            return [
                'status' => 'error',
                'message' => 'Order not found'
            ];
        }

        $products = \app\models\SalesorderItemsDetail::find()
            ->where(['salesorder_id' => $so_id])
            ->asArray()
            ->all();

        $orderArr = $order->toArray();

        // bill_vendor_loc_name
        $orderArr['bill_vendor_loc_name'] = $order->billVendor ? $order->billVendor->vendor_loc_name : null;

        // ship_vendor_loc_name
        $orderArr['ship_vendor_loc_name'] = $order->shipVendor ? $order->shipVendor->vendor_loc_name : null;

        // bill_warehouse_name
        $orderArr['bill_warehouse_name'] = $order->billWarehouse ? $order->billWarehouse->warehouse_name : null;

        // ship_warehouse_name
        $orderArr['ship_warehouse_name'] = $order->shipWarehouse ? $order->shipWarehouse->warehouse_name : null;

        // vendor_acc_name
        $orderArr['vendor_acc_name'] = $order->vendorAccount ? $order->vendorAccount->acc_name : null;

        $orderArr['products'] = $products ?: [];
      
        return [
            'status' => 'success',
            'data' => $orderArr
        ];
    }



    
}
