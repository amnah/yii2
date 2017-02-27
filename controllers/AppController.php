<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

class AppController extends Controller
{
    public $layout = false;

    public function actionIndex()
    {
        return $this->render('index');
    }
}
