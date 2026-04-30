<style>
    :root {
        --space-navigation-image: 0px;
    }

    .header-image {
        position: absolute;
        left: -9999px;
    }

	.body-container {
		margin: 0 2rem;
	}

    .body-outline {
        margin: 0;
    }

    .table tbody tr td,
    input[type="checkbox"] {
        vertical-align: middle;
    }

    .simple-table  {
        margin: 0 0 1rem 0;
    }

    .simple-table__container {
        width: 100%;
        display: flex;
        justify-content: space-evenly;
        padding: 0.5rem;
        gap:4rem;
    }

    .gap-1rem {
        gap: 1rem;
    }

    .input-border{
        text-align: left;
    }

    td:first-child {
        padding-left: 2rem;
    }
</style>

<?php $url = Yii::app()->getBaseUrl(true);
$baseurl = Yii::app()->request->hostInfo.Yii::app()->homeUrl;
$this->pageTitle=Yii::app()->name . '-picklistEditor';
$this->breadcrumbs=array(
'picklistEditor',
);
?>
<div>
    <div class="body-menu d-flex justify-content-between">
        <div>
            <h1 class="body-heading">Picklist Editor</h1>
        </div>
    </div>
    <div class="body-container d-flex flex-col justify-content-center align-items-center"><!-- Main List Body div-->
        <div class="simple-table d-flex justify-content-between">
			<div class="simple-table__container" style="">
				<div class="d-flex justify-content-between gap-1rem">
					<div class="input-heading ">
						<label>Module</label>
					</div>
                    <select name="Tab_tabid" id="Tab_tabid" class="header-elements">
                        <option value="-1">Select</option>
                        <?php foreach($tabs as $tab) { ?>
                        <option value="<?php echo htmlentities($tab['tabid']) ?>"><?php echo $tab['tablable'] ?></option>

                        <?php } ?>
                    </select>
					<div class="ajxwarning errorMessage error-container" id="" style="display:none">
					</div>
				</div>
				<div class="d-flex justify-content-between gap-1rem">
                    <div class="input-heading">
                        <label for="">Picklist in Contacts</label>
					</div>
                    <select name="picklist" id="ropDownCars" class="header-elements">
                        <option value="default">Select</option>
                        <option value="1">User</option>
                    </select>
					<div class="ajxwarning errorMessage error-container" id="" style="display:none">
					</div>
				</div>
			</div>
		</div>
        <div class="body-outline general-list height-full-body" >
            <div data-simplebar class="adjusted-height" id="allvalues-tab">
                <table class="table-view table table-striped">
                    <thead>
                        <tr class="table-primary">
                            <th>Type values</th>
                            <!-- <th style="width:100px;">Actions</th> -->
                        </tr>
                    </thead>
                    <tbody id="firstlist">
                        <!-- <tr>
                            <td>2016-2017</td>
                            <td>
                                
                            </td>
                        </tr> -->
                    </tbody>
                </table>
            </div>
            <!-- <input type="button" class="btn picklist-btn" data-toggle="modal" data-target="#assign-value" value="Add Value"/><br /> -->

            <div class="seprator"></div>
            <div class="body-footer d-flex justify-content-evenly align-items-center mx-3">
                <div class="action-icon-container add-btn list-view-add-btn btn-primary d-flex justify-content-center align-items-center position-static" data-toggle="modal" data-target="#assign-value">
                    <div class="action-icon-container-label">
                        <span>Add</span>
                    </div>
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentcolor">
                        <path d="M6 16H10V10H16V6H10V0H6V6H0V10H6V16Z" fill="#ffffff"></path>
                    </svg> 
                </div>
                <div class="action-icon-container list-view-add-btn btn-primary d-flex justify-content-center align-items-center position-static" id="rename_val" data-toggle="modal" data-target="#rename-value">
                    <div class="action-icon-container-label">
                        <span>Rename</span>
                    </div>
                    <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="action-icon">
                        <path d="M17.1102 5.40807L19.2322 7.52507L17.1102 5.40807ZM18.4748 3.54307L12.7368 9.27007C12.4404 9.56557 12.2382 9.94205 12.1557 10.3521L11.6257 13.0001L14.2788 12.4701C14.6896 12.3881 15.0663 12.1871 15.3628 11.8911L21.1008 6.16407C21.2732 5.99197 21.41 5.78766 21.5033 5.56281C21.5966 5.33795 21.6446 5.09695 21.6446 4.85357C21.6446 4.61019 21.5966 4.36919 21.5033 4.14433C21.41 3.91948 21.2732 3.71517 21.1008 3.54307C20.9284 3.37097 20.7237 3.23446 20.4984 3.14132C20.2731 3.04818 20.0316 3.00024 19.7878 3.00024C19.5439 3.00024 19.3025 3.04818 19.0772 3.14132C18.8519 3.23446 18.6472 3.37097 18.4748 3.54307V3.54307Z" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M19.6409 15V18C19.6409 18.5304 19.4298 19.0391 19.054 19.4142C18.6782 19.7893 18.1686 20 17.6371 20H6.61612C6.08468 20 5.575 19.7893 5.19921 19.4142C4.82342 19.0391 4.6123 18.5304 4.6123 18V7C4.6123 6.46957 4.82342 5.96086 5.19921 5.58579C5.575 5.21071 6.08468 5 6.61612 5H9.62185" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </div>
                <div class="action-icon-container list-view-add-btn btn-primary d-flex justify-content-center align-items-center position-static" id="delete_val" data-toggle="modal" data-target="#delete-value">
                    <div class="action-icon-container-label">
                        <span>Delete</span>
                    </div>
                    <svg viewBox="0 0 18 19" class="action-icon">
                        <path d="M5.14414 15.2656H12.8539L13.2793 6.26562H4.71875L5.14414 15.2656Z" fill="#ffffff"></path>
                        <path d="M15.1875 5H12.9375V3.59375C12.9375 2.97324 12.433 2.46875 11.8125 2.46875H6.1875C5.56699 2.46875 5.0625 2.97324 5.0625 3.59375V5H2.8125C2.50137 5 2.25 5.25137 2.25 5.5625V6.125C2.25 6.20234 2.31328 6.26562 2.39062 6.26562H3.45234L3.88652 15.459C3.91465 16.0584 4.41035 16.5312 5.00977 16.5312H12.9902C13.5914 16.5312 14.0854 16.0602 14.1135 15.459L14.5477 6.26562H15.6094C15.6867 6.26562 15.75 6.20234 15.75 6.125V5.5625C15.75 5.25137 15.4986 5 15.1875 5ZM6.32812 3.73438H11.6719V5H6.32812V3.73438ZM12.8549 15.2656H5.14512L4.71973 6.26562H13.2803L12.8549 15.2656Z" fill="#ffffff"></path>
                    </svg>
                </div>
            </div>
        </div>
	</div>
