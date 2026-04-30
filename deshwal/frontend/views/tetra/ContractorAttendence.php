<?php  foreach ($user as $key=>$users) {?>
<tr class="prodlist">
	<td class="contractorattendancedataDelete text-center" id="contractorattendancedata_<?php echo $key; ?>_Delete"><a href="javascript:void;"><span class="glyphicon glyphicon-trash"></span></a></td>
	<td class="">
	<div style="display:none" id="contractorattendancedata_<?php echo $key;?>_code_em" class="ajxwarning errorMessage tooltip_img"></div>
	<input type="text" maxlength="25" id="contractorattendancedata_<?php echo $key;?>_code" name="contractorattendancedata[<?php echo $key;?>][code]" value="<?php echo $users['code']; ?>" class="code form-control" readonly="readonly">						
	</td>
	
	<td class="">
	<div style="display:none" id="contractorattendancedata_<?php echo $key;?>_shift_operator_name_em" class="ajxwarning errorMessage tooltip_img"></div>
	<input type="text" maxlength="25" id="contractorattendancedata_<?php echo $key;?>_shift_operator_name" name="contractorattendancedata[<?php echo $key;?>][shift_operator_name]" value="<?php echo $users['shift_operator_name']; ?>" class="shift_operator_name form-control" readonly="readonly">						
	</td>

	<td class="">
	<div style="display:none" id="contractorattendancedata_<?php echo $key;?>_role_em" class="ajxwarning errorMessage tooltip_img"></div>
	<input type="text" maxlength="25" id="contractorattendancedata_<?php echo $key;?>_role" name="contractorattendancedata[<?php echo $key;?>][role]" value="<?php echo $users['role']; ?>" class="role form-control" readonly="readonly">	
	</td>
	
	<td class="">
	<div style="display:none" id="contractorattendancedata_<?php echo $key;?>_att_status_em" class="ajxwarning errorMessage tooltip_img"></div>
	<select id="contractorattendancedata_<?php echo $key;?>_att_status" class="att_status form-control" name="contractorattendancedata[<?php echo $key;?>][att_status]">
	<?php  foreach ($attst as $attsts) {?>
	<option value="<?php echo $attsts['statusid']; ?>" <?php if($users['statusid']==$attsts['statusid']) echo "selected"; ?> ><?php echo $attsts['statusname']; ?></option>
	<?php  } ?>
	</select>					
	</td>

	<td class="">
	<div style="display:none" id="contractorattendancedata_<?php echo $key;?>_equipment_type_em" class="ajxwarning errorMessage tooltip_img"></div>
	<select id="contractorattendancedata_<?php echo $key;?>_equipment_type" class="equipment_type form-control" name="contractorattendancedata[<?php echo $key;?>][equipment_type]">
	<option value="">Select an Option</option>
	<?php foreach ($machinetype as $machinetypes) {?>
	<option value="<?php echo $machinetypes['typeid']; ?>" <?php if($users['typeid']==$machinetypes['typeid']) echo "selected"; ?> ><?php echo $machinetypes['type']; ?></option>
	<?php  } ?>
	</select>
	</td>

	<td class="">
	<div style="display:none" id="contractorattendancedata_<?php echo $key;?>_machine_dumper_no_em" class="ajxwarning errorMessage tooltip_img"></div>
	<select id="contractorattendancedata_<?php echo $key;?>_machine_dumper_no" class="machine_dumper_no form-control" name="contractorattendancedata[<?php echo $key;?>][machine_dumper_no]">
	<option value="">Select an Option</option>
	<?php if($users['equipment_id'] !='') {?>
	<option value="<?php echo $users['equipment_id']; ?>" <?php if($users['equipment_id']==$users['equipment_id']) echo "selected"; ?> ><?php echo $users['serial_no']; ?></option>
	<?php } ?>
	</select>
	</td>

	</tr>
	<?php } ?>
