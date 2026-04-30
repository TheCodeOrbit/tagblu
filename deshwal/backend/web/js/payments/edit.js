document.addEventListener("DOMContentLoaded", function () {
  // Check if mode is 'Create'
  const modeInput = document.getElementById("mode");
  if (modeInput && modeInput.value === "Create") {
    var souringdeal = $("#sourcing_deal1").val();
    if (souringdeal) {
      getsourcingdetail(souringdeal);
    }
    // initialize stage with draft
    $("#stage").val("1").trigger("change");
    //add row to invoice detail
    // addRowBtn("2637", "payments");
    addRowBtn('2637', 'payments')
    .then((message) => {
      console.log(message); // "Data appended successfully"
    })
    .catch((error) => {
      console.log(error); // "Error occurred while appending data"
    });
  }
});

$(document).ready(function () {
  /////////////create mutation for sourcing deal/////////////////
  // Create a MutationObserver to detect changes to the input vendor account
  var targetNode = document.getElementById("sourcing_deal1");
  var observer = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
      if (
        mutation.type === "attributes" &&
        mutation.attributeName === "value"
      ) {
        console.log("sourcing_deal1 value changed to:", targetNode.value);

        getsourcingdetail(targetNode.value);
      }
    }
  });

  // Configuration for the observer (observe attribute changes)
  var config = { attributes: true };
  observer.observe(targetNode, config);

  ///////////// create mutation for po1 /////////////
  // Create a MutationObserver to detect changes to the input po1
  var targetNodePO = document.getElementById("po1");
  var observerPO = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
      if (
        mutation.type === "attributes" &&
        mutation.attributeName === "value"
      ) {
        console.log("po1 value changed to:", targetNodePO.value);

        getpodetail(targetNodePO.value); // Call your function here
      }
    }
  });

  // Configuration for the observer (observe attribute changes)
  var configPO = { attributes: true };
  observerPO.observe(targetNodePO, configPO);

  var po_id = $("#po1").val();
  // alert('po1 id: ' + po_id); // ✅ Correct
  getpodetail(po_id);
});

