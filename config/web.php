<?php

// ------------------------------------------------------------------------
// Main config
// ------------------------------------------------------------------------
$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'timeZone' => 'UTC',
    'language' => 'en-US',
    'params' => require_once __DIR__ . '/params.php',
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            'cookieValidationKey' => env('YII_KEY'),
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
        ],
        'cache' => [
            'class' => 'yii\redis\Cache',
        ],
        'session' => [
            'class' => 'yii\redis\Session',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'forceTranslation' => true,
                ],
            ],
        ],
        'mailer' => [
            'class' => 'app\components\Mailer',
            'viewPath' => '@app/views/emails',
            'useFileTransport' => env('MAIL_FILE_TRANSPORT'),
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => env('MAIL_HOST'),
                'port' => env('MAIL_PORT'),
                'username' => env('MAIL_USER'),
                'password' => env('MAIL_PASS'),
                'encryption' => env('MAIL_ENCRYPTION'),
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => env('DB_DSN'),
            'username' => env('DB_USER'),
            'password' => env('DB_PASS'),
            'tablePrefix' => env('DB_TABLE_PREFIX'),
            'charset' => 'utf8',
            'enableSchemaCache' => YII_ENV_PROD,
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
    ],
];

// ------------------------------------------------------------------------
// Debug and gii
// ------------------------------------------------------------------------
$debugModule = 'amnah\yii2\debug\Module';
if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => $debugModule,
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*'],
    ];
} elseif (isDebugEnabled()) {
    // enable debug for current ip
    $userIp = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => $debugModule,
        'allowedIPs' => [$userIp],
    ];
}


return $config;