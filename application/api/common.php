<?php
/**
 * unicode 转为 中英文
 */
function unicode2utf8($str){
    if (! $str)
        return $str;
    $decode = json_decode($str);
    if ($decode)
        return $decode;
    $str = '["' . strtolower($str) . '"]';
    $decode = json_decode($str);
    if (count($decode) == 1) {
        return $decode[0];
    }
    return $str;
}


/**
 * 随机数的生成
 * @param number $len
 * @param number $type
 * @return string
 */
function genSecret($len = 6, $type =1){
    $secret = '';
    for ($i = 0; $i < $len; $i ++) {
        if (1 == $type) {
            if (0 == $i) {
                $secret .= chr(rand(49, 57));
            } else {
                $secret .= chr(rand(48, 57));
            }
        } else
            if (2 == $type) {
                $secret .= chr(rand(65, 90));
            } else {
                if (0 == $i) {
                    $secret .= chr(rand(65, 90));
                } else {
                    $secret .= (0 == rand(0, 1)) ? chr(rand(65, 90)) : chr(rand(48, 57));
                }
            }
    }
    return $secret;
}

function is_weixin(){

    if ( strpos($_SERVER['HTTP_USER_AGENT'],

            'MicroMessenger') !== false ) {

        return true;

    }

    return false;

}

function is_mobile($str){
    if(preg_match("/^(((d{3}))|(d{3}-))?13d{9}$/", $str)){
        return true;
    }else{
        return false;
    }
}

function is_phone($str){
    if (preg_match("/^(\d3,4|\d{3,4}-)?\d{7,8}$/",$str)){
        return true;
    }else{
        return false;
    }
}

function is_email($str){
    if (preg_match("/^\w+[-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/",$str)){
        return true;
    }else{
        return false;
    }
}

function is_url($str){
    if (preg_match("^http://([\w-]+\.)+[\w-]+(/[\w-./?%&=]*)?$",$str)){
        return true;
    }else{
        return false;
    }
}

function is_idcard($str){
    if (preg_match("/^\d{15}|\d{}18$/",$str)){
        return true;
    }else{
        return false;
    }
}