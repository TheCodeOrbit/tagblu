<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "grn_shipped_details".
 *
 * @property int $grn_shipped_details_id
 * @property int $grn_id
 * @property string|null $transporter_name
 * @property string|null $vehicle_size
 * @property string|null $shippment_mode
 * @property string|null $docket_number
 * @property string|null $seal_number
 * @property string|null $vehicle_number
 * @property string|null $shipped_date
 * @property string|null $estimate_delivery_date
 * @property string|null $delivery_date
 * @property string|null $status
 * @property int $deleted
 */
class GrnShippedDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'grn_shipped_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['grn_id'], 'required'],
            [['grn_id', 'deleted'], 'integer'],
            [['shipped_date', 'estimate_delivery_date', 'delivery_date',
            'transporter_name', 'vehicle_size', 'shippment_mode', 'docket_number', 'seal_number', 'vehicle_number', 'status'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'grn_shipped_details_id' => 'Grn Shipped Details ID',
            'grn_id' => 'Grn ID',
            'transporter_name' => 'Transporter Name',
            'vehicle_size' => 'Vehicle Size',
            'shippment_mode' => 'Shippment Mode',
            'docket_number' => 'Docket Number',
            'seal_number' => 'Seal Number',
            'vehicle_number' => 'Vehicle Number',
            'shipped_date' => 'Shipped Date',
            'estimate_delivery_date' => 'Estimate Delivery Date',
            'delivery_date' => 'Delivery Date',
            'status' => 'Status',
            'deleted' => 'Deleted',
        ];
    }

    public function saveGrnShippedDetails($entityId)
    {
        $items=$_POST['grn_shipped_details']??[];
		if(count($items)>0)
		{
			foreach($items as $rec)
			{
                $rec['grn_id']=$entityId;
                $rec_obj=new GrnShippedDetails;	
                $rec_obj->attributes=$rec;
                $rec_obj->validate();
                $rec_obj->save(false);
			}
		}
    }
}
