<?php

namespace app\models;

use Yii;

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
class PasswordReset extends \app\components\BaseModel
{
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
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'token' => Yii::t('app', 'Token'),
            'created_at' => Yii::t('app', 'Created At'),
            'consumed_at' => Yii::t('app', 'Consumed At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id'])->inverseOf('passwordResets');
    }
}
