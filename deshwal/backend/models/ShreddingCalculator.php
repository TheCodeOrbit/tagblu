<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "shredding_calculator".
 *
 * @property int $shredding_calculator_id
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
 * @property int $wooden_box_cost
 * @property int $box_count
 * @property int $wooden_box_charges
 * @property int|null $expense
 * @property int|null $costing
 * @property int|null $profit
 * @property int|null $profit_percentage
 * @property float|null $unit_cost_price
 * @property int $shredding_cal_parent_id
 * @property int $deleted
 */
class ShreddingCalculator extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shredding_calculator';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['from_range', 'max_count', 'eng_count', 'vendor_eng_require', 'vendor_eng_count',  'box_count',   'shredding_cal_parent_id', 'deleted'], 'integer'],
            [['base_price', 'unit_cost_price'], 'number'],
            [['shredding_cal_parent_id'], 'required'],
            [['range'], 'string', 'max' => 100],
            [['costing','eng_cost', 'engineer_cost','wooden_box_cost','wooden_box_charges',  'expense','machine_movement_charges','profit', 'profit_percentage','total',], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'shredding_calculator_id' => 'Shredding Calculator ID',
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
            'wooden_box_cost' => 'Wooden Box Cost',
            'box_count' => 'Box Count',
            'wooden_box_charges' => 'Wooden Box Charges',
            'expense' => 'Expense',
            'costing' => 'Costing',
            'profit' => 'Profit',
            'profit_percentage' => 'Profit Percentage',
            'unit_cost_price' => 'Unit Cost Price',
            'shredding_cal_parent_id' => 'Shredding Cal Parent ID',
            'deleted' => 'Deleted',
        ];
    }

    public function saveShreddingCalculator($entityId)
    {
        //    print_r($entityId);
        //    die;
        $shredding_calculator = $_REQUEST['shredding_calculator'];
        // print_r($shredding_calculator);
        // die;
        if (count($shredding_calculator) > 0) {
            foreach ($shredding_calculator as $shreddingcal) {
                $shreddingcal['shredding_cal_parent_id'] = $entityId;
                $shreddingcal_obj = new ShreddingCalculator();
                $shreddingcal_obj->attributes = $shreddingcal;
                // print_r($shreddingcal_obj->attributes);die;
                $shreddingcal_obj->validate();
                $shreddingcal_obj->save(false);
            }
        }
    }
}
