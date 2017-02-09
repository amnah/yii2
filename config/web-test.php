<?php

$config = require_once __DIR__ . '/web.php';

$config['id'] .= '_test';
$config['components']['mailer']['useFileTransport'] = true;
$config['components']['db']['dsn'] .= "_test";
$config['components']['assetManager']['basePath'] = __DIR__ . '/../web/assets';

$config['components']['urlManager']['showScriptName'] = true;
$config['components']['request']['cookieValidationKey'] = 'test';
$config['components']['request']['enableCsrfValidation'] = 'false';

// but if you absolutely need it set cookie domain to localhost
/*
$config['components']['request']['csrfCookie'] = [
    'domain' => 'localhost',
];
*/

return $config;