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
  // alert(modeInput);

  if (modeInput && modeInput.value === "Create") {
    // alert(modeInput);
    // initialize currency with INr
    $("#currency").val("1").trigger("change");
    data = { currency: 1, _csrf: $("#csrfToken").val() };

    //end ddepika
    getexchangerate(data);
    // initialize country with India
    $("#country").val("1").trigger("change");
    data = { country: $(this).val(), _csrf: $("#csrfToken").val() };
    ///on country change get state
    // alert($('#country').val());
    //getstate($("#country")) line is commented by ptpatel to resolve issue on date 20-11-2025 state value show two times because it trigger using $("#country").val("1").trigger("change"); this line
    // getstate($("#country"));
  }
});

///on country change get state
$(document).on("change", "#country", function () {
  data = { country: $(this).val(), _csrf: $("#csrfToken").val() };

  getstate(this);
});
///on state change get city
$(document).on("change", "#state", function () {
  data = { state: $(this).val(), _csrf: $("#csrfToken").val() };

  getcity(this);
});

function getstate(thisobj) {
  // alert("test"+thisobj.value);
  const country = $("#country").val();
  const csrfToken = $('meta[name="csrf-token"]').attr("content");

  // Reset dropdowns

  const stateDropdown = $("#state")
    .empty()
    .append('<option value="">Select</option>');

  if (country) {
    $.ajax({
      type: "POST",
      url: "getstate",
      data: { country: country, _csrf: csrfToken },
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          response.categories.forEach((state) => {
            stateDropdown.append(
              `<option value="${state.id}">${state.name}</option>`
            );
          });
          stateDropdown.trigger("change"); // Update Select2 dropdown
          // Hide the nearest validation message
          stateDropdown.closest("div").find(".help-block").hide();
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

  const cityDropdown = $("#city")
    .empty()
    .append('<option value="">Select</option>');

  if (state) {
    $.ajax({
      type: "POST",
      url: "getcity",
      data: { state: state, _csrf: csrfToken },
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          response.categories.forEach((city) => {
            cityDropdown.append(
              `<option value="${city.id}">${city.name}</option>`
            );
          });
          cityDropdown.trigger("change"); // Update Select2 dropdown
          $("#state_code").val(response.statecode.state_code);
          $("#place_of_supply").val(response.statecode.short_code);
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

$(document).on("change", "#city", function () {
  data = { city: $(this).val(), _csrf: $("#csrfToken").val() };

  getcitycode(this);
});

function getcitycode(thisobj) {
  const city = thisobj.value;
  const csrfToken = $('meta[name="csrf-token"]').attr("content");

  if (city) {
    $.ajax({
      type: "POST",
      url: "getcitycode",
      data: { city: city, _csrf: csrfToken },
      dataType: "json",
      success: function (response) {
        console.log(response);

        if (response.status === "success") {
          $("#city_short_name").val(response.short_name); // Access short_name correctly
          generateLocationName();
        } else {
          alert(response.message);
        }
      },
      error: function (xhr) {
        console.error(xhr);
        alert("Error occurred while fetching city code. Please try again.");
      },
    });
  }
}

//onchange spoc name get number
// get contact details
// Create a MutationObserver to detect changes to the input vendor account
var targetNode = document.getElementById("spoc_name1");
var observer = new MutationObserver(function (mutationsList) {
  for (var mutation of mutationsList) {
    if (mutation.type === "attributes" && mutation.attributeName === "value") {
      getcontacts();

      console.log("spoc_name1 value changed to:", targetNode.value);
    }
  }
});
// Configuration for the observer (observe attribute changes)
var config = { attributes: true };
observer.observe(targetNode, config);

function getcontacts() {
  data = {
    contact_name: $("#spoc_name1").val(),
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
        $("#spoc_number").val(response.data.mobile);
        $("#spoc_email").val(response.data.email);
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
//onchange escalation name get number
// get contact details
// Create a MutationObserver to detect changes to the input vendor account
var targetNode = document.getElementById("escalation_name1");
var observer = new MutationObserver(function (mutationsList) {
  for (var mutation of mutationsList) {
    if (mutation.type === "attributes" && mutation.attributeName === "value") {
      getcontacts2();

      console.log("escalation_name1 value changed to:", targetNode.value);
    }
  }
});
// Configuration for the observer (observe attribute changes)
var config = { attributes: true };
observer.observe(targetNode, config);

function getcontacts2() {
  data = {
    contact_name: $("#escalation_name1").val(),
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
        $("#escalation_number").val(response.data.mobile);
        $("#escalation_email").val(response.data.email);
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

// Create a MutationObserver to detect changes to the input Location Finance/invoice name
var targetNode = document.getElementById("loc_fin_invoice_name1");
var observer = new MutationObserver(function (mutationsList) {
  for (var mutation of mutationsList) {
    if (mutation.type === "attributes" && mutation.attributeName === "value") {
      getcontacts3();

      console.log("loc_fin_invoice_name1 value changed to:", targetNode.value);
    }
  }
});
// Configuration for the observer (observe attribute changes)
var config = { attributes: true };
observer.observe(targetNode, config);

function getcontacts3() {
  data = {
    contact_name: $("#loc_fin_invoice_name1").val(),
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
        $("#loc_fin_invoice_number").val(response.data.mobile);
        $("#loc_fin_invoice_email").val(response.data.email);
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

///get echnge rate nd currency from vendor_account
// Create a MutationObserver to detect changes to the input vendor account
var targetNode = document.getElementById("vendor_account1");
var observer = new MutationObserver(function (mutationsList) {
  for (var mutation of mutationsList) {
    if (mutation.type === "attributes" && mutation.attributeName === "value") {
      getvendordetail();

      console.log("vendor_account1 value changed to:", targetNode.value);
    }
  }
});
// Configuration for the observer (observe attribute changes)
var config = { attributes: true };
observer.observe(targetNode, config);

$(document).ready(function () {
  if ($("#vendor_account").val().trim() !== "") {
    getvendordetail();
  }

  $("#vendor_account").on("input change", function () {
    if ($(this).val().trim() !== "") {
      getvendordetail();
    }
  });
});

// Function to fetch vendor details
function getvendordetail() {
  let data = {
    vendor_account: $("#vendor_account1").val(),
    _csrf: $("#csrfToken").val(),
  };

  $.ajax({
    type: "POST",
    url: "getvendordetail",
    data: data,
    dataType: "json",
    success: function (response) {
      console.log(response); // Log the entire response to check its structure

      if (response && response.data) {
        $("#currency").val(response.data.currency);
        $("#exchange_rate").val(response.data.exchange_rate);
        $("#currency").trigger("change");

        // Set vendor_loc_name from vendor account table
        if (response.data.account_short_name) {
          $("#account_short_name").val(response.data.account_short_name);
        }
        else{
          $("#account_short_name").val('');

        }

        generateLocationName(); // Update Location Name dynamically
      } else {
        console.log("Invalid response format or missing data");
      }
    },
    error: function () {
      alert("Error occurred. Please try again.");
    },
  });
}

// Function to generate the location name correctly
function generateLocationName() {
  const vendorLocationName = $("#vendor_loc_name").val(); // Keep original value from vendor table
  const hiddenShortname = $("#account_short_name").val();

  const cityShortName = $("#city_short_name").val();
  const area = $("#area_sector_name").val();
  const buildingName = $("#building_name").val();
  const plotNumber = $("#plot_number").val();
  const floor = $("#floor").val();
  // alert(hiddenShortname);
  let locationName ='';
  if(hiddenShortname)
  locationName += `${hiddenShortname}`;

  if(cityShortName)
  {
    if(locationName != '')
    locationName += ' '+`${cityShortName}`;
    else
    locationName += `${cityShortName}`;
  }

  if(area)
  {
    if(locationName != '')
      locationName += ' '+`${area}`;
    else
    locationName += `${area}`;
  }

  if (buildingName && !plotNumber) {
    locationName += ` ${buildingName}`;
  }
  else if (buildingName && plotNumber) {
    locationName += ` ${buildingName}/${plotNumber}`;
  } 
  else if (!buildingName && plotNumber) {
    locationName += ` ${plotNumber}`;
  }

  if (floor) {
    locationName += ` ${floor}`;
  }

  $("#vendor_loc_name").val(locationName.trim()); // Store in a separate field if needed
}

// Trigger location name update only when relevant inputs change
$(
  "#account_short_name, #city_short_name, #area_sector_name, #building_name, #plot_number, #floor"
).on("input", generateLocationName);

$("#account_short_name").on("change", generateLocationName);

//code added by ptpatel on date 06-09-2025 to prevent duplicate account name
 $(document).on("blur", "#vendor_loc_name", function () {
  // console.log("acc blur");
   var  acc_id = $("#vendor_account1").val();
    var $input = $(this);
    var field = $input.attr("id");   // email or mobile
    var value = $input.val().trim();
    
    var $formGroup = $input.closest(".form-group"); 
    var $helpBlock = $input.closest("div").find(".help-block"); 
    if (value === "") {
       $formGroup.removeClass("error");
      $helpBlock.text(""); // clear old messages
        return; // skip empty
    }

    $.ajax({
        url: "isaccountlocationduplicate",   
        type: "POST",
        data: {
            field: field,
            value: value,
            acc_id : acc_id,
            _csrf: yii.getCsrfToken() // important in Yii2
        },
        success: function (res) {
            if (res.exists) {
              $formGroup.addClass("error");
                $helpBlock.text(value + " already exists for selected account name !");
            } else {
               if ($helpBlock.text().includes("already exists")) {
                    $helpBlock.text("");
                }
                $formGroup.removeClass("error");
            }
            toggleSaveButton();
        },
        error: function () {
            console.log("Error checking " + field);
             $formGroup.addClass("error");
        }
    });
    });
    function toggleSaveButton() {
    if ($(".form-group.error").length > 0 || $(".help-block:contains('required')").length > 0) {
        $(".savebutton").prop("disabled", true);
    } else {
        $(".savebutton").prop("disabled", false);
    }
  }
//end code added by ptpatel on date 06-09-2025 to  prevent duplicate account name

