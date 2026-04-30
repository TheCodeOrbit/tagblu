<?php

namespace backend\controllers;

// use yii\web\Controller;
use yii\web\Response;
use common\components\Controller;
/**
 * Site controller
 */
class DashboardController extends Controller
{

    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }


  
}
