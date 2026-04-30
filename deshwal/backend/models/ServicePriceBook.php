<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "service_price_book".
 *
 * @property int $servicepricebook_id
 * @property int|null $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $servicepricebook_no
 * @property int|null $contractid
 * @property int|null $service_name
 * @property float|null $base_amount_taxes_excluded
 * @property float|null $taxable_percentage
 * @property int $deleted
 */
class ServicePriceBook extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_price_book';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'servicepricebook_no', 'contractid', 'service_name', 'base_amount_taxes_excluded', 'taxable_percentage'], 'default', 'value' => null],
            [['deleted'], 'default', 'value' => 0],
            [['ownerid', 'creatorid', 'modifiedby', 'contractid', 'service_name', 'deleted'], 'integer'],
            [['creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['createdtime', 'modifiedtime'], 'safe'],
            [['base_amount_taxes_excluded', 'taxable_percentage'], 'number'],
            [['servicepricebook_no'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'servicepricebook_id' => 'Servicepricebook ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'servicepricebook_no' => 'Servicepricebook No',
            'contractid' => 'Contractid',
            'service_name' => 'Service Name',
            'base_amount_taxes_excluded' => 'Base Amount Taxes Excluded',
            'taxable_percentage' => 'Taxable Percentage',
            'deleted' => 'Deleted',
        ];
    }

}
