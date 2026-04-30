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
$this->registerCssFile('@web/thememain/css/flatpickr.min.css');
$this->registerCssFile('@web/thememain/css/select2.min.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/multilist-dd.css', ['depends' => [AdminAsset::class]]);
?>

<div class="exportrequest-form">

    <?php $form = ActiveForm::begin(["id" => "pristine-valid-example"]); ?>
    <div class="row mb-2">
            <div class="col-md-3">
                 <!-- <?php $form->field($model, 'ownerid',[
                        'template' => "{label}<span class='red'> *</span><br>{input}\n{hint}\n{error}"
                    ])->dropDownList(
                    $owners,
                    ['prompt' => 'Select Owner','class' => 'form-control productinput singleselect']
                ) ?> -->
                <?= $form->field($model, 'ownerid', [
                    'template' => "{label}<span class='red'> *</span><br>{input}\n{hint}\n{error}"
                ])->dropDownList(
                    $owners,
                    [
                        'prompt' => 'Select Owner',
                        'class' => 'form-control productinput singleselect',
                        'disabled' => true,
                    ]
                ) ?>

                <?= Html::hiddenInput(Html::getInputName($model, 'ownerid'), $model->ownerid) ?>
            </div>
             <div class="col-md-3">
                <?= $form->field($model, 'from_date',[
                        'template' => "{label}<span class='red'> *</span><br>{input}\n{hint}\n{error}"
                    ])->textInput(['class' => 'form-control DD~M flatpickr']) ?>
            </div>
             <div class="col-md-3">
                <?= $form->field($model, 'to_date',[
                            'template' => "{label}<span class='red'> *</span><br>{input}\n{hint}\n{error}"
                        ])->textInput(['class' => 'form-control DD~M flatpickr']) ?>
            </div>
             <div class="col-md-3">                
                <?= $form->field($model, 'module_name',[
                        'template' => "{label}<span class='red'> *</span><br>{input}\n{hint}\n{error}"
                    ])->dropDownList(
                    $modulenames,  // array like ['mod1' => 'Module 1', 'mod2' => 'Module 2']
                    ['prompt' => 'Select Module','class' => 'DD~M form-control productinput singleselect']
                ) ?>
            </div>
            <div class="col-md-3">                
                <?= $form->field($model, 'export_all', [
                        'template' => "{label}{input}\n{hint}\n{error}",
                    ])->checkbox([
                        'class' => 'form-check-input CKB~O',
                        'uncheck' => '0', // 👈 ensures value=0 is posted when unchecked
                        'value' => '1',   // checked value
                    ]) ?>
            </div> 
    </div>
    

        <?= $form->field($model, 'creatorid')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'modifiedby')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'createdtime')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'modifiedtime')->hiddenInput()->label(false) ?>


    <div class="form-group">
        <?= Html::Button('Save', ['class' => 'btn btn-primary savebutton']) ?>
        <a href="<?= $baseUrl?>/exportrequest/list"><button type="button" class="btn mod-close btn-secondary" name="btncancel">Cancel</button></a>
    </div>
    <?php
    $js = <<<JS
    flatpickr(".flatpickr", {
        dateFormat: "d-m-Y",
        defaultDate: new Date(),
        onReady: function(selectedDates, dateStr, instance) {
            console.log('Flatpickr type:', typeof flatpickr);
            console.log('From date picker object:', instance);
        }
    });
    JS;
    $this->registerJs($js);
    $this->registerJsFile('@web/thememain/js/select2.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
    $this->registerJsFile('@web/thememain/js/tetra/single-dd.js', ['depends' => [\yii\web\JqueryAsset::class]]);
    $this->registerJsFile('@web/thememain/js/tetra/multilist-dd.js', ['depends' => [\yii\web\JqueryAsset::class]]);
    $this->registerJsFile('@web/js/exportrequest/edit.js', ['depends' => [AdminAsset::class]]); 
    // $this->registerJsFile('@web/thememain/js/select2.min.js', [
    //     'depends' => [\yii\web\JqueryAsset::class],
    // ]);
    ?>

    <?php ActiveForm::end(); ?>

</div>

