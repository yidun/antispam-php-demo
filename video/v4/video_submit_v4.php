<?php
/** 调用易盾反垃圾云服务视频信息提交接口API示例 */
/** 产品密钥ID，产品标识 */
define("SECRETID", "your_secret_id");
/** 产品私有密钥，服务端生成签名信息使用，请严格保管，避免泄露 */
define("SECRETKEY", "your_secret_key");
/** 业务ID，易盾根据产品业务特点分配 */
define("BUSINESSID", "your_business_id");
/** 接口地址 */
define("API_URL", "http://as.dun.163.com/v4/video/submit");
/** api version */
define("VERSION", "v4");
/** API timeout*/
define("API_TIMEOUT", 1);
require("../../util.php");

/**
 * 反垃圾请求接口简单封装
 * $params 请求参数
 */
function check($params){
	$params["secretId"] = SECRETID;
	$params["businessId"] = BUSINESSID;
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
		"dataId"=>"fbfcad1c-dba1-490c-b4de-e784c2691765",
		"url"=>"http://xxx.xxx.com/xxxx"
		// "callback"=>"{\"p\":\"xx\"}",
		// "scFrequency"=>"5",
	);

	$ret = check($params);
	var_dump($ret);
	if ($ret["code"] == 200) {
		$result = $ret["result"];
		// status 0:成功，1:失败
		$status = $result["status"];
		$taskId = $result["taskId"];
		// 缓冲池排队待处理数据量
		$dealingCount = $result["dealingCount"];
		if($status === 0){
			echo "SUBMIT SUCCESS: taskId={$taskId}, dealingCount = {$dealingCount}";
		}else{
			echo "提交失败，taskId=".$taskId;
		}
    }else{
    	var_dump($ret);
    }
}

main();
?>
