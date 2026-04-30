<script src="../../css/bootstrap-3.3.6-dist/js/jquery-1.12.0.min.js"></script>
<script src="../../js/jquery-ui.min.js"></script>
<script src="../../js/cookies.js"></script>
<script src="../../css/bootstrap-3.3.6-dist/js/bootstrap.min.js"></script>
<script src="../../js/chosen.jquery.min.js"></script>

<link rel="stylesheet" id="mainstylesheet" type="text/css" href="../../css/reset.css" />
<link rel="stylesheet" id="mainstylesheet" type="text/css" href="../../css/normalize.css" />
<link rel="stylesheet" href="../../css/bootstrap-3.3.6-dist/css/bootstrap.min.css"/>
<link rel="stylesheet" id="mainstylesheet" type="text/css" href="../../css/chosen/chosen.css" />

<script type="text/javascript">
	$(document).ready(function(){

// collapsing tables
	
	$(".triangleicon").click(function(){
		$(this).toggleClass('glyphicon glyphicon-triangle-bottom glyphicon glyphicon-triangle-right');
		$(this).closest('.customerForm-header').next('table').toggle('slide');
	});
	
		// input field (click on text)
			
		/*	$('.addUsers-value').find('.form-control').css('display','none');
			
			$('.addUsers-value').click(function(){
				$(this).find('.form-control').css('display','block');
				$(this).find('span').css('display','none');
			});
			$('.addUsers-value').find('.form-control').blur(function(){
				$(this).closest('span').css('display','block');
				$(this).css('display','none');
			});
			$('.addUsers-value').blur(function(){
				$(this).find('.form-control').css('display','none');
			});
			
	*/		
			
			
		var $inputSwitches = $(".userDetail-input"),
			$inputs = $inputSwitches.find(".inputbox-width"),
			$spans = $inputSwitches.find("span.users-input");
			$spans.on("click", function() {
				var $this = $(this);
				$this.hide().siblings(".inputbox-width").val($this.text()).show().focus().select();
			});

			$inputs.on("blur", function() {
				var $this = $(this);
				$this.hide().siblings("span.users-input").text($this.val()).show();
			})

			.on('keydown', function(e) {
				if (e.which == 9) {
					e.preventDefault();
					$(this).blur().parent().nextAll($inputSwitches).first().find($spans).click();
				}
			}).hide();

		// pick list code

		var $picklistShow = $(".userDetail-picklists"),
			$select = $picklistShow.find(".picklist"),
			$spans = $picklistShow.find("span.userDetail-picklistValue");
			$spans.on("click", function() {
				var $this = $(this);
				$this.hide().siblings(".picklist").val($this.text()).show().focus().select();
			});

			$select.on("blur", function() {
				var $this = $(this);
				$this.hide().siblings("span.userDetail-picklistValue").text($this.val()).show();
			}).on('keydown', function(e) {
				if (e.which == 9) {
					e.preventDefault();
					$(this).blur().parent().nextAll($picklistShow).first().find($spans).click();
				}
			}).hide();

		// Checkbox

		var $changetoCheckbox = $(".userDetail-checkbox"),
			$checkbox = $changetoCheckbox.find(".userDetail-checkBox"),
			$spans = $changetoCheckbox.find("span.userDetail-checkboxValue");
			$spans.on("click", function() {
				var $this = $(this);
				$this.hide().siblings(".userDetail-checkBox").val($this.text()).show().focus().select();
			});

			$checkbox.on("blur", function() {
				var $this = $(this);
				$this.hide().siblings("span.userDetail-checkboxValue").text($this.val()).show();
			}).on('keydown', function(e) {
				if (e.which == 9) {
					e.preventDefault();
					$(this).blur().parent().nextAll($changetoCheckbox).first().find($spans).click();
				}
			}).hide();

		// input field add click on td

		var $tdchangedInput = $(".userDetail-editInput"),
			$inputs = $tdchangedInput.find(".userDetail-inputBox"),
			$spans = $tdchangedInput.find("span.userDetail-input-textContainer");
			$spans.on("click", function() {
				var $this = $(this);
				$this.hide().siblings(".userDetail-inputBox").val($this.text()).show().focus().select();
			});

			$inputs.on("blur", function() {
				var $this = $(this);
				$this.hide().siblings("span.userDetail-input-textContainer").text($this.val()).show();
			}).on('keydown', function(e) {
				if (e.which == 9) {
					e.preventDefault();
					$(this).blur().parent().nextAll($tdchangedInput).first().find($spans).click();
				}
			}).hide();

		// input field add click on td

		var $changeblanktd = $(".userDetail-editTextarea"),
			$textarea = $changeblanktd.find(".userDetail-textarea"),
			$spans = $changeblanktd.find("span.userDetail-input-textContainer");
			$spans.on("click", function() {
				var $this = $(this);
				$this.hide().siblings(".userDetail-textarea").val($this.text()).show().focus().select();
			});

			$textarea.on("blur", function() {
				var $this = $(this);
				$this.hide().siblings("span.userDetail-input-textContainer").text($this.val()).show();
			}).on('keydown', function(e) {
				if (e.which == 9) {
					e.preventDefault();
					$(this).blur().parent().nextAll($changeblanktd).first().find($spans).click();
				}
			}).hide();	
	});

