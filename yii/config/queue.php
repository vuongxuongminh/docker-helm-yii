<?php

return [
    'class' => 'yii\queue\amqp_interop\Queue',
    'host' => $_SERVER['RABBITMQ_HOST'],
    'port' => $_SERVER['RABBITMQ_PORT'],
    'user' => $_SERVER['RABBITMQ_USER'],
    'password' => $_SERVER['RABBITMQ_PASSWORD'],
    'queueName' => $_SERVER['RABBITMQ_QUEUE'] ?? 'default',
];