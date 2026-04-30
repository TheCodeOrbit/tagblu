<?php

namespace app\models;

use Yii;
use yii\web\BadRequestHttpException;

/**
 * This is the model class for table "packinglistdit_product_details".
 *
 * @property int $product_details_id
 * @property int $packinglist_id
 * @property string|null $product_discription
 * @property string|null $product_qty
 * @property string|null $product_hsn
 * @property string|null $packing_type
 * @property string|null $special_instructions
 * @property int|null $deleted
 *
 * @property PackingListDit $packinglist
 */
class PackinglistditProductDetails extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'packinglistdit_product_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_discription', 'product_qty', 'product_hsn', 'packing_type', 'special_instructions'], 'default', 'value' => null],
            [['deleted'], 'default', 'value' => 0],
            [['packinglist_id'], 'required'],
            [['packinglist_id', 'deleted'], 'integer'],
            [['special_instructions'], 'string'],
            [['product_discription', 'product_qty', 'product_hsn'], 'string', 'max' => 200],
            [['packing_type'], 'string', 'max' => 100],
            [['packinglist_id'], 'exist', 'skipOnError' => true, 'targetClass' => PackingListDit::class, 'targetAttribute' => ['packinglist_id' => 'packinglist_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'product_details_id' => 'Product Details ID',
            'packinglist_id' => 'Packinglist ID',
            'product_discription' => 'Product Discription',
            'product_qty' => 'Product Qty',
            'product_hsn' => 'Product Hsn',
            'packing_type' => 'Packing Type',
            'special_instructions' => 'Special Instructions',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * Gets query for [[Packinglist]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPackinglist()
    {
        return $this->hasOne(PackingListDit::class, ['packinglist_id' => 'packinglist_id']);
    }

     public function savePackinglistditProductDetails($entityId)
    {
        $packinglist_id = $entityId;
        if (empty($_REQUEST['packinglistdit_product_details'])) {
            return false;
        } else {
            //delete old record from child table            
            $sql = "Delete from packinglistdit_product_details where packinglist_id = :packinglist_id";
            Yii::$app->db->createCommand($sql)->bindValue(":packinglist_id", $entityId)->execute();
        }

        $po_items = $_REQUEST['packinglistdit_product_details'];
        if (count($po_items) > 0) {
            foreach ($po_items as $product_detail) {
                // echo $entityId;die;
                if (!is_array($product_detail)) {
                    continue; // skip invalid entries
                }

                $product_detail['packinglist_id'] = $packinglist_id;
                if (empty($product_detail['product_qty']) || $product_detail['product_qty'] == 0) {
                    // Set a flash message (if necessary)
                    Yii::$app->session->setFlash('error', 'Invalid request.Error: Invalid request. Product Quantity cannot be zero or blank.');

                    // Throw an exception with a custom message
                    throw new BadRequestHttpException('Invalid request.Error: Invalid request. Product Quantity cannot be zero or blank');

                } 
                $product_detail_obj = new PackinglistditProductDetails();
                $product_detail_obj->attributes = $product_detail;
                $product_detail_obj->validate();
                $product_detail_obj->save(false);
            }
        }
    }

}
