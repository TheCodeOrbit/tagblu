<?php

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'controllerMap' => [
        'fixture' => [
            'class' => \yii\console\controllers\FixtureController::class,
            'namespace' => 'common\fixtures',
          ],
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                    'logFile' => '@runtime/logs/console-mail.log',
                ],
            ],
        ],
           'db' => [
            'class' => \yii\db\Connection::class,
            'dsn' => 'mysql:host=139.84.169.156;dbname=deshwal',
            'username' => 'deshwal_erp',
            'password' => 'Qe2/G@OrK/ndH5t4',
            'charset' => 'utf8',
            'attributes' => [
                PDO::ATTR_PERSISTENT => true,
            ],
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,              
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.yourhost.com',
                'username' => 'no-reply@yourhost.com',
                'password' => 'your-password',
                'port' => '587',                     
                'encryption' => 'tls',                
            ],
        ],
    ],
    'params' => $params,
];
