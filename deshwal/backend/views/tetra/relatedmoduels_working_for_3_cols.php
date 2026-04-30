<?php

use app\models\ListHire;
use app\models\PurchaseOrder;
use app\models\Quotes;
use app\models\Reference;
use app\models\SalesorderDit;
use app\models\Servicedetail;
use app\models\ServicedetailDetails;
use app\models\Sourcingdeal;
use app\models\VendorAccount;
use app\models\QuotesDit;
use app\models\Opportunity;
use app\models\User;
use backend\models\AccessCheck;

if (!empty($relatemodules)) {
    //print_r($relatemodules);die;

    $i = 1;
    $cnt = 0;
    foreach ($relatemodules as $key => $value) {
        $col1 = $col2 = $col3 = $collbl1 = $collbl2 = $collbl3 = '';
        $record_creatorid = $record_createdat = '';
        $head = array();
        $ColumnList = array();
        $mval = '';
        // echo "<pre>";print_r($relatemodules);die;
        //get count of records
        if ($value['related_recordfieldnme'] == 'related_to_id') {
            // echo $sql = "select count(".$value['related_tablekeyid'].") as cnt from `".$value['related_table']."` where related_to ='$TabId'  and related_to_id=$Recordid";
            // $sql = "select count(" . $value['related_tablekeyid'] . ") as cnt from `" . $value['related_table'] . "` where related_to =:module  and related_to_id=:record and ownerid=:ownerid";
            // deleted = 0 added by ptpatel on date 25-07-25 right side count show 
            $sql = "select count(" . $value['related_tablekeyid'] . ") as cnt from `" . $value['related_table'] . "` where related_to =:module  and related_to_id=:record  and deleted = 0";
            $v = Yii::$app->db->createCommand($sql)
                ->bindValue(":module", $TabId)
                ->bindValue(":record", $Recordid)
                // ->bindValue(":ownerid", Yii::$app->user->id)
                ->queryOne();
            $cnt = $v['cnt'];
            if ($cnt > 0) {
                //get header view columns

                $sql_h = "select fieldname as showfield,uitype from field where tabid = :related_module and headerview=1";
                $vhead = Yii::$app->db->createCommand($sql_h)
                    ->bindValue(":related_module", $value['related_module'])
                    ->queryOne();

                //get primary key
                // // Get the table schema
                $tableSchema = Yii::$app->db->schema->getTableSchema($value['related_table']);

                // Check if the table exists and has a primary key
                if ($tableSchema !== null && !empty($tableSchema->primaryKey)) {
                    $pkey = implode(', ', $tableSchema->primaryKey);
                    // echo "Primary key column(s) for table '$tableName': " . implode(', ', $tableSchema->primaryKey);
                    // exit;
                } else {
                    $pkey = '';
                }

                if (!empty($vhead)) {
                    $showfield = $vhead['showfield'];
                    //get show fields from table
                    if (!empty($showfield)) {
                        // print_r($showfield); 
                         //get extra column label 
                        if(!empty($value['related_columns'])){
                                $colsArray = explode(",", $value['related_columns']);
                                $in = "'" . implode("','", $colsArray) . "'";

                                $sql_col_label = "select * from field where tabid = :related_module AND `columnname` IN ($in)";
                                $col_label = Yii::$app->db->createCommand($sql_col_label)
                                        ->bindValue(":related_module", $value['related_module'])
                                        ->queryAll();

                                // RE-INDEX BY columnname (IMPORTANT FIX)
                                $col_label_map = [];
                                foreach ($col_label as $cl) {
                                    $col_label_map[$cl['columnname']] = $cl;
                                }

                                $collbl1 = (isset($colsArray[0]) && isset($col_label_map[$colsArray[0]])) ? $col_label_map[$colsArray[0]]['fieldlabel'] : '';
                                $collbl2 = (isset($colsArray[1]) && isset($col_label_map[$colsArray[1]])) ? $col_label_map[$colsArray[1]]['fieldlabel'] : '';
                                $collbl3 = (isset($colsArray[2]) && isset($col_label_map[$colsArray[2]])) ? $col_label_map[$colsArray[2]]['fieldlabel'] : '';
                            }

                            if(!empty($value['related_columns']))
                                $sql_h = "select creatorid,createdtime," . $showfield . ",$pkey ,". $value['related_columns'] ." 
                                        from `" . $value['related_table'] . "` 
                                        where related_to =:module and related_to_id=:record 
                                        order by $pkey desc limit 1 ";
                            else
                                $sql_h = "select creatorid,createdtime," . $showfield . ",$pkey  
                                        from `" . $value['related_table'] . "` 
                                        where related_to =:module and related_to_id=:record 
                                        order by $pkey desc limit 1 ";

                            $head = Yii::$app->db->createCommand($sql_h)
                                    ->bindValue(":module", $TabId)
                                    ->bindValue(":record", $Recordid)
                                    ->queryOne();

                            $pkey = $head[$pkey];

                            if (!empty($head)){
                                $showfieldval = $head[$showfield];  

                                if(!empty($value['related_columns'])){

                                    $col1 = (isset($colsArray[0]) && isset($head[$colsArray[0]]) && isset($col_label_map[$colsArray[0]])) 
                                                ? $this->context->getValues($col_label_map[$colsArray[0]], $head[$colsArray[0]]) 
                                                : '-';

                                    $col2 = (isset($colsArray[1]) && isset($head[$colsArray[1]]) && isset($col_label_map[$colsArray[1]])) 
                                                ? $this->context->getValues($col_label_map[$colsArray[1]], $head[$colsArray[1]]) 
                                                : '-';

                                    $col3 = (isset($colsArray[2]) && isset($head[$colsArray[2]]) && isset($col_label_map[$colsArray[2]])) 
                                                ? $this->context->getValues($col_label_map[$colsArray[2]], $head[$colsArray[2]]) 
                                                : '-';
                                }

                                $creatorname = new Listhire;
                                $record_creatorid = $creatorname->getuser($field["fieldid"], $head['creatorid']);
                                $record_createdat = date('d-m-Y h:i:s', strtotime($head['createdtime']));
                            }
                            else {
                                $showfieldval = $col1 = $col2 = $col3 = '';
                            }

                            //orignal code
                           /* $sql_h = "select " . $showfield . ",$pkey from `" . $value['related_table'] . "` where related_to =:module  and related_to_id=:record order by $pkey desc limit 1 ";
                            $head = Yii::$app->db->createCommand($sql_h)
                                ->bindValue(":module", $TabId)
                                ->bindValue(":record", $Recordid)
                                ->queryOne();
                            $pkey = $head[$pkey];
                            if (!empty($head))
                                $showfieldval = $head[$showfield];
                            else
                                $showfieldval = '';*/
                    }
                }
            }
        } else {
            // echo "in else";die;
            // $sql = "select count(" . $value['related_tablekeyid'] . ") as cnt from `" . $value['related_table'] . "` where " . $value['related_recordfieldnme'] . " =:record  and ownerid=:ownerid";
            // deleted = 0 added by ptpatel on date 25-07-2025 because count show wrong in right side section 
            $sql = "select count(" . $value['related_tablekeyid'] . ") as cnt from `" . $value['related_table'] . "` where " . $value['related_recordfieldnme'] . " =:record and deleted = 0";
            $v = Yii::$app->db->createCommand($sql)
                ->bindValue(":record", $Recordid)
                // ->bindValue(":ownerid", Yii::$app->user->id)
                ->queryOne();
            $cnt = $v['cnt'];
            // if($value['related_tablekeyid'] == 'payments_id')
                // echo "payments<pre>".print_r($value);
            if ($cnt > 0) {
                //get header view columns 
                $sql_h = "select fieldname as showfield,uitype from field where tabid = :related_module and headerview=1";
                $vhead = Yii::$app->db->createCommand($sql_h)
                    ->bindValue(":related_module", $value['related_module'])
                    ->queryOne();
                // if($value['related_module'] == 65) 
                // print_r($vhead);
                if (!empty($vhead)) {
                    $showfield = $vhead['showfield'];
                    //get show fields from table 
                    if (!empty($showfield)) {

                        //get primary key
                        // // Get the table schema
                        $tableSchema = Yii::$app->db->schema->getTableSchema($value['related_table']);

                        // Check if the table exists and has a primary key
                        if ($tableSchema !== null && !empty($tableSchema->primaryKey)) {
                            $pkey = implode(', ', $tableSchema->primaryKey);
                            // echo "Primary key column(s) for table '$tableName': " . implode(', ', $tableSchema->primaryKey);
                            // exit;
                        } else {
                            $pkey = '';
                        }

                        //code added by ptpatel for dispaly column in rightside module on date 29-11-2025
                        if(!empty($value['related_columns'])){
                            $colsArray = explode(",", $value['related_columns']);
                            $in = "'" . implode("','", $colsArray) . "'";

                            $sql_col_label = "select * from field where tabid = :related_module AND `columnname` IN ($in)";
                            $col_label = Yii::$app->db->createCommand($sql_col_label)
                                    ->bindValue(":related_module", $value['related_module'])
                                    ->queryAll();

                            // ==== IMPORTANT FIX: MATCH LABEL WITH CORRECT COLUMN ====
                            $col_label_map = [];
                            foreach ($col_label as $cl) {
                                $col_label_map[$cl['columnname']] = $cl;
                            }

                            // now reorder same like $colsArray
                            $col_label[0] = isset($colsArray[0], $col_label_map[$colsArray[0]]) ? $col_label_map[$colsArray[0]] : [];
                            $col_label[1] = isset($colsArray[1], $col_label_map[$colsArray[1]]) ? $col_label_map[$colsArray[1]] : [];
                            $col_label[2] = isset($colsArray[2], $col_label_map[$colsArray[2]]) ? $col_label_map[$colsArray[2]] : [];

                            $collbl1 = isset($col_label[0]['fieldlabel']) ? $col_label[0]['fieldlabel'] : '';
                            $collbl2 = isset($col_label[1]['fieldlabel']) ? $col_label[1]['fieldlabel'] : '';
                            $collbl3 = isset($col_label[2]['fieldlabel']) ? $col_label[2]['fieldlabel'] : '';
                        }
                        // end label part

                        if(!empty($value['related_columns']))
                            $sql_h = "select creatorid,createdtime," . $showfield . ",$pkey ,". $value['related_columns'] ." 
                                    from `" . $value['related_table'] . "` 
                                    where `" . $value['related_fieldname'] . "`=:record 
                                    order by $pkey desc limit 1";
                        else
                            $sql_h = "select creatorid,createdtime," . $showfield . ",$pkey 
                                    from `" . $value['related_table'] . "` 
                                    where `" . $value['related_fieldname'] . "`=:record 
                                    order by $pkey desc limit 1";
                        //end code added by ptpatel on date 29-11-2025 for right side module 
                        // $sql_h = "select " . $showfield . ",$pkey from `" . $value['related_table'] . "` where `" . $value['related_fieldname'] . "`=:record order by $pkey desc limit 1 ";
                        $head = Yii::$app->db->createCommand($sql_h)
                            // ->bindValue(":module", $TabId)
                            ->bindValue(":record", $Recordid)
                            ->queryOne();
                            // echo "<pre>";print_r($colsArray);print_r($head);die;
                        $pkey = $head[$pkey];

                        if ($showfield == 'contacts_id' && !empty($head)) {
                            //get contact name from  contatcs
                            $sql_h = "select concat(first_name,' ',if(last_name is null,'',last_name)) as fullname from contacts where contacts_id=:record limit 1";
                            $headval = Yii::$app->db->createCommand($sql_h)
                                // ->bindValue(":module", $TabId)
                                ->bindValue(":record", $head[$showfield])
                                ->queryOne();
                            $showfieldval = $headval['fullname'];
                            if(!empty($value['related_columns'])){                   
                                    $col1 = (isset($colsArray[0]) && isset($head[$colsArray[0]])) 
                                                ? $this->context->getValues($col_label[0], $head[$colsArray[0]]) 
                                                : '-';

                                    $col2 = (isset($colsArray[1]) && isset($head[$colsArray[1]])) 
                                                ? $this->context->getValues($col_label[1], $head[$colsArray[1]])  
                                                : '-';

                                    $col3 = (isset($colsArray[2]) && isset($head[$colsArray[2]])) 
                                                ? $this->context->getValues($col_label[2], $head[$colsArray[2]])  
                                                : '-';
                                }

                                $creatorname = new Listhire;
                                $record_creatorid = $creatorname->getuser($field["fieldid"], $head['creatorid']);
                                $record_createdat = date('d-m-Y h:i:s', strtotime($head['createdtime']));
                            
                        } else {
                            if(!empty($value['related_columns'])){                   
                                    $col1 = (isset($colsArray[0]) && isset($head[$colsArray[0]])) 
                                                ? $this->context->getValues($col_label[0], $head[$colsArray[0]]) 
                                                : '-';

                                    $col2 = (isset($colsArray[1]) && isset($head[$colsArray[1]])) 
                                                ? $this->context->getValues($col_label[1], $head[$colsArray[1]])  
                                                : '-';

                                    $col3 = (isset($colsArray[2]) && isset($head[$colsArray[2]])) 
                                                ? $this->context->getValues($col_label[2], $head[$colsArray[2]])  
                                                : '-';
                                }

                                $creatorname = new Listhire;
                                $record_creatorid = $creatorname->getuser($field["fieldid"], $head['creatorid']);
                                $record_createdat = date('d-m-Y h:i:s', strtotime($head['createdtime']));
                            $showfieldval = $head[$showfield];
                        }
                    }
                }
            }
        }
        if ($cnt > 0) {
            $id = Yii::$app->user->id;
            $model = new AccessCheck();
            $tabs = $model->tabs($id, $value['related_module']);
            $profile = $model->profile($id, $tabs, $value['related_module']);
            $modelaccess = $model->moduleaccess($id, $profile, $tabs);
            $rolebasedrecord = $model->rolebasedrecord($id, $profile);
            $hasadminpower = $model->hasadminpower($profile);
            $modulepermission = $model->modulepermission($profile, $tabs);

            $model1 = new Reference($value['related_table'], $value['related_tablekeyid']);
            list($ColumnList, $RecordList, $totalitemcount) = $model1->getListRecord_relatedsidemenu('', '', $rolebasedrecord, $modulepermission, $value['related_module']);
            // print_r($ColumnList);
        }

        //get module name from modulename
        $sql = "select tablabel,tabid as relateid from tab where name=:module";
        $cmd = Yii::$app->db->createCommand($sql)->bindValue(":module", $value['modulename'])->queryOne();
        if ($cmd) {
            $modulelabel = $cmd['tablabel'];
            $relateid = $cmd['relateid'];
        } else {
            $modulelabel = '';
            $relateid = '';

        }


        ?>
        <div class="collapse-container">
            <!-- related Section -->
            <label class="collapse-header" for="toggle-<?= ucfirst($value['modulename']); ?>">
                <img src="<?= $baseUrl ?>thememain/img/module-icon/<?= $value['modulename']; ?>.png"
                    class="<?= ucfirst($value['modulename']); ?>" alt="<?= ucfirst($value['modulename']); ?> Icon">
                <?= $modulelabel ?> (<?= $cnt; ?>)
                <?php
                if ($relateid != 53 && $relateid != 58) {
                    //code added by ptpatel on date 05-04-2025
                    // if sourceing deal module stage is "Pricing Done" then only quote can be added otherwise hide add button for quote module in right sidebar 
                    if ($relateid == 42) { //42 quote in right sidebar
                        $stage_value = Sourcingdeal::find()->select("stage")->where(['sourcingdeal_id' => $Recordid])->scalar();
                        if ($stage_value == 10) { //10 for priceing done
                            ?>
                            <a
                                href="<?= $baseUrl; ?><?= $value['modulename']; ?>/create?sourcemodule=<?= $TabId; ?>&sourceid=<?= $Recordid; ?>">
                                <i class="fa-solid fa-plus" title="add new"></i>
                            </a>
                            <?php
                        }
                    } else if ($relateid == 72) //DEvit Quotes in right sidebar
                    {
                        // if in Quote devit Module stage is "Approved"
                        $quote_value = QuotesDit::find()->select("quote_stage")->where(['opportunity_name' => $Recordid])->one();
                        $opportunity = Opportunity::find()->select("opportunity_stage")->where(['opportunity_id' => $Recordid])->one();
                        // echo $relateid;
                        // print_r($quote_value);die;
                        // echo $opportunity->opportunity_stage;die;
                        if (((!empty($quote_value) && $quote_value->quote_stage != 4) || (empty($quote_value))) && $opportunity->opportunity_stage == 5) { //1 if for quote stage nt approved 2 is for opportunity status active
                            ?>
                                <a
                                    href="<?= $baseUrl; ?><?= $value['modulename']; ?>/create?sourcemodule=<?= $TabId; ?>&sourceid=<?= $Recordid; ?>">
                                    <i class="fa-solid fa-plus" title="add new"></i>
                                </a>
                            <?php
                        }
                    } else if ($relateid == 74) //DEvit Sales order in right sidebar
                    {
                        // if in Quote devit Module stage is "Approved"
                        $so_value = SalesorderDit::find()->select("so_stage")->where(['deal_name' => $Recordid])->one();
                        $opportunity = Opportunity::find()->select("opportunity_stage")->where(['opportunity_id' => $Recordid])->one();
                        // echo $relateid;
                        // print_r($so_value);die;
                        // echo $so_value->so_stage;die;
                        if ((!empty($so_value) && $so_value->so_stage != '4') || (empty($so_value) && $opportunity->opportunity_stage == 8)) { //1. if for sales order stage not approved 2. is for opportunity status won
                            ?>
                                    <a
                                        href="<?= $baseUrl; ?><?= $value['modulename']; ?>/create?sourcemodule=<?= $TabId; ?>&sourceid=<?= $Recordid; ?>">
                                        <i class="fa-solid fa-plus" title="add new"></i>
                                    </a>
                            <?php
                        }
                    } else if ($relateid == 13) //purchase oreder in right sidebar
                    {
                        // if in Quote Module stage is "Approved" and account name is active then only purchase order can be added  otherwise hide add button for purchase order in right sidebar
                        $quote_value = Quotes::find()->select("quote_stage,account_name")->where(['quotes_id' => $Recordid])->one();
                        $active_account = VendorAccount::find()->where(["vendoraccid" => $quote_value->account_name])->one();
                        if ($quote_value->quote_stage == 1 && $active_account->acc_status == 2) { //1 if for quote approved 2 is for account status active
                            ?>
                                        <a
                                            href="<?= $baseUrl; ?><?= $value['modulename']; ?>/create?sourcemodule=<?= $TabId; ?>&sourceid=<?= $Recordid; ?>">
                                            <i class="fa-solid fa-plus" title="add new"></i>
                                        </a>
                            <?php
                        }
                    }
                    //code added by ptpatel 
                    // on date 26-06-25
                    else if ($relateid == 12 ) { //side bar contracts in account
                         //added condition for contracts, add only if it is account billable type = 1(RC) by on 26 june 2025
                         $active_account = VendorAccount::find()->where(["vendoraccid" => $Recordid])->one();
                        
                        $sdstge = $active_account['billing_type'];
                        if ($sdstge == 1)//won
                        {
                           
                        ?>
                                                                <a
                                                                    href="<?= $baseUrl; ?><?= $value['modulename']; ?>/create?sourcemodule=<?= $TabId; ?>&sourceid=<?= $Recordid; ?>"><i
                                                                        class="fa-solid fa-plus" title="add new"></i></a>
                        <?php
                        }
                    }
                    // on date 06-05-25
                    else if ($relateid == 55) //sourcing deal
                    {
                        $sdstge = $Record['stage'];
                        $inspection = $drilling = $degaussing = $shredding = $datawiping = [];
                       //added by deepika on 18 june 2025
                        $pickup = '';
                        //    if servicedetails are added in sourcing deal then only show + button
                        $services = Servicedetail::find()->select("*")->where(['related_to_id' => $Recordid])->all();
                        // echo "<pre>";print_r($services);
                        foreach ($services as $service) {
                            $service_details = ServicedetailDetails::find()->select("*")->where(['servicedetail_id' => $service->servicedetail_id])->all();
                            // echo "<pre> service detail";print_r($service_details);
        
                            foreach ($service_details as $service_details) {
                                if ($service_details->service_type == 1 && $sdstge == 'WON')//degaussing
                                {
                                    $degaussing = $service_details->servicedetail_detail_id;
                                } else if ($service_details->service_type == 2 && $sdstge == 'WON')//drilling
                                {
                                    $drilling = $service_details->servicedetail_detail_id;
                                } else if ($service_details->service_type == 3)//inspection
                                {
                                    $inspection = $service_details->servicedetail_detail_id;
                                } else if ($service_details->service_type == 4 && $sdstge == 'WON')//Shredding  
                                {
                                    $shredding = $service_details->servicedetail_detail_id;
                                } else if ($service_details->service_type == 5 && $sdstge == 'WON')//datawiping
                                {
                                    $datawiping = $service_details->servicedetail_detail_id;
                                }
                            }



                        }
                      

                        // echo "<pre>";print_r($drilling);die;
        
                        //this will show + button infront of service detail
                        ?>
                                        <a
                                            href="<?= $baseUrl; ?><?= $value['modulename']; ?>/create?sourcemodule=<?= $TabId; ?>&sourceid=<?= $Recordid; ?>"><i
                                                class="fa-solid fa-plus" title="add new"></i></a>
                        <?php
                    } else if ($relateid == 2 && !(empty($inspection))) {
                        ?>
                                            <a
                                                href="<?= $baseUrl; ?><?= $value['modulename']; ?>/create?sourcemodule=<?= $TabId; ?>&sourceid=<?= $Recordid; ?>"><i
                                                    class="fa-solid fa-plus" title="add new"></i></a>
                        <?php
                    } else if ($relateid == 3 && !(empty($drilling))) {
                        ?>
                                                <a
                                                    href="<?= $baseUrl; ?><?= $value['modulename']; ?>/create?sourcemodule=<?= $TabId; ?>&sourceid=<?= $Recordid; ?>"><i
                                                        class="fa-solid fa-plus" title="add new"></i></a>
                        <?php
                    } else if ($relateid == 4 && !(empty($degaussing))) {
                        ?>
                                                    <a
                                                        href="<?= $baseUrl; ?><?= $value['modulename']; ?>/create?sourcemodule=<?= $TabId; ?>&sourceid=<?= $Recordid; ?>"><i
                                                            class="fa-solid fa-plus" title="add new"></i></a>
                        <?php
                    } else if ($relateid == 5 && !(empty($shredding))) {
                        ?>
                                                        <a
                                                            href="<?= $baseUrl; ?><?= $value['modulename']; ?>/create?sourcemodule=<?= $TabId; ?>&sourceid=<?= $Recordid; ?>"><i
                                                                class="fa-solid fa-plus" title="add new"></i></a>
                        <?php
                    } else if ($relateid == 6 && !(empty($datawiping))) {
                        ?>
                                                            <a
                                                                href="<?= $baseUrl; ?><?= $value['modulename']; ?>/create?sourcemodule=<?= $TabId; ?>&sourceid=<?= $Recordid; ?>"><i
                                                                    class="fa-solid fa-plus" title="add new"></i></a>
                        <?php
                    } else if (!in_array($relateid, [2, 3, 4, 5, 6, 24])) {
                        
                        // else{
                        //end code added by ptpatel on te 06-05-25
                        //end code added by ptpatel on date 05-04-2025
                        ?>
                                                                <a
                                                                    href="<?= $baseUrl; ?><?= $value['modulename']; ?>/create?sourcemodule=<?= $TabId; ?>&sourceid=<?= $Recordid; ?>"><i
                                                                        class="fa-solid fa-plus" title="add new"></i></a>
                        <?php
                    }//added by ptpatel on date 05-04-2025
                     //added by deepika on 18 june 2025
                    else if (in_array($relateid, [24]) ) {
                         //added condition for pickup, add only if it is won by deepika on 18 june 2025
                        $sdstge = $Record['stage'];
                        if ($sdstge == "WON")//won
                        {
                           
                        ?>
                                                                <a
                                                                    href="<?= $baseUrl; ?><?= $value['modulename']; ?>/create?sourcemodule=<?= $TabId; ?>&sourceid=<?= $Recordid; ?>"><i
                                                                        class="fa-solid fa-plus" title="add new"></i></a>
                        <?php
                        }
                    }
                }  else {
                    
                    ?>
                        <a
                            href="<?= $baseUrl; ?><?= $value['modulename']; ?>/list?sourcemodule=<?= $TabId; ?>&sourceid=<?= $Recordid; ?>"><i
                                class="fa-solid fa-plus" title="add new"></i></a>
                    <?php
                } ?>

                <i class="fa-solid fa-angle-down icon-right"></i>
            </label>
            <input type="checkbox" id="toggle-<?= ucfirst($value['modulename']); ?>" onchange="toggleCollapseIcon(this)">
            <div class="collapse-content">
                <!-- <div class="collapse-content relatedmodulesidebar-card"> -->
                <?php
                if (!empty($head)) {
                    //show detail ?>
                    <!-- <p class="rel-rec"><a
                            href="<?= $baseUrl; ?><?= $value['modulename'] ?>/detail?Record=<?= $pkey; ?>&sourcemodule=<?= $TabId; ?>&sourceid=<?= $Recordid; ?>"><?= $showfieldval; ?></a>
                    </p> -->
                    
                        <?php include ('relatedmoduledetailcard.php'); ?>
                    <?php
                } ?>
                <?php
                if (!empty($RecordList)) {
                    // echo "<pre>";print_r($RecordList);
                    foreach ($RecordList as $key => $mval) {
                        if ($key != "RecordId") {
                            if (isset($ColumnList[$key])) { ?>
                                <!-- <p><?php //echo $ColumnList[$key] ?>: <?php //echo $mval ?></p> --><!--this line commented by ptpatel on date 29-11-2025 when card and column added dynamically for related module -->
                            <?php }
                        }
                    }
                }
                ?>
                <?php if ($cnt > 0) { ?>
                    <div class="list-content">
                        <a
                            href="<?= $baseUrl; ?><?= $value['modulename']; ?>/list?sourcemodule=<?= $TabId; ?>&sourceid=<?= $Recordid; ?>">View
                            All</a>
                    </div>

                <?php } ?>

            </div>
        </div>
        <?php
        $i++;
    }
} ?>
<!-- <div class="collapse-container">
    -- Document Section -->
