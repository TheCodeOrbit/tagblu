let weightApplicable = false;
$(document).ready(function () {
  let isWeight = false;
  var newURL = window.location.href;
  var module = "segregation";
  var str = newURL.split(module);
  var action = str[1].split("/")[1].split("?")[0];
  editusrl = str[0] + "segregation/list";
  console.log("action" + action);
  const urlParams = new URLSearchParams(window.location.search);
  const itemid = urlParams.get('itemid'); 
  // startLoading();
  // if(action != "edit")
  $('.savebutton').prop('disabled', true);
  $(".section-total_weight").hide();
  $(".section-matched_weight").hide();
  var data = {
    Recordid: itemid,
    _csrf: $("#csrfToken").val(),
  };
  $.ajax({
    type: "POST",
    url: "getgrndata",
    // async:false,
    data: data,
    success: function (data) {
      if (data.status === "success") {
        // alert(grn_date[2]+"-"+grn_date[1]+"-"+grn_date[0]);
        console.log(data.data.products);
        let grn_date = data.data.createdtime.split(" ")[0].split("-");
        $("#grn_no").val(data.data.grn_no);
        $("#grn_date").val(grn_date[2]+"-"+grn_date[1]+"-"+grn_date[0]);
        $("#total_quantity").val(data.data.received_qty);
        if (data.data.uom && data.data.uom.trim().toLowerCase() === "kg") {
            $(".section-total_weight").show();
            $(".section-matched_weight").show();
            $("#total_weight").val(data.data.total_weight);
            weightApplicable = true;
          } else {
            $(".section-total_weight").hide();
            $(".section-matched_weight").hide();
            weightApplicable = false;
          }
        $("#pickup_id").val(data.data.pickup_no);
        $("#lot_no").val(data.data.lot_number);
      }else {
          alert(data.message); //  SHOW ERROR
      }
      // stopLoading();
    },
    error: function (data) {
      // if error occured

      alert("Error occured.please try again");
      stopLoading();
    },
    dataType: "json",
  });
  /*if (action == "edit") {
    let totalQty = 0;
    let totalWeight = 0;

    $('.qty').each(function () {
      totalQty += parseFloat($(this).val()) || 0;
    });
    
    $('.prod_weight').each(function () {
      totalWeight += parseFloat($(this).val()) || 0;
    });
    $('#matched_quantity').val(totalQty);
    let total_qty_in_grn = $('#total_quantity').val();
    let total_weight_in_grn = $('#total_weight').val();
    if(totalWeight == total_weight_in_grn)
      isWeight = true;
    console.log("TOTAL WEIGHT"+totalWeight);
    if (totalQty == total_qty_in_grn && isWeight) {
      $("#save_as_draft").val("1");
      $('.savebutton').prop('disabled', false);
      $('.add-more-records').prop('disabled', true);
      $('.savedraftbutton').prop('disabled', true);
    } else {
      $("#save_as_draft").val("0");
      $('.savebutton').prop('disabled', true);
      $('.add-more-records').prop('disabled', false);
      $('.savedraftbutton').prop('disabled', false);
    }
  }*/

    if (action === "edit") {
      let totalweight = $("#total_weight").val();
      if(totalweight !== 0 && totalweight !== '')
      {
        $(".section-total_weight").show();
        $(".section-matched_weight").show();
        weightApplicable = true;
      }
    validateGrnForm();
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
            getproductdata(trid, `${inputElement.value}`);
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

  // Initialize observers for existing and dynamic inputs
  observeMatchingInputs();
  monitorDynamicInputs();


  function getproductdata(trid, productid) {
    trid= $.trim(trid)
    startLoading();
    console.log("product_id=>trid" + productid + "=>" + trid);
    var data = {
      Recordid: productid,
      _csrf: $("#csrfToken").val(),
    };
    let blockid = 2644;
    let mainmodule = "segregation";
    let totalRows = $('#productTable' + blockid + ' tr').length;
    let geturl = getAbsoluteUrl();
    let url = geturl + mainmodule + "/getproductlist?blockid=" + blockid + "&cnt_rows=" + totalRows;
    $.ajax({
      type: "POST",
      url: "getproductdetails",
      // url: url,
      // async:false,
      data: data,
      success: function (data) {
        console.log(data);
        
        $("#category_" + trid).val(data.data.category).trigger('change');
        $("#sub_category_" + trid).val(data.data.sub_catagory_id).trigger('change');
        $("#model_no_" + trid).val(data.data.prod_model_id).trigger('change');
        $("#hsn_" + trid).val(data.data.hsn_code);
        $("#make_" + trid).val(data.data.prod_make_id).trigger('change');
        $('#uom_' + trid).val(data.data.uom).trigger('change');
        $('#prod_weight_' + trid).val(data.data.weight_kg);
        stopLoading();
      },
      error: function (data) {
        // if error occured

        alert("Error occured.please try again");
        stopLoading();
      },
      dataType: "json",
    });
  }

  // function getlocationcode(trid, locationfloor) {
    $(document).on('change', 'select.productinput[id^="location_floor_"]', function () {
    let elementId = $(this).attr('id'); // e.g., "location_floor_1"
    let trid = elementId.split('_').pop(); // "1"
    let locationfloor = $(this).val();
    startLoading();
    var data = {
      Recordid: locationfloor,
      _csrf: $("#csrfToken").val(),
    };
    $.ajax({
      type: "POST",
      url: "getlocationcde",
      data: data,
      success: function (data) {
        const locationDropdown = $("#location_code_" + trid)
        .empty()
        .append('<option value="">Select Location</option>');
        data.locations.forEach((location) => {
            locationDropdown.append(
              `<option value="${location.location_code_id}">${location.location_code_value}</option>`
            );
          });
          locationDropdown.trigger("change");
          $("#location_code_" + trid).trigger("change");

        stopLoading();
      },
      error: function (data) {
        alert("Error occured.please try again");
        stopLoading();
      },
      dataType: "json",
    });
  });
  // }

  /*$(document).on('input', '.qty', function () {
    let qty = $(this).val();
    let rowId = $(this).closest('tr').attr('id');
    let total_qty_in_grn = $("#total_quantity").val();
    // let total_weight_in_grn = $('#total_weight').val();
    // You can do something with qty here, like calculation or showing something dynamically
    let totalQty = 0;
    let totalMatchedQty = 0;
    // let totalWeight = 0;
    

    $('.qty').each(function () {
      totalQty += parseFloat($(this).val()) || 0;
    });

    //  $('.prod_weight').each(function () {
    //   totalWeight += parseFloat($(this).val()) || 0;
    // });

    $('#matched_quantity').val(totalQty) || 0;

    // if(totalWeight == total_weight_in_grn)
    //   isWeight = true;

    if (totalQty == total_qty_in_grn ) {
      $("#save_as_draft").val("1");
      $('.savebutton').prop('disabled', false);
      $('.add-more-records').prop('disabled', true);
      $('.savedraftbutton').prop('disabled', true);
    } else {
      $("#save_as_draft").val("0");
      $('.savebutton').prop('disabled', true);
      $('.add-more-records').prop('disabled', false);
      $('.savedraftbutton').prop('disabled', false);
    }

    // alert("from checkQuantityMatch"+total_qty_in_grn+"---"+totalQty);
  });*/

  $(document).on('input change', '.qty', function () {
    validateGrnForm();
  });
  $(document).on('click', '.remove-row-btn', function () {
    /*let total_qty_in_grn = $("#total_quantity").val();
    // You can do something with qty here, like calculation or showing something dynamically
    let totalQty = 0;
    let totalMatchedQty = 0;

    $('.qty').each(function () {
      totalQty += parseFloat($(this).val()) || 0;
    });

    $('#matched_quantity').val(totalQty) || 0;

    if (totalQty == total_qty_in_grn) {
      $("#save_as_draft").val("1");
      $('.savebutton').prop('disabled', false);
      $('.add-more-records').prop('disabled', true);
      $('.savedraftbutton').prop('disabled', true);
    } else {
      $("#save_as_draft").val("0");
      $('.savebutton').prop('disabled', true);
      $('.add-more-records').prop('disabled', false);
      $('.savedraftbutton').prop('disabled', false);
    }*/
        let totalweight = $("#total_weight").val();
            if(totalweight !== 0 && totalweight !== '')
            {
              $(".section-total_weight").show();
              $(".section-matched_weight").show();
              weightApplicable = true;
            }
      validateGrnForm();
    // alert("from checkQuantityMatch"+total_qty_in_grn+"---"+totalQty);
  });

  // $('.singleselect').select2();
  //this is require when product category subcategory are disable
  $('.singleselect').each(function () {
    
    const $select = $(this);

      // Only apply if the select has class 'readonly-dd'
      if ($select.hasClass('readonly-dd')) {
          const $container = $select.next('.select2-container');

          if ($container.length) {
              $container.find('.select2-selection--single').css({
                  'background-color': '#e9e9ef',
                  'cursor': 'not-allowed'
              });

              // Optional: hide clear button
              $container.find('.select2-selection__clear').hide();
          }
      }
  });



  // $(".add-more-records").hide();
  // $(".remove-row-btn").remove()
});

function validateGrnForm() {

    let totalQty = 0;
    let totalWeight = 0;
    let weightRowMismatch = false;
    let zeroQtyFound = false;

    let total_qty_in_grn = parseFloat($("#total_quantity").val()) || 0;
    let total_weight_in_grn = parseFloat($("#total_weight").val()) || 0;

    // reset previous errors
    $('.qty').removeClass('is-invalid');
    $('.qty').each(function () {
        $(this).closest('td').find('.help-block').text('').hide();
    });

    // ---- Calculate total quantity & validate qty ----
    $('.qty').each(function () {
        let qty = parseFloat($(this).val());
        let $td = $(this).closest('td');
        let $help = $td.find('.help-block');

        if (!qty || qty <= 0) {
            zeroQtyFound = true;
            $(this).addClass('is-invalid');
             $(this).val("");
            $help.text('Quantity must be greater than 0').show();
        }

        totalQty += qty || 0;
    });

    $('#matched_quantity').val(totalQty);

    //  Stop if qty error exists
    if (zeroQtyFound) {
        $("#save_as_draft").val("0");
        $('.savebutton').prop('disabled', true);
        $('.add-more-records').prop('disabled', false);
        $('.savedraftbutton').prop('disabled', false);
        return false;
    }

    let isQtyValid = (totalQty === total_qty_in_grn);
    let isWeightValid = true;

    // ---- Weight calculation (KG only) ----
    if (typeof weightApplicable !== "undefined" && weightApplicable) {

        totalWeight = 0;

        $('.qty').each(function () {
            let qty = parseFloat($(this).val()) || 0;
            let rowWeight = parseFloat(
                $(this).closest('tr').find('.prod_weight').val()
            ) || 0;

            if (qty > 0 && rowWeight <= 0) {
                weightRowMismatch = true;
            }

            totalWeight += qty * rowWeight;
        });

        totalWeight = parseFloat(totalWeight.toFixed(2));
        $('#matched_weight').val(totalWeight);

        isWeightValid =
            !weightRowMismatch &&
            Math.abs(totalWeight - total_weight_in_grn) < 0.01;
    } else {
        $('#matched_weight').val(0);
    }

    // ---- FINAL DECISION ----
    let isValid = isQtyValid && isWeightValid;

    $("#save_as_draft").val(isValid ? "1" : "0");
    $('.savebutton').prop('disabled', !isValid);
    $('.add-more-records').prop('disabled', isValid);
    $('.savedraftbutton').prop('disabled', isValid);

    return isValid;
}