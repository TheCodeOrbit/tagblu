$(document).ready(function () {
var setparent = exchangeRate = false;
var setgrandparent = false;
var  selectedModulename = '';
  const $tableBody = $("#dynamicTable tbody");


  $(document).on("click", "#refresh-icon", function (e) {
    location.reload(); // Reload the current page
  });

  const modeInput = document.getElementById("mode");
  if (modeInput && modeInput.value === "Create") {
    getmoduleName();
  }
  else if (modeInput && modeInput.value === "edit") {
    // getmoduleName();
  }

  $(document).on("change", "#picklist_module_name", function (e) {
    // alert($(this).val());
    let selectedModuleId = $(this).val();
    getpicklist(selectedModuleId);
  });

  $(document).on("change", "#picklist_table_name", function (e) {
    // alert($(this).val());
    selectedModulename = $("#picklist_table_name option:selected").text();
    let selectedModuleId = $(this).val();
    if (selectedModuleId != null && selectedModuleId != "") {
      getpicklisttablerows(selectedModuleId, 1, 10);
    }
  });
  //open model 

  // add button handler
  $(document).on("click", "#addpicklist", function () {
    console.log("addpicklist click" + $("#picklist_table_name").val());
    if ($("#picklist_table_name").val() == 0) {
      alert("First select module and picklist table.");
      return;
    }
    $("#picklistmode").val("add");
    $("#editRecordId").val("");
    $("#editFieldId").val("");
    $("#picklistEditValue").val("");
    $("#picklist_parent_dd").val('').trigger("change");
    $('#picklist_grand_parent_dd').val('').trigger("change");
    $(".help-block").text("");
    $("#exchange_rate").val("");
    

    // set values into modal fields
    $("#modal_label_name").text("Add Picklist Item to "+selectedModulename);
    $("#editRecordId").val();
    $("#editFieldId").val($("#picklist_table_name").val());
    $("#picklistEditValue").val();
    if(exchangeRate){
      $("#exchange_rate").val();
      $("#picklistExchangeRateValue").show();
    }
    else
    {
      $("#picklistExchangeRateValue").hide();
    }

    // show modal
    $("#picklistModal").modal("show");
    $("#modellbl").text("Enter New Value");
    console.log(setparent);
    if(setparent)
    {
      $("#parent_dd_div").show();
      $("#picklist_parent_dd").val('');
    }
    else
    {
      $("#picklist_parent_dd").val('');
      $("#parent_dd_div").hide();
    }
    if(setgrandparent)
    {
      $("#parent_grand_dd_div").show();
      $("#picklist_grand_parent_dd").val('');
    }
    else
    {
      $("#picklist_grand_parent_dd").val('');
      $("#parent_grand_dd_div").hide();
    }
  });
  $(document).on("click", ".editpicklistRow", function (e) {
    console.log("editpicklistRow click");
    $("#picklistmode").val("edit");
    $("#editRecordId").val("");
    $("#editFieldId").val("");
    $("#picklistEditValue").val("");
    $("#exchange_rate").val("");
    $(".help-block").text("");


    let row = $(this).closest("tr");
    let recordid = row.data("id");
    let value = row.data("value");
    let fieldid = row.data("fieldid");
    let parent_id = row.data("parentid");
    let grandparent_id = row.data("grandparentid");
    let exchange_rate = row.data("exchange_rate");
    let targettable = row.data("targettable");

    // set values into modal fields
    $("#modal_label_name").text("Edit Picklist Item");
    $("#editRecordId").val(recordid);
    $("#editFieldId").val(fieldid);
    $("#picklistEditValue").val(value);

    if (targettable === "currency") {
        $("#exchange_rate").val(exchange_rate || "");
        $("#picklistExchangeRateValue").show();
    } else {
        $("#exchange_rate").val("");
        $("#picklistExchangeRateValue").hide();
    }

    if(parent_id == "" || parent_id == null) {
        $("#picklist_parent_dd").val("");
        $("#parent_dd_div").hide();
    } else {
        if (parent_id) {
            let pid = parent_id.toString();
            if (pid.includes(",")) {
                selectedValues = pid.split(",").map(v => v.trim()).filter(v => v !== "");
            } else {
                selectedValues = [pid.trim()];
            }
            $("#picklist_parent_dd").val(selectedValues).trigger("change");
        }
        $("#parent_dd_div").show();
    }
    if(grandparent_id == "" || grandparent_id == null) {
        $("#picklist_grand_parent_dd").val("");
        $("#parent_grand_dd_div").hide();
    } else {
        $("#parent_grand_dd_div").show();
        $("#picklist_grand_parent_dd").val(grandparent_id);
    }

    // show modal
    $("#picklistModal").modal("show");
    $("#modellbl").text("Enter Value");
});

  
  $(document).on("click", ".removeRow", function (e) {
    const $btn = $(this); 
    console.log("editpicklistRow click");
    $("#picklistmode").val("delete");
    $("#editRecordId").val("");
    $("#editFieldId").val("");
    $("#picklistEditValue").val("");

    let row = $(this).closest("tr");
    let recordid = row.data("id");
    let value = row.data("value");
    let fieldid = row.data("fieldid"); // make sure <tr> has data-table attr

    // set values into modal fields
    $("#modal_label_name").text("Edit");
    $("#editRecordId").val(recordid);
    $("#editFieldId").val(fieldid);
    $("#picklistEditValue").val(value);

    // show modal
    // $("#picklistModal").modal("show");
    $("#modellbl").text("Enter new Name");
    // $(".savePicklistData").trigger("click");
    if (confirm("Are you sure you want to delete?")) {
      $(".savePicklistData").trigger("click");
      //  $btn.blur();
      document.activeElement.blur();
  } else {
      // do nothing
  }
  });
  //save data
  $(document).on("click", ".savePicklistData", function () {
    let editRecordId = $("#editRecordId").val();
    let parentArr = $("#picklist_parent_dd").val();
    if (Array.isArray(parentArr)) {
        parentArr = parentArr.filter(function(val) { 
            return val && val.toLowerCase() !== "select";
        });
    }
    let data = {
      editRecordId: editRecordId,
      _csrf: $("#csrfToken").val(),
      editFieldId: $("#editFieldId").val(),
      picklistEditValue: $("#picklistEditValue").val(),
      picklistmode: $("#picklistmode").val(),
      picklistParentValue : $("#picklist_parent_dd").val(),
      picklistGrandParentValue : $("#picklist_grand_parent_dd").val(),
      exchangeRate : $("#exchange_rate").val(),
    };
    
    errorMessage = "This filed is required";
    console.log("setgrandparent"+setgrandparent+"---setparent"+setparent);
    console.log("gpd"+$("#picklist_grand_parent_dd").val());
    console.log("pd"+$("#picklist_parent_dd").val());
    let picklist_mode = $("#picklistmode").val();
    console.log("picklistmode"+picklist_mode);
    if (setgrandparent && picklist_mode != "delete") {
      if($("#picklist_grand_parent_dd").val() == "" || $("#picklist_grand_parent_dd").val() == null){
          var errorElement = $("#picklist_grand_parent_dd").closest(".form-group").find(".help-block");
          errorElement.html(errorMessage);
          $("#picklist_grand_parent_dd").focus();
          return false;
      }
      else
      {
        var errorElement = $("#picklist_grand_parent_dd").closest(".form-group").find(".help-block");
        errorElement.html("");
      }
    }
    if (setparent && picklist_mode != "delete") {
      if($("#picklist_parent_dd").val() == "" || $("#picklist_parent_dd").val() == null){
          var errorElement = $("#picklist_parent_dd").closest(".form-group").find(".help-block");
          errorElement.html(errorMessage);
          $("#picklist_parent_dd").focus();
          return false;
      }
      else
      {
        var errorElement = $("#picklist_parent_dd").closest(".form-group").find(".help-block");
        errorElement.html("");
      }
    }
    if (!$("#picklistEditValue").val() && picklist_mode != "delete") {
      var errorElement = $("#picklistEditValue").closest(".form-group").find(".help-block");
      errorElement.html(errorMessage);
      $("#picklistEditValue").focus();
      return false;
    }
    else
    {
       var errorElement = $("#picklistEditValue").closest(".form-group").find(".help-block");
      errorElement.html("");
    }
    $.ajax({
      type: "POST",
      url: "savetablevalue",
      data: data,
      success: function (data) {
        if (data.status === "success") {
          $("#picklistModal").modal("hide");
          $("#picklist_table_name").trigger("change");
          alert(data.message);
        }
        else alert("sometinhg went wrong");
      },
      error: function (data) {
        alert("Error occured.please try again");
      },
      dataType: "json",
    });

  });
var currentPageSize = 10;

  $(document).on('change', '#pageSizeSelect', function() {
    currentPageSize = parseInt($(this).val(), 10);
    let selectedModuleId = $("#picklist_table_name").val();
    if(selectedModuleId){
        getpicklisttablerows(selectedModuleId, 1, currentPageSize);
    }
});

  $('.singleselect').select2({
    placeholder: '-- Select Field --',
    width: '100%'
  });
  $('#picklist_parent_dd').on('select2:select select2:unselect', function () {
    let values = $(this).val();
    if (values && Array.isArray(values)) {
        let filtered = values.filter(function(val) { 
            return val && val.toLowerCase() !== "select";
        });
        // Only trigger change if something was removed
        if (filtered.length !== values.length) {
            $(this).val(filtered).trigger('change');
        }
    }
});
  $('.multySelect').select2({
        placeholder: "Select",
        allowClear: true,
        width: '100%' // Ensures it spans the full width like Bootstrap form controls
    });
  function getmoduleName() {
    // alert(thisobj.value);
    startLoading();
    console.log("calling");
    const csrfToken = $('meta[name="csrf-token"]').attr("content");


    const selectedModuleId = $("#picklist-module_name").val();
    console.log("selectedModuleId=" + selectedModuleId);
    // Reset dropdowns
    const moduleDropdown = $("#picklist-module_name")
      .empty()
      .append('<option value="">Select</option>');
    $.ajax({
      type: "GET",
      url: "getmodulenames",
      data: { _csrf: csrfToken },
      dataType: "json",
      success: function (response) {
        console.log(response);
        if (response.status === "success") {
          console.log(response.data);
          Object.entries(response.data).forEach(([id, name]) => {
            moduleDropdown.append(`<option value="${id}">${name}</option>`);
          });
          moduleDropdown.trigger("change"); // Update Select2 dropdown
          if (selectedModuleId) {
            moduleDropdown.val(selectedModuleId).trigger("change"); // Select2 update
          }
          stopLoading();
        } else {
          alert(response.message);
          stopLoading();
        }

      },
      error: function (xhr) {
        console.error(xhr);
        alert("Error occurred while fetching categories. Please try again.");
        stopLoading();
      },
    });
  }


  function getpicklist(selectedModuleId) {
    // alert(thisobj.value);
    startLoading();
    console.log("calling");
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    // Reset dropdowns
    const picklistDropdown = $("#picklist_table_name")
      .empty()
      .append('<option value="">Select </option>');
    $.ajax({
      type: "GET",
      url: "getpicklisttables",
      data: { _csrf: csrfToken, moduleid: selectedModuleId },
      dataType: "json",
      success: function (response) {
        console.log(response);
        if (response.status === "success") {
          Object.entries(response.data).forEach(([id, name]) => {
            picklistDropdown.append($('<option>', {
              value: id,
              text: name
            }));
          });

          picklistDropdown.trigger("change"); // refresh Select2 or normal dropdown

          stopLoading();
        } else {
          alert(response.message);
          stopLoading();
        }

      },
      error: function (xhr) {
        console.error(xhr);
        alert("Error occurred while fetching categories. Please try again.");
        stopLoading();
      },
    });
  }


$(document).on('click', '#importPicklist', function() {
    var selectedModulename = $("#picklist_table_name option:selected").text();
    if ($('#select2-picklist_parent_dd-container').is(':visible') && $('#select2-picklist_parent_dd-container li').length === 0) {
      alert('Selecting Parent is mandatory!');
      return false;
    }
    var parentNames = [];
    $('#select2-picklist_parent_dd-container li .select2-selection__choice__display').each(function() {
        parentNames.push($(this).text().trim());
    });

    var headline = "Add Picklist Item to " + selectedModulename;
    if (parentNames.length) {
        headline = "Add Picklist Item to " + selectedModulename + " (" + parentNames.join(', ') + ")";
    }

    $('#import_modal_label_name').text(headline);

    $('#importResult').html(''); 
    $('#importModuleId').val($('#picklist_module_name').val());
    $("#picklistModal").modal("hide");

    if ($('#picklist_table_name').val() == 0 || $('#picklist_table_name').val() == null) {
        alert('First select Picklist Table.');
        return;
    }
    $('#importTableId').val($('#picklist_table_name').val());
    var parentIds = [];
    var $parentContainer = $('#select2-picklist_parent_dd-container');
    if ($parentContainer.length) {
        $parentContainer.find('li').each(function() {
            var desc = $(this).find('button').attr('aria-describedby');
            if (desc) {
                var parts = desc.split('-');
                var parentId = parts[parts.length - 1];
                parentIds.push(parentId);
            }
        });
    }
    $('#importParentIds').val(parentIds.length ? parentIds.join(',') : '');

    $('#importModal').modal('show');
});


  $(document).on('click', '#downloadSampleCsv', function(){
      window.location.href = '/uploads/sample_picklist_bulk_upload.csv';
  });
 

   $('#importCsvForm').on('submit', function(e){
      e.preventDefault();
       startLoading();
       var selectedTable = $("#picklist_table_name").val();
       let colName = $("#picklist_table_name option:selected").text();
      var formData = new FormData(this);
      var csrfToken = $('meta[name="csrf-token"]').attr('content') || $('input[name="_csrf"]').val();
      formData.append('_csrf', csrfToken);
      formData.append('colName',colName);
      $.ajax({
          type: 'POST',
          url: 'savetablevalue', 
          data: formData,
          processData: false,
          contentType: false,
          success: function(data){
              if(data.status === 'completed') {
                 stopLoading();
                 $('#importModal').modal('hide');
                 
                  getpicklisttablerows(selectedTable, 1, 10);
                 alert(data.message);
              } else if(data.status === 'fail' || data.status === 'error') {
                  $('#importResult').html('<div class="text-danger">'+data.message+'</div>');
                  stopLoading();
              } else {
                  $('#importResult').html('<div class="text-success">'+data.message+'</div>');
                  stopLoading();
              }
          },
          error: function(){
              $('#importResult').html('<div class="text-danger">Error uploading. Please try again.</div>');
              stopLoading();
          }
      });
  });

  $(document).ready(function () {
    function updateDownloadCsvUrl() {
      // let fieldId = $('#picklist_table_name').val();
      let colName = $("#picklist_table_name option:selected").text();
      console.log(colName, 'colName');
      if (colName && colName !== '0') {
        $('#downloadCsvBtn').attr('href',
          '/admin/picklist/download-sample-csv?colName=' + encodeURIComponent(colName));
        $('#downloadCsvBtn').removeClass('disabled');
      } else {
        $('#downloadCsvBtn').attr('href', '#');
        $('#downloadCsvBtn').addClass('disabled');
      }
    }

    $('#picklist_table_name').on('change', updateDownloadCsvUrl);

    updateDownloadCsvUrl();

    $('#downloadCsvBtn').on('click', function (e) {
      if ($(this).attr('href') === '#' || $(this).hasClass('disabled')) {
        e.preventDefault();
        alert("Please select a module to download the sample CSV.");
      }
    });
  });

  $('#importModal').on('hidden.bs.modal', function () {
    let $fileInput = $('#csv_file_input');
    $fileInput.val('');
    $('#importResult').html('');
  });
//  $('#exportPicklistBtn').on('click', function(e) {
//         var exportUrl = $(this).attr('data-export-url');
//         var fieldid = $('#picklist_table_name').val();
//         var parentValue = $('#picklist_parent_dd').val();
//         if (!fieldid || fieldid == 0) {
//           console.log('fieldid=' + fieldid + ', parentValue=' + parentValue);
//             alert('Please select both Field/Table and Parent.');
//             return;
//         }
//         var url = exportUrl
//             + '?fieldid=' + encodeURIComponent(fieldid)
//             + (parentValue ? '&parentValue=' + encodeURIComponent(parentValue) : '');
//         window.location = url;
//     });


$(function () {
    const dltBtn = $('<button/>', {
        id: 'delete-picklist',
        type: 'button',
        class: 'btn btn-danger',
        text: 'Delete All',
        style: 'display:none;margin-right:5px'
    });

    if ($('#exportPicklistBtn').length) {        
        dltBtn.insertBefore('#exportPicklistBtn'); 
    }
});


$('#exportPicklistBtn').on('click', function(e) {
    var exportUrl = $(this).attr('data-export-url');
    var moduleId = $('#picklist_module_name').val();
    var moduleName = $('#picklist_module_name option:selected').text().trim();
    var picklistTableId = $('#picklist_table_name').val();
    var picklistTableName = $('#picklist_table_name option:selected').text().trim();
    var parentValue = $('#picklist_parent_dd').val();
    var parentText = "";

    if (Array.isArray(parentValue)) {
        parentText = $('#picklist_parent_dd option:selected').map(function() {
            return $(this).text().trim();
        }).get().join(',');
    } else if (parentValue) {
        parentText = $('#picklist_parent_dd option:selected').text().trim();
    }

    var params = [
            "fieldid=" + encodeURIComponent(picklistTableId),
            "fieldname=" + encodeURIComponent(picklistTableName),
            "moduleid=" + encodeURIComponent(moduleId),
            "modulename=" + encodeURIComponent(moduleName),
            "parentValue=" + encodeURIComponent(parentValue || ""),
            "parentText=" + encodeURIComponent(parentText || "")
        ].join('&');
        window.location = exportUrl + "?" + params;
    });

  function getpicklisttablerows(selectedfieldid, page = 1, pageSize = 10) {
    startLoading();
    $.ajax({
        type: 'GET',
        url: 'getpicklisttablerows',
        data: { selectedfieldid: selectedfieldid, page: page, pageSize: pageSize },
        dataType: 'json',
        success: function(response) {
            currentParentLbl = response.parent_dd_lbl || 'Parent';
            setparent = !!(response.parentdata && Object.keys(response.parentdata).length > 0);
            setgrandparent = !!(response.grandparentdata && Object.keys(response.grandparentdata).length > 0);

            var tbody = $("#dynamicTable tbody").empty();
            // $('#delete-picklist').show();
            $.each(response.data, function(idx, item) {
                tbody.append(
                    '<tr data-id="'+item.id+'" data-value="'+item.name+'" data-fieldid="'+selectedfieldid+'" data-parentid="'+(item.parent_id || '')+'" data-grandparentid="'+(item.grand_parent_id || '')+'" data-exchange_rate="'+(item.exchange_rate || '')+'" data-targettable="'+(item.targettable || '')+'">'+
                 
                        '<td>'+item.name+'</td>'+
                        '<td class="text-end">'+
                            '<button type="button" class="btn btn-primary btn-sm editpicklistRow">Edit</button> '+
                            '<button type="button" class="btn btn-danger btn-sm removeRow">Delete</button>'+
                        '</td>'+
                    '</tr>'
                );
            });

            var parentSelect = $('#picklist_parent_dd');
            parentSelect.empty().append('<option value="">Select</option>');
            if (setparent) {
                $.each(response.parentdata, function(k, v) {
                    parentSelect.append('<option value="' + k + '">' + v + '</option>');
                });
            }
            $('#parent_dd_lbl').text(currentParentLbl);

            if (response.is_multiple) {
                parentSelect.attr('multiple', 'multiple').attr('name', 'picklistparentdd[]').addClass('multySelect').removeClass('singleselect');
            } else {
                parentSelect.removeAttr('multiple').attr('name', 'picklistparentdd').addClass('singleselect').removeClass('multySelect');
            }

            var grandParentSelect = $('#picklist_grand_parent_dd');
            grandParentSelect.empty().append('<option value="">Select</option>');
            if (setgrandparent) {
                $.each(response.grandparentdata, function(k, v) {
                    grandParentSelect.append('<option value="' + k + '">' + v + '</option>');
                });
                $('#parent_grand_dd_div').show();
            } else {
                $('#parent_grand_dd_div').hide();
            }

           
            var pagination = response.pagination;
            if (pagination && pagination.totalPages > 1) {
                var pagHtml = '<nav><ul class="pagination justify-content-end mb-0">';
                var current = pagination.currentPage;
                var total = pagination.totalPages;
                if(current > 1){
                    pagHtml += '<li class="page-item"><a class="page-link" href="#" data-page="'+(current-1)+'">&laquo;</a></li>';
                } else {
                    pagHtml += '<li class="page-item disabled"><span class="page-link">&laquo;</span></li>';
                }
                // max 5 numbers at a time
                var start = Math.max(1, current - 2), end = Math.min(total, current + 2);
                if (start > 1) { pagHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>'; }
                for (var i = start; i <= end; i++) {
                    if (i === current) {
                        pagHtml += '<li class="page-item active"><span class="page-link">' + i + '</span></li>';
                    } else {
                        pagHtml += '<li class="page-item"><a class="page-link" href="#" data-page="'+i+'">' + i + '</a></li>';
                    }
                }
                if (end < total) { pagHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>'; }
                if(current < total){
                    pagHtml += '<li class="page-item"><a class="page-link" href="#" data-page="'+(current+1)+'">&raquo;</a></li>';
                } else {
                    pagHtml += '<li class="page-item disabled"><span class="page-link">&raquo;</span></li>';
                }
                pagHtml += '</ul></nav>';
                $('#paginationContainer').html(pagHtml);
            } else {
                $('#paginationContainer').empty();
            }

            // Pagination click event
            $('#paginationContainer').off('click').on('click', '.page-link', function(e){
                e.preventDefault();
                var page = $(this).data('page');
                if (page && page > 0) {
                    getpicklisttablerows(selectedfieldid, page, pageSize);
                }
            });
            if (response.pagination) {
                renderPagination(response.pagination, selectedfieldid, pageSize);
            }
            stopLoading();
        },
        error: function() {
            stopLoading();
        }
    });
}

function renderPagination(pagination, selectedfieldid, pageSize) {
    const current = pagination.currentPage;
    const total = pagination.totalPages;
    const totalRecords = pagination.total;
    let html = '';

    html += `<div class="d-flex align-items-center">
        <span class="me-3">Showing page ${current} of ${total} (${totalRecords} records)</span>
        <ul class="pagination mb-0">`;

    if (current > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" data-page="${current-1}">&laquo;</a></li>`;
    }

    let start = Math.max(1, current - 2), end = Math.min(total, current + 2);
    for (let i = start; i <= end; i++) {
        html += `<li class="page-item${i === current ? ' active' : ''}">
            <a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
    }

    if (current < total) {
        html += `<li class="page-item"><a class="page-link" href="#" data-page="${current+1}">&raquo;</a></li>`;
    }
    html += '</ul></div>';

    $("#paginationContainer").html(html);

    $("#paginationContainer a.page-link").off('click').on('click', function(e) {
        e.preventDefault();
        let page = $(this).data('page');
        if (page) getpicklisttablerows(selectedfieldid, page, pageSize);
    });
}



  // $('#dynamicTable').DataTable({
  //       "paging": true,        // enable pagination
  //       "pageLength": 10,       // show 5 rows per page
  //       "lengthMenu": [5, 10, 25, 50],
  //       "searching": false,     // enable search box
  //       "ordering": false,      // enable column sorting
  //       "info": true           // show "Showing 1 to 5 of X entries"
  //   });
});

$(document).ready(function () {
    var rowsToShow = 10; // how many rows to show at a time
    var $rows = $("#dynamicTable tbody tr");
    var totalRows = $rows.length;

    // hide all rows initially
    $rows.hide();

    // show first N rows
    $rows.slice(0, rowsToShow).show();

    var currentIndex = rowsToShow;

    // detect scroll inside table container
    $("#tableContainer").on("scroll", function () {
        var $container = $(this);

        // check if user reached near bottom
        if ($container.scrollTop() + $container.innerHeight() >= this.scrollHeight - 10) {
            // show next set of rows
            $rows.slice(currentIndex, currentIndex + rowsToShow).fadeIn();
            currentIndex += rowsToShow;

            // stop when all rows are shown
            if (currentIndex >= totalRows) {
                // unbind scroll to prevent further checks
                $container.off("scroll");
            }
        }
    });
});
$(document).on('click', '#delete-picklist', async function () {
    const ok = await showConfirm(
        "Are you sure you want to delete all picklist items for this table?"
    );
    if (!ok) {
        return;
    }

    var fieldId = $('#picklisttablename').val();
    if (!fieldId) {
        alert('Please select Picklist Table.');
        return;
    }

    var recordIds = [];
    $('#dynamicTable tbody tr').each(function () {
        var id = $(this).data('id');
        if (id) {
            recordIds.push(id);
        }
    });

    if (!recordIds.length) {
        alert('No records to delete.');
        return;
    }

    var csrf = $('meta[name="csrf-token"]').attr('content');

    startLoading();
    $.ajax({
        type: 'POST',
        url: 'bulk-delete-picklist',
        data: {
            csrf: csrf,
            fieldId: fieldId,
            ids: recordIds
        },
        traditional: true,
        dataType: 'json',
        success: function (res) {
            stopLoading();
            if (res.status === 'success') {
                alert(res.message);
                let currentPageSize = 10;
                let selectedFieldId = $('#picklisttablename').val();
                if (selectedFieldId) {
                    getpicklisttablerows(selectedFieldId, 1, currentPageSize);
                }
            } else {
                alert(res.message || 'Something went wrong.');
            }
        },
        error: function () {
            stopLoading();
            alert('Error occurred. Please try again.');
        }
    });
});
