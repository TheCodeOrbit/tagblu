<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "shredding_asset_details".
 *
 * @property int $shredding_asset_id
 * @property int|null $shredding_id
 * @property string|null $laptop_serial_no
 * @property string|null $hdd_sdd_serial_no
 * @property string|null $make
 * @property string|null $type
 * @property string|null $capacity
 * @property string|null $software_name
 * @property string|null $certificate
 * @property string|null $shredding_date
 * @property int|null $shredding_completed
 * @property int|null $creatorid
 * @property int|null $modifiedby
 * @property string|null $createdtime
 * @property string|null $modifiedtime
 * @property int $deleted
 */
class ShreddingAssetDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shredding_asset_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['shredding_id', 'shredding_completed', 'creatorid', 'modifiedby', 'deleted'], 'integer'],
            [['shredding_date', 'createdtime', 'modifiedtime'], 'safe'],
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
            'shredding_asset_id' => 'Shredding Asset ID',
            'shredding_id' => 'Shredding ID',
            'laptop_serial_no' => 'Laptop Serial No',
            'hdd_sdd_serial_no' => 'Hdd Sdd Serial No',
            'make' => 'Make',
            'type' => 'Type',
            'capacity' => 'Capacity',
            'software_name' => 'Software Name',
            'certificate' => 'Certificate',
            'shredding_date' => 'Shredding Date',
            'shredding_completed' => 'Shredding Completed',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'deleted' => 'Deleted',
        ];
    }
    public function saveShreddingAssets($entityId)
    {
        $asset_list = $_POST['shredding_asset_details']??[];
        //print_r($asset_list);die;
        $hdd_completed = 0;
        if (!empty($asset_list)) {
            if (count($asset_list) > 0) {
                foreach ($asset_list as $sd) {
                    $sd['shredding_id'] = $entityId;
                    if($sd["shredding_completed"] == 1 && !empty($sd["certificate"])){
                        $hdd_completed++;
                    }
                    $sd_obj = new ShreddingAssetDetails();
                    $sd_obj->attributes = $sd;
                    $sd_obj->validate();
                    $sd_obj->save(false);
                }
            }
        }
        return $hdd_completed;
    }
}
