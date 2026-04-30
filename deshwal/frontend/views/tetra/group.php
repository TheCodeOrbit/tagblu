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

<script>
	$(document).ready(function(){
		// Chosen Click
		$(".chosen-select").chosen();

		// to display chosen select on modal
		$('#new-custom-block-modal, #new-custom-field-modal').on('shown.bs.modal', function () {
			$('.chosen-select', this).chosen('destroy').chosen();
		});

		// clickable row
		$("[data-target='#new-custom-block-modal']").click(function(){
			$('.clickable-row').removeAttr('onclick');
		});

		$('.clickable-row').click(function(){
			$(this).attr('onclick',"location.href='groupDetail.php'");
		});
	});
</script>

<div class="container-fluid">
	<div class="row" style="background-color:#fafafb;">

		<?php include_once 'crmLeft1.php';?> <!-- left side menus -->

		<div class="col-sm-10" id="rightside-detail"> <!-- crm right side page start -->
			<div class="bottom-seperator">
				<h1 class="page-heading"> Groups </h1>
			</div>

			<div class="listviewActionDiv row"> <!-- action, add, chosen links -->
				<div class="col-sm-4">
					<a href="addGroup.php" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span> Add Group </a>
				</div>

				<div class="col-sm-6">
					<div class="pull-right">
						<span>1 to 1</span>
						<span class="glyphicon glyphicon-refresh refreshbtn"></span>
					</div>
				</div>

				<div class="col-sm-2">
					<div aria-label="..." role="group" class="btn-group pull-left">
						<button class="btn btn-default" type="button">
							<span class="glyphicon glyphicon-chevron-left"></span>
						</button>

						<button data-toggle="dropdown" class="btn dropdown-toggle btn-default" type="button">
							<span class=" glyphicon glyphicon-forward"></span>
						</button>

						<div class="dropdown-menu">
							<span style="width:100px; margin-left:14px;">Page
								<span> <input type="text" style="width:50px;"> of 1 </span>
							</span>
						</div>

						<button class="btn btn-default" type="button">
							<span class="glyphicon glyphicon-chevron-right"></span>
						</button>
					</div>
				</div>
			</div><!-- action, add, chosen links end -->

			<div class="listviewActionDiv row"> <!-- group Table div -->
				<div class="col-sm-12">
					<table class="table table-hover" id="listingTable">
						<thead>
							<tr>
								<th> <strong> Group Name </strong> </th>
								<th> <strong> Description </strong> </th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<tr class="clickable-row">
								<td> <a href="groupDetail.php"> Team Selling </a> </td>
								<td> <a href="groupDetail.php"> Group Related to Sales </a> </td>
								<td class="actionIcons">
									<a href="addGroup.php"><span class="glyphicon glyphicon-pencil editbtn cursorpointer" title="Edit"></span></a>
									<span class="glyphicon glyphicon-trash editbtn" title="Delete" data-toggle="modal" data-target="#new-custom-block-modal"></span>
								</td>
							</tr>
							<tr class="clickable-row">
								<td> <a href="groupDetail.php"> Marketing Group </a> </td>
								<td> <a href="groupDetail.php"> Group Related to Marketing Activities </a> </td>
								<td class="actionIcons">
									<a href="addGroup.php"><span class="glyphicon glyphicon-pencil editbtn cursorpointer" title="Edit"></span></a>
									<span class="glyphicon glyphicon-trash editbtn" title="Delete" data-toggle="modal" data-target="#new-custom-block-modal"></span>
								</td>
							</tr>
							<tr class="clickable-row">
								<td> <a href="groupDetail.php"> Support Group </a> </td>
								<td> <a href="groupDetail.php"> Group Related to providing Support to Customers </a> </td>
								<td class="actionIcons">
									<a href="addGroup.php"><span class="glyphicon glyphicon-pencil editbtn cursorpointer" title="Edit"></span></a>
									<span class="glyphicon glyphicon-trash editbtn" title="Delete" data-toggle="modal" data-target="#new-custom-block-modal"></span>
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<!-- delete btn Modal -->
				<div class="modal fade" id="new-custom-block-modal">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<div class="row">
									<div class="col-sm-12">
										<button type="button" class="close" data-dismiss="modal">&times;</button>
										<h4> <strong> Delete Group - Marketing Group </strong> </h4>
									</div>
								</div>
							</div>

							<!-- Modal content-->
							<div class="modal-body">
								<div class="row">
									<div class="col-sm-12">
										<form class="form form-horizontal" action="" method="">
											<div class="form-group">
												<label class="control-label col-sm-4"> Transfer Ownership To <span class="star">*</span> </label>
												<div class="col-sm-8">
													<select class="form-control grouppicklist chosen-select">
														<optgroup label="Users">
															<option> Tetra Administrator </option>
														</optgroup>
														<optgroup label="Groups">
															<option> Team Selling </option>
															<option> Marketing Group </option>
														</optgroup>
													</select>
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
			</div> <!-- group Table div end -->
			
		</div> <!-- crm right side page end -->
	</div>
</div>