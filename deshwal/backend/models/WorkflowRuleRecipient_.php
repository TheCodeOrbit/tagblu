<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "workflow_rule_recipient".
 *
 * @property int $id
 * @property int $rule_id
 * @property string $recipient_type
 * @property string|null $module_field
 * @property int|null $user_id
 * @property string|null $email
 *
 * @property WorkflowRule $rule
 */
class WorkflowRuleRecipient extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const RECIPIENT_TYPE_MODULE_FIELD = 'module_field';
    const RECIPIENT_TYPE_USER = 'user';
    const RECIPIENT_TYPE_MANUAL = 'manual';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'workflow_rule_recipient';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['module_field', 'user_id', 'email'], 'default', 'value' => null],
            [['rule_id', 'recipient_type'], 'required'],
            [['rule_id', 'user_id'], 'integer'],
            [['recipient_type'], 'string'],
            [['module_field'], 'string', 'max' => 100],
            [['email'], 'string', 'max' => 255],
            ['recipient_type', 'in', 'range' => array_keys(self::optsRecipientType())],
            [['rule_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkflowRule::class, 'targetAttribute' => ['rule_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'rule_id' => 'Rule ID',
            'recipient_type' => 'Recipient Type',
            'module_field' => 'Module Field',
            'user_id' => 'User ID',
            'email' => 'Email',
        ];
    }

    /**
     * Gets query for [[Rule]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRule()
    {
        return $this->hasOne(WorkflowRule::class, ['id' => 'rule_id']);
    }


    /**
     * column recipient_type ENUM value labels
     * @return string[]
     */
    public static function optsRecipientType()
    {
        return [
            self::RECIPIENT_TYPE_MODULE_FIELD => 'module_field',
            self::RECIPIENT_TYPE_USER => 'user',
            self::RECIPIENT_TYPE_MANUAL => 'manual',
        ];
    }

    /**
     * @return string
     */
    public function displayRecipientType()
    {
        return self::optsRecipientType()[$this->recipient_type];
    }

    /**
     * @return bool
     */
    public function isRecipientTypeModulefield()
    {
        return $this->recipient_type === self::RECIPIENT_TYPE_MODULE_FIELD;
    }

    public function setRecipientTypeToModulefield()
    {
        $this->recipient_type = self::RECIPIENT_TYPE_MODULE_FIELD;
    }

    /**
     * @return bool
     */
    public function isRecipientTypeUser()
    {
        return $this->recipient_type === self::RECIPIENT_TYPE_USER;
    }

    public function setRecipientTypeToUser()
    {
        $this->recipient_type = self::RECIPIENT_TYPE_USER;
    }

    /**
     * @return bool
     */
    public function isRecipientTypeManual()
    {
        return $this->recipient_type === self::RECIPIENT_TYPE_MANUAL;
    }

    public function setRecipientTypeToManual()
    {
        $this->recipient_type = self::RECIPIENT_TYPE_MANUAL;
    }
}
