<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "delivery_challan".
 *
 * @property int $deliverychallan_id
 * @property int|null $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $deliverychallan_no
 * @property int|null $delivery_challan_type
 * @property int|null $delivery_challan_location
 * @property string|null $company_name
 * @property string|null $company_address
 * @property string|null $company_gstin
 * @property string|null $company_pan
 * @property string|null $contact_number
 * @property string|null $delivery_challan_date
 * @property string|null $dc_eway_bill_number
 * @property int|null $so_number
 * @property int|null $mod_of_transport
 * @property int|null $transporter_name
 * @property int|null $status
 * @property string|null $pod_image
 * @property string|null $delivery_date
 * @property int|null $return_condition
 * @property string|null $returned_date
 * @property string|null $vehicle_docket_number
 * @property int|null $no_of_pcs
 * @property int|null $number_of_boxes
 * @property float|null $shipment_weight
 * @property float|null $freight_cost
 * @property string|null $customer_bill_to_name
 * @property string|null $customer_bill_to_address
 * @property string|null $customer_bill_to_gstin
 * @property string|null $customer_bill_to_pan
 * @property int|null $payment_terms
 * @property string|null $cust_po_number
 * @property string|null $cust_po_date
 * @property string|null $customer_ship_to_name
 * @property string|null $customer_ship_to_address
 * @property string|null $customer_ship_to_gstin
 * @property string|null $state_code
 * @property string|null $customer_ship_to_pan
 * @property string|null $material_receiver_name
 * @property string|null $material_receiver_contact_number
 * @property string|null $material_receiver_alt_contact_number
 * @property string|null $material_receiver_email
 * @property string|null $poduct_description
 * @property int|null $product_qty
 * @property string|null $product_hsn
 * @property float|null $unit_price
 * @property float|null $total_amount
 * @property float|null $gst_rate
 * @property float|null $gst_amount
 * @property float|null $invoice_sub_total
 * @property float|null $total_invoice_amt
 * @property string|null $total_invoice_amount_words
 * @property int|null $invoice_created
 * @property string|null $invoice_date
 * @property string|null $invoice_number
 * @property int $deleted
 */
class DeliveryChallan extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'delivery_challandit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'deliverychallan_no', 'delivery_challan_type', 'delivery_challan_location', 'company_name', 'company_address', 'company_gstin', 'company_pan', 'contact_number', 'delivery_challan_date', 'dc_eway_bill_number', 'so_number', 'mod_of_transport', 'transporter_name', 'status', 'pod_image', 'delivery_date', 'return_condition', 'returned_date', 'vehicle_docket_number', 'no_of_pcs', 'number_of_boxes', 'shipment_weight', 'freight_cost', 'customer_bill_to_name', 'customer_bill_to_address', 'customer_bill_to_gstin', 'customer_bill_to_pan', 'payment_terms', 'cust_po_number', 'cust_po_date', 'customer_ship_to_name', 'customer_ship_to_address', 'customer_ship_to_gstin', 'state_code', 'customer_ship_to_pan', 'material_receiver_name', 'material_receiver_contact_number', 'material_receiver_alt_contact_number', 'material_receiver_email',  'invoice_created', 'invoice_date', 'invoice_number'], 'default', 'value' => null],
            [['deleted'], 'default', 'value' => 0],
            [['ownerid', 'creatorid', 'modifiedby', 'delivery_challan_type', 'delivery_challan_location', 'so_number', 'mod_of_transport', 'transporter_name', 'status', 'return_condition', 'no_of_pcs', 'number_of_boxes', 'payment_terms', 'invoice_created', 'deleted'], 'integer'],
            [['creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['createdtime', 'modifiedtime', 'delivery_challan_date', 'delivery_date', 'returned_date', 'cust_po_date', 'invoice_date'], 'safe'],
            [['shipment_weight', 'freight_cost', ], 'number'],
            [['deliverychallan_no', 'company_pan', 'state_code'], 'string', 'max' => 200],
            [['company_name', 'pod_image', 'customer_bill_to_name', 'customer_ship_to_name', ], 'string', 'max' => 500],
            [['company_address', 'customer_bill_to_address', 'customer_ship_to_address',], 'string', 'max' => 1000],
            [['company_gstin', 'dc_eway_bill_number', 'vehicle_docket_number', 'customer_bill_to_gstin', 'cust_po_number', 'customer_ship_to_gstin', 'material_receiver_name', 'material_receiver_email', 'invoice_number'], 'string', 'max' => 255],
            [['contact_number', 'material_receiver_contact_number', 'material_receiver_alt_contact_number'], 'string', 'max' => 15],
            [['customer_bill_to_pan', 'customer_ship_to_pan'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'deliverychallan_id' => 'Deliverychallan ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'deliverychallan_no' => 'Deliverychallan No',
            'delivery_challan_type' => 'Delivery Challan Type',
            'delivery_challan_location' => 'Delivery Challan Location',
            'company_name' => 'Company Name',
            'company_address' => 'Company Address',
            'company_gstin' => 'Company Gstin',
            'company_pan' => 'Company Pan',
            'contact_number' => 'Contact Number',
            'delivery_challan_date' => 'Delivery Challan Date',
            'dc_eway_bill_number' => 'Dc Eway Bill Number',
            'so_number' => 'So Number',
            'mod_of_transport' => 'Mod Of Transport',
            'transporter_name' => 'Transporter Name',
            'status' => 'Status',
            'pod_image' => 'Pod Image',
            'delivery_date' => 'Delivery Date',
            'return_condition' => 'Return Condition',
            'returned_date' => 'Returned Date',
            'vehicle_docket_number' => 'Vehicle Docket Number',
            'no_of_pcs' => 'No Of Pcs',
            'number_of_boxes' => 'Number Of Boxes',
            'shipment_weight' => 'Shipment Weight',
            'freight_cost' => 'Freight Cost',
            'customer_bill_to_name' => 'Customer Bill To Name',
            'customer_bill_to_address' => 'Customer Bill To Address',
            'customer_bill_to_gstin' => 'Customer Bill To Gstin',
            'customer_bill_to_pan' => 'Customer Bill To Pan',
            'payment_terms' => 'Payment Terms',
            'cust_po_number' => 'Cust Po Number',
            'cust_po_date' => 'Cust Po Date',
            'customer_ship_to_name' => 'Customer Ship To Name',
            'customer_ship_to_address' => 'Customer Ship To Address',
            'customer_ship_to_gstin' => 'Customer Ship To Gstin',
            'state_code' => 'State Code',
            'customer_ship_to_pan' => 'Customer Ship To Pan',
            'material_receiver_name' => 'Material Receiver Name',
            'material_receiver_contact_number' => 'Material Receiver Contact Number',
            'material_receiver_alt_contact_number' => 'Material Receiver Alt Contact Number',
            'material_receiver_email' => 'Material Receiver Email',
            'invoice_created' => 'Invoice Created',
            'invoice_date' => 'Invoice Date',
            'invoice_number' => 'Invoice Number',
            'deleted' => 'Deleted',
        ];
    }

}
