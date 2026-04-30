<?php

use common\models\WorkflowTemplate;

use backend\assets\AdminAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Exportrequest $model */
/** @var yii\widgets\ActiveForm $form */
$baseUrl = Url::base();
// echo $baseUrl;die;

$this->title = 'Workflow Rule';
$this->registerCssFile('@web/thememain/css/jquery.dataTables.min.css', ['depends' => [AdminAsset::class]]); ?>

<style>
    .head-img {
        position: relative;
        top: 0px !important;
        padding: 10px;
        width: 55px;
    }
</style>

<div class="page-content">
    <div class="records table-responsiv">
        <div class="record-header">
            <div class="add">
                <img src="<?= $baseUrl; ?>/thememain/img/module-icon/workflowrule.png" class=" head-img">
                <span class="sm-modname"><?= $this->title; ?></span>
                <br>
            </div>

            <div class="browse">
                <img src="<?= $baseUrl; ?>/thememain/img/flowbite-refresh-outline.svg"
                    id="refresh-icon"
                    alt="Refresh"
                    title="Refresh Page">


                <a href="<?php echo Yii::$app->urlManager->createUrl('workflowrule/create'); ?>" class="add-lead-btn2"><button>+ Add</button></a>

            </div>
        </div>
    </div>
</div>
<div class="select-1">
    <div class="container-d">
        <!-- <div class="accordion-item row titlerow"> -->
            <!-- <div class="accordion-header col-12 blocktitle2743">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2743">
                    <strong><?= $this->title; ?> </strong>
                </button>
            </div> -->
            <!-- <div id="collapse2743" class="accordion-collapse collapse show" data-bs-parent="#simpleAccordion"> -->
                <!-- <div class="accordion-body"> -->
                    <div class="row mb-2">
                        <div class="col-lg-1"></div>
                        <div class="col-lg-10">
                            <!-- listing -->
                            <!-- <div class="card"> -->
                            <div class="row">
                                <div class="col-md-12">

                                </div>
                            </div>
                            <div class="table-responsive pt-2">
                                <table id="dtrecord" class="table table-striped table-bordered" width="100%" cellspacing="0"
                                    style="text-align: left !important">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Module</th>
                                            <th>Template</th>
                                            <th>Active</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>

                            </div>
                        </div>
                        <div class="col-lg-1"></div>
                    </div>
                <!-- </div> -->
            <!-- </div> -->
        <!-- </div> -->
    </div>
</div>
<?php $this->registerJsFile('https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js', ['depends' => [AdminAsset::class]]);
$this->registerJsFile('@web/js/workflowrule/edit.js', ['depends' => [AdminAsset::class]]);
?>