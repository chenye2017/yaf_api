<?php
class SmsController extends Yaf_Controller_Abstract {


    public function indexAction() {
        return $this->sendAction();


    }

    public function sendAction() {
        $userid = $this->getRequest()->getPost('userid', '');
        $submit = $this->getRequest()->getPost('submit', 0); //为了防止爬虫，其实人为看一下多传递个参数就可以了，但post传递的参数好像看不到

        if ($submit != 1) {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>'请通过正常渠道登录'
            ]);
        }

        if (!$userid || !is_numeric($userid) ) {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>'用户id必须传递且必须是数字'
            ]);
        }

        $smsModel = new smsModel();
        $result = $smsModel->send($userid);
        if (!$result) {
            echo json_encode([
                'errno'=>$smsModel->errno,
                'errmsg'=>$smsModel->errmsg,
            ]);
            return false;
        } else {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>'发送短信成功'
            ]);
            return true;
        }

    }

}
