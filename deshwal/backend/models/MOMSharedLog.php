<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "MOM_shared_log".
 *
 * @property int $log_id
 * @property int $record_id
 * @property int $is_mail_sent 0 - not sent,1 sent
 * @property string $created_at
 * @property string $updated_at
 */
class MOMSharedLog extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'MOM_shared_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['record_id', 'is_mail_sent', 'created_at', 'updated_at'], 'required'],
            [['record_id', 'is_mail_sent'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'log_id' => 'Log ID',
            'record_id' => 'Record ID',
            'is_mail_sent' => 'Is Mail Sent',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

}
