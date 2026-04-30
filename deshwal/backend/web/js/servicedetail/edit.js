$(document).ready(function () {
  var newURL = window.location.href;
  var newURL = window.location.href;
  var module = "leads";
  var str = newURL.split(module);
  console.log("str" + str[0]);
  // var slicestr=newURL.substring(0,str);
  editusrl = str[0] + "leads/list";
  console.log("url" + editusrl);

  //check if any product if added
  mode = $("#mode").val();
  // if (mode == "Create") addRowBtn("197", "servicedetail");
  if (mode == "Create"){
    addRowBtn('197', 'servicedetail')
  .then((message) => {
    console.log(message); // "Data appended successfully"
  })
  .catch((error) => {
    console.log(error); // "Error occurred while appending data"
  });

  }
  // produt changes observer
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
            getServiceinfo(trid, `${inputElement.value}`);
            getbaseprice(trid);
          } else {
            nearestTr.id = "";
            console.log("No <tr> ancestor found");
          }
          checkinspectionfields(inputElement.value,nearestTr.id);
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
      'input[id^="service_type_"][id$="1"]'
    );
    inputs.forEach((input) => observeInputChanges(input));
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
                'input[id^="service_type_"][id$="1"]'
              );
              // console.log("deepika");
              newInputs.forEach((input) => observeInputChanges(input));
            }
          });
        }
      });
    });

    observer.observe(container, {
      childList: true, // Detect added elements
      subtree: true, // Include all child elements
    });

    console.log("Monitoring dynamic inputs for pattern: service_type_*1");
  }

  // Initialize observers for existing and dynamic inputs
  observeMatchingInputs();
  monitorDynamicInputs();

  // get productinfo
  function getServiceinfo(trid, service_type) {
    // alert(service_type);
    data = { service_type: service_type, _csrf: $("#csrfToken").val() };

    $.ajax({
      type: "POST",
      url: "getserviceinfo",
      // async:false,
      data: data,
      success: function (response) {
        console.log(response); // Log the entire response to check its structure

        // Check if the data object exists and contains 'first_name'
        if (response && response.data) {
          //  $("#product_description_"+trid).val(response.data.product_description);
          $("#hsn_code_" + trid).val(response.data.hsn_code);
          $("#sub_category_" + trid).val(response.data.sub_category);
          $("#category_" + trid).val(response.data.category);
          $("#uom_" + trid).val(response.data.uom);
          $("#uom_" + trid)
            .val(response.data.uom)
            .trigger("change");
          $("#std_cost_price_" + trid).val(response.data.cost_price);
          //   $("#warehouse_state").val(response.data.state);
          //   $("#warehouse_state_code").val(response.data.statecode);

          //   $("#warehouse_pincode").val(response.data.pincode);
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
  function getbaseprice(trid) {
    service_type = $("#service_type_" + trid + "1").val();
    qty_required = $("#qty_required_" + trid).val();
    // alert(productid);
    if (service_type && qty_required) {
      data = {
        service_type: service_type,
        qty_required: qty_required,
        _csrf: $("#csrfToken").val(),
      };

      $.ajax({
        type: "POST",
        url: "getbaseprice",
        // async:false,
        data: data,
        success: function (response) {
          console.log(response); // Log the entire response to check its structure

          // Check if the data object exists and contains 'first_name'
          if (response && response.data) {
            $("#unit_service_cost_" + trid).val(response.data.base_price);
            var totalbase_price = qty_required * response.data.base_price;
            $("#total_service_cost_" + trid).val(totalbase_price.toFixed(2));
            setTotalservicecost();
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

  function setTotalservicecost() {
    var total = 0;
    $("[class^=total_service_cost]").each(function () {
      var suffix = $(this).attr("id").match(/\d+$/)
        ? $(this).attr("id").match(/\d+$/)[0]
        : "";
      var baseprice = parseFloat($(`#total_service_cost_${suffix}`).val()) || 0;
      total += baseprice;
      $("#total_service_cost").val(total.toFixed(2));
    });
  }

  function calculate_row_margin(row) {
    var std_cost_price = parseFloat(row.find(".std_cost_price").val()) || 0;
    var marketing_expenses =
      parseFloat(row.find(".marketing_expenses").val()) || 0;
    var sale_price_eg =
      parseFloat(row.find(".sale_price_exclusive_gst").val()) || 0;
    var qty_required = parseFloat(row.find(".qty_required").val()) || 0;

    var margin = sale_price_eg - std_cost_price - marketing_expenses;
    var margin_percentage =
      sale_price_eg !== 0 ? (margin / sale_price_eg) * 100 : 0;
    var total = qty_required * sale_price_eg;

    row.find(".margin").val(margin.toFixed(2));
    row.find(".margin_percentage").val(margin_percentage.toFixed(2));
    row.find(".total_exclusive_gst").val(total.toFixed(2));
  }

  function calculate_cgstamout(row) {
    var total = parseFloat(row.find(".total_exclusive_gst").val()) || 0;
    var cgst_on_saleprice =
      parseFloat(row.find(".cgst_on_saleprice").val()) || 0;
    var sgst = parseFloat(row.find(".sgst").val()) || 0;
    var igst = parseFloat(row.find(".igst").val()) || 0;

    var cgst_amount = parseFloat(row.find(".cgst_amount").val()) || 0;
    var sgst_amount = parseFloat(row.find(".sgst_amount").val()) || 0;
    var igst_amount = parseFloat(row.find(".igst_amount").val()) || 0;

    var cgst_amount = (total * cgst_on_saleprice) / 100;
    var sgst_amount = (total * sgst) / 100;
    var igst_amount = (total * igst) / 100;
    row.find(".cgst_amount").val(cgst_amount.toFixed(2));
    row.find(".sgst_amount").val(sgst_amount.toFixed(2));
    row.find(".igst_amount").val(igst_amount.toFixed(2));

    var total_inclusive_gst = total + cgst_amount + sgst_amount + igst_amount;
    row.find(".total_inclusive_gst").val(total_inclusive_gst.toFixed(2));
  }

  $(document).on(
    "change",
    ".sale_price_exclusive_gst,.marketing_expenses,.qty_required",
    function () {
      var currentRow = $(this).closest("tr");
      calculate_row_margin(currentRow);

      if (currentRow.length) {
        // Check if the row exists
        var tridd = currentRow.attr("id"); // Use .attr("id") to get the ID
        if (tridd) {
          // Check if the row has an id
          // alert("Row ID: " + tridd);
          getbaseprice(tridd);
        } else {
          // alert("This row does not have an ID.");
        }
      } else {
        // alert("No row found.");
      }
    }
  );

  //.bill_from_warehouse,.ship_from_warehouse change done on date 08-09-2025 for ERP point 423
  $(document).on(
    "change",
    ".std_cost_price,.marketing_expenses,.sale_price_exclusive_gst,.qty_required,.service_type,.bill_from_warehouse,.bill_to_location,.cgst_on_saleprice,.sgst,.igst",
    function () {
      console.log("on change fire");
      var currentRow = $(this).closest("tr");
      calculate_cgstamout(currentRow);
      checkGstType();
      //getbaseprice(currentRow);
    }
  );
  $("#vendor_account_name").addClass("readonly-dd");

  $(document).on(
    "change",
    "[id^=std_cost_price_],[id^=marketing_expenses_]",
    function () {
      //Loop through marketing expenses
      var total = 0;
      $("[id^=marketing_expenses_]").each(function () {
        var suffix = $(this).attr("id").match(/\d+$/)
          ? $(this).attr("id").match(/\d+$/)[0]
          : "";
        var me = parseFloat($(`#marketing_expenses_${suffix}`).val()) || 0;
        total += me;
        $("#total_marketing_expenses").val(total.toFixed(2));
      });

      var sp_exgst = 0;
      $("[id^=total_exclusive_gst_]").each(function () {
        var suffix = $(this).attr("id").match(/\d+$/)
          ? $(this).attr("id").match(/\d+$/)[0]
          : "";
        var sp_ex = parseFloat($(`#total_exclusive_gst_${suffix}`).val()) || 0;
        sp_exgst += sp_ex;
        $("#total_sp_amount_exclusive_gst").val(sp_exgst.toFixed(2));
      });
    }
  );

  $(document).on(
    "change",
    "[id^=std_cost_price_],[id^=total_exclusive_gst_],[id^=total_inclusive_gst_], [id^=qty_required_]",
    function () {
      //Loop through marketing expenses

      var sp_exgst = 0;
      $("[id^=total_exclusive_gst_]").each(function () {
        var suffix = $(this).attr("id").match(/\d+$/)
          ? $(this).attr("id").match(/\d+$/)[0]
          : "";
        var sp_ex = parseFloat($(`#total_exclusive_gst_${suffix}`).val()) || 0;
        sp_exgst += sp_ex;
        $("#total_sp_amount_exclusive_gst").val(sp_exgst.toFixed(2));
      });

      var sp_Ingst = 0;
      $("[id^=total_inclusive_gst_]").each(function () {
        var suffix = $(this).attr("id").match(/\d+$/)
          ? $(this).attr("id").match(/\d+$/)[0]
          : "";
        var sp_in = parseFloat($(`#total_inclusive_gst_${suffix}`).val()) || 0;
        sp_Ingst += sp_in;
        $("#total_sp_amount_inclusive_gst").val(sp_Ingst.toFixed(2));
      });
    }
  );

  $(document).on("click", ".remove-row-btn", function () {
    var row = $(this).closest("tr");
    if (row.length) {
      // Check if the row exists
      var tridd = row.attr("id"); // Use .attr("id") to get the ID
      if (tridd) {
        // Check if the row has an id
        // alert("Row ID: " + tridd);
        getbaseprice(tridd);
      } else {
        // alert("This row does not have an ID.");
      }
    } else {
      // alert("No row found.");
    }
    row.remove(); // Remove the row from the table

    updateTotals(); // Call function to update totals after deletion
  });

  // Function to recalculate totals after row deletion
  function updateTotals() {
    var totalExclusive = 0;
    var totalInclusive = 0;
    var totalMarketing = 0;

    $("[id^=total_exclusive_gst_]").each(function () {
      var sp_ex = parseFloat($(this).val()) || 0;
      totalExclusive += sp_ex;
    });

    $("[id^=total_inclusive_gst_]").each(function () {
      var sp_in = parseFloat($(this).val()) || 0;
      totalInclusive += sp_in;
    });

    $("[id^=marketing_expenses_]").each(function () {
      var market_ex = parseFloat($(this).val()) || 0;
      totalMarketing += market_ex;
    });

    $("#total_sp_amount_exclusive_gst").val(totalExclusive.toFixed(2));
    $("#total_sp_amount_inclusive_gst").val(totalInclusive.toFixed(2));
    $("#total_marketing_expenses").val(totalMarketing.toFixed(2));
  }

  // location changes observer
  function observeLocationChanges(inputElement) {
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
            const trid = nearestTr.id;
            console.log("Nearest <tr> ID:", trid);
            checkGstType(trid);
            validationServicelocation(trid);
          } else {
            console.log("No <tr> ancestor found");
          }
        }
      });
    });

    observer.observe(inputElement, {
      attributes: true,
      attributeFilter: ["value"],
    });

    console.log(`Observer attached to input: ${inputElement.id}`);
  }

  // Function to observe all matching inputs
  function observeAllLocation() {
    const inputs = document.querySelectorAll(
      'input[id^="bill_to_location_"], input[id^="service_to_location_"],input[id^="bill_from_warehouse_"]'
    );
    inputs.forEach((input) => observeLocationChanges(input));
    console.log(`Observers attached to ${inputs.length} inputs.`);
  }

  // Monitor dynamically added inputs
  function monitorDynamicLocationInputs() {
    const container = document.body; // Observe the entire document

    const observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        if (mutation.type === "childList" && mutation.addedNodes.length > 0) {
          mutation.addedNodes.forEach((node) => {
            if (node.nodeType === 1) {
              // Check for new matching inputs
              const newInputs = node.querySelectorAll(
                //added bill from warehouse change done for ERP point 423
                'input[id^="bill_to_location_"], input[id^="service_to_location_"],input[id^="bill_from_warehouse_"]'
                

              );
              newInputs.forEach((input) => observeLocationChanges(input));
            }
          });
        }
      });
    });

    observer.observe(container, {
      childList: true,
      subtree: true,
    });

    console.log("Monitoring dynamic inputs for warehouses.");
  }

  // Initialize observers for warehouse fields
  observeAllLocation();
  monitorDynamicLocationInputs();

  function validationServicelocation(trid) {
    let shipLocation = $(`#service_to_location_${trid}1`).val();
    let service_type = $(`#service_type_${trid}1`).val();

    console.log("service_to_location_", $(`#service_to_location_${trid}1`));
    console.log("service_type_", $(`#service_type_${trid}1`));
    console.log("shipLocation", shipLocation);
    console.log("service_type", service_type);

    let isDuplicate = false;

    // Loop through all rows to check for duplicate service_to_location for the same service_type
    $("[id^='service_type_']").each(function () {
      let currentRowId = $(this).attr("id").match(/\d+/)[0]; // Extract the row number
      let currentServiceType = $(`#service_type_${currentRowId}1`).val();
      let currentShipLocation = $(
        `#service_to_location_${currentRowId}1`
      ).val();

      // Skip checking the current row itself
      if (parseInt(currentRowId) !== parseInt(trid)) {
        if (
          currentServiceType == service_type &&
          currentShipLocation == shipLocation
        ) {
          isDuplicate = true;
          return false; // Break out of the loop
        }
      }
    });

    if (isDuplicate) {
      alert(
        `Service To Location is already selected for same Service Type. Please choose a different Service To Location.`
      );
      $(`#service_to_location_${trid}1`).val("");
      $(`#service_to_location_${trid}`).val("");
    }
  }

  // Fetch GST type based on warehouse locations
  function checkGstType(trid) {
    console.log("checkGST call"+trid)
    // let billLocation = $(`#bill_to_location_${trid}1`).val();
    // let shipLocation = $(`#service_to_location_${trid}1`).val();
    // this changes is done based on ERP point 423- by ptpatel on date 08-09-2025
    /**In service detail, gst is fetched based on Bill to Location and Service to Location. Is it correct? || As discussed on 08/09/2025  it should be based on Bill From Warehouse  and Bill to Location */
    let bill_from_warehouse = $(`#bill_from_warehouse_${trid}1`).val();
    let bill_to_location = $(`#bill_to_location_${trid}1`).val();
    
    let service_type = $(`#service_type_${trid}1`).val();

    // if (billLocation && shipLocation) {
    if (bill_from_warehouse && bill_to_location) {
      $.ajax({
        url: "getlocationstates", // Yii2 controller action URL
        type: "POST",
        data: {
          // billLocation: billLocation,
          // shipLocation: shipLocation,
          bill_to_location: bill_to_location,
          bill_from_warehouse: bill_from_warehouse,
          service_type: service_type,
          _csrf: $("#csrfToken").val(),
        },
        dataType: "json",
        success: function (response) {
          console.log("billState:", response);
          if (response.success) {
            let billState = response.billState;
            let shipState = response.shipState;
            let gstRateStr = response.gst_percentage; // e.g. "5%"

            // 1. Remove the '%' character
            let numericGstStr = gstRateStr.replace("%", "");

            // 2. Convert to a float
            let gstRate = parseFloat(numericGstStr); // e.g. 5

            console.log(gstRate);

            // Now gstRate is a number (e.g., 5), so you can do arithmetic.
            if (billState === shipState) {
              // Apply CGST & SGST (Half GST each)
              let halfGST = (gstRate / 2).toFixed(2); // e.g. "2.50"
              $(`#cgst_on_saleprice_${trid}`).val(halfGST);
              $(`#sgst_${trid}`).val(halfGST);
              $(`#igst_${trid}`).val(0);
            } else {
              // Apply IGST (Full GST)
              $(`#cgst_on_saleprice_${trid}`).val(0);
              $(`#sgst_${trid}`).val(0);
              $(`#igst_${trid}`).val(gstRate);
            }
          } else {
            alert("Error fetching state codes.");
          }
        },

        error: function () {
          alert("Failed to fetch state data.");
        },
      });
    }
  }
});

