<?php

use backend\assets\AdminAsset;
use yii\helpers\Html;
use yii\helpers\Url;

$isEdit = !empty($model);
$this->title = $isEdit ? 'Edit PDF Header & Footer' : 'Add PDF Header & Footer';
$baseUrl = Url::base();
// echo $baseUrl;die;
$this->registerCssFile('@web/thememain/css/flatpickr.min.css');
$this->registerCssFile('@web/thememain/css/select2.min.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/multilist-dd.css', ['depends' => [AdminAsset::class]]);
?>

<style>body{font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,sans-serif;background:#f4f6f9}.card{margin:20px auto;background:#fff;border-radius:10px;box-shadow:0 8px 24px #0f172a14;border:1px solid #e5e7eb;overflow:hidden}.card-header{padding:18px 24px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;gap:12px}.card-header h1{margin:0;font-size:22px;font-weight:600;color:#111827;flex:1}.back-btn{padding:6px 12px;border-radius:6px;border:none;background:#e5e7eb;font-size:13px;cursor:pointer}.back-btn:hover{background:#d1d5db}.card-body{padding:18px 24px 22px;background:#f9fafb}.form-grid{display:grid;grid-template-columns:1fr 1fr;column-gap:24px;row-gap:14px}.form-row{display:flex;flex-direction:column}.form-row label{font-size:13px;font-weight:600;color:#374151;margin-bottom:6px}.form-row small{font-size:11px;color:#9ca3af;margin-top:3px}.form-control{width:100%;padding:9px 13px;border-radius:10px;border:1px solid #e5e7eb;font-size:13px;background:#fff;outline:none}.form-control:disabled{background:#f3f4f6;color:#9ca3af;cursor:not-allowed}textarea.form-control{min-height:110px;resize:vertical;font-family:'Courier New',monospace}.preview-box{background:#fff;border-radius:10px;border:1px solid #e5e7eb;min-height:110px;padding:12px;font-size:12px;overflow:auto}.preview-label{font-size:11px;color:#6b7280;margin-bottom:4px}.form-actions{grid-column:1 / -1;display:flex;justify-content:flex-end;gap:10px;margin-top:10px}.btn-secondary{padding:8px 16px;border-radius:8px;border:none;background:#e5e7eb;font-size:13px;cursor:pointer}.btn-primary{padding:8px 16px;border-radius:8px;border:none;background:#2563eb;color:#fff;font-size:13px;cursor:pointer}.btn-primary:hover{background:#1d4ed8}.btn-secondary:hover{background:#d1d5db}
    .layout-grid{
    display:grid;
    grid-template-columns: minmax(0, 1.2fr) minmax(0, 1fr);
    gap:24px;
    align-items:flex-start;
}

@media (max-width: 900px){
    .layout-grid{
        grid-template-columns:1fr;
    }
    .layout-right{
        margin-top:10px;
    }
}
</style>

<div class="card">
    <div class="card-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <button class="back-btn" id="back-pdf-btn" onclick="window.location.href='<?= Url::to(['modulesetting/pdf']) ?>'">
            Back to List
        </button>
    </div>
    <div class="card-body">
        <form id="pdf-form">
            <input type="hidden" name="<?= $csrfParam ?>" value="<?= $csrfToken ?>">
            <input type="hidden" id="pdf-id" name="id" value="<?= $isEdit ? (int)$model['id'] : '' ?>">

            <div class="layout-grid">
                <!-- LEFT COLUMN: FORM FIELDS -->
                <div class="layout-left">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="tab-select">Select Tab *</label>
                            <select id="tab-select" name="tab_id" class="form-control" <?= $isEdit ? 'disabled' : '' ?>>
                                <option value="">Choose Tab</option>
                                <?php foreach ($tabs as $t): ?>
                                    <option value="<?= (int)$t['tabid'] ?>"
                                        <?= $isEdit && (int)$t['tabid'] === (int)$model['tab_id'] ? 'selected' : '' ?>>
                                        <?= Html::encode($t['tablabel']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small>Select tab for which you want to configure PDF header and footer</small>
                        </div>

                        <div class="form-row">
                            <label for="name-input">
                                Configuration Name
                                <span style="color: #ef4444;">*</span>
                            </label>
                            <input
                                type="text"
                                id="name-input"
                                name="name"
                                class="form-control"
                                maxlength="255"
                                value="<?php echo $isEdit && !empty($model['name']) ? Html::encode($model['name']) : ''; ?>"
                                placeholder="e.g. Invoice PDF, Quotation Layout, Dispatch Note"
                                required
                                style="font-weight: 500;"
                            >
                            <small>A short, unique label for this PDF layout within the selected tab.</small>
                        </div>

                        <div class="form-row">
                            <label for="status">Status</label>
                            <select id="status" name="status" class="form-control" <?= $isEdit ? '' : 'disabled' ?>>
                                <option value="1" <?= $isEdit && (int)$model['status'] === 1 ? 'selected' : '' ?>>Active</option>
                                <option value="0" <?= $isEdit && (int)$model['status'] === 0 ? 'selected' : '' ?>>Inactive</option>
                            </select>
                            <small>Choose whether this configuration is active</small>
                        </div>

                        <div class="form-row">
                            <label for="stamp">Stamp</label>
                            <input type="file" id="stamp-file" name="stamp_file" accept=".png,.jpg,.jpeg,image/png,image/jpeg" style="font-size:13px;" <?= $isEdit ? '' : 'disabled' ?>>
                            <small style="display:block; margin-top:4px; color:#9ca3af;">
                                Allowed formats: PNG, JPG, JPEG. Max size: 50 KB.
                            </small>
                            <?php if (!empty($stampAttachment) && isset($stampAttachment['path'])): ?>
                                <div class="upd-file" style="margin-top:6px; font-size:12px; color:#4b5563;">
                                    Uploaded file:<br>
                                    <a href="<?= Url::to([
                                            'modulesetting/download',
                                            'type'     => 'attachment',
                                            'filename' => $stampAttachment['attachmentsid'],
                                        ]) ?>"
                                       target="_blank"
                                       style="color:#2563eb; text-decoration:none;">
                                        <?= Html::encode($stampAttachment['name'] ?: $stampAttachment['name']) ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="form-row">
                            <label>Header HTML Content</label>
                            <textarea id="header-content" name="header_content" class="form-control" style="min-height:220px;" <?= $isEdit ? '' : 'disabled' ?>><?= $isEdit ? Html::encode($model['header_content']) : '' ?></textarea>
                            <small>HTML that will appear at the top of the PDF for this tab</small>
                        </div>

                        <div class="form-row">
                            <label>Footer HTML Content</label>
                            <textarea id="footer-content" name="footer_content" class="form-control" style="min-height:220px;" <?= $isEdit ? '' : 'disabled' ?>><?= $isEdit ? Html::encode($model['footer_content']) : '' ?></textarea>
                            <small>HTML that will appear at the bottom of the PDF for this tab</small>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn-secondary" id="btn-preview">Render Preview</button>
                            <button type="submit" class="btn-primary" id="btn-save" <?= $isEdit ? '' : 'disabled' ?>>Save</button>
                        </div>
                    </div>
                </div>

                <div class="layout-right">
                    <div class="form-row">
                        <label>PDF Layout Preview</label>
                        <div id="pdf-layout-preview" style="background:#ffffff;border-radius:10px;border:1px solid #e5e7eb;min-height:300px;padding:12px;font-size:13px;color:#4b5563;">
                            <div id="layout-header" style="border-bottom:1px dashed #d1d5db; padding-bottom:8px; margin-bottom:8px; display:none;"></div>
                            <div id="layout-body" style="min-height:200px; padding:8px 0; text-align:center; color:#9ca3af;">
                                Sample PDF body content area
                            </div>
                            <div id="layout-footer" style="border-top:1px dashed #d1d5db; padding-top:8px; margin-top:8px; display:none;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
