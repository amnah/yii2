<?php

namespace app\controllers;

use Yii;
use app\controllers\v1\PublicController;

class SiteController extends PublicController
{
    /**
     * @inheritdoc
     */
    protected $checkAuth = false;

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
    public function actions()
    {
        $actions = parent::actions();
        $actions['error'] = [
            'class' => 'yii\web\ErrorAction',
        ];
        return $actions;
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
        $data = parent::actionContact();
        return $this->render('contact', $data);
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
