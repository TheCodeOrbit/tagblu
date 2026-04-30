<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "opportunity_ship_detail".
 *
 * @property int $opportunity_shipdetail_id
 * @property int $opportunity_id
 * @property int|null $ship_to_location
 * @property string|null $ship_to_address
 * @property string|null $ship_to_state
 * @property string|null $ship_state_code
 * @property string|null $ship_legal_name
 * @property string|null $pan_number
 * @property string|null $gstin_no_uin
 * @property int $deleted
 *
 * @property Opportunity $opportunity
 */
class OpportunityShipDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'opportunity_ship_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['opportunity_id'], 'required'],
            [['opportunity_id', 'ship_to_location', 'deleted'], 'integer'],
            [[ 'ship_to_state', 'ship_state_code', 'ship_legal_name', 'gstin_no_uin'], 'string', 'max' => 200],
            [['pan_number'], 'string', 'max' => 100],
            [['ship_to_address'], 'string', 'max' => 3000],
            [['opportunity_id'], 'exist', 'skipOnError' => true, 'targetClass' => Opportunity::class, 'targetAttribute' => ['opportunity_id' => 'opportunity_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'opportunity_shipdetail_id' => 'Opportunity Shipdetail ID',
            'opportunity_id' => 'Opportunity ID',
            'ship_to_location' => 'Ship To Location',
            'ship_to_address' => 'Ship To Address',
            'ship_to_state' => 'Ship To State',
            'ship_state_code' => 'Ship State Code',
            'ship_legal_name' => 'Ship Legal Name',
            'pan_number' => 'Pan Number',
            'gstin_no_uin' => 'Gstin No Uin',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * Gets query for [[Opportunity]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOpportunity()
    {
        return $this->hasOne(Opportunity::class, ['opportunity_id' => 'opportunity_id']);
    }

     public function saveOpportunityShipDetail($entityId)
    {

        $saveOpportunityShipDetail = $_POST['opportunity_ship_detail']??[];
        // echo "<br>pickup vehicle<pre>";
        // print_r($_POST['pickup_vehicle_details']);die;
        if (!empty($saveOpportunityShipDetail)) {
            if (count($saveOpportunityShipDetail) > 0) {
                foreach ($saveOpportunityShipDetail as $product_detail) {
                    // echo $entityId;die;
                    if(is_array($product_detail))
                    {
                    $product_detail['opportunity_id'] = intval($entityId);
                    $product_detail_obj = new OpportunityShipDetail();
                    $product_detail_obj->attributes = $product_detail;
                    // print_r($product_detail_obj->attributes);die;
                    $product_detail_obj->validate();
                    $product_detail_obj->save(false);
                    // $modlog = new ModtrackerBasic();
                    // $modlog->auditlog($oldAttributes = '', $product_detail_obj, 'productdetail', $product_detail_obj->$product_costing_detail_id, 0, Yii::$app->user->id);
                    }
                }
            }
        }
    }
}
