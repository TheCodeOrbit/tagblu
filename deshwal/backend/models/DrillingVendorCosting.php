<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "drilling_vendor_costing".
 *
 * @property int $drilling_vendor_costing_id
 * @property string|null $date
 * @property int $drilling_vendor_id
 * @property string|null $engineer_name
 * @property string|null $engineer_number
 * @property string|null $aadhar_card
 * @property string|null $hdd_drilled
 * @property string|null $bit_count
 * @property string|null $bit_price
 * @property string|null $labour_used
 * @property string|null $labour_price
 * @property string|null $lounge_daily_rent
 * @property string|null $travel
 * @property string|null $food
 * @property string|null $drilling_machine_rent
 * @property string|null $total_cost
 * @property int $deleted
 *
 * @property DrillingVendorDetails $drillingVendor
 */
class DrillingVendorCosting extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'drilling_vendor_costing';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date'], 'safe'],
            [['drilling_vendor_id', 'deleted'], 'required'],
            [['drilling_vendor_id', 'deleted'], 'integer'],
            [['engineer_name', 'engineer_number', 'aadhar_card', 'hdd_drilled', 'bit_count', 'bit_price', 'labour_used', 'labour_price', 'lounge_daily_rent', 'travel', 'food', 'drilling_machine_rent', 'total_cost'], 'string', 'max' => 200],
            [['drilling_vendor_id'], 'exist', 'skipOnError' => true, 'targetClass' => DrillingVendorDetails::class, 'targetAttribute' => ['drilling_vendor_id' => 'drilling_vendor_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'drilling_vendor_costing_id' => 'Drilling Vendor Costing ID',
            'date' => 'Date',
            'drilling_vendor_id' => 'Drilling Vendor ID',
            'engineer_name' => 'Engineer Name',
            'engineer_number' => 'Engineer Number',
            'aadhar_card' => 'Aadhar Card',
            'hdd_drilled' => 'Hdd Drilled',
            'bit_count' => 'Bit Count',
            'bit_price' => 'Bit Price',
            'labour_used' => 'Labour Used',
            'labour_price' => 'Labour Price',
            'lounge_daily_rent' => 'Lounge Daily Rent',
            'travel' => 'Travel',
            'food' => 'Food',
            'drilling_machine_rent' => 'Drilling Machine Rent',
            'total_cost' => 'Total Cost',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * Gets query for [[DrillingVendor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDrillingVendor()
    {
        return $this->hasOne(DrillingVendorDetails::class, ['drilling_vendor_id' => 'drilling_vendor_id']);
    }

    public function saveDrillingVendorCosting($entityId)
    {
        $drilling_vendor_costing = $_POST['drilling_vendor_costing'] ?? [];
        // print_r($drilling_vendor_costing);die;
        if (count($drilling_vendor_costing) > 0) {
            foreach ($drilling_vendor_costing as $key => $vendor_costing) {
                $vendor_costing['drilling_vendor_id'] = (int) $entityId;
                $vendor_costing_obj = new DrillingVendorCosting();
                $vendor_costing_obj->attributes = $vendor_costing;
                $vendor_costing_obj->validate();
                $vendor_costing_obj->save(false);
            }
        }
    }
}
