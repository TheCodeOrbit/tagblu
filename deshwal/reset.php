<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/common/config/bootstrap.php';
require __DIR__ . '/console/config/bootstrap.php';

$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/common/config/main.php',
    require __DIR__ . '/common/config/main-local.php',
    require __DIR__ . '/console/config/main.php',
    require __DIR__ . '/console/config/main-local.php'
);

$application = new yii\console\Application($config);
$user = \common\models\User::findOne(['username' => 'nitay@ditserv.com']);
if ($user) {
    $user->setPassword('password123');
    $user->save(false);
    echo "Yii2 Native Password Reset Success!\n";
} else {
    echo "User not found!\n";
}
