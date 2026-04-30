<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "foc_dit".
 *
 * @property int $focdit_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $focdit_no
 * @property string|null $customer_name
 * @property string|null $address
 * @property string|null $city
 * @property string|null $state
 * @property int|null $pin_code
 * @property int|null $mobile_number
 * @property string|null $stage
 * @property int|null $submit_for_approval
 * @property string|null $comment
 * @property int $deleted
 *
 * @property FocditProductDetails[] $focditProductDetails
 */
class FocDit extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'foc_dit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['focdit_no', 'customer_name', 'address', 'city', 'state', 'pin_code', 'mobile_number', 'stage', 'submit_for_approval', 'comment'], 'default', 'value' => null],
            [['deleted'], 'default', 'value' => 0],
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'pin_code', 'mobile_number', 'submit_for_approval', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime'], 'safe'],
            [['focdit_no', 'customer_name', 'city', 'state', 'stage'], 'string', 'max' => 200],
            [['address'], 'string', 'max' => 3000],
            [['comment'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'focdit_id' => 'Focdit ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'focdit_no' => 'Focdit No',
            'customer_name' => 'Customer Name',
            'address' => 'Address',
            'city' => 'City',
            'state' => 'State',
            'pin_code' => 'Pin Code',
            'mobile_number' => 'Mobile Number',
            'stage' => 'Stage',
            'submit_for_approval' => 'Submit For Approval',
            'comment' => 'Comment',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * Gets query for [[FocditProductDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFocditProductDetails()
    {
        return $this->hasMany(FocditProductDetails::class, ['focdit_id' => 'focdit_id']);
    }

}
