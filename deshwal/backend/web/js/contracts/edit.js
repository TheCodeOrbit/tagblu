$(document).ready(function () {
  const stageSelect = document.getElementById("contract_status");

  if (stageSelect) {
  // By default set to Draft if in Create mode
  const modeInput = document.getElementById("mode");
  if (modeInput && modeInput.value === "Create") {
    $("#contract_status").val("1").trigger("change");
  }

  // Get the updated stage value
  const stage = parseInt($("#contract_status").val() || "0");

  // Disable all options except current stage and conditional "8"
  const options = stageSelect.options;
  for (let i = 0; i < options.length; i++) {
    const value = options[i].value;

    // Always enable current stage
    if (parseInt(value) === stage) {
      options[i].disabled = false;
    }
    // Enable "8" if stage > 3 and not 5(changes required)
    // unsigend is enable when once contract was approved previously 
    else if (value === "8" && stage > 3 && stage !== 5) {
      options[i].disabled = false;
    }
    // Disable everything else
    else {
      options[i].disabled = true;
    }
  }
}


  // Trigger the logic that handles field visibility and other controls
  toggleBlocks();

  // Update toggle on dropdown change
  $("#contract_status").on("change", function () {
    toggleBlocks();
  });

  // Handle auto-fill for contract status when account is fetched
  const modeInput = document.getElementById("mode");
  if (modeInput && modeInput.value === "Create") {
    getaccountdetail();

    setTimeout(() => {
      flatpickr("#created_date", {
        defaultDate: new Date(),
        dateFormat: "d-m-Y"
      });
    }, 500);
  }


  // Check again on dropdown value change
  $("#contract_status").on("change", function () {
    toggleBlocks();
  });
  // when status = 1 (draft) or 2 (in review) hide signin block and 
  function toggleBlocks() {
    let contract_status = parseInt($("#contract_status").val() || "0");
    const modeInput = document.getElementById("mode");

    if (modeInput && modeInput.value === "Create") {
      $("#contract_status_hidden").val(1); //1= draft
    }

    // once contract is reviewed then disable send_for_review checkbox
    if ($("#send_for_review").is(':checked')) {
      $("#send_for_review").prop("disabled", true);
    }

    // once contract is signed and attached then disable contract_attached checkbox
    if ($("#contract_attached").is(':checked')) {
      $("#contract_attached").prop("disabled", true);
    }

    if (contract_status >= 3  && contract_status != 5 && contract_status != 4) {
        $("#contract_attached").removeClass("CKB~O").addClass("CKB~M");
        $(".row213").show();
    }
    else {
        $(".row213").hide();
    }

    console.log("toggle" + contract_status);
    if (contract_status == "7") {
      $("#activated_date").prop("disabled", false)
        .removeClass("DDT~O")
        .addClass("DDT~M");

      $("#activated_by").prop("disabled", false)
        .removeClass("V~O")
        .addClass("V~M");
    } 
    else if(contract_status == "8")
      {
        $("#activated_date").prop("disabled", true).removeClass("DDT~M").addClass("DDT~O");
        $("#activated_by").prop("disabled", true).removeClass("V~M").addClass("V~O");
        $('#activated_date').next('.help-block').text('');
        $('#activated_by').closest('.form-group').find('.help-block').text('');
      }
      else {
      $("#activated_date").prop("disabled", true);
      $("#activated_by").prop("disabled", true);
      
    }
  }

  /////aprrove////////////
  $(document).on("click", "#approvesubmit", function () {
    let data = {
      Recordid: $("#Recordid").val(),
      _csrf: $("#csrfToken").val(),
      approve_reason: $("#approve_comment").val(),
    };
    if ($("#approve_comment").val() == "") {
      alert("Please enter comment!");
      $("#approve_comment").focus();
      return false;
    }

    $.ajax({
      type: "POST",
      url: "approvecontract",
      data: data,
      success: function (data) {
        if (data.status === "success") location.reload();
        else alert("sometinhg went wrong");
      },
      error: function (data) {
        alert("Error occured.please try again");
      },
      dataType: "json",
    });

  });
  //////reject/////////////
  $(document).on("click", "#rejectsubmit", function () {
    //alert("dfhfdhd");
    data = {
      Recordid: $("#Recordid").val(),
      _csrf: $("#csrfToken").val(),
      leadstatus_m: $("#leadstatus_m").val(),
      reject_reason: $("#reject_comment").val(),
    };
    // {leadstatus_v:$("#leadstatus_v").val(),Recordid: $('#Recordid').val();,approve_reason:$("#approve_reason").val();, _csrf: $('#csrfToken').val();};
    if ($("#reject_comment").val() == "") {
      alert("Please enter comment!");
      $("#reject_comment").focus();
    } else {
      $.ajax({
        type: "POST",
        url: "approvecontract",
        // async:false,
        data: data,
        success: function (data) {
          if (data.status === "success") location.reload();
          else alert("sometinhg went wrong");
        },
        error: function (data) {
          // if error occured

          alert("Error occured.please try again");
        },
        dataType: "json",
      });
    }

  });
});

