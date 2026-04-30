<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "inventory_ageing".
 *
 * @property int $inventory_ageing_id
 * @property int $ownerid
 * @property int|null $grn_asset_detail_id
 * @property string|null $grn_no
 * @property string|null $grn_date
 * @property string|null $lot_no
 * @property string|null $account_name
 * @property string|null $product_name 
 
 * @property int|null $subcategory
 * @property int|null $qty
 * @property float|null $amount
 * @property int|null $uom
 * @property int $is_active 1-active,0 inactive
 
 * @property int $deleted
 */
class InventoryAgeing extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rep_inventory_ageing';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['grn_asset_detail_id', 'grn_no', 'grn_date', 'lot_no', 'account_name', 'product_name', 'subcategory', 'qty', 'amount', 'uom'], 'default', 'value' => null],
            [['is_active'], 'default', 'value' => 1],
            [['deleted'], 'default', 'value' => 0],
            [['ownerid', 'grn_asset_detail_id', 'subcategory', 'uom', 'is_active', 'deleted'], 'integer'],
            [['grn_date'], 'safe'],
            [['amount'], 'number'],
            [['grn_no', 'lot_no'], 'string', 'max' => 100],
            [['account_name', 'product_name'], 'string', 'max' => 200],
            [[ 'qty'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'inventory_ageing_id' => 'Inventory Ageing ID',
            'ownerid' => 'Ownerid',
            'grn_asset_detail_id' => 'Grn Asset Detail ID',
            'grn_no' => 'Grn No',
            'grn_date' => 'Grn Date',
            'lot_no' => 'Lot No',
            'account_name' => 'Account Name',
            'product_name' => 'Product Name',
            'subcategory' => 'Subcategory',
            'qty' => 'Qty',
            'amount' => 'Amount',
            'uom' => 'Uom',
            'is_active' => 'Is Active',
            'deleted' => 'Deleted',
        ];
    }

}
