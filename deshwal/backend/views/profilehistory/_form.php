<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\ProfileModtrackerBasic $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="profile-modtracker-basic-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'crmid')->textInput() ?>

    <?= $form->field($model, 'module')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'whodid')->textInput() ?>

    <?= $form->field($model, 'changedon')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
