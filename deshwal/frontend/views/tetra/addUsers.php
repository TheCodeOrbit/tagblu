<script src="../../css/bootstrap-3.3.6-dist/js/jquery-1.12.0.min.js"></script>
<script src="../../js/jquery-ui.min.js"></script>
<script src="../../js/cookies.js"></script>
<script src="../../css/bootstrap-3.3.6-dist/js/bootstrap.min.js"></script>
<script src="../../js/chosen.jquery.min.js"></script>
<!-- chosen -->
<script type="text/javascript" src="../../js/chosen.jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="../../css/chosen/chosen.css">
<!-- end -->
<link rel="stylesheet" id="mainstylesheet" type="text/css" href="../../css/reset.css" />
<link rel="stylesheet" id="mainstylesheet" type="text/css" href="../../css/normalize.css" />
<link rel="stylesheet" href="../../css/bootstrap-3.3.6-dist/css/bootstrap.min.css"/>
<link rel="stylesheet" id="mainstylesheet" type="text/css" href="../../css/chosen/chosen.css" />
	<script>
		$(document).ready(function(){
			// Chosen Click
			$(".chosen-select").chosen();

			// Dropdown
			$('.dropdown-toggle').dropdown();
		});
	</script>

<div class="container-fluid">
	<div class="row" style="background-color:#fafafb;">

		<?php include_once 'crmLeft1.php';?> <!-- left side menus -->

		<div class="col-sm-10" id="rightside-main"> <!-- crm right side page start -->
			
				<div class="topcontent-details">
					<div class="row">
						<div class="col-sm-8">
							<h1 class="addmargin0 page-heading"> Creating New User </h1>
						</div>

						<div class="col-sm-4">
							<div class="pull-right">
								<button type="submit" class="btn btn-success addgrpsave"><strong>Save</strong></button>
								<a class="pull-right addgrpcancel" href="#">Cancel</a>
							</div>
						</div>
					</div>
				</div>

				<div class="seperator"> <!-- add user tables start -->
					<div>
						<div class="customerForm-header">
							<strong> User Login & Role </strong>
						</div>

						<table class="table table-bordered table-hover"> <!-- user login table start -->
							<tbody>
								<tr>
									<td class="addUsers-label">
										<label class="control-label"> User Name <span class="star">*</span></label>
									</td>
									<td class="addUsers-value">
										<input type="text" class="inputwidth form-control input-md">
									</td>
									<td class="addUsers-label">
										<label class="control-label"> Primary Email <span class="star">*</span></label>
									</td>
									<td class="addUsers-value">
										<input type="text" class="inputwidth form-control input-md">
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="control-label"> First Name </label>
									</td>
									<td class="addUsers-value">
										<input type="text" class="inputwidth form-control input-md">
									</td>
									<td class="addUsers-label">
										<label class="control-label"> Last Name <span class="star">*</span></label>
									</td>
									<td class="addUsers-value">
										<input type="text" class="inputwidth form-control input-md">
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="control-label"> Password <span class="star">*</span></label>
									</td>
									<td class="addUsers-value">
										<input type="Password" class="inputwidth form-control input-md">
									</td>
									<td class="addUsers-label">
										<label class="control-label"> Confirm Password <span class="star">*</span></label>
									</td>
									<td class="addUsers-value">
										<input type="Password" class="inputwidth form-control input-md">
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="control-label"> Admin </label>
									</td>
									<td class="addUsers-value">
										<input type="checkbox" name="">
									</td>
									<td class="addUsers-label">
										<label class="control-label"> Roles <span class="star">*</span></label>
									</td>
									<td class="addUsers-value">
										<div class="chzn-container chzn-container-single">
											<select class="form-control inputwidth styled-select chosen-select">
												<option value="1"> CEO </option>
												<option value="2"> Vice President </option>
												<option value="3"> Sales Manager </option>
												<option value="4"> Sales Person </option>
											</select>
										</div>
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="control-label"> Default Lead View </label>
									</td>
									<td class="addUsers-value">
										<div class="chzn-container chzn-container-single">
											<select class="form-control inputwidth styled-select chosen-select">
												<option value="1"> Today </option>
												<option value="2"> Last 2 Days </option>
												<option value="3"> Last Week </option>
											</select>
										</div>
									</td>
								</tr>
							</tbody>
						</table> <!-- user login table end -->

						<div class="customerForm-header">
							<strong> Calendar Settings </strong>
						</div>

						<table class="table table-bordered table-hover"> <!-- calender setting table start -->
							<tbody>
								<tr>
									<td class="addUsers-label">
										<label class="control-label"> Starting Day of the week </label>
									</td>
									<td class="addUsers-value">
										<div class="chzn-container chzn-container-single">
											<select class="form-control inputwidth styled-select chosen-select">
												<option> Sunday </option>
												<option> Monday </option>
												<option> Tuesday </option>
												<option> Wednesday </option>
												<option> Thursday </option>
												<option> Friday </option>
												<option> Saturday </option>
											</select>
										</div>
									</td>
									<td class="addUsers-label">
										<label class="control-label"> Day starts at </label>
									</td>
									<td class="addUsers-value">
										<div class="chzn-container chzn-container-single">
											<select class="form-control inputwidth styled-select chosen-select">
												<option> 12:00 AM </option>
												<option> 01:00 AM </option>
												<option> 02:00 AM </option>
												<option> 03:00 AM </option>
												<option> 04:00 AM </option>
												<option> 05:00 AM </option>
												<option> 06:00 AM </option>
												<option> 07:00 AM </option>
												<option> 08:00 AM </option>
												<option> 09:00 AM </option>
												<option> 10:00 AM </option>
												<option> 11:00 AM </option>
												<option> 12:00 PM </option>
												<option> 01:00 PM </option>
												<option> 02:00 PM </option>
												<option> 03:00 PM </option>
												<option> 04:00 PM </option>
												<option> 05:00 PM </option>
												<option> 06:00 PM </option>
												<option> 07:00 PM </option>
												<option> 08:00 PM </option>
												<option> 09:00 PM </option>
												<option> 10:00 PM </option>
												<option> 11:00 PM </option>
											</select>
										</div>
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="control-label"> Date Format </label>
									</td>
									<td class="addUsers-value">
										<div class="chzn-container chzn-container-single">
											<select class="form-control inputwidth styled-select chosen-select">
												<option> dd-mm-yyyy </option>
												<option> mm-dd-yyyy </option>
												<option> yyyy-mm-dd </option>
											</select>
										</div>
									</td>
									<td class="addUsers-label">
										<label class="control-label"> Calendar Hour Format </label>
									</td>
									<td class="addUsers-value">
										<div class="chzn-container chzn-container-single">
											<select class="form-control inputwidth styled-select chosen-select">
												<option> 12 </option>
												<option> 24 </option>
											</select>
										</div>
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="control-label"> Time Zone </label>
									</td>
									<td class="addUsers-value">
										<div class="chzn-container chzn-container-single">
											<select class="form-control inputwidth styled-select chosen-select">
												<option> (UTC-11:00) Coordinated Universal Time-11 </option>
												<option> (UTC-11:00) Samoa </option>
												<option> (UTC-10:00) Hawaii </option>
												<option> (UTC-09:00) Alaska </option>
												<option> (UTC-08:00) Pacific Time (US & Canada) </option>
												<option> (UTC-08:00) Tijuana, Baja California </option>
												<option> (UTC-07:00) Mountain Time (US & Canada) </option>
												<option> (UTC-07:00) Chihuahua, La Paz, Mazatlan </option>
												<option> (UTC-07:00) Mazatlan </option>
												<option> (UTC-07:00) Arizona </option>
												<option> (UTC-06:00) Saskatchewan </option>
												<option> (UTC-06:00) Central America </option>
												<option> (UTC-06:00) Central Time (US & Canada) </option>
												<option> (UTC-06:00) Mexico City </option>
												<option> (UTC-06:00) Monterrey </option>
												<option> (UTC-05:00) Eastern Time (US & Canada) </option>
												<option> (UTC-05:00) Bogota, Lima, Quito </option>
												<option> (UTC-05:00) Lima </option>
												<option> (UTC-05:00) Rio Branco </option>
												<option> (UTC-05:00) Indiana (East) </option>
												<option> (UTC-04:30) Caracas </option>
												<option> (UTC-04:00) Atlantic Time (Canada) </option>
												<option> (UTC-04:00) Manaus </option>
												<option> (UTC-04:00) Santiago </option>
												<option> (UTC-04:00) La Paz </option>
												<option> (UTC-04:00) Cuiaba </option>
												<option> (UTC-04:00) Asuncion </option>
												<option> (UTC-03:30) Newfoundland </option>
												<option> (UTC-03:00) Buenos Aires </option>
												<option> (UTC-03:00) Brasilia </option>
											</select>
										</div>
									</td>
									<td class="addUsers-label">
										<label class="control-label"> Default Calendar View </label>
									</td>
									<td class="addUsers-value">
										<div class="chzn-container chzn-container-single">
											<select class="form-control inputwidth styled-select chosen-select">
												<option> Today </option>
												<option> This Week </option>
												<option> This Month </option>
												<option> This Year </option>
											</select>
										</div>
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="control-label"> Default Call Duration (Mins) </label>
									</td>
									<td class="addUsers-value">
										<div class="chzn-container chzn-container-single">
											<select class="form-control inputwidth styled-select chosen-select">
												<option> 5 </option>
												<option> 10 </option>
												<option> 30 </option>
												<option> 60 </option>
												<option> 120 </option>
											</select>
										</div>
									</td>
									<td class="addUsers-label">
										<label class="control-label"> Other Event Duration (Mins) </label>
									</td>
									<td class="addUsers-value">
										<div class="chzn-container chzn-container-single">
											<select class="form-control inputwidth styled-select chosen-select">
												<option> 5 </option>
												<option> 10 </option>
												<option> 30 </option>
												<option> 60 </option>
												<option> 120 </option>
											</select>
										</div>
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="control-label"> Default Event Status </label>
									</td>
									<td class="addUsers-value">
										<div class="chzn-container chzn-container-single">
											<select class="form-control inputwidth styled-select chosen-select">
												<option> Select an Option </option>
												<option> Planned </option>
												<option> Held </option>
												<option> Not Held </option>
											</select>
										</div>
									</td>
									<td class="addUsers-label">
										<label class="control-label"> Default Activity Type </label>
									</td>
									<td class="addUsers-value">
										<div class="chzn-container chzn-container-single">
											<select class="form-control inputwidth styled-select chosen-select">
												<option> Select an Option </option>
												<option> Call </option>
												<option> Meeting </option>
												<option> Mobile Call </option>
											</select>
										</div>
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="control-label"> Popup Reminder Interval </label>
									</td>
									<td class="addUsers-value">
										<div class="chzn-container chzn-container-single">
											<select class="form-control inputwidth styled-select chosen-select">
												<option> Select an Option </option>
												<option> 1 Minutes </option>
												<option> 5 Minutes </option>
												<option> 15 Minutes </option>
												<option> 30 Minutes </option>
												<option> 45 Minutes </option>
												<option> 1 Hour </option>
												<option> 1 Day </option>
											</select>
										</div>
									</td>
									<td class="addUsers-label">
										<label class="control-label"> Hide Completed Calendar Events </label>
									</td>
									<td class="addUsers-value">
										<input type="checkbox" name="">
									</td>
								</tr>
							</tbody>
						</table> <!-- calender setting table end -->

						<div class="customerForm-header">
							<strong> Currency and Number Field Configuration </strong>
						</div>

						<table class="table table-bordered table-hover"> <!-- currency table start -->
							<tbody>
								<tr>
									<td class="addUsers-label">
										<label class="control-label"> Currency </label>
									</td>
									<td class="addUsers-value">
										<div class="chzn-container chzn-container-single">
											<select class="form-control inputwidth styled-select chosen-select">
												<option> India, Rupees </option>
											</select>
										</div>
									</td>
									<td class="addUsers-label">
										<label class="control-label"> Digit Grouping Pattern </label>
									</td>
									<td class="addUsers-value">
										<div class="chzn-container chzn-container-single">
											<select class="form-control inputwidth styled-select chosen-select">
												<option> 123,456,789 </option>
												<option> 123456789 </option>
												<option> 123456,789 </option>
												<option> 12,34,56,789 </option>
											</select>
										</div>
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="control-label"> Decimal Separator </label>
									</td>
									<td class="addUsers-value">
										<div class="chzn-container chzn-container-single">
											<select class="form-control inputwidth styled-select chosen-select">
												<option> . </option>
												<option> , </option>
												<option> ' </option>
												<option> Space </option>
												<option> $ </option>
											</select>
										</div>
									</td>
									<td class="addUsers-label">
										<label class="control-label"> Digit Grouping Separator </label>
									</td>
									<td class="addUsers-value">
										<div class="chzn-container chzn-container-single">
											<select class="form-control inputwidth styled-select chosen-select">
												<option> . </option>
												<option> , </option>
												<option> ' </option>
												<option> Space </option>
												<option> $ </option>
											</select>
										</div>
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="control-label"> Symbol Placement </label>
									</td>
									<td class="addUsers-value">
										<div class="chzn-container chzn-container-single">
											<select class="form-control inputwidth styled-select chosen-select">
												<option> $1.0 </option>
												<option> 1.0$ </option>
											</select>
										</div>
									</td>
									<td class="addUsers-label">
										<label class="control-label"> Number Of Currency Decimals </label>
									</td>
									<td class="addUsers-value">
										<div class="chzn-container chzn-container-single">
											<select class="form-control inputwidth styled-select chosen-select">
												<option> 0 </option>
												<option> 1 </option>
												<option> 2 </option>
												<option> 3 </option>
												<option> 4 </option>
												<option> 5 </option>
											</select>
										</div>
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="control-label"> Truncate Trailing Zeros </label>
									</td>
									<td class="addUsers-value">
										<input type="checkbox" name="">
									</td>
								</tr>
							</tbody>
						</table> <!-- currency table end -->

						<div class="customerForm-header">
							<strong> More Information </strong>
						</div>

						<table class="table table-bordered table-hover"> <!-- more info table start -->
							<tbody>
								<tr>
									<td class="addUsers-label">
										<label class="control-label"> Title </label>
									</td>
									<td class="addUsers-value">
										<input type="text" class="inputwidth form-control input-md">
									</td>
									<td class="addUsers-label">
										<label class="control-label"> Fax </label>
									</td>
									<td class="addUsers-value">
										<input type="text" class="inputwidth form-control input-md">
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="control-label"> Department </label>
									</td>
									<td class="addUsers-value">
										<input type="text" class="inputwidth form-control input-md">
									</td>
									<td class="addUsers-label">
										<label class="control-label"> Other Email </label>
									</td>
									<td class="addUsers-value">
										<input type="text" class="inputwidth form-control input-md">
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="control-label"> Office Phone </label>
									</td>
									<td class="addUsers-value">
										<input type="text" class="inputwidth form-control input-md">
									</td>
									<td class="addUsers-label">
										<label class="control-label"> Secondary Email </label>
									</td>
									<td class="addUsers-value">
										<input type="text" class="inputwidth form-control input-md">
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="control-label"> Mobile Phone </label>
									</td>
									<td class="addUsers-value">
										<input type="text" class="inputwidth form-control input-md">
									</td>
									<td class="addUsers-label">
										<label class="control-label"> Reports To </label>
									</td>
									<td class="addUsers-value">
										<div class="input-group  inputwidth" id="main_note">
											<span class="transponame input-group-addon cursorPointer">
												<span class="glyphicon glyphicon-remove-circle cursorPointer text-info" type="button" title="Clear">
												</span>
											</span>
											<input class="form-control" type="text" value="" name="depotcode1" id="depotcode1">
											<span class="transearch input-group-addon cursorPointer" onclick="window.open('userReportTo.php', 'newwindow', 'width=1000, height=800'); return false;">
												<span class="searchtrans glyphicon glyphicon-search cursorPointer text-info" type="button" title="Select"></span>
											</span>
											<span class="transponame input-group-addon cursorPointer">
												<span class="glyphicon glyphicon-plus cursorPointer text-info" title="Create"></span>
											</span>
										</div>
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="control-label"> Home Phone </label>
									</td>
									<td class="addUsers-value">
										<input type="text" class="inputwidth form-control input-md">
									</td>
									<td class="addUsers-label">
										<label class="control-label"> Secondary Phone </label>
									</td>
									<td class="addUsers-value">
										<input type="text" class="inputwidth form-control input-md">
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="control-label"> Signature </label>
									</td>
									<td class="addUsers-value">
										<textarea class="form-control"></textarea>
										<!--<input type="text" class="inputwidth form-control input-md">-->
									</td>
									<td class="addUsers-label">
										<label class="control-label"> Documents </label>
									</td>
									<td class="addUsers-value">
										<textarea class="form-control"></textarea>
										<!--<input type="text" class="inputwidth form-control input-md">-->
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="control-label"> Internal Mail Composer </label>
									</td>
									<td class="addUsers-value">
										<input type="checkbox" name="">
									</td>
									<td class="addUsers-label">
										<label class="control-label"> Theme </label>
									</td>
									<td class="addUsers-value">
										<div class="chzn-container chzn-container-single">
											<select class="form-control inputwidth styled-select chosen-select">
												<option> Alphagrey </option>
												<option> Softed </option>
												<option> Bluelagoon </option>
												<option> Nature </option>
											</select>
										</div>
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="control-label"> Language </label>
									</td>
									<td class="addUsers-value">
										<div class="chzn-container chzn-container-single">
											<select class="form-control inputwidth styled-select chosen-select">
											<option> a </option>
											<option> b </option>
											<option> c </option>
											<option> d </option>
											<option> e </option>
											</select>
										</div>
									</td>
									<td class="addUsers-label">
										<label class="control-label"> CRM Phone Extension </label>
									</td>
									<td class="addUsers-value">
										<input type="text" class="inputwidth form-control input-md">
									</td>
								</tr>
								
								<tr>
									<td class="addUsers-label">
										<label class="control-label"> Default Record View </label>
									</td>
									<td class="addUsers-value">
										<div class="chzn-container chzn-container-single">
											<select class="form-control inputwidth styled-select chosen-select">
											<option> a </option>
											<option> b </option>
											<option> c </option>
											<option> d </option>
											<option> e </option>
											</select>
										</div>
									</td>
									<td class="addUsers-label">
										<label class="control-label"> Left Panel Hide </label>
									</td>
									<td class="addUsers-value">
										<input type="checkbox" name="">
									</td>
								</tr>
								
								<tr>
									<td class="addUsers-label">
										<label class="control-label"> Row Height </label>
									</td>
									<td class="addUsers-value">
										<div class="chzn-container chzn-container-single">
											<select class="form-control inputwidth styled-select chosen-select">
											<option> a </option>
											<option> b </option>
											<option> c </option>
											</select>
										</div>
									</td>
								</tr>
							</tbody>
						</table> <!-- more info table end -->

						<div class="customerForm-header">
							<strong> User Address </strong>
						</div>

						<table class="table table-bordered table-hover"> <!-- user address table start -->
							<tbody>
								<tr>
									<td class="addUsers-label">
										<label class="control-label"> Street Address </label>
									</td>
									<td class="addUsers-value">
										<textarea class="form-control"></textarea>
										<!--<input type="text" class="inputwidth form-control input-md">-->
									</td>
									<td class="addUsers-label">
										<label class="control-label"> Country </label>
									</td>
									<td class="addUsers-value">
										<input type="text" class="inputwidth form-control input-md">
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="control-label"> City </label>
									</td>
									<td class="addUsers-value">
										<input type="text" class="inputwidth form-control input-md">
									</td>
									<td class="addUsers-label">
										<label class="control-label"> Postal Code </label>
									</td>
									<td class="addUsers-value">
										<input type="text" class="inputwidth form-control input-md">
									</td>
								</tr>

								<tr>
									<td class="addUsers-label">
										<label class="control-label"> State </label>
									</td>
									<td class="addUsers-value">
										<input type="text" class="inputwidth form-control input-md">
									</td>
								</tr>
							</tbody>
						</table> <!-- user address table end -->

						<div class="customerForm-header">
							<strong> User Photograph </strong>
						</div>

						<table class="table table-bordered table-hover"> <!-- user photograph table start -->
							<tbody>
								<tr>
									<td class="addUsers-label">
										<label class="control-label"> Upload Photograph </label>
									</td>
									<td class="addUsers-value">
										<input type="file">
									</td>
								</tr>
							</tbody>
						</table> <!-- user photograph table end -->

						<div class="customerForm-header">
							<strong> Tag Cloud Display </strong>
						</div>

						<table class="table table-bordered table-hover"> <!-- tag cloud table start -->
							<tbody>
								<tr>
									<td class="addUsers-label">
										<label class="control-label"> Tag Cloud </label>
									</td>
									<td class="addUsers-value">
										<input type="checkbox" name="">
									</td>
									<td class="addUsers-value"></td>
									<td class="addUsers-value"></td>
								</tr>
							</tbody>
						</table> <!-- tag cloud table end -->
					</div>

					<div class="pull-right">
						<button type="submit" class="btn btn-success addgrpsave"><strong>Save</strong></button>
						<a class="pull-right addgrpcancel" href="#">Cancel</a>
					</div>
				</div> <!-- add user tables end -->
			
		</div> <!-- crm right side page end -->
	</div>
</div>