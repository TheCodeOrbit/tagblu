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
        'request' => [
		'csrfParam' => '_csrf-frontend',
		//added on 20 june 2025
		'cookieValidationKey' => 'SFGF#5465645@@@',
    'enableCsrfValidation' => true,
    'secureHeaders' => ['X-Forwarded-Proto' => 'https']
		//end on 20 june 2025
        ],
        'user' => [
            'identityClass' => 'common\models\Vendorlogin',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
            'loginUrl' => ['/site/login'],  // Adjust if needed

        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
		'name' => 'advanced-frontend',
		//added on 20 june 2025
		'cookieParams' => [
        'secure' => true,
        'httponly' => true,
    ],//end on 20 june 2025
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
        // custom added by deepika
        'request'=>[
        'class' => 'common\components\Request',
        'web'=> '/frontend/web'
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
