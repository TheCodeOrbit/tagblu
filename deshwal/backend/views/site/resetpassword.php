<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use backend\assets\LoginformAsset;


LoginformAsset::register($this);
$this->title = 'Reset Password';
$baseUrl = Yii::$app->HomeUrl;
?>

<div class="login-form">
 
  <?php $form = ActiveForm::begin(['id' => 'resetpassword-form']); ?>
   <?php if (!empty($siteSetting->logo_path)): ?>
    <h2>
        <?= Html::img(
            Yii::getAlias('@web') . $siteSetting->logo_path,
            ['alt' => $siteSetting->company, 'style' => '']
        ) ?>
    </h2>
<?php endif; ?>
  <!-- <h2><img src="<= $baseUrl; ?>thememain/img/login/logo.png"></h2> -->
  <?php if ($model->hasErrors('token')): ?>
    <div class="alert alert-danger">
      <?= $model->getFirstError('token') ?>
    </div>

    <!-- Redirect button to forgot password page -->
    <div class="form-group">
      <?= \yii\helpers\Html::a(
        'Go to Forgot Password',
        ['site/forgotpassword'], // replace with your forgot password route
        ['class' => 'btn login-button']
      ) ?>
    </div>

  <?php else: ?>

    <!-- Success flash -->
    <?php if (Yii::$app->session->hasFlash('success')): ?>
      <div class="alert alert-success">
        <?= Yii::$app->session->getFlash('success') ?>
      </div>
    <?php endif; ?>

    <!-- Show -->
    <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'New Password']) ?>
    <?= $form->field($model, 'confirm_password')->passwordInput(['placeholder' => 'Confirm Password']) ?>

    <?= Html::submitButton('Reset Password', ['class' => 'login-button', 'name' => 'resetpassword-button']) ?>
  <?php endif; ?>
  <?php ActiveForm::end(); ?>
</div>