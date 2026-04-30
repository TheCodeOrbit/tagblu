<?php

namespace backend\modules\datawiping\controllers;

use app\models\Attachments;
use common\controllers\ModuleController;
use backend\models\AccessCheck;
use app\models\EditModel;
use app\models\DataWiping;
use app\models\DataWipingAssetDetails;
use app\models\ModtrackerBasic;
use DateTime;
use Yii;
use yii\base\Exception;
use yii\helpers\FileHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout='single';
    public $ModuleName='datawiping';
    public $FieldId='datawiping_id';
    public $TableName='data_wiping';
    public $TabLabel='Data Wiping';
    public $TabId='6';

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
        left join state on state.state_id = vendor_locations.state 
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
                ->createCommand("SELECT * FROM servicedetail_details WHERE service_type = 5 and servicedetail_id = :servicedetail_id and service_to_location=:service_to_location and deleted=0")
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
        $command = $connection->createCommand("SELECT address,city_name,state,pincode FROM  warehouse  left join city on city.cityid = warehouse.city WHERE warehouse_id = :warehouse_id")->bindValue(":warehouse_id", $warehouse);
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
                ->createCommand("SELECT * FROM servicedetail_details WHERE service_type = 5 and servicedetail_id = :servicedetail_id and deleted=0")
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

    public function actionDatawipingcompleted(){
        try{
            $transaction_enabled = false;
            if (!(isset($_POST['wiping_completed']) && $_POST["wiping_completed"] == "Yes")) {
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
            $oldAttributes = Yii::$app->db->createCommand("select * from `data_wiping` where datawiping_id=:datawiping_id")
                ->bindValue(":datawiping_id", $Record)
                ->queryOne();
            if (empty($oldAttributes)) {
                throw new Exception("No record found");
            }
            $current_wiping_status = $oldAttributes["wiping_status"];
            $fe_user = $oldAttributes["fe_name"];
            $logistic_user = $oldAttributes["logistic_spoc_name"];
            $hdd_count = (int)$oldAttributes["hdd_count"];
            $hdd_completed = (int)$oldAttributes["hdd_completed"];
            $client_sign = $oldAttributes["image"];
            if(empty($hdd_count)){
                throw new Exception("Invalid action ! HDD Count is not found");
            }
            if($current_wiping_status != 4){
                throw new Exception("Invalid action, current status is not 'Data Wiping in process/ Activity in process'");
            }
            if($fe_user != $id){
                throw new Exception("You are not the FE of this record");
            }
            if($hdd_count < $hdd_completed){
                throw new Exception("Invalid action, HDD count is less than HDD completed");
            }
            $assets_query = DataWipingAssetDetails::find()->where(['datawiping_id' => $Record, 'deleted' => 0]);
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
                    if (empty($ad->wiping_date)) {
                        throw new Exception("'Wiping Date' is required for all records in the 'ASSET DETAILS' section");
                    }
                    if (empty($ad->wiping_completed)) {
                        throw new Exception("'Wiping Completed' status is required for all records in the 'ASSET DETAILS' section");
                    }
                    if ($ad->wiping_completed != 1) {
                        throw new Exception("'Wiping Completed' status has to be 'Yes' for all records in the 'ASSET DETAILS' section");
                    }
                }
            } else {
                throw new Exception("Asset Details are not found for this record");
            }
            if(empty($client_sign)){
                throw new Exception("Client Signature is not uploaded");
            }
            $new_wiping_status = 5;
            $newattributes = array("wiping_status" => $new_wiping_status, "completed_date" => date("Y-m-d"));

            $sql = "UPDATE data_wiping set wiping_status = :wiping_status,completed_date=:completed_date,modifiedtime = :modifiedtime,modifiedby = :modifiedby where datawiping_id = :id";
            $status = Yii::$app->db->createCommand($sql)
                ->bindValue(":wiping_status", $new_wiping_status)
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
    public function actionDatawipingassets(){
        try{
            $transaction_enabled = false;
            if (!(isset($_POST['wiping_asset_details']) && $_POST["wiping_asset_details"] == "Yes")) {
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
            $oldAttributes = Yii::$app->db->createCommand("select * from `data_wiping` where datawiping_id=:datawiping_id")
                ->bindValue(":datawiping_id", $Record)
                ->queryOne();
            if (empty($oldAttributes)) {
                throw new Exception("No record found");
            }
            $current_wiping_status = $oldAttributes["wiping_status"];
            $fe_user = $oldAttributes["fe_name"];
            $logistic_user = $oldAttributes["logistic_spoc_name"];
            $hdd_count = (int)$oldAttributes["hdd_count"];
            $hdd_completed = (int)$oldAttributes["hdd_completed"];
            $client_sign = $oldAttributes["image"];
            if(empty($hdd_count)){
                throw new Exception("Invalid action ! HDD Count is not found");
            }
            if($current_wiping_status != 4){
                throw new Exception("Invalid action, current status is not 'Data Wiping in process/ Activity in process'");
            }
            if($fe_user != $id){
                throw new Exception("You are not the FE of this record");
            }
            if($hdd_count < $hdd_completed){
                throw new Exception("Invalid action, HDD count is less than HDD completed");
            }
            $assets_query = DataWipingAssetDetails::find()->where(['datawiping_id' => $Record, 'deleted' => 0]);
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
                    if (empty($ad->wiping_date)) {
                        throw new Exception("'Wiping Date' is required for all records in the 'ASSET DETAILS' section");
                    }
                    if (empty($ad->wiping_completed)) {
                        throw new Exception("'Wiping Completed' status is required for all records in the 'ASSET DETAILS' section");
                    }
                    if ($ad->wiping_completed != 1) {
                        throw new Exception("'Wiping Completed' status has to be 'Yes' for all records in the 'ASSET DETAILS' section");
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

    public function actionDatawipingclientsign(){
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
            $oldAttributes = Yii::$app->db->createCommand("select * from `data_wiping` where datawiping_id=:datawiping_id")
                ->bindValue(":datawiping_id", $Record)
                ->queryOne();
            if (empty($oldAttributes)) {
                throw new Exception("No record found");
            }
            $current_wiping_status = $oldAttributes["wiping_status"];
            $fe_user = $oldAttributes["fe_name"];
            $logistic_user = $oldAttributes["logistic_spoc_name"];
            $hdd_count = (int)$oldAttributes["hdd_count"];
            $hdd_completed = (int)$oldAttributes["hdd_completed"];
            $client_sign = $oldAttributes["image"];
            $data_wiping_no = $oldAttributes["data_wiping_no"];
            $random_number = rand(10,99);
            $signature_file_name = "$data_wiping_no^client_signature_".$random_number.".png";
            if(empty($hdd_count)){
                throw new Exception("Invalid action ! HDD Count is not found");
            }
            if($current_wiping_status != 4){
                throw new Exception("Invalid action, current status is not 'Data Wiping in process/ Activity in process'");
            }
            if($fe_user != $id){
                throw new Exception("You are not the FE of this record");
            }
            if($hdd_count < $hdd_completed){
                throw new Exception("Invalid action, HDD count is less than HDD completed");
            }
            $assets_query = DataWipingAssetDetails::find()->where(['datawiping_id' => $Record, 'deleted' => 0]);
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
                    if (empty($ad->wiping_date)) {
                        throw new Exception("'Wiping Date' is required for all records in the 'ASSET DETAILS' section");
                    }
                    if (empty($ad->wiping_completed)) {
                        throw new Exception("'Wiping Completed' status is required for all records in the 'ASSET DETAILS' section");
                    }
                    if ($ad->wiping_completed != 1) {
                        throw new Exception("'Wiping Completed' status has to be 'Yes' for all records in the 'ASSET DETAILS' section");
                    }
                }
            } else {
                throw new Exception("Asset Details are not found for this record");
            }
    
            $new_attributes = ['image'=>$signature_file_name];
            //Removed _ from the module name 4th params by vishwas 05-02-2026
            $result = EditModel::saveGeneratedFiles($image_data[1], $signature_file_name,$Record,'datawiping',$oldAttributes, $new_attributes,"image/png");
            if (!$result['success']) {
                throw new Exception($result["message"]); // Return error response
            }
            // File successfully saved, now serve it to user
            $image_id = $result["fileName"];
            if(empty($image_id)){
                throw new Exception("Uploaded file data is not found");
            }

            $sql = "UPDATE data_wiping set image = :image,modifiedtime = :modifiedtime,modifiedby = :modifiedby where datawiping_id = :id";
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
                         WHERE sourcingdeal_id = :sourcingdeal_id and service_type=5
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

     /*** 
     * Download sample CSV file
     */
     public function actionDownloadsample()
    {
        $filePath = \Yii::getAlias('@webroot/thememain/samples/bulk_upload_DW.csv');
        $fileName = 'bulk_upload_DW.csv';

        if (!file_exists($filePath)) {
            throw new \yii\web\NotFoundHttpException("Sample file not found.");
        }

        return \Yii::$app->response->sendFile($filePath, $fileName, [
            'mimeType' => 'text/csv',
            'inline' => false,
        ]);
    }

    public function actionUploadzipfiles()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $zip = UploadedFile::getInstanceByName("certificate_zip");
        if (!$zip) {
            return ["status" => "error", "message" => "ZIP file not received"];
        }

        $extractPath = Yii::getAlias("@runtime/zip_" . time());
        mkdir($extractPath, 0777, true);
        $zipTempPath = $extractPath . "/" . $zip->name;
        $zip->saveAs($zipTempPath);

        $zipArchive = new \ZipArchive();
        if ($zipArchive->open($zipTempPath) !== TRUE) {
            return ["status" => "error", "message" => "Cannot open ZIP file"];
        }

        $zipArchive->extractTo($extractPath);
        $zipArchive->close();

        $fileMap = [];

        foreach (scandir($extractPath) as $file) {
            if ($file === "." || $file === "..") continue;
            if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) === "zip") continue;
            $fullPath = $extractPath . "/" . $file;
            if (!is_file($fullPath)) continue;

            $uploadedFile = new \yii\web\UploadedFile([
                'name' => $file,
                'tempName' => $fullPath,
                'type' => mime_content_type($fullPath),
                'size' => filesize($fullPath),
                'error' => UPLOAD_ERR_OK
            ]);
            $result = $this->saveAttachedFiles($uploadedFile);
            if ($result['success']) {
                $fileMap[$file] = $result["fileName"];
            }
        }

        return [
            "status" => "success",
            "files" => $fileMap 
        ];
    }

   
    public function saveAttachedFiles($file)
    {
        if (empty($file)) {
            return ['success' => false, 'message' => 'No file received'];
        }

        $maxFileSize = 5 * 1024 * 1024 * 1024;

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'doc', 'docx', 'pdf', 'xls', 'xlsx', 'zip', 'eml','msg'];
        $allowedMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/pdf',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            "message/rfc822",
            "application/vnd.ms-outlook",
            'application/zip',
            'multipart/x-zip',
            'application/x-compressed',
            "application/x-zip-compressed","application/octet-stream"
        ];

        $ext = strtolower(pathinfo($file->name, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExtensions)) {
            return ['success' => false, 'message' => "Invalid extension: $ext"];
        }

        $year = date('Y');
        $month = date('m');
        $week = date('W');

        $baseUploadPath = Yii::getAlias('@webroot/uploads');
        $targetDir = "$baseUploadPath/$year/$month/week_$week/";

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $uniqueName = uniqid() . ".$ext";
        $targetFullPath = $targetDir . $uniqueName;
        $dbPath = "uploads/$year/$month/week_$week/$uniqueName";

        $saved = false;

        if ($file instanceof \yii\web\UploadedFile && is_uploaded_file($file->tempName)) {
            $saved = $file->saveAs($targetFullPath);
        }else {
            $saved = copy($file->tempName, $targetFullPath);
        }

        if (!$saved) {
            return ['success' => false, 'message' => 'Failed to save file to target path'];
        }

        $model = new Attachments();
        $model->name = $file->name;
        $model->type = $file->type;
        $model->path = $dbPath;
        $model->storedname = $uniqueName;

        if ($model->save()) {
            return [
                'success' => true,
                'fileName' => $model->attachmentsid
            ];
        }

        return ['success' => false, 'message' => 'DB save failed'];
    }

    public function actionDocumentation()
    {
        $request  = Yii::$app->request;
        $mode     = $request->post('mode', $request->get('mode', 'download'));
        $basePath = Yii::getAlias('@webroot/thememain/docs/');
        $fileName = 'Datawiping.docx';
        $fullPath = $basePath . $fileName;

        if ($mode === 'upload') {
            $file = \yii\web\UploadedFile::getInstanceByName('documentation');
            if (!$file) {
                return $this->asJson(['success' => false, 'message' => 'No file received']);
            }

            if (!is_dir($basePath)) {
                \yii\helpers\FileHelper::createDirectory($basePath, 0755);
            }

            if (!$file->saveAs($fullPath, true)) {
                return $this->asJson(['success' => false, 'message' => 'Failed to save documentation']);
            }

            $uploadedFile = new \yii\web\UploadedFile([
                'name'     => $fileName,                    
                'tempName' => $fullPath,
                'type'     => mime_content_type($fullPath),
                'size'     => filesize($fullPath),
                'error'    => UPLOAD_ERR_OK,
            ]);
            $result = $this->saveAttachedFiles($uploadedFile);

            return $this->asJson([
                'success'      => $result['success'],
                'message'      => $result['success'] ? 'Documentation uploaded' : $result['message'],
                'attachmentId' => $result['success'] ? $result['fileName'] : null,
            ]);
        }

        if (!is_file($fullPath)) {
            throw new \yii\web\NotFoundHttpException('File not found');
        }

        return Yii::$app->response->sendFile($fullPath, $fileName);
    }



    public function actionBulksavecsv()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isPost) {
            return ['success' => false, 'message' => 'Invalid request method'];
        }
        $csvJson = Yii::$app->request->post('csvdata');
        if (empty($csvJson)) {
            return ['success' => false, 'message' => 'No CSV data received'];
        }

        $records = json_decode($csvJson, true);
        if (!is_array($records) || empty($records)) {
            return ['success' => false, 'message' => 'Invalid CSV data format'];
        }

        $totalRecords = count($records);
        $transaction  = Yii::$app->db->beginTransaction();
        $successCount = 0;
        $errors       = [];
        $hddCompleted  = 0;
        try {
            foreach ($records as $index => $record) {
                try {
                    $model = new DataWipingAssetDetails();

                    $model->datawiping_id     = Yii::$app->request->post('_record_id');

                    $model->laptop_serial_no  = $record['laptop_serial_no']  ?? null;
                    $model->hdd_sdd_serial_no = $record['hdd_sdd_serial_no'] ?? null;
                    $model->make          = $record['make_id'] ?? null;
                    $model->type          = $record['type_id'] ?? null;
                    $model->capacity      = $record['capacity_id'] ?? null;
                    $model->software_name = $record['software_id'] ?? null;
                    $model->wiping_completed = $record['wiping_completed_id'] ?? 0;

                    $certAttachId = $record['cert_attach_id'];

                    $model->certificate = (string)$certAttachId ?? '';


                    $wipingDateStr = $record['wiping_date'] ?? '';
                    $model->wiping_date = $this->parseCsvDate($wipingDateStr) ?: null;

                    $wipingCompleted = trim($record['wiping_completed_id'] ?? '');
                   

                    $userId = Yii::$app->user->id ?? 1;
                    $now    = date('Y-m-d H:i:s');

                    $model->creatorid    = $userId;
                    $model->modifiedby   = $userId;
                    $model->createdtime  = $now;
                    $model->modifiedtime = $now;
                    $model->deleted      = 0;

                    if ($model->save(false)) {
                        $successCount++;
                        if ($record['wiping_completed_id'] == 1  && !empty($certAttachId)) {
                            $hddCompleted++;
                        }
                    } else {
                        $errors[] = "Row " . ($index + 1) . ": " . json_encode($model->errors);
                    }
                } catch (\Throwable $rowError) {
                    $errors[] = "Row " . ($index + 1) . ": " . $rowError->getMessage();
                }
            }

            if (empty($errors)) {
                 $datawipingId = (int)Yii::$app->request->post('_record_id');

                $dwModel = DataWiping::findOne($datawipingId);
                if ($dwModel) {
                    $oldAttrs = $dwModel->oldAttributes;
                    $oldHddCompleted = (int)$dwModel->hdd_completed;
                    $newHddCompleted = $oldHddCompleted + (int)$hddCompleted;
                    $dwModel->hdd_completed = $newHddCompleted;
                    $dwModel->save(false);

                    $data = [
                        'hdd_completed' => $newHddCompleted
                    ];
                    $modlog = new ModtrackerBasic();
                    $modlog->auditlog($oldAttrs,$data,'datawiping',$datawipingId,2,Yii::$app->user->id);
                }
                $modelDatawiping = new DataWiping();
                $modelDatawiping->saveToVpReports($datawipingId );
                $transaction->commit();
                return [
                    'success'       => true,
                    'message'       => "$successCount records saved successfully to data_wiping_asset_details",
                    'total_saved'   => $successCount,
                    'total_records' => $totalRecords,
                ];
            }

            $transaction->rollBack();
            return [
                'success'         => false,
                'message'         => "Saved {$successCount}/{$totalRecords} records. Errors: " . implode('; ', array_slice($errors, 0, 5)),
                'errors'          => $errors,
                'total_saved'     => $successCount,
                'total_processed' => $totalRecords,
            ];
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error("Bulk CSV save failed: " . $e->getMessage(), __METHOD__);
            return [
                'success' => false,
                'message' => 'Database transaction failed: ' . $e->getMessage(),
            ];
        }
    }
    /**
     * Safely get CSV value with trimming
     */
    private function getCsvValue($record, $key)
    {
        return isset($record[$key]) ? trim($record[$key]) : '';
    }

    /**
     * Parse various date formats from CSV
     */
    private function parseCsvDate($dateString)
    {
        if (empty($dateString)) {
            return null;
        }
        $formats = [
            'd-m-Y',
            'd/m/Y',
            'Y-m-d',
            'm/d/Y',
            'd.m.Y'
        ];

        foreach ($formats as $format) {
            $date = DateTime::createFromFormat($format, $dateString);
            if ($date && $date->format($format) === $dateString) {
                return $date->format('Y-m-d');
            }
        }

        // Try strtotime as fallback
        $timestamp = strtotime($dateString);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }

        return null;
    }

    public function actionAssetlist($datawiping_id = '', $page = 1)
    {
        $page      = (int)$page;
        $pageSize  = 50;

        $serial    = trim(Yii::$app->request->get('serial', ''));
        $wiping    = Yii::$app->request->get('wiping', ''); // '', '0', '1'
        $dateFrom  = trim(Yii::$app->request->get('date_from', ''));
        $dateTo    = trim(Yii::$app->request->get('date_to', ''));
        $datawiping_id = Yii::$app->request->get('datawiping_id', '');
        if (!$datawiping_id || $datawiping_id == '') {
            return false;
        }
        $dataWiping = DataWiping::find()
            // ->select(['data_wiping_no'])
            ->where(['datawiping_id' => $datawiping_id])
            ->asArray()
            ->one();
        $data_wiping_no = $dataWiping['data_wiping_no'] ?? '';
        $owner = $dataWiping['ownerid'] ?? '';
        $userId = Yii::$app->user->id ?? null;
        $isAdmin = Yii::$app->user->identity->is_super_admin ?? 0;
        $isSuperAdmin = Yii::$app->user->identity->is_admin ?? 0;
        $hasAccess = 0;
        if($isSuperAdmin || $isAdmin || $owner == $userId ){
        $hasAccess = 1;
        }
        $query = DataWipingAssetDetails::find()
            ->alias('d')
            ->select([
                'd.*',
                'dm.value AS make_name',
                'dt.value AS type_name',
                'dc.value AS capacity_name',
                'sn.value AS software_name_value',
                'dwc.value AS wiping_completed_value',
                'a.path',
                'a.name AS certificate_name',
            ])
            ->leftJoin('disk_make dm', 'dm.id = d.make')
            ->leftJoin('disk_type dt', 'dt.id = d.type')
            ->leftJoin('disk_capacity dc', 'dc.id = d.capacity')
            ->leftJoin('wiping_software sn', 'sn.id = d.software_name')
            ->leftJoin('data_wiping_completed dwc', 'dwc.id = d.wiping_completed')
            ->leftJoin('attachments a', 'a.attachmentsid = d.certificate')
            ->where(['d.datawiping_id' => $datawiping_id, 'd.deleted' => 0]);
        if ($serial !== '') {
            $query->andWhere(['like', 'd.laptop_serial_no', $serial]);
        }
        if ($wiping !== '' && $wiping !== null) {
            $query->andWhere(['d.wiping_completed' => (int)$wiping]);
        }
        if ($dateFrom !== '') {
            $query->andWhere(['>=', 'd.wiping_date', date('Y-m-d', strtotime($dateFrom))]);
        }
        if ($dateTo !== '') {
            $query->andWhere(['<=', 'd.wiping_date', date('Y-m-d', strtotime($dateTo))]);
        }

        $totalRecords = (int)$query->count();

        $rows = $query
            ->orderBy(['d.wiping_date' => SORT_DESC, 'd.laptop_serial_no' => SORT_ASC])
            ->offset(($page - 1) * $pageSize)
            ->limit($pageSize)
            ->asArray()
            ->all();
        $this->layout = '@app/views/layouts/main-one';
        return $this->render('assetdetails', [
            'datawiping_id' => $datawiping_id,
            'datewiping_no' => $data_wiping_no,
            'rows'          => $rows,
            'totalRecords'  => $totalRecords,
            'page'          => $page,
            'hasAccess'      => $hasAccess,
            'pageSize'      => $pageSize,
            'filters'       => [
                'serial'    => $serial,
                'wiping'    => $wiping,
                'date_from' => $dateFrom,
                'date_to'   => $dateTo,
            ],
        ]);
    }

    public function actionAssetdetail($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $model = DataWipingAssetDetails::find()
            ->alias('d')
            ->select(['d.*', 'a.path', 'a.name'])
            ->leftJoin('attachments a', 'a.attachmentsid = d.certificate')
            ->where(['d.datawiping_asset_id' => $id, 'd.deleted' => 0])
            ->asArray()
            ->one();

        if (!$model) {
            return ['success' => false, 'message' => 'Record not found'];
        }

        $diskMake = (new \yii\db\Query())
            ->from('disk_make')
            ->where(['is_active' => 1])
            ->orderBy(['seq_no' => SORT_ASC])
            ->all();

        $diskType = (new \yii\db\Query())
            ->from('disk_type')
            ->where(['is_active' => 1])
            ->orderBy(['seq_no' => SORT_ASC])
            ->all();

        $diskCapacity = (new \yii\db\Query())
            ->from('disk_capacity')
            ->where(['is_active' => 1])
            ->orderBy(['seq_no' => SORT_ASC])
            ->all();

        $softwares = (new \yii\db\Query())
            ->from('wiping_software')
            ->where(['is_active' => 1])
            ->orderBy(['seq_no' => SORT_ASC])
            ->all();

        $wipingCompletedOptions = (new \yii\db\Query())
            ->from('data_wiping_completed')
            ->where(['is_active' => 1])
            ->orderBy(['seq_no' => SORT_ASC])
            ->all();

        $selectedMakeId     = $model['make'] ?? null;
        $selectedTypeId     = $model['type'] ?? null;
        $selectedCapacityId = $model['capacity'] ?? null;
        $selectedSoftwareId = $model['software_name'] ?? null;
        $selectedWipingId   = $model['wiping_completed'] ?? null;

        return [
            'success' => true,
            'data' => [
                'datawiping_asset_id' => $model['datawiping_asset_id'],
                'laptop_serial_no'    => $model['laptop_serial_no'],
                'hdd_sdd_serial_no'   => $model['hdd_sdd_serial_no'],

                'make'                => $model['make'],
                'type'                => $model['type'],
                'capacity'            => $model['capacity'],
                'software_name'       => $model['software_name'],
                'wiping_completed'    => $model['wiping_completed'],
                'wiping_date'         => $model['wiping_date'],

                'certificate'         => $model['certificate'],
                'certificate_name'    => $model['certificate_name'] ?? null,
                'path'                => $model['path'] ?? null,
                'name'                => $model['name'] ?? null,

                'makeOptions'         => $diskMake,
                'typeOptions'         => $diskType,
                'capacityOptions'     => $diskCapacity,
                'softwareOptions'     => $softwares,
                'wipingOptions'       => $wipingCompletedOptions,

                'selectedMakeId'      => $selectedMakeId,
                'selectedTypeId'      => $selectedTypeId,
                'selectedCapacityId'  => $selectedCapacityId,
                'selectedSoftwareId'  => $selectedSoftwareId,
                'selectedWipingId'    => $selectedWipingId,
            ],
        ];
    }



  public function actionAssetupdate()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id');
        $transaction = Yii::$app->db->beginTransaction();

        $model = DataWipingAssetDetails::findOne([
            'datawiping_asset_id' => $id,
            'deleted'             => 0,
        ]);

        if (!$model) {
            return ['success' => false, 'message' => 'Record not found'];
        }

        $model->laptop_serial_no  = Yii::$app->request->post('laptop_serial_no',  $model->laptop_serial_no);
        $model->hdd_sdd_serial_no = Yii::$app->request->post('hdd_sdd_serial_no', $model->hdd_sdd_serial_no);

        $model->make          = Yii::$app->request->post('make_id',     $model->make);
        $model->type          = Yii::$app->request->post('type_id',     $model->type);
        $model->capacity      = Yii::$app->request->post('capacity_id', $model->capacity);
        $model->software_name = Yii::$app->request->post('software_id', $model->software_name);
        $model->wiping_completed = Yii::$app->request->post('wiping_completed', $model->wiping_completed);
      
        $wipingDateStr = Yii::$app->request->post('wiping_date', '');
        if ($wipingDateStr) {
            $dt = \DateTime::createFromFormat('d-m-Y', $wipingDateStr);
            if ($dt) {
                $model->wiping_date = $dt->format('Y-m-d');
            }
        }

        $model->modifiedby   = Yii::$app->user->id ?? $model->modifiedby;
        $model->modifiedtime = date('Y-m-d H:i:s');

        if ($model->save(false)) {
            $datawipingId   = $model->datawiping_id;
            $modelDatawiping = DataWiping::findOne($datawipingId);
            if ($modelDatawiping === null) {
                $transaction->rollBack();
                return ['success' => false, 'message' => 'DataWiping record not found'];
            }

            $modelDatawiping->saveToVpReports($datawipingId);

            if (!DataWipingAssetDetails::recalcParentHddCompleted($datawipingId)) {
                $transaction->rollBack();
                return ['success' => false, 'message' => 'Failed to update hdd_completed'];
            }

            $transaction->commit();
            return ['success' => true, 'message' => 'Record updated successfully'];
        }

        $transaction->rollBack();
        return ['success' => false, 'message' => 'Validation failed', 'errors' => $model->errors];
    }


    public function actionDownload($id)
    {
        $att = Attachments::findOne($id);
        if (!$att) {
            throw new \yii\web\NotFoundHttpException('File not found');
        }

        $filePath = Yii::getAlias('@webroot') . '/' . ltrim($att->path, '/');
        if (!file_exists($filePath)) {
            throw new \yii\web\NotFoundHttpException('File not found on server');
        }

        return Yii::$app->response->sendFile($filePath, $att->name ?? basename($filePath));
    }

    public function actionAssetuploadcertificate($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $model = DataWipingAssetDetails::findOne(['datawiping_asset_id' => $id, 'deleted' => 0]);
        if (!$model) {
            return ['success' => false, 'message' => 'Record not found'];
        }

        $file = UploadedFile::getInstanceByName('certificate_file');
        if (!$file) {
            return ['success' => false, 'message' => 'No file uploaded'];
        }

        $basePath = Yii::getAlias('@webroot/uploads/certificates');
        if (!is_dir($basePath)) {
            mkdir($basePath, 0777, true);
        }

        $safeName  = uniqid('cert_') . '.' . $file->extension;
        $fullPath  = $basePath . DIRECTORY_SEPARATOR . $safeName;

        if (!$file->saveAs($fullPath)) {
            return ['success' => false, 'message' => 'Failed to save file'];
        }

        $att = new Attachments();
        $att->name = $file->name;
        $att->path = 'uploads/certificates/' . $safeName;
        $att->createdtime = date('Y-m-d H:i:s');
        if (!$att->save(false)) {
            return ['success' => false, 'message' => 'Failed to save attachment'];
        }

        $model->certificate = (string)$att->attachmentsid;
        $model->modifiedby = Yii::$app->user->id ?? $model->modifiedby;
        $model->modifiedtime = date('Y-m-d H:i:s');
        $model->save(false);

        return [
            'success'           => true,
            'message'           => 'Certificate uploaded',
            'attachmentsid'     => (string)$att->attachmentsid,
            'certificate_name'  => $att->name,
            'attachment_path'   => $att->path,
        ];
    }
    public function actionGetcount()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $record_id = Yii::$app->request->post('id');
        $count = DataWipingAssetDetails::find()
            ->where(['datawiping_id' => $record_id])
            ->count();

        return [
            'success' => true,
            'count'   => (int)$count,
        ];
    }
    public function actionDownloadpicklists()
    {
        $diskMake = (new \yii\db\Query())
            ->from('disk_make')
            ->where(['is_active' => 1])
            ->orderBy(['seq_no' => SORT_ASC])
            ->all();

        $diskType = (new \yii\db\Query())
            ->from('disk_type')
            ->where(['is_active' => 1])
            ->orderBy(['seq_no' => SORT_ASC])
            ->all();

        $diskCapacity = (new \yii\db\Query())
            ->from('disk_capacity')
            ->where(['is_active' => 1])
            ->orderBy(['seq_no' => SORT_ASC])
            ->all();

        $softwares = (new \yii\db\Query())
            ->from('software_name')
            ->where(['is_active' => 1])
            ->orderBy(['seq_no' => SORT_ASC])
            ->all();

        $wipingCompletedOptions = (new \yii\db\Query())
            ->from('data_wiping_completed')
            ->where(['is_active' => 1])
            ->orderBy(['seq_no' => SORT_ASC])
            ->all();

        $csvContent = "Picklist,Values\n";
        
        $csvContent .= "Disk Make," . implode(',', array_column($diskMake, 'value')) . "\n";
        
        $csvContent .= "Disk Type," . implode(',', array_column($diskType, 'value')) . "\n";
        
        $csvContent .= "Disk Capacity," . implode(',', array_column($diskCapacity, 'value')) . "\n";
        
        $csvContent .= "Software," . implode(',', array_column($softwares, 'software_name_value')) . "\n";
        
        $csvContent .= "Wiping Completed," . implode(',', array_column($wipingCompletedOptions, 'value')) . "\n";

        $filename = 'picklists_data_' . date('Y-m-d_H-i-s') . '.csv';
        
        return Yii::$app->response->sendContentAsFile(
            $csvContent,
            $filename,
            ['mimeType' => 'text/csv']
        );
    }

    public function actionCanbulkupload()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $recordId = \Yii::$app->request->post('id');
        if (empty($recordId)) {
            return ['success' => false, 'allow' => false, 'message' => 'No record id'];
        }

        $userId   = \Yii::$app->user->id ?? null;
        $userRole = \Yii::$app->user->identity->role ?? null;

        $record = (new \yii\db\Query())
            ->from('data_wiping')
            ->where(['datawiping_id' => $recordId])
            ->one();
        
        if (!$record) {
            return ['success' => false, 'allow' => false, 'message' => 'Record not found'];
        }

        $wipingStatus  =  $record['wiping_status']  ?? null;
        $feUser        = (int) $record['fe_name']        ?? 0;
        $hddCount      = (int)($record['hdd_count']      ?? 0);
        $hddCompleted  = (int)($record['hdd_completed']  ?? 0);

        $actions = [
            'Edit'                    => false,
            'DataWipingCompleted'     => false,
            'DataWipingClientSignature' => false,
        ];
        if (($wipingStatus == 3 || $wipingStatus == 4) && $userRole == 'H56' && $feUser == $userId) {
            $actions['Edit'] = true;
        }
        if (empty($wipingStatus) || $wipingStatus == 2) {
            $actions['Edit'] = true;
        }
        if ($wipingStatus == 4 && ($hddCompleted == $hddCount) && $userRole == 'H56' && $feUser == $userId) {
            $actions['DataWipingCompleted'] = true;
            $actions['DataWipingClientSignature'] = true;
        }
        if($userId == (int) $record['ownerid']){
            $actions['Edit'] = true;
        }
        if ($wipingStatus == 5 || $wipingStatus == 6 || $wipingStatus == 7 || $wipingStatus == 8) {
            $actions['Edit'] = false;
        }
        
        $allowBulkUpload = (bool)$actions['Edit'];

        return [
            'success' => true,
            'allow'   => $allowBulkUpload,
            'data'    => [
                'wiping_status'  => $wipingStatus,
                'hdd_count'      => $hddCount,
                'hdd_completed'  => $hddCompleted,
                'actions'        => $actions,
            ],
        ];
    }

}
