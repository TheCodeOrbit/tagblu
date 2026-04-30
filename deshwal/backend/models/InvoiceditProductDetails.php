<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "invoicedit_product_details".
 *
 * @property int $invoicedit_product_id
 * @property int $invoicedit_id
 * @property string|null $product_discription
 * @property string|null $product_qty
 * @property string|null $product_hsn
 * @property string|null $currency
 * @property float|null $unit_price
 * @property string|null $discount_age
 * @property float|null $total_amount
 * @property float|null $gst_rate
 * @property float|null $gst_amount
 * @property int $deleted
 *
 * @property Invoicedit $invoicedit
 */
class InvoiceditProductDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invoicedit_product_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['invoicedit_id'], 'required'],
            [['invoicedit_id','product_discription', 'deleted'], 'integer'],
            [['unit_price', 'total_amount', 'gst_rate', 'gst_amount'], 'number'],
            // [['product_discription'], 'string', 'max' => 1000],
            [['product_qty', 'product_hsn', 'currency', 'discount_age'], 'string', 'max' => 200],
            [['invoicedit_id'], 'exist', 'skipOnError' => true, 'targetClass' => Invoicedit::class, 'targetAttribute' => ['invoicedit_id' => 'invoicedit_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'invoicedit_product_id' => 'Invoicedit Product ID',
            'invoicedit_id' => 'Invoicedit ID',
            'product_discription' => 'Product Discription',
            'product_qty' => 'Product Qty',
            'product_hsn' => 'Product Hsn',
            'currency' => 'Currency',
            'unit_price' => 'Unit Price',
            'discount_age' => 'Discount Age',
            'total_amount' => 'Total Amount',
            'gst_rate' => 'Gst Rate',
            'gst_amount' => 'Gst Amount',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * Gets query for [[Invoicedit]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInvoicedit()
    {
        return $this->hasOne(Invoicedit::class, ['invoicedit_id' => 'invoicedit_id']);
    } 
    public function saveInvoiceditProductDetails($entityId)
    {
        $invoicedit_id = $entityId;
        if (empty($_REQUEST['invoicedit_product_details'])) {
            return false;
        } else {
            //delete old record from child table            
            $sql = "Delete from invoicedit_product_details where invoicedit_id = :invoicedit_id";
            Yii::$app->db->createCommand($sql)->bindValue(":invoicedit_id", $entityId)->execute();
        }
        $po_items=$_POST['invoicedit_product_details'];
       
        // echo "<pre>";print_r($po_items);
        if (count($po_items) > 0) {
            foreach ($po_items as $product_detail) {
                // echo $entityId;die;
                if (!is_array($product_detail)) {
                    continue; // skip invalid entries
                }
                // $product_detail['invoicedit_id'] = $invoicedit_id;
                // if (empty($product_detail['product_qty']) || $product_detail['product_qty'] == 0) {
                //     // Set a flash message (if necessary)
                //     Yii::$app->session->setFlash('error', 'Invalid request.Error: Invalid request. Product Quantity cannot be zero or blank.');

                //     // Throw an exception with a custom message
                //     throw new BadRequestHttpException('Invalid request.Error: Invalid request. Product Quantity cannot be zero or blank');

                // } 
			    $product_detail['invoicedit_id']=$entityId;  
                $product_detail_obj = new InvoiceditProductDetails();
                $product_detail_obj->attributes = $product_detail;
                $product_detail_obj->validate();
                $product_detail_obj->save(false);

                //stock out from DC when status is delivered
                // if($dc_status == 7 || $dc_status == 8)//7 - deliverd 8-return
                // {
                //     $StockCalculation =  new StockCalculation();
                //     $StockCalculation->getTodayStockSingleProduct($product_detail['poduct_description']);
                // }
            }
        }
    }
}
