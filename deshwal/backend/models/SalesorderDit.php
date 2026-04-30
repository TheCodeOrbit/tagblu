<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "salesorder_dit".
 *
 * @property int $salesorder_dit_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property int $deleted
 * @property string $salesorder_dit_no
 * @property int|null $deal_name
 * @property int|null $account_name
 * @property int|null $so_stage
 * @property float|null $margin_percentage
 * @property float|null $gross_profit
 * @property string|null $quote_name
 * @property string|null $requester_name_contact_name
 * @property int|null $so_type
 * @property int|null $team
 * @property int|null $delivery_location
 * @property string|null $bill_to_legal_name
 * @property string|null $address
 * @property int|null $state
 * @property int|null $city
 * @property string|null $state_code
 * @property string|null $pin_code
 * @property string|null $gst
 * @property string|null $pan
 * @property float|null $cgst_amount
 * @property float|null $sgst_amount
 * @property float|null $igst_amount
 * @property float|null $basic_amount
 * @property float|null $grand_total
 * @property string|null $amount_in_words
 * @property int|null $first_level_name
 * @property string|null $first_level_number
 * @property string|null $first_level_email
 * @property string|null $first_level_designation
 * @property string|null $second_level_name
 * @property string|null $second_level_number
 * @property string|null $second_level_email
 * @property string|null $second_level_designation
 * @property int|null $wh_first_level_name
 * @property string|null $wh_first_level_number
 * @property string|null $wh_first_level_email
 * @property string|null $wh_first_level_designation
 * @property int|null $wh_second_level_name
 * @property string|null $wh_second_level_number
 * @property string|null $wh_second_level_email
 * @property string|null $wh_second_level_designation
 * @property int|null $pro_first_level_name
 * @property string|null $pro_first_level_number
 * @property string|null $pro_first_level_email
 * @property string|null $pro_first_level_designation
 * @property int|null $pro_second_level_name
 * @property string|null $pro_second_level_number
 * @property string|null $pro_second_level_email
 * @property string|null $pro_second_level_designation
 * @property int|null $timeline_commited
 * @property string|null $timeline_commited_date
 * @property int|null $case_scattered_delivery
 * @property string|null $case_scattered_delivery_files
 * @property int|null $additional_service_offered
 * @property int|null $free_chargeable_offered_services
 * @property int|null $scope_work_installation
 * @property string|null $scope_work_installation_doc
 * @property string|null $estimate_date_delivery
 * @property string|null $actual_date_delivery
 * @property int $send_for_approval
 *
 * @property SalesorderditProductDetails[] $salesorderditProductDetails
 * @property SalesorderditShipToAddress[] $salesorderditShipToAddresses
 */
