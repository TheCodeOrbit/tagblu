<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "raiserequest_client".
 *
 * @property int $raiserequest_client_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string|null $createdtime
 * @property string|null $modifiedtime
 * @property string $raiserequest_client_no
 * @property string|null $acc_name
 * @property string|null $parent_account
 * @property int|null $zone_region
 * @property string|null $address
 * @property string|null $country
 * @property string|null $state
 * @property string|null $city
 * @property string|null $phone
 * @property string|null $email
 * @property int|null $account_type
 * @property int|null $status
 * @property int $deleted
 * @property int $is_temp
 */
class RaiserequestClient extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'raiserequest_client';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // 'acc_name', remove from null
            [['createdtime', 'modifiedtime',  'parent_account', 'zone_region', 'address', 'country', 'state', 'city', 'phone', 'email', 'account_type', 'status'], 'default', 'value' => null],
            [['is_temp'], 'default', 'value' => 0],
            [['ownerid', 'creatorid', 'modifiedby', 'raiserequest_client_no'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'zone_region', 'account_type', 'status', 'deleted', 'is_temp','team_name','india_it_users','industry','sub_industry_type','sub_industry','deshwal_isr','account_manager','devit_isr','devit_vertical_manager','devit_rsm_director','devit_business_manager','organization','isr_head'], 'integer'],
            [['createdtime', 'modifiedtime'], 'safe'],
            [['raiserequest_client_no'], 'string', 'max' => 50],
            [['acc_name', 'parent_account', 'country', 'state', 'city'], 'string', 'max' => 100],
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
            'raiserequest_client_id' => 'Raiserequest Client ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'raiserequest_client_no' => 'Raiserequest Client No',
            'acc_name' => 'Acc Name',
            'parent_account' => 'Parent Account',
            'zone_region' => 'Zone Region',
            'address' => 'Address',
            'country' => 'Country',
            'state' => 'State',
            'city' => 'City',
            'phone' => 'Phone',
            'email' => 'Email',
            'account_type' => 'Account Type',
            'status' => 'Status',
            'deleted' => 'Deleted',
            'is_temp' => 'Is Temp',
        ];
    }
    public function getDeshwalIsrUser()
    {
        return $this->hasOne(User::class, ['id' => 'deshwal_isr'])
        ->select(['id', 'role']);
    }

    public function getAccountManagerUser()
    {
        return $this->hasOne(User::class, ['id' => 'account_manager'])
        ->select(['id', 'role']);
    }

    public function getDevitIsrUser()
    {
        return $this->hasOne(User::class, ['id' => 'devit_isr'])
        ->select(['id', 'role']);
    }

    public function getDevitVerticalManagerUser()
    {
        return $this->hasOne(User::class, ['id' => 'devit_vertical_manager'])
        ->select(['id', 'role']);
    }

    public function getDevitRsmDirectorUser()
    {
        return $this->hasOne(User::class, ['id' => 'devit_rsm_director'])
        ->select(['id', 'role']);
    }

    public function getDevitBusinessManagerUser()
    {
        return $this->hasOne(User::class, ['id' => 'devit_business_manager'])
        ->select(['id', 'role']);
    }
    public function getIsrHeadUser()
    {
        return $this->hasOne(User::class, ['id' => 'isr_head'])
        ->select(['id', 'role']);
    }

}
