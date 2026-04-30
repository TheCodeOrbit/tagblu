<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\ModuleRelation $model */

$this->title = 'Update Module Relation: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Module Relations', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="module-relation-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
