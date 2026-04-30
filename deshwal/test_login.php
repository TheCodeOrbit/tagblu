<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/common/config/bootstrap.php';
require __DIR__ . '/backend/config/bootstrap.php';

$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/common/config/main.php',
    require __DIR__ . '/common/config/main-local.php',
    require __DIR__ . '/backend/config/main.php',
    require __DIR__ . '/backend/config/main-local.php'
);

$app = new yii\web\Application($config);

// Run login
$model = new \common\models\LoginForm();
$model->username = 'nitay@ditserv.com';
$model->password = 'password123'; 
$res = $model->login();
ini_set('display_errors', 1);
error_reporting(E_ALL);

$userModelAfter = \common\models\User::findByUsername('nitay@ditserv.com');

file_put_contents('test_login_result.txt', "Login result: " . ($res ? 'true' : 'false') . "\nAFTER LOGIN hash: " . $userModelAfter->password_hash . "\n");
