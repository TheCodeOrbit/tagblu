<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "data_wiping".
 *
 * @property int $datawiping_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $data_wiping_no
 * @property string|null $billable
 * @property string|null $preferred_wiping_date
 * @property string|null $wiping_status
 * @property string|null $logistic_spoc_name
 * @property string|null $currency
 * @property string|null $opportunity_name
 * @property string|null $account_name
 * @property string|null $spoc_name
 * @property string|null $hdd_count
 * @property string|null $submit_approval
 * @property string|null $visible_status
 * @property string|null $spoc_mobile_number
 * @property string|null $email_date
 * @property string|null $customer_name
 * @property string|null $bill_location
 * @property string|null $bill_address
 * @property string|null $city
 * @property string|null $state
 * @property string|null $pincode
 * @property string|null $gstin_no
 * @property string|null $bill_pan_no
 * @property string|null $billing_hdd_count
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
 * @property string|null $data_wiping_owner
 * @property int $deleted
 */
class DataWiping extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'data_wiping';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime','email_confirmation'], 'safe'],
            [['data_wiping_no', 'po_number', 'image', 'billable', 'preferred_wiping_date', 'currency', 'opportunity_name', 'account_name', 'spoc_name', 'submit_approval', 'visible_status', 'spoc_mobile_number', 'email_date', 'customer_name', 'bill_location', 'city', 'state', 'pincode', 'gstin_no', 'taxable_value', 'total_cost', 'activity_location', 'activity_city', 'activity_state', 'activity_gstin_no',  'activity_pincode', 'activity_pan_no', 'exchange_rate', 'data_wiping_owner'], 'string', 'max' => 200],
            [['activity_address','bill_address'], 'string', 'max' => 3000],
            [['wiping_status','pickup_location_type','pickup_location_client','pickup_location_engineer','entry_formalities_person','material_location_floor','activity_area',
            'power_supply_area','wifi_service','machine_plug','num_of_days','working_timings','extend_time_provision',
            'extension_provision','machines_num_working_condition','hdd_count','hdd_completed','hdd_count_loose_unorganized','power_extension_provision',
            'activtiy_spoc','activtiy_spoc_email','activtiy_spoc_mobile','bill_spoc','bill_spoc_number','bill_spoc_email',
            'billing_amount','pickup_location','pickup_address','pickup_city','pickup_state','pickup_pin','pickup_spoc','pickup_spoc_number',
            'hsap_key_serial_num','hsap_key_image','courrier_name','docket_number','shipped_date','gate_pass','delivery_challan_invoice',
            'delivery_location_type','delivery_location_internal','delivery_location_client','delivery_location_engineer','delivery_address','delivery_city','delivery_state','delivery_pin','receiver_spoc_name','receiver_spoc_number','delivery_date','delivery_condition','hsap_key_receipient',
            'logistic_spoc_name','logistic_spoc_number','activity_schedule_date','completed_date','fe_name','fe_number',
            'hsap_key_require','hsap_count','dongle_pickup_date','dongle_pickup_condition'
            ], 'safe'],
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
            'datawiping_id' => 'Datawiping ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'data_wiping_no' => 'Data Wiping No',
            'pickup_location_type' => 'pickup_location_type',
            'billable' => 'Billable',
            'preferred_wiping_date' => 'Preferred Wiping Date',
            'wiping_status' => 'Wiping Status',
            'logistic_spoc_name' => 'Logistic Spoc Name',
            'currency' => 'Currency',
            'opportunity_name' => 'Opportunity Name',
            'account_name' => 'Account Name',
            'spoc_name' => 'Spoc Name',
            'hdd_count' => 'Hdd Count',
            'client_preferred_wiping_date' => 'Client Preferred Wiping Date',
            'client_logistic_spoc_name' => 'Client Logistic Spoc Name',
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
            'bill_spoc' => 'Billing SPOC Name',
            'hdd_completed' => 'HDD Completed',
            'bill_spoc_number' => 'Billing SPOC Number',
            'bill_spoc_email' => 'Billing SPOC Email',
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
            'data_wiping_owner' => 'Data Wiping Owner',
            'deleted' => 'Deleted',
            'pickup_location_client'=>'pickup_location_client',
            'pickup_location_engineer'=>'pickup_location_engineer',
            'po_number'=> 'Po Number', 
            'image'=>'Image',
            'entry_formalities_person' => 'What are the formalities for entry personnel',
            'material_location_floor' => 'Material stored at which location/floor',
            'activity_area' => 'Activity will be perform at which floor/area?',
            'power_supply_area' => 'Identify the secure designated area with enough power socket along with power supply',
            'wifi_service' => 'Do we have open/Guest WIFI service available',
            'machine_plug' => "How many machine's can we plug in at single point of time",
            'num_of_days' => 'No of days to complete the activity',
            'working_timings' => 'What are the working timings?',
            'extend_time_provision' => 'Do we have any provision to extend the timings',
            'extension_provision' => 'What is the procedure to inform/update regarding extention',
            'machines_num_working_condition' => 'How many machines are in working conditions',
            'hdd_count_loose_unorganized' => 'HDD Completed',
            'power_extension_provision' => 'Power extention provision in case power sockets are not available',
            'activtiy_spoc' => 'Activity SPOC',
            'activtiy_spoc_email' => 'Activity SPOC Email',
            'activtiy_spoc_mobile' => 'Activity SPOC Mobile'
        ];
    }

    public function dataWipingStageCalc($RecordId)
    {
        if(empty($RecordId)) return 2;
        $opportunity_name = $_POST["data_wiping"]["opportunity_name"]??null;
        $account_name = $_POST["data_wiping"]["account_name"]??null;
        $spoc_name = $_POST["data_wiping"]["spoc_name"]??null;
        $fe_name = $_POST["data_wiping"]["fe_name"]??null;
        $activity_location = $_POST["data_wiping"]["activity_location"]??null;
        $bill_location = $_POST["data_wiping"]["bill_location"]??null;
        $activity_schedule_date = $_POST["data_wiping"]["activity_schedule_date"]??null;
        $data = DataWiping::findOne($RecordId);
        if(empty($data)) return 2;
        $current_stage = $data->wiping_status??null;
        if($current_stage == 2){
            if($opportunity_name && $account_name && $spoc_name && $fe_name && $activity_location && $bill_location && $activity_schedule_date){
                return 3;
            }
            return 2;
        }
        // $role = Yii::$app->user->identity->role ?? null;
        $role = Yii::$app->session->get('active_profile_id') ?? null;
        
        if($role == 'H56' && $current_stage == 3){
            return 4; //Data Wiping in process/ Activity in process
        }
        if($current_stage){
            return $current_stage;
        }
        return 2;
    }

    function saveToVpReports($RecordId)
    {
        //echo "at a <br>";
        $Record = (int)$RecordId;
        $this->saveVpDataWiping($RecordId);
    }
    
    function saveVpDataWiping($RecordId)
    {
        $sql = "SELECT data_wiping_no FROM data_wiping where datawiping_id=:RecordId";
        $result = Yii::$app->db->createCommand($sql)->bindValue(":RecordId",$RecordId)->queryOne();
        $data_wiping_no= $result['data_wiping_no'];
        //delete old record
        $sql_del = "Delete from `rep_vp_data_wiping` where req_reference_no=:req_reference_no";
        Yii::$app->db->createCommand($sql_del)
        ->bindValue(":req_reference_no",$data_wiping_no)
        ->execute();

        $sql = "SELECT sourcingdeal.sourcingdeal_no,sourcingdeal.sourcingdeal_id, sourcingdeal.vendor_account_name as account_id,
            data_wiping.data_wiping_no,data_wiping.datawiping_id,
            data_wiping.hdd_count,data_wiping.hdd_completed,GREATEST(data_wiping.hdd_count - data_wiping.hdd_completed, 0) AS hdd_pending, 
            data_wiping.wiping_status as status,wiping_status.wiping_status_value as status_name 
            from data_wiping 
            left join wiping_status on wiping_status.wiping_statusid = data_wiping.wiping_status
            left join sourcingdeal on sourcingdeal.sourcingdeal_id = data_wiping.opportunity_name 
            where data_wiping.datawiping_id = :RecordId";
        $result = Yii::$app->db->createCommand($sql)->bindValue(":RecordId",$RecordId)->queryAll();
        //echo "at b ";exit;
        foreach($result as $value)
        {
            $account_id = $value['account_id']?$value['account_id']:null;
            $sourcingdeal_no = $value['sourcingdeal_no']?$value['sourcingdeal_no']:null;
            $sourcingdeal_id = $value['sourcingdeal_id']?$value['sourcingdeal_id']:null;
            $data_wiping_no = $value['data_wiping_no'] ? $value['data_wiping_no']:null;
            $datawiping_id = $value['datawiping_id']?$value['datawiping_id']:null;
            $hdd_count = $value['hdd_count']?$value['hdd_count'] : null;
            $hdd_completed = empty($value['hdd_completed'])?0 : $value['hdd_completed'];
            $hdd_pending = empty($value['hdd_pending']) ?0: $value['hdd_pending'];
            $status = $value['status'] ? $value['status'] : null;
            $status_name = $value['status_name'] ? $value['status_name'] : null;
            
            $sql_ins = "INSERT INTO `rep_vp_data_wiping` 
                SET account_id = :account_id,
                    req_reference_no = :req_reference_no,
                    sourcingdeal_no = :sourcingdeal_no,
                    sourcingdeal_id = :sourcingdeal_id,
                    datawiping_id = :datawiping_id,
                    hdd_count = :hdd_count,
                    hdd_completed = :hdd_completed,
                    hdd_pending = :hdd_pending,
                    status = :status,
                    status_name = :status_name,
                    created_on = NOW()";

            Yii::$app->db->createCommand($sql_ins)
                ->bindValue(":account_id", $account_id)
                ->bindValue(":req_reference_no", $data_wiping_no)
                ->bindValue(":sourcingdeal_no", $sourcingdeal_no)
                ->bindValue(":sourcingdeal_id", $sourcingdeal_id)
                ->bindValue(":datawiping_id", $datawiping_id)
                ->bindValue(":hdd_count", $hdd_count)
                ->bindValue(":hdd_completed", $hdd_completed)
                ->bindValue(":hdd_pending", $hdd_pending)
                ->bindValue(":status", $status)
                ->bindValue(":status_name", $status_name)
                ->execute();   
        }
    }
}


