<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "openingstock_prod".
 *
 * @property int $openingstock_prod_id
 * @property int $creatorid
 * @property int $ownerid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $openingstock_no
 * @property int|null $productid
 * @property int|null $location
 * @property string|null $stock_date
 * @property float|null $quantity
 * @property int $deleted
 */
class OpeningstockProd extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'openingstock_prod';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['openingstock_no', 'productid', 'location', 'stock_date', 'quantity'], 'default', 'value' => null],
            [['deleted'], 'default', 'value' => 0],
            [['creatorid', 'ownerid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['creatorid', 'ownerid', 'modifiedby', 'productid', 'location', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime', 'stock_date'], 'safe'],
            [['quantity'], 'number'],
            [['openingstock_no'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'openingstock_prod_id' => 'Openingstock Prod ID',
            'creatorid' => 'Creatorid',
            'ownerid' => 'Ownerid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'openingstock_no' => 'Openingstock No',
            'productid' => 'Productid',
            'location' => 'Location',
            'stock_date' => 'Stock Date',
            'quantity' => 'Quantity',
            'deleted' => 'Deleted',
        ];
    }

}
