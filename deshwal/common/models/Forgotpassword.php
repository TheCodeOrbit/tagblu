<?php

namespace common\models;

use Yii;
use yii\base\Model;
use common\models\User;
use PHPMailer\PHPMailer\PHPMailer;

class Forgotpassword extends Model
{
    public $username;

    /**
     * Validation rules
     */
    public function rules()
    {
        return [
            [['username'], 'trim'],
            [['username'], 'required'],
            ['username', 'validateUsername'],
        ];
    }

    public function validateUsername($attribute, $params)
    {
        $user = User::findOne(['username' => $this->$attribute]);
        if (!$user) {
            $this->addError($attribute, 'Username not found.');
        }
    }
    /**
     * Sends a password reset email
     */
    public function sendEmail()
    {
        /** @var User $user */
        $user = User::findOne(['username' => $this->username]);

        // echo "<pre>";print_r($user);die;
        if (!$user || empty($user->email)) {
            return false; // user not found or has no email
        }

        // generate token
        $user->generatePasswordResetToken();
        if (!$user->save(false)) {
            return false;
        }

        // build reset link
        $resetLink = Yii::$app->urlManager->createAbsoluteUrl([
            'site/resetpassword',
            'token' => $user->password_reset_token
        ]);

        $expireSeconds = Yii::$app->params['user.passwordResetTokenExpire'];
        $expiryTime = date("h:i A", time() + $expireSeconds);

        $body = "
                <html>
                <head>
                <title>Password Reset Request</title>
                </head>
                <body>
                <p>Hello,</p>
                <p>We have received a request to reset your password. Please click on below link to reset your password.</p>
                <p>Please note that this link is valid till 12 hours from now.</p>
                <p>
                    <a href='$resetLink' style='text-decoration:none;'>
                    Reset Password
                    </a>
                </p>
                <p>If you didn't request this, you can safely ignore this email.</p>
                <p>Thank you.</p>
                </body>
                </html>
                ";
        // echo $body;
        // die;
        try {
            $rootDir = dirname(__DIR__);
            // echo $rootDir;die;
            require_once($rootDir . '/../PHPMailer/src/Exception.php');
            require_once($rootDir . '/../PHPMailer/src/PHPMailer.php');
            require_once($rootDir . '/../PHPMailer/src/SMTP.php');
            require_once($rootDir . '/../api/params.php');

            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->Host = SMTP_HOST;
            $mail->Port = SMTP_PORT;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USER;
            $mail->Password = SMTP_PASS;
            $to_mail_id = $user->email;
            $mail->SMTPSecure = 'tls';     // Enable TLS encryption

            $mail->MsgHTML($body);


            $mail->SetFrom('erp@Dwmpl.com');
            $mail->isHTML(true);
            $mail->Subject = "Password Reset Request";

            $mail->AddAddress($to_mail_id);
            if (!$mail->Send()) {
                // echo "Mailer Error: " . $mail->ErrorInfo;

                return false;
            } else {
                // echo "<br>Mail sent successfully";
                // die;
                return true;
            }
        } catch (\Throwable $e) {
            echo "Error sending email: " . $e->getMessage();
            Yii::error($e->getMessage(), __METHOD__);
            // die;
        }
    }
}
