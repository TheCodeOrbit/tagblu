<?php
namespace common\models;

use yii\db\ActiveRecord;

class WorkflowRule extends ActiveRecord
{
    public $copy_template_id;
    public static function tableName() { return 'workflow_rule'; }

    public function rules()
    {
        return [
            [['module','name','trigger_event','trigger_type'], 'required'],
            [['trigger_fields'], 'string'],
            [['active'], 'integer'],
            [['stage_id'],'safe'],
            // Custom Validation to prevent duplicates added by ptpatel on date 08-12-2025
            [['module', 'trigger_type', 'trigger_event', 'trigger_fields', ], 'validateUniqueWorkflow'],
            [['copy_template_id'], 'integer'],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'stage_id' => 'Stage',
        ];
    }


    public function getRecipients() {
        return $this->hasMany(WorkflowRuleRecipient::class, ['rule_id'=>'id']);
    }

    public function getTemplate() {
        return $this->hasOne(WorkflowTemplate::class, ['id'=>'template_id']);
    }

    //added by ptpatel on date 08-12-2025
    public function validateUniqueWorkflow($attribute, $params)
    {
        // echo "<pre>";print_r($this->attributes);die;
        $query = self::find()
        ->where([
            'module'        => $this->module,
            'trigger_type'  => $this->trigger_type,
            'trigger_event' => $this->trigger_event,
        ]);

           // only compare trigger_fields if not empty
        if (!empty($this->trigger_fields)) {
            $query->andWhere(['trigger_fields' => $this->trigger_fields]);
        }

        $id = trim((string)$this->id);     // normalize empty string, space, null, 0

        if ($id !== "" && $id !== null && ctype_digit($id)) {
            $query->andWhere(['<>', 'id', $id]);
        }

        // $sql = $query->createCommand()->getRawSql();
        //     echo $query->exists(); die;
        if ($query->exists()) {
            $this->addError($attribute, 'This combination (module, event, fields, type) already exists.');
        }
    }
    //added by ptpatel on date 08-12-2025
}
