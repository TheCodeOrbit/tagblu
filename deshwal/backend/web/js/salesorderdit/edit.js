$(document).ready(function () {
    var newURL = window.location.href;

    // get exchangerate
    const modeInput = document.getElementById("mode");
    if (modeInput && modeInput.value === "Create") {
        // initialize stage with draft
        $("#so_stage").val("1").trigger("change");
        $(".section-send_for_first_approval").addClass("tr-hidden");


    }
    var so_stage = $("#so_stage").val();
    // if(so_stage == 1)
    //$(".section-send_for_second_approval").addClass("tr-hidden");


    //hide procurement executive and ready to ship and procurement pending
    $(".section-procurement_executive").addClass("tr-hidden");
    $(".section-procurement_pending").addClass("tr-hidden");
    $(".section-ready_to_ship").addClass("tr-hidden");

    var so_stage = $('#so_stage').val();
    //show procuremnet pending and ready to ship checkbox when approved stage
    if (so_stage == "4") {

        $(".section-procurement_executive").removeClass("tr-hidden");
        $(".section-procurement_pending").removeClass("tr-hidden");
        $(".section-ready_to_ship").removeClass("tr-hidden");
        $("#procurement_executive").attr("readonly",true);
        $('#procurement_executive').next('#showCustomer1').hide();
        $('#procurement_executive').parents().find('#removeTextValue').hide();



    }
    if (so_stage == "6") {

        $(".section-procurement_executive").removeClass("tr-hidden");
        $(".section-procurement_pending").removeClass("tr-hidden");
        // $(".section-ready_to_ship").removeClass("tr-hidden");
    }
    if (so_stage == "5") {

        // $(".section-procurement_executive").removeClass("tr-hidden");
        // $(".section-procurement_pending").removeClass("tr-hidden");
        $(".section-ready_to_ship").removeClass("tr-hidden");
    }
    var procurement_pending = document.getElementById("procurement_pending");
    var ready_to_ship = document.getElementById("ready_to_ship");
    if (procurement_pending || procurement_pending) {
        if (procurement_pending.checked || ready_to_ship.checked) {
            // Checkbox is checked
            $("#ready_to_ship").prop("disabled", true);
            $("#procurement_pending").prop("disabled", true);
        } else {
            $("#ready_to_ship").prop("disabled", false);
            $("#procurement_pending").prop("disabled", false);
        }
    }
    // When procurement_pending is changed
    $("#procurement_pending").on("change", function () {
        if (this.checked) {
            $("#ready_to_ship").prop("checked", false);
            $("#procurement_executive").attr("readonly",false);
            $('#procurement_executive').next('#showCustomer1').show();
            $('#procurement_executive').parents().find('#removeTextValue').show();


            $("#procurement_executive").val("");
            $("#procurement_executive1").val("");
            $("#procurement_executive").removeClass("V~O").addClass("V~M");
        } else {
            $("#procurement_executive").attr("readonly",true);
            $('#procurement_executive').next('#showCustomer1').hide();
            $('#procurement_executive').parents().find('#removeTextValue').hide();            

            $("#procurement_executive").val("");
            $("#procurement_executive1").val("");
            $("#procurement_executive").removeClass("V~M").addClass("V~O");
        }
    });

    // When ready_to_ship is changed
    $("#ready_to_ship").on("change", function () {
        if (this.checked) {
            $("#procurement_pending").prop("checked", false);
            $("#procurement_executive").removeClass("V~M").addClass("V~O");
            var helpBlock = $("#procurement_executive").closest(".form-group").find(".help-block");
            if (helpBlock.length) {
                helpBlock.html('');
            }
             $("#procurement_executive").val("");
            $("#procurement_executive1").val("");
            $("#procurement_executive").attr("readonly",true);
            $('#procurement_executive').next('#showCustomer1').hide();
            $('#procurement_executive').parents().find('#removeTextValue').hide();   
        }
    });


});

//added on 11 oct 2025 make disable all stages except Ready to sHip and Procuremnt Pending
// var invSelect = document.getElementById("so_stage");
// if (invSelect) {
//     // Get the updated stage invvalue
//     const invstage = parseInt($("#so_stage").val() || "0");

//     // Disable all options except current invstage 
//     const invoptions = invSelect.options;
//     for (let i = 0; i < invoptions.length; i++) {
//         const invvalue = parseInt(invoptions[i].value);

