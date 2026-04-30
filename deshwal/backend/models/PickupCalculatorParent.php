<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pickup_calculator_parent".
 *
 * @property int $pickup_calculator_parentid
 * @property int $productid
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedbby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property int $deleted
 */
class PickupCalculatorParent extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pickup_calculator_parent';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['productid', 'ownerid', 'creatorid', 'modifiedby', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pickup_calculator_parentid' => 'Pickup Calculator Parentid',
            'productid' => 'Productid',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'deleted' => 'Deleted',
        ];
    }
}