$pdfSave       = Url::to(['modulesetting/save-pdf-header-footer']);
$pdfUrl        = Url::to(['modulesetting/pdf']);
$downloadBase = Url::to(['modulesetting/download']);
$js = <<<JS
const tabSelect      = document.getElementById('tab-select');
const nameInput      = document.getElementById('name-input');
const headerInput    = document.getElementById('header-content');
const footerInput    = document.getElementById('footer-content');
const statusSelect   = document.getElementById('status');
const headerPreview  = document.getElementById('header-preview');
const footerPreview  = document.getElementById('footer-preview');
const form           = document.getElementById('pdf-form');
const btnPreview     = document.getElementById('btn-preview');
const btnSave        = document.getElementById('btn-save');
const idInput        = document.getElementById('pdf-id');
const layoutHeader   = document.getElementById('layout-header');
const layoutFooter   = document.getElementById('layout-footer');
const layoutBody     = document.getElementById('layout-body');
const stampInput     = document.getElementById('stamp-file');
const isEdit = '$isEdit';
const pdfSave = "$pdfSave";
const pdfUrl = "$pdfUrl";
const downloadBase = "$downloadBase";
const MAX_STAMP_SIZE_BYTES = 100 * 1024;
const ALLOWED_STAMP_EXT = ['png', 'jpg', 'jpeg'];
if(!isEdit)
   disableFormFields(true);
