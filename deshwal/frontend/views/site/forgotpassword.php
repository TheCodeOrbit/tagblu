<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \common\models\LoginForm $model */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;
use backend\assets\LoginAsset;


LoginAsset::register($this);

$this->title = 'Forgot Password';
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
                    <?php $form = ActiveForm::begin(['id' => 'forgotpassword-form']); ?>
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
                    <?= $form->field($model, 'username')->textInput(['autofocus' => true,'placeholder' => 'Username'])->label(false) ?>
                    
                    <?php // $form->field($model, 'rememberMe')->checkbox() ?>
                    
                    <button type="submit" class="btn btn-primary w-100">Forgot Password</button>
                    
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// $this->registerJs("
// // Function to generate a 32-byte random string
// function generateRandomString(bytes) {
//   const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
//   let result = '';
//   for (let i = 0; i < bytes; i++) {
//     const randomIndex = Math.floor(Math.random() * characters.length);
//     result += characters[randomIndex];
//   }
//   return result;
// }

// // Function to create a hidden input field and append it to the form
// function createHiddenInput() {
//   // Generate a 32-byte random string
//   const randomString = generateRandomString(32);

//   // Create a hidden input element
//   const input = document.createElement('input');
//   input.type = 'hidden'; // Set the type to 'hidden'
//   input.name = 'pkey'; // Set the name attribute
//   input.id = 'pkey'; // Set the name attribute
//   input.value = randomString; // Set the value to the random string

//   // Find the form with the ID 'login-form'
//   const form = document.getElementById('login-form');
  
//   // Append the input to the form
//   if (form) {
//     form.appendChild(input);
//   } else {
//     console.error('Form with id login-form not found.');
//   }
// }

// // Call the function to create and append the hidden input
// createHiddenInput();


//  $('#login-form').on('submit', function(e) {
//     //alert(e);
//     e.preventDefault();  // Prevent form submission (page reload)
//     //alert('gsdgd');
//     encryptPassword(e);
// });
// var public_key=$('#pkey').val();
// const encryptionKey = CryptoJS.enc.Utf8.parse(public_key); // EXACTLY 32 bytes
// const iv = CryptoJS.lib.WordArray.random(16); // 16-byte IV

// function encryptPassword(event) {
//     event.preventDefault();

//     let password = document.getElementById('loginform-password').value;

//     let encrypted = CryptoJS.AES.encrypt(password, encryptionKey, {
//         iv: iv,
//         mode: CryptoJS.mode.CBC,
//         padding: CryptoJS.pad.Pkcs7
//     });

//     // Combine IV and Ciphertext, then Base64 encode
//     let encryptedPassword = CryptoJS.enc.Base64.stringify(iv.concat(encrypted.ciphertext));

//     document.getElementById('loginform-password').value = encryptedPassword;
//     document.getElementById('login-form').submit();
// }

// ");
?>