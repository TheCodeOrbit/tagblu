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

// $this->title = Yii::t('app', $TabLabel . " Detail");

$scriptPath = $baseUrl . "js/$ModuleName/edit.js";
$relationName = $action_name === 'create' ? 'createfields' : 'editfields';

$counter = 0;
$relationName = $action_name === 'create' ? 'createfields' : 'editfields';
?>

<style>.col-md-3{
	border: 0 !important;
}
.selectedRecords {
    /* display: flex;
    flex-wrap: wrap;
    align-items: center; */
    min-height: 38px;
    /* border: 1px solid #ccc;
    border-radius: 6px;
    padding: 4px 6px;
    background-color: #fff;
    cursor: text; */
}

.selectedRecords .tag {
    display: flex;
    align-items: center;
    background-color: #e3f2fd;
    border: 1px solid #90caf9;
    border-radius: 3px;
    padding: 4px 8px;
    margin: 2px;
    font-size: 13px;
    color: #1565c0;
}

.selectedRecords .tag .remove-chip {
    margin-left: 6px;
    cursor: pointer;
    font-weight: bold;
    color: #1976d2;
	padding: 0px 2px 0px 2px;
}

.selectedRecords .tag .remove-chip:hover {
    color: #0d47a1;
}

</style>
<?php
$j = 1;
foreach ($block->$relationName as $field) {
	if ($j == 1)
		echo '<div class="row pad-top blockrow' . $block->blockid . '">';
	$j++;

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
		} else { //remove when fieldaccess is implemented properly
			$visible = 0;
			$readonly = 0;
		}
	}
	$mandatory = '';
	
	if ($field['readonly'] == 0)
	{
		$readonly = 1;
		
	}
	else{
		$typeofdata = $field['typeofdata'];
		$mandatory = $field['mandatory'];
	}
	
		// print_r($field);die;
	
	//else echo $permission; exit();
	if ($field["uitype"] != 70 && $visible == 0) {
		$FieldTypeRecord = Fieldtype::find()->where(['uitype' => $field["uitype"]])->one();
		$field["fieldtype"] = $FieldTypeRecord['getfieldtype'];
		$field["classname"] = $FieldTypeRecord['classname'];
	}
	// below if condition added by ptpatel to allow edit to admin only from field table
	if($hasadminpower == 0 && $field['admin_edit_allow'] == 1 ){
		$readonly = 1;
	}
	// end if condition added by ptpatel to allow edit to admin only from field table
	//for dropdownclass
	$read = $readonly ? 'readonly-dd' : '';


	if (($field["uitype"] == 8 || $field["uitype"] == 25 || $field["uitype"] == 24 || $field["uitype"] == 9 || $field["uitype"] == 10) && $visible == 0) {
		$PickList = new Picklist;
		$PickList->fieldid = $field["fieldid"];
		// $Column->blocks[$blockKey]->fields[$FieldKey]->fieldoptions=$PickList->getPickListOption($table_name);
		$ownerId = $Record['ownerid'] ?? '';
		if ($field['fieldname'] == 'vertical_manager')
			$field["fieldoptions"] = $PickList->getVerticalManager($ModuleName);
		else if ($field['fieldname'] == 'ownerid')
			$field["fieldoptions"] = $PickList->getusers($field['fieldname'], $field['uitype'], Yii::$app->user->id,$ownerId);
		else
			$field["fieldoptions"] = $PickList->getPickListOption($ModuleName);
		if ($ModuleName == "leads" && $field["fieldname"] == "leadstatus") {
			//print_r($Record['vertical_manager']);die;
			//if ($ActionName == "Create") {
			//limit  fieldoptons
			// Keys to remove
			$keysToRemove = [3, 13, 5, 6, 4];

			$arraykey = $field["fieldoptions"];
			//if lead status equal to keystoremove then show it and non editable
			if (isset($Record['leadstatus']) && in_array($Record['leadstatus'], $keysToRemove)) {
				$read = 'readonly-dd';
			} else {
				// Remove elements
				$field["fieldoptions"] = array_diff_key($field["fieldoptions"], array_flip($keysToRemove));
			}

			// print_r($field["fieldoptions"]);die;
			// } 
			// else if ($ActionName == "Edit" && $Record["vertical_manager"] == Yii::$app->user->id) {
			// 	//limit  fieldoptons
			// 	// Keys to remove
			// 	$keysToRemove = [1, 2, 4, 6, 7, 8, 9, 10, 11, 12];
			// 	$arraykey = $field["fieldoptions"];
			// 	// Remove elements
			// 	$field["fieldoptions"] = array_diff_key($field["fieldoptions"], array_flip($keysToRemove));

			// 	// print_r($field["fieldoptions"]);die;

			// }
		}
	} else if ($field["uitype"] == 22 && $visible == 0) {
		$PickList = new Multilist;
		$PickList->fieldid = $field["fieldid"];
		// $Column->blocks[$blockKey]->fields[$FieldKey]->fieldoptions=$PickList->getMultiListOption($table_name);


		$field["fieldoptions"] = $PickList->getMultiListOption($ModuleName);
	}
	//print_r($FieldTypeRecord);die;
	if ($field["uitype"] != 2 && $field["columnname"] != 'salutation'  && $field["uitype"] != 70 && $field["uitype"] != 53 && $visible == 0) {
		if ($mandatory == 1)
			$clss = "required-field";
		else
			$clss = 'not-required-field';
		//close/open divs
		if ($counter == 0) {

			echo '<div class="form-group  ' . $clss . ' form-field-cst section-' . $field["columnname"] . ' col-lg-3 col-md-6 mb-2">';
		} else {
			echo '</div><div class="form-group  ' . $clss . ' form-field-cst section-' . $field["columnname"] . ' col-lg-3 col-md-6 mb-2">';
		}
	}
	if ($field["uitype"] == 12 || $field["uitype"] == 26 || $field["uitype"] == 28 || $field["uitype"] == 31) {
		$popupclass = "ref-form-control";
	} else {
		$popupclass = '';
	}
	if ($field["uitype"] == 31) {
		$popupclass = "selectedRecords";
	}

	if ($readonly == 1)
	{
		
		//added on 20 march 2025
		$typeofdata = str_replace("~M","~O",$field["typeofdata"]);
		$mandatory=0;
	}
	else{
		$typeofdata = $field['typeofdata'];
		$mandatory = $field['mandatory'];
	}
	

	//check mandatory
	if ($mandatory == 1) {

		//echo $field["maximumlength"];
		$classarray = array(
			'class' => 'form-control ' . $popupclass . ' ' . $typeofdata,
			'id' => $field["fieldname"],
			"fieldid" => $field["fieldid"],
			"value" => isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "",
			'maxlength' => $field["maximumlength"],
			'data-pristine-required' => 'true',
			'data-pristine-required-message' => $field["fieldlabel"] . ' is required ',
			'readonly' => $readonly ? true : false,
			'data-isunique' => $field['isunique'] == 1 ? '1' : ''

		);
		$mandatoryspan = "<span class='red'> *</span>";
	} else {
		$classarray = array(
			'class' => $popupclass . ' form-control ' . $readonly . ' ' . $typeofdata,
			'readonly' => $readonly ? true : false,
			'id' => $field["fieldname"],
			'maxlength' => $field["maximumlength"],
			"value" => isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "",
			'data-isunique' => $field['isunique'] == 1 ? '1' : ''
		);
		$mandatoryspan = "";
	}


	?>

	<?php if ($field["uitype"] != 2  && $field["columnname"] != 'salutation'  && $field["uitype"] != 70 && $field["uitype"] != 53 && $field["uitype"] != 6 && $visible == 0) { //show label if not hiden type and not radio type
		
				echo Html::label($field["fieldlabel"] . $mandatoryspan, $field["fieldname"], ['class' => 'control-label ', 'title' => !empty($field["description"]) ? $field["description"] : $field["fieldlabel"]]);
			}

			if ($field["uitype"] == 1 && $visible == 0) {
				if ($field["fieldid"] == 8 || $field["fieldid"] == 100) {
					$PickList = new Picklist;
					$PickList->fieldid = 19;
					// $Column->blocks[$blockKey]->fields[$FieldKey]->fieldoptions=$PickList->getPickListOption($table_name);
					// echo $field['columnname'];die;

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
			  padding: 2px;" name=' . $field["tablename"] . '[salutation]">
			  ';
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
						Html::{$field["fieldtype"]}($field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray) . '
			                                    </div>
			                                </div>';
				} else {
					echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);
				}
			} 
			else if ($field['uitype'] == 11) {
				
				echo '<input type="text" id="'.$field["fieldname"].'" class=" form-control 1 V~O" name="'.$field["tablename"] . '[' . $field["fieldname"] . ']" value="'.$Record->{$field["columnname"]}.'" readonly="" maxlength="100">';
			}
			else if ($field['uitype'] == 16) {
				$classarray['class'] = $classarray['class'] . ' timepicker';
				// print_r($classarray);die;
				include 'uitype/duration.php';
				//echo $this->render('/uitype/',['classarray'=>$classarray,'field'=>$field,'fieldoptions'=>$field["fieldoptions"]]);  // Include the HTML structure defined earlier
		
				// 		echo Html::input('time', $field["tablename"] . '[' . $field["fieldname"] . ']', '', [
				//     'step' => 60,  // Restrict input to full minutes (no seconds)
				//     'class' => $classarray,
				//     'id' => $field['fieldname'],
				// ]);
			}
			else if ($field['uitype'] == 30) {
				$classarray['class'] = $classarray['class'] . ' timepicker';
				// print_r($classarray);die;
				include 'uitype/time.php';
				//echo $this->render('/uitype/',['classarray'=>$classarray,'field'=>$field,'fieldoptions'=>$field["fieldoptions"]]);  // Include the HTML structure defined earlier
		
				// 		echo Html::input('time', $field["tablename"] . '[' . $field["fieldname"] . ']', '', [
				//     'step' => 60,  // Restrict input to full minutes (no seconds)
				//     'class' => $classarray,
				//     'id' => $field['fieldname'],
				// ]);
			} else if ($field["uitype"] == 5 && $visible == 0) {


				if (isset($Record->{$field["columnname"]})) {
					// echo $Record->{$field["columnname"]};die;
					$MRecords = \app\models\Attachments::find()
						->where(['attachmentsid' => $Record->{$field["columnname"]}])
						->one();
					//   print_r($MRecords);die;
					//   echo $MRecords->name;die;
					if ($MRecords) {
						if ($field["columnname"] == 'profilepic') {
							// echo $baseUrl;die;
							$filepath = $baseUrl . $Record->{$field["columnname"]};
						} else {
							$filepath = "<br><a href='" . $baseUrl . $ModuleName . "/download?fileid=" . $Record->{$field["columnname"]} . "'>" . $MRecords->name . "</a>";
						}
					}

					// $filepath = $MRecords->name;
					$classarray['class'] = 'form-control temp-file';
					$classarray['data-module'] = $ModuleName;
					echo "<input type='hidden' name='" . $field["columnname"] . "_hiddenfile' value='" . $Record->{$field["columnname"]} . "'>";
				} else
					$filepth = '';
				
				if (isset($classarray['class'])) {
					$classarray['class'] .= ' temp-file';
				} else {
					$classarray['class'] = 'temp-file';
				}
				$classarray['data-module'] = $ModuleName;
				if($readonly == 0)
				echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);
			else
				echo Html::{$field["fieldtype"]}(
					$field["tablename"] . '[' . $field["fieldname"] . ']',
					isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "",
					array_merge($classarray, ['disabled' => true])
				);
				//for preview
				echo '<div class="file-preview mt-2"></div>';
				//end for preview
				if (!empty($MRecords) && !empty($filepath) ) {
					if ($field["columnname"] == 'profilepic') {
						echo "<br><img src='" . $filepath . "' height='150' width='150'/>";
					} 
					// ptpatel added if($Record->{$field["columnname"]} != '') on date 05-04-25 this problem arise in edit mode
					else if($Record->{$field["columnname"]} != '') {
						echo "<br><div class='upd-file'>Uploaded file: " . $filepath . "</div>";
					}
				}
				if($field['columnname'] == 'vrf_form')
                {
                    echo "<a href='".$baseUrl."thememain/samples/vrf_form.docx' download>Dowanload Sample VRF Form Here</a>";
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
					echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $field["fieldname"] . ']', $val, $classarray);
				} else
					echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);
			} elseif ($field["uitype"] == 4 && $visible == 0) //text area
			{
				echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);
			} elseif ($field["uitype"] == 8 && $visible == 0 && $field["columnname"] != 'salutation') //simgle drop down
			{
				if ($field["columnname"] == "ownerid") {

					// echo Html::{$field["fieldtype"]}(
					// 	$field["tablename"] . '[' . $field["fieldname"] . ']',
					// 	isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : Yii::$app->user->id,
					// 	$field["fieldoptions"],
					// 	['class' => 'form-control ' . $field["typeofdata"].' '.$read, 'id' => $field["fieldname"], "prompt" => "Select " . $field["fieldlabel"], 'data-pristine-required' => 'true', 'data-pristine-required-message' => $field["fieldlabel"] . ' is required ',]
					// );
					// added on 08 jan 2025 by deepika check if admin then only shw dropdown
					// if ($hasadminpower === 1)
					// 	$classarray['class'] = $classarray['class'] . ' singleselect ';
					// else
					// 	$classarray['class'] = $classarray['class'] . ' readonly-dd';
					$classarray['class'] = $classarray['class'] . ' singleselect';


					echo $this->render('dropdown', ['baseUrl' => $baseUrl, 'classarray' => $classarray, 'field' => $field, 'fieldoptions' => $field["fieldoptions"], 'Record' => isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : Yii::$app->user->id, 'readonly' => $readonly, 'read' => $read]);
				} else {
					// print_r($field["fieldoptions"]);
					// echo $read;
					if (!empty($read))
						$classarray['class'] = $classarray['class'] . ' readonly-dd';
					else
						$classarray['class'] = $classarray['class'] . ' singleselect';

					// print_r($classarray);
		

					echo $this->render('dropdown', ['baseUrl' => $baseUrl, 'classarray' => $classarray, 'field' => $field, 'fieldoptions' => $field["fieldoptions"], 'Record' => isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '', 'readonly' => $readonly, 'read' => $read]);
				}
			} elseif ($field["uitype"] == 9 && $visible == 0) //checkboxlist
			{
				// print_r($field["fieldoptions"]);
				//print_r( $Record->{$field["columnname"]});
				if (isset($Record->{$field["columnname"]}))
					$selectedValues = explode(',', $Record->{$field["columnname"]});
				else
					$selectedValues = '';
				if ($mandatory == 1) {
					echo Html::checkboxList(
						$field["tablename"] . '[' . $field["fieldname"] . ']', // Name for the checkbox list
						$selectedValues, // Array of selected values
						$field["fieldoptions"], // Array of options (value => label)
						[
							'item' => function ($index, $label, $name, $checked, $value) use ($field, $read,$typeofdata) {
								return Html::tag(
									'label', // Wrap the checkbox and label in a <label> element
									Html::checkbox($name, $checked, [
										'value' => $value,
										'class' => 'form-check-input ' . $typeofdata . ' ' . $read, // Custom class for checkbox
										'id' => $field["fieldname"] . '_' . $index, // Unique ID for each checkbox
										'data-pristine-required' => 'true',
										'data-pristine-required-message' => $label . ' is required ',
										'title' => !empty($field["description"]) ? $field["description"] : $field["fieldlabel"],
									]) . ' ' . Html::encode($label), // Display the label text
									['class' => 'form-check-label'] // Apply the 'red' class to the label
								);
							},
							'class' => 'form-check-list', // Optional: Add a wrapper class for the list
						]
					);


					// echo Html::{$field["fieldtype"]}(
					// 	$field["tablename"] . '[' . $field["fieldname"] . ']',
					// 	$selectedValues,
					// 	$field["fieldoptions"],
					// 	['class' => 'form-check-input ' . $field["typeofdata"] . ' ' . $read, 'id' => $field["fieldname"], "prompt" => "Select " . $field["fieldlabel"], 'data-pristine-required' => 'true', 'data-pristine-required-message' => $field["fieldlabel"] . ' is required ',]
					// );
				} else {
					echo Html::checkboxList(
						$field["tablename"] . '[' . $field["fieldname"] . ']', // Name for the checkbox list
						$selectedValues, // Array of selected values
						$field["fieldoptions"], // Array of options (value => label)
						[
							'item' => function ($index, $label, $name, $checked, $value) use ($field, $read,$typeofdata) {
								return Html::tag(
									'label', // Wrap the checkbox and label in a <label> element
									Html::checkbox($name, $checked, [
										'value' => $value,
										'class' => 'form-check-input ' . $typeofdata . ' ' . $read, // Custom class for checkbox
										'id' => $field["fieldname"] . '_' . $index, // Unique ID for each checkbox
				
									]) . ' ' . Html::encode($label), // Display the label text
									['class' => 'form-check-label'] // Apply the 'red' class to the label
								);
							},
							'class' => 'form-check-list', // Optional: Add a wrapper class for the list
						]
					);
					// echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $field["fieldname"] . ']',
					// //  isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '',
					// $selectedValues, 
					//  $field["fieldoptions"], ['class' => 'form-control ' . $field["typeofdata"] . ' ' . $read, 'id' => $field["fieldname"], "prompt" => "Select " . $field["fieldlabel"]]);
				}
			} elseif ($field["uitype"] == 10 && $visible == 0) //radio list
			{
				// print_r($field["fieldoptions"]);
		
				if ($mandatory == 1) {
					echo Html::radioList(
						$field["tablename"] . '[' . $field["fieldname"] . ']', // Name for the radio button list
						isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '', // Selected value
						$field["fieldoptions"], // Array of options (value => label)
						[
							'item' => function ($index, $label, $name, $checked, $value) use ($field, $read,$typeofdata) {
								return Html::tag(
									'label', // Wrap the radio button and label in a <label> element
									Html::radio($name, $checked, [
										'value' => $value,
										'class' => 'form-check-input ' . $typeofdata . ' ' . $read, // Custom class for radio button
										'id' => $field["fieldname"] . '_' . $index, // Unique ID for each radio button
										'data-pristine-required' => 'true',
										'data-pristine-required-message' => $label . ' is required ',
									]) . ' ' . Html::encode($label), // Display the label text
									['class' => 'form-check-label '] // Apply the 'red' class to the label
								);
							},
							'class' => 'form-check-list', // Optional: Add a wrapper class for the list
						]
					);


					// echo Html::{$field["fieldtype"]}(
					// 	$field["tablename"] . '[' . $field["fieldname"] . ']',
					// 	isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '',
					// 	$field["fieldoptions"],
					// 	['class' => 'form-control ' . $field["typeofdata"] . ' ' . $read, 'id' => $field["fieldname"], "prompt" => "Select " . $field["fieldlabel"], 'data-pristine-required' => 'true', 'data-pristine-required-message' => $field["fieldlabel"] . ' is required ']
					// );
				} else {
					// if($field["columnname"] == 'reminder')
					// {
					// 	echo "inn".$typeofdata ;	
					// 	echo $field["columnname"];die;
					// }
					echo Html::radioList(
						$field["tablename"] . '[' . $field["fieldname"] . ']', // Name for the radio button list
						isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '', // Selected value
						$field["fieldoptions"], // Array of options (value => label)
						[
							'item' => function ($index, $label, $name, $checked, $value) use ($field, $read,$typeofdata) {
								return Html::tag(
									'label', // Wrap the radio button and label in a <label> element
									Html::radio($name, $checked, [
										'value' => $value,
										'class' => 'form-check-input ' .$typeofdata. ' ' . $read, // Custom class for radio button
										'id' => $field["fieldname"] . '_' . $index, // Unique ID for each radio button
				
									]) . ' ' . Html::encode($label), // Display the label text
									['class' => 'form-check-label '] // Apply the 'red' class to the label
								);
							},
							'class' => 'form-check-list', // Optional: Add a wrapper class for the list
						]
					);

					// echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '', $field["fieldoptions"], ['class' => 'form-control ' . $field["typeofdata"] . ' ' . $read, 'id' => $field["fieldname"], "prompt" => "Select " . $field["fieldlabel"]]);
				}
			} elseif ($field["uitype"] == 22 && $visible == 0) //multi drop down
			{
				if (isset($Record->{$field["columnname"]}))
					$selectedValues = explode(',', $Record->{$field["columnname"]});
				else
					$selectedValues = '';
				// Output the multi-list (above HTML)
				echo $this->render('multi_list', ['baseUrl' => $baseUrl, 'classarray' => $classarray, 'field' => $field, 'fieldoptions' => $field["fieldoptions"], 'Record' => isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '', 'selectedValues' => $selectedValues, 'readonly' => $readonly]);  // Include the HTML structure defined earlier
				// print_r($field["fieldoptions"]);
		
				// if ($field["mandatory"] == 1) {
		
				// 	echo Html::{$field["fieldtype"]}(
				// 		$field["tablename"] . '[' . $field["fieldname"] . ']',
				// 		isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '',
				// 		$field["fieldoptions"],
				// 		['multiple' => true,  // Allow multiple selection,
				// 		'class' => 'form-control '.$field["typeofdata"],'id' => $field["fieldname"], "prompt" => "Select " . $field["fieldlabel"], 'data-pristine-required' => 'true', 'data-pristine-required-message' => $field["fieldlabel"] . ' is required ', 'disabled' => $readonly ? true : false]
				// 	);
				// } else {
				// 	echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '', $field["fieldoptions"], [
				// 		'multiple' => true,  // Allow multiple selection,
				// 		'disabled' => $readonly ? true : false,
				// 		 'class' => 'form-control '.$field["typeofdata"] ,
				// 		 'id' => $field["fieldname"],
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
				$output = [];
				foreach ($relatedkeys as $item) {
					$output[$item['source_module']] = str_replace(' ', '', $item['modulename']);
				}



				$options = $output;
				unset($options["prompt"]);

				if ($mandatory == 1) {
					echo Html::{$field["fieldtype"]}(
						$field["tablename"] . '[' . $field["fieldname"] . ']',
						isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : $val,
						$options,
						[
							'class' => 'form-control ' . $typeofdata . " " . $read,
							'id' => $field["fieldname"],
							'data-pristine-required' => 'true',
							'data-pristine-required-message' => $field["fieldlabel"] . ' is required ',
							// 'disabled' => $readonly ? true : false
						]
					);
				} else {
					echo Html::{$field["fieldtype"]}(
						$field["tablename"] . '[' . $field["fieldname"] . ']',
						isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : $val,
						$options,
						[
							// 'disabled' => $readonly ? true : false, 
							'class' => 'form-control ' . $typeofdata . " " . $read,
							'id' => $field["fieldname"],
						]
					);
				}
			} elseif ($field["uitype"] == 23 && $visible == 0) //for numeric
			{

				// <!-- Label for the text input -->
		
				echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);
			} elseif ($field["uitype"] == 24 && $visible == 0) //multiple drop down
			{
				// print_r($field["fieldoptions"]);die;
				if ($mandatory == 1) {

					echo Html::{$field["fieldtype"]}(
						$field["tablename"] . '[' . $field["fieldname"] . ']',
						isset($Record->{$field["columnname"]}) ? explode(',', $Record->{$field["columnname"]}) : '',
						$field["fieldoptions"],
						[
							'id' => $field["fieldname"],
							'class' => 'form-control ' . $typeofdata . " " . $read,
							"prompt" => "Select " . $field["fieldlabel"],
							'data-pristine-required' => 'true',
							'data-pristine-required-message' => $field["fieldlabel"] . ' is required ',
							'multiple' => true,
						]
					);
				} else {
					echo Html::{$field["fieldtype"]}(
						$field["tablename"] . '[' . $field["fieldname"] . ']',
						isset($Record->{$field["columnname"]}) ? explode(',', $Record->{$field["columnname"]}) : '',
						$field["fieldoptions"],
						[
							'id' => $field["fieldname"],
							// 'disabled' => $readonly ? true : false,
							'class' => 'form-control ' . $typeofdata . " " . $read,
							"prompt" => "Select " . $field["fieldlabel"],
							'multiple' => true,

						]
					);
				}
			} elseif ($field["uitype"] == 13 && $visible == 0) {
				include "uitype/Datetime.php";
				// echo Html::input($field["fieldtype"], $field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);
			} elseif ($field["uitype"] == 17 && $visible == 0) {
				include "uitype/Date.php";
				// echo Html::input($field["fieldtype"], $field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);
			} elseif ($field["uitype"] == 12 && $visible == 0) {
				//check sourceid and source module
				$val = '';
				if (isset($relatedkeys) && isset($sourceid) && isset($sourcemodule)) {
					$sourceModuleToFind = $sourcemodule; // The source_module you're looking for
		
					// Loop through the array
					foreach ($relatedkeys as $item) {
						if ($item['source_module'] == $sourceModuleToFind) {
							// If the source_module matches, return the related_recordfieldname
							$related_recordfieldnme = $item['related_recordfieldnme'];
							break; // Stop the loop once we find the match
						}
					}
					if ($related_recordfieldnme == $field["columnname"]) {
						$val = $sourceid;
						$classarray['readonly'] = 'readonly';
					}
				}
				?>
				<div>
				<?php
				$relatedmod_tabid = $field["related_mod"];
				$fieldname = $field["columnname"];
				$fieldname1 = $field["columnname"] . "1";
				$fieldname2 = $field["columnname"] . "2";
				$fieldname3 = $field["columnname"] . "3";
				$model1 = new Reference($TableName, $FieldId);
				$relatedmodulename = $model1->getRelatedNoduleName($field["fieldid"]);
				$getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldName($field["fieldid"]);

				$ref_hid_value = isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : $val;

				if ($ref_hid_value != '')
					$ref_disp_value = $model1->getRefEntityValue($field["fieldid"], $ref_hid_value);
				else
					$ref_disp_value = '';


				$relatedmod = ''; // $field["relatedmodulename"];
				$getRelatedDField = ''; // $field["getRelatedDisplayFieldName"];
				
				?>
					<div class="vendor-input-wrapper">
				<?php if ($classarray['readonly'] !== 'readonly' && $classarray['readonly'] !=1) { ?>
							<!-- Cross Icon on the Left -->
							<svg class="icon-left" fill="#2F80ED" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="15"
								height="15" role="button" tabindex="0" id="removeTextValue" data-fieldname1="<?= $fieldname1 ?>" data-fieldname="<?= $fieldname ?>"
								aria-label="Remove vendor">
								<path
									d="M4.7070312 3.2929688 L3.2929688 4.7070312 L10.585938 12 L3.2929688 19.292969 L4.7070312 20.707031 L12 13.414062 L19.292969 20.707031 L20.707031 19.292969 L13.414062 12 L20.707031 4.7070312 L19.292969 3.2929688 L12 10.585938 L4.7070312 3.2929688 Z">
								</path>
							</svg>
					<?php
				} ?>

						<input class="effect" style="flex-grow:1;" type="hidden" id="<?php echo $fieldname1; ?>"
							name="<?php echo $field["tablename"] . '[' . $field["fieldname"] . ']' ?>"
							value="<?php echo $ref_hid_value; ?>" readonly='readonly'>
					<?php
					if (is_array($classarray)) {
						$classarray["fieldid"] = $field["fieldid"];
					} ?>
				<?php echo Html::{$field["fieldtype"]}('', $ref_disp_value, $classarray); ?>

				<?php if ($classarray['readonly'] !== 'readonly' && $classarray['readonly'] !=1) { ?>
							<!-- Search Icon on the Right 3-->
							<svg class="icon-right search-icon plus-icon" width="15" height="15" viewBox="0 0 24 25" fill="none"
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
								width="15" height="15" role="button" tabindex="0" onclick="addVendor('<? //$fieldname1 ?>','<?// $fieldname ?>','<?// $getRelatedDisplayFieldName; ?>','<?// $relatedmodulename; ?>',<?// $field['fieldid'];
										; ?>)" aria-label="Add vendor">
								<path d="M12 5v7H5v2h7v7h2v-7h7v-2h-7V5z"></path>
							</svg> -->
					<?php
				} ?>
					</div>




				</div>
	<?php } elseif ($field["uitype"] == 31 && $visible == 0) {
				//check sourceid and source module
				$val = '';
				if (isset($relatedkeys) && isset($sourceid) && isset($sourcemodule)) {
					$sourceModuleToFind = $sourcemodule; // The source_module you're looking for
		
					// Loop through the array
					foreach ($relatedkeys as $item) {
						if ($item['source_module'] == $sourceModuleToFind) {
							// If the source_module matches, return the related_recordfieldname
							$related_recordfieldnme = $item['related_recordfieldnme'];
							break; // Stop the loop once we find the match
						}
					}
					if ($related_recordfieldnme == $field["columnname"]) {
						$val = $sourceid;
						$classarray['readonly'] = 'readonly';
					}
				}
				?>
				<div>
				<?php
				$relatedmod_tabid = $field["related_mod"];
				$fieldname = $field["columnname"];
				$fieldname1 = $field["columnname"] . "1";
				$fieldname2 = $field["columnname"] . "2";
				$fieldname3 = $field["columnname"] . "3";
				$model1 = new Reference($TableName, $FieldId);
				$relatedmodulename = $model1->getRelatedNoduleName($field["fieldid"]);
				$getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldName($field["fieldid"]);
				$refval='';
				$ref_hid_value='';
				if(isset($Record->{$field["columnname"]}))
				{
					$rec= explode(",",$Record->{$field["columnname"]});
					$reccont = count($rec);
					$i=1;
					foreach($rec as $rval)
					{
						// $ref_hid_value = isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : $val;
						$ref_hid_value .= $rval;
						if($reccont != $i)
						$ref_hid_value .= ",";

						if ($rval != '')
							$ref_disp_value = $model1->getRefEntityValue($field["fieldid"], $rval);
						else
							$ref_disp_value = '';

						if($ref_disp_value !='')
						{
							$refval .="<span class='tag'>$ref_disp_value<button type='button' class='remove-chip' data-id='$rval' data-hiddenfield='$fieldname1' data-textfield='$fieldname' aria-label='Remove $ref_disp_value'>×</button></span>";
						}
					}
					
				}
				


				$relatedmod = ''; // $field["relatedmodulename"];
				$getRelatedDField = ''; // $field["getRelatedDisplayFieldName"];
				
				?>
					<div class=""></div>

					<div class="vendor-input-wrapper">
				<?php if ($classarray['readonly'] !== 'readonly' && $classarray['readonly'] !=1) { ?>
							
					<?php
				} ?>

						<input class="effect" style="flex-grow:1;" type="hidden" id="<?php echo $fieldname1; ?>"
							name="<?php echo $field["tablename"] . '[' . $field["fieldname"] . ']' ?>"
							value="<?php echo $ref_hid_value; ?>" readonly='readonly'>

							
					<?php
					if (is_array($classarray)) {
						$classarray["fieldid"] = $field["fieldid"];
					} ?>
				<?php //echo Html::{$field["fieldtype"]}('', $ref_disp_value, $classarray); ?>
				 <!-- This will show selected tags -->
				<div class="form-control ref-form-control-multi selectedRecords" id="<?= $fieldname ?>"
					>
				<?= $refval;?>	
				</div>

				<?php if ($classarray['readonly'] !== 'readonly' && $classarray['readonly'] !=1) { ?>
							<!-- Search Icon on the Right 3-->
							<svg class="icon-right search-icon plus-icon openPopupBtn" width="15" height="15" viewBox="0 0 24 25" fill="none"
								xmlns="http://www.w3.org/2000/svg" data-toggle="modal" data-target="#myModalMulti" role="button"
								aria-hidden="true" tabindex="0" 
								id="showMultiCustomer1"
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

					<?php
				} ?>
					</div>




				</div>
	<?php } elseif ($field["uitype"] == 27 && $visible == 0) { ?>
				<di>
				<?php
				$relatedmod_tabid = $field["related_mod"];
				$fieldname = $field["columnname"];
				$fieldname1 = $field["columnname"] . "1";
				$fieldname2 = $field["columnname"] . "2";
				$fieldname3 = $field["columnname"] . "3";
				$model1 = new Reference($TableName, $FieldId);
				$relatedmodulename = $model1->getRelatedNoduleName($field["fieldid"]);
				$getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldName($field["fieldid"]);

				$ref_hid_value = isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '';

				if (isset($Record->{$field["columnname"]}) && $Record->{$field["columnname"]} != '')
					$ref_disp_value = $model1->getRefEntityValue($field["fieldid"], $ref_hid_value);
				else
					$ref_disp_value = '';


				$relatedmod = ''; // $field["relatedmodulename"];
				$getRelatedDField = ''; // $field["getRelatedDisplayFieldName"];
		
				?>
					<input class="effect" style="flex-grow:1;" type="hidden" id="<?php echo $fieldname1; ?>"
						name="<?php echo $field["tablename"] . '[' . $field["fieldname"] . ']' ?>" value="<?php echo $ref_hid_value; ?>"
						readonly='readonly'>
			<?php echo Html::{$field["fieldtype"]}('', $ref_disp_value, $classarray); ?>



		<?php } elseif ($field["uitype"] == 28 && $visible == 0) { //conditional reference 
				//check sourceid and source module
		$val = '';
		if (isset($relatedkeys) && isset($sourceid) && isset($sourcemodule)) {
			$sourceModuleToFind = $sourcemodule; // The source_module you're looking for

			// Loop through the array
			foreach ($relatedkeys as $item) {
				if ($item['source_module'] == $sourceModuleToFind) {
					// If the source_module matches, return the related_recordfieldname
					$related_recordfieldnme = $item['related_recordfieldnme'];
					break; // Stop the loop once we find the match
				}
			}
			if ($related_recordfieldnme == $field["columnname"]) {
				$val = $sourceid;
				$classarray['readonly'] = 'readonly';
			}

		}
		?>
					<div>
					<?php
					$relatedmod_tabid = $field["related_mod"];
					$fieldname = $field["columnname"];
					$fieldname1 = $field["columnname"] . "1";
					$fieldname2 = $field["columnname"] . "2";
					$fieldname3 = $field["columnname"] . "3";
					$model1 = new Reference($TableName, $FieldId);
					$relatedmodulename = $model1->getRelatedNoduleName($field["fieldid"]);
					$getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldName($field["fieldid"]);
					$getRelatedConditionFieldName = $model1->getRelatedConditionFieldName($field["fieldid"]);

					// $ref_hid_value = isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '';

					// if (isset($Record->{$field["columnname"]}) && $Record->{$field["columnname"]} != '')
					// 	$ref_disp_value = $model1->getRefEntityValue($field["fieldid"], $ref_hid_value);
					// else
					// 	$ref_disp_value = '';

					$ref_hid_value = isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : $val;

				if ($ref_hid_value != '')
					$ref_disp_value = $model1->getRefEntityValue($field["fieldid"], $ref_hid_value);
				else
					$ref_disp_value = '';


					$relatedmod = ''; // $field["relatedmodulename"];
					$getRelatedDField = ''; // $field["getRelatedDisplayFieldName"];
			
					?>



						<div class="vendor-input-wrapper">
						<?php if ($classarray['readonly'] !== 'readonly'  && $classarray['readonly'] !=1) { ?>
							<!-- Cross Icon on the Left 1 -->
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
							<?php
				} ?>

							<input class="effect" style="flex-grow:1;" type="hidden" id="<?php echo $fieldname1; ?>"
								name="<?php echo $field["tablename"] . '[' . $field["fieldname"] . ']' ?>"
								value="<?php echo $ref_hid_value; ?>" readonly='readonly'>
					<?php echo Html::{$field["fieldtype"]}('', $ref_disp_value, $classarray); ?>

					<?php if ($classarray['readonly'] !== 'readonly'  && $classarray['readonly'] !=1) { ?>
							<!-- Search Icon on the Right -->
							<svg class="icon-right search-icon plus-icon" width="15" height="15" viewBox="0 0 24 25" fill="none"
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
							<!-- <svg class="icon-right plus-icon" fill="#2F80ED" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
								width="15" height="15" role="button" tabindex="0" onclick="addVendor('<? //$fieldname1 ?>','<? //$fieldname ?>','<? //$getRelatedDisplayFieldName; ?>','<? //$relatedmodulename; ?>',<? //$field['fieldid'];
										; ?>)" aria-label="Add vendor">
								<path d="M12 5v7H5v2h7v7h2v-7h7v-2h-7V5z"></path>
							</svg> -->
							<?php
				} ?>
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
					$fieldname = $field["columnname"];
					$fieldname1 = $field["columnname"] . "1";
					$fieldname2 = $field["columnname"] . "2";
					$fieldname3 = $field["columnname"] . "3";
					//get relatedkey 
					$relatedmod_arr = [];

					if (isset($relatedkeys)) {
						// Initialize an empty array to store source_module values
			
						// Loop through the array and collect the source_module values
						foreach ($relatedkeys as $item) {
							$relatedmod_arr[] = $item['source_module'];
						}
					}

					// $relatedmod_arr = explode(",", $field["related_mod"]);
					$model1 = new Reference($TableName, $FieldId);
					$i = 0;
					$onclick1 = '';
					$relatedvalue = '';
					foreach ($relatedmod_arr as $value) {
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
								$relatedvalue = $value;
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
						if (!empty($sourcemodule))
							$ref_disp_value = $model1->getRefEntityValue($field["fieldid"], $ref_hid_value, $sourcemodule);
						else
							$ref_disp_value = $model1->getRefEntityValue($field["fieldid"], $ref_hid_value, $relatedvalue);
					} else
						$ref_disp_value = '';
					?>



						<div class="vendor-input-wrapper">
						<?php
						// Check if 'readonly' is not equal to 'readonly' (simplified condition)
						if ($classarray['readonly'] !== 'readonly'  && $classarray['readonly'] !=1): ?>
								<!-- Cross Icon on the Left -->
								<svg class="icon-left" fill="#2F80ED" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="15"
									height="15" role="button" tabindex="0"
									id="removeTextValue" data-fieldname1="<?= $fieldname1 ?>" data-fieldname="<?= $fieldname ?>"aria-label="Remove vendor">
									<path
										d="M4.7070312 3.2929688 L3.2929688 4.7070312 L10.585938 12 L3.2929688 19.292969 L4.7070312 20.707031 L12 13.414062 L19.292969 20.707031 L20.707031 19.292969 L13.414062 12 L20.707031 4.7070312 L19.292969 3.2929688 L12 10.585938 L4.7070312 3.2929688 Z">
									</path>
								</svg>
					<?php endif; ?>

							<input class="effect" style="flex-grow:1;" type="hidden" id="<?php echo $fieldname1; ?>"
								name="<?php echo $field["tablename"] . '[' . $field["fieldname"] . ']' ?>"
								value="<?php echo $ref_hid_value; ?>" readonly='readonly'>
					<?php if (is_array($classarray)) {
						$classarray["fieldid"] = $field["fieldid"];
					} ?>
					<?php echo Html::{$field["fieldtype"]}('', $ref_disp_value, $classarray); ?>

						<?php
						// Check if 'readonly' is not equal to 'readonly' (simplified condition)
						if ($classarray['readonly'] !== 'readonly'  && $classarray['readonly'] !=1): ?>
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
		?>
		<!-- added by deepika on 3 Apr 2026 tckling checkbox submits -->
				<?php
				if(!$readonly)
					{?>
				<input type="hidden" name="<?= $field["tablename"] . '[' . $field["fieldname"] . ']'?>" value="0">
				<?php
					}
	
				if (isset($Record->{$field["columnname"]})) {
					if ($Record->{$field["columnname"]} == 1)
						$checked = 1;
					else if ($Record->{$field["columnname"]} == 0)
						$checked = 0;
				} else
					$checked = '';
				if ($mandatory == 1) {
					echo Html::{$field["fieldtype"]}(
						$field["tablename"] . '[' . $field["fieldname"] . ']', // Name attribute
						$checked ? true : false, // Checked state
						[
							'id' => $field["fieldname"],
							'disabled' => $readonly ? true : false,
							"class" => "form-check-input " . $typeofdata . " " . $field["classname"] . " " . $read, // Optional custom CSS class
		
							'data-pristine-required' => 'true', // Custom attribute
							'data-pristine-required-message' => $field["fieldlabel"] . ' is required ', // Custom validation message
							'title' => !empty($field["description"]) ? $field["description"] : $field["fieldlabel"],
							'label' => Html::tag('span', $field["fieldlabel"] . ' <span class="red">*</span>', [
										'title' => !empty($field["description"]) ? $field["description"] : $field["fieldlabel"], // Title attribute
									])

						]
					);
				} else {

					echo Html::{$field["fieldtype"]}(
						$field["tablename"] . '[' . $field["fieldname"] . ']', // Name attribute
						$checked ? true : false, // Checked state
						// [
						// 	'id' => $field["fieldname"],
						// 	'disabled' => $readonly ? true : false,
						// 	"class" => "form-check-input " . $typeofdata . " " . $field["classname"] . " " . $read, // Optional custom CSS class
		
						// 	"title" => !empty($field["description"]) ? $field["description"] : $field["fieldlabel"],
						// 	'label' => Html::tag('span', $field["fieldlabel"], [
						// 				'title' => !empty($field["description"]) ? $field["description"] : $field["fieldlabel"], // Title attribute
		
						// 			])
						// ]
						
						//rsolve issue of is_admin check uncheck issue by ptpatel
						array_merge([
							'id' => $field["fieldname"],
							'disabled' => $readonly ? true : false,
							"class" => "form-check-input " . $typeofdata . " " . $field["classname"] . " " . $read, // Optional custom CSS class
		
							"title" => !empty($field["description"]) ? $field["description"] : $field["fieldlabel"],
							'label' => Html::tag('span', $field["fieldlabel"], [
										'title' => !empty($field["description"]) ? $field["description"] : $field["fieldlabel"], // Title attribute
		
									])
						],
							$field["fieldid"] == 1307 ? ['uncheck' => 0] : [] //  add condition here
						)
						//end code added by ptpatel
					);
				}
			} elseif ($field["uitype"] == 53) //hiden
			{
				echo Html::hiddenInput($field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : Yii::$app->user->id, [

					'id' => $field["fieldname"],
				]);
			} elseif ($field["uitype"] == 70) //hiden
			{
				echo Html::hiddenInput($field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : date("Y-m-d H:i:s"), [

					'id' => $field["fieldname"],
				]);
			} else if ($field["uitype"] == 11) //hiden
			{
				echo Html::hiddenInput($field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);
			} else if($visible == 0 && $field["fieldname"] != "salutation") {
				echo "work in progress for XX uitype -- " . $field["uitype"]. $field["fieldname"] ;
			}
			//}counter if
		
			if ($field["uitype"] != 2  && $field["columnname"] != 'salutation' && $visible == 0) {
				echo '<div class="help-block"></div>';

				$counter++;
			}
} ?>


	<?php
	// echo $block->blocklabel;
	if($TabId == 18 && $block->blockid == 149)
	{
		$role = "deshwal";
	include("DynamicSingleTwoCol.php");
	}
	if($TabId == 18 && $block->blockid == 150)
	{
		//if condition added by ptpatel on date 08-04-25 finanace manager profile wan't show OEM MANAGER DETAILS block
		 if($roleId != "H19"){ //H19 if finance manager roleid
				$role = "oem";
			include("DynamicSingleTwoCol.php");
		 }
	}
	else{
		
		?>
	
		</div>
		<!-- close col-lg-3 col-md-6 -->
		</div>
		<!-- close row -->
		 <?php
	}
	
	?>
	
