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
				
				$('#moreinfo').click(function(){
					$('#toggleinfo').toggle('slide');
				});
				
				// Active chosen select button
			
					$(".chosen-select").chosen();	
				
				// ok icon when cliked on td
				
				$('.celltoselect span').css({'display':'none','margin-right':'10px'});
				$('.celltoselect').click(function(){
					$(this).toggleClass('cellselected');
					$('span',this).toggle();
				});
				
			});
		</script>
	

	<div class="container-fluid">
		<div class="row" style="background-color:#fafafb;" id="fullpage">
			<?php include_once 'crmLeft1.php'?>
			<div class="col-sm-10" id="rightside-main">
				<div class="">	<!-- header part --->
					<div class="row bottom-seperator">
						<div class="col-sm-12">
							<h1 class="page-heading">Picklist Dependency</h1>
						</div>
					</div>
				</div>
				<form class="form form-horizontal" name="">
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label for="module" class="control-label col-sm-3">Module</label>
								<div class="col-sm-9">
									<select class="chosen-select form-control" style="width:280px;" disabled>
										<option>Organization</option>
										<option>To Do</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="sourceField" class="control-label col-sm-3">Source Field</label>
								<div class="col-sm-9">
									<select class="chosen-select form-control" style="width:280px;" disabled>
										<option>Rating</option>
										<option>Industry</option>
									</select>
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="targetField" class="control-label col-sm-3">Target Field</label>
								<div class="col-sm-9">
									<select class="chosen-select form-control" style="width:280px;" disabled>
										<option>Rating</option>
										<option>Industry</option>
									</select>
								</div>
							</div>
						</div>
					</div>
				</form>
				<div class="row">
					<div class="col-sm-12">
						<span class="circle"><span class="glyphicon glyphicon-info-sign"> </span>
							Click on the respective cell to change the mapping for picklist values of target field  <span id="moreinfo" class="cursorpointer">More..</span>
						</span>	
						<ul id="toggleinfo" style="display:none; padding:0px;">
							<li><br />
								<span class="circle"><span class="glyphicon glyphicon-info-sign"> </span></span>
								Only mapped picklist values of the Source field will be shown below (except for first time)
							</li><br />
							<li>
								<span class="circle"><span class="glyphicon glyphicon-info-sign"> </span></span>
								If you want to see or change the mapping for the other picklist values of Source field,
								then you can select the values by clicking on 'Select Source values' button on the right side
							</li><br />
							<li>
								<span class="circle"><span class="glyphicon glyphicon-info-sign"> </span></span>
								Selected values of the Target field values, are highlighted as Selected Values
							</li>
						</ul>
					
						<div style="margin:20px 0px 20px 0px;">
							<button type="button" class="btn addCustombtn" data-toggle="modal" data-target="#selectsource"><strong>Select Soure Values</strong></button>
						</div>
						<table class="table table-bordered table-responsive table-hover">
							<thead>
								<tr>
									<th class="text-center"><strong>Industry</strong></th>
									<th class="text-center"><strong>Banking</strong></th>
									<th class="text-center"><strong>Biotechnology</strong></th>
									<th class="text-center"><strong>Chemicals</strong></th>
									<th class="text-center"><strong>Communications</strong></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="borderRemoved"></td>
									<td class="text-center celltoselect"><span class="glyphicon glyphicon-ok"></span> Analyst</td>
									<td class="text-center celltoselect"><span class="glyphicon glyphicon-ok"></span>Analyst</td>
									<td class="text-center celltoselect"><span class="glyphicon glyphicon-ok"></span>Analyst</td>
									<td class="text-center celltoselect"><span class="glyphicon glyphicon-ok"></span>Analyst</td>
								</tr>
								<tr>
									<td class="borderRemoved"></td>
									<td class="text-center celltoselect"><span class="glyphicon glyphicon-ok"></span> Analyst</td>
									<td class="text-center celltoselect"><span class="glyphicon glyphicon-ok"></span>Analyst</td>
									<td class="text-center celltoselect"><span class="glyphicon glyphicon-ok"></span>Analyst</td>
									<td class="text-center celltoselect"><span class="glyphicon glyphicon-ok"></span>Analyst</td>
								</tr>
								<tr>
									<td class="borderRemoved text-center"><strong>Type</strong></td>
									<td class="text-center celltoselect"><span class="glyphicon glyphicon-ok"></span> Analyst</td>
									<td class="text-center celltoselect"><span class="glyphicon glyphicon-ok"></span>Analyst</td>
									<td class="text-center celltoselect"><span class="glyphicon glyphicon-ok"></span>Analyst</td>
									<td class="text-center celltoselect"><span class="glyphicon glyphicon-ok"></span>Analyst</td>
								</tr>
								<tr>
									<td class="borderRemoved"></td>
									<td class="text-center celltoselect"><span class="glyphicon glyphicon-ok"></span> Analyst</td>
									<td class="text-center celltoselect"><span class="glyphicon glyphicon-ok"></span>Analyst</td>
									<td class="text-center celltoselect"><span class="glyphicon glyphicon-ok"></span>Analyst</td>
									<td class="text-center celltoselect"><span class="glyphicon glyphicon-ok"></span>Analyst</td>
								</tr>
								<tr>
									<td class="borderRemoved"></td>
									<td class="text-center celltoselect"><span class="glyphicon glyphicon-ok"></span> Analyst</td>
									<td class="text-center celltoselect"><span class="glyphicon glyphicon-ok"></span>Analyst</td>
									<td class="text-center celltoselect"><span class="glyphicon glyphicon-ok"></span>Analyst</td>
									<td class="text-center celltoselect"><span class="glyphicon glyphicon-ok"></span>Analyst</td>
								</tr>
								<tr>
									<td class="borderRemoved"></td>
									<td class="text-center celltoselect"><span class="glyphicon glyphicon-ok"></span> Analyst</td>
									<td class="text-center celltoselect"><span class="glyphicon glyphicon-ok"></span>Analyst</td>
									<td class="text-center celltoselect"><span class="glyphicon glyphicon-ok"></span>Analyst</td>
									<td class="text-center celltoselect"><span class="glyphicon glyphicon-ok"></span>Analyst</td>
								</tr>
								<tr>
									<td class="borderRemoved"></td>
									<td class="text-center celltoselect"><span class="glyphicon glyphicon-ok"></span> Analyst</td>
									<td class="text-center celltoselect"><span class="glyphicon glyphicon-ok"></span>Analyst</td>
									<td class="text-center celltoselect"><span class="glyphicon glyphicon-ok"></span>Analyst</td>
									<td class="text-center celltoselect"><span class="glyphicon glyphicon-ok"></span>Analyst</td>
								</tr>
								<tr>
									<td class="borderRemoved"></td>
									<td class="text-center celltoselect"><span class="glyphicon glyphicon-ok"></span> Analyst</td>
									<td class="text-center celltoselect"><span class="glyphicon glyphicon-ok"></span>Analyst</td>
									<td class="text-center celltoselect"><span class="glyphicon glyphicon-ok"></span>Analyst</td>
									<td class="text-center celltoselect"><span class="glyphicon glyphicon-ok"></span>Analyst</td>
								</tr>
							</tbody>
						</table>
						<div class="pull-right">
							<button type="button" class="btn btn-success radius-zero">Save</button>
							<a href="#">Cancel</a>
						</div>
						<!--  modal for source button -->
						<div class="modal fade" id="selectsource">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<strong>Select Source Picklist Values</strong>
										<a type="button" class="close" data-dismiss="modal">&nbsp;</a> 
									</div>
									<div class="modal-body">
										<table class="table" id="sourceTable">
											<tbody>
												<tr>
													<td><input type="checkbox"/> chemicals</td>
													<td><input type="checkbox"/> chemicals</td>
													<td><input type="checkbox"/> chemicals</td>
												</tr>
												<tr>
													<td><input type="checkbox"/> chemicals</td>
													<td><input type="checkbox"/> chemicals</td>
													<td><input type="checkbox"/> chemicals</td>
												</tr>
												<tr>
													<td><input type="checkbox"/> chemicals</td>
													<td><input type="checkbox"/> chemicals</td>
													<td><input type="checkbox"/> chemicals</td>
												</tr>
												<tr>
													<td><input type="checkbox"/> chemicals</td>
													<td><input type="checkbox"/> chemicals</td>
													<td><input type="checkbox"/> chemicals</td>
												</tr>
												<tr>
													<td><input type="checkbox"/> chemicals</td>
													<td><input type="checkbox"/> chemicals</td>
													<td><input type="checkbox"/> chemicals</td>
												</tr>
												<tr>
													<td><input type="checkbox"/> chemicals</td>
													<td><input type="checkbox"/> chemicals</td>
													<td><input type="checkbox"/> chemicals</td>
												</tr>
											</tbody>
										</table>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-success">Save</button>
										<a href="#" data-dismiss="modal">Cancel</a>
 									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

