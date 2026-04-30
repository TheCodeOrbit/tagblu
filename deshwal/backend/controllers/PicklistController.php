<?php

namespace backend\controllers;

use app\models\Field;
use app\models\PicklistModtrackerBasic;
use app\models\Tab;
use backend\models\AccessCheck;
use common\models\Picklist;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * PicklistController implements the CRUD actions for Picklist model.
 */
class PicklistController extends Controller
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
     * Lists all Picklist models.
     *
     * @return string
     */
    public function actionIndex1()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Picklist::find(),
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
     * Displays a single Picklist model.
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
     * Creates a new Picklist model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionIndex()
    {
        $model = new Picklist();
        $modulename = $this->getmodulenames();
        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $model->save();
                $this->layout = '@app/views/layouts/main-one';
                return $this->redirect(['index']);
            }
        } else {
            $model->loadDefaultValues();
        }
        $this->layout = '@app/views/layouts/main-one';
        return $this->render('create', [
            'model' => $model,
            'modulenames' => $modulename
        ]);
    }

    /**
     * Updates an existing Picklist model.
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
     * Deletes an existing Picklist model.
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

    /**
     * Finds the Picklist model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Picklist the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Picklist::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function getmodulenames()
    {
        $id = \Yii::$app->user->id;
        $allowedModules = [];
        $model = new AccessCheck();
        // $Modules = Tab::find()->where([
        //     'visible' => 0,
        //     'presence' => 0,
        // ])->all();
        $Modules = Field::find()
            ->alias('f')
            ->select(['f.tabid', 't.tablabel'])
            ->distinct()
            ->innerJoin(['t' => Tab::tableName()], 't.tabid = f.tabid')
            ->where(['f.uitype' => 8]) // all dropdowns
            ->asArray()
            ->all();
        // echo "<pre>";print_r($Modules);die;
        $allowedModules = [];
        // $allowedModules[0] = "Select";

        foreach ($Modules as $Module) {
            $allowedModules[$Module['tabid']] = $Module['tablabel'];
        }
        if (!empty($allowedModules))
            return $allowedModules;
        else
            return 'No module Found';
    }

public function actionGetpicklisttables()
    {

        \Yii::$app->response->format = Response::FORMAT_JSON;
        $tables = [];
        $moduleid = \Yii::$app->request->get('moduleid');
      
        $tbls = (new \yii\db\Query())
            ->select(['f.fieldid', 'f.fieldlabel', 'p.targettable'])
            ->from(['f' => 'field'])
            ->innerJoin(['p' => 'picklist'], 'f.fieldid = p.fieldid')
            ->where([
                'f.uitype' => 8,
                'f.tabid'  => $moduleid,
            ])
            ->andWhere(['not in', 'f.fieldname', ['ownerid']])
            ->andWhere(['not in', 'f.fieldid', ['193']]) 
            ->andWhere(['!=', 'p.targettable', 'user'])   
            ->all();
        foreach ($tbls as $Module) {
            // $tables[$Module['fieldid']] = $Module->fieldlabel;
            $tables[$Module['fieldid']] = $Module['fieldlabel'];
        }
        if (!empty($tables)) {
            return [
                'status' => 'success',
                'data'   => $tables,
            ];
        } else {
            return [
                'status'  => 'error',
                'message' => 'No Picklist Found',
            ];
        }
    }

public function actionGetpicklisttablerows()
    {

        \Yii::$app->response->format = Response::FORMAT_JSON;
        $tables = $parent_rows = $grand_parent_rows = [];
        $is_multiple = false;
        $selectedfieldid = \Yii::$app->request->get('selectedfieldid');
        $tbls = Picklist::find()->where([
            'fieldid' => $selectedfieldid,
        ])->one();
        $depdent_dd = (new \yii\db\Query())
            ->select(['*'])
            ->from('picklist_dependency')
            ->where(['child_table' => $tbls->targettable])
            ->one();
        // echo "<pre>";print_r($depdent_dd);die;
        if (isset($depdent_dd['child_table']) && $depdent_dd['child_table'] != '') {
            
            
            if($depdent_dd['child_table'] == 'prod_model'){
                $is_multiple = true;
            }
            $stateMap = [];
            $getparentrecord = (new \yii\db\Query())
                ->select(['*'])
                ->from([$depdent_dd['targettable']])
                ->where(["is_active" => 1])
                ->all();
            // echo "<pre>";print_r($getparentrecord);die;
            foreach ($getparentrecord as $getparentrow) {
                $parent_rows[$getparentrow[$depdent_dd['targetfield']]] = $getparentrow[$depdent_dd['dispfield']];
                $stateMap[$getparentrow[$depdent_dd['targetfield']]] = $getparentrow;
            }
            if($depdent_dd['child_table'] != 'currency'){
                
                $getgrand_depdent_dd = (new \yii\db\Query())
                    ->from('picklist_dependency')
                    ->where(['child_table' => $depdent_dd['targettable']])
                    ->one();
                if ($getgrand_depdent_dd) {
                    $getgrandparentrecord = (new \yii\db\Query())
                        ->select(['*'])
                        ->from([$getgrand_depdent_dd['targettable']])
                        ->where(["is_active" => 1])
                        ->all();
                    foreach ($getgrandparentrecord as $getgrandparentrow) {
                        $grand_parent_rows[$getgrandparentrow[$getgrand_depdent_dd['targetfield']]] = $getgrandparentrow[$getgrand_depdent_dd['dispfield']];
                    }
                }
            }
        }

        // echo "<pre>";print_r($grand_parent_rows);die;
        $page = \Yii::$app->request->get('page', 1);
        $pageSize = \Yii::$app->request->get('pageSize', 10);

        $query = (new \yii\db\Query())
            ->select(['*'])
            ->from($tbls->targettable)
            ->where(["is_active" => 1]);

        $totalCount = $query->count();
        $getrecord = $query
            ->offset(($page-1) * $pageSize)
            ->limit($pageSize)
            ->orderBy([$tbls->targetfield => SORT_DESC])
            ->all();
            
        foreach ($getrecord as $Module) {
            $state_id = $country_id = null;
            if (isset($depdent_dd['child_dependent_field'])) {
                $state_id = $Module[$depdent_dd['child_dependent_field']] ?? null; 
                // echo "<pre>";print_r($getgrand_depdent_dd);die;
                if (!empty($getgrand_depdent_dd) && $state_id && isset($stateMap)) {
                    $country_id = $stateMap[$state_id][$getgrand_depdent_dd['targetfield']] ?? null;
                }
            }

            if($tbls->targettable == 'currency' ){
                $tables[] = [
                    'id'        => $Module[$tbls->targetfield],   
                    'name'      => $Module[$tbls->dispfield],     
                    'exchange_rate'=> $Module['exchange_rate'],
                    'parent_id' => $state_id, 
                    'grand_parent_id' => $country_id,
                ];
            }
            else{
                $tables[] = [
                    'id'        => $Module[$tbls->targetfield],   
                    'name'      => $Module[$tbls->dispfield],  
                    'exchange_rate'=> null,   
                    'parent_id' => $state_id, 
                    'grand_parent_id' => $country_id,
                ];
            }
        }
        // echo "<pre>";print_r($tables);die;
        if (!empty($tables)) {
            return [
                'status' => 'success',
                'data'   => $tables,
                'parentdata' => $parent_rows,
                'grandparentdata' => $grand_parent_rows,                
                'is_multiple'=>$is_multiple,
                'pagination' => [
                    'total' => $totalCount,
                    'pageSize' => $pageSize,
                    'currentPage' => (int)$page,
                    'totalPages' => ceil($totalCount / $pageSize)
                ]
            ];
        } else {
            return [
                'status'  => 'error',
                'message' => 'No Picklist Found',
            ];
        }
    }



    public function actionPicklistdata()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        // $query = Exportrequest::find();
        $query = Picklist::find()
            ->asArray()
            ->all();

        // echo "<pre>";print_r($query);die;
        $data = [];
        foreach ($query as $exportdata) {

            $data[] = [
                'id' => $exportdata['id'],
                'targettable' => $exportdata['targettable'],
                'targetfield' => $exportdata['targetfield'],
                'dispfield' => $exportdata['dispfield'],
                'action' => '<a href="' . \Yii::$app->urlManager->createUrl(['picklist/delete', 'id' => $exportdata->id]) . '" class="">Delete</a>',
                // 'action' => '<a href="' . \Yii::$app->urlManager->createUrl(['exportrequest/update', 'export_request_id' => $exportdata['export_request_id']]) . '" class="btn btn-primary btn-sm">Edit</a>' 
            ];
        }

        return [
            'data' => $data,
        ];
    }

    public function actionSavetablevalue()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        // echo "<pre>";print_r($_POST);die;
        try {
            $csvUpload = \Yii::$app->request->post('csv_upload', false);

            if ($csvUpload) {
                return $this->processBulkCsvUpload();
            }
            $fieldid = \Yii::$app->request->post('editFieldId');
            $recordid = \Yii::$app->request->post('editRecordId');
            $newValue = \Yii::$app->request->post('picklistEditValue');
            $picklistmode = \Yii::$app->request->post('picklistmode');
            $parentValue = \Yii::$app->request->post('picklistParentValue');
            $grandparentValue = \Yii::$app->request->post('picklistGrandParentValue');
            $exchange_rate = \Yii::$app->request->post('exchangeRate');
            $picklistrecord = Picklist::find()->where(['fieldid' => $fieldid])->one();
            //track audit log
            $audModel = new PicklistModtrackerBasic();
            // echo "<pre>"; print_R($picklistrecord);die;
            $getrecord = '';
            //for edit 

            if ($parentValue != '') {
                $depdent_dd = (new \yii\db\Query())
                    ->select(['*'])
                    ->from('picklist_dependency')
                    ->where(['child_table' => $picklistrecord->targettable])
                    ->one();
            }
            if ($grandparentValue != '' && $grandparentValue != 0) {
                $grand_depdent_dd = (new \yii\db\Query())
                    ->select(['*'])
                    ->from('picklist_dependency')
                    ->where(['child_table' => $depdent_dd['targettable']])
                    ->one();
            }
            if ($picklistmode === "edit") {
                // echo "dasd";die;
                $insertData = [
                    $picklistrecord->dispfield => $newValue,
                ];
                if (isset($depdent_dd) && isset($depdent_dd['child_dependent_field'])) {
                    // echo "<pre>";print_r($parentValue);die;
                    if (is_array($parentValue)) {
                        if ($parentValue != '') {
                            // use parent_field from dependency table
                            if(count($parentValue) > 1 )
                                $insertData[$depdent_dd['child_dependent_field']] = implode(",",$parentValue);
                            else
                                $insertData[$depdent_dd['child_dependent_field']] = reset($parentValue);
                        }
                    }
                    else
                        $insertData[$depdent_dd['child_dependent_field']] = $parentValue;
                }
                // echo "<pre>";print_r($insertData);die;
                // echo $depdent_dd['child_dependent_field'];echo $grandparentValue;die;

                if ($grandparentValue != '') {
                    $existing = (new \yii\db\Query())
                        ->select([$grand_depdent_dd['child_dependent_field']])
                        ->from($grand_depdent_dd['child_table'])
                        ->where([$depdent_dd['targetfield'] => $parentValue])
                        ->one();
                    // echo "<pre>";print_r($existing);die;
                    if ($existing[$grand_depdent_dd['child_dependent_field']] != $grandparentValue) {
                        if ($grand_depdent_dd && isset($grand_depdent_dd['child_dependent_field'])) {
                            // use parent_field from dependency table
                            $grand_record_oldAttributes = (new \yii\db\Query())
                                            ->select('*')
                                            ->from($grand_depdent_dd['child_table'])
                                            ->where([$depdent_dd['targetfield'] => $parentValue])
                                            ->one();
                            $grand_insert_data = [$grand_depdent_dd['child_dependent_field'] =>  $grandparentValue];
                            $updateparent = \Yii::$app->db->createCommand()
                                ->update(
                                    $grand_depdent_dd['child_table'],
                                    [$grand_depdent_dd['child_dependent_field'] =>  $grandparentValue],
                                    [
                                        $depdent_dd['targetfield'] => $parentValue // condition
                                    ]
                                )
                                ->execute();
                                $audModel->picklistauditlog($grand_record_oldAttributes,$grand_insert_data,$grand_depdent_dd['child_table'],2,\Yii::$app->user->id,$parentValue);
                
                            if ($updateparent < 0) {
                                return ['status' => 'fail', 'message' => 'cannot updated parent']; //
                            }
                        }
                    }
                }
                // echo "<pre>";print_r($insertData);die;
                $oldAttributes = (new \yii\db\Query())
                    ->select('*')
                    ->from($picklistrecord->targettable)
                    ->where([$picklistrecord->targetfield => $recordid])
                    ->one();
                    // echo "<pre>";print_r($oldAttributes);die;
                    $exists = '';
                    if($parentValue){
                        $parentString = is_array($parentValue) ? implode(",", $parentValue) : $parentValue;
                        $exists = (new \yii\db\Query())
                            ->from($picklistrecord->targettable)
                            ->where([
                                $picklistrecord->dispfield => $insertData[$picklistrecord->dispfield],
                                $depdent_dd['child_dependent_field'] => $parentString
                            ])
                            ->exists();
                            // echo "<pre>";print_r($parentString);die;
                    }
                    else if($picklistrecord->targettable == 'currency')
                    {
                        $insertData['exchange_rate'] = $exchange_rate;
                        $exists = (new \yii\db\Query())
                        ->from($picklistrecord->targettable)
                        ->where([$picklistrecord->dispfield => $insertData[$picklistrecord->dispfield]])
                        ->andwhere(['exchange_rate'=>$exchange_rate])
                        ->exists();
                    }
                    else{
                        $exists = (new \yii\db\Query())
                        ->from($picklistrecord->targettable)
                        ->where([$picklistrecord->dispfield => $insertData[$picklistrecord->dispfield]])
                        ->exists();
                    }
                        // echo "<pre>";print_r($insertData);die;
                        if (!$exists) {
                        $getrecord = \Yii::$app->db->createCommand()
                                ->update(
                                    $picklistrecord->targettable,
                                    $insertData,
                                    [
                                        $picklistrecord->targetfield => $recordid // condition
                                    ]
                                )
                                ->execute();
                                $audModel->picklistauditlog($oldAttributes,$insertData,$picklistrecord->targettable,2,\Yii::$app->user->id,$recordid);
                            // die;
                            if ($getrecord !== false) {
                                return ['status' => 'success', 'message' => 'Record updated successfully'];
                            } else {
                                return ['status' => 'fail', 'message' => 'No record updated'];
                            }
                        } else {                    
                                return ['status' => 'success', 'message' => 'Value Already Exists.'];
                        }  
                    // echo "<pre>";print_r($exists);die;
                
            } else if ($picklistmode == "add") {
                $maxSeq = (new Query())
                    ->from($picklistrecord->targettable)
                    ->max('seq_no');
                $nextSeq = $maxSeq ? $maxSeq + 1 : 1;
                $insertData = [
                    $picklistrecord->dispfield => $newValue,   // column => value
                    'is_active' => 1,
                    'seq_no'    => $nextSeq,
                ];
                if ($parentValue != '') {
                    // if ($depdent_dd && isset($depdent_dd['child_dependent_field'])) {
                    //     // use parent_field from dependency table
                    //     $insertData[$depdent_dd['child_dependent_field']] = $parentValue;
                    // }
                    if (isset($depdent_dd) && isset($depdent_dd['child_dependent_field'])) {
                    // echo "<pre>";print_r($parentValue);die;
                    if (is_array($parentValue)) {
                        if ($parentValue != '') {
                            // use parent_field from dependency table
                            if(count($parentValue) > 1 )
                                $insertData[$depdent_dd['child_dependent_field']] = implode(",",$parentValue);
                            else
                                $insertData[$depdent_dd['child_dependent_field']] = reset($parentValue);
                        }
                    }
                    else
                        $insertData[$depdent_dd['child_dependent_field']] = $parentValue;
                    }
                }
                // check value is already exists
                // check value is already exists (ignore is_active, seq_no)
                $checkArray = $insertData;
                unset($checkArray['is_active'], $checkArray['seq_no']);
                $exists = (new \yii\db\Query())
                ->from($picklistrecord->targettable)
                ->where($checkArray)   // all column=>value pairs must match
                ->exists();
                // echo $exists;die;
                if (!$exists) {
                     $getrecord = \Yii::$app->db->createCommand()
                        ->insert($picklistrecord->targettable, $insertData)
                        ->execute();
                        $newPk = \Yii::$app->db->getLastInsertID();
                        $audModel->picklistauditlog($oldAttributes = '', $insertData, $picklistrecord->targettable, 0, \Yii::$app->user->id,$newPk);
                        
                        if ($getrecord !== false) {
                            return ['status' => 'success', 'message' => 'Record added successfully.'];
                        } else {
                            return ['status' => 'fail', 'message' => 'No record updated'];
                        }
                } else {                    
                        return ['status' => 'success', 'message' => 'Value Already Exists.'];
                }                
            } else if ($picklistmode == "delete") {
                 $oldAttributes = (new \yii\db\Query())
                    ->select('*')
                    ->from($picklistrecord->targettable)
                    ->where([$picklistrecord->targetfield => $recordid])
                    ->one();
                    $delete_data = ['is_active' => 0];
                $getrecord = \Yii::$app->db->createCommand()
                    ->update(
                        $picklistrecord->targettable, // table name
                        $delete_data,
                        [
                            $picklistrecord->targetfield => $recordid // condition
                        ]
                    )
                    ->execute();
                    $audModel->picklistauditlog($oldAttributes,$delete_data,$picklistrecord->targettable,3,\Yii::$app->user->id,$recordid);
                // die;
                if ($getrecord !==  false) {
                    return ['status' => 'success', 'message' => 'Record deleted successfully.'];
                } else {
                    return ['status' => 'fail', 'message' => 'No record updated'];
                }
            }
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Process bulk CSV upload
     */
    private function processBulkCsvUpload()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
            $file = \yii\web\UploadedFile::getInstanceByName('csv_file');
            if (!$file || $file->extension !== 'csv') {
                return ['status' => 'fail', 'message' => 'Please upload a valid CSV file.'];
            }

            $fieldid = \Yii::$app->request->post('editFieldId') ?: \Yii::$app->request->post('table_id');
            $colName = \Yii::$app->request->post('colName') ?: \Yii::$app->request->post('table_id');
            if (!$colName) {
                return ['status' => 'fail', 'message' => 'Invalid Column name.'];
            }
            $recordid = \Yii::$app->request->post('editRecordId');
            $parentValue = \Yii::$app->request->post('picklistParentValue', '');
            $exchange_rate = \Yii::$app->request->post('exchangeRate', '');

            $picklistrecord = Picklist::find()->where(['fieldid' => $fieldid])->one();
            if (!$picklistrecord) {
                return ['status' => 'fail', 'message' => 'Invalid fieldId provided.'];
            }

            $filePath = \Yii::getAlias('@runtime') . '/' . uniqid() . '.' . $file->extension;
            $file->saveAs($filePath);

            $handle = fopen($filePath, 'r');
            if (!$handle) {
                unlink($filePath);
                return ['status' => 'fail', 'message' => 'Cannot open uploaded file.'];
            }

            $results = [];
            $header = fgetcsv($handle, 1000, ',');
            $header = array_map(function ($h) {
                return strtolower(trim(preg_replace('/^\xEF\xBB\xBF/', '', $h)));
            }, $header);
            $possibleFields = [
                strtolower($picklistrecord->dispfield),
                'picklist',
                'value',
                'picklist ' . strtolower($picklistrecord->dispfield),
                'picklist ' . strtolower($colName),
            ];
            $mainFieldIdx = false;
            foreach ($possibleFields as $fname) {
                $idx = array_search($fname, $header);
                if ($idx !== false) {
                    $mainFieldIdx = $idx;
                    break;
                }
            }
            if ($mainFieldIdx === false) {
                fclose($handle);
                unlink($filePath);
                return ['status' => 'fail', 'message' => "CSV must have a column matching '{$picklistrecord->dispfield}', 'Picklist', or 'Value' (case-insensitive)."];
            }

            $maxSeq = (new \yii\db\Query())
                ->from($picklistrecord->targettable)
                ->max('seq_no');
            $seq = $maxSeq ? $maxSeq : 0;
            $audModel = new PicklistModtrackerBasic();
            $depdent_dd = null;
            if ($parentValue !== '') {
                $depdent_dd = (new \yii\db\Query())
                    ->select(['*'])
                    ->from('picklist_dependency')
                    ->where(['child_table' => $picklistrecord->targettable])
                    ->one();
            }

            $parentValues = array_filter(array_map('trim', explode(',', $parentValue)));
            $insertedRows = 0;
            $skippedRows = 0;
            $lineNum = 1;
            while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
                $lineNum++;
                // if ($lineNum == 2) continue; // skip header

                $newValue = trim($row[$mainFieldIdx]);
                if (empty($newValue)) {
                    $skippedRows++;
                    $results[] = ['status' => 'skipped', 'message' => "Row $lineNum: Value is empty, skipped."];
                    continue;
                }
                // Special character validation
                if (!preg_match('/^[a-zA-Z0-9 _\-,.\/]+$/u', $newValue)) {
                    $skippedRows++;
                    $results[] = [
                        'status' => 'skipped','message' => "Row $lineNum: Value contains invalid special characters, skipped."
                    ];
                    continue;
                }

                if (!empty($parentValues) && $depdent_dd && isset($depdent_dd['child_dependent_field'])) {
                    $insertData = [
                        $picklistrecord->dispfield => $newValue,
                        'is_active' => 1,
                        $depdent_dd['child_dependent_field'] => implode(',', $parentValues),
                    ];
                    if ($picklistrecord->targettable == 'currency' && $exchange_rate !== '') {
                        $insertData['exchange_rate'] = $exchange_rate;
                    }
                    $checkArray = $insertData;
                    unset($checkArray['is_active']);
                    $exists = (new \yii\db\Query())
                        ->from($picklistrecord->targettable)
                        ->where($checkArray)
                        ->exists();

                    if (!$exists) {
                         $insertData['seq_no'] = ++$seq;
                        \Yii::$app->db->createCommand()
                            ->insert($picklistrecord->targettable, $insertData)
                            ->execute();
                        $newPk = \Yii::$app->db->getLastInsertID();
                        $audModel->picklistauditlog('', $insertData, $picklistrecord->targettable, 0, \Yii::$app->user->id,$newPk);
                        $results[] = [
                            'status' => 'success',
                            'message' => "Row $lineNum, parent=" . implode(',', $parentValues) . ": Added."
                        ];
                        $insertedRows++; 
                    } else {
                        $results[] = [
                            'status' => 'info',
                            'message' => "Row $lineNum, parent=" . implode(',', $parentValues) . ": Value already exists."
                        ];
                        $skippedRows++;
                    }
                } else {
                    $insertData = [
                        $picklistrecord->dispfield => $newValue,
                        'is_active' => 1,
                    ];
                    if ($picklistrecord->targettable == 'currency' && $exchange_rate !== '') {
                        $insertData['exchange_rate'] = $exchange_rate;
                    }
                    $checkArray = $insertData;
                    unset($checkArray['is_active']);
                    $exists = (new \yii\db\Query())
                        ->from($picklistrecord->targettable)
                        ->where($checkArray)
                        ->exists();

                    if (!$exists) {
                        $insertData['seq_no'] = ++$seq;
                        \Yii::$app->db->createCommand()
                            ->insert($picklistrecord->targettable, $insertData)
                            ->execute();
                        $newPk = \Yii::$app->db->getLastInsertID();
                        $audModel->picklistauditlog('', $insertData, $picklistrecord->targettable, 0, \Yii::$app->user->id,$newPk);
                        $results[] =['status' => 'success', 'message' => "Row $lineNum: Added."];
                        $insertedRows++;
                    } else {
                        $results[] = ['status' => 'info', 'message' => "Row $lineNum: Value already exists."];
                        $skippedRows++;
                    }
                }
            } 
            fclose($handle);
            unlink($filePath);
            $msg = "Bulk upload completed. Inserted: $insertedRows, Skipped/Existed: $skippedRows";
            return ['status' => 'completed', 'message' => $msg, 'results' => $results];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /*** 
     * Download sample CSV file
     */
    public function actionDownloadSampleCsv()
    {
        $colName = \Yii::$app->request->get('colName');

        if (!$colName) {
            throw new \yii\web\NotFoundHttpException("Invalid colName.");
        }

        $headerName = 'Picklist ' . $colName;
        $filePath = \Yii::getAlias('@webroot/thememain/samples/pickuplist_sample.csv');
        $fileName = 'pickuplist_sample.csv';

        if (!file_exists($filePath)) {
            throw new \yii\web\NotFoundHttpException("Sample file not found.");
        }

        $csvContent = fopen('php://temp', 'r+');
        fputcsv($csvContent, [$headerName]);
        $input = fopen($filePath, 'r');
        while (($row = fgetcsv($input)) !== false) {
            fputcsv($csvContent, $row);
        }
        fclose($input);
        rewind($csvContent);
        $csvString = stream_get_contents($csvContent);
        fclose($csvContent);

        $csvString = preg_replace('/^\xEF\xBB\xBF/', '', $csvString);

        return \Yii::$app->response->sendContentAsFile(
            $csvString,
            $fileName,
            [
                'mimeType' => 'text/csv',
                'inline' => false,
            ]
        );
    }


