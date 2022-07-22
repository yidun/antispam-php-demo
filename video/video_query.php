<?php
/** 产品密钥ID，产品标识 */
define("SECRETID", "your_secret_id");
/** 产品私有密钥，服务端生成签名信息使用，请严格保管，避免泄露 */
define("SECRETKEY", "your_secret_key");
/** 业务ID，易盾根据产品业务特点分配 */
define("BUSINESSID", "your_business_id");
/** 易盾反垃圾云服务点播結果查詢接口地址 */
define("API_URL", "http://as.dun.163.com/v1/video/query/task");
/** api version */
define("VERSION", "v1");
/** API timeout*/
define("API_TIMEOUT", 10);
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
	$taskIds = array("a42d1727d40f47cba8de3458820cb814","01fb38b09d694dbaae0560e62c1fc2af");
	$params = array(
		"taskIds"=>json_encode($taskIds)
	);

	$ret = check($params);
	//var_dump($ret);
	if ($ret["code"] == 200) {
		$result_array = $ret["result"];
		foreach($result_array as $res_index => $result){
			//-1:提交检测失败，0:正常，10：检测中，20：不是7天内数据，30：taskId不存在，110：请求重复，120：参数错误，130：解析错误，140：数据类型错误
		    $status = $result["status"];
		    $taskId = $result["taskId"];
			if($status!=0){
				echo "获取结果异常，status=".$status."taskId=".$taskId;
				continue;
			}
			$callback = $result["callback"];
			$level = $result["level"];
			if($level != 0){ // 返回 level == 0表示正常
				// 从evidences里获取证据信息，详细说明见http://dun.163.com/support/api#API_13
				foreach ($result['evidences'] as $evi_index => $evidence) {
					echo json_encode($evidence)."taskId=".$taskId;
				}
			} else {
				echo "点播视频结果正常，callback=".$callback."taskId=".$taskId;
			}
		}
    	}else{
    		var_dump($ret); // error handler
    	}
}

main();
?>
