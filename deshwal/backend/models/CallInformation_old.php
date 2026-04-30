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
 * @property string $comments
 * @property string|null $call_to
 * @property string|null $call_to_id
 * @property string $call_type
 * @property int $outgoing_call_status
 * @property string $call_start_time
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

            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime', 'subject', 'comments', 'call_type', 'outgoing_call_status', 'call_start_time',  'related_to', 'related_to_id'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'deleted', 'outgoing_call_status', 'related_to', 'related_to_id','account_name'], 'integer'],
            [['createdtime', 'modifiedtime', 'call_start_time','call_end_time'], 'safe'],
            [['subject', 'comments', 'call_to', 'call_to_id', 'call_type', 'call_purpose', 'call_agenda', 'team1', 'call_result', 'upd'], 'string', 'max' => 200],
            [['call_duration'], 'string', 'max' => 10],
            [['voice_recording'], 'string', 'max' => 255],

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

            'call_type' => 'Call Type',
            'call_start_time' => 'Call Start Time',
            'call_end_time'=>'Call End Time',
            'comments' => 'Comments',
            'call_to' => 'Call To',
            'call_to_id' => 'Call To ID',
            'call_type' => 'Call Type',
            'outgoing_call_status' => 'Outgoing Call Status',
            'call_start_time' => 'Call Start Time',
            'call_duration' => 'Call Duration',
            'related_to' => 'Related To',
            'related_to_id' => 'Related To ID',
            'voice_recording' => 'Voice Recording',
            'call_purpose' => 'Call Purpose',
            'call_agenda' => 'Call Agenda',
            'team1' => 'Team1',
            'call_result' => 'Call Result',
            'upd' => 'Upd',
        ];
    }


    public function getModtrackerBasic()
    {
        return $this->hasOne(ModtrackerBasic::className(), ['crmid' => 'callinfo_id'])
            ->andOnCondition(['module' => 'calls']);
    }
}
