<?php

namespace app\components;

use app\models\User;

class Mailer extends \yii\swiftmailer\Mailer
{
    /**
     * Send confirmation email
     * @param User $user
     */
    public function sendConfirmationEmail($user)
    {
        $this->compose('auth/confirmEmail', ['user' => $user])
            ->setTo($user->email)
            ->setSubject(trans('auth.confirmSubject'))
            ->send();
    }
}