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
<div class="row" id="fullpage"><!-- fullpage starts -->
	<?php include_once 'LeftSide.php';?>

	<div class="col-sm-10 rightside-page maincontent-details" id="rightside-main-details"> <!-- main details page starts -->
		<div class="topcontent-details"><!-- header part starts -->
			<span class="toggleButton glyphicon glyphicon-chevron-left" id="collapsebtn-details" data-toggle="collapse" data-target="#leftsiede-summary"></span>
			<div class="row">
				<div class="col-sm-5">
					<h4 class="h4 page-heading no-gutter"><?php echo $ModuleLabel;?> Detail</h4>
				</div>
				<div class="col-sm-7">
					<?php
						if($_REQUEST['SourceModule']!="")
						{
							$RelatedSourceModule=$_REQUEST['SourceModule'];
							$RelatedSourceRecord=$_REQUEST['SourceRecord'];
							$DetailUrl="{$ActionUrl}Edit/Record/{$Record->recordId}/SourceModule/{$RelatedSourceModule}/SourceRecord/{$RelatedSourceRecord}";
						}
						else
						$DetailUrl="{$ActionUrl}Edit/Record/{$Record->recordId}";
						$val = explode(",",$operation['opt']); 
						$permod = $operation['name'];
						$module = $ModuleName;
					?>
					<?php if (in_array('2',$val) and $module == $permod){ ?>
					<?php } else if($operation['opt'] =='1') { ?>	
						<?php if($twantyfour == '1' && $rcdate == $currentdate){ ?>
						<button class="btn" onclick="window.location ='<?php echo $DetailUrl;?>'">Edit &nbsp;<span class=" glyphicon glyphicon-edit"></span></button>
						<?php } else if($user_id=='1') { ?> <button class="btn" onclick="window.location ='<?php echo $DetailUrl;?>'">Edit &nbsp;<span class=" glyphicon glyphicon-edit"></span></button> <?php } else{ ?>
<button class="btn" onclick="window.location ='<?php echo $DetailUrl;?>'">Edit &nbsp;<span class=" glyphicon glyphicon-edit"></span></button>
						<?php } ?>
					<?php } else { ?>
					<?php if($twantyfour == '1' && $rcdate == $currentdate) { ?>
					<button class="btn" onclick="window.location ='<?php echo $DetailUrl;?>'">Edit &nbsp;<span class=" glyphicon glyphicon-edit"></span></button> 
					<?php } else if($user_id=='1') { ?> <button class="btn" onclick="window.location ='<?php echo $DetailUrl;?>'">Edit &nbsp;<span class=" glyphicon glyphicon-edit"></span></button> <?php } else { ?>
<button class="btn" onclick="window.location ='<?php echo $DetailUrl;?>'">Edit &nbsp;<span class=" glyphicon glyphicon-edit"></span></button>
					<?php } ?>
					<?php } ?>
					<!--	<button class="btn">Send Email</button>
						<button class="btn">Converted Lead</button>
						<button class="btn">More..</button>
						<button class="btn"> <span class="glyphicon glyphicon-wrench"></span></button> -->
				</div>
				<!--	<div class="col-sm-2">
					<div class="btn-group pull-right" role="group" aria-label="...">
						<button type="button" class="btn"><</button>
						<button type="button" class="btn">></button>
					</div>
				</div> -->
			</div>
		</div><!-- header part ends -->

		<!-- body parts of main details page --> 
		<div class="container-fluid">
			<div class="recordDetails-container row">
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
						<?php if($Block->blocktype!="Tax"):?>
						<div class="DetailView-recordDetails-header"> <a href="#"><span class="glyphicon glyphicon-triangle-bottom caretCollapse"></span></a><strong> &nbsp;<?php echo $Block['blocklable'];?></strong></div>
						<?php endif;?>
						<div class="DetailView-table">
							<table class="table table-bordered table-responsive" id="block_<?php echo $Block->blockid;?>">
								<tbody>
									<?php if($Block->blocktype=="Simple"):?>
										<tr>
											<?php $counter=1;
											//echo "<pre>";
											//print_r($Block['columnlist'] );
											foreach ($Block->DetailFields as $key=> $Field): ?>	
											<td class="record-label" id="lbl_<?php echo $Field->fieldname; ?>"><?php echo $Field->fieldlable; ?></td>
											<td class="record-value" id="<?php echo $Field->fieldname; ?>">
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
												}	?> 
											</td>
											<?php if($counter%2==0):?>
										</tr>

										<tr>
											<?php endif;?>
											<?php $counter+=1; endforeach;?>
										</tr>

										<input type="hidden" name="module" id="module" value="<?php echo $ModuleName; ?>">

									<?php elseif($Block->blocktype=="Multiple"):?>
										<tr>
											<?php foreach ($Block->DetailFields as $key=> $Field):?>
												<td class=""><b><?php echo strip_tags($Field->fieldlable);?></b></td>
											<?php endforeach;?>
										</tr>

										<?php 
										//echo "<pre>";
										//print_r($Record[Multiple_Records][$Block->blockid]);
										if(count($Record[Multiple_Records][$Block->blockid])>0):
										foreach($Record[Multiple_Records][$Block->blockid] as $MPKey=>$Multiple_Record):?>
											<tr>
												<?php foreach($Block->DetailFields as $key=> $Field):?>
												<?php if($Block->blockid=="66" && $Field->columnname =='hours'){ ?>
												<td id="llb<?php echo $Field->fieldname.$MPKey.'_time'; ?>" class="lbl<?php echo $Field->columnname; ?>"><?php //echo count($Multiple_Record->TaxDetails);?>
												<?php }else{ ?>
												<td id="<?php echo $Field->fieldname.$MPKey.'_time'; ?>" class="<?php echo $Field->columnname; ?>">
													<?php //echo count($Multiple_Record->TaxDetails);?>
													<?php } ?>
													<?php if(($ModuleName=="SalesReturn" || $ModuleName=="Invoice") and $Field->columnname == 'tax_per' and count($Multiple_Record->TaxDetails)>0){
													echo '<label id="'.$attr_id.'" class="cursorPointer" onclick="dialogOpen(event)">Tax Details</label><br>';

													echo '
													<div id="tax_detail_'.$MPKey.'" class="dialog" style="display:none; background-color:#fff;">
														<header id="popupheader">
															<span class="popuptitle"><strong>Tax Detail</strong></span>
															<span id="closedialog" class="cursorPointer closepop popupclose_nocase pull-right"><b>&times;</b></span>
														</header>
														<div class="row popup_content">
															<div class="col-sm-4"><b>Tax Type</b></div>
															<div class="col-sm-4"><b>Tax(%)</b></div>
															<div class="col-sm-4"><b>Tax Value</b></div>
														</div>
														<body>';

													foreach($Multiple_Record->TaxDetails as $Tax):
													echo '<div class="row popup_content">';
													echo '<div class="col-sm-4">'.$Tax->tax_type." (".$Tax->tax_on.")".'</div>';
													echo '<div class="col-sm-4">'.$Tax->tax_per." %".'</div>';
													echo '<div class="col-sm-4">'.$Tax->tax_value.'</div>';
													echo '</div>';

													endforeach;

													echo '</body></div>';

													}
													?>

													<?php echo strip_tags($Multiple_Record->{$Field->columnname});?>
												</td>
												<?php endforeach;?>
											</tr>
										<?php endforeach; endif;?>
									<?php elseif($Block->blocktype=="Tax"):?>

									<?php foreach ($Block->DetailFields as $key=> $Field):?>
										<tr>
											<td class=" col-sm-11"><b><?php echo $Field->fieldlable;?></b></td>
											<td class=""><b><?php echo strip_tags($Record->{$Field->columnname})?></b></td>
										</tr>
									<?php endforeach;?>
									<?php endif;?>
								</tbody>
							</table><!-- table ends -->
						</div>
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
			</div><!-- table ends -->
		</div><!-- container ends -->
	</div><!-- main details page ends -->
</div><!-- fullpage ends -->
