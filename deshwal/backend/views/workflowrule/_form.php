<?php

use backend\assets\AdminAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\WorkflowRule $model */
/** @var yii\widgets\ActiveForm $form */

$baseUrl = Url::base();
$this->registerCssFile('@web/thememain/css/multiple.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/select2.min.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/multilist-dd.css', ['depends' => [AdminAsset::class]]);
?>

<div class="workflow-rule-form">

    <?php $form = ActiveForm::begin([
            "id" => "pristine-valid-example",
            'enableClientValidation' => true,
            'validateOnChange' => false,
            'validateOnBlur' => false,
            'validateOnType' => false,
        ]); ?>

    <div class="row">
        <div class="col-md-3 mb-2">
            <?= $form->field($model, 'module')->dropDownList(
                $moduleList,
                ['prompt' => 'Select Module', 'class' => 'form-control productinput singleselect DD~M']
            ) ?>
        </div>
        <div class="col-md-3 mb-2">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'class' => 'form-control productinput V~M']) ?>
        </div>
        <div class="col-md-3 mb-2">
            <?= $form->field($model, 'trigger_event')->dropDownList(['create' => 'Create', 'update' => 'Update', 'change' => 'Change','approve' => 'Approve',], ['prompt' => '', 'class' => 'form-control productinput singleselect DD~M']) ?>
        </div>
        <div class="col-md-3 mb-2">
            <!-- 'class'=>'form-control productinput singleselect DD~M' -->
            <!-- <?= $form->field($model, 'trigger_fields')->textInput(['maxlength' => true]) ?> -->
            <?= $form->field($model, 'trigger_fields')->dropDownList([''], 
            ["class" => 'form-control productinput singleselect DD~M',
            'data-value' => $model->trigger_fields ]) ?>
        </div>
         <div class="col-md-3 mb-2 section-workflowrule-stage_id">
            <?= $form->field($model, 'stage_id')->dropDownList([''], 
            ["class" => 'form-control productinput multySelect  DD~M',
            'data-value' => $model->stage_id,"multiple"=>true ]) ?>
         </div>
        <!-- <div class="col-md-3 mb-2">
            <?= $form->field($model, 'template_id')->dropDownList($templateList, ['label' => 'Template', 'prompt' => '', 'class' => 'form-control productinput singleselect DD~M']) ?>
        </div> -->
        <div class="col-md-3 mb-2">
            <!-- 'sms' => 'Sms', 'notification' => 'Notification' deepika ma'am told to remove it means do inactive but this is not by picklist table on date 10-03-2026 -->
            <?= $form->field($model, 'trigger_type')->dropDownList(['email' => 'Email', ], ['prompt' => '', 'class' => 'form-control productinput singleselect DD~M']) ?>
        </div>
        <div class="col-md-3 mb-2">
            <?= $form->field($model, 'active')->dropDownList(
                [
                    1 => 'Yes',
                    0 => 'No',
                ],
                ['prompt' => '', "class" => 'form-control productinput singleselect DD~M']
            ) ?>
        </div>
        <!-- <div class="row mb-3"> -->
            <div class="col-md-3">
                <label>
                    <input type="checkbox" id="copy_template_checkbox">
                    Copy existing template
                </label>
            </div>

            <div class="col-md-3" id="copy_template_section" style="display:none;">
                <?= $form->field($model, 'copy_template_id')->label('Select Template')->dropDownList(
                    $templateList,
                    ['prompt' => 'Select Template', 'class' => 'form-control singleselect']
                ) ?>
            </div>
        <!-- </div> -->

    </div>
    <!-- template form start from here -->
    <div class="accordion-item row titlerow pt-3">
        <div class="accordion-header col-12 blocktitle2743">
            <!-- <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2743"> -->
            <strong class="workflow-template-title">Workflow Template </strong>
            <!-- </button> -->
        </div>

        <div id="collapse2743" class="accordion-collapse collapse show" data-bs-parent="#simpleAccordion">
            <!-- <div class="accordion-body">
                <div class="row mb-2">
                    <div class="col-md-4">
                        <?= $form->field($templatemodel, 'name')->textInput(['maxlength' => true, 'class' => 'form-control V~M']) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($templatemodel, 'subject')->textInput(['maxlength' => true, 'class' => 'form-control V~M']) ?>
                    </div>
                    <div class="col-md-4"></div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-8">
                        <?= $form->field($templatemodel, 'body')->textarea(['rows' => 6]) ?>
                    </div>
                    <div class="col-md-4">
                        <label>Available Fields</label>
                        <select id="availableFields" size="8" class="form-control">
                        </select>
                    </div>
                </div>
            </div> -->
            <div class="accordion-body">
                <div class="row">
                    <!-- LEFT SIDE -->
                    <div class="col-md-8">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <?= $form->field($templatemodel, 'name')
                                    ->textInput(['maxlength' => true, 'class' => 'form-control V~M']) ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($templatemodel, 'subject')
                                    ->textInput(['maxlength' => true, 'class' => 'form-control V~M']) ?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <?= $form->field($templatemodel, 'body')->textarea(['rows' => 10]) ?>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT SIDE -->
                    <div class="col-md-4">
                        <label>Available Fields</label>
                        <!-- <select id="availableFields" size="12" class="form-control">
                        </select> -->
                        <div id="availableFields" class="form-control workflow-template_availableFields" >
                                <!-- JS will insert draggable items here -->
                            </div>
                    </div>
                </div>
            </div>

        </div>
    </div>



</div>
<div class="row mb-2">
    <div class="col-md-4">
        <div class="form-group">
            <?= Html::Button('Save', ['class' => 'btn btn-primary savebutton']) ?>
            <a href="<?= $baseUrl ?>/workflowrule/index"><button type="button" class="btn mod-close btn-secondary" name="btncancel">Cancel</button></a>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>

</div>

<?php
// $this->registerJsFile('@web/js/ckeditor/ckeditor.js', [
//     'position' => \yii\web\View::POS_HEAD
// ]);

$this->registerJs("
    window.modulefields = '{$model->trigger_fields}';
", \yii\web\View::POS_HEAD);
$this->registerJsFile('@web/thememain/js/select2.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('@web/thememain/js/tetra/single-dd.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('@web/thememain/js/tetra/multilist-dd.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('@web/js/workflowrule/edit.js', ['depends' => [AdminAsset::class]]);
// Initialize CKEditor 5 on page ready
// $this->registerJs("
// ClassicEditor
//     .create(document.querySelector('#workflowtemplate-body'))
//     .catch(error => console.error(error));
// ", \yii\web\View::POS_READY); 
$this->registerJs("
window.workflowEditor = null;

ClassicEditor
    .create(document.querySelector('#workflowtemplate-body'))
    .then(editor => {
        window.workflowEditor = editor;   // store globally
    })
    .catch(error => console.error(error));
", \yii\web\View::POS_READY);
?>