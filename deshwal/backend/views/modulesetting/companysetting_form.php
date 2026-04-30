<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use backend\assets\AdminAsset;

$this->title = 'Company Setting Form';
$baseUrl = Url::base();
$csrfToken = Yii::$app->request->csrfToken;
$csrfParam = Yii::$app->request->csrfParam;
$mode = (Yii::$app->request->get('mode') == 'edit') ? 'update' : 'create';

$this->registerCssFile('@web/thememain/css/flatpickr.min.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/select2.min.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/multilist-dd.css', ['depends' => [AdminAsset::class]]);
?>

<?php
$record = $record ?: [];
$id = $record['id'] ?? '';
$company = $record['company'] ?? '';
$logo_path = $record['logo_path'] ?? '';
$active = !empty($record['active']) ? 1 : 0;
?>

<div class="page-wrapper">
    <div class="container-fluid">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0"><?= Html::encode($this->title) ?></h4>
            <?= Html::a(
                '<i class="fa fa-arrow-left"></i> Back',
                ['modulesetting/companysetting'],
                ['class' => 'btn btn-secondary']
            ) ?>
        </div>

        <div class="card">
            <div class="card-body">

                <form id="company-setting-form" enctype="multipart/form-data">
                    <input type="hidden" name="<?= Html::encode($csrfParam) ?>" value="<?= Html::encode($csrfToken) ?>">
                    <input type="hidden" name="id" value="<?= Html::encode($id) ?>">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Company <span class="text-danger">*</span></label>
                            <input type="text" name="company" class="form-control" value="<?= Html::encode($company) ?>" placeholder="Enter company name">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Logo</label>
                            <input type="file" name="logo_file" class="form-control" accept=".png,.jpg,.jpeg,.svg">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Active</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="active" value="1" id="activeCheck" <?= $active ? 'checked' : '' ?>>
                                <label class="form-check-label" for="activeCheck">
                                    Mark as Active
                                </label>
                            </div>
                        </div>

                        <?php if (!empty($logo_path)): ?>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Current Logo</label>
                                <div>
                                    <img src="<?= $baseUrl . $logo_path; ?>" alt="Current Logo" style="max-height:80px;max-width:200px;object-fit:contain;border:1px solid #ddd;padding:5px;">
                                </div>
                                <div class="mt-1 text-muted small"><?= Html::encode($logo_path) ?></div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            <?= !empty($id) ? '<i class="fa fa-save"></i> Update' : '<i class="fa fa-save"></i> Save' ?>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<?php
$saveUrl = Url::to(['modulesetting/savecompanysetting']);

$js = <<<JS
$(document).on('submit', '#company-setting-form', function (e) {
    e.preventDefault();

    var form = document.getElementById('company-setting-form');
    var formData = new FormData(form);

    if ($.trim($('input[name="company"]').val()) === '') {
        alert('Company is required.');
        $('input[name="company"]').focus();
        return false;
    }
    showConfirm('Are you sure? This will replace the current logo.')
        .then(function (ok) {
            if (!ok) return;                
    $.ajax({
        url: '{$saveUrl}',
        type: 'POST',
        data: formData,
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function (response) {
            if (response.success) {
                alert(response.message);
                if (response.redirect) {
                    window.location.href = response.redirect;
                }
            } else {
                alert(response.message);
            }
        },
        error: function () {
            alert('Something went wrong while saving company setting.');
        }
    });
                        });
});
JS;

$this->registerJs($js);
?>