//         console.log(parseInt($("#so_stage").val()) + " i=" + i);
//         if (parseInt($("#so_stage").val()) == 4 && (i == 5 || i == 6)) {
//             invoptions[i].disabled = false;
//         }
//         // Always enable current invstage
//         else if (invvalue === invstage) {
//             invoptions[i].disabled = false;
//         }
//         else {
//             invoptions[i].disabled = true;
//         }
//     }
// }





/////////add a row on create///////////
const mode = document.getElementById("mode");
if (mode && mode.value === "Create") {
    $("#loading-overlay").css('display', 'grid');
    //get products from product detail
    getbilladdress();
    getdealname();
    $("#loading-overlay").css('display', 'none');


}

// Create a MutationObserver to detect changes to the input opportunity
var targetNode = document.getElementById("deal_name1");
var observer = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
        if (mutation.type === "attributes" && mutation.attributeName === "value") {
            console.log("Opportuntiy value changed to:", targetNode.value);
            $("#loading-overlay").css('display', 'grid');
            //get products from product detail
            getbilladdress();
            getdealname();
            $("#loading-overlay").css('display', 'none');

        }
    }
});

// Configuration for the observer (observe attribute changes)
var config = { attributes: true };
if (targetNode)
    observer.observe(targetNode, config);

// Create a MutationObserver to detect changes to the input quote name
var targetNodeQute = document.getElementById("quote_name1");
var observer = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
        if (mutation.type === "attributes" && mutation.attributeName === "value") {
            console.log("quote value changed to:", targetNode.value);
            $("#loading-overlay").css('display', 'grid');
            //get products from product detail
            gettotalamount();
            getproductdetail();
            getshipaddress();


            $("#loading-overlay").css('display', 'none');

        }
    }
});

// Configuration for the observer (observe attribute changes)
var config = { attributes: true };
if (targetNodeQute)
    observer.observe(targetNodeQute, config);

// Create a MutationObserver to detect changes to the finance first_level_name1
var targetNodeflm1 = document.getElementById("first_level_name1");
var observer1 = new MutationObserver(function (mutationsList1) {
    for (var mutation1 of mutationsList1) {
        if (mutation1.type === "attributes" && mutation1.attributeName === "value") {
            console.log("FLM value changed to:", targetNodeflm1.value);
            $("#loading-overlay").css('display', 'grid');
            //get products from product detail
            getcontactdetail(targetNodeflm1.value, $("#first_level_email"), $("#first_level_designation"), $("#first_level_number"))


            $("#loading-overlay").css('display', 'none');

        }
    }
});

// Configuration for the observer (observe attribute changes)
var config1 = { attributes: true };
if (targetNodeflm1)
    observer1.observe(targetNodeflm1, config1);

// Create a MutationObserver to detect changes to the finance second_level_name
var targetNodeslm2 = document.getElementById("second_level_name1");
var observer2 = new MutationObserver(function (mutationsList2) {
    for (var mutation2 of mutationsList2) {
        if (mutation2.type === "attributes" && mutation2.attributeName === "value") {
            console.log("FLM value changed to:", targetNodeslm2.value);
            $("#loading-overlay").css('display', 'grid');
            //get products from product detail
            getcontactdetail(targetNodeslm2.value, $("#second_level_email"), $("#second_level_designation"), $("#second_level_number"))


            $("#loading-overlay").css('display', 'none');

        }
    }
});

// Configuration for the observer (observe attribute changes)
var config2 = { attributes: true };
if (targetNodeslm2)
    observer2.observe(targetNodeslm2, config2);

///////////warehouse escalation start////////////
// Create a MutationObserver to detect changes to the warehouse first_level_name1
var targetNodeflmwh1 = document.getElementById("wh_first_level_name1");
var observerwh1 = new MutationObserver(function (mutationsListwh1) {
    for (var mutationwh1 of mutationsListwh1) {
        if (mutationwh1.type === "attributes" && mutationwh1.attributeName === "value") {
            console.log("FLM value changed to:", targetNodeflmwh1.value);
            $("#loading-overlay").css('display', 'grid');
            //get products from product detail
            getcontactdetail(targetNodeflmwh1.value, $("#wh_first_level_email"), $("#wh_first_level_designation"), $("#wh_first_level_number"))


            $("#loading-overlay").css('display', 'none');

        }
    }
});

// Configuration for the observer (observe attribute changes)
var configwh1 = { attributes: true };
if (targetNodeflmwh1)
    observerwh1.observe(targetNodeflmwh1, configwh1);

