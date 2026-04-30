<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pickup_calculator".
 *
 * @property int $pickup_calculator_id
 * @property int $pickup_calculator_parentid
 * @property int $productid
 * @property string|null $product_name
 * @property int|null $from_range
 * @property string|null $range
 * @property int|null $base
 * @property int|null $bubble_roll_price
 * @property int|null $bubble_roll_count
 * @property int|null $total_price
 * @property int|null $shrink_wrap_price
 * @property int|null $shrink_wrap_count
 * @property int|null $total_proce
 * @property int|null $box_price
 * @property float|null $box_count
 * @property int|null $box_prices
 * @property int|null $tape_price
 * @property int|null $tape_qty
 * @property int|null $tape_cost
 * @property int|null $labour_count
 * @property int|null $labour_cost
 * @property int|null $labour_cost1
 * @property int|null $eng_count
 * @property int|null $price
 * @property int|null $eng_cost
 * @property int|null $weight
 * @property int|null $price1
 * @property int|null $travel_cost
 * @property int|null $base_price
 * @property float|null $insurance
 * @property int|null $insurance_cost
 * @property int|null $total
 * @property int|null $average
 * @property int $deleted
 */
class PickupCalculator extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pickup_calculator';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pickup_calculator_parentid', 'productid'], 'required'],
            [['pickup_calculator_parentid', 'productid', 'from_range', 'base',  'bubble_roll_count', 'shrink_wrap_count',  'labour_count',  'eng_count',   'weight',   'average', 'deleted'], 'integer'],
            [['box_count', 'insurance'], 'number'],
            [['product_name', 'range'], 'string', 'max' => 100],
            [['tape_qty', 'total_price', 'shrink_wrap_price','bubble_roll_price','box_price', 'box_prices', 'tape_price','tape_cost','labour_cost', 'labour_cost1', 'price','price1','total', 'base_price', 'insurance_cost', 'eng_cost','travel_cost','total_proce',],'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pickup_calculator_id' => 'Pickup Calculator ID',
            'pickup_calculator_parentid' => 'Pickup Calculator Parentid',
            'productid' => 'Productid',
            'product_name' => 'Product Name',
            'from_range' => 'From Range',
            'range' => 'Range',
            'base' => 'Base',
            'bubble_roll_price' => 'Bubble Roll Price',
            'bubble_roll_count' => 'Bubble Roll Count',
            'total_price' => 'Total Price',
            'shrink_wrap_price' => 'Shrink Wrap Price',
            'shrink_wrap_count' => 'Shrink Wrap Count',
            'total_proce' => 'Total Proce',
            'box_price' => 'Box Price',
            'box_count' => 'Box Count',
            'box_prices' => 'Box Prices',
            'tape_price' => 'Tape Price',
            'tape_qty' => 'Tape Qty',
            'tape_cost' => 'Tape Cost',
            'labour_count' => 'Labour Count',
            'labour_cost' => 'Labour Cost',
            'labour_cost1' => 'Labour Cost1',
            'eng_count' => 'Eng Count',
            'price' => 'Price',
            'eng_cost' => 'Eng Cost',
            'weight' => 'Weight',
            'price1' => 'Price1',
            'travel_cost' => 'Travel Cost',
            'base_price' => 'Base Price',
            'insurance' => 'Insurance',
            'insurance_cost' => 'Insurance Cost',
            'total' => 'Total',
            'average' => 'Average',
            'deleted' => 'Deleted',
        ];
    }

    public function savePickupCalculator($entityId)
    {
        // print_r($entityId);
        // die;

        $pickup_product_id = PickupCalculatorParent::find()->select('productid')->where(['pickup_calculator_parentid' => $entityId])->one();
        $product_name = Products::find()->select('product_name')->where(['products_id' => $pickup_product_id['productid']])->one();
        $pickup_calculator = $_REQUEST['pickup_calculator'];
        // print_r($pickup_calculator);
        // die;

        if (count($pickup_calculator) > 0) {
            foreach ($pickup_calculator as $pickupcal) {
                $pickupcal['pickup_calculator_parentid'] = $entityId;

                // Add product name to array
                $pickupcal['productid'] = $pickup_product_id['productid'];
                $pickupcal['product_name'] = $product_name['product_name'];

                $pickupcal_obj = new PickupCalculator();
                $pickupcal_obj->attributes = $pickupcal;

                $pickupcal_obj->validate();
                $pickupcal_obj->save(false);
            }
        }
    }
}
