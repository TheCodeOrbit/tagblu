<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "vehicle_planning".
 *
 * @property int $vehicle_planning_id
 * @property int $transporter_name_vp
 * @property int $vendor_lookup
 * @property float $amount
 * @property string $attach_quote
 * @property int $pickup_id
 * @property int $deleted
 *
 * @property Pickup $pickup
 */
class VehiclePlanning extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vehicle_planning';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pickup_id'], 'required'],
            [['pickup_id', 'deleted'], 'integer'],
            [['amount'], 'number'],
            [['transporter_name_vp', 'vendor_lookup', 'attach_quote','schedule_pickup_date','pickup_doc','remarks'], 'safe'],
            [['pickup_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pickup::class, 'targetAttribute' => ['pickup_id' => 'pickup_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'vehicle_planning_id' => 'Vehicle Planning ID',
            'transporter_name_vp' => 'Transporter Name',
            'vendor_lookup' => 'Vendor Lookup',
            'amount' => 'Amount',
            'attach_quote' => 'Attach Quote',
            'pickup_id' => 'Pickup ID',
            'deleted' => 'Deleted',
            'schedule_pickup_date' =>'Schedule Pickup Date',
            'pickup_doc' => 'Pickup Document',
            'remarks' => 'remarks'
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
    public function saveVehiclePlanning($entityId)
    {
        $vehicle_planning = $_POST['vehicle_planning']??[];
        // print_r($savePickupDocumentDetails);die;
        if (!empty($vehicle_planning)) {
            if (count($vehicle_planning) > 0) {
                foreach ($vehicle_planning as $pm) {
                    $pm['pickup_id'] = $entityId;
                    $pm_obj = new VehiclePlanning();
                    $pm_obj->attributes = $pm;
                    $pm_obj->validate();
                    $pm_obj->save(false);
                }
            }
        }
    }
}
