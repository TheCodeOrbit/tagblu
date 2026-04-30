<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "uiinputs".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $hidden_field
 * @property string|null $password
 * @property string|null $textarea
 * @property string|null $file
 * @property int|null $checkbox
 * @property string|null $listbox
 * @property string|null $dropdown_single
 * @property string|null $checkboxlist
 * @property string|null $radio_button
 * @property string|null $referencetype
 * @property string|null $DateTimePicker
 * @property string|null $label
 * @property string|null $dropdown_multipe
 * @property string|null $MonthYearPicker
 * @property string|null $DateTime
 * @property string|null $date
 * @property string|null $BatchList
 * @property string|null $maskingdate
 */
class Uiinputs extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'uiinputs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['textarea'], 'string'],
            [['checkbox'], 'integer'],
            [['DateTimePicker', 'date', 'maskingdate'], 'safe'],
            [['name', 'hidden_field', 'password', 'file', 'listbox', 'dropdown_single', 'checkboxlist', 'radio_button', 'referencetype', 'label', 'dropdown_multipe', 'BatchList'], 'string', 'max' => 255],
            [['MonthYearPicker'], 'string', 'max' => 7],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'hidden_field' => 'Hidden Field',
            'password' => 'Password',
            'textarea' => 'Textarea',
            'file' => 'File',
            'checkbox' => 'Checkbox',
            'listbox' => 'Listbox',
            'dropdown_single' => 'Dropdown Single',
            'checkboxlist' => 'Checkboxlist',
            'radio_button' => 'Radio Button',
            'referencetype' => 'Referencetype',
            'DateTimePicker' => 'Date Time Picker',
            'label' => 'Label',
            'dropdown_multipe' => 'Dropdown Multipe',
            'MonthYearPicker' => 'Month Year Picker',
          
            'date' => 'Date',
            'BatchList' => 'Batch List',
            'maskingdate' => 'Maskingdate',
        ];
    }
}
