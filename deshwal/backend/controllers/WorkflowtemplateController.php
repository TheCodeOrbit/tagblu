<?php

namespace backend\controllers;

use common\models\WorkflowTemplate;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * WorkflowTemplateController implements the CRUD actions for WorkflowTemplate model.
 */
class WorkflowtemplateController extends Controller
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
     * Lists all WorkflowTemplate models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => WorkflowTemplate::find(),
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
     * Displays a single WorkflowTemplate model.
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
     * Creates a new WorkflowTemplate model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new WorkflowTemplate();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['index']);
            }
        } else {
            $model->loadDefaultValues();
        }
            $this->layout = '@app/views/layouts/main-one';
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing WorkflowTemplate model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id
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
     * Deletes an existing WorkflowTemplate model.
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
     * Finds the WorkflowTemplate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return WorkflowTemplate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WorkflowTemplate::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionGetdata()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $data = WorkflowTemplate::find()->select(["name","id"])
            ->asArray()
            ->all();

        if (empty($data)) {
            return [
                'status' => 'error',
                'message' => 'No data found'
            ];
        }

        return [
            'status' => 'success',
            'data' => $data
        ];

    }

    public function actionGettemplatedatabyid()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $id = Yii::$app->request->post('template_id');

        if (empty($id)) {
            return [
                'status' => 'error',
                'message' => 'Template ID not found.',
                'data' => null,
            ];
        }

        // Fetch template using findOne for efficiency
        $data = WorkflowTemplate::find()
            ->where(['id' => $id])
            ->asArray()
            ->one();
        if (!$data) {
            return [
                'status' => 'error',
                'message' => 'Template not found.',
            ];
        }

        return [
            'status' => 'success',
            'data' => $data,
        ];
    }

    public function actionSavetemplate()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
            $id = Yii::$app->request->post('id');
            $name = Yii::$app->request->post('name');
            $subject = Yii::$app->request->post('subject');
            $body = Yii::$app->request->post('body');

            if (empty($id)) {
                throw new \Exception("Invalid ID.");
            }

            // Fetch old record for logging
            // $old = (new \yii\db\Query())
            //     ->from('workflow_template')
            //     ->where(['id' => $id])
            //     ->one();

            // if (!$old) {
            //     throw new \Exception("Template not found.");
            // }

            // Update record
            $updated = Yii::$app->db->createCommand()
                ->update(
                    'workflow_template',
                    [
                        'name' => $name,
                        'subject' => $subject,
                        'body' => $body
                    ],
                    ['id' => $id]
                )
                ->execute();

            if ($updated === false || $updated == 0) {
                throw new \Exception("Update failed. No changes applied.");
            }

            // If you want to add logs
            // $add_log = new ModuleRelationModtrackerBasic();
            // $add_log->modulerelationauditlog($old, $dynamicColumns, Yii::$app->user->id, $id);

            return [
                'status' => 'success',
                'message' => 'Template updated successfully.',
                'data' => ['id' => $id]
            ];

        } catch (\Throwable $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

}
