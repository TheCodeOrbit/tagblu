<?php //echo "<pre>"; print_r($dropdownList); die;?>
<!--<?php echo $blockfield_array[$i]['fieldid']; ?> <?php echo "<pre>"; print_r($dumpers); ?>-->
	<?php $counter=0; foreach ($getdataList as $users) {?>
<tr>

<td id="dailydrilling_data_<?php echo $counter;?>_Delete" class="drilling_dataDelete text-center" style="padding: 5px; border: 1px solid rgb(221, 221, 221);"><a href="javascript:void;"><span class="glyphicon glyphicon-trash"></span></a></td>


						
																							<td class="">
						<div style="display:none" id="dailydrilling_data_<?php echo $counter;?>_no_em" class="ajxwarning errorMessage tooltip_img"></div>
							
												<input type="text" maxlength="25" id="drilling_data_<?php echo $counter;?>_drill_machine_no" name="drilling_data[<?php echo $counter;?>][drill_machine_no]" value="<?php echo $users['equipmentno']; ?>" class="productQty form-control " readonly="readonly">						
		</td>

																				<td class="">
						<div style="display:none" id="drilling_data_<?php echo $counter;?>_area_em" class="ajxwarning errorMessage tooltip_img"></div>
				<select id="drilling_data_<?php echo $counter;?>_working_area" class="area status form-control" name="drilling_data[<?php echo $counter;?>][working_area]">
<option value="">Select an Option</option>
<?php  foreach ($dropdownList as $dumpers) {?>

<option value="<?php echo $dumpers['workingareaid']; ?>"><?php echo $dumpers['workingareaname']; ?></option>

<?php  } ?>
</select>



</td>

																				<td class="">
						<div style="display:none" id="drilling_data_<?php echo $counter;?>_doles_drilled_em" class="ajxwarning errorMessage tooltip_img"></div>
							
												<input type="text" maxlength="25" id="drilling_data_<?php echo $counter;?>_no_of_holes_drilled" name="drilling_data[<?php echo $counter;?>][no_of_holes_drilled]" value="" class="noofholes form-control ">						
		</td> 

																				<td class="">
						<div style="display:none" id="drilling_data_<?php echo $counter;?>_avg_burden_em" class="ajxwarning errorMessage tooltip_img"></div>
							
												<input type="text" maxlength="25" id="drilling_data_<?php echo $counter;?>_avg_burden" name="drilling_data[<?php echo $counter;?>][avg_burden]" value="" class="avgburden form-control ">						
		</td>

																				<td class="">
						<div style="display:none" id="dailydrilling_<?php echo $counter;?>_hole_depth_em" class="ajxwarning errorMessage tooltip_img"></div>
							
												<input type="text" maxlength="25" id="drilling_data_<?php echo $counter;?>_avg_hole_depth" name="drilling_data[<?php echo $counter;?>][avg_hole_depth]" value="" class="avghole form-control ">						
		</td>

																				<td class="">
						<div style="display:none" id="dailydrilling_<?php echo $counter;?>_avg_spacing_em" class="ajxwarning errorMessage tooltip_img"></div>
							
												<input type="text" maxlength="25" id="drilling_data_<?php echo $counter;?>_avg_spacing" name="drilling_data[<?php echo $counter;?>][avg_spacing]" value="" class="avgspacing form-control " class="avgspacing">						
		</td>



																							
											</tr>
		<?php $counter++; } ?>
