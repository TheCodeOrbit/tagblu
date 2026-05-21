<?php

namespace backend\components;

use Yii;

class SvgRenderHelper
{
    public static function renderIcon($iconName, $module = false)
    {
        $path = $module 
            ? Yii::getAlias('@webroot') . '/thememain/img/module-icon/' 
            : Yii::getAlias('@webroot') . '/thememain/img/';
        
        $fullPath = $path . $iconName;

        if (!file_exists($fullPath)) {
            return '';
        }

        $svg = '';
        $maxRetries = 5;
        for ($i = 0; $i < $maxRetries; $i++) {
            clearstatcache(true, $fullPath);
            $svg = @file_get_contents($fullPath);
            if ($svg !== false && !empty($svg)) {
                break;
            }
            usleep(50000); // 50ms wait
        }

        if ($svg === false) {
            return '';
        }

        // Standardize size for sidebar icons
        $svg = preg_replace('/<svg\b([^>]*)\bwidth="[^"]*"/i', '<svg$1width="24"', $svg);
        $svg = preg_replace('/<svg\b([^>]*)\bheight="[^"]*"/i', '<svg$1height="24"', $svg);

        return $svg;
    }
}