$(".select-1").on('click', function(){
  $('#callmodal').css('display','none');//close modal oncliecking outside modal

});



 function getAbsoluteUrl(){
	var newURL = window.location.href;
	 var module = jQuery("#module").val();
	var str=newURL.indexOf(module);
	
	var slicestr=newURL.substring(0,str);
	return slicestr;
}
function removeTextValue(hiddenname, searchname) {
    // Check if hiddenname and searchname are strings, not objects
    if (typeof hiddenname !== 'string') {
        hiddenname = jQuery(hiddenname).attr('id');
    }
    if (typeof searchname !== 'string') {
        searchname = jQuery(searchname).attr('id');
    }

    // Proceed with jQuery selectors using validated IDs
    var module = jQuery("#module").val();
    jQuery("#" + searchname).val(''); // Reset the input field with searchname ID
    jQuery("#" + hiddenname).val(''); // Reset the hidden field with hiddenname ID
   
}

	
function showCustomer1(hiddenfield,field,RelatedDisplayFieldName,mainmodule,maintabid,searchparam=''){
    var geturl=getAbsoluteUrl();
    //alert("searh"+geturl);
    var url=geturl+mainmodule+'/popuplist?hiddenfield='+hiddenfield+'&field='+field+'&rdisfield='+RelatedDisplayFieldName+'&mname='+mainmodule+'&maintabid='+maintabid;
    csrfTokenName = $("#csrfTokenName").val();
    csrfToken = $("#csrfToken").val();
    //[csrfParam]: csrfToken,
    if(searchparam =='')
    {
        data= {
        'other':'other',
        _csrf: csrfToken,
      };
  }
    else data = {  searchparam: searchparam, _csrf: csrfToken,};
	// alert(url);
        // $.get(url, function(data) {
        //     $('#modalreference').modal('show')
        //         .find('.modal-content')
        //         .html(data);
        // });
         console.log(data);

        $.ajax({
            type: 'POST',
            url: url,
            // async:false,
            data:data,
            success:function(data){
                 $('#modal22').modal('show')
                .find('.modal-content')
                .html(data);
            },
             error: function(data) { // if error occured
                          
               alert("Error occured.please try again");
            }, 
          dataType:'html'
        });
}
function addVendor(hiddenfield,field,RelatedDisplayFieldName,mainmodule,maintabid,searchparam=''){
    
    var url=geturl+mainmodule+'/quickcreatepopup?hiddenfield='+hiddenfield+'&field='+field+'&rdisfield='+RelatedDisplayFieldName+'&mname='+mainmodule+'&maintabid='+maintabid;
    csrfTokenName = $("#csrfTokenName").val();
    csrfToken = $("#csrfToken").val();
    //[csrfParam]: csrfToken,
    if(searchparam =='')
        data= { "_csrf": csrfToken};
    else data = {  searchparam: searchparam, "_csrf": csrfToken};
    // alert(url);
        // $.get(url, function(data) {
        //     $('#modalreference').modal('show')
        //         .find('.modal-content')
        //         .html(data);
        // });


        $.ajax({
            type: 'POST',
            url: url,
            async:false,
            data:data,
            success:function(data){
                 $('#modalreference').modal('show')
                .find('.modal-content')
                .html(data);
            },
             error: function(data) { // if error occured
                          
               alert("Error occured.please try again");
            }, 
          dataType:'html'
        });
}
function showinParent(recordid,recordvalue,field,hiddenfield){
    // alert(document.getElementById(field).value);
	document.getElementById(field).value = recordvalue;
	document.getElementById(hiddenfield).value = recordid;

}
// Function to close the modal
function closeModal() {
  $('#modalreference').modal('hide');
}
function closeModalP() {
  $('#modal22').modal('hide');
}
  // save call
