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

function showCustomer1(
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
    maintabid;
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

  if (dependent == "") {
    alert("First select " + dependentfield);
  } else {
    var geturl = getAbsoluteUrl();
    // alert(geturl);
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
    } else
      data = {
        pageNumberpre: pageNumberpre,
        pageNumber: pageNumber,
        searchparam: searchparam,
        dependent: dependentfield,
        conditionfield: conditionfield,
        dependentval: dependent,
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
      $(".c-faqs__items").html(data);
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
  // alert("deep"+recordid);
  document.getElementById(field).value = recordvalue;
  document.getElementById(hiddenfield).value = recordid;
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
  var call_information_outgoing_call_status = $(
    "#call_information_outgoing_call_status"
  ).val();
  // alert(call_information_outgoing_call_status);
  start_date_time =
    call_information_start_date + " " + call_information_start_time;

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

  if (!$("#attendees").val().trim()) {
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
  participants = "";
  $(".attendee").each(function () {
    console.log($(this).attr("data-id"));
    participants = $(this).attr("data-id") + "," + participants;
  });

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
        participants: participants,
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
// Fetch filtered data from the backend
function fetchFilteredList() {
  console.log("deep");
  const dropdownList = document.getElementById("dropdownList");
  dropdownList.innerHTML = ""; // Clear the previous list
  // alert(dropdownList);
  const input = document.getElementById("attendees").value;
  // inputval = encodeURIComponent(input);
  if (input != "") {
    fetch(`searchusers?query=${encodeURIComponent(input)}`)
      .then((response) => response.json())
      .then((data) => {
        if (data == "") ropdownList.innerHTML = "";

        data.forEach((item) => {
          const li = document.createElement("li");
          li.textContent = item.showfield;
          li.dataset.id = item.id; // Save the ID in a dataset attribute
          li.onclick = () => addToContainer(item.id, item.showfield);
          dropdownList.appendChild(li);
        });
      })
      .catch((error) => console.error("Error fetching data:", error));
  } else {
    dropdownList.innerHTML = "";
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
//ck editor
CKEDITOR.ClassicEditor.create(document.getElementById("modnotes"), {
  toolbar: {
      items: [
          // 'exportPDF','exportWord', '|',
          'heading', '|',
          'bold', 'italic', 'strikethrough', 'underline', 'code', 'subscript', 'superscript', 'removeFormat', '|',
          'bulletedList', 'numberedList', 'todoList', '|',
          'outdent', 'indent', '|',
          'undo', 'redo',
          '-',
          'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
          'alignment', '|',
          // 'link', 'insertImage', 'blockQuote', 'insertTable', 'mediaEmbed', 'codeBlock', 'htmlEmbed', '|',
          'link', 'blockQuote',  '|',
          // 'specialCharacters', 'horizontalLine', 'pageBreak', '|',
          'specialCharacters', 'horizontalLine',  '|',
          // 'textPartLanguage', '|',
          // 'textPartLanguage', '|',
          // 'sourceEditing',
      ],
      shouldNotGroupWhenFull: true
  },
  heading: {
      options: [
          { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
          { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
          { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
          { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
          { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
          { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
          { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
      ]
  },
  // The "super-build" contains more premium features that require additional configuration, disable them below.
  // Do not turn them on unless you read the documentation and know how to configure them and setup the editor.
  removePlugins: [
      'CKBox',
      'CKFinder',
      'EasyImage',
      'RealTimeCollaborativeComments',
      'RealTimeCollaborativeTrackChanges',
      'RealTimeCollaborativeRevisionHistory',
      'PresenceList',
      'Comments',
      'TrackChanges',
      'TrackChangesData',
      'RevisionHistory',
      'Pagination',
      'WProofreader',
      'MathType'
  ]
}).then( editor => {
  myEditor = editor;
} )
.catch( err => {
  console.error( err.stack );
} );



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

//save notes
$(document).on("click", ".post-btn", function (e) {
  e.preventDefault();

  let $button = $(this); // Cache the button reference
  $button.text("Posting..").prop("disabled", true);

  // let modnotesval = editorInstance ? editorInstance.getData() : ""; // Get editor content
  let modnotesval = document.getElementById("modnotes").value; // Get editor content
  let Recordid = $("#Recordid").val();
  let fileInput = $("#attach-notes")[0].files[0];
  let csrfToken = $("#csrfToken").val();

  if (!modnotesval.trim() && !fileInput) {
    alert("Please provide either a file or some text!");
    $button.text("Post").prop("disabled", false); // Reset button state
    return;
  }

  let formData = new FormData();
  if (fileInput) {
    formData.append("file", fileInput);
  }
  formData.append("modnotesval", modnotesval);
  formData.append("Recordid", Recordid);
  formData.append("_csrf", csrfToken);

  // Call the `postnotes` function
  postnotes(formData, "upload-status", Recordid)
    .then(() => {
      // Handle successful response
      alert("Notes successfully posted!");

      // Reset CKEditor instance and file input
      if (editorInstance) {
        editorInstance.setData(""); // Clear CKEditor content
      }
      $("#attach-notes").val(""); // Clear file input field

      $button.text("Post").prop("disabled", false); // Reset button state
    })
    .catch((error) => {
      // Handle errors
      console.error("Error posting notes:", error);
      alert("An error occurred while posting notes.");
      $button.text("Post").prop("disabled", false); // Reset button state
    });
});

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
  $(".search-icon").attr("onclick", onclickval);
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

$(document).on("change", "#related_to", function () {
  // alert("fdgfd");
  $("#related_to_id").val("");
  var tabid = $(this).val();
  var onclickval = $("#uni_related" + tabid).val();
  // alert(onclickval);
  $(".search-icon").attr("onclick", onclickval);
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
function addRowBtn(blockid, mainmodule) {
  //alert("bxbxc");
  let totalRows = $("#productTable" + blockid + " tr").length;
  var geturl = getAbsoluteUrl();
  var url =
    geturl +
    mainmodule +
    "/getproductlist?blockid=" +
    blockid +
    "&cnt_rows=" +
    totalRows;
  // Get the table body
  const tableBody = document.querySelector(
    "#productTable" + blockid + " tbody"
  );

  //fetch from ajax

  $.ajax({
    type: "GET",
    url: url,
    // async:false,
    // data: data,
    success: function (data) {
      //$('#productTable' + blockid + ' tr:last').after(data);
      var tbody = $("#productTable" + blockid + " tbody");
      if (tbody.find("tr").length) {
        tbody.find("tr:last").after(data);
      } else {
        tbody.append(data);
      }
      // $("#updateModel").modal("show").find(".modal-content").html(data);
    },
    error: function (data) {
      // if error occured

      alert("Error occured.please try again");
    },
    dataType: "html",
  });
}
// remove multipl block row
$(document).on("click", ".remove-row-btn", function () {
  $(this).closest("tr").remove();
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
