$(document).ready(function () {
  var newURL = window.location.href;
  var newURL = window.location.href;
  var module = "leads";
  var str = newURL.split(module);
  console.log("str" + str[0]);
  // var slicestr=newURL.substring(0,str);
  editusrl = str[0] + "leads/list";
  console.log("url" + editusrl);

  ///on state change get state

  $(document).on("change", "#stateid", function () {
    data = { state: $(this).val(), _csrf: $("#csrfToken").val() };
    // alert(data);
    getcity(this);
  });

  function getcity(thisobj) {
    // alert(thisobj.value);
    const state = thisobj.value;
    console.log(state);
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    // Reset dropdowns

    const cityDropdown = $("#city")
      .empty()
      .append('<option value="">Select</option>');

    if (state) {
      $.ajax({
        type: "POST",
        url: "getcity",
        data: { state: state, _csrf: csrfToken },
        dataType: "json",
        success: function (response) {
          console.log(response.statecode);
          if (response.status === "success") {
            response.categories.forEach((city) => {
              cityDropdown.append(
                `<option value="${city.id}">${city.name}</option>`
              );
            });
            cityDropdown.trigger("change"); // Update Select2 dropdown
            $("#statecode").val(response.statecode.state_code);
            $("#state").val(response.statecode.state_value);
          } else {
            alert(response.message);
          }
        },
        error: function (xhr) {``
          console.error(xhr);
          alert("Error occurred while fetching categories. Please try again.");
        },
      });
    }
  }

   var targetNode1 = document.getElementById("warehouse_manager1");
  var observer1 = new MutationObserver(function (mutationsList1) {
    for (var mutation1 of mutationsList1) {
      if (
        mutation1.type === "attributes" &&
        mutation1.attributeName === "value"
      ) {
        console.log("warehouse_manager1 value changed to:", targetNode1.value);

        getcontact(targetNode1.value);
      }
    }
  });

  // Configuration for the observer (observe attribute changes)
  var config1 = { attributes: true };
  observer1.observe(targetNode1, config1);

    function getcontact(warehouse_manager) {
    if (warehouse_manager) {
      data = {
        warehouse_manager: warehouse_manager,
        _csrf: $("#csrfToken").val(),
      };

      $.ajax({
        type: "GET",
        url: "getcontact",
        // async:false,
        data: data,
        success: function (response) {

          // Check if the data object exists and contains 'first_name'
          if (response && response.data) {
            $("#contact_number").val(response.data.mobile);

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
});

//////////////////// on the class end validation code zitendra /////////////////////////

////////////////////end validation code zitendra /////////////////////////

//code added by ptpatel on date 08-01-2026 to check duplicate product name

 /* //this code is commented by ptpatel because generalize duplicate functionality is used on date 02-03-2026
 $(document).on("blur", "#warehouse_name", function () {
    const urlParams = new URLSearchParams(window.location.search);
    const recordid = urlParams.get('Record');
    console.log("acc blur"+recordid);
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
        url: "iswarehouseduplicate",  
        type: "POST",
        data: {
            field: field,
            value: value,
            recordid : recordid,
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
    });

    function toggleSaveButton() {
      if ($(".form-group.error").length > 0 || $(".help-block:contains('required')").length > 0) {
          $(".savebutton").prop("disabled", true);
      } else {
          $(".savebutton").prop("disabled", false);
      }
    }*/
     //code added by ptpatel on date 08-01-2026 to check duplicate product name