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

			// clickable row
			$(".clickable-row").click(function() {
				window.document.location = $(this).data("href");
			});
		});
	</script>


	<div class="container-fluid">
		<div id="content">
			<div class="row" style="background-color:#fafafb;">

				<?php include_once 'crmLeft1.php';?> <!-- left side menus -->

				<div class="col-sm-10" id="rightside-detail"> <!-- crm right side page start -->
					<div class="bottom-seperator">
						<h1 class="page-heading"> Users </h1>
					</div>

					<div class="listviewActionDiv row"> <!-- action, add, chosen links -->
						<div class="col-sm-4">
							<div class="btn-group">
								<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" id="dropdownMenu1">Action
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu">
									<li><a href="#">Export</a></li>
								</ul>
							</div>
							<!--<a href="#" class="btn btn-default"> Action <span class="glyphicon glyphicon-triangle-bottom"></span></a>-->
							<a href="addUsers.php" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span> Add Users </a>
						</div>

						<div class="col-sm-4">
							<div class="chzn-container chzn-container-single">
								<select style="width: 220px;" class="chosen-select" data-placeholder="Active Users">
									<option value=""></option>
									<option>Active Users</option>
									<option>Inactive Users</option>
								</select>
							</div>
						</div>

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

					<div class="listviewAction-pagination">
						<ul class="pagination pagination">
							<li><a href="#">A</a></li><li><a href="#">B</a></li><li><a href="#">C</a></li><li><a href="#">D</a></li>
							<li><a href="#">E</a></li><li><a href="#">F</a></li><li><a href="#">G</a></li><li><a href="#">H</a></li>
							<li><a href="#">I</a></li><li><a href="#">J</a></li><li><a href="#">K</a></li><li><a href="#">L</a></li>
							<li><a href="#">M</a></li><li><a href="#">N</a></li><li><a href="#">O</a></li><li><a href="#">P</a></li>
							<li><a href="#">Q</a></li><li><a href="#">R</a></li><li><a href="#">S</a></li><li><a href="#">T</a></li>
							<li><a href="#">U</a></li><li><a href="#">V</a></li><li><a href="#">W</a></li><li><a href="#">X</a></li>
							<li><a href="#">Y</a></li><li><a href="#">Z</a></li>
						</ul>
					</div>

					<div> <!-- Users Table div -->
						<div class="div2">
							<table id="listingTable" class="table table-hover">
								<thead> 
									<tr>
										<th><a href="#"> Details </a></th>
										<th><a href="#"></a></th>
										<th><a href="#"> Role </a></th>
										<th><a href="#"> User Name </a></th>
										<th><a href="#"> Status </a></th>
										<th><a href="#"> Other Email </a></th>
										<th><a href="#"> Admin </a></th>
										<th><a href="#"> Office Phone </a></th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									<tr class='clickable-row' data-href='userDetail.php'>
										<td><a href="userDetail.php"> <img src="../../images/user.png"> </td>
										<td><a href="userDetail.php"> Tetra Administrator </a> <br> <a href="mailto:Khushboo@tetrain.com"> Khushboo@tetrain.com </a></td>
										<td><a href="addRoles.php"> CEO </a></td>
										<td><a href="userDetail.php"> admin </a></td>
										<td><a href="#"></a></td>
										<td><a href="userDetail.php"> Active </a></td>
										<td><a href="userDetail.php"> yes </a></td>
										<td><a href="userDetail.php"></a></td>
										<td><a href="addUsers.php"><span class="glyphicon glyphicon-pencil editbtn" title="Edit"></span></a></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div> <!-- Users Table div end -->

				</div> <!-- crm right side page end -->

			</div>
		</div>
	</div>