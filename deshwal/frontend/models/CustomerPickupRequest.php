<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "customer_pickup_request".
 *
 * @property int $pickup_request_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $account_name
 * @property string|null $location
 * @property string|null $address
 * @property string|null $city
 * @property string|null $state
 * @property string|null $pincode
 * @property string|null $spoc_name
 * @property string|null $spoc_number
 * @property string|null $spoc_email
 * @property string|null $escalation_name
 * @property string|null $escalation_number
 * @property string|null $escalation_email
 * @property string|null $sender_email_phone
 * @property string|null $preferred_pickup_date
 * @property int $pickup_required
 * @property string|null $remarks
 * @property int|null $location_type
 * @property int|null $additional_info
 * @property int|null $doc_received
 * @property string|null $pickup_document
 * @property int $deleted
 */
class CustomerPickupRequest extends \yii\db\ActiveRecord
{
    // public $pickup_document = [];
    // public $additional_info = [];
    public function beforeSave($insert)
    {
        if ($insert) { // If new record
            $this->ownerid = Yii::$app->user->id; // Set ownerid from logged-in user
            $this->creatorid = Yii::$app->user->id; // Set creatorid
            $this->createdtime = date('Y-m-d H:i:s'); // Set current timestamp
        }
        
        // Update common fields
        $this->modifiedby = Yii::$app->user->id; // Set modifiedby
        $this->modifiedtime = date('Y-m-d H:i:s'); // Set current timestamp
        
        return parent::beforeSave($insert);
    }
    public function getPickupItems()
    {
        return $this->hasMany(CustomerPickupAssets::class, ['pickup_request_id' => 'pickup_request_id']);
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customer_pickup_request';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime','location'], 'required'],
            ['spoc_email', 'email'],
            [['escalation_email','alternate_email'], 'email'],
            [['spoc_name', 'escalation_name','alternate_name'], 'match', 'pattern' => '/^[a-zA-Z\s]+$/', 'message' => 'Name can only contain letters and spaces'],
            [['spoc_name', 'escalation_name','alternate_name'], 'string', 'min' => 3, 'max' => 200], // Min 3 and max 50 characters if entered
            [['spoc_number','escalation_number','alternate_mobile'], 'match', 'pattern' => '/^[6-9]\d{9}$/', 'message' => 'Mobile number must be 10 digits and start with 6-9'],
            [['spoc_number','escalation_number','alternate_mobile'], 'string', 'min' => 10, 'max' => 10], // Ensures 10 digits if provided
            [['ownerid', 'creatorid', 'modifiedby', 'pickup_required', 'location_type', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime', 'preferred_pickup_date'], 'safe'],
            [['location', 'city', 'state', 'remarks','floor_num_material_count'], 'string', 'max' => 200],
            [['address'], 'string', 'max' => 1000],
            [['pincode'], 'string', 'max' => 6],
            [['spoc_email', 'escalation_email'], 'string', 'max' => 100],
            [['sender_email_phone'], 'string', 'max' => 500],
            [['doc_received'], 'file', 'skipOnEmpty' => true, 'extensions' => 'jpg, png, pdf', 'maxSize' => 1024 * 1024 * 2], // Max 2MB
            [['add_to_permanent_data','status'], 'integer'], // Ensure it's treated as an integer (0 or 1)

            // Conditionally required fields if add_to_permanent_data is checked (1)
            [['alternate_name', 'alternate_email', 'alternate_mobile'], 'required', 
                'when' => function($model) {
                    return $model->add_to_permanent_data == 1; // Check explicitly for 1
                }, 
                'whenClient' => "function (attribute, value) { 
                    return $('#customerpickuprequest-add_to_permanent_data').is(':checked');
                }"
            ],
            [['preferred_pickup_date', 'pickup_document','working_timings','entry_formalities_person','vehicle_entry_formalities'], 'required', 
                'when' => function ($model) {
                    return Yii::$app->request->post('action') === 'submit';
                }, 'whenClient' => "function (attribute, value) {
                    return $('input[name=\"action\"]').val() === 'submit';
                }"
            ],
            [['doc_received'], 'required', 
                'when' => function ($model) {
                    return in_array(3, explode(',', $model->pickup_document));
                }, 'whenClient' => "function (attribute, value) {
                    var selectedValues = $('#customerpickuprequest-pickup_document').val();
                    return selectedValues && selectedValues.includes('3'); // Client-side check
                }"
            ],
            ['terms_and_condition', 'required',
                'requiredValue' => 1, // Checkbox must be checked (value=1)
                'message' => 'You must agree to the terms before submitting.',
                'when' => function ($model) {
                    return Yii::$app->request->post('action') === 'submit';
                },
                'whenClient' => "function (attribute, value) {
                    return $('input[name=\"action\"]').val() === 'submit' && !$('input[name=\"terms_and_condition\"]').prop('checked');
                }"
            ],
            [
                ['account_name','pickup_request','pickup_document','additional_info','city_state','country',
                'working_timings','extend_time_provision','extension_provision','entry_formalities_person',
                'material_location_floor','material_floor',"service_lift","lift_timing","stairs_space","material_move","segregation",
                "space_for_segregation","movement_from_premises","distance","floor_num_for_take_out","space_for_vehicle","small_vehicle",
                "vehicle_as_per_height","material_from_basement_to_grnd","vehicle_entry_formalities","vehicle_inside_premises",
                "terms_and_condition","assigned_to"],
                'safe'
            ]
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pickup_request_id' => 'Pickup ID',
            'pickup_request' => "Pickup Request ID",
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
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
            'escalation_number' => 'Escalation Mobile',
            'escalation_email' => 'Escalation Email',
            'sender_email_phone' => 'Sender Email Phone',
            'preferred_pickup_date' => 'Agreed / Requested Collection Date',
            'pickup_required' => 'Pickup Required',
            'remarks' => 'Remarks',
            'location_type' => 'Location Type',
            'additional_info' => 'Safety Info',
            'doc_received' => 'Upload Contract Transit Document',
            'pickup_document' => 'Pickup Document Required',
            'deleted' => 'Deleted',
            'alternate_name' => "Alternate Name",
            'alternate_email' => "Alternate Email",
            'alternate_mobile' => "Alternate Mobile",
            'add_to_permanent_data' => "Add to permanent data",
            'city_state' => "City State",
            'country' => "Country",
            'status' => "Pickup Request Status",
            'working_timings' =>"What are the working timings?",
            'extend_time_provision' => "Do we have any provision to extend the timings", //d 
            'extension_provision' => "What is the procedure to inform/update regarding extention", //d
            'entry_formalities_person' => "What are the formalities for entry personnel",//d
            'material_location_floor'=> "Material lying at which location/Floor", //d
            'material_floor' => "At which floor all the material is stored?",
            "floor_num_material_count" => "Please share the floor Number with material count",
            "service_lift" => "Do we have service lift available",
            "lift_timing" => "What are the lift timings?",
            "stairs_space" => "Does stairs has sufficient space from where we can move the the material out from the premises?",
            "material_move" =>"How we can move the mateiral out",
            "segregation" => "All items are segerated or Segregation require",
            "space_for_segregation" => "Do we have space availbale for this segregation",
            "movement_from_premises" => "What is the material movement from premises?",
            "distance"=> "Distance between material and vehicle parked",
            "floor_num_for_take_out" => "Please share the basement floor / number from where we need to take out the mateiral",
            "space_for_vehicle" => "Do facility has sufficient space that vehicle can go inside the basement to pick the material",
            "small_vehicle" => "Do we require small vehicle for this movement",
            "vehicle_as_per_height" => "Please share the vehicle Name/Size which is allowed as per height",
            "material_from_basement_to_grnd" => "Please share how we can move the material from basement to Ground floor where vehicle parked?",
            "vehicle_entry_formalities" => "What are the formalities for vehicle entry",
            "vehicle_inside_premises" => "Vehicle can parked inside the premises",
            "terms_and_condition" =>"Count me in! I'd like to receive insights into extending the lifecycle of electronics in data centers and businesses.",
            "assigned_to" => "Assigned To"
        ];
    }
}


