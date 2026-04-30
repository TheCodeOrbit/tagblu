$(document).ready(function () {
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
  const modeInput = document.getElementById("mode");


  if (modeInput && modeInput.value === "Create") {
    alert(modeInput);
    // initialize currency with INr
    $('#currency').val("1").trigger("change");
    data = { currency: 1, _csrf: $('#csrfToken').val() };

    //end ddepika
    getexchangerate(data);
    // initialize country with India
    $('#country').val("1").trigger("change");
    data = { country: $(this).val(), _csrf: $('#csrfToken').val() };
    ///on country change get state
    // alert($('#country').val());
    getstate($('#country'));
  }

});


///on country change get state
$(document).on("change", "#country", function () {
  data = { country: $(this).val(), _csrf: $('#csrfToken').val() };

  getstate(this);


});
///on state change get city
$(document).on("change", "#state", function () {
  data = { state: $(this).val(), _csrf: $('#csrfToken').val() };

  getcity(this);


});
function getstate(thisobj) {
  // alert("test"+thisobj.value);
  const country = $('#country').val();
  const csrfToken = $('meta[name="csrf-token"]').attr("content");

  // Reset dropdowns

  const stateDropdown = $("#state").empty().append('<option value="">Select</option>');

  if (country) {
    $.ajax({
      type: "POST",
      url: "getstate",
      data: { country: country, _csrf: csrfToken },
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          response.categories.forEach((state) => {
            stateDropdown.append(`<option value="${state.id}">${state.name}</option>`);
          });
          stateDropdown.trigger('change'); // Update Select2 dropdown
        } else {
          alert(response.message);
        }
      },
      error: function (xhr) {
        console.error(xhr);
        alert("Error occurred while fetching categories. Please try again.");
      },
    });
  }

}
function getcity(thisobj) {
  // alert(thisobj.value);
  const state = thisobj.value;
  const csrfToken = $('meta[name="csrf-token"]').attr("content");

  // Reset dropdowns

  const cityDropdown = $("#city").empty().append('<option value="">Select</option>');

  if (state) {
    $.ajax({
      type: "POST",
      url: "getcity",
      data: { state: state, _csrf: csrfToken },
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          response.categories.forEach((city) => {
            cityDropdown.append(`<option value="${city.id}">${city.name}</option>`);
          });
          cityDropdown.trigger('change'); // Update Select2 dropdown
        } else {
          alert(response.message);
        }
      },
      error: function (xhr) {
        console.error(xhr);
        alert("Error occurred while fetching categories. Please try again.");
      },
    });
  }

}


