<?php

namespace backend\controllers;

use app\models\ModuleRelationModtrackerBasic;
use app\models\Tab;
use backend\models\AccessCheck;
use backend\models\ModuleRelation;
use backend\models\ModuleRelationSearch;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ModuleRelationController implements the CRUD actions for ModuleRelation model.
 */
class ModulerelationController extends Controller
{
    protected $TabId = 108;
    protected  $FieldId = 'id';
    protected    $ModuleName = 'modulerelation';
    protected    $TableName = 'module_relation';
    protected    $TabLabel = 'Relation Modules';
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
     * Lists all ModuleRelation models.
     *
     * @return string
     */
    public function actionIndex()
    {
        // $searchModel = new ModuleRelationSearch();
        // $dataProvider = $searchModel->search($this->request->queryParams);

        // return $this->render('index', [
        //     'searchModel' => $searchModel,
        //     'dataProvider' => $dataProvider,
        // ]);
         $dataProvider = new ActiveDataProvider([
            'query' => ModuleRelation::find(),
            /*
            'pagination' => [
                'pageSize' => 50
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
            */
        ]);
        $removeTabIds = [33,37,38,41,59,60,61,62,67,68,69,70,76,77,80,81,82,92,93,94,103,104,];//inventory,role,profile,user,drillingcalculator,datawipingcalculator,shreddingcalculator,degaussingcalculator,segregation,tagging,sticker removal,cleaning,widgets,inventoryagaing,clubed inventory,productpricebook,servicepricebook,exportrequest,picklist,userloginhistory,sourcingdealreport,sourcingdealproductdetail
        $tablabels = Tab::find()->where(["presence"=>0])
        //->andWhere(["NOT IN", "tabid", $removeTabIds])
        ->select(["tabid","tablabel"])->all();
         $this->layout = '@app/views/layouts/main-one';
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'tablabels' =>$tablabels,
        ]);
    }

    /**
     * Displays a single ModuleRelation model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ModuleRelation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new ModuleRelation();
        $tablabels = Tab::find()->where(["presence"=>0])->select(["tabid","tablabel"])->all();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                // return $this->redirect(['view', 'id' => $model->id]);
                 $this->layout = '@app/views/layouts/main-one';
                return $this->redirect(['index']);
            }
        } else {
            $model->loadDefaultValues();
        }

       $this->layout = '@app/views/layouts/main-one';
        return $this->render('create', [
            'model' => $model,
            'tablabels' =>$tablabels,
        ]);
    }

    /**
     * Updates an existing ModuleRelation model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ModuleRelation model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ModuleRelation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return ModuleRelation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ModuleRelation::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    // Handle AJAX request for DataTables
    public function actionModulerealtiondata()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
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


    // if($hasadminpower == 1){
      // Fetch Module Relation with tab names
        $data = ModuleRelation::find()
            ->alias('mr')
            ->leftJoin('tab t1', 't1.tabid = mr.source_module')
            ->leftJoin('tab t2', 't2.tabid = mr.related_module')
            ->select([
                'mr.*',
                't1.tablabel AS source_module_name',
                't2.tablabel AS related_module_name',
            ])
            ->asArray()
            ->all();

        //  Fetch all fields once
        $allFields = (new \yii\db\Query())
            ->from('field')
            ->select(['fieldid', 'fieldname', 'fieldlabel', 'columnname', 'tabid'])
            ->where(['create_view'=>1,'detail_view'=>1,'edit_view'=>1])
            ->all();

        //  Group fields by tabid
        $fieldsByTabId = [];
        foreach ($allFields as $f) {
            // $fieldsByTabId[$f['tabid']][] = $f;
             $fieldsByTabId[$f['tabid']][$f['columnname']] = $f;
        }

        //  Build final data
        $result = [];
        foreach ($data as $row) {

            $id = $row['id'];
            $relatedTabId = $row['related_module'];              
            $relatedFieldLabel = $fieldsByTabId[$relatedTabId][$row['related_fieldname']]['fieldlabel'] ?? '';
            $relatedRecordFieldLabel = $fieldsByTabId[$relatedTabId][$row['related_recordfieldnme']]['fieldlabel'] ?? '';


            // Attach fields for this related module
            $fields = $fieldsByTabId[$relatedTabId] ?? [];
            $relatedCols = explode(',', $row['related_columns']);
            $labels = [];

            foreach ($relatedCols as $colName) {
                $colName = trim($colName);

                if (isset($fieldsByTabId[$relatedTabId][$colName])) {
                    $labels[] = $fieldsByTabId[$relatedTabId][$colName]['fieldlabel'];
                }
            }

            // Build final structured data
            $result[$id] = [
                'id' => $row['id'],
                // 'source_module' => $row['source_module'],
                // 'related_module' => $row['related_module'],
                // 'related_fieldname' => $row['related_fieldname'],
                // 'related_recordfieldnme'=>$row['related_recordfieldnme'],
                'related_fieldname' => $relatedFieldLabel,
                'related_recordfieldnme'=>$relatedRecordFieldLabel,
                'source_module_name' => $row['source_module_name'],
                'related_module_name' => $row['related_module_name'],
                'related_columns' => $row['related_columns'],
                'fields' => $fields,  
                 'related_column_labels' => implode(', ', $labels), // labels
                 'action' => '<a href="' . Yii::$app->urlManager->createUrl(['modulerelation/update', 'id' => $row['id']]) . '" class="btn btn-primary btn-sm">Edit</a>',
            ];
        }

        return [
            'data' => array_values($result),
        ];
    }

    public function actionGetrelatedmodules($tabid)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        // Your original query (unchanged)
        $data = ModuleRelation::find()
            ->alias('mr')
            ->leftJoin('tab t2', 't2.tabid = mr.related_module')
            ->select([
                'mr.*',
                't2.tablabel',
            ])
            ->where(["mr.source_module" => $tabid])
            ->asArray()
            ->all();

        // --------------------------------------------
        //  FETCH ALL FIELDS 
        // --------------------------------------------
        $allFields = (new \yii\db\Query())
            ->from('field')
            ->select(['fieldid', 'fieldname', 'fieldlabel', 'columnname', 'tabid'])            
            ->where(['create_view'=>1,'detail_view'=>1,'edit_view'=>1])
            ->all();

        // Group by tabid + columnname
        $fieldsByTabId = [];
        foreach ($allFields as $f) {
            $fieldsByTabId[$f['tabid']][$f['columnname']] = $f;
        }

        // --------------------------------------------
        //  Append related column labels 
        // --------------------------------------------
        foreach ($data as &$row) {

            $relatedTabId = $row['related_module'];         // tabid
            $relatedCols = explode(',', $row['related_columns'] ?? '');

            $labels = [];
            foreach ($relatedCols as $colName) {
                $colName = trim($colName);

                if (isset($fieldsByTabId[$relatedTabId][$colName])) {
                    $labels[] = $fieldsByTabId[$relatedTabId][$colName]['fieldlabel'];
                }
            }

            // Attach label string
            $row['related_column_labels'] = implode(', ', $labels);
        }

        return $data;
    }

    public function actionGetfields($tabid)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $tablename = (new \yii\db\Query())
                    ->select('tablename')
                    ->from('tab')->where(['tabid' => $tabid])->scalar();
        
        $fields = (new \yii\db\Query())
            ->select(['fieldname', 'fieldlabel', 'fieldid','columnname'])
            ->from('field')
            ->where(['tabid' => $tabid,'tablename'=>$tablename])            
            ->andWhere(['create_view'=>1,'detail_view'=>1,'edit_view'=>1])
            ->all();

        return $fields;
    }
    public function actionSaverelationmodulecolumns()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id');
        $related_columns = Yii::$app->request->post('related_columns');

        if (!$id) {
            return ['status' => 'error', 'message' => 'Invalid ID'];
        }


        if (is_array($related_columns) && !empty($related_columns)) {
            $related_columns = implode(",", $related_columns);
        }

        $old = (new \yii\db\Query())
            ->select('related_columns')
            ->from('module_relation')
            ->where(['id' => $id])
            ->scalar();

        if ($old === $related_columns) {
            return ['status' => 'success', 'message' => 'No changes were made.'];
        }
        $updated = Yii::$app->db->createCommand()
            ->update(
                'module_relation',      
                ['related_columns' => $related_columns], 
                ['id' => $id]      
            )
            ->execute();

        $add_log = new ModuleRelationModtrackerBasic();
        $add_log->modulerelationauditlog($old,$related_columns,Yii::$app->user->id,$id);           
        
        if ($updated) {
            return ['status' => 'success','message'=>'Column List Updated.'];
        }

        return ['status' => 'error', 'message' => 'Database update failed'];
    }

}
