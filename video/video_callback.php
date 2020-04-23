<?php
/** 离线视频检查结果查询接口 */
/** 产品密钥ID，产品标识 */
define("SECRETID", "your_secret_id");
/** 产品私有密钥，服务端生成签名信息使用，请严格保管，避免泄露 */
define("SECRETKEY", "your_secret_key");
/** 业务ID，易盾根据产品业务特点分配 */
define("BUSINESSID", "your_business_id");
/** 易盾反垃圾云服务视频检测结果获取接口地址 */
define("API_URL", "https://as.dun.163yun.com/v3/video/callback/results");
/** api version */
define("VERSION", "v3.1");
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
		$result_array = $ret["result"];
		foreach($result_array as $res_index => $result){
			$status = $result["status"];
			if($status!=0){
				echo "视频异常，status=".$status;
				continue;
			}
			$level = $result["level"];
			if($level != 0){ // 返回 level == 0表示正常
				// 从evidences里获取证据信息，详细说明见http://dun.163.com/support/api#API_13
				foreach ($result['evidences'] as $evi_index => $evidence) {
					echo json_encode($evidence)."\n";
				}
			}
		}
    }else{
    	var_dump($ret);
    }
}

main();
?>
