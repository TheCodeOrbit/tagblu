<?php

namespace backend\modules\pickup\controllers;

use common\controllers\ModuleController;
use common\components\TcpdfHelper;
use app\models\EditModel;
use app\models\Pickup;
use backend\models\AccessCheck;
use DateTime;
use Yii;
use yii\base\Exception;
/**
 * Default controller for the `grn` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout='multiple';
    public $ModuleName='pickup';
    public $FieldId='pickup_id';
    public $TableName='pickup';
    public $TabLabel='Pickup';

   
    public $TabId='24';
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

    public function actionGetvendor()
    {   
        $data = $_POST;
        $productItems = [];
        $opportuity_name1 = Yii::$app->request->post('opportuity_name1');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("SELECT  vendor_account_name,acc_name FROM sourcingdeal inner join vendor_account on vendor_account.vendoraccid=sourcingdeal.vendor_account_name WHERE sourcingdeal_id = :sourcingdeal_id")
        ->bindValue(":sourcingdeal_id", $opportuity_name1);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            $vendor = $columns;
            // Products /assets
            // first find sourcing deal id than its related product_costing_detail
            $command = $connection
            ->createCommand("SELECT * FROM product_costing WHERE related_to = :related_to and related_to_id=:related_to_id and deleted=0 order by product_costing_id desc limit 1")//change on date 06-01-26 to get first product
            ->bindValue(":related_to", 51)
            ->bindValue(":related_to_id", $opportuity_name1);
            $productCostingData = $command->queryOne();
            if(!empty($productCostingData)){
                $product_costing_id = $productCostingData['product_costing_id'];
                $columns["product_costing_id"] = $product_costing_id;
                $command = $connection
                ->createCommand("SELECT * FROM product_costing_detail WHERE product_costing_id = :product_costing_id")
                ->bindValue(":product_costing_id", $product_costing_id);
                $productItems = $command->queryAll();
                foreach($productItems as $key=>$val){
                    $productid = $val["productid"]??"";
                    if($productid){
                        //get product name
                        $command = $connection
                        ->createCommand("SELECT product_name FROM products WHERE products_id = :products_id")
                        ->bindValue(":products_id", $productid);
                        $productData = $command->queryOne();
                        $productItems[$key]['product_name'] = $productData['product_name']??"";
                    }
                }
            }
            
            
            return $this->asJson([
                'status' => 'success',
                'data' => [
                    'account' => $columns,
                    'related' => $productItems??[]
                ]
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Vendor found.',
                'data'=>''
            ]);
        }
    }
    public function actionGetvendoraddress()
    {
        $data = $_POST;
        $account_name1 = Yii::$app->request->post('account_name1');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("SELECT address,city_name,state_value as state,pincode 
        FROM vendor_locations 
        left join city on city.cityid = vendor_locations.city 
        left join state on state.state_id = vendor_locations.state 
        WHERE vendorloc_id = :vendoraccid")->bindValue(":vendoraccid", $account_name1);
        $columns = $command->queryOne();
        if (!empty($columns)) {
              return $this->asJson([
                    'status' => 'success',
                    'data' => $columns,
                ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Vendor Address found.',
                'data'=>''
            ]);
        }

    }
    public function actionGetwarehouseaddress()
    {
        $data = $_POST;
        $warehouse = Yii::$app->request->post('warehouse');
        $connection = Yii::$app->db;
        $command = $connection->createCommand("SELECT address,city_name,state,pincode FROM  warehouse  left join city on city.cityid = warehouse.city WHERE warehouse_id = :warehouse_id")->bindValue(":warehouse_id", $warehouse);
        $columns = $command->queryOne();
        if (!empty($columns)) {
              return $this->asJson([
                    'status' => 'success',
                    'data' => $columns,
                ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No warehouse found.',
                'data'=>''
            ]);
        }

    }
    public function actionGetusernumber()
    {
        $data = $_POST;
        $deshwal_spoc_name = Yii::$app->request->post('deshwal_spoc_name');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                        SELECT  mobile FROM  user   WHERE id  = :userid ")->bindValue(":userid", $deshwal_spoc_name);
        $columns = $command->queryOne();

        if (!empty($columns)) {
             return $this->asJson([
                    'status' => 'success',
                    'data' => $columns['mobile'],
                ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Vendor Address found.',
                'data'=>''
            ]);
        }
    }
    
    public function actionGetspoc()
    {   
        $data = $_POST;
        $record_id = Yii::$app->request->post('spoc');
        $account_name = "";
        $spoc_name = "";
        $bill_to_location = "";

        $connection = Yii::$app->db;
        $command = $connection
        ->createCommand("SELECT * FROM contacts WHERE contacts_id = :record_id")
        ->bindValues([":record_id"=> $record_id]);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => [
                    'spoc_mobile' => $columns['mobile']??"",
                    'spoc_email' => $columns['email']??"",
                ]
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Contact found.',
                'data'=>''
            ]);
        }
    }
    public function actionGetuserdetails()
    {   
        $data = $_POST;
        $record_id = Yii::$app->request->post('user');
        $account_name = "";
        $spoc_name = "";
        $bill_to_location = "";

        $connection = Yii::$app->db;
        $command = $connection
        ->createCommand("SELECT first_name,last_name,email,mobile FROM user WHERE id = :record_id")
        ->bindValues([":record_id"=> $record_id]);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Contact found.',
                'data'=>''
            ]);
        }
    }

    public function getVendorLocationAddress($connection,$location_id){
        if(empty($location_id)) return "";
        $location_query = $connection->createCommand("SELECT * FROM vendor_locations WHERE vendorloc_id = :vendorloc_id")->bindValues([":vendorloc_id"=> $location_id]);
        $location_data = $location_query->queryOne();
        if(empty($location_data)) return "";
        $address_string = "";
        $location_name = $location_data["vendor_loc_name"]??"";
        $plot_number = $location_data["plot_number"]??"";
        $building_name = $location_data["building_name"]??"";
        $area_sector_name = $location_data["area_sector_name"]??"";
        $floor = $location_data["floor"]??"";
        $pincode = $location_data["pincode"];
        if(!empty($floor)){
            $address_string .= "Floor ".$floor.", ";
        }
        if(!empty($plot_number)){
            $address_string .= "Plot No. ".$plot_number.", ";
        }
        if(!empty($building_name)){
            $address_string .= $building_name.", ";
        }
        if(!empty($area_sector_name)){
            $address_string .= "Area/Sector ".$area_sector_name.", ";
        }
        $country_id = $location_data["country"]??"";
        $state_id = $location_data["state"]??"";
        $city_id = $location_data["city"]??"";
        if(!empty($city_id)){
            $city_name = $this->getCityName($connection,$city_id);
            if(!empty($city_name)){
                $address_string .= $city_name.", ";
            }
        }
        if(!empty($state_id)){
            $state_name = $this->getStateName($connection,$state_id);
            if(!empty($state_name)){
                $address_string .= $state_name.", ";
            }
        }
        if(!empty($country_id)){
            $country_name = $this->getCountryName($connection,$country_id);
            if(!empty($country_name)){
                $address_string .= $country_name.", ";
            }
        }
        if(!empty($pincode)){
            $address_string .= "PIN ".$pincode;
        }
        return trim(trim($address_string),",");
    }
    public function getVendorAcountAddress($connection,$acount_id){
        if(empty($acount_id)) return "";
        $account_query = $connection->createCommand("SELECT * FROM vendor_account WHERE vendoraccid = :vendoraccid")->bindValues([":vendoraccid"=> $acount_id]);
        $account_data = $account_query->queryOne();
        if(empty($account_data)) return "";
        $address_string = "";
        $account_name = $account_data["acc_name"]??"";
        
        $country_id = $account_data["country"]??"";
        $state_id = $account_data["state"]??"";
        $city_id = $account_data["HO_city"]??"";
        if(!empty($city_id)){
            $city_name = $this->getCityName($connection,$city_id);
            if(!empty($city_name)){
                $address_string .= $city_name.", ";
            }
        }
        if(!empty($state_id)){
            $state_name = $this->getStateName($connection,$state_id);
            if(!empty($state_name)){
                $address_string .= $state_name.", ";
            }
        }
        if(!empty($country_id)){
            $country_name = $this->getCountryName($connection,$country_id);
            if(!empty($country_name)){
                $address_string .= $country_name.", ";
            }
        }
        if(!empty($pincode)){
            $address_string .= "PIN ".$pincode;
        }
        return trim(trim($address_string),",");
    }
    public function getCityName($connection,$id){
        if(empty($id)) return "";
        $query = $connection->createCommand("SELECT * FROM city WHERE cityid = :id")->bindValues([":id"=> $id]);
        $data = $query->queryOne();
        if(empty($data)) return "";
        return $data["city_name"]??"";
    }
    public function getStateName($connection,$id){
        if(empty($id)) return "";
        $query = $connection->createCommand("SELECT * FROM state WHERE state_id = :id")->bindValues([":id"=> $id]);
        $data = $query->queryOne();
        if(empty($data)) return "";
        return $data["state_value"]??"";
    }
    public function getCountryName($connection,$id){
        if(empty($id)) return "";
        $query = $connection->createCommand("SELECT * FROM country WHERE country_id = :id")->bindValues([":id"=> $id]);
        $data = $query->queryOne();
        if(empty($data)) return "";
        return $data["country_value"]??"";
    }
    public function getFinancialYear($date = null) {
        $date = $date ? new DateTime($date) : new DateTime();
        $year = (int) $date->format('Y');
        $month = (int) $date->format('m');
        if ($month < 4) {
            $startYear = $year - 1;
            $endYear = $year;
        } else {
            $startYear = $year;
            $endYear = $year + 1;
        }
        return $startYear . '-' . substr($endYear, -2);
    }

    public function getVehicleNumber($connection,$pickup_id){
        if(empty($pickup_id)) return "";
        $query = $connection->createCommand("SELECT vehicle_number FROM shipped_details WHERE pickup_id = :id")->bindValues([":id"=> $pickup_id]);
        $data = $query->queryAll();
        if(empty($data)) return "";
        return implode(", ", array_column($data, "vehicle_number"));
    }

    public function generateSequenceNo($connection,$semodule){
        $qry1 = "SELECT * from modentity_num  where semodule ='$semodule'";
        $command = $connection->createCommand($qry1);
        $data = $command->queryOne();
        if(empty($data)) return "";
        $generated_id = $data["cur_id"];
        
        Yii::$app->db->createCommand("UPDATE modentity_num SET cur_id = cur_id + 1 WHERE semodule = :semodule")
        ->bindValue(':semodule', $semodule)->execute();
        return $generated_id??"";
    }

    public function actionGenerateformsix($Record){
        $record_id = $Record;//"74";//Yii::$app->request->post('Record');
        $account_name = "";
        $pickup_location_name = "";
        $pickup_address = "";
        $transporter_name = "";
        $transporter_address = "";
        $vehicle_type = "";
        $transporter_gst = "";
        $vehicle_nos = "";
        $pickup_no = "";
        
        $deshwal_logo = Yii::getAlias('@webroot/images/deshwal_stamp.png');
        $connection = Yii::$app->db;
        $command = $connection->createCommand("SELECT * FROM pickup WHERE pickup_id = :record_id")->bindValues([":record_id"=> $record_id]);
        $pickup_data = $command->queryOne();
        if(!empty($pickup_data)){
            $pickup_account_id = $pickup_data["account_name"];
            $pickup_no = $pickup_data["pickup_no"];
            if(!empty($pickup_account_id)){
                $pickup_account_query = $connection->createCommand("SELECT acc_name FROM vendor_account WHERE vendoraccid = :vendoraccid")->bindValues([":vendoraccid"=> $pickup_account_id]);
                $pickup_account_data = $pickup_account_query->queryOne();
                $account_name = $pickup_account_data["acc_name"]??"";
            }
            $pickup_location_id = $pickup_data["pickup_location"];
            $pickup_address = $pickup_data["pickup_address"]??"";
            $spoc_number = $pickup_data["spoc_number"]??"";
            $spoc_email = $pickup_data["spoc_email"]??"";
            if(!empty($pickup_location_id)){
                $pickup_complete_address = $this->getVendorLocationAddress($connection,$pickup_location_id);
                if(empty($pickup_complete_address)){
                    $pickup_complete_address = $pickup_address;
                }
                $pickup_address = $pickup_location_name.", ".$pickup_complete_address;
                if(!empty($spoc_number)) $pickup_address = $pickup_address." Phone: ".$spoc_number;
                if(!empty($spoc_email)) $pickup_address = $pickup_address." Email: ".$spoc_email;
            }

            //transporter name and address
            $transporter_command = $connection->createCommand("SELECT * FROM shipped_details WHERE pickup_id = :record_id")->bindValues([":record_id"=> $record_id]);
            $transporter_data = $transporter_command->queryOne();
            if(!empty($transporter_data)){
                $transporter_id = $transporter_data["transporter_name"]??"";
                $vehicle_size = $transporter_data["vehicle_size"]??"";
                if($transporter_id){
                    $transporter_account_query = $connection->createCommand("SELECT * FROM vendor_account WHERE vendoraccid = :vendoraccid")->bindValues([":vendoraccid"=> $transporter_id]);
                    $transporter_account_data = $transporter_account_query->queryOne();
                    $transporter_name = $transporter_account_data["acc_name"]??"";
                    $transporter_address = $transporter_account_data["address"]??"";
                    $transporter_gst = $transporter_account_data["gst_number"]??"";
                    $transporter_phone = $transporter_account_data["phone"]??"";
                    $transporter_email = $transporter_account_data["email"]??"";
                    if(!empty($transporter_phone)) $transporter_address = $transporter_address." Phone: ".$transporter_phone;
                    if(!empty($transporter_email)) $transporter_address = $transporter_address." Email: ".$transporter_email;
                    //$transporter_address = $this->getVendorAcountAddress($connection,$transporter_id);
                    // if(!empty($transporter_name)){
                    //     $transporter_address_query = $connection->createCommand("SELECT vendor_loc_name,address,gstin_no_uin FROM vendor_locations WHERE vendorloc_id = :vendorloc_id")->bindValues([":vendorloc_id"=> $transporter_id]);
                    //     $transporter_address_data = $transporter_address_query->queryOne();
                    //     $transporter_address = $transporter_address_data["address"]??"";
                    //     $transporter_gst = $transporter_address["gstin_no_uin"]??"";
                    // }
                }
                if(!empty($vehicle_size)){
                    $vehicle_type_command = $connection->createCommand("SELECT vehiclesize_value FROM pick_vehicle_size WHERE vehiclesizeid = :vehiclesizeid")->bindValues([":vehiclesizeid"=> $vehicle_size]);
                    $vehicle_type_data = $vehicle_type_command->queryOne();
                    $vehicle_type = $vehicle_type_data["vehiclesize_value"]??"";
                }
                $vehicle_nos = $this->getVehicleNumber($connection,$record_id);
            }
        }
        // start new
        //get pickup assets
        $asset_name_qty_text = "";
        $waste_category = "";
        $total_picked_qty = 0;
        $assets_total_weight = 0;
        $assets = [];
        $assets_command = $connection->createCommand("SELECT * FROM pickup_asset_detail WHERE pickup_id = :record_id and deleted=0")->bindValues([":record_id"=> $record_id]);
        $pickup_assets = $assets_command->queryAll();
        foreach($pickup_assets as $i => $pa){
            //if(empty($waste_category)) $waste_category = $pa["category"];
            $picked_qty = (int)$pa["picked_qty"];
            if(empty($picked_qty)) $picked_qty = 0;
            $total_picked_qty += $picked_qty;
            $product_data = $this->product_data($connection,$pa["porduct_name"]);
            $product_name = $product_data["product_name"];
            $product_weight = $product_data["weight_kg"];
            if(isset($assets[$product_name])){
                $assets[$product_name] = $assets[$product_name] + $picked_qty;
            }else{
                $assets[$product_name] = $picked_qty;
            }
            $assets_total_weight += ($product_weight * $picked_qty);
        }
        foreach($assets as $k=>$v){
            $asset_name_qty_text .= $v." ".$k.", ";
        }
        $asset_name_qty_text = trim(trim($asset_name_qty_text),",");
        // replace this in description of E-wate <td><br><br><br><br>&nbsp;</td>
        // end new
        $waste_catgory_sql = "SELECT products.waste_catagory,
            prod_waste_catagory.waste_catagory_value
            FROM pickup_asset_detail 
            left join products on products.products_id = pickup_asset_detail.porduct_name 
            left join prod_waste_catagory on prod_waste_catagory.waste_catagory_id = products.waste_catagory
            WHERE pickup_asset_detail.pickup_id = :record_id and pickup_asset_detail.deleted=0";
        $assets_waste_cat_command = $connection->createCommand($waste_catgory_sql)->bindValues([":record_id"=> $record_id]);
        $pickup_assets_waste_categories = $assets_waste_cat_command->queryAll();
        if(empty($pickup_assets_waste_categories)) $pickup_assets_waste_categories = [];
        foreach($pickup_assets_waste_categories as $i => $pa){
            if(empty($waste_category)) $waste_category = $pa["waste_catagory_value"];
        }
        $financial_year = $this->getFinancialYear();
        $sequence_no = $this->generateSequenceNo($connection,'pickup_form6');
        $manifestation_no = $this->generateSequenceNo($connection,'pickup_form6_manifest');
        if($sequence_no){
            $pdf_file_name = "$pickup_no^Form-6^$sequence_no.pdf";
        }else{
            $pdf_file_name = "$pickup_no^Form-6.pdf";
        }
        $pdf = new TcpdfHelper('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetMargins(10, 8, 10);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 9);

        // Form Details
    
        $html = <<<EOD
        <div style="text-align:center;margin:0px;padding:0px;line-height:0.8">
            <h2 style="line-height:0.8">Form-6</h2>
            <p style="line-height:0.8">[See rule 19]</p>
            <h2 style="line-height:0.8">E-WASTE MANIFEST</h2>
        </div>
        <table border="1" cellpadding="5">
            <tr>
                <td width="5%"><b>1</b></td>
                <td width="45%">Sender's Name and Mailing Address (Including Phone No.)</td>
                <td width="50%">{$account_name} {$pickup_address}</td>
            </tr>
            <tr>
                <td><b>2</b></td>
                <td>Sender's Authorization No., if applicable:</td>
                <td></td>
            </tr>
            <tr>
                <td><b>3</b></td>
                <td>Manifest Document No:</td>
                <td>MDN/Manesar/{$financial_year}/{$manifestation_no}</td>
            </tr>
            <tr>
                <td><b>4</b></td>
                <td>Transporter's Name and Address (Including Phone No.):</td>
                <td>{$transporter_name}, {$transporter_address}</td>
            </tr>
            <tr>
                <td><b>5</b></td>
                <td>Type of Vehicle:</td>
                <td>{$vehicle_type}</td>
            </tr>
            <tr>
                <td><b>6</b></td>
                <td>Transporter's Registration No:</td>
                <td>{$transporter_gst}</td>
            </tr>
            <tr>
                <td><b>7</b></td>
                <td>Vehicle Registration No:</td>
                <td>{$vehicle_nos}</td>
            </tr>
            <tr>
                <td><b>8</b></td>
                <td>Receiver's Name & Address:</td>
                <td>
                    <b>DESHWAL WASTE MANAGEMENT PVT. LTD.</b><br>
                    Plot No. 15, Sector-5, IMT Manesar,<br>
                    Gurugram, Haryana-122050 | M: 9910048342<br>
                    E-mail: compliance@dwmpl.com
                </td>
            </tr>
            <tr>
                <td><b>9</b></td>
                <td>Receiver's Authorization No., if applicable:</td>
                <td>As per Invoice/Annexure</td>
            </tr>
            <tr>
                <td><b>10</b></td>
                <td>Description of E-Waste (Item, Weight/Numbers):</td>
                <td>Approx. ($assets_total_weight) kg of $waste_category ($asset_name_qty_text)</td>
            </tr>
            <tr>
                <td rowspan="2"><b>11</b></td>
                <td colspan="2">Name and stamp of Sender* (Manufacturer or Producer or Bulk Consumer or Collection Centre or Refurbisher or Dismantler):</td>
            </tr>
            <tr>
                <td colspan="2"><br><br><br>
                    <table width="100%">
                        <tr>
                            <td width="33%" align="left"><b>Name & Stamp:</b></td>
                            <td width="33%" align="center"><b>Signature:</b></td>
                            <td width="33%" align="right"><b>Month/Day/Year</b></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td rowspan="2"><b>12</b></td>
                <td colspan="2">Transporter acknowledgement of receipt of E-Wastes</td>
            </tr>
            <tr>
                <td colspan="2">
                    <table width="100%">
                        <tr>
                            <td width="33%" align="left"><img src="{$deshwal_logo}" width="50"><br><b>Name & Stamp:</b></td>
                            <td width="33%" align="center"><b><br><br><br><br>Signature:</b></td>
                            <td width="33%" align="right"><br><br><br><br><br><b>Month/Day/Year</b></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td rowspan="2"><b>13</b></td>
                <td>Receiver* (Collection Centre or Refurbisher or Dismantler or Recycler) certification of receipt of E-waste</td>
                <td>We hereby declare that the contents of the consignment are fully and accurately described above by proper shipping name, categorized, packed, marked, labeled, and are in proper condition for transport by road according to applicable national government regulations.</td>
            </tr>
            <tr>
                <td colspan="2">
                    <table width="100%">
                        <tr>
                            <td width="33%" align="left"><img src="{$deshwal_logo}" width="50"><br><b>Name & Stamp:</b></td>
                            <td width="33%" align="center"><br><br><br><br><br><b>Signature:</b></td>
                            <td width="33%" align="right"><br><br><br><br><br><b>Month/Day/Year</b></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table border="1" cellpadding="5">
            <tr>
                <td width="15%"><b>Yellow Copy:</b></td>
                <td width="85%">To be retained by the sender after taking signature from the transporter. Other copies will be carried by the transporter.</td>
            </tr>
        </table>
        EOD;

        $pdf->writeHTML($html, true, false, true, false, '');
        $sr_no = <<<EOD
        Sr. No. FY {$financial_year}/ {$sequence_no}
        EOD;
        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '140', $y = '23', $sr_no, $border = 0, $ln = 1, $fill = 0, $reset = true, $align = '', $autopadding = true);
        //$pdf->Output('form6-ewaste.pdf', 'I');
        //exit;
        $pdfData = $pdf->Output('', 'S'); // 'S' returns PDF data as a string
        $base64Pdf = base64_encode($pdfData);

        // Save PDF to disk and database
        $new_attributes = ['form6_unsigned_copy'=>$pdf_file_name];
        $result = EditModel::saveGeneratedFiles($base64Pdf, $pdf_file_name,$record_id,'pickup',$pickup_data, $new_attributes);

        if (!$result['success']) {
            return json_encode($result); // Return error response
        }
        // PDF successfully saved, now serve it to user
        $attachment_id = $result["fileName"];
        $sql = "update pickup set form6_unsigned_copy = :form6_unsigned_copy,modifiedtime = :modifiedtime,modifiedby = :modifiedby where pickup_id = :id";
        Yii::$app->db->createCommand($sql)
            ->bindValue(":form6_unsigned_copy", $attachment_id)
            ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
            ->bindValue(":modifiedby", Yii::$app->user->id)
            ->bindValue(":id", $Record)
            ->execute();
        return $this->redirect(['download', 'fileid' => $attachment_id]);
    }

    public function actionGenerateformten($Record){
        $record_id = $Record;//"74";//Yii::$app->request->post('Record');
        $account_name = "";
        $pickup_location_name = "";
        $pickup_address = "";
        $transporter_name = "";
        $transporter_address = "";
        $vehicle_type = "";
        $transporter_gst = "";
        $vehicle_nos = "";
        $pickup_no = "";

        $deshwal_logo = Yii::getAlias('@webroot/images/deshwal_stamp.png');
        $connection = Yii::$app->db;
        $command = $connection->createCommand("SELECT * FROM pickup WHERE pickup_id = :record_id")->bindValues([":record_id"=> $record_id]);
        $pickup_data = $command->queryOne();
        if(!empty($pickup_data)){
            $pickup_account_id = $pickup_data["account_name"];
            $pickup_no = $pickup_data["pickup_no"];
            if(!empty($pickup_account_id)){
                $pickup_account_query = $connection->createCommand("SELECT acc_name FROM vendor_account WHERE vendoraccid = :vendoraccid")->bindValues([":vendoraccid"=> $pickup_account_id]);
                $pickup_account_data = $pickup_account_query->queryOne();
                $account_name = $pickup_account_data["acc_name"]??"";
            }
            $pickup_location_id = $pickup_data["pickup_location"];
            $pickup_address = $pickup_data["pickup_address"]??"";
            $spoc_number = $pickup_data["spoc_number"]??"";
            $spoc_email = $pickup_data["spoc_email"]??"";
            if(!empty($pickup_location_id)){
                $pickup_complete_address = $this->getVendorLocationAddress($connection,$pickup_location_id);
                if(empty($pickup_complete_address)){
                    $pickup_complete_address = $pickup_address;
                }
                $pickup_address = $pickup_location_name.", ".$pickup_complete_address;
                if(!empty($spoc_number)) $pickup_address = $pickup_address." Phone: ".$spoc_number;
                if(!empty($spoc_email)) $pickup_address = $pickup_address." Email: ".$spoc_email;
            }

            //transporter name and address
            $transporter_command = $connection->createCommand("SELECT * FROM shipped_details WHERE pickup_id = :record_id")->bindValues([":record_id"=> $record_id]);
            $transporter_data = $transporter_command->queryOne();
            if(!empty($transporter_data)){
                $transporter_id = $transporter_data["transporter_name"]??"";
                $vehicle_size = $transporter_data["vehicle_size"]??"";
                if($transporter_id){
                    $transporter_account_query = $connection->createCommand("SELECT * FROM vendor_account WHERE vendoraccid = :vendoraccid")->bindValues([":vendoraccid"=> $transporter_id]);
                    $transporter_account_data = $transporter_account_query->queryOne();
                    $transporter_name = $transporter_account_data["acc_name"]??"";
                    $transporter_address = $transporter_account_data["address"]??"";
                    $transporter_gst = $transporter_account_data["gst_number"]??"";
                    $transporter_phone = $transporter_account_data["phone"]??"";
                    $transporter_email = $transporter_account_data["email"]??"";
                    if(!empty($transporter_phone)) $transporter_address = $transporter_address." Phone: ".$transporter_phone;
                    if(!empty($transporter_email)) $transporter_address = $transporter_address." Email: ".$transporter_email;
                    //$transporter_address = $this->getVendorAcountAddress($connection,$transporter_id);
                    // if(!empty($transporter_name)){
                    //     $transporter_address_query = $connection->createCommand("SELECT vendor_loc_name,address,gstin_no_uin FROM vendor_locations WHERE vendorloc_id = :vendorloc_id")->bindValues([":vendorloc_id"=> $transporter_id]);
                    //     $transporter_address_data = $transporter_address_query->queryOne();
                    //     $transporter_address = $transporter_address_data["address"]??"";
                    //     $transporter_gst = $transporter_address["gstin_no_uin"]??"";
                    // }
                }
                if(!empty($vehicle_size)){
                    $vehicle_type_command = $connection->createCommand("SELECT vehiclesize_value FROM pick_vehicle_size WHERE vehiclesizeid = :vehiclesizeid")->bindValues([":vehiclesizeid"=> $vehicle_size]);
                    $vehicle_type_data = $vehicle_type_command->queryOne();
                    $vehicle_type = $vehicle_type_data["vehiclesize_value"]??"";
                }
                $vehicle_nos = $this->getVehicleNumber($connection,$record_id);
            }
        }
        $financial_year = $this->getFinancialYear();
        $sequence_no = $this->generateSequenceNo($connection,'pickup_form10');
        $manifestation_no = $this->generateSequenceNo($connection,'pickup_form6_manifest');
        if($sequence_no){
            $pdf_file_name = "$pickup_no^Form-10^$sequence_no.pdf";
        }else{
            $pdf_file_name = "$pickup_no^Form-10.pdf";
        }
        
        $pdf = new TcpdfHelper('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetMargins(5, 3, 5);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 8.5);

        // Form Details
        $html = <<<EOD
        <div style="text-align:center;margin:0px;padding:0px;line-height:0.8">
            <h3 style="line-height:0.8">Form 10</h3>
            <p style="line-height:0.8">[See rule 19(1)]</p>
            <h3 style="line-height:0.8">MANIFEST FOR HAZARDOUS AND OTHER WASTE</h3>
        </div>
        <table border="1" cellpadding="5">
            <tr>
                <td width="5%"><b>1</b></td>
                <td width="45%">Sender's Name and Mailing address: (Including Phone No. and Email)</td>
                <td width="50%">{$account_name}, {$pickup_address}</td>
            </tr>
            <tr>
                <td><b>2</b></td>
                <td>Sender's Authorization No.</td>
                <td></td>
            </tr>
            <tr>
                <td><b>3</b></td>
                <td>Manifest Documents </td>
                <td>MDN/Manesar/{$financial_year}/{$manifestation_no}</td>
            </tr>
            <tr>
                <td><b>4</b></td>
                <td>Transporter's Name and Address: (Including Phone No. & Email)</td>
                <td>{$transporter_name}, {$transporter_address}</td>
            </tr>
            <tr>
                <td><b>5</b></td>
                <td>Type of Vehicle</td>
                <td>{$vehicle_type}</td>
            </tr>
            <tr>
                <td><b>6</b></td>
                <td>Transporter's Registration No.</td>
                <td>{$transporter_gst}</td>
            </tr>
            <tr>
                <td><b>7</b></td>
                <td>Vehicle Registration No.</td>
                <td>{$vehicle_nos}</td>
            </tr>
            <tr>
                <td><b>8</b></td>
                <td>Receiver's Name & Mailing Address: (Including Phone No. & Email)</td>
                <td>
                    <b>DESHWAL WASTE MANAGEMENT PVT. LTD.</b><br>
                    G1-146 (B), Industrial Area Khushkhera Tehsil Tijara,<br>
                     Distt. Alwar, Rajasthan | Mob:- 9910048342<br>
                    Email - Complaince@dwmpl.com
                </td>
            </tr>
            <tr>
                <td><b>9</b></td>
                <td>Receiver's authorization No.</td>
                <td>RPCB/HWM/2021-2022/HSW/HSW/65</td>
            </tr>
            <tr>
                <td><b>10</b></td>
                <td>Waste Description</td>
                <td></td>
            </tr>
            <tr>
                <td><b>11</b></td>
                <td>Total Quantity<br>No. of Containers</td>
                <td>.............................LTR.<br>.............................MT<br>.............................Nos.</td>
            </tr>
            <tr>
                <td><b>12</b></td>
                <td>Physical Form</td>
                <td>(Solid/Semi-Solid/Sludge/Oily/Tarry/Slurry/Liquid)</td>
            </tr>
            <tr>
                <td><b>13</b></td>
                <td>Special handling instructions and additional information</td>
                <td></td>
            </tr>
            <tr>
                <td rowspan="2"><b>14</b></td>
                <td>Sender's Certificate</td>
                <td>I hereby declare that the contents of the consignment are fully and accurately described above by proper shipping name and are categorized, packed, marked, and labeled, and are in all respects in proper condition for transport by road according to applicable national government regulations.</td>
            </tr>
            <tr>
                <td colspan="2"><br><br>
                    <table width="100%">
                        <tr>
                            <td width="33%" align="left"><b>Name & Stamp:</b></td>
                            <td width="33%" align="center"><b>Signature:</b></td>
                            <td width="33%" align="right"><b>Month/Day/Year</b></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td rowspan="2"><b>15</b></td>
                <td colspan="2">Transporter acknowledgement of receipt of Wastes</td>
            </tr>
            <tr>
                <td colspan="2">
                    <table width="100%">
                        <tr>
                            <td width="33%" align="left"><img src="{$deshwal_logo}" width="45"><br><b>Name & Stamp:</b></td>
                            <td width="33%" align="center"><b><br><br><br><br>Signature:</b></td>
                            <td width="33%" align="right"><br><br><br><br><br><b>Month/Day/Year</b></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td rowspan="2"><b>16</b></td>
                <td>Receiver's certification for receipt of Hazardous and other waste</td>
                <td>We hereby declare that the contents of the consignment are fully and accurately described above by proper shipping name, categorized, packed, marked, labeled, and are in proper condition for transport by road according to applicable national government regulations.</td>
            </tr>
            <tr>
                <td colspan="2">
                    <table width="100%">
                        <tr>
                            <td width="33%" align="left"><img src="{$deshwal_logo}" width="45"><br><b>Name & Stamp:</b></td>
                            <td width="33%" align="center"><br><br><br><br><br><b>Signature:</b></td>
                            <td width="33%" align="right"><br><br><br><br><br><b>Month/Day/Year</b></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                <small>1. White Form forwarded by the sender to SPCB.</small><br>
                <small>2. Yellow Form retained by sender after taking sign from transporter.</small><br>
                <small>3. Pink Form sent by the receiver to the sender.</small>
                </td>
                <td>
                <small>4. Grey Form sent by receiver to the SPCB of the sender in case that is in another state.</small><br>
                <small>5. Blue Form retained by receiver.</small><br>
                <small>6. Orange Form handed over to transporter after accepting waste.</small><br>
                <small>7. Green Form sent by receiver to SPCB.</small>
                </td>
            </tr>
        </table>
        EOD;

        $pdf->writeHTML($html, true, false, true, false, '');
        $sr_no = <<<EOD
        Sr. No. FY {$financial_year}/ {$sequence_no}
        EOD;
        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '152', $y = '14', $sr_no, $border = 0, $ln = 1, $fill = 0, $reset = true, $align = '', $autopadding = true);

        //$pdf->Output($pdf_file_name, 'I');
        //exit;
        $pdfData = $pdf->Output('', 'S'); // 'S' returns PDF data as a string
        $base64Pdf = base64_encode($pdfData);

        // Save PDF to disk and database
        $new_attributes = ['form10_unsigned_copy'=>$pdf_file_name];
        $result = EditModel::saveGeneratedFiles($base64Pdf, $pdf_file_name,$record_id,'pickup',$pickup_data, $new_attributes);

        if (!$result['success']) {
            return json_encode($result); // Return error response
        }
        // PDF successfully saved, now serve it to user
        $attachment_id = $result["fileName"];
        $sql = "update pickup set form10_unsigned_copy = :form10_unsigned_copy,modifiedtime = :modifiedtime,modifiedby = :modifiedby where pickup_id = :id";
        Yii::$app->db->createCommand($sql)
            ->bindValue(":form10_unsigned_copy", $attachment_id)
            ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
            ->bindValue(":modifiedby", Yii::$app->user->id)
            ->bindValue(":id", $Record)
            ->execute();
        return $this->redirect(['download', 'fileid' => $attachment_id]);
    }

    public function product_data($connection,$product_id){
        if(empty($product_id)){
            return ["product_name" =>"","weight_kg" => 0];
        }
        $command = $connection
        ->createCommand("SELECT product_name,weight_kg FROM products WHERE products_id = :products_id")
        ->bindValue(":products_id", $product_id);
        $productData = $command->queryOne();
        return empty($productData)?["product_name" =>"","weight_kg" => 0]:$productData;
    }
    public function actionGenerategreencert($Record){
        $record_id = $Record;//"74";//Yii::$app->request->post('Record');
        $signatory_name = "RAJU YADAV";
        $account_name = "";
        $pickup_location_name = "";
        $pickup_address = "";
        $transporter_name = "";
        $transporter_address = "";
        $vehicle_type = "";
        $transporter_gst = "";
        $legal_entity_name = "";
        $waste_category = "";
        $total_picked_qty = 0;
        $assets = [];
        $asset_name_qty_text = "";
        $assets_total_weight = 0;
        $date2 = date("jS M Y");
        $template_image = Yii::getAlias('@webroot/images/green-certificate-page1.png');
        $other_pages_template_image = Yii::getAlias('@webroot/images/green-certificate-page2.png');
        $connection = Yii::$app->db;
        $command = $connection->createCommand("SELECT * FROM pickup WHERE pickup_id = :record_id")->bindValues([":record_id"=> $record_id]);
        $pickup_data = $command->queryOne();
        if(!empty($pickup_data)){
            $pickup_account_id = $pickup_data["account_name"];
            $pickup_no = $pickup_data["pickup_no"];
            $pickup_id = $pickup_data["pickup_id"];
            if(!empty($pickup_account_id)){
                $pickup_account_query = $connection->createCommand("SELECT acc_name,legal_entity  FROM vendor_account WHERE vendoraccid = :vendoraccid")->bindValues([":vendoraccid"=> $pickup_account_id]);
                $pickup_account_data = $pickup_account_query->queryOne();
                $account_name = $pickup_account_data["acc_name"]??"";
                $legal_entity_name = $pickup_account_data["legal_entity"]??"";
            }
            $pickup_location_id = $pickup_data["pickup_location"];
            $pickup_address = $pickup_data["pickup_address"]??"";
            if(!empty($pickup_location_id)){
                $pickup_complete_address = $this->getVendorLocationAddress($connection,$pickup_location_id);
                if(empty($pickup_complete_address)){
                    $pickup_complete_address = $pickup_address;
                }
                $pickup_address = $pickup_complete_address;
            }
            if(!empty($pickup_data["actual_pickup_date"])){
                $date2 = date("jS M Y", strtotime($pickup_data["actual_pickup_date"]));
            }
            //get pickup assets
            $assets_command = $connection->createCommand("SELECT * FROM pickup_asset_detail WHERE pickup_id = :record_id and deleted=0")->bindValues([":record_id"=> $record_id]);
            $pickup_assets = $assets_command->queryAll();
            foreach($pickup_assets as $i => $pa){
                //if(empty($waste_category)) $waste_category = $pa["category"];
                $picked_qty = (int)$pa["picked_qty"];
                if(empty($picked_qty)) $picked_qty = 0;
                $total_picked_qty += $picked_qty;
                $product_data = $this->product_data($connection,$pa["porduct_name"]);
                $product_name = $product_data["product_name"];
                $product_weight = $product_data["weight_kg"];
                if(isset($assets[$product_name])){
                    $assets[$product_name] = $assets[$product_name] + $picked_qty;
                }else{
                    $assets[$product_name] = $picked_qty;
                }
                $assets_total_weight += ($product_weight * $picked_qty);
            }
            //
        }
        $waste_catgory_sql = "SELECT products.waste_catagory,
            prod_waste_catagory.waste_catagory_value
            FROM pickup_asset_detail 
            left join products on products.products_id = pickup_asset_detail.porduct_name 
            left join prod_waste_catagory on prod_waste_catagory.waste_catagory_id = products.waste_catagory
            WHERE pickup_asset_detail.pickup_id = :record_id and pickup_asset_detail.deleted=0";
        $assets_waste_cat_command = $connection->createCommand($waste_catgory_sql)->bindValues([":record_id"=> $record_id]);
        $pickup_assets_waste_categories = $assets_waste_cat_command->queryAll();
        if(empty($pickup_assets_waste_categories)) $pickup_assets_waste_categories = [];
        foreach($pickup_assets_waste_categories as $i => $pa){
            if(empty($waste_category)) $waste_category = $pa["waste_catagory_value"];
        }
        if(empty($legal_entity_name)) $legal_entity_name = $account_name;
        
        // for ($i = 1; $i <= $total_picked_qty; $i++) {
        //     $assets[] = ["Asset $i", rand(1, 50) . " pcs"];
        // }

        $financial_year = $this->getFinancialYear();
        $sequence_no = $this->generateSequenceNo($connection,'pickup_green_cert');
        if($sequence_no){
            $pdf_file_name = "$pickup_no^Green-Certificate^$sequence_no.pdf";
        }else{
            $pdf_file_name = "$pickup_no^Green-Certificate.pdf";
        }
        foreach($assets as $k=>$v){
            $asset_name_qty_text .= $v." ".$k.", ";
        }
        $asset_name_qty_text = trim(trim($asset_name_qty_text),",");
        $date1 = date("d-M-Y"); 
        
        $offset_adjustment = 10;
        $pdf = new TcpdfHelper('L', 'mm', 'A4', true, 'UTF-8', false);
        // $pdf->SetPrintHeader(false);
        // $pdf->SetPrintFooter(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false);
        $pdf->AddPage();

        // Set background image
        //$pdf->Image($template_image, 0, 0, 297, 210, '', '', '', false, 300, '', false, false, 0);
        $pdf->Image($template_image, 0, 0, 297, 210, '', '', '', false, 300, '', false, false, 0, 'C');
        // Set font
        $pdf->SetFont('helvetica', 'B', 14);

        // Add certificate details
        $pdf->SetTextColor(0, 0, 0); // Black text
        $pdf->SetXY(10, 15);
        $pdf->Cell(0, 10, 'Authorization No.: HSPCB/2022/26946732EWREF00', 0, 1, 'L');

        $pdf->SetXY(10, 26);
        $pdf->Cell(0, 10, "Certificate No.: DWMPL/RC/$financial_year/$sequence_no", 0, 1, 'L');

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetXY(240, 18);
        $pdf->Cell(0, 10, "Issue Date: ".$date1, 0, 1, 'L');

        //-->
        $pdf->SetFont('helvetica', 'B', 20);
        //$pdf->SetTextColor(64, 64, 64); // Dark Gray
        $pdf->SetTextColor(96, 96, 96);
        $pdf->SetXY(0 + $offset_adjustment, 65); // adjust Y as needed
        $pdf->Cell(297, 10, "FOR $waste_category DISPOSAL", 0, 1, 'C');
        //<--
        $pdf->SetTextColor(64, 64, 64);
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->SetXY(0, 75);
        $line1 = "This is to Certify that Approx. ($assets_total_weight) kg of $waste_category ($asset_name_qty_text) was collected Against Manifest dated $date2";
        $pdf->Cell(297, 10, $line1, 0, 1, 'C');
        //-->
        $pdf->SetFont('helvetica', 'I', 16);
        $pdf->SetTextColor(96, 96, 96);
        $pdf->SetXY(0 + $offset_adjustment, 88); // adjust Y accordingly
        $pdf->Cell(297, 10, 'This Certificate is presented to', 0, 1, 'C');
        //<--
        $pdf->SetFont('helvetica', 'B', 17);
        $pdf->SetTextColor(64, 64, 64);
        $pdf->SetXY(0 + $offset_adjustment, 97);
        $pdf->Cell(297, 10, $legal_entity_name, 0, 1, 'C');


        // Define max width for text
        $w = 200; // Width of the text block

        // Center the text horizontally
        $page_width = $pdf->GetPageWidth(); // Get the full page width
        $x_position = ($page_width - $w) / 2; // Calculate centered X position

        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->SetXY($x_position + $offset_adjustment, 110);
        $pdf->MultiCell($w, 10, $pickup_address, 0, 'C', false);
        //--->
        $pdf->SetFont('helvetica', 'I', 16);
        $pdf->SetTextColor(64, 64, 64);
        $text = "$waste_category received for Dismantling and recycling has\nbeen safely disposed off at our registered facility in an\nenvironment friendly manner.";
        $w = 180;
        $x = ($pdf->GetPageWidth() - $w) / 2;
        $pdf->SetXY($x + $offset_adjustment, 150); // Adjust Y based on your layout
        $pdf->MultiCell($w, 7, $text, 0, 'C', false);
        //<---
        //-->
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetTextColor(80, 0, 128); // Dark Purple
        $pdf->SetXY(195, 187); // adjust X and Y for bottom right corner
        $pdf->Cell(80, 10, $signatory_name, 0, 1, 'R');
        //<--
        // ---- Table: Asset List ----
        $pdf->SetFont('helvetica', '', 12);
        $max_per_page = 20; // Max 20 assets per page
        $total_assets = count($assets);
        $product_names = array_keys($assets);
        $product_quantities = array_values($assets);
        $page = 1;
        $start_index = 0;

        while ($start_index < $total_assets) {
            // Add new page (except for first one)
            if ($page >= 1) {
                $pdf->AddPage();
                $pdf->Image($other_pages_template_image, 0, 0, 297, 210, '', '', '', false, 300, '', false, false, 0, 'C');
            }

            // Table headers
            $pdf->SetFillColor(200, 200, 200); // Gray header
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetXY(50, 59);
            $pdf->Cell(100, 8, 'Assets', 1, 0, 'C', true);
            $pdf->Cell(80, 8, 'Picked Quantity', 1, 1, 'C', true);

            // Table rows (20 per page)
            $pdf->SetFont('helvetica', '', 11);
            $pdf->SetTextColor(0, 0, 0);
            
            for ($i = $start_index; $i < min($start_index + $max_per_page, $total_assets); $i++) {
                $product = $product_names[$i];
                $quantity = $product_quantities[$i];
                $pdf->SetFillColor(255, 255, 255); // white background
                $pdf->SetX(50);
                $pdf->Cell(100, 6, $product, 1, 0, 'L',true);
                $pdf->Cell(80, 6, $quantity, 1, 1, 'L',true);
            }
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->SetTextColor(80, 0, 128); // Dark Purple
            $pdf->SetXY(195, 187); // adjust X and Y for bottom right corner
            $pdf->Cell(80, 10, $signatory_name, 0, 1, 'R');
            // Update index for next page
            $start_index += $max_per_page;
            $page++;
        }
        
        // Output PDF
        // $pdf->Output('certificate.pdf', 'I');
        // exit;
        $pdfData = $pdf->Output('', 'S'); // 'S' returns PDF data as a string
        $base64Pdf = base64_encode($pdfData);

        // Save PDF to disk and database
        $new_attributes = ['green_certificate'=>$pdf_file_name];
        $result = EditModel::saveGeneratedFiles($base64Pdf, $pdf_file_name,$record_id,'pickup',$pickup_data, $new_attributes);

        if (!$result['success']) {
            return json_encode($result); // Return error response
        }
        // PDF successfully saved, now serve it to user
        $attachment_id = $result["fileName"];
        $sql = "update pickup set green_certificate = :green_certificate,modifiedtime = :modifiedtime,modifiedby = :modifiedby where pickup_id = :id";
        Yii::$app->db->createCommand($sql)
            ->bindValue(":green_certificate", $attachment_id)
            ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
            ->bindValue(":modifiedby", Yii::$app->user->id)
            ->bindValue(":id", $Record)
            ->execute();
        $pickup_modal = new Pickup();
        $pickup_modal->save_vp_certificate($Record);
        return $this->redirect(['download', 'fileid' => $attachment_id]);
    }

    public function actionGetproductinfo()
    {   
        $data = $_POST;
        $productid = Yii::$app->request->post('productid');
        $connection = Yii::$app->db;
        // echo "
        //                 SELECT product_category_value as category,sub_catagory_value as subcategory,uom_value FROM `products` 
        //                 join product_category on product_category.product_category_id = products.category
        //                 join prod_sub_catagory on prod_sub_catagory.sub_catagory_id = products.subcategory
        //                 join prod_uom on prod_uom.uom_id = products.uom
        //                   WHERE products_id = 36
        //             ";die;

        $command = $connection->createCommand("
                        SELECT prod_model_value as model,prod_make_value as make,hsn_code ,cost_price,prod_category_value as category,sub_catagory_value as subcategory,uom_value,products.make as make_name,products.model as model_name,products.subcategory as subcategory_name,products.category as category_name FROM `products` 
                        left join prod_category on prod_category.prod_category_id = products.category
                        left join prod_sub_catagory on prod_sub_catagory.sub_catagory_id = products.subcategory
                        left join prod_make on prod_make.prod_make_id = products.make
                        left join prod_model on prod_model.prod_model_id  = products.model
                        left join prod_uom on prod_uom.uom_id = products.uom
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
                'message' => 'No Product info found.',
                'data'=>''
            ]);
        }
    }

    public function get_pickup_done_id($connection,$pickup_done){
        if(empty($pickup_done)) return "";
        $query = $connection->createCommand("SELECT id FROM pickedup WHERE value = :value and is_active=1")->bindValues([":value"=> $pickup_done]);
        $data = $query->queryOne();
        if(empty($data)) return "";
        return $data["id"]??"";
    }
    public function get_valid_pickup_done_values($connection){
        $query = $connection->createCommand("SELECT value FROM pickedup WHERE is_active=1");
        $data = $query->queryAll();
        $values = array_column($data, 'value');
        return implode(', ', $values);
    }
    public function get_pickup_inspection_condition_id($connection,$condition){
        if(empty($condition)) return "";
        $query = $connection->createCommand("SELECT condition_id FROM pickup_ins_condition WHERE condition_value = :value and is_active=1")->bindValues([":value"=> $condition]);
        $data = $query->queryOne();
        if(empty($data)) return "";
        return $data["condition_id"]??"";
    }
    public function get_valid_conditions_values($connection){
        $query = $connection->createCommand("SELECT condition_value FROM pickup_ins_condition WHERE is_active=1");
        $data = $query->queryAll();
        $values = array_column($data, 'condition_value');
        return implode(', ', $values);
    }
    public function get_product_id($connection,$product_name){
        if(empty($product_name)) return "";
        $query = $connection->createCommand("SELECT products_id FROM products WHERE product_name = :value and deleted=0")->bindValues([":value"=> $product_name]);
        $data = $query->queryOne();
        if(empty($data)) return "";
        return $data["products_id"]??"";
    }
    public function actionImportdata()
    {
        try{
            $active_transaction = false;
            $Recordid = filter_var(Yii::$app->request->post('Recordid'), FILTER_SANITIZE_NUMBER_INT);
            $blockid = filter_var(Yii::$app->request->post('blockid'), FILTER_SANITIZE_NUMBER_INT);
            $Exceldata = Yii::$app->request->post('excel_data');
            if(empty($Recordid)){
                throw new Exception("Pickup information is required");
            }
            if(empty($blockid)){
                throw new Exception("Blockid is required");
            }
            if(empty($Exceldata)){
                throw new Exception("No data is found from the excel file");
            }
            if(!is_array($Exceldata)){
                throw new Exception("Data received from excel sheet is not valid");
            }
            $TabId = $this->TabId;
            $FieldId = $this->FieldId;
            $ModuleName = $this->ModuleName;
            $TableName = $this->TableName;
            $TabLabel = $this->TabLabel;

            $id = Yii::$app->user->id;
            $model = new AccessCheck();
            $tabs = $model->tabs($id, $ModuleName);
            $profile = $model->profile($id, $tabs, $ModuleName);
            $modelaccess = $model->moduleaccess($id, $profile, $tabs);
            $rolebasedrecord = $model->rolebasedrecord($id, $profile);
            $hasadminpower = $model->hasadminpower($profile);
            $modulepermission = $model->modulepermission($profile, $tabs);
            $editpermission = $model->checkpermission($id, $ModuleName, "edit");
            $exportpermission = $model->checkpermission($id, $ModuleName, 'export');
            $importpermission = $model->checkpermission($id, $ModuleName, 'import');


            if(empty($importpermission)){
                throw new Exception("You do not have import access for this module");
            }
            if(empty($editpermission)){
                throw new Exception("You do not have edit access for this module");
            }
            $actionid = "detail";
            $model = new EditModel($TableName, $FieldId, $ModuleName, $actionid);
            $model->_members[$FieldId] = $Recordid;
            list($Column, $Record) = $model->getFieldDetail($rolebasedrecord);
            
            $fields = [];
            $blocks_table = "";
            $blocks = $Column->blocks ?? []; // this comes from getFieldDetail()

            foreach ($blocks as $block) {
                if ($block->blockid == $blockid && isset($block->detailfields)) {
                    foreach ($block->detailfields as $field) {
                        $attributes = $field->getAttributes(); // or use $field->_attributes if private
                        $fields[] = [
                            'columnname' => $attributes['columnname'] ?? null,
                            'fieldlabel' => $attributes['fieldlabel'] ?? null,
                        ];
                        if(empty($blocks_table)) $blocks_table = $attributes['tablename'] ?? null;
                    }
                }
            }
            if(empty($blocks_table)){
                throw new Exception("Not able to figure out the module to update");
            }
            //after this need to find the label from the excel data
            $fieldLabels = [];
            foreach ($fields as $f) {
                $fieldLabels[$f['columnname']] = $f['fieldlabel'];
            }

            $updatableFields = ['pickup_done', 'condition', 'pickup_remarks'];
            // Determine identifier
            if ($blockid == 2739) {
                $identifierColumn = 'product_name';
            } else {
                $identifierColumn = 'serial_number';
            }

            $requiredColumns = array_merge($updatableFields, [$identifierColumn]);

            // Map: columnname => label
            $columnLabelMap = [];
            foreach ($requiredColumns as $col) {
                if (!isset($fieldLabels[$col])) {
                    throw new Exception("Missing label for column: $col");
                }
                $columnLabelMap[$col] = $fieldLabels[$col];
            }
            // Step 2: Validate header and map index positions
            $excelHeader = $Exceldata[0];
            $columnIndexes = [];
            foreach ($columnLabelMap as $col => $label) {
                $index = array_search($label, $excelHeader);
                if ($index === false) {
                    throw new Exception("Excel column '$label' not found.");
                }
                $columnIndexes[$col] = $index;
            }
            
            $updatedRows = 0;
            $errors = [];

            // Step 3: Process each row
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            $active_transaction = true;
            for ($i = 1; $i < count($Exceldata); $i++) {
                $row = $Exceldata[$i];
                // Get identifier value
                $identifierValue = trim($row[$columnIndexes[$identifierColumn]] ?? '');
                if ($identifierValue === '') {
                    $errors[] = "Row $i: Missing identifier ($identifierColumn)";
                    throw new Exception("Row $i: Missing identifier ({$columnLabelMap[$identifierColumn]}). Data can not be uploaded without a valid identifier");
                }

                // Prepare values to update
                $updateData = [];
                foreach ($updatableFields as $field) {
                    $lbl = $columnLabelMap[$field];
                    $value = trim($row[$columnIndexes[$field]] ?? '');
                    if ($value === '') {
                        $errors[] = "Row $i: '$field' cannot be empty.";
                        throw new Exception("Row $i: '$lbl' cannot be empty.");
                    }
                    if($field == "pickup_done"){
                        //fetch value from corresponding picklist table
                        $value_db = $this->get_pickup_done_id($connection,$value);
                        if(empty($value_db)){
                            $possible_values = $this->get_valid_pickup_done_values($connection);
                            throw new Exception("Row $i: '$lbl' value is not valid. Possible values are $possible_values and we have received $value");
                        }
                        $value = $value_db;
                    }else if($field == "condition"){
                        $value_db = $this->get_pickup_inspection_condition_id($connection,$value);
                        if(empty($value_db)){
                            $possible_values = $this->get_valid_conditions_values($connection);
                            throw new Exception("Row $i: '$lbl' value is not valid. Possible values are $possible_values and we have received $value");
                        }
                        $value = $value_db;
                    }else if($field == "product_name"){
                        $value_db = $this->get_product_id($connection,$value);
                        if(empty($value_db)){
                            throw new Exception("Row $i: '$lbl' value is not a valid Product Name. Please do not modify the product name in the exported excel");
                        }
                        $value = $value_db;
                        $identifierValue = $value;
                    }

                    $query = (new \yii\db\Query())
                    ->from($blocks_table)
                    ->where([$identifierColumn => $identifierValue, "pickup_id" => $Recordid]);

                    // Temporarily inspect the raw SQL being executed
                    //throw new Exception("Row $i: Query = " . $query->createCommand()->getRawSql());

                    // Actual check
                    $recordExists = $query->exists();
                    if (!$recordExists) {
                        throw new Exception("Row $i: No record found for $identifierColumn = '$identifierValue'. Please do not modify the serial number / product name in the exported excel");
                    }
                        $updateData[$field] = $value;
                }
                $connection->createCommand()
                    ->update($blocks_table, $updateData, [$identifierColumn => $identifierValue,"pickup_id" => $Recordid])
                    ->execute();
                $updatedRows++;
            }
            $transaction->commit();
            $active_transaction = false;
            return $this->asJson([
                'status' => 'success',
                'data' => []
            ]);
        }catch (Exception $e) {
            if($active_transaction) $transaction->rollBack();
            return $this->asJson([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => $e->getMessage(),
                'data'=>''
            ]);
        }catch (Error $e) {
            if($active_transaction) $transaction->rollBack();
            return $this->asJson([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => $e->getMessage(),
                'data'=>''
            ]);
        }
    }
}
