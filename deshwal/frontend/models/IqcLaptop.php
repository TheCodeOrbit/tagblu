<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "iqc_laptop".
 *
 * @property int $iqclaptop_id
 * @property string|null $iqc_laptop_no
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $tag_no
 * @property string|null $serial_no
 * @property string|null $sub_category
 * @property string|null $make
 * @property int|null $copy_previous_submitted_data
 * @property string|null $lot_number
 * @property string|null $model
 * @property string|null $cpu
 * @property string|null $generation
 * @property string|null $hdd
 * @property string|null $ram1
 * @property string|null $hdd1
 * @property string|null $category
 * @property string|null $other_category
 * @property string|null $hdd_capacity
 * @property string|null $ssd_capacity
 * @property string|null $hdd_health
 * @property string|null $hdd_category
 * @property string|null $hdd_serial_numbers
 * @property string|null $touch_pad
 * @property int|null $screen_size
 * @property string|null $screen
 * @property int|null $hinge
 * @property int|null $hinge_cover
 * @property int|null $defected_side
 * @property int|null $microphone
 * @property int|null $power_panel
 * @property int|null $sound
 * @property int|null $web_cam
 * @property int $port
 * @property int|null $usb
 * @property int|null $dvd
 * @property int|null $dvd_flap
 * @property int|null $battery
 * @property int|null $battery_health
 * @property int|null $int_battery
 * @property int|null $int_battery_health
 * @property int|null $battery_cable
 * @property int|null $status
 * @property int|null $grade
 * @property string|null $location
 * @property string|null $remarks
 * @property int|null $ram
 * @property int|null $ram_type
 * @property int|null $ram_capacity
 * @property int|null $ram_status
 * @property int|null $all_ok
 * @property int|null $does_laptop_switch
 * @property int|null $bios_password
 * @property string|null $front_panel
 * @property string|null $panel
 * @property int|null $palmrest_panel
 * @property int|null $base_panel
 * @property int|null $logic_card
 * @property int|null $base
 * @property int|null $keyboard
 * @property int|null $hdd_connector
 * @property int|null $hdd_cover
 * @property int|null $hdd_casing
 * @property int|null $wifi
 * @property int|null $boot
 * @property int|null $ram_cover
 * @property int|null $R2V3_grading_category
 * @property string|null $battery_health1
 * @property int|null $deleted
 * @property string|null $provide_screen_description
 */
class IqcLaptop extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iqc_laptop';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime', 'port'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'copy_previous_submitted_data', 'screen_size', 'hinge', 'hinge_cover', 'defected_side', 'microphone', 'power_panel', 'sound', 'web_cam', 'port', 'usb', 'dvd', 'dvd_flap', 'battery', 'battery_health', 'int_battery', 'int_battery_health', 'battery_cable', 'status', 'grade', 'ram', 'ram_type', 'ram_capacity', 'ram_status', 'all_ok', 'does_laptop_switch', 'bios_password',  'logic_card', 'base', 'keyboard', 'hdd_connector', 'hdd_cover', 'hdd_casing', 'wifi', 'boot', 'ram_cover', 'R2V3_grading_category', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime','make', 'model',  'cpu', 'generation'], 'safe'],
            [['iqc_laptop_no'], 'string', 'max' => 100],
            [['tag_no', 'serial_no', 'lot_number', 'ram1', 'hdd1', 'hdd_category', 'touch_pad', 'screen', 'front_panel', 'panel', 'battery_health1', 'provide_screen_description'], 'string', 'max' => 100],
            [['sub_category', 'hdd', 'category', 'hdd_capacity', 'ssd_capacity', 'hdd_health', 'location'], 'string', 'max' => 5],
            [['other_category', 'remarks'], 'string', 'max' => 200],
            [['hdd_serial_numbers','palmrest_panel', 'base_panel'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'iqclaptop_id' => 'IQC Laptop ID',
            'iqc_laptop_no' => 'IQC Laptop No',
            'ownerid' => 'Owner ID',
            'creatorid' => 'Creator ID',
            'modifiedby' => 'Modified By',
            'createdtime' => 'Created Time',
            'modifiedtime' => 'Modified Time',
            'tag_no' => 'Tag No',
            'serial_no' => 'Serial No',
            'sub_category' => 'Sub Category',
            'make' => 'Make',
            'copy_previous_submitted_data' => 'Copy Previous Submitted Data',
            'lot_number' => 'Lot Number',
            'model' => 'Model',
            'cpu' => 'CPU',
            'generation' => 'Generation',
            'hdd' => 'HDD',
            'ram1' => 'RAM1',
            'hdd1' => 'HDD1',
            'category' => 'Category',
            'other_category' => 'Other Category',
            'hdd_capacity' => 'HDD Capacity',
            'ssd_capacity' => 'SSD Capacity',
            'hdd_health' => 'HDD Health',
            'hdd_category' => 'HDD Category',
            'hdd_serial_numbers' => 'HDD Serial Numbers',
            'touch_pad' => 'Touch Pad',
            'screen_size' => 'Screen Size',
            'screen' => 'Screen',
            'hinge' => 'Hinge',
            'hinge_cover' => 'Hinge Cover',
            'defected_side' => 'Defected Side',
            'microphone' => 'Microphone',
            'power_panel' => 'Power Panel',
            'sound' => 'Sound',
            'web_cam' => 'Web Cam',
            'port' => 'Port',
            'usb' => 'USB',
            'dvd' => 'DVD',
            'dvd_flap' => 'DVD Flap',
            'battery' => 'Battery',
            'battery_health' => 'Battery Health',
            'int_battery' => 'Internal Battery',
            'int_battery_health' => 'Internal Battery Health',
            'battery_cable' => 'Battery Cable',
            'status' => 'Status',
            'grade' => 'Grade',
            'location' => 'Location',
            'remarks' => 'Remarks',
            'ram' => 'RAM',
            'ram_type' => 'RAM Type',
            'ram_capacity' => 'RAM Capacity',
            'ram_status' => 'RAM Status',
            'all_ok' => 'All OK',
            'does_laptop_switch' => 'Does Laptop Switch',
            'bios_password' => 'BIOS Password',
            'front_panel' => 'Front Panel',
            'panel' => 'Panel',
            'palmrest_panel' => 'Palmrest Panel',
            'base_panel' => 'Base Panel',
            'logic_card' => 'Logic Card',
            'base' => 'Base',
            'keyboard' => 'Keyboard',
            'hdd_connector' => 'HDD Connector',
            'hdd_cover' => 'HDD Cover',
            'hdd_casing' => 'HDD Casing',
            'wifi' => 'WiFi',
            'boot' => 'Boot',
            'ram_cover' => 'RAM Cover',
            'R2V3_grading_category' => 'R2V3 Grading Category',
            'battery_health1' => 'Battery Health 1',
            'deleted' => 'Deleted',
            'provide_screen_description' => 'Provide Screen Description',
        ];
    }
}
