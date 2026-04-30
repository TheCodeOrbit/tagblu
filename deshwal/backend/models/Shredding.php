<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "shredding".
 *
 * @property int $shredding_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $shredding_no
 * @property string|null $po_number
 * @property string|null $image
 * @property string|null $billable
 * @property string|null $preferred_shredding_date
 * @property string|null $shredding_status
 * @property string|null $logistic_spoc_name
 * @property string|null $currency
 * @property string|null $opportunity_name
 * @property string|null $account_name
 * @property string|null $spoc_name
 * @property string|null $hdd_count
 * @property string|null $submit_approval
 * @property string|null $visible_status
 * @property string|null $spoc_mobile_number
 * @property int|null $email_confirmation
 * @property string|null $email_date
 * @property string|null $customer_name
 * @property string|null $bill_location
 * @property string|null $bill_address
 * @property string|null $city
 * @property string|null $state
 * @property string|null $pincode
 * @property string|null $gstin_no
 * @property string|null $bill_spoc
 * @property string|null $hdd_completed
 * @property string|null $bill_spoc_number
 * @property string|null $bill_spoc_email
 * @property string|null $billing_amount
 * @property string|null $billing_type
 * @property string|null $taxable_value
 * @property string|null $total_cost
 * @property string|null $activity_location
 * @property string|null $activity_city
 * @property string|null $activity_state
 * @property string|null $activity_gstin_no
 * @property string|null $activity_address
 * @property string|null $activity_pincode
 * @property string|null $activity_pan_no
 * @property string|null $exchange_rate
 * @property string|null $shredding_owner
 * @property int $deleted
 * @property int|null $entry_formalities_person
 * @property string|null $material_location_floor
 * @property string|null $activity_area
 * @property string|null $secure_degaussing_area
 * @property string|null $proper_ventilation
 * @property string|null $trolley_for_movement
 * @property int|null $power_supply_area
 * @property string|null $power_socket_to_machine_distance
 * @property int|null $service_lift
 * @property string|null $lift_timings
 * @property int|null $stairs_area
 * @property string|null $how_to_do_machine_movement
 * @property int|null $working_timings
 * @property int|null $extend_time_provision
 * @property int|null $extension_provision
 * @property int|null $hdd_sdd_removed_from_device
 * @property int|null $who_will_remove_hdd_sdd
 * @property int|null $space_available_hdd_removal
 * @property string|null $activtiy_spoc
 * @property string|null $activtiy_spoc_email
 * @property string|null $activtiy_spoc_mobile
 * @property int|null $pickup_location_type
 * @property string|null $pickup_location
 * @property string|null $pickup_location_client
 * @property string|null $pickup_location_engineer
 * @property string|null $pickup_address
 * @property string|null $pickup_city
 * @property string|null $pickup_state
 * @property string|null $pickup_pin
 * @property string|null $pickup_spoc
 * @property string|null $pickup_spoc_number
 * @property string|null $hsap_key_serial_num
 * @property string|null $hsap_key_image
 * @property string|null $courrier_name
 * @property string|null $docket_number
 * @property string|null $shipped_date
 * @property string|null $gate_pass
 * @property string|null $delivery_challan_invoice
 * @property int|null $delivery_location_type
 * @property string|null $delivery_location_internal
 * @property string|null $delivery_location_client
 * @property string|null $delivery_location_engineer
 * @property string|null $delivery_address
 * @property string|null $delivery_city
 * @property string|null $delivery_state
 * @property string|null $delivery_pin
 * @property string|null $receiver_spoc_name
 * @property string|null $receiver_spoc_number
 * @property string|null $delivery_date
 * @property int|null $delivery_condition
 * @property string|null $hsap_key_receipient
 * @property string|null $logistic_spoc_number
 * @property string|null $activity_schedule_date
 * @property string|null $completed_date
 * @property string|null $fe_name
 * @property string|null $fe_number
 * @property int|null $hsap_key_require
 * @property string|null $hsap_count
 * @property string|null $dongle_pickup_date
 * @property int|null $dongle_pickup_condition
 *
 * @property ShreddingAssetDetails[] $shreddingAssetDetails
 */
