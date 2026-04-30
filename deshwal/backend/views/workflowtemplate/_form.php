<?php

use backend\assets\AdminAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
$baseUrl = Url::base();
/** @var yii\web\View $this */
/** @var common\models\WorkflowTemplate $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="workflow-template-form p-5">

    <?php $form = ActiveForm::begin(["id" => "pristine-valid-example"]); ?>
    <!-- <input type="hidden" id="csrfToken" value="<?= Yii::$app->request->csrfToken ?>">
    <input type="hidden" id="editRecordId" value="<?= $model->id ?>"> -->
    <input type="hidden" name="mode" id="mode" value="Create">
    <input type="hidden" name="_csrf" id="csrfToken" value="<?= Yii::$app->request->getCsrfToken() ?>">
                <input type="hidden" id="editRecordId" name="editRecordId">
                
    <div class="row mb-2">
        <div class="col-md-4">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true,'class' => 'form-control V~M']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'subject')->textInput(['maxlength' => true,'class' => 'form-control V~M']) ?>
        </div>
         <div class="col-md-4"></div>
    </div>
    <div class="row mb-2">
        <div class="col-md-12">
            <!-- 'id' => 'template-body', -->
            <?= $form->field($model, 'body')->textarea(['rows' => 6]) ?>
        </div>
    </div>
    <div class="row mb-2">
        <div class="form-group">
            <button type="button" class="btn btn-primary savetemplate">Save</button>

             <!-- <?= Html::Button('Save', ['type'=>'button','class' => 'btn btn-primary savetemplate']) ?> -->
            <a href="<?= $baseUrl?>/workflowtemplate/index"><button type="button" class="btn mod-close btn-secondary" name="btncancel">Cancel</button></a>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
<?php $this->registerJsFile('@web/js/workflowtemplate/edit.js', ['depends' => [AdminAsset::class]]); 
// $this->registerJs("CKEDITOR.replace('template-body');", \yii\web\View::POS_END); ?>