// Create a MutationObserver to detect changes to the input vendor account
var targetNode = document.getElementById("account_name1");
var observer = new MutationObserver(function (mutationsList) {
  for (var mutation of mutationsList) {
    if (mutation.type === "attributes" && mutation.attributeName === "value") {
      getaccountdetail();
      console.log("vendor1 value changed to:", targetNode.value);
    }
  }
});

// Configuration for the observer (observe attribute changes)
var config = { attributes: true };
observer.observe(targetNode, config);

// Create a MutationObserver to detect changes to the input contactt
var targetNode = document.getElementById("contact_person_name1");
var observer = new MutationObserver(function (mutationsList) {
  for (var mutation of mutationsList) {
    if (mutation.type === "attributes" && mutation.attributeName === "value") {
      getcontactdetail();
      console.log("contact_person_name1 value changed to:", targetNode.value);
    }
  }
});

// Configuration for the observer (observe attribute changes)
var config = { attributes: true };
observer.observe(targetNode, config);

// Create a MutationObserver to detect changes to the input hqcorporate_address
var targetNode = document.getElementById("hqcorporate_address1");
var observer = new MutationObserver(function (mutationsList) {
  for (var mutation of mutationsList) {
    if (mutation.type === "attributes" && mutation.attributeName === "value") {
      getaddressdetail();
      console.log("hqcorporate_address1 value changed to:", targetNode.value);
    }
  }
});

// Configuration for the observer (observe attribute changes)
var config = { attributes: true };
observer.observe(targetNode, config);

// Create a MutationObserver to detect changes to the input company_signed_by1
var targetNode = document.getElementById("company_signed_by1");
var observer = new MutationObserver(function (mutationsList) {
  for (var mutation of mutationsList) {
    if (mutation.type === "attributes" && mutation.attributeName === "value") {
      getuserdetail1();
      console.log("company_signed_by1 value changed to:", targetNode.value);
    }
  }
});

// Configuration for the observer (observe attribute changes)
// var config = { attributes: true };
// observer.observe(targetNode, config);

// // Create a MutationObserver to detect changes to the input customer_signed_by1
// var targetNode = document.getElementById("customer_signed_by1");
// var observer = new MutationObserver(function (mutationsList) {
//   for (var mutation of mutationsList) {
//     if (mutation.type === "attributes" && mutation.attributeName === "value") {
//       getcustdetail();
//       console.log("customer_signed_by1 value changed to:", targetNode.value);
//     }
//   }
// });

// Configuration for the observer (observe attribute changes)
// var config = { attributes: true };
// observer.observe(targetNode, config);

function getaccountdetail() {
  data = {
    account_name: $("#account_name1").val(),
    _csrf: $("#csrfToken").val(),
  };

  $.ajax({
    type: "POST",
    url: "getaccountdetail",
    data: data,
    success: function (response) {
      console.log("account names", response);

      if (response && response.data) {
        $("#account_category").val(response.data.account_category);
        $("#account_code").val(response.data.account_code);

        $("#bankaccount_number").val(response.data.bankaccount_number);
        $("#bank_name").val(response.data.bank_name);
        $("#ifsc_code").val(response.data.ifsc_code);
        $("#payment_terms").val(response.data.payment_terms);
        $("#swift_code").val(response.data.swift_code);
        // below if condition added by ptpatel mode is create than autofill contract status as draft
        let modeInput = document.getElementById("mode");
        if (modeInput && modeInput.value === "Create") {
          $("#contract_status").val("1").trigger("change");
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

function getcontactdetail() {
  data = {
    contact_person_name: $("#contact_person_name1").val(),
    _csrf: $("#csrfToken").val(),
  };

  $.ajax({
    type: "POST",
    url: "getcontactdetail",
    data: data,
    success: function (response) {
      if (response && response.data) {
        $("#contact_email").val(response.data.contact_email);
        $("#contact_designation").val(response.data.contact_designation);
        $("#contact_phone_number").val(response.data.contact_phone_number);
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

function getaddressdetail() {
  data = {
    hqcorporate_address: $("#hqcorporate_address1").val(),
    _csrf: $("#csrfToken").val(),
  };

  $.ajax({
    type: "POST",
    url: "getaddressdetail",
    data: data,
    success: function (response) {
      if (response && response.data) {
        $("#city").val(response.data.city);
        $("#state").val(response.data.state);
        $("#gst_Number").val(response.data.gst);
        $("#pan_number").val(response.data.pan);
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
function getuserdetail1() {
  data = {
    userid: $("#company_signed_by1").val(),
    _csrf: $("#csrfToken").val(),
  };

  $.ajax({
    type: "POST",
    url: "getuserdetail",
    data: data,
    success: function (response) {
      if (response && response.data) {
        $("#company_signed_title").val(response.data.designation);
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
// function getcustdetail() {
//   data = {
//     userid: $("#customer_signed_by1").val(),
//     _csrf: $("#csrfToken").val(),
//   };

//   $.ajax({
//     type: "POST",
//     url: "getcustdetail",
//     data: data,
//     success: function (response) {
//       if (response && response.data) {
//         $("#customer_signed_title").val(response.data.designation);
//       } else {
//         console.log("Invalid response format or missing data");
//       }
//     },
//     error: function (data) {
//       // if error occured
//       alert("Error occured.please try again");
//     },
//     dataType: "json",
//   });
// }