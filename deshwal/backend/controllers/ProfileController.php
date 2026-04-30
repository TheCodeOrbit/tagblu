<?php

namespace backend\controllers;

use Yii;
use app\models\Profile;
use app\models\ProfileModtrackerBasic;
use app\models\Roles;
use yii\data\ActiveDataProvider;
// use yii\web\Controller;
use common\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProfileController implements the CRUD actions for Profile model.
 */
class ProfileController extends Controller
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

    /**
     * Lists all Profile models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Profile::find(),
            /*
            'pagination' => [
                'pageSize' => 50
            ],
            'sort' => [
                'defaultOrder' => [
                    'profile_id' => SORT_DESC,
                ]
            ],
            */
        ]);
        // echo "<pre>";
        // print_r($dataProvider);
        // die;
        $this->layout = '@app/views/layouts/main-one';
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    // Handle AJAX request for DataTables
    public function actionProfiledata()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $query = Profile::find();

        $data = [];
        foreach ($query->all() as $profile) {
            $data[] = [
                'id' => $profile->profileid,
                'name' => $profile->profilename,
                'description' => $profile->description,

                'action' => '<a href="' . Yii::$app->urlManager->createUrl(['profile/update', 'profileid' => $profile->profileid]) . '" class="btn btn-primary btn-sm">Edit</a>' . ' ' .
                    '<a href="' . Yii::$app->urlManager->createUrl(['profile/view', 'profileid' => $profile->profileid]) . '" class="btn btn-warning btn-sm">view</a>',
            ];
        }

        return [
            'data' => $data,
        ];
    }

    /**
     * Displays a single Profile model.
     * @param int $profile_id Profile ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($profileid)
    {
        $model = $this->findModel($profileid);
        // print_r($model);die;
        $this->layout = '@app/views/layouts/main-one';
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Profile model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        set_time_limit(500);
        $model = new Profile();
        //print_r($modelrole);die;
        $allwidgets =Yii::$app->db->createCommand("select * from widget")->queryAll();
        if ($this->request->isPost) {
            // Capture the 'tabs[]' array
            $tabarray = Yii::$app->request->post('tabs', []);
            // print_r($tabarray); // Print the tabs array to debug
            // print_r(Yii::$app->request->post()); // Debug 'Profile' data
            // die;
            if (!empty($_POST['Profile']) && !empty($tabarray)) {
                // echo "<pre>";print_r($_POST['fields']);die;
                //  print_r( $_POST['tabs[]']);die;
                if ($model->load($this->request->post()) && $model->save()) {
                    $profile_id = $model->profileid;


                    //insert into profile to tab
                    if (!empty($tabarray)) {
                        for ($i = 0; $i < count($tabarray); $i++) {
                            $tabid = $tabarray[$i];
                            $permi = array();
                            // $view = isset($_POST['1_' . $tabid]) ? 1 : '';
                            // $create = isset($_POST['2_' . $tabid]) ? 2 : '';
                            // $edit = isset($_POST['3_' . $tabid]) ? 3 : '';
                            // $delete = isset($_POST['4_' . $tabid]) ? 4 : '';
                            // $approve = isset($_POST['5_' . $tabid]) ? 5 : '';
                            

                            $view = isset($_POST['3_' . $tabid]) ? '3' : '';
                        $create = isset($_POST['0_' . $tabid]) ? '0' : '';
                        $edit = isset($_POST['1_' . $tabid]) ? '1': '';
                        $delete = isset($_POST['2_' . $tabid]) ? '2' : '';
                        $approve = isset($_POST['5_' . $tabid]) ? '5' : '';
                            //insert into profiletostanpermission                       

                            Yii::$app
                                ->db
                                ->createCommand()
                                ->insert('profile2tab', ['profileid' => $profile_id, 'tabid' => $tabid])
                                ->execute();
                            $permi = array($view, $create, $edit, $delete, $approve);
                            // echo "<pre>";
                            // print_r($permi);
                            // echo "</pre>";
                            // die;
                            for ($j = 0; $j < count($permi); $j++) {
                                $v = $permi[$j];
                                if ($v != '') {
                                    // echo "insert into profile2standardpermissions set  profileid = $profileid, tabid =$tabid, operation = $v, permissions = '0'";
                                    Yii::$app
                                    ->db
                                    ->createCommand("insert into profile2standardpermissions set  profileid = :profileid, tabid =:tabid, operation = :v, permissions = '0'")
                                    ->bindValue(":profileid",$profile_id)
                                    ->bindValue(":tabid",$tabid)
                                    ->bindValue(":v",$v)
                                    ->execute();
                                }
                            }
                        }
                    }

                    // Insert toggle data into profile2field

                    if (isset($_POST['fields']) && is_array($_POST['fields'])) {
                        foreach ($_POST['fields'] as $fieldData) {
                            // echo "<pre>";print_r($fieldData);die;
                            $fieldid = $fieldData['fieldid'];
                            $tabids = $fieldData['tabid'];
                            $visible = $fieldData['visible'];
                            $readonly = $fieldData['readonly'];

                            Yii::$app
                                ->db
                                ->createCommand()
                                ->insert('profile2field', [
                                        'profileid' => $profile_id,
                                        'tabid' => $tabids,
                                        'fieldid' => $fieldid,
                                        'visible' => $visible,
                                        'readonly' => $readonly,
                                    ])
                                ->execute();
                        }
                    }
                    if (isset($_POST['widgets']) && !empty($_POST['widgets'])) {
                        // foreach ($_POST['widgets'] as $widgetData) {
                            $widgetid = implode(",",$_POST['widgets']);
                            Yii::$app
                                ->db
                                ->createCommand()
                                ->insert('profile2widget', [
                                        'profileid' => $profile_id,
                                        'widgetid' =>$widgetid,
                                    ])
                                ->execute();
                        // }
                    }
                    $modlog = new ProfileModtrackerBasic();
                    $modlog->auditlog($model->oldAttributes = '', $_POST['fields'], 'profile', $model->profileid , $auditstatus = 0, Yii::$app->user->id);
                // echo "added";die;
                    return $this->redirect(['index']);
                }
            }
        } else {

            $model->loadDefaultValues();
        }
        $this->layout = '@app/views/layouts/main-one';
        return $this->render('create', [
            'model' => $model,            
            'widgets' => $allwidgets,
        ]);
    }

    public function actionSaveToggleState()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $fieldId = Yii::$app->request->post('fieldId');
        $visible = Yii::$app->request->post('visible');
        $readonly = Yii::$app->request->post('readonly');
        $tabid = Yii::$app->request->post('tabid'); // Pass tabid if needed

        if ($fieldId !== null) {
            $row = (new \yii\db\Query())
                ->from('profile2field')
                ->where(['fieldid' => $fieldId])
                ->one();

            if ($row) {
                // Update existing record
                Yii::$app->db->createCommand()
                    ->update('profile2field', ['visible' => $visible, 'readonly' => $readonly], ['fieldid' => $fieldId])
                    ->execute();
            } else {
                // Insert new record if it doesn't exist
                Yii::$app->db->createCommand()
                    ->insert('profile2field', [
                            'fieldid' => $fieldId,
                            'tabid' => $tabid,
                            'visible' => $visible,
                            'readonly' => $readonly,
                        ])
                    ->execute();
            }

            return ['success' => true, 'message' => 'State saved successfully'];
        }

        return ['success' => false, 'message' => 'Invalid field ID'];
    }

    // public function actionCreate()
    // {
    //     $model = new Profile();

    //     if ($this->request->isPost) {
    //         if (!empty($_POST['profilename']) || !empty($_POST['description']) || !empty($_POST['tabs'])) {

    //             $sql = "INSERT INTO profile (profilename, description) 
    //             VALUES (:profilename, :description)";
    //             Yii::$app->db->createCommand($sql)
    //                 ->bindValue(':profilename', $_POST['profilename'])
    //                 ->bindValue(':description', $_POST['description'])
    //                 ->execute();

    //             // Retrieve the last inserted profile_id

    //             if ($sql) {

    //                 // $profile_id = $model->profileid; // Ensure this ID is correctly generated
    //                 $profile_id = Yii::$app->db->getLastInsertID();
    //                 $tabarray = $_POST['tabs'];

    //                 Yii::info("Profile created successfully with ID: {$profile_id}", 'debug');

    //                 // Insert into profile to tab
    //                 for ($i = 0; $i < count($tabarray); $i++) {
    //                     $tabid = $tabarray[$i];
    //                     $permi = array();
    //                     $view = isset($_POST['1_' . $tabid]) ? 1 : '';
    //                     $create = isset($_POST['2_' . $tabid]) ? 2 : '';
    //                     $edit = isset($_POST['3_' . $tabid]) ? 3 : '';
    //                     $delete = isset($_POST['4_' . $tabid]) ? 4 : '';
    //                     $approve = isset($_POST['5_' . $tabid]) ? 5 : '';

    //                     // Insert into profile2tab
    //                     try {
    //                         $result = Yii::$app
    //                             ->db
    //                             ->createCommand()
    //                             ->insert('profile2tab', ['profileid' => $profile_id, 'tabid' => $tabid])
    //                             ->execute();
    //                         if ($result) {
    //                             Yii::info("Inserted into profile2tab: ProfileID={$profile_id}, TabID={$tabid}", 'debug');
    //                         } else {
    //                             Yii::error("Failed to insert into profile2tab: ProfileID={$profile_id}, TabID={$tabid}", 'debug');
    //                         }
    //                     } catch (\Exception $e) {
    //                         Yii::error("Error inserting into profile2tab: " . $e->getMessage(), 'debug');
    //                     }

    //                     $permi = array($view, $create, $edit, $delete, $approve);

    //                     // Insert into profiletostanpermission
    //                     for ($j = 0; $j < count($permi); $j++) {
    //                         if (!empty($permi[$j])) {
    //                             try {
    //                                 $result = Yii::$app
    //                                     ->db
    //                                     ->createCommand()
    //                                     ->insert('profiletostanpermission', [
    //                                         'profileid' => $profile_id,
    //                                         'tabid' => $tabid,
    //                                         'operation' => $permi[$j],
    //                                         'permission' => '1',
    //                                     ])
    //                                     ->execute();
    //                                 if ($result) {
    //                                     Yii::info("Inserted into profiletostanpermission: ProfileID={$profile_id}, TabID={$tabid}, Operation={$permi[$j]}", 'debug');
    //                                 } else {
    //                                     Yii::error("Failed to insert into profiletostanpermission: ProfileID={$profile_id}, TabID={$tabid}, Operation={$permi[$j]}", 'debug');
    //                                 }
    //                             } catch (\Exception $e) {
    //                                 Yii::error("Error inserting into profiletostanpermission: " . $e->getMessage(), 'debug');
    //                             }
    //                         }
    //                     }
    //                 }

    //                 return $this->redirect(['index']);
    //             } else {
    //                 Yii::error("Failed to save profile: " . json_encode($model->errors), 'debug');
    //             }
    //         } else {
    //             Yii::error("Invalid data: profilename, description, or tabs missing", 'debug');
    //         }
    //     }

    //     return $this->render('create', [
    //         'model' => $model,
    //     ]);
    // }
    public function actionTabs()
    {

        $requestData = $_REQUEST;
        $draw = $requestData['draw'];
        $start = isset($requestData['start']) ? $requestData['start'] : 0;
        //echo $start;die;
        $rowperpage = isset($requestData['length']) ? $requestData['length'] : 5; // Rows display per page
        // $rowperpage = 2;
        $columnIndex = isset($requestData['order'][0]['column']); // Column index
        $columnName = isset($requestData['columns'][$columnIndex]['data']); // Column name
        echo $columnName;


        $columnSortOrder = isset($requestData['order'][0]['dir']) ? $requestData['order'][0]['dir'] : 'desc'; // asc or desc
        $searchValue = isset($requestData['search']['value']); // Search value
        $columns = array(
            1 => 'profileid',

        );

        $sql = "select profileid,profilename,description,if(enabled=1,'Active','Inactive') as enabled from profile where is_deleted=0";



        if (!empty($requestData['search']['value'])) {
            $sql .= " AND (profilename LIKE '%" . $requestData['search']['value'] . "%' ";
            $sql .= " OR description LIKE '%" . $requestData['search']['value'] . "%' )";
            //$sql.=" OR IF('%" . $requestData['search']['value'] . "%'  like '%active%',enabled =1 ,  enabled=0))";
        }


        $data = Yii::$app->db->createCommand($sql)->queryAll();
        $totalData = count($data);
        $totalFiltered = $totalData;


        $sql .= " ORDER BY " . $columns[$columnIndex] . "   " . $columnSortOrder . "  LIMIT " . $start . " ," . $rowperpage . "   ";

        //echo $sql;die;
        $result = Yii::$app->db->createCommand($sql)->queryAll();

        $data = array();
        $i = 1;

        foreach ($result as $key => $row) {

            $nestedData = array();
            //$nestedData[] = $i;
            $nestedData[] = $row["profilename"];
            $nestedData[] = $row["description"];
            $nestedData[] = $row["enabled"];
            $nestedData[] = $row["profileid"];
            $data[] = $nestedData;
            $i++;
        }

        $json_data = array(
            "draw" => intval($draw),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data   // total data array
        );


        return json_encode($json_data, JSON_HEX_QUOT | JSON_HEX_TAG);
    }

    /**
     * Updates an existing Profile model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $profile_id Profile ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function actionUpdate($profileid)
    {
        set_time_limit(500);
        $model = $this->findModel($profileid);

        $allwidgets =Yii::$app->db->createCommand("select * from widget")->queryAll();
        if ($this->request->isPost) {
            // print_r($_POST);die;
            if (!empty($_POST['profilename']) || !empty($_POST['description']) || !empty($_POST['tabs'] )) {

                if ($model->load($this->request->post()) && $model->save()) {
                    //get old fields detail for history 
                    $oldfields = Yii::$app
                        ->db
                        ->createCommand('SELECT `fieldid`,`visible`,`readonly` FROM profile2field WHERE profileid = :profileid')
                        ->bindValue(':profileid', $profileid)
                        ->queryAll();
                        $oldfieldsdata = [];

                        foreach ($oldfields as $row) {
                            $oldfieldsdata[$row['fieldid']] = [
                                'visible'  => $row['visible'],
                                'readonly' => $row['readonly']
                            ];
                        }
                    //delete from profile2tab
                    Yii::$app
                        ->db
                        ->createCommand()
                        ->delete('profile2tab', ['profileid' => $profileid])
                        ->execute();
                    //delete from profiletostanpermission
                    Yii::$app
                        ->db
                        ->createCommand()
                        ->delete('profile2standardpermissions', ['profileid' => $profileid])
                        ->execute();

                    //delete from profile2field
                    Yii::$app
                        ->db
                        ->createCommand()
                        ->delete('profile2field', ['profileid' => $profileid])
                        ->execute();


                    $tabarray = $_POST['tabs'];
                    //insert into profile to tab
                    for ($i = 0; $i < count($tabarray); $i++) {
                        $tabid = $tabarray[$i];
                        $permi = array();
                        $view = isset($_POST['3_' . $tabid]) ? '3' : '';
                        $create = isset($_POST['0_' . $tabid]) ? '0' : '';
                        $edit = isset($_POST['1_' . $tabid]) ? '1' : '';
                        $delete = isset($_POST['2_' . $tabid]) ? '2' : '';
                        $approve = isset($_POST['5_' . $tabid]) ? '5' : '';
                        $import = isset($_POST['4_' . $tabid]) ? '4' : '';
                        $export = isset($_POST['6_' . $tabid]) ? '6' : '';
                        //insert into profiletostanpermission                       

                        Yii::$app
                            ->db
                            ->createCommand()
                            ->insert('profile2tab', ['profileid' => $profileid, 'tabid' => $tabid])
                            ->execute();
                        $permi = array($view, $create, $edit, $delete, $approve, $import, $export);
                        // print_r($permi);die;
                        for ($j = 0; $j < count($permi); $j++) {
                            // echo "<br>";

                            $v = $permi[$j];
                            // echo "<br>";

                            if ($v != '') {
                                // echo "<br>";
                                // echo "insert into profile2standardpermissions set  profileid = $profileid, tabid =$tabid, operation = $v, permissions = '0'";
                                Yii::$app
                                ->db
                                ->createCommand("insert into profile2standardpermissions set  profileid = :profileid, tabid =:tabid, operation = :v, permissions = '0'")
                                ->bindValue(":profileid",$profileid)
                                ->bindValue(":tabid",$tabid)
                                ->bindValue(":v",$v)
                                ->execute();

                                // Yii::$app
                                //     ->db
                                //     ->createCommand()
                                //     ->insert('profile2standardpermissions', ['profileid' => $profileid, 'tabid' => $tabid, 'operation' => $v, 'permissions' => '0'])
                                //     ->execute();
                            }
                        }
                        
                    }
                    // die;
                    // Insert toggle data into profile2field
                    if (isset($_POST['fields']) && is_array($_POST['fields'])) {
                        foreach ($_POST['fields'] as $fieldData) {

                            // print_r($fieldData);
                            // die;
                            $fieldid = $fieldData['fieldid'];
                            $tabids = $fieldData['tabid'];
                            $visible = $fieldData['visible'];
                            $readonly = $fieldData['readonly'];

                            Yii::$app
                                ->db
                                ->createCommand()
                                ->insert('profile2field', [
                                        'profileid' => $profileid,
                                        'tabid' => $tabids,
                                        'fieldid' => $fieldid,
                                        'visible' => $visible,
                                        'readonly' => $readonly,
                                    ])
                                ->execute();
                        }
                    }
                    //code for dashboard widget
                    //delete from profile2field
                    Yii::$app
                        ->db
                        ->createCommand()
                        ->delete('profile2widget', ['profileid' => $profileid])
                        ->execute();

                    if (isset($_POST['widgets']) && !empty($_POST['widgets'])) {
                        // foreach ($_POST['widgets'] as $widgetData) {
                            $widgetid = implode(",",$_POST['widgets']);
                            Yii::$app
                                ->db
                                ->createCommand()
                                ->insert('profile2widget', [
                                        'profileid' => $profileid,
                                        'widgetid' =>$widgetid,
                                    ])
                                ->execute();
                        // }
                    }
                    // echo "updated"; die;
                    $modlog = new ProfileModtrackerBasic();
                    $modlog->auditlog($oldfieldsdata, $_POST['fields'], 'profile', $model->profileid , $auditstatus = 2, Yii::$app->user->id);
                // echo "update";die;
                    return $this->redirect(['index']);
                }
            }
        } else {

            $model->loadDefaultValues();
        }
        $this->layout = '@app/views/layouts/main-one';
        return $this->render('update', [
            'model' => $model,
            'widgets' => $allwidgets,
        ]);
    }

    /**
     * Deletes an existing Profile model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $profileid Profile ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($profile_id)
    {
        $this->findModel($profile_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Profile model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $profile_id Profile ID
     * @return Profile the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($profileid)
    {
        if (($model = Profile::findOne(['profileid' => $profileid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
