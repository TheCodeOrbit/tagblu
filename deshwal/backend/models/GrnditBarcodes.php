<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "grndit_barcodes".
 *
 * @property int $grndit_barcodes_id
 * @property int $grndit_id
 * @property string|null $product_name
 * @property string|null $hsn_code
 * @property string|null $bar_code
 */
class GrnditBarcodes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'grndit_barcodes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['grndit_id'], 'required'],
            [['grndit_id'], 'integer'],
            [['product_name'], 'string', 'max' => 200],
            [['hsn_code', 'bar_code'], 'string', 'max' => 100],
            [['grndit_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'grndit_barcodes_id' => 'Grndit Barcodes ID',
            'grndit_id' => 'Grndit ID',
            'product_name' => 'Product Name',
            'hsn_code' => 'Hsn Code',
            'bar_code' => 'Bar Code',
        ];
    }

    public function saveGrnditBarcodes($entityId)
    {
       
            //delete old record from child table            
            $sql = "Delete from grndit_barcodes where grndit_id = :grndit_id";
            Yii::$app->db->createCommand($sql)->bindValue(":grndit_id", $entityId)->execute();
        
        //get po items product wise from grn products
        $sql = "SELECT * FROM grndit_product_details where grndit_id = :grndit_id";
        $po_items = Yii::$app->db->createCommand($sql)->bindValue(":grndit_id", $entityId)->queryAll();

        // print_r($po_items);die;
        if (count($po_items) > 0) {
            foreach ($po_items as $product_detail) {
                // echo $entityId;die;
                if (!is_array($product_detail)) {
                    continue; // skip invalid entries
                }
                $received_qty = (int) $product_detail['received_qty'];
                for($i=0;$i<$received_qty;$i++)
                {    
                $product_detail_obj = new GrnditBarcodes;
                $barcode['grndit_id'] =$entityId;
                $barcode['product_name'] =$product_detail['product_name'];
                $barcode['hsn_code'] =$product_detail['hsn_code'];
                $barcode['bar_code'] ='';
                $product_detail_obj->attributes = $barcode;
                // print_r($product_detail_obj->attributes);die;
                $product_detail_obj->validate();
                $product_detail_obj->save(false);
                }
            }
        }
    }
}
