$(document).ready(function () {
    console.log('salesorder edit js loaded');

});
// document.getElementById('payment_terms').addEventListener('input', function(event) {
//     console.log(event.target.value);  
// });
// const targetNode = document.getElementById('vendor_name1');
// const observer = new MutationObserver(() => {
//     const vendorId = targetNode.value;
//     $.ajax({
//         type: "POST",
//         url: "getpaymentterm", 
//         data: {
//             vendor_id: vendorId,
//             _csrf: $("#csrfToken").val(),
//         },
//         dataType: "json",
//         success: function(response) {
//             if (response && response.data && response.data.payment_term !== undefined) {
//                 document.getElementById('payment_terms').value = response.data.payment_term;
//             } else {
//                 document.getElementById('payment_terms').value = '';
//             }
//         },
//         error: function(xhr, status, error) {
//             console.error("AJAX error:", error);
//             document.getElementById('payment_terms').value = '';
//         }
//     });
// });
// observer.observe(targetNode, { attributes: true, attributeFilter: ['value'] });
// const billLocNode = document.getElementById('bill_vendor_location1');
// const billLocObserver = new MutationObserver(() => {
//  const locationId = billLocNode.value;
//  $.ajax({
//   type: "POST",
//   url: "getbillvendorlocation",
//   data: {
//    location_id: locationId,
//    _csrf: $("#csrfToken").val()
//   },
//   dataType: "json",
//   success: function(response) {
//    var data = response.data || {};
//    document.getElementById('bill_address').value = data.address || '';
//    document.getElementById('bill_city').value = data.city || '';
//    document.getElementById('bill_state').value = data.state || '';
//    document.getElementById('bill_pincode').value = data.pincode || '';
//    document.getElementById('bill_statecode').value = data.state_code || '';
//    document.getElementById('bill_gst_number').value = data.gstin_no_uin || '';
//    document.getElementById('bill_pan_number').value = data.pan_no || '';
//   },
//   error: function(xhr, status, error) {
//    console.error("AJAX error (bill):", error);
//   }
//  });
// });
// billLocObserver.observe(billLocNode, { attributes: true, attributeFilter: ['value'] });

// const shipLocNode = document.getElementById('ship_vendor_location1');
// const shipLocObserver = new MutationObserver(() => {
//  const locationId = shipLocNode.value;
//  $.ajax({
//   type: "POST",
//   url: "getshipvendorlocation",
//   data: {
//    location_id: locationId,
//    _csrf: $("#csrfToken").val()
//   },
//   dataType: "json",
//   success: function(response) {
//    var data = response.data || {};
//    document.getElementById('ship_address').value = data.address || '';
//    document.getElementById('ship_city').value = data.city || '';
//    document.getElementById('ship_state').value = data.state || '';
//    document.getElementById('ship_pincode').value = data.pincode || '';
//    document.getElementById('ship_statecode').value = data.state_code || '';
//    document.getElementById('ship_gst_number').value = data.gstin_no_uin || '';
//    document.getElementById('ship_pan_number').value = data.pan_no || '';
//   },
//   error: function(xhr, status, error) {
//    console.error("AJAX error (ship):", error);
//   }
//  });
// });
// shipLocObserver.observe(shipLocNode, { attributes: true, attributeFilter: ['value'] });



// const billWhNode = document.getElementById('bill_wh_location1');
// const billWhObserver = new MutationObserver(() => {
//     const whId = billWhNode.value;
//     $.ajax({
//         type: "POST",
//         url: "getbillwhlocation",
//         data: {
//             warehouse_id: whId,
//             _csrf: $("#csrfToken").val()
//         },
//         dataType: "json",
//         success: function(response) {
//             const data = response.data || {};
//             document.getElementById('bill_wh_address').value = data.address || '';
//             document.getElementById('bill_wh_city').value = data.city || '';
//             document.getElementById('bill_wh_state').value = data.state || '';
//             document.getElementById('bill_wh_pincode').value = data.pincode || '';
//             document.getElementById('bill_wh_statecode').value = data.statecode || '';
//             document.getElementById('bill_wh_gst_number').value = data.gstn || '';
//             document.getElementById('bill_wh_pan_number').value = data.pan_number || '';
//         },
//         error: function(xhr, status, error) {
//             console.error("AJAX error (bill wh):", error);
//         }
//     });
// });
// billWhObserver.observe(billWhNode, { attributes: true, attributeFilter: ['value'] });


