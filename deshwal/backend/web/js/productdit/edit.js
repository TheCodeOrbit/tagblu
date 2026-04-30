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
   
   /* 
//    this code is commented by ptpatel becuase as per productmaster devit sheet subcategory is dependant on master category on date 02-07-25
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
    */

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

    // Handle change event for master category //code added by ptpatel
    $(document).on("change", "#master_category", function () {
        const mastercategoryId = $(this).val();
        const csrfToken = $('meta[name="csrf-token"]').attr("content");

        const subcategoryDropdown = $("#sub_category").empty().append('<option value="">Select </option>');

        if (mastercategoryId) {
            $.ajax({
                type: "POST",
                url: "mastersubcategories",
                data: { master_category_id: mastercategoryId, _csrf: csrfToken },
                dataType: "json",
                success: function (response) {
                    if (response.status === "success") {
                        response.subcategories.forEach((subcategories) => {
                            subcategoryDropdown.append(`<option value="${subcategories.sub_category_id}">${subcategories.sub_category_value}</option>`);
                        });
                        subcategoryDropdown.trigger('change'); // Update Select2 dropdown
                    } else {
                        alert(response.message);
                    }
                },
                error: function (xhr) {
                    console.error(xhr);
                    alert("Error occurred while fetching Sub category. Please try again.");
                },
            });
        }
    });
    //code end added by ptpatel
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
//  this code is commented because for desciption this formula not needed by ptpatel on date 15-01-2025 to resolve v11- 219
 /*$(document).on("change", "#subcategory,#product_name,#make", function () {
    
    var make = $("#make option:selected").text();
    var product_name = $("#product_name").val();
    var subcategory =  $("#subcategory option:selected").text();
    // alert(product_name+' '+make+' '+ subcategory);
    if(make && make !='Select' && product_name && subcategory  && subcategory !='Select')
    var desc = subcategory+' - '+make+' '+product_name;
    else  var desc = '';
    $("#product_description").val(desc);
 });*/
 //  end this code is commented because for desciption this formula not needed by ptpatel on date 15-01-2025 to resolve v11- 219


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
//  start code added by ptpatel for CR-Point(Row 4) of DevIT  Length,Width,Heigth added and Product Dimensions in Weight calculated based on this and autofill from here on date 04-09-2025 
  /**
   * formula given by client (Length*Width*Height/4500).
   */
  function calculateDimensions() {
    console.log("df"+$('#product_length').val()+"-"+$('#product_width').val()+"-"+$('#product_height').val());
        var l = parseFloat($('#product_length').val()) || 0;
        var w = parseFloat($('#product_width').val()) || 0;
        var h = parseFloat($('#product_height').val()) || 0;

        var result = (l * w * h) / 4500;
        $('#product_dimensions_cm').val(result > 0 ? result.toFixed(2) : '');
    }

     $(document).on('input', '#product_length, #product_width, #product_height', calculateDimensions);
//  end code added by ptpatel for CR-Point(Row 4) of DevIT  Length,Width,Heigth added and Product Dimensions in Weight calculated based on this and autofill from here on date 04-09-2025 
     //code added by ptpatel on date 08-01-2026 to check duplicate product name

 /* //this code is commented by ptpatel because generalize duplicate functionality is used on date 02-03-2026
$(document).on("blur", "#product_name", function () {
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
    });*/

    function toggleSaveButton() {
      if ($(".form-group.error").length > 0 || $(".help-block:contains('required')").length > 0) {
          $(".savebutton").prop("disabled", true);
      } else {
          $(".savebutton").prop("disabled", false);
      }
    }
     //code added by ptpatel on date 08-01-2026 to check duplicate product name