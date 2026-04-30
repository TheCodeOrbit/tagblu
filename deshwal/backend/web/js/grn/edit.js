$(document).ready(function () {
  var newURL = window.location.href;
  var newURL = window.location.href;
  var module = "grn";
  var str = newURL.split(module);
  console.log("str" + str[0]);
  // var slicestr=newURL.substring(0,str);
  editusrl = str[0] + "grn/list";
  console.log("url" + editusrl);
    function calculate_physical_quantity() {
        var total_physical_quantity = 0;
        $(".physical_quantity").each(function () {
            var value = parseFloat($(this).val());
            if (!isNaN(value) && value >= 0) {
                total_physical_quantity += value;
            }
        });
        if (total_physical_quantity >= 0) {
            $("#total_quantity").val(total_physical_quantity);
        } else {
            $("#total_quantity").val(0);
        }
        calculate_total_purchase_value()
    }
    function calculate_purchase_order_quantity() {
        var total_purchase_order_quantity = 0;
        $(".purchase_order_quantity").each(function () {
            var value = parseFloat($(this).val());
            if (!isNaN(value) && value >= 0) {
                total_purchase_order_quantity += value;
            }
        });
        if (total_purchase_order_quantity >= 0) {
            $("#total_po_quantity").val(total_purchase_order_quantity);
        } else {
            $("#total_po_quantity").val(0);
        }
    }
    
    function calculate_total_purchase_value() {
        var item_value = 0;
        $(".item_value").each(function () {
            var value = parseFloat($(this).val());
            if (!isNaN(value) && value >= 0) {
                item_value += value;
            }
        });
        if (item_value >= 0) {
            $("#total_purchase_value").val(item_value);
        } else {
            $("#total_purchase_value").val(0);
        }
    }

    function makeSelect2Readonly($select) {
        $select.on('select2:opening select2:selecting', function(e) {
            e.preventDefault();
        });
    }
    
    async function getpickupdata() {
        let data = { record_id: $("#pickup_id1").val(), _csrf: $('#csrfToken').val() };
        try {
            $("#loading-overlay").css('display', 'grid');
            let response = await $.ajax({
                type: 'POST',
                url: "getpickupdata",
                data: data,
                dataType: 'json'
            });
            if (response && response.data) {
                let master = response.data.master || null;
                let shipped_details = response.data.shipped_details || null;
                let documents_fe = response.data.document_details || null;
                let product_details = response.data.product_details || null;
                if (master) {
                    $(".remove-row-btn").each(function () {
                        $(this).trigger("click");
                    });
                    $("#account_name1").val(master.account_id);
                    $("#account_name").val(master.account_name);

                    $("#location1").val(master.location_id);
                    $("#location").val(master.location);

                    $("#fe_name1").val(master.fe_id);
                    $("#fe_name").val(master.fe_name);

                    $("#cs_spoc1").val(master.cs_spoc_id);
                    $("#cs_spoc").val(master.cs_spoc);

                    $("#logistics_user1").val(master.logistics_user_id);
                    $("#logistics_user").val(master.logistics_user);
                    if (shipped_details && Array.isArray(shipped_details)) {
                        for (const item of shipped_details) {
                            await addDynamicRows(item,75);
                        }
                    }
                    if (documents_fe && Array.isArray(documents_fe)) {
                        for (const item of documents_fe) {
                            await addDynamicRows(item,2631);
                        }
                    }
                    if (product_details && Array.isArray(product_details)) {
                        for (const item of product_details) {
                            await addDynamicRows(item,2632);
                        }
                    }
                }
                $("#loading-overlay").css('display', 'none');
            } else {
                $("#loading-overlay").css('display', 'none');
                console.log("Invalid response format or missing data");
            }
        } catch (error) {
            $("#loading-overlay").css('display', 'none');
            alert('Error occurred. Please try again.');
        }
    }

    async function addDynamicRows(item,blockid) {
        return new Promise((resolve, reject) => {
            let mainmodule = "grn";
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
                    if (blockid == 75) {
                        lastRow.find('.transporter_name').val(item.transporter_name_value);
                        lastRow.find('[name*="[transporter_name]"]').val(item.transporter_name);//name
                        lastRow.find('[name*="[vehicle_size]"]').val(item.vehicle_size || "").trigger("change");
                        lastRow.find('[name*="[shippment_mode]"]').val(item.shippment_mode).trigger("change"); //name
                        lastRow.find('.docket_number').val(item.docket_number);
                        lastRow.find('.seal_number').val(item.seal_number);
                        lastRow.find('.vehicle_number').val(item.vehicle_number);

                        lastRow.find('.shipped_date').val(item.shipped_date);
                        lastRow.find('.estimate_delivery_date').val(item.estimate_delivery_date);
                        lastRow.find('.delivery_date').val(item.delivery_date);
                        lastRow.find('[name*="[status]"]').val(item.status).trigger("change")//name;
                        let selectors = [
                            'select[name*="vehicle_size"]',
                            'select[name*="shippment_mode"]',
                            'select[name*="status"]'
                        ];
                        selectors.forEach(selector => {
                            lastRow.find(selector).on('select2:opening select2:selecting', function (e) {
                                e.preventDefault();
                            });
                        });
                    } else if (blockid == 2631) {
                        lastRow.find('[name*="[document_for_pickup]"]').val(item.document_for_pickup).trigger("change");//name
                        lastRow.find('[name*="[document_attached]"]').val(item.document_attached);
                        let input = lastRow.find('.attachment');
                        input.attr("type", "text").val(item.attachment).removeClass('F~O').removeClass('F~M').attr("disabled",false).hide();
                        input.next('.attachment-link').remove();

                        input.next('.upd-file').remove();
                        if (item.attachment) {
                            let fileUrl = '/deshwal/admin/grn/download?fileid=' + item.attachment;
                            let link = $('<a>')
                                .attr('href', fileUrl)
                                .attr('target', '_blank')
                                .addClass('attachment-link')
                                .text(' View');
                            let container = $('<div>')
                                .addClass('upd-file')
                                .append(link);
                            input.after(container);
                        }
                        let selectors = [
                            'select[name*="document_for_pickup"]',
                            'select[name*="document_attached"]'
                        ];
                        selectors.forEach(selector => {
                            lastRow.find(selector).on('select2:opening select2:selecting', function (e) {
                                e.preventDefault();
                            });
                        });
                    } else if (blockid == 2632) {
                        //lastRow.find('.porduct_name').val(item.porduct_name);
                        lastRow.find('[name*="[porduct_name]"]').val(item.porduct_name || ""); // hidden field
                        lastRow.find('.porduct_name').val(item.product_name_grn || ""); // visible field
                        lastRow.find('.category').val(item.category);
                        lastRow.find('.sub_category').val(item.sub_category);
                        lastRow.find('.model_no').val(item.model_no);
                        lastRow.find('.make').val(item.make);
                        lastRow.find('[name*="[all_accessories]"]').val(item.all_accessories).trigger("change");//name
                        lastRow.find('.hsn_code').val(item.hsn_code);
                        lastRow.find('.quoted_price_gst_include').val(item.quoted_price_gst_include);
                        lastRow.find('.quoted_price_gst_exclude').val(item.quoted_price_gst_exclude);
                        lastRow.find('.quantity_quoted').val(item.quantity_quoted);
                        lastRow.find('.total_quantity').val(item.total_quantity);
                        lastRow.find('.uom').val(item.uom);
                        lastRow.find('.cgst').val(item.cgst);
                        lastRow.find('.sgst').val(item.sgst);
                        lastRow.find('.igst').val(item.igst);
                        lastRow.find('.cgst_amount').val(item.cgst_amount);
                        lastRow.find('.sgst_amount').val(item.sgst_amount);
                        lastRow.find('.igst_amount').val(item.igst_amount);
                        lastRow.find('.total_quoted_price_gst_include').val(item.total_quoted_price_gst_include);
                        lastRow.find('.total_quoted_price_gst_exclude').val(item.total_quoted_price_gst_exclude);
                        lastRow.find('.pickup_qty').val(item.pickup_qty);
                        lastRow.find('.picked_qty').val(item.picked_qty);
                        lastRow.find('.difference').val(item.difference);
                        let selectors = [
                            'select[name*="all_accessories"]'
                        ];
                        selectors.forEach(selector => {
                            lastRow.find(selector).on('select2:opening select2:selecting', function (e) {
                                e.preventDefault();
                            });
                        });
                    }
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

    $(document).on("change", ".received_qty", function () {
        var rcv_value = parseFloat($(this).val()) || 0;
        var currentRow = $(this).closest('tr');
        var picked_qty = parseFloat(currentRow.find('.picked_qty').val()) || 0;
        var variance = rcv_value - picked_qty;
        currentRow.find('.received_variance').val(variance.toFixed(2));
        
    })
    // $(document).on("change", ".purchase_order_quantity", function () {
    //     calculate_purchase_order_quantity()
    // })
    // $(document).on("click", ".remove-row-btn", function () {
    //     calculate_physical_quantity()
    //     calculate_purchase_order_quantity()
    //     calculate_total_purchase_value()
    // })
    // $(document).on("change", ".unit_price_without_tax,.physical_quantity", function (e) {
    //     var $row = $(this).closest('tr.product-row');

    //     // Get the unit_price_without_tax and physical_quantity of that row
    //     var unitPriceWithoutTax = parseFloat($row.find('.unit_price_without_tax').val()) || 0;
    //     var physicalQuantity = parseFloat($row.find('.physical_quantity').val()) || 0;

    //     // Calculate item value = unit price without tax * physical qty
    //     var itemValue = unitPriceWithoutTax * physicalQuantity;

    //     // Calculate tax = 18% of item value
    //     var taxValue = itemValue * 1.18;

    //     // Update corresponding row item value and tax value
    //     $row.find('.item_value').val(itemValue.toFixed(2));
    //     $row.find('.unit_price_with_tax').val(taxValue.toFixed(2));
    //     calculate_total_purchase_value()
    // })

    $(".add-more-records").hide();
    $(".remove-row-btn").remove()
    let selects = $('select[name*="vehicle_size"],select[name*="shippment_mode"],select[name*="status"],select[name*="document_for_pickup"],select[name*="document_attached"],select[name*="all_accessories"]');

    selects.on('select2:opening select2:selecting', function(e) {
        e.preventDefault();
    });
    //get pickupdata
    // Create a MutationObserver to detect changes to the input vendor account
    var targetNode = document.getElementById('pickup_id1');
    if (targetNode.observerInstance) {
        targetNode.observerInstance.disconnect();
    }
    var observer = new MutationObserver(function (mutationsList) {
        for (var mutation of mutationsList) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
            $("#productTable75,#productTable2631,#productTable2632").find(".product-row").remove()
            getpickupdata();
            console.log('business_entity value changed to:', targetNode.value);
        }
        }
    });
    // Configuration for the observer (observe attribute changes)
    var config = { attributes: true };
    observer.observe(targetNode, config);
});