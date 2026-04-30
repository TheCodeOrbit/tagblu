<?php
$this->registerCss(
    <<<CSS
#assetEditModal .modal-dialog {
    max-width: 900px;
}

#assetEditModal .modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 12px 40px rgba(15, 23, 42, 0.25);
}

#assetEditModal .modal-header {
    border-bottom: 1px solid #e5e7eb;
    padding: 1.25rem 1.75rem;
}

#assetEditModal .modal-title {
    font-weight: 700;
    font-size: 1.25rem;
}

#assetEditDynamicFields {
    padding: 1.5rem 1.75rem 1rem;
}

#assetEditDynamicFields table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

#assetEditDynamicFields .product-row {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    grid-column-gap: 32px;
    grid-row-gap: 18px;
    width: 100%;
}

#assetEditDynamicFields .product-row > td {
    display: block;
    padding: 0;
    border: 0;
}

#assetEditDynamicFields .section-certificate {
    grid-column: 1 / span 3;
}

#assetEditDynamicFields td:has(> .remove-row-btn) {
    grid-column: 3 / span 1;
    display: flex;
    justify-content: flex-end;
    align-items: center;
}
.remove-row-btn{
    display:none;
}
#assetEditDynamicFields .section-laptop_serial_no::before,
#assetEditDynamicFields .section-hdd_sdd_serial_no::before,
#assetEditDynamicFields .section-make::before,
#assetEditDynamicFields .section-type::before,
#assetEditDynamicFields .section-capacity::before,
#assetEditDynamicFields .section-software_name::before,
#assetEditDynamicFields .section-wiping_completed::before,
#assetEditDynamicFields .section-wiping_date::before,
#assetEditDynamicFields .section-certificate::before {
    display: block;
    margin-bottom: 6px;
    font-size: 0.9rem;
    font-weight: 600;
    color: #4b5563;
}

#assetEditDynamicFields .section-laptop_serial_no::before      { content: "Laptop Serial"; }
#assetEditDynamicFields .section-hdd_sdd_serial_no::before     { content: "HDD/SDD Serial"; }
#assetEditDynamicFields .section-make::before                  { content: "Make"; }
#assetEditDynamicFields .section-type::before                  { content: "Type"; }
#assetEditDynamicFields .section-capacity::before              { content: "Capacity"; }
#assetEditDynamicFields .section-software_name::before         { content: "Software"; }
#assetEditDynamicFields .section-wiping_completed::before      { content: "Wiping Completed"; }
#assetEditDynamicFields .section-wiping_date::before           { content: "Wiping Date (dd-mm-yyyy)"; }
#assetEditDynamicFields .section-certificate::before           { content: "Certificate"; }

#assetEditDynamicFields .productinput.form-control,
#assetEditDynamicFields .singleselect,
#assetEditDynamicFields .wiping_date {
    width: 100%;
    height: 44px;
    border-radius: 6px;
    border: 1px solid #d1d5db;
    padding: 0.55rem 0.75rem;
    font-size: 0.92rem;
    color: #111827;
    background-color: #ffffff;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

#assetEditDynamicFields .productinput.form-control:focus,
#assetEditDynamicFields .singleselect:focus,
#assetEditDynamicFields .wiping_date:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 1px rgba(37, 99, 235, 0.35);
    outline: none;
}

#assetEditDynamicFields input[type="file"].certificate {
    height: auto;
    padding: 0.4rem 0.75rem;
    border-radius: 6px;
    border: 1px solid #d1d5db;
    background-color: #f9fafb;
}

#assetEditModal .modal-footer {
    border-top: 1px solid #e5e7eb;
    padding: 1rem 1.75rem 1.25rem;
    background: linear-gradient(135deg, #f9fafb, #eef2ff);
}

#assetEditSaveBtn {
    min-width: 110px;
    border-radius: 9999px;
    border: none;
    padding: 0.6rem 1.6rem;
    font-weight: 600;
    background: linear-gradient(135deg, #2563eb, #3b82f6);
    color: #ffffff;
    box-shadow: 0 8px 20px rgba(37, 99, 235, 0.35);
}

#assetEditSaveBtn:hover {
    filter: brightness(1.05);
}

#assetEditModal .btn-secondary {
    border-radius: 9999px;
    padding: 0.6rem 1.4rem;
    font-weight: 500;
    border: 1px solid #d1d5db;
    background-color: #ffffff;
    color: #111827;
}

@media (max-width: 768px) {
    #assetEditDynamicFields .product-row {
        grid-template-columns: 1fr;
        grid-row-gap: 14px;
    }
    #assetEditDynamicFields .section-certificate {
        grid-column: 1 / span 1;
    }
}
CSS
);


