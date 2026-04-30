<?php $ModuleLabel=$ActionList['ModuleLabel'];?>
<div class="row" id="fullpage"><!-- fullpage starts -->
	<!-- left side links -->
	<?php include_once 'LeftSide.php' ?>
	<!-- main summary page -->
	<div class="col-sm-10 maincontent-summary rightside-page" id="rightside-summary-main"> <!-- right side main starts -->
		<div class="topcontent-summary"><!-- header part starts -->
			<span class="glyphicon glyphicon-chevron-left toggleButton" id="collapsebtn" data-toggle="collapse" data-target="#leftsiede-summary"></span>
			<div class="row">
				<div class="col-sm-8">
					<?php
						if($_REQUEST['SourceModule']!="")
						{
							$RelatedSourceModule=$_REQUEST['SourceModule'];
							$RelatedSourceRecord=$_REQUEST['SourceRecord'];
							$DetailUrl="{$ActionUrl}Detail/Record/{$Record->recordId}/SourceModule/{$RelatedSourceModule}/SourceRecord/{$RelatedSourceRecord}";
						}
						else
						$DetailUrl="{$ActionUrl}Detail/Record/{$Record->recordId}";
					?>
					<h4 class="h4 page-heading no-gutter"><?php echo $ModuleLabel;?> Summary</h4>
				</div>
				<!--<div class="col-sm-4">
					<button class="btn">Edit</button>
					<button class="btn">Send Email</button>
					<button class="btn"> <span class="glyphicon glyphicon-wrench"></span> &nbsp;Settings</button>
				</div>
				<div class="col-sm-4">
					<div class="btn-group" role="group" aria-label="...">
						<button type="button" class="btn"><</button>
						<button type="button" class="btn">></button>
					</div>
				</div>-->
			</div>
		</div><!-- header part ends -->
		<!-- body parts of main details page -->
		<div class="container-fluid"><!-- container starts -->
			<div class="row recordDetails-container"><!-- recordDetails container starts -->
				<div class="col-sm-6">
					<div class="recordDetails"><!-- recordDetails starts -->
						<table class="table table-bordered">
							<tbody>
								<?php 
								//echo "<pre>";
								//Print_r($ColumnList);
								//print_r($Record);
								//die;
								foreach ($ColumnList as $key=> $Field): ?>
								<tr>
									<td class="record-label"><?php echo $Field->fieldlable; ?></td>
									<td class="record-value">
										<?php 
											if($Field->uitype=="6" ){ 
												if($Record->{$Field->columnname} == '0'){
												echo $Record->{$Field->columnname} = 'No';
												}else{
												echo $Record->{$Field->columnname} = 'Yes';
												}
											}else if($Field->uitype=="16"){
												echo date('d/m/Y H:i:s',strtotime($Record->{$Field->columnname}));
											}else if($Field->uitype=="13" ){	
												$dt=date('Y-m-d',strtotime($Record->{$Field->columnname}));
												if($dt == '' or $dt =='1970-01-01' or $dt=='-0001-11-30'){
													echo $Field->columnname =" ";	
												}else{
													echo $Field->columnname = date('d/m/Y',strtotime($Record->{$Field->columnname}));
												}
											}else if($Field->uitype=="17" ){
												$dt=date('Y-m-d',strtotime($Record->{$Field->columnname}));
												if($dt == '' or $dt =='1970-01-01' or $dt=='-0001-11-30'){
													echo $Field->columnname ="";
												}else{		
													echo $Field->columnname = date('d/m/Y',strtotime($Record->{$Field->columnname}));
												}
											}else if($Field->uitype=="19" ){
												$dt=date('Y-m-d',strtotime($Record->{$Field->columnname}));
												if($dt == '' or $dt =='1970-01-01' or $dt=='-0001-11-30'){
													echo $Field->columnname ="";
												}else{		
													echo $Field->columnname = date('d/m/Y',strtotime($Record->{$Field->columnname}));
												}
											}else if($Field->uitype=="15" ){
												$dt=date('Y-m',strtotime($Record->{$Field->columnname}));
												if($dt == '' or $dt =='1970-01-01' or $dt =='-0001-11-30'){
													echo $Field->columnname =" ";	
												}else{
													echo $Field->columnname = date('m/Y',strtotime($Record->{$Field->columnname}));
												}
											}else{
												echo strip_tags($Record->{$Field->columnname});
											}
										?>
									</td>
								</tr>
								<?php endforeach;?>
							</tbody>
						</table>
						<hr>
						<input type="button" onclick="window.location ='<?php echo $DetailUrl;?>'" class="btn" value="Show Full Details"/>
					</div><!-- recordDetails ends -->
				</div>
				
				<div class="col-sm-4">
					<!--<div class="enquiry-widgetContainer">
						<div class="enquiry-widget">
							<header>
								Followup 
								<span class="pull-right"><a class="btn">Add</a></span>
							</header>
							<footer>
								about followup
							</footer>
						</div> 
					</div>-->
				</div>
				<!-- right side links -->
				<?php include_once 'RightSide.php' ?>
			</div><!-- recordDetails container ends -->
		</div><!-- container ends -->
	</div><!-- right side main ends -->
</div><!-- fullpage ends -->