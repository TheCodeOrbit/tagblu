<?php

namespace backend\modules\drillingformat\controllers;

use common\controllers\ModuleController;

/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout='single';
    public $ModuleName='drillingformat';
    public $FieldId='drilling_format_id';
    public $TableName='drilling_format';
    public $TabLabel='Drilling Format';

   
    public $TabId='43';
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
