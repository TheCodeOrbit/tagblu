		<?php session_start(); 
			
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
							$depotcode=$_SESSION['depot_code'];
							if($_REQUEST['addn'] != 'addnew'):	

							$countrecord=count($RecordList);
							if($_SESSION['taxcounterr'])
							{
							$totcountppp=$_SESSION['taxcounterr'];							
							$counter=($totcountppp + $countrecord);
							$_SESSION['taxcounterr'] = $counter;						
							} 
							else
							{
								$_SESSION['taxcounterr'] = $countrecord ;
							}							

							if(count($RecordList)>0):												
								//static $counterp=0;
								foreach ($RecordList as $Record):							
								?>                               
							    <tr >
								<td class="<?php echo $_REQUEST['objname']."Delete"; ?>" onclick="delDeductCalculation('<?php echo $counterp ;?>');">						
									<a href="javascript:void;"><span class="glyphicon glyphicon-trash"></span></span></a>								
								</td>								
								<?php
								foreach ($InvProdFieldList as $key=> $uitype):									
								?>
								<td >										 
                                <span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>								
								<?php 								
								if($uitype == '12'): ?>
								<div class="input-group">
									<span class="input-group-addon">
										<span class="glyphicon glyphicon-remove-circle cursorPointer text-info" type="button" onclick="removeTextValue('<?php echo  $key."".$counterp ;?>');"></span>
									</span>
								<?php  endif; 							
								
								if($key == 'extrafreeqty' OR $key == 'sold_qty')
								{ $readonly=""; }
								else
								{	$readonly="readonly";}								
								
								?> 							
									<?php if($uitype == '12'): 
									$key1=$key."id";

									$tableName=$_REQUEST['objname'];								
									$model=new Reference($tablename);
									$fieldname=$model->getRelatedFieldId($tableName,$key);									
									$modulename=$model->getRelatedNoduleName($fieldname);	
									?>
<div id="<?php echo $key."id".$counterp."_em_" ;?>" class="ajxwarning errorMessage tooltipImages bb5" style="display:none">Product Name cannot be blank.</div>
									<input type="text" value="<?php echo $Record[$key];?>" id="<?php echo $key."".$counterp;?>" name ="<?php echo  $key."".$counterp ;?>" class="form-control ui-autocomplete-input" autocomplete="off" <?php echo $readonly; ?>>

									<input type="hidden" value="<?php echo $Record[$key1]; ?>" name ="<?php echo $_REQUEST['objname']."[".$counterp."][".$key."]" ;?>" id="<?php echo $key."id".$counterp ;?>">

									<span class="transearch input-group-addon">
										<span type="button" class="glyphicon glyphicon-search cursorPointer text-info" data-toggle="modal" data-target="#myModal22" onclick="showProductlistPop('<?php echo $counterp ;?>','<?php echo $key; ?>','<?php echo $modulename;?>')"></span>
									</span>
								</div>	
								<?php  
								else:								
								if($key == 'batch_no'):
								?>
								<div class="input-group">
<div id="<?php echo $_REQUEST['objname']."_".$counterp."_".$key."_em_" ;?>" class="ajxwarning errorMessage tooltipImages bb5" style="display:none">Batch No cannot be blank.</div>
								<input type="text" value="" id="<?php echo $_REQUEST['objname']."_".$counterp."_".$key ;?>" name ="<?php echo $_REQUEST['objname']."[".$counterp."][".$key."]" ;?>" class="form-control ui-autocomplete-input BatchValidation8" autocomplete="off" <?php echo $readonly; ?>>

								<span class="transearch input-group-addon">
										<span type="button" class="glyphicon glyphicon-search cursorPointer text-info" data-toggle="modal" data-target="#myModal22" onclick="showBatchNo('<?php echo $counterp; ?>','<?php echo $depotcode;?>');"></span>
									</span>
									</div>

								<?php else: 
								
								if($key == 'tax_per'):
								?>
							<div id="<?php echo $_REQUEST['objname']."_".$counterp."_".$key."_em_" ;?>" class="ajxwarning errorMessage tooltipImages bb5" style="display:none">Tax Details cannot be blank.</div>
								<label id="lbl_<?php echo $_REQUEST['objname']."_".$counterp."_".$key ;?>" onclick="showTaxPerDetails('<?php echo $counterp; ?>','<?php echo $depotcode;?>','record',event);">Tax Details</label><br/>

                                <input type="text" value="<?php echo $Record[$key];?>" id="<?php echo $_REQUEST['objname']."_".$counterp."_".$key ;?>" name ="<?php echo $_REQUEST['objname']."[".$counterp."][".$key."]" ;?>" class="form-control ui-autocomplete-input" autocomplete="off" <?php echo $readonly; ?>>	
								<div id="tax_hidden_field<?php echo $counterp; ?>"></div>
								<div id="dialog1s-modal1s" class="dialog" style="background-color:#fff;"></div>

								<?php
								else:	
								if($key == 'price')
								{ ?>
                                    <input type="hidden" value="mrp" id="<?php echo $_REQUEST['objname']."_".$counterp."_".mrp ;?>" name ="<?php echo "mrp".$counterp ;?>" class="form-control ui-autocomplete-input">

								<?php }
								?>
