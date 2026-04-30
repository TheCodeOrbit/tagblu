<?php
namespace common\models;

use yii\db\ActiveRecord;

class WorkflowTemplate extends ActiveRecord
{
    public static function tableName() { return 'workflow_template'; }

    public function rules()
    {
        return [
            [['name','subject','body'],'required']
        ];
    }
}
