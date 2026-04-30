<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "delivery_challandit".
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
 * @property string|null $returnable_date
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
 * @property int|null $foc_number
 * @property string|null $customer_ship_to_name
 * @property string|null $customer_ship_to_address
 * @property string|null $customer_ship_to_gstin
 * @property string|null $state_code
 * @property string|null $customer_ship_to_pan
 * @property string|null $material_receiver_name
 * @property string|null $material_receiver_contact_number
 * @property string|null $material_receiver_alt_contact_number
 * @property string|null $material_receiver_email
 * @property int|null $invoice_created
 * @property string|null $invoice_date
 * @property string|null $invoice_number
 * @property int $send_for_approval
 * @property float|null $total_invoice_amt
 * @property string|null $total_invoice_amount_words
 * @property string|null $comment
 * @property int|null $vender_account_name
 * @property string|null $ship_by
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
 * @property DeliverychallanditProductDetails[] $deliverychallanditProductDetails
 */
class DeliveryChallandit extends \yii\db\ActiveRecord
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
            [['ownerid', 'deliverychallan_no', 'delivery_challan_type', 'delivery_challan_location', 'company_name', 'company_address', 'company_gstin', 'company_pan', 'contact_number', 'delivery_challan_date', 'dc_eway_bill_number', 'returnable_date', 'so_number', 'mod_of_transport', 'transporter_name', 'status', 'pod_image', 'delivery_date', 'return_condition', 'returned_date', 'vehicle_docket_number', 'no_of_pcs', 'number_of_boxes', 'shipment_weight', 'freight_cost', 'customer_bill_to_name', 'customer_bill_to_address', 'customer_bill_to_gstin', 'customer_bill_to_pan', 'payment_terms', 'cust_po_number', 'cust_po_date', 'foc_number', 'customer_ship_to_name', 'customer_ship_to_address', 'customer_ship_to_gstin', 'state_code', 'customer_ship_to_pan', 'material_receiver_name', 'material_receiver_contact_number', 'material_receiver_alt_contact_number', 'material_receiver_email', 'invoice_created', 'invoice_date', 'invoice_number', 'total_invoice_amt', 'total_invoice_amount_words', 'comment', 'vender_account_name', 'ship_by', 'warehouse_location_name', 'warehouse_address', 'warehouse_state', 'warehouse_pin_code', 'warehouse_warehouse_name', 'warehouse_city', 'warehouse_state_code', 'warehouse_gstin_no', 'vendor_name', 'vendor_location_name', 'vendor_address', 'vendor_city', 'vendor_pin_code', 'vendor_pan_no', 'vendor_state', 'vendor_state_code', 'vendor_gstin_no'], 'default', 'value' => null],
            [['deleted'], 'default', 'value' => 0],
            [['ownerid', 'creatorid', 'modifiedby', 'delivery_challan_type', 'delivery_challan_location', 'so_number', 'mod_of_transport', 'transporter_name', 'status', 'return_condition', 'no_of_pcs', 'number_of_boxes', 'payment_terms', 'foc_number', 'invoice_created', 'send_for_approval', 'vender_account_name', 'deleted'], 'integer'],
            [['creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['createdtime', 'modifiedtime', 'delivery_challan_date', 'returnable_date', 'delivery_date', 'returned_date', 'cust_po_date', 'invoice_date'], 'safe'],
            [['company_address', 'customer_bill_to_address', 'customer_ship_to_address', 'warehouse_address', 'vendor_address'], 'string'],
            [['shipment_weight', 'freight_cost', 'total_invoice_amt'], 'number'],
            [['deliverychallan_no', 'company_pan', 'state_code', 'warehouse_location_name', 'warehouse_warehouse_name', 'vendor_name', 'vendor_location_name'], 'string', 'max' => 200],
            [['company_name', 'pod_image', 'customer_bill_to_name', 'customer_ship_to_name', 'total_invoice_amount_words'], 'string', 'max' => 500],
            [['company_gstin', 'dc_eway_bill_number', 'vehicle_docket_number', 'customer_bill_to_gstin', 'cust_po_number', 'customer_ship_to_gstin', 'material_receiver_name', 'material_receiver_email', 'invoice_number'], 'string', 'max' => 255],
            [['contact_number', 'material_receiver_contact_number', 'material_receiver_alt_contact_number'], 'string', 'max' => 15],
            [['customer_bill_to_pan', 'customer_ship_to_pan', 'warehouse_pin_code'], 'string', 'max' => 20],
            [['comment'], 'string', 'max' => 1000],
            [['ship_by', 'warehouse_state', 'warehouse_city', 'warehouse_state_code', 'vendor_city', 'vendor_pin_code', 'vendor_pan_no', 'vendor_state', 'vendor_state_code'], 'string', 'max' => 50],
            [['warehouse_gstin_no', 'vendor_gstin_no'], 'string', 'max' => 100],
             // added for handling blank values saving in by ptpatel on date 24-01-2026
            // [['vender_account_name'], 'trim'],
            // [['vender_account_name'], 'required', 'message' => 'Vendor Name cannot be blank.'],
            // [['vender_account_name'], 'integer', 'message' => 'Vandor Name must be a number.'],
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
            'returnable_date' => 'Returnable Date',
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
            'foc_number' => 'Foc Number',
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
            'send_for_approval' => 'Send For Approval',
            'total_invoice_amt' => 'Total Invoice Amt',
            'total_invoice_amount_words' => 'Total Invoice Amount Words',
            'comment' => 'Comment',
            'vender_account_name' => 'Vender Account Name',
            'ship_by' => 'Ship By',
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
     * Gets query for [[DeliverychallanditProductDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeliverychallanditProductDetails()
    {
        return $this->hasMany(DeliverychallanditProductDetails::class, ['deliverychallan_id' => 'deliverychallan_id']);
    }

    public function savetoreports($RecordId)
    {
        $Record = (int) $RecordId;
        $this->save_stock($Record);
    }

    function save_stock($RecordId)
    {

        $sql = "SELECT * FROM deliverychallandit_product_details where deliverychallan_id = :deliverychallan_id";
        $result = Yii::$app->db->createCommand($sql)->bindValue(":deliverychallan_id", $RecordId)->queryAll();
        $location_sql = "SELECT delivery_challan_location FROM delivery_challandit where deliverychallan_id = :deliverychallan_id";
        $location = Yii::$app->db->createCommand($location_sql)->bindValue(":deliverychallan_id", $RecordId)->queryOne();
        // echo "<pre>";print_r($location);die;

        foreach ($result as $result) {
            $StockCalculation = new StockCalculation();
            $StockCalculation->getTodayStockSingleProduct($result['poduct_description'], $location['delivery_challan_location']);
        }
    }
    public function changeInvoiceStatus($RecordId)
    {
        $Record = (int) $RecordId;
        $approved_invoice = 3;//Approved
        $invoice = Invoicedit::findOne(['delivery_challan_number' => $Record, 'invoice_status' => $approved_invoice]);
        if ($invoice !== null) {
            $invoice->invoice_status = 4;//Pending for Submission
            $invoice->save(false); // use `save(true)` if you want validation

            //send notification to owner
            $notification = new Notifications();
            $notification->userid = $invoice->ownerid;
            $notification->message = "Invoice No " . $invoice->invoicedit_no . " status changed to 'Pending for Submission' .Please check";
            $notification->read_status = 0; // Unread notification
            $notification->display_status = 0;
            $notification->source_link = Yii::$app->request->baseUrl . "/invoicedit/detail?Record=" . $invoice->invoicedit_id;
            ;
            $notification->createdtime = date('Y-m-d H:i:s');
            $notification->modifiedtime = date('Y-m-d H:i:s');
            if (!$notification->save()) {
                //echo 'save failed';
                //exit;
            }
        } else {
            echo "invoice not found";
        }
    }

}
