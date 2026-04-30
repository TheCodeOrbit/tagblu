$(document).ready(function () {
  var newURL = window.location.href;
  var newURL = window.location.href;
  var module = "opportunities";
  var str = newURL.split(module);
  console.log("str" + str[0]);
  // var slicestr=newURL.substring(0,str);
  editusrl = str[0] + "opportunities/list";
  console.log("url" + editusrl);

  // var form = document.getElementById("pristine-valid-example");
  // var pristine = new Pristine(form);
  // $(document).on("click", ".savebutton", function (e) {
  //   // alert("checking");
  //   // $('.savebutton').click(function(e){
  //   console.log("clicked");

  //   var isValid = true;
  //   console.log("teregdfg fh");

  //   var valid = pristine.validate();
  //   if (valid && isValid) {
  //     form.submit();
  //   }
  // });

  // get exchangerate
  const modeInput = document.getElementById("mode");
  if (modeInput && modeInput.value === "Create") {
    // initialize currency with INr
    $("#currency").val("1").trigger("change");
    data = { currency: 1, _csrf: $("#csrfToken").val() };

    //end ddepika
    getexchangerate(data);
    fetchtermscondition();

  }
  $(document).on("change", "#currency", function () {
    data = { currency: $(this).val(), _csrf: $("#csrfToken").val() };

    getexchangerate(data);
  });
  //end exchange rate
});

function getexchangerate(data) {
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
}

//show only won
// Hide all options except the one with a specific value
$("#opportunity_stage option").each(function () {
  if ($(this).val() != "16" && $(this).val() !== "") {
    // Show only the option with value "16" = won
    $(this).remove(); // Remove options that don't match
  }
});

/////////add a row on create///////////
const mode = document.getElementById("mode");
if (mode && mode.value === "Create") {
  // addRowBtn('105', 'quotes');
  //get products from product detail
  getproductdetail();
}

//get vendor locations i.e, bill location
// Create a MutationObserver to detect changes to the input vendor account
var targetNode = document.getElementById("bill_name1");
var observer = new MutationObserver(function (mutationsList) {
  for (var mutation of mutationsList) {
    if (mutation.type === "attributes" && mutation.attributeName === "value") {
      console.log("bill_name1 value changed to:", targetNode.value);

      getbilllocation();
    }
  }
});

