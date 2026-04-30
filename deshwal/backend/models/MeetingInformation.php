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
 * @property string|null $meeting_no
 * @property string|null $internal_participants
 * @property string|null $external_participants
 * @property int|null $related_to
 * @property int $related_to_id
 * @property int|null $participants_reminder
 * @property int|null $remainder
 * @property string|null $account_name
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
 * @property string|null $expence_category
 * @property string|null $expence_type
 * @property int $submit_approval
 * @property int|null $repeat
 * @property int|null $tax_type
 * @property string|null $expence_date
 * @property string|null $repeat_type
 * @property string|null $mom_date
 * @property string|null $mom_time
 * @property string|null $attendees
 * @property string|null $discussion_points
 * @property string|null $next_action
 * @property int $deleted
 * @property int|null $type_of_meeting
 * @property int|null $type_of_engagement
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
            [['meeting_no', 'internal_participants', 'external_participants', 'related_to', 'participants_reminder', 'remainder', 'account_name', 'conveyance_required', 'solution_architect', 'location', 'all_day', 'from', 'to', 'host', 'internal_comments', 'external_comments', 'from_location', 'to_location', 'distance1', 'description', 'expence_category', 'expence_type', 'repeat', 'tax_type', 'expence_date', 'repeat_type', 'mom_date', 'mom_time', 'attendees', 'discussion_points', 'next_action', 'type_of_meeting', 'type_of_engagement','account_name_manual','external_participants_manual'], 'default', 'value' => null],
            [['deleted'], 'default', 'value' => 0],
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime', 'related_to_id', 'title'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'related_to', 'related_to_id', 'participants_reminder', 'remainder', 'conveyance_required', 'solution_architect', 'all_day', 'confirms', 'MOM_shared', 'submit_approval', 'repeat', 'tax_type', 'deleted', 'type_of_meeting', 'type_of_engagement'], 'integer'],
            [['createdtime', 'modifiedtime', 'from', 'to', 'expence_date', 'mom_date', 'mom_time'], 'safe'],
            [['discussion_points', 'next_action'], 'string'],
            [['meeting_no', 'account_name', 'title', 'location', 'host', 'internal_comments', 'external_comments', 'from_location', 'to_location', 'description', 'expence_category', 'expence_type', 'repeat_type', 'attendees'], 'string', 'max' => 200],
            [['internal_participants', 'external_participants', 'distance1'], 'string', 'max' => 100],
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
            'meeting_no' => 'Meeting No',
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
            'mom_date' => 'Mom Date',
            'mom_time' => 'Mom Time',
            'attendees' => 'Attendees',
            'discussion_points' => 'Discussion Points',
            'next_action' => 'Next Action',
            'deleted' => 'Deleted',
            'type_of_meeting' => 'Type Of Meeting',
            'type_of_engagement' => 'Type Of Engagement',
        ];
    }

}
