<?php

namespace backend\modules\gateoutward\controllers;

use app\models\Attachments;
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
    public $ModuleName = 'gateoutward';
    public $FieldId = 'gateoutward_id';
    public $TableName = 'gate_outward';
    public $TabLabel = 'Gate Outward';
    public $TabId = '99';

    //  public function beforeAction($action)
    // {
    //     $this->layout = '@app/views/layouts/main';  // Set the main layout before each action
    //     return parent::beforeAction($action);
    // }

    public function actionGetvehicledetail()
    {
        $vehicle_no = Yii::$app->request->post('vehical_no');
        if ($vehicle_no) {
            $query = new Query();
                $query->select([
                    'invoice_number',
                    "DATE_FORMAT(invoice_date, '%d-%m-%Y') AS invoice_date",
                    'invoice_image',
                    'gate_pass_number AS gatepass_number',
                    'gate_pass_photo AS gatepass_image'
                ])
                ->from('vehicle_loading')
                ->andWhere(['vehicleloading_id' => $vehicle_no]);
            $command = $query->createCommand();
            $result = $command->queryAll();

            if(count($result)> 0 && isset($result[0])){
                return $this->asJson([
                    'status' => 'success',
                    'data' => $result[0]
                ]);
            }
        }
        return $this->asJson([
            'status'=> 'error',
        ]);
    }

    public function actionGetattachmentpath()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $attachmentId = Yii::$app->request->post('attachment_id');

        $download_url  = \Yii::$app->urlManager->createUrl(['/gateoutward/downloadfile','fileid' => $attachmentId]);

        return [
            'status' => 'success',
            'file_url' => $download_url
        ];
    }

    public function actionDownloadfile($fileid)
    {   
        $record = Attachments::findOne(['attachmentsid' => $fileid]);
        $filePath = Yii::getAlias('@webroot/' . $record->path);
        if (!file_exists($filePath)) {
            throw new \yii\web\NotFoundHttpException('File not found');
        }

        return Yii::$app->response->sendFile($filePath);
    }


}
