<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "purchase_order_dit".
 *
 * @property int $purchaseorder_dit_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property int $deleted
 * @property int $purchaseorder_dit_no
 * @property string $purchase_order_date
 * @property string $po_Issued_entity_name
 * @property int $reference_number
 * @property string $po_type
 * @property string $stage
 * @property string $delivery_instruction
 * @property string $terms_condition
 * @property string $credit_terms
 * @property string $po_expiry_date
 * @property int $send_for_approval
 * @property string|null $first_approval_comment
 * @property string|null $second_approval_comment
 * @property string $vendor_name
 * @property string $location
 * @property string $address
 * @property string $gst_number
 * @property string $state_code
 * @property string $source_of_supply
 * @property string $bill_entitiy_name
 * @property string $bill_location
 * @property string $bill_address
 * @property string $bill_gst_number
 * @property string $bill_state_code
 * @property string $destination_of_supply
 * @property string $delivery_entitiy_name
 * @property string $delivery_location
 * @property string $delivery_address
 * @property string $delivery_gst_number
 * @property string $delivery_state_code
 * @property string $delivery_destination_of_supply
 * @property float|null $sub_total
 * @property float|null $cgst_amount
 * @property float|null $sgst_amount
 * @property float|null $igst_amount
 * @property float|null $total
 *
 * @property PurchaseorderditProductDetails[] $purchaseorderditProductDetails
 */
