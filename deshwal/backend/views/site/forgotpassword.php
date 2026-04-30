<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \common\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use backend\assets\LoginformAsset;
 

LoginformAsset::register($this);
$this->title = 'Forgot Password';
$baseUrl = Yii::$app->HomeUrl;
?>
<div class="login-form">
  <?php $form = ActiveForm::begin(['id' => 'forgotpassword-form']); ?>
   <?php if (!empty($siteSetting->logo_path)): ?>
    <h2>
        <?= Html::img(
            Yii::getAlias('@web') . $siteSetting->logo_path,
            ['alt' => $siteSetting->company, 'style' => '']
        ) ?>
    </h2>
<?php endif; ?>
  <!-- <h2><img src="<= $baseUrl; ?>thememain/img/login/logo.png"></h2> -->
  <?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success">
      <?= Yii::$app->session->getFlash('success') ?>
    </div>
  <?php endif; ?>
  <?php if (Yii::$app->session->hasFlash('error')): ?>
    <div class="alert alert-error">
      <?= Yii::$app->session->getFlash('error') ?>
    </div>
  <?php endif; ?>
  <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

  <?= Html::submitButton('Forgot Password', ['class' => 'login-button', 'name' => 'forgotpassword-button']) ?>
  <?php ActiveForm::end(); ?>
</div>