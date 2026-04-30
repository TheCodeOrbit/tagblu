<?php

namespace backend\modules\salesorderdit\controllers;

use common\components\MyPDF;
use common\components\PdfHeaderFooterHelper;
use common\controllers\ModuleController;
use common\components\TcpdfHelper;
use DateTime;

use Yii;
/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'multiple';
    public $ModuleName = 'salesorderdit';
    public $FieldId = 'salesorder_dit_id';
    public $TableName = 'salesorder_dit';
    public $TabLabel = 'DevIT Sales Order';


    public $TabId = '74';
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


    public function actionGetproductdetail()
    {
        $data = $_POST;
        $quote_name = Yii::$app->request->post('quote_name');
        $connection = Yii::$app->db;

       //auto fill product_description and oem part no as per V11 - point no 36 code added by ptptale on date 11-10-2025

//         $sql = "SELECT opd.*,
//        od.days_value AS add_product_delivery_timeline,
//        DATE_FORMAT(oppd.add_price_validity, '%d-%m-%Y') AS add_price_validity,
//        product_dit.product_name AS prod_name,
//        product_dit.product_description AS prod_description,
//        product_dit.oem_part_number AS prod_oem_part_number
// FROM quotesdit_product_detail opd
// JOIN quotes_dit qd ON qd.quotes_dit_id = opd.quotes_dit_id
// JOIN product_dit ON product_dit.productdit_id = opd.product_name
// LEFT JOIN opportunity_product_detail oppd 
//        ON oppd.opportunity_id = qd.opportunity_name 
//       AND oppd.product_name = opd.product_name
// LEFT JOIN oppr_days od 
//        ON od.days_id = oppd.add_product_delivery_timeline
// WHERE opd.quotes_dit_id = :quote_name;";


         $sql = "SELECT 
          opd.*,
          (
            SELECT od.days_value
            FROM opportunity_product_detail oppd2
            JOIN oppr_days od 
              ON od.days_id = oppd2.add_product_delivery_timeline
            WHERE oppd2.opportunity_id = qd.opportunity_name
              AND oppd2.product_name   = opd.product_name
            ORDER BY oppd2.opportunity_product_detail_id DESC
            LIMIT 1
          ) AS add_product_delivery_timeline,
          DATE_FORMAT((
            SELECT oppd3.add_price_validity
            FROM opportunity_product_detail oppd3
            WHERE oppd3.opportunity_id = qd.opportunity_name
              AND oppd3.product_name   = opd.product_name
            ORDER BY oppd3.opportunity_product_detail_id DESC
            LIMIT 1
          ), '%d-%m-%Y') AS add_price_validity,
          product_dit.product_name        AS prod_name,
          product_dit.product_description AS prod_description,
          product_dit.oem_part_number     AS prod_oem_part_number
        FROM quotesdit_product_detail opd
        JOIN quotes_dit qd 
          ON qd.quotes_dit_id = opd.quotes_dit_id
        JOIN product_dit 
          ON product_dit.productdit_id = opd.product_name
        WHERE opd.quotes_dit_id = :quote_name";
        $command = $connection->createCommand($sql)->bindValue(":quote_name", $quote_name);
        $columns = $command->queryAll();
         //code addded by ptpatel on date 16-10-2025 for v11 - 19
        // Loop by reference to allow modification/removal
        foreach ($columns as $key => &$rows) {
            // echo "<pre>";print_r($rows);
            $product_name = $rows['product_name'];
            $quote_qty = (float) $rows['qty']; // Make sure it's treated as a number
            $remaining_qty = $quote_qty;
            // Check if SO is created for this quote and product
            $sql_chk = "SELECT sum(qty) as qty
                        FROM salesorder_dit so
                        LEFT JOIN salesorderdit_product_details spd ON spd.salesorder_dit_id = so.salesorder_dit_id
                        WHERE so.quote_name = :quote_name  AND spd.product_name = :product_name";

            $cmd = $connection->createCommand($sql_chk)
                ->bindValue(":quote_name", $quote_name)
                ->bindValue(":product_name", $product_name);

            $chkcolumns = $cmd->queryOne();
            if ($chkcolumns) {            
                $ordered_qty = isset($chkcolumns['qty']) ? (float)$chkcolumns['qty'] : 0;              
                $remaining_qty = $quote_qty - $ordered_qty;
                // echo $remaining_qty.' '.$quote_qty.' '.$ordered_qty.' '.$chkcolumns['qty'];
            }
            if ($remaining_qty <= 0) {
                // Remove the row if no remaining quantity
                unset($columns[$key]);
            } else {
                // Add the remaining quantity
                $rows['remaining_qty'] = $remaining_qty;
            }
            $rows['quotesdit_qty'] = $rows['qty'];
            unset($rows['qty']);
            // die;
        }
        unset($rows); // Unset reference variable (good practice)

        // Optional: Reindex the array if needed
        $columns = array_values($columns);
        // echo "<pre>";print_r($columns);die;
        //end code added by ptpatel on date 16-101-2025 for v11 - 19
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Product found.',
                'data' => ''
            ]);
        }

    }

    public function actionGetshipaddress()
    {
        $data = $_POST;
        $quote_name = Yii::$app->request->post('quote_name');
        $connection = Yii::$app->db;

       
        $sql = "select opd.*,vendor_locations.vendor_loc_name,vendor_account.pan_num as pan,ct.city_name,vendor_locations.pincode from quotesdit_ship_detail opd 
        join quotes_dit  on quotes_dit.quotes_dit_id = opd.quotes_dit_id
        join vendor_locations on vendor_locations.vendorloc_id = opd.ship_to_location
        left join city ct on ct.cityid = vendor_locations.city
        join vendor_account on vendor_account.vendoraccid = quotes_dit.account_name
        where opd.quotes_dit_id=:quote_name";
        $command = $connection->createCommand($sql)->bindValue(":quote_name", $quote_name);
        $columns = $command->queryAll();
        // print_r($columns);die;
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Product found.',
                'data' => ''
            ]);
        }

    }
     public function actionGettotalamount()
    {
        $data = $_POST;
        $quote_name = Yii::$app->request->post('quote_name');
        $connection = Yii::$app->db;

       
        $sql = "select cgst_amount,sgst_amount,igst_amount,sub_total,grand_total,amount_in_words,margin  from quotes_dit where quotes_dit_id=:quote_name";
        $command = $connection->createCommand($sql)->bindValue(":quote_name", $quote_name);
        $columns = $command->queryOne();
        // print_r($columns);die;
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Product found.',
                'data' => ''
            ]);
        }

    }

    
     public function actionGetbilladdress()
    {
        $data = $_POST;
        $deal_name = Yii::$app->request->post('deal_name');
        $connection = Yii::$app->db;

       
        
        $sql = "select (select SUM(COALESCE(sales_price, 0) - COALESCE(cost_price, 0)) from  opportunity_product_detail where opportunity_id=:deal_name) as margin_percentage,opd.product_category,concat(contacts.first_name,' ',contacts.last_name) as contact,acc_name as vendor,opd.vendor_account_name,opd.zone_region,opd.team_name,requester_customer_name,warehouse_loc_business_entity,bill_from_location,bill_from_address,bill_from_state,bill_from_state_code,bill_location,bill_address,bill_state,bill_state_code,vendor_account.pan_num as pan_number,opd.bill_gstin_no,vl2.vendor_loc_name as bill_location_name,warehouse_name,
        (select sum(gross_profit) from  opportunity_product_detail where opportunity_id=:deal_name) as gross_profit,
       
        (select avg(margin_percentage) from  opportunity_product_detail where opportunity_id=:deal_name) as margin,
        vendor_account.legal_entity,vl2.pincode,ct.city_name,customer_po_num,customer_payment_terms,DATE_FORMAT(customer_po_date, '%d-%m-%Y') AS customer_po_date ,DATE_FORMAT(po_received_date, '%d-%m-%Y') AS po_received_date   from opportunity opd 
        join vendor_locations vl2 on vl2.vendorloc_id = opd.bill_location
        left join city ct on ct.cityid = vl2.city
        join warehouse on warehouse.warehouse_id = opd.warehouse_loc_business_entity
        join vendor_account on vendor_account.vendoraccid = opd.vendor_account_name
        join contacts on contacts.contacts_id = opd.requester_customer_name
        where opd.opportunity_id=:deal_name";
        $command = $connection->createCommand($sql)->bindValue(":deal_name", $deal_name);
        $columns = $command->queryOne();
        // print_r($columns);die;
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Product found.',
                'data' => ''
            ]);
        }

    }
     public function actionGetcontacts()
    {
        $data = $_POST;
        $contacts_id = Yii::$app->request->post('contacts_id');
        $connection = Yii::$app->db;

       
        
        $sql = "select cdesignation_value as designation,mobile,email from contacts  LEFT JOIN cdesignation on cdesignation.cdesignationid=contacts.designation where contacts_id =:contacts_id";
        $command = $connection->createCommand($sql)->bindValue(":contacts_id", $contacts_id);
        $columns = $command->queryOne();
        // print_r($columns);die;
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Product found.',
                'data' => ''
            ]);
        }

    }
    public function actionGeneratepdf($Record)
    {
        $sql = "select ct.city_name ,vl.vendor_loc_name,vendoraccid,salesorder_dit.*,va.acc_name,concat(contacts.first_name,' ',contacts.last_name) as contact_name from  `salesorder_dit` 
        left join vendor_account va on va.vendoraccid = salesorder_dit.account_name
        left join vendor_locations vl on vl.vendorloc_id = salesorder_dit.delivery_location
        left join city ct on ct.cityid = vl.city
        left join contacts on contacts.contacts_id = salesorder_dit.requester_name_contact_name
        where salesorder_dit_id = :Record";
        $connection = Yii::$app->db;
        $command = $connection->createCommand($sql)->bindValue(":Record", $Record);
        $columns = $command->queryOne();

        $sql = "select product_dit.product_name as p_product_name,product_dit.product_description,salesorderdit_product_details.* from salesorderdit_product_details
        join product_dit on product_dit.productdit_id = salesorderdit_product_details.product_name
        where salesorder_dit_id = :Record";
        $connection = Yii::$app->db;
        $command = $connection->createCommand($sql)->bindValue(":Record", $Record);
        $quoteitems = $command->queryAll();

        //get shipping detail
        $sql = "select salesorderdit_ship_to_address.*, vl.vendor_loc_name from  `salesorderdit_ship_to_address` 
        left join vendor_locations vl on vl.vendorloc_id = salesorderdit_ship_to_address.ship_delivery_location
        left join vendor_account va on va.vendoraccid = vl.vendor_account
        where salesorder_dit_id = :Record limit 1";
        $connection = Yii::$app->db;
        $command = $connection->createCommand($sql)->bindValue(":Record", $Record);
        $shipcolumns = $command->queryOne();
        $ship_delivery_location = $shipcolumns['vendor_loc_name'];
        $ship_address = $shipcolumns['ship_address'];
        $ship_city = $shipcolumns['ship_city'];
        $ship_state = $shipcolumns['ship_state'];
        $ship_pin_code = $shipcolumns['ship_pin_code'];
        $ship_state_code = $shipcolumns['ship_state_code'];
        $ship_gst = $shipcolumns['ship_gst'];
        $ship_pan = $shipcolumns['ship_pan'];

        ///////get deshwal ISR
        $sql_v = "select concat(u.first_name,' ',u.last_name) as deshwal_spocname,u.email,u.mobile from vendor_account_orgaisation_section va join user u on va.userid = u.id where va.vendoraccid = :vendoraccid";
        $command = $connection->createCommand($sql_v)->bindValue(":vendoraccid", $columns['vendoraccid']);
        // echo $columns['vendoraccid'];die;
        $colv = $command->queryOne();
        if ($colv) {
            $deshwal_spocname = $colv['deshwal_spocname'] ?? '';
            $email = $colv['email'] ?? '';
            $mobile = $colv['mobile'] ?? '';
        } else {
            $deshwal_spocname = '';
            $email = '';
            $mobile = '';
        }

        $deshwal_logo = Yii::getAlias('@webroot/thememain/img/dcpdfheader.jpg');

        $record_id = $Record;//"74";//Yii::$app->request->post('Record');

        // Company Information
        $todaydate = date("M d,Y");
        $sodate = date("d/m/Y", strtotime($columns['createdtime']));
        // $expiry_date = date("M d,Y", strtotime($columns['expiry_date']));
        $terms_and_condition = nl2br($columns['terms_and_condition']);
        $acc_name = $columns['acc_name'];
        $contact_name = $columns['contact_name'];
        $salesorder_dit_no = $columns['salesorder_dit_no'];
        $bill_to_legal_name = $columns['vendor_loc_name'];
        $bill_to_address = nl2br($columns['address']);
        $bill_city = $columns['city_name'];
        $bill_state = $columns['state'];
        $customer_po_num = $columns['customer_po_num'];




        $bill_pincode = $columns['pin_code'];
        $bill_gstin_no_uin = $columns['gst'];
        // $warehouse_address = $columns['warehouse_address'];
        // $warehouse_city = $columns['warehouse_city'];
        // $warehouse_state = $columns['warehouse_state'];
        // $warehouse_pincode = $columns['warehouse_pincode'];
        // $warehouse_gstin_no = $columns['warehouse_gstin_no'];
        $basic_cp = (number_format($columns['basic_amount'], 2) ?? 0);
        $total_cgst_amount = (number_format($columns['cgst_amount'], 2) ?? '-');
        $total_sgst_amount = (number_format($columns['sgst_amount'], 2) ?? '-');
        $total_igst_amount = (number_format($columns['igst_amount'], 2) ?? '-');
        $total_amount_main = (number_format($columns['grand_total'], 2) ?? '0');

        $total_cgst_amount = ($total_cgst_amount !='-')?(number_format($columns['cgst_amount'], 2)) :'-';
        $total_sgst_amount = ($total_sgst_amount !='-')?(number_format($columns['sgst_amount'], 2)) :'-';
        $total_igst_amount = ($total_igst_amount !='-')?(number_format($columns['igst_amount'], 2)) :'-';
        
        $amtinwords = $this->numberToWords($columns['grand_total']);

       

        $warehouse_address = '';
        $warehouse_city = '';
        $warehouse_state = '';
        $warehouse_pincode = '';
        $warehouse_gstin_no = '';


        // Set image path (make sure the image path is accessible by TCPDF)
        $logoPath = $deshwal_logo; // Place header.png in TCPDF's images directory
        $tplVars = [
            'logoPath' => $logoPath
        ];

        // create new PDF document
        $pdf = new MyPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Deshwal Waste Management');
        $pdf->SetTitle('DevIT Sales Order');
        $pdf->SetSubject('DevIT Sales Order PDF');
        $pdf->SetKeywords('TCPDF, PDF, Sales Order');

        // set header and footer off
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // set margins
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(TRUE, 10);

        // set font for Unicode symbols (₹)
        $pdf->SetFont('dejavusans', '', 9);
        // $pdf->SetFont('freeserif', '', 9);
        // $pdf->SetFont('helvetica', '', 9);
        $pdf->setPrintFooter(true);
        $pdf->setPrintHeader(true);
        $pdf->SetAutoPageBreak(true, 25); // Reserve 25mm for footer
        PdfHeaderFooterHelper::setupPdfWithTemplate(
            $pdf,
            (int)$this->TabId,      
            $tplVars,
            'salesorder_dit',      
            false
        );
        // add a page
        $pdf->AddPage();
        $pdf->drawHeaderContent();
        // $pdf->drawStamp();

        // logo path
        $logo = K_PATH_IMAGES . 'header.png';

        $html = <<<EOD
        
        <table style="">
          <tr>
          
            <td width="100%" align="right" >
              <p style="padding-top:10px"><span style="font-size:25pt;font-weight:600">Sales Order</span><br><span style="font-size:8pt;font-weight:bold">Sales Order#$salesorder_dit_no</span></p>
            </td>
          </tr>
        </table>
        
        <br>
        
        <table cellpadding="4" cellspacing="0" border="0">
        <tr>
        <td width="50%"><p><strong>Dev IT Serv Private Limited</strong><br>3rd Floor, Plot No. 79,<br>Bluemonk House, Sector-34<br>Gurugram Haryana 122001<br>India<br>GSTIN 06AACCD2388K1ZH<br>LUT Number AD060222011578</p></td>
        <td>&nbsp;
        </td>
        </tr>
        </table>
        <br><br>
         <table cellpadding="4" cellspacing="0" border="0">
        <tr>
        <td width="50%"><p>Bill To<br><strong>$bill_to_legal_name</strong><br>$bill_to_address
        <br>$bill_city, $bill_state, $bill_pincode<br>GSTIN $bill_gstin_no_uin<br>PAN:-
        </p>
        </td>
        <td>&nbsp;
        </td>
        </tr>
        </table>
        
         <br><br>
         <table cellpadding="4" cellspacing="0" border="0">
        <tr>
        <td width="50%"><p>Ship To<br><strong>$ship_delivery_location</strong><br>$ship_address
        <br>$ship_city, $ship_state, $ship_pin_code<br>Contact Person : Mr. Aman : 93542
        53076<br>GSTIN $ship_gst<br>PAN:- $ship_pan
        </p>
        </td>


         <td width="50%"><br>
         <table  style="font-size:11px">
            <tr>
            <td width="60%" style="text-align:right;">Order Date :</td><td width="40%" style="text-align:right;">$sodate</td>
            </tr>
            <tr>
            <td style="text-align:right;">Customer Po Number :</td><td style="text-align:right;">$customer_po_num</td>
            </tr>
         </table>
        
        </td>
        </tr>
        <tr>
        <td><br>Place Of Supply: $ship_state ($ship_state_code)</td>
        </tr>
        </table>
        <br>
        <br>
        <table border="0" cellpadding="8" cellspacing="0" style="font-size:9px">
        <tr style="background-color:#3c3d3a;color:#fff; font-weight:bold;">
          <td width="5%">#</td>
          <td width="40%">Item & Description</td>
          <td width="12%">HSN/SAC</td>
          <td width="12%" style="text-align:center;">Qty</td>
          <td width="13%" style="text-align:right;">Rate</td>
          <td width="18%" align="right">Amount</td>
        </tr>
EOD;
        $i = 1;
        $subtotal = 0;
        foreach ($quoteitems as $value) {


            $productid = $value['p_product_name'];
            $product_description = $value['product_description'];
            // $working_condition = $value['working_conditions'];
            $hsn_code = $value['hsn_code'];
            $cost_price = number_format($value['basic_price'], 2);
            $quantity = $value['qty'];


            $cgst_percent = ($value['cgst_per'] == 0) ? '-' : $value['cgst_per'];
            $sgst_percent = ($value['sgst_per'] == 0) ? '-' : $value['sgst_per'];
            $igst_percent = ($value['igst_per'] == 0) ? '-' : $value['igst_per'];
            $subtotal +=$value['basic_price']*$value['qty'];
            $total_amount = number_format($value['basic_price']*$value['qty'], 2);


            $html .= <<<EOD
        <tr>
          <td style="text-align:center; border-bottom:1px solid #b2b2b2">$i</td>
          <td  style="border-bottom:1px solid #b2b2b2"><strong>$productid</strong><br>$product_description</td>
          <td align="right"  style="border-bottom:1px solid #b2b2b2">$hsn_code</td>
          <td align="center"  style="border-bottom:1px solid #b2b2b2">$quantity</td>
          <td align="right"  style="border-bottom:1px solid #b2b2b2">$cost_price</td>


          <td align="right"  style="border-bottom:1px solid #b2b2b2">$total_amount</td>
        </tr>
        EOD;


            $i++;
        }
        $subtotal = number_format($subtotal,2);
        $html .= <<<EOD
        
        </table>
        
        <br>
        <br>


        <table cellpadding="6">
            <tr>
                <td width="65%">&nbsp;</td>
                <td  width="35%">
                    <table cellpadding="5" style="margin-right:2px">
                        <tr>
                            <td width="45%">Sub Total</td><td width="10%">:</td><td style="text-align:right;" width="45%">$subtotal</td>
                            </tr>
                            <tr>
                            <td>CGST</td><td>:</td><td style="text-align:right;">$total_cgst_amount</td>
                            </tr>
                            <tr>
                            <td>SGST</td><td>:</td><td style="text-align:right;">$total_sgst_amount</td>
                            </tr>
                            <tr>
                            <td>IGST</td><td>:</td><td style="text-align:right;">$total_igst_amount</td>
                            </tr>
                            <tr style="background-color:#f5f4f3;font-weight:bold">
                            <td>Total</td><td>:</td><td style="text-align:right;">₹ $total_amount_main</td>
                            
                        </tr>
                    </table>
              
                </td>
            </tr>
        </table>
        <br>
        <br>
        <br>
        <table>
            <tr>    
                <td style="text-align:left;">Authorized Signature __________________________________</td>
            </tr>
        </table>
       
      
        EOD;


        $pdf->writeHTML($html, true, false, true, false, '');
        $filename = $salesorder_dit_no . '_salesorder_devit_' . $todaydate . '.pdf';
        $pdf->Output($filename, 'I');


    }

     public function actionGetdealname()
    {
        $data = $_POST;
        $dealid = Yii::$app->request->post('dealid');
        $connection = Yii::$app->db;
        $sql = "select DATE_FORMAT(po_received_date, '%d-%m-%Y') AS po_received_date,DATE_FORMAT(customer_po_date, '%d-%m-%Y') AS customer_po_date,deal_name as deal_name_auto from opportunity  where opportunity_id=:dealid";
        $command = $connection->createCommand($sql)->bindValue(":dealid", $dealid);
        $columns = $command->queryOne();
        // print_r($columns);die;
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'deal name not found.',
                'data' => ''
            ]);
        }

    }

    

}
