<?php

namespace backend\modules\deliverychallandit\controllers;

use common\components\TcpdfHelper;
use common\components\MyPDF;
use common\components\PdfHeaderFooterHelper;
use common\controllers\ModuleController;
use Yii;

/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'single';
    public $ModuleName = 'deliverychallandit';
    public $FieldId = 'deliverychallan_id';
    public $TableName = 'delivery_challandit';
    public $TabLabel = 'Delivery Challan';
    public $TabId = '88';
    /**
     * Renders the index view for the module
     * @return string
     */
    //  public function beforeAction($action)
    // {
    //     $this->layout = '@app/views/layouts/main';  // Set the main layout before each action
    //     return parent::beforeAction($action);
    // }

    public function actionExample()
    {
        return $this->render('index');
    }



    public function actionGetcompanydetail()
    {
        $warehouse_id = Yii::$app->request->get('dc_location_id');
        $connection = Yii::$app->db;
        // SELECT warehouse_name,address,gstn,contact_number,pan_number from warehouse 
        $command = $connection->createCommand("
                        SELECT warehouse.*,c.city_name from warehouse 
                        left join city c on c.cityid = warehouse.city
                          WHERE warehouse_id = :warehouse_id
                    ")->bindValue(":warehouse_id", $warehouse_id);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Product info found.',
                'data' => ''
            ]);
        }
    }

    public function actionGetcustomerpodetail()
    {
        $so_number = Yii::$app->request->get('so_number');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                         SELECT 
                            so.customer_po_num,
                            so.customer_po_date,
                            so.bill_to_legal_name,
                            so.address,
                            so.gst,
                            so.pan,
                            so.account_name,
                            so.customer_payment_terms,
                            va.acc_name
                        FROM 
                            salesorder_dit so
                            join vendor_account va on va.vendoraccid = so.account_name
                        WHERE 
                            so.salesorder_dit_id = :so_number;
                    ")->bindValue(":so_number", $so_number);
        $prod_command = $connection->createCommand("
                         SELECT spd.*,product_dit.product_description as prod_name  
                         FROM salesorderdit_product_details spd 
                         join product_dit on product_dit.productdit_id = spd.product_name
                         WHERE salesorder_dit_id = :so_number;
                    ")->bindValue(":so_number", $so_number);
        $ship_command = $connection->createCommand("
                         SELECT ssa.* ,v.vendor_loc_name
                         FROM 
                         salesorderdit_ship_to_address ssa 
                         join vendor_locations v on v.vendorloc_id = ssa.ship_delivery_location
                         WHERE salesorder_dit_id = :so_number;
                    ")->bindValue(":so_number", $so_number);

        $columns = $command->queryOne();
        $prod_columns = $prod_command->queryAll();
        $ship_columns = $ship_command->queryOne();
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
                'product_details' => $prod_columns,
                'ship_details' => $ship_columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Info found.',
                'data' => ''
            ]);
        }
    }

    public function actionGetvendorlocationdetail()
    {
        $vendorloc_id = Yii::$app->request->get('vendor_location_name');
        $connection = Yii::$app->db;
        // SELECT warehouse_name,address,gstn,contact_number,pan_number from warehouse 
        $command = $connection->createCommand("
                        SELECT vendor_locations.*,c.city_name,s.state_value,v.gst_number from vendor_locations 
                        left join city c on c.cityid = vendor_locations.city
                        left join state s on s.state_id = vendor_locations.state
                        left join vendor_account v on v.vendoraccid = vendor_locations.vendor_account
                          WHERE vendorloc_id = :vendorloc_id
                    ")->bindValue(":vendorloc_id", $vendorloc_id);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Product info found.',
                'data' => ''
            ]);
        }   
    }

    public function actionGetmaterialreceiverdetail()
    {
        $contact = Yii::$app->request->get('contact');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                         SELECT 
                            email,
                            mobile,
                            alternative_phone
                        FROM 
                            contacts
                        WHERE 
                            contacts_id = :contact;
                    ")->bindValue(":contact", $contact);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Info found.',
                'data' => ''
            ]);
        }
    }

    public function actionGetfocdetail()
    {
        $foc_number = Yii::$app->request->get('foc_number');
        $connection = Yii::$app->db;
        $command = $connection->createCommand("
                        SELECT foc.*,CONCAT(c.first_name, ' ', c.last_name) AS contact_name 
                        from foc_dit foc 
                        left join contacts c on c.contacts_id = foc.customer_name
                        WHERE foc.focdit_id = :foc_number
                    ")->bindValue(":foc_number", $foc_number);
        $prod_command = $connection->createCommand("
                         SELECT foc.*,pd.product_name as product_ori_name
                         FROM focdit_product_details foc 
                         join product_dit pd on pd.productdit_id = foc.product_name
                         WHERE foc.focdit_id = :foc_number
                    ")->bindValue(":foc_number", $foc_number);
        $columns = $command->queryOne();
        $prod_columns = $prod_command->queryAll();
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
                'product_details' => $prod_columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No FOC info found.',
                'data' => ''
            ]);
        }
    }

    public function actionGeneratepdf($Record)
    {
        $sql = "select delivery_challandit.*,state.state_value as state_name,c.first_name,c.last_name,
        vl.state as warehouse_state,vl.statecode as warehouse_state_code,vl.pincode as warehouse_pin,
        vl2.state as shipfrom_state,vl2.statecode as shipfrom_statecode,vl2.pincode as shipfrom_pin,
        vl2.warehouse_name as shipfrom_warehousename,vl2.address as shipfrom_address,vl2.gstn as shipfrom_gstin,
        vl2.city as shipfrom_city,vls.vendor_loc_name,vls.address as vendor_address,vls.gstin_no_uin as vendor_gstin,
        vls.pincode as vendor_pincode,vls.state as vendor_state,vls.state_code as vendor_statecode,vls.city as vendor_city,
        va2.acc_name as vendorname from  `delivery_challandit` 
        left join warehouse vl on vl.warehouse_id = delivery_challandit.delivery_challan_location 
        LEFT JOIN warehouse vl2 ON vl2.warehouse_id = delivery_challandit.warehouse_location_name
        left join state on state.state_code = delivery_challandit.state_code
        left join contacts c on c.contacts_id = delivery_challandit.material_receiver_name
        left join vendor_locations vls on vls.vendorloc_id = delivery_challandit.vendor_location_name
        left join vendor_account va2 on va2.vendoraccid = delivery_challandit.vendor_name
        where deliverychallan_id = :Record";
        $connection = Yii::$app->db;
        $command = $connection->createCommand($sql)->bindValue(":Record", $Record);
        $columns = $command->queryOne();

        //pu.uom_value, join prod_uom pu on pu.uom_id = p.uom 
        $sql = "select deliverychallandit_product_details.*,p.product_name,p.product_description,pu.productdit_uom_value
        from deliverychallandit_product_details
        left join product_dit p on p.productdit_id = deliverychallandit_product_details.poduct_description 
        left join productdit_uom pu on pu.productdit_uom_id = p.uom
        where deliverychallan_id = :Record";
        $connection = Yii::$app->db;
        $command = $connection->createCommand($sql)->bindValue(":Record", $Record);
        $quoteitems = $command->queryAll();
        // echo "<pre>";print_r($columns);die;

         $sales_sql = "
                         SELECT 
                            state_code,
                            state,
                            pin_code
                        FROM 
                            salesorder_dit
                        WHERE 
                            salesorder_dit_id = :so_number
                    ";

        $sales_command = $connection->createCommand($sales_sql)->bindValue(":so_number", $columns['so_number']);
        $sales_quoteitems = $sales_command->queryOne();

        $ship_command = $connection->createCommand("
                         SELECT ssa.* 
                         FROM 
                         salesorderdit_ship_to_address ssa 
                         WHERE salesorder_dit_id = :so_number;
                    ")->bindValue(":so_number",$columns['so_number']);

        $ship_columns = $ship_command->queryOne();

        $deshwal_logo = Yii::getAlias('@webroot/thememain/img/deshwal-header.png');

        $record_id = $Record; //"74";//Yii::$app->request->post('Record');

        // Company Information
        $todaydate = date("M d,Y");

        $warehous_name = $columns['company_name'];
        $warehous_address = $columns['company_address'];
        $warehouse_gstin = $columns['company_gstin'];
        $warehouse_state = $columns['warehouse_state'];
        $warehouse_state_code = $columns['warehouse_state_code'];
        $warehouse_pin = $columns["warehouse_pin"];

        $customer_ship_to_name = $columns['customer_ship_to_name'];
        $customer_ship_to_address = $columns['customer_ship_to_address'];
        $customer_ship_to_gstin = $columns['customer_ship_to_gstin'];
        $state_code = $ship_columns['ship_state_code'];
        $state_name = $ship_columns['ship_state'];
        $customer_ship_to_city = $ship_columns['ship_city'];
        $customer_ship_to_pin_code = $ship_columns['ship_pin_code'];

        $deliverychallan_no = $columns['deliverychallan_no'];
        $delivery_challan_date = date("d-M-Y", strtotime($columns['delivery_challan_date']));
        
        $customer_bill_to_name = $columns['customer_bill_to_name'];
        $customer_bill_to_address = $columns['customer_bill_to_address'];
        $customer_bill_to_gstin = $columns['customer_bill_to_gstin'];
        $customer_bill_to_state_code = $sales_quoteitems['state_code'];
        $customer_bill_to_state_name = $sales_quoteitems['state'];
        $customer_bill_to_pin_code = $sales_quoteitems['pin_code'];

        $ship_from_address =   $ship_loc_name = $ship_from_gstin =   $ship_from_state_code = $ship_from_state_name =   $ship_from_city =  $ship_from_pin_code = '';
        
        if($columns['ship_by'] == 1)
        {
            // $customer_ship_from_name = $columns['customer_ship_to_name'];
            $ship_from_name = $columns['shipfrom_warehousename'];
            $ship_from_address = $columns['shipfrom_address'];
            $ship_from_gstin = $columns['shipfrom_gstin'];
            $ship_from_state_code = $columns['shipfrom_statecode'];
            $ship_from_state_name = $columns['shipfrom_state'];
            $ship_from_city = $columns['shipfrom_city'];
            $ship_from_pin_code = $columns['shipfrom_pin'];
        }
        else if($columns['ship_by'] == 2)
        {
            // $customer_ship_from_name = $columns['customer_ship_to_name'];
            $ship_from_name= $columns['vendorname'];
            $ship_loc_name = $columns['vendor_loc_name'].'<br/>';
            $ship_from_address = $columns['vendor_address'];
            $ship_from_gstin = $columns['vendor_gstin'];
            $ship_from_state_code = $columns['vendor_statecode'];
            $ship_from_state_name = $columns['vendor_state'];
            $ship_from_city = $columns['vendor_city'];
            $ship_from_pin_code = $columns['vendor_pincode'];
        }


        $material_receiver_name = $columns['first_name']." ".$columns['last_name'];
        $material_receiver_contact_number = $columns['material_receiver_contact_number'];
        // Set image path (make sure the image path is accessible by TCPDF)
        $logoPath = $deshwal_logo; // Place header.png in TCPDF's images directory
        // logo path
        // $logo = K_PATH_IMAGES . 'header.png';
        $deshwal_logo = Yii::getAlias('@webroot/thememain/img/dcpdfheader.jpg');
        $logoPath = $deshwal_logo; 
        $tplVars = [
            'deliverychallan_no'   => $deliverychallan_no,
            'deliverychallan_date' => $delivery_challan_date,
            'warehouse_name'       => $warehous_name,
            'warehouse_address'    => $warehous_address,
            'warehouse_gstin'      => $warehouse_gstin,
            'warehouse_state'      => $warehouse_state,
            'warehouse_state_code' => $warehouse_state_code,
            'warehouse_pin'        => $warehouse_pin,
            'customer_name'        => $customer_bill_to_name,
            'logoPath'             => $logoPath,
        ];
        
        $pdf = new MyPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Deshwal Waste Management');
        $pdf->SetTitle('DevIT Delivery Challan PDF');
        $pdf->SetSubject('DevIT Delivery Challan PDF');
        $pdf->SetKeywords('TCPDF, PDF, Delivery Challan');
        $pdf->SetMargins(10, 10, 10);  
        $pdf->SetAutoPageBreak(true, 25);
        $pdf->SetFont('dejavusans', '', 9);

        
        PdfHeaderFooterHelper::setupPdfWithTemplate(
            $pdf,
            (int)$this->TabId,      
            $tplVars,
            'delivery_challan',      
            false
        );
        $pdf->AddPage();
        $pdf->drawHeaderContent();
        // $pdf->drawStamp();
            
        $html = <<<EOD
        <table style="font-weight:bold;font-size:14px;text-align:center;" cellpadding="6">
          <tr>
            <td width="100%">
                <table>
                    <tr>
                        <td>
                            Delivery Challan
                        </td>
                        
                    </tr>
                    
                </table>
              
            </td>
          </tr>
        </table>
        <table cellpadding="4" cellspacing="0" border="1" width="100%">
            <tr>
                <td width="50%" rowspan="2" valign="top" style="border: 0.1mm solid #000;text-align:left;">
                    Bill From : <br/><b>$warehous_name</b><br/>$warehous_address, $warehouse_pin<br/>
                    GSTIN/UIN: $warehouse_gstin<br/>
                    State Name : $warehouse_state, Code : $warehouse_state_code
                </td>
                <td width="25%" style="border: 0.1mm solid #000;text-align:left;">Delivery Note No.<br/><strong>$deliverychallan_no</strong></td>
                <td width="25%" style="border: 0.1mm solid #000;text-align:left;">Dated<br/><strong>$delivery_challan_date</strong></td>
            </tr>
            <tr>
                <td style="border: 0.1mm solid #000;text-align:left;"></td>
                <td style="border: 0.1mm solid #000;text-align:left;">Mode/Terms of Payment<br/></td>
            </tr>
            <tr>
                <td width="50%" valign="top" style="border: 0.1mm solid #000;text-align:left;">
                    Ship From : <br/><b>$ship_from_name</b><br/>$ship_loc_name $ship_from_address, $ship_from_pin_code<br/>
                    GSTIN/UIN: $ship_from_gstin<br/>
                    State Name : $ship_from_state_name, Code : $ship_from_state_code
                </td>
                <td width="25%" rowspan="2" style="border: 0.1mm solid #000;text-align:left;">Reference No. & Date.<br/><strong>&nbsp;</strong></td>
                <td width="25%" rowspan="2" style="border: 0.1mm solid #000;text-align:left;">Other References<br/><strong>&nbsp;</strong></td>
            </tr>
        </table>

        <table cellpadding="4" cellspacing="0" border="1" width="100%">
            <tr>
                <td width="50%" rowspan="3" valign="top" style="border: 0.1mm solid #000;text-align:left;">
                    Consignee (Ship to)<br/><b>$customer_bill_to_name</b><br/>
                    $customer_ship_to_address, $customer_ship_to_city, $customer_ship_to_pin_code<br/>
                    GSTIN/UIN : $customer_ship_to_gstin<br/>
                    State Name : $state_name, Code : $state_code
                </td>
                <td width="25%" style="border: 0.1mm solid #000;text-align:left;">Buyer's Order No.<br/><strong>&nbsp;</strong></td>
                <td width="25%" style="border: 0.1mm solid #000;text-align:left;">Dated<br/><strong></strong></td>
            </tr>

            <tr>
                <td style="border: 0.1mm solid #000;text-align:left;">Dispatch Doc No.<br/><strong>&nbsp;</strong></td>
                <td style="border: 0.1mm solid #000;text-align:left;"></td>
            </tr>
            <tr>
                <td style="border: 0.1mm solid #000;text-align:left;">Dispatched through<br/><strong>&nbsp;</strong></td>
                <td style="border: 0.1mm solid #000;text-align:left;">Destination<br/><strong>&nbsp;</strong></td>
            </tr>
        </table>

        <table cellpadding="4" cellspacing="0" border="1" width="100%">
            <tr>
                <td width="50%" valign="top" style="border: 0.1mm solid #000;text-align:left;">
                    Buyer (Bill to)<br/><b>$customer_bill_to_name</b><br/>
                    $customer_bill_to_address, $customer_bill_to_pin_code<br/>
                    GSTIN/UIN : $customer_bill_to_gstin<br/>
                    State Name : $customer_bill_to_state_name, Code : $customer_bill_to_state_code<br/>
                    Place of Supply : $customer_bill_to_state_name
                </td>

                <td width="50%" valign="top" style="border: 0.1mm solid #000;text-align:left;">
                    Receiver Name<br/>
                    <b>Contact Details</b><br/>
                    <strong>$material_receiver_name $material_receiver_contact_number</strong>
                </td>
            </tr>
        </table>

        <table style="font-size: 7px;" cellpadding="4" cellspacing="0" border="1" width="100%">
            <tr>
              <td width="4%"  style="border: 0.1mm solid #000;text-align:center;">SI.No</td>
              <td width="41%" style="border: 0.1mm solid #000;text-align:center;">Discription of Goods</td>
              <td width="12%" style="border: 0.1mm solid #000;text-align:center;">HSN/SAC</td>
              <td width="8%"  style="border: 0.1mm solid #000;text-align:center;">GST Rate</td>
              <td width="10%" style="border: 0.1mm solid #000;text-align:center;">Quantity</td>
              <td width="10%" style="border: 0.1mm solid #000;text-align:center;">Rate</td>
              <td width="5%"  style="border: 0.1mm solid #000;text-align:center;">per</td>
              <td width="10%" style="border: 0.1mm solid #000;text-align:center;">Amount</td>
            </tr>
EOD;

        $i = 1;
        $total_qty = 0;
        $gst_amount_ = 0;
        $total_gst_rate = 0.00;
        $total_unit_amount = 0.00;
        $total_amount_for_tax = 0.00;
        foreach ($quoteitems as $value) {
            if ($i == 1)
                $amtinwords = $columns['total_invoice_amount_words'];
            $productid = $value['product_name'];
            $product_desc = $value['product_description'];
            $hsn_code = $value['product_hsn'];
            $gstrate = $value['gst_rate'];
            $quantity = $value['product_qty'];
            $rate = number_format($value['unit_price'], 2);
            $per = $value['productdit_uom_value'];
            $total_amount = number_format($value['total_amount'], 2);
            $total_qty = $total_qty + $quantity;

            $gst_rate_ = (float)$value['gst_rate'];
            $total_gst_rate = $total_gst_rate + $gst_rate_;
            $total_unit_amount = $total_unit_amount + $value['unit_price'];
            $total_amount_for_tax = $total_amount_for_tax + $value['total_amount'];
            if ($customer_bill_to_state_code == $state_code) {
                $gst_amount_ = $gst_amount_ + ($value['gst_amount'] / 2);
            } else {
                $gst_amount_ = $gst_amount_ + $value['gst_amount'];
            }
            $html .= <<<EOD
        <tr>
          <td align="right" style="border: 0.1mm solid #000;text-align:right;">$i</td>
          <td style="border: 0.1mm solid #000;text-align:left;">$productid<br/>$product_desc</td>
          <td align="right" style="border: 0.1mm solid #000;text-align:right;">$hsn_code</td>
          <td align="right" style="border: 0.1mm solid #000;text-align:right;">$gstrate %</td>
          <td align="right" style="border: 0.1mm solid #000;text-align:right;"><b>$quantity $per</b></td>
          <td align="right" style="border: 0.1mm solid #000;text-align:right;">$rate</td>
          <td align="right" style="border: 0.1mm solid #000;text-align:right;">$per</td>
          <td align="right" style="border: 0.1mm solid #000;text-align:right;">$total_amount</td>
        </tr>
EOD;
            $i++;
        }
        $cgst_in_per_ = $sgst_in_per_ = $total_gst_rate / 2;
        $cgst_amt_ = ($total_amount_for_tax * $cgst_in_per_) / 100;
        $sgst_amt_ = ($total_amount_for_tax * $sgst_in_per_) / 100;
        $cgst_amt_in_format = number_format($cgst_amt_, 2);
        $sgst_amt_in_format = number_format($sgst_amt_, 2);

        if ($customer_bill_to_state_code == $state_code) {
            $html .= <<<EOD
            <tr>
            <td align="right" style="border: 0.1mm solid #000;text-align:right;"></td>
            <td style="border: 0.1mm solid #000;text-align:right;"><b>Output CGST<br/>Output SGST</b><br/><br/><br/><br/></td>
            <td align="right" style="border: 0.1mm solid #000;text-align:right;"></td>
            <td align="right" style="border: 0.1mm solid #000;text-align:right;"></td>
            <td align="right" style="border: 0.1mm solid #000;text-align:right;"></td>
            <td align="right" style="border: 0.1mm solid #000;text-align:right;"></td>
            <td align="right" style="border: 0.1mm solid #000;text-align:right;"></td>
            <td align="right" style="border: 0.1mm solid #000;text-align:right;"><b>$gst_amount_<br/>$gst_amount_</b></td>
            </tr>
EOD;
        } else {
            $html .= <<<EOD
            <tr>
            <td align="right" style="border: 0.1mm solid #000;text-align:right;"></td>
            <td style="border: 0.1mm solid #000;text-align:right;"><b>Output IGST</b><br/><br/><br/><br/></td>
            <td align="right" style="border: 0.1mm solid #000;text-align:right;"></td>
            <td align="right" style="border: 0.1mm solid #000;text-align:right;"></td>
            <td align="right" style="border: 0.1mm solid #000;text-align:right;"></td>
            <td align="right" style="border: 0.1mm solid #000;text-align:right;"></td>
            <td align="right" style="border: 0.1mm solid #000;text-align:right;"></td>
            <td align="right" style="border: 0.1mm solid #000;text-align:right;"><b>$gst_amount_</b></td>
            </tr>
EOD;
        }
        if ($customer_bill_to_state_code == $state_code) {
            $total_with_tax  = number_format($total_amount_for_tax + ($gst_amount_ * 2), 2);
        } else {
            $total_with_tax  = number_format($total_amount_for_tax + $gst_amount_, 2);
        }

        $html .= <<<EOD
            <tr>
            <td align="right" style="border: 0.1mm solid #000;text-align:right;"></td>
            <td align="right" style="border: 0.1mm solid #000;text-align:right;">Total</td>
            <td align="right" style="border: 0.1mm solid #000;text-align:right;"></td>
            <td align="right" style="border: 0.1mm solid #000;text-align:right;"></td>
            <td align="right" style="border: 0.1mm solid #000;text-align:right;"><b>$total_qty $per</b></td>
            <td align="right" style="border: 0.1mm solid #000;text-align:right;"></td>
            <td align="right" style="border: 0.1mm solid #000;text-align:right;"></td>
            <td align="right" style="border: 0.1mm solid #000;text-align:right;"><b>₹ $total_with_tax</b></td>
            </tr>
            <tr>   
                <td colspan="8" style="border: 0.1mm solid #000;text-align:left;">Amount Chargeable (in words): <br/><b>INR $amtinwords</b></td>
            </tr>
            <tr style="text-align:center;font-weight:bold;">
                <td rowspan="2" width="46%" style="border: 0.1mm solid #000;">HSN/SAC</td>
                <td rowspan="2" width="10%" style="border: 0.1mm solid #000;">Taxable Value</td>
EOD;
        if ($customer_bill_to_state_code == $state_code) {
            $html .= <<<EOD
                <td colspan="2" width="17%" style="border: 0.1mm solid #000;">CGST</td>
                <td colspan="2" width="17%" style="border: 0.1mm solid #000;">SGST/UTGST</td>
EOD;
        } else {
            $html .= <<<EOD
                <td colspan="2" width="34%" style="border: 0.1mm solid #000;">IGST</td>
EOD;
        }
        $html .= <<<EOD
                <td rowspan="2" width="10%" style="border: 0.1mm solid #000;">Total Tax Amount</td>
            </tr>
EOD;
        if ($customer_bill_to_state_code == $state_code) {
            $html .= <<<EOD
            <tr style="text-align:center;font-weight:bold;">
                <td width="8%" style="border: 0.1mm solid #000;">Rate</td>
                <td width="9%" style="border: 0.1mm solid #000;">Amount</td>
                <td width="8%" style="border: 0.1mm solid #000;">Rate</td>
                <td width="9%" style="border: 0.1mm solid #000;">Amount</td>
            </tr>
EOD;
        } else {
            $html .= <<<EOD
            <tr style="text-align:center;font-weight:bold;">
                <td width="16%" style="border: 0.1mm solid #000;">Rate</td>
                <td width="18%" style="border: 0.1mm solid #000;">Amount</td>
            </tr>
EOD;
        }

        $i = 1;
        $all_total_taxable = 0.00;
        $all_cgst_amt = $all_sgst_amt = $all_tax_amt = 0.00;

        foreach ($quoteitems as $value) {
            $hsn_code1 = $value['product_hsn'];
            $gst_rate = (float)$value['gst_rate'];
            $cgst_in_per = $sgst_in_per = $gst_rate / 2;

            $quantity = (float)$value['product_qty'];
            $gstamount = (float)$value['gst_amount'] / 2;
            $unit_price = (float)$value['unit_price'];
            $rate = number_format($value['total_amount'], 2);
            $cgst_amt = (($unit_price * $quantity) * $cgst_in_per) / 100;
            $sgst_amt = (($unit_price * $quantity) * $sgst_in_per) / 100;
            $total_tax_amt = $cgst_amt + $sgst_amt;
            $all_total_taxable += $value['total_amount'];
            $all_cgst_amt += $gstamount;
            $all_sgst_amt += $gstamount;
            $all_tax_amt  += $total_tax_amt;

            $cgst_amt_f = number_format($gstamount, 2);
            $sgst_amt_f = number_format($gstamount, 2);
            if ($customer_bill_to_state_code == $state_code) {
                $total_tax_amt_f = number_format($gstamount + $gstamount, 2);
                $html .= <<<EOD
            <tr style="text-align:center;">
                <td align="left" style="border: 0.1mm solid #000;text-align:left;">$hsn_code1</td>
                <td align="right" style="border: 0.1mm solid #000;text-align:right;">$rate</td>
                <td align="right" style="border: 0.1mm solid #000;text-align:right;">{$cgst_in_per}%</td>
                <td align="right" style="border: 0.1mm solid #000;text-align:right;">$cgst_amt_f</td>
                <td align="right" style="border: 0.1mm solid #000;text-align:right;">{$sgst_in_per}%</td>
                <td align="right" style="border: 0.1mm solid #000;text-align:right;">$sgst_amt_f</td>
                <td align="right" style="border: 0.1mm solid #000;text-align:right;">$total_tax_amt_f</td>
            </tr>
EOD;
            } else {
                $total_tax_amt_f = number_format(($gstamount * 2), 2);
                $html .= <<<EOD
            <tr style="text-align:center;">
                <td align="left" style="border: 0.1mm solid #000;text-align:left;">$hsn_code1</td>
                <td align="right" style="border: 0.1mm solid #000;text-align:right;">$rate</td>
                <td align="right" style="border: 0.1mm solid #000;text-align:right;">{$gst_rate}%</td>
                <td align="right" style="border: 0.1mm solid #000;text-align:right;">{$value["gst_amount"]}</td>
                <td align="right" style="border: 0.1mm solid #000;text-align:right;">$total_tax_amt_f</td>
            </tr>
EOD;
            }
        }

        $all_total_taxable_f = number_format($all_total_taxable, 2);
        $all_cgst_amt_f = number_format($all_cgst_amt, 2);
        $all_sgst_amt_f = number_format($all_sgst_amt, 2);

        $all_igst_amt = number_format(($all_sgst_amt + $all_sgst_amt), 2);

        $all_tax_amt_in_word = $this->numberToWords($all_tax_amt);
        $all_tax_amt_f = number_format($all_tax_amt, 2);
        if ($customer_bill_to_state_code == $state_code) {
            $html .= <<<EOD
            <tr style="text-align:right;font-weight:bold;">
                <td style="border: 0.1mm solid #000;"><strong>Total</strong></td>
                <td align="right" style="border: 0.1mm solid #000;"><strong>$all_total_taxable_f</strong></td>
                <td colspan="2" align="right" style="border: 0.1mm solid #000;"><strong>$all_cgst_amt_f</strong></td>
                <td colspan="2" align="right" style="border: 0.1mm solid #000;"><strong>$all_sgst_amt_f</strong></td>
                <td align="right" style="border: 0.1mm solid #000;"><strong>$all_tax_amt_f</strong></td>
            </tr>
EOD;
            $colspan_ = 7;
        } else {
            $html .= <<<EOD
            <tr style="text-align:right;font-weight:bold;">
                <td style="border: 0.1mm solid #000;"><strong>Total</strong></td>
                <td align="right" style="border: 0.1mm solid #000;"><strong>$all_total_taxable_f</strong></td>
                <td colspan="2" align="right" style="border: 0.1mm solid #000;"><strong>$all_igst_amt</strong></td>
                <td align="right" style="border: 0.1mm solid #000;"><strong>$all_tax_amt_f</strong></td>
            </tr>
EOD;
            $colspan_ = 5;
        }

        $html .= <<<EOD
            <tr style="font-size: 9px;">
                <td colspan="$colspan_" style="border: 0.1mm solid #000;text-align:left;">
                    Tax Amount (in words) :<b> INR $all_tax_amt_in_word Only</b><br/><br/><br/>
                    <br/>Company's PAN:<b> AACCD2388K</b>
                </td>
            </tr>
            <tr style="font-size: 9px;">
                <td width="50%" style="border: 0.1mm solid #000;text-align:left;">Recd. in Good Condition</td>
                <td width="50%" style="border: 0.1mm solid #000;text-align:left;"><b>for Dev IT Serv Private Limited</b><br/><br/>Authorised Signatory</td>
            </tr>
        </table>

        <br/><br/>

        <p align="center" style="font-size: 8px;">This is a Computer Generated Document</p>

        EOD;

        $pdf->writeHTML($html, true, false, true, false, '');
        $filename = $deliverychallan_no . '_delivery_challan_' . $todaydate . '.pdf';
        $pdf->Output($filename, 'I');
    }
}
