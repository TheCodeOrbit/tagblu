<?php foreach ($DumperList as $key=>$obr_contractor_dumper) { ?>
	<tr class="prodlist">
	<td class="">
		<div style="display:none" id="obr_contractor_dumper_<?php echo $key;?>_dumper_no_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="obr_contractor_dumper_<?php echo $key;?>_dumper_no" name="obr_contractor_dumper[<?php echo $key;?>][dumper_no]" value="<?php echo $obr_contractor_dumper['dumper_no']; ?>" class="form-control dumper_w" readonly="readonly">						
	</td>
	<td class="">
		<div style="display:none" id="obr_contractor_dumper_<?php echo $key;?>_operator_name_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="hidden" maxlength="25" id="obr_contractor_dumper_<?php echo $key;?>_operator_id" name="obr_contractor_dumper[<?php echo $key;?>][operator_id]" value="<?php echo $obr_contractor_dumper['operator_id']; ?>">
		<input type="text" maxlength="25" id="obr_contractor_dumper_<?php echo $key;?>_operator_name" name="obr_contractor_dumper[<?php echo $key;?>][operator_name]" value="<?php echo $obr_contractor_dumper['operator_name']; ?>" class="form-control operatorname_w" readonly="readonly">						
	</td>

	<td class="">
		<div style="display:none" id="obr_contractor_dumper_<?php echo $key;?>_working_area_em" class="ajxwarning errorMessage tooltip_img"></div>
		<select id="obr_contractor_dumper_<?php echo $key;?>_working_area" class="working_area form-control area_w" name="obr_contractor_dumper[<?php echo $key;?>][working_area]">
		<option value="">Select an Option</option>
		<?php  foreach ($workarea as $workareas) {?>
		<option value="<?php echo $workareas['workingareaid']; ?>" <?php if($workareas['workingareaid']==$obr_contractor_dumper['working_area']) echo 'selected' ?> > <?php echo $workareas['workingareaname']; ?></option>
		<?php  } ?>
	</select>							
	</td>

	<td class="">
	<div style="display:none" id="obr_contractor_dumper_<?php echo $key;?>_accessory_no_em" class="ajxwarning errorMessage tooltip_img"></div>
		<select id="obr_contractor_dumper_<?php echo $key;?>_accessory_no" class="remarks form-control accessory_w" name="obr_contractor_dumper[<?php echo $key;?>][accessory_no]">
		<option value="">Select an Option</option>
		<?php  foreach ($accessory as $accessorys) {?>
		<option value="<?php echo $accessorys['equipment_id']; ?>" <?php if($accessorys['equipment_id']==$obr_contractor_dumper['equipment_id']) echo 'selected' ?>><?php echo $accessorys['serial_no'].'('.$accessorys['equipment_name'].')'; ?></option>
		<?php  } ?>
		</select>
	</td>

	<td class="">
		<div style="display:none" id="obr_contractor_dumper_<?php echo $key;?>_no_of_trips_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="obr_contractor_dumper_<?php echo $key;?>_no_of_trips" name="obr_contractor_dumper[<?php echo $key;?>][no_of_trips]" value="<?php echo $obr_contractor_dumper['no_of_trips'];?>" class="nooftripsdump form-control nooftrips_w">					
	</td>

	<td class="" style="display:none">
		<div style="display:none" id="obr_contractor_dumper_<?php echo $key;?>_dumper_factor_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="hidden" maxlength="25" id="obr_contractor_dumper_<?php echo $key;?>_dumper_factor" name="obr_contractor_dumper[<?php echo $key;?>][dumper_factor]" value="<?php echo $obr_contractor_dumper['dumper_factor']; ?>" class="form-control factor_w" readonly>					
	</td>

	<td class="" style="display:none">
		<div style="display:none" id="obr_contractor_dumper_<?php echo $key;?>_ob_removed_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="hidden" maxlength="25" id="obr_contractor_dumper_<?php echo $key;?>_ob_removed" name="obr_contractor_dumper[<?php echo $key;?>][ob_removed]" value="<?php echo $obr_contractor_dumper['ob_removed'];?>" class="ob_removed form-control obremoved_w" readonly>					
	</td>

	<td class="">
		<div style="display:none" id="obr_contractor_dumper_<?php echo $key;?>_ir_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="obr_contractor_dumper_<?php echo $key;?>_ir" name="obr_contractor_dumper[<?php echo $key;?>][ir]" value="<?php echo $obr_contractor_dumper['ir']; ?>" class="irdumper form-control ir_w irvalidation">
	</td>

	<td class="">
		<div style="display:none" id="obr_contractor_dumper_<?php echo $key;?>_fr_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="obr_contractor_dumper_<?php echo $key;?>_fr" name="obr_contractor_dumper[<?php echo $key;?>][fr]" value="<?php echo $obr_contractor_dumper['fr']; ?>" class="frdumper form-control fr_w frvalidation"">						
	</td>

	<td class="">
	<div style="display:none" id="obr_contractor_dumper_<?php echo $key;?>_running_km_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="obr_contractor_dumper_<?php echo $key;?>_running_km" name="obr_contractor_dumper[<?php echo $key;?>][running_km]" value="<?php echo $obr_contractor_dumper['runninghrs']; ?>" class="runningkm form-control runningkm_w" readonly>						
	</td>

	<td class="">
		<div style="display:none" id="obr_contractor_dumper_<?php echo $key;?>_idle_hrs_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="obr_contractor_dumper_<?php echo $key;?>_idle_hrs" name="obr_contractor_dumper[<?php echo $key;?>][idle_hrs]" value="<?php echo $obr_contractor_dumper['idle_hrs'];?>" class="idle_hrs form-control idle_hrs_w">						
	</td>

	<td class="">
		<div style="display:none" id="obr_contractor_dumper_<?php echo $key;?>_breakdown_hrs_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="obr_contractor_dumper_<?php echo $key;?>_breakdown_hrs" name="obr_contractor_dumper[<?php echo $key;?>][breakdown_hrs]" value="<?php echo $obr_contractor_dumper['breakdown_hrs'];?>" class="breackhrs form-control breakdown_w">						
	</td>

	<td class="">
		<div style="display:none" id="obr_contractor_dumper_<?php echo $key;?>_diesel_consumption_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="obr_contractor_dumper_<?php echo $key;?>_diesel_consumption" name="obr_contractor_dumper[<?php echo $key;?>][diesel_consumption]" value="<?php echo $obr_contractor_dumper['diesel_consumption'];?>" class="diesel_consumption form-control diesel_consumption_w">						
	</td>

	<td class="">
	<div style="display:none" id="obr_contractor_dumper_<?php echo $key;?>_remarks_em" class="ajxwarning errorMessage tooltip_img"></div>
		<select id="obr_contractor_dumper_<?php echo $key;?>_remarks" class="remarks form-control remarks_w" name="obr_contractor_dumper[<?php echo $key;?>][remarks]">
		<option value="">Select an Option</option>
		<?php  foreach ($reamrks as $reamrk) {?>
		<option value="<?php echo $reamrk['remarksid']; ?>" <?php if($reamrk['remarksid']==$obr_contractor_dumper['remarksid']) echo 'selected' ?>><?php echo $reamrk['remarks']; ?></option>
		<?php  } ?>
		</select>
	</td>

</tr>
<?php  } ?>
