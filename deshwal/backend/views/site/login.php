<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \common\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use backend\assets\LoginformAsset;


LoginformAsset::register($this);
$this->title = 'Login';
$baseUrl = Yii::$app->HomeUrl;
// $this->registerJsFile($baseUrl.'thememain/js/tetra/crypto-js.min.js', ['depends' => [LoginformAsset::class]]);

?>

 


 <div class="login-form">
 <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
 <?php if (!empty($siteSetting->logo_path)): ?>
    <h2>
        <?= Html::img(
            Yii::getAlias('@web') . $siteSetting->logo_path,
            ['alt' => $siteSetting->company, 'style' => '']
        ) ?>
    </h2>
<?php endif; ?>
        <!-- <h2><img src="<= $baseUrl; ?>thememain/img/login/logo.png"></h2> -->
        <!-- <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" placeholder="Enter your email">
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" placeholder="Enter your password">
        </div> -->
        <?php if (Yii::$app->session->hasFlash('success')): ?>
            <div class="alert alert-success">
                <?= Yii::$app->session->getFlash('success') ?>
            </div>
        <?php endif; ?>
        <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

<?= $form->field($model, 'password')->passwordInput() ?>
        <div class="form-actions">
          <div>
          <input type="checkbox" id="loginform-rememberme" class="form-check-input" name="LoginForm[rememberMe]" value="1" checked="">
            <!-- <input type="checkbox" id="remember"> -->
            <label for="remember">Remember Me</label>
          </div>
          <a href="<?php echo \Yii::$app->urlManager->createUrl(['site/forgotpassword']); ?>">Forgot Password?</a>
        </div>
        <!-- <button class="login-button">Login</button> -->
        <?= Html::submitButton('Login', ['class' => 'login-button', 'name' => 'login-button']) ?>
        <?php ActiveForm::end(); ?>
      </div>

      <script src=""></script>
<script>
    // alert("gsdgd sdgd");
  
 

</script>
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

//");
?>



    
