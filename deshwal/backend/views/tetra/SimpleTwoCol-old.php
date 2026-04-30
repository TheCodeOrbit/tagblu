<?php

use yii\helpers\Html;
use common\models\Fieldtype;
use backend\models\AccessCheck;
use common\models\MultiList;
use common\models\Picklist;
use app\models\Reference;

// echo $TableName;die;

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
	padding-left: 30px; /* Space for the left icon */
	padding-right: 60px; /* Space for the two right icons */
}

.icon-left, .icon-right {
	position: absolute;
	cursor: pointer;
}

.icon-left {
	left: 10px;
}

.search-icon {
	right: 30px; /* Position search icon closer to the text */
}

.plus-icon {
	right: 10px; /* Position plus icon at the far right */
}


</style>

<?php
// echo "Block<pre>";
// print_r($Block->fields);
//die();

$counter = 0;
foreach ($Block->fields as $field) {
	$fieldid = $field["fieldid"];
	if($counter == 0)
	{
		echo '<div class="row"><!--open first row--><div class="col-md-6 section-'.$field["columnname"].'"><!--open first col-->';

	}

	if ($hasadminpower == 1) {
		$visible =	0;
		$readonly =	0;
	} else {
		//now check if this field is allowed to edit ,readonly etc
		$model = new AccessCheck();
		$permission = $model->fieldacces($uid, $fieldid);
		//print_r($permission);die;
		if (is_array($permission)) {
			$visible =	$permission['visible'];
			$readonly =	$permission['readonly'];
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
		// $Column->blocks[$BlockKey]->fields[$FieldKey]->fieldoptions=$PickList->getPickListOption($table_name);


		$field["fieldoptions"] = $PickList->getPickListOption($ModuleName);
	} else if ($field["uitype"] == 22 && $visible == 0) {
		$PickList = new MultiList;
		$PickList->fieldid = $field["fieldid"];
		// $Column->blocks[$BlockKey]->fields[$FieldKey]->fieldoptions=$PickList->getMultiListOption($table_name);


		$field["fieldoptions"] = $PickList->getMultiListOption($ModuleName);
	}
	//print_r($FieldTypeRecord);die;
	if ($field["uitype"] != 2 && $field["uitype"] != 11 && $field["uitype"] != 70 && $field["uitype"] != 53 && $visible == 0) {
		//close/open divs
		if ($counter > 0) {
			echo '</div><!-- close form group--></div><!-- close col6 div--><div class="col-md-6 inner section-'.$field["columnname"].'"><div class="form-group field-uiinputs-' . $field["fieldname"] . '">';
		} else {
			// for first col 6
			echo '<div class="form-group field-uiinputs-' . $field["fieldname"] . '">';
		}
	}
	if($field["uitype"]==12)
	{
		$popupclass="ref-form-control";
	}
	else{
		$popupclass='';
	}
	//check mandatory
	if ($field["mandatory"] == 1) {

		//echo $field["maximumlength"];
		$classarray = array(
			'class' => 'form-control '.$popupclass,
			'id' => $field["fieldname"],
			"fieldid" => $field["fieldid"],
			"value" => isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]}	: "",
			'maxlength' => $field["maximumlength"],
			'data-pristine-required' => 'true',
			'data-pristine-required-message' => $field["fieldlabel"] . ' is required ',
			'readonly' => $readonly ? true : false

		);
		$mandatory = "<span class='red'> *</span>";
	} else {
		$classarray = array(
			'class' => $popupclass.' form-control ' . $readonly,
			'readonly' => $readonly ? true : false,
			'id' => $field["fieldname"],
			'maxlength' => $field["maximumlength"],
			"value" => isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]}	: ""
		);
		$mandatory = "";
	}

