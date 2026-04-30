<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "grn_dit".
 *
 * @property int $grndit_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $grndit_no
 * @property string|null $grn_date
 * @property string|null $status
 * @property string|null $purchase_order_number
 * @property string|null $vendor_name
 * @property string|null $invoice_number
 * @property string|null $invoice_date
 * @property string|null $freight_charges
 * @property string|null $vendor_location
 * @property string|null $vendor_address
 * @property string|null $vendor_gst_number
 * @property string|null $vendor_state_code
 * @property string|null $source_of_supply
 * @property string|null $entity_name
 * @property string|null $entity_location
 * @property string|null $entity_address
 * @property string|null $entity_gst_number
 * @property string|null $entity_state_code
 * @property string|null $destination_of_supply
 * @property int|null $delivery_entity_name
 * @property string|null $delivery_location
 * @property string|null $delivery_address
 * @property string|null $delivery_gst_number
 * @property string|null $delivery_state_code
 * @property string|null $delivery_destination_supply
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
 * @property float|null $balance_qty
 * @property int $invoice
 * @property int $e_way_bill
 * @property int $deleted
 */
class GrnDit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'grn_dit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'delivery_entity_name',  'invoice', 'e_way_bill', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime', 'grn_date', 'invoice_date'], 'safe'],
            [['basic_cost_price', 'cgst_percentage', 'sgst_percentage', 'igst_percentage', 'total', 'balance_qty'], 'number'],
            [['grndit_no', 'status', 'purchase_order_number', 'vendor_name', 'invoice_number', 'freight_charges', 'vendor_location', 'vendor_gst_number', 'vendor_state_code', 'source_of_supply', 'entity_name', 'entity_location', 'entity_gst_number', 'destination_of_supply', 'delivery_location', 'delivery_gst_number', 'delivery_destination_supply', 'product_name', 'product_description', 'hsn_code'], 'string', 'max' => 200],
            [['vendor_address', 'entity_address', 'delivery_address'], 'string', 'max' => 3000],
            [['entity_state_code', 'delivery_state_code', 'po_qty'], 'string', 'max' => 100],
            [['received_qty'], 'number'],
            // added for handling blank values saving in by ptpatel on date 24-01-2026
            [['vendor_name'], 'trim'],
            [['vendor_name'], 'required', 'message' => 'Vendor Name cannot be blank.'],
            [['vendor_name'], 'integer', 'message' => 'Vandor Name must be a number.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'grndit_id' => 'Grndit ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'grndit_no' => 'Grndit No',
            'grn_date' => 'Grn Date',
            'status' => 'Status',
            'purchase_order_number' => 'Purchase Order Number',
            'vendor_name' => 'Vendor Name',
            'invoice_number' => 'Invoice Number',
            'invoice_date' => 'Invoice Date',
            'freight_charges' => 'Freight Charges',
            'vendor_location' => 'Vendor Location',
            'vendor_address' => 'Vendor Address',
            'vendor_gst_number' => 'Vendor Gst Number',
            'vendor_state_code' => 'Vendor State Code',
            'source_of_supply' => 'Source Of Supply',
            'entity_name' => 'Entity Name',
            'entity_location' => 'Entity Location',
            'entity_address' => 'Entity Address',
            'entity_gst_number' => 'Entity Gst Number',
            'entity_state_code' => 'Entity State Code',
            'destination_of_supply' => 'Destination Of Supply',
            'delivery_entity_name' => 'Delivery Entity Name',
            'delivery_location' => 'Delivery Location',
            'delivery_address' => 'Delivery Address',
            'delivery_gst_number' => 'Delivery Gst Number',
            'delivery_state_code' => 'Delivery State Code',
            'delivery_destination_supply' => 'Delivery Destination Supply',
            'product_name' => 'Product Name',
            'product_description' => 'Product Description',
            'hsn_code' => 'Hsn Code',
            'po_qty' => 'Po Qty',
            'basic_cost_price' => 'Basic Cost Price',
            'received_qty' => 'Received Qty',
            'cgst_percentage' => 'Cgst Percentage',
            'sgst_percentage' => 'Sgst Percentage',
            'igst_percentage' => 'Igst Percentage',
            'total' => 'Total',
            'balance_qty' => 'Balance Qty',
            'invoice' => 'Invoice',
            'e_way_bill' => 'E Way Bill',
            'deleted' => 'Deleted',
        ];
    }
    public function grnStageCalc($current_stage)
    {

        $all_zero_or_blank = true;
        $all_match = true;

        // foreach ($_POST['grndit_product_details'] as $product) {
        //     $po_qty = isset($product['po_qty']) ? (int)$product['po_qty'] : 0;
        //     $received_qty = isset($product['received_qty']) && $product['received_qty'] !== '' ? (int)$product['received_qty'] : 0;

        //     if ($received_qty > 0) {
        //         $all_zero_or_blank = false;
        //     }

        //     if ($po_qty !== $received_qty) {
        //         $all_match = false;
        //     }
        // }

        // if ($all_zero_or_blank) {
        //     $status = '3';//draft
        // } elseif ($all_match) {
        //     $status = '2';//fully received
        // } else {
        //     $status = '1';//Partially Received
        // }
        $status = '4';//barcode pending




        return $status;
    }


    public function Savetoinventory($Recordid)
    {
        $connection = Yii::$app->db;
        //now check if all barcodes updated then add to inventory
        $query = (new \yii\db\Query())
            ->from("grndit_barcodes")
            ->where(['grndit_id' => $Recordid])
            ->andWhere([
                'or',
                ['bar_code' => ''],
                ['bar_code' => null],
            ]);
        $recordExists = $query->exists();
        if (!$recordExists) {


            //delete old records for this grnid
            $connection->createCommand("DELETE from inventory_dit where grndit_id = :grndit_id")
                ->bindValue(":grndit_id", $Recordid)->execute();
            //add to inventory
            // $query = (new \yii\db\Query())
            //     ->from($blocks_table)
            //     ->where(['grndit_id' => $Recordid])
            //     ->all();
            $query = (new \yii\db\Query())
                ->select([
                    'grndit_barcodes.product_name',
                    'grndit_barcodes.hsn_code',
                    'grndit_barcodes.bar_code',
                    'grn_dit.purchase_order_number',
                    'grn_dit.delivery_entity_name'
                ])
                ->from('grndit_barcodes')
                ->innerJoin('grn_dit', 'grndit_barcodes.grndit_id = grn_dit.grndit_id')
                ->where(['grndit_barcodes.grndit_id' => $Recordid])
                ->all();

            // print_r($query);

            foreach ($query as $row) {
                // echo 'Block ID: ' . $row['id'] . '<br>';
                // echo 'Block Name: ' . $row['name'] . '<br>';
                // and so on...
                $updatedata = array();
                $updatedata['creatorid'] = Yii::$app->user->id;
                $updatedata['modifiedby'] = Yii::$app->user->id;
                $updatedata['ownerid'] = Yii::$app->user->id;
                $updatedata['createdtime'] = date("Y-m-d H:i:s");
                $updatedata['modifiedtime'] = date("Y-m-d H:i:s");
                $updatedata['grndit_id'] = $Recordid;
                $updatedata['product_name'] = $row['product_name'];
                $updatedata['hsn_code'] = $row['hsn_code'];
                $updatedata['qty'] = 1;
                $updatedata['serial_no'] = $row['bar_code'];
                $updatedata['status'] = 1;
                $updatedata['po_number'] = $row['purchase_order_number'];
                $updatedata['location'] = $row['delivery_entity_name'];
                //insert 
                $connection->createCommand()
                    ->insert("inventory_dit", $updatedata)
                    ->execute();


            }
            $query = (new \yii\db\Query())
                ->select([
                    'grndit_product_details.product_name',
                    'grn_dit.delivery_entity_name',
                ])
                ->from(['grndit_product_details']) // If you want to alias it, use ['gpd' => 'grndit_product_details']
                ->innerJoin('grn_dit', 'grndit_product_details.grndit_id = grn_dit.grndit_id')
                ->where(["grndit_product_details.grndit_id" => $Recordid])
                ->groupBy('product_name') // <-- Add this line
                ->all();



            foreach ($query as $row) {
                $StockCalculation = new StockCalculation();
                $StockCalculation->getTodayStockSingleProduct($row['product_name'],$row['delivery_entity_name']);
            }


        }

        ///update grn dit status
        $all_zero_or_blank = true;
        $all_match = true;
        $grndit_product_details = $connection->createCommand("Select count(*) as cnt from grndit_product_details where grndit_id=:grndit_id and balance_qty>0")->bindValue(":grndit_id", $Recordid)->queryOne();
        $cnt = $grndit_product_details['cnt'];
        // foreach ($grndit_product_details as $product) {
        //     $po_qty = isset($product['po_qty']) ? (int)$product['po_qty'] : 0;
        //     $received_qty = isset($product['received_qty']) && $product['received_qty'] !== '' ? (int)$product['received_qty'] : 0;

        //     if ($received_qty > 0) {
        //         $all_zero_or_blank = false;
        //     }

        //     if ($po_qty !== $received_qty) {
        //         $all_match = false;
        //     }
        // }
        $status = 0;
        if ($cnt) {
            $status = '1';//Partially Received
        } else {
            $status = '2';//fully received
        }
        if ($status) {
            $connection->createCommand("update grn_dit set status=:status where grndit_id=:grndit_id")->bindValue(":grndit_id", $Recordid)->bindValue(":status", $status)->queryAll();
        }

    }

}
