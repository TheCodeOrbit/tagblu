<!-- ApproveDetailView.php File Refactored By Kaushal Kumar on 24-12-2021 -->
<?php $ModuleName=$ActionList['ModuleName'];
	$user_id=$_SESSION[Yii::app()->params['dirName']."_id"];
	$ActionName=$ActionList['ActionName'];
	$ModuleLabel=$ActionList['ModuleLabel'];
	$currentdate = date('Y-m-d');
	$ActionUrl=Yii::app()->createAbsoluteUrl($ModuleName)."/";
?>
<style>
	.body-container {
		height: auto;
		min-height: var(--space-main-body);
	}

	footer {
		z-index: 10;
	}
</style>
<div id="fullpage">
	<div class="topcontent-details">
		<div class="body-menu flex jc-spbt ai-cntr">
			<div>
				<p class="body-heading"><?php echo 'Approve '.$ModuleLabel;?> Data</p>
			</div>
		</div> 
	</div>
	<div class="body-container ">		
		<?php $form=$this->beginWidget('CActiveForm', array()); ?>
		<?php if($module=='users'){ ?>
			<div class="col-sm-10">
				<?php } ?>
				<?php foreach($ColumnList->DetailBlocks as $BlockKey=>$Block): ?>
					<div class="DetailView-recordDetails">
						<?php if($Block->blocktype=="Simple" || $Block->blockid=="SimpleTwoCol"):?>
							<?php if($Block->blocktype=="SimpleTwoCol"){?>
						<div class="body-outline h-auto mb-3">

					<div class="input-heading" style="margin:0.2rem 0.7rem;"><?php echo $Block->blocklable;?></div>
					<div class="simple-table d-flex justify-content-between flex-wrap" style="margin:0.2rem 0.7rem;">

<?php }else{ ?>
							<div class="mb-4 border border-1 border-primary border-radius-10 d-flex justify-content-evenly align-items-center px-4 py-2 mx-2rem">
<?php } ?>
								<?php foreach ($Block->DetailFields as $key=> $Field): ?>
									<?php if($Field->approve_detail_view==1){?>
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
												}

												else if($Field->uitype=="16" ){ 
												echo $Field->columnname = date('d/m/Y H:i:s',strtotime($Record->{$Field->columnname})); 
												}else{ 
													echo strip_tags($Record->{$Field->columnname}); 
													}	 
												}	
											?> 
											</p>
										</div>
									<?php }?>
								<?php endforeach;?>
							</div>

<?php if($Block->blocktype=="SimpleTwoCol"){?>
</div>
<?php } ?>































							<input type="hidden" name="ApproveDetailView[module]" id="module" value="<?php echo $ModuleName; ?>">
							<input type="hidden" name="ApproveDetailView[RecordId]" id="RecordId" value="<?php echo $Record->recordId; ?>">	
						<?php elseif($Block->blocktype=="Multiple"):?>
							<div class="body-outline h-auto mb-3">
								<table  id="block_<?php echo $Block->blockid;?>" class="table-view table table-striped">
									<thead>
										<?php if($Block->blocklable){ ?>
											<tr>
												<!-- below colspan needs to come dynamic based on the table th in total -->
												<th colspan="10">
													<div class="input-heading p-2">
														<?php echo $Block->blocklable;?>
													</div>
												</th>
											</tr>
										<?php } ?>
										<tr class="table-primary">
											<?php foreach ($Block->DetailFields as $key=> $Field):?>
												<th><?php echo strip_tags($Field->fieldlable);?></th>
											<?php endforeach;?>
										</tr>
									</thead>
										
									<tbody>
										<?php 
										if(count($Record[Multiple_Records][$Block->blockid])>0):
											foreach($Record[Multiple_Records][$Block->blockid] as $MPKey=>$Multiple_Record):?>
												<tr>
													<?php foreach($Block->DetailFields as $key=> $Field):?>
														<td class="text-center">
															<?php echo strip_tags($Multiple_Record->{$Field->columnname});?>	
														</td>
													<?php endforeach;?>			
												</tr>
											<?php endforeach;?> 	
										<?php endif;?>
									</tbody>
								</table>
							</div>
						<?php endif;?>
					</div>
				<?php endforeach;?>
				<?php if($module == 'users'){?>
					<?php include_once 'RightSide.php' ?>
				<?php }?>
				<div class="d-flex justify-content-end mx-2rem mb-2">
					<button type="button" class="btn btn-primary input-save me-5" data-bs-toggle="modal" data-bs-target="#addCommentModal">Recheck</button>
					<?php echo CHtml::submitButton('approve', array('name' => 'btnapprove','class' => 'btn btn-primary input-save','data-bs-toggle'=>'modal')); ?>	
				</div>
			</div>
			<!-- popUpModal -->
<div class="modal fade" id="addCommentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="d-flex justify-content-center adani-bold">
        <h5 class="modal-title h3" id="exampleModalLongTitle">Add Comments</h5>
      </div>
      <div class="d-flex justify-content-center my-4">
		<textarea class="form-control textarea-height b-prim" rows="3" name="ApproveDetailView[comment]"></textarea>
      </div>
      <div class="d-flex justify-content-center">
        <button type="button" class="btn btn-danger input-save me-5" data-bs-dismiss="modal">Discard</button>
		<?php echo CHtml::submitButton('Submit', array('name' => 'btnrecheck','class' => 'btn btn-primary input-save close','data-toggle' => 'modal', 'data-target' => '#addCommentModal')); ?>
      </div>
    </div>
  </div>
</div>
		<?php $this->endWidget(); ?>
	</div>
</div>


<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/dailydrilling/ApproveList.js"></script>
<script>
	
</script>
