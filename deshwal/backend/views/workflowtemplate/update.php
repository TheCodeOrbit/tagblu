<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\WorkflowTemplate $model */

$this->title = 'Update Workflow Template: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Workflow Templates', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="workflow-template-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
