let abortImport = false;
let picklistCache = {};
let invalidLocations = [];
let totalRowsToProcess = 0;
let processedRows = 0;
let bulkRowIndex = null;
function abortWithError(fieldName, value, reason) {
    abortImport = true;
    $('#productTable62 tbody').empty();
    stopLoading();
    $('#errRow').text(bulkRowIndex + 1);
    $('#errField').text(fieldName); 
    $('#errValue').text(value);
    $('#errReason').text(reason);
    $('#importErrorModal').modal('show');
}
$(document).ready(function () {
  //commented by deepika on 21 nov 2025. this code was by Vishwas
  // var addRowDiv = $('.add-more-records').closest('.col-3');
  // if (addRowDiv.find('#bulk-upload-btn').length === 0 && addRowDiv.is(':visible') &&!addRowDiv.hasClass('disabled') &&!addRowDiv.is(':disabled')) {
  //   addRowDiv.append('<button class="btn btn-secondary ml-2" id="bulk-upload-btn" type="button">Bulk Upload CSV</button>');
  //   addRowDiv.append('<input type="file" id="bulk-upload-file" accept=".csv" style="display:none" />');
  //   addRowDiv.append('<a href="" class="btn btn-primary" style="margin-left: 5px;" id="sample-download" type="button">Sample download</a>');
  //    $('#sample-download').attr('href',
  //         'downloadsample');
  // }
  //added by deepika on 21 nov 2025

  var addRowBtn = $('.add-more-records');
  var addRowDiv = addRowBtn.closest('[class*="col-"]'); // match any col- class

  // Change col-3 → col-6
  addRowDiv.removeClass(function (index, className) {
    return (className.match(/col-\d+/g) || []).join(' ');
  }).addClass('col-6');

  // Only append once
  if (
    addRowDiv.find('#bulk-upload-btn').length === 0 &&
    addRowDiv.is(':visible') &&
    !addRowDiv.hasClass('disabled') &&
    !addRowDiv.is(':disabled')
  ) {
    // Create a wrapper for ALL 3 items
    var wrapper = $('<div class="d-flex flex-nowrap align-items-center gap-2"></div>');

    // Move Add Row button inside wrapper
    addRowBtn.appendTo(wrapper);

    // Bulk Upload button
    var uploadBtn = $('<button>', {
      id: 'bulk-upload-btn',
      type: 'button',
      class: 'btn btn-secondary',
      text: 'Bulk Upload CSV'
    });

    // File input
    var fileInput = $('<input>', {
      id: 'bulk-upload-file',
      type: 'file',
      accept: '.csv',
      style: 'display:none'
    });

    // Sample Download button
    var sampleBtn = $('<a>', {
      id: 'sample-download',
      class: 'btn btn-primary',
      href: 'downloadsample',
      text: 'Sample Download'
    });

    // Add all buttons to wrapper
    wrapper.append(uploadBtn);
    wrapper.append(sampleBtn);

    // Put wrapper into the column
    addRowDiv.html(wrapper);

    // Add hidden file input
    addRowDiv.append(fileInput);
  }


  $('#bulk-upload-btn').on('click', function () {
    $('#bulk-upload-file').val('');
    $('#bulk-upload-file').click();
  });

  $(document).on('click', '.sample-download', function () {
    window.location.href = '/uploads/downloadSample.csv';
  });

  $('#bulk-upload-file').on('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;

    let existingRows = $('#productTable62 tbody tr.product-row').length;

    if (existingRows > 0) {
        let confirmDelete = confirm(
            "Existing records will be deleted and new records will be uploaded.\n\nDo you want to continue?"
        );
        if (!confirmDelete) {
            $(this).val(""); 
            return;
        }
        $('#productTable62 tbody').empty(); 
    }

    const reader = new FileReader();
    reader.onload = function (evt) {
      const csvText = evt.target.result;
      const csvRows = parseCSV(csvText);
      if (csvRows.length === 0) return;
      totalRowsToProcess = csvRows.length;
      processedRows = 0;
      invalidLocations = [];

      bulkAddRows(csvRows);
    };
    reader.readAsText(file);
  });
});

