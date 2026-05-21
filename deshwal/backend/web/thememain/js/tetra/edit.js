$.ajaxSetup({
    headers: {
        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
    }
});
// added on 3 Apr 2026 by deepika for handling hidden input of checkboxes
document.addEventListener('DOMContentLoaded', function () {

    document.querySelector('.savebutton').addEventListener('click', function () {

        // Handle all checkboxes before form submission
        document.querySelectorAll('input[type="checkbox"]').forEach(function (checkbox) {

            let name = checkbox.name;

            // Find the hidden input with same name
            let hidden = document.querySelector('input[type="hidden"][name="' + CSS.escape(name) + '"]');

            if (!hidden) return; // No hidden input exists

            if (checkbox.disabled) {
                // If checkbox is disabled → disable hidden input also (so form does not submit it)
                hidden.disabled = true;
            } else {
                // If checkbox is enabled → enable hidden input
                hidden.disabled = false;
            }

        });

    });

});
// added on 6 march 2026 by deepika
// Global file, loaded on all pages
$(document).ready(function () {
    $(document).on("click", "#approvesubmit", function (e) {
        var btn = $(this);
       if($("#approve_comment").val().trim() == '')
       {
         return false;   // stop execution if comment is empty
       }
        // Check if already clicked
        if (btn.data("clicked")) {
            // Already clicked, ignore this click but let AJAX proceed if already running
            return false;
        }

        // Mark as clicked and disable the button visually
        btn.data("clicked", true);
        btn.prop("disabled", true);
        btn.text("Submitting...");

        // Let all existing click handlers (like AJAX) run
    });
});
$(document).ready(function () {
    $(document).on("click", "#modifysubmit", function (e) {
      if($("#modify_comment").val().trim() == '')
       {
         return false;   // stop execution if comment is empty
       }
        var btn = $(this);

        // Check if already clicked
        if (btn.data("clicked")) {
            // Already clicked, ignore this click but let AJAX proceed if already running
            return false;
        }

        // Mark as clicked and disable the button visually
        btn.data("clicked", true);
        btn.prop("disabled", true);
        btn.text("Submitting...");

        // Let all existing click handlers (like AJAX) run
    });
});
$(document).ready(function () {
    $(document).on("click", "#rejectsubmit", function (e) {
        var btn = $(this);
        if($("#reject_comment").val().trim() == '')
       {
         return false;   // stop execution if comment is empty
       }
        // Check if already clicked
        if (btn.data("clicked")) {
            // Already clicked, ignore this click but let AJAX proceed if already running
            return false;
        }

        // Mark as clicked and disable the button visually
        btn.data("clicked", true);
        btn.prop("disabled", true);
        btn.text("Submitting...");

        // Let all existing click handlers (like AJAX) run
    });
});
$(document).ready(function () {
    $(document).on("click", "#rejectgeneralsubmit", function (e) {
       if($("#reject_general_comment").val().trim() == '')
       {
         return false;   // stop execution if comment is empty
       }
        var btn = $(this);

        // Check if already clicked
        if (btn.data("clicked")) {
            // Already clicked, ignore this click but let AJAX proceed if already running
            return false;
        }

        // Mark as clicked and disable the button visually
        btn.data("clicked", true);
        btn.prop("disabled", true);
        btn.text("Submitting...");

        // Let all existing click handlers (like AJAX) run
    });
});
$(document).ready(function () {
    $(document).on("click", "#reactivatesubmit", function (e) {
        if($("#reactivate_comment").val().trim() == '')
       {
         return false;   // stop execution if comment is empty
       }
        var btn = $(this);

        // Check if already clicked
        if (btn.data("clicked")) {
            // Already clicked, ignore this click but let AJAX proceed if already running
            return false;
        }

        // Mark as clicked and disable the button visually
        btn.data("clicked", true);
        btn.prop("disabled", true);
        btn.text("Submitting...");

        // Let all existing click handlers (like AJAX) run
    });
});
// timepicker
// $('.timepicker').timepicker({
//   showMeridian: false,
//   showInputs: true
// });
//end timepicker
$(".select-1").on("click", function () {
  $("#callmodal").css("display", "none"); //close modal oncliecking outside modal
});

function startLoading() {
  const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

  // Add the active class to show the overlay
  $("#loading-overlay").addClass("active");

  // Prevent scrolling
  $("body").addClass("loading").css("top", `-${scrollTop}px`);
}

function stopLoading() {
  const scrollTop = Math.abs(parseInt($("body").css("top"), 10));

  // Remove the active class to hide the overlay
  $("#loading-overlay").removeClass("active");

  // Re-enable scrolling
  $("body").removeClass("loading").css("top", "");
  window.scrollTo(0, scrollTop);
}

function getAbsoluteUrl() {
  var newURL = window.location.href;
  var module = jQuery("#module").val();
  var str = newURL.indexOf(module);

  var slicestr = newURL.substring(0, str);
  return slicestr;
}
function getBaseeUrl() {
  var newURL = window.location.href;
  var module = jQuery("#module").val();
  //alert(module);
  var str = newURL.split(module);

  var slicestr = str[0];
  return slicestr;
}
const urlParams = new URLSearchParams(window.location.search);
function removeTextValue(hiddenname, searchname) {
  // Check if hiddenname and searchname are strings, not objects
  if (typeof hiddenname !== "string") {
    hiddenname = jQuery(hiddenname).attr("id");
  }
  if (typeof searchname !== "string") {
    searchname = jQuery(searchname).attr("id");
  }

  // Proceed with jQuery selectors using validated IDs
  var module = jQuery("#module").val();
  jQuery("#" + searchname).val(""); // Reset the input field with searchname ID
  jQuery("#" + hiddenname).val(""); // Reset the hidden field with hiddenname ID
}
function startLoading() {
  $("#loading-overlay").addClass("active");
}

function stopLoading() {
  $("#loading-overlay").removeClass("active");
}

$(".btn-close, .btn-secondary").click(function () {
  $("#add-lead-modal").modal("hide");
});

$("#edit-lead-btn").on("click", function () {
  record = $("#recordid").val();
  startLoading(); // Show loading overlay
  const sourcemodule = urlParams.get("sourcemodule");
  const sourceid = urlParams.get("sourceid");
  var url = "edit?Record=" + record;
  if (sourcemodule && sourceid) {
    // Check if both sourcemodule and sourceid are not null or undefined
    url += `&sourcemodule=${encodeURIComponent(
      sourcemodule
    )}&sourceid=${encodeURIComponent(sourceid)}`;
  }

  $.get(url, function (data) {
    $("#add-lead-modal").modal("show").find(".modal-content").html(data);
    $("#toggle-switch2").removeClass("active");

    //added on 21/12/2024 for back to top
    const modalBody = document.getElementById("modalBody");
    const backToTopButton = document.getElementById("backToTop");
    if (modalBody) {
      modalBody.addEventListener("scroll", function () {
        //alert(modalBody);
        if (modalBody.scrollTop > 200) {
          backToTopButton.style.display = "block";
        } else {
          backToTopButton.style.display = "none";
        }
      });

      // Scroll back to top when the button is clicked
      backToTopButton.addEventListener("click", function () {
        modalBody.scrollTo({
          top: 0,
          behavior: "smooth",
        });
      });
    }
    //end back to top
  }).always(function () {
    stopLoading(); // Hide loading overlay
  });
});

function showModuleselection(
  mainmodule,
  sourcemodule,
  sourceid,
  pageNumberpre = "",
  pageNumber = "",
  searchparam = ""
) {
  var geturl = getAbsoluteUrl();
  // alert(geturl);
  var url = geturl + mainmodule + "/popuplistselect";
  csrfTokenName = $("#csrfTokenName").val();
  csrfToken = $("#csrfToken").val();

  if (searchparam == "") {
    data = {
      sourcemodule: sourcemodule,
      sourceid: sourceid,
      pageNumberpre: pageNumberpre,
      pageNumber: pageNumber,
      searchparam: "",
      _csrf: csrfToken,
    };
  } else
    data = {
      sourcemodule: sourcemodule,
      sourceid: sourceid,
      pageNumberpre: pageNumberpre,
      pageNumber: pageNumber,
      searchparam: searchparam,
      _csrf: csrfToken,
    };

  $.ajax({
    type: "POST",
    url: url,
    // async:false,
    data: data,
    success: function (data) {
      $("#modal22").modal("show").find(".modal-content").html(data);
    },
    error: function (data) {
      // if error occured

      alert("Error occured.please try again");
    },
    dataType: "html",
  });
}
function showroledusers(
  hiddenfield,
  field,
  RelatedDisplayFieldName,
  mainmodule,
  maintabid,
  pageNumberpre = "",
  pageNumber = "",
  searchparam = ""
) {
  //get the field id of the clicked input
  var nearestInput = $("#" + field).attr("fieldid");
  if (!nearestInput) nearestInput = null;
  var geturl = getAbsoluteUrl();
  var url =
    geturl +
    mainmodule +
    "/popuplist?hiddenfield=" +
    hiddenfield +
    "&field=" +
    field +
    "&rdisfield=" +
    RelatedDisplayFieldName +
    "&mname=" +
    mainmodule +
    "&maintabid=" +
    maintabid +
    "&roled=1";
  if (nearestInput) {
    url = url + "&current_fieldid=" + nearestInput;
  }
  csrfTokenName = $("#csrfTokenName").val();
  csrfToken = $("#csrfToken").val();
  //[csrfParam]: csrfToken,
  //   if(searchparam =='')
  //   {
  //       data= {
  //       'other':'other',
  //       _csrf: csrfToken,
  //     };
  // }
  //   else data = {  searchparam: searchparam, _csrf: csrfToken,};

  if (searchparam == "") {
    data = {
      pageNumberpre: pageNumberpre,
      pageNumber: pageNumber,
      searchparam: "",
      _csrf: csrfToken,
    };
  } else
    data = {
      pageNumberpre: pageNumberpre,
      pageNumber: pageNumber,
      searchparam: searchparam,
      _csrf: csrfToken,
    };
  //
  // alert(url);
  // $.get(url, function(data) {
  //     $('#modalreference').modal('show')
  //         .find('.modal-content')
  //         .html(data);
  // });
  console.log(data);
  $("#loading-overlay").css("display", "grid");
  $.ajax({
    type: "POST",
    url: url,
    // async:false,
    data: data,
    success: function (data) {
      $("#loading-overlay").css("display", "none");
      $("#modal22").modal("show").find(".modal-content").html(data);
    },
    error: function (data) {
      // if error occured
      $("#loading-overlay").css("display", "none");
      alert("Error occured.please try again");
    },
    dataType: "html",
  });
}
    //dependent,conditionfield,dependentval code added  by ptpatel on date 17-01-2025 to resolve blank search issue 
