<?php
namespace common\components;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class ModelHelper
{
    public static function createMultiple($modelClass, $multipleModels = [])
    {
        $models = [];
        $post = Yii::$app->request->post($modelClass, []); // Get posted data
        $keys = array_keys(ArrayHelper::map($multipleModels, 'item_id', 'item_id'));

        // Loop through the post data to create models or load existing ones
        foreach ($post as $index => $data) {
            $model = isset($keys[$index]) ? $multipleModels[$keys[$index]] : new $modelClass;
            $model->load([$modelClass => $data]);
            $models[] = $model;
        }

        return $models;
    }
}
