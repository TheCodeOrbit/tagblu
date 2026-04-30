<?php

namespace backend\modules\termsandconditions\controllers;

use common\controllers\ModuleController;

/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'single';
    public $ModuleName = 'termsandconditions';
    public $FieldId = 'terms_conditions_id';
    public $TableName = 'terms_and_conditions';
    public $TabLabel='Terms And Conditions';

    public $TabId='63';
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
