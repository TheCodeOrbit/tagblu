<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "profile".
 *
 * @property int $profile_id
 * @property string $profilename
 * @property string $description
 * @property string $created_on
 */
class Profile extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'profile';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['profilename', 'description'], 'required'],
            [['enabled','is_deleted'], 'integer'],
            [['profilename'], 'unique', 'message' => 'This profile name has already been taken.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'profileid' => 'Profile ID',
            'profilename' => 'Profile Name',
            'description' => 'Description',
           
            
        ];
    }
}
