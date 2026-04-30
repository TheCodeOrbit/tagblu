<?php

namespace app\models;

use Yii;
use yii\web\BadRequestHttpException;

/**
 * This is the model class for table "focdit_product_details".
 *
 * @property int $focdit_product_id
 * @property int $focdit_id
 * @property string|null $product_name
 * @property string|null $product_discription
 * @property string|null $product_hsn
 * @property string|null $product_qty
 * @property float|null $unit_price
 * @property float|null $base_price
 * @property float|null $gst_rate
 * @property float|null $gst_amount
 * @property float|null $total_amount
 * @property int $deleted
 *
 * @property FocDit $focdit
 */
class FocditProductDetails extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'focdit_product_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_name', 'product_discription', 'product_hsn', 'product_qty', 'unit_price', 'base_price', 'gst_rate', 'gst_amount', 'total_amount'], 'default', 'value' => null],
            [['focdit_id', 'deleted'], 'required'],
            [['focdit_id', 'deleted'], 'integer'],
            [['unit_price', 'base_price', 'gst_rate', 'gst_amount', 'total_amount'], 'number'],
            [['product_name', 'product_discription', 'product_hsn', 'product_qty'], 'string', 'max' => 200],
            [['focdit_id'], 'exist', 'skipOnError' => true, 'targetClass' => FocDit::class, 'targetAttribute' => ['focdit_id' => 'focdit_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'focdit_product_id' => 'Focdit Product ID',
            'focdit_id' => 'Focdit ID',
            'product_name' => 'Product Name',
            'product_discription' => 'Product Discription',
            'product_hsn' => 'Product Hsn',
            'product_qty' => 'Product Qty',
            'unit_price' => 'Unit Price',
            'base_price' => 'Base Price',
            'gst_rate' => 'Gst Rate',
            'gst_amount' => 'Gst Amount',
            'total_amount' => 'Total Amount',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * Gets query for [[Focdit]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFocdit()
    {
        return $this->hasOne(FocDit::class, ['focdit_id' => 'focdit_id']);
    }

    public function saveFocditProductDetails($entityId)
    {
        $focdit_id = $entityId;
        if (empty($_REQUEST['focdit_product_details'])) {
            return false;
        } else {
            //delete old record from child table            
            $sql = "Delete from focdit_product_details where focdit_id = :focdit_id";
            Yii::$app->db->createCommand($sql)->bindValue(":focdit_id", $entityId)->execute();
        }

        $po_items = $_REQUEST['focdit_product_details'];
        if (count($po_items) > 0) {
            foreach ($po_items as $product_detail) {
                // echo $entityId;die;
                if (!is_array($product_detail)) {
                    continue; // skip invalid entries
                }

                $product_detail['focdit_id'] = $focdit_id;
                if (empty($product_detail['product_qty']) || $product_detail['product_qty'] == 0) {
                    // Set a flash message (if necessary)
                    Yii::$app->session->setFlash('error', 'Invalid request.Error: Invalid request. Product Quantity cannot be zero or blank.');

                    // Throw an exception with a custom message
                    throw new BadRequestHttpException('Invalid request.Error: Invalid request. Product Quantity cannot be zero or blank');

                } 
                $product_detail_obj = new FocditProductDetails();
                $product_detail_obj->attributes = $product_detail;
                $product_detail_obj->validate();
                $product_detail_obj->save(false);
            }
        }
    }
}
