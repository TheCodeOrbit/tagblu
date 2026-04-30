// ============ SALES ORDER BULK IMPORT (TAG + SERVER FETCH) ============
$(function ($) {
    $('.add-more-records[data-blockid="2781"][data-module="salesorder"]')
        .removeAttr('onclick')
        .removeClass('add-more-records')
        .addClass('add-more-records-so');
});
if ($('.detail_bulk_upload').length) {
    // $('.detail_bulk_upload').show();  
}
const Loader = {
    show: function () {
        $('#loading-overlay').addClass('active').show();
    },
    hide: function () {
        $('#loading-overlay').removeClass('active').hide();
    }
};
var productTable2781 = $('#productTable2781');
let SO_abortImport = false;
let SO_bulkRowIndex = null;
const MAX_ALLWED = 100;
function SO_abortWithError(fieldName, value, reason, tagNumber) {
    SO_abortImport = true;
    productTable2781.find('tbody').empty();
    Loader.hide();

    $('#errRow').text((SO_bulkRowIndex + 2));
    $('#errField').text(fieldName || '');

    if (tagNumber) {
        $('#errValue').text('Tag: ' + tagNumber + (value ? ' | Value: ' + value : ''));
    } else {
        $('#errValue').text(value || '');
    }

    $('#errReason').text(reason || 'Import aborted.');
    $('#importErrorModal').modal('show');
}

$(document).on('click', '#sample-download-so', function () {
    window.location.href = 'downloadsample';
});
function SO_normalize(str) {
    if (!str) return '';
    return String(str)
        .replace(/\u00A0/g, ' ')
        .replace(/\r/g, '')
        .replace(/\t/g, ' ')
        .trim();
} 

function SO_parseCSV(text) {
    const lines = (text || '').trim().split('\n');
    if (!lines.length) return [];
    const rawHeaders = lines[0].split(',').map(h => SO_normalize(h).toLowerCase());
    console.log(rawHeaders,'rawHeaders');
    const headers = rawHeaders.map(h => {
        h = h.replace(/^"|"$/g, '').trim().toLowerCase();
        if (h === 'tag number') return 'tagnumber';
        if (h === 'qty') return 'qty';
        if (h === 'selling price') return 'selling_price';
        console.log(h,'h');
        return h;
    });
    return lines.slice(1).map(line => {
        const cols = line.split(',').map(c => SO_normalize(c));
        const obj = {};
        headers.forEach((h, i) => {
            obj[h] = cols[i] || '';
        });
        return obj;
    });
}


function SO_isRowEmpty(row) {
    return Object.values(row).every(v => !SO_normalize(v));
}

function SO_validateShape(row) {
    console.log(row,'row');
    console.log(row.tagnumber,'tagnumber');
    const tag = SO_normalize(row.tagnumber);
    console.log(tag,'tag');
    const qtyStr = SO_normalize(row.qty);
    const spStr = SO_normalize(row.selling_price);

    const qty = parseFloat(qtyStr || '0');
    const sellingPrice = spStr === '' ? null : parseFloat(spStr);

    if (!tag) {
        SO_abortWithError('Tag Number', '', 'Tag Number is required.');
        return null;
    }
    if (isNaN(qty) || qty <= 0) {
        SO_abortWithError('Qty', qtyStr, 'Qty must be a positive number.');
        return null;
    }
    if (spStr !== '' && isNaN(sellingPrice)) {
        SO_abortWithError('Selling Price', spStr, 'Selling Price must be numeric.');
        return null;
    }

    return { tag, qty, sellingPrice };
}

function SO_fetchProductByTag(tag) {
    const salesorderId = $('#recordid').val() || 0;
    const csrf = $('#csrfToken').val() || $('meta[name="csrf-token"]').attr('content');

    return new Promise((resolve, reject) => {
        $.ajax({
            type: 'POST',
            url: 'bulkproductbytag',
            data: {
                tagnumber: tag,
                salesorder_id: salesorderId,
                _csrf: csrf
            },
            dataType: 'json',
            success: function (res) {
                if (res && res.status === 'success' && res.data) {
                    resolve(res.data);
                } else {
                    reject((res && res.message) ? res.message : 'Server rejected this tag.');
                }
            },
            error: function () {
                reject('Server error while validating tag.');
            }
        });
    });
}

