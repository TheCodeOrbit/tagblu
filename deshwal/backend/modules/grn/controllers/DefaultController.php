<?php

namespace backend\modules\grn\controllers;

use common\controllers\ModuleController;
use Yii;
use yii\base\Exception;
/**
 * Default controller for the `grn` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout='multiple';
    public $ModuleName='grn';
    public $FieldId='grn_id';
    public $TableName='grn';
    public $TabLabel='Grn';
    public $TabId='32';

    public function actionExample()
    {
        return $this->render('index');
    }
    public function actionGetpurchaseorder()
    {   
        $data = $_POST;
        $record_id = Yii::$app->request->post('record_id');
        $connection = Yii::$app->db;
        $command = $connection
        ->createCommand("SELECT * FROM purchase_order WHERE purchase_order_id = :record_id")
        ->bindValue(":record_id", $record_id);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            $vendor_id = $columns['vendor_name'];
            if($vendor_id){
                //get vendor name
                $command = $connection
                ->createCommand("SELECT acc_name FROM vendor_account WHERE vendoraccid = :vendoraccid")
                ->bindValue(":vendoraccid", $vendor_id);
                $vendrorData = $command->queryOne();
                $columns['vendor_name'] = $vendrorData['acc_name']??"";
            }
            // purchase order items
            $command = $connection
            ->createCommand("SELECT * FROM purchase_order_itemsdetail WHERE purchase_order_id = :record_id")
            ->bindValue(":record_id", $record_id);
            $purchaseOrderItems = $command->queryAll();
            foreach($purchaseOrderItems as $key=>$val){
                $product_name = $val["product_name"]??"";
                if($product_name){
                    //get product name
                    $command = $connection
                    ->createCommand("SELECT product_name FROM products WHERE products_id = :products_id")
                    ->bindValue(":products_id", $product_name);
                    $productData = $command->queryOne();
                    $purchaseOrderItems[$key]['product_name'] = $productData['product_name']??"";
                }
            }
            return $this->asJson([
                'status' => 'success',
                'data' => [
                    'master' => $columns,
                    'related' => $purchaseOrderItems??[]
                ]
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Contact found.',
                'data'=>''
            ]);
        }
    }

    public function actionGetpickupdata()
    {   
        try{
            $record_id = Yii::$app->request->post('record_id');
            if(empty($record_id)){
                throw new Exception("Invalid Request. Pickup ID is required");
            }
            $account_name = "";
            $location = "";
            $fe_name = "";
            $cs_spoc = "";
            $logistics_user = "";
            $connection = Yii::$app->db;
            $command = $connection
            ->createCommand("SELECT * FROM pickup WHERE pickup_id = :record_id and deleted=0")
            ->bindValue(":record_id", $record_id);
            $columns = $command->queryOne();
            if(empty($columns)){
                throw new Exception("No valid data is found for this Pickup ID");
            }
            $pickup_id = $columns['pickup_id']??"";
            $account_id = $columns['account_name']??"";
            $location_id = $columns['delivery_location']??"";
            $fe_id = $columns['fe_name']??"";
            $cs_spoc_id = $columns['creatorid']??"";
            $logistics_user_id = $columns['logistic_user']??"";

            if($account_id){
                $account_name = $this->getAccountName($connection,$account_id);
            }
            if($location_id){
                $location = $this->getLocationName($connection,$location_id);
            }

            if($fe_id){
                $fe_name = $this->getUserName($connection,$fe_id);
            }
            if($cs_spoc_id){
                $cs_spoc = $this->getUserName($connection,$cs_spoc_id);
            }
            if($logistics_user_id){
                $logistics_user = $this->getUserName($connection,$logistics_user_id);
            }

            $shipped_details = [];
            $document_details = [];
            $product_details = [];

            if($pickup_id){
                $command = $connection
                ->createCommand("SELECT * FROM shipped_details WHERE pickup_id = :record_id and deleted=0")
                ->bindValue(":record_id", $pickup_id);
                $shipped_details = $command->queryAll();
                if($shipped_details && count($shipped_details)){
                    foreach($shipped_details as $key=>$val){
                        $shipped_details[$key]['transporter_name_value'] = $this->getAccountName($connection,$val["transporter_name"]);
                    }
                }

                $command = $connection
                ->createCommand("SELECT * FROM pickup_document_details WHERE pickup_id = :record_id and deleted=0")
                ->bindValue(":record_id", $pickup_id);
                $document_details = $command->queryAll();

                $command = $connection
                ->createCommand("SELECT * FROM pickup_asset_detail WHERE pickup_id = :record_id and deleted=0")
                ->bindValue(":record_id", $pickup_id);
                $product_details = $command->queryAll();
                foreach($product_details as $key=>$val){
                    $product_id_from_pickup = $val["porduct_name"]??"";
                    if($product_id_from_pickup){
                        //get product name
                        $command = $connection
                        ->createCommand("SELECT product_name FROM products WHERE products_id = :products_id")
                        ->bindValue(":products_id", $product_id_from_pickup);
                        $productData = $command->queryOne();
                        $product_details[$key]['product_name_grn'] = $productData['product_name']??"";
                    }else{
                        $product_details[$key]['product_name_grn'] = "";
                    }
                }
            }
            
            return $this->asJson([
                'status' => 'success',
                'data' => [
                    'master' =>[
                        'account_id' => $account_id,
                        'account_name' => $account_name,
                        'location_id' => $location_id,
                        'location' => $location,
                        'fe_id' => $fe_id,
                        'fe_name' => $fe_name,
                        'cs_spoc_id' => $cs_spoc_id,
                        'cs_spoc' => $cs_spoc,
                        'logistics_user_id' => $logistics_user_id,
                        'logistics_user' => $logistics_user,
                    ],
                    'shipped_details' => $shipped_details??[],
                    'document_details' => $document_details??[],
                    'product_details' => $product_details??[],
                ]
            ]);
        }catch (Exception $e) {
            return $this->asJson([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => $e->getMessage(),
                'data'=>''
            ]);
        }
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

    public function getLocationName($connection,$location_id){
        if(empty($location_id)) return "";
        // $location_query = $connection->createCommand("SELECT * FROM vendor_locations WHERE vendorloc_id = :vendorloc_id")
        // ->bindValues([":vendorloc_id"=> $location_id]);
        //added on 27 oct 2025 by deepika
        $location_query = $connection->createCommand("SELECT warehouse_name FROM warehouse WHERE warehouse_id = :warehouse_id")
        ->bindValues([":warehouse_id"=> $location_id]);
        $location_data = $location_query->queryOne();
        if(empty($location_data)) return "";
        // $location_name = $location_data["vendor_loc_name"]??"";
        $location_name = $location_data["warehouse_name"]??"";
        return $location_name;
    }

    public function getUserName($connection,$user_id)
    {   
        $command = $connection
        ->createCommand("SELECT concat(first_name,' ',last_name) as name FROM user WHERE id = :id")
        ->bindValues([":id"=> $user_id]);
        $data = $command->queryOne();
        if(empty($data)) return "";
        $name = $data["name"]??"";
        return $name;
    }
}
