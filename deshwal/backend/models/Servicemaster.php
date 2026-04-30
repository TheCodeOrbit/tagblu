<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "servicemaster".
 *
 * @property int $servicemaster_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $servicemaster_no
 * @property string|null $service_name
 * @property string|null $service_description
 * @property string|null $hsn_code
 * @property string|null $taxpreference
 * @property string|null $active
 * @property string|null $service_nature
 * @property string|null $category
 * @property string|null $sub_category
 * @property string|null $uom
 * @property string|null $remarks
 * @property string|null $unit_band
 * @property float|null $cost_price
 * @property float|null $gst_percentage
 * @property float|null $purchase_price
 * @property float|null $min_margin
 * @property float|null $std_margin
 * @property float|null $max_margin
 * @property string $valid_from
 * @property int $deleted
 */
class Servicemaster extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'servicemaster';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime', 'valid_from'], 'safe'],
            [['cost_price', 'gst_percentage', 'purchase_price', 'min_margin', 'std_margin', 'max_margin'], 'number'],
            [['servicemaster_no', 'service_name', 'service_description', 'hsn_code', 'taxpreference', 'active', 'service_nature', 'category', 'sub_category', 'uom', 'remarks', 'unit_band'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'servicemaster_id' => 'Servicemaster ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'servicemaster_no' => 'Servicemaster No',
            'service_name' => 'Service Name',
            'service_description' => 'Service Description',
            'hsn_code' => 'Hsn Code',
            'taxpreference' => 'Taxpreference',
            'active' => 'Active',
            'service_nature' => 'Service Nature',
            'category' => 'Category',
            'sub_category' => 'Sub Category',
            'uom' => 'Uom',
            'remarks' => 'Remarks',
            'unit_band' => 'Unit Band',
            'cost_price' => 'Cost Price',
            'gst_percentage' => 'Gst Percentage',
            'purchase_price' => 'Purchase Price',
            'min_margin' => 'Min Margin',
            'std_margin' => 'Std Margin',
            'max_margin' => 'Max Margin',
            'valid_from' => 'Valid From',
            'deleted' => 'Deleted',
        ];
    }
}
