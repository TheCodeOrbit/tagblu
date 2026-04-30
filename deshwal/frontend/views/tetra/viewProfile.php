<script src="../../css/bootstrap-3.3.6-dist/js/jquery-1.12.0.min.js"></script>
<script src="../../js/jquery-ui.min.js"></script>
<script src="../../js/cookies.js"></script>
<script src="../../css/bootstrap-3.3.6-dist/js/bootstrap.min.js"></script>
<script src="../../js/chosen.jquery.min.js"></script>

	
<link rel="stylesheet" id="mainstylesheet" type="text/css" href="../../css/reset.css" />
<link rel="stylesheet" id="mainstylesheet" type="text/css" href="../../css/normalize.css" />
<link rel="stylesheet" href="../../css/bootstrap-3.3.6-dist/css/bootstrap.min.css"/>
<link rel="stylesheet" id="mainstylesheet" type="text/css" href="../../css/chosen/chosen.css" />
<link rel="stylesheet" href="../../css/main.css"/>

		<script>
			$(document).ready(function(){
				
				// Active chosen select button
			
					$(".chosen-select").chosen();	
										
				
			// toggle td icon
				$('.addProfile-toggle').click(function(){
					$(this).find('span').toggleClass('glyphicon glyphicon-chevron-down glyphicon glyphicon-chevron-up');
					$(this).closest('tr').next().toggle();
				});
				
			//	range color 
				$('.select-range').change(function(){
					if($(this).val() == 1){
						$(this).removeClass('select-range-greenthumb');
						$(this).removeClass('select-range-bluethumb');
						$(this).addClass('select-range-redthumb');
					}else if($(this).val() == 2){
						$(this).removeClass('select-range-redthumb');
						$(this).removeClass('select-range-bluethumb');
						$(this).addClass('select-range-greenthumb');
						
					}else{
						$(this).removeClass('select-range-redthumb');
						$(this).removeClass('select-range-greenthumb');
						$(this).addClass('select-range-bluethumb');
					
					};
				})
				
		
			});
		
			
		</script>
		
		<div class="container-fluid">
			<div class="row" style="background-color:#fafafb;" id="fullpage">
			<!-- Left side -->
			<?php include_once 'crmLeft1.php'?>
			<div class="col-sm-10" id="rightside-main">
				<div class="">	<!-- header part --->
					<div class="row bottom-seperator">
						<div class="col-sm-6">
							<h1 class="page-heading">Profile View</h1>
							
						</div>
						<div class="col-sm-6">
							<div aria-label="..." role="group" class="pull-right">
								<button type="button" onclick="location.href='addProfile.php'" class="btn btn-default topcontent-savebtn">Edit</button>
							</div>
						
						</div> 
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div class="row">
								<div class="col-sm-2">
									Profile Name : <br/><br/>
									Description :<br/>
									<br/>
								</div>
								<div class="col-sm-10">
									<strong>Guest Profile</strong> <br/><br/>
									<strong>Guest Profile for Test Users</strong><br/>
									<br/>
								</div>
							</div>
							
							<div class="row" id="addprofile-jumbotron">
								<div class="col-sm-2">
									<p class="glyphicon glyphicon-remove text-danger"></p> View All <br />
									<p class="glyphicon glyphicon-remove text-danger"></p> Edit All
								</div>
								<div class="col-sm-10">
									<ul class="addProfile-info">
										<li>
											<span class="circle"><span class="glyphicon glyphicon glyphicon-info-sign"></span>
												Can view all the module's information
											</span>
										</li>
										<li>
											<span class="circle"><span class="glyphicon glyphicon glyphicon-info-sign"></span>
												Can edit all the module's information
											</span>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div id="addprofile-container"> <!-- AddProfile Container Starts -->
								<p><strong>Edit privileges for this profile:</strong></p>
								<table class="table table-bordered table-hover"><!-- Table starts -->
									<thead class="tax-table-header">
										<tr>
											<th> <strong> Modules</strong></th>
											<th> <strong> View</strong></th>
											<th> <strong> Create/Edit</strong></th>
											<th> <strong> Delete</strong></th>
											<th> <strong> Field and Tool Privileges</strong></th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td> <span class="glyphicon glyphicon-ok text-success"> </span> Dashboard</td>
											<td class="text-center text-success"> <span class="glyphicon glyphicon-ok"></span> </td>
											<td class="text-center text-success"> <span class="glyphicon glyphicon-ok"></span> </td>
											<td class="text-center text-danger"> <span class="glyphicon glyphicon-remove"></span> </td>
											<td> <div class="addProfile-toggle cursorpointer"><span class="glyphicon glyphicon-chevron-down"></span></div></td>
										</tr>
										<tr class="range-containers" style="display:none;">
											<td class="" colspan="5">
												<div class="row">
													<div class="col-sm-2"><strong>Fields</strong></div>
													<div class="col-sm-10">
														<span id="range-info" class="pull-right">
															<span style="background-color:#ff7373;"></span> <label> Invisible</label>
															<span style="background-color:#68ff87;"></span> <label> Read Only</label>
															<span style="background-color:#6478ff;"></span> <label> Write</label>
														</span>
													</div>
												</div>
												<table class="table table-bordered table-hover table-responsive"><!-- Range table starts -->
													<tbody>
														<tr><!-- Range rows starts -->
															<td>
																<span class="invisible-indicator"></span> <span> Salutation</span>
															</td>
															<td>
																<span class="write-indicator"></span> <span>Salutation</span>
															</td>
															<td>
																<span class="write-indicator"></span> <span>Salutation</span>
															</td>
														</tr>
														<tr>
															<td>
																<span class="write-indicator"></span> <span>Salutation</span>
															</td>
															<td>
																<span class="readyonly-indicator"></span> <span>Salutation</span>
															</td>
															<td>
																<span class="write-indicator"></span> <span>Salutation</span>
															</td>
														</tr>
														<tr>
															<td>
																<span class="write-indicator"></span> <span>Salutation</span>
															</td>
															<td>
																<span class="write-indicator"></span> <span>Salutation</span>
															</td>
															<td>
																<span class="write-indicator"></span> <span>Salutation</span>
															</td>
														</tr>
														<!-- Range rows ends -->
														<tr><!-- tools row starts -->
															<td colspan="4">
																<div class="cow">
																	<div class="col-sm-12"><strong>Tools</strong></div>
																</div>
																<table class="table table-bordered table-hover table-responsive">
																	<tbody>
																		<tr>
																			<td><span class="glyphicon glyphicon-remove text-danger"></span> Import</td>
																			<td><span class="glyphicon glyphicon-remove text-danger"></span> Export</td>
																			<td><span class="glyphicon glyphicon-ok text-success"></span> DuplicatesHandling</td>
																		</tr>
																	</tbody>
																</table>
															</td>
														</tr><!-- tools row ends -->
													</tbody>
												</table><!-- Range table ends -->
											</td>
										</tr>
									</tbody>
								</table><!-- Table ends -->
							</div>  <!-- AddProfile Container Ends -->
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