//////////////////// on the class end validation code zitendra /////////////////////

////////////////////end calculation code zitendra /////////////////////////

////////////////////get vendor account////////////////
var related_to = $("#related_to").val();
var related_to_id = $("#related_to_id1").val();
if (related_to && related_to_id) {
  getvendor(related_to, related_to_id);
}
///on change related_to_id
// Create a MutationObserver to detect changes to the input vendor account
var targetNode = document.getElementById("related_to_id1");
var observer = new MutationObserver(function (mutationsList) {
  for (var mutation of mutationsList) {
    if (mutation.type === "attributes" && mutation.attributeName === "value") {
      var related_to = $("#related_to").val();
      var related_to_id = $("#related_to_id1").val();
      getvendor(related_to, related_to_id);

      console.log("related_to_id1 value changed to:", targetNode.value);
    }
  }
});
// Configuration for the observer (observe attribute changes)
var config = { attributes: true };
observer.observe(targetNode, config);

function getvendor(related_to, related_to_id) {
  data = {
    related_to: related_to,
    related_to_id: related_to_id,
    _csrf: $("#csrfToken").val(),
  };

  $.ajax({
    type: "POST",
    url: "getvendor",
    // async:false,
    data: data,
    success: function (response) {
      console.log(response); // Log the entire response to check its structure

      // Check if the data object exists and contains 'first_name'
      if (response && response.data) {
        $("#vendor_account_name").val(response.data.vendorname);
        $("#vendor_account_name1").val(response.data.vendor);
      } else {
        console.log("Invalid response format or missing data");
      }
    },
    error: function (data) {
      // if error occured

      console.log("Error occured.please try again");
    },
    dataType: "json",
  });
}
//////////////////end vendor account/////////////////////

