<?php

use yii\helpers\Html;
use common\models\Fieldtype;
use backend\models\AccessCheck;
use common\models\Picklist;
use common\models\Multilist;
use app\models\Reference;
use backend\assets\AdminAsset;
$baseUrl = Yii::$app->HomeUrl;
AdminAsset::register($this);
$this->title = Yii::t('app', "Add " . $TabLabel);
$counter = 0;
$relationName = $action_name === 'create' ? 'createfields' : 'editfields';
$cnt_rows = 1;

$MAX_RECORD_COUNT = 0;
if ((int)$TabId === 14) {
    $MAX_RECORD_COUNT = 101;
}
$putcheck = 0;
$renderedCount = 0; 
$showBinColumn = false;
$laptop_id = (new \yii\db\Query())
    ->select(['sub_catagory_id'])
    ->from('prod_sub_catagory')
    ->where(['LOWER(TRIM(sub_catagory_value))' => 'laptop'])
    ->scalar();
?>


<div class="row">

    <div class="col-12">
        <div class="table-container">
            <?php
            $shrinkClass = '';
            if ($TabId == "68" || $TabId == "67") {
                $shrinkClass = 'shrinkcols';
            }
            ?>
            <table id="productTable<?= $block->blockid; ?>" class="multipleTable <?= $shrinkClass; ?>">
                <thead>
                    <tr>
                        <?php
                        //code added by ptpatel on date 19-04-25
                        $colIndex = 1;
                        foreach ($block->$relationName as $field) {
                            //below line added by ptpatel for auto width of input field
                            $widthClass = $field['dynamic_class'] != NULL && $field['dynamic_class'] != 'col-pinned' ? $field['dynamic_class'] : 'productinput';
                            $isSetPinIcon = '';
                            $isSetPin = '';
                            if(isset($field['dynamic_class']) && strpos($field['dynamic_class'], 'col-pinned') !== false){
                                $isSetPinIcon = 'pin-icon fa';
                                $isSetPin = ' col-pinned';
                            }
                            $subCategory = null;
                            if ($hasadminpower == 1) {
                                $visible = 0;
                                $readonly = 0;
                            } else {
                                //now check if this field is allowed to edit ,readonly etc
                                $fieldid = $field->fieldid;
                                $model = new AccessCheck();
                                $permission = $model->fieldacces($uid, $fieldid);
                                
                                // if($fieldid == 1834)
                                // print_r($permission);die;
                                if (is_array($permission)) {
                                    $visible = $permission['visible'];
                                    $readonly = $permission['readonly'];
                                } else {//remove when fieldaccess is implemented properly
                                    $visible = 0;
                                    $readonly = 0;
                                }
                            }
                            
                            if ($TabId == 68 && Yii::$app->request->get('itemid')) {
                                $itemParts = explode("_", Yii::$app->request->get('itemid'));
                                $subCategory = $itemParts[2] ?? null;
                            }

                            // Special check for fieldid = 2753
                            // if ($field["fieldid"] == "2753") {
                            if ($field["fieldname"] == "bin_number") {
                                // Only show fieldid 2753 if subCategory == 41
                                if ($subCategory == $laptop_id) {
                                    ?>
                                    <th class='<?= $widthClass; ?> <?= $isSetPin; ?>'
                                        title="<?= !empty($field["description"]) ? $field["description"] : $field["fieldlabel"] ?>" data-col="<?= $colIndex ?>">
                                        <?= $field["fieldlabel"]; ?><span class="<?php echo $isSetPinIcon; ?>" style="color: #add8e6;margin:5px;" data-col="<?= $colIndex ?>">
                                    </span>
                                    </th>
                                    <?php
                                }
                                // else: skip this field if subCategory != 41
                            } else if ($field["uitype"] != "2" && $visible == 0 ) {
                                // Show all other fields normally
                                ?>
                                    <th class='<?= $widthClass; ?> <?= $isSetPin; ?>'
                                        title="<?= !empty($field["description"]) ? $field["description"] : $field["fieldlabel"] ?>" data-col="<?= $colIndex ?>">
                                    <?= $field["fieldlabel"]; ?><span class="<?php echo $isSetPinIcon; ?> fa-unlock" style="color: #add8e6;margin:5px;" data-col="<?= $colIndex ?>">
                                    </span>
                                    </th>
                                <?php
                            }
                            $colIndex++;
                        } 
                         if (!in_array($TabId, [69, 70,12,42,13,78,88])) { ?>
                        <th class='col-80'>Action</th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>


                    <?php
                    $cnt_multiple_product = 0;
                    // echo "<pre>".$RecordId;
                    if ($block->blocktype == "Multiple" and !empty($RecordId)) {
                        $Multiple_table = $block->$relationName[0]->tablename;
                        $modelname = convertToUcfirstOrPascalCase($Multiple_table);

                        $tbl = "app\models\\" . $modelname;
                        $newmod = new $tbl();
                        $MultiRecord = [];
                        if ($Multiple_table == 'product_costing_detail') {
                            $MultiRecord = $newmod->find()->where(['product_costing_id' => $RecordId])->all();
                        } else if ($Multiple_table == 'user_targets') {
                            $MultiRecord = $newmod->find()->where(['userid' => $RecordId])->all();
                        } else {
                            // echo $FieldId;
                            // echo "<br>";
                            // echo $RecordId;
                            // echo "<br>";
                    
                            $MultiRecord = $newmod->find()->where([$FieldId => $RecordId])->all();
                        }
                        $cnt_multiple_product = count($MultiRecord);
                        // echo $cnt_multiple_product;die;
                    
                    }//die;
                    if ((int)$TabId === 14 && $MAX_RECORD_COUNT && $cnt_multiple_product >= $MAX_RECORD_COUNT && $putcheck ==1) {
                        echo '<tr><td colspan="100%" class="text-danger"><strong>Record is more than 150.</strong></td></tr>';
                    } else if ($cnt_multiple_product > 0) {
                        $cnt_rows = 1;//$cnt_multiple_product;
                        // echo "<pre>";
                        // print_r($MultiRecord);
                        // print_r('COunt');
                        // print_r(count($MultiRecord)); 
                        // die;
                        
                        foreach ($MultiRecord as $MRecord) {
                            ?>
                            <tr id=" <?= $cnt_rows; ?>" class="product-row">
                                <?php
                                if ($field['tabid'] == '67')
                                    echo '<input type="hidden" id="prod_weight_' . $cnt_rows . '" name="' . $field["tablename"] . '[' . $cnt_rows . '][prod_weight]" class = "prod_weight" value="' . $MRecord->prod_weight . '"/>';
        
                                foreach ($block->$relationName as $field) {
                                    //below line added by ptpatel for auto width of input field
                                    $widthClass = $field['dynamic_class'] != NULL && $field['dynamic_class'] != 'col-pinned'  ? $field['dynamic_class'] : 'wdinput';

                                    // echo "<pre>";
                                    // print_r($block);die;
                                    // echo $field["columnname"]."<br>";
                                    // echo $MRecord->{$field["columnname"]}."<br>";
                        

                                    $fieldid = $field->fieldid;
                                    if ($hasadminpower == 1) {
                                        $visible = 0;
                                        $readonly = 0;
                                    } else {
                                        //now check if this field is allowed to edit ,readonly etc
                                        $model = new AccessCheck();
                                        $permission = $model->fieldacces($uid, $fieldid);
                                        // echo $fieldid;
                                        // print_r($permission);die;
                                        if (is_array($permission)) {
                                            $visible = $permission['visible'];
                                            $readonly = $permission['readonly'];
                                        } else {//remove when fieldaccess is implemented properly
                                            $visible = 0;
                                            $readonly = 0;
                                        }
                                    }
                                    if ($field['readonly'] == 0)
                                        $readonly = 1;
                                    // print_r($field);die;
                                    //else echo $permission; exit();
                                    if ($field["uitype"] != 70 && $visible == 0) {
                                        $FieldTypeRecord = Fieldtype::find()->where(['uitype' => $field["uitype"]])->one();
                                        $field["fieldtype"] = $FieldTypeRecord['getfieldtype'];
                                        $field["classname"] = $FieldTypeRecord['classname'];
                                    }

                                    //for dropdownclass
                                    $read = $readonly ? 'readonly-dd' : '';


                                    if (($field["uitype"] == 8 || $field["uitype"] == 25 || $field["uitype"] == 24 || $field["uitype"] == 9 || $field["uitype"] == 10) && $visible == 0) {
                                        $PickList = new Picklist;
                                        $PickList->fieldid = $field["fieldid"];
                                        // $Column->blocks[$BlockKey]->fields[$FieldKey]->fieldoptions=$PickList->getPickListOption($table_name);
                        
                                        if ($field['fieldname'] == 'vertical_manager')
                                            $field["fieldoptions"] = $PickList->getVerticalManager($ModuleName);
                                        else
                                            $field["fieldoptions"] = $PickList->getPickListOption($ModuleName);
                                        if ($ModuleName == "leads" && $field["fieldname"] == "leadstatus") {
                                            //print_r($MRecord['vertical_manager']);die;
                                            if ($ActionName == "Create") {
                                                //limit  fieldoptons
                                                // Keys to remove
                                                $keysToRemove = [3, 13, 5];
                                                $arraykey = $field["fieldoptions"];
                                                // Remove elements
                                                $field["fieldoptions"] = array_diff_key($field["fieldoptions"], array_flip($keysToRemove));

                                                // print_r($field["fieldoptions"]);die;
                                            } else if ($ActionName == "Edit" && $MRecord["vertical_manager"] == Yii::$app->user->id) {
                                                //limit  fieldoptons
                                                // Keys to remove
                                                $keysToRemove = [1, 2, 4, 6, 7, 8, 9, 10, 11, 12];
                                                $arraykey = $field["fieldoptions"];
                                                // Remove elements
                                                $field["fieldoptions"] = array_diff_key($field["fieldoptions"], array_flip($keysToRemove));

                                                // print_r($field["fieldoptions"]);die;
                        
                                            }
                                        }
                                    } else if ($field["uitype"] == 22 && $visible == 0) {
                                        $PickList = new Multilist;
                                        $PickList->fieldid = $field["fieldid"];
                                        // $Column->blocks[$BlockKey]->fields[$FieldKey]->fieldoptions=$PickList->getMultiListOption($table_name);
                        

                                        $field["fieldoptions"] = $PickList->getMultiListOption($ModuleName);
                                    }
                                    //print_r($FieldTypeRecord);die;
                                    if ($field["uitype"] != 2 && $field["fieldid"] != 19 && $field["uitype"] != 11 && $field["uitype"] != 70 && $field["uitype"] != 53 && $visible == 0) {
                                        //hide this because it is creating issue in layout of td tr
                                        $clss = '';
                                        // if ($mandatory == 1)
                                        //     $clss = "required-field";
                                        // else
                                        //     $clss = 'not-required-field';
                                        //close/open divs
                                        if ($field["fieldname"] == "bin_number" && !$showBinColumn) {
                                            // Do not show the bin number column's td
                                        } else {
                                            if ($counter == 0) {

                                                // echo '<td class="wdinput 1 form-group  ' . $clss . ' form-field-cst section-' . $field["columnname"] . '  ">';
                                                echo '<td class="' . $widthClass . ' 1 form-group  ' . $clss . ' form-field-cst section-' . $field["columnname"] . ' col-pinned">';
                                            } else {
                                                // echo '</td><td class="wdinput form-group  ' . $clss . ' form-field-cst section-' . $field["columnname"] . ' ">';
                                                echo '</td><td class="' . $widthClass . ' form-group  ' . $clss . ' form-field-cst section-' . $field["columnname"] . '  col-pinned">';

                                            }
                                        }






                                    }
                                    if ($field["uitype"] == 12 || $field["uitype"] == 26 || $field["uitype"] == 28) {
                                        $popupclass = "ref-form-control";
                                    } else {
                                        $popupclass = '';
                                    }
                                    if ($readonly == 1) {

                                        //added on 20 march 2025
                                        $typeofdata = str_replace("~M", "~O", $field["typeofdata"]);
                                        $mandatory = 0;
                                    } else {
                                        $typeofdata = $field['typeofdata'];
                                        $mandatory = $field['mandatory'];
                                    }

                                    //check mandatory
                                    if ($mandatory == 1) {

                                        //echo $field["maximumlength"];
                                        $classarray = array(
                                            'class' => $field["fieldname"] . ' productinput form-control ' . $popupclass . ' ' . $typeofdata,
                                            'id' => $field["fieldname"] . "_" . $cnt_rows,
                                            "fieldid" => $field["fieldid"],
                                            "value" => isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : "",
                                            'maxlength' => $field["maximumlength"],
                                            'data-pristine-required' => 'true',
                                            'data-pristine-required-message' => $field["fieldlabel"] . ' is required ',
                                            'readonly' => $readonly ? true : false

                                        );
                                        $mandatoryspan = "<span class='red'> *</span>";
                                    } else {
                                        $classarray = array(
                                            'class' => $field["fieldname"] . " " . $popupclass . ' productinput form-control ' . $readonly . ' ' . $typeofdata,
                                            'readonly' => $readonly ? true : false,
                                            'id' => $field["fieldname"] . "_" . $cnt_rows,
                                            'maxlength' => $field["maximumlength"],
                                            "value" => isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : ""
                                        );
                                        $mandatoryspan = "";
                                    }


                                    ?>

                                    <?php

                                    if ($field["uitype"] == 1 && $visible == 0) {
                                        if ($field["fieldid"] == 8) {
                                            $PickList = new Picklist;
                                            $PickList->fieldid = 19;
                                            // $Column->blocks[$BlockKey]->fields[$FieldKey]->fieldoptions=$PickList->getPickListOption($table_name);
                        

                                            $fieldoptions = $PickList->getPickListOption($ModuleName);
                                            // print_r($fieldoptions);
                                            // echo $MRecord['salutation'];die;
                                            $selesalu = isset($MRecord['salutation']) ? $MRecord['salutation'] : "";
                                            echo '<div class="form-group">
			                                    <div class="input-group mb-2">
			                                        <div class="input-group-prepend">
			                                            <div class="input-group-text" style="background: none;">
			                                            <Select style="background: none;
			  border: none;
			  padding: 2px;" name=' . $field["tablename"] . '[' . $cnt_rows . ']' . '[salutation]">
			  <option class="opt-none">None</option>';
                                            foreach ($fieldoptions as $key => $value) {
                                                if ($selesalu == $key)
                                                    $sel = "selected";
                                                else
                                                    $sel = '';
                                                # code...
                                                echo '<option class="opt-none" ' . $sel . ' value="' . $key . '">' . $value . '</option>';
                                            }

                                            echo '</select>

			  </div>
			                                        </div>' .
                                                Html::{$field["fieldtype"]}($field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . ']', isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : "", $classarray) . '
			                                    </div>
			                                </div>';
                                        } else {
                                            echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . ']', isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : "", $classarray);
                                        }
                                    } else if ($field['uitype'] == 16) {
                                        $classarray['class'] = $classarray['class'] . ' timepicker';
                                        // print_r($classarray);die;
                                        $cnt_rows = $cnt_rows;
                                        include 'uitype/duration.php';
                                        //echo $this->render('/uitype/',['classarray'=>$classarray,'field'=>$field,'fieldoptions'=>$field["fieldoptions"]]);  // Include the HTML structure defined earlier
                        
                                        // 		echo Html::input('time', $field["tablename"] . '[' . $field["fieldname"] . ']', '', [
//     'step' => 60,  // Restrict input to full minutes (no seconds)
//     'class' => $classarray,
//     'id' => $field['fieldname'],
// ]);
                                    } else if ($field['uitype'] == 30) {
                                        $classarray['class'] = $classarray['class'] . ' timepicker';
                                        // print_r($classarray);die;
                                        $cnt_rows = $cnt_rows;
                                        include 'uitype/time.php';
                                        //echo $this->render('/uitype/',['classarray'=>$classarray,'field'=>$field,'fieldoptions'=>$field["fieldoptions"]]);  // Include the HTML structure defined earlier
                        
                                        // 		echo Html::input('time', $field["tablename"] . '[' . $field["fieldname"] . ']', '', [
//     'step' => 60,  // Restrict input to full minutes (no seconds)
//     'class' => $classarray,
//     'id' => $field['fieldname'],
// ]);
                                    } else if ($field["uitype"] == 5 && $visible == 0) {
                                        $filepath = '';
                                        if (isset($MRecord->{$field["columnname"]})) {
                                            // echo $Record->{$field["columnname"]};die;
                                            $MRecords2 = \app\models\Attachments::find()
                                                ->where(['attachmentsid' => $MRecord->{$field["columnname"]}])
                                                ->one();
                                            //   print_r($MRecords);die;
                                            // echo $MRecords->name;die;
                                            $filepath = "<br><a href='" . $baseUrl . $ModuleName . "/download?fileid=" . $MRecord->{$field["columnname"]} . "'>" . $MRecords2->name . "</a>";
                                            // $filepath = $MRecords->name;
                                            $classarray['class'] = 'productinput  form-control temp-file';
                                            echo "<input type='hidden' name='" . $field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . "_hidden]' value='" . $MRecord->{$field["columnname"]} . "'>";
                                        }
                                        if(!empty($classarray['class']))
                                             $classarray['class'] .= ' temp-file';
                                        else
                                            $classarray['class'] = 'temp-file';
                                        if ($readonly == 0)
                                            echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . ']', isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : "", $classarray);
                                        else
                                            echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . ']', isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : "", array_merge($classarray, ['disabled' => true]));
                                        //for preview
                                        echo '<div class="file-preview mt-2"></div>';
                                        //end for preview
                                        
                                        if (!empty($filepath)) {
                                            echo "<br><div class='upd-file'>Uploaded file: " . $filepath . "</div>";
                                        }

                                    } elseif ($field["uitype"] == 2 && $visible == 0) //hiden
                                    {
                                        if (!empty($sourcemodule) && !empty($sourceid)) {
                                            $sourcemodule = htmlspecialchars($_REQUEST['sourcemodule']);
                                            $sourceid = htmlspecialchars($_REQUEST['sourceid']);
                                            // if($field["fieldname"] == 'related_to')
                                            if ($field["fieldname"] == 'related_to')
                                                $val = $sourcemodule;
                                            else
                                                $val = $sourceid;
                                            echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . ']', $val, $classarray);
                                        } else
                                            echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . ']', isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : "", $classarray);
                                    } elseif ($field["uitype"] == 4 && $visible == 0) //text area
                                    {
                                        echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . ']', isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : "", $classarray);
                                    } elseif ($field["uitype"] == 8 && $visible == 0 && $field["fieldid"] != 19) //simgle drop down
                                    {
                                        if ($field["columnname"] == "ownerid") {

                                            echo Html::{$field["fieldtype"]}(
                                                $field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . ']',
                                                isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : Yii::$app->user->id,
                                                $field["fieldoptions"],
                                                ['class' => 'singleselect productinput form-control ' . $typeofdata . ' ' . $read, 'id' => $field["fieldname"] . "_" . $cnt_rows, "prompt" => "Select " . $field["fieldlabel"], 'data-pristine-required' => 'true', 'data-pristine-required-message' => $field["fieldlabel"] . ' is required ',]
                                            );
                                        } else {
                                            // print_r($field["fieldoptions"]);
                                            // if ($field["fieldid"] == "2753" && $subCategory != '41') {
                                            //     // Do not render Bin Number field if sub_category is not '41'
                                            //     return;
                                            // }
                                            if ($mandatory == 1) {

                                                echo Html::{$field["fieldtype"]}(
                                                    $field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . ']',
                                                    isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : '',
                                                    $field["fieldoptions"],
                                                    ['class' => 'singleselect productinput form-control ' . $typeofdata . ' ' . $read, 'id' => $field["fieldname"] . "_" . $cnt_rows, "prompt" => "Select " . $field["fieldlabel"], 'data-pristine-required' => 'true', 'data-pristine-required-message' => $field["fieldlabel"] . ' is required ']
                                                );
                                            } //
                                            // code adeed by ptpatel on date 17-04-25 to show disable  dd if it is readonly
                                            else if ($readonly == 1) {
                                                echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . ']', isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : '', $field["fieldoptions"], ['class' => ' productinput form-control ' . $typeofdata . ' ' . $read, 'id' => $field["fieldname"] . "_" . $cnt_rows, "prompt" => "Select " . $field["fieldlabel"]]);
                                            }
                                            //end code added by ptpatel on date 17-04-25
                                            else {
                                                echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . ']', isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : '', $field["fieldoptions"], ['class' => 'singleselect productinput form-control ' . $typeofdata . ' ' . $read, 'id' => $field["fieldname"] . "_" . $cnt_rows, "prompt" => "Select " . $field["fieldlabel"]]);
                                            }
                                        }
                                    } elseif ($field["uitype"] == 9 && $visible == 0) //checkboxlist
                                    {
                                        // print_r($field["fieldoptions"]);
                        
                                        if ($mandatory == 1) {

                                            echo Html::{$field["fieldtype"]}(
                                                $field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . ']',
                                                isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : '',
                                                $field["fieldoptions"],
                                                ['class' => 'form-check-input ' . $typeofdata . ' ' . $read, 'id' => $field["fieldname"] . "_" . $cnt_rows, "prompt" => "Select " . $field["fieldlabel"], 'data-pristine-required' => 'true', 'data-pristine-required-message' => $field["fieldlabel"] . ' is required ',]
                                            );
                                        } else {
                                            echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . ']', isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : '', $field["fieldoptions"], ['class' => 'form-control ' . $typeofdata . ' ' . $read, 'id' => $field["fieldname"] . "_" . $cnt_rows, "prompt" => "Select " . $field["fieldlabel"]]);
                                        }

                                    } elseif ($field["uitype"] == 10 && $visible == 0) //radio list
                                    {
                                        // print_r($field["fieldoptions"]);
                        
                                        if ($mandatory == 1) {

                                            echo Html::{$field["fieldtype"]}(
                                                $field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . ']',
                                                isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : '',
                                                $field["fieldoptions"],
                                                ['class' => 'form-control ' . $typeofdata . ' ' . $read, 'id' => $field["fieldname"] . "_" . $cnt_rows, "prompt" => "Select " . $field["fieldlabel"], 'data-pristine-required' => 'true', 'data-pristine-required-message' => $field["fieldlabel"] . ' is required ']
                                            );
                                        } else {
                                            echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . ']', isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : '', $field["fieldoptions"], ['class' => 'form-control ' . $typeofdata . ' ' . $read, 'id' => $field["fieldname"] . "_" . $cnt_rows, "prompt" => "Select " . $field["fieldlabel"]]);
                                        }

                                    } elseif ($field["uitype"] == 22 && $visible == 0) //multi drop down
                                    {
                                        // Output the multi-list (above HTML)
                                        echo $this->render('multi_list', ['classarray' => $classarray, 'field' => $field, 'fieldoptions' => $field["fieldoptions"]]);  // Include the HTML structure defined earlier
                                        // print_r($field["fieldoptions"]);
                        
                                        // if ($mandatory == 1) {
                        
                                        // 	echo Html::{$field["fieldtype"]}(
                                        // 		$field["tablename"] . '[' . $field["fieldname"] . ']',
                                        // 		isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : '',
                                        // 		$field["fieldoptions"],
                                        // 		['multiple' => true,  // Allow multiple selection,
                                        // 		'class' => 'form-control '.$typeofdata,'id' => $field["fieldname"]."_".$cnt_rows, "prompt" => "Select " . $field["fieldlabel"], 'data-pristine-required' => 'true', 'data-pristine-required-message' => $field["fieldlabel"] . ' is required ', 'disabled' => $readonly ? true : false]
                                        // 	);
                                        // } else {
                                        // 	echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $field["fieldname"] . ']', isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : '', $field["fieldoptions"], [
                                        // 		'multiple' => true,  // Allow multiple selection,
                                        // 		'disabled' => $readonly ? true : false,
                                        // 		 'class' => 'form-control '.$typeofdata ,
                                        // 		 'id' => $field["fieldname"]."_".$cnt_rows,
                                        // 		  "prompt" => "Select " . $field["fieldlabel"]]);
                                        // }
                        
                                    } elseif ($field["uitype"] == 25 && $visible == 0) //simgle drop down
                                    {
                                        // echo $sourcemodule;die;
                                        //print_r($field["fieldoptions"]);
                                        if ($field["fieldname"] == 'related_to') {
                                            if (!empty($sourcemodule)) {
                                                $val = $sourcemodule;
                                                $readonly = 1;
                                                $read = $readonly ? 'readonly-dd' : '';

                                            } else
                                                $val = '';
                                        } else
                                            $val = '';


                                        $options = $field["fieldoptions"];
                                        unset($options["prompt"]);

                                        if ($mandatory == 1) {
                                            echo Html::{$field["fieldtype"]}(
                                                $field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . ']',
                                                isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : $val,
                                                $options,
                                                [
                                                    'class' => 'form-control ' . $typeofdata . " " . $read,
                                                    'id' => $field["fieldname"] . "_" . $cnt_rows,
                                                    'data-pristine-required' => 'true',
                                                    'data-pristine-required-message' => $field["fieldlabel"] . ' is required ',
                                                    // 'disabled' => $readonly ? true : false
                                                ]
                                            );
                                        } else {
                                            echo Html::{$field["fieldtype"]}(
                                                $field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . ']',
                                                isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : $val,
                                                $options,
                                                [
                                                    // 'disabled' => $readonly ? true : false, 
                                                    'class' => 'form-control ' . $typeofdata . " " . $read,
                                                    'id' => $field["fieldname"] . "_" . $cnt_rows,
                                                ]
                                            );
                                        }

                                    } elseif ($field["uitype"] == 23 && $visible == 0) //for numeric
                                    {

                                        // <!-- Label for the text input -->
                        
                                        echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . ']', isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : "", $classarray);
                                    } elseif ($field["uitype"] == 24 && $visible == 0) //multiple drop down
                                    {
                                        // print_r($field["fieldoptions"]);die;
                                        if ($mandatory == 1) {

                                            echo Html::{$field["fieldtype"]}(
                                                $field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . ']',
                                                isset($MRecord->{$field["columnname"]}) ? explode(',', $MRecord->{$field["columnname"]} ?? '') : '',
                                                $field["fieldoptions"],
                                                [
                                                    'id' => $field["fieldname"] . "_" . $cnt_rows,
                                                    'class' => 'form-control ' . $typeofdata . " " . $read,
                                                    "prompt" => "Select " . $field["fieldlabel"],
                                                    'data-pristine-required' => 'true',
                                                    'data-pristine-required-message' => $field["fieldlabel"] . ' is required ',
                                                    'multiple' => true,
                                                ]
                                            );
                                        } else {
                                            echo Html::{$field["fieldtype"]}(
                                                $field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . ']',
                                                isset($MRecord->{$field["columnname"]}) ? explode(',', $MRecord->{$field["columnname"]} ?? '') : '',
                                                $field["fieldoptions"],
                                                [
                                                    'id' => $field["fieldname"] . "_" . $cnt_rows,
                                                    // 'disabled' => $readonly ? true : false,
                                                    'class' => 'form-control ' . $typeofdata . " " . $read,
                                                    "prompt" => "Select " . $field["fieldlabel"],
                                                    'multiple' => true,

                                                ]
                                            );
                                        }
                                    } elseif ($field["uitype"] == 13 && $visible == 0) {
                                        $cnt_rows = $cnt_rows;
                                        include "uitype/Datetime.php";
                                        // echo Html::input($field["fieldtype"], $field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . ']', isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : "", $classarray);
                                    } elseif ($field["uitype"] == 17 && $visible == 0) {
                                        $cnt_rows = $cnt_rows;
                                        include "uitype/Date.php";
                                        // echo Html::input($field["fieldtype"], $field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . ']', isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : "", $classarray);
                                    } elseif ($field["uitype"] == 12 && $visible == 0) { ?>
                                                    <div>
                                                <?php
                                                $relatedmod_tabid = $field["related_mod"];
                                                $fieldname = $field["columnname"] . "_" . $cnt_rows;
                                                $fieldname1 = $fieldname . "1";
                                                $fieldname2 = $fieldname . "2";
                                                $fieldname3 = $fieldname . "3";
                                                $model1 = new Reference($TableName, $FieldId);
                                                $relatedmodulename = $model1->getRelatedNoduleName($field["fieldid"]);
                                                $getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldName($field["fieldid"]);

                                                $ref_hid_value = isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : '';

                                                if (isset($MRecord->{$field["columnname"]}) && $MRecord->{$field["columnname"]} != '')
                                                    $ref_disp_value = $model1->getRefEntityValue($field["fieldid"], $ref_hid_value);
                                                else
                                                    $ref_disp_value = '';


                                                $relatedmod = '';// $field["relatedmodulename"];
                                                $getRelatedDField = '';// $field["getRelatedDisplayFieldName"];
                                
                                                ?>



                                                        <div class="vendor-input-wrapper">
                                                    <?php
                                                    // Check if 'readonly' is not equal to 'readonly' (simplified condition)
                                                    if (!$classarray['readonly']): ?>
                                                                <!-- Cross Icon on the Left -->
                                                                <svg class="icon-left" fill="#2F80ED" xmlns="http://www.w3.org/2000/svg"
                                                                    viewBox="0 0 24 24" width="15" height="15" role="button" tabindex="0"
                                                                    id="removeTextValue" data-fieldname1="<?= $fieldname1 ?>" data-fieldname="<?= $fieldname ?>"
                                                                    aria-label="Remove vendor">
                                                                    <path
                                                                        d="M4.7070312 3.2929688 L3.2929688 4.7070312 L10.585938 12 L3.2929688 19.292969 L4.7070312 20.707031 L12 13.414062 L19.292969 20.707031 L20.707031 19.292969 L13.414062 12 L20.707031 4.7070312 L19.292969 3.2929688 L12 10.585938 L4.7070312 3.2929688 Z">
                                                                    </path>
                                                                </svg>
                                                <?php
                                                    endif;
                                                    ?>

                                                            <input class="effect" style="flex-grow:1;" type="hidden" id="<?php echo $fieldname1; ?>"
                                                                name="<?php echo $field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . ']' ?>"
                                                                value="<?php echo $ref_hid_value; ?>" readonly='readonly'>
                                                <?php echo Html::{$field["fieldtype"]}('', $ref_disp_value, $classarray); ?>

                                                    <?php
                                                    // Check if 'readonly' is not equal to 'readonly' (simplified condition)
                                                    if (!$classarray['readonly']): ?>
                                                                <!-- Search Icon on the Right 1-->
                                                                <svg class="icon-right search-icon plus-icon" width="15" height="15" viewBox="0 0 24 25"
                                                                    fill="none" xmlns="http://www.w3.org/2000/svg" data-toggle="modal"
                                                                    data-target="#myModal22" role="button" aria-hidden="true" tabindex="0" 
                                                                    id="showCustomer1"
                                                                    data-fieldname1="<?= $fieldname1 ?>"
                                                                    data-fieldname="<?= $fieldname ?>"
                                                                    data-display="<?= $getRelatedDisplayFieldName ?>"
                                                                    data-module="<?= $relatedmodulename ?>"
                                                                    data-fieldid="<?= $field['fieldid'] ?>"
                                                                    data-val6=""
                                                                    data-val7=""
                                                                    data-val8=""
                                                                    data-sourcemodule="<?= $sourcemodule ?>"
                                                                    data-sourceid="<?= $sourceid ?>"
                                                                    aria-label="Search vendor 1">
                                                                    <path
                                                                        d="M21 21.5L16.514 17.006L21 21.5ZM19 11C19 13.2543 18.1045 15.4163 16.5104 17.0104C14.9163 18.6045 12.7543 19.5 10.5 19.5C8.24566 19.5 6.08365 18.6045 4.48959 17.0104C2.89553 15.4163 2 13.2543 2 11C2 8.74566 2.89553 6.58365 4.48959 4.98959C6.08365 3.39553 8.24566 2.5 10.5 2.5C12.7543 2.5 14.9163 3.39553 16.5104 4.98959C18.1045 6.58365 19 8.74566 19 11V11Z"
                                                                        stroke="#2F80ED" stroke-width="2" stroke-linecap="round"></path>
                                                                </svg>

                                                                <!-- Plus Icon on the Right -->
                                                                <!-- <svg class="icon-right plus-icon" fill="#2F80ED" xmlns="http://www.w3.org/2000/svg"
                                                                    viewBox="0 0 24 24" width="15" height="15" role="button" tabindex="0" onclick="addVendor('<?// $fieldname1 ?>','<?// $fieldname ?>','<?// $getRelatedDisplayFieldName; ?>','<?// $relatedmodulename; ?>',<?// $block->blockid;
                                                                            ; ?>)" aria-label="Add vendor">
                                                                    <path d="M12 5v7H5v2h7v7h2v-7h7v-2h-7V5z"></path>
                                                                </svg> -->
                                                    <?php
                                                    endif;
                                                    ?>
                                                        </div>




                                                    </div>
                                    <?php } elseif ($field["uitype"] == 27 && $visible == 0) { ?>
                                                    <d>
                                                <?php
                                                $relatedmod_tabid = $field["related_mod"];
                                                $fieldname = $field["columnname"] . "_" . $cnt_rows;
                                                $fieldname1 = $fieldname . "1";
                                                $fieldname2 = $fieldname . "2";
                                                $fieldname3 = $fieldname . "3";
                                                $model1 = new Reference($TableName, $FieldId);
                                                $relatedmodulename = $model1->getRelatedNoduleName($field["fieldid"]);
                                                $getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldName($field["fieldid"]);

                                                $ref_hid_value = isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : '';

                                                if (isset($MRecord->{$field["columnname"]}) && $MRecord->{$field["columnname"]} != '')
                                                    $ref_disp_value = $model1->getRefEntityValue($field["fieldid"], $ref_hid_value);
                                                else
                                                    $ref_disp_value = '';


                                                $relatedmod = '';// $field["relatedmodulename"];
                                                $getRelatedDField = '';// $field["getRelatedDisplayFieldName"];
                                
                                                ?>
                                                        <input class="effect" style="flex-grow:1;" type="hidden" id="<?php echo $fieldname1; ?>"
                                                            name="<?php echo $field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . ']' ?>"
                                                            value="<?php echo $ref_hid_value; ?>" readonly='readonly'>
                                            <?php echo Html::{$field["fieldtype"]}('', $ref_disp_value, $classarray); ?>



                                        <?php } elseif ($field["uitype"] == 28 && $visible == 0) { //conditional reference ?>
                                                        <div>
                                                    <?php
                                                    $relatedmod_tabid = $field["related_mod"];
                                                    $fieldname = $field["columnname"] . "_" . $cnt_rows;
                                                    $fieldname1 = $fieldname . "1";
                                                    $fieldname2 = $fieldname . "2";
                                                    $fieldname3 = $fieldname . "3";
                                                    $model1 = new Reference($TableName, $FieldId);
                                                    $relatedmodulename = $model1->getRelatedNoduleName($field["fieldid"]);
                                                    $getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldName($field["fieldid"]);
                                                    $getRelatedConditionFieldName = $model1->getRelatedConditionFieldName($field["fieldid"]);

                                                    $ref_hid_value = isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : '';

                                                    if (isset($MRecord->{$field["columnname"]}) && $MRecord->{$field["columnname"]} != '')
                                                        $ref_disp_value = $model1->getRefEntityValue($field["fieldid"], $ref_hid_value);
                                                    else
                                                        $ref_disp_value = '';


                                                    $relatedmod = '';// $field["relatedmodulename"];
                                                    $getRelatedDField = '';// $field["getRelatedDisplayFieldName"];
                                    
                                                    ?>



                                                            <div class="vendor-input-wrapper">
                                                        <?php
                                                        // Check if 'readonly' is not equal to 'readonly' (simplified condition)
                                                        if (!$classarray['readonly']): ?>
                                                                    <!-- Cross Icon on the Left -->
                                                                    <svg class="icon-left" fill="#2F80ED" xmlns="http://www.w3.org/2000/svg"
                                                                        viewBox="0 0 24 24" width="15" height="15" role="button" tabindex="0"
                                                                        id="removeTextValue" data-fieldname1="<?= $fieldname1 ?>" data-fieldname="<?= $fieldname ?>"
                                                                        aria-label="Remove vendor">
                                                                        <path
                                                                            d="M4.7070312 3.2929688 L3.2929688 4.7070312 L10.585938 12 L3.2929688 19.292969 L4.7070312 20.707031 L12 13.414062 L19.292969 20.707031 L20.707031 19.292969 L13.414062 12 L20.707031 4.7070312 L19.292969 3.2929688 L12 10.585938 L4.7070312 3.2929688 Z">
                                                                        </path>
                                                                    </svg>
                                                        <?php
                                                        endif;
                                                        ?>

                                                                <input class="effect" style="flex-grow:1;" type="hidden"
                                                                    id="<?php echo $fieldname1; ?>"
                                                                    name="<?php echo $field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . ']' ?>"
                                                                    value="<?php echo $ref_hid_value; ?>" readonly='readonly'>
                                                    <?php echo Html::{$field["fieldtype"]}('', $ref_disp_value, $classarray); ?>

                                                        <?php
                                                        // Check if 'readonly' is not equal to 'readonly' (simplified condition)
                                                        if (!$classarray['readonly']): ?>
                                                                    <!-- Search Icon on the Right -->
                                                                    <svg class="icon-right search-icon plus-icon" width="15" height="15" viewBox="0 0 24 25"
                                                                        fill="none" xmlns="http://www.w3.org/2000/svg" data-toggle="modal"
                                                                        data-target="#myModal22" role="button" aria-hidden="true" tabindex="0"
                                                                        id="showReferenceConditional" 
                                                                        data-fieldname1="<?= $fieldname1 ?>"
                                                                        data-fieldname="<?= $fieldname ?>"
                                                                        data-display="<?= $getRelatedDisplayFieldName; ?>"
                                                                        data-module="<?= $relatedmodulename; ?>"
                                                                        data-fieldid="<?= $field['fieldid']; ?>"
                                                                        data-dep1="<?= $field["dependent_field"]; ?>1"
                                                                        data-dep="<?= $field["dependent_field"]; ?>"
                                                                        data-cond="<?= $getRelatedConditionFieldName; ?>"
                                                                        aria-label="Search vendor">

                                                                        showReferenceConditional('bill_to_location_21','bill_to_location_2','vendor_loc_name','vendorlocations',197,'vendor_account_name1','vendor_account_name','vendor_account','55')
                                                                        <path
                                                                            d="M21 21.5L16.514 17.006L21 21.5ZM19 11C19 13.2543 18.1045 15.4163 16.5104 17.0104C14.9163 18.6045 12.7543 19.5 10.5 19.5C8.24566 19.5 6.08365 18.6045 4.48959 17.0104C2.89553 15.4163 2 13.2543 2 11C2 8.74566 2.89553 6.58365 4.48959 4.98959C6.08365 3.39553 8.24566 2.5 10.5 2.5C12.7543 2.5 14.9163 3.39553 16.5104 4.98959C18.1045 6.58365 19 8.74566 19 11V11Z"
                                                                            stroke="#2F80ED" stroke-width="2" stroke-linecap="round"></path>
                                                                    </svg>

                                                                    <!-- Plus Icon on the Right -->
                                                                    <!-- <svg class="icon-right plus-icon" fill="#2F80ED" xmlns="http://www.w3.org/2000/svg"
                                                                        viewBox="0 0 24 24" width="15" height="15" role="button" tabindex="0" onclick="addVendor('<?// $fieldname1 ?>','<?// $fieldname ?>','<?// $getRelatedDisplayFieldName; ?>','<?// $relatedmodulename; ?>',<?// $block->blockid;
                                                                                ; ?>)" aria-label="Add vendor">
                                                                        <path d="M12 5v7H5v2h7v7h2v-7h7v-2h-7V5z"></path>
                                                                    </svg> -->
                                                        <?php
                                                        endif;
                                                        ?>
                                                            </div>




                                                        </div>
                                        <?php } elseif ($field["uitype"] == 26 && $visible == 0) {
                                        if ($field["fieldname"] == 'related_to_id') {
                                            $readonly = 1;
                                            $val = $sourceid;
                                            // print_r($classarray);
                                            if (!empty($sourceid)) {
                                                if (isset($classarray['readonly']))
                                                    $classarray['readonly'] = 'readonly';
                                                else
                                                    array_push($classarray['readonly'], 'readonly');
                                            }
                                        } else
                                            $val = '';
                                        ?>

                                                        <div>
                                                    <?php
                                                    $fieldname = $field["columnname"] . "_" . $cnt_rows;
                                                    $fieldname1 = $fieldname . "1";
                                                    $fieldname2 = $fieldname . "2";
                                                    $fieldname3 = $fieldname . "3";
                                                    $relatedmod_arr = explode(",", $field["related_mod"]);
                                                    $model1 = new Reference($TableName, $FieldId);
                                                    $i = 0;
                                                    $onclick1 = '';
                                                    foreach ($relatedmod_arr as $key => $value) {
                                                        # code...
                                                        if (!empty($value)) {
                                                            $relatedmod_tabid = $value;
                                                            if ((!empty($MRecord['related_to']) && $value == $MRecord['related_to']) || $i == 0) {
                                                                $relatedmodulename = $model1->getRelatedNoduleNameBytab($field["fieldid"], $relatedmod_tabid);
                                                                $getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldNameBytab($field["fieldid"], $relatedmod_tabid);
                                                                // code added by ptpatel to resolve issue of server
                                                                $fieldname1Esc = htmlspecialchars($fieldname1, ENT_QUOTES);
                                                                $fieldnameEsc = htmlspecialchars($fieldname, ENT_QUOTES);
                                                                $displayFieldEsc = htmlspecialchars($getRelatedDisplayFieldName, ENT_QUOTES);
                                                                $moduleNameEsc = htmlspecialchars($relatedmodulename, ENT_QUOTES);
                                                                $fieldidInt = (int) $field['fieldid'];
                                                                
                                                                $onclick1 = "showCustomer1('" . $fieldname1 . "','" . $fieldname . "', '" . htmlspecialchars($getRelatedDisplayFieldName) . "', '" . htmlspecialchars($relatedmodulename) . "', '" . (int) $field['fieldid'] . "')";
                                                                echo '<input type="hidden" class="relatedsearch"
                                                                    id="uni_related' . $value . '" 
                                                                    data-fname1="' . $fieldname1Esc . '" 
                                                                    data-fname="' . $fieldnameEsc . '" 
                                                                    data-display="' . $displayFieldEsc . '" 
                                                                    data-module="' . $moduleNameEsc . '" 
                                                                    data-fieldid="' . $fieldidInt . '"
                                                                    value="' . $onclick1 . '">';
                                                                    // echo '<input type="hidden" id="uni_related' . $value . '" value="' . $onclick1 . '">';

                                                            } else {
                                                                $relatedmodulenametemp = $model1->getRelatedNoduleNameBytab($field["fieldid"], $relatedmod_tabid);
                                                                $getRelatedDisplayFieldNametemp = $model1->getRelatedDisplayFieldNameBytab($field["fieldid"], $relatedmod_tabid);
                                                                
                                                                // Escape each value for safety in HTML
                                                                    $fieldname1Esc = htmlspecialchars($fieldname1, ENT_QUOTES);
                                                                    $fieldnameEsc = htmlspecialchars($fieldname, ENT_QUOTES);
                                                                    $displayFieldEsc = htmlspecialchars($getRelatedDisplayFieldNametemp, ENT_QUOTES);
                                                                    $moduleNameEsc = htmlspecialchars($relatedmodulenametemp, ENT_QUOTES);
                                                                    $fieldidInt = (int) $field['fieldid'];
                                                                    $onclick = "showCustomer1('" . $fieldname1 . "','" . $fieldname . "', '" . htmlspecialchars($getRelatedDisplayFieldNametemp) . "', '" . htmlspecialchars($relatedmodulenametemp) . "', '" . (int) $field['fieldid'] . "')";
                                                                
                                                                    // Output hidden input with separate data attributes
                                                                    echo '<input type="hidden" class="relatedsearch"
                                                                        id="uni_related' . htmlspecialchars($value, ENT_QUOTES) . '" 
                                                                        data-fname1="' . $fieldname1Esc . '" 
                                                                        data-fname="' . $fieldnameEsc . '" 
                                                                        data-display="' . $displayFieldEsc . '" 
                                                                        data-module="' . $moduleNameEsc . '" 
                                                                        data-fieldid="' . $fieldidInt . '"
                                                                        value="' . $onclick . '">';
                                                                        
                                                                // echo '<input type="hidden" id="uni_related' . $value . '" value="' . $onclick . '">';


                                                            }
                                                            $i++;
                                                        }
                                                    }

                                                    $ref_hid_value = isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : $val;
                                                    // echo "select targettable,entityidfield,fieldname from entityname where fieldid=".$field["fieldid"];die;
                                    

                                                    if ($ref_hid_value) {
                                                        $ref_disp_value = $model1->getRefEntityValue($field["fieldid"], $ref_hid_value);
                                                    } else
                                                        $ref_disp_value = '';
                                                    ?>



                                                            <div class="vendor-input-wrapper">
                                                        <?php
                                                        // Check if 'readonly' is not equal to 'readonly' (simplified condition)
                                                        if (!$classarray['readonly']): ?>
                                                                    <!-- Cross Icon on the Left -->
                                                                    <svg class="icon-left" fill="#2F80ED" xmlns="http://www.w3.org/2000/svg"
                                                                        viewBox="0 0 24 24" width="15" height="15" role="button" tabindex="0"
                                                                        id="removeTextValue" data-fieldname1="<?= $fieldname1 ?>" data-fieldname="<?= $fieldname ?>"
                                                                        aria-label="Remove vendor">
                                                                        <path
                                                                            d="M4.7070312 3.2929688 L3.2929688 4.7070312 L10.585938 12 L3.2929688 19.292969 L4.7070312 20.707031 L12 13.414062 L19.292969 20.707031 L20.707031 19.292969 L13.414062 12 L20.707031 4.7070312 L19.292969 3.2929688 L12 10.585938 L4.7070312 3.2929688 Z">
                                                                        </path>
                                                                    </svg>
                                                    <?php endif; ?>

                                                                <input class="effect" style="flex-grow:1;" type="hidden"
                                                                    id="<?php echo $fieldname1; ?>"
                                                                    name="<?php echo $field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . ']' ?>"
                                                                    value="<?php echo $ref_hid_value; ?>" readonly='readonly'>
                                                    <?php echo Html::{$field["fieldtype"]}('', $ref_disp_value, $classarray); ?>

                                                        <?php
                                                        // Check if 'readonly' is not equal to 'readonly' (simplified condition)
                                                        if (!$classarray['readonly']): ?>
                                                                    <!-- Search Icon on the Right -->
                                                                    <svg class="icon-right related-search-icon  search-icon" width="15" height="15"
                                                                        viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg"
                                                                        data-toggle="modal" data-target="#myModal22" role="button" aria-hidden="true"
                                                                        tabindex="0" data-onrefclick="<?= $onclick1 ?>" aria-label="Search vendor">
                                                                        <path
                                                                            d="M21 21.5L16.514 17.006L21 21.5ZM19 11C19 13.2543 18.1045 15.4163 16.5104 17.0104C14.9163 18.6045 12.7543 19.5 10.5 19.5C8.24566 19.5 6.08365 18.6045 4.48959 17.0104C2.89553 15.4163 2 13.2543 2 11C2 8.74566 2.89553 6.58365 4.48959 4.98959C6.08365 3.39553 8.24566 2.5 10.5 2.5C12.7543 2.5 14.9163 3.39553 16.5104 4.98959C18.1045 6.58365 19 8.74566 19 11V11Z"
                                                                            stroke="#2F80ED" stroke-width="2" stroke-linecap="round"></path>
                                                                    </svg>



                                                    <?php endif; ?>
                                                            </div>




                                                        </div>
                                        <?php } elseif ($field["uitype"] == 6 && $visible == 0) { //checkbox
                                        if (isset($MRecord->{$field["columnname"]})) {
                                            if ($MRecord->{$field["columnname"]} == 1)
                                                $checked = 1;
                                            else
                                                $checked = 0;
                                        } else
                                            $checked = '';
                                        if ($mandatory == 1) {
                                            echo Html::{$field["fieldtype"]}(
                                                $field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . ']', // Name attribute
                                                $checked ? true : false, // Checked state
                                                [
                                                    'disabled' => $readonly ? true : false,
                                                    "class" => "form-check-input " . $field['typeofdata'] . " " . $field["classname"] . " " . $read, // Optional custom CSS class
                                                    "label" => $field["fieldlabel"], // Label for the checkbox
                                                    'data-pristine-required' => 'true', // Custom attribute
                                                    'id' => $field["fieldname"] . "_" . $cnt_rows,
                                                    'data-pristine-required-message' => $field["fieldlabel"] . ' is required ' // Custom validation message
                                                ]
                                            );
                                        } else {

                                            echo Html::{$field["fieldtype"]}(
                                                $field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . ']', // Name attribute
                                                $checked ? true : false, // Checked state
                                                [
                                                    'disabled' => $readonly ? true : false,
                                                    "class" => "form-check-input " . $field['typeofdata'] . " " . $field["classname"] . " " . $read, // Optional custom CSS class
                                                    "label" => $field["fieldlabel"], // Label for the checkbox
                                                    'id' => $field["fieldname"] . "_" . $cnt_rows,

                                                ]
                                            );
                                        }
                                    } elseif ($field["uitype"] == 53) //hiden
                                    {
                                        echo Html::hiddenInput($field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . ']', isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : Yii::$app->user->id, [

                                            'id' => $field["fieldname"] . "_" . $cnt_rows,
                                        ]);
                                    } elseif ($field["uitype"] == 70) //hiden
                                    {
                                        echo Html::hiddenInput($field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . ']', isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : date("Y-m-d H:i:s"), [

                                            'id' => $field["fieldname"] . "_" . $cnt_rows,
                                        ]);
                                    } else if ($field["uitype"] == 11) //hiden
                                    {
                                        echo Html::hiddenInput($field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . ']', isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : "", $classarray);
                                    } else if ($visible == 0) {
                                        echo "work in progress for XX uitype -- " . $field["uitype"];
                                    }
                                    //}counter if
                        
                                    if ($field["uitype"] != 2 && $field["uitype"] != 11 && $field["fieldid"] != 19) {
                                        echo '<div class="help-block"></div>';

                                        $counter++;
                                    }

                                }
                                ?>
                                    </td>
                                    <?php if (!in_array($TabId, [69, 70,12,42,13,78,88])) { ?>
                                        <td><button class="remove-row-btn">X</button></td>
                                    <?php } ?>

                            </tr>
                            <?php
                            $cnt_rows++;
                        }
                        // }
                    } ?>


                </tbody>
            </table>

        </div>
        <div id="paginationBar" style="display:none;">
            <button id="firstPage">&laquo;</button>
            <button id="prevPage">&lsaquo;</button>
            <span id="pageNumbers"></span>
            <button id="nextPage">&rsaquo;</button>
            <button id="lastPage">&raquo;</button>

            <span id="recordInfo" style="margin-left:10px;"></span>
        </div>

    </div>
