<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Attachments;

class FileController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['get-file-link', 'download'],
                        'allow' => true,
                        'roles' => ['@', '?'],
                    ],
                ],
            ],
        ];
    }

    public function actionGetFileLink($fileid)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $model = Attachments::findOne($fileid);
        if (!$model) {
            return ['success' => false, 'message' => 'File not found'];
        }

        $fileName = $model->path;
        $fullPath = Yii::getAlias('@webroot/' . $fileName);

        if (!file_exists($fullPath)) {
            return ['success' => false, 'message' => 'File does not exist'];
        }

        $downloadUrl = Yii::$app->urlManager->createAbsoluteUrl(['file/download', 'fileid' => $fileid]);

        return [
            'success' => true,
            'fileUrl' => $downloadUrl,
            'originalName' => basename($fileName),
        ];
    }

    public function actionDownload($fileid)
    {
        $model = Attachments::findOne($fileid);
        if (!$model) {
            throw new NotFoundHttpException("File not found");
        }

        $filePath = Yii::getAlias('@webroot/' . $model->path);

        if (!file_exists($filePath)) {
            throw new NotFoundHttpException("File does not exist");
        }

        return Yii::$app->response->sendFile($filePath, basename($model->path));
    }
}

?>