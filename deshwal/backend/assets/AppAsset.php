<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        //  'css/site.css',

        'theme/css/bootstrap.min.css',
        'theme/libs/jquery-vectormap/jquery-vectormap.min.css',
        'theme/css/preloader.min.css',
        'theme/css/icons.min.css',
        'theme/css/app.min.css',
     
    
    ];
    public $js = [
        'theme/libs/jquery/jquery.min.js',
        'theme/libs/bootstrap/bootstrap.min.js',
        'theme/libs/metismenu/metismenu.min.js',
        'theme/libs/simplebar/simplebar.min.js',
        'theme/libs/node-waves/node-waves.min.js',
        'theme/libs/feather-icons/feather-icons.min.js',
        'theme/libs/pace-js/pace-js.min.js',
      
       
        
    ];



    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];

     // Add the favicon path
     public $favicon = 'theme/images/favicon.ico';

    
}
