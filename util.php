<?php
/** php内部使用的字符串编码 */
define("INTERNAL_STRING_CHARSET", "auto");
/** api signatureMethod,默认MD5,支持国密SM3 */
define("SIGNATURE_METHOD", "MD5");
require 'vendor/autoload.php';
require 'httpClient.php';

/**
 * curl post请求
 * @params 输入的参数
 */
function curl_post($params, $url, $timout){
    $httpClient = HttpClient::getInstance();
    return $httpClient->post($url, $params, $timout);
}

/**
 * curl get请求
 * @params 输入的参数
 */
function curl_get_set_header($params, $url, $timeout, $headersMap){
    $httpClient = HttpClient::getInstance();
    return $httpClient->get($url, $params, $timeout, $headersMap);
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

function genOpenApiSignature($secretKey, $params, $header) {
    // 1. 参数名按照ASCII码表升序排序
    $paramNames = array_keys($params);
    sort($paramNames);

    // 从header中取得timestamp和nonce
    $timestamp = $header["X-YD-TIMESTAMP"];
    $nonce = $header["X-YD-NONCE"];

    // 2. 按照排序拼接参数名与参数值
    $paramBuffer = "";
    foreach ($paramNames as $paramName) {
        $paramValue = $params[$paramName];
        $paramBuffer .= $paramName . ($paramValue ?? "");
    }

    // 3. 将secretKey，nonce，timestamp拼接到最后
    $paramBuffer .= $secretKey . $nonce . $timestamp;

    try {
        // 使用SHA-1算法计算散列值
        return sha1($paramBuffer);
    } catch (Exception $e) {
        // 错误处理
        error_log("[ERROR] not supposed to happen: " . $e->getMessage());
    }
    return "";
}
?>
