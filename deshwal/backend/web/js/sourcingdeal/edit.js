$(document).ready(function () {
  var newURL = window.location.href;
  var newURL = window.location.href;
  var module = "leads";
  var str = newURL.split(module);
  console.log("str" + str[0]);
  // var slicestr=newURL.substring(0,str);
  editusrl = str[0] + "leads/list";
  console.log("url" + editusrl);

  ///////////check mode of form////////
  const modeInput = document.getElementById("mode");
  if (modeInput && modeInput.value === "Create") {
    //hide stage when create mode change on date 20-06-25 by ptpatel as per erp issue list point no 5
    $(".section-stage").hide();
    //initialize stage with new = 1 value
    $("#stage").val("1").trigger("change");
    // initialize currency with INr
    $("#currency").val("1").trigger("change");
    getcurrency();
    hidewhilecreating();
  }
  
    detailincorrect();
  //erp change code start
  // hide type of contract filed if is_contract is not checked added on 20-06-25 as per ERP issue list point no 7
  $(".section-type_of_contract").hide();

  // Show/hide based on initial value
  if ($("#is_contract").val() == "1") {
    $(".section-type_of_contract").show();
  }

  // Toggle on dropdown change
  $("#is_contract").change(function () {
    if ($(this).val() == "1") {
      $(".section-type_of_contract").show();
    } else {
      $(".section-type_of_contract").hide();
    }
  });

  // 27 == lost sourceing deal stage
  function handleStageChange() {
    var val = $("#stage").val();
    if (val == "27") {
      $(".section-loss_reason").show();
    } else {
      $(".section-loss_reason").hide();
    }
  }

  $("#stage").change(function () {
    handleStageChange();
  });

  // Call it manually for pre-filled value
  handleStageChange();
  hideiscontract();

  function hidewhilecreating() {
    // List of field IDs or classes you want to check
    const fields = ["#contact_email", "#role", "#designation", "#department", "#contact_mobile"];
    // Loop through each field
    fields.forEach(function (selector) {
      const $field = $(selector);
      // Check if element exists
      if ($field.length) {
        const value = $field.val();
        // If value exists, do something (e.g., trigger change or set value)
        if (value) {
          console.log(selector + " has value: " + value);
          // If you want to trigger change
          $field.trigger("change");
        } else {
          // If value is empty, hide the field (or its wrapper section)
          console.log(selector + " is empty, hiding...");
          const className = selector.replace("#", ".section-");
          $(className).hide();
        }
      }
    });
  }

  $("#business_type").change(function () {
    hideiscontract();
  });
  function hideiscontract() {
    if ($("#business_type").val() == 3) {
      $("#is_contract").val("").trigger("change");
      $(".section-is_contract").hide();
      $(".section-type_of_contract").hide();
    }
    else {
      $("#is_contract").val("").trigger("change");
      $(".section-is_contract").show();
      //   if ($("#is_contract").val() == "1") {
      //   $(".section-type_of_contract").show();
      // }
    }
  }
  //erp change code end

  ////////////checkif business type is empty/////////
  // Create a MutationObserver to detect changes to the input opportuity_name1
  var targetNodeva = document.getElementById("vendor_account_name1");
  var observerva = new MutationObserver(function (mutationsListva) {
    for (var mutationva of mutationsListva) {
      if (
        mutationva.type === "attributes" &&
        mutationva.attributeName === "value"
      ) {
        //  getcontacts();

        console.log('vendor_account_name1 value changed to:', targetNodeva.value);

        getbusinesstype();
        getaccmangeranddeshwalisr(targetNodeva.value);

      }
    }
  });
  // Configuration for the observer (observe attribute changes)
  var configva = { attributes: true };
  if (targetNodeva) observerva.observe(targetNodeva, configva);
  var businesstype = $("#business_type").val();
  // $("#business_type").val('2').trigger("change");
  // alert(businesstype);
  if (
    $("#business_type").val() === "" ||
    (modeInput && modeInput.value === "Create")
  ) {
    getbusinesstype();

  }
  if($("#vendor_account_name1").val() !== "")
  {
    getaccmangeranddeshwalisr($("#vendor_account_name1").val());
  }
  function getbusinesstype() {
    var businesstype = $("#business_type").val();
    // $("#business_type").val('2').trigger("change");
    // alert(businesstype);

    // alert("test"+$("#business_type").val());
    data = {
      vendor_account_name: $("#vendor_account_name1").val(),
      _csrf: $("#csrfToken").val(),
    };
    //set business type if "Existing Business
    // New Business (No WON Opportunity in Last 2 Years – Make this Dynamic)"
    $.ajax({
      type: "POST",
      url: "checkopportunity",
      // async:false,
      data: data,
      success: function (response) {
        // alert("response ="+response.data);

        // Check if the data object exists and contains 'first_name'
        if (response) {
          // alert(response.data);
          if (response.data != "0") {
            $("#business_type").val("2").trigger("change");
          } else {
            $("#business_type").val("3").trigger("change");
          }
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




  //set closure month on change of closure date
  const closingDateInput = document.getElementById("closing_date");
  const closureMonthInput = document.getElementById("closure_month");

  // Add an event listener to the closing date input
  closingDateInput.addEventListener("change", function () {
    // Get the selected date in DD-MM-YYYY format
    const selectedDateString = this.value;

    // Split the date by '-'
    const dateParts = selectedDateString.split("-");

    if (dateParts.length === 3) {
      // Rearrange the date to YYYY-MM-DD format
      const formattedDate = `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`;

      // Create a new Date object with the corrected format
      const selectedDate = new Date(formattedDate);

      if (!isNaN(selectedDate)) {
        // Extract month and year from the selected date
        const month = selectedDate.getMonth() + 1; // Months are zero-based
        const year = selectedDate.getFullYear();

        // Set the closure month
        closureMonthInput.value = month;
        $("#closure_month").val(month).trigger("change");

        var wekno = getWeekNumber(selectedDate);
        // //   alert(wekno);
        $("#closure_week").val(wekno).trigger("change");

        // Set the closure year
        // closureYearInput.value = year;
      } else {
        // Clear the inputs if the date is invalid
        closureMonthInput.value = "";
      }
    } else {
      // If the date format is incorrect, clear the inputs
      closureMonthInput.value = "";
    }
  });

  function getWeekNumber(date) {
    var d = new Date(date);
    d.setHours(0, 0, 0, 0);
    // Set to the nearest Thursday
    d.setDate(d.getDate() + 3 - ((d.getDay() + 6) % 7));
    // Get first day of year
    var startOfYear = new Date(d.getFullYear(), 0, 1);
    // Calculate full weeks to the nearest Thursday
    var weekNo = Math.ceil(((d - startOfYear) / 86400000 + 1) / 7);
    return weekNo;
  }

  // get exchangerate
  $(document).on("change", "#currency", function () {
    getcurrency();
  });
  function getcurrency() {
    data = { currency: $("#currency").val(), _csrf: $("#csrfToken").val() };

    $.ajax({
      type: "POST",
      url: "getexchangerate",
      // async:false,
      data: data,
      success: function (data) {
        //window.location.href = editusrl;
        $("#exchange_rate").val(data);
      },
      error: function (data) {
        // if error occured

        alert("Error occured.please try again");
      },
      dataType: "html",
    });
  }
  //end exchange rate
  // get contact details
  // Create a MutationObserver to detect changes to the input vendor account
  var targetNode = document.getElementById("contact_name1");
  var observer = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
      if (
        mutation.type === "attributes" &&
        mutation.attributeName === "value"
      ) {
        getcontacts();

        console.log("contact_name1 value changed to:", targetNode.value);
      }
    }
  });
  // Configuration for the observer (observe attribute changes)
  var config = { attributes: true };
  observer.observe(targetNode, config);

  var contact_name1 = $("#contact_name1").val();
  if (contact_name1 != "") {
    getcontacts();
  }
  function getcontacts() {
    data = {
      contact_name: $("#contact_name1").val(),
      _csrf: $("#csrfToken").val(),
    };

    $.ajax({
      type: "POST",
      url: "getcontacts",
      // async:false,
      data: data,
      success: function (response) {
        console.log(response); // Log the entire response to check its structure

        // Check if the data object exists and contains 'first_name'
        if (response && response.data) {
          //$("#contact_name").val(response.data.first_name);
          // $("#contact_name1").val(response.data.contacts_id);
          $("#contact_mobile").val(response.data.mobile);
          $("#contact_email").val(response.data.email);
          $("#role").val(response.data.contact_role);
          $("#designation").val(response.data.designation);
          $("#department").val(response.data.department);
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
  //end get contacts
  //////check product is added
  function checkproducts(callback) {
    var st = false;
    var data = {
      record: $("#record").val(),
      _csrf: $("#csrfToken").val(),
    };

    $.ajax({
      type: "POST",
      url: "checkproducts",
      data: data,
      success: function (response) {
        console.log(response); // Log the entire response to check its structure

        if (response && response.data) {
          if (response.data == "1") {
            st = true;
          } else {
            st = false;
          }
        } else {
          console.log("Invalid response format or missing data");
          st = false;
        }

        callback(st); // Call the callback function with the result
      },
      error: function () {
        alert("Error occurred. Please try again");
        st = false;
        callback(st); // Return the result in case of error
      },
      dataType: "json",
    });
  }

  ///////////////check submit_for_pricing is cheked then disble it/////////
  var submit_for_pricing = document.getElementById("submit_for_pricing");
  if (submit_for_pricing) {
    if (submit_for_pricing.checked) {
      // Checkbox is checked
      $("#submit_for_pricing").prop("disabled", true);
      detailincorrect();
    } else {
      // Check if product is added for this record
      checkproducts(function (res) {
        if (res) {
          $("#submit_for_pricing").prop("disabled", false);
        } else {
          $("#submit_for_pricing").prop("disabled", true);
        }
      });
    }
  }

  ///////////////check special_pricing is cheked then disble it/////////
  var special_pricing = document.getElementById("special_pricing");
  if (special_pricing) {
    if (special_pricing.checked) {
      // Checkbox is checked
      $("#special_pricing").prop("disabled", true);
    } else {
      // Check if product is added for this record
      checkproducts(function (res) {
        if (res) {
          $("#special_pricing").prop("disabled", false);
        } else {
          $("#special_pricing").prop("disabled", true);
        }
      });
    }
  }
  ///////////////check costing_done is cheked then disble it/////////
  var costing_done = document.getElementById("costing_done");
  if (costing_done) {
    if (costing_done.checked) {
      // Checkbox is checked
      $("#costing_done").prop("disabled", true);
    } else {
      // Checkbox is not checked
      $("#costing_done").prop("disabled", false);
    }
  }
  ///////////on change stage to grn set forecast category///////////
  $(document).on("change", "#stage", function () {
    var stage = $(this).val();
    if (stage == 24)
      $("#forecast_category").val("4").trigger("change"); //pl created
    else if (stage == 11)
      //quote created
      $("#forecast_category").val("1").trigger("change"); //funnel
  });
  //end on change stage to grn set forecast category

  ///////////on change lead source to OEM set OEM  and OEM manger///////////
  var lead_source = $("#lead_source").val();
  // if(lead_source)
  setOEM(lead_source);

  $(document).on("change", "#lead_source", function () {
    var lead_source = $(this).val();
    console.log("lead_source"+lead_source);
    // if(lead_source)
    setOEM(lead_source);
  });
  $(document).on("change", "#oem", function () {
    getOemanager();
  });
  $(document).on("change", "#oem_manager", function () {
    getOemanagername();
  });
  $(document).on("change", "#oem_manager_name", function () {
    getoememail();
  });
  function getoememail() {
    const oem_manager_name = $("#oem_manager_name").val();
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    if (oem_manager_name) {
      $.ajax({
        type: "POST",
        url: "getoememail",
        data: { oem_manager_name: oem_manager_name, _csrf: csrfToken },
        dataType: "json",
        success: function (response) {
          if (response.status === "success") {
            $("#oem_manager_email").val(response.oememail);
          } else {
            $("#oem_manager_email").val("");
          }
        },
        error: function (xhr) {
          console.error(xhr);
          alert(
            "Error occurred while fetching OEM manger name. Please try again."
          );
        },
      });
    }
  }
  function setOEM(lead_source) {
    // alert(lead_source);
    if (lead_source == 12) { //corrected on date 13-08-25 as it not working while upload sourcing deal data from backend
      //OEM
      $("#oem").val(null).trigger("change");
      $("#oem").prop("disabled", false);
      // $("#oem_manager_name").val(null).trigger("change");
      // $("#oem_manager_name").prop("disabled",false);
      getOem();
    } else {
      $("#oem").val(null).trigger("change");
      $("#oem").prop("disabled", true);
      $("#oem_manager").val(null).trigger("change");
      $("#oem_manager").prop("disabled", true);

      $("#oem_manager_name").val(null).trigger("change");
      $("#oem_manager_name").prop("disabled", true);
    }
  }
  function getOem() {
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    const subcategoryDropdown = $("#oem")
      .empty()
      .append('<option value="">Select</option>');

    $.ajax({
      type: "POST",
      url: "getoem",
      data: { _csrf: csrfToken },
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          response.subcategories.forEach((subcategory) => {
            subcategoryDropdown.append(
              `<option value="${subcategory.id}">${subcategory.name}</option>`
            );
          });
          subcategoryDropdown.trigger("change"); // Update Select2 dropdown
        } else {
          // alert(response.message);
        }
      },
      error: function (xhr) {
        console.error(xhr);
        alert("Error occurred while fetching OEM. Please try again.");
      },
    });
  }
  function getOemanager() {
    const role = $("#oem").val();
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    const subcategoryDropdown = $("#oem_manager")
      .empty()
      .append('<option value="">Select</option>');

    if (role) {
      $.ajax({
        type: "POST",
        url: "getoemanager",
        data: { role: role, _csrf: csrfToken },
        dataType: "json",
        success: function (response) {
          if (response.status === "success") {
            response.oems.forEach((subcategory) => {
              subcategoryDropdown.append(
                `<option value="${subcategory.id}">${subcategory.name}</option>`
              );
            });
            subcategoryDropdown.trigger("change"); // Update Select2 dropdown
            $("#oem_manager").prop("disabled", false);
          } else {
            // alert(response.message);
            $("#oem_manager").val(null).trigger("change");
            $("#oem_manager").prop("disabled", true);
          }
        },
        error: function (xhr) {
          console.error(xhr);
          alert(
            "Error occurred while fetching OEM manger name. Please try again."
          );
        },
      });
    }
  }
  function getOemanagername() {
    const role = $("#oem_manager").val();
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    const subcategoryDropdown = $("#oem_manager_name")
      .empty()
      .append('<option value="">Select</option>');

    if (role) {
      $.ajax({
        type: "POST",
        url: "getoemanagername",
        data: { role: role, _csrf: csrfToken },
        dataType: "json",
        success: function (response) {
          if (response.status === "success") {
            response.oems.forEach((subcategory) => {
              subcategoryDropdown.append(
                `<option value="${subcategory.id}">${subcategory.name}</option>`
              );
            });
            subcategoryDropdown.trigger("change"); // Update Select2 dropdown
            $("#oem_manager_name").prop("disabled", false);
          } else {
            // alert(response.message);
            $("#oem_manager_name").val(null).trigger("change");
            $("#oem_manager_name").prop("disabled", true);
          }
        },
        error: function (xhr) {
          console.error(xhr);
          alert(
            "Error occurred while fetching OEM manger name. Please try again."
          );
        },
      });
    }
  }

  //end on change lead source to OEM set OEM  and OEM manger
  
//code added for CR point changes by ptpatel on date 10-09-2025
  //set closure month on change of closure date
  const commitDateInput = document.getElementById("commit_date");
  const commitMonthInput = document.getElementById("commit_month");

  $(document).on("change",'#commit_date', function () {
    console.log("on change fire of commit date");
    // Get the selected date in DD-MM-YYYY format
    const commitedDateString = $(this).val();

    // Split the date by '-'
    const commitdateParts = commitedDateString.split("-");

    if (commitdateParts.length === 3) {
      // Rearrange the date to YYYY-MM-DD format
      const commitformattedDate = `${commitdateParts[2]}-${commitdateParts[1]}-${commitdateParts[0]}`;

      // Create a new Date object with the corrected format
      const commitselectedDate = new Date(commitformattedDate);

      if (!isNaN(commitselectedDate)) {
        // Extract month and year from the selected date
        const commitmonth = commitselectedDate.getMonth() + 1; // Months are zero-based
        const commityear = commitselectedDate.getFullYear();

        // Set the closure month
        $(commitMonthInput).val(commitmonth);
        $("#commit_month").val(commitmonth).trigger("change");

        var commitwekno = getWeekNumber(commitselectedDate);
          console.log(commitwekno);
        $("#commit_week").val(commitwekno).trigger("change");

        // Set the closure year
        // $(closureYearInput).val(commityear);
      } else {
        // Clear the inputs if the date is invalid
        $(commitMonthInput).val("");
      }
    } else {
      // If the date format is incorrect, clear the inputs
      $(commitMonthInput).val("");
    }
});

function getaccmangeranddeshwalisr(acc_id)
{
   console.log("acc_id"+acc_id);
    const csrfToken_ = $('meta[name="csrf-token"]').attr("content");

    if (acc_id) {
      $.ajax({
        type: "POST",
        url: "getaccmgrandisr",
        data: { acc_id: acc_id, _csrf: csrfToken_ },
        dataType: "json",
        success: function (response) {
          if (response.status === "success") {
            $("#deshwal_isr").val(response.data.deshwal_isr_name);
            $("#deshwal_isr1").val(response.data.deshwal_isr_id); // Update Select2 dropdown
            $("#account_manager").val(response.data.account_manager_name);
            $("#account_manager1").val(response.data.account_manager_id);
          } else {
            // alert(response.message);
            console.log(response.message);
          }
        },
        error: function (xhr) {
          console.error(xhr);
          alert(
            "Error occurred while fetching Account manger and Deshwal ISR. Please try again."
          );
        },
      });
    }
}

// $(document).on("change",'[id^="no_gst_"]',function(){
//  // If any checkbox with class no-gst is checked
//     if ($('[id^="no_gst_"]:checked').length > 0) {
//         $('[id^="cgst_"], [id^="sgst_"], [id^="igst_"]').val(0);
//     }
// });
//end code added for CR points
});

/////////////////get logistic cost///////////////
$(document).on(
  "change",
  "#packing_cost, #vehicle_cost, #labour_cost, #local_union_charge, #engineer_cost, #misc, #halting_cost, #unloading_cost",
  function () {
    var packing_cost = parseFloat($("#packing_cost").val()) || 0;
    var vehicle_cost = parseFloat($("#vehicle_cost").val()) || 0;
    var labour_cost = parseFloat($("#labour_cost").val()) || 0;
    var local_union_charge = parseFloat($("#local_union_charge").val()) || 0;
    var engineer_cost = parseFloat($("#engineer_cost").val()) || 0;
    var misc = parseFloat($("#misc").val()) || 0;
    var halting_cost = parseFloat($("#halting_cost").val()) || 0;
    var unloading_cost = parseFloat($("#unloading_cost").val()) || 0;

    var total_logistics_cost =
      packing_cost +
      vehicle_cost +
      labour_cost +
      local_union_charge +
      engineer_cost +
      misc +
      halting_cost +
      unloading_cost;

    $("#total_logistics_cost").val(total_logistics_cost.toFixed(2));
    $("#logistics_cost").val(total_logistics_cost.toFixed(2));
  }
);

////////////master pricing on change repairing cost/////////////
$(document).on(
  "change",
  "#logistics_cost, #repairing_cost, #exp_cost,#additional_cost",
  function () {
    var logistics_cost = parseFloat($("#logistics_cost").val()) || 0;
    var repairing_cost = parseFloat($("#repairing_cost").val()) || 0;
    var exp_cost = parseFloat($("#exp_cost").val()) || 0;
    var sales_price_gst_exclude =
      parseFloat($("#sales_price_gst_exclude").val()) || 0;
    var cost_price_gst_exclude =
      parseFloat($("#cost_price_gst_exclude").val()) || 0;
    var additional_cost = parseFloat($("#additional_cost").val()) || 0;

    var actual_profit =
      sales_price_gst_exclude -
      cost_price_gst_exclude +
      logistics_cost +
      repairing_cost +
      exp_cost +
      additional_cost;

    $("#actual_profit").val(actual_profit.toFixed(2));
    var actual_profit_percentage =
      (actual_profit / sales_price_gst_exclude) * 100;
    $("#actual_profit_percentage").val(actual_profit_percentage.toFixed(2));
  }
);

///////enable costing_done/////////
var margin_percentage = $("#margin_percentage").val();
if (margin_percentage >= 30) {
  $(".section-costing_done").removeClass("tr-hidden");
} else {
  $(".section-costing_done").addClass("tr-hidden");
  // alert(actual_profit_percentage);
}
/////////on change prcing type///////////
$(document).on("change", "#pricing_type", function () {
  var pricing_type = $("#pricing_type").val();
  if (pricing_type == 1 || pricing_type == 2)
    $("#inspection_required").val("2").trigger("change");
  else $("#inspection_required").val("").trigger("change");
});
///////enable costing_done/////////
var margin_percentage = parseFloat($("#margin_percentage").val());
// alert(margin_percentage);

if (margin_percentage < 30) {
  $(".section-ceo_approval").removeClass("tr-hidden");
} else {
  $(".section-ceo_approval").addClass("tr-hidden");
}

////////////////approve reject/
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

  $.ajax({
    type: "POST",
    url: "approvesourcingdeal",
    data: data,
    success: function (data) {
      if (data.status === "success") location.reload();
      else alert("sometinhg went wrong");
    },
    error: function (data) {
      alert("Error occured.please try again");
    },
    dataType: "json",
  });
});
$(document).on("click", "#rejectsubmit", function () {
  let data = {
    Recordid: $("#Recordid").val(),
    _csrf: $("#csrfToken").val(),
    reject_reason: $("#reject_comment").val(),
  };
  if ($("#reject_comment").val() == "") {
    alert("Please enter comment!");
    $("#reject_comment").focus();
  } else {
    $.ajax({
      type: "POST",
      url: "approvesourcingdeal",
      data: data,
      success: function (data) {
        if (data.status === "success") location.reload();
        else alert("sometinhg went wrong");
      },
      error: function (data) {
        alert("Error occured.please try again");
      },
      dataType: "json",
    });
  }
});

//code added by ptpatel on date 13-10-2025 
//v11- 19 - When we send any Sourcing deal to Pricing Pending we have no option to reject or send back the sourcing deal to Owner. 
// please provide option when price desk will check the check box name "Details incorrect" & Update the remarks and then click on submit.
// Once done stage will changed to Qualified and owner change to the created by

var details_incorrect = document.getElementById("details_incorrect");
  if (details_incorrect) {
    if (details_incorrect.checked) {
      // Checkbox is checked
      $("#details_incorrect, #details_incorrect_remark").prop("disabled", true);
    } else {
      // Check if product is added for this record
      $("#details_incorrect, #details_incorrect_remark").prop("disabled", false);
    }
  }
  $(document).on("change", "#details_incorrect", function () {
        // Checked
        detailincorrect();
});
$(document).on("change", "#special_pricing,#ceo_approval,#costing_done", function () {
        // Checked
         $("#details_incorrect").prop("checked", false);
         $("#details_incorrect_remark").removeClass("V~M").addClass("V~O");
});

function detailincorrect()
{
  let details_incorrect_fields = [".section-details_incorrect",".section-details_incorrect_remark"];
  var submit_for_pricing = $("#submit_for_pricing").is(":checked");
  //if submit_for_pricing this will show details_incorrect_fields
  if(submit_for_pricing){
     $(details_incorrect_fields.join(",")).show();
     $("#details_incorrect_remark").addClass("V~M").removeClass("V~O");
  }
  else{
    $(details_incorrect_fields.join(",")).hide();
    $("#details_incorrect_remark").removeClass("V~M").addClass("V~O");
  }
  if($("#details_incorrect").is(":checked")){
    // uncheck this two
   $("#special_pricing, #ceo_approval,#costing_done").prop("checked", false);
   $(".section-details_incorrect_remark").show();
  }
  else {
    // $("#details_incorrect, #details_incorrect_remark").prop("disabled", false);
    $(".section-details_incorrect_remark").hide();
    $("#details_incorrect_remark").removeClass("V~M").addClass("V~O");
  }
}

$(document).ready(function () {
  var $stage = $('#stage');
  var initialVal = $stage.val();

  function restrictStageOptions() {
    var currentVal = $stage.val();

    if (initialVal === "14") {
      if (currentVal === "14") {
        $stage.find('option').each(function () {
          var optionVal = $(this).val();
          $(this).prop('disabled', !(optionVal === "14" || optionVal === "10"));
        });
      } else if (currentVal === "10") {
        $stage.find('option').each(function () {
          var optionVal = $(this).val();
          $(this).prop('disabled', !(optionVal === "10" || optionVal === "14"));
        });
      } else {
        $stage.find('option').prop('disabled', false);
      }
      $stage.trigger('change.select2');
    }
  }

  restrictStageOptions();

  $stage.off('select2:selecting').on('select2:selecting', function (e) {
    var selectingVal = e.params.args.data.id;
    var currentVal = $stage.val();
    if (initialVal === "14") {
      if (
        (currentVal === "14" && selectingVal !== "10") ||
        (currentVal === "10" && selectingVal !== "14")
      ) {
        e.preventDefault();
      }
      closuredateshowonWON();
    }
  });
  closuredateshowonWON();
});
//code added by ptpatel end here

//code added by ptpatel for v11 - point -123 on date 26-11-2025
$(document).on("change", "#stage", function () {
    closuredateshowonWON();
});
function closuredateshowonWON() {
    var stage = $('#stage').val();
    var closure_fields = ['#closing_date', '#closure_month', '#closure_week'];
    
   /** --------------------------
     * v11- 138 change Please ensure that the closure date field is made non-editable. It must capture the current date automatically when the stage is updated to WON.
     ----------------------------*/
    const el = document.querySelector("#closing_date");

    if (el) {
        let picker = el._flatpickr;
        if (!picker) {
            picker = flatpickr(el); // initialize if not initialized
        }
        picker.setDate(new Date(), true); // true = trigger change
    }

    closure_fields.forEach(selector => {
        var classStr = $(selector).attr("class") || "";

        // Toggle ~O / ~M
        if (stage === "14") {
            classStr = classStr.replace(/~O/g, "~M");
        } else {
            classStr = classStr.replace(/~M/g, "~O");
        }

        // Clean spaces
        classStr = classStr.replace(/\s+/g, " ").trim();

        // Remove error class if present
        if (classStr.includes("error")) {
            classStr = classStr.replace(/\berror\b/g, "").replace(/\s+/g, " ").trim();
            $(selector).closest(".form-group").find(".help-block").text('');
        }

        // Apply updated class
        $(selector).attr("class", classStr);

        // Show/Hide section
        var sectionClass = ".section-" + selector.replace("#", "");
        if (stage === "14") {
            $(sectionClass).show();
        } else {
            $(sectionClass).hide();
        }
    });
}

//end code added by ptpatel for v11 - point -123 on date 26-11-2025
// code added by ptpatel for disable ceo_approval on date 01-12-2025
 ///////////////check ceo_approval is cheked then disble it/////////
  var ceo_approval = document.getElementById("ceo_approval");
  if (ceo_approval) {
    if (ceo_approval.checked) {
      // Checkbox is checked
      $("#ceo_approval").prop("disabled", true);
    } else {
      // Checkbox is not checked
      $("#ceo_approval").prop("disabled", false);
    }
  }
  
// end code added by ptpatel for disable ceo_approval on date 01-12-2025

//code  added for RC and NON Rc and select contract related account
$(document).ready(function () {
    toggleRCField();
    $(document).on('change', '#type_of_contracts', function () {
        toggleRCField();
    });
    function toggleRCField() {
        var contractType = $('#type_of_contracts').val(); 
        if (contractType === '1') { 
            $('#type_of_rc')
                .prop('disabled', false)
                .closest('.form-group')
                .show();
            // loadRCContracts();
        } else {
            $('#type_of_rc')
                .val('')
                .prop('disabled', true)
                .closest('.form-group')
                .hide();
        }
    }

});
$(document).on('change', '#type_of_rc', function () {
    var rcValue = $(this).val(); 
    if (rcValue === 'no' || rcValue === '') {
        $('#type_of_contracts').val('').trigger('change'); 
        $('#type_of_rc')
            .val('')
            .prop('disabled', true)
            .closest('.form-group')
            .hide();
    }
});
//end code added for RC and NON Rc and select contract related account