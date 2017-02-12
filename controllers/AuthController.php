<?php

namespace app\controllers;

use Yii;
use yii\base\DynamicModel;
use yii\filters\AccessControl;
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
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    ['allow' => true, 'roles' => ['@'], 'actions' => ['logout']],
                    ['allow' => true, 'roles' => ['?']],
                ],
            ],
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
            $user = User::findOne([$field => trim($model->email)]);
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
     * @param array|string $redirectUrl
     * @return \yii\web\Response
     */
    protected function performLogin($user, $rememberMe = true, $redirectUrl = null)
    {
        $duration = $rememberMe ? 2592000 : 0; // 30 days
        Yii::$app->user->login($user, $duration);
        return $redirectUrl ? $this->redirect($redirectUrl) : $this->goBack();
    }

    /**
     * Register
     */
    public function actionRegister()
    {
        $user = new User;
        $user->setScenario(User::SCENARIO_REGISTER);
        if ($user->loadPostAndSave()) {

            // log user in directly
            //return $this->performLogin($user);

            // send confirmation email
            $user->setConfirmationToken()->save(false);
            mailer()->sendConfirmationEmail($user);

            return $this->render('registered', [
                'user' => $user
            ]);
        }

        return $this->render('register', [
            'user' => $user,
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
            $user->clearConfirmationToken()->save(false);
            Yii::$app->session->setFlash('status', trans('auth.confirmed'));
            return $this->performLogin($user, true, '/');
        }

        return $this->render('confirm');
    }
}
