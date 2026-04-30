<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "iqc_desktop".
 *
 * @property int $iqcdesktop_id
 * @property string|null $iqc_desktop_no
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $sub_category
 * @property string|null $checkbox
 * @property string|null $serial_no
 * @property string|null $tag_no
 * @property string|null $lot_no
 * @property string|null $make
 * @property string|null $model
 * @property string|null $motherboard
 * @property string|null $generation
 * @property string|null $motherboard_status
 * @property string|null $provide_description_mb
 * @property string|null $cpu
 * @property int|null $processors
 * @property string|null $cpu_status
 * @property string|null $provide_description_cpu
 * @property string|null $ram_slot
 * @property string|null $slot_no
 * @property string|null $ram
 * @property string|null $capacity
 * @property string|null $ram_status
 * @property string|null $provide_description_ram
 * @property string|null $hdd
 * @property int|null $hdd_category
 * @property int|null $hdd_capacity
 * @property string|null $health_per
 * @property string|null $provide_description_hdd
 * @property string|null $display
 * @property string $smps
 * @property string $smps_status
 * @property string|null $hdd_casing
 * @property int|null $usb
 * @property int|null $cabinate
 * @property int|null $status
 * @property int|null $location
 * @property string|null $grade
 * @property string|null $R2V3_grading_category
 * @property string|null $remarks
 * @property int|null $deleted
 */
class IqcDesktop extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iqc_desktop';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime', 'smps', 'smps_status'], 'safe'],
            [['ownerid', 'creatorid', 'modifiedby', 'processors', 'hdd_category', 'hdd_capacity', 'usb', 'cabinate', 'status', 'location', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime'], 'safe'],
            [['iqc_desktop_no', 'cpu', 'cpu_status', 'ram_status', 'hdd_casing'], 'string', 'max' => 100],
            [['sub_category', 'checkbox', 'serial_no', 'tag_no', 'lot_no', 'make', 'model', 'motherboard', 'generation', 'motherboard_status', 'provide_description_mb', 'provide_description_cpu', 'ram_slot', 'slot_no', 'ram', 'capacity', 'provide_description_ram', 'hdd', 'health_per', 'provide_description_hdd', 'display', 'smps', 'smps_status', 'grade', 'R2V3_grading_category', 'remarks'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'iqcdesktop_id' => 'Iqcdesktop ID',
            'iqc_desktop_no' => 'Iqc Desktop No',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'sub_category' => 'Sub Category',
            'checkbox' => 'Checkbox',
            'serial_no' => 'Serial No',
            'tag_no' => 'Tag No',
            'lot_no' => 'Lot No',
            'make' => 'Make',
            'model' => 'Model',
            'motherboard' => 'Motherboard',
            'generation' => 'Generation',
            'motherboard_status' => 'Motherboard Status',
            'provide_description_mb' => 'Provide Description Mb',
            'cpu' => 'Cpu',
            'processors' => 'Processors',
            'cpu_status' => 'Cpu Status',
            'provide_description_cpu' => 'Provide Description Cpu',
            'ram_slot' => 'Ram Slot',
            'slot_no' => 'Slot No',
            'ram' => 'Ram',
            'capacity' => 'Capacity',
            'ram_status' => 'Ram Status',
            'provide_description_ram' => 'Provide Description Ram',
            'hdd' => 'Hdd',
            'hdd_category' => 'Hdd Category',
            'hdd_capacity' => 'Hdd Capacity',
            'health_per' => 'Health Per',
            'provide_description_hdd' => 'Provide Description Hdd',
            'display' => 'Display',
            'smps' => 'Smps',
            'smps_status' => 'Smps Status',
            'hdd_casing' => 'Hdd Casing',
            'usb' => 'Usb',
            'cabinate' => 'Cabinate',
            'status' => 'Status',
            'location' => 'Location',
            'grade' => 'Grade',
            'R2V3_grading_category' => 'R2v3 Grading Category',
            'remarks' => 'Remarks',
            'deleted' => 'Deleted',
        ];
    }
}
