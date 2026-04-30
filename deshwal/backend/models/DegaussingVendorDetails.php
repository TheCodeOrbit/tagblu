<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "degaussing_vendor_details".
 *
 * @property int $degaussing_vendor_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $degaussing_vendor_no
 * @property string|null $degaussing_done_by
 * @property string|null $comments
 * @property string|null $degaussing
 * @property string|null $create_po
 * @property string|null $degaussing_tentative_date
 * @property string|null $degaussing_start_date
 * @property string|null $degaussing_complete_date
 * @property string|null $total_hdd_count
 * @property string|null $currency
 * @property string|null $exchange_rate
 * @property int $deleted
 *
 * @property DegaussingVendorCosting[] $degaussingVendorCostings
 */
class DegaussingVendorDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'degaussing_vendor_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'deleted','total_hdd_degauss','po_required'], 'integer'],
            [['total_vendor_cost'],'number'],
            [['createdtime', 'modifiedtime'], 'safe'],
            [['degaussing_vendor_no', 'degaussing_done_by', 'comments', 'degaussing', 'create_po', 
            'degaussing_tentative_date', 'degaussing_start_date', 'degaussing_complete_date', 'total_hdd_count', 'currency', 
            'exchange_rate','vendor_name','deshwal_engineer_name'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'degaussing_vendor_id' => 'Degaussing Vendor ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'degaussing_vendor_no' => 'Degaussing Vendor No',
            'degaussing_done_by' => 'Degaussing Done By',
            'comments' => 'Comments',
            'degaussing' => 'Degaussing',
            'create_po' => 'Create Po',
            'degaussing_tentative_date' => 'Degaussing Tentative Date',
            'degaussing_start_date' => 'Degaussing Start Date',
            'degaussing_complete_date' => 'Degaussing Complete Date',
            'total_hdd_count' => 'Total Hdd Count',
            'currency' => 'Currency',
            'exchange_rate' => 'Exchange Rate',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * Gets query for [[DegaussingVendorCostings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDegaussingVendorCostings()
    {
        return $this->hasMany(DegaussingVendorCosting::class, ['degaussing_vendor_id' => 'degaussing_vendor_id']);
    }
}
