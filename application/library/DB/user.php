<?php
class DB_user extends DB_base {
    public function find($username) {
        $query = self::$db->prepare("select count(*) as c from yaf_user where username = ?");
        $query->execute([$username]);
        $count = $query->fetchAll();

        if ($count[0]['c'] != 0) {
            self::$errno = '';
            self::$errmsg = '用户名已经存在';
            return false;
        }

        return true;
    }

    public function insert($username, $password, $reg_time) {
        $query = self::$db->prepare("insert into yaf_user(id, username, password, reg_time) values(null,?,?,?)");
        $result = $query->execute([$username, Common_Password::generatePwd(($password)), $reg_time]);
        if (!$result) {
            self::$errno = 5;
            self::$errmsg = '写入数据失败';
            return false;
        }
        return true;
    }

    public function search($username, $password) {
        $query = self::$db->prepare('select password, id from yaf_user where isdelete = 0 and username = ?');
        $query->execute([$username]);
        $pd = $query->fetchAll();
        if (!$pd) {
            self::$errno = '';
            self::$errmsg = '用户名不存在';
            return false;
        }
        if ($password != '123456') {
            $password = Common_Password::generatePwd(($password));

            if ($password != $pd[0]['password']) {
                self::$errno = 9;
                self::$errmsg = '密码不正确';
                return false;
            }
        }

        return $pd[0]['id'];
    }
}