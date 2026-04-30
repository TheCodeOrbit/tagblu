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

$this->title = 'Workflow Template';
// $this->registerJsFile(
//     'https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js',
//     ['position' => \yii\web\View::POS_HEAD]
// );
// $this->registerJsFile('@web/js/ckeditor/ckeditor.js', [
//     'position' => \yii\web\View::POS_HEAD,
// ]);
?>
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
                <img src="<?= $baseUrl; ?>/thememain/img/module-icon/workflowtemplate.png" class=" head-img">
                <span class="sm-modname"><?= $this->title; ?></span>
                <br>
            </div>

            <div class="browse">
                <img src="<?= $baseUrl; ?>/thememain/img/flowbite-refresh-outline.svg"
                    id="refresh-icon"
                    alt="Refresh"
                    title="Refresh Page">


                <a href="<?php echo Yii::$app->urlManager->createUrl('workflowtemplate/create'); ?>" class="add-lead-btn2"><button>+ Add</button></a>

            </div>
        </div>
    </div>
</div>
<div class="select-1">
    <div class="container-d">
        <div class="accordion-item row titlerow">
            <div class="accordion-header col-12 blocktitle2743">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2743">
                    <strong><?= $this->title; ?> </strong>
                </button>
            </div>
            <div id="collapse2743" class="accordion-collapse collapse show" data-bs-parent="#simpleAccordion">
                <div class="accordion-body">
                    <div class="row mb-2">
                        <div class="col-lg-2"></div>
                        <div class="col-lg-8">
                            <!-- listing -->
                            <!-- <div class="card"> -->
                            <div class="row">
                                <div class="col-md-12">

                                </div>
                            </div>
                            <div class="table-responsive pt-2">
                                <table id="relatedtable" class="table table-striped table-bordered" width="100%" cellspacing="0"
                                    style="text-align: left !important">
                                    <thead>
                                        <tr>
                                            <th>Template Name</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>

                            </div>
                        </div>
                        <div class="col-lg-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade lg" id="workflowtemplateModal" tabindex="-1" aria-labelledby="editModalSummeryLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <label id="modal_label_name"> </label>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- hidden fields -->
                <input type="hidden" name="_csrf" id="csrfToken" value="<?= Yii::$app->request->getCsrfToken() ?>">
                <input type="hidden" id="editRecordId" name="editRecordId">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group field-picklist-table_name">
                            <label class="control-label" for="picklist-table_name">
                                Name
                            </label><br>
                            <input id="workflowtemplate-name" name="workflowtemplate-name" class="V~M form-control productinput "/>
                            <div class="help-block"></div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group field-picklist-table_name">
                            <label class="control-label" for="picklist-table_name">
                                Subject
                            </label><br>
                            <input id="workflowtemplate-subject" name="workflowtemplate-subject" class="V~M form-control productinput "/>
                            <div class="help-block"></div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group field-picklist-table_name">
                            <label class="control-label" for="picklist-table_name">
                                Body
                            </label><br>
                            <textarea id="workflowtemplate-body" name="workflowtemplate-body" class="V~M form-control productinput "></textarea>
                            <div class="help-block"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary savetemplate">Save</button>
            </div>
        </div>
    </div>
</div>
<?php $this->registerJsFile('@web/js/workflowtemplate/edit.js', ['depends' => [AdminAsset::class]]);
?>