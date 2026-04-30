<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "meeting_information".
 *
 * @property int $meetinginfo_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property int|null $internal_participants
 * @property int|null $external_participants
 * @property int|null $related_to
 * @property int $related_to_id
 * @property int|null $participants_reminder
 * @property int|null $currency
 * @property int|null $remainder
 * @property string|null $contact_name
 * @property string|null $account_name
 * @property int|null $LOB_discussed
 * @property int|null $conveyance_required
 * @property int|null $solution_architect
 * @property string $title
 * @property string|null $location
 * @property int|null $all_day
 * @property string|null $from
 * @property string|null $to
 * @property string|null $host
 * @property string|null $internal_comments
 * @property string|null $external_comments
 * @property string|null $from_location
 * @property string|null $to_location
 * @property int $confirms
 * @property string|null $distance1
 * @property int|null $MOM_shared
 * @property string|null $description
 * @property int|null $expence_category
 * @property string|null $expence_type
 * @property int $submit_approval
 * @property int|null $repeat
 * @property int|null $tax_type
 * @property string|null $expence_date
 * @property string|null $repeat_type
 * @property string|null $test_name
 * @property int $deleted
 */
class MeetingInformation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'meeting_information';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mom_date', 'mom_time', 'attendees', 'discussion_points', 'next_action'], 'default', 'value' => null],
            [['discussion_points', 'next_action'], 'string'],
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime', 'title'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby',  'related_to', 'related_to_id', 'participants_reminder', 'remainder', 'conveyance_required', 'solution_architect', 'all_day', 'confirms', 'MOM_shared', 'expence_category', 'submit_approval', 'repeat', 'tax_type', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime', 'from', 'to', 'expence_date','mom_date', 'mom_time'], 'safe'],
            [['account_name', 'title', 'location', 'host', 'internal_participants', 'external_participants', 'internal_comments', 'external_comments', 'from_location', 'to_location', 'description', 'expence_type', 'repeat_type','attendees'], 'string', 'max' => 200],
            [['distance1'], 'string', 'max' => 100],
            [['meeting_no'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'meetinginfo_id' => 'Meetinginfo ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'internal_participants' => 'Internal Participants',
            'external_participants' => 'External Participants',
            'related_to' => 'Related To',
            'related_to_id' => 'Related To ID',
            'participants_reminder' => 'Participants Reminder',
            'remainder' => 'Remainder',
            'account_name' => 'Account Name',
            'conveyance_required' => 'Conveyance Required',
            'solution_architect' => 'Solution Architect',
            'title' => 'Title',
            'location' => 'Location',
            'all_day' => 'All Day',
            'from' => 'From',
            'to' => 'To',
            'host' => 'Host',
            'internal_comments' => 'Internal Comments',
            'external_comments' => 'External Comments',
            'from_location' => 'From Location',
            'to_location' => 'To Location',
            'confirms' => 'Confirms',
            'distance1' => 'Distance1',
            'MOM_shared' => 'Mom Shared',
            'description' => 'Description',
            'expence_category' => 'Expence Category',
            'expence_type' => 'Expence Type',
            'submit_approval' => 'Submit Approval',
            'repeat' => 'Repeat',
            'tax_type' => 'Tax Type',
            'expence_date' => 'Expence Date',
            'repeat_type' => 'Repeat Type',
            'deleted' => 'Deleted',
            'meeting_no'=>'Meeting No',
            //added for MOM block
            'mom_date' => 'Mom Date',
            'mom_time' => 'Mom Time',
            'attendees' => 'Attendees',
            'discussion_points' => 'Discussion Points',
            'next_action' => 'Next Action',
            
        ];
    }
}
