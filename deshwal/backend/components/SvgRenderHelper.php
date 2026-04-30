<?php

namespace backend\components;

use Yii;

class SvgRenderHelper
{
    public static function renderIcon($filename, $moduleIcon = false)
{
    $basePath = $moduleIcon
        ? Yii::getAlias('@webroot') . '/thememain/img/module-icon/'
        : Yii::getAlias('@webroot') . '/thememain/img/';

    $fullPath = $basePath . ltrim($filename, '/');

    if (!is_file($fullPath)) {
        return '';
    }

    $svg = file_get_contents($fullPath);

    $svg = preg_replace('/<svg\b([^>]*)\bwidth="[^"]*"/i', '<svg$1width="30"', $svg);
    $svg = preg_replace('/<svg\b([^>]*)\bheight="[^"]*"/i', '<svg$1height="30"', $svg);

    return $svg;
}
}