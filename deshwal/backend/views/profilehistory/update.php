<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ProfileModtrackerBasic $model */

$this->title = 'Update Profile Modtracker Basic: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Profile Modtracker Basics', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="profile-modtracker-basic-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