else{
   renderLayoutPreview();
}
function initSelect2() {
    
    $('#tab-select').select2({
        placeholder: 'Choose Tab',
        allowClear: true,
        width: '100%'
    });
    $('#tab-select').on('change', function () {
        console.log('changes');
        if (this.value) {
            console.log('came');
            disableFormFields(false);
        } else {
            console.log('came not');
            disableFormFields(true);
        }
    });
}
initSelect2();
if (stampInput) {
    stampInput.addEventListener('change', function () {
        const file = this.files && this.files[0] ? this.files[0] : null;
        if (!file) {
            return;
        }

        const fileName = file.name || '';
        const ext = fileName.split('.').pop().toLowerCase();

        if (!ALLOWED_STAMP_EXT.includes(ext)) {
            alert('Invalid stamp format. Allowed formats: PNG, JPG, JPEG.');
            this.value = '';
            return;
        }

        if (file.size > MAX_STAMP_SIZE_BYTES) {
            const sizeKB = (file.size / 1024).toFixed(1);
            alert('Stamp file is too large (' + sizeKB + ' KB). Maximum allowed size is 100 KB.');
            this.value = ''; 
            return;
        }
    });
}
$('#back-pdf-btn').on('click', function () {
    window.location = pdfUrl;
});

function renderLayoutPreview() {
    const headerHtml = headerInput.value.trim();
    const footerHtml = footerInput.value.trim();

    if (headerHtml) {
        layoutHeader.style.display = 'block';
        layoutHeader.innerHTML = headerHtml;
    } else {
        layoutHeader.style.display = 'none';
        layoutHeader.innerHTML = '';
    }

    if (footerHtml) {
        layoutFooter.style.display = 'block';
        layoutFooter.innerHTML = footerHtml;
    } else {
        layoutFooter.style.display = 'none';
        layoutFooter.innerHTML = '';
    }

    layoutBody.innerHTML = 'Sample PDF body content area';
}

headerInput.addEventListener('input', renderLayoutPreview);
footerInput.addEventListener('input', renderLayoutPreview);

if (btnPreview) {
    btnPreview.addEventListener('click', function (e) {
        e.preventDefault();
        renderLayoutPreview();
    });
}

document.addEventListener('DOMContentLoaded', renderLayoutPreview);

form.addEventListener('submit', function (e) {
    e.preventDefault();
    disableFormFields(false);
    $('#tab-select').disabled    = false;
    if (!$('#tab-select').val()) {
        alert('Please select a tab first.');
        return;
    }
    
    if (stampInput && stampInput.files.length) {
        const file = stampInput.files[0];
        const fileName = file.name || '';
        const ext = fileName.split('.').pop().toLowerCase();

        if (!ALLOWED_STAMP_EXT.includes(ext)) {
            alert('Invalid stamp format. Allowed formats: PNG, JPG, JPEG.');
            return;
        }
        if (file.size > MAX_STAMP_SIZE_BYTES) {
            const sizeKB = (file.size / 1024).toFixed(1);
            alert('Stamp file is too large (' + sizeKB + ' KB). Maximum allowed size is 50 KB.');
            return;
        }
    }

    const formData = new FormData(form);
    formData.set('tab_id', $('#tab-select').val());
    $.ajax({
        url: pdfSave,
        type: 'POST',
        data: formData,
        processData: false, 
        contentType: false, 
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                alert('Saved successfully');
                window.location.href = pdfUrl;
            } else {
                alert(res.message || 'Save failed');
            }
        },
        error: function () {
            alert('Save failed');
        }
    });
});



function disableFormFields(disabled) {
    headerInput.disabled  = disabled;
    footerInput.disabled  = disabled;
    statusSelect.disabled = disabled;
    btnSave.disabled      = disabled;
    btnPreview.disabled   = disabled;
    stampInput.disabled   = disabled;
    // tabSelect.disabled    = disabled;
    nameInput.disabled    = disabled;
}
JS;
$this->registerJs($js, \yii\web\View::POS_END);
$this->registerJsFile('@web/thememain/js/select2.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('@web/thememain/js/tetra/single-dd.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('@web/thememain/js/tetra/multilist-dd.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>