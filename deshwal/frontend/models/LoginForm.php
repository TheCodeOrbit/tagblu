<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\Vendorlogin;  // The Member model that interacts with the custom table

class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['password', 'validatePassword'],
            ['rememberMe', 'boolean'],
        ];
    }

    /**
     * Validates the password.
     * @param string $attribute the attribute currently being validated
     */
    public function validatePassword($attribute)
    {
        if ($this->_user && !$this->_user->validatePassword($this->password)) {
            $this->addError($attribute, 'Incorrect username or password.');
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        
        if ($this->validate()) {
            $user = $this->getUser();  // Retrieve the user by username
            if ($user) {
                // Call the login method from Yii's user component with a valid identity
                return Yii::$app->user->login($user, $this->rememberMe ? 3600*24*30 : 0);
            }
        }
        return false;
    }

    /**
     * Finds user by username.
     * @return Vendorlogin|null
     */
    protected function getUser()
    {
        if ($this->_user === false) {
            $this->_user = Vendorlogin::findByUsername($this->username,$this->password);
            if ($this->_user) {
                // $this->addError($attribute, 'Incorrect username or password.');
            }
        }
        // print_r($this->_user);die;
        return $this->_user;
    }
}
