$(document).ready(function () {

  //hide add row btn
  $(".add-more-records").hide();
  var stageSelect = document.getElementById("status");

  if (stageSelect) {
  // By default set to Draft if in Create mode
  const modeInput = document.getElementById("mode");
  if (modeInput && modeInput.value === "Create") {
    $("#status").val("1").trigger("change");
    
    setTimeout(() => {
      flatpickr("#delivery_challan_date", {
        defaultDate: new Date(),
        dateFormat: "d-m-Y"
      });
    }, 500);
  }
// Get the updated stage value
  const stage = parseInt($("#status").val() || "0");

  // Disable all options except current stage and conditional "8"
  const options = stageSelect.options;
  for (let i = 0; i < options.length; i++) {
    const value = options[i].value;

    // Always enable current stage
    if (parseInt(value) === stage) {
      options[i].disabled = false;
    } 
    else if (value === "4" ) {
      options[i].disabled = false;
    }
    // Disable everything else
    else {
      options[i].disabled = true;
    }
  }
}

///invoice 

$("#invoice_created").val("1").trigger("change");
$("#invoice_created").prop("disabled",true);
$("#invoice_date").prop("disabled",true);
$("#invoice_number").prop("disabled",true);


$(document).on("change", "#delivery_challan_type", function () {
        var delivery_challan_type = $(this).val();
        toggleBlocks(delivery_challan_type);
    });

function toggleBlocks(dc_type)
{
        let hide_section = ["foc_number","pod_image","delivery_date","return_condition","returned_date"];
        // if(dc_type == 1)
        // {
        //     hide_section.forEach(function(key) {
        //       let el = document.querySelector('.section-' + key);
        //       if (el) {
        //           el.style.display = 'none';
        //       }
        //   });
        // }
        // else 
        if(dc_type == 4){
          $(".section-foc_number").style.display = "block";
          $(".section-pod_image").style.display = "block";
        }
        else if(dc_type == 3){
          $(".section-return_condition").style.display = "block";
          $(".section-returned_date").style.display = "block";
        }
        else
        {
            hide_section.forEach(function(key) {
              let el = document.querySelector('.section-' + key);
              if (el) {
                  el.style.display = 'none';
              }
          });
        }
}
//  const selectedDate = new Date();

// if (!isNaN(selectedDate.getTime())) {
//   selectedDate.setDate(selectedDate.getDate()); // add 15 days

//   const newDay = String(selectedDate.getDate()).padStart(2, '0');
//   const newMonth = String(selectedDate.getMonth() + 1).padStart(2, '0');
//   const newYear = selectedDate.getFullYear();

//   const formattedDate = `${newDay}-${newMonth}-${newYear}`;
//   console.log("Setting date:", formattedDate);
//   const delivery_challan_date = $("#delivery_challan_date");
//   if (delivery_challan_date.length > 0) {
//       delivery_challan_date.val(formattedDate);

//       // Initialize Flatpickr only once
//       if (delivery_challan_date.data("flatpickr")) {
//         // Update Flatpickr date if it's already initialized
//         delivery_challan_date.data("flatpickr").setDate(formattedDate, true);
//       } else {
//         // Initialize Flatpickr if not already initialized
//         delivery_challan_date.flatpickr({
//           dateFormat: "d-m-Y",
//           defaultDate: formattedDate,
//           allowInput: false,
//           readOnly: true,
//         });
//       }
//     }
// }


  var targetNode1 = document.getElementById("delivery_challan_location1");
  var observer1 = new MutationObserver(function (mutationsList1) {
    for (var mutation1 of mutationsList1) {
      if (
        mutation1.type === "attributes" &&
        mutation1.attributeName === "value"
      ) {
        console.log("delivery_challan_location1 value changed to:", targetNode.value);

        getcompanydetail(targetNode1.value);
      }
    }
  });

  // Configuration for the observer (observe attribute changes)
  var config1 = { attributes: true };
  observer1.observe(targetNode1, config1);

///////////get company detail///////
  function getcompanydetail(delivery_challan_location_id) {
    if (delivery_challan_location_id) {
      data = {
        dc_location_id: delivery_challan_location_id,
        _csrf: $("#csrfToken").val(),
      };

      $.ajax({
        type: "GET",
        url: "getcompanydetail",
        // async:false,
        data: data,
        success: function (response) {

          // Check if the data object exists and contains 'first_name'
          if (response && response.data) {
            $("#company_name").prop("readonly", true);
            $("#company_address").prop("readonly", true);
            $("#company_gstin").prop("readonly", true);
            $("#company_pan").prop("readonly", true);
            $("#contact_number").prop("readonly", true);

            $("#company_name").val(response.data.mobile);
            $("#company_address").val(response.data.email);
            $("#company_gstin").val(response.data.mobile);
            $("#company_pan").val(response.data.email);
            $("#contact_number").val(response.data.mobile);

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

   var targetNode = document.getElementById("so_number1");
  var observer = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
      if (
        mutation.type === "attributes" &&
        mutation.attributeName === "value"
      ) {
        console.log("delivery_challan_location1 value changed to:", targetNode.value);

        getcustomerpodetail(targetNode.value);
      }
    }
  });

  // Configuration for the observer (observe attribute changes)
  var config = { attributes: true };
  observer.observe(targetNode, config);
  ///////////get customer po detail///////
  async function getcustomerpodetail(so_number1) {
    if (so_number1) {
      data = {
        so_number: so_number1,
        _csrf: $("#csrfToken").val(),
      };

      try {
        const dcresponse = await $.ajax({
            type: "GET",
            url: "getcustomerpodetail",
            data: data,
            dataType: "json",
        });
          // Check if the data object exists and contains 'first_name'
          if (dcresponse && dcresponse.data) {
            $("#customer_bill_to_name").prop("readonly", true);
            $("#customer_bill_to_address").prop("readonly", true);
            $("#customer_bill_to_gstin").prop("readonly", true);
            $("#customer_bill_to_pan").prop("readonly", true);
            $("#cust_po_number").prop("readonly", true);
            $("#cust_po_date").prop("readonly", true);
            $("#payment_terms").prop("readonly", true);
            
            $("#customer_bill_to_name").val(dcresponse.data.bill_to_legal_name);
            $("#customer_bill_to_address").val(dcresponse.data.address);
            $("#customer_bill_to_gstin").val(dcresponse.data.gst);
            $("#customer_bill_to_pan").val(dcresponse.data.pan);
            $("#cust_po_number").val(dcresponse.data.customer_po_num);
            flatpickr("#cust_po_date", {
              dateFormat: "d-m-Y",
              defaultDate: dcresponse.data.customer_po_date , // expects dd-mm-yyyy
              allowInput: false,
              readOnly: true,
            });
            // $("#cust_po_date").val(dcresponse.data.customer_po_date);
            $("#payment_terms").val(dcresponse.data.customer_payment_terms).trigger("change");

            
            //ship details
            $("#customer_ship_to_name").prop("readonly", true);
            $("#customer_ship_to_address").prop("readonly", true);
            $("#customer_ship_to_gstin").prop("readonly", true);
            $("#state_code").prop("readonly", true);
            $("#customer_ship_to_pan").prop("readonly", true);

            $("#customer_ship_to_name").val(dcresponse.ship_details.vendor_loc_name);
            $("#customer_ship_to_address").val(dcresponse.ship_details.ship_address);
            $("#customer_ship_to_gstin").val(dcresponse.ship_details.ship_gst);
            $("#state_code").val(dcresponse.ship_details.ship_state_code);
            $("#customer_ship_to_pan").val(dcresponse.ship_details.pan);


            if (dcresponse && dcresponse.product_details) {
            $('#productTable2749 tbody').html('');

            $("#loading-overlay").css('display', 'grid');

            // Initialize currentRow to keep track of the last appended row
            let currentRow = '';

            // Loop through each product in the response
            for (let i = 0; i < dcresponse.product_details.length; i++) {
                const j = i + 1;
                const res = dcresponse.product_details[i];

                // Wait for the row to be added before proceeding with updates
                await addRowBtn("2749", "deliverychallan");

                // Find the last row and get its index
                const tbody = $('#productTable' + 2749 + ' tbody');
                const lastRow = tbody.find('tr:last');
                const rowIndex = lastRow.index();

                // Check if this row is new and not already updated
                if (lastRow.length > 0 && currentRow !== rowIndex) {
                    console.log(`Processing row ${j} (Row Index: ${rowIndex})`);

                    // Find the product_name input element
                    const dcproductNameInput = lastRow.find(`#poduct_description_${j}`);

                    if (dcproductNameInput.length > 0) {
                        // Update the input values for the row
                        lastRow.find(`#poduct_description_${j}`).attr("readonly", "readonly");;
                        lastRow.find(`#poduct_description_${j}`).val(res.prod_name);
                        lastRow.find(`#product_hsn_${j}`).prop("readonly",true);
                        lastRow.find(`#product_hsn_${j}`).val(res.hsn_code);
                        lastRow.find(`#unit_price_${j}`).prop("readonly",true);
                        lastRow.find(`#unit_price_${j}`).val(res.basic_price);
                        lastRow.find(`#gst_rate_${j}`).val(res.cgst_per);
                        $(".remove-row-btn").addClass("tr-hidden");
                        
                        lastRow.find(`#total_amount_${j}`).prop("readonly",true);
                        lastRow.find(`#gst_rate_${j}`).prop("readonly",true);
                        lastRow.find(`#gst_amount_${j}`).prop("readonly",true);
                        lastRow.find(`#invoice_sub_total_${j}`).prop("readonly",true);
                        lastRow.find(`#total_invoice_amt_${j}`).prop("readonly",true);
                        lastRow.find(`#total_invoice_amount_words_${j}`).prop("readonly",true);

                        lastRow.find(`#product_qty_${j}`).removeClass("NU~O").addClass("NU~M");

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
          }  

          } else {
            console.log("Invalid response format or missing data");
          }
    }catch (error) {
        console.error("Error occurred while fetching product details:", error);
        alert("Error occurred. Please try again.");
    }

  }
}

  //////get materila reciver details ////
  var mrn_targetNode = document.getElementById("material_receiver_name1");
  var mrn_observer = new MutationObserver(function (mrn_mutationsList) {
    for (var mrn_mutation of mrn_mutationsList) {
      if (
        mrn_mutation.type === "attributes" &&
        mrn_mutation.attributeName === "value"
      ) {
        getmaterialreceiverdetail(mrn_targetNode.value);
      }
    }
  });

  // Configuration for the observer (observe attribute changes)
  var mrn_config = { attributes: true };
  mrn_observer.observe(mrn_targetNode, mrn_config);
  ///////////get customer po detail///////
  function getmaterialreceiverdetail(contact) {
    if (contact) {
      data = {
        contact: contact,
        _csrf: $("#csrfToken").val(),
      };

      $.ajax({
        type: "GET",
        url: "getmaterialreceiverdetail",
        // async:false,
        data: data,
        success: function (response) {
          console.log(response);
          // Check if the data object exists and contains 'first_name'
          if (response && response.data) {
            $("#material_receiver_contact_number").prop("readonly", true);
            $("#material_receiver_alt_contact_number").prop("readonly", true);
            $("#material_receiver_email").prop("readonly", true);
            
            $("#material_receiver_contact_number").val(response.data.mobile);
            $("#material_receiver_alt_contact_number").val(response.data.home_mobile);
            $("#material_receiver_email").val(response.data.email);

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

  // calculations
  // .unit_price, .gst_rate
  $(document).on('input', '.product_qty', function () {
    const $row = $(this).closest('.product-row');
    calculateRow($row);
    calculateInvoiceTotals();
});

function calculateRow($row) {
    const qty = parseFloat($row.find('.product_qty').val()) || 0;
    const price = parseFloat($row.find('.unit_price').val()) || 0;
    const gstRate = parseFloat($row.find('.gst_rate').val()) || 0;
    const igstRate = parseFloat($row.find('.igst_rate').val()) || 0;
    const cgstRate = parseFloat($row.find('.igst_rate').val()) || 0;
    const sgstRate = parseFloat($row.find('.igst_rate').val()) || 0;


    const totalAmount = qty * price;

    let gstAmount = 0;
    if (igstRate !== null && igstRate !== 0) {
        // IGST is applicable
        gstAmount = (totalAmount * igstRate) / 100;
    }
    else{
        gstAmount = (totalAmount * (sgstRate + cgstRate)) / 100;
    }
    const invoiceSubTotal = totalAmount + gstAmount;

    $row.find('.total_amount').val(totalAmount.toFixed(2));
    $row.find('.gst_amount').val(gstAmount.toFixed(2));
    $row.find('.invoice_sub_total').val(invoiceSubTotal.toFixed(2));
}

function calculateInvoiceTotals() {
    let totalInvoiceAmount = 0;
    $('.product-row').each(function () {
        const rowTotal = parseFloat($(this).find('.invoice_sub_total').val()) || 0;
        totalInvoiceAmount += rowTotal;
    });

    $('.total_invoice_amt').val(totalInvoiceAmount.toFixed(2));
    $('.total_invoice_amount_words').val(numberToWords(totalInvoiceAmount) + ' only');
}

function numberToWords(num) {
    // Simple converter (English, up to 999999)
    const a = ['','One','Two','Three','Four','Five','Six','Seven','Eight','Nine','Ten','Eleven',
        'Twelve','Thirteen','Fourteen','Fifteen','Sixteen','Seventeen','Eighteen','Nineteen'];
    const b = ['','', 'Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'];

    if ((num = num.toString()).length > 9) return 'Overflow';
    let n = ('000000000' + num).substr(-9).match(/^(\d{2})(\d{2})(\d{2})(\d{3})$/);
    if (!n) return ''; 
    let str = '';
    str += (n[1] != 0) ? (a[Number(n[1])] || b[n[1][0]] + ' ' + a[n[1][1]]) + ' Crore ' : '';
    str += (n[2] != 0) ? (a[Number(n[2])] || b[n[2][0]] + ' ' + a[n[2][1]]) + ' Lakh ' : '';
    str += (n[3] != 0) ? (a[Number(n[3])] || b[n[3][0]] + ' ' + a[n[3][1]]) + ' Thousand ' : '';
    str += (n[4] != 0) ? (a[Number(n[4])] || b[n[4][0]] + ' ' + a[n[4][1]]) + ' ' : '';
    return str.trim();
}
});