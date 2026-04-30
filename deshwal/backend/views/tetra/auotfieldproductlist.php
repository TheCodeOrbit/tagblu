<?php

use app\models\Products;
use yii\helpers\Html;
use common\models\Fieldtype;
use backend\models\AccessCheck;
use common\models\Picklist;
use common\models\Multilist;
use app\models\Reference;
use backend\assets\AdminAsset;
$baseUrl = Yii::$app->HomeUrl;
AdminAsset::register($this);
// $this->title = Yii::t('app', $TabLabel . " Detail");
$counter = 0;
$relationName = $action_name === 'create' ? 'createfields' : 'editfields';
// print_r($Record $Record->subcategory);
if(isset($Record->sub_category))
{
    $subcategory = $Record->sub_category;
}
elseif(isset($Record->subcategory))
{
    $subcategory = $Record->subcategory;
}
$category = $Record->category ?? $Record->category ?? '';

$laptop_id = (new \yii\db\Query())
    ->select(['sub_catagory_id'])
    ->from('prod_sub_catagory')
    ->where(['LOWER(TRIM(sub_catagory_value))' => 'laptop'])
    ->scalar();
$subcategories=['mobile','mouse','headphones','laptopbag','keyboard','mobilecharger','laptopcharger','laptopbattery','datacard'];
$serialnonotrequired = (new \yii\db\Query())
    ->select(['sub_catagory_id'])
    ->from('prod_sub_catagory')
    ->where(['IN', new \yii\db\Expression("REPLACE(LOWER(TRIM(sub_catagory_value)), ' ', '')"), $subcategories])
    ->column(); //  get all IDs as array

$categories=['cable&wires'];
$serialnonotrequired_in_categories = (new \yii\db\Query())
    ->select(['prod_category_id'])
    ->from('prod_category')
    ->where([
        'IN',
        new \yii\db\Expression("REPLACE(LOWER(TRIM(prod_category_value)), ' ', '')"),
        $categories
    ])
    ->column();
