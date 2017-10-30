<?php

class MailController extends Yaf_Controller_Abstract {


    public function indexAction() {
        return $this->sendAction();
    }

    public function sendAction() {
        //接收参数
        $submit = $this->getRequest()->getPost('submit', 0);
        $userid = $this->getRequest()->getPost('userid', '');
        $title = $this->getRequest()->getPost('title', '');
        $content = $this->getRequest()->getPost('content', '');

        if ($submit != 1) {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>'请通过正常渠道登录'
            ]);
            return false;
        }

        if (!$userid || !$title || !$content) {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>'用户id，名称，内容必须传递'
            ]);
        }

        $mailModel = new mailModel();
        $result = $mailModel->send(trim($userid), trim($title), trim($content));
        if (!$result) {
            echo json_encode([
                'errno'=>$mailModel->errno,
                'errmsg'=>$mailModel->errmsg,
            ]);
            return false;
        } else {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>'发送成功'
            ]);
            return true;
        }

    }

}
