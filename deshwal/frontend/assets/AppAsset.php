<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        // 'css/site.css',
        'css/all.min.css',
        'css/fonts.css',
        'css/fontawesome.css',
        'css/style-main.css',
        'css/fSelect.css'
    ];
    public $js = [
        'js/main/fSelect.js',
        'js/main/xlsx.full.min.js',
        'js/main/bootstrap.bundle.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];
}
