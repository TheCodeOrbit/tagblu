<?php foreach ($ALLLIST as $key=>$obremoval_data) { ?>
<tr class="prodlist">
	<td class="">
		<div style="display:none" id="obremoval_data_<?php echo $key;?>_contractor_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="hidden" maxlength="25" id="obremoval_data_<?php echo $key;?>_contractor_id" name="obremoval_data[<?php echo $key;?>][contractor_id]" value="<?php echo $obremoval_data['contractor_id']; ?>">
		<input type="text" maxlength="25" id="obremoval_data_<?php echo $key;?>_contractor" name="obremoval_data[<?php echo $key;?>][contractor]" value="<?php echo $obremoval_data['contractor']; ?>" class="form-control" readonly="readonly">						
	</td>

	<td class="">
		<div style="display:none" id="obremoval_data_<?php echo $key;?>_nooftrips_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="obremoval_data_<?php echo $key;?>_nooftrips" name="obremoval_data[<?php echo $key;?>][nooftrips]" value="<?php echo $obremoval_data['nooftrips'];?>" class="nooftripsdump form-control" readonly="readonly">					
	</td>

	<td class="">
		<div style="display:none" id="obremoval_data_<?php echo $key;?>_total_obremoved_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="obremoval_data_<?php echo $key;?>_total_obremoved" name="obremoval_data[<?php echo $key;?>][total_obremoved]" value="<?php echo $obremoval_data['total_obremoved']; ?>" class="form-control" readonly>					
	</td>

	<td class="">
		<div style="display:none" id="obremoval_data_<?php echo $key;?>_average_lead_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="obremoval_data_<?php echo $key;?>_average_lead" name="obremoval_data[<?php echo $key;?>][average_lead]" value="<?php echo $obremoval_data['average_lead'];?>" class="average_lead form-control" readonly>					
	</td>
	</tr>
<?php  } ?>

