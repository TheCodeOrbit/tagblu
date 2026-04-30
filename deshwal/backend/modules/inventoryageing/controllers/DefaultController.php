<?php

namespace backend\modules\inventoryageing\controllers;

use common\controllers\ModuleController;
use Yii;
use yii\base\Exception;
use yii\db\Expression;
use yii\db\Query;
use yii\web\Response;

/**
 * Default controller for the `grn` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'single';
    public $ModuleName = 'inventoryageing';
    public $FieldId = 'inventory_ageing_id';
    public $TableName = 'rep_inventory_ageing';
    public $TabLabel = 'Inventory Ageing';
    public $TabId = '77';

    public function actionExample()
    {
        return $this->render('index');
    }

    public function actionGetgrndata()
    {
        $Recordid = Yii::$app->request->post('Recordid');
        $connection = Yii::$app->db;
        $command = $connection->createCommand("
                    SELECT 
                        grn.*, 
                        grn_asset_detail.*,
                        pickup.pickup_no,pickup.pickup_id
                    FROM grn
                    INNER JOIN grn_asset_detail ON grn.grn_id = grn_asset_detail.grn_id
                    INNER JOIN pickup ON grn.pickup_id = pickup.pickup_id
                    WHERE grn_asset_detail.grn_asset_detail_id = :Recordid
                ")->bindValue(":Recordid", $Recordid);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Contact found.',
                'data' => ''
            ]);
        }
    }

    public function actionGetproductdetails()
    {
        $Recordid = Yii::$app->request->post('Recordid');
        $connection = Yii::$app->db;


        // AND pc.product_category_id = p.category
        $command = $connection->createCommand("
                SELECT 
                        p.*, 
                        ps.sub_catagory_value ,
                        pm.prod_model_value, 
                        m.prod_make_value,
                        pc.prod_category_value
                    FROM products p
                    LEFT JOIN prod_sub_catagory ps ON p.subcategory = ps.sub_catagory_id
                    LEFT JOIN prod_model pm ON p.model = pm.prod_model_id 
                    LEFT JOIN prod_make m ON p.make = m.prod_make_id
                    LEFT JOIN prod_category pc ON p.category = pc.prod_category_id
                    WHERE p.products_id = :Recordid;

            ")->bindValue(":Recordid", $Recordid);

        // if (isset($_POST['product_group_id'])) {
        //     $productGroupId = intval($_POST['product_group_id']); // Sanitize input
        //     $db = Yii::$app->db;

        //     // Fetch categories based on product_group_id
        //     $query = "SELECT prod_catagory_id AS id, prod_catagory_value AS name FROM prod_catagory WHERE FIND_IN_SET(:product_group_id, prod_group_id) AND is_active = 1 ORDER BY seq_no ASC";
        //     $command = $db->createCommand($query);
        //     $command->bindValue(':product_group_id', $productGroupId);
        //     $categories = $command->queryAll();

        //     // Return categories in JSON format
        //     return ['status' => 'success', 'categories' => $categories];
        // } else {
        //     return ['status' => 'error', 'message' => 'Product Group ID is required.'];
        // }
        $columns = $command->queryOne();

        // $query = "
        //         SELECT sub_catagory_id AS id, sub_catagory_value AS name 
        //         FROM prod_sub_catagory 
        //         WHERE FIND_IN_SET(:category_id, prod_catagory_id) AND is_active = 1";
        //     $command = $connection->createCommand($query);
        //     $command->bindValue(':category_id', $columns['category']);
        //     $subcategories = $command->queryAll();
        //     $columns['subcategories'] = $subcategories;
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Contact found.',
                'data' => ''
            ]);
        }
    }

    public function actionGetinventoryageingdetails()
    {
        $subcategory = Yii::$app->request->get('subcategory');
        //  print_r($subcategory);die;

        $result = (new \yii\db\Query())
            ->select([
                // 'rep_inventory_ageing.grn_date',
                'DATE_FORMAT(rep_inventory_ageing.grn_date, "%d-%m-%Y") AS grn_date',
                'rep_inventory_ageing.lot_no',
                'vendor_account.account_name',
                'rep_inventory_ageing.product_name',
                'rep_inventory_ageing.inventory_ageing_id',
                'rep_inventory_ageing.qty',
                'prod_sub_catagory.sub_catagory_value',

                new Expression("CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 0 AND 15 THEN rep_inventory_ageing.amount ELSE 0 END AS day_0_15"),
                new Expression("CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 16 AND 30 THEN rep_inventory_ageing.amount ELSE 0 END AS day_16_30"),
                new Expression("CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 31 AND 60 THEN rep_inventory_ageing.amount ELSE 0 END AS day_31_60"),
                new Expression("CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 61 AND 90 THEN rep_inventory_ageing.amount ELSE 0 END AS day_61_90"),
                new Expression("CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 91 AND 180 THEN rep_inventory_ageing.amount ELSE 0 END AS day_91_180"),
                new Expression("CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) > 180 THEN rep_inventory_ageing.amount ELSE 0 END AS day_180_plus"),

                // Optional: total value
                new Expression("rep_inventory_ageing.amount AS total_value"),
            ])
            ->from('rep_inventory_ageing')
            ->leftJoin('prod_sub_catagory', 'prod_sub_catagory.sub_catagory_id = rep_inventory_ageing.subcategory')
            ->innerJoin('vendor_account', 'rep_inventory_ageing.account_name = vendor_account.vendoraccid')
            ->where(['rep_inventory_ageing.subcategory' => $subcategory])
            ->all();

        if (!empty($result)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $result,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Contact found.',
                'data' => ''
            ]);
        }
    }

    public function actionGetinventoryageing()
    {
        $subcategory = Yii::$app->request->get('subcategory');
        //  print_r($subcategory);die;


        $result = (new Query())
            ->select([
                'prod_sub_catagory.sub_catagory_value',
                'SUM(rep_inventory_ageing.qty) AS qty',
                'SUM(rep_inventory_ageing.amount) AS total_value',
                'prod_uom.uom_value',
                'rep_inventory_ageing.subcategory',
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

        if (!empty($result)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $result,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Contact found.',
                'data' => ''
            ]);
        }
    }

    public function actionFilteroptioncolumn()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            ['value' => 'sub_catagory_value', 'label' => 'Subcategory'],
            ['value' => 'uom_value', 'label' => 'UOM'],            
            ['value' => 'amt_0_15', 'label' => '0-15 Days'],
            ['value' => 'amt_16_30', 'label' => '16-30 Days'],
            ['value' => 'amt_31_60', 'label' => '31-60 Days'],
            ['value' => 'amt_61_90', 'label' => '61-90 Days'],
            ['value' => 'amt_91_180', 'label' => '91-180 Days'],
            ['value' => 'amt_180_plus', 'label' => '>180 Days'],
            ['value' => 'total_value', 'label' => 'Total Value'],
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
                "$this->TableName.subcategory",
                'prod_sub_catagory.sub_catagory_value',
                'SUM(' . $this->TableName . '.qty) AS qty',
                'SUM(' . $this->TableName . '.amount) AS total_value',
                'prod_uom.uom_value',
                'SUM(CASE WHEN DATEDIFF(CURDATE(), ' . $this->TableName . '.grn_date) BETWEEN 0 AND 15 THEN ' . $this->TableName . '.amount ELSE 0 END) AS amt_0_15',
                'SUM(CASE WHEN DATEDIFF(CURDATE(), ' . $this->TableName . '.grn_date) BETWEEN 16 AND 30 THEN ' . $this->TableName . '.amount ELSE 0 END) AS amt_16_30',
                'SUM(CASE WHEN DATEDIFF(CURDATE(), ' . $this->TableName . '.grn_date) BETWEEN 31 AND 60 THEN ' . $this->TableName . '.amount ELSE 0 END) AS amt_31_60',
                'SUM(CASE WHEN DATEDIFF(CURDATE(), ' . $this->TableName . '.grn_date) BETWEEN 61 AND 90 THEN ' . $this->TableName . '.amount ELSE 0 END) AS amt_61_90',
                'SUM(CASE WHEN DATEDIFF(CURDATE(), ' . $this->TableName . '.grn_date) BETWEEN 91 AND 180 THEN ' . $this->TableName . '.amount ELSE 0 END) AS amt_91_180',
                'SUM(CASE WHEN DATEDIFF(CURDATE(), ' . $this->TableName . '.grn_date) > 180 THEN ' . $this->TableName . '.amount ELSE 0 END) AS amt_180_plus',
            ])
            ->from('' . $this->TableName . '')
            ->leftJoin('prod_sub_catagory', 'prod_sub_catagory.sub_catagory_id = ' . $this->TableName . '.subcategory')
            ->leftJoin('prod_uom', 'prod_uom.uom_id = ' . $this->TableName . '.uom')
            ->innerJoin('vendor_account', '' . $this->TableName . '.account_name = vendor_account.vendoraccid')
            ->groupBy('' . $this->TableName . '.subcategory');


        $havingConditions = [];

        if (!empty($search)) {
            $havingConditions[] = [
                'or',
                ['like', new Expression('CAST(SUM(qty) AS CHAR)'), $search],
                ['like', new Expression('CAST(SUM(amount) AS CHAR)'), $search],
                ['like', new Expression('CAST(SUM(CASE WHEN DATEDIFF(CURDATE(), grn_date) BETWEEN 0 AND 15 THEN amount ELSE 0 END) AS CHAR)'), $search],
                ['like', new Expression('CAST(SUM(CASE WHEN DATEDIFF(CURDATE(), grn_date) BETWEEN 16 AND 30 THEN amount ELSE 0 END) AS CHAR)'), $search],
                ['like', new Expression('CAST(SUM(CASE WHEN DATEDIFF(CURDATE(), grn_date) BETWEEN 31 AND 60 THEN amount ELSE 0 END) AS CHAR)'), $search],
                ['like', new Expression('CAST(SUM(CASE WHEN DATEDIFF(CURDATE(), grn_date) BETWEEN 61 AND 90 THEN amount ELSE 0 END) AS CHAR)'), $search],
                ['like', new Expression('CAST(SUM(CASE WHEN DATEDIFF(CURDATE(), grn_date) BETWEEN 91 AND 180 THEN amount ELSE 0 END) AS CHAR)'), $search],
                ['like', new Expression('CAST(SUM(CASE WHEN DATEDIFF(CURDATE(), grn_date) > 180 THEN amount ELSE 0 END) AS CHAR)'), $search],
                ['like', 'sub_catagory_value', $search],
                ['like', 'uom_value', $search],
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
        // echo $query->createCommand()->getRawSql();die;
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

    public function actionReportdetailview()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $subcatgory_id = (int) Yii::$app->request->get('subcategory');
        $ModuleName = $this->ModuleName;
        $TabId = $this->TabId;
        $TabLabel = $this->TabLabel;

        $result = (new \yii\db\Query())
            ->select([
                'DATE_FORMAT(rep_inventory_ageing.grn_date, "%d-%m-%Y") AS grn_date',
                'rep_inventory_ageing.lot_no',
                'vendor_account.acc_name as account_name',
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
            ->where(['rep_inventory_ageing.subcategory' => $subcatgory_id])
            ->all();

        // print_r($result);die;
        return [
            'html' => $this->renderPartial('@app/views/tetra/reportdetailview', [
                'ModuleName' => $ModuleName,
                'TabId' => $TabId,
                'TabLabel' => $TabLabel,
                'subcategory' => $subcatgory_id,
                'subcategory_value' => $result[0]['sub_catagory_value']
            ]),
            'gridData' => $result
        ];
    }
}
