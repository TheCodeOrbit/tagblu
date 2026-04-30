<?php $ModuleName=$ActionList['ModuleName'];
	$user_id=$_SESSION[Yii::app()->params['dirName']."_id"];
	//echo "<br>U r in detail view";
	//die;
	$ActionName=$ActionList['ActionName'];
	$ModuleLabel=$ActionList['ModuleLabel'];
	$currentdate = date('Y-m-d');
	//echo "<br>ModuleName=$ModuleName and ActionName=$ActionName";
	$ActionUrl=Yii::app()->createAbsoluteUrl($ModuleName)."/";
?>
<div class="body-container" id="fullpage"><!-- fullpage starts -->
	<?php //include_once 'LeftSide.php';?>
		<div class="topcontent-details"><!-- topcontent starts -->
			 <div class="body-menu flex jc-spbt ai-cntr">
                <div>
                    <p class="body-heading"><?php echo 'Detail '.$ModuleLabel;?> Data</p>
                </div>
          </div> 
      </div>     
		<!-- topcontent end -->
		<!-- body parts of main details page --> 
		<div class="body-container ">
			
				<?php if($module=='users'){ ?>
				<div class="col-sm-10">
					<?php } ?>
					<?php
					//echo "<pre>";
					//print_r($ColumnList->DetailBlocks);
					//die;

					foreach($ColumnList->DetailBlocks as $BlockKey=>$Block):
					//echo "<pre>";
					//print_r($Block);
					//die;
					?>

					<div class="DetailView-recordDetails">
						
						<?php if($Block->blocktype=="Simple"):?>
							<div class="mb-4 border border-3 border-primary border-radius-10 d-flex justify-content-evenly align-items-center p-4 mx-2rem">
								<?php foreach ($Block->DetailFields as $key=> $Field): ?>
									<div class="d-flex" id="lbl_<?php echo $Field->fieldname; ?>">
										<p class="input-heading input-heading--custom-padding d-flex justify-content-center align-items-center me-4"><?php echo $Field->fieldlable; ?></p>
										<p class="input-border d-flex align-items-center p-3">
										
										<?php
											// detail view records value
											if($Field->tablename=="Entity"){
											echo date('d/m/Y h:i:s',strtotime($Record->EntityRecord->{$Field->columnname})); 
											}else{ 
											if($Field->uitype=="6" ){ 
												if($Record->{$Field->columnname} == '0'){
												echo $Record->{$Field->columnname} = 'No';
												}else{
												echo $Record->{$Field->columnname} = 'Yes';
												}
											}
											else if($Field->uitype=="13" ){ 
											$dt=date('Y-m-d',strtotime($Record->{$Field->columnname}));
												if($dt == '' or $dt =='1970-01-01' or $dt=='-0001-11-30'){
												echo $Field->columnname =" ";	
												}else{
												echo $Field->columnname = date('d/m/Y',strtotime($Record->{$Field->columnname})); 
												}
											}else if($Field->uitype=="15" ){ 
												if($Record->{$Field->columnname} == '' or $Record->{$Field->columnname} =='1970-01-01' or $Record->{$Field->columnname} =='-0001-11-30'){
												echo $Field->columnname =" ";	
												}else{
												echo $Field->columnname = date('m/Y',strtotime($Record->{$Field->columnname})); 
												}
											}else if($Field->uitype=="17" ){ 
												$dt=date('Y-m-d',strtotime($Record->{$Field->columnname}));
												if($dt == '' or $dt =='1970-01-01' or $dt=='-0001-11-30'){
												echo $Field->columnname =" ";	
												}else{
												echo $Field->columnname = date('d/m/Y',strtotime($Record->{$Field->columnname})); 
												}
											}else if($Field->uitype=="19" ){ 
												$dt=date('Y-m-d',strtotime($Record->{$Field->columnname}));
												if($dt == '' or $dt =='1970-01-01' or $dt=='-0001-11-30'){
												echo $Field->columnname =" ";	
												}else{
												echo $Field->columnname = date('d/m/Y',strtotime($Record->{$Field->columnname})); 
												}
											}else if($Field->uitype=="16" ){ 
											echo $Field->columnname = date('d/m/Y H:i:s',strtotime($Record->{$Field->columnname})); 
											}else{ 
												echo strip_tags($Record->{$Field->columnname}); 
												}	 
											}	
										?> 
										
										</p>
									</div>
								<?php endforeach;?>
							</div>
							<input type="hidden" name="module" id="module" value="<?php echo $ModuleName; ?>">

						<?php elseif($Block->blocktype=="SimpleTwoCol"): ?>
							<div class="body-outline h-auto mb-5">
								<div class="input-heading mt-2 mb-2 mx-2"><?php echo $Block->blocklable;?></div>

								<div class="simple-table d-flex justify-content-between">
								<div class="simple-table__container" style="<?php if($Block->blockid !=6){ echo 'border:none' ;}?>">
								<?php 
									$count_tr =0;
									foreach ($Block->DetailFields as $key=> $Field){ ?>
									<?php if($count_tr%2 == 0){?>
										<div class="d-flex justify-content-between mb-3">
										<?php if($Field['uitype']!=2){?>
											<div class="input-heading simple-table__container__heading"><label><?php echo $Field->fieldlable; ?></label></div>
										
											<input type="text" value="<?php echo $Record->{$Field->columnname} ?>" name="<?php $Record->{$Field->columnname}?>" class="input-border simple-table__container__input" readonly/> 
										<?php } ?>
										</div>
										
										<?php }$count_tr++; } ?>

										</div>
										<div class="simple-table__container" style="<?php if($Block->blockid !=6){ echo 'border:none' ;}?>">
										<?php 
									$count_tr =0;
									foreach ($Block->DetailFields as $key=> $Field){ ?>
									<?php if($count_tr%2 != 0){?>
										<div class="d-flex justify-content-between mb-3">
										<?php if($Field['uitype']!=2){?>
											<div class="input-heading simple-table__container__heading"><label><?php echo $Field->fieldlable; ?></label></div>
										
											<input type="text" value="<?php echo $Record->{$Field->columnname} ?>" name="<?php $Record->{$Field->columnname}?>" class="input-border simple-table__container__input" readonly/> 
										<?php } ?>
										</div>
	
										<?php }$count_tr++; } ?>

										</div>
										</div>

								

							<!--	<table>
									<?php 
									$count_tr =0;
									foreach ($Block->DetailFields as $key=> $Field){ ?>
<?php if($count_tr%2 == 0){?>
<tr class="table-primary">
	<?php } ?>
	<th style="width: 25%"><?php echo $Field->fieldlable; ?></th>
	<td style="width: 25%;" class="text-center input-border"><?php echo $Record->{$Field->columnname}; ?></td>
	<?php if($count_tr%2 != 0){?>
</tr>
<?php } ?>
									<?php $count_tr++; } ?>
									
											
								</table>-->
							</div>
						<?php	elseif($Block->blocktype=="Multiple"):?>
							<div class="body-outline h-auto mb-5">
							<?php if($Block->blocklable){ ?>
							<div class="input-heading mx-2 mt-2 mb-2"><?php echo $Block->blocklable;?></div>
							<?php } ?>
								<table  id="block_<?php echo $Block->blockid;?>" class="table-view table table-striped">
									<thead>
										<tr class="table-primary">
											<?php foreach ($Block->DetailFields as $key=> $Field):?>
												<th><?php echo strip_tags($Field->fieldlable);?></th>
											<?php endforeach;?>
										</tr>
									</thead>
										
									<tbody>
										<?php 
											if(count($Record[Multiple_Records][$Block->blockid])>0):
												$rec_counter = 0;
											foreach($Record[Multiple_Records][$Block->blockid] as $MPKey=>$Multiple_Record):?>
												<tr>
													<?php foreach($Block->DetailFields as $key=> $Field):?>
														<td class="text-center">
															<?php 
															if($Field['uitype']==14){?>
																<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#<?php echo $Field->columnname.$rec_counter.$Block->blockid;?>">
																	Reasons
																	</button>

																	<!-- Modal -->
																	<div class="modal fade" id="<?php echo $Field->columnname.$rec_counter.$Block->blockid;?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
																	<div class="modal-dialog">
																		<div class="modal-content">
																		<div class="modal-header">
																			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
																		</div>
																		<div class="modal-body">
																			<div class="d-flex justify-content-between">
																				<div class="input-heading" style="width:50%">Stopage Reason</div>
																				<div class="input-heading" style="width:50%">Hours</div>
																			</div>
																			<?php 
																			if(count($Multiple_Record->reasons)>0){
																				foreach($Multiple_Record->reasons as $reason_record){ ?>
																					<div class="d-flex justify-content-between anchor-class">
																						<select class="input-border form-control" readonly>
																							<?php $PickList=new PickList;//$Field->tablename
																								$fieldoptionsReason=$PickList->getPickListReasonOptionEdit($reason_record->reason_id);
																								echo($fieldoptionsReason);
																							?>
																						</select>
																						<input value="<?php echo $reason_record->loss_hour; ?>" readonly>
																					</div>
																				<?php } 
																			}?>
																		</div>
																		<div class="modal-footer">
																			<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
																		</div>
																		</div>
																	</div>
																	</div>
															<?php }else{
																echo strip_tags($Multiple_Record->{$Field->columnname});
															}
															?>	
														</td>
													<?php endforeach;?>			
												</tr>
											<?php $rec_counter++; endforeach;?> 	
										<?php endif;?>
									</tbody>
								
								</table><!-- table ends -->
							</div>
						<?php endif;?> <!-- Multiple Block endif -->
					</div><!-- detail view recordDetails ends -->	
					<?php endforeach;?>
				</div>

				<?php if($module == 'users'){?>
					<?php include_once 'RightSide.php' ?>
				<?php }?>

				<!--<div class="rightside-details col-sm-2">
					<ul class="quicklinkdiv-right-container nav nav-pills nav-stacked">
					<li class="quicklinkdiv-right"><a href="<?php echo $ActionUrl;?>list" class="list-group-item">Contact</a></li>
					</ul>
				</div>--><!-- right side ends -->
			
		</div><!-- container ends -->
	<!--</div>--><!-- main details page ends -->
</div><!-- fullpage ends -->
