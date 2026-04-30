<?php

namespace backend\modules\gateinward\controllers;

use common\controllers\ModuleController;
use Yii;
use yii\base\Exception;
/**
 * Default controller for the `gateinward` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout='multiple';
    public $ModuleName='gateinward';
    public $FieldId='gateinward_id';
    public $TableName='gateinward';
    public $TabLabel='Gateinward';
    public $TabId='64';

    public function actionExample()
    {
        return $this->render('index');
    }
    public function getAccountName($connection,$account_id){
        if(empty($account_id)) return "";
        $command = $connection
        ->createCommand("SELECT acc_name FROM vendor_account WHERE vendoraccid = :vendoraccid")
        ->bindValue(":vendoraccid", $account_id);
        $data = $command->queryOne();
        if(empty($data)) $account_id;
        return $data['acc_name']??"";
    }
    public function getShipmentMode($connection,$mode){
        if(empty($mode)) return "";
        $command = $connection
        ->createCommand("SELECT value FROM pickup_shippment_mode WHERE id = :id")
        ->bindValue(":id", $mode);
        $data = $command->queryOne();
        if(empty($data)) $mode;
        return $data['value']??"";
    }
    public function actionPickupassets()
    {
        try{
            $pickeupItems = [];
            $docket_number = Yii::$app->request->post('docket_number');
            if(empty($docket_number)){
                throw new Exception("Docket Number is required");
            }
            $connection = Yii::$app->db;
            // pickup completed status == 6
            $command = $connection->createCommand("SELECT shipped_details.*,pickup.account_name,pickup_no FROM shipped_details inner join pickup on shipped_details.pickup_id=pickup.pickup_id 
            WHERE shipped_details.docket_number = :docket_number and shipped_details.deleted=0 and shipped_details.status=2")
            ->bindValue(":docket_number", $docket_number);
            $columns = $command->queryAll();
            $count = $command->queryScalar();
            if($count == 0){
                throw new Exception("No data found for Docket Number: $docket_number");
            }

            foreach($columns as $col){
                $transporter_name = $col["transporter_name"]??"";
                $account_name = $col["account_name"]??"";
                $shippment_mode = $col["shippment_mode"]??"";
                $vehicle_number = $col["vehicle_number"]??"";
                $shipped_date = $col["shipped_date"]??"";
                $pickup_id = $col["pickup_id"]??"";
                $pickup_no = $col['pickup_no']??"";
                $transporter_name = $this->getAccountName($connection,$transporter_name);
                $account_name = $this->getAccountName($connection,$account_name);
                $shippment_mode = $this->getShipmentMode($connection,$shippment_mode);
                $pickeupItems[] = [
                    "transporter_name"=>$transporter_name,
                    "account_name"=>$account_name,
                    "shippment_mode"=>$shippment_mode,
                    "vehicle_number"=>$vehicle_number,
                    "shipped_date"=>$shipped_date,
                    "pickup_id"=>$pickup_id,
                    "pickup_no"=>$pickup_no
                ];
            }
                
            return $this->asJson([
                'status' => 'success',
                'message' => 'OK',
                'data' => $pickeupItems
            ]);
        }catch(Exception $e){
            return $this->asJson([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => $e->getMessage(),
                'data'=>''
            ]);
        }
    }
}