// Create a MutationObserver to detect changes to the warehouse second_level_name
var targetNodeslmwh2 = document.getElementById("wh_second_level_name1");
var observerwh2 = new MutationObserver(function (mutationsListwh2) {
    for (var mutationwh2 of mutationsListwh2) {
        if (mutationwh2.type === "attributes" && mutationwh2.attributeName === "value") {
            console.log("FLM value changed to:", targetNodeslmwh2.value);
            $("#loading-overlay").css('display', 'grid');
            //get products from product detail
            getcontactdetail(targetNodeslmwh2.value, $("#wh_second_level_email"), $("#wh_second_level_designation"), $("#wh_second_level_number"))


            $("#loading-overlay").css('display', 'none');

        }
    }
});

// Configuration for the observer (observe attribute changes)
var configwh2 = { attributes: true };
if (targetNodeslmwh2)
    observerwh2.observe(targetNodeslmwh2, configwh2);

///////////warehouse escalation end//////////////

///////////procurement escalation start////////////
// Create a MutationObserver to detect changes to the procurement first_level_name1
var targetNodeflmpro1 = document.getElementById("pro_first_level_name1");
var observerpro1 = new MutationObserver(function (mutationsListpro1) {
    for (var mutationpro1 of mutationsListpro1) {
        if (mutationpro1.type === "attributes" && mutationpro1.attributeName === "value") {
            console.log("FLM value changed to:", targetNodeflmpro1.value);
            $("#loading-overlay").css('display', 'grid');
            //get products from product detail
            getcontactdetail(targetNodeflmpro1.value, $("#pro_first_level_email"), $("#pro_first_level_designation"), $("#pro_first_level_number"))


            $("#loading-overlay").css('display', 'none');

        }
    }
});

// Configuration for the observer (observe attribute changes)
var configpro1 = { attributes: true };
if (targetNodeflmpro1)
    observerpro1.observe(targetNodeflmpro1, configpro1);

// Create a MutationObserver to detect changes to the procurement second_level_name
var targetNodeslmpro2 = document.getElementById("pro_second_level_name1");
var observerpro2 = new MutationObserver(function (mutationsListpro2) {
    for (var mutationpro2 of mutationsListpro2) {
        if (mutationpro2.type === "attributes" && mutationpro2.attributeName === "value") {
            console.log("FLM value changed to:", targetNodeslmpro2.value);
            $("#loading-overlay").css('display', 'grid');
            //get products from product detail
            getcontactdetail(targetNodeslmpro2.value, $("#pro_second_level_email"), $("#pro_second_level_designation"), $("#pro_second_level_number"))


            $("#loading-overlay").css('display', 'none');

        }
    }
});

// Configuration for the observer (observe attribute changes)
var configpro2 = { attributes: true };
if (targetNodeslmpro2)
    observerpro2.observe(targetNodeslmpro2, configpro2);

///////////procurement escalation end//////////////



// ///////////set default stage//////////////
document.addEventListener("DOMContentLoaded", function () {
    // Check if mode is 'Create'
    const modeInput = document.getElementById("mode");
    if (modeInput && modeInput.value === "Create") {

        //set today date
        // Get today's date in YYYY-MM-DD format
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, "0");
        var mm = String(today.getMonth() + 1).padStart(2, "0"); // Months are zero-indexed
        var yyyy = today.getFullYear();

        var todaydate = dd + "-" + mm + "-" + yyyy; // Format the date as YYYY-MM-DD
        // alert('"'+todaydate+'"');

        $("#quote_create_date").val(todaydate);
        ///////////15 days + creation date = expiry date

        setTimeout(() => {
            flatpickr("#quote_create_date", {
                defaultDate: new Date(),
                dateFormat: "d-m-Y",
            });

            //getexpirydate();
        }, 500); // Waits for 600 milliseconds (1/2 seconds)

        // $("#expiry_date").val(expiry_date);

        // getvendoraccount();
    }
});


