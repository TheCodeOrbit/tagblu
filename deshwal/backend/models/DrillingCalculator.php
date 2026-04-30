<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "drilling_calculator".
 *
 * @property int $drilling_calculator_id
 * @property int|null $from_range
 * @property string|null $range
 * @property int|null $max_count
 * @property float|null $base_price
 * @property int|null $engineer_cost
 * @property int|null $eng_count
 * @property int|null $eng_cost
 * @property int|null $vendor_eng_require
 * @property int|null $vendor_eng_count
 * @property int|null $total
 * @property int|null $machine_movement_charges
 * @property int $bit_price
 * @property int $bit_count
 * @property int $bit_costing
 * @property int|null $expense
 * @property int|null $costing
 * @property int|null $profit
 * @property int|null $profit_percentage
 * @property float|null $unit_cost_price
 * @property int $drilling_cal_parent_id
 * @property int $deleted
 */
class DrillingCalculator extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'drilling_calculator';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['from_range', 'max_count',  'eng_count','vendor_eng_require', 'vendor_eng_count',   'bit_count',   'drilling_cal_parent_id', 'deleted'], 'integer'],
            [['base_price', 'unit_cost_price'], 'number'],
            [['drilling_cal_parent_id'], 'required'],
            [['range'], 'string', 'max' => 100],            
            [['costing','eng_cost', 'engineer_cost', 'bit_costing','bit_price','expense', 'machine_movement_charges', 'profit','profit_percentage','total',], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'drilling_calculator_id' => 'Drilling Calculator ID',
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
            'machine_movement_charges' => 'Machine Movement Charges',
            'bit_price' => 'Bit Price',
            'bit_count' => 'Bit Count',
            'bit_costing' => 'Bit Costing',
            'expense' => 'Expense',
            'costing' => 'Costing',
            'profit' => 'Profit',
            'profit_percentage' => 'Profit Percentage',
            'unit_cost_price' => 'Unit Cost Price',
            'drilling_cal_parent_id' => 'Drilling Cal Parent ID',
            'deleted' => 'Deleted',
        ];
    }

    public function getDrillingCalculatorParents()
    {
        return $this->hasOne(DrillingCalculatorParents::class, ['drilling_cal_parent_id ' => 'drilling_cal_parent_id']);
    }

    public function saveDrillingCalculator($entityId){
         //    print_r($entityId);
        //    die;
        $drilling_calculator = $_REQUEST['drilling_calculator'];
        // print_r($drilling_calculator);
        // die;
        if (count($drilling_calculator) > 0) {
            foreach ($drilling_calculator as $drillingcal) {
                $drillingcal['drilling_cal_parent_id'] = $entityId;
                $drillingcal_obj = new DrillingCalculator();
                $drillingcal_obj->attributes = $drillingcal;
                // print_r($drillingcal_obj->attributes);die;
                $drillingcal_obj->validate();
                $drillingcal_obj->save(false);
            }
        }

    }
}
