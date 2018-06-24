<?php

namespace common\models;

use Yii;
use yii\filters\RateLimitInterface;
use yii\base\NotSupportedException;
/**
 * This is the model class for table "adminuser".
 *
 * @property int $id
 * @property string $username
 * @property string $nickname
 * @property string $password
 * @property string $email
 * @property string $profile
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property int $allowance
 * @property int $allowance_updated_at
 */
class Adminuser extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface, RateLimitInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'adminuser';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['username', 'nickname', 'password', 'email', 'auth_key', 'password_hash'], 'required'],
            [['expire_at', 'allowance', 'allowance_updated_at'], 'integer'],
            [['profile'], 'string'],
            [['username', 'nickname', 'password', 'email'], 'string', 'max' => 128],
            [['auth_key'], 'string', 'max' => 32],
            [['password_hash', 'password_reset_token', 'access_token'], 'string', 'max' => 255],
            [['access_token'], 'unique'],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户名',
            'nickname' => '昵称',
            'password' => '密码',
            'email' => 'Email',
            'profile' => '简介',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'access_token' => '访问token',
            'expire_at' => '过期时间',
            'allowance' => 'Allowance',
            'allowance_updated_at' => 'Allowance Updated At'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        //throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
        return static::find()
            ->where(['access_token'=>$token])
            ->andWhere(['>','expire_at',time()])
            ->one();
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            //'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString();
        return $this->access_token;
    }

    public function getRateLimit($request, $action)
    {
        return [3,1];  //每秒只能请求3次
    }

    public function loadAllowance($request, $action)
    {
        return [$this->allowance,$this->allowance_updated_at];
    }

    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        $this->allowance = $allowance;
        $this->allowance_updated_at = $timestamp;
        $this->save();
    }
}
