$(document).ready(function () {
  var form = document.getElementById("update-password-form");
  $(".updateprofile").on("click", function (event) {
    event.preventDefault();
    let isValid = true;

    // Clear previous error messages
    document
      .querySelectorAll(".error-message")
      .forEach((el) => (el.textContent = ""));

    // Get field values
    const currentPassword = document
      .querySelector("#current_password")
      .value.trim();
    const newPassword = document.querySelector("#new_password").value.trim();
    const confirmPassword = document
      .querySelector("#confirm_password")
      .value.trim();

    // Validation for Current Password
    if (currentPassword) {
      //   document.querySelector("#current_password_error").textContent =
      //     "Current password must be at least 6 characters.";
      //   isValid = false;
      csrfTokenName = $("#csrfTokenName").val();
      csrfToken = $("#csrfToken").val();
      $.ajax({
        url: "validatepassword", // Replace with your actual action URL
        method: "POST",
        data: {
          current_password: currentPassword,
          _csrf: csrfToken,
        },
        success: function (response) {
          if (response.status === "error") {
            document.querySelector("#current_password_error").textContent =
              response.message;
            isValid = false;
          }
        },
        async: false, // Ensures synchronous validation
      });
    }

    // Validation for New Password
    const passwordRegex =
      /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%?&#])[A-Za-z\d@$!%?&#]{8,}$/;
    if (currentPassword && !newPassword && !confirmPassword) {
      document.querySelector("#new_password_error").textContent =
        "New password is required ";
      document.querySelector("#confirm_password_error").textContent =
        "Confirm password is required";
      isValid = false;
    } else if (newPassword && !passwordRegex.test(newPassword)) {
      document.querySelector("#new_password_error").textContent =
        "New password must be at least 8 characters, include uppercase, lowercase, a number, and a special character.";
      isValid = false;
    }

    // Validation for Confirm Password
    if (newPassword && confirmPassword !== newPassword) {
      document.querySelector("#confirm_password_error").textContent =
        "Confirm password does not match new password.";
      isValid = false;
    }

    // Validate Email (Optional)
    const email = document.querySelector("#email").value.trim();
    if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      document.querySelector("#email_error").textContent =
        "Invalid email format.";
      isValid = false;
    }

    const profilePicInput = document.querySelector("#profilepic");
    const profilePicError = document.querySelector("#profilepic_error");

    // Clear previous error message
    profilePicError.textContent = "";

    // Check only if a file is selected
    if (profilePicInput.files.length > 0) {
      const file = profilePicInput.files[0];
      const allowedExtensions = ["image/jpeg", "image/png", "image/jpg"];
      const maxSize = 5 * 1024 * 1024; // 5 MB

      // Validate file type
      if (!allowedExtensions.includes(file.type)) {
        profilePicError.textContent =
          "Only PNG, JPG, and JPEG images are allowed.";
        isValid = false;
      }

      // Validate file size
      if (file.size > maxSize) {
        profilePicError.textContent = "File size cannot exceed 5MB.";
        isValid = false;
      }
    }
    
    // Prevent form submission if validation fails
    if (isValid) {
      form.submit();
    } else {
      // Prevent form submission if any field is invalid
      e.preventDefault();
      $("html, body").animate(
        {
          scrollTop: $(".help-block:visible:first").offset().top,
        },
        500
      );
    }
  });
});
