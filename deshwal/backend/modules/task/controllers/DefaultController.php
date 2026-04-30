<?php

namespace backend\modules\task\controllers;

use common\controllers\ModuleController;

/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout='single';
    public $ModuleName='task';
    public $FieldId='taskinfo_id';
    public $TableName='task_information';
    public $TabLabel='Task';

   
    public $TabId='22';
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
