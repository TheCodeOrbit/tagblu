<?php
use yii\helpers\Html;
use common\models\Fieldtype;
use backend\models\AccessCheck;
use common\models\Picklist;
use common\models\Multilist;
use app\models\Reference;
use app\models\ListHire;
use backend\assets\AdminAsset;
$baseUrl = Yii::$app->HomeUrl;

?>
<style type="text/css">

</style>
<!-- <link src='@web/thememain/css/simpletwocol.css'> -->
<link rel="stylesheet" href="<?= $baseUrl; ?>/thememain/css/simpletwocol.css">
<?php
// $scriptPath=$baseUrl."js/$ModuleName/Edit.js";
// $this->registerJsFile($scriptPath, ['depends' => [AdminAsset::class]]);
// $this->registerJsFile('@web/theme/libs/pristinejs/pristinejs.min.js', ['depends' => [AdminAsset::class]]);
// $this->registerJsFile('@web/theme/libs/theme/js/pages/form-validation.init.js', ['depends' => [AdminAsset::class]]);


// echo '<div class="form-row"><!--open first row--><div class="form-group  form-field-cst"><!--open first col-->';
echo "<div class='form-container-cst " . $cls . "'>";
$counter = 0;
$relationName = $action_name === 'create' ? 'createfields' : 'editfields';