class PurchaseOrderDit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'purchase_order_dit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'deleted', 'send_for_approval'], 'integer'],//'reference_number', 
            [['createdtime', 'modifiedtime','po_approve_date','purchase_order_date', 'po_expiry_date','estimate_time_delivery', ], 'safe'],
            [['terms_condition', 'delivery_instruction'], 'string'],
            [['sub_total', 'cgst_amount', 'sgst_amount', 'igst_amount', 'stage', 'total'], 'number'],
            [['po_Issued_entity_name', 'po_type', 'credit_terms', 'delivery_destination_of_supply','purchaseorder_dit_no','reference_number',], 'string', 'max' => 200],//added 'reference_number', by ptpatel when multiple so selected
            [['first_approval_comment', 'second_approval_comment'], 'string', 'max' => 1000],
            [['vendor_name', 'location', 'gst_number', 'state_code', 'source_of_supply', 'bill_entitiy_name', 'bill_location', 'bill_gst_number', 'bill_state_code', 'destination_of_supply', 'delivery_entitiy_name', 'delivery_location', 'delivery_gst_number', 'delivery_state_code'], 'string', 'max' => 100],
            [['address', 'bill_address', 'delivery_address'], 'string', 'max' => 3000],
            // added for handling blank values saving in by ptpatel on date 24-01-2026
            [['vendor_name'], 'trim'],
            [['vendor_name'], 'required', 'message' => 'Vendor Name cannot be blank.'],
            [['vendor_name'], 'integer', 'message' => 'Vendor Name must be a number.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'purchaseorder_dit_id' => 'Purchaseorder Dit ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'deleted' => 'Deleted',
            'purchaseorder_dit_no' => 'Purchaseorder Dit No',
            'purchase_order_date' => 'Purchase Order Date',
            'po_Issued_entity_name' => 'Po Issued Entity Name',
            'reference_number' => 'Reference Number',
            'po_type' => 'Po Type',
            'stage' => 'Stage',
            'delivery_instruction' => 'Delivery Instruction',
            'terms_condition' => 'Terms Condition',
            'credit_terms' => 'Credit Terms',
            'po_expiry_date' => 'Po Expiry Date',
            'estimate_time_delivery' =>'Estimate Time Delivery',
            'send_for_approval' => 'Send For Approval',
            'first_approval_comment' => 'First Apporval Comment',
            'second_approval_comment' => 'Second Approval Comment',
            'vendor_name' => 'Vendor Name',
            'location' => 'Location',
            'address' => 'Address',
            'gst_number' => 'Gst Number',
            'state_code' => 'State Code',
            'source_of_supply' => 'Source Of Supply',
            'bill_entitiy_name' => 'Bill Entitiy Name',
            'bill_location' => 'Bill Location',
            'bill_address' => 'Bill Address',
            'bill_gst_number' => 'Bill Gst Number',
            'bill_state_code' => 'Bill State Code',
            'destination_of_supply' => 'Destination Of Supply',
            'delivery_entitiy_name' => 'Delivery Entitiy Name',
            'delivery_location' => 'Delivery Location',
            'delivery_address' => 'Delivery Address',
            'delivery_gst_number' => 'Delivery Gst Number',
            'delivery_state_code' => 'Delivery State Code',
            'delivery_destination_of_supply' => 'Delivery Destination Of Supply',
            'sub_total' => 'Sub Total',
            'cgst_amount' => 'Cgst Amount',
            'sgst_amount' => 'Sgst Amount',
            'igst_amount' => 'Igst Amount',
            'total' => 'Total',
        ];
    }

    /**
     * Gets query for [[PurchaseorderditProductDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPurchaseorderditProductDetails()
    {
        return $this->hasMany(PurchaseorderditProductDetails::class, ['purchaseorder_dit_id' => 'purchaseorder_dit_id']);
    }

   /* public function completedSO($Recordid)
    {
        $connection = Yii::$app->db;
        //get sO from this po
         $sql = "SELECT reference_number
                        FROM purchase_order_dit 
                        Where purchaseorder_dit_id = :purchaseorder_dit_id";

        $command = $connection->createCommand($sql)->bindValue(":purchaseorder_dit_id", $Recordid);
        $columns = $command->queryOne();
        $salesorder_id = $columns['reference_number'];
        //check if po for all the so generated
         $sql = "SELECT opd.*, 
               product_dit.product_name AS prod_name,
               product_dit.product_description AS prod_description,
               product_dit.oem_part_number AS prod_oem_part_number
        FROM salesorderdit_product_details opd 
        JOIN salesorder_dit ON salesorder_dit.salesorder_dit_id = opd.salesorder_dit_id
        JOIN product_dit ON product_dit.productdit_id = opd.product_name
        WHERE opd.salesorder_dit_id = :so_name";

        $command = $connection->createCommand($sql)->bindValue(":so_name", $salesorder_id);
        $columns = $command->queryAll();
        $cnt = 0;
        // Loop by reference to allow modification/removal
        foreach ($columns as $key => &$rows) {
            $product_name = $rows['product_name'];
            $so_qty = (float) $rows['qty']; // Make sure it's treated as a number
            $remaining_qty = $so_qty;

            // Check if PO is created for this SO and product
            $sql_chk = "SELECT sum(qty) as qty
                        FROM purchase_order_dit po
                        LEFT JOIN purchaseorderdit_product_details ppd ON ppd.purchaseorder_dit_id = po.purchaseorder_dit_id
                        WHERE po.reference_number = :reference_number  AND ppd.product_name = :product_name";

            $cmd = $connection->createCommand($sql_chk)
                ->bindValue(":reference_number", $salesorder_id)
                ->bindValue(":product_name", $product_name);

            $chkcolumns = $cmd->queryOne();
            // print_r($chkcolumns);
            if ($chkcolumns) {          
                $ordered_qty = (float) $chkcolumns['qty'];                
                $remaining_qty = $so_qty - $ordered_qty;
                //echo $remaining_qty.' '.$so_qty.' '.$ordered_qty.' '.$chkcolumns['qty'];
            }
            //echo "<br>".$remaining_qty;die;

            if ($remaining_qty > 0) {
                $cnt = 0;

                break;
            } else {
               $cnt++;
            }
        }
        //echo $cnt;die;
        //insert into po_completed_so_dit table
        if($cnt > 0)//po generated for all the products
        {
            //check if record already exist
            $sql_chk = "SELECT count(*) as cntso
                        FROM po_completed_so_dit 
                        WHERE salesorder_id = :reference_number ";

            $cmd = $connection->createCommand($sql_chk)
                ->bindValue(":reference_number", $salesorder_id);
            $chkso = $cmd->queryOne();
            if(!$chkso['cntso'])
            {
                //insert into table
                $sql = "INSERT INTO `po_completed_so_dit`( `salesorder_id`, `completed_date`) VALUES (:salesorder_id,now())";
                 $cmd = $connection->createCommand($sql)
                ->bindValue(":salesorder_id", $salesorder_id)->execute();
            }

        }
        else{
            //delete if already exist 
            $sql = "DELETE FROM `po_completed_so_dit` WHERE  `salesorder_id`=:salesorder_id";
                 $cmd = $connection->createCommand($sql)
                ->bindValue(":salesorder_id", $salesorder_id)->execute();
        }

    }*/

    public function completedSO($Recordid)
    {
        $connection = Yii::$app->db;

        //Get SO reference_number(s) from PO
        $sql = "SELECT reference_number
                FROM purchase_order_dit 
                WHERE purchaseorder_dit_id = :purchaseorder_dit_id";
        $command = $connection->createCommand($sql)
            ->bindValue(":purchaseorder_dit_id", $Recordid);
        $columns = $command->queryOne();

        if (!$columns || empty($columns['reference_number'])) {
            return;
        }

        //  Split reference_number into array (handle commas)
        $so_list = array_filter(array_map('trim', explode(',', $columns['reference_number'])));

        foreach ($so_list as $salesorder_id) {
            if (empty($salesorder_id)) continue;

            //  Fetch SO product details
            $sql = "SELECT opd.*, 
                        product_dit.product_name AS prod_name,
                        product_dit.product_description AS prod_description,
                        product_dit.oem_part_number AS prod_oem_part_number
                    FROM salesorderdit_product_details opd 
                    JOIN salesorder_dit ON salesorder_dit.salesorder_dit_id = opd.salesorder_dit_id
                    JOIN product_dit ON product_dit.productdit_id = opd.product_name
                    WHERE opd.salesorder_dit_id = :so_id";

            $command = $connection->createCommand($sql)
                ->bindValue(":so_id", $salesorder_id);
            $columns = $command->queryAll();

            if (empty($columns)) {
                continue;
            }

            $allProductsCompleted = true;

            foreach ($columns as $rows) {
                $product_name = $rows['product_name'];
                $so_qty = (float) $rows['qty'];

                // Build dynamic FIND_IN_SET conditions for all SOs in this PO
                $findSetConditions = [];
                $params = [];
                foreach ($so_list as $i => $num) {
                    $param = ":ref{$i}";
                    $findSetConditions[] = "FIND_IN_SET($param, REPLACE(po.reference_number, ' ', ''))";
                    $params[$param] = $num;
                }

                $where = implode(' OR ', $findSetConditions);

                //  Compute total ordered quantity from all related POs
                $sql_chk = "
                    SELECT SUM(ppd.qty) AS qty
                    FROM purchase_order_dit po
                    LEFT JOIN purchaseorderdit_product_details ppd 
                        ON ppd.purchaseorder_dit_id = po.purchaseorder_dit_id
                    WHERE ($where)
                    AND ppd.product_name = :product_name
                ";

                $params[':product_name'] = $product_name;

                $cmd = $connection->createCommand($sql_chk)->bindValues($params);
                $chkcolumns = $cmd->queryOne();

                $ordered_qty = (float) ($chkcolumns['qty'] ?? 0);
                $remaining_qty = $so_qty - $ordered_qty;

                if ($remaining_qty > 0) {
                    $allProductsCompleted = false;
                    break; // some products not yet covered by PO
                }
            }

            //  Update po_completed_so_dit table
            if ($allProductsCompleted) {
                // insert if not exists
                $sql_chk = "SELECT COUNT(*) AS cntso
                            FROM po_completed_so_dit 
                            WHERE salesorder_id = :salesorder_id";

                $cmd = $connection->createCommand($sql_chk)
                    ->bindValue(":salesorder_id", $salesorder_id);
                $chkso = $cmd->queryOne();

                if (empty($chkso['cntso'])) {
                    $sql_insert = "INSERT INTO po_completed_so_dit (salesorder_id, completed_date)
                                VALUES (:salesorder_id, NOW())";
                    $connection->createCommand($sql_insert)
                        ->bindValue(":salesorder_id", $salesorder_id)
                        ->execute();
                }
            } else {
                // delete if exists (not completed anymore)
                $sql_del = "DELETE FROM po_completed_so_dit 
                            WHERE salesorder_id = :salesorder_id";
                $connection->createCommand($sql_del)
                    ->bindValue(":salesorder_id", $salesorder_id)
                    ->execute();
            }
        }
    }

}
