<?php
//echo $Block->blockid."hhh";
if($Block->blockid !=6){ ?>
					<div class="body-outline h-auto mb-3">
						<?php } ?>

						<?php if($Block->blockid !=77 and $Block->blockid !=81 and $Block->blockid !=82){ 
							

							if($Block->blockid ==6){}else{
							?>
						<div class="input-heading" style="margin: 0.5rem 0.7rem;"><?php echo $Block->blocklable;?></div>
							<?php } } ?>
						<div class="simple-table d-flex justify-content-between mb-2" style="<?php echo ($Block->blockid==6)?'margin:0 2rem':'';?>">
							<!--  2 different containers  -->
							
							<div class="simple-table__container" style="<?php if($Block->blockid !=6){ echo 'border:none' ;}?>">
								<?php $counter=0;foreach($Block->Fields as $field) {
									if($counter%4 == 0){ ?>
										<div class="d-flex justify-content-between mb-1 gap-1rem">
										<?php if($field['uitype']!=2){?>
											<div class="input-heading simple-table__container__heading">
												<?php echo $form->labelEx($model,$field['fieldname'], array ()); ?>
											</div>
										<?php } ?>
										<?php 
											if($field['uitype']==1){
												echo $form->{$field[fieldtype]}($model,$field['columnname'], array ('autocomplete' => 'new-password','class' =>'input-border simple-table__container__input','value'=>$Record->{$field['columnname']}?$Record->{$field['columnname']}:''));
												
?>
											<div class="error-container hide">Please Enter Some value</div>
	
											<?php 
											echo $form->error($model,$field['fieldname'],array('class'=>'ajxwarning errorMessage error-container')

											);
										}else if($field['uitype']==2){
												echo $form->{$field['fieldtype']}($model,$field['columnname'],array (
													'autocomplete' => 'new-password','class' => 'effect '.$field["classname"],'value'=>$Record->{$field['columnname']}));
											}else if($field['uitype']==8){
												echo $form->{$field['fieldtype']}($model,$field['fieldname'], $field['fieldoptions'],array('class' => 'form-select w-150px','value'=>$field['columnname'],'empty'=>'Select '.$field['fieldlable'],'options' => array($Record->{$field['columnname']}=>array('selected'=>true))));
												?>
											<div class="error-container hide">Please Enter Some value</div>
	
											<?php 
												echo $form->error($model,$field['fieldname'],array('class'=>'ajxwarning errorMessage error-container'));
							
											}else if($field['uitype']==13){
												include 'uitype/Date.php';
											}else if($field['uitype']==12){
												$relatedmod_tabid	= $field['related_mod'];
												$fieldname		= $field['columnname'];
												$fieldname1		= $field['columnname']."1";
												$fieldname2		= $field['columnname']."2";
												$fieldname3		= $field['columnname']."3";
												if($ModuleType=="Related" and $ActionList['ActionName']=="Create"){
													$RefFields=$ActionList['RefFields'];
													if(($field['columnname']=="customer_id" or $field['columnname']=="customerno")){
														$ref_hid_value=$RefFields['customerid'];
														$ref_disp_value=$RefFields['customername'];
													}
												} else {
													$ref_hid_value 	= $Record->{$field['columnname']};
													$ref_disp_value	= $field['reffieldvalue'];
												}
												echo $form->{'hiddenField'}($model,$fieldname, array ('class' => $field['classname'],'value'=>$ref_hid_value));

												$relatedmod	= $field['relatedmodulename'];
												$getRelatedDField= $field['getRelatedDisplayFieldName'];
												echo $form->{'hiddenField'}($model,$fieldname2, array ('class' => $field['classname'],'value'=>$relatedmod));
												echo $form->{'hiddenField'}($model,$fieldname3, array ('class' => $field['classname'],'value'=>$getRelatedDField)); ?>
													
											<?php if($ModuleType!="Related"){?>
												<!-- <a class="btn btn-default " onclick="removeTextValue('<?php echo $fieldname1;?>','<?php echo $fieldname;?>');"><i class="mdi mdi-window-close"></i></a> -->
											<?php }?>
											<input class="effect" type="text" id="<?php echo $fieldname1;?>" name="<?php echo $fieldname1;?>" value="<?php echo $ref_disp_value;?>" readonly='readonly'>
											<?php echo $form->labelEx($model,$field['fieldname'], array ('class' => 'control-label labelwidth')); ?>
																					
											<?php if($ModuleType!="Related"){?>
												<svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg" data-toggle="modal" data-target="#myModal22" aria-hidden="true" tabindex="-1" onclick="showCustomer1('<?php echo $fieldname2 ;?>','<?php echo $fieldname;?>','<?php echo $fieldname3;?>','customername','<?php echo $ModuleName; ?>','<?php echo $multiplebloackid ; ?>','<?php echo $multipleobjname;?>','<?php echo $Block->blockid; ?>')">
													<path d="M21 21.5L16.514 17.006L21 21.5ZM19 11C19 13.2543 18.1045 15.4163 16.5104 17.0104C14.9163 18.6045 12.7543 19.5 10.5 19.5C8.24566 19.5 6.08365 18.6045 4.48959 17.0104C2.89553 15.4163 2 13.2543 2 11C2 8.74566 2.89553 6.58365 4.48959 4.98959C6.08365 3.39553 8.24566 2.5 10.5 2.5C12.7543 2.5 14.9163 3.39553 16.5104 4.98959C18.1045 6.58365 19 8.74566 19 11V11Z" stroke="#2F80ED" stroke-width="2" stroke-linecap="round"/>
												</svg>
											<?php }
											}else{
												echo "work in progress for uitype -- ".$field['uitype'];
											} ?>
										</div>
									<?php } 
									$counter++;
								}?>	
							</div>
							<div class="simple-table__container" style="<?php if($Block->blockid !=6){ echo 'border:none' ;}?>">
								<?php $counter=0;foreach($Block->Fields as $field){ 
									if($counter%4 == 1){?>
										<div class="d-flex justify-content-between mb-1 gap-1rem">
											<?php if($field['uitype']!=2){?>
												<div class="input-heading simple-table__container__heading">
													<?php echo $form->labelEx($model,$field['fieldname'], array ()); ?>
												</div>
											<?php } ?>
											<?php 
											if($field['uitype']==1){
												echo $form->{$field[fieldtype]}($model,$field['columnname'], array ('autocomplete' => 'new-password','class' =>'input-border simple-table__container__input','value'=>$Record->{$field['columnname']}?$Record->{$field['columnname']}:''));
												?>
											<div class="error-container hide">Please Enter Some value</div>
	
											<?php 
												echo $form->error($model,$field['fieldname'],array('class'=>'ajxwarning errorMessage error-container'));
											}else if($field['uitype']==2){
												echo $form->{$field['fieldtype']}($model,$field['columnname'],array (
													'autocomplete' => 'new-password','class' => 'effect '.$field["classname"],'value'=>$Record->{$field['columnname']}));
											}else if($field['uitype']==8){?>
												<?php echo $form->{$field['fieldtype']}($model,$field['fieldname'], $field['fieldoptions'],array('class' => 'form-select w-150px','value'=>$field['columnname'],'empty'=>'Select '.$field['fieldlable'],'options' => array($Record->{$field['columnname']}=>array('selected'=>true)))); ?>

											<div class="error-container hide">Please Enter Some value</div>
	
											
												<?php echo $form->error($model,$field['fieldname'],array('class'=>'ajxwarning errorMessage error-container')); ?>
							
											<?php }else if($field['uitype']==13){
												include 'uitype/Date.php';
											}else if($field['uitype']==12){
												$relatedmod_tabid	= $field['related_mod'];
												$fieldname		= $field['columnname'];
												$fieldname1		= $field['columnname']."1";
												$fieldname2		= $field['columnname']."2";
												$fieldname3		= $field['columnname']."3";
													if($ModuleType=="Related" and $ActionList['ActionName']=="Create"){
														$RefFields=$ActionList['RefFields'];
														if(($field['columnname']=="customer_id" or $field['columnname']=="customerno"))
														{
														$ref_hid_value=$RefFields['customerid'];
														$ref_disp_value=$RefFields['customername'];
														}
													} else {
														$ref_hid_value 	= $Record->{$field['columnname']};
														$ref_disp_value	= $field['reffieldvalue'];
													}
													echo $form->{'hiddenField'}($model,$fieldname, array ('class' => $field['classname'],'value'=>$ref_hid_value));

													$relatedmod	= $field['relatedmodulename'];
													$getRelatedDField= $field['getRelatedDisplayFieldName'];
													echo $form->{'hiddenField'}($model,$fieldname2, array ('class' => $field['classname'],'value'=>$relatedmod));
													echo $form->{'hiddenField'}($model,$fieldname3, array ('class' => $field['classname'],'value'=>$getRelatedDField)); ?>
													
											<?php if($ModuleType!="Related"){?>
												<!-- <a class="btn btn-default " onclick="removeTextValue('<?php echo $fieldname1;?>','<?php echo $fieldname;?>');"><i class="mdi mdi-window-close"></i></a> -->
											<?php }?>
											<input class="effect" type="text" id="<?php echo $fieldname1;?>" name="<?php echo $fieldname1;?>" value="<?php echo $ref_disp_value;?>" readonly='readonly'>
											<?php echo $form->labelEx($model,$field['fieldname'], array ('class' => 'control-label labelwidth')); ?>
																					
											<?php if($ModuleType!="Related"){?>
												
												<svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg" data-toggle="modal" data-target="#myModal22" aria-hidden="true" tabindex="-1" onclick="showCustomer1('<?php echo $fieldname2 ;?>','<?php echo $fieldname;?>','<?php echo $fieldname3;?>','customername','<?php echo $ModuleName; ?>','<?php echo $multiplebloackid ; ?>','<?php echo $multipleobjname;?>','<?php echo $Block->blockid; ?>')">
													<path d="M21 21.5L16.514 17.006L21 21.5ZM19 11C19 13.2543 18.1045 15.4163 16.5104 17.0104C14.9163 18.6045 12.7543 19.5 10.5 19.5C8.24566 19.5 6.08365 18.6045 4.48959 17.0104C2.89553 15.4163 2 13.2543 2 11C2 8.74566 2.89553 6.58365 4.48959 4.98959C6.08365 3.39553 8.24566 2.5 10.5 2.5C12.7543 2.5 14.9163 3.39553 16.5104 4.98959C18.1045 6.58365 19 8.74566 19 11V11Z" stroke="#2F80ED" stroke-width="2" stroke-linecap="round"/>
												</svg>
												<?php }
											}else{
												echo " work in progess for ui type -- ".$field['uitype'];
											}  ?>
										</div>
								<?php } 
								$counter++;
								}?>	
							</div>
							<div class="simple-table__container" style="<?php if($Block->blockid !=6){ echo 'border:none' ;}?>">
								<?php $counter=0;foreach($Block->Fields as $field) {
									if($counter%4 == 2){ ?>
										<div class="d-flex justify-content-between mb-1 gap-1rem">
										<?php if($field['uitype']!=2){?>
											<div class="input-heading simple-table__container__heading">
												<?php echo $form->labelEx($model,$field['fieldname'], array ()); ?>
											</div>
										<?php } ?>
										<?php 
											if($field['uitype']==1){
												echo $form->{$field[fieldtype]}($model,$field['columnname'], array ('autocomplete' => 'new-password','class' =>'input-border simple-table__container__input','value'=>$Record->{$field['columnname']}?$Record->{$field['columnname']}:''));
												?>
											<div class="error-container hide">Please Enter Some value</div>
	
											<?php 
												echo $form->error($model,$field['fieldname'],array('class'=>'ajxwarning errorMessage error-container'));
											}else if($field['uitype']==2){
												echo $form->{$field['fieldtype']}($model,$field['columnname'],array (
													'autocomplete' => 'new-password','class' => 'effect '.$field["classname"],'value'=>$Record->{$field['columnname']}));
											}else if($field['uitype']==8){
												echo $form->{$field['fieldtype']}($model,$field['fieldname'], $field['fieldoptions'],array('class' => 'form-select w-150px','value'=>$field['columnname'],'empty'=>'Select '.$field['fieldlable'],'options' => array($Record->{$field['columnname']}=>array('selected'=>true))));
												?>
											<div class="error-container hide">Please Enter Some value</div>
	
											<?php 
												echo $form->error($model,$field['fieldname'],array('class'=>'ajxwarning errorMessage error-container'));
							
											}else if($field['uitype']==13){
												include 'uitype/Date.php';
											}else if($field['uitype']==12){
												$relatedmod_tabid	= $field['related_mod'];
												$fieldname		= $field['columnname'];
												$fieldname1		= $field['columnname']."1";
												$fieldname2		= $field['columnname']."2";
												$fieldname3		= $field['columnname']."3";
												if($ModuleType=="Related" and $ActionList['ActionName']=="Create"){
													$RefFields=$ActionList['RefFields'];
													if(($field['columnname']=="customer_id" or $field['columnname']=="customerno")){
														$ref_hid_value=$RefFields['customerid'];
														$ref_disp_value=$RefFields['customername'];
													}
												} else {
													$ref_hid_value 	= $Record->{$field['columnname']};
													$ref_disp_value	= $field['reffieldvalue'];
												}
												echo $form->{'hiddenField'}($model,$fieldname, array ('class' => $field['classname'],'value'=>$ref_hid_value));

												$relatedmod	= $field['relatedmodulename'];
												$getRelatedDField= $field['getRelatedDisplayFieldName'];
												echo $form->{'hiddenField'}($model,$fieldname2, array ('class' => $field['classname'],'value'=>$relatedmod));
												echo $form->{'hiddenField'}($model,$fieldname3, array ('class' => $field['classname'],'value'=>$getRelatedDField)); ?>
													
											<?php if($ModuleType!="Related"){?>
												<!-- <a class="btn btn-default " onclick="removeTextValue('<?php echo $fieldname1;?>','<?php echo $fieldname;?>');"><i class="mdi mdi-window-close"></i></a> -->
											<?php }?>
											<input class="effect" type="text" id="<?php echo $fieldname1;?>" name="<?php echo $fieldname1;?>" value="<?php echo $ref_disp_value;?>" readonly='readonly'>
											<?php echo $form->labelEx($model,$field['fieldname'], array ('class' => 'control-label labelwidth')); ?>
																					
											<?php if($ModuleType!="Related"){?>
												<svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg" data-toggle="modal" data-target="#myModal22" aria-hidden="true" tabindex="-1" onclick="showCustomer1('<?php echo $fieldname2 ;?>','<?php echo $fieldname;?>','<?php echo $fieldname3;?>','customername','<?php echo $ModuleName; ?>','<?php echo $multiplebloackid ; ?>','<?php echo $multipleobjname;?>','<?php echo $Block->blockid; ?>')">
													<path d="M21 21.5L16.514 17.006L21 21.5ZM19 11C19 13.2543 18.1045 15.4163 16.5104 17.0104C14.9163 18.6045 12.7543 19.5 10.5 19.5C8.24566 19.5 6.08365 18.6045 4.48959 17.0104C2.89553 15.4163 2 13.2543 2 11C2 8.74566 2.89553 6.58365 4.48959 4.98959C6.08365 3.39553 8.24566 2.5 10.5 2.5C12.7543 2.5 14.9163 3.39553 16.5104 4.98959C18.1045 6.58365 19 8.74566 19 11V11Z" stroke="#2F80ED" stroke-width="2" stroke-linecap="round"/>
												</svg>
											<?php }
											}else{
												echo "work in progress for uitype -- ".$field['uitype'];
											} ?>
										</div>
									<?php } 
									$counter++;
								}?>	
							</div>
							<div class="simple-table__container" style="<?php if($Block->blockid !=6){ echo 'border:none' ;}?>">
								<?php $counter=0;foreach($Block->Fields as $field) {
									if($counter%4 == 3){ ?>
										<div class="d-flex justify-content-between mb-1 gap-1rem">
										<?php if($field['uitype']!=2){?>
											<div class="input-heading simple-table__container__heading">
												<?php echo $form->labelEx($model,$field['fieldname'], array ()); ?>
											</div>
										<?php } ?>
										<?php 
											if($field['uitype']==1){
												echo $form->{$field[fieldtype]}($model,$field['columnname'], array ('autocomplete' => 'new-password','class' =>'input-border simple-table__container__input','value'=>$Record->{$field['columnname']}?$Record->{$field['columnname']}:''));
												?>
											<div class="error-container hide">Please Enter Some value</div>
	
											<?php 
												echo $form->error($model,$field['fieldname'],array('class'=>'ajxwarning errorMessage error-container'));
											}else if($field['uitype']==2){
												echo $form->{$field['fieldtype']}($model,$field['columnname'],array (
													'autocomplete' => 'new-password','class' => 'effect '.$field["classname"],'value'=>$Record->{$field['columnname']}));
											}else if($field['uitype']==8){
												echo $form->{$field['fieldtype']}($model,$field['fieldname'], $field['fieldoptions'],array('class' => 'form-select w-150px','value'=>$field['columnname'],'empty'=>'Select '.$field['fieldlable'],'options' => array($Record->{$field['columnname']}=>array('selected'=>true))));
												?>
											<div class="error-container hide">Please Enter Some value</div>
	
											<?php 
												echo $form->error($model,$field['fieldname'],array('class'=>'ajxwarning errorMessage error-container'));
							
											}else if($field['uitype']==13){
												include 'uitype/Date.php';
											}else if($field['uitype']==12){
												$relatedmod_tabid	= $field['related_mod'];
												$fieldname		= $field['columnname'];
												$fieldname1		= $field['columnname']."1";
												$fieldname2		= $field['columnname']."2";
												$fieldname3		= $field['columnname']."3";
												if($ModuleType=="Related" and $ActionList['ActionName']=="Create"){
													$RefFields=$ActionList['RefFields'];
													if(($field['columnname']=="customer_id" or $field['columnname']=="customerno")){
														$ref_hid_value=$RefFields['customerid'];
														$ref_disp_value=$RefFields['customername'];
													}
												} else {
													$ref_hid_value 	= $Record->{$field['columnname']};
													$ref_disp_value	= $field['reffieldvalue'];
												}
												echo $form->{'hiddenField'}($model,$fieldname, array ('class' => $field['classname'],'value'=>$ref_hid_value));

												$relatedmod	= $field['relatedmodulename'];
												$getRelatedDField= $field['getRelatedDisplayFieldName'];
												echo $form->{'hiddenField'}($model,$fieldname2, array ('class' => $field['classname'],'value'=>$relatedmod));
												echo $form->{'hiddenField'}($model,$fieldname3, array ('class' => $field['classname'],'value'=>$getRelatedDField)); ?>
													
											<?php if($ModuleType!="Related"){?>
												<!-- <a class="btn btn-default " onclick="removeTextValue('<?php echo $fieldname1;?>','<?php echo $fieldname;?>');"><i class="mdi mdi-window-close"></i></a> -->
											<?php }?>
											<input class="effect" type="text" id="<?php echo $fieldname1;?>" name="<?php echo $fieldname1;?>" value="<?php echo $ref_disp_value;?>" readonly='readonly'>
											<?php echo $form->labelEx($model,$field['fieldname'], array ('class' => 'control-label labelwidth')); ?>
																					
											<?php if($ModuleType!="Related"){?>
												<svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg" data-toggle="modal" data-target="#myModal22" aria-hidden="true" tabindex="-1" onclick="showCustomer1('<?php echo $fieldname2 ;?>','<?php echo $fieldname;?>','<?php echo $fieldname3;?>','customername','<?php echo $ModuleName; ?>','<?php echo $multiplebloackid ; ?>','<?php echo $multipleobjname;?>','<?php echo $Block->blockid; ?>')">
													<path d="M21 21.5L16.514 17.006L21 21.5ZM19 11C19 13.2543 18.1045 15.4163 16.5104 17.0104C14.9163 18.6045 12.7543 19.5 10.5 19.5C8.24566 19.5 6.08365 18.6045 4.48959 17.0104C2.89553 15.4163 2 13.2543 2 11C2 8.74566 2.89553 6.58365 4.48959 4.98959C6.08365 3.39553 8.24566 2.5 10.5 2.5C12.7543 2.5 14.9163 3.39553 16.5104 4.98959C18.1045 6.58365 19 8.74566 19 11V11Z" stroke="#2F80ED" stroke-width="2" stroke-linecap="round"/>
												</svg>
											<?php }
											}else{
												echo "work in progress for uitype -- ".$field['uitype'];
											} ?>
										</div>
									<?php } 
									$counter++;
								}?>	
							</div>

						</div>
						<?php if($Block->blockid !=6){ ?>
					</div>
					<?php } ?>
