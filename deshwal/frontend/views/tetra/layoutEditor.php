<script src="../../css/bootstrap-3.3.6-dist/js/jquery-1.12.0.min.js"></script>
<script src="../../js/cookies.js"></script>
<script src="../../css/bootstrap-3.3.6-dist/js/bootstrap.min.js"></script>
<script src="../../js/jquery-ui.min.js"></script>
<script src="../../js/chosen.jquery.min.js"></script>

<link rel="stylesheet" id="mainstylesheet" type="text/css" href="../../css/reset.css" />
<link rel="stylesheet" id="mainstylesheet" type="text/css" href="../../css/normalize.css" />
<link rel="stylesheet" href="../../css/bootstrap-3.3.6-dist/css/bootstrap.min.css"/>
<link rel="stylesheet" id="mainstylesheet" type="text/css" href="../../css/chosen/chosen.css" />
<link rel="stylesheet" href="../../css/main.css"/>
		<script>
			$(document).ready(function(){
				
				$('.popupopen').click(function(){
					var popupHeading = $(this).prev('.labelNametochoose').text();
					$('.ui-dialog-title').text(popupHeading);
				});
				
				// to display chosen select on modal	
				$('#new-custom-block-modal, #new-custom-field-modal').on('shown.bs.modal', function () {
					$('.chosen-select', this).chosen('destroy').chosen();
				});
			
				// Active chosen select button
			
					$(".chosen-select").chosen();
					
	
		// Table sortable
				$("#detailview-layout").sortable(
					{ 
						cursor: "move"
					}
				);
				
		// table data sortable		
				$("#firstlist,#secondlist").sortable(
					{ 
						cursor: "move",
						containment: "#layoutEditor-table1",
						connectWith: "#secondlist,#firstlist"
					}
				);
				$("#thirdlist,#fourthlist").sortable(
					{ 
						cursor: "move",
						containment: "#layoutEditor-table2",
						connectWith: "#thirdlist,#fourthlist"
					}
				);
				$("#fifthlist,#sixthlist").sortable(
					{ 
						cursor: "move",
						containment: "#layoutEditor-table3",
						connectWith: "#fifthlist,#sixthlist"
					}
				);
		
		// relatedModulelist sortable
				$('.relatedModulelist').sortable({
					containment: '.relatedModulelist'
				});
		
		
			// Tab menus 
			$( "#tab" ).tabs();

			// Dropdown
			$('.dropdown-toggle').dropdown();
			
			// dilog box when clicked on edit icon
			$('.popupopen').click(function(){
				$("#layouteditoredit" ).dialog({
					
					position:{
						my:"right top", at: "left bottom", of: $(this)
					}
				});
			});
			// dilog cancel button
			$('#popupcancel-onedit').click(function(){
				$('#layouteditoredit').dialog('close');
			});
			
			// to display close icon on dialog
			
			$.fn.bootstrapBtn = $.fn.button.noConflict();
			
				
			});
			
			
		</script>

	<div class="container-fluid">
		<div class="row" style="background-color:#fafafb;" id="fullpage">
			<!-- Left side -->
			<?php include_once 'crmLeft1.php'?>
			<div class="col-sm-10" id="rightside-main">
				<div class="topcontent-details">	<!-- header part --->
					<div class="row">
						<div class="col-sm-6">
							<h1 class="page-heading">Fields and Layout Editor</h1>
						</div>
						
						<div class="col-sm-6">
							<div class="pull-right" style="margin-right:10px;">
								<select data-placeholder="Modules" class="chosen-select" style="width:220px;">
									<option value=""></option>
									<option>Customer</option>
									<option>Contact</option>
									<option>Depot</option>
									<option>Bank</option>
									<option>Product</option>
									<option>Recipt</option>
									<option>Transporter</option>
									<option>Supplier</option>
									<option>Order</option>
									<option>Invoice</option>
								</select>
							</div>
						</div> 
					</div>
				</div>
				<div id="tab">
					<div class="row" id="tabmenu">
						<div class="col-sm-12">
							<ul class="nav nav-tabs">
							  <li role="presentation" class="active"><a href="#detailview-layout" data-toggle="tabs">Detail View Layout</a></li>
							  <li role="presentation"><a href="#arrange-related-tabs" data-toggle="tabs">Arrange Related Tabs</a></li>
							</ul>
						</div>
					</div>
					<div class="row" style="padding-right:10px;">
						<div class="col-sm-6">
							<button type="button" class="btn btn-info" data-toggle="modal" data-target="#new-custom-block-modal">
								<span class="glyphicon glyphicon-plus"></span>
									Add Custom Block
							</button>
						</div>
						<div class="col-sm-6">
							<button type="button" class="btn btn-success radius-zero pull-right"> Save Field Sequence</button>
						</div>
					</div>
					<div id="detailview-layout">
						<div id="block_1">
							<div class="DetailView-recordDetails-header"> 
								<span class="dragg-img"><img src="../../images/drag.png"></img><strong> &nbsp;Opportunity Details</strong></span>
								<div class="pull-right">
									<button type="button" class="btn btn-info" data-toggle="modal" data-target="#new-custom-field-modal">
										<span class="glyphicon glyphicon-plus"></span>
											Add Custom Field
									</button>
									<span class="dropdown" style="display:inline-block">
										<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span></button>
										<ul class="dropdown-menu dropdown-menu-right">
											<li><a href="#">Always Show</a></li>
											<li><a href="#">Inactive Fields</a></li>
										</ul>
									</span>
								</div>
							</div>
							<table id="layoutEditor-table1" class="table table-bordered">
								<tbody>
									<tr>					
										<td>
											<ul id="firstlist">
												<li>
													<img src="../../images/drag.png"></img><span class="labelNametochoose">contacts</span>
													<span class="editbtn popupopen glyphicon glyphicon-pencil pull-right cursorPointer" title="Edit"></span>
												</li>
												<li>
													<img src="../../images/drag.png"></img><span class="labelNametochoose">Depot</span>
													<span class="editbtn popupopen glyphicon glyphicon-pencil pull-right cursorPointer" title="Edit"></span>
												</li>
												<li>
													<img src="../../images/drag.png"></img><span class="labelNametochoose">Invoice</span>
													<span class="editbtn popupopen glyphicon glyphicon-pencil pull-right cursorPointer" title="Edit"></span>
												</li>
												<li>
													<img src="../../images/drag.png"></img><span class="labelNametochoose">Order</span>
													<span class="editbtn popupopen glyphicon glyphicon-pencil pull-right cursorPointer" title="Edit"></span>
												</li>
												<li>
													<img src="../../images/drag.png"></img><span class="labelNametochoose">Opportunity</span>
													<span class="editbtn popupopen glyphicon glyphicon-pencil pull-right cursorPointer" title="Edit"></span>
												</li>
												<li>
													<img src="../../images/drag.png"></img><span class="labelNametochoose">Supplier</span>
													<span class="editbtn popupopen glyphicon glyphicon-pencil pull-right cursorPointer" title="Edit"></span>
												</li>
											</ul>
										</td>
										<td>
											<ul id="secondlist">
												<li>
													<img src="../../images/drag.png"></img><span class="labelNametochoose">Supplier</span>
													<span class="editbtn popupopen glyphicon glyphicon-pencil pull-right cursorPointer" title="Edit"></span>
												</li>
												<li>
													<img src="../../images/drag.png"></img><span class="labelNametochoose">Supplier</span>
													<span class="editbtn popupopen glyphicon glyphicon-pencil pull-right cursorPointer" title="Edit"></span>
												</li>
												<li>
													<img src="../../images/drag.png"></img><span class="labelNametochoose">Supplier</span>
													<span class="editbtn popupopen glyphicon glyphicon-pencil pull-right cursorPointer" title="Edit"></span>
												</li>
												<li>
													<img src="../../images/drag.png"></img><span class="labelNametochoose">Supplier</span>
													<span class="editbtn popupopen glyphicon glyphicon-pencil pull-right cursorPointer" title="Edit"></span>
												</li>
												<li>
													<img src="../../images/drag.png"></img><span class="labelNametochoose">Supplier</span>
													<span class="editbtn popupopen glyphicon glyphicon-pencil pull-right cursorPointer" title="Edit"></span>
												</li>
												<li>
													<img src="../../images/drag.png"></img><span class="labelNametochoose">Supplier</span>
													<span class="editbtn popupopen glyphicon glyphicon-pencil pull-right cursorPointer" title="Edit"></span>
												</li>
											</ul>
										</td>					
									</tr>
								</tbody>
							</table>
						</div>
						<div id="block_2">
							<table id="layoutEditor-table2" class="table table-bordered">
								<div class="DetailView-recordDetails-header"> 
									<span class="dragg-img"><img src="../../images/drag.png"></img><strong> &nbsp;Customer Information</strong></span>
									<div class="pull-right">
										<button type="button" class="btn btn-info" data-toggle="modal" data-target="#new-custom-field-modal">
											<span class="glyphicon glyphicon-plus"></span>
											Add Custom Field
										</button>
										<span class="dropdown" style="display:inline-block">
											<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span></button>
											<ul class="dropdown-menu dropdown-menu-right">
												<li><a href="#">Always Show</a></li>
												<li><a href="#">Inactive Fields</a></li>
											</ul>
										</span>
									</div>
								</div>
								<tbody>
									<tr>					
										<td>
											<ul id="thirdlist">
												<li>
													<img src="../../images/drag.png"></img><span class="labelNametochoose">Supplier</span>
													<span class="editbtn popupopen glyphicon glyphicon-pencil pull-right cursorPointer" title="Edit"></span>
													<span class="editbtn glyphicon glyphicon-trash pull-right cursorPointer" title="Delete" data-toggle="modal" data-target="#deleteAlert"></span>
												</li>
												<li>
													<img src="../../images/drag.png"></img><span class="labelNametochoose">Supplier</span>
													<span class="editbtn popupopen glyphicon glyphicon-pencil pull-right cursorPointer" title="Edit"></span>
													<span class="editbtn glyphicon glyphicon-trash pull-right cursorPointer" title="Delete" data-toggle="modal" data-target="#deleteAlert"></span>
												</li>
												<li>
													<img src="../../images/drag.png"></img><span class="labelNametochoose">Supplier</span>
													<span class="editbtn popupopen glyphicon glyphicon-pencil pull-right cursorPointer" title="Edit"></span>
													<span class="editbtn glyphicon glyphicon-trash pull-right cursorPointer" title="Delete" data-toggle="modal" data-target="#deleteAlert"></span>
												</li>
												<li>
													<img src="../../images/drag.png"></img><span class="labelNametochoose">Supplier</span>
													<span class="editbtn popupopen glyphicon glyphicon-pencil pull-right cursorPointer" title="Edit"></span>
													<span class="editbtn glyphicon glyphicon-trash pull-right cursorPointer" title="Delete" data-toggle="modal" data-target="#deleteAlert"></span>
												</li>
												<li>
													<img src="../../images/drag.png"></img><span class="labelNametochoose">Supplier</span>
													<span class="editbtn popupopen glyphicon glyphicon-pencil pull-right cursorPointer" title="Edit"></span>
													<span class="editbtn glyphicon glyphicon-trash pull-right cursorPointer" title="Delete" data-toggle="modal" data-target="#deleteAlert"></span>
												</li>
												<li>
													<img src="../../images/drag.png"></img><span class="labelNametochoose">Supplier</span>
													<span class="editbtn popupopen glyphicon glyphicon-pencil pull-right cursorPointer" title="Edit"></span>
													<span class="editbtn glyphicon glyphicon-trash pull-right cursorPointer" title="Delete" data-toggle="modal" data-target="#deleteAlert"></span>
												</li>
											</ul>
										</td>
										<td>
											<ul id="fourthlist">
												<li>
													<img src="../../images/drag.png"></img><span class="labelNametochoose">Supplier</span>
													<span class="editbtn popupopen glyphicon glyphicon-pencil pull-right cursorPointer" title="Edit"></span>
													<span class="editbtn glyphicon glyphicon-trash pull-right cursorPointer" title="Delete" data-toggle="modal" data-target="#deleteAlert"></span>
												</li>
												<li>
													<img src="../../images/drag.png"></img><span class="labelNametochoose">Supplier</span>
													<span class="editbtn popupopen glyphicon glyphicon-pencil pull-right cursorPointer" title="Edit"></span>
													<span class="editbtn glyphicon glyphicon-trash pull-right cursorPointer" title="Delete" data-toggle="modal" data-target="#deleteAlert"></span>
												</li>
												<li>
													<img src="../../images/drag.png"></img><span class="labelNametochoose">Supplier</span>
													<span class="editbtn popupopen glyphicon glyphicon-pencil pull-right cursorPointer" title="Edit"></span>
													<span class="editbtn glyphicon glyphicon-trash pull-right cursorPointer" title="Delete" data-toggle="modal" data-target="#deleteAlert"></span>
												</li>
												<li>
													<img src="../../images/drag.png"></img><span class="labelNametochoose">Supplier</span>
													<span class="editbtn popupopen glyphicon glyphicon-pencil pull-right cursorPointer" title="Edit"></span>
													<span class="editbtn glyphicon glyphicon-trash pull-right cursorPointer" title="Delete" data-toggle="modal" data-target="#deleteAlert"></span>
												</li>
												<li>
													<img src="../../images/drag.png"></img><span class="labelNametochoose">Supplier</span>
													<span class="editbtn popupopen glyphicon glyphicon-pencil pull-right cursorPointer" title="Edit"></span>
													<span class="editbtn glyphicon glyphicon-trash pull-right cursorPointer" title="Delete" data-toggle="modal" data-target="#deleteAlert"></span>
												</li>
												<li>
													<img src="../../images/drag.png"></img><span class="labelNametochoose">Supplier</span>
													<span class="editbtn popupopen glyphicon glyphicon-pencil pull-right cursorPointer" title="Edit"></span>
													<span class="editbtn glyphicon glyphicon-trash pull-right cursorPointer" title="Delete" data-toggle="modal" data-target="#deleteAlert"></span>
												</li>
											</ul>
										</td>					
									</tr>
								</tbody>
							</table>
						</div>
						<div id="block_3">
							<table id="layoutEditor-table3" class="table table-bordered">
								<div class="DetailView-recordDetails-header"> 
									<span class="dragg-img"><img src="../../images/drag.png"></img><strong> &nbsp;Description Details</strong></span>
									<div class="pull-right">
										<button type="button" class="btn btn-info" data-toggle="modal" data-target="#new-custom-field-modal">
											<span class="glyphicon glyphicon-plus"></span>
											Add Custom Field
										</button>
										<span class="dropdown" style="display:inline-block">
											<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span></button>
											<ul class="dropdown-menu dropdown-menu-right">
												<li><a href="#">Always Show</a></li>
												<li><a href="#">Inactive Fields</a></li>
											</ul>
										</span>
									</div>
								</div>
								<tbody>
									<tr>					
										<td>
											<ul id="fifthlist">
												<li>
													<img src="../../images/drag.png"></img><span class="labelNametochoose">Supplier</span>
													<span class="editbtn popupopen glyphicon glyphicon-pencil pull-right cursorPointer" title="Edit"></span>
													<span class="editbtn glyphicon glyphicon-trash pull-right cursorPointer" title="Delete" data-toggle="modal" data-target="#deleteAlert"></span>
												</li>
											</ul>
										</td>
										<td>
											<ul id="sixthlist">
												<li>
													<img src="../../images/drag.png"></img><span class="labelNametochoose">Supplier</span>
													<span class="editbtn popupopen glyphicon glyphicon-pencil pull-right cursorPointer" title="Edit"></span>
													<span class="editbtn glyphicon glyphicon-trash pull-right cursorPointer" title="Delete" data-toggle="modal" data-target="#deleteAlert"></span>
												</li>
												<li>
													<img src="../../images/drag.png"></img><span class="labelNametochoose">Supplier</span>
													<span class="editbtn popupopen glyphicon glyphicon-pencil pull-right cursorPointer" title="Edit"></span>
													<span class="editbtn glyphicon glyphicon-trash pull-right cursorPointer" title="Delete" data-toggle="modal" data-target="#deleteAlert"></span>
												</li>
											</ul>
										</td>					
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div id="arrange-related-tabs">
						<div class="row">
							<div class="col-sm-2">
								Arrange Related List
							</div>
							<div class="col-sm-10">
								<div class="row">
									<div class="col-sm-5">
										<ul class="relatedModulelist">
											<li class="alert">
												<img src="../../images/drag.png"></img>
												<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
												Quotes
											</li>
											<li class="alert">
												<img src="../../images/drag.png"></img>
												<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
												Contacts
											</li>
											<li class="alert">
												<img src="../../images/drag.png"></img>
												<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
												Services
											</li>
											<li class="alert">
												<img src="../../images/drag.png"></img>
												<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
												Products
											</li>
											<li class="alert">
												<img src="../../images/drag.png"></img>
												<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
												Documents
											</li>
											<li class="alert">
												<img src="../../images/drag.png"></img>
												<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
												Activities
											</li>
										</ul>
									</div>
									<div class="col-sm-7">
										<ul>
											<li><span class="circle"><span class="glyphicon glyphicon-info-sign"> </span> Drag and drop the module to reorder the list.</span></li><br />
											<li><span class="circle"><span class="glyphicon glyphicon-info-sign"> </span> Click on the close icon to remove the module from the list.</span></li><br />
											<li><span class="circle"><span class="glyphicon glyphicon-info-sign"> </span> Select the module from the removed modules to add back to list</span></li><br />
										</ul>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-3">Select Module to Add</div>
							<div class="col-sm-9">
								<div class="row">
									<div class="col-sm-5">
										<form name="" role="form" action="" method="">
											<input type="text" class="form-control inputwidth" placeholder="Select Module.."/>
										</form>
									</div>
									<div class="col-sm-7"><button type="button" class="btn btn-success">Save</button></div>
								</div>
							</div>
						</div>
					</div>
				</div>



				<!-- Modal to open dialog on Edit-->
			
				<div id="layouteditoredit" title="Name" style="display:none;">
					<div>
						<form class="form form-horizontal margintop0" action="" method="">
							<div class="checkbox">
								<label><input type="checkbox" name="mandatory" checked="" readonly=""> &nbsp; Mandatory Field </label>
							</div>
								<div class="checkbox">
									<label><input type="checkbox" name="mandatory" checked="" readonly=""> &nbsp; Active </label>
								</div>
								<div class="checkbox">
									<label><input type="checkbox" name="mandatory" checked="" readonly=""> &nbsp; Quick Create </label>
								</div>
								<div class="checkbox">
									<label><input type="checkbox" name="mandatory" checked="" readonly=""> &nbsp; Summary View </label>
								</div>
								<div class="checkbox">
									<label><input type="checkbox" name="mandatory" checked="" readonly=""> &nbsp; Mass Edit </label>
								</div>
								<div class="checkbox">
									<label><input type="checkbox" name="mandatory" checked="" readonly=""> &nbsp; Default Value </label>
								</div>
						</form>

						<div class="dialog-footer">
							<div class="pull-right">
								<button type="button" class="btn btn-success addgrpsave"> Save </button>
								<a href="#" class="addgrpcancel" id="popupcancel-onedit"> Cancel </a>
							</div>
						</div>
					</div>
				</div>
				<!-- Model End -->
				
				<!-- modal to create custom fields -->
				<div class="modal fade" id="new-custom-field-modal"><!-- modal starts -->
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<span class="page-heading">Create Custom Field</span>
								<button type="button" class="close pull-right" data-dismiss="modal">&times;</button>
							</div>
							<div class="cutom-field-modal">
								<form class="form  form-horizontal" id="create-customField-form" role="form" name="" method="" action="">
									<div class="form-group">
										<label for="fieldType" class="col-sm-3">Select Field Type</label>
										<div class="col-sm-9">
											<select class="chosen-select" style="width:385px;">
												<option>Choose Field Type</option>
												<option>Text</option>
												<option>Integer</option>
												<option>Decimal</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="labelName" class="col-sm-3">Label Name</label>
										<div class="col-sm-9">
											<input type="text" class="form-control col-sm-8" placeholder="Enter Label Name">
										</div>
									</div>
									<div class="form-group">
										<label for="taxValue" class="col-sm-3">Length</label>
										<div class="col-sm-9">	
											<input type="text" class="form-control" placeholder="Enter Length">
										</div>
									</div>
								</form>
							</div>
							<div class="modal-footer">
								<div class="pull-right">
									<button type="button" class="btn btn-success">Save</button>
									<a href="#" data-dismiss="modal">Cancel</a>
								</div>
							</div>
						</div>
					</div>
				</div>  <!-- modal ends -->
				<!-- modal to create custom Block -->
				<div class="modal fade" id="new-custom-block-modal"><!-- modal starts -->
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<span class="page-heading">Add Custom Block</span>
								<button type="button" class="close pull-right" data-dismiss="modal">&times;</button>
							</div>
							<div class="cutom-field-modal">
								<form class="form  form-horizontal" id="create-customField-form" role="form" name="" method="" action="">
									<div class="form-group">
										<label for="blockName" class="col-sm-3">Block Name</label>
										<div class="col-sm-9">
											<input type="text" class="form-control col-sm-8" placeholder="Enter Block Name">
										</div>
									</div>
									<div class="form-group">
										<label for="addAfter" class="col-sm-3">Add After</label>
										<div class="col-sm-9">
											<select class="chosen-select" style="width:390px;">
												<option>Add After</option>
												<option>Opportunity</option>
												<option>Opportunity</option>
												<option>Opportunity</option>
											</select>
										</div>
									</div>
								</form>
							</div>
							<div class="modal-footer">
								<div class="pull-right">
									<button type="button" class="btn btn-success">Save</button>
									<a href="#" data-dismiss="modal">Cancel</a>
								</div>
							</div>
						</div>
					</div>
				</div>  <!-- modal ends -->
				<div class="modal fade" id="deleteAlert">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<strong> Are You Sure That You Want To Delete ?</strong>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-warning"> Yes</button>
								<button type="button" class="btn btn-success" data-dismiss="modal"> No</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

