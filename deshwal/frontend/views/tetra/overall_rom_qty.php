<?php foreach ($gettotallist as $key=>$overall_rom_qty) { ?>
<tr class="prodlist">
	<td class="">
		<div style="display:none" id="overall_rom_qty_<?php echo $key;?>_trip_qty_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="overall_rom_qty_<?php echo $key;?>_trip_qty" name="overall_rom_qty[<?php echo $key;?>][trip_qty]" value="<?php echo $overall_rom_qty['trip_qty'];?>" class="trip_qty form-control" readonly="readonly">					
	</td>

	<td class="">
		<div style="display:none" id="overall_rom_qty_<?php echo $key;?>_wm_qty_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="overall_rom_qty_<?php echo $key;?>_wm_qty" name="overall_rom_qty[<?php echo $key;?>][wm_qty]" value="<?php echo $overall_rom_qty['wm_qty']; ?>" class="form-control" readonly>					
	</td>

	<td class="">
		<div style="display:none" id="overall_rom_qty_<?php echo $key;?>_average_lead_em" class="ajxwarning errorMessage tooltip_img"></div>
		<input type="text" maxlength="25" id="overall_rom_qty_<?php echo $key;?>_average_lead" name="overall_rom_qty[<?php echo $key;?>][average_lead]" value="<?php echo $overall_rom_qty['average_lead'];?>" class="average_lead form-control" readonly>					
	</td>
</tr>

<?php }?>