<!-- <label class="collapse-header" for="toggle-documents">
        <img src="<?= $baseUrl ?>thememain/img/detail/file_120.png" class="document" alt="Document Icon">
        Document
        (0)
        <i class="fa-solid fa-angle-down icon-right"></i>
    </label>
    <input type="checkbox" id="toggle-documents" onchange="toggleCollapseIcon(this)">
    <div class="collapse-content">
        <div class="upload-section">
            <button class="upload-btn">Upload Files</button>
            <p>Or drop files</p>
        </div>
    </div>
</div> -->
<script>
    function toggleCollapseIcon(checkbox) {
        const labelIcon = checkbox.parentNode.querySelector('.icon-right');
        if (checkbox.checked) {
            labelIcon.classList.remove('fa-angle-down');
            labelIcon.classList.add('fa-angle-up');
        } else {
            labelIcon.classList.remove('fa-angle-up');
            labelIcon.classList.add('fa-angle-down');
        }
    }
</script>
<script>
    // Show popup
    function showPopup(popupId) {
        document.getElementById(popupId).style.display = 'flex';
    }

    // Hide popup
    function hidePopup(event) {
        if (event.target.classList.contains('popup-overlay') || event.target.classList.contains('close-btn')) {
            event.target.closest('.popup-overlay').style.display = 'none';
        }
    }
</script>
<script>
    document.querySelectorAll('.accordion-header').forEach(header => {
        header.addEventListener('click', () => {
            const content = header.nextElementSibling;

            header.classList.toggle('active');
            if (content.style.display === 'block') {
                content.style.display = 'none';
            } else {
                content.style.display = 'block';
            }
        });
    });
</script>


<script>
    document.querySelectorAll('.dropdown-btn').forEach(button => {
        button.addEventListener('click', () => {
            const dropdown = button.closest('.dropdown');
            dropdown.classList.toggle('show');
        });
    });

    document.addEventListener('click', (event) => {
        if (!event.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown').forEach(dropdown => dropdown.classList.remove('show'));
        }
    });
</script>


<script>
    document.querySelectorAll('.dropdown-btn').forEach(button => {
        button.addEventListener('click', () => {
            const dropdown = button.closest('.dropdown');
            dropdown.classList.toggle('show');
        });
    });

    document.addEventListener('click', (event) => {
        if (!event.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown').forEach(dropdown => dropdown.classList.remove('show'));
        }
    });
</script>