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

<div class="container-fluid">
	<div class="row" style="background-color:#fafafb;">

		<?php include_once 'crmLeft1.php';?> <!-- left side menus -->

		<div class="col-sm-10" id="rightside-detail"> <!-- crm right side page start -->
			<div class="col-sm-12 line-seperator">
				<div class="col-sm-10">
					<h1 class="marginleft page-heading"> Team Selling </h1>
				</div>
				<div class="col-sm-2">
					<button class="btn btn-success pull-right addgrpsave" value="Edit" type="submit"><strong> Edit </strong></button>
				</div>
			</div>

			<div class="listviewActionDiv row"> <!-- group table strat -->
				<div class="col-sm-12">
					<label class="col-sm-2 control-label groupslabel"> Group Name <span class="star">*</span> </label>
					<div class="col-sm-3 groupsvalue"> <b> Team Selling </b> </div>
					<div class="col-sm-7"></div>
				</div>
				<div class="col-sm-12">
					<label class="col-sm-2 control-label groupslabel"> Description </label>
					<div class="col-sm-3"> <b> Group Related to Sales </b> </div>
					<div class="col-sm-7"></div>
				</div>
				<div class="col-sm-12">
					<label class="col-sm-2 control-label groupslabel"> Group Members </label>
					<div class="col-sm-3">
						<ul class="nav nav-list list-group grouplist">
							<li class="grouplist-header"> <b> ROLES </b> </li>
							<li class="grouplist-link"> <a href="addRoles.php"> Sales Manager </a> </li>
							<li class="grouplist-header"> <b> ROLE AND SUBORDINATES </b> </li>
							<li class="grouplist-link"> <a href="addRoles.php"> Sales Person </a> </li>
						</ul>
					</div>
					<div class="col-sm-7"></div>
				</div>
			</div> <!-- group table end -->
		</div> <!-- crm right side page end -->
	</div>
</div>