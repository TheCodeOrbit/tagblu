<?php
namespace backend\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use common\models\User;
use yii\db\Expression;

class ApiController extends Controller
{
    public $enableCsrfValidation = false; // Disable CSRF validation for API
    private $apiToken;

    public function init()
    {
        $this->apiToken = Yii::$app->params['apiToken'];
    }
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        return $behaviors;
    }
    public function beforeAction($action)
    {
        //allow CORS
        Yii::$app->response->headers->set('Access-Control-Allow-Origin', '*');
        Yii::$app->response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        Yii::$app->response->headers->set('Access-Control-Allow-Headers', 'Authorization, Content-Type');

        Yii::$app->response->format = Response::FORMAT_JSON;

        $headers = Yii::$app->request->headers;
        $authHeader = $headers->get('Authorization');

        if (!$authHeader || !preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) {
            Yii::$app->response->statusCode = 401;
            echo json_encode([
                'status' => 'error',
                'message' => 'Authorization header missing or invalid.',
                'data' => null
            ]);
            return false; // Stop execution
        }

        $token = $matches[1];
        if ($token !== $this->apiToken) {
            Yii::$app->response->statusCode = 401;
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid API token.',
                'data' => null
            ]);
            return false; // Stop execution
        }

        return parent::beforeAction($action);
    }

    // Handle API responses
    protected function apiResponse($status = 'success', $data = [], $message = '')
    {
        return [
            'status' => $status,
            'data' => $data,
            'message' => $message,
        ];
    }

    public function actionUsers()
    {
        $users = User::find()
            ->select(['id', new Expression('CONCAT(first_name, " ", last_name) AS name')])
            ->where(['deleted' => 0])
            ->andWhere(['!=', 'id', 1])
            ->asArray()
            ->all();

        return $this->apiResponse('success', $users, 'Users fetched successfully');
    }

    public function actionUser($id)
    {
        $user = User::find()
            ->select(['id', 'first_name', 'last_name', 'email'])
            ->where(['id' => $id, 'deleted' => 0])
            ->asArray()
            ->one();

        if ($user === null) {
            return $this->apiResponse('error', null, 'User not found');
        }

        return $this->apiResponse('success', $user, 'User fetched successfully');
    }

    public function actionSaveIqc()
    {
        $request = Yii::$app->request;
        $body = $request->bodyParams;

        if (empty($body['first_name']) || empty($body['last_name']) || empty($body['email']) || empty($body['password'])) {
            return $this->apiResponse('error', null, 'Missing required fields.');
        }

        $user = new User();
        $user->first_name = $body['first_name'];
        $user->last_name = $body['last_name'];
        $user->email = $body['email'];
        $user->username = $body['username'] ?? $body['email']; // if username not passed, use email
        $user->deleted = 0;

        if (method_exists($user, 'setPassword')) {
            $user->setPassword($body['password']);
        } else {
            $user->password = Yii::$app->security->generatePasswordHash($body['password']);
        }

        if (method_exists($user, 'generateAuthKey')) {
            $user->generateAuthKey();
        }

        if ($user->save()) {
            return $this->apiResponse('success', ['id' => $user->id], 'User created successfully');
        } else {
            return $this->apiResponse('error', $user->getErrors(), 'Failed to create user');
        }
    }
}
