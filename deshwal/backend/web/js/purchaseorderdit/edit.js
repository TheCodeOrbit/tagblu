$(document).ready(function () {
    var newURL = window.location.href;
    $(".add-more-records").hide();
    // get exchangerate
    const modeInput = document.getElementById("mode");
    if (modeInput && modeInput.value === "Create") {
        //addRowBtn('2718', 'purchaseorderdit')//commented on 13 oct 2025

        // initialize stage with draft
        $("#stage").val("1").trigger("change");
        $(".section-send_for_first_approval").addClass("tr-hidden");


        //set today date
        // Get today's date in YYYY-MM-DD format
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, "0");
        var mm = String(today.getMonth() + 1).padStart(2, "0"); // Months are zero-indexed
        var yyyy = today.getFullYear();

        var todaydate = dd + "-" + mm + "-" + yyyy; // Format the date as YYYY-MM-DD
        //alert('"'+todaydate+'"');

        $("#purchase_order_date").val(todaydate);
        setTimeout(() => {
            flatpickr("#purchase_order_date", {
                defaultDate: new Date(),
                dateFormat: "d-m-Y",
            });


        }, 500); // Waits for 600 milliseconds (1/2 seconds)


    }
    var stage = $("#stage").val();
    // if(so_stage == 1)
    //$(".section-send_for_second_approval").addClass("tr-hidden");

});
///////////////////get Vendor  account///////////////////////////
/////////////create mutation for Sales order/////////////////
// Create a MutationObserver to detect changes to the input reference_number1
var targetNode_so = document.getElementById("reference_number1");
var observer = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
        if (mutation.type === "attributes" && mutation.attributeName === "value") {
            console.log("so value changed to:", targetNode_so.value);

            //getvendoraccount(targetNode_so.value);
        }
    }
});
// Configuration for the observer (observe attribute changes)
var config = { attributes: true };
if (targetNode_so)
    observer.observe(targetNode_so, config);
///////////////////get Vendor  address////////////////////////////
/////////////create mutation for Vendor location/////////////////
// Create a MutationObserver to detect changes to the input bill_location1
var targetNode_loc1 = document.getElementById("location1");
var observer = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
        if (mutation.type === "attributes" && mutation.attributeName === "value") {
            console.log("Vendor location value changed to:", targetNode_loc1.value);

            getvendoraddress(targetNode_loc1.value);
        }
    }
});
// Configuration for the observer (observe attribute changes)
var config = { attributes: true };
if (targetNode_loc1)
    observer.observe(targetNode_loc1, config);
///////////////////get bill address//////////////////////////////
/////////////create mutation for Vendor location/////////////////
// Create a MutationObserver to detect changes to the input bill_entitiy_name1
var targetNode_loc2 = document.getElementById("bill_entitiy_name1");
var observer = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
        if (mutation.type === "attributes" && mutation.attributeName === "value") {
            console.log("Bill location value changed to:", targetNode_loc2.value);

            getbilladdress(targetNode_loc2.value);
        }
    }
});
// Configuration for the observer (observe attribute changes)
var config = { attributes: true };
if (targetNode_loc2)
    observer.observe(targetNode_loc2, config);
///////////////////get delivery address//////////////////////////////
/////////////create mutation for Vendor location/////////////////
// Create a MutationObserver to detect changes to the input delivery_entitiy_name1
var targetNode_loc3 = document.getElementById("delivery_entitiy_name1");
var observer = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
        if (mutation.type === "attributes" && mutation.attributeName === "value") {
            console.log("Delvery location value changed to:", targetNode_loc3.value);

            getdeliveryaddress(targetNode_loc3.value);
        }
    }
});

// Configuration for the observer (observe attribute changes)
var config = { attributes: true };
if (targetNode_loc3)
    observer.observe(targetNode_loc3, config);

