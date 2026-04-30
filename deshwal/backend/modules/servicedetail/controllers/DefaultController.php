<?php

namespace backend\modules\servicedetail\controllers;

use app\models\Servicemaster;
use app\models\VendorLocations;
use app\models\Warehouse;
use common\controllers\ModuleController;
use Yii;
use yii\web\Response;

/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'multiple';
    public $ModuleName = 'servicedetail';
    public $FieldId = 'servicedetail_id';
    public $TableName = 'servicedetail';
    public $TabLabel = 'Service Detail';
    public $ChildTableName = 'servicedetail_details';
    public $ChildFieldId = 'servicedetail_detail_id';


    public $TabId = '55';
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


    public function actionGetserviceinfo()
    {
        $data = $_POST;
        $service_type = Yii::$app->request->post('service_type');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                        SELECT  service_description,gst_percentage,hsn_code,uom,cost_price, service_category_value as category, service_sub_category_value as sub_category, service_uom_value FROM `servicemaster` 
                        left join service_category  on service_category.service_categoryid = servicemaster.category
                        left join service_sub_category  on service_sub_category.service_sub_categoryid = servicemaster.sub_category
                        left join service_uom on service_uom.service_uomid = servicemaster.uom
                        left join gst_percent on gst_percent.gst_percent_id = servicemaster.gst_percentage
                          WHERE servicemaster_id = :servicemaster_id
                    ")->bindValue(":servicemaster_id", $service_type);
        $columns = $command->queryOne();
        if (!empty($columns)) {


            return $this->asJson([
                'status' => 'success',
                'data' => $columns,

            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Product info found.',
                'data' => ''
            ]);
        }
    }


    public function actionGetvendor()
    {
        $data = $_POST;
        $related_to = Yii::$app->request->post('related_to');
        $related_to_id = Yii::$app->request->post('related_to_id');
        $connection = Yii::$app->db;
        if ($related_to == 8) {
            //fetch from opportunity
            $sql = "select vendor_account_name as vendor,acc_name as vendorname from opportunity join vendor_account on getvendor.vendoraccid = opportunity.vendor_account_name  where opportunity_id=:related_to_id";
        } else if ($related_to == 51) {
            //fetch from sourcingdeal
            $sql = "select vendor_account_name as vendor,acc_name as vendorname from sourcingdeal join vendor_account on vendor_account.vendoraccid = sourcingdeal.vendor_account_name  where sourcingdeal_id=:related_to_id";
        }

        $command = $connection->createCommand($sql)->bindValue(":related_to_id", $related_to_id);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Product info found.',
                'data' => ''
            ]);
        }
    }


    public function actionGetlocationstates()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $bill_to_location = Yii::$app->request->post('bill_to_location');
        $bill_from_warehouse = Yii::$app->request->post('bill_from_warehouse');
        $serviceType = Yii::$app->request->post('service_type');

        if (!$bill_to_location || !$bill_from_warehouse) {
            return ['success' => false, 'message' => 'Invalid warehouse selection'];
        }

        // $billState = (int)VendorLocations::find()->select('state')->where(['vendorloc_id' => $billLocation])->scalar();
        // $shipState = (int)VendorLocations::find()->select('state')->where(['vendorloc_id' => $shipLocation])->scalar();
        $bill_to_location = (int)VendorLocations::find()->select('state')->where(['vendorloc_id' => $bill_to_location])->scalar();
        $bill_from_warehouse = (int)Warehouse::find()->select('stateid')->where(['warehouse_id' => $bill_from_warehouse])->scalar();
        


        $service = Servicemaster::find()
            ->alias('s')
            ->select(['g.gst_percent_value'])
            ->innerJoin('gst_percent g', 's.gst_percentage = g.gst_percent_id')
            ->where(['s.servicemaster_id' => $serviceType])
            ->asArray()
            ->one();

        if ($bill_to_location && $bill_from_warehouse) {
            return [
                'success' => true,
                'billState' => $bill_to_location,
                'shipState' => $bill_from_warehouse,
                'gst_percentage' => $service['gst_percent_value'],
            ];
        } else {
            return ['success' => false, 'message' => 'Warehouse not found'];
        }
    }

    public function actionGetbaseprice()
    {
        $data = $_POST;
        $service_type = Yii::$app->request->post('service_type');
        $quantity_required = Yii::$app->request->post('qty_required');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                        SELECT LOWER(service_name) AS service_name from servicemaster
                          WHERE servicemaster_id = :servicemaster_id
                    ")->bindValue(":servicemaster_id", $service_type);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            $service_name = trim($columns['service_name']);
            $service_name = str_replace(' ', '', $service_name);
            if($service_name != 'pickup')
            $tble = $service_name . "_calculator";
            else $tble = '';
            
            //check if table exist
            if (!empty($tble) && Yii::$app->db->schema->getTableSchema($tble, true) !== null) {
                //get base price
                // echo $quantity_required;
                $sql = "select `base_price` from $tble where :quantity_required between from_range and max_count";
                $res = Yii::$app->db->createCommand($sql)->bindValue(":quantity_required", $quantity_required)->queryOne();
                // print_r($res);die;
                if ($res) {
                    $columns['base_price'] = $res['base_price'];
                }
            } else {
                $columns['base_price'] = '';
            }

            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Product info found.',
                'data' => ''
            ]);
        }
    }
}
