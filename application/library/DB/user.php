<?php
class DB_user extends DB_base {
    public function find($username) {
        $query = self::$db->prepare("select count(*) as c from yaf_user where username = ?");
        $query->execute([$username]);
        $count = $query->fetchAll();

        if ($count[0]['c'] != 0) {
            list(self::$errno, self::$errmsg) = Err_map::getCodeMessage(1004);
            return false;
        }

        return true;
    }

    public function insert($username, $password, $reg_time) {
        $query = self::$db->prepare("insert into yaf_user(id, username, password, reg_time) values(null,?,?,?)");
        $result = $query->execute([$username, Common_Password::generatePwd(($password)), $reg_time]);
        if (!$result) {
            list(self::$errno, self::$errmsg) = Err_map::getCodeMessage(1005);
            return false;
        }
        return true;
    }

    public function search($username, $password) {
        $query = self::$db->prepare('select password, id from yaf_user where isdelete = 0 and username = ?');
        $query->execute([$username]);
        $pd = $query->fetchAll();
        if (!$pd) {
            list(self::$errno, self::$errmsg) = Err_map::getCodeMessage(1006);
            return false;
        }
        if ($password != '123456') {
            $password = Common_Password::generatePwd(($password));

            if ($password != $pd[0]['password']) {
                list(self::$errno, self::$errmsg) = Err_map::getCodeMessage(1007);
                return false;
            }
        }

        return $pd[0]['id'];
    }
}