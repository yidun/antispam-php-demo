<?php
/** 文档解决方案提交检测接口 */
/** 产品密钥ID，产品标识 */
define("SECRETID", "your_secret_id");
/** 产品私有密钥，服务端生成签名信息使用，请严格保管，避免泄露 */
define("SECRETKEY", "your_secret_key");
/** 易盾反垃圾云服务文档解决方案检测接口地址 */
define("API_URL", "http://as-file.dun.163yun.com/v1/file/submit");
/** api version */
define("VERSION", "v1.1");
/** API timeout*/
define("API_TIMEOUT", 10);
require("../util.php");

/**
 * 反垃圾请求接口简单封装
 * $params 请求参数
 */
function check($params){
	$params["secretId"] = SECRETID;
	$params["version"] = VERSION;
	$params["timestamp"] = time() * 1000;// time in milliseconds
	$params["nonce"] = sprintf("%d", rand()); // random int

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
		"url"=>"http://xxx.xxx.com/xxxx",
		"dataId"=>"xxx"
		// "dataType"=>"1",
		// "checkFlag"=>"3",
		// "ip"=>"123.115.77.137",
		// "account"=>"java@163.com",
		// "callback"=>"ebfcad1c-dba1-490c-b4de-e784c2691768"
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
