<?php
use yii\helpers\Html;
use common\models\Fieldtype;
use backend\models\AccessCheck;
use common\models\Multilist;
use common\models\Picklist;
use app\models\Reference;
use backend\assets\AdminAsset;

?>
<style type="text/css">
	.red {
		color: #f00;
	}

	/*for reference model*/
	.vendor-input-wrapper {
		position: relative;
		display: flex;
		align-items: center;
	}

	.ref-form-control {
		width: 100%;
		padding-left: 30px;
		/* Space for the left icon */
		padding-right: 60px;
		/* Space for the two right icons */
	}

	.icon-left,
	.icon-right {
		position: absolute;
		cursor: pointer;
	}

	.icon-left {
		left: 10px;
	}

	.search-icon {
		right: 30px;
		/* Position search icon closer to the text */
	}

	.plus-icon {
		right: 10px;
		/* Position plus icon at the far right */
	}

	.form-container-cst {
		display: flex;
		flex-wrap: wrap;
		gap: 16px;
		/* Spacing between fields */
		justify-content: flex-start;
		/* Align fields at the start */
	}

	.form-field-cst {
		flex: 1 1 calc(50% - 16px);
		/* Default to 50% width */
		max-width: calc(50% - 16px);
		/* Prevent from going beyond 50% */
		min-width: 200px;
		/* Ensure a reasonable minimum width */
	}

	@media (max-width: 768px) {
		.form-field-cst {
			flex: 1 1 100%;
			/* Full width on small screens */
		}
	}

	.opt-none:hover {
		background: none !important;

	}
</style>

<?php
$baseUrl = Yii::$app->HomeUrl;
$scriptPath = $baseUrl . "js/$ModuleName/Edit.js";
$this->registerCssFile('@web/thememain/css/listview.css', ['depends' => [AdminAsset::class]]);
// $this->registerCssFile('@web/thememain/js/custom.js', ['depends' => [AdminAsset::class]]);
$this->registerJsFile($scriptPath, ['depends' => [AdminAsset::class]]);
$this->registerJsFile('@web/theme/libs/pristinejs/pristinejs.min.js', ['depends' => [AdminAsset::class]]);
$this->registerJsFile('@web/theme/libs/theme/js/pages/form-validation.init.js', ['depends' => [AdminAsset::class]]);
$relationName = 'masseditfields';

?>
<div class="modal-header">
	<h5 class="modal-title" id="exampleModalLabel">Bulk Update</h5>
	<button type="button" class="btn-close update-close-btn" aria-label="Close"></button>
</div>
<div class="modal-body">
	<!-- Dropdown to Select Field -->
	<div class="update-field-name">
		<label for="updatefiled_names">Select Field to Update</label>
		<select name="updatefiled_names" id="updatefiled_names" class="form-control">
			<option value="">Select field</option>
			<?php foreach ($ColumnList->blocks as $block) {
				//print_r($block->$relationName);die;
				if (!empty($block->$relationName)) {
					foreach ($block->$relationName as $field): ?>
						<option value="<?php echo $field['columnname']; ?>"><?php echo $field['fieldlabel']; ?></option>
					<?php endforeach;
				}
			} ?>
		</select>
	</div>

	<!-- Input Field (Dynamic) -->
	<div id="field-input-container">
		<label id="field-label" for="field-input"></label>

		<?php

		$clsshr = 'tr-hidden';
		// echo '<div class="form-row"><!--open first row--><div class="form-group  form-field-cst"><!--open first col-->';
