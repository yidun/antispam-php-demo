<?php
/** 调用易盾反垃圾云服务文本V5离线检测结果获取接口API示例 */
/** 产品密钥ID，产品标识 */
define("SECRETID", "your_secret_id");
/** 产品私有密钥，服务端生成签名信息使用，请严格保管，避免泄露 */
define("SECRETKEY", "your_secret_key");
/** 业务ID，易盾根据产品业务特点分配 */
define("BUSINESSID", "your_business_id");
/** 接口地址 */
define("API_URL", "http://as.dun.163.com/v5/text/callback/results");
/** api version */
define("VERSION", "v5");
/** API timeout*/
define("API_TIMEOUT", 10);
require("../../util.php");

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
	$ret = check();
	var_dump($ret);

	if ($ret["code"] == 200) {
		$result_array = $ret["result"];
		foreach($result_array as $index => $result){
			// 内容安全结果
			$antispam = $result["antispam"];
			if ($antispam != null) {
				$taskId = $antispam["taskId"];
				$dataId = $antispam["dataId"];
				$suggestion = $antispam["suggestion"];
				$resultType = $antispam["resultType"];
				$censorType = $antispam["censorType"];
				$isRelatedHit = $antispam["isRelatedHit"];
				$labels = $antispam["labels"];
				echo "内容安全结果，taskId: {$taskId}，dataId: {$dataId}，suggestion: {$suggestion}";
				foreach ($labels as $index => $labelElement) {
					$label = $labelElement["label"];
					$level = $labelElement["level"];
					$subLabels = $labelElement["subLabels"];
					foreach ($subLabels as $index => $subLabelElement) {
						echo "内容安全分类，label: {$label}，subLabel: {$subLabelElement["subLabel"]}, \n details:".json_encode($subLabelElement["details"]);
					}
				}
			}
			// 情感分析结果
			$emotionAnalysis = $result["emotionAnalysis"];
			if ($emotionAnalysis != null) {
				$taskId = $emotionAnalysis["taskId"];
				$dataId = $emotionAnalysis["dataId"];
				echo "情感分析结果，taskId: {$taskId}，dataId: {$dataId}，details:".json_encode($emotionAnalysis["details"]);
			}
			// 反作弊结果
			$anticheat = $result["anticheat"];
			if ($anticheat != null) {
				$taskId = $anticheat["taskId"];
				$dataId = $anticheat["dataId"];
				echo "反作弊结果，taskId: {$taskId}，dataId: {$dataId}，details:".json_encode($anticheat["details"]);
			}
			// 用户画像结果
			$userRisk = $result["userRisk"];
			if ($userRisk != null) {
				$taskId = $userRisk["taskId"];
				$dataId = $userRisk["dataId"];
				echo "用户画像结果，taskId: {$taskId}，dataId: {$dataId}，details:".json_encode($userRisk["details"]);
			}
			// 语种检测结果
			$language = $result["language"];
			if ($language != null) {
				$taskId = $language["taskId"];
				$dataId = $language["dataId"];
				echo "语种检测结果，taskId: {$taskId}，dataId: {$dataId}，details:".json_encode($language["details"]);
			}
		}
	} else {
		var_dump($ret);
	}
}

main();
?>
