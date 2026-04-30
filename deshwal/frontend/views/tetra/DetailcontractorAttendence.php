<?php //echo "<pre>"; print_r($detail); die;?>
<!--<?php echo $blockfield_array[$i]['fieldid']; ?> <?php echo "<pre>"; print_r($dumpers); ?>-->


	<tr>
																	<td class=""><b>Code</b></td>
																	<td class=""><b>Shift Operator Name</b></td>
																	<td class=""><b>Role</b></td>
																	<td class=""><b>Att Status</b></td>
																	<td class=""><b>Machine / Dumper No</b></td>
																	<td class=""><b>Equipment Type</b></td>
																</tr>

	<?php $counter=0; foreach ($detail as $sserr) {?>
<tr>						
																									<td class="">
												<span id="contractorattendance_<?php echo $counter;?>_role" class="damageQty text-right damageqtywidth"><?php echo $sserr['code']; ?></span>						

										</td>
														<td class="">
												<span id="contractorattendance_<?php echo $counter;?>_role" class="damageQty text-right damageqtywidth"><?php echo $sserr['shift_operator_name']; ?></span>						

										</td>

																<td class="">
												<span id="contractorattendance_<?php echo $counter;?>_role" class="damageQty text-right damageqtywidth"><?php echo $sserr['role']; ?></span>						

										</td>


										<td class="">
												<span id="contractorattendance_<?php echo $counter;?>_role" class="damageQty text-right damageqtywidth"><?php echo $sserr['att_status']; ?></span>						

										</td>
		<td class="">
												<span id="contractorattendance_<?php echo $counter;?>_role" class="damageQty text-right damageqtywidth"><?php echo $sserr['machine_dumper_no']; ?></span>						

										</td>

					<td class="">
												<span id="contractorattendance_<?php echo $counter;?>_role" class="damageQty text-right damageqtywidth"><?php echo $sserr['equipment_type']; ?></span>						

										</td>



											</tr>
		<?php $counter++; } ?>
