<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "shredding_information".
 *
 * @property int $shreddinginfo_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $currency
 * @property string|null $agreement
 * @property string|null $billable
 * @property string|null $preferred_shredding_date
 * @property string|null $shredding_status
 * @property int $related_to
 * @property int $related_to_id
 * @property string|null $logistic_spoc_name
 * @property string|null $email
 * @property string|null $shredding_owner
 * @property string|null $opportunity_name
 * @property string|null $spoc_name
 * @property string|null $hdd_count
 * @property string|null $submit_approval
 * @property string|null $account_name
 * @property string|null $spoc_mobile_number
 * @property string|null $activity_location
 * @property string|null $activity_address
 * @property string|null $activity_state
 * @property string|null $activity_pan_no
 * @property string|null $activity_city
 * @property int|null $activity_pincode
 * @property int|null $activity_gstin_no
 * @property int $deleted
 */
class ShreddingInformation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shredding_information';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime', 'related_to', 'related_to_id'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'related_to', 'related_to_id', 'activity_pincode', 'activity_gstin_no', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime'], 'safe'],
            [['currency', 'agreement', 'billable', 'preferred_shredding_date', 'shredding_status', 'logistic_spoc_name', 'email', 'shredding_owner', 'opportunity_name', 'spoc_name', 'hdd_count', 'submit_approval', 'account_name', 'spoc_mobile_number', 'activity_location', 'activity_address', 'activity_state', 'activity_pan_no', 'activity_city'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'shreddinginfo_id' => 'Shreddinginfo ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'currency' => 'Currency',
            'agreement' => 'Agreement',
            'billable' => 'Billable',
            'preferred_shredding_date' => 'Preferred Shredding Date',
            'shredding_status' => 'Shredding Status',
            'related_to' => 'Related To',
            'related_to_id' => 'Related To ID',
            'logistic_spoc_name' => 'Logistic Spoc Name',
            'email' => 'Email',
            'shredding_owner' => 'Shredding Owner',
            'opportunity_name' => 'Opportunity Name',
            'spoc_name' => 'Spoc Name',
            'hdd_count' => 'Hdd Count',
            'submit_approval' => 'Submit Approval',
            'account_name' => 'Account Name',
            'spoc_mobile_number' => 'Spoc Mobile Number',
            'activity_location' => 'Activity Location',
            'activity_address' => 'Activity Address',
            'activity_state' => 'Activity State',
            'activity_pan_no' => 'Activity Pan No',
            'activity_city' => 'Activity City',
            'activity_pincode' => 'Activity Pincode',
            'activity_gstin_no' => 'Activity Gstin No',
            'deleted' => 'Deleted',
        ];
    }
}
