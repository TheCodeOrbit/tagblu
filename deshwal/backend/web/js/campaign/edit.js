$(document).ready(function () {
  var newURL = window.location.href;
  var module = jQuery("#module").val();
  var str = newURL.indexOf(module);

  const slicestr = newURL.substring(0, str);
  // get exchangerate
  $(document).on("change", "#currency", function () {
    data = { currency: $(this).val(), _csrf: $("#csrfToken").val() };

    getexchangerate(data);
  });

  //end exchange rate
  function getexchangerate(data) {
    $.ajax({
      type: "POST",
      url: slicestr + "leads/getexchangerate",
      // async:false,
      data: data,
      success: function (data) {
        //location.reload();
        $("#exchange_rate").val(data);
      },
      error: function (data) {
        // if error occured

        alert("Error occured.please try again");
      },
      dataType: "html",
    });
  }
  const modeInput = document.getElementById("mode");

  if (modeInput && modeInput.value === "Create") {
    // alert(modeInput);
    // initialize currency with INr
    $("#currency").val("1").trigger("change");
    data = { currency: 1, _csrf: $("#csrfToken").val() };

    //end ddepika
    getexchangerate(data);
  }

  // get exchangerate
  $(document).on("change", "#sender_address", function () {
    data = { sender_address: $(this).val(), _csrf: $("#csrfToken").val() };
    getsendername(data);
  });

  //end exchange rate
  function getsendername(data) {
    $.ajax({
      type: "POST",
      url: slicestr + "leads/getsendername",
      // async:false,
      data: data,
      success: function (data) {
        //location.reload();
        $("#sender_name").val(data);
      },
      error: function (data) {
        // if error occured

        alert("Error occured.please try again");
      },
      dataType: "html",
    });
  }
});
