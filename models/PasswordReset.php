<?php

namespace app\models;

use Yii;
use app\components\BaseModel;

/**
 * This is the model class for table "{{%password_reset}}".
 *
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property string $created_at
 * @property string $consumed_at
 *
 * @property User $user
 */
class PasswordReset extends BaseModel
{
    /**
     * @var int Number of minutes before token expires
     */
    const EXPIRE_MINUTES = 60;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['timestamp']['updatedAtAttribute'] = false;
        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => trans('ID'),
            'user_id' => trans('User ID'),
            'token' => trans('Token'),
            'created_at' => trans('Created At'),
            'consumed_at' => trans('Consumed At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id'])->inverseOf('passwordResets');
    }

    /**
     * Get model by token
     * @param string $token
     * @return static
     */
    public static function getByToken($token)
    {
        // check for token that hasn't been consumed and hasn't expired yet
        /** @var static $model */
        $seconds = static::EXPIRE_MINUTES * 60;
        $createdAfter = date('Y-m-d H:i:s', time()-$seconds);
        $model = static::find()
            ->where(['token' => $token])
            ->andWhere(['consumed_at' => null])
            ->andWhere(['>=', 'created_at', $createdAfter])
            ->one();
        return $model;
    }

    /**
     * Update or create token for user
     * @param int $userId
     * @return static
     */
    public static function setTokenForUser($userId)
    {
        return static::updateOrCreate([
            'user_id' => $userId,
            'consumed_at' => null,
        ], [
            'token' => Yii::$app->security->generateRandomString(),
            'created_at' => static::getTimestampValue(),
        ]);
    }

    /**
     * Consume password reset
     */
    public function consume()
    {
        $this->consumed_at = $this->getTimestampValue();
        $this->save(false);
        return $this;
    }
}