</div>

<!-- modal for assign value and add value -->
<div class="modal fade" id="assign-value" aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">
            <div class="modal-header jc-cntr">
				<h5 class="modal-title text-center body-heading" id="exampleModalLabel">Edit Picklist Item</h5>
			</div>
<div class="modal-body">
<form class="form form-horizontal" action="" name="" role="form">
<div class="form-group">
<div class="input-heading ">
						<label for="newName">Item Value</label>
					</div>
<!-- <label class="col-sm-4 text-right" for="newName">Item Value</label> -->
<div class="col-sm-7">
<input type="text" class="form-control input-border" id="item_value">
</div>
</div>
</form>
</div>
<div class="modal-footer">
<div class="pull-right">
<a class="btn bottomsavebtn btn-primary input-save me-5 close" id="addvaluesubmit">Save</a>
<a href="#" data-dismiss="modal" class="btn addgrpcancel btn-danger input-save" id="canceladditem">Cancel</a>
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



















<!-- Trigger Classes for Modal : data-bs-toggle="modal" data-bs-target="#exampleModal" -->
<div class="modal fade" id="assign-value1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <!-- Todo: Onclick of Submit Show this alert -->
	<div class="alert alert-success alert-dismissible fade hide" role="alert">
		Data Saved Successfully
	</div>
	<div class="modal-dialog modal-dialog-centered modal-xl">
		<div class="modal-content">
			<div class="modal-header jc-cntr">
				<h5 class="modal-title text-center body-heading" id="exampleModalLabel">Edit Picklist Item</h5>
			</div>
			<div class="modal-body">
				<!-- put the contents here. Items like Item to Rename(input only) Enter new Name, Active -->
			</div>
			<div class="modal-footer jc-cntr">
				<input class="btn btn-primary input-save me-5 close" type="submit" name="yt0" value="Submit">	
				<button type="button" class="btn btn-danger input-save" data-bs-dismiss="modal">Discard</button>
			</div>
		</div>
	</div>
</div>






<script>
function getList (yan,fill){
var geturl= '<?php echo  Yii::app()->request->hostInfo.Yii::app()->homeUrl; ?>';
var url=geturl+'/Pick/Pass';
$.ajax({
type:'POST',
url:url,
data:{ yan:yan,fill:fill},
success:function(data){ 
	data = data.replace(/script/g,"");
if(fill=="ropDownCars"){
$('#firstlist').empty();            
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

//$('#ropDownCars').css("pointer-events", "none");
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


//$('#ropDownCars').css("pointer-events", "none");
$('#ropDownCars').trigger("chosen:updated");


var fill =   $('#ropDownCars').attr('id');
var yan =tabledropdown;
getList(yan,fill);


},

});
}

$('#assign-value,#rename-value,#delete-value').on('shown.bs.modal', function () {
$('.chosen-select', this).chosen('destroy').chosen();
});
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

/**** selectct second drop dwon all fields show ****/

$(document.body).on('change', '#ropDownCars' ,function(){
var fill =   $(this).attr('id');
var yan = $('#ropDownCars').val();
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
var item_value_regEx = /[^a-zA-Z\d\s-]+$/;
					
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
$('#firstlist').append("<tr id='firstlist'><td>"+item_value+"</td></tr>");                                        
$('#sam').append("<option value='"+item_value+"'>"+item_value+"</option>");
$("#canceladditem").click();
//window.location.reload();

},

});
}
});


$(document.body).on('click', '#editvaluesubmit' ,function(){

var module    =   $("#Tab_tabid").val();
var picklist  =   $("#ropDownCars").val();
var active  =   $("#active").val();
var prev_value   =   $("#sam").val();
var addafter    =   "";
var newName  =   $("#newName").val();
var item_value_regEx = /[^a-zA-Z\d\s-]+$/;
					
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
console.log(data);
window.location.reload();

},

});

});


$(document.body).on('click', '#deletevaluesubmit' ,function(){
var module    =   $("#Tab_tabid").val();
var picklist  =   $("#ropDownCars").val();
var prev_value   =   $("#sam_delete").val();
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
window.location.reload();
},
});
});
</script>
