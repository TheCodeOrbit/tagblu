<?php

namespace backend\modules\purchaseorderdit\controllers;

use common\components\MyPDF;
use common\controllers\ModuleController;
use common\components\TcpdfHelper;
use common\components\PdfHeaderFooterHelper;
use DateTime;

use Yii;
/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'multiple';
    public $ModuleName = 'purchaseorderdit';
    public $FieldId = 'purchaseorder_dit_id';
    public $TableName = 'purchase_order_dit';
    public $TabLabel = 'DevIT Purchase Order';


    public $TabId = '78';
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


    public function actionGetvendoraddress()
    {
        $data = $_POST;
        $bill_location = Yii::$app->request->post('bill_location');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                        SELECT vendor_locations.legal_entity_name,vendor_locations.address,state_value as state,vendor_locations.state_code,vendor_locations.gstin_no_uin,vendor_account.pan_no FROM vendor_locations 
                        join vendor_account on vendor_account.vendoraccid = vendor_locations.vendor_account
                        join state on state.state_id = vendor_locations.state
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
                'message' => 'No Info found.',
                'data' => ''
            ]);
        }
    }
    public function actionGetvendoraccount()
    {
        $data = $_POST;
        $vendor = Yii::$app->request->post('vendor');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                        SELECT vendoraccid,acc_name from salesorder_dit
                        join vendor_account on vendor_account.vendoraccid = salesorder_dit.account_name
                         WHERE salesorder_dit_id  = :bill_location
                    ")->bindValue(":bill_location", $vendor);
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

    public function actionGetwarehouseaddress()
    {
        $data = $_POST;
        $business_entity = Yii::$app->request->post('bill_location');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                        SELECT warehouse_name,address,state,statecode,pincode,gstn FROM warehouse WHERE warehouse_id = :business_entity
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
                'message' => 'No location found.',
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
                        SELECT product_description,hsn_code,gst_percentage FROM `product_dit` 
                          WHERE productdit_id = :products_id
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
                'message' => 'No Product info found.',
                'data' => ''
            ]);
        }
    }
    public function actionGeneratepdf($Record)
    {
        $sql = "select vcon.country_value as vendorcountry,stcon.country_value as whcountry ,ct.city_name as whcity,ctv.city_name ,val.city,val.vendor_loc_name  as vendor_loc_name,vendoraccid,DATE_FORMAT(`purchase_order_dit`.`estimate_time_delivery`,'%d-%m-%Y') AS estimate_time_delivery_format,purchase_order_dit.*,va.acc_name,va.pan_no,val.pincode,po_Issued_entity_name  as contact_name,vl.warehouse_name,vl.pincode as whpincode,purchaseorder_cerdit_terms_value  from  `purchase_order_dit` 
        left join vendor_account va on va.vendoraccid = purchase_order_dit.vendor_name
        left join vendor_locations val on val.vendorloc_id = purchase_order_dit.location
        left join warehouse vl on vl.warehouse_id = purchase_order_dit.delivery_entitiy_name 
        left join city ct on ct.cityid = vl.city
        left join state vst on vst.state_id =ct.stateid
        left join country vcon on vcon.country_id =vst.country_id
        left join city ctv on ctv.cityid = val.city
         left join state dst on dst.state_id =ctv.stateid
        left join country stcon on stcon.country_id =dst.country_id
        left join purchaseorder_cerdit_terms on purchaseorder_cerdit_terms.purchaseorder_cerdit_terms_id= purchase_order_dit.credit_terms
        where purchaseorder_dit_id = :Record";
        $connection = Yii::$app->db;
        $command = $connection->createCommand($sql)->bindValue(":Record", $Record);
        $columns = $command->queryOne();
        
        $sql = "select products.product_name as p_product_name,products.product_description as prod_description ,purchaseorderdit_product_details.* from purchaseorderdit_product_details
        join product_dit as products on products.productdit_id = purchaseorderdit_product_details.product_name
        where purchaseorder_dit_id = :Record";
        $connection = Yii::$app->db;
        $command = $connection->createCommand($sql)->bindValue(":Record", $Record);
        $quoteitems = $command->queryAll();



        $deshwal_logo = Yii::getAlias('@webroot/thememain/img/deshwal-header.png');
        $devit_stamp = Yii::getAlias('@webroot/images/devit_stamp.png');

        $record_id = $Record;//"74";//Yii::$app->request->post('Record');

        // Company Information
        $todaydate = date("d/m/Y");
        //$quote_create_date = date("M d,Y", strtotime($columns['quote_create_date']));
        // $expiry_date = date("M d,Y", strtotime($columns['expiry_date']));
        $terms_and_condition = nl2br($columns['terms_condition']);
        $acc_name = $columns['acc_name'];
        $contact_name = $columns['contact_name'];
        $purchase_order_dit_no = $columns['purchaseorder_dit_no'];
        $bill_to_legal_name = $columns['vendor_loc_name'];
        $bill_to_address = nl2br($columns['address']);
        $bill_city = $columns['city_name'];
        $bill_state = $columns['source_of_supply'];
        $bill_state_code = $columns['bill_state_code'];
        $po_date = $columns['purchase_order_date'];
        $credit_terms = $columns['purchaseorder_cerdit_terms_value'];

        //delivery
        $delivery_name = $columns['warehouse_name'];
        $whpincode = $columns['whpincode'];
        $delivery_location = $columns['delivery_location'];
        $delivery_address = $columns['delivery_address'];
        $delivery_destination_of_supply = $columns['delivery_destination_of_supply'];
        $delivery_state_code = $columns['delivery_state_code'];
        $delivery_gst_number = $columns['delivery_gst_number'];
        $whcity_name = $columns['whcity'];
        $vendorcountry = $columns['vendorcountry'];
        $whcountry = $columns['whcountry'];
        $bill_entitiy_name = $columns['bill_entitiy_name'];
        $estimate_time_delivery = $columns['estimate_time_delivery_format'];
        $pincode = $columns['pincode'];
        $pan_number = $columns['pan_no'];

        $delivery_instruction = '<span style="text-align: justify;">'.$columns['delivery_instruction'].'</span>';

        ///////get material receiver
        $sql_v = "select concat(u.first_name,' ',u.last_name) as deshwal_spocname,u.email,u.mobile from warehouse va join user u on va.warehouse_manager = u.id where va.warehouse_id = :bill_entitiy_name";
        $command = $connection->createCommand($sql_v)->bindValue(":bill_entitiy_name", $bill_entitiy_name);
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



        // $bill_pincode = $columns['bill_pincode'];
        $bill_gstin_no_uin = $columns['gst_number'];
        // $warehouse_address = $columns['warehouse_address'];
        // $warehouse_city = $columns['warehouse_city'];
        // $warehouse_state = $columns['warehouse_state'];
        // $warehouse_pincode = $columns['warehouse_pincode'];
        // $warehouse_gstin_no = $columns['warehouse_gstin_no'];
        $basic_cp = "₹ " . (number_format($columns['sub_total'], 2) ?? 0);
        $total_cgst_amount = "₹ " . (number_format($columns['cgst_amount'], 2) ?? 0);
        $total_sgst_amount = "₹ " . (number_format($columns['sgst_amount'], 2) ?? 0);
        $total_igst_amount = "₹ " . (number_format($columns['igst_amount'], 2) ?? 0);
        $total_amount_main = "₹ " . (number_format($columns['total'], 2) ?? 0);
        $amtinwords = $this->numberToWords($columns['total']);
        $total_cgst_sgst = number_format($columns['cgst_amount'] + $columns['sgst_amount']);
        $bill_pincode = '';

        $warehouse_address = '';
        $warehouse_city = '';
        $warehouse_state = '';
        $warehouse_pincode = '';
        $warehouse_gstin_no = '';


        // Set image path (make sure the image path is accessible by TCPDF)
        // $logoPath = $deshwal_logo; // Place header.png in TCPDF's images directory
        $devit_logo = Yii::getAlias('@webroot/thememain/img/dcpdfheader.jpg');
        $logoPath = $devit_logo;

       $tplVars = [
            'logoPath' => $logoPath
        ];

        $pdf = new MyPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Deshwal Waste Management');
        $pdf->SetTitle('DevIT Purchase Order');
        $pdf->SetSubject('DevIT Purchase Order PDF');
        $pdf->SetKeywords('TCPDF, PDF, Purchase Order');

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
            'purchaseorder_dit',      
            false
        );
        $pdf->AddPage();
        $pdf->drawHeaderContent();
        // $pdf->drawStamp();

        // logo path
        $logo = K_PATH_IMAGES . 'header.png';

        // if ($columns['terms_condition'] != '') {
          $title = 'Special Terms & Conditions';
            $special_terms_and_condition = $title.'<p style="text-align: justify;">'.$columns['terms_condition']."</p>";

            $terms_and_condition = '<p style="text-align: center;font-size:7px;">Terms & Conditions</p>
                <ol style="padding-left: 20px;font-size:7px;">
                    <li>PO number to be mentioned on invoice for each material.</li>
                    <li>Material number to be mentioned on invoice/challan for each material.</li>
                    <li>
                        Delivery is expected complete when Gate entry is successful, which requires following:
                        <ol type="a" style="padding-left: 20px;">
                            <li>Copy of the PO</li>
                            <li>Printed tax invoice from vendor</li>
                            <li>E-Way bill as needed</li>
                        </ol>
                    </li>
                    <li>All items should be strictly as per specifications and quality norms defined by Dev IT Serv Pvt. Ltd.</li>
                    <li>All deliveries shall be subject to acceptance and approval of Dev IT Serv Pvt. Ltd.</li>
                    <li>All disputes subject to jurisdiction of Gurgaon Court.</li>
                    <li>Goods that need to be returned to the vendor, should be picked up within 7 days of communication to the vendor.
                        We reserve the right to dispose the material in case of any delays.
                    </li>
                    <li>Please deliver material between 9 A.M. and 6.00 P.M. on working days.</li>
                    <li>
                        In case of late deliveries, Buyer reserves the right to:
                        <ol type="a" style="padding-left: 20px;">
                            <li>cancel</li>
                            <li>purchase from elsewhere or</li>
                            <li>accept the delivery, holding Seller accountable for loss caused by the cancellation, purchasing elsewhere, or the late delivery, as the case may be.</li>
                        </ol>
                    </li>
                    <li>For Further Enquiry/Information mail at purchase@ditserv.com</li>
                </ol>';

            $html = <<<EOD

            <br>

            <table cellpadding="4" style="width:100%;border-collapse:collapse;margin-bottom:10px;font-size:7.5px;font-weight:bold;text-align:center;">
                <tr>
                    <td width="100%" style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;">
                        Purchase Order
                    </td>
                </tr>
            </table>

            <table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;margin-bottom:10px;font-size:7.5px;padding-top:6px;padding-bottom:6px;">
                <tr style="padding:0px;">
                    <td width="50%" style="border:1px solid #bbbcbc;padding:0px;vertical-align:top;">
                        <table cellpadding="1" style="width:100%;border-collapse:collapse;margin-bottom:10px;font-size:7.5px;">
                            <tr>
                                <td style="border:none;padding:4px 8px;vertical-align:top;">Purchase Order#</td>
                                <td style="border:none;padding:4px 8px;vertical-align:top;">:<strong> $purchase_order_dit_no</strong></td>
                            </tr>
                            <tr>
                                <td style="border:none;padding:4px 8px;vertical-align:top;">Date</td>
                                <td style="border:none;padding:4px 8px;vertical-align:top;"><strong>: $todaydate</strong></td>
                            </tr>
                            <tr>
                                <td style="border:none;padding:4px 8px;vertical-align:top;">Payment Terms</td>
                                <td style="border:none;padding:4px 8px;vertical-align:top;"><strong>: $credit_terms</strong></td>
                            </tr>
                            <tr>
                                <td style="border:none;padding:4px 8px;vertical-align:top;">Estimated Time of Delivery</td>
                                <td style="border:none;padding:4px 8px;vertical-align:top;"><strong>: $estimate_time_delivery</strong></td>
                            </tr>
                        </table>
                    </td>
                    <td width="50%" style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;">
                        <table style="width:100%;border-collapse:collapse;margin-bottom:10px;font-size:7.5px;">
                            <tr>
                                <td style="border:none;padding:4px 8px;vertical-align:top;">Place Of Supply</td>
                                <td style="border:none;padding:4px 8px;vertical-align:top;"><strong>: $delivery_destination_of_supply ($delivery_state_code)</strong></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <table cellpadding="4" style="width:100%;border-collapse:collapse;margin-bottom:10px;font-size:7.5px;">
                <tr>
                    <th width="50%" style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;background-color: #e1e9f7;font-weight:bold;">
                        <strong>Vendor Details</strong>
                    </th>
                    <th width="50%" style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;background-color:#e1e9f7;font-weight:bold;">
                        <strong>Deliver To</strong>
                    </th>
                </tr>
                <tr>
                    <td style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;">
                        <p style="line-height:12px">
                            <b>$acc_name</b><br>
                            $bill_to_legal_name<br>
                            $bill_to_address<br>
                            $bill_city, $bill_state, $pincode $vendorcountry<br>
                            GSTIN: $bill_gstin_no_uin<br>
                            PAN No: $pan_number
                        </p>
                    </td>
                    <td style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;">
                        <p style="line-height:12px">
                            Dev IT Serv Private Limited&lt;-- delivery_location--&gt;<br>
                            $delivery_address<br>
                            $whcity_name, $delivery_destination_of_supply, $whpincode $whcountry<br>
                            GSTIN: $delivery_gst_number
                        </p>
                    </td>
                </tr>
            </table>

            <table cellpadding="4" style="width:100%;border-collapse:collapse;margin-bottom:10px;font-size:7.5px;">
                <tr>
                    <td style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;">
                        <table cellpadding="1" style="width:100%;border-collapse:collapse;margin-bottom:10px;font-size:7.5px;">
                            <tr>
                                <td style="border:none;padding:4px 8px;vertical-align:top;">
                                    <strong>Material Receiver Name:</strong> $deshwal_spocname
                                </td>
                            </tr>
                            <tr>
                                <td style="border:none;padding:4px 8px;vertical-align:top;">
                                    <strong>Contact No:</strong>  $mobile
                                </td>
                            </tr>
                            <tr>
                                <td style="border:none;padding:4px 8px;vertical-align:top;">
                                    <strong>Email ID:</strong>  $email
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;">
                        <strong>Delivery Instruction</strong><br/>$delivery_instruction
                    </td>
                </tr>
            </table>

            <table cellpadding="2" style="width:100%;border-collapse:collapse;margin-bottom:10px;font-size:7.5px;">
                <tr style="background-color:#f2f3f4;font-weight:bold;">
                    <th rowspan="2" width="6%"  style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;text-align:center;">S.No</th>
                    <th rowspan="2" width="30%" style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;">Item & Description</th>
                    <th rowspan="2" width="9%"  style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;">HSN/SAC</th>
                    <th rowspan="2" width="5%"  style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;text-align:right;">Qty</th>
                    <th rowspan="2" width="10%" style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;text-align:right;">Rate</th>
            EOD;

            if ($quoteitems[0]['cgst'] != '') {
                $html .= <<<EOD
                    <th colspan="2" width="12%" style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;text-align:center;">CGST</th>
                    <th colspan="2" width="12%" style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;text-align:center;">SGST</th>
            EOD;
            } else {
                $html .= <<<EOD
                    <th width="24%" style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;text-align:center;">IGST</th>
            EOD;
            }

            $html .= <<<EOD
                    <th rowspan="2" width="16%" style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;text-align:right;">Amount</th>
                </tr>
            EOD;

            if ($quoteitems[0]['cgst'] != 0) {
                $html .= <<<EOD
                <tr style="background-color:#f2f3f4;font-weight:bold;">
                    <th width="4%" style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;text-align:right;">%</th>
                    <th width="8%" style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;text-align:right;">Amt</th>
                    <th width="4%" style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;text-align:right;">%</th>
                    <th width="8%" style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;text-align:right;">Amt</th>
                </tr>
            EOD;
            } else {
                $html .= <<<EOD
                <tr style="background-color:#f2f3f4;font-weight:bold;">
                    <th width="12%" style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;text-align:right;">%</th>
                    <th width="12%" style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;text-align:right;">Amt</th>
                </tr>
            EOD;
            }

            $i = 1;
            $gross_total_amount = 0;
            $gross_total_amount_in_format = 0;
            $total_quantity = 0;
            foreach ($quoteitems as $value) {

                $productid           = $value['p_product_name'];
                $product_description = $value['prod_description'];
                $hsn_code            = $value['hsn_code'];
                $cost_price          = number_format($value['basic_cost_price'], 2);
                $quantity            = $value['qty'];
                //to resolve issue -v11-269 total qty getting incorrect added by ptpatel on date 11-02-2026
                $total_quantity  +=$value['qty'];

                $cgst_percent = ($value['cgst'] == 0) ? '-' : $value['cgst'];
                $sgst_percent = ($value['sgst'] == 0) ? '-' : $value['sgst'];
                $igst_percent = ($value['igst'] == 0) ? '-' : $value['igst'];

                $igst_amt = $cgst_amt = $sgst_amt = '';
                if ($value['cgst'] != 0) {
                    $cgst_amt = (($value['basic_cost_price'] * $quantity) * $cgst_percent) / 100;
                    $sgst_amt = (($value['basic_cost_price'] * $quantity) * $cgst_percent) / 100;
                }
                if ($value['igst'] != 0) {
                    $igst_amt = (($value['basic_cost_price'] * $quantity) * $igst_percent) / 100;
                }
                $total_amount = number_format($value['qty'] * $value['basic_cost_price'], 2);
                $gross_total_amount = $gross_total_amount + $value['product_total'];
                $gross_total_amount_in_format += $value['qty'] * $value['basic_cost_price'];

                $html .= <<<EOD
                <tr>
                    <th style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;text-align:center;">$i</th>
                    <th style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;">$productid<br>$product_description</th>
                    <th style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;">$hsn_code</th>
                    <th style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;text-align:right;">$quantity</th>
                    <th style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;text-align:right;">$cost_price</th>
            EOD;

                if ($cgst_percent != "-") {
                    $html .= <<<EOD
                    <th style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;text-align:right;">$cgst_percent</th>
                    <th style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;text-align:right;">$cgst_amt</th>
                    <th style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;text-align:right;">$sgst_percent</th>
                    <th style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;text-align:right;">$sgst_amt</th>
            EOD;
                } else {
                    $html .= <<<EOD
                    <th style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;text-align:right;">$igst_percent</th>
                    <th style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;text-align:right;">$igst_amt</th>
            EOD;
                }

                $html .= <<<EOD
                    <th style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;text-align:right;">$total_amount</th>
                </tr>
            EOD;

                $i++;
            }

            $gross_total_amount_in_format = number_format($gross_total_amount_in_format, 2);
            $quantity = number_format($quantity, 2);

            $html .= <<<EOD
            </table>

            <table cellpadding="4" style="width:100%;border-collapse:collapse;margin-bottom:10px;font-size:7.5px;">
                <tr>
                    <td width="50%" style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;">Items in Total: $total_quantity</td>
                    <td width="50%" style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;">
                        <table cellpadding="2" style="width:100%;border-collapse:collapse;margin-bottom:10px;font-size:7.5px;">
                            <tr>
                                <td style="border:none;padding:4px 8px;vertical-align:top;text-align:right;" widht="50%">Sub Total:</td>
                                <td style="border:none;padding:4px 8px;vertical-align:top;text-align:right;" widht="50%">$gross_total_amount_in_format</td>
                            </tr>
            EOD;

            if ($sgst_percent != 0 && $sgst_percent != '-') {
                $total_cgst_sgst_amount = number_format($columns['cgst_amount'], 2);
                $html .= <<<EOD
                            <tr>
                                <td style="border:none;padding:4px 8px;vertical-align:top;text-align:right;" widht="50%">SGST ($sgst_percent %):</td>
                                <td style="border:none;padding:4px 8px;vertical-align:top;text-align:right;" widht="50%">$total_cgst_sgst_amount</td>
                            </tr>
            EOD;
            }
            if ($cgst_percent != 0 && $cgst_percent != '-') {
                $total_cgst_sgst_amount = number_format($columns['cgst_amount'], 2);
                $html .= <<<EOD
                            <tr>
                                <td style="border:none;padding:4px 8px;vertical-align:top;text-align:right;" widht="50%">CGST ($cgst_percent %):</td>
                                <td style="border:none;padding:4px 8px;vertical-align:top;text-align:right;" widht="50%">$total_cgst_sgst_amount</td>
                            </tr>
            EOD;
            }
            if ($igst_percent != 0 && !empty($igst_percent) && $igst_percent != '-') {
                $html .= <<<EOD
                            <tr>
                                <td style="border:none;padding:4px 8px;vertical-align:top;text-align:right;" widht="50%">IGST ($igst_percent %):</td>
                                <td style="border:none;padding:4px 8px;vertical-align:top;text-align:right;" widht="50%">$total_igst_amount</td>
                            </tr>
            EOD;
            }

            $html .= <<<EOD
                            <tr>
                                <td style="border:none;padding:4px 8px;vertical-align:top;text-align:right;font-weight:bold;" widht="50%">Total:</td>
                                <td style="border:none;padding:4px 8px;vertical-align:top;text-align:right;font-weight:bold;" widht="50%">$total_amount_main</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;">$special_terms_and_condition</td>
                    <td style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;text-align:center;">
                        DevIT Serv Pvt Ltd<br><img src="$devit_stamp" height="100">
                        <p style="margin-top:0px;">Authorized Signature</p>
                    </td>
                </tr>
            </table>

            <br><br><br><br><br>

            <table style="width:100%;border-collapse:collapse;margin-bottom:10px;font-size:7.5px;">
                <tr>
                    <td style="border:1px solid #bbbcbc;padding:6px;vertical-align:top;">$terms_and_condition</td>
                </tr>
            </table>

            <br><br>
            EOD;


        //    echo $html;die;
        $pdf->writeHTML($html, true, false, true, false, '');
        $filename = $purchase_order_dit_no . '_quotation_devit_' . $todaydate . '.pdf';
        $pdf->Output($filename, 'I');


    }


    /* this code is commented because we need  to get multiple products for mutilple so
    public function actionGetproductdetail()
    {
        $data = $_POST;
        $so_name = Yii::$app->request->post('so_name');
        $connection = Yii::$app->db;

        //auto fill product_description and oem part no as per V11 - point no 36 code added by ptptale on date 11-10-2025

        $sql = "SELECT opd.*, 
               product_dit.product_name AS prod_name,
               product_dit.product_description AS prod_description,
               product_dit.oem_part_number AS prod_oem_part_number
        FROM salesorderdit_product_details opd 
        JOIN salesorder_dit ON salesorder_dit.salesorder_dit_id = opd.salesorder_dit_id
        JOIN product_dit ON product_dit.productdit_id = opd.product_name
        WHERE opd.salesorder_dit_id = :so_name";

        $command = $connection->createCommand($sql)->bindValue(":so_name", $so_name);
        $columns = $command->queryAll();

        // Loop by reference to allow modification/removal
        foreach ($columns as $key => &$rows) {
            $product_name = $rows['product_name'];
            $so_qty = (float) $rows['qty']; // Make sure it's treated as a number
            $remaining_qty = $so_qty;

            // Check if PO is created for this SO and product
            $sql_chk = "SELECT sum(qty) as qty
                        FROM purchase_order_dit po
                        LEFT JOIN purchaseorderdit_product_details ppd ON ppd.purchaseorder_dit_id = po.purchaseorder_dit_id
                        WHERE po.reference_number = :reference_number  AND ppd.product_name = :product_name";

            $cmd = $connection->createCommand($sql_chk)
                ->bindValue(":reference_number", $so_name)
                ->bindValue(":product_name", $product_name);

            $chkcolumns = $cmd->queryOne();
            // print_r($chkcolumns);
            if ($chkcolumns) {          
                $ordered_qty = (float) $chkcolumns['qty'];                
                $remaining_qty = $so_qty - $ordered_qty;
                //echo $remaining_qty.' '.$so_qty.' '.$ordered_qty.' '.$chkcolumns['qty'];
            }
            //echo "<br>".$remaining_qty;die;

            if ($remaining_qty <= 0) {
                // Remove the row if no remaining quantity
                unset($columns[$key]);
            } else {
                // Add the remaining quantity
                $rows['remaining_qty'] = $remaining_qty;
            }
        }
        unset($rows); // Unset reference variable (good practice)

        // Optional: Reindex the array if needed
        $columns = array_values($columns);

        // Return response
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


    }*/

    public function actionGetproductdetail()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $so_name = Yii::$app->request->post('so_name');
        $connection = Yii::$app->db;

        // Convert comma-separated SO IDs (e.g. "25,26") into integer array
        $soIds = array_filter(array_map('intval', explode(',', trim($so_name))));
        if (empty($soIds)) {
            return $this->asJson([
                'status' => 'error',
                'message' => 'Invalid Sales Order IDs.',
                'data' => ''
            ]);
        }

        // Build named placeholders (:id0, :id1, etc.)
        $placeholders = [];
        $params = [];
        foreach ($soIds as $index => $id) {
            $key = ":id{$index}";
            $placeholders[] = $key;
            $params[$key] = $id;
        }
        $placeholderString = implode(',', $placeholders);

        // Fetch SO products
        $sql = "SELECT salesorder_dit.salesorder_dit_id,
                    salesorder_dit.salesorder_dit_no AS salesorder_dit_no,opd.product_name,
                    product_dit.product_name AS prod_name,
                    product_dit.product_description AS prod_description,
                    product_dit.oem_part_number AS prod_oem_part_number,
                    opd.hsn_code,sum(qty) as qty
                FROM salesorderdit_product_details opd 
