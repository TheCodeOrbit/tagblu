<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "task_information".
 *
 * @property int $taskinfo_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $status
 * @property string|null $priority
 * @property string|null $task_owner
 * @property string $subject
 * @property string $due_date
 * @property string|null $reminder
 * @property string|null $reminder_date_time
 * @property string|null $notify_by
 * @property string|null $repeat
 * @property string|null $type
 * @property string|null $repeat_type
 * @property string|null $except_weekends_holidays
 * @property string|null $ends
 * @property int|null $ends_number
 * @property string|null $ends_date
 * @property string|null $ends_calender
 * @property string|null $frequency
 * @property string|null $every_days
 * @property string|null $every_weeks
 * @property string|null $every_months
 * @property string|null $every_years
 * @property string|null $contact _name
 * @property string|null $related_to
 * @property string|null $description
 * @property int $deleted
 */
class TaskInformation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task_information';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime', 'subject', 'due_date','related_to_id'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'ends_number', 'deleted','related_to_id'], 'integer'],
            [['createdtime', 'modifiedtime', 'reminder_date_time', 'ends_date', 'ends_calender'], 'safe'],
            [['status', 'priority', 'subject', 'due_date', 'reminder', 'notify_by', 'repeat', 'repeat_type', 'except_weekends_holidays', 'ends', 'frequency', 'every_days', 'every_weeks', 'every_months', 'contact _name', 'related_to'], 'string', 'max' => 200],
            [['type', 'every_years'], 'string', 'max' => 100],
            [['description'], 'string', 'max' => 400],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'taskinfo_id' => 'Taskinformation  ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'status' => 'Status',
            'priority' => 'Priority',
            'subject' => 'Subject',
            'due_date' => 'Due Date',
            'reminder' => 'Reminder',
            'reminder_date_time' => 'Reminder Date Time',
            'notify_by' => 'Notify By',
            'repeat' => 'Repeat',
            'type' => 'Type',
            'repeat_type' => 'Repeat Type',
            'except_weekends_holidays' => 'Except Weekends Holidays',
            'ends' => 'Ends',
            'ends_number' => 'Ends Number',
            'ends_date' => 'Ends Date',
            'ends_calender' => 'Ends Calender',
            'frequency' => 'Frequency',
            'every_days' => 'Every Days',
            'every_weeks' => 'Every Weeks',
            'every_months' => 'Every Months',
            'every_years' => 'Every Years',
            'contact _name' => 'Contact  Name',
            'related_to' => 'Related To',
            'description' => 'Description',
            'deleted' => 'Deleted',
        ];
    }

    public function getModtrackerBasic()
    {
        return $this->hasOne(ModtrackerBasic::className(), ['crmid' => 'taskinfo_id'])
            ->andOnCondition(['module' => 'task']);
    }
}
