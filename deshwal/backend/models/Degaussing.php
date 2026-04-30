<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "degaussing".
 *
 * @property int $degaussinginfo_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedbyactivity_schedule_date
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string $degaussing_no
 * @property string|null $currency
 * @property string|null $billable
 * @property string|null $activity_schedule_date
 * @property string|null $degaussing_status
 * @property string|null $logistic_spoc_name
 * @property string|null $email
 * @property string|null $exchange_rate
 * @property string|null $opportunity_name
 * @property string|null $spoc_name
 * @property string|null $hdd_count
 * @property string|null $account_name
 * @property string|null $spoc_mobile_number
 * @property string|null $submit_approval
 * @property string|null $activity_spoc_email
 * @property string|null $activity_location
 * @property string|null $activity_address
 * @property string|null $activity_state
 * @property string|null $activity_spoc
 * @property string|null $activity_city
 * @property string|null $activity_pincode
 * @property string|null $activity_spoc_mobile
 * @property string|null $bill_location
 * @property string|null $bill_spoc
 * @property string|null $bill_spoc_number
 * @property string|null $bill_address
 * @property string|null $city
 * @property string|null $state
 * @property string|null $pincode
 * @property string|null $gstin_no_uin
 * @property string|null $bill_spoc_email
 * @property int $deleted
 */
