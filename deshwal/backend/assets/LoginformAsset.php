<?php

namespace backend\assets;

use yii\web\AssetBundle;

class LoginformAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        // 'theme/css/bootstrap.min.css',
        // 'thememain/css/login.css',
       
        
    ];

    public $js = [
        
    
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];

}