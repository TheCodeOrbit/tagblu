<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "segregation".
 *
 * @property int $segregation_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property int $deleted
 * @property string|null $grn_no
 * @property string|null $grn_date
 * @property string|null $total_quantity
 * @property string|null $lot_no
 * @property string|null $pickup_id
 * @property string|null $matched_quantity
 */
class Segregation extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'segregation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['grn_no', 'grn_date', 'total_quantity','total_weight','save_as_draft', 'lot_no', 'pickup_id', 'matched_quantity','grn_asset_detail_id'], 'default', 'value' => null],
            [['deleted'], 'default', 'value' => 0],
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'deleted','grn_asset_detail_id'], 'integer'],
            [['createdtime', 'modifiedtime', 'grn_date'], 'safe'],
            [['grn_no', 'lot_no', 'pickup_id'], 'string', 'max' => 200],
            [['total_quantity', 'matched_quantity'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'segregation_id' => 'Segregation ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'deleted' => 'Deleted',
            'grn_no' => 'Grn No',
            'grn_date' => 'Grn Date',
            'total_quantity' => 'Total Quantity',
            'lot_no' => 'Lot No',
            'pickup_id' => 'Pickup ID',
            'matched_quantity' => 'Matched Quantity',
            'total_weight' => 'Total Weight',
        ];
    }

}
