<?php

namespace app\components;

use Yii;
use yii\web\Controller;
use yii\web\UnauthorizedHttpException;

class BaseController extends Controller
{
    /**
     * @var bool Check for authorized user
     */
    protected $checkAuth = true;

    /**
     * @inheritdoc
     */
    public function init()
    {
        // set json output and use "pretty" output in debug mode
        Yii::$app->response->format = 'json';
        Yii::$app->response->formatters['json'] = [
            'class' => 'yii\web\JsonResponseFormatter',
            'prettyPrint' => YII_DEBUG, // use "pretty" output in debug mode
        ];

        // check auth
        if ($this->checkAuth && !Yii::$app->user->id) {
            throw new UnauthorizedHttpException;
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        // check for CORS preflight OPTIONS. if so, then return false so that it doesn't run
        // the controller action
        // @link https://github.com/yiisoft/yii2/pull/8626/files
        // @link https://github.com/yiisoft/yii2/issues/6254
        if (Yii::$app->request->isOptions) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [

            // cors filter
            /*
            'corsFilter' => [
                "class" => Cors::className(),
            ],
            */

            // rate limiter
            /*
            'rateLimiter' => [
                'class' => RateLimiter::className(),
            ],
            */
        ];
    }
}