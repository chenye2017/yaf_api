<?php

class PushController extends Yaf_Controller_Abstract {


    public function indexAction() {
        return $this->sendAction();
    }

    //这个推送没有往数据库里面写内容
    public function pushAction() {
        //接收参数
        $submit = $this->getRequest()->getPost('submit', 0);
        $cid = $this->getRequest()->getPost('cid', '');
        $content = $this->getRequest()->getPost('content', '');
        if ($submit != 1) {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>'请通过正常渠道登录'
            ]);
            return false;
        }

        if (!$cid || !$content) {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>'设备id，内容必须传递'
            ]);
        }

        $pushModel = new pushModel();
        $result = $pushModel->push(trim($cid), trim($content));
        if (!$result) {
            echo json_encode([
                'errno'=>$pushModel->errno,
                'errmsg'=>$pushModel->errmsg,
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
