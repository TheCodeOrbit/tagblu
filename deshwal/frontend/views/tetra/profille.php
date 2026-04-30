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
				
				// Dropdown
			$('.dropdown-toggle').dropdown();
				
			// viewProfile on click of row
				
				$("[data-target='#deleteButton']").click(function(){
					$('.viewprofile').removeAttr('onclick');
				});
				
				$('.viewprofile').click(function(){
					$(this).attr('onclick',"location.href='viewProfile.php'");
				});
				$('.viewprofile').addClass('cursorpointer');
				
				
				// to display chosen select on modal	
				$('#deleteButton').on('shown.bs.modal', function () {
					$('.chosen-select', this).chosen('destroy').chosen();
				});
				// Active chosen select button
			
					$(".chosen-select").chosen();	
								
		// height of page according to window height		
			/*	var windowHeight = $(window).innerHeight();
				$('#fullpage').css('min-height', windowHeight);
				var leftside = $('#fullpage').height();
				$('#rightside-main').css('min-height', leftside);
				$('#leftsiede-summary').css('min-height', leftside);
				
				*/
			});
		
			
		</script>

	<div class="container-fluid">
		<div class="row" style="background-color:#fafafb;" id="fullpage">

			<!-- Left side -->
			<?php include_once 'crmLeft1.php'?>

			<div class="col-sm-10" id="rightside-main">

				<div class="bottom-seperator">
					<h1 class="page-heading"> Profiles </h1>
				</div>
				<div>
					
					<div class="row">
						<div class="col-sm-12">
							<div id="profile-container">
								<div class="row">
									<div class="col-sm-9">
										<div class="addNewProfile-btn">
											<button type="button" onclick="location.href='addProfile.php'" class="btn">
												<span class="glyphicon glyphicon-plus"></span>
												<strong>Add New Profile</strong>
											</button>
										</div>
									</div>
									<div class="col-sm-1"><span> 1  of 20</span> <span class=" glyphicon glyphicon-refresh cursorPointer"></span></div>
									<div class="col-sm-2">
										<div aria-label="..." role="group" class="btn-group">
											<button class="btn btn-default" type="button"> <span class="glyphicon glyphicon-chevron-left"></span></button>
											<button data-toggle="dropdown" class="btn dropdown-toggle btn-default" type="button"><span class=" glyphicon glyphicon-forward"></span></button>
											<div class="dropdown-menu">
												<span style="width:100px; margin-left:14px;">Page <span> <input type="text" style="width:50px;"> of 1</span> </span>
											</div>										  
											<button class="btn btn-default" type="button"><span class="glyphicon glyphicon-chevron-right"></span></button>
										</div>
									</div>
								</div>
								<table class="table table-bordered table-hover">
									<thead class="tax-table-header">
										<tr>
											<th><strong>Profile Name</strong></th>
											<th><strong>Description</strong></th>
											<th><strong></strong></th>
										</tr>
									</thead>
									<tbody>
										<tr class="viewprofile">
											<td>Administrator</td>
											<td>Admin Profile</td>
											<td class="actionIcons">
												<a href="addProfile.php"><span class="glyphicon glyphicon-pencil editbtn cursorpointer"></span></a>
												<a href="addProfile.php"><span class="glyphicon glyphicon-share editbtn cursorpointer"></span></a>
												<a href="#" data-toggle="modal" data-target="#deleteButton"><span class="glyphicon glyphicon-trash editbtn cursorpointer"></span></a>
											</td>
										</tr>
										<tr class="viewprofile">
											<td>Sales Profile</td>
											<td>Profile Related to Sales</td>
											<td class="actionIcons">
												<a href="addProfile.php"><span class="glyphicon glyphicon-pencil editbtn cursorpointer"></span></a>
												<a href="addProfile.php"><span class="glyphicon glyphicon-share editbtn cursorpointer"></span></a>
												<a href="#" data-toggle="modal" data-target="#deleteButton"><span class="glyphicon glyphicon-trash editbtn cursorpointer"></span></a>
											</td>
										</tr>
										<tr class="viewprofile">
											<td>Support Profile</td>
											<td>Profile Related to Support</td>
											<td class="actionIcons">
												<a href="addProfile.php"><span class="glyphicon glyphicon-pencil editbtn cursorpointer"></span></a>
												<a href="addProfile.php"><span class="glyphicon glyphicon-share editbtn cursorpointer"></span></a>
												<a href="#" data-toggle="modal" data-target="#deleteButton"><span class="glyphicon glyphicon-trash editbtn cursorpointer"></span></a>
											</td>
										</tr>
										<tr class="viewprofile">
											<td>Guest Profile</td>
											<td>Guest Profile for Test Users</td>
											<td class="actionIcons">
												<a href="addProfile.php"><span class="glyphicon glyphicon-pencil editbtn cursorpointer"></span></a>
												<a href="addProfile.php"><span class="glyphicon glyphicon-share editbtn cursorpointer"></span></a>
												<a href="#" data-toggle="modal" data-target="#deleteButton"><span class="glyphicon glyphicon-trash editbtn cursorpointer"></span></a>
											</td>
										</tr>
									</tbody>
								</table>
								<!-- modal for delete button -->
								<div class="modal fade" id="deleteButton">
									<div class="modal-dialog">
										<div class="modal-content">
											<form class="form form-horizontal" action="" name="">
												<div class="modal-header">
													<span class="page-heading">Delete Profile - Sales Profile</span>
													<button type="button" class="close" data-dismiss="modal">&times;</button>
												</div>
												<div class="modal-body">
													<div class="form-group">
														<label for="deleteItem" class="control-label col-sm-4">Transfer roles to profile</label>
														<div class="col-sm-8">
															<select class="chosen-select" style="width:220px;">
																<option>1</option>
																<option>2</option>
																<option>3</option>
																<option>4</option>
															</select>
														</div>
													</div>
													
												</div>
												<div class="modal-footer">
													<div class="pull-right">
														<button type="button" class="btn btn-success radius-zero">Delete </button>
														<a href="#" data-dismiss="modal"> Cancel</a>
													</div>
												</div>
											</form>
										</div>
									</div>
									<!-- modal ends -->
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

