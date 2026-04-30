<?php

namespace backend\controllers;

use app\models\Tab;
use common\models\WorkflowRule;
use common\models\WorkflowTemplate;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Html;

/**
 * WorkflowruleController implements the CRUD actions for WorkflowRule model.
 */
class WorkflowruleController extends Controller
{
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
     * Lists all WorkflowRule models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => WorkflowRule::find(),
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
        $this->layout = '@app/views/layouts/main-one';
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single WorkflowRule model.
     * @param int $id
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
     * Creates a new WorkflowRule model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new WorkflowRule();
        $templatemodel = new WorkflowTemplate();
        $tabs = (new \yii\db\Query())
            ->select(['tablabel', 'name'])
            ->from('tab')
            ->where(['presence'=>0,"visible"=>0])
            ->orderBy(['tablabel' => SORT_ASC])
            ->all();

        // $moduleList = \yii\helpers\ArrayHelper::map($tabs, 'tablename', 'tablabel');
        $moduleList = \yii\helpers\ArrayHelper::map($tabs, 'name', 'tablabel');
        $templates = (new \yii\db\Query())
            ->select(['name','id'])
            ->from('workflow_template')
            ->orderBy(['id' => SORT_ASC])
            ->all();
        $templateList = \yii\helpers\ArrayHelper::map($templates, 'id', 'name');
        if ($this->request->isPost) {
            if ($model->copy_template_id) {
                // user selected existing template → no new template creation
                $model->template_id = $model->copy_template_id;
                $model->created_by = Yii::$app->user->id;
                if ($model->load($this->request->post()) && $model->save()) {                 
                        return $this->redirect(['index']);                 
                }
            } else {
                if ($templatemodel->load($this->request->post()) && $templatemodel->save())
                {                
                    $model->template_id = $templatemodel->id;
                    $model->created_by = Yii::$app->user->id;
                    // echo "<pre>"; print_r($_POST);print_r($model->attributes);die;
                   if ($model->load($this->request->post())) {
                        if (is_array($model->stage_id)) {
                            $model->stage_id = implode(',', array_map('intval', $model->stage_id));
                        }
                        if ($model->save()) {
                            return $this->redirect(['index']);
                        }
                    }
                }
            }
            
        } else {
            $model->loadDefaultValues();
            $templatemodel->loadDefaultValues();
        }
        $this->layout = '@app/views/layouts/main-one';
        return $this->render('create', [
            'model' => $model,
            'moduleList' => $moduleList,
            'templateList' => $templateList,
            'templatemodel' =>$templatemodel,
        ]);
    }

    /**
     * Updates an existing WorkflowRule model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $templatemodel = $this->tempfindModel($model->template_id);
        $tabs = (new \yii\db\Query())
            ->select(['tablabel', 'name'])
            ->from('tab')
            ->where(['presence'=>0,"visible"=>0])
            ->orderBy(['tablabel' => SORT_ASC])
            ->all();

        $moduleList = \yii\helpers\ArrayHelper::map($tabs, 'name', 'tablabel');
         $templates = (new \yii\db\Query())
            ->select(['name','id'])
            ->from('workflow_template')
            ->orderBy(['id' => SORT_ASC])
            ->all();
        $templateList = \yii\helpers\ArrayHelper::map($templates, 'id', 'name');
        // if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
        //     return $this->redirect(['index']);
        // }
        if ($this->request->isPost) {
            // if ($templatemodel->load($this->request->post()) && $templatemodel->save()) {                
            //     $model->template_id = $templatemodel->id;
            //         if ($model->load($this->request->post()) && $model->save()) {                 
            //                 return $this->redirect(['index']);                 
            //         }
            //     }
            if ($model->copy_template_id) {
                // user selected existing template → no new template creation
                $model->template_id = $model->copy_template_id;
                $model->created_by = Yii::$app->user->id;
                if ($model->load($this->request->post()) && $model->save()) {                 
                        return $this->redirect(['index']);                 
                }
            } else {
                if ($templatemodel->load($this->request->post()) && $templatemodel->save())
                {                
                    $model->template_id = $templatemodel->id;
                    $model->created_by = Yii::$app->user->id;
                    $model->stage_id = implode(",",$model->stage_id);
                    if ($model->load($this->request->post()) && $model->save()) {                 
                            return $this->redirect(['index']);                 
                    }
                }
            }
            
        }

       $this->layout = '@app/views/layouts/main-one';
        return $this->render('update', [
            'model' => $model,
            'moduleList' => $moduleList,
            'templateList' => $templateList,
            'templatemodel' =>$templatemodel,
        ]);
    }

    /**
     * Deletes an existing WorkflowRule model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the WorkflowRule model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return WorkflowRule the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WorkflowRule::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    protected function tempfindModel($id)
    {
        if (($model = WorkflowTemplate::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

   public function actionGetallworkflowrules()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $rows = WorkflowRule::find()
            ->select(["name","id","module","active","template_id"])
            ->asArray()
            ->all();

        foreach ($rows as &$row) {
            $row['module'] = str_replace('_', '', $row['module']);// te remove _
            // Get module name
            $tab = Tab::find()->select('tablabel')->where(["name"=>$row['module']])->asArray()->one(); // if you have lookup table
            $row['module_name'] =$tab['tablabel'];
            $row['active'] = ($row['active'] == 1) ? 'Yes' : 'No';
            // Get template name
            $row['template'] = WorkflowTemplate::find()
                ->select('name')
                ->where(['id' => $row['template_id']])
                ->scalar() ?? '-';

            // Action Button
            $row['action'] = Html::a(
                'Edit',
                ['workflowrule/update', 'id' => $row['id']],
                ['class' => 'btn btn-sm btn-primary']
            );
        }

        return [
            'data' => $rows   // DataTables ONLY needs this
        ];
    }

    public function actionGetfields($tabname)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        // $tablename = (new \yii\db\Query())
        //             ->select('tablename')
        //             ->from('tab')->where(['tabid' => $tabid])->scalar();
        
        // $fields = (new \yii\db\Query())
        //     ->select(['fieldname', 'fieldlabel', 'columnname'])
        //     ->from('field')
        //     ->where(['tablename'=>$tabname])
        //     ->all();
        $fields = (new \yii\db\Query())
        ->select([
            't.name',
            'f.fieldname',
            'f.fieldlabel',
            'f.columnname'
        ])
        ->from(['f' => 'field'])
        ->innerJoin(['t' => 'tab'], 't.tabid = f.tabid')
        ->where(['t.name' => $tabname])
        ->all();

        return $fields;
    }

    public function actionGetstagedata($modulename, $field)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        /*  Get module + field info */
        $fieldData = (new \yii\db\Query())
            ->select([
                't.tabid',
                't.name AS modulename',
                'f.fieldid',
                'f.fieldlabel',
                'f.columnname'
            ])
            ->from(['t' => 'tab'])
            ->innerJoin(['f' => 'field'], 'f.tabid = t.tabid')
            ->where([
                't.name' => $modulename,
                'f.columnname' => $field
            ])
            ->one();
        // echo "<pre>";print_r($fieldData);die;
        if (empty($fieldData)) {
            return [
                'success' => false,
                'message' => 'Module or field not found'
            ];
        }

        /*  Get picklist configuration */
        $picklist = (new \yii\db\Query())
            ->select([
                'targettable',
                'targetfield',
                'dispfield',
                'default_value'
            ])
            ->from('picklist')
            ->where(['fieldid' => $fieldData['fieldid']])
            ->one();

        if (empty($picklist)) {
            return [
                'success' => false,
                'message' => 'Picklist configuration not found'
            ];
        }

        /*  Fetch picklist values */
        $items = (new \yii\db\Query())
            ->select([
                "{$picklist['targetfield']} AS id",
                "{$picklist['dispfield']} AS value"
            ])
            ->from($picklist['targettable'])
            ->all();

        /*  Final clean response */
        return [
            'success' => true,
            'items' => $items
        ];
    }


}