$(document).on('click', '.savecall', function() {
  
   var call_information_related_to = $("#call_information_related_to").val();
   var call_information_related_to_id = $("#call_information_related_to_id").val();
   var call_information_subject = $("#call_information_subject").val();
   var call_information_comments = $("#call_information_comments").val();
   var call_information_creatorid = $("#call_information_creatorid").val();
   var call_information_createdtime = $("#call_information_createdtime").val();

    csrfTokenName = $("#csrfTokenName").val();
    csrfToken = $("#csrfToken").val();

    if(call_information_related_to != '' 
      && call_information_related_to_id !=''
      && call_information_subject !=''
      && call_information_comments !=''
      && call_information_creatorid!=''
      )
    {
      call_information = {call_information:
        {
        
        related_to:call_information_related_to, 
        related_to_id:call_information_related_to_id, 
        subject:call_information_subject, 
        comments:call_information_comments,
        creatorid:call_information_creatorid,
        modifiedby:call_information_creatorid,
        ownerid:call_information_creatorid,
        createdtime:call_information_createdtime,
        modifiedtime:call_information_createdtime,
       
      },
      module:"calls",
        mode:"create",
       _csrf:csrfToken
  };
      $.ajax({
        url: "addcall", // Adjust route as needed
        type: "POST",
        data: call_information,
        success: function (response) {
          if (response.success) {
            // alert(response.message);
            // $("#updateModel").modal("hide"); // Close modal on success
            location.reload();
          } else {
            alert("Error: " + response.message);
          }
        },
        error: function (xhr, status, error) {
          console.error("AJAX Error:", error);
          alert("An error occurred while updating.");
        },
      });

    }
});
// Open modal
$(document).on('click','#open-call-btn',function(){
 
//openModalBtn.addEventListener('click', () => {
  $('#callmodal').css('display','block');
  Recordid = $("#Recordid").val();
  $.ajax({
            type: 'GET',
            url: "addcall?Recordid="+Recordid,
            success:function(data){
                //alert(data);
                 $('.modal-1').html(data);
                $('#callmodal').css('display','flex');

            },
             error: function(data) { // if error occured
                          
               alert("Error occured.please try again");
            }, 
          dataType:'html'
    });
});
$(document).on('click','#open-meeting-btn',function(){
 
//openModalBtn.addEventListener('click', () => {
  $('#callmodal').css('display','block');
  Recordid = $("#Recordid").val();
  $.ajax({
            type: 'GET',
            url: "addmeeting?Recordid="+Recordid,
            success:function(data){
                //alert(data);
                 $('.modal-1').html(data);
                $('#callmodal').css('display','flex');

            },
             error: function(data) { // if error occured
                          
               alert("Error occured.please try again");
            }, 
          dataType:'html'
    });
});
$(document).on('click','#open-task-btn',function(){
 
    //openModalBtn.addEventListener('click', () => {
  $('#callmodal').css('display','block');
  Recordid = $("#Recordid").val();
  $.ajax({
            type: 'GET',
            url: "addtask?Recordid="+Recordid,
            success:function(data){
                //alert(data);
                 $('.modal-1').html(data);
                $('#callmodal').css('display','flex');

            },
             error: function(data) { // if error occured
                          
               alert("Error occured.please try again");
            }, 
          dataType:'html'
    });
});
// open document
$(document).on('click','#attach-doc-btn',function(){
 
    //openModalBtn.addEventListener('click', () => {
  $('#callmodal').css('display','block');
  Recordid = $("#Recordid").val();
  $.ajax({
            type: 'GET',
            url: "adddoc?Recordid="+Recordid,
            success:function(data){
                //alert(data);
                 $('.modal-1').html(data);
                $('#callmodal').css('display','flex');

            },
             error: function(data) { // if error occured
                          
               alert("Error occured.please try again");
            }, 
          dataType:'html'
    });
});
//save meeting 
$(document).on('click', '.savemeeting', function() {

   var meeting_information_related_to = $("#meeting_information_related_to").val();
   var meeting_information_related_to_id = $("#meeting_information_related_to_id").val();
   var meeting_information_subject = $("#meeting_information_subject").val();
   var meeting_information_description = $("#meeting_information_description").val();
   var meeting_information_creatorid = $("#meeting_information_creatorid").val();
   var meeting_information_createdtime = $("#meeting_information_createdtime").val();
   var meeting_information_start=$("#meeting_information_start-date").val();
   var meeting_information_start_time=$("#meeting_information_start-time").val();
   var meeting_information_end=$("#meeting_information_end-date").val();
   var meeting_information_end_time=$("#meeting_information_end-time").val();
   console.log(meeting_information_start);
   console.log(meeting_information_start_time);
   console.log(meeting_information_end);
   console.log(meeting_information_end_time);

   start_time = meeting_information_start+' '+meeting_information_start_time;
   end_time = meeting_information_end+' '+meeting_information_end_time;
   participants = '';
   $(".attendee").each(function()
    {
        console.log($(this).attr("data-id"));
        participants = $(this).attr("data-id")+','+participants;
    });

    csrfTokenName = $("#csrfTokenName").val();
    csrfToken = $("#csrfToken").val();

    if(meeting_information_related_to != '' 
      && meeting_information_related_to_id !=''
      && meeting_information_subject !=''
      && meeting_information_description !=''
      && meeting_information_creatorid!=''
      && meeting_information_start !=""
      && meeting_information_start_time !=""
      && meeting_information_end !=""
      && meeting_information_end_time !=""
      && participants !=""
      )
    {
      meeting_information = {meeting_information:
        {
        
        related_to:meeting_information_related_to, 
        related_to_id:meeting_information_related_to_id, 
        title:meeting_information_subject, 
        description:meeting_information_description,
        creatorid:meeting_information_creatorid,
        modifiedby:meeting_information_creatorid,
        ownerid:meeting_information_creatorid,
        createdtime:meeting_information_createdtime,
        modifiedtime:meeting_information_createdtime,
        from:start_time,
        to:end_time,
        participants:participants
       
      },
      module:"meeting",
        mode:"create",
       _csrf:csrfToken
  };
      $.ajax({
        url: "addmeeting", // Adjust route as needed
        type: "POST",
        data: meeting_information,
        success: function (response) {
          if (response.success) {
            // alert(response.message);
            // $("#updateModel").modal("hide"); // Close modal on success
            location.reload();
          } else {
            alert("Error: " + response.message);
          }
        },
        error: function (xhr, status, error) {
          console.error("AJAX Error:", error);
          alert("An error occurred while updating.");
        },
      });

    }
});
//save task
$(document).on('click', '.savetask', function() {

   var task_information_related_to = $("#task_information_related_to").val();
   var task_information_related_to_id = $("#task_information_related_to_id").val();
   var task_information_subject = $("#task_information_subject").val();
   var task_information_description = $("#task_information_description").val();
   var task_information_due_date = $("#task_information_due_date").val();
   var task_information_ownerid = $("#task_information_ownerid").val();
   var task_information_creatorid = $("#task_information_creatorid").val();
   var task_information_createdtime = $("#task_information_createdtime").val();
   console.log(task_information_due_date);

   
    csrfTokenName = $("#csrfTokenName").val();
    csrfToken = $("#csrfToken").val();
    if(task_information_related_to != '' 
      && task_information_related_to_id !=''
      && task_information_subject !=''
      && task_information_ownerid !=''
      && task_information_creatorid!=''
      )
    {
        task_information = {task_information:
            {
            
            related_to:task_information_related_to, 
            related_to_id:task_information_related_to_id, 
            subject:task_information_subject, 
            description:task_information_description,
            creatorid:task_information_creatorid,
            modifiedby:task_information_creatorid,
            ownerid:task_information_ownerid,
            createdtime:task_information_createdtime,
            modifiedtime:task_information_createdtime,
            due_date:task_information_due_date,
          },
          module:"task",
            mode:"create",
           _csrf:csrfToken
        };
        $.ajax({
        url: "addtask", // Adjust route as needed
        type: "POST",
        data: task_information,
        success: function (response) {
          if (response.success) {
            // alert(response.message);
            // $("#updateModel").modal("hide"); // Close modal on success
            location.reload();
          } else {
            alert("Error: " + response.message);
          }
        },
        error: function (xhr, status, error) {
          console.error("AJAX Error:", error);
          alert("An error occurred while updating.");
        },
      });
    }

});

