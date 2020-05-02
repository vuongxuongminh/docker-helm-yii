<?php

$monolog = new Monolog\Logger('app_logger');
$rotatingFileHandler = new Monolog\Handler\RotatingFileHandler(
    dirname(__DIR__).'/runtime/logs/app.log',
    14,
    \Monolog\Logger::DEBUG
);
$rotatingFileHandler->setFormatter(new Monolog\Formatter\JsonFormatter());
$monolog->pushHandler($rotatingFileHandler);

return [
    'traceLevel' => YII_DEBUG ? 3 : 0,
    'targets' => [
        [
            'class' => 'samdark\log\PsrTarget',
            'logger' => $monolog,
            'levels' => ['error', 'warning'],
        ],
    ],
];