//////////////get bill to address///////////
async function getbilladdress() {
    const data = {
        deal_name: $("#deal_name1").val(),
        _csrf: $("#csrfToken").val(),
    };

    try {
        const response = await $.ajax({
            type: "POST",
            url: "getbilladdress",
            data: data,
            dataType: "json",
        });

        // console.log(response); // Log the entire response to check its structure

        if (response && response.data) {
            $("#loading-overlay").css('display', 'grid');

            // $(`#warehouse_loc_business_entity1`).val(response.data.warehouse_loc_business_entity);
            // $(`#warehouse_loc_business_entity`).val(response.data.warehouse_name);
            // $(`#bill_from_location1`).val(response.data.bill_from_location);
            // $(`#bill_from_location`).val(response.data.bill_from_location_name);
            $(`#city`).val(response.data.city_name);
            $(`#pin_code`).val(response.data.pincode);
            $(`#bill_to_legal_name`).val(response.data.legal_entity);
            $(`#delivery_location`).val(response.data.bill_location_name);
            $(`#delivery_location1`).val(response.data.bill_location);
            $(`#address`).val(response.data.bill_address);
            $(`#state`).val(response.data.bill_state);
            $(`#state_code`).val(response.data.bill_state_code);
            $(`#gst`).val(response.data.bill_gstin_no);
            $(`#pan`).val(response.data.pan_number);

            $(`#account_name1`).val(response.data.vendor_account_name);
            $(`#account_name`).val(response.data.vendor);
            // $(`#region`).val(response.data.zone_region).trigger("change");
            $(`#team`).val(response.data.team_name).trigger("change");
            $(`#requester_name_contact_name1`).val(response.data.requester_customer_name);
            $(`#requester_name_contact_name`).val(response.data.contact);
            $(`#gross_profit`).val(response.data.gross_profit);
            // $(`#margin_percentage`).val(parseFloat(response.data.margin_percentage).toFixed(2));
            $(`#customer_po_num`).val(response.data.customer_po_num);
            $(`#customer_payment_terms`).val(response.data.customer_payment_terms).trigger('change');
            $(`#customer_po_date`).val(response.data.customer_po_date);
            var fp = document.querySelector("#customer_po_date")._flatpickr;
            // flatpickr("#customer_po_date", {
            //     onReady: function(selectedDates, dateStr, instance) {
            //         instance.setDate(response.data.customer_po_date);
            //     }
            //     });
            setTimeout(() => {
                flatpickr("#customer_po_date", {
                    defaultDate: response.data.customer_po_date,
                    dateFormat: "d-m-Y"
                });
                 flatpickr("#po_received_date", {
                    defaultDate: response.data.po_received_date,
                    dateFormat: "d-m-Y"
                });
            }, 500);
            // $(`#po_received_date`).val(response.data.po_received_date);


            $("#loading-overlay").css('display', 'none');

            // After all rows are processed, update the total amount after a delay
            // setTimeout(() => {
            // setTotalAmount();
            // }, 5000);
        } else {
            console.error("Invalid response format or missing data");
        }
    } catch (error) {
        console.error("Error occurred while fetching product details:", error);
        alert("Error occurred. Please try again.");
    }
}
//////////////get ship address///////////
async function getshipaddress() {
    const data = {
        quote_name: $("#quote_name1").val(),
        _csrf: $("#csrfToken").val(),
    };

    try {
        const response = await $.ajax({
            type: "POST",
            url: "getshipaddress",
            data: data,
            dataType: "json",
        });

        // console.log(response); // Log the entire response to check its structure

        if (response && response.data) {
            $('#productTable2696 tbody').html('');

            $("#loading-overlay").css('display', 'grid');

            // Initialize currentRow to keep track of the last appended row
            let currentRow = '';

            // Loop through each product in the response
            for (let i = 0; i < response.data.length; i++) {
                const j = i + 1;
                const res = response.data[i];

                // Wait for the row to be added before proceeding with updates
                await addRowBtn("2696", "salesorderdit");

                // Find the last row and get its index
                const tbody = $('#productTable' + 2696 + ' tbody');
                const lastRow = tbody.find('tr:last');
                const rowIndex = lastRow.index();

                // Check if this row is new and not already updated
                if (lastRow.length > 0 && currentRow !== rowIndex) {
                    console.log(`Processing row ${j} (Row Index: ${rowIndex})`);

                    // Find the product_name input element
                    const productNameInput = lastRow.find(`#ship_delivery_location_${j}`);

                    if (productNameInput.length > 0) {
                        // Update the input values for the row

                        lastRow.find(`#ship_delivery_location_${j}1`).val(res.ship_to_location);
                        lastRow.find(`#ship_delivery_location_${j}`).val(res.vendor_loc_name);
                        lastRow.find(`#ship_address_${j}`).val(res.ship_address);
                        lastRow.find(`#ship_city_${j}`).val(res.city_name);
                        lastRow.find(`#ship_state_${j}`).val(res.ship_state);
                        lastRow.find(`#ship_pin_code_${j}`).val(res.pincode);
                        lastRow.find(`#ship_state_code_${j}`).val(res.ship_state_code);
                        lastRow.find(`#ship_gst_${j}`).val(res.ship_gst);
                        lastRow.find(`#ship_pan_${j}`).val(res.pan);
                        // $(".remove-row-btn").addClass("tr-hidden");



                        // Update the currentRow index after appending and updating
                        currentRow = rowIndex;
                    } else {
                        // console.error(`Ship Location input not found for ID: #ship_to_location_${j}`);
                        console.error("error fetching shiping adresses");
                    }
                }
            }
            $(".savebutton").prop("disabled", false);
            $("#loading-overlay").css('display', 'none');

            // After all rows are processed, update the total amount after a delay
            // setTimeout(() => {
            // setTotalAmount();
            // }, 5000);
        } else {
            console.error("Invalid response format or missing data");
        }
    } catch (error) {
        console.error("Error occurred while fetching product details:", error);
        alert("Error occurred. Please try again.");
    }
}

