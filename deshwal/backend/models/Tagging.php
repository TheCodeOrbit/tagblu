<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tagging".
 *
 * @property int $tagging_id
 * @property string $tagging_no
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property int $deleted
 * @property string|null $grn_no
 * @property string|null $grn_date
 * @property string|null $lot_no
 * @property string|null $pickup_id
 */
class Tagging extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tagging';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['grn_no', 'grn_date', 'lot_no', 'pickup_id'], 'default', 'value' => null],
            [['deleted'], 'default', 'value' => 0],
            // 'tagging_no',
            [[ 'ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime', 'grn_date'], 'safe'],
            // [['tagging_no'], 'string', 'max' => 100],
            [['grn_no', 'lot_no', 'pickup_id'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tagging_id' => 'Tagging ID',
            // 'tagging_no' => 'Tagging No',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'deleted' => 'Deleted',
            'grn_no' => 'Grn No',
            'grn_date' => 'Grn Date',
            'lot_no' => 'Lot No',
            'pickup_id' => 'Pickup ID',
        ];
    }

}
