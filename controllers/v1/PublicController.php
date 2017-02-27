<?php

namespace app\controllers\v1;

use Yii;
use app\components\BaseController;
use yii\base\DynamicModel;
use app\components\Mailer;

class PublicController extends BaseController
{
    /**
     * @inheritdoc
     */
    protected $checkAuth = false;

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Contact
     */
    public function actionContact()
    {
        $defaultAttributes = ['name' => '', 'email' => '', 'subject' => '', 'body' => '', 'verificationCode' => ''];
        $model = new DynamicModel($defaultAttributes);
        $model->addRule(['name', 'email', 'subject', 'body'], 'required')
            ->addRule(['email'], 'email')
            ->addRule(['verificationCode'], 'captcha', ['captchaAction' => $this->getUniqueId() . '/captcha']);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            /** @var Mailer $mailer */
            $mailer = Yii::$app->mailer;
            $mailer->sendContactEmail($model);
            return ["success" => true];
        }

        return ["errors" => $model->errors, "model" => $model];
    }
}
