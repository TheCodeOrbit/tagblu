$(document).ready(function () {
  const inventoryTags = [];
  // $('.savebutton').prop('disabled', true);
  // var newURL = window.location.href;
  var module = "stickerremoval";
  // var str = newURL.split(module);
  // var action = str[1].split("/")[1].split("?")[0];
  // const urlParams = new URLSearchParams(window.location.search);
  // const itemid = urlParams.get('itemid').split("_");
  // // startLoading();
  // // if(action != "edit")
  // var data = {
  //   Recordid: itemid[0],
  //   Productid: itemid[1],
  //   Subcategory: itemid[2],
  //   _csrf: $("#csrfToken").val(),
  // };
  // $.ajax({
  //   type: "POST",
  //   url: "getdatafrominventory",
  //   // async:true,
  //   data: data,
  //   success: //async 
  //     function (data) {
  //       if (data.status === "success") {
  //         console.log(data.data[0]);
  //         let grn_date = data.data[0].createdtime.split(" ")[0].split("-");
  //         $("#grn_number").val(data.data[0].grn_no);
  //         $("#grn_date").val(grn_date[2] + "-" + grn_date[1] + "-" + grn_date[0]);
  //         // $("#total_quantity").val(data.data.received_qty);
  //         $("#pickup_id").val(data.data[0].pickup_id);
  //         $("#lot_number").val(data.data[0].lot_no);
  //       }
  //     },
  //   error: function (data) {
  //     // if error occured

  //     // alert("Error occured.please try again");
  //     // stopLoading();
  //   },
  //   dataType: "json",
  // });


  // $(".add-more-records").hide();
  $(".remove-row-btn").remove()

  //barcode scanner code

  $('.barcode-input').focus(); // Always keep focus

  $('.barcode-input').on('keypress', function (e) {
    if (e.which == 13) { // ENTER key pressed
      e.preventDefault();
      var barcode = $(this).val().trim();
      // if(barcode !== '') {
      //     $.ajax({
      //         // url: '{$barcodeUrl}',
      //         type: 'GET',
      //         data: {barcode: barcode},
      //         success: function(response) {
      //             if (response.success) {
      //                 alert('Product: ' + response.product_name);
      //                 // Here you can update the UI as needed
      //             } else {
      //                 alert('Barcode not found!');
      //             }
      //             $('.barcode-input').val(''); // Clear input after scan
      //             $('.barcode-input').focus();  // Refocus
      //         },
      //         error: function() {
      //             alert('Server Error');
      //             $('.barcode-input').val('').focus();
      //         }
      //     });
      // }
    }
  });

  //end barcode scanner code 


  const observer = new MutationObserver(function () {
    checksavebutton();
  });

  $('.help-block').each(function () {
    observer.observe(this, { childList: true, subtree: true, characterData: true });
  });
});

$(document).on('keypress', "#tag_number", function (e) {
  if (e.which === 13) { // ENTER key pressed
      e.preventDefault();
      console.log()
      
    var input = $(this);
    // var id_number = input.attr('id').split("_")[2];
    const value = input.val().trim();
     // Clear previous validation message
    input.closest('.form-group').find('.help-block').text('');
     console.log(value);
    if(value !== '')
      checktagnoandgrn(value);
    else
      {
        $('#tag_number').closest('.form-group').find('.help-block')
          .text('Tag Number is required.')
          .css('color', 'red')
          .show();
      }
    }
});
// $(document).on('blur', "input[id^='tag_number']", function () {
 /* working code
 $(document).on('blur', "#tag_number", function () {
  var input = $(this);
  // var id_number = input.attr('id').split("_")[2];
  const value = input.val().trim();
  if(value != '')
    checktagnoandgrn(value);
  else
    {
      $('#tag_number').closest('.form-group').find('.help-block')
        .text('Tag Number is required.')
        .css('color', 'red')
        .show();
    }
});*/

function checktagnoandgrn(tagno) {

  var newURL = window.location.href;
  var module = "stickerremoval";
  // var str = newURL.split(module);
  // var action = str[1].split("/")[1].split("?")[0];
  // const urlParams = new URLSearchParams(window.location.search);
  // const itemid = urlParams.get('itemid').split("_");
  startLoading();
  // if(action != "edit")
  var data = {
    // Recordid: itemid[0],
    // Productid: itemid[1],
    // Subcategory: itemid[2],
    TagNumber: tagno,
    _csrf: $("#csrfToken").val(),
  };
  $.ajax({
    type: "POST",
    url: "checkgrnandtagnoininventory",
    // async:true,
    data: data,
    success: //async 
      function (data) {
        if (data.status === "success") {
          console.log(data);
          $('#inventory_id').val(data.data.inventory_id);
          console.log("ineventory_store");
          $('#bin_number').val(data.data.bin_number).trigger('change');
          if (data.data.status == 3) {
            $('#status').val(4).trigger('change'); //4 is cleannig require
             // Auto-save to database after 200ms
                setTimeout(function () {
                  inventoryTags = data.data.inventory_id;
                  updateInventory(data.data.inventory_id,data.data.tag_number,data.data.bin_number);
                }, 200);
          }
          else if (data.data.status != 3) {
            $('#status').val(data.data.status).trigger('change'); //4 is cleannig require
            $statusText = $('#status option:selected').text();
            // alert('Stage Error : '+ $statusText);
            // $('.help-block').addClass('big-text');
            $('#tag_number').closest('.form-group').find('.help-block').addClass('fs-4')
            .text('Stage Error: current status is ' + $statusText)
              .css('color', 'red')
              .show();
              $('#tag_number').val("");
              $('#bin_number').val("");
              $('#status').val("");
          }
          stopLoading();
        }
        else {
          $("inventory_id").val("");
          $('#tag_number').closest('.form-group').find('.help-block').addClass('fs-4')
          .text('Tag Number not found.')
          .css('color', 'red')
          .show();
          $('#tag_number').val("");
              $('#bin_number').val("");
              $('#status').val("");
          stopLoading();
        }
      },
    error: function (data) {
      // if error occured

      // alert("Error occured.please try again");
      // stopLoading();
    },
    dataType: "json",
  });


}
$(document).on("click", ".remove-row-btn", function () {
  console.log("remove row button call");
  checksavebutton();
});
function checksavebutton() {
  var hasErrors = $('.help-block:visible').filter(function () {
    return $(this).text().trim() !== '';
  }).length > 0;

  $('.savebutton').prop('disabled', hasErrors);
  $('.add-more-records').prop('disabled', hasErrors);
}

