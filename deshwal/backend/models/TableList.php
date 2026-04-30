<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "table_list".
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string|null $phone
 * @property string|null $country
 * @property string|null $city
 * @property string|null $owner
 * @property string|null $company_name
 * @property string|null $address
 * @property string|null $company_address
 * @property string|null $company_websibe
 * @property string|null $employee_age
 * @property string|null $employee_name
 * @property string $created_at
 */
class TableList extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'table_list';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name', 'email'], 'required'],
            [['created_at'], 'safe'],
            [['first_name', 'last_name'], 'string', 'max' => 520],
            [['email', 'country', 'city', 'owner', 'company_name', 'address', 'company_address', 'company_website', 'employee_age', 'employee_name'], 'string', 'max' => 200],
            [['phone'], 'string', 'max' => 105],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'country' => 'Country',
            'city' => 'City',
            'owner' => 'Owner',
            'company_name' => 'Company Name',
            'address' => 'Address',
            'company_address' => 'Company Address',
            'company_website' => 'Company Websibe',
            'employee_age' => 'Employee Age',
            'employee_name' => 'Employee Name',
            'created_at' => 'Created At',
        ];
    }
}
