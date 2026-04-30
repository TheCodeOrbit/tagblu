<?php
/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Invalid Request';
?>
<div class="site-invalid">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?php
       echo "Invalid Request";
        ?>
    </div>

    <p>
        <?= Html::a('Go back to homepage', Yii::$app->homeUrl) ?>
    </p>
</div>
