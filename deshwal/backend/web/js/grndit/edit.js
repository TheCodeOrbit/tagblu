$(document).ready(function () {
  var newURL = window.location.href;

  //hide add more button on load
  $(".add-more-records").addClass("tr-hidden");
  $('.add-more-records').prop('disabled', true);


  const modeInput = document.getElementById("mode");
  if (modeInput && modeInput.value === "Create") {

    // initialize stage with draft
    $("#status").val("3").trigger("change");

    //set today date
    // Get today's date in YYYY-MM-DD format
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, "0");
    var mm = String(today.getMonth() + 1).padStart(2, "0"); // Months are zero-indexed
    var yyyy = today.getFullYear();

    var todaydate = dd + "-" + mm + "-" + yyyy; // Format the date as YYYY-MM-DD
    //alert('"'+todaydate+'"');

    $("#grn_date").val(todaydate);
    setTimeout(() => {
      flatpickr("#grn_date", {
        defaultDate: new Date(),
        dateFormat: "d-m-Y",
      });


    }, 500); // Waits for 600 milliseconds (1/2 seconds)


  }
  var status = $("#status").val();
  // if(so_stage == 1)
  //$(".section-send_for_second_approval").addClass("tr-hidden");

  // Create a MutationObserver to detect changes to the input opportunity
  var targetNode = document.getElementById("purchase_order_number1");
  var observer = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
      if (mutation.type === "attributes" && mutation.attributeName === "value") {
        console.log("PO value changed to:", targetNode.value);
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
  if (targetNode)
    observer.observe(targetNode, config);

  /////////////////////////get product detail////////////
  async function getproductdetail() {
    const data = {
      purchase_order_number: $("#purchase_order_number1").val(),
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
        var subtotal = 0;
        var totalgst = 0;
        var total_cgst = 0;
        var total_sgst = 0;
        var total_igst = 0;
        var total = 0;
        const tbody = $('#productTable' + 2725 + ' tbody');
        tbody.empty();

        // Loop through each product in the response
        for (let i = 0; i < response.data.length; i++) {
          const j = i + 1;
          const res = response.data[i];

          // Wait for the row to be added before proceeding with updates
          await addRowBtn("2725", "grndit");

          // Find the last row and get its index

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
              lastRow.find(`#product_name_${j}1`).val(res.product_id);
              lastRow.find(`#product_name_${j}`).val(res.product_name);
              lastRow.find(`#already_received_${j}`).val(res.already_received);
              lastRow.find(`#product_description_${j}`).val(res.product_description);
              lastRow.find(`#hsn_code_${j}`).val(res.hsn_code);
              lastRow.find(`#po_qty_${j}`).val(res.qty);
              lastRow.find(`#basic_cost_price_${j}`).val(res.basic_cost_price);
              lastRow.find(`#cgst_percentage_${j}`).val(res.cgst);
              lastRow.find(`#sgst_percentage_${j}`).val(res.sgst);
              lastRow.find(`#igst_percentage_${j}`).val(res.igst);
              lastRow.find(`#total${j}`).val(res.product_total);


              // Update the currentRow index after appending and updating
              currentRow = rowIndex;
              // lastRow.find('.remove-row-btn').hide();
              // lastRow.find('.remove-row-btn').prop('disabled', true);
              $(".remove-row-btn").addClass("tr-hidden");




            } else {
              //console.error(`Product Name input not found for ID: #product_name_${j}`);
            }
          }
        }

        $("#loading-overlay").css('display', 'none');

        // After all rows are processed, update the total amount after a delay
        // setTimeout(() => {
        //setTotalAmount();
        // }, 5000);
        populatebarcodeblock(response.data);

      } else {
        console.error("Invalid response format or missing data");
      }
    } catch (error) {
      console.error("Error occurred while fetching product details:", error);
      alert("Error occurred. Please try again.");
    }
  }

  //////////////////////generate barcode/////
  async function populatebarcodeblock(res) {

    var tbody1 = $('#productTable' + 2761 + ' tbody');
    tbody1.empty();  // 🔄 Clear previous rows
    // Initialize currentRow to keep track of the last appended row
    let currentRow = '';
    let globalRowIndex = 1;

    for (let k = 0; k < res.length; k++) {
      const product = res[k];
      let qty = parseInt(product.qty, 10);
      if (isNaN(qty) || qty < 1) continue;

      let product_id = product.product_id;
      let product_name = product.product_name;
      let hsn_code = product.hsn_code;

      for (let l = 0; l < qty; l++) {
        let j = globalRowIndex++;  // <- increment for each row, globally unique

        await addRowBtn("2761", "grndit");

        const lastRow = tbody1.find('tr:last');
        const rowIndex = lastRow.index();

        if (lastRow.length > 0 && currentRow !== rowIndex) {
          console.log(`Processing row ${j} (Row Index: ${rowIndex})`);

          // Ensure that IDs like #product_name_11 exist in the row
          lastRow.find(`#product_name_${j}1`).val(product_id);
          lastRow.find(`#product_name_${j}`).val(product_name);
          lastRow.find(`#hsn_code_${j}`).val(hsn_code);

          currentRow = rowIndex;
          // lastRow.find('.remove-row-btn').hide();
          // lastRow.find('.remove-row-btn').prop('disabled', true);
          $(".remove-row-btn").addClass("tr-hidden");


        }
      }
    }


  }

  /////// on enter press move to next barcode section////
  $(document).on('keydown', '.bar_code', function (e) {
    if (e.key === 'Enter' || e.key === 'Tab') {
      e.preventDefault();

      const currentInput = $(this);
      const currentRow = currentInput.closest('tr');
      const nextRow = currentRow.next('tr');

      if (nextRow.length > 0) {
        // Focus the barcode input in the next row
        nextRow.find('.bar_code').focus();
      }
    }
  });


  //////////////get ship address///////////
  async function getshipaddress() {
    const data = {
      deal_name: $("#purchase_order_number1").val(),
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
        $(`#delivery_entity_name1`).val(response.data.delivery_entitiy_name);
        $(`#delivery_entity_name`).val(response.data.warehouse_name);
        $(`#delivery_location`).val(response.data.warehouse_name);
        $(`#delivery_address`).val(response.data.address);
        $(`#delivery_destination_supply`).val(response.data.state);
        $(`#delivery_state_code`).val(response.data.statecode);
        // $(`#pincode`).val(response.data.pincode);
        $(`#delivery_gst_number`).val(response.data.gstn);

        ///vendor detail
        $(`#vendor_name1`).val(response.data.vendor_name);
        $(`#vendor_name`).val(response.data.acc_name);
        $(`#vendor_location1`).val(response.data.location);
        $(`#vendor_location`).val(response.data.location);
        $(`#vendor_address`).val(response.data.vendor_address);
        $(`#vendor_gst_number`).val(response.data.gst_number);
        $(`#vendor_state_code`).val(response.data.state_code);
        $(`#source_of_supply`).val(response.data.source_of_supply);


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

  //////////////get bill to address///////////
  async function getbilladdress() {
    const data = {
      deal_name: $("#purchase_order_number1").val(),
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
        $(`#entity_name1`).val(response.data.delivery_entitiy_name);
        $(`#entity_name`).val(response.data.warehouse_name);
        $(`#entity_location`).val(response.data.warehouse_name);
        $(`#entity_address`).val(response.data.address);
        $(`#destination_of_supply`).val(response.data.state);
        $(`#entity_state_code`).val(response.data.statecode);
        // $(`#pincode`).val(response.data.pincode);
        $(`#entity_gst_number`).val(response.data.gstn);

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

  ///get received qty
  const cpinput = document.querySelectorAll('input[id^="received_qty_"]'); // Select all inputs with IDs starting with "received_qty_"

  $(document).on(
    "change",
    "[id^=received_qty_]",
    function () {
      $("[id^=received_qty_]").each(function () {
        var trid = $(this).attr("id").match(/\d+$/)
          ? $(this).attr("id").match(/\d+$/)[0]
          : "";
        var received_qty = $(this).val();
        if (trid) {
          // Check if the correct elements exist
          var already_received_elem = document.getElementById("already_received_" + trid);
          var po_qty_elem = document.getElementById("po_qty_" + trid);
          var balance_qty_elem = document.getElementById("balance_qty_" + trid);
          // console.log('po_qty_elem:', po_qty_elem); // Log the po_qty element
          // console.log('balance_qty_elem:', balance_qty_elem); // Log the balance_qty element

          if (po_qty_elem && balance_qty_elem) {
            // Get the numeric values
            var po_qty = parseFloat(po_qty_elem.value) || 0;
            var balance_qty = parseFloat(balance_qty_elem.value) || 0;
            var already_received = parseFloat(already_received_elem.value) || 0;

            // Log the values we're using for the calculation
            //console.log(`po_qty: ${po_qty}, balance_qty: ${balance_qty}`);
            // Get the element by ID using jQuery
            const element = $("#received_qty_" + trid);

            // Find the nearest .help-block element (searching up or down the DOM)
            const helpBlock = element.closest('.form-group').find('.help-block').first();
            var bal = po_qty-already_received;
            if (received_qty > bal) {
              document.getElementById("received_qty_" + trid).value = '';
              received_qty=0;

              // Set the message (for example)
              helpBlock.text("Received qty cannot be greater than (PO qty - Already Received) ");
              
            }
            else{
              helpBlock.text("");
           
            }
               // Perform your calculation
            var balance_qty = bal -  received_qty;
            //console.log(`Calculated result for ${trid}: ${result}`); // Log the result
            document.getElementById("balance_qty_" + trid).value = balance_qty;

            //now calculate total amount
            var cgst = document.getElementById("cgst_percentage_" + trid).value;
            var sgst = document.getElementById("sgst_percentage_" + trid).value;
            var igst = document.getElementById("igst_percentage_" + trid).value;
            var basic_cost_price = document.getElementById("basic_cost_price_" + trid).value;
            var subtotal = basic_cost_price * received_qty;
            var totalamount = subtotal + (subtotal * cgst / 100) + + (subtotal * sgst / 100) + (subtotal * igst / 100);
            document.getElementById("total_" + trid).value = totalamount;
          } else {
            //console.log(`Missing po_qty or balance_qty for row with ID: ${trid}`);
          }
        } else {
          // console.log("No ID found for the <tr> element");
        }


      });





    });


    /////////////import barcodes////////////
     $(document).on("click", ".import-btn", function () { 
        let blockid = $(this).data("section");
        $("#data-import-blockid").val(blockid);
        $(".dataimport-error-msg").text("");
        $('#dataimport-file').val("");
    })
    $(document).on("click", "#dataimport-submit", function () {
        let postData = {
            Recordid: $("#dataImportRecordid").val(),
            _csrf: $("#dataImportCsrfToken").val(),
            blockid: $("#data-import-blockid").val(),
        };
        console.log(postData);
        $(".dataimport-error-msg").text("");
        var fileInput = $('#dataimport-file')[0];
        if (!fileInput.files.length) {
            $(".dataimport-error-msg").text('Please select a file to upload.');
            return;
        }
        var file = fileInput.files[0];
        var fileType = file.name.split('.').pop().toLowerCase();
        var maxSize = 1 * 1024 * 1024; // 1MB in bytes

        if (!['xlsx','XLSX', 'xls','XLS'].includes(fileType)) {
            $(".dataimport-error-msg").text('Invalid file type. Please select an Excel file.');
            return;
        }
        if (file.size > maxSize) {
            $(".dataimport-error-msg").text('File size exceeds 1MB. Please upload a smaller file.');
            return;
        }
        var reader = new FileReader();
        reader.onload = function (e) {
            var data = new Uint8Array(e.target.result);
            var workbook = XLSX.read(data, { type: 'array' });
            var sheetName = workbook.SheetNames[0]; // Read first sheet
            var sheet = XLSX.utils.sheet_to_json(workbook.Sheets[sheetName], { header: 1 });
            let datarray = sheet.filter(row => row.length > 0);
            if (datarray.length <= 1) {
                $(".dataimport-error-msg").text("The Excel file appears to be empty or malformed.");
                return;
            }
            console.log("deep",datarray);
            postData["excel_data"] = datarray;
            $("#loading-overlay").css('display', 'grid');
            $.ajax({
                type: "POST",
                url: "importdata",
                data: postData,
                success: function (data) {
                    //$("#loading-overlay").css('display', 'none');
                    if (data.status === "success") {
                        $("#dataimport-submit").remove()
                        $(".dataimport-error-msg").text("Data is uploaded successfully.");
                        location.reload();
                    } else {
                        $("#loading-overlay").css('display', 'none');
                        $(".dataimport-error-msg").text(data.errors || "sometinhg went wrong");
                    }
                },
                error: function (data) {
                    $("#loading-overlay").css('display', 'none');
                    $(".dataimport-error-msg").text("Error occured.please try again");
                },
                dataType: "json",
            });
        };
        reader.readAsArrayBuffer(file);
    });

     $(".remove-row-btn").addClass("tr-hidden");



});
