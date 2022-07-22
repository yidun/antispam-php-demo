<?php
/** 离线直播音频检查结果查询接口 */
/** 产品密钥ID，产品标识 */
define("SECRETID", "your_secret_id");
/** 产品私有密钥，服务端生成签名信息使用，请严格保管，避免泄露 */
define("SECRETKEY", "your_secret_key");
/** 业务ID，易盾根据产品业务特点分配 */
define("BUSINESSID", "your_business_id");
/** 调用易盾反垃圾云服务查询直播语音片段离线结果接口API示例 */
define("API_URL", "http://as-liveaudio.dun.163.com/v1/liveaudio/query/task");
/** api version */
define("VERSION", "v1.0");
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
	$params["taskId"] = "xxx";
	$params["startTime"] = "1578326400000";
	$params["endTime"] = "1578327000000";// 10min limit
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
		foreach($result_array as $res_index => $result){
		    $taskId = $result["taskId"];
			$action = $result["action"];
            $segment_array = $result["segments"];
            $asrStatus = $result["asrStatus"];
            $startTime = $result["startTime"];
            $endTime = $result["endTime"];
            // 证据信息如下
            /*foreach($segment_array as $label_index => $segmentsInfo){
                $label = $segmentsInfo["label"];
                $level = $segmentsInfo["level"];
            }*/
            if ($action == 0) {
                echo "结果：通过，taskId=".$taskId + "startTime:".$startTime + "endTime:".$endTime;
            } else if ($action == 2) {
                echo "结果：不通过，taskId=".$taskId + "startTime:".$startTime + "endTime:".$endTime;
            }
		}
    }else{
    	var_dump($ret);
    }
}

main();
?>
