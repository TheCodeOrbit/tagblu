<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "profile_modtracker_detail".
 *
 * @property int|null $id
 * @property string $fieldlabel
 * @property string|null $fieldname
 * @property int $fieldid
 * @property string|null $prevalue visible(0 – show,1 – hide),readonly(0 – false, 1 – true)
 * @property string|null $postvalue visible(0 – show,1 – hide),readonly(0 – false, 1 – true)
 */
class ProfileModtrackerDetail extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'profile_modtracker_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'fieldname', 'prevalue', 'postvalue'], 'default', 'value' => null],
            [['id', 'fieldid'], 'integer'],
            [['fieldlabel', 'fieldid'], 'required'],
            [['prevalue', 'postvalue'], 'string'],
            [['fieldlabel'], 'string', 'max' => 200],
            [['fieldname'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fieldlabel' => 'Fieldlabel',
            'fieldname' => 'Fieldname',
            'fieldid' => 'Fieldid',
            'prevalue' => 'Prevalue',
            'postvalue' => 'Postvalue',
        ];
    }

}
