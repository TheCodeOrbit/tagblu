<?php 
//print_r($ALLLIST);die;

foreach ($ALLLIST as $key=>$debtors_plan) { ?>
	<tr class="prodlist">
	<td class="">
		<div style="display:none" id="debtors_plan_<?php echo $key;?>_uom_em" class="ajxwarning errorMessage tooltip_img"></div>
		<select id="debtors_plan_<?php echo $key;?>_uom" class="uom form-control uom_w" name="debtors_plan[<?php echo $key;?>][uom]">
		<option value="<?php echo $debtors_plan['monthsid']; ?>" selected><?php echo $debtors_plan['uom']; ?></option>
		</select>	
	</td>
	<td class="">
		<div style="display:none" id="debtors_plan_<?php echo $key;?>_opening_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="debtors_plan_<?php echo $key;?>_opening" name="debtors_plan[<?php echo $key;?>][opening]" value="<?php echo $debtors_plan['opening']; ?>" class="input-border form-control opening opening_w" >						
	</td>

	<td class="">
		<div style="display:none" id="debtors_plan_<?php echo $key;?>_overdues_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="debtors_plan_<?php echo $key;?>_overdues" name="debtors_plan[<?php echo $key;?>][overdues]" value="<?php echo $debtors_plan['overdues']; ?>" class="input-border form-control overdues overdues_w" >						
	</td>

	<td class="">
		<div style="display:none" id="debtors_plan_<?php echo $key;?>_collection_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="debtors_plan_<?php echo $key;?>_collection" name="debtors_plan[<?php echo $key;?>][collection]" value="<?php echo $debtors_plan['collection']; ?>" class="input-border form-control collection collection_w" >						
	</td>

	<td class="">
		<div style="display:none" id="debtors_plan_<?php echo $key;?>_closing_balance_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="debtors_plan_<?php echo $key;?>_closing_balance" name="debtors_plan[<?php echo $key;?>][closing_balance]" value="<?php echo $debtors_plan['closing_balance']; ?>" class="input-border form-control closing_balance closing_balance_w" readonly>						
	</td>

</tr>

<?php  } ?>