JOIN salesorder_dit 
    ON salesorder_dit.salesorder_dit_id = opd.salesorder_dit_id
JOIN product_dit 
    ON product_dit.productdit_id = opd.product_name
WHERE opd.salesorder_dit_id IN ($placeholderString)  -- list of IDs here
GROUP BY 
    salesorder_dit.salesorder_dit_id,
    salesorder_dit.salesorder_dit_no,
    product_dit.product_name,
    product_dit.product_description,
    product_dit.oem_part_number,
    opd.hsn_code;";

        $columns = $connection->createCommand($sql, $params)->queryAll();

        // Process each product record
        foreach ($columns as $key => &$rows) {
            $product_name = $rows['product_name'];
            $ref_no = $rows['salesorder_dit_id'];
            $so_qty = (float) $rows['qty'];
            $remaining_qty = $so_qty;

            // Build same placeholders for the second query
            // $poParams = $params;
            $poParams[':product_name'] = $product_name;
            $poParams[':ref_no'] = $ref_no;

            // Build FIND_IN_SET conditions dynamically for all SO IDs
            $findInSetConditions = [];
            foreach (array_keys($params) as $pKey) {
                $findInSetConditions[] = "FIND_IN_SET($pKey, po.reference_number)";
            }
            $findInSetSql = implode(' OR ', $findInSetConditions);

            // Check if PO already created for this SO and product
            // $sql_chk = "SELECT SUM(ppd.qty) AS qty
            //             FROM purchase_order_dit po
            //             LEFT JOIN purchaseorderdit_product_details ppd 
            //                 ON ppd.purchaseorder_dit_id = po.purchaseorder_dit_id
            //             WHERE ($findInSetSql)
            //             AND ppd.product_name = :product_name";
            //so remaining qty was not coming 
             $sql_chk = "SELECT if(SUM(ppd.qty) is null,0,sum(ppd.qty)) AS qty
                        FROM purchase_order_dit po
                        LEFT JOIN purchaseorderdit_product_details ppd 
                            ON ppd.purchaseorder_dit_id = po.purchaseorder_dit_id
                        WHERE po.reference_number = :ref_no
                        AND ppd.product_name = :product_name";
                      
            $chkcolumns = $connection->createCommand($sql_chk, $poParams)->queryOne();

            if (!empty($chkcolumns['qty'])) {
                $ordered_qty = (float) $chkcolumns['qty'];
                $remaining_qty = $so_qty - $ordered_qty;
            }
            //echo $ordered_qty." ".$remaining_qty;die;
            

            // Remove product if no remaining quantity
            if ($remaining_qty <= 0) {
                unset($columns[$key]);
            } else {
                $rows['remaining_qty'] = $remaining_qty;
            }
        }
        unset($rows);

        $columns = array_values($columns);

        if (!empty($columns)) {
            return [
                'status' => 'success',
                'data' => $columns,
            ];
        }

        return [
            'status' => 'error',
            'message' => 'No Product found.',
            'data' => ''
        ];
    }

    public function actionGetsodetail()
    {
        $sourceid = Yii::$app->request->post('sourceid');
        $sourcemodule = Yii::$app->request->post('sourcemodule');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("SELECT  relation_with_account,tablename,tablekeyid from module_relation join tab on tab.tabid = source_module WHERE source_module = :related_to and related_table='purchase_order_dit' ")
            ->bindValue(":related_to", $sourcemodule);
        $columns = $command->queryOne();
        $columns2 = '';
        if (!empty($columns)) {
            $relation_with_account = $columns['relation_with_account'];
            $tablename = $columns['tablename'];
            $tablekeyid = $columns['tablekeyid'];
            // if (!empty($relation_with_account)) {
                if ($tablename == "vendor_account") {
                    $command = $connection->createCommand("SELECT  vendoraccid as vendor,acc_name from vendor_account where vendoraccid  = $sourceid ");
                    $columns2 = $command->queryOne();
                } else {
                    // $command = $connection->createCommand("SELECT  $tablename.`$relation_with_account` as vendor,va.acc_name from `$tablename` join vendor_account va on va.vendoraccid = $tablename.$relation_with_account where $tablename.`$tablekeyid` = $sourceid ");
                    $command = $connection->createCommand("SELECT salesorder_dit.`salesorder_dit_no`, salesorder_dit.`salesorder_dit_id` from `salesorder_dit` where salesorder_dit.`salesorder_dit_id` = $sourceid ;");
                    $columns2 = $command->queryOne();

                }
            // }

        }
        if (!empty($columns2)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns2,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Info found.',
                'data' => ''
            ]);
        }

    }
}
