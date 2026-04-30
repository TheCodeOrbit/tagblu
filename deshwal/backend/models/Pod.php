<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pod".
 *
 * @property int $pod_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property int $deleted
 * @property string $pod_no
 * @property string|null $so_number
 * @property string|null $delivery_date
 * @property string|null $proof_of_delivery
 */
class Pod extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pod';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime', 'pod_no'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'deleted','vendor_name'], 'integer'],
            [['proof_of_delivery'], 'file', 'skipOnEmpty' => true, 'extensions' => 'jpg,jpeg,png,gif,pdf'],
            [['pod_no'], 'string', 'max' => 100],
            [['so_number', 'proof_of_delivery'], 'string', 'max' => 200],
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime', 'pod_no'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime', 'so_number', 'delivery_date', 'proof_of_delivery'], 'safe'],
             // added for handling blank values saving in by ptpatel on date 24-01-2026
            [['vendor_name'], 'trim'],
            [['vendor_name'], 'required', 'message' => 'Vendor Name cannot be blank.'],
            [['vendor_name'], 'integer', 'message' => 'Vandor Name must be a number.'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pod_id' => 'Pod ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'deleted' => 'Deleted',
            'pod_no' => 'Pod No',
            'so_number' => 'So Number',
            'delivery_date' => 'Delivery Date',
            'proof_of_delivery' => 'Proof Of Delivery',
        ];
    }
}