// const shipWhNode = document.getElementById('ship_wh_location1');
// const shipWhObserver = new MutationObserver(() => {
//     const whId = shipWhNode.value;
//     $.ajax({
//         type: "POST",
//         url: "getshipwhlocation",
//         data: {
//             warehouse_id: whId,
//             _csrf: $("#csrfToken").val()
//         },
//         dataType: "json",
//         success: function(response) {
//             const data = response.data || {};
//             document.getElementById('ship_wh_address').value = data.address || '';
//             document.getElementById('ship_wh_city').value = data.city || '';
//             document.getElementById('ship_wh_state').value = data.state || '';
//             document.getElementById('ship_wh_pincode').value = data.pincode || '';
//             document.getElementById('ship_wh_statecode').value = data.statecode || '';
//             document.getElementById('ship_wh_gst_number').value = data.gstn || '';
//             document.getElementById('ship_wh_pan_number').value = data.pan_number || '';
//         },
//         error: function(xhr, status, error) {
//             console.error("AJAX error (ship wh):", error);
//         }
//     });
// });
// shipWhObserver.observe(shipWhNode, { attributes: true, attributeFilter: ['value'] });


// ///Working////
// // const targetNode = document.getElementById('vendor_name1');
// // const observer = new MutationObserver(() => {
// //     console.log('Vendor ID changed:', targetNode.value);
// //     console.log('Vendor Name (visible):', document.getElementById('vendor_name').value);
// // });
// // observer.observe(targetNode, { attributes: true, attributeFilter: ['value'] });


// console.log('salesorder edit js end');


function fetchAndSet(endpoint, inputId, map) {
    const node = document.getElementById(inputId);
    const observer = new MutationObserver(() => {
        let payload = {};
        payload[map.param] = node.value;
        payload['_csrf'] = $("#csrfToken").val();
        $.ajax({
            type: "POST",
            url: endpoint,
            data: payload,
            dataType: "json",
            success: function(response) {
                const data = response.data || {};
                for(let [key, domId] of Object.entries(map.fields)) {
                    document.getElementById(domId).value = data[key] || '';
                }
            },
            error: function(xhr, status, error) {
                console.error(`AJAX error (${endpoint}):`, error);
                for(let domId of Object.values(map.fields)) {
                    document.getElementById(domId).value = '';
                }
            }
        });
    });
    observer.observe(node, { attributes: true, attributeFilter: ['value'] });
}

$(document).ready(function () {
    fetchAndSet("getpaymentterm", "vendor_name1", {
        param: "vendor_id",
        fields: { payment_term: "payment_terms" }
    });

    fetchAndSet("getbillvendorlocation", "bill_vendor_location1", {
        param: "location_id",
        fields: {
            address: "bill_address",
            city: "bill_city",
            state: "bill_state",
            pincode: "bill_pincode",
            statecode: "bill_statecode",
            gstn: "bill_gst_number",
            pan_number: "bill_pan_number"
        }
    });

    fetchAndSet("getshipvendorlocation", "ship_vendor_location1", {
        param: "location_id",
        fields: {
            address: "ship_address",
            city: "ship_city",
            state: "ship_state",
            pincode: "ship_pincode",
            statecode: "ship_statecode",
            gstn: "ship_gst_number",
            pan_number: "ship_pan_number"
        }
    });

    fetchAndSet("getbillwhlocation", "bill_wh_location1", {
        param: "warehouse_id",
        fields: {
            address: "bill_wh_address",
            city: "bill_wh_city",
            state: "bill_wh_state",
            pincode: "bill_wh_pincode",
            statecode: "bill_wh_statecode",
            gstn: "bill_wh_gst_number",
            pan_number: "bill_wh_pan_number"
        }
    });

    fetchAndSet("getshipwhlocation", "ship_wh_location1", {
        param: "warehouse_id",
        fields: {
            address: "ship_wh_address",
            city: "ship_wh_city",
            state: "ship_wh_state",
            pincode: "ship_wh_pincode",
            statecode: "ship_wh_statecode",
            gstn: "ship_wh_gst_number",
            pan_number: "ship_wh_pan_number"
        }
    });

    console.log('salesorder edit js loaded');
});
