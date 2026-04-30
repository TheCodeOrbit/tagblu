<?php

namespace backend\controllers;


use common\models\User;

use Yii;
// use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\data\ActiveDataProvider;
use common\components\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * ProfileController implements the CRUD actions for Profile model.
 */
class PasswordupdateController extends Controller
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
                        'delete' => ['GET'],
                    ],
                ],
            ]
        );
    }


    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => User::find(),
            /*
            'pagination' => [
                'pageSize' => 50
            ],
            'sort' => [
                'defaultOrder' => [
                    'roleid' => SORT_DESC,
                ]
            ],
            */
        ]);
        $this->layout = '@app/views/layouts/main-one';
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionProfileview()
    {


        $id = Yii::$app->user->id;
        $activeroleId = Yii::$app->session->get('active_profile_id');

        // Fetch user details
        $model = User::findOne($id);

        // Fetch the profile name and user name
        $userData = Yii::$app->db->createCommand("
                              SELECT profile.profilename, user.first_name, user.last_name, user.email,user.profilepic 
                              FROM profile
                              JOIN profile2tab ON profile2tab.profileid = profile.profileid
                              JOIN role2profile ON role2profile.profileid = profile.profileid
                              JOIN role ON role.roleid = role2profile.roleid
                              JOIN user2role ON user2role.roleid = role.roleid
                              JOIN user ON user.id = user2role.userid
                              WHERE user.id = :uid
                              AND user2role.roleid = :roleid
                              LIMIT 1
                          ")
            ->bindValue(':uid', $id)
            ->bindValue(':roleid', $activeroleId)
            ->queryOne();

        $profilename = $userData['profilename'] ?? 'Unknown'; // Set 'Unknown' if profile name is not found

        if ($model->load(Yii::$app->request->post())) {
            $currentPasswordInput = Yii::$app->request->post('User')['current_password'];
            $newPasswordInput = Yii::$app->request->post('User')['password'];

            if (!empty($currentPasswordInput)) {
                // Validate Old Password
                if (!Yii::$app->security->validatePassword($currentPasswordInput, $model->password_hash)) {
                    $model->addError('current_password', 'current password is incorrect.');
                } else {
                    // If Old Password is correct, check New Password
                    if (empty($newPasswordInput)) {
                        $model->addError('password', 'New Password is required when Old Password is provided.');
                    } else {
                        $model->setPassword($newPasswordInput); // Hashes and sets the new password
                    }
                }
            }

            // Update email
            $model->email = $_POST['User']['email'];

            // Handle image upload
            $imageFile = UploadedFile::getInstance($model, 'profilepic');
            if ($imageFile) {
                $filePath = 'thememain/profile/' . $imageFile->name;

                if ($imageFile->saveAs($filePath)) {
                    $model->profilepic = $imageFile->name;
                } else {
                    $model->addError('profilepic', 'Failed to upload image.');
                }
            }

            // Save model
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Profile updated successfully');
                return $this->redirect(['passwordupdate/profileview']);
            } else {
                Yii::$app->session->setFlash('error', 'Failed to update profile. Please try again.');
            }
        }

        $this->layout = '@app/views/layouts/main-one';
        return $this->render('@app/views/tetra/profileview', [
            'model' => $model, // Correct variable name passed to the view
            'profilename' => $profilename,
        ]);
    }

    /* ==============================
     * code addedby ptpatel to preview files on date 24-02-2026
     * TEMP UPLOAD (AJAX)
     * ============================== */
    public function actionTempupload($module)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $file = UploadedFile::getInstanceByName('file');
        if (!$file) {
            return ['status' => 'error', 'message' => 'No file'];
        }

        $path = Yii::getAlias("@runtime/temp-uploads/");
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $fileName = uniqid($module . '_') . '.' . $file->extension;
        $file->saveAs($path . $fileName);
        // Store in session
        Yii::$app->session->set("temp_attachment_{$module}", [
            'file' => $fileName,
            'ext'  => $file->extension,
            'path' => $path,
        ]);

        return [
            'status' => 'success',
            'url'    => Yii::$app->urlManager->createUrl([
                $module.'/previewtemp',
                'module' => $module,
                'file'   => $fileName
            ]),
            'type' => $file->extension
        ];
    }

    /* ==============================
     * INLINE PREVIEW (NO DOWNLOAD)
     * ============================== */
    public function actionPreviewtemp($module, $file)
    {
        $path = Yii::getAlias("@runtime/temp-uploads/") . $file;

        if (!file_exists($path)) {
            throw new NotFoundHttpException('File not found');
        }

        return Yii::$app->response->sendFile(
            $path,
            null,
            ['inline' => true]
        );
    }
    //end code added by ptpatel to preview files on date 24-02-2026
}
