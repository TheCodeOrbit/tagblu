<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "inventory_log_details".
 *
 * @property int $logdetailsid
 * @property int $inventory_id
 * @property int|null $inventory_updatedby
 * @property string|null $inventory_update_at
 * @property int|null $segregation_updatedby
 * @property string|null $segregation_updated_at
 * @property int|null $tagging_updatedby
 * @property string|null $tagging_updated_at
 * @property int|null $sticker_removal_updatedby
 * @property string|null $sticker_removal_updated_at
 * @property int|null $cleaning_updatedby
 * @property string|null $cleaning_updated_by
 */
class InventoryLogDetails extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inventory_log_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inventory_updatedby', 'inventory_update_at', 'segregation_updatedby', 'segregation_updated_at', 'tagging_updatedby', 'tagging_updated_at', 'sticker_removal_updatedby', 'sticker_removal_updated_at', 'cleaning_updatedby', 'cleaning_updated_by','inventorystatus_updatedby','inventorystatus_updated_at'], 'default', 'value' => null],
            [['logdetailsid', 'inventory_id'], 'required'],
            [['logdetailsid', 'inventory_id', 'inventory_updatedby', 'segregation_updatedby', 'tagging_updatedby', 'sticker_removal_updatedby', 'cleaning_updatedby','inventorystatus_updatedby'], 'integer'],
            [['inventory_update_at', 'segregation_updated_at', 'tagging_updated_at', 'sticker_removal_updated_at', 'cleaning_updated_by','inventorystatus_updated_at'], 'safe'],
            [['logdetailsid'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'logdetailsid' => 'Logdetailsid',
            'inventory_id' => 'Inventory ID',
            'inventory_updatedby' => 'Inventory Updatedby',
            'inventory_update_at' => 'Inventory Update At',
            'segregation_updatedby' => 'Segregation Updatedby',
            'segregation_updated_at' => 'Segregation Updated At',
            'tagging_updatedby' => 'Tagging Updatedby',
            'tagging_updated_at' => 'Tagging Updated At',
            'sticker_removal_updatedby' => 'Sticker Removal Updatedby',
            'sticker_removal_updated_at' => 'Sticker Removal Updated At',
            'cleaning_updatedby' => 'Cleaning Updatedby',
            'cleaning_updated_by' => 'Cleaning Updated By',
        ];
    }

}
