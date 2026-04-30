<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "materialissuenote_dit".
 *
 * @property int $mindit_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $mindit_no
 * @property int $deleted
 * @property string|null $comp_name
 * @property string|null $comp_address
 * @property string|null $comp_gstin
 * @property string|null $comp_pan
 * @property string|null $contact_number
 * @property int|null $min_type
 * @property string|null $min_date
 * @property string|null $requester_name
 * @property int|null $department
 * @property int|null $purpose
 * @property string|null $remark
 * @property int|null $so_number
 */
class MaterialissuenoteDit extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'materialissuenote_dit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mindit_no', 'comp_name', 'comp_address', 'comp_gstin', 'comp_pan', 'contact_number', 'min_date', 'requester_name', 'department', 'purpose', 'remark', 'so_number'], 'default', 'value' => null],
            [['min_type'], 'default', 'value' => 0],
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'deleted', 'min_type', 'department', 'purpose', 'so_number','comp_name'], 'integer'],
            [['createdtime', 'modifiedtime', 'min_date'], 'safe'],
            [['mindit_no','comp_gstin', 'requester_name'], 'string', 'max' => 200],
            [['remark'], 'string', 'max' => 1000],
            [['comp_address'], 'string', 'max' => 3000],
            [['comp_pan'], 'string', 'max' => 10],
            [['contact_number'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'mindit_id' => 'Mindit ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'mindit_no' => 'Mindit No',
            'deleted' => 'Deleted',
            'comp_name' => 'Comp Name',
            'comp_address' => 'Comp Address',
            'comp_gstin' => 'Comp Gstin',
            'comp_pan' => 'Comp Pan',
            'contact_number' => 'Contact Number',
            'min_type' => 'Min Type',
            'min_date' => 'Min Date',
            'requester_name' => 'Requester Name',
            'department' => 'Department',
            'purpose' => 'Purpose',
            'remark' => 'Remark',
            'so_number' => 'So Number',
        ];
    }

}