// function normalizeCsvText(str) {
//   if (!str) return "";
//   return String(str)
//     .replace(/[\u00A0\u1680\u2000-\u200A\u202F\u205F\u3000]/g, ' ')
//     .replace(/\uFFFD/g, ' ')
//     .replace(/\s+/g, ' ')
//     .trim();
// }
function normalizeCsvText(str) {
    if (!str) return '';
    return String(str)
        .replace(/\u00A0/g, ' ') 
        .replace(/\\"/g, '"')     
        .replace(/""/g, '"')    
        .trim();
}

function parseCSV(data) {
  const lines = data.trim().split('\n');
  const headers = lines[0].split(',').map(h => normalizeCsvText(h));
  return lines.slice(1).map(line => {
    const cols = line.split(',').map(c => normalizeCsvText(c));
    let obj = {};
    headers.forEach((header, idx) => { obj[header] = cols[idx] || ''; });
    return obj;
  });
}


function bulkAddRows(csvRows) {
  startLoading();
  abortImport = false;
  const startRowCount = $('#productTable62 tbody tr.product-row').length;

  let i = 0;
  let addedCount = 0;

  async function processNext() {
    if (abortImport) {
      stopLoading();
      return;
    }
    while (i < csvRows.length && isRowEmpty(csvRows[i])) {
      i++;
    }

    if (i >= csvRows.length) {
      updateTotals();
      stopLoading();
      return;
    }
    bulkRowIndex = i;
    // if (!isRowValid(csvRows[i])) {
    //     i++;      
    //     processNext();
    //     return;
    // }
    if (!isRowValid(csvRows[i])) {
      $('#productTable62 tbody').empty();

      stopLoading();
      return;
    }
    bulkRowIndex = i;
    await addRowBtn('62', 'productdetail');

    const $row = $('#productTable62 tbody tr.product-row').eq(startRowCount + addedCount);
    await setRowFields($row, csvRows[i], i+1);
    if (abortImport) {
      stopLoading();
      return;
    }
    i++;
    addedCount++;

    setTimeout(processNext, 250);
  }

  processNext();
}



function isRowEmpty(csvRow) {
  return Object.values(csvRow).every(val => {
    return val === undefined || val === null || val.toString().trim() === '';
  });
}

function isRowValid(data) {
  const mandatoryFields = {
    "Product*": "Product",
    "Qty*": "Quantity",
  };
  let missingFields = [];
  Object.keys(mandatoryFields).forEach(key => {
    if (!data[key]) missingFields.push(mandatoryFields[key]);
  });
  if (missingFields.length > 0) {
    abortWithError(
            missingFields.join(', '),
            '',
            "Mandatory field(s) missing"
        );
    return false;
  }
  return true;
}

function setRowFields($row, data, rowIndex) {
  if (abortImport) return;
  const $productId = $row.find('.productid');
  $productId.val(data['Product*'] || '');

  const mapping = {
    "Vendor1": ".vendor1",
    "Vendor1 Pricing": ".vendor1_pricing",
    "Vendor2": ".vendor2",
    "Vendor2 Pricing": ".vendor2_pricing",
    "Pickup Location": ".pickup_location",
    "Billing from location": ".billing_from_location",
    "Shipping from location": ".shipping_from_location",
    "Bill to warehouse": ".bill_to_warehouse",
    "SP Inclusive GST": ".sp_inclusive_gst",
    "Asset Condition": "[id^=asset_condition_]",
    "All Accessories": "[id^=all_accessories_]",
    "No GST": "[id^=no_gst_]",
    "Quoted Price Inclusive GST": ".quoted_price_inclusive_gst",
    "Qty*": ".quantity_required",
  };

  Object.keys(mapping).forEach(key => {
    if (abortImport) return;
    if (!data[key]) return;

    let selector = mapping[key];
    let $field = $row.find(selector);

    if (key === "Asset Condition") {
      let fieldValue = normalizeCsvText(data[key] || "");
      fetchPicklistMap("asset_condition", function (map) {
        if (abortImport) return;

        let mappedID = map[fieldValue] || "";
        if (!mappedID) {
          abortWithError("Asset Condition", fieldValue, "Value not in dropdown options");
          return;
        }

        $field.val(mappedID).trigger("change");
      });
      return;
    }

    // ---------- ACCESSORIES ----------
    if (key === "All Accessories") {
      let fieldValue = normalizeCsvText(data[key] || "");
      fetchPicklistMap("all_accessories", function (map) {
        if (abortImport) return;

        let mappedID = map[fieldValue] || "";
        if (!mappedID) {
          abortWithError("All Accessories", fieldValue, "Value not in dropdown options");
          return;
        }

        $field.val(mappedID).trigger("change");
      });
      return;
    }

    // ---------- NO GST ----------
    if (key === "No GST") {
      let isChecked = String(data[key]).toLowerCase() === "yes";
      $field.prop("checked", isChecked).trigger("change");
      return;
    }

    if ([
      "Pickup Location",
      "Billing from location",
      "Shipping from location",
      "Bill to warehouse"
    ].includes(key)) {
      if ($field.length === 0) {
        abortImport = true;
        $('#productTable62 tbody').empty();
        stopLoading();
        abortWithError(         
          key,               
          data[key],          
          "Location field element not found"
        );
        return;
      }
      $field.val(data[key]);

      let id = $field.attr("id");
      if (!id) {
        abortImport = true;
        $('#productTable62 tbody').empty();
        stopLoading();
        abortWithError(
          key,
          data[key],
          "Location input has no id"
        );
        return;
      }
      let hiddenId = id.replace(/_([0-9]+)$/, "_$11");
      $row.find("#" + hiddenId).val(data[key]);

      fetchLocationId(
      $row.attr("id"),
      id,
      data[key],
      key,
      $field,
      rowIndex,
        function (success) {

          if (!success) {
            return;
          } else {
            if (!abortImport) {
              $field.trigger("change");
            }
          }

          processedRows++;
          checkIfImportFinished(); 
        }
      );

      return;
    }

    // ---------- PRICE / QTY ----------
    if (["Quoted Price Inclusive GST", "Qty*"].includes(key)) {
      $field.val(data[key])
        .trigger("input")
        .trigger("keyup")
        .trigger("change");
      return;
    }

    // ---------- DEFAULT ----------
    $field.val(data[key]).trigger("change");
  });

  $row.find("input, select").trigger("change");
}
function checkIfImportFinished() {
  if (abortImport) {
    return;
  }
  if (processedRows >= totalRowsToProcess) {

    if (invalidLocations.length > 0) {
      abortWithError(
                fieldType,
                displayValue,
                "Location Not Found"
            );
            return;
    }

    invalidLocations = [];
    processedRows = 0;
    totalRowsToProcess = 0;
  }
}

function fetchPicklistMap(columnName, callback) {

  // ✔ if already available, return immediately
  if (picklistCache[columnName]) {
    callback(picklistCache[columnName]);
    return;
  }

  // ✔ Fetch only once
  $.ajax({
    url: 'getpicklistmap',
    type: 'GET',
    data: { columnname: columnName },
    success: function (res) {

      if (!res.error) {
        picklistCache[columnName] = res;  // store result
      }

      callback(res);
    },
    error: function () {
      callback({});
    }
  });
}

function fetchLocationId(trid, inputId, displayValue, fieldType,$field,rowIndex = null, callback) {
  if (abortImport) {
    callback(false);
    return;
  }
  var vendor_account_name = $("#vendor_account_name1").val();

  $.ajax({
    type: "POST",
    url: "fetchlocationid",
    data: {
      display_value: displayValue,
      field_type: fieldType,
      vendor_account_name: vendor_account_name,
      _csrf: $("#csrfToken").val()
    },
    dataType: "json",

    success: function (response) {
      if (abortImport) {
        callback(false);
        return;
      }
      if (response.status === "success") {
        let hiddenId = inputId.replace(/_([0-9]+)$/, "_$11");
        $("#" + hiddenId).val(response.id);
        $field.val(response.name);
        callback(true);
      } else {
        // if (bulkRowIndex !== null) {  
        abortWithError(
                fieldType,
                displayValue,
                "Location Not Found"
            );
            return;
        // }
        callback(false);
        return;
      }
    },

    error: function () {
      if (!abortImport) {
        invalidLocations.push(displayValue);
      }
      callback(false);
    }
  });
}

$(document).ready(function () {
  $(document).on('change input', '.productid', function () {
    var $input = $(this);
    var $row = $input.closest('tr.product-row');
    var productidVal = $input.val();
    var trid = $row.attr('id');
    console.log(productidVal, 'productidVal');
    console.log(trid, 'trid');
    if (productidVal) {
      getProductinfo(trid, productidVal, true);
    }
  });

  $(document).on('change input',
    '.pickup_location, .billing_from_location, .shipping_from_location, .bill_to_warehouse',
    function () {

      var $input = $(this);
      var $row = $input.closest('tr.product-row');

      var displayValue = $input.val();
      var trid = $row.attr('id');
      var fieldType = $input.attr('class').split(" ")[0];

      if (!displayValue) return;
      return;
    });

  
  function getProductinfo(trid, productid, isName = false,rowIndex = null) {
    let cleanProductId = productid.replace(/^"|"$/g, '').replace(/\\"/g, '"');
    data = { productid: cleanProductId, _csrf: $("#csrfToken").val(), isName: isName };

    $.ajax({
      type: "POST",
      url: "getproductinfo",
      // async:false,
      data: data,
      success: function (response) {
        if (response && response.data && response.status === "success") {
          $("#product_description_" + trid).val(
            response.data.product_description
          );
          $(`#productid_${trid}1`).val(response.data.products_id);
          $("#hsn_code_" + trid).val(response.data.hsn_code);
          $("#subcategory_" + trid).val(response.data.subcategory);
          $("#category_" + trid).val(response.data.category);
          $("#uom_" + trid).val(response.data.uom_value);
          $("#cp_" + trid).val(response.data.cost_price);
          // if (response.data.model)
          $("#model_no_" + trid).val(response.data.model);
          if (response.data.make) $("#make_" + trid).val(response.data.make);
          // alert(response.data.cost_price);
          $("#calculated_sp_" + trid).val(response.data.cost_price);
          getcalculatedsp(trid, response.data.subcategory, '');
          setgst(trid);//added on 28 july 2025
          //   $("#warehouse_state").val(response.data.state);
          //   $("#warehouse_state_code").val(response.data.statecode);
          //   $("#warehouse_gstin_no").val(response.data.gstn);
          //   $("#warehouse_pincode").val(response.data.pincode);
        } else {
          // if (bulkRowIndex !== null) {
            abortWithError(
                "Product*",
                productid,
                "Product Not Found"
            );
            return;
        // }
        }
      },
      error: function (data) {

        alert("Error occured.please try again");
      },
      dataType: "json",
    });
  }

  function getLocationId(trid, inputId, displayValue) {
    $.ajax({
      type: "POST",
      url: "fetchLocationId",
      data: {
        display_value: displayValue,
        _csrf: $("#csrfToken").val()
      },
      dataType: "json",

      success: function (response) {
        if (response && response.status === "success" && response.location_id) {


          let hiddenId = inputId.replace(/_([0-9]+)$/, "_$11");

          $("#" + hiddenId).val(response.location_id);

          // Trigger dependent logic
          $("#" + inputId).trigger("change");
        }
      },

      error: function () {
        console.warn("Location ID fetch failed.");
      }
    });
  }

});


$(document).ready(function () {
  var newURL = window.location.href;
  var newURL = window.location.href;
  var module = "leads";
  var str = newURL.split(module);
  // console.log("str" + str[0]);
  // var slicestr=newURL.substring(0,str);
  editusrl = str[0] + "leads/list";
  // console.log("url" + editusrl);

  //check if any product if added
  mode = $("#mode").val();
  // if (mode == "Create") addRowBtn("62", "productdetail");
  // if (mode == "Create"){
  //   addRowBtn('62', 'productdetail')
  // .then((message) => {
  //   // console.log(message); // "Data appended successfully"  

  //    // if any existing no_gst_ is already checked, check the newly added one
  //   // if ($('[id^="no_gst_"]:checked').length > 0) {
  //   //     $('[id^="no_gst_"]').last().prop("checked", true);
  //   // }
  //   // $('[id^="no_gst_"]').trigger("change");
  // })
  // .catch((error) => {
  //   // console.log(error); // "Error occurred while appending data"
  // });

  // } 

  ///fetch product type added on 21 june by deepika
  var related_to_id1 = $("#related_to_id1").val();
  fetchproducttype(related_to_id1);
  function fetchproducttype(related_to_id) {
    $.ajax({
      type: "GET",
      url: "getproductype?related_to_id=" + related_to_id,
      // async:false,

      success: function (response) {

        // Check if the data object exists and contains 'first_name'
        if (response && response.data) {
          if (response.data.pricing_type_value != '') {
            var moname = $(".sm-modname").text();
            $(".sm-modname").html(moname + "  &nbsp;&nbsp;&nbsp; <strong>Pricing type: " + response.data.pricing_type_value + "</strong>");
          }

        } else {
          // console.log("Invalid response format or missing data");
        }
      },
      error: function (data) {
        // if error occured

        //alert("Error occured.please try again");
      },
      dataType: "json",
    });
  }
  ///end by deepika 21 june
  // produt changes observer
  // Function to observe input value changes
  function observeInputChanges(inputElement) {
    const observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        if (
          mutation.type === "attributes" &&
          mutation.attributeName === "value"
        ) {
          // console.log(
          //   `Value changed in ${inputElement.id}: ${inputElement.value}`
          // );
          const nearestTr = inputElement.closest("tr");
          if (nearestTr) {
            trid = nearestTr.id;
            // console.log("Nearest <tr> ID:", nearestTr.id);

            getbaseprice(trid, `${inputElement.value}`);
            getProductinfo(trid, `${inputElement.value}`);

          } else {
            nearestTr.id = "";
            // console.log("No <tr> ancestor found");
          }
        }
      });
    });

    observer.observe(inputElement, {
      attributes: true, // Observe attribute changes
      attributeFilter: ["value"], // Only watch 'value' attribute
    });

    // console.log(`Observer attached to input: ${inputElement.id}`);
  }

  // Function to observe all matching inputs
  function observeMatchingInputs() {
    // Match inputs with ID pattern 'productid_*1'
    const inputs = document.querySelectorAll(
      'input[id^="productid_"][id$="1"]'
    );
    inputs.forEach((input) => observeInputChanges(input));
    // console.log(`Observers attached to ${inputs.length} inputs.`);
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
                'input[id^="productid_"][id$="1"]'
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

    // console.log("Monitoring dynamic inputs for pattern: productid_*1");
  }

  // Initialize observers for existing and dynamic inputs
  observeMatchingInputs();
  monitorDynamicInputs();

  // get productinfo
  function getProductinfo(trid, productid) {
    // alert(productid);
    data = { productid: productid, _csrf: $("#csrfToken").val() };

    $.ajax({
      type: "POST",
      url: "getproductinfo",
      // async:false,
      data: data,
      success: function (response) {
        // console.log(response); // Log the entire response to check its structure

        // Check if the data object exists and contains 'first_name'
        if (response && response.data) {
          $("#product_description_" + trid).val(
            response.data.product_description
          );
          $("#hsn_code_" + trid).val(response.data.hsn_code);
          $("#subcategory_" + trid).val(response.data.subcategory);
          $("#category_" + trid).val(response.data.category);
          $("#uom_" + trid).val(response.data.uom_value);
          $("#cp_" + trid).val(response.data.cost_price);
          // if (response.data.model)
          $("#model_no_" + trid).val(response.data.model);
          if (response.data.make) $("#make_" + trid).val(response.data.make);
          // alert(response.data.cost_price);
          $("#calculated_sp_" + trid).val(response.data.cost_price);
          getcalculatedsp(trid, response.data.subcategory, '');
          setgst(trid);//added on 28 july 2025
          //   $("#warehouse_state").val(response.data.state);
          //   $("#warehouse_state_code").val(response.data.statecode);
          //   $("#warehouse_gstin_no").val(response.data.gstn);
          //   $("#warehouse_pincode").val(response.data.pincode);
        } else {
          // console.log("Invalid response format or missing data");
        }
      },
      error: function (data) {
        // if error occured

        alert("Error occured.please try again");
      },
      dataType: "json",
    });
  }

  //check changes on cgst.sgst ,igst
  const cpinput = document.querySelectorAll(
    'input[id^="cp_"], input[id^="cgst_"], input[id^="sgst_"],input[id^="igst_"],input[id^="quantity_required_"]'
  );

  cpinput.forEach((input) => {
    input.addEventListener("change", function () {
      const nearestTr = input.closest("tr");
      if (nearestTr) {
        trid = nearestTr.id;
        // console.log('Nearest <tr> ID:', nearestTr.id);
        // alert(trid);
        calgst(trid);
      } else {
        nearestTr.id = "";
        // console.log("No <tr> ancestor found");
      }
    });
  });
  // new logic
  // Event listeners for CP and Quantity fields in each row
  document.addEventListener("input", function (event) {
    if (
      event.target.classList.contains("cp") ||
      event.target.classList.contains("quantity_required") ||
      event.target.classList.contains("cgst") ||
      event.target.classList.contains("sgst") ||
      event.target.classList.contains("igst")
    ) {
      const row = event.target.closest(".product-row"); // Identify the specific row
      // calculateRowPrice(row);
    }
  });
  function calculateRowPrice(trid) {
    // alert("sdfdsf"+trid);
    cp = $("#cp_" + trid).val() || 0;
    quantity_required = $("#quantity_required_" + trid).val() || 0;
    cgst = $("#cgst_" + trid).val() || 0;
    sgst = $("#sgst_" + trid).val() || 0;
    igst = $("#igst_" + trid).val() || 0;
    totalcost = cp * quantity_required;
    cgstamout = (cgst / 100) * totalcost;
    sgstamout = (sgst / 100) * totalcost;
    igstamout = (igst / 100) * totalcost;
    totalcostwithprice = totalcost + cgstamout + sgstamout + igstamout;
    // alert(totalcostwithprice);
    $("#gst_amount_" + trid).val(cgstamout);
    $("#sgst_amount_" + trid).val(sgstamout);
    $("#igst_amount_" + trid).val(igstamout);
    $("#total_cp_" + trid).val(totalcost);
    $("#total_price_" + trid).val(totalcostwithprice);
    // calctotalamt();
  }
  function calctotalamt() {
    const gst_amount = document.querySelectorAll('input[id^="gst_amount_"]');
    cpinput.forEach((input) => {
      // console.log(input.value);
    });
  }

  // $(document).on('click', '.savebutton', function(e) {
  //   var form = document.getElementById("pristine-valid-example");
  // var pristine = new Pristine(form);

  //   // $('.savebutton').click(function(e){
  // console.log("clicked");

  //                 var isValid = true;
  // console.log("teregdfg fh");

  //         var valid = pristine.validate();
  //         if(valid && isValid){
  //       form.submit();
  //     }

  // });

  // location changes observer
  function observeLocationChanges(inputElement) {
    const observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        if (
          mutation.type === "attributes" &&
          mutation.attributeName === "value"
        ) {
          // console.log(
          //   `Value changed in ${inputElement.id}: ${inputElement.value}`
          // );
          const nearestTr = inputElement.closest("tr");

          if (nearestTr) {
            const trid = nearestTr.id;
            // console.log("Nearest <tr> ID:", trid);
            checkGstType(trid);
          } else {
            // console.log("No <tr> ancestor found");
          }
        }
      });
    });

    observer.observe(inputElement, {
      attributes: true,
      attributeFilter: ["value"],
    });

    // console.log(`Observer attached to input: ${inputElement.id}`);
  }

  // Function to observe all matching inputs
  function observeAllLocation() {
    const inputs = document.querySelectorAll(
      // 'input[id^="billing_from_location_"], input[id^="shipping_from_location_"]' 
      'input[id^="billing_from_location_"], input[id^="bill_to_warehouse_"],input[id^="productid_"]'
    );
    inputs.forEach((input) => observeLocationChanges(input));
    // console.log(`Observers attached to ${inputs.length} inputs.`);
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
                'input[id^="billing_from_location_"], input[id^="bill_to_warehouse_"],input[id^="productid_"]'
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

    // console.log("Monitoring dynamic inputs for warehouses.");
  }

  // Initialize observers for warehouse fields
  observeAllLocation();
  monitorDynamicLocationInputs();


});