// save doc
$(document).on('click', '.savedoc', function() {

   var documents_title = $("#documents_title").val();
   var documents_note_content = $("#documents_note_content").val();
   var documents_note_ownerid = $("#documents_note_ownerid").val();
   var documents_note_folderid = $("#documents_note_folderid").val();
   var documents_related_to = $("#documents_related_to").val();
   var documents_related_to_id = $("#documents_related_to_id").val();
   var documents_creatorid = $("#documents_creatorid").val();
   var documents_createdtime = $("#documents_createdtime").val();
   

   let formData = new FormData();
        let fileInput = $('#dragfileInput')[0].files[0];
        console.log(fileInput);
        if (!fileInput) {
            alert('please select a file !');
            return;
        }
        if (!documents_title.trim() || !documents_note_content.trim() || !documents_note_ownerid.trim()||!documents_note_folderid.trim()||!documents_related_to.trim()  || !documents_creatorid.trim() ||  !documents_createdtime.trim()) {
            alert('please fill all the fields !');
            return;
        }



        if (fileInput !='') {
        formData.append('file', fileInput);//attach the file if provided
        }

        formData.append('documents'+"[title]", documents_title);
        formData.append('documents'+"[notecontent]", documents_note_content);
        formData.append('documents'+"[ownerid]", documents_note_ownerid);
        formData.append('documents'+"[folderid]", documents_note_folderid);
        formData.append('documents'+"[related_to]", documents_related_to);
        formData.append('documents'+"[related_to_id]", documents_related_to_id);
        formData.append('documents'+"[creatorid]", documents_creatorid);
        formData.append('documents'+"[createdtime]", documents_createdtime);
        formData.append('documents'+"[modifiedtime]", documents_createdtime);
        formData.append('documents'+"[modifiedby]", documents_creatorid);
        formData.append('_csrf', $('#csrfToken').val()); // Add CSRF token for security
        formData.append('mode',"create"); 
        formData.append('module', "documents");
       
       //call function
       uploadstatus = "upload-status1";
        $.ajax({
            url: 'adddoc', // Update with your Yii2 controller action URL
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.success) {
                    // $('#upload-status').text('Notes Saved successfully: ' + response.fileUrl);
                   // $('#'+uploadstatus).text('Notes Saved successfully');
                   location.reload();
                } else {
                    //$('#'+uploadstatus).text('File upload failed: ' + response.message);
                    alert(response.message);
                }
            },
            error: function (xhr, status, error) {
                // $('#upload-status').text('An error occurred: ' + error);
                    alert(error);

            }
        });
   
        

});
// Fetch filtered data from the backend
function fetchFilteredList() {
  const dropdownList = document.getElementById("dropdownList");
  dropdownList.innerHTML = ""; // Clear the previous list
  
  const input = document.getElementById("attendees");
  const inputValue = input.value;

  if (inputValue.trim() === "") {
    // Exit early if the input is empty
    return;
  }

  fetch(`searchusers?query=${encodeURIComponent(inputValue)}`)
    .then((response) => response.json())
    .then((data) => {
      const dropdownList = document.getElementById("dropdownList");
      dropdownList.innerHTML = ""; // Clear the previous list

      data.forEach((item) => {
        const li = document.createElement("li");
        li.textContent = item.showfield;
        li.dataset.id = item.id; // Save the ID in a dataset attribute
        li.onclick = () => addToContainer(item.id, item.showfield);
        dropdownList.appendChild(li);
      });

      // Clear the search box
      input.value = "";
    })
    .catch((error) => console.error("Error fetching data:", error));
}


