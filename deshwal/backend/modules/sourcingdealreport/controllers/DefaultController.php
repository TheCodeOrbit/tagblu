<?php

namespace backend\modules\sourcingdealreport\controllers;

use app\models\ListModel;
use backend\models\AccessCheck;
use common\controllers\ModuleController;
use Yii;
use yii\base\Exception;
use yii\db\Expression;
use yii\db\Query;

/**
 * Default controller for the `grn` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    //for dynamica column and records fetch 
    // public $layout = 'single';
    // public $ModuleName = 'sourcingdealreport';
    // public $FieldId = 'sourcingdeal_id'; //this is sourcingdeal id becuse this use all over
    // public $TableName = 'sourcingdeal';
    // public $TabLabel = 'Sourcing Deal';
    // public $TabId = '103';

    
    // public $parentfieldid = 'sourcingdeal_id ';
    // public $parentModulename = 'sourcingdeal';
    // public $parentTabId = '51';

    // as per email report 
    // 
     public $layout = 'single';
    public $ModuleName = 'sourcingdealreport';
    public $FieldId = 'sourcingdeal_id'; //this is sourcingdeal id becuse this use all over
    public $TableName = 'rep_sourcingdeal';
    public $TabLabel = 'Sourcing Deal';
    public $TabId = '103';

    public function actionExample()
    {
        return $this->render('index');
    }
    //this function is not used because only those field to show which are in mailing report
     public function actionGetcolumnfieldsforreport()
    {
        
        $TableName = $this->TableName;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $ModuleName = $this->parentModulename;
        $TabId = $this->parentTabId;

        // Get all columns for the 'leaddetails' table
        $columns = (new \yii\db\Query())
            ->select(['columnname AS field', 'fieldlabel AS headerName', 'fieldid'])
            ->from('field')
            ->where(['tabid' => $TabId])
            ->all();

        $id = Yii::$app->user->id;
        $accmodel = new AccessCheck();
        $tabs = $accmodel->tabs($id, $ModuleName);
        $profile = $accmodel->profile($id, $tabs, $ModuleName);
        $modelaccess = $accmodel->moduleaccess($id, $profile, $tabs);
        $rolebasedrecord = $accmodel->rolebasedrecord($id, $profile);
        $hasadminpower = $accmodel->hasadminpower($profile);
        //below code is not used for now
        foreach ($columns as &$cols) {
           
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
            // $cols['visible_permission'] = $visible;
            // $cols['readonly_permission'] = $readonly;
            // $cols['userid'] = $id;
        }
        //code end added by ptpatel
        return $columns;
    }

    public function actionFilteroptioncolumn()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

       $fields = (new \yii\db\Query())
        ->select(['value' => 'fieldname', 'label' => 'fieldlabel'])
        ->from('field')
        ->where(['tabid' => $this->parentTabId])
        ->andWhere(['not in', 'uitype', [2, 3, 53]]) // 2 = hidden, 3 = password, 53 = hidden userid
        ->orderBy(['fieldid' => SORT_ASC])
        ->all();
        // echo "<pre>";print_r($fields);die;

        return $fields;
    }

    /*public function actionReportdata()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $search = Yii::$app->request->get('search', '');

        $fromDate = Yii::$app->request->get('from_date');
        $toDate   = Yii::$app->request->get('to_date');
        $account     = Yii::$app->request->get('accname');

         
       //  Sorting & Pagination
        $sortCol  = Yii::$app->request->post('sort_column');
        $sortDir  = Yii::$app->request->post('sort_direction', 'asc');

        // AG Grid sends `startRow` & `endRow` for infinite row model
        $startRow = Yii::$app->request->post('startRow', 0);
        $endRow   = Yii::$app->request->post('endRow', 100);
        $pageSize = $endRow - $startRow;
        $offset   = $startRow;

        $filters = [
            'from_date' => $fromDate,
            'to_date'   => $toDate,
            'vendor_account_name'   => $account,
            'sort_column' => $sortCol,
            'sort_direction' => $sortDir,
            'limit' => 10,
            'offset' => $offset,
        ];

        $TabId = $this->parentTabId;
        $FieldId = $this->parentfieldid;
        $ModuleName = $this->parentModulename;
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
        //as per disccusion module wise permission is allowed means if this report module allow in profile then user can see all recods of report
        $modulepermission = $model->modulepermission($profile, $tabs);
        $modulepermission['shareid']= '';

        // Read selectedRowIds from the request
        $selectedIds = Yii::$app->request->post('selectedRowIds', []);
        // $selectedIds = array_map('strval', $selectedIds); // Normalize to strings

        $listModel = new ListModel($TableName, $FieldId, $ModuleName);
        $ActionList = $listModel->getActionList($ModuleName);
        
        list($ColumnList, $RecordList, $totalitemcount) = $listModel->getFilterReportRecord(
            $ActionList['OrderBy'] ?? '',
            $ActionList['SortOrder'] ?? '',
            [],//$rolebasedrecord,// this is not required here if report module is allow user can see all records as per discussion on date 13-11-2025
            $modulepermission,
            $filters
        );
            // echo "<pre>";print_r($RecordList);die;

        return [
            'rows' => $RecordList,
            'total' => $totalitemcount,
        ];
    }*/

    public function actionReportdata(){

         Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        //  Filters
        $fromDate = Yii::$app->request->post('from_date');
        $toDate   = Yii::$app->request->post('to_date');
        $accname     = Yii::$app->request->post('accname');

        //  Sorting & Pagination
        $sortCol  = Yii::$app->request->post('sort_column');
        $sortDir  = Yii::$app->request->post('sort_direction', 'asc');

        // AG Grid sends `startRow` & `endRow` for infinite row model
        $startRow = Yii::$app->request->post('startRow', 0);
        $endRow   = Yii::$app->request->post('endRow', 100);
        $pageSize = $endRow - $startRow;
        $offset   = $startRow;

        $query = (new \yii\db\Query())
            ->select('*')
            ->from($this->TableName);

        
        // if ($fromDate) {
        //     $query->andWhere(['>=', 'createdtime', date("Y-m-d 00:00:00", strtotime($fromDate))]);
        // }
        // if ($toDate) {
        //     $query->andWhere(['<=', 'createdtime', date("Y-m-d 23:59:59", strtotime($toDate))]);
        // }

        //  User filter
        if ($accname) {
            $query->andWhere(['acc_id' => $accname]);
        }


        // Sorting
        if (!empty($sortCol)) {
            $query->orderBy([$sortCol => strtolower($sortDir) === 'asc' ? SORT_ASC : SORT_DESC]);
        } else {
            // $query->orderBy(['createdtime' => SORT_DESC]);
        }

        $totalCount = (clone $query)->count('*', Yii::$app->db);

        // Fetch rows with pagination
        $rows = $query
            ->offset($offset)
            ->limit($pageSize)
            ->all();

        return [
            'rows'  => $rows,
            'total' => (int)$totalCount,
        ];
    }

}