// Configuration for the observer (observe attribute changes)
var config = { attributes: true };
observer.observe(targetNode, config);
function getbilllocation() {
  data = {
    bill_location: $("#bill_name1").val(),
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
        // for below field only - as per client change it will come from account and it should be autofill and readonly
        // $("#bill_legal_name").val(response.data.legal_entity_name);
        $("#bill_address").val(response.data.address);
        $("#bill_state").val(response.data.state);
        $("#bill_state_code").val(response.data.state_code);
        $("#bill_gstin_no_uin").val(response.data.gstin_no_uin);
        $("#bill_pincode").val(response.data.pincode);
        $("#bill_pan_no").val(response.data.pan_no);
        $("#bill_city").val(response.data.city);
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

//get warehouse
// Create a MutationObserver to detect changes to the input vendor account
var targetNode = document.getElementById("business_entity1");
var observer = new MutationObserver(function (mutationsList) {
  for (var mutation of mutationsList) {
    if (mutation.type === "attributes" && mutation.attributeName === "value") {
      getwarehouse();
      console.log("business_entity value changed to:", targetNode.value);
    }
  }
});
// Configuration for the observer (observe attribute changes)
var config = { attributes: true };
observer.observe(targetNode, config);
function getwarehouse() {
  $("#warehouse_name").val("");
  $("#warehouse_address").val("");
  $("#warehouse_state").val("");
  $("#warehouse_state_code").val("");
  $("#warehouse_gstin_no").val("");
  $("#warehouse_pincode").val("");
  $("#warehouse_city").val("");

  data = {
    business_entity: $("#business_entity1").val(),
    _csrf: $("#csrfToken").val(),
  };

  $.ajax({
    type: "POST",
    url: "getwarehouse",
    // async:false,
    data: data,
    success: function (response) {
      console.log(response); // Log the entire response to check its structure

      // Check if the data object exists and contains 'first_name'
      if (response && response.data) {
        $("#warehouse_name").val(response.data.warehouse_name);
        $("#warehouse_address").val(response.data.address);
        $("#warehouse_state").val(response.data.state);
        $("#warehouse_state_code").val(response.data.statecode);
        $("#warehouse_gstin_no").val(response.data.gstn);
        $("#warehouse_pincode").val(response.data.pincode);
        $("#warehouse_city").val(response.data.city);
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

///////////////// get product info block start////////////////////////////
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
          // getProductinfo(trid, `${inputElement.value}`);
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
  // Match inputs with ID pattern 'product_name_*1'
  const inputs = document.querySelectorAll(
    'input[id^="product_name_"][id$="1"]'
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
              'input[id^="product_name_"][id$="1"]'
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

  console.log("Monitoring dynamic inputs for pattern: product_name_*1");
}

// Initialize observers for existing and dynamic inputs
observeMatchingInputs();
monitorDynamicInputs();

// get productinfo
function getProductinfo(trid, productid) {
  // alert(productid);
  data = { productid: productid, _csrf: $("#csrfToken").val() };

  $("#description_" + trid).val("");
  $("#hsn_code_" + trid).val("");
  // $("#subcategory_"+trid).val(response.data.subcategory);
  $("#category_" + trid).val("");
  $("#uom_" + trid).val("");
  $("#list_price_" + trid).val("");
  calculateamount();

  $.ajax({
    type: "POST",
    url: "getproductinfo",
    // async:false,
    data: data,
    success: function (response) {
      console.log(response); // Log the entire response to check its structure

      // Check if the data object exists and contains 'first_name'
      if (response && response.data) {
        $("#description_" + trid).val(response.data.product_description);
        $("#hsn_code_" + trid).val(response.data.hsn_code);
        // $("#subcategory_"+trid).val(response.data.subcategory);
        $("#category_" + trid).val(response.data.category);
        $("#uom_" + trid).val(response.data.uom_value);
        $("#list_price_" + trid).val(response.data.cost_price);
        $("#quantity_" + trid).val("1");
        calculateamount();
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
/////////////////get product info block end/////////////////////////

////////////////calculate price///////////////
// Function to convert numbers to words
function numberToWords(num) {
  const a = [
    "",
    "one",
    "two",
    "three",
    "four",
    "five",
    "six",
    "seven",
    "eight",
    "nine",
    "ten",
    "eleven",
    "twelve",
    "thirteen",
    "fourteen",
    "fifteen",
    "sixteen",
    "seventeen",
    "eighteen",
    "nineteen",
  ];
  const b = [
    "",
    "",
    "twenty",
    "thirty",
    "forty",
    "fifty",
    "sixty",
    "seventy",
    "eighty",
    "ninety",
  ];
  const c = ["hundred", "thousand", "lakh", "crore"];

  if (num === 0) return "zero";

  let words = "";

  if (num >= 10000000) {
    words += numberToWords(Math.floor(num / 10000000)) + " crore ";
    num %= 10000000;
  }
  if (num >= 100000) {
    words += numberToWords(Math.floor(num / 100000)) + " lakh ";
    num %= 100000;
  }
  if (num >= 1000) {
    words += numberToWords(Math.floor(num / 1000)) + " thousand ";
    num %= 1000;
  }
  if (num >= 100) {
    words += numberToWords(Math.floor(num / 100)) + " hundred ";
    num %= 100;
  }
  if (num > 0) {
    if (num < 20) {
      words += a[num];
    } else {
      words += b[Math.floor(num / 10)];
      if (num % 10 > 0) words += " " + a[num % 10];
    }
  }

  return words.trim();
}
////////////////////start calculation code zitendra /////////////////////////
//$(document).on("change", "[id^=quantity_required_]", function () { alert();
$(document).on(
  "change",
  "[id^=quantity_],[id^=list_price_],[id^=cost_price_], [id^=cgst_percent_], [id^=sgst_percent_], [id^=igst_percent_], #total_cost_price, #total_base_cp, #total_price",
  function () {
    calculateamount();
  }
);
function calculateamount() {
  // Initialize total values for all rows
  var total_cost_price = 0;
  var total_base_cp = 0;
  var total_price = 0;
  var total_cgst_amount = 0;
  var total_sgst_amount = 0;
  var total_igst_amount = 0;

  // Loop through all rows
  $("[id^=cost_price_]").each(function () {
    var suffix = $(this).attr("id").match(/\d+$/)
      ? $(this).attr("id").match(/\d+$/)[0]
      : "";

    if (suffix) {
      var quantity = parseFloat($(`#quantity_${suffix}`).val()) || 0;
      var cost_price = parseFloat($(`#cost_price_${suffix}`).val()) || 0;
      var cgst = parseFloat($(`#cgst_percent_${suffix}`).val()) || 0;
      var sgst = parseFloat($(`#sgst_percent_${suffix}`).val()) || 0;
      var igst = parseFloat($(`#igst_percent_${suffix}`).val()) || 0;

      // Calculate Total CP for this row
      var total_cp = quantity * cost_price;
      $(`#basic_price_${suffix}`).val(total_cp.toFixed(2));

      // Calculate GST Amounts
      var cgst_amount = cgst > 0 ? (total_cp * cgst) / 100 : 0;
      $(`#cgst_amount_${suffix}`).val(cgst_amount.toFixed(2));

      var sgst_amount = sgst > 0 ? (total_cp * sgst) / 100 : 0;
      $(`#sgst_amount_${suffix}`).val(sgst_amount.toFixed(2));

      var igst_amount = igst > 0 ? (total_cp * igst) / 100 : 0;
      $(`#igst_amount_${suffix}`).val(igst_amount.toFixed(2));

      // Calculate Total Price for this row
      var totalGST = cgst_amount + sgst_amount + igst_amount;
      var total_row_price = total_cp + totalGST;
      $(`#total_amount_${suffix}`).val(total_row_price.toFixed(2));

      // Add row totals to the global totals
      total_cost_price += total_cp;
      total_base_cp += quantity;
      total_price += total_row_price;
      total_cgst_amount += cgst_amount;
      total_sgst_amount += sgst_amount;
      total_igst_amount += igst_amount;
    }
  });

  $("[id^=cost_price_]").each(function () {
    var suffix = $(this).attr("id").match(/\d+$/)
      ? $(this).attr("id").match(/\d+$/)[0]
      : "";

    if (suffix) {
      var quantity = parseFloat($(`#quantity_${suffix}`).val()) || 0;
      var cost_price = parseFloat($(`#cost_price_${suffix}`).val()) || 0;

      // Calculate Total CP for this row
      var total_cp = quantity * cost_price;
      $(`#basic_cp_${suffix}`).val(total_cp.toFixed(2));
    }
  });

  // Update global totals
  $("#total_cgst_amount").val(total_cgst_amount.toFixed(2));
  $("#total_sgst_amount").val(total_sgst_amount.toFixed(2));
  $("#total_igst_amount").val(total_igst_amount.toFixed(2));
  $("#sub_total").val(total_cost_price.toFixed(2));
  //$('#final_amount').val(total_price.toFixed(2));
  // var rounded_total_price = Math.round(total_price);
  $("#final_amount").val(total_price.toFixed(2));
  // Total amount in words (optional placeholder)
  // Convert total price to words
  var total_price_words = numberToWords(total_price);
  //$('#amount_word').val(total_price_words.charAt(0).toUpperCase() + total_price_words.slice(1));
}

////////////////////end calculation code zitendra /////////////////////////
////////end price////////////////////

// ///////////set default stage//////////////
document.addEventListener("DOMContentLoaded", function () {
  // Check if mode is 'Create'
  const modeInput = document.getElementById("mode");
  if (modeInput && modeInput.value === "Create") {
    // Select the dropdown by ID
    const quote_stage = $("#quote_stage"); // Using jQuery for Select2

    if (quote_stage.length) {
      // Set the default value for Select2 dropdown
      quote_stage.val("5").trigger("change"); // Use the value corresponding to "Quote generated"
    }
    //set today date
    // Get today's date in YYYY-MM-DD format
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, "0");
    var mm = String(today.getMonth() + 1).padStart(2, "0"); // Months are zero-indexed
    var yyyy = today.getFullYear();

    var todaydate = dd + "-" + mm + "-" + yyyy; // Format the date as YYYY-MM-DD
    // alert('"'+todaydate+'"');

    $("#quote_creation_date").val(todaydate);
    ///////////15 days + creation date = expiry date

    setTimeout(() => {
      flatpickr("#quote_creation_date", {
        defaultDate: new Date(),
        dateFormat: "d-m-Y",
      });

      getexpirydate();
    }, 500); // Waits for 600 milliseconds (1/2 seconds)

    // $("#expiry_date").val(expiry_date);

    getvendoraccount();
  }
});

function getexpirydate() {
  const dateValue = $("#quote_creation_date").val(); // Get the quote creation date (dd-mm-yyyy)
  const dateParts = dateValue.split("-"); // Split into [dd, mm, yyyy]

  if (dateParts.length === 3) {
    const day = parseInt(dateParts[0], 10);
    const month = parseInt(dateParts[1], 10) - 1; // Month is 0-based in JavaScript
    const year = parseInt(dateParts[2], 10);

    const selectedDate = new Date(year, month, day);

    if (!isNaN(selectedDate.getTime())) {
      // Add 15 days
      selectedDate.setDate(selectedDate.getDate() + 15);

      // Format the new date as dd-mm-yyyy for display
      const newDay = ("0" + selectedDate.getDate()).slice(-2);
      const newMonth = ("0" + (selectedDate.getMonth() + 1)).slice(-2); // Months are 0-based
      const newYear = selectedDate.getFullYear();
      const formattedDate = `${newDay}-${newMonth}-${newYear}`;

      // Update expiry_date input
      $("#expiry_date").val(formattedDate);

      // Initialize Flatpickr only once
      if ($("#expiry_date").data("flatpickr")) {
        $("#expiry_date").data("flatpickr").setDate(formattedDate, true);
      } else {
        $("#expiry_date").flatpickr({
          dateFormat: "d-m-Y",
          defaultDate: formattedDate,
          allowInput: false,
          readOnly: true,
        });
      }
    }
  }
}

///////////on change ralated to id
// Create a MutationObserver to detect changes to the input related_to_id1
var targetNode = document.getElementById("related_to_id1");
var observer = new MutationObserver(function (mutationsList) {
  for (var mutation of mutationsList) {
    if (mutation.type === "attributes" && mutation.attributeName === "value") {
      getvendoraccount();
      console.log("related_to_id1 value changed to:", targetNode.value);
    }
  }
});
// Configuration for the observer (observe attribute changes)
var config = { attributes: true };
observer.observe(targetNode, config);

////////get vendor name and KYC//////////////
// Create a MutationObserver to detect changes to the input vendor account
var targetNode = document.getElementById("account_name1");
var observer = new MutationObserver(function (mutationsList) {
  for (var mutation of mutationsList) {
    if (mutation.type === "attributes" && mutation.attributeName === "value") {
      getvendordetail();
      console.log("account_name1 value changed to:", targetNode.value);
    }
  }
});

// Configuration for the observer (observe attribute changes)
var config = { attributes: true };
observer.observe(targetNode, config);
function getvendordetail() {
  $("#kyc_status").val("");
  $("#payment_terms").val("");

  data = {
    account_name: $("#account_name1").val(),
    _csrf: $("#csrfToken").val(),
  };

  $.ajax({
    type: "POST",
    url: "getvendordetail",
    // async:false,
    data: data,
    success: function (response) {
      console.log("response"+response); // Log the entire response to check its structure

      // Check if the data object exists and contains 'first_name'
      if (response && response.data) {
        // $("#vendor_name").val(response.data.vendor_name);
        $("#kyc_status").val(response.data.kyc_status);
        $("#payment_terms").val(response.data.credit_days).trigger("change");
         // bill_legal_name changes as per client ERP changes on date 20-06-25 by ptpatel
        $("#bill_legal_name").val(response.data.legal_entity);
       
        if (response.data.credit_days == null) {
          let Paymentterms = $("#payment_terms");
          let PaymenttermsError =
            Paymentterms.closest(".form-group").find(".help-block");
          PaymenttermsError.text("");
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

/////////////////////vendor account///////////////
function getvendoraccount() {
  $("#account_name").val("");
  $("#account_name1").val("");
  $("#margin_percent").val("");
  data = {
    related_to: $("#related_to").val(),
    related_to_id: $("#related_to_id1").val(),
    _csrf: $("#csrfToken").val(),
  };

  $.ajax({
    type: "POST",
    url: "getvendoraccount",
    // async:false,
    data: data,
    success: function (response) {
      console.log(response); // Log the entire response to check its structure

      // Check if the data object exists and contains 'first_name'
      if (response && response.data) {
        $("#account_name").val(response.data.account_name);
        $("#account_name1").val(response.data.account_id);
        $("#margin_percent").val(response.data.margin_percent);
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

/////////////////////////get product detail////////////
async function getproductdetail() {
  const data = {
    related_to: $("#related_to").val(),
    related_to_id: $("#related_to_id1").val(),
    _csrf: $("#csrfToken").val(),
  };

  try {
    const response = await $.ajax({
      type: "POST",
      url: "getproductdetail",
      data: data,
      dataType: "json",
    });

    console.log(response); // Log the entire response to check its structure

    if (response && response.data) {
      $("#loading-overlay").css('display', 'grid');

      // Initialize currentRow to keep track of the last appended row
      let currentRow = '';

      // Loop through each product in the response
      for (let i = 0; i < response.data.length; i++) {
        const j = i + 1;
        const res = response.data[i];

        // Wait for the row to be added before proceeding with updates
        await addRowBtn("105", "quotes");

        // Find the last row and get its index
        const tbody = $('#productTable' + 105 + ' tbody');
        const lastRow = tbody.find('tr:last');
        const rowIndex = lastRow.index();
       
        // Check if this row is new and not already updated
        if (lastRow.length > 0 && currentRow !== rowIndex) {
          console.log(`Processing row ${j} (Row Index: ${rowIndex})`);

          // Find the product_name input element
          const productNameInput = lastRow.find(`#product_name_${j}`);

          if (productNameInput.length > 0) {
            // Update the input values for the row
            productNameInput.val(res.product_name);
            lastRow.find(`#product_name_${j}1`).val(res.productid);
            lastRow.find(`#category_${j}`).val(res.category);
            lastRow.find(`#hsn_code_${j}`).val(res.hsn_code);
            lastRow.find(`#quantity_${j}`).val(res.quantity_required);
            lastRow.find(`#uom_${j}`).val(res.uom);
            lastRow.find(`#cgst_percent_${j}`).val(res.cgst);
            lastRow.find(`#sgst_percent_${j}`).val(res.sgst);
            lastRow.find(`#igst_percent_${j}`).val(res.igst);
            lastRow.find(`#cgst_amount_${j}`).val(res.cgst_amount);
            lastRow.find(`#sgst_amount_${j}`).val(res.sgst_amount);
            lastRow.find(`#igst_amount_${j}`).val(res.igst_amount);
            lastRow.find(`#cost_price_${j}`).val(res.quoted_price_gst_exclude);
            lastRow.find(`#total_amount_${j}`).val(res.total_quoted_price_exclusive_gst);
            lastRow.find(`#working_condition_${j}`).val(res.asset_condition).trigger("change");

            // Update the currentRow index after appending and updating
            currentRow = rowIndex;
          } else {
            console.error(`Product Name input not found for ID: #product_name_${j}`);
          }
        }
      }
      $("#loading-overlay").css('display', 'none');

      // After all rows are processed, update the total amount after a delay
      // setTimeout(() => {
        setTotalAmount();
      // }, 5000);
    } else {
      console.error("Invalid response format or missing data");
    }
  } catch (error) {
    console.error("Error occurred while fetching product details:", error);
    alert("Error occurred. Please try again.");
  }
}


function setTotalAmount() {
  var basic_cp = 0;
  var total_cgst_amount = 0;
  var total_sgst_amount = 0;
  var total_igst_amount = 0;
  // alert('suffix');
  $("[id^=product_name_]").each(function () {
    var suffix = $(this).attr("id").match(/\d+$/)
      ? $(this).attr("id").match(/\d+$/)[0]
      : "";
    // alert(suffix);
    var total_amount = parseFloat($(`#total_amount_${suffix}`).val()) || 0;
    var cgst_amount = parseFloat($(`#cgst_amount_${suffix}`).val()) || 0;
    var sgst_amount = parseFloat($(`#sgst_amount_${suffix}`).val()) || 0;
    var igst_amount = parseFloat($(`#igst_amount_${suffix}`).val()) || 0;
    basic_cp += total_amount;
    total_cgst_amount += cgst_amount;
    total_sgst_amount += sgst_amount;
    total_igst_amount += igst_amount;
    $("#basic_cp").val(basic_cp.toFixed(2));
    $("#total_cgst_amount").val(total_cgst_amount.toFixed(2));
    $("#total_sgst_amount").val(total_sgst_amount.toFixed(2));
    $("#total_igst_amount").val(total_igst_amount.toFixed(2));
    $("#basic_cp").val(basic_cp.toFixed(2));
  });
  var total_amount = 0;
  total_amount =
    basic_cp + total_cgst_amount + total_sgst_amount + total_igst_amount;
  $("#total_amount").val(total_amount.toFixed(2));
}

////////hide add more button//////////
$(".add-more-records").addClass("tr-hidden");

/////////fetch terms and onditions///////////
function fetchtermscondition() {
  data = {
    moduleid: 13,
    _csrf: $("#csrfToken").val(),
  };

  $.ajax({
    type: "POST",
    url: "fetchtermscondition",
    // async:false,
    data: data,
    success: function (response) {
      console.log(response); // Log the entire response to check its structure

      // Check if the data object exists and contains 'first_name'
      if (response && response.data) {
        $("#terms_and_conditions").val(response.data.content);


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
///////validate for products/////////////
$(document).ready(function () {
  // Trigger validation when the button is clicked
  $(".savebutton").click(function () {
    //alert("Sdfds");
    // Iterate through each product_name input and check if it's empty
    var isValid = true;
    if ($(".product_name").length > 0) {
      $(".product_name").each(function () {

        if ($(this).val() === "") {
          isValid = false;
          $(this).addClass("error"); // Add error class if blank
        } else {
          $(this).removeClass("error"); // Remove error class if filled
        }
      });
    }
    else {
      isValid = false;
    }

    if (isValid) {
      $(".savebutton").prop("disabled", false);
      // If all inputs are valid, you can submit the form or perform some action
      // alert("Form is valid, proceed with submission!");
      // Example: $("#productForm").submit();
    } else {
      $(".savebutton").prop("disabled", true);
      // If any input is invalid, alert the user
      // alert("Products can't be blank");
    }
  });
});