// Add an item to the selected container
function addToContainer(id, name) {
  const container = document.getElementById("selectedContainer");

  // Check if the item is already added
  if (Array.from(container.children).some((child) => child.dataset.id === String(id))) {
    alert("User already added!");
    return;
  }

  // Create a new div for the selected item
  const newItem = document.createElement("span");
  newItem.textContent = name;
  newItem.dataset.id = id; // Store the ID
  // Add a class to the newItem
  newItem.classList.add("attendee");

  // Optionally add a remove button for each item
  const removeBtn = document.createElement("span");
  removeBtn.textContent = "X";
  removeBtn.style.marginLeft = "10px";
  removeBtn.style.border = "none";
  removeBtn.style.background = "none";
  removeBtn.style.cursor = "pointer";
  removeBtn.onclick = () => newItem.remove();

  newItem.appendChild(removeBtn);
  container.appendChild(newItem);
}
//add ckeditor
//ck editor
  let editorInstance;
  ClassicEditor
    .create(document.querySelector('.notes-editor'),{
    //.create(document.querySelector('.notes-editor'), {
      //  toolbar: ['bold', 'italic', 'link', 'undo', 'redo']
   // })
   editorConfig: {
            height: '800px'
        
      }
    })
    .then(editor => {
        //console.log('Editor is ready!', editor);
        editorInstance = editor;
    })
    .catch(error => {
        console.error('There was a problem initializing the editor.', error);
    });
    //end ck editor

    //ck editor
  let editorInstance1;
  ClassicEditor
    .create(document.querySelector('.notes-editor2'),{
    //.create(document.querySelector('.notes-editor'), {
      //  toolbar: ['bold', 'italic', 'link', 'undo', 'redo']
   // })
   editorConfig1: {
            height: '800px'
        
      }
    })
    .then(editor => {
        //console.log('Editor is ready!', editor);
        editorInstance1 = editor;
    })
    .catch(error => {
        console.error('There was a problem initializing the editor.', error);
    });
    //end ck editor

