<?php

class ArticleModel {
    public $errno;  //这个控制器还需要用，返回错误信息
    public $errmsg;
    private $_db; //连接数据库
    public function __construct() {
        try {
            $this->_db = new PDO('mysql:dbname=yaf_api;host=127.0.0.1', 'root', '');

            $this->_db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); //禁用预处理
            $this->_db->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false); //取出来的东西不要转换类型
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

    public function status($aid, $status) {
        $query = $this->_db->prepare('update yaf_article set isdelete = ? where id = ?');
        $result = $query->execute([$status, $aid]); //更新语句这个就能出结果，返回布尔值，成功true， 失败false
        if ($result) {
            return true;
        } else {
            $this->errno = '';
            $this->errmsg = $query->errorInfo();
            return false;
        }
    }

    public function info($aid)
    {
        $query = $this->_db->prepare( 'select * from yaf_article where id = ?');
        $query->execute([$aid]);
        $result = $query->fetchAll();
        $result = $result[0];  //这个获取错误，或者获取失败，直接返回null
        if (!$result) {
            $this->errno = '';
            $this->errmsg = $query->errorInfo();
            return false;
        } else {
            return $result;
        }
    }

    //感觉删除状态还是和文章状态分开来比较好，zz,有个默认where条件比较好拼装
    public function get($page, $pageSize, $status, $cate)
    {
        $where_arr = [];
        $sql = 'select * from yaf_article ';
        if ($status != -1 || $cate != -1) {
            $sql .= ' where ';
            if ($status != -1) {
                $sql .= ' isdelete = ? and';
                array_push($where_arr, $status);
            }
            if ($cate != -1) {
                $sql .= ' cate = ? and';
                array_push($where_arr, $cate);
            }
            $sql = trim($sql, 'and ');
        }
        $offset = ($page - 1) * $pageSize;
        $sql .= ' order by id desc limit ? offset ?';
        array_push($where_arr, intval($pageSize));
        array_push($where_arr, $offset);

        $query = $this->_db->prepare($sql);
//        $sql = $query->debugDumpParams();
//        var_dump($sql);
        $query->execute($where_arr);
        $result = $query->fetchAll();
        if (!$result) {
            $this->errno = '';
            $this->errmsg = $query->errorInfo();
            return false;
        } else {
            return $result;
        }

    }

    public function getCate($cate) {
        $query = $this->_db->prepare('select catename from yaf_cate where id = ? and isdelete = 0');
        $query->execute([$cate]);
        $cate = $query->fetchAll();
        if (!$cate) {
            $this->errno = '';
            $this->errmsg = $query->errorInfo();
            return false;
        } else {
            return $cate[0];
        }
    }





}
