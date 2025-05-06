<?php
declare(strict_types=1);

use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;

return [
    'App' => [
        'namespace' => 'TestApp',
        'encoding' => 'UTF-8',
        'defaultLocale' => 'en_US',
        'defaultTimezone' => 'UTC',
        'base' => false,
        'dir' => 'src',
        'webroot' => 'webroot',
        'wwwRoot' => WWW_ROOT,
        'fullBaseUrl' => false,
        'imageBaseUrl' => 'img/',
        'cssBaseUrl' => 'css/',
        'jsBaseUrl' => 'js/',
        'paths' => [
            'plugins' => [dirname(dirname(__DIR__))],
            'templates' => [APP . 'Template' . DS],
            'locales' => [APP . 'Locale' . DS],
        ],
    ],
    'Security' => [
        'salt' => 'test-salt-for-testing',
    ],
    'Datasources' => [
        'default' => [
            'className' => Connection::class,
            'driver' => Mysql::class,
            'persistent' => false,
            'host' => 'localhost',
            'username' => 'root',
            'password' => '',
            'database' => 'test_openapi_theme',
            'encoding' => 'utf8mb4',
            'timezone' => 'UTC',
            'cacheMetadata' => true,
        ],
        'test' => [
            'className' => Connection::class,
            'driver' => Mysql::class,
            'persistent' => false,
            'host' => 'localhost',
            'username' => 'root',
            'password' => '',
            'database' => 'test_openapi_theme_test',
            'encoding' => 'utf8mb4',
            'timezone' => 'UTC',
            'cacheMetadata' => true,
        ],
    ],
    'debug' => true,
]; 