function SO_fillRow(row, index, data, qty, sellingPriceFromCsv) {
    const j = index; 

    const qtyInStock = parseFloat(data.qty || '0');
    if (qty > qtyInStock) {
        SO_abortWithError(
            'Qty',
            qty.toString(),
            `Qty (${qty}) cannot be less than Qty In Stock (${qtyInStock}).`,data.tag_number || ''
        );
        return false;
    }

    const spExcl = parseFloat(data.sp_exclusive_gst || '0');

    let sellingPriceToSet = sellingPriceFromCsv;
    if (sellingPriceToSet != null) {
        if (sellingPriceToSet < spExcl) {
            SO_abortWithError(
                'Selling Price',
                sellingPriceFromCsv.toString(),
                `Selling Price (${sellingPriceFromCsv}) cannot be more than Selling Price (GST Exclude) (${spExcl}).`,data.tag_number || ''
            );
            return false;
        }
    } else {
        sellingPriceToSet = '';
    }
    row.find('#product_name_' + j + '1').val(data.product_id || '');
    row.find('#product_name_' + j).val(data.product_name || '');
    row.find('#tag_number_' + j).val(data.tag_number || '');
    row.find('#category_' + j).val(data.prod_category_value || '');
    row.find('#sub_category_' + j).val(data.sub_catagory_value || '');
    row.find('#qty_in_stock_' + j).val(data.qty || '');
    row.find('#qty_' + j).val(qty);
    
    row.find('#hsn_code_' + j).val(data.hsn_code || '');
    row.find('#gst_percentage_' + j).val(data.gst_percentage || '');
    row.find('#purchase_price_' + j).val(data.quoted_price_gst_exclude || '');
    row.find('#selling_price_gst_exclude_' + j).val(data.sp_exclusive_gst || '');

    row.find('#selling_price_' + j).val(sellingPriceToSet);

    const sp = sellingPriceToSet;
    const basePrice = sp * qty;
    row.find('#base_price_gst_exclude_' + j).val(isNaN(basePrice) ? '' : basePrice.toFixed(2));

    row.find('#inventory_id_' + j).val(data.inventory_id || '');
    // row.find('#inventory_id_' + j).hide();
    
    if (typeof afterRowCreatedAndPopulated === 'function') {
        afterRowCreatedAndPopulated(row, j);
    }
    soRecalcFormRow(j);
    return true;
}

function SO_bulkAddRows(csvRows) {
    Loading.show();
    SO_abortImport = false;

    soInitGstModeOnce();
    const existingRows = productTable2781.find('tbody tr.product-row').length;
    let currentIndex = existingRows; 
    let i = 0;

    const usedTagsInFile = new Set();

    async function next() {
        if (SO_abortImport) {
           Loader.hide();
            return;
        }

        while (i < csvRows.length && SO_isRowEmpty(csvRows[i])) i++;

        if (i >= csvRows.length) {
           Loader.hide();
            return;
        }

        SO_bulkRowIndex = i;
        const rowData = csvRows[i];
        const parsed = SO_validateShape(rowData);
        if (!parsed) {
            return;
        }

        const tag = parsed.tag;
        const qty = parsed.qty;
        const sellingPrice = parsed.sellingPrice;

        if (usedTagsInFile.has(tag.toLowerCase())) {
            SO_abortWithError('Tag Number', tag, 'Duplicate tag in CSV file.',tag);
            return;
        }
        usedTagsInFile.add(tag.toLowerCase());

        try {
            const prod = await SO_fetchProductByTag(tag);

            await addRowBtn(2781, 'salesorder');

            currentIndex += 1;
            const row = productTable2781.find('tbody tr.product-row').last();

            const ok = SO_fillRow(row, currentIndex, prod, qty, sellingPrice);
            if (!ok) return; 

            i++;
            setTimeout(next, 100);
        } catch (errMsg) {
            Loader.hide();
            SO_abortWithError('Tag Number', tag, errMsg);
        }
    }

    next();
}

