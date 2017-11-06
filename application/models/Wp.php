<?php

$path = dirname(__FILE__).'/../Library/ThirdLibrary/wp/';
require_once $path."WxPay.Api.php";
require_once $path."WxPay.NativePay.php";


class WpModel {
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

    public function createbill($pid, $userid) {
        $query = $this->_db->prepare("select stock, etime from yaf_item where id = ?");
        $query->execute([$pid]);
        $count = $query->fetchAll();

        if (!$count) {
            $this->errno = '';
            $this->errmsg = '商品不存在';
            return false;
        }

        if ($count[0]['stock'] < 1) {
            $this->errno = '';
            $this->errmsg = '商品库存不足';
            return false;
        }

        if (strtotime($count[0]['etime']) < time()) {
            $this->errno = '';
            $this->errmsg = '商品已经过期了';
            return false;
        }

        $query = $this->_db->prepare("insert into yaf_bill(itemid, uid, status) values(?,?,?)");
        $result = $query->execute([$pid, $userid, 0]);
        if (!$result) {
            $this->errno = '';
            $this->errmsg = '写入数据失败';
            return false;
        }

        $bill_id = $this->_db->lastInsertId();

        $query = $this->_db->prepare('update yaf_item set stock = ? where id = ?');
        $stock = $count[0]['stock'] - 1;
        $result = $query->execute([$stock, $pid]);
        if (!$result) {
            $this->errno = '';
            $this->errmsg = '减少商品库存失败';
        }

        return $bill_id;
    }

    public function qrcode($billId)
    {
        $query = $this->_db->prepare('select * from yaf_bill where id = ?');
        $query->execute([$billId]);
        $bill = $query->fetchAll();
        if (!$bill) {
            $this->errno = '';
            $this->errmsg = '订单不存在';
            return false;
        }

        $item = $bill[0]['itemid'];
        $query = $this->_db->prepare('select * from yaf_item where id = ?');
        $query->execute([$item]);
        $item_info = $query->fetchAll();
        if (!$item_info) {
            $this->errno = '';
            $this->errmsg = '商品不存在';
            return false;
        }

        $input = new WxPayUnifiedOrder();
        $input->SetBody($item_info[0]['name']);
        $input->SetAttach($billId);
        $input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
        $input->SetTotal_fee("2");
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 86400*3));
        $input->SetGoods_tag($item_info[0]['name']);
        $input->SetNotify_url("http://paysdk.weixin.qq.com/example/notify.php");
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id($billId);
        $notify = new NativePay();
        $result = $notify->GetPayUrl($input);
        $url2 = $result["code_url"];
        return $url2;

    }







}
