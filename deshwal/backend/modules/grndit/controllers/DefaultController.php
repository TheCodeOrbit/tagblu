<?php

namespace backend\modules\grndit\controllers;

use common\controllers\ModuleController;
use common\components\TcpdfHelper;
use DateTime;
use backend\models\AccessCheck;
use app\models\GrnDit;
use app\models\EditModel;
use app\models\ModtrackerBasic;
use backend\modules\quotes\controllers\MyPDF;
use yii\base\Exception;

use Yii;
/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'multiple';
    public $ModuleName = 'grndit';
    public $FieldId = 'grndit_id';
    public $TableName = 'grn_dit';
    public $TabLabel = 'DevIT GRN';


    public $TabId = '79';
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

    public function actionGetshipaddress()
    {
        $data = $_POST;
        $purchaseorder_dit_id = Yii::$app->request->post('deal_name');
        $connection = Yii::$app->db;



        $command = $connection->createCommand("
                        SELECT purchase_order_dit.vendor_name,acc_name,purchase_order_dit.location,purchase_order_dit.address as vendor_address,purchase_order_dit.gst_number,purchase_order_dit.state_code,purchase_order_dit.source_of_supply, delivery_entitiy_name,warehouse_name,warehouse.address,warehouse.state,warehouse.statecode,warehouse.pincode,warehouse.gstn     FROM purchase_order_dit 
                        left join warehouse on purchase_order_dit.delivery_entitiy_name =warehouse.warehouse_id
                        left join vendor_account on purchase_order_dit.vendor_name =vendor_account.vendoraccid WHERE purchaseorder_dit_id = :purchaseorder_dit_id
                    ")->bindValue(":purchaseorder_dit_id", $purchaseorder_dit_id);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No location found.',
                'data' => ''
            ]);
        }

    }

    public function actionGetbilladdress()
    {
        $data = $_POST;
        $purchaseorder_dit_id = Yii::$app->request->post('deal_name');
        $connection = Yii::$app->db;



        $command = $connection->createCommand("
                        SELECT bill_entitiy_name as delivery_entitiy_name,warehouse_name,warehouse.address,warehouse.state,warehouse.statecode,warehouse.pincode,warehouse.gstn     FROM purchase_order_dit join warehouse on purchase_order_dit.bill_entitiy_name =warehouse.warehouse_id WHERE purchaseorder_dit_id = :purchaseorder_dit_id
                    ")->bindValue(":purchaseorder_dit_id", $purchaseorder_dit_id);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No location found.',
                'data' => ''
            ]);
        }

    }


    public function actionGetproductdetail()
    {
        $data = $_POST;
        $purchase_order_number = Yii::$app->request->post('purchase_order_number');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                        SELECT purchaseorderdit_product_details.product_name as product_id,pd.product_name,purchaseorderdit_product_details.product_description,purchaseorderdit_product_details.hsn_code,purchaseorderdit_product_details.qty,purchaseorderdit_product_details.basic_cost_price,purchaseorderdit_product_details.cgst,purchaseorderdit_product_details.sgst,purchaseorderdit_product_details.igst,purchaseorderdit_product_details.product_total
                        FROM purchaseorderdit_product_details 
                        join `product_dit` pd on pd.productdit_id = purchaseorderdit_product_details.product_name
                          WHERE purchaseorder_dit_id = :purchase_order_number
                    ")->bindValue(":purchase_order_number", $purchase_order_number);
        $columns = $command->queryAll();
        // print_r($columns);die;
        if (!empty($columns)) {
            $data = array();
            $i = 0;
            foreach ($columns as $key => $row) {
                $data[$i]['product_id'] = $row['product_id'];
                $data[$i]['product_name'] = $row['product_name'];
                $data[$i]['product_description'] = $row['product_description'];
                $data[$i]['hsn_code'] = $row['hsn_code'];
                $data[$i]['qty'] = $row['qty'];
                $data[$i]['basic_cost_price'] = $row['basic_cost_price'];
                $data[$i]['cgst'] = $row['cgst'];
                $data[$i]['sgst'] = $row['sgst'];
                $data[$i]['igst'] = $row['igst'];
                $data[$i]['product_total'] = $row['product_total'];
                //get total receive qty from grn
                $command = $connection->createCommand("Select if(sum(grndit_product_details.received_qty) is null, 0,sum(grndit_product_details.received_qty)) as already_received from grndit_product_details join grn_dit on grn_dit.grndit_id = grndit_product_details.grndit_id where purchase_order_number = :purchase_order_number and grndit_product_details.product_name = :product_name")->bindValue(":purchase_order_number", $purchase_order_number)->bindValue(":product_name", $row['product_id']);
                $val = $command->queryOne();
                $data[$i]['already_received'] = $val['already_received'];


                $i++;
            }

            return $this->asJson([
                'status' => 'success',
                'data' => $data,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Product info found.',
                'data' => ''
            ]);
        }
    }
    public function actionImportdata()
    {
        try {
            $active_transaction = false;
            $Recordid = filter_var(Yii::$app->request->post('Recordid'), FILTER_SANITIZE_NUMBER_INT);
            $blockid = filter_var(Yii::$app->request->post('blockid'), FILTER_SANITIZE_NUMBER_INT);
            $Exceldata = Yii::$app->request->post('excel_data');

            if (empty($Recordid)) {
                throw new Exception("Grn Information is required");
            }
            if (empty($blockid)) {
                throw new Exception("Blockid is required");
            }
            if (empty($Exceldata) || !is_array($Exceldata)) {
                throw new Exception("Excel data is invalid or missing");
            }

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
            $modulepermission = $model->modulepermission($profile, $tabs);
            $editpermission = $model->checkpermission($id, $ModuleName, "edit");
            $importpermission = $model->checkpermission($id, $ModuleName, 'import');

            if (empty($importpermission) || empty($editpermission)) {
                throw new Exception("You do not have permission to import or edit this module");
            }

            $actionid = "detail";
            $model = new EditModel($TableName, $FieldId, $ModuleName, $actionid);
            $model->_members[$FieldId] = $Recordid;
            list($Column, $Record) = $model->getFieldDetail($rolebasedrecord);

            $fields = [];
            $blocks_table = "";
            $blocks = $Column->blocks ?? [];

            foreach ($blocks as $block) {
                if ($block->blockid == $blockid && isset($block->detailfields)) {
                    foreach ($block->detailfields as $field) {
                        $attributes = $field->getAttributes();
                        $fields[] = [
                            'columnname' => $attributes['columnname'] ?? null,
                            'fieldlabel' => $attributes['fieldlabel'] ?? null,
                        ];
                        if (empty($blocks_table)) {
                            $blocks_table = $attributes['tablename'] ?? null;
                        }
                    }
                }
            }

            if (empty($blocks_table)) {
                throw new Exception("Unable to determine the target module/table for update");
            }

            $fieldLabels = [];
            foreach ($fields as $f) {
                $fieldLabels[$f['columnname']] = $f['fieldlabel'];
            }

            $updatableFields = ['product_name', 'hsn_code', 'bar_code'];
            $identifierColumn = 'product_name';
            $requiredColumns = array_merge($updatableFields, [$identifierColumn]);

            $columnLabelMap = [];
            foreach ($requiredColumns as $col) {
                if (!isset($fieldLabels[$col])) {
                    throw new Exception("Missing label for column: $col");
                }
                $columnLabelMap[$col] = $fieldLabels[$col];
            }

            $excelHeader = $Exceldata[0];
            $columnIndexes = [];
            foreach ($columnLabelMap as $col => $label) {
                $index = array_search($label, $excelHeader);
                if ($index === false) {
                    throw new Exception("Excel column '$label' not found.");
                }
                $columnIndexes[$col] = $index;
            }

            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            $active_transaction = true;

            // === Validate product-wise quantity ===
            $excelProductCount = [];
            $productNameMap = [];

            for ($i = 1; $i < count($Exceldata); $i++) {
                $row = $Exceldata[$i];
                $productRaw = trim($row[$columnIndexes['product_name']] ?? '');

                if ($productRaw === '')
                    continue;

                $productId = $this->get_product_id($connection, $productRaw);
                if (empty($productId))
                    continue;

                if (!isset($excelProductCount[$productId])) {
                    $excelProductCount[$productId] = 0;
                    $productNameMap[$productId] = $productRaw;
                }
                $excelProductCount[$productId]++;
            }

            $existingProductCount = Yii::$app->db->createCommand("
            SELECT product_name, COUNT(*) as qty
            FROM grndit_barcodes
            WHERE grndit_id = :grndit_id
            GROUP BY product_name
        ")->bindValue(':grndit_id', $Recordid)->queryAll();

            $dbProductCount = [];
            foreach ($existingProductCount as $row) {
                $dbProductCount[$row['product_name']] = $row['qty'];
            }

            foreach ($excelProductCount as $productId => $excelCount) {
                $dbCount = $dbProductCount[$productId] ?? 0;
                if ($excelCount !== $dbCount) {
                    $productLabel = $productNameMap[$productId] ?? "Product ID $productId";
                    throw new Exception("Mismatch for $productLabel: Excel has $excelCount rows, system has $dbCount. Please verify.");
                }
            }

            // === Step 1: Check for duplicate bar_codes in Excel ===
            $excelBarCodes = [];
            $duplicateBarCodesInExcel = [];

            for ($i = 1; $i < count($Exceldata); $i++) {
                $row = $Exceldata[$i];
                $barCode = trim($row[$columnIndexes['bar_code']] ?? '');

                if ($barCode === '')
                    continue;

                if (isset($excelBarCodes[$barCode])) {
                    $duplicateBarCodesInExcel[] = "Row $i: Duplicate Serial No '$barCode' found in Excel.";
                } else {
                    $excelBarCodes[$barCode] = $i;
                }
            }

            if (!empty($duplicateBarCodesInExcel)) {
                throw new Exception("Duplicate Serial No in Excel:\n" . implode("\n", $duplicateBarCodesInExcel));
            }

            // === Step 2: Check for duplicate bar_codes in DB (global) ===
            $allBarCodes = array_filter(array_keys($excelBarCodes), fn($b) => $b !== '');
            if (!empty($allBarCodes)) {
                $placeholders = implode(',', array_fill(0, count($allBarCodes), '?'));

                $command = Yii::$app->db->createCommand("
        SELECT bar_code FROM {$blocks_table}
        WHERE bar_code IN ($placeholders)
    ");
                foreach ($allBarCodes as $index => $value) {
                    $command->bindValue($index + 1, $value); // 1-based indexing
                }

                $existingBarCodes = $command->queryColumn();

                if (!empty($existingBarCodes)) {
                    $conflicts = implode(", ", $existingBarCodes);
                    throw new Exception("The following Serial No(s) already exist in the system: $conflicts. All Serial No must be globally unique.");
                }
            }


            // === Delete old records for this GRN ===
            $connection->createCommand("DELETE FROM $blocks_table WHERE grndit_id = :grndit_id")
                ->bindValue(":grndit_id", $Recordid)
                ->execute();

            // === Insert new records ===
            $updatedRows = 0;

            for ($i = 1; $i < count($Exceldata); $i++) {
                $row = $Exceldata[$i];
                $updateData = [];

                foreach ($updatableFields as $field) {
                    $lbl = $columnLabelMap[$field];
                    $value = trim($row[$columnIndexes[$field]] ?? '');

                    if ($value === '') {
                        throw new Exception("Row $i: '$lbl' cannot be empty.");
                    }

                    if ($field === "product_name") {
                        $value_db = $this->get_product_id($connection, $value);
                        if (empty($value_db)) {
                            throw new Exception("Row $i: '$lbl' is not a valid Product Name.");
                        }
                        $value = $value_db;
                        $identifierValue = $value;
                    }

                    $updateData[$field] = $value;
                }

                $insertData = $updateData;
                $insertData[$identifierColumn] = $identifierValue;
                $insertData['grndit_id'] = $Recordid;

                $connection->createCommand()
                    ->insert($blocks_table, $insertData)
                    ->execute();


                $updatedRows++;
            }
            $modlog = new ModtrackerBasic();
            $auditstatus = 6;//serail no uploaded
            $modlog->auditlog('', '', $this->TabId, $Recordid, $auditstatus, Yii::$app->user->id);

            // Add to inventory
            $modelgrn = new GrnDit();
            $modelgrn->Savetoinventory($Recordid);

            $transaction->commit();
            $active_transaction = false;

            return $this->asJson([
                'status' => 'success',
                'data' => []
            ]);
        } catch (Exception $e) {
            if ($active_transaction) {
                $transaction->rollBack();
            }
            return $this->asJson([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => $e->getMessage(),
                'data' => ''
            ]);
        } catch (Error $e) {
            if ($active_transaction) {
                $transaction->rollBack();
            }
            return $this->asJson([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => $e->getMessage(),
                'data' => ''
            ]);
        }
    }


    public function get_product_id($connection, $product_name)
    {
        if (empty($product_name))
            return "";
        $query = $connection->createCommand("SELECT productdit_id FROM product_dit WHERE product_name = :value and deleted=0")->bindValues([":value" => $product_name]);
        $data = $query->queryOne();
        if (empty($data))
            return "";
        return $data["productdit_id"] ?? "";
    }





}