function updateInventory(inventoryId,tagNumber,binNumber){
  console.log("inventory update call"+inventoryTags);
  $.ajax({
    url: 'updateinventory', // replace with your actual endpoint
    method: 'POST',
    data: {
      tag_number:tagNumber,
      bin_number:binNumber,
      inventory_id: inventoryId,
      status_id: 4, // because you are setting it to cleaning required
      _csrf: $("#csrfToken").val(),
      // add other fields as needed
    },
    success: function (response) {
      console.log("response"+response.success);
      if(response.success == true )
        data = `<tr>
                      <td>`+tagNumber+`</td>
                      <td>`+$('#bin_number option:selected').text()+`</td>
                      <td>`+$('#status option:selected').text()+`</td>
                </tr>`;
                $("inventory_id").val("");
                $('#tag_number').val("");
                $('#bin_number').val("");
                $('#status').val("");
        $("#inventory_tbl").append(data);
      console.log('Auto-save successful:', response);
    },
    error: function (xhr) {
      console.error('Auto-save failed:', xhr.responseText);
    }
  });
  //
}

//code added by ptpatel on date 12-12-2025
$(document).on("click", "#btnBulkStatusUpload", function () {
      $("#bulkStatusFile").val("");
      $("#bulkStatusMessage").addClass("d-none").html("");
      $("#bulkStatusForm").show();           
        $("#btnSubmitBulkStatus").show();
      $("#bulkStatusModal").modal("show");
  });
  $(document).on("click", "#btnSubmitBulkStatus", function () {
      let $btn = $(this);
      let fileInput = $("#bulkStatusFile")[0];

      if (!fileInput.files.length) {
          alert("Please select a CSV file");
          return;
      }
      // disable immediately
      $btn.prop("disabled", true);
      startLoading();
      let formData = new FormData();
      formData.append("csvfile", fileInput.files[0]);
      formData.append("_csrf", $("#csrfToken").val());

      $.ajax({
          url: "bulkupdateinventorystatus", 
          type: "POST",
          data: formData,
          contentType: false,
          processData: false,

          success: function (response) {
              console.log(response);

              if (response.success) {
                 let msg='';
                  if(response.updated > 0 )
                    msg = "Bulk update completed successfully!<br>";
                msg += "Updated Records: " + (response.updated || 0) + " of " + (response.totalrows) ;
                if (response.errors && response.errors.length > 0) {
                    msg += "<div class='text-danger'><br>Errors:<br>";
                    response.errors.forEach(err => {
                        msg += "- " + err + "<br>";
                    });
                     msg += "</div>";
                }

                // alert(msg);
                $("#bulkStatusForm").hide();           
                $("#btnSubmitBulkStatus").hide();
                if(response.updated > 0 ){
                    $("#bulkStatusMessage")
                      .removeClass("d-none alert-danger")
                      .addClass("alert alert-success")
                      .html(msg);
                  }
                  else
                  {
                     $("#bulkStatusMessage")
                    .removeClass("d-none alert-success")
                    .addClass("alert alert-danger")
                    .html(msg);
                  }

            } else {

                let msg = "Bulk update failed!<br>";
                if (response.errors && response.errors.length > 0) {
                    msg += "<br>Errors:<br>";
                    response.errors.forEach(err => {
                        msg += "- " + err + "<br>";
                    });
                } else {
                    msg += (response.message || "");
                }

                $("#bulkStatusForm").hide();           
                $("#btnSubmitBulkStatus").hide();
                $("#bulkStatusMessage")
                  .removeClass("d-none alert-success")
                  .addClass("alert alert-danger")
                  .html(msg);
            }

          },

          error: function (err) {
              console.error(err);
              alert("Error while uploading CSV.");
          },
          complete: function () {
            // ALWAYS re-enable (success or error)
            
            stopLoading();
            $btn.prop("disabled", false);
        }
      });
  });
//end code added by ptpatel on date 12-12-2025