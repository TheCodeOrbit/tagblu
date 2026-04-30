<?php
use app\models\ListHire;
use app\models\Reference;

function convertToUcfirstOrPascalCase($string)
{
    // Check if the string contains underscores
    if (strpos($string, '_') !== false) {
        // Convert to PascalCase by splitting, capitalizing each part, and joining
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    } else {
        // Capitalize the first letter of the string
        return ucfirst($string);
    }
}


?>
<?php
echo '<html><head><meta charset="utf-8"></head><body>';

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

foreach ($ColumnList->blocks as $BlockKey => $Block) {

    // if (!empty($Block->detailfields) && $Block->blocktype != "Multiple") {
    // adde on 14 jan 2025 to hide block with display_status == 0
    if ($Block->blockid == $blockid) {
        $label = $Block->blocklabel;
        if (!empty($Block->detailfields) && $Block->blocktype != "Multiple" && $Block->display_status = 1) {
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
                        } else if ($field["uitype"] == 5) {
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
                    } ?>
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


            
                <table>
                    <thead>
                        
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
                      
                    </thead>
                    <tbody>
                        <?php
                        if (isset($MultiRecord)) {
                            foreach ($MultiRecord as $MRecord) { ?>
                                <tr>
                                    <?php
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
                                        } else if ($field["uitype"] == 5) {
                                            if ($MRecord->{$field["columnname"]}) {
                                                $MRecords = \app\models\Attachments::find()
                                                    ->where(['attachmentsid' => $MRecord->{$field["columnname"]}])
                                                    ->one();
                                                //print_r($MRecords);die;
                                                $MRecord->{$field["columnname"]} = "<br>" . $MRecords->name . " <a href='" . $baseUrl . $ModuleName . "/download?fileid=" . $MRecord->{$field["columnname"]} . "'><i class='fa fa-download' aria-hidden='true' title='download'></i></a>";
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
           
            <?php
        }
    }
}
echo '</body></html>';

header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=$label-".date('Y-m-d').".xls");
header("Pragma: no-cache");
header("Expires: 0");
die;
?>
