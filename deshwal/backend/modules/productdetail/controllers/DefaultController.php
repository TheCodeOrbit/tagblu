<?php

namespace backend\modules\productdetail\controllers;

use app\models\Products;
use app\models\VendorLocations;
use app\models\Warehouse;
use common\controllers\ModuleController;
use Yii;
use yii\web\Response;
/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'multiple';
    public $ModuleName = 'productdetail';
    public $FieldId = 'product_costing_id';
    public $TableName = 'product_costing';
    public $TabLabel = 'Product Detail';
    public $ChildTableName = 'product_costing_detail';
    public $ChildFieldId = 'product_costing_detail_id';


    public $TabId = '31';
    /**
     * Renders the index view for the module
     * @return string
     */
    //  public function beforeAction($action)
// {
//     $this->layout = '@app/views/layouts/main';  // Set the main layout before each action
//     return parent::beforeAction($action);
// }

    public function actionExample()
    {
        return $this->render('index');
    }


    public function actionGetproductinfo()
    {
        $data = $_POST;
        $productid = Yii::$app->request->post('productid');
        $connection = Yii::$app->db;
        if(is_string($productid)){
            $productid = str_replace('\\"', '"', trim($productid, '"'));
        }
        $command = $connection->createCommand("
                SELECT prod_model_value AS model, prod_make_value AS make, hsn_code, cost_price,
                    prod_category_value AS category, products.products_id,
                    sub_catagory_value AS subcategory, uom_value
                FROM `products`
                    LEFT JOIN prod_category ON prod_category.prod_category_id = products.category
                    LEFT JOIN prod_sub_catagory ON prod_sub_catagory.sub_catagory_id = products.subcategory
                    LEFT JOIN prod_make ON prod_make.prod_make_id = products.make
                    LEFT JOIN prod_model ON prod_model.prod_model_id = products.model
                    LEFT JOIN prod_uom ON prod_uom.uom_id = products.uom
                WHERE product_name = :productid
                OR products_id = :productid
                OR product_no = :productid
            ")->bindValue(":productid", $productid);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Product info found.',
                'data' => ''
            ]);
        }
    }
    public function actionGetvendor()
    {
        $data = $_POST;
        $related_to = Yii::$app->request->post('related_to');
        $related_to_id = Yii::$app->request->post('related_to_id');
        $connection = Yii::$app->db;
        if ($related_to == 8) {
            //fetch from opportunity
            $sql = "select vendor_account_name as vendor,acc_name as vendorname from opportunity join vendor_account on getvendor.vendoraccid = opportunity.vendor_account_name  where opportunity_id=:related_to_id";
        } else if ($related_to == 51) {
            //fetch from sourcingdeal
            $sql = "select vendor_account_name as vendor,acc_name as vendorname from sourcingdeal join vendor_account on vendor_account.vendoraccid = sourcingdeal.vendor_account_name  where sourcingdeal_id=:related_to_id";
        }

        $command = $connection->createCommand($sql)->bindValue(":related_to_id", $related_to_id);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Product info found.',
                'data' => ''
            ]);
        }
    }


    public function actionGetlocationstates()
    {
        // print_r($_POST); exit;
        Yii::$app->response->format = Response::FORMAT_JSON;
        $billLocation = Yii::$app->request->post('billLocation');
        $shipLocation = Yii::$app->request->post('shipLocation');
        $product_id = Yii::$app->request->post('product_id');

        if (!$billLocation || !$shipLocation) {
            return ['success' => false, 'message' => 'Invalid Location selection'];
        }

        $billState = (int) VendorLocations::find()->select('state')->where(['vendorloc_id' => $billLocation])->scalar();
        // $shipState = VendorLocations::find()->select('state')->where(['vendorloc_id' => $shipLocation])->scalar();
        $shipState = (int) Warehouse::find()->select('stateid')->where(['warehouse_id' => $shipLocation])->scalar();

        $product = Products::find()
            ->select(['gst_percentage'])
            ->where(['products_id' => $product_id])
            ->asArray()
            ->one();

        if ($billState && $shipState) {
            return [
                'success' => true,
                'billState' => $billState,
                'shipState' => $shipState,
                'gst_percentage' => $product['gst_percentage'],
            ];
        } else {
            return ['success' => false, 'message' => 'Location not found'];
        }
    }

    public function actionGetbaseprice()
    {
        $data = $_POST;
        $productid = Yii::$app->request->post('productid');
        $quantity_required = Yii::$app->request->post('qty_required');
        $connection = Yii::$app->db;


        $command = $connection->createCommand("
                        SELECT average from pickup_calculator
                          WHERE (:quantity_required between from_range and base) and productid = (select subcategory from products where products_id=:products_id)")
            ->bindValue(":products_id", $productid)
            ->bindValue(":quantity_required", $quantity_required);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            $average = trim($columns['average']);




            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Product info found.',
                'data' => ''
            ]);
        }
    }

    public function actionGetproductype()
    {

        $sourcingdeal_id = Yii::$app->request->get('related_to_id');
        $connection = Yii::$app->db;


        $command = $connection->createCommand("
                       Select pricing_type_value from sourcingdeal join pricing_type on pricing_type.pricing_type_id = sourcingdeal.pricing_type where sourcingdeal_id = :sourcingdeal_id
                    ")->bindValue(":sourcingdeal_id", $sourcingdeal_id);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Product info found.',
                'data' => ''
            ]);
        }
    }

    public function actionGetproductpricebook()
    {

        $subcategory_id = Yii::$app->request->post('subcategory_id');
        $sourceId = Yii::$app->request->post('sourceId');
        $conditions = Yii::$app->request->post('asset_condition');
        $connection = Yii::$app->db;

        $command1 = $connection->createCommand("
                        select billing_type,vendor_account_name,contract_id from sourcingdeal sd 
                        join vendor_account va on sd.vendor_account_name = va.vendoraccid 
                        join contracts c on c.account_name = sd.vendor_account_name
                        where sourcingdeal_id = :sourcingdeal_id
                    ")->bindValue(":sourcingdeal_id", $sourceId);
        $columns1 = $command1->queryOne();
        $billingtype = $columns1['billing_type'];
        if ($billingtype == 1) {
            $command = $connection->createCommand("
                        SELECT ppb.base_amount_taxes_excluded,sub_catagory_value
                                    FROM product_price_book ppb
                                    JOIN contracts c ON ppb.contractid = c.contract_id
                                    left join prod_sub_catagory on prod_sub_catagory.sub_catagory_id = ppb.product_name
                                    WHERE prod_sub_catagory.sub_catagory_value = :subcategory_id 
                                    AND ppb.conditions = :conditions
                                    AND ppb.contractid = :contract_id
                    ")->bindValue(":subcategory_id", $subcategory_id)
                ->bindValue(":contract_id", $columns1['contract_id'])
                ->bindValue(":conditions", $conditions);
            $columns = $command->queryOne();
            if (!empty($columns)) {
                return $this->asJson([
                    'status' => 'success',
                    'data' => $columns,
                ]);
            } else {
                return $this->asJson([
                    'status' => 'error',
                    'message' => 'No Product info found.',
                    'data' => ''
                ]);
            }
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'Account is Non RC.',
                'data' => ''
            ]);
        }


    }

    public function actionFetchlocationid()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $displayValue = Yii::$app->request->post('display_value');
        $fieldType = Yii::$app->request->post('field_type');
        $vendor_account_name = Yii::$app->request->post('vendor_account_name');
        
        if (!$displayValue || !$fieldType || !$vendor_account_name) {
            return ["status" => "error", "message" => "Invalid parameters"];
        }

        if (
            in_array($fieldType, [
                "Pickup Location",
                "Billing from location",
                "Shipping from location"
            ])
        ) {

            $row = (new \yii\db\Query())
                ->from("vendor_locations")
                ->where(["vendor_loc_no" => $displayValue])
                ->andWhere(["vendor_account" => $vendor_account_name])
                ->orWhere(["vendor_loc_name" => $displayValue])
                ->orWhere(["vendorloc_id" => $displayValue])
                ->one();

            if ($row) {
                return [
                    "status" => "success",
                    "id" => $row["vendorloc_id"],
                    "name" =>isset($row["vendor_loc_name"]) ? $row["vendor_loc_name"] : ""
                ];
            }

            return ["status" => "error", "message" => "Vendor location not found"];
        }

        if ($fieldType === "Bill to warehouse") {

            $row = (new \yii\db\Query())
                ->from("warehouse")
                ->where(["warehouse_no" => $displayValue])
                ->orWhere(["warehouse_name" => $displayValue])
                ->orWhere(["warehouse_id" => $displayValue])
                ->one();

            if ($row) {
                return [
                    "status" => "success",
                    "id" => $row["warehouse_id"],
                    "name" =>isset($row["warehouse_name"]) ? $row["warehouse_name"] : ""
                ];
            }

            return ["status" => "error", "message" => "Warehouse not found"];
        }

        return ["status" => "error", "message" => "Invalid field type"];
    }

    /*** 
     * Download sample CSV file
     */
    public function actionDownloadsample()
    {
        $filePath = \Yii::getAlias('@webroot/thememain/samples/bulk_upload_SD_PD.csv');
        $fileName = 'bulk_upload_SD_PD.csv';

        if (!file_exists($filePath)) {
            throw new \yii\web\NotFoundHttpException("Sample file not found.");
        }

        return \Yii::$app->response->sendFile($filePath, $fileName, [
            'mimeType' => 'text/csv',
            'inline' => false,
        ]);
    }
    //added by deepika on 21 nov 2025 for getting asset and accessories

    public function actionGetpicklistmap($columnname)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // STEP 1: Get field row
        $field = \app\models\Field::find()
            ->where(['columnname' => $columnname])
            ->asArray()
            ->one();

        if (!$field) {
            return ['error' => 'Field not found'];
        }

        // STEP 2: Get picklist info
        $picklist = \common\models\Picklist::find()
            ->where(['fieldid' => $field['fieldid']])
            ->asArray()
            ->one();

        if (!$picklist) {
            return ['error' => 'Picklist configuration missing'];
        }

        $targetTable = $picklist['targettable'];   // e.g. asset_condition_list
        $targetField = $picklist['targetfield'];   // e.g. condition_id
        $dispfield = $picklist['dispfield'];     // e.g. condition_name

        // STEP 3: Fetch data dynamically
        $rows = (new \yii\db\Query())
            ->select([$targetField, $dispfield])
            ->from($targetTable)
            ->all();

        // STEP 4: Convert to mapping JSON
        $map = [];
        foreach ($rows as $row) {
            $map[$row[$dispfield]] = (string) $row[$targetField];
        }

        return $map;  // Returned as JSON
    }



}
