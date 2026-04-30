$(document).ready(function(){
  var newURL = window.location.href;
  var newURL = window.location.href;
var module = "products";
var str=newURL.split(module);
console.log("str"+str[0]);
// var slicestr=newURL.substring(0,str);
editusrl = str[0]+"products/list";
  console.log("url"+editusrl);



});


  //////////////////// on the class end validation code zitendra /////////////////////////
  $(document).ready(function() {
    // Initialize Select2 for all dropdowns
    $('#category, #sub_category').select2();

 

    // Handle change event for category
    $(document).on("change", "#category", function () {
        const categoryId = $(this).val();
        const csrfToken = $('meta[name="csrf-token"]').attr("content");

        const subcategoryDropdown = $("#sub_category").empty().append('<option value="">Select Sub Category</option>');

        if (categoryId) {
            $.ajax({
                type: "POST",
                url: "subcategories",
                data: { category_id: categoryId, _csrf: csrfToken },
                dataType: "json",
                success: function (response) {
                    if (response.status === "success") {
                        response.subcategories.forEach((subcategory) => {
                            subcategoryDropdown.append(`<option value="${subcategory.id}">${subcategory.name}</option>`);
                        });
                        subcategoryDropdown.trigger('change'); // Update Select2 dropdown
                    } else {
                        alert(response.message);
                    }
                },
                error: function (xhr) {
                    console.error(xhr);
                    alert("Error occurred while fetching subcategories. Please try again.");
                },
            });
        }
    });

   
});
 ////////////set taxable default/////////
 const modeInput = document.getElementById("mode");
 // alert(modeInput);

 if (modeInput && modeInput.value === "Create") {
   // alert(modeInput);
   // initialize currency with INr
//    $('#tax_preference').val("2").trigger("change");
  
 }

   //code added by ptpatel on date 08-01-2026 to check duplicate product name

 /* //this code is commented by ptpatel because generalize duplicate functionality is used on date 02-03-2026
$(document).on("blur", "#service_name", function () {
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
        url: "isservicenameuplicate",  
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

