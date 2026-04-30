<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "iqc_tft".
 *
 * @property int $iqctft_id
 * @property string|null $iqc_tft_no
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $tag_number
 * @property string|null $serial_number
 * @property string|null $fill_previous_lot_no
 * @property string|null $lot_no
 * @property int|null $screen_size
 * @property string|null $make
 * @property string|null $other_description
 * @property int|null $screen_status
 * @property string|null $screen_type
 * @property int|null $location
 * @property int|null $power
 * @property int|null $power_button
 * @property int|null $menu_button
 * @property int|null $vga_port
 * @property int|null $hdmi_port
 * @property string|null $dp_port
 * @property int|null $dvi_port
 * @property int|null $usb_port
 * @property int|null $status
 * @property int|null $grade
 * @property string|null $ramarks
 * @property int|null $r2v3_grading_category
 * @property int|null $deleted
 */
class IqcTft extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iqc_tft';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'screen_size', 'screen_status', 'location', 'power', 'power_button', 'menu_button', 'vga_port', 'hdmi_port', 'dvi_port', 'usb_port', 'status', 'grade', 'r2v3_grading_category', 'deleted'], 'safe'],
            [['createdtime', 'modifiedtime'], 'safe'],
            [['iqc_tft_no', 'screen_type'], 'safe',],
            [['tag_number', 'serial_number', 'fill_previous_lot_no', 'lot_no', 'make', 'other_description', 'dp_port', 'ramarks'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'iqctft_id' => 'Iqctft ID',
            'iqc_tft_no' => 'Iqc Tft No',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'tag_number' => 'Tag Number',
            'serial_number' => 'Serial Number',
            'fill_previous_lot_no' => 'Fill Previous Lot No',
            'lot_no' => 'Lot No',
            'screen_size' => 'Screen Size',
            'make' => 'Make',
            'other_description' => 'Other Description',
            'screen_status' => 'Screen Status',
            'screen_type' => 'Screen Type',
            'location' => 'Location',
            'power' => 'Power',
            'power_button' => 'Power Button',
            'menu_button' => 'Menu Button',
            'vga_port' => 'Vga Port',
            'hdmi_port' => 'Hdmi Port',
            'dp_port' => 'Dp Port',
            'dvi_port' => 'Dvi Port',
            'usb_port' => 'Usb Port',
            'status' => 'Status',
            'grade' => 'Grade',
            'ramarks' => 'Ramarks',
            'r2v3_grading_category' => 'R2v3 Grading Category',
            'deleted' => 'Deleted',
        ];
    }
}
