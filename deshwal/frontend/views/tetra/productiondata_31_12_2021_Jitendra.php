<?php foreach ($ALLLIST as $key=>$productiondata) { ?>
	<tr class="prodlist">
	<td class="">
		<div style="display:none" id="productiondata_<?php echo $key;?>_uom_em" class="ajxwarning errorMessage tooltip_img"></div>
		<select id="productiondata_<?php echo $key;?>_uom" class="uom form-control uom_w" name="productiondata[<?php echo $key;?>][uom]">
		<option value="<?php echo $productiondata['monthsid']; ?>" selected><?php echo $productiondata['uom']; ?></option>
		</select>	
	</td>

	<td class="">
		<div style="display:none" id="productiondata_<?php echo $key;?>_ob_removal_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiondata_<?php echo $key;?>_ob_removal" name="productiondata[<?php echo $key;?>][ob_removal]" value="<?php echo $productiondata['ob_removal']; ?>" class="form-control ob_removal ob_removal_w">						
	</td>

	<td class="">
		<div style="display:none" id="productiondata_<?php echo $key;?>_ob_lead_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiondata_<?php echo $key;?>_ob_lead" name="productiondata[<?php echo $key;?>][ob_lead]" value="<?php echo $productiondata['ob_lead']; ?>" class="form-control ob_lead ob_lead_w">						
	</td>

	<td class="">
		<div style="display:none" id="productiondata_<?php echo $key;?>_rom_production_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiondata_<?php echo $key;?>_rom_production" name="productiondata[<?php echo $key;?>][rom_production]" value="<?php echo $productiondata['rom_production']; ?>" class="form-control rom_production rom_production_w">						
	</td>

	<td class="">
		<div style="display:none" id="productiondata_<?php echo $key;?>_rom_lead_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiondata_<?php echo $key;?>_rom_lead" name="productiondata[<?php echo $key;?>][rom_lead]" value="<?php echo $productiondata['rom_lead']; ?>" class="form-control rom_lead rom_lead_w">						
	</td>

	<td class="">
		<div style="display:none" id="productiondata_<?php echo $key;?>_yield_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiondata_<?php echo $key;?>_yield" name="productiondata[<?php echo $key;?>][yield]" value="<?php echo $productiondata['yield']; ?>" class="form-control yield yield_w" >						
	</td>
	
	<td class="">
		<div style="display:none" id="productiondata_<?php echo $key;?>_washed_coal_production_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiondata_<?php echo $key;?>_washed_coal_production" name="productiondata[<?php echo $key;?>][washed_coal_production]" value="<?php echo $productiondata['washed_coal_production']; ?>" class="form-control washed_coal_production washed_coal_production_w" readonly>						
	</td>

	<td class="">
		<div style="display:none" id="productiondata_<?php echo $key;?>_reject_production_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiondata_<?php echo $key;?>_reject_production" name="productiondata[<?php echo $key;?>][reject_production]" value="<?php echo $productiondata['reject_production']; ?>" readonly class="form-control reject_production reject_production_w" >						
	</td>

	<td class="">
		<div style="display:none" id="productiondata_<?php echo $key;?>_coal_dispatch_from_siding_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiondata_<?php echo $key;?>_coal_dispatch_from_siding" name="productiondata[<?php echo $key;?>][coal_dispatch_from_siding]" value="<?php echo $productiondata['coal_dispatch_from_siding']; ?>" class="form-control coal_dispatch_from_siding coal_dispatch_from_siding_w" >						
	</td>
	
	<td class="">
		<div style="display:none" id="productiondata_<?php echo $key;?>_reject_coal_dispatch_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiondata_<?php echo $key;?>_reject_coal_dispatch_from_siding" name="productiondata[<?php echo $key;?>][reject_coal_dispatch]" value="<?php echo $productiondata['reject_coal_dispatch']; ?>" class="form-control reject_coal_dispatch reject_coal_dispatch_w" >						
	</td>



<td class="">
		<div style="display:none" id="productiondata_<?php echo $key;?>_rakedipatch_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="productiondata_<?php echo $key;?>_rakedipatch" name="productiondata[<?php echo $key;?>][rakedipatch]" value="<?php echo $productiondata['rakedipatch']; ?>" class="form-control inputwidth rakedipatch rakedipatch_w">						
	</td>



</tr>
<?php  } ?>
