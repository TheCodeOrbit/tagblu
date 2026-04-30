<?php $url = Yii::app()->getBaseUrl(true);
	$baseurl = Yii::app()->request->hostInfo.Yii::app()->homeUrl;
	$this->pageTitle=Yii::app()->name . '-picklistEditor';
	$this->breadcrumbs=array(
		'picklistEditor',
	);
?>

<?php if(Yii::app()->user->hasFlash('picklistEditor')): ?>

<div class="flash-success">
	<?php echo Yii::app()->user->getFlash('picklistEditor'); ?>
</div>
<?php else: ?>

<div class="form">
<?php
	$form = $this->beginWidget('CActiveForm', array(
		'id'=>'form',  //form-id
		'enableAjaxValidation'=>true,
		'clientOptions'=>array(
			'validateOnSubmit'=>true,
		),
	));
?>

<?php echo CHtml::errorSummary($model); ?>

<div class="row" id="fullpage">
	<div class="col-sm-12 rightside-page" id="rightside-detail">
		<div class="row setting-header">
			<h4 class="h4 page-heading">Picklist Editor</h4>
		</div><hr>

		<div class="listViewContents">
			<div class="row">
				<label class="col-sm-2" for="Select Module">Select Module</label>
				<div class="col-sm-6 tab_id">
					<select class="chosen-select select-chosen" name="Tab_tabid" id="Tab_tabid" >
						    <option value="-1">Select</option>
						<?php foreach($tabs as $tab) { ?>
						<option value="<?php echo $tab['tabid'] ?>"><?php echo htmlentities($tab['tablable']) ?></option>
                                                
						<?php } ?>
					</select>
				</div>
				<div class="col-sm-4"></div>
			</div>
			<br>

			<div class="row">
				<label class="col-sm-2" for="Select Picklist">Select Picklist in Contacts</label>
				<div class="col-sm-6 tab_id">
					<select id="ropDownCars" data-placeholder="Select Role Name" class="chosen-select select-chosen">
						<option value=''>select picklist</option>
					</select>
				</div>
				<div class="col-sm-4"></div>
			</div>
			<br>

			<div id="tab">
				<div class="row" id="tabmenu">
					<div class="col-sm-12">
						<ul class="nav nav-tabs">
							<li role="presentation" class="active"><a href="#allvalues-tab" data-toggle="tabs">All Values</a></li>
						</ul>
					</div>
				</div><br>
				<div id="allvalues-tab">
					<div class="row" >
						<div class="col-sm-6">
							<table class="table table-bordered table-striped">
								<thead>
									<tr class="setting-table-header">
										<th>Type Values</th>
									</tr>
								</thead>
								<tbody id="firstlist">
								
								</tbody>
							</table>
						</div>
						<div class="col-sm-6">
							<div class="row">
								<div class="col-sm-5">
									<input type="button" class="btn picklist-btn" data-toggle="modal" data-target="#assign-value" value="Add Value"/><br />
									<input type="button" class="btn picklist-btn" id="rename_val" data-toggle="modal" data-target="#rename-value" value="Rename Value"/><br />
									<input type="button" class="btn picklist-btn" id="delete_val" data-toggle="modal" data-target="#delete-value" value="Delete Value"/><br />
									<!--<input type="button" class="btn bottomsavebtn picklist-btn" value="Save Order"/><br />-->
								</div>
								<!--
								<div class="col-sm-7">
									<ul>
										<li><span class="circle"><span class="glyphicon glyphicon-info-sign"> </span> Drag items to reposition them</span></li><br />
										<li><span class="circle"><span class="glyphicon glyphicon-info-sign"> </span> Select an item to rename or delete</span></li><br />
										<li><span class="circle"><span class="glyphicon glyphicon-info-sign"> </span> 
										To Delete multiple items hold Ctrl key down while selecting items</span></li><br />
									</ul>
								</div>-->
							</div>
						</div>
					</div>
				</div>

				<!-- modal for assign value and add value -->
				<div class="modal fade" id="assign-value">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title">Assign Values to Roles</h4>
							</div>
							<div class="modal-body">
								<form class="form form-horizontal" action="" name="" role="form">
									<div class="form-group">
										<label class="col-sm-4 text-right" for="newName">Item Value</label>
										<div class="col-sm-7">
											<input type="text" class="form-control" id="item_value">
										</div>
									</div>
								</form>
							</div>
							<div class="modal-footer">
								<div class="pull-right">
									<a class="btn bottomsavebtn" id="addvaluesubmit">Save</a>
									<a href="#" data-dismiss="modal" class="btn addgrpcancel" id="canceladditem">Cancel</a>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- modal for rename value -->
				<div class="modal fade" id="rename-value">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title">Rename PickList Item</h4>
							</div>
							<div class="modal-body">
								<form method="POST" enctype="multipart/form-data" class="form form-horizontal" action="" name="" role="form">
									<div class="form-group">
										<label for="renameItem" class="col-sm-4 text-right">Item to Rename</label>
										<div class="col-sm-7 tab_idone">
											<select id="sam" class="chosen-select modal-chosen" data-placeholder="Select Role Name"></select>
										</div>
									</div>
									<!--
									<div class="form-group">
										<label for="renameItem" class="col-sm-4">Add After</label>
										<div class="col-sm-7">
											<select id="addafter" class="chosen-select modal-chosen" data-placeholder="Add After"></select>
										</div>
									</div>
									-->
									<div class="form-group">
							<label class="col-sm-4 text-right" for="newName">Enter New Name</label>
										<div class="col-sm-7">
								<input type="text" id="newName" class="form-control">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 text-right" for="Active">Active</label>
										<div class="col-sm-7 tab_idone">
											<select id="active" class="chosen-select modal-chosen">
												<option value="1">Yes</option>
												<option value="0">No</option>
											</select>
										</div>
									</div>
								</form>
							</div>
							<div class="modal-footer">
								<div class="pull-right">
									<a class="btn bottomsavebtn" id="editvaluesubmit">Save</a>
									<a href="#" data-dismiss="modal" class="btn addgrpcancel" id="canceledititem">Cancel</a>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- modal for delete value -->
				<div class="modal fade" id="delete-value">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title">Delete PickList Items</h4>
							</div>
							<div class="modal-body">
								<form class="form form-horizontal" action="" name="" role="form">
									<div class="form-group">
										<label for="deleteItem" class="col-sm-4 text-right">Items to Delete</label>
										<div class="col-sm-7 tab_idone">
											<select id="sam_delete" class="chosen-select modal-chosen" data-placeholder="Select Role Name"></select>
										</div>
									</div>
									<!--<div class="form-group">
										<label class="col-sm-4" for="newName">Replace it with</label>
										<div class="col-sm-7">
											<select class="chosen-select modal-chosen">
												<option>1</option>
												<option>2</option>
												<option>3</option>
												<option>4</option>
											</select>
										</div>
									</div>
									-->
								</form>
							</div>
							<div class="modal-footer">
								<div class="pull-right">
									<button type="button" class="btn bottomsavebtn" id="deletevaluesubmit">Delete</button>
									<a href="#" data-dismiss="modal" class="btn addgrpcancel">Cancel</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php $this->endWidget(); ?>