// Fetch GST type based on warehouse locations
// this function move outside of document ready becuase cannot call from no_gst by ptpatel
function checkGstType(trid) {
  trid = trid.trim();
  //alert(trid+' '+$(`#billing_from_location_11`).val());
  // console.log('TRID =', trid, typeof trid);
  // console.log(`#billing_from_location_${trid}1`);

  let billLocation = $(`#billing_from_location_${trid}1`).val();
  // let shipLocation = $(`#shipping_from_location_${trid}1`).val();
  //please map Billing From Location and Bill to Warehouse
  let shipLocation = $(`#bill_to_warehouse_${trid}1`).val();
  //alert(shipLocation);
  let product_id = $(`#productid_${trid}1`).val();

  // console.log('billLocation ' + billLocation);
  // console.log('shipLocation ' + shipLocation);

  if (billLocation && shipLocation) {
    return new Promise((resolve) => {
      $.ajax({
        url: "getlocationstates", // Yii2 controller action URL
        type: "POST",
        data: {
          billLocation: billLocation,
          shipLocation: shipLocation,
          product_id: product_id,
          _csrf: $("#csrfToken").val(),
        },
        dataType: "json",
        success: function (response) {
          if (response.success) {
            let billState = response.billState;
            let shipState = response.shipState;
            let gstRate = response.gst_percentage;
            if (billState === shipState) {
              // ✅ Apply CGST & SGST (Half GST each)
              let halfGST = (gstRate / 2).toFixed(2);
              $(`#cgst_${trid}`).val(halfGST);
              $(`#sgst_${trid}`).val(halfGST);
              $(`#igst_${trid}`).val(0);
              setgst(trid);
              resolve();
            } else {
              // ✅ Apply IGST (Full GST)
              $(`#cgst_${trid}`).val(0);
              $(`#sgst_${trid}`).val(0);
              $(`#igst_${trid}`).val(gstRate);
              setgst(trid);
              resolve();
            }
          } else {
            // alert("Error fetching state codes.");
            resolve();
          }
        },
        error: function () {
          // alert("Failed to fetch state data.");
          resolve();
        },
      });
    });
  }
}
$(document).on("click", ".remove-row-btn", function () {
  var row = $(this).closest("tr");
  row.remove(); // Remove the row from the table

  updateTotals(); // Call function to update totals after deletion
});

