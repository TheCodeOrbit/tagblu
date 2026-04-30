$(document).ready(function () {
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
  function calculatePendingHdd() {
    var pendinghdd = 0;
    var total_hdd_count = parseInt($("#total_hdd_count").val()) || 0;
    var hdd_drilled = parseInt($("#hdd_drilled").val()) || 0;
    pendinghdd = total_hdd_count - hdd_drilled;
    if (pendinghdd < 0) {
      $("#hdd_drilled").parents(".form-group").find(".help-block").text("HDD Drilled can not be more than Total HDD Count");
    } else {
      $("#pending_hdd").val(pendinghdd);
      $("#hdd_drilled").parents(".form-group").find(".help-block").text("");
    }
  }
  function imageUploadControl() {
    var image_available = $("#image_available").val();
    if (image_available == 2) {
      $(".section-image").show();
    } else {
      $(".section-image").hide();
    }
  }
  var newURL = window.location.href;
  var module = jQuery("#module").val();
  var str = newURL.indexOf(module);

  const slicestr = newURL.substring(0, str);
  // get exchangerate
  $(document).on("change", "#currency", function () {
    data = { currency: $(this).val(), _csrf: $('#csrfToken').val() };
    getexchangerate(data);
  });
  //end exchange rate
  $(document).on("change", "#total_hdd_count,#hdd_drilled", function () {
    calculatePendingHdd()
  })
  $(document).on("change", "#image_available", function () {
    imageUploadControl()
  })
  imageUploadControl()
  const modeInput = document.getElementById("mode");
  if (modeInput && modeInput.value === "Create") {
    // initialize currency with INr
    $('#currency').val("1").trigger("change");
      data = { currency: 1, _csrf: $('#csrfToken').val() };
    //end ddepika
    getexchangerate(data);
  }

});
