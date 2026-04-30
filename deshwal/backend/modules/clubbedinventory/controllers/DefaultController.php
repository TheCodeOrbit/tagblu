<?php

namespace backend\modules\clubbedinventory\controllers;

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
    public $ModuleName = 'clubbedinventory';
    public $FieldId = 'id';
    public $TableName = 'rep_clubbed_inventory';
    public $TabLabel = 'Clubbed Inventory';
    public $TabId = '80';

    public function actionExample()
    {
        return $this->render('index');
    }
    public function actionFilteroptioncolumn()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            ['value' => 'prod_category_value', 'label' => 'Category'],
            ['value' => 'sub_catagory_value', 'label' => 'Subcategory'],
            ['value' => 'uom_value', 'label' => 'UOM'],
            ['value' => 'qty', 'label' => 'Quantity'],
            ['value' => 'purchase_value', 'label' => 'Purchase Value'],
            ['value' => 'location_code_value', 'label' => 'Location Code'],
            ['value' => 'location_floor_value', 'label' => 'Location Floor'],
        ];
    }

    public function actionFiltercolumnoperator()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            ['value' => 'contains', 'label' => 'Contains'],            
            ['value' => 'not_contains', 'label' => 'Doesn\'t Contains'],
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

        $sortCol = Yii::$app->request->get('sort_column');
        $sortDir = Yii::$app->request->get('sort_direction', 'asc');

        $page = Yii::$app->request->get('page', 1);
        $pageSize = 1000;
        $offset = ($page - 1) * $pageSize;

        $query = (new \yii\db\Query())
            ->select([
                "$this->TableName.*",
                'prod_category.prod_category_value',
                'prod_sub_catagory.sub_catagory_value',
                'prod_uom.uom_value',
                'seg_location_code.location_code_value',
                'seg_location_floor.location_floor_value',
            ])
            ->from('' . $this->TableName . '')
            ->leftJoin('prod_sub_catagory', 'prod_sub_catagory.sub_catagory_id = ' . $this->TableName . '.subcategory')
            ->leftJoin('prod_category', 'prod_category.prod_category_id = ' . $this->TableName . '.category')
            ->leftJoin('prod_uom', 'prod_uom.uom_id = ' . $this->TableName . '.uom')
            ->innerJoin('seg_location_code', '' . $this->TableName . '.location_code = seg_location_code.location_code_id')
            ->innerJoin('seg_location_floor', '' . $this->TableName . '.location_floor = seg_location_floor.location_floor_id');
        // ->groupBy('' . $this->TableName . '.subcategory');

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
                    $havingConditions[] = ['like', '%'. $filterColumn, $filterValue , false];
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

        $totalCount = $query->count(); // total records (required for ag-Grid)

        $rows = $query
            ->offset($offset)
            ->limit($pageSize)
            ->all();

        return [
            'rows' => $rows,
            'total' => $totalCount,
        ];
    }
}
