<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "contacts".
 *
 * @property int $contacts_id
 * @property int|null $ownerid
 * @property int|null $creatorid
 * @property int|null $modifiedby
 * @property string|null $createdtime
 * @property string|null $modifiedtime
 * @property string|null $level_management
 * @property string|null $department
 * @property int $is_admin
 * @property string|null $role
 * @property string|null $role_0
 * @property string|null $status
 * @property int|null $related_to
 * @property int|null $related_to_id
 * @property string|null $contact_role
 * @property string|null $designation
 * @property string|null $relationship
 * @property int|null $salutation
 * @property string|null $first_name
 * @property string|null $last_name
 * @property int|null $vendor_account_name
 * @property string|null $vendor_location
 * @property string|null $mobile
 * @property string|null $email
 * @property int|null $landline
 * @property string|null $anniversary_date
 * @property string|null $birth_date
 * @property string|null $strength
 * @property string|null $contact_no
 * @property int|null $home_mobile
 * @property string|null $home_email
 * @property int|null $active_contact
 * @property string|null $reports_to
 * @property string|null $remark
 * @property string|null $weakness
 * @property string|null $social
 * @property string|null $facebook
 * @property string|null $linkedIn
 * @property string|null $twitter
 * @property string|null $instagram
 * @property string|null $mailing_street
 * @property string|null $mailing_city
 * @property string|null $mailing_state
 * @property string|null $mailing_zip
 * @property string|null $mailing_country
 * @property string|null $other_street
 * @property string|null $other_city
 * @property string|null $other_state
 * @property string|null $other_zip
 * @property string|null $other_country
 * @property string|null $username
 * @property string|null $password
 * @property string|null $auth_key
 * @property int $deleted
 * @property int $is_temp
 * @property int|null $seq_no
 * @property string|null $industry
 * @property string|null $hierarchy_level
 * @property string|null $description
 * @property string|null $do_not_call
 * @property string|null $email_opt_out
 * @property string|null $gender
 * @property string|null $middle_name
 * @property string|null $nick_name
 * @property int|null $official_mobile_number
 * @property int|null $personal_mobile_number
 * @property string|null $alternative_phone
 * @property string|null $password_reset_token
 */
class Contacts extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contacts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime', 'level_management', 'department', 'role', 'role_0', 'status', 'related_to', 'related_to_id', 'contact_role', 'designation', 'relationship', 'salutation', 'first_name', 'last_name', 'vendor_account_name', 'vendor_location', 'mobile', 'email', 'landline', 'anniversary_date', 'birth_date', 'strength', 'contact_no', 'home_mobile', 'home_email', 'active_contact', 'reports_to', 'remark', 'weakness', 'social', 'facebook', 'linkedIn', 'twitter', 'instagram', 'mailing_street', 'mailing_city', 'mailing_state', 'mailing_zip', 'mailing_country', 'other_street', 'other_city', 'other_state', 'other_zip', 'other_country', 'username', 'password', 'auth_key', 'seq_no', 'industry', 'hierarchy_level', 'description', 'do_not_call', 'email_opt_out', 'gender', 'middle_name', 'nick_name', 'official_mobile_number', 'personal_mobile_number', 'alternative_phone', 'password_reset_token'], 'default', 'value' => null],
            [['is_temp'], 'default', 'value' => 0],
            [['ownerid', 'creatorid', 'modifiedby', 'is_admin', 'related_to', 'related_to_id', 'salutation', 'vendor_account_name', 'landline', 'home_mobile', 'active_contact', 'deleted', 'is_temp', 'seq_no', 'official_mobile_number', 'personal_mobile_number'], 'integer'],
            [['createdtime', 'modifiedtime', 'anniversary_date', 'birth_date'], 'safe'],
            [['level_management', 'department', 'role', 'role_0', 'status', 'contact_role', 'designation', 'relationship', 'first_name', 'last_name', 'vendor_location', 'strength', 'contact_no', 'reports_to', 'remark', 'weakness', 'social', 'facebook', 'linkedIn', 'twitter', 'instagram', 'mailing_street', 'mailing_city', 'mailing_state', 'other_street', 'description'], 'string', 'max' => 200],
            [['mobile', 'alternative_phone'], 'string', 'max' => 15],
            [['email', 'home_email', 'mailing_zip', 'mailing_country', 'other_city', 'other_state', 'other_zip', 'other_country', 'industry', 'hierarchy_level', 'do_not_call', 'email_opt_out', 'gender', 'middle_name', 'nick_name'], 'string', 'max' => 100],
            [['username'], 'string', 'max' => 110],
            [['password', 'auth_key', 'password_reset_token'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'contacts_id' => 'Contacts ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'level_management' => 'Level Management',
            'department' => 'Department',
            'is_admin' => 'Is Admin',
            'role' => 'Role',
            'role_0' => 'Role 0',
            'status' => 'Status',
            'related_to' => 'Related To',
            'related_to_id' => 'Related To ID',
            'contact_role' => 'Contact Role',
            'designation' => 'Designation',
            'relationship' => 'Relationship',
            'salutation' => 'Salutation',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'vendor_account_name' => 'Vendor Account Name',
            'vendor_location' => 'Vendor Location',
            'mobile' => 'Mobile',
            'email' => 'Email',
            'landline' => 'Landline',
            'anniversary_date' => 'Anniversary Date',
            'birth_date' => 'Birth Date',
            'strength' => 'Strength',
            'contact_no' => 'Contact No',
            'home_mobile' => 'Home Mobile',
            'home_email' => 'Home Email',
            'active_contact' => 'Active Contact',
            'reports_to' => 'Reports To',
            'remark' => 'Remark',
            'weakness' => 'Weakness',
            'social' => 'Social',
            'facebook' => 'Facebook',
            'linkedIn' => 'Linked In',
            'twitter' => 'Twitter',
            'instagram' => 'Instagram',
            'mailing_street' => 'Mailing Street',
            'mailing_city' => 'Mailing City',
            'mailing_state' => 'Mailing State',
            'mailing_zip' => 'Mailing Zip',
            'mailing_country' => 'Mailing Country',
            'other_street' => 'Other Street',
            'other_city' => 'Other City',
            'other_state' => 'Other State',
            'other_zip' => 'Other Zip',
            'other_country' => 'Other Country',
            'username' => 'Username',
            'password' => 'Password',
            'auth_key' => 'Auth Key',
            'deleted' => 'Deleted',
            'is_temp' => 'Is Temp',
            'seq_no' => 'Seq No',
            'industry' => 'Industry',
            'hierarchy_level' => 'Hierarchy Level',
            'description' => 'Description',
            'do_not_call' => 'Do Not Call',
            'email_opt_out' => 'Email Opt Out',
            'gender' => 'Gender',
            'middle_name' => 'Middle Name',
            'nick_name' => 'Nick Name',
            'official_mobile_number' => 'Official Mobile Number',
            'personal_mobile_number' => 'Personal Mobile Number',
            'alternative_phone' => 'Alternative Phone',
            'password_reset_token' => 'Password Reset Token',
        ];
    }

}
