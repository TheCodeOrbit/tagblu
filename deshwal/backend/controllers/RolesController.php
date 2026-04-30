<?php

namespace backend\controllers;

use app\models\Roles;
use yii\data\ActiveDataProvider;
// use yii\web\Controller;
use common\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

/**
 * RolesController implements the CRUD actions for Roles model.
 */
class RolesController extends Controller
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
     * Lists all Roles models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Roles::find(),
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

    /**
     * Displays a single Roles model.
     * @param int $roleid Role ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($roleid)
    {
        $this->layout = '@app/views/layouts/main-one';
        return $this->render('view', [
            'model' => $this->findModel($roleid),
        ]);
    }

    /**
     * Creates a new Roles model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Roles();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'roleid' => $model->roleid]);
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
     * Updates an existing Roles model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $roleid Role ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($roleid)
    {
        $model = $this->findModel($roleid);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'roleid' => $model->roleid]);
        }
        $this->layout = '@app/views/layouts/main-one';
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Roles model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $roleid Role ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($roleid)
    {
        $this->findModel($roleid)->delete();

        return $this->redirect(['index']);
    }



    /**
     * Finds the Roles model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $roleid Role ID
     * @return Roles the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($roleid)
    {
        if (($model = Roles::findOne(['roleid' => $roleid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    // Action role
    public function actionRole()
    {
        $this->layout = '@app/views/layouts/main-one';
        return $this->render('role');
    }
    //add role
    public function actionAddroles()
    {

        if (isset($_REQUEST['roleid'])) {

            $roleid = base64_decode($_REQUEST['roleid']);
            $sql = "SELECT * FROM role WHERE roleid='" . $roleid . "' and is_deleted = 0 and enabled = 1";
            $orgheaddetails = Yii::$app->db->createCommand($sql)->queryOne();
            // print_r($orgheaddetails);die;
            $sql = "SELECT distinct(profileid),profilename FROM profile";
            $profilelists = Yii::$app->db->createCommand($sql)->queryAll();

            // $orgheaddetails = Yii::app()->db->createCommand()
            //                    ->select()
            //                    ->from('role')
            //                    ->where("roleid=:roleid",array(':roleid'=>$roleid))
            //                    ->queryRow() ; 


            //                $profilelists = Yii::app()->db->createCommand()
            //                    ->selectDistinct('profileid,profilename')
            //                    ->from('profile')
            //                    ->queryAll();
            $this->layout = '@app/views/layouts/main-one';
            return $this->render('addRoles', ['orgheaddetails' => $orgheaddetails, 'profilelists' => $profilelists, 'action' => 'Add']);
        } else {
            echo "some error occurred";
            exit;
        }
    }
    public function actionEditroles()
    {

        if (isset($_REQUEST['roleid'])) {
            $roleid = base64_decode($_REQUEST['roleid']);
            $sql = "SELECT * FROM role WHERE roleid='" . $roleid . "'";
            $userrolelistS = Yii::$app->db->createCommand($sql)->queryOne();
            $depth = $userrolelistS['depth'];

            $sql = "SELECT distinct(profileid) as profileid FROM role2profile WHERE roleid='" . $roleid . "'";
            $userprofilearray = Yii::$app->db->createCommand($sql)->queryOne();



            // $connection=Yii::app()->db;

            // $command=$connection->createCommand()
            //                     ->select()
            //                     ->from('role')
            //                     ->where('roleid=:roleid',array(':roleid'=>$roleid));
            // $userrolelistS = $command->queryRow();
            // $depth = $userrolelistS['depth'];    

            // $userprofilearray = $connection->createCommand()
            //                                 ->selectDistinct('profileid')
            //                                 ->from('role2profile')
            //                                 ->where("roleid=:roleid",array(':roleid'=>$roleid))
            //                                 ->queryRow()  ; 




            $depth_parent = (int)$depth - 1;
            $sql = "SELECT * FROM role WHERE depth=" . $depth_parent;
            $orgheaddetails_parent = Yii::$app->db->createCommand($sql)->queryOne();

            // $orgheaddetails_parent = $connection->createCommand()
            //                                    ->select()
            //                                    ->from('role')
            //                                    ->where('depth=:depth',array(':depth'=>$depth_parent))
            //                                    ->queryRow();

            $roleid_parent = $orgheaddetails_parent['roleid'];


            $sql = "SELECT * FROM role WHERE roleid='" . $roleid_parent . "'";
            $orgheaddetails = Yii::$app->db->createCommand($sql)->queryOne();

            // $orgheaddetails = $connection->createCommand()
            //                          ->select()
            //                          ->from('role')
            //                          ->where('roleid=:roleid',array(':roleid'=>$roleid_parent))
            //                          ->queryRow();

            $sql = "SELECT distinct(profileid),profilename FROM profile where is_deleted=0 and enabled=1 ";
            $profilelists = Yii::$app->db->createCommand($sql)->queryAll();

            // $profilelists = $connection->createCommand()
            //                            ->selectDistinct('profileid,profilename')
            //                            ->from('profile')
            //                            ->queryAll();
            $this->layout = '@app/views/layouts/main-one';
            return $this->render(
                'addRoles',
                [
                    'orgheaddetails' => $orgheaddetails,
                    'userrolelistS' => $userrolelistS,
                    'action' => 'Edit',
                    'userprofilearray' => $userprofilearray,
                    'profilelists' => $profilelists
                ]
            );
        } else {
            echo "some error occurred";
            exit;
        }
    }



    public function actionRoledelete()
    {
        $roleid = base64_decode($_REQUEST['roleid']);
        // $command = Yii::$app->db->createCommand();
        // $command->delete('role', 'roleid=:roleid', array(':roleid'=>$roleid));
        //     $command->delete('role2profile', 'roleid=:roleid', array(':roleid'=>$roleid));
        Yii::$app
            ->db
            ->createCommand()
            ->delete('role', ['roleid' => $roleid])
            ->execute();
        Yii::$app
            ->db
            ->createCommand()
            ->delete('role2profile', ['roleid' => $roleid])
            ->execute();
        $this->redirect(['role']);
    }

    public function actionSubmitrole()
    {


        $user_rolename = filter_var($_POST['user_rolename'], FILTER_SANITIZE_STRING);
        $rolename = filter_var($_POST['rolename'], FILTER_SANITIZE_STRING);
        $roleid = $_POST['roleid'];
        $parentrole = $_POST['parentrole'];

        $depth = $_POST['depth'];
        $action = filter_var($_POST['action'], FILTER_SANITIZE_STRING);
        $profile = filter_var($_POST['profile'], FILTER_SANITIZE_STRING);

        $showinaccounts = isset($_POST['showinaccounts']) ? filter_var($_POST['showinaccounts'], FILTER_SANITIZE_STRING) : 0;
        $admin_edit_allow = isset($_POST['admin_edit_allow']) ? filter_var($_POST['admin_edit_allow'], FILTER_SANITIZE_STRING) : 0;


        if ($action == "Edit") {

            $currroleid = $_POST['roleiduser'];

            Yii::$app->db->createCommand()
            ->update('role', 
                [
                    'rolename' => $user_rolename, 
                    'showinaccounts' => $showinaccounts,  // ✅ Include all columns inside the same array
                    'admin_edit_allow' => $admin_edit_allow,//this is allow edit to admin in org section of account
                ], 
                ['roleid' => $currroleid]  // ✅ This should be the WHERE condition
            )
            ->execute();
        

            Yii::$app->db->createCommand()
                ->update('role2profile', ['profileid' => $profile], ['roleid' => $currroleid])
                ->execute();

            //         $command = Yii::app()->db->createCommand();
            // $command->update('role', array(
            //             'rolename'=>$user_rolename,
            //         ), 'roleid=:roleid', array(':roleid'=>$currroleid));

            //          $command = Yii::app()->db->createCommand();
            //  $command->update('role2profile', array(
            //             'profileid'=>$profile,
            //          ), 'roleid=:roleid', array(':roleid'=>$currroleid));

        } else {



            $depth = (int)$depth + 1;
            $sql = "SELECT roleid as maxid FROM role ORDER BY LENGTH( roleid ) DESC ,  `roleid` DESC ";
            $rolelists = Yii::$app->db->createCommand($sql)->queryOne();

            //    $connection=Yii::app()->db;
            // $q_rolelists="SELECT roleid as maxid FROM role ORDER BY LENGTH( roleid ) DESC ,  `roleid` DESC ";
            //     $command= $connection->createCommand($q_rolelists);
            // $rolelists = $command->queryRow();


            $id = str_replace("H", "", $rolelists['maxid']);
            $currroleid = "H" . ($id + 1);
            //echo "<pre>";print_r($currroleid);exit ;
            $curr_parentrole = $parentrole . "::" . $currroleid;

            Yii::$app
                ->db
                ->createCommand()
                ->insert('role', [
                    'roleid' => $currroleid,
                    'rolename' => $user_rolename,
                    'parentrole' => $curr_parentrole,
                    'depth' => $depth,
                    'allowassignedrecordsto' => 1,
                    'showinaccounts' => $showinaccounts,
                    'admin_edit_allow' => $admin_edit_allow, //this is allow edit to admin in org section of account
                ])
                ->execute();

            Yii::$app
                ->db
                ->createCommand()
                ->insert('role2profile', [
                    'roleid' => $currroleid,
                    'profileid' => $profile
                ])
                ->execute();

            // $command->insert('role', array(
            //             'roleid'=>$currroleid,
            //             'rolename'=>$user_rolename,
            //             'parentrole'=>$curr_parentrole,
            //             'depth'=>$depth,
            //             'allowassignedrecordsto'=>1,

            //         ));

            //         $command = Yii::app()->db->createCommand();
            // $command->insert('role2profile', array(
            //             'roleid'=>$currroleid,
            //             'profileid'=>$profile,
            //         ));



        }


        // if($action=="Edit"){
        //         Yii::app()->user->setFlash('successmgs','Role Successfully Edited');
        // }else{
        //         Yii::app()->user->setFlash('successmgs','Role Successfully Created');
        // }
        $this->redirect(['role']);



        //echo "<pre>";print_r($company);exit ;



    }
}
