<?php

namespace backend\modules\degaussingvendordetails\controllers;

use common\controllers\ModuleController;

// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout='single';
    public $ModuleName='degaussingvendordetails';
    public $FieldId='degaussing_vendor_id';
    public $TableName='degaussing_vendor_details';
    public $TabLabel='Degaussing Vendor Details';
   
    public $TabId='48';
    public function actionExample()
    {
        return $this->render('index');
    }
}
