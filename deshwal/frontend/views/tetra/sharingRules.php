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
					
			// to display chosen select on modal	
				$('#customRules').on('shown.bs.modal', function () {
					$('.chosen-select', this).chosen('destroy').chosen();
				});	
			
			// sharing-toggle 
			
				$('.sharing-toggle').click(function(){
					$(this).find('span').toggleClass('glyphicon glyphicon-chevron-down glyphicon glyphicon-chevron-up');
					$(this).closest('tr').next().toggle();
				});
			
				
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
							<h1 class="page-heading">Sharing Rules</h1>
							
						</div>
						<div class="col-sm-6">
							<button type="button" class="btn btn-success pull-right">Apply New Sharing Rules</button> 
						
						</div> 
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div id="profile-container">
								<table class="table table-bordered table-hover">
									<thead class="tax-table-header">
										<tr>
											<th><strong>Module</strong></th>
											<th><strong>Public: Read Only</strong></th>
											<th><strong>Public: Read,Create/Edit</strong></th>
											<th><strong>Public: Read, Create/Edit, Delete</strong></th>
											<th><strong>Private</strong></th>
											<th><strong>Advanced Sharing Rules</strong></th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>Administrator</td>
											<td><input type="radio" class="sharing-option" name="administrator"/></td>
											<td><input type="radio" class="sharing-option" name="administrator"/></td>
											<td><input type="radio" class="sharing-option" name="administrator"/></td>
											<td><input type="radio" class="sharing-option" name="administrator"/></td>
											<td><div class="sharing-toggle cursorpointer"><span class="glyphicon glyphicon-chevron-down"></span></div></td>
										</tr>
										<tr style="display:none;">
											<td colspan="6">
												<div class="row">
													<div class="col-sm-12">
														<div class="row titleRow">
															<div class="col-sm-6"><strong>Sharing Rules for Opportunities :</strong></div>
															<div class="col-sm-6"><button type="button" class="btn btn-default pull-right" data-toggle="modal" data-target="#customRules">Add Custom Rules</button></div>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-sm-12">
														<div class="text-center" style="border-top:2px solid #ddd; padding:10px;">No Custom Access Rules defined</div>
													</div>
												</div>
											</td>
										</tr>
										<tr>
											<td>Sales Profile</td>
											<td><input type="radio" class="sharing-option" name="sales"/></td>
											<td><input type="radio" class="sharing-option" name="sales"/></td>
											<td><input type="radio" class="sharing-option" name="sales"/></td>
											<td><input type="radio" class="sharing-option" name="sales"/></td>
											<td><div class="sharing-toggle cursorpointer"><span class="glyphicon glyphicon-chevron-down"></span></div></td>
										</tr>
										<tr style="display:none;">
											<td colspan="6">
												<div class="row">
													<div class="col-sm-12">
														<div class="row titleRow">
															<div class="col-sm-6">Sharing Rules for Opportunities :</div>
															<div class="col-sm-6"><button type="button" class="btn btn-default pull-right" data-toggle="modal" data-target="#customRules">Add Custom Rules</button></div>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-sm-12">
														<div class="text-center" style="border-top:2px solid #ddd; padding:10px;">No Custom Access Rules defined</div>
													</div>
												</div>
											</td>
										</tr>
										<tr>
											<td>Support Profile</td>
											<td><input type="radio" class="sharing-option" name="support"/></td>
											<td><input type="radio" class="sharing-option" name="support"/></td>
											<td><input type="radio" class="sharing-option" name="support"/></td>
											<td><input type="radio" class="sharing-option" name="support"/></td>
											<td><div class="sharing-toggle cursorpointer"><span class="glyphicon glyphicon-chevron-down"></span></div></td>
										</tr>
										<tr style="display:none;">
											<td colspan="6">
												<div class="row">
													<div class="col-sm-12">
														<div class="row titleRow">
															<div class="col-sm-6">Sharing Rules for Opportunities :</div>
															<div class="col-sm-6"><button type="button" class="btn btn-default pull-right" data-toggle="modal" data-target="#customRules">Add Custom Rules</button></div>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-sm-12">
														<div class="text-center" style="border-top:2px solid #ddd; padding:10px;">No Custom Access Rules defined</div>
													</div>
												</div>
											</td>
										</tr>
										<tr>
											<td>Guest Profile</td>
											<td><input type="radio" class="sharing-option" name="guest"/></td>
											<td><input type="radio" class="sharing-option" name="guest"/></td>
											<td><input type="radio" class="sharing-option" name="guest"/></td>
											<td><input type="radio" class="sharing-option" name="guest"/></td>
											<td><div class="sharing-toggle cursorpointer"><span class="glyphicon glyphicon-chevron-down"></span></div></td>
										</tr>
										<tr style="display:none;">
											<td colspan="6">
												<div class="row">
													<div class="col-sm-12">
														<div class="row titleRow">
															<div class="col-sm-6">Sharing Rules for Opportunities :</div>
															<div class="col-sm-6"><button type="button" class="btn btn-default pull-right" data-toggle="modal" data-target="#customRules">Add Custom Rules</button></div>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-sm-12">
														<div class="text-center" style="border-top:2px solid #ddd; padding:10px;">No Custom Access Rules defined</div>
													</div>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
								<div id="customRules" class="modal fade">
									<div class="modal-dialog"><!-- modal starts -->
										<div class="modal-content">
											<div class="modal-header"><!-- modal header -->
												<div class="row">
													<div class="col-sm-10"><h4>Add Custom Rule to Opportunities</h4></div>
													<div class="col-sm-2"><a href="#" class="close" data-dismiss="modal">&times;</a></div>
												</div>
											</div><!-- modal header ends -->
											<div class="modal-body">
												<form class="form form-horizontal" action="" method=""><!-- form starts -->
													<div class="form-group">
														<label for="Opportunitiesof" class="control-label col-sm-4">Opportunities of</label>
														<div class="col-sm-8">
															<select class="form-control chosen-select">
																<optgroup label="Groups"></optgroup>
																<option>Team Selling</option>
																<option>Marketing Group</option>
																<option>Support Group</option>
																<optgroup label="Roles"></optgroup>
																<option>Marketing Group</option>
																<option>Support Group</option>
															</select>
														</div>
													</div>
													<div class="form-group">
														<label for="accessedby" class="control-label col-sm-4">Can be accessed by</label>
														<div class="col-sm-8">
															<select class="form-control chosen-select">
																<option></option>
																<option>Team Selling</option>
																<option>Marketing Group</option>
																<option>Support Group</option>
																<option>Team Selling</option>
																<option>Marketing Group</option>
																<option>Support Group</option>
															</select>
														</div>
													</div>
													<div class="form-group">
														<label for="permission" class="control-label col-sm-4">With Permissions</label>
														<div class="col-sm-8">
															<input type="radio" name="permission"/> Read<br />
															<input type="radio" name="permission"/> Read and Write
														</div>
													</div>
													<footer class="pull-right" style="padding:10px;">
														<button type="button" class="btn btn-success">Save</button>
														<a href="#" data-dismiss="modal">Cancel</a>
													</footer>
												</form><!-- form ends -->
												<div class="modal-footer"></div>
											</div>
										</div>
									</div><!-- modal ends -->
								</div>
								<button type="button" class="btn btn-success pull-right">Apply New Sharing Rules</button> 
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

