<?php

use backend\assets\AdminAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Exportrequest $model */
/** @var yii\widgets\ActiveForm $form */

$baseUrl = Url::base();
// echo $baseUrl;die;

$this->title = 'Profile History Detail';
$this->registerCssFile('@web/thememain/css/jquery.dataTables.min.css', ['depends' => [AdminAsset::class]]); ?>

<style>
    .head-img {
        position: relative;
        top: 0px !important;
        padding: 10px;
        width: 55px;
    }

    #dtrecord tbody tr {
        cursor: pointer;
    }
</style>

<div class="page-content">
    <div class="records table-responsiv">
        <div class="record-header">
            <div class="add">
                <img src="<?= $baseUrl; ?>/thememain/img/module-icon/profilehistory.svg" class=" head-img">
                <span class="sm-modname"><?= $this->title; ?></span>
                <br>
            </div>

            <div class="browse">
                <img src="<?= $baseUrl; ?>/thememain/img/flowbite-refresh-outline.svg"
                    id="refresh-icon"
                    alt="Refresh"
                    title="Refresh Page">
                    <button class="btn btn-sm btn-secondary" id="backToProfilehistory">← Back</button>
            </div>
        </div>
    </div>
</div>
<div class="select-1">
    <div class="container-d">
        <!-- <div class="accordion-item row titlerow"> -->

        <!-- <div id="collapse2743" class="accordion-collapse collapse show" data-bs-parent="#simpleAccordion"> -->
        <!-- <div class="accordion-body"> -->
        <div class="row mb-2">
            <div class="col-lg-1"></div>
            <div class="col-lg-10">
                <select id="tabFilter" class="form-control mb-3">
                    <option value="">-- Select Tab --</option>
                    <?php foreach ($data as $tabName => $fields): ?>
                        <option value="<?= htmlspecialchars($tabName) ?>">
                            <?= htmlspecialchars($tabName) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <table id="historyTable" class="table table-bordered">
                    <thead>
                         <tr>
                            <th rowspan="2">Field</th>
                            <th colspan="2">Previous Value</th>
                            <th colspan="2">New Value</th>
                        </tr>
                        <tr>
                            <th>Visible</th>
                            <th>Readonly</th>
                            <th>Visible</th>
                            <th>Readonly</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="col-lg-1"></div>
        </div>
        <!-- </div> -->
        <!-- </div> -->
        <!-- </div> -->
    </div>
</div>
<?php
$this->registerJs("
    window.allData = " . json_encode($data) . ";
", \yii\web\View::POS_HEAD);
?>
<?php $this->registerJsFile('https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js', ['depends' => [AdminAsset::class]]);
$this->registerJsFile('@web/js/profilehistory/detailedit.js', ['depends' => [AdminAsset::class]]);
?>