<?php

use backend\assets\AdminAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\WorkflowRule $model */
/** @var yii\widgets\ActiveForm $form */
$this->registerCssFile('@web/thememain/css/multiple.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/select2.min.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/multilist-dd.css', ['depends' => [AdminAsset::class]]);
?>

<div class="workflow-rule-form">

    <?php $form = ActiveForm::begin(["id" => "pristine-valid-example"]); ?>
    <div class="row mb-2">
        <div class="col-md-3">
            <?= $form->field($model, 'module')->dropDownList(
                $moduleList,
                ['prompt' => 'Select Module','class'=>'form-control productinput singleselect DD~M']
            ) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true,'class'=>'form-control productinput V~M']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'trigger_event')->dropDownList(['create' => 'Create', 'update' => 'Update', 'change' => 'Change',], ['prompt' => '','class'=>'form-control productinput singleselect DD~M']) ?>
        </div>
        <div class="col-md-3">
            <!-- 'class'=>'form-control productinput singleselect DD~M' -->
            <!-- <?= $form->field($model, 'trigger_fields')->textInput(['maxlength' => true]) ?> -->
             <?= $form->field($model, 'trigger_fields')->dropDownList(['prompt' => 'Select',]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'template_id')->dropDownList($templateList,['label'=>'Template','prompt' => '','class'=>'form-control productinput singleselect DD~M']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'trigger_type')->dropDownList(['email' => 'Email', 'sms' => 'Sms', 'notification' => 'Notification',], ['prompt' => '','class'=>'form-control productinput singleselect DD~M']) ?>
        </div>
        <div class="col-md-3">
           <?= $form->field($model, 'active')->dropDownList(
                [
                    1 => 'Yes',
                    0 => 'No',
                ],
                ['prompt' => 'Select',"class"=>'form-control productinput singleselect DD~M']
            ) ?>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-md-4">
            <div class="form-group">
                <?= Html::Button('Save', ['class' => 'btn btn-primary savebutton']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php  $this->registerJsFile('@web/thememain/js/select2.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
    $this->registerJsFile('@web/thememain/js/tetra/single-dd.js', ['depends' => [\yii\web\JqueryAsset::class]]);
    $this->registerJsFile('@web/thememain/js/tetra/multilist-dd.js', ['depends' => [\yii\web\JqueryAsset::class]]);
    $this->registerJsFile('@web/js/workflowrule/edit.js', ['depends' => [AdminAsset::class]]); 