<?php
class DB_base {
    public static $db = '';
    public static $errno = '';
    public static $errmsg = '';

    public function __construct()
    {
        if (!self::$db) {
            self::$db = new PDO('mysql:dbname=yaf_api;host:127.0.0.1', 'root', '');
        }
        return true;
    }

    public function errno() {
        return self::$errno;
    }

    public function errmsg() {
        return self::$errmsg;
    }
}