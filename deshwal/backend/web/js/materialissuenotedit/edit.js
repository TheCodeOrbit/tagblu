const compName = document.getElementById('comp_name1');
console.log(compName,'compName');
const compNameObserver = new MutationObserver(() => {
    const compNo = compName.value;
    if (!compNo) return;
    var _csrf= $("#csrfToken").val();
    $.ajax({
        type: 'POST',
        url: 'getwarehouseval', 
        data: {
            comp_name: compNo,
            _csrf: _csrf
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                $('#comp_address').val(response.comp_address);
                $('#comp_gstin').val(response.gstNo);
                $('#comp_pan').val(response.panNo);
                $('#contact_number').val(response.contactNo);
            }
        },
        error: function () {
            console.warn('Error fetching!!')
        }
    });
});

compNameObserver.observe(compName, { attributes: true, attributeFilter: ['value'] });

$('#comp_name1').on('change', function() { 
    compNameObserver.takeRecords(); 
});

//////////////////////////////////////////////////////


const soNode = document.getElementById('so_number1');

const soObserver = new MutationObserver(async() => {
    startLoading();
    const soNum = soNode.value;
    if (!soNum) return;
    
    var _csrf = $("#csrfToken").val();
    try {
        const response = await $.ajax({
            type: 'POST',
            url: 'getsodetail',
            data: {
                so_number: soNum,
                _csrf: _csrf
            },
            dataType: 'json'
        });
        
        console.log('AJAX success raw response:', response);

        if (response.status === 'success' && Array.isArray(response.product)) {
            $('.accordion-button[data-bs-target="#collapse2815"]').trigger('click');

            const $addRowBtn = $('.add-more-records[data-blockid="2815"][data-module="materialissuenotedit"]');
            const $tbody = $('#productTable2815 tbody');

            $tbody.empty();

            for (let i = 0; i < response.product.length; i++) {
                const prod = response.product[i];
                const j = i + 1;

                $addRowBtn.trigger('click');
                
                await waitForRowCreation($tbody, i + 1);

                const $lastRow = $tbody.find('tr:last');
                if ($lastRow.length > 0) {
                    $lastRow.find('#product_name_' + j + '1').val(prod.prod_id || '');
                    $lastRow.find('#product_name_' + j).val(prod.product_name || '');
                    $lastRow.find('#product_description_' + j).val(prod.product_description || '');
                    $lastRow.find('#product_hsn_' + j).val(prod.hsn_code || '');

                    if (typeof afterRowCreatedAndPopulated === 'function') {
                        afterRowCreatedAndPopulated($lastRow, j);
                    }
                }
            }
        }
    } catch (error) {
        console.warn('Error fetching SO details:', error);
    }
});


soObserver.observe(soNode, { attributes: true, attributeFilter: ['value'] });


function waitForRowCreation($tbody, expectedRowCount, timeout = 2000) {
    return new Promise((resolve, reject) => {
        const startTime = Date.now();
        let observer;
        
        const checkRowCount = () => {
            const currentRowCount = $tbody.find('tr').length;
            if (currentRowCount >= expectedRowCount) {
                if (observer) observer.disconnect();
                resolve();
                return;
            }
            
            if (Date.now() - startTime > timeout) {
                if (observer) observer.disconnect();
                reject(new Error('Timeout waiting for row creation'));
                return;
            }
            
            setTimeout(checkRowCount, 50);
        };
        
        observer = new MutationObserver(() => {
            const currentRowCount = $tbody.find('tr').length;
            if (currentRowCount >= expectedRowCount) {
                observer.disconnect();
                resolve();
            }
        });
        
        observer.observe($tbody[0], { childList: true, subtree: true });
        checkRowCount();
    });
}

$('#so_number1').on('change', function() {
    soObserver.takeRecords(); 
});
function makeMandatory($el) {
    let cls = $el.attr('class') || '';
    cls = cls.replace(/(\S*~)O\b/g, '$1M');

    $el.attr('class', cls);
}

function makeOptional($el) {
    let cls = $el.attr('class') || '';

    cls = cls.replace(/(\S*~)M\b/g, '$1O');

    $el.attr('class', cls);
}
function setLabelRequired($wrapper, isRequired) {
    const $label = $wrapper.find('label.control-label');
    let html = $label.html() || '';
    html = html.replace(/<span class="red"> \*<\/span>/g, '');
    if (isRequired) {
        html += '<span class="red"> *</span>';
        $wrapper.removeClass('not-required-field').addClass('required-field');
    } else {
        $wrapper.removeClass('required-field').addClass('not-required-field');
    }
    $label.html(html);
}


$('#min_type').on('change', function () {
    const val = $(this).val();

    const $department     = $('#department');  
    const $departmentWrap = $department.closest('.form-group');

    const $soNumber     = $('#so_number');   
    const $soNumberWrap = $soNumber.closest('.form-group');

    if (val === '1') {             
        makeMandatory($department);
        setLabelRequired($departmentWrap, true);
        makeOptional($soNumber);
        setLabelRequired($soNumberWrap, false);

    } else if (val === '2') {  
        makeOptional($department);
        setLabelRequired($departmentWrap, false);
        makeMandatory($soNumber);
        setLabelRequired($soNumberWrap, true);

    } else {                        
        makeOptional($department);
        setLabelRequired($departmentWrap, false);

        makeOptional($soNumber);
        setLabelRequired($soNumberWrap, false);
    }
});

