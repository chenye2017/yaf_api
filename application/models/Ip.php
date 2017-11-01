<?php


class IpModel {
    public $errno;  //这个控制器还需要用，返回错误信息
    public $errmsg;
    private $_db = null; //连接数据库
    public function __construct() {
        try {
            $this->_db = new PDO('mysql:dbname=yaf_api;host=127.0.0.1', 'root', '');

            $this->_db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); //禁用预处理
            $this->_db->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false); //取出来的东西不要转换类型
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }

    }

    public function ip($ip)
    {
        $result = ThirdLibrary_Ip::find($ip);
        return $result;
    }





}
