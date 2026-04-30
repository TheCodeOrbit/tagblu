<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rep_modelwise_clubbed_inventory".
 *
 * @property int $id
 * @property int $modelno
 * @property int $category
 * @property int $subcategory
 * @property float|null $qty
 * @property int $uom
 * @property float $purchase_value
 * @property int $location_code
 * @property int $location_floor
 */
class RepModelwiseClubbedInventory extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rep_modelwise_clubbed_inventory';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qty'], 'default', 'value' => null],
            [['modelno', 'category', 'subcategory', 'uom', 'purchase_value', 'location_code', 'location_floor'], 'required'],
            [['modelno', 'category', 'subcategory', 'uom', 'location_code', 'location_floor'], 'integer'],
            [['qty', 'purchase_value'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'modelno' => 'Modelno',
            'category' => 'Category',
            'subcategory' => 'Subcategory',
            'qty' => 'Qty',
            'uom' => 'Uom',
            'purchase_value' => 'Purchase Value',
            'location_code' => 'Location Code',
            'location_floor' => 'Location Floor',
        ];
    }

}
