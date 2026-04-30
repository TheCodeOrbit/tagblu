<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \common\models\LoginForm $model */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;
use backend\assets\LoginAsset;


LoginAsset::register($this);

$this->title = 'Reset Password';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->HomeUrl;

// $this->registerJsFile($baseUrl.'js/crypto-js.min.js', ['depends' => [LoginAsset::class]]);
?>
<div class="login-page">
    <div class="container-fluid custom-gutter">
        <div class="row vh-100">
            <!-- Left Column -->
            <div class="col-md-6 left-column">
                <h1>Welcome</h1>
                <div class="circle-center"></div>
            </div>
            <div class="col-md-6 right-column">
                <div class="form-container text-center">
                    <div class="logo">
                        <img src="<?= $baseUrl; ?>images/logo.png" alt="Deshwal Waste Management">
                    </div>
                    <?php $form = ActiveForm::begin(['id' => 'resetpassword-form']); ?>
                          <div class="pb-2">
                        <?php if (Yii::$app->session->hasFlash('success')): ?>
                            <div id="alert-message" class="text-success alert-dismissible fade show" role="alert">
                                <?= Yii::$app->session->getFlash('success') ?>
                            </div>
                            <script>
                                setTimeout(function () {
                                    $('#alert-message').fadeOut('slow');
                                }, 10000);
                            </script>
                        <?php endif; ?>

                        <?php if (Yii::$app->session->hasFlash('error')): ?>
                            <div id="alert-message" class="text-danger alert-dismissible fade show" role="alert">
                                <?= Yii::$app->session->getFlash('error') ?>
                            </div>
                            <script>
                                setTimeout(function () {
                                    $('#alert-message').fadeOut('slow');
                                }, 10000);
                            </script>
                        <?php endif; ?>
                    </div>
                        <!-- Show -->
                        <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'New Password'])->label(false) ?>
                        <?= $form->field($model, 'confirm_password')->passwordInput(['placeholder' => 'Confirm Password'])->label(false) ?>

                        <?= Html::submitButton('Reset Password', ['class' => 'btn btn-primary w-100', 'name' => 'resetpassword-button']) ?>
                      
                      <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
