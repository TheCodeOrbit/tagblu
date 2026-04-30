<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "invoicedit".
 *
 * @property int $invoicedit_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $invoicedit_no
 * @property string|null $delivery_challan_number
 * @property string|null $company_name
 * @property string|null $company_address
 * @property string|null $contact_number
 * @property string|null $gstin
 * @property string|null $invoice_number
 * @property string|null $invoice_date
 * @property string|null $invoice_status
 * @property int $send_for_approval
 * @property string|null $mode_of_transport
 * @property string|null $transporter_name
 * @property string|null $vehicle_docket_number
 * @property string|null $payment_terms
 * @property string|null $payment_due_date
 * @property string|null $e_invoice_number
 * @property string|null $e-way_bill_number
 * @property string|null $place_of_supply
 * @property string|null $customer_bill_name
 * @property string|null $customer_bill_address
 * @property string|null $customer_bill_gstin
 * @property string|null $customer_bill_pan
 * @property string|null $customer_po_number
 * @property string|null $customer_po_date
 * @property string|null $customer_ship_name
 * @property string|null $customer_ship_address
 * @property string|null $customer_ship_gstin
 * @property string|null $customer_ship_pan
 * @property string|null $material_receiver_name
 * @property string|null $material_receiver_contact_number
 * @property string|null $material_receiver_email
 * @property float|null $invoice_sub_total
 * @property float|null $discount
 * @property float|null $total_invoice_amount
 * @property string|null $total_invoice_amount_word
 * @property string|null $comment
 * @property int|null $mod_of_transport
 * @property string|null $warehouse_location_name
 * @property string|null $warehouse_address
 * @property string|null $warehouse_state
 * @property string|null $warehouse_pin_code
 * @property string|null $warehouse_warehouse_name
 * @property string|null $warehouse_city
 * @property string|null $warehouse_state_code
 * @property string|null $warehouse_gstin_no
 * @property string|null $vendor_name
 * @property string|null $vendor_location_name
 * @property string|null $vendor_address
 * @property string|null $vendor_city
 * @property string|null $vendor_pin_code
 * @property string|null $vendor_pan_no
 * @property string|null $vendor_state
 * @property string|null $vendor_state_code
 * @property string|null $vendor_gstin_no
 * @property int $deleted
 *
 * @property InvoiceditProductDetails[] $invoiceditProductDetails
 */