public function actionExportpicklistvalues()
{
    $fieldid = \Yii::$app->request->get('fieldid');
    $parentValue = \Yii::$app->request->get('parentValue', null);
    $fieldname = \Yii::$app->request->get('fieldname');
    $modulename = \Yii::$app->request->get('modulename');
    if (empty($fieldid)) {
        throw new \yii\web\BadRequestHttpException('Field (table) selection is required.');
    }

    $picklistConfig = Picklist::find()->where(['fieldid' => $fieldid])->one();
    if (!$picklistConfig) {
        throw new \yii\web\NotFoundHttpException('Picklist config not found.');
    }
    $query = (new \yii\db\Query());
    $query->from($picklistConfig->targettable)
        ->where(['is_active' => 1]);

    $dependency = null;
    if ($parentValue) {
        $dependency = (new \yii\db\Query())
            ->from('picklist_dependency')
            ->where(['child_table' => $picklistConfig->targettable])
            ->one();
        if ($dependency && isset($dependency['child_dependent_field'])) {
            $query->andWhere([$dependency['child_dependent_field'] => $parentValue]);
        }
    }

    $picklistValues = $query->all();

    $hasMakeId = isset($picklistValues[0]['make_id']);
    $header = [];
    $output = [];
    if ($hasMakeId) {
        // Use $fieldname and $modulename as the CSV column headers and as keys in each output row
        $header = [$fieldname, 'Parent'];
        $allMakerIds = [];
        foreach ($picklistValues as $row) {
            if (!empty($row['make_id'])) {
                $ids = array_map('trim', explode(',', $row['make_id']));
                $allMakerIds = array_merge($allMakerIds, $ids);
            }
        }
        $allMakerIds = array_unique(array_filter($allMakerIds));

        $makerMap = [];
        if (!empty($allMakerIds)) {
            $makers = (new \yii\db\Query())
                ->from('prod_make')
                ->where(['prod_make_id' => $allMakerIds])
                ->all();
            foreach ($makers as $maker) {
                $makerMap[$maker['prod_make_id']] = $maker['prod_make_value'];
            }
        }

        foreach ($picklistValues as $row) {
            $makerNames = [];
            if (!empty($row['make_id'])) {
                $ids = array_map('trim', explode(',', $row['make_id']));
                foreach ($ids as $mid) {
                    if (isset($makerMap[$mid])) {
                        $makerNames[] = $makerMap[$mid];
                    }
                }
            }
            // Use header keys for output row
            $output[] = [
                $fieldname  => $row['prod_model_value'],
                'Parent' => implode(', ', $makerNames),
            ];
        }
    } else {
        $header = [$fieldname];
        $dispfield = $picklistConfig['dispfield'];
        foreach ($picklistValues as $row) {
            $output[] = [
                $fieldname => $row[$dispfield],
            ];
        }
    }

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename=picklist_values.csv');
    $fp = fopen('php://output', 'w');

    fputcsv($fp, $header); // Write custom header

    foreach ($output as $row) {
        $rowData = [];
        foreach ($header as $colName) {
            $rowData[] = isset($row[$colName]) ? $row[$colName] : '';
        }
        fputcsv($fp, $rowData);
    }
    fclose($fp);
    exit;
}

