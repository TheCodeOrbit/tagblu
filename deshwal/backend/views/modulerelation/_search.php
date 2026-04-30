<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\ModuleRelationSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="module-relation-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'source_module') ?>

    <?= $form->field($model, 'related_module') ?>

    <?= $form->field($model, 'related_table') ?>

    <?= $form->field($model, 'related_tablekeyid') ?>

    <?php // echo $form->field($model, 'related_fieldname') ?>

    <?php // echo $form->field($model, 'related_recordfieldnme') ?>

    <?php // echo $form->field($model, 'relation_with_account') ?>

    <?php // echo $form->field($model, 'sequence') ?>

    <?php // echo $form->field($model, 'deleted') ?>

    <?php // echo $form->field($model, 'actions') ?>

    <?php // echo $form->field($model, 'related_columns') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
