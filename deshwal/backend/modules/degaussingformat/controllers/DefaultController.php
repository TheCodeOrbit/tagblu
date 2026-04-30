<?php
namespace backend\modules\degaussingformat\controllers;
use common\controllers\ModuleController;

// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout='single';
    public $ModuleName='degaussingformat';
    public $FieldId='degaussing_format_id';
    public $TableName='degaussing_format';
    public $TabLabel='Degaussing Format';
    public $TabId='47';

    public function actionExample()
    {
        return $this->render('index');
    }
}
