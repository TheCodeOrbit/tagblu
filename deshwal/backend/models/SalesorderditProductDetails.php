<?php

namespace app\models;
use yii\web\BadRequestHttpException;

use Yii;

/**
 * This is the model class for table "salesorderdit_product_details".
 *
 * @property int $salesorderdit_product_details_id
 * @property int $salesorder_dit_id
 * @property int|null $product_name
 * @property string|null $hsn_code
 * @property int|null $qty
 * @property float|null $basic_price
 * @property float|null $cgst_per
 * @property float|null $sgst_per
 * @property float|null $igst_per
 * @property float|null $amount
 * @property string|null $customer_po_num
 * @property int|null $customer_payment_terms
 * @property string|null $customer_po_date
 * @property int $deleted
 * @property string|null $add_price_validity
 * @property int|null $add_product_delivery_timeline
 * @property SalesorderDit $salesorderDit
 */
class SalesorderditProductDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'salesorderdit_product_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['salesorder_dit_id'], 'required'],
            [['salesorder_dit_id', 'product_name', 'customer_payment_terms', 'add_product_delivery_timeline', 'deleted'], 'integer'],
            [['basic_price', 'cgst_per', 'sgst_per', 'igst_per', 'amount'], 'number'],
            [['customer_po_date','add_price_validity'], 'safe'],
            [['hsn_code'], 'string', 'max' => 100],
            [['customer_po_num','oem_part_number',], 'string', 'max' => 200],
            [['salesorder_dit_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalesorderDit::class, 'targetAttribute' => ['salesorder_dit_id' => 'salesorder_dit_id']],
            [['qty','remaining_qty', 'quotesdit_qty',], 'number'],
            [[ 'product_description', 'oem_part_number', 'remaining_qty', 'quotesdit_qty', ], 'default', 'value' => null],
            [['product_description'], 'string'],
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'salesorderdit_product_details_id' => 'Salesorderdit Product Details ID',
            'salesorder_dit_id' => 'Salesorder Dit ID',
            'product_name' => 'Product Name',
            'product_description' => 'Product Description',
            'oem_part_number' => 'Oem Part Number',
            'hsn_code' => 'Hsn Code',
            'qty' => 'Qty',
            'remaining_qty' => 'Remaining Qty',
            'quotesdit_qty' => 'Quotes Qty',
            'basic_price' => 'Basic Price',
            'cgst_per' => 'Cgst Per',
            'sgst_per' => 'Sgst Per',
            'igst_per' => 'Igst Per',
            'amount' => 'Amount',
            'customer_po_num' => 'Customer Po Num',
            'customer_payment_terms' => 'Customer Payment Terms',
            'customer_po_date' => 'Customer Po Date',
            'add_price_validity' => 'Add Price Validity',
            'add_product_delivery_timeline' => 'Add Product Delivery Timeline',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * Gets query for [[SalesorderDit]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSalesorderDit()
    {
        return $this->hasOne(SalesorderDit::class, ['salesorder_dit_id' => 'salesorder_dit_id']);
    }

    public function saveSalesorderditProductDetails($entityId)
    {
        if (empty($_REQUEST['salesorderdit_product_details'])) {
            return false;
        } else {
            //delete old record from child table            
            $sql = "Delete from salesorderdit_product_details where salesorder_dit_id = :salesorder_dit_id";
            Yii::$app->db->createCommand($sql)->bindValue(":salesorder_dit_id", $entityId)->execute();
        }

        $po_items = $_REQUEST['salesorderdit_product_details'];
        if (count($po_items) > 0) {
            foreach ($po_items as $product_detail) {
                // echo $entityId;die;
                if (!is_array($product_detail)) {
                    continue; // skip invalid entries
                }

                $product_detail['salesorder_dit_id'] = $entityId;
                if ($product_detail['amount'] === '') {
                    // Set a flash message (if necessary)
                    Yii::$app->session->setFlash('error', 'Invalid request.Error: Invalid request. Product Amount cannot be zero or blank.');

                    // Throw an exception with a custom message
                    throw new BadRequestHttpException('Invalid request.Error: Invalid request. Product Amount cannot be zero or blank');

                } else if (empty($product_detail['qty']) || $product_detail['qty'] == 0) {
                    // Set a flash message (if necessary)
                    Yii::$app->session->setFlash('error', 'Invalid request.Error: Invalid request. Product Quantity cannot be zero or blank.');

                    // Throw an exception with a custom message
                    throw new BadRequestHttpException('Invalid request.Error: Invalid request. Product Quantity cannot be zero or blank');

                } else if ($product_detail['basic_price']  === '') {
                    // Set a flash message (if necessary)
                    Yii::$app->session->setFlash('error', 'Invalid request.Error: Invalid request.Basic Price cannot be zero or blank.');

                    // Throw an exception with a custom message
                    throw new BadRequestHttpException('Invalid request.Error: Invalid request. Basic Price cannot be zero or blank');

                } else if (empty($product_detail['product_name']) || $product_detail['product_name'] == 0) {
                    // Set a flash message (if necessary)
                    Yii::$app->session->setFlash('error', 'Invalid request.Error: Invalid Product Name');

                    // Throw an exception with a custom message
                    throw new BadRequestHttpException('Invalid request.Error: Invalid Product Name.');

                }
                $product_detail_obj = new SalesorderditProductDetails;
                $product_detail_obj->attributes = $product_detail;
                // print_r($product_detail_obj->attributes);die;
                $product_detail_obj->validate();
                $product_detail_obj->save(false);
            }
        }
    }
}
