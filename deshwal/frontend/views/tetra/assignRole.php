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
					<div class="row bottom-seperator"> <!-- header start -->
						<span class="col-sm-8">
							<h1 class="page-heading"> Assign Role </h1>
						</span>
						<span class="col-sm-4">
							<img src="/tetraclients/adani/images/logo.png" alt="JAGSONPAL" style="margin-bottom:15px;">
						</span>
					</div> <!-- header end -->
					
					<ul class="listRoles"> <!--roles tree structure start -->
						<li class="treeRoles">
							<div class="edit">
								<span class="glyphicon glyphicon-minus minus-sign"></span>
								<a class="btn btn-primary activeEditTrash"> Organization </a>
							</div>

							<ul class="listRoles">
								<li class="treeRoles">
									<div class="edit">
										<span class="glyphicon glyphicon-minus minus-sign"></span>
										<a href="addRoles.php" class="btn btn-default activeEditTrash"> CEO </a>
									</div>

									<ul class="listRoles">
										<li class="treeRoles">
											<div class="edit">
												<span class="glyphicon glyphicon-minus minus-sign"></span>
												<a href="addRoles.php" class="btn btn-default activeEditTrash"> Vice President </a>
											</div>

											<ul class="listRoles">
												<li class="treeRoles">
													<div class="edit">
														<span class="glyphicon glyphicon-minus minus-sign"></span>
														<a href="addRoles.php" class="btn btn-default activeEditTrash"> Sales Manager </a>
													</div>

													<ul class="listRoles">
														<li class="treeRoles">
															<div class="edit">
																<span class="glyphicon glyphicon-minus minus-sign"></span>
																<a href="addRoles.php" class="btn btn-default activeEditTrash"> Sales Person </a>
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
					</ul> <!--roles tree structure end -->
				</div>
			</div>
		</div>
	</div>
