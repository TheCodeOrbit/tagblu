<?php foreach ($ALLLIST as $key=>$obrshift_data) { ?>
	<tr>
	<td class="obrshift_dataDelete text-center"><a href="javascript:void;"><span class="glyphicon glyphicon-trash"></span></a></td>
	<td class="">
		<div style="display:none" id="obrshift_data_<?php echo $key;?>_overman_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="hidden" maxlength="25" id="obrshift_data_<?php echo $key;?>_manpowerid" name="obrshift_data[<?php echo $key;?>][manpowerid]" value="<?php echo $obrshift_data['manpowerid']; ?>">
		<input type="text" maxlength="25" id="obrshift_data_<?php echo $key;?>_overman" name="obrshift_data[<?php echo $key;?>][overman]" value="<?php echo $obrshift_data['overman']; ?>" class="form-control" readonly="readonly">	
	</td>

	<td class="">
		<div style="display:none" id="obrshift_data_<?php echo $key;?>_working_area_em" class="ajxwarning errorMessage tooltip_img"></div>
		<select id="obrshift_data_<?php echo $key;?>_working_area" class="form-control" name="obrshift_data[<?php echo $key;?>][working_area]">
		<option value="">Select an Option</option>
		<?php  foreach ($obrworkingarea as $obrworkingareas) {?>
		<option value="<?php echo $obrworkingareas['workingareaid']; ?>" <?php if($obrworkingareas['workingareaid']==$obrshift_data['workingareaid']) echo 'selected'?> ><?php echo $obrworkingareas['workingareaname']; ?></option>
		<?php  } ?>
		</select>		
	</td>
	
	<td class="">
		<div style="display:none" id="obrshift_data_<?php echo $key;?>_contractor_em" class="ajxwarning errorMessage tooltip_img"></div>
		<select id="obrshift_data_<?php echo $key;?>_contractor" class="form-control" name="obrshift_data[<?php echo $key;?>][contractor]">
		<option value="">Select an Option</option>
		<?php  foreach ($obrcontractor as $obrcontractors) {?>
		<option value="<?php echo $obrcontractors['contractormaster_id']; ?>" <?php if($obrcontractors['contractormaster_id']==$obrshift_data['contractormaster_id']) echo 'selected'?> ><?php echo $obrcontractors['company_name']; ?></option>
		<?php  } ?>	
		</select>			
	</td>


	</tr>
<?php  } ?>