foreach ($block->$relationName as $field) {

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
		else if ($field['fieldname'] == 'ownerid')
			$field["fieldoptions"] = $PickList->getusers($field['fieldname'], $field['uitype'], Yii::$app->user->id);
		else
			$field["fieldoptions"] = $PickList->getPickListOption($ModuleName);
		if ($ModuleName == "leads" && $field["fieldname"] == "leadstatus") {
			//print_r($Record['vertical_manager']);die;
			// if ($ActionName == "Create") {
				//limit  fieldoptons
				// Keys to remove
				$keysToRemove = [3, 13, 5,6];
				$arraykey = $field["fieldoptions"];
				// Remove elements
				$field["fieldoptions"] = array_diff_key($field["fieldoptions"], array_flip($keysToRemove));

				// print_r($field["fieldoptions"]);die;
			// } else if ($ActionName == "Edit" && $Record["vertical_manager"] == Yii::$app->user->id) {
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
		// $Column->blocks[$BlockKey]->fields[$FieldKey]->fieldoptions=$PickList->getMultiListOption($table_name);


		$field["fieldoptions"] = $PickList->getMultiListOption($ModuleName);
	}
	//print_r($FieldTypeRecord);die;
	if ($field["uitype"] != 2 && $field["fieldid"] != 19 && $field["uitype"] != 11 && $field["uitype"] != 70 && $field["uitype"] != 53 && $visible == 0) {
		if ($field["mandatory"] == 1)
			$clss = "required-field";
		else
			$clss = 'not-required-field';

		if ($counter == 0) {
			echo '<div class="form-field-cst form-group ' . $clss . ' form-field-cst section-' . $field["columnname"] . '"><!--open first col-->';
		}
		//close/open divs
		if ($counter > 0) {
			if ($counter % 2 == 0)
				echo '</div><!-- close form group--></div><!-- close col6 div-->
					<div class="form-group   form-field-cst section-' . $field["columnname"] . ' inner ' . $clss . '"><div class=" field-uiinputs-' . $field["fieldname"] . '">';
			else
				echo '</div><!-- close form group--></div><!-- close col6 div--><div class="form-group   form-field-cst section-' . $field["columnname"] . ' inner ' . $clss . '"><div class="field-uiinputs-' . $field["fieldname"] . '">';
		} else {
			// for first col 6
			echo '<div class="   field-uiinputs-' . $field["fieldname"] . ' ' . $clss . '">';
		}




	}
	if ($field["uitype"] == 12 || $field["uitype"] == 26 || $field["uitype"] == 28) {
		$popupclass = "ref-form-control";
	} else {
		$popupclass = '';
	}

	//check mandatory
	if ($field["mandatory"] == 1) {

		//echo $field["maximumlength"];
		$classarray = array(
			'class' => 'form-control ' . $popupclass . ' ' . $field["typeofdata"],
			'id' => $field["fieldname"],
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
			'class' => $popupclass . ' form-control ' . $readonly . ' ' . $field["typeofdata"],
			'readonly' => $readonly ? true : false,
			'id' => $field["fieldname"],
			'maxlength' => $field["maximumlength"],
			"value" => isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : ""
		);
		$mandatory = "";
	}


	?>

	<?php if ($field["uitype"] != 2 && $field["fieldid"] != 19 && $field["uitype"] != 11 && $field["uitype"] != 70 && $field["uitype"] != 53 && $field["uitype"] != 6 && $visible == 0) { //show label if not hiden type and not radio type
		
				echo Html::label($field["fieldlabel"] . $mandatory, $field["fieldname"], ['class' => 'control-label ']);
			}

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
			  padding: 2px;" name=' . $field["tablename"] . '[salutation]">
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
						Html::{$field["fieldtype"]}($field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray) . '
			                                    </div>
			                                </div>';
				} else {
					echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);
				}
			} else if ($field['uitype'] == 16) {
				$classarray['class'] = $classarray['class'] . ' timepicker';
				// print_r($classarray);die;
				include 'uitype/duration.php';
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
					// echo $MRecords->name;die;
					$filepath = "<br><a href='" . $baseUrl . $ModuleName . "/download?fileid=" . $Record->{$field["columnname"]} . "'>" . $MRecords->name . "</a>";
					// $filepath = $MRecords->name;
					$classarray['class'] = 'form-control';
					echo "<input type='hidden' name='" . $field["columnname"] . "_hiddenfile' value='" . $MRecord->{$field["columnname"]} . "'>";
				} else
					$filepth = '';

				echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);
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
					else if ($field["fieldname"] == 'ownerid')
					$val = Yii::$app->user->id;
					else
						$val = $sourceid;
					echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $field["fieldname"] . ']', $val, $classarray);
				} else
					echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);
			} elseif ($field["uitype"] == 4 && $visible == 0) //text area
			{
				echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);
			} elseif ($field["uitype"] == 8 && $visible == 0 && $field["fieldid"] != 19) //simgle drop down
			{
				if ($field["columnname"] == "ownerid") {

					// echo Html::{$field["fieldtype"]}(
					// 	$field["tablename"] . '[' . $field["fieldname"] . ']',
					// 	isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : Yii::$app->user->id,
					// 	$field["fieldoptions"],
					// 	['class' => 'form-control ' . $field["typeofdata"] . ' ' . $read, 'id' => $field["fieldname"], "prompt" => "Select " . $field["fieldlabel"], 'data-pristine-required' => 'true', 'data-pristine-required-message' => $field["fieldlabel"] . ' is required ',]
					// );
					// added on 08 jan 2025 by deepika check if admin then only shw dropdown
					// if ($hasadminpower === 1)
					// 	$classarray['class'] = $classarray['class'] . 'singleselect ';
					// else
					// 	$classarray['class'] = $classarray['class'] . ' readonly-dd';
						$classarray['class'] = $classarray['class'] . 'singleselect ';


					echo $this->render('dropdown', ['baseUrl' => $baseUrl, 'classarray' => $classarray, 'field' => $field, 'fieldoptions' => $field["fieldoptions"], 'Record' => isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : Yii::$app->user->id, 'readonly' => $readonly, 'read' => $read]);
				} else {
					// print_r($field["fieldoptions"]);
					$classarray['class'] = $classarray['class'] . ' singleselect';

					echo $this->render('dropdown', ['baseUrl' => $baseUrl, 'classarray' => $classarray, 'field' => $field, 'fieldoptions' => $field["fieldoptions"], 'Record' => isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '', 'readonly' => $readonly, 'read' => $read]);
					// if ($field["mandatory"] == 1) {
		
					// 	echo Html::{$field["fieldtype"]}(
					// 		$field["tablename"] . '[' . $field["fieldname"] . ']',
					// 		isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '',
					// 		$field["fieldoptions"],
					// 		['class' => 'form-control ' . $field["typeofdata"] . ' ' . $read, 'id' => $field["fieldname"], "prompt" => "Select " . $field["fieldlabel"], 'data-pristine-required' => 'true', 'data-pristine-required-message' => $field["fieldlabel"] . ' is required ']
					// 	);
					// } else {
					// 	echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '', $field["fieldoptions"], ['class' => 'form-control ' . $field["typeofdata"] . ' ' . $read, 'id' => $field["fieldname"], "prompt" => "Select " . $field["fieldlabel"]]);
					// }
				}
			} elseif ($field["uitype"] == 9 && $visible == 0) //checkboxlist
			{
				// print_r($field["fieldoptions"]);
				//print_r( $Record->{$field["columnname"]});
				if (isset($Record->{$field["columnname"]}))
					$selectedValues = explode(',', $Record->{$field["columnname"]});
				else
					$selectedValues = '';
				if ($field["mandatory"] == 1) {
					echo Html::checkboxList(
						$field["tablename"] . '[' . $field["fieldname"] . ']', // Name for the checkbox list
						$selectedValues, // Array of selected values
						$field["fieldoptions"], // Array of options (value => label)
						[
							'item' => function ($index, $label, $name, $checked, $value) use ($field, $read) {
								return Html::tag(
									'label', // Wrap the checkbox and label in a <label> element
									Html::checkbox($name, $checked, [
										'value' => $value,
										'class' => 'form-check-input ' . $field["typeofdata"] . ' ' . $read, // Custom class for checkbox
										'id' => $field["fieldname"] . '_' . $index, // Unique ID for each checkbox
										'data-pristine-required' => 'true',
										'data-pristine-required-message' => $label . ' is required ',
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
							'item' => function ($index, $label, $name, $checked, $value) use ($field, $read) {
								return Html::tag(
									'label', // Wrap the checkbox and label in a <label> element
									Html::checkbox($name, $checked, [
										'value' => $value,
										'class' => 'form-check-input ' . $field["typeofdata"] . ' ' . $read, // Custom class for checkbox
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
		
				if ($field["mandatory"] == 1) {
					echo Html::radioList(
						$field["tablename"] . '[' . $field["fieldname"] . ']', // Name for the radio button list
						isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '', // Selected value
						$field["fieldoptions"], // Array of options (value => label)
						[
							'item' => function ($index, $label, $name, $checked, $value) use ($field, $read) {
								return Html::tag(
									'label', // Wrap the radio button and label in a <label> element
									Html::radio($name, $checked, [
										'value' => $value,
										'class' => 'form-check-input ' . $field["typeofdata"] . ' ' . $read, // Custom class for radio button
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
					echo Html::radioList(
						$field["tablename"] . '[' . $field["fieldname"] . ']', // Name for the radio button list
						isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '', // Selected value
						$field["fieldoptions"], // Array of options (value => label)
						[
							'item' => function ($index, $label, $name, $checked, $value) use ($field, $read) {
								return Html::tag(
									'label', // Wrap the radio button and label in a <label> element
									Html::radio($name, $checked, [
										'value' => $value,
										'class' => 'form-check-input ' . $field["typeofdata"] . ' ' . $read, // Custom class for radio button
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
				// Output the multi-list (above HTML)
				if (isset($Record->{$field["columnname"]})) {
					$modellist = new Listhire;
					$Recordlist = $modellist->getPickListDetailMultiplewitval($field['fieldid'], $Record->{$field["columnname"]});
					// print_r($Recordlist);die;
				} else
					$Recordlist = '';
				if (isset($Record->{$field["columnname"]}))
					$selectedValues = explode(',', $Record->{$field["columnname"]});
				else
					$selectedValues = '';
				echo $this->render('multi_list', ['baseUrl' => $baseUrl, 'classarray' => $classarray, 'Recordlist' => $Recordlist, 'field' => $field, 'fieldoptions' => $field["fieldoptions"], 'Record' => isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '', 'selectedValues' => $selectedValues, 'readonly' => $readonly]);

				// Include the HTML structure defined earlier
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

				if ($field["mandatory"] == 1) {
					echo Html::{$field["fieldtype"]}(
						$field["tablename"] . '[' . $field["fieldname"] . ']',
						isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : $val,
						$options,
						[
							'class' => 'form-control ' . $field["typeofdata"] . " " . $read,
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
							'class' => 'form-control ' . $field["typeofdata"] . " " . $read,
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
				if ($field["mandatory"] == 1) {

					echo Html::{$field["fieldtype"]}(
						$field["tablename"] . '[' . $field["fieldname"] . ']',
						isset($Record->{$field["columnname"]}) ? explode(',', $Record->{$field["columnname"]}) : '',
						$field["fieldoptions"],
						[
							'id' => $field["fieldname"],
							'class' => 'form-control ' . $field["typeofdata"] . " " . $read,
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
							'class' => 'form-control ' . $field["typeofdata"] . " " . $read,
							"prompt" => "Select " . $field["fieldlabel"],
							'multiple' => true,

						]
					);
				}
			} elseif ($field["uitype"] == 13 && $visible == 0) {
				// include "uitype/Date.php";
				echo Html::input($field["fieldtype"], $field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);
			} elseif ($field["uitype"] == 17 && $visible == 0) {
				// include "uitype/Date.php";
				echo Html::input($field["fieldtype"], $field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);
			} elseif ($field["uitype"] == 12 && $visible == 0) {
				if($field['columnname'] == 'contacts_id')
				{
					?>
				 <input type="text" id="search" class="form-control" autocomplete="off" placeholder="Start typing to search...">
				 
				 <div id="search-results"></div>
				 <br>
				<?php

				}
				// else{
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


				$relatedmod = '';// $field["relatedmodulename"];
				$getRelatedDField = '';// $field["getRelatedDisplayFieldName"];
		
				?>



					<div class="vendor-input-wrapper">
				<?php if ($classarray['readonly'] !== 'readonly') { ?>
							<!-- Cross Icon on the Left -->
							<svg class="icon-left icon-left-contactrole" fill="#2F80ED" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="15"
								height="15" role="button" tabindex="0" onclick="removeTextValue(<?= $fieldname1; ?>,<?= $fieldname; ?>);"
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
				<?php if (is_array($classarray)) {
					$classarray["fieldid"] = $field["fieldid"];
				} 
				$classarray['readonly'] = 'readonly'?>
				<?php echo Html::{$field["fieldtype"]}('', $ref_disp_value, $classarray); ?>

				
					</div>





				</div>
	<?php  //}
 } elseif ($field["uitype"] == 27 && $visible == 0) { ?>
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

				$ref_hid_value = isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '';

				if (isset($Record->{$field["columnname"]}) && $Record->{$field["columnname"]} != '')
					$ref_disp_value = $model1->getRefEntityValue($field["fieldid"], $ref_hid_value);
				else
					$ref_disp_value = '';


				$relatedmod = '';// $field["relatedmodulename"];
				$getRelatedDField = '';// $field["getRelatedDisplayFieldName"];
		
				?>
					<input class="effect" style="flex-grow:1;" type="hidden" id="<?php echo $fieldname1; ?>"
						name="<?php echo $field["tablename"] . '[' . $field["fieldname"] . ']' ?>" value="<?php echo $ref_hid_value; ?>"
						readonly='readonly'>
			<?php echo Html::{$field["fieldtype"]}('', $ref_disp_value, $classarray); ?>



		<?php } elseif ($field["uitype"] == 28 && $visible == 0) { //conditional reference ?>
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
								id="removeTextValue" data-fieldname1="<?= $fieldname1 ?>" data-fieldname="<?= $fieldname ?>" aria-label="Remove vendor">
								<path
									d="M4.7070312 3.2929688 L3.2929688 4.7070312 L10.585938 12 L3.2929688 19.292969 L4.7070312 20.707031 L12 13.414062 L19.292969 20.707031 L20.707031 19.292969 L13.414062 12 L20.707031 4.7070312 L19.292969 3.2929688 L12 10.585938 L4.7070312 3.2929688 Z">
								</path>
							</svg>

							<input class="effect" style="flex-grow:1;" type="hidden" id="<?php echo $fieldname1; ?>"
								name="<?php echo $field["tablename"] . '[' . $field["fieldname"] . ']' ?>"
								value="<?php echo $ref_hid_value; ?>" readonly='readonly'>
					<?php echo Html::{$field["fieldtype"]}('', $ref_disp_value, $classarray); ?>


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
						</div>




					</div>
		<?php } elseif ($field["uitype"] == 26 && $visible == 0) {
				if ($field["fieldname"] == 'related_to_id') {
					$readonly = 1;
					$val = $sourceid;
					// echo $sourcemodule;die;
					if (!empty($sourcemodule) && !empty($sourceid)) {
						$sourcemodule = htmlspecialchars($_REQUEST['sourcemodule']);
						$sourceid = htmlspecialchars($_REQUEST['sourceid']);

					}
					if (isset($Record['related_to'])) {
						$sourcemodule = $Record['related_to'];
					}
					// print_r($classarray);
					if (!empty($sourceid)) {
						if (isset($classarray['readonly']))
							$classarray['readonly'] = 'readonly';
						else
							array_push($classarray['readonly'], 'readonly');
					}


					//get modulename
					// Fetch module record
					$module = \app\models\Field::find()
						->where(['tabid' => $sourcemodule])
						->one();
					// print_r($module);die;
		
					// Check if a record was found
					if ($module !== null) {
						$reftablename = $module->tablename; // Use -> for accessing attributes in ActiveRecord
					} else {
						$reftablename = null; // Handle the case where no record was found
						// Optionally, log or throw an error here
						//throw new \Exception("No module found for tabid: {$sourcemodule}");
					}
					//   print_r($module);die;
		
				} else {
					$val = '';
					$reftablename = '';
				}
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
					// print_r($relatedmod_arr);die;
					$model1 = new Reference($TableName, $FieldId);
					$i = 0;

					foreach ($relatedmod_arr as $key => $value) {
						# code...
						if (!empty($value)) {
							$relatedmod_tabid = $value;
							if ((!empty($Record['related_to']) && $value == $Record['related_to']) || $i == 0) {
								// echo "deepika";
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
					// echo $onclick1;die;
					//echo $val;
					$ref_hid_value = isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : $val;
					// echo "select targettable,entityidfield,fieldname from entityname where fieldid=".$field["fieldid"];die;
			

					if ($ref_hid_value) {
						$ref_disp_value = $model1->getMultiRefEntityValue($field["fieldid"], $ref_hid_value, $reftablename);
					} else
						$ref_disp_value = '';
					?>



						<div class="vendor-input-wrapper">
						<?php
						// Check if 'readonly' is not equal to 'readonly' (simplified condition)
						if ($classarray['readonly'] !== 'readonly'): ?>
								<!-- Cross Icon on the Left -->
								<svg class="icon-left" fill="#2F80ED" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="15"
									height="15" role="button" tabindex="0"
									id="removeTextValue" data-fieldname1="<?= $fieldname1 ?>" data-fieldname="<?= $fieldname ?>" aria-label="Remove vendor">
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
						// onclick="<?php echo $onclick1 ?? ""; ?"
						if ($classarray['readonly'] !== 'readonly'): ?>
								<!-- Search Icon on the Right -->
								<svg class="icon-right related-search-icon search-icon" width="15" height="15" viewBox="0 0 24 25" fill="none"
									xmlns="http://www.w3.org/2000/svg" data-toggle="modal" data-target="#myModal22" role="button"
									aria-hidden="true" tabindex="0"  data-onrefclick="<?= $onclick1 ?>" aria-label="Search vendor">
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
				if ($field["mandatory"] == 1) {
					echo Html::{$field["fieldtype"]}(
						$field["tablename"] . '[' . $field["fieldname"] . ']', // Name attribute
						$checked ? true : false, // Checked state
						[
							'disabled' => $readonly ? true : false,
							"class" => "form-check-input " . $field['typeofdata'] . " " . $field["classname"] . " " . $read, // Optional custom CSS class
							"label" => $field["fieldlabel"] . ' <span class="red">*</span>', // Label for the checkbox
							'data-pristine-required' => 'true', // Custom attribute
							'data-pristine-required-message' => $field["fieldlabel"] . ' is required ' // Custom validation message
						]
					);
				} else {

					echo Html::{$field["fieldtype"]}(
						$field["tablename"] . '[' . $field["fieldname"] . ']', // Name attribute
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
			} else if($visible == 0) {
				echo "work in progress for XX uitype -- " . $field["uitype"];
			}
			//}counter if
		
			if ($field["uitype"] != 2 && $field["uitype"] != 11 && $field["fieldid"] != 19) {
				echo '<div class="help-block"></div>';

				$counter++;
			}

}
echo "</div><!-- close form group--></div></div><!--close last row-->";
// echo "</div>";

?>

	<!-- <script type="text/javascript" src="<?= $scriptPath ?>"></script>
<script type="text/javascript" src="<?= $baseUrl; ?>theme/libs/pristinejs/pristinejs.min.js"></script> -->
	<!-- <script type="text/javascript" src="<?= $baseUrl; ?>theme/js/pages/form-validation.init.js"></script> -->