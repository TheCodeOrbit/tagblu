$(document).ready(function () {
  var newURL = window.location.href;

  // get exchangerate
  const modeInput = document.getElementById("mode");
  if (modeInput && modeInput.value === "Create") {
    // initialize stage with draft
    $("#quote_stage").val("1").trigger("change");
   $(".section-send_for_first_approval").addClass("tr-hidden");
   $(".section-send_for_second_approval").addClass("tr-hidden");
   

  }
  var quote_stage = $("#quote_stage").val();
  if(quote_stage == 1)
   $(".section-send_for_second_approval").addClass("tr-hidden");

  //disable auto dd of stage
   const stageSelect = document.getElementById("quote_stage");
      const options = stageSelect.options;

      for (let i = 0; i < options.length; i++) {
          if (options[i].value === "1" || options[i].value === "2" || options[i].value === "3" || options[i].value === "4" || options[i].value === "5") { 
              options[i].disabled = true;
              // options[i].text += " (Disabled due to total invoice and total payment amount are mismatch)";
              //break;
          }
          if(modeInput.value === "Create" && options[i].value === "1")
          {
            options[i].disabled = false;
          }
      }
  
 
});




/////////add a row on create///////////
const mode = document.getElementById("mode");
if (mode && mode.value === "Create") {
    $("#loading-overlay").css('display', 'grid');
  //get products from product detail
  getproductdetail();
  getshipaddress();
  getbilladdress();
      $("#loading-overlay").css('display', 'none');


}

// Create a MutationObserver to detect changes to the input opportunity
var targetNode = document.getElementById("opportunity_name1");
var observer = new MutationObserver(function (mutationsList) {
  for (var mutation of mutationsList) {
    if (mutation.type === "attributes" && mutation.attributeName === "value") {
      console.log("Opportuntiy value changed to:", targetNode.value);
       $("#loading-overlay").css('display', 'grid');
      //get products from product detail
      getproductdetail();
      getshipaddress();
      getbilladdress();
      $("#loading-overlay").css('display', 'none');

    }
  }
});

// Configuration for the observer (observe attribute changes)
var config = { attributes: true };
if(targetNode)
observer.observe(targetNode, config);



// ///////////set default stage//////////////
document.addEventListener("DOMContentLoaded", function () {
  // Check if mode is 'Create'
  const modeInput = document.getElementById("mode");
  if (modeInput && modeInput.value === "Create") {
    
    //set today date
    // Get today's date in YYYY-MM-DD format
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, "0");
    var mm = String(today.getMonth() + 1).padStart(2, "0"); // Months are zero-indexed
    var yyyy = today.getFullYear();

    var todaydate = dd + "-" + mm + "-" + yyyy; // Format the date as YYYY-MM-DD
    // alert('"'+todaydate+'"');

    $("#quote_create_date").val(todaydate);
    ///////////15 days + creation date = expiry date

    setTimeout(() => {
      flatpickr("#quote_create_date", {
        defaultDate: new Date(),
        dateFormat: "d-m-Y",
      });

      //getexpirydate();
    }, 500); // Waits for 600 milliseconds (1/2 seconds)

    // $("#expiry_date").val(expiry_date);

    // getvendoraccount();
  }
});


