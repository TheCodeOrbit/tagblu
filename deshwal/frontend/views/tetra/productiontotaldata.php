<?php //print_r($SESSIONS);?>
<tr class="prodlist">
	<td class="">
		<div style="display:none" id="productiontotaldata_0_ob_removal_total_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiontotaldata_0_ob_removal_total" name="productiontotaldata[0][ob_removal_total]" value="<?php echo $productiontotaldata[0]['ob_removal_total']; ?>" class="input-border form-control ob_removal_total ob_removal_total_w" readonly>						
	</td>

	<td class="<?php print_r($sessiondata);?>">
		<div style="display:none" id="productiontotaldata_0_ob_lead_total_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiontotaldata_0_ob_lead_total" name="productiontotaldata[0][ob_lead_total]" value="<?php echo $productiontotaldata[0]['ob_lead_total']; ?>" class="input-border form-control ob_lead_total ob_lead_total_w" readonly>						
	</td>

	<td class="">
		<div style="display:none" id="productiontotaldata_0_rom_production_total_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiontotaldata_0_rom_production_total" name="productiontotaldata[0][rom_production_total]" value="<?php echo $productiontotaldata[0]['rom_production_total']; ?>" class="input-border form-control rom_production_total rom_production_total_w" readonly>						
	</td>

	<td class="">
		<div style="display:none" id="productiontotaldata_0_rom_lead_total_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiontotaldata_0_rom_lead_total" name="productiontotaldata[0][rom_lead_total]" value="<?php echo $productiontotaldata[0]['rom_lead_total']; ?>" class="input-border form-control rom_lead_total rom_lead_total_w" readonly>						
	</td>

	<td class="">
		<div style="display:none" id="productiontotaldata_0_yield_total_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiontotaldata_0_yield_total" name="productiontotaldata[0][yield_total]" value="<?php echo $productiontotaldata[0]['yield_total']; ?>" readonly class="input-border form-control yield_total yield_total_w" >						
	</td>

	<td class="">
		<div style="display:none" id="productiontotaldata_0_washed_coal_production_total_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiontotaldata_0_washed_coal_production_total" name="productiontotaldata[0][washed_coal_production_total]" value="<?php echo $productiontotaldata[0]['washed_coal_production_total']; ?>" class="input-border form-control washed_coal_production_total washed_coal_production_total_w" readonly>						
	</td>

	<td class="">
		<div style="display:none" id="productiontotaldata_0_reject_production_total_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiontotaldata_0_reject_production_total" name="productiontotaldata[0][reject_production_total]" value="<?php echo $productiontotaldata[0]['reject_production_total']; ?>" class="input-border form-control reject_production_total reject_production_total_w" readonly>						
	</td>

	<td class="">
		<div style="display:none" id="productiontotaldata_0_washed_coal_dispatch_from_siding_total_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiontotaldata_0_washed_coal_dispatch_from_siding_total" name="productiontotaldata[0][washed_coal_dispatch_from_siding_total]" value="<?php echo $productiontotaldata[0]['washed_coal_dispatch_from_siding_total']; ?>" class="input-border form-control washed_coal_dispatch_from_siding_total washed_coal_dispatch_from_siding_total_w" readonly>						
	</td>
	
	<td class="">
		<div style="display:none" id="productiontotaldata_0_reject_coal_dispatch_total_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiontotaldata_0_reject_coal_dispatch_total" name="productiontotaldata[0][reject_coal_dispatch_total]" value="<?php echo $productiontotaldata[0]['reject_coal_dispatch_total']; ?>" readonly class="input-border form-control reject_coal_dispatch_total reject_coal_dispatch_total_w" >						 
	</td>

	<!-- Amit Editing 08-06-19  -->
	<td class="">
		<div style="display:none" id="productiontotaldata_0_rake_dispatch_total_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiontotaldata_0_rake_dispatch_total" name="productiontotaldata[0][rake_dispatch_total]" value="<?php echo $productiontotaldata[0]['rake_dispatch_total']; ?>" readonly class="input-border form-control inputwidth rake_dispatch_total rake_dispatch_total_w" >						 
	</td>
	<!-- End -->


 
	<?php if($sessiondata['cms_mine_name']== "pekb"){?>
<td class="">
		<div style="display:none" id="productiontotaldata_0_rakedisppatch_reject_total_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiontotaldata_0_rakedisppatch_reject_total" name="productiontotaldata[0][rakedisppatch_reject_total]" value="<?php echo $productiontotaldata[0]['rakedisppatch_reject_total']; ?>" readonly class="input-border form-control inputwidth rakedisppatch_reject_total rakedisppatch_reject_total_w" >						 
	</td>

<!--<td class="">
	<div style="display:none" id="productiondata_<?php echo $key;?>_rakedisppatch_reject_total_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiondata_<?php echo $key;?>_rakedisppatch_reject" name="productiondata[<?php echo $key;?>][rakedisppatch_reject_total]" value="<?php echo $productiondata['rakedisppatch_reject_total']; ?>" class="input-border form-control inputwidth rakedisppatch_reject_total rakedisppatch_reject_total_w">						
	</td>-->
<?php  } ?>







	<td class="">
		<div style="display:none" id="productiontotaldata_0_inv_washed_coal_total_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiontotaldata_0_inv_washed_coal_total" name="productiontotaldata[0][inv_washed_coal_total]" value="<?php echo $productiontotaldata[0]['inv_washed_coal_total']; ?>" readonly class="input-border form-control inputwidth inv_washed_coal_total inv_washed_coal_total_w" >						 
	</td>

	<td class="">
		<div style="display:none" id="productiontotaldata_0_inv_reject_coal_total_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiontotaldata_0_inv_reject_coal_total" name="productiontotaldata[0][inv_reject_coal_total]" value="<?php echo $productiontotaldata[0]['inv_reject_coal_total']; ?>" readonly class="input-border form-control inputwidth inv_reject_coal_total inv_reject_coal_total_w" >						 
	</td>

	<td class="">
		<div style="display:none" id="productiontotaldata_0_washed_coal_received_total_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiontotaldata_0_washed_coal_received_total" name="productiontotaldata[0][washed_coal_received_total]" value="<?php echo $productiontotaldata[0]['washed_coal_received_total']; ?>" readonly class="input-border form-control inputwidth washed_coal_received_total washed_coal_received_total_w" >						 
	</td>
	<?php if($sessiondata['cms_mine_name']== "kurmitar"){?>
	<td class="">
		<div style="display:none" id="productiontotaldata_0_subgrade_qty_total_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiontotaldata_0_subgrade_qty_total" name="productiontotaldata[0][subgrade_qty]" value="<?php echo $productiontotaldata[0]['subgrade_qty']; ?>" readonly class="input-border form-control inputwidth subgrade_qty_total subgrade_qty_total_w" >						 
	</td>
	<td class="">
		<div style="display:none" id="productiontotaldata_0_subgrade_lead_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiontotaldata_0_subgrade_lead_total" name="productiontotaldata[0][subgrade_lead]" value="<?php echo $productiontotaldata[0]['subgrade_lead']; ?>" readonly class="input-border form-control inputwidth subgrade_lead_total subgrade_lead_total_w" >						 
	</td>

<?php } ?>



</tr>
