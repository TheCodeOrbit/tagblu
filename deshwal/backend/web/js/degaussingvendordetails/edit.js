$(document).ready(function () {
  var newURL = window.location.href;
  var module = jQuery("#module").val();
  var str = newURL.indexOf(module);

  const slicestr = newURL.substring(0, str);
  function getexchangerate(data) {
    $.ajax({
      type: 'POST',
      url: slicestr + "leads/getexchangerate",
      // async:false,
      data: data,
      success: function (data) {
        //location.reload();
        $("#exchange_rate").val(data);
      },
      error: function (data) { // if error occured
        alert('Error occured.please try again');
      },
      dataType: 'html'
    });
  }

  function manageDegaussingDoneBy() {
    var degaussing_done_by = $("#degaussing_done_by").val();
    if (degaussing_done_by == 2) {
      $(".section-deshwal_engineer_name").show()
      $(".section-vendor_name").hide()
      removeTextValue("vendor_name1","vendor_name")
    } else if (degaussing_done_by == 3) {
      $(".section-deshwal_engineer_name").hide()
      removeTextValue("deshwal_engineer_name1", "deshwal_engineer_name");
      $(".section-vendor_name").show()
    } else {
      $(".section-deshwal_engineer_name").hide()
      removeTextValue("deshwal_engineer_name1", "deshwal_engineer_name");
      $(".section-vendor_name").hide()
      removeTextValue("vendor_name1","vendor_name")
    }
  }
  function hddDegaussed() {
    var total = 0;
    $(".hdd_degauss").each(function () {
      var value = parseInt($(this).val());
      if (!isNaN(value) && value >= 0) {
        total += value;
      }
    });
    if (total >= 0) {
      return total;
    } else {
      return 0
    }
  }
  function calculateTotalVendorCost() {
    var total_vc = 0;
    $(".total_cost").each(function () {
      var value = parseFloat($(this).val());
      if (!isNaN(value) && value >= 0) {
        total_vc += value;
      }
    });
    if (total_vc >= 0) {
      $("#total_vendor_cost").val(total_vc.toFixed(2))
    } else {
      $("#total_vendor_cost").val(0)
    }
  }
  // get exchangerate
  $(document).on("change", "#currency", function () {
    data = { currency: $(this).val(), _csrf: $('#csrfToken').val() };
    getexchangerate(data);
  });
  //end exchange rate
  $(document).on("change", "#degaussing_done_by", function () {
    manageDegaussingDoneBy();
  })
  $(document).on("change", ".hdd_degauss", function () {
    var total_hdd_degaussed = hddDegaussed();
    var total_hdd_count = parseInt($("#total_hdd_count").val()) || 0;
    if (total_hdd_degaussed > total_hdd_count) {
      $(this).parents("td").find(".help-block").text("HDD Degauss can not be more than the total HDD Count")
      $(this).val(0)
    } else {
      $(this).parents("td").find(".help-block").text("");
      $("#total_hdd_degauss").val(total_hdd_degaussed)
    }
  })
  $(document).on("change", "#total_hdd_count", function () {
    var total_hdd_degauss = parseInt($("#total_hdd_degauss").val()) || 0
    var total_hdd_count = parseInt($("#total_hdd_count").val()) || 0;
    if (total_hdd_degauss && total_hdd_count < total_hdd_degauss ) {
      $(this).parents(".form-group").find(".help-block").text("Total HDD Count can not be less than HDD Degauass")
      $(this).val(0)
    } else {
      $(this).parents(".form-group").find(".help-block").text("");
    }
  })

  $(document).on("change", ".bit_count,.bit_price,.labour_used,.labour_price,.lounge_daily_rent,.travel,.food,.degaussing_machine_rent", function () {
    var $row = $(this).closest('tr.product-row'); 
    var bit_count = parseFloat($row.find('.bit_count').val()) || 0;
    var bit_price = parseFloat($row.find('.bit_price').val()) || 0;
    var labour_used = parseFloat($row.find('.labour_used').val()) || 0;
    var labour_price = parseFloat($row.find('.labour_price').val()) || 0;
    var lounge_daily_rent = parseFloat($row.find('.lounge_daily_rent').val()) || 0;
    var travel = parseFloat($row.find('.travel').val()) || 0;
    var food = parseFloat($row.find('.food').val()) || 0;
    var degaussing_machine_rent = parseFloat($row.find('.degaussing_machine_rent').val()) || 0;
    var row_cost = (bit_count * bit_price) + (labour_used * labour_price) + lounge_daily_rent + travel + food + degaussing_machine_rent;
    $row.find('.total_cost').val(row_cost.toFixed(2));
    calculateTotalVendorCost()
  });

  $(document).on("click", ".remove-row-btn", function () {
    var total_hdd_drilled = hddDegaussed()
    $("#total_hdd_degauss").val(total_hdd_drilled)
    calculateTotalVendorCost()
  })
  manageDegaussingDoneBy()
  const modeInput = document.getElementById("mode");
  if (!$('#currency').val()) {
    // initialize currency with INr
    $('#currency').val("1").trigger("change");
    data = { currency: 1, _csrf: $('#csrfToken').val() };
    getexchangerate(data);
  }

});
