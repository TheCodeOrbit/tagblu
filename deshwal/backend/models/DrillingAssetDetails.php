<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "drilling_asset_details".
 *
 * @property int $drilling_asset_id
 * @property int|null $drilling_id
 * @property string|null $laptop_serial_no
 * @property string|null $hdd_sdd_serial_no
 * @property string|null $make
 * @property string|null $type
 * @property string|null $capacity
 * @property string|null $software_name
 * @property string|null $certificate
 * @property string|null $drilling_date
 * @property int|null $drilling_completed
 * @property int|null $creatorid
 * @property int|null $modifiedby
 * @property string|null $createdtime
 * @property string|null $modifiedtime
 * @property int $deleted
 */
class DrillingAssetDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'drilling_asset_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['drilling_id', 'drilling_completed', 'creatorid', 'modifiedby', 'deleted'], 'integer'],
            [['drilling_date', 'createdtime', 'modifiedtime'], 'safe'],
            [['deleted'], 'required'],
            [['laptop_serial_no', 'hdd_sdd_serial_no', 'make', 'type', 'capacity', 'software_name', 'certificate'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'drilling_asset_id' => 'Drilling Asset ID',
            'drilling_id' => 'Drilling ID',
            'laptop_serial_no' => 'Laptop Serial No',
            'hdd_sdd_serial_no' => 'Hdd Sdd Serial No',
            'make' => 'Make',
            'type' => 'Type',
            'capacity' => 'Capacity',
            'software_name' => 'Software Name',
            'certificate' => 'Certificate',
            'drilling_date' => 'Drilling Date',
            'drilling_completed' => 'Drilling Completed',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'deleted' => 'Deleted',
        ];
    }
    public function saveDrillingAssets($entityId)
    {
        $asset_list = $_POST['drilling_asset_details']??[];
        //print_r($asset_list);die;
        $hdd_completed = 0;
        if (!empty($asset_list)) {
            if (count($asset_list) > 0) {
                foreach ($asset_list as $sd) {
                    $sd['drilling_id'] = $entityId;
                    if($sd["drilling_completed"] == 1 && !empty($sd["certificate"])){
                        $hdd_completed++;
                    }
                    $sd_obj = new DrillingAssetDetails();
                    $sd_obj->attributes = $sd;
                    $sd_obj->validate();
                    $sd_obj->save(false);
                }
            }
        }
        return $hdd_completed;
    }
}
