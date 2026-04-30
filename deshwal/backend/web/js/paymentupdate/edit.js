$(document).ready(function () {

  let actualBalanceAmount = 0;
  const modeInput = document.getElementById("mode");
  if (modeInput && modeInput.value === "Create") {
    setTimeout(() => {
      flatpickr("#invoice_date", {
        defaultDate: new Date(),
        dateFormat: "d-m-Y"
      });
    }, 500);
  }
  else
  {
    actualBalanceAmount = $("#balance_amount").val() || 0;
  }

  var targetNode = document.getElementById("so_number1");
  var observer = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
      if (mutation.type === "attributes" && mutation.attributeName === "value") {
        getinvoicenonvendorname(targetNode.value);
      }
    }
  });

  var config = { attributes: true };
  observer.observe(targetNode, config);

  function getinvoicenonvendorname(recordId) {
    if (recordId) {
      const data = {
        recordId: recordId,
        _csrf: $("#csrfToken").val(),
      };

      $.ajax({
        type: "post",
        url: "getinvoicenonvendorname",
        data: data,
        success: function (response) {
          if (response && response.data) {
            // console.log("payresponse", response.data);

            $("#invoice_number").val(response.data.invoice_number);
            $("#invoice_amount").val(response.data.invoice_amount);
            $("#vendor_name1").val(response.data.vendor_name);
            $("#vendor_name").val(response.data.acc_name);

            actualBalanceAmount = parseFloat(response.data.balance_amount) || 0;

            // Update balance field
            $("#balance_amount").val(actualBalanceAmount.toFixed(2));
          } else {
            console.log("Invoice data not available");
          }
        }
      });
    }
  }

  $(document).on("blur", "#payment_received_amount", function () {
    const received = parseFloat($(this).val()) || 0;
    // const balance = parseFloat($("#balance_amount").val()) || 0;
    const balance = actualBalanceAmount
    const $helpBlock = $(this).closest(".form-group").find(".help-block");
    const $saveBtn = $(".savebutton");

    if (received > balance) {
      $(this).addClass("error");
      $helpBlock.text("Payment Received Amount cannot be greater than Balance Amount.").show();
      $saveBtn.prop("disabled", true);
    } else {
      $(this).removeClass("error");
      $helpBlock.text("");
      $saveBtn.prop("disabled", false);
    }

    calculateBasicAmt();
  });

  //  Use global `actualBalanceAmount` here
  function calculateBasicAmt() {
    const receivedAmt = parseFloat($("#payment_received_amount").val()) || 0;
    const balanceAmt = actualBalanceAmount - receivedAmt;
    $("#balance_amount").val(balanceAmt.toFixed(2));
  }
});
