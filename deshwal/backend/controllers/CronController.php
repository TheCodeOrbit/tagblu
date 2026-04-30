<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\BadRequestHttpException;

class CronController extends Controller
{
    public $enableCsrfValidation = false;

    protected function getCronToken()
    {
        return Yii::$app->params['cronToken'];
    }

    public function beforeAction($action)
    {
        $token = Yii::$app->request->get('token');
        // print_r("Received token: $token\n");
        // print_r("Expected token: " . $this->getCronToken() . "\n");
        // exit;
        if ($token !== $this->getCronToken()) {
            Yii::$app->response->statusCode = 403;
            echo 'Forbidden';
            return false;
        }
        return parent::beforeAction($action);
    }

    /**
     * Generic router:
     *  - cron/run?job=sales-stock&token=pA878zX9mLq2YvD3sE4rT5uV6wX7yZ0a!@$%^
     *  - cron/run?job=attachment-garbage&token=pA878zX9mLq2YvD3sE4rT5uV6wX7yZ0a!@$%^
     *  Daily SO PO approve report- cron/run?job=approved-so-po-report&token=pA878zX9mLq2YvD3sE4rT5uV6wX7yZ0a!@$%^
     *  Approval date updation- cron/run?job=so-po-approve&token=pA878zX9mLq2YvD3sE4rT5uV6wX7yZ0a!@$%^
     */
    public function actionRun($job)
    {
        echo "Starting cron job: $job\n";

        switch ($job) {

            case 'sales-stock':
                $this->runSalesOrderStockCron();
                break;

            case 'attachment-garbage':
                $this->runAttachmentGarbageCron();
                break;
            case 'approved-so-po-report':
                $this->runApproveSOPOCron();
                break;
            case 'audit_cron':
                $this->runUpdateApproveDate();
                break;

            default:
                throw new BadRequestHttpException('Unknown cron job: ' . $job);
        }

        return 'OK: job=' . $job . ' executed at ' . date('Y-m-d H:i:s');
    }


    protected function runSalesOrderStockCron()
    {
        $controller = new \console\controllers\SalesOrderStockController(
            'sales-order-stock',
            Yii::$app
        );
        $controller->actionCalcDailyStockAll();

    }

    protected function runAttachmentGarbageCron()
    {
        $controller = new \console\controllers\AttachmentGarbageController(
            'attachment-garbage',
            Yii::$app
        );
        $controller->actionRun(); 
    }

    protected function runApproveSOPOCron()
    {
        $controller = new \console\controllers\ReportController(
            'approved-so-po-report',
            Yii::$app
        );
        $controller->actionDaily(); 
    }
    protected function runUpdateApproveDate()
    {
        $controller = new \console\controllers\AuditController(
            'audit_cron',
            Yii::$app
        );
        $controller->actionRun(); 
    }
}
