<?php

use yii\helpers\Url;
use yii\helpers\Html;
use backend\assets\AdminAsset;
use yii\bootstrap5\ActiveForm;

AdminAsset::register($this);
$this->title = Yii::t('app', 'Profile ');

$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', ['depends' => [AdminAsset::class]]);



$url = Url::to(['create']);
$baseUrl = Yii::$app->HomeUrl;

$csrfTokenName = Yii::$app->request->csrfParam;  // This replaces csrfTokenName
$csrfToken = Yii::$app->request->csrfToken;      // Get the CSRF token itself 

?>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: poppins;
    }

    .user-profile-page-1 {

        margin: 20px auto;
        padding: 20px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .user-profile-page-1 .header {
        display: flex;
        align-items: center;
        padding: 20px;
        background: #f4f4f9;
        border-radius: 8px 8px 0 0;
    }

    .user-profile-page-1 .header img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        margin-right: 20px;
    }

    .user-profile-page-1 .header .user-info {
        font-size: 1.2em;
    }

    .user-profile-page-1 .header .user-info span {
        display: block;
        color: #555;
        font-size: 14px;

    }

    .user-profile-page-1 .header .user-info strong {
        font-size: 17px;
        font-weight: 600;
    }

    .user-profile-page-1 h2 {
        margin-top: 20px;
        color: #333;
        background: #e8e8e8;
        padding: 10px 23px;
        margin-bottom: 21px;
        font-size: 16px;
        font-weight: 600;
    }

    .user-profile-page-1 .form {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .user-profile-page-1 .form-group {
        display: flex;
        flex-direction: column;
    }

    .user-profile-page-1 .form-group label {

        margin-bottom: 5px;
        color: #777;
        font-size: 14px;
        font-weight: 500;

    }

    .user-profile-page-1 .form-group input {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 12px;
    }

    .user-profile-page-1 .form-group input:focus {
        outline: none;
        border-color: #007bff;
    }

    .user-profile-page-1 .section {
        margin-top: 30px;
    }
</style>


<input type="hidden" name="csrfToken" id="csrfToken" value="<?= $csrfToken; ?>">
<input type="hidden" name="csrfTokenName" id="csrfTokenName" value="<?= $csrfTokenName; ?>">
<div class="page-content">

</div>


<!-- end add model -->
<div class="select-1">



    <div class="container-d user-profile-page-1">
        <div class="header">
            <img src="<?= Yii::getAlias('@web') ?>/thememain/profile/<?= Html::encode($model->profilepic ?: 'no-image.png') ?>" alt="User Avatar">
            <div class="user-info">
                <strong><?= Html::encode($model->first_name) . " " . Html::encode($model->last_name) ?></strong>

            </div>
        </div>
        <div>
            <?php if (Yii::$app->session->hasFlash('success')): ?>
                <div class="alert alert-success">
                    <?= Yii::$app->session->getFlash('success'); ?>
                </div>
            <?php endif; ?>

            <?php if (Yii::$app->session->hasFlash('error')): ?>
                <div class="alert alert-danger">
                    <?= Yii::$app->session->getFlash('error'); ?>
                </div>
            <?php endif; ?>
        </div>
        <h2>User Login & Role</h2>
        <div class="form">
            <div class="form-group">
                <label for="username">User Name</label>
                <input type="text" id="username" readonly value="<?= $model->username; ?>">
            </div>
            <div class="form-group">
                <label for="first-name">First Name </label>
                <input type="text" id="first-name" readonly value="<?= $model->first_name; ?>">
            </div>
            <div class="form-group">
                <label for="last-name">Last Name </label>
                <input type="text" id="last-name" readonly value="<?= $model->last_name; ?>">
            </div>
            <div class="form-group">
                <label for="phone">Phone </label>
                <input type="text" id="phone" readonly value="<?= $model->mobile; ?>">
            </div>
            <div class="form-group">
                <label for="email">Primary Email</label>
                <input type="email" readonly value="<?= $model->email; ?>">
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <input type="text" id="role" readonly value="<?= $profilename; ?>">
            </div>


        </div>

        <div class="section">
            <h2>Update password</h2>

            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'id' => 'update-password-form']]); ?>
            <div class="form">

                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <?= Html::passwordInput('User[current_password]', '', [
                        'id' => 'current_password',
                        'class' => 'form-control',
                    ]); ?>
                    <span class="error-message text-danger" id="current_password_error"></span>
                </div>


                <div class="form-group">
                    <label for="password">New Password</label>
                    <?= Html::passwordInput('User[password]', '', [
                        'id' => 'new_password',
                        'class' => 'form-control',
                    ]); ?>
                    <span class="error-message text-danger" id="new_password_error"></span>
                </div>


                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <?= Html::passwordInput('User[confirm_password]', '', [
                        'id' => 'confirm_password',
                        'class' => 'form-control',
                    ]); ?>
                    <span class="error-message text-danger" id="confirm_password_error"></span>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <?= Html::textInput('User[email]', $model->email, [
                        'id' => 'email',
                        'class' => 'form-control',
                    ]); ?>
                    <span class="error-message text-danger" id="email_error"></span>
                </div>


                <div class="form-group">
                    <label for="profilepic">Profile Image</label>
                    <?= Html::fileInput('User[profilepic]', null, [
                        'id' => 'profilepic',
                        'class' => 'form-control temp-file',//temp-file added for preview image
                        'data-module'=>'user' // this line added for preview image
                    ]); ?>
                    <span class="error-message text-danger" id="profilepic_error"></span>
                    <!-- for preview image-->
                    <div class="file-preview mt-2"></div>
                    <!-- end for preview image-->
                    <p>Note: Allowed file type PNG,JPG,JPEG and Maximum file size allow 5 MB</p>

                </div>
                

                <div class="form-group">

                    <?= Html::Button('Save Changes', ['class' => 'btn btn-primary updateprofile']); ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>


<?php
$this->registerJsFile('@web/thememain/js/tetra/updateprofile.js', ['depends' => [AdminAsset::class]]);
$this->registerJs("

 
");




?>

<script>


</script>