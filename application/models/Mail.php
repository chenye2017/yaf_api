<?php

require './vendor/autoload.php';
use Nette\Mail\Message;

class MailModel {
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

    public function send($userid, $title, $content)
    {
        $sql = 'select * from yaf_user where id = ? and isdelete = 0';
        $query = $this->_db->prepare($sql);
        $query->execute([$userid]);
        $email = $query->fetchAll();
        $email = $email[0]['email'];

        if (!$email) {
            $this->errno = '';
            $this->errmsg = '用户邮箱不存在';
            return false;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errno = '';
            $this->errmsg = '用户邮箱不合法';
            return false;
        }

        $mail = new Message;
        $mail->setFrom('chenyeyufatang@126.com')
            ->addTo($email)
            ->setSubject($title)
            ->setBody($content);

        $mailer = new Nette\Mail\SmtpMailer([
            'host' => 'smtp.126.com',
            'username' => 'chenyeyufatang@126.com',
            'password' => 'songchunlai2017',  //注意这里是smtp独立的密码，并不是邮箱的密码，126邮箱那块可以打开，qq邮箱没有找到设置
            'secure' => 'ssl',
        ]);
        $mailer->send($mail);
        return true;
    }





}
