$(document).ready(function () {
  var newURL = window.location.href;
  var module = jQuery("#module").val();
  var str = newURL.indexOf(module);

  const slicestr = newURL.substring(0, str);
  // var slicestr=newURL.substring(0,str);
  editusrl = str[0] + "leads/list";
  console.log("url" + editusrl);

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
    //initialize account status with in process
    $("#acc_status").val("1").trigger("change");
    //show only In Process
    // Hide all options except the one with a specific value
    $("#acc_status option").each(function () {
      if ($(this).val() != "1") {
        // Show only the option with value "1" =In Process
        $(this).remove(); // Remove options that don't match
      }
    });

    // initialize country with India
    $("#country").val("1").trigger("change");
    data = { country: $(this).val(), _csrf: $("#csrfToken").val() };
    ///on country change get state
    getstate($("#country"));

    // initialize currency with INr
    $("#currency").val("1").trigger("change");
    data = { currency: 1, _csrf: $("#csrfToken").val() };

    //end ddepika
    getexchangerate(data);
  }
  if (modeInput && modeInput.value === "Edit") {
    //  alert($('#currency').val());
    if ($("#currency").val() == "") {
      // initialize currency with INr
      $("#currency").val("1").trigger("change");
      data = { currency: 1, _csrf: $("#csrfToken").val() };

      //end ddepika
      getexchangerate(data);
    }
    console.log("acc_cate_outside"+$("#account_category").val());
    if($("#account_category").val() != ''){
      console.log("acc_cate_inside"+$("#account_category").val());
      hideNshowFieldasperAccCategory($("#account_category").val());
    }
    toggleRemark();
  }

  // if ($("#acc_status").val() == "2") {
  //   // alert("Active");
  //   $(
  //     "#kyc_completed,#kyc_date,#submitted_for_kyc,#kyc_completed_by,#kyc_submitted_by,#recheck_kyc,#recheck_kyc_date,#pan_no,#gst_number,#state_code,#cin,#IEC_code,#legal_entity,#upload_gst_number1,#pan_number,#cancelled_cheque,#vrf_form,#ca_certified_last_3years,#3years_financial_statement,#6months_gst_return,#6months_bank_statement,#bank_names,#account_name,#account_number,#bank_ifsc_code,#bank_swift_code"
  //   ).prop("disabled", true);
  // }
});

