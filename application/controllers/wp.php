<?php

$path_name = dirname(__FILE__).'/../library/ThirdLibrary/qrcode/';
include_once ($path_name.'phpqrcode.php');

class WpController extends Yaf_Controller_Abstract {


    public function indexAction() {



    }

    public function createbillAction() {  //和商品有关，商品id
        $pid = $this->getRequest()->getPost('pid', '');
        $submit = $this->getRequest()->getPost('submit', 0); //为了防止爬虫，其实人为看一下多传递个参数就可以了，但post传递的参数好像看不到

        //检测是否通过正常渠道调用接口
        if ($submit != 1) {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>'请通过正常渠道登录'
            ]);
            return false;
        }

        //检测参数是否正确
        if (!$pid || !is_numeric($pid) ) {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>'商品id必须传递且必须是数字'
            ]);
            return false;
        }

        //检测用户是否登录
        session_start();
        if (!$_SESSION['userid'] || !$_SESSION['user_token'] || !$_SESSION['login_time'] || $_SESSION['user_token'] != md5('cy'.$_SESSION['login_time'].$_SESSION['userid'])) {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>'用户必须登录'
            ]);
            return false;
        }



        $wpModel = new wpModel();
        $result = $wpModel->createbill($pid, $_SESSION['userid']);
        if (!$result) {
            echo json_encode([
                'errno'=>$wpModel->errno,
                'errmsg'=>$wpModel->errmsg,
            ]);
            return false;
        } else {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>'生成订单成功',
                'data'=>$result
            ]);
            return true;
        }

    }

    public function qrcodeAction()  //和订单相关
    {
        $billId = $this->getRequest()->getPost('billId', 0);
        $submit = $this->getRequest()->getPost('submit', 0);
        //检查参数
        //检测是否通过正常渠道调用接口
        if ($submit != 1) {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>'请通过正常渠道登录'
            ]);
            return false;
        }

        //检测参数是否正确
        if (!$billId || !is_numeric($billId) ) {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>'订单id必须传递且必须是数字'
            ]);
            return false;
        }

        $wpModel = new wpModel();
        $url = $wpModel->qrcode($billId); //var_dump($url);exit;
        if (!$url) {
            echo json_encode([
                'errno'=>$wpModel->errno,
                'errmsg'=>$wpModel->errmsg
            ]);
            return false;
        } else {
            QRcode::png($url);
        }

    }

    public function callbackAction()
    {

    }

}
