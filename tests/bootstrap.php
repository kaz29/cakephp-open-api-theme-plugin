<?php
declare(strict_types=1);

/**
 * Test suite bootstrap for OpenApiTheme.
 *
 * This function is used to find the location of CakePHP whether CakePHP
 * has been installed as a dependency of the plugin, or the plugin is itself
 * installed as a dependency of an application.
 */
$findRoot = function ($root) {
    do {
        $lastRoot = $root;
        $root = dirname($root);
        if (is_dir($root . '/vendor/cakephp/cakephp')) {
            return $root;
        }
    } while ($root !== $lastRoot);

    throw new Exception("Cannot find the root of the application, unable to run tests");
};
$root = $findRoot(__FILE__);
unset($findRoot);

chdir($root);

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
define('ROOT', $root);
define('APP_DIR', 'TestApp');
define('APP', ROOT . DS . 'tests' . DS . 'test_app' . DS);
define('TMP', sys_get_temp_dir() . DS);
define('CONFIG', ROOT . DS . 'tests' . DS . 'config' . DS);
define('CAKE_CORE_INCLUDE_PATH', ROOT . DS . 'vendor' . DS . 'cakephp' . DS . 'cakephp');
define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . DS);
define('CAKE', CORE_PATH . 'src' . DS);
define('TESTS', ROOT . DS . 'tests' . DS);
define('TEST_APP', TESTS . 'test_app' . DS);
define('WWW_ROOT', TEST_APP . 'webroot' . DS);

require_once $root . '/vendor/autoload.php';
require_once CORE_PATH . 'config/bootstrap.php';

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Cake\Datasource\ConnectionManager;
use Cake\Log\Log;
use Cake\Utility\Security;

Configure::write('debug', true);
Security::setSalt('dummy-salt-for-tests');

// Setup test database configuration
ConnectionManager::setConfig('default', [
    'className' => Connection::class,
    'driver' => Mysql::class,
    'database' => 'test_openapi_theme',
    'username' => 'root',
    'password' => '',
    'encoding' => 'utf8mb4',
    'timezone' => 'UTC',
    'cacheMetadata' => true,
    'quoteIdentifiers' => false,
    'log' => false,
]);

ConnectionManager::setConfig('test', [
    'className' => Connection::class,
    'driver' => Mysql::class,
    'database' => 'test_openapi_theme_test',
    'username' => 'root',
    'password' => '',
    'encoding' => 'utf8mb4',
    'timezone' => 'UTC',
    'cacheMetadata' => true,
    'quoteIdentifiers' => false,
    'log' => false,
]);

Cache::setConfig([
    '_cake_core_' => [
        'engine' => 'File',
        'prefix' => 'cake_core_',
        'serialize' => true,
        'path' => TMP,
    ],
    '_cake_model_' => [
        'engine' => 'File',
        'prefix' => 'cake_model_',
        'serialize' => true,
        'path' => TMP,
    ],
]);

Log::setConfig([
    'debug' => [
        'engine' => 'Cake\Log\Engine\FileLog',
        'path' => TMP . 'logs/',
        'file' => 'debug',
        'levels' => ['notice', 'info', 'debug'],
    ],
    'error' => [
        'engine' => 'Cake\Log\Engine\FileLog',
        'path' => TMP . 'logs/',
        'file' => 'error',
        'levels' => ['warning', 'error', 'critical', 'alert', 'emergency'],
    ],
]);

// Load test specific plugin configuration
Configure::load('app', 'default', false);
