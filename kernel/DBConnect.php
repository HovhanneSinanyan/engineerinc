<?php

namespace Kernel;
use PDO;
use PDOException;
use Kernel\Config;

class DBConnect {
    private $host;
    private $port;
    private $username;
    private $password;
    private $database;
    private $charset;
    private $pdo;
    

    public function __construct()
    {
        if (null == $this->pdo) {
            $this->host = Config::getConf('DB_HOST', 'default');
            $this->port = Config::getConf('DB_PORT', 'default');
            $this->username = Config::getConf('DB_USERNAME', 'default');
            $this->password = Config::getConf('DB_PASSWORD', 'default');
            $this->database = Config::getConf('DB_NAME', 'default');
            $this->charset = Config::getConf('DB_CHARSET', 'utf8');
            $this->connect();
        }
    }

    private function connect()
    {
        $dsn = "mysql:host={$this->host}:{$this->port};dbname={$this->database};charset={$this->charset}";

        try {
            $this->pdo = new PDO($dsn, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->pdo;
    }
}