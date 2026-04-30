<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Uiinputs $model */

$this->title = 'Create Uiinputs';
$this->params['breadcrumbs'][] = ['label' => 'Uiinputs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="uiinputs-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
