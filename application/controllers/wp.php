<?php

$path_name = dirname(__FILE__).'/../library/ThirdLibrary/qrcode/';
include_once ($path_name.'phpqrcode.php');

class WpController extends Yaf_Controller_Abstract {


    public function indexAction() {



    }

    public function createbillAction() {  //和商品有关，商品id
        $pid = Common_Request::post('pid', '');
        $submit = Common_Request::post('submit', 0); //为了防止爬虫，其实人为看一下多传递个参数就可以了，但post传递的参数好像看不到

        //检测是否通过正常渠道调用接口
        if ($submit != 1) {
            list($errno, $errmsg) = Err_map::getCodeMessage(1001);
            echo Common_Request::response($errno, $errmsg);
            return false;
        }

        //检测参数是否正确
        if (!$pid || !is_numeric($pid) ) {
            list($errno, $errmsg) = Err_map::getCodeMessage(2002);
            echo Common_Request::response($errno, $errmsg);
            return false;
        }

        //检测用户是否登录
        session_start();
        if (!$_SESSION['userid'] || !$_SESSION['user_token'] || !$_SESSION['login_time'] || $_SESSION['user_token'] != md5('cy'.$_SESSION['login_time'].$_SESSION['userid'])) {
            list ($errno, $errmsg) = Err_map::getCodeMessage(2003);
            echo Common_Request::response($errno, $errmsg);
            return false;
        }

        $wpModel = new wpModel();
        $result = $wpModel->createbill($pid, $_SESSION['userid']);
        if (!$result) {
            echo Common_Request::response($wpModel->errno, $wpModel->errmsg);
            return false;
        } else {
            echo Common_Request::response('', '', $result);
            return true;
        }

    }

    public function qrcodeAction()  //和订单相关
    {
        $billId = Common_Request::post('billId', 0);
        $submit = Common_Request::post('submit', 0);
        //检查参数
        //检测是否通过正常渠道调用接口
        if ($submit != 1) {
            list($errno, $errmsg) = Err_map::getCodeMessage(1001);
            echo Common_Request::response($errno, $errmsg);
            return false;
        }

        //检测参数是否正确
        if (!$billId || !is_numeric($billId) ) {
            list($errno, $errmsg) = Err_map::getCodeMessage(2001);
            echo Common_Request::response($errno, $errmsg);
            return false;
        }

        $wpModel = new wpModel();
        $url = $wpModel->qrcode($billId); //var_dump($url);exit;
        if (!$url) {
            echo Common_Request::response($wpModel->errno, $wpModel->errmsg);
            return false;
        } else {
            QRcode::png($url);
        }

    }

    public function callbackAction()
    {

    }

}
