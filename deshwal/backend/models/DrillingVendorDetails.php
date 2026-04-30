<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "drilling_vendor_details".
 *
 * @property int $drilling_vendor_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $drilling_vendor_no
 * @property string|null $drilling_done_by
 * @property string|null $po_required
 * @property string|null $comments
 * @property string|null $drilling
 * @property string|null $create_po
 * @property string|null $vendor_name
 * @property string|null $drilling_tentative_date
 * @property string|null $drilling_start_date
 * @property string|null $drilling_complete_date
 * @property string|null $total_hdd_count
 * @property string|null $currency
 * @property string|null $exchange_rate
 * @property int $deleted
 */
class DrillingVendorDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'drilling_vendor_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'deleted','hdd_drilled'], 'integer'],
            [['total_vendor_cost'],'number'],
            [['createdtime', 'modifiedtime'], 'safe'],
            [['drilling_vendor_no', 'drilling_done_by', 'po_required', 'comments', 'drilling', 'create_po', 'vendor_name', 'drilling_tentative_date', 'drilling_start_date', 'drilling_complete_date', 'total_hdd_count', 'currency', 'exchange_rate','deshwal_engineer_name'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'drilling_vendor_id' => 'Drilling Vendor ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'drilling_vendor_no' => 'Drilling Vendor No',
            'drilling_done_by' => 'Drilling Done By',
            'po_required' => 'Po Required',
            'comments' => 'Comments',
            'drilling' => 'Drilling',
            'create_po' => 'Create Po',
            'vendor_name' => 'Vendor Name',
            'drilling_tentative_date' => 'Drilling Tentative Date',
            'drilling_start_date' => 'Drilling Start Date',
            'drilling_complete_date' => 'Drilling Complete Date',
            'total_hdd_count' => 'Total Hdd Count',
            'currency' => 'Currency',
            'exchange_rate' => 'Exchange Rate',
            'deleted' => 'Deleted',
            'deshwal_engineer_name' => 'Deshwal Engineer Name',
            'total_vendor_cost' => 'Total Vendor Cost',
            'hdd_drilled' => 'HDD Drilled'
        ];
    }
}
