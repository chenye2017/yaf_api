<?php

$push_file = dirname(__FILE__) . '/../library/ThirdLibrary/geitui/';
require_once($push_file . 'IGt.Push.php');
require_once($push_file . 'igetui/IGt.AppMessage.php');
require_once($push_file . 'igetui/IGt.APNPayload.php');
require_once($push_file . 'igetui/template/IGt.BaseTemplate.php');
require_once($push_file . 'IGt.Batch.php');
require_once($push_file .  'igetui/utils/AppConditions.php');

define('APPKEY','HugU1KSJMO7oEjZ4ktj6G7');
define('APPID','GTQYvO3pl26qHrVlHNcau8');
define('MASTERSECRET','iS21JbEVD18kKLVbeZYSWA');
/*define('CID','');
define('DEVICETOKEN','');
define('Alias','请输入别名');*/
define('HOST','http://sdk.open.api.igexin.com/apiex.htm');

class PushModel {



    public $errno;  //这个控制器还需要用，返回错误信息
    public $errmsg;
    private $_db = null; //连接数据库
    public function __construct() {
        try {
            $this->_db = new PDO('mysql:dbname=yaf_api;host=127.0.0.1', 'root', '');

            $this->_db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); //禁用预处理
            $this->_db->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false); //取出来的东西不要转换类型
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }

    }



    public function push($cid, $content){
        //$igt = new IGeTui(HOST,APPKEY,MASTERSECRET);
        $igt = new IGeTui(NULL,APPKEY,MASTERSECRET,false);
        /*var_dump('ll');exit;
var_dump(dirname(__FILE__));exit;*/
        //消息模版：
        // 1.TransmissionTemplate:透传功能模板
        // 2.LinkTemplate:通知打开链接功能模板
        // 3.NotificationTemplate：通知透传功能模板
        // 4.NotyPopLoadTemplate：通知弹框下载功能模板

//    	$template = IGtNotyPopLoadTemplateDemo();
//    	$template = IGtLinkTemplateDemo();
//   	$template = IGtNotificationTemplateDemo();
        $template = $this->IGtTransmissionTemplateDemo();

        //个推信息体
        $message = new IGtSingleMessage();

        $message->set_isOffline(true);//是否离线
        $message->set_offlineExpireTime(3600*12*1000);//离线时间
        $message->set_data($template);//设置推送消息类型
//	$message->set_PushNetWorkType(0);//设置是否根据WIFI推送消息，1为wifi推送，0为不限制推送
        //接收方
        $target = new IGtTarget();
        $target->set_appId(APPID);
        $target->set_clientId($cid);
//    $target->set_alias(Alias);


        try {
            $rep = $igt->pushMessageToSingle($message, $target);
            var_dump($rep);
            echo ("<br><br>");

        }catch(RequestException $e){
            $requstId =e.getRequestId();
            $rep = $igt->pushMessageToSingle($message, $target,$requstId);
            var_dump($rep);
            echo ("<br><br>");
        }

    }

    public function IGtTransmissionTemplateDemo(){
        $template =  new IGtTransmissionTemplate();
        $template->set_appId(APPID);//应用appid
        $template->set_appkey(APPKEY);//应用appkey
        $template->set_transmissionType(1);//透传消息类型
        $template->set_transmissionContent("哈哈哈 张琴最丑");//透传内容
        //$template->set_duration(BEGINTIME,ENDTIME); //设置ANDROID客户端在此时间区间内展示消息
        //APN简单推送
//        $template = new IGtAPNTemplate();
//        $apn = new IGtAPNPayload();
//        $alertmsg=new SimpleAlertMsg();
//        $alertmsg->alertMsg="";
//        $apn->alertMsg=$alertmsg;
////        $apn->badge=2;
////        $apn->sound="";
//        $apn->add_customMsg("payload","payload");
//        $apn->contentAvailable=1;
//        $apn->category="ACTIONABLE";
//        $template->set_apnInfo($apn);
//        $message = new IGtSingleMessage();

        //APN高级推送
        $apn = new IGtAPNPayload();
        $alertmsg=new DictionaryAlertMsg();
        $alertmsg->body="body";
        $alertmsg->actionLocKey="ActionLockey";
        $alertmsg->locKey="LocKey";
        $alertmsg->locArgs=array("locargs");
        $alertmsg->launchImage="launchimage";
//        IOS8.2 支持
        $alertmsg->title="Title";
        $alertmsg->titleLocKey="TitleLocKey";
        $alertmsg->titleLocArgs=array("TitleLocArg");

        $apn->alertMsg=$alertmsg;
        $apn->badge=7;
        $apn->sound="";
        $apn->add_customMsg("payload","payload");
        $apn->contentAvailable=1;
        $apn->category="ACTIONABLE";
        $template->set_apnInfo($apn);

        //PushApn老方式传参
//    $template = new IGtAPNTemplate();
//          $template->set_pushInfo("", 10, "", "com.gexin.ios.silence", "", "", "", "");

        return $template;
    }





}