///on gst change get state code
$(document).on("change", "#gst_number", function () {
  var inputValue = $(this).val(); // Get input value
  var firstTwoDigits = inputValue.slice(0, 2); // Get the first two characters
  $("#state_code").val(firstTwoDigits);
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
  // alert(thisobj.value);
  const country = thisobj.value;
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

  const cityDropdown = $("#HO_city")
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

///////////on change  industry get sub industry type////////
//if subindustry is blank then rese it
if ($("#sub_industry_type").val() == "")
  $("#sub_industry_type").empty().append('<option value="">Select</option>');
$(document).on("change", "#industry", function () {
  data = { state: $(this).val(), _csrf: $("#csrfToken").val() };

  getsubindustrytype(this);
});
function getsubindustrytype(thisobj) {
  // alert(thisobj.value);
  const industry = thisobj.value;
  const csrfToken = $('meta[name="csrf-token"]').attr("content");

  // Reset dropdowns

  const cityDropdown = $("#sub_industry_type")
    .empty()
    .append('<option value="">Select</option>');

  if (sub_industry) {
    $.ajax({
      type: "POST",
      url: "getsubindustrytype",
      data: { industry: industry, _csrf: csrfToken },
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          response.categories.forEach((sub_industry_type) => {
            cityDropdown.append(
              `<option value="${sub_industry_type.id}">${sub_industry_type.name}</option>`
            );
          });
          cityDropdown.trigger("change"); // Update Select2 dropdown
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
///////////on change sub industry get sub industry segment////////
//if subindustry is blank then rese it
if ($("#sub_industry").val() == "")
  $("#sub_industry").empty().append('<option value="">Select</option>');
$(document).on("change", "#sub_industry_type", function () {
  data = { state: $(this).val(), _csrf: $("#csrfToken").val() };

  getsubindustry(this);
});
function getsubindustry(thisobj) {
  // alert(thisobj.value);
  const sub_industry_type = thisobj.value;
  const csrfToken = $('meta[name="csrf-token"]').attr("content");

  // Reset dropdowns

  const cityDropdown = $("#sub_industry")
    .empty()
    .append('<option value="">Select</option>');

  if (sub_industry_type) {
    $.ajax({
      type: "POST",
      url: "getsubindustry",
      data: { sub_industry_type: sub_industry_type, _csrf: csrfToken },
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          response.categories.forEach((sub_industry) => {
            cityDropdown.append(
              `<option value="${sub_industry.id}">${sub_industry.name}</option>`
            );
          });
          cityDropdown.trigger("change"); // Update Select2 dropdown
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
///////////on change credit stage Hold/No Credit show finance remark////////
$(".section-finance_remarks").addClass("tr-hidden");

var credit_stage = parseInt($("#credit_stage").val());
if(credit_stage){
  showfinance(credit_stage);
}
$(document).on("change", "#credit_stage", function () {
  credit_stage = parseInt($(this).val());
  if(credit_stage){
    showfinance(credit_stage);
  }
  // alert(credit_stage);
});
function showfinance(credit_stage) {
  console.log(credit_stage);
  if (credit_stage == 1 || credit_stage == 3) {
    //show finance remark
    $(".section-finance_remarks").removeClass("tr-hidden");
  } else {
    $(".section-finance_remarks").addClass("tr-hidden");
  }
  if (credit_stage == 1) {
    console.log("credit_stage-->"+credit_stage);
    //hide section-credit_limit
    $(".section-credit_limit").addClass("tr-hidden");
    //start change as per new flow account flow
    // $(".section-credit_days").addClass("tr-hidden");
    $("#acc_receivable_days").val(1).trigger("change");
    $("#acc_receivable_days").prop('disabled',true);
    // $(".section-exposure").addClass("tr-hidden");
    //end change as per new account flow
    $(".section-credit_rating").addClass("tr-hidden");
    $("#credit_limit").val("");
    $("#credit_days").val(null).trigger("change");
    $("#credit_rating").val(null).trigger("change");
  }
  else
  {
    //start change as per new flow account flow
    $(".section-exposure").removeClass("tr-hidden");
    //end change as per new account flow
  }
  //code start as per new flow in sheet
  if(credit_stage == 3)
  {
    $("#acc_status").prop("disabled", false);
    $("#acc_status").val(3).trigger("change");
    $("#acc_status").prop("disabled", true);   
  }
  //end code ad per new flow 
  else {
    $(".section-credit_limit").removeClass("tr-hidden");
    //code start as per new account flow
    // $(".section-credit_days").removeClass("tr-hidden");    
    if(credit_stage != 1)
    {
      $(".section-exposure").removeClass("tr-hidden");    
      $("#acc_receivable_days").prop('disabled',false);
    }
    $("#acc_status").prop("disabled", false);
    $("#acc_status").val(2).trigger("change");
    $("#acc_status").prop("disabled", true);
    //code end as per new flow
    $(".section-credit_rating").removeClass("tr-hidden");
  }
}
///check if kyc sumitted is checked then reset it
var submitted_for_kyc = document.getElementById("submitted_for_kyc");
if (submitted_for_kyc) {
  if (submitted_for_kyc.checked) {
    // Checkbox is checked
    $("#submitted_for_kyc").prop("disabled", true);
    $(".section-recheck_kyc,.section-kyc_completed").show();
    $(".section-submit_for_finance_kyc").show();
  } else {
    // Checkbox is not checked
    $("#submitted_for_kyc").prop("disabled", false);
    $(".section-recheck_kyc,.section-kyc_completed,.section-submit_for_finance_kyc").hide();
    $("#recheck_kyc").prop("disabled", true);
      $('.section-need_exceptional_finance_approval','.section-need_exceptional_finance_approval_remark','.section-need_exceptional_finance_approval_file').hide();
      $(".section-submit_for_finance_kyc").show();
  }
}
if (submitted_for_kyc) {
  $("#submitted_for_kyc").on("change",function(){
  // document
  //   .getElementById("submitted_for_kyc")
  //   .addEventListener("change", function () {
      console.log("3");
      if (this.checked) {
        console.log("4"); 
        //set today date in kyc completed
        // Get today's date in YYYY-MM-DD format
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, "0");
        var mm = String(today.getMonth() + 1).padStart(2, "0"); // Months are zero-indexed
        var yyyy = today.getFullYear();

        today = dd + "-" + mm + "-" + yyyy; // Format the date as YYYY-MM-DD

        // Set today's date as the value of the input
        $("#kyc_submitted_date").val(today);
        //also make mandatory other fields
        $("#pan_no").addClass("V~M");
        $("#pan_no").removeClass("V~O");

        $("#legal_entity").addClass("V~M");
        $("#legal_entity").removeClass("V~O");

        $("#gst_number").addClass("V~M");
        $("#gst_number").removeClass("V~O");

        /////////bank details///////////
        $("#bank_names").addClass("V~M");
        $("#bank_names").removeClass("V~O");
        $("#account_name").addClass("V~M");
        $("#account_name").removeClass("V~O");
        $("#account_number").addClass("NU~M");
        $("#account_number").removeClass("NU~O");
        $("#bank_ifsc_code").addClass("AN~M");
        $("#bank_ifsc_code").removeClass("AN~O");
        // $("#bank_swift_code").addClass("V~M"); // commented by ptpatel on date 18-04-2025 remove swiftcode from mandatory
        $("#bank_swift_code").removeClass("V~O");

        ///upload docs/////
        if (!$("#upload_gst_number1").closest("div").find(".upd-file").length) {
          $("#upload_gst_number1").addClass("V~M");
          $("#upload_gst_number1").removeClass("V~O");
        }
        if (!$("#pan_number").closest("div").find(".upd-file").length) {
          $("#pan_number").addClass("V~M");
          $("#pan_number").removeClass("V~O");
        }
        if (!$("#cancelled_cheque").closest("div").find(".upd-file").length) {
          $("#cancelled_cheque").addClass("V~M");
          $("#cancelled_cheque").removeClass("V~O");
        }
        if (!$("#vrf_form").closest("div").find(".upd-file").length) {
          $("#vrf_form").addClass("V~M");
          $("#vrf_form").removeClass("V~O");
        }
        //this portion is commented as per new Account Flow sheet on date 01-10-2025
        /*if (
          !$("#ca_certified_last_3years").closest("div").find(".upd-file")
            .length
        ) {
          $("#ca_certified_last_3years").addClass("V~M");
          $("#ca_certified_last_3years").removeClass("V~O");
        }
        if (
          !$("#3years_financial_statement").closest("div").find(".upd-file")
            .length
        ) {
          $("#3years_financial_statement").addClass("V~M");
          $("#3years_financial_statement").removeClass("V~O");
        }
        if (!$("#6months_gst_return").closest("div").find(".upd-file").length) {
          $("#6months_gst_return").addClass("V~M");
          $("#6months_gst_return").removeClass("V~O");
        }
        if (
          !$("#6months_bank_statement").closest("div").find(".upd-file").length
        ) {
          $("#6months_bank_statement").addClass("V~M");
          $("#6months_bank_statement").removeClass("V~O");
        }*/
       //this portion is commented as per new Account Flow sheet on date 01-10-2025
        //below field added as per CR sheet changes on date 05-08-25
        $("#kyc_msme_status").addClass("DD~M");
        $("#kyc_msme_status").removeClass("DD~O");
        // CR sheet changes ended
        /**START - this part added as per account Flow sheet changes on date 01-10-2025 */
        bankdetialsblock(this.checked);
        /**END - this part added as per account Flow sheet changes on date 01-10-2025 */
      } else {
        // unset today date in kyc completed
        $("#kyc_submitted_date").val("");
        $("#pan_no").removeClass("V~M");
        $("#pan_no").addClass("V~O");

        $("#legal_entity").removeClass("V~M");
        $("#legal_entity").addClass("V~O");
        $("#gst_number").removeClass("V~M");
        $("#gst_number").addClass("V~O");

        /////////bank details///////////
        $("#bank_names").addClass("V~O");
        $("#bank_names").removeClass("V~M");
        $("#account_name").addClass("V~O");
        $("#account_name").removeClass("V~M");
        $("#account_number").addClass("NU~O");
        $("#account_number").removeClass("NU~M");
        $("#bank_ifsc_code").addClass("AN~O");
        $("#bank_ifsc_code").removeClass("AN~M");
        $("#bank_swift_code").addClass("V~O");
        // $("#bank_swift_code").removeClass("V~M"); // commented by ptpatel on date 18-04-2025 remove swiftcode from mandatory

        ///upload docs/////
        $("#upload_gst_number1").addClass("V~O");
        $("#upload_gst_number1").removeClass("V~M");
        $("#pan_number").addClass("V~O");
        $("#pan_number").removeClass("V~M");
        $("#cancelled_cheque").addClass("V~O");
        $("#cancelled_cheque").removeClass("V~M");
        $("#vrf_form").addClass("V~O");
        $("#vrf_form").removeClass("V~M");
        $("#ca_certified_last_3years").addClass("V~O");
        $("#ca_certified_last_3years").removeClass("V~M");
        $("#3years_financial_statement").addClass("V~O");
        $("#3years_financial_statement").removeClass("V~M");
        $("#6months_gst_return").addClass("V~O");
        $("#6months_gst_return").removeClass("V~M");
        $("#6months_bank_statement").addClass("V~O");
        $("#6months_bank_statement").removeClass("V~M");

        //below field added as per CR sheet changes on date 05-08-25
        $("#kyc_msme_status").addClass("DD~O");
        $("#kyc_msme_status").removeClass("DD~M ");
        // CR sheet changes ended
      }
    });
}
/////////on checkig kyc completed set kyc date
var kyc_completed = document.getElementById("kyc_completed");
if (kyc_completed) {
  if (kyc_completed.checked) {
    // Checkbox is checked
    $("#kyc_completed").prop("disabled", true);
    $("#recheck_kyc").prop("disabled", true);
    // $(".row147").show(); //finance block    
    $(".section-submit_for_finance_kyc").show();
  } else {
    // Checkbox is not checked
    $("#kyc_completed").prop("disabled", false);
    // $(".row147").hide(); //finance block
    $(".section-submit_for_finance_kyc").hide();
    console.log("kyc_completed in else"+$(".row147").length);
  }
}
if (kyc_completed) {
  document
    .getElementById("kyc_completed")
    .addEventListener("change", function () {
      if (this.checked) {
        //set today date in kyc completed
        // Get today's date in YYYY-MM-DD format
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, "0");
        var mm = String(today.getMonth() + 1).padStart(2, "0"); // Months are zero-indexed
        var yyyy = today.getFullYear();

        today = dd + "-" + mm + "-" + yyyy; // Format the date as YYYY-MM-DD

        // Set today's date as the value of the input
        $("#kyc_date").val(today);
        //hide kyc recheck
        var recheck_kycisDisabled = $("#recheck_kyc").prop("disabled");
        if (!recheck_kycisDisabled) {
          $("#recheck_kyc").prop("checked", false);
          $("#recheck_kyc_date").val("");

          $(".section-bankaccount_number").removeClass("tr-hidden");
          $(".section-bank_name").removeClass("tr-hidden");
          $(".section-ifsc_code").removeClass("tr-hidden");
          $(".section-payment_terms").removeClass("tr-hidden");
          $(".section-swift_code").removeClass("tr-hidden");
          //start code - as per new flow changes 
          $("#acc_receivable_days").val('1').trigger("change"); // Advance
          $("#credit_days").val('6').trigger("change");; //60 days
          //end code as per new flow
        }
      } else {
        // unset today date in kyc completed
        $("#kyc_date").val("");
        $(".section-bankaccount_number").addClass("tr-hidden");
        $(".section-bank_name").addClass("tr-hidden");
        $(".section-ifsc_code").addClass("tr-hidden");
        $(".section-payment_terms").addClass("tr-hidden");
        $(".section-swift_code").addClass("tr-hidden");
        
      }
    });
}
///check if recheck kyc remark is visible///
var remarks = document.getElementById("remarks");

if (remarks) {
  if ($("#remarks").val()) {
    $(".section-remarks").removeClass("tr-hidden");
  } else {
    $(".section-remarks").addClass("tr-hidden");
  }
}
/////////on checkig kyc recheck set kyc recheck date
var recheck_kyc = document.getElementById("recheck_kyc");
if (recheck_kyc) {
  if (recheck_kyc.checked) {
    // // Checkbox is checked
    // $("#recheck_kyc").prop("disabled", true);
    //show kyc recheck remark
    $(".section-remarks").removeClass("tr-hidden");
  } else {
    // Checkbox is not checked
    // $("#recheck_kyc").prop("disabled", false);
    //show kyc recheck remark
    $(".section-remarks").addClass("tr-hidden");
  }
}
if (recheck_kyc) {
  document
    .getElementById("recheck_kyc")
    .addEventListener("change", function () {
      if (this.checked) {
        //set today date in recheck_kyc
        // Get today's date in YYYY-MM-DD format
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, "0");
        var mm = String(today.getMonth() + 1).padStart(2, "0"); // Months are zero-indexed
        var yyyy = today.getFullYear();

        today = dd + "-" + mm + "-" + yyyy; // Format the date as YYYY-MM-DD

        // Set today's date as the value of the input
        $("#recheck_kyc_date").val(today);
        //hide kyc complted

        $("#kyc_completed").prop("checked", false);
        $("#kyc_date").val("");

        ////show kyc remarks
        $(".section-remarks").removeClass("tr-hidden");
        $("#remarks").addClass("V~M");
        $("#remarks").removeClass("V~O");

        $(".section-bankaccount_number").addClass("tr-hidden");
        $(".section-bank_name").addClass("tr-hidden");
        $(".section-ifsc_code").addClass("tr-hidden");
        $(".section-payment_terms").addClass("tr-hidden");
        $(".section-swift_code").addClass("tr-hidden");
      } else {
        ///hide kyc remarks
        $(".section-remarks").addClass("tr-hidden");
        $("#remarks").addClass("V~O");
        $("#remarks").removeClass("V~M");
        // unset today date in recheck_kyc
        $("#recheck_kyc_date").val("");
        $(".section-bankaccount_number").removeClass("tr-hidden");
        $(".section-bank_name").removeClass("tr-hidden");
        $(".section-ifsc_code").removeClass("tr-hidden");
        $(".section-payment_terms").removeClass("tr-hidden");
        $(".section-swift_code").removeClass("tr-hidden");
      }
    });
}

// $("#recheck_kyc").change(function () {
//   if ($(this).is(":checked")) {
//       $(".section-kyc_date").show();
//   } else {
//       $(".section-kyc_date").hide();
//   }
// });

/////////set next review date on change last review date////////////

// var date1 = $('#last_credit_review_date');
// var date2 = $('#next_credit_review_date'); // Read-only date2

// Initialize Flatpickr on date2 with dd-mm-yyyy format
/*document.addEventListener("DOMContentLoaded", function () {
  // Ensure the date1 and date2 elements exist
  const date1 = document.getElementById("last_credit_review_date");
  const date2 = document.getElementById("next_credit_review_date");

  // Initialize Flatpickr on date2 with dd-mm-yyyy format for display
  const flatpickrInstance = flatpickr(date2, {
    dateFormat: "d-m-Y", // Display as dd-mm-yyyy
    allowInput: false, // Disable manual input
    readonly: true, // Make it readonly
    defaultDate: date2.value, // Optionally set the default value if date2 already has a value
  });

  // Listen for changes on date1
  if (date1 && date2) {
    date1.addEventListener("change", function () {
      // Get the selected date from date1 (in dd-mm-yyyy format)
      const dateValue = date1.value; // e.g., "29-01-2025"
      const dateParts = dateValue.split("-"); // Split into [dd, mm, yyyy]

      if (dateParts.length === 3) {
        // Convert dd-mm-yyyy to a Date object
        const day = parseInt(dateParts[0], 10);
        const month = parseInt(dateParts[1], 10) - 1; // Month is 0-based
        const year = parseInt(dateParts[2], 10);

        const selectedDate = new Date(year, month, day);

        if (selectedDate instanceof Date && !isNaN(selectedDate)) {
          // Add 180 days to the selected date
          selectedDate.setDate(selectedDate.getDate() + 180);

          // Format the new date as dd-mm-yyyy for display
          const newDay = ("0" + selectedDate.getDate()).slice(-2);
          const newMonth = ("0" + (selectedDate.getMonth() + 1)).slice(-2); // Months are 0-based
          const newYear = selectedDate.getFullYear();

          // Set the formatted date in date2 (display as dd-mm-yyyy)
          date2.value = `${newDay}-${newMonth}-${newYear}`;

          // Convert to yyyy-mm-dd for Flatpickr internal use (ISO format)
          const isoFormattedDate = `${newYear}-${newMonth}-${newDay}`;

          // Set the date for Flatpickr in ISO format (internally)
          flatpickrInstance.setDate(isoFormattedDate, true); // 'true' ensures it's in ISO format
        }
      }
    });
  }

  // Optionally, before submitting the form, convert date2 value to yyyy-mm-dd format
  document.querySelector("form").addEventListener("submit", function (e) {
    // Get the value from date2 in dd-mm-yyyy format
    const dateParts = date2.value.split("-");
    if (dateParts.length === 3) {
      // Convert dd-mm-yyyy to yyyy-mm-dd
      const year = dateParts[2];
      const month = dateParts[1];
      const day = dateParts[0];

      // Set the value in yyyy-mm-dd format before submitting
      date2.value = `${year}-${month}-${day}`;
    }
  });
});
*/

/**code added by ptpatel on date 29-07-2025 */

document.addEventListener("DOMContentLoaded", function () {
  const date1 = document.getElementById("last_credit_review_date");
  const date2 = document.getElementById("next_credit_review_date");

  // Helper: Parse yyyy-mm-dd string into Date object
  function parseYMD(dateStr) {
    const [y, m, d] = dateStr.split("-");
    return new Date(parseInt(y), parseInt(m) - 1, parseInt(d));
  }

  // Helper: Format Date object as dd-mm-yyyy string
  function formatDMY(dateObj) {
    const dd = String(dateObj.getDate()).padStart(2, "0");
    const mm = String(dateObj.getMonth() + 1).padStart(2, "0");
    const yyyy = dateObj.getFullYear();
    return `${dd}-${mm}-${yyyy}`;
  }

  // Preprocess value for edit mode: if yyyy-mm-dd, convert to dd-mm-yyyy
  let defaultFormattedDate = null;
  if (date2 && date2.value && /^\d{4}-\d{2}-\d{2}$/.test(date2.value)) {
    const parsed = parseYMD(date2.value);
    if (!isNaN(parsed)) {
      defaultFormattedDate = parsed;
      date2.value = formatDMY(parsed); // Set input value for display
    }
  }

  // Initialize Flatpickr with display format
  const flatpickrInstance = flatpickr(date2, {
    dateFormat: "d-m-Y",
    allowInput: false,
    readonly: true,
    defaultDate: defaultFormattedDate || null,
  });

  // On change of date1, add 180 days and update date2
  if (date1 && date2) {
    date1.addEventListener("change", function () {
      const [dd, mm, yyyy] = date1.value.split("-");
      if (dd && mm && yyyy) {
        const baseDate = new Date(parseInt(yyyy), parseInt(mm) - 1, parseInt(dd));
        if (!isNaN(baseDate)) {
          baseDate.setDate(baseDate.getDate() + 180);
          flatpickrInstance.setDate(baseDate, true); // internal Flatpickr update
          date2.value = formatDMY(baseDate); // update display value
        }
      }
    });
  }

  // On form submit, convert date2 from dd-mm-yyyy to yyyy-mm-dd
  const form = document.querySelector("form");
  if (form && date2) {
    form.addEventListener("submit", function () {
      const [dd, mm, yyyy] = date2.value.split("-");
      if (dd && mm && yyyy) {
        date2.value = `${yyyy}-${mm}-${dd}`; // convert to ISO before submit
      }
    });
  }
});

/**end code added by ptptatel on date 29-07-25 */
/////////on checking finance_detail_completed set kyc date
var finance_detail_completed = document.getElementById(
  "finance_detail_completed"
);
if (finance_detail_completed) {
  if (finance_detail_completed.checked) {
    // Checkbox is checked
    $("#finance_detail_completed").prop("disabled", true);
  } else {
    // Checkbox is not checked
    $("#finance_detail_completed").prop("disabled", false);
  }
}
if (finance_detail_completed) {
  document
    .getElementById("finance_detail_completed")
    .addEventListener("change", function () {
      if (this.checked) {
        //set today date in kyc completed
        // Get today's date in YYYY-MM-DD format
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, "0");
        var mm = String(today.getMonth() + 1).padStart(2, "0"); // Months are zero-indexed
        var yyyy = today.getFullYear();

        today = dd + "-" + mm + "-" + yyyy; // Format the date as YYYY-MM-DD

        // Set today's date as the value of the input
        $("#finance_detail_submitted_date").val(today);
      } else {
        // unset today date in kyc completed
        $("#finance_detail_submitted_date").val("");
      }
    });
}

///////////////set default credit_days////////////
var credit_days = $("#credit_days").val();
if (credit_days == "") {
  $("#credit_days").val(6);
}
var acc_receivable_days = $("#acc_receivable_days").val();
if (acc_receivable_days == "") {
  $("#acc_receivable_days").val(1);
}

//code added by ptpatel on date 04-04-25

$(".savebutton").on("click", function (e) {
  console.log("savebutton clicked");
  let flag = true;
  e.preventDefault();

  let account_category = $("#account_category").val();
  let account_type = $("#acc_type").val();

  console.log("acccategoryval => " + account_category);
  console.log("account_type => " + account_type);

  // Define all fields
  let allFields = [
    "#upload_gst_number1", "#pan_number", "#cancelled_cheque", "#vrf_form",
    //"#ca_certified_last_3years", "#3years_financial_statement", "#6months_gst_return", "#6months_bank_statement"
  ];
  // Define mandatory fields based on logic
  let mandatoryFields = [];
  let banksFields = ["#bank_names","#account_name","#account_number","#bank_ifsc_code"];

  if (account_category == "2") {
    // Client
    mandatoryFields = [
      "#upload_gst_number1", "#pan_number", "#cancelled_cheque", "#vrf_form",
    ];
    console.log("client=>"+mandatoryFields);
    // Mark bank fields as mandatory
    banksFields.forEach(function (selector) {
      let $el = $(selector);
      if ($el.hasClass("V~O")) {
        $el.removeClass("V~O").addClass("V~M");
        flag = false;
      }
    });
    if ($("#account_number").hasClass("NU~O")) {
      $("#account_number").removeClass("NU~O").addClass("NU~M");
    }
    
    if ($("#bank_ifsc_code").hasClass("AN~O")) {
      $("#bank_ifsc_code").removeClass("AN~O").addClass("AN~M");
    }
  }
  //this else if condition code added on date 06-08-25 while doing work in CR points
  else if(account_category == "1,2")
  {
    // vendor+client
     mandatoryFields = [
      "#upload_gst_number1", "#pan_number", "#cancelled_cheque", "#vrf_form",
    ];
    console.log("client=>"+mandatoryFields);
    // Mark bank fields as mandatory
    banksFields.forEach(function (selector) {
      let $el = $(selector);
      if ($el.hasClass("V~O")) {
        $el.removeClass("V~O").addClass("V~M");
        flag = false;
      }
    });
    if ($("#account_number").hasClass("NU~O")) {
      $("#account_number").removeClass("NU~O").addClass("NU~M");
    }
    
    if ($("#bank_ifsc_code").hasClass("AN~O")) {
      $("#bank_ifsc_code").removeClass("AN~O").addClass("AN~M");
    }
  } 
  //end else if condition code added on date 06-08-25 while doing work in CR points
  else {
    // Vendor (any type) — remove mandatory from bank fields
    //commented part need to commented as per new account flow sheet on date01-10-2025
    /*banksFields.forEach(function (selector) {
      let $el = $(selector);
      if ($el.hasClass("V~M")) {
        $el.removeClass("V~M").addClass("V~O");
      }
    });
    if ($("#account_number").hasClass("NU~M")) {
      $("#account_number").removeClass("NU~M").addClass("NU~O");
    }
    
    if ($("#bank_ifsc_code").hasClass("AN~M")) {
      $("#bank_ifsc_code").removeClass("AN~M").addClass("AN~O");
    }*/
  }
  if (account_category == "1" && account_type == "2") {
    // Vendor + Corporate
    mandatoryFields = [
      "#upload_gst_number1", "#pan_number", "#vrf_form"
    ];
    console.log("Vendor + Corporate=>"+mandatoryFields);
  } else if (account_category == "1") {
    // Vendor only
    mandatoryFields = [...allFields];
    console.log("Vendor=>"+mandatoryFields);
  }
  


  // Loop through all fields
  allFields.forEach(function (selector) {
    let $el = $(selector);
    if (mandatoryFields.includes(selector)) {
      // Field is mandatory
      if ($el.hasClass("F~O")) {
        $el.removeClass("F~O").addClass("F~M");
        flag = false;
      }
    } else {
      // Not mandatory
      if ($el.hasClass("F~M")) {
        $el.removeClass("F~M").addClass("F~O");
      }
    }
  });
  if(!validatePanAadhaarById())
    flag = false;

  if (!flag) {
    // const validator = new Validator();
    return false;
  }
});

//end of code added by ptpatel on date 04-04-25
//code added by ptpatel on date 18-04-25
$("#kyc_msme_status").on("change",function(){
  var msme_status = $(this).val();
  if(msme_status == 1)
  {
    if ($('input[name="msme_certificate_hiddenfile"]').length > 0) {
      if($('input[name="msme_certificate_hiddenfile"]').val() != "")
      {
        $("#msme_certificate").removeClass("F~M");
        $("#msme_certificate").addClass("F~O");
      }
    }
    else
    {
      $("#msme_certificate").removeClass("F~O");
      $("#msme_certificate").addClass("F~M");
    }
    $("#declaration_form").removeClass("F~M");
    $("#declaration_form").addClass("F~O");
  }
  //
  else if(msme_status == 2)
  {
    if ($('input[name="declaration_form_hiddenfile"]').length > 0) {
      if($('input[name="declaration_form_hiddenfile"]').val() != "")
      {
        $("#declaration_form").removeClass("F~M");
        $("#declaration_form").addClass("F~O");
      }
    }
    else
    {
      $("#declaration_form").removeClass("F~O");
      $("#declaration_form").addClass("F~M");
    }
     $("#msme_certificate").removeClass("F~M");
    $("#msme_certificate").addClass("F~O");
  }
  //
  else{
    $("#msme_certificate").removeClass("F~M");
    $("#msme_certificate").addClass("F~O");
     $("#declaration_form").removeClass("F~M");
      $("#declaration_form").addClass("F~O");
  }
})
//end of code added by ptpatel on date 18-04-25
//code added by ptpatel on date 06-09-2025 to prevent duplicate account name
/* //this code is commented by ptpatel because generalize duplicate functionality is used on date 02-03-2026 $(document).on("blur", "#acc_name", function () {  
  const urlParams = new URLSearchParams(window.location.search);
  const recordid = urlParams.get('Record');
  console.log("acc blur"+recordid);
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
        url: "isaccountduplicate",   
        type: "POST",
        data: {
            field: field,
            value: value,
            recordid : recordid,
            _csrf: yii.getCsrfToken() // important in Yii2
        },
        success: function (res) {
            if (res.exists) {
              $formGroup.addClass("error");
                $helpBlock.text(value + " already exists!");
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
}*/

//end code added by ptpatel on date 06-09-2025 to  prevent duplicate account name
/****
 * as per sheet in  Deshwal_<CR>_Request Report Sheet_GivenByClient 
 * hide show fields as per account field mapping sheet
 * code start from here on date 01-10-2025
 */
var submit_for_finance_kyc = document.getElementById("submit_for_finance_kyc");
if (submit_for_finance_kyc) {
  if (submit_for_finance_kyc.checked) {
   applyReadonlyFinanceFields(true);
    // Checkbox is checked
    $("#submit_for_finance_kyc").prop("disabled", true);
    
    $(".section-finance_detail_completed").show();
    $(".section-finance_detail_submitted_date").show();
    $(".row147").show();
  } else {
    // Checkbox is not checked
    $("#submit_for_finance_kyc").prop("disabled", false);
    $(".row147").hide();
  }
}
var finance_kyc_incompleted = document.getElementById("finance_kyc_incompleted");
if (finance_kyc_incompleted) {
  if (finance_kyc_incompleted.checked) {
    // Checkbox is checked
    $("#finance_kyc_incompleted").prop("disabled", true);
    
   applyReadonlyFinanceFields(true);
    // $(".section-finance_detail_completed").show();
    // $(".section-finance_detail_submitted_date").show();
    $(".row147").show();
  } else {
    // Checkbox is not checked
    $("#finance_kyc_incompleted").prop("disabled", false);
        // $("#finance_kyc_incompleted_remark").prop("readonly",false);
        // var today = new Date();
        // var dd = String(today.getDate()).padStart(2, "0");
        // var mm = String(today.getMonth() + 1).padStart(2, "0"); // Months are zero-indexed
        // var yyyy = today.getFullYear();

        // today = dd + "-" + mm + "-" + yyyy; // Format the date as YYYY-MM-DD

        // // Set today's date as the value of the input
        // $("#finance_kyc_incompleted_date").val(today);

  }
}
$(document).on("change", "#account_category", function () {
  let accval = $(this).val();
  hideNshowFieldasperAccCategory(accval);
});
function hideNshowFieldasperAccCategory(accCateVal)
{
  console.log("from hideNshowFieldasperAccCategory");
  //client == 2
  //vendor == 1
  //section-trade_name,section-coi adde in v11-154 point
  let hideINClient = [".section-organization", ".section-vendor_function",".section-trade_name",".section-coi"];
  let hideINVendor = [".row147,.row148,.row149,.row150,.row151",
    '.section-account_short_name','.section-acc_source','.section-acc_type','.section-industry','.section-sub_industry','.section-sub_industry_type','.section-billing_type','.section-team_name','.section-india_head_office','.section-global_head_office','.section-ca_certified_last_3years','.section-3years_financial_statement','.section-6months_gst_return','.section-6months_bank_statement','.section-need_exceptional_finance_approval','.section-need_exceptional_finance_approval_remark','.section-need_exceptional_finance_approval_file','.section-submit_for_finance_kyc'
  ]; // example classes

  // combine both arrays
  // let allHideSelectors = hideINClient.concat(hideINVendor);

  if (accCateVal == "2") { //client
      $(hideINClient.join(",")).hide();   
      $(hideINVendor.join(",")).show();
      var kyccompleted = document.getElementById("kyc_completed");
      if (kyccompleted.checked)
        $('.section-submit_for_finance_kyc').show();
      else
        $('.section-submit_for_finance_kyc').hide();
    var submit_for_finance_kyc = document.getElementById("submit_for_finance_kyc");
    if (submit_for_finance_kyc.checked) {
      $(".row147").show(); //finance block
      console.log("row147 show from submit_for_finance_kyc if");
      // $('.section-need_exceptional_finance_approval,.section-need_exceptional_finance_approval_remark,.section-need_exceptional_finance_approval_file,.section-submit_for_finance_kyc').show();
      // $(".section-finance_detail_completed").hide();
      // $(".section-finance_detail_submitted_date").hide();
    } else {
      $(".row147").hide(); //finance block
      console.log("row147 hide from submit_for_finance_kyc else");
      // $('.section-need_exceptional_finance_approval,.section-need_exceptional_finance_approval_remark,.section-need_exceptional_finance_approval_file').show();
      // $(".section-finance_detail_completed").hide();
      // $(".section-finance_detail_submitted_date").hide();
    }
    var finance_kyc_incompleted = document.getElementById("finance_kyc_incompleted");
    if (finance_kyc_incompleted.checked ) {
      console.log("from inside finance_kyc_incompleted");
      $(".row147").show(); //finance block
      
    applyReadonlyFinanceFields(true);
      if(submit_for_finance_kyc.checked){
        $('.section-finance_detail_completed, .section-finance_detail_submitted_date').show();
        console.log("from inside submit_for_finance_kyc");
      }
      else{
         console.log("from inside submit_for_finance_kyc in else part");
       $('.section-finance_detail_completed, .section-finance_detail_submitted_date').hide();
      }
    } else {
      // $(".row147").hide(); //finance block
      // $('.section-finance_detail_completed, .section-finance_detail_submitted_date').hide();
      if(submit_for_finance_kyc.checked){
        $(".row147").show();
        $('.section-finance_detail_completed, .section-finance_detail_submitted_date').show();
        console.log("from inside submit_for_finance_kyc else");
      }
      else{
        $(".row147").hide();
         console.log("from inside submit_for_finance_kyc in else else part");
       $('.section-finance_detail_completed, .section-finance_detail_submitted_date').hide();
      }
      // if($("#finance_kyc_incompleted").checked){
        // $("#finance_kyc_incompleted_remark").prop("readonly",false);
        // var today = new Date();
        // var dd = String(today.getDate()).padStart(2, "0");
        // var mm = String(today.getMonth() + 1).padStart(2, "0"); // Months are zero-indexed
        // var yyyy = today.getFullYear();

        // today = dd + "-" + mm + "-" + yyyy; // Format the date as YYYY-MM-DD

        // // Set today's date as the value of the input
        // $("#finance_kyc_incompleted_date").val(today);
    //  }
    }


  } else if (accCateVal == "1") {   //vendor   
      $(hideINClient.join(",")).show();      
      $(hideINVendor.join(",")).hide();  
  }
}
 function bankdetialsblock(isChecked){
    let bankdetailList = [
    "#bank_names",
    "#account_name",
    "#account_number",
    "#bank_ifsc_code",
    "#bank_swift_code"
  ];

  bankdetailList.forEach(function(selector) {
      let $field = $(selector);

      // get current class
      let currentClass = $field.attr("class") || "";
      let updatedClass = '';
      // replace only the trailing ~O with ~M
      if(isChecked)
        updatedClass = currentClass.replace(/~O/g, "~M");
      else
        updatedClass = currentClass.replace(/~M/g, "~O");

      // set it back
      $field.attr("class", updatedClass);
  });

 }

 $(document).on("change", "#submit_for_finance_kyc", function () {
    finanaceKYC($(this).is(":checked"));
});

function finanaceKYC(isChecked) {
    let finanaceKYC = [
        "#ca_certified_last_3years",
        "#3years_financial_statement",
        "#6months_gst_return",
        "#6months_bank_statement"
    ];

    finanaceKYC.forEach(function(selector) {
        let $field = $(selector);
        let currentClass = $field.attr("class") || "";

        let updatedClass;
        if (isChecked) {
            // Replace ~O with ~M (only once)
            updatedClass = currentClass.replace(/~O/g, "~M");
              // $(".row147").show(); //finance block
        } else {
            // Replace ~M with ~O (only once)
            updatedClass = currentClass.replace(/~M/g, "~O");
            // $(".row147").hide(); //finance block
        }

        $field.attr("class", updatedClass);
    });
}


  $(document).on("change", "#need_exceptional_finance_approval", function () {
        // Checked
        needexceptionalfinance($(this).is(":checked"));
});

 function needexceptionalfinance(vals){
  let mandatory_fields = [".section-need_exceptional_finance_approval_remark",".section-need_exceptional_finance_approval_file"];
    // $(mandatory_fields.join(",")).show();   
    //   $(hideINVendor.join(",")).show();
    console.log("from needexceptionalfinance"+vals);
    if(vals){
        $(mandatory_fields.join(",")).show();
        $("#need_exceptional_finance_approval_remark").addClass("V~M").removeClass("V~O");
        //finanaceKYC(true);
        finanaceKYC(false);
    }
    else{
        $(mandatory_fields.join(",")).hide();        
        $("#need_exceptional_finance_approval_remark").addClass("V~O").removeClass("V~M");
        // finanaceKYC(false);
        finanaceKYC(false);
    }
}

$(document).on("change", "#finance_kyc_incompleted, #finance_detail_completed", function () {
    if (this.id === "finance_detail_completed") {
      console.log("finance_detail_completed  if =---1");
      financefields(true);
        if ($(this).is(":checked")) {
            // Checked → uncheck the other and set current date
            $("#finance_kyc_incompleted").prop("checked", false);
            // let today = new Date().toISOString().split('T')[0]; // yyyy-mm-dd
            // $("#finance_detail_submitted_date").val(today);
        } else {
            // Unchecked → check the other and clear the date
            $("#finance_kyc_incompleted").prop("checked", true);
            $("#finance_detail_submitted_date").val("");
            console.log("calling from if else === 2");
        $("#finance_kyc_incompleted_remark").prop("readonly",false);
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, "0");
        var mm = String(today.getMonth() + 1).padStart(2, "0"); // Months are zero-indexed
        var yyyy = today.getFullYear();

        today = dd + "-" + mm + "-" + yyyy; // Format the date as YYYY-MM-DD

        // Set today's date as the value of the input
        $("#finance_kyc_incompleted_date").val(today);
            
        }
    } else {
      console.log("finance_kyc_incompleted =====3");
       financefields(false);
        if ($(this).is(":checked")) {
            // Checked → uncheck the other and clear the date
            $("#finance_detail_completed").prop("checked", false);
            $("#finance_detail_submitted_date").val("");

            if($("#finance_kyc_incompleted").checked){
            $("#finance_kyc_incompleted_remark").prop("readonly",false);
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, "0");
            var mm = String(today.getMonth() + 1).padStart(2, "0"); // Months are zero-indexed
            var yyyy = today.getFullYear();

            today = dd + "-" + mm + "-" + yyyy; // Format the date as YYYY-MM-DD

            // Set today's date as the value of the input
            $("#finance_kyc_incompleted_date").val(today);
        }

        } else {
            // Unchecked → check the other and set current date
            $("#finance_detail_completed").prop("checked", true);
            // let today = new Date().toISOString().split('T')[0];
            $("#finance_kyc_incompleted_remark").val("");
            console.log("calling from if else else ==== 4");
            
        }
    }
});

function financefields(_isChecked)
{
  console.log("financefields" + _isChecked)
  let finanacefields = [
        "#credit_rating",
        "#credit_stage",
        "#credit_limit",
        "#exposure",'#last_credit_review_date',"#comp_type"
    ];

    finanacefields.forEach(function(selector) {
        let $field = $(selector);
        let currentClass = $field.attr("class") || "";

        let updatedClass;
        if (_isChecked) {
            // Replace ~O with ~M (only once)
            updatedClass = currentClass.replace(/~O/g, "~M");
              // $(".row147").show(); //finance block
        } else {
            // Replace ~M with ~O (only once)
            updatedClass = currentClass.replace(/~M/g, "~O");
            // $(".row147").hide(); //finance block
        }

        $field.attr("class", updatedClass);
    });
}

if(finance_kyc_incompleted || submit_for_finance_kyc || finance_detail_completed)
{
   applyReadonlyFinanceFields(true);
  console.log("readonlyfinnacefields call ---1");
}
var _financekycincompleted = document.getElementById("finance_kyc_incompleted");
var _financedetailcompleted = document.getElementById("finance_detail_completed");
if (!_financekycincompleted.checked && !_financedetailcompleted.checked) 
{
   applyReadonlyFinanceFields(false);
}
$(document).on("change", "#finance_kyc_incompleted, #submit_for_finance_kyc", function () {
  if(this.checked){
   applyReadonlyFinanceFields(true);
  }
});
$(document).on("change", "#finance_detail_completed", function () {
  if(this.checked){
    applyReadonlyFinanceFields(false);
  }
});

function readonlyfinnacefields(vals) {
  // "#outstanding","#payment_terms",
    let finanacefields = [
        "#credit_rating", "#credit_stage", "#credit_days", "#acc_receivable_days", "#credit_limit",
        "#exposure", "#last_credit_review_date", "#next_credit_review_date","#finance_remarks",
        "#comp_type",  "#currency", "#finance_kyc_incompleted_remark","#finance_kyc_incompleted_date"
    ];
    $("#outstanding, #payment_terms").prop("readonly",true);
    finanacefields.forEach(function(field) {
        let $el = $(field);
        
        let el = $el[0]; // raw DOM element

        // Handle Select2 dropdowns
        if ($el.hasClass('select2-hidden-accessible')) {
            $el.prop("disabled", vals);
            $el.select2({ disabled: vals });
        }
        // Handle Flatpickr fields
        else if ($el[0] && $el[0]._flatpickr){

            $el[0]._flatpickr.input.readOnly = vals; // vals = true/false
            $el[0]._flatpickr.set("clickOpens", !vals);   // prevent typing
        }
        // Normal input/text field
        else {
            $el.prop("readonly", vals);
        }

           var classStr = $el.attr("class") || "";

          // Remove flatpickr-input and normalize spaces
          classStr = classStr.replace(/\bflatpickr-input\b/g, "").replace(/\s+/g, " ").trim();

          // Toggle ~O/~M based on vals
          if (vals) {
              classStr = classStr.replace(/~M/g, "~O").replace(/\s+/g, " ").trim();;
          } else {
              classStr = classStr.replace(/~O/g, "~M").replace(/\s+/g, " ").trim();;
          }

          // Remove error class if present
          if (classStr.includes("error")) {
              classStr = classStr.replace(/\berror\b/g, "").replace(/\s+/g, " ").trim();
              $el.closest(".form-group").find(".help-block").text('');
          }
          $el.attr("class", classStr);
        // Visual readonly class
        if (vals) $el.addClass("readonly-field");
        else $el.removeClass("readonly-field");
    });
 if ($("#finance_kyc_incompleted").is(":checked")) {
        $("#finance_kyc_incompleted_remark").prop("readonly",false);
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, "0");
        var mm = String(today.getMonth() + 1).padStart(2, "0"); // Months are zero-indexed
        var yyyy = today.getFullYear();

        today = dd + "-" + mm + "-" + yyyy; // Format the date as YYYY-MM-DD

        // Set today's date as the value of the input
        $("#finance_kyc_incompleted_date").val(today);
     }
     else
     {
      $("#finance_kyc_incompleted_remark").prop("readonly",true);
      $("#finance_kyc_incompleted_date").prop("readonly",true);
      $("#finance_kyc_incompleted_date").val('');
      $("#finance_kyc_incompleted_date").removeClass("DT~M").addClass("DT~O");
      $("#finance_kyc_incompleted_remark").removeClass("V~M").addClass("V~O");
     }
      
}
function applyReadonlyFinanceFields(vals) {
    startLoading(); // start loader

    let counter = 0;
    let intervalId = setInterval(() => {
        readonlyfinnacefields(vals);
        counter++;

        if (counter >= 5) { // after 5 iterations (5 sec)
            clearInterval(intervalId);
            stopLoading(); // stop loader
            console.log("Readonly fields update completed.");
        }
    }, 1000);
}

$(document).ready(function () {
var need_exceptional_finance_approval = document.getElementById("need_exceptional_finance_approval");
if (need_exceptional_finance_approval) {
  console.log("needexceptionalfinance-------"+need_exceptional_finance_approval.checked);
  if (need_exceptional_finance_approval.checked) {
    console.log("need_exceptional_finance_approval in if *************");
    needexceptionalfinance(true);
  } else {
    console.log("need_exceptional_finance_approval in else *************");
    needexceptionalfinance(false);
  }
}
if ($("#finance_detail_completed").is(':checked')) {
    // Checkbox is checked
    $("#finance_kyc_incompleted").prop("disabled",true);
  }
});
/****
 * as per sheet in  Deshwal_<CR>_Request Report Sheet_GivenByClient 
 * hide show fields as per account field mapping sheet
 * code end  here on date 01-10-2025
 */
$(document).on("change", "#credit_stage", function () {
    creditstagechanges(false);
});
function creditstagechanges() {
    let No_Credit = [ "#credit_limit", "#exposure"];
    let HOLD_stage = [ "#credit_limit", "#exposure", "#credit_days"];
    let approve_stage = ["#credit_limit", "#exposure", "#credit_days"];
    let credit_stage = $("#credit_stage").val();

    if (credit_stage == "1") { // No Credit
        HOLD_stage.forEach(selector => {
            if (selector === "#credit_days") {
                // Dropdown
                $(selector).val("").prop("disabled", false);
                } else {
                // Normal input fields
                $(selector).val("").prop("readonly", false);
              }
               var classStr = $(selector).attr("class") || "";

               classStr = classStr.replace(/~M/g, "~O").replace(/\s+/g, " ").trim();;

          // Remove error class if present
          if (classStr.includes("error")) {
              classStr = classStr.replace(/\berror\b/g, "").replace(/\s+/g, " ").trim();
              $(selector).closest(".form-group").find(".help-block").text('');
          }
          $(selector).attr("class", classStr);
        });
        No_Credit.forEach(selector => {
            $(selector).val("").prop("readonly", true);
            var classStr = $(selector).attr("class") || "";

               classStr = classStr.replace(/~M/g, "~O").replace(/\s+/g, " ").trim();;

          // Remove error class if present
          if (classStr.includes("error")) {
              classStr = classStr.replace(/\berror\b/g, "").replace(/\s+/g, " ").trim();
              $(selector).closest(".form-group").find(".help-block").text('');
          }
          $(selector).attr("class", classStr);
      });
    } 
    else if (credit_stage == "3") { // Hold
        No_Credit.forEach(selector => {
            $(selector).val("").prop("readonly", false);            
           var classStr = $(selector).attr("class") || "";

               classStr = classStr.replace(/~M/g, "~O").replace(/\s+/g, " ").trim();;

          // Remove error class if present
          if (classStr.includes("error")) {
              classStr = classStr.replace(/\berror\b/g, "").replace(/\s+/g, " ").trim();
              $(selector).closest(".form-group").find(".help-block").text('');
          }
          $(selector).attr("class", classStr);
        });
        HOLD_stage.forEach(selector => {
          
            if (selector === "#credit_days") {
                // Dropdown
                $(selector).val("").trigger("change");
                $(selector).val("").prop("disabled", true);
                } else {
                // Normal input fields
                $(selector).val("").prop("readonly", true);
                }
                var classStr = $(selector).attr("class") || "";

               classStr = classStr.replace(/~M/g, "~O").replace(/\s+/g, " ").trim();;

          // Remove error class if present
          if (classStr.includes("error")) {
              classStr = classStr.replace(/\berror\b/g, "").replace(/\s+/g, " ").trim();
              $(selector).closest(".form-group").find(".help-block").text('');
          }
          $(selector).attr("class", classStr);
        });
        
    } 
    else if (credit_stage == "2") { // approved
        // Make all fields editable again
        $("#credit_limit, #exposure")
            .prop("readonly", false)
            .prop("disabled", false);
        $("#credit_days").prop("disabled", false);
        approve_stage.forEach(selector => {
          var classStr = $(selector).attr("class") || "";

          classStr = classStr.replace(/~O/g, "~M").replace(/\s+/g, " ").trim();;

            // Remove error class if present
            if (classStr.includes("error")) {
                classStr = classStr.replace(/\berror\b/g, "").replace(/\s+/g, " ").trim();
                $(selector).closest(".form-group").find(".help-block").text('');
            }
            $(selector).attr("class", classStr);
        });
      }
}

//code added by ptpatel on date 15-12-2025 for v11- 154
 $(document).on("change", "#fortune_500, #exceptional_approval", function () {
    if (this.id === "fortune_500" && this.checked) {
        $("#exceptional_approval").prop("checked", false);
    }
    else if (this.id === "exceptional_approval" && this.checked) {
        $("#fortune_500").prop("checked", false);
    }
    toggleRemark();
});

function toggleRemark() {
    // Fortune 500
    if ($("#fortune_500").is(":checked") || $("#exceptional_approval").is(":checked")) {
        $(".section-fortune500_exceptional_approval_remark").show();
        $("#fortune500_exceptional_approval_remark")
            .addClass("V~M")
            .removeClass("V~O");
    } else {
        $(".section-fortune500_exceptional_approval_remark").hide();
        $("#fortune500_exceptional_approval_remark")
            .addClass("V~O")
            .removeClass("V~M");
    }
}

 $(document).on("blur", "input[id^='director_pan_'],input[id^='director_aadhar_card_']", function () {
    validatePanAadhaarById();
});
const PAN_REGEX = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
const AADHAAR_REGEX = /^[2-9]{1}[0-9]{11}$/;
function validatePanAadhaarById() {
    let isValid = true;

    // PAN fields
    $("input[id^='director_pan_']").each(function () {
        let $field = $(this);
        let value = $.trim($field.val());

        if (value === "") {
            clearError($field); // empty allowed
            showError($field, "This field is mandatory.");
        }
        else if (!PAN_REGEX.test(value.toUpperCase())) {
            showError($field, "Invalid PAN number");
            isValid = false;
        }
        else {
            clearError($field); // valid PAN
        }
    });

    // Aadhaar fields
    $("input[id^='director_aadhar_card_']").each(function () {
        let $field = $(this);
        let value = $.trim($field.val());

        if (value === "") {
            clearError($field);
            showError($field, "This field is mandatory.");
        }
        else if (!AADHAAR_REGEX.test(value)) {
            showError($field, "Invalid Aadhaar number");
            isValid = false;
        }
        else {
            clearError($field);
        }
    });

    return isValid;
}


function showError($field, message) {
    let $formGroup = $field.closest(".form-group");
    let $helpBlock = $formGroup.find(".help-block").first();

    $field.addClass("error");
    $helpBlock.text(message).show();
}

function clearError($field) {
    let $formGroup = $field.closest(".form-group");
    let $helpBlock = $formGroup.find(".help-block").first();

    $field.removeClass("error");
    $helpBlock.text("").hide();
}
//code added by ptpatel on date 15-12-2025 for v11- 154

function applyReadonly() {
    $('.readonly-dd').prop('disabled', true);
}

// On page load
$(document).ready(function () {
    applyReadonly();
});

// After dynamic update (important)
$(document).ajaxComplete(function () {
    applyReadonly();
});