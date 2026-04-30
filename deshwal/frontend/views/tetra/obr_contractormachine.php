<?php foreach ($MachineList as $key=>$obr_contractor_machine) {?>
	<tr class="prodlist">
	<td class="">
		<div style="display:none" id="obr_contractor_machine_<?php echo $key;?>_machine_no_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="obr_contractor_machine_<?php echo $key;?>_machine_no" name="obr_contractor_machine[<?php echo $key;?>][machine_no]" value="<?php echo $obr_contractor_machine['machine_no']; ?>" class="form-control " readonly="readonly">						
	</td>
	<td class="">
		<div style="display:none" id="obr_contractor_machine_<?php echo $key;?>_operator_name_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="obr_contractor_machine_<?php echo $key;?>_operator_name" name="obr_contractor_machine[<?php echo $key;?>][operator_name]" value="<?php echo $obr_contractor_machine['operator_name']; ?>" class="form-control " readonly="readonly">						
	</td>
	<td class="">
		<div style="display:none" id="obr_contractor_machine_<?php echo $key;?>_working_area_em" class="ajxwarning errorMessage tooltip_img"></div>
		<select id="obr_contractor_machine_<?php echo $key;?>_working_area" class="working_area form-control" name="obr_contractor_machine[<?php echo $key;?>][working_area]">
		<option value="">Select an Option</option>
		<?php  foreach ($workarea as $workareas) {?>
		<option value="<?php echo $workareas['workingareaid']; ?>" <?php if($workareas['workingareaid']==$obr_contractor_machine['working_area']) echo 'selected' ?>><?php echo $workareas['workingareaname']; ?></option>
		<?php  } ?>
	</select>					
	</td>
	<td class="">
		<div style="display:none" id="obr_contractor_machine_<?php echo $key;?>_ir_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="obr_contractor_machine_<?php echo $key;?>_ir" name="obr_contractor_machine[<?php echo $key;?>][ir]" value="<?php echo $obr_contractor_machine['ir']; ?>" class="irmachine form-control irvalidation">					
	</td>
	<td class="">
		<div style="display:none" id="obr_contractor_machine_<?php echo $key;?>_fr_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="obr_contractor_machine_<?php echo $key;?>_fr" name="obr_contractor_machine[<?php echo $key;?>][fr]" value="<?php echo $obr_contractor_machine['fr']; ?>" class="frmachine form-control frvalidation">
	</td>
	<td class="">
		<div style="display:none" id="obr_contractor_machine_<?php echo $key;?>_running_hrs_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="obr_contractor_machine_<?php echo $key;?>_running_hrs" name="obr_contractor_machine[<?php echo $key;?>][running_hrs]" value="<?php echo $obr_contractor_machine['runninghrs']; ?>" class=" running_hrs form-control " readonly="readonly">						
	</td>

	<td class="">
		<div style="display:none" id="obr_contractor_machine_<?php echo $key;?>_idle_hrs_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="obr_contractor_machine_<?php echo $key;?>_idle_hrs" name="obr_contractor_machine[<?php echo $key;?>][idle_hrs]" value="<?php echo $obr_contractor_machine['idle_hrs'];?>" class="idle_hrs form-control idle_hrs_w">						
	</td>

	<td class="">
	<div style="display:none" id="obr_contractor_machine_<?php echo $key;?>_breakdown_hrs_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="obr_contractor_machine_<?php echo $key;?>_breakdown_hrs" name="obr_contractor_machine[<?php echo $key;?>][breakdown_hrs]" value="<?php echo $obr_contractor_machine['breakdown_hrs']; ?>" class="breackdown form-control">						
	</td>

	<td class="">
		<div style="display:none" id="obr_contractor_machine_<?php echo $key;?>_diesel_consumption_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="obr_contractor_machine_<?php echo $key;?>_diesel_consumption" name="obr_contractor_machine[<?php echo $key;?>][diesel_consumption]" value="<?php echo $obr_contractor_machine['diesel_consumption'];?>" class="diesel_consumption form-control diesel_consumption_w">						
	</td>		

	<td class="">
	<div style="display:none" id="obr_contractor_machine_<?php echo $key;?>_remarks_em" class="ajxwarning errorMessage tooltip_img"></div>
		<select id="obr_contractor_machine_<?php echo $key;?>_remarks" class="remarks form-control" name="obr_contractor_machine[<?php echo $key;?>][remarks]">
		<option value="">Select an Option</option>
		<?php  foreach ($reamrks as $reamrk) {?>
		<option value="<?php echo $reamrk['remarksid']; ?>" <?php if($reamrk['remarksid']==$obr_contractor_machine['remarksid']) echo 'selected' ?>><?php echo $reamrk['remarks']; ?></option>
		<?php  } ?>
	</select>
	</td>
</tr>
<?php  } ?>
