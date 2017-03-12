<?php

namespace app\components;

use Yii;
use yii\base\DynamicModel;
use yii\swiftmailer\Mailer as BaseMailer;
use app\models\PasswordReset;
use app\models\User;

class Mailer extends BaseMailer
{
    /**
     * Send contact email
     * @param DynamicModel $contactForm
     * @return bool
     */
    public function sendContactEmail($contactForm) {
        return $this->compose()
            ->setTo(Yii::$app->params['adminEmail'])
            ->setFrom([$contactForm->email => $contactForm->name])
            ->setSubject($contactForm->subject)
            ->setTextBody($contactForm->body)
            ->send();
    }

    /**
     * Send confirmation email
     * @param User $user
     * @param bool $viaApi
     * @return bool
     */
    public function sendConfirmationEmail($user, $viaApi = false)
    {
        $baseUrl = $viaApi ? 'app#/confirm' : 'auth/confirm';
        $confirmUrl = url([$baseUrl, 'email' => $user->email, 'confirmation' => $user->confirmation], true);
        return $this->compose('auth/confirmEmail', compact('user', 'confirmUrl'))
            ->setTo($user->email)
            ->setSubject(trans('auth.confirmSubject'))
            ->send();
    }

    /**
     * Send reset email
     * @param PasswordReset $passwordReset
     * @param bool $viaApi
     * @return bool
     */
    public function sendResetEmail($passwordReset, $viaApi = false)
    {
        $baseUrl = $viaApi ? 'app#/reset' : 'auth/reset';
        $resetUrl = url([$baseUrl, 'token' => $passwordReset->token], true);
        return $this->compose('auth/resetPassword', compact('passwordReset', 'resetUrl'))
            ->setTo($passwordReset->user->email)
            ->setSubject(trans('auth.resetSubject'))
            ->send();
    }
}