$(document).ready(function () {
  var newURL = window.location.href;
  var newURL = window.location.href;
  var module = "grn";
  var str = newURL.split(module);
  console.log("str" + str[0]);
  // var slicestr=newURL.substring(0,str);
  editusrl = str[0] + "grn/list";
  console.log("url" + editusrl);

  let today = new Date().toISOString().split("T")[0];
  $('.c-faqs__item-question:first').trigger("click");
  function calculate_gross_total() {
    var gross_total = 0;
    $(".total").each(function () {
      var value = parseFloat($(this).val());
      if (!isNaN(value) && value >= 0) {
        gross_total += value;
      }
    });
    if (gross_total >= 0) {
      $("#total_amount").val(gross_total);
    } else {
      $("#total_amount").val(0);
    }
    getTCSPercentageAndTCSAmount();
  }
  function calculate_row_base_cp(row) {
    var cost = parseFloat(row.find('.cost_price').val()) || 0;
    var qty = parseFloat(row.find('.quantity').val()) || 0;
    var cgst = parseFloat(row.find('.cgst').val()) || 0;
    var sgst = parseFloat(row.find('.sgst').val()) || 0;
    var igst = parseFloat(row.find('.igst').val()) || 0;
    var totalPrice = cost * qty;
    var totalRowPrice = totalPrice;
    if (cgst) {
      totalRowPrice = totalRowPrice + (cgst * totalPrice) / 100
    }
    if (sgst) {
      totalRowPrice = totalRowPrice + (sgst * totalPrice) / 100
    }
    if (igst) {
      totalRowPrice = totalRowPrice + (igst * totalPrice) / 100
    }

    row.find('.base_cp').val(totalPrice.toFixed(2));
    row.find('.total').val(totalRowPrice.toFixed(2));
    calculate_gross_total()
  }
  $(document).on("change", ".quantity,.cost_price,.cgst,.sgst,.igst", function () {
    var currentRow = $(this).closest('tr');
    calculate_row_base_cp(currentRow);
  })
  $(document).on("click", ".remove-row-btn", function () {
    calculate_gross_total()
  })
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
      url: "approvepo",
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
  $(document).on("click", "#modifysubmit", function () {
    //alert("dfhfdhd");
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
        url: "approvepo",
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
  // get exchangerate
  // Initialize Select2 for all dropdowns
  $('#currency').select2();
  // get exchangerate
  const modeInput = document.getElementById("mode");
  if (modeInput && modeInput.value === "Create") {
    setTimeout(() => {
      getexpirydate();
    }, 500); // Waits for 600 milliseconds (1/2 seconds)
    // fetchtermscondition();

    //get quoteid
    var quoteid = $("#quote1").val();
    // alert(quoteid);
    if (quoteid) {
      getallquotesproducts(quoteid);
      getbillingdetail(quoteid);
      getSourcingAccount(quoteid);
      getcontactandtype(quoteid);
    }

    $('#stage').val("1").trigger("change");

    // initialize currency with INr
    $('#currency').val("1").trigger("change");
    data = { currency: 1, _csrf: $('#csrfToken').val() };

    //end ddepika
    getexchangerate(data);
  }
  // Listen for the change event on select2
  $('#currency').on('select2:select', function (e) {

    var selected_currency = e.target.value;  // Get the selected value
    if (!selected_currency) {
      $("#exchange_rate").val("")
    } else {
      var data = {
        currency: selected_currency,
        _csrf: $('#csrfToken').val()
      };
      getexchangerate(data);
    }

  });

  function getexchangerate(data) {
    $.ajax({
      type: 'POST',
      url: "getexchangerate",
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

  //get warehouse
  // Create a MutationObserver to detect changes to the input vendor account
  // var targetNode = document.getElementById('ship_warehouse_name1');
  // var observer = new MutationObserver(function (mutationsList) {
  //     for (var mutation of mutationsList) {
  //     if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
  //         getwarehouse();
  //         console.log('business_entity value changed to:', targetNode.value);
  //     }
  //     }
  // });
  // // Configuration for the observer (observe attribute changes)
  // var config = { attributes: true };
  // observer.observe(targetNode, config);
  // function getwarehouse() {
  //     data = { business_entity: $("#ship_warehouse_name1").val(), _csrf: $('#csrfToken').val() };
  //     $.ajax({
  //     type: 'POST',
  //     url: "getwarehouse",
  //     data: data,
  //     success: function (response) {
  //         if (response && response.data) {
  //             $("#warehouse_address").val(response.data.address);
  //             $("#warehouse_state").val(response.data.state);
  //             $("#warehouse_state_code").val(response.data.statecode);
  //             $("#warehouse_gstin_no").val(response.data.gstn);
  //             $("#warehouse_pincode").val(response.data.pincode);
  //         } else {
  //             console.log("Invalid response format or missing data");
  //         }

  //     },
  //     error: function (data) { // if error occured
  //         alert('Error occured.please try again');
  //     },
  //     dataType: 'json'
  //     });

  // }

  // Function to attach the MutationObserver to a specific input field
  function getproductdetail(targetId) {
    data = {
      product_id: $("#" + targetId).val(),
      _csrf: $('#csrfToken').val()
    };
    $.ajax({
      type: 'POST',
      url: "getproduct",
      data: data,
      success: function (response) {
        if (response && response.data) {
          var currentRow = $("#" + targetId).closest('tr');
          currentRow.find(".product_description").val(response.data.product_description);
          currentRow.find(".hsn_code").val(response.data.hsn_code);
          currentRow.find(".category").val(response.data.prod_catagory_value);
        } else {
          console.log("Invalid response format or missing data");
        }
      },
      error: function (data) { // if error occured

        alert('Error occured.please try again');
      },
      dataType: 'json'
    });
  }
  function attachObserver(targetNode) {
    var observer = new MutationObserver(function (mutationsList) {
      for (var mutation of mutationsList) {
        if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
          // getproductdetail(targetNode.id);
          console.log('product_name value changed to:', targetNode.value);
        }
      }
    });

    // Configuration for the observer (observe attribute changes)
    var config = { attributes: true };
    observer.observe(targetNode, config);
  }
  // Observe the document body for added nodes
  var mutationObserver = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
      if (mutation.type === 'childList') {
        mutation.addedNodes.forEach(function (node) {
          if (node.nodeType === 1 && node.matches('input[id^="product_name_"]')) {
            // Attach observer to new dynamically added input elements
            attachObserver(node);
          }
          // Also check for any dynamically added nodes that contain the matching input
          if (node.nodeType === 1) {
            node.querySelectorAll('input[id^="product_name_"]').forEach(function (inputNode) {
              attachObserver(inputNode);
            });
          }
        });
      }
    }
  });

  // Configuration for observing child node additions
  var configm = { childList: true, subtree: true };

  // Start observing the document body
  mutationObserver.observe(document.body, configm);

  // Optional: Attach the observer to existing dynamic elements (if they are already on the page when script runs)
  document.querySelectorAll('input[id^="product_name_"]').forEach(function (inputNode) {
    attachObserver(inputNode);
  });
});

//////////////////// on the class end validation code zitendra /////////////////////////

////////////////////end validation code zitendra /////////////////////////

document.querySelectorAll(".accordion-toggle").forEach(button => {
  button.addEventListener("click", () => {
    const content = button.closest(".accordion-item").querySelector(".accordion-content");
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
document.querySelectorAll(".tab").forEach(tab => {
  tab.addEventListener("click", function () {
    // Remove active class from all tabs and contents
    document.querySelectorAll(".tab").forEach(t => t.classList.remove("active"));
    document.querySelectorAll(".tab-content-detail-view").forEach(content => content.classList.remove("active"));
    // Add active class to clicked tab and corresponding content
    this.classList.add("active");
    const tabId = this.getAttribute("data-tab");
    document.getElementById(tabId).classList.add("active");
  });
});


/////////////create mutation for quotes/////////////////
// Create a MutationObserver to detect changes to the input vendor account
var targetNode = document.getElementById("quote1");
var observer = new MutationObserver(function (mutationsList) {
  for (var mutation of mutationsList) {
    if (mutation.type === "attributes" && mutation.attributeName === "value") {
      console.log("quote1 value changed to:", targetNode.value);

      getallquotesproducts(targetNode.value);
      getbillingdetail(targetNode.value);
      getcontactandtype(targetNode.value);
    }
  }
});

// Configuration for the observer (observe attribute changes)
var config = { attributes: true };
observer.observe(targetNode, config);

/////////get products from quotes/////////
// function getallquotesproducts(quotes_id) {
//   data = {
//     quotes_id: quotes_id,
//     _csrf: $("#csrfToken").val(),
//   };

//   $.ajax({
//     type: "POST",
//     url: "getallquotesproducts",
//     // async:false,
//     data: data,
//     success: function (response) {
//       console.log(response); // Log the entire response to check its structure

//       // Check if the data object exists and contains 'first_name'
//       if (response && response.data) {
//         // $("#account_name").val(response.data.account_name);
//         // $("#account_name1").val(response.data.account_id);
//         // $("#margin_percent").val(response.data.margin_percent);
//         var j = 0;
//         for (var i = 0; i < response.data.length; i++) {
//           j = i + 1;
//           var res = response.data[i];
//           //alert(res.product_name);

//           addRowBtn("80", "purchaseorder");
//           setTimeout(() => {
//             $("#product_name_" + j).val(res.product_name);
//             $("#product_name_" + j + "1").val(res.products_id);
//             // alert($("#product_name_" + j + "1").val());

//             $("#product_description_" + j).val(res.product_description);
//             $("#category_" + j).val(res.category);
//             $("#hsn_code_" + j).val(res.hsn_code);
//             $("#quantity_" + j).val(res.quantity);
//             $("#uom_" + j).val(res.uom);
//             $("#cgst_" + j).val(res.cgst_percent);
//             $("#sgst_" + j).val(res.sgst_percent);
//             $("#igst_" + j).val(res.igst_percent);
//             $("#cgst_amount_" + j).val(res.cgst_amount);
//             $("#sgst_amount_" + j).val(res.sgst_amount);
//             $("#igst_amount_" + j).val(res.igst_amount);
//             $("#cost_price_" + j).val(res.cost_price);
//             $("#total_" + j).val(res.total_amount);
//             $("#asset_condition_" + j).val(res.asset_condition).trigger("change");

//             // var total_cp = res.quantity_required * res.quoted_price_inclusive_gst;
//             // $(`#basic_cp_${j}`).val(total_cp.toFixed(2));
//             // var total_amount = total_cp+res.cgst_amount+res.sgst_amount+res.igst_amount;
//             // $(`#total_amount_${j}`).val(total_amount.toFixed(2));

//             //alert($("#product_name_"+j).val());
//           }, 500); // Waits for 600 milliseconds (1/2 seconds)
//         }
//         setTimeout(() => {
//           //setTotalAmount();
//         }, 1000);
//       } else {
//         console.log("Invalid response format or missing data");
//       }
//     },
//     error: function (data) {
//       // if error occured

//       alert("Error occured.please try again");
//     },
//     dataType: "json",
//   });
// }
async function getallquotesproducts(quotes_id) {
  const data = {
    quotes_id: quotes_id,
    _csrf: $("#csrfToken").val(),
  };

  try {
    const response = await $.ajax({
      type: "POST",
      url: "getallquotesproducts",
      data: data,
      dataType: "json",
    });

    console.log(response); // Log the entire response to check its structure

    if (response && response.data) {
      $("#loading-overlay").css('display', 'grid');

      let currentRow = ''; // Initialize currentRow to track the last updated row
      //empty old item
      $('#productTable80 tbody').html('');
      for (let i = 0; i < response.data.length; i++) {
        const j = i + 1; // Increment j to match the row
        const res = response.data[i];
       
        // Wait for the row to be added before proceeding with updates
        await addRowBtn("80", "purchaseorder");
       

        // Find the last row and get its index
        const tbody = $('#productTable80 tbody');
        const lastRow = tbody.find('tr:last');
        const rowIndex = lastRow.index();

        // Check if the row exists and hasn't been updated yet
        if (lastRow.length > 0 && currentRow !== rowIndex) {
          console.log(`Processing row ${j} (Row Index: ${rowIndex})`);

          // Update input fields in the last row
          lastRow.find(`#product_name_${j}`).val(res.product_name);
          lastRow.find(`#product_name_${j}1`).val(res.products_id);
          lastRow.find(`#product_description_${j}`).val(res.product_description);
          lastRow.find(`#category_${j}`).val(res.category);
          lastRow.find(`#hsn_code_${j}`).val(res.hsn_code);
          lastRow.find(`#quantity_${j}`).val(res.quantity);
          lastRow.find(`#uom_${j}`).val(res.uom);
          lastRow.find(`#cgst_${j}`).val(res.cgst_percent);
          lastRow.find(`#sgst_${j}`).val(res.sgst_percent);
          lastRow.find(`#igst_${j}`).val(res.igst_percent);
          lastRow.find(`#cgst_amount_${j}`).val(res.cgst_amount);
          lastRow.find(`#sgst_amount_${j}`).val(res.sgst_amount);
          lastRow.find(`#igst_amount_${j}`).val(res.igst_amount);
          lastRow.find(`#cost_price_${j}`).val(res.cost_price);
          lastRow.find(`#total_${j}`).val(res.total_amount);
          lastRow.find(`#asset_condition_${j}`).val(res.asset_condition).trigger("change");
          $(".remove-row-btn").each(function() {
                  $(this).closest("td").remove();
              });
          // Update currentRow to ensure we don't update the same row again
          currentRow = rowIndex;
        }
      }

      $("#loading-overlay").css('display', 'none');
     
    } else {
      console.error("Invalid response format or missing data");
    }
  } catch (error) {
    console.error("Error occurred while fetching all quotes products:", error);
    alert("Error occurred. Please try again.");
  }
}

////////hide add more button//////////
$(".add-more-records").addClass("tr-hidden");
////////////get billing detail//////////
function getbillingdetail(quotes_id) {
  data = {
    quotes_id: quotes_id,
    _csrf: $("#csrfToken").val(),
  };

  $.ajax({
    type: "POST",
    url: "getbillingdetail",
    // async:false,
    data: data,
    success: function (response) {
      console.log(response); // Log the entire response to check its structure

      // Check if the data object exists and contains 'first_name'
      if (response && response.data) {
        $("#payment_terms").val(response.data.payment_terms).trigger("change");

        $("#bill_to_name").val(response.data.vendor_loc_name);
        $("#bill_to_name1").val(response.data.bill_name);
        $("#bill_legal_name").val(response.data.bill_legal_name);
        $("#bill_address").val(response.data.bill_address);
        $("#billing_city").val(response.data.bill_city);
        $("#billing_state").val(response.data.bill_state);
        $("#billing_state_code").val(response.data.bill_state_code);
        $("#bill_gstin_no").val(response.data.bill_gstin_no_uin);
        $("#bill_pan_no").val(response.data.bill_pan_no);
        $("#bill_to_pincode").val(response.data.bill_pincode);

        $("#warehouse_business_entity").val(response.data.warehouse_name);
        $("#warehouse_business_entity1").val(response.data.business_entity);
        $("#ship_warehouse_name").val(response.data.warehouse_name);
        $("#warehouse_address").val(response.data.warehouse_address);
        $("#warehouse_city").val(response.data.warehouse_city);
        $("#warehouse_state").val(response.data.warehouse_state);
        $("#warehouse_state_code").val(response.data.warehouse_state_code);
        $("#warehouse_pincode").val(response.data.warehouse_pincode);
        $("#warehouse_gstin_no").val(response.data.warehouse_gstin_no);


        $("#po_bill_location_name").val(response.data.qu_bill_warehouse_name);
        $("#po_bill_location_name1").val(response.data.qu_bill_location_name);
        $("#po_bill_address").val(response.data.qu_bill_address);
        $("#po_bill_state").val(response.data.qu_bill_state);
        $("#po_bill_pin_code").val(response.data.qu_bill_pin_code);
        $("#po_bill_warehouse_name").val(response.data.qu_bill_warehouse_name);
        $("#po_bill_city").val(response.data.qu_bill_city);
        $("#po_bill_state_code").val(response.data.qu_bill_state_code);
        $("#po_bill_gstin_no").val(response.data.qu_bill_gstin_no);

        $("#basic_cp").val(response.data.basic_cp);
        $("#total_cgst_amount").val(response.data.total_cgst_amount);
        $("#total_sgst_amount").val(response.data.total_sgst_amount);
        $("#total_igst_amount").val(response.data.total_igst_amount);
        $("#total_amount").val(response.data.total_amount);
        $("#terms_conditions").val(response.data.terms_and_conditions);



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
 // code added to get contact name and type from quote as per client changes on date 20-06-25 by ptpatel\
function getcontactandtype(quotes_id){
  data = {
    quotes_id: quotes_id,
    _csrf: $("#csrfToken").val(),
  };

  $.ajax({
    type: "POST",
    url: "getcontactandtype",
    // async:false,
    data: data,
    success: function (response) {
      console.log("response getcontactandtype"+response); // Log the entire response to check its structure

      // Check if the data object exists and contains 'first_name'
      if (response && response.data) {

        $("#contact_name1").val(response.data.contact_name1);
        $("#contact_name").val(response.data.contact_name);
        //change as per client change point 19 and 104  on date 21-06-25
        let types = response.data.type.split(",");
        $("#type").val(types).trigger("change");
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

        // code end to get contact name and type from quote as per client changes on date 20-06-25 by ptpatel
////////expiry date/////////////
function getexpirydate() {
  const selectedDate = new Date();

  if (!isNaN(selectedDate.getTime())) {
    // Add 15 days
    selectedDate.setDate(selectedDate.getDate() + 15);

    // Format the new date as dd-mm-yyyy for display
    const newDay = ("0" + selectedDate.getDate()).slice(-2);
    const newMonth = ("0" + (selectedDate.getMonth() + 1)).slice(-2); // Months are 0-based
    const newYear = selectedDate.getFullYear();
    const formattedDate = `${newDay}-${newMonth}-${newYear}`;

    // Update po_expiry_date input
    const poExpiryDateInput = $("#po_expiry_date");

    // Ensure the input exists
    if (poExpiryDateInput.length > 0) {
      poExpiryDateInput.val(formattedDate);

      // Initialize Flatpickr only once
      if (poExpiryDateInput.data("flatpickr")) {
        // Update Flatpickr date if it's already initialized
        poExpiryDateInput.data("flatpickr").setDate(formattedDate, true);
      } else {
        // Initialize Flatpickr if not already initialized
        poExpiryDateInput.flatpickr({
          dateFormat: "d-m-Y",
          defaultDate: formattedDate,
          allowInput: false,
          readOnly: true,
        });
      }
    }
  }
}

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
        $("#terms_conditions").val(response.data.content);


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

///////////////get account & sourcing deal if quotes is in relation////////
function getSourcingAccount(quotes_id) {
  data = {
    quotes_id: quotes_id,
    _csrf: $("#csrfToken").val(),
  };
  $.ajax({
    type: "POST",
    url: "getsourcingaccount",
    // async:false,
    data: data,
    success: function (response) {
      console.log(response); // Log the entire response to check its structure

      // Check if the data object exists and contains 'first_name'
      if (response && response.data) {
        // $("#terms_conditions").val(response.data.content);
        $("#vendor_name1").val(response.data.vendoraccid);
        $("#vendor_name").val(response.data.acc_name);
        $("#opportunity_name1").val(response.data.sourcingdeal_id);
        $("#opportunity_name").val(response.data.sourcingdeal_no);

        let vendorInput = document.getElementById("vendor_name1");
        let vendorInput2 = document.getElementById("vendor_name");

        if (vendorInput) {
          // Find the parent wrapper
          let wrapper = vendorInput.closest(".vendor-input-wrapper");

          if (wrapper) {
            // Remove all SVGs with class "icon-left" and "icon-right"
            wrapper.querySelectorAll(".icon-left, .icon-right").forEach(svg => svg.remove());
          }
        }
        // Add readonly attribute to vendor_name
        if (vendorInput2) {
          vendorInput2.setAttribute("readonly", "readonly");
        }

        let opportunity_name1 = document.getElementById("opportunity_name1");
        let opportunity_name2 = document.getElementById("opportunity_name");

        if (opportunity_name1) {
          // Find the parent wrapper
          let wrapper = opportunity_name1.closest(".vendor-input-wrapper");

          if (wrapper) {
            // Remove all SVGs with class "icon-left" and "icon-right"
            wrapper.querySelectorAll(".icon-left, .icon-right").forEach(svg => svg.remove());
          }
        }
        // Add readonly attribute to vendor_name
        if (opportunity_name2) {
          opportunity_name2.setAttribute("readonly", "readonly");
        }

        getTCSPercentageAndTCSAmount();

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
  // Trigger validation when the save button is clicked
  $(".savebutton").click(function (event) {
    // Prevent the default form submission action
    event.preventDefault();
// alert(event);
    var isValid = true;

    // Check if product_name elements exist
    if ($(".product_name").length > 0) {
      $(".product_name").each(function () {
        // Check if the product_name field is empty
        if ($(this).val() === "") {
          isValid = false;
          $(this).addClass("error"); // Add error class if blank
        } else {
          $(this).removeClass("error"); // Remove error class if filled
        }
      });
    } else {
      isValid = false; // If no product_name fields exist, set isValid to false
    }

    if (isValid) {
      // Proceed with form submission if valid
      //alert("Form is valid, submitting...");
      
    } else {
      // If validation fails, show an alert and do not submit
      //alert("Products can't be blank");
    }
  });
});

///////////////check submit_for_approval is cheked then disble it added on 20 june 2025/////////
var submit_approval = document.getElementById("submit_approval");
if (submit_approval) {
  if (submit_approval.checked) {
    // Checkbox is checked
    $("#submit_approval").prop("disabled", true);
  } else {
    $("#submit_approval").prop("disabled", false);
  }
}

//code added by ptpatel on date 09-09-2025 to get margin

var targetNodeon = document.getElementById('opportunity_name1');
  var observeron = new MutationObserver(function (mutationsListon) {
      for (var mutationon of mutationsListon) {
      if (mutationon.type === 'attributes' && mutationon.attributeName === 'value') {
          getmarginofsourcingdeal(targetNodeon.value);
          getsourcingdealname(targetNodeon.value);
          getTCSPercentageAndTCSAmount();
          console.log('sourcing deal value changed to:', targetNodeon.value);
      }
      }
  });
  // Configuration for the observer (observe attribute changes)
  var configon = { attributes: true };
  observeron.observe(targetNodeon, configon);

  function getmarginofsourcingdeal(sourcingdealId)
  {
    data = {
    sourcingdealId: sourcingdealId,
    _csrf: $("#csrfToken").val(),
  };

  $.ajax({
    type: "POST",
    url: "getmargin",
    // async:false,
    data: data,
    success: function (response) {
      console.log("response getmargin"+response); // Log the entire response to check its structure

      // Check if the data object exists and contains 'first_name'
      if (response && response.data) {

        $("#margin").val(response.data.margin);
        $("#margin_percentage").val(response.data.margin_percentage);
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
//end code added by ptpatel to get margin

//code addeded for TCS change in total amount block
  function getTCSPercentageAndTCSAmount() {
    // let related_to = $("#related_to").val();
    let sourcing_deal_id= $("#opportunity_name1").val();
    data = {
      sourcing_deal_id: sourcing_deal_id,
      _csrf: $("#csrfToken").val(),
    };

    // if(related_to == 51){
      $.ajax({
        type: "POST",
        url: "gettcspercentageandtcsamount",
        // async:false,
        data: data,
        success: function (response) {
          console.log(response); // Log the entire response to check its structure

          // Check if the data object exists and contains 'first_name'
          if (response && response.data) {
            let tcs_percentage = parseFloat(response.data.tcs_percentage) || 0;
            let tcs_amount = parseFloat(response.data.tcs_amount) || 0;
            let total_amount = parseFloat($("#total_amount").val()) || 0;
            let grand_total =  response.data.final_quoted_amount_incl_gst || 0;
            let round_off =  parseFloat(response.data.round_off) || 0;
            $("#tcs_percentage").val(tcs_percentage);
            $("#tcs_amount").val(tcs_amount);
            // added round off on 14 oct 2025
            $("#round_off").val(round_off);
            $("#grand_total").val(grand_total);
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
    // }
    // else
    // {
    //   console.log("Can't get TCS Percentage and TCS Amount.");
    // }
  }
  //code ended for TCS change in total amount block

  function getsourcingdealname(sourcingdealId)
  {
      data = {
        sourcingdealId: sourcingdealId,
        _csrf: $("#csrfToken").val(),
      };

      $.ajax({
        type: "POST",
        url: "getsourcingdealname",
        // async:false,
        data: data,
        success: function (response) {
          console.log("response getmargin"+response); // Log the entire response to check its structure

          // Check if the data object exists and contains 'first_name'
          if (response && response.data) {

            $("#sourcing_deal_name").val(response.data.sourcing_deal_name);
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
$(document).ready(function() {
  var $stage = $('#stage');
  function handleStageBehavior() {
  var val = $stage.val();
  
  if (val === "3") {
    $stage.find('option').each(function() {
      var optionVal = $(this).val();
      if (optionVal === "5" || optionVal === "3") {
        $(this).prop('disabled', false);
      } else {
        $(this).prop('disabled', true);
      }
    });

    $stage.prop('disabled', false);
    $stage.removeAttr('readonly');
    $stage.removeClass('readonly-dd');

    $stage.off('select2:selecting').on('select2:selecting', function(e) {
      var selectingVal = e.params.args.data.id;
      if (selectingVal !== "3" && selectingVal !== "5") {
        e.preventDefault();
      }
    });

  } else {
    resetOptions();
    $stage.prop('disabled', true);
    $stage.attr('readonly', 'readonly');
    $stage.off('select2:selecting');
  }

  $stage.trigger('change.select2');
}
handleStageBehavior();
});
