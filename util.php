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
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8'));
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

/**
 * curl get请求
 * @params 输入的参数
 */
function curl_get_set_header($params, $url, $timeout, $headersMap){
    $queryString = http_build_query($params);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url . "?" . $queryString);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // 本地调式使用，生产环境建议不推荐禁用SSL证书验证
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 不验对等证书
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 不验证证书中的主机名
    // 设置超时时间
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    // 确认 HTTP 请求方法为 GET，这行可以省略，因为 cURL 默认就是 GET
    curl_setopt($ch, CURLOPT_HTTPGET, 1);
    $headers = [];
    foreach ($headersMap as $key => $value) {
        $headers[] = "$key: $value";
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $output = curl_exec($ch);

    // 检查是否有错误发生
    if(curl_errno($ch)){
        // 可以选择记录或返回错误信息
        $output = 'Curl error: ' . curl_error($ch);
    }
    echo "output:{$output}";

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
