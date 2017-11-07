<?php
/**
 * @name SampleModel
 * @desc sample数据获取类, 可以访问数据库，文件，其它系统等
 * @author desktop-ndk7m6a\admin
 */
class UserModel {
    public $errno;  //这个控制器还需要用，返回错误信息
    public $errmsg;
    //private $_db; //连接数据库
    public function __construct() {
        try {
            $this->_db = new PDO('mysql:dbname=yaf_api;host=127.0.0.1', 'root', '');
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }

    }   
    
    public function register($username, $password) {
        $pdo = new DB_user();
        $find_result = $pdo->find($username);
        if (!$find_result) {
            $this->errno = $pdo->errno();
            $this->errmsg = $pdo->errmsg();
            return false;
        }

        if (strlen($password) < 8) {
            $this->errno = 4;
            $this->errmsg = '密码太短了';
            return false;
        }

        $reg_time = date('Y:m:d H:i:s', time());

        $insert_result = $pdo->insert($username, $password, $reg_time);

        if (!$insert_result) {
            $this->errno = $pdo->errno();
            $this->errmsg = $pdo->errmsg();
            return false;
        }
        return true;
    }

    public function login($username, $password) {
        $pdo = new DB_user();
        $search_result = $pdo->search($username, $password);
        if (!$search_result) {
            $this->errno = $pdo->errno();
            $this->errmsg = $pdo->errmsg();
            return false;
        }

        return intval($search_result);

    }





}
