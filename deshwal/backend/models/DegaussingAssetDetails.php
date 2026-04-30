<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "degaussing_asset_details".
 *
 * @property int $degaussing_asset_id
 * @property int|null $degaussinginfo_id
 * @property string|null $laptop_serial_no
 * @property string|null $hdd_sdd_serial_no
 * @property string|null $make
 * @property string|null $type
 * @property string|null $capacity
 * @property string|null $image_before_activity
 * @property string|null $image_after_activity
 * @property int $deleted
 */
class DegaussingAssetDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'degaussing_asset_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['degaussinginfo_id', 'deleted'], 'integer'],
            [['laptop_serial_no', 'hdd_sdd_serial_no', 'make', 'type', 'capacity', 'image_before_activity', 'image_after_activity'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'degaussing_asset_id' => 'Degaussing Asset ID',
            'degaussinginfo_id' => 'Degaussinginfo ID',
            'laptop_serial_no' => 'Laptop Serial No',
            'hdd_sdd_serial_no' => 'Hdd Sdd Serial No',
            'make' => 'Make',
            'type' => 'Type',
            'capacity' => 'Capacity',
            'image_before_activity' => 'Image Before Activity',
            'image_after_activity' => 'Image After Activity',
            'deleted' => 'Deleted',
        ];
    }

    public function saveDegaussingAssets($entityId)
    {
        $asset_list = $_POST['degaussing_asset_details']??[];
        $hdd_completed = 0;
        if (!empty($asset_list)) {
            if (count($asset_list) > 0) {
                foreach ($asset_list as $sd) {
                    $sd['degaussinginfo_id'] = $entityId;
                    if(!empty($sd["laptop_serial_no"]) && !empty($sd['hdd_sdd_serial_no']) && !empty($sd['make']) && !empty($sd['type']) && !empty($sd['capacity']) && !empty($sd['image_before_activity']) && !empty($sd['image_after_activity']) ){
                        $hdd_completed++;
                    }
                    $sd_obj = new DegaussingAssetDetails();
                    $sd_obj->attributes = $sd;
                    $sd_obj->validate();
                    $sd_obj->save(false);
                }
            }
        }
        return $hdd_completed;
    }
}