</div><!-- form -->
<?php endif; ?>

<script>
function getList (yan,fill){
	var geturl= '<?php echo  Yii::app()->request->hostInfo.Yii::app()->homeUrl; ?>';
	var url=geturl+'/Pick/Pass';
	//alert("url="+url+" and yan="+yan+" and fill="+fill);
   	$.ajax({
        type:'POST',
		url:url,
        data:{ yan:yan,fill:fill},
		success:function(data){ 
			data = data.replace(/script/g,"");
			if(fill=="ropDownCars"){
				$('#firstlist').empty();        
         //alert(data); 

	$('#firstlist').append(data);
			}
if(fill=="delete_val"){
				     
            	$('#sam_delete').html(data);
              $('#deletevalid').html(data);
			}else{
				$('#sam').html(data);
				$('#addafter').html(data);
			}
		},
	});
}
	$(document).ready(function(){

$('#ropDownCars').css("pointer-events", "none");
$('#ropDownCars').trigger('chosen:updated');

	var modulename=$("#Tab_tabid option:last").val();
	    $("#Tab_tabid option[value='"+modulename+"']").attr("selected","selected");
				$('#Tab_tabid').trigger("chosen:updated");

		var field_id =   $('#Tab_tabid').attr('id');
		if(field_id == 'Tab_tabid'){
			var sam = $('#Tab_tabid').val();
			var geturl= '<?php echo  Yii::app()->request->hostInfo.Yii::app()->homeUrl; ?>';
			var url=geturl+'/Pick/Jass?sam='+sam;
			$.ajax({                                            
				type:'POST',
				url:url,
				data:{ sam:sam},
				success:function(data){ 
					console.log(data);
					$('#ropDownCars')
						.find('option')
						.remove()
						.end()
						.append(''+data+'');
					$('#ropDownCars').trigger('chosen:updated');
 var tabledropdown='364';
 $("#ropDownCars option[value='364']").attr("selected","selected");

$('#ropDownCars option[value="565"]').remove();
$('#ropDownCars option[value="380"]').remove();
$('#ropDownCars option[value="378"]').remove();
$('#ropDownCars option[value="377"]').remove();


$('#ropDownCars').css("pointer-events", "none");
$('#ropDownCars').trigger("chosen:updated");


   var fill =   $('#ropDownCars').attr('id');
			  var yan =tabledropdown;
				getList(yan,fill);


				},

			});
		}

		// to display chosen select on modal
		$('#assign-value,#rename-value,#delete-value').on('shown.bs.modal', function () {
			$('.chosen-select', this).chosen('destroy').chosen();
		});
		// Active chosen select button
		$(".chosen-select").chosen();

	});	
</script>

<script>
	$(document.body).on('change', '#Tab_tabid' ,function(){
		var field_id =   $(this).attr('id');
		if(field_id == 'Tab_tabid'){
			var sam = $('#Tab_tabid').val();
			var geturl= '<?php echo  Yii::app()->request->hostInfo.Yii::app()->homeUrl; ?>';
			var url=geturl+'/Pick/Jass?sam='+sam;

			$.ajax({

				type:'POST',
				url:url,
				data:{ sam:sam},
				success:function(data){ 
					console.log(data);
					$('#ropDownCars')
						.find('option')
						.remove()
						.end()
						.append(''+data+'');
					$('#ropDownCars').trigger('chosen:updated');
				},

			});
		}
	});

