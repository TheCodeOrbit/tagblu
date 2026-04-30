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
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/jquery.datetimepicker.css"/>
<div id="myModal22" class="modal fade" role="dialog"></div>
<div id="BatchList" class="modal fade" role="dialog"></div>

<div class="row" id="fullpage">
	<!-- left side --> 
	<?php include_once 'LeftSide.php';?>
	<div class="col-sm-10 rightside-page" id="rightside-main"> <!-- right side main starts -->
		<!-- collapse button to collapse left side -->
		<span class="glyphicon glyphicon-chevron-left toggleButton" id="collapsebtn"></span>

		<div class="customerForm"><!-- customer form starts -->
			<?php $form=$this->beginWidget('CActiveForm', array( 
			'enableClientValidation'=>true,
			'clientOptions'=>array( 'validateOnSubmit'=>true, ), )); 
			?>
			<div class="topcontent-details"><!-- topcontent starts -->
				<div class="row">
					<div class="col-sm-8">
						<h4 class="h4 page-heading no-gutter"><?php echo $ActionName.' '.$ModuleLabel;?></h4> 
					</div>
					
					<div class="col-sm-4">
						<div class="pull-right" role="group" aria-label="...">
							<?php echo CHtml::submitButton('Save', array('class' => 'btn topcontent-savebtn')); ?>
							<? echo '&nbsp;&nbsp;&nbsp;'; ?>
							<?php echo CHtml::submitButton('Discard', array('name' => 'btncancel','class' => 'btn topcontent-discardbtn')); ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<?php if($ActionList['ErrorMsg'] != ''): ?>
						<div class="alert alert-danger top-alert-error">
						 <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
						 <strong><?php echo $ActionList['ErrorMsg'];?></strong>
						</div>	
						<?php endif; ?>
					</div>
				</div>
			</div><!-- topcontent ends -->

			<?php 
				$currentdate=date('Y-m-d'); 
				$finaldate =strtolower(date("Y-m-d", strtotime("-1 day" , strtotime( $currentdate ))));

				$currentdate1=date('d-m-Y'); 
				$finaldateshow =strtolower(date("d-m-Y", strtotime("-1 day" , strtotime( $currentdate1 ))));
			?>

			<span class="note">Fields with <span class="required star">&nbsp;*&nbsp;</span> are required.</span>
			<input type="hidden" value="<?php echo $ActionName; ?>" id="mode" name="mode"/>
			<input type="hidden" value="<?php echo $RecordID; ?>" id="recordid" name="recordid"/>
			<input type="hidden" value="<?php echo $ModuleName; ?>" id="module" name="module"/>
			<input type="hidden" id="inv_mng_rule" value="<?php echo $invmngrule; ?>">
			<input type="hidden" value="<?php echo $sesionid; ?>" id="sesionid" name="sesionid"/>
			<input type="hidden" value="<?php echo $finaldate; ?>" id="finaldate" name="finaldate"/>
			<input type="hidden" value="<?php echo $finaldateshow; ?>" id="finaldateshow" name="finaldateshow"/>

			<?php 
				$grand_total=false;
				foreach($ColumnList->Blocks as $BlockKey=>$Block):?>		

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

			<?php 
				if($Block->blocktype=="Simple"):
			?>
			<div class="customerForm-header"><strong><?php echo $Block->blocklable;?></strong></div>
			<div id="block_<?php echo $Block->blockid;?>" class="simple_block">
				<table class="table table-bordered" > <!-- simple table starts -->
					<tbody>
						<tr>
							<?php
							$counter=1;foreach($Block->Fields as $field): ?>
							<?php if($field['uitype']==8):?>
							<td class="record-label"><?php echo $form->labelEx($model,$field['fieldname'], array ('class' => 'control-label labelwidth')); ?></td>
							<td class="record-value">
								<?php echo $form->error($model,$field['fieldname'], array('class'=>'ajxwarning errorMessage')); ?>
								<?php echo $form->{$field[fieldtype]}($model,$field['fieldname'], $field['fieldoptions'],array('class' => $field['classname'],'value'=>$field['columnname'],'empty'=>'Select an Option','options' => array($Record->{$field['columnname']}=>array('selected'=>true)))); ?>
							</td>

							<?php elseif($field['uitype']==6):?>
							<td class="record-label"><?php echo $form->labelEx($model,$field['fieldname'], array ('class' => 'control-label labelwidth')); ?></td>
							<td class="record-value">
								<?php echo $form->error($model,$field['fieldname'], array('class'=>'ajxwarning errorMessage')); ?>
								<?php echo $form->{$field[fieldtype]}($model,$field['fieldname'],array('class' => $field['classname'],'checked'=>($Record->$field['columnname']=="1")?true:false)); ?>
							</td>

							<!--**************khushboo Code Start*********************-->
							<?php elseif($field['uitype']==53):?>
							<td class="record-label"><?php echo $form->labelEx($model,$field['fieldlable'], array ('class' => 'control-label labelwidth')); ?></td>

							<?php if($RecordID !=''){ ?>
							<td class="record-value">
								<?php echo $form->error($model,$field['fieldname'], array('class'=>'ajxwarning errorMessage')); ?>
								<?php echo $form->{$field[fieldtype]}($model,$field['fieldname'], $field['fieldoptions'],array('class' => $field['classname'],'value'=>$field['columnname'],'options' => array($assignedtouser=>array('selected'=>true)))); ?>
							</td>

							<?php }else{ ?>
							<td class="record-value">
								<?php echo $form->error($model,$field['fieldname'], array('class'=>'ajxwarning errorMessage')); ?>
								<?php echo $form->{$field[fieldtype]}($model,$field['fieldname'], $field['fieldoptions'],array('class' => $field['classname'],'value'=>$field['columnname'],'options' => array($uid=>array('selected'=>true)))); ?>
							</td>
							<?php }
							/**************khushboo Code End**************************/

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

							elseif($field['uitype']==1):?>
							<?php if(($ModuleName=="obr_contractor" and $field['fieldname']=="total_nooftrips") or ($ModuleName=="cecontractor" and $field['fieldname']=="total_nooftrips")):
							else:?>
							<td class="record-label"><?php echo $form->labelEx($model,$field['fieldname'], array ('class' => 'control-label labelwidth')); ?></td>
							<?php endif; ?>
							<td class="record-value">
								<?php echo $form->error($model,$field['fieldname'], array('class'=>'ajxwarning errorMessage')); ?>
								<?php if(($ModuleName=="obr_contractor" and $field['fieldname']=="total_nooftrips") or ($ModuleName=="cecontractor" and $field['fieldname']=="total_nooftrips")):
								echo $form->hiddenField($model,$field['fieldname'], array ('class' => $field['classname'],'value'=>$Record->{$field['columnname']}));?>

								<?php elseif($field->fieldname=="drilling_in_charge"):
								echo $form->error($model,$field['fieldname'], array('class'=>'ajxwarning errorMessage'));
								echo "<select id= 'EditModel_drilling_in_charge' name='EditModel[drilling_in_charge]' class='form-control inputwidth'> </select>";
								else:
								echo $form->{$field[fieldtype]}($model,$field['columnname'], array ('autocomplete' => 'new-password','class' => $field["classname"].' form-control inputwidth','value'=>$Record->{$field['columnname']}));?>
								<?php endif; ?>
							</td>

							<?php elseif($field['uitype']==20):?>
							<td class="record-label"><?php echo $form->labelEx($model,$field['fieldname'], array ('class' => 'control-label labelwidth')); ?></td>
							<td class="record-value">
								<?php 
								echo $form->error($model,$field['fieldname'], array('class'=>'ajxwarning errorMessage')); 							echo $form->hiddenField($model,$field['columnname'], array ('class' => $field['classname'],'value'=>$Record->{$field['columnname']}));
								echo "<input type='text' name={$field[columnname]} id={$field[columnname]} class='$field[classname]' value=''/> ";?>
							</td>

							<?php elseif($field['uitype']==12):
								$relatedmod_tabid=$field['related_mod'];					
								$fieldname=$field['columnname'];					
								$fieldname1=$field['columnname']."1";	
								$fieldname2=$field['columnname']."2";	
								$fieldname3=$field['columnname']."3";
							if($ModuleType=="Related" and $ActionList['ActionName']=="Create")
							{
								$RefFields=$ActionList['RefFields'];
								if($field['columnname']=="depotname")
								{
								$ref_hid_value=$RefFields['depotid'];	
								$ref_disp_value=$RefFields['depotname'];
								}
								elseif(($field['columnname']=="customer_id" or $field['columnname']=="customerno"))
								{
								$ref_hid_value=$RefFields['customerid'];	
								$ref_disp_value=$RefFields['customername'];
								}		
							}
							else
							{
								$ref_hid_value=$Record->{$field['columnname']};	
								$ref_disp_value=$field['reffieldvalue'];		
							}	
							echo $form->{'hiddenField'}($model,$fieldname, array ('class' => $field['classname'],'value'=>$ref_hid_value));

							$relatedmod				=	$field['relatedmodulename'];
							$getRelatedDField	=	$field['getRelatedDisplayFieldName'];							

							echo $form->{'hiddenField'}($model,$fieldname2, array ('class' => $field['classname'],'value'=>$relatedmod));
							echo $form->{'hiddenField'}($model,$fieldname3, array ('class' => $field['classname'],'value'=>$getRelatedDField));					  
							?>
							<?php if($isDepotUser==1 and $field['columnname']=="depotname" and $ModuleType=="Master"):?>

							<?php else:?>
							<td class="record-label"><?php echo $form->labelEx($model,$field['fieldname'], array ('class' => 'control-label labelwidth')); ?></td>
							<td class="record-value">
								<?php echo $form->error($model,$field['fieldname'], array('class'=>'ajxwarning errorMessage')); ?>
								<div id="main_note" class="input-group inputwidth">
									<?php if($ModuleType!="Related"):?>			
									<span class="transponame input-group-addon">
										<span type="button" class="glyphicon glyphicon-remove-circle cursorPointer text-info" onclick="removeTextValue('<?php echo $fieldname1;?>','<?php echo $fieldname;?>');"></span>
									</span>
									<?php endif;?>
									<input class="form-control inputwidth" type="text" id="<?php echo $fieldname1;?>" name="<?php echo $fieldname1;?>" value="<?php echo $ref_disp_value;?>" readonly='readonly' <?php //if($ModuleType=="Related") echo "readonly='readonly'";?>>

									<?php if($ModuleType!="Related"):?>
									<span class="transearch input-group-addon">
										<span type="button" class="searchtrans glyphicon glyphicon-search cursorPointer text-info" data-toggle="modal" data-target="#myModal22" onclick="showCustomer1('<?php echo $fieldname2 ;?>','<?php echo $fieldname;?>','<?php echo $fieldname3;?>','customername','<?php echo $ModuleName; ?>','<?php echo $multiplebloackid ; ?>','<?php echo $multipleobjname;?>','<?php echo $Block->blockid; ?>')"></span>
									</span>
									<?php endif;?>
								</div>
							</td>
							<?php endif;?>
							<?php endif;?>
							<?php if($counter%2==0):?>
						</tr>

						<tr>
							<?php endif;?>
							<?php $counter+=1;
							endforeach;?>
						</tr>
					</tbody>
				</table><!-- simple table ends -->
				<input type="hidden" value="<?php echo $publickey;?>" id="publickey">
			</div>

			<?php elseif($Block->blocktype=="Multiple"):
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
			<div class="customerForm-header"><strong><?php echo $Block->blocklable;?></strong></div>
			<div id="contain_<?php echo $obj_name;?>" class="mul_block">
				<table class="table table-bordered table-responsive" id="Tbl_<?php echo $obj_name;?>"><!-- multiple table starts -->
					<thead>
						<tr>
							<?php if($ModuleName != 'p_reconciliation' AND $ModuleName != 'ce_p_reconciliation' and $ModuleName != 'cecontractor' and $ModuleName != 'production' and $ModuleName != 'obr_contractor' and $Block->blockid !='13' and $Block->blockid !='24' and $Block->blockid !='57' and $Block->blockid !='58'):?>
							<th class="text-center">Tools</th>
							<?php endif;?>
							<?php foreach ($Block->Fields as $key=> $Field):
							//print_r($Field);
							?>
							<?php //if($Field->edit_view==1):?>				
							<th class="text-center"><?php echo $Field->fieldlable;?></th>
							<?php //endif;?>
							<?php endforeach;?>
						</tr>
					</thead>

					<tbody id="prodlist_<?php echo $obj_name;?>">
						<?php
							$cnt_multiple_product=count($Record[Multiple_Records][$Block->blockid]);
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
						if($cnt_multiple_product>0):
						foreach($Record[Multiple_Records][$Block->blockid] as $MPkey=> $Multiple_Record): $tax_html="";?>
							<tr class="prodlist">
								<?php if($ModuleName != 'p_reconciliation' AND $ModuleName != 'ce_p_reconciliation' and $ModuleName != 'cecontractor' and $ModuleName != 'production' and $ModuleName != 'obr_contractor' and $Block->blockid !='13' and $Block->blockid !='24' and $Block->blockid !='57' and $Block->blockid !='58'):?>
								<td class="<?php echo $obj_name;?>Delete text-center" id="<?php echo $Block->Fields[0]->tablename.'_'.$MPkey.'_Delete'?>"><a href="javascript:void;"><span class="glyphicon glyphicon-trash"></span></a></td>
								<?php endif;?>

								<?php
								//print_r($Multiple_Record);
								//die;
								foreach($Block->Fields as $key=> $Field):
								//print_r($Field);
								//die;
								?>
								<?php //if($Field->edit_view==1):?>	
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
										<?php echo $form->textField(${$obj_name},"[$MPkey]$Field->fieldname", array ('class' => $Field->classname,'value'=>$Multiple_Record->{$Field->fieldname},'readonly' => $readonly,'onkeyup' => $blastintfunc));?>
									</td>

								<?php }} elseif($Field['uitype']==13){ $MPkeys =$MPkey +1; ?>
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
										<?php echo $form->dropDownList(${$obj_name},"[$MPkey]$Field->fieldname", $Field['fieldoptions'],array('class' => $Field['classname'],'value'=>$Multiple_Record->{$Field->fieldname},'empty'=>'Select an Option','options' => array($Multiple_Record->{$Field->fieldname}=>array('selected'=>true)))); ?>
									</td>

								<?php } }elseif($Field['uitype']==22){?>
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

								<?php }elseif($Field['uitype']==18){?>
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
									<?php //endif;?>
								<?php endforeach;?>
							</tr>
						<?php endforeach;?>
						<?php endif; ?>
					</tbody>
				</table><!-- multiple table ends -->
			</div>

			<table class="table table-bordered" id="add_<?php echo $Block->blockid;?>">
				<tr>
					<td colspan="11">
						<span class="btn" id="Add<?php echo $obj_name;?>" rel="<?php echo $Block->blockid;?>"><span class="glyphicon glyphicon-plus"></span> Add </span>
					</td>
					<?php endif;?>
				</tbody>
			</table>


	<?php if($Block->blocktype=="Tax"):
		$obj_name="";
		?>
			<!-- tax table starts -->
			<table class="table table-responsive table-bordered reciptTaxTable" id="block_<?php echo $Block->blockid;?>">
				<!--<div class="customerForm-header"><strong><?php echo $Block->blocklable;?></strong></div>-->
				<tbody>

				<?php foreach ($Block->Fields as $key=> $Field):
					//echo "<pre>";
					//print_r($Field);?>
					<?php //if($Field->edit_view==1):?>
					<tr>
					<?php if($Field->uitype==14):?>
						<?php if($Field['columnname'] == 'discount'):?>
								<td class="text-right dialog"><?php echo "hi".$form->hiddenField($model,$Field['columnname'], array ('class' => $Field['classname'],'value'=>$Record->{$Field['columnname']}));?><?php echo $Field->fieldlable;?></td>
						<td class="text-right"><span id="<?php echo $Field['fieldname'];?>"><?php echo $Record->{$Field['columnname']};?></span></td>
						<?php else:?>
						<td class="text-right"><?php echo $form->hiddenField($model,$Field['columnname'], array ('class' => $Field['classname'],'value'=>$Record->{$Field['columnname']}));?><?php echo $Field->fieldlable;?></td>
						<td class="text-right"><span id="<?php echo $Field['fieldname'];?>"><?php echo $Record->{$Field['columnname']};?></span></td>
						<?php endif;?>
					<?php elseif($Field->uitype==1):?>
						<?php if($Field['columnname'] == 'adjustment'):?>
						<td class="text-right"><?php echo $Field->fieldlable;?>
							<label><input id="adjust_add" type="radio" name="JplModule[adjust]" value="Add" class="active" <?php if(isset($Record->adjust)){ if($Record->adjust=='Add'){ echo "checked"; }}else{ echo "checked"; }?> > &nbsp;Add</label>
							<label><input id="adjust_deduct" type="radio" name="JplModule[adjust]" value="Deduct" <?php if($Record->adjust=='Deduct'){ echo "checked"; }?>> &nbsp;Deduct</label>

						</td>
						<td class="text-right"><?php echo $form->{$Field[fieldtype]}($model,$Field['columnname'], array ('class' => $Field['classname'],'value'=>$Record->{$Field['columnname']}));?></td>
						<?php else:?>
						<td class="text-right"><?php echo $Field->fieldlable;?></td>
						<td class="text-right"><?php echo $form->{$Field[fieldtype]}($model,$Field['columnname'], array ('class' => $Field['classname'],'value'=>$Record->{$Field['columnname']}));?>

						<?php if($Field['columnname'] == 'received'){?>
						<div id="main_note" class="input-group inputwidth">
						<span class="transearch input-group-addon">
						<span type="button" class="searchtrans glyphicon glyphicon-search cursorPointer text-info" data-toggle="modal" data-target="#myModal22" onclick="showCreditNote();"></span>
						</span>
						<span class="transponame input-group-addon"><span type="button" class="glyphicon glyphicon-remove-circle cursorPointer text-info" onclick="removeCreditNote();"></span></span>
						</div>
						<div id="myModal22" class="modal fade" role="dialog">d</div>

						<?php } else if($Field['columnname'] == 'cc_note_amt'){ ?>
						<div id="main_note" class="input-group inputwidth">
						<span class="transearch input-group-addon">
						<input type="hidden" name="hidden_cc_note_amt" id="hidden_cc_note_amt" value="<?php if ($RecordID !='') echo $cclistval['totalamount']; else echo '' ?>" />
						<input type="hidden" name="hidden_cc_note_id" id="hidden_cc_note_id" value="<?php if ($RecordID !='') echo $cclistval['noteids']; else echo ''  ?>" />
						<input type="hidden" name="hidden_cccount" id="hidden_cccount" value="<?php if ($RecordID !='') echo $cclistval['count']; else echo ''  ?>" />
						<input type="hidden" name="hidden_cccountid" id="hidden_cccountid" value="<?php if ($RecordID !='') echo $cclistval['countids']; else echo '' ?>" />
						<span id="ccspan" class="glyphicon glyphicon-plus cursorPointer text-info" onclick="credinotePopup(event)"></span>
						<div id="creditnote_popup" class="dialog" style="display:none; background-color:#fff;">
							<header id="popupheader">
								<span class="popuptitle"><strong>Credit Note Details</strong></span>
								<span id="closedialog" class="cursorPointer closepop popupclose_nocase pull-right" style="position:relative;top:-12px;"><b>&times;</b></span>
							</header>
							<body>
								<div class="row popup_content" id="parentcc" name="parentcc">
								<div class="col-sm-6"><b>Note No</b></div>
								<div class="col-sm-4"><b>Amount</b></div>
								<div class="col-sm-2" style="top:-13px;"><b>Action</b></div>
								</div>
								<?php if ($RecordID !='') {
								if($cclistval['count'] > 0){
								foreach($cclist as $key=>$val) { ?>
<div class="row popup_content" id="ccdetail" name="ccdetail">
<input type="hidden" id="ccid<?php echo $key; ?>" name="ccid<?php echo $key; ?>" value="<?php echo $val['noteid']; ?>" />
<input type="hidden" id="ccmodulename<?php echo $key; ?>" name="ccmodulename<?php echo $key; ?>" value="<?php echo $val['modulename']; ?>" />
<div class="row popup_content" id="ccdetail<?php echo $key;?>" style="margin:0px;padding:0px;">
<input type="hidden" id="notenocc<?php echo $key;?>" name="notenocc<?php echo $key;?>" value="<?php echo $val['noteno'];?>" />
<div class="col-sm-6" id="notenocc<?php echo $key;?>" name="notenocc<?php echo $key;?>"> <?php echo $val['noteno'];?></div>
<input type="hidden" id="amountcc<?php echo $key;?>" name="amountcc<?php echo $key;?>" value="<?php echo $val['amount'];?>" />
<div class="col-sm-4" id="amountcc<?php echo $key;?>" name="amountcc<?php echo $key;?>"> <?php echo $val['amount'];?></div>
<div class="col-sm-2"><span type="button" class="glyphicon glyphicon-remove-circle cursorPointer text-info" onclick="removeCreditNotes('<?php echo $key;?>','<?php echo $val['noteno']; ?>','<?php echo $val['amount'];?>')">
</span></div></div></div>
								<?php  } } else { ?>
								<div class="row popup_content" id="ccdetail" name="ccdetail"></div>
								<?php }
								} else { ?>
								<div class="row popup_content" id="ccdetail" name="ccdetail"></div>
								<?php } ?>

							</body>
						</div>
						</span>
						<span class="transponame input-group-addon">
							<span type="button" class="searchtrans glyphicon glyphicon-search cursorPointer text-info" data-toggle="modal" data-target="#myModal22" onclick="showCustomer1('ManualNote','cc_note_amt','<?php echo $fieldname3;?>','customername','<?php echo $ModuleName; ?>','<?php echo $multiplebloackid ; ?>','<?php echo $multipleobjname;?>','<?php echo $Block->blockid; ?>')"></span>
						</span>
						</div>

						<?php } else if($Field['columnname'] == 'db_note_amt'){ ?>
						<div id="main_note" class="input-group inputwidth">
						<span class="transearch input-group-addon">
						<input type="hidden" name="hidden_db_note_amt" id="hidden_db_note_amt" value="<?php if ($RecordID !='') echo $dblistval['totalamount']; else echo '' ?>" />
						<input type="hidden" name="hidden_db_note_id" id="hidden_db_note_id" value="<?php if ($RecordID !='') echo $dblistval['noteids']; else echo '' ?>" />
						<input type="hidden" name="hidden_dbcount" id="hidden_dbcount" value="<?php if ($RecordID !='') echo $dblistval['count']; else echo '' ?>" />
						<input type="hidden" name="hidden_dbcountid" id="hidden_dbcountid" value="<?php if ($RecordID !='') echo $dblistval['countids']; else echo '' ?>" />
						<span id="dbspan" class="glyphicon glyphicon-plus cursorPointer text-info" onclick="debitnotePopup(event)"></span>
						<div id="debitnote_popup" class="dialog" style="display:none; background-color:#fff;">
							<header id="popupheader">
								<span class="popuptitle"><strong>Debit Note Details</strong></span>
								<span id="closedialog" class="cursorPointer closepop popupclose_nocase pull-right" style="position:relative;top:-12px;"><b>&times;</b></span>
							</header>
							<body>
								<div class="row popup_content" id="parentdb" name="parentdb">
								<div class="col-sm-6"><b>Note No</b></div>
								<div class="col-sm-4"><b>Amount</b></div>
								<div class="col-sm-2" style="top:-13px;"><b>Action</b></div>
								</div>
								<?php if ($RecordID !='') {
								if($dblistval['count'] > 0) {
								foreach($dblist as $key=>$val) { ?>
<div class="row popup_content" id="dbdetail" name="dbdetail">
<input type="hidden" id="dbid<?php echo $key; ?>" name="dbid<?php echo $key; ?>" value="<?php echo $val['noteid']; ?>" />
<input type="hidden" id="dbmodulename<?php echo $key; ?>" name="dbmodulename<?php echo $key; ?>" value="<?php echo $val['modulename']; ?>" />
<div class="row popup_content" id="dbdetail<?php echo $key;?>" style="margin:0px;padding:0px;">
<input type="hidden" id="notenodb<?php echo $key;?>" name="notenodb<?php echo $key;?>" value="<?php echo $val['noteno'];?>" />
<div class="col-sm-6" id="notenodb<?php echo $key;?>" name="notenodb<?php echo $key;?>"> <?php echo $val['noteno'];?></div>
<input type="hidden" id="amountdb<?php echo $key;?>" name="amountdb<?php echo $key;?>" value="<?php echo $val['amount'];?>" />
<div class="col-sm-4" id="amountdb<?php echo $key;?>" name="amountdb<?php echo $key;?>"> <?php echo $val['amount'];?></div>
<div class="col-sm-2"><span type="button" class="glyphicon glyphicon-remove-circle cursorPointer text-info" onclick="removeDebitNote('<?php echo $key;?>','<?php echo $val['noteid']; ?>','<?php echo $val['amount'];?>')">
</span></div></div></div>
								<?php  } } else { ?>
								<div class="row popup_content" id="dbdetail" name="dbdetail"></div>
								<?php  }
								} else { ?>
								<div class="row popup_content" id="dbdetail" name="dbdetail"></div>
								<?php } ?>
							</body>
						</div>
						</span>
						<span class="transponame input-group-addon">
							<span type="button" class="searchtrans glyphicon glyphicon-search cursorPointer text-info" data-toggle="modal" data-target="#myModal22" onclick="showCustomer1('ManualNote','db_note_amt','<?php echo $fieldname3;?>','customername','<?php echo $ModuleName; ?>','<?php echo $multiplebloackid ; ?>','<?php echo $multipleobjname;?>','<?php echo $Block->blockid; ?>')"></span>

						</span>
						</div>
						<?php } ?>

						</td>
						<?php endif;?>

					<?php endif;?>
					</tr>
					<?php //endif;?>
				<?php endforeach;?>


				</tbody>
			</table><!-- tax table starts -->

		<?php endif;?>




			<?php //endforeach;?>

			<?php endforeach;?>

			<div class="form-btn pull-right"><!-- save and close buttons -->
				<?php echo CHtml::submitButton('Save', array('class' => 'btn bottomsavebtn')); ?>
				<? echo '&nbsp;&nbsp;&nbsp;'; ?>
				<?php echo CHtml::submitButton('Discard', array('name' => 'btncancel','class' => 'btn bottomdiscardbtn')); ?>
			</div>
			<?php $this->endWidget(); ?>
		</div><!-- customer form ends -->
	</div><!-- right side main ends -->
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