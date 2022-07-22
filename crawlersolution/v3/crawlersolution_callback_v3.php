<?php
/** 网站解决方案获取结果-轮询模式接口API示例-v3版本 */
/** 产品密钥ID，产品标识 */
define("SECRETID", "your_secret_id");
/** 产品私有密钥，服务端生成签名信息使用，请严格保管，避免泄露 */
define("SECRETKEY", "your_secret_key");
/** 接口地址 */
define("API_URL", "http://as.dun.163.com/v3/crawler/callback/results");
/** api version */
define("VERSION", "v3.0");
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
	$params["version"] = VERSION;
	$params["timestamp"] = time() * 1000;// time in milliseconds
	$params["nonce"] = sprintf("%d", rand()); // random int

	$params["signatureMethod"] = SIGNATURE_METHOD;
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
        foreach($result_array as $res_index => $resultInfo){
			// 机器检测结果
            $antispam = $resultInfo["antispam"];
			// 增值服务信息
            $valueAddService = $resultInfo["valueAddService"];
			// 反作弊检测结果
            $anticheat = $resultInfo["anticheat"];
			// 网站人工审核结果
            $censor = $resultInfo["censor"];


			$taskId = $censor["taskId"];
			$dataId = $censor["dataId"];
			$result = $censor["result"];
			$callback = $censor["callback"];
			// 证据信息
			$evidencesObject = $censor["evidences"];
			echo "网站检测解决方案结果：taskId={$taskId}：dataId={$dataId}：result={$result}：callback={$callback}";
        }
    }else{
    	var_dump($ret);
    }
}

main();
?>
