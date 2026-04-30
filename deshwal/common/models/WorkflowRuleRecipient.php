<?php
namespace common\models;

use yii\db\ActiveRecord;

class WorkflowRuleRecipient extends ActiveRecord
{
    public static function tableName() { return 'workflow_rule_recipient'; }

    public function rules()
    {
        return [
            [['rule_id','recipient_type'], 'required'],
            ['email', 'email'],
            [['module_field','user_id','email'], 'safe']
        ];
    }
}
