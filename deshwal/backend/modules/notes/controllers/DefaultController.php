<?php

namespace backend\modules\notes\controllers;

use common\controllers\ModuleController;

/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout='single';
    public $ModuleName='notes';
    public $FieldId='modnotesid';
    public $TableName='modnotes';
    public $TabLabel='Notes';

   
    public $TabId='27';
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
