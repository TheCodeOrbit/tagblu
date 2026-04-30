<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "opportunity_product_detail".
 *
 * @property int $opportunity_product_detail_id
 * @property int $opportunity_id
 * @property string|null $purchase_request_number
 * @property int|null $product_name
 * @property string|null $hsn_code
 * @property float|null $quantity
 * @property float|null $cost_price
 * @property float|null $margin_percentage
 * @property float|null $sales_price
 * @property string|null $cgst
 * @property string|null $sgst
 * @property string|null $igst
 * @property float|null $cgst_amount
 * @property float|null $sgst_amount
 * @property float|null $igst_amount
 * @property float|null $total_cost_tax_exclude
 * @property float|null $total_sale_tax_exclude
 * @property float|null $total_amt_tax_include
 * @property float|null $total_amount
 * @property float|null $gross_profit
 * @property string|null $add_price_validity
 * @property int|null $add_product_delivery_timeline
 * @property string|null $add_product_warranty
 * @property int|null $reject
 * @property string|null $remarks
 * @property int|null $master_category
 * @property int|null $sub_category
 * @property string|null $product_description
 * @property int $deleted
 *
 * @property Opportunity $opportunity
 */
class OpportunityProductDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'opportunity_product_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['opportunity_id'], 'required'],
            [['opportunity_id', 'product_name', 'add_product_delivery_timeline', 'reject', 'master_category', 'sub_category', 'deleted'], 'integer'],
            [['quantity', 'cost_price', 'margin_percentage', 'sales_price', 'cgst_amount', 'sgst_amount', 'igst_amount', 'total_cost_tax_exclude', 'total_sale_tax_exclude', 'total_amt_tax_include', 'total_amount', 'gross_profit'], 'number'],
            [['add_price_validity'], 'safe'],
            [['remarks', 'product_description'], 'string'],
            [['purchase_request_number', 'hsn_code', 'cgst', 'sgst', 'igst', 'add_product_warranty'], 'string', 'max' => 200],
            [['opportunity_id'], 'exist', 'skipOnError' => true, 'targetClass' => Opportunity::class, 'targetAttribute' => ['opportunity_id' => 'opportunity_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'opportunity_product_detail_id' => 'Opportunity Product Detail ID',
            'opportunity_id' => 'Opportunity ID',
            'purchase_request_number' => 'Purchase Request Number',
            'product_name' => 'Product Name',
            'hsn_code' => 'Hsn Code',
            'quantity' => 'Quantity',
            'cost_price' => 'Cost Price',
            'margin_percentage' => 'Margin Percentage',
            'sales_price' => 'Sales Price',
            'cgst' => 'Cgst',
            'sgst' => 'Sgst',
            'igst' => 'Igst',
            'cgst_amount' => 'Cgst Amount',
            'sgst_amount' => 'Sgst Amount',
            'igst_amount' => 'Igst Amount',
            'total_cost_tax_exclude' => 'Total Cost Tax Exclude',
            'total_sale_tax_exclude' => 'Total Sale Tax Exclude',
            'total_amt_tax_include' => 'Total Amt Tax Include',
            'total_amount' => 'Total Amount',
            'gross_profit' => 'Gross Profit',
            'add_price_validity' => 'Add Price Validity',
            'add_product_delivery_timeline' => 'Add Product Delivery Timeline',
            'add_product_warranty' => 'Add Product Warranty',
            'reject' => 'Reject',
            'remarks' => 'Remarks',
            'master_category' => 'Master Category',
            'sub_category' => 'Sub Category',
            'product_description' => 'Product Description',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * Gets query for [[Opportunity]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOpportunity()
    {
        return $this->hasOne(Opportunity::class, ['opportunity_id' => 'opportunity_id']);
    }
    public function saveOpportunityProductDetail($entityId)
    {

        $saveOpportunityProductDetail = $_POST['opportunity_product_detail']??[];
        // echo "<br>pickup vehicle<pre>";
        // print_r($_POST['pickup_vehicle_details']);die;
        if (!empty($saveOpportunityProductDetail)) {
            if (count($saveOpportunityProductDetail) > 0) {
                foreach ($saveOpportunityProductDetail as $product_detail) {
                    // echo $entityId;die;
                    if(is_array($product_detail))
                    {
                    $product_detail['opportunity_id'] = intval($entityId);
                    $product_detail_obj = new OpportunityProductDetail();
                    $product_detail_obj->attributes = $product_detail;
                    // print_r($product_detail_obj->attributes);die;
                    $product_detail_obj->validate();
                    $product_detail_obj->save(false);
                    // $modlog = new ModtrackerBasic();
                    // $modlog->auditlog($oldAttributes = '', $product_detail_obj, 'productdetail', $product_detail_obj->$product_costing_detail_id, 0, Yii::$app->user->id);
                    }
                }
            }
        }
    }
}
