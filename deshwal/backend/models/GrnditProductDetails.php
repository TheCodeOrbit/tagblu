<?php

namespace app\models;

use Yii;
use yii\web\BadRequestHttpException;

/**
 * This is the model class for table "grndit_product_details".
 *
 * @property int $grndit_prod_id
 * @property int $grndit_id
 * @property string|null $product_name
 * @property string|null $product_description
 * @property string|null $hsn_code
 * @property string|null $po_qty
 * @property float|null $basic_cost_price
 * @property int|null $received_qty
 * @property float|null $cgst_percentage
 * @property float|null $sgst_percentage
 * @property float|null $igst_percentage
 * @property float|null $total
 * @property int $deleted
 */
class GrnditProductDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'grndit_product_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['grndit_id'], 'required'],
            [['grndit_id', 'already_received', 'deleted'], 'integer'],
            [['product_description'], 'string'],
            [['basic_cost_price', 'cgst_percentage', 'sgst_percentage', 'igst_percentage', 'total'], 'number'],
            [[ 'product_name', 'hsn_code'], 'string', 'max' => 200],
            [['po_qty'], 'string', 'max' => 100],
            [['balance_qty','received_qty'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'grndit_prod_id' => 'Grndit Prod ID',
            'grndit_id' => 'Grndit ID',
            'product_name' => 'Product Name',
            'product_description' => 'Product Description',
            'hsn_code' => 'Hsn Code',
            'po_qty' => 'Po Qty',
            'basic_cost_price' => 'Basic Cost Price',
            'received_qty' => 'Received Qty',
            'balance_qty'=>'Balance Qty',
            'cgst_percentage' => 'Cgst Percentage',
            'sgst_percentage' => 'Sgst Percentage',
            'igst_percentage' => 'Igst Percentage',
            'total' => 'Total',
            'deleted' => 'Deleted',
            'already_received'=>'Already Received'
        ];
    }
    public function saveGrnditProductDetails($entityId)
    {
       if (empty($_REQUEST['grndit_product_details'])) {
            return false;
        } else {
            //delete old record from child table            
            $sql = "Delete from grndit_product_details where grndit_id = :grndit_id";
            Yii::$app->db->createCommand($sql)->bindValue(":grndit_id", $entityId)->execute();
        }

        $po_items = $_REQUEST['grndit_product_details'];
        if (count($po_items) > 0) {
            foreach ($po_items as $product_detail) {
                // echo $entityId;die;
                if (!is_array($product_detail)) {
                    continue; // skip invalid entries
                }

                $product_detail['purchaseorder_dit_id'] = $entityId;
                if (empty($product_detail['product_name']) || $product_detail['product_name'] == 0) {
                    // Set a flash message (if necessary)
                    Yii::$app->session->setFlash('error', 'Invalid request.Error: Invalid request. Product Name cannot be  blank.');

                    // Throw an exception with a custom message
                    throw new BadRequestHttpException('Invalid request.Error: Invalid request. Product Amount cannot be zero or blank');

                } else if (empty($product_detail['po_qty']) || $product_detail['po_qty'] == 0) {
                    // Set a flash message (if necessary)
                    Yii::$app->session->setFlash('error', 'Invalid request.Error: Invalid request. Product Quantity cannot be zero or blank.');

                    // Throw an exception with a custom message
                    throw new BadRequestHttpException('Invalid request.Error: Invalid request. Product Quantity cannot be zero or blank');

                } else if (empty($product_detail['basic_cost_price']) || $product_detail['basic_cost_price'] == 0) {
                    // Set a flash message (if necessary)
                    Yii::$app->session->setFlash('error', 'Invalid request.Error: Invalid request.Basic Price cannot be zero or blank.');

                    // Throw an exception with a custom message
                    throw new BadRequestHttpException('Invalid request.Error: Invalid request. Basic Price cannot be zero or blank');

                } 
                $product_detail_obj = new GrnditProductDetails;
                $product_detail['grndit_id'] =$entityId;
                $product_detail_obj->attributes = $product_detail;
                // print_r($product_detail_obj->attributes);die;
                $product_detail_obj->validate();
                $product_detail_obj->save(false);
            }
        }
    }
}