?>
    <tr id="<?= $cnt_rows;?>" class="product-row">
       <?php
        foreach ($block->createfields as  $field) {
            // echo "<pre>";print_r($field);
            $fieldid = $field->fieldid;
            if ($hasadminpower == 1) {
                $visible = 0;
                $readonly = 0;
            } else {
                //now check if this field is allowed to edit ,readonly etc
                $model = new AccessCheck();
                $permission = $model->fieldacces($uid, $fieldid);
                //print_r($permission);die;
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
                    //print_r($Record['vertical_manager']);die;
                    if ($ActionName == "Create") {
                        //limit  fieldoptons
                        // Keys to remove
                        $keysToRemove = [3, 13, 5];
                        $arraykey = $field["fieldoptions"];
                        // Remove elements
                        $field["fieldoptions"] = array_diff_key($field["fieldoptions"], array_flip($keysToRemove));

                        // print_r($field["fieldoptions"]);die;
                    } else if ($ActionName == "Edit" && $Record["vertical_manager"] == Yii::$app->user->id) {
                        //limit  fieldoptons
                        // Keys to remove
                        $keysToRemove = [1, 2, 4, 6, 7, 8, 9, 10, 11, 12];
                        $arraykey = $field["fieldoptions"];
                        // Remove elements
                        $field["fieldoptions"] = array_diff_key($field["fieldoptions"], array_flip($keysToRemove));

                        // print_r($field["fieldoptions"]);die;
        
                    }
                }
                //code added by ptpatel on date 25-04-25
                if(($field['tabid'] == 69 ||$field['tabid'] == 68 || $field['tabid'] == 70) && $field["fieldname"] == "status"){
                    if($field['tabid'] == 69)
                        $keysToRemove = [1,2,3];
                    else if($field['tabid'] == 70)
                        $keysToRemove = [1,2,3,4];
                    else if($field['tabid'] == 68)
                        $keysToRemove = [1,2];
                    $arraykey = $field["fieldoptions"];
                    // Remove elements
                    $field["fieldoptions"] = array_diff_key($field["fieldoptions"], array_flip($keysToRemove));

                }
                //end code added by ptpatel on date 25-04-25
            } else if ($field["uitype"] == 22 && $visible == 0) {
                $PickList = new Multilist;
                $PickList->fieldid = $field["fieldid"];
                // $Column->blocks[$BlockKey]->fields[$FieldKey]->fieldoptions=$PickList->getMultiListOption($table_name);
        

                $field["fieldoptions"] = $PickList->getMultiListOption($ModuleName);
            }
            if($Record->inventory_id != "")
                {   
                        echo '<input type="hidden" id="inventory_id" name="'.$field["tablename"].'['.$cnt_rows.'][inventory_id]" value="'.$Record->inventory_id.'" />';
                }
            //print_r($FieldTypeRecord);die;
            if ($field["uitype"] != 2 && $field["fieldid"] != 19 && $field["uitype"] != 11 && $field["uitype"] != 70 && $field["uitype"] != 53 && $visible == 0) {
                //hide this because it is creating issue in layout of td tr
                $clss = '';
                // if ($field["mandatory"] == 1)
                //     $clss = "required-field";
                // else
                //     $clss = 'not-required-field';
                //close/open divs

                // added on 21 jan 2026

                if ($counter == 0) {

                    echo '<td class="wdinput form-group  ' . $clss . ' form-field-cst section-' . $field["columnname"] . ' col-pinned">';
                } 
                // else if ($field["fieldid"] == "2753" && $subcategory != '41') { }
                else if ($field["fieldname"] == "bin_number" && $subcategory != $laptop_id) { }
                else {
                    echo '<!--close td inner--></td><td class="wdinput form-group  ' . $clss . ' form-field-cst section-' . $field["columnname"] . ' col-pinned">';

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
                    'class' => $field["fieldname"].' productinput form-control ' . $popupclass . ' ' . $typeofdata,
                    'id' => $field["fieldname"]."_".$cnt_rows,
                    "fieldid" => $field["fieldid"],
                    "value" => isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "",
                    'maxlength' => $field["maximumlength"],
                    'data-pristine-required' => 'true',
                    'data-pristine-required-message' => $field["fieldlabel"] . ' is required ',
                    'readonly' => $readonly ? true : false

                );
                $mandatory = "<span class='red'> *</span>";
            } else {
                $classarray = array(
                    'class' => $field["fieldname"]. " ".$popupclass . ' productinput form-control ' . $readonly . ' ' . $typeofdata,
                    'readonly' => $readonly ? true : false,
                    'id' => $field["fieldname"]."_".$cnt_rows,
                    'maxlength' => $field["maximumlength"],
                    "value" => isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : ""
                );
                $mandatory = "";
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
                    // echo $Record['salutation'];die;
                    $selesalu = isset($Record['salutation']) ? $Record['salutation'] : "";
                    echo '<div class="form-group">
			                                    <div class="input-group mb-2">
			                                        <div class="input-group-prepend">
			                                            <div class="input-group-text" style="background: none;">
			                                            <Select style="background: none;
			  border: none;
			  padding: 2px;" name=' . $field["tablename"].'['.$cnt_rows.']' . '[salutation]">
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
                        Html::{$field["fieldtype"]}($field["tablename"] .'['.$cnt_rows.']' . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray) . '
			                                    </div>
			                                </div>';
                } else {
                    if(($field['tabid'] == 69 || $field['tabid'] == 70 ) && $field["columnname"] == "tag_number")
                    {
                        $classarray['class'] .= ' barcode-input';
                        echo Html::{$field["fieldtype"]}($field["tablename"] .'['.$cnt_rows.']' . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);
                    }
                    //this is for showing duplicte no validation
                    else if($field['tabid'] == 68) 
                    {
                        if(
                            ($field["fieldid"] == "2751" && in_array($subcategory, $serialnonotrequired)) || 
                            ($field["fieldid"] == "2751" && in_array($category, $serialnonotrequired_in_categories))
                        )
                        {
                            // if sbcategory is mobile serail number not mandatory
                            $classarray['class'] = str_replace('V~M', 'V~O', $classarray['class']);
                            echo "<input type='hidden' value='0' name='".$field["tablename"] .'['.$cnt_rows.']' . '[serialnotrequired]'."'>";
                            echo Html::{$field["fieldtype"]}($field["tablename"] .'['.$cnt_rows.']' . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);
                        }
                        else {
                        echo Html::{$field["fieldtype"]}($field["tablename"] .'['.$cnt_rows.']' . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);
                        }
                        if($field["fieldid"] == "2751")
                        {
                            echo '<div class="serial-help-block text-red"></div>';
                        }
                        else if($field["fieldid"] == "2752")
                        {
                            echo '<div class="tag-help-block text-red"></div>';
                        }
                    }
                    else
                        echo Html::{$field["fieldtype"]}($field["tablename"] .'['.$cnt_rows.']' . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);
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
            } else if ($field["uitype"] == 5 && $visible == 0) {
                if($readonly == 0)
                echo Html::{$field["fieldtype"]}($field["tablename"] .'['.$cnt_rows.']' . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);
                    else
                    echo Html::{$field["fieldtype"]}($field["tablename"] .'['.$cnt_rows.']' . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", array_merge($classarray, ['disabled' => true])
                );
            //for preview
            echo '<div class="file-preview mt-2"></div>';
            //end for preview
				

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
                    echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $field["fieldname"] . ']', $val, $classarray);
                } else
                    echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);
            } elseif ($field["uitype"] == 4 && $visible == 0) //text area
            {
                echo Html::{$field["fieldtype"]}($field["tablename"] .'['.$cnt_rows.']' . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);
            } elseif ($field["uitype"] == 8 && $visible == 0 && $field["fieldid"] != 19) //simgle drop down
            {
                ?>
                <script type="text/javascript" src="<?= $baseUrl; ?>thememain/js/select2.min.js"></script>
                <script type="text/javascript" src="<?= $baseUrl; ?>thememain/js/tetra/single-dd.js"></script>
                <link rel="stylesheet" href="<?= $baseUrl; ?>/thememain/css/select2.min.css">
                <link rel="stylesheet" href="<?= $baseUrl; ?>/thememain/css/product.css">
                
                <?php
                if ($field["columnname"] == "ownerid") {

                    echo Html::{$field["fieldtype"]}(
                        $field["tablename"] .'['.$cnt_rows.']' . '[' . $field["fieldname"] . ']',
                        isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : Yii::$app->user->id,
                        $field["fieldoptions"],
                        ['class' => 'singleselect productinput form-control ' . $typeofdata . ' ' . $read, 'id' => $field["fieldname"]."_".$cnt_rows, "prompt" => "Select " . $field["fieldlabel"], 'data-pristine-required' => 'true', 'data-pristine-required-message' => $field["fieldlabel"] . ' is required ',]
                    );
                }
                else if ($field["columnname"] == "status" && ($field["tabid"] == 69 || $field["tabid"] == 70)) {

                    echo Html::{$field["fieldtype"]}(
                        $field["tablename"] .'['.$cnt_rows.']' . '[' . $field["fieldname"] . ']',
                        // isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : Yii::$app->user->id,
                        $field["tabid"] == 69 ? 4 : 5,
                        $field["fieldoptions"],
                        ['class' => 'singleselect productinput form-control ' . $typeofdata . ' ' . $read, 'id' => $field["fieldname"]."_".$cnt_rows, "prompt" => "Select " . $field["fieldlabel"], 'data-pristine-required' => 'true', 'data-pristine-required-message' => $field["fieldlabel"] . ' is required ',]
                    );
                }
                //else if ($field["fieldid"] == "2753" && $subcategory != '41') { // if (2750)subcategory == laptop then show bin number
                else if ($field["fieldname"] == "bin_number" && $subcategory != $laptop_id) { // if (2750)subcategory == laptop then show bin number
                }                
                else {
                    // print_r($field["fieldoptions"]);
                    if ($mandatory == 1) {

                        echo Html::{$field["fieldtype"]}(
                            $field["tablename"] .'['.$cnt_rows.']' . '[' . $field["fieldname"] . ']',
                            isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '',
                            $field["fieldoptions"],
                            ['class' => 'singleselect prodsingleselect productinput form-control ' . $typeofdata . ' ' . $read, 'id' => $field["fieldname"]."_".$cnt_rows, "prompt" => "Select " . $field["fieldlabel"], 'data-pristine-required' => 'true', 'data-pristine-required-message' => $field["fieldlabel"] . ' is required ']
                        );
                    } 
                    // added by ptpatel on date 16-04-25
                    else if($readonly == 1) //  it  show readonly dd
                    {
                        echo Html::{$field["fieldtype"]}($field["tablename"] .'['.$cnt_rows.']' . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '', $field["fieldoptions"], ['class' => ' productinput form-control ' . $typeofdata . ' ' . $read, 'id' => $field["fieldname"]."_".$cnt_rows, "prompt" => "Select " . $field["fieldlabel"]]);
                    }
                    //end added by ptpatel on date 16-04-25
                   else {
                        
                        echo Html::{$field["fieldtype"]}($field["tablename"] .'['.$cnt_rows.']' . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '', $field["fieldoptions"], ['class' => 'singleselect productinput form-control ' . $typeofdata . ' ' . $read, 'id' => $field["fieldname"]."_".$cnt_rows, "prompt" => "Select " . $field["fieldlabel"]]);
                    }
                }
            } elseif ($field["uitype"] == 9 && $visible == 0) //checkboxlist
            {
                // print_r($field["fieldoptions"]);
        
                if ($mandatory == 1) {

                    echo Html::{$field["fieldtype"]}(
                        $field["tablename"] .'['.$cnt_rows.']' . '[' . $field["fieldname"] . ']',
                        isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '',
                        $field["fieldoptions"],
                        ['class' => 'form-check-input ' . $typeofdata . ' ' . $read, 'id' => $field["fieldname"]."_".$cnt_rows, "prompt" => "Select " . $field["fieldlabel"], 'data-pristine-required' => 'true', 'data-pristine-required-message' => $field["fieldlabel"] . ' is required ',]
                    );
                } else {
                    echo Html::{$field["fieldtype"]}($field["tablename"] .'['.$cnt_rows.']' . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '', $field["fieldoptions"], ['class' => 'form-control ' . $typeofdata . ' ' . $read, 'id' => $field["fieldname"]."_".$cnt_rows, "prompt" => "Select " . $field["fieldlabel"]]);
                }

            } elseif ($field["uitype"] == 10 && $visible == 0) //radio list
            {
                // print_r($field["fieldoptions"]);
        
                if ($mandatory == 1) {

                    echo Html::{$field["fieldtype"]}(
                        $field["tablename"] .'['.$cnt_rows.']' . '[' . $field["fieldname"] . ']',
                        isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '',
                        $field["fieldoptions"],
                        ['class' => 'form-control ' . $typeofdata . ' ' . $read, 'id' => $field["fieldname"]."_".$cnt_rows, "prompt" => "Select " . $field["fieldlabel"], 'data-pristine-required' => 'true', 'data-pristine-required-message' => $field["fieldlabel"] . ' is required ']
                    );
                } else {
                    echo Html::{$field["fieldtype"]}($field["tablename"] .'['.$cnt_rows.']' . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '', $field["fieldoptions"], ['class' => 'form-control ' . $typeofdata . ' ' . $read, 'id' => $field["fieldname"]."_".$cnt_rows, "prompt" => "Select " . $field["fieldlabel"]]);
                }

            } elseif ($field["uitype"] == 22 && $visible == 0) //multi drop down
            {
                // Output the multi-list (above HTML)
                echo $this->render('multi_list', ['classarray' => $classarray, 'field' => $field, 'fieldoptions' => $field["fieldoptions"]]);  // Include the HTML structure defined earlier
                // print_r($field["fieldoptions"]);
        
                // if ($mandatory == 1) {
        
                // 	echo Html::{$field["fieldtype"]}(
                // 		$field["tablename"] . '[' . $field["fieldname"] . ']',
                // 		isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '',
                // 		$field["fieldoptions"],
                // 		['multiple' => true,  // Allow multiple selection,
                // 		'class' => 'form-control '.$typeofdata,'id' => $field["fieldname"]."_".$cnt_rows, "prompt" => "Select " . $field["fieldlabel"], 'data-pristine-required' => 'true', 'data-pristine-required-message' => $field["fieldlabel"] . ' is required ', 'disabled' => $readonly ? true : false]
                // 	);
                // } else {
                // 	echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '', $field["fieldoptions"], [
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
                        $field["tablename"] .'['.$cnt_rows.']' . '[' . $field["fieldname"] . ']',
                        isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : $val,
                        $options,
                        [
                            'class' => 'form-control ' . $typeofdata . " " . $read,
                            'id' => $field["fieldname"]."_".$cnt_rows,
                            'data-pristine-required' => 'true',
                            'data-pristine-required-message' => $field["fieldlabel"] . ' is required ',
                            // 'disabled' => $readonly ? true : false
                        ]
                    );
                } else {
                    echo Html::{$field["fieldtype"]}(
                        $field["tablename"] .'['.$cnt_rows.']' . '[' . $field["fieldname"] . ']',
                        isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : $val,
                        $options,
                        [
                            // 'disabled' => $readonly ? true : false, 
                            'class' => 'form-control ' . $typeofdata . " " . $read,
                            'id' => $field["fieldname"]."_".$cnt_rows,
                        ]
                    );
                }

            } elseif ($field["uitype"] == 23 && $visible == 0) //for numeric
            {

                // <!-- Label for the text input -->
        
                echo Html::{$field["fieldtype"]}($field["tablename"] .'['.$cnt_rows.']' . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);
            } elseif ($field["uitype"] == 24 && $visible == 0) //multiple drop down
            {
                // print_r($field["fieldoptions"]);die;
                if ($mandatory == 1) {

                    echo Html::{$field["fieldtype"]}(
                        $field["tablename"] .'['.$cnt_rows.']' . '[' . $field["fieldname"] . ']',
                        isset($Record->{$field["columnname"]}) ? explode(',', $Record->{$field["columnname"]}) : '',
                        $field["fieldoptions"],
                        [
                            'id' => $field["fieldname"]."_".$cnt_rows,
                            'class' => 'form-control ' . $typeofdata . " " . $read,
                            "prompt" => "Select " . $field["fieldlabel"],
                            'data-pristine-required' => 'true',
                            'data-pristine-required-message' => $field["fieldlabel"] . ' is required ',
                            'multiple' => true,
                        ]
                    );
                } else {
                    echo Html::{$field["fieldtype"]}(
                        $field["tablename"] .'['.$cnt_rows.']' . '[' . $field["fieldname"] . ']',
                        isset($Record->{$field["columnname"]}) ? explode(',', $Record->{$field["columnname"]}) : '',
                        $field["fieldoptions"],
                        [
                            'id' => $field["fieldname"]."_".$cnt_rows,
                            // 'disabled' => $readonly ? true : false,
                            'class' => 'form-control ' . $typeofdata . " " . $read,
                            "prompt" => "Select " . $field["fieldlabel"],
                            'multiple' => true,

                        ]
                    );
                }
            } elseif ($field["uitype"] == 13 && $visible == 0) {
                $cnt_rows = $cnt_rows;
                include "uitype/Datetimemulti.php";
                // echo Html::input($field["fieldtype"], $field["tablename"] .'['.$cnt_rows.']' . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);
            } elseif ($field["uitype"] == 17 && $visible == 0) {
                $cnt_rows = $cnt_rows;
                include "uitype/Datemulti.php";
                // echo Html::input($field["fieldtype"], $field["tablename"] .'['.$cnt_rows.']' . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);
            } elseif ($field["uitype"] == 12 && $visible == 0) { ?>
                        <div>
                        <?php
                        $relatedmod_tabid = $field["related_mod"];
                        $fieldname = $field["columnname"]."_".$cnt_rows;
                        $fieldname1 = $fieldname . "1";
                        $fieldname2 = $fieldname . "2";
                        $fieldname3 = $fieldname . "3";
                        $model1 = new Reference($TableName, $FieldId);
                        $relatedmodulename = $model1->getRelatedNoduleName($field["fieldid"]);
                        $getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldName($field["fieldid"]);

                        $ref_hid_value = isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '';

                        if (isset($Record->{$field["columnname"]}) && $Record->{$field["columnname"]} != '')
                            $ref_disp_value = $model1->getRefEntityValue($field["fieldid"], $ref_hid_value);
                        else
                            $ref_disp_value = '';


                        $relatedmod = '';// $field["relatedmodulename"];
                        $getRelatedDField = '';// $field["getRelatedDisplayFieldName"];
                
                        ?>



                            <div class="vendor-input-wrapper">
                                <!-- Cross Icon on the Left -->
                                <svg class="icon-left" fill="#2F80ED" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="15"
                                    height="15" role="button" tabindex="0"
                                    id="removeTextValue" 
								data-fieldname1="<?= $fieldname1 ?>"
								data-fieldname="<?= $fieldname ?>"
                                 aria-label="Remove vendor">
                                    <path
                                        d="M4.7070312 3.2929688 L3.2929688 4.7070312 L10.585938 12 L3.2929688 19.292969 L4.7070312 20.707031 L12 13.414062 L19.292969 20.707031 L20.707031 19.292969 L13.414062 12 L20.707031 4.7070312 L19.292969 3.2929688 L12 10.585938 L4.7070312 3.2929688 Z">
                                    </path>
                                </svg>

                                <input class="effect" style="flex-grow:1;" type="hidden" id="<?php echo $fieldname1; ?>"
                                    name="<?php echo $field["tablename"] .'['.$cnt_rows.']' . '[' . $field["fieldname"] . ']' ?>"
                                    value="<?php echo $ref_hid_value; ?>" readonly='readonly'>
                        <?php echo Html::{$field["fieldtype"]}('', $ref_disp_value, $classarray); ?>


                                <!-- Search Icon on the Right -->
                                <svg class="icon-right search-icon" width="15" height="15" viewBox="0 0 24 25" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" data-toggle="modal" data-target="#myModal22" role="button"
                                    aria-hidden="true" tabindex="0" 
                                    id="showCustomer1"
                                        data-fieldname1="<?= $fieldname1 ?>"
                                        data-fieldname="<?= $fieldname ?>"
                                        data-display="<?= $getRelatedDisplayFieldName ?>"
                                        data-module="<?= $relatedmodulename ?>"
                                        data-fieldid="<?= $field['fieldid'] ?>"
                                        data-val6=""
                                        data-val7=""
                                        data-val8=""
                                        data-sourcemodule=""
                                        data-sourceid=""
                                            aria-label="Search vendor">
                                    <path
                                        d="M21 21.5L16.514 17.006L21 21.5ZM19 11C19 13.2543 18.1045 15.4163 16.5104 17.0104C14.9163 18.6045 12.7543 19.5 10.5 19.5C8.24566 19.5 6.08365 18.6045 4.48959 17.0104C2.89553 15.4163 2 13.2543 2 11C2 8.74566 2.89553 6.58365 4.48959 4.98959C6.08365 3.39553 8.24566 2.5 10.5 2.5C12.7543 2.5 14.9163 3.39553 16.5104 4.98959C18.1045 6.58365 19 8.74566 19 11V11Z"
                                        stroke="#2F80ED" stroke-width="2" stroke-linecap="round"></path>
                                </svg>

                                <!-- Plus Icon on the Right -->
                                <!-- <svg class="icon-right plus-icon" fill="#2F80ED" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                    width="15" height="15" role="button" tabindex="0" onclick="addVendor('<?= $fieldname1 ?>','<?= $fieldname ?>','<?= $getRelatedDisplayFieldName; ?>','<?= $relatedmodulename; ?>',<?= $block->blockid;
                                            ; ?>)" aria-label="Add vendor">
                                    <path d="M12 5v7H5v2h7v7h2v-7h7v-2h-7V5z"></path>
                                </svg> -->
                            </div>




                        </div>
            <?php } elseif ($field["uitype"] == 27 && $visible == 0) { ?>
                        <d>
                        <?php
                        $relatedmod_tabid = $field["related_mod"];
                        $fieldname = $field["columnname"]."_".$cnt_rows;
                        $fieldname1 = $fieldname . "1";
                        $fieldname2 = $fieldname . "2";
                        $fieldname3 = $fieldname . "3";
                        $model1 = new Reference($TableName, $FieldId);
                        $relatedmodulename = $model1->getRelatedNoduleName($field["fieldid"]);
                        $getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldName($field["fieldid"]);

                        $ref_hid_value = isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '';

                        if (isset($Record->{$field["columnname"]}) && $Record->{$field["columnname"]} != '')
                            $ref_disp_value = $model1->getRefEntityValue($field["fieldid"], $ref_hid_value);
                        else
                            $ref_disp_value = '';


                        $relatedmod = '';// $field["relatedmodulename"];
                        $getRelatedDField = '';// $field["getRelatedDisplayFieldName"];
                
                        ?>
                            <input class="effect" style="flex-grow:1;" type="hidden" id="<?php echo $fieldname1; ?>"
                                name="<?php echo $field["tablename"] . '['.$cnt_rows.']' .'[' . $field["fieldname"] . ']' ?>"
                                value="<?php echo $ref_hid_value; ?>" readonly='readonly'>
                    <?php echo Html::{$field["fieldtype"]}('', $ref_disp_value, $classarray); ?>



                <?php } elseif ($field["uitype"] == 28 && $visible == 0) { //conditional reference ?>
                            <div>
                            <?php
                            $relatedmod_tabid = $field["related_mod"];
                            $fieldname = $field["columnname"]."_".$cnt_rows;
                            $fieldname1 = $fieldname . "1";
                            $fieldname2 = $fieldname . "2";
                            $fieldname3 = $fieldname . "3";
                            $model1 = new Reference($TableName, $FieldId);
                            $relatedmodulename = $model1->getRelatedNoduleName($field["fieldid"]);
                            $getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldName($field["fieldid"]);
                            $getRelatedConditionFieldName = $model1->getRelatedConditionFieldName($field["fieldid"]);

                            $ref_hid_value = isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '';

                            if (isset($Record->{$field["columnname"]}) && $Record->{$field["columnname"]} != '')
                                $ref_disp_value = $model1->getRefEntityValue($field["fieldid"], $ref_hid_value);
                            else
                                $ref_disp_value = '';


                            $relatedmod = '';// $field["relatedmodulename"];
                            $getRelatedDField = '';// $field["getRelatedDisplayFieldName"];
                    
                            ?>



                                <div class="vendor-input-wrapper">
                                    <!-- Cross Icon on the Left -->
                                    <svg class="icon-left" fill="#2F80ED" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                        width="15" height="15" role="button" tabindex="0"
                                        id="removeTextValue" data-fieldname1="<?= $fieldname1 ?>" data-fieldname="<?= $fieldname ?>" aria-label="Remove vendor">
                                        <path
                                            d="M4.7070312 3.2929688 L3.2929688 4.7070312 L10.585938 12 L3.2929688 19.292969 L4.7070312 20.707031 L12 13.414062 L19.292969 20.707031 L20.707031 19.292969 L13.414062 12 L20.707031 4.7070312 L19.292969 3.2929688 L12 10.585938 L4.7070312 3.2929688 Z">
                                        </path>
                                    </svg>

                                    <input class="effect" style="flex-grow:1;" type="hidden" id="<?php echo $fieldname1; ?>"
                                        name="<?php echo $field["tablename"] .'['.$cnt_rows.']' . '[' . $field["fieldname"] . ']' ?>"
                                        value="<?php echo $ref_hid_value; ?>" readonly='readonly'>
                            <?php echo Html::{$field["fieldtype"]}('', $ref_disp_value, $classarray); ?>


                                    <!-- Search Icon on the Right -->
                                    <svg class="icon-right search-icon" width="15" height="15" viewBox="0 0 24 25" fill="none"
                                        xmlns="http://www.w3.org/2000/svg" data-toggle="modal" data-target="#myModal22" role="button"
                                        aria-hidden="true" tabindex="0"
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
                                        <path
                                            d="M21 21.5L16.514 17.006L21 21.5ZM19 11C19 13.2543 18.1045 15.4163 16.5104 17.0104C14.9163 18.6045 12.7543 19.5 10.5 19.5C8.24566 19.5 6.08365 18.6045 4.48959 17.0104C2.89553 15.4163 2 13.2543 2 11C2 8.74566 2.89553 6.58365 4.48959 4.98959C6.08365 3.39553 8.24566 2.5 10.5 2.5C12.7543 2.5 14.9163 3.39553 16.5104 4.98959C18.1045 6.58365 19 8.74566 19 11V11Z"
                                            stroke="#2F80ED" stroke-width="2" stroke-linecap="round"></path>
                                    </svg>

                                    <!-- Plus Icon on the Right -->
                                    <!-- <svg class="icon-right plus-icon" fill="#2F80ED" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24" width="15" height="15" role="button" tabindex="0" onclick="addVendor('<?= $fieldname1 ?>','<?= $fieldname ?>','<?= $getRelatedDisplayFieldName; ?>','<?= $relatedmodulename; ?>',<?= $block->blockid;
                                                ; ?>)" aria-label="Add vendor">
                                        <path d="M12 5v7H5v2h7v7h2v-7h7v-2h-7V5z"></path>
                                    </svg> -->
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
                            $fieldname = $field["columnname"]."_".$cnt_rows;
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
                                    if ((!empty($Record['related_to']) && $value == $Record['related_to']) || $i == 0) {
                                        $relatedmodulename = $model1->getRelatedNoduleNameBytab($field["fieldid"], $relatedmod_tabid);
                                        $getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldNameBytab($field["fieldid"], $relatedmod_tabid);
                                        // code added by ptpatel to resolve issue of server
                                        $fieldname1Esc = htmlspecialchars($fieldname1, ENT_QUOTES);
                                        $fieldnameEsc = htmlspecialchars($fieldname, ENT_QUOTES);
                                        $displayFieldEsc = htmlspecialchars($getRelatedDisplayFieldName, ENT_QUOTES);
                                        $moduleNameEsc = htmlspecialchars($relatedmodulename, ENT_QUOTES);
                                        $fieldidInt = (int) $field['fieldid'];
                                       
                                        $onclick1 = "showCustomer1('" . $fieldname1 . "','" . $fieldname . "', '" . htmlspecialchars($getRelatedDisplayFieldName) . "', '" . htmlspecialchars($relatedmodulename) . "', '" . (int)$field['fieldid'] . "')";
                                        
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

                            $ref_hid_value = isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : $val;
                            // echo "select targettable,entityidfield,fieldname from entityname where fieldid=".$field["fieldid"];die;
                    

                            if ($ref_hid_value) {
                                $ref_disp_value = $model1->getRefEntityValue($field["fieldid"], $ref_hid_value);
                            } else
                                $ref_disp_value = '';
                            ?>



                                <div class="vendor-input-wrapper">
                                <?php
                                // Check if 'readonly' is not equal to 'readonly' (simplified condition)
                                if ($classarray['readonly'] !== 'readonly'): ?>
                                        <!-- Cross Icon on the Left -->
                                        <svg class="icon-left" fill="#2F80ED" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                            width="15" height="15" role="button" tabindex="0"
                                            id="removeTextValue" data-fieldname1="<?= $fieldname1 ?>" data-fieldname="<?= $fieldname ?>" aria-label="Remove vendor">
                                            <path
                                                d="M4.7070312 3.2929688 L3.2929688 4.7070312 L10.585938 12 L3.2929688 19.292969 L4.7070312 20.707031 L12 13.414062 L19.292969 20.707031 L20.707031 19.292969 L13.414062 12 L20.707031 4.7070312 L19.292969 3.2929688 L12 10.585938 L4.7070312 3.2929688 Z">
                                            </path>
                                        </svg>
                            <?php endif; ?>

                                    <input class="effect" style="flex-grow:1;" type="hidden" id="<?php echo $fieldname1; ?>"
                                        name="<?php echo $field["tablename"] .'['.$cnt_rows.']' . '[' . $field["fieldname"] . ']' ?>"
                                        value="<?php echo $ref_hid_value; ?>" readonly='readonly'>
                            <?php echo Html::{$field["fieldtype"]}('', $ref_disp_value, $classarray); ?>

                                <?php
                                // Check if 'readonly' is not equal to 'readonly' (simplified condition)
                                if ($classarray['readonly'] !== 'readonly'): ?>
                                        <!-- Search Icon on the Right -->
                                        <svg class="icon-right related-search-icon search-icon" width="15" height="15" viewBox="0 0 24 25" fill="none"
                                            xmlns="http://www.w3.org/2000/svg" data-toggle="modal" data-target="#myModal22" role="button"
                                            aria-hidden="true" tabindex="0" data-onrefclick="<?= $onclick1 ?>" aria-label="Search vendor">
                                            <path
                                                d="M21 21.5L16.514 17.006L21 21.5ZM19 11C19 13.2543 18.1045 15.4163 16.5104 17.0104C14.9163 18.6045 12.7543 19.5 10.5 19.5C8.24566 19.5 6.08365 18.6045 4.48959 17.0104C2.89553 15.4163 2 13.2543 2 11C2 8.74566 2.89553 6.58365 4.48959 4.98959C6.08365 3.39553 8.24566 2.5 10.5 2.5C12.7543 2.5 14.9163 3.39553 16.5104 4.98959C18.1045 6.58365 19 8.74566 19 11V11Z"
                                                stroke="#2F80ED" stroke-width="2" stroke-linecap="round"></path>
                                        </svg>



                            <?php endif; ?>
                                </div>




                            </div>
                <?php } elseif ($field["uitype"] == 6 && $visible == 0) { //checkbox
                if (isset($Record->{$field["columnname"]})) {
                    if ($Record->{$field["columnname"]} == 1)
                        $checked = 1;
                    else
                        $checked = 0;
                } else
                    $checked = '';
                if ($mandatory == 1) {
                    echo Html::{$field["fieldtype"]}(
                        $field["tablename"] . '['.$cnt_rows.']' .'[' . $field["fieldname"] . ']', // Name attribute
                        $checked ? true : false, // Checked state
                        [
                            'disabled' => $readonly ? true : false,
                            "class" => "form-check-input " . $field['typeofdata'] . " " . $field["classname"] . " " . $read, // Optional custom CSS class
                            "label" => $field["fieldlabel"], // Label for the checkbox
                            'data-pristine-required' => 'true', // Custom attribute
                            'data-pristine-required-message' => $field["fieldlabel"] . ' is required ' // Custom validation message
                        ]
                    );
                } else {

                    echo Html::{$field["fieldtype"]}(
                        $field["tablename"] .'['.$cnt_rows.']' . '[' . $field["fieldname"] . ']', // Name attribute
                        $checked ? true : false, // Checked state
                        [
                            'disabled' => $readonly ? true : false,
                            "class" => "form-check-input " . $field['typeofdata'] . " " . $field["classname"] . " " . $read, // Optional custom CSS class
                            "label" => $field["fieldlabel"], // Label for the checkbox
        
                        ]
                    );
                }
            } elseif ($field["uitype"] == 53) //hiden
            {
                echo Html::hiddenInput($field["tablename"] .'['.$cnt_rows.']' . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : Yii::$app->user->id, [

                    'id' => $field["fieldname"]."_".$cnt_rows,
                ]);
            } elseif ($field["uitype"] == 70) //hiden
            {
                echo Html::hiddenInput($field["tablename"] .'['.$cnt_rows.']' . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : date("Y-m-d H:i:s"), [

                    'id' => $field["fieldname"]."_".$cnt_rows,
                ]);
            } else if ($field["uitype"] == 11) //hiden
            {
                echo Html::hiddenInput($field["tablename"] .'['.$cnt_rows.']' . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);
            } else if($visible == 0) {
                echo "work in progress for XX uitype -- " . $field["uitype"];
            }
            //}counter if
        // && ($field['tabid'] == 68 && $field["fieldid"] != "2752") added by ptpatel on date 09-01-2026 to remove two helpblock in tagging
            if ($field["uitype"] != 2 && $field["uitype"] != 11 && $field["fieldid"] != 19 && ($field['tabid'] == 68 && $field["fieldid"] != "2752")) {
                echo '<div class="help-block"></div>';

                $counter++;
            }

        } ?>
        <!-- close td -->
            </td>

            <td><button class="remove-row-btn">X</button></td>
    </tr>
    <?php
    die;?>