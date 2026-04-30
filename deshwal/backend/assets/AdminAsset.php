<?php

namespace backend\assets;

use yii\web\AssetBundle;

class AdminAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        // 'theme/css/bootstrap.min.css',
        // "thememain/css/quill.css",
        'thememain/css/bootstrap.min.css',
        // 'thememain/css/globals.css',
        'thememain/css/tetra.css',
        'thememain/css/fonts.css',
        'thememain/fontawesome/css/all.css',
        'thememain/css/dashboard-premium.css', /* Modern Navbar Styles */
        'thememain/css/style-search-all.css', /* Search Dropdown Styles */

        // 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
        // 'thememain/css/style.css',
        // 'theme/css/app.min.css',

    ];

    public $js = [
        'js/ckeditor/ckeditor.js',
        // "thememain/js/quill.min.js",
        'thememain/js/tetra/edit.js',
        'thememain/jquery/jquery.min.js',
        'thememain/bootstrap/bootstrap.min.js',
        'thememain/js/notification.js',
        'thememain/js/signature_pad.min.js',
        //  'theme/libs/jquery/jquery.min.js',
        //  'theme/libs/bootstrap/bootstrap.min.js',
        // 'thememain/js/main-dashboard.js',
        'thememain/js/xlsx.full.min.js',
        //03-12-25 added by ptpatel here to resolve issue and commented in first line
        // 'js/ckeditor/ckeditor.js',


    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];
}
