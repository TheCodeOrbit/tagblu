<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "degaussing_vendor_costing".
 *
 * @property int $degaussing_vendor_costing_id
 * @property int $degaussing_vendor_id
 * @property string|null $date
 * @property string|null $engineer_name
 * @property string|null $engineer_number
 * @property string|null $aadhar_card
 * @property string|null $hdd_degauss
 * @property string|null $bit_count
 * @property string|null $bit_price
 * @property string|null $labour_used
 * @property string|null $labour_price
 * @property string|null $lounge_daily_rent
 * @property string|null $travel
 * @property string|null $food
 * @property string|null $degaussing_machine_rent
 * @property string|null $total_cost
 * @property int $deleted
 *
 * @property DegaussingVendorDetails $degaussingVendor
 */
class DegaussingVendorCosting extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'degaussing_vendor_costing';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['degaussing_vendor_id'], 'required'],
            [['degaussing_vendor_id', 'deleted'], 'integer'],
            [['date', 'engineer_name', 'engineer_number', 'aadhar_card', 'hdd_degauss', 'bit_count', 'bit_price', 'labour_used', 'labour_price', 'lounge_daily_rent', 'travel', 'food', 'degaussing_machine_rent', 'total_cost'], 'string', 'max' => 200],
            [['degaussing_vendor_id'], 'exist', 'skipOnError' => true, 'targetClass' => DegaussingVendorDetails::class, 'targetAttribute' => ['degaussing_vendor_id' => 'degaussing_vendor_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'degaussing_vendor_costing_id' => 'Degaussing Vendor Costing ID',
            'degaussing_vendor_id' => 'Degaussing Vendor ID',
            'date' => 'Date',
            'engineer_name' => 'Engineer Name',
            'engineer_number' => 'Engineer Number',
            'aadhar_card' => 'Aadhar Card',
            'hdd_degauss' => 'Hdd Degauss',
            'bit_count' => 'Bit Count',
            'bit_price' => 'Bit Price',
            'labour_used' => 'Labour Used',
            'labour_price' => 'Labour Price',
            'lounge_daily_rent' => 'Lounge Daily Rent',
            'travel' => 'Travel',
            'food' => 'Food',
            'degaussing_machine_rent' => 'Degaussing Machine Rent',
            'total_cost' => 'Total Cost',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * Gets query for [[DegaussingVendor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDegaussingVendor()
    {
        return $this->hasOne(DegaussingVendorDetails::class, ['degaussing_vendor_id' => 'degaussing_vendor_id']);
    }
    public function saveDegaussingVendorCosting($entityId)
    {
        $degaussing_vendor_costing = $_POST['degaussing_vendor_costing'] ?? [];
        if (count($degaussing_vendor_costing) > 0) {
            foreach ($degaussing_vendor_costing as $key => $vendor_costing) {
                $vendor_costing['degaussing_vendor_id'] = (int) $entityId;
                $vendor_costing_obj = new DegaussingVendorCosting();
                $vendor_costing_obj->attributes = $vendor_costing;
                $vendor_costing_obj->validate();
                $vendor_costing_obj->save(false);
            }
        }
    }
}
