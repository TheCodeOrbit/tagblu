<?php

namespace backend\modules\raiserequestclient\controllers;

use common\controllers\ModuleController;
use Yii;

/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'single';
    public $ModuleName = 'raiserequestclient';
    public $FieldId = 'raiserequest_client_id';
    public $TableName = 'raiserequest_client';
    public $TabLabel = 'Raise Request Client';
    public $TabId = '97';
    /**
     * Renders the index view for the module
     * @return string
     */
    //  public function beforeAction($action)
    // {
    //     $this->layout = '@app/views/layouts/main';  // Set the main layout before each action
    //     return parent::beforeAction($action);
    // }

    public function actionExample()
    {
        return $this->render('index');
    }

}
