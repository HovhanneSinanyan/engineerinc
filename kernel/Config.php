<?php

namespace Kernel;

class Config {
    private static $instance = null;
    private $env = [];
    private function __construct()
    {
        $this->env = parse_ini_file(__DIR__.'/../.env');
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function getConf($config, $default) {
        return self::getInstance()->env[$config] ?? $default;
    }    
}