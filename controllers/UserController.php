<?php

namespace app\controllers;

use Yii;
use app\controllers\v1\UserController as BaseUserController;

class UserController extends BaseUserController
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
    public function actionProfile()
    {
        $data = parent::actionProfile();
        return $this->render('profile', $data);
    }
}
