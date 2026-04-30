<style>
	.width-30perc {
		width: 30%;
	}

	.table-box{
		padding: 0.1rem;
		background: var(--clr-white);
		border-radius: 2px;
	}
</style>

<?php	
	$obj_name="";
	foreach ($Block->Fields as $key=> $Field):
		if($obj_name=="")
		{
			$obj_name=$Field->tablename;
			//if($obj_name=="ReceiveProduct")
			//$obj_name="ReceiptProduct";
			break;
		}
	endforeach;
?>

<input type="hidden" name="multiple_block_id" id="multiple_block_id" value="block_<?php echo $Block->blockid;?>" />
<!-- Multiple Block Code Start -->
<?php if($Block->blocklable){ //echo $Block->blocktype;
	//	echo $Block->blockid;
	?>
	<div data-simplebar class="body-outline mb-3 h-auto <?php 
	if ($Block->blockid==34) { 
		echo "coal-grid-table-1";
	}elseif ($Block->blockid==35) {
		echo "coal-grid-table-2";
	}elseif($Block->blockid==36){
		echo "coal-grid-table-3";
	}elseif($Block->blockid==37){
		echo "coal-grid-table-5";
	}elseif($Block->blockid==38){
		echo "coal-grid-table-4";
	}else{
		echo "coal-grid-table-6";
	}?>">
			
				<table id="Tbl_<?php echo $obj_name;?>">
					<thead>
						<tr>
							<th colspan="20">
								<?php if($Block->blockid==3 || $Block->blockid==5 || $Block->blockid==93){}else{ ?>
									<div class="d-flex justify-content-between">
										<div>
											<svg width="16" height="14" viewBox="0 0 12 10" fill="none" xmlns="http://www.w3.org/2000/svg" class="table-box cursor-pointer" id="">
												<rect x="1" y="1" width="9.32269" height="8" fill="white" stroke="#3D89CF" stroke-width="2"></rect>
												<rect x="3.32422" y="4" width="4.52908" height="2" fill="#3D89CF"></rect>
											</svg>
										</div>
										<div class="text-capitalize m-auto">
											<?php echo (($Block->blockid==65 and $MineType=="Iron") ? $Block->ironblocklabel : $Block->blocklable);?>
										</div>
									</div>
								<?php } ?>
							</th>
						</tr>
						<tr>
							<?php foreach ($Block->Fields as $key=> $Field): ?>
								<th><?php echo $Field->fieldlable;?></th>
							<?php endforeach;?>
							<?php //if($ActionName=="Create"){ ?>
								<th class="deleteTool">Tools</th>
							<?php //}?>
						</tr>
					</thead>
				<tbody id="prodlist_<?php echo $obj_name;?>">
					<?php
					if($ModuleName=='ehs' && $ActionName=="Create"){
						$this->productBlockEHS('1','93');
						$this->productBlockEHS('2','93');
						$this->productBlockEHS('3','93');
						$this->productBlockEHS('4','93');
					}
					if($ModuleName=='water_management' && $ActionName=="Create"){
						$this->productBlockEHS('1',$Block->blockid);
					}
					if($ModuleName=='screening_crushing' && $ActionName=="Create"){
						$this->productBlockEHS('1',$Block->blockid);
					}
					?>
			<?php
				$cnt_multiple_product=count($Record[Multiple_Records][$Block->blockid]);
				//$cnt_multiple_product=1;
				$tablename2="depot";
				$modelss=new Reference($tablename2);
				if($Field->edit_view==1) {
					$aa="$MPkey";
					if($_SESSION['countpro'] == '') {	
						$aa++;			
						//$_SESSION['countpro'] =$aa;
						$_SESSION['countpro'] =$cnt_multiple_product;
					}
				}
			echo "<script type='text/javascript'>cnt_multiple_product='$cnt_multiple_product';cnt_multiple_product=parseInt(cnt_multiple_product)+1;</script>";

			//$cnt_multiple_product=1;
			$reason_counter =0;
					if($cnt_multiple_product>0){
			foreach($Record[Multiple_Records][$Block->blockid] as $MPkey=> $Multiple_Record){ $tax_html="";?>
					<?php //echo "<br>cnt_multiple_product >0  case";?>
					<tr class="prodlist">
					<?php
					//print_r($Multiple_Record);
					//die;
					foreach($Block->Fields as $key=> $Field){
					//print_r($Field);
					//die;
					?>
					
					<?php	if($Field['uitype']==1){
						if($ModuleName == 'p_reconciliation' || $ModuleName == 'ce_p_reconciliation'){?>
						<td class="<?php echo $Field->td_classname;?>">
							<?php $MPkeys =$MPkey +1;
							$fldname="$Field->fieldname"; 						
							?>	
							<?php echo $form->error(${$obj_name},"[$MPkeys]$Field->fieldname", array('class'=>'ajxwarning errorMessage tooltip_img')); ?>				
							<?php echo $form->textField(${$obj_name},"[$MPkeys]$Field->fieldname", array ('class' => $Field->classname,'value'=>$Multiple_Record->{$Field->fieldname},'readonly' => $readonly,'onkeyup' => $blastintfunc));?>
						</td><?php } else { ?>
						<td class="<?php echo $Field->td_classname;?>">
							<?php $counterp="$MPkey";
							$fldname="$Field->fieldname"; 						
							?>	
							<?php echo $form->error(${$obj_name},"[$MPkey]$Field->fieldname", array('class'=>'ajxwarning errorMessage tooltip_img')); ?>				
							<?php echo $form->textField(${$obj_name},"[$MPkey]$Field->fieldname", array ('class' => $Field->classname . ' input-border','value'=>$Multiple_Record->{$Field->fieldname},'readonly' => $readonly,'onkeyup' => $blastintfunc));?>
							<div class="error-container hide">All errors displayed Here</div>
						</td>

					<?php } ?>
					<?php }elseif($Field['uitype']==14){ ?>
						<td>
						<button type="button" value="Stoppage reason"  class="btn btn-primary form-control target-button" data-bs-toggle="modal" data-bs-target="#<?php echo $obj_name.'_'.$reason_counter.'_'.$Field->fieldname.'_reason'; ?>">Stoppage reason<i class="pe-none"></i></button>

						<div class="modal fade" id="<?php echo $obj_name.'_'.$reason_counter.'_'.$Field->fieldname.'_reason';?>" tabindex="-1" aria-labelledby="exampleModal" aria-hidden="true">
							<div class="modal-dialog modal-dialog-centered modal-xl">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
									</div>
										<div class="modal-body position-relative" data-que="<?php echo $reason_counter;?>">
											<div class="d-flex justify-content-between">
												<div class="input-heading" style="width:50%">Stopage Reason</div>
												<div class="input-heading" style="width:50%">Hours</div>
											</div>
											<?php 
											if(count($Multiple_Record->reasons)>0){
												foreach($Multiple_Record->reasons as $reason_record){ ?>
													<div class="d-flex justify-content-between anchor-class">
														<select class="input-border form-control" name="<?php echo $Field->tablename;?>[<?php echo $reason_counter;?>][reason][]">
															<?php $PickList=new PickList;//$Field->tablename
																//$parent_record = Yii::app()->request->getParam('Record');
																if($Block->blockid == "65"){
																	$reason_attr_name = "coal_extraction_id";
																}else if($Block->blockid == "101"){
																	$reason_attr_name = "sc_reason_id";
																}
																$fieldoptionsReason=$PickList->getPickListReasonOptionEdit($reason_record->reason_id);
																echo($fieldoptionsReason);
															?>
														</select>
														<input placeholder="hh:mm:ss" maxlength="8" pattern="^((\d+:)?\d+:)?\d*$" value="<?php echo $reason_record->loss_hour; ?>" class="without_ampm input-border form-control" name="<?php echo $Field->tablename;?>[<?php echo $reason_counter;?>][loss_hour][]">
													</div>
												<?php } 
											}else{?>
												<div class="position-relative">
													<div class="d-flex justify-content-between anchor-class">
														<select class="input-border form-control" name="<?php echo $Field->tablename;?>[<?php echo $reason_counter;?>][reason][]">
															<?php $PickList=new PickList;
																$fieldoptionsReason=$PickList->getPickListReasonOption();
																echo $fieldoptionsReason;
															?>
														</select>
														<input placeholder="hh:mm:ss" maxlength="8" pattern="^((\d+:)?\d+:)?\d*$" class="without_ampm input-border form-control" name="<?php echo $Field->tablename;?>[<?php echo $reason_counter;?>][loss_hour][]">
													</div>
													
												</div>
											<?php }
											?>
											
										</div>
										<div class="modal-footer">
											<!-- <input class="btn btn-primary btn-custom" type="submit" name="yt0" value="Save">
											<input name="btncancel" class="btn btn-outline-danger btn-custom me-5" type="submit" value="Discard"> -->
											<button type="button" class="btn btn-primary btn-custom add-rows-to-pop" >
												<svg width="16" height="16" viewBox="0 0 16 16">
													<path d="M6 16H10V10H16V6H10V0H6V6H0V10H6V16Z" fill="#ffffff"/>
												</svg> 
											</button>
											<button type="button" class="btn btn-outline-danger btn-custom me-5" data-bs-dismiss="modal">Close</button>
										</div>
									<!-- </div>	 -->
								</div>
							</div>
						</div>  

						
						
			
					</td>
					<?php $reason_counter++;}elseif($Field['uitype']==13){ $MPkeys =$MPkey +1; ?>
					<td>
						<div id="<?php echo $obj_name.'_'.$MPkeys.'_'.$Field->fieldname.'_em_'; ?>" class="ajxwarning errorMessage <?php echo $tooltip_class;?> bb1" style="display:none;"></div>
						<input type="hidden" name="<?php echo $obj_name.'['.$MPkeys.']'.'['.$Field->fieldname.']'; ?>" id="<?php echo $obj_name.'_'.$MPkeys.'_'.$Field->fieldname.'_dt'; ?>" value="<?php echo $Multiple_Record->{$Field->fieldname};?>" />
						<input class="form-control dtpick <?php echo $Field->classname;?>" type="text" id="<?php echo $obj_name.'_'.$MPkeys.'_'.$Field->fieldname; ?>" value="<?php echo date('d-m-Y',strtotime($Multiple_Record->{$Field->fieldname}));?>" readonly autocomplete="off" />
					</td>

					<?php } elseif($Field['uitype']==27){ $disp_dt=(($Multiple_Record->{$Field->fieldname} !='' && $Multiple_Record->{$Field->fieldname} !='0000-00-00 00:00:00') ? date('d-m-Y H:i',strtotime($Multiple_Record->{$Field->fieldname})) : '');?>
						<td class="<?php echo $Field->td_classname;?>">

						<?php echo $form->error(${$obj_name},"[$MPkey]$Field->fieldname", array('class'=>'ajxwarning errorMessage ')); ?>
						<input  type="hidden" name="<?php echo $obj_name.'['.$MPkey.']'.'['.$Field->fieldname.'create]'; ?>" id="<?php echo $obj_name.'_'.$MPkey.'_'.$Field->fieldname.'_jqdtEditoption'; ?>" value="2" />

						<input  type="hidden" name="<?php echo $obj_name.'['.$MPkey.']'.'['.$Field->fieldname.']'; ?>" id="<?php echo $obj_name.'_'.$MPkey.'_'.$Field->fieldname.'_jqdt'; ?>" value="<?php echo $Multiple_Record->{$Field->fieldname};?>" />
				
						<input class="form-control inputwidth jqdt <?php echo $Field->classname;?>" type="text" id="<?php echo $obj_name.'_'.$MPkey.'_'.$Field['fieldname'];?>" value="<?php echo $disp_dt;?>" autocomplete="off" />

						<!--<img style="height:20px; position:relative; right:25px; top:3px; width: 20px;" src="<?php echo Yii::app()->request->baseUrl; ?>/images/calendar/cal_icon.png" alt="calendar">-->


					<?php } elseif($Field['uitype']==15){?>
						<td class="<?php echo $Field->td_classname;?>">
							<?php echo $form->textField(${$obj_name},"[$MPkey]$Field->fieldname", array ('class' => $Field->classname,'value'=>$Multiple_Record->{$Field->fieldname}));?>
						</td>

					<?php }elseif($Field['uitype']==8){
						if($ModuleName == 'p_reconciliation' || $ModuleName == 'ce_p_reconciliation'){
							$MPkeys =$MPkey +1; ?>
							<td class="<?php echo $Field->td_classname;?>">
								<?php echo $form->error(${$obj_name},"[$MPkey]$Field->fieldname", array('class'=>'ajxwarning errorMessage tooltip_img')); ?>
								<?php echo $form->dropDownList(${$obj_name},"[$MPkeys]$Field->fieldname", $Field['fieldoptions'],array('class' => $Field['classname'],'value'=>$Multiple_Record->{$Field->fieldname},'empty'=>'Select an Option','options' => array($Multiple_Record->{$Field->fieldname}=>array('selected'=>true)))); ?>
							</td>

						<?php } else { ?>
						<td class="<?php echo $Field->td_classname;?>">
							<div id="<?php   if($ModuleName == 'logisticsiding' and $Field->tablename=='siding_receipt'){echo $Field->tablename.'_'.$MPkey.'_'.$Field->columnname.'_em_';} else{  echo $Field->columnname.'id'.$MPkey.'_em_';  } ?>" class="ajxwarning errorMessage tooltipImages bb2" style="display:none">Stock Type cannot be blank.</div>
							<?php	//echo $form->textField(${$obj_name},"[$MPkey]$Field->fieldname", array ('class' => $Field->classname,'value'=>$Multiple_Record->{$Field->fieldname})); ?>
							<div class="d-flex flex-col">
								<?php echo $form->dropDownList(${$obj_name},"[$MPkey]$Field->fieldname", $Field['fieldoptions'],array('class' => 'h5 input-border p-2 mb-0','aria-label'=>'select example','value'=>$Multiple_Record->{$Field->fieldname},'empty'=>'Select an Option','options' => array($Multiple_Record->{$Field->fieldname}=>array('selected'=>true)))); ?>
								<div class="error-container hide">All errors displayed Here</div>
							</div>
						</td>

					<?php }} elseif($Field['uitype']==22){?>
						<td class="<?php echo $Field->td_classname;?> multi_chosen">
							<?php if($RecordID !=''){
								$vals	= explode(",",$Multiple_Record->{$Field->fieldname});
								$selected =array();
								foreach($vals as $val){
								$selected[$val] = array('selected' => 'selected');
								}
							}
							echo $form->listBox(${$obj_name},"[$MPkey]$Field->fieldname",$Field['fieldoptions'],array('empty' => 'Select an Option','class'=>'form-control multi-select resean-select inputwidth','id'=>$attr_id,'multiple' => 'true','value'=>$Multiple_Record->{$Field->fieldname},'options' =>$selected));?>
						</td>

					<?php } elseif($Field['uitype']==12){?>
						<td class="<?php echo $Field->td_classname;?>">	<!--uitype is 12-->
							<span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
							<div class="input-group">
								<!--<span class="transponame input-group-addon">
									<span class="glyphicon glyphicon-remove-circle cursorPointer text-info" type="button" onclick="<?php echo $obj_name;?>RemoveValue('<?php echo $Field->fieldname;?>','<?php echo $MPkey;?>');"></span>
								</span>-->
								<!--<span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>-->
								<?php	//echo $form->textField(${$obj_name},"[$MPkey]$Field->fieldname", array ('id'=>'productid'.$MPkey,'class' => $Field->classname,'value'=>$Field->reffieldvalue)); ?>
								<input type="text" id="<?php echo $Field->fieldname.$MPkey;?>" name="<?php echo $Field->fieldname.$MPkey;?>" value="<?php echo $Multiple_Record->{$Field->getRelatedDisplayFieldName};?>" class="<?php echo $Field->classname;?>" readonly>
								<!--<input type="hidden" value="<?php echo $Multiple_Record->{$Field->fieldname};?>" id="<?php echo "productidid".$MPkey;?>" name ="<?php echo $attr_name;?>" >-->
								<?php echo $form->hiddenField(${$obj_name},"[$MPkey]$Field->fieldname", array ('id'=>$Field->fieldname.'id'.$MPkey,'value'=>$Multiple_Record->{$Field->fieldname})); ?>
								<!--<?php echo $form->error($model,$field['fieldname'], array('class'=>'ajxwarning errorMessage')); ?>-->

								<!--<span class="transearch input-group-addon">
									<?php if($invmngrule=="2"){ ?>
									<span type="button" class="glyphicon glyphicon-search cursorPointer text-info" style="pointer-events: none" data-toggle="modal" data-target="#myModal22"></span>
									<?php }else{ ?>
									<span type="button" class="glyphicon glyphicon-search cursorPointer text-info" data-toggle="modal" data-target="#myModal22" onclick="showProductlistPop('<?php echo $MPkey;?>','<?php echo $Field->fieldname;?>','<?php echo $Field->relatedmodulename;?>','<?php echo $Field->fieldid;?>')"></span>
									<?php } ?>
								</span>-->
								<!--<?php echo $form->error(${$obj_name},"[$MPkey]$Field->fieldname", array('class'=>'ajxwarning errorMessage')); ?>-->
								<div id="<?php echo $Field->fieldname.'id'.$MPkey."_em_";?>" class="ajxwarning errorMessage tooltipImages bb5" style="display:none">Product Name cannot be blank.</div>
							</div>
						</td>

					<?php } elseif($Field['uitype']==18){?>
						<td class="<?php echo $Field->td_classname;?>">	<!--uitype is 12-->
							<span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
							<div class="input-group">
								<!--*******Cross Button ********** -->
								<span class="transponame input-group-addon">
									<span class="glyphicon glyphicon-remove-circle cursorPointer text-info" type="button" onclick="<?php echo $obj_name;?>RemoveValue('<?php echo $Field->fieldname;?>','<?php echo $MPkey;?>');"></span>
								</span>
								<!--******* Text and Hidden Field********** -->
								<input type="text" id="<?php echo $Field->fieldname.$MPkey;?>" name="<?php echo $Field->fieldname.$MPkey;?>" value="<?php echo $Multiple_Record->{$Field->fieldname};?>" class="<?php echo $Field->classname;?>">
								<?php echo $form->hiddenField(${$obj_name},"[$MPkey]$Field->fieldname", array ('id'=>$Field->fieldname.'id'.$MPkey,'value'=>$Multiple_Record->{$Field->fieldname})); ?>
								<!--*******Search Button ********** -->

								<span class="transearch input-group-addon">
									<span type="button" class="glyphicon glyphicon-search cursorPointer text-info" data-toggle="modal" data-target="#myModal22" onclick="showProductlistPop('<?php echo $MPkey;?>','<?php echo $Field->fieldname;?>','Stock','<?php echo $Field->fieldid;?>')"></span>
								</span>
								<?php echo $form->error(${$obj_name},"[$MPkey]$Field->fieldname", array('class'=>'ajxwarning errorMessage')); ?>
							</div>
						</td>
						<?php }?>


				<?php }//Field foreach close ?>
				
					<?php if($ModuleName != 'p_reconciliation' AND $ModuleName != 'ce_p_reconciliation' and $ModuleName != 'cecontractor' and $ModuleName != 'production' and $ModuleName != 'obr_contractor' and $Block->blockid !='13' and $Block->blockid !='24' and $Block->blockid !='57' and $Block->blockid !='58'){?>
					<td class="input-border">
						<div class="d-flex justify-content-center align-items-center">
							<div class="action-icon-container d-flex justify-content-center align-items-center <?php echo $obj_name;?>Delete" id="<?php echo $Block->Fields[0]->tablename.'_'.$MPkey.'_Delete'?>">
								<svg viewBox="0 0 18 19" class="action-icon action-icon--delete">
									<path d="M5.14414 15.2656H12.8539L13.2793 6.26562H4.71875L5.14414 15.2656Z"/>
									<path d="M15.1875 5H12.9375V3.59375C12.9375 2.97324 12.433 2.46875 11.8125 2.46875H6.1875C5.56699 2.46875 5.0625 2.97324 5.0625 3.59375V5H2.8125C2.50137 5 2.25 5.25137 2.25 5.5625V6.125C2.25 6.20234 2.31328 6.26562 2.39062 6.26562H3.45234L3.88652 15.459C3.91465 16.0584 4.41035 16.5312 5.00977 16.5312H12.9902C13.5914 16.5312 14.0854 16.0602 14.1135 15.459L14.5477 6.26562H15.6094C15.6867 6.26562 15.75 6.20234 15.75 6.125V5.5625C15.75 5.25137 15.4986 5 15.1875 5ZM6.32812 3.73438H11.6719V5H6.32812V3.73438ZM12.8549 15.2656H5.14512L4.71973 6.26562H13.2803L12.8549 15.2656Z"/>
								</svg>
							</div>
						</div>
					</td>
					<?php }?>
			
			</tr>
				
			<?php }?>

			
			<?php }?>
		</tbody>
		</table>
		<?php //if($ActionName=="Create"){ ?>
			
		<div class="d-flex justify-content-end">							
			<div class="btn-primary add-btn d-flex jc-cntr align-items-center position-static p-3 rounded margin-add" id="Add<?php echo $obj_name;?>" rel="<?php echo $Block->blockid;?>">
				<svg width="16" height="16" viewBox="0 0 16 16">
					<path d="M6 16H10V10H16V6H10V0H6V6H0V10H6V16Z" fill="#ffffff"/>
				</svg>
				<span class="ms-2">ADD</span>
			</div>
		</div>
		<?php //}?>
	</div>
 	<?php } else {?>
				<div class="body-outline h-auto">
					<div class="input-heading mx-2 mt-2"><?php echo $Block->blocklable;?></div>
			
					<table id="Tbl_<?php echo $obj_name;?>">
						<tr>
							<?php foreach ($Block->Fields as $key=> $Field): ?>
							<th><?php echo $Field->fieldlable;?></th>
							<?php endforeach;?>
													<?php //if($ActionName=="Create"){ ?>
							<th class="deleteTool">Tools</th>
													<?php //}?>
							
							
						</tr>
						<?php 
						if($ModuleName=='ehs' && $ActionName=="Create"){
							$this->productBlockEHS('1','93');
							$this->productBlockEHS('2','93');
							$this->productBlockEHS('3','93');
							$this->productBlockEHS('4','93');
						}
						if($ModuleName=='water_management' && $ActionName=="Create"){
							$this->productBlockEHS('1',$Block->blockid);
						}
						if($ModuleName=='screening_crushing' && $ActionName=="Create"){
							$this->productBlockEHS('1',$Block->blockid);
						}
						?>
						<?php
							$cnt_multiple_product=count($Record[Multiple_Records][$Block->blockid]);
							//$cnt_multiple_product=1;
							$tablename2="depot";
							$modelss=new Reference($tablename2);
							if($Field->edit_view==1)
							{
								$aa="$MPkey";
								if($_SESSION['countpro'] == '')
							{	
								$aa++;			
								//$_SESSION['countpro'] =$aa;
								$_SESSION['countpro'] =$cnt_multiple_product;
							}
						}
						echo "<script type='text/javascript'>cnt_multiple_product='$cnt_multiple_product';cnt_multiple_product=parseInt(cnt_multiple_product)+1;</script>";

						//$cnt_multiple_product=1;
								if($cnt_multiple_product>0){
									$reason_counter = 0;
						foreach($Record[Multiple_Records][$Block->blockid] as $MPkey=> $Multiple_Record){ $tax_html="";?>
								<?php //echo "<br>cnt_multiple_product >0  case";?>
								<tr class="prodlist">
								<?php
								//print_r($Multiple_Record);
								//die;
								foreach($Block->Fields as $key=> $Field){
								//print_r($Field);
								//die;
								?>
								
								<?php	if($Field['uitype']==1){
									if($ModuleName == 'p_reconciliation' || $ModuleName == 'ce_p_reconciliation'){?>
									<td class="<?php echo $Field->td_classname;?>">
										<?php $MPkeys =$MPkey +1;
										$fldname="$Field->fieldname"; 						
										?>	
										<?php echo $form->error(${$obj_name},"[$MPkeys]$Field->fieldname", array('class'=>'ajxwarning errorMessage tooltip_img')); ?>				
										<?php echo $form->textField(${$obj_name},"[$MPkeys]$Field->fieldname", array ('class' => $Field->classname,'value'=>$Multiple_Record->{$Field->fieldname},'readonly' => $readonly,'onkeyup' => $blastintfunc));?>
									</td><?php } else { ?>
									<td class="<?php echo $Field->td_classname;?>">
										<?php $counterp="$MPkey";
										$fldname="$Field->fieldname"; 						
										?>	
										<?php echo $form->error(${$obj_name},"[$MPkey]$Field->fieldname", array('class'=>'ajxwarning errorMessage tooltip_img')); ?>				
										<?php echo $form->textField(${$obj_name},"[$MPkey]$Field->fieldname", array ('class' => $Field->classname . ' input-border','value'=>$Multiple_Record->{$Field->fieldname},'readonly' => $readonly,'onkeyup' => $blastintfunc));?>
										<div class="error-container hide">All errors displayed Here</div>
									</td>

								<?php } ?>
								<?php }elseif($Field['uitype']==14){ ?>
									<td>
									<button type="button" value="Stoppage reason"  class="btn btn-primary form-control target-button" data-bs-toggle="modal" data-bs-target="#<?php echo $obj_name.'_'.$reason_counter.'_'.$Field->fieldname.'_reason'; ?>">Stoppage reason</button>

									<div class="modal fade" id="<?php echo $obj_name.'_'.$reason_counter.'_'.$Field->fieldname.'_reason';?>" tabindex="-1" aria-labelledby="exampleModal" aria-hidden="true">
										<div class="modal-dialog modal-dialog-centered modal-xl">
											<div class="modal-content">
													<div class="modal-body position-relative" data-que="<?php echo $reason_counter;?>">
														<div class="d-flex justify-content-between">
															<div class="input-heading" style="width:50%">Stopage Reason</div>
															<div class="input-heading" style="width:50%">Hours</div>
														</div>
														<?php 
														if(count($Multiple_Record->reasons) >0){
															foreach($Multiple_Record->reasons as $reason_record){ ?>
																<div class="d-flex justify-content-between anchor-class">
																	<select class="input-border form-control" name="<?php echo $Field->tablename;?>[<?php echo $reason_counter;?>][reason][]">
																		<?php $PickList=new PickList;//$Field->tablename
																			//$parent_record = Yii::app()->request->getParam('Record');
																			if($Block->blockid == "65"){
																				$reason_attr_name = "coal_extraction_id";
																			}else if($Block->blockid == "101"){
																				$reason_attr_name = "sc_reason_id";
																			}
																			$fieldoptionsReason=$PickList->getPickListReasonOptionEdit($reason_record->reason_id);
																			echo($fieldoptionsReason);
																		?>
																	</select>
																	<input placeholder="hh:mm:ss" maxlength="8" pattern="^((\d+:)?\d+:)?\d*$" value="<?php echo $reason_record->loss_hour; ?>" class="without_ampm input-border form-control" name="<?php echo $Field->tablename;?>[<?php echo $reason_counter;?>][loss_hour][]">
																</div>
															<?php } 
														}else{?>
															<div class="position-relative">
																<div class="d-flex justify-content-between anchor-class">
																	<select class="input-border form-control" name="<?php echo $Field->tablename;?>[<?php echo $reason_counter;?>][reason][]">
																		<?php $PickList=new PickList;
																			$fieldoptionsReason=$PickList->getPickListReasonOption();
																			echo $fieldoptionsReason;
																		?>
																	</select>
																	<input placeholder="hh:mm:ss" maxlength="8" pattern="^((\d+:)?\d+:)?\d*$" class="without_ampm input-border form-control" name="<?php echo $Field->tablename;?>[<?php echo $reason_counter;?>][loss_hour][]">
																</div>
															</div>
														<?php }
														?>
													</div>
													<div class="modal-footer">
														<!-- <input class="btn btn-primary btn-custom" type="submit" name="yt0" value="Save">
														<input name="btncancel" class="btn btn-outline-danger btn-custom me-5" type="submit" value="Discard"> -->
														<button type="button" class="btn btn-primary btn-custom add-rows-to-pop" >
															<svg width="16" height="16" viewBox="0 0 16 16">
																<path d="M6 16H10V10H16V6H10V0H6V6H0V10H6V16Z" fill="#ffffff"/>
															</svg> 
														</button>
														<button type="button" class="btn btn-outline-danger btn-custom me-5" data-bs-dismiss="modal">Close</button>
													</div>
												<!-- </div>	 -->
											</div>
										</div>
									</div>  

								
						
								</td>
								<?php $reason_counter++; } elseif($Field['uitype']==13){ $MPkeys =$MPkey +1; ?>
								<td>
									<div id="<?php echo $obj_name.'_'.$MPkeys.'_'.$Field->fieldname.'_em_'; ?>" class="ajxwarning errorMessage <?php echo $tooltip_class;?> bb1" style="display:none;"></div>
									<input type="hidden" name="<?php echo $obj_name.'['.$MPkeys.']'.'['.$Field->fieldname.']'; ?>" id="<?php echo $obj_name.'_'.$MPkeys.'_'.$Field->fieldname.'_dt'; ?>" value="<?php echo $Multiple_Record->{$Field->fieldname};?>" />
									<input class="form-control dtpick <?php echo $Field->classname;?>" type="text" id="<?php echo $obj_name.'_'.$MPkeys.'_'.$Field->fieldname; ?>" value="<?php echo date('d-m-Y',strtotime($Multiple_Record->{$Field->fieldname}));?>" readonly autocomplete="off" />
								</td>

								<?php } elseif($Field['uitype']==27){ $disp_dt=(($Multiple_Record->{$Field->fieldname} !='' && $Multiple_Record->{$Field->fieldname} !='0000-00-00 00:00:00') ? date('d-m-Y H:i',strtotime($Multiple_Record->{$Field->fieldname})) : '');?>
									<td class="<?php echo $Field->td_classname;?>">

									<?php echo $form->error(${$obj_name},"[$MPkey]$Field->fieldname", array('class'=>'ajxwarning errorMessage ')); ?>
									<input  type="hidden" name="<?php echo $obj_name.'['.$MPkey.']'.'['.$Field->fieldname.'create]'; ?>" id="<?php echo $obj_name.'_'.$MPkey.'_'.$Field->fieldname.'_jqdtEditoption'; ?>" value="2" />

									<input  type="hidden" name="<?php echo $obj_name.'['.$MPkey.']'.'['.$Field->fieldname.']'; ?>" id="<?php echo $obj_name.'_'.$MPkey.'_'.$Field->fieldname.'_jqdt'; ?>" value="<?php echo $Multiple_Record->{$Field->fieldname};?>" />
							
									<input class="form-control inputwidth jqdt <?php echo $Field->classname;?>" type="text" id="<?php echo $obj_name.'_'.$MPkey.'_'.$Field['fieldname'];?>" value="<?php echo $disp_dt;?>" autocomplete="off" />

									<!--<img style="height:20px; position:relative; right:25px; top:3px; width: 20px;" src="<?php echo Yii::app()->request->baseUrl; ?>/images/calendar/cal_icon.png" alt="calendar">-->


								<?php } elseif($Field['uitype']==15){?>
									<td class="<?php echo $Field->td_classname;?>">
										<?php echo $form->textField(${$obj_name},"[$MPkey]$Field->fieldname", array ('class' => $Field->classname,'value'=>$Multiple_Record->{$Field->fieldname}));?>
									</td>

								<?php }elseif($Field['uitype']==8){
									if($ModuleName == 'p_reconciliation' || $ModuleName == 'ce_p_reconciliation'){
										$MPkeys =$MPkey +1; ?>
										<td class="<?php echo $Field->td_classname;?>">
											<?php echo $form->error(${$obj_name},"[$MPkey]$Field->fieldname", array('class'=>'ajxwarning errorMessage tooltip_img')); ?>
											<?php echo $form->dropDownList(${$obj_name},"[$MPkeys]$Field->fieldname", $Field['fieldoptions'],array('class' => $Field['classname'],'value'=>$Multiple_Record->{$Field->fieldname},'empty'=>'Select an Option','options' => array($Multiple_Record->{$Field->fieldname}=>array('selected'=>true)))); ?>
										</td>

									<?php } else { ?>
									<td class="<?php echo $Field->td_classname;?>">
										<div id="<?php   if($ModuleName == 'logisticsiding' and $Field->tablename=='siding_receipt'){echo $Field->tablename.'_'.$MPkey.'_'.$Field->columnname.'_em_';} else{  echo $Field->columnname.'id'.$MPkey.'_em_';  } ?>" class="ajxwarning errorMessage tooltipImages bb2" style="display:none">Stock Type cannot be blank.</div>
										<?php	//echo $form->textField(${$obj_name},"[$MPkey]$Field->fieldname", array ('class' => $Field->classname,'value'=>$Multiple_Record->{$Field->fieldname})); ?>
										<div class="d-flex flex-col">
											<?php echo $form->dropDownList(${$obj_name},"[$MPkey]$Field->fieldname", $Field['fieldoptions'],array('class' => 'h5 input-border p-2 mb-0','aria-label'=>'select example','value'=>$Multiple_Record->{$Field->fieldname},'empty'=>'Select an Option','options' => array($Multiple_Record->{$Field->fieldname}=>array('selected'=>true)))); ?>
											<div class="error-container hide">All errors displayed Here</div>
										</div>
									</td>

								<?php }} elseif($Field['uitype']==22){?>
									<td class="<?php echo $Field->td_classname;?> multi_chosen">
										<?php if($RecordID !=''){
											$vals	= explode(",",$Multiple_Record->{$Field->fieldname});
											$selected =array();
											foreach($vals as $val){
											$selected[$val] = array('selected' => 'selected');
											}
										}
										echo $form->listBox(${$obj_name},"[$MPkey]$Field->fieldname",$Field['fieldoptions'],array('empty' => 'Select an Option','class'=>'form-control multi-select resean-select inputwidth','id'=>$attr_id,'multiple' => 'true','value'=>$Multiple_Record->{$Field->fieldname},'options' =>$selected));?>
									</td>

								<?php } elseif($Field['uitype']==12){?>
									<td class="<?php echo $Field->td_classname;?>">	<!--uitype is 12-->
										<span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
										<div class="input-group">
											<span class="transponame input-group-addon">
												<span class="glyphicon glyphicon-remove-circle cursorPointer text-info" type="button" onclick="<?php echo $obj_name;?>RemoveValue('<?php echo $Field->fieldname;?>','<?php echo $MPkey;?>');"></span>
											</span>
											<!--<span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>-->
											<?php	//echo $form->textField(${$obj_name},"[$MPkey]$Field->fieldname", array ('id'=>'productid'.$MPkey,'class' => $Field->classname,'value'=>$Field->reffieldvalue)); ?>
											<input type="text" id="<?php echo $Field->fieldname.$MPkey;?>" name="<?php echo $Field->fieldname.$MPkey;?>" value="<?php echo $Multiple_Record->{$Field->getRelatedDisplayFieldName};?>" class="<?php echo $Field->classname;?>" readonly>
											<!--<input type="hidden" value="<?php echo $Multiple_Record->{$Field->fieldname};?>" id="<?php echo "productidid".$MPkey;?>" name ="<?php echo $attr_name;?>" >-->
											<?php echo $form->hiddenField(${$obj_name},"[$MPkey]$Field->fieldname", array ('id'=>$Field->fieldname.'id'.$MPkey,'value'=>$Multiple_Record->{$Field->fieldname})); ?>
											<!--<?php echo $form->error($model,$field['fieldname'], array('class'=>'ajxwarning errorMessage')); ?>-->

											<span class="transearch input-group-addon">
												<?php if($invmngrule=="2"){ ?>
												<span type="button" class="glyphicon glyphicon-search cursorPointer text-info" style="pointer-events: none" data-toggle="modal" data-target="#myModal22"></span>
												<?php }else{ ?>
												<span type="button" class="glyphicon glyphicon-search cursorPointer text-info" data-toggle="modal" data-target="#myModal22" onclick="showProductlistPop('<?php echo $MPkey;?>','<?php echo $Field->fieldname;?>','<?php echo $Field->relatedmodulename;?>','<?php echo $Field->fieldid;?>')"></span>
												<?php } ?>
											</span>
											<!--<?php echo $form->error(${$obj_name},"[$MPkey]$Field->fieldname", array('class'=>'ajxwarning errorMessage')); ?>-->
											<div id="<?php echo $Field->fieldname.'id'.$MPkey."_em_";?>" class="ajxwarning errorMessage tooltipImages bb5" style="display:none">Product Name cannot be blank.</div>
										</div>
									</td>

								<?php } elseif($Field['uitype']==18){?>
									<td class="<?php echo $Field->td_classname;?>">	<!--uitype is 12-->
										<span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
										<div class="input-group">
											<!--*******Cross Button ********** -->
											<span class="transponame input-group-addon">
												<span class="glyphicon glyphicon-remove-circle cursorPointer text-info" type="button" onclick="<?php echo $obj_name;?>RemoveValue('<?php echo $Field->fieldname;?>','<?php echo $MPkey;?>');"></span>
											</span>
											<!--******* Text and Hidden Field********** -->
											<input type="text" id="<?php echo $Field->fieldname.$MPkey;?>" name="<?php echo $Field->fieldname.$MPkey;?>" value="<?php echo $Multiple_Record->{$Field->fieldname};?>" class="<?php echo $Field->classname;?>">
											<?php echo $form->hiddenField(${$obj_name},"[$MPkey]$Field->fieldname", array ('id'=>$Field->fieldname.'id'.$MPkey,'value'=>$Multiple_Record->{$Field->fieldname})); ?>
											<!--*******Search Button ********** -->

											<span class="transearch input-group-addon">
												<span type="button" class="glyphicon glyphicon-search cursorPointer text-info" data-toggle="modal" data-target="#myModal22" onclick="showProductlistPop('<?php echo $MPkey;?>','<?php echo $Field->fieldname;?>','Stock','<?php echo $Field->fieldid;?>')"></span>
											</span>
											<?php echo $form->error(${$obj_name},"[$MPkey]$Field->fieldname", array('class'=>'ajxwarning errorMessage')); ?>
										</div>
									</td>
									<?php }?>


							<?php }//Field foreach close ?>
							
								<?php if($ModuleName != 'p_reconciliation' AND $ModuleName != 'ce_p_reconciliation' and $ModuleName != 'cecontractor' and $ModuleName != 'production' and $ModuleName != 'obr_contractor' and $Block->blockid !='13' and $Block->blockid !='24' and $Block->blockid !='57' and $Block->blockid !='58'){?>
								<td class="input-border">
									<div class="d-flex justify-content-center align-items-center">
										<div class="action-icon-container d-flex justify-content-center align-items-center <?php echo $obj_name;?>Delete" id="<?php echo $Block->Fields[0]->tablename.'_'.$MPkey.'_Delete'?>">
											<svg viewBox="0 0 18 19" class="action-icon action-icon--delete">
												<path d="M5.14414 15.2656H12.8539L13.2793 6.26562H4.71875L5.14414 15.2656Z"/>
												<path d="M15.1875 5H12.9375V3.59375C12.9375 2.97324 12.433 2.46875 11.8125 2.46875H6.1875C5.56699 2.46875 5.0625 2.97324 5.0625 3.59375V5H2.8125C2.50137 5 2.25 5.25137 2.25 5.5625V6.125C2.25 6.20234 2.31328 6.26562 2.39062 6.26562H3.45234L3.88652 15.459C3.91465 16.0584 4.41035 16.5312 5.00977 16.5312H12.9902C13.5914 16.5312 14.0854 16.0602 14.1135 15.459L14.5477 6.26562H15.6094C15.6867 6.26562 15.75 6.20234 15.75 6.125V5.5625C15.75 5.25137 15.4986 5 15.1875 5ZM6.32812 3.73438H11.6719V5H6.32812V3.73438ZM12.8549 15.2656H5.14512L4.71973 6.26562H13.2803L12.8549 15.2656Z"/>
											</svg>
										</div>
									</div>
								</td>
								<?php }?>
						
						</tr>
							
						<?php }?>

						
						<?php }?>
					</table>
					<?php //if($ActionName=="Create"){ ?>
						
					<div class="d-flex justify-content-end">
						<div class="btn-primary add-btn d-flex jc-cntr align-items-center position-static p-3 rounded margin-add" id="Add<?php echo $obj_name;?>" rel="<?php echo $Block->blockid;?>">
							<svg width="16" height="16" viewBox="0 0 16 16">
								<path d="M6 16H10V10H16V6H10V0H6V6H0V10H6V16Z" fill="#ffffff"/>
							</svg>                        
							<span class="ms-2">ADD</span>
						</div>
					</div>
					<?php //}?>
				</div>
			<?php } ?>
	<!-- Multiple Block Code End -->
            <?php //endif;?>
