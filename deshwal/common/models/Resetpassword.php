<?php

namespace common\models;

use Yii;
use yii\base\Model;
use common\models\User;

class Resetpassword extends Model
{
    public $password;
    public $confirm_password;
    public $token;

    /**
     * @var \common\models\User|null
     */
    private $_user;

    /**
     * Constructor - assign token from controller
     */
    public function __construct($token, $config = [])
    {
        $this->token = $token;
        parent::__construct($config);
    }

    /**
     * Validation rules
     */
    public function rules()
    {
        return [
            [['password', 'confirm_password', 'token'], 'required'],
            // ['password', 'string', 'min' => 6],
            ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength']],
            [
                'confirm_password',
                'compare',
                'compareAttribute' => 'password',
                'message' => "Passwords and Confirm Password don't match."
            ],
            ['token', 'validateToken'],
        ];
    }

    /**
     * Validate the password reset token
     */
    public function validateToken($attribute, $params)
    {
        
        /*if (empty($this->$attribute) || !is_string($this->$attribute)) {
            $error = 'Password reset token cannot be blank.';
            Yii::$app->session->setFlash('error', $error);
            $this->addError($attribute, $error);
            echo "error";die;
            return;
        }

        $this->_user = User::findOne(['password_reset_token' => $this->$attribute]);

        if (!$this->_user) {
            $error = 'Invalid password reset token. Please try Again.';
            Yii::$app->session->setFlash('error', $error);
            $this->addError($attribute, $error);*/
// echo "bj".$this->$attribute;die;
            if (empty($this->$attribute) || !is_string($this->$attribute)) {
                $this->addError($attribute, 'Password reset token cannot be blank.');
                return;
            }

            // $this->_user = User::findOne(['password_reset_token' => $this->$attribute]);
            $this->_user = User::findByPasswordResetToken($this->$attribute);

            if (!$this->_user) {
                $this->addError($attribute, 'Invalid password reset token.');
                return false;;
            }
            return $this->_user;
    }

    /**
     * Resets the password
     */
    public function resetPassword()
{
    // Extra safety: ensure token exists and user was validated
     $user = User::findByPasswordResetToken($this->token);
    // echo "<pre>";print_r($user);die;
    if (!$user) {
        $this->addError('token', 'Invalid password reset token.');
        return false;
    }

    // $user = $this->_user;
    $user->setPassword($this->password);
    $user->removePasswordResetToken();

    return $user->save(false);
}

}
