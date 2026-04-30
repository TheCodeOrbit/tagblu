<?php
use app\models\ListHire;
use app\models\Reference;
$baseUrl = Yii::$app->HomeUrl;
?>
<div id="summary" class="tab-content-detail-view active">
    <div class="accordion">
        <?php
        $i=1;
        $isedit = false;
        if($i == 1 && isset($Record->leadstatus)){
            $leadStatus = $Record->leadstatus;
            $i++;
        }
        $leadStatusArray = [4,5,6,9];
        // echo $id;die;
        $id = Yii::$app->user->id;
        if(($hasadminpower == 1 || $Record->ownerid == $id)) { 
           $isedit = true;
        }
         $ColumnList = $arrRender['ColumnList'];
        // echo "<pre>";print_r($ColumnList);die;
        foreach ($ColumnList->blocks as $BlockKey => $Block) {

            // if (!empty($Block->detailfields) && $Block->blocktype != "Multiple") {
            // added on 14 jan 2025 to hide block with display_status == 0
            if (
                !empty($Block->detailfields) && $Block->blocktype != "Multiple" &&
                $Block->display_status == '1' || ($TabId == 18 && $Block->blocklabel == 'OEM MANAGER DETAILS'
                )
            ) {

                ?>
                <details class="c-faqs__item">
                    <summary class="c-faqs__item-question">
                        <?= $Block->blocklabel ?> <i class="fa-solid fa-angle-down"></i>
                    </summary>

                     <!-- onlyid part added by ptptael 18-03-25 -->
                     <div class="details-container" id="details-container">

                        <?php
                        
                        foreach ($Block->detailfields as $field) {                       
                        ?>
                            <?php
                            if ($field["uitype"] == 12 || $field["uitype"] == 27 || $field["uitype"] == 28) {
                                $ref_hid_value = isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '';
                                $model1 = new Reference($TableName, $FieldId);
                                $relatedmodulename = $model1->getRelatedNoduleName($field["fieldid"]);
                                $getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldName($field["fieldid"]);
                                if (isset($Record->{$field["columnname"]}) && $Record->{$field["columnname"]} != '')
                                $Record->{$field["columnname"]} = "<a href='".$baseUrl.$relatedmodulename."/detail?Record=".$ref_hid_value."'>".$model1->getRefEntityValue($field["fieldid"], $ref_hid_value)."</a>";
                                else
                                    $Record->{$field["columnname"]} = '';
                            } 
                            else if ($field["uitype"] == 17) {

                                if (isset($Record->{$field["columnname"]}) && $Record->{$field["columnname"]} != "0000-00-00") {
                                   
                                    $date = $Record->{$field["columnname"]}; // original date in Y-m-d format
                
                                    // Convert to a timestamp
                                    $timestamp = strtotime($date);

                                    // Format the timestamp to d-m-Y
                                    $Record->{$field["columnname"]} = date('d-m-Y', $timestamp);
                                } else
                                    $Record->{$field["columnname"]} = '-';
                            } 
                            else if ($field["uitype"] == 13) {

                                if (isset($Record->{$field["columnname"]}) && $Record->{$field["columnname"]} != "0000-00-00 00:00:00") {
                                   
                                    $date = $Record->{$field["columnname"]}; // original date in Y-m-d format
                
                                    // Convert to a timestamp
                                    $timestamp = strtotime($date);

                                    // Format the timestamp to d-m-Y
                                    $Record->{$field["columnname"]} = date('d-m-Y H:i:s', $timestamp);
                                } else
                                    $Record->{$field["columnname"]} = '-';
                            } 
                            
                            else if ($field["uitype"] == 8 || $field["uitype"] == 10) {
                                $modellist = new Listhire;
                                if (isset($Record->{$field["columnname"]}))
                                    $Record->{$field["columnname"]} = $modellist->getPickListDetailvalue($field["fieldid"], $Record->{$field["columnname"]});
                                else
                                    $Record->{$field["columnname"]};
                            } else if ($field["uitype"] == 6) { //checkbox
                                $modellist = new Listhire;
                                if (isset($Record->{$field["columnname"]})) {
                                    if ($Record->{$field["columnname"]} == 1)
                                        $Record->{$field["columnname"]} = "Yes";
                                    else if ($Record->{$field["columnname"]} == 0)
                                        $Record->{$field["columnname"]} = "No";
                                } else
                                    $Record->{$field["columnname"]};
                            } else if ($field["uitype"] == 22 || $field["uitype"] == 9) { //comma separated value
                                $modellist = new Listhire;
                                if (isset($Record->{$field["columnname"]}))
                                    $Record->{$field["columnname"]} = $modellist->getPickListDetailMultiple($field["fieldid"], $Record->{$field["columnname"]});
                                else
                                    $Record->{$field["columnname"]};
                            } else if ($field["uitype"] == 53) {
                                $modellist = new Listhire;
                                if (isset($Record->{$field["columnname"]}))
                                    $Record->{$field["columnname"]} = $modellist->getuser($field["fieldid"], $Record->{$field["columnname"]});
                                else
                                    $Record->{$field["columnname"]};
                                // } else if ($field["uitype"] == 5 && $TabLabel == 'Documents') {
                            } else if ($field["uitype"] == 5) {

                                if ($Record->{$field["columnname"]}) {
                                    if ($field["columnname"] == 'profilepic' && !empty($Record->{$field["columnname"]})) {
                                        $Record->{$field["columnname"]} = "<br><img src='" . $baseUrl . $Record->{$field["columnname"]} . "' height ='150' width = '150'/>";
                                    } else {
                                        $records = \app\models\Attachments::find()
                                            ->where(['attachmentsid' => $Record->{$field["columnname"]}])
                                            ->one();
                                        //print_r($records);die;
                                        $Record->{$field["columnname"]} = "<br>" . $records->name . " <a href='" . $baseUrl . $ModuleName . "/download?fileid=" . $Record->{$field["columnname"]} . "'><i class='fa fa-download' aria-hidden='true' title='download'></i></a>";
                                    }
                                }
                            }
                            if ($field["columnname"] == "firstname") {
                                if (isset($Record->{$field["columnname"]})) {
                                    // echo "deepika ".$Record['salutation'];die;   
                                    if (!empty($Record['salutation'])) {
                                        $modellist = new Listhire;

                                        $salutation = $modellist->getSalutation($Record['salutation']);

                                        $Record->{$field["columnname"]} = $salutation . " " . $Record->{$field["columnname"]};
                                    }
                                }
                            }
                            if ($field['columnname'] != "salutation") {
                                //$Record->{$field["columnname"]} ='';
                
                                //check for related modules
                                if ($field['columnname'] == 'related_to') {
                                    //get modulename
                                    $module = \app\models\Tab::find()
                                        ->where(['tabid' => $Record->{$field["columnname"]}])
                                        ->one();
                                    $Record->{$field["columnname"]} = ucfirst($module->name);
                                }
                                if ($field['columnname'] == 'related_to_id') {
                                    $related = \app\models\Tab::find()
                                        ->where(['name' => strtolower($Record['related_to'])])
                                        ->one();
                                    $relatedtab = $related['tabid'];
                                    //get modulename
                                    $module = \app\models\Field::find()
                                        ->where(['tabid' => $relatedtab])
                                        ->andWhere(['headerview' => 1])
                                        ->one();
                                    //print_r($module);die;
                                    $tablename = $module['tablename'];
                                    //$columnname = $module['tablename'];
                
                                    // $relatedmodulename = $model1->getRelatedNoduleName($field["fieldid"]);
                                    // $getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldName($field["fieldid"]);
                                    $model1 = new Reference($TableName, $FieldId);
                                    if (isset($Record->{$field["columnname"]}) && $Record->{$field["columnname"]} != '')
                                        $Record->{$field["columnname"]} = $model1->getMultiRefEntityValue($field["fieldid"], $Record->{$field["columnname"]}, $tablename);
                                    else
                                        $Record->{$field["columnname"]} = '';

                                    // //get primary key
                                    // Yii::$app->db->createCommand("select $columnname from $tablename where ");
                                    //$Record->{$field["columnname"]}= ucfirst($module->tablename);
                                }
                                //added on 21/dec/2024 by deepika show if conditional
                                $clsssdet = '';
                                if ($field['is_conditional'] == 1 && empty($Record->{$field["columnname"]})) {
                                    // $clsssdet = "tr-hidden";
                                    continue;
                                }

                                ?>

                                <div class="detail-group Details-1  detail-<?= $field["columnname"]; ?> <?= $clsssdet; ?>">
                                    <label
                                        title="<?= !empty($field["description"]) ? $field["description"] : $field["fieldlabel"] ?>"><?= $field["fieldlabel"]; ?></label>
                                        <span>
                                           <!-- code edited by ptpatel on date 12-03-2025 -->
                                        <!-- show edit button in lead summery  -->
                                        <div id="lead_display_<?= $field["columnname"]; ?>">
                                        <?= isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "-"; ?>
                                        <?php 
                                         $uitypeArray = [2,3,11,25,26,27,28,29,53,70];
                                         if($isedit && $field['single_edit'] == 0   && $field['fieldid'] != '193' && $field['fieldid'] != '1048' )
                                         {
                                                if($TabId != 7 )//lead tab
                                                {    
                                                    if(!in_array($field['uitype'], $uitypeArray) && $Block->blocklabel != 'SYSTEM GENERATED'  )  { 
                                                    ?><i class="fa-solid fa-pen single-edit-class" style="cursor: pointer;"
                                                    data-uitype="<?= $field['uitype'] ?>"
                                                    data-tabid="<?= $TabId ?>"
                                                    data-fieldlabel="<?= $field['fieldlabel'] ?>"
                                                    data-fieldid="<?= $field['fieldid'] ?>"
                                                    data-recordid="<?= $Recordid ?>"
                                                    data-columnname="<?= $field['columnname'] ?>"
                                                    data-view="summary"
                                                    onclick="singleEdit('<?= $field['uitype'] ?>','<?= $TabId; ?>','<?= $field['fieldlabel'] ?>','<?= $field['fieldid'] ?>','<?= $Recordid; ?>','<?= $field['columnname']?>','summary')"></i>
                                                        <?php }  
                                                }
                                                else
                                                {
                                                    if(!in_array($leadStatus, $leadStatusArray)  ){
                                                        if(!in_array($field['uitype'], $uitypeArray) && $Block->blocklabel != 'SYSTEM GENERATED')
                                                       {
                                                        ?><i class="fa-solid fa-pen single-edit-class" style="cursor: pointer;" 
                                                        data-uitype="<?= $field['uitype'] ?>"
                                                        data-tabid="<?= $TabId ?>"
                                                        data-fieldlabel="<?= $field['fieldlabel'] ?>"
                                                        data-fieldid="<?= $field['fieldid'] ?>"
                                                        data-recordid="<?= $Recordid ?>"
                                                        data-columnname="<?= $field['columnname'] ?>"
                                                        data-view="summary"
                                                      onclick="singleEdit('<?= $field['uitype'] ?>','<?= $TabId; ?>','<?= $field['fieldlabel'] ?>','<?= $field['fieldid'] ?>','<?= $Recordid; ?>','<?= $field['columnname']?>','summary')"></i>
                                                        <?php 
                                                       }
                                                    }
                                                    
                                                }
                                            }                                        
                                        ?>
                                        </div>
                                        
                                        <!-- code edited by ptpatel on date 12-03-2025 ended here-->
                                    </span>
                                </div>

                                <?php
                            }
                        }

                        // <!-- add conditon for dynamic fields -->
                        if ($TabId == 18 && $Block->blocklabel === "ORGANISATION SECTION") {
                            $tablnamee = 'vendor_account_orgaisation_section';
                            $sql = "select * from role where showinaccounts=1 and (parentrole like '%H2::%' || parentrole like '%H57::%')";
                            $result = Yii::$app->db->createCommand($sql)->queryAll();
                            // print_r($result);
                            $i = 1;
                            $count = count($result);
                            // echo $count;die;
                            foreach ($result as $value) {
                                $roleid = '-';
                                $sqlv = "select concat(first_name,' ',if(last_name is null,'',last_name)) as userid from $tablnamee join user on user.id = $tablnamee.userid where $tablnamee.roleid=:roleid and vendoraccid=:vendoraccid";
                                $vresult = Yii::$app->db->createCommand($sqlv)
                                    // ->bindParam(":tablnamee", $tablnamee)
                                    ->bindValue(":roleid", $value['roleid'])
                                    ->bindValue(":vendoraccid", $Recordid)
                                    ->queryOne();
                                if (!empty($vresult))
                                    $roleid = $vresult['userid'];
                                ?>
                                <div class="detail-group Details-1  detail-<?= $value['rolename'] ?> <?= $clsssdet; ?>">
                                    <label title="<?= $value['rolename'] ?>"><?= $value['rolename'] ?></label>
                                    <span><?= $roleid; ?></span>
                                </div>
                                <?php
                            }
                        }
                        //<!-- end conditon for dynamic fields -->
                        // <!-- add conditon for dynamic fields OEM MANAGER DETAILS -->
                        if ($TabId == 18 && $Block->blockid == 150) {
                            $tablnamee = 'vendor_account_oem_manager_detail';
                            $sql2 = "select * from role where showinaccounts=1 and parentrole not like '%H2::%'";
                            $result = Yii::$app->db->createCommand($sql2)->queryAll();
                            // print_r($result);
                            $i = 1;
                            $count = count($result);
                            // echo $count;die;
                            foreach ($result as $value) {
                                $roleid = '-';
                                $sqlv = "select concat(first_name,' ',if(last_name is null,'',last_name)) as userid from $tablnamee join user on user.id = $tablnamee.userid where $tablnamee.roleid=:roleid and vendoraccid=:vendoraccid";
                                $vresult = Yii::$app->db->createCommand($sqlv)
                                    // ->bindParam(":tablnamee", $tablnamee)
                                    ->bindValue(":roleid", $value['roleid'])
                                    ->bindValue(":vendoraccid", $Recordid)
                                    ->queryOne();
                                if (!empty($vresult))
                                    $roleid = $vresult['userid'];
                                ?>
                                <div class="detail-group Details-1  detail-<?= $value['rolename'] ?> <?= $clsssdet; ?>">
                                    <label title="<?= $value['rolename'] ?>"><?= $value['rolename'] ?></label>
                                    <span><?= $roleid; ?></span>
                                </div>
                                <?php
                            }
                        }
                        //<!-- end conditon for dynamic fields OEM MANAGER DETAILS -->
                        ?>
                    </div>
                </details>

                <?php
            } elseif ($Block->display_status == '1') {
                //for multi record
        
                if ($Block->blocktype == "Multiple" and !empty($Record)) {
                    // echo "<pre>";
                    // print_r($Block->detailfields);die;
                    $Multiple_table = $Block->detailfields[0]->tablename;
                    $modelname = convertToUcfirstOrPascalCase($Multiple_table);

                    $tbl = "app\models\\" . $modelname;
                    $newmod = new $tbl();
                    $MultiRecord = [];
                    if ($Multiple_table == 'product_costing_detail') {
                        $MultiRecord = $newmod->find()->where(['product_costing_id' => $Record])->all();
                    } else if ($Multiple_table == 'grn_item_detail') {
                        $MultiRecord = $newmod->find()->where(['grn_id' => $Record])->all();
                    } else if ($Multiple_table == 'purchase_order_itemsdetail') {
                        $MultiRecord = $newmod->find()->where(['purchase_order_id' => $Record])->all();
                    }
                    $cnt_multiple_product = count($MultiRecord);
                    // echo $cnt_multiple_product;die;
        
                }

                ?>
                <details class="c-faqs__item">
                    <summary class="c-faqs__item-question">
                        <?= $Block->blocklabel ?> <i class="fa-solid fa-angle-down"></i>
                    </summary>

                    <div class="details-container">

                        <?php
                        if (isset($MultiRecord)) {
                            foreach ($MultiRecord as $MRecord) {
                                foreach ($Block->detailfields as $field) { ?>

                                    <?php
                                    if ($field["uitype"] == 12 || $field["uitype"] == 27 || $field["uitype"] == 28) {
                                        $ref_hid_value = isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : '';
                                        $model1 = new Reference($TableName, $FieldId);
                                        $relatedmodulename = $model1->getRelatedNoduleName($field["fieldid"]);
                                        $getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldName($field["fieldid"]);
                                        if (isset($MRecord->{$field["columnname"]}) && $MRecord->{$field["columnname"]} != '')
                                            $MRecord->{$field["columnname"]} = $model1->getRefEntityValue($field["fieldid"], $ref_hid_value);
                                        else
                                            $MRecord->{$field["columnname"]} = '-';
                                    }
                                    else  if ($field["uitype"] == 31) {
                                        $ref_hid_value = isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : '';
                                        $model1 = new Reference($TableName, $FieldId);
                                        $relatedmodulename = $model1->getRelatedNoduleName($field["fieldid"]);
                                        $getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldName($field["fieldid"]);
                                        if (isset($MRecord->{$field["columnname"]}) && $MRecord->{$field["columnname"]} != '')
                                            $MRecord->{$field["columnname"]} = $model1->getRefEntityValue($field["fieldid"], $ref_hid_value);
                                        else
                                            $MRecord->{$field["columnname"]} = '-';
                                    }
                                    else if ($field["uitype"] == 17) {
                                        

                                        if (isset($MRecord->{$field["columnname"]}) && $MRecord->{$field["columnname"]} != "0000-00-00") {
                                           
                                            $date = $MRecord->{$field["columnname"]}; // original date in Y-m-d format
                        
                                            // Convert to a timestamp
                                            $timestamp = strtotime($date);
        
                                            // Format the timestamp to d-m-Y
                                            $MRecord->{$field["columnname"]} = date('d-m-Y', $timestamp);
                                        } else
                                            $MRecord->{$field["columnname"]} = '-';
                                    } 
                                    else if ($field["uitype"] == 13) {
        
                                        if (isset($MRecord->{$field["columnname"]}) && $MRecord->{$field["columnname"]} != "0000-00-00 00:00:00") {
                                           
                                            $date = $MRecord->{$field["columnname"]}; // original date in Y-m-d format
                        
                                            // Convert to a timestamp
                                            $timestamp = strtotime($date);
        
                                            // Format the timestamp to d-m-Y
                                            $MRecord->{$field["columnname"]} = date('d-m-Y H:i:s', $timestamp);
                                        } else
                                            $MRecord->{$field["columnname"]} = '-';
                                    } 
                                     else if ($field["uitype"] == 8 || $field["uitype"] == 10) {
                                        $modellist = new Listhire;
                                        if (isset($MRecord->{$field["columnname"]}))
                                            $MRecord->{$field["columnname"]} = $modellist->getPickListDetailvalue($field["fieldid"], $MRecord->{$field["columnname"]});
                                        else
                                            $MRecord->{$field["columnname"]};
                                    } else if ($field["uitype"] == 6) { //checkbox
                                        $modellist = new Listhire;
                                        if (isset($MRecord->{$field["columnname"]})) {
                                            if ($MRecord->{$field["columnname"]} == 1)
                                                $MRecord->{$field["columnname"]} = "Yes";
                                            else if ($MRecord->{$field["columnname"]} == 0)
                                                $MRecord->{$field["columnname"]} = "No";
                                        } else
                                            $MRecord->{$field["columnname"]};
                                    } else if ($field["uitype"] == 22 || $field["uitype"] == 9 || $field["uitype"] == 10) { //comma separated value
                                        $modellist = new Listhire;
                                        if (isset($MRecord->{$field["columnname"]}))
                                            $MRecord->{$field["columnname"]} = $modellist->getPickListDetailMultiple($field["fieldid"], $MRecord->{$field["columnname"]});
                                        else
                                            $MRecord->{$field["columnname"]};
                                    } else if ($field["uitype"] == 53) {
                                        $modellist = new Listhire;
                                        if (isset($MRecord->{$field["columnname"]}))
                                            $MRecord->{$field["columnname"]} = $modellist->getuser($field["fieldid"], $MRecord->{$field["columnname"]});
                                        else
                                            $MRecord->{$field["columnname"]};
                                    } else if ($field["uitype"] == 5 && $TabLabel == 'Documents') {
                                        if ($MRecord->{$field["columnname"]}) {
                                            $MRecords = \app\models\Attachments::find()
                                                ->where(['attachmentsid' => $MRecord->{$field["columnname"]}])
                                                ->one();
                                            //print_r($MRecords);die;
                                            $MRecord->{$field["columnname"]} = "<br><a href='" . $baseUrl . $ModuleName . "/download?fileid=" . $MRecord->{$field["columnname"]} . "'>" . $MRecords->name . "</a>";
                                        }
                                    }
                                    if ($field["columnname"] == "firstname") {
                                        if (isset($MRecord->{$field["columnname"]})) {
                                            // echo "deepika ".$MRecord['salutation'];die;   
                                            if (!empty($MRecord['salutation'])) {
                                                $modellist = new Listhire;

                                                $salutation = $modellist->getSalutation($MRecord['salutation']);

                                                $MRecord->{$field["columnname"]} = $salutation . " " . $MRecord->{$field["columnname"]};
                                            }
                                        }
                                    }
                                    if ($field['columnname'] != "salutation") {
                                        //$MRecord->{$field["columnname"]} ='';
                
                                        //check for related modules
                                        if ($field['columnname'] == 'related_to') {
                                            //get modulename
                                            $module = \app\models\Tab::find()
                                                ->where(['tabid' => $MRecord->{$field["columnname"]}])
                                                ->one();
                                            $MRecord->{$field["columnname"]} = ucfirst($module->name);
                                        }
                                        if ($field['columnname'] == 'related_to_id') {
                                            $related = \app\models\Tab::find()
                                                ->where(['name' => strtolower($MRecord['related_to'])])
                                                ->one();
                                            $relatedtab = $related['tabid'];
                                            //get modulename
                                            $module = \app\models\Field::find()
                                                ->where(['tabid' => $relatedtab])
                                                ->andWhere(['headerview' => 1])
                                                ->one();
                                            //print_r($module);die;
                                            $tablename = $module['tablename'];
                                            //$columnname = $module['tablename'];
                
                                            // $relatedmodulename = $model1->getRelatedNoduleName($field["fieldid"]);
                                            // $getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldName($field["fieldid"]);
                                            $model1 = new Reference($TableName, $FieldId);
                                            if (isset($MRecord->{$field["columnname"]}) && $MRecord->{$field["columnname"]} != '')
                                                $MRecord->{$field["columnname"]} = $model1->getMultiRefEntityValue($field["fieldid"], $MRecord->{$field["columnname"]}, $tablename);
                                            else
                                                $MRecord->{$field["columnname"]} = '';

                                            // //get primary key
                                            // Yii::$app->db->createCommand("select $columnname from $tablename where ");
                                            //$MRecord->{$field["columnname"]}= ucfirst($module->tablename);
                                        }
                                        ?>

                                        <div class="detail-group Details-1">
                                            <label
                                                title="<?= !empty($field["description"]) ? $field["description"] : $field["fieldlabel"] ?>"><?= $field["fieldlabel"]; ?></label>
                                            <span><?= isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : "-" ?></span>
                                        </div>

                                        <?php
                                    }
                                }
                            }
                        } ?>
                    </div>
                </details>
                <?php



            }
        } ?>
    </div>
</div>

