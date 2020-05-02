<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => sprintf('mysql:host=%s;port=%s;dbname=%s', $_SERVER['DB_HOST'], $_SERVER['DB_PORT'], $_SERVER['DB_DATABASE']),
    'username' => $_SERVER['DB_USERNAME'],
    'password' => $_SERVER['DB_PASSWORD'],
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
