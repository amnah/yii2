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
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        // create ad hoc model for validation
        $defaultAttributes = ['email' => '', 'password' => '', 'rememberMe' => true];
        $model = new DynamicModel($defaultAttributes);
        $model->addRule(['email', 'password'], 'required')->addRule(['rememberMe'], 'boolean');

        /** @var User $user */
        // validate data and check user/password
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = User::find()
                ->orWhere(["email" => $model->email])
                ->orWhere(["username" => $model->email])
                ->one();
            if (!$user || !$user->validatePassword($model->password)) {
                $model->addError('email', Yii::t('app', 'Invalid credentials'));
            }

            if (!$model->hasErrors()) {
                $duration = $model->rememberMe ? 2592000 : 0; // 30 days
                Yii::$app->user->login($user, $duration);
                return $this->goBack();
            }
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
