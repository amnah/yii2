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
    protected $csrfExceptions = ['logout-api', 'login-api'];

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
     * Logout for api
     */
    public function actionLogoutApi()
    {
        // logout, remove token, and remove expired tokens for user
        Yii::$app->user->logout(); // just in case
        $token = $this->apiAuth->getTokenFromHeader(Yii::$app->request);
        $userId = $this->apiAuth->removeToken($token);
        $this->apiAuth->getUserTokens($userId);
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
            return ['success' => true, 'user' => $user];
        }
        return ['error' => 'Not logged in'];
    }

    /**
     * Check auth status for api
     */
    public function actionCheckAuthApi()
    {
        $token = $this->apiAuth->getTokenFromHeader(Yii::$app->request);
        $user = $this->apiAuth->getUserByToken($token);
        if ($user) {
            return ['success' => true, 'user' => $user];
        }
        return ['error' => 'Not logged in'];
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
     * Login for api (get a bearer token instead of logging in via session/cookie)
     */
    public function actionLoginApi()
    {
        // disable session so nothing gets saved to session/cookie
        Yii::$app->user->enableSession = false;

        // return any errors
        $data = $this->actionLogin();
        if (empty($data['success'])) {
            return $data;
        }

        // remove expired tokens for user and then create new token
        $this->apiAuth->getUserTokens($data['user']->id);
        $data["token"] = $this->apiAuth->createTokenForUser($data['user']);
        return $data;
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
            // clear confirmation, consume $passwordReset, and login
            $user->clearConfirmationToken();
            $passwordReset->consume();
            return $this->performLogin($user);
        }

        return ['errors' => $user->errors, 'user' => $user];
    }
}
