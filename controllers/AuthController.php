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
     * @inheritdoc
     */
    public function actionForgot()
    {
        $data = parent::actionForgot();
        return $this->render('forgot', $data);
    }

    /**
     * @inheritdoc
     */
    public function actionReset($token)
    {
        $data = parent::actionReset($token);
        if (isset($data['success'])) {
            Yii::$app->session->setFlash('status', trans('auth.resetSuccess'));
            return $this->goHome();
        }
        return $this->render('reset', $data);
    }
}
