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
        $submit = $this->getRequest()->getPost('submit', 0); //getQuery竟然跟获取不到post方式提交的参数，因为他是获取get方式提交的参数

        if (!$isAdmin) {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>'没有权限'
            ]);
            return false;
        }

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

        if (!$isAdmin) {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>'没有权限'
            ]);
            return false;
        }

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

    //修改文章状态，0、默认状态， 1删除， 2 下线
    public function statusAction()
    {
        //验证是否有权限增加和调用接口渠道是否正确
        $isAdmin = $this->_checkAdmin();
        $submit = $this->getRequest()->getPost('submit', 0);
        $aid = $this->getRequest()->getPost('aid', 0);
        $status = $this->getRequest()->getPost('status', 0);

        if (!$isAdmin) {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>'没有权限'
            ]);
            return false;
        }

        if ($submit != 1) {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>'请通过正确的方式请求'
            ]);
            return false;
        }

        //验证参数
        if (!is_numeric($aid) || $aid < 0) {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>'参数错误'
            ]);
            return false;
        }


        $articleModel = new articleModel();
        $result = $articleModel->status($aid, $status);
        if (!$result) {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>$articleModel->errmsg
            ]);
            return false;
        } else {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>'修改成功'
            ]);
            return true;
        }
    }

    //获取文章详情
    public function ListAction()
    {
        $aid = $this->getRequest()->getPost('aid', 0);

        //验证参数
        if (!is_numeric($aid) || $aid < 0) {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>'参数错误'
            ]);
            return false;
        }


        $articleModel = new articleModel();
        $result = $articleModel->info($aid);
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
                'data'=> [
                    '作者'=>$result['author'],
                    '标题'=>$result['title'],
                    '内容'=>$result['content']
                ]
            ]);
            return true;
        }
    }

    //其实这个联立查出来很方便
    public function getAction()
    {
        //给默认值的好处就是方便接口的扩展，可以在方法名后面加更多的参数，也不用担心新的参数之前的参数没有传，导致方法错误，因为所有的参数都有值，如果想某个参数不起作用，也可以给一个特定的值，model层sql语句拼装的时候如果这个参数值是这么多，直接把这个筛选条件去掉就可以了
        $page = $this->getRequest()->getPost('page', 1); //页码
        $pageSize = $this->getRequest()->getPost('pagesize', 10); //页的内容多少
        $status = $this->getRequest()->getPost('status', -1);  //文章状态
        $cate = $this->getRequest()->getPost('cate', -1); //分类

        //定义一个空数组，存放cate种类
        $cate_arr = [];
        $new_result = [];

        $articleModel = new articleModel();
        $result = $articleModel->get($page, $pageSize, $status, $cate);
        if (!$result) {
            echo json_encode([
                'errno'=>'',
                'errmsg'=>$articleModel->errmsg
            ]);
            return false;
        } else {
            foreach ($result as $r_key=>$r_value) {
                if ($cate_arr[$r_value['cate']]) {
                    $cate = $cate_arr[$r_value['cate']];
                } else {
                    $cate = $articleModel->getCate($r_value['cate']);
                    $cate = $cate['catename'];
                    $cate_arr[$r_value['cate']] = $cate;
                    if (!$cate) {
                        echo json_encode([
                            'errno'=>$articleModel->errno,
                            'errmsg'=>$articleModel->errmsg
                        ]);
                        return false;
                    }
                }

                if (mb_strlen($r_value['content']) > 5) {
                    $desc = mb_substr($r_value['content'], 0,4, 'utf-8');
                } else {
                    $desc = $r_value['content'];
                }

                $new_result[] = [
                  '作者'=>$r_value['author'],
                  '标题'=>$r_value['title'],
                  '内容'=>$r_value['content'],
                  '分类'=>$cate,
                  '简介'=>$desc
                ];
            }

            echo json_encode([
                'errno'=>'',
                'errmsg'=>'',
                'data'=>$new_result
            ]);
            return true;
        }
    }


    private function _checkAdmin()
    {
        return true;  //没有写具体的验证方法
    }
}
