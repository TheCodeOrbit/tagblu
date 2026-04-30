<?php foreach ($ALLLIST as $key=>$budget_revenue) { ?>
	<tr class="prodlist">
	<td class="">
		<div style="display:none" id="budget_revenue_<?php echo $key;?>_uom_em" class="ajxwarning errorMessage tooltip_img"></div>
		<select id="budget_revenue_<?php echo $key;?>_uom" class="uom form-control uom_w" name="budget_revenue[<?php echo $key;?>][uom]">
		<option value="<?php echo $budget_revenue['monthsid']; ?>" selected><?php echo $budget_revenue['uom']; ?></option>
		</select>	
	</td>
	<td class="">
		<div style="display:none" id="budget_revenue_<?php echo $key;?>_mining_fees_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="budget_revenue_<?php echo $key;?>_mining_fees" name="budget_revenue[<?php echo $key;?>][mining_fees]" value="<?php echo $budget_revenue[<?php echo $key;?>]['mining_fees']; ?>" class="form-control mining_fees mining_fees_w" readonly>						
	</td>

	<td class="">
		<div style="display:none" id="budget_revenue_<?php echo $key;?>_quality_incentive_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="budget_revenue_<?php echo $key;?>_quality_incentive" name="budget_revenue[<?php echo $key;?>][quality_incentive]" value="<?php echo $budget_revenue[<?php echo $key;?>]['quality_incentive']; ?>" class="form-control quality_incentive quality_incentive_w" readonly>						
	</td>

	<td class="">
		<div style="display:none" id="budget_revenue_<?php echo $key;?>_reject_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="budget_revenue_<?php echo $key;?>_reject" name="budget_revenue[<?php echo $key;?>][reject]" value="<?php echo $budget_revenue[<?php echo $key;?>]['reject']; ?>" class="form-control reject reject_w" readonly>						
	</td>

	<td class="">
		<div style="display:none" id="budget_revenue_<?php echo $key;?>_total_revenue_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="budget_revenue_<?php echo $key;?>_total_revenue" name="budget_revenue[<?php echo $key;?>][total_revenue]" value="<?php echo $budget_revenue[<?php echo $key;?>]['total_revenue']; ?>" class="form-control total_revenue total_revenue_w" readonly>						
	</td>

	<td class="">
		<div style="display:none" id="budget_revenue_<?php echo $key;?>_variable_cost_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="budget_revenue_<?php echo $key;?>_variable_cost" name="budget_revenue[<?php echo $key;?>][variable_cost]" value="<?php echo $budget_revenue[<?php echo $key;?>]['variable_cost']; ?>" readonly class="form-control variable_cost variable_cost_w" >						
	</td>

	<td class="">
		<div style="display:none" id="budget_revenue_<?php echo $key;?>_contribution_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="budget_revenue_<?php echo $key;?>_contribution" name="budget_revenue[<?php echo $key;?>][contribution]" value="<?php echo $budget_revenue[<?php echo $key;?>]['contribution']; ?>" class="form-control contribution contribution_w" readonly>						
	</td>

	<td class="">
		<div style="display:none" id="budget_revenue_<?php echo $key;?>_logistics_cost_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="budget_revenue_<?php echo $key;?>_logistics_cost" name="budget_revenue[<?php echo $key;?>][logistics_cost]" value="<?php echo $budget_revenue[<?php echo $key;?>]['logistics_cost']; ?>" class="form-control logistics_cost logistics_cost_w" readonly>						
	</td>

	<td class="">
		<div style="display:none" id="budget_revenue_<?php echo $key;?>_fixed_cost_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="budget_revenue_<?php echo $key;?>_fixed_cost" name="budget_revenue[<?php echo $key;?>][fixed_cost]" value="<?php echo $budget_revenue[<?php echo $key;?>]['fixed_cost']; ?>" class="form-control fixed_cost fixed_cost_w" readonly>						
	</td>
	
	<td class="">
		<div style="display:none" id="budget_revenue_<?php echo $key;?>_inc_inventory_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="budget_revenue_<?php echo $key;?>_inc_inventory" name="budget_revenue[<?php echo $key;?>][inc_inventory]" value="<?php echo $budget_revenue[<?php echo $key;?>]['inc_inventory']; ?>" readonly class="form-control inc_inventory inc_inventory_w" >						 
	</td>

	<!-- Amit Editing <?php echo $key;?>8-<?php echo $key;?>6-19  -->
	<td class="">
		<div style="display:none" id="budget_revenue_<?php echo $key;?>_operating_ebitda_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="budget_revenue_<?php echo $key;?>_operating_ebitda" name="budget_revenue[<?php echo $key;?>][operating_ebitda]" value="<?php echo $budget_revenue[<?php echo $key;?>]['operating_ebitda']; ?>" readonly class="form-control inputwidth operating_ebitda operating_ebitda_w" >						 
	</td>
	<!-- End -->
	<td class="">
		<div style="display:none" id="budget_revenue_<?php echo $key;?>_expance_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="budget_revenue_<?php echo $key;?>_expance" name="budget_revenue[<?php echo $key;?>][expance]" value="<?php echo $budget_revenue[<?php echo $key;?>]['expance']; ?>" readonly class="form-control inputwidth expance expance_w" >						 
	</td>
	
	<td class="">
		<div style="display:none" id="budget_revenue_<?php echo $key;?>_ebitda_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="budget_revenue_<?php echo $key;?>_ebitda" name="budget_revenue[<?php echo $key;?>][ebitda]" value="<?php echo $budget_revenue[<?php echo $key;?>]['ebitda']; ?>" readonly class="form-control inputwidth ebitda ebitda_w" >						 
	</td>

	<td class="">
		<div style="display:none" id="budget_revenue_<?php echo $key;?>_interest_net_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="budget_revenue_<?php echo $key;?>_interest_net" name="budget_revenue[<?php echo $key;?>][interest_net]" value="<?php echo $budget_revenue[<?php echo $key;?>]['interest_net']; ?>" readonly class="form-control inputwidth interest_net interest_net_w" >						 
	</td>

	<td class="">
		<div style="display:none" id="budget_revenue_<?php echo $key;?>_depreciation_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="budget_revenue_<?php echo $key;?>_depreciation" name="budget_revenue[<?php echo $key;?>][depreciation]" value="<?php echo $budget_revenue[<?php echo $key;?>]['depreciation']; ?>" readonly class="form-control inputwidth depreciation depreciation_w" >						 
	</td>

	<td class="">
		<div style="display:none" id="budget_revenue_<?php echo $key;?>_pbt_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="budget_revenue_<?php echo $key;?>_pbt" name="budget_revenue[<?php echo $key;?>][pbt]" value="<?php echo $budget_revenue[<?php echo $key;?>]['pbt']; ?>" readonly class="form-control inputwidth pbt pbt_w" >						 
	</td>
	<td class="">
		<div style="display:none" id="budget_revenue_<?php echo $key;?>_contingency_ceo_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="budget_revenue_<?php echo $key;?>_contingency_ceo" name="budget_revenue[<?php echo $key;?>][contingency_ceo]" value="<?php echo $budget_revenue[<?php echo $key;?>]['contingency_ceo']; ?>" readonly class="form-control inputwidth contingency_ceo contingency_ceo_w" >						 
	</td>
</tr>
<?php  } ?>
