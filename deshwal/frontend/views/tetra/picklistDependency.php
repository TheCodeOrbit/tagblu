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
		
			});
		</script>


	<div class="container-fluid">
		<div class="row" style="background-color:#fafafb;" id="fullpage">
			<?php include_once 'crmLeft1.php'?>
			<div class="col-sm-10" id="rightside-main">
				<div class="">	<!-- header part --->
					<div class="row bottom-seperator">
						<div class="col-sm-6">
							<h1 class="page-heading">Picklist Dependency</h1>
						</div>
						
						<div class="col-sm-6">
							
						</div> 
					</div>
				</div>
				<div class="row" style="padding-right:10px;">
					<div class="col-sm-6">
						<button type="button" onclick="location.href='addpicklistDependency.php'" class="btn addCustombtn"><span class="glyphicon glyphicon-plus"></span> Add Picklist Dependency </button>
					</div>
					<div class="col-sm-6">
						<div class="pull-right">
							<select data-placeholder="Modules" class="chosen-select" style="width:220px;">
								<option value=""></option>
								<option>Customer</option>
								<option>Contact</option>
								<option>Depot</option>
								<option>Bank</option>
								<option>Product</option>
								<option>Recipt</option>
								<option>Transporter</option>
								<option>Supplier</option>
								<option>Order</option>
								<option>Invoice</option>
							</select>
						</div>
					</div>
				</div>
				<div id="picklistTable-container">
					<table id="picklistDependency-table" class="table table-bordered">
						<thead>
							<tr>
								<th>Module</th>
								<th>Source Field</th>
								<th>Target Field</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>Organization</td>
								<td>Industry</td>
								<td>
									Type
									
								</td>
								<td class="actionIcons">
									<a href="editpicklistDependency.php"><span class="glyphicon glyphicon-pencil editbtn cursorpointer"></span></a>
									<a data-target="#deleteButton-modal" data-toggle="modal" href="#"><span class="glyphicon glyphicon-trash editbtn cursorpointer"></span></a>
								</td>
							</tr>
						</tbody>
					</table>
					<!--  modal for deleteButton -->
					<div class="modal fade" id="deleteButton-modal">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<strong>Are you sure you want to delete this picklist dependency?</strong>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-danger">Yes</button>
									<button type="button" class="btn btn-info" data-dismiss="modal">No</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

