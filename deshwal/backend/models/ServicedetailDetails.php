<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "servicedetail_details".
 *
 * @property int $servicedetail_detail_id
 * @property string|null $service_type
 * @property string|null $category
 * @property string|null $sub_category
 * @property string|null $location
 * @property int|null $bill_to_location
 * @property int|null $service_to_location
 * @property int|null $bill_from_warehouse
 * @property int|null $ship_from_warehouse
 * @property string|null $hsn_code
 * @property float|null $std_cost_price
 * @property float|null $marketing_expenses
 * @property float|null $sale_price_exclusive_gst
 * @property float|null $margin
 * @property float|null $margin_percentage
 * @property int|null $qty_required
 * @property int|null $uom
 * @property float|null $cgst_on_saleprice
 * @property float|null $sgst
 * @property float|null $igst
 * @property float|null $cgst_amount
 * @property float|null $sgst_amount
 * @property float|null $igst_amount
 * @property float|null $total_inclusive_gst
 * @property float $total_exclusive_gst
 * @property float|null $unit_service_cost
 * @property float|null $total_service_cost
 * @property int $servicedetail_id
 * @property int $deleted
 *
 * @property Servicedetail $servicedetail
 */
class ServicedetailDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'servicedetail_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bill_to_location', 'service_to_location', 'bill_from_warehouse', 'ship_from_warehouse',  'uom', 'billable_type', 'servicedetail_id', 'deleted'], 'integer'],
            [['std_cost_price', 'marketing_expenses', 'sale_price_exclusive_gst', 'margin', 'margin_percentage', 'cgst_on_saleprice', 'sgst', 'igst', 'cgst_amount', 'sgst_amount', 'igst_amount', 'total_inclusive_gst', 'total_exclusive_gst', 'unit_service_cost', 'total_service_cost'], 'number'],
            [['servicedetail_id'], 'required'],
            [['service_type', 'category', 'sub_category', 'hsn_code'], 'string', 'max' => 200],
            [['location'], 'string', 'max' => 100],
            [['servicedetail_id'], 'exist', 'skipOnError' => true, 'targetClass' => Servicedetail::class, 'targetAttribute' => ['servicedetail_id' => 'servicedetail_id']],
            [['qty_required',], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'servicedetail_detail_id' => 'Servicedetail Detail ID',
            'service_type' => 'Service Type',
            'category' => 'Category',
            'sub_category' => 'Sub Category',
            'location' => 'Location',
            'bill_to_location' => 'Bill To Location',
            'service_to_location' => 'Service To Location',
            'bill_from_warehouse' => 'Bill From Warehouse',
            'ship_from_warehouse' => 'Ship From Warehouse',
            'hsn_code' => 'Hsn Code',
            'std_cost_price' => 'Std Cost Price',
            'marketing_expenses' => 'Marketing Expenses',
            'sale_price_exclusive_gst' => 'Sale Price Exclusive Gst',
            'margin' => 'Margin',
            'margin_percentage' => 'Margin Percentage',
            'qty_required' => 'Qty Required',
            'uom' => 'Uom',
            'billable_type' => 'Billable Type',
            'cgst_on_saleprice' => 'Cgst On Saleprice',
            'sgst' => 'Sgst',
            'igst' => 'Igst',
            'cgst_amount' => 'Cgst Amount',
            'sgst_amount' => 'Sgst Amount',
            'igst_amount' => 'Igst Amount',
            'total_inclusive_gst' => 'Total Inclusive Gst',
            'total_exclusive_gst' => 'Total Exclusive Gst',
            'unit_service_cost' => 'Unit Service Cost',
            'total_service_cost' => 'Total Service Cost',
            'servicedetail_id' => 'Servicedetail ID',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * Gets query for [[Servicedetail]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getServicedetail()
    {
        return $this->hasOne(Servicedetail::class, ['servicedetail_id' => 'servicedetail_id']);
    }
    public function saveServicedetailDetails($entityId)
    {
        //    print_r($entityId);
        //    die;
        $servicedetail_detail = $_REQUEST['servicedetail_details'];
        // print_r($servicedetail_detail);
        // die;
        if (count($servicedetail_detail) > 0) {
            foreach ($servicedetail_detail as $servicedetail) {
                $servicedetail['servicedetail_id'] = $entityId;
                $servicedetail_obj = new ServicedetailDetails();
                $servicedetail_obj->attributes = $servicedetail;
                // print_r($servicedetail_obj->attributes);die;
                $servicedetail_obj->validate();
                $servicedetail_obj->save(false);
            }
        }
    }
}
