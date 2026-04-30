$(document).ready(function () {

  //hide add row btn
  $(".add-more-records").hide();
  // $('#payment_terms option').prop('disabled', true); // disable all options
  if ($("#delivery_challan_type").val() != "") {
    toggleBlocks($("#delivery_challan_type").val(), $("#status").val());
  }



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
  else {
    toggleBlocks($("#delivery_challan_type").val(), $("#status").val());
    if (parseInt($("#send_for_approval").val()) == 1 && $("#send_for_approval").is(":checked")) {
      $("#send_for_approval").prop("disabled", true);
    }
    if ($("#delivery_challan_type").val() == 4) {
      $(".product_qty").prop("readonly", true);
    }
    $("#company_name").prop("readonly", true);
    $("#company_address").prop("readonly", true);
    $("#company_gstin").prop("readonly", true);
    $("#company_pan").prop("readonly", true);
    $("#contact_number").prop("readonly", true);

    $("#customer_bill_to_name").prop("readonly", true);
    $("#customer_bill_to_address").prop("readonly", true);
    $("#customer_bill_to_gstin").prop("readonly", true);
    $("#customer_bill_to_pan").prop("readonly", true);
    $("#cust_po_number").prop("readonly", true);
    $("#cust_po_date").prop("readonly", true);
    $("#payment_terms").prop("readonly", true);
    if ($("#delivery_challan_type").val() == 4 ) {
      $("#material_receiver_name").prop("readonly", true);
      toggleIconsBasedOnReadonly();
    }
    else
    {
      $("#material_receiver_name").prop("readonly", false);
      toggleIconsBasedOnReadonly();
    }
    $("#material_receiver_contact_number").prop("readonly", true);
    $("#material_receiver_alt_contact_number").prop("readonly", true);
    $("#material_receiver_email").prop("readonly", true);
    toggleIconsBasedOnReadonly();

    $("#customer_ship_to_name").prop("readonly", true);
    $("#customer_ship_to_address").prop("readonly", true);
    $("#customer_ship_to_gstin").prop("readonly", true);
    $("#state_code").prop("readonly", true);
    $("#customer_ship_to_pan").prop("readonly", true);

    if ($("#delivery_challan_type").val() != 2) {
      $(".unit_price").prop("readonly", true);
    }
    togglewarehouseorvendor($("#ship_by").val());
  }

  var inv_val = $("#invoice_created").val();
  if(inv_val == "" || inv_val == "0")
    $("#invoice_created").val("1").trigger("change");
  // else
  //   $("#invoice_created").val(inv_val).trigger("change");
  $("#invoice_created").prop("readonly", true);
  $("#invoice_date").prop("disabled", true);
  $("#invoice_number").prop("disabled", true);
  var invSelect = document.getElementById("invoice_created");
  if (invSelect) {
    // Get the updated stage invvalue
    const invstage = parseInt($("#invoice_created").val() || "0");

    // Disable all options except current invstage and conditional "8"
    const invoptions = invSelect.options;
    for (let i = 0; i < invoptions.length; i++) {
      const invvalue = parseInt(invoptions[i].value);

      // Always enable current invstage
      if (invvalue === invstage) {
        invoptions[i].disabled = false;
      }
      else if (parseInt($("#status").val()) == 5 && i == 2) {
        invoptions[i].disabled = true;
      }
      else {
        invoptions[i].disabled = true;
      }
    }
  }


  var stageSelect = document.getElementById("status");
  if (stageSelect) {
    let delivery_challan_type = parseInt($("#delivery_challan_type").val());
    console.log("delivery_challan_type"+delivery_challan_type);
    // Get the updated stage value
    const stage = parseInt($("#status").val() || "0");
    var invoice_created = parseInt($("#invoice_created").val());
    
    console.log("invoice_created"+invoice_created);
    // Disable all options except current stage and conditional "8"
    const options = stageSelect.options;
    for (let i = 0; i < options.length; i++) {
      const value = options[i].value;

      // Always enable current stage
      if (parseInt(value) === stage) {
        options[i].disabled = false;
      }
      else if ((stage === "1" && i == 4) || (stage == 5 && i == 6) || (stage == 6 && i === 7 
    //     && (
    //   (invoice_created == 2 && delivery_challan_type == 1) ||
    //   (delivery_challan_type != 1)
    // )
  ) || (stage == 7 && i == 8)) {
        options[i].disabled = false;
      }
      // else if ((stage === "1" && i == 4) || (stage == 5 && i == 6) || (stage == 6 && (i == 7 && delivery_challan_type != 1)) || (stage == 7 && i == 8)) {
      //   options[i].disabled = false;
      // }
      else if(i==4 && (stage == "1"))
      {
        options[i].disabled = false;
      }
      // Disable everything else
      else {
        options[i].disabled = true;
      }
    }
  }

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
      url: "approvedeliverychallandit",
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
        url: "approvedeliverychallandit",
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
  ///invoice 




  $(document).on("change", "#delivery_challan_type", function () {
    var delivery_challan_type = $(this).val();
    var status = $("#status").val();
    toggleBlocks(delivery_challan_type, status);
  });
  $(document).on("change", "#status", function () {
    var delivery_challan_type = $("#delivery_challan_type").val();
    var status = $(this).val();
    toggleBlocks(delivery_challan_type, status);
  });
  // function toggleBlocks(dc_type, status) {
  //   let hide_section = ["foc_number", "pod_image", "delivery_date", "return_condition", "returned_date", "returnable_date"];
  //   // let all_sections = ["foc_number", "pod_image", "delivery_date", "return_condition", "returned_date", "returnable_date"];
  //  //this imp
  //   hide_section.forEach(function (key) {
  //     let el = document.querySelector('.section-' + key);
  //     if (el) {
  //       el.style.display = '';
  //     }
  //   });
  //   if (dc_type == 4) {
  //     //show FOC section
  //     hide_section = ["so_number","pod_image", "delivery_date", "return_condition", "returned_date", "returnable_date","customer_bill_to_gstin","customer_bill_to_pan","cust_po_number","cust_po_date","payment_terms","customer_ship_to_gstin","state_code","customer_ship_to_pan","material_receiver_alt_contact_number","material_receiver_email"];
  //     $(".row2750").hide();
  //     $("#invoice_created").val("").trigger("change");
  //     if(status == 6 )
  //     {
  //        hide_section = ["so_number", "return_condition", "returned_date", "returnable_date","customer_bill_to_gstin","customer_bill_to_pan","cust_po_number","cust_po_date","payment_terms","customer_ship_to_gstin","state_code","customer_ship_to_pan","material_receiver_alt_contact_number","material_receiver_email"];
  //     }
  //     else if (status == 7)//in delivery
  //     {
  //       hide_section = ["so_number", "return_condition", "returned_date", "returnable_date","customer_bill_to_gstin","customer_bill_to_pan","cust_po_number","cust_po_date","payment_terms","customer_ship_to_gstin","state_code","customer_ship_to_pan","material_receiver_alt_contact_number","material_receiver_email"];
  //       $("#pod_image").removeClass("F~O").addClass("F~M");
  //       $("#delivery_date").removeClass("DT~O").addClass("DT~M");
  //     }
  //     else if (status == 8)//in delivery
  //     {
  //        hide_section = ["so_number", "pod_image", "delivery_date", "returnable_date","customer_bill_to_gstin","customer_bill_to_pan","cust_po_number","cust_po_date","payment_terms","customer_ship_to_gstin","state_code","customer_ship_to_pan","material_receiver_alt_contact_number","material_receiver_email"];
  //         $("#return_condition").removeClass("DD~O").addClass("DD~M");
  //         $("#returned_date").removeClass("DT~O").addClass("DT~M");

  //     }
  //   }
  //   else if (dc_type == 3) {
  //     //returned_date
  //     hide_section = ["foc_number", "pod_image", "delivery_date", "return_condition", "returned_date"];
  //     $("#returnable_date").removeClass("DT~O").addClass("DT~M");
  //   }
  //   else if (status == 6)//in transit
  //   {
  //     //show pod_image and delivery date
  //     hide_section = ["foc_number", "return_condition", "returned_date", "returnable_date"];
  //   }
  //   else if (status == 7)//in delivery
  //   {
  //     //mandatory pod_image and delivery date
  //     hide_section = ["foc_number", "return_condition", "returned_date", "returnable_date"];
  //     $("#pod_image").removeClass("F~O").addClass("F~M");
  //     $("#delivery_date").removeClass("DT~O").addClass("DT~M");
  //   }
  //   else if (status == 8)//in Returned
  //   {
  //     //show pod_image and delivery date
  //     hide_section = ["foc_number", "pod_image", "delivery_date", "returnable_date"];
  //     $("#return_condition").removeClass("DD~O").addClass("DD~M");
  //     $("#returned_date").removeClass("DT~O").addClass("DT~M");
  //   }
  //   hide_section.forEach(function (key) {
  //     let el = document.querySelector('.section-' + key);
  //     if (el) {
  //       el.style.display = 'none';
  //     }
  //   });
  //   // }
  // }

  ////////toggle function start//////////////////
  function toggleBlocks(dc_type, status) {
    dc_type = parseInt(dc_type);
    status = parseInt(status);

    const allSections = [
      "foc_number", "so_number", "pod_image", "delivery_date", "return_condition", "returned_date",
      "returnable_date", "customer_bill_to_gstin", "customer_bill_to_pan", "cust_po_number", "cust_po_date",
      "payment_terms", "customer_ship_to_gstin", "state_code", "customer_ship_to_pan",
      "material_receiver_alt_contact_number", "material_receiver_email", "dc_eway_bill_number",'vender_account_name'
    ];

    // Step 1: Show all sections initially
    allSections.forEach((key) => {
      const el = document.querySelector('.section-' + key);
      if (el) el.style.display = '';
    });

    // Step 2: Hide based on DC Type
    let hideByType = [];

    switch (dc_type) {
      case 1:
        hideByType = ["returnable_date", "foc_number"];
        $("#material_receiver_name").prop("readonly", false);
         toggleIconsBasedOnReadonly('material_receiver_name');
        $(".product_qty").prop("readonly",false);
        // $(".row2750").show();
        $(".section-invoice_created").show();
        break;
      case 2:
        hideByType = ["dc_eway_bill_number", "returnable_date", "foc_number", "state_code"];
         $("#material_receiver_name").prop("readonly", false);
          toggleIconsBasedOnReadonly('material_receiver_name');
        $(".product_qty").prop("readonly",false);
        //  $(".row2750").show();
         $(".section-invoice_created").hide();
        break;
      case 3:
        hideByType = ["dc_eway_bill_number", "foc_number"];
        $("#returnable_date").removeClass("DT~O").addClass("DT~M");
         $("#material_receiver_name").prop("readonly", false);
          toggleIconsBasedOnReadonly('material_receiver_name');
        $(".product_qty").prop("readonly",false);
        //  $(".row2750").show();
         $(".section-invoice_created").hide();
        break;
      case 4:
        hideByType = [
          "so_number", "dc_eway_bill_number", "returnable_date",
          "customer_bill_to_gstin", "customer_bill_to_pan", "cust_po_date", "cust_po_number",
          "state_code", "payment_terms", "customer_ship_to_gstin", "customer_ship_to_pan",
          "material_receiver_alt_contact_number", "material_receiver_email","vender_account_name"
        ];
        // $(".row2750").hide();
        $(".section-invoice_created").hide();
        $("#material_receiver_name").prop("readonly", true);
         toggleIconsBasedOnReadonly('material_receiver_name');
        $(".product_qty").prop("readonly",true);
        break;
    }

    // Step 3: Default status-based hiding
    let hideByStatus = [
      "return_condition", "returned_date", "pod_image", "delivery_date" // Hide by default
    ];

    switch (status) {
      case 6: // In transit → show pod + delivery
        hideByStatus = ["return_condition", "returned_date"];
        break;
      case 7: // In delivery → show + mandatory
        hideByStatus = ["return_condition", "returned_date"];
        
        if (!$("#pod_image").closest(".form-group").find(".upd-file").length) {
          $("#pod_image").addClass("F~M").removeClass("F~O");
        } else {
          $("#pod_image").removeClass("F~M"); // clear error classes if file exists
        }
        $("#delivery_date").removeClass("DT~O").addClass("DT~M");
        break;
      case 8: // Returned → show + mandatory
        hideByStatus = ["pod_image", "delivery_date"];
        $("#return_condition").removeClass("DD~O").addClass("DD~M");
        $("#returned_date").removeClass("DT~O").addClass("DT~M");
        break;
    }

    // Step 4: Combine and hide unique fields
    const hideCombined = [...new Set([...hideByType, ...hideByStatus])];

    hideCombined.forEach((key) => {
      const el = document.querySelector('.section-' + key);
      if (el) el.style.display = 'none';
    });
  }


  //toggle function end
  var targetNode1 = document.getElementById("delivery_challan_location1");
  var observer1 = new MutationObserver(function (mutationsList1) {
    for (var mutation1 of mutationsList1) {
      if (
        mutation1.type === "attributes" &&
        mutation1.attributeName === "value"
      ) {
        console.log("delivery_challan_location1 value changed to:", targetNode1.value);

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

            $("#company_name").val(response.data.warehouse_name);
            $("#company_address").val(response.data.address);
            $("#company_gstin").val(response.data.gstn);
            $("#company_pan").val(response.data.pan_number);
            $("#contact_number").val(response.data.contact_number);

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
          $("#vender_account_name").prop("readonly", true);
          $("#vender_account_name").val(dcresponse.data.acc_name);
          $("#vender_account_name1").val(dcresponse.data.account_name);
          $("#customer_bill_to_name").prop("readonly", true);
          $("#customer_bill_to_address").prop("readonly", true);
          $("#customer_bill_to_gstin").prop("readonly", true);
          $("#customer_bill_to_pan").prop("readonly", true);
          $("#cust_po_number").prop("readonly", true);
          // $("#cust_po_date").prop("readonly", true);
          $("#payment_terms").prop("readonly", true);

          $("#customer_bill_to_name").val(dcresponse.data.bill_to_legal_name);
          $("#customer_bill_to_address").val(dcresponse.data.address);
          $("#customer_bill_to_gstin").val(dcresponse.data.gst);
          $("#customer_bill_to_pan").val(dcresponse.data.pan);
          $("#cust_po_number").val(dcresponse.data.customer_po_num);
          flatpickr("#cust_po_date", {
            dateFormat: "d-m-Y",
            defaultDate: dcresponse.data.customer_po_date, // expects dd-mm-yyyy
            allowInput: false,
            readOnly: true,
          });
          // $("#cust_po_date").val(dcresponse.data.customer_po_date);
          $("#payment_terms").val(dcresponse.data.customer_payment_terms).trigger("change");

          $("#payment_terms").prop("readonly", true);


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
          $("#customer_ship_to_pan").val(dcresponse.ship_details.ship_pan);


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
              await addRowBtn("2749", "deliverychallandit");

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
                  lastRow.find(`#poduct_description_${j}`).attr("readonly", "readonly");
                  lastRow.find(`#poduct_description_${j}1`).val(res.product_name);
                  lastRow.find(`#poduct_description_${j}`).val(res.prod_name);
                  lastRow.find(`#product_hsn_${j}`).val(res.hsn_code);

                  lastRow.find(`#product_hsn_${j}`).prop("readonly", true);
                  if ($("#delivery_challan_type").val() != 2) {
                    lastRow.find(`#unit_price_${j}`).prop("readonly", true);
                    lastRow.find(`#unit_price_${j}`).val(res.basic_price);
                  }
                  if (res.igst_per != null || res.igst_per != "")
                    lastRow.find(`#gst_rate_${j}`).val(res.igst_per);
                  if ((res.cgst_per != null || res.cgst_per != "") && (res.sgst_per != null || res.sgst_per != ""))
                    lastRow.find(`#gst_rate_${j}`).val(res.cgst_per + res.sgst_per);
                  $(".remove-row-btn").addClass("tr-hidden");

                  lastRow.find(`#total_amount_${j}`).prop("readonly", true);
                  lastRow.find(`#gst_rate_${j}`).prop("readonly", true);
                  lastRow.find(`#gst_amount_${j}`).prop("readonly", true);
                  lastRow.find(`#invoice_sub_total_${j}`).prop("readonly", true);
                  // lastRow.find(`#total_invoice_amt_${j}`).prop("readonly", true);
                  // lastRow.find(`#total_invoice_amount_words_${j}`).prop("readonly", true);

                  lastRow.find(`#product_qty_${j}`).removeClass("NU~O").addClass("NU~M");

                  // Update the currentRow index after appending and updating
                  currentRow = rowIndex;
                } else {
                  // console.error(`Ship Location input not found for ID: #ship_to_location_${j}`);
                  console.error("error fetching shiping adresses");
                }
                $('#productTable2749').find('td:has(button.remove-row-btn.tr-hidden)').remove();
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
            if(response.data.alternative_phone !=0)
              $("#material_receiver_alt_contact_number").val(response.data.alternative_phone);
            $("#material_receiver_email").val(response.data.email);
            toggleIconsBasedOnReadonly('material_receiver_name');

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
  $(document).on('input', '.product_qty, .unit_price', function () {
    const $row = $(this).closest('.product-row');
    calculateRow($row);
    calculateInvoiceTotals();
  });

  function calculateRow($row) {
    const qty = parseFloat($row.find('.product_qty').val()) || 0;
    const price = parseFloat($row.find('.unit_price').val()) || 0;
    const gstRate = parseFloat($row.find('.gst_rate').val()) || 0;
    // alert(gstRate);
    // const igstRate = parseFloat($row.find('.igst_rate').val()) || 0;
    // const cgstRate = parseFloat($row.find('.igst_rate').val()) || 0;
    // const sgstRate = parseFloat($row.find('.igst_rate').val()) || 0;

    const totalAmount = qty * price;

    let gstAmount = 0;
    // if (igstRate !== null && igstRate !== 0) {
    //   // IGST is applicable
    //   gstAmount = (totalAmount * igstRate) / 100;
    // }
    // else
    if (gstRate !== null && gstRate !== 0) {
      // IGST is applicable
      gstAmount = (totalAmount * gstRate) / 100;
    }
    // else {
    //   gstAmount = (totalAmount * (sgstRate + cgstRate)) / 100;
    // }
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

    $('#total_invoice_amt').val(totalInvoiceAmount.toFixed(2));
    console.log(numberToWords(totalInvoiceAmount));
    $('#total_invoice_amount_words').val(numberToWords(totalInvoiceAmount));
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


  /////////////////////FOC PART ///////////////////////
  var targetNodefoc = document.getElementById("foc_number1");
  var observerfoc = new MutationObserver(function (mutationsListfoc) {
    for (var mutationfoc of mutationsListfoc) {
      if (
        mutationfoc.type === "attributes" &&
        mutationfoc.attributeName === "value"
      ) {
        console.log("foc_number1 value changed to:", targetNodefoc.value);

        getfocdetail(targetNodefoc.value);
      }
    }
  });

  // Configuration for the observer (observe attribute changes)
  var configfoc = { attributes: true };
  observerfoc.observe(targetNodefoc, configfoc);

  ///////////get company detail///////
  async function getfocdetail(foc_number) {
    if (foc_number) {
      data = {
        foc_number: foc_number,
        _csrf: $("#csrfToken").val(),
      };

      try {
        const dcresponse = await $.ajax({
          type: "GET",
          url: "getfocdetail",
          data: data,
          dataType: "json",
        });
        // Check if the data object exists and contains 'first_name'
        if (dcresponse && dcresponse.data) {
          $("#customer_bill_to_name").prop("readonly", true);
          $("#customer_bill_to_address").prop("readonly", true);
          $("#customer_ship_to_name").prop("readonly", true);
          $("#customer_ship_to_address").prop("readonly", true);
          $("#material_receiver_name").prop("readonly", true);
          $("#material_receiver_contact_number").prop("readonly", true);
          toggleIconsBasedOnReadonly('material_receiver_name');

          $("#customer_bill_to_name").val(dcresponse.data.contact_name);
          $("#customer_bill_to_address").val(dcresponse.data.address);
          $("#customer_ship_to_name").val(dcresponse.data.contact_name);
          $("#customer_ship_to_address").val(dcresponse.data.address);
          $("#material_receiver_name1").val(dcresponse.data.customer_name);
          $("#material_receiver_name").val(dcresponse.data.contact_name);
          $("#material_receiver_contact_number").val(dcresponse.data.mobile_number);
          $("#material_receiver_alt_contact_number").val('');
          $("#material_receiver_email").val('');

          if (dcresponse && dcresponse.product_details) {
            console.log(dcresponse.product_details);

            $('#productTable2749 tbody').html('');

            $("#loading-overlay").css('display', 'grid');

            // Initialize currentRow to keep track of the last appended row
            let currentRow = '';

            // Loop through each product in the response
            for (let i = 0; i < dcresponse.product_details.length; i++) {
              const j = i + 1;
              const res = dcresponse.product_details[i];
              // Wait for the row to be added before proceeding with updates
              await addRowBtn("2749", "deliverychallandit");

              // Find the last row and get its index
              const foctbody = $('#productTable' + 2749 + ' tbody');
              const foclastRow = foctbody.find('tr:last');
              const focrowIndex = foclastRow.index();

              // Check if this row is new and not already updated
              if (foclastRow.length > 0 && currentRow !== focrowIndex) {
                console.log(`Processing row ${j} (Row Index: ${focrowIndex})`);

                // Find the product_name input element
                const dcproductNameInput = foclastRow.find(`#poduct_description_${j}`);

                if (dcproductNameInput.length > 0) {
                  // Update the input values for the row
                  foclastRow.find(`#poduct_description_${j}`).attr("readonly", "readonly");
                  foclastRow.find(`#poduct_description_${j}1`).val(res.product_name);
                  foclastRow.find(`#poduct_description_${j}`).val(res.product_ori_name);

                  foclastRow.find(`#product_qty_${j}`).val(res.product_qty);
                  foclastRow.find(`#product_qty_${j}`).prop("readonly", true);

                  foclastRow.find(`#product_hsn_${j}`).val(res.product_hsn);
                  foclastRow.find(`#product_hsn_${j}`).prop("readonly", true);

                  foclastRow.find(`#unit_price_${j}`).prop("readonly", true);
                  foclastRow.find(`#unit_price_${j}`).val(res.unit_price);

                  foclastRow.find(`#total_amount_${j}`).prop("readonly", true);
                  foclastRow.find(`#total_amount_${j}`).val(res.base_price);

                  foclastRow.find(`#gst_rate_${j}`).val(res.gst_rate);
                  foclastRow.find(`#gst_rate_${j}`).prop("readonly", true);

                  foclastRow.find(`#gst_amount_${j}`).prop("readonly", true);
                  foclastRow.find(`#gst_amount_${j}`).val(res.gst_amount);

                  foclastRow.find(`#invoice_sub_total_${j}`).prop("readonly", true);
                  foclastRow.find(`#invoice_sub_total_${j}`).val(res.total_amount);

                  $(".remove-row-btn").addClass("tr-hidden");

                  foclastRow.find(`#total_invoice_amt_${j}`).prop("readonly", true);
                  foclastRow.find(`#total_invoice_amount_words_${j}`).prop("readonly", true);


                  // Update the currentRow index after appending and updating
                  currentRow = focrowIndex;
                } else {
                  // console.error(`Ship Location input not found for ID: #ship_to_location_${j}`);
                  console.error("error fetching shiping adresses");
                }
                calculateInvoiceTotals();
                $('#productTable2749').find('td:has(button.remove-row-btn.tr-hidden)').remove();
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

    }
  }

  toggleIconsBasedOnReadonly('material_receiver_name');
  function toggleIconsBasedOnReadonly(fieldname) {

    if (fieldname != '') {
      const $input = $(`#${fieldname}`);

      if ($input.length && $input.prop("readonly")) {
        $(`svg[data-fieldname="${fieldname}"]`).hide();
      } else {
        $(`svg[data-fieldname="${fieldname}"]`).show();
      }
    }
  }

  /////////////////////FOC PART END ///////////////////////

  // AS PER SHIP BY HIDE SHOW FIELDS\

  // /SHIP BY WAREHOUSE///////////////////////////////////
    //toggle function end
  var targetNodeware = document.getElementById("warehouse_location_name1");
  var observerware = new MutationObserver(function (mutationsListware) {
    for (var mutationware of mutationsListware) {
      if (
        mutationware.type === "attributes" &&
        mutationware.attributeName === "value"
      ) {
        console.log("warehouse_location_name value changed to:", targetNodeware.value);

        getwarehousedetail(targetNodeware.value);
      }
    }
  });

  // Configuration for the observer (observe attribute changes)
  var configware = { attributes: true };
  observerware.observe(targetNodeware, configware);

  ///////////get company detail///////
  function getwarehousedetail(warehouse_location_name) {
    if (warehouse_location_name) {
      data = {
        dc_location_id: warehouse_location_name,
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
            // $("#warehouse_location_name").val(response.data.warehouse_name);
            // $("#warehouse_location_name1").val(response.data.warehouse_id);
            $("#warehouse_address").val(response.data.address);
            $("#warehouse_city").val(response.data.city_name);
            $("#warehouse_state").val(response.data.state);
            $("#warehouse_pin_code").val(response.data.pincode);
            $("#warehouse_gstin_no").val(response.data.gstn);
            $("#warehouse_state_code").val(response.data.statecode);

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


  
  var targetNodeloc = document.getElementById("vendor_location_name1");
  var observerloc = new MutationObserver(function (mutationsListloc) {
    for (var mutationloc of mutationsListloc) {
      if (
        mutationloc.type === "attributes" &&
        mutationloc.attributeName === "value"
      ) {
        console.log("vendor_location_name1 value changed to:", targetNodeloc.value);

        getvendorlocationdetail(targetNodeloc.value);
      }
    }
  });

  // Configuration for the observer (observe attribute changes)
  var configloc = { attributes: true };
  observerloc.observe(targetNodeloc, configloc);

  ///////////get company detail///////
  function getvendorlocationdetail(vendor_location_name) {
    if (vendor_location_name) {
      data = {
        vendor_location_name: vendor_location_name,
        _csrf: $("#csrfToken").val(),
      };

      $.ajax({
        type: "GET",
        url: "getvendorlocationdetail",
        // async:false,
        data: data,
        success: function (response) {

          // Check if the data object exists and contains 'first_name'
          if (response && response.data) {
            $("#vendor_address").val(response.data.address);
            $("#vendor_city").val(response.data.city_name);
            $("#vendor_state").val(response.data.state_value);
            $("#vendor_pin_code").val(response.data.pincode);
            $("#vendor_gstin_no").val(response.data.gst_number);
            $("#vendor_state_code").val(response.data.state);

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
  $(document).on("change", "#ship_by", function () {
    togglewarehouseorvendor($(this).val());    
  });
  function togglewarehouseorvendor(value){
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

    // Hide & clear all first
   
    

    // Show only the relevant fields
    if (value == "1") {
        $(warehouse_fields.join(",")).show();
        hideAndClear(vendor_fields);
    } 
    else if (value == "2") {
        $(vendor_fields.join(",")).show();
         hideAndClear(warehouse_fields);
    }
  }
  //EDN SHIP BY CODE FOR WAREHOUSE///////////////////////
});