// Function to recalculate totals after row deletion
function updateTotals() {
  // console.log("updateTotals call");
  var totalMarketing = 0;
  $("[id^=marketing_expenses_]").each(function () {
    var market_ex = parseFloat($(this).val()) || 0;
    totalMarketing += market_ex;
  });
  $("#total_marketing_expenses").val(totalMarketing.toFixed(2));


  var totalquotedInclusive = 0;
  var totalquotedExclusive = 0;

  $("[id^=total_quoted_price_inclusive_gst_]").each(function () {
    var qin = parseFloat($(this).val()) || 0;
    totalquotedInclusive += qin;
  });

  $("[id^=total_quoted_price_exclusive_gst_]").each(function () {
    var qex = parseFloat($(this).val()) || 0;
    totalquotedExclusive += qex;
  });


  $("#total_quoted_amt_inclusive_gst").val(totalquotedInclusive.toFixed(2));
  $("#total_quoted_amt_exclusive_gst").val(totalquotedExclusive.toFixed(2));
  //  console.log("in update total before 442");
  // console.log("set final_quoted_amount_incl_gst"+totalquotedInclusive.toFixed(2));
  // $("#final_quoted_amount_incl_gst").val(totalquotedInclusive.toFixed(2));


  var totalspInclusive = 0;
  var totalspExclusive = 0;
  // console.log("in update total before 449");
  $("[id^=total_sp_inclusive_gst_]").each(function () {
    var sp_in = parseFloat($(this).val()) || 0;
    totalspInclusive += sp_in;
  });

  $("[id^=total_sp_exclusive_gst_]").each(function () {
    var sp_ex = parseFloat($(this).val()) || 0;
    totalspExclusive += sp_ex;
  });

  // console.log("in update total before 460");
  $("#total_sp_amount_inclusive_gst").val(totalspInclusive.toFixed(2));
  $("#total_sp_amount_exclusive_gst").val(totalspExclusive.toFixed(2));
  // console.log("in update total before tcs");
  calculateTCS();
  // console.log("in update total after tcs");
  //ERP Point 433 change start from here
  let roundOffVal = $("#round_off").val().trim();

  // If empty or 0 → call updateTotals()
  if (roundOffVal !== "" || parseFloat(roundOffVal) !== 0) {
    let roundOff = parseFloat(roundOffVal) || 0;
    // console.log(roundOff);
    let baseTotal = parseFloat($("#total_quoted_amt_inclusive_gst").val()) || 0;
    // console.log(baseTotal);
    // Final inclusive = base exclusive ± round off
    let finalTotal = baseTotal + roundOff;
    // console.log(finalTotal);

    $("#total_quoted_amt_inclusive_gst").val(finalTotal.toFixed(2));
  }
  //end code for ERP 433

}




