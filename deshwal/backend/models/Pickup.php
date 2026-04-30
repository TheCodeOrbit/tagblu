<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pickup".
 *
 * @property int $pickup_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string $pickup_no
 * @property int|null $currency
 * @property int|null $logistic_user
 * @property int|null $pickup_inspection_require
 * @property float|null $exchange_rate
 * @property int|null $opportuity_name
 * @property string|null $account_name
 * @property string|null $location
 * @property string|null $address
 * @property string|null $city
 * @property string|null $state
 * @property int|null $pincode
 * @property string|null $spoc_name
 * @property string|null $spoc_number
 * @property string|null $spoc_email
 * @property string|null $escalation_name
 * @property string|null $escalation_number
 * @property string|null $escalation_email
 * @property int|null $business_manager
 * @property string|null $preferred_pickup_date
 * @property int|null $pickup_status
 * @property string|null $remarks
 * @property int|null $location_type
 * @property int|null $additional_info
 * @property int|null $doc_received
 * @property int $pickup
 * @property int $sale_on_site
 * @property int $both_decision
 * @property string|null $pickup_doneby
 * @property string|null $pickup_date
 * @property int|null $form_6
 * @property string|null $vendor_name
 * @property string|null $deshwal_spoc_name
 * @property string|null $deshwal_spoc_mobile
 * @property string|null $pickup_tentative_date
 * @property string|null $pickup_complete_date
 * @property string|null $vehicle_number
 * @property float|null $purchase_value
 * @property float|null $sale_value
 * @property int|null $credit_days
 * @property string|null $sale_pickup_tentative_date
 * @property string|null $sale_site_remarks
 * @property string|null $vendor_type
 * @property string|null $payment_type
 * @property string|null $sale_site_status
 * @property string|null $profit_loss
 * @property string|null $sale_site_pickup_date
 * @property string|null $pickup_done_by
 * @property string|null $spoc_person_name
 * @property string|null $spoc_person_mobile
 * @property float|null $engineer_cost
 * @property float|null $packing_material
 * @property float|null $other_charges
 * @property float|null $labour_cost
 * @property float|null $mathadi
 * @property float|null $local_vehicle_charge
 * @property float|null $total_vendor_costing
 * @property int|null $no_of_vehicle
 * @property string|null $date
 * @property string|null $docket_number
 * @property string|null $vehicle_no
 * @property int|null $vehicle_size
 * @property int|null $mode
 * @property float|null $empty_weight
 * @property float|null $loaded_weight
 * @property string|null $lock_by
 * @property string|null $seal_number
 * @property string|null $tentative_delivery_date
 * @property string|null $delivered_date
 * @property int|null $status
 * @property string|null $ageing
 * @property string|null $age
 * @property string|null $shipping_ageing
 * @property string|null $shipping_age
 * @property int|null $attach
 * @property int|null $document_for_pickup
 * @property int|null $document_attached
 * @property string|null $attachment
 * @property int $deleted
 *
 * @property PickupAssetDetail[] $pickupAssetDetails
 * @property PickupDocumentDetails[] $pickupDocumentDetails
 * @property PickupVehicleDetails[] $pickupVehicleDetails
 */
