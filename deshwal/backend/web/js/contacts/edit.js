$(document).ready(function () {
  var newURL = window.location.href;
  var newURL = window.location.href;
  var module = "leads";
  var str = newURL.split(module);
  console.log("str" + str[0]);
  // var slicestr=newURL.substring(0,str);
  editusrl = str[0] + "leads/list";
  console.log("url" + editusrl);

  const modeInput = document.getElementById("mode");
  if (modeInput && modeInput.value === "Create") {
    // initialize country with India
    $("#mailing_country").val("1").trigger("change");
    data = { country: $(this).val(), _csrf: $("#csrfToken").val() };
    ///on country change get state
    // alert($('#mailing_country').val());
    getstate($("#mailing_country"));
  }
  ///on country change get state
  $(document).on("change", "#mailing_country", function () {
    data = { country: $(this).val(), _csrf: $("#csrfToken").val() };

    getstate(this);
  });
  ///on state change get city
  $(document).on("change", "#mailing_state", function () {
    data = { state: $(this).val(), _csrf: $("#csrfToken").val() };

    getcity(this);
  });
  function getstate(thisobj) {
    // alert("test"+thisobj.value);
    const country = $("#mailing_country").val();
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    // Reset dropdowns

    const stateDropdown = $("#mailing_state")
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

    const cityDropdown = $("#mailing_city")
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
/* this part is commented becuse in contact module remove dependancy of industry designation and hierarchy_level on date 14-06-25
  ///on industry change get state
  $(document).on("change", "#industry", function () {
    data = { industry: $(this).val(), _csrf: $("#csrfToken").val() };

    gethierarchy(this);
  });


  $(document).on("change", "#hierarchy_level", function () {
    data = { hierarchy: $(this).val(), _csrf: $("#csrfToken").val() };

    getdesignation(this);
  });


  function gethierarchy(thisobj) {
    // alert("test"+thisobj.value);
    const industry = $("#industry").val();
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    // Reset dropdowns

    const hierarchyDropdown = $("#hierarchy_level")
      .empty()
      .append('<option value="">Select</option>');

    if (industry) {
      $.ajax({
        type: "POST",
        url: "gethierarchy",
        data: { industry: industry, _csrf: csrfToken },
        dataType: "json",
        success: function (response) {
          if (response.status === "success") {
            response.hierarchies.forEach((hierarchy) => {
              hierarchyDropdown.append(
                `<option value="${hierarchy.id}">${hierarchy.name}</option>`
              );
            });
            hierarchyDropdown.trigger("change"); // Update Select2 dropdown
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


  function getdesignation(thisobj) {
    // alert(thisobj.value);
    const hierarchy = thisobj.value;
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    // Reset dropdowns

    const designationDropdown = $("#designation")
      .empty()
      .append('<option value="">Select</option>');

    if (hierarchy) {
      $.ajax({
        type: "POST",
        url: "getdesignation",
        data: { hierarchy: hierarchy, _csrf: csrfToken },
        dataType: "json",
        success: function (response) {
          if (response.status === "success") {
            response.categories.forEach((designation) => {
              designationDropdown.append(
                `<option value="${designation.id}">${designation.name}</option>`
              );
            });
            designationDropdown.trigger("change"); // Update Select2 dropdown
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
  } */

  /*var form = document.getElementById("pristine-valid-example");
  var pristine = new Pristine(form);
  $(document).on('click', '.savebutton', function (e) {
    // alert("checking");
    // $('.savebutton').click(function(e){
    console.log("clicked");

    var isValid = true;
    console.log("teregdfg fh");




    var valid = pristine.validate();
    if (valid && isValid) {
      form.submit();
    }


  });*/

  // code added by ptpatel on selection of account industry will auto fill and readonly
    // Function to observe input value changes
  function observeInputChanges(inputElement) {
    const observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        if (
          mutation.type === "attributes" &&
          mutation.attributeName === "value"
        ) {
          console.log(
            `Value changed in ${inputElement.id}: ${inputElement.value}`
          );
            fillIndustry(`${inputElement.value}`);
        }
      });
    });

    observer.observe(inputElement, {
      attributes: true, // Observe attribute changes
      attributeFilter: ["value"], // Only watch 'value' attribute
    });

    console.log(`Observer attached to input: ${inputElement.id}`);
  }

  // Function to observe all matching inputs
  function observeMatchingInputs() {
    // Match inputs with ID pattern 'productid_*1'
    const inputs = document.querySelectorAll(
      'input[id^="vendor_account_name"]'
    );
    inputs.forEach((input) => observeInputChanges(input));
    console.log(`Observers attached to ${inputs.length} inputs.`);
  }

  // Function to monitor dynamically added inputs
  function monitorDynamicInputs() {
    const container = document.body; // Observe the entire document

    const observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        if (mutation.type === "childList" && mutation.addedNodes.length > 0) {
          mutation.addedNodes.forEach((node) => {
            if (node.nodeType === 1) {
              // Check for new matching inputs
              const newInputs = node.querySelectorAll(
                'input[id^="vendor_account_name"]'
              );
              // console.log("deepika");
              newInputs.forEach((input) => observeInputChanges(input));
            }
          });
        }
      });
    });

    observer.observe(container, {
      childList: true, // Detect added elements
      subtree: true, // Include all child elements
    });

    console.log("Monitoring dynamic inputs for pattern: product_name_1");
  }

  // Initialize observers for existing and dynamic inputs
  observeMatchingInputs();
  monitorDynamicInputs();
