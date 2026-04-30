<?php foreach ($MachineList as $key=>$ce_machine_details) {?>
	<tr class="prodlist">
	<td class="">
		<div style="display:none" id="ce_machine_details_<?php echo $key;?>_machine_no_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="ce_machine_details_<?php echo $key;?>_machine_no" name="ce_machine_details[<?php echo $key;?>][machine_no]" value="<?php echo $ce_machine_details['machine_no']; ?>" class="form-control " readonly="readonly">						
	</td>
	<td class="">
		<div style="display:none" id="ce_machine_details_<?php echo $key;?>_operator_name_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="ce_machine_details_<?php echo $key;?>_operator_name" name="ce_machine_details[<?php echo $key;?>][operator_name]" value="<?php echo $ce_machine_details['operator_name']; ?>" class="form-control operatorname_w" readonly="readonly">						
	</td>
	<td class="">
		<div style="display:none" id="ce_machine_details_<?php echo $key;?>_working_area_em" class="ajxwarning errorMessage tooltip_img"></div>
		<select id="ce_machine_details_<?php echo $key;?>_working_area" class="working_area form-control area_w" name="ce_machine_details[<?php echo $key;?>][working_area]">
		<option value="">Select an Option</option>
		<?php  foreach ($workarea as $workareas) {?>
		<option value="<?php echo $workareas['workingareaid']; ?>" <?php if($ce_machine_details['working_area'] ==$workareas['workingareaid']) echo 'selected' ?>><?php echo $workareas['workingareaname']; ?></option>
		<?php  } ?>
	</select>					
	</td>
	<td class="">
		<div style="display:none" id="ce_machine_details_<?php echo $key;?>_ir_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="ce_machine_details_<?php echo $key;?>_ir" name="ce_machine_details[<?php echo $key;?>][ir]" value="<?php echo $ce_machine_details['ir']; ?>" class="irmachine form-control irvalidation">					
	</td>
	<td class="">
		<div style="display:none" id="ce_machine_details_<?php echo $key;?>_fr_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="ce_machine_details_<?php echo $key;?>_fr" name="ce_machine_details[<?php echo $key;?>][fr]" value="<?php echo $ce_machine_details['fr']; ?>" class="frmachine form-control frvalidation">
	</td>
	<td class="">
		<div style="display:none" id="ce_machine_details_<?php echo $key;?>_running_hours_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="ce_machine_details_<?php echo $key;?>_running_hours" name="ce_machine_details[<?php echo $key;?>][running_hours]" value="<?php echo $ce_machine_details['running_hours']; ?>" class="form-control " readonly="readonly">						
	</td>
	
	<td class="">
		<div style="display:none" id="ce_machine_details_<?php echo $key;?>_idle_hrs_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="ce_machine_details_<?php echo $key;?>_idle_hrs" name="ce_machine_details[<?php echo $key;?>][idle_hrs]" value="<?php echo $ce_machine_details['idle_hrs'];?>" class="idle_hrs form-control idle_hrs_w">						
	</td>

	<td class="">
	<div style="display:none" id="ce_machine_details_<?php echo $key;?>_breakdown_hours_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="ce_machine_details_<?php echo $key;?>_breakdown_hours" name="ce_machine_details[<?php echo $key;?>][breakdown_hours]" value="<?php echo $ce_machine_details['breakdown_hours']; ?>" class="breackhrs form-control ">						
	</td>
	
	<td class="">
		<div style="display:none" id="ce_machine_details_<?php echo $key;?>_diesel_consumption_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="ce_machine_details_<?php echo $key;?>_diesel_consumption" name="ce_machine_details[<?php echo $key;?>][diesel_consumption]" value="<?php echo $ce_machine_details['diesel_consumption'];?>" class="diesel_consumption form-control diesel_consumption_w">						
	</td>

	<td class="">
	<div style="display:none" id="ce_machine_details_<?php echo $key;?>_remarks_em" class="ajxwarning errorMessage tooltip_img"></div>
		<select id="ce_machine_details_<?php echo $key;?>_remarks" class="remarks form-control remarks_w" name="ce_machine_details[<?php echo $key;?>][remarks]">
		<option value="">Select an Option</option>
		<?php  foreach ($reamrks as $reamrk) {?>
		<option value="<?php echo $reamrk['remarksid']; ?>" <?php if($ce_machine_details['remarksid'] ==$reamrk['remarksid']) echo 'selected' ?>><?php echo $reamrk['remarks']; ?></option>
		<?php  } ?>
		</select>
	</td>
</tr>
<?php  } ?>
