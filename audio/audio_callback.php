<?php
/** 离线音频检查结果查询接口 */
/** 产品密钥ID，产品标识 */
define("SECRETID", "your_secret_id");
/** 产品私有密钥，服务端生成签名信息使用，请严格保管，避免泄露 */
define("SECRETKEY", "your_secret_key");
/** 业务ID，易盾根据产品业务特点分配 */
define("BUSINESSID", "your_business_id");
/** 易盾反垃圾云服务音频检测结果获取接口地址 */
define("API_URL", "http://as.dun.163.com/v3/audio/callback/results");
/** 点播语音版本v3.2及以上二级细分类结构进行调整 */
define("VERSION", "v3.3");
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
		$result_array = $ret["antispam"];
		foreach($result_array as $res_index => $result){
		    $taskId = $result["taskId"];
		    $asrStatus = $result["asrStatus"];
		    if($asrStatus == 4) {
                $asrResult = $result["asrResult"];
                echo "检测失败: taskId={$taskId}, asrResult={$asrResult}";
		    } else {
                $action = $result["action"];
                // 音频数据所在断句详细信息
                $segments_array = $result["segments"];
                $label_array = $result["labels"];
                // 证据信息如下
                /*foreach($label_array as $label_index => $labelInfo){
                    $label = $labelInfo["label"];
                    $level = $labelInfo["level"];
                }*/
                if ($action == 0) {
                    echo "结果：通过，taskId=".$taskId;
                } else if ($action == 2) {
                    echo "结果：不通过，taskId=".$taskId;
                }
		    }
		}
    }else{
    	var_dump($ret);
    }
}

main();
?>
