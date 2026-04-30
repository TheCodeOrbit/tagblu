<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "openingstock_prod_dit".
 *
 * @property int $openingstock_prod_dit_id
 * @property int $creatorid
 * @property int $ownerid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property int|null $productid
 * @property int|null $location
 * @property string|null $stock_date
 * @property float|null $quantity
 * @property string|null $openingstockproddit_no
 * @property int $deleted
 */
class OpeningstockProdDit extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'openingstock_prod_dit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['productid', 'location', 'stock_date', 'quantity', 'openingstockproddit_no'], 'default', 'value' => null],
            [['deleted'], 'default', 'value' => 0],
            [['creatorid', 'ownerid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['creatorid', 'ownerid', 'modifiedby', 'productid', 'location', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime', 'stock_date'], 'safe'],
            [['quantity'], 'number'],
            [['openingstockproddit_no'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'openingstock_prod_dit_id' => 'Openingstock Prod Dit ID',
            'creatorid' => 'Creatorid',
            'ownerid' => 'Ownerid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'productid' => 'Productid',
            'location' => 'Location',
            'stock_date' => 'Stock Date',
            'quantity' => 'Quantity',
            'openingstockproddit_no' => 'Openingstockproddit No',
            'deleted' => 'Deleted',
        ];
    }

}
