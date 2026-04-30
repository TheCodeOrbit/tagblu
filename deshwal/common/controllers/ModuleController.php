<?php

namespace common\controllers;

use backend\modules\documents\Documents;
// use yii\web\Controller;
use common\components\Controller;
use Yii;
use app\models\Attachments;
use app\models\CallInformation;
use app\models\CallType;
use app\models\OutgoingCallStatus;
use app\models\DefaultFilter;
use app\models\EditModel;
use app\models\ListModel;
use app\models\ApproveListModel;
use app\models\Blocks;
use backend\models\AccessCheck;
use common\models\Tab;
use common\models\Field;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\db\Expression;

use yii\web\UploadedFile;
use yii\web\Response;

use backend\assets\AdminAsset;
use backend\models\KanbanCard;

use backend\models\TableList;
use backend\models\Task;
use app\models\Reference;
use app\models\Leaddetails;
use app\models\Leadinformation;
use app\models\LeadStatus;
use app\models\MeetingInformation;
use app\models\Modnotes;
use app\models\Documents as Documentmodel;
use app\models\ModtrackerBasic;
use app\models\TaskInformation;
use app\models\UserFilter;
use app\models\UsersDetails;
use app\models\CheckAllowActions;

use common\components\Request;
use common\models\User;
use Psy\CodeCleaner\FunctionReturnInWriteContextPass;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use app\models\AutoNo; // Include the AutoNo class
use app\models\CvcolumnModtrackerBasic;
use app\models\Detaileditsetting;
use app\models\Field as ModelsField;
use app\models\GeneratePi;
use app\models\Inventory;
use app\models\InventoryLogDetails;
use app\models\ListHire;
use app\models\Notifications;
use app\models\Pageination;
use app\models\SearchModel;
use app\models\SegregationDetail;
use app\models\Tab as ModelsTab;
use app\models\User as ModelsUser;
use common\models\Multilist;
use common\models\Picklist;
use yii\data\Pagination;
use yii\helpers\Html;
use yii\web\MultiFieldSession;
use app\models\LeadContactsDetail; //added LeadContactsDetail model
use app\models\RaiserequestClient;
use app\models\RaiserequestVendor;
use app\models\Role;
use app\models\VendorAccount;
use DateTime;
use Normalizer;
use PHPMailer\PHPMailer\PHPMailer;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;

class ModuleController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['error'],
                        'allow' => true,
                    ],
                    [

                        // 'actions' => ['logout', 'index', 'create', 'list', 'popuplist', 'popupsearch', 'quickcreatepopup', 'getcolumnfields', 'saveselectedcolumns', 'gettabledata', 'filterbylead', 'test', 'edit', 'detail', 'approvelead', 'postnotes', 'getnotes', 'deleteselectedrow', 'bulkupdate', 'bulkupdateview', 'addcall', 'addmeeting', 'searchusers', 'addtask', 'adddoc', 'download', "detailhistory", 'updatestage', 'exportdata','savefilterbylead','getfilterdetails'],

                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        // Specify the action you want to exclude from CSRF validation
        // $actionsWithCsrf = ['create', 'edit']; // Only these need CSRF

        // if (!in_array($action->id, $actionsWithCsrf)) {
        //     $this->enableCsrfValidation = false;
        // }
        // Clean POST
        if (!empty($_POST)) {
            $_POST = $this->cleanInput($_POST);
        }

        // Clean GET
        if (!empty($_GET)) {
            $_GET = $this->cleanInput($_GET);
        }
        return parent::beforeAction($action);
    }


    public function actionDashboard()
    {
        // print_r($_POST);die;
        $sourceid = Yii::$app->request->get('sourceid');
        $sourcemodule = Yii::$app->request->get('sourcemodule');
        // Get pagination parameters from the request
        $start = Yii::$app->request->get('start', 0); // Start index, default to 0
        $limit = Yii::$app->request->get('limit', 10); // Limit (number of records), default to 10

        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;
        $layout = $this->layout;


        $model = new AccessCheck();
        $id = Yii::$app->user->id;
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $modulepermission = $model->modulepermission($profile, $tabs);
        // echo $ModuleName;die;
        $createpermission = $model->checkpermission($id, $ModuleName, 'create');
        $editpermission = $model->checkpermission($id, $ModuleName, 'edit');
        $deletepermission = $model->checkpermission($id, $ModuleName, 'delete');
        $listpermission = $model->checkpermission($id, $ModuleName, 'list');
        $detailpermission = $model->checkpermission($id, $ModuleName, 'detail');
        $approvepermission = $model->checkpermission($id, $ModuleName, 'approvelist');
        $importpermission = $model->checkpermission($id, $ModuleName, 'import');
        $exportpermission = $model->checkpermission($id, $ModuleName, 'export');

        $arrRender = array();

        $model = new ListModel($TableName, $FieldId, $ModuleName);
        $filed_name = $model->getfilterColumnList();
        $connection = Yii::$app->db;
        $command = $connection->createCommand("
        SELECT fieldlabel,columnname, tablename, mandatory 
        FROM `field` 
        WHERE import = :import AND tabid = :tabid
    ");
        $command->bindValue(':import', 1);
        $command->bindValue(':tabid', $TabId);

        // Execute query to fetch columns
        $columnsIMP = $command->queryAll();
        $srcheaderfullname = '';
        $sourcemodule = Yii::$app->request->get('sourcemodule');
        $sourceid = Yii::$app->request->get('sourceid');
        if (!empty($sourceid) && !empty($sourcemodule)) {
            $modeledit = new EditModel($TableName, $FieldId, $ModuleName, 'edit');
            // get header column
            $header = $modeledit->getHeaderDetail($sourcemodule);
            $srcheaderfullname = '';
            if (!empty($header) && isset($header['columns'])) {
                //get related tblename
                $arrtab = Yii::$app->db
                    ->createCommand("SELECT tablename FROM field where tabid=$sourcemodule limit 1")
                    // ->bindValue(":TableName", $TableName)
                    ->queryOne();
                $relatedTableName = $arrtab['tablename'];


                // // Get the table schema
                $tableSchema = Yii::$app->db->schema->getTableSchema($relatedTableName);

                // // Check if the table exists and has a primary key
                if ($tableSchema !== null && !empty($tableSchema->primaryKey)) {
                    // echo "Primary key column(s) for table '$relatedTableName': " . implode(', ', $tableSchema->primaryKey);
                    $retFieldId = implode(', ', $tableSchema->primaryKey);
                    if ($retFieldId) {
                        $arr_tab = Yii::$app->db
                            ->createCommand("SELECT CONCAT_WS(' ', " . $header['columns'] . ") AS full_name FROM $relatedTableName where $retFieldId=$sourceid")
                            // ->bindValue(":TableName", $TableName)
                            ->queryOne();
                        // print_r($arr_tab);die;

                        $srcheaderfullname = $arr_tab['full_name'];
                    }
                }
            }
        }
        $arrRender['ModuleName'] = $ModuleName;
        $arrRender['Tabname'] = ucfirst($ModuleName);
        $arrRender['TabLabel'] = $TabLabel;
        $arrRender['TabId'] = $TabId;
        $arrRender['DataImport'] = $columnsIMP;

        $arrRender['filed_name'] = $filed_name;
        $arrRender['layout'] = $layout;
        $arrRender['srcheaderfullname'] = $srcheaderfullname;
        $arrRender['TableName'] = $TableName;
        $arrRender['createpermission'] = $createpermission;
        $arrRender['editpermission'] = $editpermission;
        $arrRender['deletepermission'] = $deletepermission;
        $arrRender['detailpermission'] = $detailpermission;
        $arrRender['listpermission'] = $listpermission;
        $arrRender['approvepermission'] = $approvepermission;
        $arrRender['importpermission'] = $importpermission;
        $arrRender['exportpermission'] = $exportpermission;

        $this->layout = '@app/views/layouts/main-one';
        $this->render('@app/views/tetra/dashboardview', $arrRender);
    }

    public function actionApprovelead()
    {
        // print_r($_POST);die;
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;


        $Record = $_POST['Recordid'];
        $id = Yii::$app->user->id;

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $actionid = "edit";
        $auditmodel = new EditModel($TableName, $FieldId, $ModuleName, $actionid);

        // $modellead = new Leadinformation();
        //  $modellead->leadstatus = $_POST['leadstatus_v'];
        // $modellead->modifiedtime = date("Y-m-d H:i:s");
        // $modellead->modifiedby = $id;
        // $modellead->save();


        $newAttribute = $_POST;

        if ($auditmodel->approvelead($Record)) {
            return $this->asJson([
                'status' => 'success',
                'message' => 'Approved successfully.',
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'Failed to update record.',
                'errors' => $auditmodel->getErrors(),
            ]);
        }


        $this->layout = '@app/views/layouts/main-one';
    }


    public function actionApprovepo()
    {
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;

        $Record = $_POST['Recordid'];
        $id = Yii::$app->user->id;

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $actionid = "edit";
        $auditmodel = new EditModel($TableName, $FieldId, $ModuleName, $actionid);

        $newAttribute = $_POST;

        // if ($auditmodel->approvePurchaseOrder($Record)) {
        if ($auditmodel->approveCommon($Record)) {
            return $this->asJson([
                'status' => 'success',
                'message' => 'Updated successfully.',
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'Failed to update record.',
                'errors' => $auditmodel->getErrors(),
            ]);
        }


        $this->layout = '@app/views/layouts/main-one';
    }

    public function actionApprovepickup()
    {
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;

        $Record = $_POST['Recordid'];
        $id = Yii::$app->user->id;

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $actionid = "edit";
        $auditmodel = new EditModel($TableName, $FieldId, $ModuleName, $actionid);

        $newAttribute = $_POST;
        $status = $auditmodel->approvePickup($Record);
        if ($status === true) {
            return $this->asJson([
                'status' => 'success',
                'message' => 'Updated successfully.',
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'Failed to update record.',
                'errors' => $status,
            ]);
        }


        $this->layout = '@app/views/layouts/main-one';
    }

    public function actionApprovesourcingdeal()
    {
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;

        $Record = $_POST['Recordid'];
        $id = Yii::$app->user->id;

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $actionid = "edit";
        $auditmodel = new EditModel($TableName, $FieldId, $ModuleName, $actionid);

        $newAttribute = $_POST;

        // if ($auditmodel->approveSourcingDeal($Record)) {
        if ($auditmodel->approveCommon($Record)) {
            return $this->asJson([
                'status' => 'success',
                'message' => 'Updated successfully.',
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'Failed to update record.',
                'errors' => $auditmodel->getErrors(),
            ]);
        }


        $this->layout = '@app/views/layouts/main-one';
    }
    public function actionApprovepayments()
    {
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;

        $Record = $_POST['Recordid'];
        $id = Yii::$app->user->id;

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $actionid = "edit";
        $auditmodel = new EditModel($TableName, $FieldId, $ModuleName, $actionid);

        $newAttribute = $_POST;

        if ($auditmodel->approvePayments($Record)) {
            return $this->asJson([
                'status' => 'success',
                'message' => 'Updated successfully.',
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'Failed to update record.',
                'errors' => $auditmodel->getErrors(),
            ]);
        }


        $this->layout = '@app/views/layouts/main-one';
    }
    public function actionApprovequotesdit()
    {
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;

        $Record = $_POST['Recordid'];
        $id = Yii::$app->user->id;

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $actionid = "edit";
        $auditmodel = new EditModel($TableName, $FieldId, $ModuleName, $actionid);

        $newAttribute = $_POST;

        // if ($auditmodel->approvequotesdit($Record)) {
        if ($auditmodel->approveCommon($Record)) {
            return $this->asJson([
                'status' => 'success',
                'message' => 'Updated successfully.',
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'Failed to update record.',
                'errors' => $auditmodel->getErrors(),
            ]);
        }


        $this->layout = '@app/views/layouts/main-one';
    }

    public function actionApprovesalesorderdit()
    {
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;

        $Record = $_POST['Recordid'];
        $id = Yii::$app->user->id;

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $actionid = "edit";
        $auditmodel = new EditModel($TableName, $FieldId, $ModuleName, $actionid);

        $newAttribute = $_POST;

        if ($auditmodel->approveSalesorderdit($Record)) {
            return $this->asJson([
                'status' => 'success',
                'message' => 'Updated successfully.',
            ]);
        } 
        //  if ($auditmodel->approveCommon($Record)) {
        //     return $this->asJson([
        //         'status' => 'success',
        //         'message' => 'Updated successfully.',
        //     ]);
        // } 
        else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'Failed to update record.',
                'errors' => $auditmodel->getErrors(),
            ]);
        }


        $this->layout = '@app/views/layouts/main-one';
    }
    public function actionApprovepurchaseorderdit()
    {
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;

        $Record = $_POST['Recordid'];
        $id = Yii::$app->user->id;

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $actionid = "edit";
        $auditmodel = new EditModel($TableName, $FieldId, $ModuleName, $actionid);

        $newAttribute = $_POST;

        if ($auditmodel->approvePurchaseorderdit($Record)) {
            return $this->asJson([
                'status' => 'success',
                'message' => 'Updated successfully.',
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'Failed to update record.',
                'errors' => $auditmodel->getErrors(),
            ]);
        }


        $this->layout = '@app/views/layouts/main-one';
    }

    public function actionApproveopportunity()
    {
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;

        $Record = $_POST['Recordid'];
        $id = Yii::$app->user->id;

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $actionid = "edit";
        $auditmodel = new EditModel($TableName, $FieldId, $ModuleName, $actionid);

        $newAttribute = $_POST;

        if ($auditmodel->approveCommon($Record)) {
            return $this->asJson([
                'status' => 'success',
                'message' => 'Updated successfully.',
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'Failed to update record.',
                'errors' => $auditmodel->getErrors(),
            ]);
        }


        $this->layout = '@app/views/layouts/main-one';
    }


    public function actionPopuplistselect()
    {
        // $this->enableCsrfValidation = false;//disable csrf
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;


        $id = Yii::$app->user->id;
        $uid = Yii::$app->params['dirName'];

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $modulepermission = $model->modulepermission($profile, $tabs);



        //$model=new ListModel($TableName,$FieldId);
        $model = new Reference($TableName, $FieldId);
        $ActionList = $model->getActionList($ModuleName);
        // print_r($ActionList);die;
        $ActionList['OrderBy'] = Yii::$app->request->get('OrderBy');
        $ActionList['SortOrder'] = Yii::$app->request->get('SortOrder');
        $sourcemodule = Yii::$app->request->post('sourcemodule');
        $sourceid = Yii::$app->request->post('sourceid');
        $searchparam = $_POST['searchparam'];
        $searchparam = $_POST['searchparam'];
        $srch = '';
        if (!empty($searchparam)) {
            $srch = array();
            foreach ($searchparam as $key) {
                $srch[$key[0]] = $key[1];
            }
        }
        // print_r($srch);die();


        list($ColumnList, $RecordList, $totalitemcount) = $model->getListRecord_pop($ActionList['OrderBy'], $ActionList['SortOrder'], $rolebasedrecord, $modulepermission, $ModuleName);
        $this->layout = '@app/views/layouts/main-new';
        $arrRender = array();
        $arrRender['RecordList'] = $RecordList;
        $arrRender['ColumnList'] = $ColumnList;
        $arrRender['ActionList'] = $ActionList;
        $arrRender['ModName'] = $ModuleName;
        $arrRender['operation'] = $modelaccess;
        $arrRender['modulepermission'] = $modulepermission;
        $arrRender['totalitemcount'] = $totalitemcount;
        $arrRender['searchparam'] = $srch;
        $arrRender['sourceid'] = $sourceid;
        $arrRender['sourcemodule'] = $sourcemodule;
        $arrRender['TabLabel'] = $TabLabel;
        $this->renderPartial('@app/views/tetra/PopupS', $arrRender);
    }
    public function actionPopuplist()
    {

        // $this->enableCsrfValidation = false;//disable csrf
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;
        // print_r($FieldId);
        // print_r($ModuleName);
        // print_r($TableName);
        // print_r($TabLabel);
        // exit;

        $id = Yii::$app->user->id;
        $uid = Yii::$app->params['dirName'];

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $modulepermission = $model->modulepermission($profile, $tabs);



        //$model=new ListModel($TableName,$FieldId);
        $model = new Reference($TableName, $FieldId);
        $ActionList = $model->getActionList($ModuleName);
        // print_r($ActionList);die;
        $ActionList['OrderBy'] = Yii::$app->request->get('OrderBy');
        $ActionList['SortOrder'] = Yii::$app->request->get('SortOrder');
        $searchparam = $_POST['searchparam'] ?? [];
        $searchparam_child = $_POST['searchparam_child'] ?? [];
        $srch = [];
        if (!empty($searchparam)) {
            foreach ($searchparam as $key) {
                $srch[$key[0]] = $key[1];
            }
        }

        $srch_child = [];
        if (!empty($searchparam_child)) {
            foreach ($searchparam_child as $row) {
                if (!isset($row[0], $row[1])) continue;
                $srch_child[$row[0]] = $row[1]; // "pickup_asset_detail.hsn_code" => "75675678"
            }
        }

        //added on 27 jan 2025 for roled users
        $roled = Yii::$app->request->get('roled');

        // print_r($srch);die();

        ///added on 23 june for sourcingdeal contract by deepika
        //get related actions
        $sourcemodule = Yii::$app->request->post('sourcemodule') ?? null;
        $sourceid = Yii::$app->request->post('sourceid') ?? null;

        //end on 23 junr
        $childSearchConfig = null;
        if(isset($_GET['current_fieldid'])){
            $childSearchConfig = $model->getChildSearchConfig($_GET['current_fieldid']);
        }
        list($ColumnList, $RecordList, $totalitemcount) = $model->getListRecord_pop($ActionList['OrderBy'], $ActionList['SortOrder'], $rolebasedrecord, $modulepermission, $ModuleName, $sourcemodule, $sourceid, $childSearchConfig,$srch_child);
        
        $this->layout = '@app/views/layouts/main-new';
        $this->renderPartial('@app/views/tetra/PopupL', array('RecordList' => $RecordList, 'ColumnList' => $ColumnList, 'ActionList' => $ActionList, 'ModName' => $ModuleName, 'operation' => $modelaccess, 'modulepermission' => $modulepermission, 'totalitemcount' => $totalitemcount,'searchparam' => $srch,'searchparam_child' => $srch_child, 'childSearchConfig' => $childSearchConfig,'TabLabel' => $TabLabel));
    }
    public function actionPopuplistmulti()
    {

        // $this->enableCsrfValidation = false;//disable csrf
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;


        $id = Yii::$app->user->id;
        $uid = Yii::$app->params['dirName'];

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $modulepermission = $model->modulepermission($profile, $tabs);



        //$model=new ListModel($TableName,$FieldId);
        $model = new Reference($TableName, $FieldId);
        $ActionList = $model->getActionList($ModuleName);
        // print_r($ActionList);die;
        $ActionList['OrderBy'] = Yii::$app->request->get('OrderBy');
        $ActionList['SortOrder'] = Yii::$app->request->get('SortOrder');
        $searchparam = $_POST['searchparam'];
        $searchparam = $_POST['searchparam'];
        $srch = '';
        if (!empty($searchparam)) {
            $srch = array();
            foreach ($searchparam as $key) {
                $srch[$key[0]] = $key[1];
            }
        }
        //added on 27 jan 2025 for roled users
        $roled = Yii::$app->request->get('roled');

        // print_r($srch);die();

        ///added on 23 june for sourcingdeal contract by deepika
        //get related actions
        $sourcemodule = Yii::$app->request->post('sourcemodule') ?? null;
        $sourceid = Yii::$app->request->post('sourceid') ?? null;

        //end on 23 junr


        list($ColumnList, $RecordList, $totalitemcount) = $model->getListRecord_pop($ActionList['OrderBy'], $ActionList['SortOrder'], $rolebasedrecord, $modulepermission, $ModuleName, $sourcemodule, $sourceid);
        $this->layout = '@app/views/layouts/main-new';
        $this->renderPartial('@app/views/tetra/MultiPopupL', array('RecordList' => $RecordList, 'ColumnList' => $ColumnList, 'ActionList' => $ActionList, 'ModName' => $ModuleName, 'operation' => $modelaccess, 'modulepermission' => $modulepermission, 'totalitemcount' => $totalitemcount, 'searchparam' => $srch, 'TabLabel' => $TabLabel));
    }

    // quill notes username 
    public function actionGetusernames()
    {

        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;

        $id = Yii::$app->user->id;
        $uid = Yii::$app->params['dirName'];

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $modulepermission = $model->modulepermission($profile, $tabs);

        Yii::$app->response->format = Response::FORMAT_JSON;
        $query = Yii::$app->request->get('q', '');

        $usernames = User::find()
            ->select(["CONCAT(first_name, last_name) AS fullname"])
            ->where(['like', 'first_name', $query . '%', false])
            ->orWhere(['like', 'last_name', $query . '%', false])
            ->limit(10)
            ->column();

        return ['usernames' => $usernames];
    }



    public function actionSavemention()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $fullName = Yii::$app->request->post('user'); // Get the full name from POST request


        if (!$fullName) {
            return ['success' => false, 'message' => 'No user name provided.'];
        }

        // Split full name into first and last name
        //$nameParts = explode(" ", trim($fullName), 2); // Splitting into max 2 parts

        // if (count($nameParts) < 2) {
        //     return ['success' => false, 'message' => "Invalid name format: $fullName"];
        // }

        // $firstName = $nameParts[0];
        // $lastName = $nameParts[1];

        // Find user case-insensitively
        // $user = User::find()
        //     ->where(['like', 'first_name', $firstName, false])
        //     ->andWhere(['like', 'last_name', $lastName, false])
        //     ->one();
        // Perform the search using the concatenated first name and last name
        $user = User::find()
            ->where(['like', 'CONCAT(first_name, last_name)', $fullName, false])
            ->one();

        if ($user) {
            // Save notification
            $notification = new Notifications();
            $notification->userid = $user->id;
            $notification->message = "You were mentioned in a note.";
            $notification->read_status = 0; // Unread notification
            $notification->source_link = Yii::$app->request->baseUrl;
            $notification->createdtime = date('Y-m-d H:i:s');

            if (!$notification->save()) {
                return ['success' => false, 'errors' => $notification->errors];
            }

            return ['success' => true, 'message' => 'Mention saved successfully.'];
        } else {
            return ['success' => false, 'message' => "User not found: $fullName"];
        }
    }


    public function actionPopuplistdependent()
    {
        // $this->enableCsrfValidation = false;//disable csrf
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;


        $id = Yii::$app->user->id;
        $uid = Yii::$app->params['dirName'];

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $modulepermission = $model->modulepermission($profile, $tabs);



        //$model=new ListModel($TableName,$FieldId);
        $model = new Reference($TableName, $FieldId);
        $ActionList = $model->getActionList($ModuleName);
        // print_r($ActionList);die;
        $ActionList['OrderBy'] = Yii::$app->request->get('OrderBy');
        $ActionList['SortOrder'] = Yii::$app->request->get('SortOrder');
        $searchparam = $_POST['searchparam'];
        $searchparam = $_POST['searchparam'];
        $srch = '';
        if (!empty($searchparam)) {
            $srch = array();
            foreach ($searchparam as $key) {
                $srch[$key[0]] = $key[1];
            }
        }
        // print_r($srch);die();


        list($ColumnList, $RecordList, $totalitemcount) = $model->getListRecord_pop($ActionList['OrderBy'], $ActionList['SortOrder'], $rolebasedrecord, $modulepermission, $ModuleName);
        $this->layout = '@app/views/layouts/main-new';
        $this->renderPartial('@app/views/tetra/PopupLDep', array('RecordList' => $RecordList, 'ColumnList' => $ColumnList, 'ActionList' => $ActionList, 'ModName' => $ModuleName, 'operation' => $modelaccess, 'modulepermission' => $modulepermission, 'totalitemcount' => $totalitemcount, 'searchparam' => $srch, 'TabLabel' => $TabLabel));
    }
    public function actionRelatedlist()
    {
        // $this->enableCsrfValidation = false;//disable csrf
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;


        $id = Yii::$app->user->id;
        $uid = Yii::$app->params['dirName'];

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $modulepermission = $model->modulepermission($profile, $tabs);

        $model = new Reference($TableName, $FieldId);
        $ActionList = $model->getActionList($ModuleName);
        // print_r($ActionList);die;
        $ActionList['OrderBy'] = Yii::$app->request->get('OrderBy');
        $ActionList['SortOrder'] = Yii::$app->request->get('SortOrder');



        //get related actions
        $sourcemodule = Yii::$app->request->get('sourcemodule');
        $sourceid = Yii::$app->request->get('sourceid');
        $relatedactions = $model->getRelatedmoduleActiond($sourcemodule, $TabId);
        //if notes get notes
        if ($ModuleName == "notes") {
            $getnotes = $this->getnotesnew($sourcemodule, $sourceid);
            $this->layout = '@app/views/layouts/main-new';
            $this->renderPartial('@app/views/tetra/RelatedNotes', array('ActionList' => $ActionList, 'ModName' => $ModuleName, 'operation' => $modelaccess, 'modulepermission' => $modulepermission, 'getnotes' => $getnotes, 'TabLabel' => $TabLabel, 'sourcemodule' => $sourcemodule, 'sourceid' => $sourceid));
        } else {
            //$model=new ListModel($TableName,$FieldId);
            $searchparam = Yii::$app->request->post('searchparam');
            $divcontainer = Yii::$app->request->post('divcontainer');
            $srch = '';
            if (!empty($searchparam)) {
                $srch = array();
                foreach ($searchparam as $key) {
                    $srch[$key[0]] = $key[1];
                }
            }
            // print_r($searchparam)+0;die;
            list($ColumnList, $RecordList, $totalitemcount) = $model->getListRecord_related($ActionList['OrderBy'], $ActionList['SortOrder'], $rolebasedrecord, $modulepermission, $ModuleName);
            $this->layout = '@app/views/layouts/main-new';
            $this->renderPartial('@app/views/tetra/Relatedlist', array('RecordList' => $RecordList, 'ColumnList' => $ColumnList, 'ActionList' => $ActionList, 'ModName' => $ModuleName, 'operation' => $modelaccess, 'modulepermission' => $modulepermission, 'totalitemcount' => $totalitemcount, 'searchparam' => $srch, 'divcontainer' => $divcontainer, 'relatedactions' => $relatedactions, 'TabLabel' => $TabLabel, 'sourcemodule' => $sourcemodule, 'sourceid' => $sourceid));
        }
    }
    public function actionBulkupdateview()
    {
        // $this->enableCsrfValidation = false;//disable csrf
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;


        $id = Yii::$app->user->id;
        $uid = Yii::$app->params['dirName'];

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $modulepermission = $model->modulepermission($profile, $tabs);

        $actionid = "edit";
        $model = new EditModel($TableName, $FieldId, $ModuleName, $actionid);
        //$model->_members[$FieldId]=$Record;
        $arrRender['model'] = $model;
        $ActionList = $model->getActionList($ModuleName);
        $ActionList['ActionName'] = "Edit";
        $this->getClientScript($ModuleName, "massedit");

        $Column = $model->getFieldDetail($rolebasedrecord);
        // echo "xdgdf<pre>";
        // print_r($Column);die;
        $arrRender['ColumnList'] = $Column;
        $arrRender['profile'] = $profile;
        $arrRender['uid'] = $id;
        $arrRender['tabs'] = $tabs;
        $arrRender['hasadminpower'] = $hasadminpower;
        $arrRender['TableName'] = $TableName;
        $arrRender['FieldId'] = $FieldId;
        $arrRender['action_name'] = $actionid;
        $modeluser = new UsersDetails();
        $arrRender['userlist'] = $modeluser->getuserlist();
        $arrRender['ModuleName'] = $ModuleName;
        $arrRender['TableName'] = $TableName;
        $arrRender['FieldId'] = $FieldId;
        $arrRender['Tabname'] = $TabLabel; //ucfirst($ModuleName);


        $this->layout = '@app/views/layouts/main-one';
        $this->renderPartial('@app/views/tetra/Bulkupdateview', $arrRender);
    }

    public function actionPopupsearch()
    {
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;


        $id = Yii::$app->user->id;
        $uid = Yii::$app->params['dirName'];

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $modulepermission = $model->modulepermission($profile, $tabs);



        //$model=new ListModel($TableName,$FieldId);
        $model = new Reference($TableName, $FieldId);
        $ActionList = $model->getActionList($ModuleName);
        //print_r($ActionList);die;
        $ActionList['OrderBy'] = Yii::$app->request->get('OrderBy');
        $ActionList['SortOrder'] = Yii::$app->request->get('SortOrder');
        list($ColumnList, $RecordList, $totalitemcount) = $model->getListRecord_pop($ActionList['OrderBy'], $ActionList['SortOrder'], $rolebasedrecord, $modulepermission, $ModuleName);
        $this->layout = '@app/views/layouts/main-new';
        $this->renderPartial('@app/views/tetra/PopSearch', array('RecordList' => $RecordList, 'ColumnList' => $ColumnList, 'ActionList' => $ActionList, 'ModName' => $ModuleName, 'operation' => $modelaccess, 'modulepermission' => $modulepermission, 'totalitemcount' => $totalitemcount, 'TabLabel' => $TabLabel));
    }
    public function actionGetcolumnfields()
    {
        $TabId = $this->TabId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        //get customview
        $savedColumns = (new \yii\db\Query())
            ->select(['cvid'])
            ->from('customview')
            ->where(['entitytype' => ucfirst($ModuleName)])
            ->andWhere(['userid' => Yii::$app->user->id])
            ->column();
        //print_r($savedColumns);die;
        // Get the columns saved for the current user
        $savedColumns = (new \yii\db\Query())
            ->select(['fieldid'])
            ->from('cvcolumnlist')
            ->where(['cvid' => $savedColumns[0]])
            ->orderBy(['columnindex' => SORT_ASC])
            ->column();


        // Get all columns for the 'leaddetails' table
        $columns = (new \yii\db\Query())
            ->select(['columnname AS field', 'fieldlabel AS headerName', 'fieldid'])
            ->from('field')
            ->where(['tabid' => $TabId])
            // ->orderBy(['list_view' => 1]) 
            ->all();
        //print_r($columns);die;

        // Set visibility based on whether column is in saved columns
        foreach ($columns as &$column) {
            $column['visible'] = in_array($column['fieldid'], $savedColumns);
        }
        // echo "<pre>";
        // print_r($columns);die;
        $connection = Yii::$app->db;
        //fist check user list

        $command = $connection->createCommand("
        SELECT field.fieldid as fieldid, field.columnname AS field, field.fieldlabel as headerName
        FROM customview 
        JOIN cvcolumnlist ON customview.cvid = cvcolumnlist.cvid
        JOIN field ON cvcolumnlist.columnname = field.columnname
        WHERE  UPPER(customview.entitytype) = UPPER(:entitytype)
        AND customview.setdefault = :setdefault 
        AND field.tablename = :tablename 
        AND customview.userid=:userid
        ORDER BY cvcolumnlist.columnindex
        ")
            ->bindValue(':entitytype', $ModuleName)
            ->bindValue(':setdefault', 1)
            ->bindValue(':userid', Yii::$app->user->id)
            ->bindValue(':tablename', $TableName);


        $columns = $command->queryAll();
        if (empty($columns)) {

            $command = $connection->createCommand("
        SELECT field.fieldid as fieldid, field.columnname AS field, field.fieldlabel as headerName
        FROM customview 
        JOIN cvcolumnlist ON customview.cvid = cvcolumnlist.cvid
        JOIN field ON cvcolumnlist.columnname = field.columnname
        WHERE  UPPER(customview.entitytype) = UPPER(:entitytype)
        AND customview.setdefault = :setdefault 
        AND field.tablename = :tablename 
        AND customview.userid=1
        ORDER BY cvcolumnlist.columnindex
        ")
                ->bindValue(':entitytype', $ModuleName)
                ->bindValue(':setdefault', 1)
                ->bindValue(':tablename', $TableName);


            $columns = $command->queryAll();
        }
        foreach ($columns as &$column) {
            $column['visible'] = in_array($column['fieldid'], $savedColumns);
        }
        $id = Yii::$app->user->id;
        $accmodel = new AccessCheck();
        $tabs = $accmodel->tabs($id, $ModuleName);
        $profile = $accmodel->profile($id, $tabs, $ModuleName);
        $modelaccess = $accmodel->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $accmodel->rolebasedrecord($id, $profile);
        $hasadminpower = $accmodel->hasadminpower($profile);   
        // editpermission added by ptpatel on date 11-12-2025 for inventory status update from grid     
        $editpermission = $accmodel->checkpermission($id, $ModuleName, "edit");
        foreach ($columns as &$cols) {
            $fiedlObj = Field::find()
                ->select(['single_edit', 'uitype', 'tabid'])
                ->where(['fieldid' => $cols['fieldid']])
                ->one();
            // echo "<pre>";print_r($iseditable);die;
            if ($hasadminpower == 1) {
                $visible = 0;
                $readonly = 0;
            } else {

                $permission = $accmodel->fieldacces($id, $cols['fieldid']);
                if (is_array($permission)) {
                    $visible = $permission['visible'];
                    $readonly = $permission['readonly'];
                } else { //remove when fieldaccess is implemented properly
                    $visible = 0;
                    $readonly = 0;
                }
            }
            $cols['visible_permission'] = $visible;
            $cols['readonly_permission'] = $readonly;
            $cols['single_edit'] = $fiedlObj->single_edit;
            $cols['uitype'] = $fiedlObj->uitype;
            $cols['tabid'] = $fiedlObj->tabid;
            $cols['userid'] = $id;
            $cols['editpermission'] = $editpermission;
        }
        //code end added by ptpatel
        return $columns;
    }

    public function actionTest()
    {
        // $this->layout = '@app/views/layouts/main-new';
        // $this->render('@app/views/tetra/test',);

        $this->layout = '@app/views/layouts/main-one';
        $this->render('@app/views/tetra/MultiCol');
    }
    public function actionDetailold()
    {
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;
        $layout = $this->layout;


        $Recordid = Yii::$app->request->get('Record');

        $id = Yii::$app->user->id;

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $modulepermission = $model->modulepermission($profile, $tabs);


        $actionid = "detail";
        $model = new EditModel($TableName, $FieldId, $ModuleName, $actionid);
        $model->_members[$FieldId] = $Recordid;
        $arrRender['model'] = $model;
        $ActionList = $model->getActionList($ModuleName);
        $ActionList['ActionName'] = "Edit";
        $this->getClientScript($ModuleName, "edit");

        //get related modules
        $relatemodules = $model->getRelatedmodules($TabId);

        // echo "<br>Final Else Case";
        list($Column, $Record) = $model->getFieldDetail($rolebasedrecord);
        $arrRender['Record'] = $Record;

        // get header column
        $header = $model->getHeaderDetail($TabId);
        $headerfullname = '';
        //print_r($header);die;
        if (!empty($header) && isset($header['columns'])) {
            $arr_tab = Yii::$app->db
                ->createCommand("SELECT CONCAT_WS(' ', " . $header['columns'] . ") AS full_name FROM $TableName where $FieldId=$Recordid")
                // ->bindValue(":TableName", $TableName)
                ->queryOne();
            // print_r($arr_tab);die;

            $headerfullname = $arr_tab['full_name'];
        }

        // for pipeline stage
        $modellist = new ListModel($TableName, $FieldId, $ModuleName);
        $filed_name = $modellist->getColumnList();
        $kanbnacolumn = $modellist->getKanbanList();

        if ($kanbnacolumn) {
            // print_r($kanbnacolumn);die;
            //fetch from picklist
            $fieldid = $kanbnacolumn['fieldid'];
            $PickListDetail = $modellist->getPickListDetail($fieldid);
            $targettable = $PickListDetail['targettable'];
            $targetfield = $PickListDetail['targetfield'];
            $dispfield = $PickListDetail['dispfield'];
            if ($ModuleName == 'leads') {
                $kanbanstatus = (new \yii\db\Query())
                    ->select(['*'])
                    ->from($targettable)
                    ->where(['is_active' => 1])
                    // ->andWhere(['>', 'pipeline_status', 0]) // Corrected "not in" syntax
                    ->orderBy(['seq_no' => SORT_ASC])
                    ->all();
            } else {
                $kanbanstatus = (new \yii\db\Query())
                    ->select(['*'])
                    ->from($targettable)
                    ->where(['is_active' => 1])
                    ->orderBy(['seq_no' => SORT_ASC])
                    ->all();
            }
            // print_r($kanbanstatus);die;
            $arrRender['pipelinecolumn'] = $kanbnacolumn['fieldname'];
            $arrRender['pipelinetatuses'] = $kanbanstatus;
            $arrRender['pipelinestatusid'] = $targetfield;
            $arrRender['pipelinestatusvalue'] = $dispfield;
            $arrRender['kanbancolumn'] = $kanbnacolumn;

            $ActionList = $model->getActionList($ModuleName);
            $ActionList['OrderBy'] = ''; //Yii::$app->request->get('OrderBy');
            $ActionList['SortOrder'] = ''; //Yii::$app->request->get('SortOrder');
            $curPageNo = ''; //$_REQUEST['pagejump'];
            // list($ColumnList, $RecordList, $totalitemcount) = $model->getListRecord($ActionList['OrderBy'], $ActionList['SortOrder'], $rolebasedrecord, $modulepermission);
            // $arrRender['leadInformation'] = $RecordList;



        }
        //end pipeline stage
        $Recordid = Yii::$app->request->get('Record');

        //check if document is related
        $docModules = '';
        $docrecords = '';
        if (!empty($relatemodules)) {
            $targetModules = [23];
            $docModules = array_filter($relatemodules, function ($item) use ($targetModules) {
                return in_array($item['related_module'], $targetModules);
            });
            if (!empty($docModules)) {
                //get all the doucments
                $dmodel = new Documentmodel();

                // $allnotes = $model->find()
                // ->where(['related_to' => $TabId])
                // ->andWhere(['related_to_id' => $Record])
                // ->orderBy(['modnotesid' => SORT_DESC])
                // ->all();
                $docrecords = $dmodel->find(['doc_no', 'filename', 'title', 'notecontent', 'folderid'])
                    ->where(['related_to' => $TabId])
                    ->andWhere(['related_to_id' => $Recordid])
                    ->andWhere(['deleted' => 0])
                    ->orderBy(['docid' => SORT_DESC])
                    ->all();
                //   print_r($docrecords);die;

            }
        }

        //echo "<pre>"; print_r($arrRender);echo "</pre>";die;
        $arrRender['ColumnList'] = $Column;
        $modeluser = new UsersDetails();
        $arrRender['userlist'] = $modeluser->getuserlist();
        $arrRender['ModuleName'] = $ModuleName;
        $arrRender['TabId'] = $TabId;
        $arrRender['TableName'] = $TableName;
        $arrRender['FieldId'] = $FieldId;
        $arrRender['Tabname'] = ucfirst($ModuleName);
        $arrRender['Recordid'] = $Recordid;
        $arrRender['getnotes'] = $this->getnotes($Recordid);
        $arrRender['allactivities'] = $this->getAllActivities($Recordid);
        $arrRender['Detailhistory'] = $this->Detailhistory($Recordid);
        $arrRender['headerfullname'] = $headerfullname;
        $arrRender['relatemodules'] = $relatemodules;
        $arrRender['TabLabel'] = $TabLabel;
        $arrRender['layout'] = $layout;
        $arrRender['docModules'] = $docModules;
        $arrRender['docrecords'] = $docrecords;
        $arrRender['hasadminpower'] = $hasadminpower;
        // print_r( $arrRender['Detailhistory']);die;
        // $this->layout = '@app/views/layouts/main-new'; 
        $this->layout = '@app/views/layouts/main-one';
        $this->render('@app/views/tetra/detailview', $arrRender);
    }
    public function actionDetail()
    {
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;
        $layout = $this->layout;


        $Recordid = Yii::$app->request->get('Record');



        $id = Yii::$app->user->id;

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $modulepermission = $model->modulepermission($profile, $tabs);
        $editpermission = $model->checkpermission($id, $ModuleName, "edit");
        $exportpermission = $model->checkpermission($id, $ModuleName, 'export');
        $importpermission = $model->checkpermission($id, $ModuleName, 'import');


        $actionid = "detail";
        $model = new EditModel($TableName, $FieldId, $ModuleName, $actionid);
        $model->_members[$FieldId] = $Recordid;
        $arrRender['model'] = $model;
        $ActionList = $model->getActionList($ModuleName);
        $ActionList['ActionName'] = "Edit";
        $this->getClientScript($ModuleName, "edit");

        //update notification
        $this->updatenotification($id, $Recordid);

        //get related modules
        $relatemodules = $model->getRelatedmodules($TabId);

        // echo "<br>Final Else Case";
        try {
            list($Column, $Record) = $model->getFieldDetail($rolebasedrecord);
        } catch (\Throwable $e) {
            Yii::error("Error in getFieldDetail: " . $e->getMessage(), __METHOD__);

            // Optional: show a friendly error to user
            Yii::$app->session->setFlash('error', $e->getMessage());
            $this->layout = "@backend/views/layouts/main-one";
            // Option 1: Redirect or render error
            return $this->render('@backend/views/site/errorcustom', [
                'message' => $e->getMessage(),
            ]);

            // Option 2 (if inside an AJAX context):
            // return ['success' => false, 'message' => $e->getMessage()];
        }

        $arrRender['Record'] = $Record;

        // get header column
        $header = $model->getHeaderDetail($TabId);
        $headerfullname = '';
        //print_r($header);die;
        if (!empty($header) && isset($header['columns'])) {
            $arr_tab = Yii::$app->db
                ->createCommand("SELECT CONCAT_WS(' ', " . $header['columns'] . ") AS full_name FROM $TableName where $FieldId=$Recordid")
                // ->bindValue(":TableName", $TableName)
                ->queryOne();
            // print_r($arr_tab);die;

            $headerfullname = $arr_tab['full_name'];
        }

        /////get related detail//////////
        $srcheaderfullname = '';
        $sourcemodule = Yii::$app->request->get('sourcemodule');
        $sourceid = Yii::$app->request->get('sourceid');
        if (!empty($sourceid) && !empty($sourcemodule)) {
            // get header column
            $header = $model->getHeaderDetail($sourcemodule);
            $srcheaderfullname = '';
            //print_r($header);die;
            if (!empty($header) && isset($header['columns'])) {
                //get related tblename
                $arrtab = Yii::$app->db
                    ->createCommand("SELECT tablename FROM field where tabid=$sourcemodule limit 1")
                    // ->bindValue(":TableName", $TableName)
                    ->queryOne();
                $relatedTableName = $arrtab['tablename'];


                // // Get the table schema
                $tableSchema = Yii::$app->db->schema->getTableSchema($relatedTableName);

                // // Check if the table exists and has a primary key
                if ($tableSchema !== null && !empty($tableSchema->primaryKey)) {
                    // echo "Primary key column(s) for table '$relatedTableName': " . implode(', ', $tableSchema->primaryKey);
                    $retFieldId = implode(', ', $tableSchema->primaryKey);
                    if ($retFieldId) {
                        $arr_tab = Yii::$app->db
                            ->createCommand("SELECT CONCAT_WS(' ', " . $header['columns'] . ") AS full_name FROM $relatedTableName where $retFieldId=$sourceid")
                            // ->bindValue(":TableName", $TableName)
                            ->queryOne();
                        // print_r($arr_tab);die;

                        $srcheaderfullname = $arr_tab['full_name'];
                    }
                }
            }
        }


        // for pipeline stage
        $modellist = new ListModel($TableName, $FieldId, $ModuleName);
        $filed_name = $modellist->getColumnList();
        $kanbnacolumn = $modellist->getKanbanList();

        if ($kanbnacolumn) {
            // print_r($kanbnacolumn);die;
            //fetch from picklist
            $fieldid = $kanbnacolumn['fieldid'];
            $PickListDetail = $modellist->getPickListDetail($fieldid);
            $targettable = $PickListDetail['targettable'];
            $targetfield = $PickListDetail['targetfield'];
            $dispfield = $PickListDetail['dispfield'];
            if ($ModuleName == 'leads') {
                $kanbanstatus = (new \yii\db\Query())
                    ->select(['*'])
                    ->from($targettable)
                    ->where(['is_active' => 1])
                    // ->andWhere(['>', 'pipeline_status', 0]) // Corrected "not in" syntax
                    ->orderBy(['seq_no' => SORT_ASC])
                    ->all();
            } else {
                $kanbanstatus = (new \yii\db\Query())
                    ->select(['*'])
                    ->from($targettable)
                    ->where(['is_active' => 1])
                    ->orderBy(['seq_no' => SORT_ASC])
                    ->all();
            }
            // print_r($kanbanstatus);die;
            $arrRender['pipelinecolumn'] = $kanbnacolumn['fieldname'];
            $arrRender['pipelinetatuses'] = $kanbanstatus;
            $arrRender['pipelinestatusid'] = $targetfield;
            $arrRender['pipelinestatusvalue'] = $dispfield;
            $arrRender['kanbancolumn'] = $kanbnacolumn;

            $ActionList = $model->getActionList($ModuleName);
            $ActionList['OrderBy'] = ''; //Yii::$app->request->get('OrderBy');
            $ActionList['SortOrder'] = ''; //Yii::$app->request->get('SortOrder');
            $curPageNo = ''; //$_REQUEST['pagejump'];
            // list($ColumnList, $RecordList, $totalitemcount) = $model->getListRecord($ActionList['OrderBy'], $ActionList['SortOrder'], $rolebasedrecord, $modulepermission);
            // $arrRender['leadInformation'] = $RecordList;



        }
        //end pipeline stage
        $Recordid = Yii::$app->request->get('Record');

        //check if document is related
        $docModules = '';
        $docrecords = '';
        if (!empty($relatemodules)) {
            $targetModules = [23];
            $docModules = array_filter($relatemodules, function ($item) use ($targetModules) {
                return in_array($item['related_module'], $targetModules);
            });
            if (!empty($docModules)) {
                //get all the doucments
                $dmodel = new Documentmodel();

                // $allnotes = $model->find()
                // ->where(['related_to' => $TabId])
                // ->andWhere(['related_to_id' => $Record])
                // ->orderBy(['modnotesid' => SORT_DESC])
                // ->all();
                $docrecords = $dmodel->find(['doc_no', 'filename', 'title', 'notecontent', 'folderid'])
                    ->where(['related_to' => $TabId])
                    ->andWhere(['related_to_id' => $Recordid])
                    ->andWhere(['deleted' => 0])
                    ->orderBy(['docid' => SORT_DESC])
                    ->all();
                //   print_r($docrecords);die;

            }
        }

        $arrRender['AllowCertificateGeneration'] = false;
        // a generalized model is required here
        $allowedActionsModel = new CheckAllowActions();
        $allowedActions = $allowedActionsModel->checkAllowedActions($TabId, $ModuleName, $Recordid, $Record, $exportpermission, $importpermission);
        if (!empty($allowedActions)) {
            foreach ($allowedActions as $key => $val) {
                $arrRender[$key] = $val;
                if ($key == "Edit" && $val !== true) {
                    //$Record['ownerid'] = null;
                }
            }
        }

        //echo "<pre>"; print_r($arrRender);echo "</pre>";die;
        $arrRender['ColumnList'] = $Column;
        $modeluser = new UsersDetails();
        $arrRender['userlist'] = $modeluser->getuserlist();
        $arrRender['ModuleName'] = $ModuleName;
        $arrRender['TabId'] = $TabId;
        $arrRender['TableName'] = $TableName;
        $arrRender['FieldId'] = $FieldId;
        $arrRender['Tabname'] = ucfirst($ModuleName);
        $arrRender['Recordid'] = $Recordid;
        $arrRender['getnotes'] = $this->getnotes($Recordid);
        $arrRender['allactivities'] = $this->getAllActivities($Recordid);
        $arrRender['Detailhistory'] = $this->Detailhistory($Recordid);
        $arrRender['headerfullname'] = $headerfullname;
        $arrRender['relatemodules'] = $relatemodules;
        $arrRender['TabLabel'] = $TabLabel;
        $arrRender['layout'] = $layout;
        $arrRender['docModules'] = $docModules;
        $arrRender['docrecords'] = $docrecords;
        $arrRender['hasadminpower'] = $hasadminpower;
        $arrRender['srcheaderfullname'] = $srcheaderfullname;
        $arrRender['editpermission'] = $editpermission;
        $arrRender['exportpermission'] = $exportpermission;

        // echo "<pre>";print_r( $arrRender['Detailhistory']);die;
        // $this->layout = '@app/views/layouts/main-new'; 
        $this->layout = '@app/views/layouts/main-one';
        $this->render('@app/views/tetra/detailview-new', $arrRender);
    }
    public function actionGetallrelatedmodules()
    {
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;
        $layout = $this->layout;


        $Recordid = Yii::$app->request->get('Record');

        $id = Yii::$app->user->id;


        $actionid = "detail";
        $model = new EditModel($TableName, $FieldId, $ModuleName, $actionid);
        $model->_members[$FieldId] = $Recordid;


        $arrRender['relatemodules'] = $model->getRelatedmodules($TabId);
        $arrRender['TabId'] = $TabId;
        $arrRender['Recordid'] = $Recordid;

        $this->layout = '@app/views/layouts/main-one';
        $this->renderPartial('@app/views/tetra/relatedmoduels-ajax', $arrRender);
    }
    public function getAllActivities($Record)
    {
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;


        $id = Yii::$app->user->id;
        $data = $_POST;
        // $Record = $_POST['Recordid'];

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);


        $query = (new \yii\db\Query())
            ->select([
                'call_information.subject AS activity_description',
                'call_information.callinfo_id AS activity_id',
                'call_information.call_start_time AS activity_date', // Use `changedon` for calls
                new \yii\db\Expression("'call' AS activity_type"),
            ])
            ->from('call_information')

            ->where(['call_information.related_to' => $TabId, 'call_information.related_to_id' => $Record])
            ->andWhere(['>', 'call_information.call_start_time', new \yii\db\Expression('NOW()')]);



        $query->union(
            (new \yii\db\Query())
                ->select([
                    'meeting_information.title AS activity_description',
                    'meeting_information.meetinginfo_id AS activity_id',
                    'meeting_information.from AS activity_date', // Use `form_date` for meetings
                    new \yii\db\Expression("'meeting' AS activity_type"),
                ])
                ->from('meeting_information')

                ->where(['meeting_information.related_to' => $TabId, 'meeting_information.related_to_id' => $Record])
                ->andWhere(['>', 'meeting_information.from', new \yii\db\Expression('NOW()')])

        );

        $query->union(
            (new \yii\db\Query())
                ->select([
                    'task_information.subject AS activity_description',
                    'task_information.taskinfo_id AS activity_id',
                    'task_information.due_date AS activity_date', // Use `changedon` for tasks
                    new \yii\db\Expression("'task' AS activity_type"),
                ])
                ->from('task_information')
                ->where(['task_information.related_to' => $TabId, 'task_information.related_to_id' => $Record])
                ->andWhere(['>', 'task_information.due_date', new \yii\db\Expression('NOW()')])

        );

        // Combine and sort by `activity_date` in descending order
        $activities = (new \yii\db\Query())
            ->from(['combined' => $query])
            ->orderBy(['activity_date' => SORT_ASC])
            ->all();


        // print_r($activities);die;


        return $activities;
    }


    public function actionSavefilterbylead()
    {
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;

        $model = new AccessCheck();
        $id = Yii::$app->user->id;
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $modulepermission = $model->modulepermission($profile, $tabs);
        $now = date('Y-m-d H:i:s');
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $labelValue = Yii::$app->request->post('labelValue');
            $inputValue = Yii::$app->request->post('inputValue');
            if (isset($_POST['inputValue'])) {
                if (is_array($_POST['inputValue'])) {
                    $inputValue = implode(',', $inputValue);
                }
            }
            $fieldId = Yii::$app->request->post('fieldId');
            $filterOperator = Yii::$app->request->post('filteroperator');
            $filterid = Yii::$app->request->post('filterid');

            // Remove any quotes from the value, if present
            $filterOperator = trim($filterOperator, "'\"");

            // Insert into user_filter using Active Record
            $save_filter = new UserFilter();
            $save_filter->fieldlabel = $labelValue;
            $save_filter->userinput = $inputValue;
            $save_filter->fieldid = $fieldId;
            $save_filter->userid = $id;
            $save_filter->filteroperator = $filterOperator;
            $save_filter->filter_id = $filterid;
            $save_filter->created_by = $id;
            $save_filter->created_at = $now;
            $save_filter->modified_by = $id;
            $save_filter->modified_at = $now;
            $save_filter->action = 'created';
            //  print_r($save_filter->save());die;
            if ($save_filter->save()) {
                return $this->asJson([
                    'status' => 'success',
                    'message' => 'Lead filter values created successfully.',
                ]);
            } else {
                return $this->asJson([
                    'status' => 'error',
                    'message' => 'Failed to update or create Lead filter record.',
                    'errors' => $save_filter->getErrors(),
                ]);
            }
        }

        return $this->asJson([
            'status' => 'error',
            'message' => 'Invalid request method.',
        ]);
    }




    public function actionShowsavefilterfeilds()
    {
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;

        $model = new AccessCheck();
        $id = Yii::$app->user->id;
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $modulepermission = $model->modulepermission($profile, $tabs);

        if (Yii::$app->request->isAjax) {
            $user_id = Yii::$app->user->id;
            $lead_filterid = Yii::$app->request->get('lead_filterid'); // Use get() since AJAX uses GET

            // Fetch filter fields from the database
            $filters = (new \yii\db\Query())
                ->select('*')
                ->from('user_filter')
                ->where(['userid' => $user_id, 'deleted' => 0, 'filter_id' => $lead_filterid])
                ->all();

            if ($filters) {
                return $this->asJson([
                    'status' => 'success',
                    'filters' => $filters,
                ]);
            } else {
                return $this->asJson([
                    'status' => 'error',
                    'message' => 'No filter fields found for the given filter name.',
                ]);
            }
        }

        return $this->asJson([
            'status' => 'error',
            'message' => 'Invalid request method.',
        ]);
    }


    public function getcalls()
    {
        // print_r($_POST);die;
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;



        $id = Yii::$app->user->id;
        $data = $_POST;
        $Record = 24; //$_POST['Recordid'];

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);

        $model = new CallInformation();
        $records = $model->find()
            ->joinWith(['modtrackerBasic'])
            ->where(['call_information.related_to' => $TabId])
            ->andWhere(['call_information.related_to_id' => $Record])
            ->orderBy(['call_information.callinfo_id' => SORT_DESC])
            ->all();
        $detail = array();
        foreach ($records as $record) {
            $a = array();


            $a['callcomments'] = $record->comments;
            $a['callbyuser'] = Yii::$app->user->id;
            //print_r($record->modtrackerBasic);die;
            // Access related ModtrackerBasic fields
            if ($record->modtrackerBasic) {
                $timestamp = strtotime($record->modtrackerBasic->changedon);

                // Format the date
                $enteredat = date('M d, Y \a\t g.i a', $timestamp); // Format: Oct 22, 2024 at 9.16 am
                $a['calldon'] = $enteredat;
            } else
                $a['calldon'] = '';

            // } else {
            //     echo "No ModtrackerBasic found for this Modnotes.\n";
            // }
            array_push($detail, $a);
        }
        // echo "<pre>";print_r($detail);
        //die;
        return $detail;
    }


    public function getmeetings()
    {
        // print_r($_POST);die;
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;


        $id = Yii::$app->user->id;
        $data = $_POST;
        $Record = 24; //$_POST['Recordid'];

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);

        $model = new MeetingInformation();
        $records = $model->find()
            ->joinWith(['modtrackerBasic'])
            ->where(['meeting_information.related_to' => $TabId])
            ->andWhere(['meeting_information.related_to_id' => $Record])
            ->orderBy(['meeting_information.meetinginfo_id' => SORT_DESC])
            ->all();
        $detail = array();
        foreach ($records as $record) {
            $a = array();

            $a['meetingdescription'] = $record->description;
            //print_r($record->modtrackerBasic);die;
            // Access related ModtrackerBasic fields
            if ($record->modtrackerBasic) {
                $timestamp = strtotime($record->modtrackerBasic->changedon);

                // Format the date
                $enteredat = date('M d, Y \a\t g.i a', $timestamp); // Format: Oct 22, 2024 at 9.16 am
                $a['meetingdon'] = $enteredat;
            } else
                $a['meetingdon'] = '';

            // } else {
            //     echo "No ModtrackerBasic found for this Modnotes.\n";
            // }
            array_push($detail, $a);
        }
        // echo "<pre>";print_r($detail);
        //die;
        return $detail;
    }

    public function gettasks()
    {
        // print_r($_POST);die;
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;


        $id = Yii::$app->user->id;
        $data = $_POST;
        $Record = 24; //$_POST['Recordid'];

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);

        $model = new TaskInformation();
        $records = $model->find()
            ->joinWith(['modtrackerBasic'])
            ->where(['task_information.related_to' => $TabId])

            ->orderBy(['task_information.taskinfo_id' => SORT_DESC])
            ->all();
        $detail = array();
        foreach ($records as $record) {
            $a = array();

            $a['taskdescription'] = $record->description;
            $timestamp = strtotime($record->due_date);
            $enteredat = date('M d, Y \a\t g.i a', $timestamp);
            $a['taskduedate'] = $enteredat;
            //print_r($record->modtrackerBasic);die;
            // Access related ModtrackerBasic fields

            // if ($record->modtrackerBasic) {
            //     $timestamp = strtotime($record->modtrackerBasic->changedon);

            //     // Format the date
            //     $enteredat = date('M d, Y \a\t g.i a', $timestamp); // Format: Oct 22, 2024 at 9.16 am
            //     $a['meetingdon'] = $enteredat;
            // } else $a['meetingdon'] = '';

            // } else {
            //     echo "No ModtrackerBasic found for this Modnotes.\n";
            // }
            array_push($detail, $a);
        }
        // echo "<pre>";print_r($detail);
        //die;
        return $detail;
    }

    public function actionCreate()
    {
        ini_set('max_execution_time', 30000);
        // print_r($_POST); exit;
        $sourceid = Yii::$app->request->get('sourceid');
        $sourcemodule = Yii::$app->request->get('sourcemodule');


        //print_r($_SESSION);die;
        //   	$ModuleName="Tetra";
        $action = "Edit";
        // $this->getClientScript($ModuleName,$action);
        //              Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/Tetra/jqdt.js', CClientScript::POS_END);		
        $arrRender = array();
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;
        $layout = $this->layout;
        //direct creation of account not allowed as per new account flow it only create by raise request
        if($TabId == 18)
        {
            $this->layout = '@backend/views/layouts/main-one'; // Disable layout for this action
                        return $this->render('@backend/views/site/errorcustom', [
                            'message' => 'Action Not Allowed.',
                        ]);
        }

        // echo $uid=Yii::$app->session[Yii::$app->params['dirName'].'_id'];die;
        $id = Yii::$app->user->id;
        $uid = Yii::$app->params['dirName'];

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $modulepermission = $model->modulepermission($profile, $tabs);
        // print_r($rolebasedrecord);
        $actionid = "create";
        $model = new EditModel($TableName, $FieldId, $ModuleName, $actionid);
        $arrRender['model'] = $model;
        $ActionList = $model->getActionList($ModuleName);
        $ActionList['ActionName'] = "Create";
        $this->getClientScript($ModuleName, strtolower($action));

        //if isset sourceid and sourcemodule check related field name 
        $relatedkeys = $model->getralatedkeys($TabId);
        // print_r($relatedkeys);die;

        $arrRender['ActionList'] = $ActionList;
        if (isset($_POST['btncancel'])) {
            $this->redirect(array("$ModuleName/List"));
        } elseif ($this->request->isPost) {
            $tabs = 1;
            if (Yii::$app->request->isPost) {

                // echo "<pre>";print_r($_POST);die;
                try {

                //check duplicate value
                  $uniquefields = (new \yii\db\Query())
                        ->select(['columnname','fieldlabel'])
                        ->from('field')
                        ->where([
                            'tabid' => $TabId,
                            'isunique' => 1
                        ])
                        ->all();
                        // echo "<pre>";print_r($uniquefields);
                    foreach ($uniquefields as $field) {

                        $fieldName  = $field['columnname'];
                        $fieldlabel = $field['fieldlabel'];
                        $value = trim($_POST[$TableName][$fieldName] ?? '');
                        if (!empty($value)) {
                            if ($this->actionCheckduplicate($fieldName, $value,null)) {
                                
                                throw new \Exception("$value already exists for $fieldlabel.");
                            }
                        }
                    }
                    // die;
                    //check duplicate value
                    $result = $model->saveModule($tabs);

                    if ($result) {
                        Yii::$app->session->setFlash('success', 'Data saved successfully.');

                        if (!empty($sourceid) && !empty($sourcemodule)) {
                            return $this->redirect([
                                'list',
                                'sourcemodule' => $sourcemodule,
                                'sourceid' => $sourceid
                            ]);
                        } elseif (in_array($TabId, [67, 68, 69, 70])) {
                            // Segregation, Tagging, Sticker Removal, Cleaning
                            return $this->redirect(['dashboard']);
                        } else {
                            return $this->redirect(['list']);
                        }

                    } else {
                        Yii::$app->session->setFlash('error', 'Failed to save data.');
                        $this->layout = '@backend/views/layouts/main-one'; // Disable layout for this action
                        return $this->render('@backend/views/site/errorcustom', [
                            'message' => $e->getMessage(),
                        ]);
                    }

                } catch (\Throwable $e) {
                    // Optional: Log the exception
                    //echo "deep".$e->getMessage();die;
                    Yii::error("Error in saveModule: " . $e->getMessage(), __METHOD__);

                    // Flash message for user
                    Yii::$app->session->setFlash('error', $e->getMessage());
                    $this->layout = '@backend/views/layouts/main-one'; // Disable layout for this action
                    return $this->render('@backend/views/site/errorcustom', [
                        'message' => $e->getMessage(),
                    ]);


                }

            }
        } else {

            // echo "<br>Final Else Case";
            // die;
            //rolebase will be implemented later
            // $Column=$model->getFieldDetail($rolebasedrecord);
            $Column = $model->getFieldDetail($rolebasedrecord);
            // echo "xdgdf<pre>";
            // print_r($Column);die;
            $arrRender['ColumnList'] = $Column;
            $arrRender['profile'] = $profile;
            $arrRender['uid'] = $id;
            $arrRender['tabs'] = $tabs;
            $arrRender['hasadminpower'] = $hasadminpower;
            $arrRender['TableName'] = $TableName;
            $arrRender['FieldId'] = $FieldId;
            $arrRender['action_name'] = $actionid;
            $arrRender['sourceid'] = $sourceid;
            $arrRender['sourcemodule'] = $sourcemodule;
            $arrRender['TabId'] = $TabId;
            $arrRender['TabLabel'] = $TabLabel;
            $arrRender['layout'] = $layout;
            $arrRender['relatedkeys'] = $relatedkeys;
            $arrRender['hasadminpower'] = $hasadminpower;
            $arrRender['RecordId'] = '';
            //  //below line added by ptpatel on date 08-04-25
            $arrRender['roleId'] = $rolebasedrecord['roleid'];
            // echo "xdgdf<pre>";
            // print_r($arrRender)	;die;
            // $this->layout = 'main';

            // $this->layout = '@app/views/layouts/main'; 
            // $this->render('@app/views/tetra/EditView',$arrRender);

            $this->layout = '@app/views/layouts/main-new';
            // $this->render('@app/views/tetra/EditView-old',$arrRender);
            // echo $layout;die;

            if ($layout == "multiple" || $layout == "single") {
                $this->layout = '@app/views/layouts/main-one';
                $this->render('@app/views/tetra/MultiCol', $arrRender);
            } else if ($layout == "contactrole")
                $this->renderPartial('@app/views/tetra/ContactroleEditView', $arrRender);
            else
                $this->renderPartial('@app/views/tetra/EditView', $arrRender);
        }
        // return $this->render('index');
    }
     public function actionBulkuplaodtagging()
    {
        ini_set('max_execution_time', 30000);
        // print_r($_POST); exit;
        $sourceid = Yii::$app->request->get('sourceid');
        $sourcemodule = Yii::$app->request->get('sourcemodule');
        $taggingdetails = Yii::$app->request->get('itemid');

        //print_r($_SESSION);die;
        //   	$ModuleName="Tetra";
        $action = "Edit";
        // $this->getClientScript($ModuleName,$action);
        //              Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/Tetra/jqdt.js', CClientScript::POS_END);		
        $arrRender = array();
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;
        $layout = $this->layout;
        //direct creation of account not allowed as per new account flow it only create by raise request
        if($TabId == 18)
        {
            $this->layout = '@backend/views/layouts/main-one'; // Disable layout for this action
                        return $this->render('@backend/views/site/errorcustom', [
                            'message' => 'Action Not Allowed.',
                        ]);
        }

        // echo $uid=Yii::$app->session[Yii::$app->params['dirName'].'_id'];die;
        $id = Yii::$app->user->id;
        $uid = Yii::$app->params['dirName'];

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $modulepermission = $model->modulepermission($profile, $tabs);
        // print_r($rolebasedrecord);
        $actionid = "create";
        $model = new EditModel($TableName, $FieldId, $ModuleName, $actionid);
        $arrRender['model'] = $model;
        $ActionList = $model->getActionList($ModuleName);
        $ActionList['ActionName'] = "Create";
        $this->getClientScript($ModuleName, strtolower($action));

        //if isset sourceid and sourcemodule check related field name 
        $relatedkeys = $model->getralatedkeys($TabId);
        // print_r($relatedkeys);die;

        $arrRender['ActionList'] = $ActionList;
        if (isset($_POST['btncancel'])) {
            $this->redirect(array("$ModuleName/List"));
        } elseif ($this->request->isPost) {
            $tabs = 1;
            if (Yii::$app->request->isPost) {

                // echo "<pre>";print_r($_POST);die;
                try {
                    $result = $model->saveModule($tabs);

                    if ($result) {
                        Yii::$app->session->setFlash('success', 'Data saved successfully.');

                        if (!empty($sourceid) && !empty($sourcemodule)) {
                            return $this->redirect([
                                'list',
                                'sourcemodule' => $sourcemodule,
                                'sourceid' => $sourceid
                            ]);
                        } elseif (in_array($TabId, [67, 68, 69, 70])) {
                            // Segregation, Tagging, Sticker Removal, Cleaning
                            return $this->redirect(['dashboard']);
                        } else {
                            return $this->redirect(['list']);
                        }

                    } else {
                        Yii::$app->session->setFlash('error', 'Failed to save data.');
                        $this->layout = '@backend/views/layouts/main-one'; // Disable layout for this action
                        return $this->render('@backend/views/site/errorcustom', [
                            'message' => $e->getMessage(),
                        ]);
                    }

                } catch (\Throwable $e) {
                    // Optional: Log the exception
                    //echo "deep".$e->getMessage();die;
                    Yii::error("Error in saveModule: " . $e->getMessage(), __METHOD__);

                    // Flash message for user
                    Yii::$app->session->setFlash('error', $e->getMessage());
                    $this->layout = '@backend/views/layouts/main-one'; // Disable layout for this action
                    return $this->render('@backend/views/site/errorcustom', [
                        'message' => $e->getMessage(),
                    ]);


                }

            }
        } else {

            // echo "<br>Final Else Case";
            // die;
            //rolebase will be implemented later
            // $Column=$model->getFieldDetail($rolebasedrecord);
            $Column = $model->getFieldDetail($rolebasedrecord);
            // echo "xdgdf<pre>";
            // print_r($Column);die;
            $arrRender['ColumnList'] = $Column;
            $arrRender['profile'] = $profile;
            $arrRender['uid'] = $id;
            $arrRender['tabs'] = $tabs;
            $arrRender['hasadminpower'] = $hasadminpower;
            $arrRender['TableName'] = $TableName;
            $arrRender['FieldId'] = $FieldId;
            $arrRender['action_name'] = $actionid;
            $arrRender['sourceid'] = $sourceid;
            $arrRender['sourcemodule'] = $sourcemodule;
            $arrRender['TabId'] = $TabId;
            $arrRender['TabLabel'] = $TabLabel;
            $arrRender['layout'] = $layout;
            $arrRender['relatedkeys'] = $relatedkeys;
            $arrRender['hasadminpower'] = $hasadminpower;
            $arrRender['RecordId'] = '';
            //  //below line added by ptpatel on date 08-04-25
            $arrRender['roleId'] = $rolebasedrecord['roleid'];
            $arrRender['taggingdetails'] = $taggingdetails ?? '';
            // echo "xdgdf<pre>";
            // print_r($arrRender)	;die;
            // $this->layout = 'main';

            // $this->layout = '@app/views/layouts/main'; 
            // $this->render('@app/views/tetra/EditView',$arrRender);

            $this->layout = '@app/views/layouts/main-new';
            // $this->render('@app/views/tetra/EditView-old',$arrRender);
            // echo $layout;die;

            
                $this->layout = '@app/views/layouts/main-one';
                $this->render('@app/views/tetra/MultiColTagging', $arrRender);
           
        }
        // return $this->render('index');
    }
    public function actionEdit()
    {
        $sourceid = Yii::$app->request->get('sourceid');
        $sourcemodule = Yii::$app->request->get('sourcemodule');
        $RecordId = Yii::$app->request->get('Record');
        // $RecordId = base64_decode($RecordId);
        //print_r($_SESSION);die;
        //      $ModuleName="Tetra";
        $action = "Edit";
        // $this->getClientScript($ModuleName,$action);
        //              Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/Tetra/jqdt.js', CClientScript::POS_END);        
        $arrRender = array();

        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;
        $TabId = $this->TabId;
        $layout = $this->layout;
        //Check for the access check according to the record in detaileditsetting table vishwas 26-02-2026
        // $this->checkEditPermissionByDetailSetting($TabId, $ModuleName, $RecordId);
        // echo $uid=Yii::$app->session[Yii::$app->params['dirName'].'_id'];die;
        $id = Yii::$app->user->id;
        $uid = Yii::$app->params['dirName'];

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        // print_r($rolebasedrecord);
        $actionid = "edit";
        $model = new EditModel($TableName, $FieldId, $ModuleName, $actionid);
        $model->_members[$FieldId] = $RecordId;
        $arrRender['model'] = $model;
        $ActionList = $model->getActionList($ModuleName);
        $ActionList['ActionName'] = "Edit";
        $this->getClientScript($ModuleName, strtolower($action));
        //if isset sourceid and sourcemodule check related field name 
        $relatedkeys = $model->getralatedkeys($TabId);

        $arrRender['ActionList'] = $ActionList;
        if (isset($_POST['btncancel'])) {
            $this->redirect(array("$ModuleName/List"));
        } elseif ($this->request->isPost) {
            $tabs = 1;
            if (Yii::$app->request->isPost) {
                //code added by ptpatel
                $singleedit = Yii::$app->request->Post('singleedit') ? 1 : 0;
                try {
                    //check duplicate value
                  $uniquefields = (new \yii\db\Query())
                        ->select(['columnname','fieldlabel'])
                        ->from('field')
                        ->where([
                            'tabid' => $TabId,
                            'isunique' => 1
                        ])
                        ->all();
                        // echo "<pre>";print_r($uniquefields);
                    foreach ($uniquefields as $field) {

                        $fieldName  = $field['columnname'];
                        $fieldlabel = $field['fieldlabel'];
                        $value = trim($_POST[$TableName][$fieldName] ?? '');
                        if (!empty($value)) {
                            if ($this->actionCheckduplicate($fieldName, $value,$RecordId)) {
                                
                                throw new \Exception("$value already exists for $fieldlabel.");
                            }
                        }
                    }
                    //check duplicate value
                    //check singleedit and update
                    if($singleedit == 1){
                        $result = $this->updateSingleField();
                        if($result)
                        {
                            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                            $arrRender['model'] = $model;
                            $this->getClientScript( $ModuleName, strtolower($action));
                            list($Column, $Record) = $model->getFieldDetail($rolebasedrecord);
                            $arrRender['Record'] = $Record;
                            $arrRender['ColumnList'] = $Column;
                            $arrRender['RecordId'] = $RecordId;
                            $arrRender['profile'] = $profile;
                            $arrRender['uid'] = $id;
                            $arrRender['tabs'] = $tabs;
                            $arrRender['hasadminpower'] = $hasadminpower;
                            $arrRender['TableName'] = $TableName;
                            $arrRender['FieldId'] = $FieldId;
                            $arrRender['action_name'] = $actionid;
                            $arrRender['relatedkeys'] = $relatedkeys;
                            $arrRender['ModuleName'] = $ModuleName;
                            $arrRender['TabId'] = $TabId;
                            $postData = Yii::$app->request->post();
                            // echo  $postData['from_page']; 
                            
                            $arrRender['Detailhistory'] = $this->Detailhistory($RecordId);
                            $arrRender['TabLabel'] = $TabLabel;
                            $historyHtml = $this->renderPartial('@app/views/tetra/detailhistory', $arrRender);
                            if ($postData['from_page'] == "summary") {
                                return [
                                    'success' => true,
                                    'from_page' => 'summary',
                                    'html' => 
                                    // $this->renderPartial('@app/views/tetra/editsummerylabel',
                                    $this->renderPartial('@app/views/tetra/detailsummary',
                                     [
                                        // 'field' => ['columnname' => $columnname, 'single_edit' => 0, 'uitype' => $uitype,'fieldlabel'=>$fieldlabel,'fieldid'=>$fieldid], // Adjust as needed
                                        'arrRender' => $arrRender,
                                        'Record' => $Record,
                                        'TabId' => $TabId,
                                        'Recordid' => $RecordId,
                                        'ModuleName' => $ModuleName,
                                        'TableName' => $TableName,
                                        'FieldId' => $FieldId,
                                        'hasadminpower' => $hasadminpower,
                                        'ColumnList'=>$Column,
                                        'baseUrl' => Yii::$app->HomeUrl,
                                    ]),
                                    'historyHtml' => $historyHtml
                                ];
                            } elseif ($postData['from_page'] == "list") {
                                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                                return [
                                    'success' => true,
                                    'from_page' => 'list',
                                ];
                            } elseif ($postData['from_page'] == "multiple") {
                                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                                return [
                                    'success' => true,
                                    'from_page' => 'multiple',
                                    'html' => 
                                    $this->renderPartial('@app/views/tetra/editsummarymultiple', [
                                    // $this->renderPartial('@app/views/tetra/detailsummarymultiple', [
                                        'arrRender' => $arrRender,
                                        'Record' => $Record,
                                        'TabId' => $TabId,
                                        'Recordid' => $RecordId,
                                        'ModuleName' => $ModuleName,
                                        'TableName' => $TableName,
                                        'FieldId' => $FieldId,
                                        'hasadminpower' => $hasadminpower,
                                        'ColumnList'=>$Column,
                                        'baseUrl' => Yii::$app->HomeUrl,
                                    ]),
                                    'historyHtml' => $historyHtml
                                ];
                            }

                            // }
                            //code ended added by ptpatel on date 17-03-25
                        }   
                        else
                        {
                            Yii::$app->session->setFlash('error', 'Data not updated.');
                            $this->layout = '@backend/views/layouts/main-one'; // Use your custom layout
                            return $this->render('@backend/views/site/errorcustom', [
                                'message' => 'Data not updated.',
                            ]);
                        }                         
                    }
                    else {
                    //check singleedit and update
                    if ($model->updateModule($RecordId)) {
                        // echo "saved";die;
                        Yii::$app->session->setFlash('success', 'Data saved successfully.');
                        //code added by ptpatel on date 17-03-25
                        // echo $singleedit."save";exit;
                        if ($singleedit) {
                            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                            $arrRender['model'] = $model;
                            $this->getClientScript($ModuleName, strtolower($action));
                            list($Column, $Record) = $model->getFieldDetail($rolebasedrecord);
                            $arrRender['Record'] = $Record;
                            $arrRender['ColumnList'] = $Column;
                            $arrRender['RecordId'] = $RecordId;
                            $arrRender['profile'] = $profile;
                            $arrRender['uid'] = $id;
                            $arrRender['tabs'] = $tabs;
                            $arrRender['hasadminpower'] = $hasadminpower;
                            $arrRender['TableName'] = $TableName;
                            $arrRender['FieldId'] = $FieldId;
                            $arrRender['action_name'] = $actionid;
                            $arrRender['relatedkeys'] = $relatedkeys;
                            $arrRender['hasadminpower'] = $hasadminpower;
                            $arrRender['TabId'] = $TabId;
                            $postData = Yii::$app->request->post();
                            // echo  $postData['from_page']; 
                            if ($postData['from_page'] == "summary") {
                                return [
                                    'success' => true,
                                    'from_page' => 'summary',
                                    'html' => $this->renderPartial('@app/views/tetra/editsummerylabel', [
                                        // 'field' => ['columnname' => $columnname, 'single_edit' => 0, 'uitype' => $uitype,'fieldlabel'=>$fieldlabel,'fieldid'=>$fieldid], // Adjust as needed
                                        'arrRender' => $arrRender,
                                        'Record' => $Record,
                                        'TabId' => $TabId,
                                        'Recordid' => $RecordId,
                                        'ModuleName' => $ModuleName,
                                        'TableName' => $TableName,
                                        'FieldId' => $FieldId,
                                        'hasadminpower' => $hasadminpower,
                                    ])
                                ];
                            } elseif ($postData['from_page'] == "list") {
                                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                                return [
                                    'success' => true,
                                    'from_page' => 'list',
                                ];
                            } elseif ($postData['from_page'] == "multiple") {
                                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                                return [
                                    'success' => true,
                                    'from_page' => 'multiple',
                                    'html' => $this->renderPartial('@app/views/tetra/editsummarymultiple', [
                                        'arrRender' => $arrRender,
                                        'Record' => $Record,
                                        'TabId' => $TabId,
                                        'Recordid' => $RecordId,
                                        'ModuleName' => $ModuleName,
                                        'TableName' => $TableName,
                                        'FieldId' => $FieldId,
                                        'hasadminpower' => $hasadminpower,
                                    ])
                                ];
                            }

                            // }
                            //code ended added by ptpatel on date 17-03-25
                        }
                        //code ended added by ptpatel on date 17-03-25
                        if (!empty($sourceid) && !empty($sourcemodule))
                            return $this->redirect(['detail', 'Record' => $RecordId, 'sourcemodule' => $sourcemodule, 'sourceid' => $sourceid]); // Adjust accordingly
                        //added by ptpatel on date 22-04-25
                        else if ($TabId == 67) {
                            return $this->redirect(['dashboard']);
                        }
                        //end added by ptpatel on date 22-04-25
                        else
                            return $this->redirect(['detail', 'Record' => $RecordId]); // Adjust accordingly
                    } else {
                        // Failed to save - show error page
                        Yii::$app->session->setFlash('error', 'Failed to save data.');

                        $this->layout = '@backend/views/layouts/main-one'; // Use your custom layout
                        return $this->render('@backend/views/site/errorcustom', [
                            'message' => 'Failed to save data. Please check and try again.',
                        ]);
                    }
                    }//single edit else part } close
                } catch (\Throwable $e) {
                    Yii::error("Error in updateModule: " . $e->getMessage(), __METHOD__);
                    Yii::$app->session->setFlash('error', $e->getMessage());

                    $this->layout = '@backend/views/layouts/main-one'; // Use your custom layout
                    return $this->render('@backend/views/site/errorcustom', [
                        'message' => $e->getMessage(),
                    ]);
                }


            }
        } else {
            $model = new EditModel($TableName, $FieldId, $ModuleName, $actionid);
            $model->_members[$FieldId] = $RecordId;
            $arrRender['model'] = $model;
            $ActionList = $model->getActionList($ModuleName);
            $ActionList['ActionName'] = "Edit";
            $this->getClientScript($ModuleName, strtolower($action));

            // echo "<br>Final Else Case";
            list($Column, $Record) = $model->getFieldDetail($rolebasedrecord);
            $arrRender['Record'] = $Record;
            //echo "<pre>"; print_r($arrRender);echo "</pre>";die;
            $arrRender['ColumnList'] = $Column;
            // die;
            //rolebase will be implemented later
            // $Column=$model->getFieldDetail($rolebasedrecord);
            //$Column=$model->getFieldDetail($rolebasedrecord);
            // echo "xdgdf<pre>";
            //print_r($Column);die;
            $arrRender['ColumnList'] = $Column;
            $arrRender['RecordId'] = $RecordId;
            $arrRender['profile'] = $profile;
            $arrRender['uid'] = $id;
            $arrRender['tabs'] = $tabs;
            $arrRender['hasadminpower'] = $hasadminpower;
            $arrRender['TableName'] = $TableName;
            $arrRender['FieldId'] = $FieldId;
            $arrRender['action_name'] = $actionid;
            $arrRender['sourceid'] = $sourceid;
            $arrRender['sourcemodule'] = $sourcemodule;
            $arrRender['TabLabel'] = $TabLabel;
            $arrRender['layout'] = $layout;
            $arrRender['relatedkeys'] = $relatedkeys;
            $arrRender['hasadminpower'] = $hasadminpower;
            $arrRender['TabId'] = $TabId;
            //below line added by ptpatel on date 08-04-25
            $arrRender['roleId'] = $rolebasedrecord['roleid'];

            // 
            // echo "xdgdf<pre>";
            // print_r($arrRender)  ;die;
            // $this->layout = 'main';

            // $this->layout = '@app/views/layouts/main'; 
            // $this->render('@app/views/tetra/EditView',$arrRender);

            $this->layout = '@app/views/layouts/main-new';
            if ($layout == 'contactrole')
                $this->render('@app/views/tetra/ContactroleEditView', $arrRender);
            else if ($layout == "multiple" || $layout == "single") {
                $this->layout = '@app/views/layouts/main-one';
                $this->render('@app/views/tetra/MultiCol', $arrRender);
            } else
                $this->renderPartial('@app/views/tetra/EditView', $arrRender);
        }
        // return $this->render('index');
    }

    public function actionEdititems()
    {
        $sourceid = Yii::$app->request->get('sourceid');
        $sourcemodule = Yii::$app->request->get('sourcemodule');
        $inspectionitems = Yii::$app->request->post('inspectionitems');
        $RecordId = Yii::$app->request->get('Record');
        $inspectionitems = base64_decode($inspectionitems);

        // print_r($_POST);die;
        try {
            $cond = ($inspectionitems == $RecordId) ? 1 : 2;
            // Simulating a request that might fail
            if (!isset($inspectionitems) || $cond > 1) {
                Yii::$app->session->setFlash('error', 'Error: Invalid request');

                throw new BadRequestHttpException('Invalid request');
            }

            // Your normal logic goes here
        } catch (BadRequestHttpException $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw new \yii\web\BadRequestHttpException('Invalid Request');
        }


        //      $ModuleName="Tetra";
        $action = "Edit";
        // $this->getClientScript($ModuleName,$action);
        //              Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/Tetra/jqdt.js', CClientScript::POS_END);        
        $arrRender = array();

        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;
        $TabId = $this->TabId;
        $layout = $this->layout;


        // echo $uid=Yii::$app->session[Yii::$app->params['dirName'].'_id'];die;
        $id = Yii::$app->user->id;
        $uid = Yii::$app->params['dirName'];

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        // print_r($rolebasedrecord);
        $actionid = "edit";
        if (isset($_POST['AddLaptopDetail']))
            $arrRender['showblocks'][] = "2657";
        else if (isset($_POST['AddDesktopDetail']))
            $arrRender['showblocks'][] = "2658";
        else if (isset($_POST['AddTFTDetail']))
            $arrRender['showblocks'][] = "2659";
        else if (isset($_POST['AddGeneralDetail']))
            $arrRender['showblocks'][] = "145";


        $model = new EditModel($TableName, $FieldId, $ModuleName, $actionid);
        $model->_members[$FieldId] = $RecordId;
        $arrRender['model'] = $model;
        $ActionList = $model->getActionList($ModuleName);
        $ActionList['ActionName'] = "Edit";
        $this->getClientScript($ModuleName, strtolower($action));
        //if isset sourceid and sourcemodule check related field name 
        $relatedkeys = $model->getralatedkeys($TabId);

        $arrRender['ActionList'] = $ActionList;
        if (isset($_POST['btncancel'])) {
            $this->redirect(array("$ModuleName/List"));
        } else if (isset($_POST['AddLaptopDetail']) || isset($_POST['AddDesktopDetail']) || isset($_POST['AddTFTDetail']) || isset($_POST['AddGeneralDetail'])) {
            $model = new EditModel($TableName, $FieldId, $ModuleName, $actionid);
            $model->_members[$FieldId] = $RecordId;
            $arrRender['model'] = $model;
            $ActionList = $model->getActionList($ModuleName);
            $ActionList['ActionName'] = "Edit";
            $this->getClientScript($ModuleName, strtolower($action));

            // echo "<br>Final Else Case";
            list($Column, $Record) = $model->getFieldDetail($rolebasedrecord);
            $arrRender['Record'] = $Record;
            //echo "<pre>"; print_r($arrRender);echo "</pre>";die;
            $arrRender['ColumnList'] = $Column;
            // die;
            //rolebase will be implemented later
            // $Column=$model->getFieldDetail($rolebasedrecord);
            //$Column=$model->getFieldDetail($rolebasedrecord);
            // echo "xdgdf<pre>";
            //print_r($Column);die;
            $arrRender['ColumnList'] = $Column;
            $arrRender['RecordId'] = $RecordId;
            $arrRender['profile'] = $profile;
            $arrRender['uid'] = $id;
            $arrRender['tabs'] = $tabs;
            $arrRender['hasadminpower'] = $hasadminpower;
            $arrRender['TableName'] = $TableName;
            $arrRender['FieldId'] = $FieldId;
            $arrRender['action_name'] = $actionid;
            $arrRender['sourceid'] = $sourceid;
            $arrRender['sourcemodule'] = $sourcemodule;
            $arrRender['TabLabel'] = $TabLabel;
            $arrRender['layout'] = $layout;
            $arrRender['relatedkeys'] = $relatedkeys;
            $arrRender['hasadminpower'] = $hasadminpower;
            $arrRender['TabId'] = $TabId;
            //below line added by ptpatel on date 08-04-25
            $arrRender['roleId'] = $rolebasedrecord['roleid'];

            // 
            // echo "xdgdf<pre>";
            // print_r($arrRender)  ;die;
            // $this->layout = 'main';

            // $this->layout = '@app/views/layouts/main'; 
            // $this->render('@app/views/tetra/EditView',$arrRender);

            $this->layout = '@app/views/layouts/main-new';
            if ($layout == 'contactrole')
                $this->render('@app/views/tetra/ContactroleEditView', $arrRender);
            else if ($layout == "multiple" || $layout == "single") {
                $this->layout = '@app/views/layouts/main-one';
                $this->render('@app/views/tetra/MultiCol', $arrRender);
            } else
                $this->renderPartial('@app/views/tetra/EditView', $arrRender);
        } elseif ($this->request->isPost) {
            $tabs = 1;
            if (Yii::$app->request->isPost) {

                //code added by ptpatel
                $singleedit = Yii::$app->request->Post('singleedit') ? 1 : 0;
                try {
                    if ($model->updateModule($RecordId)) {
                        // echo "saved";die;
                        Yii::$app->session->setFlash('success', 'Data saved successfully.');
                        //code added by ptpatel on date 17-03-25
                        // echo $singleedit."save";exit;
                        if ($singleedit) {
                            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                            $arrRender['model'] = $model;
                            $this->getClientScript($ModuleName, strtolower($action));
                            list($Column, $Record) = $model->getFieldDetail($rolebasedrecord);
                            $arrRender['Record'] = $Record;
                            $arrRender['ColumnList'] = $Column;
                            $arrRender['RecordId'] = $RecordId;
                            $arrRender['profile'] = $profile;
                            $arrRender['uid'] = $id;
                            $arrRender['tabs'] = $tabs;
                            $arrRender['hasadminpower'] = $hasadminpower;
                            $arrRender['TableName'] = $TableName;
                            $arrRender['FieldId'] = $FieldId;
                            $arrRender['action_name'] = $actionid;
                            $arrRender['relatedkeys'] = $relatedkeys;
                            $arrRender['hasadminpower'] = $hasadminpower;
                            $arrRender['TabId'] = $TabId;
                            $postData = Yii::$app->request->post();
                            // echo  $postData['from_page']; 
                            if ($postData['from_page'] == "summary") {
                                return [
                                    'success' => true,
                                    'from_page' => 'summary',
                                    'html' => $this->renderPartial('@app/views/tetra/editsummerylabel', [
                                        // 'field' => ['columnname' => $columnname, 'single_edit' => 0, 'uitype' => $uitype,'fieldlabel'=>$fieldlabel,'fieldid'=>$fieldid], // Adjust as needed
                                        'arrRender' => $arrRender,
                                        'Record' => $Record,
                                        'TabId' => $TabId,
                                        'Recordid' => $RecordId,
                                        'ModuleName' => $ModuleName,
                                        'TableName' => $TableName,
                                        'FieldId' => $FieldId,
                                        'hasadminpower' => $hasadminpower,
                                    ])
                                ];
                            } elseif ($postData['from_page'] == "list") {
                                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                                return [
                                    'success' => true,
                                    'from_page' => 'list',
                                ];
                            } elseif ($postData['from_page'] == "multiple") {
                                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                                return [
                                    'success' => true,
                                    'from_page' => 'multiple',
                                    'html' => $this->renderPartial('@app/views/tetra/editsummarymultiple', [
                                        'arrRender' => $arrRender,
                                        'Record' => $Record,
                                        'TabId' => $TabId,
                                        'Recordid' => $RecordId,
                                        'ModuleName' => $ModuleName,
                                        'TableName' => $TableName,
                                        'FieldId' => $FieldId,
                                        'hasadminpower' => $hasadminpower,
                                    ])
                                ];
                            }

                            // }
                            //code ended added by ptpatel on date 17-03-25
                        }
                        //code ended added by ptpatel on date 17-03-25
                        if (!empty($sourceid) && !empty($sourcemodule))
                            return $this->redirect(['detail', 'Record' => $RecordId, 'sourcemodule' => $sourcemodule, 'sourceid' => $sourceid]); // Adjust accordingly
                        //added by ptpatel on date 22-04-25
                        else if ($TabId == 67) {
                            return $this->redirect(['dashboard']);
                        }
                        //end added by ptpatel on date 22-04-25
                        else
                            return $this->redirect(['detail', 'Record' => $RecordId]); // Adjust accordingly
                    } else {
                        // Failed to save - show error page
                        Yii::$app->session->setFlash('error', 'Failed to save data.');

                        $this->layout = '@backend/views/layouts/main-one'; // Use your custom layout
                        return $this->render('@backend/views/site/errorcustom', [
                            'message' => 'Failed to save data. Please check and try again.',
                        ]);
                    }
                } catch (\Throwable $e) {
                    Yii::error("Error in updateModule: " . $e->getMessage(), __METHOD__);
                    Yii::$app->session->setFlash('error', $e->getMessage());

                    $this->layout = '@backend/views/layouts/main-one'; // Use your custom layout
                    return $this->render('@backend/views/site/errorcustom', [
                        'message' => $e->getMessage(),
                    ]);
                }
            }
        }
        // return $this->render('index');
    }

    public function actionConvert()
    {
        $RecordId = Yii::$app->request->get('Record');
        // $RecordId = base64_decode($RecordId);
        //print_r($_SESSION);die;
        //      $ModuleName="Tetra";
        $action = "Create";
        // $this->getClientScript($ModuleName,$action);
        //              Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/Tetra/jqdt.js', CClientScript::POS_END);        
        $arrRender = array();

        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;
        $TabId = $this->TabId;
        $layout = $this->layout;


        // echo $uid=Yii::$app->session[Yii::$app->params['dirName'].'_id'];die;
        $id = Yii::$app->user->id;
        $uid = Yii::$app->params['dirName'];

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        // print_r($rolebasedrecord);
        $actionid = "edit";
        $model = new EditModel($TableName, $FieldId, $ModuleName, $actionid);
        $model->_members[$FieldId] = $RecordId;
        $arrRender['model'] = $model;
        $ActionList = $model->getActionList($ModuleName);
        $ActionList['ActionName'] = "Edit";
        $this->getClientScript($ModuleName, strtolower($action));
        //if isset sourceid and sourcemodule check related field name 
        $relatedkeys = $model->getralatedkeys($TabId);

        $arrRender['ActionList'] = $ActionList;
        if (isset($_POST['btncancel'])) {
            $this->redirect(array("$ModuleName/List"));
        } elseif ($this->request->isPost) {
            // print_r($_POST);die;
            $tabs = 1;
            if (Yii::$app->request->isPost) {
                try {
                    if ($model->updateModule($RecordId)) {
                        //echo "saved";die;
                        Yii::$app->session->setFlash('success', 'Data saved successfully.');

                        return $this->redirect(['detail', 'Record' => $RecordId]); // Adjust accordingly
                    } else {
                        // Failed to save - show error page
                        Yii::$app->session->setFlash('error', 'Failed to save data.');

                        $this->layout = '@backend/views/layouts/main-one'; // Use your custom layout
                        return $this->render('@backend/views/site/errorcustom', [
                            'message' => 'Failed to save data. Please check and try again.',
                        ]);
                    }
                } catch (\Throwable $e) {
                    Yii::error("Error in updateModule: " . $e->getMessage(), __METHOD__);
                    Yii::$app->session->setFlash('error', $e->getMessage());

                    $this->layout = '@backend/views/layouts/main-one'; // Use your custom layout
                    return $this->render('@backend/views/site/errorcustom', [
                        'message' => $e->getMessage(),
                    ]);
                }
            }
        } else {

            $model = new EditModel($TableName, $FieldId, $ModuleName, $actionid);
            $model->_members[$FieldId] = $RecordId;
            $arrRender['model'] = $model;
            $ActionList = $model->getActionList($ModuleName);
            $ActionList['ActionName'] = "Edit";
            $this->getClientScript($ModuleName, strtolower($action));

            // echo "<br>Final Else Case";
            list($Column, $Record) = $model->getFieldDetail($rolebasedrecord);
            $arrRender['Record'] = $Record;
            // echo "<pre>"; print_r($Record);echo "</pre>";die;
            // echo $Record['vendor'];die;
            //get vendor detail
            $opporname = '';
            $category = $Record['category'];
            //get category
            $sql_cat = "SELECT lead_category_value as category_value FROM `lead_category`  where  lead_category_id = :category";
            $cat = Yii::$app->db->createCommand($sql_cat)->bindValue(":category", $category)->queryOne();
            if ($cat)
                $category_value = $cat['category_value'];
            else
                $category_value = '';
            // echo $category_value;die;
            if (!empty($Record['vendor'])) {
                // echo $Record['vendor'];die;
                $vend = Yii::$app->db->createCommand("Select acc_name from vendor_account where vendoraccid=:id")->bindValue(":id", $Record['vendor'])->queryOne();
                if ($vend)
                    $opporname = $vend['acc_name'] . "/" . $category_value . '/' . date("dmY");
            } else
                $opporname = $Record['account_name'] . "/" . $category_value . '/' . date("dmY");
            // echo $opporname;die;
            //compare if cotactexist
            $cont = Yii::$app->db->createCommand("Select * from contacts where mobile=:mobile and deleted = 0 and is_temp=0")->bindValue(":mobile", $Record['phone'])->queryOne();
            if (!empty($cont)) {
                $first_name = $cont['first_name'];
                $last_name = $cont['last_name'];
                $email = $cont['email'];
                $mobile = $cont['mobile'];
                $contacts_id = $cont['contacts_id'];
            } else {
                $first_name = '';
                $last_name = '';
                $contacts_id = '';
                $email = '';
                $mobile = '';
            }

            $arrRender['ColumnList'] = $Column;
            // die;
            //rolebase will be implemented later
            // $Column=$model->getFieldDetail($rolebasedrecord);
            //$Column=$model->getFieldDetail($rolebasedrecord);
            // echo "xdgdf<pre>";
            //print_r($Column);die;
            $arrRender['ColumnList'] = $Column;
            $arrRender['RecordId'] = $RecordId;
            $arrRender['profile'] = $profile;
            $arrRender['uid'] = $id;
            $arrRender['tabs'] = $tabs;
            $arrRender['hasadminpower'] = $hasadminpower;
            $arrRender['TableName'] = $TableName;
            $arrRender['FieldId'] = $FieldId;
            $arrRender['action_name'] = $actionid;
            $arrRender['TabLabel'] = $TabLabel;
            $arrRender['layout'] = $layout;
            $arrRender['relatedkeys'] = $relatedkeys;
            $arrRender['hasadminpower'] = $hasadminpower;
            $arrRender['opporname'] = $opporname;
            $arrRender['first_name'] = $first_name;
            $arrRender['last_name'] = $last_name;
            $arrRender['email'] = $email;
            $arrRender['mobile'] = $mobile;
            $arrRender['contacts_id'] = $contacts_id;

            // 
            // echo "xdgdf<pre>";
            // print_r($arrRender)  ;die;
            // $this->layout = 'main';

            // $this->layout = '@app/views/layouts/main'; 
            // $this->render('@app/views/tetra/EditView',$arrRender);

            // $this->layout = '@app/views/layouts/main-new';
            $this->layout = '@app/views/layouts/main-one';
            $this->renderPartial('@app/views/tetra/ConvertView', $arrRender);
        }
        // return $this->render('index');
    }

    public function actionGetproductlist()
    {
        $block_id = filter_var(Yii::$app->request->get('blockid'), FILTER_SANITIZE_NUMBER_INT);
        $cnt_rows = filter_var(Yii::$app->request->get('cnt_rows'), FILTER_SANITIZE_NUMBER_INT);
        //added on 23 june 2025 by deepika
        $sourceid = filter_var(Yii::$app->request->get('sourceid'), FILTER_SANITIZE_NUMBER_INT);
        $sourcemodule = filter_var(Yii::$app->request->get('sourcemodule'), FILTER_SANITIZE_NUMBER_INT);
        //end on 23 june by deepika
        //print_r($_SESSION);die;
        //   	$ModuleName="Tetra";
        $action = "Edit";
        // $this->getClientScript($ModuleName,$action);
        //              Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/Tetra/jqdt.js', CClientScript::POS_END);		
        $arrRender = array();

        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;
        $layout = $this->layout;



        // echo $uid=Yii::$app->session[Yii::$app->params['dirName'].'_id'];die;
        $id = Yii::$app->user->id;
        $uid = Yii::$app->params['dirName'];

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $hasadminpower = $model->hasadminpower($profile);
        $modulepermission = $model->modulepermission($profile, $tabs);
        // print_r($rolebasedrecord);
        $actionid = "create";



        $Block = new Blocks;
        $Block->blockid = $block_id;
        $BlockDetail = $Block->getBlockDetail($ModuleName);


        // echo "xdgdf<pre>";
        // print_r($Column);die;
        //added on 23 june 2025 by deepika
        $arrRender['sourceid'] = $sourceid;
        $arrRender['sourcemodule'] = $sourcemodule;
        //added on 23 june 2025 by deepika

        $arrRender['block'] = $BlockDetail;
        $arrRender['profile'] = $profile;
        $arrRender['uid'] = $id;
        $arrRender['tabs'] = $tabs;
        $arrRender['hasadminpower'] = $hasadminpower;
        $arrRender['TableName'] = $TableName;
        $arrRender['FieldId'] = $FieldId;
        $arrRender['action_name'] = $actionid;
        $arrRender['TabLabel'] = $TabLabel;
        $arrRender['ModuleName'] = $ModuleName;
        $arrRender['cnt_rows'] = $cnt_rows;
        // 
        // echo "xdgdf<pre>";
        // print_r($arrRender)	;die;
        // $this->layout = 'main';

        // $this->layout = '@app/views/layouts/main'; 
        // $this->render('@app/views/tetra/EditView',$arrRender);

        $this->layout = '@app/views/layouts/main-new';
        // $this->render('@app/views/tetra/EditView-old',$arrRender);
        // echo $layout;die;

        $this->renderAjax('@app/views/tetra/Productlist', $arrRender);

        // return $this->render('index');
    }
    public function actionQuickcreatepopup()
    {
        //print_r($_SESSION);die;
        //    $ModuleName="Tetra";
        $action = "Edit";
        // $this->getClientScript($ModuleName,$action);
        //              Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/Tetra/jqdt.js', CClientScript::POS_END);      
        $arrRender = array();

        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;


        // echo $uid=Yii::$app->session[Yii::$app->params['dirName'].'_id'];die;
        $id = Yii::$app->user->id;
        $uid = Yii::$app->params['dirName'];

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        // print_r($rolebasedrecord);
        $actionid = "quickcreate";
        $model = new EditModel($TableName, $FieldId, $ModuleName, $actionid);
        $arrRender['model'] = $model;
        $ActionList = $model->getActionList($ModuleName);
        $ActionList['ActionName'] = "Create";
        $this->getClientScript($ModuleName, strtolower($action));

        $arrRender['ActionList'] = $ActionList;
        if (isset($_POST['btncancel'])) {
            $this->redirect(array("$ModuleName/List"));
        } elseif (isset($_POST['savemodule'])) {
            $tabs = 1;
            if (Yii::$app->request->isPost) {
                $result = $model->saveModule($tabs);

                if ($result) {
                    // echo "saved";die;
                    $this->redirect(array("$ModuleName/List"));
                    Yii::$app->session->setFlash('success', 'Data saved successfully.');
                    return $this->redirect(['view', 'id' => $model->id]); // Adjust accordingly
                } else {
                    echo "Failed";
                    die;
                    Yii::$app->session->setFlash('error', 'Failed to save data.');
                }
            }
        } else {

            // echo "<br>Final Else Case";
            // die;
            //rolebase will be implemented later
            // $Column=$model->getFieldDetail($rolebasedrecord);
            $Column = $model->getFieldDetail($rolebasedrecord);
            // echo "xdgdf<pre>";
            // print_r($Column);die;
            $arrRender['ColumnList'] = $Column;
            $arrRender['profile'] = $profile;
            $arrRender['uid'] = $id;
            $arrRender['tabs'] = $tabs;
            $arrRender['hasadminpower'] = $hasadminpower;
            $arrRender['TableName'] = $TableName;
            $arrRender['FieldId'] = $FieldId;
            $arrRender['TabLabel'] = $TabLabel;


            // echo "xdgdf<pre>";
            // print_r($arrRender)  ;die;
            // $this->layout = 'main';

            // $this->layout = '@app/views/layouts/main'; 
            // $this->render('@app/views/tetra/EditView',$arrRender);

            $this->layout = '@app/views/layouts/main-new';
            $this->renderPartial('@app/views/tetra/QuickEditView', $arrRender);
        }
        // return $this->render('index');
    }
    public function actionIndex()
    {
        return "This is a common controller!";
    }


    public function actionValidatepassword()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $currentPassword = Yii::$app->request->post('current_password');
        $user = Yii::$app->user->identity; // Get the current logged-in user

        if (!Yii::$app->security->validatePassword($currentPassword, $user->password_hash)) {
            return [
                'status' => 'error',
                'message' => 'The current password you entered is incorrect.',
            ];
        }

        return [
            'status' => 'success',
            'message' => 'Password is correct.',
        ];
    }



    public function actionDupvalidationuser()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $status = [];


        if (isset($_POST['email'])) {
            $email = Yii::$app->request->post('email');
            // Email validation
            $exists = User::find()->where(['email' => $email])->exists();
            if ($exists) {
                $status[] = 'email_error';
            }
        }
        if (isset($_POST['username'])) {
            $username = Yii::$app->request->post('username');


            // Username validation
            $exists = User::find()->where(['username' => $username])->exists();
            if ($exists) {
                $status[] = 'username_error';
            }
        }

        if (!empty($status)) {
            return ['status' => implode(',', $status), 'message' => 'Validation failed.'];
        }

        return ['status' => 'success', 'message' => 'Validation successful'];
    }

    public function actionList()
    {
        $sourceid = Yii::$app->request->get('sourceid');
        $sourcemodule = Yii::$app->request->get('sourcemodule');
        // $filterid added for widget filter data by ptpatel on date 26-05-25
        $filterid = Yii::$app->request->get('widgetidid');
        // Get pagination parameters from the request
        $start = Yii::$app->request->get('start', 0); // Start index, default to 0
        $limit = Yii::$app->request->get('limit', 10); // Limit (number of records), default to 10

        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;
        $layout = $this->layout;


        $model = new AccessCheck();
        $id = Yii::$app->user->id;
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        // print_r($rolebasedrecord);die;
        $hasadminpower = $model->hasadminpower($profile);
        $modulepermission = $model->modulepermission($profile, $tabs);
        // echo $ModuleName;die;
        $createpermission = $model->checkpermission($id, $ModuleName, 'create');
        $editpermission = $model->checkpermission($id, $ModuleName, 'edit');
        $deletepermission = $model->checkpermission($id, $ModuleName, 'delete');
        $listpermission = $model->checkpermission($id, $ModuleName, 'list');
        $detailpermission = $model->checkpermission($id, $ModuleName, 'detail');
        $approvepermission = $model->checkpermission($id, $ModuleName, 'approvelist');
        $importpermission = $model->checkpermission($id, $ModuleName, 'import');
        $exportpermission = $model->checkpermission($id, $ModuleName, 'export');

        //get admin ids
        $adminowners = $model->getadminids($id, $profile);

        $arrRender = array();

       

        //added on 23/12/2024 to save user filter
        // Get custom view ID for the user and the module
        $userColumns = (new \yii\db\Query())
            ->select(['cvid'])
            ->from('customview')
            ->where(['entitytype' => $ModuleName])
            ->andWhere(['userid' => Yii::$app->user->id])
            ->column();
        //    print_r($userColumns);die;
        if (empty($userColumns)) {
            //get admin cvid 
            $adminColumns = (new \yii\db\Query())
                ->select(['cvid'])
                ->from('customview')
                ->where(['entitytype' => $ModuleName])
                ->andWhere(['userid' => 1])
                ->column();
                
            $admincvid = $adminColumns[0];

            // If no custom view exists for the user, create a new one
            Yii::$app->db->createCommand("
                   INSERT INTO `customview` 
                   SET viewname = 'All', setdefault = 1, setmetrics = 0, entitytype = :ModuleName, 
                       userid = :uid, tabid = :TabId, status = 0")
                ->bindValue(":ModuleName", ucfirst($ModuleName))
                ->bindValue(":TabId", $TabId)
                ->bindValue(":uid", Yii::$app->user->id)
                ->execute();

            // Get the last inserted `cvid`
            $cvid = Yii::$app->db->getLastInsertID();
            Yii::$app->db->createCommand("
               INSERT INTO cvcolumnlist (cvid, columnindex, columnname, fieldid)
               SELECT $cvid, columnindex, columnname, fieldid
               FROM cvcolumnlist
               WHERE cvid = $admincvid")
                ->execute();
        } else {
            $cvid = $userColumns[0];
        }
        // Get the columns saved for the current user
        $savedColumns = (new \yii\db\Query())
            ->select(['fieldid'])
            ->from('cvcolumnlist')
            ->where(['cvid' => $cvid])
            ->column();

 
        // Get all columns for the 'leaddetails' table
        $columns = (new \yii\db\Query())
            ->select(['columnname', 'columnname AS field', 'fieldlabel AS headerName', 'fieldid'])
            ->from('field')
            ->where(['tabid' => $TabId, 'list_view' => 1])
            ->orderBy(['sequence' => SORT_ASC])
            ->all();
        // print_r($columns);die;

         //get columns for column selector added on 01 Oct 2025
        //get table name from tab table
        $maintable = (new \yii\db\Query())
            ->select(['tablename'])
            ->from('tab')
            ->where(['tabid' => $TabId])
            ->one();
        $maintablename = $maintable['tablename'];

        $columnselector = (new \yii\db\Query())
            ->select(['columnname', 'columnname AS field', 'fieldlabel AS headerName', 'fieldid'])
            ->from('field')
            ->where(['tabid' => $TabId, 'list_view' => 1,'tablename'=>$maintablename])
            ->orderBy(['sequence' => SORT_ASC])
            ->all();
          
         // Set visibility based on whether column is in saved columns
        foreach ($columnselector as &$column) {
            $column['visible'] = in_array($column['fieldid'], $savedColumns);
        }
        
        // Set visibility based on whether column is in saved columns
        foreach ($columns as &$column) {
            $column['visible'] = in_array($column['fieldid'], $savedColumns);
        }
        $model = new ListModel($TableName, $FieldId, $ModuleName);
        $filed_name = $model->getfilterColumnList();
        $kanbnacolumn = $model->getKanbanList();

        if ($kanbnacolumn) {
            // print_r($kanbnacolumn);die;
            //fetch from picklist
            $fieldid = $kanbnacolumn['fieldid'];
            $PickListDetail = $model->getPickListDetail($fieldid);
            $targettable = $PickListDetail['targettable'];
            $targetfield = $PickListDetail['targetfield'];
            $dispfield = $PickListDetail['dispfield'];
            $kanbanstatus = (new \yii\db\Query())
                ->select([$targetfield, $dispfield])
                ->from($targettable)
                ->where(['is_active' => 1])
                // ->where(['>', 'pipeline_status', 0])
                ->orderBy(['seq_no' => SORT_ASC])
                ->all();
            // code added by ptpatel on date 07-04-25
            $eachStatusCounts = (new \yii\db\Query())
                ->select([
                    'ls.' . $targetfield,
                    'ls.' . $dispfield . ',
                    COUNT(li.' . $kanbnacolumn['fieldname'] . ') AS total'
                ])
                ->from(['li' => $TableName])
                ->leftJoin(['ls' => $targettable], 'ls.' . $targetfield . ' = li.' . $kanbnacolumn['fieldname'])
                ->groupBy('li.' . $kanbnacolumn['fieldname'])
                ->all();
            //end code added by ptpatel on date 07-04-25
            $arrRender['kanbnacolumn'] = $kanbnacolumn['fieldname'];
            $arrRender['leadStatuses'] = $kanbanstatus;
            $arrRender['kanbanstatusid'] = $targetfield;
            $arrRender['kanbanstatusvalue'] = $dispfield;
            $arrRender['kanbancolumn'] = $kanbnacolumn;

            $ActionList = $model->getActionList($ModuleName);
            $ActionList['OrderBy'] = ''; //Yii::$app->request->get('OrderBy');
            $ActionList['SortOrder'] = ''; //Yii::$app->request->get('SortOrder');
            $curPageNo = ''; //$_REQUEST['pagejump'];
            // list($ColumnList, $RecordList, $totalitemcount) = $model->getkanbanListRecord($ActionList['OrderBy'], $ActionList['SortOrder'], $rolebasedrecord, $modulepermission);
            list($ColumnList, $RecordList, $totalitemcount) = $model->getListRecord($ActionList['OrderBy'], $ActionList['SortOrder'], $rolebasedrecord, $modulepermission);
            $arrRender['leadInformation'] = $RecordList;
            //added by ptpatel on date 07-04-25 to show labels
            $arrRender['ColumnList'] = $ColumnList;
            $arrRender['eachStatusCounts'] = $eachStatusCounts;
            //end added by ptpatel on date 07-04-25 to show labels
            // echo "<pre>";print_r($kanbanstatus);die;
            // echo "<pre>";print_r($kanbanstatus);die;
            // print_r($arrRender['leadInformation']);die;
        }

        // Get all filters for the current tab and user, fallback to userid = 1
        $allfilter = (new \yii\db\Query())
            ->select(['*'])
            ->from('default_filter')
            ->where(['tabid' => $TabId])
            ->andWhere(['or', ['userid' => $id], ['userid' => 1]])  // Include filters for logged-in user and fallback to userid = 1
            ->andWhere(['!=','deleted',1]) 
            ->all();

        // Get the default filter for the current tab and user, fallback to userid = 1
        $defaultfilter = (new \yii\db\Query())
            ->select(['*'])
            ->from('default_filter')
            ->where(['tabid' => $TabId])
            ->andWhere(['or', ['userid' => $id], ['userid' => 1]])  // Include default filter for logged-in user and fallback to userid = 1
            ->one();

        // Initialize default filter condition
        $defaultfiltercondition = '';
        if (!empty($defaultfilter)) {
            // Fetch condition from the child table dynamically for the logged-in user or fallback to userid = 1
            $defaultfiltercondition = (new \yii\db\Query())
                ->select(['user_filter.*', 'field.*'])  // Select fields from both tables
                ->from('user_filter')
                ->join('INNER JOIN', 'field', 'field.fieldid = user_filter.fieldid')  // Explicit INNER JOIN
                ->where(['and', ['user_filter.deleted' => 0], ['user_filter.filter_id' => $defaultfilter['id']]])  // Specify table for filter_id
                ->andWhere(['or', ['user_filter.userid' => $id], ['user_filter.userid' => 1]])  // Include conditions for logged-in user or fallback to userid = 1
                ->one();
            if ($defaultfiltercondition) {
                if ($defaultfiltercondition['uitype'] == 8) {
                    //get piklist options
                    //get field detail from field table
                    $sql = "select fieldid from field where fieldname = :fieldname and tablename = :fieldtablename and uitype = :fielduitype";
                    $command = Yii::$app->db->createCommand($sql)
                        ->bindValue(':fieldname', $defaultfiltercondition['columnname'])
                        ->bindValue(':fieldtablename', $defaultfiltercondition['tablename'])
                        ->bindValue(':fielduitype', $defaultfiltercondition['uitype'])
                        ->queryOne();
                    // print_r($command);die;
                    if ($command) {
                        //get valuew from picklist
                        $PickList = new Picklist;
                        $PickList->fieldid = $command['fieldid'];
                        // $Column->blocks[$BlockKey]->fields[$FieldKey]->fieldoptions=$PickList->getPickListOption($table_name);


                        $fieldoptions = $PickList->getPickListOption($ModuleName);
                        // print_r($fieldoptions);die;
                        $opt = '<select id="filterValue" class="form-control">';
                        foreach ($fieldoptions as $key => $value) {
                            if ($defaultfiltercondition['userinput'] == $key)
                                $sel = "selected";
                            else
                                $sel = '';

                            # code...
                            $opt .= '<option class="opt-none" ' . $sel . ' value="' . $key . '">' . $value . '</option>';
                        }

                        $opt .= '</select>';
                    } else {
                        $opt = '';
                    }
                    $defaultfiltercondition['opt'] = $opt;
                }
            }
        }
        $connection = Yii::$app->db;
        $command = $connection->createCommand("
        SELECT fieldlabel,columnname, tablename, mandatory 
        FROM `field` 
        WHERE import = :import AND tabid = :tabid
    ");
        $command->bindValue(':import', 1);
        $command->bindValue(':tabid', $TabId);

        // Execute query to fetch columns
        $columnsIMP = $command->queryAll();
        $massedit = $model->getMassEditColumnList();
        // echo "<pre>";
        // print_r($massedit);die;

        /////get related detail//////////
        $srcheaderfullname = '';
        $sourcemodule = Yii::$app->request->get('sourcemodule');
        $sourceid = Yii::$app->request->get('sourceid');
        if (!empty($sourceid) && !empty($sourcemodule)) {
            $modeledit = new EditModel($TableName, $FieldId, $ModuleName, 'edit');
            // get header column
            $header = $modeledit->getHeaderDetail($sourcemodule);
            $srcheaderfullname = '';
            //print_r($header);die;
            if (!empty($header) && isset($header['columns'])) {
                //get related tblename
                $arrtab = Yii::$app->db
                    ->createCommand("SELECT tablename FROM field where tabid=$sourcemodule limit 1")
                    // ->bindValue(":TableName", $TableName)
                    ->queryOne();
                $relatedTableName = $arrtab['tablename'];


                // // Get the table schema
                $tableSchema = Yii::$app->db->schema->getTableSchema($relatedTableName);

                // // Check if the table exists and has a primary key
                if ($tableSchema !== null && !empty($tableSchema->primaryKey)) {
                    // echo "Primary key column(s) for table '$relatedTableName': " . implode(', ', $tableSchema->primaryKey);
                    $retFieldId = implode(', ', $tableSchema->primaryKey);
                    if ($retFieldId) {
                        $arr_tab = Yii::$app->db
                            ->createCommand("SELECT CONCAT_WS(' ', " . $header['columns'] . ") AS full_name FROM $relatedTableName where $retFieldId=$sourceid")
                            // ->bindValue(":TableName", $TableName)
                            ->queryOne();
                        // print_r($arr_tab);die;

                        $srcheaderfullname = $arr_tab['full_name'];
                    }
                }
            }
        }
        $is_import_allowed = Tab::find()
            ->select('import_allowed')
            ->where(['tabid' => $TabId])
            ->scalar();

        // widgetfilterid used for filter widget
        $arrRender['adminowners'] = $adminowners;
        // widgetfilterid used for filter widget data
        $arrRender['widgetfilterid'] = $filterid;
        $arrRender['allfilter'] = $allfilter;
        $arrRender['defaultfilter'] = $defaultfilter;
        $arrRender['defaultfiltercondition'] = $defaultfiltercondition;
        $arrRender['ModuleName'] = $ModuleName;
        $arrRender['Tabname'] = ucfirst($ModuleName);
        $arrRender['columns'] = $columns;
        $arrRender['columnselector']=$columnselector;
        $arrRender['filed_name'] = $filed_name;
        $arrRender['massedit'] = $massedit;
        $arrRender['TabLabel'] = $TabLabel;
        $arrRender['TabId'] = $TabId;
        $arrRender['DataImport'] = $columnsIMP;
        $arrRender['layout'] = $layout;
        $arrRender['srcheaderfullname'] = $srcheaderfullname;
        //added byptpatel
        $arrRender['TableName'] = $TableName;
        $arrRender['createpermission'] = $createpermission;
        $arrRender['editpermission'] = $editpermission;
        $arrRender['deletepermission'] = $deletepermission;
        $arrRender['detailpermission'] = $detailpermission;
        $arrRender['listpermission'] = $listpermission;
        $arrRender['approvepermission'] = $approvepermission;
        $arrRender['importpermission'] = $importpermission;
        $arrRender['exportpermission'] = $exportpermission;
        $arrRender['isimportallowed'] = $is_import_allowed;

        $this->layout = '@app/views/layouts/main-one';
        if ($layout == 'contactrole')
            $this->render('@app/views/tetra/contactroleview', $arrRender);
        else
            $this->render('@app/views/tetra/listview', $arrRender);
    }

    public function actionApprovelist()
    {
        $sourceid = Yii::$app->request->get('sourceid');
        $sourcemodule = Yii::$app->request->get('sourcemodule');
        // Get pagination parameters from the request
        $start = Yii::$app->request->get('start', 0); // Start index, default to 0
        $limit = Yii::$app->request->get('limit', 10); // Limit (number of records), default to 10

        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;
        $layout = $this->layout;


        $model = new AccessCheck();
        $id = Yii::$app->user->id;
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $modulepermission = $model->modulepermission($profile, $tabs);
        $arrRender = array();

        //added on 23/12/2024 to save user filter
        // Get custom view ID for the user and the module
        $userColumns = (new \yii\db\Query())
            ->select(['cvid'])
            ->from('customview')
            ->where(['entitytype' => $ModuleName])
            ->andWhere(['userid' => Yii::$app->user->id])
            ->column();
        //    print_r($userColumns);die;
        if (empty($userColumns)) {
            //get admin cvid 
            $adminColumns = (new \yii\db\Query())
                ->select(['cvid'])
                ->from('customview')
                ->where(['entitytype' => $ModuleName])
                ->andWhere(['userid' => 1])
                ->column();
            $admincvid = $adminColumns[0];

            // If no custom view exists for the user, create a new one
            Yii::$app->db->createCommand("
                   INSERT INTO `customview` 
                   SET viewname = 'All', setdefault = 1, setmetrics = 0, entitytype = :ModuleName, 
                       userid = :uid, tabid = :TabId, status = 0")
                ->bindValue(":ModuleName", ucfirst($ModuleName))
                ->bindValue(":TabId", $TabId)
                ->bindValue(":uid", Yii::$app->user->id)
                ->execute();

            // Get the last inserted `cvid`
            $cvid = Yii::$app->db->getLastInsertID();
            Yii::$app->db->createCommand("
               INSERT INTO cvcolumnlist (cvid, columnindex, columnname, fieldid)
               SELECT $cvid, columnindex, columnname, fieldid
               FROM cvcolumnlist
               WHERE cvid = $admincvid")
                ->execute();
        } else {
            $cvid = $userColumns[0];
        }
        // Get the columns saved for the current user
        $savedColumns = (new \yii\db\Query())
            ->select(['fieldid'])
            ->from('cvcolumnlist')
            ->where(['cvid' => $cvid])
            ->column();


        // Get all columns for the 'leaddetails' table
        $columns = (new \yii\db\Query())
            ->select(['columnname', 'columnname AS field', 'fieldlabel AS headerName', 'fieldid'])
            ->from('field')
            ->where(['tabid' => $TabId, 'list_view' => 1])
            ->orderBy(['sequence' => SORT_ASC])
            ->all();
        // print_r($columns);die;

        // Set visibility based on whether column is in saved columns
        foreach ($columns as &$column) {
            $column['visible'] = in_array($column['fieldid'], $savedColumns);
        }
        $model = new ApproveListModel($TableName, $FieldId, $ModuleName);
        $filed_name = $model->getfilterColumnList();
        $kanbnacolumn = $model->getKanbanList();

        if ($kanbnacolumn) {
            // print_r($kanbnacolumn);die;
            //fetch from picklist
            $fieldid = $kanbnacolumn['fieldid'];
            $PickListDetail = $model->getPickListDetail($fieldid);
            $targettable = $PickListDetail['targettable'];
            $targetfield = $PickListDetail['targetfield'];
            $dispfield = $PickListDetail['dispfield'];
            $kanbanstatus = (new \yii\db\Query())
                ->select([$targetfield, $dispfield])
                ->from($targettable)
                ->where(['is_active' => 1])
                // ->where(['>', 'pipeline_status', 0])
                ->orderBy(['seq_no' => SORT_ASC])
                ->all();
            // print_r($kanbanstatus);die;
            $arrRender['kanbnacolumn'] = $kanbnacolumn['fieldname'];
            $arrRender['leadStatuses'] = $kanbanstatus;
            $arrRender['kanbanstatusid'] = $targetfield;
            $arrRender['kanbanstatusvalue'] = $dispfield;
            $arrRender['kanbancolumn'] = $kanbnacolumn;

            $ActionList = $model->getActionList($ModuleName);
            $ActionList['OrderBy'] = ''; //Yii::$app->request->get('OrderBy');
            $ActionList['SortOrder'] = ''; //Yii::$app->request->get('SortOrder');
            $curPageNo = ''; //$_REQUEST['pagejump'];
            list($ColumnList, $RecordList, $totalitemcount) = $model->getListRecord($ActionList['OrderBy'], $ActionList['SortOrder'], $rolebasedrecord, $modulepermission);
            $arrRender['leadInformation'] = $RecordList;
            //below line added by ptpatel on date 29-03-25
            $arrRender['totalitemcount'] = $totalitemcount;
            // print_r($arrRender['leadInformation']);die;
        }

        // Get all filters for the current tab and user, fallback to userid = 1
        $allfilter = (new \yii\db\Query())
            ->select(['*'])
            ->from('default_filter')
            ->where(['tabid' => $TabId])
            ->andWhere(['or', ['userid' => $id], ['userid' => 1]])  // Include filters for logged-in user and fallback to userid = 1
            ->all();

        // Get the default filter for the current tab and user, fallback to userid = 1
        $defaultfilter = (new \yii\db\Query())
            ->select(['*'])
            ->from('default_filter')
            ->where(['tabid' => $TabId])
            ->andWhere(['or', ['userid' => $id], ['userid' => 1]])  // Include default filter for logged-in user and fallback to userid = 1
            ->one();

        // Initialize default filter condition
        $defaultfiltercondition = '';
        if (!empty($defaultfilter)) {
            // Fetch condition from the child table dynamically for the logged-in user or fallback to userid = 1
            $defaultfiltercondition = (new \yii\db\Query())
                ->select(['user_filter.*', 'field.*'])  // Select fields from both tables
                ->from('user_filter')
                ->join('INNER JOIN', 'field', 'field.fieldid = user_filter.fieldid')  // Explicit INNER JOIN
                ->where(['and', ['user_filter.deleted' => 0], ['user_filter.filter_id' => $defaultfilter['id']]])  // Specify table for filter_id
                ->andWhere(['or', ['user_filter.userid' => $id], ['user_filter.userid' => 1]])  // Include conditions for logged-in user or fallback to userid = 1
                ->one();
            if ($defaultfiltercondition) {
                if ($defaultfiltercondition['uitype'] == 8) {
                    //get piklist options
                    //get field detail from field table
                    $sql = "select fieldid from field where fieldname = :fieldname and tablename = :fieldtablename and uitype = :fielduitype";
                    $command = Yii::$app->db->createCommand($sql)
                        ->bindValue(':fieldname', $defaultfiltercondition['columnname'])
                        ->bindValue(':fieldtablename', $defaultfiltercondition['tablename'])
                        ->bindValue(':fielduitype', $defaultfiltercondition['uitype'])
                        ->queryOne();
                    // print_r($command);die;
                    if ($command) {
                        //get valuew from picklist
                        $PickList = new Picklist;
                        $PickList->fieldid = $command['fieldid'];
                        // $Column->blocks[$BlockKey]->fields[$FieldKey]->fieldoptions=$PickList->getPickListOption($table_name);


                        $fieldoptions = $PickList->getPickListOption($ModuleName);
                        // print_r($fieldoptions);die;
                        $opt = '<select id="filterValue" class="form-control">';
                        foreach ($fieldoptions as $key => $value) {
                            if ($defaultfiltercondition['userinput'] == $key)
                                $sel = "selected";
                            else
                                $sel = '';

                            # code...
                            $opt .= '<option class="opt-none" ' . $sel . ' value="' . $key . '">' . $value . '</option>';
                        }

                        $opt .= '</select>';
                    } else {
                        $opt = '';
                    }
                    $defaultfiltercondition['opt'] = $opt;
                }
            }
        }
        $connection = Yii::$app->db;
        $command = $connection->createCommand("
        SELECT fieldlabel,columnname, tablename, mandatory 
        FROM `field` 
        WHERE import = :import AND tabid = :tabid
    ");
        $command->bindValue(':import', 1);
        $command->bindValue(':tabid', $TabId);

        // Execute query to fetch columns
        $columnsIMP = $command->queryAll();
        $massedit = $model->getMassEditColumnList();
        // echo "<pre>";
        // print_r($massedit);die;

        /////get related detail//////////
        $srcheaderfullname = '';
        $sourcemodule = Yii::$app->request->get('sourcemodule');
        $sourceid = Yii::$app->request->get('sourceid');
        if (!empty($sourceid) && !empty($sourcemodule)) {
            $modeledit = new EditModel($TableName, $FieldId, $ModuleName, 'edit');
            // get header column
            $header = $modeledit->getHeaderDetail($sourcemodule);
            $srcheaderfullname = '';
            //print_r($header);die;
            if (!empty($header) && isset($header['columns'])) {
                //get related tblename
                $arrtab = Yii::$app->db
                    ->createCommand("SELECT tablename FROM field where tabid=$sourcemodule limit 1")
                    // ->bindValue(":TableName", $TableName)
                    ->queryOne();
                $relatedTableName = $arrtab['tablename'];


                // // Get the table schema
                $tableSchema = Yii::$app->db->schema->getTableSchema($relatedTableName);

                // // Check if the table exists and has a primary key
                if ($tableSchema !== null && !empty($tableSchema->primaryKey)) {
                    // echo "Primary key column(s) for table '$relatedTableName': " . implode(', ', $tableSchema->primaryKey);
                    $retFieldId = implode(', ', $tableSchema->primaryKey);
                    if ($retFieldId) {
                        $arr_tab = Yii::$app->db
                            ->createCommand("SELECT CONCAT_WS(' ', " . $header['columns'] . ") AS full_name FROM $relatedTableName where $retFieldId=$sourceid")
                            // ->bindValue(":TableName", $TableName)
                            ->queryOne();
                        // print_r($arr_tab);die;

                        $srcheaderfullname = $arr_tab['full_name'];
                    }
                }
            }
        }

        $arrRender['allfilter'] = $allfilter;
        $arrRender['defaultfilter'] = $defaultfilter;
        $arrRender['defaultfiltercondition'] = $defaultfiltercondition;
        $arrRender['ModuleName'] = $ModuleName;
        $arrRender['Tabname'] = ucfirst($ModuleName);
        $arrRender['columns'] = $columns;
        $arrRender['filed_name'] = $filed_name;
        $arrRender['massedit'] = $massedit;
        $arrRender['TabLabel'] = $TabLabel;
        $arrRender['TabId'] = $TabId;
        $arrRender['DataImport'] = $columnsIMP;
        $arrRender['layout'] = $layout;
        $arrRender['srcheaderfullname'] = $srcheaderfullname;
        //code added by ptpatel on date 29-03-25
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'status' => 'success',
                'result' => $arrRender,
            ];
        }
        //end code added by ptpatel on date 29-03-25
        $this->layout = '@app/views/layouts/main-one';

        $this->render('@app/views/tetra/approvelistview', $arrRender);
    }

    public function actionGetfilterdetails($filterId)
    {

        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;


        $model = new AccessCheck();
        $id = Yii::$app->user->id;
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $modulepermission = $model->modulepermission($profile, $tabs);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $filterCondition = (new \yii\db\Query())
            ->select(['user_filter.*', 'field.*'])
            ->from('user_filter')
            ->join('INNER JOIN', 'field', 'field.fieldid = user_filter.fieldid')
            ->where(['and', ['user_filter.filter_id' => $filterId], ['user_filter.userid' => $id], ['user_filter.deleted' => 0]])

            ->one();

        if ($filterCondition) {
            return [
                'success' => true,
                'data' => $filterCondition,
                'filid' => $filterId,
            ];
        } else {
            return [
                'success' => false,
                'message' => 'No filter details found for the selected filter ID.',
            ];
        }
    }

    public function actionDeletefilter()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $filterId = Yii::$app->request->post('filter_id');
        $userId   = Yii::$app->user->id;
        $now      = date('Y-m-d H:i:s');
        $deleteFromDefault = Yii::$app->request->post('delete_from_default', 0);
        if (!$filterId) {
            return ['success' => false, 'message' => 'Invalid filter ID.'];
        }

        try {
        $db = \Yii::$app->db;
        $transaction = $db->beginTransaction();

        $childRows = $db->createCommand()
            ->update('user_filter', [
                'deleted'     => 1,
                'modified_by' => $userId,
                'modified_at' => $now,
                'action'      => 'deleted',
            ], 'filter_id = :filterId', [':filterId' => $filterId]) 
            ->execute();

        $parentRows = 0;
        if ($deleteFromDefault) {
            $parentRows = $db->createCommand()
                ->update('default_filter', [
                    'deleted'     => 1,
                    'modified_by' => $userId,
                    'modified_at' => $now,
                    'action'      => 'deleted',
                ], 'id = :filterId AND userid != 1', [':filterId' => $filterId])
                ->execute();
        }

        $transaction->commit();

        $message = "Child filters deleted ($childRows)";
        if ($parentRows > 0) {
            $message .= ". Parent filter also deleted ($parentRows)";
        } else {
            if ($deleteFromDefault) {
                $message .= ". Parent filter protected (user_id=1 or checkbox unchecked)";
            } else {
                $message .= ". Parent filter preserved (checkbox unchecked)";
            }
        }

        return [
            'success' => true,
            'message' => $message,
            'parent_rows' => $parentRows,
            'child_rows' => $childRows,
            'deleteFromDefault' => $deleteFromDefault
        ];

    } catch (\Exception $e) {
        $transaction?->rollBack();
        return ['success' => false, 'message' => $e->getMessage()];
    }
    }


    public function actionSaveasfilter()
    {
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;

        $model = new AccessCheck();
        $id = Yii::$app->user->id;
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $modulepermission = $model->modulepermission($profile, $tabs);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $now = date('Y-m-d H:i:s');
        
        // Retrieve POST data as strings
        $filterName = Yii::$app->request->post('filter_name');
        $description = Yii::$app->request->post('description');
        $fieldId = Yii::$app->request->post('fieldId'); // Direct string input
        $labelValue = Yii::$app->request->post('labelValue'); // Direct string input
        $inputValue = Yii::$app->request->post('inputValue'); // Direct string input
        $filterOperator = Yii::$app->request->post('filteroperator'); // Direct string input

        if (isset($_POST['inputValue'])) {
            if (is_array($_POST['inputValue'])) {
                $inputValue = implode(',', $inputValue);
            }
        }
        $defaultFilter = new DefaultFilter();
        $defaultFilter->filter_name = $filterName;
        $defaultFilter->description = $description;
        $defaultFilter->tabid = $TabId;
        $defaultFilter->default_condition = ''; // Default condition, adjust as needed
        $defaultFilter->userid = $id;
        $defaultFilter->created_by = $id;
        $defaultFilter->created_at = $now;
        $defaultFilter->modified_by = $id;
        $defaultFilter->modified_at = $now;
        $defaultFilter->action = 'created';
        if ($defaultFilter->save()) {
            // Save into user_filter without foreach
            $userFilter = new UserFilter();
            $userFilter->filter_id = $defaultFilter->id; // Link to saved default_filter
            $userFilter->fieldid = (int) $fieldId;
            $userFilter->fieldlabel = $labelValue; // Save as string
            $userFilter->filteroperator = $filterOperator; // Save as string
            $userFilter->userinput = $inputValue; // Save as string
            $userFilter->userid = $id;
            $userFilter->created_by = $id;
            $userFilter->created_at = $now;
            $userFilter->modified_by = $id;
            $userFilter->modified_at = $now;
            $userFilter->action = 'created';
            //  print_r($save_filter->save());die;
            if ($userFilter->save()) {
                return $this->asJson([
                    'status' => 'success',
                    'message' => 'filter and user filter created successfully.',
                ]);
            }
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'failed to save the filter',

            ]);
        }
    }




    public function actionExportdata()
    {
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;


        // Fetch necessary configurations
        $model = new AccessCheck();
        $id = Yii::$app->user->id;
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $modulepermission = $model->modulepermission($profile, $tabs);

        // Read selectedRowIds from the request
        $selectedIds = Yii::$app->request->post('selectedRowIds', []);
        $selectedIds = array_map('strval', $selectedIds); // Normalize to strings

        $listModel = new ListModel($TableName, $FieldId, $ModuleName);
        $ActionList = $listModel->getActionList($ModuleName);

        list($ColumnList, $RecordList, $totalitemcount) = $listModel->getExportRecord(
            $ActionList['OrderBy'] ?? '',
            $ActionList['SortOrder'] ?? '',
            $rolebasedrecord,
            $modulepermission,
        );



        // Debugging logs
        Yii::info('Selected IDs: ' . print_r($selectedIds, true), 'export');
        Yii::info('Record List: ' . print_r($RecordList, true), 'export');
        // print_r($RecordList);die;
        // Filter records
        $filteredRecords = array_filter($RecordList, function ($record) use ($selectedIds) {
            return in_array((string) $record['RecordId'], $selectedIds);
        });



        Yii::info('Filtered Records: ' . print_r($filteredRecords, true), 'export');

        if (empty($filteredRecords)) {
            return $this->asJson([
                'success' => false,
                'message' => 'No records found for the selected IDs.',
            ]);
        }

        // Extract headers dynamically
        $headers = array_values($ColumnList);

        // Map filtered records to dynamic headers
        $rows = array_map(function ($record) use ($ColumnList) {
            return array_map(function ($key) use ($record) {
                return $record[$key] ?? "";
            }, array_keys($ColumnList));
        }, $filteredRecords);

        return $this->asJson([
            'success' => true,
            'headers' => $headers,
            'rows' => $rows,
        ]);
    }

    public function actionExportitems()
    {
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;

        $Recordid = filter_var(Yii::$app->request->get('record'), FILTER_SANITIZE_NUMBER_INT);
        $blockid = filter_var(Yii::$app->request->get('section'), FILTER_SANITIZE_NUMBER_INT);



        $id = Yii::$app->user->id;

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $modulepermission = $model->modulepermission($profile, $tabs);
        $editpermission = $model->checkpermission($id, $ModuleName, "edit");
        $exportpermission = $model->checkpermission($id, $ModuleName, 'export');


        $actionid = "detail";
        $model = new EditModel($TableName, $FieldId, $ModuleName, $actionid);
        $model->_members[$FieldId] = $Recordid;

        // echo "<br>Final Else Case";
        list($Column, $Record) = $model->getFieldDetail($rolebasedrecord);
        $arrRender['Record'] = $Record;

        // echo "xdgdf<pre>";
        // print_r($Column);die;
        $arrRender['ColumnList'] = $Column;
        $modeluser = new UsersDetails();
        $arrRender['userlist'] = $modeluser->getuserlist();
        $arrRender['ModuleName'] = $ModuleName;
        $arrRender['TabId'] = $TabId;
        $arrRender['TableName'] = $TableName;
        $arrRender['FieldId'] = $FieldId;
        $arrRender['Tabname'] = ucfirst($ModuleName);
        $arrRender['Recordid'] = $Recordid;
        $arrRender['TabLabel'] = $TabLabel;
        $arrRender['hasadminpower'] = $hasadminpower;
        $arrRender['exportpermission'] = $exportpermission;
        $arrRender['blockid'] = $blockid;

        // print_r( $arrRender);die;
        $this->layout = '@app/views/layouts/main-new';
        $this->layout = false;
        $this->render('@app/views/tetra/detailexport', $arrRender);
    }


    public function actionFilterbylead()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $labelValue = Yii::$app->request->post('labelValue');
            $inputValue = Yii::$app->request->post('inputValue');
            $fieldId = Yii::$app->request->post('fieldId');
            $filterOperator = Yii::$app->request->post('filteroperator');

            if (!empty($fieldId) && !empty($inputValue)) {
                $field = (new \yii\db\Query())
                    ->select('columnname')
                    ->from('field')
                    ->where(['fieldid' => $fieldId])
                    ->one();

                if ($field && isset($field['columnname'])) {
                    $columnName = $field['columnname'];
                    $query = Leaddetails::find();

                    switch ($filterOperator) {
                        case 'Equals':
                            $query->andWhere([$columnName => $inputValue]);
                            break;
                        case 'Not_Equals':
                            $query->andWhere(['<>', $columnName, $inputValue]);
                            break;
                        case 'Contains':
                            $query->andWhere(['like', $columnName, $inputValue]);
                            break;
                        case 'Not_Contains':
                            $query->andWhere(['not like', $columnName, $inputValue]);
                            break;
                        case 'In':
                            $query->andWhere(['in', $columnName, $inputValue]); // $inputValue should be an array
                            break;
                        case 'Not_In':
                            $query->andWhere(['not in', $columnName, $inputValue]); // $inputValue should be an array
                            break;
                        case 'is_Empty':
                            $query->andWhere(['or', [$columnName => null], [$columnName => '']]);
                            break;
                        case 'is_Not_Empty':
                            $query->andWhere(['and', ['is not', $columnName, null], ['<>', $columnName, '']]);
                            break;
                        case 'Begins_with':
                            $query->andWhere(['like', $columnName, "$inputValue%", false]); // Searches for values beginning with $inputValue
                            break;
                        default:
                            return $this->asJson([
                                'success' => false,
                                'message' => 'Invalid filter operator',
                            ]);
                    }

                    $filteredLeads = $query->asArray()->all(); // Convert to array

                    return $this->asJson([
                        'success' => true,
                        'data' => $filteredLeads,
                    ]);
                } else {
                    return $this->asJson([
                        'success' => false,
                        'message' => 'Column not found for the provided field ID',
                    ]);
                }
            } else {
                return $this->asJson([
                    'success' => false,
                    'message' => 'Field ID or input value is missing',
                ]);
            }
        } else {
            return $this->asJson([
                'success' => false,
                'message' => 'Invalid request',
            ]);
        }
    }

    public function actionSaveselectedcolumns()
    {
        $this->enableCsrfValidation = false; //disable csrf
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        // Get selected and deselected columns from the POST request
        $selectedColumns = Yii::$app->request->post('selectedColumns', []);
        $deselectedColumns = Yii::$app->request->post('deselectedColumns', []);
        $ModuleName = $this->ModuleName;

        $deleted = false;

        //get customview
        $savedColumns = (new \yii\db\Query())
            ->select(['cvid'])
            ->from('customview')
            ->where(['entitytype' => ucfirst($ModuleName)])
            ->andWhere(['userid' => Yii::$app->user->id])
            ->column();
        $cvid = $savedColumns[0];
        //($savedColumns);die;
        //$oldcols added by ptpatel on date 25-11-25 add log for change in cvcolumn
        $oldCols = (new \yii\db\Query())
            ->select([
                'cv.columnname',
                'cv.fieldid',
                'f.fieldlabel AS columnlabel'
            ])
            ->from('cvcolumnlist cv')
            ->leftJoin('field f', 'f.fieldid = cv.fieldid')
            ->where(['cv.cvid' => $cvid])
            ->all();

        // Process selected columns: insert if they don't exist
        foreach ($selectedColumns as $column) {
            $exists = (new \yii\db\Query())
                ->from('cvcolumnlist')
                ->where(['fieldid' => $column['field_id']])
                ->andWhere(['cvid' => $cvid])
                ->exists();


            if (!$exists) {
                //get the last columnindex
                $mxcolumns = (new \yii\db\Query())
                    ->select(['max(columnindex) as maxid'])
                    ->from('cvcolumnlist')
                    ->where(['cvid' => $cvid])
                    ->one();
                // print_r($mxcolumns);die;

                $maxid = $mxcolumns['maxid'] + 1;
                Yii::$app->db->createCommand()->insert('cvcolumnlist', [
                    'cvid' => $cvid, // Update with relevant ID
                    'columnindex' => $maxid,
                    'columnname' => $column['columnname'],
                    'fieldid' => $column['field_id'],
                ])->execute();
                // array_push($newCols,$column['columnname']);                
            }
        }
        //added on 28 nov 2025 to check deleted cvcolumnlist by deepika
        // Assuming you have these PHP variables already populated
       
        // Assuming you have these PHP variables already populated
        $loggedInUser = Yii::$app->user->identity->id; // Get the logged-in user ID
        $currentTimestamp = date('Y-m-d H:i:s'); // Get the current timestamp
        $selectedColumns_l = json_encode($selectedColumns); // Convert the selected columns array to JSON
        $deselectedColumns_l = json_encode($deselectedColumns); // Convert the deselected columns array to JSON

        foreach ($deselectedColumns as $field_id) {
            // Fetch the row to be deleted (for logging purposes)
            $row = Yii::$app->db->createCommand('SELECT * FROM cvcolumnlist WHERE fieldid = :fieldid')
                ->bindValue(':fieldid', $field_id)
                ->queryOne();

            // If the row exists, log it into the cvcolumnlist_log table
            if ($row) {
                // Insert into log table with additional fields (whodid, createdat, selectedcolumns, deselectedcolumns)
                Yii::$app->db->createCommand()->insert('cvcolumnlist_log', [
                    'cvid' => $row['cvid'],
                    'columnindex' => $row['columnindex'],
                    'fieldid' => $row['fieldid'],
                    'columnname' => $row['columnname'], // Add other columns as necessary
                    'whodid' => $loggedInUser,
                    'createdat' => $currentTimestamp,
                    'selectedcolumns' => $selectedColumns_l,
                    'deselectedcolumns' => $deselectedColumns_l,
                    // Add any other relevant fields here
                ])->execute();
            }

            // Delete the row from cvcolumnlist
            $deletedRows = Yii::$app->db->createCommand()->delete('cvcolumnlist', [
                'fieldid' => $field_id,'cvid'=>$cvid
            ])->execute();

            // Check if deletion was successful
            if ($deletedRows > 0) {
                $deleted = true;
            }
        }


        
        // Process deselected columns: delete if they exist
        // foreach ($deselectedColumns as $field_id) {
        //     $deletedRows = Yii::$app->db->createCommand()->delete('cvcolumnlist', [
        //         'fieldid' => $field_id,
        //     ])->execute();

        //     if ($deletedRows > 0) {
        //         $deleted = true;
        //     }
        // }
        
        $oldformatted = $newFormatted = [];
        //to get changed column in sequence 
        $finalColumns = (new \yii\db\Query())
            ->select([
                'cv.columnname',
                'cv.fieldid',
                'f.fieldlabel AS columnlabel'
            ])
            ->from('cvcolumnlist cv')
            ->leftJoin('field f', 'f.fieldid = cv.fieldid')
            ->where(['cv.cvid' => $cvid])
            ->all();

        foreach ($oldCols as $col) {
            $oldformatted[$col['columnname']] = $col['columnlabel'];
        }
        
        foreach ($finalColumns as $col) {
            $newFormatted[$col['columnname']] = $col['columnlabel'];
        }
        // echo "<pre>deleted col";print_r(json_encode($oldformatted));print_r(json_encode($newFormatted));die;
        
        $add_cvcolumn_log = new CvcolumnModtrackerBasic();
        $add_cvcolumn_log->cvcolumnauditlog(json_encode($oldformatted),json_encode($newFormatted),$ModuleName,Yii::$app->user->id,$cvid);           
        //end code added by ptpatel on date 25-11-25 to add log for change in cvcolumn
        // Return different messages based on actions performed
        if ($deleted) {
            return ['status' => 'success', 'message' => 'Deselected columns deleted successfully.'];
        } else {
            return ['status' => 'success', 'message' => 'Selected columns saved successfully.'];
        }
    }

    public function actionGettabledata()
    {

        //echo "xcx";die;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;


        $model = new AccessCheck();
        $id = Yii::$app->user->id;
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $modulepermission = $model->modulepermission($profile, $tabs);
        // Fetch data from the 'leaddetails' table
        //$leads = Leaddetails::find()->asArray()->all();
        $action = "List";
        $model = new ListModel($TableName, $FieldId, $ModuleName);
        $this->getClientScript($ModuleName, strtolower($action));
        $ActionList = $model->getActionList($ModuleName);
        $ActionList['OrderBy'] = Yii::$app->request->get('OrderBy');
        $ActionList['SortOrder'] = Yii::$app->request->get('SortOrder');
        if (isset($_POST['inputValue'])) {
            if (is_array($_POST['inputValue'])) {
                $_POST['inputValue'] = implode(",", $_POST['inputValue']);
            }
        }
        $curPageNo = ''; //$_REQUEST['pagejump'];
        list($ColumnList, $RecordList, $totalitemcount) = $model->getListRecord($ActionList['OrderBy'], $ActionList['SortOrder'], $rolebasedrecord, $modulepermission);
        $arrRender = array();
        $arrRender['RecordList'] = $RecordList;
        $arrRender['totalitemcount'] = $totalitemcount;
        return $arrRender;
    }

    public function actionGettabledataapproval()
    {

        //echo "xcx";die;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;


        $model = new AccessCheck();
        $id = Yii::$app->user->id;
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $modulepermission = $model->modulepermission($profile, $tabs);
        // Fetch data from the 'leaddetails' table
        //$leads = Leaddetails::find()->asArray()->all();

        $action = "List";
        $model = new ApproveListModel($TableName, $FieldId, $ModuleName);
        $this->getClientScript($ModuleName, strtolower($action));
        $ActionList = $model->getActionList($ModuleName);
        $ActionList['OrderBy'] = Yii::$app->request->get('OrderBy');
        $ActionList['SortOrder'] = Yii::$app->request->get('SortOrder');
        $curPageNo = ''; //$_REQUEST['pagejump'];
        list($ColumnList, $RecordList, $totalitemcount) = $model->getListRecord($ActionList['OrderBy'], $ActionList['SortOrder'], $rolebasedrecord, $modulepermission);
        // print_r($RecordList);
        $arrRender = array();
        $arrRender['RecordList'] = $RecordList;
        $arrRender['totalitemcount'] = $totalitemcount;
        return $arrRender;
    }

    // public function actionGettabledataapproval()
    // {

    //     //echo "xcx";die;
    //     \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    //     $FieldId = $this->FieldId;
    //     $ModuleName = $this->ModuleName;
    //     $TableName = $this->TableName;
    //     $TabLabel = $this->TabLabel;


    //     $model = new AccessCheck();
    //     $id = Yii::$app->user->id;
    //     $tabs = $model->tabs($id, $ModuleName);
    //     $profile = $model->profile($id, $tabs, $ModuleName);
    //     $modelaccess = $model->moduleaccess($id, $profile, $tabs);
    //     $rolebasedrecord = $model->rolebasedrecord($id, $profile);
    //     $hasadminpower = $model->hasadminpower($profile);
    //     $modulepermission = $model->modulepermission($profile, $tabs);
    //     // Fetch data from the 'leaddetails' table
    //     //$leads = Leaddetails::find()->asArray()->all();
    //     $action = "List";
    //     $model = new ApproveListModel($TableName, $FieldId, $ModuleName);
    //     $this->getClientScript($ModuleName, strtolower($action));
    //     $ActionList = $model->getActionList($ModuleName);
    //     $ActionList['OrderBy'] = Yii::$app->request->get('OrderBy');
    //     $ActionList['SortOrder'] = Yii::$app->request->get('SortOrder');
    //     $curPageNo = ''; //$_REQUEST['pagejump'];
    //     list($ColumnList, $RecordList, $totalitemcount) = $model->getListRecord($ActionList['OrderBy'], $ActionList['SortOrder'], $rolebasedrecord, $modulepermission);
    //     // print_r($RecordList);
    //     $arrRender = array();
    //     $arrRender['RecordList'] = $RecordList;
    //     $arrRender['totalitemcount'] = $totalitemcount;
    //     return $arrRender;
    // }

    /**
     * Load Module Script  
     */
    public function getClientScript($ModuleName, $action)
    {
        $baseUrl = Yii::$app->HomeUrl;
        $scriptPath = $baseUrl . "js/$ModuleName/$action.js";
        //added on 06jan 2025
        // $this->view->registerJsFile(
        //     $scriptPath,  // Path to your JavaScript file
        //     [
        //         'depends' => [\yii\web\JqueryAsset::class],  // List of dependencies, e.g., jQuery
        //         'position' => \yii\web\View::POS_END,        // Position at the end of the body
        //     ]
        // );

    }

    ///////list related functions
    public function actionGetLeads()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Fetch data from the database
        $leads = Leaddetails::find()->asArray()->all();

        return $leads;
    }

    public function actionAgTable()
    {
        AdminAsset::register(Yii::$app->view);
        $this->layout = "main-new";
        return $this->render('ag-table');
    }

    public function actionTable()
    {
        AdminAsset::register(Yii::$app->view);
        $this->layout = "main-new";
        return $this->render('table');
    }

    public function actionGetData()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
            $models = TableList::find()->all();
            $data = [];

            foreach ($models as $model) {
                $data[] = [
                    'id' => $model->id,
                    'first_name' => $model->first_name,
                    'last_name' => $model->last_name,
                    'email' => $model->email,
                    'phone' => $model->phone,
                    'country' => $model->country,
                    'city' => $model->city,
                    'owner' => is_array($model->owner) ? implode(', ', $model->owner) : $model->owner,  // Example of array handling
                    'company_name' => $model->company_name,
                    'address' => $model->address,
                    'company_address' => $model->company_address,
                    'company_website' => $model->company_website,
                    'employee_age' => $model->employee_age,
                    'employee_name' => $model->employee_name,
                    'created_at' => $model->created_at,
                ];
            }

            return $data;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function actionGetTable()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
            $models = TableList::find()->all();
            $data = [];

            foreach ($models as $model) {
                $data[] = [
                    'first_name' => $model->first_name,
                    'last_name' => $model->last_name,
                    'email' => $model->email,
                    'phone' => $model->phone,
                    'city' => $model->city,
                    'company_name' => $model->company_name,
                ];
            }

            return $data;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function actionUpdatestage()
    {
        // print_r($_POST);die;
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;

        $id = Yii::$app->user->id;

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;


        if (Yii::$app->request->isPost) {
            $RecordId = Yii::$app->request->post('RecordId');

            $actionid = "edit";
            $model = new EditModel($TableName, $FieldId, $ModuleName, $actionid);
            $model->_members[$FieldId] = $RecordId;


            if ($model->updateModule($RecordId)) {
                //echo "saved";die;
                // Yii::$app->session->setFlash('success', 'Data saved successfully.');
                //return $this->redirect(['list']); // Adjust accordingly
                return ['success' => true];
            } else {
                return ['success' => false];
            }
        }



        return ['success' => false];
    }
    public function actionPostnotes()
    {
        // print_r($_POST);die;
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;



        $id = Yii::$app->user->id;
        $data = $_POST;

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);



        ///start upload code

        Yii::$app->response->format = Response::FORMAT_JSON;

        $file = UploadedFile::getInstanceByName('file'); // Optional file upload
        //print_r($_FILES);die;
        $modnotes = Yii::$app->request->post('modnotesval'); // Text content
        $Recordid = Yii::$app->request->post('Recordid'); // Text content

        if (!$file && empty(trim($modnotes))) {
            return ['success' => false, 'message' => 'You must provide either a file or some text.'];
        }

        $fileUrl = null;
        $model = new Modnotes();
        $model->notecontent = $modnotes;
        $model->related_to = $TabId;
        $model->related_to_id = $Recordid;
        $model->userid = Yii::$app->user->id;
        $model->ownerid = Yii::$app->user->id;
        $model->creatorid = Yii::$app->user->id;
        $model->modifiedby = Yii::$app->user->id;
        $model->modifiedtime = date("Y-m-d H:i:s");
        $model->createdtime = date("Y-m-d H:i:s");


        // Handle file upload if a file is provided
        if ($file) {
            // Security: Validate file extension and MIME type
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'doc', 'docx', 'pdf', 'xls', 'xlsx'];
            $allowedMimeTypes = [
                'image/jpeg',
                'image/png',
                'image/gif',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/pdf',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ];
            if (!in_array($file->extension, $allowedExtensions) || !in_array($file->type, $allowedMimeTypes)) {
                return ['success' => false, 'message' => 'Invalid file type.'];
            }

            // Determine the directory structure based on year, month, and week
            $year = date('Y');
            $month = date('m');
            $week = date('W'); // Week of the year

            // Define the upload base path
            $baseUploadPath = Yii::getAlias('@webroot/uploads');
            $targetPath = $baseUploadPath . "/$year/$month/week_$week/";


            // Create directories if they do not exist
            if (!is_dir($targetPath)) {
                if (!mkdir($targetPath, 0755, true)) {
                    return ['success' => false, 'message' => 'Failed to create upload directories.'];
                }
            }

            // Generate a secure unique file name
            $fileName = uniqid() . '.' . $file->extension;
            $filePath = $targetPath . $fileName;
            $filesavepath = "uploads/$year/$month/week_$week/" . $fileName;


            //save to attachments
            $modelatach = new Attachments();
            $modelatach->name = $file->name;
            $modelatach->type = $file->type;
            $modelatach->path = $filesavepath;
            $modelatach->storedname = $fileName;
            if ($modelatach->validate()) {
                if ($modelatach->save()) {
                    $model->filename = $modelatach->attachmentsid;
                }
            }

            // Save the file
            if ($file->saveAs($filePath)) {
                $fileUrl = Yii::getAlias('@web') . "/uploads/$year/$month/week_$week/" . $fileName;
            } else {
                $message = 'Failed to save the file.';
            }
        }



        //end upload code

        if ($model->validate()) {
            if ($model->save()) {
                $modlog = new ModtrackerBasic();
                $modlog->auditlog('', '', $model->related_to, $model->related_to_id, 3, $model->userid, "notes", $model->modnotesid);

                $modlog->auditlog('', $model, "notes", $model->modnotesid, 0, $model->userid);
                // $allnotes= getnotes();
                // return $allnotes;

                ///now check all the mentions/////
                // Regular expression to match words starting with @
                preg_match_all('/@\w+/', $modnotes, $matches);

                // $matches[0] will contain all the words starting with @
                //  print_r($matches[0]);die;
                if (!empty($matches[0])) {
                    foreach ($matches[0] as $value) {
                        // echo $value;
                        // echo "<BR>";
                        $value = str_replace('@', '', $value);
                        // echo $value;
                        // echo "<BR>";

                        if (!empty($value)) {
                            $fullName = $value;
                            // Perform the search using the concatenated first name and last name
                            $user = User::find()
                                ->where(['like', 'CONCAT(first_name, last_name)', $fullName, false])
                                ->one();

                            if ($user) {
                                // Save notification
                                $notification = new Notifications();
                                $notification->userid = $user->id;
                                $notification->message = "You were mentioned in a note.";
                                $notification->read_status = 0; // Unread notification
                                $notification->source_link = Yii::$app->request->baseUrl . "/" . $ModuleName . "/detail?Record=" . $model->related_to_id;
                                $notification->createdtime = date('Y-m-d H:i:s');

                                if (!$notification->save()) {
                                    // return ['success' => false, 'errors' => $notification->errors];
                                }

                                // return ['success' => true, 'message' => 'Mention saved successfully.'];
                            }
                        }
                    }
                }

                //end mentions/////////

                return [
                    'success' => true,
                    'message' => $fileUrl ? 'File uploaded successfully.' : 'Notes saved successfully.',
                    'fileUrl' => $fileUrl,
                    'textContent' => $modnotes,
                ];
                //get all notes
            } else {

                return [
                    'success' => false,
                    'message' => "Failed to save model: " .
                        json_encode(
                            $model->getErrors()
                        )
                ];
            }
        } else {

            return [
                'success' => false,
                'message' => "Failed to save model: " .
                    json_encode(
                        $model->getErrors()
                    )
            ];
        }
    }
    public function getnotes($Record)
    {
        // print_r($_POST);die;
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TabLabel = $this->TabLabel;

        $TableName = $this->TableName;


        $id = Yii::$app->user->id;
        $data = $_POST;
        //$Record = $_POST['Recordid'];

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);

        $model = new Modnotes();

        // $allnotes = $model->find()
        // ->where(['related_to' => $TabId])
        // ->andWhere(['related_to_id' => $Record])
        // ->orderBy(['modnotesid' => SORT_DESC])
        // ->all();
        $records = $model->find()
            ->joinWith(['modtrackerBasic', 'attachments'])
            ->where(['modnotes.related_to' => $TabId])
            ->andWhere(['modnotes.related_to_id' => $Record])
            ->andWhere(['modnotes.deleted' => 0])
            ->orderBy(['modnotes.modnotesid' => SORT_DESC])
            ->all();
        $detail = array();
        //echo "<pre>";
        //print_r($records);die;
        foreach ($records as $record) {
            $a = array();
            $username = $this->getuser($record->userid);

            $a['notecontent'] = $record->notecontent;
            $a['notebyuser'] = $username;
            if (isset($record->attachments)) {
                //print_r($record->attachments);die;
                $a['fileid'] = $record->attachments['attachmentsid'];
                $a['filename'] = $record->attachments['name'];
                $a['filepath'] = $record->attachments['path'] . $record->attachments['storedname'];
            } else {
                $a['fileid'] = '';
                $a['filename'] = '';
                $a['filepath'] = '';
            }
            //print_r($record->modtrackerBasic);die;
            // Access related ModtrackerBasic fields
            if ($record->createdtime) {
                $timestamp = strtotime($record->createdtime);

                // Format the date
                $enteredat = date('M d, Y \a\t g.i a', $timestamp); // Format: Oct 22, 2024 at 9.16 am
                $a['notedon'] = $enteredat;
            } else
                $a['notedon'] = '';

            // } else {
            //     echo "No ModtrackerBasic found for this Modnotes.\n";
            // }
            array_push($detail, $a);
        }
        // echo "<pre>";print_r($detail);
        // die;
        return $detail;
    }
    public function getnotesnew($TabId, $Record)
    {

        $model = new Modnotes();

        // $allnotes = $model->find()
        // ->where(['related_to' => $TabId])
        // ->andWhere(['related_to_id' => $Record])
        // ->orderBy(['modnotesid' => SORT_DESC])
        // ->all();
        $records = $model->find()
            ->joinWith(['modtrackerBasic', 'attachments'])
            ->where(['modnotes.related_to' => $TabId])
            ->andWhere(['modnotes.related_to_id' => $Record])
            ->andWhere(['modnotes.deleted' => 0])
            ->orderBy(['modnotes.modnotesid' => SORT_DESC])
            ->all();
        $detail = array();
        //echo "<pre>";
        // print_r($records);die;
        foreach ($records as $record) {
            $a = array();
            $username = $this->getuser($record->userid);

            $a['notecontent'] = $record->notecontent;
            $a['notebyuser'] = $username;
            if (isset($record->attachments)) {
                //print_r($record->attachments);die;
                $a['fileid'] = $record->attachments['attachmentsid'];
                $a['filename'] = $record->attachments['name'];
                $a['filepath'] = $record->attachments['path'] . $record->attachments['storedname'];
            } else {
                $a['fileid'] = '';
                $a['filename'] = '';
                $a['filepath'] = '';
            }
            //print_r($record->modtrackerBasic);die;
            // Access related ModtrackerBasic fields
            if ($record->modtrackerBasic) {
                $timestamp = strtotime($record->modtrackerBasic->changedon);

                // Format the date
                $enteredat = date('M d, Y \a\t g.i a', $timestamp); // Format: Oct 22, 2024 at 9.16 am
                $a['notedon'] = $enteredat;
            } else
                $a['notedon'] = '';

            // } else {
            //     echo "No ModtrackerBasic found for this Modnotes.\n";
            // }
            array_push($detail, $a);
        }
        // echo "<pre>";print_r($detail);
        // die;
        return $detail;
    }
    function getuser($userid)
    {

        $connection = Yii::$app->db;
        // $command=$connection->createCommand("select targettable,targetfield,dispfield  from picklist where     fieldid=:fieldid")->bindParam(':fieldid'=>$fieldid);


        $command = $connection->createCommand("select id,email,concat(first_name,' ',last_name) as showfield from user  where deleted =0 and id=:id")->bindValue(":id", $userid);
        $Columns = $command->queryOne();

        return $Columns['showfield'];
    }
    function actionDeleteselectedrow()
    {
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;


        $id = Yii::$app->user->id;


        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);

        if (Yii::$app->request->isAjax) {

            $leadIds = Yii::$app->request->post('leadIds', []);
            if (!empty($leadIds)) {
                $ids = implode(', ', $leadIds);
                //print_r($ids);die;
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $sql = "Update $TableName set deleted = 1 where $FieldId in ($ids)";
                    $result = Yii::$app->db->createCommand($sql)->execute();
                    // print_r($result);die;
                    if ($result) {
                        $transaction->commit();
                        return $this->asJson([
                            'status' => 'success',
                        ]);
                    }
                } catch (\Exception $e) {
                    echo "Failed to Archieve data: " . $e->getMessage() . " " . $e->getTraceAsString();
                    die;
                    // Rollback the transaction if something goes wrong
                    $transaction->rollBack();
                    return $this->asJson([
                        'status' => 'error',
                        'message' => "Failed to Archieve data: " . $e->getMessage() . " " . $e->getTraceAsString(),
                    ]);
                }
            }
        }
    }
    public function actionBulkupdate()
    {
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;



        $id = Yii::$app->user->id;


        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON; // Set response format as JSON

        $request = \Yii::$app->request;

        // Check if the request is POST
        if ($request->isPost) {
            // Retrieve data from the POST request
            $leadIds = $request->post('hiddenLeadIds'); // Comma-separated IDs (e.g., "1,2,4")
            $fieldcolumn = $request->post('selectedValue'); // Field ID
            $userInput = $request->post('userInput'); // User-provided value to update
            //print_r($_POST);die;
            // Validate input
            if (empty($leadIds) || empty($fieldcolumn) || $userInput === null) {
                return [
                    'success' => false,
                    'message' => 'Invalid input data.',
                ];
            }

            // Convert lead IDs to an array
            $leadIdsArr = explode(',', $leadIds);
            $leadIdsArray = array_map('intval', $leadIdsArr); // Ensure the values are integers
            $transaction = Yii::$app->db->beginTransaction();

            try {
                // Fetch the column name from the `field` table based on `fieldid`
                $fieldtable = (new \yii\db\Query())
                    ->select('tablename')
                    ->from('field')
                    ->where(['columnname' => $fieldcolumn, 'tabid' => $TabId])
                    ->scalar();
                //echo $field;die;


                if (!$fieldtable) {
                    return [
                        'success' => false,
                        'message' => 'Invalid field.',
                    ];
                }
                foreach ($leadIdsArr as $valueid) {
                    // echo $valueid;die;
                    //get old values
                    $oldvalue = Yii::$app->db->createCommand("
                        SELECT $fieldcolumn 
                        FROM $fieldtable 
                        WHERE $FieldId = :valueid
                    ")
                        ->bindValue(':valueid', $valueid)
                        ->queryScalar();
                    //echo $oldvalue;die;
                    $oldattrbute = array($fieldcolumn => $oldvalue);
                    $newattrbute = array($fieldcolumn => $userInput);
                    // print_r($newattrbute);die;
                    $modlog = new ModtrackerBasic();
                    $auditstatus = 1;
                    $modlog->auditlog($oldattrbute, $newattrbute, $ModuleName, $valueid, $auditstatus, Yii::$app->user->id);
                    # code...
                    // Perform the bulk update
                    $affectedRows = \Yii::$app->db->createCommand()
                        ->update(
                            $fieldtable,        // Table name
                            [$fieldcolumn => $userInput],   // Column to update and its new value
                            [$FieldId => $valueid] // Condition: leadid in array
                        )
                        ->execute();
                }
                $transaction->commit();





                return [
                    'success' => true,
                    'message' => "Bulk update successful. ", //$affectedRows rows updated.",
                ];
            } catch (\Exception $e) {
                $transaction->rollBack();

                return [
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage(),
                ];
            }
        }

        return [
            'success' => false,
            'message' => 'Invalid request method.',
        ];
    }
    public function actionAddcall()
    {


        $id = Yii::$app->user->id;
        //uncomment for access control
        // $model=new AccessCheck();
        // $tabs=$model->tabs($id,$ModuleName);
        // $profile=$model->profile($id,$tabs,$ModuleName);
        // $modelaccess=$model->moduleaccess($id,$profile,$tabs);
        // $rolebasedrecord=$model->rolebasedrecord($id,$profile);

        $actionid = "quickcreate";
        $request = \Yii::$app->request;

        if ($request->isPost) {
            // print_r($_POST);die;
            $TabId = "20"; //$this->TabId;
            $FieldId = "callinfo_id"; //$this->FieldId;
            $ModuleName = "call"; //$this->ModuleName;
            $TableName = "call_information"; //$this->TableName;
            $model = new EditModel($TableName, $FieldId, $ModuleName, $actionid);


            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON; // Set response format as JSON
            $model->saveModule(1);
            return [
                'success' => true,
                'message' => "call insert successful. ", //$affectedRows rows updated.",
            ];
        } else {
            $TabId = $this->TabId;
            $FieldId = $this->FieldId;
            $ModuleName = $this->ModuleName;
            $TableName = $this->TableName;
            $TabLabel = $this->TabLabel;

            $recordid = Yii::$app->request->get('Recordid');
            $arrRender = array();
            $arrRender['TabId'] = $TabId;
            $arrRender['FieldId'] = $FieldId;
            $arrRender['ModuleName'] = "calls";
            $arrRender['TableName'] = $TableName;
            $arrRender['Recordid'] = $recordid;
            $arrRender['TabLabel'] = $TabLabel;

            $callTypeModel = new CallType();
            $callTypeList = $callTypeModel->getCallTypeList();

            $OutgoingCallStatusModel = new OutgoingCallStatus();
            $OutgoingCallStatusList = $OutgoingCallStatusModel->outingCallTypeList();

            $arrRender['calltypelist'] = $callTypeList;
            $arrRender['OutgoingCallStatusList'] = $OutgoingCallStatusList;
            // print_r($arrRender);die;

            $this->layout = '@app/views/layouts/main-one';
            $this->renderPartial('@app/views/tetra/callminiform', $arrRender);
        }

        return [
            'success' => false,
            'message' => 'Invalid request method.',
        ];

        // CallInformation


    }
    public function actionAddmeeting()
    {


        $id = Yii::$app->user->id;
        //uncomment for access control
        // $model=new AccessCheck();
        // $tabs=$model->tabs($id,$ModuleName);
        // $profile=$model->profile($id,$tabs,$ModuleName);
        // $modelaccess=$model->moduleaccess($id,$profile,$tabs);
        // $rolebasedrecord=$model->rolebasedrecord($id,$profile);

        $actionid = "quickcreate";
        $request = \Yii::$app->request;

        if ($request->isPost) {
            $TabId = "21"; //$this->TabId;
            $FieldId = "meetinginfo_id"; //$this->FieldId;
            $ModuleName = "meeting"; //$this->ModuleName;
            $TableName = "meeting_information"; //$this->TableName;
            $model = new EditModel($TableName, $FieldId, $ModuleName, $actionid);

            //print_r($_POST);
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON; // Set response format as JSON
            $model->saveModule(1);
            return [
                'success' => true,
                'message' => "meeting insert successful. ", //$affectedRows rows updated.",
            ];
        } else {
            $TabId = $this->TabId;
            $FieldId = $this->FieldId;
            $ModuleName = $this->ModuleName;
            $TableName = $this->TableName;
            $TabLabel = $this->TabLabel;
            $Recordid = Yii::$app->request->get('Recordid');


            $arrRender = array();
            $arrRender['TabId'] = $TabId;
            $arrRender['FieldId'] = $FieldId;
            $arrRender['ModuleName'] = "meeting";
            $arrRender['TableName'] = $TableName;
            $arrRender['Recordid'] = $Recordid;
            $arrRender['TabLabel'] = $TabLabel;

            //print_r($arrRender);die;

            $this->layout = '@app/views/layouts/main-one';
            $this->renderPartial('@app/views/tetra/callminiform', $arrRender);
        }

        return [
            'success' => false,
            'message' => 'Invalid request method.',
        ];

        // CallInformation


    }
    public function actionAddtask()
    {
        $id = Yii::$app->user->id;
        //uncomment for access control
        // $model=new AccessCheck();
        // $tabs=$model->tabs($id,$ModuleName);
        // $profile=$model->profile($id,$tabs,$ModuleName);
        // $modelaccess=$model->moduleaccess($id,$profile,$tabs);
        // $rolebasedrecord=$model->rolebasedrecord($id,$profile);

        $actionid = "quickcreate";
        $request = \Yii::$app->request;

        if ($request->isPost) {
            $TabId = "22"; //$this->TabId;
            $FieldId = "taskinfo_id"; //$this->FieldId;
            $ModuleName = "task"; //$this->ModuleName;
            $TableName = "task_information"; //$this->TableName;
            $model = new EditModel($TableName, $FieldId, $ModuleName, $actionid);

            // print_r($_POST);die;
            //print_r($_POST);
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON; // Set response format as JSON
            $model->saveModule(1);
            return [
                'success' => true,
                'message' => "Task insert successful. ", //$affectedRows rows updated.",
            ];
        } else {
            $TabId = $this->TabId;
            $FieldId = $this->FieldId;
            $ModuleName = $this->ModuleName;
            $TableName = $this->TableName;
            $TabLabel = $this->TabLabel;
            $model = new EditModel($TableName, $FieldId, $ModuleName, $actionid);
            $Recordid = Yii::$app->request->get('Recordid');

            $arrRender = array();
            $arrRender['TabId'] = $TabId;
            $arrRender['FieldId'] = $FieldId;
            $arrRender['ModuleName'] = "task";
            $arrRender['TableName'] = $TableName;
            $arrRender['Recordid'] = $Recordid;
            $modeluser = new UsersDetails();
            $userlist = $modeluser->getuserlist();
            $arrRender['userlist'] = $userlist;
            $arrRender['TabLabel'] = $TabLabel;


            //print_r($arrRender);die;

            $this->layout = '@app/views/layouts/main-one';
            $this->renderPartial('@app/views/tetra/callminiform', $arrRender);
        }

        return [
            'success' => false,
            'message' => 'Invalid request method.',
        ];

        // CallInformation


    }
    public function actionAdddoc()
    {
        $id = Yii::$app->user->id;
        //uncomment for access control
        // $model=new AccessCheck();
        // $tabs=$model->tabs($id,$ModuleName);
        // $profile=$model->profile($id,$tabs,$ModuleName);
        // $modelaccess=$model->moduleaccess($id,$profile,$tabs);
        // $rolebasedrecord=$model->rolebasedrecord($id,$profile);

        $actionid = "quickcreate";
        $request = \Yii::$app->request;



        if ($request->isPost) {
            // print_r($_POST);die;
            $TabId = "23"; //$this->TabId;
            $FieldId = "docid"; //$this->FieldId;
            $ModuleName = "documents"; //$this->ModuleName;
            $TableName = "documents"; //$this->TableName;
            $model = new EditModel($TableName, $FieldId, $ModuleName, $actionid);


            ///start upload code

            Yii::$app->response->format = Response::FORMAT_JSON;

            $file = UploadedFile::getInstanceByName('file'); // Optional file upload
            //print_r($_FILES);die;
            $documents = Yii::$app->request->post('documents'); // Text content

            if (!$file || empty($documents)) {
                return ['success' => false, 'message' => 'You must provide file and fill required fields'];
            }

            $fileUrl = null;



            // Handle file upload if a file is provided
            if ($file) {
                // Security: Validate file extension and MIME type
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'doc', 'docx', 'pdf', 'xls', 'xlsx'];
                $allowedMimeTypes = [
                    'image/jpeg',
                    'image/png',
                    'image/gif',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/pdf',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                ];
                if (!in_array($file->extension, $allowedExtensions) || !in_array($file->type, $allowedMimeTypes)) {
                    return ['success' => false, 'message' => 'Invalid file type.'];
                }

                // Determine the directory structure based on year, month, and week
                $year = date('Y');
                $month = date('m');
                $week = date('W'); // Week of the year

                //get folder name from id
                $command = Yii::$app->db->createCommand("select * from `attachmentsfolder`  where folderid=:folderid")->bindParam(':folderid', $documents['folderid']);
                $folder = $command->queryOne();
                //print_r($folder);die;
                $foldername = $folder['path'];

                // Define the upload base path
                $baseUploadPath = Yii::getAlias('@webroot');
                $targetPath = $baseUploadPath . "/" . $foldername . "/$year/$month/week_$week/";


                // Create directories if they do not exist
                if (!is_dir($targetPath)) {
                    if (!mkdir($targetPath, 0755, true)) {
                        return ['success' => false, 'message' => 'Failed to create upload directories.'];
                    }
                }

                // Generate a secure unique file name
                $fileName = uniqid() . '.' . $file->extension;
                $filePath = $targetPath . $fileName;
                $filesavepath = $foldername . "/$year/$month/week_$week/" . $fileName;


                //save to attachments
                $modelatach = new Attachments();
                $modelatach->name = $file->name;
                $modelatach->type = $file->type;
                $modelatach->path = $filesavepath;
                $modelatach->storedname = $fileName;
                if ($modelatach->validate()) {
                    if ($modelatach->save()) {
                        $_POST['documents']['filename'] = $modelatach->attachmentsid;
                    }
                }


                // Save the file
                if ($file->saveAs($filePath)) {
                    $fileUrl = Yii::getAlias('@web') . "/" . $foldername . "/$year/$month/week_$week/" . $fileName;
                } else {
                    $message = 'Failed to save the file.';
                    die;
                }
            }

            //end upload code
            $model->saveModule(1);
            return [
                'success' => true,
                'message' => "Document insert successful. ", //$affectedRows rows updated.",
            ];
        } else {
            $TabId = $this->TabId;
            $FieldId = $this->FieldId;
            $ModuleName = $this->ModuleName;
            $TableName = $this->TableName;
            $model = new EditModel($TableName, $FieldId, $ModuleName, $actionid);

            $modeluser = new UsersDetails();
            $userlist = $modeluser->getuserlist();
            $folderlist = $modeluser->getfolderlist();
            $Recordid = Yii::$app->request->get('Record');


            $arrRender = array();
            $arrRender['TabId'] = $TabId;
            $arrRender['FieldId'] = $FieldId;
            $arrRender['ModuleName'] = "documents";
            $arrRender['TableName'] = $TableName;
            $arrRender['Recordid'] = $Recordid;
            $arrRender['folderlist'] = $folderlist;
            $arrRender['userlist'] = $userlist;


            //print_r($arrRender);die;

            $this->layout = '@app/views/layouts/main-one';
            $this->renderPartial('@app/views/tetra/callminiform', $arrRender);
        }

        return [
            'success' => false,
            'message' => 'Invalid request method.',
        ];

        // CallInformation


    }
    public function actionSearchusers()
    {
        $connection = Yii::$app->db;
        $TabId = $this->TabId;
        // $command=$connection->createCommand("select targettable,targetfield,dispfield  from picklist where     fieldid=:fieldid")->bindParam(':fieldid'=>$fieldid);
        if (isset($_GET['query']) && !empty($_GET['query'])) {
            if (Yii::$app->request->get('query') != "all" && $TabId != '21')
                $record = Yii::$app->request->get('query') . "%";
            else
                $record = '';
            // $command=$connection->createCommand("select concat(first_name,' ',last_name) as fullname,id from user  where deleted = 0 and first_name like :record ")->bindParam(':record',$record);
            // $Columns = $command->queryAll();

            // $query = Yii::$app->request->get('query')."%";
            $modeluser = new UsersDetails();
            $Columns = $modeluser->getuserlist($record);
            if (!empty($Columns))

                if (!empty($Columns)) {
                    // print_r($Columns);die;
                    echo json_encode($Columns);
                }
        }
    }
    public function actionDownload($fileid)
    {
        $model = new Attachments();
        $records = $model->find()
            ->where(['attachmentsid' => $fileid])
            ->one();
        $fileName = $records['path'];
        //print_r($records);die;

        // Define the base directory for files
        $filePath = Yii::getAlias('@webroot/' . $fileName);

        // Check if the file exists
        if (!file_exists($filePath)) {
            throw new NotFoundHttpException('The requested file does not exist.');
        }

        // Send the file for download
        return Yii::$app->response->sendFile($filePath, $fileName);
    }

    public function getHistory($id, $TabId)
    {
        $TabId = $TabId;
        if (!is_numeric($id)) {
            throw new \yii\web\BadRequestHttpException('Invalid Record ID.');
        }
        /* zitendra update karaya deepika ne SELECT 
                    GROUP_CONCAT(`id`) AS ids,
                    GROUP_CONCAT(`fieldname`) AS fieldnames,
                    GROUP_CONCAT(
                        (SELECT fieldlabel FROM `field` WHERE `tabid` = $TabId AND `fieldname` LIKE modtracker_detail.fieldname limit 1)
                    ) AS fieldlabels,
                    GROUP_CONCAT(
                        (SELECT fieldid FROM `field` WHERE `tabid` = $TabId AND `fieldname` LIKE modtracker_detail.fieldname limit 1)
                    ) AS fieldids,
                    GROUP_CONCAT(
                        (SELECT uitype FROM `field` WHERE `tabid` = $TabId AND `fieldname` LIKE modtracker_detail.fieldname limit 1)
                    ) AS uitypes,
                    GROUP_CONCAT(`prevalue`) AS prevalues,
                    GROUP_CONCAT(`postvalue`) AS postvalues
                FROM 
                    `modtracker_detail` 
                WHERE id = :id and modtracker_detail.fieldname !='modifiedtime'*/

        $connection = Yii::$app->db;

        try {
           /* $command = $connection->createCommand("
                SELECT  
    GROUP_CONCAT(`id` SEPARATOR '~') AS ids,
    GROUP_CONCAT(`fieldname` SEPARATOR '~') AS fieldnames,
    GROUP_CONCAT(
        (SELECT fieldlabel 
         FROM `field` 
         WHERE `tabid` = $TabId 
         AND `fieldname` LIKE modtracker_detail.fieldname 
         LIMIT 1
        ) SEPARATOR '~'
    ) AS fieldlabels,
    GROUP_CONCAT(
        (SELECT fieldid 
         FROM `field` 
         WHERE `tabid` = $TabId 
         AND `fieldname` LIKE modtracker_detail.fieldname 
         LIMIT 1
        ) SEPARATOR '~'
    ) AS fieldids,
    GROUP_CONCAT(
        (SELECT uitype 
         FROM `field` 
         WHERE `tabid` = $TabId 
         AND `fieldname` LIKE modtracker_detail.fieldname 
         LIMIT 1
        ) SEPARATOR '~'
    ) AS uitypes,
    GROUP_CONCAT(`prevalue` SEPARATOR '~') AS prevalues,
    GROUP_CONCAT(`postvalue` SEPARATOR '~') AS postvalues
FROM 
    `modtracker_detail`
WHERE 
    id = :id 
    AND modtracker_detail.fieldname != 'modifiedtime';

                        ")->bindValue(":id", $id);*/
    //code addded by ptpatel on date 03-11-2025 added to compare role in vendor account also
        $command = $connection->createCommand("
        SELECT  
            GROUP_CONCAT(md.id SEPARATOR '~') AS ids,
            GROUP_CONCAT(md.fieldname SEPARATOR '~') AS fieldnames,
            GROUP_CONCAT(
                COALESCE(
                    (SELECT rolename 
                    FROM role 
                    WHERE roleid = md.fieldname 
                    LIMIT 1),
                    (SELECT fieldlabel 
                    FROM field 
                    WHERE tabid = $TabId 
                    AND fieldname = md.fieldname 
                    LIMIT 1),
                    md.fieldname
                )
                SEPARATOR '~'
            ) AS fieldlabels,
            GROUP_CONCAT(
                COALESCE(
                    (SELECT fieldid 
                    FROM field 
                    WHERE tabid = $TabId 
                    AND fieldname = md.fieldname 
                    LIMIT 1),
                    ''
                )
                SEPARATOR '~'
            ) AS fieldids,
            GROUP_CONCAT(
                COALESCE(
                    (SELECT uitype 
                    FROM field 
                    WHERE tabid = $TabId 
                    AND fieldname = md.fieldname 
                    LIMIT 1),
                    ''
                )
                SEPARATOR '~'
            ) AS uitypes,
            GROUP_CONCAT(md.prevalue SEPARATOR '~') AS prevalues,
            GROUP_CONCAT(md.postvalue SEPARATOR '~') AS postvalues
        FROM 
            modtracker_detail md
        WHERE 
            md.id = :id 
            AND md.fieldname != 'modifiedtime';
    ")->bindValue(":id", $id);
    //end code added by ptpatel on date 03-11-2025



            $list = $command->queryAll();
            //print_r($list);die;
            return $list;
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw new \yii\web\ServerErrorHttpException('An error occurred while fetching the record.');
        }
    }


    public function Detailhistory($Record)
    {
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;

        // Validate if $Record is a numeric ID or a comma-separated list of IDs
        $recordArray = array_map('trim', explode(',', $Record));

        if (empty($recordArray) || !array_reduce($recordArray, fn($carry, $id) => $carry && is_numeric($id), true)) {
            throw new \yii\web\BadRequestHttpException('Invalid Record ID(s).');
        }

        $connection = Yii::$app->db;

        try {
            // Fetch basic record data for the given IDs
            $recordPlaceholders = implode(',', array_fill(0, count($recordArray), ':record_id_'));

            // print_r($recordPlaceholders);


            $command = $connection->createCommand("

                        SELECT modtracker_basic.`id`, `crmid`, `module`,targetmodule, (SELECT concat(user.`first_name`,' ',user.`last_name`) as `first_name` FROM `user` WHERE user.id=modtracker_basic.`whodid` limit 1) AS whodid, modtracker_basic.`changedon`, 
                               CASE 
                                   WHEN `status` = 0 THEN 'Created'
                                   WHEN `status` = 1 THEN 'Bulkupdated'
                                   WHEN `status` = 3 THEN 'Added'
                                   WHEN `status` = 4 THEN 'Lead Converted'
                                   WHEN `status` = 5 THEN 'Imported'
                                   WHEN `status` = 6 THEN 'Imported Serial No'
                                   WHEN `status` = 9 THEN 'Updated via Import'
                                   WHEN `status` = 10 THEN 'singleedit'
                                   ELSE 'Updated'
                               END AS status
                        FROM `modtracker_basic`
                        left join modtracker_relations on modtracker_relations.id=modtracker_basic.id
                        WHERE module='$ModuleName' and crmid IN ($Record) ORDER BY changedon DESC;
                    ");




            $columns = $command->queryAll();
            // print_r($columns); die;
            if (!$columns) {
                //throw new \yii\web\NotFoundHttpException('No records found in basic tracker.');
            }

            // Log fetched columns
            Yii::info('Fetched columns: ' . json_encode($columns), __METHOD__);

            $allRecordData = [];

            // Fetch detailed history for each record
            foreach ($columns as $column) {
                $historyData = $this->getHistory($column['id'], $TabId); // Call getHistory for each crmid
                //print_r($historyData);die;


                // Aggregate data
                $allRecordData[] = [
                    'basic' => $column,
                    'details' => $historyData
                ];
            }

            //get related module history 
            // SELECT   CASE 
            //                        WHEN `status` = 0 THEN 'Created'
            //                        WHEN `status` = 1 THEN 'Bulkupdated'
            //                        ELSE 'Updated'
            //                    END AS status,changedon,(SELECT user.`first_name` FROM `user` WHERE user.id=modtracker_basic.`whodid` limit 1) AS whodid FROM `modtracker_basic` INNER JOIN modtracker_detail on modtracker_detail.id = modtracker_basic.id and module = 'notes' INNER JOIN modnotes on modnotes.modnotesid = modtracker_basic.crmid where crmid in (select modnotes.modnotesid from modnotes where related_to = 7 and related_to_id = 25) and status=0 group by status,changedon,crmid;

            // Return all results as JSON
            //return $this->asJson($allRecordData);
            // print_r($allRecordData);die;
            return $allRecordData;
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw new \yii\web\ServerErrorHttpException('An error occurred while processing the request.');
        }
    }
    function actionGetexchangerate()
    {
        $data = $_POST;
        $currency = Yii::$app->request->post('currency');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("

                        SELECT exchange_rate FROM currency WHERE currencyid = :currency
                    ")->bindValue(":currency", $currency);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            return $columns['exchange_rate'];
        } else
            return '';
    }
    function actionGetsendername()
    {
        $data = $_POST;
        $sender_address = Yii::$app->request->post('sender_address');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("

                        SELECT first_name , last_name FROM user WHERE id = :sender_address
                    ")->bindValue(":sender_address", $sender_address);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            return $columns['first_name'] . ' ' . $columns['last_name'];
        } else
            return '';
    }
    function actionGetupcomingevents()
    {
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;


        $Recordid = Yii::$app->request->get('Record');
        $id = Yii::$app->user->id;
        $arrRender['allactivities'] = $this->getAllActivities($Recordid);
        $arrRender['TabId'] = $TabId;
        $arrRender['Recordid'] = $Recordid;

        $this->layout = '@app/views/layouts/main-one';
        $this->renderPartial('@app/views/tetra/upcomingevents-new', $arrRender);
    }
    function actionGetallnotes()
    {
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;


        $Recordid = Yii::$app->request->get('Record');
        $id = Yii::$app->user->id;
        $arrRender['getnotes'] = $this->getnotes($Recordid);
        $arrRender['ModuleName'] = $ModuleName;

        $this->layout = '@app/views/layouts/main-one';
        $this->renderPartial('@app/views/tetra/notesection', $arrRender);
    }
    // added on 23/12/2024
    function actionSetcolumnsequence()
    {
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;
        $Columnids = Yii::$app->request->post('columnIds');
        $uid = Yii::$app->user->id;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        // Get custom view ID for the user and the module
        $userColumns = (new \yii\db\Query())
            ->select(['cvid'])
            ->from('customview')
            ->where(['entitytype' => ucfirst($ModuleName)])
            ->andWhere(['userid' => $uid])
            ->column();

        if (empty($userColumns)) {
            // If no custom view exists for the user, create a new one
            Yii::$app->db->createCommand("
                INSERT INTO `customview` 
                SET viewname = 'All', setdefault = 1, setmetrics = 0, entitytype = :ModuleName, 
                    userid = :uid, tabid = :TabId, status = 0")
                ->bindValue(":ModuleName", ucfirst($ModuleName))
                ->bindValue(":TabId", $TabId)
                ->bindValue(":uid", $uid)
                ->execute();

            // Get the last inserted `cvid`
            $id = Yii::$app->db->getLastInsertID();
        } else {
            //delete previous sequence
            $id = $userColumns[0];
            Yii::$app->db->createCommand("DELETE from cvcolumnlist where cvid=$id")->execute();
            //  echo "DELETE from cvcolumnlist where cvid=$id";die;

        }

        if ($id) {
            // Insert columns from the original `cvid`
            $cvid = (new \yii\db\Query())
                ->select(['cvid'])
                ->from('customview')
                ->where(['entitytype' => ucfirst($ModuleName)])
                ->andWhere(['userid' => 1])
                ->scalar();

            // Yii::$app->db->createCommand("
            //     INSERT INTO cvcolumnlist (cvid, columnindex, columnname, fieldid)
            //     SELECT $id, columnindex, columnname, fieldid
            //     FROM cvcolumnlist
            //     WHERE cvid = $cvid")
            //     ->execute();

            // Now update the sequence safely
            $transaction = Yii::$app->db->beginTransaction();
            try {
                // Step 1: Get current columns and their columnindex
                $currentColumns = (new \yii\db\Query())
                    ->select(['columnname', 'columnindex'])
                    ->from('cvcolumnlist')
                    ->where(['cvid' => $id])
                    ->orderBy('columnindex') // Ensure the current order is preserved
                    ->all();

                // Step 2: Build a new columnindex mapping based on the desired Columnids
                $columnIndexMap = [];
                $newIndex = 0;
                foreach ($Columnids as $columnname) {
                    if ($newIndex > 0) {
                        $columnIndexMap[$columnname] = $newIndex;
                    }
                    $newIndex++;
                }
                // echo "<pre>";
                // print_r($columnIndexMap);
                // echo "<br>";

                // Step 3: Update columnindex values based on the new sequence
                foreach ($columnIndexMap as $columnname => $newIndex) {

                    // Insert into `cvcolumnlist` based on `field` table
                    Yii::$app->db->createCommand("
                        INSERT INTO cvcolumnlist (cvid, columnindex, columnname, fieldid)
                        SELECT :cvid, :columnindex, columnname, fieldid
                        FROM field
                        WHERE tabid = :tabid AND columnname = :columnname")
                        ->bindValue(':cvid', $id) // Bind the cvid parameter
                        ->bindValue(':columnindex', $newIndex) // Bind the new columnindex
                        ->bindValue(':tabid', $TabId) // Bind the tabid parameter
                        ->bindValue(':columnname', $columnname) // Bind the columnname
                        ->execute();
                }

                // Commit the transaction after all updates
                $transaction->commit();
                return [
                    'success' => true,
                    'message' => 'Sequence saved successfully.',
                ];
            } catch (\Exception $e) {
                // Rollback the transaction in case of any error
                $transaction->rollBack();
                // Yii::error("Error updating column sequence: " . $e->getMessage());
                return [
                    'success' => false,
                    'message' => 'Something went wrong',
                ];
            }
        }
    }

    public function setAutoNo($tabs)
    {
        $table_name = $this->ModuleName;
        $model = new AutoNo();
        $upAutoNo = $model->setAutomoduleno($tabs, $table_name);
        return $upAutoNo;
    }
    public function getAutoNo($tabs)
    {
        $table_name = $this->ModuleName;
        $model = new AutoNo();
        $orderno = $model->getautomoduleno($tabs, $table_name);
        return $orderno;
    }
    function updateCRMSequence($semodule, $crmid)
    {
        // echo "UPDATE `modentity_num` SET cur_id = $crmid where semodule='$semodule'" ;die;
        try {
            Yii::$app->db->createCommand("UPDATE `modentity_num` SET cur_id = :crmid where semodule=:semodule")
                ->bindParam(":crmid", $crmid)
                ->bindParam(":semodule", $semodule)
                ->execute();
        } catch (\Exception $e) {
            // Handle the error, e.g. log it or display a message
            Yii::error($e->getMessage());
        }
    }

    public function actionCsvupload()
    {
        $TabId = $this->TabId;
        $TableName = $this->TableName;
        $ModuleName = $this->ModuleName;

        if (Yii::$app->request->isPost) {
            // echo "<pre>";print_r($_POST['bulk_ownerid']);die;
            try {
                $bulk_ownerid = $_POST['bulk_ownerid'] ?? null;
                if (empty($bulk_ownerid)) {
                    Yii::$app->session->setFlash('error', 'Error importing. No Owner Selected.');
                    return $this->redirect(['list']);
                }

                $uploadedFile = UploadedFile::getInstanceByName('file');
                if (!$uploadedFile) {
                    Yii::$app->session->setFlash('error', 'No file uploaded.');
                    return $this->redirect(['list']);
                }

                if ($uploadedFile->extension !== 'csv') {
                    Yii::$app->session->setFlash('error', 'Invalid file type. Please upload a CSV file.');
                    return $this->redirect(['list']);
                }

                // Field rules
                $fields = Yii::$app->db->createCommand("
                SELECT fieldname, typeofdata, isunique,fieldlabel, fieldid, uitype 
                FROM field 
                WHERE tabid = :tabid
            ")->bindValue(':tabid', $TabId)->queryAll();

                $fieldRules = [];
                $isunique = [];

                foreach ($fields as $field) {
                    $fieldRules[$field['fieldname']] = $field['typeofdata'];
                    $isunique[$field['fieldname']] = $field['isunique'];
                    $label[$field['fieldname']] = $field['fieldlabel'];
                    $fieldMeta[$field['fieldname']]  = [
                        'fieldid' => $field['fieldid'],
                        'uitype'  => $field['uitype'],
                    ];
                }

                $connection = Yii::$app->db;
                $command = $connection->createCommand("
                SELECT GROUP_CONCAT(columnname) AS columnname 
                FROM `field` 
                WHERE import = :import AND tabid = :tabid
            ");
                $command->bindValue(':import', 1);
                $command->bindValue(':tabid', $TabId);
                $result = $command->queryOne();

                if (empty($result['columnname'])) {
                    Yii::$app->session->setFlash('error', 'No columns are defined for import in the database.');
                    return $this->redirect(['list']);
                }

                $allowedColumns = explode(',', $result['columnname']);

                // Determine auto-increment column
                $autoIncrementColumn = '';
                $command = $connection->createCommand("
                SELECT columnname 
                FROM `field` 
                WHERE uitype = 11 AND tabid = :tabid
            ");
                $command->bindValue(':tabid', $TabId);
                $result = $command->queryOne();
                if ($result) {
                    $autoIncrementColumn = $result['columnname'];
                }

                $requiredColumns = ['ownerid', 'createdtime', 'creatorid', 'modifiedby', 'modifiedtime', $autoIncrementColumn];
                $csvColumns = array_merge($requiredColumns, $allowedColumns);

                $columnsStr = implode(',', array_map(fn($col) => "`" . trim($col) . "`", $csvColumns));
                $rowCount = 0;
                $transaction = $connection->beginTransaction();

                try {
                    if (($handle = fopen($uploadedFile->tempName, 'r')) === false) {
                        throw new \Exception('Unable to open CSV file.');
                    }

                    // $contactFieldCount = 4;
                    $contactFieldCount = 6;
                    $totalContacts = 3;
                    $totalContactFields = ($TabId == 7) ? $contactFieldCount * $totalContacts : 0;

                    $headers = fgetcsv($handle, 0, ",");
                    $totalColumns = count($headers);

                    $leadFieldCount = $totalColumns - $totalContactFields;

                    while (($row = fgetcsv($handle)) !== false) {
                        $rowCount++;

                        if (count($row) < $totalColumns) {

                            throw new \Exception("Incomplete data at row $rowCount.");
                        }

                        $leadData = array_slice($row, 0, $leadFieldCount);
                        $contactFields = array_slice($row, -$totalContactFields);
                        $contacts = array_chunk($contactFields, $contactFieldCount);

                        foreach ($leadData as $key => $value) {
                            if (empty(trim($value))) {
                                $command = $connection->createCommand("
                                SELECT COUNT(*) AS cnt 
                                FROM `field` 
                                WHERE columnname = :columnname AND import = :import AND tabid = :tabid AND mandatory = 1
                            ");
                                $command->bindValue(':columnname', $allowedColumns[$key]);
                                $command->bindValue(':import', 1);
                                $command->bindValue(':tabid', $TabId);
                                $result = $command->queryOne();

                                if ($result['cnt']) {
                                    throw new \Exception("Blank value detected in column '" . $allowedColumns[$key] . "' at row $rowCount.");
                                }
                            }
                        }


                        $id = Yii::$app->user->id;
                        $timestamp = date('Y-m-d H:i:s');
                        $tableautono = $this->getAutoNo($TabId);

                        $valuesStrArray = [
                            ':ownerid' => $bulk_ownerid,
                            ':createdtime' => $timestamp,
                            ':creatorid' => $id,
                            ':modifiedby' => $id,
                            ':modifiedtime' => $timestamp,
                            ':tableautono' => $tableautono,
                        ];
                        
                        foreach ($allowedColumns as $index => $column) {

                                $rawValue = isset($leadData[$index]) ? trim($leadData[$index]) : '';

                                if ($TabId == 7 && $column === 'lead_source' && $rawValue !== '') {

                                    if (!isset($fieldMeta['lead_source'])) {
                                        throw new \Exception("Lead Source field metadata not found. Please configure the field correctly.");
                                    }

                                    $lsFieldId = $fieldMeta['lead_source']['fieldid'];
                                    $lsUitype  = $fieldMeta['lead_source']['uitype'];

                                    $mappedVal = $this->getdependantValue($TabId,$lsFieldId,$label['lead_source'],$lsUitype,$rawValue,null);

                                    if ($mappedVal === null || $mappedVal === '') {
                                        throw new \Exception(
                                            "No match found for '$rawValue' in field " . $label['lead_source'] . " at CSV Row " . $rowCount
                                        );
                                    }

                                    $value = $mappedVal;
                                } else {
                                    $value = $rawValue;
                                }

                                $valuesStrArray[':column' . $index] = $value;

                                $this->validateFieldByType($rowCount, $value, $fieldRules[$column] ?? '', $label[$column]);

                                if (($isunique[$column] ?? '') == 1 && $value !== '') {
                                    $sql = "SELECT count(*) as cnt FROM `$TableName` WHERE `$column` = :val";
                                    $res = Yii::$app->db->createCommand($sql)
                                        ->bindValue(':val', $value)
                                        ->queryOne();
                                    if ($res['cnt'] > 0) {
                                        throw new \Exception(
                                            "Duplicate detected: The value '$value' for column '$column' already exists in the system (Row $rowCount). Please ensure all values are unique."
                                        );
                                    }
                                }
                            }

                        $extraData = [];
                        if ($TabId == 7) {
                            $extraData = [
                                'leadstatus' => 14,
                                'customer_type' => 1
                            ];
                        }

                        $columnsArray = array_map('trim', explode(',', $columnsStr));
                        $valuesAssocArray = array_combine($columnsArray, $valuesStrArray);
                        $valuesAssocArray = array_merge($valuesAssocArray, $extraData);
                        //echo $valuesAssocArray['`account_category`'];

                        if ($TabId == 18) {

                            // set account code
                            $code = '';
                            if ($valuesAssocArray['`account_category`'] == '2')
                                $code .= "C";
                            if ($valuesAssocArray['`account_category`'] == '1')
                                $code .= "V";
                            if ($valuesAssocArray['`account_category`'] == '3')
                                $code .= "P";
                            $valuesAssocArray['`cust_code`'] = $this->getaccountcode($code);
                        }
                        //  echo "<pre>";
                        // print_r($valuesAssocArray);die;


                        $finalColumns = array_keys($valuesAssocArray);
                        $columnNames = array_map(fn($col) => "$col", $finalColumns);
                        $placeholders = array_map(fn($col) => ":" . str_replace('`', '', $col), $finalColumns);

                        $valuesAssocArray = array_combine(
                            array_map(fn($k) => str_replace('`', '', $k), array_keys($valuesAssocArray)),
                            array_values($valuesAssocArray)
                        );

                        
                        $sql = "INSERT INTO `$TableName` (" . implode(',', $columnNames) . ") VALUES (" . implode(',', $placeholders) . ")";
                        $command = $connection->createCommand($sql);
                        $command->bindValues($valuesAssocArray);
                        $command->execute();

                        $leadId = $connection->getLastInsertID();

                        $modlog = new ModtrackerBasic();
                        $modlog->auditlog('', $valuesAssocArray, $ModuleName, $leadId, 5, $id);

                        $this->updateCRMSequence($ModuleName, $leadId + 1);

                        if ($TabId == 7) {
                            $cnn = 1;
                            foreach ($contacts as $contact) {
                                $firstName = trim($contact[0] ?? '');
                                $lastName = trim($contact[1] ?? '');
                                $mobile = trim($contact[2] ?? '');
                                $email = trim($contact[3] ?? '');
                                //added as per v11 - 33,34 change code added by ptpatel on date 11-10-2025
                                $designation = trim($contact[4] ?? '');
                                $contact_validation = trim($contact[5] ?? '');

                                if (empty($firstName) && empty($lastName) && empty($mobile) && empty($designation) && empty($contact_validation)) {
                                    continue;
                                }
                                //code added by ptpatel on date 13-10-2025 to select designationa and contact validation
                                if ($designation || $contact_validation) {//9-checkbox and 10-radio
                                    try {
                                        if (!empty($designation) || !empty($contact_validation)) {
                                            $fieldid_for_contact ='';
                                            $fieldlabel_for_contact ='';
                                            if($designation)
                                            {
                                               $fieldid_for_contact = 4033;//field id for designation
                                               
                                                $fieldlabel_for_contact ='Designation';
                                                //fetch fieldid of designation
                                                $oldAttributes = Yii::$app->db->createCommand("select fieldid from `field` where columnname=:columname and tabid=:tabid and tablename=:tablename")
                                                    ->bindValue(":columname", 'designation')
                                                    ->bindValue(":tabid", '7')
                                                    ->bindValue(":tablename", 'lead_contacts_detail')
                                                    ->queryOne();
                                                 
                                          
                                                if ($oldAttributes) {
                                                    // Process the result
                                                    $fieldid_for_contact = $oldAttributes['fieldid'];
                                                } else {
                                                    // Handle the case where no result is found
                                                    //Yii::error('No fieldid found for designation.');
                                                     return [
                                                    'success' => false,
                                                    'error' => "No column found for $fieldlabel_for_contact",
                                                ];
                                                }
                                               
                                                //fetch end
                                                $value = $designation;
                                                $designation = $this->getdependantValue(7, $fieldid_for_contact, $fieldlabel_for_contact, 8, $value,null);
                                                if (empty(trim($value))) {
                                                return [
                                                    'success' => false,
                                                    'error' => "No match found for '$value' in field " . $fieldlabel_for_contact . " at CSV Row " . ($index + 1),
                                                ];
                                            }

                                            }
                                            if($contact_validation)
                                            {
                                                $fieldid_for_contact = 4034;//field id for contact valdation
                                                 $fieldlabel_for_contact ='Contact validation';
                                                //fetch fieldid of Contact validation
                                                $oldAttributes = Yii::$app->db->createCommand("select fieldid from `field` where columnname=:columname and tabid=:tabid and tablename=:tablename")
                                                    ->bindValue(":columname", 'contact_validation')
                                                    ->bindValue(":tabid", '7')
                                                    ->bindValue(":tablename", 'lead_contacts_detail')
                                                    ->queryOne();
                                          
                                                if ($oldAttributes) {
                                                    // Process the result
                                                    $fieldid_for_contact = $oldAttributes['fieldid'];
                                                } else {
                                                    // Handle the case where no result is found
                                                    //Yii::error('No fieldid found for contact_validation.');
                                                     return [
                                                    'success' => false,
                                                    'error' => "No column found for $fieldlabel_for_contact",
                                                ];
                                                }
                                                //fetch end
                                                $value = $contact_validation;
                                                $contact_validation = $this->getdependantValue(7, $fieldid_for_contact, $fieldlabel_for_contact, 8, $value,null);
                                                if (empty(trim($value))) {
                                                    return [
                                                        'success' => false,
                                                        'error' => "No match found for '$value' in field " . $fieldlabel_for_contact . " at CSV Row " . ($index + 1),
                                                    ];
                                                }
                                            }
                                        }
                                    } catch (\Exception $e) {
                                        // $transaction->rollBack();
                                        return [
                                            'success' => false,
                                            'error' => "No match found for '$value' in field " . $fieldid_for_contact . " at CSV Row " . ($index + 1),
                                        ];
                                    }
                                }
                                // echo $designation.",".$contact_validation;die;
                                //end code added by ptpatel on date 13-10-2025

                                $this->validateFieldByType($rowCount, $firstName, "AN~M", 'Contact First Name ' . $cnn);
                                $this->validateFieldByType($rowCount, $lastName, "AN~M", 'Contact Last Name ' . $cnn);
                                $this->validateFieldByType($rowCount, $mobile, "MOB~M", 'Contact Mobile ' . $cnn);
                                $this->validateFieldByType($rowCount, $email, "E~O", 'Contact Email ' . $cnn);
                                $this->validateFieldByType($rowCount, $designation, "DD~O", 'Designation ' . $cnn);
                                $this->validateFieldByType($rowCount, $contact_validation, "DD~O", 'Contact Validation ' . $cnn);
                                $cnn++;

                                $contactModel = new LeadContactsDetail();
                                $contactModel->attributes = [
                                    'leadid' => $leadId,
                                    'first_name' => $firstName,
                                    'last_name' => $lastName,
                                    'mobile' => $mobile,
                                    'email' => $email,
                                    'designation' => $designation,
                                    'contact_validation' => $contact_validation,
                                ];

                                if (!$contactModel->save()) {
                                    throw new \Exception("Failed to save contact: " . json_encode($contactModel->getErrors()));
                                }
                            }
                        }
                    }

                    $transaction->commit();
                    // echo "hi";die;
                    fclose($handle);

                    Yii::$app->session->setFlash('success', "$rowCount rows inserted successfully.");
                    return $this->redirect(['list']);
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    if (isset($handle) && is_resource($handle)) {
                        fclose($handle);
                    }
                    throw $e;
                }
            } catch (\Throwable $e) {
                Yii::error([
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ], __METHOD__);

                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->redirect(['list']);
            }
        }

        return $this->render('csvupload');
    }

    // added by deepika on 27 may2025
    function validateFieldByType($rowCount, $value, $typeOfData, $fieldLabel = 'Field')
    {
        list($type, $mandatory) = explode('~', $typeOfData);
        $value = trim($value);

        // Mandatory check
        if ($mandatory == 'M' && $value === '') {
            throw new \Exception("$fieldLabel is mandatory at row $rowCount.");
        }

        // echo "deep".$value;die;

        // Skip further validation if optional and empty
        if ($mandatory == 'O' && $value === '') {
            return;
        }

        // Type-specific validation
        switch ($type) {
            case 'A': // Alphabets
                if (!preg_match('/^[a-zA-Z\s]+$/', $value)) {
                    throw new \Exception("$fieldLabel should contain alphabets only at row $rowCount");
                }
                break;
            case 'AN': // Alphanumeric
                if (!preg_match('/^[a-zA-Z0-9\s]+$/', $value)) {
                    throw new \Exception("$fieldLabel should be alphanumeric at row $rowCount.");
                }
                break;
            case 'NU': // Numeric
                if (!is_numeric($value)) {
                    throw new \Exception("$fieldLabel should be numeric at row $rowCount.");
                }
                break;
            case 'MOB': // Mobile
                if (!preg_match('/^[6-9]\d{9}$/', $value)) {
                    throw new \Exception("$fieldLabel should be a valid 10-digit mobile number at row $rowCount.");
                }
                break;
            case 'E': // Email
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    throw new \Exception("$fieldLabel is not a valid email address at row $rowCount.");
                }
                break;
            case 'URL':
                if (!filter_var($value, FILTER_VALIDATE_URL)) {
                    throw new \Exception("$fieldLabel is not a valid URL at row $rowCount.");
                }
                break;
            case 'DC': // Decimal
                if (!preg_match('/^\d+(\.\d+)?$/', $value)) {
                    throw new \Exception("$fieldLabel should be a valid decimal at row $rowCount.");
                }
                break;
            case 'DT': // Date
                if (!preg_match('/^\d{2}-\d{2}-\d{4}$/', $value)) {
                    throw new \Exception("$fieldLabel should be in DD-MM-YYYY format at row $rowCount.");
                }
                break;
            case 'DTT': // DateTime (Optional parsing)
                // Add more precise validation if needed
                break;
            // Add more types if needed
        }
    }

    // get state
    public function actionGetstate()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
            if (isset($_POST['country'])) {
                $country = intval($_POST['country']); // Sanitize input
                $db = Yii::$app->db;

                // Fetch categories based on country
                $query = "SELECT state_id AS id, state_value AS name FROM state WHERE FIND_IN_SET(:country, country_id) AND is_active = 1 ORDER BY seq_no ASC";
                $command = $db->createCommand($query);
                $command->bindValue(':country', $country);
                $categories = $command->queryAll();

                // Return categories in JSON format
                return ['status' => 'success', 'categories' => $categories];
            } else {
                return ['status' => 'error', 'message' => 'Country is required.'];
            }
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()];
        }
    }
    // get city
    public function actionGetcity()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
            if (isset($_POST['state'])) {
                $state = intval($_POST['state']); // Sanitize input
                $db = Yii::$app->db;

                // Fetch categories based on state
                $query = "SELECT cityid AS id, city_name AS name FROM city WHERE FIND_IN_SET(:state, stateid) AND is_active = 1 ORDER BY seq_no ASC";
                $command = $db->createCommand($query);
                $command->bindValue(':state', $state);
                $categories = $command->queryAll();

                // Fetch categories based on country
                $query = "SELECT state_code,short_code,state_value FROM state WHERE state_id=:state AND is_active = 1 ORDER BY seq_no ASC";
                $command = $db->createCommand($query);
                $command->bindValue(':state', $state);
                $statecode = $command->queryOne();

                // Return categories in JSON format
                return ['status' => 'success', 'categories' => $categories, 'statecode' => $statecode];
            } else {
                return ['status' => 'error', 'message' => 'State is required.'];
            }
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()];
        }
    }

    public function actionGetfilterdropdown()
    {
        $ModuleName = $this->ModuleName;

        $fielduitype = filter_var($_POST['fielduitype'], FILTER_VALIDATE_INT);

        // Validate fieldname and fieldtablename using regular expressions or other methods
        $fieldname = isset($_POST['fieldname']) ? $_POST['fieldname'] : '';
        $fieldtablename = isset($_POST['fieldtablename']) ? $_POST['fieldtablename'] : '';

        // Optionally, sanitize the strings
        $fieldname = filter_var($fieldname, FILTER_SANITIZE_STRING);
        $fieldtablename = filter_var($fieldtablename, FILTER_SANITIZE_STRING);

        //get field detail from field table
        $sql = "select fieldid from field where fieldname = :fieldname and tablename = :fieldtablename and uitype = :fielduitype";
        $command = Yii::$app->db->createCommand($sql)
            ->bindValue(':fieldname', $fieldname)
            ->bindValue(':fieldtablename', $fieldtablename)
            ->bindValue(':fielduitype', $fielduitype)
            ->queryOne();
        // print_r($command);die;
        if ($command) {
            //get valuew from picklist
            $PickList = new Picklist;
            $PickList->fieldid = $command['fieldid'];
            // $Column->blocks[$BlockKey]->fields[$FieldKey]->fieldoptions=$PickList->getPickListOption($table_name);


            $fieldoptions = $PickList->getPickListOption($ModuleName);
            // print_r($fieldoptions);die;
            $opt = '<select id="filterValue" class="form-control">';
            foreach ($fieldoptions as $key => $value) {
                // if ($selesalu == $key)
                //     $sel = "selected";
                // else
                //     $sel = '';
                $sel = '';
                # code...
                $opt .= '<option class="opt-none" ' . $sel . ' value="' . $key . '">' . $value . '</option>';
            }

            $opt .= '</select>';
        } else {
            $opt = '';
        }
        return $opt;
    }

    //code added by ptpatel on date 01-04-25
    public function actionGetmultiplefilterdropdown()
    {
        $ModuleName = $this->ModuleName;

        $fielduitype = filter_var($_POST['fielduitype'], FILTER_VALIDATE_INT);

        // Validate fieldname and fieldtablename using regular expressions or other methods
        $fieldname = isset($_POST['fieldname']) ? $_POST['fieldname'] : '';
        $fieldtablename = isset($_POST['fieldtablename']) ? $_POST['fieldtablename'] : '';

        // Optionally, sanitize the strings
        $fieldname = filter_var($fieldname, FILTER_SANITIZE_STRING);
        $fieldtablename = filter_var($fieldtablename, FILTER_SANITIZE_STRING);

        //get field detail from field table
        $sql = "SELECT f.*, ft.fieldtype 
        FROM field f
        JOIN fieldtype ft ON f.uitype = ft.uitype
        WHERE f.fieldname = :fieldname 
          AND f.tablename = :fieldtablename 
          AND f.uitype = :fielduitype";

        $command = Yii::$app->db->createCommand($sql)
            ->bindValue(':fieldname', $fieldname)
            ->bindValue(':fieldtablename', $fieldtablename)
            ->bindValue(':fielduitype', $fielduitype)
            ->queryOne();
        // print_r($command);die;
        if ($command) {
            //get valuew from picklist
            $PickList = new Multilist;
            $PickList->fieldid = $command['fieldid'];
            // $Column->blocks[$BlockKey]->fields[$FieldKey]->fieldoptions=$PickList->getPickListOption($table_name);

            $fieldoptions = $PickList->getMultiListOption($ModuleName);
            $baseUrl = Yii::$app->HomeUrl;
            // print_r($fieldoptions);die;
            $command["fieldoptions"] = $fieldoptions;
            // <script type="text/javascript" src="/deshwal/admin/thememain/jquery/jquery.min.js"></script>
            // <script type="text/javascript" src="/deshwal/admin/thememain/bootstrap/bootstrap.min.js"></script>
            $opt = '<div id="multipledddata"><link href="/deshwal/admin/thememain/css/bootstrap.min.css" rel="stylesheet">
            <link href="/deshwal/admin/thememain/css/multiple.css" rel="stylesheet">
            <link href="/deshwal/admin/thememain/css/select2.min.css" rel="stylesheet">
            <link href="/deshwal/admin/thememain/css/multilist-dd.css" rel="stylesheet">

            <script type="text/javascript" src="/deshwal/admin/thememain/js/select2.min.js"></script>
            <script type="text/javascript" src="/deshwal/admin/thememain/js/tetra/single-dd.js"></script>
            <script type="text/javascript" src="/deshwal/admin/thememain/js/tetra/multilist-dd.js"></script>';
            $opt .= '<select id="filterValue" multiple class="multySelect form-control ' . $command["typeofdata"] . '">';
            foreach ($fieldoptions as $key => $value) {
                $sel = '';
                # code...
                $opt .= '<option class="opt-none" ' . $sel . ' value="' . $key . '">' . $value . '</option>';
            }

            $opt .= '</select> </div>';
        } else {
            $opt = '';
        }
        return $opt;
    }
    //code ended by ptpatel on date 01-04-25

    public function actionGetnotifications()
    {
        $uid = Yii::$app->user->id; // Get logged-in user ID

        // Fetch unread notifications
        $notifications = Yii::$app->db->createCommand("
        SELECT id, message, source_link, createdtime 
        FROM notification 
        WHERE read_status = 0 AND userid = :uid 
        ORDER BY createdtime DESC
    ")->bindValue(':uid', $uid)->queryAll();

        // Count only notifications with display_status = 0
        $unreadCount = Yii::$app->db->createCommand("
        SELECT COUNT(*) FROM notification 
        WHERE display_status = 0 AND userid = :uid
    ")->bindValue(':uid', $uid)->queryScalar();

        return json_encode([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    // Mark notifications as seen when opening the dropdown
    public function actionMarknotificationsseen()
    {
        $uid = Yii::$app->user->id;

        Yii::$app->db->createCommand("
        UPDATE notification 
        SET display_status = 1 
        WHERE display_status = 0 AND userid = :uid
    ")->bindValue(':uid', $uid)->execute();

        return json_encode(['status' => 'success']);
    }

    // Mark a notification as read when clicked
    public function actionMarknotificationread()
    {
        $uid = Yii::$app->user->id;
        $id = Yii::$app->request->post('id');

        Yii::$app->db->createCommand("
        UPDATE notification 
        SET read_status = 1 
        WHERE id = :id AND userid = :uid
    ")->bindValues([':id' => $id, ':uid' => $uid])->execute();

        return json_encode(['status' => 'success']);
    }

    public function actionUpdatereadstatus()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Retrieve POST parameters
        $id = Yii::$app->request->post('notifId');
        $readStatus = Yii::$app->request->post('read_status');

        if ($id === null) {
            throw new BadRequestHttpException("Missing notification id.");
        }

        // Find the notification record
        $notification = Notifications::findOne($id);
        if (!$notification) {
            return ['success' => false, 'message' => 'Notification not found.'];
        }

        // Update the read status
        $notification->read_status = $readStatus;

        if ($notification->save()) {
            return ['success' => true];
        } else {
            // Return error messages if saving fails
            return ['success' => false, 'message' => 'Unable to update notification.', 'errors' => $notification->getErrors()];
        }
    }

    //addeed by ptpatel on 13-03-25
    public function actionSingleedit()
    {
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;
        $TabId = $this->TabId;
        $layout = $this->layout;

        $modelData = [];
        if (Yii::$app->request->post()) {
            $sourceid = Yii::$app->request->get('sourceid') ?? null;
            $sourcemodule = Yii::$app->request->get('sourcemodule') ?? null;
            $from_page = Yii::$app->request->post('from');
            $arrRender['sourceid'] = $sourceid;
            $arrRender['sourcemodule'] = $sourcemodule;
            $arrRender['modulename'] = $ModuleName;
            $arrRender['tablename'] = $TableName;
            $arrRender['columnname'] = Yii::$app->request->post('columnname');
            $arrRender['field'] = Field::find()->where(['uitype' => Yii::$app->request->post('uitype')])
                ->andWhere(['tabid' => Yii::$app->request->post('tabid')])
                ->andWhere(['fieldid' => Yii::$app->request->post('fieldid')])
                ->asArray()->one();
            $uid = Yii::$app->user->id;
            $arrRender['field']['recordid'] = Yii::$app->request->post('recordid');
            $arrRender['TabId'] = $TabId;
            $arrRender['uid'] = $uid;
            $actionid = "edit";
            $RecordId = Yii::$app->request->post('recordid');
            // echo $ModuleName;die;
            $model = new EditModel($TableName, $FieldId, $ModuleName, $actionid);
            $model->_members[$FieldId] = $RecordId;
            $arrRender['model'] = $model;
            $this->getClientScript($ModuleName, strtolower($actionid));
            //if isset sourceid and sourcemodule check related field name 
            $relatedkeys = $model->getralatedkeys($TabId);
            $id = Yii::$app->user->id;
            $accessmodel = new AccessCheck();
            $tabs = $accessmodel->tabs($id, $ModuleName);
            $profile = $accessmodel->profile($id, $tabs, $ModuleName);
            $modelaccess = $accessmodel->moduleaccess($id, $profile, $tabs);
            $rolebasedrecord = $accessmodel->rolebasedrecord($id, $profile);
            $hasadminpower = $accessmodel->hasadminpower($profile);
            list($Column, $Record) = $model->getFieldDetail($rolebasedrecord);
            // echo "<pre>";print_r($Record);exit;
            $this->layout = '@app/views/layouts/main-one';
            $this->renderPartial('@app/views/tetra/EditSummeryField', ['arrRender' => $arrRender, 'Record' => $Record, 'sourcemodule' => $sourcemodule, 'sourceid' => $sourceid, 'relatedkeys' => $relatedkeys, 'from_page' => $from_page]);
        }
    }


    // //function added by ptpatel on 15-03-25
    public function actionSearchinallmodule()
    {
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;

        if (Yii::$app->request->isAjax) {
            $search = Yii::$app->request->get('search');
            $searchModule = Yii::$app->request->get('selectedmodule');
            $tabid = Yii::$app->request->get('tabid');
            $model = new AccessCheck();
            $id = Yii::$app->user->id;
            $tabs = $model->tabs($id, $ModuleName);
            $profile = $model->profile($id, $tabs, $ModuleName);
            $modelaccess = $model->moduleaccess($id, $profile, $tabs);
            $rolebasedrecord = $model->rolebasedrecord($id, $profile);
            $hasadminpower = $model->hasadminpower($profile);
            // echo $hasadminpower;die;
            $modulepermission = $model->modulepermission($profile, $tabs);
            // echo "<pre>";print_R($modulepermission);die;
            $action = "List";
            $arrRender = $tabarr = array();
            // echo $searchModule;die;
            if ($tabid != 'all') {
                $getdetails = Tab::find()->select(['tablename', 'tablekeyid', 'name', 'tablabel', 'tabid'])->where(['presence' => 0, 'tabid' => $tabid])->one();
                $searchmodel = new SearchModel($getdetails->tablename, $getdetails->tablekeyid, $getdetails->name);
                $this->getClientScript($ModuleName, strtolower($action));
                $ActionList = $searchmodel->getActionList($ModuleName);
                $ActionList['OrderBy'] = Yii::$app->request->get('OrderBy');
                $ActionList['SortOrder'] = Yii::$app->request->get('SortOrder');
                $ColumnList = $searchmodel->getColumnList();
                list($Column, $RecordList, $totalitemcount) = $searchmodel->getsearchAllListRecord($TableName, $search, $ActionList['OrderBy'], $ActionList['SortOrder'], $rolebasedrecord, $modulepermission);

                $arrRender['Column'] = $Column;
                $arrRender['RecordList'] = $RecordList;
                $arrRender['totalitemcount'] = $totalitemcount;
                $arrRender['modulename'] = ucfirst($getdetails->tablabel);
                // $arrRender['ori_modulename'] = $ModuleName;
                $arrRender['ori_modulename'] = $getdetails->name;
                $arrRender['tabid'] = $getdetails->tabid;
                // echo "<pre>";print_r($arrRender);die;
            } else //($tabid == 'all')
            {

                if ($hasadminpower == 1) {
                    $getdetailsArr = Tab::find()->select(['tablename', 'tablekeyid', 'name', 'tablabel', 'tabid'])
                        ->where(['presence' => 0, 'search_allowed' => 1])
                        //->andWhere(['NOT IN', 'tabid', [4,6,37, 38, 41,58,59,60,61,62,63,66,67,68,69,70,10,72,73,75,79,80,81,82,83,84,85,86,87,89,85,88,80,33,77,76,90,89]])
                        ->all();
                } else {
                    $getdetailsArr = Tab::find()->select(['t.tablename', 't.tablekeyid', 't.name', 't.tablabel', 't.tabid'])
                        ->alias('t')
                        ->join('INNER JOIN', 'profile2tab p2t', 'p2t.tabid = t.tabid')
                        ->join('INNER JOIN', 'profile p', 'p.profileid = p2t.profileid')
                        ->join('INNER JOIN', 'role2profile r2p', 'r2p.profileid = p.profileid')
                        ->join('INNER JOIN', 'role r', 'r.roleid = r2p.roleid')
                        ->join('INNER JOIN', 'user2role u2r', 'u2r.roleid = r.roleid')
                        ->join('INNER JOIN', 'user u', 'u.id = u2r.userid')
                        ->join('LEFT JOIN', 'submenu s', 's.submenu_id = t.submenu')
                        ->where([
                            't.presence' => 0,
                            't.visible' => 0,
                            'u.id' => $id,
                            'p2t.permissions' => 0,
                            't.search_allowed' => 1
                        ])
                        ->groupBy('t.tabid')
                        ->orderBy('t.tabsequence')
                        ->all();
                }
                foreach ($getdetailsArr as $arr) {
                    // echo "<pre>";print_r($arr);die;
                    // && $arr->tablename !='products' 
                    // && $arr->tablename != 'purchase_order' && $arr->tablename != 'vendor_account' 
                    // && $arr->tablename != 'contacts' &&  $arr->tablename !='meeting_information'
                    // && $arr->tablename != 'task_information'
                    if ($arr->tablename != "" && $arr->tablename != null) { //because data wiping pricing no tablename are there
                        $searchmodel = new SearchModel($arr->tablename, $arr->tablekeyid, $arr->name);
                        $this->getClientScript($ModuleName, strtolower($action));
                        $ActionList = $searchmodel->getActionList($ModuleName);
                        $ActionList['OrderBy'] = Yii::$app->request->get('OrderBy');
                        $ActionList['SortOrder'] = Yii::$app->request->get('SortOrder');
                        $ColumnList = $searchmodel->getColumnList();
                        list($Column, $RecordList, $totalitemcount) = $searchmodel->getsearchAllListRecord($TableName, $search, $ActionList['OrderBy'], $ActionList['SortOrder'], $rolebasedrecord, $modulepermission);
                        if (count($RecordList) > 0) {
                            $arrR = array();
                            $arrR['Column'] = $Column;
                            $arrR['RecordList'] = $RecordList;
                            $arrR['totalitemcount'] = $totalitemcount;
                            $arrR['modulename'] = ucfirst($arr['tablabel']);
                            $arrR['ori_modulename'] = $arr['name'];
                            $arrR['tabid'] = $arr['tabid'];
                            array_push($tabarr, $arrR);
                        }

                        // if($arr->tablename !='products')
                        //     break;
                    }
                }
                // die;
            }
            // $this->layout = '@app/views/layouts/main-one';
            // $this->render('@app/views/tetra/searchresult', ['ModuleName' => $ModuleName]);
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($tabid != 'all') {
                return [
                    'status' => 'success',
                    'search' => 'single',
                    'result' => $arrRender,
                ];
            } else {
                return [
                    'status' => 'success',
                    'search' => 'all',
                    'result' => $tabarr,
                ];
            }
        }
        // Fetch the latest record from the DB
        $this->layout = '@app/views/layouts/main-one';
        $this->render('@app/views/tetra/searchresult', ['ModuleName' => $ModuleName]);
    }
    function convertToUcfirstOrPascalCase($string)
    {
        // Check if the string contains underscores
        if (strpos($string, '_') !== false) {
            // Convert to PascalCase by splitting, capitalizing each part, and joining
            return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
        } else {
            // Capitalize the first letter of the string
            return ucfirst($string);
        }
    }

    function numberToWords($number)
    {
        $no = floor($number);
        $point = round($number - $no, 2) * 100;
        $hundred = null;
        $digits_1 = strlen($no);
        $i = 0;
        $str = [];
        $words = array(
            0 => '',
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
            10 => 'ten',
            11 => 'eleven',
            12 => 'twelve',
            13 => 'thirteen',
            14 => 'fourteen',
            15 => 'fifteen',
            16 => 'sixteen',
            17 => 'seventeen',
            18 => 'eighteen',
            19 => 'nineteen',
            20 => 'twenty',
            30 => 'thirty',
            40 => 'forty',
            50 => 'fifty',
            60 => 'sixty',
            70 => 'seventy',
            80 => 'eighty',
            90 => 'ninety'
        );
        $digits = ['', 'hundred', 'thousand', 'lakh', 'crore'];
        while ($i < $digits_1) {
            $divider = ($i == 2) ? 10 : 100;
            $number_part = $no % $divider;
            $no = floor($no / $divider);
            $i += ($divider == 10) ? 1 : 2;
            if ($number_part) {
                $plural = (($counter = count($str)) && $number_part > 9) ? 's' : null;
                $str[] = ($number_part < 21) ? $words[$number_part] .
                    " " . $digits[$counter] . $plural :
                    $words[floor($number_part / 10) * 10]
                    . " " . $words[$number_part % 10] . " " . $digits[$counter] . $plural;
            } else {
                $str[] = null;
            }
        }
        $str = array_reverse($str);
        $result = implode(" ", array_filter($str));
        $decimal = ($point) ? "and " . $words[floor($point / 10) * 10] . " " . $words[$point % 10] . " Paise" : '';
        return ucwords(trim($result . " Rupees " . $decimal));
    }

    //function added by ptpatel on date 08-04-25
    function actionGetkanbandata()
    {
        $sourceid = Yii::$app->request->get('sourceid');
        $sourcemodule = Yii::$app->request->get('sourcemodule');
        // Get pagination parameters from the request
        $start = Yii::$app->request->get('start', 0); // Start index, default to 0
        $limit = Yii::$app->request->get('limit', 10); // Limit (number of records), default to 10

        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;
        $layout = $this->layout;

        $model = new AccessCheck();
        $id = Yii::$app->user->id;
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $modulepermission = $model->modulepermission($profile, $tabs);

        $columnId = Yii::$app->request->get('column_id');
        $userColumns = (new \yii\db\Query())
            ->select(['cvid'])
            ->from('customview')
            ->where(['entitytype' => $ModuleName])
            ->andWhere(['userid' => Yii::$app->user->id])
            ->column();
        //    print_r($userColumns);die;
        if (empty($userColumns)) {
            //get admin cvid 
            $adminColumns = (new \yii\db\Query())
                ->select(['cvid'])
                ->from('customview')
                ->where(['entitytype' => $ModuleName])
                ->andWhere(['userid' => 1])
                ->column();
            $admincvid = $adminColumns[0];

            // If no custom view exists for the user, create a new one
            Yii::$app->db->createCommand("
                    INSERT INTO `customview` 
                    SET viewname = 'All', setdefault = 1, setmetrics = 0, entitytype = :ModuleName, 
                        userid = :uid, tabid = :TabId, status = 0")
                ->bindValue(":ModuleName", ucfirst($ModuleName))
                ->bindValue(":TabId", $TabId)
                ->bindValue(":uid", Yii::$app->user->id)
                ->execute();

            // Get the last inserted `cvid`
            $cvid = Yii::$app->db->getLastInsertID();
            Yii::$app->db->createCommand("
                INSERT INTO cvcolumnlist (cvid, columnindex, columnname, fieldid)
                SELECT $cvid, columnindex, columnname, fieldid
                FROM cvcolumnlist
                WHERE cvid = $admincvid")
                ->execute();
        } else {
            $cvid = $userColumns[0];
        }
        // Get the columns saved for the current user
        $savedColumns = (new \yii\db\Query())
            ->select(['fieldid'])
            ->from('cvcolumnlist')
            ->where(['cvid' => $cvid])
            ->column();


        // Get all columns for the 'leaddetails' table
        $columns = (new \yii\db\Query())
            ->select(['columnname', 'columnname AS field', 'fieldlabel AS headerName', 'fieldid'])
            ->from('field')
            ->where(['tabid' => $TabId, 'list_view' => 1])
            ->orderBy(['sequence' => SORT_ASC])
            ->all();
        // print_r($columns);die;

        // Set visibility based on whether column is in saved columns
        foreach ($columns as &$column) {
            $column['visible'] = in_array($column['fieldid'], $savedColumns);
        }
        $model = new ListModel($TableName, $FieldId, $ModuleName);
        $filed_name = $model->getfilterColumnList();
        $kanbnacolumn = $model->getKanbanList();
        // echo "<pre>";print_r($kanbnacolumn);die;

        if ($kanbnacolumn) {
            // print_r($kanbnacolumn);die;
            //fetch from picklist
            $fieldid = $kanbnacolumn['fieldid'];
            $PickListDetail = $model->getPickListDetail($fieldid);
            $targettable = $PickListDetail['targettable'];
            $targetfield = $PickListDetail['targetfield'];
            $dispfield = $PickListDetail['dispfield'];
            $kanbanstatus = (new \yii\db\Query())
                ->select([$targetfield, $dispfield])
                ->from($targettable)
                ->where(['is_active' => 1])
                // ->where(['>', 'pipeline_status', 0])
                ->orderBy(['seq_no' => SORT_ASC])
                ->all();
            // code added by ptpatel on date 07-04-25
            $eachStatusCounts = (new \yii\db\Query())
                ->select([
                    'ls.' . $targetfield,
                    'ls.' . $dispfield . ',
                     COUNT(li.' . $kanbnacolumn['fieldname'] . ') AS total'
                ])
                ->from(['li' => $TableName])
                ->leftJoin(['ls' => $targettable], 'ls.' . $targetfield . ' = li.' . $kanbnacolumn['fieldname'])
                ->groupBy('li.' . $kanbnacolumn['fieldname'])
                ->all();

            //end code added by ptpatel on date 07-04-25
            $arrRender['kanbnacolumn'] = $kanbnacolumn['fieldname'];
            $arrRender['leadStatuses'] = $kanbanstatus;
            $arrRender['kanbanstatusid'] = $targetfield;
            $arrRender['kanbanstatusvalue'] = $dispfield;
            $arrRender['kanbancolumn'] = $kanbnacolumn;

            $ActionList = $model->getActionList($ModuleName);
            $ActionList['OrderBy'] = ''; //Yii::$app->request->get('OrderBy');
            $ActionList['SortOrder'] = ''; //Yii::$app->request->get('SortOrder');
            $curPageNo = ''; //$_REQUEST['pagejump'];
            $where = $columnId;
            list($ColumnList, $RecordList, $totalitemcount) = $model->getkanbanListRecord($ActionList['OrderBy'], $ActionList['SortOrder'], $rolebasedrecord, $modulepermission, $where);
            $arrRender['leadInformation'] = $RecordList;
            //added by ptpatel on date 07-04-25 to show labels
            $arrRender['totalitemcount'] = $totalitemcount;
            $arrRender['ColumnList'] = $ColumnList;
            //  $arrRender['eachStatusCounts'] = $eachStatusCounts;
        }

        return $this->renderPartial('@app/views/tetra/kanbancolumncards', [
            'leads' => $arrRender,
        ]);
    }

    public function actionGetautofieldproductlist()
    {

        $block_id = filter_var(Yii::$app->request->get('blockid'), FILTER_SANITIZE_NUMBER_INT);
        $cnt_rows = filter_var(Yii::$app->request->get('cnt_rows'), FILTER_SANITIZE_NUMBER_INT);
        $inventory_id = filter_var(Yii::$app->request->get('inventory_id'), FILTER_SANITIZE_NUMBER_INT);
        $dependent_table = Yii::$app->request->get('tablename');

        // print_r(Yii::$app->request->get('tablename'));die;
        //   	$ModuleName="Tetra";
        $action = "Edit";
        // $this->getClientScript($ModuleName,$action);
        //              Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/Tetra/jqdt.js', CClientScript::POS_END);		
        $arrRender = array();

        $FieldId = $this->FieldId;
        // $ModuleName = $this->ModuleName;
        $ModuleName = $dependent_table;
        // echo $ModuleName;die;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;
        $layout = $this->layout;
        // echo $uid=Yii::$app->session[Yii::$app->params['dirName'].'_id'];die;
        $id = Yii::$app->user->id;
        $uid = Yii::$app->params['dirName'];

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $hasadminpower = $model->hasadminpower($profile);
        $modulepermission = $model->modulepermission($profile, $tabs);
        // print_r($rolebasedrecord);
        $actionid = "create";
        $Block = new Blocks;
        $Block->blockid = $block_id;
        $BlockDetail = $Block->getBlockDetail($ModuleName);

        // echo "xdgdf<pre>";
        // print_r($BlockDetail);die;


        $arrRender['block'] = $BlockDetail;
        $arrRender['profile'] = $profile;
        $arrRender['uid'] = $id;
        $arrRender['tabs'] = $tabs;
        $arrRender['hasadminpower'] = $hasadminpower;
        $arrRender['TableName'] = $TableName;
        $arrRender['FieldId'] = $FieldId;
        $arrRender['action_name'] = $actionid;
        $arrRender['TabLabel'] = $TabLabel;
        $arrRender['ModuleName'] = $ModuleName;
        $arrRender['cnt_rows'] = $cnt_rows;
        $data = $connection = Yii::$app->db;

        $rows = (new \yii\db\Query())
            ->from($dependent_table)
            ->where([$dependent_table . '_id' => $inventory_id])
            // ->where(['grn_no' => $detail_id])
            ->one(Yii::$app->db);

        $data = (object) $rows;

        $arrRender['Record'] = $data;

        // echo "xdgdf<pre>";
        // print_r($arrRender['Record'])	;die;
        // $this->layout = 'main';

        // $this->layout = '@app/views/layouts/main'; 
        // $this->render('@app/views/tetra/EditView',$arrRender);

        $this->layout = '@app/views/layouts/main-new';
        // $this->render('@app/views/tetra/EditView-old',$arrRender);
        // echo $layout;die;

        $this->renderAjax('@app/views/tetra/auotfieldproductlist', $arrRender);

        // return $this->render('index');
    }

    //for cleaning and sticker removal auto save functionality
    public function actionUpdateinventory()
    {
        $ModuleName = $this->ModuleName;
        $modelleadetail = new Inventory();
        $item['tag_number'] = Yii::$app->request->post('tag_number');
        $item['bin_number'] = Yii::$app->request->post('bin_number');
        $item['inventory_id'] = Yii::$app->request->post('inventory_id');
        $item['status'] = Yii::$app->request->post('status_id');
        // echo "<pre>";print_r($item);die;
        $modelleadetail->updateInventoryStatus($item, $ModuleName);

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            'success' => true,
        ];
    }

    //inventory report

    public function actionExportalldata()
    {
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;


        // Fetch necessary configurations
        $model = new AccessCheck();
        $id = Yii::$app->user->id;
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $modulepermission = $model->modulepermission($profile, $tabs);

        // Read selectedRowIds from the request
        $selectedIds = Yii::$app->request->post('selectedRowIds', []);
        $subcategory_id = Yii::$app->request->post('subcategory_id');
        // $selectedIds = array_map('strval', $selectedIds); // Normalize to strings

        //this line need for reporting module like sourcingdealreport
        $ModuleName = preg_replace('/report$/', '', $ModuleName);

        $listModel = new ListModel($TableName, $FieldId, $ModuleName);
        $ActionList = $listModel->getActionList($ModuleName);
        if ($TabId == 77) //inventory ageing
        {
            if (isset($subcategory_id) && $subcategory_id != '') {
                $ColumnList = [
                    'grn_date' => 'GRN Date',
                    'lot_no' => 'Lot No.',
                    'account_name' => 'Account Name',
                    'product_name' => 'Product',
                    'qty' => 'Quantity',
                    'sub_catagory_value' => 'Sub Category',
                    'day_0_15' => '0-15 Days',
                    'day_16_30' => '16-30 Days',
                    'day_31_60' => '31-60 Days',
                    'day_61_90' => '61-90 Days',
                    'day_91_180' => '91-180 Days',
                    'day_180_plus' => '>180 Days',
                    'total_value' => 'Total Value',
                ];
                $RecordList = $result = (new \yii\db\Query())
                    ->select([
                        'DATE_FORMAT(rep_inventory_ageing.grn_date, "%d-%m-%Y") AS grn_date',
                        'rep_inventory_ageing.lot_no',
                        'vendor_account.account_name',
                        'products.product_name',
                        'rep_inventory_ageing.qty',
                        'prod_sub_catagory.sub_catagory_value',
                        new \yii\db\Expression("CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 0 AND 15 THEN rep_inventory_ageing.amount ELSE 0 END AS day_0_15"),
                        new \yii\db\Expression("CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 16 AND 30 THEN rep_inventory_ageing.amount ELSE 0 END AS day_16_30"),
                        new \yii\db\Expression("CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 31 AND 60 THEN rep_inventory_ageing.amount ELSE 0 END AS day_31_60"),
                        new \yii\db\Expression("CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 61 AND 90 THEN rep_inventory_ageing.amount ELSE 0 END AS day_61_90"),
                        new \yii\db\Expression("CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 91 AND 180 THEN rep_inventory_ageing.amount ELSE 0 END AS day_91_180"),
                        new \yii\db\Expression("CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) > 180 THEN rep_inventory_ageing.amount ELSE 0 END AS day_180_plus"),
                        new \yii\db\Expression("rep_inventory_ageing.amount AS total_value"),
                    ])
                    ->from('rep_inventory_ageing')
                    ->leftJoin('prod_sub_catagory', 'prod_sub_catagory.sub_catagory_id = rep_inventory_ageing.subcategory')
                    ->leftJoin('products', 'products.products_id = rep_inventory_ageing.product_name')
                    ->innerJoin('vendor_account', 'rep_inventory_ageing.account_name = vendor_account.vendoraccid')
                    ->where(['rep_inventory_ageing.subcategory' => $subcategory_id])
                    ->all();

                // print_r($result);die;

            } else {
                $ColumnList = [
                    'sub_catagory_value' => 'Sub Category',
                    'qty' => 'Quantity',
                    'uom_value' => 'UOM',
                    'amt_0_15' => '0-15 Days',
                    'amt_16_30' => '16-30 Days',
                    'amt_31_60' => '31-60 Days',
                    'amt_61_90' => '61-90 Days',
                    'amt_91_180' => '91-180 Days',
                    'amt_180_plus' => '>180 Days',
                    'total_value' => 'Total Value',
                ];
                $RecordList = (new Query())
                    ->select([
                        'rep_inventory_ageing.subcategory',
                        'prod_sub_catagory.sub_catagory_value',
                        'SUM(rep_inventory_ageing.qty) AS qty',
                        'SUM(rep_inventory_ageing.amount) AS total_value',
                        'prod_uom.uom_value',
                        'SUM(CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 0 AND 15 THEN rep_inventory_ageing.amount ELSE 0 END) AS amt_0_15',
                        'SUM(CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 16 AND 30 THEN rep_inventory_ageing.amount ELSE 0 END) AS amt_16_30',
                        'SUM(CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 31 AND 60 THEN rep_inventory_ageing.amount ELSE 0 END) AS amt_31_60',
                        'SUM(CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 61 AND 90 THEN rep_inventory_ageing.amount ELSE 0 END) AS amt_61_90',
                        'SUM(CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 91 AND 180 THEN rep_inventory_ageing.amount ELSE 0 END) AS amt_91_180',
                        'SUM(CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) > 180 THEN rep_inventory_ageing.amount ELSE 0 END) AS amt_180_plus',
                    ])
                    ->from('rep_inventory_ageing')
                    ->leftJoin('prod_sub_catagory', 'prod_sub_catagory.sub_catagory_id = rep_inventory_ageing.subcategory')
                    ->leftJoin('prod_uom', 'prod_uom.uom_id = rep_inventory_ageing.uom')
                    ->innerJoin('vendor_account', 'rep_inventory_ageing.account_name = vendor_account.vendoraccid')
                    ->groupBy('rep_inventory_ageing.subcategory')
                    ->all();
            }
        } else if ($TabId == 80) //clubbed inventory
        {
            $ColumnList = [
                'prod_category_value' => 'Category',
                'sub_catagory_value' => 'Sub Category',
                'qty' => 'Quantity',
                'uom_value' => 'UOM',
                'purchase_value' => 'Purchase Value',
                'location_code_value' => 'Location Code',
                'location_floor_value' => 'Location Floor',
            ];
            $RecordList = (new Query())
                ->select([
                    "$TableName.*",
                    'prod_category.prod_category_value',
                    'prod_sub_catagory.sub_catagory_value',
                    'prod_uom.uom_value',
                    'seg_location_code.location_code_value',
                    'seg_location_floor.location_floor_value',
                ])
                ->from('' . $TableName . '')
                ->leftJoin('prod_sub_catagory', 'prod_sub_catagory.sub_catagory_id = ' . $TableName . '.subcategory')
                ->leftJoin('prod_category', 'prod_category.prod_category_id = ' . $TableName . '.category')
                ->leftJoin('prod_uom', 'prod_uom.uom_id = ' . $TableName . '.uom')
                ->innerJoin('seg_location_code', '' . $TableName . '.location_code = seg_location_code.location_code_id')
                ->innerJoin('seg_location_floor', '' . $TableName . '.location_floor = seg_location_floor.location_floor_id')
                // ->groupBy( $TableName . '.category')
                ->all();
        } else if ($TabId == 123) //clubbed inventory
        {
            $ColumnList = [
                'prod_model_value' => 'Model',
                'prod_category_value' => 'Category',
                'sub_catagory_value' => 'Sub Category',
                'qty' => 'Quantity',
                'uom_value' => 'UOM',
                'purchase_value' => 'Purchase Value',
                'location_code_value' => 'Location Code',
                'location_floor_value' => 'Location Floor',
            ];
            $RecordList = (new Query())
                ->select([
                    "$TableName.*",
                    'prod_model.prod_model_value',
                    'prod_category.prod_category_value',
                    'prod_sub_catagory.sub_catagory_value',
                    'prod_uom.uom_value',
                    'seg_location_code.location_code_value',
                    'seg_location_floor.location_floor_value',
                ])
                ->from('' . $TableName . '')
                ->leftJoin('prod_model', 'prod_model.prod_model_id = ' . $this->TableName . '.modelno')
                ->leftJoin('prod_sub_catagory', 'prod_sub_catagory.sub_catagory_id = ' . $TableName . '.subcategory')
                ->leftJoin('prod_category', 'prod_category.prod_category_id = ' . $TableName . '.category')
                ->leftJoin('prod_uom', 'prod_uom.uom_id = ' . $TableName . '.uom')
                ->innerJoin('seg_location_code', '' . $TableName . '.location_code = seg_location_code.location_code_id')
                ->innerJoin('seg_location_floor', '' . $TableName . '.location_floor = seg_location_floor.location_floor_id')
                // ->groupBy( $TableName . '.category')
                ->all();
        } else if ($TabId == 95) //user login activity
        {
            $ColumnList = [
                'user_id' => 'User',
                'activity' => 'Activity',
                'ip_address' => 'IP Address',
                'user_agent' => 'User Agent',
                'created_at' => 'Time',
            ];
            $RecordList = (new Query())
                ->select([
                    "$TableName.*",
                    'CONCAT( user.first_name, " ", user.last_name) as user_id',
                ])
                ->from('' . $TableName . '')
                ->leftJoin('user', 'user.id = ' . $TableName . '.user_id')
                ->all();
        } else {
            list($ColumnList, $RecordList, $totalitemcount) = $listModel->getExportAllRecord(
                $ActionList['OrderBy'] ?? '',
                $ActionList['SortOrder'] ?? '',
                $rolebasedrecord,
                $modulepermission,
            );
            // echo "<pre>";print_r($ColumnList);die;
        }
        // Debugging logs
        Yii::info('Selected IDs: ' . print_r($selectedIds, true), 'export');
        Yii::info('Record List: ' . print_r($RecordList, true), 'export');
        // [oem_role_user_names] => 
        //     [org_role_user_names] => 

        if ($TabId == 18) {
            $allRoles = [];

            // Step 1: Collect all unique role names
            foreach ($RecordList as $record) {
                foreach (['oem_role_user_names', 'org_role_user_names'] as $field) {
                    if (!empty($record[$field])) {
                        $pairs = explode(',', $record[$field]);
                        foreach ($pairs as $pair) {
                            $parts = explode('-', trim($pair), 2);
                            if (count($parts) == 2) {
                                $role = trim($parts[0]);
                                $allRoles[$role] = true;
                            }
                        }
                    }
                }
            }

            $uniqueRoles = array_keys($allRoles); // new dynamic columns
            $finalDataMap = [];

            // Step 2: Build a map of role-based data by RecordId
            foreach ($RecordList as $record) {
                $row = ['record_id' => $record['RecordId']];

                foreach ($uniqueRoles as $role) {
                    $row[$role] = '';
                }

                foreach (['oem_role_user_names', 'org_role_user_names'] as $field) {
                    if (!empty($record[$field])) {
                        $pairs = explode(',', $record[$field]);
                        foreach ($pairs as $pair) {
                            $parts = explode('-', trim($pair), 2);
                            if (count($parts) == 2) {
                                $role = trim($parts[0]);
                                $user = trim($parts[1]);
                                $row[$role] = $user; // or concatenate if needed
                            }
                        }
                    }
                }

                $finalDataMap[$record['RecordId']] = $row;
            }

            // Step 3: Merge finalDataMap into RecordList by RecordId and remove unwanted fields
            foreach ($RecordList as &$record) {
                $id = $record['RecordId'];
                unset($record['oem_role_user_names'], $record['org_role_user_names']); // remove fields

                if (isset($finalDataMap[$id])) {
                    $record = array_merge($record, $finalDataMap[$id]);
                    unset($record['record_id']);
                }
                unset($record['isEdit']);
                unset($record['RecordId']);
            }
            unset($record); // clean reference
        }

        //row code complete


        // echo "<pre>";print_r($RecordList);die;
        // echo "<pre>=====================";
        // print_r($finalData[154]);die;
        // Filter records
        // $filteredRecords = array_filter($RecordList, function ($record) use ($selectedIds) {
        //     return in_array((string) $record['RecordId'], $selectedIds);
        // });

        Yii::info('Filtered Records: ' . print_r($RecordList, true), 'export');

        if (empty($RecordList)) {
            return $this->asJson([
                'success' => true,
                'message' => 'No records found.',
            ]);
        }

        // Extract headers dynamically
        $headers = array_values($ColumnList);
        if (!empty($uniqueRoles) && $TabId == 18) {
            $headers = array_values(array_unique(array_merge($headers, $uniqueRoles)));
        }
        // echo "<pre>";print_r($headers);die;

        // Map filtered records to dynamic headers
        if ($TabId == 18) {
            // Step 1: Start with known field labels
            $headers = $ColumnList; // already key => label

            // Step 2: Detect extra keys from data (like dynamic role fields)
            $allRecordKeys = [];
            foreach ($RecordList as $record) {
                $allRecordKeys = array_merge($allRecordKeys, array_keys($record));
            }
            $allRecordKeys = array_unique($allRecordKeys);

            foreach ($allRecordKeys as $key) {
                if (!isset($headers[$key])) {
                    $headers[$key] = $key; // fallback: use key as label
                }
            }

            // Step 4: Get ordered keys and headers
            $finalKeys = array_keys($headers);
            $headers = array_values($headers); // for export/csv/etc.
            // unset($finalKeys
            // Step 5: Build rows for each record
            $rows = array_map(function ($record) use ($finalKeys) {
                return array_map(function ($key) use ($record) {
                    return $record[$key] ?? '';
                }, $finalKeys);
            }, $RecordList);

        } else {
            $rows = array_map(function ($record) use ($ColumnList) {
                return array_map(function ($key) use ($record) {
                    return $record[$key] ?? "";
                }, array_keys($ColumnList));
            }, $RecordList);
        }

        // echo "<pre>";print_r($rows[154]);die;
        return $this->asJson([
            'success' => true,
            'headers' => $headers,
            'rows' => $rows,
        ]);
    }

    public function actionReport()
    {
        $arrRender = array();

        $TabId = $this->TabId;
        $ModuleName = $this->ModuleName;
        $TabLabel = $this->TabLabel;

        $model = new AccessCheck();
        $id = Yii::$app->user->id;
        $uid = Yii::$app->params['dirName'];

        $model = new AccessCheck();
        $id = Yii::$app->user->id;
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $modulepermission = $model->modulepermission($profile, $tabs);
        // echo $ModuleName;die;
        $listpermission = $model->checkpermission($id, $ModuleName, 'list');
        $detailpermission = $model->checkpermission($id, $ModuleName, 'detail');
        $exportpermission = $model->checkpermission($id, $ModuleName, 'export');

        
        $arrRender['ModuleName'] = $ModuleName;
        $arrRender['TabId'] = $TabId;
        $arrRender['TabLabel'] = $TabLabel;
        $users = [];
        if ($TabId == 95) {
            $users = User::find()
                ->select([
                    'id',
                    new Expression("CONCAT(first_name, ' ', last_name) AS username")
                ])
                ->where(['deleted' => 0])
                ->asArray()
                ->all();

            // Make it key-value (id => username)
            $users = ArrayHelper::map($users, 'id', 'username');
            // echo "<pre>";print_R($users);die;
        }
        $this->layout = '@app/views/layouts/main-one';
        return $this->render('@app/views/tetra/reportview', [
            'ModuleName' => $ModuleName,
            'TabLabel' => $TabLabel,
            'TabId' => $TabId,
            'users' => $users,
            'listpermission' => $listpermission,
            'hasadminpower' => $hasadminpower,
            'exportpermission'=>$exportpermission,
        ]);
    }

    //update notification by deepika 31 may 2025
    public function updatenotification($id, $Recordid)
    {
        $sql = "UPDATE notification 
        SET read_status = 1 
        WHERE SUBSTRING_INDEX(SUBSTRING_INDEX(source_link, 'Record=', -1), '&', 1) = :record 
        AND userid = :id";

        Yii::$app->db->createCommand($sql)
            ->bindValue(":record", $Recordid)
            ->bindValue(":id", $id)
            ->execute();
    }

    ///added by deepika on 9 june 2025 for cust code in account module
    public function getaccountcode($code)
    {
        $currentfinanceyear = $this->getCurrentFinanceYear();
        $table_name = $this->ModuleName;
        $model = new AutoNo();
        $orderno = $model->getautomodulecode(1, $table_name);
        $code = $code . $currentfinanceyear . $orderno;
        return $code;
    }
    function getCurrentFinanceYear()
    {
        // $currentMonth = date('m'); // Get current month (01 to 12)

        // If the current month is before April (January to March), the fiscal year is for the previous year
        // if ($currentMonth < 4) {
        //     $startYear = date('y') - 1; // Fiscal year starts from the previous year
        //     $endYear = date('y'); // Fiscal year ends in the current year
        // } else {
        //     $startYear = date('y'); // Fiscal year starts in the current year
        //     $endYear = date('y') + 1; // Fiscal year ends in the next year
        // }

        //////get financial year/////
        $startyear = '';
        $endyear = '';
        $sql = "select * from fyear where is_active = 1 limit 1";
        $cmd = Yii::$app->db->createCommand($sql)->queryOne();
        if ($cmd) {
            $startyear = $cmd['start_year'];
            $endyear = $cmd['end_year'];

            $string = "Hello, World!";
            $n = 2; // Number of characters to extract from the end
            $startyear = substr($startyear, -$n);
            $endyear = substr($endyear, -$n);
        }
        // echo $startyear.$endyear;die;

        return $startyear . $endyear; // Concatenate the last two digits of the start and end years
    }
    // code for improved import  functionality
    public function actionImprovedimport()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $ModuleName = $this->ModuleName;
        $TabId = $this->TabId;
        $TabLabel = $this->TabLabel;

        $fileHeaders = $fileHeaders ?? [];  // safe default to avoid undefined
        $fileFirstRow = $fileFirstRow ?? [];
        $availableFields = $availableFields ?? [];
        $allFields = $allFields ?? [];

        $allFields = Field::find()
            ->where(['import' => 1, 'tabid' => $TabId])
            ->select(['fieldid', 'fieldlabel', 'fieldname', 'mandatory'])
            ->asArray()
            ->all();
        //this code is for sample generate
        $connection = Yii::$app->db;
        //70 is createdtime, modified time ,//53 is created by modified by
        $command = $connection->createCommand("
        SELECT fieldlabel,columnname, tablename, mandatory,uitype,fieldid
        FROM `field` 
        WHERE import = :import AND tabid = :tabid AND uitype != 70 AND uitype != 53 
    ");
        $command->bindValue(':import', 1);
        $command->bindValue(':tabid', $TabId);

        // Execute query to fetch columns
        $columnsIMP = $command->queryAll();
        //code for sample generate complete
        if ($TabId == 18) {
            $all_rolesfield = (new Query())
                ->select(['roleid', 'showinaccounts', 'rolename'])
                ->from('role') // or '{{%role}}' if table uses prefix
                ->where(['showinaccounts' => 1])
                ->all();
            $allFields = array_merge($allFields, $all_rolesfield);
        }
        return [
            'html' => $this->renderPartial('@app/views/tetra/importview', [
                'ModuleName' => $ModuleName,
                'TabId' => $TabId,
                'TabLabel' => $TabLabel,
                'allFields' => $allFields,
                'fileHeaders' => $fileHeaders,
                'fileFirstRow' => $fileFirstRow,
                'availableFields' => $availableFields,
                'DataImport'=>$columnsIMP,
            ]),
        ];
    }


    public function actionUploadcsv()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $uploadedFile = \yii\web\UploadedFile::getInstanceByName('csv_file');
        if (!$uploadedFile || strtolower($uploadedFile->extension) !== 'csv') {
            return ['error' => 'Please upload a valid CSV file.'];
        }

        $filePath = Yii::getAlias('@runtime/') . uniqid('csv_') . '.' . $uploadedFile->extension;
        if (!$uploadedFile->saveAs($filePath)) {
            return ['error' => 'Failed to save uploaded file.'];
        }

        //  Convert to UTF-8 if not already
        $content = file_get_contents($filePath);
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252', 'ASCII'], true);
        if ($encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
            file_put_contents($filePath, $content);
        }

        // Store in session for later use in mapping
        Yii::$app->session->set('uploaded_csv_path', $filePath);

        $headers = [];
        $firstRow = [];

        if (($handle = fopen($filePath, 'r')) !== false) {
            // Handle BOM (Byte Order Mark)
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($handle);
            }

            // Detect delimiter
            $delimiter = ",";
            $delimiters = [",", ";", "|", "\t"];
            $firstLine = fgets($handle);
            rewind($handle);

            $maxFields = 0;
            foreach ($delimiters as $d) {
                $fields = str_getcsv($firstLine, $d);
                if (count($fields) > $maxFields) {
                    $delimiter = $d;
                    $maxFields = count($fields);
                }
            }

            $headers = fgetcsv($handle, 0, $delimiter);
            $firstRow = fgetcsv($handle, 0, $delimiter);
            fclose($handle);
        }

        if (empty($headers)) {
            return ['error' => 'CSV appears empty or corrupted.'];
        }

        return [
            'headers' => array_map('trim', $headers),
            'firstRow' => array_map('trim', $firstRow),
        ];
    }


    public function actionSavemapping()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $connection = Yii::$app->db;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $FieldId = $this->FieldId;
        $TabId = $this->TabId;

        //overwrite based on this field
        $overwriteField = $csvHeader = '';

        // debug - remove or comment in production
        // echo "<pre>";print_r($_POST);die;

        $headers = Yii::$app->request->post('headerNames', []);
        $mappedFields = Yii::$app->request->post('mappedFields', []);
        $defaultValues = Yii::$app->request->post($TableName, []);
        $selectedfieldsid = Yii::$app->request->post('selectedfieldsid', '');
        // echo "<pre>";print_r($selectedfieldsid);die;
        $selectedFieldsArray = array_map('trim', explode(',', $selectedfieldsid));

        // echo "<pre>";print_r($mappedFields);
        // echo "-------------------------";
        // die;
        $importMode = Yii::$app->request->post('duplicateAction'); // skip|overwrite|merge

        if (count($headers) !== count($mappedFields)) {
            return ['success' => false, 'error' => 'Mapping count mismatch.'];
        }

        $validMapping = array_filter($mappedFields, fn($fieldId) => !empty($fieldId));
        if (empty($validMapping)) {
            return ['success' => false, 'error' => 'No fields are mapped.'];
        }

        // Build header -> field mapping
        $mapping = [];
        foreach ($headers as $i => $header) {
            // echo $mappedFields[$i];
            if (!empty($mappedFields[$i])) {
                //for OEM Mager section in account module
                // if ($TabId == 18 && strpos($mappedFields[$i], 'H') !== false) {
                if ($TabId == 18 && strpos($mappedFields[$i], 'H') !== false && preg_match('/^H\d+$/', $mappedFields[$i])){
                    // echo "1";
                    $mapping[$header] = [
                        'role_id' => $mappedFields[$i],
                        // 'default_value' => $defaultValues[$header] ?? ''
                        'default_value' => $defaultValues[$mappedFields[$i]] ?? ''
                    ];
                } else {
                    // echo "2";echo $mappedFields[$i];
                    $mapping[$header] = [
                        'field_id' => $mappedFields[$i],
                        // 'default_value' => $defaultValues[$header] ?? ''
                        'default_value' => $defaultValues[$mappedFields[$i]] ?? ''
                    ];
                }
                // echo "<pre>";print_r($mapping);die;
            }
        }
        // echo "<pre>";print_r($mapping);die;
        $fieldnameToCsvHeader = [];
        foreach ($mapping as $csvHeader => $map) {
            if (isset($map['field_id']))
                $fieldnameToCsvHeader[$map['field_id']] = $csvHeader;
            else if (isset($map['role_id']) && $TabId == 18)
                $fieldnameToCsvHeader[$map['role_id']] = $csvHeader;
        }
        // echo "<pre>";print_r($fieldnameToCsvHeader);die;

        // Get CSV path
        $csvPath = Yii::$app->session->get('uploaded_csv_path');
        if (!$csvPath || !file_exists($csvPath)) {
            return ['success' => false, 'error' => 'CSV file not found or expired.'];
        }
        $csvRows = [];
        $maxRows = 1000; // Set your desired limit

        if (($handle = fopen($csvPath, 'r')) !== false) {
            $csvHeaders = fgetcsv($handle);
            $rowCount = 0;

            while (($row = fgetcsv($handle)) !== false) {
                if ($rowCount >= $maxRows) {
                    return ['success' => false, 'error' => 'You can upload max ' . $maxRows . ' Records At a time.'];
                }

                $csvRows[] = array_combine($csvHeaders, $row);
                $rowCount++;
            }

            fclose($handle);
        } else {
            return ['success' => false, 'error' => 'Failed to open CSV file.'];
        }

        if (empty($csvRows)) {
            return ['success' => false, 'error' => 'CSV has no data rows.'];
        }
        // die;
        // Read all CSV rows
        // echo "<pre>";print_r($selectedFieldsArray);die;
        /**
         * $selectedFieldsArray  = selected field in second step based on this we can 
         * ex call -subject wise overwrite
         * Array([0] => subject)
         */
        if (!empty($importMode) && !empty(array_filter($selectedFieldsArray))) {
            $deduplicationMap = [];
            $fileDuplicateCount = 0;
            $dbDuplicateCount = 0;

            // Step 1: Prepare values to check in DB
            $dbCheckRows = [];

            foreach ($csvRows as $rowIndex => $csvRow) {
                $keyParts = [];
                foreach ($selectedFieldsArray as $fieldName) {
                    if (!isset($fieldnameToCsvHeader[$fieldName])) {
                        return ['success' => false, 'error' => "Deduplication failed: '$fieldName' is not mapped to any CSV column."];
                    }
                    $csvHeader = $fieldnameToCsvHeader[$fieldName];
                    $keyParts[] = strtolower(trim($csvRow[$csvHeader] ?? ''));
                }
                $dbCheckRows[] = $keyParts;
            }

            // Step 2: Create query condition for DB
            $dbCondition = ['or'];
            foreach ($dbCheckRows as $keyParts) {
                // $this->sanitizeUtf8String($this->fixEncoding($value))
                // echo "<pre>";print_r($keyParts);die;
                $and = [];
                foreach ($selectedFieldsArray as $i => $fieldName) {
                    // echo $fieldName;die;
                    $refine_field = "REPLACE(LOWER($fieldName), ' ', '')";
                    $overwriteField  = $fieldName;
                    $and[$refine_field] = $this->normalizeAndCleanString($keyParts[$i]);
                    // echo $and[$fieldName];die;
                }
                $dbCondition[] = $and;
            }
            // echo "<pre>count-->".count($dbCondition)."\n";
            // echo "<pre>";print_r($dbCondition);die;
            // Step 3: Fetch existing rows from DB
            $dbDuplicates = [];
            if (count($dbCondition) > 1) { // make sure there's something to check
                $existingRows = (new \yii\db\Query())
                    ->select($selectedFieldsArray)
                    ->from($TableName)
                    ->where($dbCondition)
                    ->all();
                // $sql = $existingRows->createCommand()->getRawSql();
                // echo $sql;
                // echo "<pre>cnt existingRows-->".(count($existingRows))."\n";
                // die;
                foreach ($existingRows as $row) {
                    $key = implode('|', array_map(fn($f) => $this->normalizeAndCleanString($row[$f] ?? ''), $selectedFieldsArray));
                    $dbDuplicates[$key] = true;
                }
                // echo "<pre>";print_r($dbDuplicates);die;
                /**$dbDuplicates = Array([test07102025] => 1) */
                // $dbDuplicateCount = count($dbDuplicates);
            }
            // echo "dbDuplicateCount".$dbDuplicateCount;
            // die;
            // echo "<pre>";print_r($csvRows);die;
            // Step 4: Deduplication logic
            foreach ($csvRows as $rowIndex => $csvRow) {
                $keyParts = [];
                foreach ($selectedFieldsArray as $fieldName) {
                    $csvHeader = $fieldnameToCsvHeader[$fieldName] ?? null;
                    // $keyParts[] = strtolower(trim(mb_convert_encoding($csvRow[$csvHeader] ?? '', 'UTF-8', 'auto') ?? ''));
                    $keyParts[] = $this->normalizeAndCleanString($csvRow[$csvHeader] ?? '');
                }
                $uniqueKey = $this->normalizeAndCleanString(implode('|', $keyParts));
                //$uniqueKey = test07102025
                // echo "uniqueKey".$uniqueKey;die;
                $isFileDuplicate = isset($deduplicationMap[$uniqueKey]);
                $isDbDuplicate = isset($dbDuplicates[$uniqueKey]);
                
                if ($isFileDuplicate || $isDbDuplicate) {
                    if ($isFileDuplicate)
                        $fileDuplicateCount++;
                    if ($isDbDuplicate) 
                        $dbDuplicateCount++;
                    // echo $importMode . $dbDuplicateCount;die;
                    switch ($importMode) {
                        case 'skip':
                            continue 2;

                        case 'Overwrite':
                            if ($isDbDuplicate) {
                                // Find existing record ID to update
                                // echo $overwriteField;die;
                                $csv_vals = "REPLACE(LOWER(".$csvRow[$csvHeader]."), ' ', '')";
                                $existingRecord = (new \yii\db\Query())
                                                ->select($FieldId)
                                                ->from($TableName)
                                                // ->where($dbCondition)
                                                ->where([$overwriteField => $csvRow[$csvHeader]])
                                                // ->andWhere(['deleted'=>0])
                                                // ->orderBy([$FieldId => SORT_DESC]);
                                                ->one();
                                                
                                // echo $existingRecord->createCommand()->getRawSql();die;
                                // echo "<pre>a";print_r($existingRecord);die;
                                if ($existingRecord) {
                                        $csvRow[$FieldId] = $existingRecord[$FieldId];
                                        $uniqueKey = $csvRow[$csvHeader];
                                        // Prepare update data from CSV row
                                        $updateableRows[$uniqueKey] = $csvRow;
                                }
                            } else {
                                // Not in DB, just add for insert
                                $deduplicationMap[$uniqueKey] = $csvRow;
                            }
                            break;
                        default:
                            continue 2;
                    }
                } else {
                    $deduplicationMap[$uniqueKey] = $csvRow;
                }
            }
            $insertableRows = array_values($deduplicationMap);
        } else {
            // No deduplication, insert all rows as-is
            $insertableRows = $csvRows;
        }
        // echo "<pre>ins";print_r($insertableRows);echo "------updt";
        // echo "<pre>updateableRows";print_r($updateableRows);
        // die;
        $inserted = $updated = 0;
        $failed = 0;
        $errors = [];

        try {

                // echo "in else";die;
                // Auto increment column
                $autoIncrementColumn = '';
                $autoResult = $connection->createCommand("
                    SELECT columnname 
                    FROM `field` 
                    WHERE uitype = 11 AND tabid = :tabid
                    ")->bindValue(':tabid', $TabId)->queryOne();

                if ($autoResult) {
                    $autoIncrementColumn = $autoResult['columnname'];
                }

                $allValues = [];
                $roleallValues = [];
                
                $paramBindings = [];
                $rowCount = 0;
                $roleparamGroups = [];
                $transaction = $connection->beginTransaction();
                // echo "<pre>";print_r($insertableRows);die;
                foreach ($insertableRows as $rowIndex => $csvRow) {
                    $insertData = [];
                    $requiredData = [
                        'ownerid' => Yii::$app->user->id ?? null,
                        'createdtime' => date('Y-m-d H:i:s'),
                        'creatorid' => Yii::$app->user->id ?? null,
                        'modifiedby' => Yii::$app->user->id ?? null,
                        'modifiedtime' => date('Y-m-d H:i:s'),
                    ];

                    if ($autoIncrementColumn) {
                        $requiredData[$autoIncrementColumn] = $this->getAutoNo($TabId);
                    }
                    // echo "<pre>";print_r($mapping);die;
                    // to store previous column in call,meeting,task,document module
                    $prevColValue ='';
                    foreach ($mapping as $csvHeader => $map) {
                        if (isset($map['field_id']))
                            $fieldName = is_numeric($map['field_id']) ? '' : trim($map['field_id']);
                        else if (isset($map['role_id']) && $TabId == 18)
                            $fieldName = is_numeric($map['role_id']) ? '' : trim($map['role_id']);

                        if (!$fieldName)
                            continue;

                        // echo $csvRow[$csvHeader];die;
                        // Clean value, fallback to default
                        $value = preg_replace('/\s+/', ' ', trim($csvRow[$csvHeader] ?? ''));
                        // echo $value;die;
                        $defaultvalue = 0;
                        if ($value === '' || $value === null) {

                            $value = preg_replace('/\s+/', ' ', trim($map['default_value'] ?? ''));
                            $defaultvalue = 1;
                        }
                        // echo $value."\n";
                        $value = $this->sanitizeUtf8String($this->fixEncoding($value));
                        //   echo "------".$value."\n";
                        // Get field details

                        if (isset($map['field_id'])) {
                            // echo $map['field_id']."\n";
                            $fields = $connection->createCommand("
                                SELECT typeofdata, isunique, fieldlabel ,uitype,fieldid,mandatory,fieldname
                                FROM field 
                                WHERE fieldname = :fieldname AND tabid = :tabid
                            ")->bindValue(':fieldname', $map['field_id'])
                                ->bindValue(':tabid', $TabId)
                                ->queryOne();
                            /**code add for check account duplication in vendor account on date 08-09-2025 */
                            if ($TabId == 18 && ($fields['fieldid'] == 43)) { //43 account name
                                //check email or mobile is exists in db
                                $exists = (new \yii\db\Query())
                                    ->from($this->TableName)   // <-- your table
                                    ->where([$fields['fieldname'] => $value])
                                    ->exists();
                                if ($exists) {
                                    return [
                                        'success' => false,
                                        'error' => $fields['fieldlabel'] . " $value is already exists at CSV Row " . ($rowIndex + 1),
                                    ];
                                }
                            }
                            //end code to check duplicate account in vendor account
                            //code start for check vendor location duplication for same account  
                            if($TabId == 29 && $fieldName == "vendor_account")
                            {
                                $check_acc_name= '';
                                // echo $value;die;
                                    $acc_data = VendorAccount::find()->where(['acc_name'=>$value,'deleted'=>0])->one();
                                    if($acc_data)
                                        $check_acc_name = $acc_data->vendoraccid;
                                    // echo "<pre>";print_r($check_acc_name);die;
                            }
                            if ($TabId == 29 && ($fields['fieldid'] == 290)) { //290 location name
                                // echo $check_acc_name."\n";
                                $exists = (new \yii\db\Query())
                                    ->from($this->TableName)   // <-- your table
                                    ->where([$fields['fieldname'] => $value])
                                    ->andWhere(['vendor_account'=>$check_acc_name])
                                    ->exists();
                                    // echo "df".$exists;die;
                                if ($exists) {
                                    return [
                                        'success' => false,
                                        'error' => $fields['fieldlabel'] . " $value is already exists at CSV Row " . ($rowIndex + 1),
                                    ];
                                }
                                // die;
                            }
                            /**code end for location duplication check in vendor location module as per ERP finding point -417 */
                            /** code  add for contact and email duplication chaeck in contact module*/
                            if ($TabId == 19 && ($fields['fieldid'] == 103 || $fields['fieldid'] == 104)) {
                                //check email or mobile is exists in db
                                $exists = (new \yii\db\Query())
                                    ->from($this->TableName)   // <-- your table
                                    ->where([$fields['fieldname'] => $value])
                                    ->exists();
                                if ($exists) {
                                    return [
                                        'success' => false,
                                        'error' => $fields['fieldlabel'] . " $value is already exists at CSV Row " . ($rowIndex + 1),
                                    ];
                                }
                            }
                            /** code end add for contact and email duplication chaeck in contact module*/
                            /** code  add for check duplicate product name in product module*/
                            if ($TabId == 9 && ($fields['fieldid'] == 502)) { //502 product name
                                //check email or mobile is exists in db
                                $exists = (new \yii\db\Query())
                                    ->from($this->TableName)   // <-- your table
                                    ->where([$fields['fieldname'] => $value])
                                    ->exists();
                                if ($exists) {
                                    return [
                                        'success' => false,
                                        'error' => $fields['fieldlabel'] . " $value is already exists at CSV Row " . ($rowIndex + 1),
                                    ];
                                }
                            }
                            /** code end add for contact and email duplication chaeck in contact module*/
                            /** code  add for check duplicate product name in product dit module on date 08-01-2026*/
                            if ($TabId == 73 && ($fields['fieldid'] == 3139)) { //3139 product name in dit
                                //check email or mobile is exists in db
                                $exists = (new \yii\db\Query())
                                    ->from($this->TableName)   // <-- your table
                                    ->where([$fields['fieldname'] => $value])
                                    ->exists();
                                if ($exists) {
                                    return [
                                        'success' => false,
                                        'error' => $fields['fieldlabel'] . " $value is already exists at CSV Row " . ($rowIndex + 1),
                                    ];
                                }
                            }
                            /** code  add for check duplicate warehouse in warehouse module on date 08-01-2026*/
                            if ($TabId == 30 && ($fields['fieldid'] == 296)) { //3139 warehouse name in 
                                //check email or mobile is exists in db
                                $exists = (new \yii\db\Query())
                                    ->from($this->TableName)   // <-- your table
                                    ->where([$fields['fieldname'] => $value])
                                    ->exists();
                                if ($exists) {
                                    return [
                                        'success' => false,
                                        'error' => $fields['fieldlabel'] . " $value is already exists at CSV Row " . ($rowIndex + 1),
                                    ];
                                }
                            }
                            /** code end add for contact and email duplication chaeck in contact module*/
                            // if ($fields['mandatory'] == 1 && empty($value)) {
                            if ($fields['mandatory'] == 1 && ($value === '' || $value === null)) {
                                return [
                                    'success' => false,
                                    'error' => "No match found for '$value' in field " . $fields['fieldlabel'] . " at CSV Row " . ($rowIndex + 1),
                                ];
                            }
                            // echo $value."\n";
                            // Dependent value
                            if (in_array($fields['uitype'], [8, 12, 22, 27, 10, 9, 25, 26])) {//9-checkbox and 10-radio
                                try {
                                    $oldvalue = $value;
                                    // if($fields['uitype'] == 12){
                                    //     echo "<pre>";print_r($fields);die;
                                    // }
                                    if ($defaultvalue == 0 && !empty($value)) {
                                        //this code is added to get related_module to get records from entitytable need modulename
                                        //call,meeting,taask,document
                                        if ($fields['uitype'] == 25 && in_array($TabId,['20','21','22','23'])) {
                                            // $prevColValue = '';
                                            $prevColValue = strtolower($value);
                                            // echo "in 25".$prevColValue;
                                        }

                                        $value = $this->getdependantValue($TabId, $fields['fieldid'], $fields['fieldlabel'], $fields['uitype'], $value,$fields['uitype'] == 26 ? $prevColValue : null);
                                        // if($fields['fieldid'] == 1296)
                                        // echo $value;die;
                                        if (empty(trim($value))) {
                                            return [
                                                'success' => false,
                                                'error' => "No match found for '$oldvalue' in field " . $fields['fieldlabel'] . " at CSV Row " . ($rowIndex + 1),
                                            ];
                                        }
                                    }
                                    else if(in_array($fields['uitype'], [8, 12, 22, 25, 26,27]) && 
                                            (   ($fields['uitype'] == 8 && $fields['typeofdata'] == 'DD~M') ||
                                                ($fields['uitype'] == 12 && ($fields['typeofdata'] == 'DD~M' || $fields['typeofdata'] == 'V~M')) ||
                                                ($fields['uitype'] == 22 && ($fields['typeofdata'] == 'DD~M' || $fields['typeofdata'] == 'V~M')) || 
                                                ($fields['uitype'] == 25 && ($fields['typeofdata'] == 'V~M')) ||
                                                ($fields['uitype'] == 26 && ($fields['typeofdata'] == 'V~M')) ||
                                                ($fields['uitype'] == 27 && ($fields['typeofdata'] == 'V~M'))
                                            )
                                        )
                                    {
                                        return [
                                                'success' => false,
                                                'error' => "No match found for '$oldvalue' in field " . $fields['fieldlabel'] . " at CSV Row " . ($rowIndex + 1),
                                            ];
                                    }
                                } catch (\Exception $e) {
                                    // $transaction->rollBack();
                                    return [
                                        'success' => false,
                                        'error' => "No match found for '$value' in field " . $fields['fieldlabel'] . " at CSV Row " . ($rowIndex + 1),
                                    ];
                                }
                            }
                            if($fields['uitype'] == 6) // checkbox
                            {
                                $value = strtolower($value) == 'yes' ? 1 : 0 ;
                            }
                            // && ($fields['typeofdata'] == 'DT~O' || $fields['typeofdata'] == 'DT~M')
                            // Validate
                            try {
                                // echo $value."\n";
                                $this->validateFieldByType($rowIndex + 2, $value, $fields['typeofdata'] ?? '', $fields['fieldlabel']);
                            } catch (\Exception $e) {
                                // $transaction->rollBack();
                                return [
                                    'success' => false,
                                    'error' => $e->getMessage()
                                ];
                            }
                            //this code is here because validateFieldByType want dd-mm-yy and DB want yyyy-mm-dd
                            if (in_array($fields['uitype'], [13, 17])) {
                                
                                if($fields['uitype'] == 13)
                                {
                                        // Convert DD-MM-YYYY HH:MM → YYYY-MM-DD HH:MM:SS
                                    $datetime = DateTime::createFromFormat('d-m-Y H:i', $value);

                                    if ($datetime) {
                                        $value = $datetime->format('Y-m-d H:i:s');
                                    } else {
                                        return [
                                        'success' => false,
                                        'error' => "Invalid '$value' in field " . $fields['fieldlabel'] . " at CSV Row " . ($rowIndex + 1),
                                    ];
                                    }
                                }
                                else{
                                    $value = $this->convertDateDMY($value);
                                }
                                // echo $value;die;
                            }
                        }

                        if ($TabId == 18 && $fieldName == 'account_category') {
                            // echo $value;die;
                            $codes = [
                                '1' => 'V',
                                '2' => 'C',
                                // '3' => 'P', //partner has been removed
                            ];
                            // echo "<pre>";print_r($value);die;
                            $val = (strpos($value, ',') !== false) ? explode(',', $value) : [$value];

                            $code = '';
                            foreach ($val as $v) {
                                $v = trim($v); // remove spaces
                                if (isset($codes[$v])) {
                                    $code .= $codes[$v];
                                }
                            }
                            $insertData['cust_code'] = $this->getaccountcode($code);
                            //create auto genrate no and add history
                            // $model = new AutoNo();
                            // $model->setAutomoduleno(1, $ModuleName);
                            //to resolve duplicate account no issue on date 20-09-2025
                            $this->setAutoNo(1);
                        }
                        $insertData[$fieldName] = $value;

                        // echo "<pre>";print_r($insertData);die;
                    }
                    // echo "<pre>";print_r($insertData);die;
                    // Required system fields

                    $finalData = array_merge($requiredData, $insertData);
                    //need to add one auto incrment number in crmentity to resolve Acc auto no issue on date 202-09-2025

                    $columnNames = array_keys($finalData);
                    // echo "<pre>";print_r($finalData);
                    $main_columns = [];
                    $placeholders = [];
                    $roleplaceholders = [];

                    foreach ($columnNames as $col) {

                        if ($TabId == 18 && preg_match('/^H\d+$/', $col)) {
                            $roleparamBindings[":userid_{$rowCount}"] = '';

                            if (!empty($finalData[$col])) {
                                $rolewiseuid = User::findOne(['email' => $finalData[$col]]);
                                $roleparamName = ":{$col}_{$rowCount}";

                                if ($rolewiseuid) {
                                    $rolematch = (new Query())
                                        ->select('*')
                                        ->from('user2role')
                                        ->where(['userid' => $rolewiseuid->id])
                                        ->one();

                                    if ($rolematch['roleid'] === $col) {
                                        $roleparamBindings[":userid_{$rowCount}"] = $rolewiseuid->id;
                                        $roleparamBindings[$roleparamName] = $col;

                                        // ✅ Append role mapping in a nested array per row
                                        $roleparamGroups[$rowCount][] = [
                                            ":userid_{$rowCount}" => $rolewiseuid->id,
                                            $roleparamName => $col,
                                        ];
                                    } else {
                                        $rolename = (new Query())
                                            ->select('rolename')
                                            ->from('role')
                                            ->where(['roleid' => $col])
                                            ->one();

                                        return [
                                            'success' => false,
                                            'error' => "Invalid Mapping.\n Please enter a valid Email Id." .
                                                $fieldnameToCsvHeader[$col] . " - '" . $finalData[$col] .
                                                "' does not have " . $rolename['rolename'] . ' Role at row ' . ($rowCount + 1),
                                        ];
                                    }
                                }
                            }
                        } else {
                            if ($TabId == 18) {
                                $main_columns[] = $col;
                            }

                            $paramName = ":{$col}_{$rowCount}";
                            $placeholders[] = $paramName;
                            $paramBindings[$paramName] = $finalData[$col];
                        }
                    }


                    // Debug output
                    // echo "<pre>1"; print_r($roleparamGroups); 

                    // echo "<pre>";print_r($roleparamBindings);die;
                    if (!empty($roleplaceholders))
                        $roleallValues[] = '(' . implode(',', $roleplaceholders) . ')';
                    if (!empty($placeholders))
                        $allValues[] = '(' . implode(',', $placeholders) . ')';
                    $rowCount++;
                    $inserted++;
                }
                
                    // echo "<pre>";print_r($updateableRows);die;
                //update code
                $updateallValues = [];
                $updaterowCount = 0;
                if(isset($updateableRows) && !empty($updateableRows)){
                    $updateableRows = array_values($updateableRows);
                }
                // echo "<pre>";print_r($updateableRows);print_r($overwriteField);die;
                if(isset($updateableRows) && !empty($updateableRows)){
                    foreach ($updateableRows as $rowIndex => $csvRow) {
                        // echo "<pre>rowIndex";print_r($rowIndex);
                        // print_r($csvRow);die;
                        $updateData = [];
                        $existingRecord = (new \yii\db\Query())
                                                    ->select($FieldId)
                                                    ->from($TableName)
                                                    ->where([$FieldId => $csvRow[$FieldId]])
                                                    ->one();
                        // echo "<pre>";print_r($existingRecord);die;
                        if ($existingRecord) {
                                // Use DB values for system fields instead of new ones
                                $requiredData = [
                                    "$FieldId" => $existingRecord[$FieldId],
                                    'ownerid' => $existingRecord['ownerid'] ?? Yii::$app->user->id,
                                    'createdtime' => $existingRecord['createdtime'] ?? date('Y-m-d H:i:s'),
                                    'creatorid' => $existingRecord['creatorid'] ?? Yii::$app->user->id,
                                    'modifiedby' => Yii::$app->user->id ?? $existingRecord['modifiedby'],
                                    'modifiedtime' => date('Y-m-d H:i:s'),
                                ];
                            }
                            
                        // echo "in update1";
                        // echo "<pre>";print_r($mapping);die;
                        // to store previous column in call,meeting,task,document module
                        $prevColValue ='';
                        foreach ($mapping as $csvHeader => $map) {
                            if (isset($map['field_id']))
                                $fieldName = is_numeric($map['field_id']) ? '' : trim($map['field_id']);
                            else if (isset($map['role_id']) && $TabId == 18)
                                $fieldName = is_numeric($map['role_id']) ? '' : trim($map['role_id']);

                            if (!$fieldName)
                                continue;

                            // echo $csvRow[$csvHeader];die;
                            // Clean value, fallback to default
                            $value = preg_replace('/\s+/', ' ', trim($csvRow[$csvHeader] ?? ''));
                            // echo $value;die;
                            $defaultvalue = 0;
                            if ($value === '' || $value === null) {

                                $value = preg_replace('/\s+/', ' ', trim($map['default_value'] ?? ''));
                                $defaultvalue = 1;
                            }
                            // echo $value."\n";
                            $value = $this->sanitizeUtf8String($this->fixEncoding($value));
                            //   echo "------".$value."\n";
                            // Get field details

                            if (isset($map['field_id'])) {
                                // echo $map['field_id']."\n";
                                $fields = $connection->createCommand("
                                    SELECT typeofdata, isunique, fieldlabel ,uitype,fieldid,mandatory,fieldname
                                    FROM field 
                                    WHERE fieldname = :fieldname AND tabid = :tabid
                                ")->bindValue(':fieldname', $map['field_id'])
                                    ->bindValue(':tabid', $TabId)
                                    ->queryOne();
                                //code start for check vendor location duplication for same account  
                                if($TabId == 29 && $fieldName == "vendor_account")
                                {
                                    $check_acc_name= '';
                                    // echo $value;die;
                                        $acc_data = VendorAccount::find()->where(['acc_name'=>$value,'deleted'=>0])->one();
                                        if($acc_data)
                                            $check_acc_name = $acc_data->vendoraccid;
                                        // echo "<pre>";print_r($check_acc_name);die;
                                }
                                /**code end for location duplication check in vendor location module as per ERP finding point -417 */
                                /***THIS CODE IS NOT CORRECT IN UPDATE BECAUSE WE CANT have that record ID */
                                
                                /** code  add for contact and email duplication chaeck in contact module*/
                                /*if ($TabId == 19 && ($fields['fieldid'] == 103 || $fields['fieldid'] == 104)) {
                                    //check email or mobile is exists in db
                                    $exists = (new \yii\db\Query())
                                        ->from($this->TableName)   // <-- your table
                                        ->where([$fields['fieldname'] => $value])
                                        ->exists();
                                    if ($exists) {
                                        return [
                                            'success' => false,
                                            'error' => $fields['fieldlabel'] . " $value is already exists at CSV Row " . ($rowIndex + 1),
                                        ];
                                    }
                                }*/
                                /** code end add for contact and email duplication chaeck in contact module*/
                                /** code  add for check duplicate product name in product module*/
                                /*if ($TabId == 9 && ($fields['fieldid'] == 502)) { //502 product name
                                    //check email or mobile is exists in db
                                    $exists = (new \yii\db\Query())
                                        ->from($this->TableName)   // <-- your table
                                        ->where([$fields['fieldname'] => $value])
                                        ->exists();
                                    if ($exists) {
                                        return [
                                            'success' => false,
                                            'error' => $fields['fieldlabel'] . " $value is already exists at CSV Row " . ($rowIndex + 1),
                                        ];
                                    }
                                }*/
                                 /** code  add for check duplicate product name in product dit module on date 08-01-2026*/
                                /*if ($TabId == 73 && ($fields['fieldid'] == 3139)) { //3139 product name in dit
                                    //check email or mobile is exists in db
                                    $exists = (new \yii\db\Query())
                                        ->from($this->TableName)   // <-- your table
                                        ->where([$fields['fieldname'] => $value])
                                        ->exists();
                                    if ($exists) {
                                        return [
                                            'success' => false,
                                            'error' => $fields['fieldlabel'] . " $value is already exists at CSV Row " . ($rowIndex + 1),
                                        ];
                                    }
                                }*/
                                /** code  add for check duplicate warehouse in warehouse module on date 08-01-2026*/
                                /*if ($TabId == 30 && ($fields['fieldid'] == 296)) { //3139 warehouse name in 
                                    //check email or mobile is exists in db
                                    $exists = (new \yii\db\Query())
                                        ->from($this->TableName)   // <-- your table
                                        ->where([$fields['fieldname'] => $value])
                                        ->exists();
                                    if ($exists) {
                                        return [
                                            'success' => false,
                                            'error' => $fields['fieldlabel'] . " $value is already exists at CSV Row " . ($rowIndex + 1),
                                        ];
                                    }
                                }*/
                                /** code end add for contact and email duplication chaeck in contact module*/
                            /***THIS CODE IS NOT CORRECT IN UPDATE BECAUSE WE CANT have that record ID */
                                // if ($fields['mandatory'] == 1 && empty($value)) {
                                if ($fields['mandatory'] == 1 && ($value === '' || $value === null)) {
                                    return [
                                        'success' => false,
                                        'error' => "No match found for '$value' in field " . $fields['fieldlabel'] . " at CSV Row " . ($rowIndex + 1),
                                    ];
                                }
                                // echo $value."\n";
                                // Dependent value
                                if (in_array($fields['uitype'], [8, 12, 22, 27, 10, 9, 25, 26])) {//9-checkbox and 10-radio
                                    try {
                                        $oldvalue = $value;
                                        // if($fields['uitype'] == 8){
                                        //     echo "<pre>";print_r($fields);
                                        // }
                                        // echo "1".$value." ".$defaultvalue;
                                        if ($defaultvalue == 0 && !empty($value)) {
                                            // echo "2";
                                            //this code is added to get related_module to get records from entitytable need modulename
                                            //call,meeting,taask,document
                                            if ($fields['uitype'] == 25 && in_array($TabId,['20','21','22','23'])) {
                                                // $prevColValue = '';
                                                $prevColValue = strtolower($value);
                                                // echo "in 25".$prevColValue;
                                            }
                                                // if($fields['fieldid'] === 12){
                                                //     echo "sd".$value;die;
                                                // }
                                            $value = $this->getdependantValue($TabId, $fields['fieldid'], $fields['fieldlabel'], $fields['uitype'], $value,$fields['uitype'] == 26 ? $prevColValue : null);
                                            // if($fields['fieldid'] == 58)
                                            // echo $value;die;
                                            if (empty(trim($value))) {
                                                return [
                                                    'success' => false,
                                                    'error' => "No match found for '$oldvalue' in field " . $fields['fieldlabel'] . " at CSV Row " . ($rowIndex + 1),
                                                ];
                                            }
                                        }
                                        //else if(in_array($fields['uitype'], [8, 12, 22, 25, 26,27]))
                                        else if(in_array($fields['uitype'], [8, 12, 22, 25, 26,27]) && 
                                            (   ($fields['uitype'] == 8 && $fields['typeofdata'] == 'DD~M') ||
                                                ($fields['uitype'] == 12 && ($fields['typeofdata'] == 'DD~M' || $fields['typeofdata'] == 'V~M')) ||
                                                ($fields['uitype'] == 22 && ($fields['typeofdata'] == 'DD~M' || $fields['typeofdata'] == 'V~M')) || 
                                                ($fields['uitype'] == 25 && ($fields['typeofdata'] == 'V~M')) ||
                                                ($fields['uitype'] == 26 && ($fields['typeofdata'] == 'V~M')) ||
                                                ($fields['uitype'] == 27 && ($fields['typeofdata'] == 'V~M'))
                                            )
                                        )
                                        {
                                            return [
                                                    'success' => false,
                                                    'error' => "No match found for '$oldvalue' in field " . $fields['fieldlabel'] . " at CSV Row " . ($rowIndex + 1),
                                                ];
                                        }
                                        // echo "3";
                                    } catch (\Exception $e) {
                                        // $transaction->rollBack();
                                        return [
                                            'success' => false,
                                            'error' => "No match found for '$value' in field " . $fields['fieldlabel'] . " at CSV Row " . ($rowIndex + 1),
                                        ];
                                    }
                                }
                                if($fields['uitype'] == 6) // checkbox
                                {
                                    $value = strtolower($value) == 'yes' ? 1 : 0 ;
                                }
                                // && ($fields['typeofdata'] == 'DT~O' || $fields['typeofdata'] == 'DT~M')
                                // Validate
                                try {
                                    // echo $rowIndex."\n";die;
                                    $this->validateFieldByType($rowIndex + 2, $value, $fields['typeofdata'] ?? '', $fields['fieldlabel']);
                                } catch (\Exception $e) {
                                    // $transaction->rollBack();
                                    return [
                                        'success' => false,
                                        'error' => $e->getMessage()
                                    ];
                                }
                                //this code is here because validateFieldByType want dd-mm-yy and DB want yyyy-mm-dd
                                if (in_array($fields['uitype'], [13, 17])) {
                                    
                                    if($fields['uitype'] == 13)
                                    {
                                            // Convert DD-MM-YYYY HH:MM → YYYY-MM-DD HH:MM:SS
                                        $datetime = DateTime::createFromFormat('d-m-Y H:i', $value);

                                        if ($datetime) {
                                            $value = $datetime->format('Y-m-d H:i:s');
                                        } else {
                                            return [
                                            'success' => false,
                                            'error' => "Invalid '$value' in field " . $fields['fieldlabel'] . " at CSV Row " . ($rowIndex + 1),
                                        ];
                                        }
                                    }
                                    else{
                                        $value = $this->convertDateDMY($value);
                                    }
                                    // echo $value;die;
                                }
                            }
                            $updateData[$fieldName] = $value;

                            // echo "<pre>ss";print_r($updateData);
                        }
                        // echo "<pre>all";print_r($updateData);die;
                        // Required system fields

                        $updatefinalData = array_merge($requiredData, $updateData);
                        //need to add one auto incrment number in crmentity to resolve Acc auto no issue on date 202-09-2025

                        $updatecolumnNames = $updatefinalData;

                        foreach ($updatecolumnNames as $key => $col) {
                            if ($TabId == 18 && preg_match('/^H\d+$/', $key)) {
                                if (trim($col) != '') {
                                    $updaterolewiseuid = User::findOne(['email' => $col]);
                                    if ($updaterolewiseuid) {
                                        $rolematch = (new Query())
                                            ->select('*')
                                            ->from('user2role')
                                            ->where(['userid' => $updaterolewiseuid->id])
                                            ->one();

                                        if ($rolematch['roleid'] === $key) {
                                            $updatecolumnNames[$key] = $updaterolewiseuid->id;
                                        } else {
                                            $rolename = (new Query())
                                                ->select('rolename')
                                                ->from('role')
                                                ->where(['roleid' => $key])
                                                ->one();

                                            return [
                                                'success' => false,
                                                'error' => "Invalid Mapping.\n Please enter a valid Email Id." .
                                                    $fieldnameToCsvHeader[$col] . " - '" . $updatecolumnNames[$col] .
                                                    "' does not have " . $rolename['rolename'] . ' Role at row ' . ($updaterowCount + 1),
                                            ];
                                        }
                                    }
                                }
                            } 
                        }
                       
                        $updateallValues[] = $updatecolumnNames;
                        $updaterowCount++;
                        $updated++;
                    }
                }
                //update code end here

                // echo "sdaDASDA";die;
                // echo "<pre>updateallValues";print_r($updateallValues);

                if ($TabId == 18) {
                    // echo "in tabid 18";
                    if (count($allValues) > 0) {
                            // echo "in allValues<pre>";print_r($allValues);die;
                        $newparams = $newroleparams = $newroleparamskeys = [];

                        foreach ($paramBindings as $key => $value) {
                            if (preg_match('/_(\d+)$/', $key, $matches)) {
                                $index = (int) $matches[1];
                                $newparams[$index][$key] = $value;
                            }
                        }
                        /*if(isset($roleparamBindings)){
                            foreach ($roleparamBindings as $key => $value) {
                                if (preg_match('/_(\d+)$/', $key, $matches)) {
                                    $index = (int)$matches[1];
                                    $newroleparams[$index][$key] = $value;
                                }
                            }
                        }*/
                        // echo "<pre>";print_r($roleparamGroups);die;
                        if (!empty($roleparamGroups)) {
                            foreach ($roleparamGroups as $rowIndex => $groupList) {
                                foreach ($groupList as $group) {
                                    $entry = [];
                                    foreach ($group as $key => $value) {
                                        $entry[$key] = $value;
                                    }
                                    $newroleparams[$rowIndex][] = $entry; // preserve each group as separate
                                }
                            }
                        }


                        // print_r($newroleparams);die;
                        /**if want to add limit  */
                        // $limit = 500; // Set your desired limit
                        // $max = min(count($allValues), $limit); // Ensure you don't exceed actual array size

                        // for ($j = 0; $j < $max; $j++) {
                        $lastInsertId = 0;
                        for ($j = 0; $j < count($allValues); $j++) {
                            // echo "<pre>";print_r($main_columns);
                            // echo "<pre>";print_r($allValues[$j]);die;
                            // echo "<pre>";print_r($newroleparams);die;
                            // Insert into main table
                            $sql = "INSERT INTO $TableName (" . implode(',', $main_columns) . ") VALUES " . $allValues[$j];
                            $command = $connection->createCommand($sql);
                            $command->bindValues($newparams[$j]);
                            $command->execute();

                            $lastInsertId = Yii::$app->db->getLastInsertID();
                            if (!empty($newroleparams) && isset($newroleparams) && is_array($newroleparams)) {
                                // if($j==1)
                                // echo "<pre>"; print_r($newroleparams[$j]);die;
                                // echo "2";
                                if (isset($newroleparams[$j]) && is_array($newroleparams[$j])) {
                                    $newparams_count = count($newroleparams[$j]);
                                    if ($newparams_count > 0) {
                                        for ($k = 0; $k < $newparams_count; $k++) {
                                            if (!empty($newroleparams[$j][$k][':userid_' . $j])) {
                                                $roleid = array_values($newroleparams[$j][$k])[1];
                                                $parentrole = (new Query())
                                                    ->select(['parentrole'])
                                                    ->from('role') // or '{{%role}}' if table uses prefix
                                                    ->where(['roleid' => $roleid])
                                                    ->one();
                                                $parentrole = explode("::", $parentrole['parentrole'])[1];
                                                $parentrole_name = (new Query())
                                                    ->select(['rolename'])
                                                    ->from('role') // or '{{%role}}' if table uses prefix
                                                    ->where(['roleid' => $parentrole])
                                                    ->one();
                                                if (strpos($parentrole_name['rolename'], 'OEM') !== false) {
                                                    $vendor_tablename = 'vendor_account_oem_manager_detail';
                                                } else {
                                                    $vendor_tablename = 'vendor_account_orgaisation_section';
                                                }
                                                // echo $j;
                                                // Add vendoraccid into role params
                                                $roleParams = [":vendoraccid_$j" => $lastInsertId] + $newroleparams[$j][$k];

                                                // echo $vendor_tablename;
                                                // echo "<pre>";print_r($roleParams);die;
                                                // Insert into role mapping table
                                                $sql = "INSERT INTO `$vendor_tablename` (vendoraccid, userid, roleid)
                                                        VALUES (" . implode(",", array_keys($roleParams)) . ")";
                                                $command = $connection->createCommand($sql);
                                                $command->bindValues($roleParams);
                                                $command->execute();
                                            }
                                        }
                                    }
                                }
                            }
                            //this is for adding in modetracker
                            if (is_array($newparams[$j]) && !empty($newparams[$j])) {
                                $cleanedArray = [];
                                foreach ($newparams[$j] as $key => $value) {
                                    // Remove leading ":" and trailing "_number"
                                    $newKey = preg_replace('/^:(.+)_\d+$/', '$1', $key);
                                    $cleanedArray[$newKey] = $value;
                                }
                                // echo "<pre>";print_r($cleanedArray);die;
                                $modlog = new ModtrackerBasic();
                                $modlog->auditlog('', $cleanedArray, $ModuleName, $lastInsertId, 5, Yii::$app->user->id);
                            }
                            // $this->updateCRMSequence($ModuleName, $lastInsertId + 1);
                        }                      
                        // echo $lastInsertId;die;
                        //$this->updateCRMSequence($ModuleName, $lastInsertId + 1); //this need to add for resolve Account no issue on date 202-09-2025
                        
                        // die;
                        // echo "3";
                        /*$autoNo = Yii::$app->db->createCommand("Select cur_id
                        from modentity_num 
                        where semodule = :semodule and active = :active")
                        ->bindValue(':semodule','vendoraccount')
                        ->bindValue(':active',1)
                        ->queryOne();
                        // echo "<pre>";print_r($autoNo);die;
                        $autoNo = ($autoNo['cur_id']) + count($allValues) + 1;
                        Yii::$app->db->createCommand()->update('modentity_num', [
                        'cur_id' => $autoNo,
                        ], [
                            'semodule' => 'vendoraccount'
                        ])->execute();*/
                        // echo "----";
                        // this set while create cust code
                        // $model = new AutoNo();
                        // $model->setAutomoduleno(1, $ModuleName);
                    }
                    
                    if (count($updateallValues) > 0) {
                                        // echo "<pre>in j";print_r($updateallValues);die;    
                        for ($j = 0; $j < count($updateallValues); $j++) {

                                $old_attrbutes = (new \yii\db\Query())
                                    ->select("*")
                                    ->from($TableName)
                                    ->where(["$FieldId" => $updateallValues[$j][$FieldId]])
                                    ->one();

                                // 🔹 Added: Track OEM and ORG changes in arrays
                                $vendorChangesOld = [];
                                $vendorChangesNew = [];

                                $updateallValues_count = count($updateallValues[$j]);
                                if ($updateallValues_count > 0) {
                                    foreach ($updateallValues[$j] as $k => $v) {

                                        if ($TabId == 18 && preg_match('/^H\d+$/', $k)) {
                                            $roleid = $k;

                                            $parentrole = (new Query())
                                                ->select(['parentrole'])
                                                ->from('role')
                                                ->where(['roleid' => $roleid])
                                                ->one();

                                            $parentrole = explode("::", $parentrole['parentrole'])[1];
                                            $parentrole_name = (new Query())
                                                ->select(['rolename'])
                                                ->from('role')
                                                ->where(['roleid' => $parentrole])
                                                ->one();

                                            if (strpos($parentrole_name['rolename'], 'OEM') !== false) {
                                                $vendor_tablename = 'vendor_account_oem_manager_detail';
                                            } else {
                                                $vendor_tablename = 'vendor_account_orgaisation_section';
                                            }

                                            $isfound = (new Query())
                                                ->select("*")
                                                ->from($vendor_tablename)
                                                ->where([
                                                    'roleid' => $roleid,
                                                    $FieldId => $updateallValues[$j]["$FieldId"]
                                                ])
                                                ->one();

                                            // 🔹 Capture old state if record exists
                                            if ($isfound) {
                                                $vendorChangesOld[] = $isfound;
                                            }

                                            if (!isset($v) || trim($v) === '') {
                                                // If blank, delete existing record
                                                if ($isfound) {
                                                    Yii::$app->db->createCommand()
                                                        ->delete($vendor_tablename, [
                                                            'roleid' => $roleid,
                                                            $FieldId => $updateallValues[$j][$FieldId],
                                                        ])
                                                        ->execute();
                                                }
                                            } else {
                                                // If non-empty, replace (delete + insert)
                                                if ($isfound) {
                                                    Yii::$app->db->createCommand()
                                                        ->delete($vendor_tablename, [
                                                            'roleid' => $roleid,
                                                            $FieldId => $updateallValues[$j][$FieldId],
                                                        ])
                                                        ->execute();
                                                }

                                                Yii::$app->db->createCommand()
                                                    ->insert($vendor_tablename, [
                                                        'roleid' => $roleid,
                                                        'userid' => $v,
                                                        $FieldId => $updateallValues[$j][$FieldId],
                                                    ])
                                                    ->execute();

                                                // 🔹 Capture new state
                                                // $vendorChangesNew[] = [
                                                //     'roleid' => $roleid,
                                                //     'userid' => $v,
                                                //     $FieldId => $updateallValues[$j][$FieldId],
                                                //     'tablename' => $vendor_tablename
                                                // ];
                                                // store new value (always as string)
                                                   $vendorChangesNew[$roleid] = (string)$v;
                                            }

                                            unset($updateallValues[$j][$k]);
                                        }
                                    }
                                }

                                $record_id = $updateallValues[$j][$FieldId];
                                unset($updateallValues[$j][$FieldId]);

                                $rowsUpdated = Yii::$app->db->createCommand()
                                    ->update($TableName, $updateallValues[$j], [$FieldId => $record_id])
                                    ->execute();

                                if ($rowsUpdated > 0 || !empty($vendorChangesOld) || !empty($vendorChangesNew)) {
                                    $modlog = new ModtrackerBasic();

                                    // Prepare base audit data
                                        $combinedOld = $old_attrbutes;
                                        $combinedNew = $updateallValues[$j];

                                    if (!empty($vendorChangesOld)) {
                                        $flattenedOld = [];
                                        foreach ($vendorChangesOld as $entry) {
                                            if (isset($entry['roleid']) && isset($entry['userid'])) {
                                                $flattenedOld[$entry['roleid']] = $entry['userid'];
                                            }
                                        }
                                        $vendorChangesOld = $flattenedOld;
                                    }
                                    // Ensure both arrays have string values before merge
                                        // $vendorChangesOld = array_map('strval', $vendorChangesOld);
                                        // $vendorChangesNew = array_map('strval', $vendorChangesNew);
                                        $combinedOld = array_merge($combinedOld, $vendorChangesOld);
                                        $combinedNew = array_merge($combinedNew, $vendorChangesNew);
                                        // echo "<pre>";print_r( $combinedOld );print_r( $combinedNew );die;   
                                    $modlog->auditlog($combinedOld, $combinedNew, $ModuleName, $record_id, 9, Yii::$app->user->id);
                                }
                            }

                    }
                    $transaction->commit();
                    // echo "save";die;
                } else {
                    // $transaction = $connection->beginTransaction();
                    try {
                        /* $batchSize = 500;
                        $totalRows = count($allValues);
                        for ($i = 0; $i < $totalRows; $i += $batchSize) {
                            $currentBatchValues = array_slice($allValues, $i, $batchSize);

                            // Extract parameter bindings for current batch only
                            $currentParamBindings = [];
                            $offset = $i * count($columnNames);
                            $limit = count($currentBatchValues) * count($columnNames);
                            $bindingKeys = array_keys($paramBindings);
                            $bindingChunk = array_slice($bindingKeys, $offset, $limit);

                            foreach ($bindingChunk as $key) {
                                $currentParamBindings[$key] = $paramBindings[$key];
                            }

                            $sql = "INSERT INTO $TableName (" . implode(',', $columnNames) . ") VALUES " . implode(',', $currentBatchValues);
                            $command = $connection->createCommand($sql);
                            $command->bindValues($currentParamBindings);
                            $command->execute();
                        }*/
                            // echo "<pre>";
                            // print_r($allValues);echo "----";print_r($updateallValues);die;
                        if (count($allValues) > 0) {
                            $newparams = [];
                            foreach ($paramBindings as $key => $value) {
                                if (preg_match('/_(\d+)$/', $key, $matches)) {
                                    $index = (int) $matches[1];
                                    $newparams[$index][$key] = $value;
                                }
                            }
                            for ($j = 0; $j < count($allValues); $j++) {
                                //if column name have reserved kyword metting table have from to like column name
                                //// Common MySQL reserved keywords
                                $sqlReserved = [
                                    'add','all','alter','and','as','asc','between','case','check','column','constraint','create','database','default',
                                    'delete','desc','distinct','drop','else','exists','foreign','from','group','having','in','index','insert','into',
                                    'join','key','limit','not','null','on','or','order','primary','references','replace','select','set','table',
                                    'to','trigger','unique','update','values','view','where','when','while','repeat'
                                ];
                                $columnNames = array_map(function($col) use ($sqlReserved) {
                                    if (in_array(strtolower($col), $sqlReserved, true)) {
                                        return "`$col`";
                                    }
                                    return $col;
                                }, $columnNames);
                                // echo "<pre>";print_r($newparams[$j]);die;
                                // $sql = "INSERT INTO $TableName (" . implode(',', $columnNames) . ") VALUES " . implode(',', $allValues);
                                $sql = "INSERT INTO $TableName (" . implode(',', $columnNames) . ") VALUES " . $allValues[$j];
                                // echo $sql;die;
                                $command = $connection->createCommand($sql);
                                $command->bindValues($newparams[$j]);
                                $command->execute();
                                $lastInsertId = Yii::$app->db->getLastInsertID();
                                if ($autoIncrementColumn) {
                                    $autoIncrementColumn_value = $this->getAutoNo($TabId);
                                    //update auto increment value
                                    $sqlupdt = "update $TableName set $autoIncrementColumn= '$autoIncrementColumn_value' where $FieldId = $lastInsertId";
                                    $command = $connection->createCommand($sqlupdt);
                                    //$command->bindValues($newparams[$j]);
                                    $command->execute();

                                    //update auto num
                                    $this->setAutoNo($TabId);
                                }
                                // echo $lastInsertId;
                                if (is_array($newparams[$j]) && !empty($newparams[$j])) {
                                    // echo "1";
                                    $cleanedArray = [];
                                    foreach ($newparams[$j] as $key => $value) {
                                        // Remove leading ":" and trailing "_number"
                                        $newKey = preg_replace('/^:(.+)_\d+$/', '$1', $key);
                                        $cleanedArray[$newKey] = $value;
                                    }
                                    // echo "2";
                                    // echo "<pre>";print_r($cleanedArray);die;
                                    $modlog = new ModtrackerBasic();
                                    $modlog->auditlog('', $cleanedArray, $ModuleName, $lastInsertId, 5, Yii::$app->user->id);
                                }
                                // echo "4";
                                // $model = new AutoNo();
                                // $model->setAutomoduleno(1, $ModuleName);
                            }
                        }
                        // echo count($updateallValues);die;
                        if (count($updateallValues) > 0) {
                            // echo "in updateallValues<pre>";print_r($updateallValues);die;
                            
                            for ($j = 0; $j < count($updateallValues); $j++) {
                                
                            // echo "<pre>";print_r($updateallValues[$j]);die;
                                $old_attrbutes = (new \yii\db\Query())
                                                ->select("*")
                                                ->from($TableName)
                                                ->where(["$FieldId" => $updateallValues[$j][$FieldId]])
                                                ->one();
                                                // echo "<pre>";print_r($old_attrbutes);die;
                                //if column name have reserved kyword metting table have from to like column name
                                //// Common MySQL reserved keywords
                                $sqlReserved = [
                                    'add','all','alter','and','as','asc','between','case','check','column','constraint','create','database','default',
                                    'delete','desc','distinct','drop','else','exists','foreign','from','group','having','in','index','insert','into',
                                    'join','key','limit','not','null','on','or','order','primary','references','replace','select','set','table',
                                    'to','trigger','unique','update','values','view','where','when','while','repeat'
                                ];
                                // echo "asd---<pre>";print_r($updateallValues[$j]["$FieldId"]);die;
                                 Yii::$app->db->createCommand()
                                        ->update($TableName, $updateallValues[$j], [$FieldId => $updateallValues[$j]["$FieldId"]])
                                        ->execute();
                                $modlog = new ModtrackerBasic();
                                $modlog->auditlog($old_attrbutes, $updateallValues[$j], $ModuleName, $updateallValues[$j][$FieldId], 9, Yii::$app->user->id);
                                // echo "asd";die;
                            }
                        }
                        $transaction->commit();
                    // echo "save";die;
                    } catch (\Throwable $e) {
                        $transaction->rollBack();
                        throw $e;
                    } 
            
                }
            // echo "inserted".$inserted."fd-".$fileDuplicateCount."-dd-".$dbDuplicateCount;die;
            // Cleanup
            @unlink($csvPath);
            Yii::$app->session->remove('uploaded_csv_path');

            // echo 
            return [
                'success' => true,
                'inserted' => $inserted,
                'failed' => $failed,
                'errors' => $errors,
                'fileDuplicateCount' => isset($fileDuplicateCount) ? $fileDuplicateCount : '',
                'dbDuplicateCount' => isset($dbDuplicateCount) ? $dbDuplicateCount : ''
            ];
        } catch (\Exception $e) {
            if (isset($transaction))
                $transaction->rollBack();

            return [
                'success' => false,
                'inserted' => $inserted,
                'failed' => count($insertableRows) - $inserted,
                'errors' => ["Insert failed: " . $e->getMessage()],
            ];
        }
    }

    function resolveCommaSeparatedValues($table, $column, $inputValues, $rowIndex, $fieldLabel)
    {
        $targetField = $targetTable = $dispField = '';
        $parts = array_map('trim', explode(',', $inputValues)); // Split CSV values

        $records = (new \yii\db\Query())
            ->select(['id', $column])
            ->from($table)
            ->where(['in', $column, $parts])
            ->all();

        $nameToId = \yii\helpers\ArrayHelper::map($records, $column, 'id');

        $resolvedIds = [];
        foreach ($parts as $val) {
            if (isset($nameToId[$val])) {
                $resolvedIds[] = $nameToId[$val];
            } else {
                throw new \Exception("No match found for '$val' in field '$fieldLabel' at CSV Row " . ($rowIndex + 2));
            }
        }

        return implode(',', $resolvedIds); // Final ID string like "3,4"
    }

    function convertDateDMY($inputDate)
    {
        $inputDate = trim($inputDate);
        if ($inputDate === '')
            return null;

        $date = DateTime::createFromFormat('d-m-Y', $inputDate); // uppercase 'Y' = 4-digit year
        if ($date) {
            return $date->format('Y-m-d'); // MySQL format
        }

        return null; // or handle error
    }

    function getdependantValue($TabId, $fieldid, $fieldlabel, $uitype, $val, $prevColValue)
    {
        $targetField = $targetTable = $dispField = '';
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;

        //24 there is no filed for ui type 24
        if ($uitype == 8  || $uitype == 22 || $uitype == 10) {//single select dropdown,multiselect ,25=mrelated
            $sql = "SELECT * FROM `picklist` WHERE `fieldid` = :fieldid";
            $command = Yii::$app->db->createCommand($sql);
            $command->bindValue(':fieldid', $fieldid);
            $result = $command->queryOne();

            if (!$result) {
                return 'No Picklist found.'; // or handle the case where no picklist record is found
            }

            // Get necessary fields from result
            $targetField = $result['targetfield'];
            $targetTable = $result['targettable'];
            $dispField = $result['dispfield'];
        } else if ($uitype == 12 || $uitype == 26 || $uitype == 27) {
            if(in_array($TabId,['20','21','22','23']) && $uitype == 26)
            {
                // echo "prevColValue".$prevColValue;die;
                $sql = "SELECT * FROM `entityname` WHERE `fieldid` = :fieldid and `modulename` = :modulename";
                $command = Yii::$app->db->createCommand($sql);
                $command->bindValue(':fieldid', $fieldid);
                $command->bindValue(':modulename', $prevColValue);
                $result = $command->queryOne();
            }
            else
            {
                $sql = "SELECT * FROM `entityname` WHERE `fieldid` = :fieldid";
                $command = Yii::$app->db->createCommand($sql);
                $command->bindValue(':fieldid', $fieldid);
                $result = $command->queryOne();
            }
            if (!$result) {
                return 'No Entity Name found.'; // or handle the case where no picklist record is found
            }

            // Get necessary fields from result
            $targetField = $result['entityidfield'];
            $targetTable = $result['targettable'];
            $dispField = $result['shownetityfields'];
            //this condition added to resolve issue aride in meeting module for location to upload bulk data by ptpatel on date 08-10-2025 while creating samples
            if($dispField != $result['fieldname']){
                $dispField = $result['fieldname'];
            }
        } else if($uitype == 25){
            $sql = "
                SELECT 
                    mr.source_module
                FROM module_relation AS mr
                JOIN tab AS t1 ON t1.tabid = mr.source_module
                JOIN tab AS t2 ON t2.tabid = mr.related_module
                WHERE t2.name = :related_module
                AND mr.source_module = (
                    SELECT tabid FROM tab WHERE name = :source_module
                )
            ";

            $command = Yii::$app->db->createCommand($sql);
            $command->bindValue(':related_module', $ModuleName); // the related module name
            $command->bindValue(':source_module', strtolower($val)); // or use another variable if needed
            $result = $command->queryOne();
            return $result ? $result['source_module'] : null;
            
        }

        //for multiple value 
        if ($uitype == 10 || $uitype == 22) {

            $placeholders = [];
            $params = [];
            $parts = array_map('trim', explode(',', $val)); // Split CSV values

            foreach ($parts as $index => $val) {
                $placeholder = ':val' . $index;
                //code added on date 08-10-2025
                if (preg_match('/\w+\s*\(/', $dispField)) {
                    // It's a function — use as is, no backticks
                    $dispField = $dispField;
                } else {
                    // It's a regular column — wrap in backticks
                    $dispField = "`$dispField`";
                }
                $placeholders[] = "LOWER($dispField) = LOWER($placeholder)";
                //code end added on date 08-10-2025
                // $placeholders[] = "LOWER(`$dispField`) = LOWER($placeholder)";
                $params[$placeholder] = $val;
            }

            $whereClause = implode(' OR ', $placeholders);

            $sql = "SELECT `$targetField` FROM `$targetTable` WHERE $whereClause";
            $command = Yii::$app->db->createCommand($sql);
            $command->bindValues($params);
            $result = $command->queryAll();
            return implode(',', array_column($result, $targetField));

        }
        //for single value
        else {
            if (preg_match('/\w+\s*\(/', $dispField)) {
                // It's a function — use as is, no backticks
                $fieldExpr = $dispField;
            } else {
                // It's a regular column — wrap in backticks
                $fieldExpr = "`$dispField`";
            }

            // $value_sql = "SELECT `$targetField` FROM `$targetTable` WHERE LOWER(`$dispField`) = LOWER(:val)";
            $value_sql = "SELECT `$targetField` FROM `$targetTable` WHERE LOWER($fieldExpr) = LOWER(:val)";
            $command_val = Yii::$app->db->createCommand($value_sql);
            $command_val->bindValue(':val', $val);
            $value_result = $command_val->queryOne();
            return $value_result ? $value_result[$targetField] : null;
        }
    }

    function normalizeAndCleanString($str)
    {
        // Normalize Unicode characters if intl extension is enabled
        if (class_exists('Normalizer')) {
            $str = Normalizer::normalize($str, Normalizer::FORM_KC);
        }

        // Remove regular and non-breaking spaces
        $str = preg_replace('/[\x{00A0}\s]+/u', '', $str);

        // Convert to lowercase
        return mb_strtolower(trim($str), 'UTF-8');
    }


    //     function sanitizeUtf8String($value) {
//     // Convert to UTF-8
//     $value = mb_convert_encoding($value, 'UTF-8', 'auto');

    //     // Normalize whitespace
//     $value = preg_replace('/\s+/u', ' ', trim($value));

    //     // Optionally, convert special characters to basic equivalents
//     $value = iconv('UTF-8', 'UTF-8//IGNORE', $value); // remove invalid UTF-8 chars

    //     return $value;
// }

    function fixEncoding($value)
    {
        // Convert from Windows-1252 (or ISO-8859-1) to UTF-8
        return iconv('Windows-1252', 'UTF-8//IGNORE', $value);
    }
    function sanitizeUtf8String($value)
    {
        // Remove non-printable/control characters (except newline/tab if needed)
        $value = preg_replace('/[^\P{C}\n\t]+/u', '', $value);

        // Attempt to detect encoding
        $encoding = mb_detect_encoding($value, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);

        // If detected, convert to UTF-8
        if ($encoding) {
            $value = mb_convert_encoding($value, 'UTF-8', $encoding);
        } else {
            // Fallback to iconv with ignore
            $value = iconv('UTF-8', 'UTF-8//IGNORE', $value);
        }

        // Normalize whitespace
        $value = preg_replace('/\s+/u', ' ', trim($value));

        return $value;
    }


    // end code for improved import  functionality

    //approve contract
    public function actionApprovecontract()
    {
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;

        $Record = $_POST['Recordid'];
        $id = Yii::$app->user->id;

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $actionid = "edit";
        $auditmodel = new EditModel($TableName, $FieldId, $ModuleName, $actionid);

        $newAttribute = $_POST;

        if ($auditmodel->approveContract($Record)) {
            return $this->asJson([
                'status' => 'success',
                'message' => 'Updated successfully.',
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'Failed to update record.',
                'errors' => $auditmodel->getErrors(),
            ]);
        }


        $this->layout = '@app/views/layouts/main-one';
    }

    //approve delivery challan
    public function actionApprovedeliverychallandit()
    {
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;

        $Record = $_POST['Recordid'];
        $id = Yii::$app->user->id;

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $actionid = "edit";
        $auditmodel = new EditModel($TableName, $FieldId, $ModuleName, $actionid);

        $newAttribute = $_POST;

        if ($auditmodel->approveDeliverychallandit($Record)) {
            return $this->asJson([
                'status' => 'success',
                'message' => 'Updated successfully.',
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'Failed to update record.',
                'errors' => $auditmodel->getErrors(),
            ]);
        }


        $this->layout = '@app/views/layouts/main-one';
    }

    /**
     * Sales Order Approve Stage Management
     * @return never
     */
    public function actionApprovesalesorder()
    {
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;

        $Record = $_POST['Recordid'];
        $id = Yii::$app->user->id;

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $actionid = "edit";
        $auditmodel = new EditModel($TableName, $FieldId, $ModuleName, $actionid);

        $newAttribute = $_POST;

        if ($auditmodel->approveSalesOrder($Record)) {
            return $this->asJson([
                'status' => 'success',
                'message' => 'Updated successfully.',
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'Failed to update record.',
                'errors' => $auditmodel->getErrors(),
            ]);
        }


        $this->layout = '@app/views/layouts/main-one';
    }


    //approve contract
    public function actionApprovefocdit()
    {
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;

        $Record = $_POST['Recordid'];
        $id = Yii::$app->user->id;

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $actionid = "edit";
        $auditmodel = new EditModel($TableName, $FieldId, $ModuleName, $actionid);

        $newAttribute = $_POST;

        if ($auditmodel->approveFocdit($Record)) {
            return $this->asJson([
                'status' => 'success',
                'message' => 'Updated successfully.',
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'Failed to update record.',
                'errors' => $auditmodel->getErrors(),
            ]);
        }


        $this->layout = '@app/views/layouts/main-one';
    }

    //approveinvoicedit
    public function actionApproveinvoicedit()
    {
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;

        $Record = $_POST['Recordid'];
        $id = Yii::$app->user->id;

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $actionid = "edit";
        $auditmodel = new EditModel($TableName, $FieldId, $ModuleName, $actionid);

        $newAttribute = $_POST;

        if ($auditmodel->approveInvoicedit($Record)) {
            return $this->asJson([
                'status' => 'success',
                'message' => 'Updated successfully.',
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'Failed to update record.',
                'errors' => $auditmodel->getErrors(),
            ]);
        }


        $this->layout = '@app/views/layouts/main-one';
    }

    //addeed by ptpatel on 16-07-25
    // public function actionGetfilterinputs()
    // {
    //     $FieldId = $this->FieldId;
    //     $ModuleName = $this->ModuleName;
    //     $TableName = $this->TableName;
    //     $TabLabel = $this->TabLabel;
    //     $TabId = $this->TabId;
    //     $layout = $this->layout;

    //     $modelData = [];
    //     echo "hi";die;
    //     if (Yii::$app->request->post()) {
    //         // $sourceid = Yii::$app->request->get('sourceid') ?? null;
    //         // $sourcemodule = Yii::$app->request->get('sourcemodule') ?? null;
    //         // $from_page = Yii::$app->request->post('from');
    //         // $arrRender['sourceid'] = $sourceid;
    //         // $arrRender['sourcemodule'] = $sourcemodule;
    //         $arrRender['modulename'] = $ModuleName;
    //         $arrRender['tablename'] = $TableName;
    //         // $arrRender['columnname'] = Yii::$app->request->post('columnname');
    //         $arrRender['field'] = Field::find()->where(['fieldid' => Yii::$app->request->post('field_id')])
    //             ->asArray()->one();
    //         $uid = Yii::$app->user->id;
    //         // $arrRender['field']['recordid'] = Yii::$app->request->post('recordid');
    //         $arrRender['TabId'] = $TabId;
    //         $arrRender['uid'] = $uid;


    //     $actionid = "create";
    //     $model = new EditModel($TableName, $FieldId, $ModuleName, $actionid);
    //     $arrRender['model'] = $model;
    //     $ActionList = $model->getActionList($ModuleName);
    //     $ActionList['ActionName'] = "Create";
    //     $this->getClientScript($ModuleName, strtolower($actionid));

    //     //if isset sourceid and sourcemodule check related field name 
    //     $relatedkeys = $model->getralatedkeys($TabId);
    //     // print_r($relatedkeys);die;

    //     $arrRender['ActionList'] = $ActionList;


    //         // $actionid = "edit";
    //         // $RecordId = Yii::$app->request->post('recordid');
    //         // // echo $ModuleName;die;
    //         // $model = new EditModel($TableName, $FieldId, $ModuleName, $actionid);
    //         // $model->_members[$FieldId] = $RecordId;
    //         // $arrRender['model'] = $model;
    //         // $this->getClientScript($ModuleName, strtolower($actionid));
    //         //if isset sourceid and sourcemodule check related field name 
    //         // $relatedkeys = $model->getralatedkeys($TabId);
    //         $id = Yii::$app->user->id;
    //         $accessmodel = new AccessCheck();
    //         $tabs = $accessmodel->tabs($id, $ModuleName);
    //         $profile = $accessmodel->profile($id, $tabs, $ModuleName);
    //         $modelaccess = $accessmodel->moduleaccess($id, $profile, $tabs);
    //         $rolebasedrecord = $accessmodel->rolebasedrecord($id, $profile);
    //         $hasadminpower = $accessmodel->hasadminpower($profile);
    //         list($Column, $Record) = $model->getFieldDetail($rolebasedrecord);
    //         echo "<pre>";print_r($Record);exit;
    //         // $this->layout = '@app/views/layouts/main-one';
    //         $this->renderPartial('@app/views/tetra/filterinputfields', ['arrRender' => $arrRender, 'relatedkeys' => $relatedkeys]);
    //     }
    // }


    public function actionGetfilterinputs()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!Yii::$app->request->isPost) {
            return ['html' => '<span class="text-danger">Invalid request</span>'];
        }

        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;
        $TabId = $this->TabId;
        $layout = $this->layout;

        $fieldId = Yii::$app->request->post('field_id');

        $arrRender['modulename'] = $ModuleName;
        $arrRender['tablename'] = $TableName;
        $arrRender['field'] = Field::find()->where(['fieldid' => $fieldId])->asArray()->one();
        $arrRender['TabId'] = $TabId;
        $arrRender['uid'] = Yii::$app->user->id;

        $model = new EditModel($TableName, $FieldId, $ModuleName, 'create');
        $arrRender['model'] = $model;
        $arrRender['ActionList'] = $model->getActionList($ModuleName);
        $this->getClientScript($ModuleName, 'create');

        $relatedkeys = $model->getralatedkeys($TabId);

        $html = $this->renderPartial('@app/views/tetra/filterinputfields', [
            'arrRender' => $arrRender,
            'relatedkeys' => $relatedkeys,
            'ModuleName' => $ModuleName,
        ]);

        return ['html' => $html];
    }

    public function actionApproveraiserequestbyclient()
    {
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;

        $Record = $_POST['Recordid'];
        $id = Yii::$app->user->id;

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $actionid = "edit";
        $auditmodel = new EditModel($TableName, $FieldId, $ModuleName, $actionid);

        $newAttribute = $_POST;

        if ($auditmodel->approveraiserequestbyclient($Record)) {
            //below line code commented on date 13-01-2026 as per v11- 209
            // if($this->emailForRequest($Record,'client')){
                return $this->asJson([
                    'status' => 'success',
                    'message' => 'Updated successfully.',
                ]);
            // }
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'Failed to update record.',
                'errors' => $auditmodel->getErrors(),
            ]);
        }


        $this->layout = '@app/views/layouts/main-one';
    }

    public function actionApproveraiserequestbyvendor()
    {
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;

        $Record = $_POST['Recordid'];
        $id = Yii::$app->user->id;

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        $actionid = "edit";
        $auditmodel = new EditModel($TableName, $FieldId, $ModuleName, $actionid);

        $newAttribute = $_POST;

        if ($auditmodel->approveRaiserequestbyvendor($Record)) {
            //below line code commented on date 13-01-2026 as per v11- 209
            // if($this->emailForRequest($Record,'vendor')){
                return $this->asJson([
                    'status' => 'success',
                    'message' => 'Updated successfully.',
                ]);
            // }
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'Failed to update record.',
                'errors' => $auditmodel->getErrors(),
            ]);
        }


        $this->layout = '@app/views/layouts/main-one';
    }
    
    public function emailForRequest($Record,$type)
    {
        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;

        $rootDir = dirname(__DIR__);
            // echo $rootDir;die;
            require_once($rootDir . '/../PHPMailer/src/Exception.php');
            require_once($rootDir . '/../PHPMailer/src/PHPMailer.php');
            require_once($rootDir . '/../PHPMailer/src/SMTP.php');
            require_once($rootDir . '/../api/params.php');

            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->Host = SMTP_HOST;
            $mail->Port = SMTP_PORT;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USER;
            $mail->Password = SMTP_PASS;
            $mail->SMTPSecure = 'tls';     // Enable TLS encryption

            $mail->SetFrom('erp@Dwmpl.com');
            $mail->isHTML(true);

        // $link = Yii::$app->request->hostInfo . Yii::$app->request->url;
        $email_message = $subject = '';
        $subject = 'Deshwal ERP - ';
        $baseControllerUrl = Yii::$app->request->hostInfo 
            . Yii::$app->urlManager->baseUrl 
            . '/' . Yii::$app->controller->module->id 
            . '/' ;
        $detaillink  = $baseControllerUrl.'detail?Record='.$Record;
        // echo $detaillink;die;
         $email_message .= 'Hello User, <br/> Your Vendor Request (';
        if($type == "client"){
            // echo "3c";
            $request_info = RaiserequestClient::findOne($Record); 
            if($request_info->status == 3)        
                $AR_Text = 'Approved';
            else if($request_info->status == 4)   
                $AR_Text = 'Rejected';
            $to_mail_id = $request_info->email;
            $subject .= "Client Request Status (Request No. ".$request_info->raiserequest_client_no.")".$AR_Text;
            $email_message .= "Request No. ".$request_info->raiserequest_client_no.') has been '.$AR_Text.". <br/> <a href=".$detaillink.">View Details</a>";
        }
        else if($type == "vendor"){
            // echo "3v";
            $request_info = RaiserequestVendor::findOne($Record);
             if($request_info->status == 3)        
                $AR_Text = 'Approved';
            else if($request_info->status == 4)   
                $AR_Text = 'Rejected';
            $to_mail_id = $request_info->email;
            $subject .= "Vendor Request Status (Request No. ".$request_info->raiserequest_vendor_no.")".$AR_Text;
            $email_message .= "Request No. ".$request_info->raiserequest_vendor_no.') has been '.$AR_Text.". <br/> <a href=".$detaillink.">View Details</a>";
        }

        // $to_mail_id = 'durgesh.tetra@gmail.com';
        // $to_mail_id = 'deepika.tetra@gmail.com';

        $mail->Subject = $subject;
        $mail->AddAddress($to_mail_id);
        $mail->SetFrom('erp@Dwmpl.com');
        $mail->isHTML(true);
        $mail->MsgHTML($email_message);
        // $mail->AddBCC('deepika.tetra@gmail.com');
        $mail->AddBCC('rakeshdubey@tetrain.com');
        // echo "<br>Final Mail Object=<pre>";
        // print_r($mail);
        try {
               /* $sent = Yii::$app->mailer
                    ->compose()
                    ->setFrom(['erp@Dwmpl.com'])
                    ->setTo($to_mail_id)
                    ->setSubject($subject)
                    ->setHtmlBody($email_message)   // or setTextBody(strip_tags($email_message))
                    ->send();

                if ($sent) {
                    return true;
                } else {
                    return false;
                }*/
                    if (!$mail->Send()) {
                        // echo "Mailer Error: " . $mail->ErrorInfo;
                    return false;
                } else {
                    // echo "<br>Mail sent successfully";
                    return true;
                }
            } catch (\Exception $e) {
                Yii::error("Mailer Error: " . $e->getMessage(), __METHOD__);
                return "Mailer Exception: " . $e->getMessage();
            }
    }

    public function actionPopuplistproduct()
    {
        $download = Yii::$app->request->get('download');
        $post     = Yii::$app->request->getRawBody();
        $params   = json_decode($post, true);

        $usedInventoryIds = isset($params['exclude_inv']) ? $params['exclude_inv'] : [];
        $usedInventoryIds = array_unique($usedInventoryIds);
        $excludeSoId      = (isset($params['salesorder_id']) && $params['salesorder_id'] != '') ? $params['salesorder_id'] : 0;

        $subQuery = (new \yii\db\Query())
            ->select([
                'i.inventory_id',
                '(i.qty - IFNULL(SUM(soid.qty), 0)) AS qty',
                'i.lot_no',
                'i.category',
                'i.subcategory',
                'i.tag_number',
                'i.product_name AS product_id',
                'pr.product_name',
                'c.prod_category_value',
                'sc.sub_catagory_value',
                'pr.gst_percentage',
                'pr.hsn_code',
                '(SELECT pcd.quoted_price_gst_exclude
                    FROM pickup p
                    INNER JOIN sourcingdeal sd ON sd.sourcingdeal_id = p.opportuity_name
                    INNER JOIN product_costing pc ON pc.related_to_id = sd.sourcingdeal_id
                    INNER JOIN product_costing_detail pcd ON pc.product_costing_id = pcd.product_costing_id
                    WHERE p.pickup_no = i.pickup_id
                    LIMIT 1
                ) AS quoted_price_gst_exclude',
                '(SELECT pcd.sp_exclusive_gst
                    FROM pickup p
                    INNER JOIN sourcingdeal sd ON sd.sourcingdeal_id = p.opportuity_name
                    INNER JOIN product_costing pc ON pc.related_to_id = sd.sourcingdeal_id
                    INNER JOIN product_costing_detail pcd ON pc.product_costing_id = pcd.product_costing_id
                    WHERE p.pickup_no = i.pickup_id
                    LIMIT 1
                ) AS sp_exclusive_gst',
            ])
            ->from(['i' => 'inventory'])
            ->leftJoin(
                ['soid' => 'salesorder_items_detail'],
                'soid.inventory_id = i.inventory_id 
                AND soid.salesorder_id != ' . (int)$excludeSoId . ' 
                AND soid.salesorder_id NOT IN (SELECT salesorder_id FROM sales_order WHERE deleted =1)'
            )
            ->innerJoin(['pr' => 'products'], 'pr.products_id = i.product_name')
            ->innerJoin(['c' => 'prod_category'], 'c.prod_category_id = i.category')
            ->innerJoin(['sc' => 'prod_sub_catagory'], 'sc.sub_catagory_id = i.subcategory')
            ->where(['i.deleted' => 0])
            ->groupBy([
                'i.inventory_id',
                'i.lot_no',
                'i.category',
                'i.subcategory',
                'i.tag_number',
                'i.product_name',
                'pr.product_name',
                'c.prod_category_value',
                'sc.sub_catagory_value',
                'pr.gst_percentage',
                'pr.hsn_code'
            ]);

        if (!empty($usedInventoryIds)) {
            $subQuery->andWhere(['NOT IN', 'i.inventory_id', $usedInventoryIds]);
        }

        $query = (new \yii\db\Query())
            ->from(['sub' => $subQuery])
            ->where(['>', 'sub.qty', 0])
            ->andWhere(['IS NOT', 'sub.quoted_price_gst_exclude', null])
            ->andWhere(['IS NOT', 'sub.sp_exclusive_gst', null]);

        if ($download) {
            $allProducts = $query->orderBy('sub.inventory_id')->all();

            $baseDir = Yii::getAlias('@webroot/thememain/uploads/csv');
            if (!is_dir($baseDir)) {
                mkdir($baseDir, 0775, true);
            }

            $fileName = 'sales_order_bulk_' . date('Ymd_His') . '.csv';
            $filePath = $baseDir . DIRECTORY_SEPARATOR . $fileName;

            $fp = fopen($filePath, 'w');
            fputcsv($fp, ['Tag Number', 'QTY', 'Selling Price']);
            foreach ($allProducts as $row) {
                fputcsv($fp, [
                    $row['tag_number'],
                    (int)$row['qty'],
                    $row['sp_exclusive_gst'],
                ]);
            }
            fclose($fp);

            if (!file_exists($filePath)) {
                throw new \yii\web\NotFoundHttpException("CSV file not found.");
            }

            return Yii::$app->response->sendFile($filePath, $fileName, [
                'mimeType' => 'text/csv',
                'inline'   => false,
            ]);
        }

    $page    = (int)Yii::$app->request->get('page', 1);
    $perPage = (int)Yii::$app->request->get('per_page', 20);
    $page    = max(1, $page);
    $perPage = $perPage > 0 ? $perPage : 20;
    $offset  = ($page - 1) * $perPage;

    $searchLotNo       = Yii::$app->request->get('search_lot_no', '');
    $searchProductName = Yii::$app->request->get('search_product_name', '');
    $searchCategory    = Yii::$app->request->get('search_category', '');
    $searchSubCategory = Yii::$app->request->get('search_sub_category', '');
    $searchTagNo       = Yii::$app->request->get('search_tag_no', '');

    if ($searchLotNo !== '') {
        $query->andWhere(['like', 'sub.lot_no', $searchLotNo]);
    }
    if ($searchProductName !== '') {
        $query->andWhere(['like', 'sub.product_name', $searchProductName]);
    }
    if ($searchCategory !== '') {
        $query->andWhere(['sub.category' => $searchCategory]);
    }
    if ($searchSubCategory !== '') {
        $query->andWhere(['sub.subcategory' => $searchSubCategory]);
    }
    if ($searchTagNo !== '') {
        $query->andWhere(['like', 'sub.tag_number', $searchTagNo]);
    }

    $byTag = (int)Yii::$app->request->get('by_tag', 0);

    if ($byTag) {
        $totalCount = (int)$query->count();
        $allProducts = $query
            ->orderBy('sub.inventory_id')
            ->limit(1)
            ->all();
    } else {
        $totalCount = (int)$query->count();
        $allProducts = $query
            ->orderBy('sub.inventory_id')
            ->offset($offset)
            ->limit($perPage)
            ->all();
    }


        $categories = (new \yii\db\Query())
            ->select(['prod_category_id', 'prod_category_value'])
            ->from('prod_category')
            ->where(['is_active' => 1])
            ->all();

        $subcategories = (new \yii\db\Query())
            ->select(['sub_catagory_id', 'sub_catagory_value', 'prod_catagory_id'])
            ->from('prod_sub_catagory')
            ->where(['is_active' => 1])
            ->all();

        $this->layout = false;
        return $this->renderPartial('@app/views/tetra/PopupP', [
            'allProducts'   => $allProducts,
            'categories'    => $categories,
            'subcategories' => $subcategories,
            'totalCount'    => $totalCount,
            'page'          => $page,
            'perPage'       => $perPage,
        ]);
    }
    /**
     * Delete the tag number from update page
     * @return array{message: string, status: string}
     */
    public function actionDeleteitembyinvid() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $post = Yii::$app->request->getRawBody();
        $params = json_decode($post, true);
        $inventory_id = $params['inventory_id'] ?? null;

        if (!$inventory_id) {
            return ['status' => 'fail', 'message' => 'No tag_number provided'];
        }
        $result = \Yii::$app->db->createCommand()
            ->delete('salesorder_items_detail', ['inventory_id' => $inventory_id])
            ->execute();

        if ($result) {
            return ['status' => 'success', 'message' => 'Deleted'];
        }
        return ['status' => 'fail', 'message' => 'Could not delete DB record'];
    }

    //code added by ptpatel on date 10-11-2025
    public function actionGetaccountsforreport()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $TabId = $this->TabId;
        $FieldId = $this->FieldId;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;

        $id = Yii::$app->user->id;

        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $modelaccess = $model->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);
        $hasadminpower = $model->hasadminpower($profile);
        if($hasadminpower == 1){
            $vendor_accounts = VendorAccount::find()->select(['vendoraccid', 'acc_name'])->where(['deleted'=>0])->all();
        }
        else{
            $vendor_accounts = VendorAccount::find()->select(['vendoraccid', 'acc_name'])->where(['deleted'=>0,'ownerid'=>$id])->all();
        }
        $vendorList = ArrayHelper::map($vendor_accounts, 'vendoraccid ', 'acc_name');
       
        return [
            'status' => 'success',
            'data' => $vendor_accounts
        ];
    }

    //end code added by ptaptel on date 10-11-2025
    //code added by ptpatel on date 29-11-2025 for rightside related module column 
    public function getValues($field, $Record)
    {
        $targetField = $targetTable = $dispField = '';
        $TabId = $this->TabId;
        $uitype = $field["uitype"];
        $fieldid = $field["fieldid"];

        if ($uitype == 17) {
            return date('d-m-Y', strtotime($Record));
        }else if ($uitype == 13) {
            return date('d-m-Y h:i:s', strtotime($Record));
        } else if ($uitype == 6) {
            return ($Record == "1") ? 'Yes' : 'No';
        } else if (in_array($uitype, [8, 12, 22, 27, 10, 9, 25, 26, 28])) {
            //24 there is no filed for ui type 24
            if ($uitype == 8  || $uitype == 22 || $uitype == 10) { //single select dropdown,multiselect ,25=mrelated
                $sql = "SELECT * FROM `picklist` WHERE `fieldid` = :fieldid";
                $command = Yii::$app->db->createCommand($sql);
                $command->bindValue(':fieldid', $fieldid);
                $result = $command->queryOne();

                if (!$result) {
                    return 'No Picklist found.'; // or handle the case where no picklist record is found
                }

                // Get necessary fields from result
                $targetField = $result['targetfield'];
                $targetTable = $result['targettable'];
                $dispField = $result['dispfield'];
            } else if ($uitype == 12 || $uitype == 26 || $uitype == 27 || $uitype == 28) {
                if (in_array($TabId, ['20', '21', '22', '23']) && $uitype == 26) {
                    // echo "prevColValue".$prevColValue;die;
                    $modulename = Tab::find(['modulename'])->where(["tabid" => $field['tabid']])->scalar();
                    $sql = "SELECT * FROM `entityname` WHERE `fieldid` = :fieldid and `modulename` = :modulename";
                    $command = Yii::$app->db->createCommand($sql);
                    $command->bindValue(':fieldid', $fieldid);
                    $command->bindValue(':modulename', $modulename);
                    $result = $command->queryOne();
                } else {
                    $sql = "SELECT * FROM `entityname` WHERE `fieldid` = :fieldid";
                    $command = Yii::$app->db->createCommand($sql);
                    $command->bindValue(':fieldid', $fieldid);
                    $result = $command->queryOne();
                }
                if (!$result) {
                    return 'No Entity Name found.'; // or handle the case where no picklist record is found
                }

                // Get necessary fields from result
                $targetField = $result['entityidfield'];
                $targetTable = $result['targettable'];
                $dispField = $result['shownetityfields'];
                //this condition added to resolve issue aride in meeting module for location to upload bulk data by ptpatel on date 08-10-2025 while creating samples
                if ($dispField != $result['fieldname']) {
                    $dispField = $result['fieldname'];
                }
            } else if ($uitype == 25) {
                $modulename = Tab::find(['modulename'])->where(["tabid" => $field['tabid']])->scalar();

                $sql = "SELECT 
                    t1.name AS source_module
                FROM module_relation AS mr
                JOIN tab AS t1 ON t1.tabid = mr.source_module
                JOIN tab AS t2 ON t2.tabid = mr.related_module
                WHERE t2.tabid = :related_module
                AND mr.source_module = :source_module";

                $command = Yii::$app->db->createCommand($sql);
                $command->bindValue(':related_module', $modulename); // the related module name
                $command->bindValue(':source_module', strtolower($Record)); // or use another variable if needed
                $result = $command->queryOne();
                return $result ? $result['source_module'] : null;
            }


            if ($uitype == 10 || $uitype == 22) {
                if (preg_match('/\w+\s*\(/', $dispField)) {
                    // It's a function — use as is, no backticks
                    $fieldExpr = $dispField;
                } else {
                    // It's a regular column — wrap in backticks
                    $fieldExpr = "`$dispField`";
                }
                $placeholders = [];
                $params = [];
                $parts = array_map('trim', explode(',', $Record)); // Split CSV values
                // echo "<pre>";print_r($parts);die;
                foreach ($parts as $index => $val) {
                    $placeholder = ':val' . $index;
                    //code added on date 08-10-2025
                    // if (preg_match('/\w+\s*\(/', $targetField)) {
                    //     // It's a function — use as is, no backticks
                    //     $targetField = $targetField;
                    // } else {
                    //     // It's a regular column — wrap in backticks
                    //     $targetField = "`$targetField`";
                    // }
                    $placeholders[] = "LOWER($targetField) = LOWER($placeholder)";
                    //code end added on date 08-10-2025
                    // $placeholders[] = "LOWER(`$dispField`) = LOWER($placeholder)";
                    $params[$placeholder] = $val;
                }

                $whereClause = implode(' OR ', $placeholders);

                // $sql = "SELECT `$targetField` FROM `$targetTable` WHERE $whereClause";
                // $sql = "SELECT `$dispField` FROM `$targetTable` WHERE $whereClause";
                $sql = "SELECT $fieldExpr FROM `$targetTable` WHERE $whereClause";
                $command = Yii::$app->db->createCommand($sql);
                $command->bindValues($params);
                $result = $command->queryAll();
                // return implode(',', array_column($result, $targetField));
                return implode(',', array_column($result, $dispField));
            }
            //for single value
            else {
                if (preg_match('/\w+\s*\(/', $dispField)) {
                    // It's a function — use as is, no backticks
                    $fieldExpr = $dispField;
                } else {
                    // It's a regular column — wrap in backticks
                    $fieldExpr = "`$dispField`";
                }

                // $value_sql = "SELECT `$targetField` FROM `$targetTable` WHERE LOWER(`$dispField`) = LOWER(:val)";
                // $value_sql = "SELECT `$targetField` FROM `$targetTable` WHERE LOWER($fieldExpr) = LOWER()";
                // $value_sql = "SELECT `$dispField` FROM `$targetTable` WHERE LOWER($targetField) = LOWER(:val)";
                $value_sql = "SELECT $fieldExpr FROM `$targetTable` WHERE LOWER($targetField) = LOWER(:val)";
                $command_val = Yii::$app->db->createCommand($value_sql);
                $command_val->bindValue(':val', $Record);
                $value_result = $command_val->queryOne();
                // echo "<pre>";print_r($value_result);die;
                return $value_result ? $value_result[$dispField] : null;
            }
        } else if ($uitype == 53) {
            $sql = "SELECT first_name,last_name from  user WHERE `id` = :fieldid";
                $command = Yii::$app->db->createCommand($sql);
                $command->bindValue(':fieldid', $Record);
                $result = $command->queryOne();
            return $result['first_name']." ".$result['last_name'];
        } //in array if condition close here
        else
        {
            return $Record;
        }

    }
     //code added by ptpatel on date 29-11-2025
     //code added by ptpatel on date 09-12-2025
    /*public function actionBulkupdateinventorystatus()
    {
        // echo "in module controller";die;
         ini_set('memory_limit', '2024M');
         set_time_limit(0);
        //  var_dump(ini_get('memory_limit')); die;
       
        $ModuleName = $this->ModuleName;
        $TabId = $this->TabId;
        $tabids_sc_c = [69,70];
        $file = UploadedFile::getInstanceByName('csvfile');

        if (!$file) {
            return $this->asJson(['success' => false, 'message' => 'File missing']);
        }

        // $csvData = array_map('str_getcsv', file($file->tempName));
        // unset($csvData[0]); // remove header
        //to overcome memory issue for 20000 records
        $handle = fopen($file->tempName, 'r');
        fgetcsv($handle); // skip header

        $updated = 0;
        $errors  = [];
        $rownumber = 2;//1 is always header in csv file

        // Start transaction
        $transaction = Yii::$app->db->beginTransaction();
        $statusLookup = (new \yii\db\Query())
            ->select(['LOWER(status_value) AS status_value', 'status_id'])
            ->from('inv_status')
            ->indexBy('status_value')
            ->column();

        $statusMap = (new \yii\db\Query())
            ->select(['status_id', 'status_value'])
            ->from('inv_status')
            ->all();

        $statusMap = ArrayHelper::map($statusMap, 'status_id', function ($row) {
            return strtolower($row['status_value']);
        });

        try {

            // foreach ($csvData as $row) {
            //to overcome memory issue for 20000 records
            while (($row = fgetcsv($handle)) !== false) {

                if (empty($row[0])) {
                    $errors[] = "Empty tag number found in CSV at Row No " . $rownumber;
                    $rownumber++;
                    continue;
                }

                if (!in_array($TabId, $tabids_sc_c)){
                    if (empty($row[1])) {
                        $errors[] = "Empty status found in CSV at Row No " . $rownumber;
                        $rownumber++;
                        continue;
                    }
                }

                $tagNumber = strtolower(trim($row[0]));
                $newStatus = '';
                if (!in_array($TabId, $tabids_sc_c)){
                    // Fetch status_id from inv_status table
                    

                    $statusId  = $statusLookup[strtolower(trim($row[1]))] ?? null;
                    
                    if (!$statusId) {
                        $errors[] = "Invalid status: '{$row[1]}' at Row No $rownumber";
                        $rownumber++;
                        continue;
                    }

                    $newStatus = trim($statusId);
                }

                // Get inventory record by tag number
                $inventory = Inventory::find()
                    ->where('LOWER(tag_number) = :tag', [':tag' => $tagNumber])
                    ->one();

                if (!$inventory) {
                    $errors[] = "Tag not found: $tagNumber at Row No " . $rownumber;
                    $rownumber++;
                    continue;
                }

                
                if (!in_array($TabId, $tabids_sc_c)){
                    // VALIDATION: old status
                    // $oldStatus = (new \yii\db\Query())
                    //     ->select('LOWER(status_value)')
                    //     ->from('inv_status')
                    //     ->where(['status_id' => $inventory->status])
                    //     ->scalar();
                    $oldStatus = $statusMap[$inventory->status] ?? null;

                    $newStatusText = strtolower(trim($row[1]));
                    // echo $inventory->status." n - ".$newStatus;die;
                    // RULE: Old and new status must not be same
                    if ($inventory->status == $newStatus) {
                        $errors[] = "Old status and new status are same for tag: $tagNumber (Row No $rownumber)";
                        $rownumber++;
                        continue;
                    }
                    // RULE 1: Inventory → cannot change
                    if ($inventory->status == 1 || $inventory->status == 2 || $inventory->status == 5) {
                        $errors[] = "Status cannot be changed for tag: $tagNumber because current status is $oldStatus (Row No $rownumber)";
                        $rownumber++;
                        continue;
                    }

                    // RULE 2: Sticker removal → only 'Cleaning Require'
                    if ($inventory->status == 3 && $newStatus != 4) {
                        $errors[] = "Current Status of $tagNumber is '$oldStatus' (Row No $rownumber). Only 'Cleaning Require' allowed.";
                        $rownumber++;
                        continue;
                    }

                    // RULE 3: Cleaning require → only 'IQC Require'
                    if ($inventory->status == 4 && $newStatus != 5) {
                        $errors[] = "Current Status of $tagNumber is '$oldStatus' (Row No $rownumber). Only 'IQC Require' allowed.";
                        $rownumber++;
                        continue;
                    }
                    

                    $item = [
                        'status'        => $newStatus,
                        'inventory_id'  => $inventory->inventory_id
                    ];
                }
                else if($TabId == 69)//sticker removal
                {
                    if($inventory->status != 3)
                    {   
                        $oldStatus = (new \yii\db\Query())
                        ->select('LOWER(status_value)')
                        ->from('inv_status')
                        ->where(['status_id' => $inventory->status])
                        ->scalar();
                        $errors[] = "Stage Error: current status of Tag No '$tagNumber' is '$oldStatus' at Row No " . $rownumber;
                        $rownumber++;
                        continue;
                    }

                        $item = [
                        'tag_number'    => $tagNumber,
                        'status'        => 4,//cleaning
                        'inventory_id'  => $inventory->inventory_id
                    ];
                }
                else if($TabId == 70)//cleaning
                {
                    if($inventory->status != 4)
                    {   
                        $oldStatus = (new \yii\db\Query())
                        ->select('LOWER(status_value)')
                        ->from('inv_status')
                        ->where(['status_id' => $inventory->status])
                        ->scalar();
                        $errors[] = "Stage Error: current status of Tag No '$tagNumber' is '$oldStatus' at Row No " . $rownumber;
                        $rownumber++;
                        continue;
                    }

                        $item = [
                        'tag_number'    => $tagNumber,
                        'status'        => 5,//IQC Require
                        'inventory_id'  => $inventory->inventory_id
                    ];
                }

                $result = $inventory->updateInventoryStatus($item, $ModuleName);

                if ($result) {
                    $updated++;
                } else {
                    $errors[] = "Failed to update tag: $tagNumber at Row No " . $rownumber;
                }

                $rownumber++;
            }
            //to overcome memory issue for 20000 records
            fclose($handle);

            //  Errors found → rollback
            if (!empty($errors)) {
                $transaction->rollBack();
                return $this->asJson([
                    'success' => false,
                    'updated' => 0,
                    'errors'  => $errors,
                    'message' => "Bulk update rolled back due to validation errors."
                ]);
            }

            // No errors → commit
            $transaction->commit();

            return $this->asJson([
                'success' => true,
                'updated' => $updated,
                'errors'  => []
            ]);

        } catch (\Exception $e) {
            // In case of exception → rollback
            $transaction->rollBack();

            return $this->asJson([
                'success' => false,
                'message' => 'Error occurred: ' . $e->getMessage(),
                'errors' => []
            ]);
        }
    }*/

   /* public function actionBulkupdateinventorystatus()
    {
        ini_set('memory_limit', '2024M');
        set_time_limit(0);

        $ModuleName = $this->ModuleName;
        $TabId = $this->TabId;
        $tabids_sc_c = [69,70];
        $file = UploadedFile::getInstanceByName('csvfile');

        if (!$file) {
            return $this->asJson(['success' => false, 'message' => 'File missing']);
        }

        $handle = fopen($file->tempName, 'r');
        fgetcsv($handle); // skip header

        $updated = 0;
        $errors  = [];
        $rownumber = 2;

        $modifiedtime = date("Y-m-d H:i:s");
        $modifiedBy   = Yii::$app->user->id;

        // ✅ Chunk settings 
        $chunkSize   = 500;
        $chunkCount  = 0;

        // $statusLookup = (new \yii\db\Query())
        //     ->select(['LOWER(status_value) AS status_value', 'status_id'])
        //     ->from('inv_status')
        //     ->indexBy('status_value')
        //     ->column();

        $statusMaps = (new \yii\db\Query())
            ->select(['status_id', 'status_value'])
            ->from('inv_status')
            ->all();

        $statusMap = ArrayHelper::map($statusMaps, 'status_id', function ($row) {
            return strtolower($row['status_value']);
        });

        $statusValueToId = [];

        foreach ($statusMaps as $row) {
            $statusValueToId[strtolower($row['status_value'])] = $row['status_id'];
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {

            while (($row = fgetcsv($handle)) !== false) {

                // ================= EXISTING LOGIC (UNCHANGED) ================= 

                if (empty($row[0])) {
                    $errors[] = "Empty tag number found in CSV at Row No " . $rownumber;
                    $rownumber++; $chunkCount++;
                    continue;
                }

                // if (!in_array($TabId, $tabids_sc_c) && empty($row[1])) {
                 if ($TabId == 33 && empty($row[1])) {
                    $errors[] = "Empty status found in CSV at Row No " . $rownumber;
                    $rownumber++; $chunkCount++;
                    continue;
                }

                $tagNumber = strtolower(trim($row[0]));
                $newStatus = '';

                if ($TabId == 33) {
                    $statusId = $statusValueToId[strtolower(trim($row[1]))] ?? null;
                    if (!$statusId) {
                        $errors[] = "Invalid status: '{$row[1]}' at Row No $rownumber";
                        $rownumber++; $chunkCount++;
                        continue;
                    }
                    $newStatus = trim($statusId);
                }

                $inventory = Inventory::find()->select(['inventory_id', 'status'])
                    ->where('LOWER(tag_number) = :tag', [':tag' => $tagNumber])
                    ->one();

                if (!$inventory) {
                    $errors[] = "Tag not found: $tagNumber at Row No " . $rownumber;
                    $rownumber++; $chunkCount++;
                    continue;
                }

                if ($TabId == 33) {                    
                    //to add log details
                    $log = new InventoryLogDetails();
                    
                    $oldStatus = $statusMap[$inventory->status] ?? null;

                    if ($inventory->status == $newStatus) {
                        $errors[] = "Old status and new status are same for tag: $tagNumber (Row No $rownumber)";
                        $rownumber++; $chunkCount++;
                        continue;
                    }

                    if (in_array($inventory->status, [1,2,5])) {
                        $errors[] = "Status cannot be changed for tag: $tagNumber because current status is $oldStatus (Row No $rownumber)";
                        $rownumber++; $chunkCount++;
                        continue;
                    }

                    if ($inventory->status == 3 && $newStatus != 4) {
                        //if this status 3 (sticker removal) then update it in status 4 (cleaning) so set log for sticker removal
                        $log->sticker_removal_updatedby = $modifiedBy;
                        $log->sticker_removal_updated_at = $modifiedtime;

                        $errors[] = "Current Status of $tagNumber is '$oldStatus' (Row No $rownumber). Only 'Cleaning Require' allowed.";
                        $rownumber++; $chunkCount++;
                        continue;
                    }

                    if ($inventory->status == 4 && $newStatus != 5) {
                         //if this status 4 (cleaning) then update it in status 5 (iqc require) so set log for cleaning
                        $log->cleaning_updatedby = $modifiedBy;
                        $log->cleaning_updated_at = $modifiedtime;

                        $errors[] = "Current Status of $tagNumber is '$oldStatus' (Row No $rownumber). Only 'IQC Require' allowed.";
                        $rownumber++; $chunkCount++;
                        continue;
                    }

                   
                    Yii::$app->db->createCommand()->update(
                            'inventory',
                            [
                                'status'        => $newStatus,
                                'modifiedtime'  => $modifiedtime,
                                'modifiedby'    => $modifiedBy,
                            ],
                            ['inventory_id' => $inventory->inventory_id]
                        )->execute();


                    //this give data to check that inventory update from inventory tab
                    $log->inventory_id = $inventory->inventory_id;
                    $log->inventorystatus_updatedby = $modifiedBy;
                    $log->inventorystatus_updated_at = $modifiedtime;
                    $log->save(false);

                    $updated++;
                            
                            
                } elseif ($TabId == 69) {

                    if ($inventory->status != 3) {
                        $oldStatus = $statusMap[$inventory->status] ?? null;
                        $errors[] = "Stage Error: current status of Tag No '$tagNumber' is '$oldStatus' at Row No $rownumber";
                        $rownumber++; $chunkCount++;
                        continue;
                    }

                     Yii::$app->db->createCommand()->update(
                            'inventory',
                            [
                                'status'        => 4,
                                'modifiedtime'  => $modifiedtime,
                                'modifiedby'    => $modifiedBy,
                            ],
                            ['inventory_id' => $inventory->inventory_id]
                        )->execute();

                        // Inventory log
                        $log = new InventoryLogDetails();
                        $log->inventory_id = $inventory->inventory_id;
                        $log->sticker_removal_updatedby = $modifiedBy;
                        $log->sticker_removal_updated_at = $modifiedtime;
                        $log->save(false);
                        $updated++;

                } elseif ($TabId == 70) {

                    if ($inventory->status != 4) {
                        $oldStatus = $statusMap[$inventory->status] ?? null;
                        $errors[] = "Stage Error: current status of Tag No '$tagNumber' is '$oldStatus' at Row No $rownumber";
                        $rownumber++; $chunkCount++;
                        continue;
                    }

                     Yii::$app->db->createCommand()->update(
                            'inventory',
                            [
                                'status'        => 5,
                                'modifiedtime'  => $modifiedtime,
                                'modifiedby'    => $modifiedBy,
                            ],
                            ['inventory_id' => $inventory->inventory_id]
                        )->execute();

                        // Inventory log
                        $log = new InventoryLogDetails();
                        $log->inventory_id = $inventory->inventory_id;
                        $log->cleaning_updatedby = $modifiedBy;
                        $log->cleaning_updated_at = $modifiedtime;
                        $log->save(false);
                        $updated++;

                } else {
                    $errors[] = "Failed to update tag: $tagNumber at Row No $rownumber";
                }


                $rownumber++;
                $chunkCount++;

                // ================= CHUNK COMMIT ================= 
                if ($chunkCount >= $chunkSize) {

                    if (!empty($errors)) {
                        $transaction->rollBack();
                        fclose($handle);
                        return $this->asJson([
                            'success' => false,
                            'updated' => 0,
                            'errors'  => $errors,
                            'message' => 'Bulk update rolled back due to validation errors.'
                        ]);
                    }

                    $transaction->commit();
                    $transaction = Yii::$app->db->beginTransaction();
                    $chunkCount = 0;
                }
            }

            fclose($handle);

            if (!empty($errors)) {
                $transaction->rollBack();
                return $this->asJson([
                    'success' => false,
                    'updated' => 0,
                    'errors'  => $errors,
                    'message' => 'Bulk update rolled back due to validation errors.'
                ]);
            }

            $transaction->commit();

            return $this->asJson([
                'success' => true,
                'updated' => $updated,
                'errors'  => []
            ]);

        } catch (\Exception $e) {
            $transaction->rollBack();
            return $this->asJson([
                'success' => false,
                'message' => 'Error occurred: ' . $e->getMessage(),
                'errors'  => []
            ]);
        }
    }*/

        public function actionBulkupdateinventorystatus()
        {
            ini_set('memory_limit', '2048M');
            set_time_limit(0);

            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $TabId = $this->TabId;
            $file  = UploadedFile::getInstanceByName('csvfile');

            if (!$file) {
                return ['success' => false, 'message' => 'File missing'];
            }

            $handle = fopen($file->tempName, 'r');
            fgetcsv($handle); // skip header

            $updated     = 0;
            $errors      = [];
            $rownumber   = 2;

            $modifiedtime = date("Y-m-d H:i:s");
            $modifiedBy   = Yii::$app->user->id;

            /** Chunk config */
            $chunkSize  = 500;
            $chunkCount = 0;

            /** Status maps */
            $statusRows = (new \yii\db\Query())
                ->select(['status_id', 'status_value'])
                ->from('inv_status')
                ->all();

            $statusIdToValue = [];
            $statusValueToId = [];

            foreach ($statusRows as $row) {
                $statusIdToValue[$row['status_id']] = strtolower($row['status_value']);
                $statusValueToId[strtolower($row['status_value'])] = $row['status_id'];
            }

            $transaction = Yii::$app->db->beginTransaction();

            try {

                while (($row = fgetcsv($handle)) !== false) {

                    /** ---------- BASIC VALIDATION ---------- */
                    if (empty($row[0])) {
                        $errors[] = "Empty tag number at Row No $rownumber";
                        $rownumber++; continue;
                    }

                    if ($TabId == 33 && empty($row[1])) {
                        $errors[] = "Empty status at Row No $rownumber";
                        $rownumber++; continue;
                    }

                    $tagNumber = strtolower(trim($row[0]));

                    /** ---------- INVENTORY ---------- */
                    $inventory = Inventory::find()
                        ->select(['inventory_id', 'status'])
                        ->where('LOWER(tag_number) = :tag', [':tag' => $tagNumber])
                        ->one();

                    if (!$inventory) {
                        $errors[] = "Tag not found: $tagNumber (Row No $rownumber)";
                        $rownumber++; continue;
                    }

                    $oldStatus = $statusIdToValue[$inventory->status] ?? '';

                    /** ---------- TAB 33 : INVENTORY STATUS ---------- */
                    if ($TabId == 33) {

                        $newStatus = $statusValueToId[strtolower(trim($row[1]))] ?? null;

                        if (!$newStatus) {
                            $errors[] = "Invalid status '{$row[1]}' at Row No $rownumber";
                            $rownumber++; continue;
                        }

                        if ($inventory->status == $newStatus) {
                            $errors[] = "Old status and new status are same for tag: $tagNumber (Row No $rownumber)";
                            $rownumber++; continue;
                        }

                        if (in_array($inventory->status, [1, 2, 5])) {
                            $errors[] = "Status cannot be changed for tag: $tagNumber because current status is $oldStatus (Row No $rownumber)";
                            $rownumber++; continue;
                        }

                        $log = new InventoryLogDetails();
                        $log->inventory_id = $inventory->inventory_id;

                        /** 3 → 4 only */
                        if ($inventory->status == 3 && $newStatus != 4) {
                            $errors[] = "Only 'Cleaning Require' allowed for $tagNumber (Row No $rownumber)";
                            $rownumber++; continue;
                        }

                        /** 4 → 5 only */
                        if ($inventory->status == 4 && $newStatus != 5) {
                            $errors[] = "Only 'IQC Require' allowed for $tagNumber (Row No $rownumber)";
                            $rownumber++; continue;
                        }

                        /** Update inventory */
                        Yii::$app->db->createCommand()->update(
                            'inventory',
                            [
                                'status'       => $newStatus,
                                'modifiedtime' => $modifiedtime,
                                'modifiedby'   => $modifiedBy,
                            ],
                            ['inventory_id' => $inventory->inventory_id]
                        )->execute();

                        /** Logs */
                        $log->inventorystatus_updatedby = $modifiedBy;
                        $log->inventorystatus_updated_at = $modifiedtime;

                        if ($inventory->status == 3) {
                            $log->sticker_removal_updatedby = $modifiedBy;
                            $log->sticker_removal_updated_at = $modifiedtime;
                        }

                        if ($inventory->status == 4) {
                            $log->cleaning_updatedby = $modifiedBy;
                            $log->cleaning_updated_at = $modifiedtime;
                        }

                        $log->save(false);
                        $updated++;
                    }

                    /** ---------- TAB 69 : STICKER → CLEANING ---------- */
                    elseif ($TabId == 69) {

                        if ($inventory->status != 3) {
                            $errors[] = "Stage Error: current status of Tag No '$tagNumber' is '$oldStatus' at Row No $rownumber";
                            $rownumber++; continue;
                        }

                        Yii::$app->db->createCommand()->update(
                            'inventory',
                            [
                                'status'       => 4,
                                'modifiedtime' => $modifiedtime,
                                'modifiedby'   => $modifiedBy,
                            ],
                            ['inventory_id' => $inventory->inventory_id]
                        )->execute();

                        $log = new InventoryLogDetails();
                        $log->inventory_id = $inventory->inventory_id;
                        $log->sticker_removal_updatedby = $modifiedBy;
                        $log->sticker_removal_updated_at = $modifiedtime;
                        $log->save(false);

                        $updated++;
                    }

                    /** ---------- TAB 70 : CLEANING → IQC ---------- */
                    elseif ($TabId == 70) {

                        if ($inventory->status != 4) {
                            $errors[] ="Stage Error: current status of Tag No '$tagNumber' is '$oldStatus' at Row No $rownumber";
                            $rownumber++; continue;
                        }

                        Yii::$app->db->createCommand()->update(
                            'inventory',
                            [
                                'status'       => 5,
                                'modifiedtime' => $modifiedtime,
                                'modifiedby'   => $modifiedBy,
                            ],
                            ['inventory_id' => $inventory->inventory_id]
                        )->execute();

                        $log = new InventoryLogDetails();
                        $log->inventory_id = $inventory->inventory_id;
                        $log->cleaning_updatedby = $modifiedBy;
                        $log->cleaning_updated_at = $modifiedtime;
                        $log->save(false);

                        $updated++;
                    }

                    $rownumber++;
                    $chunkCount++;

                    /** ---------- CHUNK COMMIT ---------- */
                    if ($chunkCount >= $chunkSize) {
                        $transaction->commit();
                        $transaction = Yii::$app->db->beginTransaction();
                        $chunkCount = 0;
                    }
                }

                fclose($handle);
                $transaction->commit();

                return [
                    'success' => true,
                    'updated' => $updated,
                    'errors'  => $errors,
                    'totalrows' => ($rownumber - 2) , //total no of records
                ];

            } catch (\Throwable $e) {

                $transaction->rollBack();

                return [
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors'  => []
                ];
            }
        }



    //code added by ptpatel on date 09-12-2025
    public function actionGetinventorydata()
    {
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $inventory_id = Yii::$app->request->post('recordid');
        $from         = Yii::$app->request->post('from');
        $columnname   = Yii::$app->request->post('columnname');

        if (!$inventory_id) {
            return ['status' => 'fail', 'message' => 'No Inventory found.'];
        }

        // Fetch inventory
        $inv = (new \yii\db\Query())
            ->select('*')
            ->from('inventory')
            ->where(['inventory_id' => $inventory_id])
            ->one();

        if (!$inv) {
            return ['status' => 'fail', 'message' => 'Data not found for given ID'];
        }

        // Fetch current status text
        $currentStatus = (new \yii\db\Query())
            ->select('status_value')
            ->from('inv_status')
            ->where(['status_id' => $inv['status']])
            ->scalar();

        // Fetch all statuses
        $statusList = (new \yii\db\Query())
            ->select(['status_id', 'status_value'])
            ->from('inv_status')
            ->all();

        // ---- APPLY RULES (same as bulk update) ---- //
        $currentStatusId = $inv['status'];
        $allowedStatusIds = [];

        if ($currentStatusId == 1 || $currentStatusId == 2 || $currentStatusId == 5) {
            // No change allowed for status = 1 inventory
            // No change Allowed for status =2 tagging pending becuse before this item doent have tag number
            // No change allowed for status = 5 IQC Require
            $allowedStatusIds = [];
        }
        elseif ($currentStatusId == 3) { //sticker removal
            // Sticker removal → only Cleaning Require (ID = 4)
            $allowedStatusIds = [4];
        }
        elseif ($currentStatusId == 4) {
            // Cleaning require → only IQC Require (ID = 5)
            $allowedStatusIds = [5];
        }
        else {
            // Default: all statuses allowed
            $allowedStatusIds = [];
        }

        // ---- BUILD DROPDOWN OPTIONS ---- //
        $statusOptions = "<option value=''>Select New Status</option>";

        foreach ($statusList as $s) {

            // Skip not allowed status
            if (!in_array($s['status_id'], $allowedStatusIds)) {
                continue;
            }

            // Select current status by default
            $selected = ($s['status_id'] == $currentStatusId) ? "selected" : "";
            $statusOptions .= "<option value='{$s['status_id']}' $selected>{$s['status_value']}</option>";
        }

        // ---- BUILD HTML ---- //
        $html = "
            <form id='updateStatusForm'>

                <input type='hidden' name='recordid' value='{$inv['inventory_id']}'>
                <input type='hidden' name='columnname' value='{$columnname}'>
                <input type='hidden' name='from_page' value='{$from}'>
                <input type='hidden' name='singleedit' value='singleedit'>
                <input type='hidden' name='mode' value='edit'>
                <input type='hidden' name='module' value='inventory'>
                <input type='hidden' name='tablename' value='{$TableName}'>

                <div class='form-group mb-2'>
                    <label>Tag Number</label>
                    <input type='text' class='form-control' value='{$inv['tag_number']}' readonly>
                </div>

                <div class='form-group mb-2'>
                    <label>Current Status</label>
                    <input type='text' class='form-control' value='{$currentStatus}' readonly>
                </div>

                <div class='form-group mb-2'>
                    <label>New Status</label>
                    <select class='form-control singleselect DD~M' name='inventory[status]'>
                        $statusOptions
                    </select>
                    <div class='help-block'></div>
                </div>

            </form>
        ";

        return [
            'status' => 'success',
            'html'   => $html
        ];
    }

    //code addee by ptpatel on date 12-12-2025
    public function actionDownloadBinsCsv()
    {
        // Fetch all bin numbers from DB
        $bins = (new \yii\db\Query())
            ->select(['bin_number_value'])
            ->from('tag_bin_number')   
            ->orderBy(['bin_number_id' => SORT_ASC])
            ->all();

        // Start CSV content
        $csv = "bin_number\n";

        foreach ($bins as $b) {
            $csv .= $b['bin_number_value'] . "\n";
        }

        // Output CSV for download
        return Yii::$app->response->sendContentAsFile(
            $csv,
            'all_bins.csv',
            [
                'mimeType' => 'text/csv',
                'inline' => false
            ]
        );
    }

    public static function cleanInput(array $data): array
    {
        return array_map(function ($value) {
            if (is_array($value)) {
                return self::cleanInput($value);
            }
            return is_string($value) ? trim($value) : $value;
        }, $data);
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

    protected function checkEditPermissionByDetailSetting(int $tabId, string $moduleName, int $recordId): void
    {
        $user_id   = Yii::$app->user->id;
        $user_role = Yii::$app->session->get('active_profile_id') ?? null;
        if($user_id == 1){
            return;
        }
        $detail = Detaileditsetting::find()
            ->where([
                'tabid'       => $tabId,
                'module_name' => $moduleName,
            ])
            ->one();

        if (!$detail) {
            return; 
        }

        if ((int)$detail->edit_allow !== 1) {
            throw new ForbiddenHttpException('You are not allowed to edit this record.');
        }

        $stageField = trim((string)$detail->stage_field);
        $stageValue = $detail->stage_value;

        if ($stageField !== '' && strcasecmp($stageField, 'NA') !== 0) {
            $record = (new \yii\db\Query())
                ->from($this->TableName)        
                ->where([$this->FieldId => $recordId])
                ->one();

            if (!$record) {
                throw new ForbiddenHttpException('Record not found.');
            }

            $recordStageValue = $record[$stageField] ?? null;

            if ($stageValue !== null && $stageValue !== '' &&
                (string)$recordStageValue !== (string)$stageValue) {
                throw new ForbiddenHttpException('You are not allowed to edit this record.');
            }
        }

        $isSuperAdmin = Yii::$app->user->identity->is_super_admin ?? false;
        $isAdmin      = Yii::$app->user->identity->is_admin ?? false;


        // super admin
        if ($isSuperAdmin && (int)$detail->superadmin_allow === 1) {
            return;
        }

        // admin
        if ($isAdmin && (int)$detail->admin_allow === 1) {
            return;
        }
            // user
        $userIds = json_decode($detail->user_id ?? '[]', true) ?: [];
        if (is_array($userIds) && in_array($user_id, $userIds)) {
            return;
        }
            //role
        $roles = json_decode($detail->user_role ?? '[]', true) ?: [];
        if (is_array($roles) && !empty($user_role) && in_array($user_role, $roles)) {
            return;
        }

        throw new ForbiddenHttpException('You are not allowed to edit this record.');
    }

    public function actionCheckduplicate($fieldName, $value, $ignoreId = null)
    {
        // Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $FieldId = $this->FieldId;
        $TableName = $this->TableName;
        $TabId = $this->TabId;
        // echo $TabId;die;
        if(in_array($TabId,["96","97","20"])){
            $TableName = 'vendor_account';
            $FieldId = 'vendoraccid';
        }
        $query = (new Query())
            ->from($TableName)
             ->where(['LOWER(REPLACE(' . $fieldName . ', " ", ""))' => strtolower(str_replace(' ', '', $value))]);

        if (!empty($ignoreId)) {
            $query->andWhere(['!=', $FieldId, $ignoreId]);
        }

        // return ['exists' => $query->exists()];
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ['exists' => $query->exists()];
        }

        // Normal backend/manual check
        return $query->exists();
    }
    public function updateSingleField()
    {

        $FieldId = $this->FieldId;
        $TableName = $this->TableName;
        $ModuleName = $this->ModuleName;
        $TableName = $this->TableName;
        $TabLabel = $this->TabLabel;
        // Get field details
        
        $postData = Yii::$app->request->post();
        $Recordid = $postData['recordid'];
        // echo "<pre>";print_r($postData); echo $FieldId;die;
        $fieldData = (new \yii\db\Query())
            ->select(['columnname', 'fieldlabel', 'isunique'])
            ->from('field')
            ->where(['fieldid' => $postData['fieldid']])
            ->one();

        if (!$fieldData) {
            throw new \Exception("Invalid field.");
        }

        $fieldName  = $fieldData['columnname'];

        $datavalue = is_array($postData[$TableName][$fieldName]) ? 
                    implode(",",$postData[$TableName][$fieldName]) : 
                    (trim($postData[$TableName][$fieldName] ?? ''));
        $rowsAffected = 0;
        if ($datavalue !== '' && $datavalue !== null){
            $data = $postData[$TableName] ?? [];

            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    if (is_array($value)) {
                        $data[$key] = implode(',', $value);
                    } else {
                        $data[$key] = trim($value);
                    }
                }
            }
            // echo "<pre>";print_r($postData);die;
            // Update only single column
            $oldAttribute = (new \yii\db\Query())
                ->select($fieldName)
                ->from($TableName)
                ->where([$FieldId => $Recordid])
                ->one();
            $rowsAffected = Yii::$app->db->createCommand()
                ->update(
                    $TableName,
                    [$fieldName => $datavalue],
                    [$FieldId => $Recordid]
                )
                ->execute();
            if($rowsAffected > 0){
                //add log
                $modlog = new ModtrackerBasic();
                $auditstatus = 10;
                $mode = $_POST["mode"];
                $module = $_POST["module"];
                $customtablename = $module . "cf";
                $CS = array();
                if (isset($_POST[$customtablename]))
                    $CS = $_POST[$customtablename];
                else
                    $CS = '';
                $modlog->auditlog($oldAttribute, $data, $ModuleName, $Recordid, $auditstatus, Yii::$app->user->id);
             }
        }
        if($rowsAffected > 0)
            return true;
        else 
            return false;
    }
}