//------------------------------------selectct second drop dwon all fields show-----------------

	$(document.body).on('change', '#ropDownCars' ,function(){
		      var fill =   $(this).attr('id');
			  var yan = $('#ropDownCars').val();
		//alert("yan="+yan + " and fill="+fill);
				getList(yan,fill);

				});

	$(document.body).on('click', '#rename_val' ,function(){
		      var fill =   $(this).attr('id');
			  var yan = $('#ropDownCars').val();
				getList(yan,fill);

	});

	$(document.body).on('click', '#delete_val' ,function(){

		      var fill =   $(this).attr('id');

			  var yan = $('#ropDownCars').val();
				getList(yan,fill);

	});

	$(document.body).on('click', '#addvaluesubmit' ,function(){
		      var item_value   =   $("#item_value").val();
		      var module    =   $("#Tab_tabid").val();
		      var picklist  =   $("#ropDownCars").val();
		      var action    =   "Add";
                      var item_value_regEx = /[^a-zA-Z\d\s-]+$/
					
					if(item_value_regEx.test(item_value)){
						alert("This is not an allowed Item Value.");
return false;						
//e.preventDefault();
					}
		      if(item_value==""){
		          alert("Please Fill the Blank");
		          return false;
              }
			  if(item_value != ''){
                           
				var geturl= '<?php echo  Yii::app()->request->hostInfo.Yii::app()->homeUrl; ?>';
				var url=geturl+'/Pick/AddItem';
				
		       	$.ajax({
		            type:'POST',
					url:url,
		            data:{ item_value:item_value,module:module,picklist:picklist,action:action},
					success:function(data){ 
           
						//alert(data);
				$('#firstlist').append("<tr id='firstlist'><td>"+item_value+"</td></tr>");                                        
						$('#sam').append("<option value='"+item_value+"'>"+item_value+"</option>");
						$("#canceladditem").click();
						window.location.reload();
						
					},

				});
			 }
	});
	
	
	$(document.body).on('click', '#editvaluesubmit' ,function(){
                      
		      var module    =   $("#Tab_tabid").val();
		      var picklist  =   $("#ropDownCars").val();
		      var active  =   $("#active").val();
		      var prev_value   =   $("#sam").val();
		      //var addafter    =   $("#addafter").val();
		      var addafter    =   "";
		      var newName  =   $("#newName").val();
                    var item_value_regEx = /[^a-zA-Z\d\s-]+$/
					
					if(item_value_regEx.test(newName)){
						alert("This is not an allowed Enter New Name.");
                                                            return false;						
					}
                     
		      var action   = "Edit";
		      
		      if(prev_value == '-1'){
		      		alert("Please Select Rename Item");
		      		return false;
		      }
		      if(newName == ''){
		      		alert("Please Select New Name");
		      		return false;
		      }
		     
				var geturl= '<?php echo  Yii::app()->request->hostInfo.Yii::app()->homeUrl; ?>';
				var url=geturl+'/Pick/AddItem';
				
		       	$.ajax({
		            type:'POST',
					url:url,
		            data:{item_value:newName,module:module,picklist:picklist,prev_value:prev_value,addafter:addafter,action:action,active:active},
					success:function(data){ 
                                        
                                        //alert(data);
                                   
						console.log(data);
						//$('#firstlist').append("<ul id='firstlist'><li><img src='../../images/drag.png'>"+item_value+"</li></ul>");
						//$('#sam').append("<option value='"+item_value+"'>"+item_value+"</option>");
						//$("#canceledititem").click();
						window.location.reload();
						
					},

				});
			 
	});


	$(document.body).on('click', '#deletevaluesubmit' ,function(){
//alert(item_value);
		      var module    =   $("#Tab_tabid").val();
		      var picklist  =   $("#ropDownCars").val();
		      var prev_value   =   $("#sam_delete").val();
		      //var addafter    =   $("#addafter").val();

		      var deletevalid    =   "";
		   
		      var action   = "Delete";
		      
		      if(prev_value == '-1'){
		      		alert("Please Select Rename Item");
		      		return false;
		      }


				var geturl= '<?php echo  Yii::app()->request->hostInfo.Yii::app()->homeUrl; ?>';
				var url=geturl+'/Pick/DeleteItem';
			
		       	$.ajax({
		            type:'POST',
					url:url,
		            data:{module:module,picklist:picklist,prev_value:prev_value,deletevalid:deletevalid,action:action},
					success:function(data){ 
						//console.log(data);
						//$('#firstlist').append("<ul id='firstlist'><li><img src='../../images/drag.png'>"+item_value+"</li></ul>");
						//$('#sam').append("<option value='"+item_value+"'>"+item_value+"</option>");
						//$("#canceledititem").click();
						window.location.reload();
						
					},

				});


	
			 
	});
</script>
