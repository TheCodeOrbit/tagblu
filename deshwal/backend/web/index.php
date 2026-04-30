<?php

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/../../common/config/bootstrap.php';
require __DIR__ . '/../config/bootstrap.php';

$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../../common/config/main.php',
    require __DIR__ . '/../../common/config/main-local.php',
    require __DIR__ . '/../config/main.php',
    require __DIR__ . '/../config/main-local.php'
);
$app = new yii\web\Application($config);


$nonce = bin2hex(random_bytes(16)); // or from CSP logic

// Make it available globally
Yii::$app->on(\yii\base\Application::EVENT_BEFORE_REQUEST, function () use ($nonce) {
    Yii::$app->view->cspNonce = $nonce;
    Yii::$app->params['cspNonce'] = $nonce;
});
header("Content-Security-Policy:     default-src 'self'; script-src 'self' 'nonce-$nonce'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com;   img-src 'self' data: http://www.w3.org https://c.animaapp.com;  font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com https://fontawesome.com  data:;");


$app->run();
//(new yii\web\Application($config))->run();
