<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "roles".
 *
 * @property int $role_id
 * @property string $roleName
 * @property int $roleID
 * @property int $parentRole
 * @property string $profile
 * @property string $report
 * @property int $Enabled
 * @property int $createdby
 * @property string $createddatetime
 * @property int $modifiedby
 * @property string $modifieddatetime
 */
class Roles extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'roles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['roleName', 'roleID', 'parentRole', 'profile', 'report', 'Enabled', 'createdby', 'modifiedby'], 'required'],
            [['roleID', 'parentRole', 'Enabled', 'createdby', 'modifiedby'], 'integer'],
            [['createddatetime', 'modifieddatetime'], 'safe'],
            [['roleName', 'profile', 'report'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'role_id' => 'Role ID',
            'roleName' => 'Role Name',
            'roleID' => 'Role ID',
            'parentRole' => 'Parent Role',
            'profile' => 'Profile',
            'report' => 'Report',
            'Enabled' => 'Enabled',
            'createdby' => 'Createdby',
            'createddatetime' => 'Createddatetime',
            'modifiedby' => 'Modifiedby',
            'modifieddatetime' => 'Modifieddatetime',
        ];
    }
}