//////////////get bill to address///////////
async function getbilladdress() {
  const data = {
    deal_name: $("#opportunity_name1").val(),
    _csrf: $("#csrfToken").val(),
  };

  try {
    const response = await $.ajax({
      type: "POST",
      url: "getbilladdress",
      data: data,
      dataType: "json",
    });

    // console.log(response); // Log the entire response to check its structure

    if (response && response.data) {
      $("#loading-overlay").css('display', 'grid');

    $(`#warehouse_loc_business_entity1`).val(response.data.warehouse_loc_business_entity);
    $(`#warehouse_loc_business_entity`).val(response.data.warehouse_name);
    $(`#bill_from_location1`).val(response.data.bill_from_location);
    $(`#bill_from_location`).val(response.data.bill_from_location_name);
    $(`#bill_from_address`).val(response.data.bill_from_address);
    $(`#bill_from_state`).val(response.data.bill_from_state);
    $(`#bill_from_state_code`).val(response.data.bill_from_state_code);
    $(`#bill_to_location`).val(response.data.bill_location_name);
    $(`#bill_to_location1`).val(response.data.bill_location);
    $(`#bill_to_address`).val(response.data.bill_address);
    $(`#bill_to_state`).val(response.data.bill_state);
    $(`#bill_to_state_code`).val(response.data.bill_state_code);
    $(`#bill_to_gst`).val(response.data.bill_gstin_no);
    $(`#bill_to_pan`).val(response.data.pan_number);

    $(`#account_name1`).val(response.data.vendor_account_name);
    $(`#account_name`).val(response.data.vendor);
    $(`#region`).val(response.data.zone_region).trigger("change");
    $(`#team_name`).val(response.data.team_name).trigger("change");
    $(`#requester_name1`).val(response.data.requester_customer_name);
    $(`#requester_name`).val(response.data.contact);
    $(`#gross_profit`).val(response.data.gross_profit);
    // $(`#margin`).val(parseFloat(response.data.margin_percentage).toFixed(2));
    $(`#category`).val(response.data.product_category);
    $(`#payment_terms`).val(response.data.payment_terms);
    $(`#deal_name`).val(response.data.deal_name);
    setmargin();

    
      $("#loading-overlay").css('display', 'none');

      // After all rows are processed, update the total amount after a delay
      // setTimeout(() => {
       // setTotalAmount();
      // }, 5000);
    } else {
      console.error("Invalid response format or missing data");
    }
  } catch (error) {
    console.error("Error occurred while fetching product details:", error);
    alert("Error occurred. Please try again.");
  }
}
//////////////get ship address///////////
async function getshipaddress() {
  const data = {
    deal_name: $("#opportunity_name1").val(),
    _csrf: $("#csrfToken").val(),
  };

  try {
    const response = await $.ajax({
      type: "POST",
      url: "getshipaddress",
      data: data,
      dataType: "json",
    });

    // console.log(response); // Log the entire response to check its structure

    if (response && response.data) {
      $("#loading-overlay").css('display', 'grid');

      // Initialize currentRow to keep track of the last appended row
      let currentRow = '';

      // Loop through each product in the response
      for (let i = 0; i < response.data.length; i++) {
        const j = i + 1;
        const res = response.data[i];

        // Wait for the row to be added before proceeding with updates
        await addRowBtn("2692", "quotesdit");

        // Find the last row and get its index
        const tbody = $('#productTable' + 2692 + ' tbody');
        const lastRow = tbody.find('tr:last');
        const rowIndex = lastRow.index();
       
        // Check if this row is new and not already updated
        if (lastRow.length > 0 && currentRow !== rowIndex) {
          console.log(`Processing row ${j} (Row Index: ${rowIndex})`);

          // Find the product_name input element
          const productNameInput = lastRow.find(`#ship_to_location_${j}`);

          if (productNameInput.length > 0) {
            // Update the input values for the row
            
            lastRow.find(`#ship_to_location_${j}1`).val(res.ship_to_location);
            lastRow.find(`#ship_to_location_${j}`).val(res.vendor_loc_name);
            lastRow.find(`#ship_address_${j}`).val(res.ship_to_address);
            lastRow.find(`#ship_state_${j}`).val(res.ship_to_state);
            lastRow.find(`#ship_state_code_${j}`).val(res.ship_state_code);
            lastRow.find(`#ship_legal_name_${j}`).val(res.ship_legal_name);
            lastRow.find(`#ship_gst_${j}`).val(res.gstin_no_uin);
            

            // Update the currentRow index after appending and updating
            currentRow = rowIndex;
          } else {
            // console.error(`Ship Location input not found for ID: #ship_to_location_${j}`);
            console.error("error fetching shiping adresses");
          }
        }
      }
      $("#loading-overlay").css('display', 'none');

      // After all rows are processed, update the total amount after a delay
      // setTimeout(() => {
       // setTotalAmount();
      // }, 5000);
    } else {
      console.error("Invalid response format or missing data");
    }
  } catch (error) {
    console.error("Error occurred while fetching product details:", error);
    alert("Error occurred. Please try again.");
  }
}

