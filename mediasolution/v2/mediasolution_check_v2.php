<?php
/** 融媒体解决方案检测提交接口API示例-v2版本 */
/** 产品密钥ID，产品标识 */
define("SECRETID", "your_secret_id");
/** 产品私有密钥，服务端生成签名信息使用，请严格保管，避免泄露 */
define("SECRETKEY", "your_secret_key");
/** 接口地址 */
define("API_URL", "http://as.dun.163.com/v2/mediasolution/submit");
/** api version */
define("VERSION", "v2");
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
    // 设置私有参数
    $jsonArray = array();
    array_push($jsonArray, array(
        "type" => "text",
        "data" => "融媒体文本段落",
        "dataId" => "0001"
    ));
    array_push($jsonArray, array(
        "type" => "image",
        "data" => "http://xxx",
		"dataId" => "0002"
    ));
    array_push($jsonArray, array(
        "type" => "audio",
        "data" => "http://xxx",
		"dataId" => "0003"
    ));
    array_push($jsonArray, array(
        "type" => "audiovideo",
        "data" => "http://xxx",
		"dataId" => "0004"
    ));
    array_push($jsonArray, array(
        "type" => "file",
        "data" => "http://xxx",
		"dataId" => "0005"
    ));
	$params = array(
		"title"=>"融媒体解决方案的标题",
		"callback"=>"i am callback",
		"content"=>json_encode($jsonArray)
	);

	$ret = check($params);
	var_dump($ret);
	if ($ret["code"] == 200) {
		$result = $ret["result"];
		$antispam = $ret["antispam"];
        $dataId = $antispam["dataId"];
        $taskId = $antispam["taskId"];
		echo "SUBMIT SUCCESS: taskId={$taskId}, dataId={$dataId}%n";
    }else{
    	var_dump($ret);
    }
}

main();
?>
