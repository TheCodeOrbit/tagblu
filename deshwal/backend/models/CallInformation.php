<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "call_information".
 *
 * @property int $callinfo_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string $subject
 * @property int $deleted
 * @property string|null $comments
 * @property string|null $call_to
 * @property string|null $call_to_id
 * @property int|null $account_name
 * @property string $call_type
 * @property int $outgoing_call_status
 * @property string $call_start_time
 * @property string|null $call_end_time
 * @property string $call_duration
 * @property int $related_to
 * @property int $related_to_id
 * @property string|null $voice_recording
 * @property string|null $call_purpose
 * @property string|null $call_agenda
 * @property string|null $team1
 * @property string|null $call_result
 * @property string|null $upd
 */
class CallInformation extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'call_information';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['comments', 'call_to', 'call_to_id',  'call_end_time','account_name', 'voice_recording', 'call_purpose', 'call_agenda', 'team1', 'call_result', 'upd','type_of_engagement','acc_name'], 'default', 'value' => null],
            [['deleted'], 'default', 'value' => 0],
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime', 'subject', 'call_type', 'outgoing_call_status', 'call_start_time', 'call_duration', 'related_to', 'related_to_id'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'deleted', 'account_name', 'outgoing_call_status', 'related_to', 'related_to_id','type_of_engagement'], 'integer'],
            [['createdtime', 'modifiedtime', 'call_start_time', 'call_end_time'], 'safe'],
            [['comments'], 'string'],
            [['subject', 'call_to_id', 'call_type', 'call_purpose', 'call_agenda', 'team1', 'call_result', 'upd','acc_name'], 'string', 'max' => 200],
            [['call_to'], 'string', 'max' => 100],
            [['call_duration'], 'string', 'max' => 10],
            [['voice_recording'], 'string', 'max' => 255],
            [['call_no'], 'string', 'max' => 200],
            // added for handling blank values saving in by ptpatel on date 24-01-2026
            // [['account_name'], 'trim'],
            // [['account_name'], 'required', 'message' => 'Account Name cannot be blank.'],
            // [['account_name'], 'integer', 'message' => 'Account Name must be a number.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'callinfo_id' => 'Callinfo ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'subject' => 'Subject',
            'deleted' => 'Deleted',
            'comments' => 'Comments',
            'call_to' => 'Call To',
            'call_to_id' => 'Call To ID',
            'account_name' => 'Account Name',
            'call_type' => 'Call Type',
            'outgoing_call_status' => 'Outgoing Call Status',
            'call_start_time' => 'Call Start Time',
            'call_end_time' => 'Call End Time',
            'call_duration' => 'Call Duration',
            'related_to' => 'Related To',
            'related_to_id' => 'Related To ID',
            'voice_recording' => 'Voice Recording',
            'call_purpose' => 'Call Purpose',
            'call_agenda' => 'Call Agenda',
            'team1' => 'Team1',
            'call_result' => 'Call Result',
            'upd' => 'Upd',
            'call_no' => 'Call No',
            'type_of_engagement' => 'Type Of Engagement',
            'acc_name'=>'Account Name',
        ];
    }

    public function getModtrackerBasic()
    {
        return $this->hasOne(ModtrackerBasic::className(), ['crmid' => 'callinfo_id'])
            ->andOnCondition(['module' => 'calls']);
    }
}
