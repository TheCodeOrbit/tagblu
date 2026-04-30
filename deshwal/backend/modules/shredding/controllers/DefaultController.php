<?php

namespace backend\modules\shredding\controllers;

use common\controllers\ModuleController;
use backend\models\AccessCheck;
use app\models\EditModel;
use app\models\Shredding;
use app\models\ShreddingAssetDetails;
use app\models\ModtrackerBasic;
use Yii;
use yii\base\Exception;
/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout='single';
    public $ModuleName='shredding';
    public $FieldId='shredding_id';
    public $TableName='shredding';
    public $TabLabel='Shredding';
    public $TabId='5';

    public function actionExample()
    {
        return $this->render('index');
    }
    public function actionGetopportunity()
    {   
        $data = $_POST;
        $record_id = Yii::$app->request->post('opportunity');
        $account_name = "";
        $spoc_name = "";
        $bill_to_location = "";

        $connection = Yii::$app->db;
        $command = $connection
        ->createCommand("SELECT * FROM opportunity WHERE opportunity_id = :record_id and degaussing=:degaussing")
        ->bindValues([":record_id"=> $record_id,":degaussing"=>1]);
        $columns = $command->queryOne();
        if (!empty($columns)) {
             //get vendor name
            $vendor_id = $columns['vendor_account_name'];
            if($vendor_id){
                $command = $connection
                ->createCommand("SELECT acc_name FROM vendor_account WHERE vendoraccid = :vendoraccid")
                ->bindValue(":vendoraccid", $vendor_id);
                $vendrorData = $command->queryOne();
                $account_name = $vendrorData['acc_name']??"";
            }
            
            //get spoc name
            $contact_id = $columns['contact_name'];
            if($contact_id){
                $command = $connection
                ->createCommand("SELECT first_name FROM contacts WHERE contacts_id = :contacts_id")
                ->bindValue(":contacts_id", $contact_id);
                $contactData = $command->queryOne();
                $spoc_name = $contactData['first_name']??"";
            }
            
            // bill to location 
            $bill_location = $columns['bill_location'];
            if($bill_location){
                $command = $connection->createCommand("SELECT vendorloc_id ,vendor_loc_name FROM vendor_locations WHERE vendorloc_id=:vendorloc_id")
                ->bindValues([":vendorloc_id"=>"$bill_location"]);
                $data = $command->queryOne();
                $bill_to_location =  $data && $data["vendor_loc_name"]?$data["vendor_loc_name"]:"";
            }
            return $this->asJson([
                'status' => 'success',
                'data' => [
                    'account_name' => $account_name??"",
                    'spoc_name' => $spoc_name??"",
                    'spoc_mobile' => $columns['contact_mobile']??"",
                    'bill_address' => $columns['bill_address']??"",
                    'bill_location' => $bill_to_location??"",
                    'bill_state' => $columns['bill_state']??"",
                    'bill_pincode' => $columns['bill_pincode']??"",
                    'bill_gstin_no' => $columns['bill_gstin_no']??""
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
    public function actionGetspoc()
    {   
        $data = $_POST;
        $record_id = Yii::$app->request->post('spoc');
        $account_name = "";
        $spoc_name = "";
        $bill_to_location = "";

        $connection = Yii::$app->db;
        $command = $connection
        ->createCommand("SELECT * FROM contacts WHERE contacts_id = :record_id")
        ->bindValues([":record_id"=> $record_id]);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => [
                    'spoc_mobile' => $columns['mobile']??"",
                    'spoc_email' => $columns['email']??"",
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

    public function actionGetspocuser()
    {   
        $data = $_POST;
        $record_id = Yii::$app->request->post('spoc');
        $account_name = "";
        $spoc_name = "";
        $bill_to_location = "";

        $connection = Yii::$app->db;
        $command = $connection
        ->createCommand("SELECT * FROM user WHERE id = :record_id and status=10 and deleted=0")
        ->bindValues([":record_id"=> $record_id]);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => [
                    'spoc_mobile' => $columns['mobile']??"",
                    'spoc_email' => $columns['email']??"",
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

    public function actionGetlocationddress()
    {
        $data = $_POST;
        $location = Yii::$app->request->post('location');
        $sourcing_deal = Yii::$app->request->post('sourcing_deal')??null;
        $connection = Yii::$app->db;
        $bill_to_locations = [];
        $billable_type = "";
        $hdd_count = 0;
        $billing_amount = 0;

        $command = $connection->createCommand("SELECT address,city_name,state_value as state,pincode,gstin_no_uin 
        FROM vendor_locations 
        left join city on city.cityid = vendor_locations.city 
        join state on state.state_id = vendor_locations.state 
        WHERE vendorloc_id = :vendoraccid")->bindValue(":vendoraccid", $location);
        $columns = $command->queryOne();
        if(!empty($sourcing_deal)){
            $command = $connection
            ->createCommand("SELECT * FROM servicedetail WHERE related_to = :related_to and related_to_id=:related_to_id and deleted=0")
            ->bindValue(":related_to", 51)
            ->bindValue(":related_to_id", $sourcing_deal);
            $serviceDetailData = $command->queryOne();
            if(!empty($serviceDetailData)){
                $servicedetail_id = $serviceDetailData['servicedetail_id'];
                $columns["servicedetail_id"] = $servicedetail_id;
                $command = $connection
                ->createCommand("SELECT * FROM servicedetail_details WHERE service_type = 4 and servicedetail_id = :servicedetail_id and service_to_location=:service_to_location and deleted=0")
                ->bindValue(":servicedetail_id", $servicedetail_id)
                ->bindValue(":service_to_location", $location);
                $serviceDetailItems = $command->queryAll();
                foreach($serviceDetailItems as $key=>$val){
                    $bill_to_location = $val["bill_to_location"];
                    if($bill_to_location){
                        $bill_to_locations[] = $bill_to_location;
                    }
                    $billable_type = $val["billable_type"];
                    $quantity_required = (int)$val["qty_required"]??0;
                    $total_exclusive_gst =  (float)$val["total_exclusive_gst"]??0;
                    $billing_amount = $billing_amount + $total_exclusive_gst;
                    $hdd_count =  $hdd_count + $quantity_required;
                }
            }
            $bill_to_locations = implode(",",$bill_to_locations);
        }
        if (!empty($columns)) {
              return $this->asJson([
                    'status' => 'success',
                    'data' => $columns,
                    'billable_type' => $billable_type??null,
                    'hdd_count' => $hdd_count??"",
                    'total_exclusive_gst' => $billing_amount,
                    'bill_to_locations' => $bill_to_locations??""
                ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Vendor Address found.',
                'data'=>''
            ]);
        }

    }
    public function actionWarehouse()
    {
        $data = $_POST;
        $warehouse = Yii::$app->request->post('warehouse');
        $connection = Yii::$app->db;
        $command = $connection->createCommand("SELECT address,city_name,state,pincode FROM  warehouse left join city on city.cityid = warehouse.city WHERE warehouse_id = :warehouse_id")->bindValue(":warehouse_id", $warehouse);
        $columns = $command->queryOne();
        if (!empty($columns)) {
              return $this->asJson([
                    'status' => 'success',
                    'data' => $columns,
                ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No warehouse found.',
                'data'=>''
            ]);
        }

    }
    public function actionGetuserdetails()
    {   
        $data = $_POST;
        $record_id = Yii::$app->request->post('user');
        $account_name = "";
        $spoc_name = "";
        $bill_to_location = "";
        $connection = Yii::$app->db;
        $command = $connection
        ->createCommand("SELECT first_name,last_name,email,mobile FROM user WHERE id = :record_id")
        ->bindValues([":record_id"=> $record_id]);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Contact found.',
                'data'=>''
            ]);
        }
    }
    public function actionGetvendor()
    {   
        $data = $_POST;
        $productItems = [];
        $opportuity_name1 = Yii::$app->request->post('opportuity_name1');
        $connection = Yii::$app->db;

        $hdd_count = 0;
        $billable_type = "";
        $bill_to_location = "";
        $service_to_locations = [];
        $command = $connection->createCommand("SELECT  vendor_account_name,acc_name,vendor_account.billing_type as billing_type FROM sourcingdeal inner join vendor_account on vendor_account.vendoraccid=sourcingdeal.vendor_account_name WHERE sourcingdeal_id = :sourcingdeal_id")
        ->bindValue(":sourcingdeal_id", $opportuity_name1);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            $vendor = $columns;
            // Products /assets
            // first find sourcing deal id than its related product_costing_detail
            $command = $connection
            ->createCommand("SELECT * FROM product_costing WHERE related_to = :related_to and related_to_id=:related_to_id and deleted=0")
            ->bindValue(":related_to", 51)
            ->bindValue(":related_to_id", $opportuity_name1);
            $productCostingData = $command->queryOne();
            if(!empty($productCostingData)){
                $product_costing_id = $productCostingData['product_costing_id'];
                $columns["product_costing_id"] = $product_costing_id;
                $command = $connection
                ->createCommand("SELECT * FROM product_costing_detail WHERE product_costing_id = :product_costing_id and deleted=0")
                ->bindValue(":product_costing_id", $product_costing_id);
                $productItems = $command->queryAll();
                foreach($productItems as $key=>$val){
                    $productid = $val["productid"]??"";
                    // $quantity_required = (int)$val["quantity_required"]??0;
                    // $hdd_count =  $hdd_count + $quantity_required;
                    if($productid){
                        //get product name
                        $command = $connection
                        ->createCommand("SELECT product_name FROM products WHERE products_id = :products_id")
                        ->bindValue(":products_id", $productid);
                        $productData = $command->queryOne();
                        $productItems[$key]['product_name'] = $productData['product_name']??"";
                    }
                }
            }
            $command = $connection
            ->createCommand("SELECT * FROM servicedetail WHERE related_to = :related_to and related_to_id=:related_to_id and deleted=0")
            ->bindValue(":related_to", 51)
            ->bindValue(":related_to_id", $opportuity_name1);
            $serviceDetailData = $command->queryOne();
            if(!empty($serviceDetailData)){
                $servicedetail_id = $serviceDetailData['servicedetail_id'];
                $columns["servicedetail_id"] = $servicedetail_id;
                $command = $connection
                ->createCommand("SELECT * FROM servicedetail_details WHERE service_type = 4 and servicedetail_id = :servicedetail_id and deleted=0")
                ->bindValue(":servicedetail_id", $servicedetail_id);
                $serviceDetailItems = $command->queryAll();
                foreach($serviceDetailItems as $key=>$val){
                    $service_to_location = $val["service_to_location"];
                    if($service_to_location){
                        $service_to_locations[] = $service_to_location;
                    }
                    $quantity_required = (int)$val["qty_required"]??0;
                    $billable_type = $val["billable_type"];
                    $bill_to_location = $val["bill_to_location"];
                    $hdd_count =  $hdd_count + $quantity_required;
                }
            }
            $service_to_locations = implode(",",$service_to_locations);
            
            return $this->asJson([
                'status' => 'success',
                'data' => [
                    'account' => $columns,
                    'service_to_locations' => $service_to_locations,
                    'hdd_count' => $hdd_count,
                    'billable_type' => $billable_type,
                    'bill_to_location' => $bill_to_location,
                    'related' => $productItems??[]
                ]
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Vendor found.',
                'data'=>''
            ]);
        }
    }

    public function actionShreddingcompleted(){
        try{
            $transaction_enabled = false;
            if (!(isset($_POST['shredding_completed']) && $_POST["shredding_completed"] == "Yes")) {
                throw new Exception("Invalid Action");
            }
            $role = Yii::$app->user->identity->role ?? null;
		    $id = Yii::$app->user->id;
            if(empty($id)){
                throw new Exception("Unathorized Access");
            }
            
            $TabId = $this->TabId;
            $FieldId = $this->FieldId;
            $ModuleName = $this->ModuleName;
            $TableName = $this->TableName;
            $TabLabel = $this->TabLabel;

            $Record = $_POST['Recordid'];
            $model = new AccessCheck();
            $tabs = $model->tabs($id, $ModuleName);
            $profile = $model->profile($id, $tabs, $ModuleName);
            $modelaccess = $model->moduleaccess($id, $profile, $tabs);
            $rolebasedrecord = $model->rolebasedrecord($id, $profile);
            $hasadminpower = $model->hasadminpower($profile);
            $actionid = "edit";
            $auditmodel = new EditModel($TableName, $FieldId, $ModuleName, $actionid);

            $transaction = Yii::$app->db->beginTransaction();
            $transaction_enabled = true;
            $oldAttributes = Yii::$app->db->createCommand("select * from `shredding` where shredding_id=:shredding_id")
                ->bindValue(":shredding_id", $Record)
                ->queryOne();
            if (empty($oldAttributes)) {
                throw new Exception("No record found");
            }
            $current_shredding_status = $oldAttributes["shredding_status"];
            $fe_user = $oldAttributes["fe_name"];
            $logistic_user = $oldAttributes["logistic_spoc_name"];
            $hdd_count = (int)$oldAttributes["hdd_count"];
            $hdd_completed = (int)$oldAttributes["hdd_completed"];
            $client_sign = $oldAttributes["image"];
            if(empty($hdd_count)){
                throw new Exception("Invalid action ! HDD Count is not found");
            }
            if($current_shredding_status != 4){
                throw new Exception("Invalid action, current status is not 'Shredding in process/ Activity in process'");
            }
            if($fe_user != $id){
                throw new Exception("You are not the FE of this record");
            }
            if($hdd_count < $hdd_completed){
                throw new Exception("Invalid action, HDD count is less than HDD completed");
            }
            $assets_query = ShreddingAssetDetails::find()->where(['shredding_id' => $Record, 'deleted' => 0]);
            $assets_count = $assets_query->count();
            if($assets_count != $hdd_count){
                throw new Exception("Invalid action, HDD count is not matching with assets count from the 'ASSET DETAILS' section");
            }
            if ($assets_count > 0) {
                $assets_data = $assets_query->all();
                foreach ($assets_data as $ad) {
                    if (empty($ad->laptop_serial_no)) {
                        throw new Exception("'Laptop Serial No' is required for all records in the 'ASSET DETAILS' section");
                    }
                    if (empty($ad->hdd_sdd_serial_no)) {
                        throw new Exception("'Hdd / Sdd Serial No' is required for all records in the 'ASSET DETAILS' section");
                    }
                    if (empty($ad->make)) {
                        throw new Exception("'Make' is required for all records in the 'ASSET DETAILS' section");
                    }
                    if (empty($ad->type)) {
                        throw new Exception("'Type' is required for all records in the 'ASSET DETAILS' section");
                    }
                    if (empty($ad->capacity)) {
                        throw new Exception("'Capacity' is required for all records in the 'ASSET DETAILS' section");
                    }
                    if (empty($ad->software_name)) {
                        throw new Exception("'Software Name' is required for all records in the 'ASSET DETAILS' section");
                    }
                    if (empty($ad->certificate)) {
                        throw new Exception("'Certificate' is required for all records in the 'ASSET DETAILS' section");
                    }
                    if (empty($ad->shredding_date)) {
                        throw new Exception("'Shredding Date' is required for all records in the 'ASSET DETAILS' section");
                    }
                    if (empty($ad->shredding_completed )) {
                        throw new Exception("'Shredding Completed' status is required for all records in the 'ASSET DETAILS' section");
                    }
                    if ($ad->shredding_completed  != 1) {
                        throw new Exception("'Shredding Completed' status has to be 'Yes' for all records in the 'ASSET DETAILS' section");
                    }
                }
            } else {
                throw new Exception("Asset Details are not found for this record");
            }
            if(empty($client_sign)){
                throw new Exception("Client Signature is not uploaded");
            }
            $new_shredding_status = 5;
            $newattributes = array("shredding_status" => $new_shredding_status, "completed_date" => date("Y-m-d"));

            $sql = "UPDATE shredding set shredding_status = :shredding_status,completed_date=:completed_date,modifiedtime = :modifiedtime,modifiedby = :modifiedby where shredding_id = :id";
            $status = Yii::$app->db->createCommand($sql)
                ->bindValue(":shredding_status", $new_shredding_status)
                ->bindValue(":completed_date", date("Y-m-d"))
                ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                ->bindValue(":modifiedby", $id)
                ->bindValue(":id", $Record)
                ->execute();
            $modlog = new ModtrackerBasic();
            $modlog->auditlog($oldAttributes, $newattributes, $ModuleName, $Record, 2, Yii::$app->user->id);
            $transaction->commit();
            $transaction_enabled = false;
            if ($status) {
                return $this->asJson([
                    'status' => 'success',
                    'message' => 'Updated successfully.',
                ]);
            } 
            throw new Exception('Failed to update record');
        }catch(Exception $e){
            if($transaction_enabled) $transaction->rollBack();
            return $this->asJson([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors'=> $e->getMessage()
            ]);
        }
    }
    public function actionShreddingassets(){
        try{
            $transaction_enabled = false;
            if (!(isset($_POST['shredding_asset_details']) && $_POST["shredding_asset_details"] == "Yes")) {
                throw new Exception("Invalid Action");
            }
            $role = Yii::$app->user->identity->role ?? null;
		    $id = Yii::$app->user->id;
            if(empty($id)){
                throw new Exception("Unathorized Access");
            }
            $assets_data = [];
            $TabId = $this->TabId;
            $FieldId = $this->FieldId;
            $ModuleName = $this->ModuleName;
            $TableName = $this->TableName;
            $TabLabel = $this->TabLabel;

            $Record = $_POST['Recordid'];
            $model = new AccessCheck();
            $tabs = $model->tabs($id, $ModuleName);
            $profile = $model->profile($id, $tabs, $ModuleName);
            $modelaccess = $model->moduleaccess($id, $profile, $tabs);
            $rolebasedrecord = $model->rolebasedrecord($id, $profile);
            $hasadminpower = $model->hasadminpower($profile);
            $actionid = "edit";
            $auditmodel = new EditModel($TableName, $FieldId, $ModuleName, $actionid);

            $transaction = Yii::$app->db->beginTransaction();
            $transaction_enabled = true;
            $oldAttributes = Yii::$app->db->createCommand("select * from `shredding` where shredding_id=:shredding_id")
                ->bindValue(":shredding_id", $Record)
                ->queryOne();
            if (empty($oldAttributes)) {
                throw new Exception("No record found");
            }
            $current_shredding_status = $oldAttributes["shredding_status"];
            $fe_user = $oldAttributes["fe_name"];
            $logistic_user = $oldAttributes["logistic_spoc_name"];
            $hdd_count = (int)$oldAttributes["hdd_count"];
            $hdd_completed = (int)$oldAttributes["hdd_completed"];
            $client_sign = $oldAttributes["image"];
            if(empty($hdd_count)){
                throw new Exception("Invalid action ! HDD Count is not found");
            }
            if($current_shredding_status != 4){
                throw new Exception("Invalid action, current status is not 'Shredding in process/ Activity in process'");
            }
            if($fe_user != $id){
                throw new Exception("You are not the FE of this record");
            }
            if($hdd_count < $hdd_completed){
                throw new Exception("Invalid action, HDD count is less than HDD completed");
            }
            $assets_query = ShreddingAssetDetails::find()->where(['shredding_id' => $Record, 'deleted' => 0]);
            $assets_count = $assets_query->count();
            if($assets_count != $hdd_count){
                throw new Exception("Invalid action, HDD count is not matching with assets count from the 'ASSET DETAILS' section");
            }
            if ($assets_count > 0) {
                $assets_data = $assets_query->all();
                foreach ($assets_data as $ad) {
                    if (empty($ad->laptop_serial_no)) {
                        throw new Exception("'Laptop Serial No' is required for all records in the 'ASSET DETAILS' section");
                    }
                    if (empty($ad->hdd_sdd_serial_no)) {
                        throw new Exception("'Hdd / Sdd Serial No' is required for all records in the 'ASSET DETAILS' section");
                    }
                    if (empty($ad->make)) {
                        throw new Exception("'Make' is required for all records in the 'ASSET DETAILS' section");
                    }
                    if (empty($ad->type)) {
                        throw new Exception("'Type' is required for all records in the 'ASSET DETAILS' section");
                    }
                    if (empty($ad->capacity)) {
                        throw new Exception("'Capacity' is required for all records in the 'ASSET DETAILS' section");
                    }
                    if (empty($ad->software_name)) {
                        throw new Exception("'Software Name' is required for all records in the 'ASSET DETAILS' section");
                    }
                    if (empty($ad->certificate)) {
                        throw new Exception("'Certificate' is required for all records in the 'ASSET DETAILS' section");
                    }
                    if (empty($ad->shredding_date)) {
                        throw new Exception("'Shredding Date' is required for all records in the 'ASSET DETAILS' section");
                    }
                    if (empty($ad->shredding_completed )) {
                        throw new Exception("'Shredding Completed' status is required for all records in the 'ASSET DETAILS' section");
                    }
                    if ($ad->shredding_completed  != 1) {
                        throw new Exception("'Shredding Completed' status has to be 'Yes' for all records in the 'ASSET DETAILS' section");
                    }
                }
            } else {
                throw new Exception("Asset Details are not found for this record");
            }
    
            $transaction_enabled = false;
            return $this->asJson([
                'status' => 'success',
                'message' => 'Updated successfully.',
                'data' => $assets_data
            ]);
             
            throw new Exception('Failed to update record');
        }catch(Exception $e){
            if($transaction_enabled) $transaction->rollBack();
            return $this->asJson([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors'=> $e->getMessage()
            ]);
        }
    }

    public function actionShreddingclientsign(){
        try{
            $transaction_enabled = false;
            if (!(isset($_POST['put_client_sign']) && $_POST["put_client_sign"] == "Yes")) {
                throw new Exception("Invalid Action");
            }
            if (!(isset($_POST['image']))) {
                throw new Exception("No data is received for client signature");
            }
            if(empty($_POST['image'])){
                throw new Exception("No data is received in the client signature");
            }
            $signatureData = $_POST["image"];
            $image_data = explode(',', $signatureData);
            if (count($image_data) == 2) {
                $signatureDecodedData = base64_decode($image_data[1]);
            } else {
                throw new Exception("Invalid signature file format!");
            }
            if(empty($image_data[1])){
                throw new Exception("No data is received in the client signature");
            }

            $role = Yii::$app->user->identity->role ?? null;
		    $id = Yii::$app->user->id;
            if(empty($id)){
                throw new Exception("Unathorized Access");
            }
            $assets_data = [];
            $TabId = $this->TabId;
            $FieldId = $this->FieldId;
            $ModuleName = $this->ModuleName;
            $TableName = $this->TableName;
            $TabLabel = $this->TabLabel;

            $Record = $_POST['Recordid'];
            $model = new AccessCheck();
            $tabs = $model->tabs($id, $ModuleName);
            $profile = $model->profile($id, $tabs, $ModuleName);
            $modelaccess = $model->moduleaccess($id, $profile, $tabs);
            $rolebasedrecord = $model->rolebasedrecord($id, $profile);
            $hasadminpower = $model->hasadminpower($profile);
            $actionid = "edit";
            $auditmodel = new EditModel($TableName, $FieldId, $ModuleName, $actionid);

            $transaction = Yii::$app->db->beginTransaction();
            $transaction_enabled = true;
            $oldAttributes = Yii::$app->db->createCommand("select * from `shredding` where shredding_id=:shredding_id")
                ->bindValue(":shredding_id", $Record)
                ->queryOne();
            if (empty($oldAttributes)) {
                throw new Exception("No record found");
            }
            $current_shredding_status = $oldAttributes["shredding_status"];
            $fe_user = $oldAttributes["fe_name"];
            $logistic_user = $oldAttributes["logistic_spoc_name"];
            $hdd_count = (int)$oldAttributes["hdd_count"];
            $hdd_completed = (int)$oldAttributes["hdd_completed"];
            $client_sign = $oldAttributes["image"];
            $shredding_no = $oldAttributes["shredding_no"];
            $random_number = rand(10,99);
            $signature_file_name = "$shredding_no^client_signature_".$random_number.".png";
            if(empty($hdd_count)){
                throw new Exception("Invalid action ! HDD Count is not found");
            }
            if($current_shredding_status != 4){
                throw new Exception("Invalid action, current status is not 'Shredding in process/ Activity in process'");
            }
            if($fe_user != $id){
                throw new Exception("You are not the FE of this record");
            }
            if($hdd_count < $hdd_completed){
                throw new Exception("Invalid action, HDD count is less than HDD completed");
            }
            $assets_query = ShreddingAssetDetails::find()->where(['shredding_id' => $Record, 'deleted' => 0]);
            $assets_count = $assets_query->count();
            if($assets_count != $hdd_count){
                throw new Exception("Invalid action, HDD count is not matching with assets count from the 'ASSET DETAILS' section");
            }
            if ($assets_count > 0) {
                $assets_data = $assets_query->all();
                foreach ($assets_data as $ad) {
                    if (empty($ad->laptop_serial_no)) {
                        throw new Exception("'Laptop Serial No' is required for all records in the 'ASSET DETAILS' section");
                    }
                    if (empty($ad->hdd_sdd_serial_no)) {
                        throw new Exception("'Hdd / Sdd Serial No' is required for all records in the 'ASSET DETAILS' section");
                    }
                    if (empty($ad->make)) {
                        throw new Exception("'Make' is required for all records in the 'ASSET DETAILS' section");
                    }
                    if (empty($ad->type)) {
                        throw new Exception("'Type' is required for all records in the 'ASSET DETAILS' section");
                    }
                    if (empty($ad->capacity)) {
                        throw new Exception("'Capacity' is required for all records in the 'ASSET DETAILS' section");
                    }
                    if (empty($ad->software_name)) {
                        throw new Exception("'Software Name' is required for all records in the 'ASSET DETAILS' section");
                    }
                    if (empty($ad->certificate)) {
                        throw new Exception("'Certificate' is required for all records in the 'ASSET DETAILS' section");
                    }
                    if (empty($ad->shredding_date)) {
                        throw new Exception("'Shredding Date' is required for all records in the 'ASSET DETAILS' section");
                    }
                    if (empty($ad->shredding_completed )) {
                        throw new Exception("'Shredding Completed' status is required for all records in the 'ASSET DETAILS' section");
                    }
                    if ($ad->shredding_completed  != 1) {
                        throw new Exception("'Shredding Completed' status has to be 'Yes' for all records in the 'ASSET DETAILS' section");
                    }
                }
            } else {
                throw new Exception("Asset Details are not found for this record");
            }
    
            $new_attributes = ['image'=>$signature_file_name];
            $result = EditModel::saveGeneratedFiles($image_data[1], $signature_file_name,$Record,'shredding',$oldAttributes, $new_attributes,"image/png");
            if (!$result['success']) {
                throw new Exception($result["message"]); // Return error response
            }
            // File successfully saved, now serve it to user
            $image_id = $result["fileName"];
            if(empty($image_id)){
                throw new Exception("Uploaded file data is not found");
            }

            $sql = "UPDATE shredding set image = :image,modifiedtime = :modifiedtime,modifiedby = :modifiedby where shredding_id = :id";
            $status = Yii::$app->db->createCommand($sql)
                ->bindValue(":image", $image_id)
                ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                ->bindValue(":modifiedby", $id)
                ->bindValue(":id", $Record)
                ->execute();
            $transaction->commit();
            $transaction_enabled = false;
            return $this->asJson([
                'status' => 'success',
                'message' => 'Updated successfully.',
                'data' => $assets_data
            ]);
             
            throw new Exception('Failed to update record');
        }catch(Exception $e){
            if($transaction_enabled) $transaction->rollBack();
            return $this->asJson([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors'=> $e->getMessage()
            ]);
        }
    }

    //code added by ptpatel on to get auto accout name if user come from sourcing deal right side menu
    public function actionGetsourcingdetail(){
        $data = $_POST;
        $sourcingdeal_id = Yii::$app->request->post('sourcingdeal');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                         SELECT  acc_name,vendoraccid,service_to_location,vendor_loc_name  from sourcingdeal sd
                         join sourcingdeal_stage on sourcingdeal_stage.stage_id = sd.stage
                         join vendor_account va on va.vendoraccid = sd.vendor_account_name
                         join servicedetail on servicedetail.related_to = 51 and servicedetail.related_to_id=sd.sourcingdeal_id and servicedetail.deleted = 0
                         join servicedetail_details on servicedetail_details.servicedetail_id = servicedetail.servicedetail_id
                         join vendor_locations on vendor_locations.vendorloc_id = servicedetail_details.service_to_location and vendor_locations.deleted = 0
                         WHERE sourcingdeal_id = :sourcingdeal_id and service_type=4
                    ")->bindValue(":sourcingdeal_id", $sourcingdeal_id);
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
    //end code added by ptpatel
}
