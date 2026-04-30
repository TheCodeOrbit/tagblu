<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "degaussing_calculator".
 *
 * @property int $degaussing_calculator_id
 * @property float $from_range
 * @property string|null $range
 * @property int|null $max_count
 * @property int|null $unit_cost_expense_max_count
 * @property float|null $base_price
 * @property float|null $min_price
 * @property int|null $engineer_cost
 * @property int|null $eng_count
 * @property int|null $eng_cost
 * @property int|null $vendor_eng_require
 * @property int|null $vendor_eng_count
 * @property int|null $total
 * @property int|null $machine_movement_charges
 * @property int|null $expense
 * @property int|null $costing
 * @property int|null $profit
 * @property int|null $profit_percentage
 * @property int|null $unit_sale
 * @property int $degaussing_cal_parent_id
 * @property int $deleted
 */
class DegaussingCalculator extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'degaussing_calculator';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['degaussing_cal_parent_id'], 'required'],
            [['from_range', 'base_price', 'min_price','profit', 'profit_percentage','unit_sale',], 'number'],
            [['max_count', 'unit_cost_expense_max_count', 'eng_count', 'vendor_eng_require', 'vendor_eng_count', 'degaussing_cal_parent_id', 'deleted'], 'integer'],
            [['range'], 'string', 'max' => 100],
            [['costing','eng_cost', 'engineer_cost','expense','machine_movement_charges', 'total',], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'degaussing_calculator_id' => 'Degaussing Calculator ID',
            'from_range' => 'From Range',
            'range' => 'Range',
            'max_count' => 'Max Count',
            'unit_cost_expense_max_count' => 'Unit Cost Expense Max Count',
            'base_price' => 'Base Price',
            'min_price' => 'Min Price',
            'engineer_cost' => 'Engineer Cost',
            'eng_count' => 'Eng Count',
            'eng_cost' => 'Eng Cost',
            'vendor_eng_require' => 'Vendor Eng Require',
            'vendor_eng_count' => 'Vendor Eng Count',
            'total' => 'Total',
            'machine_movement_charges' => 'Machine Movement Charges',
            'expense' => 'Expense',
            'costing' => 'Costing',
            'profit' => 'Profit',
            'profit_percentage' => 'Profit Percentage',
            'unit_sale' => 'Unit Sale',
            'degaussing_cal_parent_id' => 'Degaussing Cal Parent ID',
            'deleted' => 'Deleted',
        ];
    }

    public function saveDegaussingCalculator($entityId)
    {
        //    print_r($entityId);
        //    die;
        $degaussing_calculator = $_REQUEST['degaussing_calculator'];
        // print_r($degaussing_calculator);
        // die;
        if (count($degaussing_calculator) > 0) {
            foreach ($degaussing_calculator as $degaussingcal) {
                $degaussingcal['degaussing_cal_parent_id'] = $entityId;
                $degaussingcal_obj = new DegaussingCalculator();
                $degaussingcal_obj->attributes = $degaussingcal;
                // print_r($degaussingcal_obj->attributes);die;
                $degaussingcal_obj->validate();
                $degaussingcal_obj->save(false);
            }
        }
    }

      
}
