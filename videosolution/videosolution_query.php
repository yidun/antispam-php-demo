<?php
/** 产品密钥ID，产品标识 */
define("SECRETID", "your_secret_id");
/** 产品私有密钥，服务端生成签名信息使用，请严格保管，避免泄露 */
define("SECRETKEY", "your_secret_key");
/** 易盾反垃圾云服务点播音视频解决方案查询接口地址 */
define("API_URL", "http://as.dun.163.com/v1/videosolution/query/task");
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
	$taskIds = array("202b1d65f5854cecadcb24382b681c1a","0f0345933b05489c9b60635b0c8cc721");
	$params = array(
		"taskIds"=>json_encode($taskIds)
	);
	var_dump($params);

	$ret = check($params);
	var_dump($ret);
	if ($ret["code"] == 200) {
		$result_array = $ret["result"];
        foreach($result_array as $res_index => $resultInfo){
            $taskId = $resultInfo["taskId"];
            $status = $resultInfo["status"];
            $result = $resultInfo["result"];
            // 人审证据信息
            $reviewEvidencesObject = $resultInfo["reviewEvidences"];
            // 证据信息
            $evidencesObject = $resultInfo["evidences"];
            echo "音视频结果结果：taskId={$taskId}：status={$status}：result={$result}";
        }
    }else{
    	var_dump($ret);
    }
}
main();
?>
