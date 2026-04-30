<?php

namespace backend\modules\purchaseorder\controllers;
use common\components\TcpdfHelper;

use common\controllers\ModuleController;
use common\components\MyPDF;
use common\components\PdfHeaderFooterHelper;
use Yii;
/**
 * Default controller for the `purchaseorder` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'multiple';
    public $ModuleName = 'purchaseorder';
    public $FieldId = 'purchase_order_id';
    public $TableName = 'purchase_order';
    public $TabLabel = 'Purchase Order';


    public $TabId = '13';
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

        $command = $connection
            ->createCommand("SELECT address,state,statecode,pincode,gstn FROM warehouse WHERE warehouse_id = :business_entity")
            ->bindValue(":business_entity", $business_entity);
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

    public function actionGetproduct()
    {
        $data = $_POST;
        $product_id = Yii::$app->request->post('product_id');
        $connection = Yii::$app->db;
        $command = $connection
            ->createCommand("SELECT product_description, hsn_code, category, prod_catagory_value 
                FROM products 
                LEFT OUTER JOIN prod_catagory ON prod_catagory.prod_catagory_id = products.category
                WHERE products.products_id = :products_id"
            )
            ->bindValue(":products_id", $product_id);
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
    public function actionGetallquotesproducts()
    {
        $data = $_POST;
        $quotes_id = Yii::$app->request->post('quotes_id');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                         SELECT  cgst_amount,sgst_amount,igst_amount,total_amount,quoted_items_detail.cost_price,quoted_items_detail.cgst_percent,quoted_items_detail.sgst_percent,quoted_items_detail.igst_percent,quoted_items_detail.quantity,products.products_id,products.product_name,product_description,quoted_items_detail.hsn_code,quoted_items_detail.uom,quoted_items_detail.cost_price,quoted_items_detail.category,quoted_items_detail.working_condition as asset_condition FROM `quoted_items_detail` 
                        join products on products.products_id = quoted_items_detail.product_name
                          WHERE quotes_id = :quotes_id
                    ")->bindValue(":quotes_id", $quotes_id);
        $columns = $command->queryAll();
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

    public function actionGetbillingdetail()
    {
        $data = $_POST;
        $quotes_id = Yii::$app->request->post('quotes_id');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                        SELECT quotes.*, vl.vendor_loc_name 
                        FROM quotes
                        LEFT JOIN vendor_locations AS vl ON vl.vendorloc_id = quotes.bill_name
                        WHERE quotes.quotes_id = :quotes_id
                    ")->bindValue(":quotes_id", $quotes_id);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No billing Info found.',
                'data' => ''
            ]);
        }
    }
    
    // this function added to auto fetch contact name and type as per client change on date 20-06-25
    public function actionGetcontactandtype()
    {
        $data = $_POST;
        $quotes_id = Yii::$app->request->post('quotes_id');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                                    SELECT q.contact_name as contact_name1,q.po_type as type ,c.first_name AS contact_name 
                                    FROM quotes q
                                    LEFT JOIN contacts c ON q.contact_name = c.contacts_id
                                    WHERE q.quotes_id = :quotes_id
                                ")->bindValue(":quotes_id", $quotes_id);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No billing Info found.',
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

    public function actionGetsourcingaccount()
    {
        $data = $_POST;
        $quotes_id = Yii::$app->request->post('quotes_id');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                         SELECT  acc_name,sourcingdeal_no,sourcingdeal_id,vendoraccid  from quotes 
                         join sourcingdeal  sd on sd.sourcingdeal_id = quotes.related_to_id and quotes.related_to = 51
                         join vendor_account va on va.vendoraccid = sd.vendor_account_name
                         WHERE quotes_id = :quotes_id
                    ")->bindValue(":quotes_id", $quotes_id);
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
    public function actionGeneratepdf($Record)
    {
        $sql = "select quotes_no,vendoraccid,purchase_order.*,payment_terms_value as payment_terms,va.acc_name,concat(contacts.first_name,' ',contacts.last_name) as contact_name from  `purchase_order` 
        join vendor_account va on va.vendoraccid = purchase_order.vendor_name
        left join contacts on contacts.contacts_id = purchase_order.contact_name
        join qu_payment_terms on qu_payment_terms.payment_termsid = purchase_order.payment_terms
        join quotes on quotes.quotes_id = purchase_order.quote
        where purchase_order_id = :Record";
        $connection = Yii::$app->db;
        $command = $connection->createCommand($sql)->bindValue(":Record", $Record);
        $columns = $command->queryOne();
        // print_r($columns);die;
        $po_stage = $columns['stage'];

        $sql = "select products.product_name as p_product_name,purchase_order_itemsdetail.*,assetcondition_value as working_conditions from purchase_order_itemsdetail
join products on products.products_id = purchase_order_itemsdetail.product_name
left join po_asset_condition on po_asset_condition.assetconditionid = purchase_order_itemsdetail.asset_condition
where purchase_order_id = :Record";
        $connection = Yii::$app->db;
        $command = $connection->createCommand($sql)->bindValue(":Record", $Record);
        $quoteitems = $command->queryAll();



        $deshwal_logo = Yii::getAlias('@webroot/thememain/img/deshwal-header.png');
        $deshwal_stamp = Yii::getAlias('@webroot/images/deshwal_stamp.png');


        $record_id = $Record; //"74";//Yii::$app->repurchase_order_noquest->post('Record');

        // Company Information
        $todaydate = date("d/m/Y", strtotime($columns['createdtime']));
        $quote_creation_date = $todaydate;
        $expiry_date = date("d/m/Y", strtotime($columns['po_expiry_date']));
        $terms_and_conditions = nl2br($columns['terms_conditions']);
        $acc_name = $columns['acc_name'];
        $contact_name = $columns['contact_name'];
        $quotation_no = $columns['quotes_no'];
        $purchase_order_no = $columns['purchase_order_no'];
        $bill_legal_name = $columns['bill_legal_name'];
        $bill_address = nl2br($columns['bill_address']);
        $bill_city = $columns['billing_city'];
        $bill_state = $columns['billing_state'];
        //get bill country
        $sql_c = "select country_value from country where country_id in (select country_id from state where state_value=:state_value) ";
        $command = $connection->createCommand($sql_c)->bindValue(":state_value", $bill_state);
        $countryitem = $command->queryOne();
        if ($countryitem)
            $bill_country = $countryitem['country_value'];
        else
            $bill_country = '';

        $bill_pincode = $columns['bill_pan_no'];
        $bill_gstin_no_uin = $columns['bill_gstin_no'];
        $warehouse_address = $columns['warehouse_address'];
        $warehouse_city = $columns['warehouse_city'];
        $warehouse_state = ucfirst($columns['warehouse_state']);
        $warehouse_state_code = $columns['warehouse_state_code'];
        $warehouse_pincode = $columns['warehouse_pincode'];
        $warehouse_gstin_no = $columns['warehouse_gstin_no'];


        $po_bill_warehouse_name = $columns['po_bill_warehouse_name'];
        $po_bill_address = $columns['po_bill_address'];
        $po_bill_state = ucfirst($columns['po_bill_state']);
        $po_bill_pin_code = $columns['po_bill_pin_code'];
        $po_bill_city = $columns['po_bill_city'];
        $po_bill_state_code = $columns['po_bill_state_code'];
        $po_bill_gstin_no = $columns['po_bill_gstin_no'];

        //get bill country
        $po_sql_c = "select country_value from country where country_id in (select country_id from state where state_value=:state_value) ";
        $command = $connection->createCommand($po_sql_c)->bindValue(":state_value", $po_bill_state);
        $po_countryitem = $command->queryOne();
        if ($po_countryitem)
            $po_bill_country = $po_countryitem['country_value'];
        else
            $po_bill_country = '';

        ////warehouse_country
        $sql_c = "select country_value from country where country_id in (select country_id from state where state_value=:state_value) ";
        $command = $connection->createCommand($sql_c)->bindValue(":state_value", $warehouse_state);
        $countryitem = $command->queryOne();
        if ($countryitem)
            $warehouse_country = $countryitem['country_value'];
        else
            $warehouse_country = '';

        $payment_terms = $columns['payment_terms'];
        $basic_cp = "₹ " . (number_format($columns['basic_cp'], 2) ?? 0);
        $total_cgst_amount = "₹ " . (number_format($columns['total_cgst_amount'], 2) ?? 0);
        $total_sgst_amount = "₹ " . (number_format($columns['total_sgst_amount'], 2) ?? 0);
        $total_igst_amount = "₹ " . (number_format($columns['total_igst_amount'], 2) ?? 0);
        $tcs_percentage = (number_format($columns['tcs_percentage'], 2) ?? 0);
        $tcs_amount = "₹ " . (number_format($columns['tcs_amount'], 2) ?? 0);
        // $total_amount_main = "₹ " . (number_format($columns['total_amount'], 2) ?? 0);
        $total_amount_main = "₹ " . (number_format($columns['grand_total'], 2) ?? 0);
        $amtinwords = $this->numberToWords($columns['total_amount']);
        //added round of on 14 oct 2025
        $round_off_value = (float) ($columns['round_off'] ?? 0);
        $round_off = "₹ " . number_format($round_off_value, 2);


        // Set image path (make sure the image path is accessible by TCPDF)
        $logoPath = $deshwal_logo; // Place header.png in TCPDF's images directory

        $tplVars = [
            'purchase_order_no'    => $purchase_order_no,
            'purchase_order_date'  => $todaydate,
            'vendor_name'          => $acc_name,
            'contact_name'         => $contact_name,
            'warehouse_city'       => $warehouse_city,
            'warehouse_state'      => $warehouse_state,
            'warehouse_state_code' => $warehouse_state_code,
            'total_amount'         => $total_amount_main,
            'amount_in_words'      => $amtinwords,
            'payment_terms'        => $payment_terms,
            'quote_no'             => $quotation_no,
            'logoPath'             => $logoPath
        ];

        $isWaterMark = ($po_stage != 3);

        $pdf = new MyPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Deshwal Waste Management');
        $pdf->SetTitle('Purchase Order');
        $pdf->SetSubject('Purchase Order PDF');
        $pdf->SetKeywords('TCPDF, PDF, purchase order');
        $pdf->SetMargins(10, 10, 10);  
        $pdf->SetAutoPageBreak(true, 25);
        $pdf->SetFont('dejavusans', '', 9);

        PdfHeaderFooterHelper::setupPdfWithTemplate(
            $pdf,
            (int)$this->TabId,
            $tplVars,
            'purchase_order',
            $isWaterMark
        );

        $pdf->AddPage();
        $pdf->drawHeaderContent();
        // $pdf->drawStamp();
        $html = <<<EOD
<table border="0" cellpadding="0" cellspacing="2" class="border tab1" style="font-size:7px">
 <tr>
        <td width="25%" >Purchase Order#<br>Date<br>Payment Terms</td>
        <td width="25%" class="border-right " style="border-right: 0.5mm solid #d4d4d7;"><strong>:$purchase_order_no<br>:$todaydate<br>:$payment_terms</strong></td>
        <td width="25%">Place of Supply<br>PO Expiry Date:<br>Reference Quotation No:</td>
        <td width="25%"><strong>:$warehouse_state ($warehouse_state_code)</strong><br>:$expiry_date<br>:$quotation_no</td>
 </tr>
</table>

<table border="0" cellpadding="0" cellspacing="2" class="border" style="border: 0.5mm solid #d4d4d7;" style="font-size:7px">
<tr style="background-color:#f2f2f2;" class="border" style="border: 0.5mm solid #d4d4d7;">
  <td width="33%" align="left" class="border border-right  border-bottom"  style="border-right: 0.5mm solid #d4d4d7;border-bottom: 0.5mm solid #d4d4d7;"><b>Client / Vendor Address</b></td>
  <td width="33%" align="left" class="border border-right  border-bottom"  style="border-right: 0.5mm solid #d4d4d7;border-bottom: 0.5mm solid #d4d4d7;"><b>Delivery Address</b></td>
  <td width="34%" align="left" class="border border-bottom" style="border-bottom: 0.5mm solid #d4d4d7;"><b>Bill to Address</b></td>
</tr>
<tr>
  <td width="33%" class="border-right " style="border-right: 0.5mm solid #d4d4d7;"><strong>$acc_name</strong><br>$bill_address<br>$bill_city, $bill_state, $bill_country $bill_pincode.<br><b>GSTIN: $bill_gstin_no_uin</b></td>
  <td width="33%" class="border-right " style="border-right: 0.5mm solid #d4d4d7;">Deshwal Waste Management Pvt. Ltd. <br>$warehouse_address<br>$warehouse_city, $warehouse_state, $warehouse_country $warehouse_pincode<br><b>GSTIN: $warehouse_gstin_no</b></td>
  <td width="34%">Deshwal Waste Management Pvt. Ltd. <br>$po_bill_address<br>$po_bill_city, $po_bill_state, $po_bill_country $po_bill_pin_code <br><b>GSTIN: $po_bill_gstin_no</b></td>
</tr>
</table>

<table border="0" cellpadding="3" cellspacing="0" style="font-size:7px">
<tr style="background-color:#f2f2f2; font-weight:bold;">
  <td width="5%"  class="border" style="border: 0.5mm solid #d4d4d7;" align="center">#</td>
  <td width="16%" class="border" style="border: 0.5mm solid #d4d4d7;">Product Name</td>
  <td width="14%" class="border" style="border: 0.5mm solid #d4d4d7;">Asset Condition</td>
  <td width="8%"  class="border" style="border: 0.5mm solid #d4d4d7;">HSN Code</td>
  <td width="8%"  class="border" style="border: 0.5mm solid #d4d4d7;" alignt="right">Unit Price</td>
  <td width="8%"  class="border" style="border: 0.5mm solid #d4d4d7;" align="right">Qty</td>
  <td width="8%"  class="border" style="border: 0.5mm solid #d4d4d7;">UOM</td>
  <td width="7%"  class="border" style="border: 0.5mm solid #d4d4d7;" align="right">CGST%</td>
  <td width="7%"  class="border" style="border: 0.5mm solid #d4d4d7;" align="right">SGST%</td>
  <td width="7%"  class="border" style="border: 0.5mm solid #d4d4d7;" align="right">IGST%</td>
  <td width="12%" class="border" style="border: 0.5mm solid #d4d4d7;" align="right">Total Amount</td>
</tr>
EOD;

        $i        = 1;
        $totalqty = 0;
        foreach ($quoteitems as $value) {
            $productid        = $value['p_product_name'];
            $working_condition = $value['working_conditions'];
            $hsn_code         = $value['hsn_code'];
            $cost_price       = number_format($value['cost_price'], 2);
            $quantity         = $value['quantity'];
            $uom              = $value['uom'];
            $cgst_percent     = ($value['cgst'] == 0) ? '-' : $value['cgst'];
            $sgst_percent     = ($value['sgst'] == 0) ? '-' : $value['sgst'];
            $igst_percent     = ($value['igst'] == 0) ? '-' : $value['igst'];
            $total_amount     = number_format($value['total'], 2);
            $totalqty        += $quantity;

            $html .= <<<EOD
<tr>
  <td align="center" class="border" style="border: 0.5mm solid #d4d4d7;">$i</td>
  <td class="border" style="border: 0.5mm solid #d4d4d7;">$productid</td>
  <td class="border" style="border: 0.5mm solid #d4d4d7;">$working_condition</td>
  <td align="right" class="border" style="border: 0.5mm solid #d4d4d7;">$hsn_code</td>
  <td align="right" class="border" style="border: 0.5mm solid #d4d4d7;">$cost_price</td>
  <td align="right" class="border" style="border: 0.5mm solid #d4d4d7;">$quantity</td>
  <td class="border" style="border: 0.5mm solid #d4d4d7;">$uom</td>
  <td align="right" class="border" style="border: 0.5mm solid #d4d4d7;">$cgst_percent</td>
  <td align="right" class="border" style="border: 0.5mm solid #d4d4d7;">$sgst_percent</td>
  <td align="right" class="border" style="border: 0.5mm solid #d4d4d7;">$igst_percent</td>
  <td align="right" class="border" style="border: 0.5mm solid #d4d4d7;">$total_amount</td>
</tr>
EOD;
            $i++;
        }

        $totalqty = number_format($totalqty, 2);

        $html .= <<<EOD
<tr>
  <td width="65%" style="font-size:7px" class="border" style="border: 0.5mm solid #d4d4d7;" rowspan="2">
    Items in Total $totalqty<br><br><strong>Terms & Conditions:</strong><br>$terms_and_conditions
  </td>
  <td width="35%" class="border" style="border: 0.5mm solid #d4d4d7;">
    <table>
      <tr><td width="45%">Sub Total</td><td width="10%">:</td><td class="right" style="text-align:right;" width="45%">$basic_cp</td></tr>
      <tr><td>CGST</td><td>:</td><td class="right" style="text-align:right;">$total_cgst_amount</td></tr>
      <tr><td>SGST</td><td>:</td><td class="right" style="text-align:right;">$total_sgst_amount</td></tr>
      <tr><td>IGST</td><td>:</td><td class="right" style="text-align:right;">$total_igst_amount</td></tr>
      <tr><td>TCS %</td><td>:</td><td class="right" style="text-align:right;">$tcs_percentage</td></tr>
      <tr><td>TCS Amount</td><td>:</td><td class="right" style="text-align:right;">$tcs_amount</td></tr>
      <tr><td>Round Off</td><td>:</td><td class="right" style="text-align:right;">$round_off</td></tr>
      <tr style="font-weight:bold"><td>Total</td><td>:</td><td class="right" style="text-align:right;">$total_amount_main</td></tr>
    </table>
  </td>
</tr>
<tr>
  <td class="border" style="border: 0.5mm solid #d4d4d7;" align="center">
    Deshwal Waste Management Pvt Ltd<br>
    <img src="$deshwal_stamp" height="100"><br>
    Aurthorized Signature
  </td>
</tr>
</table>
<br><br>
EOD;
        // if(isset($pdf->stampUrl) && $pdf->stampUrl != ''){
        //     $vars['deshwal_stamp'] = $pdf->stampUrl;
        //     $html = $this->renderPdfTemplateVars($html, $vars);
        // }
        $pdf->writeHTML($html, true, false, true, false, '');
        // $filename = $purchase_order_no.'_PO_deshwal_'.$todaydate.'.pdf';
        // $pdf->Output($filename, 'I');

        //added on 16oct 2025 for saving PO PDF in server by deep
        $filename = $purchase_order_no . '_PO_deshwal.pdf';
        $saveDir = Yii::getAlias('@webroot/uploads/purchase_orders/');
        $savePath = $saveDir . $filename;

        // Ensure directory exists
        if (!file_exists($saveDir)) {
            mkdir($saveDir, 0775, true);
        }

        // ✅ Delete old file if exists
        if (file_exists($savePath)) {
            unlink($savePath);
        }

        // ✅ Save new PDF
        $pdf->Output($savePath, 'F');
        @chmod($savePath, 0644); // ensure readable from frontend

        // Show in browser
        $pdf->Output($filename, 'I');
    }

    public function actionGetmargin()
    {
        $data = $_POST;
        $sourcingdealId = Yii::$app->request->post('sourcingdealId');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("SELECT margin,margin_percentage from sourcingdeal WHERE  deleted = 0  AND sourcingdeal_id = " . $sourcingdealId . " limit 1");
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

    public function actionGetsourcingdealname()
    {
        $data = $_POST;
        $sourcingdealId = Yii::$app->request->post('sourcingdealId');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("SELECT deal_name as sourcing_deal_name from sourcingdeal WHERE  deleted = 0  AND sourcingdeal_id = " . $sourcingdealId . " limit 1");
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
}