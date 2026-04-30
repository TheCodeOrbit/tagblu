<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "shipped_details".
 *
 * @property int $shipped_details_id
 * @property string|null $transporter_name
 * @property string|null $vehicle_size
 * @property string|null $shippment_mode
 * @property string|null $docket_number
 * @property string|null $seal_number
 * @property string|null $shipped_date
 * @property string|null $estimate_delivery_date
 * @property string|null $delivery_date
 * @property string|null $status
 * @property int $pickup_id
 * @property int $deleted
 *
 * @property Pickup $pickup
 */
class ShippedDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shipped_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['shipped_date', 'estimate_delivery_date', 'delivery_date','vehicle_number','halting_days','halting_reason','halting_status',], 'safe'],
            [['pickup_id'], 'required'],
            [['pickup_id', 'deleted'], 'integer'],
            [['transporter_name', 'vehicle_size', 'shippment_mode', 'docket_number', 'seal_number', 'status'], 'string', 'max' => 100],
            [['pickup_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pickup::class, 'targetAttribute' => ['pickup_id' => 'pickup_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'shipped_details_id' => 'Shipped Details ID',
            'transporter_name' => 'Transporter Name',
            'vehicle_size' => 'Vehicle Size',
            'shippment_mode' => 'Shippment Mode',
            'docket_number' => 'Docket Number',
            'seal_number' => 'Seal Number',
            'vehicle_number' => 'Vehicle Number',
            'shipped_date' => 'Shipped Date',
            'estimate_delivery_date' => 'Estimate Delivery Date',
            'delivery_date' => 'Delivery Date',
            'status' => 'Status',
            'pickup_id' => 'Pickup ID',
            'deleted' => 'Deleted',
            'halting_status' => 'halting_status',
            'halting_days' => 'halting_days',
            'halting_reason' => 'halting_reason'
        ];
    }

    /**
     * Gets query for [[Pickup]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPickup()
    {
        return $this->hasOne(Pickup::class, ['pickup_id' => 'pickup_id']);
    }
    public function saveShippedDetails($entityId)
    {
        $shipped_details = $_POST['shipped_details']??[];
        if (!empty($shipped_details)) {
            if (count($shipped_details) > 0) {
                foreach ($shipped_details as $sd) {
                    $sd['pickup_id'] = $entityId;
                    $sd_obj = new ShippedDetails();
                    $sd_obj->attributes = $sd;
                    $sd_obj->validate();
                    $sd_obj->save(false);
                }
            }
        }
    }
}
