<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_costing".
 *
 * @property int $product_costing_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string $product_costing_no
 * @property float|null $total_quoted_amt_inclusive_gst
 * @property float|null $total_quoted_amt_exclusive_gst
 * @property float|null $total_sp_amount_inclusive_gst
 * @property float|null $total_sp_amount_exclusive_gst
 * @property float|null $total_marketing_expenses
 * @property int $related_to
 * @property int $related_to_id
 * @property string|null $vendor_account_name
 * @property string|null $total_logistics_cost
 * @property float|null $total_expence_cost
 * @property string|null $vendor1
 * @property float|null $vendor1_pricing
 * @property string|null $vendor2
 * @property float|null $vendor2_pricing
 * @property float|null $direct_expenses_service_expens = Spare Cost
 * @property float|null $marketing_expenses = Repair Cost
 * @property float|null $margin
 * @property float|null $margin_percentage
 * @property string|null $round_off
 * @property float|null $tcs_percentage
 * @property float|null $tcs_amount
 * @property float|null $final_quoted_amount_incl_gst
 * @property int $deleted
 */
class ProductCosting extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_costing';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['total_quoted_amt_inclusive_gst', 'total_quoted_amt_exclusive_gst', 'total_sp_amount_inclusive_gst', 'total_sp_amount_exclusive_gst', 'total_marketing_expenses', 'vendor_account_name', 'total_logistics_cost', 'total_expence_cost', 'vendor1', 'vendor1_pricing', 'vendor2', 'vendor2_pricing', 'direct_expenses_service_expens', 'marketing_expenses', 'margin', 'margin_percentage', 'round_off', 'tcs_percentage', 'tcs_amount', 'final_quoted_amount_incl_gst'], 'default', 'value' => null],
            [['deleted'], 'default', 'value' => 0],
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime', 'product_costing_no', 'related_to', 'related_to_id'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'related_to', 'related_to_id', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime'], 'safe'],
            [['total_quoted_amt_inclusive_gst', 'total_quoted_amt_exclusive_gst', 'total_sp_amount_inclusive_gst', 'total_sp_amount_exclusive_gst', 'total_marketing_expenses', 'total_expence_cost', 'vendor1_pricing', 'vendor2_pricing', 'direct_expenses_service_expens', 'marketing_expenses', 'margin', 'margin_percentage', 'tcs_percentage', 'tcs_amount', 'final_quoted_amount_incl_gst'], 'number'],
            [['product_costing_no', 'vendor_account_name', 'total_logistics_cost'], 'string', 'max' => 100],
            [['vendor1', 'vendor2'], 'string', 'max' => 200],
            [['round_off'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'product_costing_id' => 'Product Costing ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'product_costing_no' => 'Product Costing No',
            'total_quoted_amt_inclusive_gst' => 'Total Quoted Amt Inclusive Gst',
            'total_quoted_amt_exclusive_gst' => 'Total Quoted Amt Exclusive Gst',
            'total_sp_amount_inclusive_gst' => 'Total Sp Amount Inclusive Gst',
            'total_sp_amount_exclusive_gst' => 'Total Sp Amount Exclusive Gst',
            'total_marketing_expenses' => 'Total Marketing Expenses',
            'related_to' => 'Related To',
            'related_to_id' => 'Related To ID',
            'vendor_account_name' => 'Vendor Account Name',
            'total_logistics_cost' => 'Total Logistics Cost',
            'total_expence_cost' => 'Total Expence Cost',
            'vendor1' => 'Vendor1',
            'vendor1_pricing' => 'Vendor1 Pricing',
            'vendor2' => 'Vendor2',
            'vendor2_pricing' => 'Vendor2 Pricing',
            'direct_expenses_service_expens' => 'Direct Expenses Service Expens',
            'marketing_expenses' => 'Marketing Expenses',
            'margin' => 'Margin',
            'margin_percentage' => 'Margin Percentage',
            'round_off' => 'Round Off',
            'tcs_percentage' => 'Tcs Percentage',
            'tcs_amount' => 'Tcs Amount',
            'final_quoted_amount_incl_gst' => 'Final Quoted Amount Incl Gst',
            'deleted' => 'Deleted',
        ];
    }

    public function getProductCostingDetail()
    {
        return $this->hasMany(ProductCostingDetail::class, ['product_costing_id' => 'product_costing_id']);
    }
}
