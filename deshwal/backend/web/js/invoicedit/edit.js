$(document).ready(function () {

  $(".add-more-records").hide();
  // $("#payment_due_date").addClass('readonly-bg');
  const stageSelect = document.getElementById("invoice_status");

  const modeInput = document.getElementById("mode");
  if (modeInput && modeInput.value === "Create") {
    $("#invoice_status").val("1").trigger("change");
    setTimeout(() => {
      flatpickr("#invoice_date", {
        defaultDate: new Date(),
        dateFormat: "d-m-Y"
      });
    }, 500);
  }
  else {
    // if ($("#invoice_status").val() > 1) {
    //   $("#send_for_approval").prop("disabled", true);
    // }
    if (parseInt($("#invoice_status").val()) == 1 && $("#invoice_status").is(":checked")) {
      console.log("in send_for_approval");
      $("#send_for_approval").prop("disabled", true);
    }
    setTimeout(() => {
      const invoice_dateVal = $("#invoice_date").val(); // e.g. "2025-09-02"
      let defaultinvoiceDate = null;

      if (invoice_dateVal) {
        var invparts = invoice_dateVal.split("-");
        // Convert Y-m-d to d-m-Y
        defaultinvoiceDate = `${invparts[2]}-${invparts[1]}-${invparts[0]}`; // 02-09-2025
      }

      flatpickr("#invoice_date", {
        dateFormat: "d-m-Y",
        defaultDate: defaultinvoiceDate,
      });
    // }, 500);
    // setTimeout(() => {
      const rawVal = $("#payment_due_date").val(); // e.g. "2025-09-02"
      let defaultDate = null;

      if (rawVal) {
        const parts = rawVal.split("-");
        // Convert Y-m-d to d-m-Y
        defaultDate = `${parts[2]}-${parts[1]}-${parts[0]}`; // 02-09-2025
      }

      flatpickr("#payment_due_date", {
        dateFormat: "d-m-Y",
        defaultDate: defaultDate,
        // clickOpens: false
      });
    }, 500);
    $(".remove-row-btn").addClass("tr-hidden");
    $('#productTable2753').find('td:has(button.remove-row-btn.tr-hidden)').remove();
    $('#productTable2753 thead th.col-80').remove();
    //need to call hide show ship from address
    getdcdetail($("#delivery_challan_number1").val());

  }

  if (stageSelect) {
    var invoice_status = parseInt($("#invoice_status").val());
    //   alert(invoice_status);
    const options = stageSelect.options;
    if (invoice_status != 4)// if not Pending for Submission make readonly
    {
      const stage = parseInt($("#invoice_status").val() || "0");

      for (let i = 0; i < options.length; i++) {
        const value = options[i].value;

        // Always enable current stage
        if (parseInt(value) === stage) {
          options[i].disabled = false;
        }
        // Disable everything else
        else {
          options[i].disabled = true;
        }
      }

    }
    else if (invoice_status == 4) {
      //then disable other status except  1,2,3,6,7,8
      const stage = parseInt($("#invoice_status").val() || "0");

      // Disable all options except current stage and conditional "5"
      for (let i = 0; i < options.length; i++) {
        const value = options[i].value;

        // Always enable current stage
        if (parseInt(value) === stage || parseInt(value) === 5) {
          options[i].disabled = false;
        }
        // Disable everything else
        else {
          options[i].disabled = true;
        }
      }
    }
  }
  //   if (stageSelect) {
  //     // Get the updated stage value
  //     const stage = parseInt($("#stage").val() || "0");

  //     // Disable all options except current stage and conditional "8"
  //     const options = stageSelect.options;
  //     for (let i = 0; i < options.length; i++) {
  //       const value = options[i].value;

  //       // Always enable current stage
  //       if (parseInt(value) === stage) {
  //         options[i].disabled = false;
  //       }
  //       // Disable everything else
  //       else {
  //         options[i].disabled = true;
  //       }
  //     }
  //   }


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
      url: "approveinvoicedit",
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
        url: "approveinvoicedit",
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
  function applySelect2() {
    $('.singleselect').each(function () {
      if (!$(this).hasClass('select2-hidden-accessible')) {
        $(this).select2({
          placeholder: "Select",
          allowClear: true,
          width: '100%'
        });
      }
    });
    $('.multySelect').each(function () {
      if (!$(this).hasClass('select2-hidden-accessible')) {
        $(this).select2({
          placeholder: "Select",
          allowClear: true,
          width: '100%'
        });
      }
    });
  }


  ///////////autofill from delivery challan/////////////
  var targetNode1 = document.getElementById("delivery_challan_number1");
  var observer1 = new MutationObserver(function (mutationsList1) {
    for (var mutation1 of mutationsList1) {
      if (
        mutation1.type === "attributes" &&
        mutation1.attributeName === "value"
      ) {
        console.log("dc value changed to:", targetNode1.value);
        // empty product table
        $('#productTable2753 tbody').html('');

        getdcdetail(targetNode1.value);
        getproductdetail(targetNode1.value);
        calculateRowTotals();
      }
    }
  });

  // Configuration for the observer (observe attribute changes)
  var config1 = { attributes: true };
  observer1.observe(targetNode1, config1);

  ///////////get dc detail///////
  function getdcdetail(dcid) {
    
    if (dcid) {
      //empty autofills
            $("#company_name").val('');
            $("#company_address").val('');
            $("#gstin").val('');
            $("#contact_number").val('');
            $("#vehicle_docket_number").val('');
            $("#transporter_name1").val('');
            $("#transporter_name").val('');
            $("#payment_terms").val('');
            $("#invoice_date").val();
            $("#e-way_bill_number").val('');
            $("#place_of_supply").val('');
            $("#dc_eway_bill_number").val('');

            $("#customer_bill_name").val('');
            $("#customer_bill_address").val('');
            $("#customer_bill_gstin").val('');
            $("#customer_bill_pan").val('');
            $("#customer_po_number").val('');
            $("#customer_po_date").val('');
           

            $("#customer_ship_name").val('');
            $("#customer_ship_address").val('');
            $("#customer_ship_gstin").val('');
            $("#customer_ship_pan").val('');
            $("#material_receiver_name1").val('');
            $("#material_receiver_name").val('');
            $("#material_receiver_contact_number").val('');
            $("#material_receiver_email").val('');
            $("#invoice_sub_total").val('');
            $("mod_of_transport").val('');

      //end empty autofills
      data = {
        dcid: dcid,
        _csrf: $("#csrfToken").val(),
      };

      $.ajax({
        type: "GET",
        url: "getdcdetail",
        // async:false,
        data: data,
        success: function (response) {
          
          // Check if the data object exists and contains 'first_name'
          if (response && response.data) {
            $("#company_name").prop("readonly", true);
            $("#company_address").prop("readonly", true);
            $("#gstin").prop("readonly", true);
            // $("#company_pan").prop("readonly", true);
            $("#contact_number").prop("readonly", true);
            $("#vehicle_docket_number").prop("readonly", true);

            $("#company_name").val(response.data.company_name);
            $("#company_address").val(response.data.company_address);
            $("#gstin").val(response.data.company_gstin);
            // $("#company_pan").val(response.data.pan_number);
            $("#contact_number").val(response.data.contact_number);
            $("#vehicle_docket_number").val(response.data.vehicle_docket_number);
            $("#transporter_name1").val(response.data.transporter_name);
            $("#transporter_name").val(response.data.acc_name);
            $("#payment_terms").val(response.data.payment_terms);
            // $("#payment_due_date").val(response.data.payment_terms).trigger("change");
            ///payment due date is payment_terms + invoice date
            // if(modeInput.value === "Create")
            setPaymentDueDate($("#invoice_date").val(), response.data.payment_terms);
            $("#e-way_bill_number").val(response.data.dc_eway_bill_number);
            $("#place_of_supply").val(response.data.state_code);
            $("#dc_eway_bill_number").val(response.data.dc_eway_bill_number);

            $("#customer_bill_name").val(response.data.customer_bill_to_name);
            $("#customer_bill_address").val(response.data.customer_bill_to_address);
            $("#customer_bill_gstin").val(response.data.customer_bill_to_gstin);
            $("#customer_bill_pan").val(response.data.customer_bill_to_pan);
            $("#customer_po_number").val(response.data.cust_po_number);
            // $("#customer_po_date").val(response.data.cust_po_date);
            flatpickr("#customer_po_date", {
              dateFormat: "d-m-Y",
              defaultDate: response.data.cust_po_date, // expects dd-mm-yyyy
              allowInput: false,
              readOnly: true,
            });

            $("#customer_ship_name").val(response.data.customer_ship_to_name);
            $("#customer_ship_address").val(response.data.customer_ship_to_address);
            $("#customer_ship_gstin").val(response.data.customer_ship_to_gstin);
            $("#customer_ship_pan").val(response.data.customer_ship_to_pan);
            $("#material_receiver_name1").val(response.data.material_receiver_name);
            $("#material_receiver_name").val(response.data.mcvname);
            $("#material_receiver_contact_number").val(response.data.material_receiver_contact_number);
            $("#material_receiver_email").val(response.data.material_receiver_email);
            $("#invoice_sub_total").val(response.data.total_invoice_amt);

            $("#mod_of_transport").val(response.data.mod_of_transport);

            console.log("dcinfo"+response.data);
            console.log("ship_by"+response.data.ship_by);
            if(response.data.ship_by)
            {
              togglewarehouseorvendor(response.data);
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

  }
  ///////get product detail////////////
  async function getproductdetail(dcid) {
    if (dcid) {
      data = {
        dcid: dcid,
        _csrf: $("#csrfToken").val(),
      };

      try {
        const dcresponse = await $.ajax({
          type: "GET",
          url: "getproductdetail",
          data: data,
          dataType: "json",
        });
        // Check if the data object exists and contains 'first_name'
        // console.log("dcresponse");
        // console.log(dcresponse);
        if (dcresponse && dcresponse.product_details) {
          if (dcresponse && dcresponse.product_details) {
            $('#productTable2753 tbody').html('');

            $("#loading-overlay").css('display', 'grid');

            // Initialize currentRow to keep track of the last appended row
            let currentRow = '';

            // Loop through each product in the response
            for (let i = 0; i < dcresponse.product_details.length; i++) {
              const j = i + 1;
              const res = dcresponse.product_details[i];
              // Wait for the row to be added before proceeding with updates
              await addRowBtn("2753", "invoicedit");

              // Find the last row and get its index
              const tbody = $('#productTable' + 2753 + ' tbody');
              const lastRow = tbody.find('tr:last');
              const rowIndex = lastRow.index();

              console.log("lastRow" + lastRow + "rowIndex" + rowIndex);
              // Check if this row is new and not already updated
              if (lastRow.length > 0 && currentRow !== rowIndex) {
                console.log(`Processing row ${j} (Row Index: ${rowIndex})`);

                // Find the product_name input element
                const dcproductNameInput = lastRow.find(`#product_discription_${j}`);

                if (dcproductNameInput.length > 0) {

                  // Update the input values for the row
                  lastRow.find(`#product_discription_${j}1`).val(res.poduct_description);
                  lastRow.find(`#product_discription_${j}`).val(res.prod_name);
                  lastRow.find(`#currency_${j}`).val("1").trigger("change");
                  lastRow.find(`#product_qty_${j}`).val(res.product_qty);
                  lastRow.find(`#product_hsn_${j}`).val(res.product_hsn);
                  lastRow.find(`#unit_price_${j}`).val(res.unit_price);
                  lastRow.find(`#gst_rate_${j}`).val(res.gst_rate);
                  // lastRow.find(`#gst_amount_${j}`).val(res.gst_amount);
                  $(".remove-row-btn").addClass("tr-hidden");
                  currentRow = rowIndex;
                } else {
                  console.error(`Ship Location input not found for ID: #${j}`);
                  console.error("error fetching product");
                }
                $('#productTable2753').find('td:has(button.remove-row-btn.tr-hidden)').remove();
                $('#productTable2753 thead th.col-80').remove();
                calculateRowTotals();
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
      } catch (error) {
        console.error("Error occurred while fetching product details:", error);
        alert("Error occurred. Please try again.");
      }
      applySelect2();

    }
  }
  $(document).on('input', '.discount_age', function () {
    console.log("Discount changed");
    calculateRowTotals();
  });

  $(document).on('input', '#discount', function () {

    calculateRowTotals();
  });

  $(document).on('change', '#invoice_date', function () {
    console.log("invoice_date changed"+$(this).val()+"--"+$("#payment_terms").val());
    setPaymentDueDate($(this).val(),$("#payment_terms").val());
  });

    function togglewarehouseorvendor(arr){
    let value = arr.ship_by;
    // alert(value);
    let warehouse_fields = [".section-warehouse_location_name",".section-warehouse_address",".section-warehouse_state",
      ".section-warehouse_pin_code",".section-warehouse_warehouse_name",".section-warehouse_city",".section-warehouse_state_code",".section-warehouse_gstin_no"
    ];
    let vendor_fields =[".section-vendor_name",'.section-vendor_location_name','.section-vendor_address','.section-vendor_pin_code',
      '.section-vendor_city','.section-vendor_pan_no','.section-vendor_state','.section-vendor_state_code','.section-vendor_gstin_no'
    ];
    function hideAndClear(fields) {
        fields.forEach(selector => {
            $(selector).hide();
            $(selector).find("input, select, textarea").val(""); // clear inputs inside
        });
    }

    // Show only the relevant fields
    if (value == "1") {
        $(warehouse_fields.join(",")).show();  
        $("#warehouse_location_name1").val(arr.warehouse_location_name || "");  
        $("#warehouse_location_name").val(arr.warehouse_name || "");
        $("#warehouse_address").val(arr.warehouse_address || "");
        $("#warehouse_city").val(arr.warehouse_city || "");
        $("#warehouse_gstin_no").val(arr.warehouse_gstin_no || "");
        $("#warehouse_pin_code").val(arr.warehouse_pin_code || "");
        $("#warehouse_state").val(arr.warehouse_state || "");
        $("#warehouse_state_code").val(arr.warehouse_state_code || "");
        hideAndClear(vendor_fields);
    } 
    else if (value == "2") {
        $(vendor_fields.join(",")).show();
        $("#vendor_name1").val(arr.vendor_name || "");  
        $("#vendor_name").val(arr.vendorname || "");  
        $("#vendor_location_name1").val(arr.vendor_location_name || "");
        $("#vendor_location_name").val(arr.vendorlocname || "");
        $("#vendor_address").val(arr.vendor_address || "");
        $("#vendor_city").val(arr.vendor_city || "");
        $("#vendor_gstin_no").val(arr.vendor_gstin_no || "");
        $("#vendor_pan_no").val(arr.vendor_pan_no || "");
        $("#vendor_state").val(arr.vendor_state || "");
        $("#vendor_state_code").val(arr.vendor_state_code || "");
        $("#vendor_pin_code").val(arr.vendor_pin_code || "");
         hideAndClear(warehouse_fields);
    }
    }

});
function calculateRowTotals() {
  let subTotal = 0;
  let totalGST = 0;

  $('.product-row').each(function () {
    const row = $(this);
    const rowId = row.attr('id');

    const qty = parseFloat($('#product_qty_' + rowId).val()) || 0;
    const unitPrice = parseFloat($('#unit_price_' + rowId).val()) || 0;
    const discountPer = parseFloat($('#discount_age_' + rowId).val()) || 0;
    const gstRate = parseFloat($('#gst_rate_' + rowId).val()) || 0;

    // Total before discount
    const baseTotal = qty * unitPrice;
    const discountAmt = (baseTotal * discountPer) / 100;
    const totalAmount = baseTotal - discountAmt;

    // GST calculation
    const gstAmount = (totalAmount * gstRate) / 100;

    // Set calculated values
    $('#total_amount_' + rowId).val(totalAmount.toFixed(2));
    $('#gst_amount_' + rowId).val(gstAmount.toFixed(2));

    // Add to cumulative totals
    subTotal += totalAmount;
    totalGST += gstAmount;
  });

  // Update subtotal and GST in DOM
  $('#invoice_sub_total').val(subTotal.toFixed(2));
  // $('#total_gst').val(totalGST.toFixed(2));

  // Manual discount from input
  const manualDiscount = parseFloat($('#discount').val()) || 0;

  // Final Invoice Amount
  const invoiceTotal = subTotal - manualDiscount + totalGST;
  $('#total_invoice_amount').val(invoiceTotal.toFixed(2));
  $('#total_invoice_amount_word').val(numberToWords(invoiceTotal.toFixed(2)));
}

function numberToWords(number) {
  number = parseFloat(number); // Accept dynamic input
  const no = Math.floor(number);
  const point = Math.round((number - no) * 100);

  const words = {
    0: '',
    1: 'one',
    2: 'two',
    3: 'three',
    4: 'four',
    5: 'five',
    6: 'six',
    7: 'seven',
    8: 'eight',
    9: 'nine',
    10: 'ten',
    11: 'eleven',
    12: 'twelve',
    13: 'thirteen',
    14: 'fourteen',
    15: 'fifteen',
    16: 'sixteen',
    17: 'seventeen',
    18: 'eighteen',
    19: 'nineteen',
    20: 'twenty',
    30: 'thirty',
    40: 'forty',
    50: 'fifty',
    60: 'sixty',
    70: 'seventy',
    80: 'eighty',
    90: 'ninety'
  };

  const getWords = (num) => {
    if (num === 0) return '';
    if (num < 21) return words[num];
    const tens = Math.floor(num / 10) * 10;
    const units = num % 10;
    return words[tens] + (units ? ' ' + words[units] : '');
  };

  const segments = [];
  let n = no;

  // Break number using Indian system: ######,##,###
  const crore = Math.floor(n / 10000000);
  if (crore) {
    segments.push(getWords(crore) + ' crore');
    n = n % 10000000;
  }

  const lakh = Math.floor(n / 100000);
  if (lakh) {
    segments.push(getWords(lakh) + ' lakh');
    n = n % 100000;
  }

  const thousand = Math.floor(n / 1000);
  if (thousand) {
    segments.push(getWords(thousand) + ' thousand');
    n = n % 1000;
  }

  const hundred = Math.floor(n / 100);
  if (hundred) {
    segments.push(getWords(hundred) + ' hundred');
    n = n % 100;
  }

  if (n) {
    segments.push((segments.length ? 'and ' : '') + getWords(n));
  }

  let result = segments.join(' ').replace(/\s+/g, ' ').trim();

  let paise = '';
  if (point > 0) {
    const p1 = Math.floor(point / 10) * 10;
    const p2 = point % 10;
    paise = ' and ' + getWords(p1) + (p2 ? ' ' + getWords(p2) : '') + ' paise';
  }

  return (result + ' rupees' + paise + ' only')
    .replace(/\s+/g, ' ')
    .trim()
    .replace(/\b\w/g, c => c.toUpperCase());
}

function setPaymentDueDate(invoice_date, payment_term) {
  // Convert d-m-Y to Date object
  const parts = invoice_date.split("-");
  const invoiceDate = new Date(`${parts[2]}-${parts[1]}-${parts[0]}`); // "Y-m-d"

  const daysMatch = payment_term.match(/^(\d+)\s*days?$/i);
  console.log("invoiceDate" + invoiceDate);
  const daysToAdd = daysMatch ? parseInt(daysMatch[1]) : 0;
  setTimeout(() => {
    if (daysToAdd > 0) {
      const today = new Date(invoiceDate);
      const dueDate = new Date(today);
      dueDate.setDate(today.getDate() + daysToAdd);

      flatpickr("#payment_due_date", {
        defaultDate: dueDate,
        dateFormat: "d-m-Y",
        // clickOpens: false
      });
    } 
    // else {
    //   // Optional: clear or set to today, or skip entirely
    //   flatpickr("#payment_due_date", {
    //     defaultDate: new Date(), // or null if you want it empty
    //     dateFormat: "d-m-Y",
    //     clickOpens: false
    //   });
    // }
  }, 500);

  

}
