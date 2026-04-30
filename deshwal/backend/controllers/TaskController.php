<?php

namespace backend\controllers;

use backend\models\Task;
use backend\models\TaskSearch;
// use yii\web\Controller;
use common\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use Yii;
use yii\web\BadRequestHttpException;

/**
 * TaskController implements the CRUD actions for Task model.
 */
class TaskController extends Controller
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
     * Lists all Task models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $highTasks = Task::find()->where(['priority' => 'High'])->all();
        $mediumTasks = Task::find()->where(['priority' => 'Medium'])->all();
        $lowTasks = Task::find()->where(['priority' => 'Low'])->all();

        return $this->render('index', [
            'highTasks' => $highTasks,
            'mediumTasks' => $mediumTasks,
            'lowTasks' => $lowTasks,
        ]);
    }

    /**
     * Displays a single Task model.
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
     * Creates a new Task model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Task();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Task model.
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
     * Deletes an existing Task model.
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


    public function actionUpdatePriority()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;

        if (!$request->isAjax || !$request->isPost) {
            throw new BadRequestHttpException('Invalid request.');
        }

        $id = $request->post('id');
        $priority = $request->post('priority');

        // Validate priority
        $validPriorities = ['High', 'Medium', 'Low'];
        if (!in_array($priority, $validPriorities)) {
            return ['success' => false, 'message' => 'Invalid priority value.'];
        }

        // Find the task
        $task = Task::findOne($id);
        if (!$task) {
            return ['success' => false, 'message' => 'Task not found.'];
        }

        // Update priority
        $task->priority = $priority;

        if ($task->save()) {
            return ['success' => true];
        } else {
            // Capture validation errors
            return ['success' => false, 'message' => $task->getErrors()];
        }
    }

    /**
     * Finds the Task model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Task the loaded model
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Task::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested task does not exist.');
    }
}
