<?php

namespace app\models;

use Yii;

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
class User extends \app\components\BaseModel implements \yii\web\IdentityInterface
{
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'email' => Yii::t('app', 'Email'),
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
            'confirmation' => Yii::t('app', 'Confirmation'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
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
        if (isset($this->getDirtyAttributes()['password'])) {
            $this->password = Yii::$app->getSecurity()->generatePasswordHash($this->password);
        }
        return true;
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
        return $this;
    }

    /**
     * Confirm email address by emptying the field
     */
    public function confirmEmail()
    {
        $this->confirmation = null;
        $this->save();
    }
}
