<?php

namespace App\Helpers\Models;

use PDO;
use PDOException;
use Exception;

class Connection
{

    static public $connect_keys = [
        'driver' => '',
        'host' => '',
        'database' => '',
        'username' => '',
        'password' => '',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => '',
        'debug' => true,
    ];

    private $_connection;

    static private $_instance;

    private function __clone() {}
    
    private function __construct() {
        try {
            if ($this->check_keys()) {
                $keys = static::$connect_keys;
                $dsn = "{$keys['driver']}:host={$keys['host']};dbname={$keys['database']};charset={$keys['charset']}";
                $this->_connection = new PDO($dsn, "{$keys['username']}", "{$keys['password']}", [PDO::ATTR_PERSISTENT => true]);

                if ($keys['debug'] === true) {
                    $this->_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } else {
                    $this->_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                }
            }
        } catch (PDOException $e) {
            throw new Exception("Error Connecting to Database");
        } catch (Exception $exception) {
        }
    }

    private function check_keys () {
        foreach (static::$connect_keys as $key => $value) {
            if ( !in_array($key, ['prefix', 'password', 'debug'])  && empty($value) ) {
                throw new Exception("No value for {$key}, fill to connect");
            }
        }
        return true;
    }
    
    /**
     * Get the pdo connection.
     */
    public function getConnection() {
        return $this->_connection;
    }

    /**
     * @return Connection
     * @throws Exception
     */
    static public function getInstance() {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
  
}