function getvendoraddress(bill_location) {
    data = {
        bill_location: bill_location,
        _csrf: $("#csrfToken").val(),
    };

    $.ajax({
        type: "POST",
        url: "getvendoraddress",
        // async:false,
        data: data,
        success: function (response) {
            console.log(response); // Log the entire response to check its structure

            // Check if the data object exists and contains 'first_name'
            if (response && response.data) {

                $("#address").val(response.data.address);
                // $("#billing_city").val(response.data.bill_city);
                $("#source_of_supply").val(response.data.state);
                $("#state_code").val(response.data.state_code);
                $("#gst_number").val(response.data.gstin_no_uin);



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
function getbilladdress(bill_location) {
    data = {
        bill_location: bill_location,
        _csrf: $("#csrfToken").val(),
    };

    $.ajax({
        type: "POST",
        url: "getwarehouseaddress",
        // async:false,
        data: data,
        success: function (response) {
            console.log(response); // Log the entire response to check its structure

            // Check if the data object exists and contains 'first_name'
            if (response && response.data) {
                $("#bill_location").val(response.data.warehouse_name);
                $("#bill_address").val(response.data.address);
                // $("#bill_location").val(response.data.bill_city);
                $("#destination_of_supply").val(response.data.state);
                $("#bill_state_code").val(response.data.statecode);
                $("#bill_gst_number").val(response.data.gstn);
                var bill_state_code = $("#bill_state_code").val();
                var delivery_state_code = $("#delivery_state_code").val();
                gst = 18;
                if (bill_state_code == delivery_state_code) {
                    //cgst and sgst
                    gst = gst / 2;
                    $(".cgst").val(gst);
                    $(".sgst").val(gst);
                    $(".igst").val('');

                }
                else {
                    //igst
                    $(".cgst").val('');
                    $(".sgst").val('');
                    $(".igst").val(gst);

                }
                settotalprice();

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
function getdeliveryaddress(bill_location) {
    data = {
        bill_location: bill_location,
        _csrf: $("#csrfToken").val(),
    };

    $.ajax({
        type: "POST",
        url: "getwarehouseaddress",
        // async:false,
        data: data,
        success: function (response) {
            console.log(response); // Log the entire response to check its structure

            // Check if the data object exists and contains 'first_name'
            if (response && response.data) {
                $("#delivery_location").val(response.data.warehouse_name);
                $("#delivery_address").val(response.data.address);
                // $("#bill_location").val(response.data.bill_city);
                $("#delivery_gst_number").val(response.data.gstn);
                $("#delivery_state_code").val(response.data.statecode);
                $("#delivery_destination_of_supply").val(response.data.state);
                var bill_state_code = $("#bill_state_code").val();
                var delivery_state_code = $("#delivery_state_code").val();
                gst = 18;
                if (bill_state_code == delivery_state_code) {
                    //cgst and sgst
                    gst = gst / 2;
                    $(".cgst").val(gst);
                    $(".sgst").val(gst);
                    $(".igst").val('');

                }
                else {
                    //igst
                    $(".cgst").val('');
                    $(".sgst").val('');
                    $(".igst").val(gst);

                }
                
                settotalprice();


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
function getvendoraccount(vendor) {
    data = {
        vendor: vendor,
        _csrf: $("#csrfToken").val(),
    };

    $.ajax({
        type: "POST",
        url: "getvendoraccount",
        // async:false,
        data: data,
        success: function (response) {
            console.log(response); // Log the entire response to check its structure

            // Check if the data object exists and contains 'first_name'
            if (response && response.data) {
                $("#vendor_name1").val(response.data.vendoraccid);
                $("#vendor_name").val(response.data.acc_name);



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
/////////////// product changes observer///////////////////////////
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
    // Match inputs with ID pattern 'product_name_*1'
     // 'input[id^="reference_no_"][id$="1"]' added by ptpatel on date 28-10-2025
    // const inputs = document.querySelectorAll(
    //     'input[id^="product_name_"][id$="1"],input[id^="reference_no_"][id$="1"]'
    // );
    //fixed product fetch issue from SO by deepika in 14 jan 2026
    const inputs = document.querySelectorAll(
        'input[id^="product_name_"][id$="1"]'
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
                        // 'input[id^="reference_no_"][id$="1"]' added by ptpatel on date 28-10-2025
                        // const newInputs = node.querySelectorAll(
                        //     'input[id^="product_name_"][id$="1"],input[id^="reference_no_"][id$="1"]'
                        // );
                        //fixed product fetch issue from SO by deepika in 14 jan 2026
                        const newInputs = node.querySelectorAll(
                            'input[id^="product_name_"][id$="1"]'
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

    console.log("Monitoring dynamic inputs for pattern: product_name_*1");
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

            // Check if the data object exists and contains 'first_name'
            if (response && response.data) {
                $("#product_description_" + trid).val(response.data.product_description);
                $("#hsn_code_"+trid).val(response.data.hsn_code);
                var bill_state_code = $("#bill_state_code").val();
                var delivery_state_code = $("#delivery_state_code").val();
                var gst = response.data.gst_percentage;
                $("#gst_" + trid).val(gst);

                if (bill_state_code == delivery_state_code) {
                    //cgst and sgst

                    gst = gst / 2;
                    $("#cgst_" + trid).val(gst);
                    $("#sgst_" + trid).val(gst);
                    $("#igst_" + trid).val('');

                }
                else {
                    //igst
                    $("#igst_" + trid).val(gst);
                    $("#cgst_" + trid).val('');
                    $("#sgst_" + trid).val('');
                }
                settotalprice();


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

////////////approvals/////////////
$(document).on("click", "#approvesubmit", function () {
    let data = {
        Recordid: $("#Recordid").val(),
        _csrf: $("#csrfToken").val(),
        approve_reason: $("#approve_comment").val(),
    };
    if ($("#approve_comment").val() == "") {
        alert("Please enter comment!");
        $("#approve_comment").focus();
        return false;
    }

    $.ajax({
        type: "POST",
        url: "approvepurchaseorderdit",
        data: data,
        success: function (data) {
            if (data.status === "success") location.reload();
            else alert("sometinhg went wrong");
        },
        error: function (data) {
            alert("Error occured.please try again");
        },
        dataType: "json",
    });
});
$(document).on("click", "#rejectsubmit", function () {
    //alert("dfhfdhd");
    data = {
        Recordid: $("#Recordid").val(),
        _csrf: $("#csrfToken").val(),
        leadstatus_m: $("#leadstatus_m").val(),
        reject_reason: $("#reject_comment").val(),
    };
    // {leadstatus_v:$("#leadstatus_v").val(),Recordid: $('#Recordid').val();,approve_reason:$("#approve_reason").val();, _csrf: $('#csrfToken').val();};
    if ($("#reject_comment").val() == "") {
        alert("Please enter comment!");
        $("#reject_comment").focus();
    } else {
        $.ajax({
            type: "POST",
            url: "approvepurchaseorderdit",
            // async:false,
            data: data,
            success: function (data) {
                if (data.status === "success") location.reload();
                else alert("sometinhg went wrong");
            },
            error: function (data) {
                // if error occured

                alert("Error occured.please try again");
            },
            dataType: "json",
        });
    }
    // alert("dfhfdhd");
});

//disable auto dd of stage
const stageSelect = document.getElementById("stage");
const options = stageSelect.options;
var stage = document.getElementById("stage").value;
var mode = document.getElementById("mode");
if (mode && mode.value === "Create")
    stage = 1;
for (let i = 0; i < options.length; i++) {
    //alert(options[i].value);
    if (stage != options[i].value) {
        options[i].disabled = true;

        if (stage == "4" && options[i].value == "5")
            options[i].disabled = false;


    }

}
///////////////check submit_for_approval is cheked then disble it/////////
var submit_approval = document.getElementById("send_for_approval");
if (submit_approval) {
    if (submit_approval.checked) {
        // Checkbox is checked
        $("#send_for_approval").prop("disabled", true);
    } else {
        $("#send_for_approval").prop("disabled", false);
    }
}

//////////////Product Total///////////////
// Event listeners for CP and Quantity fields in each row
document.addEventListener("input", function (event) {
    if (
        event.target.classList.contains("qty") ||
        event.target.classList.contains("basic_cost_price") ||
        event.target.classList.contains("cgst") ||
        event.target.classList.contains("sgst") ||
        event.target.classList.contains("igst")
    ) {
        const row = $(event.target).closest(".product-row").attr("id"); // Using jQuery

        calculateRowPrice(row);
    }
});
function calculateRowPrice(trid) {
    //alert("sdfdsf"+trid);
    cp = $("#basic_cost_price_" + trid).val() || 0;
    quantity_required = $("#qty_" + trid).val() || 0;
    cgst = $("#cgst_" + trid).val() || 0;
    sgst = $("#sgst_" + trid).val() || 0;
    igst = $("#igst_" + trid).val() || 0;
    totalcost = cp * quantity_required;
    cgstamout = (cgst / 100) * totalcost;
    sgstamout = (sgst / 100) * totalcost;
    igstamout = (igst / 100) * totalcost;
    totalcostwithprice = totalcost + cgstamout + sgstamout + igstamout;
    // alert(cp+" "+quantity_required+" "+totalcost);
    $("#product_total_" + trid).val(totalcostwithprice);
    // calctotalamt();
}

$(document).on(
    "change",
    "[id^=qty_], [id^=basic_cost_price_],[id^=cgst_],[id^=sgst_],[id^=igst_]",
    function () {

        settotalprice();

    }

);
document.addEventListener("click", function(e) {
    if (e.target.closest(".remove-row-btn")) {
        setTimeout(function(){
            settotalprice();
        }, 0);
    }
}, true); //  capture phase

function settotalprice() {
    var subtotal = 0;
    var total = 0;
    var grandtotal = 0;
    var cgstamout = 0;
    var sgstamout = 0;
    var igstamout = 0;
    var bill_state_code = parseInt($("#bill_state_code").val());
    var delivery_state_code = parseInt($("#delivery_state_code").val());
    if ($("[class^=qty]").length > 0){
        $("[class^=qty]").each(function () {
            var suffix = $(this).attr("id").match(/\d+$/)
                ? $(this).attr("id").match(/\d+$/)[0]
                : "";
            var basic_cost_price = parseFloat($(`#basic_cost_price_${suffix}`).val()) || 0;
            var qty = parseFloat($(`#qty_${suffix}`).val()) || 0;
            var gst = parseFloat($(`#gst_${suffix}`).val()) || 0;

            var modeso= document.getElementById("mode");
            if(modeso !== "Create")
            {
                var cgst = parseFloat($(`#cgst_${suffix}`).val()) || 0;
                var sgst = parseFloat($(`#sgst_${suffix}`).val()) || 0;
                var igst = parseFloat($(`#igst_${suffix}`).val()) || 0;

                gst = cgst+sgst+igst;
                $(`#gst_${suffix}`).val(gst);
            }

            //check if ordered qty if greater than remaining qty
            var remaining_qty = parseFloat($(`#remaining_qty_${suffix}`).val()) || 0;
            // Get the element by ID using jQuery
            const element = $("#qty_" + suffix);

            // Find the nearest .help-block element (searching up or down the DOM)
            const helpBlock = element.closest('.form-group').find('.help-block').first();
            
            if (qty > remaining_qty) {
                document.getElementById("qty_" + suffix).value = '';
                
                $(`#qty_${suffix}`).val('');

                // Set the message (for example)
                helpBlock.text("Ordered qty cannot be greater than Remaining Qty ");
                
            }
            else{
                helpBlock.text("");
            
            }
        

            if (bill_state_code == delivery_state_code) 
            {
                cgst = gst / 2;
                $(`#cgst_${suffix}`).val(cgst);
                sgst = gst / 2;
                $(`#sgst_${suffix}`).val(sgst);
            }
            else{
                $(`#igst_${suffix}`).val(gst);
                
            }

            // alert(basic_cost_price);
            var cgst = parseFloat($(`#cgst_${suffix}`).val()) || 0;
            var sgst = parseFloat($(`#sgst_${suffix}`).val()) || 0;
            var igst = parseFloat($(`#igst_${suffix}`).val()) || 0;
            var midtotal = basic_cost_price * qty;
            total += basic_cost_price * qty;
            //  alert(basic_cost_price+" "+qty+" "+total);
            subtotal += (basic_cost_price * qty);
            cgst = cgst * midtotal / 100;
            sgst = sgst * midtotal / 100;
            igst = igst * midtotal / 100;
            //grandtotal += (basic_cost_price*qty) + cgst + sgst + igst;
            cgstamout += cgst;
            sgstamout += sgst;
            igstamout += igst;
            grandtotal = subtotal + cgstamout + sgstamout + igstamout;
            $("#sub_total").val(total.toFixed(2));
            $("#cgst_amount").val(cgstamout.toFixed(2));
            $("#sgst_amount").val(sgstamout.toFixed(2));
            $("#igst_amount").val(igstamout.toFixed(2));
            $("#total").val(grandtotal.toFixed(2));
            calculateRowPrice(suffix);
             $(".savebutton").prop("disabled", false);
        });
    }else
    {
            $("#sub_total").val("");
            $("#cgst_amount").val("");
            $("#sgst_amount").val("");
            $("#igst_amount").val("");
            $("#total").val("");
             $(".savebutton").prop("disabled", true);
    }

}

///added on 13 oct 2025 for autofill products from sales order
/////////////// product changes observer///////////////////////////
// Function to observe input value changes
function observeInputChangesSo(inputElement) {
  if (!inputElement) return;
  const modeInput_so= document.getElementById("mode");

  // Attach listener for future user input
  inputElement.addEventListener("input", () => {
    const value = inputElement.value.trim();
    if (value !== "") {
      console.log("🚀 [input] Calling getproductdetail()");
      if (modeInput_so && modeInput_so.value === "Create") 
      getproductdetail();
    }
  });

  // 🟡 Check immediately if value is already present (set via PHP)
  const initialValue = inputElement.value.trim();
  if (initialValue !== "") {
    console.log("🚀 [initial] Calling getproductdetail() for prefilled value:", initialValue);
    if (modeInput_so && modeInput_so.value === "Create") 
    getproductdetail();
  }
}





// Function to observe all matching inputs
function observeMatchingInputsso() {
    // Match inputs with ID pattern 'product_name_*1'
    const inputs = document.querySelectorAll(
         'input[id^="reference_number1"]'
    );
    inputs.forEach((input) => observeInputChangesSo(input));
    console.log(`SO Observers attached to ${inputs.length} inputs.`);
}

// Function to monitor dynamically added inputs
function monitorDynamicInputsso() {
  const container = document.body;

  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      if (mutation.type === "childList" && mutation.addedNodes.length > 0) {
        mutation.addedNodes.forEach((node) => {
          if (node.nodeType === 1) {
            const newInputs = node.querySelectorAll('input[id^="reference_number1"]');
            newInputs.forEach((input) => observeInputChangesSo(input));
          }
        });
      }
    });
  });

  observer.observe(container, {
    childList: true,
    subtree: true,
  });

  console.log("Monitoring dynamic inputs for: reference_number1*");
}


// Initialize observers for existing and dynamic inputs
document.addEventListener("DOMContentLoaded", () => {
  const input = document.getElementById("reference_number1");
//   observeInputChangesSo(input);
});

observeMatchingInputsso();
monitorDynamicInputsso();
// Create a MutationObserver to detect changes to the input quote name
var targetNodeso = document.getElementById("reference_number1");
var observer = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
        if (mutation.type === "attributes" && mutation.attributeName === "value") {
            console.log("so value changed to:", targetNodeso.value);
            $("#loading-overlay").css('display', 'grid');
            //get products from product detail
            getproductdetail();
            //  Enable save button if reference_number_1 changed and not empty
                if (targetNodeso.value.trim() !== "") {
                    $(".savebutton").prop("disabled", false);

                //  Fix selector here — use the correct ID
                const $refField = $("#reference_number1");
                const $formGroup = $refField.closest(".form-group");
                const $helpBlock = $formGroup.find(".help-block").first();

                $formGroup.removeClass("error");
                $helpBlock.text("");
                    console.log("Save button enabled — reference_number_1 changed.");
                }
            $("#loading-overlay").css('display', 'none');

        }
    }
});

// Configuration for the observer (observe attribute changes)
var config = { attributes: true };
if (targetNodeso)
    observer.observe(targetNodeso, config);

async function getproductdetail() {
    const data = {
        so_name: $("#reference_number1").val(),
        _csrf: $("#csrfToken").val(),
    };

    try {
        const response = await $.ajax({
            type: "POST",
            url: "getproductdetail",
            data: data,
            dataType: "json",
        });

        // console.log(response); // Log the entire response to check its structure

        if (response && response.data) {
            $('#productTable2718 tbody').html('');

            $("#loading-overlay").css('display', 'grid');

            // Initialize currentRow to keep track of the last appended row
            let currentRow = '';
            var subtotal = 0;
            var totalgst = 0;
            var total_cgst = 0;
            var total_sgst = 0;
            var total_igst = 0;
            var total = 0;
            // Loop through each product in the response
            for (let i = 0; i < response.data.length; i++) {
                const j = i + 1;
                const res = response.data[i];

                // Wait for the row to be added before proceeding with updates
                await addRowBtn("2718", "salesorderdit");

                // Find the last row and get its index
                const tbody = $('#productTable' + 2718 + ' tbody');
                const lastRow = tbody.find('tr:last');
                const rowIndex = lastRow.index();

                // Check if this row is new and not already updated
                if (lastRow.length > 0 && currentRow !== rowIndex) {
                    console.log(`Processing row ${j} (Row Index: ${rowIndex})`);

                    // Find the product_name input element
                    const productNameInput = lastRow.find(`#product_name_${j}`);

                    if (productNameInput.length > 0) {
                        // Update the input values for the row
                        productNameInput.val(res.product_name);

                        lastRow.find(`#reference_no_${j}1`).val(res.salesorder_dit_id);
                        lastRow.find(`#reference_no_${j}`).val(res.salesorder_dit_no);

                        lastRow.find(`#product_name_${j}1`).val(res.product_name);
                        lastRow.find(`#product_name_${j}`).val(res.prod_name);

                        //auto fill product_description and oem part no as per V11 - point no 36 code added by ptptale on date 11-10-2025

                        lastRow.find(`#product_description_${j}`).val(res.prod_description);
                        lastRow.find(`#oem_part_number_${j}`).val(res.prod_oem_part_number);

                        //auto fill product_description and oem part no as per V11 - point no 36 code end added by ptptale on date 11-10-2025


                        // lastRow.find(`#category_${j}`).val(res.category);
                        lastRow.find(`#hsn_code_${j}`).val(res.hsn_code);
                        lastRow.find(`#so_qty_${j}`).val(res.qty);
                        lastRow.find(`#remaining_qty_${j}`).val(res.remaining_qty);
                        // lastRow.find(`#uom_${j}`).val(res.uom);
                        // lastRow.find(`#cgst_per_${j}`).val(res.cgst_per);
                        // lastRow.find(`#sgst_per_${j}`).val(res.sgst_per);
                        // lastRow.find(`#igst_per_${j}`).val(res.igst_per);
                        // lastRow.find(`#basic_price_${j}`).val(res.basic_price);
                        var amnt = res.sales_price * res.quantity;
                        // lastRow.find(`#amount_${j}`).val(res.amount);
                        // $(".remove-row-btn").addClass("tr-hidden");

                        var cgst_amt = amnt * res.cgst / 100;
                        total_cgst += cgst_amt;

                        var sgst_amt = amnt * res.sgst / 100;
                        total_sgst += sgst_amt;

                        var igst_amt = amnt * res.igst / 100;
                        total_igst += igst_amt;

                        var totalgst = cgst_amt + sgst_amt + igst_amt;
                        subtotal += amnt;
                        // alert(subtotal);
                        // alert(total_cgst + total_sgst + total_igst);
                        // total += subtotal + total_cgst + total_sgst + total_igst;
                        // alert(total);

                        // Update the currentRow index after appending and updating
                        currentRow = rowIndex;
                    } else {
                        console.error(`Product Name input not found for ID: #product_name_${j}`);
                    }
                }
            }
            $(".savebutton").prop("disabled", false);
            var totalgst = total_cgst + total_sgst + total_igst;
            total = subtotal + totalgst;
            // var total_price_words = numberToWords(total);




            $("#loading-overlay").css('display', 'none');

            // After all rows are processed, update the total amount after a delay
            // setTimeout(() => {
            //setTotalAmount();
            // }, 5000);
        } else {
            console.error("Invalid response format or missing data");
        }
    } catch (error) {
        console.error("Error occurred while fetching product details:", error);
        alert("Error occurred. Please try again.");
    }
}

// Get the table by ID
const table = document.getElementById('productTable2718');

if (table) {
  // Get the first <tr> inside the <thead>
  const thead = table.querySelector('thead');
  if (thead) {
    const firstHeaderRow = thead.querySelector('tr');
    if (firstHeaderRow) {
      // Create new <th> element
      const th = document.createElement('th');
      th.textContent = 'Action';

      // Append the new <th> at the end of the header row
      firstHeaderRow.appendChild(th);
    } else {
      console.warn('No header row (<tr>) found inside <thead>');
    }
  } else {
    console.warn('No <thead> found in table');
  }
} else {
  console.warn('Table with id productTable2718 not found');
}

$(document).ready(function () {
    function showRefError($refField) {
        var $formGroup = $refField.closest('.form-group');
        var $helpBlock = $formGroup.find('.help-block').first();
        if ($helpBlock.length === 0) {
            $helpBlock = $refField.closest('div').find('.help-block').first();
        }
        $formGroup.addClass('error');
        $helpBlock.text('This field is mandatory.');
    }

    function clearRefError($refField) {
        var $formGroup = $refField.closest('.form-group');
        var $helpBlock = $formGroup.find('.help-block').first();
        if ($helpBlock.length === 0) {
            $helpBlock = $refField.closest('div').find('.help-block').first();
        }
        $formGroup.removeClass('error');
        $helpBlock.text(''); // ✅ Clear message completely
    }

    const $refField = $('#reference_number1');
    const $saveBtn = $('.savebutton');
    const $form = $saveBtn.closest('form');

    // 🔁 Live validation while typing
    $refField.on('input change', function () {
        var val = $(this).val().trim();
        if (val !== '') {
            clearRefError($refField);
            $saveBtn.prop('disabled', false);
        } else {
            showRefError($refField);
            $saveBtn.prop('disabled', true);
        }
    });

    // 🚫 Intercept form submission
    $form.on('submit', function (e) {
        var val = String($refField.val() || '').trim();

        clearRefError($refField);

        if (val === '') {
            e.preventDefault();
            showRefError($refField);
            $saveBtn.prop('disabled', true);
            console.log('Save disabled because reference_number1 is empty.');
            return false;
        }

        // ✅ Allow form to submit
        console.log('Save allowed — reference_number1 =', val);
        $saveBtn.prop('disabled', false);
        return true;
    });
});



 //code added by ptpatel on date  30-03-26
 $(document).ready(function () {
      /////////////create mutation for sourcing deal/////////////////
    // Create a MutationObserver to detect changes to the input vendor account
    function getQueryParam(name) {
      const urlParams = new URLSearchParams(window.location.search);
      return urlParams.get(name);
  }

     // Get the values of sourcemodule and sourceid
    var sourcemodule = getQueryParam('sourcemodule');
    var sourceid = getQueryParam('sourceid');

    // Check if both parameters are present and valid
    if (sourcemodule && sourceid) {
        // Call the desired jQuery function, passing the parameters if needed
        getSOdetail(sourceid,sourcemodule);
    }
     
   
    /////////get so detail///////
    function getSOdetail(sourceid,sourcemodule) {
      // alert("getsourvingcall");
      if (sourcemodule && sourceid) {
        data = {
          sourceid: sourceid,
          sourcemodule : sourcemodule,
          _csrf: $("#csrfToken").val(),
        };

        $.ajax({
          type: "POST",
          url: "getsodetail",
          // async:false,
          data: data,
          success: function (response) {

            // Check if the data object exists and contains 'first_name'
            if (response && response.data) {
            //   $("#reference_number").val(response.data.salesorder_dit_no);
            //   $("#reference_number1").val(response.data.salesorder_dit_id);

            addMultiSelectTag({
                containerId: '#reference_number',
                hiddenFieldId: '#reference_number1',
                text: response.data.salesorder_dit_no,
                value: response.data.salesorder_dit_id
            });
            $("#reference_number").prop("readonly", true);
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
    //end code added by ptpatel on date 30-03-26