// UI wiring: attach Bulk Upload around Add Ro+w button
$(document).ready(function () {
    $('.add-more-records-so[data-blockid="2781"][data-module="salesorder"]').each(function () {
        const btn = $(this);
        const col = btn.closest('[class*="col-"]');

        if (col.data('bulk-initialized')) return;
        col.data('bulk-initialized', true);

        col.removeClass(function (i, c) {
            return (c.match(/col-\d+/g) || []).join(' ');
        }).addClass('col-6');

        if (!col.is(':visible') || col.hasClass('disabled') || col.is(':disabled')) return;

        const wrap = $('<div class="d-flex flex-nowrap align-items-center gap-2"></div>');
        btn.appendTo(wrap);

        const uploadBtn = $('<button/>', {
            id: 'bulk-upload-btn-so',
            type: 'button',
            class: 'btn btn-secondary',
            text: 'Bulk Upload CSV'
        });

        const sampleBtn = $('<a/>', {
            id: 'sample-download-so',
            class: 'btn btn-primary',
            text: 'Sample Download'
        });

        wrap.append(uploadBtn).append(sampleBtn);
        col.html(wrap);

        const fileInput = $('<input/>', {
            id: 'bulk-upload-file-so',
            type: 'file',
            accept: '.csv',
            style: 'display:none'
        });
        col.append(fileInput);
    });

    // open file dialog
    $(document)
        .off('click', '#bulk-upload-btn-so')
        .on('click', '#bulk-upload-btn-so', function () {
            $('#bulk-upload-file-so').val('').trigger('click');
        });

    // handle CSV selection
    $(document)
        .off('change', '#bulk-upload-file-so')
        .on('change', '#bulk-upload-file-so', function (e) {    
            const file = e.target.files[0];
            if (!file) return;

            const existingRows = productTable2781.find('tbody tr.product-row').length;
            if (existingRows > 0) {
                const c = confirm(
                    'Existing records will be deleted and new records will be uploaded. Do you want to continue?'
                );
                if (!c) {
                    $(this).val('');
                    return;
                }
                productTable2781.find('tbody').empty();
            }

            if (file.type && file.type !== 'text/csv' && !file.name.endsWith('.csv')) {
                alert('Only CSV files are allowed.');
                return;
            }

            const reader = new FileReader();
            reader.onload = function (evt) {
                try {
                    const text = evt.target.result || '';
                    const rows = SO_parseCSV(text);
                    // console.log(rows,'rows');
                    if (!rows.length) {
                        alert('CSV has no data rows.');
                       Loader.hide();
                        return;
                    }
                    //Revert bulk upload from detail page changes
                    // if (rows.length > MAX_ALLWED) {
                    //     alert('You can upload maximum 100 records from this screen.\n'
                    //         + 'For more than 100 records, please use the Detail page Bulk Import.');
                    //    Loader.hide();
                    //     return;
                    // }
                    Loader.show();

                    SO_bulkAddRows(rows);
                } catch (err) {
                    Loader.hide();
                    console.error('CSV parse error:', err);
                    alert('Could not parse CSV file. Please check format.');
                }
            };
            reader.onerror = function () {
                alert('Unable to read file.');
                Loader.hide();
            };
            reader.readAsText(file);
        });
});

// ============ END SALES ORDER BULK IMPORT ============


console.log('salesorderloaded');

function fetchAndSet(endpoint, inputId, map) {
    const node = document.getElementById(inputId);
    if (!node) {
        console.warn(`fetchAndSet: No element found with id ${inputId}`);
        return;
    }
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
               if(endpoint === "getbillwhlocation") {
                    ensureAndSetHiddenInput('bill_wh_address_stateCode', data.statecode || '');
                }

                // For Ship To Address state code (ship_to_address_stateCode)
                if(endpoint === "getshipvendorlocation") {
                    ensureAndSetHiddenInput('ship_to_address_stateCode', data.statecode || '');
                }
            },
            error: function(xhr, status, error) {
                console.error(`AJAX error (${endpoint}):`, error);
                for(let domId of Object.values(map.fields)) {
                    document.getElementById(domId).value = '';
                }
                if(endpoint === "getbillwhlocation") {
                    ensureAndSetHiddenInput('bill_wh_address_stateCode', '');
                }
                if(endpoint === "getshipvendorlocation") {
                    ensureAndSetHiddenInput('ship_to_address_stateCode', '');
                }
            }
        });
    });
    observer.observe(node, { attributes: true, attributeFilter: ['value'] });
}
function ensureAndSetHiddenInput(id, value) {
    let node = document.getElementById(id);
    if (!node) {
        node = document.createElement('input');
        node.type = 'hidden';
        node.id = id;
        document.body.appendChild(node);
    }
    node.value = value;
    $(node).trigger('change');
}

$(document).on('change', '#bill_wh_address_stateCode', function() {
    setBillingState(this.value);
});
$(document).on('change', '#ship_to_address_stateCode', function() {
    setShippingState(this.value);
});
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
    /** ship_to_address_stateCode in this case only below */
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


    /** bill_wh_address_stateCode creating in this case only below*/
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

    //code added by ptpatel on date 04-11-2025
    $('#productTable2801')
    .closest('.accordion-body')
    .find('.add-more-records')
    .remove();
    $('#productTable2801 .remove-row-btn').closest('td').remove();
    $('#productTable2801 th').filter(function () {
        return $(this).text().trim() === 'Action';
    }).remove();
    //end code added by ptpatel on date 04-11-2025
});

