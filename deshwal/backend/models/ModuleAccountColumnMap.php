<?php
namespace app\models;

use yii\db\ActiveRecord;

class ModuleAccountColumnMap extends ActiveRecord
{
    public static function tableName()
    {
        return 'module_account_column_map';
    }
}
