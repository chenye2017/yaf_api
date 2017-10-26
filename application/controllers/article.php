<?php

class ArticleController extends Yaf_Controller_Abstract
{


    public function indexAction()
    {

        return $this->listAction();


    }

    public function addAction()
    {
        //验证是否有权限增加和调用接口渠道是否正确
        $isAdmin = $this->_checkAdmin();
        $submit = $this->getRequest()->getPost('submit', 0); //getQuery竟然跟获取不到post方式提交的参数

        if ($submit != 1) {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>'请通过正确的方式请求'
            ]);
            return false;
        }

        //获取参数
        $author = $this->getRequest()->getPost('author', '');
        $content = $this->getRequest()->getPost('content', '');
        $title = $this->getRequest()->getPost('title', '');
        $cate = $this->getRequest()->getPost('cate', '');

        //验证参数
        if (!$author || !$content || !$title || !$cate) {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>'参数错误'
            ]);
            return false;
        }

        $articleModel = new articleModel();
        $result = $articleModel->add($author, $content, $title, $cate);
        if (!$result) {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>$articleModel->errmsg
            ]);
            return false;
        } else {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>'',
                'data'=>['aid'=>$result]
            ]);
            return true;
        }

    }

    public function editAction()
    {
        //验证是否有权限增加和调用接口渠道是否正确
        $isAdmin = $this->_checkAdmin();
        $submit = $this->getRequest()->getPost('submit', 0);

        if ($submit != 1) {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>'请通过正确的方式请求'
            ]);
            return false;
        }

        //获取参数
        $aid = $this->getRequest()->getPost('aid', '');

        //验证参数
        if (!is_numeric($aid) || $aid < 0) {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>'参数错误'
            ]);
            return false;
        }

        //获取参数
        $author = $this->getRequest()->getPost('author', '');
        $content = $this->getRequest()->getPost('content', '');
        $title = $this->getRequest()->getPost('title', '');
        $cate = $this->getRequest()->getPost('cate', '');

        //验证参数
        if (!$author || !$content || !$title || !$cate) {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>'参数错误'
            ]);
            return false;
        }

        $articleModel = new articleModel();
        $result = $articleModel->add($author, $content, $title, $cate, $aid);
        if (!$result) {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>$articleModel->errmsg
            ]);
            return false;
        } else {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>'',
                'data'=>['aid'=>$result]
            ]);
            return true;
        }
    }

    private function _checkAdmin()
    {
        return true;  //没有写具体的验证方法
    }
}
