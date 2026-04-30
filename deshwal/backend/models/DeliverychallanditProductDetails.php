<?php

namespace app\models;

use Yii;
use yii\web\BadRequestHttpException;

/**
 * This is the model class for table "deliverychallandit_product_details".
 *
 * @property int $deliverychallan_product_details_id
 * @property int|null $deliverychallan_id
 * @property string|null $poduct_description
 * @property float|null $product_qty
 * @property string|null $product_hsn
 * @property float|null $unit_price
 * @property float|null $total_amount
 * @property float|null $gst_rate
 * @property float|null $gst_amount
 * @property float|null $invoice_sub_total
 * @property int $deleted
 *
 * @property DeliveryChallandit $deliverychallan
 */
class DeliverychallanditProductDetails extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'deliverychallandit_product_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['deliverychallan_id', 'poduct_description', 'product_qty', 'product_hsn', 'unit_price', 'total_amount', 'gst_rate', 'gst_amount', 'invoice_sub_total'], 'default', 'value' => null],
            [['deleted'], 'default', 'value' => 0],
            [['deliverychallan_id', 'deleted'], 'integer'],
            [['product_qty', 'unit_price', 'total_amount', 'gst_rate', 'gst_amount', 'invoice_sub_total'], 'number'],
            [['poduct_description'], 'string', 'max' => 1000],
            [['product_hsn'], 'string', 'max' => 255],
            [['deliverychallan_id'], 'exist', 'skipOnError' => true, 'targetClass' => DeliveryChallandit::class, 'targetAttribute' => ['deliverychallan_id' => 'deliverychallan_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'deliverychallan_product_details_id' => 'Deliverychallan Product Details ID',
            'deliverychallan_id' => 'Deliverychallan ID',
            'poduct_description' => 'Poduct Description',
            'product_qty' => 'Product Qty',
            'product_hsn' => 'Product Hsn',
            'unit_price' => 'Unit Price',
            'total_amount' => 'Total Amount',
            'gst_rate' => 'Gst Rate',
            'gst_amount' => 'Gst Amount',
            'invoice_sub_total' => 'Invoice Sub Total',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * Gets query for [[Deliverychallan]].
     *
     * @return \yii\db\ActiveQuery
     */
     public function getDeliverychallan()
    {
        return $this->hasOne(DeliveryChallandit::class, ['deliverychallan_id' => 'deliverychallan_id']);
    }

        public function saveDeliverychallanditProductDetails($entityId)
    {
        $deliverychallan_id = $entityId;
        if (empty($_REQUEST['deliverychallandit_product_details'])) {
            return false;
        } else {
            //delete old record from child table            
            $sql = "Delete from deliverychallandit_product_details where deliverychallan_id = :deliverychallan_id";
            Yii::$app->db->createCommand($sql)->bindValue(":deliverychallan_id", $entityId)->execute();
        }
        $dc_status = $_REQUEST['delivery_challandit']['status'];
        $po_items = $_REQUEST['deliverychallandit_product_details'];
        // echo "<pre>";print_r($po_items);
        if (count($po_items) > 0) {
            foreach ($po_items as $product_detail) {
                // echo $entityId;die;
                if (!is_array($product_detail)) {
                    continue; // skip invalid entries
                }
                $product_detail['deliverychallan_id'] = $deliverychallan_id;
                if (empty($product_detail['product_qty']) || $product_detail['product_qty'] == 0) {
                    // Set a flash message (if necessary)
                    Yii::$app->session->setFlash('error', 'Invalid request.Error: Invalid request. Product Quantity cannot be zero or blank.');

                    // Throw an exception with a custom message
                    throw new BadRequestHttpException('Invalid request.Error: Invalid request. Product Quantity cannot be zero or blank');

                } 
                $product_detail_obj = new DeliverychallanditProductDetails();
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
