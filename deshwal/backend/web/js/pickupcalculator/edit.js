$(document).ready(function () {
  var newURL = window.location.href;
  var module = "leads";
  var str = newURL.split(module);
  editusrl = str[0] + "leads/list";

  mode = $("#mode").val();
  // if (mode == "Create") addRowBtn("2641", "pickupcalculator");
  if (mode == "Create"){
    addRowBtn('2641', 'pickupcalculator')
  .then((message) => {
    console.log(message); // "Data appended successfully"
  })
  .catch((error) => {
    console.log(error); // "Error occurred while appending data"
  });

  } 
  observeProductField();

  let currentProductId = null;

  function observeProductField() {
    const productIdInput = document.getElementById("productid1");

    if (!productIdInput) {
      console.error("Product ID input not found");
      return;
    }

    const observer = new MutationObserver(function (mutations) {
      mutations.forEach(function (mutation) {
        if (
          mutation.type === "attributes" &&
          mutation.attributeName === "value"
        ) {
          const productId = productIdInput.value;
          currentProductId = productId;
          console.log("Product ID changed to:", productId);
          console.log("currentProductId:", currentProductId);


          // First update the first row as before
          fetchProductName(productId);

          // Then copy these values to all other rows
          copyToAllRows(productId);

          validationProduct(productId);
        }
      });
    });

    observer.observe(productIdInput, {
      attributes: true,
      attributeFilter: ["value"],
    });
  }

  // Your existing function (unchanged)
  function fetchProductName(productId) {
    if (!productId) return;

    const data = {
      productid: productId,
      _csrf: $("#csrfToken").val(),
    };

    $.ajax({
      type: "POST",
      url: "getproductinfo",
      data: data,
      success: function (response) {
        console.log("API Response:", response);

        if (response && response.data) {
          // Update the first row
          $("#productid_1").val(response.data.products_id);
          $("#product_name_1").val(response.data.product_name);
          copyToAllRows(productId);
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", error);
      },
      dataType: "json",
    });
  }

  // New function to copy values to all rows
  function copyToAllRows(productId) {
    // Get the values from the first row
    const productIdValue = $("#productid_1").val();
    const productNameValue = $("#product_name_1").val();

    // Copy to all other rows (2 through N)
    $('[id^="productid_"]').each(function () {
      const rowId = this.id.split("_")[1]; // Get the row number (2, 3, etc.)
      console.log("rowId", rowId);
      $(this).val(productIdValue);
      $("#productid_" + rowId).val(productIdValue);
      $("#product_name_" + rowId).val(productNameValue);
    });
  }

  $(document).on("click", ".add-more-records", function () {
    //alert('hello');
    //copyToAllRows();
  });

  let isDuplicate = false;

  // Trigger check when product ID changes
  function validationProduct(productId){
    if (!productId) return;
    // alert(productId);
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    $.ajax({
      url: "checkpickupduplicate", // your Yii2 controller action
      type: "POST",
      dataType: "json",
      data: {
        productid: productId,
        _csrf: csrfToken,
      },
      success: function (res) {
        if (res.exists === true) {
          isDuplicate = true;
          $(".savebutton").prop("disabled", true);
          $("#productid")
            .closest(".form-group")
            .find(".help-block")
            .html("This product is already taken. Please choose another.");
        } else {
          isDuplicate = false;
          $(".savebutton").prop("disabled", false);
          $("#productid").closest(".form-group").find(".help-block").html("");
        }
      },
    });
  }

  $(".savebutton").on("click", function (e) {
    if (isDuplicate) {
      e.preventDefault();
      return false;
    }
  });


  $(document).on("input", "[id^=from_range_], [id^=base_]", function () {
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
    let maxCountField = currentRow.find("[id^=base_]");
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
    "[id^=base_], [id^=bubble_roll_price_], [id^=bubble_roll_count_], [id^=shrink_wrap_price_], [id^=shrink_wrap_count_],[id^=box_price_],[id^=box_count_], [id^=tape_price_], [id^=tape_qty_], [id^=labour_count_], [id^=labour_cost_], [id^=eng_count_], [id^=price_], [id^=price1_], [id^=insurance_]",
    function () {
      let currentRow = $(this).closest("tr.product-row"); 
      calculateRowPickupCal(currentRow);
    }
  );

  function calculateRowPickupCal(row) {
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
    let base = parseFloat(row.find("#base_" + rowNumber).val()) || 0;
    let BubbleRollPrice =
      parseFloat(row.find("#bubble_roll_price_" + rowNumber).val()) || 0;
    let BubbleRollCount =
      parseFloat(row.find("#bubble_roll_count_" + rowNumber).val()) || 0;
    let ShrinkWrapPrice =
      parseFloat(row.find("#shrink_wrap_price_" + rowNumber).val()) || 0;
    let ShrinkWrapCount =
      parseFloat(row.find("#shrink_wrap_count_" + rowNumber).val()) || 0;

    let BoxPrice = parseFloat(row.find("#box_price_" + rowNumber).val()) || 0;
    let TapeCrice = parseFloat(row.find("#tape_price_" + rowNumber).val()) || 0;
    let TapeQty = parseFloat(row.find("#tape_qty_" + rowNumber).val()) || 0;
    let LabourCount =
      parseFloat(row.find("#labour_count_" + rowNumber).val()) || 0;
    let LabourCost =parseFloat(row.find("#labour_cost_" + rowNumber).val()) || 0;
    let EngCount = parseFloat(row.find("#eng_count_" + rowNumber).val()) || 0;

    let Price = parseFloat(row.find("#price_" + rowNumber).val()) || 0;
    let price1 = parseFloat(row.find("#price1_" + rowNumber).val()) || 0;

    let Insurance = parseFloat(row.find("#insurance_" + rowNumber).val()) || 0;

    // TotalPrice
    let TotalPrice = BubbleRollCount * BubbleRollPrice;
    row.find("#total_price_" + rowNumber).val(TotalPrice.toFixed(2));
    // TotalProce
    let TotalProce = ShrinkWrapPrice * ShrinkWrapCount;
    row.find("#total_proce_" + rowNumber).val(TotalProce.toFixed(2));
    // BoxCount
    //code added by ptpatel on date 28-05-25 for desktop calculaation chaeck
    let productid = $("#productid1").val();
    // let BoxCount;
    if(productid == "34") //34 =laptop 2=desktop
    {
      BoxCount = base / 20;         
      row.find("#box_count_" + rowNumber).val(BoxCount.toFixed(2));   
    }
    else if(productid == "2") //34 =laptop 2=desktop
    {
      BoxCount = 0;         
      row.find("#box_count_" + rowNumber).val(BoxCount.toFixed(2));   
    }
    else
    {
      BoxCount = base / 10;
      row.find("#box_count_" + rowNumber).val(BoxCount.toFixed(2));
    }

    //end code added by ptpatel

    // let BoxCount = base / 20;
    // row.find("#box_count_" + rowNumber).val(BoxCount.toFixed(2));
    // BoxPrices
    let BoxPrices = BoxPrice * BoxCount;
    row.find("#box_prices_" + rowNumber).val(BoxPrices.toFixed(2));
    // TapeCost
    let TapeCost = TapeQty * TapeCrice;
    row.find("#tape_cost_" + rowNumber).val(TapeCost.toFixed(2));

    // LabourCost1
    let LabourCost1 = LabourCost * LabourCount;
    row.find("#labour_cost1_" + rowNumber).val(LabourCost1.toFixed(2));

    //EngCost
    let EngCost = EngCount * Price;
    row.find("#eng_cost_" + rowNumber).val(EngCost.toFixed(2));

    // weight_
    let Weight;
    if(productid == "34") //34 =laptop 2=desktop
    {
        Weight = base * 3;
        row.find("#weight_" + rowNumber).val(Weight.toFixed(2));   
    }
    else //for tft and desktop
    {
      Weight = base * 5;
      row.find("#weight_" + rowNumber).val(Weight.toFixed(2));
    }
    // let Weight = base * 3;
    // row.find("#weight_" + rowNumber).val(Weight.toFixed(2));

    //TravelCost
    let TravelCost = price1 * Weight;
    row.find("#travel_cost_" + rowNumber).val(TravelCost.toFixed(2));

    //base_price_1
    let BasePrice;
    if(productid == "34") //34 =laptop 2=desktop
    {
        BasePrice = 10000 * base;
        row.find("#base_price_" + rowNumber).val(BasePrice.toFixed(2));  
    }
    else if(productid == "2")
    {
       BasePrice = 3000 * base;
        row.find("#base_price_" + rowNumber).val(BasePrice.toFixed(2));
    }
    else
    {
        BasePrice = 1500 * base;
        row.find("#base_price_" + rowNumber).val(BasePrice.toFixed(2)); 
    }
    // let BasePrice = 10000 * base;
    // row.find("#base_price_" + rowNumber).val(BasePrice.toFixed(2));

    // insurance_cost_1

    let InsuranceCost = BasePrice * (Insurance / 100);
    row.find("#insurance_cost_" + rowNumber).val(InsuranceCost.toFixed(2));

    // total_1
    let Total =
      InsuranceCost +
      TravelCost +
      EngCost +
      LabourCost1 +
      TapeCost +
      BoxPrices +
      TotalProce +
      TotalPrice;
    row.find("#total_" + rowNumber).val(Total.toFixed(2));

    let Average = Total / base;
    row.find("#average_" + rowNumber).val(Average.toFixed(2));
  }
});
