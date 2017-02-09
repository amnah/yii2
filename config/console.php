<?php

$config = require_once __DIR__ . '/web.php';

$config['id'] .= '_console';
$config['controllerNamespace'] = 'app\commands';

unset($config['components']['request']);
unset($config['components']['log']['traceLevel']);
unset($config['components']['user']);
unset($config['components']['errorHandler']);

/*
Yii::setAlias('@tests', dirname(__DIR__) . '/tests');
$config['controllerMap'] = [
    'fixture' => [ // Fixture generation command line.
        'class' => 'yii\faker\FixtureController',
    ],
];
*/

return $config;