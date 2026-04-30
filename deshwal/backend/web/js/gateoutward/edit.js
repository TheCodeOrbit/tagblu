const vehicalLoadingNode = document.getElementById('vehicle_number1');


const vehicleObserver = new MutationObserver(() => {
    const vehicleLoading = vehicalLoadingNode.value;
    if (!vehicleLoading) return;
    var _csrf= $("#csrfToken").val();
    $.ajax({
        type: 'POST',
        url: 'getvehicledetail', 
        data: {
            vehical_no: vehicleLoading,
            _csrf: _csrf
        },
        dataType: 'json',
        success: function(response) {
            console.log(response,'response');
            if (response.status === 'success' && response.data) {
                let data = response.data;
                $('#gatepass_number').val(data.gatepass_number);
                $('#invoice_number').val(data.invoice_number);
                $('#invoice_amount').val(data.invoice_amount);
                $('#invoice_date').val(data.invoice_date);
                appendUploadedFile('#gatepass_image', data.gatepass_image, 'download_gatepass_image');
                appendUploadedFile('#invoice_image', data.invoice_image, 'download_invoice_image');
            }
        }
    });
});


function appendUploadedFile(targetSelector, attachmentId, labelText) {

    $(targetSelector).siblings('.upd-file').remove();
    $(targetSelector).siblings('.uploaded-file-hidden').remove();

    $(targetSelector).after(`
        <div class="upd-file" style="margin-top:4px;">
            Uploaded file: <br>
            <a href="javascript:void(0);" 
               data-attachment-id="${attachmentId}" 
               class="download-file">
               ${labelText}
            </a>
        </div>
        <input type="hidden" class="uploaded-file-hidden" name="${targetSelector.replace('#','')}_saved" value="${attachmentId}">
    `);

    $('.download-file').off('click').on('click', function() {
        let attachmentId = $(this).data('attachment-id');
        let _csrf = $("#csrfToken").val();

        $.ajax({
            type: 'POST',
            url: 'getattachmentpath',
            data: {
                attachment_id: attachmentId,
                _csrf: _csrf
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' && response.file_url) {
                    const link = document.createElement('a');
                    link.href = response.file_url;
                    link.download = "";
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                } else {
                    alert('File not found or download failed.');
                }
            }
        });
    });
}


vehicleObserver.observe(vehicalLoadingNode, { attributes: true, attributeFilter: ['value'] });

$('#vehicle_number1').on('change', function() {
    vehicleObserver.takeRecords(); 
});

$(document).ready(function() {
  var $btn = $('.savebutton');
  $btn.text('Out');
  $btn.removeClass('btn-primary').addClass('btn-danger');
});

