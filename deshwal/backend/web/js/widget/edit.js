$(document).ready(function () {
    // $("#profile_name")
     data = { _csrf: $("#csrfToken").val() };
  const ProfileDropdown = $("#profile_name" ).empty().append('<option value="">Select Profile</option>');
  $.ajax({
    type: "POST",
    url: "getprofiles",
    // async:false,
    data: data,
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {

        response.data.forEach((profile) => {

          ProfileDropdown.append(`<option value="${profile.id}">${profile.profilename}</option>`);
        });
        ProfileDropdown.trigger('change'); // Update Select2 dropdown

      }
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
});