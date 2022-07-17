<?php
/**
 * Project: startup
 * File: init.php
 *
 * Initial version by: @oghenemavo
 * Initial version created on: 12/10/2019 12:53 PM
 *
 * Contact: princetunes@gmail.com
 *
 */


use Pimple\Container;
use Dotenv\Dotenv;
use Northwoods\Config\ConfigFactory;
use App\Helpers\Models\{Connection, DatabaseObject};
use Rakit\Validation\Validator as Scrutinize;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('\App\Helpers\Errors\Error::errorHandler');
set_exception_handler('\App\Helpers\Errors\Error::exceptionHandler');

// start session
session_start();

$dotenv = Dotenv::create(dirname(__DIR__, 2) );
$dotenv->load();
$dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS']);
$dotenv->required('DB_HOST')->allowedValues(['localhost', '127.0.0.1']);

$config = ConfigFactory::make([
    'directory' => dirname(__DIR__, 1) . '/config',
    'environment' => 'development',
//    'type' => 'yaml'
]);

$container = new Container();

$container['db_connection'] = function ($c) use ($config) {
    Connection::$connect_keys = [
        'driver' => $config->get('app.db.driver'),
        'host' => $config->get('app.db.host'),
        'database' => $config->get('app.db.db_name'),
        'username' => $config->get('app.db.username'),
        'password' => $config->get('app.db.password'),
        'charset' => $config->get('app.db.charset'),
        'collation' => $config->get('app.db.collation'),
        'prefix' => '',
        'debug' => $config->get('app.app.debug'),
    ];

    return Connection::getInstance()->getConnection();
};

$container['db_object'] = function ($c) {
    return DatabaseObject::set_database($c['db_connection']);
};

$container['scrutinize'] = function ($c) {
    return new Scrutinize;
};

$db = $container['db_object'];

$scrutinize = $container['scrutinize'];