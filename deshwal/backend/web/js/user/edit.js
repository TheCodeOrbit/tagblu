$(document).ready(function () {
  var newURL = window.location.href;
  var newURL = window.location.href;
  var module = "leads";
  var str = newURL.split(module);
  console.log("str" + str[0]);
  // var slicestr=newURL.substring(0,str);
  editusrl = str[0] + "leads/list";
  console.log("url" + editusrl);




  const fileInput = document.getElementById("profilepic");

  // Check if the file input exists
  if (fileInput) {
    // Create the note element
    const fileNote = document.createElement("div");
    fileNote.id = "fileNote";
    fileNote.innerHTML =
      "Note: Allowed file types PNG, JPG, JPEG and maximum file size allowed is 5 MB.";
    fileNote.style.color = "gray";
    fileNote.style.fontSize = "small";

    // Insert the note below the file input field
    fileInput.insertAdjacentElement("afterend", fileNote);
  }

  // Note: Allowed file type PNG,JPG,JPEG and Maximum file size allow 5 MB

  var email = $("#email").val().trim();
  var username = $("#username").val().trim();
  const emailBlock = $("#email").closest(".form-group").find(".help-block");
  const usernameBlock = $("#username")
    .closest(".form-group")
    .find(".help-block");

  $(document).on("change", "#username", function () {
    userval = $(this).val();
    if (userval) {
      $.ajax({
        url: "dupvalidationuser",
        method: "POST",
        data: {
          username: userval,
          _csrf: yii.getCsrfToken(),
        },
        success: function (response) {
          usernameBlock.text("");

          if (response.status.includes("username_error")) {
            usernameBlock.text(userval + " Username is already taken.");
            $("#username").val("");
          }
        },
        error: function () {
          usernameBlock.text(
            "An error occurred while validating the username."
          );
        },
      });
    }
  });
  $(document).on("change", "#email", function () {
    email = $(this).val();
    if (email) {
      $.ajax({
        url: "dupvalidationuser",
        method: "POST",
        data: {
          email: email,
          _csrf: yii.getCsrfToken(),
        },
        success: function (response) {
          usernameBlock.text("");

          if (response.status.includes("email_error")) {
            emailBlock.text(email + " Emaid id is already exist.");
            $("#email").val("");
          }
        },
        error: function () {
          emailBlock.text("An error occurred while validating the email.");
        },
      });
    }
  });
  //code added by ptpatel on date 26-05-25 to fetch fyear and fyname from database
  $(document).on('load',  function () {
    let elementId = $(this).attr('id'); 
    let trid = elementId.split('_').pop(); // "1"
     startLoading();
    $.ajax({
      url: "getfyear",
      method: "POST",
      data: {
        _csrf: yii.getCsrfToken(),
      },
      success: function (response) {
        // year_
        const fyearDropdown = $("#year_" + trid)
          .empty()
          .append('<option value="">Select Year</option>');
           data.fyears.forEach((fyear) => {
            fyearDropdown.append(
              `<option value="${fyear.yearid}">${fyear.yearname}</option>`
            );
          });
          fyearDropdown.trigger("change");
          $("#year_" + trid).trigger("change");
      },
      error: function () {
        // emailBlock.text("An error occurred while validating the email.");
      },
    });
  });
  //end code added by ptpatel on date 26-05-25
});

////////////make reports_to is readonly/////////
$("#reports_to").removeClass("singleselect");
$("#reports_to").attr("readonly", "readonly");
$('#role').on('change', function (e) {
  var role = e.target.value;  // Get the selected value
  // role = $(role).val();
  if (role) {
    $.ajax({
      url: "getroleusers",
      method: "POST",
      data: {
        role: role,
        _csrf: yii.getCsrfToken(),
      },
      dataType: 'json',
      success: function (response) {
        if (response.users) {
          $('#reports_to').html("");
          $('#reports_to').append('<option value="">Select</option>');
          // Loop through the users array
          $.each(response.users, function (index, user) {
            // alert(user.id + '">' + user.fullname);
            // Append user data to the #user-list div
            $('#reports_to').append('<option value="' + user.id + '">' + user.fullname + '</option>');
          });
          // Remove the readonly attribute
          $("#reports_to").removeAttr("readonly");
          $("#reports_to").select2();
        }
      },
      error: function () {
        console.log("An error occurred.");
        // emailBlock.text("An error occurred while validating the email.");
      },
    });
  }
});
//code added by ptpatel on date 04-09-2025 username should not contain space
$(document).on("keydown", "#username", function () {
  if (e.key === " ") {
    e.preventDefault();
  }
});
// remove space while user paste text
$(document).on("input", "#username", function () {
  $(this).val($(this).val().replace(/\s/g, ""));
});
$(document).on("click", "#user_deactivate_btn", function (e) {
    e.preventDefault(); // prevent page reload
    let btnText = $("#user_deactivate_btn").text().trim();
      console.log(btnText);
    if (confirm("Are you sure you want to "+btnText+" this user?")) {
      startLoading();
      
      let urlParams = new URLSearchParams(window.location.search);
       var recordId = urlParams.get("Record");
        let  csrfTokenName = $("#csrfTokenName").val();
        let  csrfToken = $("#csrfToken").val();
        $.ajax({
            url: "deactivateuser", //  replace with your backend URL
            type: "POST",
            data: {
                record_id: recordId,
                _csrf: csrfToken,
            },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    alert("User "+btnText+" successfully!");
                } else {
                    alert(response.message || "Failed to "+btnText+" user.");
                }
                stopLoading();
                window.location.reload();
            },
            error: function () {
                alert("An error occurred while "+btnText+" user.");
                stopLoading();
            }
        });
    } else {
        console.log("Action cancelled.");
    }
});
  // code added by ptpatel on date 04-09-2025 