//////////////////// on change SP make vendor1 and vendor2 mandatory /////////////////////////
$(document).on(
  "change",
  "[id^=sp_inclusive_gst_], [id^=calculated_sp_],[id^=quoted_price_inclusive_gst_],[id^=marketing_expenses_],[id^=quantity_required_],[id^=direct_expenses_service_expens_1]",
  function () {
    //Loop through marketing expenses
    var total = 0;
    $("[id^=direct_expenses_service_expens_1]").each(function () {
      var suffix = $(this).attr("id").match(/\d+$/)
        ? $(this).attr("id").match(/\d+$/)[0]
        : "";

      getmargin(suffix);
      settotal_qotedgst(suffix);
      settotal_qotednogst(suffix);
      setgst(suffix);
    });
    $("[id^=marketing_expenses_]").each(function () {
      var suffix = $(this).attr("id").match(/\d+$/)
        ? $(this).attr("id").match(/\d+$/)[0]
        : "";
      var me = parseFloat($(`#marketing_expenses_${suffix}`).val()) || 0;

      total += me;
      $("#total_marketing_expenses").val(total.toFixed(2));

      getmargin(suffix);
      settotal_qotedgst(suffix);
      settotal_qotednogst(suffix);
      setgst(suffix);
    });
    // Loop through all elements with ids starting with 'sp_inclusive_gst_'
    var totalspgst = 0;
    var totalsp = 0;
    $("[id^=sp_inclusive_gst_]").each(function () {
      var suffix = $(this).attr("id").match(/\d+$/)
        ? $(this).attr("id").match(/\d+$/)[0]
        : "";

      if (suffix) {
        var sp_inclusive_gst =
          parseFloat($(`#sp_inclusive_gst_${suffix}`).val()) || 0;
        var calculated_sp =
          parseFloat($(`#calculated_sp_${suffix}`).val()) || 0;
        // var sp_exclusive_gst = (sp_inclusive_gst / 1.18).toFixed(2);//old commneted on 28 july2025
        //get gst 
        cgst = parseFloat($(`#cgst_${suffix}`).val()) || 0;
        sgst = parseFloat($(`#sgst_${suffix}`).val()) || 0;
        igst = parseFloat($(`#igst_${suffix}`).val()) || 0;
        gstrate = 0;
        if (igst != 0)
          gstrate = 1 + igst / 100;
        else if (cgst != 0)
          gstrate = 1 + (cgst + sgst) / 100;

        if (gstrate != 0) {
          var sp_exclusive_gst = (sp_inclusive_gst / gstrate).toFixed(2);
          ``;
          $(`#sp_exclusive_gst_${suffix}`).val(sp_exclusive_gst);
        }

        // Alert the value for debugging
        // console.log(sp_inclusive_gst);

        // Manipulate the vendor classes based on the comparison
        var vendor1 = $(`#vendor1_${suffix}`);
        var vendor2 = $(`#vendor2_${suffix}`);

        if (sp_inclusive_gst > calculated_sp) {
          vendor1.removeClass("V~O").addClass("V~M");
          vendor2.removeClass("V~O").addClass("V~M");
        } else {
          vendor1.removeClass("V~M").addClass("V~O");
          vendor2.removeClass("V~M").addClass("V~O");
        }
        getmargin(suffix);
        settotalspgst(suffix);
        settotalspnogst(suffix);

        var total_sp_inclusive_gst = parseFloat($(`#total_sp_inclusive_gst_${suffix}`).val()) || 0;
        totalspgst += total_sp_inclusive_gst;
        $("#total_sp_amount_inclusive_gst").val(totalspgst.toFixed(2));

        // alert(sp_exclusive_gst);
        var total_sp_exclusive_gst = parseFloat($(`#total_sp_exclusive_gst_${suffix}`).val()) || 0;
        totalsp += total_sp_exclusive_gst;
        $("#total_sp_amount_exclusive_gst").val(totalsp);
      }
    });
    $("[id^=quantity_required_]").each(function () {
      var suffix = $(this).attr("id").match(/\d+$/)
        ? $(this).attr("id").match(/\d+$/)[0]
        : "";
      // alert('fhfhfd');

      getbaseprice(suffix);
      setrowTotallogisticcost(suffix);
      // setTotallogisticcost();
      settotalspgst(suffix);
      settotalspnogst(suffix);
      getmargin(suffix);
      settotal_qotedgst(suffix);
      settotal_qotednogst(suffix);
      setgst(suffix);

    });
    var totalquotedgst = 0;
    var totalquoted = 0;
    $("[id^=quoted_price_inclusive_gst_]").each(function () {
      var suffix = $(this).attr("id").match(/\d+$/)
        ? $(this).attr("id").match(/\d+$/)[0]
        : "";

      var quoted_price_inclusive_gst =
        parseFloat($(`#quoted_price_inclusive_gst_${suffix}`).val()) || 0;
      // var quoted_price_gst_exclude = (
      //   quoted_price_inclusive_gst / 1.18
      // ).toFixed(2); // old commneted on 28july 2025

      //get gst 
      cgst = parseFloat($(`#cgst_${suffix}`).val()) || 0;
      sgst = parseFloat($(`#sgst_${suffix}`).val()) || 0;
      igst = parseFloat($(`#igst_${suffix}`).val()) || 0;
      gstrate = 0;
      if (igst != 0)
        gstrate = 1 + igst / 100;
      else if (cgst != 0)
        gstrate = 1 + (cgst + sgst) / 100;

      if (gstrate != 0) {
        var quoted_price_gst_exclude = truncateToDecimals(
          quoted_price_inclusive_gst / gstrate,
          4
        ).toFixed(4);


        // alert(quoted_price_gst_exclude);
        $(`#quoted_price_gst_exclude_${suffix}`).val(quoted_price_gst_exclude);

      }
      else {
        quoted_price_gst_exclude = 0;
      }

      //get cgst
      var cgstamt = quoted_price_inclusive_gst - quoted_price_gst_exclude;
      // $(`#cgst_amount_${suffix}`).val(cgstamt.toFixed(2));

      getmargin(suffix);
      settotal_qotedgst(suffix);
      settotal_qotednogst(suffix);
      setgst(suffix);

      var total_quoted_price_inclusive_gst = parseFloat($(`#total_quoted_price_inclusive_gst_${suffix}`).val()) || 0;

      totalquotedgst += total_quoted_price_inclusive_gst;
      $("#total_quoted_amt_inclusive_gst").val(totalquotedgst.toFixed(2));

      var total_quoted_price_exclusive_gst = parseFloat($(`#total_quoted_price_exclusive_gst_${suffix}`).val()) || 0;

      totalquoted += parseFloat(total_quoted_price_exclusive_gst);
      $("#total_quoted_amt_exclusive_gst").val(totalquoted.toFixed(2));
    });
    calculateTCS();
  }
);
function settotalspgst(suffix) {
  var sp_exclusive_gst =
    parseFloat($(`#sp_inclusive_gst_${suffix}`).val()) || 0;
  var quantity_required =
    parseFloat($(`#quantity_required_${suffix}`).val()) || 0;
  var total = (sp_exclusive_gst * quantity_required).toFixed(2);
  $(`#total_sp_inclusive_gst_${suffix}`).val(total);
}
function settotalspnogst(suffix) {
  var sp_exclusive_gst =
    parseFloat($(`#sp_exclusive_gst_${suffix}`).val()) || 0;
  var quantity_required =
    parseFloat($(`#quantity_required_${suffix}`).val()) || 0;
  var total = (sp_exclusive_gst * quantity_required).toFixed(2);
  $(`#total_sp_exclusive_gst_${suffix}`).val(total);
}
function settotal_qotedgst(suffix) {
  var quoted_price_inclusive_gst =
    parseFloat($(`#quoted_price_inclusive_gst_${suffix}`).val()) || 0;
  var quantity_required =
    parseFloat($(`#quantity_required_${suffix}`).val()) || 0;
  var total = (quoted_price_inclusive_gst * quantity_required).toFixed(2);
  // alert(total);
  $(`#total_quoted_price_inclusive_gst_${suffix}`).val(total);
}
function settotal_qotednogst(suffix) {
  return new Promise((resolve) => {
    var quoted_price_gst_exclude =
      parseFloat($(`#quoted_price_gst_exclude_${suffix}`).val()) || 0;
    var quantity_required =
      parseFloat($(`#quantity_required_${suffix}`).val()) || 0;
    var total = (quoted_price_gst_exclude * quantity_required).toFixed(2);
    // console.log("qge"+$(`#quoted_price_gst_exclude_${suffix}`).val()+"qr"+quantity_required+"tot="+total);
    $(`#total_quoted_price_exclusive_gst_${suffix}`).val(total);
    resolve(); // mark as finished
  });
}
function setgst(suffix) {
  var cgst = parseFloat($(`#cgst_${suffix}`).val()) || 0;
  var sgst = parseFloat($(`#sgst_${suffix}`).val()) || 0;
  var igst = parseFloat($(`#igst_${suffix}`).val()) || 0;
  var total_quoted_price_exclusive_gst = $(`#total_quoted_price_exclusive_gst_${suffix}`).val();
  var cgstamt = (cgst * total_quoted_price_exclusive_gst) / 100;
  var sgstamt = (sgst * total_quoted_price_exclusive_gst) / 100;
  var igstamt = (igst * total_quoted_price_exclusive_gst) / 100;
  // alert(cgst+' '+sgst+' '+igst);
  $(`#cgst_amount_${suffix}`).val(cgstamt.toFixed(2));
  $(`#sgst_amount_${suffix}`).val(sgstamt.toFixed(2));
  $(`#igst_amount_${suffix}`).val(igstamt.toFixed(2));
  // alert(cgst+sgst+igst);
  // console.log("tqegst"+total_quoted_price_exclusive_gst+"-cgstamt"+cgstamt+"-sgstamt"+sgstamt+"-igstamt"+igstamt);
  //added on 28 july 2025
  gstrate = 1 + ((cgst + sgst + igst) / 100);
  // alert(gstrate);
  var quoted_price_inclusive_gst =
    parseFloat($(`#quoted_price_inclusive_gst_${suffix}`).val()) || 0;

  var quoted_price_gst_exclude = truncateToDecimals(
    quoted_price_inclusive_gst / gstrate,
    4
  ).toFixed(4);

  var sp_inclusive_gst =
    parseFloat($(`#sp_inclusive_gst_${suffix}`).val()) || 0;
  $(`#quoted_price_gst_exclude_${suffix}`).val(quoted_price_gst_exclude);


  var sp_exclusive_gst = (sp_inclusive_gst / gstrate).toFixed(2);

  $(`#sp_exclusive_gst_${suffix}`).val(sp_exclusive_gst);


}
function getmargin(suffix) {
  var quoted_price_gst_exclude =
    parseFloat($(`#quoted_price_gst_exclude_${suffix}`).val()) || 0;
  var sp_exclusive_gst =
    parseFloat($(`#sp_exclusive_gst_${suffix}`).val()) || 0;
  var marketing_expenses =
    parseFloat($(`#marketing_expenses_${suffix}`).val()) || 0;
  var direct_expenses_service_expenses_total_qty =
    parseFloat(
      $(`#direct_expenses_service_expens_${suffix}`).val()
    ) || 0;
  var logistics_cost =
    parseFloat(
      $(`#logistics_cost_${suffix}`).val()
    ) || 0;
  // var margin =
  //   parseFloat(
  //     sp_exclusive_gst - direct_expenses_service_expenses_total_qty - logistics_cost - marketing_expenses -
  //     quoted_price_gst_exclude
  //   ) || 0;
  //SP (Exclusive GST) - Quoted price (GST exclude) New formula given on date 24-09-2025 on email
  var margin = parseFloat(sp_exclusive_gst - quoted_price_gst_exclude) || 0;
  // alert(sp_exclusive_gst +'-'+ direct_expenses_service_expenses_total_qty +'-'+ logistics_cost +'-'+ marketing_expenses +'-'+ quoted_price_gst_exclude);

  $(`#margin_${suffix}`).val(margin.toFixed(2));
  if (sp_exclusive_gst > 0)
    var marginpercentge = parseFloat(margin / sp_exclusive_gst).toFixed(2) * 100;
  else marginpercentge = 0;
  $(`#margin_percentage_${suffix}`).val(marginpercentge);
}

