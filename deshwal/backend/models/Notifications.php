<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "notification".
 *
 * @property int $id
 * @property int $userid
 * @property string $source_link
 * @property int $read_status
 * @property int $display_status
 * @property string $message
 * @property string $createdtime
 * @property string $modifiedtime
 */
class Notifications extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notification';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userid', 'source_link', 'message', 'createdtime', 'modifiedtime'], 'required'],
            [['userid', 'read_status', 'display_status'], 'integer'],
            [['message'], 'string'],
            [['createdtime', 'modifiedtime'], 'safe'],
            [['source_link'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userid' => 'Userid',
            'source_link' => 'Source Link',
            'read_status' => 'Read Status',
            'display_status' => 'Display Status',
            'message' => 'Message',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
        ];
    }
}
