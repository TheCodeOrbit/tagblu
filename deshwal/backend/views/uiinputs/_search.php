<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\UiinputsSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="uiinputs-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'hidden_field') ?>

    <?= $form->field($model, 'password') ?>

    <?= $form->field($model, 'textarea') ?>

    <?php // echo $form->field($model, 'file') ?>

    <?php // echo $form->field($model, 'checkbox') ?>

    <?php // echo $form->field($model, 'listbox') ?>

    <?php // echo $form->field($model, 'dropdown_single') ?>

    <?php // echo $form->field($model, 'checkboxlist') ?>

    <?php // echo $form->field($model, 'radio_button') ?>

    <?php // echo $form->field($model, 'referencetype') ?>

    <?php // echo $form->field($model, 'DateTimePicker') ?>

    <?php // echo $form->field($model, 'label') ?>

    <?php // echo $form->field($model, 'dropdown_multipe') ?>

    <?php // echo $form->field($model, 'MonthYearPicker') ?>

    <?php // echo $form->field($model, 'DateTime') ?>

    <?php // echo $form->field($model, 'date') ?>

    <?php // echo $form->field($model, 'BatchList') ?>

    <?php // echo $form->field($model, 'maskingdate') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