function validateSellingPrice(row) {
    var suggested = parseFloat($('#selling_price_gst_exclude_' + row).val()) || 0; 
    var finalVal  = parseFloat($('#selling_price_' + row).val()) || 0;            

    if (finalVal < suggested) {
        alert('Selling Price cannot be less than Suggested Selling Price.');
        $('#selling_price_' + row).val(suggested.toFixed(2)).trigger('change');  
        return false;
    }
    return true;
}

$(document).on('change blur', '.selling_price', function () {
    var id  = $(this).attr('id');     
    var row = id.split('_').pop();    
    validateSellingPrice(row);
});

$('form').on('submit', function () {
    var ok = true;
    $('.selling_price').each(function () {
        var row = this.id.split('_').pop();
        if (!validateSellingPrice(row)) {
            ok = false;
            return false;
        }
    });
    return ok;
});


/////aprrove////////////REJECT///
 $(document).on("click", "#approvesubmit", function () {
    let data = {
        Recordid: $("#recordid").val(),   
        _csrf: $("#csrfToken").val(),
        approve_reason: $("#approve_comment").val()
    };

    if (data.approve_reason === "") {
        alert("Please enter comment!");
        $("#approve_comment").focus();
        return false;
    }

    $.ajax({
        type: "POST",
        url: "approvesalesorder",
        data: data,
        success: function (response) {
            if (response.status === "success") location.reload();
            else alert("Something went wrong");
        },
        error: function () {
            alert("Error occurred. Please try again.");
        },
        dataType: "json",
    });
});

$(document).on("click", "#rejectsubmit", function () {
    let data = {
        Recordid: $("#recordid").val(),   
        _csrf: $("#csrfToken").val(),
        reject_reason: $("#reject_comment").val()
    };

    if (data.reject_reason === "") {
        alert("Please enter comment!");
        $("#reject_comment").focus();
        return false;
    }

    $.ajax({
        type: "POST",
        url: "approvesalesorder",
        data: data,
        success: function (response) {
            if (response.status === "success") location.reload();
            else alert("Something went wrong");
        },
        error: function () {
            alert("Error occurred. Please try again.");
        },
        dataType: "json",
    });
});

$(document).ready(function() {
    var $checkbox = $("#send_for_approval");
    if ($checkbox.length && $checkbox.is(":checked")) {
        $checkbox.prop("disabled", true);
    }
});

$(document).ready(function() {
    function toggleRowsBasedOnStage() {
        var stageVal = parseInt($('#stage').val(), 10);
        if (stageVal < 4) {
            $('.row2800, .row2801').hide();
        } else {
            $('.row2800, .row2801').show();
        }
    }
    toggleRowsBasedOnStage();
    $('#stage').on('change input', function() {
        toggleRowsBasedOnStage();
    });

   
});
window.SO_GST_MODE = window.SO_GST_MODE || null;

    function soInitGstModeOnce() {
        const bill = parseInt($('#lead_display_bill_wh_statecode').text().trim() || '', 10);
        const ship = parseInt($('#lead_display_ship_statecode').text().trim() || '', 10);

        if (!isNaN(bill) && !isNaN(ship)) {
            window.SO_GST_MODE = (bill == ship) ? 'INTRA' : 'INTER';
        } else {
            window.SO_GST_MODE = null;
        }
    }

    function soNum(v) {
        const n = parseFloat(v);
        return isNaN(n) ? 0 : n;
    }
     function soCalcGstForLine(qty, spExcl, gstPercentage) {
        const base = soNum(qty) * soNum(spExcl);
        const gstPer = soNum(gstPercentage);

        let cgstPer = 0, sgstPer = 0, igstPer = 0;

        if (window.SO_GST_MODE === 'INTRA') {
            cgstPer = gstPer / 2;
            sgstPer = gstPer / 2;
            igstPer = 0;
        } else{
            cgstPer = 0;
            sgstPer = 0;
            igstPer = gstPer;
        } 

        const cgstAmt = base * cgstPer / 100;
        const sgstAmt = base * sgstPer / 100;
        const igstAmt = base * igstPer / 100;
        const total   = base + cgstAmt + sgstAmt + igstAmt;

        return {
            base,
            gstPer,
            cgstPer,
            sgstPer,
            igstPer,
            cgstAmt,
            sgstAmt,
            igstAmt,
            total
        };
    }

    function soRecalcFormRow(j) {
        if (!window.SO_GST_MODE) {
            soInitGstModeOnce(); 
        }

    var $row   = $('#productTable2781').find('tbody tr.product-row').filter(function () {
        return $(this).find('#qty_' + j).length > 0;
    }).first();
    if (!$row.length) return;

    var qty    = soNum($row.find('#qty_' + j).val());
    var spExcl = soNum($row.find('#selling_price_' + j).val());
    var gstPer = soNum($row.find('#gst_percentage_' + j).val());

    var calc = soCalcGstForLine(qty, spExcl, gstPer);

    $row.find('#base_price_gst_exclude_' + j).val(calc.base.toFixed(2));

    $row.find('#cgst_percentage_' + j).val(calc.cgstPer.toFixed(2));
    $row.find('#sgst_percentage_' + j).val(calc.sgstPer.toFixed(2));
    $row.find('#igst_percentage_' + j).val(calc.igstPer.toFixed(2));

    $row.find('#cgst_amount_' + j).val(calc.cgstAmt.toFixed(2));
    $row.find('#sgst_amount_' + j).val(calc.sgstAmt.toFixed(2));
    $row.find('#igst_amount_' + j).val(calc.igstAmt.toFixed(2));

    $row.find('#total_amount_' + j).val(calc.total.toFixed(2));
}

