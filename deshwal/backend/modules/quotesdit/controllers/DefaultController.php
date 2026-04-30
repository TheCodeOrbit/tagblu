<?php

namespace backend\modules\quotesdit\controllers;

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
    public $ModuleName = 'quotesdit';
    public $FieldId = 'quotes_dit_id';
    public $TableName = 'quotes_dit';
    public $TabLabel = 'DevIT Quotes';


    public $TabId = '72';
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
        $deal_name = Yii::$app->request->post('deal_name');
        $connection = Yii::$app->db;



        $sql = "select opd.*,product_dit.product_name as prod_name,product_dit.product_description  from opportunity_product_detail opd 
        join opportunity  on opportunity.opportunity_id = opd.opportunity_id
        join product_dit on product_dit.productdit_id = opd.product_name
        where opd.opportunity_id=:deal_name  and reject = 0";
        $command = $connection->createCommand($sql)->bindValue(":deal_name", $deal_name);
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

    public function actionGetshipaddress()
    {
        $data = $_POST;
        $deal_name = Yii::$app->request->post('deal_name');
        $connection = Yii::$app->db;



        $sql = "select opd.*,vendor_locations.vendor_loc_name from opportunity_ship_detail opd 
        join opportunity  on opportunity.opportunity_id = opd.opportunity_id
        join vendor_locations on vendor_locations.vendorloc_id = opd.ship_to_location
        where opd.opportunity_id=:deal_name";
        $command = $connection->createCommand($sql)->bindValue(":deal_name", $deal_name);
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
    public function actionGetbilladdress()
    {
        $data = $_POST;
        $deal_name = Yii::$app->request->post('deal_name');
        $connection = Yii::$app->db;



        $sql = "select (select (SUM(COALESCE(sales_price, 0) - COALESCE(cost_price, 0)))*100 from  opportunity_product_detail where opportunity_id=:deal_name) as margin,opd.product_category,concat(contacts.first_name,' ',contacts.last_name) as contact,acc_name as vendor,opd.vendor_account_name,opd.zone_region,opd.team_name,requester_customer_name,warehouse_loc_business_entity,bill_from_location,bill_from_address,bill_from_state,bill_from_state_code,bill_location,bill_address,bill_state,bill_state_code,opd.pan_number,opd.bill_gstin_no,vl1.vendor_loc_name as bill_from_location_name,vl2.vendor_loc_name as bill_location_name,warehouse_name,
        (select sum(gross_profit) from  opportunity_product_detail where opportunity_id=:deal_name) as gross_profit,
        (select avg(margin_percentage) from  opportunity_product_detail where opportunity_id=:deal_name) as margin_percentage,opd.deal_name,
        opt.payment_terms_value as payment_terms from opportunity opd 
        left join vendor_locations vl1 on vl1.vendorloc_id = opd.bill_from_location
        left join vendor_locations vl2 on vl2.vendorloc_id = opd.bill_location
        left join warehouse on warehouse.warehouse_id = opd.warehouse_loc_business_entity
        left join vendor_account on vendor_account.vendoraccid = opd.vendor_account_name
        left join contacts on contacts.contacts_id = opd.requester_customer_name
        left join opp_payment_terms opt on opt.payment_terms_id = opd.customer_payment_terms
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
    public function actionGeneratepdf($Record)
    {
        $sql = "select ct.city_name ,vl.vendor_loc_name,vendoraccid,quotes_dit.*,va.acc_name,concat(contacts.first_name,' ',contacts.last_name) as contact_name from  `quotes_dit` 
        join vendor_account va on va.vendoraccid = quotes_dit.account_name
        join vendor_locations vl on vl.vendorloc_id = quotes_dit.bill_to_location
        left join city ct on ct.cityid = vl.city
        join contacts on contacts.contacts_id = quotes_dit.requester_name 
        where quotes_dit_id = :Record";
        $connection = Yii::$app->db;
        $command = $connection->createCommand($sql)->bindValue(":Record", $Record);
        $columns = $command->queryOne();

        $sql = "select product_dit.product_name as p_product_name,product_dit.product_description,quotesdit_product_detail.* from quotesdit_product_detail
        join product_dit on product_dit.productdit_id = quotesdit_product_detail.product_name
        where quotes_dit_id = :Record";
        $connection = Yii::$app->db;
        $command = $connection->createCommand($sql)->bindValue(":Record", $Record);
        $quoteitems = $command->queryAll();

        //////////get ship address/////
         $sql = "select quotesdit_ship_detail.* from quotesdit_ship_detail
        where quotes_dit_id = :Record limit 1";
        $connection = Yii::$app->db;
        $command = $connection->createCommand($sql)->bindValue(":Record", $Record);
        $shipadd = $command->queryOne();
        $shiplegalname = $shipadd['ship_legal_name'];
        $ship_address = $shipadd['ship_address'];
        $ship_state = $shipadd['ship_state'];
        $ship_state_code = $shipadd['ship_state_code'];
        $ship_gst = $shipadd['ship_gst'];

        ///////get devit ISR
        $sql_v = "select concat(u.first_name,' ',u.last_name) as deshwal_spocname,u.email,u.mobile from vendor_account_orgaisation_section va join user u on va.userid = u.id where va.vendoraccid = :vendoraccid and  roleid='H59'";
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
        $quote_create_date = date("M d,Y", strtotime($columns['quote_create_date']));
        $expiry_date = date("M d,Y", strtotime($columns['expiry_date']));
        $terms_and_condition = nl2br($columns['terms_and_condition']);
        $acc_name = $columns['acc_name'];
        $contact_name = $columns['contact_name'];
        $quotes_dit_no = $columns['quotes_dit_no'];
        $bill_to_legal_name = $columns['vendor_loc_name'];
        $bill_to_address = nl2br($columns['bill_to_address']);
        $bill_city = $columns['city_name'];
        $bill_state = $columns['bill_to_state'];
        $bill_to_state_code  = $columns['bill_to_state_code'];
        $delivery_terms = $columns['delivery_terms'];
        $payment_terms = $columns['payment_terms'];




        // $bill_pincode = $columns['bill_pincode'];
        $bill_gstin_no_uin = $columns['bill_to_gst'];
        // $warehouse_address = $columns['warehouse_address'];
        // $warehouse_city = $columns['warehouse_city'];
        // $warehouse_state = $columns['warehouse_state'];
        // $warehouse_pincode = $columns['warehouse_pincode'];
        // $warehouse_gstin_no = $columns['warehouse_gstin_no'];
        $basic_cp = "₹ " . (number_format($columns['sub_total'], 2) ?? 0);
        $total_cgst_amount = "₹ " . (number_format($columns['cgst_amount'], 2) ?? 0);
        $total_sgst_amount = "₹ " . (number_format($columns['sgst_amount'], 2) ?? 0);
        $total_igst_amount = "₹ " . (number_format($columns['igst_amount'], 2) ?? 0);
        $total_amount_main = "₹ " . (number_format($columns['grand_total'], 2) ?? 0);
        $amtinwords = $this->numberToWords($columns['grand_total']);

        $bill_pincode = '';

        $warehouse_address = '';
        $warehouse_city = '';
        $warehouse_state = '';
        $warehouse_pincode = '';
        $warehouse_gstin_no = '';


        // Set image path (make sure the image path is accessible by TCPDF)
        $logoPath = $deshwal_logo; // Place header.png in TCPDF's images directory
        $tplVars = [
            'logoPath' => $logoPath,
            'quotes_dit_no'=> $quotes_dit_no
        ];

        // create new PDF document
        $pdf = new MyPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Deshwal Waste Management');
        $pdf->SetTitle('DevIT Quotation');
        $pdf->SetSubject('DevIT Quotation PDF');
        $pdf->SetKeywords('TCPDF, PDF, quotation');

        // set header and footer off
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // set margins
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(TRUE, 10);

        // set font for Unicode symbols (₹)
        $pdf->SetFont('dejavusans', '', 9);
        $pdf->setPrintFooter(true);
        $pdf->SetAutoPageBreak(true, 25); // Reserve 25mm for footer
        PdfHeaderFooterHelper::setupPdfWithTemplate(
            $pdf,
            (int)$this->TabId,      
            $tplVars,
            'quotes_devit',      
            false
        );
        // add a page
        $pdf->AddPage();
        $pdf->drawHeaderContent();
        // $pdf->drawStamp();

        // logo path
        $logo = K_PATH_IMAGES . 'header.png';

      $html = <<<EOD
        
        
        <br>
        
        <table cellpadding="4" cellspacing="0" border="0" style="font-size:6.5px;font-weight:bold">
          
          <tr>
          <td width="25%">Creation Date: $quote_create_date</td>
          <td width="25%">Expiry Date: $expiry_date</td>
          <td width="25%">Payment Terms: $payment_terms</td>
          <td width="25%">Delivery: $delivery_terms</td>
          </tr>
        </table>


        <table cellpadding="4" cellspacing="0" border="0" style="font-size:6.5px;font-weight:bold">  
          <tr><td>To: $acc_name<br><br>Dear: $contact_name</td></tr>
          <tr><td>As per your requirement, We are pleased to submit our formal proposal and look forward to strengthen our relationship by providing the latest and
        most cost-effective products and solutions.<br></td></tr>
        </table>
        
        
        <table border="0" cellpadding="3" cellspacing="0">
        <tr style="background-color:#f2f2f2; ">
          <td style="border: 0.5mm solid #e6e2d2; text-align:center" width="50%"><b>Billing Address</b></td>
          <td style="border: 0.5mm solid #e6e2d2; text-align:center" width="50%"><b>Ship to Address</b></td>
          
        </tr>
        <tr>
          <td style="border: 0.5mm solid #e6e2d2;">$acc_name</td>
          <td style="border: 0.5mm solid #e6e2d2;">$shiplegalname</td>
          
        </tr>
        <tr>
          <td style="border: 0.5mm solid #e6e2d2;">$bill_to_address</td>
          <td style="border: 0.5mm solid #e6e2d2;">$ship_address</td>
          
        </tr>
        <tr>
          <td style="border: 0.5mm solid #e6e2d2;">$bill_city, $bill_state, $bill_to_state_code</td>
          <td style="border: 0.5mm solid #e6e2d2;">$bill_city, $ship_state, $ship_state_code</td>
          
        </tr>
        <tr>
          <td style="border: 0.5mm solid #e6e2d2;"><b>GSTIN: $bill_gstin_no_uin</b></td>
          <td style="border: 0.5mm solid #e6e2d2;"><b>GSTIN: $ship_gst    </b></td>
         
        </tr>
        </table>
        
        <br><br>
        
        <table border="0" cellpadding="3" cellspacing="0" style="font-size:7px">
        <tr style="background-color:#f2f2f2; font-weight:bold;">
          <td width="5%" style="border: 0.5mm solid #e6e2d2;">S.No</td>
          <td width="20%" style="border: 0.5mm solid #e6e2d2;">Product Name</td>
          <td width="10%" style="border: 0.5mm solid #e6e2d2;">HSN Code</td>
          <td width="8%" style="border: 0.5mm solid #e6e2d2;">Qty</td>
          <td width="10%" style="border: 0.5mm solid #e6e2d2;">Basic Price</td>
          <td width="12%" style="border: 0.5mm solid #e6e2d2;">Amount</td> 
          <td width="7%" style="border: 0.5mm solid #e6e2d2;">SGST%</td>
          <td width="7%" style="border: 0.5mm solid #e6e2d2;">CGST%</td>
          <td width="6%" style="border: 0.5mm solid #e6e2d2;">IGST%</td>
          <td width="15%" align="right" style="border: 0.5mm solid #e6e2d2;">Total Amount</td>
        </tr>
EOD;
        $i = 1;
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
            $total_amount = number_format($value['amount'], 2);
            $amount = number_format($value['basic_price']*$quantity, 2);


            $html .= <<<EOD
        <tr style="">
          <td align="right" style="border: 0.5mm solid #e6e2d2;">$i</td>
          <td style="border: 0.5mm solid #e6e2d2;"><strong>$productid</strong><br>$product_description</td>
          <td align="right" style="border: 0.5mm solid #e6e2d2;">$hsn_code</td>
          <td align="right" style="border: 0.5mm solid #e6e2d2;">$quantity</td>
          


          <td align="right" style="border: 0.5mm solid #e6e2d2;">$cost_price</td>
          <td align="right" style="border: 0.5mm solid #e6e2d2;">$amount</td>
          <td align="right" style="border: 0.5mm solid #e6e2d2;">$sgst_percent</td>
          <td align="right" style="border: 0.5mm solid #e6e2d2;">$cgst_percent</td>
          <td align="right" style="border: 0.5mm solid #e6e2d2;">$igst_percent</td>
          <td align="right" style="border: 0.5mm solid #e6e2d2;">$total_amount</td>
        </tr>
        EOD;


            $i++;
        }
        $html .= <<<EOD
        
        </table>
        
        <br>
        <br>


        <table cellpadding="0">
            <tr>
                <td width="65%">Special Terms & Conditions:<br>$terms_and_condition</td>
                <td  width="35%">
                    <table>
                        <tr>
                            <td width="45%">Basic Amount</td><td width="10%">:</td><td style="text-align:right;" width="45%">$basic_cp</td>
                            </tr>
                        <tr>
                            <td>SGST</td><td>:</td><td style="text-align:right;">$total_sgst_amount</td>
                        </tr>
                        <tr>
                        <td>CGST</td><td>:</td><td style="text-align:right;">$total_cgst_amount</td>
                        </tr>
                        <tr>
                            <td>IGST</td><td>:</td><td style="text-align:right;">$total_igst_amount</td>
                        </tr>
                        <tr>
                            <td>Grand Total</td><td>:</td><td style="text-align:right;">$total_amount_main</td>
                            
                        </tr>
                    </table>
              
                </td>
            </tr>
        </table>
        <br>
        <br>
        <table>
            <tr>    
                <td style="text-align:left;"><strong>Amount in Words: $amtinwords</strong></td>
               
            </tr>
        </table>
        <br>
        <br>
        <table>
            <tr>    
                <td style="text-align:left;">&nbsp;</td>
                <td align="right">$deshwal_spocname<br>$email<br>$mobile</td>
            </tr>
        </table>


        <table>
            <tr>
            <td width="60%">Payment Information For:<br>Beneficiary Name: Dev IT Serv Pvt.Ltd<br>Bank A/C No: 17182790000011<br>Bank Code: HDFC0001718<br>
            </td>
            <td width="40%"><span style="font-size:10px">DEV IT Serv Private Limited</span><br>$deshwal_spocname<br>Mobile: $mobile<br>Email: $email
            </td>
            </tr>
        </table>
        <table cellpadding="8">
            <tr>
            <td colspan="2" style="background-color:#f2f2f1;" >PO to be raised on: DEV IT SERV PVT LTD</td>
            </tr>
            
        </table>
        <h2>Quote Details</h2>
        <hr style="background-color:#a0a0a0;"><br>
        <h3>Product Description</h3>
        <table style="font-weight:bold">
           
       
        EOD;
       
        $i = 1;
        foreach ($quoteitems as $value) {


            $productid = $value['p_product_name'];
            $product_description = $value['product_description'];


            // $working_condition = $value['working_conditions'];
            $hsn_code = $value['hsn_code'];
            $cost_price = number_format($value['basic_price'], 2);
            $quantity = $value['qty'];


            


            $html .= <<<EOD
                    <tr>
                    <td width="5%">$i</td>
                    <td  width="40%">$productid<br>$product_description</td>
                    <td  width="30%">$quantity</td>
                    
                    </tr>
                    EOD;


            $i++;
        }
         $html .= <<<EOD
        </table>
        EOD;



        $pdf->writeHTML($html, true, false, true, false, '');
        $filename = $quotes_dit_no . '_quotation_deshwal_' . $todaydate . '.pdf';
        $pdf->Output($filename, 'I');


    }




}
