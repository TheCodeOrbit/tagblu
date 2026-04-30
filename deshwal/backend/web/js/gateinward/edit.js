$(document).ready(function () {
    function showErrorMessage(message) {
        $(".custom-alert").remove()
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
    function showSuccessMessage(message) {
        $(".custom-alert").remove()
        var alertDiv = document.createElement('div');
        alertDiv.className = 'custom-alert alert alert-success alert-dismissible fade show';
        alertDiv.role = 'alert';
        alertDiv.innerHTML = `
            <strong>${message}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        $('#myDIV').append(alertDiv);
        setTimeout(() => {
            $(alertDiv).fadeOut(500, function () {
                $(this).remove();
            });
        }, 30000);
    }
    async function addAssetsDynamicRows(item) {
        return new Promise((resolve, reject) => {
            let blockid = 2628;
            let mainmodule = "gateinward";
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
                    lastRow.find('.transporter_name').val(item.transporter_name || "");
                    lastRow.find('.vehicle_number').val(item.vehicle_number || "");
                    lastRow.find('.account_name').val(item.account_name || "");
                    lastRow.find('.shipped_date').val(item.shipped_date || "");
                    lastRow.find('.shippment_mode').val(item.shippment_mode || "");
                    lastRow.find(".remove-row-btn").remove()
                    resolve();
                },
                error: function (data) {
                    alert("Error occurred. Please try again.");
                    reject();
                }
            });
        });
    }
    async function getPickupData(){
        var docket_number = $("#docket_number").val();
        let data = {
            Recordid: $("#Recordid").val(),
            _csrf: $("#csrfToken").val(),
            docket_number: docket_number,
        };
        $("#loading-overlay").css('display', 'grid');
        let response = await $.ajax({
            type: "POST",
            url: "pickupassets",
            data: data,
            dataType: "json",
        });
        if (response && response.status === "success" && response.data) {
            if (response.data && Array.isArray(response.data)) {
                for (const item of response.data) {
                    $("#pickup_id").val(item.pickup_no);
                    $("#pickup_id1").val(item.pickup_id);
                    await addAssetsDynamicRows(item);
                }
            }
            $("#loading-overlay").css('display', 'none');
        } else {
            $("#loading-overlay").css('display', 'none');
            showErrorMessage(response.errors || "something went wrong");
        }
    }
    var newURL = window.location.href;
    var newURL = window.location.href;
    var module = "gateinward";
    var str = newURL.split(module);
    editusrl = str[0] + "gateinward/list";
    $(".add-more-records").hide();
    $(document).on("change", "#docket_number", function () {
        $(".product-row").remove()
        var docket_number = $(this).val();
        if (!docket_number) return false;
        getPickupData()
    }) 
});