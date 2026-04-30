$(document).ready(function () {
  var newURL = window.location.href;
  var newURL = window.location.href;
  var module = "leads";
  var str = newURL.split(module);
  console.log("str" + str[0]);
  // var slicestr=newURL.substring(0,str);
  editusrl = str[0] + "leads/list";
  console.log("url" + editusrl);

 //code added by ptpatel to resolve staging issue on date 30-06-25
// below code copy from convertlead popup 
// });
$(document).on("click", ".btn-close-convertlead", function () {
  window.location.reload();
});
$(document).on("click", ".convert-btn", function () {
  console.log("Convert button clicked");

  waitForElement("#account_category", function ($el) {
    console.log("#account_category found");

    const $select = $($el);

    if (typeof $.fn.select2 === 'function') {
      if ($select.hasClass('select2-hidden-accessible')) {
        $select.select2('destroy');
      }

      $select.select2({
        placeholder: "Select a category",
        allowClear: true,
        width: '100%'
      });

      console.log("Select2 initialized on #account_category");
    } else {
      console.warn("Select2 plugin not loaded!");
    }
  });
  //code added by ptpatel on date 31-01-2026 for account validation
  waitForElement("#create_account", function ($el) {
    console.log("#account_category found");

    const $checkbox  = $($el);

     if ($checkbox.is(":checked")) {
        validateDealName();
    }
    
  });
  // end code added by ptaptel on date 31-01-2026
});


// Utility to poll until element exists
function waitForElement(selector, callback, maxAttempts = 50, interval = 100) {
  let attempts = 0;
  const checkExist = setInterval(function () {
    const el = document.querySelector(selector);
    if (el) {
      clearInterval(checkExist);
      callback(el);
    } else if (++attempts >= maxAttempts) {
      clearInterval(checkExist);
      console.warn(selector + " still not found after waiting.");
    }
  }, interval);
}

waitForElement("#contact_name1", function (el) {
  console.log("#contact_name1 is ready:", el);

  // Detect user input (just in case)
  el.addEventListener("input", function () {
    console.log("Input changed:", this.value);
    if(this.value)
    checkcontacts();
  });

  // Detect programmatic value changes (e.g., via AJAX)
  const originalSetter = Object.getOwnPropertyDescriptor(Object.getPrototypeOf(el), 'value').set;
  const originalGetter = Object.getOwnPropertyDescriptor(Object.getPrototypeOf(el), 'value').get;

  Object.defineProperty(el, 'value', {
    set: function (val) {
      originalSetter.call(this, val);
      console.log("Programmatically set:", val);
    if(this.value)
      checkcontacts();
    },
    get: function () {
      return originalGetter.call(this);
    }
  });
});


function checkcontacts() {
            $(".savebutton").attr("disabled", true);
            var create_contact = $("#create_contact").prop("checked");
            var contact = $("#contact_name1").val();
            if ($('#vendor_name1').length)         // use this if you are using id to check
            {
                var account = $("#vendor_name1").val();
            }
            else if ($('#vendor1').length) {
                var account = $("#vendor1").val();
            }


            // alert(account);
            //check contact with account
            data = { contact: contact, vendor_account_name: account, _csrf: $("#csrfToken").val() };

            // alert("choose_contact "+choose_contact);
            if (choose_contact) {
                $("#contact_name1").addClass("V~M");
                $("#contact_name").addClass("V~M");
                $("#first_name").removeClass("V~M");
                $("#last_name").removeClass("V~M");
                isValid = false;
                $.ajax({
                    type: "POST",
                    url: "checkcontact",
                    // async:false,
                    data: data,
                    success: function (data) {
                        //location.reload();
                        $(".cont-alert").text('');
                        // alert(data.data);
                        var msg = '';
                        isValid = false;
                        if (data.data === 'matched') {
                            isValid = true;
                            // alert(isValid);
                            $(".savebutton").attr("disabled", false);

                        }
                        else {
                            msg = 'Specified Contact must be parented by specified Account';
                            isValid = false;
                        }
                        $(".cont-alert").text(msg);

                    },
                    error: function (data) {
                        // if error occured
                        isValid = false;
                        alert("Error occured.please try again");
                    },
                    dataType: "json",
                });
            }
            else {
                $(".savebutton").attr("disabled", false);

                $("#contact_name1").removeClass("V~M");
                $("#contact_name").removeClass("V~M");
                $("#first_name").addClass("V~M");
                $("#last_name").addClass("V~M");
            }

        }
        //end check contacts
        // Create a MutationObserver to detect changes to the input vendor account
        document.addEventListener("DOMContentLoaded", function () {
          var targetNode = document.getElementById("contact_name1");
          var observer = new MutationObserver(function (mutationsList) {
              for (var mutation of mutationsList) {
                  if (
                      mutation.type === "attributes" &&
                      mutation.attributeName === "value"
                  ) {
                      if (targetNode.value)
                          checkcontacts();

                      console.log("contact_name1 value changed to:", targetNode.value);
                  }
              }
          });
           // Configuration for the observer (observe attribute changes)
        var config = { attributes: true };
        observer.observe(targetNode, config);
        


       

       
        // alert("1");
        if ($("#create_account").length) {
            var create_account = $("#create_account").prop("checked");
            if (create_account) {
                $("#vendor_name1").removeClass("V~M");
                $("#vendor_name").removeClass("V~M");
                $("#deal_name").addClass("V~M");

                //disable choose contact
                $("#choose_contact").attr("disabled", true);
                $(".chooseontact").addClass("tr-hidden");
            }
        }
         });
        // alert("2");
        // jQuery to detect when a radio button is checked
        // $('input[type="radio"][id="create_account"]').change(function () {
        
         $(document).on("click",'input[type="radio"][id="create_account"]',function () {
            if ($(this).is(':checked')) {
                console.log('Radio button checked!');
                // Perform your logic here
                $("#vendor_name1").removeClass("V~M");
                $("#vendor_name").removeClass("V~M");
                $("#deal_name").addClass("V~M");

                //disable choose contact
                var contacts_id = "<?php echo $contacts_id ?>";
                if (!contacts_id)
                    $("#create_contact").prop("checked", true);
                $("#choose_contact").prop("checked", false);
                $("#choose_contact").attr("disabled", true);
                $(".chooseontact").addClass("tr-hidden");
                $("#contact_name1").val('');
                $("#contact_name").val('');
                 //////add mandatory from account category
                 $("#account_category").addClass("V~M").removeClass("V~O");
            }
        });

        // $('input[type="radio"][id="choose_account"]').change(function () {
         $(document).on("click",'input[type="radio"][id="choose_account"]',function () {
            if ($(this).is(':checked')) {
                console.log('Radio button checked!');
                // Perform your logic here
                //check if vendor is blank or not
                $("#vendor_name1").addClass("V~M");
                $("#vendor_name").addClass("V~M");
                $("#deal_name").removeClass("V~M");
                //enable choose contact
                var contacts_id = "<?php echo $contacts_id ?>";
                if (!contacts_id)
                    $("#create_contact").prop("checked", true);
                $("#choose_contact").prop("checked", false);
                $("#choose_contact").attr("disabled", false);
                $(".chooseontact").removeClass("tr-hidden");
                $("#contact_name1").val('');
                $("#contact_name").val('');

                //////remove mandatory from account category
                $("#account_category").addClass("V~O").removeClass("V~M");


            }
        });

        //$('input[type="radio"][id="create_contact"]').change(function () {
        $(document).on("click",'input[type="radio"][id="create_contact"]',function () {
            if ($(this).is(':checked')) {
                $(".savebutton").attr("disabled", false);

                console.log('Radio button checked!');
                // Perform your logic here
                //blank contact is blank or not
                $("#contact_name").removeClass("V~M");
                $("#contact_name1").removeClass("V~M");
                //enable choose contact

                $("#contact_name1").val('');
                $("#contact_name").val('');

                $("#first_name").addClass("AN~M");
                $("#last_name").addClass("AN~M");

            }
        });

        // $('input[type="radio"][id="choose_contact"]').change(function () {
          $(document).on("click",'input[type="radio"][id="choose_contact"]',function () {
            if ($(this).is(':checked')) {
                $(".savebutton").attr("disabled", true);

                console.log('Radio button checked!');
                // Perform your logic here
                //blank contact is blank or not
                $("#contact_name").addClass("V~M");
                $("#contact_name1").addClass("V~M");
                //enable choose contact

                $("#first_name").removeClass("AN~M");
                $("#last_name").removeClass("AN~M");

            }
        });

        //

        const validator = new Validator();

        //$(".form-control, input[type='radio'], input[type='file'], input[type='checkbox'], .leave").on("change", function () { //alert('dsfs');
        $(document).on("change",".form-control, input[type='radio'], input[type='file'], input[type='checkbox'], .leave" , function () {
        if ($(this).is(":visible") || $(this).hasClass("leave")) {
                validator.validateField($(this));
            }
        });

        $(document).on("click", ".savebutton", function (e) {
            let isValid = true;

            // var contacts_id = "<?php echo $contacts_id ?>";
            var contacts_id = $("#contacts_id").val();
            // alert(contacts_id);

            //check if contact is selected of same account
            //Specified Contact must be parented by specified Account

            // $("#exchange_rate").val('');
            isValid = false;
            $(".cont-alert").text('');

            //check if choose account is checked
            var choose_account = $("#choose_account").prop("checked");
            var create_account = $("#create_account").prop("checked");
            // alert(choose_account);
            if (choose_account) {
                //alert(choose_account);
                //check if vendor is blank or not
                $("#vendor_name1").addClass("V~M");
                $("#vendor_name").addClass("V~M");
                $("#deal_name").removeClass("V~M");
                //enable choose contact
                $("#choose_contact").attr("disabled", false);

            }
            else if (create_account) {
                $("#vendor_name1").removeClass("V~M");
                $("#vendor_name").removeClass("V~M");
                $("#deal_name").addClass("V~M");

                //disable choose contact
                $("#choose_contact").attr("disabled", true);
            }
            // Check if choose_contact is selected
            var choose_contact = $("#choose_contact").prop("checked");
            var create_contact = $("#create_contact").prop("checked");

            // alert("choose_contact "+choose_contact);
            if (choose_contact) {
                $("#contact_name1").addClass("V~M");
                $("#contact_name").addClass("V~M");
                $("#first_name").removeClass("AN~M");
                $("#last_name").removeClass("AN~M");
                // isValid = false;
            }
            else {
                $("#contact_name1").removeClass("V~M");
                $("#contact_name").removeClass("V~M");
                $("#first_name").addClass("AN~M");
                $("#last_name").addClass("AN~M");
            }
            // alert("create_contact "+create_contact);
            // alert("choose_contact "+choose_contact);
            // alert("contacts_id "+contacts_id);
            // alert("1 " + isValid);
            if (contacts_id !== '' && (create_contact || choose_contact)) {
                if (confirm('Contact already exist with this number, please verify or proceed without creating contact')) {
                    $("#create_contact").prop("checked", false);
                    $("#choose_contact").prop("checked", false);
                    $("#contact_name1").removeClass("V~M");
                    $("#contact_name").removeClass("V~M");
                    isValid = false;
                }
                else
                    isValid = false;

                $(".cont-alert").text('Contact already exist with this number, please verify or proceed without creating contact');
            }
            else {
                isValid = true;
            }
            // alert("2 " + isValid);

            $(".form-control, input[type='radio'], input[type='file'], input[type='checkbox'], .leave").each(function () {
                if ($(this).is(":visible") || $(this).hasClass("leave")) {
                    if (!validator.validateField($(this))) {
                        isValid = false;
                    }
                }
            });
            // alert("last " + isValid);
            if (!isValid) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: $(".help-block:visible:first").offset().top
                }, 500);
            } else {
                // alert(isValid);

                $("#pristine-valid-example").submit();
            }
        });
        // Make sure the Validator object exists and validateField is a function
        console.log(window.Validator); // This should display the Validator object in console
        if (typeof window.Validator !== "undefined") {
          window.Validator.validateField($('leadinformation[firstname]')); // Replace with an actual field
        }
        $(document).on("blur change",".form-control", function () {
            // Check if Validator.validateField is available and call it
            if (typeof window.Validator.validateField === "function") {
                window.Validator.validateField($(this));
            } else {
                console.error("Validator.validateField is not a function.");
            }
        });

        //end code added by ptpatel on date 30-06-25
  // $(document).on('click', '.savebutton', function (e) {
    

  //   var isValid = true;
  //   console.log("teregdfg fh");




  //   if (valid && isValid) {
  //     //form.submit();
  //   }


  // });


