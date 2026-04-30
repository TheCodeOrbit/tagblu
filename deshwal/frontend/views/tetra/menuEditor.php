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
				<!-- header part --->
				<div class="bottom-seperator">
					<h1 class="page-heading"> Menu Editor </h1>
				</div>
				<!--<div class="topcontent-details">
					<div class="row">
						<div class="col-sm-12">
							<h3 class="page-heading">Menu Editor</h3>							
						</div>
					</div>
				</div>-->
				<div class="row" style="padding-right:10px;">
					<div class="col-sm-12">
						<form role="form" name="" action="">
							 <select class="chosen-select form-control" multiple="true">
								<option value="AC">A</option>
								<option value="AD">B</option>
								<option value="AM">C</option>
								<option value="AP">D</option>
							</select>
						</form>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div class="bg-info infoBlock">
								<span class="circle"><span class="glyphicon glyphicon-info-sign"> </span> Selected Values will appear for the user with this role</span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div class="menuEditor-savebtn">
								<button type="button" class="btn btn-success">Save</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

