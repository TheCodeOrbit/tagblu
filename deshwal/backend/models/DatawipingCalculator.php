<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "datawiping_calculator".
 *
 * @property int $datawiping_calculator_id
 * @property float $from_range
 * @property string|null $range
 * @property int|null $max_count
 * @property float|null $base_price
 * @property int|null $engineer_cost
 * @property int|null $eng_count
 * @property int|null $eng_cost
 * @property int|null $vendor_eng_require
 * @property int|null $vendor_eng_count
 * @property int|null $total
 * @property int|null $dongle_movement
 * @property int|null $expense
 * @property int|null $costing
 * @property int|null $profit
 * @property int|null $profit_percentage
 * @property float|null $unit_cost_price
 * @property int $datawiping_cal_parent_id
 * @property int $deleted
 */
class DatawipingCalculator extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'datawiping_calculator';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['datawiping_cal_parent_id'], 'required'],
            [['from_range', 'base_price', 'unit_cost_price','profit','profit_percentage',], 'number'],
            [['max_count', 'eng_count',  'vendor_eng_require', 'vendor_eng_count',  'dongle_movement',  'datawiping_cal_parent_id', 'deleted'], 'integer'],
            [['range'], 'string', 'max' => 100],
            [['costing','eng_cost', 'engineer_cost','expense','total',], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'datawiping_calculator_id' => 'Datawiping Calculator ID',
            'from_range' => 'From Range',
            'range' => 'Range',
            'max_count' => 'Max Count',
            'base_price' => 'Base Price',
            'engineer_cost' => 'Engineer Cost',
            'eng_count' => 'Eng Count',
            'eng_cost' => 'Eng Cost',
            'vendor_eng_require' => 'Vendor Eng Require',
            'vendor_eng_count' => 'Vendor Eng Count',
            'total' => 'Total',
            'dongle_movement' => 'Dongle Movement',
            'expense' => 'Expense',
            'costing' => 'Costing',
            'profit' => 'Profit',
            'profit_percentage' => 'Profit Percentage',
            'unit_cost_price' => 'Unit Cost Price',
            'datawiping_cal_parent_id' => 'Datawiping Cal Parent ID',
            'deleted' => 'Deleted',
        ];
    }

     public function saveDatawipingCalculator($entityId){
        //     print_r($entityId);
        //    die;
        $datawiping_calculator = $_REQUEST['datawiping_calculator'];
        // print_r($datawiping_calculator);
        // die;
        if (count($datawiping_calculator) > 0) {
            foreach ($datawiping_calculator as $datawipingcal) {
                $datawipingcal['datawiping_cal_parent_id'] = $entityId;
                $datawipingcal_obj = new DatawipingCalculator();
                $datawipingcal_obj->attributes = $datawipingcal;
                //  print_r($datawipingcal_obj->attributes);die;
                $datawipingcal_obj->validate();
                $datawipingcal_obj->save(false);
            }
        }

    }
}
