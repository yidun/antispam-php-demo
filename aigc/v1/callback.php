<?php
/** 产品密钥ID，产品标识 */
define("SECRETID", "your_secret_id");
/** 产品私有密钥，服务端生成签名信息使用，请严格保管，避免泄露 */
define("SECRETKEY", "your_secret_key");
/** 易盾反垃圾aigc在线检测接口地址 */
define("API_URL", "https://as.dun.163.com/v1/stream/callback/results");
/** api version */
define("VERSION", "v1");
/** API timeout*/
define("API_TIMEOUT", 10);
require("../../util.php");

/**
 * 反垃圾请求接口简单封装
 * $params 请求参数
 */
function callback(){
    $params = array();
	$params["secretId"] = SECRETID;
	$params["version"] = VERSION;
	$params["timestamp"] = time() * 1000;// time in milliseconds
	$params["nonce"] = sprintf("%d", rand()); // random int

	$params["signatureMethod"] = SIGNATURE_METHOD;
	$params = toUtf8($params);
	$params["signature"] = gen_signature(SECRETKEY, $params);
	var_dump($params);

	$result = curl_post($params, API_URL, API_TIMEOUT);
	var_dump($result);
	if($result === FALSE){
		return array("code"=>500, "msg"=>"aigc get callback results failed.");
	}else{
		return json_decode($result, true);	
	}
}

// 简单测试
function main(){
    echo "mb_internal_encoding=".mb_internal_encoding()."\n";
	$ret = callback();

	if ($ret["code"] == 200) {
		$result_array = $ret["result"];
		// var_dump($result_array);
		foreach($result_array as $index => $resultInfo){
			// 机器检测结果
			$antispam = $resultInfo["antispam"];
			if ($antispam != null) {
				echo "机器检测结果: ".json_encode($antispam);
                $sessionTaskId = $resultInfo["sessionTaskId"];
                $sessionIdReturn = $resultInfo["sessionId"];
				$suggestion = $antispam["suggestion"];
				$label = $antispam["label"];
				// 证据信息
				$evidencesObject = $antispam["evidences"];
				echo "sessionTaskId={$sessionTaskId}, sessionId={$sessionIdReturn}, result={$suggestion}, label={$label}";
			}
		}
    }else{
    	var_dump($ret);
    }
}

main();
?>