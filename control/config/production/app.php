<?php
/**
 * Project: startup
 * File: app.php
 *
 * Initial version by: @oghenemavo
 * Initial version created on: 29/09/2019 9:20 PM
 *
 * Contact: princetunes@gmail.com
 *
 */

return [
    'app' => [
        'url' => 'localhost',
        'hash' => [
            'algo' => 'PASSWORD_BCRYPT',
            'cost' => 10
        ],
        'debug' => false
    ],
    'db' => [
        'driver' => 'mysql',
        'host' => getenv('DB_HOST'),
        'db_name' => getenv('DB_NAME'),
        'username' => getenv('DB_NAME'),
        'password' => getenv('DB_NAME'),
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => '',
    ],
    'auth' => [
        'session' => 'user_id',
        'remember' => 'user_r',
    ],
    'mail' => [],

];