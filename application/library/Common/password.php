<?php
class Common_Password {

    public static function generatePwd($password){
        return md5('cy_'.$password);
    }
}