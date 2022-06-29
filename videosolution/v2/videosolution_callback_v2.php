<?php
/** 点播音视频解决方案检查结果查询接口 */
/** 产品密钥ID，产品标识 */
define("SECRETID", "your_secret_id");
/** 产品私有密钥，服务端生成签名信息使用，请严格保管，避免泄露 */
define("SECRETKEY", "your_secret_key");
/** 易盾反垃圾云服务点播音视频解决方案结果获取接口地址 */
define("API_URL", "http://as.dun.163.com/v2/videosolution/callback/results");
/** api version */
define("VERSION", "v2");
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
		// var_dump($result_array);
		foreach ($result_array as $index => $resultInfo) {
			// status任务状态， 0：正常，1：已过期，2：数据不存在，3：检测中
			$status = $resultInfo["status"];
			if ($status == 0) {
				// 机器检测结果
				$antispam = $resultInfo["antispam"];
				if ($antispam != null) {
					echo "机器检测结果: " . json_encode($antispam);
				}
				// 语种检测数据
				$language = $resultInfo["language"];
				if ($language != null) {
					echo "语种检测数据: " . json_encode($language);
				}
				// 语音翻译数据
				$asr = $resultInfo["asr"];
				if ($asr != null) {
					echo "语音翻译数据: " . json_encode($asr);
				}
				// 人声属性检测数据
				$voice = $resultInfo["voice"];
				if ($voice != null) {
					echo "人声属性检测数据: " . json_encode($voice);
				}
			}
		}
    }else{
    	var_dump($ret);
    }
}

main();
?>
