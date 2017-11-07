<?php
class Common_Request {
    public static function post($key, $value = null) {
        return self::request($key, $value, 'post');
    }

    public static function get($key, $value = null) {
        return self::request($key, $value, 'get');
    }

    public static function request($key, $value = null, $type = null) {
        if ($type == 'get') {
            $result = isset($_GET[$key]) ? $_GET[$key] : trim($value);
        } elseif ($type == 'post') {
            $result = isset($_POST[$key]) ? $_POST[$key] : trim($value);
        } else {
            $result = isset($_REQUEST[$key]) ? $_REQUEST[$key] : trim($value);
        }
        return $result;
    }

    public static function response($errCode, $errMsg, $data = '') {
        $arr = [
            'errno'=>$errCode,
            'errmsg'=>$errMsg
        ];
        if ($data) {
            $arr['data'] = $data;
        }
        return json_encode($arr);

    }
}