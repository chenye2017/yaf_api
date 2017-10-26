<?php

class ArticleModel {
    public $errno;  //这个控制器还需要用，返回错误信息
    public $errmsg;
    private $_db; //连接数据库
    public function __construct() {
        try {
            $this->_db = new PDO('mysql:dbname=yaf_api;host=127.0.0.1', 'root', '');
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }

    }

    public function add($author, $content, $title, $cate, $aid = 0)
    {
        if (!$aid) {
            $quary = $this->_db->prepare('select count(*) as c from yaf_cate where id = ? and isdelete = 0');
            $quary->execute([$cate]);
            $count = $quary->fetchAll();
            if (!$count) {
                echo json_encode([
                    $this->errno = '',
                    $this->errmsg = '文章分类不存在，请添加分类'
                ]);
                return false;
            }

            $quary = $this->_db->prepare('insert into yaf_article(id, author, content, title, cate) values(null, ?, ?, ?, ?)');
            $quary->execute([$author, $content, $title, $cate]);
            $id = $this->_db->lastinsertId();
            if (!$id) {
                echo json_encode([
                    $this->errno = '',
                    $this->errmsg = '文章插入失败'
                ]);
                return false;
            } else {
                echo json_encode([
                    $this->errno = '',
                    $this->errmsg = ''
                ]);
                return $id;
            }
        } else {
            $quary = $this->_db->prepare('select count(*) from yaf_article where isdelete = 0 and id = ?');
            $quary->execute([$aid]);
            $count = $quary->fetchAll();
            if ($count[0] < 1) {
                echo json_encode([
                    $this->errno = '',
                    $this->errmsg = '文章不存在'
                ]);
                return false;
            }
            $quary = $this->_db->prepare('update yaf_article set author = ?, content = ?, title = ?, cate = ? where id = ?');
            $result = $quary->execute([$author, $content, $title, $cate, $aid]);
            if (!$result) {
                echo json_encode([
                    $this->errno = '',
                    $this->errmsg = '文章更新失败'
                ]);
                return false;
            } else {
                echo json_encode([
                    $this->errno = '',
                    $this->errmsg = '文章更新成功'
                ]);
                return $aid;
            }
        }
    }





}
