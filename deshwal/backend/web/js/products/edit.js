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
    $('#product_nature, #category, #subcategory, #oem').select2();

    // Handle change event for product group (if it exists)
    $(document).on("change", "#product_group", function () {
        const productGroupId = $(this).val();
        const csrfToken = $('meta[name="csrf-token"]').attr("content");

        // Reset dropdowns
        const categoryDropdown = $("#category").empty().append('<option value="">Select Category</option>');
        const subcategoryDropdown = $("#subcategory").empty().append('<option value="">Select Sub Category</option>');
        const oemDropdown = $("#oem").empty().append('<option value="">Select OEM</option>');

        if (productGroupId) {
            $.ajax({
                type: "POST",
                url: "categories",
                data: { product_group_id: productGroupId, _csrf: csrfToken },
                dataType: "json",
                success: function (response) {
                    if (response.status === "success") {
                        response.categories.forEach((category) => {
                            categoryDropdown.append(`<option value="${category.id}">${category.name}</option>`);
                        });
                        categoryDropdown.trigger('change'); // Update Select2 dropdown
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
    });

    // Handle change event for category
    $(document).on("change", "#category", function () {
        const categoryId = $(this).val();
        const csrfToken = $('meta[name="csrf-token"]').attr("content");

        const subcategoryDropdown = $("#subcategory").empty().append('<option value="">Select Sub Category</option>');

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

    // Handle change event for subcategory
    $(document).on("change", "#subcategory", function () {
        const subCategoryId = $(this).val();
        const csrfToken = $('meta[name="csrf-token"]').attr("content");

        const oemDropdown = $("#oem").empty().append('<option value="">Select OEM</option>');

        if (subCategoryId) {
            $.ajax({
                type: "POST",
                url: "oems",
                data: { sub_category_id: subCategoryId, _csrf: csrfToken },
                dataType: "json",
                success: function (response) {
                    if (response.status === "success") {
                        response.oems.forEach((oem) => {
                            oemDropdown.append(`<option value="${oem.id}">${oem.name}</option>`);
                        });
                        oemDropdown.trigger('change'); // Update Select2 dropdown
                    } else {
                        alert(response.message);
                    }
                },
                error: function (xhr) {
                    console.error(xhr);
                    alert("Error occurred while fetching OEMs. Please try again.");
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
   $('#tax_preference').val("2").trigger("change");
  
 }
 ///////////get product description//////////////
 $(document).on("change", "#subcategory,#product_name,#make", function () {
    
    var make = $("#make option:selected").text();
    var product_name = $("#product_name").val();
    var subcategory =  $("#subcategory option:selected").text();
    // alert(product_name+' '+make+' '+ subcategory);
    if(make && make !='Select' && product_name && subcategory  && subcategory !='Select')
    var desc = subcategory+' - '+make+' '+product_name;
    else  var desc = '';
    if(desc)
    {        
        $("#product_description").val(desc);
        checkProductDescription();
    }
 });

  ///////////get Dimension Weight / Wt with Packaging//////////////
  $(document).on("change", "#length,#width,#height", function () {
    
    var length = parseFloat($("#length").val()) || 0;
    var width = parseFloat($("#width").val())||0;
    var height =  parseFloat($("#height").val())||0;
    // alert(length+' '+width+' '+ height);
    if(length && width && height)
    {
    var weight_with_packing_kg = (length*width*height)/5000;
    $("#weight_with_packing_kg").val(weight_with_packing_kg.toFixed(2));

    }
    else 
    {
        var weight_with_packing_kg = '';
        $("#weight_with_packing_kg").val('');
    }
 });

  ///////////get Dimension Weight / Wt with Packaging//////////////
  $(document).on("change", "#minimum_margin_percentage,#standard_sale_price", function () {
    
    var minimum_margin_percentage = parseFloat($("#minimum_margin_percentage").val()) || 0;
    var standard_sale_price = parseFloat($("#standard_sale_price").val())||0;
    // alert(length+' '+width+' '+ height);
    if(standard_sale_price && minimum_margin_percentage)
    {
        var minimum_margin_percentageval = (standard_sale_price*minimum_margin_percentage)/100;
        var cost_price = (standard_sale_price-minimum_margin_percentageval);
        $("#cost_price").val(cost_price.toFixed(2));
    }
    else 
    {
        $("#cost_price").val('');
    }
 });
//code added by ptpatel on date 08-01-2026 to check duplicate product name

//  $(document).on("blur", "#product_name", function () {
//this change is don as per v11 - Deshwal CR Point 17 Feb 2026 Point No 14 done by ptpatel on date 20-02-2026
function checkProductDescription() {
    const urlParams = new URLSearchParams(window.location.search);
    const recordid = urlParams.get('Record');
    var $input = $("#product_description");
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
        url: "isproductduplicate",  
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
}

    function toggleSaveButton() {
      if ($(".form-group.error").length > 0 || $(".help-block:contains('required')").length > 0) {
          $(".savebutton").prop("disabled", true);
      } else {
          $(".savebutton").prop("disabled", false);
      }
    }
// Also trigger on normal typing and blur
$(document).on("input blur", "#product_description", function () {
    // checkProductDescription();
    // chcekduplicate(this);
});

