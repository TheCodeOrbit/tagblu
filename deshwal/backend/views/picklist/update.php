<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Picklist $model */

$this->title = 'Update Picklist: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Picklists', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="picklist-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
