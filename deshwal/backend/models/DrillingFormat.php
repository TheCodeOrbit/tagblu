<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "drilling_format".
 *
 * @property int $drilling_format_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property int $drilling_format_no
 * @property string|null $category
 * @property string|null $hdd_make
 * @property string|null $hdd_size
 * @property string|null $capacity
 * @property string|null $weight
 * @property string|null $drilling
 * @property string|null $hdd_type
 * @property string|null $hdd_serial_number
 * @property string|null $size
 * @property string|null $image_available
 * @property string|null $total_hdd_count
 * @property string|null $hdd_drilled
 * @property string|null $pending_hdd
 * @property int $deleted
 */
class DrillingFormat extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'drilling_format';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime', 'drilling_format_no','drilling'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime','image'], 'safe'],
            [['category', 'hdd_make', 'hdd_size', 'capacity', 'weight', 'hdd_type', 'hdd_serial_number', 'size', 'image_available', 'total_hdd_count', 'hdd_drilled', 'pending_hdd'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'drilling_format_id' => 'Drilling Format ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'category' => 'Category',
            'hdd_make' => 'Hdd Make',
            'hdd_size' => 'Hdd Size',
            'capacity' => 'Capacity',
            'drilling' => 'Drilling',
            'weight' => 'Weight',
            'drilling_format_no' => 'Drilling Format No',
            'hdd_type' => 'Hdd Type',
            'hdd_serial_number' => 'Hdd Serial Number',
            'size' => 'Size',
            'image_available' => 'Image Available',
            'image' => 'Upload Image',
            'total_hdd_count' => 'Total Hdd Count',
            'hdd_drilled' => 'Hdd Drilled',
            'pending_hdd' => 'Pending Hdd',
            'deleted' => 'Deleted',
        ];
    }
}
