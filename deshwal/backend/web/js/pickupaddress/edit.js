
$(document).ready(function () {
   const sourceId = new URLSearchParams(window.location.search).get('sourceid');
      if(sourceId != "")
      {
        startLoading();
        $.ajax({
        type: "POST",
        url: "getaccount",
        data: { acc: sourceId, _csrf: $('meta[name="csrf-token"]').attr("content")},
        dataType: "json",
        success: function (response) {
          if (response.status === "success") {
            console.log(response);
            $("#account_name").val(response.data.acc_name);
            $("#account_name1").val(response.data.account_name);
            $("#account_name").prop("readonly",true);
            stopLoading();
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
      stopLoading();



  var targetNode1 = document.getElementById("spoc_name1");
  var observer1 = new MutationObserver(function (mutationsList1) {
    for (var mutation1 of mutationsList1) {
      if (
        mutation1.type === "attributes" &&
        mutation1.attributeName === "value"
      ) {
        console.log("spoc_name1 value changed to:", targetNode.value);

        getspocdetail(targetNode1.value);
      }
    }
  });

  // Configuration for the observer (observe attribute changes)
  var config1 = { attributes: true };
  observer1.observe(targetNode1, config1);

///////////get spoc detail///////
  function getspocdetail(contactid) {
    if (contactid) {
      data = {
        contactid: contactid,
        _csrf: $("#csrfToken").val(),
      };

      $.ajax({
        type: "POST",
        url: "getspocdetail",
        // async:false,
        data: data,
        success: function (response) {

          // Check if the data object exists and contains 'first_name'
          if (response && response.data) {
            $("#phone_no").val(response.data.mobile);
            $("#email").val(response.data.email);

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

  }
  

  /////////////create mutation for inspection address/////////////////
  // Create a MutationObserver to detect changes to the input vendor account
  var targetNode = document.getElementById("location_name1");
  var observer = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
      if (
        mutation.type === "attributes" &&
        mutation.attributeName === "value"
      ) {

        getaddressdetail(targetNode.value);
      }
    }
  });

  // Configuration for the observer (observe attribute changes)
  var config = { attributes: true };
  observer.observe(targetNode, config);
  function getaddressdetail(locationid) {
    locationid = $("#location_name1").val();
    //alert(locationid);

    if (locationid) {
      data = {
        locationid: locationid,
        _csrf: $("#csrfToken").val(),
      };

      $.ajax({
        type: "POST",
        url: "getaddressdetail",
        // async:false,
        data: data,
        success: function (response) {

          // Check if the data object exists and contains 'first_name'
          if (response && response.data) {
            $("#address").val(response.data.address);
            $("#state").val(response.data.state).trigger("change");
            $("#city").val(response.data.city).trigger("change");
            $("#pin_code").val(response.data.pincode);

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

  }
});