class SalesorderDit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'salesorder_dit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'deal_name_auto','modifiedby', 'createdtime', 'modifiedtime', 'salesorder_dit_no'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'deleted', 'deal_name', 'account_name', 'so_stage', 'so_type', 'team', 'delivery_location',  'first_level_name', 'wh_first_level_name', 'wh_second_level_name', 'pro_first_level_name', 'pro_second_level_name', 'timeline_commited', 'case_scattered_delivery', 'additional_service_offered', 'free_chargeable_offered_services', 'scope_work_installation', 'send_for_approval','customer_payment_terms','procurement_executive','procurement_pending','ready_to_ship'], 'integer'],
            [['createdtime', 'modifiedtime', 'so_approval_date','timeline_commited_date', 'estimate_date_delivery', 'actual_date_delivery','customer_po_date','po_received_date'], 'safe'],
            [['margin_percentage', 'gross_profit', 'cgst_amount', 'sgst_amount', 'igst_amount', 'basic_amount', 'grand_total'], 'number'],
            [['address','terms_and_condition'], 'string'],
            [['salesorder_dit_no', 'quote_name', 'requester_name_contact_name', 'bill_to_legal_name', 'gst', 'amount_in_words', 'first_level_email', 'first_level_designation', 'second_level_name', 'second_level_email', 'second_level_designation', 'wh_first_level_email', 'wh_first_level_designation', 'wh_second_level_email', 'wh_second_level_designation', 'pro_first_level_email', 'pro_first_level_designation', 'pro_second_level_email', 'pro_second_level_designation', 'case_scattered_delivery_files', 'scope_work_installation_doc','state', 'city','customer_po_num','po_received_date'], 'string', 'max' => 200],
            [['state_code', 'pin_code', 'pan'], 'string', 'max' => 50],
            [['first_level_number', 'second_level_number', 'wh_first_level_number', 'wh_second_level_number', 'pro_first_level_number', 'pro_second_level_number'], 'string', 'max' => 20],
             [['deal_name_auto'], 'string', 'max' => 500],
             // added for handling blank values saving in by ptpatel on date 24-01-2026
            [['account_name'], 'trim'],
            [['account_name'], 'required', 'message' => 'Account Name cannot be blank.'],
            [['account_name'], 'integer', 'message' => 'Account Name must be a number.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'salesorder_dit_id' => 'Salesorder Dit ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'deleted' => 'Deleted',
            'salesorder_dit_no' => 'Salesorder Dit No',
            'deal_name' => 'Deal Name',
            'account_name' => 'Account Name',
            'so_stage' => 'So Stage',
            'margin_percentage' => 'Margin Percentage',
            'gross_profit' => 'Gross Profit',
            'quote_name' => 'Quote Name',
            'requester_name_contact_name' => 'Requester Name Contact Name',
            'so_type' => 'So Type',
            'team' => 'Team',
            'delivery_location' => 'Delivery Location',
            'bill_to_legal_name' => 'Bill To Legal Name',
            'address' => 'Address',
            'state' => 'State',
            'city' => 'City',
            'state_code' => 'State Code',
            'pin_code' => 'Pin Code',
            'gst' => 'Gst',
            'pan' => 'Pan',
            'cgst_amount' => 'Cgst Amount',
            'sgst_amount' => 'Sgst Amount',
            'igst_amount' => 'Igst Amount',
            'basic_amount' => 'Basic Amount',
            'grand_total' => 'Grand Total',
            'amount_in_words' => 'Amount In Words',
            'first_level_name' => 'First Level Name',
            'first_level_number' => 'First Level Number',
            'first_level_email' => 'First Level Email',
            'first_level_designation' => 'First Level Designation',
            'second_level_name' => 'Second Level Name',
            'second_level_number' => 'Second Level Number',
            'second_level_email' => 'Second Level Email',
            'second_level_designation' => 'Second Level Designation',
            'wh_first_level_name' => 'Wh First Level Name',
            'wh_first_level_number' => 'Wh First Level Number',
            'wh_first_level_email' => 'Wh First Level Email',
            'wh_first_level_designation' => 'Wh First Level Designation',
            'wh_second_level_name' => 'Wh Second Level Name',
            'wh_second_level_number' => 'Wh Second Level Number',
            'wh_second_level_email' => 'Wh Second Level Email',
            'wh_second_level_designation' => 'Wh Second Level Designation',
            'pro_first_level_name' => 'Pro First Level Name',
            'pro_first_level_number' => 'Pro First Level Number',
            'pro_first_level_email' => 'Pro First Level Email',
            'pro_first_level_designation' => 'Pro First Level Designation',
            'pro_second_level_name' => 'Pro Second Level Name',
            'pro_second_level_number' => 'Pro Second Level Number',
            'pro_second_level_email' => 'Pro Second Level Email',
            'pro_second_level_designation' => 'Pro Second Level Designation',
            'timeline_commited' => 'Timeline Commited',
            'timeline_commited_date' => 'Timeline Commited Date',
            'case_scattered_delivery' => 'Case Scattered Delivery',
            'case_scattered_delivery_files' => 'Case Scattered Delivery Files',
            'additional_service_offered' => 'Additional Service Offered',
            'free_chargeable_offered_services' => 'Free Chargeable Offered Services',
            'scope_work_installation' => 'Scope Work Installation',
            'scope_work_installation_doc' => 'Scope Work Installation Doc',
            'estimate_date_delivery' => 'Estimate Date Delivery',
            'actual_date_delivery' => 'Actual Date Delivery',
            'send_for_approval' => 'Send For Approval',            
            'deal_name_auto' => 'Deal Name Auto',
            'po_received_date' => 'Po Received Date',
        ];
    }

    /**
     * Gets query for [[SalesorderditProductDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSalesorderditProductDetails()
    {
        return $this->hasMany(SalesorderditProductDetails::class, ['salesorder_dit_id' => 'salesorder_dit_id']);
    }

    /**
     * Gets query for [[SalesorderditShipToAddresses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSalesorderditShipToAddresses()
    {
        return $this->hasMany(SalesorderditShipToAddress::class, ['salesorder_dit_id' => 'salesorder_dit_id']);
    }

    
    public function completedQuote($Recordid)
    {
        $connection = Yii::$app->db;
        //get Quote from this SO
         $sql = "SELECT quote_name
                        FROM salesorder_dit 
                        Where salesorder_dit_id = :salesorder_dit_id";

        $command = $connection->createCommand($sql)->bindValue(":salesorder_dit_id", $Recordid);
        $columns = $command->queryOne();
        $quote_name = $columns['quote_name'];
        //check if quote for all the so generated
         $sql = "SELECT opd.*, 
               product_dit.product_name AS prod_name,
               product_dit.product_description AS prod_description,
               product_dit.oem_part_number AS prod_oem_part_number
        FROM quotesdit_product_detail opd 
        JOIN quotes_dit ON quotes_dit.quotes_dit_id = opd.quotes_dit_id
        JOIN product_dit ON product_dit.productdit_id = opd.product_name
        WHERE opd.quotes_dit_id = :quote_name";

        $command = $connection->createCommand($sql)->bindValue(":quote_name", $quote_name);
        $columns = $command->queryAll(); //always return one
        $cnt = 0;
        // Loop by reference to allow modification/removal
        foreach ($columns as $key => &$rows) {
            $product_name = $rows['product_name'];
            $quote_qty = (float) $rows['qty']; // Make sure it's treated as a number
            $remaining_qty = $quote_qty;
            // Check if PO is created for this SO and product
            $sql_chk = "SELECT sum(qty) as qty
                        FROM salesorder_dit so
                        LEFT JOIN salesorderdit_product_details spd ON spd.salesorder_dit_id = so.salesorder_dit_id
                        WHERE so.quote_name = :quote_name  AND spd.product_name = :product_name";

            $cmd = $connection->createCommand($sql_chk)
                ->bindValue(":quote_name", $quote_name)
                ->bindValue(":product_name", $product_name);

            $chkcolumns = $cmd->queryOne();
            if ($chkcolumns) {          
                $ordered_qty = (float) $chkcolumns['qty'];                
                $remaining_qty = $quote_qty - $ordered_qty;
            }

            if ($remaining_qty > 0) {
                $cnt = 0;

                break;
            } else {
               $cnt++;
            }
        }
        //insert into so_completed_quote_dit table
        if($cnt > 0)//quote generated for all the products
        {
            //check if record already exist
            $sql_chk = "SELECT count(*) as cntso
                        FROM so_completed_quote_dit 
                        WHERE quote_dit_id = :quote_name ";

            $cmd = $connection->createCommand($sql_chk)
                ->bindValue(":quote_name", $quote_name);
            $chkso = $cmd->queryOne();
            if(!$chkso['cntso'])
            {
                //insert into table
                $sql = "INSERT INTO `so_completed_quote_dit`( `quote_dit_id`, `completed_date`) VALUES (:quote_dit_id,now())";
                 $cmd = $connection->createCommand($sql)
                ->bindValue(":quote_dit_id", $quote_name)->execute();
            }

        }
        else{
            //delete if already exist 
            $sql = "DELETE FROM `so_completed_quote_dit` WHERE  `quote_dit_id`=:quote_dit_id";
                 $cmd = $connection->createCommand($sql)
                ->bindValue(":quote_dit_id", $quote_name)->execute();
        }

    }
}
