<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ProfileModtrackerBasic $model */

$this->title = 'Create Profile Modtracker Basic';
$this->params['breadcrumbs'][] = ['label' => 'Profile Modtracker Basics', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="profile-modtracker-basic-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
