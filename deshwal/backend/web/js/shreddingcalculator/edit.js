$(document).ready(function () {
  var newURL = window.location.href;
  var newURL = window.location.href;
  var module = "leads";
  var str = newURL.split(module);
  console.log("str" + str[0]);
  // var slicestr=newURL.substring(0,str);
  editusrl = str[0] + "leads/list";
  console.log("url" + editusrl);

  mode = $("#mode").val();
  // if (mode == "Create") addRowBtn("2606", "shreddingcalculator");
  if (mode == "Create"){
    addRowBtn('2606', 'shreddingcalculator')
  .then((message) => {
    console.log(message); // "Data appended successfully"
  })
  .catch((error) => {
    console.log(error); // "Error occurred while appending data"
  });

  }
  let allowSubmission = false; // flag to block multiple form submissions
  $(".savebutton").on("click", function (e) {
    e.preventDefault();
    //alert('shatawiping');

    const $saveBtn = $(this);
    const $errorDiv = $("#error-div");
    const $form = $("#pristine-valid-example");
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    // Don't allow submission if already disabled
    if ($saveBtn.prop("disabled")) return;
    if (mode == "Create") {
      $.ajax({
        url: "shreddingcalcheck",
        type: "POST",
        dataType: "json",
        data: {
          _csrf: csrfToken,
        },
        success: function (res) {
          if (res.exists === true) {
            allowSubmission = false;

            // Disable the save button
            $saveBtn.prop("disabled", true);

            // Show error message
            $errorDiv.html(
              '<div class="alert alert-danger">A Shredding Cost Calculator record already exists. Please edit the existing one.</div>'
            );
          } else {
            allowSubmission = true;
            $errorDiv.html(""); // Clear error if any
            $form.submit(); // Submit the form
          }
        },
        error: function () {
          allowSubmission = false;
          $errorDiv.html(
            '<div class="alert alert-danger">Error while checking. Please try again later.</div>'
          );
        },
      });
    }
  });

  $(document).on("input", "[id^=from_range_], [id^=max_count_]", function () {
    let currentRow = $(this).closest("tr");

    if (!currentRow.length) {
      console.error("No row found.");
      return;
    }

    let rowId = currentRow.attr("id"); // Ex: 'row_2'
    if (!rowId) {
      console.error("Row ID is missing for:", currentRow);
      return;
    }

    let fromRangeField = currentRow.find("[id^=from_range_]");
    let maxCountField = currentRow.find("[id^=max_count_]");
    let rangeField = currentRow.find("[id^=range_]");

    let fromRange = fromRangeField.val().trim();
    let maxCount = maxCountField.val().trim();

    let fromValue = fromRange !== "" ? parseInt(fromRange) : 0;
    let maxValue = maxCount !== "" ? parseInt(maxCount) : 0;

    let fromRangeError = fromRangeField
      .closest(".form-group")
      .find(".help-block");
    let maxCountError = maxCountField
      .closest(".form-group")
      .find(".help-block");

    fromRangeError.text("");
    maxCountError.text("");

    // Basic validations
    if (fromRange === "") {
      fromRangeError.text("From range is required.");
    } else if (isNaN(fromValue) || fromValue < 0) {
      fromRangeError.text("Enter a valid positive number.");
    }

    if (maxCount === "") {
      maxCountError.text("Max count is required.");
    } else if (isNaN(maxValue) || maxValue < 0) {
      maxCountError.text("Enter a valid positive number.");
    } else if (maxValue < fromValue) {
      maxCountError.text(
        "Max count should be greater than or equal to From range."
      );
    }

    // Check with previous row if exists
    let prevRow = currentRow.prev("tr");
    if (prevRow.length) {
      let prevMaxField = prevRow.find("[id^=max_count_]");
      let prevMax = parseInt(prevMaxField.val()) || 0;

      if (fromValue <= prevMax) {
        fromRangeError.text(
          "From range must be greater than previous row's Max count (" +
            prevMax +
            ")."
        );
        rangeField.val(""); // Clear range
        return; // Stop further processing
      }
    }

    // If all is valid, update range
    if (fromValue >= 0 && maxValue >= fromValue) {
      rangeField.val(fromValue + "-" + maxValue);
    } else {
      rangeField.val("");
    }
  });

  // Event binding for shredding rows
  $(document).on(
    "input",
    "[id^=base_price_], [id^=eng_count_], [id^=eng_cost_], [id^=max_count_], [id^=vendor_eng_require_], [id^=vendor_eng_count_], [id^=machine_movement_charges_], [id^=wooden_box_cost_], [id^=box_count_]",
    function () {
      let currentRow = $(this).closest("tr.product-row"); // Your shredding row class
      calculateRowShreddingDetails(currentRow);
    }
  );

  function calculateRowShreddingDetails(row) {
    if (!row.length) {
      console.error("Invalid row provided");
      return;
    }

    let rowId = row.attr("id");
    if (!rowId) {
      console.error("Row has no ID attribute:", row);
      return;
    }

    let rowNumber = rowId.split("_").pop();

    // Fetch input values
    let basePrice = parseFloat(row.find("#base_price_" + rowNumber).val()) || 0;
    let engCount = parseFloat(row.find("#eng_count_" + rowNumber).val()) || 0;
    let engCost = parseFloat(row.find("#eng_cost_" + rowNumber).val()) || 0;
    let maxCount = parseFloat(row.find("#max_count_" + rowNumber).val()) || 1;
    let vendorEngRequire =
      parseFloat(row.find("#vendor_eng_require_" + rowNumber).val()) || 0;
    let vendorEngCount =
      parseFloat(row.find("#vendor_eng_count_" + rowNumber).val()) || 0;
    let machineMovementCharge =
      parseFloat(row.find("#machine_movement_charges_" + rowNumber).val()) || 0;
    let woodenBoxCost =
      parseFloat(row.find("#wooden_box_cost_" + rowNumber).val()) || 0;
    let boxCount = parseFloat(row.find("#box_count_" + rowNumber).val()) || 0;

    // Step 1: Engineer Cost and Vendor Total
    let engineerCost = (engCount * engCost) / maxCount;
    let totalVendorEngCost = (vendorEngRequire * vendorEngCount) / maxCount;
    row.find("#engineer_cost_" + rowNumber).val(engineerCost.toFixed(2));
    row.find("#total_" + rowNumber).val(totalVendorEngCost.toFixed(2));

    // Step 2: Wooden box charges
    let woodenBoxCharges = woodenBoxCost * boxCount;
    row
      .find("#wooden_box_charges_" + rowNumber)
      .val(woodenBoxCharges.toFixed(2));

    // Step 3: Expense
    let expense =
      (engineerCost + totalVendorEngCost) * maxCount +
      machineMovementCharge +
      woodenBoxCharges;
    row.find("#expense_" + rowNumber).val(expense.toFixed(2));

    // Step 4: Costing, Profit, etc.
    let costing = maxCount * basePrice;
    let profit = costing - expense;
    let profitPercentage = (profit / costing) * 100;
    let unitCostPrice = expense / maxCount;

    // Step 5: Set final values
    row.find("#costing_" + rowNumber).val(costing.toFixed(2));
    row.find("#profit_" + rowNumber).val(profit.toFixed(2));
    row
      .find("#profit_percentage_" + rowNumber)
      .val(profitPercentage.toFixed(2));
    row.find("#unit_cost_price_" + rowNumber).val(unitCostPrice.toFixed(2));
  }
});
