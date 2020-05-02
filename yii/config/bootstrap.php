<?php

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

defined('YII_DEBUG') or define('YII_DEBUG', $_SERVER['YII_DEBUG'] === 'true');
defined('YII_ENV') or define('YII_ENV', $_SERVER['YII_ENV']);