document.querySelectorAll(".accordion-toggle").forEach((button) => {
  button.addEventListener("click", () => {
    const content = button
      .closest(".accordion-item")
      .querySelector(".accordion-content");
    const upArrow = button.querySelector(".up");
    const downArrow = button.querySelector(".down");
    if (content.style.display === "block") {
      content.style.display = "none"; // Hide content
      upArrow.style.display = "none"; // Hide up arrow
      downArrow.style.display = "inline"; // Show down arrow
    } else {
      content.style.display = "block"; // Show content
      upArrow.style.display = "inline"; // Show up arrow
      downArrow.style.display = "none"; // Hide down arrow
    }
  });
});
// Tab Switching Logic
document.querySelectorAll(".tab").forEach((tab) => {
  tab.addEventListener("click", function () {
    // Remove active class from all tabs and contents
    document
      .querySelectorAll(".tab")
      .forEach((t) => t.classList.remove("active"));
    document
      .querySelectorAll(".tab-content-detail-view")
      .forEach((content) => content.classList.remove("active"));
    // Add active class to clicked tab and corresponding content
    this.classList.add("active");
    const tabId = this.getAttribute("data-tab");
    document.getElementById(tabId).classList.add("active");
  });
});
 

// get exchangerate
// Initialize Select2 for all dropdowns
$('#currency').select2();
 // Listen for the change event on select2
