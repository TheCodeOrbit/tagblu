<?php foreach ($ALLLIST as $key=>$collection_revenue) { ?>
	<tr class="prodlist">
	<td class="">
		<div style="display:none" id="collection_revenue_<?php echo $key;?>_uom_em" class="ajxwarning errorMessage tooltip_img"></div>
		<select id="collection_revenue_<?php echo $key;?>_uom" class="uom form-control collection_revenue_form_fields uom_w" name="collection_revenue[<?php echo $key;?>][uom]">
		<option value="<?php echo $collection_revenue['monthsid']; ?>" selected><?php echo $collection_revenue['uom']; ?></option>
		</select>	
	</td>
	<td class="">
		<div style="display:none" id="collection_revenue_<?php echo $key;?>_tompl_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="collection_revenue_<?php echo $key;?>_tompl" name="collection_revenue[<?php echo $key;?>][tompl]" value="<?php echo $collection_revenue[$key]['tompl']; ?>" class="input-border form-control collection_revenue_form_fields tompl tompl_w" >						
	</td>

	<td class="">
		<div style="display:none" id="collection_revenue_<?php echo $key;?>_dispatch_qty_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="collection_revenue_<?php echo $key;?>_dispatch_qty" name="collection_revenue[<?php echo $key;?>][dispatch_qty]" value="<?php echo $collection_revenue[$key]['dispatch_qty']; ?>" class="input-border form-control collection_revenue_form_fields dispatch_qty dispatch_qty_w" >						
	</td>

	<td class="">
		<div style="display:none" id="collection_revenue_<?php echo $key;?>_rake_dispatch_qty_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="collection_revenue_<?php echo $key;?>_rake_dispatch_qty" name="collection_revenue[<?php echo $key;?>][rake_dispatch_qty]" value="<?php echo $collection_revenue[$key]['rake_dispatch_qty']; ?>" class="input-border form-control collection_revenue_form_fields rake_dispatch_qty rake_dispatch_qty_w" >						
	</td>

	<td class="">
		<div style="display:none" id="collection_revenue_<?php echo $key;?>_washed_coal_inventory_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="collection_revenue_<?php echo $key;?>_washed_coal_inventory" name="collection_revenue[<?php echo $key;?>][washed_coal_inventory]" value="<?php echo $collection_revenue[$key]['washed_coal_inventory']; ?>" class="input-border form-control collection_revenue_form_fields washed_coal_inventory washed_coal_inventory_w">						
	</td>

	<td class="">
		<div style="display:none" id="collection_revenue_<?php echo $key;?>_coll_inc_inventory_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="collection_revenue_<?php echo $key;?>_coll_inc_inventory" name="collection_revenue[<?php echo $key;?>][coll_inc_inventory]" value="<?php echo $collection_revenue[$key]['coll_inc_inventory']; ?>" class="input-border form-control collection_revenue_form_fields coll_inc_inventory coll_inc_inventory_w" >						
	</td>

	<td class="">
		<div style="display:none" id="collection_revenue_<?php echo $key;?>_rake_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="collection_revenue_<?php echo $key;?>_rake" name="collection_revenue[<?php echo $key;?>][rake]" value="<?php echo $collection_revenue[$key]['rake']; ?>" class="input-border form-control collection_revenue_form_fields rake rake_w">						
	</td>

	
</tr>

<?php  } ?>
