$(document).ready(function () {
  var newURL = window.location.href;
  var module = "leads";
  var str = newURL.split(module);
  editusrl = str[0] + "leads/list";

  mode = $("#mode").val();
  // if (mode == "Create") addRowBtn("2608", "degaussingcalculator");
  if (mode == "Create"){
    addRowBtn('2608', 'degaussingcalculator')
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

    const $saveBtn = $(this);
    const $errorDiv = $("#error-div");
    const $form = $("#pristine-valid-example");
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    // Don't allow submission if already disabled
    if ($saveBtn.prop("disabled")) return;
    if (mode == "Create") {
      $.ajax({
        url: "degaussingcalcheck",
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
              '<div class="alert alert-danger">A Degaussing Cost Calculator record already exists. Please edit the existing one.</div>'
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

  // Trigger calculation on input change for degaussing rows
  $(document).on(
    "input",
    "[id^=base_price_], [id^=eng_count_], [id^=eng_cost_], [id^=max_count_], [id^=vendor_eng_require_], [id^=vendor_eng_count_], [id^=machine_movement_charges_]",
    function () {
      let currentRow = $(this).closest("tr.product-row"); // Ensure your rows have this class
      calculateRowDegaussingDetails(currentRow);
    }
  );

  function calculateRowDegaussingDetails(row) {
    if (!row.length) return;

    let rowId = row.attr("id");
    if (!rowId) return;

    let rowNumber = rowId.split("_").pop();

    // Fetch input values
    let engCount = parseFloat(row.find("#eng_count_" + rowNumber).val()) || 0;
    let engCost = parseFloat(row.find("#eng_cost_" + rowNumber).val()) || 0;
    let maxCount = parseFloat(row.find("#max_count_" + rowNumber).val()) || 1;
    let basePrice = parseFloat(row.find("#base_price_" + rowNumber).val()) || 0;
    let vendorEngRequire =
      parseFloat(row.find("#vendor_eng_require_" + rowNumber).val()) || 0;
    let vendorEngCount =
      parseFloat(row.find("#vendor_eng_count_" + rowNumber).val()) || 0;
    let machineMovementCharge =
      parseFloat(row.find("#machine_movement_charges_" + rowNumber).val()) || 0;

    // Step 1: Engineer cost
    let engineerCost = (engCount * engCost) / maxCount;
    row.find("#engineer_cost_" + rowNumber).val(engineerCost.toFixed(2));

    // Step 2: Vendor Eng Total
    let totalVendorEng = (vendorEngRequire * vendorEngCount) / maxCount;
    row.find("#total_" + rowNumber).val(totalVendorEng.toFixed(2));

    // Step 3: Unit cost per max count (machine movement)
    let unitCostExpenseMaxCount = machineMovementCharge / maxCount;
    row
      .find("#unit_cost_expense_max_count_" + rowNumber)
      .val(unitCostExpenseMaxCount.toFixed(2));

    // Step 4: Total expense
    let totalExpense =
      (engineerCost + totalVendorEng) * maxCount + machineMovementCharge;
    row.find("#expense_" + rowNumber).val(totalExpense.toFixed(2));

    // Step 5: Costing
    let totalCosting = basePrice * maxCount;
    row.find("#costing_" + rowNumber).val(totalCosting.toFixed(2));

    // Step 6: Profit & %
    let profit = totalCosting - totalExpense;
    row.find("#profit_" + rowNumber).val(profit.toFixed(2));

    let profitPercentage = (profit / totalCosting) * 100;
    row
      .find("#profit_percentage_" + rowNumber)
      .val(profitPercentage.toFixed(2));

    // Step 7: Unit sale
    let unitSale = totalCosting / maxCount;
    row.find("#unit_sale_" + rowNumber).val(unitSale.toFixed(2));
  }
});
