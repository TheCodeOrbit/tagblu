<?php

namespace backend\modules\openingstockprod\controllers;

use common\controllers\ModuleController;
use Yii;
use yii\db\Query;

/**
 * Default controller for the `materialissuenotedit` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'single';
    public $ModuleName = 'openingstockprod';
    public $FieldId = 'openingstock_prod_id';
    public $TableName = 'openingstock_prod';
    public $TabLabel = 'Open Stock Product Details';
    public $TabId = '121';

    //  public function beforeAction($action)
    // {
    //     $this->layout = '@app/views/layouts/main';  // Set the main layout before each action
    //     return parent::beforeAction($action);
    // }

}
