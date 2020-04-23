<?php
/** 直播电视墙检测结果查询接口　*/
/** 产品密钥ID，产品标识 */
define("SECRETID", "your_secret_id");
/** 产品私有密钥，服务端生成签名信息使用，请严格保管，避免泄露 */
define("SECRETKEY", "your_secret_key");
/** 业务ID，易盾根据产品业务特点分配 */
define("BUSINESSID", "your_business_id");
/** 易盾反垃圾云服务直播电视墙检测结果获取接口地址 */
define("API_URL", "http://as.dun.163yun.com/v3/livewall/callback/results");
/** api version */
define("VERSION", "v3");
/** API timeout*/
define("API_TIMEOUT", 10);
require("../util.php");

/**
 * 反垃圾请求接口简单封装
 * $params 请求参数
 */
function check(){
    $params = array();
	$params["secretId"] = SECRETID;
	$params["businessId"] = BUSINESSID;
	$params["version"] = VERSION;
	$params["timestamp"] = time() * 1000;// time in milliseconds
	$params["nonce"] = sprintf("%d", rand()); // random int

	$params = toUtf8($params);
	$params["signature"] = gen_signature(SECRETKEY, $params);
	// var_dump($params);

	$result = curl_post($params, API_URL, API_TIMEOUT);
	// var_dump($result);
	if($result === FALSE){
		return array("code"=>500, "msg"=>"file_get_contents failed.");
	}else{
		return json_decode($result, true);	
	}
}

// 简单测试
function main(){
    echo "mb_internal_encoding=".mb_internal_encoding()."\n";
	$ret = check();
	var_dump($ret);

	if ($ret["code"] == 200) {
		$result = $ret["result"];
		foreach($result as $index => $liveWall){
		    // 直播电视墙唯一uuid
			$taskId = $liveWall["taskId"];
			// 检测状态
			$status = $liveWall["status"];
			$callback = $liveWall["callback"];
			$dataId = $liveWall["dataId"];
			// 机器结果
			$evidences = $liveWall["evidences"];
			// 人审结果
            $reviewEvidences = $liveWall["reviewEvidences"];
		}
    }else{
    	var_dump($ret);
    }
}

main();
?>
