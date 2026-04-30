$(document).ready(function () {
  var newURL = window.location.href;
  var newURL = window.location.href;
  var module = "leads";
  var str = newURL.split(module);
  console.log("str" + str[0]);
  // var slicestr=newURL.substring(0,str);
  editusrl = str[0] + "leads/list";
  console.log("url" + editusrl);
  /**
 * v11 -268 added code on date 23-02-2026
 */
  $(".section-acc_name").hide();
  $("#acc_name").removeClass("V~M").addClass("V~O");

  flatpickr("#call_start_time", {
    enableTime: true,
    dateFormat: "Y-m-d H:i",
    time_24hr: true,
    minDate: "today",
    onChange: calculateDuration,
  });

  flatpickr("#call_end_time", {
    enableTime: true,
    dateFormat: "Y-m-d H:i",
    time_24hr: true,
    minDate: "today",
    onChange: calculateDuration,
  });

  function calculateDuration() {
    let startInput = document.getElementById("call_start_time").value;
    let endInput = document.getElementById("call_end_time").value;

    console.log("startInput:", startInput);
    console.log("endInput:", endInput);

    if (!startInput || !endInput) {
      document.getElementById("call_duration").value = "";
      return;
    }

    // ✅ Use JavaScript's Date constructor
    let start = new Date(startInput.replace(" ", "T")); // Ensures correct parsing
    let end = new Date(endInput.replace(" ", "T"));

    console.log("Parsed Start:", start);
    console.log("Parsed End:", end);

    if (end < start) {
      // alert("End time cannot be earlier than start time!");

      // document.getElementById("call_end_time")._flatpickr.clear();
      // document.getElementById("call_duration").value = "";
      return;
    }

    // ✅ Correct duration calculation
    let diffMs = end - start;
    let totalMinutes = Math.floor(diffMs / (1000 * 60));
    let hours = Math.floor(totalMinutes / 60);
    let minutes = totalMinutes % 60;

    console.log(`Duration: ${hours}h ${minutes}m`);
    document.getElementById("call_duration").value = `${hours}h ${minutes}m`;
  }

  // Attach event listeners
  document
    .getElementById("call_start_time")
    .addEventListener("change", calculateDuration);
  document
    .getElementById("call_end_time")
    .addEventListener("change", calculateDuration);


  //get account name
   // Function to get query parameter value by name
    function getQueryParam(param) {
        var urlParams = new URLSearchParams(window.location.search); // Get the query string
        return urlParams.get(param); // Return the value of the parameter
    }

    // Get the values of sourcemodule and sourceid
    var sourcemodule = getQueryParam('sourcemodule');
    var sourceid = getQueryParam('sourceid');

    // Check if both parameters are present and valid
    if (sourcemodule && sourceid) {
        // Call the desired jQuery function, passing the parameters if needed
        getaccountname(sourceid,sourcemodule);
    }
  // Create a MutationObserver to detect changes to the input vendor account
var targetNode_v = document.getElementById("related_to_id1");
var observer = new MutationObserver(function (mutationsList) {
  for (var mutation of mutationsList) {
    if (
      mutation.type === "attributes" &&
      mutation.attributeName === "value"
    ) {
      console.log("rel value changed to:", targetNode_v.value);
      // alert("inspection_location value changed to:", targetNode_v.value);
      var related_to = document.getElementById("related_to").value;

      getaccountname(targetNode_v.value,related_to);
    }
  }
});

// Configuration for the observer (observe attribute changes)
var config = { attributes: true };
observer.observe(targetNode_v, config);
});

