$(document).ready(function () {

    $(".add-more-records").hide();
 const modeInput = document.getElementById("mode");
  if (modeInput && modeInput.value === "Create") {

    setTimeout(() => {
      flatpickr("#date", {
        defaultDate: new Date(),
        dateFormat: "d-m-Y"
      });
    }, 500);
  }
  else
  {
            $("#company_name").prop("readonly", true);
            $("#company_address").prop("readonly", true);
            $("#company_gstin").prop("readonly", true);
            $("#company_pan").prop("readonly", true);
            $("#contact_number").prop("readonly", true);

            $("#mode_of_transport").prop("readonly", true);
            $("#transporter_name").prop("readonly", true);
            $("#vehicle_docket_number").prop("readonly", true);

            $("#customer_name").prop("readonly", true);
            $("#customer_address").prop("readonly", true);
            $("#customer_gstin").prop("readonly", true);
            $("#customer_pan").prop("readonly", true);
            $("#material_receiver_name").prop("readonly", true);
            $("#material_receiver_contact_num").prop("readonly", true);
            $("#material_receiver_alternate_contact_num").prop("readonly", true);
            $("#material_receiver_email").prop("readonly", true);
            $("#productTable2759 input[id^='product_hsn_']").prop("readonly", true);
            $('#productTable2759 thead th').each(function (index) {
                    if ($(this).text().trim().toLowerCase() === 'action') {
                      // Remove the <th>
                      $(this).remove();
            $('#productTable2759 tbody tr').each(function () {
                  $(this).find('td').eq(index).remove();
                });
                      return false; // exit loop once done
                    }
                  });
                  $(".remove-row-btn").addClass("tr-hidden"); 
  }
// fill compnay details from warehouse
  var targetNode1 = document.getElementById("company_details1");
  var observer1 = new MutationObserver(function (mutationsList1) {
    for (var mutation1 of mutationsList1) {
      if (
        mutation1.type === "attributes" &&
        mutation1.attributeName === "value"
      ) {
        console.log("company_details1 value changed to:", targetNode1.value);

        getcompanydetail(targetNode1.value);
      }
    }
  });

  // Configuration for the observer (observe attribute changes)
  var config1 = { attributes: true };
  observer1.observe(targetNode1, config1);

  function getcompanydetail(company_details) {
    if (company_details) {
      data = {
        company_id: company_details,
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

  //end get comapny detail

  // fill other details from delivery challan
  var targetNode = document.getElementById("dc_number1");
  var observer = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
      if (
        mutation.type === "attributes" &&
        mutation.attributeName === "value"
      ) {
        console.log("dc_number1 value changed to:", targetNode.value);

        getdeliverychallandetail(targetNode.value);
      }
    }
  });

  // Configuration for the observer (observe attribute changes)
  var config= { attributes: true };
  observer.observe(targetNode, config);

    async function getdeliverychallandetail(dc_id) {
    if (dc_id) {
      data = {
        dc_id: dc_id,
        _csrf: $("#csrfToken").val(),
      };

      try {
        const dcresponse = await $.ajax({
          type: "GET",
          url: "getdeliverychallandetail",
          data: data,
          dataType: "json",
        });
        // Check if the data object exists and contains 'first_name'
        if (dcresponse && dcresponse.data) {
          $("#mode_of_transport").prop("readonly", true);
            $("#transporter_name").prop("readonly", true);
            $("#vehicle_docket_number").prop("readonly", true);

            $("#customer_name").prop("readonly", true);
            $("#customer_address").prop("readonly", true);
            $("#customer_gstin").prop("readonly", true);
            $("#customer_pan").prop("readonly", true);
            $("#material_receiver_name").prop("readonly", true);
            $("#material_receiver_contact_num").prop("readonly", true);
            $("#material_receiver_alternate_contact_num").prop("readonly", true);
            $("#material_receiver_email").prop("readonly", true);
            

            $("#mode_of_transport").val(dcresponse.data.mod_of_transport).trigger("change");
            $("#transporter_name").val(dcresponse.data.mrn_full_name);
            $("#vehicle_docket_number").val(dcresponse.data.vehicle_docket_number);

            $("#customer_name").val(dcresponse.data.customer_ship_to_name);
            $("#customer_address").val(dcresponse.data.customer_ship_to_address);
            $("#customer_gstin").val(dcresponse.data.customer_ship_to_gstin);
            $("#customer_pan").val(dcresponse.data.customer_ship_to_pan);
            $("#material_receiver_name1").val(dcresponse.data.material_receiver_name);
            $("#material_receiver_name").val(dcresponse.data.mrn_full_name);
            $("#material_receiver_contact_num").val(dcresponse.data.material_receiver_contact_number);
            $("#material_receiver_alternate_contact_num").val(dcresponse.data.material_receiver_alt_contact_number);
            $("#material_receiver_email").val(dcresponse.data.customer_ship_to_pan);


          if (dcresponse && dcresponse.product_details) {
            $('#productTable2759 tbody').html('');

            $("#loading-overlay").css('display', 'grid');

            // Initialize currentRow to keep track of the last appended row
            let currentRow = '';

            // Loop through each product in the response
            for (let i = 0; i < dcresponse.product_details.length; i++) {
              console.log(dcresponse.product_details);
              const j = i + 1;
              const res = dcresponse.product_details[i];
              // Wait for the row to be added before proceeding with updates
              await addRowBtn("2759", "packinglistdit");
              
              applySelect2();

              // Find the last row and get its index
              const tbody = $('#productTable' + 2759 + ' tbody');
              const lastRow = tbody.find('tr:last');
              const rowIndex = lastRow.index();

              // Check if this row is new and not already updated
              if (lastRow.length > 0 && currentRow !== rowIndex) {
                console.log(`Processing row ${j} (Row Index: ${rowIndex})`);

                // Find the product_name input element
                const dcproductNameInput = lastRow.find(`#product_discription_${j}`);

                if (dcproductNameInput.length > 0) {
                  // Update the input values for the row
                  // lastRow.find(`#product_discription_${j}`).attr("readonly", "readonly");
                  lastRow.find(`#product_discription_${j}1`).val(res.poduct_description);
                  lastRow.find(`#product_discription_${j}`).val(res.product_name);
                  lastRow.find(`#product_qty_${j}`).val(res.product_qty);
                  
                  lastRow.find(`#product_qty_${j}`).prop("readonly", true);
                  lastRow.find(`#product_hsn_${j}`).val(res.product_hsn);
                  
                  lastRow.find(`#product_hsn_${j}`).prop("readonly", true);
                 
                 $('#productTable2759 thead th').each(function (index) {
                    if ($(this).text().trim().toLowerCase() === 'action') {
                      // Remove the <th>
                      $(this).remove();

                      return false; // exit loop once done
                    }
                  });
                  $(".remove-row-btn").addClass("tr-hidden"); 

                  lastRow.find(`#product_qty_${j}`).removeClass("NU~O").addClass("NU~M");

                  // Update the currentRow index after appending and updating
                  currentRow = rowIndex;
                } else {
                  // console.error(`Ship Location input not found for ID: #ship_to_location_${j}`);
                  console.error("error fetching shiping adresses");
                }
                $('#productTable2759').find('td:has(button.remove-row-btn.tr-hidden)').remove();
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

  //end code for other details from delivery challan
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