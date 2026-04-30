<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "fieldtype".
 *
 * @property int $fieldtypeid
 * @property int $uitype
 * @property string $fieldtype
 * @property string $getfieldtype
 * @property string $classname
 */
class Fieldtype extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fieldtype';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uitype', 'fieldtype', 'getfieldtype', 'classname'], 'required'],
            [['uitype'], 'integer'],
            [['fieldtype', 'getfieldtype', 'classname'], 'string', 'max' => 55],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'fieldtypeid' => 'Fieldtypeid',
            'uitype' => 'Uitype',
            'fieldtype' => 'Fieldtype',
            'getfieldtype' => 'Getfieldtype',
            'classname' => 'Classname',
        ];
    }
}
