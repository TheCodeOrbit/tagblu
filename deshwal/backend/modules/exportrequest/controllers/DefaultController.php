<?php

namespace backend\modules\exportrequest\controllers;

use app\models\Tab;
use backend\models\AccessCheck;
use common\controllers\ModuleController;
use Yii;

/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'single';
    public $ModuleName = 'exportrequest';
    public $FieldId = 'export_request_id';
    public $TableName = 'exportrequest';
    public $TabLabel = 'Export Request';
    public $TabId = '92';
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

    public function actionGetmodulenames()
    {
        $id = Yii::$app->user->id;
        $allowedModules = [];
        $model = new AccessCheck();
        $Modules = Tab::find()->where(['visible'=>0])->all();
        foreach($Modules as $Module){
            $ModuleName = $Module->name;
            $tabs = $model->tabs($id, $ModuleName);
            $profile = $model->profile($id, $tabs, $ModuleName);
            $modelaccess = $model->moduleaccess($id, $profile, $tabs);
            $rolebasedrecord = $model->rolebasedrecord($id, $profile);
            $hasadminpower = $model->hasadminpower($profile);
            $modulepermission = $model->modulepermission($profile, $tabs);
            $exportpermission = $model->checkpermission($id, $ModuleName, 'export');
            if($exportpermission == 1){
                 $allowedModules[$Module['tabid']] = $ModuleName;
            }
        }
        // echo "<pre>";print_r($allowedModules);die;
        if (!empty($allowedModules)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $allowedModules,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Module found for loggedin user',
                'data' => ''
            ]);
        }
    }

}