</script>


<div class="container-fluid">
	<div class="row" style="background-color:#fafafb;">

		<?php include_once 'crmLeft1.php';?>

		<div class="col-sm-10" id="rightside-main"> <!-- crm right side page start -->
			<form>
				<div class="topcontent-details">
					<div class="row">
						<div class="col-sm-8">
							<h1 class="addmargin0 page-heading"> Tetra Administrator </h1>
						</div>

						<div class="col-sm-4">
							<div class="pull-right">
								<button type="submit" value="Edit" class="btn btn-success addgrpsave"><strong> Edit </strong></button>
								<button type="submit" value="Change Password" class="btn btn-success addgrpsave"><strong> Change Password </strong></button>
							</div>
						</div>
					</div>
				</div>

				<div class="seperator"> <!-- user detail tables start -->
					<div class="userdetailtable">
						<div class="customerForm-header">
							<a href="#"><span class="glyphicon glyphicon-triangle-bottom triangleicon" id="userheading"></span></a> <strong> &nbsp; User Login & Role </strong>
						</div>

						<table class="table table-bordered table-hover" id="usertable">
							<tbody>
								<tr>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> User Name </label>
									</td>
									<td class="addUsers-value">
										<span> admin </span>
									</td>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Primary Email </label>
									</td>
									<td class="addUsers-value userDetail-input">
										<span class="users-input">Khushboo@tetrain.com</span>
										<input type="text" class="form-control input-md inputbox-width">
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> First Name </label>
									</td>
									<td class="addUsers-value userDetail-input">
										<span class="users-input">Tetra</span>
										<input type="text" class="form-control input-md inputbox-width">
									</td>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Last Name </label>
									</td>
									<td class="addUsers-value userDetail-input">
										<span>Administrator</span>
										<input type="text" class="form-control input-md inputbox-width">
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Admin </label>
									</td>
									<td class="addUsers-value">
										<span> Yes </span>
									</td>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Roles </label>
									</td>
									<td class="addUsers-value">
										<a href="addRoles.php"><span> CEO </span></a>
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Default Lead View </label>
									</td>
									<td class="addUsers-value userDetail-picklists">
										<span class="userDetail-picklistValue">Today</span>
										<select class="form-control picklist">
											<option class="selectspacing"> Today </option>
											<option class="selectspacing"> Last 2 Days </option>
											<option class="selectspacing"> Last Week </option>
										</select>
									</td>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Status </label>
									</td>
									<td class="addUsers-value">
										<span> Active </span>
									</td>
								</tr>
							</tbody>
						</table>
					</div>

					<div class="userdetailtable">
						<div class="customerForm-header">
							<a href="#"><span class="glyphicon glyphicon-triangle-bottom triangleicon" id="calenderheading"></span></a> <strong> &nbsp; Calendar Settings </strong>
						</div>

						<table class="table table-bordered table-hover" id="calendertable">
							<tbody>
								<tr>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Starting Day of the week </label>
									</td>
									<td class="addUsers-value userDetail-picklists">
										<span class="userDetail-picklistValue">Sunday</span>
										<select class="form-control picklist">
											<option class="selectspacing"> Sunday </option>
											<option class="selectspacing"> Monday </option>
											<option class="selectspacing"> Tuesday </option>
											<option class="selectspacing"> Wednesday </option>
											<option class="selectspacing"> Thursday </option>
											<option class="selectspacing"> Friday </option>
											<option class="selectspacing"> Saturday </option>
										</select>
									</td>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Day starts at </label>
									</td>
									<td class="addUsers-value userDetail-picklists">
										<span class="userDetail-picklistValue">12:00 AM</span>
										<select class="form-control picklist">
											<option class="selectspacing"> 12:00 AM </option>
											<option class="selectspacing"> 01:00 AM </option>
											<option class="selectspacing"> 02:00 AM </option>
											<option class="selectspacing"> 03:00 AM </option>
											<option class="selectspacing"> 04:00 AM </option>
											<option class="selectspacing"> 05:00 AM </option>
											<option class="selectspacing"> 06:00 AM </option>
											<option class="selectspacing"> 07:00 AM </option>
											<option class="selectspacing"> 08:00 AM </option>
											<option class="selectspacing"> 09:00 AM </option>
											<option class="selectspacing"> 10:00 AM </option>
											<option class="selectspacing"> 11:00 AM </option>
											<option class="selectspacing"> 12:00 PM </option>
											<option class="selectspacing"> 01:00 PM </option>
											<option class="selectspacing"> 02:00 PM </option>
											<option class="selectspacing"> 03:00 PM </option>
											<option class="selectspacing"> 04:00 PM </option>
											<option class="selectspacing"> 05:00 PM </option>
											<option class="selectspacing"> 06:00 PM </option>
											<option class="selectspacing"> 07:00 PM </option>
											<option class="selectspacing"> 08:00 PM </option>
											<option class="selectspacing"> 09:00 PM </option>
											<option class="selectspacing"> 10:00 PM </option>
											<option class="selectspacing"> 11:00 PM </option>
										</select>
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Date Format </label>
									</td>
									<td class="addUsers-value userDetail-picklists">
										<span class="userDetail-picklistValue">dd-mm-yyyy</span>
										<select class="form-control picklist">
											<option class="selectspacing"> dd-mm-yyyy </option>
											<option class="selectspacing"> mm-dd-yyyy </option>
											<option class="selectspacing"> yyyy-mm-dd </option>
										</select>
									</td>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Calendar Hour Format </label>
									</td>
									<td class="addUsers-value userDetail-picklists">
										<span class="userDetail-picklistValue">12</span>
										<select class="form-control picklist">
											<option class="selectspacing"> 12 </option>
											<option class="selectspacing"> 24 </option>
										</select>
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Time Zone </label>
									</td>
									<td class="addUsers-value userDetail-picklists">
										<span class="userDetail-picklistValue">(UTC-11:00) Coordinated Universal Time-11</span>
										<select class="form-control picklist">
											<option class="selectspacing"> (UTC-11:00) Coordinated Universal Time-11 </option>
											<option class="selectspacing"> (UTC-11:00) Samoa </option>
											<option class="selectspacing"> (UTC-10:00) Hawaii </option>
											<option class="selectspacing"> (UTC-09:00) Alaska </option>
											<option class="selectspacing"> (UTC-08:00) Pacific Time (US & Canada) </option>
											<option class="selectspacing"> (UTC-08:00) Tijuana, Baja California </option>
											<option class="selectspacing"> (UTC-07:00) Mountain Time (US & Canada) </option>
											<option class="selectspacing"> (UTC-07:00) Chihuahua, La Paz, Mazatlan </option>
											<option class="selectspacing"> (UTC-07:00) Mazatlan </option>
											<option class="selectspacing"> (UTC-07:00) Arizona </option>
											<option class="selectspacing"> (UTC-06:00) Saskatchewan </option>
											<option class="selectspacing"> (UTC-06:00) Central America </option>
											<option class="selectspacing"> (UTC-06:00) Central Time (US & Canada) </option>
											<option class="selectspacing"> (UTC-06:00) Mexico City </option>
											<option class="selectspacing"> (UTC-06:00) Monterrey </option>
											<option class="selectspacing"> (UTC-05:00) Eastern Time (US & Canada) </option>
											<option class="selectspacing"> (UTC-05:00) Bogota, Lima, Quito </option>
											<option class="selectspacing"> (UTC-05:00) Lima </option>
											<option class="selectspacing"> (UTC-05:00) Rio Branco </option>
											<option class="selectspacing"> (UTC-05:00) Indiana (East) </option>
											<option class="selectspacing"> (UTC-04:30) Caracas </option>
											<option class="selectspacing"> (UTC-04:00) Atlantic Time (Canada) </option>
											<option class="selectspacing"> (UTC-04:00) Manaus </option>
											<option class="selectspacing"> (UTC-04:00) Santiago </option>
											<option class="selectspacing"> (UTC-04:00) La Paz </option>
											<option class="selectspacing"> (UTC-04:00) Cuiaba </option>
											<option class="selectspacing"> (UTC-04:00) Asuncion </option>
											<option class="selectspacing"> (UTC-03:30) Newfoundland </option>
											<option class="selectspacing"> (UTC-03:00) Buenos Aires </option>
											<option class="selectspacing"> (UTC-03:00) Brasilia </option>
										</select>
									</td>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Default Calendar View </label>
									</td>
									<td class="addUsers-value userDetail-picklists">
										<span class="userDetail-picklistValue">Today</span>
										<select class="form-control picklist">
											<option class="selectspacing"> Today </option>
											<option class="selectspacing"> This Week </option>
											<option class="selectspacing"> This Month </option>
											<option class="selectspacing"> This Year </option>
										</select>
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Default Call Duration (Mins) </label>
									</td>
									<td class="addUsers-value userDetail-picklists">
										<span class="userDetail-picklistValue">5</span>
										<select class="form-control picklist">
											<option class="selectspacing"> 5 </option>
											<option class="selectspacing"> 10 </option>
											<option class="selectspacing"> 30 </option>
											<option class="selectspacing"> 60 </option>
											<option class="selectspacing"> 120 </option>
										</select>
									</td>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Other Event Duration (Mins) </label>
									</td>
									<td class="addUsers-value userDetail-picklists">
										<span class="userDetail-picklistValue">5</span>
										<select class="form-control picklist">
											<option class="selectspacing"> 5 </option>
											<option class="selectspacing"> 10 </option>
											<option class="selectspacing"> 30 </option>
											<option class="selectspacing"> 60 </option>
											<option class="selectspacing"> 120 </option>
										</select>
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Default Event Status </label>
									</td>
									<td class="addUsers-value userDetail-picklists">
										<span class="userDetail-picklistValue">Select an Option</span>
										<select class="form-control picklist">
											<option class="selectspacing"> Select an Option </option>
											<option class="selectspacing"> Planned </option>
											<option class="selectspacing"> Held </option>
											<option class="selectspacing"> Not Held </option>
										</select>
									</td>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Default Activity Type </label>
									</td>
									<td class="addUsers-value userDetail-picklists">
										<span class="userDetail-picklistValue">Select an Option</span>
										<select class="form-control picklist">
											<option class="selectspacing"> Select an Option </option>
											<option class="selectspacing"> Call </option>
											<option class="selectspacing"> Meeting </option>
											<option class="selectspacing"> Mobile Call </option>
										</select>
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Popup Reminder Interval </label>
									</td>
									<td class="addUsers-value userDetail-picklists">
										<span class="userDetail-picklistValue">1 Minutes</span>
										<select class="form-control picklist">
											<option class="selectspacing"> Select an Option </option>
											<option class="selectspacing"> 1 Minutes </option>
											<option class="selectspacing"> 5 Minutes </option>
											<option class="selectspacing"> 15 Minutes </option>
											<option class="selectspacing"> 30 Minutes </option>
											<option class="selectspacing"> 45 Minutes </option>
											<option class="selectspacing"> 1 Hour </option>
											<option class="selectspacing"> 1 Day </option>
										</select>
									</td>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Hide Completed Calendar Events </label>
									</td>
									<td class="addUsers-value userDetail-checkbox">
										<span class="userDetail-checkboxValue">No</span>
										<input type="checkbox" name="" class="userDetail-checkBox">
									</td>
								</tr>
							</tbody>
						</table>
					</div>

					<div class="userdetailtable">
						<div class="customerForm-header">
							<a href="#"><span class="glyphicon glyphicon-triangle-bottom triangleicon" id="currencyheading"></span></a> <strong> &nbsp; Currency and Number Field Configuration </strong>
						</div>

						<table class="table table-bordered table-hover" id="currencytable">
							<tbody>
								<tr>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Currency </label>
									</td>
									<td class="addUsers-value userDetail-picklists">
										<span class="userDetail-picklistValue">India, Rupees</span>
										<select class="form-control picklist">
											<option class="selectspacing"> India, Rupees </option>
										</select>
									</td>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Digit Grouping Pattern </label>
									</td>
									<td class="addUsers-value userDetail-picklists">
										<span class="userDetail-picklistValue">123,456,789</span>
										<select class="form-control picklist">
											<option class="selectspacing"> 123,456,789 </option>
											<option class="selectspacing"> 123456789 </option>
											<option class="selectspacing"> 123456,789 </option>
											<option class="selectspacing"> 12,34,56,789 </option>
										</select>
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Decimal Separator </label>
									</td>
									<td class="addUsers-value userDetail-picklists">
										<span class="userDetail-picklistValue">.</span>
										<select class="form-control picklist">
											<option class="selectspacing"> . </option>
											<option class="selectspacing"> , </option>
											<option class="selectspacing"> ' </option>
											<option class="selectspacing"> Space </option>
											<option class="selectspacing"> $ </option>
										</select>
									</td>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Digit Grouping Separator </label>
									</td>
									<td class="addUsers-value userDetail-picklists">
										<span class="userDetail-picklistValue">,</span>
										<select class="form-control picklist">
											<option class="selectspacing"> . </option>
											<option class="selectspacing"> , </option>
											<option class="selectspacing"> ' </option>
											<option class="selectspacing"> Space </option>
											<option class="selectspacing"> $ </option>
										</select>
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Symbol Placement </label>
									</td>
									<td class="addUsers-value userDetail-picklists">
										<span class="userDetail-picklistValue">$1.0</span>
										<select class="form-control picklist">
											<option class="selectspacing"> $1.0 </option>
											<option class="selectspacing"> 1.0$ </option>
										</select>
									</td>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Number Of Currency Decimals </label>
									</td>
									<td class="addUsers-value userDetail-picklists">
										<span class="userDetail-picklistValue">2</span>
										<select class="form-control picklist">
											<option class="selectspacing"> 0 </option>
											<option class="selectspacing"> 1 </option>
											<option class="selectspacing"> 2 </option>
											<option class="selectspacing"> 3 </option>
											<option class="selectspacing"> 4 </option>
											<option class="selectspacing"> 5 </option>
										</select>
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Truncate Trailing Zeros </label>
									</td>
									<td class="addUsers-value userDetail-checkbox">
										<span class="userDetail-checkboxValue">No</span>
										<input type="checkbox" name="" class="userDetail-checkBox">
									</td>
								</tr>
							</tbody>
						</table>
					</div>

					<div class="userdetailtable">
						<div class="customerForm-header">
							<a href="#"><span class="glyphicon glyphicon-triangle-bottom triangleicon" id="infoheading"></span></a> <strong> &nbsp; More Information </strong>
						</div>

						<table class="table table-bordered table-hover" id="infotable">
							<tbody>
								<tr>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Title </label>
									</td>
									<td class="addUsers-value userDetail-editInput">
										<span class="userDetail-input-textContainer"></span>
										<input type="text" class="form-control input-md userDetail-inputBox">
									</td>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Fax </label>
									</td>
									<td class="addUsers-value userDetail-editInput">
										<span class="userDetail-input-textContainer"></span>
										<input type="text" class="form-control input-md userDetail-inputBox">
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Department </label>
									</td>
									<td class="addUsers-value userDetail-editInput">
										<span class="userDetail-input-textContainer"></span>
										<input type="text" class="form-control input-md userDetail-inputBox">
									</td>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Other Email </label>
									</td>
									<td class="addUsers-value userDetail-editInput">
										<span class="userDetail-input-textContainer"></span>
										<input type="text" class="form-control input-md userDetail-inputBox">
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Office Phone </label>
									</td>
									<td class="addUsers-value userDetail-editInput">
										<span class="userDetail-input-textContainer"></span>
										<input type="text" class="form-control input-md userDetail-inputBox">
									</td>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Secondary Email </label>
									</td>
									<td class="addUsers-value userDetail-editInput">
										<span class="userDetail-input-textContainer"></span>
										<input type="text" class="form-control input-md userDetail-inputBox">
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Mobile Phone </label>
									</td>
									<td class="addUsers-value userDetail-editInput">
										<span class="userDetail-input-textContainer"></span>
										<input type="text" class="form-control input-md userDetail-inputBox">
									</td>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Reports To </label>
									</td>
									<td class="addUsers-value">
										<!--<div class="input-group  inputbox-width" id="main_note">
											<span class="transponame input-group-addon">
												<span class="glyphicon glyphicon-remove-circle cursorPointer text-info" type="button" title="Clear">
												</span>
											</span>
											<input class="form-control" type="text" value="" name="depotcode1" id="depotcode1">
											<span class="transearch input-group-addon">
												<span class="searchtrans glyphicon glyphicon-search cursorPointer text-info" type="button" title="Select"></span>
											</span>
											<span class="transponame input-group-addon">
												<span class="glyphicon glyphicon-plus cursorPointer text-info" title="Create"></span>
											</span>
										</div>-->
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Home Phone </label>
									</td>
									<td class="addUsers-value userDetail-editInput">
										<span class="userDetail-input-textContainer"></span>
										<input type="text" class="form-control input-md userDetail-inputBox">
									</td>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Secondary Phone </label>
									</td>
									<td class="addUsers-value userDetail-editInput">
										<span class="userDetail-input-textContainer"></span>
										<input type="text" class="form-control input-md userDetail-inputBox">
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Signature </label>
									</td>
									<td class="addUsers-value userDetail-editTextarea">
										<span class="userDetail-input-textContainer"></span>
										<textarea class="form-control userDetail-textarea"></textarea>
									</td>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Documents </label>
									</td>
									<td class="addUsers-value userDetail-editTextarea">
										<span class="userDetail-input-textContainer"></span>
										<textarea class="form-control userDetail-textarea"></textarea>
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Internal Mail Composer </label>
									</td>
									<td class="addUsers-value userDetail-checkbox">
										<span class="userDetail-checkboxValue">No</span>
										<input type="checkbox" name="" class="userDetail-checkBox">
									</td>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Theme </label>
									</td>
									<td class="addUsers-value userDetail-picklists">
										<span class="userDetail-picklistValue">Alphagrey</span>
										<select class="form-control picklist">
											<option class="selectspacing"> Alphagrey </option>
											<option class="selectspacing"> Softed </option>
											<option class="selectspacing"> Bluelagoon </option>
											<option class="selectspacing"> Nature </option>
										</select>
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Language </label>
									</td>
									<td class="addUsers-value userDetail-picklists">
										<span class="userDetail-picklistValue">English</span>
										<select class="form-control picklist">
											<option class="selectspacing"> English </option>
											<option class="selectspacing"> b </option>
											<option class="selectspacing"> c </option>
											<option class="selectspacing"> d </option>
											<option class="selectspacing"> e </option>
										</select>
									</td>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> CRM Phone Extension </label>
									</td>
									<td class="addUsers-value userDetail-editInput">
										<span class="userDetail-input-textContainer"></span>
										<input type="text" class="form-control input-md userDetail-inputBox">
									</td>
								</tr>
								
								<tr>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Default Record View </label>
									</td>
									<td class="addUsers-value userDetail-picklists">
										<span class="userDetail-picklistValue">Summary</span>
										<select class="form-control picklist">
											<option class="selectspacing"> Summary </option>
											<option class="selectspacing"> Detail </option>
										</select>
									</td>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Left Panel Hide </label>
									</td>
									<td class="addUsers-value userDetail-checkbox">
										<span class="userDetail-checkboxValue">No</span>
										<input type="checkbox" name="" class="userDetail-checkBox">
									</td>
								</tr>
								
								<tr>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Row Height </label>
									</td>
									<td class="addUsers-value userDetail-picklists">
										<span class="userDetail-picklistValue">Medium</span>
										<select class="form-control picklist">
											<option class="selectspacing"> Wide </option>
											<option class="selectspacing"> Medium </option>
											<option class="selectspacing"> Narrow </option>
										</select>
									</td>
								</tr>
							</tbody>
						</table>
					</div>

					<div class="userdetailtable">
						<div class="customerForm-header">
							<a href="#"><span class="glyphicon glyphicon-triangle-bottom triangleicon" id="addressheading"></span></a> <strong> &nbsp; User Address </strong>
						</div>

						<table class="table table-bordered table-hover" id="addresstable">
							<tbody>
								<tr>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Street Address </label>
									</td>
									<td class="addUsers-value userDetail-editTextarea">
										<span class="userDetail-input-textContainer"></span>
										<textarea class="form-control userDetail-textarea"></textarea>
									</td>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Country </label>
									</td>
									<td class="addUsers-value userDetail-editInput">
										<span class="userDetail-input-textContainer"></span>
										<input type="text" class="form-control input-md userDetail-inputBox">
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> City </label>
									</td>
									<td class="addUsers-value userDetail-editInput">
										<span class="userDetail-input-textContainer"></span>
										<input type="text" class="form-control input-md userDetail-inputBox">
									</td>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Postal Code </label>
									</td>
									<td class="addUsers-value userDetail-editInput">
										<span class="userDetail-input-textContainer"></span>
										<input type="text" class="form-control input-md userDetail-inputBox">
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> State </label>
									</td>
									<td class="addUsers-value userDetail-editInput">
										<span class="userDetail-input-textContainer"></span>
										<input type="text" class="form-control input-md userDetail-inputBox">
									</td>
								</tr>
							</tbody>
						</table>
					</div>

					<div class="userdetailtable">
						<div class="customerForm-header">
							<a href="#"><span class="glyphicon glyphicon-triangle-bottom triangleicon" id="photoheading"></span></a> <strong> &nbsp; User Photograph </strong>
						</div>

						<table class="table table-bordered table-hover" id="phototable">
							<tbody>
								<tr>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Upload Photograph </label>
									</td>
									<td class="addUsers-value">
										<!--<input type="file">-->
									</td>
								</tr>
							</tbody>
						</table>
					</div>

					<div class="userdetailtable">
						<div class="customerForm-header">
							<a href="#"><span class="glyphicon glyphicon-triangle-bottom triangleicon" id="advancetable"></span></a> <strong> &nbsp; User Advanced Options </strong>
						</div>

						<table class="table table-bordered table-hover" id="advancedetail">
							<tbody>
								<tr>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Access Key </label>
									</td>
									<td class="addUsers-value">
										<span> hkhkhkj </span>
									</td>
									<td class="addUsers-value"></td>
									<td class="addUsers-value"></td>
								</tr>
							</tbody>
						</table>
					</div>

					<div class="userdetailtable">
						<div class="customerForm-header">
							<strong> Tag Cloud Display </strong>
						</div>

						<table class="table table-bordered table-hover">
							<tbody>
								<tr>
									<td class="addUsers-label">
										<label class="userDetail-labels pull-right"> Tag Cloud </label>
									</td>
									<td class="addUsers-value">
										<!--<input type="checkbox" name="">-->
										<span> <span class="glyphicon glyphicon-ok"></span> &nbsp; Shown </span>
									</td>
									<td class="addUsers-value"></td>
									<td class="addUsers-value"></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div> <!-- user detail tables end -->
			</form>
		</div> <!-- crm right side page end -->
	</div>
</div>