class Shredding extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shredding';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'email_confirmation', 'deleted', 'entry_formalities_person', 'power_supply_area', 'service_lift', 'stairs_area', 'working_timings', 'extend_time_provision', 'extension_provision', 'hdd_sdd_removed_from_device', 'who_will_remove_hdd_sdd', 'space_available_hdd_removal', 'pickup_location_type', 'delivery_location_type', 'delivery_condition', 'hsap_key_require', 'dongle_pickup_condition'], 'integer'],
            [['createdtime', 'modifiedtime', 'shipped_date', 'delivery_date', 'activity_schedule_date', 'completed_date', 'dongle_pickup_date'], 'safe'],
            [['shredding_no', 'po_number', 'image', 'billable', 'preferred_shredding_date', 'shredding_status', 'logistic_spoc_name', 'currency', 'opportunity_name', 'account_name', 'spoc_name', 'hdd_count', 'submit_approval', 'visible_status', 'spoc_mobile_number', 'email_date', 'customer_name', 'bill_location',  'city', 'state', 'pincode', 'gstin_no', 'taxable_value', 'total_cost', 'activity_location', 'activity_city', 'activity_state', 'activity_gstin_no',  'activity_pincode', 'activity_pan_no', 'exchange_rate', 'shredding_owner', 'pickup_location_engineer'], 'string', 'max' => 200],
            [['bill_spoc', 'activtiy_spoc', 'activtiy_spoc_mobile', 'pickup_location_client', 'delivery_location_internal', 'delivery_location_client', 'receiver_spoc_number', 'logistic_spoc_number', 'fe_name', 'fe_number', 'hsap_count'], 'string', 'max' => 10],
            [['hdd_completed', 'bill_spoc_number', 'billing_amount', 'pickup_location'], 'string', 'max' => 20],
            [['bill_spoc_email', 'activtiy_spoc_email'], 'string', 'max' => 50],
            [['billing_type', 'courrier_name'], 'string', 'max' => 5],
            [['material_location_floor',  'pickup_city', 'pickup_state', 'pickup_spoc', 'hsap_key_serial_num', 'hsap_key_image', 'docket_number', 'gate_pass', 'delivery_challan_invoice', 'delivery_location_engineer', 'delivery_city', 'delivery_state', 'receiver_spoc_name', 'hsap_key_receipient'], 'string', 'max' => 100],
            [['activity_area', 'secure_degaussing_area', 'proper_ventilation', 'trolley_for_movement', 'power_socket_to_machine_distance', 'lift_timings', 'how_to_do_machine_movement'], 'string', 'max' => 500],
            [['pickup_pin', 'delivery_pin'], 'string', 'max' => 6],
            [['pickup_spoc_number'], 'string', 'max' => 15],
            [['activity_address'], 'string', 'max' => 3000],
            [['pickup_address','bill_address', 'delivery_address'], 'string'],
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
            'shredding_id' => 'Shredding ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'shredding_no' => 'Shredding No',
            'po_number' => 'Po Number',
            'image' => 'Image',
            'billable' => 'Billable',
            'preferred_shredding_date' => 'Preferred Shredding Date',
            'shredding_status' => 'Shredding Status',
            'logistic_spoc_name' => 'Logistic Spoc Name',
            'currency' => 'Currency',
            'opportunity_name' => 'Opportunity Name',
            'account_name' => 'Account Name',
            'spoc_name' => 'Spoc Name',
            'hdd_count' => 'Hdd Count',
            'submit_approval' => 'Submit Approval',
            'visible_status' => 'Visible Status',
            'spoc_mobile_number' => 'Spoc Mobile Number',
            'email_confirmation' => 'Email Confirmation',
            'email_date' => 'Email Date',
            'customer_name' => 'Customer Name',
            'bill_location' => 'Bill Location',
            'bill_address' => 'Bill Address',
            'city' => 'City',
            'state' => 'State',
            'pincode' => 'Pincode',
            'gstin_no' => 'Gstin No',
            'bill_spoc' => 'Bill Spoc',
            'hdd_completed' => 'Hdd Completed',
            'bill_spoc_number' => 'Bill Spoc Number',
            'bill_spoc_email' => 'Bill Spoc Email',
            'billing_amount' => 'Billing Amount',
            'billing_type' => 'Billing Type',
            'taxable_value' => 'Taxable Value',
            'total_cost' => 'Total Cost',
            'activity_location' => 'Activity Location',
            'activity_city' => 'Activity City',
            'activity_state' => 'Activity State',
            'activity_gstin_no' => 'Activity Gstin No',
            'activity_address' => 'Activity Address',
            'activity_pincode' => 'Activity Pincode',
            'activity_pan_no' => 'Activity Pan No',
            'exchange_rate' => 'Exchange Rate',
            'shredding_owner' => 'Shredding Owner',
            'deleted' => 'Deleted',
            'entry_formalities_person' => 'Entry Formalities Person',
            'material_location_floor' => 'Material Location Floor',
            'activity_area' => 'Activity Area',
            'secure_degaussing_area' => 'Secure Degaussing Area',
            'proper_ventilation' => 'Proper Ventilation',
            'trolley_for_movement' => 'Trolley For Movement',
            'power_supply_area' => 'Power Supply Area',
            'power_socket_to_machine_distance' => 'Power Socket To Machine Distance',
            'service_lift' => 'Service Lift',
            'lift_timings' => 'Lift Timings',
            'stairs_area' => 'Stairs Area',
            'how_to_do_machine_movement' => 'How To Do Machine Movement',
            'working_timings' => 'Working Timings',
            'extend_time_provision' => 'Extend Time Provision',
            'extension_provision' => 'Extension Provision',
            'hdd_sdd_removed_from_device' => 'Hdd Sdd Removed From Device',
            'who_will_remove_hdd_sdd' => 'Who Will Remove Hdd Sdd',
            'space_available_hdd_removal' => 'Space Available Hdd Removal',
            'activtiy_spoc' => 'Activtiy Spoc',
            'activtiy_spoc_email' => 'Activtiy Spoc Email',
            'activtiy_spoc_mobile' => 'Activtiy Spoc Mobile',
            'pickup_location_type' => 'Pickup Location Type',
            'pickup_location' => 'Pickup Location',
            'pickup_location_client' => 'Pickup Location Client',
            'pickup_location_engineer' => 'Pickup Location Engineer',
            'pickup_address' => 'Pickup Address',
            'pickup_city' => 'Pickup City',
            'pickup_state' => 'Pickup State',
            'pickup_pin' => 'Pickup Pin',
            'pickup_spoc' => 'Pickup Spoc',
            'pickup_spoc_number' => 'Pickup Spoc Number',
            'hsap_key_serial_num' => 'Hsap Key Serial Num',
            'hsap_key_image' => 'Hsap Key Image',
            'courrier_name' => 'Courrier Name',
            'docket_number' => 'Docket Number',
            'shipped_date' => 'Shipped Date',
            'gate_pass' => 'Gate Pass',
            'delivery_challan_invoice' => 'Delivery Challan Invoice',
            'delivery_location_type' => 'Delivery Location Type',
            'delivery_location_internal' => 'Delivery Location Internal',
            'delivery_location_client' => 'Delivery Location Client',
            'delivery_location_engineer' => 'Delivery Location Engineer',
            'delivery_address' => 'Delivery Address',
            'delivery_city' => 'Delivery City',
            'delivery_state' => 'Delivery State',
            'delivery_pin' => 'Delivery Pin',
            'receiver_spoc_name' => 'Receiver Spoc Name',
            'receiver_spoc_number' => 'Receiver Spoc Number',
            'delivery_date' => 'Delivery Date',
            'delivery_condition' => 'Delivery Condition',
            'hsap_key_receipient' => 'Hsap Key Receipient',
            'logistic_spoc_number' => 'Logistic Spoc Number',
            'activity_schedule_date' => 'Activity Schedule Date',
            'completed_date' => 'Completed Date',
            'fe_name' => 'Fe Name',
            'fe_number' => 'Fe Number',
            'hsap_key_require' => 'Hsap Key Require',
            'hsap_count' => 'Hsap Count',
            'dongle_pickup_date' => 'Dongle Pickup Date',
            'dongle_pickup_condition' => 'Dongle Pickup Condition',
        ];
    }

    /**
     * Gets query for [[ShreddingAssetDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getShreddingAssetDetails()
    {
        return $this->hasMany(ShreddingAssetDetails::class, ['shredding_id' => 'shredding_id']);
    }
    public function shreddingStageCalc($RecordId)
    {
        if(empty($RecordId)) return 2;
        $opportunity_name = $_POST["shredding"]["opportunity_name"]??null;
        $account_name = $_POST["shredding"]["account_name"]??null;
        $spoc_name = $_POST["shredding"]["spoc_name"]??null;
        $fe_name = $_POST["shredding"]["fe_name"]??null;
        $activity_location = $_POST["shredding"]["activity_location"]??null;
        $bill_location = $_POST["shredding"]["bill_location"]??null;
        $activity_schedule_date = $_POST["shredding"]["activity_schedule_date"]??null;
        $data = Shredding::findOne($RecordId);
        if(empty($data)) return 2;
        $current_stage = $data->shredding_status??null;
        if($current_stage == 2){
            if($opportunity_name && $account_name && $spoc_name && $fe_name && $activity_location && $bill_location && $activity_schedule_date){
                return 3;
            }
            return 2;
        }
        // $role = Yii::$app->user->identity->role ?? null;
        $role = Yii::$app->session->get('active_profile_id') ?? null;

        if($role == 'H56' && $current_stage == 3){
            return 4; //Shredding in process/ Activity in process
        }
        if($current_stage){
            return $current_stage;
        }
        return 2;
    }

    function saveToVpReports($RecordId)
    {
        $Record = (int)$RecordId;
        $this->saveVpShredding($RecordId);
    }
    
    function saveVpShredding($RecordId)
    {
        $sql = "SELECT shredding_no FROM shredding where shredding_id=:RecordId";
        $result = Yii::$app->db->createCommand($sql)->bindValue(":RecordId",$RecordId)->queryOne();
        $shredding_no= $result['shredding_no'];
        //delete old record
        $sql_del = "Delete from `rep_vp_shredding` where req_reference_no=:req_reference_no";
        Yii::$app->db->createCommand($sql_del)
        ->bindValue(":req_reference_no",$shredding_no)
        ->execute();

        $sql = "SELECT sourcingdeal.sourcingdeal_no,sourcingdeal.sourcingdeal_id, sourcingdeal.vendor_account_name as account_id,
            shredding.shredding_no,shredding.shredding_id,
            shredding.hdd_count,shredding.hdd_completed,GREATEST(shredding.hdd_count - shredding.hdd_completed, 0) AS hdd_pending, 
            shredding.shredding_status as status,shredding_status.Shredding_status_value as status_name 
            from shredding 
            left join shredding_status on shredding_status.Shredding_statusid = shredding.shredding_status
            left join sourcingdeal on sourcingdeal.sourcingdeal_id = shredding.opportunity_name 
            where shredding.shredding_id = :RecordId";
        $result = Yii::$app->db->createCommand($sql)->bindValue(":RecordId",$RecordId)->queryAll();
        
        foreach($result as $value)
        {
            $account_id = $value['account_id']?$value['account_id']:null;
            $sourcingdeal_no = $value['sourcingdeal_no']?$value['sourcingdeal_no']:null;
            $sourcingdeal_id = $value['sourcingdeal_id']?$value['sourcingdeal_id']:null;
            $shredding_no = $value['shredding_no'] ? $value['shredding_no']:null;
            $shredding_id = $value['shredding_id']?$value['shredding_id']:null;
            $hdd_count = $value['hdd_count']?$value['hdd_count'] : null;
            $hdd_completed = empty($value['hdd_completed'])?0 : $value['hdd_completed'];
            $hdd_pending = empty($value['hdd_pending']) ?0: $value['hdd_pending'];
            $status = $value['status'] ? $value['status'] : null;
            $status_name = $value['status_name'] ? $value['status_name'] : null;
            
            $sql_ins = "INSERT INTO `rep_vp_shredding` 
                SET account_id = :account_id,
                    req_reference_no = :req_reference_no,
                    sourcingdeal_no = :sourcingdeal_no,
                    sourcingdeal_id = :sourcingdeal_id,
                    shredding_id = :shredding_id,
                    hdd_count = :hdd_count,
                    hdd_completed = :hdd_completed,
                    hdd_pending = :hdd_pending,
                    status = :status,
                    status_name = :status_name,
                    created_on = NOW()";

            Yii::$app->db->createCommand($sql_ins)
                ->bindValue(":account_id", $account_id)
                ->bindValue(":req_reference_no", $shredding_no)
                ->bindValue(":sourcingdeal_no", $sourcingdeal_no)
                ->bindValue(":sourcingdeal_id", $sourcingdeal_id)
                ->bindValue(":shredding_id", $shredding_id)
                ->bindValue(":hdd_count", $hdd_count)
                ->bindValue(":hdd_completed", $hdd_completed)
                ->bindValue(":hdd_pending", $hdd_pending)
                ->bindValue(":status", $status)
                ->bindValue(":status_name", $status_name)
                ->execute();   
        }
    }
}
