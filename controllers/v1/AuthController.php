<?php

namespace app\controllers\v1;

use Yii;
use yii\base\DynamicModel;
use yii\filters\VerbFilter;
use app\components\BaseController;
use app\components\Mailer;
use app\models\PasswordReset;
use app\models\User;

class AuthController extends BaseController
{
    /**
     * @inheritdoc
     */
    protected $checkAuth = false;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'logout' => ['post'],
            ],
        ];
        return $behaviors;
    }

    /**
     * Logout
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return ['success' => true];
    }

    /**
     * Check auth status
     */
    public function actionCheckAuth()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;
        if ($user) {
            return ["success" => true, "user" => $user];
        }
        return ["error" => "Not logged in"];
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
                $model->addError('email', trans('auth.loginFailed'));
            } elseif ($user->confirmation) {
                $model->addError('email', trans('auth.unconfirmed'));
            } else {
                return $this->performLogin($user, $model->rememberMe);
            }
        }

        return ['errors' => $model->errors, 'model' => $model];
    }

    /**
     * Perform login
     * @param User $user
     * @param bool $rememberMe
     * @return array
     */
    protected function performLogin($user, $rememberMe = true)
    {
        $duration = $rememberMe ? 2592000 : 0; // 30 days
        Yii::$app->user->login($user, $duration);
        return ['success' => true, 'user' => $user];
    }

    /**
     * Register
     */
    public function actionRegister()
    {
        $user = new User;
        $user->setScenario(User::SCENARIO_REGISTER);
        if ($user->loadPostAndSave()) {
            // send confirmation email
            /** @var Mailer $mailer */
            $user->setConfirmationToken();
            $mailer = Yii::$app->mailer;
            $mailer->sendConfirmationEmail($user, Yii::$app->request->isAjax);
            return ['success' => true, 'user' => $user];
        }

        return ['errors' => $user->errors, 'user' => $user];
    }

    /**
     * Confirm
     */
    public function actionConfirm($email, $confirmation)
    {
        // find and confirm user
        $user = User::findOne(['email' => $email, 'confirmation' => $confirmation]);
        if ($user) {
            $user->clearConfirmationToken();
            return $this->performLogin($user, true);
        }

        return ['error' => trans('auth.invalidToken')];
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
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = User::findOne(['email' => trim($model->email)]);
            if (!$user) {
                $model->addError('email', trans('auth.forgotFailed'));
            } else {
                /** @var Mailer $mailer */
                $passwordReset = PasswordReset::setTokenForUser($user->id);
                $mailer = Yii::$app->mailer;
                $mailer->sendResetEmail($passwordReset, Yii::$app->request->isAjax);
                return ['success' => true, 'model' => $model];
            }
        }

        return ['errors' => $model->errors, 'model' => $model];
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
            return ['error' => trans('auth.invalidToken')];
        }

        $user = $passwordReset->user;
        $user->clearPassword()->setScenario(User::SCENARIO_RESET);
        if ($user->loadPostAndSave()) {
            // consume $passwordReset and login
            $passwordReset->consume();
            return $this->performLogin($user);
        }

        return ['errors' => $user->errors, 'user' => $user];
    }
}
