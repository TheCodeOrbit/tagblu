<style>
	.body-container {
		overflow: auto;
	}
</style>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/chosen.jquery.min.js"></script>

<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.inputmask.bundle.js"></script>

<?php
$siteDir = Yii::app()->params['dirNameehs'];
$ModuleName=$ActionList['ModuleName'];
$ModuleType=$ActionList['ModuleType'];
$MineType=$ActionList['MineType'];
//echo "<br>MineType=$MineType";
$ActionName=$ActionList['ActionName'];
$isDepotUser=$ActionList['isDepotUser'];
$ModuleLabel=$ActionList['ModuleLabel'];
$depotcode=$_SESSION[$siteDir.'_depot_code'];
$_SESSION['countpro']="";
$_SESSION['taxcounterr']="";
$sesionid=$_SESSION[$siteDir.'_id'];
// print_r($_SESSION);die;
$MineNamee=$_SESSION['cms_mine_name'];
if($MineNamee=='pekb'){
$MineName=1;	
}elseif($MineNamee=='talabira'){
$MineName=2;	
}elseif($MineNamee=='gp3'){
$MineName=3;	
}elseif($MineNamee=='kurmitar'){
$MineName=4;	
}else{

$MineName=5;	

}




$invmngrule = $ActionList['invmngrule_details']['invmngrule'];
if($_SESSION['blastnoc'] == '')
	{$bno=($cnt_multiple_product+1) ;
		$_SESSION['blastnoc'] =$bno;
	}
//echo "<pre>";print_r($invmngrule);exit;
//print_r($_SESSION);
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
			<input type="hidden" value="<?php echo $MineName; ?>" id="mine_name" name="EditModel[mine_name]"/>
			
				
			
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
								<?php if($ActionName=="Create"){
								$dis_field_name=$field['fieldname'];
								$ele_disabled=false;$ele_readonly=false;} else{

								$dis_field_name=$field['fieldname'].'dis';
								echo $form->hiddenField($model,$field['fieldname'], array ('value'=>$Record->{$field['columnname']}));
								$ele_disabled=true;$ele_readonly='readonly';}?>
								<?php //echo $form->error($model,$field['fieldname'], array('class'=>'ajxwarning errorMessage')); ?>
								<?php echo $form->{$field['fieldtype']}($model,$field['fieldname'], $field['fieldoptions'],array('class' => 'form-select w-150px','disabled'=>$ele_disabled,'value'=>$field['columnname'],'empty'=>'Select '.$field['fieldlable'],'options' => array($Record->{$field['columnname']}=>array('selected'=>true)))); ?>
								<?php echo $form->error($model,$field['fieldname'],array('class'=>'ajxwarning errorMessage error-container','style'=>"top: 3rem;position: relative;left: -15rem;")); ?>
							<?php elseif($field['uitype']==2): 
								echo $form->hiddenField($model,$field['fieldname'], array ('class' => $field['classname'],'value'=>$Record->{$field['columnname']}));
							elseif($field['uitype']==1):?>
								
								<?php echo $form->error($model,$field['fieldname'], array('class'=>'ajxwarning errorMessage')); ?>
								<?php if(($ModuleName=="obr_contractor" and $field['fieldname']=="total_nooftrips") or ($ModuleName=="cecontractor" and $field['fieldname']=="total_nooftrips")):
									echo $form->hiddenField($model,$field['fieldname'], array ('class' => $field['classname'],'value'=>$Record->{$field['columnname']}));?>
									<div class="error-container hide">Please Enter Some value</div>