class Degaussing extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'degaussing';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime', 'degaussing_no'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime','entry_formalities_person','material_location_floor','activity_area',
            'secure_degaussing_area','proper_ventilation','power_socket_to_machine_distance','service_lift','lift_timings',
            'stairs_area','how_to_do_machine_movement','working_timings','extend_time_provision','extension_provision',
            'hdd_sdd_removed_from_device','who_will_remove_hdd_sdd','space_available_hdd_removal',
            'activity_spoc','activity_spoc_mobile','activity_spoc_email','hdd_completed','bill_location','bill_address',
            'bill_spoc','bill_spoc_number','bill_spoc_email','billing_amount','billing_type',
            'logistic_spoc_name','logistic_spoc_number','activity_schedule_date','completed_date','fe_name','fe_number','machine_movement',
            'pickup_location_type','pickup_location','pickup_location_client','pickup_location_engineer',
            'pickup_address','pickup_city','pickup_state','pickup_pin','pickup_spoc','pickup_spoc_number',
            'machine_serial_num','machine_model','machine_image','courrier_name','docket_number','shipped_date','gate_pass',
            'delivery_challan_invoice','delivery_location_type','delivery_location_internal','delivery_location_client',
            'delivery_location_engineer','delivery_address','delivery_city','delivery_state',
            'delivery_pin','receiver_spoc_name','receiver_spoc_number','delivery_date','delivery_condition',
            'machine_image_receipient','image'], 'safe'],
            [['degaussing_no', 'currency', 'billable', 'degaussing_status', 'exchange_rate', 'opportunity_name', 'spoc_name', 
            'hdd_count', 'account_name', 'spoc_mobile_number', 'activity_location', 'activity_address', 'activity_state', 
            'activity_city', 'activity_pincode', 'city', 'state', 'pincode', 'gstin_no_uin'], 'safe'],
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
            'degaussinginfo_id' => 'Degaussinginfo ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'degaussing_no' => 'Degaussing No',
            'currency' => 'Currency',
            'billable' => 'Billable',
            'activity_schedule_date' => 'activity_schedule_date',
            'degaussing_status' => 'Degaussing Status',
            'logistic_spoc_name' => 'Logistic Spoc Name',
            'logistic_spoc_number' => 'logistic_spoc_number',
            'exchange_rate' => 'Exchange Rate',
            'opportunity_name' => 'Opportunit Name',
            'spoc_name' => 'Spoc Name',
            'hdd_count' => 'Hdd Count',
            'account_name' => 'Account Name',
            'spoc_mobile_number' => 'Spoc Mobile Number',
            'hdd_completed' => 'hdd_completed', 
            'activity_spoc_email' => 'activity_spoc_email',
            'activity_location' => 'Activity Location',
            'activity_address' => 'Activity Address',
            'activity_state' => 'Activity State',
            'activity_spoc' => 'activity_spoc',
            'activity_city' => 'Activity City',
            'activity_pincode' => 'Activity Pincode',
            'activity_spoc_mobile' => 'activity_spoc_mobile',
            'bill_location' => 'Bill Location',
            'bill_spoc' => 'bill_spoc',
            'bill_spoc_number' => 'bill_spoc_number',
            'bill_address' => 'Bill Address',
            'city' => 'City',
            'state' => 'State',
            'pincode' => 'Pincode',
            'gstin_no_uin' => 'Gstin No Uin',
            'bill_spoc_email' => 'bill_spoc_email',
            'deleted' => 'Deleted',
        ];
    }

    public function stageCalc($RecordId)
    {
        if(empty($RecordId)) return 2;
        $opportunity_name = $_POST["degaussing"]["opportunity_name"]??null;
        $account_name = $_POST["degaussing"]["account_name"]??null;
        $spoc_name = $_POST["degaussing"]["spoc_name"]??null;
        $fe_name = $_POST["degaussing"]["fe_name"]??null;
        $activity_location = $_POST["degaussing"]["activity_location"]??null;
        $bill_location = $_POST["degaussing"]["bill_location"]??null;
        $activity_schedule_date = $_POST["degaussing"]["activity_schedule_date"]??null;
        $data = Degaussing::findOne($RecordId);
        if(empty($data)) return 2;
        $current_stage = $data->degaussing_status??null;
        if($current_stage == 2){
            if($opportunity_name && $account_name && $spoc_name && $fe_name && $activity_location && $bill_location && $activity_schedule_date){
                return 3;
            }
            return 2;
        }
        // $role = Yii::$app->user->identity->role ?? null;
        $role = Yii::$app->session->get('active_profile_id') ?? null;

        if($role == 'H56' && $current_stage == 3){
            return 4; //in process/ Activity in process
        }
        if($current_stage){
            return $current_stage;
        }
        return 2;
    }
    function saveToVpReports($RecordId)
    {
        $Record = (int)$RecordId;
        $this->saveVpDegaussing($RecordId);
    }
    
    function saveVpDegaussing($RecordId)
    {
        $sql = "SELECT degaussing_no FROM degaussing where degaussinginfo_id=:RecordId";
        $result = Yii::$app->db->createCommand($sql)->bindValue(":RecordId",$RecordId)->queryOne();
        $degaussing_no= $result['degaussing_no'];
        //delete old record
        $sql_del = "Delete from `rep_vp_degaussing` where req_reference_no=:req_reference_no";
        Yii::$app->db->createCommand($sql_del)
        ->bindValue(":req_reference_no",$degaussing_no)
        ->execute();

        $sql = "SELECT sourcingdeal.sourcingdeal_no,sourcingdeal.sourcingdeal_id, sourcingdeal.vendor_account_name as account_id,
            degaussing.degaussing_no,degaussing.degaussinginfo_id,
            degaussing.hdd_count,degaussing.hdd_completed,GREATEST(degaussing.hdd_count - degaussing.hdd_completed, 0) AS hdd_pending, 
            degaussing.degaussing_status as status,degaussing_status.degaussing_status_value as status_name 
            from degaussing 
            left join degaussing_status on degaussing_status.degaussingstatusid = degaussing.degaussing_status
            left join sourcingdeal on sourcingdeal.sourcingdeal_id = degaussing.opportunity_name 
            where degaussing.degaussinginfo_id = :RecordId";
        $result = Yii::$app->db->createCommand($sql)->bindValue(":RecordId",$RecordId)->queryAll();
        
        foreach($result as $value)
        {
            $account_id = $value['account_id']?$value['account_id']:null;
            $sourcingdeal_no = $value['sourcingdeal_no']?$value['sourcingdeal_no']:null;
            $sourcingdeal_id = $value['sourcingdeal_id']?$value['sourcingdeal_id']:null;
            $degaussing_no = $value['degaussing_no'] ? $value['degaussing_no']:null;
            $degaussinginfo_id = $value['degaussinginfo_id']?$value['degaussinginfo_id']:null;
            $hdd_count = $value['hdd_count']?$value['hdd_count'] : null;
            $hdd_completed = empty($value['hdd_completed'])?0 : $value['hdd_completed'];
            $hdd_pending = empty($value['hdd_pending']) ?0: $value['hdd_pending'];
            $status = $value['status'] ? $value['status'] : null;
            $status_name = $value['status_name'] ? $value['status_name'] : null;
            
            $sql_ins = "INSERT INTO `rep_vp_degaussing` 
                SET account_id = :account_id,
                    req_reference_no = :req_reference_no,
                    sourcingdeal_no = :sourcingdeal_no,
                    sourcingdeal_id = :sourcingdeal_id,
                    degaussinginfo_id = :degaussinginfo_id,
                    hdd_count = :hdd_count,
                    hdd_completed = :hdd_completed,
                    hdd_pending = :hdd_pending,
                    status = :status,
                    status_name = :status_name,
                    created_on = NOW()";

            Yii::$app->db->createCommand($sql_ins)
                ->bindValue(":account_id", $account_id)
                ->bindValue(":req_reference_no", $degaussing_no)
                ->bindValue(":sourcingdeal_no", $sourcingdeal_no)
                ->bindValue(":sourcingdeal_id", $sourcingdeal_id)
                ->bindValue(":degaussinginfo_id", $degaussinginfo_id)
                ->bindValue(":hdd_count", $hdd_count)
                ->bindValue(":hdd_completed", $hdd_completed)
                ->bindValue(":hdd_pending", $hdd_pending)
                ->bindValue(":status", $status)
                ->bindValue(":status_name", $status_name)
                ->execute();   
        }
    }
}
