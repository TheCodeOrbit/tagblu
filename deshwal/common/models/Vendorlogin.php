<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $verification_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class Vendorlogin extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 4;
    const STATUS_INACTIVE = 3;
    const STATUS_ACTIVE = 2;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%contacts}}';
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['contacts_id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by username.
     * @param string $username
     * @return Member|null
     */
    public static function findByUsername($username, $password)
    {

        //         $sql = Vendorlogin::find()
//     ->where(['username' => $username, 'status' => self::STATUS_ACTIVE])
//     ->createCommand()
//     ->getRawSql();  // This gives the raw SQL query
// echo $sql;
// // Print the raw SQL query
// Yii::info($sql, __METHOD__);
// die;
        $obj = static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE,'deleted'=>0]);
        // print_r($obj);
        // die;
        if ($obj !== null) {
            // $publickey = filter_var($_POST['pkey'],FILTER_SANITIZE_STRING);

            // Validate password using the instance method
            // If 'validatePassword' is an instance method, you need to create an instance of the object
            // return $obj->validatePassword($password, );
            // $decryptedPassword =  $obj->decryptAndValidatePassword($password, $obj->password,$publickey);
            $decryptedPassword = $password;
            // echo $decryptted;die;
            if(!empty($decryptedPassword))
            {
                $testuser =  $obj->validatePassword($decryptedPassword, $obj->password);
                if($testuser)
                return $obj;
                else return null;
            }
        } else {
            return null; // Return null if the user is not found
        }

        // return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Validates the password.
     * @param string $password
     * @return bool
     */
    public function validatePassword($password, $password_hash)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $password_hash); // Your column name
    }

    private function decryptAndValidatePassword($encryptedPassword, $storedHash,$publickey)
    {
        $encryptionKey = $publickey; // Must match client-side key
        // echo $encryptedPassword;
        // Decrypt password
        return $decryptedPassword = $this->decryptAES($encryptedPassword, $encryptionKey);
        

        // Validate using Yii2's password validation
        // return $this->validatePassword($decryptedPassword, $storedHash);
    }

    private function decryptAES($encryptedData, $key)
    {
        $key = substr($key, 0, 32); // Ensure exactly 32 bytes
    
        // Decode Base64
        $encryptedData = base64_decode($encryptedData);
        if (!$encryptedData) {
            error_log("Base64 decoding failed");
            return null;
        }
    
        // Extract IV and Ciphertext
        $ivSize = openssl_cipher_iv_length("AES-256-CBC");
        if (strlen($encryptedData) < $ivSize) {
            error_log("Invalid encrypted data length");
            return null;
        }
    
        $iv = substr($encryptedData, 0, $ivSize); // First 16 bytes are the IV
        $cipherText = substr($encryptedData, $ivSize); // Remaining bytes are the ciphertext
    
        // Decrypt
        $decrypted = openssl_decrypt($cipherText, "AES-256-CBC", $key, OPENSSL_RAW_DATA, $iv);
    
        if ($decrypted === false) {
            error_log("Decryption failed: " . openssl_error_string());
        }
    
        return $decrypted ?: null;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->contacts_id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }
    /**
     * This method can be used for token-based authentication (e.g., OAuth2, JWT)
     * If you don't need this functionality, you can return null
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;  // Return null if you don't need token-based authentication
    }
}
