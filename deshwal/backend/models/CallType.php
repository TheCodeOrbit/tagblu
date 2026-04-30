<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "call_type".
 *
 * @property int $calltypeid
 * @property string|null $calltype_value
 * @property int|null $is_active
 * @property int|null $seq_no
 */
class CallType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'call_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_active', 'seq_no'], 'integer'],
            [['calltype_value'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'calltypeid' => 'Calltypeid',
            'calltype_value' => 'Calltype Value',
            'is_active' => 'Is Active',
            'seq_no' => 'Seq No',
        ];
    }

    public function getCallTypeList()
    {
        return self::find()
            ->select(['calltypeid AS id', 'calltype_value AS showfield'])
            ->where('is_active' == '1')
            ->asArray()
            ->all();
    }
}
