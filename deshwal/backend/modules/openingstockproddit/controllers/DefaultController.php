<?php

namespace backend\modules\openingstockproddit\controllers;

use common\controllers\ModuleController;
use Yii;
/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'single';
    public $ModuleName = 'openingstockproddit';
    public $FieldId = 'openingstock_prod_dit_id';
    public $TableName = 'openingstock_prod_dit';
    public $TabLabel = 'Opening Stock DevIT';


    public $TabId = '122';
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