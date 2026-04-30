<?php
 
namespace backend\modules\quotes\controllers;

use common\components\MyPDF;
use common\controllers\ModuleController;
use common\components\TcpdfHelper;
use DateTime;
use common\components\PdfHeaderFooterHelper;
use Yii;
/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'multiple';
    public $ModuleName = 'quotes';
    public $FieldId = 'quotes_id';
    public $TableName = 'quotes';
    public $TabLabel = 'Quotes';


    public $TabId = '42';
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

    public function actionGetwarehouse()
    {
        $data = $_POST;
        $business_entity = Yii::$app->request->post('business_entity');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                        SELECT warehouse_name,address,state,statecode,pincode,gstn,city.city_name as city FROM warehouse
                        left join city on city.cityid = warehouse.city
                        WHERE warehouse_id = :business_entity
                    ")->bindValue(":business_entity", $business_entity);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Warehouse found.',
                'data' => ''
            ]);
        }
    }
    public function actionGetvendorlocation()
    {
        $data = $_POST;
        $bill_location = Yii::$app->request->post('bill_location');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                        SELECT vendor_account.pan_no,legal_entity_name,vendor_locations.address,state.state_value as state,state.state_code,gstin_no_uin,pincode,city.city_name as city FROM vendor_locations 
                        left join state on state.state_id = vendor_locations.state
                        left join city on city.cityid = vendor_locations.city
                        left join vendor_account on vendor_account.vendoraccid = vendor_locations.vendor_account
                        WHERE vendorloc_id = :bill_location
                    ")->bindValue(":bill_location", $bill_location);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Contact found.',
                'data' => ''
            ]);
        }
    }
    public function actionGetvendordetail()
    {
        $data = $_POST;
        $account_name = Yii::$app->request->post('account_name');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                        SELECT credit_days,acc_name as vendor_name,if(kyc_completed = 1,'KYC Completed','KYC Not Completed') as kyc_status,legal_entity FROM vendor_account
                        left join  credit_days on  credit_days.credit_daysid = vendor_account.credit_days
                        WHERE vendoraccid = :account_name
                    ")->bindValue(":account_name", $account_name);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Contact found.',
                'data' => ''
            ]);
        }
    }
    public function actionGetproductinfo()
    {
        $data = $_POST;
        $productid = Yii::$app->request->post('productid');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                         SELECT product_description,hsn_code,uom,cost_price,prod_category_value as category,sub_catagory_value as subcategory,uom_value,asset_condition FROM `products` 
                        join prod_category on prod_category.prod_category_id = products.category
                        join prod_sub_catagory on prod_sub_catagory.sub_catagory_id = products.subcategory
                        join prod_uom on prod_uom.uom_id = products.uom
                        join po_asset_condition on po_asset_condition.assetconditionid = products.asset_condition
                          WHERE products_id = :products_id
                    ")->bindValue(":products_id", $productid);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Product Info found.',
                'data' => ''
            ]);
        }
    }

    public function actionGetvendoraccount()
    {
        $data = $_POST;
        $related_to = Yii::$app->request->post('related_to');
        $related_to_id = Yii::$app->request->post('related_to_id');
        $connection = Yii::$app->db;

        if ($related_to == 51) {
            //get account from sourcingdeal
            $sql = "select vendor_account_name as account_id,acc_name as account_name,margin_percentage as margin_percent from sourcingdeal 
            join vendor_account on vendor_account.vendoraccid = sourcingdeal.vendor_account_name
            where sourcingdeal_id=:recordid";
        }

        $command = $connection->createCommand($sql)->bindValue(":recordid", $related_to_id);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Contact found.',
                'data' => ''
            ]);
        }
    }

    public function actionGetproductdetail()
    {
        $data = $_POST;
        $related_to = Yii::$app->request->post('related_to');
        $related_to_id = Yii::$app->request->post('related_to_id');
        $connection = Yii::$app->db;

        //get account from sourcingdeal
        // echo $sql = "select * from product_costing where related_to=$related_to and related_to_id=$related_to_id";
        $sql = "select pcd.*,product_name from product_costing 
        join product_costing_detail pcd on pcd.product_costing_id = product_costing.product_costing_id
        join products on products.products_id = pcd.productid
        where related_to=:related_to and related_to_id=:related_to_id";
        $command = $connection->createCommand($sql)->bindValue(":related_to", $related_to)->bindValue(":related_to_id", $related_to_id);
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

    public function actionFetchtermscondition()
    {
        $data = $_POST;
        $moduleid = Yii::$app->request->post('moduleid');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("SELECT  content from terms_and_conditions WHERE  status = 1 limit 1");
        $columns = $command->queryOne();
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No terms found.',
                'data' => ''
            ]);
        }
    }

    public function actionGeneratepdf($Record)
    {
        $sql = "select vendoraccid,quotes.*,va.acc_name,concat(contacts.first_name,' ',contacts.last_name) as contact_name from  `quotes` 
        join vendor_account va on va.vendoraccid = quotes.account_name
        join contacts on contacts.contacts_id = quotes.contact_name
        where quotes_id = :Record";
        $connection = Yii::$app->db;
        $command = $connection->createCommand($sql)->bindValue(":Record", $Record);
        $columns = $command->queryOne();

        $sql = "select products.product_name as p_product_name,quoted_items_detail.*,assetcondition_value as working_conditions from quoted_items_detail
join products on products.products_id = quoted_items_detail.product_name
left join po_asset_condition on po_asset_condition.assetconditionid = quoted_items_detail.working_condition
where quotes_id = :Record";
        $connection = Yii::$app->db;
        $command = $connection->createCommand($sql)->bindValue(":Record", $Record);
        $quoteitems = $command->queryAll();

        ///////get account manager
        $sql_v = "select concat(u.first_name,' ',u.last_name) as deshwal_spocname,u.email,u.mobile from vendor_account_orgaisation_section va join user u on va.userid = u.id where va.vendoraccid = :vendoraccid  and roleid='H25';";
        $command = $connection->createCommand($sql_v)->bindValue(":vendoraccid", $columns['vendoraccid']);
        // echo $columns['vendoraccid'];die;
        $colv = $command->queryOne();
        if($colv)
        {
            $deshwal_spocname = $colv['deshwal_spocname'] ?? '';
            $email = $colv['email'] ?? '';
            $mobile = $colv['mobile'] ?? '';
        }
        else{
            $deshwal_spocname = '';
            $email =  '';
            $mobile = '';
        }

        $deshwal_logo = Yii::getAlias('@webroot/thememain/img/deshwal-header.png');
        $deshwal_stamp = Yii::getAlias('@webroot/images/deshwal_stamp.png');
        $record_id = $Record;//"74";//Yii::$app->request->post('Record');

        // Company Information
        $todaydate = date("M d,Y");
        $quote_creation_date = date("M d,Y", strtotime($columns['quote_creation_date']));
        $expiry_date = date("M d,Y", strtotime($columns['expiry_date']));
        $terms_and_conditions = nl2br($columns['terms_and_conditions']);
        $acc_name = $columns['acc_name'];
        $contact_name = $columns['contact_name'];
        $quotes_no = $columns['quotes_no'];
        $bill_legal_name = $columns['bill_legal_name'];
        $bill_address = nl2br($columns['bill_address']);
        $bill_city = $columns['bill_city'];
        $bill_state = $columns['bill_state'];

      


        $bill_pincode = $columns['bill_pincode'];
        $bill_gstin_no_uin = $columns['bill_gstin_no_uin'];
        $warehouse_address = $columns['warehouse_address'];
        $warehouse_city = $columns['warehouse_city'];
        $warehouse_state = $columns['warehouse_state'];
        $warehouse_pincode = $columns['warehouse_pincode'];
        $warehouse_gstin_no = $columns['warehouse_gstin_no'];

        //below fields are added as per CR sheet changes on date 04-08-25 
        $qu_bill_location_name = $columns['qu_bill_warehouse_name'];
        $qu_bill_address = nl2br($columns['qu_bill_address']);
        $qu_bill_city = $columns['qu_bill_city'];
        $qu_bill_state = $columns['qu_bill_state'];
        $qu_bill_pin_code = $columns['qu_bill_pin_code'];
        $qu_bill_gstin_no = $columns['qu_bill_gstin_no'];

        //end  fields are added as per CR sheet changes on date 04-08-25 

        $basic_cp = "₹ " . (number_format($columns['basic_cp'], 2) ?? 0);
        $total_cgst_amount = "₹ " . (number_format($columns['total_cgst_amount'], 2) ?? 0);
        $total_sgst_amount = "₹ " . (number_format($columns['total_sgst_amount'], 2) ?? 0);
        $total_igst_amount = "₹ " . (number_format($columns['total_igst_amount'], 2) ?? 0);
        $tcs_percentage = (number_format($columns['tcs_percentage'], 2) ?? 0);
        $tcs_amount = "₹ " . (number_format($columns['tcs_amount'], 2) ?? 0);
        //added round of on 14 oct 2025
        $round_off_value = (float) ($columns['round_off'] ?? 0);
        $round_off = "₹ " . number_format($round_off_value, 2);

        // $total_amount_main = "₹ " . (number_format($columns['total_amount'], 2) ?? 0);
        $total_amount_main = "₹ " . (number_format($columns['grand_total'], 2) ?? 0);
        $amtinwords = $this->numberToWords($columns['grand_total']);


        // Set image path (make sure the image path is accessible by TCPDF)
        $logoPath = $deshwal_logo; // Place header.png in TCPDF's images directory
        $tplVars = [
            'logoPath' => $logoPath,
            'quotes_no'=> $quotes_no
        ];

        // create new PDF document
        $pdf = new MyPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Deshwal Waste Management');
        $pdf->SetTitle('Quotation');
        $pdf->SetSubject('Quotation PDF');
        $pdf->SetKeywords('TCPDF, PDF, quotation');

        // set header and footer off
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // set margins
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(TRUE, 10);

        // set font for Unicode symbols (₹)
        $pdf->SetFont('dejavusans', '', 9);
        $pdf->setPrintHeader(true);
        $pdf->setPrintFooter(true);
        $pdf->SetAutoPageBreak(true, 25); // Reserve 25mm for footer
        PdfHeaderFooterHelper::setupPdfWithTemplate(
            $pdf,
            (int)$this->TabId,      
            $tplVars,
            'quotes',      
            false
        );
        // add a page
        $pdf->AddPage();
        $pdf->drawHeaderContent();
        // $pdf->drawStamp();

        // logo path
        $logo = K_PATH_IMAGES . 'header.png';

        //change done as per CR sheet on date 04-08-25
        $html = <<<EOD
               
        <br>
        
        <table cellpadding="4" cellspacing="0" border="0">
          
          <tr><td>Creation Date: $quote_creation_date &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; Expiry Date: $expiry_date</td></tr>
          <tr><td><b>To: $acc_name</b><br><b>Dear: $contact_name</b></td></tr>
          <tr><td>Please find below quotation for disposal purpose:</td></tr>
        </table>
        
        
        <table border="0" cellpadding="2" cellspacing="0">
        <tr>
          <td width="33%" align="center"  style="border: 0.5mm solid #e6e2d2;">
            <b>Client / Vendor Address</b>
            
          </td>
          <td width="33%" align="center"  style="border: 0.5mm solid #e6e2d2;">
            <b>Delivery Address</b>
           
          </td>
          <td width="34%" align="center"  style="border: 0.5mm solid #e6e2d2;">
            <b>Bill to Address</b>
          </td>
        </tr>
        <tr>
        <!--  this bill_legal_name change on date 11-08-25 as client requirement with stamp change
         <td width="33%"  class="border">$bill_legal_name            
          </td> -->
          <td width="33%"  style="border: 0.5mm solid #e6e2d2;">$acc_name</td>
          <td width="33%"  style="border: 0.5mm solid #e6e2d2;">Deshwal Waste Management Pvt. Ltd.           
          </td>
         <td width="34%"  style="border: 0.5mm solid #e6e2d2;">Deshwal Waste Management Pvt. Ltd.         
          </td>
        </tr>
        <tr>
          <td width="33%"  style="border: 0.5mm solid #e6e2d2;">$bill_address
          </td>
          <td width="33%"  style="border: 0.5mm solid #e6e2d2;">$warehouse_address
          </td>
          <td width="34%"  style="border: 0.5mm solid #e6e2d2;">$qu_bill_address
          </td>
        </tr>
        <tr>
          <td width="33%"  style="border: 0.5mm solid #e6e2d2;">$bill_city, $bill_state, $bill_pincode.
          </td>
          <td width="33%"  style="border: 0.5mm solid #e6e2d2;">$warehouse_city, $warehouse_state, $warehouse_pincode
          </td>
          <td width="34%"  style="border: 0.5mm solid #e6e2d2;">$qu_bill_city, $qu_bill_state, $qu_bill_pin_code
          </td>
        </tr>
        <tr>
          <td width="33%"  style="border: 0.5mm solid #e6e2d2;"><b>GSTIN: $bill_gstin_no_uin</b>
          </td>
          <td width="33%"  style="border: 0.5mm solid #e6e2d2;"><b>GSTIN: $warehouse_gstin_no</b>
          </td>
          <td width="34%"  style="border: 0.5mm solid #e6e2d2;"><b>GSTIN: $qu_bill_gstin_no</b>
          </td>
        </tr>
        </table>
        
        <br><br>
        
        <table border="0" cellpadding="3" cellspacing="0" style="font-size:7px">
        <tr style="background-color:#f2f2f2; font-weight:bold;">
          <td width="5%" style="border: 0.5mm solid #e6e2d2;">S.No</td>
          <td width="16%" style="border: 0.5mm solid #e6e2d2;">Product Name</td>
          <td width="14%" style="border: 0.5mm solid #e6e2d2;">Asset Condition</td>
          <td width="8%" style="border: 0.5mm solid #e6e2d2;">HSN Code</td>
          <td width="8%" style="border: 0.5mm solid #e6e2d2;">Unit Price</td>
          <td width="8%" style="border: 0.5mm solid #e6e2d2;">Qty</td>
          <td width="8%" style="border: 0.5mm solid #e6e2d2;">UOM</td>
          <td width="7%" style="border: 0.5mm solid #e6e2d2;">CGST%</td>
          <td width="7%" style="border: 0.5mm solid #e6e2d2;">SGST%</td>
          <td width="7%" style="border: 0.5mm solid #e6e2d2;">IGST%</td>
          <td width="12%" align="right" style="border: 0.5mm solid #e6e2d2;">Total Amount</td>
        </tr>
EOD;
        $i = 1;
        foreach ($quoteitems as $value) {
           
            $productid = $value['p_product_name'];
            $working_condition = $value['working_conditions'];
            $hsn_code = $value['hsn_code'];
            //to resolve production issue this is added by ptpatel on date 27-11-2025
            // $cost_price = number_format($value['cost_price'], 4);
            $cost_price = (($value['cost_price'] != '') ? number_format($value['cost_price'], 4) : '-');
            $quantity = $value['quantity'];
            $uom = $value['uom'];
            $cgst_percent = ($value['cgst_percent']== 0)?'-':$value['cgst_percent'];
            $sgst_percent = ($value['sgst_percent']== 0)?'-':$value['sgst_percent'];
            $igst_percent = ($value['igst_percent']== 0)?'-':$value['igst_percent'];
            $total_amount = number_format($value['total_amount'], 2);


            $html .= <<<EOD
        <tr style="">
          <td align="right" style="border: 0.5mm solid #e6e2d2;">$i</td>
          <td style="border: 0.5mm solid #e6e2d2;">$productid</td>
          <td align="" style="border: 0.5mm solid #e6e2d2;">$working_condition</td>
          <td align="right" style="border: 0.5mm solid #e6e2d2;">$hsn_code</td>
          <td align="right" style="border: 0.5mm solid #e6e2d2;">$cost_price</td>
          <td align="right" style="border: 0.5mm solid #e6e2d2;">$quantity</td>
          <td style="border: 0.5mm solid #e6e2d2;">$uom</td>
          <td align="right" style="border: 0.5mm solid #e6e2d2;">$cgst_percent</td>
          <td align="right" style="border: 0.5mm solid #e6e2d2;">$sgst_percent</td>
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
                <td width="65%">&nbsp;</td>
                <td  width="35%">
                    <table>
                        <tr>
                            <td width="45%">Basic Amount</td><td width="10%">:</td><td style="text-align:right;" width="45%">$basic_cp</td>
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
                            <tr>
                                <td>TCS %</td><td>:</td><td style="text-align:right;">$tcs_percentage</td>
                            </tr>
                            <tr>
                                <td>TCS Amount</td><td>:</td><td style="text-align:right;">$tcs_amount</td>
                            </tr>
                             <tr>
                                <td>Round Off</td><td>:</td><td style="text-align:right;">$round_off</td>
                            </tr>
                            <tr>
                            <td>Total Amount</td><td>:</td><td style="text-align:right;">$total_amount_main</td>
                            
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
                <td style="text-align:right;"><strong>Deshwal Waste Management Private Limited</strong></td>
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
            <tr><td><strong>Terms & Conditions:</strong></td></tr>
            <tr>
                <td align="left" width="50%" style="font-size:8px">$terms_and_conditions
                </td>
                 <td class="" width="50%" align="right">Deshwal Waste Management Pvt Ltd<br><img src="$deshwal_stamp" height="100"><br>Aurthorized Signature
                </td>
            </tr>
        </table>
       
        EOD;

        $pdf->writeHTML($html, true, false, true, false, '');
        $filename = $quotes_no.'_quotation_deshwal_'.$todaydate.'.pdf';
        $pdf->Output($filename, 'I');
    }

    //to getn bill to address info as per CR sheet changes on date 04-08-2025
    public function actionGetbilltoaddressinfo()
    {
        $data = $_POST;
        $qu_bill_location_name = Yii::$app->request->post('qu_bill_location_name');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                        SELECT warehouse_name,address,state,statecode,pincode,gstn,city.city_name as city FROM warehouse
                        left join city on city.cityid = warehouse.city
                        WHERE warehouse_id = :qu_bill_location_name
                    ")->bindValue(":qu_bill_location_name", $qu_bill_location_name);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Warehouse found.',
                'data' => ''
            ]);
        }
    }

    public function actionGettcspercentageandtcsamount()
    {
        $sourcingdeal_id = Yii::$app->request->post('sourcing_deal_id');
        $connection = Yii::$app->db;

            //get account from sourcingdeal
            $sql = "select tcs_percentage,tcs_amount,round_off,final_quoted_amount_incl_gst from product_costing 
            where related_to_id=:recordid";

        $command = $connection->createCommand($sql)->bindValue(":recordid", $sourcingdeal_id);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Contact found.',
                'data' => ''
            ]);
        }
    }
}
