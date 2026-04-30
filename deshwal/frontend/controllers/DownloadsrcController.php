<?php
namespace frontend\controllers;


use Yii;
// use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Controller;

use DateTime;
use DateTimeZone;
use yii\db\Expression;

class DownloadsrcController extends Controller
{
    public $enableCsrfValidation = false; // Disable CSRF validation for API
    private $apiToken;

    public function init()
    {
        // $this->apiToken = Yii::$app->params['apiToken'];
    }




    function decode_url($encoded_url)
    {
        return base64_decode($encoded_url);
    }
    function actionIndex()
    {


        // In download.php, get the base64-encoded URL from the query string
        if (isset($_GET['url'])) {
            // Get the base64-encoded URL from the query string
            $encodedFileUrl = $_GET['url'];

            // Decode the URL to get the file path
            $fileUrl = $this->decode_url($encodedFileUrl);

            // Check if the file exists
            if (file_exists($fileUrl)) {
                // If the file exists, serve it for download
                header('Content-Type: application/csv');
                header('Content-Disposition: attachment; filename="' . basename($fileUrl) . '"');
                readfile($fileUrl);
                exit;
            } else {
                echo 'File not found!';
            }
        } else {
            echo 'Invalid request!';
        }
    }
}
