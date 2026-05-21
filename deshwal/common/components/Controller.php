<?php

namespace common\components;

use Yii;
use yii\web\Controller as BaseController;

class Controller extends BaseController
{
    public function beforeAction($action)
    {
        $nonce = Yii::$app->security->generateRandomString(16);
        Yii::$app->params['cspNonce'] = $nonce;
        Yii::$app->view->cspNonce = $nonce;

        $param = Yii::$app->request->csrfParam;

        Yii::info('======== CSRF TRACE START ========', 'csrf');
        Yii::info('CSRF PARAM: ' . $param, 'csrf');
        Yii::info('POST CSRF: ' . Yii::$app->request->post($param), 'csrf');
        Yii::info('COOKIE CSRF: ' . Yii::$app->request->cookies->getValue($param), 'csrf');
        Yii::info('$_POST: ' . print_r($_POST, true), 'csrf');
        Yii::info('$_COOKIE: ' . print_r($_COOKIE, true), 'csrf');
        Yii::info('======== CSRF TRACE END ========', 'csrf');

        return parent::beforeAction($action);
    }
    public function afterAction($action, $result)
{
    Yii::$app->response->headers->set('X-CSRF-Token', Yii::$app->request->getCsrfToken());
    return parent::afterAction($action, $result);
}


}