//save notes
$(document).on('click',".post-btn",function(e){


  if (editorInstance) {
                var modnotesval = editorInstance.getData(); // Get the editor's content
               // console.log('Editor Value:', editorValue);
}
  Recordid = $('#Recordid').val();

  e.preventDefault();

        let formData = new FormData();
        let fileInput = $('#attach-notes')[0].files[0];
        console.log(fileInput);
        if (!modnotesval.trim() && !fileInput) {
            alert('Please provide either a file or some text!');
            return;
        }


        if (fileInput !='') {
        formData.append('file', fileInput);//attach the file if provided
        }

        formData.append('modnotesval', modnotesval);
        formData.append('Recordid', Recordid);
        formData.append('_csrf', $('#csrfToken').val()); // Add CSRF token for security
       
       //call function
       uploadstatus = "upload-status";
       postnotes(formData,uploadstatus);
});
$(document).on('click',".post-btn1",function(e){


  if (editorInstance1) {
                var modnotesval = editorInstance1.getData(); // Get the editor's content
               // console.log('Editor Value:', editorValue);
}
  Recordid = $('#Recordid').val();

  e.preventDefault();

        let formData = new FormData();
        let fileInput = $('#attach-notes1')[0].files[0];
        console.log(fileInput);
        if (!modnotesval.trim() && !fileInput) {
            alert('Please provide either a file or some text!');
            return;
        }


        if (fileInput !='') {
        formData.append('file', fileInput);//attach the file if provided
        }

        formData.append('modnotesval', modnotesval);
        formData.append('Recordid', Recordid);
        formData.append('_csrf', $('#csrfToken').val()); // Add CSRF token for security
       
       //call function
       uploadstatus = "upload-status1";
       postnotes(formData,uploadstatus);
});
function postnotes(formData,uploadstatus)
{
   $.ajax({
            url: 'postnotes', // Update with your Yii2 controller action URL
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.success) {
                    // $('#upload-status').text('Notes Saved successfully: ' + response.fileUrl);
                    $('#'+uploadstatus).text('Notes Saved successfully');
                } else {
                    $('#'+uploadstatus).text('File upload failed: ' + response.message);
                }
            },
            error: function (xhr, status, error) {
                $('#upload-status').text('An error occurred: ' + error);
            }
        });
}
