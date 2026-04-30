<?php

namespace app\models;
use yii\web\BadRequestHttpException;

use Yii;
 
/**
 * This is the model class for table "purchaseorderdit_product_details".
 *
 * @property int $product_details_id
 * @property int $purchaseorder_dit_id
 * @property int|null $product_name
 * @property string|null $product_description
 * @property string|null $hsn_code
 * @property int|null $qty
 * @property float|null $basic_cost_price
 * @property float|null $cgst
 * @property float|null $sgst
 * @property float|null $igst
 * @property float|null $product_total
 * @property int $deleted
 *
 * @property PurchaseOrderDit $purchaseorderDit
 */
class PurchaseorderditProductDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'purchaseorderdit_product_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['purchaseorder_dit_id'], 'required'],
            [['purchaseorder_dit_id', 'product_name', 'deleted'], 'integer'],
            [['product_description'], 'string'],
            [['basic_cost_price', 'cgst', 'sgst', 'igst','gst', 'product_total'], 'number'],
            [['hsn_code','reference_no'], 'string', 'max' => 200],
            [['oem_part_number'], 'string', 'max' => 500],
            [['purchaseorder_dit_id'], 'exist', 'skipOnError' => true, 'targetClass' => PurchaseOrderDit::class, 'targetAttribute' => ['purchaseorder_dit_id' => 'purchaseorder_dit_id']],
            [['qty','so_qty','remaining_qty'],'number'],
            [['reference_no'], 'default', 'value' => null],
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'product_details_id' => 'Product Details ID',
            'purchaseorder_dit_id' => 'Purchaseorder Dit ID',
            'product_name' => 'Product Name',
            'product_description' => 'Product Description',
            'hsn_code' => 'Hsn Code',
            'qty' => 'Qty',
            'basic_cost_price' => 'Basic Cost Price',
            'cgst' => 'Cgst',
            'sgst' => 'Sgst',
            'igst' => 'Igst',
            'product_total' => 'Product Total',
            'deleted' => 'Deleted',
            'so_qty'=>'SO Qty',
            'remaining_qty'=>'Remaining Qty',
            'oem_part_number'=>'OEM Part Number',
        ];
    }

    /**
     * Gets query for [[PurchaseorderDit]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPurchaseorderDit()
    {
        return $this->hasOne(PurchaseOrderDit::class, ['purchaseorder_dit_id' => 'purchaseorder_dit_id']);
    }

     public function savePurchaseorderditProductDetails($entityId)
    {
        if (empty($_REQUEST['purchaseorderdit_product_details'])) {
            return false;
        } else {
            //delete old record from child table            
            $sql = "Delete from purchaseorderdit_product_details where purchaseorder_dit_id = :purchaseorder_dit_id";
            Yii::$app->db->createCommand($sql)->bindValue(":purchaseorder_dit_id", $entityId)->execute();
        }

        $po_items = $_REQUEST['purchaseorderdit_product_details'];
        // echo "<pre>";print_r($po_items);die;
        if (count($po_items) > 0) {
            foreach ($po_items as $product_detail) {
                // echo $entityId;die;
                if (!is_array($product_detail)) {
                    continue; // skip invalid entries
                }

                $product_detail['purchaseorder_dit_id'] = $entityId;
                if (empty($product_detail['product_total']) || $product_detail['product_total'] == 0) {
                    // Set a flash message (if necessary)
                    Yii::$app->session->setFlash('error', 'Invalid request.Error: Invalid request. Product Total cannot be zero or blank.');

                    // Throw an exception with a custom message
                    throw new BadRequestHttpException('Invalid request.Error: Invalid request. Product Amount cannot be zero or blank');

                } else if (empty($product_detail['qty']) || $product_detail['qty'] == 0) {
                    // Set a flash message (if necessary)
                    Yii::$app->session->setFlash('error', 'Invalid request.Error: Invalid request. Product Quantity cannot be zero or blank.');

                    // Throw an exception with a custom message
                    throw new BadRequestHttpException('Invalid request.Error: Invalid request. Product Quantity cannot be zero or blank');

                } else if (empty($product_detail['basic_cost_price']) || $product_detail['basic_cost_price'] == 0) {
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
                $product_detail_obj = new PurchaseorderditProductDetails;
                // echo "<pre>";print_r($product_detail_obj->attributes);die;
                $product_detail_obj->attributes = $product_detail;
                // print_r($product_detail_obj->attributes);die;
                $product_detail_obj->validate();
                $product_detail_obj->save(false);
            }
        }
    }
}
