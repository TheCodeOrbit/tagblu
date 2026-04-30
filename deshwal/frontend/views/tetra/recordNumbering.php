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
			<!-- Left side -->
			<?php include_once 'crmLeft1.php'?>
			<div class="col-sm-10" id="rightside-main">
				<div class="">	<!-- header part --->
					<div class="row bottom-seperator">
						<div class="col-sm-6">
							<h1 class="page-heading">Customize Record Numbering</h1>							
						</div>

						<div class="col-sm-6">
							
						</div> 
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div id="recordNumbering-container">
								<table class="table table-bordered table-hover">
									<thead class="table-header">
										<tr>
											<th style="padding-bottom: 15px;">Customize Record Numbering</th>
											<th></th>
											<th><button type="button" class="btn btn-default pull-right radius-zero"> Update Missing Record Sequence</button></th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td class="text-right">Select Module</td>
											<td>
												<select data-placeholder="Modules" class="chosen-select" style="width:220px;">
													<option>Opportunities</option>
													<option>Contacts</option>
													<option>Opportunities</option>
													<option>Opportunities</option>
												</select>
											</td>
											<td></td>
										</tr>
										<tr>
											<td class="text-right">Use Prefix</td>
											<td><input type="text" class="form-control inputwidth" placeholder=""/></td>
											<td></td>
										</tr>
										<tr>
											<td class="text-right">Start Sequence</td>
											<td><input type="text" class="form-control inputwidth" placeholder=""/></td>
											<td></td>
										</tr>
									</tbody>
								</table>
								<div class="pull-right">
									<button type="button" class="btn btn-success radius-zero">Save</button>
									<a href="#">Cancel</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

