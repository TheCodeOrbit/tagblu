<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "clubbed_inventory".
 *
 * @property int $id
 * @property int $category
 * @property int $subcategory
 * @property int $qty
 * @property int $uom
 * @property int $purchase_value
 * @property int $location_code
 * @property int $location_floor
 */
class ClubbedInventory extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rep_clubbed_inventory';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category', 'subcategory', 'qty', 'uom', 'purchase_value', 'location_code', 'location_floor'], 'required'],
            [['category', 'subcategory', 'uom',  'location_code', 'location_floor'], 'integer'],
            [['purchase_value'],'safe'],
            [['qty',],'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
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
