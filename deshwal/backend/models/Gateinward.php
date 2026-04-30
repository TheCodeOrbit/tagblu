<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "gateinward".
 *
 * @property int $gateinward_id
 * @property string|null $gateinward_no
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $docket_number
 * @property string|null $driver_name
 * @property string|null $aadhar_card_no
 * @property string|null $aadhar_front
 * @property string|null $aadhar_back
 * @property string|null $dl_no
 * @property string|null $dl_photo
 * @property string|null $any_other_document
 * @property string|null $other_document_photo
 * @property int $deleted
 */
class Gateinward extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gateinward';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'deleted','pickup_id'], 'integer'],
            [['createdtime', 'modifiedtime','gateinward_no', 'docket_number', 'driver_name', 'dl_photo', 'any_other_document',
            'aadhar_card_no','aadhar_front', 'aadhar_back', 'other_document_photo','dl_no'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'gateinward_id' => 'Gateinward ID',
            'gateinward_no' => 'Gateinward No',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'docket_number' => 'Docket Number',
            'driver_name' => 'Driver Name',
            'aadhar_card_no' => 'Aadhar Card No',
            'aadhar_front' => 'Aadhar Front',
            'aadhar_back' => 'Aadhar Back',
            'dl_no' => 'Dl No',
            'dl_photo' => 'Dl Photo',
            'any_other_document' => 'Any Other Document',
            'other_document_photo' => 'Other Document Photo',
            'deleted' => 'Deleted',
            'pickup_id'=>'Pickup Id',
        ];
    }

    public function updateStatusOfPickupShippedDetailsItems($docket_number){
        if(empty($docket_number)) return false;
        $date = date("Y-m-d");
        Yii::$app->db->createCommand("UPDATE shipped_details set status = 3,delivery_date='$date' where docket_number=:docket_number and status = 2")
        ->bindValue(":docket_number", $docket_number)
        ->queryAll();
    }
}
