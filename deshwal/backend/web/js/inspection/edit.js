$(document).ready(function () {
  var newURL = window.location.href;
  var module = jQuery("#module").val();
  var str = newURL.indexOf(module);

  const slicestr = newURL.substring(0, str);
  // get exchangerate
  $(document).on("change", "#currency", function () {
    data = { currency: $(this).val(), _csrf: $('#csrfToken').val() };

    getexchangerate(data);


  });

  
  //set inspection_preferred_time blank if set 0:00
  var inspection_preferred_time =$("#inspection_preferred_time").val();
  //alert(inspection_preferred_time);
  if(inspection_preferred_time == '0:00')
    $("#inspection_preferred_time").val('');  

  //end exchange rate
  function getexchangerate(data) {
    $.ajax({
      type: 'POST',
      url: slicestr + "leads/getexchangerate",
      // async:false,
      data: data,
      success: function (data) {
        //location.reload();
        $("#exchange_rate").val(data);

      },
      error: function (data) { // if error occured

        alert('Error occured.please try again');
      },
      dataType: 'html'
    });

  }
  const modeInput = document.getElementById("mode");


  if (modeInput && modeInput.value === "Create") {
    // alert(modeInput);
    // initialize currency with INr
    $('#currency').val("1").trigger("change");
    data = { currency: 1, _csrf: $('#csrfToken').val() };

    //end ddepika
    getexchangerate(data);
    var souringdeal = $("#sourcing_deal1").val();
    if (souringdeal) {
      getsourcingdetail(souringdeal);
    }

    $("#stages").val("1").trigger("change");

    ////////hide random and full product detail
    // $(".row145").addClass("tr-hidden");
    // $(".row2657").addClass("tr-hidden");
    // $(".row2658").addClass("tr-hidden");
    // $(".row2659").addClass("tr-hidden");
  }

  /////////////create mutation for contact/////////////////
  var targetNode_spoc = document.getElementById("spoc_name1");
  var observer = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
      if (
        mutation.type === "attributes" &&
        mutation.attributeName === "value"
      ) {
        console.log("spoc_name1 value changed to:", targetNode_spoc.value);

        getspocdetail(targetNode_spoc.value);
      }
    }
  });

  // Configuration for the observer (observe attribute changes)
  var config = { attributes: true };
  observer.observe(targetNode_spoc, config);

  ///////////get spoc detail///////
  function getspocdetail(contactid) {
    if (contactid) {
      data = {
        contactid: contactid,
        _csrf: $("#csrfToken").val(),
      };

      $.ajax({
        type: "POST",
        url: "getspocdetail",
        // async:false,
        data: data,
        success: function (response) {

          // Check if the data object exists and contains 'first_name'
          if (response && response.data) {
            $("#spoc_number").val(response.data.mobile);
            $("#spoc_email").val(response.data.email);

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

  }

  /////////////create mutation for sourcing deal/////////////////
  // Create a MutationObserver to detect changes to the input vendor account
  var targetNode_sd = document.getElementById("sourcing_deal1");
  var observer = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
      if (
        mutation.type === "attributes" &&
        mutation.attributeName === "value"
      ) {
        console.log("sourcing_deal1 value changed to:", targetNode_sd.value);

        getsourcingdetail(targetNode_sd.value);
      }
    }
  });

  // Configuration for the observer (observe attribute changes)
  var config = { attributes: true };
  observer.observe(targetNode_sd, config);

  /////////get sourcing deal detail///////
  function getsourcingdetail(sourcingdeal) {
    if (sourcingdeal) {
      data = {
        sourcingdeal: sourcingdeal,
        _csrf: $("#csrfToken").val(),
      };

      $.ajax({
        type: "POST",
        url: "getsourcingdetail",
        // async:false,
        data: data,
        success: function (response) {

          // Check if the data object exists and contains 'first_name'
          if (response && response.data) {
            $("#account_name").val(response.data.acc_name);
            $("#account_name1").val(response.data.vendoraccid);
            $("#inspection_location1").val(response.data.service_to_location);
            $("#inspection_location").val(response.data.vendor_loc_name);

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

  }

  ////on change inspection type show inspection
  var insection_type = $("#insection_type").val();
  showprodductblock(insection_type);
  $("#insection_type").on("change", function (e) {
    var insection_type = e.target.value; // Get the selected value
   showprodductblock(insection_type);

  });


});
async function addRowsfull() {
  await addRowBtn('2657', 'inspection');
  await addRowBtn('2658', 'inspection');
  await addRowBtn('2659', 'inspection');
}
async function addRowsrandom() {
  await addRowBtn('145', 'inspection');
}
if ($('.row2657').is(':visible')) {
   // Count the number of <tr> elements inside <tbody>
   var rowCount = $('table#productTable2657 tbody tr').length;

   // If the number of rows is greater than 0, call addRow function
   if (rowCount == 0) {
    addRowBtn('2657', 'inspection');
   }
  
}
if ($('.row2658').is(':visible')) {
  // Count the number of <tr> elements inside <tbody>
  var rowCount = $('table#productTable2658 tbody tr').length;

  // If the number of rows is greater than 0, call addRow function
  if (rowCount == 0) {
   addRowBtn('2658', 'inspection');
  }
}
if ($('.row2659').is(':visible')) {
   // Count the number of <tr> elements inside <tbody>
   var rowCount = $('table#productTable2659 tbody tr').length;
   // If the number of rows is greater than 0, call addRow function
   if (rowCount == 0) {
    addRowBtn('2659', 'inspection');
   }
}
if ($('.row145').is(':visible')) {
   // Count the number of <tr> elements inside <tbody>
   var rowCount = $('table#productTable145 tbody tr').length;

   // If the number of rows is greater than 0, call addRow function
   if (rowCount == 0) {
    addRowBtn('145', 'inspection');
   }
}



function showprodductblock(insection_type) {
  var modeInput = document.getElementById("mode");
 
    $(".row2657").addClass("tr-hidden");
    $(".row2658").addClass("tr-hidden");
    $(".row2659").addClass("tr-hidden");
    $(".row145").addClass("tr-hidden");
   
  
    
    if (modeInput && modeInput.value === "Create") {
      //addRowsfull();
    }
   


  }
 


////////fetch inspection address///////
/////////////create mutation for inspection address/////////////////
// Create a MutationObserver to detect changes to the input vendor account
var targetNode_loc = document.getElementById("inspection_location1");
var observer = new MutationObserver(function (mutationsList) {
  for (var mutation of mutationsList) {
    if (
      mutation.type === "attributes" &&
      mutation.attributeName === "value"
    ) {
      console.log("inspection_location value changed to:", targetNode_loc.value);
      // alert("inspection_location value changed to:", targetNode.value);

      getinspectiondetail(targetNode_loc.value);
    }
  }
});

// Configuration for the observer (observe attribute changes)
var config = { attributes: true };
observer.observe(targetNode_loc, config);

/////////get sourcing deal detail///////
function getinspectiondetail(locationid) {
  locationid = $("#inspection_location1").val();
  //alert(locationid);

  if (locationid) {
    data = {
      locationid: locationid,
      _csrf: $("#csrfToken").val(),
    };

    $.ajax({
      type: "POST",
      url: "getinspectiondetail",
      // async:false,
      data: data,
      success: function (response) {

        // Check if the data object exists and contains 'first_name'
        if (response && response.data) {
          $("#location_address").val(response.data.address);
          $("#location_state").val(response.data.state_value);
          $("#location_city").val(response.data.city_name);
          $("#location_pincode").val(response.data.pincode);

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

}

///////////////inspection done by////////
$(document).ready(function () {
  const fename = $("#logistics_fe_name_done_by_dwmpl").val();
  // Initialize the vendor settings when the page loads
  setVendor();
  // Function to set vendor visibility based on the selection
  function setVendor() {
    var inspection_done_by = $("#inspection_done_by").val();
    $(".section-vendor_name").addClass("tr-hidden");
    $(".section-vendor_spoc_name_done_by_vendor").addClass("tr-hidden");
    $(".section-vendor_spoc_number").addClass("tr-hidden");
    // $(".section-logistics_fe_name_done_by_dwmpl").addClass("tr-hidden");
    // $(".section-logistics_fe_number").addClass("tr-hidden");

    if (inspection_done_by == 2) {
      // If 'inspection_done_by' equals 2 (deshwal), hide vendor-related fields
      $(".section-vendor_name").addClass("tr-hidden");
      $(".section-vendor_spoc_name_done_by_vendor").addClass("tr-hidden");
      $(".section-vendor_spoc_number").addClass("tr-hidden");

      //uncommented on 22 sept 2025 
      $(".section-logistics_fe_name_done_by_dwmpl").removeClass("tr-hidden");
      $(".section-logistics_fe_number").removeClass("tr-hidden");
      //if condition added by ptpatel on date 29-12-2025 to reslve issue of inspection fe set blank via email on date 29-12-2025
      if(fename == ''){
      $("#logistics_fe_name_done_by_dwmpl").val('');
      $("#logistics_fe_name_done_by_dwmpl1").val('');
      $("#logistics_fe_number").val('');
      }
            //if condition added by ptpatel on date 29-12-2025 to reslve issue of inspection fe set blank via email on date 29-12-2025
    } else if (inspection_done_by == 3) {
      // Otherwise (vendor), show vendor-related fields
      $(".section-vendor_name").removeClass("tr-hidden");
      $(".section-vendor_spoc_name_done_by_vendor").removeClass("tr-hidden");
      $(".section-vendor_spoc_number").removeClass("tr-hidden");

      //uncommented on 22 sept 2025 
      $(".section-logistics_fe_name_done_by_dwmpl").addClass("tr-hidden");
      $(".section-logistics_fe_number").addClass("tr-hidden");
      $("#logistics_fe_name_done_by_dwmpl").val('');
      $("#logistics_fe_name_done_by_dwmpl1").val('');
      $("#logistics_fe_number").val('');
    }
  }

  // Trigger the setVendor function when the dropdown changes
  $("#inspection_done_by").change(function () {
    setVendor();
  });

  //////get field engineer data/////////////
  // Create a MutationObserver to detect changes to the input vendor account
  var targetNode_dw = document.getElementById("logistics_fe_name_done_by_dwmpl1");
  var observer = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
      if (
        mutation.type === "attributes" &&
        mutation.attributeName === "value"
      ) {
        console.log("logistics_fe_name_done_by_dwmpl1 value changed to:", targetNode_dw.value);
        //alert("inspection_location value changed to:", targetNode.value);

        getfedetail(targetNode_dw.value);
      }
    }
  });

  // Configuration for the observer (observe attribute changes)
  var config = { attributes: true };
  observer.observe(targetNode_dw, config);
  function getfedetail(feid) {
    data = {
      feid: feid,
      _csrf: $("#csrfToken").val(),
    };

    $.ajax({
      type: "POST",
      url: "getfedetail",
      // async:false,
      data: data,
      success: function (response) {

        // Check if the data object exists and contains 'first_name'
        if (response && response.data) {
          $("#logistics_fe_number").val(response.data.mobile);

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

  $("#schedule_inspection").prop("disabled", $("#schedule_inspection").prop("checked"));
  $("#inspection_started").prop("disabled", $("#inspection_started").prop("checked"));
  $("#inspection_completed").prop("disabled", $("#inspection_completed").prop("checked"));
  $("#submit_for_logistics").prop("disabled", $("#submit_for_logistics").prop("checked"));




});

///////////disable auto stage dd
document.addEventListener("DOMContentLoaded", function () {
  const stageSelect = document.getElementById("stages");
  const options = stageSelect.options;
  const disabledValues = [ "2", "3", "4", "8"]; // List of values to disable

  for (let i = 0; i < options.length; i++) {
    if (disabledValues.includes(options[i].value)) { // Check if the value is in the list
      options[i].disabled = true;
    }
  }
});


//code added by ptpatel on date 24-04-25

// Function to observe input value changes
function observeInputChanges(inputElement) {
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
          getproductdata(trid, `${inputElement.value}`);
          // checkQuantityMatch();
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

// Function to observe all matching inputs
function observeMatchingInputs() {
  // Match inputs with ID pattern 'productid_*1'
  const inputs = document.querySelectorAll(
    'input[id^="product_name_"][id$="1"]'
  );
  //code added by ptpatel
  // const fullinputs = document.querySelectorAll(
  //   'select[id^="prod_category_"][id$="1"]'
  // );
  //end code added by ptpatel
  inputs.forEach((input) => observeInputChanges(input));
  // fullinputs.forEach((select) => observeInputChanges(select));
  console.log(`Observers attached to ${inputs.length} inputs.`);
}

// Function to monitor dynamically added inputs
function monitorDynamicInputs() {
  const container = document.body; // Observe the entire document

  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      if (mutation.type === "childList" && mutation.addedNodes.length > 0) {
        mutation.addedNodes.forEach((node) => {
          if (node.nodeType === 1) {
            // Check for new matching inputs
            const newInputs = node.querySelectorAll(
              'input[id^="product_name_"][id$="1"]'
            );
            // console.log("deepika");
            newInputs.forEach((input) => observeInputChanges(input));

            // const newSelects = node.querySelectorAll(
            //   'select[id^="prod_category_"][id$="1"]'
            // );
            // // console.log("deepika");
            // newSelects.forEach((input) => observeInputChanges(input));

            $('input[id^="product_name_1"]').removeClass('V~O').addClass('V~M');
            // $('input[id^="prod_category_1"]').removeClass('DD~O').addClass('DD~M');
          }
        });
      }
    });
  });

  observer.observe(container, {
    childList: true, // Detect added elements
    subtree: true, // Include all child elements
  });

  console.log("Monitoring dynamic inputs for pattern: product_name_1");
}

// Initialize observers for existing and dynamic inputs
observeMatchingInputs();
monitorDynamicInputs();


function getproductdata(trid, productid) {
  trid = $.trim(trid)
  startLoading();
  console.log("product_id=>trid" + productid + "=>" + trid);
  var data = {
    Recordid: productid,
    _csrf: $("#csrfToken").val(),
  };
  let blockid = 145;
  let mainmodule = "inspection";
  let totalRows = $('#productTable' + blockid + ' tr').length;
  let geturl = getAbsoluteUrl();
  let url = geturl + mainmodule + "/getproductlist?blockid=" + blockid + "&cnt_rows=" + totalRows;
  $.ajax({
    type: "POST",
    url: "getproductdetails",
    // url: url,
    // async:false,
    data: data,
    success: function (data) {
      console.log("pro " + data);

      $("#product_subcategory_" + trid).val(data.data.sub_catagory_id).trigger('change');
      $('#uom_' + trid).val(data.data.uom).trigger('change');
      stopLoading();
    },
    error: function (data) {
      // if error occured

      alert("Error occured.please try again");
      stopLoading();
    },
    dataType: "json",
  });
}

//hide show inputs when stage change
$('#stages').on('change', handleStageChange);
$(document).ready(function () {
  // Bind change handler
  $('#stages').on('change', handleStageChange);

  // Run after a short delay to ensure prefilled values are available (especially for edit mode)
  setTimeout(handleStageChange, 50);
});

function handleStageChange() {
  const selectedValue = $('#stages').val();

  const inputMap = {
    '5': '#pav_hold_by_client_reason',
    '6': '#pav_hold_by_dwmpl_reason',
    '7': '#pav_cancelled_reason'
  };

  const allInputs = Object.values(inputMap);
  const $resumeDate = $('#resume_date');
  const $resumeGroup = $resumeDate.closest('.form-group');

  // Hide all inputMap fields and resume_date, reset their classes
  allInputs.forEach(function (selector) {
    const $input = $(selector);
    const $group = $input.closest('.form-group');
    $group.hide();
    $input.removeClass('V~M').addClass('V~O');
    $group.find('.help-block').html('');
  });

  $resumeGroup.hide();
  $resumeDate.removeClass('V~M').addClass('V~O');
  $resumeGroup.find('.help-block').html('');

  // Show the matching input field
  if (inputMap[selectedValue]) {
    const $selectedInput = $(inputMap[selectedValue]);
    const $group = $selectedInput.closest('.form-group');

    $group.show();
    $selectedInput.removeClass('V~O').addClass('V~M');
  }

  // Show resume_date only if selectedValue is '5' or '6'
  if (selectedValue === '5' || selectedValue === '6') {
    $resumeGroup.show();
    $resumeDate.removeClass('V~O').addClass('V~M');
  }
}

/////add atleast one row in full inspection
// List of table IDs

var tableIds = ['#productTable2657', '#productTable2658', '#productTable2659'];
var emptyTables = [];

// Loop through each table to check if it's empty
tableIds.forEach(function (tableId) {
  if ($(tableId).find('tbody tr').length === 0) {
    emptyTables.push(tableId);
  }
});

// If there are empty tables, show the message
if (emptyTables.length > 0) {
  var tableNames = emptyTables.map(function (tableId) {
    return tableId.replace('#', '');
  }).join(', ');

  //alert("Please add a row to at least one of the LAPTOP FULL INSPECTION DETAI,DESKTOP FULL INSPECTION DETAIL or TFT FULL INSPECTION DETAILL " );
  //disable save button
  //$(".savebutton").prop("disabled", true);
}
else {
  //enable save button
  //$(".savebutton").prop("disabled", false);
}

///////get vendor detail//////////
//////get field engineer data/////////////
// Create a MutationObserver to detect changes to the input vendor account
var targetNode_v = document.getElementById("vendor_spoc_name_done_by_vendor1");
var observer = new MutationObserver(function (mutationsList) {
  for (var mutation of mutationsList) {
    if (
      mutation.type === "attributes" &&
      mutation.attributeName === "value"
    ) {
      console.log("vendor_spoc_name_done_by_vendor value changed to:", targetNode_v.value);
      //alert("inspection_location value changed to:", targetNode.value);

      getvendordetail(targetNode_v.value);
    }
  }
});

// Configuration for the observer (observe attribute changes)
var config = { attributes: true };
observer.observe(targetNode_v, config);
function getvendordetail(vendorid) {
  data = {
    vendorid: vendorid,
    _csrf: $("#csrfToken").val(),
  };

  $.ajax({
    type: "POST",
    url: "getvendordetail",
    // async:false,
    data: data,
    success: function (response) {

      // Check if the data object exists and contains 'first_name'
      if (response && response.data) {
        $("#vendor_spoc_number").val(response.data.mobile);

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

/*
$('#insection_type').on('change', function () {
    const selectedValue = $(this).val();
    const full_inspection  = $('input[id="prod_category_1"]');
    const  random_inspection = $('input[id="product_name_1"]');

    if (selectedValue === '1') {
        // Full Inspection → Add V~M
          addRowBtn('2657', 'inspection');
          setTimeout(function() {
            full_inspection.removeClass('DD~O').addClass('DD~M');
          },1000);
    } else {
        // Random or None → Revert to V~O
        addRowBtn('145', 'inspection');
        setTimeout(function() {
          random_inspection.removeClass('V~O').addClass('V~M');
        },1000);
    }
});
*/
//end code added by ptpatel on date 24-04-25
// as per client change on date 21-06-25 code added by ptpatel
$(document).ready(function () {
 $(".section-formailites_vehicle_entry").hide();
 $("#vehicle_allowed_parking").trigger("change");

 //added on 22 sept 2025
 function updateInspectionVisibility() {
        if (!$('#schedule_inspection').is(':checked')) {
            // Schedule Inspection is NOT checked
            $('.section-inspection_started').hide();
            $('.section-inspection_completed').hide();
        } else {
            // Schedule Inspection is checked
            $('.section-inspection_started').show();

            if ($('#inspection_started').is(':checked')) {
                $('.section-inspection_completed').show();
            } else {
                $('.section-inspection_completed').hide();
            }
        }
    }

    // Run on page load
    updateInspectionVisibility();
});
$(document).on('change', "#vehicle_allowed_parking", function() {
  let vals = $(this).val();
  if(vals === '1'){
    $(".section-formailites_vehicle_entry").show();
  }
  else
  {
    $("#formailites_vehicle_entry").val("").trigger("change");
    $(".section-formailites_vehicle_entry").hide();
  }
});