<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "customer_pickup_assets".
 *
 * @property int $pickup_asset_id
 * @property int|null $pickup_request_id
 * @property string|null $product_name
 * @property int|null $total_quantity
 * @property string|null $uom
 * @property int $is_deleted
 *
 * @property CustomerPickupRequest $pickupRequest
 */
class CustomerPickupAssets extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customer_pickup_assets';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pickup_request_id', 'total_quantity', 'is_deleted'], 'integer'],
            [['product_name','other_product_name', 'uom','make','model','serial_no','processor','ram','hdd_sdd','remarks'], 'string', 'max' => 255],
            //[['product_name', 'total_quantity'], 'required'],
            [['pickup_request_id'], 'exist', 'skipOnError' => true, 'targetClass' => CustomerPickupRequest::class, 'targetAttribute' => ['pickup_request_id' => 'pickup_request_id']],
            // Conditional validation: If product_name is '97', other_product_name is required
            ['other_product_name', 'required', 'when' => function ($model) {
                return $model->product_name == '97';
            }, 'whenClient' => "function(attribute, value) {
                var productField = $(attribute.input).closest('tr').find('[name$=\"[product_name]\"]'); 
                return productField.val() == '97';
            }", 'message' => 'Other Product Name is required when Product Name is Others'],
            [['product_name', 'total_quantity'], 'required', 
                'when' => function ($model) {
                    return Yii::$app->request->post('action') === 'submit';
                }, 'whenClient' => "function (attribute, value) {
                    return $('input[name=\"action\"]').val() === 'submit';
                }"
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pickup_asset_id' => 'Pickup Asset ID',
            'pickup_request_id' => 'Pickup Request ID',
            'product_name' => 'Product Name',
            'other_product_name' => 'Other Product Name',
            'total_quantity' => 'Total Quantity',
            'uom' => 'Uom',
            'is_deleted' => 'Is Deleted',
            'make' => "Make",
            'model'=> "Model",
            'serial_no' => "Serial No",
            'processor' => "Processor",
            'ram' => "ram",
            'hdd_sdd' => "HDD/SDD",
            'remarks' => "Remarks"
        ];
    }

    /**
     * Gets query for [[PickupRequest]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPickupRequest()
    {
        return $this->hasOne(CustomerPickupRequest::class, ['pickup_request_id' => 'pickup_request_id']);
    }
}
