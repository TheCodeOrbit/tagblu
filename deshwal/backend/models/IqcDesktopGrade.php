<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "iqc_desktop_grade".
 *
 * @property int $desktop_grade_id
 * @property string|null $iqc_desktop_grade_no
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $grade
 * @property string|null $cabinet
 * @property string|null $speaker
 * @property string|null $microphone
 * @property string|null $usb_port
 * @property string|null $hdmi_port
 * @property string|null $lan_port
 * @property string|null $vga_port
 * @property string|null $ram_present
 * @property string|null $ram_status
 * @property string|null $hdd_present
 * @property string|null $hdd_health
 * @property int $deleted
 */
class IqcDesktopGrade extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iqc_desktop_grade';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'deleted'], 'safe'],
            [['createdtime', 'modifiedtime'], 'safe'],
            [['iqc_desktop_grade_no', 'cabinet', 'speaker', 'microphone'], 'safe'],
            [['grade'], 'safe'],
            [['usb_port', 'hdmi_port', 'lan_port', 'vga_port', 'ram_present', 'ram_status', 'hdd_present', 'hdd_health'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'desktop_grade_id' => 'Desktop Grade ID',
            'iqc_desktop_grade_no' => 'Iqc Desktop Grade No',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'grade' => 'Grade',
            'cabinet' => 'Cabinet',
            'speaker' => 'Speaker',
            'microphone' => 'Microphone',
            'usb_port' => 'Usb Port',
            'hdmi_port' => 'Hdmi Port',
            'lan_port' => 'Lan Port',
            'vga_port' => 'Vga Port',
            'ram_present' => 'Ram Present',
            'ram_status' => 'Ram Status',
            'hdd_present' => 'Hdd Present',
            'hdd_health' => 'Hdd Health',
            'deleted' => 'Deleted',
        ];
    }
}