//code added by ptpatel on date 03-05-25

// $(document).on('blur', "input[id^='service_type_']", function () {
//   var input = $(this);
//   var id_number = input.attr('id').split("_")[2];
//   const value = input.val().trim();
//   checkinspectionfields(value, value,tr_id);
// });
function checkinspectionfields(value,tr_id)
{
  if(value == 3)
  {
    // console.log("tr_id"+$("#billable_type_"+tr_id).val());
    $("#ship_from_warehouse_"+tr_id).removeClass("V~M").addClass("V~O");
    $("#marketing_expenses_"+tr_id).removeClass("V~M").addClass("V~O");
    $("#qty_required_"+tr_id).val("1");
    if($("#billable_type_"+tr_id).val() == 2)
      $("#sale_price_exclusive_gst_"+tr_id).removeClass("V~M").addClass("V~O");
    else
      $("#sale_price_exclusive_gst_"+tr_id).removeClass("V~O").addClass("V~M");
  }
  else{
    $("#ship_from_warehouse_"+tr_id).removeClass("V~M").addClass("V~O");
    $("#marketing_expenses_"+tr_id).removeClass("V~M").addClass("V~O");
    $("#qty_required_"+tr_id).val();
    if($("#billable_type_"+tr_id).val() == 1) 
      $("#sale_price_exclusive_gst_"+tr_id).removeClass("V~O").addClass("V~M")
  }
}
$(document).on('change', "[id^=billable_type_]", function() {
 
  let $tr = $(this).closest('tr');
  let tr_id = $tr.attr('id').trim();
  let $serviceTypeInput = $tr.find('[id^=service_type_'+tr_id+'1]');
  // console.log("serviceTypeInput"+$serviceTypeInput.val());
  if($serviceTypeInput.val() == 3)
      checkinspectionfields($serviceTypeInput.val(),tr_id);
  else
    $("#sale_price_exclusive_gst_"+tr_id).removeClass("V~O").addClass("V~M");

  //client change on date 23-06-25
  let billableTypeVal = $(this).val();
  console.log("billableTypeVal"+billableTypeVal);
  if (billableTypeVal == 2) { //2 = No
    
    $("#bill_to_location_" + tr_id).val("");
    $("#bill_from_warehouse_" + tr_id).val("");
    $("#bill_to_location_" + tr_id).closest(".vendor-input-wrapper").addClass("readonly-dd");
    $("#bill_from_warehouse_" + tr_id).closest(".vendor-input-wrapper").addClass("readonly-dd");
    $("#bill_to_location_" + tr_id).removeClass("V~M").addClass("V~O");

  } else {
    $("#bill_to_location_" + tr_id).closest(".vendor-input-wrapper").removeClass("readonly-dd");
    $("#bill_from_warehouse_" + tr_id).closest(".vendor-input-wrapper").removeClass("readonly-dd");
  }
  //end client change on date 23-06-25
});


//end code added by ptpatel 