/////////get sourcing deal detail///////
/////////////////////////get product detail////////////
function getsourcingdetail(sourcingdeal) {
  // alert('vds');
  data = {
    sourcingdeal: sourcingdeal,
    _csrf: $("#csrfToken").val(),
  };

  $.ajax({
    type: "POST",
    url: "getsourcingdetail",
    // async:false,
    data: data,
    success: function (response) {
      console.log(response); // Log the entire response to check its structure

      // Check if the data object exists and contains 'first_name'
      if (response && response.data) {
        $("#account_name").val(response.data.acc_name);
        $("#sourcing_deal_stage").val(response.data.stage_value);
        $("#bank_name").val(response.data.bank_names);
        $("#account_number").val(response.data.account_number);
        $("#swift_code").val(response.data.bank_swift_code);
        $("#bank_account_name").val(response.data.account_name);
        $("#bank_idfc_code").val(response.data.bank_ifsc_code);
        // code added by ptpatel on date 17-11-2025 for point 101 of v11 sheet 
        $("#sourcing_deal_name").val(response.data.deal_name);

        $("#payment_bank_name").val(response.data.bank_names);
        $("#payment_account_number").val(response.data.account_number);
        $("#payment_swift_code").val(response.data.bank_swift_code);
        $("#payment_account_name").val(response.data.account_name);
        $("#idfc_code").val(response.data.bank_ifsc_code);
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

var poTotalAmount = 0; // Store PO total here for validation use

function getpodetail(po) {
  // alert('vds');
  data = {
    po: po,
    _csrf: $("#csrfToken").val(),
  };

  $.ajax({
    type: "POST",
    url: "getpodetail",
    // async:false,
    data: data,
    success: function (response) {
      console.log(response); // Log the entire response to check its structure

      // Check if the data object exists and contains 'first_name'
      if (response && response.data) {
        poTotalAmount = parseFloat(response.data.total_amount || 0); // Save PO total amount
        console.log("PO Total Amount:", poTotalAmount);
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

///////calculate total amount///////////
// code by bhavitha
$(document).on(
  "change",
  "[id^=amount_],[id^=cgst_],[id^=sgst_],[id^=igst_],[id^=tcs_amount_],[id^=payment_amount_],[id^=tds_amount_]",
  function () {
    calculateAndValidateTotals();
  }
);

function calculateAndValidateTotals() {
  let isValidpay = true;
  var total = 0;
  console.log("POAA"+poTotalAmount);
  $("[id^=amount_]").each(function () {
  var totalamt = 0;

    var suffix = $(this).attr("id").match(/\d+$/)
      ? $(this).attr("id").match(/\d+$/)[0]
      : "";
    var amount = parseFloat($(`#amount_${suffix}`).val()) || 0;
    var cgst = parseFloat($(`#cgst_${suffix}`).val()) || 0;
    var sgst = parseFloat($(`#sgst_${suffix}`).val()) || 0;
    var igst = parseFloat($(`#igst_${suffix}`).val()) || 0;
    var tcs_amount = parseFloat($(`#tcs_amount_${suffix}`).val()) || 0;
    totalamt += amount + tcs_amount + cgst + sgst + igst;
    total += totalamt;

    var totalField = $(`#total_amount_${suffix}`);
    var totalError = totalField.closest(".form-group").find(".help-block");

    // Reset previous error
    totalError.text("");
    totalField.removeClass("error");
    // totalField.removeClass("is-invalid");

    // Set value
    totalField.val(totalamt.toFixed(2));

    // Show error if invalid
    if (poTotalAmount > 0 && total > poTotalAmount) {
      totalError.text(
       // `Invoice Total Amount should not be greater than PO Total Amount (${poTotalAmount}).`
       `The Invoice Total Amount cannot exceed the Total Amount of the Purchase Order (${poTotalAmount}).`
      );
      // totalField.addClass("is-invalid");
      totalField.addClass("error");
  
      $(`#total_amount_${suffix}`).focus();
      // isValidpay = false;
      $(".savebutton").prop("disabled", true);
    }
    else
    {
      $(".savebutton").prop("disabled", false);
    }
  });
  
  var paymentTotal = 0;
  $("[id^=payment_amount_]").each(function() {
    var suffix = $(this).attr("id").match(/\d+$/) ? $(this).attr("id").match(/\d+$/)[0] : "";
    var paymentAmt = parseFloat($(`#payment_amount_${suffix}`).val()) || 0;
    var tdsAmt = parseFloat($(`#tds_amount_${suffix}`).val()) || 0;
    paymentTotal += (paymentAmt + tdsAmt);
    console.log(`Row ${suffix}: Payment=${paymentAmt}, TDS=${tdsAmt}, Subtotal=${paymentAmt + tdsAmt}`);
  });
  $('#total_payment_done').val(paymentTotal.toFixed(2));
  console.log("Total Payment Done (Payment + TDS):", paymentTotal);
  
  setotal();
  return isValidpay;
}



$(document).on("change", "[id^=payment_amount_]", function () {
  var total = 0;

  $("[id^=payment_amount_]").each(function () {
    var suffix = $(this).attr("id").match(/\d+$/)
      ? $(this).attr("id").match(/\d+$/)[0]
      : "";
    var amount = parseFloat($(`#payment_amount_${suffix}`).val()) || 0;

    total += amount;
    // alert(total);
  });
  $(`#total_payment_done`).val(total.toFixed(2));

  setbalance();
});
function setotal() {
  var total_invoice_amount = 0;
  $("[id^=total_amount_]").each(function () {
    var total_amount = parseFloat($(this).val()) || 0;
    total_invoice_amount += total_amount;

    $(`#total_invoice_amount`).val(total_invoice_amount.toFixed(2));
    // alert(total_invoice_amount);
    setbalance();
  });
  resrictRequestamt();
}
function setbalance() {
  var total_invoice_amount = parseFloat($("#total_invoice_amount").val()) || 0;

  // var totalField = $(`#total_invoice_amount`);
  // var totalError = totalField.closest(".form-group").find(".help-block");

  // // Reset previous error
  // totalError.text("");
  // totalField.removeClass("is-invalid");

  // // Set value
  // totalField.val(total.toFixed(2));

  // // Show error if invalid
  // if (poTotalAmount > 0 && total_invoice_amount > poTotalAmount) {
  //   totalError.text(
  //     `Invoice Total Amount should not be greater than PO Total Amount (${poTotalAmount}).`
  //   );
  //   totalField.addClass("is-invalid");
  //   const $form = $("#pristine-valid-example");
  // }

  var total_payment_done = parseFloat($("#total_payment_done").val()) || 0;
  var bal = total_invoice_amount - total_payment_done;
  $(`#balance_amount`).val(bal.toFixed(2));
}
//////////////
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
    url: "approvepayments",
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
      url: "approvepayments",
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
var submit_approval = document.getElementById("submit_approval");
if (submit_approval) {
  if (submit_approval.checked) {
    // Checkbox is checked
    $("#submit_approval").prop("disabled", true);
  } else {
    $("#submit_approval").prop("disabled", false);
  }
}
/////////on change stage check utr no.//////////////
var stageold = $("#stage option:selected").val();

$("#stage").change(function () {
  stage = $("#stage option:selected").val();
  var mode = $("#mode").val();
  // alert(mode);
  if (
    (stage == 1 || stage == 2 || stage == 3 || stage == 4 || stage == 5) &&
    stage != stageold &&
    mode != "Create"
  ) {
    alert("You can't select this stage manually");
    $("#stage").val(stageold).trigger("change");
  }
  if (mode == "Create" && stage != 1) {
    alert("You can't select this stage manually");
    $("#stage").val("1").trigger("change");
  }
  if (stage == 6 || stage == 7) {
    //check if utr is added and total paymnet done cannot be zero
    var total_payment_done = $("#total_payment_done").val() || 0;
    if (total_payment_done == 0 || total_payment_done == "") {
      alert("Total Payment done can't be Zero. Please add payment.");
      $("#stage").val(stageold).trigger("change");
    } else {
      // Loop through all elements with the class "utr_cheque"
      $(".utr_cheque").each(function () {
        var $element = $(this); // Current element in the loop

        // Check if the element is visible
        if ($element.is(":visible")) {
          // Check if the element is not empty (check value for input/textarea)
          if ($element.val().trim() !== "") {
            // console.log('Element is visible and not empty:', $element);
          } else {
            // console.log('Element is visible but empty:', $element);
            alert("UTR number is mandatory. Please add payment Details.");
            $("#stage").val(stageold).trigger("change");
          }
        } else {
          // console.log('Element is not visible:', $element);
          alert("UTR number is mandatory. Please add payment Details.");
          $("#stage").val(stageold).trigger("change");
        }
      });
    }
  }
});

/////////on change payment type set po mandatory///////////
var payment_type = $("#payment_type option:selected").val();
makepomandate(payment_type);
$("#payment_type").change(function () {
  var payment_type = $("#payment_type option:selected").val();
  //alert(payment_type);
  makepomandate(payment_type);
});
function makepomandate(payment_type) {
  if (payment_type == 1) {
    //po non mandatory
    $("#po").addClass("V~O");
    $("#po").removeClass("V~M");

    var errorElement = $("#po").closest(".form-group").find(".help-block");
    errorElement.html(""); // Replace errorMessage with the actual message
  } else if (payment_type != "") {
    //po mandatory
    var po = $("#po1").val();
    $("#po").addClass("V~M");
    $("#po").removeClass("V~O");
    var errorElement = $("#po").closest(".form-group").find(".help-block");
    if (po == "") errorElement.html("This Field is Manadatory"); // Replace errorMessage with the actual message
  }
}
/////////////requested_amount cannot be greater than total_invoice_amount///////
$("#requested_amount").change(function () {
  resrictRequestamt();
});
function resrictRequestamt() {
  var requested_amount = parseFloat($("#requested_amount").val()) || 0;
  var total_invoice_amount = parseFloat($("#total_invoice_amount").val()) || 0;
  var errorElement = $("#requested_amount")
    .closest(".form-group")
    .find(".help-block");

  if (requested_amount > total_invoice_amount) {
    errorElement.html(
      "Requested Amount cannot be greater than Total Invoice Amount"
    );
    alert(errorElement.html());
    $("#requested_amount").val("");
    $("#requested_amount").focus();
  } else {
    errorElement.html("");
  }
}


//code added by ptpatel on date 17042025
document.addEventListener("DOMContentLoaded", function () {
  var totalInvoiceAmount = parseFloat($("#total_invoice_amount").val());
  var totalPaymentDone = parseFloat($("#total_payment_done").val());

  if (totalInvoiceAmount !== totalPaymentDone) {
      const stageSelect = document.getElementById("stage");
      const options = stageSelect.options;

      for (let i = 0; i < options.length; i++) {
          if (options[i].value === "6") { // Payment Transferred
              options[i].disabled = true;
              options[i].text += " (Disabled due to total invoice and total payment amount are mismatch)";
              break;
          }
      }
  }
  else
  {
    const stageSelect = document.getElementById("stage");
      const options = stageSelect.options;

      for (let i = 0; i < options.length; i++) {
          if (options[i].value === "6") { // Payment Transferred
              options[i].disabled = false;
              options[i].text;
              break;
          }
      }
  }
});

//end code added by ptpatel on date 17042025
