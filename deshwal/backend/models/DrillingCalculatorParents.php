<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "drilling_calculator_parents".
 *
 * @property int $drilling_cal_parent_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 */
class DrillingCalculatorParents extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'drilling_calculator_parents';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby'], 'integer'],
            [['createdtime', 'modifiedtime'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'drilling_cal_parent_id' => 'Drilling Cal Parent ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
        ];
    }

    public function getDrillingCalculator()
    {
        return $this->hasMany(DrillingCalculator::class, ['drilling_cal_parent_id' => 'drilling_cal_parent_id']);
    }

    


}
