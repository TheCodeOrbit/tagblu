<?php

namespace backend\controllers;

use app\models\AutoNo;
use app\models\EditModel;
use app\models\Exportrequest;
use app\models\ListModel;
use app\models\ModtrackerBasic;
use app\models\Tab;
use app\models\User;
use backend\assets\AdminAsset;
use backend\models\AccessCheck;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
// use yii\web\Controller;
use common\components\Controller;
use Exception;
use Yii;
use yii\db\Query;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ExportrequestController implements the CRUD actions for Exportrequest model.
 */
class ExportrequestController extends Controller
{
    protected $TabId = 92;
    protected  $FieldId = 'export_request_id';
    protected    $ModuleName = 'exportrequest';
    protected    $TableName = 'exportrequest';
    protected    $TabLabel = 'Export All Request';
    // protected    $layout = 'single';
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Exportrequest models.
     *
     * @return string
     */
  
    // public function actionIndex()
    public function actionList()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Exportrequest::find(),
            /*
            'pagination' => [
                'pageSize' => 50
            ],
            'sort' => [
                'defaultOrder' => [
                    'profile_id' => SORT_DESC,
                ]
            ],
            */
        ]);
        // echo "<pre>";
        // print_r($dataProvider);
        // die;
        $this->layout = '@app/views/layouts/main-one';
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Exportrequest model.
     * @param int $export_request_id Export Request ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($export_request_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($export_request_id),
        ]);
    }

    /**
     * Creates a new Exportrequest model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Exportrequest();
        $modulename = $this->getmodulenames();
        $model->ownerid = \Yii::$app->user->id;
        $model->creatorid = \Yii::$app->user->id;
        $model->modifiedby = \Yii::$app->user->id;
        $model->createdtime = date('Y-m-d H:i:s');
        $model->modifiedtime = date('Y-m-d H:i:s');

        $transaction = Yii::$app->db->beginTransaction();
        $owners = $this->getowners();
        if ($this->request->isPost) {
        if ($model->load($this->request->post()) ) {
            try{
                // && $model->save()
                if (!empty($model->from_date)) {
                    $model->from_date = date('Y-m-d', strtotime($model->from_date));
                }
                if (!empty($model->to_date)) {
                    $model->to_date = date('Y-m-d', strtotime($model->to_date));
                }
                    $EditModel = new EditModel('exportrequest','export_request_id','exportrequest','create');
                    $model->export_request_no = $EditModel->getAutoNo(92); //92 is tab id of this module
                    
                // echo "<pre>";print_r($model->attributes);die;
                $model->status = 1;
                $model->save();
                $modlog = new ModtrackerBasic();
                $auditstatus = 0;
                $mode = 'create';
                $module = $this->ModuleName;
                $customtablename = $module . "cf";
                $CS = array();
                if (isset($_POST[$customtablename]))
                    $CS = $_POST[$customtablename];
                else
                    $CS = '';
                $modlog->auditlog($model->oldAttributes, $model->attributes, $this->ModuleName, $model->export_request_id , $auditstatus, Yii::$app->user->id);
                $EditModel->updateCRMSequence($module, $model->export_request_id );
                //now save custom fields 
                if (!empty($CS)) {
                    $CS = array_merge($CS, ["export_request_id" => $model->export_request_id ]);
                    echo "CS=";
                    //print_r($CS);echo "<br>";die;
                    $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                    $command->execute();
                    $modlog->auditlog($oldAttributes = '', $CS, $this->ModuleName, $model->export_request_id, $auditstatus, Yii::$app->user->id);
                }
                            
                $this->setAutoNo(92,'exportrequest');
                $transaction->commit();
            }catch(Exception $e){
                
                $transaction->rollBack();
                $error_message = $e->getMessage();
                Yii::$app->session->setFlash(
                    "error",
                    $error_message
                );
            }
            $this->layout = '@app/views/layouts/main-one';
            return $this->redirect(['list']);
            }
        } else {
            $model->loadDefaultValues();
            
        }

        $this->layout = '@app/views/layouts/main-one';
        return $this->render('create', [
            'model' => $model,
            'modulenames'=>$modulename,  
            'owners' =>$owners
        ]);
    }

    /**
     * Updates an existing Exportrequest model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $export_request_id Export Request ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($export_request_id)
    {
        $modulename = $this->getmodulenames();

        $owners = $this->getowners();
        $model = $this->findModel($export_request_id);
        
        $model->ownerid = \Yii::$app->user->id;
        $model->creatorid = \Yii::$app->user->id;
        $model->modifiedby = \Yii::$app->user->id;
        $model->modifiedtime = date('Y-m-d H:i:s');
        $model->modifiedby = \Yii::$app->user->id;

        if (!empty($model->from_date)) {
            $model->from_date = date('d-m-Y', strtotime($model->from_date));
        }
        if (!empty($model->to_date)) {
            $model->to_date = date('d-m-Y', strtotime($model->to_date));
        }
        // if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
        if($this->request->isPost && $model->load($this->request->post())){
           if (!empty($model->from_date)) {
                $model->from_date = date('Y-m-d', strtotime($model->from_date));
            }
            if (!empty($model->to_date)) {
                $model->to_date = date('Y-m-d', strtotime($model->to_date));
            }
            // echo "<pre>";print_r($model->attributes);die;
            $model->status = 1;
            $model->save();
            return $this->redirect(['list']);
        }

        $this->layout = '@app/views/layouts/main-one';
        return $this->render('update', [
            'model' => $model,
            'modulenames'=>$modulename,  
            'owners' =>$owners
        ]);
    }

    /**
     * Deletes an existing Exportrequest model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $export_request_id Export Request ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($export_request_id)
    {
        $this->findModel($export_request_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Exportrequest model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $export_request_id Export Request ID
     * @return Exportrequest the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($export_request_id)
    {
        if (($model = Exportrequest::findOne(['export_request_id' => $export_request_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function getmodulenames()
    {
        $id = \Yii::$app->user->id;
        $allowedModules = [];
        $model = new AccessCheck();
        $Modules = Tab::find()->where([
            'visible'=>0,
            'presence'=>0,
            'export_allowed'=>1
            ])->all();//add exportall when added in tab table like import allowed
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
                 $allowedModules[$Module['tabid']] = $Module->tablabel;
            }
        }
        if(!empty($allowedModules))
            return $allowedModules;
        else 
            return 'No module Found';
        // echo "<pre>";print_r($allowedModules);die;
        // if (!empty($allowedModules)) {
        //     return $this->asJson([
        //         'status' => 'success',
        //         'data' => $allowedModules,
        //     ]);
        // } else {
        //     return $this->asJson([
        //         'status' => 'error',
        //         'message' => 'No Module found for loggedin user',
        //         'data' => ''
        //     ]);
        // }
    }

    protected function getowners()
    {
        $id = \Yii::$app->user->id;
        $alluser = [];
        $users = User::find()->where(['deleted'=>0])->all();
        foreach($users as $users){
                 $alluser[$users['id']] = $users['first_name'] ." ".$users['last_name'];
        }
        // echo "<pre>";print_r($alluser);die;
        if(!empty($alluser))
            return $alluser;
        else 
            return 'No User Found';
        // echo "<pre>";print_r($allowedModules);die;
        // if (!empty($allowedModules)) {
        //     return $this->asJson([
        //         'status' => 'success',
        //         'data' => $allowedModules,
        //     ]);
        // } else {
        //     return $this->asJson([
        //         'status' => 'error',
        //         'message' => 'No Module found for loggedin user',
        //         'data' => ''
        //     ]);
        // }
    }

    // Handle AJAX request for DataTables
    public function actionExportrequestdata()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        // $query = Exportrequest::find();

         


        $model = new AccessCheck();
        $userId = Yii::$app->user->id;
        $tabs = $model->tabs($userId, $this->ModuleName);
        $profile = $model->profile($userId, $tabs, $this->ModuleName);
        $modelaccess = $model->moduleaccess($userId, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($userId, $profile);
        // print_r($rolebasedrecord);die;
        $hasadminpower = $model->hasadminpower($profile);
        $modulepermission = $model->modulepermission($profile, $tabs);
        // echo $ModuleName;die;
        $createpermission = $model->checkpermission($userId, $this->ModuleName, 'create');
        $editpermission = $model->checkpermission($userId, $this->ModuleName, 'edit');
        $deletepermission = $model->checkpermission($userId, $this->ModuleName, 'delete');
        $listpermission = $model->checkpermission($userId, $this->ModuleName, 'list');
        $detailpermission = $model->checkpermission($userId, $this->ModuleName, 'detail');
        $approvepermission = $model->checkpermission($userId, $this->ModuleName, 'approvelist');
        $importpermission = $model->checkpermission($userId, $this->ModuleName, 'import');
        $exportpermission = $model->checkpermission($userId, $this->ModuleName, 'export');

        //get admin ids
        $adminowners = $model->getadminids($userId, $profile);


    if($hasadminpower == 1){
       $query = Exportrequest::find()
        ->alias('er')
        ->leftJoin('tab t', 't.tabid = er.module_name')
        ->select(['er.*', 't.*'])
        ->orderBy(['er.export_request_id' => SORT_DESC])  // DESC order
        ->asArray()
        ->all();
    }else{
        $query = Exportrequest::find()
        ->alias('er')
        ->leftJoin('tab t', 't.tabid = er.module_name')
        ->select(['er.*', 't.*'])
        ->where(['er.ownerid' => $userId]) // filter by user_id
        ->orderBy(['er.export_request_id' => SORT_DESC])  // DESC order
        ->asArray()
        ->all();
    }
            // echo "<pre>";print_r($query);die;
        $data = [];
        foreach ($query as $exportdata) {
            //  echo "<pre>";print_r($exportdata);die;
            $download_url = 'Pending.';
            if($exportdata['status'] == 2)
            {
                // echo "in if";die;
                // $download_url  = '<a href="' . \Yii::$app->urlManager->createUrl(['exportrequest/exportalldataandsave', 
                // 'tabid' => $exportdata['tabid'],
                // 'modulename'=> $exportdata['name'],
                // 'tablename'=> $exportdata['tablename'],
                // 'fieldid'=> $exportdata['tablekeyid'],
                // 'from_date' => $exportdata['from_date'],
                // 'to_date' => $exportdata['to_date'],
                // ]) . '" class="">Download</a>';
                $filepath  = dirname(\Yii::getAlias('@app')).'/api/exports/';
                if($exportdata['export_all'] == 0){
                    $filename = "Export_Request_".$exportdata['export_request_no'] ."_" .ucfirst($exportdata['name'])."_".  $exportdata['from_date'] ."_TO_". $exportdata['to_date'] . ".xls"; 
                }
                else if($exportdata['export_all'] == 1)
                {
                    $filename = "Export_Request_".$exportdata['export_request_no'] ."_" .ucfirst($exportdata['name'])."_Export_All.xls"; 
                }
                if (file_exists($filepath . $filename))
                {
                    $download_url  = '<a href="'.\Yii::$app->urlManager->createUrl(['exportrequest/downloadfile','filename' => $filename]) .'">Download</a>';
                } 

            } else if($exportdata['status'] == 3)
            {
                $download_url = 'No Data Found.';
            }

            $fromdate = $todate = '-';
            if(isset($exportdata['from_date']) && !empty($exportdata['from_date'])){
                $stimestamp = strtotime($exportdata['from_date']);
                $fromdate   = date('d-m-Y', $stimestamp);
            }

            if(isset($exportdata['to_date']) && !empty($exportdata['to_date'])){
                $etimestamp = strtotime($exportdata['to_date']);
                $todate     = date('d-m-Y', $etimestamp);
            }
            $data[] = [
                'id' => $exportdata['export_request_id'],
                'export_request_no' => $exportdata['export_request_no'],
                'from_date' => $fromdate,
                'to_date' => $todate,
                'module_name'=> $exportdata['tablabel'],
                'report_link'=> $download_url, //'<a href="' . \Yii::$app->urlManager->createUrl(['exportrequest/update', 'export_request_id' => $exportdata->export_request_id]) . '" class="">Download</a>',
                // 'action' => '<a href="' . \Yii::$app->urlManager->createUrl(['exportrequest/update', 'export_request_id' => $exportdata['export_request_id']]) . '" class="btn btn-primary btn-sm">Edit</a>' 
            ];
        }

        return [
            'data' => $data,
        ];
    }


    public function actionDownloadfile($filename)
    {
        $file  = dirname(\Yii::getAlias('@app')).'/api/exports/'. $filename;

        if (!file_exists($file)) {
            echo "file not found.";
            throw new \yii\web\NotFoundHttpException("File not found");
        }

        return \Yii::$app->response->sendFile($file, $filename, [
            'mimeType' => 'application/octet-stream',
            'inline' => false, // force download
        ]);
    }

     public function setAutoNo($tabs,$table_name)
    {
        $model = new AutoNo();
        $upAutoNo = $model->setAutomoduleno($tabs, $table_name);
        return $upAutoNo;
    }
}
