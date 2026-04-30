<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "servicedetail".
 *
 * @property int $servicedetail_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $servicedetail_no
 * @property string|null $vendor_account_name
 * @property float|null $total_marketing_expenses
 * @property float|null $total_sp_amount_inclusive_gst
 * @property float|null $total_sp_amount_exclusive_gst
 * @property float|null $total_service_cost
 * @property int|null $related_to
 * @property int|null $related_to_id
 * @property int $deleted
 *
 * @property ServicedetailDetails[] $servicedetailDetails
 */
class Servicedetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'servicedetail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'related_to', 'related_to_id', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime'], 'safe'],
            [['total_marketing_expenses', 'total_sp_amount_inclusive_gst', 'total_sp_amount_exclusive_gst', 'total_service_cost'], 'number'],
            [['servicedetail_no', 'vendor_account_name'], 'string', 'max' => 200],
             // added for handling blank values saving in by ptpatel on date 24-01-2026
            [['vendor_account_name'], 'trim'],
            [['vendor_account_name'], 'required', 'message' => 'Vendor Account Name cannot be blank.'],
            [['vendor_account_name'], 'integer', 'message' => 'Vendor Account Name must be a number.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'servicedetail_id' => 'Servicedetail ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'servicedetail_no' => 'Servicedetail No',
            'vendor_account_name' => 'Vendor Account Name',
            'total_marketing_expenses' => 'Total Marketing Expenses',
            'total_sp_amount_inclusive_gst' => 'Total Sp Amount Inclusive Gst',
            'total_sp_amount_exclusive_gst' => 'Total Sp Amount Exclusive Gst',
            'total_service_cost' => 'Total Service Cost',
            'related_to' => 'Related To',
            'related_to_id' => 'Related To ID',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * Gets query for [[ServicedetailDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getServicedetailDetails()
    {
        return $this->hasMany(ServicedetailDetails::class, ['servicedetail_id' => 'servicedetail_id']);
    }
}
