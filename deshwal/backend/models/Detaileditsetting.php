<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "detaileditsetting".
 *
 * @property int $des_id
 * @property int $tabid
 * @property string $module_name
 * @property string $stage_field
 * @property int $stage_value
 * @property int $edit_allow
 * @property string $user_role
 * @property string $user_id
 * @property int $admin_allow
 * @property int $superadmin_allow
 */
class Detaileditsetting extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'detaileditsetting';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tabid', 'module_name', 'stage_field', 'stage_value', 'user_role', 'user_id'], 'required'],
            [['tabid', 'stage_value', 'edit_allow', 'admin_allow', 'superadmin_allow'], 'integer'],
            [['module_name', 'stage_field', 'user_role', 'user_id'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'des_id' => 'Des ID',
            'tabid' => 'Tabid',
            'module_name' => 'Module Name',
            'stage_field' => 'Stage Field',
            'stage_value' => 'Stage Value',
            'edit_allow' => 'Edit Allow',
            'user_role' => 'User Role',
            'user_id' => 'User ID',
            'admin_allow' => 'Admin Allow',
            'superadmin_allow' => 'Superadmin Allow',
        ];
    }
}
