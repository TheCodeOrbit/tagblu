<?php //print_r($debtors_plan_total);?>

<tr class="prodlist">
	<td class="">
		<div style="display:none" id="debtors_plan_total_0_opening_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="debtors_plan_total_0_opening" name="debtors_plan_total[0][opening]" value="<?php echo $debtors_plan_total[0]['opening']; ?>" class="input-border form-control opening_total opening_w" readonly>						
	</td>

	<td class="">
		<div style="display:none" id="debtors_plan_total_0_overdues_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="debtors_plan_total_0_overdues" name="debtors_plan_total[0][overdues]" value="<?php echo $debtors_plan_total[0]['overdues']; ?>" class="input-border form-control overdues_total overdues_w" readonly>						
	</td>

	<td class="">
		<div style="display:none" id="debtors_plan_total_0_collection_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="debtors_plan_total_0_collection" name="debtors_plan_total[0][collection]" value="<?php echo $debtors_plan_total[0]['collection']; ?>" class="input-border form-control collection_total collection_w" readonly >						
	</td>

	<td class="">
		<div style="display:none" id="debtors_plan_total_0_closing_balance_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="debtors_plan_total_0_closing_balance" name="debtors_plan_total[0][closing_balance]" value="<?php echo $debtors_plan_total[0]['closing_balance']; ?>" class="input-border form-control closing_balance_total closing_balance_w" readonly>						
	</td>

</tr>