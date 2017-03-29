<?php

namespace app\controllers;

use Yii;
use app\controllers\v1\UserController as BaseUserController;

class UserController extends BaseUserController
{
    /**
     * @inheritdoc
     */
    protected $responseFormat = 'html';

    /**
     * @inheritdoc
     */
    public function actionProfile()
    {
        $data = parent::actionProfile();
        return $this->render('profile', $data);
    }
}
