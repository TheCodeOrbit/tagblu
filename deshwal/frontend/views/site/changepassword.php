<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<div class="container">
    <div class="mt-3 pickup-request-form row">

        <div class="col-lg-3"></div>
        <div class="col-lg-6">
            <?php $form = ActiveForm::begin([
                'id' => 'resetpassword-form',
                'action' => ['site/changepassword'], // make sure controller/action is correct
                'method' => 'post']); ?>
            <div class="pb-2">
                <?php if (Yii::$app->session->hasFlash('success')): ?>
                    <div id="alert-message" class="text-success alert-dismissible fade show" role="alert">
                        <?= Yii::$app->session->getFlash('success') ?>
                    </div>
                    <script>
                        setTimeout(function() {
                            $('#alert-message').fadeOut('slow');
                        }, 10000);
                    </script>
                <?php endif; ?>

                <?php if (Yii::$app->session->hasFlash('error')): ?>
                    <div id="alert-message" class="text-danger alert-dismissible fade show" role="alert">
                        <?= Yii::$app->session->getFlash('error') ?>
                    </div>
                    <script>
                        setTimeout(function() {
                            $('#alert-message').fadeOut('slow');
                        }, 10000);
                    </script>
                <?php endif; ?>
            </div>
            <!-- Show -->
            <div class="col-lg-12 pb-2">
                <div class="form-group required form-field-cst section-password col-lg-12">
                    <label class="control-label" for="password">Password</label>
                    <input type="password" id="password" class="form-control" name="password" placeholder="New Password" maxlength="100">
                    <div class="help-block"></div>
                </div>
            </div>
            <div class="col-lg-12 pb-2">
                <div class="form-group required form-field-cst section-confirm_password col-lg-12">
                    <label class="control-label" for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" class="form-control" name="confirm_password" placeholder="Confirm Password" maxlength="100">
                    <div class="help-block"></div>
                </div>
            </div>
            <?= Html::submitButton('Reset Password', ['class' => 'btn btn-primary w-100','id'=>'resetpassword-btn', 'name' => 'resetpassword-button']) ?>

            <?php ActiveForm::end(); ?>
        </div>
        <div class="col-lg-3"></div>
    </div>
</div>
</div>