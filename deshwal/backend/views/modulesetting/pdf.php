<?php

use backend\components\SvgRenderHelper;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Pdf Header & Footer  Management';
$baseUrl = Url::base();
$csrfToken = Yii::$app->request->csrfToken;
$csrfParam = Yii::$app->request->csrfParam;
$logo = 'modulesetting';
?>
<style>
    .card{margin:0px auto;background:#fff;border-radius:10px;box-shadow:0 8px 24px #0f172a14;border:1px solid #e5e7eb;overflow:hidden}.card-header{padding:18px 24px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;gap:12px}.card-header h1{margin:0;font-size:22px;font-weight:600;color:#111827;flex:1}.add-btn{padding:6px 14px;cursor:pointer;background:#2563eb;color:#fff;border:none;border-radius:6px;font-size:13px;font-weight:500}.add-btn:hover{background:#1d4ed8}.card-body{padding:16px 24px 22px}.table{width:100%;border-collapse:collapse;font-size:13px}.table th,.table td{padding:8px 10px;border-bottom:1px solid #e5e7eb}.table th{text-align:left;background:#f9fafb;font-weight:600;color:#374151}.table tr:nth-child(even) td{background:#f9fafb}.fields-btn{padding:4px 10px;border-radius:6px;font-size:12px;border:none;cursor:pointer;background:#eef2ff;color:#4f46e5}.fields-btn:hover{background:#e0e7ff}.empty-row{text-align:center;padding:12px;color:#9ca3af}.simple-modal{position:fixed;inset:0;display:none;align-items:center;justify-content:center;z-index:9999;font-family:inherit}.simple-modal-backdrop{position:absolute;inset:0;background:#0f172a8c}.simple-modal-dialog{position:relative;background:#fff;border-radius:18px;box-shadow:0 24px 60px #0f172a73;width:640px;max-width:calc(100% - 40px);padding:20px 22px 18px;z-index:1;border:1px solid #e5e7eb}.simple-modal-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px}.simple-modal-close{border:none;background:transparent;font-size:20px;line-height:1;cursor:pointer;color:#6b7280}.simple-modal-body{max-height:70vh;overflow-y:auto}
</style>

<div class="page-content">
  <div class="records table-responsiv">
    <div class="record-header">
      <div class="add" style="
    padding: 12px;
    margin-top: 10px;
">
         <span class="icons-coll " >
            <?= SvgRenderHelper::renderIcon($logo.'.svg',true); ?>
        </span>
        <span class="sm-modname"><strong><?= $this->title; ?></strong></span>
        <br>
      </div>

      
    </div>
  </div>
</div>

<div class="card">
    <div class="card-header">
        <h1>PDF Header & Footer</h1>
        <button class="add-btn" id="add-pdf-content">Add PDF</button>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>S.N.</th>
                    <th>Tab Name</th>
                    <th>Name</th>
                    <th>Preview</th>
                    <th>Edit</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pdfConfigs)): ?>
                    <tr>
                        <td colspan="3" class="empty-row">
                            No records found. Click "Add PDF" to create one.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $sn = 1;
                    foreach ($pdfConfigs as $row): ?>
                        <tr>
                            <td><?= $sn++; ?></td>
                            <td><?= Html::encode($row['tablabel'] ?: ('Tab ID ' . $row['tab_id'])) ?></td>
                            <td><?= Html::encode($row['name'] ?: ('Name ' . $row['name'])) ?></td>
                            <td>
                                <button class="fields-btn preview-pdf-content"
                                    data-header="<?= Html::encode($row['header_content']) ?>"
                                    data-footer="<?= Html::encode($row['footer_content']) ?>">
                                    Preview
                                </button>
                            </td>
                            <td>
                                <button class="fields-btn edit-pdf-content"
                                    data-id="<?= (int)$row['id'] ?>">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<div id="pdf-preview-modal" class="simple-modal">
    <div class="simple-modal-backdrop"></div>
    <div class="simple-modal-dialog">
        <div class="simple-modal-header">
            <span>PDF Layout Preview</span>
            <button type="button" class="simple-modal-close" id="close-preview-modal">&times;</button>
        </div>
        <div class="simple-modal-body">
            <div style="border:1px solid #e5e7eb; border-radius:8px; padding:16px; background:#ffffff;">
                <div id="preview-header"
                    style="border-bottom:1px dashed #d1d5db; padding-bottom:10px; margin-bottom:10px;">
                </div>
                <div id="preview-body"
                    style="min-height:120px; padding:10px 0; font-size:13px; color:#4b5563; text-align:center;">
                    Sample PDF body content area
                </div>
                <div id="preview-footer"
                    style="border-top:1px dashed #d1d5db; padding-top:10px; margin-top:10px;">
                </div>
            </div>
        </div>
    </div>
</div>


<?php
$pdfForm       = Url::to(['modulesetting/pdf-form']);
$js = <<<JS
const pdfForm = '$pdfForm';
$('#add-pdf-content').on('click', function () {
    window.location = pdfForm
});
$(document).on('click', '.edit-pdf-content', function () {
    const id = $(this).data('id');
    window.location = pdfForm + '?id=' + encodeURIComponent(id);
});
$(document).on('click', '.preview-pdf-content', function () {
    const headerHtml = $(this).data('header') || '';
    const footerHtml = $(this).data('footer') || '';

    $('#preview-header').html(headerHtml || '<span style="color:#9ca3af;">No header content</span>');
    $('#preview-footer').html(footerHtml || '<span style="color:#9ca3af;">No footer content</span>');

    $('#pdf-preview-modal').css('display', 'flex'); 
});

$('#close-preview-modal, .simple-modal-backdrop').on('click', function () {
    $('#pdf-preview-modal').css('display', 'none');
});
JS;

$this->registerJs($js, \yii\web\View::POS_END);
?>