public function actionBulkdeletepicklist()
{
    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    $fieldId = \Yii::$app->request->post('fieldId');
    $ids     = \Yii::$app->request->post('ids', []);

    if (empty($fieldId) || empty($ids) || !is_array($ids)) {
        return ['status' => 'fail', 'message' => 'Invalid request data.'];
    }

    $picklistRecord = Picklist::find()->where(['fieldid' => $fieldId])->one();
    if (!$picklistRecord) {
        return ['status' => 'fail', 'message' => 'Invalid fieldId provided.'];
    }

    $tableName   = $picklistRecord->targettable;
    $targetField = $picklistRecord->targetfield;

    $ids = array_filter(array_map('intval', $ids));
    if (!$ids) {
        return ['status' => 'fail', 'message' => 'No valid ids found.'];
    }

    $deletedata = ['isactive' => 0];

    $oldAttributes = (new \yii\db\Query())
        ->from($tableName)
        ->where([$targetField => $ids])
        ->all();

    $affected = \Yii::$app->db->createCommand()
        ->update($tableName, $deletedata, [$targetField => $ids])
        ->execute();

    if (!empty($oldAttributes)) {
        $audModel = new PicklistModtrackerBasic();
        foreach ($oldAttributes as $row) {
            $audModel->picklistauditlog(
                $row,
                $deletedata,
                $tableName,
                3,
                \Yii::$app->user->id,
                $row[$targetField]
            );
        }
    }

    if ($affected > 0) {
        return ['status' => 'success', 'message' => 'All picklist records deleted successfully.'];
    }

    return ['status' => 'fail', 'message' => 'No record updated.'];
}




}