function fillIndustry(accountid){
  const csrfToken = $('meta[name="csrf-token"]').attr("content");
  $.ajax({
      type: "POST",
      url: "getindustry",
      data: { accountid: accountid, _csrf: csrfToken },
      success: function (data) {
        $("#industry").val(data.industry.industry).trigger('change');
        // stopLoading();
      },
      error: function (data) {
        alert("Error occured.please try again");
        // stopLoading();
      },
      dataType: "json",
    });
}
  // code added by ptpatel end here
});

//////////////////// on the class end validation code zitendra /////////////////////////

////////////////////end validation code zitendra /////////////////////////

/**code start for ERP finding 417 -Restrict duplicate contact creation based on email address and mobile number. */
function toggleSaveButton() {
    if ($(".form-group.error").length > 0 || $(".help-block:contains('required')").length > 0) {
        $(".savebutton").prop("disabled", true);
    } else {
        $(".savebutton").prop("disabled", false);
    }
}

/* //this code is commented by ptpatel because generalize duplicate functionality is used on date 02-03-2026
 $(document).on("blur", "#email,#mobile", function () {
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
        url: "checkexistemailormobile",   
        type: "POST",
        data: {
            field: field,
            value: value,
            _csrf: yii.getCsrfToken() // important in Yii2
        },
        success: function (res) {
            if (res.exists) {
              $formGroup.addClass("error");
                $helpBlock.text((field == 'mobile' ? field + " number" : field ) + " already exists!");
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
    });*/
    //do not allow to add space
$(document).on("keydown", "#username", function () {
  if (e.key === " ") {
    e.preventDefault();
  }
});
// remove space while user paste text
$(document).on("input", "#username", function () {
  $(this).val($(this).val().replace(/\s/g, ""));
});
/**code end for ERP finding 417 - Restrict duplicate contact creation based on email address and mobile number.*/

/** code start for ERP finding 403 - While creating the contact we are getting option to create User name and password but for existing contact we are not getting any option to create user name password for customer portal. */

$(document).ready(function () {
  const modeInput = document.getElementById("mode");
  if (modeInput && modeInput.value === "Edit") {
      let username_val = $("#username").val();
      let password_val = $("#password").val();
      console.log("password_val->"+password_val+"==username_val=>"+username_val);
        if (username_val === "" || password_val === "") {
            $(".section-username, .section-password").show();
        } else if(password_val != ""){
            $(".section-password").hide();
        }
        else{
            $(".section-password").hide();
        }
      }
});

/** code end for ERP finding 403 - While creating the contact we are getting option to create User name and password but for existing contact we are not getting any option to create user name password for customer portal. */
