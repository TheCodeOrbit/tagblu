<?php
use app\models\ListHire;
use app\models\Reference;
// $skipDetail = ['block' => [2613,2781],'tab' => [6,14]];
$skipDetail = ['block' => [2613],'tab' => [6]];
?>
<div id="summary" class="tab-content-detail-view active"><!-- Summary Section start-->
    <div class="accordion">
        <?php
        $i = 1;
        $isedit = false;
        // if($i == 1 && isset($Record->leadstatus)){
        //     $leadStatus = $Record->leadstatus;
        //     $i++;
        // }
        if ($i == 1 && isset($Record->leadstatus)) {
            $leadStatus = $Record->leadstatus;
            $i++;
        }
        $leadStatusArray = [4, 5, 6, 9];
        $purchaseorderStatusArray = [4, 5, 6, 9];
        // echo $id;die;
        $id = Yii::$app->user->id;
        if (($hasadminpower == 1 || $Record->ownerid == $id)) {
            $isedit = true;
        }
        if($TabId == 18)
            $account_category = $Record->account_category;
        foreach ($ColumnList->blocks as $BlockKey => $Block) {
             /****
                 * as per sheet in  Deshwal_<CR>_Request Report Sheet_GivenByClient 
                 * hide show fields as per account field mapping sheet
                 * code start from here on date 17-12-2025
                 */
            if (
                $TabId == 18
                && (int)$account_category == 1
                && in_array((int)$Block->blockid, [147,148,149,150,151])
            ) {}
            else{
                    /****
                 * as per sheet in  Deshwal_<CR>_Request Report Sheet_GivenByClient 
                 * hide show fields as per account field mapping sheet
                 * code start from here on date 17-12-2025
                 */
                // if (!empty($Block->detailfields) && $Block->blocktype != "Multiple") {
                // adde on 14 jan 2025 to hide block with display_status == 0
                // if (!empty($Block->detailfields) && $Block->blocktype != "Multiple" && $Block->display_status = 1) {
                if (
                    !empty($Block->detailfields) && $Block->blocktype != "Multiple" &&
                    $Block->display_status == '1' || ($TabId == 18 && $Block->blockid == 150)
                ) {
                    ?>
                    <details class="c-faqs__item">
                        <summary class="c-faqs__item-question">
                            <?= $Block->blocklabel ?> <i class="fa-solid fa-angle-down"></i>
                        </summary>

                        <!-- onlyid part added by ptptael 18-03-25 -->
                        <div class="details-container" id="details-container">

                            <?php
                            foreach ($Block->detailfields as $field) { ?>

                                <?php
                                if ($field["uitype"] == 12 || $field["uitype"] == 27 || $field["uitype"] == 28) {
                                    $ref_hid_value = isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '';
                                    $model1 = new Reference($TableName, $FieldId);
                                    $relatedmodulename = $model1->getRelatedNoduleName($field["fieldid"]);
                                    $getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldName($field["fieldid"]);
                                    if (isset($Record->{$field["columnname"]}) && $Record->{$field["columnname"]} != '')
                                        $Record->{$field["columnname"]} = "<a href='" . $baseUrl . $relatedmodulename . "/detail?Record=" . $ref_hid_value . "'>" . $model1->getRefEntityValue($field["fieldid"], $ref_hid_value) . "</a>";
                                    else
                                        $Record->{$field["columnname"]} = '';

                                } 
                                else if ($field["uitype"] == 31) {
                                    $ref_hid_value_arr = isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '';
                                    if(!empty($ref_hid_value_arr))
                                    {
                                        $Record->{$field["columnname"]} = '';

                                    $exploded_ref_hid = explode(",",$ref_hid_value_arr);
                                    
                                    foreach($exploded_ref_hid as $ref_hid_value)
                                    {
                                        $model1 = new Reference($TableName, $FieldId);
                                        $relatedmodulename = $model1->getRelatedNoduleName($field["fieldid"]);
                                        $getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldName($field["fieldid"]);
                                    
                                            $Record->{$field["columnname"]} .= "<a href='" . $baseUrl . $relatedmodulename . "/detail?Record=" . $ref_hid_value . "'>" . $model1->getRefEntityValue($field["fieldid"], $ref_hid_value) . "</a>,";
                                        

                                    }
                                    
                                    }
                                

                                } else if ($field["uitype"] == 17) {

                                    if (isset($Record->{$field["columnname"]}) && $Record->{$field["columnname"]} != "0000-00-00") {

                                        $date = $Record->{$field["columnname"]}; // original date in Y-m-d format
                    
                                        // Convert to a timestamp
                                        $timestamp = strtotime($date);

                                        // Format the timestamp to d-m-Y
                                        $Record->{$field["columnname"]} = date('d-m-Y', $timestamp);
                                    } else
                                        $Record->{$field["columnname"]} = '-';
                                } else if ($field["uitype"] == 13) {

                                    if (isset($Record->{$field["columnname"]}) && $Record->{$field["columnname"]} != "0000-00-00 00:00:00") {

                                        $date = $Record->{$field["columnname"]}; // original date in Y-m-d format
                    
                                        // Convert to a timestamp
                                        $timestamp = strtotime($date);

                                        // Format the timestamp to d-m-Y
                                        $Record->{$field["columnname"]} = date('d-m-Y H:i:s', $timestamp);
                                    } else
                                        $Record->{$field["columnname"]} = '-';
                                } else if ($field["uitype"] == 8 || $field["uitype"] == 10) {
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
                                }/* else if ($field["uitype"] == 5) {
                                    if ($Record->{$field["columnname"]}) {
                                        $records = \app\models\Attachments::find()
                                            ->where(['attachmentsid' => $Record->{$field["columnname"]}])
                                            ->one();
                                        //print_r($records);die;
                                        if ($records) {
                                            $Record->{$field["columnname"]} = "<br><a href='" . $baseUrl . $ModuleName . "/download?fileid=" . $Record->{$field["columnname"]} . "'>" . $records->name . "</a>";
                                        } else {
                                            $Record->{$field["columnname"]} = "";
                                        }
                                    }
                                }*/
                                    else if ($field["uitype"] == 5) {
                                        if ($Record->{$field["columnname"]}) {
                                            $records = \app\models\Attachments::find()
                                                ->where(['attachmentsid' => $Record->{$field["columnname"]}])
                                                ->one();

                                            if ($records) {

                                                $fileName = $records->name;
                                                $fileId   = $Record->{$field["columnname"]};
                                                $fileUrl  = $baseUrl . $ModuleName . "/download?fileid=" . $fileId;

                                                // Detect extension
                                                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                                                // Default icon
                                                $iconClass = "fileicon_img.svg";

                                                if ($ext === 'pdf') {
                                                    $iconClass = 'fileicon_pdf.svg';
                                                } elseif ($ext === 'xls') {
                                                    $iconClass = 'fileicon_xls.svg';
                                                } elseif ($ext === 'xlsx') {
                                                    $iconClass = 'fileicon_xlsx.svg';
                                                } elseif ($ext === 'msg') {
                                                    $iconClass = 'fileicon_msg.svg';
                                                } elseif ($ext === 'eml') {
                                                    $iconClass = 'fileicon_eml.svg';
                                                } elseif ($ext === 'zip') {
                                                    $iconClass = 'fileicon_zip.svg';
                                                } elseif (in_array($ext, ['jpg','jpeg','png','svg','webp'])) {
                                                    $iconClass = 'fileicon_img.svg';
                                                }

                                                $iconPath = Yii::$app->homeUrl . 'thememain/img/file-icon/' . $iconClass;

                                                // Hover thumbnail for images
                                                $hoverThumb = '';
                                                if (in_array($ext, ['jpg','jpeg','png','svg','webp'])) {
                                                    $hoverThumb = "<img src='{$fileUrl}' class='file-hover-thumb'>";
                                                }

                                                $Record->{$field["columnname"]} = "<br>
                                                    <div class='file-preview-wrapper' style='display:inline-flex;align-items:center;gap:6px;'>
                                                        <img src='{$iconPath}' class='file-icon-img' alt='{$ext}' style='width:24px;height:24px;'>
                                                        {$hoverThumb}
                                                        <a href='{$fileUrl}'>{$fileName}</a>
                                                    </div>";
                                            } else {
                                                $Record->{$field["columnname"]} = "";
                                            }
                                        }
                                    }
                                if ($field["columnname"] == "firstname") {
                                    if (isset($Record->{$field["columnname"]})) {
                                        if (!empty($Record['salutation'])) {
                                            $modellist = new Listhire;

                                            $salutation = $modellist->getSalutation($Record['salutation']);

                                            $Record->{$field["columnname"]} = $salutation . " " . $Record->{$field["columnname"]};
                                        }
                                    }
                                }
                                if ($field['columnname'] != "salutation") {
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
                                        $tablename = $module['tablename'];
                                        $model1 = new Reference($TableName, $FieldId);
                                        if (isset($Record->{$field["columnname"]}) && $Record->{$field["columnname"]} != '')
                                            $Record->{$field["columnname"]} = $model1->getMultiRefEntityValue($field["fieldid"], $Record->{$field["columnname"]}, $tablename);
                                        else
                                            $Record->{$field["columnname"]} = '';
                                    }
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
                                            <!-- code added by ptpatel on date 24-03-25 -->
                                            <div id="lead_display_<?= $field["columnname"]; ?>">
                                                <?= isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "-" ?>

                                                <?php
                                                $uitypeArray = [2, 3, 11, 25, 26, 27, 28, 29, 53, 70];
                                                // 
                                                if ($isedit && $field['single_edit'] == 0 && $field['fieldid'] != '193') {
                                                    if ($TabId != 7)//lead tab
                                                    {
                                                        if (!in_array($field['uitype'], $uitypeArray) && $Block->blocklabel != 'SYSTEM GENERATED') {
                                                            ?><i class="fa-solid fa-pen single-edit-class" style="cursor: pointer;"
                                                            data-uitype="<?= $field['uitype'] ?>"
                                                            data-tabid="<?= $TabId ?>"
                                                            data-fieldlabel="<?= $field['fieldlabel'] ?>"
                                                            data-fieldid="<?= $field['fieldid'] ?>"
                                                            data-recordid="<?= $Recordid ?>"
                                                            data-columnname="<?= $field['columnname'] ?>"
                                                            data-view="multiple"
                                                                onclick="singleEdit('<?= $field['uitype'] ?>','<?= $TabId; ?>','<?= $field['fieldlabel'] ?>','<?= $field['fieldid'] ?>','<?= $Recordid; ?>','<?= $field['columnname'] ?>','multiple')"></i>
                                                        <?php }
                                                    } else {
                                                        if (!in_array($leadStatus, $leadStatusArray)) {
                                                            if (!in_array($field['uitype'], $uitypeArray) && $Block->blocklabel != 'SYSTEM GENERATED') {
                                                                ?><i class="fa-solid fa-pen single-edit-class" style="cursor: pointer;"
                                                                    data-uitype="<?= $field['uitype'] ?>"
                                                                    data-tabid="<?= $TabId ?>"
                                                                    data-fieldlabel="<?= $field['fieldlabel'] ?>"
                                                                    data-fieldid="<?= $field['fieldid'] ?>"
                                                                    data-recordid="<?= $Recordid ?>"
                                                                    data-columnname="<?= $field['columnname'] ?>"
                                                                    data-view="multiple"
                                                            onclick="singleEdit('<?= $field['uitype'] ?>','<?= $TabId; ?>','<?= $field['fieldlabel'] ?>','<?= $field['fieldid'] ?>','<?= $Recordid; ?>','<?= $field['columnname'] ?>','multiple')"></i>
                                                                <?php
                                                            }
                                                        }

                                                    }
                                                }
                                                ?>
                                            </div><!-- end code added by ptpatel on date 24-03-25 -->

                                        </span>
                                    </div>
                                    <?php
                                }
                            } 
                            // <!-- add conditon for dynamic fields -->
                            if ($TabId == 18 && $Block->blockid == 149) {
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
                } else {
                    //for multi record
                    if ($Block->blocktype == "Multiple" and !empty($Record)) {
                        // echo "<pre>";
                        // print_r($Block->detailfields);die;
                        $Multiple_table = $Block->detailfields[0]->tablename;
                        $modelname = convertToUcfirstOrPascalCase($Multiple_table);
                        // print_r($Multiple_table);die;
                        $tbl = "app\models\\" . $modelname;
                        $newmod = new $tbl();
                        $MultiRecord = [];
                        if ($Multiple_table == 'product_costing_detail' && $Recordid) {
                            $MultiRecord = $newmod->find()->where(['product_costing_id' => $Recordid])->all();
                        } else if ($Multiple_table == 'grn_item_detail' && $Recordid) {
                            $MultiRecord = $newmod->find()->where(['grn_id' => $Recordid])->all();
                        } else if ($Multiple_table == 'purchase_order_itemsdetail' && $Recordid) {
                            $MultiRecord = $newmod->find()->where(['purchase_order_id' => $Recordid])->all();
                        } else if ($Multiple_table == 'user_targets' && $Recordid) {
                            $MultiRecord = $newmod->find()->where(['userid' => $Recordid])->all();
                        } else {
                            $MultiRecord = $newmod->find()->where([$FieldId => $Recordid])->all();
                        }
                        $cnt_multiple_product = count($MultiRecord);
                    }

                    ?>
                    <details class="c-faqs__item">
                        <summary class="c-faqs__item-question">
                            <?= $Block->blocklabel ?> <i class="fa-solid fa-angle-down"></i>
                        </summary>
                        <div class="row-12" style="display:flex;">

                        
                        <?php
                        if($exportpermission == 1)
                        {?>
                        <!-- Button to trigger the export -->
                        <a href="<?= Yii::$app->urlManager->createUrl([
                            '/'.$ModuleName.'/exportitems',
                            'record' => $Recordid,
                            'section' => $Block->blockid
                        ]) ?>" target="_blank">
                            <button class="exportBtnn btn-sm" data-section="<?= $Block->blockid ?>">Export to Excel</button>
                        </a>
                        <?php
                        }
                        if(in_array($Block->blockid,$skipDetail['block'])){
                            $idT = 'popup-bulk-upload-btn';
                            $styleT= 'display:block';
                            if(isset($skipDetail['tab']) && in_array(14,$skipDetail['tab'])){
                                $idT ='so-bulk-import-btn';
                                $styleT = 'display:none';
                            }
                        ?>
                        <button class="btn btn-primary btn-sm" style="margin: auto;" id="<?=  $idT;  ?>" type="button" fdprocessedid="fiehra">Bulk Upload CSV</button>
                        <input type="file" id="popup-bulk-upload-file" accept=".csv" style="display:none">
                        <button type="button" class="btn btn-primary ml-2 btn-sm" id="sample-download-btn" style="margin:auto;<?= $styleT ?>">Sample Download</button>
                        <button type="button" id="documentation-main-btn" style="margin: auto;<?= $styleT ?>" class="btn btn-primary btn-sm" fdprocessedid="ibfdqd">Instruction</button>
                        <button type="button" id="detail-btn-asset" style="margin: auto;" class="btn btn-primary btn-sm" fdprocessedid="ibfdqd">View Asset Details</button>
                        <?php } ?>    
                        </div>
                        <!-- start of data import  -->
                        <?php if(isset($Block->blockid) && $Block->blockid == 2740 && isset($AllowBulkImportLaptop) && $AllowBulkImportLaptop == true){ ?>
                            <button class="import-btn" data-section="<?= $Block->blockid ?>" data-record="<?= $Recordid ?>" data-bs-toggle="modal" data-bs-target="#dataimport-modal">Import Excel</button>
                        <?php } ?>
                        <?php if(isset($Block->blockid) && $Block->blockid == 2741 && isset($AllowBulkImportDesktop) && $AllowBulkImportDesktop == true){ ?>
                            <button class="import-btn" data-section="<?= $Block->blockid ?>" data-record="<?= $Recordid ?>"  data-bs-toggle="modal" data-bs-target="#dataimport-modal">Import Excel</button>
                        <?php } ?>
                        <?php if(isset($Block->blockid) && $Block->blockid == 2742 && isset($AllowBulkImportTft) && $AllowBulkImportTft == true){ ?>
                            <button class="import-btn" data-section="<?= $Block->blockid ?>" data-record="<?= $Recordid ?>"  data-bs-toggle="modal" data-bs-target="#dataimport-modal">Import Excel</button>
                        <?php } ?>
                        <?php if(isset($Block->blockid) && $Block->blockid == 2739 && isset($AllowBulkImportRandom) && $AllowBulkImportRandom == true){ ?>
                            <button class="import-btn" data-section="<?= $Block->blockid ?>" data-record="<?= $Recordid ?>"  data-bs-toggle="modal" data-bs-target="#dataimport-modal">Import Excel</button>
                        <?php } ?>
                        <?php if(isset($Block->blockid) && $Block->blockid == 2761 && isset($AllowBulkImportGrnditBarcodes) && $AllowBulkImportGrnditBarcodes == true){ ?>
                            <button class="import-btn" data-section="<?= $Block->blockid ?>" data-record="<?= $Recordid ?>"  data-bs-toggle="modal" data-bs-target="#dataimport-modal">Import Excel</button>
                        <?php } ?>
                        <!-- end of data import  -->
                        <div id="section<?= $Block->blockid ?>" class=" details-container-multiple-new">
                            <table id="table<?= $Block->blockid ?>" class="expdynamicTable table table-striped">
                                <thead>
                                    <!-- figure out table headings -->
                                    <tr>
                                        <?php
                                        if (isset($MultiRecord)) {
                                            foreach ($Block->detailfields as $field) { ?>
                                                <th>
                                                    <?= $field["fieldlabel"] ?? ""; ?>
                                                </th>
                                                <?php
                                            }
                                        } ?>
                                    </tr>
                                    <!-- end ot thead  -->
                                </thead>
                                <tbody>
                                    <?php
                                    if (isset($MultiRecord)) {
                                        foreach ($MultiRecord as $MRecord) { ?>
                                            <tr>
                                                <?php
                                                foreach ($Block->detailfields as $field) {
                                                    if(in_array($field->block,$skipDetail['block'])  && in_array($field->tabid,$skipDetail['tab'])){
                                                        continue;
                                                    }  ?>

                                                    <?php
                                                    if ($field["uitype"] == 12 || $field["uitype"] == 27 || $field["uitype"] == 28) {
                                                        $ref_hid_value = isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : '';
                                                        $model1 = new Reference($TableName, $FieldId);
                                                        $relatedmodulename = $model1->getRelatedNoduleName($field["fieldid"]);
                                                        $getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldName($field["fieldid"]);
                                                        if (isset($MRecord->{$field["columnname"]}) && $MRecord->{$field["columnname"]} != '')
                                                            $MRecord->{$field["columnname"]} = $model1->getRefEntityValue($field["fieldid"], $ref_hid_value);
                                                        else
                                                            $MRecord->{$field["columnname"]} = '';
                                                    } else if ($field["uitype"] == 17) {

                                                        if (isset($MRecord->{$field["columnname"]}) && $MRecord->{$field["columnname"]} != "0000-00-00") {

                                                            $date = $MRecord->{$field["columnname"]}; // original date in Y-m-d format
                                
                                                            // Convert to a timestamp
                                                            $timestamp = strtotime($date);

                                                            // Format the timestamp to d-m-Y
                                                            $MRecord->{$field["columnname"]} = date('d-m-Y', $timestamp);
                                                        } else
                                                            $MRecord->{$field["columnname"]} = '-';
                                                    } else if ($field["uitype"] == 13) {

                                                        if (isset($MRecord->{$field["columnname"]}) && $MRecord->{$field["columnname"]} != "0000-00-00 00:00:00") {

                                                            $date = $MRecord->{$field["columnname"]}; // original date in Y-m-d format
                                
                                                            // Convert to a timestamp
                                                            $timestamp = strtotime($date);

                                                            // Format the timestamp to d-m-Y
                                                            $MRecord->{$field["columnname"]} = date('d-m-Y H:i:s', $timestamp);
                                                        } else
                                                            $MRecord->{$field["columnname"]} = '-';
                                                    } else if ($field["uitype"] == 8 || $field["uitype"] == 10) {
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
                                                        // } else if ($field["uitype"] == 5 && $TabLabel == 'Documents') {
                                                    } /*else if ($field["uitype"] == 5) { 
                                                        if ($MRecord->{$field["columnname"]}) {
                                                            $MRecords = \app\models\Attachments::find()
                                                                ->where(['attachmentsid' => $MRecord->{$field["columnname"]}])
                                                                ->one();
                                                            //print_r($MRecords);die;
                                                            $MRecord->{$field["columnname"]} = "<br>" . $MRecords->name . " <a href='" . $baseUrl . $ModuleName . "/download?fileid=" . $MRecord->{$field["columnname"]} . "'><i class='fa fa-download' aria-hidden='true' title='download'></i></a>";
                                                        }
                                                    }*/ else if ($field["uitype"] == 5) { 

                                                        if ($MRecord->{$field["columnname"]}) {

                                                            $MRecords = \app\models\Attachments::find()
                                                                ->where(['attachmentsid' => $MRecord->{$field["columnname"]}])
                                                                ->one();

                                                            if ($MRecords) {

                                                                $fileName = $MRecords->name;
                                                                $fileId   = $MRecord->{$field["columnname"]};
                                                                $fileUrl  = $baseUrl . $ModuleName . "/download?fileid=" . $fileId;

                                                                // Detect extension
                                                                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                                                                // Default icon
                                                                $iconClass = "fileicon_img.svg";

                                                                if ($ext === 'pdf') {
                                                                    $iconClass = 'fileicon_pdf.svg';
                                                                } elseif ($ext === 'xls') {
                                                                    $iconClass = 'fileicon_xls.svg';
                                                                } elseif ($ext === 'xlsx') {
                                                                    $iconClass = 'fileicon_xlsx.svg';
                                                                } elseif ($ext === 'msg') {
                                                                    $iconClass = 'fileicon_msg.svg';
                                                                } elseif ($ext === 'eml') {
                                                                    $iconClass = 'fileicon_eml.svg';
                                                                } elseif ($ext === 'zip') {
                                                                    $iconClass = 'fileicon_zip.svg';
                                                                } elseif (in_array($ext, ['jpg','jpeg','png','svg','webp'])) {
                                                                    $iconClass = 'fileicon_img.svg';
                                                                }

                                                                $iconPath = Yii::$app->homeUrl . 'thememain/img/file-icon/' . $iconClass;

                                                                $hoverThumb = '';
                                                                if (in_array($ext, ['jpg','jpeg','png','svg','webp'])) {
                                                                    $hoverThumb = "<img src='{$fileUrl}' class='file-hover-thumb'>";
                                                                }

                                                                $MRecord->{$field["columnname"]} = "
                                                                    <div class='file-preview-wrapper' style='display:inline-flex;align-items:center;gap:6px;'>
                                                                        <img src='{$iconPath}' class='file-icon-img' alt='{$ext}'>
                                                                        {$hoverThumb}
                                                                        {$fileName}
                                                                        <a href='{$fileUrl}' title='Download'>
                                                                            <i class='fa fa-download'></i>
                                                                        </a>
                                                                    </div>
                                                                ";
                                                            }
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
                                                        <td>
                                                            <?= isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : "-" ?>
                                                        </td>
                                                        <?php
                                                    }
                                                } ?>
                                            </tr>
                                            <?php
                                        }
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </details>
                    <?php
                }
            }
        } ?>
    </div>
</div><!-- Summary Section end-->