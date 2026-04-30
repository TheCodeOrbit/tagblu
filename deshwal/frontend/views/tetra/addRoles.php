<script src="../../css/bootstrap-3.3.6-dist/js/jquery-1.12.0.min.js"></script>
<script src="../../js/jquery-ui.min.js"></script>
<script src="../../js/cookies.js"></script>
<script src="../../css/bootstrap-3.3.6-dist/js/bootstrap.min.js"></script>
<script src="../../js/chosen.jquery.min.js"></script>
<!-- chosen -->
<script type="text/javascript" src="../../js/chosen.jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="../../css/chosen/chosen.css">
<!-- end -->
<link rel="stylesheet" id="mainstylesheet" type="text/css" href="../../css/reset.css" />
<link rel="stylesheet" id="mainstylesheet" type="text/css" href="../../css/normalize.css" />
<link rel="stylesheet" href="../../css/bootstrap-3.3.6-dist/css/bootstrap.min.css"/>
<link rel="stylesheet" id="mainstylesheet" type="text/css" href="../../css/chosen/chosen.css" />



<div class="container-fluid">
	<div class="row" style="background-color:#fafafb;">

		<?php include_once 'crmLeft1.php';?> <!-- left side menus -->

		<div class="col-sm-10" id="rightside-detail"> <!-- crm right side page start -->
			<div class="bottom-seperator">
				<h1 class="page-heading"> Roles </h1>
			</div>

			<div class="listviewActionDiv row">
				<div class="col-sm-12">
					<label class="col-sm-3 control-label"> Name  : <span class="star">*</span> </label>
					<div class="col-sm-8"> <input class="form-control inputboxlable"> </div>
					<div class="col-sm-1"></div>
				</div>

				<div class="col-sm-12">
					<label class="col-sm-3 control-label"> Reports To  : </label>
					<div class="col-sm-3"> <input type="text" value="CEO" readonly="" class="form-control inputboxlable cursorreadonly"> </div>
					<div class="col-sm-6"></div>
				</div>

				<div class="col-sm-12 editrolesmargin">
					<label class="col-sm-3 control-label"> Can Assign Records To  : </label>
					<div class="col-sm-2">
						<input type="radio" name="roles"> All Users
					</div>
					<div class="col-sm-4">
						<input type="radio" name="roles"> Users having Same Role or Subordinate Role
					</div>
					<div class="col-sm-3">
						<input type="radio" name="roles"> Users having Subordinate Role
					</div>
				</div>

				<div class="col-sm-12">
					<label class="col-sm-3 control-label"> Privileges  : </label>
					<div class="col-sm-3">
						<input type="radio" name="privileges"> Assign privileges directly to Role
					</div>
					<div class="col-sm-6">
						<input type="radio" name="privileges"> Assign priviliges from existing profiles
					</div>
				</div>

				<div class="col-sm-12"> <!-- roles table container start -->
					<div id="addroles-content">
						<div class="col-sm-12">
							<div id="addroles-header">
								<div class="col-sm-3">
									<h4> Copy privileges from </h4>
								</div>
								<div class="col-sm-9">
									<div class="chzn-container chzn-container-single pull-left">
										<select style="width: 220px;" class="chosen-select" data-placeholder="Choose Profiles">
											<option value=""></option>
											<option> Administrator </option>
											<option> Sales Profile </option>
											<option> Support Profile </option>
											<option> Guest Profile </option>
										</select>
									</div>
								</div>
							</div>
						</div>

						<div class="col-sm-12">
							<div id="addroles-jumbotron">
								<div class="col-sm-2">
									<form class="form form-horizontal" name="" action="" method="">
										<div class="checkbox">
											<label><input type="checkbox"> View All </label>
										</div>

										<div class="checkbox">
											<label><input type="checkbox"> Edit All </label>
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

						<div class="col-sm-12">
							<div id="addprofile-container"> <!-- AddProfile Container Starts -->
								<table class="table table-bordered table-hover"><!-- Table starts -->
									<thead class="tax-table-header">
										<tr>
											<th> <span><input type="checkbox" name=""/> <strong> &nbsp; Modules </strong></span></th>
											<th class="text-center"> <span><input type="checkbox" name=""/> <strong> &nbsp; View </strong></span></th>
											<th class="text-center"> <span><input type="checkbox" name=""/> <strong> &nbsp; Create/Edit </strong></span></th>
											<th class="text-center"> <span><input type="checkbox" name=""/> <strong> &nbsp; Delete </strong></span></th>
											<th class="text-center"> <span><input type="checkbox" name=""/> <strong> &nbsp; Field and Tool Privileges </strong></span></th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td> <input type="checkbox" name=""/> &nbsp; Dashboard </td>
											<td> <input type="checkbox" class="addProfile-check" name=""/></td>
											<td> <input type="checkbox" class="addProfile-check" name=""/></td>
											<td> <input type="checkbox" class="addProfile-check" name=""/></td>
											<td> <div class="addProfile-toggle cursorpointer"><span class="glyphicon glyphicon-chevron-down"></span></div></td>
										</tr>
										<tr class="range-containers" style="display:none;">
											<td class="" colspan="5">
												<table class="table table-bordered table-responsive"><!-- Range table starts -->
													<tbody>
														<tr>
															<td colspan="4">
																<div class="row">
																	<div class="col-sm-2"><strong> Fields </strong></div>
																	<div class="col-sm-10">
																		<span id="range-info" class="pull-right">
																			<span style="background-color:#ff7373;"></span> <label> Invisible </label>
																			<span style="background-color:#68ff87;"></span> <label> Read Only </label>
																			<span style="background-color:#6478ff;"></span> <label> Write </label>
																		</span>
																	</div>
																</div>
															</td>
														</tr>
														<tr><!-- Range rows starts -->
															<td>
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation </label>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation </label>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation </label>
																</div>
															</td>
														</tr>
														<tr>
															<td>
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation </label>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation </label>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation </label>
																</div>
															</td>
														</tr>
														<tr>
															<td>
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation </label>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation </label>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation </label>
																</div>
															</td>
														</tr>
														<!-- Range rows ends -->
													</tbody>
												</table><!-- Range table ends -->

												<table class="table table-bordered table-responsive">
													<tbody>
														<tr><!-- tools row starts -->
															<td colspan="4">
																<div class="row">
																	<div class="col-sm-12"><strong> Tools </strong></div>
																</div>
																<table class="table table-bordered table-responsive">
																	<tbody>
																		<tr>
																			<td><input type="checkbox" name=""/> Import </td>
																			<td><input type="checkbox" name=""/> Export </td>
																			<td><input type="checkbox" name=""/>  DuplicatesHandling </td>
																		</tr>
																	</tbody>
																</table>
															</td>
														</tr><!-- tools row ends -->
													</tbody>
												</table>
											</td>
										</tr>
										<tr>
											<td> <input type="checkbox" name=""/> &nbsp; Dashboard </td>
											<td> <input type="checkbox" class="addProfile-check" name=""/></td>
											<td> <input type="checkbox" class="addProfile-check" name=""/></td>
											<td> <input type="checkbox" class="addProfile-check" name=""/></td>
											<td> <div class="addProfile-toggle cursorpointer"><span class="glyphicon glyphicon-chevron-down"></span></div></td>
										</tr>
										<tr class="range-containers" style="display:none;">
											<td class="" colspan="5">
												<table class="table table-bordered table-responsive"><!-- Range table starts -->
													<tbody>
														<tr>
															<td colspan="4">
																<div class="row">
																	<div class="col-sm-2"><strong> Fields </strong></div>
																	<div class="col-sm-10">
																		<span id="range-info" class="pull-right">
																			<span style="background-color:#ff7373;"></span> <label> Invisible </label>
																			<span style="background-color:#68ff87;"></span> <label> Read Only </label>
																			<span style="background-color:#6478ff;"></span> <label> Write </label>
																		</span>
																	</div>
																</div>
															</td>
														</tr>
														<tr><!-- Range rows starts -->
															<td>
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation </label>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation </label>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation </label>
																</div>
															</td>
														</tr>
														<tr>
															<td>
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation </label>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation </label>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation </label>
																</div>
															</td>
														</tr>
														<tr>
															<td>
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation </label>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation </label>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-3">
																		<input type="range" class="select-range" min="1" max="3"/>
																	</div>
																	<label class="col-sm-3"> Salutation </label>
																</div>
															</td>
														</tr>
														<!-- Range rows ends -->
													</tbody>
												</table><!-- Range table ends -->

												<table class="table table-bordered table-responsive">
													<tbody>
														<tr><!-- tools row starts -->
															<td colspan="4">
																<div class="row">
																	<div class="col-sm-12"><strong> Tools </strong></div>
																</div>
																<table class="table table-bordered table-responsive">
																	<tbody>
																		<tr>
																			<td><input type="checkbox" name=""/> Import </td>
																			<td><input type="checkbox" name=""/> Export </td>
																			<td><input type="checkbox" name=""/>  DuplicatesHandling </td>
																		</tr>
																	</tbody>
																</table>
															</td>
														</tr><!-- tools row ends -->
													</tbody>
												</table>
											</td>
										</tr>
										<tr>
											<td> <input type="checkbox" name=""/> &nbsp; Dashboard </td>
											<td> <input type="checkbox" class="addProfile-check" name=""/></td>
											<td> <input type="checkbox" class="addProfile-check" name=""/></td>
											<td> <input type="checkbox" class="addProfile-check" name=""/></td>
											<td> </td>
										</tr>
									</tbody>
								</table><!-- Table ends -->
							</div>  <!-- AddProfile Container Ends -->
						</div>

						<div class="col-sm-12">
							<div class="text-center">
								<button type="submit" class="btn btn-success addgrpsave"><strong>Save</strong></button>
								<a class="addgrpcancel" href="#">Cancel</a>
							</div>
						</div>
					</div>
				</div> <!-- roles table container end -->
			</div>
		</div> <!-- crm right side page end -->
	</div>
</div>

<script>

	$(document).ready(function(){
		
		// adjust height of left and rightside when row is toggle
			
				var numoftogglebtn = $('.glyphicon-chevron-down').length;
				
				$('.addProfile-toggle').click(function(){
					
					var mk = $('.addProfile-toggle').find('span').attr('class');
					if(mk == 'glyphicon glyphicon-chevron-down'){
						
						$('#rightside-detail').css('height','auto');
						var leftmenuHeight = $('#rightside-detail').outerHeight();
						
						$('#leftside-menu').css('height',leftmenuHeight + 285 * numoftogglebtn);
					}else{
						$('#leftside-menu,#rightside-detail').css('min-height','100vh');
					}
					
				});

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
				}
				else if($(this).val() == 2){
					$(this).removeClass('select-range-redthumb');
					$(this).removeClass('select-range-bluethumb');
					$(this).addClass('select-range-greenthumb');
					}
					else{
						$(this).removeClass('select-range-redthumb');
						$(this).removeClass('select-range-greenthumb');
						$(this).addClass('select-range-bluethumb');
					};
				})

		// Chosen Click
			$(".chosen-select").chosen();
		});

</script>
