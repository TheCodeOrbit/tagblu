<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property int|null $creatorid
 * @property int|null $ownerid
 * @property int|null $modifiedby
 * @property string|null $createdtime
 * @property string|null $modifiedtime
 * @property string|null $emp_code
 * @property string $username
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $auth_key
 * @property string|null $password_hash
 * @property string|null $password_reset_token
 * @property string|null $email
 * @property string|null $mobile
 * @property int $status 10 - active, 9- inactive
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property string|null $verification_token
 * @property int $deleted
 * @property string $profilepic
 * @property int|null $is_admin
 * @property string|null $role
 * @property int|null $team
 * @property int|null $department
 * @property int|null $designation
 * @property int|null $reports_to
 * @property int|null $is_super_admin
 * @property User2role $user2role
 * @property UserTargets[] $userTargets
 */
class User extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['creatorid', 'ownerid', 'modifiedby', 'createdtime', 'modifiedtime', 'emp_code', 'first_name', 'last_name', 'auth_key', 'password_hash', 'password_reset_token', 'email', 'mobile', 'created_at', 'updated_at', 'verification_token', 'role', 'team', 'department', 'designation'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 10],
            [['reports_to'], 'string', 'max' => 50],
            [['creatorid', 'ownerid', 'modifiedby', 'status', 'created_at', 'updated_at', 'deleted', 'is_admin','is_super_admin', 'team', 'department', 'designation', 'reports_to'], 'integer'],
            [['createdtime', 'modifiedtime'], 'safe'],
            [['username'], 'required'],
            [['emp_code', 'profilepic'], 'string', 'max' => 100],
            [['username', 'first_name', 'last_name', 'password_hash', 'password_reset_token', 'email', 'verification_token', 'role'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['mobile'], 'string', 'max' => 15],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'creatorid' => 'Creatorid',
            'ownerid' => 'Ownerid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'emp_code' => 'Emp Code',
            'username' => 'Username',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'mobile' => 'Mobile',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'verification_token' => 'Verification Token',
            'deleted' => 'Deleted',
            'profilepic' => 'Profilepic',
            'is_admin' => 'Is Admin',
            'role' => 'Role',
            'team' => 'Team',
            'department' => 'Department',
            'designation' => 'Designation',
            'reports_to' => 'Reports To',
        ];
    }

    /**
     * Gets query for [[User2role]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser2role()
    {
        return $this->hasOne(User2role::class, ['userid' => 'id']);
    }

    /**
     * Gets query for [[UserTargets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserTargets()
    {
        return $this->hasMany(UserTargets::class, ['userid' => 'id']);
    }

}
