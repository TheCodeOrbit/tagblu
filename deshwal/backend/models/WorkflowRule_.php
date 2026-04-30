<?php

// namespace app\models;
namespace  backend\models;

use Yii;

/**
 * This is the model class for table "workflow_rule".
 *
 * @property int $id
 * @property string $module
 * @property string $name
 * @property string $trigger_event
 * @property string|null $trigger_fields
 * @property string $trigger_type
 * @property int|null $template_id
 * @property int|null $active
 * @property int|null $created_by
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property WorkflowRuleRecipient[] $workflowRuleRecipients
 */
class WorkflowRule extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const TRIGGER_EVENT_CREATE = 'create';
    const TRIGGER_EVENT_UPDATE = 'update';
    const TRIGGER_EVENT_CHANGE = 'change';
    const TRIGGER_EVENT_APPROVE = 'approve';
    const TRIGGER_TYPE_EMAIL = 'email';
    const TRIGGER_TYPE_SMS = 'sms';
    const TRIGGER_TYPE_NOTIFICATION = 'notification';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'workflow_rule';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['trigger_fields', 'template_id', 'created_by'], 'default', 'value' => null],
            [['active'], 'default', 'value' => 1],
            [['module', 'name', 'trigger_event', 'trigger_type'], 'required'],
            [['trigger_event', 'trigger_type'], 'string'],
            [['template_id', 'active', 'created_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['module'], 'string', 'max' => 100],
            [['name'], 'string', 'max' => 255],
            [['trigger_fields'], 'string', 'max' => 512],
            ['trigger_event', 'in', 'range' => array_keys(self::optsTriggerEvent())],
            ['trigger_type', 'in', 'range' => array_keys(self::optsTriggerType())],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'module' => 'Module',
            'name' => 'Name',
            'trigger_event' => 'Trigger Event',
            'trigger_fields' => 'Trigger Fields',
            'trigger_type' => 'Trigger Type',
            'stage_id'=>'Stage',
            'template_id' => 'Template ID',
            'active' => 'Active',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[WorkflowRuleRecipients]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWorkflowRuleRecipients()
    {
        return $this->hasMany(WorkflowRuleRecipient::class, ['rule_id' => 'id']);
    }


    /**
     * column trigger_event ENUM value labels
     * @return string[]
     */
    public static function optsTriggerEvent()
    {
        return [
            self::TRIGGER_EVENT_CREATE => 'create',
            self::TRIGGER_EVENT_UPDATE => 'update',
            self::TRIGGER_EVENT_CHANGE => 'change',
            self::TRIGGER_EVENT_APPROVE => 'approve',
        ];
    }

    /**
     * column trigger_type ENUM value labels
     * @return string[]
     */
    public static function optsTriggerType()
    {
        return [
            self::TRIGGER_TYPE_EMAIL => 'email',
            self::TRIGGER_TYPE_SMS => 'sms',
            self::TRIGGER_TYPE_NOTIFICATION => 'notification',
        ];
    }

    /**
     * @return string
     */
    public function displayTriggerEvent()
    {
        return self::optsTriggerEvent()[$this->trigger_event];
    }

    /**
     * @return bool
     */
    public function isTriggerEventCreate()
    {
        return $this->trigger_event === self::TRIGGER_EVENT_CREATE;
    }

    public function setTriggerEventToCreate()
    {
        $this->trigger_event = self::TRIGGER_EVENT_CREATE;
    }

    /**
     * @return bool
     */
    public function isTriggerEventUpdate()
    {
        return $this->trigger_event === self::TRIGGER_EVENT_UPDATE;
    }

    public function setTriggerEventToUpdate()
    {
        $this->trigger_event = self::TRIGGER_EVENT_UPDATE;
    }

    /**
     * @return bool
     */
    public function isTriggerEventChange()
    {
        return $this->trigger_event === self::TRIGGER_EVENT_CHANGE;
    }

    public function setTriggerEventToChange()
    {
        $this->trigger_event = self::TRIGGER_EVENT_CHANGE;
    }

    /**
     * @return bool
     */
    public function isTriggerEventApprove()
    {
        return $this->trigger_event === self::TRIGGER_EVENT_APPROVE;
    }

    public function setTriggerEventToApprove()
    {
        $this->trigger_event = self::TRIGGER_EVENT_APPROVE;
    }

    /**
     * @return string
     */
    public function displayTriggerType()
    {
        return self::optsTriggerType()[$this->trigger_type];
    }

    /**
     * @return bool
     */
    public function isTriggerTypeEmail()
    {
        return $this->trigger_type === self::TRIGGER_TYPE_EMAIL;
    }

    public function setTriggerTypeToEmail()
    {
        $this->trigger_type = self::TRIGGER_TYPE_EMAIL;
    }

    /**
     * @return bool
     */
    public function isTriggerTypeSms()
    {
        return $this->trigger_type === self::TRIGGER_TYPE_SMS;
    }

    public function setTriggerTypeToSms()
    {
        $this->trigger_type = self::TRIGGER_TYPE_SMS;
    }

    /**
     * @return bool
     */
    public function isTriggerTypeNotification()
    {
        return $this->trigger_type === self::TRIGGER_TYPE_NOTIFICATION;
    }

    public function setTriggerTypeToNotification()
    {
        $this->trigger_type = self::TRIGGER_TYPE_NOTIFICATION;
    }
}
