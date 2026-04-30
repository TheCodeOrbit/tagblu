const soNode = document.getElementById('so_number1');

function clearProductRows() {
    $(".product-row").remove();
}

async function populateProductsOnAjax(loadProducts, tableId = 'productTable2798') {
    let lastIndex = $('#' + tableId + ' tbody tr').length;
    let currentRow = '';
    console.log('came');
    for (let i = 0; i < loadProducts.length; i++) {
        const j = lastIndex + i + 1; 
        const prod = loadProducts[i];
        console.log(prod,'prod');
        await addRowBtn('2798', 'vehicleloading'); 

        const $tbody = $('#' + tableId + ' tbody');
        const $lastRow = $tbody.find('tr:last');
        const rowIndex = $lastRow.index();

        if ($lastRow.length > 0 && currentRow !== rowIndex) {
            $lastRow.find(`#product_name_${j}`).val(prod.product_name || '');
            $lastRow.find(`#category_${j}`).val(prod.category || '');
            $lastRow.find(`#sub_category_${j}`).val(prod.sub_category || '');
            $lastRow.find(`#qty_${j}`).val(prod.qty || '');
            $lastRow.find(`#qty_in_stock_${j}`).val(prod.qty_in_stock || '');
            $lastRow.find(`#cgst_percentage_${j}`).val(prod.cgst_percentage || '00');
            $lastRow.find(`#total_amount_${j}`).val(prod.total_amount || '00');
            
            // $lastRow.find(`#out_qty_${j}`)[0].setAttribute("max", prod.qty || 0);

            // $lastRow.find('td:first').prepend(
            //   `<input type="hidden" name="generatepi_items_detail[${j}][tag_number]" value="${prod.tag_number || ''}">`
            // );

            currentRow = rowIndex;
        }
    }
}

const soObserver = new MutationObserver(() => {
    const soNum = soNode.value;
    if (!soNum) return;
    var _csrf= $("#csrfToken").val();
    $.ajax({
        type: 'POST',
        url: 'getgeneratepidetails', 
        data: {
            so_number: soNum,
            _csrf: _csrf
        },
        dataType: 'json',
        success: function(response) {
            console.log(response,'response');
            // exit;
            if (response.status === 'success' && response.data) {
                let data = response.data;
                console.log(data,'data');
                $('#payment_terms').val(data.payment_terms);
                $('#po_date').val(data.po_date ? data.po_date.substr(0,10) : '');
                $('#account_name1').val(data.account_id);
                $('#account_name').val(data.account_name);
                $('#po_number').val(data.po_number);
                $('#po_date').val(data.po_date);
                $('#po_amount').val(data.po_amount);

                 clearProductRows();
                if (Array.isArray(data.products) && data.products.length > 0) {
                  populateProductsOnAjax(data.products, 'productTable2798');
                }
            }
        },
        error: function () {
            clearProductRows();
        }
    });
});



$(document).ready(function() {
    $('#productTable2798 input.out_qty').each(function() {
        var outQtyId = $(this).attr('id');
        var index = outQtyId.split('_').pop();
        var qtyVal = parseFloat($('#qty_' + index).val()) || 0;
        $(this).attr('max', qtyVal);
    });

    $('#productTable2798').on('input change', 'input.out_qty', function() {
        var outQtyId = $(this).attr('id');
        var index = outQtyId.split('_').pop();

        var qtyVal = parseFloat($('#qty_' + index).val()) || 0;
        var outQtyVal = parseFloat($(this).val()) || 0;

        if (outQtyVal > qtyVal) {
            $(this).val(qtyVal); 
        }

        var diff = qtyVal - parseFloat($(this).val()) || 0;
        $('#difference_' + index).val(diff >= 0 ? diff : 0); 
    });
});

$('form').on('submit', function(e) {
  $('#status').prop('disabled', false);

  setTimeout(function() {
    $('#status').prop('disabled', true);
  }, 100); 
});




$(document).ready(function() {
    function updateOwnerFields() {
        var ownerValue = $('#vehicle_expence_owned_by').val();
        
        var fields = ['#vehicle_number','#vendor_name', '#vendor_vehicle_number', '#amount', '#payment_terms'];
        fields.forEach(function(selector) {
            var $input = $(selector);
            var cls = $input.attr('class');
            if (cls && cls.indexOf('~M') !== -1) {
                $input.attr('class', cls.replace('~M', '~O'));
            }
        });

        if (ownerValue === "1") { 
            $('.section-vehicle_number').show();
            $('.section-vendor_name, .section-vendor_vehicle_number, .section-amount, .section-payment_terms').hide();

            var $input = $('#vehicle_number');
            var cls = $input.attr('class');
            if (cls && cls.indexOf('~O') !== -1) {
                $input.attr('class', cls.replace('~O', '~M'));
            }
        } else if (ownerValue === "2") { 
            $('.section-vehicle_number').hide();
            $('.section-vendor_name, .section-vendor_vehicle_number, .section-amount, .section-payment_terms').show();
            $('#payment_terms').val('Auto fill from Account');
            
            ['#vendor_name1', '#vendor_name', '#vendor_vehicle_number', '#amount', '#payment_terms'].forEach(function(selector) {
                var $input = $(selector);
                var cls = $input.attr('class');
                if (cls && cls.indexOf('~O') !== -1) {
                    $input.attr('class', cls.replace('~O', '~M'));
                }
            });
        } else {
            $('.section-vehicle_number, .section-vendor_name, .section-vendor_vehicle_number, .section-amount, .section-payment_terms').hide();
        }
    }
    
    updateOwnerFields();

    $('#vehicle_expence_owned_by').change(function() {
        updateOwnerFields();
    });
});
$(document).ready(function() {
    var initialValue = $('#vehicle_loading_done').val();

    if (initialValue === '1') {
        $('#vehicle_loading_done').prop('disabled', true);
        $('#status').val('2').trigger('change');
    } else {
        $('#vehicle_loading_done').prop('disabled', false);
        if (initialValue === '2') {
            $('#status').val('1').trigger('change'); 
        } else {
            $('#status').val('').trigger('change');
        }

        $('#vehicle_loading_done').on('change', function() {
            var val = $(this).val();
            if (val === '1') {
                $('#status').val('2').trigger('change'); 
            } else if (val === '2') {
                $('#status').val('1').trigger('change');
            } else {
                $('#status').val('').trigger('change'); 
            }
        });
    }
});

console.log('vehicaljs');

soObserver.observe(soNode, { attributes: true, attributeFilter: ['value'] });

$('#so_number1').on('change', function() {
    soObserver.takeRecords(); 
});

