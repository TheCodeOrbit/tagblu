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


	<div class="container-fluid"><!-- container-fluid starts -->
		<div class="row" style="background-color:#fafafb;" id="fullpage"><!-- fullpage starts -->
			<?php include_once 'crmLeft1.php'?>
			<div class="col-sm-10" id="rightside-main">
				<div>	<!-- header part starts--->
					<div class="row bottom-seperator">
						<div class="col-sm-12">
							<h1 class="page-heading">Picklist Dependency</h1>
						</div>
					</div>
				</div> <!-- header part ends -->
				<form class="form form-horizontal" name=""> <!-- form starts -->
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label for="module" class="control-label col-sm-3">Module</label>
								<div class="col-sm-9">
									<select class="chosen-select form-control" style="width:280px;">
										<option>Organization</option>
										<option>To Do</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="sourceField" class="control-label col-sm-3">Source Field</label>
								<div class="col-sm-9">
									<select class="chosen-select form-control" style="width:280px;">
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
									<select class="chosen-select form-control" style="width:280px;">
										<option>Rating</option>
										<option>Industry</option>
									</select>
								</div>
							</div>
						</div>
					</div>
				</form> <!-- form ends -->
			</div>
		</div><!-- fullpage ends -->
	</div><!-- container-fluid ends -->

