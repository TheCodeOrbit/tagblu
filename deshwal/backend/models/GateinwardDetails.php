<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "gateinward_details".
 *
 * @property int $gateinward_details_id
 * @property int $gateinward_id
 * @property string|null $transporter_name
 * @property string|null $vehicle_number
 * @property string|null $account_name
 * @property string|null $shipped_date
 * @property string|null $shippment_mode
 * @property int $deleted
 */
class GateinwardDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gateinward_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['gateinward_id'], 'required'],
            [['gateinward_id', 'deleted'], 'integer'],
            [['account_name','transporter_name', 'vehicle_number', 'shippment_mode','shipped_date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'gateinward_details_id' => 'Gateinward Details ID',
            'gateinward_id' => 'Gateinward ID',
            'transporter_name' => 'Transporter Name',
            'vehicle_number' => 'Vehicle Number',
            'account_name' => 'Account Name',
            'shipped_date' => 'Shipped Date',
            'shippment_mode' => 'Shippment Mode',
            'deleted' => 'Deleted',
        ];
    }

    public function saveGateinwardDetails($entityId)
    {
        $asset_list = $_POST['gateinward_details']??[];
        if (!empty($asset_list)) {
            if (count($asset_list) > 0) {
                foreach ($asset_list as $sd) {
                    $sd['gateinward_id'] = $entityId;
                    $sd_obj = new GateinwardDetails();
                    $sd_obj->attributes = $sd;
                    $sd_obj->validate();
                    $sd_obj->save(false);
                }
            }
        }
    }
}
