<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "quotes_dit".
 *
 * @property int $quotes_dit_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property int $deleted
 * @property string $quotes_dit_no
 * @property string|null $opportunity_name
 * @property int|null $account_name
 * @property string|null $quote_create_date
 * @property int|null $quote_stage
 * @property int|null $payment_terms
 * @property int|null $category
 * @property float|null $gross_profit
 * @property string|null $expiry_date
 * @property string|null $delivery_terms
 * @property float|null $margin
 * @property int|null $requester_name
 * @property int|null $team_name
 * @property int|null $region
 * @property int|null $bill_to_location
 * @property int|null $bill_to_legal_name
 * @property string|null $bill_to_address
 * @property string|null $bill_to_state
 * @property string|null $city
 * @property string|null $bill_to_state_code
 * @property int|null $pin_code
 * @property string|null $bill_to_gst
 * @property string|null $bill_to_pan
 * @property float|null $cgst_amount
 * @property float|null $sgst_amount
 * @property float|null $igst_amount
 * @property float|null $sub_total
 * @property float|null $grand_total
 * @property string|null $amount_in_words
 * @property string|null $terms_and_condition
 * @property int|null $warehouse_loc_business_entity
 * @property string|null $bill_from_location
 * @property string|null $bill_from_address
 * @property string|null $bill_from_state
 * @property string|null $bill_from_state_code
 * @property int|null $send_for_approval
 *
 * @property QuotesditProductDetail[] $quotesditProductDetails
 * @property QuotesditShipDetail[] $quotesditShipDetails
 */
class QuotesDit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quotes_dit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime', 'quotes_dit_no'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'deleted', 'account_name', 'quote_stage', 'category', 'requester_name', 'team_name', 'region', 'bill_to_location',  'pin_code', 'warehouse_loc_business_entity', 'send_for_approval'], 'integer'],
            [['createdtime', 'modifiedtime', 'quote_create_date', 'expiry_date'], 'safe'],
            [['gross_profit', 'margin', 'cgst_amount', 'sgst_amount', 'igst_amount', 'sub_total', 'grand_total'], 'number'],
            [['bill_to_address', 'terms_and_condition', 'bill_from_address'], 'string'],
            [['quotes_dit_no', 'opportunity_name', 'bill_to_state', 'city', 'bill_to_gst', 'amount_in_words', 'bill_from_location','bill_to_legal_name', 'payment_terms'], 'string', 'max' => 200],
            [['bill_to_state_code'], 'string', 'max' => 50],
            [['bill_to_pan'], 'string', 'max' => 20],
            [['deal_name'], 'string', 'max' => 2000],
            [['delivery_terms'], 'string'], 
            [['bill_from_state', 'bill_from_state_code'], 'string', 'max' => 100],
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
            'quotes_dit_id' => 'Quotes Dit ID',
            'ownerid' => 'Ownerid',
            'deal_name' => 'Deal Name',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'deleted' => 'Deleted',
            'quotes_dit_no' => 'Quotes Dit No',
            'opportunity_name' => 'Opportunity Name',
            'account_name' => 'Account Name',
            'quote_create_date' => 'Quote Create Date',
            'quote_stage' => 'Quote Stage',
            'payment_terms' => 'Payment Terms',
            'category' => 'Category',
            'gross_profit' => 'Gross Profit',
            'expiry_date' => 'Expiry Date',
            'delivery_terms' => 'Delivery Terms',
            'margin' => 'Margin',
            'requester_name' => 'Requester Name',
            'team_name' => 'Team Name',
            'region' => 'Region',
            'bill_to_location' => 'Bill To Location',
            'bill_to_legal_name' => 'Bill To Legal Name',
            'bill_to_address' => 'Bill To Address',
            'bill_to_state' => 'Bill To State',
            'city' => 'City',
            'bill_to_state_code' => 'Bill To State Code',
            'pin_code' => 'Pin Code',
            'bill_to_gst' => 'Bill To Gst',
            'bill_to_pan' => 'Bill To Pan',
            'cgst_amount' => 'Cgst Amount',
            'sgst_amount' => 'Sgst Amount',
            'igst_amount' => 'Igst Amount',
            'sub_total' => 'Sub Total',
            'grand_total' => 'Grand Total',
            'amount_in_words' => 'Amount In Words',
            'terms_and_condition' => 'Terms And Condition',
            'warehouse_loc_business_entity' => 'Warehouse Loc Business Entity',
            'bill_from_location' => 'Bill From Location',
            'bill_from_address' => 'Bill From Address',
            'bill_from_state' => 'Bill From State',
            'bill_from_state_code' => 'Bill From State Code',
            'send_for_approval' => 'Send For First Approval',
        ];
    }

    /**
     * Gets query for [[QuotesditProductDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuotesditProductDetails()
    {
        return $this->hasMany(QuotesditProductDetail::class, ['quotesdit_id' => 'quotes_dit_id']);
    }

    /**
     * Gets query for [[QuotesditShipDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuotesditShipDetails()
    {
        return $this->hasMany(QuotesditShipDetail::class, ['quotesdit_id' => 'quotes_dit_id']);
    }

    public function showEditBtnQuotes($record = [],$user = [])
    {
        if (isset($user) && $user->is_super_admin && isset($record['quotes_dit_id'])) { 
           
                $salesOrder = SalesorderDit::find()->where(['quote_name' => $record['quotes_dit_id']])
                ->andWhere(['=', 'deleted', 0])->all();
                $allSalesCancelled = true;
                if (!empty($salesOrder)) {
                    // As discussed with client on 20-02-2026 Removed Draft check for Sales Order
                    $allSalesCancelled =false;
                    // $allSalesCancelled=false;
                    // foreach ($salesOrder as $sales) { 
                    //     if ($sales->so_stage != 1) {
                    //         $allSalesCancelled = false;
                    //         break;
                    //     }
                    // }
                }
                if ($allSalesCancelled) {
                    return false;
                }
                
        }
        return true;
    }

    //to change related opportunity stage when quote approve or reject added by ptpatel on date 03-04-2026
    public static function handleRelatedModuleStageChange($recordId, $status)
    {
        //  echo "inquotedit model in handlequotedit 1 r=".$recordId." -s".$status;
        $quote = self::findOne($recordId);
        if (!$quote) return;

        $opportunity = Opportunity::findOne($quote->opportunity_name);
        if (!$opportunity) return;

        $oldAttributessrc = $opportunity->attributes;

        if ($status == 4) {
                //approved stage then change opportunity stage
                $opportunity_stage = 10;//quote approved
                
                //update sourcing deal
                //    echo  $sql = "Update opportunity set opportunity_stage = $opportunity_stage where opportunity_id = $related_to_id";die;
                $sql = "Update opportunity set opportunity_stage = :srcstage where opportunity_id = :opportunity_id";
                $updt = Yii::$app->db->createCommand($sql)
                    ->bindValue(":srcstage", $opportunity_stage)
                    ->bindValue(":opportunity_id", $quote->opportunity_name)
                    ->execute();

                $newattributessrc = array("opportunity_stage" => $opportunity_stage);

                $modlog = new ModtrackerBasic();
                $modlog->auditlog($oldAttributessrc, $newattributessrc, "opportunities", $quote->opportunity_name, 2, Yii::$app->user->id);
            // echo "inquotedit model in handlequotedit 2";
                }
    }
}
