<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "outgoing_call_status".
 *
 * @property int $outgoingcall_status_id
 * @property string $outgoingcall_status_value
 * @property int $is_active
 * @property int $seq_no
 */
class OutgoingCallStatus extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'outgoing_call_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['outgoingcall_status_value', 'is_active', 'seq_no'], 'required'],
            [['is_active', 'seq_no'], 'integer'],
            [['outgoingcall_status_value'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'outgoingcall_status_id' => 'Outgoingcall Status ID',
            'outgoingcall_status_value' => 'Outgoingcall Status Value',
            'is_active' => 'Is Active',
            'seq_no' => 'Seq No',
        ];
    }
    public function outingCallTypeList()
    {
        return self::find()
            ->select(['outgoingcall_status_id AS id', 'outgoingcall_status_value AS showfield'])
            ->where('is_active' == '1')
            ->asArray()
            ->all();
    }
}
