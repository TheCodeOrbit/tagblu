<?php

namespace backend\modules\drillingvendordetails\controllers;

use common\controllers\ModuleController;

/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout='single';
    public $ModuleName='drillingvendordetails';
    public $FieldId='drilling_vendor_id';
    public $TableName='drilling_vendor_details';
    public $TabLabel='Drilling Vendor Details';

   
    public $TabId='44';
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