class Invoicedit extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invoicedit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['invoicedit_no', 'delivery_challan_number', 'company_name', 'company_address', 'contact_number', 'gstin', 'invoice_number', 'invoice_date', 'invoice_status', 'mode_of_transport', 'transporter_name', 'vehicle_docket_number', 'payment_terms', 'payment_due_date', 'e_invoice_number', 'e-way_bill_number', 'place_of_supply', 'customer_bill_name', 'customer_bill_address', 'customer_bill_gstin', 'customer_bill_pan', 'customer_po_number', 'customer_po_date', 'customer_ship_name', 'customer_ship_address', 'customer_ship_gstin', 'customer_ship_pan', 'material_receiver_name', 'material_receiver_contact_number', 'material_receiver_email', 'invoice_sub_total', 'discount', 'total_invoice_amount', 'total_invoice_amount_word', 'comment', 'mod_of_transport', 'warehouse_location_name', 'warehouse_address', 'warehouse_state', 'warehouse_pin_code', 'warehouse_warehouse_name', 'warehouse_city', 'warehouse_state_code', 'warehouse_gstin_no', 'vendor_name', 'vendor_location_name', 'vendor_address', 'vendor_city', 'vendor_pin_code', 'vendor_pan_no', 'vendor_state', 'vendor_state_code', 'vendor_gstin_no'], 'default', 'value' => null],
            [['deleted'], 'default', 'value' => 0],
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'send_for_approval', 'mod_of_transport', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime', 'invoice_date', 'payment_due_date', 'customer_po_date'], 'safe'],
            [['invoice_sub_total', 'discount', 'total_invoice_amount'], 'number'],
            [['warehouse_address', 'vendor_address'], 'string'],
            [['invoicedit_no', 'delivery_challan_number', 'company_name', 'customer_ship_name', 'warehouse_location_name', 'warehouse_warehouse_name', 'vendor_name', 'vendor_location_name'], 'string', 'max' => 200],
            [['company_address', 'customer_bill_address', 'customer_ship_address'], 'string', 'max' => 3000],
            [['contact_number'], 'string', 'max' => 15],
            [['gstin', 'invoice_number', 'invoice_status', 'mode_of_transport', 'transporter_name', 'vehicle_docket_number', 'payment_terms', 'e_invoice_number', 'e-way_bill_number', 'place_of_supply', 'customer_bill_name', 'customer_bill_gstin', 'customer_bill_pan', 'customer_po_number', 'customer_ship_gstin', 'customer_ship_pan', 'material_receiver_name', 'material_receiver_contact_number', 'material_receiver_email', 'warehouse_gstin_no', 'vendor_gstin_no'], 'string', 'max' => 100],
            [['total_invoice_amount_word'], 'string', 'max' => 500],
            [['comment'], 'string', 'max' => 1000],
            [['warehouse_state', 'warehouse_city', 'warehouse_state_code', 'vendor_city', 'vendor_pin_code', 'vendor_pan_no', 'vendor_state', 'vendor_state_code'], 'string', 'max' => 50],
            [['warehouse_pin_code'], 'string', 'max' => 20],
             // added for handling blank values saving in by ptpatel on date 24-01-2026
            // [['vendor_name'], 'trim'],
            // [['vendor_name'], 'required', 'message' => 'Vendor Name cannot be blank.'],
            // [['vendor_name'], 'integer', 'message' => 'Vandor Name must be a number.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'invoicedit_id' => 'Invoicedit ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'invoicedit_no' => 'Invoicedit No',
            'delivery_challan_number' => 'Delivery Challan Number',
            'company_name' => 'Company Name',
            'company_address' => 'Company Address',
            'contact_number' => 'Contact Number',
            'gstin' => 'Gstin',
            'invoice_number' => 'Invoice Number',
            'invoice_date' => 'Invoice Date',
            'invoice_status' => 'Invoice Status',
            'send_for_approval' => 'Send For Approval',
            'mode_of_transport' => 'Mode Of Transport',
            'transporter_name' => 'Transporter Name',
            'vehicle_docket_number' => 'Vehicle Docket Number',
            'payment_terms' => 'Payment Terms',
            'payment_due_date' => 'Payment Due Date',
            'e_invoice_number' => 'E Invoice Number',
            'e-way_bill_number' => 'E Way Bill Number',
            'place_of_supply' => 'Place Of Supply',
            'customer_bill_name' => 'Customer Bill Name',
            'customer_bill_address' => 'Customer Bill Address',
            'customer_bill_gstin' => 'Customer Bill Gstin',
            'customer_bill_pan' => 'Customer Bill Pan',
            'customer_po_number' => 'Customer Po Number',
            'customer_po_date' => 'Customer Po Date',
            'customer_ship_name' => 'Customer Ship Name',
            'customer_ship_address' => 'Customer Ship Address',
            'customer_ship_gstin' => 'Customer Ship Gstin',
            'customer_ship_pan' => 'Customer Ship Pan',
            'material_receiver_name' => 'Material Receiver Name',
            'material_receiver_contact_number' => 'Material Receiver Contact Number',
            'material_receiver_email' => 'Material Receiver Email',
            'invoice_sub_total' => 'Invoice Sub Total',
            'discount' => 'Discount',
            'total_invoice_amount' => 'Total Invoice Amount',
            'total_invoice_amount_word' => 'Total Invoice Amount Word',
            'comment' => 'Comment',
            'mod_of_transport' => 'Mod Of Transport',
            'warehouse_location_name' => 'Warehouse Location Name',
            'warehouse_address' => 'Warehouse Address',
            'warehouse_state' => 'Warehouse State',
            'warehouse_pin_code' => 'Warehouse Pin Code',
            'warehouse_warehouse_name' => 'Warehouse Warehouse Name',
            'warehouse_city' => 'Warehouse City',
            'warehouse_state_code' => 'Warehouse State Code',
            'warehouse_gstin_no' => 'Warehouse Gstin No',
            'vendor_name' => 'Vendor Name',
            'vendor_location_name' => 'Vendor Location Name',
            'vendor_address' => 'Vendor Address',
            'vendor_city' => 'Vendor City',
            'vendor_pin_code' => 'Vendor Pin Code',
            'vendor_pan_no' => 'Vendor Pan No',
            'vendor_state' => 'Vendor State',
            'vendor_state_code' => 'Vendor State Code',
            'vendor_gstin_no' => 'Vendor Gstin No',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * Gets query for [[InvoiceditProductDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInvoiceditProductDetails()
    {
        return $this->hasMany(InvoiceditProductDetails::class, ['invoicedit_id' => 'invoicedit_id']);
    }

}