<div id="<?php echo $_REQUEST['objname']."_".$counterp."_".$key."_em_" ;?>" class="ajxwarning errorMessage tooltipImages bb5" style="display:none">Tax Details cannot be blank.</div>
                                <input type="text" value="<?php echo $Record[$key];?>" id="<?php echo $_REQUEST['objname']."_".$counterp."_".$key ;?>" name ="<?php echo $_REQUEST['objname']."[".$counterp."][".$key."]" ;?>" class="form-control ui-autocomplete-input" autocomplete="off" <?php echo $readonly; ?> <?php if($key == 'sold_qty'){ ?>onkeyup="showTaxPerDetailsHiddenF('<?php echo $Record['batch_no'];?>','<?php echo $counterp; ?>','<?php echo $depotcode;?>');showTaxValue('<?php echo $a ;?>','<?php echo $a ;?>','<?php echo $depotcode ;?>','0','<?php echo $counterp; ?>','<?php if($_SESSION['countpro']){echo $_SESSION['countpro'];}else{ echo $countrecord;} ?>');" <?php } ?> >

<input type="hidden" value="<?php echo $Record[$key];?>" id="org_<?php echo $_REQUEST['objname']."_".$counterp."_".$key ;?>">

                                <?php

								endif;
								endif;
								endif; ?> 
								</td>
								<?php
								endforeach;
								?>   
                                </tr>
								<?php 							
								
								$counterp++;
								endforeach;
								
								//$taxcounter++;
								$_SESSION['taxcounterr']=$totcountppp;
								
								$_SESSION['countpro']=$counterp;
								//echo $_SESSION['countpro'];
								?>								
							
							<?php //endforeach; 
							else :?>
							<tr>
							<td class="text-center" colspan="<?php echo $col_span;?>">No Record Found</td>
							</tr>
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
							}
							else
							{
								$counterp=0;
							}

							?>
							<tr >
							<td class="<?php echo $_REQUEST['objname']."Delete"; ?>" onclick="delDeductCalculation('<?php echo $counterp ;?>');"><a href="javascript:void;"><span class="glyphicon glyphicon-trash"></span></a><?php //print_r($_SESSION);?></td>
								<?php

								foreach ($InvProdFieldList as $key=> $uitype):	

								if($key == 'extrafreeqty' OR $key == 'sold_qty')
								{ $readonly=""; }
								else
								{	$readonly="readonly";}	
								?>
								<td >							
                                <span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
								

								<?php if($uitype == '12'): ?>
								<div class="input-group">
									<span class="input-group-addon">
										<span class="glyphicon glyphicon-remove-circle cursorPointer text-info" type="button" onclick="removeTextValue('<?php echo  $key."".$counterp ;?>');"></span>
									</span>
								<?php  endif; ?> 
                                <!--<span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>-->
								
								
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
								<?php  
								else:
								?>
                               <!--  <input type="text" value="" id="<?php echo $_REQUEST['objname']."_".$counterp."_".$key ;?>" name ="<?php echo $_REQUEST['objname']."[".$counterp."][".$key."]" ;?>" size=12 class="form-control ui-autocomplete-input" autocomplete="off">
							   -->
							   <?php
							   if($key == 'batch_no'):
								?>
								<div class="input-group">
