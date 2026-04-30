<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "warehouse".
 *
 * @property int $warehouse_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $warehouse_name
 * @property string $warehouse_no
 * @property string|null $address
 * @property int|null $city
 * @property string|null $state
 * @property int|null $stateid
 * @property string $statecode
 * @property string $pincode
 * @property string $gstn
 * @property string|null $email
 * @property string|null $secondary_email
 * @property string|null $email_opt_out
 * @property int|null $exchange_rate
 * @property int|null $currency
 * @property string|null $branch
 * @property int|null $branch_id
 * @property string|null $attention
 * @property string|null $street1
 * @property string|null $street2
 * @property int|null $Country/Region
 * @property int|null $phone
 * @property string|null $warehouse_manager
 * @property string|null $contact_number
 * @property string|null $pan_number
 * @property string|null $organization
 * @property int $deleted
 */
class Warehouse extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'warehouse';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['warehouse_name', 'address', 'city', 'state', 'stateid', 'email', 'secondary_email', 'email_opt_out', 'exchange_rate', 'currency', 'branch', 'branch_id', 'attention', 'street1', 'street2', 'Country/Region', 'phone', 'warehouse_manager', 'contact_number', 'pan_number', 'organization'], 'default', 'value' => null],
            [['deleted'], 'default', 'value' => 0],
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime', 'warehouse_no', 'statecode', 'pincode', 'gstn'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'city', 'stateid', 'exchange_rate', 'currency', 'branch_id', 'Country/Region', 'phone', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime'], 'safe'],
            [['warehouse_name', 'branch', 'attention', 'street1', 'street2', 'organization'], 'string', 'max' => 100],
            [['warehouse_no', 'warehouse_manager', 'pan_number'], 'string', 'max' => 200],
            [['address'], 'string', 'max' => 3000],
            [['state'], 'string', 'max' => 255],
            [['statecode', 'pincode'], 'string', 'max' => 10],
            [['gstn', 'contact_number'], 'string', 'max' => 20],
            [['email', 'secondary_email', 'email_opt_out'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'warehouse_id' => 'Warehouse ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'warehouse_name' => 'Warehouse Name',
            'warehouse_no' => 'Warehouse No',
            'address' => 'Address',
            'city' => 'City',
            'state' => 'State',
            'stateid' => 'Stateid',
            'statecode' => 'Statecode',
            'pincode' => 'Pincode',
            'gstn' => 'Gstn',
            'email' => 'Email',
            'secondary_email' => 'Secondary Email',
            'email_opt_out' => 'Email Opt Out',
            'exchange_rate' => 'Exchange Rate',
            'currency' => 'Currency',
            'branch' => 'Branch',
            'branch_id' => 'Branch ID',
            'attention' => 'Attention',
            'street1' => 'Street1',
            'street2' => 'Street2',
            'Country/Region' => 'Country/region',
            'phone' => 'Phone',
            'warehouse_manager' => 'Warehouse Manager',
            'contact_number' => 'Contact Number',
            'pan_number' => 'Pan Number',
            'organization' => 'Organization',
            'deleted' => 'Deleted',
        ];
    }

}
