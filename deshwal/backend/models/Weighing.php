<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "weighing".
 *
 * @property int $weighing_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property int|null $currency
 * @property int|null $weighing_no
 * @property string|null $agreement
 * @property string|null $po_received
 * @property string|null $billable
 * @property string|null $preferred_weighing_date
 * @property string|null $weighing_status
 * @property int $related_to
 * @property int $related_to_id
 * @property string|null $logistic_Spoc_name
 * @property string|null $visible_status
 * @property string|null $agreement_copy
 * @property string|null $po_number
 * @property string|null $image
 * @property string|null $email
 * @property string|null $weighing_owner
 * @property string|null $exchange_rate
 * @property string|null $opportunity_name
 * @property string|null $spoc_name
 * @property string|null $hdd_count
 * @property string|null $submit_approval
 * @property string|null $account_name
 * @property string|null $spoc_mobile_number
 * @property string|null $activity_location
 * @property string|null $activity_address
 * @property string|null $activity_pincode
 * @property string|null $activity_city
 * @property string|null $activity_state
 * @property string|null $activity_state_code
 * @property string|null $activity_gstin_no
 * @property int $deleted
 */
class Weighing extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'weighing';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime', 'related_to', 'related_to_id'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'currency', 'weighing_no', 'related_to', 'related_to_id', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime'], 'safe'],
            [['agreement', 'po_received', 'billable', 'preferred_weighing_date', 'weighing_status', 'logistic_Spoc_name', 'visible_status', 'agreement_copy', 'po_number', 'image', 'email', 'weighing_owner', 'exchange_rate', 'opportunity_name', 'spoc_name', 'hdd_count', 'submit_approval', 'account_name', 'spoc_mobile_number', 'activity_location', 'activity_pincode', 'activity_city', 'activity_state', 'activity_state_code', 'activity_gstin_no'], 'string', 'max' => 200],
            [[ 'activity_address'], 'string', 'max' => 3000],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'weighing_id' => 'Weighing ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'currency' => 'Currency',
            'weighing_no' => 'Weighing No',
            'agreement' => 'Agreement',
            'po_received' => 'Po Received',
            'billable' => 'Billable',
            'preferred_weighing_date' => 'Preferred Weighing Date',
            'weighing_status' => 'Weighing Status',
            'related_to' => 'Related To',
            'related_to_id' => 'Related To ID',
            'logistic_Spoc_name' => 'Logistic Spoc Name',
            'visible_status' => 'Visible Status',
            'agreement_copy' => 'Agreement Copy',
            'po_number' => 'Po Number',
            'image' => 'Image',
            'email' => 'Email',
            'weighing_owner' => 'Weighing Owner',
            'exchange_rate' => 'Exchange Rate',
            'opportunity_name' => 'Opportunity Name',
            'spoc_name' => 'Spoc Name',
            'hdd_count' => 'Hdd Count',
            'submit_approval' => 'Submit Approval',
            'account_name' => 'Account Name',
            'spoc_mobile_number' => 'Spoc Mobile Number',
            'activity_location' => 'Activity Location',
            'activity_address' => 'Activity Address',
            'activity_pincode' => 'Activity Pincode',
            'activity_city' => 'Activity City',
            'activity_state' => 'Activity State',
            'activity_state_code' => 'Activity State Code',
            'activity_gstin_no' => 'Activity Gstin No',
            'deleted' => 'Deleted',
        ];
    }
}
