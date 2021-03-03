#!/usr/local/bin/php -q
<?php
require dirname(__DIR__) . '/vendor/autoload.php';

$app_path = dirname(__DIR__);
$openapi = \OpenApi\scan(
    $app_path, 
    [
        'exclude' => [
            'vendor', 
            'tmp', 
            'logs', 
            'tests', 
            'webroot',
        ]
    ]
);
file_put_contents(dirname($app_path).'/docs/swagger.json', $openapi->toJson());
