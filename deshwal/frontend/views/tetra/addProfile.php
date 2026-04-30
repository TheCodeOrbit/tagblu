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

		
		<div class="container-fluid">
			<div class="row" style="background-color:#fafafb;" id="fullpage">
			<!-- Left side -->
			<?php include_once 'crmLeft1.php'?>
			<div class="col-sm-10" id="rightside-main">
				<div class="">	<!-- header part --->
					<div class="row bottom-seperator">
						<div class="col-sm-6">
							<h1 class="page-heading">Create Profile</h1>
							
						</div>
						<div class="col-sm-6">
							<div aria-label="..." role="group" class="pull-right">
								<button type="button" class="btn btn-success">Save</button>&nbsp;&nbsp;
								<a href="#">Cancel</a>
							</div>
						
						</div> 
					</div>
					<div class="row">
						<div class="col-sm-12">
							<form class="form form-horizontal" name="" action="" method="">
								<div class="form-group">
									<label for="ProfileName" class="col-sm-2">Profile Name :</label>
									<div class="col-sm-6">
										<input type="text" class="form-control" placeholder=""/>
									</div>
								</div>
								<div class="form-group">
									<label for="description" class="col-sm-2">Description</label>
									<div class="col-sm-8">
										<textarea class="form-control">
										
										</textarea>
									</div>
								</div>
							</form>
							
							<div class="row" id="addprofile-jumbotron">
								<div class="col-sm-2">
									<form class="form form-horizontal" name="" action="" method="">
										<div class="checkbox">
											<label><input type="checkbox"> View All</label>
										</div>
										<div class="checkbox">
											<label><input type="checkbox"> Edit All</label>
										</div>
									</form>
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
											<th> <span><input type="checkbox" name=""/> <strong> Modules</strong></span></th>
											<th> <span class="addProfile-header-check"><input type="checkbox" name=""/> <strong> View</strong></span></th>
											<th> <span class="addProfile-header-check"><input type="checkbox" name=""/> <strong> Create/Edit</strong></span></th>
											<th> <span class="addProfile-header-check"><input type="checkbox" name=""/> <strong> Delete</strong></span></th>
											<th> <span class="addProfile-header-check"><input type="checkbox" name=""/> <strong> Field and Tool Privileges</strong></span></th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td> <input type="checkbox" name=""/> Dashboard</td>
											<td> <input type="checkbox" class="addProfile-check" name=""/></td>
											<td> <input type="checkbox" class="addProfile-check" name=""/></td>
											<td> <input type="checkbox" class="addProfile-check" name=""/></td>
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
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation</label>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation</label>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation</label>
																</div>
															</td>
														</tr>
														<tr>
															<td>
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation</label>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation</label>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation</label>
																</div>
															</td>
														</tr>
														<tr>
															<td>
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation</label>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation</label>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation</label>
																</div>
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
																			<td><input type="checkbox" name=""/> Import</td>
																			<td><input type="checkbox" name=""/> Export</td>
																			<td><input type="checkbox" name=""/>  DuplicatesHandling</td>
																		</tr>
																	</tbody>
																</table>
															</td>
														</tr><!-- tools row ends -->
													</tbody>
												</table><!-- Range table ends -->
											</td>
										</tr>
										
										<tr>
											<td> <input type="checkbox" name=""/> Dashboard</td>
											<td> <input type="checkbox" class="addProfile-check" name=""/></td>
											<td> <input type="checkbox" class="addProfile-check" name=""/></td>
											<td> <input type="checkbox" class="addProfile-check" name=""/></td>
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
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation</label>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation</label>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation</label>
																</div>
															</td>
														</tr>
														<tr>
															<td>
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation</label>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation</label>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation</label>
																</div>
															</td>
														</tr>
														<tr>
															<td>
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation</label>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation</label>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation</label>
																</div>
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
																			<td><input type="checkbox" name=""/> Import</td>
																			<td><input type="checkbox" name=""/> Export</td>
																			<td><input type="checkbox" name=""/>  DuplicatesHandling</td>
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
								<div class="pull-right">
									<button type="button" class="btn btn-success">Save</button>&nbsp;&nbsp;
									<a href="#">Cancel</a>
								</div>
							</div>  <!-- AddProfile Container Ends -->
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script>
		
	
			
			$(document).ready(function(){
			
			// adjust height of left and rightside when row is toggle
			
				var numoftogglebtn = $('.glyphicon-chevron-down').length;
				
				$('.addProfile-toggle').click(function(){
					
					var mk = $('.addProfile-toggle').find('span').attr('class');
					if(mk == 'glyphicon glyphicon-chevron-down'){
						$('#rightside-main').css('height','auto');
						var leftmenuHeight = $('#rightside-main').outerHeight();
						
						$('#leftside-menu').css('height',leftmenuHeight + 178 * numoftogglebtn);
					}else{
						$('#leftside-menu,#rightside-main').css('min-height','100vh');
					}
					
				});
				
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
