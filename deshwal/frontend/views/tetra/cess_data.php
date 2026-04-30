<?php foreach ($ALLLIST as $key=>$cess_data) { ?>
	<tr>
	<td class="cess_dataDelete text-center"><a href="javascript:void;"><span class="glyphicon glyphicon-trash"></span></a></td>
	<td class="">
		<div style="display:none" id="cess_data_<?php echo $key;?>_overman_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="hidden" maxlength="25" id="cess_data_<?php echo $key;?>_manpowerid" name="cess_data[<?php echo $key;?>][manpowerid]" value="<?php echo $cess_data['manpowerid']; ?>">
		<input type="text" maxlength="25" id="cess_data_<?php echo $key;?>_overman" name="cess_data[<?php echo $key;?>][overman]" value="<?php echo $cess_data['overman']; ?>" class="form-control" readonly="readonly">	
	</td>

	<td class="">
		<div style="display:none" id="cess_data_<?php echo $key;?>_workingarea_em" class="ajxwarning errorMessage tooltip_img"></div>
		<select id="cess_data_<?php echo $key;?>_workingarea" class="form-control" name="cess_data[<?php echo $key;?>][workingarea]">
		<option value="">Select an Option</option>
		<?php  foreach ($ceworkingarea as $ceworkingareas) {?>
		<option value="<?php echo $ceworkingareas['workingareaid']; ?>" <?php if($ceworkingareas['workingareaid']==$cess_data['workingareaid']) echo 'selected'?> ><?php echo $ceworkingareas['workingareaname']; ?></option>
		<?php  } ?>
		</select>		
	</td>
	
	<td class="">
		<div style="display:none" id="cess_data_<?php echo $key;?>_contractor_em" class="ajxwarning errorMessage tooltip_img"></div>
		<select id="cess_data_<?php echo $key;?>_contractor" class="form-control" name="cess_data[<?php echo $key;?>][contractor]">
		<option value="">Select an Option</option>
		<?php  foreach ($cecontractor as $cecontractors) {?>
		<option value="<?php echo $cecontractors['contractormaster_id']; ?>" <?php if($cecontractors['contractormaster_id']==$cess_data['contractormaster_id']) echo 'selected'?> ><?php echo $cecontractors['company_name']; ?></option>
		<?php  } ?>	
		</select>			
	</td>


	</tr>
<?php  } ?>
