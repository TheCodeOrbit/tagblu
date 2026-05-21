<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    //time zone added by ptpatel on date 13-05-25
    'timeZone' => 'Asia/Kolkata', //if it not set create issune in history and in created time
    'components' => [
        'assetManager' => [
        'appendTimestamp' => true,//added forvesioning of js and css by deepika on 12 jan 2026 resolves cache issue
        ],
        'view' => [
            'class' => 'common\components\CustomView',
        ],
        'user' => [
            'identityClass' => 'common\models\Vendorlogin',
            'enableAutoLogin' => true,
            'identityCookie' => [
                'name' => '_identity-frontend',
                'httpOnly' => true,
                'path' => '/',
            ],
        ],
        'session' => [
            'name' => 'advanced-frontend',
            'cookieParams' => [
                'path' => '/',
                'httpOnly' => true,
            ],
        ],
       'request' => [
            'baseUrl' => '',
            'csrfParam' => '_csrf-frontend',
            'csrfCookie' => [
                'httpOnly' => true,
                'path' => '/',
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/invaliderror',
        ],
       

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'GET restapi/users' => 'restapi/users',  // Route to get all users
                'POST restapi/saveiqc' => 'restapi/saveiqc', // route to save iqc
            ],
        ],

    ],
    'params' => $params,
];
