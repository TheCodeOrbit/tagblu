$(document).ready(function () {
  $(document).on("click", "#refresh-icon", function (e) {
    location.reload(); // Reload the current page
  });
  // $('.singleselect').select2({
  //             placeholder: '-- Select Field --',
  //             width: '100%'
  //         });
  /*$(document).on("click", ".savebutton", function (e) {
    isvalid = true;
    var form = document.getElementById("pristine-valid-example");
    errorMessage = "This filed is required";
    if ($("#exportrequest-module_name").val() == '') {
      var errorElement = $("#exportrequest-module_name").closest(".form-group").find(".help-block");
      errorElement.html(errorMessage); // Replace errorMessage with the actual message   
      isvalid = false;
    }
    let fromDate = $("#exportrequest-from_date").val();
    let toDate = $("#exportrequest-to_date").val();

    if (!fromDate) {
      var errorElement = $("#exportrequest-from_date").closest(".form-group").find(".help-block");
      errorElement.html(errorMessage); // Replace errorMessage with the actual message   
      isValid = false;
    }

    if (!toDate) {
      var errorElement = $("#exportrequest-to_date").closest(".form-group").find(".help-block");
      errorElement.html(errorMessage); // Replace errorMessage with the actual message  
      isValid = false;
    }

    if (fromDate && toDate && fromDate === toDate) {
      errorMessage = "From Date and To Date cannot be same.";
      var errorElement = $("#exportrequest-from_date").closest(".form-group").find(".help-block");
      errorElement.html(errorMessage); // Replace errorMessage with the actual message
      isValid = false;
    }
    else {
      $("#exportrequest-from_date").closest(".form-group").find(".help-block").html('');
    }

    if (isvalid)
      form.submit();
  });
  $(document).on("change", "#exportrequest-from_date", function (e) {
    let fromDate = $("#exportrequest-from_date").val();
    let toDate = $("#exportrequest-to_date").val();

    if (fromDate !== toDate) {
      $("#exportrequest-from_date").closest(".form-group").find(".help-block").html('');
    }
  });
*/

  $(document).on("click", ".savebutton", function (e) {
  e.preventDefault(); // prevent form submit until validation is done
  let isValid = true;
  const form = document.getElementById("pristine-valid-example");
  const errorMessage = "This field is required";

  const exportAllChecked = $("#exportrequest-export_all").is(":checked");
  const fromDateInput = $("#exportrequest-from_date");
  const toDateInput = $("#exportrequest-to_date");

  // If Export All is checked → disable date fields and clear errors
  if (exportAllChecked) {
    fromDateInput.prop("disabled", true).val("");
    toDateInput.prop("disabled", true).val("");
    fromDateInput.closest(".form-group").find(".help-block").html('');
    toDateInput.closest(".form-group").find(".help-block").html('');
  } else {
    // Enable date fields back if previously disabled
    fromDateInput.prop("disabled", false);
    toDateInput.prop("disabled", false);
  }

  // Module Name validation
  if ($("#exportrequest-module_name").val().trim() === '') {
    const errorElement = $("#exportrequest-module_name")
      .closest(".form-group")
      .find(".help-block");
    errorElement.html(errorMessage);
    isValid = false;
  } else {
    $("#exportrequest-module_name")
      .closest(".form-group")
      .find(".help-block")
      .html('');
  }

  // Only validate dates if Export All is NOT checked
  if (!exportAllChecked) {
    const fromDate = fromDateInput.val();
    const toDate = toDateInput.val();

    // From Date
    if (!fromDate) {
      const errorElement = fromDateInput.closest(".form-group").find(".help-block");
      errorElement.html(errorMessage);
      isValid = false;
    } else {
      fromDateInput.closest(".form-group").find(".help-block").html('');
    }

    // To Date
    if (!toDate) {
      const errorElement = toDateInput.closest(".form-group").find(".help-block");
      errorElement.html(errorMessage);
      isValid = false;
    } else {
      toDateInput.closest(".form-group").find(".help-block").html('');
    }

    // Same Date validation
    if (fromDate && toDate && fromDate === toDate) {
      const errorElement = fromDateInput.closest(".form-group").find(".help-block");
      errorElement.html("From Date and To Date cannot be the same.");
      isValid = false;
    }
  }

  // Submit only if valid
  if (isValid) form.submit();
});


//  When Export All checkbox changes
$(document).on("change", "#exportrequest-export_all", function () {
  const fromDateInput = $("#exportrequest-from_date");
  const toDateInput = $("#exportrequest-to_date");

  if ($(this).is(":checked")) {
    // Disable both date fields and clear values/errors
    fromDateInput.prop("disabled", true).val("");
    toDateInput.prop("disabled", true).val("");
    fromDateInput.closest(".form-group").find(".help-block").html('');
    toDateInput.closest(".form-group").find(".help-block").html('');
  } else {
    // Re-enable date fields
    fromDateInput.prop("disabled", false);
    toDateInput.prop("disabled", false);
  }
});


  // Clear error message dynamically when From Date changes
  $(document).on("change", "#exportrequest-from_date, #exportrequest-to_date", function () {
    const fromDate = $("#exportrequest-from_date").val();
    const toDate = $("#exportrequest-to_date").val();

    if (fromDate && toDate && fromDate !== toDate) {
      $("#exportrequest-from_date")
        .closest(".form-group")
        .find(".help-block")
        .html('');
    }
  });


  $("#dtrecord").DataTable({
    processing: true,
    serverSide: false,
    ajax: "exportrequestdata",
    columns: [
      { data: "export_request_no" },
      { data: "from_date" },
      { data: "to_date" },
      { data: "module_name" },
      { data: "report_link" },
      // { data: "action" },
    ],
  });
  const modeInput = document.getElementById("mode");
  if (modeInput && modeInput.value === "Create") {
    getmoduleName();
  }
  else if (modeInput && modeInput.value === "edit") {
    getmoduleName();
  }

  function getmoduleName() {
    // alert(thisobj.value);
    startLoading();
    console.log("calling");
    const csrfToken = $('meta[name="csrf-token"]').attr("content");


    const selectedModuleId = $("#module_name").val();
    console.log("selectedModuleId=" + selectedModuleId);
    // Reset dropdowns
    const moduleDropdown = $("#module_name")
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
          // response.data.forEach((module) => {
          //   moduleDropdown.append(`<option value="${module}">${module}</option>`);
          //   moduleDropdown.append(
          //     `<option value="${module.id}">${module.name}</option>`
          //   );
          // });
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


});