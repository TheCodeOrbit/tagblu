<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "packing_list_dit".
 *
 * @property int $packinglist_id
 * @property int|null $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $packinglist_no
 * @property int|null $company_details
 * @property string|null $company_name
 * @property string|null $company_address
 * @property string|null $company_gstin
 * @property string|null $company_pan
 * @property int|null $contact_number
 * @property string|null $date
 * @property string|null $dc_number
 * @property int|null $mode_of_transport
 * @property string|null $transporter_name
 * @property string|null $vehicle_docket_number
 * @property int|null $no_of_boxes
 * @property float|null $shipment_weight
 * @property string|null $customer_name
 * @property string|null $customer_address
 * @property string|null $customer_gstin
 * @property string|null $customer_pan
 * @property string|null $material_receiver_name
 * @property string|null $material_receiver_contact_num
 * @property string|null $material_receiver_alternate_contact_num
 * @property string|null $material_receiver_email
 * @property string|null $declaration
 * @property int $deleted
 *
 * @property PackinglistditProductDetails[] $packinglistditProductDetails
 */
class PackingListDit extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'packing_list_dit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'packinglist_no', 'company_details', 'company_name', 'company_address', 'company_gstin', 'company_pan', 'contact_number', 'date', 'dc_number', 'mode_of_transport', 'transporter_name', 'vehicle_docket_number', 'no_of_boxes', 'shipment_weight', 'customer_name', 'customer_address', 'customer_gstin', 'customer_pan', 'material_receiver_name', 'material_receiver_contact_num', 'material_receiver_alternate_contact_num', 'material_receiver_email', 'declaration'], 'default', 'value' => null],
            [['deleted'], 'default', 'value' => 0],
            [['ownerid', 'creatorid', 'modifiedby', 'company_details', 'contact_number', 'mode_of_transport', 'no_of_boxes', 'deleted'], 'integer'],
            [['creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['createdtime', 'modifiedtime', 'date'], 'safe'],
            [['shipment_weight'], 'number'],
            [['packinglist_no', 'company_name', 'company_gstin', 'company_pan', 'dc_number'], 'string', 'max' => 200],
            [[ 'declaration'], 'string', 'max' => 1000],
            [['company_address', 'customer_address'], 'string', 'max' => 3000],
            [['transporter_name', 'vehicle_docket_number', 'customer_name', 'customer_gstin', 'material_receiver_name', 'material_receiver_email'], 'string', 'max' => 255],
            [['customer_pan', 'material_receiver_contact_num', 'material_receiver_alternate_contact_num'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'packinglist_id' => 'Packinglist ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'packinglist_no' => 'Packinglist No',
            'company_details' => 'Company Details',
            'company_name' => 'Company Name',
            'company_address' => 'Company Address',
            'company_gstin' => 'Company Gstin',
            'company_pan' => 'Company Pan',
            'contact_number' => 'Contact Number',
            'date' => 'Date',
            'dc_number' => 'Dc Number',
            'mode_of_transport' => 'Mode Of Transport',
            'transporter_name' => 'Transporter Name',
            'vehicle_docket_number' => 'Vehicle Docket Number',
            'no_of_boxes' => 'No Of Boxes',
            'shipment_weight' => 'Shipment Weight',
            'customer_name' => 'Customer Name',
            'customer_address' => 'Customer Address',
            'customer_gstin' => 'Customer Gstin',
            'customer_pan' => 'Customer Pan',
            'material_receiver_name' => 'Material Receiver Name',
            'material_receiver_contact_num' => 'Material Receiver Contact Num',
            'material_receiver_alternate_contact_num' => 'Material Receiver Alternate Contact Num',
            'material_receiver_email' => 'Material Receiver Email',
            'declaration' => 'Declaration',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * Gets query for [[PackinglistditProductDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPackinglistditProductDetails()
    {
        return $this->hasMany(PackinglistditProductDetails::class, ['packinglist_id' => 'packinglist_id']);
    }


    public function updateStatusOfDeliveychallan($dc_number)
    {
        
        $transaction = Yii::$app->db->beginTransaction();
        $modelleadetail = new DeliveryChallandit();
        $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `delivery_challandit` where deliverychallan_id=:deliverychallan_id")
                    ->bindValue(":deliverychallan_id", $dc_number)
                    ->queryOne();
                    // echo "2";
        $id = Yii::$app->user->id;
        $stageold = $modelleadetail->oldAttributes['status'];
        $creatorid =$modelleadetail->oldAttributes['creatorid'];
        $dctype = $modelleadetail->oldAttributes['delivery_challan_type'];
        $status = $data['status'] = 5; //packing list generated
        // $sql = "update delivery_challandit set invoice_created =:invoice_created,status = :status,modifiedtime = :modifiedtime where deliverychallan_id = :deliverychallan_id";
        // Yii::$app->db->createCommand($sql)
        //     ->bindValue(":status", $status)             
        //     ->bindValue(":invoice_created",3)
        //     ->bindValue(":modifiedtime", date('Y-m-d H:i:s'))
        //     ->bindValue(":deliverychallan_id", $dc_number)
        //     ->execute();
          if($dctype == 1){
            $sql = "update delivery_challandit set invoice_created =:invoice_created, status = :status,modifiedtime = :modifiedtime,modifiedby = :modifiedby,ownerid=:ownerid where deliverychallan_id = :deliverychallan_id";
            Yii::$app->db->createCommand($sql)
                ->bindValue(":status", $status)
                ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                ->bindValue(":modifiedby", $id)
                ->bindValue(":ownerid", $modelleadetail->oldAttributes['creatorid'])
                ->bindValue(":invoice_created",3)
                ->bindValue(":deliverychallan_id", $dc_number)
                ->execute();
            }
            else
            {
                $sql = "update delivery_challandit set status = :status,modifiedtime = :modifiedtime,modifiedby = :modifiedby,ownerid=:ownerid where deliverychallan_id = :deliverychallan_id";
                Yii::$app->db->createCommand($sql)
                ->bindValue(":status", $status)
                ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                ->bindValue(":modifiedby", $id)
                ->bindValue(":ownerid", $modelleadetail->oldAttributes['creatorid'])
                ->bindValue(":deliverychallan_id", $dc_number)
                ->execute();
            }
            $newattributes = array("status" => $status,"ownerid" => $modelleadetail->oldAttributes['ownerid']);
            $modlog = new ModtrackerBasic();
            $modlog->auditlog($modelleadetail->oldAttributes, $newattributes, 'deliverychallandit', $dc_number, 2, Yii::$app->user->id);
            $transaction->commit();
            // echo "update status od dc";die;
    //    $this->addmodetracker($modelleadetail,'deliverychallandit',$dc_number);
    }
    public function addmodetracker($modelleadetail,$from,$id)
    {
        $modlog = new ModtrackerBasic();
        $auditstatus = 2;//update
        // $mode = $_POST["mode"];
        $module = $from;
        $customtablename = $module . "cf";
        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $module, $id, $auditstatus, Yii::$app->user->id);
        //now save custom fields 
       
    }
}
