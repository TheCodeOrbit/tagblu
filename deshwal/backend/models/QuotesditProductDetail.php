<?php

namespace app\models;
use yii\web\BadRequestHttpException;

use Yii;

/**
 * This is the model class for table "quotesdit_product_detail".
 *
 * @property int $quotesdit_product_detail_id
 * @property int $quotes_dit_id
 * @property int|null $product_name
 * @property string|null $hsn_code
 * @property int|null $qty
 * @property float|null $basic_price
 * @property float|null $cgst_per
 * @property float|null $sgst_per
 * @property float|null $igst_per
 * @property float|null $cgst_amount
 * @property float|null $sgst_amount
 * @property float|null $igst_amount
 * @property float|null $amount
 * @property float|null $sub_total
 * @property float|null $grand_total
 * @property string|null $amount_in_words
 * @property string|null $terms_and_condition
 * @property int $deleted
 *
 * @property QuotesDit $quotesDit
 * @property QuotesDit $quotesDit0
 */
class QuotesditProductDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quotesdit_product_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['quotes_dit_id'], 'required'],
            [['quotes_dit_id', 'product_name', 'deleted'], 'integer'],
            [['basic_price', 'cgst_per', 'sgst_per', 'igst_per', 'cgst_amount', 'sgst_amount', 'igst_amount', 'amount', 'sub_total', 'grand_total'], 'number'],
            [['hsn_code'], 'string', 'max' => 100],
            [['amount_in_words'], 'string', 'max' => 200],
            [['terms_and_condition'], 'string', 'max' => 500],
            [['product_description'], 'string'],
            [['quotes_dit_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuotesDit::class, 'targetAttribute' => ['quotes_dit_id' => 'quotes_dit_id']],
            //[['quotes_dit_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuotesDit::class, 'targetAttribute' => ['quotes_dit_id' => 'quotes_dit_id']],
            [['qty'],'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'quotesdit_product_detail_id' => 'Quotesdit Product Detail ID',
            'product_description' => 'Product description',
            'quotes_dit_id' => 'Quotes Dit ID',
            'product_name' => 'Product Name',
            'hsn_code' => 'Hsn Code',
            'qty' => 'Qty',
            'basic_price' => 'Basic Price',
            'cgst_per' => 'Cgst Per',
            'sgst_per' => 'Sgst Per',
            'igst_per' => 'Igst Per',
            'cgst_amount' => 'Cgst Amount',
            'sgst_amount' => 'Sgst Amount',
            'igst_amount' => 'Igst Amount',
            'amount' => 'Amount',
            'sub_total' => 'Sub Total',
            'grand_total' => 'Grand Total',
            'amount_in_words' => 'Amount In Words',
            'terms_and_condition' => 'Terms And Condition',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * Gets query for [[QuotesDit]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuotesDit()
    {
        return $this->hasOne(QuotesDit::class, ['quotes_dit_id' => 'quotes_dit_id']);
    }

    /**
     * Gets query for [[QuotesDit0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuotesDit0()
    {
        return $this->hasOne(QuotesDit::class, ['quotes_dit_id' => 'quotes_dit_id']);
    }

    public function saveQuotesditProductDetail($entityId)
    {
        if(empty($_REQUEST['quotesdit_product_detail'])){
            return false;
        }
        else{
             //delete old record from child table            
             $sql = "Delete from quotesdit_product_detail where quotes_dit_id = :quotes_dit_id";
             Yii::$app->db->createCommand($sql)->bindValue(":quotes_dit_id", $entityId)->execute();
        }
        $po_items=$_REQUEST['quotesdit_product_detail'];
		if(count($po_items)>0)
		{
			foreach($po_items as $product_detail)
			{
                // echo $entityId;
                $product_detail['quotes_dit_id']=$entityId;
                if ($product_detail['amount'] === '') {
                    // Set a flash message (if necessary)
                    Yii::$app->session->setFlash('error', 'Invalid request.Error: Invalid request. Product Amount cannot be zero or blank.');

                    // Throw an exception with a custom message
                    throw new BadRequestHttpException('Invalid request.Error: Invalid request. Product Amount cannot be zero or blank');
     
                }
                else if(empty($product_detail['qty']) || $product_detail['qty'] == 0) 
                {
                    // Set a flash message (if necessary)
                    Yii::$app->session->setFlash('error', 'Invalid request.Error: Invalid request. Product Quantity cannot be zero or blank.');

                    // Throw an exception with a custom message
                    throw new BadRequestHttpException('Invalid request.Error: Invalid request. Product Quantity cannot be zero or blank');
     
                }
                else if($product_detail['basic_price']  === '') 
                {
                    // Set a flash message (if necessary)
                    Yii::$app->session->setFlash('error', 'Invalid request.Error: Invalid request.Basic Price cannot be zero or blank.');

                    // Throw an exception with a custom message
                    throw new BadRequestHttpException('Invalid request.Error: Invalid request. Basic Price cannot be zero or blank');
     
                }
                else if(empty($product_detail['product_name']) || $product_detail['product_name'] == 0) 
                {
                    // Set a flash message (if necessary)
                    Yii::$app->session->setFlash('error', 'Invalid request.Error: Invalid Product Name');

                    // Throw an exception with a custom message
                    throw new BadRequestHttpException('Invalid request.Error: Invalid Product Name.');
     
                }
                $product_detail_obj=new QuotesditProductDetail;	
                $product_detail_obj->attributes=$product_detail;
                // print_r($product_detail_obj->attributes);die;
                $product_detail_obj->validate();
                $product_detail_obj->save(false);
			}
		}
    }
}
