<?php

namespace backend\modules\vehicleloading\controllers;

use common\controllers\ModuleController;
use Error;
use Yii;
use yii\db\Query;

/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'single';
    public $ModuleName = 'vehicleloading';
    public $FieldId = 'vehicleloading_id';
    public $TableName = 'vehicle_loading';
    public $TabLabel = 'Vechicle Loading';
    public $TabId = '102';

    //  public function beforeAction($action)
    // {
    //     $this->layout = '@app/views/layouts/main';  // Set the main layout before each action
    //     return parent::beforeAction($action);
    // }

    public function actionGetgeneratepidetails()
    {
        $salesOrderId = Yii::$app->request->post('so_number');
        $result = [];
        $products = [];
        if ($salesOrderId) {
            $connection = Yii::$app->db;


            $sql = "SELECT pi.po_number,DATE_FORMAT(pi.po_date, '%d-%m-%Y') as po_date,so.salesorder_id,so.vendor_name AS account_id,vc.acc_name AS account_name,so.payment_terms FROM sales_order AS so INNER JOIN generate_pi AS pi INNER JOIN vendor_account vc ON vc.vendoraccid = so.vendor_name WHERE so.salesorder_id = :salesorder_id LIMIT 1";
            $command = $connection->createCommand($sql);
            $command->bindValue(':salesorder_id', $salesOrderId);
            $row = $command->queryOne();
            if($row){
                $sqlItem = "SELECT pr.product_name,soi.category,soi.sub_category,soi.qty_in_stock,soi.qty FROM salesorder_items_detail soi INNER JOIN sales_order s ON s.salesorder_id = soi.salesorder_id INNER JOIN products pr ON pr.products_id = soi.product_name WHERE soi.salesorder_id =:saoi";
                $command = $connection->createCommand($sqlItem);
                $command->bindValue("saoi", $salesOrderId);
                $products = $command->queryAll();
            }
                $result['po_number'] = isset($row['po_number']) ? $row['po_number'] : '';
                $result['po_date'] = isset($row['po_date']) ? $row['po_date'] : '';
                $result['payment_terms'] = isset($row['payment_terms']) ? $row['payment_terms'] : '';
                $result['account_name'] = isset($row['account_name']) ? $row['account_name'] : '';
                $result['account_id'] = isset($row['account_id']) ? $row['account_id'] : '';
                $result['products'] = isset($products) ? $products : '';
        }   
        

        return $this->asJson([
            'status' => 'success',
            'data' =>  $result
        ]);
    }

}
