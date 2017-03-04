<?php


// -------------------------------------------------------------
// Application functions
// -------------------------------------------------------------
/**
 * Set env
 * @param array $env
 */
function setEnv($env) {
    foreach ($env as $key => $value) {
        // skip if already set
        if (getenv($key) !== false) {
            continue;
        }

        // set bool/null explicitly, otherwise they get computed as 0 or 1
        if ($value === true) {
            $value = "true";
        } elseif ($value === false) {
            $value = "false";
        } elseif ($value === null) {
            $value = "null";
        }
        putenv("$key=$value");
    }
}

/**
 * Get env
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function env($key, $default = null)
{
    // check if $key is not set
    $value = getenv($key);
    if ($value === false) {
        return $default;
    }

    // return bool/null/value
    if ($value == "true") {
        return true;
    } elseif ($value == "false") {
        return false;
    } elseif ($value == "null") {
        return null;
    } else {
        return $value;
    }
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
 * @param $message
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
    if ($manifest === false) {
        $manifest = null;
        $manifestFile = Yii::getAlias('@app/web') . '/compiled/manifest.json';
        if (file_exists($manifestFile)) {
            $manifest = json_decode(file_get_contents($manifestFile), true);
        }
    }

    // use min file in production
    $min = YII_ENV_PROD ? '.min.' : '.';
    $pathInfo = pathinfo($file);
    $file = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . $min . $pathInfo['extension'];
    return isset($manifest[$file]) ? $manifest[$file] : $file;
}