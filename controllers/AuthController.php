<?php

namespace app\controllers;

use Yii;
use yii\base\DynamicModel;
use yii\filters\VerbFilter;
use yii\web\Controller;
use app\models\User;

class AuthController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Logout
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Login
     */
    public function actionLogin()
    {
        $defaultAttributes = ['email' => '', 'password' => '', 'rememberMe' => true];
        $model = new DynamicModel($defaultAttributes);
        $model->addRule(['email', 'password'], 'required')
            ->addRule(['rememberMe'], 'boolean');

        // find user, validate password, and check if user is confirmed
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $field = filter_var($model->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            $user = User::findOne([$field => $model->email]);
            if (!$user || !$user->validatePassword($model->password)) {
                $model->addError('email', trans('auth.failed'));
            } elseif ($user->confirmation) {
                $model->addError('email', trans('auth.unconfirmed'));
            } else {
                return $this->performLogin($user, $model->rememberMe);
            }
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Perform login
     * @param User $user
     * @param bool $rememberMe
     * @return \yii\web\Response
     */
    protected function performLogin($user, $rememberMe = true)
    {
        $duration = $rememberMe ? 2592000 : 0; // 30 days
        Yii::$app->user->login($user, $duration);
        return $this->goBack();
    }

    /**
     * Register
     */
    public function actionRegister()
    {
        $defaultAttributes = ['email' => '', 'username' => '', 'password' => '', 'confirm_password' => ''];
        $model = new DynamicModel($defaultAttributes);
        $model->addRule(['email', 'username', 'password', 'confirm_password'], 'required')
            ->addRule(['email', 'username'], 'trim')
            ->addRule(['email'], 'unique', ['targetClass' => User::className()])
            ->addRule(['username'], 'unique', ['targetClass' => User::className()])
            ->addRule(['username'], 'match', ['pattern' => '/^[A-Za-z0-9_]+$/', 'message' => trans('auth.alphanumeric')])
            ->addRule(['password'], 'string', ['min' => 3])
            ->addRule(['password'], 'compare', ['compareAttribute' => 'confirm_password']);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $attributes = $model->getAttributes(['email', 'username', 'password']);
            $user = new User($attributes);
            return $this->performRegistration($user);
        }

        return $this->render('register', [
            'model' => $model,
        ]);
    }

    /**
     * Perform registration
     * @param User $user
     * @return string
     */
    protected function performRegistration($user)
    {
        // log in directly
//        $user->save();
//        return $this->performLogin($user);

        // send confirmation email
        $user->setConfirmationToken()->save();
        Yii::$app->mailer->compose('auth/confirmEmail', ['user' => $user])
            ->setTo($user->email)
            ->setSubject(trans('auth.confirmSubject'))
            ->send();

        return $this->render('registered', [
            'user' => $user
        ]);
    }

    /**
     * Confirm
     */
    public function actionConfirm($email, $confirmation)
    {
        // find and confirm user
        $user = User::findOne(['email' => $email, 'confirmation' => $confirmation]);
        if ($user) {
            $user->confirmEmail();
            Yii::$app->session->setFlash('status', trans('auth.confirmed'));
            return $this->performLogin($user);
        }

        return $this->render('confirm');
    }
}
