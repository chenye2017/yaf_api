<?php
class Err_map {
    public static $_codeArr1 = [
        '1000' => '',
        '1001' => '',
        '1002' => '',
        '1003' => ''
    ];

    public static $_codeArr = [
        '0' => '',
        '1000' => '',
        '1001' => '请通过正常渠道提交',
        '1002' => '用户名或者密码必须传递',
        '1003' => '用户名或者密码必须传递',
        '1004' => '用户名已经存在',
        '1005' => '写入数据失败',
        '1006' => '用户名不存在',
        '1007' => '密码不正确',
        '1008' => 'login出错',
        '1009' => 'register出错'
    ];

    public static function getCodeMessage($code) {
        if (isset(self::$_codeArr[$code])) {
            return [$code, self::$_codeArr[$code]];
        } else {
            return [$code, '错误类型没有设置'];
        }
    }
}