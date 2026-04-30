<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "field".
 *
 * @property int $tabid
 * @property int $fieldid
 * @property string $columnname
 * @property string|null $tablename
 * @property int $generatedtype
 * @property string $uitype
 * @property string $fieldname
 * @property string $fieldlabel
 * @property int $readonly
 * @property int $presence
 * @property string|null $defaultvalue
 * @property int|null $maximumlength
 * @property int|null $sequence
 * @property int|null $block
 * @property int|null $displaytype
 * @property string|null $typeofdata
 * @property int $quickcreate
 * @property int|null $quickcreatesequence
 * @property string|null $info_type
 * @property int $masseditable
 * @property string|null $helpinfo
 * @property int $summaryfield
 * @property int|null $headerfield
 * @property int|null $isunique
 * @property int|null $mandatory
 */
class Field extends \yii\db\ActiveRecord
{
    public $fieldtype;
    public $classname;
    public $fieldoptions;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'field';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tabid', 'columnname', 'uitype', 'fieldname', 'fieldlabel', 'readonly'], 'required'],
            [['tabid', 'generatedtype', 'readonly', 'presence', 'maximumlength', 'sequence', 'block', 'displaytype', 'quickcreate', 'quickcreatesequence', 'masseditable', 'summaryfield', 'headerfield', 'isunique','mandatory'], 'integer'],
            [['defaultvalue', 'helpinfo'], 'string'],
            [['columnname', 'uitype'], 'string', 'max' => 30],
            [['tablename', 'typeofdata'], 'string', 'max' => 100],
            [['fieldname', 'fieldlabel'], 'string', 'max' => 50],
            [['info_type'], 'string', 'max' => 20],
            [['fieldtype,classname,fieldoptions'],'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tabid' => 'Tabid',
            'fieldid' => 'Fieldid',
            'columnname' => 'Columnname',
            'tablename' => 'Tablename',
            'generatedtype' => 'Generatedtype',
            'uitype' => 'Uitype',
            'fieldname' => 'Fieldname',
            'fieldlabel' => 'Fieldlabel',
            'readonly' => 'Readonly',
            'presence' => 'Presence',
            'defaultvalue' => 'Defaultvalue',
            'maximumlength' => 'Maximumlength',
            'sequence' => 'Sequence',
            'block' => 'Block',
            'displaytype' => 'Displaytype',
            'typeofdata' => 'Typeofdata',
            'quickcreate' => 'Quickcreate',
            'quickcreatesequence' => 'Quickcreatesequence',
            'info_type' => 'Info Type',
            'masseditable' => 'Masseditable',
            'helpinfo' => 'Helpinfo',
            'summaryfield' => 'Summaryfield',
            'headerfield' => 'Headerfield',
            'isunique' => 'Isunique',
        ];
    }
}