<?php
								else:
									echo $form->{$field[fieldtype]}($model,$field['columnname'], array ('autocomplete' => 'new-password','class' => $field["classname"].' form-control inputwidth','value'=>$Record->{$field['columnname']}));?>
									<div class="error-container hide">Please Enter Some value</div>

									<?php endif; ?>
									
									<?php elseif($field['uitype']==13):
							include 'uitype/Date.php'; 
							
							/************** Jitender Maithani code start ***************/
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
								<div class="error-container hide">Please Enter Some value</div>	
									
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
						<?php if($ActionName=="Create" and $ModuleName!="production"){ ?>
							<button type="button" id="showModel" class="btn btn-primary input-save" data-bs-toggle="modal" data-bs-target="#exampleModal">SAVE</button>
						<?php }  else {?>
													<?php if(($ModuleName=='dailydrilling' or $ModuleName=="dailyblasting" or $ModuleName=="treefelling" or $ModuleName=="screening_crushing" or $ModuleName=="obcesummary" or $ModuleName=="washeryinput" or $ModuleName=="logisticmine_12" or $ModuleName=="logisticsiding") and $Record->comment!=""){?>
                        <button type="button" class="btn btn-primary input-save me-5" data-toggle="modal" data-target="#ViewCommentModal">View Comment</button>
													<?php }?>
						<?php echo CHtml::submitButton('Discard', array('name' => 'btncancel','class' => 'btn btn-outline-danger btn-custom me-5')); ?>
						<?php echo CHtml::submitButton('Save', array('class' => 'btn btn-primary btn-custom')); ?>
					<?php } ?>
            		</div>
        		</div>
			</div><!-- topcontent ends -->

		
	<?php if($Block->blocktype=="Multiple" and $Block->blocksubtype=="MultipleTwoCol"){?>
			<div class="body-container coal-grid">
<!-- 			<input type="hidden" value="<?php echo $MineName; ?>" id="mine_name" name="mine_name"/>
 -->
	<?php } else if($Block->blocktype=="SimpleTwoColBlock"){?>
		
		<div class="body-container " style="font-size: 13px;">
<!-- 		<input type="hidden" value="<?php echo $MineName; ?>" id="mine_name" name="mine_name"/>
 -->
	<?php } else{ ?>
			<div class="body-container">
<!-- 			<input type="hidden" value="<?php echo $MineName; ?>" id="mine_name" name="mine_name"/>
 -->
	<?php }?>
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
					
				<!-- UI types
				1 : textField
				2 :hidden
				8 :dropDownList
				12 :referencetype
				13 :DateTimePicker -->
			
					<?php if($Block->blocktype=="SimpleTwoCol"){  //echo "Two column";
							include 'SimpleTwoCol.php';
						}

					  else if($Block->blocktype=="SimpleFourCol"){  //echo "Four column";
							include 'SimpleFourCol.php';
						}

					 else if($Block->blocktype=="SimpleTwoColBlock"){  //echo "Four column";
							include 'SimpleTwoColBlock.php';
						}
          ?>
			
						<?php if($Block->blocktype=="Multiple" and $Block->blocksubtype=="MultipleTwoCol") {include 'MultipleTwoColBlock.php'; ?>
					<?php 
						}else if($Block->blocktype=="Multiple"){
						include 'MultipleBlock.php';
					} ?>

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
			<?php if($ModuleName == "users"){ ?>
				<input class="form-control" id="publickey" type="hidden" value="<?php echo $publickey ?>">
			<?php } ?>
		<?php echo CHtml::submitButton('Submit', array('class' => 'btn btn-primary input-save me-5 close')); ?>	
                <!--<button type="button" class="btn btn-primary input-save me-5 close" data-dismiss="alert">submit</button>-->
                <button type="button" class="btn btn-danger input-save" data-bs-dismiss="modal">Discard</button>
            </div>
          </div>
        </div>
    </div>  	
			<?php $this->endWidget(); ?>
		
	
</div> <!-- fullpage ends -->
<?php if(($ModuleName=='dailydrilling' or $ModuleName=="dailyblasting" or $ModuleName=="treefelling" or $ModuleName=="screening_crushing" or $ModuleName=="obcesummary" or $ModuleName=="washeryinput" or $ModuleName=="logisticmine_12" or $ModuleName=="logisticsiding") and $Record->comment!=""){?>
<!-- popUpModal -->
<div class="modal fade" id="ViewCommentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="d-flex justify-content-center adani-bold">
                <h5 class="modal-title h3" id="exampleModalLongTitle">View Comments</h5>
            </div>
            <div class="d-flex justify-content-center my-4">
                <textarea class="form-control textarea-height b-prim" rows="3" readonly><?php echo $Record->comment;?></textarea>
            </div>
            <div class="d-flex justify-content-center">
                <button type="button" class="btn btn-danger input-save me-5" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php }?>
<style>
.without_ampm::-webkit-datetime-edit-ampm-field {
	display: none;
}

input[type=time]::-webkit-clear-button {
	-webkit-appearance: none;
	-moz-appearance: none;
	-o-appearance: none;
	-ms-appearance:none;
	appearance: none;
	margin: -10px; 
}
</style>
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
