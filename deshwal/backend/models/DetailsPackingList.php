<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "details_packing_list".
 *
 * @property int $details_packing_id
 * @property string|null $box_number
 * @property string|null $sub_category
 * @property string|null $condition
 * @property string|null $count
 * @property string|null $uom
 * @property string|null $remarks
 * @property string|null $upload_image
 * @property int|null $pickup_id
 * @property int $deleted
 *
 * @property Pickup $pickup
 */
class DetailsPackingList extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'details_packing_list';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pickup_id', 'deleted'], 'integer'],
            [['box_number', 'sub_category', 'condition', 'count', 'uom'], 'string', 'max' => 100],
            [['remarks'], 'string', 'max' => 200],
            [['vehicle_number','upload_image'], 'safe'],
            [['pickup_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pickup::class, 'targetAttribute' => ['pickup_id' => 'pickup_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'details_packing_id' => 'Details Packing ID',
            'box_number' => 'Box Number',
            'sub_category' => 'Sub Category',
            'condition' => 'Condition',
            'count' => 'Count',
            'uom' => 'Uom',
            'remarks' => 'Remarks',
            'upload_image' => 'Upload Image',
            'pickup_id' => 'Pickup ID',
            'deleted' => 'Deleted',
            'vehicle_number' => 'Vehicle Number'
        ];
    }

    /**
     * Gets query for [[Pickup]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPickup()
    {
        return $this->hasOne(Pickup::class, ['pickup_id' => 'pickup_id']);
    }

    public function saveDetailsPackingList($entityId)
    {
        $details_packing_list = $_POST['details_packing_list']??[];
        if (!empty($details_packing_list)) {
            if (count($details_packing_list) > 0) {
                foreach ($details_packing_list as $sd) {
                    $sd['pickup_id'] = $entityId;
                    $sd_obj = new DetailsPackingList();
                    $sd_obj->attributes = $sd;
                    $sd_obj->validate();
                    $sd_obj->save(false);
                }
            }
        }
    }
}
