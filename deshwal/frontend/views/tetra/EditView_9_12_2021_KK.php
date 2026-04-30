
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/chosen.jquery.min.js"></script>

<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.inputmask.bundle.js"></script>

<?php
$siteDir = Yii::app()->params['dirName'];
$ModuleName=$ActionList['ModuleName'];
$ModuleType=$ActionList['ModuleType'];
$ActionName=$ActionList['ActionName'];
$isDepotUser=$ActionList['isDepotUser'];
$ModuleLabel=$ActionList['ModuleLabel'];
$depotcode=$_SESSION[$siteDir.'_depot_code'];
$_SESSION['countpro']="";
$_SESSION['taxcounterr']="";
$sesionid=$_SESSION[$siteDir.'_id'];
$invmngrule = $ActionList['invmngrule_details']['invmngrule'];
if($_SESSION['blastnoc'] == '')
	{$bno=($cnt_multiple_product+1) ;
		$_SESSION['blastnoc'] =$bno;
	}
//echo "<pre>";print_r($invmngrule);exit;

//echo "<br>ModuleName=$ModuleName and ActionName=$ActionName";
$ActionUrl=Yii::app()->createAbsoluteUrl($ModuleName)."/";
//echo "<br>ActionUrl=$ActionUrl";
$this->pageTitle=Yii::app()->name . " - $ModuleName";
//$this->breadcrumbs=array('Customer',);
$fullurl = Yii::app()->request->getUrl();
//echo $fullurl ; exit ;
?>
<script type="text/javascript">var fullurl = "<?= $fullurl ?>";</script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/cookie.js"></script>
<!-- <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/jquery.datetimepicker.css"/> -->
<div id="myModal22" class="modal fade" role="dialog"></div>
<div id="BatchList" class="modal fade" role="dialog"></div>
 <div id="AddDrill" class="">
	
		
			<?php $form=$this->beginWidget('CActiveForm', array( 
			'enableClientValidation'=>true,
			'clientOptions'=>array( 'validateOnSubmit'=>true, ), )); 
			
			
				
				$currentdate=date('Y-m-d'); 
				$finaldate =strtolower(date("Y-m-d", strtotime("-1 day" , strtotime( $currentdate ))));

				$currentdate1=date('d-m-Y'); 
				$finaldateshow =strtolower(date("d-m-Y", strtotime("-1 day" , strtotime( $currentdate1 ))));
			?>

			<!-- <span class="note">Fields with <span class="required star">&nbsp;*&nbsp;</span> are required.</span> -->
			<input type="hidden" value="<?php echo $ActionName; ?>" id="mode" name="mode"/>
			<input type="hidden" value="<?php echo $RecordID; ?>" id="recordid" name="recordid"/>
			<input type="hidden" value="<?php echo $ModuleName; ?>" id="module" name="module"/>
			<input type="hidden" id="inv_mng_rule" value="<?php echo $invmngrule; ?>">
			<input type="hidden" value="<?php echo $sesionid; ?>" id="sesionid" name="sesionid"/>
			<input type="hidden" value="<?php echo $finaldate; ?>" id="finaldate" name="finaldate"/>
			<input type="hidden" value="<?php echo $finaldateshow; ?>" id="finaldateshow" name="finaldateshow"/>
			
				
			
			<div class="topcontent-details"><!-- topcontent starts -->
			 	<div class="body-menu d-flex justify-content-between align-items-center">
					<div>
						<p class="body-heading"><?php echo $ActionName.' '.$ModuleLabel;?> Data</p>
					</div>
					<!-- Calendar & Shift : Start here -->
					<div class="d-flex justify-content-between align-items-center">
						<div class="d-flex justify-content-between align-items-center">
							<?php 
								$grand_total=false;
								foreach($ColumnList->Blocks as $BlockKey=>$Block):
							?>
	
							<?php if($Block->blocktype=="Simple"): ?>
							<?php 
							// echo "<pre>";
							// 	foreach($Block->Fields as $field){
								// 		print_r($field);
								// 	}
								// 	echo " at last";
							?>
							<?php $counter=1;foreach($Block->Fields as $field): ?>
								
							<?php if($field['uitype']==8):?>
								
								<?php echo $form->error($model,$field['fieldname'], array('class'=>'ajxwarning errorMessage')); ?>
								<?php echo $form->{$field['fieldtype']}($model,$field['fieldname'], $field['fieldoptions'],array('class' => 'form-select w-150px','value'=>$field['columnname'],'empty'=>'Select '.$field['fieldlable'],'options' => array($Record->{$field['columnname']}=>array('selected'=>true)))); ?>
	
							<?php elseif($field['uitype']==2): 
								echo $form->hiddenField($model,$field['fieldname'], array ('class' => $field['classname'],'value'=>$Record->{$field['columnname']}));
							elseif($field['uitype']==1):?>
								
								<?php echo $form->error($model,$field['fieldname'], array('class'=>'ajxwarning errorMessage')); ?>
								<?php if(($ModuleName=="obr_contractor" and $field['fieldname']=="total_nooftrips") or ($ModuleName=="cecontractor" and $field['fieldname']=="total_nooftrips")):
									echo $form->hiddenField($model,$field['fieldname'], array ('class' => $field['classname'],'value'=>$Record->{$field['columnname']}));?>
	
								<?php elseif($field->fieldname=="drilling_in_charge"):
									echo $form->error($model,$field['fieldname'], array('class'=>'ajxwarning errorMessage'));
									echo "<select id= 'EditModel_drilling_in_charge' name='EditModel[drilling_in_charge]' class='form-control inputwidth'> </select>";
								else:
									echo $form->{$field[fieldtype]}($model,$field['columnname'], array ('autocomplete' => 'new-password','class' => $field["classname"].' form-control inputwidth','value'=>$Record->{$field['columnname']}));?>
									<?php endif; ?>
									
									<?php elseif($field['uitype']==13):
							include 'uitype/Date.php'; 
							
							/************** Jitender Maithani code start ***************/
							elseif($field['uitype']==13):
								include 'uitype/Date.php';
							elseif($field['uitype']==27):
								include 'uitype/DateTime.php';
							elseif($field['uitype']==15):
								include 'uitype/MonthYear.php';
							elseif($field['uitype']==17):
								include 'uitype/TimeZones.php';
							elseif($field['uitype']==22):
								include 'uitype/MultiSelect.php';
							/************* Jitender Maithani end *********************/
							elseif($field['uitype']==19):
								include 'uitype/MaskingDate.php';
							?>
							<?php elseif($field['uitype']==6):?>
								
								<?php echo $form->error($model,$field['fieldname'], array('class'=>'ajxwarning errorMessage')); ?>
							<?php echo $form->{$field[fieldtype]}($model,$field['fieldname'],array('class' => $field['classname'],'checked'=>($Record->$field['columnname']=="1")?true:false)); ?>
							
							<?php elseif($field['uitype']==20):?>
								
								<?php 
								echo $form->error($model,$field['fieldname'], array('class'=>'ajxwarning errorMessage')); 							echo $form->hiddenField($model,$field['columnname'], array ('class' => $field['classname'],'value'=>$Record->{$field['columnname']}));
								echo "<input type='text' name={$field[columnname]} id={$field[columnname]} class='$field[classname]' value=''/> ";
								?>
									
									
							<?php endif;?>
							
							<?php if($counter%2==0):?>
						</div>
	
						<div>
							<?php endif;?>
							
							<?php $counter+=1; endforeach;?>
							
							<?php endif;?>
							<?php endforeach;  ?>
						</div>
					</div>
					<!-- Calendar & Shift : Ends here -->
						
					<div>
						<?php //echo CHtml::submitButton('Save', array('class' => 'btn btn-primary input-save','id'=>'showModel','data-bs-toggle'=>'modal','data-bs-target'=>'#exampleModal')); ?>	
						<?php if($ActionName=="Create"){ ?>
							<button type="button" id="showModel" class="btn btn-primary input-save" data-bs-toggle="modal" data-bs-target="#exampleModal">SAVE</button>
							<?php }  else {?>
						<?php echo CHtml::submitButton('Discard', array('name' => 'btncancel','class' => 'btn btn-outline-danger btn-custom me-5')); ?>
						<?php echo CHtml::submitButton('Save', array('class' => 'btn btn-primary btn-custom')); ?>
					<?php } ?>
            		</div>
        		</div>
			</div><!-- topcontent ends -->

		

			<div class="body-container">
				<!--<div data-simplebar>-->
				
					<?php 
						$grand_total=false;
						foreach($ColumnList->Blocks as $BlockKey=>$Block):
					?>
						
					<?php 
						//echo "<br>block type=".$Block->blocktype;
						if($Block->blocktype=="Multiple"):
							$obj_name="";
							$multiplebloackid=$Block->blockid;
							foreach ($Block->Fields as $key=> $Field):
								if($obj_name=="")
								{
									$obj_name=$Field->tablename;
									break;
								}
							endforeach;	
							$multipleobjname=$obj_name;
						endif;
					?>

					<?php if($Block->blocktype=="Multiple"):
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
				<div data-simplebar>
					<?php if($Block->blocklable){ ?>
						<!-- ToDo: this call will be added on single modules "height-full-body"  & on multiple "h-auto"-->
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
										<?php } elseif($Field['uitype']==13){ $MPkeys =$MPkey +1; ?>
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
								<div class="add-btn btn-primary d-flex jc-cntr align-items-center position-static" id="Add<?php echo $obj_name;?>" rel="<?php echo $Block->blockid;?>">
									<svg width="16" height="16" viewBox="0 0 16 16">
										<path d="M6 16H10V10H16V6H10V0H6V6H0V10H6V16Z" fill="#ffffff"/>
									</svg>                        
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
										<?php } elseif($Field['uitype']==13){ $MPkeys =$MPkey +1; ?>
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
								<div class="add-btn btn-primary d-flex jc-cntr align-items-center position-static" id="Add<?php echo $obj_name;?>" rel="<?php echo $Block->blockid;?>">
									<svg width="16" height="16" viewBox="0 0 16 16">
										<path d="M6 16H10V10H16V6H10V0H6V6H0V10H6V16Z" fill="#ffffff"/>
									</svg>                        
								</div>
							</div>
							<?php //}?>
						</div>
					<?php } ?>
				</div>
           
            <!-- Multiple Block Code End -->
            <?php //endif;?>
			<?php endif;?>

			<?php //endforeach;?>

			<?php endforeach;?>
		 </div>
		 <!-- All Modals -->
    <!-- 1. PopUpConfirmation Add Drill-->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <!-- Todo: Onclick of Submit Show this alert -->
        <div class="alert alert-success alert-dismissible fade hide" role="alert">
            Data Saved Successfully
        </div>
        <div class="modal-dialog modal-dialog-centered modal-xl">
          <div class="modal-content">
            <div class="modal-header jc-cntr">
              <h5 class="modal-title text-center body-heading" id="exampleModalLabel">Add <?php echo $ModuleLabel; ?> Final Submit</h5>
              <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
            </div>
            <div class="modal-body">
		<table class="Tbl_<?php echo $obj_name;?>">

		</table>
               
            </div>
            <div class="modal-footer jc-cntr">
		<?php echo CHtml::submitButton('Submit', array('class' => 'btn btn-primary input-save me-5 close')); ?>	
                <!--<button type="button" class="btn btn-primary input-save me-5 close" data-dismiss="alert">submit</button>-->
                <button type="button" class="btn btn-danger input-save" data-bs-dismiss="modal">Discard</button>
            </div>
          </div>
        </div>
    </div>  	
			<?php $this->endWidget(); ?>
		
	
</div> <!-- fullpage ends -->
<script>
	$(document).ready(function(){
		var modulename = '<?php echo $ModuleName; ?>';
		var mode = $('#mode').val();
		
		if(modulename =='users'){
			if(mode=='Create'){
				$('#yw0').submit(function(e){
				//alert(modulename);
					e.stopImmediatePropagation();
					var public_key=$('#publickey').val();
					var user_pass=$('#EditModel_user_password').val();
					
					var enc_user_pass=CryptoJS.AES.encrypt(JSON.stringify(user_pass), public_key, {format: CryptoJSAesJson}).toString();
					$('#EditModel_user_password').val(enc_user_pass);
				});
			}
		}
	});
</script> 
