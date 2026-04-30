<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_targets".
 *
 * @property int $user_targets_id
 * @property int|null $userid
 * @property string|null $year
 * @property int|null $targets
 *
 * @property User $user
 */
class UserTargets extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_targets';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userid', 'year', 'targets'], 'default', 'value' => null],
            [['userid', 'targets'], 'integer'],
            [['year'], 'string', 'max' => 100],
            [['userid'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['userid' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_targets_id' => 'User Targets ID',
            'userid' => 'Userid',
            'year' => 'Year',
            'targets' => 'Targets',
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

    public function saveUserTarget($entityId,$userid)
    {
        // echo "<pre>";print_r($userid);die;
        $items=$_POST['user_targets']??[];
        if(count($items)>0)
		{
			foreach($items as $rec)
			{
                $rec['userid']=$userid;
                $rec_obj=new UserTargets();	
                $rec_obj->attributes=$rec;
                $rec_obj->validate();
                $rec_obj->save(false);
                // echo "User Targets saved successfully<pre>";print_r($rec_obj->attributes);die;
			}
		}
    }
}
