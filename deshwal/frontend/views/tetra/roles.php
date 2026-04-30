<script src="../../css/bootstrap-3.3.6-dist/js/jquery-1.12.0.min.js"></script>
<script src="../../js/jquery-ui.min.js"></script>
<script src="../../js/cookies.js"></script>
<script src="../../css/bootstrap-3.3.6-dist/js/bootstrap.min.js"></script>
<!-- chosen -->
<script type="text/javascript" src="../../js/chosen.jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="../../css/chosen/chosen.css">
<!-- end -->
<link rel="stylesheet" id="mainstylesheet" type="text/css" href="../../css/normalize.css" />
<link rel="stylesheet" href="../../css/bootstrap-3.3.6-dist/css/bootstrap.min.css"/>
<link rel="stylesheet" href="../../css/main.css"/>

<script>
$(document).ready(function(){
	// to activate tooltip
    $('[data-toggle="tooltip"]').tooltip();
});
</script>


<div class="container-fluid">
	<div class="row" style="background-color:#fafafb;">

		<?php include_once 'crmLeft1.php';?> <!-- left side menus -->

		<div class="col-sm-10" id="rightside-detail"> <!-- crm right side page start -->
			<div class="bottom-seperator">
				<h1 class="page-heading"> Roles </h1>
			</div>

			<div class="listviewActionDiv row"> <!--roles tree structure start -->
				<ul class="listRoles">
					<li class="treeRoles">
						<div class="edit">
							<span class="glyphicon glyphicon-minus minus-sign"></span>
							<a class="btn btn-primary activeEditTrash"> Organization </a>
							<a href="addRoles.php"><span class="glyphicon glyphicon-plus-sign editbtn" title="Add Roles"></span></a>
						</div>

						<ul class="listRoles">
							<li class="treeRoles">
								<div class="edit">
									<span class="glyphicon glyphicon-minus minus-sign"></span>
									<a href="addRoles.php" class="btn btn-default activeEditTrash" data-toggle="tooltip" title="Click to edit"> CEO </a>
									<a href="addRoles.php"><span class="glyphicon glyphicon-plus-sign editbtn" title="Add Roles"></span></a>
									<a href="#"> <span class="glyphicon glyphicon-trash editbtn" title="Delete" data-toggle="modal" data-target="#addrolesDelete"></span> </a>
								</div>

								<ul class="listRoles">
									<li class="treeRoles">
										<div class="edit">
											<span class="glyphicon glyphicon-minus minus-sign"></span>
											<a href="addRoles.php" class="btn btn-default activeEditTrash" data-toggle="tooltip" title="Click to edit"> Vice President </a>
											<a href="addRoles.php"><span class="glyphicon glyphicon-plus-sign editbtn" title="Add Roles"></span></a>
											<a href="#"> <span class="glyphicon glyphicon-trash editbtn" title="Delete" data-toggle="modal" data-target="#addrolesDelete"></span> </a>
										</div>

										<ul class="listRoles">
											<li class="treeRoles">
												<div class="edit">
													<span class="glyphicon glyphicon-minus minus-sign"></span>
													<a href="addRoles.php" class="btn btn-default activeEditTrash" data-toggle="tooltip" title="Click to edit"> Sales Manager </a>
													<a href="addRoles.php"><span class="glyphicon glyphicon-plus-sign editbtn" title="Add Roles"></span></a>
													<a href="#"> <span class="glyphicon glyphicon-trash editbtn" title="Delete" data-toggle="modal" data-target="#addrolesDelete"></span> </a>
												</div>

												<ul class="listRoles">
													<li class="treeRoles">
														<div class="edit">
															<span class="glyphicon glyphicon-minus minus-sign"></span>
															<a href="addRoles.php" class="btn btn-default activeEditTrash" data-toggle="tooltip" title="Click to edit"> Sales Person </a>
															<a href="addRoles.php"><span class="glyphicon glyphicon-plus-sign editbtn" title="Add Roles"></span></a>
															<a href="#"> <span class="glyphicon glyphicon-trash editbtn" title="Delete" data-toggle="modal" data-target="#addrolesDelete"></span> </a>
														</div>
													</li>
												</ul>
											</li>
										</ul>
									</li>
								</ul>
							</li>
						</ul>
					</li>
				</ul>

				<!-- delete btn Modal -->
				<div id="addrolesDelete" class="modal fade">
					<div class="modal-dialog">
						<div class="modal-content" style="width:410px;">
							<div class="modal-header">
								<div class="row">
									<div class="col-sm-12">
										<button type="button" class="close" data-dismiss="modal">&times;</button>
										<h4> <strong> Delete Role - CEO </strong> </h4>
									</div>
								</div>
							</div>

							<!-- Modal content-->
							<div class="modal-body">
								<div class="row">
									<div class="col-sm-12">
										<h5> Transfer Ownership </h5>
									</div>

									<div class="col-sm-12">
										<form class="form form-horizontal" action="" method="">
											<div class="form-group">
												<label class="control-label col-sm-4"> <span class="star">*</span> To Other Role </label>
												<div class="col-sm-8">
													<div class="input-group inputwidth" id="main_note">
														<span class="transponame input-group-addon">
															<span class="glyphicon glyphicon-remove-circle cursorPointer text-info" type="button" title="Clear">
															</span>
														</span>
														<input type="text" value="" name="depotcode1" readonly="" class="form-control cursorreadonly">
														<span class="transearch input-group-addon cursorPointer" onclick="window.open('assignRole.php', 'newwindow', 'width=1000, height=800'); return false;">
															<span class="searchtrans glyphicon glyphicon-search cursorPointer text-info" type="button" title="Select"></span>
														</span>
													</div>
												</div>
											</div>
										</form>

										<div class="modal-footer">
											<button type="button" class="btn btn-success addgrpsave"> Save </button>
											<a href="#" class="addgrpcancel" data-dismiss="modal"> Cancel </a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div> <!-- Model End -->
			</div> <!--roles tree structure end -->
		</div> <!-- crm right side page end -->
	</div>
</div>