<?php

namespace app\controllers;

use Yii;
use yii\base\DynamicModel;
use yii\web\Controller;
use app\components\Mailer;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Displays contact page.
     *
     * @return string
     */
    public function actionContact()
    {
        $defaultAttributes = ['name' => '', 'email' => '', 'subject' => '', 'body' => '', 'verificationCode' => ''];
        $model = new DynamicModel($defaultAttributes);
        $model->addRule(['name', 'email', 'subject', 'body'], 'required')
            ->addRule(['email'], 'email')
            ->addRule(['verificationCode'], 'captcha');

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            /** @var Mailer $mailer */
            $mailer = Yii::$app->mailer;
            $mailer->sendContactEmail($model);

            Yii::$app->session->setFlash('contactFormSubmitted');
            return $this->refresh();
        }

        return $this->render('contact', compact('model'));
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
