<?php

namespace backend\modules\salesorderitemsdetail\controllers;

use common\controllers\ModuleController;
use Yii;
use yii\db\Query;

/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'single';
    public $ModuleName = 'salesorderitemsdetail';
    public $FieldId = 'salesorderitemdetail_id';
    public $TableName = 'salesorder_items_detail';
    public $TabLabel = 'Sales Order Item Detail';
    public $TabId = '14';

    //  public function beforeAction($action)
    // {
    //     $this->layout = '@app/views/layouts/main';  // Set the main layout before each action
    //     return parent::beforeAction($action);
    // }

   

}
