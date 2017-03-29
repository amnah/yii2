<?php

namespace app\components;

use Yii;

class UrlManager extends \yii\web\UrlManager
{
    /**
     * @var array Routes that should be processed through yii instead of the frontend client
     *            These must appear at the beginning of the string
     */
    public $yiiRoutes = [
        "v1",      // api v1
        "debug",   // debug module
        "gii",     // gii module
    ];

    /**
     * @var string Frontend client route
     */
    public $frontendRoute = "app/index";

    /**
     * @inheritdoc
     */
    public function parseRequest($request)
    {
        // check if we're calling a route that should be processed by yii
        $pathInfo = $request->getPathInfo();
        foreach ($this->yiiRoutes as $yiiRoute) {
            // check for direct route or a prefixed route, eg, "gii" or "gii/*"
            $yiiRoutePrefix = "$yiiRoute/";
            if ($pathInfo === $yiiRoute || strpos($pathInfo, $yiiRoutePrefix) === 0) {
                return parent::parseRequest($request);
            }
        }

        // use frontend route
        $request->setPathInfo($this->frontendRoute);
        return parent::parseRequest($request);
    }
}