<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_costing_detail".
 *
 * @property int $product_costing_detail_id
 * @property int $productid
 * @property int $product_costing_id
 * @property string|null $category
 * @property string|null $subcategory
 * @property string|null $vendor1
 * @property string|null $vendor2
 * @property float|null $vendor1_pricing
 * @property float|null $vendor2_pricing
 * @property string|null $model_no
 * @property string|null $make
 * @property string|null $pickup_location
 * @property string|null $billing_from_location
 * @property string|null $shipping_from_location
 * @property string|null $bill_to_warehouse
 * @property string|null $ship_to_warehouse
 * @property string|null $processor
 * @property string|null $generation
 * @property string|null $ram
 * @property string|null $hdd
 * @property int|null $physical_condition
 * @property int|null $asset_condition
 * @property string|null $screen
 * @property int|null $all_accessories
 * @property string|null $hsn_code
 * @property float|null $calculated_sp
 * @property float|null $sp_inclusive_gst
 * @property float|null $sp_exclusive_gst
 * @property float|null $direct_expenses_service_expens
 * @property float|null $marketing_expenses
 * @property float|null $quoted_price_inclusive_gst
 * @property float|null $quoted_price_gst_exclude
 * @property float|null $margin
 * @property float|null $margin_percentage
 * @property int|null $quantity_required
 * @property string|null $uom
 * @property int|null $no_gst
 * @property float|null $cgst
 * @property float|null $sgst
 * @property float|null $igst
 * @property float|null $cgst_amount
 * @property float|null $sgst_amount
 * @property float|null $igst_amount
 * @property float|null $total_sp_inclusive_gst
 * @property float|null $total_sp_exclusive_gst
 * @property float|null $total_quoted_price_inclusive_gst
 * @property float|null $total_quoted_price_exclusive_gst
 * @property string|null $logistics_cost
 * @property string|null $total_logistics_cost
 * @property int $deleted
 */
