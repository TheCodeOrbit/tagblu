<?php foreach ($ALLLIST as $key=>$cess_contractorwise) { ?>
<tr class="prodlist">
	<td class="">
		<div style="display:none" id="cess_contractorwise_<?php echo $key;?>_contractor_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="hidden" maxlength="25" id="cess_contractorwise_<?php echo $key;?>_contractor_id" name="cess_contractorwise[<?php echo $key;?>][contractor_id]" value="<?php echo $cess_contractorwise['contractor_id']; ?>">
		<input type="text" maxlength="25" id="cess_contractorwise_<?php echo $key;?>_contractor" name="cess_contractorwise[<?php echo $key;?>][contractor]" value="<?php echo $cess_contractorwise['contractor']; ?>" class="form-control" readonly="readonly">						
	</td>

	<td class="">
		<div style="display:none" id="cess_contractorwise_<?php echo $key;?>_trip_qty_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="cess_contractorwise_<?php echo $key;?>_trip_qty" name="cess_contractorwise[<?php echo $key;?>][trip_qty]" value="<?php echo $cess_contractorwise['trip_qty'];?>" class="trip_qtydump form-control" readonly="readonly">					
	</td>

	<td class="">
		<div style="display:none" id="cess_contractorwise_<?php echo $key;?>_wm_qty_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="cess_contractorwise_<?php echo $key;?>_wm_qty" name="cess_contractorwise[<?php echo $key;?>][wm_qty]" value="<?php echo $cess_contractorwise['wm_qty']; ?>" class="form-control" readonly>					
	</td>

	<td class="">
		<div style="display:none" id="cess_contractorwise_<?php echo $key;?>_average_lead_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="cess_contractorwise_<?php echo $key;?>_average_lead" name="cess_contractorwise[<?php echo $key;?>][average_lead]" value="<?php echo $cess_contractorwise['average_lead'];?>" class="average_lead form-control" readonly>					
	</td>
	</tr>
<?php  } ?>
