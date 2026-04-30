<?php

namespace backend\assets;

use yii\web\AssetBundle;

class AdminAssetnew extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        // 'theme/css/bootstrap.min.css',
        // 'thememain/css/globals.css',
        // 'thememain/css/style.css',
        // 'theme/css/app.min.css',
        'thememain/css/index.css',
        
    ];

    public $js = [
       //   'thememain/js/tetra/edit.js',
       // 'theme/libs/jquery/jquery.min.js',
       // 'theme/libs/bootstrap/bootstrap.min.js',
       
       
    
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];

}