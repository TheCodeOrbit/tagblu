<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class WorkflowEmailLog extends ActiveRecord
{
    public static function tableName()
    {
        return 'workflow_email_log';
    }
}
