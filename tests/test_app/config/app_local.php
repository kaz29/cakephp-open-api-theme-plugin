<?php
declare(strict_types=1);

use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;

return [
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
]; 