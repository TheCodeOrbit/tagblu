$(document).ready(function () {
  var newURL = window.location.href;
  var newURL = window.location.href;
  var module = "opportunities";
  var str = newURL.split(module);
  console.log("str" + str[0]);
  // var slicestr=newURL.substring(0,str);
  editusrl = str[0] + "opportunities/list";
  console.log("url" + editusrl);

  // var form = document.getElementById("pristine-valid-example");
  // var pristine = new Pristine(form);
  // $(document).on("click", ".savebutton", function (e) {
  //   // alert("checking");
  //   // $('.savebutton').click(function(e){
  //   console.log("clicked");

  //   var isValid = true;
  //   console.log("teregdfg fh");

  //   var valid = pristine.validate();
  //   if (valid && isValid) {
  //     form.submit();
  //   }
  // });


  // Initialize Select2 for all dropdowns
  // $('#commit_month,#closure_year').select2();
  // Get references to the commit date and commit month inputs
  const commitDateInput = document.getElementById("expected_closure_date");
  const commitMonthSelect = document.getElementById("commit_month");

  // Add an event listener to the commit date input
  commitDateInput.addEventListener("change", function () {

    // Get the selected date
    // const selectedDate = new Date(this.value);

    // if (!isNaN(selectedDate)) {
    //   // Ensure the date is valid
    //   // Get the month (0-indexed, so add 1 to match the select options)
    //   const month = selectedDate.getMonth() + 1;
    //   $('#commit_month').val(month).trigger("change");

    //   // Set the commit month select value
    //   commitMonthSelect.value = month.toString();

    //   // Enable the commit month select (if it was disabled)
    //   commitMonthSelect.disabled = false;
    // } else {
    //   // Clear and disable the commit month select if the date is invalid
    //   commitMonthSelect.value = "";
    //   commitMonthSelect.disabled = true;
    // }
    const dateValue = commitDateInput.value;  // e.g., "29-01-2025"
    const dateParts = dateValue.split('-');  // Split into [dd, mm, yyyy]

    if (dateParts.length === 3) {
      // alert(dateParts[1]);
      
      // Convert dd-mm-yyyy to a Date object
      const day = parseInt(dateParts[0], 10);
      const month = parseInt(dateParts[1], 10);  // Month is 0-based
      const year = parseInt(dateParts[2], 10);
      // Set the commit month select value
      // alert(month.toString());
      commitMonthSelect.value = month.toString();
      // $("#commit_month").val().
    $('#commit_month').val(month).trigger("change");


      // Enable the commit month select (if it was disabled)
      // commitMonthSelect.disabled = false;
    }
  });

  // Get references to the inputs
  const closingDateInput = document.getElementById("closing_date");
  const closureMonthInput = document.getElementById("closure_month");
  const closureYearInput = document.getElementById("closure_year");

  // Add an event listener to the closing date input
  closingDateInput.addEventListener("change", function () {
    // Get the selected date
    const selectedDate = new Date(this.value);

    if (!isNaN(selectedDate)) {
      // Check if the date is valid
      // Extract month and year from the selected date
      const month = selectedDate.getMonth() + 1; // Months are zero-based
      const year = selectedDate.getFullYear();

      // Set the closure month
      closureMonthInput.value = month;
      $('#closure_month').val(month).trigger('change');

      // Set the closure year
      // closureYearInput.value = year;
    } else {
      // Clear the inputs if the date is invalid
      closureMonthInput.value = "";
      closureYearInput.value = "";
    }
  });

  // get contact details
  // Create a MutationObserver to detect changes to the input vendor account
  var targetNode = document.getElementById("contact_name1");
  var observer = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
      if (
        mutation.type === "attributes" &&
        mutation.attributeName === "value"
      ) {
        getcontacts();

        console.log("contact_name1 value changed to:", targetNode.value);
      }
    }
  });
  // Configuration for the observer (observe attribute changes)
  var config = { attributes: true };
  observer.observe(targetNode, config);

  function getcontacts() {
    data = {
      contact_name: $("#contact_name1").val(),
      _csrf: $("#csrfToken").val(),
    };

    $.ajax({
      type: "POST",
      url: "getcontacts",
      // async:false,
      data: data,
      success: function (response) {
        console.log(response); // Log the entire response to check its structure

        // Check if the data object exists and contains 'first_name'
        if (response && response.data) {
          //$("#contact_name").val(response.data.first_name);
          // $("#contact_name1").val(response.data.contacts_id);
          $("#contact_mobile").val(response.data.mobile);
          $("#contact_email").val(response.data.email);
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
  //end get contacts
  //get warehouse
  // Create a MutationObserver to detect changes to the input vendor account
  var targetNode = document.getElementById("business_entity1");
  var observer = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
      if (
        mutation.type === "attributes" &&
        mutation.attributeName === "value"
      ) {
        getwarehouse();
        console.log("business_entity value changed to:", targetNode.value);
      }
    }
  });
  // Configuration for the observer (observe attribute changes)
  var config = { attributes: true };
  observer.observe(targetNode, config);
  function getwarehouse() {
    data = {
      business_entity: $("#business_entity1").val(),
      _csrf: $("#csrfToken").val(),
    };

    $.ajax({
      type: "POST",
      url: "getwarehouse",
      // async:false,
      data: data,
      success: function (response) {
        console.log(response); // Log the entire response to check its structure

        // Check if the data object exists and contains 'first_name'
        if (response && response.data) {
          $("#warehouse_address").val(response.data.address);
          $("#warehouse_state").val(response.data.state);
          $("#warehouse_state_code").val(response.data.statecode);
          $("#warehouse_gstin_no").val(response.data.gstn);
          $("#warehouse_pincode").val(response.data.pincode);
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

  //get vendor locations i.e, bill location
  // Create a MutationObserver to detect changes to the input vendor account
  var targetNode = document.getElementById("bill_location1");
  var observer = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
      if (
        mutation.type === "attributes" &&
        mutation.attributeName === "value"
      ) {
        getbilllocation();
        console.log("bill_location1 value changed to:", targetNode.value);
      }
    }
  });
  // Configuration for the observer (observe attribute changes)
  var config = { attributes: true };
  observer.observe(targetNode, config);
  function getbilllocation() {
    data = {
      bill_location: $("#bill_location1").val(),
      _csrf: $("#csrfToken").val(),
    };

    $.ajax({
      type: "POST",
      url: "getvendorlocation",
      // async:false,
      data: data,
      success: function (response) {
        console.log(response); // Log the entire response to check its structure

        // Check if the data object exists and contains 'first_name'
        if (response && response.data) {
          $("#bill_legal_entity").val(response.data.legal_entity_name);
          $("#bill_address").val(response.data.address);
          $("#bill_state").val(response.data.state);
          $("#bill_state_code").val(response.data.state_code);
          $("#bill_gstin_no").val(response.data.gstin_no_uin);
          $("#bill_pincode").val(response.data.pincode);
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

  // get exchangerate
  $(document).on("change", "#currency", function () {
    data = { currency: $(this).val(), _csrf: $("#csrfToken").val() };

    $.ajax({
      type: "POST",
      url: "getexchangerate",
      // async:false,
      data: data,
      success: function (data) {
        //window.location.href = editusrl;
        $("#exchange_rate").val(data);
      },
      error: function (data) {
        // if error occured

        alert("Error occured.please try again");
      },
      dataType: "html",
    });
  });
  //end exchange rate
});

document.addEventListener("DOMContentLoaded", function () {
  // Check if mode is 'Create'
  const modeInput = document.getElementById("mode");
  if (modeInput && modeInput.value === "Create") {

    //show only lead created  added on 17 jan 2025 by deepika
    // Hide all options except the one with a specific value
    $('#stage option').each(function () {
      if ($(this).val() != '1') { // Show only the option with value "1" = Opportunity created
        $(this).remove(); // Remove options that don't match
      }
    });

    // Set default value for Stage
    const stageSelect = $("#stage");
    if (stageSelect.length) {
      stageSelect.val("1").trigger("change"); // Value for "Opportunity Created"
    }
  }
});

//show inspection
var inspection = document.getElementById('inspection');

if (inspection.checked) {
  // Checkbox is checked
  showinspection(1);
} else {
  // Checkbox is not checked
  showinspection(0);
}
document.getElementById('inspection').addEventListener('change', function () {
  if (this.checked) {
    // Checkbox is checked
    showinspection(1);
  } else {
    // Checkbox is unchecked
    showinspection(0);
  }
});

function showinspection(v) {
  if (v) {
    //show pickup section
    $(".section-no_inspection_locations").removeClass("tr-hidden");
    $(".section-inspection_billable").removeClass("tr-hidden");
    // $(".section-inspection_billing_type").removeClass("tr-hidden");
    var show = $("#inspection_billable").val().trim() === '2';
    $(".section-inspection_billing_type").toggleClass("tr-hidden", !show);
    if (!show) $("#inspection_billing_type").val(null).trigger('change');
  }
  else {
    // hide pickupsection
    $(".section-no_inspection_locations").addClass("tr-hidden");
    $(".section-inspection_billable").addClass("tr-hidden");
    $(".section-inspection_billing_type").addClass("tr-hidden");
    $("#no_inspection_locations").val("");
    $("#inspection_billable").val(null).trigger('change');;
    $("#inspection_billing_type").val(null).trigger('change');;

  }
}
//end inspection
//show drilling
var drilling = document.getElementById('drilling');

if (drilling.checked) {
  // Checkbox is checked
  showdrilling(1);
} else {
  // Checkbox is not checked
  showdrilling(0);
}
document.getElementById('drilling').addEventListener('change', function () {
  if (this.checked) {
    // Checkbox is checked
    showdrilling(1);
  } else {
    // Checkbox is unchecked
    showdrilling(0);
  }
});

function showdrilling(v) {
  if (v) {
    //show pickup section
    $(".section-no_drilling_locations").removeClass("tr-hidden");
    $(".section-drilling_billable").removeClass("tr-hidden");
    // $(".section-drilling_billing_type").removeClass("tr-hidden");
    var show = $("#drilling_billable").val().trim() === '2';
    $(".section-drilling_billing_type").toggleClass("tr-hidden", !show);
    if (!show) $("#drilling_billing_type").val(null).trigger('change');
  }
  else {
    // hide pickupsection
    $(".section-no_drilling_locations").addClass("tr-hidden");
    $(".section-drilling_billable").addClass("tr-hidden");
    $(".section-drilling_billing_type").addClass("tr-hidden");
    $("#no_drilling_locations").val("");
    $("#drilling_billable").val(null).trigger('change');;
    $("#drilling_billing_type").val(null).trigger('change');;

  }
}
//end drilling
//show degaussing
var degaussing = document.getElementById('degaussing');

if (degaussing.checked) {
  // Checkbox is checked
  showdegaussing(1);
} else {
  // Checkbox is not checked
  showdegaussing(0);
}
document.getElementById('degaussing').addEventListener('change', function () {
  if (this.checked) {
    // Checkbox is checked
    showdegaussing(1);
  } else {
    // Checkbox is unchecked
    showdegaussing(0);
  }
});

function showdegaussing(v) {
  if (v) {
    //show pickup section
    $(".section-no_degaussing_location").removeClass("tr-hidden");
    $(".section-degaussing_billable").removeClass("tr-hidden");
    // $(".section-degaussing_billing_type").removeClass("tr-hidden");
    var show = $("#degaussing_billable").val().trim() === '2';
    $(".section-degaussing_billing_type").toggleClass("tr-hidden", !show);
    if (!show) $("#degaussing_billing_type").val(null).trigger('change');
  }
  else {
    // hide pickupsection
    $(".section-no_degaussing_location").addClass("tr-hidden");
    $(".section-degaussing_billable").addClass("tr-hidden");
    $(".section-degaussing_billing_type").addClass("tr-hidden");
    $("#no_degaussing_location").val("");
    $("#degaussing_billable").val(null).trigger('change');;
    $("#degaussing_billing_type").val(null).trigger('change');;

  }
}
//end degaussing
//show shredding
var shredding = document.getElementById('shredding');

if (shredding.checked) {
  // Checkbox is checked
  showshredding(1);
} else {
  // Checkbox is not checked
  showshredding(0);
}
document.getElementById('shredding').addEventListener('change', function () {
  if (this.checked) {
    // Checkbox is checked
    showshredding(1);
  } else {
    // Checkbox is unchecked
    showshredding(0);
  }
});

function showshredding(v) {
  if (v) {
    //show pickup section
    $(".section-no_shredding_location").removeClass("tr-hidden");
    $(".section-shredding_billable").removeClass("tr-hidden");
    // $(".section-shredding_billing_type").removeClass("tr-hidden");
    var show = $("#shredding_billable").val().trim() === '2';
    $(".section-shredding_billing_type").toggleClass("tr-hidden", !show);
    if (!show) $("#shredding_billing_type").val(null).trigger('change');
  }
  else {
    // hide pickupsection
    $(".section-no_shredding_location").addClass("tr-hidden");
    $(".section-shredding_billable").addClass("tr-hidden");
    $(".section-shredding_billing_type").addClass("tr-hidden");
    $("#no_shredding_location").val("");
    $("#shredding_billable").val(null).trigger('change');;
    $("#shredding_billing_type").val(null).trigger('change');;

  }
}
//end shredding

//show data_wiping
var data_wiping = document.getElementById('data_wiping');

if (data_wiping.checked) {
  // Checkbox is checked
  showdata_wiping(1);
} else {
  // Checkbox is not checked
  showdata_wiping(0);
}
document.getElementById('data_wiping').addEventListener('change', function () {
  if (this.checked) {
    // Checkbox is checked
    showdata_wiping(1);
  } else {
    // Checkbox is unchecked
    showdata_wiping(0);
  }
});

function showdata_wiping(v) {
  if (v) {
    //show pickup section
    $(".section-no_wiping_location").removeClass("tr-hidden");
    $(".section-data_wiping_billable").removeClass("tr-hidden");
    // $(".section-data_wiping_billing_type").removeClass("tr-hidden");
    var show = $("#data_wiping_billable").val().trim() === '2';
    $(".section-data_wiping_billing_type").toggleClass("tr-hidden", !show);
    if (!show) $("#data_wiping_billing_type").val(null).trigger('change');
  }
  else {
    // hide pickupsection
    $(".section-no_wiping_location").addClass("tr-hidden");
    $(".section-data_wiping_billable").addClass("tr-hidden");
    $(".section-data_wiping_billing_type").addClass("tr-hidden");
    $("#no_wiping_location").val("");
    $("#data_wiping_billable").val(null).trigger('change');;
    $("#data_wiping_billing_type").val(null).trigger('change');;

  }
}
//end data_wiping

//show data_wiping
var pickup = document.getElementById('pickup');

if (pickup.checked) {
  // Checkbox is checked
  showpickup(1);
} else {
  // Checkbox is not checked
  showpickup(0);
}
document.getElementById('pickup').addEventListener('change', function () {
  if (this.checked) {
    // Checkbox is checked
    showpickup(1);
  } else {
    // Checkbox is unchecked
    showpickup(0);
  }
});

function showpickup(v) {
  if (v) {
    //show pickup section
    $(".section-no_pickup_locations").removeClass("tr-hidden");
  }
  else {
    // hide pickupsection
    $(".section-no_pickup_locations").addClass("tr-hidden");
    $("#no_pickup_locations").val('');

  }
}
//end pickup
//show shredding
var weighing = document.getElementById('weighing');

if (weighing.checked) {
  // Checkbox is checked
  showweighing(1);
} else {
  // Checkbox is not checked
  showweighing(0);
}
document.getElementById('weighing').addEventListener('change', function () {
  if (this.checked) {
    // Checkbox is checked
    showweighing(1);
  } else {
    // Checkbox is unchecked
    showweighing(0);
  }
});

function showweighing(v) {
  if (v) {
    //show pickup section
    $(".section-no_weighing_locations").removeClass("tr-hidden");
    $(".section-weighing_billable").removeClass("tr-hidden");
    // $(".section-data_weighing_billing_type").removeClass("tr-hidden");
    var show = $("#weighing_billable").val().trim() === '2';
    $(".section-data_weighing_billing_type").toggleClass("tr-hidden", !show);
    if (!show) $("#data_weighing_billing_type").val(null).trigger('change');
  }
  else {
    // hide pickupsection
    $(".section-no_weighing_locations").addClass("tr-hidden");
    $(".section-weighing_billable").addClass("tr-hidden");
    $(".section-data_weighing_billing_type").addClass("tr-hidden");
    $("#no_weighing_locations").val("");
    $("#weighing_billable").val(null).trigger('change');;
    $("#data_weighing_billing_type").val(null).trigger('change');;

  }
}
//end weighing

////////////////////on change inspection,drilling,degaussing,shredding/////////////////////////
$(document).on("change", "#inspection_billable", function () {
  var show = $(this).val().trim() === '2';
  $(".section-inspection_billing_type").toggleClass("tr-hidden", !show);
  if (!show) $("#inspection_billing_type").val(null).trigger('change');
});
$(document).on("change", "#drilling_billable", function () {
  var show = $(this).val().trim() === '2';
  $(".section-drilling_billing_type").toggleClass("tr-hidden", !show);
  if (!show) $("#drilling_billing_type").val(null).trigger('change');
});
$(document).on("change", "#degaussing_billable", function () {
  var show = $(this).val().trim() === '2';
  $(".section-degaussing_billing_type").toggleClass("tr-hidden", !show);
  if (!show) $("#degaussing_billing_type").val(null).trigger('change');
});
$(document).on("change", "#shredding_billable", function () {
  var show = $(this).val().trim() === '2';
  $(".section-shredding_billing_type").toggleClass("tr-hidden", !show);
  if (!show) $("#shredding_billing_type").val(null).trigger('change');
});
$(document).on("change", "#data_wiping_billable", function () {
  var show = $(this).val().trim() === '2';
  $(".section-data_wiping_billing_type").toggleClass("tr-hidden", !show);
  if (!show) $("#data_wiping_billing_type").val(null).trigger('change');
});
$(document).on("change", "#weighing_billable", function () {
  var show = $(this).val().trim() === '2';
  $(".section-data_weighing_billing_type").toggleClass("tr-hidden", !show);
  if (!show) $("#data_weighing_billing_type").val(null).trigger('change');
});

////////////////////end on change inspection/////////////////////////
//////////check if product is added against this opportunity/////
// const urlParams = new URLSearchParams(window.location.search);
if (opportunityid) {


}
////////////////end checking////////////////////////////////////
////////////////////fix pickup on edit//////////////////////////
const mode = document.getElementById("mode");
if (mode?.value === "Edit") {
  var opportunityid = urlParams.get("Record");

  checkproducts(opportunityid);
  checkbillingaddress();
  if ($("#pickup").prop("checked")) {
    $("#pickup").prop("disabled", true);

    var pickuplocations = $("#no_pickup_locations").val();
    if (pickuplocations) $("#no_pickup_locations").attr("readonly", true);
    else checkproducts(opportunityid);
    checkbillingaddress();
  }
}
if (mode?.value === "Edit" && $("#drilling").prop("checked")) {
  $("#drilling").prop("disabled", true);
  const drilling_billable = $("#drilling_billable").val();
  const drillinglocations = $("#no_drilling_locations").val();
  if (drillinglocations) $("#no_drilling_locations").attr("readonly", true);
  // if (drilling_billable) $("#drilling_billable").attr("disabled", true);
}
if (mode?.value === "Edit") {
  stage = $("#stage").val();
  if (stage == 16) $("#stage").attr("disabled", true);

}

////////////////////end fix pickup//////////////////////////////
///////////function to check if product exist/////
function checkproducts(opportunityid) {
  // alert(opportunityid);
  pickuplocations = $("#no_pickup_locations").val();
  $("#pickup").prop("disabled", true);
  $("#no_pickup_locations").attr("readonly", true);
  $('label input#pickup').closest('label').after('<span class="red"> <sup>(Please add products to this opportunity to enable Pickup)</sup></span>');

  data = {
    opportunityid: opportunityid,
    _csrf: $("#csrfToken").val(),
  };
  //  alert(data.opportunityid);
  if (pickuplocations) {
    // alert('sdfd');
    $('label input#pickup').closest('label').next('span.red').remove();

  }
  else {

    $.ajax({
      type: "POST",
      url: "checkproducts",
      // async:false,
      data: data,
      success: function (response) {
        // Check if the data object exists and contains 'first_name'
        if (response && response.data) {
          // alert(response.data.cnt);
          if (response.data.cnt > 0) {
            $("#pickup").prop("disabled", false);
            $("#no_pickup_locations").attr("readonly", false);
            $('label input#pickup').closest('label').next('span.red').remove();


          }


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

}
//////////function to check billing address////////
function checkbillingaddress() {
  bill_location = $("#bill_location").val();
  // alert(bill_location);
  if (!bill_location) {
    //disable drilling
    $("#drilling").prop("disabled", true);
    $("#drilling").prop("title", "First Update billing address.");
    $('label input#drilling').closest('label').after('<span class="red"> <sup>(First update billing detail to enable drilling)</sup></span>');
    $("#degaussing").prop("disabled", true);

  }
  else {
    $('label input#drilling').closest('label').next('span.red').remove();

  }
}