// echo "<div class='form-container-cst '>";
		$counter = 0;
		foreach ($ColumnList->blocks as $block) {
			//print_r($block->$relationName);die;
			if (!empty($block->$relationName)) {
				foreach ($block->$relationName as $field) {
					?>
					<div class="field-continer fieldid_<?= $field["columnname"] . " " . $clsshr; ?> ">

						<?php
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
							}
						}
						//else echo $permission; exit();
						if ($field["uitype"] != 70 && $visible == 0) {
							$FieldTypeRecord = Fieldtype::find()->where(['uitype' => $field["uitype"]])->one();
							$field["fieldtype"] = $FieldTypeRecord['getfieldtype'];
							$field["classname"] = $FieldTypeRecord['classname'];
						}


						if ($field["uitype"] == 8 && $visible == 0) {
							$PickList = new Picklist;
							$PickList->fieldid = $field["fieldid"];
							// $Column->blocks[$blockKey]->fields[$FieldKey]->fieldoptions=$PickList->getPickListOption($table_name);
			

							$field["fieldoptions"] = $PickList->getPickListOption($ModuleName);
							// if($ModuleName == "leads" && $field["fieldname"] == "leadstatus")
							// {
							// 	//print_r($Record['vertical_manager']);die;
							// 	if($Record["vertical_manager"]==Yii::$app->user->id)
							// 	{
							// 		//limit  fieldoptons
							// 		// Keys to remove
							// 		$keysToRemove = [1,2,4,6,7,8,9,10,11,12];
							// 		$arraykey = $field["fieldoptions"] ;
							// 		// Remove elements
							// 		$field["fieldoptions"] = array_diff_key($field["fieldoptions"], array_flip($keysToRemove));
			
							// 		// print_r($field["fieldoptions"]);die;
			
							// 	}
							// 	else
							// 	{
							// 		//limit  fieldoptons
							// 		// Keys to remove
							// 		$keysToRemove = [3, 13,5];
							// 		$arraykey = $field["fieldoptions"] ;
							// 		// Remove elements
							// 		$field["fieldoptions"] = array_diff_key($field["fieldoptions"], array_flip($keysToRemove));
			
							// 		// print_r($field["fieldoptions"]);die;
							// 	// }
			
							// }
						} else if ($field["uitype"] == 22 && $visible == 0) {
							$PickList = new MultiList;
							$PickList->fieldid = $field["fieldid"];
							// $Column->blocks[$blockKey]->fields[$FieldKey]->fieldoptions=$PickList->getMultiListOption($table_name);
			

							$field["fieldoptions"] = $PickList->getMultiListOption($ModuleName);
						}
						//print_r($FieldTypeRecord);die;
						if ($field["uitype"] != 2 && $field["fieldid"] != 19 && $field["uitype"] != 11 && $field["uitype"] != 70 && $field["uitype"] != 53 && $visible == 0) {
							if ($field["mandatory"] == 1)
								$clss = "required-field ";
							else
								$clss = 'not-required-field';






						}
						if ($field["uitype"] == 12) {
							$popupclass = "ref-form-control";
						} else {
							$popupclass = '';
						}

						//check mandatory
						if ($field["mandatory"] == 1) {

							//echo $field["maximumlength"];
							$classarray = array(
								'class' => 'form-control ' . $popupclass . " " . "",
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
								'class' => $popupclass . ' form-control ' . $readonly . " " . "",
								'readonly' => $readonly ? true : false,
								'id' => $field["fieldname"],
								'maxlength' => $field["maximumlength"],
								"value" => isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : ""
							);
							$mandatory = "";
						}


						?>

						<?php if ($field["uitype"] != 2 && $field["fieldid"] != 19 && $field["uitype"] != 11 && $field["uitype"] != 70 && $field["uitype"] != 53 && $field["uitype"] != 6 && $visible == 0) { //show label if not hiden type and not radio type
						
										//echo Html::label($field["fieldlabel"] . $mandatory, $field["fieldname"], ['class' => 'control-label ']);
									}

									if ($field["uitype"] == 1 && $visible == 0) {
										// if($field["fieldid"] == 8)
										// {
										// 	$PickList = new Picklist;
										// 	$PickList->fieldid = 19;
										// 	// $Column->blocks[$blockKey]->fields[$FieldKey]->fieldoptions=$PickList->getPickListOption($table_name);
						

										// 	$fieldoptions = $PickList->getPickListOption($ModuleName);
										// 	// print_r($fieldoptions);
										// 	// echo $Record['salutation'];die;
										// 	$selesalu = isset($Record['salutation']) ?$Record['salutation']:"";
										// 	echo '<div class="form-group '."".'" >
										//                                     <div class="input-group mb-2">
										//                                         <div class="input-group-prepend">
										//                                             <div class="input-group-text" style="background: none;">
										//                                             <Select style="background: none;
										//   border: none;
										//   padding: 2px;" name='.$field["tablename"] .'[salutation]">
										//   <option class="opt-none">None</option>';
										//   foreach ($fieldoptions as $key => $value) {
										//   	if($selesalu == $key)
										//   		$sel = "selected";
										//   	else $sel = '';
										//   	# code...
										//   	echo  '<option class="opt-none" '.$sel.' value="'.$key.'">'.$value.'</option>';
										//   }
						
										//   echo '</select>
						
										//   </div>
										//                                         </div>'.
										//                                         Html::{$field["fieldtype"]}($field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]}	: "", $classarray).'
										//                                     </div>
										//                                 </div>';
										//  }
										//  else{
										echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);
										//}
									} elseif ($field["uitype"] == 2 && $visible == 0) //hiden
									{
										echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);
									} elseif ($field["uitype"] == 4 && $visible == 0) //text area
									{
										echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);
									} elseif ($field["uitype"] == 8 && $visible == 0) //simgle drop down //$field["fieldid"] != 19==salutation
									{
										if ($field["columnname"] == "ownerid") {
											echo Html::{$field["fieldtype"]}(
												$field["tablename"] . '[' . $field["fieldname"] . ']',
												isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : Yii::$app->user->id,
												$field["fieldoptions"],
												['class' => "" . ' form-control', 'id' => $field["fieldname"], "prompt" => "Select " . $field["fieldlabel"], 'data-pristine-required' => 'true', 'data-pristine-required-message' => $field["fieldlabel"] . ' is required ', 'disabled' => $readonly ? true : false]
											);
										} else {
											// print_r($field["fieldoptions"]);
						
											if ($field["mandatory"] == 1) {

												echo Html::{$field["fieldtype"]}(
													$field["tablename"] . '[' . $field["fieldname"] . ']',
													isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '',
													$field["fieldoptions"],
													['class' => "" . ' form-control', 'id' => $field["fieldname"], "prompt" => "Select " . $field["fieldlabel"], 'data-pristine-required' => 'true', 'data-pristine-required-message' => $field["fieldlabel"] . ' is required ', 'disabled' => $readonly ? true : false]
												);
											} else {
												echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '', $field["fieldoptions"], ['disabled' => $readonly ? true : false, 'class' => "" . ' form-control', 'id' => $field["fieldname"], "prompt" => "Select " . $field["fieldlabel"]]);
											}
										}
									} elseif ($field["uitype"] == 23 && $visible == 0) //for numeric
									{

										// <!-- Label for the text input -->
						
										echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);
									} elseif ($field["uitype"] == 24 && $visible == 0) //multiple drop down
									{
										if ($field["mandatory"] == 1) {

											echo Html::{$field["fieldtype"]}(
												$field["tablename"] . '[' . $field["fieldname"] . ']',
												isset($Record->{$field["columnname"]}) ? explode(',', $Record->{$field["columnname"]}) : '',
												$field["fieldoptions"],
												[
													'id' => $field["fieldname"],
													'class' => "" . ' form-control',
													'disabled' => $readonly ? true : false,
													"prompt" => "Select " . $field["fieldlabel"],
													'data-pristine-required' => 'true',
													'data-pristine-required-message' => $field["fieldlabel"] . ' is required '
												]
											);
										} else {
											echo Html::{$field["fieldtype"]}(
												$field["tablename"] . '[' . $field["fieldname"] . ']',
												isset($Record->{$field["columnname"]}) ? explode(',', $Record->{$field["columnname"]}) : '',
												$field["fieldoptions"],
												[
													'id' => $field["fieldname"],
													'disabled' => $readonly ? true : false,
													'class' => "" . ' form-control',
													"prompt" => "Select " . $field["fieldlabel"]
												]
											);
										}
									} elseif ($field["uitype"] == 13 && $visible == 0) {
										// include "uitype/Date.php";
										echo Html::input($field["fieldtype"], $field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);
									} elseif ($field["uitype"] == 17 && $visible == 0) {
										// include "uitype/Date.php";
										echo Html::input($field["fieldtype"], $field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);
									} elseif ($field["uitype"] == 12 && $visible == 0) { ?>

							<?php
							$relatedmod_tabid = $field["related_mod"];
							$fieldname = $field["columnname"];
							$fieldname1 = $field["columnname"] . "1";
							$fieldname2 = $field["columnname"] . "2";
							$fieldname3 = $field["columnname"] . "3";
							$model1 = new Reference($TableName, $FieldId);
							$relatedmodulename = $model1->getRelatedNoduleName($field["fieldid"]);
							$getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldName($field["fieldid"]);

							// if(
							// 	$ModuleType == "Related" and
							// 	$ActionList["ActionName"] == "Create"
							// ) {
							// 	$Reffields = $ActionList["Reffields"];
							// 	if (
							// 		$field["columnname"] == "customer_id" or
							// 		$field["columnname"] == "customerno"
							// 	) {
							// 		$ref_hid_value = $Reffields["customerid"];
							// 		$ref_disp_value = $Reffields["customername"];
							// 	}
							// } else {
							$ref_hid_value = isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '';

							if (isset($Record->{$field["columnname"]}) && $Record->{$field["columnname"]} != '')
								$ref_disp_value = $model1->getRefEntityValue($field["fieldid"], $ref_hid_value);
							else
								$ref_disp_value = '';
							// }
							// echo $form
							// 	->field($model, $field["fieldname"])
							// 	->hiddenInput([
							// 		"class" => $field["classname"],
							// 		"value" => $ref_hid_value,
							// 	]);
			
							$relatedmod = '';// $field["relatedmodulename"];
							$getRelatedDField = '';// $field["getRelatedDisplayFieldName"];
							// echo $form
							// 	->field($model, $field["fieldname"])
							// 	->hiddenInput([
							// 		"class" => $field["classname"],
							// 		"value" => $relatedmod,
							// 	]);
							// echo $form
							// 	->field($model, $field["fieldname"])
							// 	->hiddenInput([
							// 		"class" => $field["classname"],
							// 		"value" => $getRelatedDField,
							// 	]);
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
								 <!-- onclick="showCustomer1('<? // $fieldname1 ?>','<?// $fieldname ?>','<? // $getRelatedDisplayFieldName; ?>','<? // $relatedmodulename; ?>',<? // $block->blockid;; ?>)" -->
								<svg class="icon-right search-icon" width="15" height="15" viewBox="0 0 24 25" fill="none"
									xmlns="http://www.w3.org/2000/svg" data-toggle="modal" data-target="#myModal22" role="button"
									aria-hidden="true" tabindex="0"
									id="showCustomer1"
                                        data-fieldname1="<?= $fieldname1 ?>"
                                        data-fieldname="<?= $fieldname ?>"
                                        data-display="<?= $getRelatedDisplayFieldName ?>"
                                        data-module="<?= $relatedmodulename ?>"
                                        data-fieldid="<?= $block->blockid ?>"
                                        data-val6=""
                                        data-val7=""
                                        data-val8=""
                                        data-sourcemodule="<?// $sourcemodule ?>"
                                        data-sourceid="<?// $sourceid ?>"
									
									aria-label="Search vendor">
									<path
										d="M21 21.5L16.514 17.006L21 21.5ZM19 11C19 13.2543 18.1045 15.4163 16.5104 17.0104C14.9163 18.6045 12.7543 19.5 10.5 19.5C8.24566 19.5 6.08365 18.6045 4.48959 17.0104C2.89553 15.4163 2 13.2543 2 11C2 8.74566 2.89553 6.58365 4.48959 4.98959C6.08365 3.39553 8.24566 2.5 10.5 2.5C12.7543 2.5 14.9163 3.39553 16.5104 4.98959C18.1045 6.58365 19 8.74566 19 11V11Z"
										stroke="#2F80ED" stroke-width="2" stroke-linecap="round"></path>
								</svg>

								<!-- Plus Icon on the Right -->
								<svg class="icon-right plus-icon" fill="#2F80ED" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
									width="15" height="15" role="button" tabindex="0"
									onclick="addVendor('<?= $fieldname1 ?>','<?= $fieldname ?>','<?= $getRelatedDisplayFieldName; ?>','<?= $relatedmodulename; ?>',<?= $block->blockid;
											; ?>)"
									aria-label="Add vendor">
									<path d="M12 5v7H5v2h7v7h2v-7h7v-2h-7V5z"></path>
								</svg>
							</div>



						<?php } elseif ($field["uitype"] == 6 && $visible == 0) { //checkbox
										if ($field["mandatory"] == 1) {
											echo Html::{$field["fieldtype"]}(
												$field["fieldname"], // Name attribute
												isset($Record->{$field["columnname"]}) ? true : false, // Checked state
												[
													'disabled' => $readonly ? true : false,
													"class" => $field["classname"], // Optional custom CSS class
													"label" => $field["fieldlabel"], // Label for the checkbox
													'data-pristine-required' => 'true', // Custom attribute
													'data-pristine-required-message' => $field["fieldlabel"] . ' is required ' // Custom validation message
												]
											);
										} else {

											echo Html::{$field["fieldtype"]}(
												$field["fieldname"], // Name attribute
												isset($Record->{$field["columnname"]}) ? true : false, // Checked state
												[
													'disabled' => $readonly ? true : false,
													"class" => $field["classname"], // Optional custom CSS class
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
									} else {
										// if ($field["columnname"] != 'salutation')
										echo "work in progress for XX uitype -- " . $field["uitype"] . $field["fieldlabel"];
									}
									//}counter if
						
									if ($field["uitype"] != 2 && $field["uitype"] != 11 && $field["fieldid"] != 19) {
										echo '<div class="help-block"></div>';

										$counter++;
									}
									echo "</div>";
				}
			}
		}

		?>

		</div>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-secondary update-close-btn" data-dismiss="modal">Close</button>
		<button type="button" class="btn btn-primary" id="confirmUpdateButton">Update</button>
	</div>
</div>
<script type="text/javascript" src="<?= $scriptPath ?>"></script>
<script type="text/javascript" src="<?= $baseUrl; ?>theme/libs/pristinejs/pristinejs.min.js"></script>
<!-- <script type="text/javascript" src="<?= $baseUrl; ?>theme/js/pages/form-validation.init.js"></script> -->
<?php
die;
?>