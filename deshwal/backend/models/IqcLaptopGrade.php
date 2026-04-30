<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "iqc_laptop_grade".
 *
 * @property int $laptop_grade_id
 * @property string|null $laptop_grade_no
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $screen
 * @property string|null $a_panel
 * @property string|null $b_panel
 * @property string|null $c_panel
 * @property string|null $d_panel
 * @property string|null $touch_pad
 * @property string|null $logic_card
 * @property string|null $battery
 * @property string|null $camera
 * @property string|null $keyboard
 * @property string|null $speaker
 * @property string|null $microphone
 * @property string|null $fingerprint
 * @property string|null $usb_port
 * @property string|null $hdmi_port
 * @property string|null $lan_port
 * @property string|null $hinge
 * @property string|null $ram_present
 * @property string|null $ram_status
 * @property string|null $hdd_present
 * @property string|null $hdd_health
 * @property int $deleted
 */
class IqcLaptopGrade extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iqc_laptop_grade';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime'], 'safe'],
            [['laptop_grade_no', 'screen', 'a_panel', 'b_panel', 'c_panel', 'd_panel', 'touch_pad', 'logic_card', 'battery', 'camera', 'keyboard', 'speaker', 'microphone', 'fingerprint','grade'], 'safe'],
            [['usb_port', 'hdmi_port', 'lan_port', 'hinge', 'ram_present', 'ram_status', 'hdd_present', 'hdd_health'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'laptop_grade_id' => 'Laptop Grade ID',
            'laptop_grade_no' => 'Laptop Grade No',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'screen' => 'Screen',
            'a_panel' => 'A Panel',
            'b_panel' => 'B Panel',
            'c_panel' => 'C Panel',
            'd_panel' => 'D Panel',
            'touch_pad' => 'Touch Pad',
            'logic_card' => 'Logic Card',
            'battery' => 'Battery',
            'camera' => 'Camera',
            'keyboard' => 'Keyboard',
            'speaker' => 'Speaker',
            'microphone' => 'Microphone',
            'fingerprint' => 'Fingerprint',
            'usb_port' => 'Usb Port',
            'hdmi_port' => 'Hdmi Port',
            'lan_port' => 'Lan Port',
            'hinge' => 'Hinge',
            'ram_present' => 'Ram Present',
            'ram_status' => 'Ram Status',
            'hdd_present' => 'Hdd Present',
            'hdd_health' => 'Hdd Health',
            'deleted' => 'Deleted',
        ];
    }
}
