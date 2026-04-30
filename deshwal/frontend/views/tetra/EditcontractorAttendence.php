<?php// echo "<pre>"; print_r($ssers); die;?>
<!--<?php echo $blockfield_array[$i]['fieldid']; ?> <?php echo "<pre>"; print_r($dumpers); ?>-->
	<?php $counter=0; foreach ($ssers as $sserr) {?>
<tr>
<!--
<td id="contractorattendance_<?php echo $counter;?>_Delete" class="contractorattendance text-center" style="padding: 5px; border: 1px solid rgb(221, 221, 221);"><a href="javascript:void;"><span class="glyphicon glyphicon-trash"></span></a></td>-->


						
																							<td class="">
						<div style="display:none" id="contractorattendance_<?php echo $counter;?>_Code_em" class="ajxwarning errorMessage tooltip_img"></div>
							
												<input type="text" maxlength="25" id="contractorattendance_<?php echo $counter;?>_Code" name="contractorattendance[<?php echo $counter;?>][Code]" value="<?php echo $sserr['code']; ?>" class="form-control productQty text-right challanqtywidth" readonly="readonly">						
		</td>

												<td class="">
		<div style="display:none" id="contractorattendance_<?php echo $counter;?>_operatorname_em" class="ajxwarning errorMessage tooltip_img"></div>
							
												<input type="text" maxlength="25" id="contractorattendance_<?php echo $counter;?>_operatorname" name="contractorattendance[<?php echo $counter;?>][operatorname]" value="<?php echo $sserr['shift_operator_name']; ?>" class="form-control receiptQty text-right receiptqtywidth" readonly="readonly">						
							</td>

														<td class="">
									

					
						<div style="display:none" id="contractorattendance_<?php echo $counter;?>_role_em" class="ajxwarning errorMessage tooltip_img"></div>
							
												<input type="hidden" maxlength="25" id="contractorattendance_<?php echo $counter;?>_role" name="contractorattendance[<?php echo $counter;?>][role]" value="<?php echo $sserr['roledid']; ?>" class="form-control damageQty text-right damageqtywidth" readonly="readonly">		

			<input type="text" maxlength="25" id="contrance_<?php echo $counter;?>_role" name="contrance_<?php echo $counter;?>_role" value="<?php echo $sserr['role']; ?>" class="form-control damageQty text-right damageqtywidth" readonly="readonly">					
							



										</td>


									<td class="">
									

					
						<div style="display:none" id="contractorattendance_<?php echo $counter;?>_status_em" class="ajxwarning errorMessage tooltip_img"></div>
							
											<!--	<input type="text" maxlength="25" id="Manpower_'<?php echo $counter;?>'_status" name="Manpower_'<?php echo $counter;?>'_status" value="<?php echo $users['status']; ?>" class="form-control ProductBatchNo batchwidth" readonly="readonly">	-->
<select id="contractorattendance_<?php echo $counter;?>_status" class="status form-control" name="contractorattendance[<?php echo $counter;?>][status]">
<!--<option value="">Select an Option</option>-->
<option value="1"<?php if(1==$sserr['att_status']) echo 'selected="selected"'; ?>>Yes</option>
<option value="2"<?php if(2==$sserr['att_status']) echo 'selected="selected"'; ?>>No</option>
</select>					

										</td>

																	<td class="">
									

					
						<div style="display:none" id="contractorattendance_<?php echo $counter;?>_dumperno_em" class="ajxwarning errorMessage tooltip_img"></div>
							
						<select id="contractorattendance_<?php echo $counter;?>_dumperno" class="dumperno status form-control" name="contractorattendance[<?php echo $counter;?>][dumperno]">
<option value="">Select an Option</option>
<?php  foreach ($dumper as $dumpers) {?>

<option value="<?php echo $dumpers['equipment_id']; ?>"<?php if($sserr['machine_dumper_no']==$dumpers['equipment_id']) echo 'selected="selected"'; ?>><?php echo $dumpers['serial_no']; ?></option>

<?php  } ?>
</select>

										</td>

					<td class="">
									<!--<?php echo $users['role']; ?>-->

					
						<div style="display:none" id="contractorattendance_<?php echo $counter;?>_equipment_em" class="ajxwarning errorMessage tooltip_img"></div>
							
												<input type="hidden" maxlength="25" id="contractorattendance_<?php echo $counter;?>_equipment" name="contractorattendance[<?php echo $counter;?>][equipment]" value="<?php echo $sserr['eqptypid']; ?>" class="form-control damageQty text-right damageqtywidth" readonly="readonly">	

<input type="text" maxlength="25" id="contrance_<?php echo $counter;?>_equipment" name="contrance_<?php echo $counter;?>_equipment" value="<?php echo $sserr['equipment_type']; ?>" class="form-control damageQty text-right damageqtywidth" readonly="readonly">						
							



										</td>



											</tr>
		<?php $counter++; } ?>