$('#min_type').trigger('change');


function attachProductObserver(rowIndex) {
    const hiddenId = 'product_name_' + rowIndex + '1';  
    const hiddenInput = document.getElementById(hiddenId);

    if (!hiddenInput) {
        console.log('attachProductObserver: hidden input NOT found for row', rowIndex, '->', hiddenId);
        return;
    }

    const productObserver = new MutationObserver(() => {
        const prodId = hiddenInput.value;   
        console.log('productObserver fired for row', rowIndex, 'value:', prodId);
        if (!prodId) return;

        const _csrf = $("#csrfToken").val();

        $.ajax({
            type: 'POST',
            url: 'getproductdetail',      
            data: {
                product_id: prodId,       
                _csrf: _csrf
            },
            dataType: 'json',
            success: function (response) {
                console.log(response.status,'status'); 
                if (response.status === 'success') {
                    console.log('came');
                    $('#product_description_' + rowIndex).val(response.result.product_description || '');
                    $('#product_hsn_' + rowIndex).val(response.result.hsn_code || '');
                    $('#product_name_' + rowIndex).val(response.result.product_name || '');
                }
            },
            error: function () {
                console.warn('Error fetching product for row', rowIndex);
            }
        });
    });

    productObserver.observe(hiddenInput, {
        attributes: true,
        attributeFilter: ['value']
    });
}

$(function () {
    const $tbody = $('#productTable2815 tbody');
    const tbodyNode = $tbody[0];
    if (!tbodyNode) return;

    $tbody.find('tr.product-row').each(function () {
        const rowId = $(this).attr('id');    
        const rowIndex = parseInt(rowId, 10);
        if (rowIndex) {
            attachProductObserver(rowIndex);
        }
    });

    const rowObserver = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            const tbody = $('.productTable2815 tbody');
                    tbody.empty();
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType !== 1) return;   
                if (!node.classList.contains('product-row')) return;

                const rowId = node.getAttribute('id');  
                const rowIndex = parseInt(rowId, 10);
                if (rowIndex) {
                    console.log('tbody observer: new product-row', rowIndex);
                    attachProductObserver(rowIndex);
                }
            });
        });
    });

    rowObserver.observe(tbodyNode, {
        childList: true,
        subtree: false  
    });
});

$(function () {
    const $minDate = $('#min_date');

    if (!$minDate[0]._flatpickr) {
        flatpickr('#min_date', {
            dateFormat: 'Y-m-d',
            allowInput: false,
            clickOpens: false,
            disableMobile: true
        });
    }

    setTimeout(function () {
        const fp = $minDate[0]._flatpickr;

        if (!fp) {
            const intervalId = setInterval(function () {
                const fp2 = $minDate[0]._flatpickr;
                if (fp2) {
                    if (!$minDate.val().trim()) {
                        fp2.setDate(new Date(), false);
                    }
                    clearInterval(intervalId);
                }
            }, 100);
            return;
        }

        if (!$minDate.val().trim()) {
            fp.setDate(new Date(), false);
        }
    }, 500); 
});

$(function () {
    $(document).on('click', '#removeTextValue', function () {
        const hiddenId = $(this).data('fieldname1'); 
        const textId   = $(this).data('fieldname');  

        if (hiddenId) {
            $('#' + hiddenId).val('').trigger('change'); 
        }
        if (textId) {
            $('#' + textId).val('');
        }
        $('#productTable2815 tbody').empty();
    });
});

$(function () {
    $('#min_type').on('change', function () {
        const val      = $(this).val();          
        const recordId = $('#record').val();  
        const $tbody   = $('#productTable2815 tbody');

        if (val === '1') {
            if (!recordId || recordId.trim() === '') {
                if ($tbody.find('tr').length === 0) {
                    if (typeof addRowBtn === 'function') {
                        addRowBtn('2815', 'materialissuenotedit');
                    } else {
                        $('.add-more-records[data-blockid="2815"][data-module="materialissuenotedit"]')
                            .trigger('click');
                    }
                }
            }
        }
        if (val === '2') {
            if (recordId && recordId.trim() == '') {
                $tbody.empty();
            }
        }
    });

    $('#min_type').trigger('change');
});


$(function () {
    const val = $('#min_type').val();
    if (!val) {
        $('.section-so_number').hide();
    }
});
$(function () {
    $('#min_type').on('change', function () {
        const val      = $(this).val(); 
        const recordId = $('#record').val();

        if (!val) {
            $('.section-so_number').hide();
            return;
        }

        if (val === '1') { 
            $('#so_number1').val('').trigger('change');
            $('#so_number').val('');
            if(!recordId || recordId == ''){
                $('#productTable2815 tbody').empty();
            }
            $('.section-so_number').hide();
        }

        if (val === '2') {
            $('.section-so_number').show();
        }
    });
    const initVal = $('#min_type').val();
    if (!initVal) {
        $('.section-so_number').hide();
    }
    $('#min_type').trigger('change');
});
$(function () {
    const recordId = $('#record').val();
    console.log(recordId,'record');
    if (recordId && recordId.trim() !== '') {
        $('#min_type').prop('disabled', true);
    }
});