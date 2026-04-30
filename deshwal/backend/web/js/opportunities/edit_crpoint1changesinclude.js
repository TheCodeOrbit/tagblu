$(document).ready(function () {
  ///////////check mode of form////////
  const modeInput = document.getElementById("mode");
  if (modeInput && modeInput.value === "Create") {
    //initialize stage with new = 1 value
    $("#opportunity_stage").val("1").trigger("change");
    addRowBtn("2681", "opportunities");

  } else if (modeInput && modeInput.value === "Edit") {
    toggleBlocks($("#opportunity_stage").val().trim());
  }
  else {
    if ($('#productTable2681 tbody tr').length == 0) {
      // Call the function to add the row (assuming addRowBtn is an async function)
      addRowBtn("2681", "opportunities", function () {
        // Once the row is added, disable the first row's remove button
        //$('#productTable2681 tbody tr:first-child .remove-row-btn').prop('disabled', true);
      });
    }

  }
  //disable auto dd of stage
  const stageSelect = document.getElementById("opportunity_stage");
  if (stageSelect) {
    const options = stageSelect.options;
    var stage = document.getElementById("opportunity_stage").value;

    for (let i = 0; i < options.length; i++) {
      //alert(options[i].value);
      if (stage != options[i].value) {
        if (options[i].value !== "6" && options[i].value !== "7" && options[i].value !== "8" && options[i].value !== "9") {
          options[i].disabled = true;
          // options[i].text += " (Disabled due to total invoice and total payment amount are mismatch)";
          //break;
        }
        else {
          if (stage != 10 && stage != 6 && stage != 7 && stage != 8 && stage != 9)
            options[i].disabled = true;

        }
      }

    }
    toggleBlocks(stage);
  }
  $(".move_to_prospect").on("click", function (e) {
    e.preventDefault(); 
     if (!confirm('The related record will also be reset. Do you want to continue?')) {
        return;
    }
    record = $('#recordid').val();
    $.ajax({
        url: 'edit?Record='+record,
        type: 'POST',
        data: {
            mode: 'Edit',
            module: 'opportunities',
            'opportunity[opportunity_stage]': 1, 
            is_set_to_prospect: 1,
            _csrf: $("#csrfToken").val(),
        },
        success: function (response) {
            window.location.reload();
        },
        error: function (xhr, status, error) {
            window.location.reload();
            console.error('Error:', status, error);
        }
        });
     });
  ////fetch account detail on load/////////
  let zone_region = $("#zone_region").val();
  let team_name = $("#team_name").val();
  let account_manager = $("#account_manager").val();
  let business_manager = $("#business_manager").val();
  let account_director_rsm = $("#account_director_rsm").val();
  let account_director_rsm1 = $("#account_director_rsm1").val();
  let devit_isr = $("#devit_isr").val();
  let devit_isr1 = $("#devit_isr1").val();
  let devit_vertical_manager = $("#devit_vertical_manager").val();
  let devit_vertical_manager1 = $("#devit_vertical_manager1").val();
  // if (!zone_region && !team_name && !account_manager && !business_manager && !account_director_rsm) {
  getaccountdetail();
  // }

  /////////////create mutation for vendor_account_name1/////////////////
  // Create a MutationObserver to detect changes to the input vendor account
  var targetNode = document.getElementById("vendor_account_name1");
  var observer = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
      if (
        mutation.type === "attributes" &&
        mutation.attributeName === "value"
      ) {
        console.log("vendor_account_name1 value changed to:", targetNode.value);

        getaccountdetail();
      }
    }
  });

  // Configuration for the observer (observe attribute changes)
  var config = { attributes: true };
  if (targetNode)
    observer.observe(targetNode, config);


  //set closure month on change of closure date
  const closingDateInput = document.getElementById("closing_date");
  const closureMonthInput = document.getElementById("closure_month");
  const closureYearInput = document.getElementById("closure_year");

  // Add an event listener to the closing date input
  if (closingDateInput) {
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
          closureYearInput.value = year;
        } else {
          // Clear the inputs if the date is invalid
          closureMonthInput.value = "";
          closureYearInput.value = '';
        }
      } else {
        // If the date format is incorrect, clear the inputs
        closureMonthInput.value = "";
      }
    });
  }

  //set commit month on change of commit date
  const commitDateInput = document.getElementById("commit_date");
  const commitMonthInput = document.getElementById("commit_month");
  const commitYearInput = document.getElementById("commit_year");

  // Add an event listener to the commit date input
  if (commitDateInput) {
    commitDateInput.addEventListener("change", function () {
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

          // Set the commit month
          commitMonthInput.value = month;
          $("#commit_month").val(month).trigger("change");

          var wekno = getWeekNumber(selectedDate);
          // //   alert(wekno);
          $("#commit_week").val(wekno).trigger("change");

          // Set the commit year
          commitYearInput.value = year;
        } else {
          // Clear the inputs if the date is invalid
          commitMonthInput.value = "";
          commitYearInput.value = '';
        }
      } else {
        // If the date format is incorrect, clear the inputs
        commitMonthInput.value = "";
      }
    });
  }

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
  ////check if stage = 1 = prospect then hide 
  var opportunity_id = document.getElementById("opportunity_stage");
  // alert(opportunity_stage);
  if (opportunity_id) {
    var opportunity_stage = document.getElementById("opportunity_stage").value;

    if (opportunity_stage != 3)//stage not qualified
      $(".section-submit_for_pricing").addClass("tr-hidden");
    if (opportunity_stage != 4)//stage not submit for pricing
      $(".section-pricing_done").addClass("tr-hidden");

    // if (opportunity_stage != 5)//if not Purchase Price Received
    // {
    //   $(".margin_percentage").attr("readonly", true);
    //   $(".margin_percentage").val("");
    // }
  }
  //onchange opportunity stage


  $("#opportunity_stage").change(function () {
    setcustomerpo();
    toggleBlocks($(this).val().trim());
  });
  function setcustomerpo() {
    var opportunity_stage = $('#opportunity_stage').val();
    // Check if value 1 is selected
    if (opportunity_stage == 8) {
      $("#customer_po_num").removeClass("V~O");
      $("#customer_po_num").addClass("V~M");
      $("#customer_payment_terms").removeClass("DD~O");
      $("#customer_payment_terms").addClass("DD~M");
      $("#customer_po_date").removeClass("DT~O");
      $("#customer_po_date").addClass("DT~M");
      $("#po_received_date").addClass("DT~M");
      $("#po_received_date").addClass("DT~M");

    }
    else {
      $("#customer_po_num").removeClass("V~M");
      $("#customer_po_num").addClass("V~O");
      $("#customer_payment_terms").removeClass("DD~M");
      $("#customer_payment_terms").addClass("DD~O");
      $("#customer_po_date").removeClass("DT~M");
      $("#customer_po_date").addClass("DT~O");
      $("#po_received_date").addClass("DT~O");
      $("#po_received_date").addClass("DT~O");
    }


  }
  //if checked then disable submit_for_screening////
  var submit_for_screening = document.getElementById("submit_for_screening");
  if (submit_for_screening) {
    if (submit_for_screening.checked) {
      // Checkbox is checked
      $("#submit_for_screening").prop("disabled", true);
    } else {
      $("#submit_for_screening").prop("disabled", false);
    }
  }
  //if checked then disable submit_for_pricing////
  var submit_for_pricing = document.getElementById("submit_for_pricing");
  if (submit_for_pricing) {
    if (submit_for_pricing.checked) {
      // Checkbox is checked
      $("#submit_for_pricing").prop("disabled", true);
    } else {
      $("#submit_for_pricing").prop("disabled", false);
    }
  }


  //if checked then disable pricing_done////
  var pricing_done = document.getElementById("pricing_done");

  if (pricing_done) {
    // Check if the checkbox is checked
    if (pricing_done.checked) {
      // If checked, disable the checkbox
      $("#pricing_done").prop("disabled", true);
    } else {
      // If unchecked and not disabled, enable the checkbox
      if (!$("#pricing_done").prop("disabled")) {
        $("#pricing_done").prop("disabled", false);
      }
    }
  }

  //check if any products added or not ,if not hide pricing done
  if (!$('#productTable43 tbody tr').length)
    $(".section-pricing_done").addClass("tr-hidden");
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

    $.ajax({
      type: "POST",
      url: "approveopportunity",
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
      url: "approveopportunity",
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
/////////get billing  address
/////////////create mutation for bill location/////////////////
// Create a MutationObserver to detect changes to the input bill_location1
var targetNode_loc = document.getElementById("bill_location1");
var observer = new MutationObserver(function (mutationsList) {
  for (var mutation of mutationsList) {
    if (mutation.type === "attributes" && mutation.attributeName === "value") {
      console.log("bill location value changed to:", targetNode_loc.value);

      getbillingdetail(targetNode_loc.value);
    }
  }
});

// Configuration for the observer (observe attribute changes)
var config = { attributes: true };
if (targetNode_loc)
  observer.observe(targetNode_loc, config);
/////////////get bill_from_location1
/////////////create mutation for bill location/////////////////
// Create a MutationObserver to detect changes to the input bill_location1
var targetNode_loc2 = document.getElementById("bill_from_location1");
var observer = new MutationObserver(function (mutationsList) {
  for (var mutation of mutationsList) {
    if (mutation.type === "attributes" && mutation.attributeName === "value") {
      console.log("bill location value changed to:", targetNode_loc2.value);

      getbillingdetailfrom(targetNode_loc2.value);
    }
  }
});
var targetNodeware_loc2 = document.getElementById("warehouse_loc_business_entity1");
var observer = new MutationObserver(function (mutationsList) {
  for (var mutation of mutationsList) {
    if (mutation.type === "attributes" && mutation.attributeName === "value") {
      console.log("bill location value changed to:", targetNodeware_loc2.value);

      getwarehouselocation(targetNodeware_loc2.value);
    }
  }
});

// Configuration for the observer (observe attribute changes)
var config = { attributes: true };
if (targetNode_loc2)
  observer.observe(targetNode_loc2, config);
if (targetNodeware_loc2)
  observer.observe(targetNodeware_loc2, config);


function getbillingdetail(bill_location) {
  data = {
    bill_location: bill_location,
    _csrf: $("#csrfToken").val(),
  };

  $.ajax({
    type: "POST",
    url: "getvendorlocation",
    // async:false,
    data: data,
    success: function (response) {
      console.log(response); // Log the entire response to check its structure

      // Check if the data object exists and contains 'first_name'
      if (response && response.data) {

        $("#bill_address").val(response.data.address);
        $("#billing_city").val(response.data.bill_city);
        $("#bill_state").val(response.data.state);
        $("#bill_state_code").val(response.data.state_code);
        $("#bill_gstin_no").val(response.data.gstin_no_uin);
        $("#pan_number").val(response.data.pan_no);
        $("#productTable43 tbody tr").each(function () {
            var trid = this.id;
            applyGstSplit(trid);
        });


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
function getwarehouselocation(warehouse_location) {
  data = {
    warehouse_location: warehouse_location,
    _csrf: $("#csrfToken").val(),
  };

  $.ajax({
    type: "POST",
    url: "getwarehouselocation",
    // async:false,
    data: data,
    success: function (response) {
      console.log(response); // Log the entire response to check its structure

      // Check if the data object exists and contains 'first_name'
      if (response && response.data) {

        $("#bill_from_address").val(response.data.address);
        // $("#billing_city").val(response.data.bill_city);
        $("#bill_from_state").val(response.data.state);
        $("#bill_from_state_code").val(response.data.statecode);
        $("#productTable43 tbody tr").each(function () {
            var trid = this.id;
            applyGstSplit(trid);
        });

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

function getbillingdetailfrom(bill_location) {
  data = {
    bill_location: bill_location,
    _csrf: $("#csrfToken").val(),
  };

  $.ajax({
    type: "POST",
    url: "getvendorlocation",
    // async:false,
    data: data,
    success: function (response) {
      console.log(response); // Log the entire response to check its structure

      // Check if the data object exists and contains 'first_name'
      if (response && response.data) {

        $("#bill_from_address").val(response.data.address);
        // $("#billing_city").val(response.data.bill_city);
        $("#bill_from_state").val(response.data.state);
        $("#bill_from_state_code").val(response.data.state_code);
        $("#productTable43 tbody tr").each(function () {
            var trid = this.id;
            applyGstSplit(trid);
        });

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
$(document).on("change", "#bill_state_code, #bill_from_state_code", function () { debugger;
    $("#productTable43 tbody tr").each(function () {
        var trid = this.id;
        applyGstSplit(trid);
    });
});

function normalizeStateCode(code) {
    if (code === null || code === undefined || code === "") return null;
    return parseInt(code, 10);
}
// get contact details
// Create a MutationObserver to detect changes to the input requester_customer_name
// change variable name only to resolve issue on date 26-08-25 by ptpatel targetNode to targetNode1 etc
var targetNode1 = document.getElementById("requester_customer_name1");
var observer1 = new MutationObserver(function (mutationsList1) {
  for (var mutation1 of mutationsList1) {
    if (
      mutation1.type === "attributes" &&
      mutation1.attributeName === "value"
    ) {
      getcontacts(targetNode1.value, $("#requester_mobile"), $("#requester_email_customer_email"));

      console.log("requester_customer_name1 value changed to:", targetNode1.value);
    }
  }
});
// Configuration for the observer (observe attribute changes)
var config1 = { attributes: true };
if (targetNode1)
  observer1.observe(targetNode1, config1);

// Create a MutationObserver to detect changes to the input requester_customer_name
var targetNode = document.getElementById("decision_maker_name1");
var observer = new MutationObserver(function (mutationsList) {
  for (var mutation of mutationsList) {
    if (
      mutation.type === "attributes" &&
      mutation.attributeName === "value"
    ) {
      getcontacts(targetNode.value, $("#decision_maker_mobile"), $("#decision_maker_email"));

      console.log("decision_maker_name1 value changed to:", targetNode.value);
    }
  }
});
// Configuration for the observer (observe attribute changes)
var config = { attributes: true };
if (targetNode)
  observer.observe(targetNode, config);

// var requester_customer_name1 = $("#requester_customer_name1").val();
// if (requester_customer_name1 != "") {
//   getcontacts(contact_name,$("#requester_mobile"),$("#requester_email_customer_email"));
// }
function getcontacts(contact_name, contact_mobile, contact_email) {
  data = {
    contact_name: contact_name,
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
        contact_mobile.val(response.data.mobile);
        contact_email.val(response.data.email);
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
///get account etail
function getaccountdetail() {
  // alert($("#vendor_account_name1").val());
  data = {
    vendor_account_name: $("#vendor_account_name1").val(),
    _csrf: $("#csrfToken").val(),
  };

  $.ajax({
    type: "POST",
    url: "getaccountdetail",
    // async:false,
    data: data,
    success: function (response) {
      console.log(response); // Log the entire response to check its structure

      // Check if the data object exists and contains 'first_name'
      if (response && response.data) {


        // Mapping of input IDs to response keys
        const fieldMappings = [
          { id: "zone_region", responseKey: "zone_region", triggerChange: true },
          { id: "team_name", responseKey: "team_name", triggerChange: true },
          { id: "account_manager1", responseKey: "account_manager" },
          { id: "account_manager", responseKey: "account_manager_name" },
          { id: "business_manager1", responseKey: "business_manager" },
          { id: "business_manager", responseKey: "business_manager_name" },
          { id: "account_director_rsm1", responseKey: "account_director_rsm" },
          { id: "account_director_rsm", responseKey: "account_director_rsm_name" },
          { id: "devit_isr1", responseKey: "devit_isr" },
          { id: "devit_isr", responseKey: "devit_isr_name" },
          { id: "devit_vertical_manager1", responseKey: "devit_vertical_manager" },
          { id: "devit_vertical_manager", responseKey: "devit_vertical_manager_name" }
        ];

        fieldMappings.forEach(field => {
          const currentValue = $(`#${field.id}`).val();
          if (!currentValue && response.data[field.responseKey] !== undefined) {
            $(`#${field.id}`).val(response.data[field.responseKey]);
            if (field.triggerChange) {
              $(`#${field.id}`).trigger("change");
            }
          }
        });


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

/////////////////on change ship to location////////////

// Function to observe all matching inputs
function observeMatchingInputsShip() {
  // Match inputs with ID pattern 'ship_to_location_*1'
  const inputs = document.querySelectorAll(
    'input[id^="ship_to_location_"][id$="1"]'
  );
  inputs.forEach((input) => observeInputChangesShip(input));
  console.log(`Observers attached to ${inputs.length} inputs.`);
}

// Function to monitor dynamically added inputs
function monitorDynamicInputsShip() {
  const container = document.body; // Observe the entire document

  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      if (mutation.type === "childList" && mutation.addedNodes.length > 0) {
        mutation.addedNodes.forEach((node) => {
          if (node.nodeType === 1) {
            // Check for new matching inputs
            const newInputs = node.querySelectorAll(
              'input[id^="ship_to_location_"][id$="1"]'
            );
            // console.log("deepika");
            newInputs.forEach((input) => observeInputChangesShip(input));
          }
        });
      }
    });
  });

  observer.observe(container, {
    childList: true, // Detect added elements
    subtree: true, // Include all child elements
  });

  console.log("Monitoring dynamic inputs for pattern: product_name_*1");
}

// Function to observe input value changes
function observeInputChangesShip(inputElement) {
  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      if (
        mutation.type === "attributes" &&
        mutation.attributeName === "value"
      ) {
        console.log(
          `Value changed in ${inputElement.id}: ${inputElement.value}`
        );
        const nearestTr = inputElement.closest("tr");
        if (nearestTr) {
          trid = nearestTr.id;
          console.log("Nearest <tr> ID:", nearestTr.id);
          getShipinfo(trid.trim(), `${inputElement.value}`);

        } else {
          nearestTr.id = "";
          console.log("No <tr> ancestor found");
        }
      }
    });
  });

  observer.observe(inputElement, {
    attributes: true, // Observe attribute changes
    attributeFilter: ["value"], // Only watch 'value' attribute
  });

  console.log(`Observer attached to input: ${inputElement.id}`);
}

// Initialize observers for existing and dynamic inputs
observeMatchingInputsShip();
monitorDynamicInputsShip();

// get productinfo
function getShipinfo(trid, locationid) {
  // alert(locationid);
  data = { locationid: locationid, _csrf: $("#csrfToken").val() };

  $.ajax({
    type: "POST",
    url: "getshipinfo",
    // async:false,
    data: data,
    success: function (response) {

      // Check if the data object exists and contains 'first_name'
      if (response && response.data) {

        $("#ship_to_address_" + trid).val(response.data.address);
        $("#ship_to_state_" + trid).val(response.data.state);
        $("#ship_state_code_" + trid).val(response.data.state_code);
        $("#ship_legal_name_" + trid).val(response.data.legal_entity_name);
        $("#pan_number_" + trid).val(response.data.pan_no);
        $("#gstin_no_uin_" + trid).val(response.data.gstin_no_uin);

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

/////////////////on change ship to location end////////////

// get productinfo
function getProductinfo(trid, product_name) {
  // alert(product_name);
  trid = trid.trim();
  data = { productid: product_name, _csrf: $("#csrfToken").val() };

  $.ajax({
    type: "POST",
    // url: "getproductinfo",
    //change for ERP Point 56,57
    url: "getproductinformation",
    // async:false,
    data: data,
    success: function (response) {

      console.log("trid" + trid);
      console.log(response); // Log the entire response to check its structure

      // Check if the data object exists and contains 'first_name'
      if (response && response.data) {
        console.log("in resp");
        $("#product_description_" + trid).val(
          response.data.product_description
        );
        var gst = response.data.gst_percentage;
        applyGstSplit(trid, gst);

        $("#hsn_code_" + trid).val(response.data.hsn_code);
        $("#master_category_" + trid).val(response.data.master_category).trigger("change");
        $("#sub_category_" + trid).val(response.data.sub_category).trigger("change");

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
/////////////on change product change end//////////////
//////////////on change master category start///////////
    // sales_price_ added by ptpatel on date 03-01-2026 for v11 - 178
$(document).on(
  "change",
  "[id^=master_category_],[id^=sub_category_],[id^=product_name_],[id^=sales_price_],[id^=quantity_],[id^=cost_price_]",
  function () {
    var changedElement = $(this);
    var suffix = changedElement.attr("id").match(/\d+$/) ? changedElement.attr("id").match(/\d+$/)[0] : "";

    // If the changed element is a master category
    if (changedElement.attr("id").startsWith("master_category_") && changedElement.val()) {
      console.log("Master category changed");
      // getSubcategory(suffix, changedElement.val());  // Call getSubcategory only for master category
      // getProduct(suffix);  // Get product when master category is changed
    }

    // If the changed element is a sub category
    if (changedElement.attr("id").startsWith("sub_category_") && changedElement.val()) {
      // console.log("Sub category changed");
      // getProduct(suffix);  // Get product when sub category is changed
    }
    // If the changed element is a product_name
    if (changedElement.attr("id").startsWith("product_name_") && changedElement.val()) {
      console.log("product name changed");
      // getProductinfo(suffix, $(this).val());  // Get product when product_name is changed\
      // settotal(suffix);
    }

    // If the changed element is a margin_percentage
    if (changedElement.attr("id").startsWith("margin_percentage_") && changedElement.val()) {
      console.log("margin changed");
      var opportunity_stage = document.getElementById("opportunity_stage").value;

      //  alert(opportunity_stage);

      // if (opportunity_stage != 5)//if not Purchase Price Received
      // {
      //   $(".margin_percentage").attr("readonly", true);
      //   $(".margin_percentage").val("");
      // }
      // else 
      settotal(suffix);  // Get product when product_name is changed
    }

    // If the changed element is a cost_price_
    if (changedElement.attr("id").startsWith("cost_price_") && (changedElement.val() || changedElement.val() == '')) {
      console.log("cost_price changed");
      settotal(suffix);  // Get product when product_name is changed
    }

    // added by ptpatel on date 03-01-2026 for v11 - 178
    // If the changed element is a sales_price_
    if (changedElement.attr("id").startsWith("sales_price_") && (changedElement.val() || changedElement.val() == '')) {
      console.log("sales_price_ changed");
      settotal(suffix);  // Get product when product_name is changed
    }
    // added by ptpatel on date 03-01-2026 for v11 - 178

    // If the changed element is a quantity_
    if (changedElement.attr("id").startsWith("quantity_") && changedElement.val()) {
      console.log("quantity changed");
      settotal(suffix);  // Get product when product_name is changed
    }
  }
);
function applyGstSplit(trid, serverGst) {
    trid = $.trim(trid);
    var bill_from_state_code = normalizeStateCode($("#bill_from_state_code").val());
    var bill_state_code      = normalizeStateCode($("#bill_state_code").val());

    var gst = 0;
    
    if (serverGst !== undefined && serverGst !== null) {
        gst = parseFloat(serverGst) || 0;
    } 
    else {        
        var cgst = parseFloat($("#cgst_" + trid).val()) || 0;
        var sgst = parseFloat($("#sgst_" + trid).val()) || 0;
        var igst = parseFloat($("#igst_" + trid).val()) || 0;
        
        if (igst > 0) {
            gst = igst;
        } else if (cgst > 0 || sgst > 0) {
            gst = cgst + sgst;
            
        } else {
            return;  
        }

    }

    $("#cgst_" + trid).val(0);
    $("#sgst_" + trid).val(0);
    $("#igst_" + trid).val(0);

    // if (!bill_from_state_code || !bill_state_code || gst === 0) {
    //     return;
    // }

    if (bill_state_code == bill_from_state_code) {
        var half = gst / 2;
        $("#cgst_" + trid).val(half);
        $("#sgst_" + trid).val(half);
    } else {
        $("#igst_" + trid).val(gst);
    }
}


//set total 
function settotal(trid) {
  var rawCost  = $("#cost_price_"  + trid).val();
  var rawSales = $("#sales_price_" + trid).val();
  var margin = parseFloat($("#margin_percentage_" + trid).val()) || 0; // Default to 0 if empty or invalid
  var cost_price = parseFloat($("#cost_price_" + trid).val()) || 0;
  var sales_price = parseFloat($("#sales_price_" + trid).val()) || 0;
  var cgst = parseFloat($("#cgst_" + trid).val()) || 0;
  var sgst = parseFloat($("#sgst_" + trid).val()) || 0;
  var igst = parseFloat($("#igst_" + trid).val()) || 0;
  var quantity = parseFloat($("#quantity_" + trid).val()) || 0;
  var igstamount = 0;
  var sgstamount = 0;
  var cgstamount = 0;
  // var reject = document.getElementById("reject_" + trid).checked;
  //code change throw error on edit if checkbox is not checked 
  var rejectElem = document.getElementById("reject_" + trid);
  var reject = rejectElem ? rejectElem.checked : false;
  // alert(reject);
  // if (cost_price && margin) {
  // added by ptpatel on date 03-01-2026 for v11 - 178
  // if (cost_price && sales_price) {
  //   let margin_amt_ = parseFloat(sales_price - cost_price);
  //   let margin_ = (parseFloat(((margin_amt_ / sales_price) * 100))).toFixed(4);
  //   $("#margin_percentage_" + trid).val(margin_);
  //   //alert(sales_price);
  // }// added by ptpatel on date 03-01-2026 for v11 - 178
  // if (rawCost === "" || rawSales === "") {
  //   $("#margin_percentage_" + trid).val("");
  // } else if (cost_price && sales_price) {
  //   // Your existing margin calculation
  //   let margin_amt_ = sales_price - cost_price;
  //   let margin_ = ((margin_amt_ / sales_price) * 100).toFixed(4);
  //   $("#margin_percentage_" + trid).val(margin_);
  // }

  // raw strings
var rawCost  = $("#cost_price_"  + trid).val();
var rawSales = $("#sales_price_" + trid).val();


if (rawCost === "" || rawSales === "") {
    //  no margin
    $("#margin_percentage_" + trid).val("");
} else {
    if (sales_price === 0 && cost_price === 0) {
        // nothing on both sides
        $("#margin_percentage_" + trid).val("0.00");
    } else if (sales_price === 0 && cost_price > 0) {
        // full loss
        $("#margin_percentage_" + trid).val("-100.00");
    } else if (sales_price > 0 && cost_price === 0) {
        // no cost
        $("#margin_percentage_" + trid).val("100.00");
    } else if (sales_price > 0) {
        // normal case
        let margin_amt_ = sales_price - cost_price;
        let margin_ = ((margin_amt_ / sales_price) * 100).toFixed(4);
        $("#margin_percentage_" + trid).val(margin_);
    } else {
        $("#margin_percentage_" + trid).val("");
    }
}


  if (reject) {
    $("#margin_percentage_" + trid).val('');
    $("#margin_percentage_" + trid).attr("readonly", true);
    margin = '';
  }
// commented by ptpatel on date 03-01-2026 for v11 - 178
  // Sales Price = Auto Calculate ( CP + Margin * CP )
  // if (cost_price && margin) {
  //   var sales_price = (cost_price + (cost_price * margin / 100));
  //   $("#sales_price_" + trid).val(sales_price);
  //   //alert(sales_price);
  // }
// commented by ptpatel on date 03-01-2026 for v11 - 178
  if (((cgst || sgst) || igst) && (cost_price || sales_price >=0) && quantity ) {
    // Total Line Item Amount Quantity * Sales Price + GST % of Quantity * Sales Price
    var total = (quantity * sales_price);


    if (igst) {
      igstamount = (igst * sales_price * quantity / 100);
      total += igstamount;
    }
    if (cgst && sgst) {
      cgstamount = (cgst * sales_price * quantity / 100);
      sgstamount = (sgst * sales_price * quantity / 100);
      total += cgstamount + sgstamount;
    }
    total_price_tax_exclude = cost_price * quantity;
    total_sale_tax_exclude = sales_price * quantity;
    total_amount_tax_include = cgstamount + sgstamount + igstamount + total_sale_tax_exclude;

    $("#igst_amount_" + trid).val(igstamount.toFixed(2));
    $("#cgst_amount_" + trid).val(cgstamount.toFixed(2));
    $("#sgst_amount_" + trid).val(sgstamount.toFixed(2));
    $("#total_cost_tax_exclude_" + trid).val(total_price_tax_exclude.toFixed(2));
    $("#total_sale_tax_exclude_" + trid).val(total_sale_tax_exclude.toFixed(2));
    $("#total_amt_tax_include_" + trid).val(total_amount_tax_include.toFixed(2));
    $("#total_amount_" + trid).val(total.toFixed(2));
  }

  // Gross Profit (Sales Price * Quantity) - (Cost Price * Quantity)
  // Parse input values safely
  var sales_price = parseFloat(sales_price) || 0;
  var cost_price = parseFloat(cost_price) || 0;
  var quantity = parseFloat(quantity) || 0;

  // Calculate gross
  var gross = (sales_price * quantity) - (cost_price * quantity);

  // Handle NaN just in case
  if (isNaN(gross)) {
    gross = 0;
  }

  // Set formatted value
  $("#gross_profit_" + trid).val(gross.toFixed(2));

  setotalcosting();
}
function setotalcosting() {
  var totaligstamt = 0;
  var totalsgstamt = 0;
  var totalcgstamt = 0;
  var settotal_cost_tax_exclude = 0;
  var settotal_sale_tax_exclude = 0;
  var settotal_amt_tax_include = 0;
  var total_margin = 0;
  var total_margin_percent = 0;

  $('.igst_amount').each(function () {
    let val = parseFloat($(this).val());
    if (!isNaN(val)) {
      totaligstamt += val;
    }
  });
  $('.sgst_amount').each(function () {
    let val = parseFloat($(this).val());
    if (!isNaN(val)) {
      totalsgstamt += val;
    }
  });
  $('.cgst_amount').each(function () {
    let val = parseFloat($(this).val());
    if (!isNaN(val)) {
      totalcgstamt += val;
    }
  });
  $('.total_cost_tax_exclude').each(function () {
    let val = parseFloat($(this).val());
    if (!isNaN(val)) {
      settotal_cost_tax_exclude += val;
    }
  });
  $('.total_sale_tax_exclude').each(function () {
    let val = parseFloat($(this).val());
    if (!isNaN(val)) {
      settotal_sale_tax_exclude += val;
    }
  });
  $('.total_amt_tax_include').each(function () {
    let val = parseFloat($(this).val());
    if (!isNaN(val)) {
      settotal_amt_tax_include += val;
    }
  });
  total_margin = settotal_sale_tax_exclude - settotal_cost_tax_exclude;
  // total_margin_percent = 100 * (total_margin / settotal_sale_tax_exclude);
  // if (isNaN(total_margin_percent)) {
  //   total_margin_percent = 0;
  // }
  // Parse and sanitize inputs
  total_margin = parseFloat(total_margin) || 0;
  settotal_sale_tax_exclude = parseFloat(settotal_sale_tax_exclude) || 0;

  // Avoid division by zero
  if (settotal_sale_tax_exclude === 0) {
    total_margin_percent = 0;
  } else {
    total_margin_percent = 100 * (total_margin / settotal_sale_tax_exclude);
  }

  // Final NaN check (just in case)
  if (isNaN(total_margin_percent)) {
    total_margin_percent = 0;
  }

  $("#total_oppr_cost_tax_exclude").val(settotal_cost_tax_exclude.toFixed(2));
  $("#total_oppr_sale_tax_exclude").val(settotal_sale_tax_exclude.toFixed(2));
  $("#total_opportunity_cgst").val(totalcgstamt.toFixed(2));
  $("#total_opportunity_sgst").val(totalsgstamt.toFixed(2));
  $("#total_opportunity_igst").val(totaligstamt.toFixed(2));
  $("#total_oppr_amount_tax_include").val(settotal_amt_tax_include.toFixed(2));
  $("#opportunity_margin").val(total_margin.toFixed(2));
  $("#opportunity_margin_percentage").val(total_margin_percent.toFixed(2));
}




// get productinfo
function getSubcategory(trid, master_category) {
  // alert(product_name);
  data = { master_category: master_category, _csrf: $("#csrfToken").val() };
  const SubDropdown = $("#sub_category_" + trid).empty().append('<option value="">Select</option>');
  $.ajax({
    type: "POST",
    url: "getsubcategory",
    // async:false,
    data: data,
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {

        response.data.forEach((oem) => {

          SubDropdown.append(`<option value="${oem.id}">${oem.subval}</option>`);
        });
        SubDropdown.trigger('change'); // Update Select2 dropdown

      }
      else {
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
function getProduct(trid) {
  master_category = $("#master_category_" + trid).val();
  sub_category = $("#sub_category_" + trid).val();
  if (master_category && sub_category) {
    data = { master_category: master_category, sub_category: sub_category, _csrf: $("#csrfToken").val() };
    const ProdDropdown = $("#product_name_" + trid).empty().append('<option value="">Select</option>');
    $.ajax({
      type: "POST",
      url: "getproduct",
      // async:false,
      data: data,
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {

          response.data.forEach((oem) => {

            ProdDropdown.append(`<option value="${oem.id}">${oem.name}</option>`);
          });
          ProdDropdown.trigger('change'); // Update Select2 dropdown

        }
        else {
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
}
/////////////on change master category change end//////////////

/////////on addition of prduct///////
// Create an observer that watches for changes to the table body
var observer = new MutationObserver(function (mutationsList) {
  for (var mutation of mutationsList) {
    if (mutation.type === 'childList') {
      // Check if new rows have been added
      mutation.addedNodes.forEach(function (node) {
        if (node.nodeName === "TR") {
          // Get the id of the added row
          var rowId = node.id;
          // console.log("New row ID:", rowId);
          // Trigger the rowAdded function when a row is added
          rowAdded(rowId);
        }
      });
    }
  }
});

// Start observing the table's tbody for changes
var tableBody = document.querySelector('#productTable43 tbody');
if (tableBody)
  observer.observe(tableBody, { childList: true });

function rowAdded(trid) {

  var uniqueNumber = new Date().getTime();
  // console.log(uniqueNumber);
  $("#purchase_request_number_" + trid).val(uniqueNumber);

}

////////////team responsible///
$("#team_responsible").change(function () {
  setsasf();
});
var team_res = $('#team_responsible');
if (team_res) {
  setsasf();

}
function setsasf() {
  var team_responsible = $('#team_responsible').val();  // Returns an array of selected values
  // Check if value 1 is selected
  if (team_responsible && team_responsible.includes('1')) {
    $("#sa_assigned").removeClass("V~O");
    $("#sa_assigned").addClass("V~M");
    $("#sf_assigned").removeClass("V~O");
    $("#sf_assigned").addClass("V~M");

    if (!team_responsible.includes('2')) {
      $("#procurement_team_member").removeClass("V~M");
      $("#procurement_team_member").addClass("V~O");
      var helpBlock = $("#procurement_team_member").closest(".form-group").find(".help-block");
      if (helpBlock.length) {
        helpBlock.html('');
      }
    }
    if ($("#sa_assigned1").val() !== '') {
        clearFieldError("sa_assigned");
    }
    if ($("#sf_assigned1").val() !== '') {
        clearFieldError("sf_assigned");
    }
  }

  // Check if value 2 is selected
  if (team_responsible && team_responsible.includes('2')) {
    $("#procurement_team_member").removeClass("V~O");
    $("#procurement_team_member").addClass("V~M");

    if (!team_responsible.includes('1')) {
      $("#sa_assigned").removeClass("V~M");
      $("#sa_assigned").addClass("V~O");
      $("#sf_assigned").removeClass("V~M");
      $("#sf_assigned").addClass("V~O");
      var helpBlock = $("#sa_assigned").closest(".form-group").find(".help-block");
      if (helpBlock.length) {
        helpBlock.html('');
      }
      var helpBlock = $("#sf_assigned").closest(".form-group").find(".help-block");
      if (helpBlock.length) {
        helpBlock.html('');
      }
    }
    if ($("#procurement_team_member1").val() !== '') {
        clearFieldError("procurement_team_member");
    }
  }

  //added for CR point -1 on date 11-02-2026 by ptpatel
  // Check if value 2 is selected
  if (team_responsible && team_responsible.includes('3')) { //solution factory
    $("#sf_assigned").removeClass("V~O");
    $("#sf_assigned").addClass("V~M");

    if (!team_responsible.includes('1')) {
      $("#sa_assigned").removeClass("V~M");
      $("#sa_assigned").addClass("V~O");
      $("#procurement_team_member").removeClass("V~M");
      $("#procurement_team_member").addClass("V~O");
      var helpBlock = $("#sa_assigned").closest(".form-group").find(".help-block");
      if (helpBlock.length) {
        helpBlock.html('');
      }
      var helpBlock = $("#procurement_team_member").closest(".form-group").find(".help-block");
      if (helpBlock.length) {
        helpBlock.html('');
      }
    }
     if ($("#sf_assigned1").val() !== '') {
        clearFieldError("sf_assigned");
    }
  }
  //end code added for CR point -1 on date 11-02-2026 by ptpatel
}
function clearFieldError(fieldId) {

    var field = $("#" + fieldId);

    // Remove error class from field itself
    field.removeClass("error");

    // Remove error class from parent form group (if used)
    field.closest(".form-group").removeClass("has-error");

    // Clear help block
    var helpBlock = field.closest(".form-group").find(".help-block");
    if (helpBlock.length) {
        helpBlock.html('');
    }
}

//////////opportunity stage////////

//code start for ERP Point - 52 added by ptpatel on date 18-09-2025
////////toggle function start//////////////////
function toggleBlocks(status) {
  status = parseInt(status);

  let mandatoryByStatus = [];

  switch (status) {
    case 6: // In transit
      mandatoryByStatus = ["decision_maker_name", "decision_maker_email", "decision_maker_mobile"];
      break;

    case 7: // In delivery
      mandatoryByStatus = ["commit_date"];
      break;
  }

  // Define prefix mapping
  const prefixMap = {
    decision_maker_name: "V",
    decision_maker_email: "E",
    decision_maker_mobile: "MOB",
    commit_date: "DT"
  };

  // Apply classes for ALL mapped fields
  Object.keys(prefixMap).forEach((key) => {
    // const input = document.querySelector('[name="opportunity[' + key + ']"]');
    const input = document.getElementById(key);
    if (input) {
      const prefix = prefixMap[key];
      // if current field is inside mandatoryByStatus → mark as M else O
      const newClass = prefix + "~" + (mandatoryByStatus.includes(key) ? "M" : "O");

      // Remove old prefix classes (~M/~O)
      input.classList.forEach((cls) => {
        if (cls.startsWith(prefix + "~")) {
          input.classList.remove(cls);
        }
      });

      // Add new one
      input.classList.add(newClass);
    }
  });

  // Hide sections
  // const hideCombined = [...new Set([...hideByType, ...mandatoryByStatus])];
  // hideCombined.forEach((key) => {
  //   const el = document.querySelector('.section-' + key);
  //   if (el) el.style.display = 'none';
  // });
}


//code end for ERP Point 52
//code start for ERP Point 54

$(document).ready(function () {
  // Run once on page load
  togglePOSections();

  // Also run whenever stage changes
  $(document).on("change", "#opportunity_stage", function () {
    togglePOSections();
  });
});
function togglePOSections() {
  let stage = $("#opportunity_stage").val();

  // Sections you want to show
  let section_to_show = ['.section-customer_po_date', '.section-customer_po_num'];

  // Hide all by default
  $(section_to_show.join(",")).hide();

  // Show only when stage = 8
  if (stage == 8) {
    $(section_to_show.join(",")).show();
  }
}

//end code for ERP Point 54
//change ERP Point 56,57
// Function to observe all matching inputs
function observeMatchingInputs() {
  // Match inputs with ID pattern 'productid_*1'
  const inputs_p = document.querySelectorAll(
    'input[id^="product_name_"][id$="1"]'
  );
  inputs_p.forEach((input) => observeInputChangesProduct(input));
  console.log(`Observers attached to ${inputs_p.length} inputs.`);
}

// Function to monitor dynamically added inputs
function monitorDynamicInputs() {
  const container = document.body; // Observe the entire document

  const observer_p = new MutationObserver((mutations_p) => {
    mutations_p.forEach((mutations_p) => {
      if (mutations_p.type === "childList" && mutations_p.addedNodes.length > 0) {
        mutations_p.addedNodes.forEach((node) => {
          if (node.nodeType === 1) {
            // Check for new matching inputs
            const newInputs = node.querySelectorAll(
              'input[id^="product_name_"][id$="1"]'
            );
            // console.log("deepika");
            newInputs.forEach((input) => observeInputChangesProduct(input));
          }
        });
      }
    });
  });

  observer_p.observe(container, {
    childList: true, // Detect added elements
    subtree: true, // Include all child elements
  });

  console.log("Monitoring dynamic inputs for pattern: productid_*1");
}

// Initialize observers for existing and dynamic inputs
observeMatchingInputs();
monitorDynamicInputs();

function observeInputChangesProduct(inputElement) {
  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      if (
        mutation.type === "attributes" &&
        mutation.attributeName === "value"
      ) {
        console.log(
          `Value changed in ${inputElement.id}: ${inputElement.value}`
        );
        const nearestTr = inputElement.closest("tr");
        if (nearestTr) {
          trid = nearestTr.id;
          console.log("Nearest <tr> ID:", nearestTr.id);

          getProductinfo(trid, `${inputElement.value}`);
          settotal(trid);

        } else {
          nearestTr.id = "";
          console.log("No <tr> ancestor found");
        }
      }
    });
  });

  observer.observe(inputElement, {
    attributes: true, // Observe attribute changes
    attributeFilter: ["value"], // Only watch 'value' attribute
  });

  console.log(`Observer attached to input: ${inputElement.id}`);
}

//end code change for ERP Point 56,57

//code added by ptpatel for v11 - point -151 on date 08-12-2025
closuredateshowonWON();
$(document).on("change", "#opportunity_stage", function () {
    closuredateshowonWON();
});
function closuredateshowonWON() {
    var stage = $('#opportunity_stage').val();
    console.log("closuredateshowonWON"+stage);
    var closure_fields = ['#closing_date', '#closure_month', '#closure_week','#closure_year'];
    
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
        if (stage === "8") {
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
        if (stage === "8") {
            $(sectionClass).show();
        } else {
            $(sectionClass).hide();
        }
    });
}
