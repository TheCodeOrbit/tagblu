<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "billing_from_address".
 *
 * @property int $billingfromaddress_id
 * @property int|null $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $billingfromaddress_no
 * @property int|null $contractid
 * @property int|null $account_name
 * @property int|null $location_name
 * @property string|null $address
 * @property int|null $city
 * @property int|null $state
 * @property string|null $pin_code
 * @property string|null $spoc_name
 * @property string|null $email
 * @property string|null $phone_no
 * @property int $deleted
 */
class BillingFromAddress extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'billing_from_address';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'billingfromaddress_no', 'contractid', 'account_name', 'location_name', 'address', 'city', 'state', 'pin_code', 'spoc_name', 'email', 'phone_no'], 'default', 'value' => null],
            [['deleted'], 'default', 'value' => 0],
            [['ownerid', 'creatorid', 'modifiedby', 'contractid', 'account_name', 'location_name', 'city', 'state', 'deleted'], 'integer'],
            [['creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['createdtime', 'modifiedtime'], 'safe'],
            [['address'], 'string'],
            [['billingfromaddress_no', 'spoc_name', 'email'], 'string', 'max' => 200],
            [['pin_code'], 'string', 'max' => 100],
            [['phone_no'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'billingfromaddress_id' => 'Billingfromaddress ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'billingfromaddress_no' => 'Billingfromaddress No',
            'contractid' => 'Contractid',
            'account_name' => 'Account Name',
            'location_name' => 'Location Name',
            'address' => 'Address',
            'city' => 'City',
            'state' => 'State',
            'pin_code' => 'Pin Code',
            'spoc_name' => 'Spoc Name',
            'email' => 'Email',
            'phone_no' => 'Phone No',
            'deleted' => 'Deleted',
        ];
    }

}
