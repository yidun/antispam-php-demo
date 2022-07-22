<?php
/** 易盾反垃圾云服务网站检测解决方案提交接口V3 API实例 */
/** 产品密钥ID，产品标识 */
define("SECRETID", "your_secret_id");
/** 产品私有密钥，服务端生成签名信息使用，请严格保管，避免泄露 */
define("SECRETKEY", "your_secret_key");
/** 接口地址 */
define("API_URL", "http://as.dun.163.com/v3/crawler/submit");
/** api version */
define("VERSION", "v3.0");
/** API timeout*/
define("API_TIMEOUT", 10);
require("../../util.php");

/**
 * 反垃圾请求接口简单封装
 * $params 请求参数
 */
function check($params){
	$params["secretId"] = SECRETID;
	$params["version"] = VERSION;
	$params["timestamp"] = time() * 1000;// time in milliseconds
	$params["nonce"] = sprintf("%d", rand()); // random int

	$params["signatureMethod"] = SIGNATURE_METHOD;
	$params = toUtf8($params);
	$params["signature"] = gen_signature(SECRETKEY, $params);
	// var_dump($params);

	$result = curl_post($params, API_URL, API_TIMEOUT);
	if($result === FALSE){
		return array("code"=>500, "msg"=>"file_get_contents failed.");
	}else{
		return json_decode($result, true);	
	}
}

// 简单测试
function main(){
    echo "mb_internal_encoding=".mb_internal_encoding()."\n";
	$params = array(
		"dataId"=>"6a7c754f9de34eb8bfdf03f209fcfc02",
		"callback"=>"34eb8bfdf03f209fcfc02",
		"url"=>"http://xxx.com",
		// 多个检测项时用英文逗号分隔
		"checkFlags"=>"1,2",
		// 回调地址。调用方用来接收易盾主动回调结果的api地址
		"callbackUrl"=>"http://xxx"
	);


	$ret = check($params);
	var_dump($ret);
	if ($ret["code"] == 200) {
		$result = $ret["result"];
        $dataId = $result["dataId"];
        $taskId = $result["taskId"];
        echo "提交成功，taskId={$taskId},dataId={$dataId}";
    }else{
    	var_dump($ret);
    }
}

main();
?>
