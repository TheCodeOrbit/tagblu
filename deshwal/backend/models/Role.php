<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "role".
 *
 * @property string $roleid
 * @property string|null $rolename
 * @property string|null $parentrole
 * @property int|null $depth
 * @property int $allowassignedrecordsto
 * @property int $seq_no
 * @property int $is_active
 */
class Role extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'role';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['roleid'], 'required'],
            [['rolename'], 'string'],
            [['depth', 'allowassignedrecordsto', 'seq_no', 'is_active'], 'integer'],
            [['roleid', 'parentrole'], 'string', 'max' => 255],
            [['roleid'], 'unique'],
            [['rolename'], 'unique', 'message' => 'This role name has already been taken.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'roleid' => 'Roleid',
            'rolename' => 'Rolename',
            'parentrole' => 'Parentrole',
            'depth' => 'Depth',
            'allowassignedrecordsto' => 'Allowassignedrecordsto',
            'seq_no' => 'Seq No',
            'is_active' => 'Is Active',
        ];
    }
}
