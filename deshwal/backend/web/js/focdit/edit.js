$(document).ready(function () {

var stageSelect = document.getElementById("stage");
 const modeInput = document.getElementById("mode");
  if (modeInput && modeInput.value === "Create") {
  $("#stage").val("1").trigger("change");
    setTimeout(() => {
      flatpickr("#date", {
        defaultDate: new Date(),
        dateFormat: "d-m-Y"
      });
    }, 500);
    addRowBtn("2765", "focdit");
  }
  else
  {
    if ($("#stage").val() > 1) {
        $("#submit_for_approval").prop("disabled", true);
      }
  }

  
  if (stageSelect) {
    // Get the updated stage value
    const stage = parseInt($("#stage").val() || "0");

    // Disable all options except current stage and conditional "8"
    const options = stageSelect.options;
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

    $(document).on("change", "#state", function () {
    data = { state: $(this).val(), _csrf: $("#csrfToken").val() };
    // alert(data);
    getcity(this);
  });

  function getcity(thisobj) {
    // alert(thisobj.value);
    const state = thisobj.value;
    console.log(state);
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    // Reset dropdowns

    const cityDropdown = $("#city")
      .empty()
      .append('<option value="">Select</option>');

    if (state) {
      $.ajax({
        type: "POST",
        url: "getcity",
        data: { state: state, _csrf: csrfToken },
        dataType: "json",
        success: function (response) {
          console.log(response.statecode);
          if (response.status === "success") {
            response.categories.forEach((city) => {
              cityDropdown.append(
                `<option value="${city.id}">${city.name}</option>`
              );
            });
            cityDropdown.trigger("change"); // Update Select2 dropdown
          } else {
            alert(response.message);
          }
        },
        error: function (xhr) {``
          console.error(xhr);
          alert("Error occurred while fetching categories. Please try again.");
        },
      });
    }
  }
 
// fill compnay details from warehouse
  var targetNode1 = document.getElementById("customer_name1");
  var observer1 = new MutationObserver(function (mutationsList1) {
    for (var mutation1 of mutationsList1) {
      if (
        mutation1.type === "attributes" &&
        mutation1.attributeName === "value"
      ) {
        console.log("customer_name1 value changed to:", targetNode1.value);

        getcustomerdetail(targetNode1.value);
      }
    }
  });

  // Configuration for the observer (observe attribute changes)
  var config1 = { attributes: true };
  observer1.observe(targetNode1, config1);

  function getcustomerdetail(customer_name1) {
    if (customer_name1) {
      data = {
        customer_name: customer_name1,
        _csrf: $("#csrfToken").val(),
      };

      $.ajax({
        type: "GET",
        url: "getcustomerdetail",
        // async:false,
        data: data,
        success: function (response) {
          console.log(response.data);
          // Check if the data object exists and contains 'first_name'
          if (response && response.data) {
            // $("#address").val(response.data.address);
            // $("#city").val(response.data.city);
            // $("#state").val(response.data.state);
            // $("#pin_code").val(response.data.pin_code);
            $("#mobile_number").val(response.data.mobile);

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

  // fill products
    // Initialize observers for existing and dynamic inputs
  observeMatchingInputs();
  monitorDynamicInputs();
  // Function to observe all matching inputs
  function observeMatchingInputs() {
    // Match inputs with ID pattern 'productid_*1'
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

    console.log("Monitoring dynamic inputs for pattern: product_name_1");
  }

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
            getproductdetail(trid, `${inputElement.value}`);
            // checkQuantityMatch();
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
  // var targetNode = document.getElementById("product_name_11");
  // var observer = new MutationObserver(function (mutationsList) {
  //   for (var mutation of mutationsList) {
  //     if (
  //       mutation.type === "attributes" &&
  //       mutation.attributeName === "value"
  //     ) {
  //       console.log("customer_name1 value changed to:", targetNode.value);
  //         const nearestTr = inputElement.closest("tr");
  //         if (nearestTr) {
  //           trid = nearestTr.id;
  //           console.log("Nearest <tr> ID:", nearestTr.id);
  //           // getproductdata(trid, `${inputElement.value}`);
  //           getproductdetail(trid, `${inputElement.value}`);
  //           // checkQuantityMatch();
  //         } else {
  //           nearestTr.id = "";
  //           console.log("No <tr> ancestor found");
  //         }
        
  //     }
  //   }
  // });

  // // Configuration for the observer (observe attribute changes)
  // var config = { attributes: true };
  // observer.observe(targetNode, config);

  function getproductdetail(trid,product_name) {

    if (product_name) {
      trid= $.trim(trid)
      startLoading();
      
      data = {
        product_name: product_name,
        _csrf: $("#csrfToken").val(),
      };

      $.ajax({
        type: "GET",
        url: "getproductdetail",
        // async:false,
        data: data,
        success: function (response) {
           $('#product_discription_' + trid).val('');
           $('#product_hsn_' + trid).val('');
           $('#product_qty_' + trid).val('');
           $('#unit_price_' + trid).val('');
           $('#base_price_' + trid).val('');
           $('#gst_rate_' + trid).val('');
           $('#gst_amount_' + trid).val('');
           $('#total_amount_' + trid).val('');
          // Check if the data object exists and contains 'first_name'
          if (response && response.data) {
            $("#product_discription_" + trid).val(response.data.product_description);
            $("#product_hsn_" + trid).val(response.data.hsn_code);
            $("#gst_rate_" + trid).val(response.data.gst_percentage);
            stopLoading();

          } else {
            console.log("Invalid response format or missing data");
             stopLoading();
          }
        },
        error: function (data) {
          // if error occured
           stopLoading();

          alert("Error occured.please try again");
        },
        dataType: "json",
      });
    }
  }

  //end get products detail


  /////////do calculation /////////////////
  $(document).on('input', '.product_qty, .unit_price, .gst_rate', function () {
    // Get the row (tr) this input belongs to
    var $row = $(this).closest('tr');
    
    // Get values from inputs
    var qty = parseFloat($row.find('.product_qty').val()) || 0;
    var unitPrice = parseFloat($row.find('.unit_price').val()) || 0;
    var gstRate = parseFloat($row.find('.gst_rate').val()) || 0;

    // Calculate Base Price
    var basePrice = qty * unitPrice;

    // Calculate GST Amount
    var gstAmount = basePrice * gstRate / 100;

    // Calculate Total Amount
    var totalAmount = basePrice + gstAmount;

    // Set calculated values back to fields
    $row.find('.base_price').val(basePrice.toFixed(2));
    $row.find('.gst_amount').val(gstAmount.toFixed(2));
    $row.find('.total_amount').val(totalAmount.toFixed(2));
});

  ////////////end calculations ////////////

});
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
      url: "approvefocdit",
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
        url: "approvefocdit",
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