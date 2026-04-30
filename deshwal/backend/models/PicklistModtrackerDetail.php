<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "picklist_modtracker_detail".
 *
 * @property int|null $id
 * @property string|null $fieldname
 * @property string|null $prevalue
 * @property string|null $postvalue
 */
class PicklistModtrackerDetail extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'picklist_modtracker_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'fieldname', 'prevalue', 'postvalue'], 'default', 'value' => null],
            [['id'], 'integer'],
            [['prevalue', 'postvalue'], 'string'],
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
            'fieldname' => 'Fieldname',
            'prevalue' => 'Prevalue',
            'postvalue' => 'Postvalue',
        ];
    }

}
