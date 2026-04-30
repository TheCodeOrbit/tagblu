<?php

namespace backend\modules\userloginhistory\controllers;

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
    public $layout = 'single';
    public $ModuleName = 'userloginhistory';
    public $FieldId = 'id';
    public $TableName = 'user_activity_log';
    public $TabLabel = 'User Login History';
    public $TabId = '95';

    public function actionExample()
    {
        return $this->render('index');
    }
    public function actionFilteroptioncolumn()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            ['value' => 'user_id', 'label' => 'User'],
            ['value' => 'activity', 'label' => 'Activity'],
            ['value' => 'ip_address', 'label' => 'IP Address'],
            ['value' => 'user_agent', 'label' => 'Agent'],
            ['value' => 'created_at', 'label' => 'Time'],
        ];
    }

    public function actionFiltercolumnoperator()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            ['value' => 'contains', 'label' => 'Contains'],
            ['value' => 'not_contains', 'label' => `Doesn't Contains`],
            ['value' => 'equals', 'label' => 'Equals'],
            ['value' => 'not_equals', 'label' => 'Not Equals'],
            ['value' => 'starts_with', 'label' => 'Starts With'],
            // ['value' => 'ends_with', 'label' => 'Ends With'],
        ];
    }

    public function actionReportdata()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $search = Yii::$app->request->get('search', '');

        $filterColumn = Yii::$app->request->get('filter_column');
        $filterOperator = Yii::$app->request->get('filter_operator');
        $filterValue = Yii::$app->request->get('filter_value');

        $fromDate = Yii::$app->request->get('from_date');
        $toDate   = Yii::$app->request->get('to_date');
        $user     = Yii::$app->request->get('user');
        $activity = Yii::$app->request->get('activity');


        $sortCol = Yii::$app->request->get('sort_column');
        $sortDir = Yii::$app->request->get('sort_direction', 'asc');

        $page = Yii::$app->request->get('page', 1);
        $pageSize = 10;
        $offset = ($page - 1) * $pageSize;

        $query = (new \yii\db\Query())
            ->select([
                "$this->TableName.*",
                "CONCAT(user.first_name, ' ', user.last_name) AS full_name",
            ])
            ->from('' . $this->TableName . '')
            ->leftJoin('user', 'user.id = ' . $this->TableName . '.user_id');
        // ->groupBy('' . $this->TableName . '.subcategory');

        
        if ($fromDate) {
            $query->andWhere(['>=', "{$this->TableName}.created_at", date("Y-m-d 00:00:00", strtotime($fromDate))]);
        }
        if ($toDate) {
            $query->andWhere(['<=', "{$this->TableName}.created_at", date("Y-m-d 23:59:59", strtotime($toDate))]);
        }

        // User filter
        if ($user) {
            $query->andWhere(["{$this->TableName}.user_id" => $user]);
        }

        // Activity filter
        if ($activity) {
            $query->andWhere(["{$this->TableName}.activity" => $activity]);
        }

        $havingConditions = [];

        if (!empty($search)) {
            $havingConditions[] = [
                'or',
                ['like', 'prod_category_value', $search],
                ['like', 'sub_catagory_value', $search],
                ['like', 'location_code_value', $search],
                ['like', 'location_floor_value', $search],
                ['like', new Expression('CAST(qty AS CHAR)'), $search],
                ['like', new Expression('CAST(purchase_value AS CHAR)'), $search],
            ];
        }

        if (!empty($filterColumn) && !empty($filterValue)) {
            switch ($filterOperator) {
                case 'equals':
                    $havingConditions[] = [$filterColumn => $filterValue];
                    break;
                case 'not_equals':
                    $havingConditions[] = ['!=', $filterColumn, $filterValue];
                    break;
                case 'starts_with':
                    $havingConditions[] = ['like', $filterColumn, $filterValue . '%', false];
                    break;
                case 'ends_with':
                    $havingConditions[] = ['like', '%' . $filterColumn, $filterValue, false];
                    break;
                case 'not_contains':
                    $havingConditions[] = ['not like', $filterColumn, '%' . $filterValue . '%', false];
                    break;
                case 'contains':
                default:
                    $havingConditions[] = ['like', $filterColumn, $filterValue];
                    break;
            }
        }

        if (!empty($havingConditions)) {
            $query->having(['and', ...$havingConditions]);
        }
        // Sorting
        if (!empty($sortCol)) {
            $query->orderBy([$sortCol => strtolower($sortDir) === 'asc' ? SORT_ASC : SORT_DESC]);
        }

        $countQuery  = clone $query;
        $totalCount  = $countQuery->count();
        // $totalCount = $query->count(); // total records (required for ag-Grid)

        $rows = $query
            ->offset($offset)
            ->limit($pageSize)
            ->all();

        return [
            'rows' => $rows,
            'total' => $totalCount,
        ];
    }

    public function actionFilteruseractivity()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        //  Filters
        $fromDate = Yii::$app->request->post('userlogin_from_date');
        $toDate   = Yii::$app->request->post('userlogin_to_date');
        $user     = Yii::$app->request->post('user');
        $activity = Yii::$app->request->post('activity');

        //  Sorting & Pagination
        $sortCol  = Yii::$app->request->post('sort_column');
        $sortDir  = Yii::$app->request->post('sort_direction', 'asc');

        // AG Grid sends `startRow` & `endRow` for infinite row model
        $startRow = Yii::$app->request->post('startRow', 0);
        $endRow   = Yii::$app->request->post('endRow', 100);
        $pageSize = $endRow - $startRow;
        $offset   = $startRow;

        $query = (new \yii\db\Query())
            ->select([
                'u.username',
                'ua.activity',
                'ua.ip_address',
                'ua.created_at'
            ])
            ->from('user_activity_log ua')
            ->leftJoin('user u', 'u.id = ua.user_id');

        
        if ($fromDate) {
            $query->andWhere(['>=', 'ua.created_at', date("Y-m-d 00:00:00", strtotime($fromDate))]);
        }
        if ($toDate) {
            $query->andWhere(['<=', 'ua.created_at', date("Y-m-d 23:59:59", strtotime($toDate))]);
        }

        //  User filter
        if ($user) {
            $query->andWhere(['ua.user_id' => $user]);
        }

        //  Activity filter
        if ($activity) {
            $query->andWhere(['ua.activity' => $activity]);
        }

        // Sorting
        if (!empty($sortCol)) {
            $query->orderBy([$sortCol => strtolower($sortDir) === 'asc' ? SORT_ASC : SORT_DESC]);
        } else {
            $query->orderBy(['ua.created_at' => SORT_DESC]);
        }

        // Total count (⚡ important for AG Grid pagination)
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