function showCustomer1(
    hiddenfield,
    field,
    RelatedDisplayFieldName,
    mainmodule,
    maintabid,
    pageNumberpre = "",
    pageNumber = "",
    searchparam = "",
    sourcemodule = '',
    sourceid = '',
    searchparam_child = "",
    dependent= "",
    conditionfield= "",
    dependentval= ""
) {
    var nearestInput = $("#" + field).attr("fieldid");
    var tabId = $('.srctabid') != 'undefined' ? $('.srctabid').val() : 0;
    if (!nearestInput) nearestInput = null;
    var geturl = getAbsoluteUrl();
    var url =
        geturl +
        mainmodule +
        "/popuplist?hiddenfield=" +
        hiddenfield +
        "&field=" +
        field +
        "&rdisfield=" +
        RelatedDisplayFieldName +
        "&mname=" +
        mainmodule +
        "&maintabid=" +
        maintabid + 
        "&srctabid=" +
        tabId;

    if (nearestInput) {
        url = url + "&current_fieldid=" + nearestInput;
        var dynamic_dependent = $("#" + field).attr("data-dynamic-dependent");
        if (dynamic_dependent) {
            url = url + "&dynamic_dependent=" + dynamic_dependent;
        }
        if (nearestInput == 3703) { // transporter_name
            url = url + "&dctype=" + $("#delivery_challan_type").val();
        }
    }

    csrfTokenName = $("#csrfTokenName").val();
    csrfToken = $("#csrfToken").val();

    var sp = {};
    if (Array.isArray(searchparam)) {
        for (var i = 0; i < searchparam.length; i++) {
            sp[i] = {};
            sp[i][0] = searchparam[i][0];
            sp[i][1] = searchparam[i][1];
        }
    }

    var sp_child = {};
    if (Array.isArray(searchparam_child)) {
        for (var j = 0; j < searchparam_child.length; j++) {
            sp_child[j] = {};
            sp_child[j][0] = searchparam_child[j][0]; // e.g. "pickup_asset_detail.serial_no"
            sp_child[j][1] = searchparam_child[j][1]; // value
        }
    }

    var data = {
        pageNumberpre: pageNumberpre,
        pageNumber: pageNumber,
        searchparam: sp,
        searchparam_child: sp_child, 
        sourcemodule: sourcemodule,
        sourceid: sourceid,
        _csrf: csrfToken,
        dependent:dependent,
        conditionfield:conditionfield,
        dependentval:dependentval,
    };

    console.log("Sending data:", data);

    $("#loading-overlay").css("display", "grid");
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        success: function (data) {
            $("#loading-overlay").css("display", "none");
            $("#modal22").modal("show").find(".modal-content").html(data);
        },
        error: function (data) {
            $("#loading-overlay").css("display", "none");
            alert("Error occurred. Please try again");
        },
        dataType: "html"
    });
}
function showMultiCustomer1(
  hiddenfield,
  field,
  RelatedDisplayFieldName,
  mainmodule,
  maintabid,
  pageNumberpre = "",
  pageNumber = "",
  searchparam = "",
  sourcemodule='',
  sourceid=''
) {

  //get the field id of the clicked input
  var nearestInput = $("#" + field).attr("fieldid");
  if (!nearestInput) nearestInput = null;
  var geturl = getAbsoluteUrl();
  var url =
    geturl +
    mainmodule +
    "/popuplistmulti?hiddenfield=" +
    hiddenfield +
    "&field=" +
    field +
    "&rdisfield=" +
    RelatedDisplayFieldName +
    "&mname=" +
    mainmodule +
    "&maintabid=" +
    maintabid;
  if (nearestInput) {
    url = url + "&current_fieldid=" + nearestInput;
    var dynamic_dependent = $("#" + field).attr("data-dynamic-dependent");
    if (dynamic_dependent) {
      url = url + "&dynamic_dependent=" + dynamic_dependent;
    }
    //this code added on date 11-07-25 for Delivery challan transportor name it depende on type of DC
    if(nearestInput == 3703)//transporter_name
    {
       url = url + "&dctype=" + $("#delivery_challan_type").val();
    }
    //this code added on date 11-07-25 for Delivery challan transportor name it depende on type of DC
  }
  csrfTokenName = $("#csrfTokenName").val();
  csrfToken = $("#csrfToken").val();
  //[csrfParam]: csrfToken,
  //   if(searchparam =='')
  //   {
  //       data= {
  //       'other':'other',
  //       _csrf: csrfToken,
  //     };
  // }
  //   else data = {  searchparam: searchparam, _csrf: csrfToken,};

  if (searchparam == "") {
    data = {
      pageNumberpre: pageNumberpre,
      pageNumber: pageNumber,
      searchparam: "",
       sourcemodule:sourcemodule,
      sourceid:sourceid,
      _csrf: csrfToken,
    };
  } else
    data = {
      pageNumberpre: pageNumberpre,
      pageNumber: pageNumber,
      searchparam: searchparam,
       sourcemodule:sourcemodule,
      sourceid:sourceid,
      _csrf: csrfToken,
    };
  //
  // alert(url);
  // $.get(url, function(data) {
  //     $('#modalreference').modal('show')
  //         .find('.modal-content')
  //         .html(data);
  // });
  console.log(data);
  $("#loading-overlay").css("display", "grid");
  $.ajax({
    type: "POST",
    url: url,
    // async:false,
    data: data,
    success: function (data) {
      $("#loading-overlay").css("display", "none");
      $("#myModalMulti").modal("show").find(".modal-content").html(data);
      $('#myModalMulti').data({
    textFieldId: RelatedDisplayFieldName,   // visible input’s container ID
    hiddenFieldId: hiddenfield // hidden field ID
});
    },
    error: function (data) {
      // if error occured
      $("#loading-overlay").css("display", "none");
      alert("Error occured.please try again");
    },
    dataType: "html",
  });
}
function showReferenceConditional(
  hiddenfield,
  field,
  RelatedDisplayFieldName,
  mainmodule,
  maintabid,
  dependentfieldhid,
  dependentfield,
  conditionfield,
  pageNumberpre = "",
  pageNumber = "",
  searchparam = ""
) {
  //first check dependent field value
  var dependent = $("#" + dependentfieldhid).val();
  // alert(mainmodule);
  if (dependent == "") {
    // added on 15 jan to show dependent label
    // Select label based on the 'for' attribute
    var label = $('label[for="' + dependentfield + '"]');

    // Get the text content of the label
    var labelText = label.text().trim();
    alert("First select " + labelText);
  } else {
    var geturl = getAbsoluteUrl();
    // alert(maintabid);
    var url =
      geturl +
      mainmodule +
      "/popuplistdependent?hiddenfield=" +
      hiddenfield +
      "&field=" +
      field +
      "&rdisfield=" +
      RelatedDisplayFieldName +
      "&mname=" +
      mainmodule +
      "&maintabid=" +
      maintabid;
      const sourceId = new URLSearchParams(window.location.search).get('sourceid');
      if(sourceId != "")
      {
        url = url + "&sourceid=" + sourceId;
      }
    if ($("#" + field).length) {
      var dynamic_dependent = $("#" + field).attr("data-dynamic-dependent");
      if (dynamic_dependent) {
        url = url + "&dynamic_dependent=" + dynamic_dependent;
      }
    }
    csrfTokenName = $("#csrfTokenName").val();
    csrfToken = $("#csrfToken").val();
    //[csrfParam]: csrfToken,
    //   if(searchparam =='')
    //   {
    //       data= {
    //       'other':'other',
    //       _csrf: csrfToken,
    //     };
    // }
    //   else data = {  searchparam: searchparam, _csrf: csrfToken,};

    if (searchparam == "") {
      data = {
        pageNumberpre: pageNumberpre,
        pageNumber: pageNumber,
        searchparam: "",
        dependent: dependentfield,
        conditionfield: conditionfield,
        dependentval: dependent,
        _csrf: csrfToken,
      };
    } else {
      data = {
        pageNumberpre: pageNumberpre,
        pageNumber: pageNumber,
        searchparam: searchparam,
        dependent: dependentfield,
        conditionfield: conditionfield,
        dependentval: dependent,
        _csrf: csrfToken,
      };
    }
    console.log(data);
    $.ajax({
      type: "POST",
      url: url,
      // async:false,
      data: data,
      success: function (data) {
        $("#modal22").modal("show").find(".modal-content").html(data);
      },
      error: function (data) {
        // if error occured

        alert("Error occured.please try again");
      },
      dataType: "html",
    });
  }
}
function addVendor(
  hiddenfield,
  field,
  RelatedDisplayFieldName,
  mainmodule,
  maintabid,
  searchparam = ""
) {
  var url =
    geturl +
    mainmodule +
    "/quickcreatepopup?hiddenfield=" +
    hiddenfield +
    "&field=" +
    field +
    "&rdisfield=" +
    RelatedDisplayFieldName +
    "&mname=" +
    mainmodule +
    "&maintabid=" +
    maintabid;
  csrfTokenName = $("#csrfTokenName").val();
  csrfToken = $("#csrfToken").val();
  //[csrfParam]: csrfToken,
  if (searchparam == "") data = { _csrf: csrfToken };
  else data = { searchparam: searchparam, _csrf: csrfToken };
  // alert(url);
  // $.get(url, function(data) {
  //     $('#modalreference').modal('show')
  //         .find('.modal-content')
  //         .html(data);
  // });

  $.ajax({
    type: "POST",
    url: url,
    async: false,
    data: data,
    success: function (data) {
      $("#modalreference").modal("show").find(".modal-content").html(data);
    },
    error: function (data) {
      // if error occured

      alert("Error occured.please try again");
    },
    dataType: "html",
  });
}
function getupcomingevents(Record) {
  var url = "getupcomingevents?Record=" + Record;
  csrfTokenName = $("#csrfTokenName").val();
  csrfToken = $("#csrfToken").val();
  //[csrfParam]: csrfToken,
  data = { _csrf: csrfToken };
  // alert(url);
  // $.get(url, function(data) {
  //     $('#modalreference').modal('show')
  //         .find('.modal-content')
  //         .html(data);
  // });

  $.ajax({
    type: "POST",
    url: url,
    async: false,
    data: data,
    success: function (data) {
      // $(".c-faqs__items").html(data);
      $(".upcomingdetail").html(data);
      $(".upcomingdetail .accordion").accordion(); // Reinitialize accordion
    },
    error: function (data) {
      // if error occured

      alert("Error occured.please try again");
    },
    dataType: "html",
  });

  $(document).on("click", ".accordion-header", function () {
    $(this).next(".accordion-content").slideToggle();
  });
}
function getrelatedmodules(Record) {
  var url = "getallrelatedmodules?Record=" + Record;
  csrfTokenName = $("#csrfTokenName").val();
  csrfToken = $("#csrfToken").val();
  //[csrfParam]: csrfToken,
  data = { _csrf: csrfToken };
  // alert(url);
  // $.get(url, function(data) {
  //     $('#modalreference').modal('show')
  //         .find('.modal-content')
  //         .html(data);
  // });

  $.ajax({
    type: "POST",
    url: url,
    async: false,
    data: data,
    success: function (data) {
      // $(".c-faqs__items").html(data);
      $(".relatedmodules").html(data);
    },
    error: function (data) {
      // if error occured

      alert("Error occured.please try again");
    },
    dataType: "html",
  });
}
function getnotes(Record) {
  var url = "getallnotes?Record=" + Record;
  csrfTokenName = $("#csrfTokenName").val();
  csrfToken = $("#csrfToken").val();
  //[csrfParam]: csrfToken,
  data = { _csrf: csrfToken };
  // alert(url);
  // $.get(url, function(data) {
  //     $('#modalreference').modal('show')
  //         .find('.modal-content')
  //         .html(data);
  // });

  $.ajax({
    type: "POST",
    url: url,
    async: false,
    data: data,
    success: function (data) {
      $(".notes-main-section").html(data);
    },
    error: function (data) {
      // if error occured

      alert("Error occured.please try again");
    },
    dataType: "html",
  });
}
function showinParent(recordid, recordvalue, field, hiddenfield) {

  // Get the element by ID
const inputElements = document.querySelectorAll(`#${field}`); // Select all elements with the given ID (use querySelectorAll)
const count = inputElements.length;

if (count > 1) {
  // If there are multiple elements with the same ID
  inputElements.forEach((input) => {
    if (input && input.type === 'text' && input.classList.contains('ref-form-control')) {
      input.value = recordvalue;  // Set value only for text input
    }
  });
} else if (count === 1) {
  // If there's only one element with the given ID
  const inputElement = inputElements[0];
  if (inputElement && inputElement.type === 'text') {
    inputElement.value = recordvalue;  // Set value only for text input
  }
}

// For the hidden field (assuming you have a different ID for the hidden field)
const inputElement2 = document.getElementById(hiddenfield);
if (inputElement2 && inputElement2.type === 'hidden') {
  inputElement2.value = recordid;  // Set value for hidden input
}

//   // alert("deep"+recordid);
//   //// Select all elements with the same ID
// const elementsWithSameId = document.querySelectorAll(`#`+field);

// // Check how many elements with that ID exist
// const count = elementsWithSameId.length;
// alert(count+' '+field+' '+hiddenfield);
// if(count > 1)
// {
//    console.log(`There are ${count} elements with the same ID.`);
//     // Now, loop through the elements to set the values
//     elementsWithSameId.forEach((input) => {
//       if (input.classList.contains('effect')) {
//         // This is the hidden input (with 'effect' class)
//         //input.value = '12345'; // Set value for the hidden input
//       } else {
//         // This is the text input (no 'effect' class)
//         input.value = recordvalue; // Set value for the text input
//       }
//     });

// }
// else{
//   document.getElementById(field).value = recordvalue;
// }
  // document.getElementById(hiddenfield).value = recordid;

  // added on 14 jan 2025 by deepika to remove mandatory message
  var errorElement = $("#" + field)
    .closest(".form-group")
    .find(".help-block");
  errorElement.html("");
  // end
}
// Function to close the modal
function closeModal() {
  $("#modalreference").modal("hide");
}
// Function to close the modal
function closeModalP() {
  $("#modal22").modal("hide");
}
// save call
$(document).on("click", ".savecall", function () {
  let isValid = true;
  // Clear previous errors
  $(".validation-error").text("");

  // Validation
  const subjectValue = $("#call_information_subject").val().trim();
  const specialCharRegex = /^[a-zA-Z0-9\s]+$/; // Allows only letters, numbers, and spaces
  const wordLimit = 100;

  // Count words
  const wordCount = subjectValue
    .split(/\s+/)
    .filter((word) => word.length > 0).length;

  if (!subjectValue) {
    $("#subject-error").text("Subject is required.");
    isValid = false;
  } else if (!specialCharRegex.test(subjectValue)) {
    $("#subject-error").text(
      "Special characters are not allowed in the subject."
    );
    isValid = false;
  } else if (wordCount > wordLimit) {
    $("#subject-error").text(
      `Subject cannot exceed ${wordLimit} words. Currently: ${wordCount} words.`
    );
    isValid = false;
  }

  if (!$("#call_information_calltypeid").val().trim()) {
    $("#call-type-error").text("Call type is required.");
    isValid = false;
  }

  if (!$("#call_information_outgoing_call_status").val().trim()) {
    $("#call-status-error").text("Outgoing call status is required.");
    isValid = false;
  }

  if (
    !$("#call_information_start-date").val().trim() ||
    !$("#call_information_start-time").val().trim()
  ) {
    $("#start-time-error").text("Start date and time are required.");
    isValid = false;
  }

  if (
    !$("#call_information_end-date").val().trim() ||
    !$("#call_information_end-time").val().trim()
  ) {
    $("#end-time-error").text("End date and time are required.");
    isValid = false;
  }

  if (!$("#call_information_call-duration").val().trim()) {
    $("#call-duration-error").text("Call duration is required.");
    isValid = false;
  }

  const commentsValue = $("#call_information_comments").val().trim();
  const commentsWordLimit = 200;

  const commentsWordCount = commentsValue
    .split(/\s+/)
    .filter((word) => word.length > 0).length;

  if (!commentsValue) {
    $("#comments-error").text("Comments are required.");
    isValid = false;
  } else if (commentsWordCount > commentsWordLimit) {
    $("#comments-error").text(
      `Comments cannot exceed ${commentsWordLimit} words. Currently: ${commentsWordCount} words.`
    );
    isValid = false;
  }

  if (!isValid) {
    $(this).text("Save").prop("disabled", false);
    return;
  }

  // If valid, proceed with AJAX call
  $(this).text("Saving..").prop("disabled", true);

  var call_information_related_to = $("#call_information_related_to").val();
  var call_information_related_to_id = $(
    "#call_information_related_to_id"
  ).val();
  var call_information_subject = $("#call_information_subject").val();
  var call_information_comments = $("#call_information_comments").val();
  var call_information_creatorid = $("#call_information_creatorid").val();
  var call_information_createdtime = $("#call_information_createdtime").val();
  var call_information_calltypeid = $("#call_information_calltypeid").val();
  var call_information_start_date = $("#call_information_start-date").val();
  var call_information_start_time = $("#call_information_start-time").val();
  var call_information_end_date = $("#call_information_end-date").val();
  var call_information_end_time = $("#call_information_end-time").val();
  var call_information_duration = $("#call_information_call-duration").val();

  var call_information_outgoing_call_status = $(
    "#call_information_outgoing_call_status"
  ).val();
  // alert(call_information_outgoing_call_status);
  start_date_time =
    call_information_start_date + " " + call_information_start_time;

  end_date_time = call_information_end_date + " " + call_information_end_time;

  console.log(call_information_calltypeid);
  console.log(call_information_start_date);
  console.log(start_date_time);
  csrfTokenName = $("#csrfTokenName").val();
  csrfToken = $("#csrfToken").val();

  if (
    call_information_related_to != "" &&
    call_information_related_to_id != "" &&
    call_information_subject != "" &&
    call_information_comments != "" &&
    call_information_creatorid != "" &&
    call_information_calltypeid != "" &&
    call_information_start_date != "" &&
    call_information_start_time != "" &&
    call_information_end_date != "" &&
    call_information_end_time != "" &&
    call_information_duration != "" &&
    call_information_outgoing_call_status != ""
  ) {
    call_information = {
      call_information: {
        related_to: call_information_related_to,
        related_to_id: call_information_related_to_id,
        subject: call_information_subject,
        comments: call_information_comments,
        creatorid: call_information_creatorid,
        modifiedby: call_information_creatorid,
        ownerid: call_information_creatorid,
        createdtime: call_information_createdtime,
        modifiedtime: call_information_createdtime,
        call_type: call_information_calltypeid,
        call_start_time: start_date_time,
        call_end_time: end_date_time,
        call_duration: call_information_duration,
        outgoing_call_status: call_information_outgoing_call_status,
      },
      module: "call",
      mode: "create",
      _csrf: csrfToken,
    };
    $.ajax({
      url: "addcall", // Adjust route as needed
      type: "POST",
      data: call_information,
      success: function (response) {
        if (response.success) {
          // alert(response.message);
          // $("#updateModel").modal("hide"); // Close modal on success
          // location.reload();
          getupcomingevents(call_information_related_to_id);
          getrelatedmodules(call_information_related_to_id);
          $("#callmodal").css("display", "none");
        } else {
          alert("Error: " + response.message);
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", error);
        alert("An error occurred while updating.");
      },
    });
  }
});

$(document).on("change", "#call_information_start-date", function () {
  calculateDuration();
});

$(document).on("change", "#call_information_start-time", function () {
  calculateDuration();
});

$(document).on("change", "#call_information_end-date", function () {
  calculateDuration();
});

$(document).on("change", "#call_information_end-time", function () {
  calculateDuration();
});

function calculateDuration() {
  // Retrieve values from input fields
  var startDate = $("#call_information_start-date").val();
  var startTime = $("#call_information_start-time").val();
  var endDate = $("#call_information_end-date").val();
  var endTime = $("#call_information_end-time").val();

  // Ensure all fields are filled
  if (!startDate || !startTime || !endDate || !endTime) {
    $("#call_information_call-duration").val("");
    return;
  }

  // Combine date and time strings and create Date objects
  var startDateTime = new Date(startDate + "T" + startTime);
  var endDateTime = new Date(endDate + "T" + endTime);

  // Check if end time is before start time
  if (endDateTime < startDateTime) {
    alert("End time cannot be earlier than start time!");
    $("#call_information_end-date").val("");
    $("#call_information_end-time").val("");
    $("#call_information_call-duration").val("Invalid time range");
    return;
  }

  
  // Calculate the difference in milliseconds
  var diffMs = endDateTime - startDateTime;

  // Convert milliseconds to minutes
  var diffMins = Math.floor(diffMs / (1000 * 60));

  // Calculate hours and remaining minutes
  var hours = Math.floor(diffMins / 60);
  var minutes = diffMins % 60;

  // Display the duration
  $("#call_information_call-duration").val(hours + "h " + minutes + "m");
}

$(document).on("change", "#call_information_outgoing_call_status", function () {
  const callStatusSelect = $("#call_information_outgoing_call_status").val(); // Get selected value
  const startDateInput = $("#call_information_start-date"); // Date input field
  const today = new Date();
  const todayDate = today.toISOString().split("T")[0]; // Format: YYYY-MM-DD

  // Log the selected status and today's date for debugging
  console.log("Selected Status:", callStatusSelect);
  console.log("Today's Date:", todayDate);

  if (callStatusSelect == 2) {
    // Disable future dates for "Completed"
    startDateInput.attr("max", todayDate); // Set max to today
    startDateInput.removeAttr("min"); // Remove min restriction
  } else if (callStatusSelect == 1) {
    // Disable past dates for "Scheduled"
    startDateInput.attr("min", todayDate); // Set min to today
    startDateInput.removeAttr("max"); // Remove max restriction
  } else {
    // Reset constraints if no valid status is selected
    startDateInput.removeAttr("min");
    startDateInput.removeAttr("max");
  }
});

$(document).on("click", ".update-close-btn", function () {
  $("#updateModel").modal("hide");
});

$(document).on("click", "#close-modal-btn", function () {
  const modal = document.getElementById("callmodal");
  modal.style.display = "none"; // Hide the modal
});
$(document).on("click", "#btn-close", function () {
  const modal = document.getElementById("add-lead-modal");
  modal.style.display = "none"; // Hide the modal
});
// Open modal
$(document).on("click", "#open-call-btn", function () {
  //openModalBtn.addEventListener('click', () => {
  $("#callmodal").css("display", "block");
  Recordid = $("#Recordid").val();
  $.ajax({
    type: "GET",
    url: "addcall?Recordid=" + Recordid,
    success: function (data) {
      //alert(data);
      $(".modal-1").html(data);
      $("#callmodal").css("display", "flex");
    },
    error: function (data) {
      // if error occured

      alert("Error occured.please try again");
    },
    dataType: "html",
  });
});
$(document).on("click", "#open-meeting-btn", function () {
  //openModalBtn.addEventListener('click', () => {
  $("#callmodal").css("display", "block");
  Recordid = $("#Recordid").val();
  $.ajax({
    type: "GET",
    url: "addmeeting?Recordid=" + Recordid,
    success: function (data) {
      //alert(data);
      $(".modal-1").html(data);
      $("#callmodal").css("display", "flex");
      // $("#attendees").select2();
      fetattendeeslist();
    },
    error: function (data) {
      // if error occured

      alert("Error occured.please try again");
    },
    dataType: "html",
  });
});
$(document).on("click", "#open-task-btn", function () {
  //openModalBtn.addEventListener('click', () => {
  $("#callmodal").css("display", "block");
  Recordid = $("#Recordid").val();
  $.ajax({
    type: "GET",
    url: "addtask?Recordid=" + Recordid,
    success: function (data) {
      //alert(data);
      $(".modal-1").html(data);
      $("#callmodal").css("display", "flex");
    },
    error: function (data) {
      // if error occured

      alert("Error occured.please try again");
    },
    dataType: "html",
  });
});
// open document
$(document).on("click", "#attach-doc-btn", function () {
  //openModalBtn.addEventListener('click', () => {
  $("#callmodal").css("display", "block");
  Recordid = $("#Recordid").val();
  $.ajax({
    type: "GET",
    url: "adddoc?Recordid=" + Recordid,
    success: function (data) {
      //alert(data);
      $(".modal-1").html(data);
      $("#callmodal").css("display", "flex");
    },
    error: function (data) {
      // if error occured

      alert("Error occured.please try again");
    },
    dataType: "html",
  });
});
//save meeting
$(document).on("click", ".savemeeting", function () {
  console.log("savemeeting");
  let isValid = true;
  // Clear previous errors
  $(".validation-error").text("");

  // Validation
  const subjectValue = $("#meeting_information_subject").val().trim();
  const specialCharRegex = /^[a-zA-Z0-9\s]+$/; // Allows only letters, numbers, and spaces
  const wordLimit = 100;

  // Count words
  const wordCount = subjectValue
    .split(/\s+/)
    .filter((word) => word.length > 0).length;

  if (!subjectValue) {
    $("#meeting_information_subject_error").text("Subject is required.");
    isValid = false;
  } else if (!specialCharRegex.test(subjectValue)) {
    $("#meeting_information_subject_error").text(
      "Special characters are not allowed in the subject."
    );
    isValid = false;
  } else if (wordCount > wordLimit) {
    $("#meeting_information_subject_error").text(
      `Subject cannot exceed ${wordLimit} words. Currently: ${wordCount} words.`
    );
    isValid = false;
  }

  if (!$("#meeting_information_description").val().trim()) {
    $("#meeting_information_description_error").text(
      "Description type is required."
    );
    isValid = false;
  }

  if (
    !$("#meeting_information_start-date").val().trim() ||
    !$("#meeting_information_start-time").val().trim()
  ) {
    $("#meeting_information_start_error").text(
      "Start date and time are required."
    );
    isValid = false;
  }

  if (
    !$("#meeting_information_end-date").val().trim() ||
    !$("#meeting_information_end-time").val().trim()
  ) {
    $("#meeting_information_end_error").text("End date and time are required.");
    isValid = false;
  }

  //Bhavitha Validation for Attendees
  // if ($("#attendees").children().length === 0) {
  //   $("#meeting_information_attendees_error").text("Attendees are required.");
  //   isValid = false;
  // }
  // console.log("attendee"+$("#attendees").val());
  if ($("#attendees").val() === null || $("#attendees").val().length === 0) {
    $("#meeting_information_attendees_error").text("Attendees are required.");
    isValid = false;
  }

  if (!isValid) {
    $(this).text("Save").prop("disabled", false);
    return;
  }

  // If valid, proceed with AJAX call
  $(this).text("Saving..").prop("disabled", true);

  var meeting_information_related_to = $(
    "#meeting_information_related_to"
  ).val();
  var meeting_information_related_to_id = $(
    "#meeting_information_related_to_id"
  ).val();
  var meeting_information_subject = $("#meeting_information_subject").val();
  var meeting_information_description = $(
    "#meeting_information_description"
  ).val();
  var meeting_information_creatorid = $("#meeting_information_creatorid").val();
  var meeting_information_createdtime = $(
    "#meeting_information_createdtime"
  ).val();
  var meeting_information_start = $("#meeting_information_start-date").val();
  var meeting_information_start_time = $(
    "#meeting_information_start-time"
  ).val();
  var meeting_information_end = $("#meeting_information_end-date").val();
  var meeting_information_end_time = $("#meeting_information_end-time").val();

  start_time = meeting_information_start + " " + meeting_information_start_time;
  end_time = meeting_information_end + " " + meeting_information_end_time;
  participants = $("#attendees").val();
  // $(".attendee").each(function () {
  //   console.log($(this).attr("data-id"));
  //   participants = $(this).attr("data-id") + "," + participants;
  // });

  csrfTokenName = $("#csrfTokenName").val();
  csrfToken = $("#csrfToken").val();

  console.log(meeting_information_start);
  console.log(meeting_information_start_time);
  console.log(meeting_information_end);
  console.log(meeting_information_end_time);
  console.log(meeting_information_related_to);
  console.log(meeting_information_related_to_id);
  console.log(meeting_information_subject);
  console.log(meeting_information_description);
  console.log(meeting_information_start);
  console.log(meeting_information_creatorid);

  if (
    meeting_information_related_to != "" &&
    meeting_information_related_to_id != "" &&
    meeting_information_subject != "" &&
    meeting_information_description != "" &&
    meeting_information_creatorid != "" &&
    meeting_information_start != "" &&
    meeting_information_start_time != "" &&
    meeting_information_end != "" &&
    meeting_information_end_time != "" &&
    participants != ""
  ) {
    meeting_information = {
      meeting_information: {
        related_to: meeting_information_related_to,
        related_to_id: meeting_information_related_to_id,
        title: meeting_information_subject,
        description: meeting_information_description,
        creatorid: meeting_information_creatorid,
        modifiedby: meeting_information_creatorid,
        ownerid: meeting_information_creatorid,
        createdtime: meeting_information_createdtime,
        modifiedtime: meeting_information_createdtime,
        from: start_time,
        to: end_time,
        internal_participants: participants,
      },
      module: "meeting",
      mode: "create",
      _csrf: csrfToken,
    };
    $.ajax({
      url: "addmeeting", // Adjust route as needed
      type: "POST",
      data: meeting_information,
      success: function (response) {
        if (response.success) {
          // alert(response.message);
          // $("#updateModel").modal("hide"); // Close modal on success
          getupcomingevents(meeting_information_related_to_id);
          getrelatedmodules(meeting_information_related_to_id);

          $("#callmodal").css("display", "none");
        } else {
          alert("Error: " + response.message);
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", error);
        alert("An error occurred while updating.");
      },
    });
  }
});
//save task
$(document).on("click", ".savetask", function () {
  let isValid = true;
  // Clear previous errors
  $(".validation-error").text("");

  // Validation
  const subjectValue = $("#task_information_subject").val().trim();
  const specialCharRegex = /^[a-zA-Z0-9\s]+$/; // Allows only letters, numbers, and spaces
  const wordLimit = 100;

  // Count words
  const wordCount = subjectValue
    .split(/\s+/)
    .filter((word) => word.length > 0).length;

  if (!subjectValue) {
    $("#task_information_subject_error").text("Subject is required.");
    isValid = false;
  } else if (!specialCharRegex.test(subjectValue)) {
    $("#task_information_subject_error").text(
      "Special characters are not allowed in the subject."
    );
    isValid = false;
  } else if (wordCount > wordLimit) {
    $("#task_information_subject_error").text(
      `Subject cannot exceed ${wordLimit} words. Currently: ${wordCount} words.`
    );
    isValid = false;
  }

  if (!$("#task_information_description").val().trim()) {
    $("#task_information_description_error").text(
      "Description type is required."
    );
    isValid = false;
  }

  if (!$("#task_information_due_date").val().trim()) {
    $("#task_information_due_date_error").text(
      "Due date and time are required."
    );
    isValid = false;
  }

  if (!$("#task_information_ownerid").val().trim()) {
    $("#task_information_ownerid_error").text("Please select the user");
    isValid = false;
  }

  if (!isValid) {
    $(this).text("Save").prop("disabled", false);
    return;
  }

  // If valid, proceed with AJAX call
  $(this).text("Saving..").prop("disabled", true);

  var task_information_related_to = $("#task_information_related_to").val();
  var task_information_related_to_id = $(
    "#task_information_related_to_id"
  ).val();
  var task_information_subject = $("#task_information_subject").val();
  var task_information_description = $("#task_information_description").val();
  var task_information_due_date = $("#task_information_due_date").val();
  var task_information_ownerid = $("#task_information_ownerid").val();
  var task_information_creatorid = $("#task_information_creatorid").val();
  var task_information_createdtime = $("#task_information_createdtime").val();
  console.log(task_information_due_date);

  csrfTokenName = $("#csrfTokenName").val();
  csrfToken = $("#csrfToken").val();
  if (
    task_information_related_to != "" &&
    task_information_related_to_id != "" &&
    task_information_subject != "" &&
    task_information_ownerid != "" &&
    task_information_creatorid != ""
  ) {
    task_information = {
      task_information: {
        related_to: task_information_related_to,
        related_to_id: task_information_related_to_id,
        subject: task_information_subject,
        description: task_information_description,
        creatorid: task_information_creatorid,
        modifiedby: task_information_creatorid,
        ownerid: task_information_ownerid,
        createdtime: task_information_createdtime,
        modifiedtime: task_information_createdtime,
        due_date: task_information_due_date,
      },
      module: "task",
      mode: "create",
      _csrf: csrfToken,
    };
    $.ajax({
      url: "addtask", // Adjust route as needed
      type: "POST",
      data: task_information,
      success: function (response) {
        if (response.success) {
          // alert(response.message);
          // $("#updateModel").modal("hide"); // Close modal on success
          //location.reload();
          getupcomingevents(task_information_related_to_id);
          getrelatedmodules(task_information_related_to_id);
          $("#callmodal").css("display", "none");
        } else {
          alert("Error: " + response.message);
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", error);
        alert("An error occurred while updating.");
      },
    });
  }
});

// save doc
$(document).on("click", ".savedoc", function () {
  var documents_title = $("#documents_title").val();
  var documents_note_content = $("#documents_note_content").val();
  var documents_note_ownerid = $("#documents_note_ownerid").val();
  var documents_note_folderid = $("#documents_note_folderid").val();
  var documents_related_to = $("#documents_related_to").val();
  var documents_related_to_id = $("#documents_related_to_id").val();
  var documents_creatorid = $("#documents_creatorid").val();
  var documents_createdtime = $("#documents_createdtime").val();

  let formData = new FormData();
  let fileInput = $("#dragfileInput")[0].files[0];
  console.log(fileInput);
  if (!fileInput) {
    alert("please select a file !");
    return;
  }
  if (
    !documents_title.trim() ||
    !documents_note_content.trim() ||
    !documents_note_ownerid.trim() ||
    !documents_note_folderid.trim() ||
    !documents_related_to.trim() ||
    !documents_creatorid.trim() ||
    !documents_createdtime.trim()
  ) {
    alert("please fill all the fields !");
    return;
  }

  if (fileInput != "") {
    formData.append("file", fileInput); //attach the file if provided
  }

  formData.append("documents" + "[title]", documents_title);
  formData.append("documents" + "[notecontent]", documents_note_content);
  formData.append("documents" + "[ownerid]", documents_note_ownerid);
  formData.append("documents" + "[folderid]", documents_note_folderid);
  formData.append("documents" + "[related_to]", documents_related_to);
  formData.append("documents" + "[related_to_id]", documents_related_to_id);
  formData.append("documents" + "[creatorid]", documents_creatorid);
  formData.append("documents" + "[createdtime]", documents_createdtime);
  formData.append("documents" + "[modifiedtime]", documents_createdtime);
  formData.append("documents" + "[modifiedby]", documents_creatorid);
  formData.append("_csrf", $("#csrfToken").val()); // Add CSRF token for security
  formData.append("mode", "create");
  formData.append("module", "documents");

  //call function
  uploadstatus = "upload-status1";
  $.ajax({
    url: "adddoc", // Update with your Yii2 controller action URL
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (response) {
      if (response.success) {
        // $('#upload-status').text('Notes Saved successfully: ' + response.fileUrl);
        // $('#'+uploadstatus).text('Notes Saved successfully');
        location.reload();
      } else {
        //$('#'+uploadstatus).text('File upload failed: ' + response.message);
        alert(response.message);
      }
    },
    error: function (xhr, status, error) {
      // $('#upload-status').text('An error occurred: ' + error);
      alert(error);
    },
  });
});
// $("#attendees").on("change",function(){alert('fdf');fetchFilteredList();});
// $("#attendees").on("keyup keydown blur change", fetchFilteredList);
// Fetch filtered data from the backend
function fetchFilteredList() {
  // console.log("deep");
  // const dropdownList = document.getElementById("dropdownList");
  // dropdownList.innerHTML = ""; // Clear the previous list
  // //alert('dropdownList');
  // const input = document.getElementById("attendees").value;
  // // inputval = encodeURIComponent(input);
  // if (input != "") {
  //   fetch(`searchusers?query=${encodeURIComponent(input)}`)
  //     .then((response) => response.json())
  //     .then((data) => {
  //       if (data == "") ropdownList.innerHTML = "";

  //       data.forEach((item) => {
  //         const li = document.createElement("li");
  //         li.textContent = item.showfield;
  //         li.dataset.id = item.id; // Save the ID in a dataset attribute
  //         li.onclick = () => addToContainer(item.id, item.showfield);
  //         dropdownList.appendChild(li);
  //       });
  //     })
  //     .catch((error) => console.error("Error fetching data:", error));
  // } else {
  //   dropdownList.innerHTML = "";
  // }
  console.log("deep");

  const dropdownList = document.getElementById("dropdownList");
  dropdownList.innerHTML = ""; // Clear the previous list

  const input = document.getElementById("attendees").value;

  if (input !== "") {
    fetch(`searchusers?query=${encodeURIComponent(input)}`)
      .then((response) => response.json())
      .then((data) => {
        if (!data || data.length === 0) {
          dropdownList.innerHTML = "<li>No results found</li>";
          return;
        }

        data.forEach((item) => {
          const li = document.createElement("li");
          li.textContent = item.showfield;
          li.dataset.id = item.id;
          li.dataset.email = item.email;
          li.classList.add("dropdown-item"); // Add class for styling
          li.onclick = () => addToContainer(item.id, item.showfield, item.email);
          dropdownList.appendChild(li);
        });
      })
      .catch((error) => console.error("Error fetching data:", error));
  }
}

// Add an item to the selected container
function addToContainer(id, name) {
  const container = document.getElementById("selectedContainer");

  // Check if the item is already added
  if (
    Array.from(container.children).some(
      (child) => child.dataset.id === String(id)
    )
  ) {
    alert("User already added!");
    return;
  }

  // Create a new div for the selected item
  const newItem = document.createElement("span");
  newItem.textContent = name;
  newItem.dataset.id = id; // Store the ID
  // Add a class to the newItem
  newItem.classList.add("attendee");

  // Optionally add a remove button for each item
  const removeBtn = document.createElement("span");
  removeBtn.textContent = "X";
  removeBtn.style.marginLeft = "10px";
  removeBtn.style.border = "none";
  removeBtn.style.background = "none";
  removeBtn.style.cursor = "pointer";
  removeBtn.onclick = () => newItem.remove();

  newItem.appendChild(removeBtn);
  container.appendChild(newItem);
}
//add ckeditor
let editorInstance;
if (document.querySelector(".notes-editor")) {
  ClassicEditor.create(document.querySelector(".notes-editor"), {
    toolbar: [
      "bold",
      "underLine",
      "italic",
      "link",
      "undo",
      "redo",
      "heading",
      "fontSize", // Font size button
      "fontColor", // Font color button
      "fontFamily", // Font family button
      "blockQuote", // Blockquote button
      "alignment", // Text alignment button
      "bulletedList",
      "numberedList",
    ],
    //  removePlugins: [
    //   'Image',        // Removes Image plugin
    //   'ImageUpload',  // Removes Image upload plugin
    //   'MediaEmbed',   // Removes Video embedding plugin
    //   'Table',        // Removes Table plugin
    //   'BlockQuote',   // Removes blockquote plugin
    //   'Alignment',    // Removes text alignment plugin
    //   'Indent',       // Removes indentation plugin
    //   'TableToolbar', // Removes table toolbar buttons (e.g. table row/column)
    // ],
    height: "1500px", // Set editor height
  }).then((editor) => {
      console.log("editor ready");
      editorInstance = editor;
  }).catch((error) => {
      console.error("There was a problem initializing the editor.", error);
  });
}
//end ck editor

//ck editor
// let editorInstance1;
// ClassicEditor.create(document.querySelector(".notes-editor2"), {
//   //.create(document.querySelector('.notes-editor'), {
//   //  toolbar: ['bold', 'italic', 'link', 'undo', 'redo']
//   // })
//   editorConfig1: {
//     height: "800px",
//   },
// })
//   .then((editor) => {
//     //console.log('Editor is ready!', editor);
//     editorInstance1 = editor;
//   })
//   .catch((error) => {
//     //console.error("There was a problem initializing the editor.", error);
//   });
//end ck editor

$(document).on("click", ".post-btn1", function (e) {
  //   if (editorInstance1) {
  //                 var modnotesval = editorInstance1.getData(); // Get the editor's content
  //                // console.log('Editor Value:', editorValue);
  // }
  //alert($(".notes-editor2").val());
  var modnotesval = $("#modnotesval").val();
  Recordid = $("#Recordid").val();
  TabId = $("#notesTabid").val();
  sourceid = $("#notessourceid").val();
  sourcemodule = $("#notessourcemodule").val();

  e.preventDefault();
  var $button = $(this); // Reference the button
  var originalText = $button.text(); // Save the original button text

  // Update button to show "Processing..." and disable it
  $button.text("Processing...").prop("disabled", true);

  let formData = new FormData();
  let fileInput = $("#attach-notes1")[0].files[0];
  console.log(fileInput);
  if (!modnotesval.trim() && !fileInput) {
    alert("Please provide either a file or some text!");
    $button.text(originalText).prop("disabled", false); // Revert button
    return;
  }

  if (fileInput != "") {
    formData.append("file", fileInput); //attach the file if provided
  }

  formData.append("modnotesval", modnotesval);
  formData.append("Recordid", Recordid);
  formData.append("_csrf", $("#csrfToken").val()); // Add CSRF token for security

  // Call function
  let uploadstatus = "upload-status1";

  postnotes(formData, uploadstatus, Recordid)
    .then(function (st) {
      if (st) {
        showRelatedModulelist("notes", sourcemodule, Recordid, TabId + "notes");
        alert(st ? "Notes saved successfully!" : "Failed to save notes.");
        // $button.text(originalText).prop("disabled", false);

        $(".notescontentmain").text("Loading...");
        return true;
      }
    })
    .catch(function () {
      alert("An error occurred during processing.");
    })
    .finally(function () {
      // Revert button to original state
      return true;
      $button.text(originalText).prop("disabled", false);
    });
});

function postnotes(formData, uploadstatus, Recordid) {
  return new Promise(function (resolve, reject) {
    $.ajax({
      url: "postnotes", // Update with your Yii2 controller action URL
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      success: function (response) {
        if (response.success) {
          getnotes(Recordid);
          resolve(1); // Success
        } else {
          resolve(0); // Failure
        }
      },
      error: function (xhr, status, error) {
        console.error("An error occurred:", error);
        reject(); // Failure due to an error
      },
    });
  });
}

// show related module
function showRelatedlst(
  mainmodule,
  sourcemodule,
  sourceid,
  divcontainer,
  pageNumberpre = "",
  pageNumber = "",
  searchparam = ""
) {
  var geturl = getBaseeUrl();
  //alert(geturl);
  var url =
    geturl +
    mainmodule +
    "/relatedlist?sourcemodule=" +
    sourcemodule +
    "&sourceid=" +
    sourceid;
  csrfTokenName = $("#csrfTokenName").val();
  csrfToken = $("#csrfToken").val();
  //[csrfParam]: csrfToken,
  if (pageNumberpre == "") pageNumberpre = 0;
  if (pageNumber == "") pageNumber = 0;

  if (searchparam == "") {
    data = {
      pageNumberpre: pageNumberpre,
      pageNumber: pageNumber,
      searchparam: "",
      divcontainer: divcontainer,
      _csrf: csrfToken,
    };
  } else
    data = {
      pageNumberpre: pageNumberpre,
      pageNumber: pageNumber,
      searchparam: searchparam,
      divcontainer: divcontainer,
      _csrf: csrfToken,
    };
  // alert(url);
  // $.get(url, function(data) {
  //     $('#modalreference').modal('show')
  //         .find('.modal-content')
  //         .html(data);
  // });
  //console.log(data);

  $.ajax({
    type: "POST",
    url: url,
    // async:false,
    data: data,
    beforeSend: function () {
      startLoading();
    },
    success: function (data) {
      $("#" + divcontainer).html(data);
      //divcontainer = 'relatedmod'+sourcemodule;
      // Scroll to the data section
      setTimeout(() => {
        document
          .getElementById(divcontainer)
          .scrollIntoView({ behavior: "smooth" });
      }, 500);
    },
    error: function (data) {
      // if error occured

      alert("Error occured.please try again");
    },
    complete: function () {
      stopLoading(); // Hide the overlay when the request completes
    },
    dataType: "html",
  });
}

// show all related module
function showRelatedModulelist(
  mainmodule,
  sourcemodule,
  sourceid,
  divcontainer,
  pageNumberpre = "",
  pageNumber = "",
  searchparam = ""
) {
  var geturl = getBaseeUrl();
  //alert(geturl);
  var url =
    geturl +
    mainmodule +
    "/relatedlist?sourcemodule=" +
    sourcemodule +
    "&sourceid=" +
    sourceid;
  csrfTokenName = $("#csrfTokenName").val();
  csrfToken = $("#csrfToken").val();
  //[csrfParam]: csrfToken,
  if (pageNumberpre == "") pageNumberpre = 0;
  if (pageNumber == "") pageNumber = 0;

  if (searchparam == "") {
    data = {
      pageNumberpre: pageNumberpre,
      pageNumber: pageNumber,
      searchparam: "",
      divcontainer: divcontainer,
      _csrf: csrfToken,
    };
  } else
    data = {
      pageNumberpre: pageNumberpre,
      pageNumber: pageNumber,
      searchparam: searchparam,
      divcontainer: divcontainer,
      _csrf: csrfToken,
    };

  console.log(data);

  $.ajax({
    type: "POST",
    url: url,
    // async:false,
    data: data,
    beforeSend: function () {
      startLoading();
    },
    success: function (data) {
      $("#" + divcontainer).html(data);
      setTimeout(() => {
        document
          .getElementById(divcontainer)
          .scrollIntoView({ behavior: "smooth" });
      }, 500);

      // $('#'+divcontainer).scrollIntoView({ behavior: 'smooth' });
    },
    error: function (data) {
      // if error occured

      alert("Error occured.please try again");
    },
    complete: function () {
      stopLoading(); // Hide the overlay when the request completes
    },
    dataType: "html",
  });
}

// module tabs
$(document).on("click", ".tabid", function () {
  var $this = $(this);
  //alert($this);
  var targetTab = $this.data("tabid"); // Get the clicked tab's data-tab value
  var $container = $this.closest("div"); // Scope to the nearest parent container (top-container or module-container)

  //alert(targetTab);
  // Update active tab buttons
  $container.find(".tabid").removeClass("active");
  $this.addClass("active");

  // Update active tab content
  $(".tab-content-detail-viewmodule").removeClass("active");
  $(`#${targetTab}`).addClass("active");
});

// for related moodule

//add change onclick handler for related module

$(document).on("change", "#related_to", function () {
  // alert("fdgfd");
  $("#related_to_id").val("");
  var tabid = $(this).val();
  var onclickval = $("#uni_related" + tabid).val();
  // alert(onclickval);
  $(".related-search-icon").attr("data-onrefclick", onclickval);
  // $(".related-search-icon").attr("onclick", onclickval);
});

// for history/summary tab

// document.addEventListener("DOMContentLoaded", function () { //comment by zitendra
//   // Get all the tab buttons and content sections
//   const tabs = document.querySelectorAll(".tab");
//   const tabContents = document.querySelectorAll(".tab-content-detail-view");

//   // Function to activate the clicked tab and show its content
//   function activateTab(tabId) {
//     // Remove active class from all tabs
//     tabs.forEach((tab) => tab.classList.remove("active"));

//     // Hide all tab content
//     tabContents.forEach((content) => content.classList.remove("active"));

//     // Add active class to the clicked tab
//     const activeTab = document.querySelector(`.tab[data-tab="${tabId}"]`);
//     activeTab.classList.add("active");

//     // Show the corresponding content
//     const activeContent = document.getElementById(tabId);
//     activeContent.classList.add("active");
//   }

//   // Set up event listeners on each tab
//   tabs.forEach((tab) => {
//     tab.addEventListener("click", function () {
//       // alert("gfdgf");
//       const tabId = tab.getAttribute("data-tab");
//       activateTab(tabId);
//     });
//   });

//   // Optionally, activate the first tab by default
//   activateTab("summary");
// });
document.addEventListener("DOMContentLoaded", function () {
  // Get all the tab buttons and content sections
  const tabs = document.querySelectorAll(".tab");
  const tabContents = document.querySelectorAll(".tab-content-detail-view");

  // Function to activate the clicked tab and show its content
  function activateTab(tabId) {
    // Remove active class from all tabs
    tabs.forEach((tab) => tab.classList.remove("active"));

    // Hide all tab content
    tabContents.forEach((content) => content.classList.remove("active"));

    // Add active class to the clicked tab
    const activeTab = document.querySelector(`.tab[data-tab="${tabId}"]`);
    const activeContent = document.getElementById(tabId);

    if (activeTab) {
      activeTab.classList.add("active");
    } else {
      console.warn(`No tab found with data-tab="${tabId}"`);
    }

    if (activeContent) {
      activeContent.classList.add("active");
    } else {
      console.warn(`No content found with id="${tabId}"`);
    }
  }

  // Set up event listeners on each tab
  tabs.forEach((tab) => {
    tab.addEventListener("click", function () {
      const tabId = tab.getAttribute("data-tab");
      activateTab(tabId);
    });
  });

  // Optionally, activate the first tab by default
  const firstTabId = tabs[0]?.getAttribute("data-tab");
  if (firstTabId) {
    activateTab(firstTabId);
  } else {
    console.warn("No tabs found to activate by default.");
  }
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

// for multiple record
function addRowBtn(blockid, mainmodule,isCount=0) {
  return new Promise((resolve, reject) => {
    let totalRows = $("#productTable" + blockid + " tr").length;
    console.log(totalRows,'totalRows');
    var geturl = getAbsoluteUrl();
    var sourcemodule = urlParams.get("sourcemodule");
  var sourceid = urlParams.get("sourceid");
    var url =
      geturl +
      mainmodule +
      "/getproductlist?blockid=" +
      blockid +
      "&cnt_rows=" +
      totalRows+"&sourcemodule="+sourcemodule+"&sourceid="+sourceid;

    // Fetch from ajax
    $.ajax({
      type: "GET",
      url: url,
      success: function (data) {
         if (isCount != 0) {
              var tbody = $("#previewTablecsvUploadSample tbody");
              if (tbody.find("tr").length) {
                tbody.find("tr:last").after(data);
              } else {
                tbody.append(data);
              }
  
          resolve("Data appended successfully"); 
        }else{
        var tbody = $("#productTable" + blockid + " tbody");
        if (tbody.find("tr").length) {
          tbody.find("tr:last").after(data);
        } else {
          tbody.append(data);
        }

        resolve("Data appended successfully"); 
      }
      },
      error: function () {
        reject("Error occurred while appending data"); // Reject with error message
      },
      dataType: "html",
    });
  });
}

// remove multipl block row
$(document).on("click", ".remove-row-btn", function (e) {
    e.preventDefault();
    var $btn = $(this);
    
    showCustomConfirm(
        'Remove Item?',
        'Are you sure you want to remove this item?',
        'Remove',
        'Cancel',
        'danger'
    ).then(function(confirmed) {
        if (!confirmed) return;
        
        var currentUrl = window.location.href.toLowerCase(); 
        const isSalesOrder =/\/salesorder(\/|\?|$)/.test(currentUrl);
        if (isSalesOrder) {
            var inventory_id = $btn.closest('tr').find('[name*="inventory_id"]').val();
            
            if (inventory_id) {
                $btn.closest("tr").remove();
                updateExcludedList();
                //   var geturl = getAbsoluteUrl();
                //   var mainmodule = "salesorder";
                //   var url = geturl + mainmodule + "/deleteitembyinvid";
                //   $.ajax({
                //       url: url,
                //       type: 'POST',
                //       dataType: 'json',
                //       data: JSON.stringify({ inventory_id: inventory_id }),
                //       contentType: 'application/json',
                //       headers: { 'X-CSRF-Token': $('#csrfToken').val() },
                //       success: function(response) {
                //           if (response.status === 'success') {
                //               $btn.closest("tr").remove();
                //               updateExcludedList();
                //           }
                //       }
                //   });
            }
        }else{
            $btn.closest("tr").remove();
        }
    });
});



$(document).ready(function () {
  /**Scroll using arrow keys in bootstrap modal */
  document.addEventListener("keydown", function (event) {
    // Find the currently active modal
    var activeModals = document.querySelectorAll(".modal.show");
    if (activeModals.length > 0) {
      // Loop through all active modals (in case there are multiple)
      activeModals.forEach(function (activeModal) {
        var modalBody = activeModal.querySelector(".modal-body");
        if (modalBody) {
          if (event.key === "ArrowDown") {
            modalBody.scrollTop += 20;
            event.preventDefault();
          } else if (event.key === "ArrowUp") {
            modalBody.scrollTop -= 20;
            event.preventDefault();
          }
        }
      });
    }
  });
});

$(document).ready(function () {
  // Toggle the 'active' class on the toggle switch when clicked

  $(document)
    .off("click", ".toggle-switch")
    .on("click", ".toggle-switch", function () {
      event.stopPropagation(); // Prevent bubbling
      console.log("deep ut");

      console.log("Clicked element:", this);
      console.log("Element classes before toggle:", $(this).attr("class"));
      $(this).toggleClass("active");
      console.log("Element classes after toggle:", $(this).attr("class"));

      toggleRequiredFields2();
    });

  function toggleRequiredFields2() {
    const isChecked = $("#toggle-switch2").hasClass("active");
    console.log("Is Checked:", isChecked);

    const requiredFields = $(".not-required-field");
    console.log("Fields to toggle:", requiredFields);

    requiredFields.each(function () {
      if (isChecked) {
        $(this).hide(); // Hide the element
      } else {
        $(this).show(); // Show the element
      }
    });
  }
});

// added on 9 jan 2025 fr convert

$(".convert-btn").on("click", function () {
  record = $("#recordid").val();
  // startLoading(); // Show loading overlay
  var url = "convert?Record=" + record;

  $.get(url, function (data) {
    $("#add-lead-modal").modal("show").find(".modal-content").html(data);
    $("#toggle-switch2").removeClass("active");

    //added on 21/12/2024 for back to top
    const modalBody = document.getElementById("modalBody");
    // const backToTopButton = document.getElementById("backToTop");
    // if (modalBody) {
    //   modalBody.addEventListener("scroll", function () {
    //     //alert(modalBody);
    //     if (modalBody.scrollTop > 200) {
    //       backToTopButton.style.display = "block";
    //     } else {
    //       backToTopButton.style.display = "none";
    //     }
    //   });

    //   // Scroll back to top when the button is clicked
    //   backToTopButton.addEventListener("click", function () {
    //     modalBody.scrollTo({
    //       top: 0,
    //       behavior: "smooth",
    //     });
    //   });
    // }
    //end back to top
  }).always(function () {
    // stopLoading(); // Hide loading overlay
  });
});

// added on 22 jan 2025 for pipeline status
// Ensure the script runs after the document is fully loaded
// document.addEventListener('DOMContentLoaded', function() {
// Add click event listener to each .stage element
document.querySelectorAll(".stage").forEach((stage) => {
  stage.addEventListener("click", function () {
    // Remove "clicked-stage" class from all stages
    document
      .querySelectorAll(".stage")
      .forEach((s) => s.classList.remove("clicked-stage"));

    // Add "clicked-stage" class to the clicked stage
    stage.classList.add("clicked-stage");

    // Get the leadstatusid from the clicked stage's data-id attribute
    const leadstatusid = $(this).data("id");
    // console.log("Leadstatus ID:", leadstatusid); // Debugging: Make sure leadstatusid is correct

    // Hide all .leaddurationbox elements by adding "tr-hidden" class
    $(".leaddurationbox").addClass("tr-hidden");
    $(".leaddescbox").addClass("tr-hidden");
    // Show the .leaddurationbox that corresponds to the clicked leadstatusid by removing "tr-hidden"
    $(".leadduration" + leadstatusid).removeClass("tr-hidden");
    $(".leaddesc" + leadstatusid).removeClass("tr-hidden");
  });
});

// bhavitha quill
$(document).ready(function () {
    var quill = new Quill("#editor-container", {
      theme: "snow",
      placeholder: "Write your notes here...",
      modules: {
        toolbar: [
          ["bold", "italic", "underline"],
          [{ list: "ordered" }, { list: "bullet" }],
        ],
      },
    });
  
  const mentionList = document.getElementById("mention-list");

  quill.on("text-change", function () {
    const cursorPosition = quill.getSelection();
    if (!cursorPosition) return;

    const text = quill.getText(0, cursorPosition.index);
    const lastWord = text.split(/\s+/).pop();

    if (lastWord.startsWith("@")) {
      fetchUsernames(lastWord.slice(1), cursorPosition.index);
    } else {
      mentionList.style.display = "none";
    }
  });
  var geturl = getAbsoluteUrl();
  //  alert(geturl);

  var url = geturl + "leads/getusernames";
  csrfTokenName = $("#csrfTokenName").val();
  csrfToken = $("#csrfToken").val();

  function fetchUsernames(query, index) {
    $.ajax({
      url: url,
      type: "GET",
      data: { q: query, _csrf: csrfToken },
      dataType: "json",
      success: function (response) {
        if (response.usernames.length > 0) {
          showMentionList(response.usernames, query, index);
        } else {
          mentionList.style.display = "none";
        }
      },
      error: function (xhr, status, error) {
        console.error("Error fetching usernames:", error);
      },
    });
  }

  function showMentionList(usernames, query, index) {
    mentionList.innerHTML = "";
    mentionList.style.display = "block";

    usernames.forEach((user) => {
      const div = document.createElement("div");
      div.classList.add("mention-item");
      div.textContent = user;
      div.onclick = function () {
        insertMention(user, index);
        //alert(user);
      };
      mentionList.appendChild(div);
    });
  }

  function insertMention(user, index) {
    quill.deleteText(index - 1, index);
    // quill.insertText(index - 1, "@" + user + " ", "bold", true);
    quill.insertText(index - 1, "@" + user + " ", "bold", false);
    mentionList.style.display = "none";
    quill.focus();

    //saveMention(user);
  }

  function saveMention(user) {
    // alert(user);
    $.ajax({
      url: geturl + "leads/savemention",
      type: "POST",
      data: { user: user, _csrf: csrfToken },
      success: function (response) {
        console.log("Mention saved:", response);
      },
      error: function (xhr, status, error) {
        console.error("Error saving mention:", error);
      },
    });
  }

  document.addEventListener("click", function (event) {
    if (!mentionList.contains(event.target)) {
      mentionList.style.display = "none";
    }
  });

  //save notes
  $(document).on("click", ".post-btn", function (e) {
    e.preventDefault();

    let $button = $(this); // Cache the button reference
    let modnotesval = quill.root.innerHTML.trim(); // Get Quill content
    let Recordid = $("#Recordid").val();
    let fileInput = $("#attach-notes")[0].files[0];
    let csrfToken = $("#csrfToken").val();

    // Validation: Check if both text and file are empty
    if (!modnotesval.replace(/<(.|\n)*?>/g, "").trim() && !fileInput) {
      alert("Please provide either a file or some text!");
      return; // Stop execution if validation fails
    }

    $button.text("Posting..").prop("disabled", true); // Disable button

    let formData = new FormData(); // Move formData declaration outside the if block
    if (fileInput) {
      formData.append("file", fileInput);
    }
    formData.append("modnotesval", modnotesval);
    formData.append("Recordid", Recordid);
    formData.append("_csrf", csrfToken);

    // Call the `postnotes` function
    postnotes(formData, "upload-status", Recordid)
      .then(() => {
        // Reset Quill editor and file input
        quill.root.innerHTML = "";
        $("#attach-notes").val("");

        $button.text("Post").prop("disabled", false); // Reset button state
      })
      .catch((error) => {
        console.error("Error posting notes:", error);
        alert("An error occurred while posting notes.");
        $button.text("Post").prop("disabled", false); // Reset button state
      });
  });
});

// });
//added by ptpatel 19-03-25
   $(document).on("click", ".single-edit-class", function () {
    let uitype = $(this).data("uitype");
    let tabid = $(this).data("tabid");
    let fieldlabel = $(this).data("fieldlabel");
    let fieldid = $(this).data("fieldid");
    let recordid = $(this).data("recordid");
    let columnname = $(this).data("columnname");
    let view = $(this).data("view");

    console.log("Clicked Edit Icon:");
    console.log({ uitype, tabid, fieldlabel, fieldid, recordid, columnname, view });
        singleEdit( uitype, tabid, fieldlabel, fieldid, recordid, columnname, view);
});
function singleEdit(
  uitype,
  tabid,
  fieldlabel,
  fieldid,
  recordid,
  columnname,
  from
) {
  //,ModuleName, fieldname, tableName, columnname, typeofdata,maximumlength,fieldtype,related_mod) {

  const singleeditsourcemodule = urlParams.get("sourcemodule");
  const singleeditsourceid = urlParams.get("sourceid");
  var url = "singleedit";
  if (singleeditsourcemodule && singleeditsourceid) {
    // Check if both sourcemodule and sourceid are not null or undefined
    url += `?sourcemodule=${encodeURIComponent(
      singleeditsourcemodule
    )}&sourceid=${encodeURIComponent(singleeditsourceid)}`;
  }
  console.log("in edit js function of single edit"+url);
  startLoading();
  $.ajax({
    url: url, //"singleedit?sourceid=null&sourcemodule=null",
    type: "POST",
    data: {
      columnname: columnname,
      recordid: recordid,
      tabid: tabid,
      uitype: uitype,
      fieldid: fieldid,
      _csrf: yii.getCsrfToken(), // For CSRF protection
      from: from,
    },
    success: function (response) {
      // console.log(response);
      $("#editModal #modal_label_name").text("Edit " + fieldlabel);
      $("#editModal .modal-body").html("");
      $("#editModal .modal-body").html(response);
      $("#editModal").modal("show");
      loadrequiredscript();
      stopLoading();
    },
    error: function (xhr) {
      stopLoading();
      alert("Failed to update " + fieldlabel + " value!");
      console.error(xhr.responseText);
    },
  });
}
function loadrequiredscript() {
    const baseUrl = getAbsoluteUrl() + 'thememain/';
    const scripts = [
        "jquery/jquery.min.js",
        "bootstrap/bootstrap.min.js",
        "js/select2.min.js",
        "js/tetra/single-dd.js",
        "js/tetra/multilist-dd.js",        
        "js/tetra/singleeditvalidate.js",
        "js/tetra/singleeditvalidation.js",
        "js/flatpickr.js",
    ];

    console.log("Loading scripts dynamically...");

    // Helper to load scripts sequentially
    function loadScript(index) {
        if (index >= scripts.length) {
            console.log(" All scripts loaded");
            initializePlugins();
            return;
        }

        const scriptUrl = baseUrl + scripts[index] + "?v=" + Date.now();

        // Remove old version if exists
        const existing = document.querySelector(`script[src*="${scripts[index]}"]`);
        if (existing) existing.remove();

        const script = document.createElement("script");
        script.src = scriptUrl;
        script.type = "text/javascript";
        script.async = false;

        // Keep CSP nonce if present
        const nonce = document.querySelector('script[nonce]')?.nonce;
        if (nonce) script.setAttribute("nonce", nonce);

        script.onload = function() {
            console.log(` Loaded: ${scripts[index]}`);
            loadScript(index + 1); // Load next file
        };

        script.onerror = function(err) {
            console.error(`❌ Failed to load: ${scripts[index]}`, err);
        };

        document.head.appendChild(script);
    }

    loadScript(0); // Start loading the first script
}
function initializePlugins() {

    // For DateTime fields
    if ($(".datetimepicker").length) {
        flatpickr(".datetimepicker", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true
        });
    }

    //  For Time Only (uitype = 30)
    if ($(".timepicker").length) {
        flatpickr(".timepicker", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true
        });
    }

    console.log("🎯 Flatpickr initialized");
}

/**for textbox editing */
function cancelEdit(vals) {
  document.getElementById("lead_edit_" + vals).style.display = "none";
  document.getElementById("lead_display_" + vals).style.display = "inline";
}
function showTextbox(vals) {
  document.getElementById("lead_display_" + vals).style.display = "none";
  document.getElementById("lead_edit_" + vals).style.display = "inline";
}

//added by ptpatel on date 16-04-2025
 function fensureSelect2Loaded(baseUrl) {
    return new Promise((resolve) => {
      if (typeof $.fn.select2 !== 'undefined') {
        // console.log("Select2 already loaded");
        resolve();
      } else {
        console.log("Loading Select2...");

        // Load CSS
        const css1 = document.createElement('link');
        css1.rel = 'stylesheet';
        css1.href = baseUrl + "thememain/css/select2.min.css";
        document.head.appendChild(css1);

        // Load JS
        const script1 = document.createElement('script');
        script1.src = baseUrl + "thememain/js/select2.min.js";
        script1.onload = function () {
          console.log("Select2 JS loaded");
          resolve();
        };
        document.head.appendChild(script1);
      }
    });
  }
  function fapplySelect2() {
    $('.singleselect').each(function () {
      if (!$(this).hasClass('select2-hidden-accessible')) {
        $(this).select2({
          placeholder: "Select",
          allowClear: true,
          width: '100%'
        });
      }
    });
    $('.multySelect').each(function () {
      if (!$(this).hasClass('select2-hidden-accessible')) {
        $(this).select2({
          placeholder: "Select",
          allowClear: true,
          width: '100%'
        });
      }
    });
  }
function addAutofilledRowBtn(blockid, mainmodule, id, tablename,qty) {
  // console.log("addAutofilled called"+blockid+"-"+mainmodule+"-"+id+"-"+ tablename+"-"+qty);
  let totalRows;
  if(qty >= 0 && qty != null && qty !=undefined){
    // console.log("upsideqty"+qty);
    totalRows = qty;
  }
  else{
    totalRows = $("#productTable" + blockid + " tr").length;
    // console.log("upsidetr"+totalRows);
  }
  var geturl = getAbsoluteUrl();
  var url =
    geturl +
    mainmodule +
    "/getautofieldproductlist?blockid=" +
    blockid +
    "&cnt_rows=" +
    totalRows +
    "&inventory_id=" + id +
    "&tablename="+tablename;
  // Get the table body
  const tableBody = document.querySelector(
    "#productTable" + blockid + " tbody"
  );

  //fetch from ajax

  /*$.ajax({
    type: "GET",
    url: url,
    success: function (data) {
      var tbody = $("#productTable" + blockid + " tbody");
      if (tbody.find("tr").length) {
        console.log("inif");
        tbody.find("tr:last").after(data);
      } else {
        console.log("inelse");
        tbody.append(data);
      }
    },
    error: function (data) {
      // if error occured

      alert("Error occured.please try again");
    },
    dataType: "html",
  });*/
  return new Promise((resolve, reject) => {
    $.ajax({
      type: "GET",
      url: url,
      success: async function (data) {
        // startLoading();
        await fensureSelect2Loaded(getAbsoluteUrl());
        var tbody = $("#productTable" + blockid + " tbody");
        if (tbody.find("tr").length) {
          // console.log("inif");
          tbody.find("tr:last").after(data);
        } else {
          // console.log("inelse");
          tbody.append(data);
        }
        fapplySelect2();

        // stopLoading();
         resolve();
      },
      error: function (data) {
        // if error occured

        alert("Error occured.please try again");
         reject(xhr);
      },
      dataType: "html",
    });
  });
}
//end added by ptpatel

//code added by ptpatel on date 05-05-25
// $(document).ready(function () {
function fetattendeeslist(){
  // console.log("select2"+typeof $.fn.select2);
  $.ajax({
    url: 'searchusers?query=all', // Adjust this URL as needed
    method: 'GET',
    dataType: 'json',
    success: function (data) {
      // console.log("pdata"+data);
      const $select = $('#attendees');
      $select.empty(); // Clear previous options

      if (data.length === 0) {
        $select.append('<option disabled>No users found</option>');
      } else {
        data.forEach(function (user) {
          $select.append(
            $('<option>', {
              value: user.id,
              text: user.showfield + ' (' + user.email + ')'
            })
          );
        });
      }
    },
    error: function (xhr, status, error) {
      console.error('Error fetching user data:', error);
    }
  });
}

//code added by ptpatel on date 05-05-25

/////added by deepika on 26 june 2025

        // Handle the export button click for each section
        // When the export button is clicked
        $('.exportBtn').on('click', function() {
    // Get the section ID from the data-section attribute of the clicked button
    var section = $(this).data('section');
    
   
    if (section) {
       recordid = $("#recordid").val();
        // Send the table data to the backend via POST
        $.ajax({
            url: 'exportitems',  // Backend action to handle the export
            type: 'get',
            data: {
                section: section,  // Pass the table HTML
                record: recordid
            },
            success: function(response) {
                // Redirect to the generated Excel file for download
                window.location.href = response;
            },
            error: function() {
                alert('Error exporting table!');
            }
        });
    } else {
        alert('No table found in the specified section.');
    }
});
// code added by ptpatel to resolve refrence type onclick issue of server on date 27-06-25 
$(document).ready(function () {
	$(document).on("click", "#showReferenceConditional", function () {
		const $svg = $(this);

		const fieldname1 = $svg.data("fieldname1");
		const fieldname = $svg.data("fieldname");
		const display = $svg.data("display");
		const module = $svg.data("module");
		const fieldid = $svg.data("fieldid");
		const dep1 = $svg.data("dep1");
		const dep = $svg.data("dep");
		const cond = $svg.data("cond");
        // alert(fieldname1+"-"+fieldname+"-"+display+"-"+module+"-"+fieldid+"-"+dep1+"-"+dep+"-"+cond);
		showReferenceConditional(fieldname1, fieldname, display, module, fieldid, dep1, dep, cond);
	});

	$(document).on("click", "#showCustomer1", function () {
		const $el = $(this);
    /*onclick="showCustomer1(
    '<?= $fieldname1 ?>',
    '<?= $fieldname ?>',
    '<?= $getRelatedDisplayFieldName; ?>',
    '<?= $relatedmodulename; ?>',
    <?= $field['fieldid']; ?>,
    '',
    '',
    '',
    '<?= $sourcemodule;?>',
    '<?= $sourceid; ?>')" */
		showCustomer1(
			$el.data("fieldname1"),
			$el.data("fieldname"),
			$el.data("display"),
			$el.data("module"),
			$el.data("fieldid"),
			$el.data("val6"), // 6th param which is ""
			$el.data("val7"), // 7th param which is ""
			$el.data("val8"), // 8th param which is ""
			$el.data("sourcemodule"),
			$el.data("sourceid")
		);
	});
  $(document).on("click", "#showMultiCustomer1", function () {
		const $el = $(this);
    /*onclick="showCustomer1(
    '<?= $fieldname1 ?>',
    '<?= $fieldname ?>',
    '<?= $getRelatedDisplayFieldName; ?>',
    '<?= $relatedmodulename; ?>',
    <?= $field['fieldid']; ?>,
    '',
    '',
    '',
    '<?= $sourcemodule;?>',
    '<?= $sourceid; ?>')" */
		showMultiCustomer1(
			$el.data("fieldname1"),
			$el.data("fieldname"),
			$el.data("display"),
			$el.data("module"),
			$el.data("fieldid"),
			$el.data("val6"), // 6th param which is ""
			$el.data("val7"), // 7th param which is ""
			$el.data("val8"), // 8th param which is ""
			$el.data("sourcemodule"),
			$el.data("sourceid")
		);
	});

  $(document).on("click", "#removeTextValue", function () {
		const $el = $(this);
		removeTextValue($el.data("fieldname1"),$el.data("fieldname"));
	});
  ///adede by deepika

  $(document).on("click", ".add-more-records", async function () {
    const $cell = $(this);
    const module = $cell.data("module");
    const blockid = $cell.data("blockid");
    const geturl = getAbsoluteUrl();

    try {
      startLoading();
      // Ensure Select2 is loaded (dynamically if needed)
      await ensureSelect2Loaded(geturl);

      // Wait for the row to be added via AJAX
      await addRowBtn(blockid, module);

      // Once the row is added, apply Select2 to all `.singleselect`
      applySelect2();

      stopLoading();

    } catch (err) {
      console.error("Error in add-more-records flow:", err);
    }
  });
  function ensureSelect2Loaded(baseUrl) {
    return new Promise((resolve) => {
      if (typeof $.fn.select2 !== 'undefined') {
        console.log("Select2 already loaded");
        resolve();
      } else {
        console.log("Loading Select2...");

        // Load CSS
        const css = document.createElement('link');
        css.rel = 'stylesheet';
        css.href = baseUrl + "thememain/css/select2.min.css";
        document.head.appendChild(css);

        // Load JS
        const script = document.createElement('script');
        script.src = baseUrl + "thememain/js/select2.min.js";
        script.onload = function () {
          console.log("Select2 JS loaded");
          resolve();
        };
        document.head.appendChild(script);
      }
    });
  }
  function applySelect2() {
    $('.singleselect').each(function () {
      if (!$(this).hasClass('select2-hidden-accessible')) {
        $(this).select2({
          placeholder: "Select",
          allowClear: true,
          width: '100%'
        });
      }
    });
    $('.multySelect').each(function () {
      if (!$(this).hasClass('select2-hidden-accessible')) {
        $(this).select2({
          placeholder: "Select",
          allowClear: true,
          width: '100%'
        });
      }
    });
  }



                    //     $(document).on("click", ". module-option", function () {
                         
                    //     selectModule(this);
                    // });
                   
});

// code end added by ptpatel to resolve refrence type onclick issue of server on date 27-06-25 
//code added by ptpatel on date 03-09-2025 to open reset password model from detailview in contact module
$(document).on("click", "#contact_passwordresetbtn", function () {
    $("#contactresetpasswordModal").modal("show");
});

function validatePasswords() {
    var $password = $("#password");
    var $confirm = $("#confirm_password");
    var $saveBtn = $(".contactResetPassword");

    var passVal = $password.val().trim();
    var confirmVal = $confirm.val().trim();
    var valid = true;

    // Reset previous errors
    $password.closest(".form-group").removeClass("error").find(".help-block").text("");
    $confirm.closest(".form-group").removeClass("error").find(".help-block").text("");

    // Regex for strong password
    var passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%?&#])[A-Za-z\d@$!%?&#]{8,}$/;

    // 1. Check password blank
    if (passVal === "") {
        $password.closest(".form-group").addClass("error")
            .find(".help-block").text("Password cannot be blank.");
        valid = false;
    } 
    // 2. Check password strength
    else if (!passwordRegex.test(passVal)) {
        $password.closest(".form-group").addClass("error")
            .find(".help-block").text("Password must be at least 8 characters long, include uppercase, lowercase, number, and special character.");
        valid = false;
    }

    // 3. Check confirm password blank
    if (confirmVal === "") {
        $confirm.closest(".form-group").addClass("error")
            .find(".help-block").text("Confirm Password cannot be blank.");
        valid = false;
    }

    // 4. Check both match
    if (passVal !== "" && confirmVal !== "" && passVal !== confirmVal) {
        $confirm.closest(".form-group").addClass("error")
            .find(".help-block").text("Password and Confirm Password do not match.");
        valid = false;
    }

    // 🔹 Enable/Disable Save button
    if (valid) {
        $saveBtn.prop("disabled", false);
    } else {
        $saveBtn.prop("disabled", true);
    }

    return valid;
}

// Run validation when clicking Save
$(document).on("click", ".contactResetPassword", function (e) {

  console.log("urlparams"+urlParams);
    if (!validatePasswords()) {
        e.preventDefault(); // stop form submit if invalid
    }
    
    $(document).on("click", ".contactResetPassword", function () {
    if (!validatePasswords()) {
        return false; // stop if validation fails
    }

    var recordId = urlParams.get("Record");  // example: 1234
    var password = $("#password").val().trim();
    var confirmPassword = $("#confirm_password").val().trim();

    let  csrfTokenName = $("#csrfTokenName").val();
    let  csrfToken = $("#csrfToken").val();
    $.ajax({
        url: "updatecontactpassword", //  replace with your backend URL
        type: "POST",
        data: {
            record_id: recordId,
            password: password,
            confirm_password: confirmPassword,
            _csrf: csrfToken,
        },
        dataType: "json",
        success: function (response) {
            if (response.success) {
                alert("Password updated successfully!");
                $("#contactresetpasswordModal").modal("hide");
                $("#password").val();$("#confirm_password").val();
            } else {
                alert(response.message || "Failed to update password.");
            }
            $("#password").val();$("#confirm_password").val();
        },
        error: function () {
            alert("An error occurred while updating the password.");
            $("#password").val();$("#confirm_password").val();
        }
    });
});

});

// Run validation live on typing/blur
$(document).on("blur", "#password", function () {
  console.log("blur called from password");
    validatePasswords();
});
$(document).on("blur", "#confirm_password", function () {
  console.log("blur called from confirm_password")
    validatePasswords();
});



//code ended by ptpatel on date 03-09-2025 to open reset password model from detailview in contact module
//Product popup code start vish
window.EXCLUDED_INVENTORY_NUMBER = [];
window.BILL_WH_STATE = '';
window.SHIP_STATE = '';
window.SELECTED_PRODUCTS = {};
window.PER_PAGE = 10;
const MAX_SELECTED_PRODUCTS = 100;
function enforceSelectionLimit() {
  const selectedCount = Object.keys(window.SELECTED_PRODUCTS || {}).length;
  const disable = selectedCount >= MAX_SELECTED_PRODUCTS;

  $('#searchResults input.product-select').each(function () {
    if (!$(this).is(':checked')) {
      $(this).prop('disabled', disable);
    } else {
      $(this).prop('disabled', false);
    }
  });
  $('#so-check-all').prop('disabled', disable);
  $('#select-all-btnSO').prop('disabled', disable);
}

function updateExcludedList() {
  const ids = new Set();
  $('#productTable2781 tbody tr').each(function () {
    var inventory_id = $(this).find('[name*="inventory_id"]').val();
    if (inventory_id) ids.add(inventory_id);
  });
  window.EXCLUDED_INVENTORY_NUMBER = Array.from(ids);
}

$(document).off('change', '#per-page').on('change', '#per-page', function () {
  window.PER_PAGE = parseInt($(this).val(), 10) || 10;
  filterProducts(1);
});
const Loading = {
    show: function () {
        $('#loading-overlay').addClass('active').show();
    },
    hide: function () {
        $('#loading-overlay').removeClass('active').hide();
    }
};
$('.add-more-records').off('click.salesorderPopup').on('click.salesorderPopup', function () { //debugger;
  if ($(this).data("module") != 'salesorder') {
    return;
  }
  var $btn = $(this);
  const mainmodule = $btn.data("module");
  // $btn.prop('disabled', true);

  updateExcludedList();

  var geturl = getAbsoluteUrl();
  var url = geturl + mainmodule + "/popuplistproduct";
  var dataToSend = {
    salesorder_id: $('#record').val(),
    module: mainmodule || '',
    exclude_inv: window.EXCLUDED_INVENTORY_NUMBER
  };

  Loading.show();

  $.ajax({
    type: "POST",
    url: url,
    contentType: "application/json",
    data: JSON.stringify(dataToSend),
    headers: {
      'X-CSRF-Token': $('#csrfToken').val()
    },
    success: function (html) {
      $('#productSearchModalSO').remove();

      $('body').append(html);

      var raw = $('#modalProductData').val();
      var modalData = raw ? JSON.parse(raw) : {};

      window.ALL_PRODUCTS = modalData.allProducts || [];
      window.ALL_CATEGORIES = modalData.categories || [];
      window.ALL_SUBCATEGORIES = modalData.subcategories || [];
      window.TOTAL_COUNT = modalData.totalCount || 0;
      window.CURRENT_PAGE = modalData.page || 1;
      window.PER_PAGE = modalData.perPage || window.PER_PAGE;

      initPopuplistProductModal();

      $('#productSearchModalSO')
        .off('hidden.bs.modal.salesorder')
        .on('hidden.bs.modal.salesorder', function () {
          $('.add-more-records').prop('disabled', false);
          window.SELECTED_PRODUCTS = {};
        })
        .modal('show');
    },
    error: function () {
      alert('Unable to load popup. Please try again.');
      $btn.prop('disabled', false);
    },
    complete: function () {
      Loading.hide();
    }
  });
});

function setBillingState(stateVal) {
  window.BILL_WH_STATE = stateVal;
  recalculateAllRowsGST();
}
function setShippingState(stateVal) {
  window.SHIP_STATE = stateVal;
  recalculateAllRowsGST();
}
$(document).on('change', '#bill_wh_statecode_hidden', function () {
  setBillingState(this.value);
});
$(document).on('change', '#ship_statecode_hidden', function () {
  setShippingState(this.value);
});

function toNum(val) { return parseFloat(val) || 0; }

function updateRowCalculations($row, j) {
  let qty = toNum($row.find('.qty').val());
  let sellingPrice = toNum($row.find('.selling_price').val());
  let basePrice = sellingPrice * qty;

  let gstPer = 0;
  if ($row.find('.gst_percentage').length) {
    gstPer = toNum($row.find('.gst_percentage').val());
  }

  let cgstPer = 0, sgstPer = 0, igstPer = 0;
  if (window.BILL_WH_STATE && window.SHIP_STATE) {
    if (parseInt(window.BILL_WH_STATE, 10) === parseInt(window.SHIP_STATE, 10)) {
      cgstPer = gstPer / 2;
      sgstPer = gstPer / 2;
      igstPer = 0;
    } else {
      cgstPer = 0;
      sgstPer = 0;
      igstPer = gstPer;
    }
  }

  $row.find('.cgst_percentage').val(cgstPer.toFixed(2));
  $row.find('.sgst_percentage').val(sgstPer.toFixed(2));
  $row.find('.igst_percentage').val(igstPer.toFixed(2));

  let cgstAmount = basePrice * cgstPer / 100;
  let sgstAmount = basePrice * sgstPer / 100;
  let igstAmount = basePrice * igstPer / 100;

  $row.find('.base_price_gst_exclude').val(basePrice.toFixed(2));
  $row.find('.cgst_amount').val(cgstAmount.toFixed(2));
  $row.find('.sgst_amount').val(sgstAmount.toFixed(2));
  $row.find('.igst_amount').val(igstAmount.toFixed(2));

  let totalAmount = basePrice + cgstAmount + sgstAmount + igstAmount;
  $row.find('.total_amount').val(totalAmount.toFixed(2));
}

function recalculateAllRowsGST() {
  $('#productTable2781 tbody tr').each(function () {
    updateRowCalculations($(this));
  });
}

function bindDynamicCalcEvents($table) {
  $table.on('input change', '.qty, .selling_price, .selling_price_gst_exclude, .gst_percentage', function () {
    var $row = $(this).closest('tr');
    var $qtyInput = $row.find('.qty');
    var $qtyStockInput = $row.find('.qty_in_stock');
    var qty = parseFloat($qtyInput.val()) || 0;
    var qty_in_stock = parseFloat($qtyStockInput.val()) || 0;
    if (qty > qty_in_stock) {
      $qtyInput.val(qty_in_stock);
      qty = qty_in_stock;
    }
    updateRowCalculations($row);
  });
}
function afterRowCreatedAndPopulated($row, j) {
  updateRowCalculations($row, j);
}

$(function () {
  bindDynamicCalcEvents($('#productTable2781'));
});
$(document).off('click', '#productSearchModalSO .btn-secondary').on('click', '#productSearchModalSO .btn-secondary', function () {
  $('#productSearchModalSO').modal('hide');
});
$(document).off('hidden.bs.modal.salesorder', '#productSearchModalSO').on('hidden.bs.modal.salesorder', '#productSearchModalSO', function () {
  $('.add-more-records').prop('disabled', false);
});
$(document).off('show.bs.modal.salesorder', '#productSearchModalSO').on('show.bs.modal.salesorder', '#productSearchModalSO', function () {
  // $('.add-more-records').prop('disabled', true);
});

function initPopuplistProductModal() {
  $('#productSearchModalSO')
    .off('shown.bs.modal.salesorder')
    .on('shown.bs.modal.salesorder', function () {
      renderCategoryDropdown();
      renderSubcategoryDropdown();
      syncSelectionCheckboxes();
      enforceSelectionLimit();
    });

  $(document)
    .off('change.salesorder', '#search-category')
    .on('change.salesorder', '#search-category', function () {
      renderSubcategoryDropdown($(this).val());
    })
    .off('change.salesorder', '#search-sub-category')
    .on('change.salesorder', '#search-sub-category', function () {
    })
    .off('input.salesorder', '#search-lot-no,#search-product-name,#search-tag-no')
    .on('input.salesorder', '#search-lot-no,#search-product-name,#search-tag-no', function () {
    })
    .off('click.salesorder', '#search-btn')
    .on('click.salesorder', '#search-btn', function () {
      filterProducts(1);
    })
    .off('click.salesorder', '.page-link-popup')
    .on('click.salesorder', '.page-link-popup', function (e) {
      e.preventDefault();
      var page = $(this).data('page');
      filterProducts(page);
    })
    .off('click.salesorder', '#select-all-btnSO')
    .on('click.salesorder', '#select-all-btnSO', function () {
      Loading.show();

      (window.ALL_PRODUCTS || []).forEach(function (prod) {
        if (prod.inventory_id) {
          window.SELECTED_PRODUCTS[prod.inventory_id] = prod;
        }
      });
      syncSelectionCheckboxes();
      enforceSelectionLimit();
      setTimeout(function () {
        Loading.hide();
      }, 400);
    })
    .off('change.salesorder', '#so-check-all')
    .on('change.salesorder', '#so-check-all', function () {
      var checked = $(this).prop('checked');
      $('#searchResults input.product-select').each(function () {
        let product = getProductFromCheckbox($(this));
        let inventory_id = product && product.inventory_id;
        if (checked) {
          if (inventory_id) window.SELECTED_PRODUCTS[inventory_id] = product;
        } else {
          if (inventory_id) delete window.SELECTED_PRODUCTS[inventory_id];
        }
        $(this).prop('checked', checked);
      });
      enforceSelectionLimit();
    })
    .off('change.salesorder', '.product-select')
    .on('change.salesorder', '.product-select', function () {
      let product = getProductFromCheckbox($(this));
      let inventory_id = product && product.inventory_id;
      if ($(this).is(':checked')) {
        if (inventory_id) window.SELECTED_PRODUCTS[inventory_id] = product;
      } else {
        if (inventory_id) delete window.SELECTED_PRODUCTS[inventory_id];
        $('#so-check-all').prop('checked', false);
      }
      enforceSelectionLimit();
    })
    .off('click.salesorder', '#append-selected-btnSO')
    .on('click.salesorder', '#append-selected-btnSO', async function () {
      Loading.show();
      $("#loading-overlay").css('display', 'grid');

      let selected = Object.values(window.SELECTED_PRODUCTS || {});
      let lastIndex = $('#productTable2781 tbody tr').length;
      let currentRow = '';

      for (let i = 0; i < selected.length; i++) {
        const j = lastIndex + i + 1;
        const prod = selected[i];

        await addRowBtn('2781', 'salesorder');
        const $tbody = $('#productTable2781 tbody');
        const $lastRow = $tbody.find('tr:last');
        const rowIndex = $lastRow.index();

        if ($lastRow.length > 0 && currentRow !== rowIndex) {
          $lastRow.find(`#product_name_${j}1`).val(prod.product_id || '');
          $lastRow.find(`#product_name_${j}`).val(prod.product_name || '');
          $lastRow.find(`#tag_number_${j}`).val(prod.tag_number || '');
          $lastRow.find(`#category_${j}`).val(prod.prod_category_value || '');
          $lastRow.find(`#sub_category_${j}`).val(prod.sub_catagory_value || '');
          $lastRow.find(`#qty_in_stock_${j}`).val(prod.qty || '');
          $lastRow.find(`#qty_${j}`).val();
          $lastRow.find(`#hsn_code_${j}`).val(prod.hsn_code || '');
          $lastRow.find(`#gst_percentage_${j}`).val(prod.gst_percentage || '');
          $lastRow.find(`#purchase_price_${j}`).val(prod.quoted_price_gst_exclude || '');
          $lastRow.find(`#selling_price_gst_exclude_${j}`).val(prod.sp_exclusive_gst || '');
          $lastRow.find(`#base_price_gst_exclude_${j}`).val(prod.base_price_gst_excluded || '');
          // $lastRow.find(`#inventory_id_${j}`).val(prod.inventory_id || '');
          $lastRow.find('[name$="[inventory_id]"]').val(prod.inventory_id || '');
          afterRowCreatedAndPopulated($lastRow, j);
          currentRow = rowIndex;
        }
      }

      Loading.hide();
      $("#loading-overlay").css('display', 'none');
      $('#productSearchModalSO').modal('hide');
      $('.add-more-records').prop('disabled', false);
    })
    .off('click.salesorder', '.remove-row-btn')
    .on('click.salesorder', '.remove-row-btn', function () {
      $(this).closest('tr').remove();
      updateExcludedList();
    })
    .off('click.salesorder', '#add-by-tag-btn')
  .on('click.salesorder', '#add-by-tag-btn', function () {
    var tagNo = $('#search-tag-no').val().trim();
    if (!tagNo) {
      alert('Please enter a Tag Number');
      return;
    }

    var geturl = getAbsoluteUrl();
    var url = geturl + 'salesorder/popuplistproduct';

    var queryParams = {
      page: 1,
      per_page: 1,
      search_tag_no: tagNo,
      ajax: 1,
      by_tag: 1       
    };

    var dataToSend = {
      salesorder_id: $('#record').val(),
      module: 'salesorder',
      exclude_inv: window.EXCLUDED_INVENTORY_NUMBER
    };

    Loading.show();

    $.ajax({
      type: 'POST',
      url: url + '?' + $.param(queryParams),
      contentType: 'application/json',
      data: JSON.stringify(dataToSend),
      headers: { 'X-CSRF-Token': $('#csrfToken').val() },
      success: function (html) {
        var $fragment = $('<div>').html(html);
        var $newModalData = $fragment.find('#modalProductData');
        if (!$newModalData.length) {
          alert('Tag not found.');
          return;
        }

        $('#modalProductData').remove();
        $('body').append($newModalData);

        var raw = $('#modalProductData').val();
        var modalData = raw ? JSON.parse(raw) : {};
        var products = modalData.allProducts || [];

        if (!products.length) {
          alert('Tag not found.');
          return;
        }

        var prod = products[0];

        (async function () {
          await addRowBtn('2781', 'salesorder');
          var $tbody   = $('#productTable2781 tbody');
          var $lastRow = $tbody.find('tr:last');

          if ($lastRow.length) {
            var $invInput = $lastRow.find('.inventory_id');
            var invIdAttr = $invInput.attr('id') || ''; 
            var m = invIdAttr.match(/^inventory_id_(\d+)$/);
            var j = m ? parseInt(m[1], 10) : 0;

            $lastRow.find(`#product_name_${j}1`).val(prod.product_id || '');
            $lastRow.find(`#product_name_${j}`).val(prod.product_name || '');
            $lastRow.find(`#tag_number_${j}`).val(prod.tag_number || '');
            $lastRow.find(`#category_${j}`).val(prod.prod_category_value || '');
            $lastRow.find(`#sub_category_${j}`).val(prod.sub_catagory_value || '');
            $lastRow.find(`#qty_in_stock_${j}`).val(prod.qty || '');
            $lastRow.find(`#qty_${j}`).val();
            $lastRow.find(`#hsn_code_${j}`).val(prod.hsn_code || '');
            $lastRow.find(`#gst_percentage_${j}`).val(prod.gst_percentage || '');
            $lastRow.find(`#purchase_price_${j}`).val(prod.quoted_price_gst_exclude || '');
            $lastRow.find(`#selling_price_gst_exclude_${j}`).val(prod.sp_exclusive_gst || '');
            $lastRow.find(`#base_price_gst_exclude_${j}`).val(prod.base_price_gst_excluded || '');
            $invInput.val(prod.inventory_id || '');

            afterRowCreatedAndPopulated($lastRow, j);
            updateExcludedList();
          }
        })();
      },
      error: function () {
        alert('Error while searching by Tag Number.');
        Loading.hide();
      },
      complete: function () {
        Loading.hide();
      }
    });
  });
}

function getSearchParams() {
  return {
    search_lot_no: $('#search-lot-no').val() || '',
    search_product_name: $('#search-product-name').val() || '',
    search_category: $('#search-category').val() || '',
    search_sub_category: $('#search-sub-category').val() || '',
    search_tag_no: $('#search-tag-no').val() || ''
  };
}

function filterProducts(page) {
  page = page || 1;
  var geturl = getAbsoluteUrl();
  var url = geturl + 'salesorder/popuplistproduct';

  var queryParams = $.extend({}, getSearchParams(), {
    page: page,
    per_page: window.PER_PAGE || 10,
    ajax: 1
  });

  var dataToSend = {
    salesorder_id: $('#record').val(),
    module: 'salesorder',
    exclude_inv: window.EXCLUDED_INVENTORY_NUMBER
  };

  Loading.show();

  $.ajax({
    type: 'POST',
    url: url + '?' + $.param(queryParams),
    contentType: 'application/json',
    data: JSON.stringify(dataToSend),
    headers: { 'X-CSRF-Token': $('#csrfToken').val() },
    success: function (html) {
      var $fragment = $('<div>').html(html);

      var $newResults = $fragment.find('#searchResults');
      if ($newResults.length) {
        $('#searchResults').replaceWith($newResults);
      }

      var $newPagination = $fragment.find('#search-pagination');
      if ($newPagination.length) {
        $('#search-pagination').replaceWith($newPagination);
      }

      $('#modalProductData').remove();
      var $newModalData = $fragment.find('#modalProductData');
      if ($newModalData.length) {
        $('body').append($newModalData);
      }

      var raw = $('#modalProductData').val();
      var modalData = raw ? JSON.parse(raw) : {};

      window.ALL_PRODUCTS = modalData.allProducts || [];
      window.ALL_CATEGORIES = modalData.categories || [];
      window.ALL_SUBCATEGORIES = modalData.subcategories || [];
      window.TOTAL_COUNT = modalData.totalCount || 0;
      window.CURRENT_PAGE = modalData.page || page;
      window.PER_PAGE = modalData.perPage || window.PER_PAGE;

      syncSelectionCheckboxes();
      enforceSelectionLimit();
    },
    error: function () {
      alert('Unable to filter products. Please try again.');
    },
    complete: function () {
      Loading.hide();
    }
  });
}

function renderCategoryDropdown() {
  let html = '<option value="">Category</option>';
  (window.ALL_CATEGORIES || []).forEach(function (cat) {
    html += `<option value="${cat.prod_category_id}">${cat.prod_category_value}</option>`;
  });
  $('#search-category').html(html);
}

function renderSubcategoryDropdown(selectedCategory) {
  let html = '<option value="">Sub-Category</option>';
  (window.ALL_SUBCATEGORIES || []).forEach(function (subcat) {
    if (!selectedCategory || subcat.prod_catagory_id == selectedCategory) {
      html += `<option value="${subcat.sub_catagory_id}">${subcat.sub_catagory_value}</option>`;
    }
  });
  $('#search-sub-category').html(html);
}

function getProductFromCheckbox($checkbox) {
  let product = $checkbox.data('raw');
  if (!product) {
    try {
      product = JSON.parse($checkbox.attr('data-raw') || '{}');
    } catch (e) {
      product = null;
    }
  }
  return product;
}

function syncSelectionCheckboxes() {
  $('#searchResults input.product-select').each(function () {
    let product = getProductFromCheckbox($(this));
    let inventory_id = product && product.inventory_id;
    $(this).prop('checked', inventory_id && window.SELECTED_PRODUCTS.hasOwnProperty(inventory_id));
  });

  var pageRows = window.ALL_PRODUCTS || [];
  var allChecked = pageRows.length > 0 && pageRows.every(function (row) {
    return row.inventory_id && window.SELECTED_PRODUCTS.hasOwnProperty(row.inventory_id);
  });
  $('#so-check-all').prop('checked', allChecked);
}


(function () {
  const tables = document.querySelectorAll('table .pin-icon, table .col-pinned')
    ? Array.from(document.querySelectorAll('table'))
        .filter(t => t.querySelector('.pin-icon, .col-pinned'))
    : [];

  tables.forEach(setupPinnedTable);

  function setupPinnedTable(table) {
    const tbody = table.querySelector('tbody');
    if (!tbody) return;

   
    function initBody() {
      tbody.querySelectorAll('tr').forEach(tr => {
        const tds = tr.querySelectorAll('td');
        tds.forEach((td, i) => {
          if (!td.dataset.col) td.dataset.col = String(i + 1);
        });
      });
    }

    function recomputePinnedOffsets() {
      const pinnedHeaders = Array.from(
        table.querySelectorAll('thead th.pinned')
      );

      pinnedHeaders.sort((a, b) =>
        Number(a.dataset.col) - Number(b.dataset.col)
      );

      let currentLeft = 0;
      const offsets = {};

      pinnedHeaders.forEach(th => {
        const col = th.dataset.col;
        offsets[col] = currentLeft;

        const sample =
          th || table.querySelector('tbody td[data-col="' + col + '"]');
        const width = sample ? sample.getBoundingClientRect().width : 0;
        currentLeft += width;
      });

      table.querySelectorAll('[data-col]').forEach(cell => {
        if (cell.classList.contains('pinned')) return;
        cell.style.left = '';
      });

      Object.keys(offsets).forEach(col => {
        const left = offsets[col] + 'px';
        table.querySelectorAll('[data-col="' + col + '"].pinned')
          .forEach(cell => { cell.style.left = left; });
      });
    }

    function togglePin(col) {
      const header = table.querySelector('thead th[data-col="' + col + '"]');
      if (!header) return;

      const shouldPin = !header.classList.contains('pinned');

      header.classList.toggle('pinned', shouldPin);

      tbody.querySelectorAll('td[data-col="' + col + '"]')
        .forEach(td => {
          if (!td.classList.contains('col-pinned')) return;
          td.classList.toggle('pinned', shouldPin);
          if (!shouldPin) td.style.left = '';
        });

      const icon = header.querySelector('.pin-icon[data-col="' + col + '"]');
      if (icon) {
        icon.style.color = shouldPin ? '#5c9cff' : '#add8e6';
        icon.classList.toggle('fa-lock', shouldPin);
        icon.classList.toggle('fa-unlock', !shouldPin);
      }

      if (!shouldPin) header.style.left = '';

      recomputePinnedOffsets();
    }

    table.addEventListener('click', e => {
      const icon = e.target.closest('.pin-icon');
      const th   = e.target.closest('th');
      let col = null;

      if (icon) col = icon.dataset.col;
      else if (th && th.dataset.col) col = th.dataset.col;
      if (!col) return;

      const header = table.querySelector('thead th[data-col="' + col + '"]');
      if (!header || !header.classList.contains('col-pinned')) return;

      togglePin(col);
    });

    function resetPinnedColumns(table) {
      if (!table) return;

      table.querySelectorAll('th.pinned, td.pinned').forEach(cell => {
        cell.classList.remove('pinned');
        cell.style.left = '';
      });

      table.querySelectorAll('.pin-icon').forEach(icon => {
        icon.style.color = '#add8e6';
        icon.classList.remove('fa-lock');
        icon.classList.remove('fa-unlock');
      });
    }


    const observer = new MutationObserver(mutations => {
    let rowAdded = false;

    mutations.forEach(mutation => {
      if (mutation.type === 'childList' && mutation.addedNodes.length) {
        mutation.addedNodes.forEach(node => {
          if (node.nodeType === 1 && node.tagName === 'TR') {
            rowAdded = true;

            const newRow = node;
            const tds = newRow.querySelectorAll('td');
            tds.forEach((td, i) => {
              if (!td.dataset.col) td.dataset.col = String(i + 1);
            });

            const pinnedCols = Array.from(
              table.querySelectorAll('thead th.pinned')
            ).map(th => th.dataset.col);

            pinnedCols.forEach(col => {
              const td = newRow.querySelector('td[data-col="' + col + '"]');
              if (td && td.classList.contains('col-pinned')) {
                td.classList.add('pinned');
              }
            });
          }
        });
      }
    });

    if (rowAdded) {
      recomputePinnedOffsets();
    }
  });

  observer.observe(tbody, {
    childList: true,
    subtree: false
  });


    observer.observe(tbody, { childList: true });

    initBody();
    recomputePinnedOffsets();
  }
})();
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('thead th.col-pinned .pin-icon').forEach(icon => {
      icon.click();
    });
  });
  // coded for product popup vishwas end


  
function openAdvancedSearch() {
    $('#adv-search-modal').modal('show');
}
// document.addEventListener('change', function (e) { 
//     if (e.target.matches('input[type="file"]')) {
//         const input = e.target;
//         const fileName = input.value || (input.files[0]?.name || '');
//         const ext = fileName.split('.').pop().toLowerCase();

//         input.classList.remove('error');

//         if (ext === 'msg') {
//             input.classList.add('error');
//             input.value = '';
//         }
//     }
// });

//preview code

$(document).on("change", ".temp-file", function () {
    let module = $(this).data("module");
    // let preview = $(this).next(".file-preview");
     let preview = $(this).closest(".form-group").find(".file-preview");
     $(this).closest(".form-group").find(".help-block").text('');
     $(this).closest(".form-group").find(".file-preview").html('');
     //validation
     let file = this.files[0];
      if (file) {
        // if (fieldClass.includes("F~M") && !file) {
        //     errorMessage = `Please upload a file.`;
        //     isValid = false;
        // } else 
          if (file) {
            // Allow these MIME types
            //allowed XLS file code added on date 04-09-2025 by ptpatel as per client request email 
            //application/vnd.openxmlformats-officedocument.spreadsheetml.sheet for XLSX 
            //ERP Point 58 .eml allowed
            var allowedTypes = ["image/jpeg", "image/png", "application/pdf", "application/zip", "application/vnd.ms-excel", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                // For .eml files
                "message/rfc822",           // standard MIME type for .eml
                "application/vnd.ms-outlook", // sometimes used for Outlook .msg/.eml
            ];
            
            // Check file type by MIME type first
            if (!allowedTypes.includes(file.type)) {
                // Check file extension if MIME type does not match
                //fileExtension !== "eml" added for ERP Point 58
                var fileExtension = file.name.split('.').pop().toLowerCase();
                console.log("fileExtension"+fileExtension);
                if (fileExtension != 'eml' && fileExtension != 'zip'  && fileExtension != 'msg' && !allowedTypes.includes(file.type)) {
                    errorMessage = `Please upload a valid file (JPEG, PNG, PDF, ZIP, XLS or XLSX, .EML, .MSG).`;
                    isValid = false;
                    $(this).closest(".form-group").find(".help-block").text(errorMessage);
                }
            }

            // Check file size
            // if (file.size > 5 * 1024 * 1024) {
            //change as per ERP finding point no 405 as per sheet V2 by ptpatel on date 02-09-2025
            //5 MB to 200 MB
            if (file.size > 200 * 1024 * 1024) {
                errorMessage = `The file size should not exceed 200MB.`;
                isValid = false;
            }
        }
      }
     //end validation
    let fd = new FormData();
    fd.append("file", this.files[0]);
    fd.append("_csrf", $('meta[name="csrf-token"]').attr("content"));

    $.ajax({
        url: "tempupload?module=" + module,
        type: "POST",
        data: fd,
        contentType: false,
        processData: false,
        success: function (res) {
            if (res.status === "success") {
                preview.html(renderPreview(res.url, res.type));
            }
        }
    });
});
function renderPreview(url, type) {
    const pathmatch =  getBaseeUrl().replace(location.origin, "");
    const imageTypes = ["jpg", "jpeg", "png", "svg", "webp"];

    let iconImage = "fileicon_img.svg";

    if (type === "pdf") iconImage = "fileicon_pdf.svg";
    else if (["doc","docx"].includes(type)) iconImage = "fileicon_doc.svg";
    else if (["xls"].includes(type)) iconImage = "fileicon_xls.svg";
    else if (["xlsx"].includes(type)) iconImage = "fileicon_xlsx.svg";
    else if (["msg"].includes(type)) iconImage = "fileicon_msg.svg";
    else if (["eml"].includes(type)) iconImage = "fileicon_eml.svg";
    else if (["zip"].includes(type)) iconImage = "fileicon_zip.svg";
    else if (imageTypes.includes(type)) iconImage = "fileicon_img.svg";
    const iconPath = `${pathmatch}thememain/img/file-icon/${iconImage}`;
    const hoverThumb = imageTypes.includes(type)
        ? `<img src="${url}" class="file-hover-thumb">`
        : '';

    const previewTitle = imageTypes.includes(type) ? "" : "Preview File";

    return `
        <a href="${url}" target="_blank" title="${previewTitle}" class="file-preview-wrapper">
            <img src="${iconPath}" class="file-icon-img" alt="${type} file">
            ${hoverThumb}
        </a>
    `;
}

$(document).on("input blur", "[data-isunique='1']", function () {
    chcekduplicate(this);
});

function chcekduplicate(element) {
    const urlParams = new URLSearchParams(window.location.search);
    const recordid = urlParams.get('Record');
    console.log("recordid"+recordid);
    var $input = $(element);
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
        url: "checkduplicate",  
        type: "GET",
        data: {
            fieldName: field,
            value: value,
            ignoreId : recordid,
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
}

function toggleSaveButton() {
      if ($(".form-group.error").length > 0 || $(".help-block:contains('required')").length > 0) {
          $(".savebutton").prop("disabled", true);
      } else {
          $(".savebutton").prop("disabled", false);
      }
    }
     function showConfirm(msg = "Do you want to save the record?") {
        // Delegate to the global premium custom confirm dialog
        if (typeof window.showCustomConfirm === 'function') {
            return window.showCustomConfirm('Confirm Action', msg, 'Yes, Save', 'Cancel', 'primary');
        }
        // Fallback if custom-alerts.js hasn't loaded
        return Promise.resolve(confirm(msg));
    }
(function() {
   function updateNavbarMore() {
      var mainContainer = document.getElementById('navbarMainItems');
      var moreWrapper   = document.getElementById('navbarMore');
      var moreContent   = document.getElementById('navbarMoreContent');

      if (!mainContainer || !moreWrapper || !moreContent) return;

      Array.from(moreContent.children).forEach(function(el) {
         mainContainer.appendChild(el);
      });

      moreWrapper.style.display = 'inline-block';
      var moreWidth = moreWrapper.offsetWidth || 80;

      var container = mainContainer.parentElement; 
      var availableWidth = container.clientWidth - moreWidth;
      var usedWidth = 0;

      var items = Array.from(mainContainer.children);
      for (var i = 0; i < items.length; i++) {
         usedWidth += items[i].offsetWidth;
      }

      if (usedWidth <= availableWidth) {
         moreWrapper.style.display = 'none';
         return;
      }

      usedWidth = 0;
      items = Array.from(mainContainer.children);
      for (var j = 0; j < items.length; j++) {
         usedWidth += items[j].offsetWidth;
      }
      for (var k = items.length - 1; k >= 0 && usedWidth > availableWidth; k--) {
         var el = items[k];
         usedWidth -= el.offsetWidth;
         moreContent.insertBefore(el, moreContent.firstChild);
         el.classList.remove('active');
      }

      moreWrapper.style.display = 'inline-block';
   }

   window.addEventListener('load', updateNavbarMore);
   window.addEventListener('resize', function() {
      clearTimeout(window.__navbarMoreTimer);
      window.__navbarMoreTimer = setTimeout(updateNavbarMore, 150);
   });
})();
//for purchase order DEVIT refrence number add via related module + btn added by ptpatel on date 30-03-2026
function addMultiSelectTag({
    containerId,
    hiddenFieldId,
    text,
    value
}) {
    // Prevent duplicate
    if ($(containerId + ' span[data-id="' + value + '"]').length) {
        return;
    }

    let tagHtml = `
        <span class="tag" data-id="${value}">
            ${text}
            
        </span>
    `;

    $(containerId).append(tagHtml);

    // Store values in hidden field (comma-separated)
    let existing = $(hiddenFieldId).val();
    let values = existing ? existing.split(',') : [];

    if (!values.includes(String(value))) {
        values.push(value);
        $(hiddenFieldId).val(values.join(','));
    }
}

$("#pristine-valid-example").on("keydown", "input, select, textarea", function (e) {
    if (e.key === "Enter" && !$(e.target).is("textarea")) {
        e.preventDefault();
        return false;
    }
});
