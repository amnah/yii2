<?php

namespace app\components;

use app\models\PasswordReset;
use app\models\User;

class Mailer extends \yii\swiftmailer\Mailer
{
    /**
     * Send confirmation email
     * @param User $user
     * @return bool
     */
    public function sendConfirmationEmail($user)
    {
        return $this->compose('auth/confirmEmail', ['user' => $user])
            ->setTo($user->email)
            ->setSubject(trans('auth.confirmSubject'))
            ->send();
    }

    /**
     * Send reset email
     * @param PasswordReset $passwordReset
     * @return bool
     */
    public function sendResetEmail($passwordReset)
    {
        return $this->compose('auth/resetPassword', ['passwordReset' => $passwordReset])
            ->setTo($passwordReset->user->email)
            ->setSubject(trans('auth.resetSubject'))
            ->send();
    }
}