//  $('#currency').on('select2:select', function (e) {
// Listen for the change event on select2
$(document).on("change", "#currency", function (e) {
  var selectedValue = e.target.value;  // Get the selected value
  console.log("Selected value: ", selectedValue);
  data = { currency: selectedValue, _csrf: $("#csrfToken").val() };
  $("#exchange_rate").val('');

  $.ajax({
    type: "POST",
    url: "getexchangerate",
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
});
//end exchange rate

document.addEventListener("DOMContentLoaded", function () {
  // Check if mode is 'Create'
  const modeInput = document.getElementById("mode");
  if (modeInput && modeInput.value === "Create") {
    // Select the dropdown by ID
    const leadStatusSelect = $("#leadstatus"); // Using jQuery for Select2

    //show only lead created  added on 16 jan 2025 by deepika
    // Hide all options except the one with a specific value
    $('#leadstatus option').each(function() {
      if ($(this).val() != '1' ) { // Show only the option with value "1" = lead created
          $(this).remove(); // Remove options that don't match
      }
    });
    // initialize currency with INr
    $('#currency').val("1").trigger("change");
    //end ddepika

    if (leadStatusSelect.length) {
      // Set the default value for Select2 dropdown
      leadStatusSelect.val("1").trigger("change"); // Use the value corresponding to "New Lead Created"
    }
  }
});

 ////////get vendor name and KYC//////////////
   // Create a MutationObserver to detect changes to the input vendor account
   document.addEventListener("DOMContentLoaded", function () {
   var targetNode = document.getElementById("vendor1");
   var observer = new MutationObserver(function (mutationsList) {
     for (var mutation of mutationsList) {
       if (
         mutation.type === "attributes" &&
         mutation.attributeName === "value"
       ) {
         getvendordetail();
         console.log("vendor1 value changed to:", targetNode.value);
       }
     }
   });
 
     // Configuration for the observer (observe attribute changes)
     var config = { attributes: true };
     observer.observe(targetNode, config);
  });
     function getvendordetail() {
       data = {
        account_name: $("#vendor1").val(),
         _csrf: $("#csrfToken").val(),
       };

      //  $("#noofemployees").val('');
      
       $('#industry').val(null).trigger('change');
      //  $("#annualrevenue").val('');
   
       $.ajax({
         type: "POST",
         url: "getvendordetail",
         // async:false,
         data: data,
         success: function (response) {
           console.log(response); // Log the entire response to check its structure
   
           // Check if the data object exists and contains 'first_name'
           if (response && response.data) {
            // $("#noofemployees").val(response.data.empsize_value);
            $("#industry").val(response.data.industry);
            $('#industry').val(response.data.industry).trigger('change');
            // $("#annualrevenue").val(response.data.annual_revenue);
           
             
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
     //////////////change event on customer type//////////
     $(document).on("change",'#customer_type', function (e) {
      var selectedValue = e.target.value;  // Get the selected value
      console.log("Selected value: ", selectedValue);
      showcutomermandatory(selectedValue);
    });
    // check customer name
    cutomertype = $("#customer_type").val();
    showcutomermandatory(cutomertype);

    function showcutomermandatory(selectedValue)
    {
      if(selectedValue == 1)//new customer 
      {
        $("#vendor").removeClass('V~M');
        $(".section-send_for_approval").addClass("tr-hidden");
        //also hide vendor account
        $(".section-vendor").addClass("tr-hidden");
        $("vendor1").val('');
        $("vendor").val('');
         //show account name
        $(".section-account_name ").removeClass("tr-hidden");
        // Uncheck the checkbox
        $("#send_for_approval").prop("checked", false);

        // Remove the asterisk based on a condition
        toggleAsterisk(false);  // This will remove the asterisk
      }
      else
      {
        $("#vendor").addClass('V~M');
        $(".section-send_for_approval").removeClass("tr-hidden");
        //show vendor account
        $(".section-vendor").removeClass("tr-hidden");
        //hide account name
        $(".section-account_name ").addClass("tr-hidden");
        $("account_name").val('');
        // Add the asterisk based on a condition
        toggleAsterisk(true);  // This will add the asterisk
      }
    }
    // Function to toggle the asterisk
function toggleAsterisk(shouldAdd) {
  var label = $('label[for="vendor"]');
  var asterisk = label.find('span.red'); // Check if the span with the red class exists

  if (shouldAdd && asterisk.length === 0) {
    // Add the asterisk if the condition is met and the span doesn't already exist
    label.append('<span class="red"> *</span>');
  } else if (!shouldAdd && asterisk.length > 0) {
    // Remove the asterisk if the condition is not met and the span exists
    asterisk.remove();
  }
}
 //////////////change event on first name last name set lead name//////////
 $(document).on("change",'#firstname', function (e) {
 
  setleadname();
});
$(document).on("change",'#lastname', function (e) {
  setleadname();
});
function setleadname()
{
  var first_name =$('#firstname').val();  // Get the selected value
  var last_name =$('#lastname').val();  // Get the selected value
  leadname = first_name+' '+last_name;
  $('#leadname').val(leadname);

}
///////////////on save button click//////
// $(".savebutton").on("click", function (e) {
//   // alert("deep");
//   $("#vendor").removeClass("V~M");
//   var vendor1 = $("#vendor1").val();
//   var account_name = $("#account_name").val();
//   if(account_name == '' && vendor1 == '')
//   {
//     errorMessage = "Please Select an Account or Add an Account Name! ";
//     // alert("Select a account or Add a Account Name!");
//     var errorElement = $("#vendor1").closest(".form-group").find(".help-block");
//     errorElement.html(errorMessage); // Replace errorMessage with the actual message
//     alert(errorElement.html());
   
//     var errorElement = $("#account_name").closest(".form-group").next(".help-block");
//     errorElement.html(errorMessage); // Replace errorMessage with the actual message
//   }
//   return false;
// });
    });
//code added by ptpatel on date 30-01-2026  for account name validation
$(document).on('shown.bs.modal', '#modalBody', function () {
  console.log("addLeadModalLabel call");
    if ($("#create_account").is(":checked")) {
        validateDealName(); //  auto-run
    }
});
// Watch choose_account checkbox
$(document).on("change", "#choose_account", function () {
    var $checkbox = $(this);

    // If checked, remove deal_name error
    if ($checkbox.is(":checked")) {
        var $dealFormGroup = $("#deal_name").closest(".form-group");
        $dealFormGroup.removeClass("error");
        $dealFormGroup.find(".help-block").text("");
    }

    toggleSaveButton();
});

// Blur event for deal_name
$(document).on("blur", "#deal_name", function () {
    validateDealName();
});
$(document).on("change", "#create_account", function () {
    if (this.checked) {
        validateDealName(); //  force validation
    }
});


function validateDealName() {

    var $input = $("#deal_name");
    var value = $input.val().trim();

    var $formGroup = $input.closest(".form-group");
    var $helpBlock = $formGroup.find(".help-block");

    if (value === "") {
        $formGroup.removeClass("error");
        $helpBlock.text("");
        toggleSaveButton();
        return;
    }

    // Skip ONLY when choose_account is checked and create_account is NOT
    if ($("#choose_account").is(":checked") && !$("#create_account").is(":checked")) {
        $formGroup.removeClass("error");
        $helpBlock.text("");
        toggleSaveButton();
        return;
    }

    const urlParams = new URLSearchParams(window.location.search);
    const recordid = urlParams.get('Record');

    startLoading();

    $.ajax({
        url: "isaccountduplicate",
        type: "POST",
        data: {
            field: "deal_name",
            value: value,
            recordid: recordid,
            _csrf: yii.getCsrfToken()
        },
        success: function (res) {
            if (res.exists) {
                $formGroup.addClass("error");
                if ($helpBlock.length === 0) {
                    $formGroup.append('<div class="help-block"></div>');
                    $helpBlock = $formGroup.find(".help-block");
                }
                $helpBlock.text(value + " already exists! Please choose an existing account.");
            } else {
                $formGroup.removeClass("error");
                if ($helpBlock.text().includes("already exists")) {
                    $helpBlock.text("");
                }
            }
            toggleSaveButton();
        },
        error: function () {
            $formGroup.addClass("error");
            toggleSaveButton();
        },
        complete: function () {
            stopLoading();
        }
    });
}

// Toggle Save button
function toggleSaveButton() {
    // If choose_account is checked, enable button no matter what
    if ($("#choose_account").is(":checked")) {
        $(".savebutton").prop("disabled", false);
        return;
    }

    // Count only visible errors
    var hasVisibleErrors = $(".form-group.error:visible").length > 0;
    var hasRequiredErrors = $(".help-block:visible:contains('required')").length > 0;

    if (hasVisibleErrors || hasRequiredErrors) {
        $(".savebutton").prop("disabled", true);
    } else {
        $(".savebutton").prop("disabled", false);
    }
}
    //code added by ptpatel on date 30-01-2026 