class Pickup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pickup';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime', 'pickup_no'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'currency', 'logistic_user', 'opportuity_name', 'pincode', 'business_manager', 'pickup_status', 'location_type', 'doc_received', 'pickup', 'sale_on_site', 'both_decision', 'form_6', 'credit_days', 'no_of_vehicle', 'vehicle_size', 'mode', 'status', 'attach', 'document_for_pickup', 'document_attached', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime', 'preferred_pickup_date', 'pickup_date', 'pickup_tentative_date', 'pickup_complete_date', 'sale_pickup_tentative_date', 'sale_site_pickup_date', 'date', 'tentative_delivery_date', 'delivered_date'], 'safe'],
            [['exchange_rate', 'purchase_value', 'sale_value', 'engineer_cost', 'packing_material', 'other_charges', 'labour_cost', 'mathadi', 'local_vehicle_charge', 'total_vendor_costing', 'empty_weight', 'loaded_weight'], 'number'],
            [['pickup_no', 'spoc_number', 'spoc_email', 'escalation_number', 'escalation_email', 'pickup_doneby', 'deshwal_spoc_name', 'pickup_done_by', 'docket_number', 'seal_number', 'ageing', 'age', 'shipping_ageing', 'shipping_age'], 'string', 'max' => 100],
            [['account_name', 'location',  'city', 'state', 'spoc_name', 'escalation_name', 'remarks', 'vendor_name', 'sale_site_remarks', 'vendor_type', 'payment_type', 'sale_site_status', 'profit_loss', 'spoc_person_name', 'lock_by', 'attachment'], 'string', 'max' => 200],
            [['deshwal_spoc_mobile', 'spoc_person_mobile'], 'string', 'max' => 20],
            [['vehicle_number', 'vehicle_no'], 'string', 'max' => 10],
            [['working_timings','extend_time_provision','extension_provision','entry_formalities_person','material_location_floor','material_floor','floor_num_material_count',
                'service_lift','lift_timing','stairs_space','material_move','segregation','space_for_segregation','movement_from_premises','distance','floor_num_for_take_out',
                'space_for_vehicle','small_vehicle','vehicle_as_per_height','material_from_basement_to_grnd','vehicle_entry_formalities','vehicle_inside_premises',
                'pickup_location','pickup_address','pickup_city','pickup_state','pickup_pin_code','delivery_location','delivery_address',
                'delivery_city','delivery_state','delivery_pin_code','logistic_user_number','actual_pickup_date','fe_name','fe_number',
                'vehicle_size1','distance_from_pickup','vehicle_parking_available','distance_from_lift','pickup_point_location',
                'hydra_require','folk_lift_require','mobile_trolley','total_labour_count','labour_rate','local_union','local_union_charges',
                'local_vehicle_require','local_vehicle_size','local_vehicle_charges','over_time','over_time_charges','form6_unsigned_copy',
                'form6_stamped_copy','form10_unsigned_copy','form10_stamped_copy','upload_unsigned_copy','upload_stamped_copy','green_certificate',
                'pickup_submitted_for_logistics','pickup_schedule','pickup_in_process','pickup_completed','pickup_inspection_require',
                'packing_material_approval_requested','vehicle_planning_approval_requested','vehicle_planning_remarks','additional_info',
                'num_of_vehicle_required','labour_count','scheduled_pickup_date','pre_pickup_remarks','num_local_vehicle','address'], 'safe'],
                 // added for handling blank values saving in by ptpatel on date 24-01-2026
            [['account_name'], 'trim'],
            [['account_name'], 'required', 'message' => 'Account Name cannot be blank.'],
            [['account_name'], 'integer', 'message' => 'account Name must be a number.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pickup_id' => 'Pickup ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'pickup_no' => 'Pickup No',
            'currency' => 'Currency',
            'logistic_user' => 'Logistic User',
            'exchange_rate' => 'Exchange Rate',
            'opportuity_name' => 'Opportuity Name',
            'account_name' => 'Account Name',
            'location' => 'Location',
            'address' => 'Address',
            'city' => 'City',
            'state' => 'State',
            'pincode' => 'Pincode',
            'spoc_name' => 'Spoc Name',
            'spoc_number' => 'Spoc Number',
            'spoc_email' => 'Spoc Email',
            'escalation_name' => 'Escalation Name',
            'escalation_number' => 'Escalation Number',
            'escalation_email' => 'Escalation Email',
            'business_manager' => 'Business Manager',
            'preferred_pickup_date' => 'Preferred Pickup Date',
            'pickup_status' => 'Pickup Status',
            'vehicle_planning_approval_requested' => 'vehicle_planning_approval_requested',
            'remarks' => 'Remarks',
            'vehicle_planning_remarks' => 'vehicle planning remarks',
            'location_type' => 'Location Type',
            'additional_info' => 'Additional Info',
            'doc_received' => 'Doc Received',
            'pickup' => 'Pickup',
            'sale_on_site' => 'Sale On Site',
            'both_decision' => 'Both Decision',
            'pickup_doneby' => 'Pickup Doneby',
            'pickup_date' => 'Pickup Date',
            'form_6' => 'Form 6',
            'vendor_name' => 'Vendor Name',
            'deshwal_spoc_name' => 'Deshwal Spoc Name',
            'deshwal_spoc_mobile' => 'Deshwal Spoc Mobile',
            'pickup_tentative_date' => 'Pickup Tentative Date',
            'pickup_complete_date' => 'Pickup Complete Date',
            'vehicle_number' => 'Vehicle Number',
            'purchase_value' => 'Purchase Value',
            'sale_value' => 'Sale Value',
            'credit_days' => 'Credit Days',
            'sale_pickup_tentative_date' => 'Sale Pickup Tentative Date',
            'sale_site_remarks' => 'Sale Site Remarks',
            'vendor_type' => 'Vendor Type',
            'payment_type' => 'Payment Type',
            'sale_site_status' => 'Sale Site Status',
            'profit_loss' => 'Profit Loss',
            'sale_site_pickup_date' => 'Sale Site Pickup Date',
            'pickup_done_by' => 'Pickup Done By',
            'spoc_person_name' => 'Spoc Person Name',
            'spoc_person_mobile' => 'Spoc Person Mobile',
            'engineer_cost' => 'Engineer Cost',
            'packing_material' => 'Packing Material',
            'other_charges' => 'Other Charges',
            'labour_cost' => 'Labour Cost',
            'mathadi' => 'Mathadi',
            'local_vehicle_charge' => 'Local Vehicle Charge',
            'total_vendor_costing' => 'Total Vendor Costing',
            'no_of_vehicle' => 'No Of Vehicle',
            'date' => 'Date',
            'docket_number' => 'Docket Number',
            'vehicle_no' => 'Vehicle No',
            'vehicle_size' => 'Vehicle Size',
            'mode' => 'Mode',
            'empty_weight' => 'Empty Weight',
            'loaded_weight' => 'Loaded Weight',
            'lock_by' => 'Lock By',
            'seal_number' => 'Seal Number',
            'tentative_delivery_date' => 'Tentative Delivery Date',
            'delivered_date' => 'Delivered Date',
            'status' => 'Status',
            'ageing' => 'Ageing',
            'age' => 'Age',
            'shipping_ageing' => 'Shipping Ageing',
            'shipping_age' => 'Shipping Age',
            'attach' => 'Attach',
            'document_for_pickup' => 'Document For Pickup',
            'document_attached' => 'Document Attached',
            'attachment' => 'Attachment',
            'deleted' => 'Deleted',
            'working_timings' => 'What are the working timings?',
            'extend_time_provision' => 'Do we have any provision to extend the timings',
            'extension_provision' => 'What is the procedure to inform/update regarding extention',
            'entry_formalities_person' => 'What are the formalities for entry personnel',
            'material_location_floor' => 'Material lying at which location/Floor',
            'material_floor' => 'At which floor all the material is stored?',
            'floor_num_material_count' => 'Please share the floor Number with material count',
            'service_lift' => 'Do we have service lift available',
            'lift_timing' => 'What are the lift timings?',
            'stairs_space' => 'Does staris has sufficient space from where we can move the the material out from the premises?',
            'material_move' => 'How we can move the material out',
            'segregation' => 'All items are segregated or segregation require',
            'space_for_segregation' => 'Do we have space available for this segregation',
            'movement_from_premises' => 'What is the material movement from premises?',
            'distance' => 'Distance between material and vehicle parked',
            'floor_num_for_take_out' => 'Please share the basement floor / number from where we need to take out the material',
            'space_for_vehicle' => 'Do facility has sufficient space that vehicle can go inside the basement to pick the material',
            'small_vehicle' => 'Do we require small vehicle for this movement',
            'vehicle_as_per_height' => 'Please share the vehicle Name/Size which is allowed as per height',
            'material_from_basement_to_grnd' => 'Please share how we can move the material from basement to Ground floor where vehicle parked?',
            'vehicle_entry_formalities' => 'What are the formalities for vehicle entry',
            'vehicle_inside_premises' => 'vehicle can parked inside the premises',
            'pickup_location' => "",
            'pickup_address' => "",
            'pickup_city' => "",
            'pickup_state' => "",
            'pickup_pin_code' => "",
            'delivery_location' => "",
            'delivery_address' => "",
            'delivery_city' => "",
            'delivery_state' => "",
            'delivery_pin_code' => "",
            'logistic_user_number' => "",
            'actual_pickup_date' => "",
            'fe_name' => "",
            'fe_number' => "",
            'vehicle_size1' => "",
            'distance_from_pickup' => "",
            'vehicle_parking_available' => "",
            'distance_from_lift' => "",
            'pickup_point_location' => "",
            'hydra_require' => "",
            'folk_lift_require' => "",
            'mobile_trolley' => "",
            'total_labour_count' => "",
            'labour_rate' => "",
            'local_union' => "",
            'local_union_charges' => "",
            'local_vehicle_require' => "",
            'local_vehicle_size' => "",
            'local_vehicle_charges' => "",
            'over_time' => "",
            'over_time_charges' => "",
            'form6_unsigned_copy' => "",
            'form6_stamped_copy' => "",
            'form10_unsigned_copy' => "",
            'form10_stamped_copy' => "",
            'upload_unsigned_copy' => "",
            'upload_stamped_copy' => "",
            'green_certificate' => "",
            'pickup_submitted_for_logistics'=>"pickup_submitted_for_logistics",
            'pickup_inspection_require' => 'pickup_inspection_require',
            'packing_material_approval_requested' => 'Packing material approval requested',
            'pickup_schedule' => 'pickup_schedule',
            'pickup_in_process' => 'pickup_in_process',
            'pickup_completed' => 'pickup_completed',
            'num_of_vehicle_required' =>'num_of_vehicle_required',
            'labour_count' => 'labour_count',
            'scheduled_pickup_date' => 'scheduled_pickup_date',
            'pre_pickup_remarks' => 'pre_pickup_remarks',
            'num_local_vehicle' => 'num_local_vehicle'
        ];
    }

    /**
     * Gets query for [[PickupAssetDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPickupAssetDetails()
    {
        return $this->hasMany(PickupAssetDetail::class, ['pickup_id' => 'pickup_id']);
    }

    /**
     * Gets query for [[PickupDocumentDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPickupDocumentDetails()
    {
        return $this->hasMany(PickupDocumentDetails::class, ['pickup_id' => 'pickup_id']);
    }

    /**
     * Gets query for [[PickupVehicleDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPickupVehicleDetails()
    {
        return $this->hasMany(PickupVehicleDetails::class, ['pickup_id' => 'pickup_id']);
    }
 
    public function pickupStageCalc($current_pickup_stage)
    {
        if($current_pickup_stage == 5) return 5;
        if($current_pickup_stage == 6) return 6;
        if(empty($current_pickup_stage)) $current_pickup_stage = 2;
        $updated_pickup_stage = $current_pickup_stage;
        $pickup_submitted_for_logistics = $_POST["pickup"]["pickup_submitted_for_logistics"]??null;
        $pickup_inspection_require = $_POST["pickup"]["pickup_inspection_require"]??null;
        $packing_material_approval_requested = $_POST["pickup"]["packing_material_approval_requested"]??null;
        $pickup_schedule = $_POST["pickup"]["pickup_schedule"]??null;
        $vehicle_planning_approval_requested = $_POST["pickup"]["vehicle_planning_approval_requested"]??null;
        $pickup_in_process = $_POST["pickup"]["pickup_in_process"]??null;
        $pickup_completed = $_POST["pickup"]["pickup_completed"]??null;
        if($current_pickup_stage == 14 && empty($packing_material_approval_requested)){
            return $current_pickup_stage;
        }
        if($current_pickup_stage == 18){
            if(empty($vehicle_planning_approval_requested)){
                return 18;
            }else{
                return 11;
            }
        }
        if(isset($pickup_completed)){
            return 6;
        }
        if($current_pickup_stage == 12){
            if($pickup_schedule){
                return 15;
            }
            return 12;
        }
        if($current_pickup_stage == 15){
            if($vehicle_planning_approval_requested){
                return 11;
            }
            return 15;
        }
        if($current_pickup_stage == 5){
            return 5;
        }
        if(isset($pickup_in_process)){
            $updated_pickup_stage = 5;
        } else if($packing_material_approval_requested){
            $updated_pickup_stage = 10;
        } else if(isset($pickup_inspection_require)){
            $updated_pickup_stage = 4;
        } else if(isset($pickup_submitted_for_logistics)){
            $updated_pickup_stage = 3;
        }
        if(empty($updated_pickup_stage)) $updated_pickup_stage = 2;
        return $updated_pickup_stage;
    }

    public function inspectionRelatedDataCreate($sourcingdeal_id,$RecordId)
    {
        if(!empty($sourcingdeal_id)){
            //check full inspection record exist or not 
            $InspectionData = Inspection::find()->where(['sourcing_deal' => $sourcingdeal_id,'insection_type'=>1,'deleted'=>0])->one();
            if ($InspectionData !== null) {
                $inspection_id =  $InspectionData->inspection_id;
                //query and save data for PickupFullProductDetailDesktop from InspectionFullProductDetailDesktop
                $inspectionDesktopProducts = InspectionFullProductDetailDesktop::find()
                    ->where(['inspection_id' => $inspection_id])
                    ->all();
                
                if (!empty($inspectionDesktopProducts)) {
                    $pickupDesktopModel = new PickupFullProductDetailDesktop();
                    $pickupDesktopModel->saveFromInspectionData($RecordId, $inspectionDesktopProducts);
                }

                //query and save data for PickupFullProductDetailLaptop from InspectionFullProductDetailLaptop
                $inspectionLaptopProducts = InspectionFullProductDetailLaptop::find()
                    ->where(['inspection_id' => $inspection_id])
                    ->all();
                
                if (!empty($inspectionLaptopProducts)) {
                    $pickupLaptoptopModel = new PickupFullProductDetailLaptop();
                    $pickupLaptoptopModel->saveFromInspectionData($RecordId, $inspectionLaptopProducts);
                }

                //query and save data for PickupFullProductDetailTft from InspectionFullProductDetailTft
                $inspectionTftProducts = InspectionFullProductDetailTft::find()
                    ->where(['inspection_id' => $inspection_id])
                    ->all();
                
                if (!empty($inspectionTftProducts)) {
                    $pickupTftModel = new PickupFullProductDetailTft();
                    $pickupTftModel->saveFromInspectionData($RecordId, $inspectionTftProducts);
                }

                //query and save data for PickupRandomProductDetail from InspectionRandomProductDetail
                $inspectionRandomProducts = InspectionRandomProductDetail::find()
                    ->where(['inspection_id' => $inspection_id])
                    ->all();
                
                if (!empty($inspectionRandomProducts)) {
                    $pickupRandomModel = new PickupRandomProductDetail();
                    $pickupRandomModel->saveFromInspectionData($RecordId, $inspectionRandomProducts);
                }
            }
        }
    }
    public function inspectionRelatedDataEdit($sourcingdeal_id,$old_sourcing_deal_id,$RecordId)
    {
        if(!empty($sourcingdeal_id)){
            //check full inspection record exist or not 
            $InspectionData = Inspection::find()->where(['sourcing_deal' => $sourcingdeal_id,'insection_type'=>1,'deleted'=>0])->one();
            
        }
        if ($old_sourcing_deal_id != $sourcingdeal_id) {
            Yii::$app->db->createCommand("delete from `pickup_full_product_detail_desktop` where pickup_id=:pickup_id")
                ->bindValue(":pickup_id", $RecordId)
                ->queryOne();
            if($InspectionData !== null){
                $inspection_id =  $InspectionData->inspection_id;
                //query and save data for PickupFullProductDetailDesktop from InspectionFullProductDetailDesktop
                $inspectionDesktopProducts = InspectionFullProductDetailDesktop::find()
                    ->where(['inspection_id' => $inspection_id])
                    ->all();
                
                if (!empty($inspectionDesktopProducts)) {
                    $pickupDesktopModel = new PickupFullProductDetailDesktop();
                    $pickupDesktopModel->saveFromInspectionData($RecordId, $inspectionDesktopProducts);
                }
            }
        }else if (isset($_POST['pickup_full_product_detail_desktop']) && !empty($_POST['pickup_full_product_detail_desktop'])) {
            Yii::$app->db->createCommand("delete from `pickup_full_product_detail_desktop` where pickup_id=:pickup_id")
                ->bindValue(":pickup_id", $RecordId)
                ->queryOne();
            //save to child table
            $pickupDesktopModel = new PickupFullProductDetailDesktop();
            $pickupDesktopModel->savePickupDesktopProduct($RecordId);
        }else if($old_sourcing_deal_id == $sourcingdeal_id && $InspectionData !== null){
            // may be there is no existing record
            $desktop_data = PickupFullProductDetailDesktop::find()->where(['pickup_id' => $RecordId])->all();
            if(empty($desktop_data)){
                $inspection_id =  $InspectionData->inspection_id;
                //query and save data for PickupFullProductDetailDesktop from InspectionFullProductDetailDesktop
                $inspectionDesktopProducts = InspectionFullProductDetailDesktop::find()
                    ->where(['inspection_id' => $inspection_id])
                    ->all();
                
                if (!empty($inspectionDesktopProducts)) {
                    $pickupDesktopModel = new PickupFullProductDetailDesktop();
                    $pickupDesktopModel->saveFromInspectionData($RecordId, $inspectionDesktopProducts);
                }
            }
        }

        if ($old_sourcing_deal_id != $sourcingdeal_id) {
            Yii::$app->db->createCommand("delete from `pickup_full_product_detail_laptop` where pickup_id=:pickup_id")
                ->bindValue(":pickup_id", $RecordId)
                ->queryOne();
            if($InspectionData !== null){
                $inspection_id =  $InspectionData->inspection_id;
                //query and save data for PickupFullProductDetailLaptop from InspectionFullProductDetailLaptop
                $inspectionLaptopProducts = InspectionFullProductDetailLaptop::find()
                    ->where(['inspection_id' => $inspection_id])
                    ->all();
                
                if (!empty($inspectionLaptopProducts)) {
                    $pickupLaptoptopModel = new PickupFullProductDetailLaptop();
                    $pickupLaptoptopModel->saveFromInspectionData($RecordId, $inspectionLaptopProducts);
                }
            }
        }else if (isset($_POST['pickup_full_product_detail_laptop']) && !empty($_POST['pickup_full_product_detail_laptop'])) {
            Yii::$app->db->createCommand("delete from `pickup_full_product_detail_laptop` where pickup_id=:pickup_id")
                ->bindValue(":pickup_id", $RecordId)
                ->queryOne();
            $pickupLaptoptopModel = new PickupFullProductDetailLaptop();
            $pickupLaptoptopModel->savePickupLaptopProduct($RecordId);
        }else if($old_sourcing_deal_id == $sourcingdeal_id && $InspectionData !== null){
            // may be there is no existing record
            $laptop_data = PickupFullProductDetailLaptop::find()->where(['pickup_id' => $RecordId])->all();
            if(empty($laptop_data)){
                $inspection_id =  $InspectionData->inspection_id;
                //query and save data for PickupFullProductDetailLaptop from InspectionFullProductDetailLaptop
                $inspectionLaptopProducts = InspectionFullProductDetailLaptop::find()
                    ->where(['inspection_id' => $inspection_id])
                    ->all();
                
                if (!empty($inspectionLaptopProducts)) {
                    $pickupLaptoptopModel = new PickupFullProductDetailLaptop();
                    $pickupLaptoptopModel->saveFromInspectionData($RecordId, $inspectionLaptopProducts);
                }
            }
        }


        if ($old_sourcing_deal_id != $sourcingdeal_id) {
            Yii::$app->db->createCommand("delete from `pickup_full_product_detail_tft` where pickup_id=:pickup_id")
                ->bindValue(":pickup_id", $RecordId)
                ->queryOne();
            if($InspectionData !== null){
                $inspection_id =  $InspectionData->inspection_id;
                //query and save data for PickupFullProductDetailTft from InspectionFullProductDetailTft
                $inspectionTftProducts = InspectionFullProductDetailTft::find()
                    ->where(['inspection_id' => $inspection_id])
                    ->all();
                
                if (!empty($inspectionTftProducts)) {
                    $pickupTftModel = new PickupFullProductDetailTft();
                    $pickupTftModel->saveFromInspectionData($RecordId, $inspectionTftProducts);
                }
            }
        }else if (isset($_POST['pickup_full_product_detail_tft']) && !empty($_POST['pickup_full_product_detail_tft'])) {
            Yii::$app->db->createCommand("delete from `pickup_full_product_detail_tft` where pickup_id=:pickup_id")
                ->bindValue(":pickup_id", $RecordId)
                ->queryOne();
            $pickupTftModel = new PickupFullProductDetailTft();
            $pickupTftModel->savePickupTftProduct($RecordId);
        }else if($old_sourcing_deal_id == $sourcingdeal_id && $InspectionData !== null){
            // may be there is no existing record
            $tft_data = PickupFullProductDetailTft::find()->where(['pickup_id' => $RecordId])->all();
            if(empty($tft_data)){
                $inspection_id =  $InspectionData->inspection_id;
                //query and save data for PickupFullProductDetailTft from InspectionFullProductDetailTft
                $inspectionTftProducts = InspectionFullProductDetailTft::find()
                    ->where(['inspection_id' => $inspection_id])
                    ->all();
                
                if (!empty($inspectionTftProducts)) {
                    $pickupTftModel = new PickupFullProductDetailTft();
                    $pickupTftModel->saveFromInspectionData($RecordId, $inspectionTftProducts);
                }
            }
        }

        if ($old_sourcing_deal_id != $sourcingdeal_id) {
            Yii::$app->db->createCommand("delete from `pickup_random_product_detail` where pickup_id=:pickup_id")
                ->bindValue(":pickup_id", $RecordId)
                ->queryOne();
            if($InspectionData !== null){
                $inspection_id =  $InspectionData->inspection_id;
                //query and save data for PickupRandomProductDetail from InspectionRandomProductDetail
                $inspectionRandomProducts = InspectionRandomProductDetail::find()
                    ->where(['inspection_id' => $inspection_id])
                    ->all();
                
                if (!empty($inspectionRandomProducts)) {
                    $pickupRandomModel = new PickupRandomProductDetail();
                    $pickupRandomModel->saveFromInspectionData($RecordId, $inspectionRandomProducts);
                }
            }
        }else if (isset($_POST['pickup_random_product_detail']) && !empty($_POST['pickup_random_product_detail'])) {
            Yii::$app->db->createCommand("delete from `pickup_random_product_detail` where pickup_id=:pickup_id")
                ->bindValue(":pickup_id", $RecordId)
                ->queryOne();
            $pickupRandomModel = new PickupRandomProductDetail();
            $pickupRandomModel->savePickupRandomProduct($RecordId);
        }else if($old_sourcing_deal_id == $sourcingdeal_id && $InspectionData !== null){
            // may be there is no existing record
            $random_data = PickupRandomProductDetail::find()->where(['pickup_id' => $RecordId])->all();
            if(empty($random_data)){
                $inspection_id =  $InspectionData->inspection_id;
                //query and save data for PickupRandomProductDetail from InspectionRandomProductDetail
                $inspectionRandomProducts = InspectionRandomProductDetail::find()
                    ->where(['inspection_id' => $inspection_id])
                    ->all();
                
                if (!empty($inspectionRandomProducts)) {
                    $pickupRandomModel = new PickupRandomProductDetail();
                    $pickupRandomModel->saveFromInspectionData($RecordId, $inspectionRandomProducts);
                }
            }
        }
    }

    public function product_data($connection,$product_id){
        if(empty($product_id)){
            return ["product_name" =>"","weight_kg" => 0];
        }
        $command = $connection
        ->createCommand("SELECT product_name,weight_kg FROM products WHERE products_id = :products_id")
        ->bindValue(":products_id", $product_id);
        $productData = $command->queryOne();
        return empty($productData)?["product_name" =>"","weight_kg" => 0]:$productData;
    }
    public function save_vp_certificate($RecordId)
    {
        $connection = Yii::$app->db;
        // $sql = "SELECT pickup_no FROM pickup where pickup.pickup_id = :RecordId";
        // $result = Yii::$app->db->createCommand($sql)->bindValue(":RecordId",$RecordId)->queryOne();
        // $pickup_no= $result['pickup_no'];

        $sql_del = "Delete from `rep_vp_certificates` where pickup_id=:pickup_id";
        $connection->createCommand($sql_del)->bindValue(":pickup_id",$RecordId)->execute();

        $sql = "SELECT sourcingdeal.sourcingdeal_no,sourcingdeal.sourcingdeal_id, sourcingdeal.vendor_account_name as account_id,
            pickup.pickup_no,pickup.pickup_id,
            pickup.form6_unsigned_copy,pickup.form6_stamped_copy,pickup.form10_unsigned_copy,pickup.form10_stamped_copy,
            pickup.green_certificate,
            pickup.pickup_status from pickup 
            left join sourcingdeal on sourcingdeal.sourcingdeal_id = pickup.opportuity_name 
            where pickup.pickup_id = :RecordId";
        $result = $connection->createCommand($sql)->bindValue(":RecordId",$RecordId)->queryAll();
        foreach($result as $value)
        {
            $account_id = $value['account_id']?$value['account_id']:null;
            $sourcingdeal_no = $value['sourcingdeal_no']?$value['sourcingdeal_no']:null;
            $sourcingdeal_id = $value['sourcingdeal_id']?$value['sourcingdeal_id']:null;
            $pickup_no = $value['pickup_no'] ? $value['pickup_no']:null;
            $pickup_id = $value['pickup_id']?$value['pickup_id']:null;
            $pickup_status = $value['pickup_status'] ? $value['pickup_status'] : null;
            $green_certificate = $value['green_certificate'] ? $value['green_certificate'] : null;
            $form6_unsigned_copy = $value['form6_unsigned_copy'] ? $value['form6_unsigned_copy'] : null;
            $form6_stamped_copy = $value['form6_stamped_copy'] ? $value['form6_stamped_copy'] : null;
            $form10_unsigned_copy = $value['form10_unsigned_copy'] ? $value['form10_unsigned_copy'] : null;
            $form10_stamped_copy = $value['form10_stamped_copy'] ? $value['form10_stamped_copy'] : null;

            //total count and total weight
            
            $total_picked_qty = 0;
            $assets_total_weight = 0;
            //get pickup assets
            $assets_command = $connection->createCommand("SELECT * FROM pickup_asset_detail WHERE pickup_id = :record_id and deleted=0")->bindValues([":record_id"=> $RecordId]);
            $pickup_assets = $assets_command->queryAll();
            foreach($pickup_assets as $i => $pa){
                $picked_qty = (int)$pa["picked_qty"];
                if(empty($picked_qty)) $picked_qty = 0;
                $total_picked_qty += $picked_qty;
                $product_data = $this->product_data($connection,$pa["porduct_name"]);
                $product_weight = $product_data["weight_kg"]?$product_data["weight_kg"]:0;
                $assets_total_weight += ($product_weight * $picked_qty);
            }
            $sql_ins = "INSERT INTO `rep_vp_certificates` 
                (account_id, req_reference_no, sourcingdeal_no, sourcingdeal_id, pickup_id, total_assets, total_weight, pickup_status,
                green_certificate,form6_unsigned_copy,form6_stamped_copy,form10_unsigned_copy,form10_stamped_copy, created_on) 
                VALUES 
                (:account_id, :req_reference_no, :sourcingdeal_no, :sourcingdeal_id, :pickup_id, :total_assets, :total_weight, :pickup_status,
                :green_certificate,:form6_unsigned_copy,:form6_stamped_copy,:form10_unsigned_copy,:form10_stamped_copy, NOW())";

            $params = [
            ':account_id' => $account_id,
            ':req_reference_no' => $pickup_no ? $pickup_no :null,
            ':sourcingdeal_no' => $sourcingdeal_no ? $sourcingdeal_no :null,
            ':sourcingdeal_id' => $sourcingdeal_id ? $sourcingdeal_id :null,
            ':pickup_id' => $pickup_id ? $pickup_id :null,
            ':total_assets' => $total_picked_qty ? $total_picked_qty :null,
            ':total_weight' => $assets_total_weight ? $assets_total_weight :null,
            ':pickup_status' => $pickup_status ? $pickup_status :null,
            ':green_certificate' => $green_certificate?$green_certificate:null,
            ':form6_unsigned_copy' =>$form6_unsigned_copy?$form6_unsigned_copy:null,
            ':form6_stamped_copy' =>$form6_stamped_copy?$form6_stamped_copy:null,
            ':form10_unsigned_copy' =>$form10_unsigned_copy?$form10_unsigned_copy:null,
            ':form10_stamped_copy' =>$form10_stamped_copy?$form10_stamped_copy:null
            ];

            $connection->createCommand($sql_ins)->bindValues($params)->execute();
        }
    }
}