/////////////////////////get product detail////////////
async function getproductdetail() {
    console.log('getProductDetails', '490')
    const data = {
        quote_name: $("#quote_name1").val(),
        _csrf: $("#csrfToken").val(),
    };

    try {
        const response = await $.ajax({
            type: "POST",
            url: "getproductdetail",
            data: data,
            dataType: "json",
        });

        console.log(response.data); // Log the entire response to check its structure

        if (response && response.data) {
            $('#productTable2697 tbody').html('');

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
                await addRowBtn("2697", "salesorderdit");

                // Find the last row and get its index
                const tbody = $('#productTable' + 2697 + ' tbody');
                const lastRow = tbody.find('tr:last');
                const rowIndex = lastRow.index();

                // Check if this row is new and not already updated
                if (lastRow.length > 0 && currentRow !== rowIndex) {
                    console.log(`Processing row ${j} (Row Index: ${rowIndex})`);

                    // Find the product_name input element
                    const productNameInput = lastRow.find(`#product_name_${j}`);
                    console.log('edit')
                    console.log(res, 'res');
                    if (productNameInput.length > 0) {
                        // Update the input values for the row
                        productNameInput.val(res.product_name);
                        lastRow.find(`#product_name_${j}1`).val(res.product_name);
                        lastRow.find(`#product_name_${j}`).val(res.prod_name);

                        //auto fill product_description and oem part no as per V11 - point no 36 code added by ptptale on date 11-10-2025

                        lastRow.find(`#product_description_${j}`).val(res.prod_description);
                        lastRow.find(`#oem_part_number_${j}`).val(res.prod_oem_part_number);

                        //auto fill product_description and oem part no as per V11 - point no 36 code end added by ptptale on date 11-10-2025


                        // lastRow.find(`#category_${j}`).val(res.category);
                        lastRow.find(`#hsn_code_${j}`).val(res.hsn_code);
                        lastRow.find(`#quotesdit_qty_${j}`).val(res.quotesdit_qty);
                        lastRow.find(`#remaining_qty_${j}`).val(res.remaining_qty);
                        lastRow.find(`#qty_${j}`).val(res.qty);
                        // lastRow.find(`#uom_${j}`).val(res.uom);
                        lastRow.find(`#cgst_per_${j}`).val(res.cgst_per);
                        lastRow.find(`#sgst_per_${j}`).val(res.sgst_per);
                        lastRow.find(`#igst_per_${j}`).val(res.igst_per);
                        lastRow.find(`#basic_price_${j}`).val(res.basic_price);
                        var amnt = res.sales_price * res.quantity;
                        lastRow.find(`#amount_${j}`).val(res.amount);
                        // lastRow.find(`.add_product_delivery_timeline`).val(res.add_product_delivery_timeline);
                        // lastRow.find(`.add_price_validity`).val(res.add_price_validity);
                        lastRow.find(`#add_price_validity_${j}`).val(res.add_price_validity);
                        lastRow.find(`#add_product_delivery_timeline_${j}`).val(res.add_product_delivery_timeline);

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
            var totalgst = total_cgst + total_sgst + total_igst;
            total = subtotal + totalgst;
            var total_price_words = numberToWords(total);




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

//////////////get total amount///////////
async function gettotalamount() {
    const data = {
        quote_name: $("#quote_name1").val(),
        _csrf: $("#csrfToken").val(),
    };

    try {
        const response = await $.ajax({
            type: "POST",
            url: "gettotalamount",
            data: data,
            dataType: "json",
        });

        // console.log(response); // Log the entire response to check its structure

        if (response && response.data) {


            $(`#cgst_amount`).val(response.data.cgst_amount);
            $(`#sgst_amount`).val(response.data.sgst_amount);
            $(`#igst_amount`).val(response.data.igst_amount);
            $(`#basic_amount`).val(response.data.sub_total);
            $(`#grand_total`).val(response.data.grand_total);
            $(`#amount_in_words`).val(response.data.amount_in_words);
            $(`#margin_percentage`).val(response.data.margin);


            $("#loading-overlay").css('display', 'none');


        } else {
            $("#loading-overlay").css('display', 'none');
            console.error("Invalid response format or missing data");
        }
    } catch (error) {
        console.error("Error occurred while fetching total amount:", error);
        alert("Error occurred. Please try again.");
    }
}
//////////////get total amount///////////
async function getcontactdetail(contacts_id, email_id, designations_id, mobile_id) {
    const data = {
        contacts_id: contacts_id,
        _csrf: $("#csrfToken").val(),
    };

    try {
        const response = await $.ajax({
            type: "POST",
            url: "getcontacts",
            data: data,
            dataType: "json",
        });

        // console.log(response); // Log the entire response to check its structure

        if (response && response.data) {


            email_id.val(response.data.email);
            designations_id.val(response.data.designation);
            mobile_id.val(response.data.mobile);


            $("#loading-overlay").css('display', 'none');


        } else {
            $("#loading-overlay").css('display', 'none');
            console.error("Invalid response format or missing data");
        }
    } catch (error) {
        console.error("Error occurred while fetching contacts", error);
        alert("Error occurred. Please try again.");
    }
}

////////hide add more button//////////
$(".add-more-records").addClass("tr-hidden");


///////validate for products/////////////
$(document).ready(function () {
    // Trigger validation when the button is clicked
    $(".savebutton").click(function () {
        //alert("Sdfds");
        // Iterate through each product_name input and check if it's empty
        var isValid = true;
        if ($(".product_name").length > 0) {
            $(".product_name").each(function () {

                if ($(this).val() === "") {
                    isValid = false;
                    $(this).addClass("error"); // Add error class if blank
                } else {
                    $(this).removeClass("error"); // Remove error class if filled
                }
            });
        }
        else {
            isValid = false;
        }

        if (isValid) {
            $(".savebutton").prop("disabled", false);
            // If all inputs are valid, you can submit the form or perform some action
            // alert("Form is valid, proceed with submission!");
            // Example: $("#productForm").submit();
        } else {
            $(".savebutton").prop("disabled", true);
            // If any input is invalid, alert the user
            // alert("Products can't be blank");
        }
    });
});
// Function to convert numbers to words
function numberToWords(num) {
    const a = [
        "",
        "One",
        "Two",
        "Three",
        "Four",
        "Five",
        "Six",
        "Seven",
        "Eight",
        "Nine",
        "Ten",
        "Eleven",
        "Twelve",
        "Thirteen",
        "Fourteen",
        "Fifteen",
        "Sixteen",
        "Seventeen",
        "Eighteen",
        "Nineteen",
    ];
    const b = [
        "",
        "",
        "Twenty",
        "Thirty",
        "Forty",
        "Fifty",
        "Sixty",
        "Seventy",
        "Eighty",
        "Ninety",
    ];
    const c = ["Hundred", "Thousand", "Lakh", "Crore"];

    if (num === 0) return "zero";

    let words = "";

    if (num >= 10000000) {
        words += numberToWords(Math.floor(num / 10000000)) + " crore ";
        num %= 10000000;
    }
    if (num >= 100000) {
        words += numberToWords(Math.floor(num / 100000)) + " lakh ";
        num %= 100000;
    }
    if (num >= 1000) {
        words += numberToWords(Math.floor(num / 1000)) + " thousand ";
        num %= 1000;
    }
    if (num >= 100) {
        words += numberToWords(Math.floor(num / 100)) + " hundred ";
        num %= 100;
    }
    if (num > 0) {
        if (num < 20) {
            words += a[num];
        } else {
            words += b[Math.floor(num / 10)];
            if (num % 10 > 0) words += " " + a[num % 10];
        }
    }

    return words.trim();
}

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
        url: "approvesalesorderdit",
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
            url: "approvesalesorderdit",
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

/////////////Questions PArt/////////////
$(".section-timeline_commited_date").addClass("tr-hidden");
$(".section-case_scattered_delivery").addClass("tr-hidden");
$(".section-case_scattered_delivery_files").addClass("tr-hidden");
$(".section-additional_service_offered").addClass("tr-hidden");
$(".section-free_chargeable_offered_services").addClass("tr-hidden");
$(".section-scope_work_installation").addClass("tr-hidden");
$(".section-scope_work_installation_doc").addClass("tr-hidden");
$(".section-scope_work_installation_docestimate_date_delivery").addClass("tr-hidden");
$(".section-estimate_date_delivery").addClass("tr-hidden");
$(".section-actual_date_delivery").addClass("tr-hidden");
// $(".section-actual_date_delivery").addClass("tr-hidden");

$("#timeline_commited").change(function () {
    var timeline_commited = $('#timeline_commited').val();
    if (timeline_commited == 1) {
        //next question
        $(".section-timeline_commited_date").removeClass("tr-hidden");
        $(".section-case_scattered_delivery").addClass("tr-hidden");

        $("#timeline_commited_date").removeClass("DT~O").addClass("DT~M");
    }
    else {
        $(".section-case_scattered_delivery").removeClass("tr-hidden");
        $(".section-timeline_commited_date").addClass("tr-hidden");

        $("#timeline_commited_date").removeClass("DT~M").addClass("DT~O");
        $("#timeline_commited_date").val("");
        var helpBlock = $("#timeline_commited_date").closest(".form-group").find(".help-block");
        if (helpBlock.length) {
            helpBlock.html('');
        }

    }

});
$("#case_scattered_delivery").change(function () {
    var case_scattered_delivery = $('#case_scattered_delivery').val();
    if (case_scattered_delivery == 1) {
        //next question
        $(".section-case_scattered_delivery_files").removeClass("tr-hidden");
        $(".section-additional_service_offered").addClass("tr-hidden");

        $("#case_scattered_delivery_files").removeClass("F~O").addClass("F~M");

    }
    else {
        $(".section-case_scattered_delivery_files").addClass("tr-hidden");
        $(".section-additional_service_offered").removeClass("tr-hidden");
        $("#case_scattered_delivery_files").removeClass("F~M").addClass("F~O");
        $("#case_scattered_delivery_files").val("");
        var helpBlock = $("#case_scattered_delivery_files").closest(".form-group").find(".help-block");
        if (helpBlock.length) {
            helpBlock.html('');
        }
    }

});

$("#additional_service_offered").change(function () {
    var additional_service_offered = $('#additional_service_offered').val();
    if (additional_service_offered == 1) {
        //next question
        $(".section-free_chargeable_offered_services").removeClass("tr-hidden");
        $(".section-scope_work_installation").addClass("tr-hidden");
        $("#free_chargeable_offered_services").removeClass("DD~O").addClass("DD~M");

    }
    else {
        $(".section-scope_work_installation").removeClass("tr-hidden");
        $(".section-free_chargeable_offered_services").addClass("tr-hidden");
        $("#free_chargeable_offered_services").val("").trigger("change");
        $("#free_chargeable_offered_services").removeClass("DD~M").addClass("DD~O");
        var helpBlock = $("#free_chargeable_offered_services").closest(".form-group").find(".help-block");
        if (helpBlock.length) {
            helpBlock.html('');
        }
    }

});

$("#scope_work_installation").change(function () {
    var scope_work_installation = $('#scope_work_installation').val();
    if (scope_work_installation == 1) {
        //next question
        $(".section-scope_work_installation_doc").removeClass("tr-hidden");
        $("#scope_work_installation_doc").removeClass("F~O").addClass("F~M");
        $(".section-estimate_date_delivery").addClass("tr-hidden");
        $(".section-actual_date_delivery").addClass("tr-hidden");
        // $(".section-actual_date_delivery").removeClass("tr-hidden");
        $("#estimate_date_delivery").val("");
        $("#actual_date_delivery").val("");

    }
    else {
        $(".section-estimate_date_delivery").removeClass("tr-hidden");
        $(".section-actual_date_delivery").removeClass("tr-hidden");
        $(".section-scope_work_installation_doc").addClass("tr-hidden");

        $("#scope_work_installation_doc").removeClass("F~M").addClass("F~O");
        $("#scope_work_installation_doc").val("");
        var helpBlock = $("#scope_work_installation_doc").closest(".form-group").find(".help-block");
        if (helpBlock.length) {
            helpBlock.html('');
        }
    }

});
//auto fill deal name as per V11 - point no 35 code added by ptptale on date 11-10-2025
async function getdealname() {
    const dealdata = {
        dealid: $("#deal_name1").val(),
        _csrf: $("#csrfToken").val(),
    };

    try {
        const response = await $.ajax({
            type: "POST",
            url: "getdealname",
            data: dealdata,
            dataType: "json",
        });

        // console.log(response); // Log the entire response to check its structure

        if (response && response.data) {
            $("#loading-overlay").css('display', 'grid');
            $(`#deal_name_auto`).val(response.data.deal_name_auto);
            $("#loading-overlay").css('display', 'none');

            var fp = document.querySelector("#customer_po_date")._flatpickr;
            flatpickr("#customer_po_date", {
                onReady: function (selectedDates, dateStr, instance) {
                    instance.setDate(response.data.customer_po_date);
                }
            });
            $(`#po_received_date`).val(response.data.po_received_date);

        } else {
            console.error("Invalid response format or missing data");
        }
    } catch (error) {
        console.error("Error occurred while fetching dealname:", error);
        alert("Error occurred. Please try again.");
    }
}
//auto fill deal name as per V11 - point no 35 code added by ptptale on date 11-10-2025
/**
 * when qty change by user need to calculate total for particuler SO Total sections amount
 * code added on date 16-10-2025 by ptpatel
 */
$(document).on(
    "change",
    "[id^=qty_]",
    function () {
         recalculatetotalsectionamounts();
    });
function recalculatetotalsectionamounts()
{
    console.log("recalculatetotalsectionamounts called");
   var subtotal = 0;
    var total = 0;
    var grandtotal = 0;
    var cgstamout = 0;
    var sgstamout = 0;
    var igstamout = 0;
    $("[class^=qty]").each(function () {
        console.log("in qty each");
        var suffix = $(this).attr("id").match(/\d+$/)
            ? $(this).attr("id").match(/\d+$/)[0]
            : "";
        var basic_price = parseFloat($(`#basic_price_${suffix}`).val()) || 0;
        var qty = parseFloat($(`#qty_${suffix}`).val()) || 0;

        var modeso= document.getElementById("mode");
        // if(modeso !== "Create")
        // {
        //     var cgst = parseFloat($(`#cgst_${suffix}`).val()) || 0;
        //     var sgst = parseFloat($(`#sgst_${suffix}`).val()) || 0;
        //     var igst = parseFloat($(`#igst_${suffix}`).val()) || 0;

        //     gst = cgst+sgst+igst;
        //     $(`#gst_${suffix}`).val(gst);
        // }

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
       
        // if (bill_state_code == delivery_state_code) 
        // {
        //     cgst = gst / 2;
        //     $(`#cgst_${suffix}`).val(cgst);
        //     sgst = gst / 2;
        //     $(`#sgst_${suffix}`).val(sgst);
        // }
        // else{
        //     $(`#igst_${suffix}`).val(gst);
            
        // }

        // alert(basic_cost_price);
        var cgst = parseFloat($(`#cgst_per_${suffix}`).val()) || 0;
        var sgst = parseFloat($(`#sgst_per_${suffix}`).val()) || 0;
        var igst = parseFloat($(`#igst_per_${suffix}`).val()) || 0;
        var midtotal = basic_price * qty;
        total += basic_price * qty;
        //  alert(basic_price+" "+qty+" "+total);
        subtotal += (basic_price * qty);
        cgst = cgst * midtotal / 100;
        sgst = sgst * midtotal / 100;
        igst = igst * midtotal / 100;
        grandtotal += (basic_price*qty) + cgst + sgst + igst;
        cgstamout += cgst;
        sgstamout += sgst;
        igstamout += igst;
        grandtotal = subtotal + cgstamout + sgstamout + igstamout;
        $("#basic_amount").val(total.toFixed(2));
        $("#cgst_amount").val(cgstamout.toFixed(2));
        $("#sgst_amount").val(sgstamout.toFixed(2));
        $("#igst_amount").val(igstamout.toFixed(2));
        $("#grand_total").val(grandtotal.toFixed(2));
        calculateRowPrice(suffix);
    });

    function calculateRowPrice(trid) {
    console.log("calculateRowPrice"+trid);
    let cp = $("#basic_price_" + trid).val() || 0;
    let quantity_required = $("#qty_" + trid).val() || 0;
    let cgst = $("#cgst_per_" + trid).val() || 0;
    let sgst = $("#sgst_per_" + trid).val() || 0;
    let igst = $("#igst_per_" + trid).val() || 0;
    let totalcost = cp * quantity_required;
    let cgstamout = (cgst / 100) * totalcost;
    let sgstamout = (sgst / 100) * totalcost;
    let igstamout = (igst / 100) * totalcost;
    let totalcostwithprice = totalcost + cgstamout + sgstamout + igstamout;
    console.log(cp+" "+quantity_required+" "+totalcost);
    // $("#amount_" + trid).val(totalcostwithprice);
    $("#amount_" + trid).val(totalcost);
    // calctotalamt();
}
}






