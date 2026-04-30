<?php

/** @var \yii\web\View $this */
/** @var string $content */

use common\widgets\Alert;
use backend\assets\LoginAsset;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

LoginAsset::register($this);
$baseUrl = Yii::$app->HomeUrl;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
  
</head>
<body>
<?php $this->beginBody() ?>


  <div class="container-fluid">
    <div class="left-section">
      <h1>Trusted By 1,000+ Happy Customers</h1>
      <p>
        18 years of excellence, "we" are a place that brings together individuals with ideas and passion. A place where people can create great things. DEV IT is a platform where everyone challenges themselves to create a better individual.
      </p>
      <img src="<?= $baseUrl; ?>thememain/img/login/Privacypolicy.svg" alt="Illustration" class="illustration">
    </div>

    <div class="right-section">
    <?= $content ?>
     </div>
  </div>
  <?php $this->endBody() ?>	
</body>
</html>
<?php $this->endPage();