<?php

namespace app\models;

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
 * @property int $tag_no
 * @property int $serial_no
 * @property string $sub_category
 * @property string $make
 * @property string $copy_previous_submitted_data
 * @property int $lot_number
 * @property string $model
 * @property string $cpu
 * @property string $generation
 * @property string $hdd
 * @property string $category
 * @property string $other_category
 * @property string $hdd_capacity
 * @property string $ssd_capacity
 * @property string $hdd_health
 * @property string|null $hdd_category
 * @property string $hdd_serial_numbers
 * @property int $touch_pad
 * @property int $screen_size
 * @property int $screen
 * @property int $hinge
 * @property int $hinge_cover
 * @property int $defected_side
 * @property int $microphone
 * @property int $power_panel
 * @property int $sound
 * @property int $web_cam
 * @property int $usb
 * @property int $dvd
 * @property int $dvd_flap
 * @property int $battery
 * @property int $battery_health
 * @property int $int_battery
 * @property int $int_battery_health
 * @property int $battery_cable
 * @property int $status
 * @property int $grade
 * @property string $location
 * @property string $remarks
 * @property int $ram
 * @property int $ram_type
 * @property int $ram_capacity
 * @property int|null $ram_status
 * @property int $all_ok
 * @property int $does_laptop_switch
 * @property int $bios_password
 * @property int $front_panel
 * @property int $panel
 * @property int $logic_card
 * @property int $base
 * @property int $keyboard
 * @property int $hdd_connector
 * @property int $hdd_cover
 * @property int $hdd_casing
 * @property int $wifi
 * @property int $boot
 * @property int $ram_cover
 * @property int $R2V3_grading_category
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
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime', 'tag_no', 'serial_no', 'sub_category', 'make', 'copy_previous_submitted_data', 'lot_number', 'model', 'cpu', 'generation', 'hdd', 'category', 'other_category', 'hdd_capacity', 'ssd_capacity', 'hdd_health', 'hdd_serial_numbers', 'touch_pad', 'screen_size', 'screen', 'hinge', 'hinge_cover', 'defected_side', 'microphone', 'power_panel', 'sound', 'web_cam', 'usb', 'dvd', 'dvd_flap', 'battery', 'battery_health', 'int_battery', 'int_battery_health', 'battery_cable', 'status', 'grade', 'location', 'remarks', 'ram', 'ram_type', 'ram_capacity', 'all_ok', 'does_laptop_switch', 'bios_password', 'front_panel', 'panel', 'logic_card', 'base', 'keyboard', 'hdd_connector', 'hdd_cover', 'hdd_casing', 'wifi', 'boot', 'ram_cover', 'R2V3_grading_category'], 'safe'],
            [['ownerid', 'creatorid', 'modifiedby', 'tag_no', 'serial_no', 'lot_number', 'touch_pad', 'screen_size', 'screen', 'hinge', 'hinge_cover', 'defected_side', 'microphone', 'power_panel', 'sound', 'web_cam', 'usb', 'dvd', 'dvd_flap', 'battery', 'battery_health', 'int_battery', 'int_battery_health', 'battery_cable', 'status', 'grade', 'ram', 'ram_type', 'ram_capacity', 'ram_status', 'all_ok', 'does_laptop_switch', 'bios_password', 'front_panel', 'panel', 'logic_card', 'base', 'keyboard', 'hdd_connector', 'hdd_cover', 'hdd_casing', 'wifi', 'boot', 'ram_cover', 'R2V3_grading_category'], 'safe'],
            [['createdtime', 'modifiedtime'], 'safe'],
            [['iqc_laptop_no'], 'string', 'max' => 100],
            [['provide_screen_description'],'string','max'=>200],
            [['sub_category', 'make', 'copy_previous_submitted_data', 'model', 'cpu', 'generation', 'hdd', 'category', 'other_category', 'hdd_capacity', 'ssd_capacity', 'hdd_health', 'hdd_category', 'hdd_serial_numbers', 'location', 'remarks'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'iqclaptop_id' => 'Iqclaptop ID',
            'iqc_laptop_no' => 'Iqc Laptop No',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'tag_no' => 'Tag No',
            'serial_no' => 'Serial No',
            'sub_category' => 'Sub Category',
            'make' => 'Make',
            'copy_previous_submitted_data' => 'Copy Previous Submitted Data',
            'lot_number' => 'Lot Number',
            'model' => 'Model',
            'cpu' => 'Cpu',
            'generation' => 'Generation',
            'hdd' => 'Hdd',
            'category' => 'Category',
            'other_category' => 'Other Category',
            'hdd_capacity' => 'Hdd Capacity',
            'ssd_capacity' => 'Ssd Capacity',
            'hdd_health' => 'Hdd Health',
            'hdd_category' => 'Hdd Category',
            'hdd_serial_numbers' => 'Hdd Serial Numbers',
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
            'usb' => 'Usb',
            'dvd' => 'Dvd',
            'dvd_flap' => 'Dvd Flap',
            'battery' => 'Battery',
            'battery_health' => 'Battery Health',
            'int_battery' => 'Int Battery',
            'int_battery_health' => 'Int Battery Health',
            'battery_cable' => 'Battery Cable',
            'status' => 'Status',
            'grade' => 'Grade',
            'location' => 'Location',
            'remarks' => 'Remarks',
            'ram' => 'Ram',
            'ram_type' => 'Ram Type',
            'ram_capacity' => 'Ram Capacity',
            'ram_status' => 'Ram Status',
            'all_ok' => 'All Ok',
            'does_laptop_switch' => 'Does Laptop Switch',
            'bios_password' => 'Bios Password',
            'front_panel' => 'Front Panel',
            'panel' => 'Panel',
            'logic_card' => 'Logic Card',
            'base' => 'Base',
            'keyboard' => 'Keyboard',
            'hdd_connector' => 'Hdd Connector',
            'hdd_cover' => 'Hdd Cover',
            'hdd_casing' => 'Hdd Casing',
            'wifi' => 'Wifi',
            'boot' => 'Boot',
            'ram_cover' => 'Ram Cover',
            'R2V3_grading_category' => 'R2v3 Grading Category',
            'provide_screen_description' => "Provide Screen Description"
        ];
    }
}