<div id="<?php echo $_REQUEST['objname']."_".$counterp."_".$key."_em_" ;?>" class="ajxwarning errorMessage tooltipImages bb5" style="display:none">Batch No cannot be blank.</div>
								<input type="text" value="" id="<?php echo $_REQUEST['objname']."_".$counterp."_".$key ;?>" name ="<?php echo $_REQUEST['objname']."[".$counterp."][".$key."]" ;?>" class="form-control ui-autocomplete-input BatchValidation8" autocomplete="off" <?php echo $readonly; ?>>

								<span class="transearch input-group-addon">
										<span type="button" class="glyphicon glyphicon-search cursorPointer text-info" data-toggle="modal" data-target="#myModal22" onclick="showBatchNo('<?php echo $counterp; ?>','<?php echo $depotcode;?>');"></span>
									</span>
									</div>

								<?php else: 
                                if($key == 'tax_per'):

								?>	
							<!--	<div class="input-group"> -->
                                <?php //echo $_REQUEST['objname']."_".$counterp."_".$key ;?>
<div id="<?php echo $_REQUEST['objname']."_".$counterp."_".$key."_em_" ;?>" class="ajxwarning errorMessage tooltipImages bb5" style="display:none">Tax Details cannot be blank.</div>
								<label id="lbl_<?php echo $_REQUEST['objname']."_".$counterp."_".$key ;?>" onclick="showTaxPerDetails('<?php echo $counterp; ?>','<?php echo $depotcode;?>','record',event);">Tax Details</label><br/>

                                <input type="text" value="<?php echo $Record[$key];?>" id="<?php echo $_REQUEST['objname']."_".$counterp."_".$key ;?>" name ="<?php echo $_REQUEST['objname']."[".$counterp."][".$key."]" ;?>" class="form-control ui-autocomplete-input" autocomplete="off" <?php echo $readonly; ?>>	

								<div id="tax_hidden_field<?php echo $counterp; ?>"></div>
								<!--<div id="dialog1s-modal1s" class="dialog" style="display:none; background-color:#fff;"></div>	-->

								<div id="dialog1s-modal1s" class="dialog" style="background-color:#fff;"></div>

								

								<?php
								else:

								if($key == 'price')
								{ ?>
                                    <input type="hidden" value="mrp" id="<?php echo $_REQUEST['objname']."_".$counterp."_".mrp ;?>" name ="<?php echo "mrp".$counterp ;?>" class="form-control ui-autocomplete-input">

								<?php
								}
								 
								?>
<div id="<?php echo $_REQUEST['objname']."_".$counterp."_".$key."_em_" ;?>" class="ajxwarning errorMessage tooltipImages bb5" style="display:none">Tax Details cannot be blank.</div>
                                <input type="text" value="<?php echo $Record[$key];?>" id="<?php echo $_REQUEST['objname']."_".$counterp."_".$key ;?>" name ="<?php echo $_REQUEST['objname']."[".$counterp."][".$key."]" ;?>" class="form-control ui-autocomplete-input productValidation8" autocomplete="off" <?php echo $readonly; ?> <?php if($key == 'sold_qty' and $key['batch_no'] != '' ){ ?>onkeyup="showTaxPerDetailsHiddenF('<?php echo $Record['batch_no'];?>','<?php echo $counterp; ?>','<?php echo $depotcode;?>');showTaxValue('<?php echo $a ;?>','<?php echo $a ;?>','<?php echo $depotcode ;?>','0','<?php echo $counterp; ?>','<?php echo $_SESSION['countpro'] ; ?>');" <?php } ?> >
									<input type="hidden" value="<?php echo $Record[$key];?>" id="org_<?php echo $_REQUEST['objname']."_".$counterp."_".$key ;?>">
                                <?php
								endif;
								endif;
								?>
                                <?php
								endif; 								
								?> 
								</td>
								<?php
                                //$totcountppp++;
								endforeach;
								$counterp++;								
								$_SESSION['countpro']=$counterp;								
								$_SESSION['taxcounterr']=$totcountppp;
								
								?>     

                                </tr>
							<?php											
							
							endif;
							?>
							<input type="hidden" id="taxcounter" value="<?php echo $_SESSION['taxcounterr'];?>">
						</tr>
						
						
					
			
		
