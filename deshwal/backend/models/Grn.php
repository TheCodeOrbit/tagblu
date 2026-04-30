<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "grn".
 *
 * @property int $grn_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string $product_no
 * @property string|null $product_name
 * @property string|null $product_description
 * @property string|null $hsn_code
 * @property string|null $unit_in_hand
 * @property string|null $account_name
 * @property int|null $tax_preference
 * @property int|null $active
 * @property int|null $product_nature
 * @property int|null $product_group
 * @property int|null $category
 * @property int|null $subcategory
 * @property int|null $oem
 * @property int|null $waste_catagory
 * @property int|null $uom
 * @property string|null $mop
 * @property string|null $cost_price
 * @property int|null $gst_percentage
 * @property int|null $mrp
 * @property int|null $minimum_margin_percentage
 * @property int $push_to_books
 * @property int $deleted
 */
class Grn extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'grn';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby','deleted'], 'integer'],
            [['createdtime', 'modifiedtime','cs_spoc'], 'safe'],
            [['pickup_id', 'lot_number','grn_no','account_name','fe_name','logistics_user','vehicle_image',
            'full_truck_image','loaded_vehicle_slip','empty_vehicle_slip','location', 'vehicle_seal_image', 
            'half_truck_image'], 'safe'],
            // added for handling blank values saving in by ptpatel on date 24-01-2026
            [['account_name'], 'trim'],
            [['account_name'], 'required', 'message' => 'Account Name cannot be blank.'],
            [['account_name'], 'integer', 'message' => 'Account Name must be a number.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pickup_id' => 'Pickup ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',

            'lot_number' => 'Lot Number',
            'grn_no' =>'GRN Number',
            'account_name' => 'Account Name',
            'fe_name' => "FE Name",//////
            'logistics_user' => "Logistics SPOC Name",////
            'location' => "Location", ////
            'cs_spoc' => "CS Spoc", ////
            
            'vehicle_image' => 'Vehicle Image',
            'vehicle_seal_image' => 'Seal Image',
            'full_truck_image' => 'Full Truck Image',
            'loaded_vehicle_slip' => 'Loaded Vehicle Slip',
            
            'empty_vehicle_slip' => 'Empty vehicle slip',
            'half_truck_image' => 'Half Truck Image',
            'deleted' => 'Deleted',
        ];
    }

    public function getLotNo(){
        $connection = Yii::$app->db;
        $qry = "SELECT * from modentity_num  where semodule ='grn_lot_no'";
        $command = $connection->createCommand($qry);
        $data = $command->queryOne();
        if(empty($data)) return "";
        $prefix		= $data['prefix'];
		$cur_id		= $data['cur_id'];
		$autoNo 	= sprintf("%04d", $cur_id);
		//current year
		$cyear = date('Y');
		$orderno	= $prefix.'-'.$cyear.'-'.$autoNo;
        
        Yii::$app->db->createCommand("UPDATE modentity_num SET cur_id = cur_id + 1 WHERE semodule = :semodule")
        ->bindValue(':semodule','grn_lot_no')->execute();
        return $orderno??"";
    }

    public function save_vp_grn($RecordId)
    {
        $connection = Yii::$app->db;
        // $sql = "SELECT pickup_no FROM pickup where pickup.pickup_id = :RecordId";
        // $result = Yii::$app->db->createCommand($sql)->bindValue(":RecordId",$RecordId)->queryOne();
        // $pickup_no= $result['pickup_no'];

        $sql_del = "Delete from `rep_vp_grn` where grn_id =:grn_id";
        $connection->createCommand($sql_del)->bindValue(":grn_id",$RecordId)->execute();

        $sql = "SELECT 
            sd1.sourcingdeal_no,
            sd1.sourcingdeal_id,
            sd1.vendor_account_name AS account_id,
            grn.grn_no,
            grn.pickup_id,
            pickup.pickup_no,
            grn.createdtime as date_material_received,
            grn.lot_number,
            po.purchase_order_id,
            po.purchase_order_no,
            pay.payment_no,
            pay.stage as payment_stage,
            pid.invoice_date,
            pid.invoice_number
        FROM grn
        LEFT JOIN pickup ON pickup.pickup_id = grn.pickup_id
        LEFT JOIN sourcingdeal sd1 ON sd1.sourcingdeal_id = pickup.opportuity_name
        LEFT JOIN purchase_order po ON po.opportunity_name = sd1.sourcingdeal_id and po.stage = 3
        LEFT JOIN payments pay ON pay.po = po.purchase_order_id
        LEFT JOIN payments_invoice_details pid ON pid.payments_id = pay.payments_id
        WHERE grn.grn_id = :RecordId";
        $result = $connection->createCommand($sql)->bindValue(":RecordId",$RecordId)->queryAll();
        foreach($result as $value)
        {
            $account_id = $value['account_id']?$value['account_id']:null;
            $sourcingdeal_no = $value['sourcingdeal_no']?$value['sourcingdeal_no']:null;
            $sourcingdeal_id = $value['sourcingdeal_id']?$value['sourcingdeal_id']:null;
            $grn_no = $value['grn_no'] ? $value['grn_no']:null;
            $pickup_no = $value['pickup_no'] ? $value['pickup_no']:null;
            $pickup_id = $value['pickup_id']?$value['pickup_id']:null;
            $lot_number = $value['lot_number'] ? $value['lot_number'] : null;
            $purchase_order_id = $value['purchase_order_id'] ? $value['purchase_order_id'] : null;
            $purchase_order_no = $value['purchase_order_no'] ? $value['purchase_order_no'] : null;
            $payment_no = $value['payment_no'] ? $value['payment_no'] : null;
            $invoice_date = $value['invoice_date'] ? $value['invoice_date'] : null;
            $invoice_number = $value['invoice_number'] ? $value['invoice_number'] : null;
            $date_material_received = $value['date_material_received'] ? $value['date_material_received'] : null;
            $payment_stage = $value['payment_stage'] ? $value['payment_stage'] : null;
            
            $total_po_qty = null;
            $assets_physical_qty = null;
            //get po items count
            if($purchase_order_id){
                $po_items_command = $connection->createCommand("SELECT sum(quantity) as total_quantity FROM purchase_order_itemsdetail WHERE purchase_order_id  = :record_id and deleted=0")->bindValues([":record_id"=> $purchase_order_id]);
                $po_items = $po_items_command->queryOne();
                $total_po_qty = $po_items["total_quantity"]??null;
            }
            //get pickupt items count
            if($pickup_id){
                
                $pickup_items_command = $connection->createCommand("SELECT sum(picked_qty) as total_quantity FROM pickup_asset_detail WHERE pickup_id   = :record_id  and deleted=0")->bindValues([":record_id"=> $pickup_id]);
                $pickup_items = $pickup_items_command->queryOne();
                $assets_physical_qty = $pickup_items["total_quantity"]??null;
            }
            
            $sql_ins = "INSERT INTO `rep_vp_grn` 
                (account_id, req_reference_no,grn_id, sourcingdeal_no, sourcingdeal_id, pickup_id, pickup_no, lot_number, purchase_order_id,
                purchase_order_no,payment_no,invoice_date,date_material_received,total_po_qty,assets_physical_qty,payment_stage,invoice_number, created_on) 
                VALUES 
                (:account_id, :req_reference_no,:grn_id, :sourcingdeal_no, :sourcingdeal_id, :pickup_id, :pickup_no, :lot_number, :purchase_order_id,
                :purchase_order_no,:payment_no,:invoice_date,:date_material_received,:total_po_qty,:assets_physical_qty,:payment_stage,:invoice_number, NOW())";

            $params = [
                ':account_id' => $account_id,
                ':req_reference_no' => $grn_no ? $grn_no :null,
                ':grn_id' => $RecordId,
                ':sourcingdeal_no' => $sourcingdeal_no ? $sourcingdeal_no :null,
                ':sourcingdeal_id' => $sourcingdeal_id ? $sourcingdeal_id :null,
                ':pickup_id' => $pickup_id ? $pickup_id :null,
                ':pickup_no' => $pickup_no ? $pickup_no :null,
                ':lot_number' => $lot_number ? $lot_number :null,
                ':purchase_order_id' => $purchase_order_id ? $purchase_order_id :null,
                ':purchase_order_no' => $purchase_order_no?$purchase_order_no:null,
                ':payment_no' =>$payment_no?$payment_no:null,
                ':invoice_date' =>$invoice_date?$invoice_date:null,
                ':date_material_received' =>$date_material_received?$date_material_received:null,
                ':total_po_qty' =>$total_po_qty?$total_po_qty:null,
                ':assets_physical_qty' =>$assets_physical_qty?$assets_physical_qty:null,
                ':payment_stage' => $payment_stage ? $payment_stage:null,
                ':invoice_number' => $invoice_number ? $invoice_number : null
            ];
            
            $res = $connection->createCommand($sql_ins)->bindValues($params)->execute();

            // try {
            //     $res = $connection->createCommand($sql_ins)
            //         ->bindValues($params)
            //         ->execute();
            // } catch (\yii\db\Exception $e) {
            //     echo "SQL Error: " . $e->getMessage();
            //     echo "<br>SQL: " . $sql_ins;
            //     echo "<br>Params: " . print_r($params, true);
            // }
        }
    }
}