function getaccountname(related_to_id,related_to) {
  data = {
    related_to: related_to,
    related_to_id: related_to_id,
    _csrf: $("#csrfToken").val(),
  };
   $("#account_name1").val('');
       $("#account_name").val('');
       $("#account_name").attr("readonly",false);
        $(".section-account_name").find("#removeTextValue").css("display","block");
       $(".section-account_name").find("#showCustomer1").css("display","block");

  $.ajax({
    type: "POST",
    url: "getaccountname",
    // async:false,
    data: data,
    success: function (response) {

      // Check if the data object exists and contains 'first_name'
      if (response && response.data) {
      //  $("#account_name1").val(response.data.vendoraccid);
      /*$("#account_name1").val(response.data.vendor);
       $("#account_name").val(response.data.acc_name);
       //make account name readonly
       $("#account_name").attr("readonly",true);
       $(".section-account_name").find("#removeTextValue").css("display","none");
       $(".section-account_name").find("#showCustomer1").css("display","none");*/
       // Case 1: valid data received

            $("#account_name1").val(response.data.vendor || "");
            $("#account_name").val(response.data.acc_name || "")
                              .attr("readonly", true);

            $(".section-account_name #removeTextValue").hide();
            $(".section-account_name #showCustomer1").hide();

            // v11-268 added code on date 23-02-2026
            $(".section-account_name").show();
            $("#account_name").removeClass("V~O").addClass("V~M");

            $(".section-acc_name").hide();
            $("#acc_name").val("")
                          .removeClass("V~M")
                          .addClass("V~O");

        }
        // Case 2: data is empty
        else if (
            response &&
            response.status === "error"
        ) {

            $("#account_name1").val("");
            $("#account_name").removeClass("V~M").addClass("V~O");

            $(".section-account_name").hide();
            $(".section-acc_name").show();

            $("#acc_name").removeClass("V~O").addClass("V~M");

        }
        /**
         * v11 -268 added code on date 23-02-2026
         */
      else {
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

/**this code is added for v11 sheet point no 31 by ptpatel on date 14-10-2025 */
// Function to validate future times when type = Scheduled
function validateCallTimes() {
  console.log("validateCallTimes call");
  var call_status = $("#outgoing_call_status").val();
  var startVal = $("#call_start_time").val();
  var endVal = $("#call_end_time").val();

  console.log("call_status"+call_status+"startVal"+startVal+"endVal"+endVal);
  // get help-block elements
  const startHelp = $("#call_start_time").closest(".form-group").find(".help-block");
  const endHelp = $("#call_end_time").closest(".form-group").find(".help-block");

   // clear previous errors
  $("#call_start_time, #call_end_time").removeClass("error");
  startHelp.text("");
  endHelp.text("");

  if (call_status == 1) {
    console.log("in if ");
    var _now = new Date();
    var _start = new Date(startVal);
    var _end = new Date(endVal);

    let valid = true;

    if (startVal && _start <= _now) {
      console.log("in startVal if");
      $("#call_start_time").addClass("error");
       startHelp.text("This must be a Future Date and Time.").show();
      valid = false;
    }

    if (endVal && _end <= _now) {
      console.log("in endVal if");
       $("#call_end_time").addClass("error");
      endHelp.text("This must be a Future Date and Time.").show();
      valid = false;
    }

      //  Enable/disable save button based on result
      if (!valid) {
        $(".savebutton").prop("disabled", true);
      } else {
        $(".savebutton").prop("disabled", false);
      }

  return valid;
  }

  return true; // For other types, skip validation
}

function endtimeisgraterthanstart() {
  const startVal = $("#call_start_time").val();
  const endVal = $("#call_end_time").val();

  // get help-block elements
  const startHelp = $("#call_start_time").closest(".form-group").find(".help-block");
  const endHelp = $("#call_end_time").closest(".form-group").find(".help-block");

  // clear previous errors
  $("#call_start_time, #call_end_time").removeClass("error");
  startHelp.text("");
  endHelp.text("");

  if (!startVal || !endVal) {
    // if one of them is empty, skip this validation
    return true;
  }

  const startDate = new Date(startVal);
  const endDate = new Date(endVal);

  if (endDate <= startDate) {
    $("#call_end_time").addClass("error");
    endHelp.text("End time must be greater than start time.").show();
    return false;
  }

  // all good
  return true;
}

function validateCallTimesCombined() {
  console.log("validateCallTimes call");
  var call_status = $("#outgoing_call_status").val();
  var startVal = $("#call_start_time").val();
  var endVal = $("#call_end_time").val();

  console.log("call_status"+call_status+"startVal"+startVal+"endVal"+endVal);
  // get help-block elements
  const startHelp = $("#call_start_time").closest(".form-group").find(".help-block");
  const endHelp = $("#call_end_time").closest(".form-group").find(".help-block");

   // clear previous errors
  $("#call_start_time, #call_end_time").removeClass("error");
  startHelp.text("");
  endHelp.text("");

    let valid = true;
    
    var _now = new Date();
    var _start = new Date(startVal);
    var _end = new Date(endVal);
  if (call_status == 1) {
    console.log("in if ");
    if (startVal && _start <= _now) {
      console.log("in startVal if");
      $("#call_start_time").addClass("error");
       startHelp.text("This must be a Future Date and Time.").show();
      valid = false;
    }

    if (endVal && _end <= _now) {
      console.log("in endVal if");
       $("#call_end_time").addClass("error");
      endHelp.text("This must be a Future Date and Time.").show();
      valid = false;
    }

  }
  if (!_start || !_end) {
    // if one of them is empty, skip this validation
    valid =  true;
  }

  if (_end <= _start) {
    $("#call_end_time").addClass("error");
    endHelp.text("End time must be greater than start time.").show();
    valid =  false;
  }
   //  Enable/disable save button based on result
      if (!valid) {
        $(".savebutton").prop("disabled", true);
      } else {
        $(".savebutton").prop("disabled", false);
      }

  return true;
}


// Trigger validation when call type changes
$(document).on("change", "#outgoing_call_status,#call_start_time,#call_end_time", function () {
  // validateCallTimes();
  // endtimeisgraterthanstart();
  validateCallTimesCombined();
});
/**end code here for v11 sheet point no 31 by ptpatel on date 14-10-2025 */
/**
 * v11 -268 added code on date 23-02-2026
 */
$(document).ready(function () {
  // $(document).on("change", "#related_to", function () {
   const modeInput = document.getElementById("mode");
    let relatedtofield = $("#related_to").val();
    let relatedtoid1 = $("#related_to_id1").val();
    let account_name = $("#account_name1").val();
    let acc_name = $("#acc_name").val();
    console.log("modeInput"+modeInput.value);
    if(relatedtofield == "7" && modeInput && modeInput.value === "Edit")
    {     
      if (!account_name && acc_name) 
      {
          $(".section-account_name").hide();
          $(".section-acc_name").show();
          $("#acc_name").removeClass("V~O").addClass("V~M");
      }else{
        console.log("getaccount name called");
       getaccountname(relatedtoid1,relatedtofield); 
      }     
    }
  // });
});
/**
 * v11 -268 end code added on date 23-02-2026
 */
