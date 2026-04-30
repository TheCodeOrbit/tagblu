<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "inventory".
 *
 * @property int $inventory_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $pickup_id
 * @property string|null $grn_no
 * @property string|null $lot_no
 * @property string|null $account_name
 * @property string|null $location
 * @property string|null $product_name
 * @property int|null $category
 * @property int|null $subcategory
 * @property int|null $model
 * @property int|null $make
 * @property int|null $hsn_code
 * @property int|null $qty
 * @property int|null $uom
 * @property string|null $location_floor
 * @property string|null $location_code
 * @property string|null $serial_number
 * @property string|null $tag_number
 * @property string|null $bin_number
 * @property int|null $status
 */
class Inventory extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inventory';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pickup_id', 'grn_no', 'lot_no', 'account_name', 'location', 'product_name', 'category', 'subcategory', 'model', 'make', 'hsn_code', 'qty', 'uom', 'location_floor', 'location_code', 'serial_number', 'tag_number', 'bin_number', 'status','pickup_pk_id', 'grn_id'], 'default', 'value' => null],
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'category', 'subcategory', 'model', 'make', 'uom', 'status','pickup_pk_id', 'grn_id',], 'integer'],
            [['createdtime', 'modifiedtime'], 'safe'],
            [['pickup_id', 'location_code', 'bin_number', 'hsn_code'], 'string', 'max' => 100],
            [['grn_no', 'lot_no', 'inventory_no', 'account_name', 'location', 'product_name', 'location_floor', 'serial_number', 'tag_number'], 'string', 'max' => 200],
            [[ 'qty'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'inventory_id' => 'Inventory ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'pickup_id' => 'Pickup ID',
            'grn_no' => 'Grn No',
            'lot_no' => 'Lot No',
            'account_name' => 'Account Name',
            'location' => 'Location',
            'product_name' => 'Product Name',
            'category' => 'Category',
            'subcategory' => 'Subcategory',
            'model' => 'Model',
            'make' => 'Make',
            'hsn_code' => 'Hsn Code',
            'qty' => 'Qty',
            'uom' => 'Uom',
            'location_floor' => 'Location Floor',
            'location_code' => 'Location Code',
            'serial_number' => 'Serial Number',
            'tag_number' => 'Tag Number',
            'bin_number' => 'Bin Number',
            'status' => 'Status',
            'pickup_pk_id' => 'Pickup Pk ID',
            'grn_id' => 'Grn ID',
        ];
    }

    /* public function saveInventory($segregation, $segregation_details,$grn_id,$grn_asset_details_id)
    {
        // $items=$_POST['segregation_detail']??[];
        // echo "<pre>in inventory model ";print_r($grn_id);die;
        $i = 0;
        $pickupid = Grn::find('pickup_id')->where(['grn_id' => $grn_id])->one();
                    // echo "<pre>";print_r($pickupid->pickup_id);die;
        if (count($segregation_details) > 0) {
            $i = 1;
            foreach ($segregation_details as $rec) {
                for ($qc = 0; $qc < $rec['qty']; $qc++) {
                    // $nameLocation = $this->getlocationaccname($segregation['pickup_id'], $segregation['grn_no']);
                    $nameLocation = $this->getlocationaccname($segregation['pickup_id'],$grn_id);
                    $rec_obj = new Inventory();
                    $rec_obj->ownerid = $segregation['ownerid'];
                    $rec_obj->creatorid = $segregation['creatorid'];
                    $rec_obj->modifiedby = $segregation['modifiedby'];
                    $rec_obj->createdtime = $segregation['createdtime'];
                    $rec_obj->modifiedtime = $segregation['modifiedtime'];
                    $rec_obj->pickup_id = $segregation['pickup_id'];
                    $rec_obj->grn_no = $segregation['grn_no'];
                    $rec_obj->lot_no = $segregation['lot_no'];
                    $rec_obj->account_name = $nameLocation->account_name; //$_POST['segregation']['account_name'];
                    $rec_obj->location = $nameLocation->location; //$_POST['segregation']['location'];
                    $rec_obj->product_name = $rec['product_name'];
                    $rec_obj->category = $rec['category'];
                    $rec_obj->subcategory = $rec['sub_category'];
                    $rec_obj->model = $rec['model_no'];
                    $rec_obj->make = $rec['make'];
                    $rec_obj->hsn_code = $rec['hsn'];
                    $rec_obj->qty = 1;
                    $rec_obj->uom = $rec['uom'];
                    $rec_obj->location_floor = $rec['location_floor'];
                    $rec_obj->location_code = $rec['location_code'];
                    $rec_obj->status = $rec['status'];
                    $rec_obj->grn_id = $grn_id;
                    $rec_obj->pickup_pk_id = $pickupid->pickup_id;
                    if ($autoField = $this->checkAutoNo()) {
                        $rec_obj->{$autoField} = $this->getAutoNo(33);
                    }

                    if (!$rec_obj->validate()) {
                        echo "Validation failed!<br>" . $rec_obj->errors;
                    }
                    // print_r($rec_obj->attributes);
                    $rec_obj->save(0);
                    //update inventory log 
                    $inventory_log_details = new InventoryLogDetails();
                    $inventory_log_details->inventory_id = $rec_obj->inventory_id;
                    $inventory_log_details->inventory_updatedby =  Yii::$app->user->id;
                    $inventory_log_details->inventory_update_at = date('Y-m-d H:i:s');
                    $inventory_log_details->segregation_updatedby = Yii::$app->user->id;
                    $inventory_log_details->segregation_updated_at =date('Y-m-d H:i:s');
                    $inventory_log_details->save(0); 
                    //modtracker
                    $this->addmodetracker($rec_obj,$rec_obj->status,"segregation");
                    // for whatever status add data into clubbed inventory
                        //get grn details
                    $grn_asset_details = Yii::$app->db->createCommand("
                                SELECT grn_details.*, grn.account_name,grn.createdtime
                                FROM grn_asset_detail grn_details
                                LEFT JOIN grn ON grn.grn_id = grn_details.grn_id
                                WHERE grn_details.grn_asset_detail_id = :id
                            ")
                            ->bindValue(":id", $grn_asset_details_id)
                            ->queryOne();
                        //add stock category and sub category wise into clubbed inventory table
                        $entryExists = ClubbedInventory::find()
                            ->where([
                                'category' => $rec_obj->category,
                                'subcategory' => $rec_obj->subcategory, 
                            ])
                            ->one();

                        if ($entryExists) {
                            $entryExists->qty += $rec_obj->qty;
                            $entryExists->purchase_value += $grn_asset_details['quoted_price_gst_include'];
                            $entryExists->save();
                        }
                        else{
                            $clubbed_inventory = new ClubbedInventory();
                            $clubbed_inventory->category = $rec_obj->category;
                            $clubbed_inventory->subcategory = $rec_obj->subcategory;
                            $clubbed_inventory->qty = 1;
                            $clubbed_inventory->uom = $rec_obj->uom;
                            $clubbed_inventory->purchase_value = (float)$grn_asset_details['quoted_price_gst_include'];
                            $clubbed_inventory->location_floor = $rec_obj->location_floor;
                            $clubbed_inventory->location_code = $rec_obj->location_code;
                            if (!$clubbed_inventory->save()) { 
                                echo "<pre>";print_r($clubbed_inventory->getErrors());die;
                            }
                        }
                        // echo "<pre>";print_r($clubbed_inventory);die;
                        //clubbed inventory insert code end here
                        //inventory ageing code start from here
                        $inventoryageingExists = InventoryAgeing::find()
                            ->where([
                                'subcategory' => $rec_obj->subcategory, 
                                'grn_asset_detail_id' => $grn_asset_details_id,
                            ])
                            ->one();
                        if ($inventoryageingExists) {
                            $inventoryageingExists->qty += $rec_obj->qty;
                            $inventoryageingExists->amount += $grn_asset_details['quoted_price_gst_include'];
                            $inventoryageingExists->save();
                        }
                        else{
                            $inventory_ageing = new InventoryAgeing();
                            $inventory_ageing->grn_asset_detail_id = $grn_asset_details_id;
                            $inventory_ageing->grn_date = $grn_asset_details['createdtime'];
                            $inventory_ageing->lot_no = $rec_obj->lot_no;
                            $inventory_ageing->account_name = $grn_asset_details['account_name'] ?? null;
                            $inventory_ageing->product_name =$grn_asset_details['porduct_name'];
                            $inventory_ageing->subcategory = $rec_obj->subcategory;
                            $inventory_ageing->qty = 1;
                            $inventory_ageing->amount = $grn_asset_details['quoted_price_gst_include'];
                            $inventory_ageing->uom = $rec_obj->uom;
                            $inventory_ageing->save();
                        }
                        //end inventory ageing code here
                }
            }
        }
    }*/

    public function saveInventory($segregation, $segregation_details, $grn_id, $grn_asset_details_id)
    {
        $pickupid = Grn::find()
            ->select(['pickup_id'])
            ->where(['grn_id' => $grn_id])
            ->one();

        if (!$pickupid) {
            return;
        }

        $inventoryRows = [];
        $clubbedData = [];
        // --- Clubbed Inventory by Model No ---
        $modelClubbedData = [];
        $ageingData = [];
        $openingStockRows = [];
        $inventoryMap = [];

        if (!empty($segregation_details)) {
            $nameLocation = $this->getlocationaccname($segregation['pickup_id'], $grn_id);

            // --- Get current running number from modentity_num ---
            $modEntity = (new \yii\db\Query())
                ->from('modentity_num')
                ->where(['semodule' => 'inventory'])
                ->one();

            $curCounter = (int)$modEntity['cur_id'];
            $year = date('Y');

            foreach ($segregation_details as $rec) {
                $grn_asset_details = Yii::$app->db->createCommand("
                    SELECT gad.*, g.account_name, g.createdtime, g.pickup_id, gad.quoted_price_gst_include
                    FROM grn_asset_detail gad
                    LEFT JOIN grn g ON g.grn_id = gad.grn_id
                    WHERE gad.grn_asset_detail_id = :id
                ")
                ->bindValue(':id', $grn_asset_details_id)
                ->queryOne();

                for ($qc = 0; $qc < (int)$rec['qty']; $qc++) {

                    
                    $inventory_no = 'INV-' . $year . '-' . str_pad($curCounter, 15, '0', STR_PAD_LEFT);
                    $curCounter++; // increment for each item
                    
                    $rec_obj = new Inventory();
                    $rec_obj->ownerid = $segregation['ownerid'];
                    $rec_obj->creatorid = $segregation['creatorid'];
                    $rec_obj->modifiedby = $segregation['modifiedby'];
                    $rec_obj->createdtime = $segregation['createdtime'];
                    $rec_obj->modifiedtime = $segregation['modifiedtime'];
                    $rec_obj->pickup_id = $segregation['pickup_id'];
                    $rec_obj->grn_no = $segregation['grn_no'];
                    $rec_obj->lot_no = $segregation['lot_no'];
                    $rec_obj->account_name = $nameLocation->account_name ?? null;
                    $rec_obj->location = $nameLocation->location ?? null;
                    $rec_obj->product_name = $rec['product_name'];
                    $rec_obj->category = $rec['category'];
                    $rec_obj->subcategory = $rec['sub_category'];
                    $rec_obj->model = $rec['model_no'];
                    $rec_obj->make = $rec['make'];
                    $rec_obj->hsn_code = $rec['hsn'];
                    $rec_obj->qty = 1;
                    $rec_obj->uom = $rec['uom'];
                    $rec_obj->location_floor = $rec['location_floor'];
                    $rec_obj->location_code = $rec['location_code'];
                    $rec_obj->status = $rec['status'];
                    $rec_obj->grn_id = $grn_id;
                    $rec_obj->pickup_pk_id = $pickupid->pickup_id;
                    $rec_obj->inventory_no = $inventory_no;

                    if (!$rec_obj->validate()) {
                        echo "Validation failed for $inventory_no<br>";
                        print_r($rec_obj->errors);
                        continue;
                    }

                    $inventoryRows[] = $rec_obj->attributes;
                    //opening stock code start from here on date 24-12-2025
                    //when status is inventory then only add in stock
                    if($qc == 0 &&  ((int)$rec['status'] == 1)){ //because inventory entry make for qty 1 in table so it need to check once only
                            $openingStockRows[] = [
                                'productid'   => $rec_obj->product_name,
                                'location'  => $nameLocation->location
                            ];
                    }
                    //opening stock code end here on date 24-12-2025

                    // --- Clubbed Data accumulation ---
                    $key = $rec_obj->category . '-' . $rec_obj->subcategory;
                    if (!isset($clubbedData[$key])) {
                        $clubbedData[$key] = [
                            'category' => $rec_obj->category,
                            'subcategory' => $rec_obj->subcategory,
                            'qty' => 0,
                            'purchase_value' => 0,
                            'uom' => $rec_obj->uom,
                            'location_floor' => $rec_obj->location_floor,
                            'location_code' => $rec_obj->location_code
                        ];
                    }
                    $clubbedData[$key]['qty'] += 1;
                    $clubbedData[$key]['purchase_value'] += (float)$grn_asset_details['quoted_price_gst_include'];

                    // ---Modelwise Clubbed Data accumulation ---
                    $key = $rec_obj->model;
                    if (!isset($modelClubbedData[$key])) {
                        $modelClubbedData[$key] = [
                            'modelno' => $rec_obj->model,
                            'category' => $rec_obj->category,
                            'subcategory' => $rec_obj->subcategory,
                            'qty' => 0,
                            'purchase_value' => 0,
                            'uom' => $rec_obj->uom,
                            'location_floor' => $rec_obj->location_floor,
                            'location_code' => $rec_obj->location_code
                        ];
                    }
                    $modelClubbedData[$key]['qty'] += 1;
                    $modelClubbedData[$key]['purchase_value'] += (float)$grn_asset_details['quoted_price_gst_include'];


                    // --- Ageing Data accumulation ---
                    $akey = $rec_obj->subcategory . '-' . $grn_asset_details_id;
                    if (!isset($ageingData[$akey])) {
                        $ageingData[$akey] = [
                            'subcategory' => $rec_obj->subcategory,
                            'grn_asset_detail_id' => $grn_asset_details_id,
                            'grn_date' => $grn_asset_details['createdtime'],
                            'lot_no' => $rec_obj->lot_no,
                            'account_name' => $grn_asset_details['account_name'] ?? null,
                            'product_name' => $grn_asset_details['porduct_name'] ?? null,
                            'qty' => 0,
                            'amount' => 0,
                            'uom' => $rec_obj->uom,
                        ];
                    }
                    $ageingData[$akey]['qty'] += 1;
                    $ageingData[$akey]['amount'] += (float)$grn_asset_details['quoted_price_gst_include'];
                }
            }

            // --- Batch insert inventory records ---
            if (!empty($inventoryRows)) {
                Yii::$app->db->createCommand()
                    ->batchInsert(
                        Inventory::tableName(),
                        array_keys($inventoryRows[0]),
                        array_map('array_values', $inventoryRows)
                    )
                    ->execute();

                // --- Insert logs for all new inventories ---
                Yii::$app->db->createCommand("
                    INSERT INTO inventory_log_details (inventory_id, inventory_updatedby, inventory_update_at, segregation_updatedby, segregation_updated_at)
                    SELECT inventory_id, :userid, NOW(), :userid, NOW()
                    FROM inventory
                    WHERE grn_id = :grn_id
                ", [
                    ':userid' => Yii::$app->user->id,
                    ':grn_id' => $grn_id
                ])->execute();
            }
            // --- opening stock entry on date 24-12-2025---
                if (!empty($openingStockRows)) {
                    foreach ($openingStockRows as $rows){
                        $stockCalculation = new StockCalculation();
                        $stockCalculation->getTodayStockSingleProductdeshwal($rows['productid'], $rows['location']);
                    }
                }
             // --- opening stock entry on date 24-12-2025---

            // --- Update modentity_num with new counter ---
            Yii::$app->db->createCommand()
                ->update('modentity_num', ['cur_id' => $curCounter], ['semodule' => 'inventory'])
                ->execute();

            // --- Clubbed Inventory updates ---
            foreach ($clubbedData as $data) {
                $entryExists = ClubbedInventory::find()
                    ->where(['category' => $data['category'], 'subcategory' => $data['subcategory']])
                    ->one();

                if ($entryExists) {
                    $entryExists->qty += $data['qty'];
                    $entryExists->purchase_value += $data['purchase_value'];
                    $entryExists->save(false);
                } else {
                    $clubbed_inventory = new ClubbedInventory();
                    $clubbed_inventory->setAttributes($data);
                    $clubbed_inventory->save(false);
                }
            }

            // --- Model-wise Clubbed Inventory ---
                foreach ($modelClubbedData as $data) {
                    $modelentryExists = RepModelwiseClubbedInventory::find()
                        ->where(['modelno' => $data['modelno']])
                        ->one();

                    if ($modelentryExists) {
                        $modelentryExists->qty += $data['qty'];
                        $modelentryExists->purchase_value += $data['purchase_value'];
                        $modelentryExists->save(false);
                    } else {
                        $modelwise_clubbed_inventory = new RepModelwiseClubbedInventory();
                        $modelwise_clubbed_inventory->setAttributes($data);
                        $modelwise_clubbed_inventory->save(false);
                    }
                }

            // --- Inventory Ageing updates ---
            foreach ($ageingData as $data) {
                $inventoryageingExists = InventoryAgeing::find()
                    ->where([
                        'subcategory' => $data['subcategory'],
                        'grn_asset_detail_id' => $data['grn_asset_detail_id'],
                    ])
                    ->one();

                if ($inventoryageingExists) {
                    $inventoryageingExists->qty += $data['qty'];
                    $inventoryageingExists->amount += $data['amount'];
                    $inventoryageingExists->save(false);
                } else {
                    $inventory_ageing = new InventoryAgeing();
                    $inventory_ageing->setAttributes($data);
                    $inventory_ageing->save(false);
                }
            }
        }
    }



    public function getlocationaccname($pickup_id, $grn_id)
    {
        return Grn::find()
            ->where(['grn_id' => $grn_id])
            ->one();
    }


    public function checkAutoNo()
    {

        $table_name = $this->tableName();
        $autoField = Yii::$app->db->createCommand("SELECT columnname
            FROM field 
            WHERE tablename = :tablename AND uitype = :uitype")
            ->bindValue(':tablename', $table_name)
            ->bindValue(':uitype', 11)
            ->queryOne();
        if (empty($autoField))
            return false; // if does not exist;
        if (count($autoField) < 1)
            return false;
        else
            return $autoField['columnname'];
    }

    public function getAutoNo($tabs)
    {
        $table_name = Inventory::tableName();
        $orderno = $this->getautomoduleno($tabs, $table_name);
        return $orderno;
    }

    function getautomoduleno($tabs, $table_name)
    {
        if ($table_name == "vendor_account")
            $table_name = "vendoraccount";

        // Get the current number
        $autoNo = Yii::$app->db->createCommand("SELECT prefix, cur_id 
        FROM modentity_num 
        WHERE semodule = :semodule AND active = 1 FOR UPDATE")
            ->bindValue(':semodule', $table_name)
            ->queryOne(); // use queryOne instead of queryAll

        if (!$autoNo) {
            throw new \Exception("Auto number config not found for module: $table_name");
        }

        $prefix = $autoNo['prefix'];
        $cur_id = $autoNo['cur_id'];

        // Build the final order number
        $autoNoStr = sprintf("%04d", $cur_id);
        $cyear = date('Y');
        $orderno = $prefix . '-' . $cyear . '-' . $autoNoStr;

        // Now increment the current ID in DB immediately
        Yii::$app->db->createCommand("UPDATE modentity_num 
        SET cur_id = cur_id + 1 
        WHERE semodule = :semodule AND active = 1")
            ->bindValue(':semodule', $table_name)
            ->execute();

        return $orderno;
    }

    public function updateInventoryStatus($item,$from)
    {
        $inventroydetails = \app\models\Inventory::findOne($item['inventory_id']);
        $oldAttributes = $inventroydetails->getOldAttributes();
        if ($inventroydetails) {
            
            //this is common for tagging,cleaning,stickerremoval
            $inventroydetails->status = trim($item['status']);

            if($from == 'tagging')
            {
                $inventroydetails->serial_number = trim($item['serial_number']);
                $inventroydetails->tag_number = trim($item['tag_number']);
                if(isset($item['bin_number']))
                    $inventroydetails->bin_number = trim($item['bin_number']);
            }
            else if($from == 'stickerremoval' || $from == 'cleaning')
            {
                $inventroydetails->tag_number = trim($item['tag_number']);
                if(isset($item['bin_number']) && !empty($item['bin_number'])){
                    $inventroydetails->bin_number = trim($item['bin_number']);
                }
            }
            // echo "<pre>";print_r($inventroydetails);die;
            if (!$inventroydetails->validate()) {
                    echo "Validation failed!<br>" . $inventroydetails->errors;
            }
            else
            {
                $inventroydetails->save(0);
                //create log in inventory log details
                $inventory_log_details = new InventoryLogDetails();
                $inventory_log_details->inventory_id = $item['inventory_id'];
                $inventory_log_details->inventory_updatedby =  Yii::$app->user->id;
                $inventory_log_details->inventory_update_at = date('Y-m-d H:i:s');
                if($from == 'tagging')
                {
                    $inventory_log_details->tagging_updatedby = Yii::$app->user->id;
                    $inventory_log_details->tagging_updated_at =date('Y-m-d H:i:s');
                }
                else if($from == 'stickerremoval')
                {
                    $inventory_log_details->sticker_removal_updatedby = Yii::$app->user->id;
                    $inventory_log_details->sticker_removal_updated_at =date('Y-m-d H:i:s');
                }
                else if($from == 'cleaning')
                {
                    $inventory_log_details->cleaning_updatedby = Yii::$app->user->id;
                    $inventory_log_details->cleaning_updated_at =date('Y-m-d H:i:s');
                }
                else if($from == 'inventory'){
                    $inventory_log_details->inventorystatus_updatedby = Yii::$app->user->id;
                    $inventory_log_details->inventorystatus_updated_at =date('Y-m-d H:i:s');
                }

                $inventory_log_details->save(0); 
                // $this->addmodetracker($inventroydetails,$oldAttributes,$from);
            }
            // echo "in updateinventory<pre>";print_r($modlog->attributes);die;
        
            return true;
        }
        else
        {
            echo "inventory not found";
            return false;
        }
    }

    public function addmodetracker($inventroydetails,$oldAttributes,$from)
    {
        /*inventory_status
        0= created
        1=inventory
        2=tagging
        3=stickerremoval
        4=cleaning
        5=IQC require
        **/
        $modlog = new ModtrackerBasic();
        $auditstatus = 0;//$inventroydetails->status;
        // below condition added on date 09-12-2205 by ptpatel
        if($from == "inventory" || $from == "tagging"){ //it is from bulk update inventory or form search tagnumber and update status
            $auditstatus = 1; //bulk update
        }
        // $mode = $_POST["mode"];
        $module = $from;
        $customtablename = $module . "cf";
        $CS = array();
        if (isset($_POST[$customtablename]))
            $CS = $_POST[$customtablename];
        else
            $CS = '';
        $modlog->auditlog($oldAttributes, $inventroydetails->attributes, $module, $inventroydetails->inventory_id, $auditstatus, Yii::$app->user->id);
        //now save custom fields 
        if (!empty($CS)) {
            $CS = array_merge($CS, ["inventory_id" => $inventroydetails->inventory_id]);
            echo "CS=";
            //print_r($CS);echo "<br>";die;
            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
            $command->execute();
            $modlog->auditlog($oldAttributes = '', $CS, $module, $inventroydetails->inventory_id, $auditstatus, Yii::$app->user->id);
        }
    }
}
