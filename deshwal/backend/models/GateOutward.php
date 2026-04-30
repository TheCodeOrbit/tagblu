<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "gate_outward".
 *
 * @property int $gateoutward_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property int $deleted
 * @property string $gateoutward_no
 * @property string|null $vehicle_number
 * @property string|null $invoice_number
 * @property string|null $invoice_date
 * @property string|null $invoice_image
 * @property string|null $gatepass_number
 * @property string|null $gatepass_image
 */
class GateOutward extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gate_outward';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime', 'gateoutward_no'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime', 'invoice_date'], 'safe'],
            [['gateoutward_no'], 'string', 'max' => 100],
            [['vehicle_number', 'invoice_number', 'invoice_image', 'gatepass_number', 'gatepass_image'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'gateoutward_id' => 'Gateoutward ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'deleted' => 'Deleted',
            'gateoutward_no' => 'Gateoutward No',
            'vehicle_number' => 'Vehicle Number',
            'invoice_number' => 'Invoice Number',
            'invoice_date' => 'Invoice Date',
            'invoice_image' => 'Invoice Image',
            'gatepass_number' => 'Gatepass Number',
            'gatepass_image' => 'Gatepass Image',
        ];
    }
}
