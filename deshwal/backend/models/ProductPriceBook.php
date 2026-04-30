<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_price_book".
 *
 * @property int $productpricebook_id
 * @property int|null $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $productpricebook_no
 * @property int|null $contractid
 * @property int|null $product_name
 * @property int|null $conditions
 * @property float|null $base_amount_taxes_excluded
 * @property string|null $uom
 * @property int $deleted
 */
class ProductPriceBook extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_price_book';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'productpricebook_no', 'contractid', 'product_name', 'conditions', 'base_amount_taxes_excluded', 'uom'], 'default', 'value' => null],
            [['deleted'], 'default', 'value' => 0],
            [['ownerid', 'creatorid', 'modifiedby', 'contractid', 'product_name', 'conditions', 'deleted'], 'integer'],
            [['creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['createdtime', 'modifiedtime'], 'safe'],
            [['base_amount_taxes_excluded'], 'number'],
            [['productpricebook_no', 'uom'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'productpricebook_id' => 'Productpricebook ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'productpricebook_no' => 'Productpricebook No',
            'contractid' => 'Contractid',
            'product_name' => 'Product Name',
            'conditions' => 'Conditions',
            'base_amount_taxes_excluded' => 'Base Amount Taxes Excluded',
            'uom' => 'Uom',
            'deleted' => 'Deleted',
        ];
    }

}
