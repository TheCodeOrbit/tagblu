<?php

namespace backend\modules\invoicedit\controllers;

use common\components\MyPDF;
use common\components\PdfHeaderFooterHelper;
use common\components\TcpdfHelper;
use common\controllers\ModuleController;
use Yii;

/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'single';
    public $ModuleName = 'invoicedit';
    public $FieldId = 'invoicedit_id';
    public $TableName = 'invoicedit';
    public $TabLabel = 'Invoice DevIT';


    public $TabId = '87';
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

    public function actionGetdcdetail()
    {
        $dcid = Yii::$app->request->get('dcid');
        $connection = Yii::$app->db;
        // SELECT * from delivery_challandit 
        //                   WHERE deliverychallan_id = :dcid
        $command = $connection->createCommand("
                        SELECT d.*,va.acc_name,opt.payment_terms_value as payment_terms,concat(cn.first_name,' ', cn.last_name) as mcvname,
                        w.warehouse_name,vls.vendor_loc_name as vendorlocname,va2.acc_name as vendorname
                        from delivery_challandit d
                                join vendor_account va on va.vendoraccid = d.transporter_name
                                left join warehouse w on w.warehouse_id = d.warehouse_location_name
                                left join salesorder_dit sd on sd.salesorder_dit_id = d.so_number
                                left join opp_payment_terms opt on opt.payment_terms_id = sd.customer_payment_terms
                                left join contacts cn on cn.contacts_id = d.material_receiver_name
                                left join vendor_locations vls on vls.vendorloc_id = d.vendor_location_name
                                left join vendor_account va2 on va2.vendoraccid = d.vendor_name
                        WHERE d.deliverychallan_id = :dcid
                    ")->bindValue(":dcid", $dcid);
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
     public function actionGetproductdetail()
    {
        $dcid = Yii::$app->request->get('dcid');
        $connection = Yii::$app->db;
        $prod_command = $connection->createCommand("
                         SELECT dp.*,product_dit.product_description as prod_name  
                         FROM deliverychallandit_product_details dp
                         join product_dit on product_dit.productdit_id = dp.poduct_description
                         WHERE  deliverychallan_id = :dcid;
                    ")->bindValue(":dcid", $dcid);     

        $prod_columns = $prod_command->queryAll();
        if (!empty($prod_columns)) {
            return $this->asJson([
                'status' => 'success',
                'product_details' => $prod_columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Info found.',
                'data' => ''
            ]);
        }
    }
   
    public function actionGeneratepdf($Record)
    {
       

        /*$sql = "select invoicedit.*,dc.so_number,dc.deliverychallan_no,dc.delivery_challan_date,dc.dc_eway_bill_number,dc.company_gstin,state.state_value as state_name,c.first_name,c.last_name,
        vl.state as warehouse_state,vl.statecode as warehouse_state_code,vl.pincode as warehouse_pin 
        from  `invoicedit` 
        left join delivery_challandit dc on dc.deliverychallan_id = invoicedit.delivery_challan_number
        left join warehouse vl on vl.warehouse_id = dc.delivery_challan_location 
        left join state on state.state_code = dc.state_code
        left join contacts c on c.contacts_id = invoicedit.material_receiver_name
        where invoicedit_id  = :Record";*/
        $sql = "select invoicedit.*,dc.ship_by,dc.so_number,dc.deliverychallan_no,dc.delivery_challan_date,dc.dc_eway_bill_number,dc.company_gstin,state.state_value as state_name,c.first_name,c.last_name,
        vl.state as warehouse_state,vl.statecode as warehouse_state_code,vl.pincode as warehouse_pin,
        vls.vendor_loc_name as vendorlocname,va2.acc_name as vendorname,vl2.state as shipfrom_state,vl2.statecode as shipfrom_statecode,vl2.pincode as shipfrom_pin,
        vl2.warehouse_name as shipfrom_warehousename,vl2.address as shipfrom_address,vl2.gstn as shipfrom_gstin,
        vl2.city as shipfrom_city,vls.vendor_loc_name,vls.address as vendor_address,vls.gstin_no_uin as vendor_gstin,
        vls.pincode as vendor_pincode,vls.state as vendor_state,vls.state_code as vendor_statecode,vls.city as vendor_city
        from  `invoicedit` 
        left join delivery_challandit dc on dc.deliverychallan_id = invoicedit.delivery_challan_number
        left join warehouse vl on vl.warehouse_id = dc.delivery_challan_location 
        left join state on state.state_code = dc.state_code
        left join contacts c on c.contacts_id = invoicedit.material_receiver_name
        left join vendor_locations vls on vls.vendorloc_id = dc.vendor_location_name
        left join vendor_account va2 on va2.vendoraccid = dc.vendor_name
        LEFT JOIN warehouse vl2 ON vl2.warehouse_id = dc.warehouse_location_name
        where invoicedit_id  = :Record";
        $connection = Yii::$app->db;
        $command = $connection->createCommand($sql)->bindValue(":Record", $Record);
        $columns = $command->queryOne();

        

        $sql = "select invoicedit_product_details.*,p.product_name as poduct_description,p.product_description,pu.productdit_uom_value
        from invoicedit_product_details
        left join product_dit p on p.productdit_id = invoicedit_product_details.product_discription 
        left join productdit_uom pu on pu.productdit_uom_id = p.uom
        where invoicedit_id = :Record";
        $connection = Yii::$app->db;
        $command = $connection->createCommand($sql)->bindValue(":Record", $Record);
        $quoteitems = $command->queryAll();
        // echo "<pre>";print_r($columns);die;

         $sales_sql = "
                         SELECT 
                            state_code,
                            state,
                            pin_code, 	salesorder_dit_no,date(createdtime) as sodate
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

        $discount = $columns['discount'];
        $customer_ship_to_name = $columns['customer_ship_name'];
        $customer_ship_to_address = $columns['customer_ship_address'];
        $customer_ship_to_gstin = $columns['customer_ship_gstin'];
        $state_code = $ship_columns['ship_state_code'];
        $state_name = $ship_columns['ship_state'];
        $customer_ship_to_city = $ship_columns['ship_city'];
        $customer_ship_to_pin_code = $ship_columns['ship_pin_code'];

        $invoicedit_no = $columns['invoicedit_no'];
        $payment_terms  = $columns['payment_terms'];
        $invoice_date = date("d-M-Y", strtotime($columns['invoice_date']));

        $dc_no = $columns['deliverychallan_no'];
        $dc_date = date("d-M-Y", strtotime($columns['delivery_challan_date']));
        $dc_e_way_bill = $columns['dc_eway_bill_number'];
        
        $customer_bill_to_name = $columns['customer_bill_name'];
        $customer_bill_to_address = $columns['customer_bill_address'];
        $customer_bill_to_gstin = $columns['customer_bill_gstin'];
        $customer_bill_to_state_code = $sales_quoteitems['state_code'];
        $customer_bill_to_state_name = $sales_quoteitems['state'];
        $customer_bill_to_pin_code = $sales_quoteitems['pin_code'];
        $so_number = $sales_quoteitems['salesorder_dit_no'];
        $sodate = date("d-M-Y", strtotime($sales_quoteitems['sodate']));

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
        $deshwal_logo = Yii::getAlias('@webroot/thememain/img/dcpdfheader.jpg');
        $logoPath = $deshwal_logo; 
        $tplVars = [
            'logoPath' => $logoPath
        ];

        $pdf = new MyPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Deshwal Waste Management');
        $pdf->SetTitle('DevIT Invoice PDF');
        $pdf->SetSubject('DevIT Invoice PDF');
        $pdf->SetKeywords('TCPDF, PDF, Invoice');
        $pdf->SetMargins(10, 10, 10);  
        $pdf->SetAutoPageBreak(true, 25);
        $pdf->SetFont('dejavusans', '', 9);

        // set font for Unicode symbols (₹)
        $pdf->SetFont('dejavusans', '', 9);
        $pdf->setPrintFooter(true);
        $pdf->SetAutoPageBreak(true, 25); // Reserve 25mm for footer
        
        PdfHeaderFooterHelper::setupPdfWithTemplate(
            $pdf,
            (int)$this->TabId,      
            $tplVars,
            'invoice_devit',      
            false
        );
        $pdf->AddPage();
        $pdf->drawHeaderContent();
        // $pdf->drawStamp();
        // logo path
        $logo = K_PATH_IMAGES . 'header.png';
        
        $html = $html = <<<EOD
        <table style="font-weight:bold;font-size:14px;text-align:center;" cellpadding="6">
         <tr>
           <td width="100%">
               <table>
                       <tr>
                       <td style="text-align:center;">
                           Tax Invoice
                       </td>
                       
                   </tr>
                   
               </table>
             
           </td>
         </tr>
        </table>
        <table cellpadding="4" cellspacing="0" border="1" width="100%">
            <tr>
                <td rowspan="2" width="50%" style="vertical-align:top;">Bill From : <br/><strong>Dev IT Serv Private Limited</strong><br>3rd Floor, Plot No-79, Bluemonk House<br>Jaipur Road, Sector 34,<br>Gurugram-122001<br>GSTIN/UIN: 06AACCD2388K1ZH<br>State Name : Haryana, Code : 06</td>
                <td width="25%" style="text-align:right;">Invoice No.<br/><strong>$invoicedit_no</strong>
                </td>
                <td width="25%" style="text-align:right;">Dated<br/><strong>$invoice_date</strong>
                </td>
            </tr>
            <tr>
              
                <td style="text-align:right;">Delivery Note</td>
                <td style="text-align:right;">Mode/Terms of Payment<br/><strong>$payment_terms</strong></td>
            </tr>
             <tr>
                <td width="50%"  style="vertical-align:top;">Ship From : <br/><b>$ship_from_name</b><br/>$ship_loc_name $ship_from_address, $ship_from_pin_code<br/>
                GSTIN/UIN: $ship_from_gstin<br/>
                State Name : $ship_from_state_name, Code : $ship_from_state_code</td>
                <td style="text-align:right;">DC Reference No. & Date<br><strong>$dc_no dt.$dc_date</strong></td>
                <td style="text-align:right;">E-Way Bill Number <br/><strong>$dc_e_way_bill</strong></td>
            </tr>
         
        </table>




        <table cellpadding="4" cellspacing="0" border="1" width="100%">
            <tr>
            <td width="50%" rowspan="3" style="vertical-align:top;">Consignee (Ship to)<br/><b>$customer_bill_to_name</b><br/>
            $customer_ship_to_address, $customer_ship_to_city, $customer_ship_to_pin_code<br/>
            GSTIN/UIN : $customer_ship_to_gstin<br/>
            State Name : $state_name, Code : $state_code
            </td>
            <td width="25%" style="text-align:right;">Buyer's Order No.<br><strong>$so_number</strong></td>
            <td width="25%" style="text-align:right;">Dated<br><strong>$sodate</strong></td>
            </tr>
            <tr>
            <td style="text-align:right;">Dispatch Doc No.<br></td>
            <td style="text-align:right;">Delivery Note Date<br></td>
            </tr>
            <tr>
            <td style="text-align:right;">Dispatched through<br></td>
            <td style="text-align:right;">Destination<br></td>
            </tr>
            <tr>
             <td width="50%" style="vertical-align:top;">Buyer (Bill to)<br/><b>$customer_bill_to_name</b><br/>
                $customer_bill_to_address, $customer_bill_to_pin_code<br/>
                GSTIN/UIN : $customer_bill_to_gstin<br/>
                State Name : $customer_bill_to_state_name, Code : $customer_bill_to_state_code<br/>
                Place of Supply : $customer_bill_to_state_name
                </td>
                <td colspan="2" style="text-align:right;">Receiver Name<br><strong>Contact Detail</strong><br><strong>$material_receiver_name $material_receiver_contact_number</strong>


                </td>
            
            </tr>
        </table>


       

        <table cellpadding="4" cellspacing="0" border="1" width="100%" style="font-size:7px">
        <tr>
          <td width="5%" style="border: 0.1mm solid #000;text-align:center;font-weight:bold;">SNo.</td>
          <td width="26%" style="border: 0.1mm solid #000;text-align:center;font-weight:bold;">Description of Goods</td>
          <td width="11%" style="border: 0.1mm solid #000;text-align:center;font-weight:bold;">HSN/SAC</td>
          <td width="8%" style="border: 0.1mm solid #000;text-align:center;font-weight:bold;">GST Rate</td>
          <td width="10%" style="border: 0.1mm solid #000;text-align:center;font-weight:bold;">Quantity</td>
          <td width="12%" style="border: 0.1mm solid #000;text-align:center;font-weight:bold;">Rate</td>
          <td width="4%" style="border: 0.1mm solid #000;text-align:center;font-weight:bold;">Per</td>
          <td width="10%" style="border: 0.1mm solid #000;text-align:center;font-weight:bold;">Discount %</td>
          <td width="14%" style="border: 0.1mm solid #000;text-align:center;font-weight:bold;">Amount</td>
        </tr>
EOD;
$i = 1;
 $total_qty = 0;$gst_amount_=0;$total_gst_rate =0.00;$total_unit_amount = 0.00;$total_amount_for_tax = 0.00;
foreach ($quoteitems as $value) {
    if($i == 1)
        $amtinwords = $columns['total_invoice_amount_word'];
    $productid = $value['poduct_description'];
    $product_desc = $value['product_description'];
    // $working_condition = $value['working_conditions'];
    $hsn_code = $value['product_hsn'];
    $gstrate = $value['gst_rate'];
    $quantity = $value['product_qty'];
    $rate = number_format($value['unit_price'], 2);;
    $per = $value['productdit_uom_value'];
    $discount_age = $value['discount_age'];
    $total_amount = number_format($value['total_amount'], 2);
    $total_qty = $total_qty + $quantity;


    $gst_rate_ = (float)$value['gst_rate'];
    $total_gst_rate = $total_gst_rate + $gst_rate_;
    $total_unit_amount = $total_unit_amount + $value['unit_price'];
    $total_amount_for_tax = $total_amount_for_tax + $value['total_amount'];
    if($customer_bill_to_state_code == $state_code)
    {
        $gst_amount_ = $gst_amount_ + ($value['gst_amount']/2);
    }
    else
    {
        $gst_amount_ = $gst_amount_ + $value['gst_amount'];
    }
    $html .= <<<EOD
        <tr style="">
         <td style="border: 0.1mm solid #000;text-align:right;">$i</td>
         <td style="border: 0.1mm solid #000;">$productid<br/>$product_desc</td>
         <td style="border: 0.1mm solid #000;text-align:right;">$hsn_code</td>
         <td style="border: 0.1mm solid #000;text-align:right;">$gstrate %</td>
         <td style="border: 0.1mm solid #000;text-align:right;font-weight:bold;"><b>$quantity $per</b></td>
         <td style="border: 0.1mm solid #000;text-align:right;">$rate</td>
         <td style="border: 0.1mm solid #000;text-align:right;">$per</td>
         <td style="border: 0.1mm solid #000;text-align:right;">$discount_age</td>
         <td style="border: 0.1mm solid #000;text-align:right;">$total_amount</td>
        </tr>
EOD;
    $i++;
}
$cgst_in_per_ = $sgst_in_per_ = $total_gst_rate / 2;
$cgst_amt_ = ($total_amount_for_tax * $cgst_in_per_) / 100;
$sgst_amt_ = ($total_amount_for_tax * $sgst_in_per_) / 100;
$cgst_amt_in_format = number_format($cgst_amt_,2);
$sgst_amt_in_format = number_format($sgst_amt_,2);
$gstin_format = number_format($gst_amount_,2);
if($customer_bill_to_state_code == $state_code)
{
$html .= <<<EOD
            <tr>
            <td style="border: 0.1mm solid #000;text-align:right;"></td>
            <td style="border: 0.1mm solid #000;text-align:right;font-weight:bold;"><b>Output CGST<br/>Output SGST</b><br/><br/><br/><br/></td>
            <td style="border: 0.1mm solid #000;text-align:right;"></td>
            <td style="border: 0.1mm solid #000;text-align:right;"></td>
            <td style="border: 0.1mm solid #000;text-align:right;"></td>
            <td style="border: 0.1mm solid #000;text-align:right;"></td>
            <td style="border: 0.1mm solid #000;text-align:right;"></td>
            <td style="border: 0.1mm solid #000;text-align:right;"></td>
            <td style="border: 0.1mm solid #000;text-align:right;font-weight:bold;"><b>$gstin_format<br/>$gstin_format</b></td>
            </tr>
EOD;
}
else
{
$html .= <<<EOD
            <tr>
            <td style="border: 0.1mm solid #000;text-align:right;"></td>
            <td style="border: 0.1mm solid #000;text-align:right;font-weight:bold;"><b>Output IGST</b><br/><br/><br/><br/></td>
            <td style="border: 0.1mm solid #000;text-align:right;"></td>
            <td style="border: 0.1mm solid #000;text-align:right;"></td>
            <td style="border: 0.1mm solid #000;text-align:right;"></td>
            <td style="border: 0.1mm solid #000;text-align:right;"></td>
            <td style="border: 0.1mm solid #000;text-align:right;"></td>
            <td style="border: 0.1mm solid #000;text-align:right;"></td>
            <td style="border: 0.1mm solid #000;text-align:right;font-weight:bold;"><b>$gstin_format</b></td>
            </tr>
EOD;
}
if($customer_bill_to_state_code == $state_code)
{
     $total_with_tax  = $total_amount_for_tax + ($gst_amount_*2) ;
}
else
{
     $total_with_tax  = $total_amount_for_tax + $gst_amount_ ;
}
$total_with_tax -=  $discount;
$total_with_tax  = number_format($total_with_tax,2);


$discount = number_format($discount,2);       
$html .= <<<EOD
     <tr style="">
        <td style="text-align:right;"></td>
        <td style="text-align:right;">Discount</td>
        <td style="border: 0.1mm solid #000;text-align:right;"></td>
        <td style="border: 0.1mm solid #000;text-align:right;"></td>
        <td style="border: 0.1mm solid #000;text-align:right;font-weight:bold;"><b></b></td>
        <td style="border: 0.1mm solid #000;text-align:right;"></td>
        <td style="border: 0.1mm solid #000;text-align:right;"></td>
        <td style="border: 0.1mm solid #000;text-align:right;"></td>
        <td style="border: 0.1mm solid #000;text-align:right;font-weight:bold;"><b>$discount</b></td>
        </tr>


        <tr style="">
        <td style="border: 0.1mm solid #000;text-align:right;"></td>
        <td style="text-align:right;">Total</td>
        <td style="border: 0.1mm solid #000;text-align:right;"></td>
        <td style="border: 0.1mm solid #000;text-align:right;"></td>
        <td style="border: 0.1mm solid #000;text-align:right;font-weight:bold;"><b>$total_qty $per</b></td>
        <td style="border: 0.1mm solid #000;text-align:right;"></td>
        <td style="border: 0.1mm solid #000;text-align:right;"></td>
        <td style="border: 0.1mm solid #000;text-align:right;"></td>
        <td style="border: 0.1mm solid #000;text-align:right;font-weight:bold;"><b>₹ $total_with_tax</b></td>
        </tr>
        <tr>    
            <td colspan="8" style="text-align:left;">Amount Chargeable (in words): <br/><b>INR $amtinwords</b></td>
        </tr>
       <tr style="text-align:center;font-weight:bold;font-size:7px">
            <td rowspan="2" width="46%" style="font-weight:bold;">HSN/SAC</td>
            <td rowspan="2" width="10%" style="font-weight:bold;">Taxable Value</td>
EOD;
if($customer_bill_to_state_code == $state_code)
{
$html .=<<<EOD
            <td colspan="2" width="17%" style="font-weight:bold;">CGST</td>
            <td colspan="2" width="17%" style="font-weight:bold;">SGST/UTGST</td>
EOD;
}
else
{
$html .=<<<EOD
            <td colspan="2" width="34%" style="font-weight:bold;">IGST</td>
EOD;
}
$html .=<<<EOD
            <td rowspan="2" width="10%" style="font-weight:bold;">Total Tax Amount</td>
        </tr>
EOD;
if($customer_bill_to_state_code == $state_code)
{
$html .=<<<EOD
                    <tr style="text-align:center;font-weight:bold;">
                    <td width="8%">Rate</td>
                    <td width="9%">Amount</td>
                    <td width="8%">Rate</td>
                    <td width="9%">Amount</td>
                </tr>
EOD;
}
else
{
$html .=<<<EOD
                    <tr style="text-align:center;font-weight:bold;">
                    <td width="16%">Rate</td>
                    <td width="18%">Amount</td>
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
    $gstamount = (float)$value['gst_amount']/2;
    $unit_price = (float)$value['unit_price'];
    $rate = number_format($value['total_amount'], 2);//number_format($unit_price, 2);
    $cgst_amt = (($unit_price * $quantity) * $cgst_in_per) / 100;
    $sgst_amt = (($unit_price * $quantity) * $sgst_in_per) / 100;
    $total_tax_amt = $cgst_amt + $sgst_amt;
    // Accumulate totals (keep as float)
    $all_total_taxable += $value['total_amount'];
    $all_cgst_amt += $gstamount;
    $all_sgst_amt += $gstamount;
    $all_tax_amt  += $total_tax_amt;
    
    // Format for display
    $cgst_amt_f = number_format($gstamount, 2);
    $sgst_amt_f = number_format($gstamount, 2);
    if($customer_bill_to_state_code == $state_code)
    { 
        $total_tax_amt_f = number_format($gstamount+$gstamount, 2);
         $html .= <<<EOD
                    <tr style="text-align:center;">
                        <td style="text-align:left;">$hsn_code1</td>
                        <td style="text-align:right;">$rate</td>
                        <td style="text-align:right;">{$cgst_in_per}%</td>
                        <td style="text-align:right;">$cgst_amt_f</td>
                        <td style="text-align:right;">{$sgst_in_per}%</td>
                        <td style="text-align:right;">$sgst_amt_f</td>
                        <td style="text-align:right;">$total_tax_amt_f</td>
                    </tr>
EOD;
    }
    else
    { $total_tax_amt_f = number_format(($gstamount*2), 2);
        $html .= <<<EOD
                    <tr style="text-align:center;">
                        <td style="text-align:left;">$hsn_code1</td>
                        <td style="text-align:right;">$rate</td>
                        <td style="text-align:right;">{$gst_rate}%</td>
                        <td style="text-align:right;">{$value["gst_amount"]}</td>
                        <td style="text-align:right;">$total_tax_amt_f</td>
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
if($customer_bill_to_state_code == $state_code)
{
$html .=<<<EOD
                    <tr style="text-align:right;font-weight:bold;">
                        <td><strong>Total</strong></td>
                        <td style="text-align:right;"><strong>$all_total_taxable_f</strong></td>
                        <td colspan="2" style="text-align:right;"><strong>$all_cgst_amt_f</strong></td>
                        <td colspan="2" style="text-align:right;"><strong>$all_sgst_amt_f</strong></td>
                        <td style="text-align:right;"><strong>$all_tax_amt_f</strong></td>
                    </tr>
EOD;
        $colspan_ = 7;
}
else
{
$html .=<<<EOD
                    <tr style="text-align:right;font-weight:bold;">
                        <td><strong>Total</strong></td>
                        <td style="text-align:right;"><strong>$all_total_taxable_f</strong></td>
                        <td colspan="2" style="text-align:right;"><strong>$all_igst_amt</strong></td>
                        <td style="text-align:right;"><strong>$all_tax_amt_f</strong></td>
                    </tr>
EOD;
        $colspan_ = 5;
}


$html .= <<<EOD
        
        </table>
        <table style="border: 0.1mm solid #000;" width="100%">
        <tr>
            <td colspan="$colspan_">Tax Amount (in words) :<b> INR $all_tax_amt_in_word Only</b><br/>
              
                 
            </td>
        </tr>
        <tr>
            <td width="50%">&nbsp;</td>
            <td width="50%">
            <table>
                    <tr>
                    <td colspan="2">Company's Bank Details</td>
                    </tr>
                    <tr>
                    <td width="40%">A/c Holder's Name </td><td width="2%">:</td><td width="58%" style="font-weight:bold;"><strong>Dev IT Serv Private Limited</strong></td>
                    </tr>
                    <tr>
                    <td>Bank Name </td><td>:</td><td style="font-weight:bold;"><strong>Dev IT Serv Private Limited</strong></td>
                    </tr>
                    <tr>
                    <td>A/c No. </td><td>:</td><td style="font-weight:bold;"><strong>17182790000011</strong></td>
                    </tr>
                    <tr>
                    <td>Branch & IFS Code </td><td>:</td><td style="font-weight:bold;"><strong>HDFC0003998</strong></td>
                    </tr>
                    
            </table>
         
            </td>
        </tr>
        <tr>
            <td>Company's PAN     :   <b> AACCD2388K</b>
            <br><span style="font-size:8px;"><u>Declaration</u><br>We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct.</span>
            </td>
            <td>
                     <table style="border: 0.1mm solid #000;">
                        <tr>
                                <td style="text-align:right;font-weight:bold;"><strong>for Dev IT Serv Private Limited</strong><br><br><br>Authorised Signatory</td>
                        </tr>
                    </table>
            </td>
        </tr>
       
       
        </table>


        
        <p style="text-align:center;font-size:8px;">This is a Computer Generated Document</p>


EOD;

        // echo $html;die;

        $pdf->writeHTML($html, true, false, true, false, '');
        $filename = $invoicedit_no . '_invoice_devit_' . $todaydate . '.pdf';
        $pdf->Output($filename, 'I');
    }


  
}