////////////////////end SP, vendor1 , vendor2 /////////////////////////

////////////calculate common margin and argin percenage/////
$(document).on(
  "change",
  " #total_sourcing_deal_amount, #total_sourcing_deal_cost, #total_sourcing_deal_sale, #service_sale,#service_cost,#product_cost,#product_sale",
  function () {
    // Initialize total values for all rows
    var total_sourcing_deal_sale = $("#total_sourcing_deal_sale").val() || 0;
    var total_sourcing_deal_cost = $("#total_sourcing_deal_cost").val() || 0;
    var totel_marketing_expenses = 0;

    // Loop through all rows
    $("[id^=marketing_expenses_]").each(function () {
      var suffix = $(this).attr("id").match(/\d+$/)
        ? $(this).attr("id").match(/\d+$/)[0]
        : "";

      var marketing_expenses =
        parseFloat($(`#marketing_expenses_${suffix}`).val()) || 0;
      totel_marketing_expenses += marketing_expenses;
    });
    var margin = 0;
    margin =
      parseFloat(
        total_sourcing_deal_sale -
        total_sourcing_deal_cost -
        totel_marketing_expenses
      ) || 0;
    if (margin < 0) margin = 0;
    $("#margin").val(margin.toFixed(2));
    var margin_percent = 0;
    if (margin > 0) margin_percent = (margin / total_sourcing_deal_sale) * 100;
    $("#margin_percentage").val(margin_percent);
  }
);
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

      // console.log("related_to_id1 value changed to:", targetNode.value);
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
      // console.log(response); // Log the entire response to check its structure

      // Check if the data object exists and contains 'first_name'
      if (response && response.data) {
        $("#vendor_account_name").val(response.data.vendorname);
        $("#vendor_account_name1").val(response.data.vendor);
      } else {
        // console.log("Invalid response format or missing data");
      }
    },
    error: function (data) {
      // if error occured

      // console.log("Error occured.please try again");
    },
    dataType: "json",
  });
}
//////////////////end vendor account/////////////////////
///////////////get base price///////////////////
function getbaseprice(trid) {
  productid = $("#productid_" + trid + "1").val();
  qty_required = $("#quantity_required_" + trid).val();

  if (productid && qty_required) {
    data = { productid: productid, qty_required: qty_required, _csrf: $("#csrfToken").val() };
    return new Promise((resolve, reject) => {
      $.ajax({
        type: "POST",
        url: "getbaseprice",
        // async:false,
        data: data,
        success: function (response) {
          // console.log(response); // Log the entire response to check its structure

          // Check if the data object exists and contains 'first_name'
          if (response && response.data) {

            $("#logistics_cost_" + trid).val(response.data.average);
            var total_logistics_cost = qty_required * response.data.average;
            $("#total_logistics_cost_" + trid).val(total_logistics_cost.toFixed(2));
            // setTotallogisticcost();
            resolve();
          } else {
            // console.log("Invalid response format or missing data");
            resolve();
          }
        },
        error: function (data) {
          // if error occured

          alert("Error occured.please try again");
          resolve();
        },
        dataType: "json",
      });
    });
  }
}
function setTotallogisticcost() {
  var total = 0;
  $("[class^=total_logistics_cost]").each(function () {
    var suffix = $(this).attr("id").match(/\d+$/)
      ? $(this).attr("id").match(/\d+$/)[0]
      : "";
    var baseprice = parseFloat($(`#total_logistics_cost_${suffix}`).val()) || 0;
    total += baseprice;
    $("#total_logistics_cost").val(total.toFixed(2));
  });

}
function setrowTotallogisticcost(suffix) {
  var qty_required = $("#quantity_required_" + suffix).val() || 0;
  var logistics_cost = $("#logistics_cost_" + suffix).val() || 0;
  var total_logistics_cost = qty_required * logistics_cost;
  $("#total_logistics_cost_" + suffix).val(total_logistics_cost.toFixed(2));

}
$(document).on('change', "[id^=asset_condition_]", function () {
  const nearestTr = $(this).closest("tr");
  if (nearestTr) {
    trid = nearestTr.attr("id");
    let subcategory_id = $("#subcategory_" + trid).val();
    // console.log("subcategory_id"+trid+"==>"+subcategory_id);
    let asset_condition = $(this).val();
    getcalculatedsp(trid, subcategory_id, asset_condition);
  }
  else {
    // console.log("trid not found.");
  }
});

