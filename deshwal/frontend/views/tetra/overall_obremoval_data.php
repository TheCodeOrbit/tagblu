<?php foreach ($gettotallist as $key=>$overall_overall_obremoval_data) { ?>
<tr class="prodlist">
	<td class="">
		<div style="display:none" id="overall_obremoval_data_<?php echo $key;?>_totnooftrips_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="overall_obremoval_data_<?php echo $key;?>_totnooftrips" name="overall_obremoval_data[<?php echo $key;?>][totnooftrips]" value="<?php echo $overall_overall_obremoval_data['totnooftrips'];?>" class="totnooftripsdump form-control" readonly="readonly">					
	</td>

	<td class="">
		<div style="display:none" id="overall_obremoval_data_<?php echo $key;?>_tottotal_obremoved_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="overall_obremoval_data_<?php echo $key;?>_tottotal_obremoved" name="overall_obremoval_data[<?php echo $key;?>][tottotal_obremoved]" value="<?php echo $overall_overall_obremoval_data['tottotal_obremoved']; ?>" class="form-control" readonly>					
	</td>

	<td class="">
		<div style="display:none" id="overall_obremoval_data_<?php echo $key;?>_totaverage_lead_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="overall_obremoval_data_<?php echo $key;?>_totaverage_lead" name="overall_obremoval_data[<?php echo $key;?>][totaverage_lead]" value="<?php echo $overall_overall_obremoval_data['totaverage_lead'];?>" class="totaverage_lead form-control" readonly>					
	</td>
</tr>

<?php }?>
