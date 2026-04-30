   // Check if mode is 'Create'
   document.addEventListener("DOMContentLoaded", function () {
    
       const modeInput = document.getElementById("mode");
       if (modeInput && modeInput.value === "Create") {
         // Initialize currency with INR (or value 1)
         $('#currency').val("1").trigger('change');
         var data = { currency: 1, _csrf: $("#csrfToken").val() };
         $("#exchange_rate").val('');
       
         $.ajax({
           type: "POST",
           url: "getexchangerate",
           data: data,
           success: function (data) {
             $("#exchange_rate").val(data);
           },
           error: function () {
             alert("Error occurred. Please try again.");
           },
           dataType: "html",
         });
       }
       
    });
$(document).ready(function () {
    var newURL = window.location.href;
    var module = jQuery("#module").val();
    var str = newURL.indexOf(module);
    //code added by ptpatel on date 28-11-25 to overcome issue like v11- 133
    const modeInput = document.getElementById("mode");
    //this code added after discussion with deepika ma'am
    if(modeInput && modeInput.value === "Edit")
    {
        $("#opportuity_name,#account_name").attr("readonly",true);
        let opportunity_name1 = document.getElementById("opportuity_name1");
        let opportunity_name = document.getElementById("opportuity_name");

        if (opportunity_name1) {
          let wrapper1 = opportunity_name1.closest(".vendor-input-wrapper");
          if (wrapper1) {
            wrapper1.querySelectorAll(".icon-left, .icon-right").forEach(svg => svg.remove());
          }
        }
        if (opportunity_name) {
          opportunity_name.setAttribute("readonly", "readonly");
        }
        let account_name1 = document.getElementById("account_name1");
        let account_name = document.getElementById("account_name");

        if (account_name1) {
          let wrapper = account_name1.closest(".vendor-input-wrapper");
          if (wrapper) {
            wrapper.querySelectorAll(".icon-left, .icon-right").forEach(svg => svg.remove());
          }
        }
        if (account_name) {
          account_name.setAttribute("readonly", "readonly");
        }
    }
    //code added by ptpatel on date 28-11-25 to overcome issue like v11- 133
    var slicestr = newURL.substring(0, str);
    const urlParams = new URLSearchParams(window.location.search);
    const sourcemodule = urlParams.get("sourcemodule");
    const sourceid = urlParams.get("sourceid");
    if(sourcemodule && sourceid)
    {
        // console.log('opportuity_name1 value changed to:', targetNode.value);
        $("#account_name").val('');
        $("#account_name1").val('');
        $("#location").val('');
        $("#location1").val('');
        $("#address").val('');
        $("#city").val('');
        $("#state").val('');
        $("#pincode").val('');
        $("#sender_email_phone").val('');
        getvendor(); 
    }
    manageAssetsEdit()
    // start for sourcing deal/opportunity
    var targetNodeOpportunity = document.getElementById('opportuity_name1');
    if (targetNodeOpportunity) {
        var observerOpportunity = new MutationObserver(function (mutationsList) {
            for (var mutation of mutationsList) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                    $("#account_name").val("");
                    $("#account_name1").val("");
                    $("#spoc_name1").val("");
                    $("#spoc_name").val("");
                    $("#spoc_number").val("")
                    $("#spoc_email").val("")
                    $("#productTable84").find(".remove-row-btn").each(function () {
                        $(this).trigger("click");
                    })
                    if (targetNodeOpportunity.value !== '') {
                        getvendor();
                        assetcall = true;

                    }
                }
            }
        });
        var configOpportunity = { attributes: true };
        observerOpportunity.observe(targetNodeOpportunity, configOpportunity);
    }
    // end for sourcing deal / opportunity
    // start for client info -> spoc
    var targetNodeClientInfoSpoc = document.getElementById('spoc_name1');
    if (targetNodeClientInfoSpoc) {
        var observerClientInfroSpoc = new MutationObserver(function (mutationsList) {
            for (var mutation of mutationsList) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                    $("#spoc_number").val('');
                    $("#spoc_email").val('');
                    if (targetNodeClientInfoSpoc.value !== '')
                        getClientInfoSpoc();
                }
            }
        });
        var configClientInfoSpoc = { attributes: true };
        observerClientInfroSpoc.observe(targetNodeClientInfoSpoc, configClientInfoSpoc);
    }
    //end for client info -> spoc

    // start for pickup location
    var targetNodePickupLocation = document.getElementById('pickup_location1');
    if (targetNodePickupLocation) {
        var observerPickupLocation = new MutationObserver(function (mutationsList) {
            for (var mutation of mutationsList) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                    $("#pickup_address").val('');
                    $("#pickup_city").val('');
                    $("#pickup_state").val('');
                    $("#pickup_pin_code").val('');
                    if (targetNodePickupLocation.value !== '')
                        getPickupLocation();
                }
            }
        });
        var configPickupLocation = { attributes: true };
        observerPickupLocation.observe(targetNodePickupLocation, configPickupLocation);
    }
    //end for pickup location

    // start for delivery location
    var targetNodeDeliveryLocation = document.getElementById('delivery_location1');
    if (targetNodeDeliveryLocation) {
        var observerDeliveryLocation = new MutationObserver(function (mutationsList) {
            for (var mutation of mutationsList) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                    $("#delivery_address").val('');
                    $("#delivery_city").val('');
                    $("#delivery_state").val('');
                    $("#delivery_pin_code").val('');
                    if (targetNodeDeliveryLocation.value !== '')
                        getDeliveryLocation();
                }
            }
        });
        var configDeliveryLocation = { attributes: true };
        observerDeliveryLocation.observe(targetNodeDeliveryLocation, configDeliveryLocation);
    }
    //end for delivery location

    // start for FE
    var targetNodeFE = document.getElementById('fe_name1');
    if (targetNodeFE) {
        var observerFE = new MutationObserver(function (mutationsList) {
            for (var mutation of mutationsList) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                    $("#fe_number").val('');
                    if (targetNodeFE.value !== '') {
                        getFeDetails();
                    } else {
                        console.log("fe user is removed")
                        $('input[name="pickup[pickup_schedule]"]:checked').prop('checked', false)
                    }
                }
            }
        });
        var configFE = { attributes: true };
        observerFE.observe(targetNodeFE, configFE);
    }
    //end for FE

    // start for Logistics Exe
    var targetNodeLogisticExe = document.getElementById('logistic_user1');
    if (targetNodeLogisticExe) {
        var observerLogisticExe = new MutationObserver(function (mutationsList) {
            for (var mutation of mutationsList) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                    $("#logistic_user_number").val('');
                    if (targetNodeLogisticExe.value !== '') {
                        getLogistcExeDetails();
                    }
                }
            }
        });
        var configLogisticExe = { attributes: true };
        observerLogisticExe.observe(targetNodeLogisticExe, configLogisticExe);
    }
    //end for Logistics Exe

    async function getvendor() {
        data = { opportuity_name1: $("#opportuity_name1").val(), _csrf: $('#csrfToken').val() };
        if($("#opportuity_name1").length > 0 )//this if cond added by ptpatel to resolve v11-140 issue
        {           
            try {
                $("#loading-overlay").css('display', 'grid');
                let response = await $.ajax({
                    type: 'POST',
                    url: "getvendor",
                    data: data,
                    // success: function (response) {
                    //     $("#loading-overlay").css('display', 'none');
                    //     console.log(response); // Log the entire response to check its structure
                        
                    // },
                    // error: function (data) { // if error occured
                    //     $("#loading-overlay").css('display', 'none');
                    //     alert('Error occured.please try again');
                    // },
                    dataType: 'json'
                });
                if (response && response.data) {
                    let account = response.data.account || null;
                    let related = response.data.related || null;
                    // Check if the data object exists and contains 'first_name'
                    if (account) {
                        $("#account_name").val(account.acc_name);
                        $("#account_name1").val(account.vendor_account_name);
                        // $(".remove-row-btn").each(function () {
                        //     $(this).trigger("click");
                        // });
                        //below modeInput if condition added by ptpatel on date 28-11-2025 to overcome issue like v11- 133
                        if ((modeInput && modeInput.value === "Create" && sourcemodule && sourceid) || (modeInput && modeInput.value === "Create")) {
                            // console.log("add asset dynamic row call");
                            if (related && Array.isArray(related)) {
                                $('#productTable84 tbody').html('');//this line added by ptpatel on date 28-11-2025 to update table row when user change sourcing deal with v11- 133 issue
                                for (const item of related) {
                                    await addAssetsDynamicRows(item);
                                }
                            }
                        }
                        $("#loading-overlay").css('display', 'none');
                    } else {
                        $("#loading-overlay").css('display', 'none');
                        console.log("Invalid response format or missing data");
                    }
                }
            }catch (error) {
                $("#loading-overlay").css('display', 'none');
                alert('Error occurred. Please try again.');
            }
        }
    }
    async function addAssetsDynamicRows(item) {
        return new Promise((resolve, reject) => {
            let blockid = 84;
            let mainmodule = "pickup";
            let totalRows = $('#productTable' + blockid + ' tr').length;
            let geturl = getAbsoluteUrl();
            let url = geturl + mainmodule + "/getproductlist?blockid=" + blockid + "&cnt_rows=" + totalRows;
            // Get the table body
            const tableBody = document.querySelector('#productTable' + blockid + ' tbody');
            $.ajax({
                type: "GET",
                url: url,
                dataType: "html",
                success: function (data) {
                    
                    let tbody = $('#productTable' + blockid + ' tbody');
                    tbody.append(data); // Always append the new row
                    // Set the values for the last added row
                    let lastRow = tbody.find('tr:last');
                    lastRow.find('[name*="[porduct_name]"]').val(item.productid || ""); // hidden field
                    lastRow.find('.porduct_name').val(item.product_name || ""); // visible field
                    
                    lastRow.find('.category').val(item.category || "");
                    lastRow.find('.sub_category').val(item.subcategory || "");
                    lastRow.find('.model_no').val(item.model_no || "");
                    lastRow.find('.make').val(item.make || "");
                    lastRow.find('[name*="[all_accessories]"]').val(item.all_accessories || "").trigger("change");
                    lastRow.find('.hsn_code').val(item.hsn_code || "");
                    lastRow.find('.quoted_price_gst_include').val(item.quoted_price_inclusive_gst || "");
                    lastRow.find('.quoted_price_gst_exclude').val(item.quoted_price_gst_exclude || "");
                    lastRow.find('.quantity_quoted').val(item.quantity_required || "");
                    lastRow.find('.uom').val(item.uom || "");
                    lastRow.find('.cgst').val(item.cgst || "");
                    lastRow.find('.sgst').val(item.sgst || "");
                    lastRow.find('.igst').val(item.igst || "");
                    lastRow.find('.cgst_amount').val(item.cgst_amount || "");
                    lastRow.find('.sgst_amount').val(item.sgst_amount || "");
                    lastRow.find('.igst_amount').val(item.igst_amount || "");
                    lastRow.find('.total_quoted_price_gst_include').val(item.total_quoted_price_inclusive_gst || "");
                    lastRow.find('.total_quoted_price_gst_exclude').val(item.total_quoted_price_exclusive_gst || "");
                    lastRow.find(".remove-row-btn").remove()
                    // lastRow.find('.purchase_order_quantity').trigger("change");
                    resolve();
                },
                error: function (data) {
                    alert("Error occurred. Please try again.");
                    reject();
                }
            });
        });
    }
    function getvendoraddress() {

        data = { account_name1: $("#location1").val(), _csrf: $('#csrfToken').val() };

        $.ajax({
            type: 'POST',
            url: "getvendoraddress",
            // async:false,
            data: data,
            success: function (response) {
                console.log(response); // Log the entire response to check its structure

                // Check if the data object exists and contains 'first_name'
                if (response && response.data) {
                    // address,city_name,state,pincode
                    $("#address").val(response.data.address);
                    $("#city").val(response.data.city_name);
                    $("#state").val(response.data.state);
                    $("#pincode").val(response.data.pincode);
                    var address = response.data.address + "\n" + response.data.city_name + "," + response.data.state + "," + response.data.pincode;
                    $("#sender_email_phone").val(address);
                } else {
                    console.log("Invalid response format or missing data");
                }

            },
            error: function (data) { // if error occured

                alert('Error occured.please try again');
            },
            dataType: 'json'
        });

    }
    function getClientInfoSpoc() {
        data = { spoc: $("#spoc_name1").val(), _csrf: $('#csrfToken').val() };
        $("#loading-overlay").css('display', 'grid');
        $.ajax({
            type: 'POST',
            url: "getspoc",
            // async:false,
            data: data,
            success: function (response) {
                $("#loading-overlay").css('display', 'none');
                console.log(response); // Log the entire response to check its structure
                if (response && response.data) {
                    $("#spoc_email").val(response.data.spoc_email);
                    $("#spoc_number").val(response.data.spoc_mobile);
                } else {
                    console.log("Invalid response format or missing data");
                }
            },
            error: function (data) { // if error occured
                alert('Error occured.please try again');
                $("#loading-overlay").css('display', 'none');
            },
            dataType: 'json'
        });

    }
    function getPickupLocation() {
        data = { account_name1: $("#pickup_location1").val(), _csrf: $('#csrfToken').val() };
        $("#loading-overlay").css('display', 'grid');
        $.ajax({
            type: 'POST',
            url: "getvendoraddress",
            data: data,
            success: function (response) {
                $("#loading-overlay").css('display', 'none');
                console.log(response);
                if (response && response.data) {
                    $("#pickup_address").val(response.data.address);
                    $("#pickup_city").val(response.data.city_name);
                    $("#pickup_state").val(response.data.state);
                    $("#pickup_pin_code").val(response.data.pincode);
                } else {
                    console.log("Invalid response format or missing data");
                }
            },
            error: function (data) { // if error occured
                $("#loading-overlay").css('display', 'none');
                alert('Error occured.please try again');
            },
            dataType: 'json'
        });
    }
    function getDeliveryLocation() {
        data = { warehouse: $("#delivery_location1").val(), _csrf: $('#csrfToken').val() };
        $("#loading-overlay").css('display', 'grid');
        $.ajax({
            type: 'POST',
            url: "getwarehouseaddress",
            data: data,
            success: function (response) {
                $("#loading-overlay").css('display', 'none');
                console.log(response);
                if (response && response.data) {
                    $("#delivery_address").val(response.data.address);
                    $("#delivery_city").val(response.data.city_name);
                    $("#delivery_state").val(response.data.state);
                    $("#delivery_pin_code").val(response.data.pincode);
                } else {
                    console.log("Invalid response format or missing data");
                }
            },
            error: function (data) { // if error occured
                alert('Error occured.please try again');
                $("#loading-overlay").css('display', 'none');
            },
            dataType: 'json'
        });
    }
    function getFeDetails() {
        data = { user: $("#fe_name1").val(), _csrf: $('#csrfToken').val() };
        $("#loading-overlay").css('display', 'grid');
        $.ajax({
            type: 'POST',
            url: "getuserdetails",
            data: data,
            success: function (response) {
                $("#loading-overlay").css('display', 'none');
                console.log(response);
                if (response && response.data) {
                    $("#fe_number").val(response.data.mobile);
                } else {
                    console.log("Invalid response format or missing data");
                }
            },
            error: function (data) { // if error occured
                alert('Error occured.please try again');
                $("#loading-overlay").css('display', 'none');
            },
            dataType: 'json'
        });
    }
    function getLogistcExeDetails() {
        data = { user: $("#logistic_user1").val(), _csrf: $('#csrfToken').val() };
        $("#loading-overlay").css('display', 'grid');
        $.ajax({
            type: 'POST',
            url: "getuserdetails",
            data: data,
            success: function (response) {
                $("#loading-overlay").css('display', 'none');
                console.log(response);
                if (response && response.data) {
                    $("#logistic_user_number").val(response.data.mobile);
                } else {
                    console.log("Invalid response format or missing data");
                }
            },
            error: function (data) { // if error occured
                alert('Error occured.please try again');
                $("#loading-overlay").css('display', 'none');
            },
            dataType: 'json'
        });
    }
    // get exchangerate
    // Initialize Select2 for all dropdowns
    if ($('#currency').length) {
        $('#currency').select2();
    }
    ////////////////////initialize currency with INR /////////////////////////

   // Listen for the change event on select2
   $('#currency').on('change', function (e) {
     var selectedValue = e.target.value;  // Get the selected value
    //  alert("Selected value: " + selectedValue);
     
     var data = { currency: selectedValue, _csrf: $("#csrfToken").val() };
     $("#exchange_rate").val('');
   
     $.ajax({
       type: "POST",
       url: "getexchangerate",
       data: data,
       success: function (data) {
         $("#exchange_rate").val(data);
       },
       error: function () {
         alert("Error occurred. Please try again.");
       },
       dataType: "html",
     });
   });
   
    //end exchange rate
    //get deshwal spoc mobile number
    $(document).on("change", "#deshwal_spoc_name", function () {
        // $("#deshwal_spoc_mobile").val('');
        data = { deshwal_spoc_name: $(this).val(), _csrf: $('#csrfToken').val() };

        $.ajax({
            type: 'POST',
            url: "getusernumber",
            // async:false,
            data: data,
            success: function (response) {
                //location.reload();
                $("#deshwal_spoc_mobile").val(response.data);

            },
            error: function (data) { // if error occured

                alert('Error occured.please try again');
            },
            dataType: 'json'
        });

    });
    //show hide based on pick up done by
    var pickupdoneby = $("#pickup_doneby").val();
    showspoc(pickupdoneby)
    $(document).on("change", "#pickup_doneby", function () {
        var pickupdoneby = $(this).val();
        showspoc(pickupdoneby);

    });
    function showspoc(pickupdoneby) {
        $(".section-spoc_person_name").addClass("tr-hidden");
        $(".section-spoc_person_mobile").addClass("tr-hidden");
        $(".section-deshwal_spoc_name").addClass("tr-hidden");
        $(".section-deshwal_spoc_mobile").addClass("tr-hidden");
        //alert(pickupdoneby);
        if (pickupdoneby == 1) {//deshwal show
            $(".section-deshwal_spoc_name").removeClass("tr-hidden");
            $(".section-deshwal_spoc_mobile").removeClass("tr-hidden");
            $(".section-spoc_person_name").addClass("tr-hidden");
            $(".section-spoc_person_mobile").addClass("tr-hidden");
            $("#spoc_person_name").val("");
            $("#spoc_person_mobile").val("");
        }
        else if (pickupdoneby == 2) {
            //vendor show
            $(".section-spoc_person_name").removeClass("tr-hidden");
            $(".section-spoc_person_mobile").removeClass("tr-hidden");
            $(".section-deshwal_spoc_name").addClass("tr-hidden");
            $(".section-deshwal_spoc_mobile").addClass("tr-hidden");
            $("#deshwal_spoc_mobile").val("");
            $('#deshwal_spoc_name').val(null).trigger('change');

        }
    }
    
    function showpickup(v) {
        if (v) {
            //show pickup section
            $(".blocktitle86").removeClass("tr-hidden");
            $(".blockrow86").removeClass("tr-hidden");
        }
        else {
            // hide pickupsection
            $(".blocktitle86").addClass("tr-hidden");
            $(".blockrow86").addClass("tr-hidden");
            $("#spoc_person_name").val("");
            $("#spoc_person_mobile").val("");
            $("#deshwal_spoc_mobile").val("");
            $('#deshwal_spoc_name').val(null).trigger('change');
            $('#pickup_doneby').val(null).trigger('change');
            $("#pickup_date").val('');
            $('#form_6').val(null).trigger('change');
            $("#pickup_tentative_date").val('');
            $("#vehicle_number").val('');
            $("#pickup_complete_date").val('');
            showspoc('');

        }
    }

    function showsaleonsite(v) {
        if (v) {
            //show pickup section
            $(".blocktitle87").removeClass("tr-hidden");
            $(".blockrow87").removeClass("tr-hidden");
        }
        else {
            // hide pickupsection
            $(".blocktitle87").addClass("tr-hidden");
            $(".blockrow87").addClass("tr-hidden");
            $("#purchase_value").val("");
            $("#purchase_value").val("");
            $("#credit_days").val("");
            $('#vendor_type').val(null).trigger('change');
            $('#sale_site_status').val(null).trigger('change');
            $("#sale_pickup_tentative_date").val('');
            $('#payment_type').val(null).trigger('change');
            $("#sale_site_remarks").val('');
            $("#profit_loss").val('');
            $("#sale_site_pickup_date").val('');
            showspoc('');

        }
    }

    /*
    // produt changes observer
    // Function to observe input value changes
    function observeInputChanges(inputElement) {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                    console.log(`Value changed in ${inputElement.id}: ${inputElement.value}`);
                    const nearestTr = inputElement.closest('tr');
                    if (nearestTr) {
                        trid = nearestTr.id;
                        console.log('Nearest <tr> ID:', nearestTr.id);
                        getProductinfo(trid, `${inputElement.value}`);

                    } else {
                        nearestTr.id = '';
                        console.log('No <tr> ancestor found');
                    }
                }
            });
        });

        observer.observe(inputElement, {
            attributes: true, // Observe attribute changes
            attributeFilter: ['value'] // Only watch 'value' attribute
        });

        console.log(`Observer attached to input: ${inputElement.id}`);
    }

    // Function to observe all matching inputs
    function observeMatchingInputs() {
        // Match inputs with ID pattern 'porduct_name_*1'
        const inputs = document.querySelectorAll('input[id^="porduct_name_"][id$="1"]');
        inputs.forEach((input) => observeInputChanges(input));
        console.log(`Observers attached to ${inputs.length} inputs.`);
    }

    // Function to monitor dynamically added inputs
    function monitorDynamicInputs() {
        const container = document.body; // Observe the entire document

        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                    mutation.addedNodes.forEach((node) => {
                        if (node.nodeType === 1) {
                            // Check for new matching inputs
                            const newInputs = node.querySelectorAll('input[id^="porduct_name_"][id$="1"]');
                            // console.log("deepika");
                            newInputs.forEach((input) => observeInputChanges(input));
                        }
                    });
                }
            });
        });

        observer.observe(container, {
            childList: true, // Detect added elements
            subtree: true, // Include all child elements
        });

        console.log('Monitoring dynamic inputs for pattern: porduct_name_*1');
    }

    // Initialize observers for existing and dynamic inputs
    observeMatchingInputs();
    monitorDynamicInputs();
    */

    // get productinfo
    function getProductinfo(trid, productid) {
        //alert(productid);
        data = { productid: productid, _csrf: $('#csrfToken').val() };

        $.ajax({
            type: 'POST',
            url: slicestr + "productdetail/getproductinfo",
            // async:false,
            data: data,
            success: function (response) {
                console.log(response); // Log the entire response to check its structure

                // Check if the data object exists and contains 'first_name'
                if (response && response.data) {

                    $("#hsn_code_" + trid).val(response.data.hsn_code);
                    $("#uom_" + trid).val(response.data.uom_value);

                } else {
                    console.log("Invalid response format or missing data");
                }

            },
            error: function (data) { // if error occured

                alert('Error occured.please try again');
            },
            dataType: 'json'
        });

    }
    function manageProvisionToTimingExtend() {
        return true;//as per point no 30
        var extend_time_provision = $("#extend_time_provision").val();
        if (extend_time_provision == 1) {
            $(".section-extension_provision").show()
            $(".section-entry_formalities_person").hide();
            $("#entry_formalities_person").val(null).trigger("change");
        }else if (extend_time_provision == 2) {
            $(".section-extension_provision").hide()
            $("#extension_provision").val(null).trigger("change");
            $(".section-entry_formalities_person").show();
        } else {
            $(".section-extension_provision").hide()
            $("#extension_provision").val(null).trigger("change");
            $(".section-entry_formalities_person").hide();
            $("#entry_formalities_person").val(null).trigger("change");
        }
    }
    function manageMaterialLocation() {
        var material_location_floor = $("#material_location_floor").val();
        if (material_location_floor == 1) {
            $(".section-material_floor").show();
            $(".section-floor_num_material_count").hide();
            $("#floor_num_material_count").val("")
        } else if (material_location_floor == 2) {
            $(".section-material_floor").hide();
            $("#material_floor").val("")
            $(".section-floor_num_material_count").show();
        } else { 
            $(".section-material_floor").hide();
            $("#material_floor").val("")
            $(".section-floor_num_material_count").hide();
            $("#floor_num_material_count").val("")
        }
    }
    function manageServiceLiftCondition() {
        var service_lift = $("#service_lift").val();
        if (service_lift == 1) {
            $(".section-lift_timing").show()
            $(".section-stairs_space").hide()
            $("#stairs_space").val(null).trigger("change")
        }else if (service_lift == 2) {
            $(".section-lift_timing").hide()
            $("#lift_timing").val();
            $(".section-stairs_space").show()
        } else {
            $(".section-lift_timing").hide()
            $("#lift_timing").val();
            $(".section-stairs_space").hide()
            $("#stairs_space").val(null).trigger("change")
        }
    }
    function manageStairsSpaceCondition() {
        var stairs_space = $("#stairs_space").val();
        if (stairs_space == 2) {
            $(".section-material_move").show()
        } else if (stairs_space == 1) {
            $(".section-material_move").hide()
            $("#material_move").val("")
        } else {
            $(".section-material_move").hide()
            $("#material_move").val("")
        }
    }
    function manageSegregation() {
        var segregation = $("#segregation").val();
        if (segregation == 1) {
            $(".section-space_for_segregation").show()
        } else if (segregation == 2) {
            $(".section-space_for_segregation").hide()
            $("#space_for_segregation").val(null).trigger("change")
        } else {
            $(".section-space_for_segregation").hide()
            $("#space_for_segregation").val(null).trigger("change")
        }
    }
    function calculateRowTotalPkgMaterial(row) {
        var cost = parseFloat(row.find('.price').val()) || 0;
        var qty = parseFloat(row.find('.qty').val()) || 0;
        var totalPrice = cost * qty;
        row.find('.total').val(totalPrice.toFixed(2));
    }
    function hidePickupStatus() {
        $(".section-pickup_status").hide();
    }
    function checkFEUser() {
        var selectedValue = $('input[name="pickup[pickup_inspection_require]"]:checked').val();
        if (selectedValue) {
            var fe_name1 = $("#fe_name1").val();
            if (!fe_name1) {
                $("#pickup_inspection_require").parents(".form-group").find(".help-block").text("Please first select FE name")
                $('input[name="pickup[pickup_inspection_require]"]:checked').prop('checked', false);
                return false;
            } else {
                $("#pickup_inspection_require").parents(".form-group").find(".help-block").text("");
            }
        }
    }
    function checkCxUserData() {
        var selectedValue = $('input[name="pickup[pickup_submitted_for_logistics]"]:checked').val();
        if (selectedValue) {
            var pickup_location1 = $("#pickup_location1").val();
            if (!pickup_location1) {
                //$("#pickup_submitted_for_logistics").parents(".form-group").find(".help-block").text("Please first select Pickup Location")
                $("#pickup_location").parents(".form-group").find(".help-block").text("Pickup Location is required for this action")
                $('input[name="pickup[pickup_submitted_for_logistics]"]:checked').prop('checked', false);
                $("#pickup_location").focus();
                return false;
            } else {
                $("#pickup_schedule").parents(".form-group").find(".help-block").text("");
                $("#pickup_location").parents(".form-group").find(".help-block").text("")
            }
            var delivery_location1 = $("#delivery_location1").val();
            if (!delivery_location1) {
                //$("#pickup_submitted_for_logistics").parents(".form-group").find(".help-block").text("Please first select Delivery Location")
                $("#delivery_location").parents(".form-group").find(".help-block").text("Delivery Location is required for this action")
                $('input[name="pickup[pickup_submitted_for_logistics]"]:checked').prop('checked', false);
                $("#delivery_location").focus();
                return false;
            } else {
                $("#pickup_schedule").parents(".form-group").find(".help-block").text("");
                $("#delivery_location").parents(".form-group").find(".help-block").text("")
            }
            var preferred_pickup_date = $("#preferred_pickup_date").val();
            if (!preferred_pickup_date) {
                $("#preferred_pickup_date").parents(".form-group").find(".help-block").text("Preferred Pickup Date is required")
                $('input[name="pickup[pickup_submitted_for_logistics]"]:checked').prop('checked', false);
                $("#preferred_pickup_date").focus();
                return false;
            } else {
                $("#preferred_pickup_date").parents(".form-group").find(".help-block").text("");
            }
            var actual_pickup_date = $("#actual_pickup_date").val();
            if (!actual_pickup_date) {
                $("#actual_pickup_date").parents(".form-group").find(".help-block").text("Actual Pickup Date is required")
                $('input[name="pickup[pickup_submitted_for_logistics]"]:checked').prop('checked', false);
                $("#actual_pickup_date").focus();
                return false;
            } else {
                $("#actual_pickup_date").parents(".form-group").find(".help-block").text("");
            }
            
            var doc_received = $("#doc_received").val();
            if (!doc_received) {
                $("#doc_received").parents(".form-group").find(".help-block").text("This field is required")
                $('input[name="pickup[pickup_submitted_for_logistics]"]:checked').prop('checked', false);
                $("#doc_received").focus();
                return false;
            } else {
                $("#doc_received").parents(".form-group").find(".help-block").text("");
            }

            var logistic_user1 = $("#logistic_user1").val();
            if (!logistic_user1) {
                //$("#pickup_submitted_for_logistics").parents(".form-group").find(".help-block").text("Please first select Delivery Location")
                $("#logistic_user").parents(".form-group").find(".help-block").text("Logistic User is required for this action")
                $('input[name="pickup[pickup_submitted_for_logistics]"]:checked').prop('checked', false);
                $("#logistic_user").focus();
                return false;
            } else {
                $("#pickup_schedule").parents(".form-group").find(".help-block").text("");
                $("#logistic_user").parents(".form-group").find(".help-block").text("")
            }
        }
    }
    function checkPackingData() {
        var selectedValue = $('input[name="pickup[packing_material_approval_requested]"]:checked').val();
        if (selectedValue) {
            var field_check = true;
            //$(".blockrow222").find("input,select").each(function () {
            $(".blockrow222").find(".form-field-cst:visible").find("input,select").each(function () {
                var field_value = $(this).val();
                if (field_value == '') {
                    $(this).parents(".form-group").find(".help-block").text("It is required for this action")
                    
                    field_check = false;
                }
            })
            if (!field_check) {
                $('input[name="pickup[packing_material_approval_requested]"]:checked').prop('checked', false);
                return false;
            } else {
                $(".blockrow222").find("input,select").each(function () {
                    $(this).parents(".form-group").find(".help-block").text("");
                })
            }
            if ($("#productTable223 tbody tr").length > 0) {
                $("#packing_material_approval_requested").parents(".form-group").find(".help-block").text("")
                $("#productTable223 tbody tr").find("input,select").each(function () {
                    var field_value = $(this).val();
                    if (field_value == '') {
                        $(this).focus()
                        $(this).parents(".form-group").find(".help-block").text("It is required for this action")
                        $('input[name="pickup[packing_material_approval_requested]"]:checked').prop('checked', false);
                        return false;
                    }
                })
            } else {
                $("#packing_material_approval_requested").parents(".form-group").find(".help-block").text("Please fill 'PACKING MATERIAL' for this action")
                $('input[name="pickup[packing_material_approval_requested]"]:checked').prop('checked', false);
                return false
            }
        }
    }
    function checkScheduleDate() {
        var selectedValue = $('input[name="pickup[pickup_schedule]"]:checked').val();
        if (selectedValue) {
            if ($("#productTable224 tbody tr").length > 0) {
                $("#pickup_schedule").parents(".form-group").find(".help-block").text("")
                $("#productTable224 tbody tr").find(".schedule_pickup_date").each(function () {
                    var field_value = $(this).val();
                    if (field_value == '') {
                        $(this).focus()
                        $(this).parents(".form-group").find(".help-block").text("It is required for this action")
                        $('input[name="pickup[pickup_schedule]"]:checked').prop('checked', false);
                        return false;
                    }
                })
            } else {
                $("#pickup_schedule").parents(".form-group").find(".help-block").text("Please fill 'Vehicle Planning (Schedule Date)' for this action")
                $('input[name="pickup[pickup_schedule]"]:checked').prop('checked', false);
                return false
            }
        }
    }
    function checkVehiclePlanning() {
        var selectedValue = $('input[name="pickup[vehicle_planning_approval_requested]"]:checked').val();
        if (selectedValue) {
            if ($("#productTable224 tbody tr").length > 0) {
                $("#vehicle_planning_approval_requested").parents(".form-group").find(".help-block").text("")
                $("#productTable224 tbody tr").find("input:not([type='file']), select").each(function () {
                    var field_name = $(this).attr("name") || "";
                    if (field_name.includes("schedule_pickup_date") || field_name.includes("pickup_doc")) {
                        return; // continue to next iteration
                    }
                    var field_value = $(this).val();
                    if (field_value == '') {
                        $(this).focus()
                        $(this).parents(".form-group").find(".help-block").text("It is required for this action")
                        $('input[name="pickup[vehicle_planning_approval_requested]"]:checked').prop('checked', false);
                        return false;
                    }
                })
            } else {
                $("#vehicle_planning_approval_requested").parents(".form-group").find(".help-block").text("Please fill 'Vehicle Planning' for this action")
                $('input[name="pickup[vehicle_planning_approval_requested]"]:checked').prop('checked', false);
                return false
            }
        }
    }
    function manageStageChecks() {
        $(".section-pickup_submitted_for_logistics,.section-pickup_schedule,.section-pickup_inspection_require,.section-packing_material_approval_requested").hide()
        $(".section-pickup_in_process,.section-vehicle_planning_approval_requested, .section-pickup_completed").hide()
        $(".section-pickup_inspection_require,.section-pickup_inspection_require").hide()
        var current_status = $("#pickup_status").val()
        // alert(current_status)
        if (current_status) {
            if (current_status == 2) {
                //$(".section-pickup_submitted_for_logistics").show()
            }else if (current_status == 3) {
                $(".section-pickup_inspection_require").show()
            } else if (current_status == 4) {
                $(".section-packing_material_approval_requested").show()
            }else if (current_status == 10) {
                
            }else if (current_status == 11) {
                
            } else if (current_status == 12) {
                //$(".section-pickup_schedule").show()
            } else if (current_status == 14) {
                $(".section-packing_material_approval_requested").show()
            } else if (current_status == 15) {
                $(".section-vehicle_planning_approval_requested").show()
            } else if (current_status == 18) {
                $(".section-vehicle_planning_approval_requested").show()
            }
        }
    }
    function showErrorMessage(message) {
        var alertDiv = document.createElement('div');
        alertDiv.className = 'custom-alert alert alert-danger alert-dismissible fade show';
        alertDiv.role = 'alert';
        alertDiv.innerHTML = `
            <strong>Error! </strong> ${message}.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        $('#myDIV').append(alertDiv);
        setTimeout(() => {
            $(alertDiv).fadeOut(500, function () {
                $(this).remove();
            });
        }, 30000);
    }
    function manageAssetsEdit() {
        $(".blocktitle84").parents(".titlerow").find(".add-more-records").remove()
        $(".row2739,.row2740,.row2741,.row2742").find(".add-more-records").remove()
        $("#productTable2739,#productTable2740,#productTable2741,#productTable2742").find(".remove-row-btn")
    }
    function manageLocalUnionCharges() {
        var local_union = $("#local_union").val();
        if (local_union == 1) {
            $(".section-local_union_charges").show()
        } else {
            $(".section-local_union_charges").hide()
            $("#local_union_charges").val("")
        }
    }
    function manageLocalVehicleRequired() {
        var local_vehicle_require = $("#local_vehicle_require").val();
        if (local_vehicle_require == 1) {
            $(".section-local_vehicle_size").show()
            $(".section-local_vehicle_charges").show()
            $(".section-num_local_vehicle").show()
        } else {
            $(".section-local_vehicle_size").hide()
            $("#local_vehicle_size").val(null).trigger("change");
            $(".section-local_vehicle_charges").hide()
            $("#local_vehicle_charges").val("");
            $(".section-num_local_vehicle").hide()
            $("#num_local_vehicle").val("");
        }
    }
    function manageOverTime() {
        var over_time = $("#over_time").val();
        if (over_time == 1) {
            $(".section-over_time_charges").show()
        } else {
            $(".section-over_time_charges").hide()
            $("#over_time_charges").val("");
        }
    }
    $(document).on("change", "#local_union", function () {
        manageLocalUnionCharges()
    });
    $(document).on("change", "#local_vehicle_require", function () {
        manageLocalVehicleRequired()
    });
    $(document).on("change", "#over_time", function () {
        manageOverTime()
    });
    $(document).on("change", "#labour_rate,#labour_count", function () {
        var labour_rate = parseFloat($("#labour_rate").val()) || 0;
        var labour_count = parseFloat($("#labour_count").val())||0;
        
        var total_labour_cost = labour_rate * labour_count;
        $("#total_labour_count").val(total_labour_cost);
    });
    $(document).on("change", "#extend_time_provision", function () {
        manageProvisionToTimingExtend()
    });
    $(document).on("change", "#material_location_floor", function () {
        manageMaterialLocation()
    });
    $(document).on("change", "#service_lift", function () {
        manageServiceLiftCondition()
    });
    $(document).on("change", "#stairs_space", function () {
        manageStairsSpaceCondition()
    });
    $(document).on("change", "#segregation", function () {
        manageSegregation()
    });
    $(document).on("change", ".qty,.price", function () {
        var currentRow = $(this).closest('tr');
        calculateRowTotalPkgMaterial(currentRow);
    });
    $('#pickup_inspection_require').change(function(){
        checkFEUser();
    });
    $('#pickup_submitted_for_logistics').change(function(){
        checkCxUserData();
    });
    $(document).on("change", ".picked_qty", function () {
        var picked_qty = parseFloat($(this).val())||0;
        var quantity_quoted = $(this).parents(".product-row").find(".quantity_quoted").val();
        if (quantity_quoted) {
            quantity_quoted = parseFloat(quantity_quoted)||0;
        }
        var diff =  picked_qty - quantity_quoted;
        $(this).parents(".product-row").find(".difference").val(diff);
    });
    $('#packing_material_approval_requested').change(function(){
        checkPackingData();
    });
    $('#pickup_schedule').change(function(){
        checkScheduleDate();
    });
    $('#vehicle_planning_approval_requested').change(function(){
        checkVehiclePlanning();
    });
    $(document).on("click", "#approvesubmit", function () {
        $(".approve-error-msg").text("");
        let data = {
            Recordid: $("#Recordid").val(),
            _csrf: $("#csrfToken").val(),
            approve_reason: $("#approve_comment").val(),
        };
        if ($("#approve_comment").val() == "") {
            $(".approve-error-msg").text("Please enter comment!");
            $("#approve_comment").focus();
            return false;
        }
        $("#loading-overlay").css('display', 'grid');
        $.ajax({
            type: "POST",
            url: "approvepickup",
            data: data,
            success: function (data) {
                $("#loading-overlay").css('display', 'none');
                if (data.status === "success") {
                    $("#approvesubmit").remove()
                    location.reload();
                } else {
                    $(".approve-error-msg").text(data.errors || "sometinhg went wrong");
                }
            },
            error: function (data) {
                $("#loading-overlay").css('display', 'none');
                $(".approve-error-msg").text("Error occured.please try again");
            },
            dataType: "json",
        });
        
    });
    $(document).on("click", "#rejectgeneralsubmit", function () {
        $(".reject-general-error-msg").text("");
        let data = {
            Recordid: $("#Recordid").val(),
            _csrf: $("#csrfToken").val(),
            reject_reason: $("#reject_general_comment").val(),
        };
        if ($("#reject_general_comment").val() == "") {
            $(".reject-general-error-msg").text("Please enter comment!");
            $("#reject_general_comment").focus();
        } else {
            $("#loading-overlay").css('display', 'grid');
            $.ajax({
                type: "POST",
                url: "approvepickup",
                data: data,
                success: function (data) {
                    $("#loading-overlay").css('display', 'none');
                    if (data.status === "success") {
                        $("#approvesubmit").remove()
                        location.reload();
                    } else {
                        $(".reject-general-error-msg").text(data.errors || "sometinhg went wrong");
                    }
                },
                error: function (data) {
                    $("#loading-overlay").css('display', 'none');
                    $(".reject-general-error-msg").text("Error occured.please try again");
                },
                dataType: "json",
            });
        }
    });
    $(document).on("click", ".pickup-start", function () {
        let data = {
            Recordid: $("#Recordid").val(),
            _csrf: $("#csrfToken").val(),
            start_pickup: "Yes"
        };
        $("#loading-overlay").css('display', 'grid');
        $.ajax({
            type: "POST",
            url: "approvepickup",
            data: data,
            success: function (data) {
                $("#loading-overlay").css('display', 'none');
                if (data.status === "success") {
                    $(".pickup-start").remove()
                    location.reload();
                } else {
                    alert(data.errors || "sometinhg went wrong");
                }
            },
            error: function (data) {
                $("#loading-overlay").css('display', 'none');
                alert("Error occured.please try again");
            },
            dataType: "json",
        });
        
    });
    $(document).on("click", ".pickup-complete", function () {
        let data = {
            Recordid: $("#Recordid").val(),
            _csrf: $("#csrfToken").val(),
            pickup_completed: "Yes"
        };
        $("#loading-overlay").css('display', 'grid');
        $.ajax({
            type: "POST",
            url: "approvepickup",
            data: data,
            success: function (data) {
                $("#loading-overlay").css('display', 'none');
                if (data.status === "success") {
                    $(".pickup-complete").remove()
                    location.reload();
                } else {
                    showErrorMessage(data.errors || "sometinhg went wrong");
                }
            },
            error: function (data) {
                $("#loading-overlay").css('display', 'none');
                showErrorMessage("Error occured.please try again");
            },
            dataType: "json",
        });
        
    });
    $(document).on("click", "#modifysubmit", function () {
        $(".approve-error-msg").text("");
        let data = {
            Recordid: $("#Recordid").val(),
            _csrf: $("#csrfToken").val(),
            modify_reason: $("#modify_comment").val(),
        };
        if ($("#modify_comment").val() == "") {
            $(".modify-error-msg").text("Please enter comment!");
            $("#modify_comment").focus();
            return false;
        }
        $("#loading-overlay").css('display', 'grid');
        $.ajax({
            type: "POST",
            url: "approvepickup",
            data: data,
            success: function (data) {
                $("#loading-overlay").css('display', 'none');
                if (data.status === "success") {
                    $("#modifysubmit").remove()
                    location.reload();
                } else {
                    $(".modify-error-msg").text(data.errors || "sometinhg went wrong");
                }
            },
            error: function (data) {
                $("#loading-overlay").css('display', 'none');
                $(".modify-error-msg").text("Error occured.please try again");
            },
            dataType: "json",
        });
        
    });
    $(document).on("click", ".pickup-submit-for-logistic", function () {
        let data = {
            Recordid: $("#Recordid").val(),
            _csrf: $("#csrfToken").val(),
            submit_for_logistrics: "Yes",
        };
        $("#loading-overlay").css('display', 'grid');
        $.ajax({
            type: "POST",
            url: "approvepickup",
            data: data,
            success: function (data) {
                $("#loading-overlay").css('display', 'none');
                if (data.status === "success") {
                    $(".pickup-submit-for-logistic,.add-lead-btn2").remove()
                    location.reload();
                } else {
                    showErrorMessage(data.errors || "sometinhg went wrong");
                }
            },
            error: function (data) {
                $("#loading-overlay").css('display', 'none');
                showErrorMessage("Error occured.please try again");
            },
            dataType: "json",
        });
        
    });
    $(document).on("click", ".pickup-schedule", function () {
        let data = {
            Recordid: $("#Recordid").val(),
            _csrf: $("#csrfToken").val(),
            pickup_schedule: "Yes",
        };
        $("#loading-overlay").css('display', 'grid');
        $.ajax({
            type: "POST",
            url: "approvepickup",
            data: data,
            success: function (data) {
                $("#loading-overlay").css('display', 'none');
                if (data.status === "success") {
                    $(".pickup-schedule,.add-lead-btn2").remove()
                    location.reload();
                } else {
                    showErrorMessage(data.errors || "sometinhg went wrong");
                }
            },
            error: function (data) {
                $("#loading-overlay").css('display', 'none');
                showErrorMessage("Error occured.please try again");
            },
            dataType: "json",
        });
        
    });
    $(document).on("click", ".import-btn", function () { 
        let blockid = $(this).data("section");
        $("#data-import-blockid").val(blockid);
        $(".dataimport-error-msg").text("");
        $('#dataimport-file').val("");
    })
    $(document).on("click", "#dataimport-submit", function () {
        let postData = {
            Recordid: $("#dataImportRecordid").val(),
            _csrf: $("#dataImportCsrfToken").val(),
            blockid: $("#data-import-blockid").val(),
        };
        console.log(postData);
        $(".dataimport-error-msg").text("");
        var fileInput = $('#dataimport-file')[0];
        if (!fileInput.files.length) {
            $(".dataimport-error-msg").text('Please select a file to upload.');
            return;
        }
        var file = fileInput.files[0];
        var fileType = file.name.split('.').pop().toLowerCase();
        var maxSize = 1 * 1024 * 1024; // 1MB in bytes

        if (!['xlsx','XLSX', 'xls','XLS'].includes(fileType)) {
            $(".dataimport-error-msg").text('Invalid file type. Please select an Excel file.');
            return;
        }
        if (file.size > maxSize) {
            $(".dataimport-error-msg").text('File size exceeds 1MB. Please upload a smaller file.');
            return;
        }
        var reader = new FileReader();
        reader.onload = function (e) {
            var data = new Uint8Array(e.target.result);
            var workbook = XLSX.read(data, { type: 'array' });
            var sheetName = workbook.SheetNames[0]; // Read first sheet
            var sheet = XLSX.utils.sheet_to_json(workbook.Sheets[sheetName], { header: 1 });
            let datarray = sheet.filter(row => row.length > 0);
            if (datarray.length <= 1) {
                $(".dataimport-error-msg").text("The Excel file appears to be empty or malformed.");
                return;
            }
            console.log(datarray);
            postData["excel_data"] = datarray;
            $("#loading-overlay").css('display', 'grid');
            $.ajax({
                type: "POST",
                url: "importdata",
                data: postData,
                success: function (data) {
                    //$("#loading-overlay").css('display', 'none');
                    if (data.status === "success") {
                        $("#dataimport-submit").remove()
                        $(".dataimport-error-msg").text("Data is uploaded successfully.");
                        location.reload();
                    } else {
                        $("#loading-overlay").css('display', 'none');
                        $(".dataimport-error-msg").text(data.errors || "sometinhg went wrong");
                    }
                },
                error: function (data) {
                    $("#loading-overlay").css('display', 'none');
                    $(".dataimport-error-msg").text("Error occured.please try again");
                },
                dataType: "json",
            });
        };
        reader.readAsArrayBuffer(file);
    });
    hidePickupStatus()
    manageStageChecks()
    manageProvisionToTimingExtend()
    manageMaterialLocation()
    manageServiceLiftCondition()
    manageStairsSpaceCondition()
    manageSegregation()
    manageLocalUnionCharges()
    manageLocalVehicleRequired()
    manageOverTime()

     ///////////on change product nae get make model///////
      // produt changes observer
  // Function to observe input value changes
  function observeInputChanges(inputElement) {
    const observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        if (
          mutation.type === "attributes" &&
          mutation.attributeName === "value"
        ) {
          console.log(
            `Value changed in ${inputElement.id}: ${inputElement.value}`
          );
          const nearestTr = inputElement.closest("tr");
          if (nearestTr) {
            trid = nearestTr.id;
            console.log("Nearest <tr> ID:", nearestTr.id);
            getProductinfo(trid, `${inputElement.value}`);

          } else {
            nearestTr.id = "";
            console.log("No <tr> ancestor found");
          }
        }
      });
    });

    observer.observe(inputElement, {
      attributes: true, // Observe attribute changes
      attributeFilter: ["value"], // Only watch 'value' attribute
    });

    console.log(`Observer attached to input: ${inputElement.id}`);
  }

  // Function to observe all matching inputs
  function observeMatchingInputs() {
    // Match inputs with ID pattern 'productname_*1'
    const inputs = document.querySelectorAll(
      'input[id^="productname_"][id$="1"]'
    );
    inputs.forEach((input) => observeInputChanges(input));
    console.log(`Observers attached to ${inputs.length} inputs.`);
  }

  // Function to monitor dynamically added inputs
  function monitorDynamicInputs() {
    const container = document.body; // Observe the entire document

    const observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        if (mutation.type === "childList" && mutation.addedNodes.length > 0) {
          mutation.addedNodes.forEach((node) => {
            if (node.nodeType === 1) {
              // Check for new matching inputs
              const newInputs = node.querySelectorAll(
                'input[id^="productname_"][id$="1"]'
              );
              // console.log("deepika");
              newInputs.forEach((input) => observeInputChanges(input));
            }
          });
        }
      });
    });

    observer.observe(container, {
      childList: true, // Detect added elements
      subtree: true, // Include all child elements
    });

    console.log("Monitoring dynamic inputs for pattern: productname_*1");
  }

  // Initialize observers for existing and dynamic inputs
  observeMatchingInputs();
  monitorDynamicInputs();

  // get productinfo
  function getProductinfo(trid, productid) {
    // alert(productid);
    data = { productid: productid, _csrf: $("#csrfToken").val() };

    $.ajax({
      type: "POST",
      url: "getproductinfo",
      // async:false,
      data: data,
      success: function (response) {
        //alert(response); // Log the entire response to check its structure

        // Check if the data object exists and contains 'first_name'
        if (response && response.data) {
          $("#category_name_" + trid).val(response.data.category_name).trigger("change");
          
          $("#sub_category_name_" + trid).val(response.data.subcategory_name).trigger("change");
          $("#make_name_" + trid).val(response.data.make_name).trigger("change");
         
          $("#model_name_" + trid).val(response.data.model_name).trigger("change");
        
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
});