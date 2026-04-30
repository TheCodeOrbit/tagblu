		<?php session_start(); 
			$siteDir = Yii::app()->params['dirName'];
		?>							
						<tr >	
							<?php
							static $counterp=0;
							if($_SESSION['countpro'])
							{ $counterp=$_SESSION['countpro']; }
							else
							{ static $counterp=0; 
							 						
							}		
							
							 $refield=$_REQUEST['field'];
							 $name=$_REQUEST['rdisfield'];							
							$depotcode=$_SESSION[$siteDir.'_depot_code'];
							if($_REQUEST['addn'] != 'addnew'):												

							if(count($RecordList)>0):												
								
							else :?>
							
							<?php
							endif;
							else:
							
							if($_SESSION['taxcounterr'])
							{
							$totcountppp=$_SESSION['taxcounterr'];
							$totcountppp=($totcountppp+1);
							}
							else
							{
								$totcountppp=1;
							}

							if($_SESSION['countpro'])
							{
							$counterp=$_SESSION['countpro'];
							$blastno=$_SESSION['blastnoc'];
							}
							else
							{
								$counterp=0;
								$blastno=1;
							}

							?>
							<tr >
							<td class="<?php echo $_REQUEST['objname']."Delete"; ?>" onclick="delDeductCalculation('<?php echo $counterp ;?>');"><a href="javascript:void;"><span class="glyphicon glyphicon-trash"></span></a><?php //print_r($_SESSION);?></td>
								<?php

								print_r($getDailyBlastchildField);

								foreach ($getDailyBlastchildField as $key=> $uitype):										
								?>
								<td >							
                                <span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
								

								<?php if($uitype == '12'): ?>
								<div class="input-group">
									<span class="input-group-addon">
										<span class="glyphicon glyphicon-remove-circle cursorPointer text-info" type="button" onclick="removeTextValue('<?php echo  $key."".$counterp ;?>');"></span>
									</span>
								<?php  endif; ?>                              
								
								
								<?php if($uitype == '12'): 
								
								$tableName=$_REQUEST['objname'];								
								$model=new Reference($tablename);
								$fieldname=$model->getRelatedFieldId($tableName,$key);									
								$modulename=$model->getRelatedNoduleName($fieldname);	
								
								?>
<div id="<?php echo $key."id".$counterp."_em_" ;?>" class="ajxwarning errorMessage tooltipImages bb5" style="display:none">Product Name cannot be blank.</div>

								<input type="text" value="" id="<?php echo $key."".$counterp;?>" name ="<?php echo  $key."".$counterp ;?>" size=12 class="form-control ui-autocomplete-input" autocomplete="off" <?php echo $readonly; ?>>

								<input type="hidden" value="" name ="<?php echo $_REQUEST['objname']."[".$counterp."][".$key."]" ;?>" id="<?php echo $key."id".$counterp ;?>">

								<span class="transearch input-group-addon">
								<span type="button" class="glyphicon glyphicon-search cursorPointer text-info" data-toggle="modal" data-target="#myModal22" onclick="showProductlistPop('<?php echo $counterp ;?>','<?php echo $key; ?>','<?php echo $modulename;?>')"></span>

								</span>

								</div>

								<?php	elseif($uitype==8): ?>
								<?php 
								$TableName="dailyblasting";
								$FieldId="dailyblasting_id";
								$fldid="57";
								$PickList=new MiningDailyBlasting($TableName);
                                $ModuleName="DailyBlasting";
								
								//$PickList->fieldid=$Field->fieldid;
								//echo $Block->Fields[0]->tablename;
								$fieldoptions=$PickList->getPickListValue($fldid);
								//print_r($fieldoptions);
								?>
	
	
<div id="<?php echo $Field->columnname.'id'.$cnt_multiple_product.'_em_';?>" class="ajxwarning errorMessage tooltipImages bb2" style="display:none">Stock Type cannot be blank.</div>

		<?php 
		$selectid=$_REQUEST['objname']."_".$counterp."_".$key ;
		$selectname=$_REQUEST['objname']."[".$counterp."][".$key."]" ;
echo CHtml::dropDownList($selectname, '',$fieldoptions,array('empty' => 'Select an Option','id'=>$selectid));?>
		<?php //echo $form->error($model,$Field['fieldname'], array('class'=>'ajxwarning errorMessage')); ?>

								<?php else: 
								
								
								?>
								
                               <input type="text" value="<?php if($key == 'blasting_no'){echo $blastno; } ?>" id="<?php echo $_REQUEST['objname']."_".$counterp."_".$key ;?>" name ="<?php echo $_REQUEST['objname']."[".$counterp."][".$key."]" ;?>" class="form-control ui-autocomplete-input productValidation8" autocomplete="off" <?php if($key == 'blasting_no'){echo "readonly"; } ?> onkeyup="<?php if($key == 'explosive_charge'){ ?> floatvalidation('<?php echo $_REQUEST['objname']."_".$counterp."_".$key ;?>'); <?php }else { ?> integervalidation('<?php echo $_REQUEST['objname']."_".$counterp."_".$key ;?>'); <?php } ?>" >
									<input type="hidden" value="<?php echo $Record[$key];?>" id="org_<?php echo $_REQUEST['objname']."_".$counterp."_".$key ;?>">
                                <?php
								endif;
								
								?>
                               
								</td>
								<?php
                                //$totcountppp++;
								endforeach;
								$counterp++;	
								$blastno++;
								$_SESSION['countpro']=$counterp;	
								$_SESSION['blastnoc']=$blastno;
								$_SESSION['taxcounterr']=$totcountppp;
								
								?>     

                                </tr>
							<?php											
							
							endif;
							?>
							<input type="hidden" id="dailyblastcounter" value="<?php echo $_SESSION['taxcounterr'];?>">
						</tr>
						
						
					
			
		
