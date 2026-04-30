<?php

namespace backend\modules\sourcingdealproductdetail\controllers;

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
    public $layout = 'single';
    public $ModuleName = 'sourcingdealproductdetail';
    public $FieldId = 'product_costing_id '; //this is sourcingdeal id becuse this use all over
    public $TableName = 'product_costing';
    public $TabLabel = 'Sourcing Deal Product Detail';
    public $TabId = '104';

    
    public $parentfieldid = 'product_costing_id ';
    public $parentModulename = 'productdetail';
    public $parentTabId = '31';

    public function actionExample()
    {
        return $this->render('index');
    }
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

    public function actionFiltercolumnoperator()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            ['value' => 'contains', 'label' => 'Contains'],            
            ['value' => 'not_contains', 'label' => `not Contains`],
            ['value' => 'equals', 'label' => 'Equals'],            
            ['value' => 'not_equals', 'label' => 'Not Equals'],
            ['value' => 'starts_with', 'label' => 'Starts With'],
            // ['value' => 'ends_with', 'label' => 'Ends With'],
        ];
    }

    public function actionReportdata()
    {

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


    $query = (new Query())
        ->select([
            'sourcingdeal.sourcingdeal_no AS Sourcing_Deal_No',
            'sourcingdeal.deal_name AS Sourcing_Deal_Name',
            'va.acc_name AS Account_Name',
            'va.cust_code AS Account_Code',
            'product_costing.product_costing_no AS Product_Costing_No',
            'product_costing.direct_expenses_service_expens AS Spare_Cost',
            'product_costing.marketing_expenses AS Repair_Cost',
            'product_costing.total_quoted_amt_inclusive_gst AS Total_Quoted_Amount_Inclusive_GST',
            'product_costing.total_quoted_amt_exclusive_gst AS Total_Quoted_Amount_Exclusive_GST',
            'product_costing.total_sp_amount_inclusive_gst AS Total_SP_Amount_Inclusive_GST',
            'product_costing.total_sp_amount_exclusive_gst AS Total_SP_Amount_Exclusive_GST',
            'product_costing_detail.total_logistics_cost AS Total_Logistics_Cost',
            'product_costing.total_expence_cost AS Total_Expense_Cost',
            'product_costing.margin AS Margin',
            'product_costing.margin_percentage AS Margin_Percentage',
            'product_costing.round_off AS Round_Off',
            'product_costing.tcs_percentage AS TCS_Percentage',
            'product_costing.tcs_amount AS TCS_Amount',
            'product_costing.final_quoted_amount_incl_gst AS Final_Quoted_Amount_Inclusive_GST',
            "CONCAT(u1.first_name,' ',u1.last_name) AS Owner",
            "CONCAT(u2.first_name,' ',u2.last_name) AS Created_By",
            "CONCAT(u3.first_name,' ',u3.last_name) AS Modified_By",
            'product_costing.createdtime AS Created_Datetime',
            'product_costing.modifiedtime AS Modified_Datetime',
            'products.product_name AS Product',
            'product_costing_detail.category AS Category',
            'product_costing_detail.subcategory AS Sub_Category',
            'product_costing_detail.vendor1 AS Vendor1',
            'product_costing_detail.vendor1_pricing AS Vendor1_Pricing',
            'product_costing_detail.vendor2 AS Vendor2',
            'product_costing_detail.vendor2_pricing AS Vendor2_Pricing',
            'product_costing_detail.make AS Make',
            'product_costing_detail.model_no AS Model',
            'vl3.vendor_loc_name AS Pickup_location',
            'vl1.vendor_loc_name AS Billing_From_Location',
            'vl2.vendor_loc_name AS Shipping_From_Location',
            'w1.warehouse_name AS Bill_To_Warehouse',
            'w2.warehouse_name AS Ship_To_Warehouse',
            'ac.assetcondition_value AS Asset_Condition',
            'pdac.all_accessories_value AS All_Accessories',
            'product_costing_detail.hsn_code AS HSN_Code',
            'product_costing_detail.calculated_sp AS Suggested_SP',
            'product_costing_detail.sp_inclusive_gst AS SP_Inclusive_GST',
            'product_costing_detail.sp_exclusive_gst AS SP_Exclusive_GST',
            'product_costing_detail.quoted_price_inclusive_gst AS Quoted_price_Inclusive_GST',
            'product_costing_detail.quoted_price_gst_exclude AS Quoted_price_GST_Exclude',
            'product_costing_detail.margin AS Detail_Margin',
            'product_costing_detail.margin_percentage AS Detail_Margin_Percentage',
            'product_costing_detail.quantity_required AS Quantity_Required',
            'product_costing_detail.uom AS UOM',
            "IF(product_costing_detail.no_gst IS NOT NULL, IF(product_costing_detail.no_gst=0,'No','Yes'),'') AS No_GST",
            'product_costing_detail.cgst AS CGST',
            'product_costing_detail.sgst AS SGST',
            'product_costing_detail.igst AS IGST',
            'product_costing_detail.cgst_amount AS CGST_Amount',
            'product_costing_detail.sgst_amount AS SGST_Amount',
            'product_costing_detail.igst_amount AS IGST_Amount',
            'product_costing_detail.total_sp_inclusive_gst AS Total_SP_Inclusive_GST',
            'product_costing_detail.total_sp_exclusive_gst AS Total_SP_Exclusive_GST',
            'product_costing_detail.total_quoted_price_inclusive_gst AS Total_Quoted_Price_Inclusive_GST',
            'product_costing_detail.total_quoted_price_exclusive_gst AS Total_Quoted_Price_Exclusive_GST',
        ])
        ->from('product_costing')
        ->innerJoin('product_costing_detail', 'product_costing_detail.product_costing_id = product_costing.product_costing_id')
        ->innerJoin('sourcingdeal', 'sourcingdeal.sourcingdeal_id = product_costing.related_to_id')
        ->innerJoin('products', 'products.products_id = product_costing_detail.productid')
        ->leftJoin('warehouse w1', 'w1.warehouse_id = product_costing_detail.bill_to_warehouse')
        ->leftJoin('warehouse w2', 'w2.warehouse_id = product_costing_detail.ship_to_warehouse')
        ->leftJoin('vendor_locations vl1', 'vl1.vendorloc_id = product_costing_detail.billing_from_location')
        ->leftJoin('vendor_locations vl2', 'vl2.vendorloc_id = product_costing_detail.shipping_from_location')
        ->leftJoin('vendor_locations vl3', 'vl3.vendorloc_id = product_costing_detail.pickup_location')
        ->leftJoin('po_asset_condition ac', 'ac.assetconditionid = product_costing_detail.asset_condition')
        ->leftJoin('prod_detail_all_accessories pdac', 'pdac.all_accessories_id = product_costing_detail.all_accessories')
        ->leftJoin('vendor_account va', 'va.vendoraccid = product_costing.vendor_account_name')
        ->leftJoin('user u1', 'u1.id = product_costing.ownerid')
        ->leftJoin('user u2', 'u2.id = product_costing.creatorid')
        ->leftJoin('user u3', 'u3.id = product_costing.modifiedby')
        ->where([
            'product_costing.deleted' => 0,
        ])
        ->andWhere(['<', 'DATE(product_costing.createdtime)', new \yii\db\Expression(':today')])
        ->addParams([':today' => date('Y-m-d')])
        ->orderBy(['product_costing.product_costing_id' => SORT_DESC]);


        
        if ($fromDate) {
            $query->andWhere(['>=', 'product_costing.createdtime', date("Y-m-d 00:00:00", strtotime($fromDate))]);
        }
        if ($toDate) {
            $query->andWhere(['<=', 'product_costing.createdtime', date("Y-m-d 23:59:59", strtotime($toDate))]);
        }
        if ($accname) {
            $query->andWhere(['=', 'product_costing.vendor_account_name', $accname]);
        }
        

        
        // Sorting
        if (!empty($sortCol)) {
            $query->orderBy([$sortCol => strtolower($sortDir) === 'asc' ? SORT_ASC : SORT_DESC]);
        } else {
            $query->orderBy(['createdtime' => SORT_DESC]);
        }

        // Total count (important for AG Grid pagination)
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