function getcalculatedsp(tr_id, subcategory_id, asset_condition) {
  let newURL = window.location.href;
  let sourceParam = newURL.split('&')[1];
  let sourceId = sourceParam.split('=')[1];
  asset_condition = $("#asset_condition_" + tr_id).val();
  if (asset_condition != '') {
    $.ajax({
      type: "post",
      data: {
        sourceId: sourceId,//sourcing deal id
        subcategory_id: subcategory_id,
        asset_condition: asset_condition,
        _csrf: $("#csrfToken").val()
      },
      url: "getproductpricebook",
      // async:false,

      success: function (response) {

        if (response && response.data) {
          $("#calculated_sp_" + tr_id).val(response.data.base_amount_taxes_excluded);
        } else {
          // console.log("Invalid response format or missing data");
          $("#calculated_sp_" + tr_id).val("");
        }
      },
      error: function (data) {
        // if error occured

        //alert("Error occured.please try again");
      },
      dataType: "json",
    });
  }
}

//added on 5 sept 2025
function truncateToDecimals(num, decimals) {
  const factor = Math.pow(10, decimals);
  return Math.floor(num * factor) / factor;
}
///CR point No GST on date 13-09-2025
$(document).on("change", '[id^="no_gst_"]', async function () {
  // console.log("nogst checked count = " + $('[id^="no_gst_"]:checked').length);
  let isChecked = $(this).is(":checked");
  $('[id^="no_gst_"]').prop("checked", isChecked);//this check or uncheck all checkbox whose id start with no_gst_

  if ($('[id^="no_gst_"]:checked').length > 0) {
    $('[id^="cgst_"], [id^="sgst_"], [id^="igst_"]').val("0");
    $('[id^="cgst_amount_"], [id^="sgst_amount_"], [id^="igst_amount_"]').val("0");
    $('#productTable62 tr[id]').each(function () {
      let trId = $(this).attr("id").trim(); // e.g. "1", "2"
      setamountwithNOGST(trId);
    });
  } else {
    // optional: reset back if none checked

    const rows = $('#productTable62 tr[id]');
    for (const row of rows) {
      let trId = $(row).attr("id").trim();
      // console.log("trId = " + trId);

      await getbaseprice(trId); // waits for ajax update

      await checkGstType(trId);
      await settotalspgst(trId);
      await settotalspnogst(trId);
      await settotal_qotedgst(trId);
      await settotal_qotednogst(trId); // if made async
      await checkGstType(trId);
      await updateTotals();
      await $("#total_quoted_amt_exclusive_gst").trigger("change");
      await calculateMargin();
      await getmargin(trId);
      // await setrowTotallogisticcost(trId);
    }

    // setTotallogisticcost();
  }
});
$(document).on("change", "#marketing_expenses,#direct_expenses_service_expens,#total_logistics_cost,#total_quoted_amt_exclusive_gst", function () {
  // total_expence_cost
  // marketing_expenses = repair cost
  // direct_expenses_service_expens = spare cost
  let repair_cost = parseFloat($("#marketing_expenses").val() || 0);
  let spare_cost = parseFloat($("#direct_expenses_service_expens").val() || 0);
  let total_logistics_cost = parseFloat($("#total_logistics_cost").val() || 0);
  let total_quoted_amt_exclusive_gst = parseFloat($("#total_quoted_amt_exclusive_gst").val() || 0);
  let total_expence_cost = repair_cost + spare_cost + total_logistics_cost + total_quoted_amt_exclusive_gst;
  $("#total_expence_cost").val(total_expence_cost);
  calculateMargin();
});
function calculateMargin() {
  let total_sp = parseFloat($("#total_sp_amount_exclusive_gst").val() || 0);
  let total_expense = parseFloat($("#total_expence_cost").val() || 0);

  let margin = total_sp - total_expense;
  let margin_percentage = total_sp ? (margin / total_sp) * 100 : 0;

  $("#margin").val(margin.toFixed(2));
  $("#margin_percentage").val(margin_percentage.toFixed(2));
}

