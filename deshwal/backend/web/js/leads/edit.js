$(document).ready(function () {
  var newURL = window.location.href;
  var newURL = window.location.href;
  var module = "leads";
  var str = newURL.split(module);
  console.log("str" + str[0]);
  // var slicestr=newURL.substring(0,str);
  editusrl = str[0] + "leads/list";
  console.log("url" + editusrl);

  //show data validated only if lead_status == 15
  lead_status_val = $("#leadstatus").val();

  //if checked then disable data_validated////
  var data_validated = document.getElementById("data_validated");
  if (data_validated) {
    if (data_validated.checked) {
      // Checkbox is checked
      $("#data_validated").prop("disabled", true);
    } else {
      if (lead_status_val != 15)//show data validated only if lead_status == 15
        $(".section-data_validated").addClass("tr-hidden");
      $("#data_validated").prop("disabled", false);
    }
  }

  //if checked then disable ready_to_pitch////
  var ready_to_pitch = document.getElementById("ready_to_pitch");
  if (ready_to_pitch) {
    if (ready_to_pitch.checked) {
      // Checkbox is checked
      $("#ready_to_pitch").prop("disabled", true);
    } else {
      if (lead_status_val != 16)//show data validated only if lead_status == 16
        $(".section-ready_to_pitch").addClass("tr-hidden");
      $("#ready_to_pitch").prop("disabled", false);
    }
  }

  /////////get data mining users if lead status = new
  if (lead_status_val == 14) {
    startLoading();
    const csrfToken = $('meta[name="csrf-token"]').attr("content");
    const oemDropdown = $("#ownerid").empty().append('<option value="">Select</option>');
    $.ajax({
      type: "POST",
      url: "getdatamining",
      data: { _csrf: csrfToken },
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          response.users.forEach((oem) => {
            oemDropdown.append(`<option value="${oem.id}">${oem.fullname}</option>`);
          });
          oemDropdown.trigger('change'); // Update Select2 dropdown
          stopLoading();

        } else {
          alert(response.message);
          stopLoading();

        }
      },
      error: function (xhr) {
        console.error(xhr);
        alert("Error occurred while fetching OEMs. Please try again.");
        stopLoading();

      },
    });
  }
  //

  var dropdown = document.getElementById("reject_Reason");
  var otherInputContainer = document.getElementById("otherInputContainer");

  function toggleOtherInput() {
    if (dropdown.value === "3") {
      otherInputContainer.style.display = "block"; // Show input field
    } else {
      otherInputContainer.style.display = "none"; // Hide input field
    }
  }

  // Attach event listener
  if (dropdown) {
    dropdown.addEventListener("change", toggleOtherInput);
  }

  // added on 18 jan 2025
  $(document).on("click", "#reactivatesubmit", function () {
    data = {
      Recordid: $("#Recordid").val(),
      _csrf: $("#csrfToken").val(),
      leadstatus_reactivate: $("#leadstatus_reactivate").val(),
      reactivate_reason: $("#reactivate_comment").val(),
    };
    // {leadstatus_v:$("#leadstatus_v").val(),Recordid: $('#Recordid').val();,approve_reason:$("#approve_reason").val();, _csrf: $('#csrfToken').val();};
    if ($("#reactivate_comment").val() == "") {
      alert("Please enter comment!");
      $("#reactivate_comment").focus();
    } else {
      $.ajax({
        type: "POST",
        url: "approvelead",
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
    // alert("dfhfdhd");
  });

  $(document).on("click", "#approvesubmit", function () {
    data = {
      Recordid: $("#Recordid").val(),
      _csrf: $("#csrfToken").val(),
      leadstatus_v: $("#leadstatus_v").val(),
      approve_reason: $("#approve_comment").val(),
    };
    // {leadstatus_v:$("#leadstatus_v").val(),Recordid: $('#Recordid').val();,approve_reason:$("#approve_reason").val();, _csrf: $('#csrfToken').val();};
    if ($("#approve_comment").val() == "") {
      alert("Please enter comment!");
      $("#approve_comment").focus();
    } else {
      $.ajax({
        type: "POST",
        url: "approvelead",
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
  $(document).on("click", "#delegatesubmit", function () {
    // alert("dfhfdhd");
    data = {
      Recordid: $("#Recordid").val(),
      _csrf: $("#csrfToken").val(),
      new_vm: $("#new_vm").val(),
      delegate_reason: $("#delegate_comment").val(),
    };
    // {leadstatus_v:$("#leadstatus_v").val(),Recordid: $('#Recordid').val();,approve_reason:$("#approve_reason").val();, _csrf: $('#csrfToken').val();};
    // alert($("#new_vm").val());
    if ($("#new_vm").val() == "") {
      alert("Please select user!");
      $("#new_vm").focus();
    } else if ($("#delegate_comment").val() == "") {
      alert("Please enter comment!");
      $("#delegate_comment").focus();
    } else {
      $.ajax({
        type: "POST",
        url: "approvelead",
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
    // alert("dfhfdhd");
  });

  $(document).on("click", "#modifysubmit", function () {
    // alert("dfhfdhd");
    data = {
      Recordid: $("#Recordid").val(),
      _csrf: $("#csrfToken").val(),
      leadstatus_m: $("#leadstatus_m").val(),
      modify_reason: $("#modify_comment").val(),
    };
    // {leadstatus_v:$("#leadstatus_v").val(),Recordid: $('#Recordid').val();,approve_reason:$("#approve_reason").val();, _csrf: $('#csrfToken').val();};
    if ($("#modify_comment").val() == "") {
      alert("Please enter comment!");
      $("#modify_comment").focus();
    } else {
      $.ajax({
        type: "POST",
        url: "approvelead",
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
    // alert("dfhfdhd");
  });
  $(document).on("click", "#rejectsubmit", function () {
    let rejectComment = $("#reject_comment").val().trim();
    let rejectReason = $("#reject_Reason").val();
    let otherReason = $("#otherInput").val().trim();

    // alert("dfhfdhd");
    data = {
      Recordid: $("#Recordid").val(),
      _csrf: $("#csrfToken").val(),
      leadstatus_v: $("#leadstatus_r").val(),
      reject_reason: $("#reject_comment").val(),
      reject_type: $("#reject_Reason").val(),
      other_reason: $("#otherInput").val(),
    };
    // {leadstatus_v:$("#leadstatus_v").val(),Recordid: $('#Recordid').val();,approve_reason:$("#approve_reason").val();, _csrf: $('#csrfToken').val();};
    // Validate Reject Reason

    if (rejectComment === "") {
      alert("Please enter a comment!");
      $("#reject_comment").focus();
      return;
    }

    if (rejectReason === "Select the Reason") {
      alert("Please select a valid rejection reason!");
      $("#reject_Reason").focus();
      return;
    }

    // Validate Other Reason if "Other" is selected
    if (rejectReason === "3" && otherReason === "") {
      alert("Please enter a reason for rejection!");
      $("#otherInput").focus();
      return;
    }
    // AJAX Request
    $.ajax({
      type: "POST",
      url: "approvelead",
      data: data,
      success: function (data) {
        if (data.status === "success") {
          location.reload();
        } else {
          alert("Something went wrong");
        }
      },
      error: function () {
        alert("Error occurred. Please try again.");
      },
      dataType: "json",
    });
    // alert("dfhfdhd");
  });
  let today = new Date().toISOString().split("T")[0];
  lead_status = $("#leadstatus option:selected").text();


  $("#contact_future_date").attr("min", today); //only ffuture date
  $(".section-not_contacted_reason").addClass("tr-hidden");
  $(".section-disqualified_reason").addClass("tr-hidden");
  $(".section-not_interested_reason").addClass("tr-hidden");
  $(".section-contact_future_date").addClass("tr-hidden");

  if (lead_status != "") setleadstatus(lead_status);

  $("#leadstatus").change(function () {
    lead_status = $("#leadstatus option:selected").text();

    setleadstatus(lead_status);
  });
  function setleadstatus(lead_status) {
    // alert(lead_status);
    if (lead_status == "Lead Created" || lead_status == "New" || lead_status == "Data Verification" || lead_status == "Ready For Calling") {
      $(".section-duplicate_lead_reference_id").addClass("tr-hidden");
    } else if (lead_status == "Contact in Future") {
      // alert("test = "+$("#contact_future_date").val());
      $(".section-contact_future_date").removeClass("tr-hidden");
      $(".section-not_contacted_reason").addClass("tr-hidden");
      $(".section-disqualified_reason").addClass("tr-hidden");
      $(".section-not_interested_reason").addClass("tr-hidden");
      $(".section-duplicate_lead_reference_id").addClass("tr-hidden");
      //$("#contact_future_date").val('');
      $("#not_contacted_reason").val("");
      $("#disqualified_reason").val("");
      $("#not_interested_reason").val("");
    } else if (lead_status == "Disqualified") {
      $(".section-disqualified_reason").removeClass("tr-hidden");
      $(".section-not_contacted_reason").addClass("tr-hidden");
      $(".section-not_interested_reason").addClass("tr-hidden");
      $(".section-contact_future_date").addClass("tr-hidden");
      $(".section-duplicate_lead_reference_id").addClass("tr-hidden");
      $("#contact_future_date").val("");
      $("#not_contacted_reason").val("");
      //$("#disqualified_reason").val('');
      $("#not_interested_reason").val("");
    } else if (lead_status == "Not Interested") {
      //alert("test = "+$("#not_contacted_reason").val());
      $(".section-not_interested_reason").removeClass("tr-hidden");
      $(".section-not_contacted_reason").addClass("tr-hidden");
      $(".section-disqualified_reason").addClass("tr-hidden");
      $(".section-contact_future_date").addClass("tr-hidden");
      $(".section-duplicate_lead_reference_id").addClass("tr-hidden");
      $("#contact_future_date").val("");
      $("#not_contacted_reason").val("");
      $("#disqualified_reason").val("");
      //$("#not_interested_reason").val('');
    } else if (lead_status == "Not Contacted") {
      $(".section-not_contacted_reason").removeClass("tr-hidden");
      $(".section-disqualified_reason").addClass("tr-hidden");
      $(".section-not_interested_reason").addClass("tr-hidden");
      $(".section-contact_future_date").addClass("tr-hidden");
      $(".section-duplicate_lead_reference_id").addClass("tr-hidden");
      $("#contact_future_date").val("");
      //$("#not_contacted_reason").val('');
      $("#disqualified_reason").val("");
      $("#not_interested_reason").val("");
    }
    // duplicate lead section show only status is duplicate lead
    else if (lead_status == "Duplicate Lead") {
      $(".section-duplicate_lead_reference_id").removeClass("tr-hidden");
    }
    else {
      $(".section-not_contacted_reason").addClass("tr-hidden");
      $(".section-disqualified_reason").addClass("tr-hidden");
      $(".section-not_interested_reason").addClass("tr-hidden");
      $(".section-contact_future_date").addClass("tr-hidden");
      // below line commented to hide duplidcate lead section 
      // $(".section-duplicate_lead_reference_id").removeClass("tr-hidden");
      $(".section-duplicate_lead_reference_id").addClass("tr-hidden");
      $("#contact_future_date").val("");
      $("#not_contacted_reason").val("");
      $("#disqualified_reason").val("");
      $("#not_interested_reason").val("");
    }
  }
  /*var form = document.getElementById("pristine-valid-example");
  var pristine = new Pristine(form);
  $(document).on('click', '.savebutton', function (e) {
    // alert("checking");
    // $('.savebutton').click(function(e){
    console.log("clicked");

    var isValid = true;
    console.log("teregdfg fh");




    var valid = pristine.validate();
    if (valid && isValid) {
      form.submit();
    }


  });*/
});

document.querySelectorAll(".accordion-toggle").forEach((button) => {
  button.addEventListener("click", () => {
    const content = button
      .closest(".accordion-item")
      .querySelector(".accordion-content");
    const upArrow = button.querySelector(".up");
    const downArrow = button.querySelector(".down");
    if (content.style.display === "block") {
      content.style.display = "none"; // Hide content
      upArrow.style.display = "none"; // Hide up arrow
      downArrow.style.display = "inline"; // Show down arrow
    } else {
      content.style.display = "block"; // Show content
      upArrow.style.display = "inline"; // Show up arrow
      downArrow.style.display = "none"; // Hide down arrow
    }
  });
});
// Tab Switching Logic
document.querySelectorAll(".tab").forEach((tab) => {
  tab.addEventListener("click", function () {
    // Remove active class from all tabs and contents
    document
      .querySelectorAll(".tab")
      .forEach((t) => t.classList.remove("active"));
    document
      .querySelectorAll(".tab-content-detail-view")
      .forEach((content) => content.classList.remove("active"));
    // Add active class to clicked tab and corresponding content
    this.classList.add("active");
    const tabId = this.getAttribute("data-tab");
    document.getElementById(tabId).classList.add("active");
  });
});

// get exchangerate
// Initialize Select2 for all dropdowns
$("#currency").select2();
// Listen for the change event on select2
//  $('#currency').on('select2:select', function (e) {
// Listen for the change event on select2
$("#currency").on("change", function (e) {
  var selectedValue = e.target.value; // Get the selected value
  console.log("Selected value: ", selectedValue);
  data = { currency: selectedValue, _csrf: $("#csrfToken").val() };
  $("#exchange_rate").val("");

  $.ajax({
    type: "POST",
    url: "getexchangerate",
    // async:false,
    data: data,
    success: function (data) {
      //location.reload();
      $("#exchange_rate").val(data);
    },
    error: function (data) {
      // if error occured

      alert("Error occured.please try again");
    },
    dataType: "html",
  });
});
//end exchange rate

document.addEventListener("DOMContentLoaded", function () {
  // Check if mode is 'Create'
  const modeInput = document.getElementById("mode");
  if (modeInput && modeInput.value === "Create") {
    // Select the dropdown by ID
    const leadStatusSelect = $("#leadstatus"); // Using jQuery for Select2

    //show only lead created  added on 16 jan 2025 by deepika
    // Hide all options except the one with a specific value
    $("#leadstatus option").each(function () {
      if ($(this).val() != "1") {
        // Show only the option with value "1" = lead created
        $(this).remove(); // Remove options that don't match
      }
    });
    // initialize currency with INr
    $("#currency").val("1").trigger("change");
    //end ddepika

    if (leadStatusSelect.length) {
      // Set the default value for Select2 dropdown
      leadStatusSelect.val("1").trigger("change"); // Use the value corresponding to "New Lead Created"
    }
  }
});

////////get vendor name and KYC//////////////
// Create a MutationObserver to detect changes to the input vendor account
var targetNode = document.getElementById("vendor1");
var observer = new MutationObserver(function (mutationsList) {
  for (var mutation of mutationsList) {
    if (mutation.type === "attributes" && mutation.attributeName === "value") {
      getvendordetail();
      console.log("vendor1 value changed to:", targetNode.value);
    }
  }
});

// Configuration for the observer (observe attribute changes)
var config = { attributes: true };
observer.observe(targetNode, config);
function getvendordetail() {
  data = {
    account_name: $("#vendor1").val(),
    _csrf: $("#csrfToken").val(),
  };

  //  $("#noofemployees").val('');

  $("#industry").val(null).trigger("change");
  //  $("#annualrevenue").val('');

  $.ajax({
    type: "POST",
    url: "getvendordetail",
    // async:false,
    data: data,
    success: function (response) {
      console.log(response); // Log the entire response to check its structure

      // Check if the data object exists and contains 'first_name'
      if (response && response.data) {
        // $("#noofemployees").val(response.data.empsize_value);
        $("#industry").val(response.data.industry);
        $("#industry").val(response.data.industry).trigger("change");
        // $("#annualrevenue").val(response.data.annual_revenue);
      } else {
        console.log("Invalid response format or missing data");
      }
    },
    error: function (data) {
      // if error occured

      alert("Error occured.please try again");
    },
    dataType: "json",
  });
}
//////////////change event on customer type//////////
$("#customer_type").on("change", function (e) {
  var selectedValue = e.target.value; // Get the selected value
  console.log("Selected value: ", selectedValue);
  showcutomermandatory(selectedValue);
});
// check customer name
cutomertype = $("#customer_type").val();
showcutomermandatory(cutomertype);

function showcutomermandatory(selectedValue) {
  if (selectedValue == 1) {
    //new customer
    $("#vendor").removeClass("V~M");
    // $(".section-send_for_approval").addClass("tr-hidden");
    //also hide vendor account
    $(".section-vendor").addClass("tr-hidden");
    $("vendor1").val("");
    $("vendor").val("");
    //show account name
    $(".section-account_name ").removeClass("tr-hidden");
    // Uncheck the checkbox
    // $("#send_for_approval").prop("checked", false);

    // Remove the asterisk based on a condition
    toggleAsterisk(false); // This will remove the asterisk
  } else {
    $("#vendor").addClass("V~M");
    // $(".section-send_for_approval").removeClass("tr-hidden");
    //show vendor account
    $(".section-vendor").removeClass("tr-hidden");
    //hide account name
    $(".section-account_name ").addClass("tr-hidden");
    $("account_name").val("");
    // Add the asterisk based on a condition
    toggleAsterisk(true); // This will add the asterisk
  }
}
// Function to toggle the asterisk
function toggleAsterisk(shouldAdd) {
  var label = $('label[for="vendor"]');
  var asterisk = label.find("span.red"); // Check if the span with the red class exists

  if (shouldAdd && asterisk.length === 0) {
    // Add the asterisk if the condition is met and the span doesn't already exist
    label.append('<span class="red"> *</span>');
  } else if (!shouldAdd && asterisk.length > 0) {
    // Remove the asterisk if the condition is not met and the span exists
    asterisk.remove();
  }
}
//////////////change event on first name last name set lead name//////////
$("#firstname").on("change", function (e) {
  setleadname();
});
$("#lastname").on("change", function (e) {
  setleadname();
});
function setleadname() {
  var first_name = $("#firstname").val(); // Get the selected value
  var last_name = $("#lastname").val(); // Get the selected value
  leadname = first_name + " " + last_name;
  $("#leadname").val(leadname);
}
/////////restrict phone nuber editing on changes required////////////
// var leadstatus = $("#leadstatus").val();
// var email = $("#email").val();
// if(leadstatus == 3)
// {
//   $("#phone").attr("readonly","readonly");
//   $("#email").attr("readonly","readonly");
//   $("#vendor").attr("readonly","readonly");
//   $(".search-icon").css("display","none");
//   $(".icon-left").css("display","none");
//   $(".icon-right").css("display","none");
//   $(".section-send_for_approval").removeClass("tr-hidden");
// }
// else{
//   $(".section-send_for_approval").addClass("tr-hidden");

// }

///////////////on save button click//////
// $(".savebutton").on("click", function (e) {
//   // alert("deep");
//   $("#vendor").removeClass("V~M");
//   var vendor1 = $("#vendor1").val();
//   var account_name = $("#account_name").val();
//   if(account_name == '' && vendor1 == '')
//   {
//     errorMessage = "Please Select an Account or Add an Account Name! ";
//     // alert("Select a account or Add a Account Name!");
//     var errorElement = $("#vendor1").closest(".form-group").find(".help-block");
//     errorElement.html(errorMessage); // Replace errorMessage with the actual message
//     alert(errorElement.html());

//     var errorElement = $("#account_name").closest(".form-group").next(".help-block");
//     errorElement.html(errorMessage); // Replace errorMessage with the actual message
//   }
//   return false;
// });

 $(document).on("change", "#customer_type", function () {toggleSaveButton();});
 $(document).on("blur", "#account_name", function () {
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
    startLoading();
    $.ajax({
        url: "isaccountduplicate",   
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
                $helpBlock.text(value + " already exists please select appropriate Account Type!");
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
        },
        complete : function ()
        {stopLoading();}
    });
    });

    function toggleSaveButton() {
      // Check errors only on VISIBLE form-groups
    var hasVisibleErrors = $(".form-group.error:visible").length > 0;

    // Required message only if visible
    var hasRequiredErrors = $(".help-block:visible:contains('required')").length > 0;

    if (hasVisibleErrors || hasRequiredErrors) {
      // if ($(".form-group.error").length > 0 || $(".help-block:contains('required')").length > 0) {
          $(".savebutton").prop("disabled", true);
      } else {
          $(".savebutton").prop("disabled", false);
      }
    }