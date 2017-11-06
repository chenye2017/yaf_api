<?php

header("content-type:text/html;charset=gb2312");


require_once ('../vendor/autoload.php');

$address = 'http://yaf.app:800/user/';
$newUser = 'user_test';
$newPwd = 'user_test_pwd';

/**
 * 测试用户注册接口
 */

$curl = new Curl\Curl();
$curl->post($address.'reg', array(
    'username' => $newUser.'_'.rand(),
    'password' => 'mypassword'
));


if ($curl->error) {
    echo $curl->error_code.' , '.$curl->error_message;
    die;
} else {
    $errInfo = json_decode($curl->response, true);
    if ($errInfo['errmsg']) {
        var_dump($errInfo['errno'], $errInfo['errmsg']);
        die;
    } else {
        echo '注册成功,用户名 : '.$errInfo['data']['username'].' , 密码 ：'.$errInfo['data']['password']."\n";
    }
}

/**
 * 测试用户登录
 */

$curl->post($address.'/login', [
    'username'=>$errInfo['data']['username'],
    'password'=>$errInfo['data']['password'],
    'submit'=>1
]);

if ($curl->error_code) {
    echo $curl->error_code.' , '.$curl->error_message;
}else {
    $errInfo_1 = json_decode($curl->response, true);
    if ($errInfo_1['errmsg']) {
        echo $errInfo_1['errmsg'];
        die;
    } else {
        echo '登录成功, 用户名 : '.$errInfo['data']['username']."\n";
    }
}

/**
 * 错误密码登录
 */
$curl->post($address.'login', [
    'username'=>$errInfo['data']['username'],
    'password'=>$newPwd.rand(),
    'submit'=>1]
);

if ($curl->error_code) {
    echo $curl->error_code.' , '.$curl->error_message;
    die;
} else {
    $errInfo_2 = json_decode($curl->response, true);
    if ($errInfo_2['errmsg']) {
        echo $errInfo_2['errno'].' , '.$errInfo_2['errmsg']." , 验证成功 \n";
        //这里用那个errno等于那个密码错误的那个no相等最好，但我之前没有写code，所以这里比较不了
    } else {
        echo '验证失败';
        die;
    }
}

echo '测试结束';