function soRecalcFormRowByElement(el) {
    var $row = $(el).closest('tr.product-row');
    var $qty = $row.find('[id^="qty_"]');
    if (!$qty.length) return;
    var id = $qty.attr('id');          
    var j  = id.split('_').pop();
    soRecalcFormRow(j);
}

$(document).on(
    'change keyup blur',
    '#productTable2781 [id^="qty_"], #productTable2781 [id^="selling_price_gst_exclude_"], #productTable2781 [id^="gst_percentage_"]',
    function () {
        soRecalcFormRowByElement(this);
    }
);

function soRecalcAllFormRows() {
    $('#productTable2781').find('tbody tr.product-row').each(function(){
        var $qty = $(this).find('[id^="qty_"]');
        if (!$qty.length) return;
        var j = $qty.attr('id').split('_').pop();
        soRecalcFormRow(j);
    });
}

$(document).on('change', '#bill_wh_statecode, #ship_statecode', function () {
    soInitGstModeOnce();
    soRecalcAllFormRows();
});


$(document).ready(function() {
  var $stage = $('#stage');

  $stage.find('option[value=""]').remove();

  function setOptionSelected(val) {
    $stage.find('option').prop('selected', false) 
      .filter('[value="' + val + '"]').prop('selected', true); 
    $stage.val(val); 
  }

  function resetOptions() {
    $stage.find('option').each(function() {
      $(this).prop('disabled', false);
    });
  }

  function handleStageBehavior() {
    var val = $stage.val();

    if (val === "7") {
      $stage.find('option').each(function() {
        var optionVal = $(this).val();
        if (optionVal !== "7" && optionVal !== "8") {
          $(this).prop('disabled', true);
        } else {
          $(this).prop('disabled', false);
        }
      });

      $stage.off('select2:selecting').on('select2:selecting', function(e) {
        if (e.params.args.data.id !== "8") {
          e.preventDefault();
        }
      });

      $stage.prop('disabled', false);

    } else {
      resetOptions();
      $stage.prop('disabled', true);
      $stage.off('select2:selecting');
    }

    $stage.trigger('change.select2');
  }

  if (!$stage.val() || $stage.val() === "") {
    setOptionSelected('1');
    $stage.trigger('change').trigger('change.select2');
  } else {
    setOptionSelected($stage.val());
  }

  handleStageBehavior();

  $stage.on('change', function() {
    var selectedValue = $(this).val();
    setOptionSelected(selectedValue); 
    handleStageBehavior();
  });
 
});

