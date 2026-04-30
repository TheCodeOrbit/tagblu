<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "raiserequest_vendor".
 *
 * @property int $raiserequest_vendor_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string|null $createdtime
 * @property string|null $modifiedtime
 * @property string $raiserequest_vendor_no
 * @property string|null $acc_name
 * @property string|null $parent_account
 * @property int|null $organization
 * @property string|null $description
 * @property int|null $zone_region
 * @property string|null $address
 * @property string|null $country
 * @property string|null $state
 * @property string|null $city
 * @property string|null $phone
 * @property string|null $email
 * @property int|null $account_function
 * @property int|null $status
 * @property int $deleted
 * @property int $is_temp
 */
class RaiserequestVendor extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'raiserequest_vendor';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // 'acc_name',
            [['createdtime', 'modifiedtime',  'parent_account', 'organization', 'description', 'zone_region', 'address', 'country', 'state', 'city', 'phone', 'email', 'account_function', 'status'], 'default', 'value' => null],
            [['is_temp'], 'default', 'value' => 0],
            [['ownerid', 'creatorid', 'modifiedby', 'raiserequest_vendor_no'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'organization', 'zone_region', 'account_function', 'status', 'deleted', 'is_temp'], 'integer'],
            [['createdtime', 'modifiedtime'], 'safe'],
            [['raiserequest_vendor_no'], 'string', 'max' => 50],
            [['acc_name', 'parent_account', 'country', 'state', 'city'], 'string', 'max' => 100],
            [['description'], 'string', 'max' => 500],
            [['address'], 'string', 'max' => 3000],
            [['phone'], 'string', 'max' => 15],
            [['email'], 'string', 'max' => 255],
             // added for handling blank values saving in by ptpatel on date 27-01-2026
            [['acc_name'], 'trim'],
            [['acc_name'], 'required', 'message' => 'Account Name cannot be blank.'],
            // [['acc_name'], 'integer', 'message' => 'Account Name must be a number.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'raiserequest_vendor_id' => 'Raiserequest Vendor ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'raiserequest_vendor_no' => 'Raiserequest Vendor No',
            'acc_name' => 'Acc Name',
            'parent_account' => 'Parent Account',
            'organization' => 'Organization',
            'description' => 'Description',
            'zone_region' => 'Zone Region',
            'address' => 'Address',
            'country' => 'Country',
            'state' => 'State',
            'city' => 'City',
            'phone' => 'Phone',
            'email' => 'Email',
            'account_function' => 'Account Function',
            'status' => 'Status',
            'deleted' => 'Deleted',
            'is_temp' => 'Is Temp',
        ];
    }

}
