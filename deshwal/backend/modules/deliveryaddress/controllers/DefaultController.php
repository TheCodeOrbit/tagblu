<?php

namespace backend\modules\deliveryaddress\controllers;

use common\controllers\ModuleController;
use Yii;
use yii\base\Exception;
/**
 * Default controller for the `grn` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout='single';
    public $ModuleName='deliveryaddress';
    public $FieldId='deliveryaddress_id';
    public $TableName='delivery_address';
    public $TabLabel='Delivery Address';
    public $TabId='85';

    public function actionExample()
    {
        return $this->render('index');
    }

    public function actionGetaccount()
    {
         $accid = Yii::$app->request->post('acc');
        $connection = Yii::$app->db;
        $command = $connection->createCommand("
                        SELECT c.account_name,va.acc_name from contracts c
                        join vendor_account va on va.vendoraccid = c.account_name
                          WHERE contract_id = :accid
                    ")->bindValue(":accid", $accid);
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
                'data'=>''
            ]);
        }
    }

        public function actionGetspocdetail(){
        $data = $_POST;
        $contactid = Yii::$app->request->post('contactid');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                         SELECT  mobile,email from contacts
                         WHERE contacts_id = :contactid
                    ")->bindValue(":contactid", $contactid);
        $columns = $command->queryOne();
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

    public function actionGetaddressdetail()
    {
        $data = $_POST;
        $loationid = Yii::$app->request->post('locationid');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                         SELECT address,state_value,city_name,vendor_locations.state,city,pincode from vendor_locations
                         left join state on state.state_id = vendor_locations.state
                         left join city on city.cityid = vendor_locations.city
                         WHERE vendorloc_id = :loationid
                    ")->bindValue(":loationid", $loationid);
        $columns = $command->queryOne();
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
 