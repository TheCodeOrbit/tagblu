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

 <div id="AddDrill" class="">
	
		
			<?php $form=$this->beginWidget('CActiveForm', array( 
			'enableClientValidation'=>true,
			'clientOptions'=>array( 'validateOnSubmit'=>true, ), )); 
			
			
				
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
			
				
			
			<div class="topcontent-details"><!-- topcontent starts -->
			 <div class="body-menu flex jc-spbt ai-cntr">
                <div>
                    <p class="body-heading">Add<?php echo ' '.$ModuleLabel;?> Data</p>
                </div>
                <div class="flex jc-spbt ai-cntr">
                <?php 
				$grand_total=false;
				foreach($ColumnList->Blocks as $BlockKey=>$Block):?>		

				<?php if($Block->blocktype=="Simple"): ?>
				<?php $counter=1;foreach($Block->Fields as $field): ?>
					<?php if($field['uitype']==8):?>
					
			<?php echo $form->error($model,$field['fieldname'], array('class'=>'ajxwarning errorMessage')); ?>
								<?php echo $form->{$field[fieldtype]}($model,$field['fieldname'], $field['fieldoptions'],array('class' => 'form-select w-150px','value'=>$field['columnname'],'empty'=>'Select Shift','options' => array($Record->{$field['columnname']}=>array('selected'=>true)))); ?>

					<?php elseif($field['uitype']==1):?>

					<?php echo $form->error($model,$field['fieldname'], array('class'=>'ajxwarning errorMessage')); ?>
					<?php echo $form->{$field[fieldtype]}($model,$field['columnname'], array ('autocomplete' => 'new-password','class' => $field["classname"].' form-control inputwidth','value'=>$Record->{$field['columnname']}));?>

					<?php elseif($field['uitype']==13):
						include 'uitype/Date.php'; ?>
					<?php endif;?>
				<?php endforeach;?>
                    
                    <?php endif;?>
                   <?php endforeach;?>
                </div>
                <div>

		<?php //echo CHtml::submitButton('Save', array('class' => 'btn btn-primary input-save','id'=>'showModel','data-bs-toggle'=>'modal','data-bs-target'=>'#exampleModal')); ?>	
                    <button type="button" id="showModel" class="btn btn-primary input-save" data-bs-toggle="modal" data-bs-target="#exampleModal">SAVE</button>
                </div>
            </div>
				<!--<div class="row">
					<div class="col-sm-8">
						<h4 class="h4 page-heading no-gutter"><?php echo $ActionName.' '.$ModuleLabel;?> Data</h4> 
					</div>
					
					<div class="col-sm-4">
						<div class="pull-right" role="group" aria-label="...">
							<?php echo CHtml::submitButton('Save', array('class' => 'btn topcontent-savebtn')); ?>
							<? echo '&nbsp;&nbsp;&nbsp;'; ?>
							<?php echo CHtml::submitButton('Discard', array('name' => 'btncancel','class' => 'btn topcontent-discardbtn')); ?>
						</div>
					</div>
				</div> -->
				<!--<div class="row">
					<div class="col-sm-12">
						<?php if($ActionList['ErrorMsg'] != ''): ?>
						<div class="alert alert-danger top-alert-error">
						 <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
						 <strong><?php echo $ActionList['ErrorMsg'];?></strong>
						</div>	
						<?php endif; ?>
					</div>
				</div>-->
			</div><!-- topcontent ends -->

		

		
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
			<div class="body-container">
                <div class="body-outline">
                    <table id="Tbl_<?php echo $obj_name;?>">
                        <tr>
                        	<?php foreach ($Block->Fields as $key=> $Field): ?>
                        	<th><?php echo $Field->fieldlable;?></th>
                        	<?php endforeach;?>
                        	<th>Tools</th>
                            <!--<th>Drilling material</th>
                            <th>Drill Machine No.</th>
                            <th>Working Area</th>
                            <th>No of Holes Drilled</th>
                            <th>Burden(m)</th>
                            <th>Hole Depth(m)</th>
                            <th>Spacing(m)</th>
                            <th>Type of Drilling</th>
                            <th>Bench Height(m)</th>
                            <th>Tools</th>-->
                        </tr>
                        
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
						if($cnt_multiple_product>0):
				foreach($Record[Multiple_Records][$Block->blockid] as $MPkey=> $Multiple_Record): $tax_html="";?>
                        
                        <?php endforeach;?>
						<?php endif; ?>
                    </table>
                    <div class="add-btn flex jc-cntr ai-cntr" id="Add<?php echo $obj_name;?>" rel="<?php echo $Block->blockid;?>">
                        <svg width="16" height="16" viewBox="0 0 16 16">
                            <path d="M6 16H10V10H16V6H10V0H6V6H0V10H6V16Z"/>
                        </svg>                        
                    </div>
                </div>
            </div>
            <!-- Multiple Block Code End -->
            <?php //endif;?>
			


	

		<?php endif;?>




			<?php //endforeach;?>

			<?php endforeach;?>

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
              <h5 class="modal-title text-center body-heading" id="exampleModalLabel">Add Drill Final Submit</h5>
              <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
            </div>
            <div class="modal-body">
		<table class="Tbl_<?php echo $obj_name;?>">

		</table>
                <!--<table>
                    <tr>
                        <th>Drilling material</th>
                        <th>Drill Machine No.</th>
                        <th>Working Area</th>
                        <th>No of Holes Drilled</th>
                        <th>Burden(m)</th>
                        <th>Hole Depth(m)</th>
                        <th>Spacing(m)</th>
                        <th>Type of Drilling</th>
                        <th>Bench Height(m)</th>
                    </tr>
                    <tr>
                        <td>Coal</td>
                        <td>CG4DM5376</td>
                        <td>Salhi Pit</td>
                        <td>889</td>
                        <td>8.00</td>
                        <td>8.00</td>
                        <td>8.00</td>
                        <td>Normal</td>
                        <td>8.00</td>
                    </tr>
                </table> -->
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
