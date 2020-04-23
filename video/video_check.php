<?php
/** 视频提交检测接口 */
/** 产品密钥ID，产品标识 */
define("SECRETID", "your_secret_id");
/** 产品私有密钥，服务端生成签名信息使用，请严格保管，避免泄露 */
define("SECRETKEY", "your_secret_key");
/** 业务ID，易盾根据产品业务特点分配 */
define("BUSINESSID", "your_business_id");
/** 易盾反垃圾云服务视频检测接口地址 */
define("API_URL", "http://as.dun.163yun.com/v3/video/submit");
/** api version */
define("VERSION", "v3");
/** API timeout*/
define("API_TIMEOUT", 1);
require("../util.php");

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
	);

	$ret = check($params);
	var_dump($ret);
	if ($ret["code"] == 200) {
		$result = $ret["result"];
        // status 0:成功，1:失败
        $status = $result["status"];
        $taskId = $result["taskId"];
        if($status === 0){
            echo "提交成功，taskId=".$taskId;
        }else{
            echo "提交失败，taskId=".$taskId;
        }
    }else{
    	var_dump($ret);
    }
}

main();
?>
