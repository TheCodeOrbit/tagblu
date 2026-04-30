<?php
namespace backend\models;

use yii\db\ActiveRecord;

class ModuleLog extends ActiveRecord
{
    public static function tableName()
    {
        return 'module_logs';
    }

    public static function getLogTypeLabels()
    {
        return [
            'parenttab' => 'Parent Tab',
            'tab' => 'Tab/Module',
            'block' => 'Block',
            'field' => 'Field',
            'sequence' => 'Sequence Changes',
            'moduleviewsharing' => 'Module View Sharing',
            'companylogo' => 'Company Logo'
        ];
    }

    public static function getActionLabels()
    {
        return [
            'add' => 'Added',
            'update' => 'Updated',
            'delete' => 'Deleted',
            'sequence' => 'Sequence Changed'
        ];
    }
}