use backend\assets\AdminAsset;
use yii\helpers\Url;

$this->title = 'Data wiping Asset Detail';
$this->registerCssFile('@web/thememain/css/flatpickr.min.css');
$csrf = Yii::$app->request->csrfToken;
$totalPages = max(1, ceil($totalRecords / $pageSize));
?>

<input type="hidden" id="csrfToken" value="<?= $csrf ?>">
<input type="hidden" id="datawipingId" value="<?= (int)$datawiping_id ?>">

<div class="container-fluid mt-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h4>Data wiping Asset Details (Datawiping No: <?= $datewiping_no ?>)</h4>

        <a href="<?= Url::to(['detail', 'Record' => (int)$datawiping_id]) ?>"
            class="btn btn-sm btn-secondary">
            ← Back to Data Wiping
        </a>
    </div>
    <form method="get" action="assetlist" class="row mb-2">
        <input type="hidden" name="datawiping_id" value="<?= (int)$datawiping_id ?>">
        <div class="col-md-3">
            <input type="text" name="serial" value="<?= htmlspecialchars($filters['serial']) ?>"
                class="form-control form-control-sm" placeholder="Serial no">
        </div>
        <div class="col-md-3" style="display: none;">
            <select name="wiping" class="form-control form-control-sm">
                <option value="">Wiping (all)</option>
                <option value="1" <?= $filters['wiping'] === '1' ? 'selected' : '' ?>>Yes</option>
                <option value="0" <?= $filters['wiping'] === '0' ? 'selected' : '' ?>>No</option>
            </select>
        </div>
        <div class="col-md-2" style="display: none;">
            <input type="text" name="date_from" value="<?= htmlspecialchars($filters['date_from']) ?>"
                class="form-control form-control-sm" placeholder="From date (dd-mm-yyyy)">
        </div>
        <div class="col-md-2" style="display: none;">
            <input type="text" name="date_to" value="<?= htmlspecialchars($filters['date_to']) ?>"
                class="form-control form-control-sm" placeholder="To date (dd-mm-yyyy)">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-sm btn-primary w-100">Search</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="assetDetailsTable">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Laptop Serial</th>
                    <th>HDD/SDD</th>
                    <th>Make</th>
                    <th>Type</th>
                    <th>Capacity</th>
                    <th>Software</th>
                    <th>Wiping</th>
                    <th>Wiping Date</th>
                    <th>Certificate</th>
                    <?php if($hasAccess ==1) { ?>
                    <th>Action</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $startIndex = ($page - 1) * $pageSize;
                foreach ($rows as $idx => $r):
                    $index = $startIndex + $idx + 1;
                    $wipingText = !empty($r['wiping_completed']) ? 'Yes' : 'No';
                    $dateText   = !empty($r['wiping_date']) ? (new DateTime($r['wiping_date']))->format('d-m-Y') : '';
                    $certHtml   = '';
                    if (!empty($r['certificate'])) {
                        $certHtml = '
                        <div id="parenttab-uploaded-file" class="uploaded-file" style="margin-top:4px;font-size:12px;">
                          <div class="upd-file">
                            Uploaded file:<br>
                            <a href="download?id=' . urlencode($r['certificate']) . '" target="_blank">
                              ' . htmlspecialchars($r['certificate_name'] ?? 'certificate_') . '
                            </a>
                          </div>
                        </div>';
                    }
                ?>
                    <tr>
                        <td><?= $index ?></td>
                        <td><?= htmlspecialchars($r['laptop_serial_no']) ?></td>
                        <td><?= htmlspecialchars($r['hdd_sdd_serial_no']) ?></td>
                        <td><?= htmlspecialchars($r['make_name']) ?></td>
                        <td><?= htmlspecialchars($r['type_name']) ?></td>
                        <td><?= htmlspecialchars($r['capacity_name']) ?></td>
                        <td><?= htmlspecialchars($r['software_name_value']) ?></td>
                        <td><?= htmlspecialchars($r['wiping_completed_value']) ?></td>
                        <td><?= htmlspecialchars($dateText) ?></td>
                        <td><?= $certHtml ?></td>
                        <?php if($hasAccess ==1) { ?>
                        <td>
                            <button type="button"
                                class="btn btn-sm btn-outline-secondary asset-edit-btn"
                                data-id="<?= (int)$r['datawiping_asset_id'] ?>">
                                Edit
                            </button>
                        </td>
                        <?php } ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <nav class="mt-2">
        <ul class="pagination pagination-sm">
            <?php
            $baseParams = $_GET;
            $baseParams['datawiping_id'] = $datawiping_id;

            $createUrl = function ($p) use ($baseParams) {
                $baseParams['page'] = $p;
                return 'assetlist?' . http_build_query($baseParams);
            };
            ?>
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= $page > 1 ? $createUrl(1) : '#' ?>">First</a>
            </li>
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= $page > 1 ? $createUrl($page - 1) : '#' ?>">Prev</a>
            </li>
            <?php
            $window = 5;
            $startP = max(1, $page - 2);
            $endP   = min($totalPages, $startP + $window - 1);
            if ($endP - $startP + 1 < $window) {
                $startP = max(1, $endP - $window + 1);
            }
            for ($p = $startP; $p <= $endP; $p++): ?>
                <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                    <a class="page-link" href="<?= $createUrl($p) ?>"><?= $p ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= $page < $totalPages ? $createUrl($page + 1) : '#' ?>">Next</a>
            </li>
            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= $page < $totalPages ? $createUrl($totalPages) : '#' ?>">Last</a>
            </li>
        </ul>
        <span>Showing <?= $totalRecords ? ($startIndex + 1) : 0 ?>–<?= min($startIndex + count($rows), $totalRecords) ?> of <?= $totalRecords ?></span>
    </nav>
</div>

<div class="modal fade" id="assetEditModal" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Asset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" fdprocessedid="7szg3g"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="assetEditId" value="1935">
                <div id="assetEditDynamicFields"></div>


                <div class="modal-footer">
                    <button type="button" id="assetEditSaveBtn" class="btn btn-primary" fdprocessedid="vqcjll">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" fdprocessedid="muqbkc">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php
    $js = <<<JS

let assetEditTemplateHtml = null;

async function loadAssetEditTemplate() {
    if (assetEditTemplateHtml !== null) {
        return assetEditTemplateHtml;
    }

    const blockid   = 2613;
    const totalRows = 1;
    const countBulk = 1;

    return $.ajax({
        url: 'getproductlist',
        method: 'GET',
        dataType: 'html',
        data: {
            blockid:  blockid,
            cnt_rows: totalRows,
            countBulk: countBulk
        }
    }).done(function (html) {
        assetEditTemplateHtml = html;
        
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.error('Failed to load asset edit template:', textStatus, errorThrown);
    });
}
$(function () {
    loadAssetEditTemplate();
});

$(function () {
    if (window.flatpickr) {
        flatpickr('#assetEditWipingDate', {
            dateFormat: 'd-m-Y', 
            allowInput: true
        });
    } else {
        console.warn('flatpickr not loaded');
    }
});
$(document).on('click', '.asset-edit-btn', async function () {
    const id = $(this).data('id');
    if (!id) return;

    startLoading();

    try {
        await loadAssetEditTemplate();
        if (!assetEditTemplateHtml) {
            stopLoading();
            alert('Unable to load edit form template');
            return;
        }

        const resp = await $.ajax({
            url: 'assetdetail',
            method: 'GET',
            dataType: 'json',
            data: { id: id }
        });

        stopLoading();

        if (!resp.success) {
            alert(resp.message || 'Failed to load record');
            return;
        }

        const d = resp.data;

        const containert = $('#assetEditDynamicFields');
        containert.empty();

        const tableT = $('<table><tbody>' + assetEditTemplateHtml + '</tbody></table>');
        const rowD   = tableT.find('tr').first();
        containert.append(rowD);  
        $('#assetEditId').val(d.datawiping_asset_id);

        rowD.find('input.laptop_serial_no').val(d.laptop_serial_no || '');
        rowD.find('input.hdd_sdd_serial_no').val(d.hdd_sdd_serial_no || '');
        buildDropdown(
            rowD.find('select#make_1, select.make')[0],
            d.makeOptions,
            'id',
            'value',
            d.make
        );

        buildDropdown(
            rowD.find('select#type_1')[0],
            d.typeOptions,
            'id',
            'value',
            d.type
        );

        buildDropdown(
            rowD.find('select#capacity_1')[0],
            d.capacityOptions,
            'id',
            'value',
            d.capacity
        );

        buildDropdown(
            rowD.find('select#software_name_1')[0],
            d.softwareOptions,
            'id',
            'value',
            d.software_name
        );

        buildDropdown(
            rowD.find('select#wiping_completed_1')[0],
            d.wipingOptions,
            'id',
            'value',
            d.wiping_completed
        );
        rowD.find('select#make_1, select.make, select.singleselect.section-make')
            .val(d.make || '').trigger('change');

        rowD.find('select#type_1').val(d.type || '').trigger('change');
        rowD.find('select#capacity_1').val(d.capacity || '').trigger('change');
        rowD.find('select#software_name_1').val(d.software_name || '').trigger('change');

        rowD.find('select#wiping_completed_1').val(d.wiping_completed || '').trigger('change');
        
        const dateField = rowD.find('.wiping_date');
            if (dateField.length) {
            const el = dateField[0];
            if (!el._flatpickr) {
                flatpickr(el, {
                    dateFormat: 'd-m-Y',
                    allowInput: true,
                    onOpen(selectedDates, dateStr, instance) {
                        console.log('onOpen selectedDates:', selectedDates,
                                    'currentMonth/year:', instance.currentMonth, instance.currentYear);
                    }
                });
            }
            if (!el._flatpickr) {
                flatpickr(el, {
                dateFormat: 'd-m-Y',
                allowInput: true
                });
            }

            if (d.wiping_date) {
                const parts = d.wiping_date.split('-');
                const jsDate = new Date(parts[0], parts[1] - 1, parts[2]);

                setTimeout(function () {
                el._flatpickr.setDate(jsDate, false);

                const display =
                    ('0' + jsDate.getDate()).slice(-2) + '-' +
                    ('0' + (jsDate.getMonth() + 1)).slice(-2) + '-' +
                    jsDate.getFullYear();
                el.value = display;

                console.log('final wiping_date value after timeout:', el.value);
                }, 600);
            } else {
                el._flatpickr.clear();
                el.value = '';
            }
            }

        const certInputT = rowD.find('input.certificate');
        const certInfoT  = $('<div id="assetEditCertInfo"></div>');
        certInputT.closest('td').append(certInfoT);

        if (d.name && d.certificate) {
            certInfoT.html(
                '<div class="uploaded-file" style="margin-top:4px;font-size:12px;">' +
                    '<div class="upd-file">Uploaded file:<br>' +
                        '<a href="download?id=' + d.certificate + '" target="_blank">' +
                        (d.name || '') +
                        '</a>' +
                    '</div>' +
                '</div>'
            );
        } else {
            certInfoT.empty();
        }

        if ($.fn.select2) {
            rowD.find('select.singleselect').select2();
        }
        if (window.flatpickr) {
            rowD.find('input.wiping_date').each(function () {
                flatpickr(this, {
                    dateFormat: 'd-m-Y',
                    allowInput: true
                });
            });
        }

        $('#assetEditModal').modal('show');
    } catch (e) {
        stopLoading();
        console.error(e);
        alert('Error loading record');
    }
});


function buildDropdown(selector, options, idField, textField, selectedId) {
    const selDD = $(selector);
    selDD.empty();
    (options || []).forEach(function (opt) {
        const id   = opt[idField];
        const text = opt[textField];
        const sel  = (selectedId != null && parseInt(selectedId) === parseInt(id)) ? 'selected' : '';
        selDD.append('<option value="' + id + '" ' + sel + '>' + text + '</option>');
    });
}


$('#assetEditSaveBtn').on('click', function () {

    if (!validatePopup()) {
        e.preventDefault();
        return;
    }

     const id = $('#assetEditId').val();
    if (!id) return;

    const rowD = $('#assetEditDynamicFields').find('tr.product-row').first();

    const fieldsData = {
        id: id,
        laptop_serial_no: rowD.find('input.laptop_serial_no').val().trim(),
        hdd_sdd_serial_no: rowD.find('input.hdd_sdd_serial_no').val().trim(),
        make_id: rowD.find('#make_1').val(),
        type_id: rowD.find('#type_1').val(),
        capacity_id: rowD.find('#capacity_1').val(),
        software_id: rowD.find('#software_name_1').val(),
        wiping_completed: rowD.find('#wiping_completed_1').val(),
        wiping_date: rowD.find('input.wiping_date').val(),
        _csrf: $('#csrfToken').val()
    };

    const fileInput = rowD.find('input.certificate')[0];
    const file = fileInput && fileInput.files ? fileInput.files[0] : null;

    startLoading();

    function updateFieldsThenClose() {
        $.ajax({
            url: 'assetupdate',
            method: 'POST',
            dataType: 'json',
            data: fieldsData,
            success: function (resp) {
                if (!resp.success) {
                    stopLoading();
                    alert(resp.message || 'Update failed');
                    return;
                }
                $('#assetEditModal').modal('hide');
                window.location.reload();
            },
            error: function () {
                stopLoading();
                alert('Error updating record');
            }
        });
    }

    if (!file) {
        updateFieldsThenClose();
        return;
    }

    const fd = new FormData();
    fd.append('certificate_file', file);
    fd.append('_csrf', $('#csrfToken').val());

    $.ajax({
        url: 'assetuploadcertificate?id=' + encodeURIComponent(id),
        method: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (resp) {
            if (!resp.success) {
                stopLoading();
                alert(resp.message || 'Certificate upload failed');
                return;
            }

            $('#assetEditCertInfo').html(
                '<div id="parenttab-uploaded-file" class="uploaded-file" style="margin-top:4px;font-size:12px;"><div class="upd-file">Uploaded file:<br><a href="/download?id=' +resp.attachmentsid + '" target="_blank">' +resp.certificate_name + '</a></div></div>'
            );
            fieldsData.cert_attach_id = resp.attachmentsid;
            updateFieldsThenClose();
        },
        error: function () {
            stopLoading();
            alert('Error uploading certificate');
        }
    });
});
function parseMarkerFromClasses(classAttr) {
        if (!classAttr) return null;
        const classes = classAttr.split(/\s+/);
        for (let i = 0; i < classes.length; i++) {
            const cls = classes[i];
            if (cls.indexOf('~') !== -1) {
                const m = cls.match(/^([A-Z]+)~([A-Z])$/i);
                if (m) {
                    return { type: m[1].toUpperCase(), req: m[2].toUpperCase(), raw: cls };
                }
            }
        }
        return null;
    }

    function validatePopup() {
        let hasError = false;
        let firstInvalid = null;

        $('#assetEditDynamicFields .form-group').removeClass('has-error');
        $('#assetEditDynamicFields .help-block').text('');
        $('#assetEditDynamicFields .productinput').removeClass('is-invalid');

        $('#assetEditDynamicFields .productinput').each(function () {
            const fieldM = $(this);
            const marker = parseMarkerFromClasses(fieldM.attr('class'));
            if (!marker) return;

            const groupD = fieldM.closest('.form-group');
            const helpD = groupD.find('.help-block');

            // For now, treat any *~M as mandatory
            if (marker.req === 'M') {
                const val = $.trim(fieldM.val());

                if (!val) {
                    hasError = true;

                    groupD.addClass('has-error');
                    fieldM.addClass('is-invalid');

                    // custom message per field if needed; simple default:
                    if (!helpD.text()) {
                        helpD.text('This field is required.');
                    }

                    if (!firstInvalid) {
                        firstInvalid = fieldM;
                    }
                }
            }
        });

        if (hasError && firstInvalid) {
            firstInvalid.focus();
        }

        return !hasError;
    }

    // Attach to popup Save / form submit
    $('#assetEditForm').on('submit', function (e) {
        if (!validatePopup()) {
            e.preventDefault();
        }
    });
document.addEventListener('DOMContentLoaded', function() {
    const paginationLinks = document.querySelectorAll('.pagination .page-link');
    
    paginationLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            const currentParams = new URLSearchParams(window.location.search);
            const currentPage = parseInt(currentParams.get('page')) || 1;
            
            const pageText = this.textContent.trim();
            let pageNum = 1;
            
            if (pageText === 'First') {
                pageNum = 1;
            } else if (pageText === 'Last') {
                const href = this.getAttribute('href');
                if (href && href !== '#') {
                    const hrefParams = new URLSearchParams(href.split('?')[1]);
                    pageNum = parseInt(hrefParams.get('page')) || 1;
                } else {
                    pageNum = currentPage; 
                }
            } else if (pageText === 'Next') {
                pageNum = currentPage + 1;
            } else if (pageText === 'Prev') {
                pageNum = Math.max(1, currentPage - 1);
            } else {
                pageNum = parseInt(pageText) || 1;
            }
            
            currentParams.set('page', pageNum);
            
            const newUrl = window.location.pathname + '?' + currentParams.toString();
            
            window.location.href = newUrl;
        });
    });
});
$(function () {
  $('script').each(function () {
    var code = $(this).text() || '';
    if (code.indexOf('Stored Date from PHP') !== -1 &&
        code.indexOf('#wiping_date_1') !== -1) {
      $(this).text('$(function () { /* wiping_date inline script disabled */ });');
      console.log('Disabled inline wiping_date script');
    }
  });
});
JS;
    $this->registerJsFile(
        'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.js',
        ['depends' => [\yii\web\JqueryAsset::class]]
    );
    $this->registerJs($js, \yii\web\View::POS_END);

    ?>