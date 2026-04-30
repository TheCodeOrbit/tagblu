<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\web\View;
use backend\assets\AdminAsset;
use backend\components\SvgRenderHelper;

$this->title = 'Company Setting';
$baseUrl = Url::base();
$csrfToken = Yii::$app->request->csrfToken;
$csrfParam = Yii::$app->request->csrfParam;
$logo = 'modulesetting';

$this->registerCssFile('@web/thememain/css/flatpickr.min.css');
$this->registerCssFile('@web/thememain/css/select2.min.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/multilist-dd.css', ['depends' => [AdminAsset::class]]);
?>

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

<div class="page-wrapper">
    <div class="container-fluid">

        <div class="page-title-box d-flex align-items-center justify-content-end mb-3">
            <div class="d-flex align-items-center gap-2">
                <?php if(isset(Yii::$app->user->identity) && isset(Yii::$app->user->identity->is_super_admin) && Yii::$app->user->identity->is_super_admin){ ?>
                <?= Html::a(
                    '<i class="fa fa-plus"></i> Add',
                    ['modulesetting/companysettingform'],
                    ['class' => 'btn btn-primary']
                ) ?>
                <?php } ?>
                <button type="button" class="btn btn-success" id="update-status-btn">
                    <i class="fa fa-check"></i> Update Status
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">

                <form method="get" action="<?= Url::to(['modulesetting/companysetting']) ?>" class="row g-2 mb-3">
                    <div class="col-md-4">
                        <input
                            type="text"
                            name="q"
                            value="<?= Html::encode($q) ?>"
                            class="form-control"
                            placeholder="Search company or logo path"
                        >
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa fa-search"></i> Search
                        </button>
                    </div>

                    <div class="col-md-2">
                        <a href="<?= Url::to(['modulesetting/companysetting']) ?>" class="btn btn-secondary w-100">
                            Reset
                        </a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle mb-0">
                        <thead>
                            <tr>
                                <th width="60">#</th>
                                <th width="80" class="text-center">Active</th>
                                <th>Company</th>
                                <th width="180">Logo</th>
                                <th>Logo Path</th>
                                <th width="100">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($rows)): ?>
                                <?php $serial = $pagination->offset + 1; ?>
                                <?php foreach ($rows as $row): ?>
                                    <tr>
                                        <td><?= $serial++ ?></td>

                                        <td class="text-center">
                                            <input
                                                type="radio"
                                                name="active_company"
                                                class="active-company-radio"
                                                value="<?= (int)$row['id'] ?>"
                                                <?= !empty($row['active']) ? 'checked' : '' ?>
                                            >
                                        </td>

                                        <td><?= Html::encode($row['company']) ?></td>

                                        <td>
                                            <?php if (!empty($row['logo_path'])): ?>
                                                <img
                                                    src="<?= $baseUrl . $row['logo_path']; ?>"
                                                    alt="logo"
                                                    style="max-height:50px; max-width:140px; object-fit:contain;"
                                                >
                                            <?php else: ?>
                                                <span class="text-muted">No Logo</span>
                                            <?php endif; ?>
                                        </td>

                                        <td><?= Html::encode($row['logo_path']) ?></td>

                                        <td>
                                            <?= Html::a(
                                                '<i class="fa fa-pencil"></i>',
                                                ['modulesetting/companysettingform', 'id' => $row['id'], 'mode' => 'edit'],
                                                [
                                                    'class' => 'btn btn-sm btn-primary',
                                                    'title' => 'Edit',
                                                    'data-pjax' => '0',
                                                ]
                                            ) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No records found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <!-- <form method="get" action="<?= Url::to(['modulesetting/companysetting']) ?>" class="d-flex align-items-center gap-2 mb-0">
                            <input type="hidden" name="q" value="<?= Html::encode($q) ?>">
                            <label for="per-page" class="mb-0"><strong>Show</strong></label>
                            <select
                                name="per-page"
                                id="per-page"
                                class="form-control"
                                style="width: 90px;"
                                onchange="this.form.submit()"
                            >
                                <option value="10" <?= $pageSize == 10 ? 'selected' : '' ?>>10</option>
                                <option value="25" <?= $pageSize == 25 ? 'selected' : '' ?>>25</option>
                                <option value="50" <?= $pageSize == 50 ? 'selected' : '' ?>>50</option>
                                <option value="100" <?= $pageSize == 100 ? 'selected' : '' ?>>100</option>
                            </select>
                        </form> -->

                        <div>
                            <strong>Total Records:</strong> <?= (int)$totalCount ?>
                        </div>
                    </div>

                    <!-- <div>
                        <?= LinkPager::widget([
                            'pagination' => $pagination,
                            'options' => ['class' => 'pagination mb-0'],
                        ]) ?>
                    </div> -->
                </div>

            </div>
        </div>

    </div>
</div>

<?php
$updateStatusUrl = Url::to(['modulesetting/updatecompanystatus']);

$js = <<<JS
$(document).on('click', '#update-status-btn', function () {
    var selectedId = $('.active-company-radio:checked').val();

    if (!selectedId) {
        alert('Please select a company to activate.');
        return;
    }
     showConfirm('Are you sure? This will replace the current logo for ')
        .then(function (ok) {
            if (!ok) return;
    $.ajax({
        url: '{$updateStatusUrl}',
        type: 'POST',
        data: {
            id: selectedId,
            '{$csrfParam}': '{$csrfToken}'
        },
        success: function (response) {
            if (response.success) {
                console.log(response.message);
                location.reload();
            } else {
                console.log(response.message || 'Failed to update status.');
            }
        },
        error: function () {
            console.log('Something went wrong while updating status.');
        }
    });
    });
});
JS;

$this->registerJs($js, View::POS_READY);
?>