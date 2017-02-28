<?php

namespace app\controllers;

use Yii;
use yii\base\DynamicModel;
use app\components\Mailer;
use app\controllers\v1\AuthController as BaseAuthController;
use app\models\PasswordReset;
use app\models\User;

class AuthController extends BaseAuthController
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        Yii::$app->response->format = 'html';
    }

    /**
     * @inheritdoc
     */
    public function actionLogout()
    {
        parent::actionLogout();
        return $this->goHome();
    }

    /**
     * @inheritdoc
     */
    public function actionLogin()
    {
        $data = parent::actionLogin();
        if (isset($data['success'])) {
            return $this->goBack();
        }
        return $this->render('login', $data);
    }

    /**
     * Register
     * @return string
     */
    public function actionRegister()
    {
        $data = parent::actionRegister();
        if (isset($data['success'])) {
            return $this->render('registered', $data);
        }
        return $this->render('register', $data);
    }

    /**
     * @inheritdoc
     */
    public function actionConfirm($email, $confirmation)
    {
        $data = parent::actionConfirm($email, $confirmation);
        if (isset($data['success'])) {
            Yii::$app->session->setFlash('status', trans('auth.confirmed'));
            return $this->goHome();
        }
        return $this->render('confirm');
    }

    /**
     * Forgot password
     * @return string
     */
    public function actionForgot()
    {
        $defaultAttributes = ['email' => ''];
        $model = new DynamicModel($defaultAttributes);
        $model->addRule(['email'], 'required')
            ->addRule(['email'], 'email');

        // find user and generate $passwordReset token
        $user = null;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = User::findOne(['email' => trim($model->email)]);
            if (!$user) {
                $model->addError('email', trans('auth.forgotFailed'));
            } else {
                /** @var Mailer $mailer */
                $passwordReset = PasswordReset::setTokenForUser($user->id);
                $mailer = Yii::$app->mailer;
                $mailer->sendResetEmail($passwordReset);
            }
        }

        return $this->render('forgot', compact('model', 'user'));
    }

    /**
     * Reset password
     * @param string $token
     * @return string
     */
    public function actionReset($token)
    {
        $passwordReset = PasswordReset::getByToken($token);
        if (!$passwordReset) {
            return $this->render('reset');
        }

        $passwordReset->user->clearPassword()->setScenario(User::SCENARIO_RESET);
        if ($passwordReset->user->loadPostAndSave()) {
            // consume $passwordReset and login
            $passwordReset->consume();
            Yii::$app->session->setFlash('status', trans('auth.resetSuccess'));
            return $this->performLogin($passwordReset->user);
        }

        return $this->render('reset', compact('passwordReset'));
    }
}
