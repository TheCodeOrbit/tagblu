<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "degaussing_format".
 *
 * @property int $degaussing_format_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $degaussing_format_no
 * @property string|null $category
 * @property string|null $hdd_make
 * @property string|null $hdd_size
 * @property string|null $capacity
 * @property string|null $weight
 * @property string|null $degaussing
 * @property string|null $currency
 * @property string|null $hdd_type
 * @property string|null $hdd_serial_number
 * @property string|null $size
 * @property string|null $image_available
 * @property string|null $total_hdd_count
 * @property string|null $hdd_drilled
 * @property string|null $deleted
 */
class DegaussingFormat extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'degaussing_format';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby','deleted'], 'integer'],
            [['createdtime', 'modifiedtime','image'], 'safe'],
            [['exchange_rate'],'string', 'max' => 10],
            [['degaussing_format_no', 'category', 'hdd_make', 'hdd_size', 'capacity', 'weight', 'degaussing', 'currency', 'hdd_type', 'hdd_serial_number', 'size', 'image_available', 'total_hdd_count', 'hdd_drilled'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'degaussing_format_id' => 'Degaussing Format ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'degaussing_format_no' => 'Degaussing Format No',
            'category' => 'Category',
            'hdd_make' => 'Hdd Make',
            'hdd_size' => 'Hdd Size',
            'capacity' => 'Capacity',
            'weight' => 'Weight',
            'degaussing' => 'Degaussing',
            'currency' => 'Currency',
            'hdd_type' => 'Hdd Type',
            'hdd_serial_number' => 'Hdd Serial Number',
            'size' => 'Size',
            'image_available' => 'Image Available',
            'image' => "Upload Image",
            'total_hdd_count' => 'Total Hdd Count',
            'hdd_drilled' => 'Hdd Drilled',
            'deleted' => 'Deleted',
        ];
    }
}
