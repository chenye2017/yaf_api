<?php

class IpController extends Yaf_Controller_Abstract {


    public function indexAction() {

    }

    public function ipAction() {
        //接收参数
        $submit = $this->getRequest()->getPost('submit', 0);
        $ip = $this->getRequest()->getPost('ip', '');


        if ($submit != 1) {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>'请通过正常渠道登录'
            ]);
            return false;
        }

        if (!$ip || !filter_var($ip, FILTER_VALIDATE_IP)) {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>'ip为空或者不合法'
            ]);
            return false;
        }

        $ipModel = new ipModel();
        $result = $ipModel->ip(trim($ip));
        if (!$result) {
            echo json_encode([
                'errno'=>$ipModel->errno,
                'errmsg'=>$ipModel->errmsg,
            ]);
            return false;
        } else {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>'',
                'data'=>$result
            ]);
            return true;
        }

    }

}