class ProductCostingDetail extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_costing_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category', 'subcategory', 'vendor1', 'vendor2', 'vendor1_pricing', 'vendor2_pricing', 'model_no', 'make', 'pickup_location', 'billing_from_location', 'shipping_from_location', 'bill_to_warehouse', 'ship_to_warehouse', 'processor', 'generation', 'ram', 'hdd', 'physical_condition', 'asset_condition', 'screen', 'all_accessories', 'hsn_code', 'calculated_sp', 'sp_inclusive_gst', 'sp_exclusive_gst', 'direct_expenses_service_expens', 'marketing_expenses', 'quoted_price_inclusive_gst', 'quoted_price_gst_exclude', 'margin', 'margin_percentage', 'quantity_required', 'uom', 'no_gst', 'cgst', 'sgst', 'igst', 'cgst_amount', 'sgst_amount', 'igst_amount', 'total_sp_inclusive_gst', 'total_sp_exclusive_gst', 'total_quoted_price_inclusive_gst', 'total_quoted_price_exclusive_gst', 'logistics_cost', 'total_logistics_cost'], 'default', 'value' => null],
            [['deleted'], 'default', 'value' => 0],
            [['productid', 'product_costing_id'], 'required'],
            [['productid', 'product_costing_id', 'physical_condition', 'asset_condition', 'all_accessories', 'quantity_required', 'no_gst', 'deleted'], 'integer'],
            [['vendor1_pricing', 'vendor2_pricing', 'calculated_sp', 'sp_inclusive_gst', 'sp_exclusive_gst', 'direct_expenses_service_expens', 'marketing_expenses', 'quoted_price_inclusive_gst', 'quoted_price_gst_exclude', 'margin', 'margin_percentage', 'cgst', 'sgst', 'igst', 'cgst_amount', 'sgst_amount', 'igst_amount', 'total_sp_inclusive_gst', 'total_sp_exclusive_gst', 'total_quoted_price_inclusive_gst', 'total_quoted_price_exclusive_gst'], 'number'],
            [['category', 'subcategory'], 'string', 'max' => 255],
            [['vendor1', 'vendor2', 'model_no', 'make', 'pickup_location', 'billing_from_location', 'shipping_from_location', 'bill_to_warehouse', 'ship_to_warehouse', 'processor', 'generation', 'ram', 'hdd', 'screen', 'hsn_code'], 'string', 'max' => 200],
            [['uom', 'logistics_cost', 'total_logistics_cost'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'product_costing_detail_id' => 'Product Costing Detail ID',
            'productid' => 'Productid',
            'product_costing_id' => 'Product Costing ID',
            'category' => 'Category',
            'subcategory' => 'Subcategory',
            'vendor1' => 'Vendor1',
            'vendor2' => 'Vendor2',
            'vendor1_pricing' => 'Vendor1 Pricing',
            'vendor2_pricing' => 'Vendor2 Pricing',
            'model_no' => 'Model No',
            'make' => 'Make',
            'pickup_location' => 'Pickup Location',
            'billing_from_location' => 'Billing From Location',
            'shipping_from_location' => 'Shipping From Location',
            'bill_to_warehouse' => 'Bill To Warehouse',
            'ship_to_warehouse' => 'Ship To Warehouse',
            'processor' => 'Processor',
            'generation' => 'Generation',
            'ram' => 'Ram',
            'hdd' => 'Hdd',
            'physical_condition' => 'Physical Condition',
            'asset_condition' => 'Asset Condition',
            'screen' => 'Screen',
            'all_accessories' => 'All Accessories',
            'hsn_code' => 'Hsn Code',
            'calculated_sp' => 'Calculated Sp',
            'sp_inclusive_gst' => 'Sp Inclusive Gst',
            'sp_exclusive_gst' => 'Sp Exclusive Gst',
            'direct_expenses_service_expens' => 'Direct Expenses Service Expens',
            'marketing_expenses' => 'Marketing Expenses',
            'quoted_price_inclusive_gst' => 'Quoted Price Inclusive Gst',
            'quoted_price_gst_exclude' => 'Quoted Price Gst Exclude',
            'margin' => 'Margin',
            'margin_percentage' => 'Margin Percentage',
            'quantity_required' => 'Quantity Required',
            'uom' => 'Uom',
            'no_gst' => 'No Gst',
            'cgst' => 'Cgst',
            'sgst' => 'Sgst',
            'igst' => 'Igst',
            'cgst_amount' => 'Cgst Amount',
            'sgst_amount' => 'Sgst Amount',
            'igst_amount' => 'Igst Amount',
            'total_sp_inclusive_gst' => 'Total Sp Inclusive Gst',
            'total_sp_exclusive_gst' => 'Total Sp Exclusive Gst',
            'total_quoted_price_inclusive_gst' => 'Total Quoted Price Inclusive Gst',
            'total_quoted_price_exclusive_gst' => 'Total Quoted Price Exclusive Gst',
            'logistics_cost' => 'Logistics Cost',
            'total_logistics_cost' => 'Total Logistics Cost',
            'deleted' => 'Deleted',
        ];
    }
public function saveProductCostingDetail($entityId)
    {
        $savePickupVehicleDetails = $_POST['product_costing_detail'];
        // echo "<br>pickup vehicle<pre>";
        // print_r($_POST['pickup_vehicle_details']);die;
        if (!empty($savePickupVehicleDetails)) {
            if (count($savePickupVehicleDetails) > 0) {
                foreach ($savePickupVehicleDetails as $product_detail) {
                    // echo $entityId;die;
                    if(is_array($product_detail))
                    {
                        // echo $entityId."<br><br><br>";
                    $product_detail['product_costing_id'] = intval($entityId);
                    $product_detail_obj = new ProductCostingDetail();
                    $product_detail_obj->attributes = $product_detail;
                    // print_r($product_detail_obj->attributes);die;
                    if($product_detail_obj->validate())
                    {
                        if( $product_detail_obj->save(false))
                        {

                        }
                        else{
                            print_r($product_detail_obj->getErrors());
    
                            die();
                        }
                    }
                    else{
                        print_r($product_detail_obj->getErrors());

                        die();
                    }
                   
                    // $modlog = new ModtrackerBasic();
                    // $modlog->auditlog($oldAttributes = '', $product_detail_obj, 'productdetail', $product_detail_obj->$product_costing_detail_id, 0, Yii::$app->user->id);
                    }
                }
            }
        }
    }
}
