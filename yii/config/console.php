<?php

$params = require __DIR__.'/params.php';
$db = require __DIR__.'/db.php';
$log = require __DIR__ . '/log.php';
$mailer = require __DIR__ . '/mailer.php';
$queue = require __DIR__ . '/queue.php';

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'queue'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
        '@tests' => '@app/tests',
        '@webroot' => '@app/web',
        '@web' => '/',
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => $log,
        'db' => $db,
        'queue' => $queue,
        'mailer' => $mailer
    ],
    'params' => $params,
    'modules' => [
        'staticAssets' => [
            'class' => 'SamIT\Yii2\StaticAssets\Module',
            'baseUrl' => '/',
            'excludedPatterns' => [
                '*/tests/*',
                '*/vendor/aws/*',
                '*/vendor/cebe/markdown*',
                '*/vendor/codeception/*',
                '*/vendor/composer/*',
                '*/vendor/docker-php/*',
                '*/vendor/jane-php/*',
                '*/vendor/league/*',
                '*/vendor/monolog/*',
                '*/vendor/myclabs/*',
                '*/vendor/opis/*',
                '*/vendor/php-http/*',
                '*/vendor/phpspec/*',
                '*/vendor/phpunit/*',
                '*/vendor/psr/*',
                '*/vendor/sebastian*',
                '*/vendor/setasign*',
                '*/vendor/symfony*',
                '*/vendor/yiisoft/yii2-composer*',
                '*/vendor/yiisoft/yii2-httpclient*',
            ],
        ],
    ],
    /*
    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    */
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
