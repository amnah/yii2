<?php

$config = require_once __DIR__ . '/console.php';

$config['id'] .= '_test';
$config['components']['mailer']['useFileTransport'] = true;
$config['components']['db']['dsn'] .= "_test";
$config['components']['assetManager']['basePath'] = __DIR__ . '/../web/assets';

$config['components']['urlManager']['showScriptName'] = true;

return $config;