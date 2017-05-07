<?php

namespace app\components;

use Yii;
use yii\di\Instance;
use yii\web\Controller;

class BaseController extends Controller
{
    /**
     * @var array Csrf exceptions. Should be the action id, eg, `actionLogin` => 'login'
     */
    protected $csrfExceptions = [];

    /**
     * @var bool Check for authorized user
     */
    protected $checkAuth = true;

    /**
     * @var string Response format
     */
    protected $responseFormat = 'json';

    /**
     * @var ApiAuth ApiAuth instance
     */
    protected $apiAuth = 'apiAuth';

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

        $this->apiAuth = Instance::ensure($this->apiAuth, ApiAuth::className());
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        // return false for CORS preflight OPTIONS (this prevents the action from running)
        // @link https://github.com/yiisoft/yii2/pull/8626/files
        // @link https://github.com/yiisoft/yii2/issues/6254
        if (Yii::$app->getRequest()->getIsOptions()) {
            return false;
        }

        // disable csrf
        // note: this needs to be called before calling parent::beforeAction()
        $exceptions = array_fill_keys($this->csrfExceptions, 1);
        if (isset($exceptions[$action->id])) {
            $this->enableCsrfValidation = false;
        }

        // call parent
        return parent::beforeAction($action);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        // set base behaviors
        $behaviors = [
            //'corsFilter' => 'yii\filters\Cors',
        ];

        // add auth behaviors
        if ($this->checkAuth) {
            $behaviors['apiAuth'] = $this->apiAuth;
            //$behaviors['rateLimiter'] = 'yii\filters\RateLimiter';
        }

        return $behaviors;
    }
}