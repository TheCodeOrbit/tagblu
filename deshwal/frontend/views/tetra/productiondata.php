<?php
//$MineName="<script>mine_name<script>";
 foreach ($ALLLIST as $key=>$productiondata) { ?>
	<tr class="prodlist">
	<td class="">
		<div style="display:none" id="productiondata_<?php echo $key;?>_uom_em" class="ajxwarning errorMessage tooltip_img"></div>
		<select id="productiondata_<?php echo $key;?>_uom" class="uom form-control uom_w" name="productiondata[<?php echo $key;?>][uom]">
		<option value="<?php echo $productiondata['monthsid']; ?>" selected><?php echo $productiondata['uom']; ?></option>
		</select>	
	</td>

	<td class="<?php //print_r($SESSIONS); ?>">
		<div style="display:none" id="productiondata_<?php echo $key;?>_ob_removal_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiondata_<?php echo $key;?>_ob_removal" name="productiondata[<?php echo $key;?>][ob_removal]" value="<?php echo $productiondata['ob_removal']; ?>" class="input-border form-control ob_removal ob_removal_w">						
	</td>

	<td class="">
		<div style="display:none" id="productiondata_<?php echo $key;?>_ob_lead_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiondata_<?php echo $key;?>_ob_lead" name="productiondata[<?php echo $key;?>][ob_lead]" value="<?php echo $productiondata['ob_lead']; ?>" class="input-border form-control ob_lead ob_lead_w">						
	</td>

	<td class="">
		<div style="display:none" id="productiondata_<?php echo $key;?>_rom_production_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiondata_<?php echo $key;?>_rom_production" name="productiondata[<?php echo $key;?>][rom_production]" value="<?php echo $productiondata['rom_production']; ?>" class="input-border form-control rom_production rom_production_w">						
	</td>

	<td class="">
		<div style="display:none" id="productiondata_<?php echo $key;?>_rom_lead_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiondata_<?php echo $key;?>_rom_lead" name="productiondata[<?php echo $key;?>][rom_lead]" value="<?php echo $productiondata['rom_lead']; ?>" class="input-border form-control rom_lead rom_lead_w">						
	</td>

	<td class="">
		<div style="display:none" id="productiondata_<?php echo $key;?>_yield_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiondata_<?php echo $key;?>_yield" name="productiondata[<?php echo $key;?>][yield]" value="<?php echo $productiondata['yield']; ?>" class="input-border form-control yield yield_w" >						
	</td>
	
	<td class="">
		<div style="display:none" id="productiondata_<?php echo $key;?>_washed_coal_production_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiondata_<?php echo $key;?>_washed_coal_production" name="productiondata[<?php echo $key;?>][washed_coal_production]" value="<?php echo $productiondata['washed_coal_production']; ?>" class="input-border form-control washed_coal_production washed_coal_production_w" >						
	</td>

	<td class="">
		<div style="display:none" id="productiondata_<?php echo $key;?>_reject_production_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiondata_<?php echo $key;?>_reject_production" name="productiondata[<?php echo $key;?>][reject_production]" value="<?php echo $productiondata['reject_production']; ?>"  class="input-border form-control reject_production reject_production_w" >						
	</td>

	<td class="">
		<div style="display:none" id="productiondata_<?php echo $key;?>_coal_dispatch_from_siding_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiondata_<?php echo $key;?>_coal_dispatch_from_siding" name="productiondata[<?php echo $key;?>][coal_dispatch_from_siding]" value="<?php echo $productiondata['coal_dispatch_from_siding']; ?>" class="input-border form-control coal_dispatch_from_siding coal_dispatch_from_siding_w" >						
	</td>
	
	<td class="">
		<div style="display:none" id="productiondata_<?php echo $key;?>_reject_coal_dispatch_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiondata_<?php echo $key;?>_reject_coal_dispatch_from_siding" name="productiondata[<?php echo $key;?>][reject_coal_dispatch]" value="<?php echo $productiondata['reject_coal_dispatch']; ?>" class="input-border form-control reject_coal_dispatch reject_coal_dispatch_w" >						
	</td>



<td class="">
		<div style="display:none" id="productiondata_<?php echo $key;?>_rakedipatch_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiondata_<?php echo $key;?>_rakedipatch" name="productiondata[<?php echo $key;?>][rakedipatch]" value="<?php echo $productiondata['rakedipatch']; ?>" class="input-border form-control inputwidth rakedipatch rakedipatch_w">						
	</td>

<?php if($SESSIONS['cms_mine_name']=="pekb"){?>

<td class="">
		<div style="display:none" id="productiondata_<?php echo $key;?>_rakedisppatch_reject_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiondata_<?php echo $key;?>_rakedisppatch_reject" name="productiondata[<?php echo $key;?>][rakedisppatch_reject]" value="<?php echo $productiondata['rakedisppatch_reject']; ?>" class="input-border form-control inputwidth rakedisppatch_reject rakedisppatch_reject_w">						
	</td>
<?php } ?>

	<td class="">
		<div style="display:none" id="productiondata_<?php echo $key;?>_inv_washed_coal_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiondata_<?php echo $key;?>_inv_washed_coal" name="productiondata[<?php echo $key;?>][inv_washed_coal]" value="<?php echo $productiondata['inv_washed_coal']; ?>" class="input-border form-control inputwidth inv_washed_coal inv_washed_coal_w">						
	</td>

	<td class="">
		<div style="display:none" id="productiondata_<?php echo $key;?>_inv_reject_coal_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiondata_<?php echo $key;?>_inv_reject_coal" name="productiondata[<?php echo $key;?>][inv_reject_coal]" value="<?php echo $productiondata['inv_reject_coal']; ?>" class="input-border form-control inputwidth inv_reject_coal inv_reject_coal_w">						
	</td>

	<td class="">
		<div style="display:none" id="productiondata_<?php echo $key;?>_washed_coal_received_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiondata_<?php echo $key;?>_washed_coal_received" name="productiondata[<?php echo $key;?>][washed_coal_received]" value="<?php echo $productiondata['washed_coal_received']; ?>" class="input-border form-control inputwidth washed_coal_received washed_coal_received_w">						
	</td>
<?php if($SESSIONS['cms_mine_name']=="kurmitar"){?>
<td class="">
		<div style="display:none" id="productiondata_<?php echo $key;?>_subgrade_qty_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiondata_<?php echo $key;?>_subgrade_qty" name="productiondata[<?php echo $key;?>][subgrade_qty]" value="<?php echo $productiondata['subgrade_qty']; ?>" class="input-border form-control inputwidth subgrade_qty subgrade_qty_w">						
	</td>
<td class="">
		<div style="display:none" id="productiondata_<?php echo $key;?>_subgrade_lead_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiondata_<?php echo $key;?>_subgrade_lead" name="productiondata[<?php echo $key;?>][subgrade_lead]" value="<?php echo $productiondata['subgrade_lead']; ?>" class="input-border form-control inputwidth subgrade_lead subgrade_lead_w">						
	</td>
<?php } ?>
</tr>
<?php  } ?>
