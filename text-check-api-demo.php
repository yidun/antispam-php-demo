<?php
/** 产品密钥ID，产品标识 */
define("SECRETID", "your_secret_id");
/** 产品私有密钥，服务端生成签名信息使用，请严格保管，避免泄露 */
define("SECRETKEY", "your_secret_key");
/** 业务ID，易盾根据产品业务特点分配 */
define("BUSINESSID", "your_business_id");
/** 易盾反垃圾云服务文本在线检测接口地址 */
define("API_URL", "https://api.aq.163.com/v2/text/check");
/** api version */
define("VERSION", "v2");
/** API timeout*/
define("API_TIMEOUT", 1);
/** php内部使用的字符串编码 */
define("INTERNAL_STRING_CHARSET", "auto");

/**
 * 计算参数签名
 * $params 请求参数
 * $secretKey secretKey
 */
function gen_signature($secretKey, $params){
	ksort($params);
	$buff="";
	foreach($params as $key=>$value){
		$buff .=$key;
		$buff .=$value;
	}
	$buff .= $secretKey;
	return md5($buff);
}

/**
 * 将输入数据的编码统一转换成utf8
 * @params 输入的参数
 * @inCharset 输入参数对象的编码
 */
function toUtf8($params){
	$conv = function($value){
		return is_string($value) ? mb_convert_encoding($value, "utf8", INTERNAL_STRING_CHARSET) : $value;
	};
	return array_map($conv, $params);
}

/**
 * 反垃圾请求接口简单封装
 * $params 请求参数
 */
function check($params){
	$params["secretId"] = SECRETID;
	$params["businessId"] = BUSINESSID;
	$params["version"] = VERSION;
	$params["timestamp"] = sprintf("%d", round(microtime(true)*1000));// time in milliseconds
	$params["nonce"] = sprintf("%d", rand()); // random int

	$params = toUtf8($params);
	$params["signature"] = gen_signature(SECRETKEY, $params);
	// var_dump($params);

	$options = array(
	    'http' => array(
	        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
	        'method'  => 'POST',
	        'timeout' => API_TIMEOUT, // read timeout in seconds
	        'content' => http_build_query($params),
	    ),
	);
	$context  = stream_context_create($options);
	$result = file_get_contents(API_URL, false, $context);
	return json_decode($result, true);
}

// 简单测试
function main(){
    echo "mb_internal_encoding=".mb_internal_encoding()."\n";
	$params = array(
		"dataId"=>"ebfcad1c-dba1-490c-b4de-e784c2691768",
		"content"=>"易盾测试内容！",
		"dataOpType"=>"1",
		"ip"=>"123.115.77.137",
		"dataType"=>"1",
		"parentDataId"=>"334d42e1-112f-4fc7-8fb7-c60542fc2018",
		"title"=>"易盾测试标题",
		"url"=>"http://www.xx.com/xxx.html",
		"account"=>"php@163.com",
		"nickname"=>"没事瞎评论php",
		"deviceType"=>"4",
		"deviceId"=>"92B1E5AA-4C3D-4565-A8C2-86E297055088",
		"callback"=>"ebfcad1c-dba1-490c-b4de-e784c2691768",
		"publishTime"=>round(microtime(true)*1000)
	);

	$ret = check($params);
	var_dump($ret);
	if ($ret["code"] == 200) {
		$action = $ret["result"]["action"];
       	if ($action == 1) {// 内容正常，通过
       		echo "content is normal\n";
      	} else if ($action == 2) {// 垃圾内容，删除
      		echo "content is spam\n";
        } else if ($action == 3) {// 嫌疑内容
        	echo "content is suspect\n";
        }
    }else{
    	// error handler
    }
}

main();
?>