?>

	<?php if ($field["uitype"] != 2 &&  $field["uitype"] != 11 && $field["uitype"] != 70 && $field["uitype"] != 53  && $field["uitype"] != 6 && $visible == 0) { //show label if not hiden type and not radio type

		echo Html::label($field["fieldlabel"] . $mandatory, $field["fieldname"], ['class' => 'control-label ']);
	}

	if ($field["uitype"] == 1 && $visible == 0) {



		echo   Html::{$field["fieldtype"]}($field["tablename"] . '[' . $field["fieldname"] . ']', '', $classarray);
	} elseif ($field["uitype"] == 2 && $visible == 0) //hiden
	{
		echo	 Html::{$field["fieldtype"]}($field["tablename"] . '[' . $field["fieldname"] . ']', '', $classarray);
	} elseif ($field["uitype"] == 4 && $visible == 0) //text area
	{
		echo	 Html::{$field["fieldtype"]}($field["tablename"] . '[' . $field["fieldname"] . ']', '', $classarray);
	} elseif ($field["uitype"] == 8 && $visible == 0) //simgle drop down
	{
		//check if  ownerid field
		if($field["columnname"] == "ownerid")
		{
			echo Html::{$field["fieldtype"]}(
					$field["tablename"] . '[' . $field["fieldname"] . ']',
					isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} :  Yii::$app->user->id,
					$field["fieldoptions"],
					['class' => 'form-control',"id"=>$field["columnname"], "prompt" => "Select " . $field["fieldlabel"], 'data-pristine-required' => 'true', 'data-pristine-required-message' => $field["fieldlabel"] . ' is required ', 'disabled' => $readonly ? true : false]
				);
		}
		else
		{

			if ($field["mandatory"] == 1) {

				echo Html::{$field["fieldtype"]}(
					$field["tablename"] . '[' . $field["fieldname"] . ']',
					isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '',
					$field["fieldoptions"],
					['class' => 'form-control',"id"=>$field["columnname"], "prompt" => "Select " . $field["fieldlabel"], 'data-pristine-required' => 'true', 'data-pristine-required-message' => $field["fieldlabel"] . ' is required ', 'disabled' => $readonly ? true : false]
				);
			} else {
				echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '', $field["fieldoptions"], ['disabled' => $readonly ? true : false, 'class' => 'form-control',"id"=>$field["columnname"], "prompt" => "Select " . $field["fieldlabel"]]);
			}
		}
	} elseif ($field["uitype"] == 23 && $visible == 0) //for numeric
	{

		// <!-- Label for the text input -->

		echo   Html::{$field["fieldtype"]}($field["tablename"] . '[' . $field["fieldname"] . ']', '', $classarray);
	} elseif ($field["uitype"] == 24 && $visible == 0) //multiple drop down
	{
		if ($field["mandatory"] == 1) {

			echo Html::{$field["fieldtype"]}(
				$field["tablename"] . '[' . $field["fieldname"] . ']',
				isset($Record->{$field["columnname"]}) ? explode(',', $Record->{$field["columnname"]}) : '',
				$field["fieldoptions"],
				[
					'class' => 'form-control',
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
					'disabled' => $readonly ? true : false,
					'class' => 'form-control',
					"prompt" => "Select " . $field["fieldlabel"]
				]
			);
		}
	} elseif ($field["uitype"] == 13 && $visible == 0) {
		// include "uitype/Date.php";
		echo Html::input($field["fieldtype"], $field["tablename"] . '[' . $field["fieldname"] . ']', '', $classarray);
	}
	 elseif ($field["uitype"] == 17 && $visible == 0) {
		// include "uitype/Date.php";
		echo Html::input($field["fieldtype"], $field["tablename"] . '[' . $field["fieldname"] . ']', '', $classarray);
	} elseif ($field["uitype"] == 12 && $visible == 0) { ?>
		<div>
			<?php
			$relatedmod_tabid = $field["related_mod"];
			$fieldname = $field["columnname"];
			$fieldname1 = $field["columnname"] . "1";
			$fieldname2 = $field["columnname"] . "2";
			$fieldname3 = $field["columnname"] . "3";
			$model1 = new Reference($TableName,$FieldId);
			$relatedmodulename = $model1->getRelatedNoduleName($field["fieldid"]);
			$getRelatedDisplayFieldName=$model1->getRelatedDisplayFieldName($field["fieldid"]);
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
				$ref_disp_value ='';// $field["reffieldvalue"];
			// }
			// echo $form
			// 	->field($model, $field["fieldname"])
			// 	->hiddenInput([
			// 		"class" => $field["classname"],
			// 		"value" => $ref_hid_value,
			// 	]);

			$relatedmod ='';// $field["relatedmodulename"];
			$getRelatedDField ='';// $field["getRelatedDisplayFieldName"];
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
	<svg class="icon-left" fill="#2F80ED" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="15" height="15" role="button" tabindex="0" onclick="removeTextValue(<?= $fieldname1;?>,<?= $fieldname;?>);" aria-label="Remove vendor">
		<path d="M4.7070312 3.2929688 L3.2929688 4.7070312 L10.585938 12 L3.2929688 19.292969 L4.7070312 20.707031 L12 13.414062 L19.292969 20.707031 L20.707031 19.292969 L13.414062 12 L20.707031 4.7070312 L19.292969 3.2929688 L12 10.585938 L4.7070312 3.2929688 Z"></path>
	</svg>

<input class="effect" style="flex-grow:1;" type="hidden" id="<?php echo $fieldname1; ?>" name="<?php echo $field["tablename"] . '[' . $field["fieldname"] . ']' ?>" value="<?php echo $ref_disp_value; ?>" readonly='readonly'>
			<?php echo   Html::{$field["fieldtype"]}('', '', $classarray);?>


	<!-- Search Icon on the Right -->
	<svg class="icon-right search-icon sct" width="15" height="15" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg" data-toggle="modal" data-target="#myModal22" role="button" aria-hidden="true" tabindex="0" onclick="showCustomer1('<?= $fieldname1 ?>','<?= $fieldname ?>','<?= $getRelatedDisplayFieldName; ?>','<?= $relatedmodulename; ?>',<?= $Block->blockid;;?>)" aria-label="Search vendor">
		<path d="M21 21.5L16.514 17.006L21 21.5ZM19 11C19 13.2543 18.1045 15.4163 16.5104 17.0104C14.9163 18.6045 12.7543 19.5 10.5 19.5C8.24566 19.5 6.08365 18.6045 4.48959 17.0104C2.89553 15.4163 2 13.2543 2 11C2 8.74566 2.89553 6.58365 4.48959 4.98959C6.08365 3.39553 8.24566 2.5 10.5 2.5C12.7543 2.5 14.9163 3.39553 16.5104 4.98959C18.1045 6.58365 19 8.74566 19 11V11Z" stroke="#2F80ED" stroke-width="2" stroke-linecap="round"></path>
	</svg>

	<!-- Plus Icon on the Right -->
	<svg class="icon-right plus-icon" fill="#2F80ED" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="15" height="15" role="button" tabindex="0" onclick="addVendor('<?= $fieldname1 ?>','<?= $fieldname ?>','<?= $getRelatedDisplayFieldName; ?>','<?= $relatedmodulename; ?>',<?= $Block->blockid;;?>)" aria-label="Add vendor">
		<path d="M12 5v7H5v2h7v7h2v-7h7v-2h-7V5z"></path>
	</svg>
</div>


		

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
		echo	 Html::hiddenInput($field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]}	: Yii::$app->user->id, [

			'id' => $field["fieldname"],
		]);
	} elseif ($field["uitype"] == 70) //hiden
	{
		echo	 Html::hiddenInput($field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]}	: date("Y-m-d H:i:s"), [

			'id' => $field["fieldname"],
		]);
	}
	else if ($field["uitype"] == 11) //hiden
	{
		echo	 Html::hiddenInput($field["tablename"] . '[' . $field["fieldname"] . ']', '', $classarray);
	}
	 else {
		echo "work in progress for XX uitype -- " . $field["uitype"];
	}
	//}counter if

	if ($field["uitype"] != 2 && $field["uitype"] != 11) {
		echo '<div class="help-block"></div>';

		$counter++;
	}
}
echo "</div><!-- close form group--></div></div><!--close last row-->";


?>