</div>
<?php
$canAddMore = true;
if ((int)$TabId === 14 && $MAX_RECORD_COUNT) {
    $canAddMore = ($cnt_multiple_product < $MAX_RECORD_COUNT);
}
//if ($canAddMore){ ?>
<div class="row">
    <div class="col-3 col-lg-6 test">
        <?php if (!in_array($TabId, [68, 69, 70])) { //68 -tagging,69-sticker removal,70-cleaning ?>
            <button class="btn btn-primary add-more-records" type="button" data-blockid="<?= $block->blockid; ?>" data-module="<?= $ModuleName; ?>"
                onclick="addRowBtn('<?= $block->blockid; ?>','<?= $ModuleName; ?>')">+ Add row</button>
                <script nonce="<?= Yii::$app->params['cspNonce'] ?>">
                    $(".add-more-records").click(function(){
                         var $cell = $(this);
                        var module = $cell.data("module");
                        var blockid = $cell.data("blockid");
                        addRowBtn(blockid,blockid);
                    });
                </script>
        <?php }
        // else {
        //     $dependent_tbl = '';
        //     if($TabId == 68)
        //         $dependent_tbl = 'segregation_detail';
        ?>
        <!-- <button class="btn btn-primary add-more-records" type="button"
            onclick="addAutofilledRowBtn('<?php //echo $block->blockid; ?>','<?php //echo $ModuleName; ?>','<?php //echo Yii::$app->request->get('itemid'); ?>','<?php //echo $dependent_tbl; ?>')">+ Add row</button> -->
        <?php //} ?>
    </div>
    <div class="col-12 col-lg-12 test detail_bulk_upload" style="display:none;">
        <span id="so-bulk-message" class="text-danger" ><strong>Note: If items are more than 100, then please use detail page for bulk import!!</strong></span>
    </div>
</div>
<?php //} ?>
<div class="row mb-2"></div>

<script>

</script>