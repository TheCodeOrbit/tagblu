<?php

namespace common\components;

use Yii;
use yii\web\Controller as BaseController;

class Controller extends BaseController
{
//    public function beforeAction($action)
// {
//     $nonce = Yii::$app->security->generateRandomString(16);

//     Yii::$app->params['cspNonce'] = $nonce;
//     Yii::$app->view->cspNonce = $nonce;

//     Yii::$app->response->headers->set(
//         'Content-Security-Policy',
//         "default-src 'self'; script-src 'self' 'nonce-$nonce'; style-src 'self' 'nonce-$nonce'"
//     );

//     return parent::beforeAction($action);
// }


public function beforeAction($action)
    {
        $param = Yii::$app->request->csrfParam;

        Yii::info('======== CSRF TRACE START ========', 'csrf');
        Yii::info('CSRF PARAM: ' . $param, 'csrf');
        Yii::info('POST CSRF: ' . Yii::$app->request->post($param), 'csrf');
        Yii::info('COOKIE CSRF: ' . Yii::$app->request->cookies->getValue($param), 'csrf');
        Yii::info('$_POST: ' . print_r($_POST, true), 'csrf');
        Yii::info('$_COOKIE: ' . print_r($_COOKIE, true), 'csrf');
        Yii::info('======== CSRF TRACE END ========', 'csrf');

        // echo  '======== CSRF TRACE START ========';
        // echo  '<br>CSRF PARAM: ' . $param;
        // echo  '<br>POST CSRF: ' . Yii::$app->request->post($param);
        // echo  '<br>COOKIE CSRF: ' . Yii::$app->request->cookies->getValue($param);
        // echo  '<br>$_POST: ' . print_r($_POST, true);
        // echo  '<br>$_COOKIE: ' . print_r($_COOKIE, true);
        // echo  '<br>======== CSRF TRACE END ========';

        return parent::beforeAction($action);
    }
    public function afterAction($action, $result)
{
    Yii::$app->response->headers->set('X-CSRF-Token', Yii::$app->request->getCsrfToken());
    return parent::afterAction($action, $result);
}


}
