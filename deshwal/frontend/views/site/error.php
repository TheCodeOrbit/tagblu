<?php
/* @var $this yii\web\View */
use yii\helpers\Html;

$this->title = 'Page Not Found';
?>
<div class="site-error">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>Sorry, the page you are looking for could not be found.</p>
    <p>Try going back to the <a href="<?= Yii::$app->homeUrl ?>">homepage</a>.</p>
</div>
