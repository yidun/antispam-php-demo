<?php
/** AIGC文本流式检测demo */

/** 产品密钥ID，产品标识 */
define("SECRETID", "your_secret_id");
/** 产品私有密钥，服务端生成签名信息使用，请严格保管，避免泄露 */
define("SECRETKEY", "your_secret_key");
/** 易盾反垃圾aigc在线检测接口地址 */
define("API_URL", "https://as.dun.163.com/v1/stream/push");
/** api version */
define("VERSION", "v1");
/** API timeout*/
define("API_TIMEOUT", 10);
require("../../util.php");

/**
 * 反垃圾请求接口简单封装
 * $params 请求参数
 */
function check($params) {
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
		return array("code"=>500, "msg"=>"file_get_contents failed.");
	}else{
		return json_decode($result, true);	
	}
}

function pushDemoForOutputStreamClose($params) {
    $params["sessionId"] = sprintf("yourSessionId%d", rand());
    $params["type"] = "3";
    return check($params);
}

function pushDemoForOutputStreamCheck($params) {
    $params["sessionId"] = sprintf("yourSessionId%d", rand());
    $params["type"] = "1";
    $params["dataId"] = "yourDataId";
    $params["content"] = "Current output segment 1";
    $params["publishTime"] = (string)time();
    return check($params);
}

function pushDemoForInputCheck($params) {
    $params["sessionId"] = sprintf("yourSessionId%d", rand());
    $params["type"] = "2";
    $params["dataId"] = "yourDataId";
    $params["content"] = "Current input content";
    $params["publishTime"] = (string)time();
    return check($params);
}


function main(){
    echo "mb_internal_encoding=".mb_internal_encoding()."\n";
    $params = array();
    $ret = pushDemoForInputCheck($params);
    if ($ret["code"] == 200) {
        $result = $ret["result"];
        $sessionTaskId = $result["sessionTaskId"];
        $sessionIdReturn = $result["sessionId"];
        echo "sessionTaskId=$sessionTaskId, sessionId=$sessionIdReturn\n";
    } else {
        $code = $ret["code"];
        $msg = $ret["msg"];
        echo "ERROR: code=$code, msg=$msg\n";
    }
}

main();
?>
