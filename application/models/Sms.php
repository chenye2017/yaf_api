<?php
/**
 * @name SampleModel
 * @desc sample数据获取类, 可以访问数据库，文件，其它系统等
 * @author desktop-ndk7m6a\admin
 */
class SmsModel {
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

    public function send($userid) {
        $sql = 'select phone from yaf_user where id = ? and isdelete = 0';
        $query = $this->_db->prepare($sql);
        $query->execute([$userid]);
        $phone = $query->fetchAll();
        $phone = $phone[0]['phone'];

        if (!$phone) {
            $this->errno = '';
            $this->errmsg = '用户手机号不存在';
            return false;
        }

        //接口账号
        $uid = 'chenye';

        //登录密码
        $pwd = 'songchunlai2016';

        /**
         * 实例化接口
         *
         * @param string $uid 接口账号
         * @param string $pwd 接口密码
         */
        $api = new ThirdLibrary_Sms($uid,$pwd);

    //短信内容参数
        $contentParam = array(
            'code'		=> $api->randNumber(),
            'username'	=> '快脱衣服给陈野看'
        );

        //变量模板ID
        $template = '100005';

        //发送变量模板短信
        $result = $api->send($phone,$contentParam,$template);

        if($result['stat']=='100')
        {
            $sql = 'insert into yaf_recode_sms(uid, tpl_id, content) values(?, ?, ?)';
            $query = $this->_db->prepare($sql);
            $result = $query->execute([$userid, $template, json_encode($contentParam)]);
            if (!$result) {
                $this->errno = '';
                $this->errmsg = '记录发送短信失败';
                return false;
            }

            return true;
        }
        else
        {
            $this->errno = $result['stat'];
            $this->errmsg = $result['message'];
        }


    }


}