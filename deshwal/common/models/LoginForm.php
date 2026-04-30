<?php

namespace common\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    // public function login()
    // {
    //     if ($this->validate()) {
    //         return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
    //     }

    //     return false;
    // }

    public function login()
    {
        if ($this->validate()) {
            $duration = $this->rememberMe ? 3600 * 24 * 30 : 0;

            if (Yii::$app->user->login($this->getUser(), $duration)) {
                Yii::$app->session->regenerateID(true); // 🔐 prevent session fixation
                //below code added to put user sign in activity log by ptpatel on date 25-08-2025
                // Log login activity
                Yii::$app->db->createCommand()->insert('user_activity_log', [
                    'user_id'    => Yii::$app->user->id,
                    'activity'   => 'Login',
                    'ip_address' => Yii::$app->request->userIP,
                    'user_agent' => Yii::$app->request->userAgent,
                    'created_at' => date('Y-m-d H:i:s'),
                ])->execute();
                //code ended for log user activity 
                return true;
            }
        }

        return false;
    }


    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}