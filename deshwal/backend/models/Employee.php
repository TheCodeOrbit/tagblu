<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "employee".
 *
 * @property int $EmployeeID
 * @property string $FirstName
 * @property string $LastName
 * @property string $Email
 * @property string|null $PhoneNumber
 * @property int|null $Status
 */
class Employee extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'employee';  // The table name in the database
    }

    public function rules()
    {
        return [
            [['first_name', 'last_name', 'email', 'phone_number', 'status'], 'required'],
            ['email', 'email'],  // Validates email format
            ['phone_number', 'string', 'max' => 15],  // Validates phone number length
            ['status', 'in', 'range' => ['active', 'inactive']],  // Validates the status value
            [['date_time'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'phone_number' => 'Phone Number',
            'status' => 'Status',
            'date_time' => 'Date Time'
        ];
    }
}
