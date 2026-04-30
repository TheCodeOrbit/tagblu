<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user2role".
 *
 * @property int $userid
 * @property string $roleid
 *
 * @property User $user
 */
class User2role extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user2role';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userid', 'roleid'], 'required'],
            [['userid'], 'integer'],
            [['roleid'], 'string', 'max' => 255],
            [['userid'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['userid' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'userid' => 'Userid',
            'roleid' => 'Roleid',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'userid']);
    }
}
