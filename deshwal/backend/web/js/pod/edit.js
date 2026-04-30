const soNode = document.getElementById('so_number1');

const soObserver = new MutationObserver(() => {
    const soNum = soNode.value;
    if (!soNum) return;
    var _csrf= $("#csrfToken").val();
    $.ajax({
        type: 'POST',
        url: 'getvendorname', 
        data: {
            so_number: soNum,
            _csrf: _csrf
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success' && response.vendor_name) {
                $('#vendor_name').val(response.vendor_name);
                $('#vendor_name1').val(response.vendor_id);
            }
        },
        error: function () {
            console.warn('Error fetching!!')
        }
    });
});

soObserver.observe(soNode, { attributes: true, attributeFilter: ['value'] });

$('#so_number1').on('change', function() {
    soObserver.takeRecords(); 
});
  console.log('pod edit js loaded');