$('form').on('submit', function(e) {
  $('#stage').prop('disabled', false);

  setTimeout(function() {
    $('#stage').prop('disabled', true);
  }, 100); 
});
(function($){

    let soPreviewRows = [];
    let soCurrentPage = 1;
    let soPageSize = 50;

    function soRenderPreviewTable() {
        const tbody = $('#soPreviewTable tbody');
        tbody.empty();

        const search = ($('#soPreviewSearch').val() || '').toLowerCase();
        let filtered = soPreviewRows;

        if (search) {
            filtered = soPreviewRows.filter(r => {
                return (
                    (r.tag_number || '').toLowerCase().includes(search) ||
                    (r.product_name || '').toLowerCase().includes(search) ||
                    (r.prod_category_value || '').toLowerCase().includes(search) ||
                    (r.sub_catagory_value || '').toLowerCase().includes(search)
                );
            });
        }

        const totalRecords = filtered.length;
        $('#soPreviewTotalRecords').text(totalRecords);

        if (!totalRecords) {
            $('#soPreviewPagination').hide();
            $('#soPreviewRecordInfo').text('No records to display.');
            return;
        }

        const totalPages = Math.ceil(totalRecords / soPageSize);
        if (soCurrentPage > totalPages) soCurrentPage = totalPages;

        const start = (soCurrentPage - 1) * soPageSize;
        const pageRows = filtered.slice(start, start + soPageSize);

        pageRows.forEach((r) => {
            const qty    = soNum(r.qty || 0);
            const spExcl = soNum(r.sp_exclusive_gst || 0);
            const gstPct = soNum(r.gst_percentage || 0);

            const calc = soCalcGstForLine(qty, spExcl, gstPct);

            const tr = $('<tr>');
            tr.append($('<td>').text(r.row_number));
            tr.append($('<td>').text(r.tag_number));
            tr.append($('<td>').text(qty));
            tr.append($('<td>').text(r.qty_in_stock));
            tr.append($('<td>').text(r.selling_price === null ? '' : r.selling_price));
            tr.append($('<td>').text(r.product_name));
            tr.append($('<td>').text(r.prod_category_value));
            tr.append($('<td>').text(r.sub_catagory_value));
            tr.append($('<td>').text(r.hsn_code));
            tr.append($('<td>').text(gstPct));
            tr.append($('<td>').text(spExcl.toFixed(2)));
            tr.append($('<td>').text(calc.base.toFixed(2)));
            tr.append($('<td>').text(calc.cgstPer.toFixed(2)));
            tr.append($('<td>').text(calc.sgstPer.toFixed(2)));
            tr.append($('<td>').text(calc.igstPer.toFixed(2)));
            tr.append($('<td>').text(calc.cgstAmt.toFixed(2)));
            tr.append($('<td>').text(calc.sgstAmt.toFixed(2)));
            tr.append($('<td>').text(calc.igstAmt.toFixed(2)));
            tr.append($('<td>').text(calc.total.toFixed(2)));
            tbody.append(tr);
        });

        $('#soPreviewPagination').show();
        $('#soPreviewRecordInfo').text(
            'Showing ' + (start + 1) + ' to ' + (start + pageRows.length) + ' of ' + totalRecords + ' records'
        );

        const pageNumsContainer = $('#soPreviewPageNumbers');
        pageNumsContainer.empty();

        function addPageBtn(label, page, disabled, isActive) {
            const btn = $('<button>')
                .addClass('btn btn-sm soPreviewPageBtn ' +
                        (isActive ? 'btn-light' : 'btn-outline-light'))
                .text(label)
                .data('page', page);

            if (disabled) {
                btn.prop('disabled', true);
            }
            pageNumsContainer.append(btn);
        }

        if (totalPages > 1) {
           

            const startPage = Math.max(1, soCurrentPage - 1);
            const endPage   = Math.min(totalPages, soCurrentPage + 1);

            for (let p = startPage; p <= endPage; p++) {
                addPageBtn(String(p), p, false, p === soCurrentPage);
            }

          
        }
    }


    function soShowErrors(errors) {
        $('#soErrRow').text('Multiple');
        const ul = $('#soErrList');
        ul.empty();
        (errors || []).forEach(msg => {
            ul.append($('<li>').text(msg));
        });
        $('#soImportErrorModal').modal('show');
    }

    function soStartLoading() {
        Loading.show();
    }

    function soStopLoading() {
       Loader.hide();
    }

    $(document).ready(function(){

        $(document).on('click', '#so-bulk-import-btn', function(){
            soInitGstModeOnce();          
            $('#soPreviewSearch').val('');
            $('#so-selected-file-name').text('');
            $('#so-bulk-file-input').val('');
            soPreviewRows = [];
            soCurrentPage = 1;
            soRenderPreviewTable();
            $('#soBulkImportModal').modal('show');
        });

        $(document).on('click', '#so-select-file-btn', function(){
            $('#so-bulk-file-input').trigger('click');
        });

        $(document).on('click', '#so-download-sample-btn', function(){
            window.location.href = 'downloadsample';
        });

        $(document).on('change', '#so-bulk-file-input', function(e){ 
            const file = e.target.files[0];
            if (!file) return;

            $('#so-selected-file-name').text(file.name);

            if (file.type && file.type !== 'text/csv' && !file.name.toLowerCase().endsWith('.csv')) {
                alert('Only CSV files are allowed.');
                $('#so-selected-file-name').text('');
                $(this).val('');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(evt){
                try {
                    const text = evt.target.result || '';
                    const salesorderId = $('#so-bulk-salesorder-id').val() || 0;
                    const csrf = $('#csrfToken').val() || $('meta[name="csrf-token"]').attr('content');
                    soStartLoading();
                    $.ajax({
                        type: 'POST',
                        url: 'bulkpreviewcsv',
                        data: {
                            salesorder_id: salesorderId,
                            csvtext: text,
                            _csrf: csrf
                        },
                        dataType: 'json',
                        success: function(res){
                            soStopLoading();
                            if (res && res.success) {
                                soPreviewRows = res.rows || [];
                                soCurrentPage = 1;
                                soRenderPreviewTable();
                                $('#soBulkReplaceWrap').show();
                                $('#soBulkReplaceAll').prop('checked', false);
                            } else {
                                soPreviewRows = [];
                                soRenderPreviewTable();
                                if (res && res.errors && res.errors.length) {
                                    soShowErrors(res.errors);
                                } else {
                                    alert((res && res.message) ? res.message : 'Error while processing CSV.');
                                }
                            }
                        },
                        error: function(){
                            soStopLoading();
                            alert('Server error while processing CSV.');
                        }
                    });
                } catch(err){
                    console.error(err);
                    alert('Could not read CSV file.');
                }
            };
            reader.onerror = function(){
                alert('Unable to read file.');
            };
            reader.readAsText(file);
        });

        $(document).on('input', '#soPreviewSearch', function(){
            soCurrentPage = 1;
            soRenderPreviewTable();
        });

        $(document).on('click', '#soPreviewFirstPage', function(){
            soCurrentPage = 1;
            soRenderPreviewTable();
        });
        $(document).on('click', '#soPreviewPrevPage', function(){
            if (soCurrentPage > 1) {
                soCurrentPage--;
                soRenderPreviewTable();
            }
        });
        $(document).on('click', '#soPreviewNextPage', function(){
            const totalPages = Math.ceil((soPreviewRows.length || 0) / soPageSize);
            if (soCurrentPage < totalPages) {
                soCurrentPage++;
                soRenderPreviewTable();
            }
        });
        $(document).on('click', '#soPreviewLastPage', function(){
            const totalPages = Math.ceil((soPreviewRows.length || 0) / soPageSize);
            soCurrentPage = totalPages || 1;
            soRenderPreviewTable();
        });
        $(document).on('click', '#soPreviewPageNumbers button', function(){
            const p = $(this).data('page') || 1;
            soCurrentPage = p;
            soRenderPreviewTable();
        });

        $(document).on('click', '#so-bulk-save-btn', async function(){
            if (!soPreviewRows.length) {
                alert('No rows to save.');
                return;
            }
             const ok = await showConfirm();
                if (!ok) {
                    return; 
                }
            const replaceAll = $('#soBulkReplaceAll').is(':checked') ? 1 : 0;
            const salesorderId = $('#so-bulk-salesorder-id').val() || 0;
            if (!salesorderId || parseInt(salesorderId, 10) <= 0) {
                alert('Invalid Sales Order id.');
                return;
            }

            const csrf = $('#csrfToken').val() || $('meta[name="csrf-token"]').attr('content');

            soStartLoading();
            $.ajax({
                type: 'POST',
                url: 'bulksavecsv_so',
                data: {
                    salesorder_id: salesorderId,
                    rows: JSON.stringify(soPreviewRows),
                    replace_all: replaceAll,
                    _csrf: csrf
                },
                dataType: 'json',
                success: function(res){
                    soStopLoading();
                    if (res && res.success) {
                        $('#soBulkImportModal').modal('hide');
                        window.location.reload();
                    } else {
                        if (res && res.errors && res.errors.length) {
                            soShowErrors(res.errors);
                        } else {
                            alert((res && res.message) ? res.message : 'Error while saving records.');
                        }
                    }
                },
                error: function(){
                    soStopLoading();
                    alert('Server error while saving records.');
                }
            });
        });

    });

})(jQuery);
$(document).on('click', '#detail-btn-asset', function () {
  const id = $('#recordid').val();
  if (!id) {
    alert('No datawiping id');
    return;
  }
  window.location.href = 'itemslist?salesorder_id=' + encodeURIComponent(id);
});
// $(function () {
//     var record = $('#record').val();
//     if (record) { 
//         $('.row2781').remove();
//     }
// });

 function loadAssetCount() {
    var salesorder_id = $('#recordid').val();
    if (!salesorder_id) return;

    $.ajax({
      url: 'getcount',
      type: 'POST',
      dataType: 'json',
      data: { id: salesorder_id, _csrf: $('#csrfToken').val() },
      success: function (res) {
        if (res && res.success) {
          const $btn = $('#detail-btn-asset');
          if ($btn.length) {
            $btn.text('View Item Details (Count = ' + ((res.count && res.count > 0) ? res.count : 0) + ')');
          }
        }
      },
      error: function (xhr) {
        console.error('get_asset_count error:', xhr.responseText);
      }
    });
  }
  loadAssetCount();

  $(document)
  .off('click', '#so-bulk-delete-btn')
  .on('click', '#so-bulk-delete-btn', async function () {

    if (!$('#soBulkReplaceAll').is(':checked')) {
        alert('Please tick the "Delete existing items" checkbox first.');
        return;
    }

    var salesorderId = $('#so-bulk-salesorder-id').val() || $('#recordid').val() || 0;
    if (!salesorderId) {
        alert('Sales Order id missing.');
        return;
    }

    const ok = await showConfirm('This will delete all existing items for this Sales Order.\nDo you want to continue?');
                if (!ok) {
                    return; 
                }
    $.ajax({
        type: 'POST',
        url: 'bulkdeleteitems_so',
        dataType: 'json',
        data: {
            salesorder_id: salesorderId,
            _csrf: $('#csrfToken').val()
        },
        success: function (res) {
            if (res && res.success) {
                alert(res.message || 'Existing items deleted.');
                $('#soPreviewTable tbody').empty();
                $('#soPreviewTotalRecords').text('0');
                $('#soPreviewRecordInfo').text('No records to display.');
                window.soPreviewRows = [];
                // $('#soBulkReplaceAll').prop('checked', false);
                    soAfterDeleteSuccess();
            } else {
                alert(res && res.message ? res.message : 'Delete failed.');
            }
        },
        error: function () {
            alert('Server error while deleting items.');
        }
    });
});

(function ($) {

    $(document).on('shown.bs.modal', '#soBulkImportModal', function () {
        var $wrap = $('#soBulkReplaceWrap');
        var $chk  = $('#soBulkReplaceAll');
        var $save = $('#so-bulk-save-btn');
        var $del  = $('#so-bulk-delete-btn');
        var $file = $('#so-bulk-file-input'); 

        if ($wrap.length && $chk.length) {
            $wrap.show();
            $chk.prop('checked', false).prop('disabled', false);
        }

        $save.show();
        $del.hide();
        $file.prop('disabled', false);
    });

    $(document).on('change', '#so-bulk-file-input', function (e) {
        var file = e.target.files && e.target.files[0];
        if (!file) return;

        $('#soBulkReplaceWrap').hide();
        $('#soBulkReplaceAll').prop('checked', false).prop('disabled', true);

        $('#so-bulk-save-btn').show();
        $('#so-bulk-delete-btn').hide();
        $('#so-bulk-file-input').prop('disabled', false);
    });

    $(document).on('change', '#soBulkReplaceAll', function () {
        var checked = $(this).is(':checked');
        var $save = $('#so-bulk-save-btn');
        var $del  = $('#so-bulk-delete-btn');
        var $file = $('#so-bulk-file-input');

        if (checked) {
            $save.hide();
            $del.show();
            $file.prop('disabled', true);
        } else {
            $save.show();
            $del.hide();
            $file.prop('disabled', false);
        }
    });

})
function soAfterDeleteSuccess(){
    $('#soBulkReplaceAll').prop('checked', false).prop('disabled', true);
    $('#soBulkReplaceWrap').hide();
    $('#so-bulk-delete-btn').hide();
    $('#so-bulk-save-btn').show();
    $('#so-bulk-file-input').prop('disabled', false).val('');
}
$(document).on('change', '#soBulkReplaceAll', function () {
    var checked = $(this).is(':checked');
    var $save = $('#so-bulk-save-btn');
    var $del  = $('#so-bulk-delete-btn');
    var $file = $('#so-bulk-file-input');

    if (checked) {
        $save.hide();
        $del.show();
        $file.prop('disabled', true);
    } else {
        $save.show();
        $del.hide();
        $file.prop('disabled', false);
    }
});

(jQuery);


