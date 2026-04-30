<?php

namespace backend\modules\raiserequestvendor\controllers;

use common\controllers\ModuleController;
use Yii;

/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'single';
    public $ModuleName = 'raiserequestvendor';
    public $FieldId = 'raiserequest_vendor_id';
    public $TableName = 'raiserequest_vendor';
    public $TabLabel = 'Raise Request Vendor';
    public $TabId = '96';
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
