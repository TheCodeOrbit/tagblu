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
			// Chosen Click
			$(".chosen-select").chosen();

			// Dropdown
			$('.dropdown-toggle').dropdown();

			// table dropdown

			$("#userheading").click(function(){
				$(this).toggleClass('glyphicon glyphicon-triangle-bottom glyphicon glyphicon-triangle-right');
				$("#usertable").slideToggle("slow");
			});
		});
	</script>

	<div class="container-fluid">
		<div id="content">
			<div class="row">

				<div class="col-sm-12" id="rightside-detail">
					<div class="row"> <!-- header start -->
						<span class="col-sm-3">
							<img style="width:auto; height:55px;" src="/tetraclients/adani/images/logo.png" alt="JAGSONPAL">
						</span>
						<span class="col-sm-9">
							<h1 class="page-heading text-center"> Users </h1>
						</span>
					</div> <!-- header end -->

					<div class="user-reportDiv row"> <!-- action, add, chosen links start -->
						<div class="col-sm-4">
							<strong class="pull-right input-in">in</strong>
							<input type="text" class="form-control input-md pull-right inputwidth">
						</div>

						<div class="col-sm-4">
							<div class="chzn-container chzn-container-single pull-left">
								<select style="width: 220px;" class="chosen-select" data-placeholder="First Name">
									<option value=""></option>
									<option>User Name</option>
									<option>Primary Email</option>
									<option>First Name</option>
									<option>Last Name</option>
								</select>
							</div>
							<span class="searchicon">
								<span class="glyphicon glyphicon-search cursorPointer text-info searchbtn" type="button"></span>
							</span>
						</div>

						<!--<div class="col-sm-1"></div>-->

						<div class="col-sm-2">
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
					</div> <!-- action, add, chosen links end -->

					<div class="userdetailtable"> <!-- office table start -->
						<div class="customerForm-header">
							<a href="#"><span class="glyphicon glyphicon-triangle-bottom" id="userheading"></span></a> <strong> &nbsp; Office Phone </strong>
						</div>

						<table class="table table-bordered table-hover" id="usertable">
							<tbody>
								<tr>
									<td></td>
								</tr>
							</tbody>
						</table>
					</div> <!-- office table end -->
					
				</div>
			</div>
		</div>
	</div>
