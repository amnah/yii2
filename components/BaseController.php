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
     * @var string Response format
     */
    protected $responseFormat = 'json';

    /**
     * @inheritdoc
     */
    public function init()
    {
        // set response format
        Yii::$app->response->format = $this->responseFormat;

        // set json pretty print in debug mode
        Yii::$app->response->formatters['json'] = [
            'class' => 'yii\web\JsonResponseFormatter',
            'prettyPrint' => YII_DEBUG,
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
                'class' => 'yii\filters\Cors',
            ],
            */

            // rate limiter
            /*
            'rateLimiter' => [
                'class' => 'yii\filters\RateLimiter',
            ],
            */
        ];
    }
}