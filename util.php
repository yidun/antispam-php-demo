<?php
/** php内部使用的字符串编码 */
define("INTERNAL_STRING_CHARSET", "auto");
/** api signatureMethod,默认MD5,支持国密SM3 */
define("SIGNATURE_METHOD", "MD5");
require 'vendor/autoload.php';

/**
 * curl post请求
 * @params 输入的参数
 */
function curl_post($params, $url, $timout){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // 设置超时时间
    curl_setopt($ch, CURLOPT_TIMEOUT, $timout);
    // POST数据
    curl_setopt($ch, CURLOPT_POST, 1);
    // 把post的变量加上
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:'.'application/x-www-form-urlencoded; charset=UTF-8'));
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

/**
 * 将输入数据的编码统一转换成utf8
 * @params 输入的参数
 */
function toUtf8($params){
	$utf8s = array();
    foreach ($params as $key => $value) {
    	$utf8s[$key] = is_string($value) ? mb_convert_encoding($value, "utf8", INTERNAL_STRING_CHARSET) : $value;
    }
    return $utf8s;
}

/**
 * 计算参数签名
 * $params 请求参数
 * $secretKey secretKey
 */
function gen_signature($secretKey, $params){
    $params["signatureMethod"] = SIGNATURE_METHOD;
	ksort($params);
	$buff="";
	foreach($params as $key=>$value){
	     if($value !== null) {
	        $buff .=$key;
		$buff .=$value;
    	     }
	}
	$buff .= $secretKey;
	return md5($buff);
//    if ($params["signatureMethod"] == "SM3") {
//        return sm3($buff);
//    } else {
//        return md5($buff);
//    }
}
?>
