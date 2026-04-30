<?php foreach ($DumperList as $key=>$ce_dumper_running_details) { ?>
	<tr class="prodlist">
	<td class="">
		<div style="display:none" id="ce_dumper_running_details_<?php echo $key;?>_dumper_no_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="ce_dumper_running_details_<?php echo $key;?>_dumper_no" name="ce_dumper_running_details[<?php echo $key;?>][dumper_no]" value="<?php echo $ce_dumper_running_details['dumper_no']; ?>" class="form-control  dumper_w" readonly="readonly">						
	</td>
	<td class="">
		<div style="display:none" id="ce_dumper_running_details_<?php echo $key;?>_operator_name_em" class="ajxwarning errorMessage tooltip_img"></div>
			<input type="text" maxlength="25" id="ce_dumper_running_details_<?php echo $key;?>_operator_name" name="ce_dumper_running_details[<?php echo $key;?>][operator_name]" value="<?php echo $ce_dumper_running_details['operator_name']; ?>" class="form-control  operatorname_w" readonly="readonly">						
	</td>

	<td class="">
	<div style="display:none" id="ce_dumper_running_details_<?php echo $key;?>_accessory_no_em" class="ajxwarning errorMessage tooltip_img"></div>
		<select id="ce_dumper_running_details_<?php echo $key;?>_accessory_no" class="remarks form-control accessory_w" name="ce_dumper_running_details[<?php echo $key;?>][accessory_no]">
		<option value="">Select an Option</option>
		<?php  foreach ($accessory as $accessorys) {?>
		<option value="<?php echo $accessorys['equipment_id']; ?>" <?php if($ce_dumper_running_details['equipment_id']==$accessorys['equipment_id']) echo 'selected'?> ><?php echo $accessorys['serial_no'].'('.$accessorys['equipment_name'].')'; ?></option>
		<?php  } ?>
		</select>
	</td>

	<td class="">
		<div style="display:none" id="ce_dumper_running_details_<?php echo $key;?>_c1_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="ce_dumper_running_details_<?php echo $key;?>_c1" name="ce_dumper_running_details[<?php echo $key;?>][c1]" value="<?php echo $ce_dumper_running_details['c1']; ?>" class="c1 form-control  c1_w">					
	</td>

	<td class="">
		<div style="display:none" id="ce_dumper_running_details_<?php echo $key;?>_c2_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="ce_dumper_running_details_<?php echo $key;?>_c2" name="ce_dumper_running_details[<?php echo $key;?>][c2]" value="<?php echo $ce_dumper_running_details['c2']; ?>" class="c2 form-control  c2_w">					
	</td>

	<td class="">
		<div style="display:none" id="ce_dumper_running_details_<?php echo $key;?>_chp_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="ce_dumper_running_details_<?php echo $key;?>_chp" name="ce_dumper_running_details[<?php echo $key;?>][chp]" value="<?php echo $ce_dumper_running_details['chp']; ?>" class="chp form-control  chp_w">					
	</td>

	<td class="">
		<div style="display:none" id="ce_dumper_running_details_<?php echo $key;?>_tba_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="ce_dumper_running_details_<?php echo $key;?>_tba" name="ce_dumper_running_details[<?php echo $key;?>][tba]" value="<?php echo $ce_dumper_running_details['tba']; ?>" class="tba form-control  tba_w">					
	</td>

	<td class="" style="display:none">
		<div style="display:none" id="ce_dumper_running_details_<?php echo $key;?>_dumper_factor_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="hidden" maxlength="25" id="ce_dumper_running_details_<?php echo $key;?>_dumper_factor" name="ce_dumper_running_details[<?php echo $key;?>][dumper_factor]" value="<?php echo $ce_dumper_running_details['dumper_factor']; ?>" class="form-control  factor_w" readonly>					
	</td>

	<td class="" style="display:none">
		<div style="display:none" id="ce_dumper_running_details_<?php echo $key;?>_ob_removed_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="hidden" maxlength="25" id="ce_dumper_running_details_<?php echo $key;?>_ob_removed" name="ce_dumper_running_details[<?php echo $key;?>][ob_removed]" value="<?php echo $ce_dumper_running_details['ob_removed']; ?>" class="ob_removed form-control  obremoved_w" readonly>					
	</td>

	<td class="">
		<div style="display:none" id="ce_dumper_running_details_<?php echo $key;?>_ir_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="ce_dumper_running_details_<?php echo $key;?>_ir" name="ce_dumper_running_details[<?php echo $key;?>][ir]" value="<?php echo $ce_dumper_running_details['ir']; ?>" class="irdumper form-control  ir_w irvalidation">
	</td>

	<td class="">
		<div style="display:none" id="ce_dumper_running_details_<?php echo $key;?>_fr_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="ce_dumper_running_details_<?php echo $key;?>_fr" name="ce_dumper_running_details[<?php echo $key;?>][fr]" value="<?php echo $ce_dumper_running_details['fr']; ?>" class="frdumper form-control  fr_w frvalidation"">						
	</td>

	<td class="">
	<div style="display:none" id="ce_dumper_running_details_<?php echo $key;?>_runningkm_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="ce_dumper_running_details_<?php echo $key;?>_runningkm" name="ce_dumper_running_details[<?php echo $key;?>][runningkm]" value="<?php echo $ce_dumper_running_details['runningkm']; ?>" class="runningkm form-control  runningkm_w" readonly>						
	</td>

	<td class="">
		<div style="display:none" id="ce_dumper_running_details_<?php echo $key;?>_idle_hrs_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="ce_dumper_running_details_<?php echo $key;?>_idle_hrs" name="ce_dumper_running_details[<?php echo $key;?>][idle_hrs]" value="<?php echo $ce_dumper_running_details['idle_hrs'];?>" class="idle_hrs form-control idle_hrs_w">						
	</td>

	<td class="">
		<div style="display:none" id="ce_dumper_running_details_<?php echo $key;?>_breakdown_hours_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="ce_dumper_running_details_<?php echo $key;?>_breakdown_hours" name="ce_dumper_running_details[<?php echo $key;?>][breakdown_hours]" value="<?php echo $ce_dumper_running_details['breakdown_hours']; ?>" class="form-control  breakdown_w">						
	</td>

	<td class="">
		<div style="display:none" id="ce_dumper_running_details_<?php echo $key;?>_diesel_consumption_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="ce_dumper_running_details_<?php echo $key;?>_diesel_consumption" name="ce_dumper_running_details[<?php echo $key;?>][diesel_consumption]" value="<?php echo $ce_dumper_running_details['diesel_consumption'];?>" class="diesel_consumption form-control diesel_consumption_w">						
	</td>

	<td class="">
	<div style="display:none" id="ce_dumper_running_details_<?php echo $key;?>_remarks_em" class="ajxwarning errorMessage tooltip_img"></div>
		<select id="ce_dumper_running_details_<?php echo $key;?>_remarks" class="remarks form-control remarks_w" name="ce_dumper_running_details[<?php echo $key;?>][remarks]">
		<option value="">Select an Option</option>
		<?php  foreach ($reamrks as $reamrk) {?>
		<option value="<?php echo $reamrk['remarksid'];?>" <?php if($ce_dumper_running_details['remarksid'] ==$reamrk['remarksid']) echo 'selected' ?>><?php echo $reamrk['remarks']; ?></option>
		<?php  } ?>
		</select>
	</td>

</tr>
<?php  } ?>
