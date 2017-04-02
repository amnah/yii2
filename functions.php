<?php


// -------------------------------------------------------------
// Application functions
// -------------------------------------------------------------
/**
 * Get env
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function env($key, $default = null)
{
    static $env;
    if ($env === null) {
        $env = require_once __DIR__ . '/env.php';
    }
    return array_key_exists($key, $env) ? $env[$key] : $default;
}

/**
 * Check if we force enable yii debug module
 * @return bool
 */
function isDebugEnabled()
{
    // store/return result
    static $result;
    if ($result !== null) {
        return $result;
    }

    // force debug module using $_GET param
    // enable this by manually entering the url "http://example.com?qwe"
    $debugPassword = env('DEBUG_PASSWORD');
    $cookieName    = '_forceDebug';
    $cookieExpire  = YII_ENV_PROD ? 60*15 : 60*60*24; // 15 minutes for production, 24 hrs for everything else

    // check $_GET and $_COOKIE
    $isGetSet = isset($_GET[$debugPassword]);
    $isCookieSet = (isset($_COOKIE[$cookieName]) && $_COOKIE[$cookieName] === $debugPassword);
    if ($debugPassword && ($isGetSet || $isCookieSet)) {
        // set/refresh cookie
        setcookie($cookieName, $debugPassword, time() + $cookieExpire);
        $result = true;
    } else {
        $result = false;
    }
    return $result;
}

// -------------------------------------------------------------
// Helper functions
// -------------------------------------------------------------
/**
 * Get url
 * @param array|string $url
 * @param bool|string $scheme
 * @return string
 */
function url($url = '', $scheme = false)
{
    return \yii\helpers\Url::to($url, $scheme);
}

/**
 * Translate message
 * @param string $message
 * @param array $params
 * @return string
 */
function trans($message, $params = [])
{
    return Yii::t('app', $message, $params);
}

/**
 * Compute asset url based on manifest file
 * @param string $file
 * @return string
 */
function assetUrl($file) {

    // use regular file in development
    if (YII_ENV_DEV) {
        return $file;
    }

    // get manifest data
    static $manifest = false;
    $webPath = Yii::getAlias('@app/web');
    if ($manifest === false) {
        $manifest = null;
        $manifestFile = "$webPath/compiled/manifest.json";
        if (file_exists($manifestFile)) {
            $manifest = json_decode(file_get_contents($manifestFile), true);
        }
    }

    // use min file in production
    $min = YII_ENV_PROD ? '.min.' : '.';
    $pathInfo = pathinfo($file);
    $file = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . $min . $pathInfo['extension'];

    // check if file exists
    if (isset($manifest[$file]) && file_exists("$webPath{$manifest[$file]}")) {
        return $manifest[$file];
    }
    return $file;
}