/////////////////////////get product detail////////////
async function getproductdetail() {
  const data = {
    deal_name: $("#opportunity_name1").val(),
    _csrf: $("#csrfToken").val(),
  };

  try {
    const response = await $.ajax({
      type: "POST",
      url: "getproductdetail",
      data: data,
      dataType: "json",
    });

    // console.log(response); // Log the entire response to check its structure

    if (response && response.data) {
      $("#loading-overlay").css('display', 'grid');

      // Initialize currentRow to keep track of the last appended row
      let currentRow = '';
      var subtotal=0;
      var totalgst=0;
      var total_cgst=0;
      var total_sgst=0;
      var total_igst=0;
      var total=0;
      // Loop through each product in the response
      for (let i = 0; i < response.data.length; i++) {
        const j = i + 1;
        const res = response.data[i];

        // Wait for the row to be added before proceeding with updates
        await addRowBtn("2694", "quotesdit");

        // Find the last row and get its index
        const tbody = $('#productTable' + 2694 + ' tbody');
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
            lastRow.find(`#product_name_${j}1`).val(res.product_name);
            lastRow.find(`#product_name_${j}`).val(res.prod_name);
            // lastRow.find(`#category_${j}`).val(res.category);
            lastRow.find(`#product_description_${j}`).val(res.product_description);
            lastRow.find(`#hsn_code_${j}`).val(res.hsn_code);
            lastRow.find(`#qty_${j}`).val(res.quantity);
            // lastRow.find(`#uom_${j}`).val(res.uom);
            lastRow.find(`#cgst_per_${j}`).val(res.cgst);
            lastRow.find(`#sgst_per_${j}`).val(res.sgst);
            lastRow.find(`#igst_per_${j}`).val(res.igst);
            // lastRow.find(`#cgst_amount_${j}`).val(res.cgst_amount);
            // lastRow.find(`#sgst_amount_${j}`).val(res.sgst_amount);
            // lastRow.find(`#igst_amount_${j}`).val(res.igst_amount);
            lastRow.find(`#basic_price_${j}`).val(res.sales_price);
            var amnt = res.sales_price * res.quantity;
            lastRow.find(`#amount_${j}`).val(amnt);

            var cgst_amt = amnt*res.cgst/100;
            total_cgst += cgst_amt;
            
            var sgst_amt = amnt*res.sgst/100;
            total_sgst += sgst_amt;

            var igst_amt = amnt*res.igst/100;
            total_igst += igst_amt;

            var totalgst = cgst_amt+sgst_amt+igst_amt;
            subtotal += amnt;
            // alert(subtotal);
            // alert(total_cgst + total_sgst + total_igst);
            // total += subtotal + total_cgst + total_sgst + total_igst;
            // alert(total);

            // Update the currentRow index after appending and updating
            currentRow = rowIndex;
          } else {
            console.error(`Product Name input not found for ID: #product_name_${j}`);
          }
        }
      }
      var totalgst = total_cgst + total_sgst + total_igst;
      total = subtotal + totalgst;
      var total_price_words = numberToWords(total);
      $("#cgst_amount").val(total_cgst);
      $("#sgst_amount").val(total_sgst);
      $("#igst_amount").val(total_igst);
      $("#sub_total").val(subtotal);
      $("#grand_total").val(total);
      $("#amount_in_words").val(total_price_words);
      setmargin();

      
      
      $("#loading-overlay").css('display', 'none');

      // After all rows are processed, update the total amount after a delay
      // setTimeout(() => {
        //setTotalAmount();
      // }, 5000);
    } else {
      console.error("Invalid response format or missing data");
    }
  } catch (error) {
    console.error("Error occurred while fetching product details:", error);
    alert("Error occurred. Please try again.");
  }
}



////////hide add more button//////////
$(".add-more-records").addClass("tr-hidden");


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
// Function to convert numbers to words
function numberToWords(num) {
  const a = [
    "",
    "One",
    "Two",
    "Three",
    "Four",
    "Five",
    "Six",
    "Seven",
    "Eight",
    "Nine",
    "Ten",
    "Eleven",
    "Twelve",
    "Thirteen",
    "Fourteen",
    "Fifteen",
    "Sixteen",
    "Seventeen",
    "Eighteen",
    "Nineteen",
  ];
  const b = [
    "",
    "",
    "Twenty",
    "Thirty",
    "Forty",
    "Fifty",
    "Sixty",
    "Seventy",
    "Eighty",
    "Ninety",
  ];
  const c = ["Hundred", "Thousand", "Lakh", "Crore"];

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
    url: "approvequotesdit",
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
      url: "approvequotesdit",
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
///////////////check submit_for_approval is cheked then disble it/////////
var submit_approval = document.getElementById("send_for_approval");
if (submit_approval) {
  if (submit_approval.checked) {
    // Checkbox is checked
    $("#send_for_approval").prop("disabled", true);
  } else {
    $("#send_for_approval").prop("disabled", false);
  }
}
///new formula for margin added on  13 sept 2025 by deepika
function setmargin() {
    var sub_total = parseFloat($("#sub_total").val()) || 0;
    var gross_profit = parseFloat($("#gross_profit").val()) || 0;
    
    if(sub_total > 0 && gross_profit >= 0) {
        var margin = (gross_profit / sub_total) * 100;
        console.log(margin+',sub total'+sub_total+', gross='+gross_profit);
        $(`#margin`).val(margin.toFixed(2));  // Optional: To show margin with 2 decimal places
    }
}
