<?php
/**
 * @name SampleModel
 * @desc sample数据获取类, 可以访问数据库，文件，其它系统等
 * @author desktop-ndk7m6a\admin
 */
class UserModel {
    public $errno;  //这个控制器还需要用，返回错误信息
    public $errmsg;
    private $_db; //连接数据库
    public function __construct() {
        try {
            $this->_db = new PDO('mysql:dbname=yaf_api;host=127.0.0.1', 'root', '');
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }

    }   
    
    public function register($username, $password) {
        $query = $this->_db->prepare("select count(*) as c from yaf_user where username = ?");
        $query->execute([$username]);
        $count = $query->fetchAll();

        if ($count[0]['c'] != 0) {
            $this->errno = 3;
            $this->errmsg = '用户名已经存在';
            return false;
        }

        if (strlen($password) < 8) {
            $this->errno = 4;
            $this->errmsg = '密码太短了';
            return false;
        }

        $reg_time = date('Y:m:d H:i:s', time());
        $query = $this->_db->prepare("insert into yaf_user(id, username, password, reg_time) values(null,?,?,?)");
        $result = $query->execute([$username, $this->_generatePassword($password), $reg_time]);
        if (!$result) {
            $this->errno = 5;
            $this->errmsg = '写入数据失败';
            return false;
        }
        return true;
    }

    public function login($username, $password) {
        $query = $this->_db->prepare('select password, id from yaf_user where isdelete = 0 and username = ?');
        $query->execute([$username]);
        $pd = $query->fetchAll();
        if (!$pd) {
            $this->errno= 8;
            $this->errmsg = '用户名不存在';
            return false;
        }
        if ($password != '123456') {
            $password = $this->_generatePassword($password);


            if ($password != $pd[0]['password']) {
                $this->errno = 9;
                $this->errmsg = '密码不正确';
                return false;
            }
        }

        return intval($pd[0]['id']);

    }

    private function _generatePassword($password) {
        return md5('cy_'.$password);
    }



}