// Trigger when input changes manually
$(document).on("input", "#total_sp_amount_exclusive_gst,#total_expence_cost", function () {
  calculateMargin();
});

function setamountwithNOGST(trId) {
  $("#sp_exclusive_gst_" + trId).val($("#sp_inclusive_gst_" + trId).val());
  $("#quoted_price_gst_exclude_" + trId).val($("#quoted_price_inclusive_gst_" + trId).val());
  $("#total_sp_exclusive_gst_" + trId).val($("#total_sp_inclusive_gst_" + trId).val());
  $("#total_quoted_price_exclusive_gst_" + trId).val($("#total_quoted_price_inclusive_gst_" + trId).val());
  updateTotals();
  $("#total_logistics_cost").trigger("change");
  $("#total_quoted_amt_exclusive_gst").trigger("change");
  getmargin(trId);
  calculateMargin();
}

//ERP point 433
$(document).on("blur", "#round_off", function () {
  let $helpBlock = $(this).siblings(".help-block"); // assuming help-block is next to input

  // Allow only numbers, optional minus, optional decimals
  let regex = /^-?\d*(\.\d{0,2})?$/; // up to 2 decimals
  let roundOffVal = $(this).val().trim();
  if (!regex.test(roundOffVal) && roundOffVal !== "") {
    $helpBlock.text("Please enter a valid number (digits and optional . or -)");
    $(this).addClass("error");
    $(".savebutton").prop("disabled", true);
    return;
  } else {
    $helpBlock.text(""); // clear error
    $(this).removeClass("error");
    $(".savebutton").prop("disabled", false);
  }
  updateTotals();
  calculateTCS();// call here because total_quoted_amt_inclusive_gst change on call of roundoff
});


//end CR point of NO GST
//changes of TCS on date 19-09-2025 added by ptpatel
$(document).on("blur", "#tcs_percentage", function () {
  calculateTCS();
});
function calculateTCS() {
  // console.log("in caltcs");
  let tcs_percentage = parseFloat($("#tcs_percentage").val().trim()) || 0;
  // if (tcs_percentage) {
  let total_quoted_amt_inclusive_gst = parseFloat($("#total_quoted_amt_inclusive_gst").val().trim()) || 0;

  if (!isNaN(tcs_percentage)) {
    // if (tcs_percentage) {
    // console.log("in caltcs 1");
    let tcs_amount = total_quoted_amt_inclusive_gst * parseFloat(tcs_percentage / 100);

    // console.log(tcs_amount);
    $("#tcs_amount").val(tcs_amount);
    let final_quoted_amount_incl_gst = parseFloat(total_quoted_amt_inclusive_gst + tcs_amount);
    // console.log(final_quoted_amount_incl_gst);
    $("#final_quoted_amount_incl_gst").val(final_quoted_amount_incl_gst);
  }
  else {
    // console.log("in caltcs2");
    $("#tcs_amount").val(0);
    $("#final_quoted_amount_incl_gst").val(parseFloat(total_quoted_amt_inclusive_gst));
  }

}
// (function () {
//   const table = document.getElementById('productTable62');
//   if (!table) return;
//   const tbody = table.querySelector('tbody');

//   function initBody() {
//     tbody.querySelectorAll('tr').forEach(tr => {
//       const tds = tr.querySelectorAll('td');
//       tds.forEach((td, i) => {
//         if (!td.dataset.col) td.dataset.col = String(i + 1);
//       });
//     });
//   }

//   function recomputePinnedOffsets() {
//     const pinnedHeaders = Array.from(
//       table.querySelectorAll('thead th.pinned')
//     );

//     pinnedHeaders.sort((a, b) =>
//       Number(a.dataset.col) - Number(b.dataset.col)
//     );

//     let currentLeft = 0;
//     const offsets = {};

//     pinnedHeaders.forEach(th => {
//       const col = th.dataset.col;
//       offsets[col] = currentLeft;

//       const sample =
//         th || table.querySelector('tbody td[data-col="' + col + '"]');
//       const width = sample ? sample.getBoundingClientRect().width : 0;
//       currentLeft += width;
//     });

//     table.querySelectorAll('[data-col]').forEach(cell => {
//       if (cell.classList.contains('pinned')) return;
//       cell.style.left = '';
//     });

//     Object.keys(offsets).forEach(col => {
//       const left = offsets[col] + 'px';
//       table.querySelectorAll('[data-col="' + col + '"].pinned')
//         .forEach(cell => { cell.style.left = left; });
//     });
//   }

//   function togglePin(col) {
//     const header = table.querySelector('thead th[data-col="' + col + '"]');
//     if (!header) return;

//     const shouldPin = !header.classList.contains('pinned');

//     header.classList.toggle('pinned', shouldPin);

//     tbody.querySelectorAll('td[data-col="' + col + '"]')
//       .forEach(td => {
//         if (!td.classList.contains('col-pinned')) return;
//         td.classList.toggle('pinned', shouldPin);
//         if (!shouldPin) td.style.left = '';
//       });

//     const icon = header.querySelector('.pin-icon[data-col="' + col + '"]');
//     if (icon) {
//       icon.style.color = shouldPin ? '#5c9cff' : '#add8e6';
//     }

//     if (!shouldPin) header.style.left = '';

//     recomputePinnedOffsets();
//   }


//   table.addEventListener('click', e => {
//     const icon = e.target.closest('.pin-icon');
//     const th   = e.target.closest('th');
//     let col = null;

//     if (icon) col = icon.dataset.col;
//     else if (th && th.dataset.col) col = th.dataset.col;
//     if (!col) return;

//     const header = table.querySelector('thead th[data-col="' + col + '"]');
//     if (!header || !header.classList.contains('col-pinned')) return;

//     togglePin(col);
//   });

//   window._pc_recomputePinnedOffsets = recomputePinnedOffsets;

//   initBody();
//   recomputePinnedOffsets();
// })();
// function resetPinnedColumns() {
//   const table = document.getElementById('productTable62');
//   if (!table) return;

//   table.querySelectorAll('th.pinned, td.pinned').forEach(cell => {
//     cell.classList.remove('pinned');
//     cell.style.left = '';
    
//   });

//   table.querySelectorAll('.pin-icon').forEach(icon => {
//     icon.classList.remove('active');    
//     icon.style.color = '#add8e6';    
//   });
// }
// (function () {
//   const table = document.getElementById('productTable62');
//   if (!table) return;
//   const tbody = table.querySelector('tbody');
//   if (!tbody) return;

//   let lastRowCount = tbody.querySelectorAll('tr').length;

//   const observer = new MutationObserver(() => {
//     const currentRowCount = tbody.querySelectorAll('tr').length;

  
//     if (currentRowCount > lastRowCount) {
//       resetPinnedColumns();       
//     }

//     lastRowCount = currentRowCount;
//   });

//   observer.observe(tbody, {
//     childList: true,   
//   });
// })();

//end code of TCS on date 19-09-2025 added by ptpatel 