<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;
use app\components\BaseModel;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property int $id
 * @property string $email
 * @property string $username
 * @property string $password
 * @property string $confirmation
 * @property string $auth_key
 * @property string $created_at
 * @property string $updated_at
 *
 * @property PasswordReset[] $passwordResets
 */
class User extends BaseModel implements IdentityInterface
{
    const SCENARIO_REGISTER = 'register';
    const SCENARIO_RESET = 'reset';
    const SCENARIO_PROFILE = 'profile';

    /**
     * @var string
     */
    public $confirm_password;

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            static::SCENARIO_REGISTER => ['email', 'username', 'password', 'confirm_password'],
            static::SCENARIO_RESET => ['password', 'confirm_password'],
            static::SCENARIO_PROFILE=> ['username'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'username', 'password'], 'string', 'max' => 255],
            [['email', 'username', 'password'], 'required'],
            [['email', 'username'], 'trim'],
            [['email'], 'unique'],
            [['username'], 'unique'],
            [['username'], 'string', 'min' => 2],
            [['username'], 'match', 'pattern' => '/^[A-Za-z0-9_]+$/', 'message' => trans('auth.alphanumeric')],
            [['password'], 'string', 'min' => 3],
            [['password'], 'compare', 'compareAttribute' => 'confirm_password'],
            [['confirm_password'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => trans('ID'),
            'email' => trans('Email'),
            'username' => trans('Username'),
            'password' => trans('Password'),
            'confirmation' => trans('Confirmation'),
            'auth_key' => trans('Auth Key'),
            'created_at' => trans('Created At'),
            'updated_at' => trans('Updated At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return ["id", "email", "username"];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasswordResets()
    {
        return $this->hasMany(PasswordReset::className(), ['user_id' => 'id'])->inverseOf('user');
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
        //return static::findOne(["access_token" => $token]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
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
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($this->isNewRecord) {
            $this->auth_key = Yii::$app->getSecurity()->generateRandomString();
        }
        if ($this->getDirtyAttributes(['password']) && $this->password) {
            $this->password = Yii::$app->getSecurity()->generatePasswordHash($this->password);
        }
        return true;
    }

    /**
     * Clear password (for form in password reset page)
     * @return static
     */
    public function clearPassword()
    {
        $this->password = '';
        return $this;
    }

    /**
     * Validate password
     * @param string $password
     * @return bool
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Set confirmation token
     * @return static
     */
    public function setConfirmationToken()
    {
        $this->confirmation = Yii::$app->getSecurity()->generateRandomString();
        $this->save(false);
        return $this;
    }

    /**
     * Clear confirmation token
     * @return static
     */
    public function clearConfirmationToken()
    {
        $this->confirmation = null;
        $this->save(false);
        return $this;
    }
}
