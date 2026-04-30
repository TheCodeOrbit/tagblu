$(document).ready(function () {
  var newURL = window.location.href;

    // get exchangerate
    const modeInput = document.getElementById("mode");
    if (modeInput && modeInput.value === "Create") {
        // initialize stage with draft
        $("#status").val("1").trigger("change");
    }
    else
    {
      if (parseInt($("#send_for_approval").val()) == 1) { 
        $("#send_for_approval").prop(":checked",true);
      }
       if (parseInt($("#send_for_approval").val()) == 1 && $("#send_for_approval").is(":checked")) {
        $("#send_for_approval").prop("disabled", true);
      }
    }
    ///on country change get state
$(document).on("change", "#country", function () {
  data = { country: $(this).val(), _csrf: $("#csrfToken").val() };
  getstate(this);
});
///on state change get city
$(document).on("change", "#state", function () {
  data = { state: $(this).val(), _csrf: $("#csrfToken").val() };
  getcity(this);
});

function getstate(thisobj) {
  // alert("test"+thisobj.value);
  const country = $("#country").val();
  const csrfToken = $('meta[name="csrf-token"]').attr("content");

  // Reset dropdowns

  const stateDropdown = $("#state")
    .empty()
    .append('<option value="">Select</option>');

  if (country) {
    $.ajax({
      type: "POST",
      url: "getstate",
      data: { country: country, _csrf: csrfToken },
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          response.categories.forEach((state) => {
            stateDropdown.append(
              `<option value="${state.id}">${state.name}</option>`
            );
          });
          stateDropdown.trigger("change"); // Update Select2 dropdown
          // Hide the nearest validation message
          stateDropdown.closest("div").find(".help-block").hide();
        } else {
          alert(response.message);
        }
      },
      error: function (xhr) {
        console.error(xhr);
        alert("Error occurred while fetching categories. Please try again.");
      },
    });
  }
}
function getcity(thisobj) {
  // alert(thisobj.value);
  const state = thisobj.value;
  const csrfToken = $('meta[name="csrf-token"]').attr("content");

  // Reset dropdowns

  const cityDropdown = $("#city")
    .empty()
    .append('<option value="">Select</option>');

  if (state) {
    $.ajax({
      type: "POST",
      url: "getcity",
      data: { state: state, _csrf: csrfToken },
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          response.categories.forEach((city) => {
            cityDropdown.append(
              `<option value="${city.id}">${city.name}</option>`
            );
          });
          cityDropdown.trigger("change"); // Update Select2 
        } else {
          alert(response.message);
        }
      },
      error: function (xhr) {
        console.error(xhr);
        alert("Error occurred while fetching categories. Please try again.");
      },
    });
  }
}

  /////aprrove////////////
  $(document).on("click", "#approvesubmit", function () {
    let data = {
      Recordid: $("#Recordid").val(),
      _csrf: $("#csrfToken").val(),
      approve_reason: $("#approve_comment").val(),
    };
    if ($("#approve_comment").val() == "") {
      alert("Please enter comment!");
      $("#approve_comment").focus();
      return false;
    }
    $("#approvesubmit").prop("disabled",true);
    $.ajax({
      type: "POST",
      url: "approveraiserequestbyclient",
      data: data,
      success: function (data) {
         if (data.status === "success") 
          {
            $("#approvesubmit").prop("disabled",false);
            location.reload();
          }
          else 
          {
            $("#approvesubmit").prop("disabled",false);
            alert("sometinhg went wrong");
          }
      },
      error: function (data) {
        $("#approvesubmit").prop("disabled",false);
        alert("Error occured.please try again");
      },
      dataType: "json",
    });

  });
  //////reject/////////////
  $(document).on("click", "#rejectsubmit", function () {
    //alert("dfhfdhd");
    data = {
      Recordid: $("#Recordid").val(),
      _csrf: $("#csrfToken").val(),
      leadstatus_m: $("#leadstatus_m").val(),
      reject_reason: $("#reject_comment").val(),
    };
    // {leadstatus_v:$("#leadstatus_v").val(),Recordid: $('#Recordid').val();,approve_reason:$("#approve_reason").val();, _csrf: $('#csrfToken').val();};
    if ($("#reject_comment").val() == "") {
      alert("Please enter comment!");
      $("#reject_comment").focus();
    } else {
      $.ajax({
        type: "POST",
        url: "approveraiserequestbyclient",
        // async:false,
        data: data,
        success: function (data) {
          if (data.status === "success") location.reload();
          else alert("sometinhg went wrong");
        },
        error: function (data) {
          // if error occured

          alert("Error occured.please try again");
        },
        dataType: "json",
      });
    }

  });

    /*
    //this code is commented by ptpatel because generalize duplicate functionality is used on date 02-03-2026
    $(document).on("blur", "#acc_name", function () {
     const urlParams = new URLSearchParams(window.location.search);
    const recordid = urlParams.get('Record');
    console.log("acc blur"+recordid);
    var $input = $(this);
    var field = $input.attr("id");   // email or mobile
    var value = $input.val().trim();
    
    var $formGroup = $input.closest(".form-group"); 
    var $helpBlock = $input.closest("div").find(".help-block"); 
    if (value === "") {
       $formGroup.removeClass("error");
      $helpBlock.text(""); // clear old messages
        return; // skip empty
    }

    $.ajax({
        url: "../vendoraccount/isaccountduplicate",   
        type: "POST",
        data: {
            field: field,
            value: value,
            recordid : recordid,
            _csrf: yii.getCsrfToken() // important in Yii2
        },
        success: function (res) {
            if (res.exists) {
              $formGroup.addClass("error");
                $helpBlock.text(value + " already exists!");
            } else {
               if ($helpBlock.text().includes("already exists")) {
                    $helpBlock.text("");
                }
                $formGroup.removeClass("error");
            }
            toggleSaveButton();
        },
        error: function () {
            console.log("Error checking " + field);
             $formGroup.addClass("error");
        }
    });
    });*/

    function toggleSaveButton() {
      if ($(".form-group.error").length > 0 || $(".help-block:contains('required')").length > 0) {
          $(".savebutton").prop("disabled", true);
      } else {
          $(".savebutton").prop("disabled", false);
      }
    }
});
function toggleOrganizationFields() {
    var org = $('#organization').val(); 

    if (org == '1') { 
        $('.section-devit_vertical_manager').show();
        $('.section-devit_rsm_director').show();
        $('.section-devit_isr').show();
        $('.section-devit_business_manager').show();
        $('.section-isr_head').show();  
        $('.section-deshwal_isr').hide();
        $('.section-account_manager').hide();
        $('.section-deshwal_isr').hide()
          .find('input, select, textarea')
          .val('')
          .trigger('change');
        $('.section-account_manager').hide()
          .find('input, select, textarea')
          .val('')
          .trigger('change');
    } else if (org == '2') {
        $('.section-deshwal_isr').show();
        $('.section-account_manager').show();
        $('.section-isr_head').hide();
        $('.section-devit_vertical_manager').hide();
        $('.section-devit_rsm_director').hide();
        $('.section-devit_isr').hide();
        $('.section-devit_business_manager').hide();
        $('.section-isr_head').hide()
            .find('input, select, textarea')
            .val('')
            .trigger('change');
        $('.section-devit_vertical_manager').hide()
            .find('input, select, textarea')
            .val('')
            .trigger('change');
        $('.section-devit_rsm_director').hide()
            .find('input, select, textarea')
            .val('')
            .trigger('change');
        $('.section-devit_isr').hide()
            .find('input, select, textarea')
            .val('')
            .trigger('change');
        $('.section-devit_business_manager').hide()
            .find('input, select, textarea')
            .val('')
            .trigger('change');
    }
}

$(document).ready(function () {
    $('#organization').on('change', toggleOrganizationFields);
    toggleOrganizationFields(); 
});
