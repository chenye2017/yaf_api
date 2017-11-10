<?php
class DB_wp extends DB_base {
    public function searchItem($pid) {
        $query = self::$db->prepare("select stock, etime from yaf_item where id = ?");
        $query->execute([$pid]);
        $count = $query->fetchAll();

        if (!$count) {
            list(self::$errno, self::$errmsg) = Err_map::getCodeMessage(2004);
            return false;
        }

        if ($count[0]['stock'] < 1) {
            list(self::$errno, self::$errmsg) = Err_map::getCodeMessage(2005);
            return false;
        }

        if (strtotime($count[0]['etime']) < time()) {
            list(self::$errno, self::$errmsg) = Err_map::getCodeMessage(2006);
            return false;
        }

        return $count;

    }

    public function createBill($pid, $userid)
    {
        $query = self::$db->prepare("insert into yaf_bill(itemid, uid, status) values(?,?,?)");
        $result = $query->execute([$pid, $userid, 0]);
        if (!$result) {
            list(self::$errno, self::$errmsg) = Err_map::getCodeMessage(2007);
            return false;
        }
        $id = self::$db->lastInsertId();
        return $id;
    }

    public function reduceItem($pid, $cinfo)
    {
        $query = self::$db->prepare('update yaf_item set stock = ? where id = ?');
        $stock = $cinfo[0]['stock'] - 1;
        $result = $query->execute([$stock, $pid]);
        if (!$result) {
            list(self::$errno, self::$errmsg) = Err_map::getCodeMessage(2008);
            return false;
        }

        return true;
    }
}